<?php

namespace App\Services;

use App\Models\ProviderAddressMapping;
use App\Models\ProviderRequirement;
use App\Models\User;

class ServicePreferenceRequirementService
{
    /**
     * Check if provider has at least one active service address.
     */
    public function hasServiceAddress(int $providerId): bool
    {
        return ProviderAddressMapping::where('provider_id', $providerId)
            ->where('status', 1)
            ->exists();
    }

    /**
     * Check if provider has at least one handyman with all 4 approved documents:
     * - state_id
     * - background_check
     * - profile_photo
     * - certificate
     */
    public function hasApprovedHandyman(int $providerId): bool
    {
        $handymanIds = User::where('user_type', 'handyman')
            ->where('provider_id', $providerId)
            ->where('status', 1)
            ->pluck('id');

        if ($handymanIds->isEmpty()) {
            return false;
        }

        $requiredKeys = ['state_id', 'background_check', 'profile_photo', 'certificate'];

        foreach ($handymanIds as $handymanId) {
            $approvedCount = ProviderRequirement::where('provider_id', $providerId)
                ->where('handyman_id', $handymanId)
                ->whereIn('key', $requiredKeys)
                ->where('status', 'approved')
                ->distinct('key')
                ->count('key');

            if ($approvedCount === count($requiredKeys)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Provider Location Requirement:
     * - Company docs: business_license (approved), state_id (approved), handyman_id = NULL
     * - Service Address: >= 1 active provider address
     * - Handyman: >= 1 handyman with all 4 approved documents
     */
    public function canUseProviderLocation(int $providerId): bool
    {
        $requiredCompanyDocs = ['business_license', 'state_id'];
        $approvedCount = ProviderRequirement::where('provider_id', $providerId)
            ->whereNull('handyman_id')
            ->whereIn('key', $requiredCompanyDocs)
            ->where('status', 'approved')
            ->distinct('key')
            ->count('key');

        if ($approvedCount < count($requiredCompanyDocs)) {
            return false;
        }

        if (!$this->hasServiceAddress($providerId)) {
            return false;
        }

        if (!$this->hasApprovedHandyman($providerId)) {
            return false;
        }

        return true;
    }

    /**
     * Customer Location Requirement:
     * - Company docs: background_check (approved), liability_insurance (approved), business_ein (approved), handyman_id = NULL
     * - Service Address: >= 1 active provider address
     * - Handyman: >= 1 handyman with all 4 approved documents
     */
    public function canUseCustomerLocation(int $providerId): bool
    {
        $requiredCompanyDocs = ['background_check', 'liability_insurance', 'business_ein'];
        $approvedCount = ProviderRequirement::where('provider_id', $providerId)
            ->whereNull('handyman_id')
            ->whereIn('key', $requiredCompanyDocs)
            ->where('status', 'approved')
            ->distinct('key')
            ->count('key');

        if ($approvedCount < count($requiredCompanyDocs)) {
            return false;
        }

        if (!$this->hasServiceAddress($providerId)) {
            return false;
        }

        if (!$this->hasApprovedHandyman($providerId)) {
            return false;
        }

        return true;
    }

    /**
     * Virtual Requirement: Always available without requirements.
     */
    public function canUseVirtual(int $providerId = 0): bool
    {
        return true;
    }

    /**
     * Get all preference keys currently available for a provider.
     */
    public function getAvailableServicePreferences(int $providerId): array
    {
        $available = ['virtual'];

        if ($this->canUseProviderLocation($providerId)) {
            $available[] = 'provider_location';
        }

        if ($this->canUseCustomerLocation($providerId)) {
            $available[] = 'customer_location';
        }

        return $available;
    }

    /**
     * Validate an array of requested preferences against the provider's completed requirements.
     *
     * @param int $providerId
     * @param array $preferences Array of ['type' => ..., 'price' => ...]
     * @return array ['valid' => bool, 'message' => string]
     */
    public function validatePreferencesForSave(int $providerId, array $preferences): array
    {
        $types = collect($preferences)->map(function ($item) {
            return is_array($item) ? ($item['type'] ?? null) : $item;
        })->filter()->all();

        if (in_array('provider_location', $types) && !$this->canUseProviderLocation($providerId)) {
            return [
                'valid' => false,
                'message' => 'Service at Location requirements are not completed.',
            ];
        }

        if (in_array('customer_location', $types) && !$this->canUseCustomerLocation($providerId)) {
            return [
                'valid' => false,
                'message' => 'Service at Customer Location requirements are not completed.',
            ];
        }

        return [
            'valid' => true,
            'message' => '',
        ];
    }
}

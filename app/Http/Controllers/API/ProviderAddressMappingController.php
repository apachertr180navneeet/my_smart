<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ServiceZone;
use App\Models\ProviderZoneMapping;
use App\Models\ProviderAddressMapping;

class ProviderAddressMappingController extends Controller
{
    /**
     * Get available zones for a provider
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getProviderZones(Request $request)
    {
        $user = auth()->user() ?? auth('sanctum')->user();
        $providerId = $request->get('provider_id', $user ? $user->id : null);

        if ($providerId) {
            $zoneIds = ProviderZoneMapping::where('provider_id', $providerId)->pluck('zone_id')->toArray();
            if (!empty($zoneIds)) {
                $zones = ServiceZone::whereIn('id', $zoneIds)
                    ->where('status', 1)
                    ->select('id', 'name')
                    ->orderBy('name', 'asc')
                    ->get();
            } else {
                $zones = ServiceZone::where('status', 1)
                    ->select('id', 'name')
                    ->orderBy('name', 'asc')
                    ->get();
            }
        } else {
            $zones = ServiceZone::where('status', 1)
                ->select('id', 'name')
                ->orderBy('name', 'asc')
                ->get();
        }

        return comman_custom_response([
            'status' => true,
            'data' => $zones
        ]);
    }

    /**
     * Get address list for provider
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getProviderAddressList(Request $request)
    {
        $user = auth()->user() ?? auth('sanctum')->user();
        $providerId = $request->get('provider_id', $user ? $user->id : null);

        $addresses = ProviderAddressMapping::when($providerId, function ($query) use ($providerId) {
            $query->where('provider_id', $providerId);
        })->where('status', 1)->get();

        return comman_custom_response([
            'status' => true,
            'data' => $addresses
        ]);
    }
}

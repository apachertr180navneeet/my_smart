<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\ProviderRequirement;
use App\Models\User;
use App\Traits\NotificationTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class ProviderRequirementController extends Controller
{
    use NotificationTrait;

    /**
     * Allowed keys for company documents (handyman_id = null)
     */
    public const COMPANY_KEYS = [
        'business_license',
        'state_id',
        'background_check',
        'liability_insurance',
        'business_ein',
    ];

    /**
     * Allowed keys for handyman staff documents (handyman_id != null)
     */
    public const HANDYMAN_KEYS = [
        'state_id',
        'background_check',
        'profile_photo',
        'certificate',
    ];

    /**
     * GET /api/provider-requirements
     * Combined list of provider company documents and all handyman staff requirements.
     */
    public function getRequirements(Request $request)
    {
        $user = auth('sanctum')->user() ?? auth()->user();
        if (!$user) {
            return comman_message_response('Unauthenticated.', 401);
        }

        $providerId = $user->id;

        // If handyman is querying, optionally derive their provider_id
        if ($user->user_type === 'handyman' && !empty($user->provider_id)) {
            $providerId = $user->provider_id;
        }

        $requirements = ProviderRequirement::where('provider_id', $providerId)
            ->with(['handyman:id,first_name,last_name,display_name'])
            ->get();

        $formatted = $requirements->map(function ($item) {
            return [
                'id' => $item->id,
                'provider_id' => $item->provider_id,
                'handyman_id' => $item->handyman_id,
                'handyman_name' => optional($item->handyman)->display_name,
                'key' => $item->key,
                'file' => $item->file_url,
                'status' => $item->status,
                'remarks' => $item->remarks,
                'created_at' => $item->created_at,
                'updated_at' => $item->updated_at,
            ];
        });

        return comman_custom_response(['data' => $formatted]);
    }

    /**
     * POST /api/provider-requirement-save
     * Upload or update requirement document.
     */
    public function saveRequirement(Request $request)
    {
        $user = auth('sanctum')->user() ?? auth()->user();
        if (!$user) {
            return comman_message_response('Unauthenticated.', 401);
        }

        $validator = Validator::make($request->all(), [
            'key' => 'required|string',
            'file' => 'required|file|mimes:jpg,jpeg,png,pdf|max:5120',
            'handyman_id' => 'nullable|integer',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->first(),
                'all_message' => $validator->errors(),
            ], 422);
        }

        $providerId = $user->id;
        $handymanId = $request->filled('handyman_id') ? (int) $request->handyman_id : null;
        $key = trim($request->key);

        if ($handymanId !== null) {
            // Handyman document upload
            if (!in_array($key, self::HANDYMAN_KEYS)) {
                return response()->json([
                    'status' => false,
                    'message' => 'Invalid handyman requirement key. Allowed keys: ' . implode(', ', self::HANDYMAN_KEYS),
                ], 422);
            }

            // Verify handyman belongs to authenticated provider
            $handyman = User::where('id', $handymanId)
                ->where('user_type', 'handyman')
                ->where('provider_id', $providerId)
                ->first();

            if (!$handyman) {
                return response()->json([
                    'status' => false,
                    'message' => 'You are not authorized to upload documents for this handyman.',
                ], 403);
            }
        } else {
            // Company document upload
            if (!in_array($key, self::COMPANY_KEYS)) {
                return response()->json([
                    'status' => false,
                    'message' => 'Invalid company requirement key. Allowed keys: ' . implode(', ', self::COMPANY_KEYS),
                ], 422);
            }
        }

        // Store file securely in public storage disk
        $uploadedFile = $request->file('file');
        $storedPath = $uploadedFile->store('provider-requirements', 'public');

        // Upsert requirement and reset status to pending
        $requirement = ProviderRequirement::updateOrCreate(
            [
                'provider_id' => $providerId,
                'handyman_id' => $handymanId,
                'key' => $key,
            ],
            [
                'file' => $storedPath,
                'status' => 'pending',
                'remarks' => null,
            ]
        );

        return response()->json([
            'status' => true,
            'message' => 'Requirement document uploaded successfully.',
            'data' => [
                'id' => $requirement->id,
                'provider_id' => $requirement->provider_id,
                'handyman_id' => $requirement->handyman_id,
                'key' => $requirement->key,
                'file' => $requirement->file_url,
                'status' => $requirement->status,
                'remarks' => $requirement->remarks,
            ],
        ], 200);
    }

    /**
     * POST /api/provider-requirement-verify
     * Provider verification of handyman staff documents.
     */
    public function verifyRequirement(Request $request)
    {
        $user = auth('sanctum')->user() ?? auth()->user();
        if (!$user) {
            return comman_message_response('Unauthenticated.', 401);
        }

        $reqId = $request->id ?? $request->requirement_id;
        $validator = Validator::make(array_merge($request->all(), ['id' => $reqId]), [
            'id' => 'required|integer',
            'status' => 'required|in:approved,rejected',
            'remarks' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->first(),
                'all_message' => $validator->errors(),
            ], 422);
        }

        $requirement = ProviderRequirement::find($reqId);
        if (!$requirement) {
            return comman_message_response('Requirement not found.', 404);
        }

        // Provider can only verify handyman staff documents (handyman_id != null)
        if (is_null($requirement->handyman_id)) {
            return response()->json([
                'status' => false,
                'message' => 'Company documents can only be verified by an administrator.',
            ], 403);
        }

        // Verify the handyman belongs to the authenticated provider
        if ((int) $requirement->provider_id !== (int) $user->id) {
            return response()->json([
                'status' => false,
                'message' => 'You are not authorized to verify this requirement.',
            ], 403);
        }

        $requirement->status = $request->status;
        $requirement->remarks = $request->remarks;
        $requirement->save();

        // Optional notification to handyman
        if (!empty($requirement->handyman_id)) {
            try {
                $activityData = [
                    'activity_type' => 'provider_requirement_status',
                    'user_id' => $requirement->handyman_id,
                    'status' => $requirement->status,
                    'key' => $requirement->key,
                    'remarks' => $requirement->remarks,
                ];
                $this->sendNotification($activityData);
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::warning('Requirement notification failed: ' . $e->getMessage());
            }
        }

        return response()->json([
            'status' => true,
            'message' => 'Handyman requirement status updated successfully.',
            'data' => [
                'id' => $requirement->id,
                'provider_id' => $requirement->provider_id,
                'handyman_id' => $requirement->handyman_id,
                'key' => $requirement->key,
                'file' => $requirement->file_url,
                'status' => $requirement->status,
                'remarks' => $requirement->remarks,
            ],
        ], 200);
    }
}

<?php

namespace App\Http\Controllers\Api\Seller;

use App\Enums\Seller\SellerVerificationStatusEnum;
use App\Enums\Seller\SellerVisibilityStatusEnum;
use App\Http\Requests\Seller\StoreSellerRequest;
use App\Services\SellerService;
use App\Traits\AuthTrait;
use App\Types\Api\ApiResponseType;
use Dedoc\Scramble\Attributes\Group;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use App\Models\Setting;
use App\Models\User;
use App\Models\Seller;
use Illuminate\Support\Facades\DB;
use App\Enums\SellerPermissionEnum;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use App\Http\Requests\Seller\RegisterRequest;
use App\Http\Requests\Seller\CompleteRegistrationRequest;

#[Group('Seller Authentication')]
class SellerAuthApiController
{
    use AuthTrait;

    protected string $role = 'seller';

    protected $sellerService;

    public function __construct(SellerService $sellerService)
    {
        $this->sellerService = $sellerService;
    }

    /**
     * creating sellers API
     */
    public function createSellerBKUP(StoreSellerRequest $request): JsonResponse
    {
        // Restrict seller self-registration in Single Vendor mode
        // if (Setting::isSystemVendorTypeSingle()) {
        //     return ApiResponseType::sendJsonResponse(
        //         success: false,
        //         message: __('labels.seller_registration_disabled'),
        //         status: 403
        //     );
        // }

        try {
            $validated = $request->validated();
            $validated['verification_status'] = SellerVisibilityStatusEnum::Draft();
            $validated['visibility_status'] = SellerVerificationStatusEnum::NotApproved();
            $seller = $this->sellerService->createSeller(
                $validated,
                $request->allFiles()
            );

            return ApiResponseType::sendJsonResponse(
                true,
                'labels.seller_created_successfully',
                $seller,
                201
            );
        } catch (ValidationException $e) {
            return ApiResponseType::sendJsonResponse(
                success: false,
                message: 'labels.validation_failed' . $e->getMessage(),
                data: $e->errors(),
            );
        } catch (\Exception $e) {
            return ApiResponseType::sendJsonResponse(
                success: true,
                message: 'labels.seller_created_successfully',
                status: 500
            );
        }
    }

    public function createSeller(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email',
                'mobile' => 'required|string|max:20|unique:users,mobile',
                'password' => 'required|string|min:8|confirmed',
                'password_confirmation' => 'required|same:password',
            ]);

            $validated = $request->all();

            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'mobile' => $validated['mobile'],
                'password' => Hash::make($validated['password']),
                'access_panel' => 'seller',
                'email_verified_at' => null,
            ]);

            $user->assignRole('seller');

            $seller = Seller::create([
                'user_id' => $user->id,
                'verification_status' => 'not_approved',
                'visibility_status' => 'draft',
            ]);

            $otp = str_pad(random_int(100000, 999999), 6, '0', STR_PAD_LEFT);
            $user->update([
                'seller_otp' => $otp,
                'seller_otp_expires_at' => now()->addMinutes(10),
            ]);

            try {
                Mail::send('emails.otp', ['otp' => $otp], function ($message) use ($user) {
                    $message->to($user->email)->subject('OTP for Registration');
                });
            } catch (\Exception $e) {
                Log::error('Failed to send OTP email: ' . $e->getMessage());
            }

            Log::info('OTP for ' . $user->email . ': ' . $otp);

            return ApiResponseType::sendJsonResponse(
                true,
                'OTP sent to your email',
                [
                    'user_id' => $user->id,
                    'email' => $user->email,
                    'requires_verification' => true,
                ],
                200
            );

        } catch (ValidationException $e) {
            return ApiResponseType::sendJsonResponse(
                false,
                'Validation failed',
                $e->errors(),
                200
            );
        } catch (\Exception $e) {
            Log::error('Registration failed: ' . $e->getMessage());
            return ApiResponseType::sendJsonResponse(
                false,
                'Registration failed: ' . $e->getMessage(),
                [],
                200
            );
        }
    }

    public function createSeller1(StoreSellerRequest $request): JsonResponse
    {
        try {
            $validated = $request->validated();

            // Step 1: Only create user and send OTP
            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'mobile' => $validated['mobile'],
                'password' => Hash::make($validated['password']),
                'access_panel' => 'seller',
                'email_verified_at' => null,
            ]);

            $user->assignRole('seller');

            $seller = Seller::create([
                'user_id' => $user->id,
                'verification_status' => 'not_approved',
                'visibility_status' => 'draft',
            ]);

            // Generate OTP
            $otp = str_pad(random_int(100000, 999999), 6, '0', STR_PAD_LEFT);
            $user->update([
                'seller_otp' => $otp,
                'seller_otp_expires_at' => now()->addMinutes(10),
            ]);

            // Send OTP email
            try {
                Mail::send('emails.otp', ['otp' => $otp], function ($message) use ($user) {
                    $message->to($user->email)
                        ->subject('OTP for Registration');
                });
            } catch (\Exception $e) {
                Log::error('Failed to send OTP email: ' . $e->getMessage());
            }

            Log::info('OTP for ' . $user->email . ': ' . $otp);

            return ApiResponseType::sendJsonResponse(
                true,
                'OTP sent to your email',
                [
                    'user_id' => $user->id,
                    'email' => $user->email,
                    'requires_verification' => true,
                ],
                201
            );

        } catch (ValidationException $e) {
            return ApiResponseType::sendJsonResponse(
                false,
                'Validation failed',
                $e->errors(),
                200
            );
        } catch (\Exception $e) {
            Log::error('Registration failed: ' . $e->getMessage());
            return ApiResponseType::sendJsonResponse(
                false,
                'Registration failed: ' . $e->getMessage(),
                [],
                200
            );
        }
    }

    public function verifyOtp(Request $request): JsonResponse
    {
      
        try {
            $request->validate([
                'email' => 'required|email|exists:users,email',
                'otp' => 'required|string|size:6',
            ]);
            } catch (ValidationException $e) {
                return ApiResponseType::sendJsonResponse(
                    false,
                    'Validation failed',
                    $e->errors(),
                    200
                );
            }

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return ApiResponseType::sendJsonResponse(false, 'User not found', [], 200);
        }

        if ($user->seller_otp !== $request->otp) {
            return ApiResponseType::sendJsonResponse(false, 'Invalid OTP', [], 200);
        }

        if ($user->seller_otp_expires_at && now()->gt($user->seller_otp_expires_at)) {
            return ApiResponseType::sendJsonResponse(false, 'OTP expired. Please request a new one.', [], 200);
        }

        $user->update([
            'email_verified_at' => now(),
            'seller_otp' => null,
            'seller_otp_expires_at' => null,
        ]);

        return ApiResponseType::sendJsonResponse(
            true,
            'Email verified successfully',
            [
                'user_id' => $user->id,
                'email' => $user->email,
                'is_verified' => true,
            ],
            200
        );
    }

    public function resendOtp(Request $request): JsonResponse
    {
        try {
        $request->validate([
            'email' => 'required|email|exists:users,email',
        ]);
        } catch (ValidationException $e) {
            return ApiResponseType::sendJsonResponse(
                false,
                'Validation failed',
                $e->errors(),
                200
            );
        }

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return ApiResponseType::sendJsonResponse(false, 'User not found', [], 200);
        }

        $otp = str_pad(random_int(100000, 999999), 6, '0', STR_PAD_LEFT);
        $user->update([
            'seller_otp' => $otp,
            'seller_otp_expires_at' => now()->addMinutes(10),
        ]);

        try {
            Mail::send('emails.otp', ['otp' => $otp], function ($message) use ($user) {
                $message->to($user->email)
                    ->subject('OTP for Registration');
            });
        } catch (\Exception $e) {
            Log::error('Failed to resend OTP: ' . $e->getMessage());
        }

        Log::info('OTP resent to ' . $user->email . ': ' . $otp);

        return ApiResponseType::sendJsonResponse(
            true,
            'OTP resent to your email',
            [],
            200
        );
    }

    public function completeRegistration(Request $request): JsonResponse
    {
        // $request->validate([
        //     'user_id' => 'required|exists:users,id',
        //     'address' => 'required|string|max:500',
        //     'city' => 'required|string|max:100',
        //     'state' => 'required|string|max:100',
        //     'landmark' => 'required|string|max:200',
        //     'zipcode' => 'required|string|max:20',
        //     'country' => 'required|string|max:100',
        //     'latitude' => 'nullable|numeric|between:-90,90',
        //     'longitude' => 'nullable|numeric|between:-180,180',
        //     'business_license' => 'required|file|mimes:jpg,jpeg,png,pdf|max:2048',
        //     'articles_of_incorporation' => 'required|file|mimes:jpg,jpeg,png,pdf|max:2048',
        //     'national_identity_card' => 'required|file|mimes:jpg,jpeg,png,pdf|max:2048',
        //     'authorized_signature' => 'required|file|mimes:jpg,jpeg,png|max:2048',
        // ]);

         try {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'address' => 'required|string|max:500',
            'city' => 'required|string|max:100',
            'state' => 'required|string|max:100',
            'landmark' => 'required|string|max:200',
            'zipcode' => 'required|string|max:20',
            'country' => 'required|string|max:100',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'business_license' => 'required|file|mimes:jpg,jpeg,png,pdf|max:2048',
            'articles_of_incorporation' => 'required|file|mimes:jpg,jpeg,png,pdf|max:2048',
            'national_identity_card' => 'required|file|mimes:jpg,jpeg,png,pdf|max:2048',
            'authorized_signature' => 'required|file|mimes:jpg,jpeg,png|max:2048',
        ]);
    } catch (ValidationException $e) {
        return ApiResponseType::sendJsonResponse(
            false,
            'Validation failed',
            $e->errors(),
            200
        );
    }

        try {
            $user = User::find($request->user_id);

            if (!$user->email_verified_at) {
                return ApiResponseType::sendJsonResponse(
                    false,
                    'Email not verified. Please verify OTP first.',
                    [],
                    200
                );
            }

            $seller = Seller::where('user_id', $user->id)->first();

            if (!$seller) {
                return ApiResponseType::sendJsonResponse(false, 'Seller not found', [], 404);
            }

            // Update seller with address
            $seller->update([
                'address' => $request->address,
                'city' => $request->city,
                'state' => $request->state,
                'landmark' => $request->landmark,
                'zipcode' => $request->zipcode,
                'country' => $request->country,
                'latitude' => $request->latitude,
                'longitude' => $request->longitude,
                'verification_status' => 'not_approved',
                'visibility_status' => 'draft',
            ]);

            // Handle file uploads
            $collections = [
                'business_license',
                'articles_of_incorporation',
                'national_identity_card',
                'authorized_signature'
            ];

            foreach ($collections as $collection) {
                if ($request->hasFile($collection)) {
                    $seller->clearMediaCollection($collection);
                    $seller->addMedia($request->file($collection))
                        ->toMediaCollection($collection);
                }
            }

            return ApiResponseType::sendJsonResponse(
                true,
                'Registration completed successfully',
                $seller,
                200
            );

        } catch (\Exception $e) {
            Log::error('Complete registration failed: ' . $e->getMessage());
            return ApiResponseType::sendJsonResponse(
                false,
                'Failed to complete registration: ' . $e->getMessage(),
                [],
                200
            );
        }
    }

    /**
     * Delete Seller Account
     */
    public function deleteAccount(Request $request): JsonResponse
    {
        try {
            $user = $request->user();
            $seller = $user?->seller();

            if (!$seller) {
                return ApiResponseType::sendJsonResponse(false, __('labels.seller_not_found'), null, 404);
            }

            // Validate permission: either user is the main seller, or has SELLER_DELETE permission
            if ((int) $seller->user_id !== (int) $user->id) {
                if (function_exists('setPermissionsTeamId')) {
                    setPermissionsTeamId($seller->id);
                }
                if (!$user->hasPermissionTo(SellerPermissionEnum::SELLER_DELETE())) {
                    return ApiResponseType::sendJsonResponse(false, __('labels.permission_denied'), [], 403);
                }
            }

            DB::beginTransaction();
            $seller->delete();
            $seller->media()->each(function ($media) {
                $media->delete();
            });
            $user->delete();
            DB::commit();

            return ApiResponseType::sendJsonResponse(true, __('labels.account_deleted_successfully'), []);
        } catch (\Exception $e) {
            DB::rollBack();
            return ApiResponseType::sendJsonResponse(false, __('labels.account_deletion_failed', ['error' => $e->getMessage()]), []);
        }
    }
}

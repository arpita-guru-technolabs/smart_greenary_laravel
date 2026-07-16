<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Models\Seller;
use App\Models\User;
use App\Traits\AuthTrait;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{
    use AuthTrait;
    protected string $role = 'seller';

    /**
     * Show Documents Upload (Step 2)
     */
    public function showDocuments()
    {
        if (!session('seller_registration.step1_complete')) {
            return redirect()->route('seller.register')
                ->with('error', 'Please complete registration first.');
        }
        
        $userId = session('seller_registration.user_id');
        $seller = Seller::where('user_id', $userId)->first();
        
        // ============================================
        // FIX: Force reload media relationships
        // ============================================
        if ($seller) {
            $seller->load('media');
        }
        
        // Get existing media files
        $documents = [];
        $collections = [
            'business_license',
            'articles_of_incorporation',
            'national_identity_card',
            'authorized_signature'
        ];
        
        foreach ($collections as $collection) {
            // ============================================
            // FIX: Use getMedia() instead of getFirstMedia()
            // ============================================
            $media = $seller ? $seller->getMedia($collection)->first() : null;
            if ($media) {
                $documents[$collection] = [
                    'exists' => true,
                    'name' => $media->file_name,
                    'size' => $media->size,
                    'url' => $media->getUrl(),
                ];
            } else {
                $documents[$collection] = ['exists' => false];
            }
        }
        
        // Clear errors on first visit
        if (!session('_old_input') && !session('errors')) {
            session()->forget('errors');
        }
        
        return view('seller.auth.documents', compact('seller', 'documents'));
    }

/**
 * Process Documents Upload (Step 2)
 */
public function uploadDocuments(Request $request)
{
    $sellerId = session('seller_registration.seller_id');
    $seller = Seller::find($sellerId);
    
    // Check if files already exist
    $existingFiles = [];
    $collections = [
        'business_license',
        'articles_of_incorporation',
        'national_identity_card',
        'authorized_signature'
    ];
    
    foreach ($collections as $collection) {
        $media = $seller ? $seller->getMedia($collection)->first() : null;
        $existingFiles[$collection] = $media ? true : false;
    }
    
    // Build validation rules - Only require if file doesn't exist AND no new file uploaded
    $rules = [];
    
    foreach ($collections as $collection) {
        if ($existingFiles[$collection]) {
            // File already exists - make it nullable
            $rules[$collection] = 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048';
        } else {
            // File doesn't exist - require it
            if ($collection == 'authorized_signature') {
                $rules[$collection] = 'required|file|mimes:jpg,jpeg,png|max:2048';
            } else {
                $rules[$collection] = 'required|file|mimes:jpg,jpeg,png,pdf|max:2048';
            }
        }
    }

    // Custom error messages
    $messages = [
        'business_license.required' => 'The business license is required.',
        'business_license.mimes' => 'The business license must be a file of type: JPEG, PNG, PDF.',
        'business_license.max' => 'The business license must not be larger than 2MB.',
        'articles_of_incorporation.required' => 'The articles of incorporation is required.',
        'articles_of_incorporation.mimes' => 'The articles of incorporation must be a file of type: JPEG, PNG, PDF.',
        'articles_of_incorporation.max' => 'The articles of incorporation must not be larger than 2MB.',
        'national_identity_card.required' => 'The national identity card is required.',
        'national_identity_card.mimes' => 'The national identity card must be a file of type: JPEG, PNG, PDF.',
        'national_identity_card.max' => 'The national identity card must not be larger than 2MB.',
        'authorized_signature.required' => 'The authorized signature is required.',
        'authorized_signature.mimes' => 'The authorized signature must be a file of type: JPEG, PNG.',
        'authorized_signature.max' => 'The authorized signature must not be larger than 2MB.',
    ];

    $validator = Validator::make($request->all(), $rules, $messages);

    if ($validator->fails()) {
        return redirect()->back()
            ->withErrors($validator)
            ->withInput();
    }

    try {
        if (!$seller) {
            return redirect()->route('seller.register')
                ->with('error', 'Seller not found. Please start over.');
        }

        foreach ($collections as $collection) {
            if ($request->hasFile($collection) && $request->file($collection)->isValid()) {
                $seller->clearMediaCollection($collection);
                $seller->addMedia($request->file($collection))
                    ->toMediaCollection($collection);
            }
        }

        session(['seller_registration.step2_complete' => true]);

        return redirect()->route('seller.register.address')
            ->with('success', 'Documents uploaded successfully!');

    } catch (\Exception $e) {
        Log::error('Document upload failed: ' . $e->getMessage());
        return redirect()->back()
            ->with('error', 'Failed to upload documents: ' . $e->getMessage())
            ->withInput();
    }
}

    /**
     * Save address to session via AJAX
     */
    public function saveAddressToSession(Request $request)
    {
        try {
            session(['seller_registration.address_data' => [
                'address' => $request->address,
                'city' => $request->city,
                'landmark' => $request->landmark,
                'state' => $request->state,
                'zipcode' => $request->zipcode,
                'country' => $request->country,
                'latitude' => $request->latitude,
                'longitude' => $request->longitude,
            ]]);
            
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()]);
        }
    }

    /**
     * Show Address (Step 3)
     */    
    public function showAddress()
    {
        if (!session('seller_registration.step2_complete')) {
            return redirect()->route('seller.register.documents')
                ->with('error', 'Please upload your documents first.');
        }
        
        $userId = session('seller_registration.user_id');
        $seller = Seller::where('user_id', $userId)->first();
        
        if (!$seller) {
            return redirect()->route('seller.register')
                ->with('error', 'Seller not found. Please start over.');
        }
        
        // ============================================
        // FIX: Store address data in session for persistence
        // ============================================
        if (!session('seller_registration.address_data')) {
            session(['seller_registration.address_data' => [
                'address' => $seller->address ?? '',
                'city' => $seller->city ?? '',
                'landmark' => $seller->landmark ?? '',
                'state' => $seller->state ?? '',
                'zipcode' => $seller->zipcode ?? '',
                'country' => $seller->country ?? '',
                'latitude' => $seller->latitude ?? '',
                'longitude' => $seller->longitude ?? '',
            ]]);
        }
        
        return view('seller.auth.address', compact('seller'));
    }

    /**
     * Process Address (Step 3)
     */
    public function saveAddress(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'address' => 'required|string|max:500',
            'city' => 'required|string|max:100',
            'landmark' => 'required|string|max:200',
            'state' => 'required|string|max:100',
            'zipcode' => 'required|string|max:20',
            'country' => 'required|string|max:100',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        try {
            $sellerId = session('seller_registration.seller_id');
            $seller = Seller::find($sellerId);
            
            if (!$seller) {
                return redirect()->route('seller.register')
                    ->with('error', 'Seller not found. Please start over.');
            }

            $seller->update([
                'address' => $request->address,
                'city' => $request->city,
                'landmark' => $request->landmark,
                'state' => $request->state,
                'zipcode' => $request->zipcode,
                'country' => $request->country,
                'latitude' => $request->latitude,
                'longitude' => $request->longitude,
                'verification_status' => 'not_approved',
            ]);

            // ============================================
            // FIX: Update session with address data
            // ============================================
            session(['seller_registration.address_data' => [
                'address' => $request->address,
                'city' => $request->city,
                'landmark' => $request->landmark,
                'state' => $request->state,
                'zipcode' => $request->zipcode,
                'country' => $request->country,
                'latitude' => $request->latitude,
                'longitude' => $request->longitude,
            ]]);

            // ============================================
            // FIX: Store step3_complete in session
            // ============================================
            session(['seller_registration.step3_complete' => true]);

            $userId = session('seller_registration.user_id');
            session()->forget('seller_registration');
            session(['registration_success_user_id' => $userId]);

            return redirect()->route('seller.register.success')
                ->with('success', 'Account created successfully!');

        } catch (\Exception $e) {
            Log::error('Address save failed: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Failed to save address: ' . $e->getMessage());
        }
    }

    /**
     * Show login form - Regenerate CSRF token
     */
    public function loginSeller(): View
    {
        // Regenerate CSRF token on each page load
        //session()->regenerateToken();
        return view('seller.auth.login');
    }


    /**
     * Logout
     */
    /*public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect(route('seller.login'))->with('success', 'Logged out successfully.');
    }*/

    public function logout(Request $request)
    {
        try {
            Auth::logout(); // Log the user out
            $request->session()->invalidate(); // Invalidate the session
            return redirect(route('seller.login'));

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => __('labels.logout_failed', ['error' => $e->getMessage()]),
                'data' => []
            ], 500);
        }
    }

    /**
     * Show registration form
     */
    public function showRegistrationForm(): View
    {
       // Check if user is already registered (coming back from step 2 or 3)
        $userId = session('seller_registration.user_id');
        $user = null;
        
        if ($userId) {
            $user = User::find($userId);
        }
        
        return view('seller.auth.register', compact('user'));
    }

    /**
     * Handle registration - sends OTP
     */
    public function registerBackup(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone' => 'required|string|max:20',
            'password' => [
                'required',
                'min:8',
                'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/'
            ],
            'password_confirmation' => 'required|same:password',
            'terms' => 'required|accepted',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            // Check if phone already exists
            if (User::where('mobile', $request->phone)->exists()) {
                return redirect()->back()
                    ->withErrors(['phone' => 'This phone number is already registered.'])
                    ->withInput();
            }

            // Create User
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'mobile' => $request->phone,
                'password' => Hash::make($request->password),
                'access_panel' => 'seller',
                'email_verified_at' => null,
            ]);

            $user->assignRole('seller');

            Seller::create([
                'user_id' => $user->id,
                'verification_status' => 'not_approved',
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

            session(['otp_email' => $user->email]);

            return redirect()->route('seller.register.verify-otp')
                ->with('success', 'OTP sent to your email!');

        } catch (\Exception $e) {
            Log::error('Registration failed: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Registration failed: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Handle registration - sends OTP
     */
 
      /**
 * Handle registration - sends OTP
 */
public function register(Request $request)
{
    // Check if user exists (coming back from step 2/3)
    $userId = session('seller_registration.user_id');
    $existingUser = $userId ? User::find($userId) : null;
    
    // Build validation rules
    $rules = [
        'name' => 'required|string|max:255',
        'email' => 'required|email',
        'phone' => 'required|string|max:20',
        'terms' => 'required|accepted',
    ];
    
    // Only require password if user doesn't exist or password is provided
    if (!$existingUser) {
        $rules['password'] = [
            'required',
            'min:8',
            'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/'
        ];
        $rules['password_confirmation'] = 'required|same:password';
    } else {
        // If user exists, password is optional (only validate if provided)
        $rules['password'] = [
            'nullable',
            'min:8',
            'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/'
        ];
        $rules['password_confirmation'] = 'nullable|same:password';
    }
    
    $validator = Validator::make($request->all(), $rules);

    if ($validator->fails()) {
        return redirect()->back()
            ->withErrors($validator)
            ->withInput();
    }

    try {
        // If user exists, update instead of creating new
        if ($existingUser) {
            $existingUser->update([
                'name' => $request->name,
                'email' => $request->email,
                'mobile' => $request->phone,
            ]);
            
            // Only update password if provided
            if ($request->filled('password')) {
                $existingUser->update([
                    'password' => Hash::make($request->password),
                ]);
            }
            
            // Check if email is already verified
            if ($existingUser->email_verified_at) {
                session(['seller_registration.step1_complete' => true]);
                return redirect()->route('seller.register.documents')
                    ->with('success', 'Email already verified! Continue to documents.');
            }
            
            // Resend OTP
            $otp = str_pad(random_int(100000, 999999), 6, '0', STR_PAD_LEFT);
            $existingUser->update([
                'seller_otp' => $otp,
                'seller_otp_expires_at' => now()->addMinutes(10),
            ]);
            
            // Send OTP email
            try {
                Mail::send('emails.otp', ['otp' => $otp], function ($message) use ($existingUser) {
                    $message->to($existingUser->email)
                        ->subject('OTP for Registration');
                });
            } catch (\Exception $e) {
                Log::error('Failed to send OTP email: ' . $e->getMessage());
            }
            
            Log::info('OTP for ' . $existingUser->email . ': ' . $otp);
            session(['otp_email' => $existingUser->email]);
            
            return redirect()->route('seller.register.verify-otp')
                ->with('success', 'OTP sent to your email!');
        }
        
        // ============================================
        // NEW USER - Check for duplicates
        // ============================================
        if (User::where('email', $request->email)->exists()) {
            return redirect()->back()
                ->withErrors(['email' => 'This email is already registered.'])
                ->withInput();
        }
        
        if (User::where('mobile', $request->phone)->exists()) {
            return redirect()->back()
                ->withErrors(['phone' => 'This phone number is already registered.'])
                ->withInput();
        }

        // Create New User
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'mobile' => $request->phone,
            'password' => Hash::make($request->password),
            'access_panel' => 'seller',
            'email_verified_at' => null,
        ]);

        $user->assignRole('seller');

        $seller = Seller::create([
            'user_id' => $user->id,
            'verification_status' => 'not_approved',
        ]);

        // Store in session for steps 2 & 3
        session([
            'seller_registration.user_id' => $user->id,
            'seller_registration.seller_id' => $seller->id,
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
        session(['otp_email' => $user->email]);

        return redirect()->route('seller.register.verify-otp')
            ->with('success', 'OTP sent to your email!');

    } catch (\Exception $e) {
        Log::error('Registration failed: ' . $e->getMessage());
        return redirect()->back()
            ->with('error', 'Registration failed: ' . $e->getMessage())
            ->withInput();
    }
}

    /**
     * Show OTP verification form
     */
    public function showOtpVerification(): View
    {
        return view('seller.auth.verify-otp');
    }

    /**
     * Verify OTP
     */
    public function verifyOtpBackup(Request $request)
    {
        $request->validate([
            'otp' => 'required|string|size:6',
        ]);

        $user = User::where('email', session('otp_email'))->first();

        if (!$user) {
            return redirect()->route('seller.register')
                ->with('error', 'User not found. Please register again.');
        }

        // Check OTP
        if ($user->seller_otp !== $request->otp) {
            return back()->withErrors([
                'otp' => 'Invalid OTP. Please try again.',
            ]);
        }

        // Check if OTP expired
        if ($user->seller_otp_expires_at && now()->gt($user->seller_otp_expires_at)) {
            return back()->withErrors([
                'otp' => 'OTP has expired. Please resend a new one.',
            ]);
        }

        // OTP valid - Verify email
        $user->update([
            'email_verified_at' => now(),
            'seller_otp' => null,
            'seller_otp_expires_at' => null,
        ]);

        session()->forget('otp_email');

        return redirect()->route('seller.register.success')
            ->with('success', 'Account verified successfully!');
    }

    /**
 * Verify OTP
 */
public function verifyOtp(Request $request)
{
    $request->validate([
        'otp' => 'required|string|size:6',
    ]);

    $user = User::where('email', session('otp_email'))->first();

    if (!$user) {
        return redirect()->route('seller.register')
            ->with('error', 'User not found. Please register again.');
    }

    // Check OTP
    if ($user->seller_otp !== $request->otp) {
        return back()->withErrors([
            'otp' => 'Invalid OTP. Please try again.',
        ]);
    }

    // Check if OTP expired
    if ($user->seller_otp_expires_at && now()->gt($user->seller_otp_expires_at)) {
        return back()->withErrors([
            'otp' => 'OTP has expired. Please resend a new one.',
        ]);
    }

    // OTP valid - Verify email
    $user->update([
        'email_verified_at' => now(),
        'seller_otp' => null,
        'seller_otp_expires_at' => null,
    ]);

    // ============================================
    // MARK STEP 1 AS COMPLETE
    // ============================================
    session(['seller_registration.step1_complete' => true]);

    session()->forget('otp_email');

    // ============================================
    // REDIRECT TO DOCUMENTS (STEP 2) INSTEAD OF SUCCESS
    // ============================================
    return redirect()->route('seller.register.documents')
        ->with('success', 'Email verified! Now upload your documents.');
}

    /**
     * Resend OTP
     */
    public function resendOtp()
    {
        $email = session('otp_email');

        if (!$email) {
            return redirect()->route('seller.register')
                ->with('error', 'Session expired. Please register again.');
        }

        $user = User::where('email', $email)->first();

        if (!$user) {
            return redirect()->route('seller.register')
                ->with('error', 'User not found. Please register again.');
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

        return back()->with('success', 'OTP resent to your email!');
    }

    /**
     * Show registration success page
     */
    public function registrationSuccess(): View
    {
        // Regenerate CSRF token for new user
        session()->regenerateToken();
        return view('seller.auth.register-success');
    }
}
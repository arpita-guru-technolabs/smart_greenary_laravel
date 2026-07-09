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
        return view('seller.auth.register');
    }

    /**
     * Handle registration - sends OTP
     */
    public function register(Request $request)
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
     * Show OTP verification form
     */
    public function showOtpVerification(): View
    {
        return view('seller.auth.verify-otp');
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

        session()->forget('otp_email');

        return redirect()->route('seller.register.success')
            ->with('success', 'Account verified successfully!');
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
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use App\Models\User;
use Carbon\Carbon;

class OTPVerificationController extends Controller
{
    /**
     * Show OTP verification page
     */
    public function show()
    {
        if (! session()->has('otp_user_id')) {
            return redirect()->route('register.hub')
                ->with('error', 'Session expired or invalid. Please continue again.');
        }

        return view('auth.verify-otp');
    }

    /**
     * Verify OTP (ONE-TIME MARK)
     */
    public function verify(Request $request)
    {
        $request->validate([
            'otp' => ['required', 'digits:6'],
        ]);

        $userId = session('otp_user_id');
        if (! $userId) {
            return redirect()->route('register.hub')
                ->with('error', 'Session expired. Please continue again.');
        }

        $user = User::find($userId);
        if (! $user) {
            $this->clearOtpState();
            return redirect()->route('register.hub')
                ->with('error', 'User not found.');
        }

        /**
         * Resolve OTP + expiry
         */
        [$expectedOtp, $expiresAt] = $this->resolveOtpData($user);

        if (! $expectedOtp) {
            return back()->with('error', 'OTP not found or expired. Please resend OTP.');
        }

        if ($expiresAt && now()->greaterThan(Carbon::parse($expiresAt))) {
            return back()->with('error', 'OTP has expired. Please request a new OTP.');
        }

        if ((string) $request->otp !== (string) $expectedOtp) {
            return back()->with('error', 'Incorrect OTP. Please try again.');
        }

        /**
         * CONTEXT-BASED OTP ACTION
         */
        $context = session('otp_context');

        /**
         * REGISTRATION OTP (ONE TIME)
         */
        if ($context === 'registration' && is_null($user->email_verified_at)) {
            $user->update([
                'email_verified_at' => now(),
            ]);
        }

        /**
         * SECURE ACTION OTP (CRITICAL FIX)
         */
        if ($context === 'secure_action') {

            // MARK VERIFIED (DO NOT CLEAR HERE)
            session(['otp_verified_for_action' => true]);

            $redirectUrl = session(
                'otp_intended_url',
                route(
                    app(\App\Http\Controllers\Auth\LoginController::class)
                        ->resolveDashboardRoute($user)
                )
            );

            // Cleanup OTP ONLY (NOT otp_verified_for_action)
            session()->forget(['otp_context', 'otp_intended_url']);
            $this->clearOtpState($user->id);

            Auth::login($user);

            return redirect($redirectUrl);
        }

        /**
         * Cleanup OTP storage (DB)
         */
        if (Schema::hasColumn('users', 'otp_code')) {
            $user->otp_code = null;
        }
        if (Schema::hasColumn('users', 'otp_expires_at')) {
            $user->otp_expires_at = null;
        }
        $user->save();

        /**
         * Cleanup session (registration flow)
         */
        $this->clearOtpState($user->id);

        Auth::login($user);

        return redirect()->intended(
            route(
                app(\App\Http\Controllers\Auth\LoginController::class)
                    ->resolveDashboardRoute($user)
            )
        );
    }

    /**
     * Resend OTP
     */
    public function resend()
    {
        $userId = session('otp_user_id');
        if (! $userId) {
            return redirect()->route('register.hub')
                ->with('error', 'Session expired.');
        }

        $user = User::find($userId);
        if (! $user || empty($user->email)) {
            return back()->with('error', 'Unable to resend OTP.');
        }

        $otp = random_int(100000, 999999);
        $expiresAt = now()->addMinutes(15);

        if (Schema::hasColumn('users', 'otp_code') && Schema::hasColumn('users', 'otp_expires_at')) {
            $user->otp_code = (string) $otp;
            $user->otp_expires_at = $expiresAt;
            $user->save();
        } else {
            Cache::put("otp_code:{$user->id}", $otp, $expiresAt);
            Cache::put("otp_expires:{$user->id}", $expiresAt, $expiresAt);
        }

        session([
            'otp_code' => (string) $otp,
            'otp_expires_at' => $expiresAt,
        ]);

        try {
            Mail::raw(
                "Your OTP is {$otp}. This OTP is valid for 15 minutes.",
                fn ($m) => $m->to($user->email)->subject('OTP Verification')
            );
        } catch (\Throwable $e) {
            Log::error('OTP resend failed: ' . $e->getMessage());
            return back()->with('error', 'Failed to send OTP.');
        }

        return back()->with('success', 'A new OTP has been sent to your email.');
    }

    /**
     * Resolve OTP data
     */
    protected function resolveOtpData(User $user): array
    {
        if (session()->has('otp_code')) {
            return [session('otp_code'), session('otp_expires_at')];
        }

        if (Schema::hasColumn('users', 'otp_code')) {
            return [$user->otp_code, $user->otp_expires_at];
        }

        return [
            Cache::get("otp_code:{$user->id}"),
            Cache::get("otp_expires:{$user->id}")
        ];
    }

    /**
     * Clear OTP state
     */
    protected function clearOtpState(?int $userId = null): void
    {
        session()->forget([
            'otp_user_id',
            'otp_code',
            'otp_expires_at',
            'otp_context',
        ]);

        if ($userId) {
            Cache::forget("otp_code:{$userId}");
            Cache::forget("otp_expires:{$userId}");
        }
    }
}

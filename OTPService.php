<?php

namespace App\Services;

use App\Models\User;
use App\Models\OTPVerification;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Carbon\Carbon;

class OTPService
{
    /**
     * Generate and send OTP to user
     */
    public function sendOTP(User $user, $type = 'email_verification')
    {
        // Delete any existing OTPs for this user and type
        OTPVerification::where('user_id', $user->id)
            ->where('type', $type)
            ->delete();

        // Generate 6-digit OTP
        $otp = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        
        // Create OTP record
        $otpRecord = OTPVerification::create([
            'user_id' => $user->id,
            'type' => $type,
            'otp' => $otp,
            'expires_at' => Carbon::now()->addMinutes(15),
            'token' => Str::random(60),
            'is_used' => false, // ✅ Explicitly set
        ]);

        // Send OTP via Email
        $this->sendOTPEmail($user, $otp);

        return $otpRecord;
    }

    /**
     * Send OTP via Email
     */
    protected function sendOTPEmail(User $user, $otp)
    {
        // Skip mail in test environment
        if (app()->environment('testing')) {
            return;
        }

        Mail::send('emails.otp-verification', [
            'user' => $user,
            'otp' => $otp,
            'expiry_minutes' => 15,
        ], function ($message) use ($user) {
            $message->to($user->email)
                ->subject('Email Verification OTP - Takniki Shiksha Careers');
        });
    }

    /**
     * Verify OTP
     */
    public function verifyOTP(User $user, $otp, $type = 'email_verification')
    {
        $otpRecord = OTPVerification::where('user_id', $user->id)
            ->where('type', $type)
            ->where('otp', $otp)
            ->where('expires_at', '>', Carbon::now())
            ->where('is_used', false)
            ->first();

        if (!$otpRecord) {
            return false;
        }

        // Mark OTP as used
        $otpRecord->update(['is_used' => true]);

        // Mark user as verified based on type
        if ($type === 'email_verification') {
            $user->email_verified_at = Carbon::now();
            $user->save(); // ✅ Explicitly save
        } elseif ($type === 'phone_verification') {
            $user->phone_verified_at = Carbon::now();
            $user->save(); // ✅ Explicitly save
        }

        return true;
    }

    /**
     * Resend OTP
     */
    public function resendOTP(User $user, $type = 'email_verification')
    {
        return $this->sendOTP($user, $type);
    }
}

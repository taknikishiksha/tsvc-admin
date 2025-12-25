<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class OtpRequired
{
    public function handle(Request $request, Closure $next)
    {
        /**
         * ✅ If OTP already verified for this secure action
         */
        if (session('otp_verified_for_action') === true) {
            return $next($request);
        }

        /**
         * 🚫 Prevent OTP redirect loop
         */
        if ($request->routeIs('verification.otp')) {
            return $next($request);
        }

        /**
         * 🔐 Trigger OTP for sensitive action (ONE-TIME)
         * Do not overwrite intended URL if already set
         */
        session([
            'otp_context' => 'secure_action',
        ]);

        if (! session()->has('otp_intended_url')) {
            session([
                'otp_intended_url' => $request->fullUrl(),
            ]);
        }

        return redirect()->route('verification.otp');
    }
}

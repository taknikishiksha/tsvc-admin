<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

class EnsureProfileComplete
{
    /**
     * Handle an incoming request.
     *
     * - Only enforces for users with role 'client'.
     * - Allows specific routes (profile completion routes, logout, etc.) to be accessed without redirect.
     */
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();

        // If not authenticated or not a client, nothing to do.
        if (! $user || ($user->role ?? null) !== 'client') {
            return $next($request);
        }

        // Allowed route names (client should be able to access these even if profile incomplete)
        $allowed = [
            'client.dashboard',        // allow dashboard so client sees prompt inline (no redirect)
            'client.profile.complete', // GET (show form)
            'client.profile.store',    // POST (save form)
            'logout',
];

        $currentRouteName = optional($request->route())->getName();

        if ($currentRouteName && in_array($currentRouteName, $allowed, true)) {
            return $next($request);
        }

        // Load profile relation if available
        $profile = $user->clientProfile ?? null;

        // If model has isComplete() helper, prefer that
        if ($profile && method_exists($profile, 'isComplete')) {
            $isComplete = (bool) $profile->isComplete();
        } else {
            // Fallback: check minimal required fields on profile
            $isComplete = false;
            if ($profile) {
                $required = [
                    'service_type',
                    'experience_level',
                    'emergency_contact_name',
                    'emergency_contact_phone',
                ];

                $missing = [];
                foreach ($required as $field) {
                    if (empty($profile->{$field})) {
                        $missing[] = $field;
                    }
                }

                $isComplete = empty($missing);
            }
        }

        if (! $isComplete) {
            // redirect to profile completion route (safe: check route exists)
            if (Route::has('client.profile.complete')) {
                return redirect()->route('client.profile.complete');
            }

            // fallback to a URL path if route name missing
            return redirect('/client/profile-complete');
        }

        return $next($request);
    }
}
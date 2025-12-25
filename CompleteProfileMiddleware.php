<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CompleteProfileMiddleware
{
    public function handle(Request $request, Closure $next, $role): Response
    {
        $user = $request->user();

        // If user is not logged in
        if (!$user) {
            return redirect()->route('login');
        }

        /**
         * ===============================
         * TEACHER PROFILE CHECK
         * ===============================
         * Important Fix:
         * Do NOT block teacher.dashboard
         * or any dashboard-related pages.
         * Only block pages where profile is REQUIRED.
         * ===============================
         */
        if ($role === 'teacher' && $user->role === 'teacher') {

            // Teacher model relation missing = show profile create page
            if (!$user->yogaTeacher) {

                // Allow dashboard first load — DO NOT BLOCK
                if ($request->routeIs('teacher.dashboard')) {
                    return $next($request);
                }

                // Allow AJAX stats calls
                if ($request->routeIs('teacher.stats')) {
                    return $next($request);
                }

                // Allow logout
                if ($request->routeIs('logout')) {
                    return $next($request);
                }

                // Block only pages where profile is needed
                return redirect()->route('teacher.profile')
                    ->with('warning', 'Please complete your teacher profile to continue.');
            }
        }

        /**
         * ===============================
         * CLIENT PROFILE CHECK
         * ===============================
         */
        if ($role === 'client' && $user->role === 'client') {

            if (!$user->client) {

                if ($request->routeIs('client.dashboard')) {
                    return $next($request);
                }

                return redirect()->route('client.profile.create')
                    ->with('warning', 'Please complete your client profile to continue.');
            }
        }

        return $next($request);
    }
}

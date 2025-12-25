<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * Usage examples:
     * ->middleware('role:superadmin')
     * ->middleware('role:admin|service')
     * ->middleware('role:admin,hr')
     */
    public function handle(Request $request, Closure $next, $roles)
    {
        // 1️⃣ User must be logged in
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $user = auth()->user();

        /**
         * 2️⃣ Normalize allowed roles
         * Supports:
         *  - admin,hr
         *  - admin|hr
         */
        $allowedRoles = preg_split('/[,\|]+/', $roles);
        $allowedRoles = collect($allowedRoles)
            ->map(fn ($r) => strtolower(trim($r)))
            ->filter()
            ->values()
            ->toArray();

        /**
         * 3️⃣ Normalize user's DB role column
         * (supports single or multi role stored as csv/pipe)
         */
        $dbRoles = [];
        if (!empty($user->role)) {
            $dbRoles = preg_split('/[,\|]+/', $user->role);
            $dbRoles = array_map(
                fn ($r) => strtolower(trim($r)),
                array_filter($dbRoles)
            );
        }

        /**
         * 4️⃣ Superadmin bypass (hard rule)
         */
        if (in_array('superadmin', $dbRoles, true)) {
            return $next($request);
        }

        /**
         * 5️⃣ Check DB role column
         */
        if (array_intersect($allowedRoles, $dbRoles)) {
            return $next($request);
        }

        /**
         * 6️⃣ Check Spatie roles (preferred if available)
         */
        try {
            if (method_exists($user, 'getRoleNames')) {
                $spatieRoles = $user->getRoleNames()
                    ->map(fn ($r) => strtolower(trim($r)))
                    ->toArray();

                if (array_intersect($allowedRoles, $spatieRoles)) {
                    return $next($request);
                }
            }
        } catch (\Throwable $e) {
            Log::warning('RoleMiddleware Spatie check failed', [
                'user_id' => $user->id,
                'error'   => $e->getMessage(),
            ]);
        }

        /**
         * 7️⃣ Unauthorized
         */
        abort(403, 'Unauthorized.');
    }
}

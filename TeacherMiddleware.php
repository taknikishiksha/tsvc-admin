<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TeacherMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (! Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();

        // role check
        if ($user->role !== 'teacher') {
            return redirect()->route('dashboard')->with('error', 'Access denied. Teacher role required.');
        }

        // Reload relations to avoid stale cached relations in session
        $user->refresh(); // reload from DB
        // Also ensure relation names (backwards compatibility)
        $profile = null;
        if (method_exists($user, 'teacherProfile')) {
            $profile = $user->teacherProfile;
        }
        if (! $profile && method_exists($user, 'yogaTeacher')) {
            $profile = $user->yogaTeacher;
        }

        if (! $profile) {
            // redirect to the profile completion form route (new name)
            return redirect()->route('teacher.profile.complete.form')
                             ->with('warning', 'Please complete your teacher profile first.');
        }

        // verification notice (if profile exists)
        if (isset($profile->verification_status) && $profile->verification_status !== 'verified') {
            if (! $request->session()->has('verification_warning_shown')) {
                $request->session()->flash('warning', 'Your profile is under verification. Some features may be limited.');
                $request->session()->put('verification_warning_shown', true);
            }
        }

        return $next($request);
    }
}

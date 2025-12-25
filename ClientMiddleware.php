<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class ClientMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Please login to access client dashboard.');
        }

        if (Auth::user()->role !== 'client') {
            return redirect()->route('dashboard')->with('error', 'Access denied. Client role required.');
        }

        if (!Auth::user()->client) {
            return redirect()->route('client.profile.create')
                ->with('warning', 'Please complete your client profile first.');
        }

        return $next($request);
    }
}
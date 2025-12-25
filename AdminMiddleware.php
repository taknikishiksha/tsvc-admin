<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'कृपया पहले लॉगिन करें।');
        }

        if (Auth::user()->role !== 'admin') {
            abort(403, 'Admin access required. आपका इस पेज तक पहुँचने का अधिकार नहीं है।');
        }

        return $next($request);
    }
}
<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminOrEmployeeMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (!Auth::check() || !in_array(Auth::user()->role, ['admin', 'employee'])) {
            \Log::info('Unauthorized access attempt by user role: ' . (Auth::check() ? Auth::user()->role : 'guest'));
            return redirect('/')->with('error', 'Unauthorized access');
        }

        \Log::info('Authorized access by user role: ' . Auth::user()->role);

        return $next($request);
    }
}

<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminOrEmployeeMiddleware
{
    /**
     * Menangani permintaan masuk dan memeriksa apakah user adalah admin atau employee.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // Memeriksa apakah user sudah login dan memiliki peran admin atau employee
        if (!Auth::check() || !in_array(Auth::user()->role, ['admin', 'employee'])) {
            // Mencatat percobaan akses tidak sah dengan role user
            \Log::info('Unauthorized access attempt by user role: ' . (Auth::check() ? Auth::user()->role : 'guest'));
            // Redirect ke halaman utama dengan pesan error
            return redirect('/')->with('error', 'Unauthorized access');
        }

        // Mencatat akses yang sah dengan role user
        \Log::info('Authorized access by user role: ' . Auth::user()->role);

        // Jika lolos pemeriksaan, teruskan permintaan
        return $next($request);
    }
}

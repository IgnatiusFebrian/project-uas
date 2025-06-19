<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminMiddleware
{
    /**
     * Menangani permintaan masuk dan memeriksa apakah user adalah admin.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // Memeriksa apakah user sudah login dan memiliki peran admin
        if (!Auth::check() || Auth::user()->role !== 'admin') {
            // Jika tidak, redirect ke halaman utama dengan pesan error
            return redirect('/')->with('error', 'Unauthorized access');
        }

        // Jika lolos pemeriksaan, teruskan permintaan
        return $next($request);
    }
}

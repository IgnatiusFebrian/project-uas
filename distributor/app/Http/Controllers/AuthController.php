<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;

class AuthController extends Controller
{
    /**
     * Menangani permintaan login dan mengembalikan token JWT.
     */
    public function login(Request $request)
    {
        // Mengambil email dan password dari request
        $credentials = $request->only('email', 'password');

        try {
            // Mencoba melakukan autentikasi dan mendapatkan token JWT
            if (! $token = JWTAuth::attempt($credentials)) {
                // Jika kredensial tidak valid, kembalikan error 401
                return response()->json(['error' => 'Invalid credentials'], 401);
            }
        } catch (JWTException $e) {
            // Jika terjadi kesalahan saat membuat token, kembalikan error 500
            return response()->json(['error' => 'Could not create token'], 500);
        }

        // Jika berhasil, kembalikan token JWT
        return response()->json(['token' => $token]);
    }

    /**
     * Logout user dengan menginvalidasi token.
     */
    public function logout()
    {
        // Menginvalidasi token JWT yang sedang digunakan
        JWTAuth::invalidate(JWTAuth::getToken());

        // Mengembalikan pesan logout berhasil
        return response()->json(['message' => 'Successfully logged out']);
    }

    /**
     * Refresh token JWT.
     */
    public function refresh()
    {
        // Mendapatkan token baru dengan me-refresh token lama
        $token = JWTAuth::refresh(JWTAuth::getToken());

        // Mengembalikan token baru
        return response()->json(['token' => $token]);
    }

    /**
     * Mendapatkan informasi user yang sedang terautentikasi.
     */
    public function me()
    {
        // Mengembalikan data user yang sedang login
        return response()->json(Auth::user());
    }
}

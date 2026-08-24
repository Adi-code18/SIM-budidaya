<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Rules\Recaptcha;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $throttleKey = Str::transliterate('api_login|' . Str::lower($request->input('email', '')) . '|' . $request->ip());

        // 1. Rate Limiting Check
        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            return response()->json([
                'status' => 'error',
                'message' => "Terlalu banyak percobaan login gagal. Silakan coba lagi dalam {$seconds} detik.",
            ], 429);
        }

        // 2. Validation
        $rules = [
            'email'    => 'required|string',
            'password' => 'required|string',
        ];

        if (!empty(config('services.recaptcha.secret_key')) && $request->has('g-recaptcha-response')) {
            $rules['g-recaptcha-response'] = ['required', new Recaptcha];
        }

        $request->validate($rules);

        $loginInput = trim($request->email);

        // 3. User lookup by email or phone
        $user = User::where('email', $loginInput)
            ->orWhere('no_tlp', $loginInput)
            ->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            RateLimiter::hit($throttleKey, 60);
            return response()->json([
                'status' => 'error',
                'message' => 'Email/No. HP atau kata sandi yang Anda masukkan salah.',
            ], 401);
        }

        // 4. One Session Enforcement (Revoke all previous tokens for this user)
        $user->tokens()->delete();

        // 5. Buat token Sanctum baru
        $token = $user->createToken('auth_token')->plainTextToken;

        RateLimiter::clear($throttleKey);

        return response()->json([
            'status' => 'success',
            'message' => 'Login berhasil',
            'data' => [
                'user' => [
                    'id_user' => $user->id_user,
                    'nama'    => $user->nama,
                    'email'   => $user->email,
                    'role'    => $user->role,
                    'no_tlp'  => $user->no_tlp,
                ],
                'token' => $token,
            ]
        ], 200);
    }

    public function logout(Request $request)
    {
        // Hapus token yang sedang digunakan
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Berhasil logout'
        ], 200);
    }
}
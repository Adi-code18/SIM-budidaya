<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Rules\Recaptcha;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * Tampilkan halaman login web manajer.
     */
    public function showLoginForm()
    {
        if (Auth::check()) {
            if (Auth::user()->role === 'manajer') {
                return redirect()->route('dashboard');
            }
            return redirect()->route('mobile.petugas.login');
        }

        return view('layouts.auth.login');
    }

    /**
     * Proses login web manajer.
     */
    public function login(Request $request)
    {
        $throttleKey = $this->getThrottleKey($request, 'web');

        // 1. Rate Limiting Check
        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            return back()->withInput($request->only('username', 'remember'))->withErrors([
                'username' => "Terlalu banyak percobaan login gagal. Silakan tunggu {$seconds} detik lagi.",
            ]);
        }

        // 2. Form & reCAPTCHA Validation
        $rules = [
            'username' => ['required', 'string'],
            'password' => ['required', 'string'],
        ];

        if (!empty(config('services.recaptcha.secret_key'))) {
            $rules['g-recaptcha-response'] = ['required', new Recaptcha];
        }

        $request->validate($rules, [
            'username.required' => 'Email atau Username wajib diisi.',
            'password.required' => 'Kata sandi wajib diisi.',
            'g-recaptcha-response.required' => 'Mohon selesaikan verifikasi reCAPTCHA.',
        ]);

        $loginInput = trim($request->input('username'));
        $password = $request->input('password');
        $remember = $request->boolean('remember');

        // 3. Lookup user via email or no_tlp (menggunakan query builder parameterized untuk keamanan terhadap SQL injection)
        $user = User::where('email', $loginInput)
            ->orWhere('no_tlp', $loginInput)
            ->first();

        // 4. Verifikasi Password
        if (!$user || !Hash::check($password, $user->password)) {
            RateLimiter::hit($throttleKey, 60);
            return back()->withInput($request->only('username', 'remember'))->withErrors([
                'username' => 'Email/Username atau Kata Sandi yang dimasukkan salah.',
            ]);
        }

        // 5. Verifikasi Hak Akses Role (Hanya Manajer yang boleh masuk ke portal Web Manajer)
        if ($user->role !== 'manajer') {
            RateLimiter::hit($throttleKey, 60);
            return back()->withInput($request->only('username', 'remember'))->withErrors([
                'username' => 'Akun ini bukan Manajer. Silakan masuk melalui portal Mobile Petugas.',
            ]);
        }

        // 6. Login & One-Session Enforcement (Hapus sesi perangkat lain)
        Auth::login($user, $remember);
        $request->session()->regenerate();
        $this->enforceSingleSession($user, $password, $request);

        RateLimiter::clear($throttleKey);

        return redirect()->intended(route('dashboard'));
    }

    /**
     * Tampilkan halaman login mobile web petugas.
     */
    public function showMobileLoginForm(Request $request)
    {
        $role = $request->query('role', 'distribusi');

        if (Auth::check()) {
            $userRole = Auth::user()->role;
            if ($userRole === 'petugas_distribusi' || $userRole === 'distribusi') {
                return redirect()->route('mobile.petugas.pengiriman');
            } elseif ($userRole === 'pembesaran') {
                return redirect()->route('petugas.pembesaran.dashboard');
            } elseif ($userRole === 'pembibitan') {
                return redirect()->route('petugas.pembibitan.dashboard');
            }
        }

        return view('mobile_web_petugas.login', ['role' => $role]);
    }

    /**
     * Proses login mobile web petugas.
     */
    public function mobileLogin(Request $request)
    {
        $throttleKey = $this->getThrottleKey($request, 'mobile');

        // 1. Rate Limiting Check
        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            return back()->withInput($request->only('email', 'selectedRole'))->withErrors([
                'email' => "Terlalu banyak percobaan login gagal. Silakan tunggu {$seconds} detik lagi.",
            ]);
        }

        // 2. Form & reCAPTCHA Validation
        $rules = [
            'email'        => ['required', 'string'],
            'password'     => ['required', 'string'],
            'selectedRole' => ['required', 'string', 'in:distribusi,pembesaran,pembibitan'],
        ];

        if (!empty(config('services.recaptcha.secret_key'))) {
            $rules['g-recaptcha-response'] = ['required', new Recaptcha];
        }

        $request->validate($rules, [
            'email.required' => 'Email atau Nomor Handphone wajib diisi.',
            'password.required' => 'Kata sandi wajib diisi.',
            'selectedRole.required' => 'Pilihan peran petugas wajib dipilih.',
            'g-recaptcha-response.required' => 'Mohon selesaikan verifikasi reCAPTCHA.',
        ]);

        $loginInput = trim($request->input('email'));
        $password = $request->input('password');
        $selectedRole = $request->input('selectedRole');

        // 3. Lookup user via email or no_tlp
        $user = User::where('email', $loginInput)
            ->orWhere('no_tlp', $loginInput)
            ->first();

        // 4. Verifikasi Password
        if (!$user || !Hash::check($password, $user->password)) {
            RateLimiter::hit($throttleKey, 60);
            return back()->withInput($request->only('email', 'selectedRole'))->withErrors([
                'email' => 'Email/No. HP atau Kata Sandi yang dimasukkan salah.',
            ]);
        }

        // 5. Verifikasi Kesesuaian Role yang dipilih
        $isRoleValid = false;
        if ($selectedRole === 'distribusi' && ($user->role === 'petugas_distribusi' || $user->role === 'distribusi')) {
            $isRoleValid = true;
        } elseif ($selectedRole === 'pembesaran' && $user->role === 'pembesaran') {
            $isRoleValid = true;
        } elseif ($selectedRole === 'pembibitan' && $user->role === 'pembibitan') {
            $isRoleValid = true;
        }

        if (!$isRoleValid) {
            RateLimiter::hit($throttleKey, 60);
            return back()->withInput($request->only('email', 'selectedRole'))->withErrors([
                'email' => 'Peran akun ini (' . e($user->role) . ') tidak cocok dengan tab peran yang dipilih (' . e($selectedRole) . ').',
            ]);
        }

        // 6. Login & One-Session Enforcement
        Auth::login($user, false);
        $request->session()->regenerate();
        $this->enforceSingleSession($user, $password, $request);

        RateLimiter::clear($throttleKey);

        // 7. Redirect ke dashboard operasional sesuai perannya
        if ($selectedRole === 'pembibitan') {
            return redirect()->route('petugas.pembibitan.dashboard');
        } elseif ($selectedRole === 'pembesaran') {
            return redirect()->route('petugas.pembesaran.dashboard');
        } else {
            return redirect()->route('mobile.petugas.pengiriman');
        }
    }

    /**
     * Logout web manajer.
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }

    /**
     * Logout mobile web petugas.
     */
    public function mobileLogout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('mobile.petugas.login');
    }

    /**
     * Enforce single session per user (One Session).
     */
    protected function enforceSingleSession(User $user, string $password, Request $request)
    {
        // 1. Invalidate session via Auth method jika memungkinkan (rehashes password hash in session)
        try {
            Auth::logoutOtherDevices($password);
        } catch (\Throwable $e) {
            // Silently continue jika ada exception
        }

        // 2. Hapus semua sesi lain dari tabel sessions jika menggunakan session database
        if (config('session.driver') === 'database') {
            try {
                $currentSessionId = $request->session()->getId();
                $userId = $user->getAuthIdentifier() ?? $user->id_user;
                DB::table(config('session.table', 'sessions'))
                    ->where('user_id', $userId)
                    ->where('id', '!=', $currentSessionId)
                    ->delete();
            } catch (\Throwable $e) {
                // Ignore jika session table belum dibuat
            }
        }
    }

    /**
     * Generate unique throttle key per IP + Input.
     */
    protected function getThrottleKey(Request $request, string $prefix): string
    {
        $input = $request->input('username') ?? $request->input('email') ?? '';
        return Str::transliterate($prefix . '|' . Str::lower($input) . '|' . $request->ip());
    }
}

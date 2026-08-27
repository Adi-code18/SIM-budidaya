<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\Google2FA;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    /**
     * Tampilkan halaman login web manajer.
     */
    public function showLoginForm(Request $request)
    {
        if ($request->has('switch')) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            return redirect()->route('login');
        }

        if (Auth::check()) {
            if (Auth::user()->role === 'manajer') {
                return redirect()->route('dashboard');
            }
            $userRole = Auth::user()->role;
            if ($userRole === 'petugas_distribusi' || $userRole === 'distribusi') {
                return redirect()->route('mobile.petugas.pengiriman');
            } elseif ($userRole === 'pembesaran') {
                return redirect()->route('petugas.pembesaran.dashboard');
            } elseif ($userRole === 'pembibitan') {
                return redirect()->route('petugas.pembibitan.dashboard');
            }
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
            $seconds = max(1, RateLimiter::availableIn($throttleKey));
            return back()->withInput($request->only('username', 'remember'))->withErrors([
                'username' => "Terlalu banyak percobaan login gagal. Silakan tunggu {$seconds} detik lagi.",
            ]);
        }

        // 2. Form Validation
        $request->validate([
            'username' => ['required', 'string'],
            'password' => ['required', 'string'],
        ], [
            'username.required' => 'Email atau Username wajib diisi.',
            'password.required' => 'Kata sandi wajib diisi.',
        ]);

        $loginInput = trim($request->input('username'));
        $password = $request->input('password');
        $remember = $request->boolean('remember');

        // 3. Lookup user via email or no_tlp
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

        // Reset session jika ada akun lain yang sedang terhubung di browser ini
        if (Auth::check() && Auth::id() !== ($user->id_user ?? $user->id)) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
        }

        // 6. Mandatory 2FA Enforcement (Wajib 2FA untuk SEMUA user)
        session([
            '2fa:user_id'  => $user->id_user ?? $user->id,
            '2fa:remember' => $remember,
            '2fa:role'     => 'manajer'
        ]);
        RateLimiter::clear($throttleKey);

        if (!$user->two_factor_confirmed_at) {
            if (!$user->two_factor_secret) {
                $google2fa = new Google2FA();
                $user->update([
                    'two_factor_secret' => encrypt($google2fa->generateSecretKey()),
                ]);
            }
            return redirect()->route('2fa.setup');
        }

        return redirect()->route('2fa.login');
    }

    /**
     * Tampilkan halaman login mobile web petugas.
     */
    public function showMobileLoginForm(Request $request)
    {
        if ($request->has('switch')) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            return redirect()->route('mobile.petugas.login');
        }

        $role = $request->query('role', 'distribusi');

        if (Auth::check()) {
            $userRole = Auth::user()->role;
            if ($userRole === 'petugas_distribusi' || $userRole === 'distribusi') {
                return redirect()->route('mobile.petugas.pengiriman');
            } elseif ($userRole === 'pembesaran') {
                return redirect()->route('petugas.pembesaran.dashboard');
            } elseif ($userRole === 'pembibitan') {
                return redirect()->route('petugas.pembibitan.dashboard');
            } elseif ($userRole === 'manajer') {
                return redirect()->route('dashboard');
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
            $seconds = max(1, RateLimiter::availableIn($throttleKey));
            return back()->withInput($request->only('email', 'selectedRole'))->withErrors([
                'email' => "Terlalu banyak percobaan login gagal. Silakan tunggu {$seconds} detik lagi.",
            ]);
        }

        // 2. Form Validation
        $request->validate([
            'email'        => ['required', 'string'],
            'password'     => ['required', 'string'],
            'selectedRole' => ['required', 'string', 'in:distribusi,pembesaran,pembibitan'],
        ], [
            'email.required' => 'Email atau Nomor Handphone wajib diisi.',
            'password.required' => 'Kata sandi wajib diisi.',
            'selectedRole.required' => 'Pilihan peran petugas wajib dipilih.',
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

        // Reset session jika ada akun lain yang sedang terhubung di browser ini
        if (Auth::check() && Auth::id() !== ($user->id_user ?? $user->id)) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
        }

        // 6. Mandatory 2FA Enforcement (Wajib 2FA untuk SEMUA user)
        session([
            '2fa:user_id'      => $user->id_user ?? $user->id,
            '2fa:remember'     => false,
            '2fa:selectedRole' => $selectedRole
        ]);
        RateLimiter::clear($throttleKey);

        if (!$user->two_factor_confirmed_at) {
            if (!$user->two_factor_secret) {
                $google2fa = new Google2FA();
                $user->update([
                    'two_factor_secret' => encrypt($google2fa->generateSecretKey()),
                ]);
            }
            return redirect()->route('2fa.setup');
        }

        return redirect()->route('2fa.login');
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
    public function enforceSingleSession(User $user, string $password, Request $request)
    {
        try {
            Auth::logoutOtherDevices($password);
        } catch (\Throwable $e) {
            // Silently continue
        }

        if (config('session.driver') === 'database') {
            try {
                $currentSessionId = $request->session()->getId();
                $userId = $user->getAuthIdentifier() ?? $user->id_user;
                DB::table(config('session.table', 'sessions'))
                    ->where('user_id', $userId)
                    ->where('id', '!=', $currentSessionId)
                    ->delete();
            } catch (\Throwable $e) {
                // Ignore jika tabel belum ada
            }
        }
    }

    /**
     * Generate unique throttle key per IP + Input.
     */
    protected function getThrottleKey(Request $request, string $prefix): string
    {
        $input = trim($request->input('username') ?? $request->input('email') ?? '');
        return Str::transliterate($prefix . '|' . Str::lower($input) . '|' . $request->ip());
    }

    /**
     * Verifikasi token Cloudflare Turnstile dari client.
     */
    protected function verifyTurnstile(Request $request): bool
    {
        $turnstileResponse = $request->input('cf-turnstile-response');
        $secretKey = config('services.turnstile.secret_key');

        if (!$turnstileResponse || !$secretKey) {
            return false;
        }

        try {
            $response = \Illuminate\Support\Facades\Http::asForm()->post('https://challenges.cloudflare.com/turnstile/v0/siteverify', [
                'secret'   => $secretKey,
                'response' => $turnstileResponse,
                'remoteip' => $request->ip(),
            ]);

            return $response->json('success') === true;
        } catch (\Throwable $e) {
            return true;
        }
    }
}

<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\Google2FA;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class TwoFactorController extends Controller
{
    /**
     * Tampilkan setup QR code untuk 2FA wajib.
     */
    public function showSetup(Request $request)
    {
        $user = $request->user();

        if (!$user && session()->has('2fa:user_id')) {
            $userId = session('2fa:user_id');
            $user = User::find($userId);
        }

        if (!$user) {
            return redirect()->route('login');
        }

        $google2fa = new Google2FA();

        // Jika belum punya secret key, buatkan baru
        if (!$user->two_factor_secret) {
            $user->update([
                'two_factor_secret' => encrypt($google2fa->generateSecretKey()),
            ]);
        }

        $secretKey = $this->getSecretKey($user);
        $qrCodeSvg = $user->two_factor_qr_code_svg;

        return view('auth.two_factor_setup', compact('user', 'secretKey', 'qrCodeSvg'));
    }

    /**
     * Helper untuk mendapatkan secret key yang terdekripsi secara aman.
     */
    protected function getSecretKey(User $user): string
    {
        if (!$user->two_factor_secret) {
            $google2fa = new Google2FA();
            $secret = $google2fa->generateSecretKey();
            $user->update(['two_factor_secret' => encrypt($secret)]);
            return $secret;
        }

        try {
            return decrypt($user->two_factor_secret);
        } catch (\Throwable $e) {
            $google2fa = new Google2FA();
            $secret = $google2fa->generateSecretKey();
            $user->update(['two_factor_secret' => encrypt($secret)]);
            return $secret;
        }
    }

    /**
     * Konfirmasi bahwa user sukses scan dan memasukkan kode 6 digit pertama.
     */
    public function confirm(Request $request)
    {
        $request->validate(['code' => 'required|digits:6'], [
            'code.required' => 'Kode OTP wajib diisi.',
            'code.digits' => 'Kode OTP harus berupa 6 angka.'
        ]);

        $user = $request->user();
        if (!$user && session()->has('2fa:user_id')) {
            $userId = session('2fa:user_id');
            $user = User::find($userId);
        }

        if (!$user) {
            return redirect()->route('login');
        }

        $google2fa = new Google2FA();
        $secret = $this->getSecretKey($user);

        $valid = $google2fa->verifyKey($secret, $request->code);

        if (!$valid) {
            return back()->withErrors(['code' => 'Kode OTP tidak sesuai. Coba lagi.']);
        }

        // Tandai 2FA sudah aktif
        $user->update(['two_factor_confirmed_at' => now()]);

        // Karena 2FA telah berhasil diaktifkan dan diverifikasi, lakukan Auth::login jika datang dari alur login
        if (!Auth::check()) {
            $remember = session()->pull('2fa:remember', false);
            $selectedRole = session()->pull('2fa:selectedRole', null);
            session()->forget(['2fa:user_id', '2fa:role']);

            Auth::login($user, $remember);
            $request->session()->regenerate();

            $currentSessionId = session()->getId();
            $user->update(['last_session_id' => $currentSessionId]);
            session(['user_session_id' => $currentSessionId]);

            if ($selectedRole) {
                if ($selectedRole === 'pembibitan') {
                    return redirect()->route('petugas.pembibitan.dashboard')->with('status', '2FA Berhasil diaktifkan!');
                } elseif ($selectedRole === 'pembesaran') {
                    return redirect()->route('petugas.pembesaran.dashboard')->with('status', '2FA Berhasil diaktifkan!');
                } else {
                    return redirect()->route('mobile.petugas.pengiriman')->with('status', '2FA Berhasil diaktifkan!');
                }
            }

            return redirect()->route('dashboard')->with('status', 'Autentikasi dua langkah (2FA) berhasil diaktifkan!');
        }

        return redirect()->route('dashboard')->with('status', 'Autentikasi dua langkah (2FA) berhasil diaktifkan!');
    }

    /**
     * Nonaktifkan 2FA untuk akun user yang sedang login.
     */
    public function disable(Request $request)
    {
        $user = $request->user();

        $request->validate([
            'password' => ['required', 'string'],
        ], [
            'password.required' => 'Kata sandi wajib diisi untuk menonaktifkan 2FA.'
        ]);

        if (!Hash::check($request->password, $user->password)) {
            return back()->withErrors(['password' => 'Kata sandi yang dimasukkan tidak sesuai.']);
        }

        $user->update([
            'two_factor_secret' => null,
            'two_factor_confirmed_at' => null,
        ]);

        return back()->with('status', '2FA berhasil dinonaktifkan.');
    }

    /**
     * Tampilkan form tantangan OTP saat login.
     */
    public function show2faForm()
    {
        if (!session()->has('2fa:user_id')) {
            return redirect()->route('login');
        }

        $userId = session('2fa:user_id');
        $user = User::find($userId);

        if (!$user) {
            return redirect()->route('login');
        }

        $secretKey = $this->getSecretKey($user);
        $qrCodeSvg = $user->two_factor_qr_code_svg;

        return view('auth.two_factor_login', compact('user', 'secretKey', 'qrCodeSvg'));
    }

    /**
     * Verifikasi OTP saat login.
     */
    public function verify2fa(Request $request)
    {
        if (!session()->has('2fa:user_id')) {
            return redirect()->route('login');
        }

        $request->validate(['code' => 'required|digits:6'], [
            'code.required' => 'Kode OTP wajib diisi.',
            'code.digits' => 'Kode OTP harus berupa 6 angka.'
        ]);

        $userId = session('2fa:user_id');
        $user = User::find($userId);

        if (!$user || !$user->two_factor_secret) {
            session()->forget(['2fa:user_id', '2fa:remember', '2fa:role', '2fa:selectedRole']);
            return redirect()->route('login')->withErrors(['username' => 'Sesi 2FA tidak valid. Silakan login kembali.']);
        }

        $google2fa = new Google2FA();
        $secret = $this->getSecretKey($user);
        $valid = $google2fa->verifyKey($secret, $request->code);

        if (!$valid) {
            return back()->withErrors(['code' => 'Kode OTP yang dimasukkan tidak sesuai. Silakan coba lagi.']);
        }

        // OTP Valid - Lakukan Auth::login
        $remember = session()->pull('2fa:remember', false);
        $selectedRole = session()->pull('2fa:selectedRole', null);
        session()->forget(['2fa:user_id', '2fa:role']);

        Auth::login($user, $remember);
        $request->session()->regenerate();

        $currentSessionId = session()->getId();
        $user->update(['last_session_id' => $currentSessionId]);
        session(['user_session_id' => $currentSessionId]);

        if ($selectedRole) {
            if ($selectedRole === 'pembibitan') {
                return redirect()->route('petugas.pembibitan.dashboard');
            } elseif ($selectedRole === 'pembesaran') {
                return redirect()->route('petugas.pembesaran.dashboard');
            } else {
                return redirect()->route('mobile.petugas.pengiriman');
            }
        }

        return redirect()->intended(route('dashboard'));
    }

    /**
     * Batalkan alur 2FA dan kembali ke login dengan membersihkan sesi.
     */
    public function cancel(Request $request)
    {
        $selectedRole = session('2fa:selectedRole');
        $role = session('2fa:role');

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        if ($selectedRole || $role === 'petugas') {
            return redirect()->route('mobile.petugas.login');
        }

        return redirect()->route('login');
    }
}

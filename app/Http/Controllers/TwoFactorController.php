<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use PragmaRX\Google2FA\Google2FA;

class TwoFactorController extends Controller
{
    // Tampilkan setup QR code untuk 2FA wajib
    public function showSetup(Request $request)
    {
        $user = $request->user();

        if (!$user && session()->has('2fa:user_id')) {
            $userId = session('2fa:user_id');
            $user = User::where('id_user', $userId)->orWhere('id', $userId)->first();
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

        return view('auth.two_factor_setup', [
            'qrSvg' => $user->two_factor_qr_code_svg,
            'secretKey' => decrypt($user->two_factor_secret),
        ]);
    }

    // Konfirmasi bahwa user sukses scan dan memasukkan kode 6 digit pertama
    public function confirm(Request $request)
    {
        $request->validate(['code' => 'required|digits:6'], [
            'code.required' => 'Kode OTP wajib diisi.',
            'code.digits' => 'Kode OTP harus berupa 6 angka.'
        ]);

        $user = $request->user();
        if (!$user && session()->has('2fa:user_id')) {
            $userId = session('2fa:user_id');
            $user = User::where('id_user', $userId)->orWhere('id', $userId)->first();
        }

        if (!$user) {
            return redirect()->route('login');
        }

        $google2fa = new Google2FA();
        $secret = decrypt($user->two_factor_secret);

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

    // Nonaktifkan 2FA untuk akun user yang sedang login
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

    // Tampilkan form tantangan OTP saat login
    public function show2faForm()
    {
        if (!session()->has('2fa:user_id')) {
            return redirect()->route('login');
        }

        return view('auth.two_factor_login');
    }

    // Verifikasi OTP saat login
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
        $user = User::where('id_user', $userId)->orWhere('id', $userId)->first();

        if (!$user || !$user->two_factor_secret) {
            session()->forget(['2fa:user_id', '2fa:remember', '2fa:role', '2fa:selectedRole']);
            return redirect()->route('login')->withErrors(['username' => 'Sesi 2FA tidak valid. Silakan login kembali.']);
        }

        $google2fa = new Google2FA();
        $secret = decrypt($user->two_factor_secret);
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
}

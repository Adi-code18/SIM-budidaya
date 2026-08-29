<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\SendOtpMail;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class EmailOtpController extends Controller
{
    /**
     * Tampilkan halaman verifikasi OTP Email untuk Manajer.
     */
    public function showForm(Request $request)
    {
        if (!session()->has('email_otp:user_id')) {
            return redirect()->route('login');
        }

        $userId = session('email_otp:user_id');
        $user = User::find($userId);

        if (!$user || $user->role !== 'manajer') {
            session()->forget(['email_otp:user_id', 'email_otp:code', 'email_otp:expires_at', 'email_otp:remember', 'email_otp:last_sent_at']);
            return redirect()->route('login');
        }

        $maskedEmail = $this->maskEmail($user->email ?? '');
        $expiresAt = session('email_otp:expires_at', now()->addMinutes(5)->timestamp);
        $remainingSeconds = max(0, $expiresAt - now()->timestamp);
        
        $lastSentAt = session('email_otp:last_sent_at', 0);
        $resendCooldown = max(0, 60 - (now()->timestamp - $lastSentAt));

        return view('auth.email_otp', compact('user', 'maskedEmail', 'remainingSeconds', 'resendCooldown'));
    }

    /**
     * Verifikasi kode OTP Email yang dimasukkan oleh Manajer.
     */
    public function verify(Request $request)
    {
        if (!session()->has('email_otp:user_id')) {
            return redirect()->route('login');
        }

        $request->validate([
            'code' => ['required', 'digits:6'],
        ], [
            'code.required' => 'Kode OTP wajib diisi.',
            'code.digits'   => 'Kode OTP harus berupa 6 digit angka.',
        ]);

        $userId = session('email_otp:user_id');
        $user = User::find($userId);

        if (!$user) {
            session()->forget(['email_otp:user_id', 'email_otp:code', 'email_otp:expires_at', 'email_otp:remember']);
            return redirect()->route('login')->withErrors(['username' => 'Sesi autentikasi telah habis. Silakan login kembali.']);
        }

        // Cek kedaluwarsa
        $expiresAt = session('email_otp:expires_at', 0);
        if (now()->timestamp > $expiresAt) {
            return back()->withErrors(['code' => 'Kode OTP telah kedaluwarsa. Silakan klik tombol "Kirim Ulang Kode".']);
        }

        // Cek percobaan gagal
        $attempts = session('email_otp:attempts', 0);
        if ($attempts >= 5) {
            session()->forget(['email_otp:user_id', 'email_otp:code', 'email_otp:expires_at', 'email_otp:remember']);
            return redirect()->route('login')->withErrors(['username' => 'Terlalu banyak percobaan kode OTP salah. Silakan login kembali.']);
        }

        $sessionOtp = (string) session('email_otp:code');
        $inputOtp = trim($request->input('code'));

        if (!hash_equals($sessionOtp, $inputOtp)) {
            session(['email_otp:attempts' => $attempts + 1]);
            $remaining = 5 - ($attempts + 1);
            return back()->withErrors(['code' => "Kode OTP yang Anda masukkan salah. Sisa percobaan: {$remaining} kali."]);
        }

        // OTP Valid - Login user
        $remember = session('email_otp:remember', false);
        session()->forget([
            'email_otp:user_id',
            'email_otp:code',
            'email_otp:expires_at',
            'email_otp:remember',
            'email_otp:attempts',
            'email_otp:last_sent_at'
        ]);

        Auth::login($user, $remember);
        $request->session()->regenerate();

        $currentSessionId = session()->getId();
        $user->update(['last_session_id' => $currentSessionId]);
        session(['user_session_id' => $currentSessionId]);

        return redirect()->intended(route('dashboard'))->with('status', 'Login berhasil! Selamat datang kembali di SIM-BUDIDAYA.');
    }

    /**
     * Kirim ulang kode OTP Email ke Manajer.
     */
    public function resend(Request $request)
    {
        if (!session()->has('email_otp:user_id')) {
            return redirect()->route('login');
        }

        $userId = session('email_otp:user_id');
        $user = User::find($userId);

        if (!$user || empty($user->email)) {
            return redirect()->route('login')->withErrors(['username' => 'Email pengguna tidak valid.']);
        }

        // Cooldown cek (minimal 60 detik)
        $lastSentAt = session('email_otp:last_sent_at', 0);
        if (now()->timestamp - $lastSentAt < 60) {
            $wait = 60 - (now()->timestamp - $lastSentAt);
            return back()->withErrors(['code' => "Mohon tunggu {$wait} detik sebelum meminta kirim ulang kode OTP."]);
        }

        $otpCode = sprintf("%06d", mt_rand(1, 999999));
        $expiresAt = now()->addMinutes(5)->timestamp;

        session([
            'email_otp:code'         => $otpCode,
            'email_otp:expires_at'   => $expiresAt,
            'email_otp:last_sent_at' => now()->timestamp,
            'email_otp:attempts'     => 0,
        ]);

        try {
            Mail::to($user->email)->send(new SendOtpMail($otpCode, $user->nama ?? 'Manajer', 5));
        } catch (\Throwable $e) {
            Log::error('Gagal mengirim Email OTP ke Manajer: ' . $e->getMessage());
            return back()->withErrors(['code' => 'Gagal mengirim email OTP: ' . $e->getMessage()]);
        }

        return back()->with('status', 'Kode OTP baru telah berhasil dikirim ke email ' . $this->maskEmail($user->email) . '.');
    }

    /**
     * Batalkan alur OTP Email dan kembali ke halaman login.
     */
    public function cancel(Request $request)
    {
        session()->forget([
            'email_otp:user_id',
            'email_otp:code',
            'email_otp:expires_at',
            'email_otp:remember',
            'email_otp:attempts',
            'email_otp:last_sent_at'
        ]);

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }

    /**
     * Helper menyamarkan email untuk privasi (e.g. ad8101058@gmail.com -> ad***58@gmail.com).
     */
    protected function maskEmail(string $email): string
    {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return $email;
        }

        $parts = explode('@', $email);
        $name = $parts[0];
        $domain = $parts[1] ?? '';

        $len = strlen($name);
        if ($len <= 2) {
            $maskedName = substr($name, 0, 1) . '*';
        } elseif ($len <= 4) {
            $maskedName = substr($name, 0, 1) . str_repeat('*', $len - 2) . substr($name, -1);
        } else {
            $maskedName = substr($name, 0, 2) . str_repeat('*', max(3, $len - 4)) . substr($name, -2);
        }

        return $maskedName . '@' . $domain;
    }
}

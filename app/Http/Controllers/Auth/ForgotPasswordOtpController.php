<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\SendResetPasswordOtpMail;
use App\Models\User;
use App\Services\EmailSecurityService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;

class ForgotPasswordOtpController extends Controller
{
    /**
     * Tampilkan form input email lupa password (Web).
     */
    public function showRequestForm()
    {
        return view('auth.forgot_password');
    }

    /**
     * Proses kirim kode OTP Lupa Password (Web).
     */
    public function sendResetOtp(Request $request)
    {
        // 1. Validasi Format Input (Menerima Email atau No. Handphone)
        $request->validate([
            'email' => ['required', 'string', 'max:255'],
        ], [
            'email.required' => 'Email atau Nomor Handphone terdaftar wajib diisi.',
        ]);

        $input = trim($request->input('email'));
        $ip = $request->ip();

        // 2. Rate Limiting Ketat
        $cooldownKey = "forgot-otp-cooldown:" . md5($input) . ":{$ip}";
        $hourlyKey = "forgot-otp-hourly:" . md5($input) . ":{$ip}";

        // Cooldown: Maksimal 1 kirim per 2 menit (120 detik)
        if (RateLimiter::tooManyAttempts($cooldownKey, 1)) {
            $seconds = RateLimiter::availableIn($cooldownKey);
            return back()->withInput()->withErrors([
                'email' => "Silakan tunggu {$seconds} detik lagi sebelum meminta kode OTP baru.",
            ]);
        }

        // Frekuensi: Maksimal 3 kali kirim per jam (3600 detik)
        if (RateLimiter::tooManyAttempts($hourlyKey, 3)) {
            $minutes = ceil(RateLimiter::availableIn($hourlyKey) / 60);
            return back()->withInput()->withErrors([
                'email' => "Batas permintaan OTP tercapai. Silakan coba kembali dalam {$minutes} menit.",
            ]);
        }

        // 2.1 Validasi Cloudflare Turnstile CAPTCHA (Hit rate limit jika gagal/tidak dicentang)
        if (!$this->verifyTurnstile($request)) {
            RateLimiter::hit($cooldownKey, 120);
            RateLimiter::hit($hourlyKey, 3600);
            return back()->withInput()->withErrors([
                'turnstile' => 'Verifikasi keamanan CAPTCHA wajib dicentang dan diselesaikan.',
            ]);
        }

        // 2.2 Validasi Domain Typo & Format Email (Jika memasukkan Email)
        $emailCheck = EmailSecurityService::checkEmail($input);
        if ($emailCheck['is_email'] && !$emailCheck['is_valid']) {
            return back()->withInput()->withErrors([
                'email' => $emailCheck['error'],
            ]);
        }

        RateLimiter::hit($cooldownKey, 120);
        RateLimiter::hit($hourlyKey, 3600);

        // 4. Proses Pencarian User di Database (Semua Role)
        $cleanPhone = preg_replace('/[^0-9]/', '', $input);
        if (str_starts_with($cleanPhone, '62')) {
            $cleanPhone = '0' . substr($cleanPhone, 2);
        }

        $user = User::where('email', strtolower($input))
            ->orWhere('no_tlp', $input)
            ->when($cleanPhone, function ($q) use ($cleanPhone) {
                return $q->orWhere('no_tlp', $cleanPhone)
                         ->orWhere('no_tlp', '+62' . ltrim($cleanPhone, '0'));
            })
            ->orWhere('nama', $input)
            ->first();

        if ($user && !empty($user->email)) {
            // Generate 6 digit angka acak menggunakan CSPRNG
            $otpCode = (string) random_int(100000, 999999);
            $expiresAt = now()->addMinutes(10)->timestamp;

            session([
                'pwd_reset:user_id'     => $user->id_user,
                'pwd_reset:email'       => $user->email,
                'pwd_reset:role'        => $user->role,
                'pwd_reset:code'        => $otpCode,
                'pwd_reset:expires_at'  => $expiresAt,
                'pwd_reset:attempts'    => 0,
                'pwd_reset:last_sent_at'=> now()->timestamp,
                'pwd_reset:verified'    => false,
            ]);

            try {
                Mail::to($user->email)->send(new SendResetPasswordOtpMail($otpCode, $user->nama, 10));
            } catch (\Exception $e) {
                Log::error("Gagal mengirim email OTP lupa password ke {$user->email}: " . $e->getMessage());
            }
        } else {
            // Email/No HP tidak terdaftar: Simpan session dummy agar alur UX konsisten
            session([
                'pwd_reset:email'       => filter_var($input, FILTER_VALIDATE_EMAIL) ? $input : 'email-pengguna@example.com',
                'pwd_reset:expires_at'  => now()->addMinutes(10)->timestamp,
                'pwd_reset:last_sent_at'=> now()->timestamp,
                'pwd_reset:verified'    => false,
            ]);
        }

        // 5. Constant-Time / Respon Sukses Seragam
        return redirect()->route('forgot.password.verify')->with('status', 'Jika akun Anda terdaftar di sistem, kode OTP 6-digit telah dikirimkan ke kotak masuk email terdaftar Anda.');
    }

    /**
     * Tampilkan halaman verifikasi OTP 6 digit.
     */
    public function showVerifyForm()
    {
        if (!session()->has('pwd_reset:email')) {
            return redirect()->route('forgot.password');
        }

        $email = session('pwd_reset:email');
        $maskedEmail = $this->maskEmail($email);
        $expiresAt = session('pwd_reset:expires_at', now()->addMinutes(10)->timestamp);
        $remainingSeconds = max(0, $expiresAt - now()->timestamp);

        $lastSentAt = session('pwd_reset:last_sent_at', 0);
        $resendCooldown = max(0, 120 - (now()->timestamp - $lastSentAt));

        return view('auth.verify_reset_otp', compact('email', 'maskedEmail', 'remainingSeconds', 'resendCooldown'));
    }

    /**
     * Verifikasi kode OTP 6 digit yang dimasukkan.
     */
    public function verifyResetOtp(Request $request)
    {
        if (!session()->has('pwd_reset:email')) {
            return redirect()->route('forgot.password');
        }

        $request->validate([
            'code' => ['required', 'digits:6'],
        ], [
            'code.required' => 'Kode OTP wajib diisi.',
            'code.digits'   => 'Kode OTP harus berupa 6 digit angka.',
        ]);

        $expiresAt = session('pwd_reset:expires_at', 0);
        if (now()->timestamp > $expiresAt) {
            return back()->withErrors(['code' => 'Kode OTP telah kedaluwarsa. Silakan minta kode OTP baru.']);
        }

        $attempts = session('pwd_reset:attempts', 0);
        if ($attempts >= 5) {
            session()->forget([
                'pwd_reset:user_id', 'pwd_reset:email', 'pwd_reset:code', 
                'pwd_reset:expires_at', 'pwd_reset:attempts', 'pwd_reset:verified'
            ]);
            return redirect()->route('forgot.password')->withErrors([
                'email' => 'Terlalu banyak percobaan kode OTP salah. Sesi reset telah dibatalkan demi keamanan.',
            ]);
        }

        $sessionOtp = (string) session('pwd_reset:code');
        $inputOtp = trim($request->input('code'));

        if (empty($sessionOtp) || !hash_equals($sessionOtp, $inputOtp)) {
            session(['pwd_reset:attempts' => $attempts + 1]);
            $remaining = 5 - ($attempts + 1);
            return back()->withErrors(['code' => "Kode OTP salah. Sisa percobaan: {$remaining} kali."]);
        }

        // OTP Valid
        $resetToken = Str::random(40);
        session([
            'pwd_reset:verified' => true,
            'pwd_reset:token'    => $resetToken,
        ]);

        return redirect()->route('forgot.password.reset.form', ['token' => $resetToken]);
    }

    /**
     * Tampilkan form pembuatan password baru.
     */
    public function showResetPasswordForm(Request $request)
    {
        $token = $request->query('token');
        if (!session('pwd_reset:verified') || session('pwd_reset:token') !== $token) {
            return redirect()->route('forgot.password')->withErrors([
                'email' => 'Sesi verifikasi reset password tidak valid atau telah kedaluwarsa.',
            ]);
        }

        return view('auth.reset_password', ['token' => $token]);
    }

    /**
     * Simpan password baru ke database.
     */
    public function resetPassword(Request $request)
    {
        $token = $request->input('token');
        if (!session('pwd_reset:verified') || session('pwd_reset:token') !== $token) {
            return redirect()->route('forgot.password')->withErrors([
                'email' => 'Sesi reset password tidak valid.',
            ]);
        }

        $request->validate([
            'password' => ['required', 'confirmed', Password::min(6)],
        ], [
            'password.required'  => 'Password baru wajib diisi.',
            'password.min'       => 'Password baru minimal 6 karakter.',
            'password.confirmed' => 'Konfirmasi password baru tidak cocok.',
        ]);

        $userId = session('pwd_reset:user_id');
        $user = User::find($userId);

        if ($user) {
            $user->password = Hash::make($request->input('password'));
            $user->save();
        }

        // Bersihkan seluruh session reset password
        session()->forget([
            'pwd_reset:user_id', 'pwd_reset:email', 'pwd_reset:code', 
            'pwd_reset:expires_at', 'pwd_reset:attempts', 'pwd_reset:verified',
            'pwd_reset:token', 'pwd_reset:last_sent_at'
        ]);

        // Redirect ke login sesuai role atau portal utama
        if ($user && in_array($user->role, ['pembibitan', 'pembesaran', 'petugas_distribusi', 'distribusi'])) {
            $targetRole = ($user->role === 'petugas_distribusi') ? 'distribusi' : $user->role;
            return redirect()->route('mobile.petugas.login', ['role' => $targetRole])->with('status', 'Kata sandi berhasil diperbarui! Silakan masuk dengan kata sandi baru Anda.');
        }

        return redirect()->route('login')->with('status', 'Kata sandi akun berhasil diperbarui! Silakan masuk dengan kata sandi baru Anda.');
    }

    /**
     * =========================================================================
     * API ENDPOINTS UNTUK APLIKASI MOBILE / CLIENT LUAR
     * =========================================================================
     */

    public function apiSendOtp(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email'],
        ]);

        $email = strtolower(trim($request->input('email')));
        $ip = $request->ip();

        $emailCheck = EmailSecurityService::checkEmail($email);
        if ($emailCheck['is_email'] && !$emailCheck['is_valid']) {
            return response()->json([
                'success'    => false,
                'message'    => $emailCheck['error'],
                'suggestion' => $emailCheck['suggestion'],
            ], 422);
        }

        $cooldownKey = "forgot-otp-cooldown:{$email}:{$ip}";
        $hourlyKey = "forgot-otp-hourly:{$email}:{$ip}";

        if (RateLimiter::tooManyAttempts($cooldownKey, 1)) {
            $seconds = RateLimiter::availableIn($cooldownKey);
            return response()->json([
                'success' => false,
                'message' => "Silakan tunggu {$seconds} detik lagi sebelum meminta OTP baru.",
            ], 429);
        }

        if (RateLimiter::tooManyAttempts($hourlyKey, 3)) {
            $minutes = ceil(RateLimiter::availableIn($hourlyKey) / 60);
            return response()->json([
                'success' => false,
                'message' => "Batas permintaan OTP tercapai. Coba lagi dalam {$minutes} menit.",
            ], 429);
        }

        RateLimiter::hit($cooldownKey, 120);
        RateLimiter::hit($hourlyKey, 3600);

        $user = User::where('email', $email)->first();
        if ($user) {
            $otpCode = (string) random_int(100000, 999999);
            session([
                'pwd_reset:user_id'    => $user->id_user,
                'pwd_reset:email'      => $user->email,
                'pwd_reset:code'       => $otpCode,
                'pwd_reset:expires_at' => now()->addMinutes(10)->timestamp,
                'pwd_reset:attempts'   => 0,
            ]);

            try {
                Mail::to($user->email)->send(new SendResetPasswordOtpMail($otpCode, $user->nama, 10));
            } catch (\Exception $e) {
                Log::error("API OTP Lupa Password error: " . $e->getMessage());
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Jika email Anda terdaftar, kode OTP telah dikirimkan.',
        ]);
    }

    public function apiVerifyOtp(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email'],
            'code'  => ['required', 'digits:6'],
        ]);

        $sessionOtp = (string) session('pwd_reset:code');
        $inputOtp = trim($request->input('code'));

        if (empty($sessionOtp) || !hash_equals($sessionOtp, $inputOtp)) {
            return response()->json([
                'success' => false,
                'message' => 'Kode OTP tidak valid atau salah.',
            ], 422);
        }

        $resetToken = Str::random(40);
        session([
            'pwd_reset:verified' => true,
            'pwd_reset:token'    => $resetToken,
        ]);

        return response()->json([
            'success'     => true,
            'message'     => 'Kode OTP valid.',
            'reset_token' => $resetToken,
        ]);
    }

    public function apiResetPassword(Request $request)
    {
        $request->validate([
            'token'    => ['required', 'string'],
            'password' => ['required', 'confirmed', Password::min(6)],
        ]);

        if (!session('pwd_reset:verified') || session('pwd_reset:token') !== $request->input('token')) {
            return response()->json([
                'success' => false,
                'message' => 'Sesi reset password tidak valid.',
            ], 403);
        }

        $userId = session('pwd_reset:user_id');
        $user = User::find($userId);

        if ($user) {
            $user->password = Hash::make($request->input('password'));
            $user->save();
        }

        session()->forget([
            'pwd_reset:user_id', 'pwd_reset:email', 'pwd_reset:code', 
            'pwd_reset:expires_at', 'pwd_reset:attempts', 'pwd_reset:verified', 'pwd_reset:token'
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Kata sandi akun berhasil diperbarui.',
        ]);
    }

    /**
     * Mask email address for privacy (e.g. ad***@gmail.com)
     */
    protected function maskEmail(string $email): string
    {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return 'em***@***.***';
        }

        $parts = explode('@', $email);
        $name = $parts[0];
        $domain = $parts[1] ?? '';

        $len = strlen($name);
        if ($len <= 2) {
            $maskedName = substr($name, 0, 1) . '***';
        } else {
            $maskedName = substr($name, 0, 2) . str_repeat('*', min(4, $len - 2));
        }

        return $maskedName . '@' . $domain;
    }

    /**
     * Verifikasi Cloudflare Turnstile CAPTCHA.
     */
    protected function verifyTurnstile(Request $request): bool
    {
        $turnstileResponse = $request->input('cf-turnstile-response');
        $secretKey = config('services.turnstile.secret_key');

        // Wajib dicentang dan memiliki respon token dari Cloudflare
        if (empty($turnstileResponse) || empty($secretKey)) {
            return false;
        }

        try {
            $response = Http::asForm()->timeout(5)->post('https://challenges.cloudflare.com/turnstile/v0/siteverify', [
                'secret'   => $secretKey,
                'response' => $turnstileResponse,
                'remoteip' => $request->ip(),
            ]);

            $data = $response->json();
            return isset($data['success']) && $data['success'] === true;
        } catch (\Throwable $e) {
            Log::error('Turnstile verification error: ' . $e->getMessage());
            return false;
        }
    }
}

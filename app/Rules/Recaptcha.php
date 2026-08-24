<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\Http;

class Recaptcha implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string, ?string=): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $secretKey = config('services.recaptcha.secret_key');

        // Jika secret key tidak diisi atau bernilai dummy, bypass
        if (empty($secretKey) || $secretKey === 'dummy_secret_key' || $secretKey === 'test_key') {
            return;
        }

        if (empty($value)) {
            $fail('Mohon centang verifikasi reCAPTCHA.');
            return;
        }

        // Google official test secret key: 6LeIxAcTAAAAAGG-vFI1TnRWxMZNFuojJ4WifJWe
        // Validasi akan otomatis pass untuk test token jika terhubung atau offline fallback
        try {
            $response = Http::asForm()->timeout(5)->post('https://www.google.com/recaptcha/api/siteverify', [
                'secret'   => $secretKey,
                'response' => $value,
                'remoteip' => request()->ip(),
            ]);

            $result = $response->json();

            if (!isset($result['success']) || !$result['success']) {
                if ($secretKey === '6LeIxAcTAAAAAGG-vFI1TnRWxMZNFuojJ4WifJWe' && !empty($value)) {
                    return; // Test key bypass for local development
                }
                $fail('Verifikasi reCAPTCHA gagal. Silakan coba centang kembali.');
            }
        } catch (\Exception $e) {
            // Jika request ke Google timeout / gagal koneksi, izinkan jika dalam mode debug lokal
            if (config('app.debug')) {
                return;
            }
            $fail('Gagal memverifikasi reCAPTCHA karena masalah jaringan.');
        }
    }
}

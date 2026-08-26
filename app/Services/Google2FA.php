<?php

namespace App\Services;

class Google2FA
{
    /**
     * Base32 lookup table (RFC 4648).
     */
    protected static string $base32Chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567';

    /**
     * Generate a random 16-character (or specified length) Base32 secret key.
     */
    public function generateSecretKey(int $length = 16): string
    {
        $secret = '';
        $charsLen = strlen(self::$base32Chars);
        for ($i = 0; $i < $length; $i++) {
            $secret .= self::$base32Chars[random_int(0, $charsLen - 1)];
        }
        return $secret;
    }

    /**
     * Generate standard OTPAuth URL for Google Authenticator / Microsoft Authenticator.
     */
    public function getQRCodeUrl(string $company, string $holder, string $secret): string
    {
        $issuer = rawurlencode($company);
        $account = rawurlencode($holder);
        return "otpauth://totp/{$issuer}:{$account}?secret={$secret}&issuer={$issuer}&algorithm=SHA1&digits=6&period=30";
    }

    /**
     * Verify a 6-digit OTP against a Base32 secret key.
     */
    public function verifyKey(string $secret, string $key, int $window = 1): bool
    {
        $key = trim($key);
        if (strlen($key) !== 6 || !ctype_digit($key)) {
            return false;
        }

        $secret = strtoupper(str_replace(' ', '', $secret));
        $binarySecret = $this->base32Decode($secret);
        if ($binarySecret === false) {
            return false;
        }

        $currentTimeSlice = (int) floor(time() / 30);

        for ($i = -$window; $i <= $window; $i++) {
            $calculatedOtp = $this->calculateOtp($binarySecret, $currentTimeSlice + $i);
            if (hash_equals((string)$calculatedOtp, $key)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Get current 6-digit OTP code for a secret key.
     */
    public function getCurrentOtp(string $secret): string
    {
        $binarySecret = $this->base32Decode(strtoupper(str_replace(' ', '', $secret)));
        $currentTimeSlice = (int) floor(time() / 30);
        return $this->calculateOtp($binarySecret, $currentTimeSlice);
    }

    /**
     * Calculate 6-digit TOTP code for a given binary secret and timestamp slice.
     */
    protected function calculateOtp(string $binarySecret, int $timeSlice): string
    {
        // Pack time slice into 8-byte big-endian binary
        $timeBytes = pack('N*', 0) . pack('N*', $timeSlice);

        // Calculate HMAC-SHA1
        $hash = hash_hmac('sha1', $timeBytes, $binarySecret, true);

        // Dynamic truncation (RFC 4226)
        $offset = ord($hash[19]) & 0x0f;
        $code = (
            ((ord($hash[$offset + 0]) & 0x7f) << 24) |
            ((ord($hash[$offset + 1]) & 0xff) << 16) |
            ((ord($hash[$offset + 2]) & 0xff) << 8) |
            (ord($hash[$offset + 3]) & 0xff)
        ) % 1000000;

        return str_pad((string)$code, 6, '0', STR_PAD_LEFT);
    }

    /**
     * Decode a Base32 string to binary.
     */
    public function base32Decode(string $b32): string|false
    {
        $b32 = strtoupper($b32);
        $buffer = 0;
        $bufferSize = 0;
        $result = '';

        for ($i = 0; $i < strlen($b32); $i++) {
            $char = $b32[$i];
            if ($char === '=') {
                break;
            }
            $val = strpos(self::$base32Chars, $char);
            if ($val === false) {
                return false;
            }

            $buffer = ($buffer << 5) | $val;
            $bufferSize += 5;

            if ($bufferSize >= 8) {
                $bufferSize -= 8;
                $result .= chr(($buffer >> $bufferSize) & 0xff);
            }
        }

        return $result;
    }

    /**
     * Generate an SVG QR code image using a clean web fallback.
     */
    public function getQRCodeInlineHtml(string $qrCodeUrl, int $size = 180): string
    {
        $encodedUrl = rawurlencode($qrCodeUrl);
        $qrSrc = "https://api.qrserver.com/v1/create-qr-code/?size={$size}x{$size}&data={$encodedUrl}&margin=10";
        return "<img src=\"{$qrSrc}\" alt=\"QR Code 2FA\" width=\"{$size}\" height=\"{$size}\" class=\"rounded-lg mx-auto\" />";
    }
}

<?php

namespace App\Services;

class EmailSecurityService
{
    /**
     * Known legitimate public email domains
     */
    protected static array $legitimateDomains = [
        'gmail.com',
        'yahoo.com',
        'yahoo.co.id',
        'outlook.com',
        'hotmail.com',
        'live.com',
        'icloud.com',
        'proton.me',
        'protonmail.com',
        'mail.com',
        'aol.com',
        'zoho.com',
        'yandex.com',
    ];

    /**
     * Major provider targets for fuzzy / typo comparison
     */
    protected static array $providerTargets = [
        'gmail.com',
        'yahoo.com',
        'yahoo.co.id',
        'outlook.com',
        'hotmail.com',
        'icloud.com',
        'proton.me',
        'protonmail.com',
    ];

    /**
     * Explicit common typo mappings (instant dictionary lookup)
     */
    protected static array $typoDomains = [
        // Gmail variants & typos (including gnail, gmeil, gmaul, etc.)
        'gnail.com'    => 'gmail.com',
        'gnail'        => 'gmail.com',
        'gmai.com'     => 'gmail.com',
        'gmai'         => 'gmail.com',
        'gmial.com'    => 'gmail.com',
        'gmial'        => 'gmail.com',
        'gmaill.com'   => 'gmail.com',
        'gmaill'       => 'gmail.com',
        'gamil.com'    => 'gmail.com',
        'gamil'        => 'gmail.com',
        'gmal.com'     => 'gmail.com',
        'gmal'         => 'gmail.com',
        'gemail.com'   => 'gmail.com',
        'gmeil.com'    => 'gmail.com',
        'gmeil'        => 'gmail.com',
        'gmaul.com'    => 'gmail.com',
        'gmaul'        => 'gmail.com',
        'gmqil.com'    => 'gmail.com',
        'gmail.co'     => 'gmail.com',
        'gmail.con'    => 'gmail.com',
        'gmaild.com'   => 'gmail.com',
        'gmaik.com'    => 'gmail.com',
        'gmail.cm'     => 'gmail.com',
        'gmaol.com'    => 'gmail.com',
        'gmail.cpm'    => 'gmail.com',
        'gmail.om'     => 'gmail.com',
        'gmail.comm'   => 'gmail.com',
        'gmai.co'      => 'gmail.com',
        'gmail.co.id'  => 'gmail.com',
        'g-mail.com'   => 'gmail.com',
        'gmail.net'    => 'gmail.com',
        'gmail.org'    => 'gmail.com',
        'gmail'        => 'gmail.com',

        // Yahoo variants & typos
        'yaho.com'     => 'yahoo.com',
        'yaho'         => 'yahoo.com',
        'yahooo.com'   => 'yahoo.com',
        'yaho.co.id'   => 'yahoo.co.id',
        'yaho.co'      => 'yahoo.com',
        'yahoo.con'    => 'yahoo.com',
        'yahoo.comm'   => 'yahoo.com',
        'yaho.id'      => 'yahoo.co.id',
        'yahoo'        => 'yahoo.com',

        // Outlook & Hotmail variants & typos
        'outlok.com'   => 'outlook.com',
        'outluk.com'   => 'outlook.com',
        'outlook.con'  => 'outlook.com',
        'outlookk.com' => 'outlook.com',
        'outlook'      => 'outlook.com',
        'hotmial.com'  => 'hotmail.com',
        'hotmai.com'   => 'hotmail.com',
        'hotmaill.com' => 'hotmail.com',
        'hotmail.con'  => 'hotmail.com',
        'hotmail'      => 'hotmail.com',

        // iCloud variants & typos
        'icoud.com'    => 'icloud.com',
        'iclod.com'    => 'icloud.com',
        'icloud.con'   => 'icloud.com',
        'icloud'       => 'icloud.com',

        // ProtonMail variants & typos
        'protonmial.com' => 'protonmail.com',
        'protonmai.com'  => 'protonmail.com',
    ];

    /**
     * Validasi format email dan deteksi typo domain (Dictionary + Levenshtein fuzzy matching).
     * 
     * @param string $input
     * @return array [
     *   'is_email'   => bool,
     *   'is_valid'   => bool,
     *   'error'      => string|null,
     *   'suggestion' => string|null
     * ]
     */
    public static function checkEmail(string $input): array
    {
        $input = trim($input);

        // Jika input adalah nomor telepon murni, bukan email
        if (preg_match('/^(\+62|62|08|0)[0-9\s\-]+$/', $input) || !str_contains($input, '@')) {
            return [
                'is_email'   => false,
                'is_valid'   => true,
                'error'      => null,
                'suggestion' => null,
            ];
        }

        $parts = explode('@', $input, 2);
        if (count($parts) < 2) {
            return [
                'is_email'   => true,
                'is_valid'   => false,
                'error'      => 'Format email tidak lengkap.',
                'suggestion' => null,
            ];
        }

        $localPart = $parts[0];
        $domain = strtolower(trim($parts[1]));

        if (empty($localPart)) {
            return [
                'is_email'   => true,
                'is_valid'   => false,
                'error'      => 'Nama pengguna sebelum tanda "@" tidak boleh kosong.',
                'suggestion' => null,
            ];
        }

        if (empty($domain)) {
            return [
                'is_email'   => true,
                'is_valid'   => false,
                'error'      => 'Domain email setelah tanda "@" belum diisi.',
                'suggestion' => $localPart . '@gmail.com',
            ];
        }

        // 1. Cek jika domain ada di daftar typo eksplisit
        if (isset(self::$typoDomains[$domain])) {
            $suggestedDomain = self::$typoDomains[$domain];
            $suggestion = "{$localPart}@{$suggestedDomain}";
            return [
                'is_email'   => true,
                'is_valid'   => false,
                'error'      => "Domain email '@{$domain}' salah ketik (typo). Maksud Anda '{$suggestion}'?",
                'suggestion' => $suggestion,
            ];
        }

        // 2. Cek jika domain tidak memiliki titik / ekstensi TLD (misal: user@gmail atau user@gnail)
        if (!str_contains($domain, '.')) {
            // Cek kemiripan fuzzy dengan nama provider tanpa TLD
            $baseSuggestion = 'gmail.com';
            if (levenshtein($domain, 'gmail') <= 2) {
                $baseSuggestion = 'gmail.com';
            } elseif (levenshtein($domain, 'yahoo') <= 2) {
                $baseSuggestion = 'yahoo.com';
            } elseif (levenshtein($domain, 'outlook') <= 2) {
                $baseSuggestion = 'outlook.com';
            } elseif (levenshtein($domain, 'hotmail') <= 2) {
                $baseSuggestion = 'hotmail.com';
            } else {
                $baseSuggestion = "{$domain}.com";
            }

            $suggestion = "{$localPart}@{$baseSuggestion}";
            return [
                'is_email'   => true,
                'is_valid'   => false,
                'error'      => "Domain email '@{$domain}' belum lengkap (tidak memiliki ekstensi domain seperti .com).",
                'suggestion' => $suggestion,
            ];
        }

        // 3. Cek jika domain berakhiran titik (misal: user@gmail.)
        if (str_ends_with($domain, '.')) {
            $fixedDomain = rtrim($domain, '.') . '.com';
            $suggestion = "{$localPart}@{$fixedDomain}";
            return [
                'is_email'   => true,
                'is_valid'   => false,
                'error'      => "Domain email tidak boleh diakhiri tanda titik.",
                'suggestion' => $suggestion,
            ];
        }

        // 4. Fuzzy Levenshtein Distance Check terhadap provider populer (jika bukan domain resmi)
        if (!in_array($domain, self::$legitimateDomains, true)) {
            foreach (self::$providerTargets as $targetProvider) {
                $distance = levenshtein($domain, $targetProvider);
                // Jika selisih hanya 1 atau 2 karakter terhadap provider populer
                if ($distance > 0 && $distance <= 2) {
                    $suggestion = "{$localPart}@{$targetProvider}";
                    return [
                        'is_email'   => true,
                        'is_valid'   => false,
                        'error'      => "Domain email '@{$domain}' terdeteksi salah ketik. Apakah maksud Anda '{$suggestion}'?",
                        'suggestion' => $suggestion,
                    ];
                }
            }
        }

        // 5. Validasi standar sintaks email RFC
        if (!filter_var($input, FILTER_VALIDATE_EMAIL)) {
            return [
                'is_email'   => true,
                'is_valid'   => false,
                'error'      => 'Format penulisan alamat email tidak valid.',
                'suggestion' => null,
            ];
        }

        return [
            'is_email'   => true,
            'is_valid'   => true,
            'error'      => null,
            'suggestion' => null,
        ];
    }
}

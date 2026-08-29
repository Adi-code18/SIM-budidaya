<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kode OTP Verifikasi Login - SIM-BUDIDAYA</title>
</head>
<body style="font-family: 'Segoe UI', Helvetica, Arial, sans-serif; background-color: #f4f6f9; margin: 0; padding: 24px; color: #334155;">
    <table role="presentation" width="100%" border="0" cellspacing="0" cellpadding="0" style="max-width: 560px; margin: 0 auto; background-color: #ffffff; border-radius: 16px; overflow: hidden; box-shadow: 0 4px 20px rgba(0,0,0,0.06); border: 1px solid #e2e8f0;">
        <!-- Header -->
        <tr>
            <td style="background-color: #051B44; padding: 32px 24px; text-align: center;">
                <h1 style="color: #ffffff; margin: 0; font-size: 22px; font-weight: 800; letter-spacing: 0.5px;">
                    SIM-BUDIDAYA
                </h1>
                <p style="color: #38bdf8; margin: 6px 0 0 0; font-size: 12px; font-weight: 600; text-transform: uppercase; letter-spacing: 1px;">
                    Sistem Informasi Manajemen Budidaya Ikan
                </p>
            </td>
        </tr>

        <!-- Content -->
        <tr>
            <td style="padding: 32px 28px;">
                <h2 style="color: #0f172a; font-size: 18px; margin: 0 0 12px 0; font-weight: 700;">
                    Verifikasi Login Manajer
                </h2>
                <p style="font-size: 14px; line-height: 1.6; color: #475569; margin: 0 0 20px 0;">
                    Halo <strong>{{ $userName }}</strong>, sistem mendeteksi permintaan masuk ke akun Manajer Anda. Gunakan kode One-Time Password (OTP) berikut untuk menyelesaikan proses autentikasi:
                </p>

                <!-- OTP Box -->
                <div style="background: linear-gradient(135deg, #051B44 0%, #0F2C59 100%); border-radius: 12px; padding: 24px; text-align: center; margin: 24px 0;">
                    <span style="display: block; font-size: 11px; font-weight: 700; color: #94a3b8; text-transform: uppercase; letter-spacing: 1.5px; margin-bottom: 8px;">
                        Kode OTP Anda
                    </span>
                    <div style="font-family: 'Courier New', Courier, monospace; font-size: 36px; font-weight: 800; color: #ffffff; letter-spacing: 10px; padding: 6px 0;">
                        {{ $otpCode }}
                    </div>
                    <span style="display: inline-block; background-color: rgba(56, 189, 248, 0.15); color: #38bdf8; border: 1px solid rgba(56, 189, 248, 0.3); font-size: 11px; font-weight: 600; padding: 4px 12px; border-radius: 20px; margin-top: 8px;">
                        Berlaku selama {{ $expiresInMinutes }} menit
                    </span>
                </div>

                <div style="background-color: #f8fafc; border-left: 4px solid #f59e0b; padding: 12px 16px; border-radius: 6px; margin: 24px 0;">
                    <p style="font-size: 12px; line-height: 1.5; color: #78350f; margin: 0;">
                        <strong>Penting:</strong> Jangan bagikan kode OTP ini kepada siapapun termasuk pihak yang mengatasnamakan tim pengembang SIM-BUDIDAYA.
                    </p>
                </div>

                <p style="font-size: 12px; line-height: 1.6; color: #64748b; margin: 0;">
                    Jika Anda tidak merasa melakukan percobaan login ini, segera ubah kata sandi akun Anda demi menjaga keamanan data sistem.
                </p>
            </td>
        </tr>

        <!-- Footer -->
        <tr>
            <td style="background-color: #f8fafc; border-top: 1px solid #e2e8f0; padding: 20px 24px; text-align: center;">
                <p style="font-size: 11px; color: #94a3b8; margin: 0;">
                    &copy; {{ date('Y') }} SIM-BUDIDAYA. Pesan ini dikirim otomatis oleh sistem keamanan.
                </p>
            </td>
        </tr>
    </table>
</body>
</html>

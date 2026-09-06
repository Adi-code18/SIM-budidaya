<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kode OTP Pemulihan Kata Sandi - SIM-BUDIDAYA</title>
    <!--[if mso]>
    <style type="text/css">
        body, table, td, a { font-family: Arial, Helvetica, sans-serif !important; }
    </style>
    <![endif]-->
</head>
<body style="font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; background-color: #ffffff; margin: 0; padding: 32px 12px; color: #1e293b; -webkit-font-smoothing: antialiased;">

    @php
        $logoUrl = isset($message) && method_exists($message, 'embed') && file_exists(public_path('build/images/Logo aquafarm.png'))
            ? $message->embed(public_path('build/images/Logo aquafarm.png')) 
            : asset('build/images/Logo aquafarm.png');
            
        try {
            $formattedDate = \Carbon\Carbon::now()->locale('id')->translatedFormat('d F Y');
        } catch (\Exception $e) {
            $formattedDate = date('d F Y');
        }
    @endphp

    <!-- Outer Container (Clean White Card) -->
    <table role="presentation" width="100%" border="0" cellspacing="0" cellpadding="0" style="max-width: 580px; margin: 0 auto; background-color: #ffffff; border-radius: 24px; overflow: hidden; box-shadow: 0 4px 24px rgba(0, 0, 0, 0.06); border: 1px solid #e2e8f0;">
        
        <!-- TOP ACCENT LINE -->
        <tr>
            <td style="height: 4px; background: linear-gradient(90deg, #0284c7 0%, #38bdf8 100%); line-height: 4px; font-size: 4px;">&nbsp;</td>
        </tr>

        <!-- TOP HEADER SECTION (Logo Aquafarm, Tanggal/Bulan Terkini, dan Judul) -->
        <tr>
            <td style="padding: 32px 32px 16px 32px; background-color: #ffffff;">
                <table role="presentation" width="100%" border="0" cellspacing="0" cellpadding="0">
                    <tr>
                        <td valign="top" style="vertical-align: top;">
                            <!-- Logo Brand Header -->
                            <table role="presentation" border="0" cellspacing="0" cellpadding="0" style="margin-bottom: 16px;">
                                <tr>
                                    <td valign="middle" style="vertical-align: middle;">
                                        <img src="{{ $logoUrl }}" alt="Logo Aquafarm" width="44" height="44" style="display: block; width: 44px; height: 44px; object-fit: contain; border-radius: 10px;" />
                                    </td>
                                    <td valign="middle" style="vertical-align: middle; padding-left: 12px;">
                                        <span style="font-size: 17px; font-weight: 900; color: #051b44; letter-spacing: -0.3px; display: block; line-height: 1.1;">SIM-BUDIDAYA</span>
                                        <span style="font-size: 10px; font-weight: 800; color: #0284c7; letter-spacing: 1.2px; text-transform: uppercase;">PUSAT KEAMANAN AKUN</span>
                                    </td>
                                </tr>
                            </table>

                            <!-- Tanggal & Bulan yang Benar -->
                            <div style="font-size: 11px; font-weight: 800; color: #0284c7; text-transform: uppercase; letter-spacing: 1.5px; margin-bottom: 6px;">
                                {{ strtoupper($formattedDate) }} &bull; RESET PASSWORD
                            </div>

                            <!-- Main Headline -->
                            <h1 style="color: #051b44; font-size: 24px; font-weight: 900; line-height: 1.2; margin: 0; letter-spacing: -0.4px;">
                                Permintaan Reset Kata Sandi
                            </h1>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>

        <!-- MAIN CONTENT SECTION (White Background) -->
        <tr>
            <td style="padding: 8px 32px 24px 32px; background-color: #ffffff;">
                <table role="presentation" width="100%" border="0" cellspacing="0" cellpadding="0">
                    <tr>
                        <td style="padding: 0;">
                            
                            <!-- Greeting -->
                            <p style="font-size: 15px; font-weight: 700; color: #0f172a; margin: 0 0 10px 0;">
                                Halo {{ $userName }},
                            </p>

                            <!-- Description -->
                            <p style="font-size: 13px; line-height: 1.65; color: #475569; margin: 0 0 22px 0;">
                                Kami menerima permintaan untuk mengatur ulang kata sandi akun <strong style="color: #051b44;">SIM-BUDIDAYA</strong> Anda. Gunakan kode <strong>One-Time Password (OTP)</strong> berikut untuk memverifikasi identitas Anda:
                            </p>

                            <!-- Modern OTP Box (Sky Blue Gradient) -->
                            <table role="presentation" width="100%" border="0" cellspacing="0" cellpadding="0" style="margin: 0 0 22px 0;">
                                <tr>
                                    <td style="background: linear-gradient(135deg, #0284c7 0%, #0369a1 100%); border-radius: 16px; padding: 22px 16px; text-align: center; border: 1px solid #0284c7; box-shadow: 0 6px 20px rgba(2, 132, 199, 0.25);">
                                        <span style="display: block; font-size: 11px; font-weight: 800; color: #e0f2fe; text-transform: uppercase; letter-spacing: 2px; margin-bottom: 8px;">
                                            KODE OTP PEMULIHAN
                                        </span>
                                        <div style="font-family: 'Courier New', Courier, monospace; font-size: 38px; font-weight: 900; color: #ffffff; letter-spacing: 12px; padding: 6px 0; margin-left: 12px; text-shadow: 0 2px 6px rgba(0,0,0,0.15);">
                                            {{ $otpCode }}
                                        </div>
                                        <div style="margin-top: 8px;">
                                            <span style="display: inline-block; background-color: rgba(255, 255, 255, 0.22); color: #ffffff; border: 1px solid rgba(255, 255, 255, 0.35); font-size: 11px; font-weight: 700; padding: 4px 14px; border-radius: 20px;">
                                                &#9201; Berlaku selama {{ $expiresInMinutes }} menit
                                            </span>
                                        </div>
                                    </td>
                                </tr>
                            </table>

                            <!-- Security Notice Box -->
                            <div style="background-color: #f0fdf4; border-left: 4px solid #10b981; padding: 12px 16px; border-radius: 10px; margin-bottom: 22px; border-top: 1px solid #dcfce7; border-right: 1px solid #dcfce7; border-bottom: 1px solid #dcfce7;">
                                <p style="font-size: 12px; line-height: 1.55; color: #065f46; margin: 0;">
                                    <strong style="color: #047857;">Penting:</strong> Jangan berikan kode OTP ini kepada siapapun termasuk staf/petugas pengelola SIM-BUDIDAYA demi keamanan data Anda.
                                </p>
                            </div>

                            <p style="font-size: 12px; line-height: 1.6; color: #64748b; margin: 0 0 22px 0;">
                                Jika Anda tidak pernah meminta pemulihan kata sandi ini, abaikan email ini. Kata sandi akun Anda tetap aman dan tidak akan berubah.
                            </p>

                            <!-- Bottom Security Team Signature -->
                            <table role="presentation" width="100%" border="0" cellspacing="0" cellpadding="0" style="border-top: 1px solid #f1f5f9; padding-top: 18px;">
                                <tr>
                                    <td width="40" valign="middle" style="vertical-align: middle; width: 40px;">
                                        <div style="width: 36px; height: 36px; border-radius: 50%; background: linear-gradient(135deg, #38bdf8 0%, #0284c7 100%); display: table-cell; vertical-align: middle; text-align: center; color: #ffffff; font-size: 15px; font-weight: bold; box-shadow: 0 2px 6px rgba(2, 132, 199, 0.25);">
                                            &#128737;
                                        </div>
                                    </td>
                                    <td valign="middle" style="vertical-align: middle; padding-left: 12px;">
                                        <div style="font-size: 12px; font-weight: 800; color: #051b44; line-height: 1.2;">
                                            Pusat Keamanan & Otentikasi
                                        </div>
                                        <div style="font-size: 11px; font-weight: 500; color: #64748b; line-height: 1.2; margin-top: 2px;">
                                            SIM-BUDIDAYA Akuakultur Management System
                                        </div>
                                    </td>
                                </tr>
                            </table>

                        </td>
                    </tr>
                </table>
            </td>
        </tr>

        <!-- FOOTER SECTION (White Background with Top Border) -->
        <tr>
            <td style="padding: 16px 32px 28px 32px; text-align: center; background-color: #fafbfc; border-top: 1px solid #f1f5f9;">
                <p style="font-size: 11px; line-height: 1.5; color: #64748b; margin: 0 0 4px 0;">
                    &copy; {{ date('Y') }} <strong>SIM-BUDIDAYA</strong>. Hak Cipta Dilindungi Undang-Undang.
                </p>
                <p style="font-size: 10px; color: #94a3b8; margin: 0;">
                    Email ini dikirim secara otomatis oleh protokol keamanan sistem saat ada permintaan reset kata sandi akun.
                </p>
            </td>
        </tr>

    </table>

</body>
</html>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Setup 2FA - SIM-BUDIDAYA</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
    </style>
</head>
<body class="bg-[#f4f6f9] min-h-screen flex items-center justify-center p-4">

    <div class="w-full max-w-lg bg-white rounded-3xl shadow-xl border border-slate-100 p-8 sm:p-10">
        
        <div class="text-center mb-6">
            <div class="w-14 h-14 rounded-2xl bg-[#051B44] text-white flex items-center justify-center mx-auto mb-3 shadow-lg shadow-[#051B44]/20">
                <i class="fa-solid fa-qrcode text-2xl text-sky-400"></i>
            </div>
            <h1 class="text-xl font-extrabold text-[#051B44]">Aktivasi Autentikasi Dua Langkah (2FA)</h1>
            <p class="text-xs text-slate-500 font-medium mt-1">Pindai QR code di bawah menggunakan aplikasi Google Authenticator.</p>
        </div>

        <div class="bg-slate-50 p-6 rounded-2xl border border-slate-200 flex flex-col items-center justify-center mb-6">
            <div class="bg-white p-3 rounded-xl shadow-xs border border-slate-100 mb-3">
                {!! $qrSvg !!}
            </div>
            <p class="text-[11px] text-slate-400 font-semibold mb-1">Atau masukkan kode rahasia secara manual:</p>
            <code class="text-xs font-mono font-bold bg-slate-200/70 text-slate-800 px-3 py-1.5 rounded-lg tracking-wider">{{ $secretKey }}</code>
        </div>

        @if ($errors->any())
        <div class="mb-4 p-3.5 rounded-2xl bg-rose-50 border border-rose-200 text-rose-700 text-xs flex items-center gap-2.5">
            <i class="fa-solid fa-circle-exclamation text-rose-500 shrink-0"></i>
            <span>{{ $errors->first() }}</span>
        </div>
        @endif

        <form action="{{ route('2fa.confirm') }}" method="POST" class="space-y-4">
            @csrf

            <div>
                <label class="block text-xs font-semibold text-slate-600 mb-1.5 text-center">Kode Konfirmasi 6-Digit</label>
                <input type="text" 
                       name="code" 
                       maxlength="6"
                       pattern="[0-9]*"
                       inputmode="numeric"
                       placeholder="Masukkan 6 digit kode dari aplikasi" 
                       required 
                       class="w-full text-center tracking-[0.4em] text-xl font-bold py-2.5 px-4 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:bg-white focus:border-[#051B44]">
            </div>

            <button type="submit" class="w-full py-3 bg-[#051B44] hover:bg-[#09265c] text-white font-bold text-xs rounded-xl shadow-lg transition-all">
                Konfirmasi & Aktifkan 2FA
            </button>
        </form>

    </div>

</body>
</html>

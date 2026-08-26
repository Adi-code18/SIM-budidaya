<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verifikasi 2FA - SIM-BUDIDAYA</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
    </style>
</head>
<body class="bg-[#f4f6f9] min-h-screen flex items-center justify-center p-4">

    <div class="w-full max-w-md bg-white rounded-3xl shadow-xl border border-slate-100 p-8 sm:p-10">
        
        <div class="text-center mb-8">
            <div class="w-14 h-14 rounded-2xl bg-[#051B44] text-white flex items-center justify-center mx-auto mb-4 shadow-lg shadow-[#051B44]/20">
                <i class="fa-solid fa-shield-halved text-2xl text-sky-400"></i>
            </div>
            <h1 class="text-xl font-extrabold text-[#051B44]">Verifikasi Dua Langkah</h1>
            <p class="text-xs text-slate-500 font-medium mt-1">Masukkan 6 digit kode dari aplikasi Google Authenticator Anda.</p>
        </div>

        @if ($errors->any())
        <div class="mb-5 p-3.5 rounded-2xl bg-rose-50 border border-rose-200 text-rose-700 text-xs flex items-center gap-2.5">
            <i class="fa-solid fa-circle-exclamation text-rose-500 shrink-0"></i>
            <span>{{ $errors->first() }}</span>
        </div>
        @endif

        <form action="{{ route('2fa.verify') }}" method="POST" class="space-y-5">
            @csrf

            <div>
                <label class="block text-xs font-semibold text-slate-600 mb-2 text-center">Kode OTP 6-Digit</label>
                <div class="relative max-w-xs mx-auto">
                    <input type="text" 
                           name="code" 
                           maxlength="6"
                           pattern="[0-9]*"
                           inputmode="numeric"
                           placeholder="123456" 
                           required 
                           autofocus
                           class="w-full text-center tracking-[0.6em] text-2xl font-extrabold py-3 px-4 bg-slate-50 border border-slate-200 rounded-2xl focus:outline-none focus:bg-white focus:border-[#051B44] focus:ring-2 focus:ring-[#051B44]/10 transition-all">
                </div>
            </div>

            <button type="submit" class="w-full py-3 bg-[#051B44] hover:bg-[#09265c] text-white font-bold text-xs rounded-xl shadow-lg shadow-[#051B44]/20 transition-all flex items-center justify-center gap-2">
                <span>Verifikasi & Masuk</span>
                <i class="fa-solid fa-arrow-right text-xs"></i>
            </button>
        </form>

        <div class="mt-6 text-center border-t border-slate-100 pt-4">
            <a href="{{ route('login') }}" class="text-xs font-bold text-slate-400 hover:text-slate-600 transition-colors">
                <i class="fa-solid fa-arrow-left mr-1"></i> Kembali ke Login
            </a>
        </div>

    </div>

</body>
</html>

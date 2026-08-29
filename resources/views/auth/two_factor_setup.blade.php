<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Aktivasi 2FA - SIM-BUDIDAYA</title>
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

    <div class="w-full max-w-md bg-white rounded-3xl shadow-xl border border-slate-100 p-6 sm:p-8 my-6"
         x-data="{ 
             copied: false,
             copyKey() {
                 navigator.clipboard.writeText('{{ $secretKey }}');
                 this.copied = true;
                 setTimeout(() => this.copied = false, 2000);
             }
         }">
        
        <!-- Header -->
        <div class="text-center mb-6">
            <div class="w-14 h-14 rounded-2xl bg-[#051B44] text-white flex items-center justify-center mx-auto mb-3 shadow-lg shadow-[#051B44]/20">
                <i class="fa-solid fa-qrcode text-2xl text-sky-400"></i>
            </div>
            <h1 class="text-xl font-extrabold text-[#051B44]">Aktivasi Google Authenticator</h1>
            <p class="text-xs text-slate-500 font-medium mt-1">Pindai kode QR di bawah menggunakan aplikasi <strong>Google Authenticator</strong> di HP Anda.</p>
        </div>

        @if ($errors->any())
        <div class="mb-5 p-3.5 rounded-2xl bg-rose-50 border border-rose-200 text-rose-700 text-xs flex items-center gap-2.5">
            <i class="fa-solid fa-circle-exclamation text-rose-500 shrink-0 text-base"></i>
            <span>{{ $errors->first() }}</span>
        </div>
        @endif

        @if (session('status'))
        <div class="mb-5 p-3.5 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-700 text-xs flex items-center gap-2.5">
            <i class="fa-solid fa-circle-check text-emerald-500 shrink-0 text-base"></i>
            <span>{{ session('status') }}</span>
        </div>
        @endif

        <!-- QR Code Card -->
        <div class="bg-slate-50 p-5 rounded-2xl border border-slate-200/90 flex flex-col items-center justify-center mb-5">
            <div class="bg-white p-3 rounded-2xl shadow-sm border border-slate-200 flex items-center justify-center max-w-[200px] max-h-[200px] mb-3 overflow-hidden [&>svg]:w-full [&>svg]:h-full [&>img]:w-full [&>img]:h-full">
                {!! $qrCodeSvg !!}
            </div>

            <p class="text-[11px] text-slate-500 font-semibold mb-1.5 text-center">Atau masukkan kunci rahasia manual:</p>
            
            <div class="inline-flex items-center gap-1.5 bg-white border border-slate-200 rounded-xl px-3 py-1.5 shadow-xs max-w-full">
                <code class="text-xs font-mono font-bold text-slate-800 tracking-wider truncate">{{ $secretKey }}</code>
                <button type="button" 
                        @click="copyKey()" 
                        class="text-xs px-2 py-0.5 rounded-lg font-bold transition-all text-slate-500 hover:text-[#051B44] hover:bg-slate-100 flex items-center gap-1"
                        title="Salin Kunci">
                    <i class="fa-regular" :class="copied ? 'fa-circle-check text-emerald-600' : 'fa-copy'"></i>
                    <span x-text="copied ? 'Tersalin' : 'Salin'"></span>
                </button>
            </div>
        </div>

        <!-- Verification Form -->
        <form action="{{ route('2fa.confirm') }}" method="POST" class="space-y-4">
            @csrf

            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1.5 text-center">
                    Masukkan 6-Digit Kode OTP dari Aplikasi
                </label>
                <div class="relative max-w-xs mx-auto">
                    <input type="text" 
                           name="code" 
                           maxlength="6"
                           pattern="[0-9]*"
                           inputmode="numeric"
                           placeholder="123456" 
                           required 
                           autofocus
                           autocomplete="one-time-code"
                           class="w-full text-center tracking-[0.5em] text-2xl font-extrabold py-3 px-4 bg-slate-50 border border-slate-200 rounded-2xl focus:outline-none focus:bg-white focus:border-[#051B44] focus:ring-2 focus:ring-[#051B44]/10 transition-all">
                </div>
            </div>

            <button type="submit" class="w-full py-3.5 bg-[#051B44] hover:bg-[#09265c] text-white font-bold text-xs rounded-xl shadow-lg shadow-[#051B44]/20 transition-all flex items-center justify-center gap-2 cursor-pointer">
                <span>Konfirmasi & Masuk</span>
                <i class="fa-solid fa-arrow-right text-xs"></i>
            </button>
        </form>

        <!-- Back Link -->
        <div class="mt-6 text-center border-t border-slate-100 pt-4">
            <a href="{{ route('2fa.cancel') }}" class="text-xs font-bold text-slate-400 hover:text-slate-600 transition-colors inline-flex items-center gap-1.5">
                <i class="fa-solid fa-arrow-left"></i>
                <span>Kembali ke Login</span>
            </a>
        </div>

    </div>

</body>
</html>

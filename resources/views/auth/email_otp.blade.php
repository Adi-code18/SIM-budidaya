<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verifikasi OTP Email - SIM-BUDIDAYA</title>
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
             cooldown: {{ $resendCooldown ?? 0 }},
             timer: null,
             init() {
                 if (this.cooldown > 0) {
                     this.timer = setInterval(() => {
                         if (this.cooldown > 1) {
                             this.cooldown--;
                         } else {
                             this.cooldown = 0;
                             clearInterval(this.timer);
                         }
                     }, 1000);
                 }
             }
         }">
        
        <!-- Header -->
        <div class="text-center mb-6">
            <div class="w-14 h-14 rounded-2xl bg-[#051B44] text-white flex items-center justify-center mx-auto mb-3 shadow-lg shadow-[#051B44]/20">
                <i class="fa-solid fa-envelope-open-text text-2xl text-sky-400"></i>
            </div>
            <h1 class="text-xl font-extrabold text-[#051B44]">Verifikasi OTP Email</h1>
            <p class="text-xs text-slate-500 font-medium mt-1.5 leading-relaxed">
                Kode keamanan 6-digit telah dikirimkan dari <strong class="text-slate-700">ad8101058@gmail.com</strong> ke email akun Manajer:
                <br>
                <span class="font-bold text-[#051B44] bg-slate-100 px-2.5 py-1 rounded-lg inline-block mt-1 border border-slate-200">{{ $user->email ?? $maskedEmail }}</span>
            </p>
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

        <div class="mb-5 p-3 rounded-2xl bg-sky-50/80 border border-sky-200/70 text-sky-900 text-xs flex items-start gap-2.5">
            <i class="fa-solid fa-circle-info text-sky-600 shrink-0 text-base mt-0.5"></i>
            <span class="leading-relaxed">
                Silakan buka aplikasi/web <strong>Gmail</strong> untuk akun email di atas. Jika belum muncul di <em>Kotak Masuk (Inbox)</em>, mohon periksa folder <em>Spam</em> atau <em>Promosi</em>.
            </span>
        </div>

        <!-- OTP Form -->
        <form action="{{ route('email.otp.verify') }}" method="POST" class="space-y-5">
            @csrf

            <div>
                <label class="block text-xs font-bold text-slate-700 mb-2 text-center">
                    Masukkan 6-Digit Kode OTP
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
                <span>Verifikasi & Masuk Dashboard</span>
                <i class="fa-solid fa-arrow-right text-xs"></i>
            </button>
        </form>

        <!-- Resend OTP Section -->
        <div class="mt-6 pt-5 border-t border-slate-100 flex flex-col items-center gap-3">
            <p class="text-xs text-slate-500 font-medium">
                Tidak menerima email kode verifikasi?
            </p>

            <form action="{{ route('email.otp.resend') }}" method="POST">
                @csrf
                <button type="submit" 
                        :disabled="cooldown > 0"
                        :class="cooldown > 0 ? 'opacity-50 cursor-not-allowed text-slate-400' : 'text-[#051B44] hover:underline cursor-pointer'"
                        class="text-xs font-extrabold flex items-center gap-1.5 transition-all">
                    <i class="fa-solid fa-rotate-right" :class="cooldown > 0 ? 'animate-spin' : ''"></i>
                    <span x-text="cooldown > 0 ? 'Kirim ulang dalam (' + cooldown + 'd)' : 'Kirim Ulang Kode OTP'"></span>
                </button>
            </form>
        </div>

        <!-- Back Link -->
        <div class="mt-5 text-center">
            <a href="{{ route('email.otp.cancel') }}" class="text-xs font-bold text-slate-400 hover:text-slate-600 transition-colors inline-flex items-center gap-1.5">
                <i class="fa-solid fa-arrow-left"></i>
                <span>Kembali ke Halaman Login</span>
            </a>
        </div>

    </div>

</body>
</html>

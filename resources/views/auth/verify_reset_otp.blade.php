<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verifikasi OTP Reset Password - SIM-BUDIDAYA</title>
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Tailwind CSS CDN Fallback -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
    </style>
</head>
<body class="bg-[#f4f6f9] min-h-screen flex items-center justify-center p-4 sm:p-6 lg:p-10">

    <div class="w-full max-w-md bg-white rounded-3xl shadow-xl shadow-slate-200/60 overflow-hidden border border-slate-100 p-8 sm:p-10"
         x-data="{
             otpCode: '',
             remainingSeconds: {{ $remainingSeconds ?? 600 }},
             resendCooldown: {{ $resendCooldown ?? 0 }},
             timer: null,
             resendTimer: null,
             init() {
                 if (this.remainingSeconds > 0) {
                     this.timer = setInterval(() => {
                         if (this.remainingSeconds > 0) this.remainingSeconds--;
                         else clearInterval(this.timer);
                     }, 1000);
                 }
                 if (this.resendCooldown > 0) {
                     this.resendTimer = setInterval(() => {
                         if (this.resendCooldown > 0) this.resendCooldown--;
                         else clearInterval(this.resendTimer);
                     }, 1000);
                 }
             },
             formatTimer(secs) {
                 const m = Math.floor(secs / 60);
                 const s = secs % 60;
                 return (m < 10 ? '0' + m : m) + ':' + (s < 10 ? '0' + s : s);
             }
         }">
        
        <!-- Header Logo & Brand -->
        <div class="flex items-center gap-3.5 mb-6">
            <img src="{{ asset('build/images/Logo aquafarm.png') }}" 
                 alt="Logo Aquafarm" 
                 class="h-11 w-auto object-contain shrink-0">
            <div>
                <h1 class="font-extrabold text-xl text-[#051B44] tracking-tight leading-tight">SIM-BUDIDAYA</h1>
                <p class="text-[10px] font-bold text-slate-400 tracking-widest uppercase">VERIFIKASI OTP</p>
            </div>
        </div>

        <div class="mb-6">
            <h2 class="text-lg font-extrabold text-slate-800">Masukkan Kode OTP</h2>
            <p class="text-xs text-slate-500 font-medium mt-1 leading-relaxed">
                Kode verifikasi 6-digit telah dikirimkan ke <strong class="text-slate-800 font-mono">{{ $maskedEmail ?? 'email Anda' }}</strong>.
            </p>
        </div>

        <!-- Flash Messages & Error Alerts -->
        @if (session('status'))
        <div class="mb-5 p-3.5 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-700 text-xs flex items-center gap-2.5 shadow-xs">
            <i class="fa-solid fa-circle-check text-emerald-500 text-sm shrink-0"></i>
            <span class="font-semibold">{{ session('status') }}</span>
        </div>
        @endif

        @if ($errors->any())
        <div class="mb-5 p-3.5 rounded-2xl bg-rose-50 border border-rose-200 text-rose-700 text-xs flex items-start gap-2.5 shadow-xs">
            <i class="fa-solid fa-circle-exclamation text-rose-500 text-sm mt-0.5 shrink-0"></i>
            <div>
                <span class="font-bold block">Terjadi Kesalahan:</span>
                <ul class="list-disc list-inside mt-0.5 space-y-0.5 text-[11px] font-medium">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
        @endif

        <!-- Form Submit OTP -->
        <form action="{{ route('forgot.password.verify.post') }}" method="POST" class="space-y-5">
            @csrf

            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-2 text-center">6 Digit Kode OTP</label>
                <div class="relative">
                    <input type="text" 
                           name="code" 
                           x-model="otpCode" 
                           maxlength="6" 
                           pattern="[0-9]{6}" 
                           inputmode="numeric" 
                           required 
                           autofocus
                           placeholder="••••••"
                           class="w-full py-3 text-center text-2xl font-mono font-extrabold tracking-[10px] text-[#051B44] bg-slate-50 border border-slate-200 rounded-2xl focus:outline-none focus:bg-white focus:border-[#0284c7] focus:ring-4 focus:ring-[#0284c7]/15 transition-all">
                </div>
            </div>

            <!-- Timer Expiration Banner -->
            <div class="p-3 rounded-xl bg-sky-50/70 border border-sky-100 flex items-center justify-between text-xs">
                <div class="flex items-center gap-2 text-sky-800 font-medium">
                    <i class="fa-regular fa-clock text-sky-600"></i>
                    <span>Sisa waktu berlaku:</span>
                </div>
                <span class="font-mono font-extrabold text-[#0284c7]" x-text="formatTimer(remainingSeconds)"></span>
            </div>

            <div>
                <button type="submit" 
                        class="w-full py-3 px-5 text-white font-extrabold text-xs rounded-xl bg-[#0284c7] hover:bg-[#0369a1] shadow-lg shadow-[#0284c7]/25 transition-all flex items-center justify-center gap-2 cursor-pointer">
                    <span>Verifikasi Kode OTP</span>
                    <i class="fa-solid fa-arrow-right text-xs"></i>
                </button>
            </div>
        </form>

        <!-- Resend OTP Form -->
        <div class="mt-6 pt-4 border-t border-slate-100 text-center space-y-3">
            <p class="text-xs text-slate-400">
                Tidak menerima email?
            </p>
            
            <form action="{{ route('forgot.password.post') }}" method="POST" class="inline">
                @csrf
                <input type="hidden" name="email" value="{{ $email }}">
                <button type="submit" 
                        :disabled="resendCooldown > 0"
                        :class="resendCooldown > 0 ? 'opacity-50 cursor-not-allowed text-slate-400' : 'text-[#0077C6] hover:text-[#051B44] cursor-pointer'"
                        class="text-xs font-extrabold transition-colors">
                    <span x-show="resendCooldown === 0">Kirim Ulang Kode OTP</span>
                    <span x-show="resendCooldown > 0" x-text="'Kirim ulang dalam (' + resendCooldown + 's)'"></span>
                </button>
            </form>

            <div class="pt-2">
                <a href="{{ route('forgot.password') }}" class="text-xs font-bold text-slate-400 hover:text-slate-600 transition-colors">
                    Ganti Alamat Email
                </a>
            </div>
        </div>

    </div>

</body>
</html>

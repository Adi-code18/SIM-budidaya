<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lupa Kata Sandi - SIM-BUDIDAYA</title>
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
    <!-- Cloudflare Turnstile -->
    <script src="https://challenges.cloudflare.com/turnstile/v0/api.js" async defer></script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
    </style>
</head>
<body class="bg-[#f4f6f9] min-h-screen flex items-center justify-center p-4 sm:p-6 lg:p-10">

    <div class="w-full max-w-md bg-white rounded-3xl shadow-xl shadow-slate-200/60 overflow-hidden border border-slate-100 p-8 sm:p-10"
         x-data="{ 
             isSubmitting: false,
             emailInput: '{{ old('email', '') }}',
             get emailSuggestion() {
                 const val = (this.emailInput || '').trim();
                 if (!val.includes('@') || /^(\+62|62|08|0)[0-9\s\-]+$/.test(val)) return '';
                 const parts = val.split('@');
                 if (parts.length !== 2) return '';
                 const local = parts[0];
                 const domain = parts[1].toLowerCase();
                 if (!local || !domain) return '';

                 const validDomains = ['gmail.com', 'yahoo.com', 'yahoo.co.id', 'outlook.com', 'hotmail.com', 'icloud.com', 'proton.me', 'protonmail.com', 'live.com', 'mail.com'];
                 if (validDomains.includes(domain)) return '';

                 const typoMap = {
                     'gnail.com': 'gmail.com', 'gnail': 'gmail.com',
                     'gmai.com': 'gmail.com', 'gmai': 'gmail.com',
                     'gmial.com': 'gmail.com', 'gmial': 'gmail.com',
                     'gmaill.com': 'gmail.com', 'gmaill': 'gmail.com',
                     'gamil.com': 'gmail.com', 'gamil': 'gmail.com',
                     'gmal.com': 'gmail.com', 'gmal': 'gmail.com',
                     'gemail.com': 'gmail.com',
                     'gmeil.com': 'gmail.com', 'gmeil': 'gmail.com',
                     'gmaul.com': 'gmail.com', 'gmaul': 'gmail.com',
                     'gmqil.com': 'gmail.com',
                     'gmail.co': 'gmail.com', 'gmail.con': 'gmail.com', 'gmaild.com': 'gmail.com',
                     'gmaik.com': 'gmail.com', 'gmail.cm': 'gmail.com', 'gmaol.com': 'gmail.com',
                     'gmail.cpm': 'gmail.com', 'gmail.om': 'gmail.com', 'gmail.comm': 'gmail.com',
                     'gmai.co': 'gmail.com', 'gmail.co.id': 'gmail.com', 'g-mail.com': 'gmail.com',
                     'gmail.net': 'gmail.com', 'gmail.org': 'gmail.com', 'gmail': 'gmail.com',
                     'yaho.com': 'yahoo.com', 'yaho': 'yahoo.com', 'yahooo.com': 'yahoo.com',
                     'yaho.co.id': 'yahoo.co.id', 'yaho.co': 'yahoo.com', 'yahoo.con': 'yahoo.com',
                     'yahoo.comm': 'yahoo.com', 'yaho.id': 'yahoo.co.id', 'yahoo': 'yahoo.com',
                     'outlok.com': 'outlook.com', 'outluk.com': 'outlook.com', 'outlook.con': 'outlook.com',
                     'outlookk.com': 'outlook.com', 'outlook': 'outlook.com',
                     'hotmial.com': 'hotmail.com', 'hotmai.com': 'hotmail.com', 'hotmaill.com': 'hotmail.com',
                     'hotmail.con': 'hotmail.com', 'hotmail': 'hotmail.com',
                     'icoud.com': 'icloud.com', 'iclod.com': 'icloud.com', 'icloud.con': 'icloud.com', 'icloud': 'icloud.com',
                     'protonmial.com': 'protonmail.com', 'protonmai.com': 'protonmail.com'
                 };
                 if (typoMap[domain]) return `${local}@${typoMap[domain]}`;

                 const lev = (a, b) => {
                     const m = [];
                     for (let i = 0; i <= b.length; i++) m[i] = [i];
                     for (let j = 0; j <= a.length; j++) m[0][j] = j;
                     for (let i = 1; i <= b.length; i++) {
                         for (let j = 1; j <= a.length; j++) {
                             m[i][j] = b[i - 1] === a[j - 1] ? m[i - 1][j - 1] : Math.min(m[i - 1][j - 1] + 1, m[i][j - 1] + 1, m[i - 1][j] + 1);
                         }
                     }
                     return m[b.length][a.length];
                 };

                 if (!domain.includes('.')) {
                     if (lev(domain, 'gmail') <= 2) return `${local}@gmail.com`;
                     if (lev(domain, 'yahoo') <= 2) return `${local}@yahoo.com`;
                     if (lev(domain, 'outlook') <= 2) return `${local}@outlook.com`;
                     if (lev(domain, 'hotmail') <= 2) return `${local}@hotmail.com`;
                     return `${local}@${domain}.com`;
                 }

                 if (domain.endsWith('.')) return `${local}@${domain.replace(/\.+$/, '')}.com`;

                 const providers = ['gmail.com', 'yahoo.com', 'yahoo.co.id', 'outlook.com', 'hotmail.com', 'icloud.com', 'proton.me'];
                 for (const prov of providers) {
                     if (lev(domain, prov) <= 2) {
                         return `${local}@${prov}`;
                     }
                 }

                 return '';
             }
         }">
        
        <!-- Header Logo & Brand -->
        <div class="flex items-center gap-3.5 mb-6">
            <img src="{{ asset('build/images/Logo aquafarm.png') }}" 
                 alt="Logo Aquafarm" 
                 class="h-11 w-auto object-contain shrink-0">
            <div>
                <h1 class="font-extrabold text-xl text-[#051B44] tracking-tight leading-tight">SIM-BUDIDAYA</h1>
                <p class="text-[10px] font-bold text-slate-400 tracking-widest uppercase">PEMULIHAN KATA SANDI</p>
            </div>
        </div>

        <div class="mb-6">
            <h2 class="text-lg font-extrabold text-slate-800">Lupa Kata Sandi Akun?</h2>
            <p class="text-xs text-slate-500 font-medium mt-1 leading-relaxed">
                Masukkan alamat email akun Anda. Kami akan mengirimkan <strong>Kode OTP 6-Digit</strong> untuk memverifikasi dan mengatur ulang kata sandi Anda.
            </p>
        </div>

        <!-- Flash Messages & Error Alerts -->
        @if ($errors->any())
        <div class="mb-5 p-3.5 rounded-2xl bg-rose-50 border border-rose-200 text-rose-700 text-xs flex items-start gap-2.5 shadow-xs">
            <i class="fa-solid fa-circle-exclamation text-rose-500 text-sm mt-0.5 shrink-0"></i>
            <div>
                <span class="font-bold block">Perhatian:</span>
                <ul class="list-disc list-inside mt-0.5 space-y-0.5 text-[11px] font-medium">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
        @endif

        @if (session('status'))
        <div class="mb-5 p-3.5 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-700 text-xs flex items-center gap-2.5 shadow-xs">
            <i class="fa-solid fa-circle-check text-emerald-500"></i>
            <span class="font-medium">{{ session('status') }}</span>
        </div>
        @endif

        <!-- Form Request OTP -->
        <form action="{{ route('forgot.password.post') }}" method="POST" @submit="isSubmitting = true" class="space-y-4">
            @csrf

            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1.5">Email atau No. Handphone Akun</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                        <i class="fa-regular fa-user text-sm"></i>
                    </span>
                    <input type="text" 
                           name="email" 
                           x-model="emailInput" 
                           placeholder="nama@email.com / 081234567890" 
                           required 
                           autofocus
                           class="w-full pl-10 pr-4 py-2.5 bg-slate-50/70 border {{ $errors->has('email') ? 'border-rose-300 ring-1 ring-rose-300' : 'border-slate-200' }} rounded-xl text-xs font-semibold text-slate-800 placeholder-slate-400 focus:outline-none focus:bg-white focus:border-[#0284c7] focus:ring-2 focus:ring-[#0284c7]/20 transition-all">
                </div>
                <!-- Typo suggestion chip -->
                <div x-cloak x-show="emailSuggestion && emailSuggestion !== emailInput" class="mt-1.5 p-2 rounded-xl bg-amber-50 border border-amber-200 text-amber-900 text-[11px] flex items-center justify-between gap-2 animate-fadeIn">
                    <div class="flex items-center gap-1.5 truncate">
                        <i class="fa-solid fa-lightbulb text-amber-500 shrink-0"></i>
                        <span class="truncate">Maksud Anda: <strong class="font-bold underline decoration-amber-400" x-text="emailSuggestion"></strong>?</span>
                    </div>
                    <button type="button" @click="emailInput = emailSuggestion" class="shrink-0 px-2 py-0.5 bg-amber-500 hover:bg-amber-600 text-white rounded-lg font-bold text-[10px] transition-colors cursor-pointer">
                        Terapkan
                    </button>
                </div>
            </div>

            <!-- Cloudflare Turnstile Widget -->
            <div class="py-1 flex justify-center">
                <div class="cf-turnstile" data-sitekey="{{ config('services.turnstile.site_key') }}" data-theme="light"></div>
            </div>

            <div class="pt-2">
                <button type="submit" 
                        :disabled="isSubmitting"
                        class="w-full py-3 px-5 text-white font-extrabold text-xs rounded-xl bg-[#0284c7] hover:bg-[#0369a1] shadow-lg shadow-[#0284c7]/25 transition-all flex items-center justify-center gap-2 cursor-pointer disabled:opacity-60 disabled:cursor-not-allowed">
                    <span x-show="!isSubmitting">Kirim Kode OTP Pemulihan</span>
                    <span x-show="isSubmitting" class="flex items-center gap-2">
                        <i class="fa-solid fa-circle-notch fa-spin"></i>
                        <span>Memproses Permintaan...</span>
                    </span>
                    <i x-show="!isSubmitting" class="fa-solid fa-arrow-right text-xs"></i>
                </button>
            </div>
        </form>

        <div class="mt-6 pt-4 border-t border-slate-100 text-center">
            <a href="{{ route('login') }}" class="text-xs font-bold text-slate-500 hover:text-[#0284c7] inline-flex items-center gap-1.5 transition-colors">
                <i class="fa-solid fa-arrow-left text-[11px]"></i>
                <span>Kembali ke Halaman Login</span>
            </a>
        </div>

    </div>

</body>
</html>

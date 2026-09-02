<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login SIM-BUDIDAYA</title>
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

    <!-- Outer Login Card -->
    <div class="w-full max-w-5xl bg-white rounded-3xl shadow-xl shadow-slate-200/60 overflow-hidden grid grid-cols-1 lg:grid-cols-12 border border-slate-100 min-h-[580px]"
         x-data="{ 
             showPassword: false, 
             showForgotNotice: false,
             username: '{{ old('username', '') }}', 
             password: '', 
             remember: {{ old('remember') ? 'true' : 'false' }},
             lockoutSeconds: {{ $errors->any() && preg_match('/(\d+)\s*detik/', implode(' ', $errors->all()), $m) ? (int)$m[1] : 0 }},
             timer: null,
             init() {
                 if (this.lockoutSeconds > 0) {
                     this.timer = setInterval(() => {
                         if (this.lockoutSeconds > 1) {
                             this.lockoutSeconds--;
                         } else {
                             this.lockoutSeconds = 0;
                             clearInterval(this.timer);
                         }
                     }, 1000);
                 }
             }
         }">
        
        <!-- Left Side: Form Section -->
        <div class="lg:col-span-6 p-8 sm:p-12 lg:p-14 flex flex-col justify-center">
            <div class="max-w-md w-full mx-auto">
                
                <!-- Logo & Brand Header -->
                <div class="flex items-center gap-3.5 mb-8">
                    <img src="{{ asset('build/images/logo PT.png') }}" 
                         alt="Logo PT" 
                         class="h-11 w-auto object-contain flex-shrink-0">
                    <div>
                        <h1 class="font-extrabold text-xl text-[#051B44] tracking-tight leading-tight">SIM-BUDIDAYA</h1>
                        <p class="text-[10px] font-bold text-slate-400 tracking-widest uppercase">PORTAL MANAJER</p>
                    </div>
                </div>

                <!-- Error Flash Messages -->
                @if ($errors->any())
                <div class="mb-5 p-3.5 rounded-2xl bg-rose-50 border border-rose-200 text-rose-700 text-xs flex items-start gap-2.5 shadow-xs">
                    <i class="fa-solid fa-circle-exclamation text-rose-500 text-sm mt-0.5 shrink-0"></i>
                    <div>
                        <span class="font-bold block">Gagal Masuk:</span>
                        <ul class="list-disc list-inside mt-0.5 space-y-0.5 text-[11px] font-medium">
                            @foreach ($errors->all() as $error)
                                <li>
                                    @if (str_contains($error, 'detik'))
                                        <span x-show="lockoutSeconds > 0">Terlalu banyak percobaan login gagal. Silakan tunggu <strong class="font-extrabold underline decoration-rose-400" x-text="lockoutSeconds"></strong> detik lagi.</span>
                                        <span x-show="lockoutSeconds === 0" class="text-emerald-700 font-bold">Waktu tunggu telah selesai. Silakan coba login kembali.</span>
                                    @else
                                        <span>{{ $error }}</span>
                                    @endif
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
                @endif

                @if (session('status'))
                <div class="mb-5 p-3.5 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-700 text-xs flex items-center gap-2.5">
                    <i class="fa-solid fa-circle-check text-emerald-500"></i>
                    <span>{{ session('status') }}</span>
                </div>
                @endif

                <!-- Form -->
                <form action="{{ route('login.post') }}" method="POST" class="space-y-4">
                    @csrf
                    
                    <!-- Field 1: Username or Email -->
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1.5">Email atau No. Handphone</label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                                <i class="fa-regular fa-user text-sm"></i>
                            </span>
                            <input type="text" 
                                   name="username"
                                   x-model="username"
                                   placeholder="Contoh: manajer@example.com"
                                   required
                                   autofocus
                                   class="w-full pl-10 pr-4 py-2.5 bg-slate-50/50 border {{ $errors->has('username') ? 'border-rose-300 ring-1 ring-rose-300' : 'border-slate-200' }} rounded-xl text-xs font-medium text-slate-800 placeholder-slate-400 focus:outline-none focus:bg-white focus:border-[#051B44] focus:ring-2 focus:ring-[#051B44]/10 transition-all">
                        </div>
                    </div>

                    <!-- Field 2: Password -->
                    <div>
                        <div class="flex items-center justify-between mb-1.5">
                            <label class="block text-xs font-semibold text-slate-600">Kata Sandi</label>
                            <a href="javascript:void(0)" 
                               @click="showForgotNotice = true" 
                               class="text-xs font-semibold text-[#0077C6] hover:text-[#051B44] transition-colors cursor-pointer">
                                Lupa Password?
                            </a>
                        </div>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                                <i class="fa-solid fa-lock text-sm"></i>
                            </span>
                            <input :type="showPassword ? 'text' : 'password'" 
                                   name="password"
                                   x-model="password"
                                   placeholder="••••••••"
                                   required
                                   class="w-full pl-10 pr-10 py-2.5 bg-slate-50/50 border border-slate-200 rounded-xl text-xs font-medium text-slate-800 placeholder-slate-400 focus:outline-none focus:bg-white focus:border-[#051B44] focus:ring-2 focus:ring-[#051B44]/10 transition-all">
                            <button type="button" 
                                     @click="showPassword = !showPassword" 
                                     class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-slate-400 hover:text-slate-600">
                                <i class="fa-regular text-sm" :class="showPassword ? 'fa-eye-slash' : 'fa-eye'"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Checkbox: Remember me -->
                    <div class="flex items-center pt-0.5">
                        <label class="flex items-center gap-2 cursor-pointer select-none">
                            <input type="checkbox" 
                                   name="remember"
                                   x-model="remember"
                                   class="w-4 h-4 rounded border-slate-300 text-[#051B44] focus:ring-[#051B44]/20 cursor-pointer">
                            <span class="text-xs text-slate-500 font-medium">Ingat perangkat ini</span>
                        </label>
                    </div>

                    <!-- Cloudflare Turnstile Widget -->
                    <div class="py-1 flex justify-center">
                        <div class="cf-turnstile" data-sitekey="{{ config('services.turnstile.site_key') }}" data-theme="light"></div>
                    </div>

                    <!-- Submit Button -->
                    <div class="pt-2">
                        <button type="submit" 
                                :disabled="lockoutSeconds > 0"
                                :class="lockoutSeconds > 0 ? 'opacity-60 cursor-not-allowed bg-slate-400 shadow-none' : 'bg-[#051B44] hover:bg-[#09265c] shadow-lg shadow-[#051B44]/25 hover:shadow-xl hover:shadow-[#051B44]/30'"
                                class="w-full py-3 px-5 text-white font-bold text-xs rounded-xl transition-all duration-200 flex items-center justify-center gap-2 group">
                            <span x-text="lockoutSeconds > 0 ? 'Coba lagi dalam (' + lockoutSeconds + 's)' : 'Masuk ke Dashboard Manajer'"></span>
                            <i class="fa-solid text-xs group-hover:translate-x-1 transition-transform" :class="lockoutSeconds > 0 ? 'fa-clock' : 'fa-arrow-right'"></i>
                        </button>
                    </div>
                </form>

                <div class="mt-6 pt-4 border-t border-slate-100 text-center">
                    <p class="text-xs text-slate-400">
                        Petugas operasional lapangan? 
                        <a href="{{ route('mobile.petugas.login') }}" class="text-[#0077C6] font-bold hover:underline">Masuk ke Portal Petugas</a>
                    </p>
                </div>

            </div>
        </div>

        <!-- Right Side: Visual Hero Section -->
        <div class="hidden lg:flex lg:col-span-6 relative bg-gradient-to-br from-[#EEF6FD] via-[#F5FAFE] to-[#E8F3FC] p-8 lg:p-10 flex-col justify-between overflow-hidden border-l border-slate-100">
            <!-- Center Image (Full & Uncropped) -->
            <div class="my-auto flex items-center justify-center">
                <img src="{{ asset('build/images/login ilustration.png') }}" 
                     alt="SIM-BUDIDAYA Illustration" 
                     class="w-full max-h-[280px] object-contain select-none pointer-events-none">
            </div>

            <!-- Bottom Information Card -->
            <div class="p-5 sm:p-6 rounded-2xl bg-white/80 backdrop-blur-md border border-white/90 shadow-sm">
                <h3 class="text-sm font-bold text-slate-800 mb-2">Sistem Informasi Manjemen Budidaya</h3>
                <p class="text-xs text-slate-600 leading-relaxed mb-4">
                    Mengatur tata kelola dan monitoring manajemen di dalam bidang <strong class="text-slate-900 font-bold">Budidaya ikan</strong> dengan media berupa WEB .
                </p>
                <div class="flex items-center gap-6 text-xs font-semibold text-slate-700">
                    <div class="flex items-center gap-2">
                        <i class="fa-solid fa-circle-check text-sky-500 text-sm"></i>
                        <span>Manajer Role</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <i class="fa-solid fa-chart-pie text-sky-500 text-sm"></i>
                        <span>Mengatur Pengelolaan</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal Notifikasi Lupa Password (Non-Fungsional Sementara) -->
        <div x-show="showForgotNotice" 
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100 scale-100"
             x-transition:leave-end="opacity-0 scale-95"
             class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 backdrop-blur-xs p-4"
             style="display: none;">
            <div @click.outside="showForgotNotice = false" 
                 class="bg-white max-w-sm w-full rounded-3xl p-6 shadow-2xl border border-slate-100 text-center space-y-4">
                <div class="w-12 h-12 rounded-2xl bg-sky-100 text-sky-600 mx-auto flex items-center justify-center text-xl">
                    <i class="fa-solid fa-key"></i>
                </div>
                <div>
                    <h3 class="font-extrabold text-base text-slate-900">Bantuan Lupa Password</h3>
                    <p class="text-xs text-slate-500 leading-relaxed mt-1">
                        Layanan pemulihan kata sandi mandiri belum diaktifkan. Silakan hubungi Administrator/Superadmin SIM-BUDIDAYA untuk melakukan reset kredensial akun Anda.
                    </p>
                </div>
                <button type="button" @click="showForgotNotice = false" 
                        class="w-full py-2.5 rounded-xl bg-[#051B44] text-white text-xs font-bold hover:bg-[#09265c] transition-colors">
                    Mengerti
                </button>
            </div>
        </div>

    </div>

</body>
</html>

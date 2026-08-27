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
                    <div class="w-11 h-11 rounded-xl bg-[#051B44] flex items-center justify-center text-white shadow-lg shadow-[#051B44]/20 flex-shrink-0">
                        <svg class="w-6 h-6 fill-current text-white" viewBox="0 0 24 24">
                            <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 14.5h-2v-2h2v2zm0-4h-2V7h2v5.5z" opacity="0.3"/>
                            <path d="M21.5 12c0-2.37-.83-4.55-2.22-6.26l-1.42 1.42C19.06 8.44 19.75 10.14 19.75 12s-.69 3.56-1.89 4.84l1.42 1.42C20.67 16.55 21.5 14.37 21.5 12zM6.14 7.16L4.72 5.74C3.33 7.45 2.5 9.63 2.5 12s.83 4.55 2.22 6.26l1.42-1.42C4.94 15.56 4.25 13.86 4.25 12s.69-3.56 1.89-4.84zM12 4.5c-4.14 0-7.5 3.36-7.5 7.5s3.36 7.5 7.5 7.5 7.5-3.36 7.5-7.5-3.36-7.5-7.5-7.5zm0 13c-3.03 0-5.5-2.47-5.5-5.5S8.97 6.5 12 6.5s5.5 2.47 5.5 5.5-2.47 5.5-5.5 5.5z"/>
                        </svg>
                    </div>
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
                            <a href="#" class="text-xs font-semibold text-[#0077C6] hover:text-[#051B44] transition-colors">Lupa Sandi?</a>
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
        <div class="hidden lg:block lg:col-span-6 relative bg-slate-900 overflow-hidden">
            <!-- Background Image -->
            <img src="https://images.unsplash.com/photo-1544551763-46a013bb70d5?auto=format&fit=crop&q=80&w=1200" 
                 alt="Aquafarm Management" 
                 class="absolute inset-0 w-full h-full object-cover">

            <!-- Soft Overlay Gradient -->
            <div class="absolute inset-0 bg-gradient-to-t from-[#051B44]/85 via-transparent to-black/20"></div>

            <!-- Floating Informational Card -->
            <div class="absolute bottom-6 left-6 right-6 p-6 rounded-2xl bg-[#E9F0F8]/90 backdrop-blur-md border border-white/60 shadow-2xl">
                <h3 class="text-xs font-bold text-slate-800 mb-1.5">Sistem Informasi Manajemen Budidaya</h3>
                <p class="text-[11px] text-slate-600 leading-relaxed mb-3">
                    Mengatur tata kelola dan monitoring manajemen di dalam bidang <strong class="text-slate-900 font-extrabold">Budidaya ikan</strong> terintegrasi berbasis WEB & Mobile.
                </p>
                <div class="flex items-center gap-4 text-[10px] font-bold text-slate-700">
                    <div class="flex items-center gap-1.5">
                        <i class="fa-solid fa-shield-halved text-[#38BDF8]"></i>
                        <span>Keamanan Berlapis (One Session & Rate Limiter)</span>
                    </div>
                </div>
            </div>
        </div>

    </div>

</body>
</html>

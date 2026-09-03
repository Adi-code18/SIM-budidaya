<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Mobile Petugas - SIM-BUDIDAYA</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="https://challenges.cloudflare.com/turnstile/v0/api.js" async defer></script>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        navy: {
                            700: '#1E3E62',
                            800: '#0F2C59',
                            900: '#0B192C',
                            950: '#051120'
                        }
                    }
                }
            }
        }
    </script>
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
    </style>
</head>
<body class="h-full bg-slate-100 flex items-center justify-center p-0 sm:p-4 antialiased">

    @php
        $initialRole = old('selectedRole', $role ?? 'distribusi');
    @endphp

    <div class="w-full sm:max-w-md min-h-screen sm:min-h-0 bg-white flex flex-col justify-between p-6 sm:p-8 overflow-y-auto sm:rounded-3xl sm:shadow-xl border border-slate-200 relative sm:my-8" 
         x-data="{
             selectedRole: '{{ $initialRole }}',
             showPass: false,
             toastShow: false,
             toastMsg: '',
             toastType: 'info',
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
             },
             
             triggerToast(msg, type = 'info') {
                 this.toastMsg = msg;
                 this.toastType = type;
                 this.toastShow = true;
                 setTimeout(() => { this.toastShow = false; }, 3000);
             }
         }">

        <!-- Toast Notification floating -->
        <div x-show="toastShow"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 -translate-y-2"
             x-transition:enter-end="opacity-100 translate-y-0"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100 translate-y-0"
             x-transition:leave-end="opacity-0 -translate-y-2"
             class="fixed top-4 left-1/2 -translate-x-1/2 z-50 px-4 py-2.5 rounded-2xl shadow-xl border text-xs font-bold flex items-center gap-2 max-w-xs w-full"
             :class="{
                 'bg-emerald-600 text-white border-emerald-500': toastType === 'success',
                 'bg-rose-600 text-white border-rose-500': toastType === 'error',
                 'bg-navy-900 text-white border-navy-800': toastType === 'info'
             }" style="display: none;">
            <i class="fa-solid" :class="{
                'fa-circle-check': toastType === 'success',
                'fa-triangle-exclamation': toastType === 'error',
                'fa-circle-info': toastType === 'info'
            }"></i>
            <span x-text="toastMsg"></span>
        </div>

        <div>
            <!-- Top Logo Section -->
            <div class="pt-4 flex flex-col items-center text-center space-y-2.5">
                <img src="{{ asset('build/images/logo PT.png') }}" 
                     alt="Logo PT" 
                     class="h-14 w-auto object-contain drop-shadow-sm">
                <div>
                    <p class="text-[11px] text-slate-500 font-medium mt-0.5 max-w-xs leading-relaxed">
                        Masuk ke aplikasi SIM-BUDIDAYA Mobile sesuai peran petugas operasional Anda.
                    </p>
                </div>
            </div>

            <!-- Error Alerts -->
            @if ($errors->any())
            <div class="my-3 p-3 rounded-2xl bg-rose-50 border border-rose-200 text-rose-700 text-xs flex items-start gap-2 shadow-xs">
                <i class="fa-solid fa-circle-exclamation text-rose-500 text-sm mt-0.5 shrink-0"></i>
                <div>
                    <span class="font-bold block">Gagal Masuk:</span>
                    <ul class="list-disc list-inside mt-0.5 space-y-0.5 text-[11px]">
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

            <!-- Role Switcher Tabs -->
            <div class="my-3 space-y-1">
                <label class="text-[10px] font-extrabold uppercase text-slate-400 tracking-wider block text-center">PILIH PERAN PETUGAS</label>
                <div class="grid grid-cols-3 gap-1.5 p-1 bg-slate-100 rounded-2xl text-[11px] font-bold">
                    <button type="button" @click="selectedRole = 'distribusi'"
                            :class="selectedRole === 'distribusi' ? 'bg-navy-800 text-white shadow-xs' : 'text-slate-600 hover:text-slate-900'"
                            class="py-2 rounded-xl transition-all text-center flex flex-col items-center gap-1">
                        <i class="fa-solid fa-truck text-xs"></i>
                        <span>Distribusi</span>
                    </button>

                    <button type="button" @click="selectedRole = 'pembesaran'"
                            :class="selectedRole === 'pembesaran' ? 'bg-navy-800 text-white shadow-xs' : 'text-slate-600 hover:text-slate-900'"
                            class="py-2 rounded-xl transition-all text-center flex flex-col items-center gap-1">
                        <i class="fa-solid fa-bars-staggered text-xs"></i>
                        <span>Pembesaran</span>
                    </button>

                    <button type="button" @click="selectedRole = 'pembibitan'"
                            :class="selectedRole === 'pembibitan' ? 'bg-navy-800 text-white shadow-xs' : 'text-slate-600 hover:text-slate-900'"
                            class="py-2 rounded-xl transition-all text-center flex flex-col items-center gap-1">
                        <i class="fa-solid fa-droplet text-xs"></i>
                        <span>Pembibitan</span>
                    </button>
                </div>
                
                <p class="text-[10px] text-sky-700 font-extrabold text-center pt-0.5" 
                   x-text="selectedRole === 'pembibitan' ? 'Akses Petugas Pembibitan & Hatchery' : (selectedRole === 'pembesaran' ? 'Akses Petugas Kolam Pembesaran' : 'Akses Petugas Distribusi & Pengiriman')"></p>
            </div>

            <!-- Login Form Section -->
            <form action="{{ route('mobile.petugas.login.post') }}" method="POST" class="space-y-3.5 my-2">
                @csrf
                
                <!-- Hidden Selected Role Field -->
                <input type="hidden" name="selectedRole" :value="selectedRole">

                <!-- Input 1: Email / No. HP -->
                <div class="space-y-1">
                    <label class="text-[10px] font-extrabold uppercase text-slate-500 tracking-wider block">EMAIL / NO. HP</label>
                    <div class="relative">
                        <i class="fa-solid fa-user absolute left-3.5 top-3.5 text-slate-400 text-xs"></i>
                        <input type="text" 
                               name="email"
                               value="{{ old('email') }}"
                               placeholder="Contoh: distribusi@example.com / 0812..."
                               required
                               class="w-full pl-9 pr-4 py-2.5 rounded-2xl bg-slate-50 border {{ $errors->has('email') ? 'border-rose-300 ring-1 ring-rose-300' : 'border-slate-200' }} text-xs font-semibold text-slate-800 placeholder-slate-400 focus:bg-white focus:outline-none focus:ring-2 focus:ring-navy-800 transition-all">
                    </div>
                </div>

                <!-- Input 2: Password -->
                <div class="space-y-1">
                    <label class="text-[10px] font-extrabold uppercase text-slate-500 tracking-wider block">KATA KUNCI</label>
                    <div class="relative">
                        <i class="fa-solid fa-lock absolute left-3.5 top-3.5 text-slate-400 text-xs"></i>
                        <input :type="showPass ? 'text' : 'password'" 
                               name="password"
                               placeholder="Masukkan kata kunci"
                               required
                               class="w-full pl-9 pr-10 py-2.5 rounded-2xl bg-slate-50 border border-slate-200 text-xs font-semibold text-slate-800 placeholder-slate-400 focus:bg-white focus:outline-none focus:ring-2 focus:ring-navy-800 transition-all">
                        <button type="button" @click="showPass = !showPass" class="absolute right-3.5 top-3 text-slate-400 hover:text-slate-600">
                            <i class="fa-solid" :class="showPass ? 'fa-eye-slash text-xs' : 'fa-eye text-xs'"></i>
                        </button>
                    </div>
                    <div class="flex justify-end pt-0.5">
                        <a href="javascript:void(0)" @click.prevent="triggerToast('Layanan lupa password belum diaktifkan. Silakan hubungi Administrator.', 'info')" 
                           class="text-[11px] font-bold text-sky-700 hover:underline cursor-pointer">
                            Lupa Password?
                        </a>
                    </div>
                </div>

                <!-- Cloudflare Turnstile Widget -->
                <div class="py-1 flex justify-center">
                    <div class="cf-turnstile" data-sitekey="{{ config('services.turnstile.site_key') }}" data-[#051120] data-theme="light"></div>
                </div>

                <!-- Submit Button -->
                <button type="submit" 
                        :disabled="lockoutSeconds > 0"
                        :class="lockoutSeconds > 0 ? 'opacity-60 cursor-not-allowed bg-slate-400 shadow-none' : 'bg-navy-800 hover:bg-navy-900 active:scale-[0.99] shadow-md'"
                        class="w-full py-3 rounded-2xl text-white font-extrabold text-xs flex items-center justify-center gap-2 transition-all">
                    <span x-text="lockoutSeconds > 0 ? 'Coba lagi dalam (' + lockoutSeconds + 's)' : 'Masuk Petugas'"></span>
                    <i class="fa-solid text-xs" :class="lockoutSeconds > 0 ? 'fa-clock' : 'fa-arrow-right'"></i>
                </button>

            </form>
        </div>
        <!-- Footer Support Section -->
        <div class="text-center pt-2 pb-1 border-t border-slate-100">
            <p class="text-[11px] text-slate-400 font-medium">
                Admin / Manajer? 
                <a href="{{ route('login') }}" class="text-sky-700 font-bold hover:underline">Portal Web Manajer</a>
            </p>
        </div>

    </div>

</body>
</html>

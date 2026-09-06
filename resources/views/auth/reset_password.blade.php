<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Buat Kata Sandi Baru - SIM-BUDIDAYA</title>
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
             showNew: false, 
             showConfirm: false, 
             password: '', 
             password_confirmation: '' 
         }">
        
        <!-- Header Logo & Brand -->
        <div class="flex items-center gap-3.5 mb-6">
            <img src="{{ asset('build/images/Logo aquafarm.png') }}" 
                 alt="Logo Aquafarm" 
                 class="h-11 w-auto object-contain shrink-0">
            <div>
                <h1 class="font-extrabold text-xl text-[#051B44] tracking-tight leading-tight">SIM-BUDIDAYA</h1>
                <p class="text-[10px] font-bold text-slate-400 tracking-widest uppercase">KATA SANDI BARU</p>
            </div>
        </div>

        <div class="mb-6">
            <h2 class="text-lg font-extrabold text-slate-800">Atur Ulang Kata Sandi</h2>
            <p class="text-xs text-slate-500 font-medium mt-1 leading-relaxed">
                Buat kata sandi baru yang kuat (minimal 6 karakter) untuk mengakses akun Anda.
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

        <!-- Form Reset Password -->
        <form action="{{ route('forgot.password.reset.post') }}" method="POST" class="space-y-4">
            @csrf
            <input type="hidden" name="token" value="{{ $token }}">

            <!-- New Password -->
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1.5">Kata Sandi Baru</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                        <i class="fa-solid fa-lock text-sm"></i>
                    </span>
                    <input :type="showNew ? 'text' : 'password'" 
                           name="password" 
                           x-model="password"
                           placeholder="Minimal 6 karakter" 
                           required 
                           minlength="6"
                           autofocus
                           class="w-full pl-10 pr-10 py-2.5 bg-slate-50/70 border border-slate-200 rounded-xl text-xs font-semibold text-slate-800 placeholder-slate-400 focus:outline-none focus:bg-white focus:border-[#0284c7] focus:ring-2 focus:ring-[#0284c7]/20 transition-all">
                    <button type="button" 
                            @click="showNew = !showNew" 
                            class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-slate-400 hover:text-slate-600">
                        <i class="fa-regular text-sm" :class="showNew ? 'fa-eye-slash' : 'fa-eye'"></i>
                    </button>
                </div>
            </div>

            <!-- Confirm New Password -->
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1.5">Konfirmasi Kata Sandi Baru</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                        <i class="fa-solid fa-shield-check text-sm"></i>
                    </span>
                    <input :type="showConfirm ? 'text' : 'password'" 
                           name="password_confirmation" 
                           x-model="password_confirmation"
                           placeholder="Ulangi kata sandi baru" 
                           required 
                           minlength="6"
                           class="w-full pl-10 pr-10 py-2.5 bg-slate-50/70 border border-slate-200 rounded-xl text-xs font-semibold text-slate-800 placeholder-slate-400 focus:outline-none focus:bg-white focus:border-[#0284c7] focus:ring-2 focus:ring-[#0284c7]/20 transition-all">
                    <button type="button" 
                            @click="showConfirm = !showConfirm" 
                            class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-slate-400 hover:text-slate-600">
                        <i class="fa-regular text-sm" :class="showConfirm ? 'fa-eye-slash' : 'fa-eye'"></i>
                    </button>
                </div>
            </div>

            <div class="pt-2">
                <button type="submit" 
                        class="w-full py-3 px-5 text-white font-extrabold text-xs rounded-xl bg-[#0284c7] hover:bg-[#0369a1] shadow-lg shadow-[#0284c7]/25 transition-all flex items-center justify-center gap-2 cursor-pointer">
                    <i class="fa-solid fa-check text-xs"></i>
                    <span>Simpan Kata Sandi Baru</span>
                </button>
            </div>
        </form>

    </div>

</body>
</html>

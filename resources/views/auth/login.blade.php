<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - SIM-BUDIDAYA</title>
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Tailwind CSS CDN Fallback -->
    <script src="https://cdn.tailwindcss.com"></script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
    </style>
</head>
<body class="bg-slate-100 min-h-screen flex items-center justify-center p-4 sm:p-6 lg:p-10">

    <div class="w-full max-w-5xl bg-white rounded-3xl shadow-2xl overflow-hidden grid grid-cols-1 lg:grid-cols-12 min-h-[620px] border border-slate-200/80">
        
        <!-- Left Side: Login Form -->
        <div class="lg:col-span-6 p-8 sm:p-12 flex flex-col justify-between">
            <div>
                <!-- Brand Header -->
                <div class="flex items-center gap-3 mb-8">
                    <div class="w-11 h-11 rounded-2xl bg-gradient-to-tr from-blue-600 to-sky-400 flex items-center justify-center shadow-lg shadow-blue-500/30">
                        <i class="fa-solid fa-fish-fins text-white text-2xl"></i>
                    </div>
                    <div>
                        <h1 class="font-extrabold text-xl text-slate-900 tracking-tight">SIM-BUDIDAYA</h1>
                        <p class="text-xs text-slate-500 font-medium">Sistem Informasi Manajemen Budidaya Ikan</p>
                    </div>
                </div>

                <!-- Title -->
                <div class="mb-8">
                    <h2 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">Selamat Datang Kembali</h2>
                    <p class="text-sm text-slate-500 mt-2">Masuk dengan kredensial akun <span class="font-semibold text-sky-600">Manajer Budidaya</span> Anda untuk mengakses dashboard.</p>
                </div>

                <!-- Form -->
                <form action="{{ route('dashboard') }}" method="GET" class="space-y-5">
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">Email / ID Manajer</label>
                        <div class="relative">
                            <i class="fa-regular fa-envelope absolute left-4 top-1/2 -translate-y-1/2 text-slate-400"></i>
                            <input type="text" 
                                   value="manajer@simbudidaya.id" 
                                   required 
                                   class="w-full pl-11 pr-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm font-medium focus:bg-white focus:outline-none focus:ring-2 focus:ring-sky-500 focus:border-sky-500 transition-all"
                                   placeholder="manajer@simbudidaya.id">
                        </div>
                    </div>

                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-700">Kata Sandi</label>
                            <a href="#" class="text-xs font-semibold text-sky-600 hover:text-sky-700">Lupa kata sandi?</a>
                        </div>
                        <div class="relative">
                            <i class="fa-solid fa-lock absolute left-4 top-1/2 -translate-y-1/2 text-slate-400"></i>
                            <input type="password" 
                                   value="••••••••••••" 
                                   required 
                                   class="w-full pl-11 pr-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm font-medium focus:bg-white focus:outline-none focus:ring-2 focus:ring-sky-500 focus:border-sky-500 transition-all"
                                   placeholder="••••••••">
                        </div>
                    </div>

                    <div class="flex items-center justify-between pt-1">
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" checked class="w-4 h-4 text-sky-600 rounded border-slate-300 focus:ring-sky-500">
                            <span class="text-xs text-slate-600 font-medium">Ingat saya di perangkat ini</span>
                        </label>
                    </div>

                    <button type="submit" 
                            class="w-full py-3.5 px-6 rounded-xl bg-gradient-to-r from-blue-700 to-sky-600 hover:from-blue-800 hover:to-sky-700 text-white font-bold text-sm shadow-lg shadow-blue-600/30 hover:shadow-blue-600/50 transition-all flex items-center justify-center gap-2">
                        <span>Masuk ke Dashboard Manajer</span>
                        <i class="fa-solid fa-arrow-right text-xs"></i>
                    </button>
                </form>
            </div>

            <!-- Role Note -->
            <div class="mt-8 pt-6 border-t border-slate-100 flex items-center justify-between text-xs text-slate-500">
                <span class="inline-flex items-center gap-1.5 font-medium">
                    <span class="w-2 h-2 rounded-full bg-emerald-500"></span> Sistem Online
                </span>
                <span>Role Terdeteksi: <strong class="text-slate-800">Manajer Utama</strong></span>
            </div>
        </div>

        <!-- Right Side: Hero Visual -->
        <div class="hidden lg:col-span-6 bg-slate-900 relative flex flex-col justify-end p-10 overflow-hidden">
            <!-- Background Image -->
            <img src="https://images.unsplash.com/photo-1544551763-46a013bb70d5?auto=format&fit=crop&q=80&w=1000" 
                 alt="Aquaculture Ponds" 
                 class="absolute inset-0 w-full h-full object-cover opacity-60 mix-blend-overlay">
            
            <div class="absolute inset-0 bg-gradient-to-t from-slate-950 via-slate-900/40 to-transparent"></div>

            <!-- Overlay Card -->
            <div class="relative z-10 p-6 rounded-2xl bg-white/10 backdrop-blur-md border border-white/20 text-white shadow-2xl space-y-3">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-sky-500/20 text-sky-300 text-xs font-semibold border border-sky-400/30">
                    <i class="fa-solid fa-circle-check"></i> SIM-BUDIDAYA Enterprise
                </div>
                <h3 class="text-xl font-bold tracking-tight">Kendalikan & Monitor Seluruh Kolam dalam Satu Dashboard</h3>
                <p class="text-xs text-slate-200 leading-relaxed">
                    Kelola log pakan harian, estimasi panen, distribusi pesanan, dan arus keuangan secara real-time dengan efisiensi tinggi.
                </p>
            </div>
        </div>

    </div>

</body>
</html>

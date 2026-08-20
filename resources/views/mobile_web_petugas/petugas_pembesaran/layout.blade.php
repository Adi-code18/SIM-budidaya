<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>@yield('title', 'Petugas Pembesaran - SIM-BUDIDAYA Mobile')</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Font Awesome 6 Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        navy: {
                            50: '#f0f4f9',
                            100: '#d9e2ec',
                            600: '#1b365d',
                            700: '#132845',
                            800: '#0F2C59',
                            900: '#0B192C',
                            950: '#060e1a',
                        }
                    },
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'sans-serif'],
                    }
                }
            }
        }
    </script>

    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: #0b1528;
            color: #1e293b;
            -webkit-tap-highlight-color: transparent;
        }

        ::-webkit-scrollbar {
            width: 4px;
            height: 4px;
        }
        ::-webkit-scrollbar-track {
            background: transparent;
        }
        ::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 9999px;
        }

        .mobile-screen-wrapper {
            max-w: 480px;
            min-height: 100vh;
            margin: 0 auto;
            background-color: #f8fafc;
            position: relative;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.4);
        }

        @media (min-width: 640px) {
            body {
                padding: 1.5rem 0;
                background: linear-gradient(135deg, #091322 0%, #11233e 100%);
            }
            .mobile-screen-wrapper {
                min-height: 844px;
                max-height: 920px;
                border-radius: 40px;
                overflow: hidden;
                border: 10px solid #1e293b;
            }
        }

        .glass-header {
            background: rgba(255, 255, 255, 0.94);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
        }
    </style>
    @stack('styles')
</head>
<body x-data="{ 
    searchOpen: false, 
    searchQuery: '',
    toastShow: false,
    toastMessage: '',
    toastType: 'success',
    triggerToast(msg, type = 'success') {
        this.toastMessage = msg;
        this.toastType = type;
        this.toastShow = true;
        setTimeout(() => { this.toastShow = false; }, 3500);
    }
}">

    <div class="mobile-screen-wrapper flex flex-col justify-between">
        
        <!-- Header (AQUA-FIELD / SIM-BUDIDAYA) -->
        @hasSection('hide_header')
        @else
        <header class="sticky top-0 z-40 glass-header border-b border-slate-100 px-4 py-3 flex items-center justify-between shadow-xs">
            <div class="flex items-center gap-2.5">
                <a href="{{ route('petugas.pembesaran.dashboard') }}" class="flex items-center gap-2">
                    <div class="w-8 h-8 rounded-xl bg-navy-800 flex items-center justify-center text-white shadow-xs">
                        <i class="fa-solid fa-water text-xs text-sky-300"></i>
                    </div>
                    <div>
                        <span class="font-extrabold text-xs tracking-tight text-navy-900 block leading-none">AQUA-FIELD</span>
                        <span class="text-[9px] font-bold text-slate-400 tracking-wider uppercase block mt-0.5">Petugas Pembesaran</span>
                    </div>
                </a>
            </div>

            <div class="flex items-center gap-2">
                <!-- Status Badge -->
                <span class="px-2.5 py-0.5 rounded-full text-[9px] font-extrabold bg-emerald-50 text-emerald-700 border border-emerald-200 flex items-center gap-1">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                    STANDBY
                </span>
                
                <button @click="searchOpen = !searchOpen" 
                        class="w-8 h-8 rounded-full bg-slate-100 text-slate-600 flex items-center justify-center hover:bg-slate-200 transition-colors">
                    <i class="fa-solid fa-magnifying-glass text-xs"></i>
                </button>
            </div>
        </header>

        <!-- Search Bar Modal -->
        <div x-show="searchOpen" 
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 -translate-y-2"
             x-transition:enter-end="opacity-100 translate-y-0"
             class="sticky top-14 z-30 bg-white px-4 py-3 border-b border-slate-200 shadow-md">
            <div class="relative">
                <i class="fa-solid fa-magnifying-glass absolute left-3.5 top-3 text-slate-400 text-xs"></i>
                <input type="text" 
                       x-model="searchQuery" 
                       placeholder="Cari kolam, siklus DOC, biomassa..." 
                       class="w-full pl-9 pr-8 py-2 bg-slate-100 text-xs font-semibold rounded-xl text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-navy-800">
            </div>
        </div>
        @endif

        <!-- Toast Alert Banner -->
        <div x-show="toastShow" 
             x-transition:enter="transition ease-out duration-300 transform"
             x-transition:enter-start="opacity-0 -translate-y-4"
             x-transition:enter-end="opacity-100 translate-y-0"
             x-transition:leave="transition ease-in duration-200 transform"
             x-transition:leave-start="opacity-100 translate-y-0"
             x-transition:leave-end="opacity-0 -translate-y-4"
             class="fixed top-4 left-1/2 -translate-x-1/2 z-50 w-11/12 max-w-xs px-4 py-3 rounded-2xl shadow-xl flex items-center gap-3 text-xs font-bold text-white"
             :class="toastType === 'success' ? 'bg-emerald-600' : (toastType === 'error' ? 'bg-rose-600' : 'bg-navy-800')">
            <i class="fa-solid" :class="toastType === 'success' ? 'fa-circle-check' : (toastType === 'error' ? 'fa-triangle-exclamation' : 'fa-circle-info')"></i>
            <span x-text="toastMessage" class="flex-1"></span>
            <button @click="toastShow = false" class="text-white/80 hover:text-white">
                <i class="fa-solid fa-xmark text-xs"></i>
            </button>
        </div>

        <!-- Main Content Area -->
        <main class="flex-1 overflow-y-auto pb-20">
            @yield('content')
        </main>

        <!-- Bottom Navigation Bar (Dashboard, Pakan, Profile) -->
        @hasSection('hide_nav')
        @else
        <nav class="fixed sm:absolute bottom-0 left-0 right-0 z-40 bg-white/95 backdrop-blur-md border-t border-slate-200/80 px-6 py-2 flex items-center justify-around shadow-lg">
            
            <!-- Tab 1: Dashboard -->
            <a href="{{ route('petugas.pembesaran.dashboard') }}" 
               class="flex flex-col items-center py-1 px-3 rounded-xl transition-all duration-200 {{ request()->routeIs('petugas.pembesaran.dashboard*') ? 'text-navy-800 font-bold' : 'text-slate-400 hover:text-slate-600 font-medium' }}">
                <i class="fa-solid fa-chart-line text-lg {{ request()->routeIs('petugas.pembesaran.dashboard*') ? 'text-navy-800' : 'text-slate-400' }}"></i>
                <span class="text-[10px] mt-1 tracking-tight">Dashboard</span>
            </a>

            <!-- Tab 2: Pakan -->
            <a href="{{ route('petugas.pembesaran.log-pakan') }}" 
               class="flex flex-col items-center py-1 px-3 rounded-xl transition-all duration-200 {{ request()->routeIs('petugas.pembesaran.log-pakan*') || request()->routeIs('petugas.pembesaran.create-batch*') ? 'text-navy-800 font-bold' : 'text-slate-400 hover:text-slate-600 font-medium' }}">
                <div class="relative">
                    <i class="fa-solid fa-bowl-food text-lg {{ request()->routeIs('petugas.pembesaran.log-pakan*') || request()->routeIs('petugas.pembesaran.create-batch*') ? 'text-navy-800' : 'text-slate-400' }}"></i>
                    <span class="absolute -top-1 -right-1.5 w-2 h-2 bg-emerald-500 rounded-full"></span>
                </div>
                <span class="text-[10px] mt-1 tracking-tight">Pakan</span>
            </a>

            <!-- Tab 3: Profile -->
            <a href="{{ route('petugas.pembesaran.akun') }}" 
               class="flex flex-col items-center py-1 px-3 rounded-xl transition-all duration-200 {{ request()->routeIs('petugas.pembesaran.akun*') ? 'text-navy-800 font-bold' : 'text-slate-400 hover:text-slate-600 font-medium' }}">
                <i class="fa-solid fa-user text-lg {{ request()->routeIs('petugas.pembesaran.akun*') ? 'text-navy-800' : 'text-slate-400' }}"></i>
                <span class="text-[10px] mt-1 tracking-tight">Profile</span>
            </a>

        </nav>
        @endif

    </div>

    @stack('scripts')
</body>
</html>

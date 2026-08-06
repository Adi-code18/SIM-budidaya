<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'SIM-BUDIDAYA - Management System')</title>
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <!-- Tailwind CSS CDN Fallback -->
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
                        }
                    }
                }
            }
        }
    </script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: #f8fafc;
        }
        .bg-navy-900 { background-color: #0B192C; }
        .bg-navy-800 { background-color: #0F2C59; }
        .bg-navy-700 { background-color: #1E3E62; }
        .text-navy-900 { color: #0B192C; }
        .text-navy-800 { color: #0F2C59; }
        .border-navy-800 { border-color: #0F2C59; }
        .sidebar-active {
            background-color: #0284C7;
            color: #ffffff;
            font-weight: 600;
            box-shadow: 0 4px 12px rgba(2, 132, 199, 0.3);
        }
        /* Custom scrollbar */
        ::-webkit-scrollbar {
            width: 6px;
            height: 6px;
        }
        ::-webkit-scrollbar-track {
            background: #f1f5f9;
        }
        ::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 4px;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }
    </style>
</head>
<body class="antialiased text-slate-800 bg-slate-50 min-h-screen" x-data="{ sidebarOpen: false }">

    <div class="flex h-screen overflow-hidden">
        
        <!-- Mobile Sidebar Backdrop -->
        <div x-show="sidebarOpen" 
             x-transition:enter="transition-opacity ease-linear duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition-opacity ease-linear duration-300"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             @click="sidebarOpen = false"
             class="fixed inset-0 z-40 bg-slate-900/60 backdrop-blur-sm lg:hidden"></div>

        <!-- Sidebar Navigation -->
        <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'" 
               class="fixed inset-y-0 left-0 z-50 w-64 bg-navy-800 text-white flex flex-col transition-transform duration-300 ease-in-out lg:static lg:translate-x-0 shrink-0 shadow-2xl">
            
            <!-- App Branding -->
            <div class="h-20 px-6 flex items-center justify-between border-b border-white/10">
                <a href="{{ route('dashboard') }}" class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-sky-400 to-blue-600 flex items-center justify-center shadow-lg shadow-sky-500/30">
                        <i class="fa-solid fa-fish-fins text-white text-xl"></i>
                    </div>
                    <div>
                        <span class="font-extrabold text-lg tracking-wider text-white block leading-tight">SIM-BUDIDAYA</span>
                        <span class="text-[10px] tracking-widest uppercase text-sky-300 font-semibold">Manajemen Ikan</span>
                    </div>
                </a>
                <button @click="sidebarOpen = false" class="lg:hidden text-slate-300 hover:text-white p-1">
                    <i class="fa-solid fa-xmark text-xl"></i>
                </button>
            </div>

            <!-- Navigation Links -->
            <nav class="flex-1 px-4 py-6 overflow-y-auto space-y-1">
                <div class="px-3 pb-2 text-[11px] font-bold uppercase tracking-wider text-slate-400">Menu Utama</div>
                
                <a href="{{ route('dashboard') }}" 
                   class="flex items-center gap-3.5 px-3.5 py-3 rounded-xl transition-all duration-200 text-sm font-medium {{ request()->routeIs('dashboard') ? 'sidebar-active' : 'text-slate-300 hover:bg-white/10 hover:text-white' }}">
                    <i class="fa-solid fa-chart-pie text-base w-5 text-center"></i>
                    <span>Dashboard Utama</span>
                </a>

                <a href="{{ route('pembudidaya') }}" 
                   class="flex items-center gap-3.5 px-3.5 py-3 rounded-xl transition-all duration-200 text-sm font-medium {{ request()->routeIs('pembudidaya') ? 'sidebar-active' : 'text-slate-300 hover:bg-white/10 hover:text-white' }}">
                    <i class="fa-solid fa-water text-base w-5 text-center"></i>
                    <span>Manajemen Kolam</span>
                </a>

                <a href="{{ route('log-pakan') }}" 
                   class="flex items-center gap-3.5 px-3.5 py-3 rounded-xl transition-all duration-200 text-sm font-medium {{ request()->routeIs('log-pakan') ? 'sidebar-active' : 'text-slate-300 hover:bg-white/10 hover:text-white' }}">
                    <i class="fa-solid fa-clipboard-list text-base w-5 text-center"></i>
                    <span>Log Pakan Harian</span>
                </a>

                <a href="{{ route('distribusi') }}" 
                   class="flex items-center gap-3.5 px-3.5 py-3 rounded-xl transition-all duration-200 text-sm font-medium {{ request()->routeIs('distribusi') ? 'sidebar-active' : 'text-slate-300 hover:bg-white/10 hover:text-white' }}">
                    <i class="fa-solid fa-truck-ramp-box text-base w-5 text-center"></i>
                    <span>Distribusi & Order</span>
                </a>

                <div class="px-3 pt-6 pb-2 text-[11px] font-bold uppercase tracking-wider text-slate-400">Keuangan & Admin</div>

                <a href="{{ route('keuangan') }}" 
                   class="flex items-center gap-3.5 px-3.5 py-3 rounded-xl transition-all duration-200 text-sm font-medium {{ request()->routeIs('keuangan') ? 'sidebar-active' : 'text-slate-300 hover:bg-white/10 hover:text-white' }}">
                    <i class="fa-solid fa-wallet text-base w-5 text-center"></i>
                    <span>Financial Management</span>
                </a>

                <a href="{{ route('mitra') }}" 
                   class="flex items-center gap-3.5 px-3.5 py-3 rounded-xl transition-all duration-200 text-sm font-medium {{ request()->routeIs('mitra') ? 'sidebar-active' : 'text-slate-300 hover:bg-white/10 hover:text-white' }}">
                    <i class="fa-solid fa-users text-base w-5 text-center"></i>
                    <span>Manajemen Mitra</span>
                </a>
            </nav>

            <!-- User Footer Profile Card -->
            <div class="p-4 border-t border-white/10 bg-navy-900/50">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="relative">
                            <img src="https://images.unsplash.com/photo-1534528741775-53994a69daeb?auto=format&fit=crop&q=80&w=120" 
                                 alt="Profile" 
                                 class="w-10 h-10 rounded-xl object-cover ring-2 ring-sky-400/50">
                            <span class="absolute bottom-0 right-0 w-3 h-3 bg-emerald-500 rounded-full border-2 border-navy-800"></span>
                        </div>
                        <div class="overflow-hidden">
                            <p class="text-sm font-bold text-white truncate">Adi Darmawan</p>
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-semibold bg-sky-500/20 text-sky-300 border border-sky-400/30">
                                <i class="fa-solid fa-user-shield text-[9px]"></i> Manajer
                            </span>
                        </div>
                    </div>
                    <a href="{{ route('login') }}" title="Logout" class="text-slate-400 hover:text-rose-400 p-2 transition-colors">
                        <i class="fa-solid fa-right-from-bracket text-base"></i>
                    </a>
                </div>
            </div>
        </aside>

        <!-- Main Content Area -->
        <div class="flex-1 flex flex-col min-w-0 overflow-hidden">

            <!-- Top Header Bar -->
            <header class="h-20 bg-white border-b border-slate-200/80 px-4 sm:px-6 flex items-center justify-between sticky top-0 z-30 shadow-sm">
                <div class="flex items-center gap-4">
                    <button @click="sidebarOpen = true" class="lg:hidden p-2 text-slate-600 hover:bg-slate-100 rounded-xl transition-colors">
                        <i class="fa-solid fa-bars-staggered text-xl"></i>
                    </button>
                    <!-- Search Input -->
                    <div class="relative hidden sm:block w-64 lg:w-80">
                        <i class="fa-solid fa-magnifying-glass absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></i>
                        <input type="text" 
                               placeholder="Cari kolam, pakan, mitra, transaksi..." 
                               class="w-full pl-10 pr-4 py-2 rounded-xl text-sm bg-slate-100/80 border border-slate-200 focus:outline-none focus:ring-2 focus:ring-sky-500 focus:bg-white transition-all">
                    </div>
                </div>

                <!-- Right Action Icons & User -->
                <div class="flex items-center gap-3 sm:gap-4">
                    <!-- Date badge -->
                    <div class="hidden md:flex items-center gap-2 px-3 py-1.5 rounded-xl bg-slate-100 border border-slate-200 text-xs font-semibold text-slate-600">
                        <i class="fa-regular fa-calendar text-sky-600"></i>
                        <span>{{ date('d M Y') }}</span>
                    </div>

                    <!-- Quick Notifications -->
                    <button class="relative p-2.5 rounded-xl text-slate-600 hover:bg-slate-100 transition-colors">
                        <i class="fa-regular fa-bell text-lg"></i>
                        <span class="absolute top-2 right-2 w-2.5 h-2.5 bg-rose-500 rounded-full ring-2 ring-white"></span>
                    </button>

                    <!-- Role Badge Indicator -->
                    <div class="flex items-center gap-3 pl-3 border-l border-slate-200">
                        <div class="text-right hidden sm:block">
                            <p class="text-xs font-bold text-slate-800">Manajer Operasional</p>
                            <p class="text-[11px] text-slate-500 font-medium">SIM-BUDIDAYA</p>
                        </div>
                        <div class="w-9 h-9 rounded-xl bg-navy-800 text-white flex items-center justify-center font-bold text-xs shadow-md">
                            MN
                        </div>
                    </div>
                </div>
            </header>

            <!-- Page Content -->
            <main class="flex-1 overflow-y-auto p-4 sm:p-6 lg:p-8">
                @yield('content')
            </main>
        </div>

    </div>

    @stack('scripts')
</body>
</html>

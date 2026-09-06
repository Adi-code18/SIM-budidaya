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
    <!-- Leaflet.js Assets (Local & CDN Fallback) -->
    <link rel="stylesheet" href="{{ asset('leaflet/leaflet.css') }}" />
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <link rel="stylesheet" href="https://unpkg.com/leaflet-control-geocoder/dist/Control.Geocoder.css" />
    <script src="{{ asset('leaflet/leaflet.js') }}"></script>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script src="https://unpkg.com/leaflet-control-geocoder/dist/Control.Geocoder.js"></script>
    <!-- Alpine.js (with Collapse Plugin) -->
    <script defer src="https://cdn.jsdelivr.net/npm/@alpinejs/collapse@3.x.x/dist/cdn.min.js"></script>
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
        [x-cloak] {
            display: none !important;
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
               class="fixed inset-y-0 left-0 z-50 w-64 bg-[#031B4E] text-white flex flex-col transition-transform duration-300 ease-in-out lg:static lg:translate-x-0 shrink-0 shadow-2xl">
            
            <!-- App Branding -->
            <div class="h-20 px-5 flex items-center justify-between border-b border-white/10">
                <a href="{{ route('dashboard') }}" class="flex items-center gap-3">
                    <img src="{{ asset('build/images/Logo aquafarm.png') }}" 
                         alt="Logo Aquafarm" 
                         class="h-9 w-auto object-contain shrink-0 drop-shadow">
                    <div>
                        <span class="font-extrabold text-base tracking-wide text-white block leading-tight">SIM-BUDIDAYA</span>
                        <span class="text-[9px] tracking-[0.16em] uppercase text-sky-300 font-semibold block mt-0.5">AQUAFARM MANAGEMENT</span>
                    </div>
                </a>
                <button @click="sidebarOpen = false" class="lg:hidden text-slate-300 hover:text-white p-1">
                    <i class="fa-solid fa-xmark text-xl"></i>
                </button>
            </div>

            <!-- Navigation Links -->
            <nav class="flex-1 px-3.5 py-6 overflow-y-auto space-y-1.5"
                 x-data="{
                    openMaster: {{ (request()->routeIs('ikan*') || request()->routeIs('petugas*') || request()->routeIs('mitra*')) ? 'true' : 'false' }},
                    openBudidaya: {{ (request()->routeIs('pembibitan*') || request()->routeIs('pembesaran*') || request()->routeIs('pembudidaya*') || request()->routeIs('log-pakan*')) ? 'true' : 'false' }},
                    openKeuangan: {{ request()->routeIs('keuangan*') ? 'true' : 'false' }}
                 }">
                
                <!-- 1. Dashboard -->
                <a href="{{ route('dashboard') }}" 
                   class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl transition-all duration-200 text-xs font-semibold {{ request()->routeIs('dashboard') ? 'bg-[#0284C7] text-white shadow-md shadow-sky-600/30' : 'text-slate-300 hover:bg-white/10 hover:text-white' }}">
                    <i class="fa-solid fa-table-cells-large text-sm w-5 text-center"></i>
                    <span>Dashboard</span>
                </a>

                <!-- 2. Master Data (Accordion) -->
                <div class="space-y-1">
                    <button type="button" 
                            @click="openMaster = !openMaster" 
                            class="w-full flex items-center justify-between px-3.5 py-2.5 rounded-xl transition-all duration-200 text-xs font-semibold cursor-pointer {{ (request()->routeIs('ikan*') || request()->routeIs('petugas*') || request()->routeIs('mitra*')) ? 'text-white bg-white/10' : 'text-slate-300 hover:bg-white/10 hover:text-white' }}">
                        <div class="flex items-center gap-3">
                            <i class="fa-solid fa-database text-sm w-5 text-center text-sky-400"></i>
                            <span>Master Data</span>
                        </div>
                        <i class="fa-solid fa-chevron-down text-[10px] text-slate-400 inline-block transition-transform duration-200" :style="openMaster ? 'transform: rotate(180deg);' : 'transform: rotate(0deg);'"></i>
                    </button>
                    
                    <!-- Submenu Master Data -->
                    <div x-show="openMaster" 
                         x-collapse
                         class="pl-4 pr-1 space-y-1 pt-1 pb-1 ml-4 border-l border-white/15">
                        
                        <a href="{{ route('ikan') }}" 
                           class="flex items-center gap-2.5 px-3 py-2 rounded-lg transition-all duration-200 text-xs font-medium {{ request()->routeIs('ikan*') ? 'bg-[#0284C7] text-white font-bold shadow-xs' : 'text-slate-300 hover:text-white hover:bg-white/5' }}">
                            <i class="fa-solid fa-fish text-xs w-4 text-center"></i>
                            <span>Jenis Ikan</span>
                        </a>

                        <a href="{{ route('petugas') }}" 
                           class="flex items-center gap-2.5 px-3 py-2 rounded-lg transition-all duration-200 text-xs font-medium {{ request()->routeIs('petugas*') ? 'bg-[#0284C7] text-white font-bold shadow-xs' : 'text-slate-300 hover:text-white hover:bg-white/5' }}">
                            <i class="fa-solid fa-users-gear text-xs w-4 text-center"></i>
                            <span>Manajemen Petugas</span>
                        </a>

                        <a href="{{ route('mitra') }}" 
                           class="flex items-center gap-2.5 px-3 py-2 rounded-lg transition-all duration-200 text-xs font-medium {{ request()->routeIs('mitra*') ? 'bg-[#0284C7] text-white font-bold shadow-xs' : 'text-slate-300 hover:text-white hover:bg-white/5' }}">
                            <i class="fa-regular fa-handshake text-xs w-4 text-center"></i>
                            <span>Manajemen Mitra</span>
                        </a>
                    </div>
                </div>

                <!-- 3. Budidaya & Operasional (Accordion) -->
                <div class="space-y-1">
                    <button type="button" 
                            @click="openBudidaya = !openBudidaya" 
                            class="w-full flex items-center justify-between px-3.5 py-2.5 rounded-xl transition-all duration-200 text-xs font-semibold cursor-pointer {{ (request()->routeIs('pembibitan*') || request()->routeIs('pembesaran*') || request()->routeIs('pembudidaya*') || request()->routeIs('log-pakan*')) ? 'text-white bg-white/10' : 'text-slate-300 hover:bg-white/10 hover:text-white' }}">
                        <div class="flex items-center gap-3">
                            <i class="fa-solid fa-water text-sm w-5 text-center text-sky-400"></i>
                            <span>Budidaya & Operasional</span>
                        </div>
                        <i class="fa-solid fa-chevron-down text-[10px] text-slate-400 inline-block transition-transform duration-200" :style="openBudidaya ? 'transform: rotate(180deg);' : 'transform: rotate(0deg);'"></i>
                    </button>
                    
                    <!-- Submenu Budidaya -->
                    <div x-show="openBudidaya" 
                         x-collapse
                         class="pl-4 pr-1 space-y-1 pt-1 pb-1 ml-4 border-l border-white/15">
                        
                        <a href="{{ route('pembibitan') }}" 
                           class="flex items-center gap-2.5 px-3 py-2 rounded-lg transition-all duration-200 text-xs font-medium {{ request()->routeIs('pembibitan*') ? 'bg-[#0284C7] text-white font-bold shadow-xs' : 'text-slate-300 hover:text-white hover:bg-white/5' }}">
                            <i class="fa-solid fa-droplet text-xs w-4 text-center"></i>
                            <span>Pembibitan</span>
                        </a>

                        <a href="{{ route('pembesaran') }}" 
                           class="flex items-center gap-2.5 px-3 py-2 rounded-lg transition-all duration-200 text-xs font-medium {{ (request()->routeIs('pembesaran*') || request()->routeIs('pembudidaya*')) ? 'bg-[#0284C7] text-white font-bold shadow-xs' : 'text-slate-300 hover:text-white hover:bg-white/5' }}">
                            <i class="fa-solid fa-bars-staggered text-xs w-4 text-center"></i>
                            <span>Pembesaran</span>
                        </a>

                        <a href="{{ route('log-pakan') }}" 
                           class="flex items-center gap-2.5 px-3 py-2 rounded-lg transition-all duration-200 text-xs font-medium {{ request()->routeIs('log-pakan*') ? 'bg-[#0284C7] text-white font-bold shadow-xs' : 'text-slate-300 hover:text-white hover:bg-white/5' }}">
                            <i class="fa-regular fa-calendar text-xs w-4 text-center"></i>
                            <span>Log Pakan</span>
                        </a>
                    </div>
                </div>

                <!-- 4. Distribusi & Order (Single) -->
                <a href="{{ route('distribusi') }}" 
                   class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl transition-all duration-200 text-xs font-semibold {{ request()->routeIs('distribusi*') ? 'bg-[#0284C7] text-white shadow-md shadow-sky-600/30' : 'text-slate-300 hover:bg-white/10 hover:text-white' }}">
                    <i class="fa-solid fa-truck text-sm w-5 text-center"></i>
                    <span>Distribusi & Order</span>
                </a>

                <!-- 5. Keuangan (Accordion) -->
                <div class="space-y-1">
                    <button type="button" 
                            @click="openKeuangan = !openKeuangan" 
                            class="w-full flex items-center justify-between px-3.5 py-2.5 rounded-xl transition-all duration-200 text-xs font-semibold cursor-pointer {{ request()->routeIs('keuangan*') ? 'text-white bg-white/10' : 'text-slate-300 hover:bg-white/10 hover:text-white' }}">
                        <div class="flex items-center gap-3">
                            <i class="fa-regular fa-credit-card text-sm w-5 text-center text-sky-400"></i>
                            <span>Keuangan</span>
                        </div>
                        <i class="fa-solid fa-chevron-down text-[10px] text-slate-400 inline-block transition-transform duration-200" :style="openKeuangan ? 'transform: rotate(180deg);' : 'transform: rotate(0deg);'"></i>
                    </button>
                    
                    <!-- Submenu Keuangan -->
                    <div x-show="openKeuangan" 
                         x-collapse
                         class="pl-4 pr-1 space-y-1 pt-1 pb-1 ml-4 border-l border-white/15">
                        
                        <a href="{{ route('keuangan.transaksi') }}" 
                           class="flex items-center gap-2.5 px-3 py-2 rounded-lg transition-all duration-200 text-xs font-medium {{ request()->routeIs('keuangan.transaksi*') ? 'bg-[#0284C7] text-white font-bold shadow-xs' : 'text-slate-300 hover:text-white hover:bg-white/5' }}">
                            <i class="fa-solid fa-receipt text-xs w-4 text-center"></i>
                            <span>Transaksi Keuangan</span>
                        </a>

                        <a href="{{ route('keuangan') }}" 
                           class="flex items-center gap-2.5 px-3 py-2 rounded-lg transition-all duration-200 text-xs font-medium {{ (request()->routeIs('keuangan') && !request()->routeIs('keuangan.transaksi*')) ? 'bg-[#0284C7] text-white font-bold shadow-xs' : 'text-slate-300 hover:text-white hover:bg-white/5' }}">
                            <i class="fa-solid fa-chart-line text-xs w-4 text-center"></i>
                            <span>Laporan & Analisis</span>
                        </a>
                    </div>
                </div>
            </nav>
        </aside>

        <!-- Main Content Area -->
        <div class="flex-1 flex flex-col min-w-0 overflow-hidden bg-[#F4F7FA]">

            <!-- Top Header Bar -->
            <header class="h-16 bg-white border-b border-slate-200/80 px-6 flex items-center justify-between sticky top-0 z-30 shadow-xs">
                <div class="flex items-center gap-4">
                    <button @click="sidebarOpen = true" class="lg:hidden p-2 text-slate-600 hover:bg-slate-100 rounded-xl transition-colors">
                        <i class="fa-solid fa-bars text-lg"></i>
                    </button>
                    <span class="text-xs sm:text-sm font-medium text-slate-600">Selamat Datang, <strong>{{ Auth::user()->nama ?? 'Manajer' }}</strong></span>
                </div>
                
                <!-- Profile Dropdown (Top-Right) -->
                <div class="relative" x-data="{ open: false }">
                    <button @click="open = !open" 
                            type="button" 
                            class="flex items-center gap-2.5 pl-3 pr-2.5 py-1.5 rounded-full hover:bg-slate-100 border border-transparent hover:border-slate-200 transition-all focus:outline-none cursor-pointer group">
                        <span class="text-xs font-bold text-slate-700 hidden sm:inline group-hover:text-sky-700 transition-colors">{{ Auth::user()->nama ?? 'Manajer' }}</span>
                        <img src="{{ Auth::user()->foto_profil_url ?? 'https://ui-avatars.com/api/?name=Manajer&background=0B2570&color=ffffff' }}" 
                             alt="{{ Auth::user()->nama ?? 'Manajer' }}" 
                             class="w-8 h-8 rounded-full object-cover border border-slate-200 shadow-2xs group-hover:ring-2 group-hover:ring-sky-500/30 transition-all">
                        <i class="fa-solid fa-chevron-down text-[10px] text-slate-400 group-hover:text-slate-600 inline-block transition-transform duration-200" :style="open ? 'transform: rotate(180deg);' : 'transform: rotate(0deg);'"></i>
                    </button>

                    <!-- Dropdown Menu -->
                    <div x-show="open" 
                         @click.outside="open = false"
                         x-transition:enter="transition ease-out duration-150"
                         x-transition:enter-start="opacity-0 scale-95 -translate-y-1"
                         x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                         x-transition:leave="transition ease-in duration-100"
                         x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                         x-transition:leave-end="opacity-0 scale-95 -translate-y-1"
                         x-cloak
                         class="absolute right-0 mt-2 w-56 bg-white rounded-2xl shadow-xl border border-slate-100 py-1.5 z-50 divide-y divide-slate-100">
                        
                        <!-- User Info Header -->
                        <div class="px-4 py-3 bg-slate-50/70">
                            <p class="text-xs font-bold text-slate-800 truncate">{{ Auth::user()->nama ?? 'Manajer' }}</p>
                            <p class="text-[11px] text-slate-500 truncate font-medium mt-0.5">{{ Auth::user()->email ?? '-' }}</p>
                            <span class="inline-block mt-1.5 px-2 py-0.5 text-[9px] font-extrabold uppercase tracking-wider rounded-md bg-sky-100 text-sky-700">
                                {{ Auth::user()->role ?? 'Manajer' }}
                            </span>
                        </div>

                        <!-- Menu Options -->
                        <div class="py-1">
                            <a href="{{ route('pengaturan') }}" 
                               @click="open = false"
                               class="flex items-center gap-2.5 px-4 py-2.5 text-xs font-semibold text-slate-700 hover:bg-sky-50 hover:text-sky-700 transition-colors">
                                <i class="fa-solid fa-gear text-slate-400 group-hover:text-sky-600 w-4 text-center"></i>
                                <span>Pengaturan & Profil</span>
                            </a>
                        </div>

                        <!-- Logout Option -->
                        <div class="py-1">
                            <form action="{{ route('logout') }}" method="POST">
                                @csrf
                                <button type="submit" 
                                        class="w-full flex items-center gap-2.5 px-4 py-2.5 text-xs font-semibold text-red-600 hover:bg-red-50 hover:text-red-700 transition-colors cursor-pointer text-left">
                                    <i class="fa-solid fa-right-from-bracket text-red-500 w-4 text-center"></i>
                                    <span>Keluar (Logout)</span>
                                </button>
                            </form>
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

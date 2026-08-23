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
    <!-- Leaflet.js Maps -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
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
               class="fixed inset-y-0 left-0 z-50 w-64 bg-[#031B4E] text-white flex flex-col transition-transform duration-300 ease-in-out lg:static lg:translate-x-0 shrink-0 shadow-2xl">
            
            <!-- App Branding -->
            <div class="h-20 px-6 flex items-center justify-between border-b border-white/10">
                <a href="{{ route('dashboard') }}" class="block">
                    <span class="font-extrabold text-xl tracking-wide text-white block leading-none">SIM-BUDIDAYA</span>
                    <span class="text-[9px] tracking-[0.18em] uppercase text-sky-300 font-semibold block mt-1">AQUAFARM MANAGEMENT</span>
                </a>
                <button @click="sidebarOpen = false" class="lg:hidden text-slate-300 hover:text-white p-1">
                    <i class="fa-solid fa-xmark text-xl"></i>
                </button>
            </div>

            <!-- Navigation Links -->
            <nav class="flex-1 px-4 py-6 overflow-y-auto space-y-1.5">
                
                <a href="{{ route('dashboard') }}" 
                   class="flex items-center gap-3.5 px-4 py-2.5 rounded-xl transition-all duration-200 text-xs font-semibold {{ request()->routeIs('dashboard') ? 'bg-[#0284C7] text-white shadow-md shadow-sky-600/30' : 'text-slate-300 hover:bg-white/10 hover:text-white' }}">
                    <i class="fa-solid fa-table-cells-large text-sm w-5 text-center"></i>
                    <span>Dashboard</span>
                </a>

                <a href="{{ route('pembibitan') }}" 
                   class="flex items-center gap-3.5 px-4 py-2.5 rounded-xl transition-all duration-200 text-xs font-semibold {{ request()->routeIs('pembibitan') ? 'bg-[#0284C7] text-white shadow-md shadow-sky-600/30' : 'text-slate-300 hover:bg-white/10 hover:text-white' }}">
                    <i class="fa-solid fa-droplet text-sm w-5 text-center"></i>
                    <span>Pembibitan</span>
                </a>

                <a href="{{ route('pembesaran') }}" 
                   class="flex items-center gap-3.5 px-4 py-2.5 rounded-xl transition-all duration-200 text-xs font-semibold {{ (request()->routeIs('pembesaran*') || request()->routeIs('pembudidaya*')) ? 'bg-[#0284C7] text-white shadow-md shadow-sky-600/30' : 'text-slate-300 hover:bg-white/10 hover:text-white' }}">
                    <i class="fa-solid fa-bars-staggered text-sm w-5 text-center"></i>
                    <span>Pembesaran</span>
                </a>

                <a href="{{ route('log-pakan') }}" 
                   class="flex items-center gap-3.5 px-4 py-2.5 rounded-xl transition-all duration-200 text-xs font-semibold {{ request()->routeIs('log-pakan') ? 'bg-[#0284C7] text-white shadow-md shadow-sky-600/30' : 'text-slate-300 hover:bg-white/10 hover:text-white' }}">
                    <i class="fa-regular fa-calendar text-sm w-5 text-center"></i>
                    <span>Log Pakan</span>
                </a>

                <a href="{{ route('distribusi') }}" 
                   class="flex items-center gap-3.5 px-4 py-2.5 rounded-xl transition-all duration-200 text-xs font-semibold {{ request()->routeIs('distribusi') ? 'bg-[#0284C7] text-white shadow-md shadow-sky-600/30' : 'text-slate-300 hover:bg-white/10 hover:text-white' }}">
                    <i class="fa-solid fa-truck text-sm w-5 text-center"></i>
                    <span>Distribusi & Order</span>
                </a>

                <a href="{{ route('keuangan') }}" 
                   class="flex items-center gap-3.5 px-4 py-2.5 rounded-xl transition-all duration-200 text-xs font-semibold {{ request()->routeIs('keuangan') ? 'bg-[#0284C7] text-white shadow-md shadow-sky-600/30' : 'text-slate-300 hover:bg-white/10 hover:text-white' }}">
                    <i class="fa-regular fa-credit-card text-sm w-5 text-center"></i>
                    <span>Keuangan</span>
                </a>

                <a href="{{ route('petugas') }}" 
                   class="flex items-center gap-3.5 px-4 py-2.5 rounded-xl transition-all duration-200 text-xs font-semibold {{ request()->routeIs('petugas*') ? 'bg-[#0284C7] text-white shadow-md shadow-sky-600/30' : 'text-slate-300 hover:bg-white/10 hover:text-white' }}">
                    <i class="fa-solid fa-users-gear text-sm w-5 text-center"></i>
                    <span>Manajemen Petugas</span>
                </a>

                <a href="{{ route('mitra') }}" 
                   class="flex items-center gap-3.5 px-4 py-2.5 rounded-xl transition-all duration-200 text-xs font-semibold {{ request()->routeIs('mitra') ? 'bg-[#0284C7] text-white shadow-md shadow-sky-600/30' : 'text-slate-300 hover:bg-white/10 hover:text-white' }}">
                    <i class="fa-regular fa-handshake text-sm w-5 text-center"></i>
                    <span>Manajemen Mitra</span>
                </a>
            </nav>

            <!-- User Profile Button Footer -->
            <div class="p-4 border-t border-white/10 flex items-center justify-between gap-2">
                <div class="flex items-center gap-2.5 px-3 py-1.5 rounded-full bg-white/10 text-white">
                    <div class="w-6 h-6 rounded-full bg-white/20 flex items-center justify-center text-xs">
                        <i class="fa-solid fa-user"></i>
                    </div>
                    <span class="text-xs font-bold pr-1">Manajer</span>
                </div>
                <a href="{{ route('login') }}" class="px-3 py-1.5 rounded-lg bg-[#E53E3E] hover:bg-red-600 text-white font-bold text-xs flex items-center gap-1.5 shadow-sm transition-all">
                    <i class="fa-solid fa-right-from-bracket text-xs"></i>
                    <span>Log Out</span>
                </a>
            </div>
        </aside>

        <!-- Main Content Area -->
        <div class="flex-1 flex flex-col min-w-0 overflow-hidden bg-[#F4F7FA]">

            <!-- Top Header Bar -->
            <header class="h-16 bg-white border-b border-slate-200/80 px-6 flex items-center justify-between sticky top-0 z-30 shadow-xs">
                <div class="flex items-center gap-4">
                    <button @click="sidebarOpen = true" class="lg:hidden p-2 text-slate-600 hover:bg-slate-100 rounded-xl transition-colors">
                        <i class="fa-solid fa-bars text-lg"></i>
                    </button>
                    <span class="text-xs sm:text-sm font-medium text-slate-600">Selamat Datang, Adi Darmawan</span>
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

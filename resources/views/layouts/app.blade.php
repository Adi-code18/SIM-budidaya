<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'AMS BUDIDAYA - Management System')</title>

    {{-- =========================================================================
        1. DEPENDENCY ASSETS & EXTERNAL LIBRARIES
        - Google Fonts (Plus Jakarta Sans): Tipografi modern aplikasi.
        - Font Awesome 6: Icon set lengkap untuk navigasi, tombol, dan indikator.
        - Chart.js: Library visualisasi data grafik & analitik budidaya.
        - Leaflet.js & OpenStreetMap: Library pemetaan GIS interaktif & geocoding.
        - Alpine.js 3: Framework JS reaktif ringan (pengganti Vue/React sederhana).
        - SweetAlert2: Modal popup notifikasi, konfirmasi aksi, & pengganti alert browser.
        - Tailwind CSS: Utility-first CSS framework untuk styling responsif & modern.
    ========================================================================= --}}

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Chart.js (Visualisasi Statistik & Grafik) -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <!-- Leaflet.js Assets (Pemetaan GIS & Geocoding Lokasi Mitra/Kolam) -->
    <link rel="stylesheet" href="{{ asset('leaflet/leaflet.css') }}" />
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <link rel="stylesheet" href="https://unpkg.com/leaflet-control-geocoder/dist/Control.Geocoder.css" />
    <script src="{{ asset('leaflet/leaflet.js') }}"></script>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script src="https://unpkg.com/leaflet-control-geocoder/dist/Control.Geocoder.js"></script>
    
    <!-- Alpine.js (State Management Reaktif di Frontend) -->
    <script defer src="https://cdn.jsdelivr.net/npm/@alpinejs/collapse@3.x.x/dist/cdn.min.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    {{-- SweetAlert2 (Modal Dialog & Toast Notifikasi Elegan) --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        .swal2-popup {
            font-family: 'Plus Jakarta Sans', sans-serif !important;
            border-radius: 1.25rem !important;
            padding: 1.5rem !important;
            box-shadow: 0 25px 50px -12px rgba(11, 25, 44, 0.25) !important;
            border: 1px solid rgba(226, 232, 240, 0.8) !important;
        }
        .swal2-title {
            font-size: 1.15rem !important;
            font-weight: 800 !important;
            color: #0F2C59 !important;
            letter-spacing: -0.02em !important;
        }
        .swal2-html-container {
            font-size: 0.875rem !important;
            color: #475569 !important;
            font-weight: 500 !important;
            line-height: 1.5 !important;
            margin-top: 0.5rem !important;
        }
        .swal2-confirm {
            background: linear-gradient(135deg, #0284C7 0%, #0369A1 100%) !important;
            border-radius: 0.75rem !important;
            font-size: 0.8125rem !important;
            font-weight: 700 !important;
            padding: 0.65rem 1.5rem !important;
            box-shadow: 0 4px 14px rgba(2, 132, 199, 0.35) !important;
            transition: all 0.2s ease !important;
        }
        .swal2-confirm:hover {
            transform: translateY(-1px) !important;
            box-shadow: 0 6px 20px rgba(2, 132, 199, 0.45) !important;
        }
        .swal2-cancel {
            background-color: #f1f5f9 !important;
            color: #64748b !important;
            border-radius: 0.75rem !important;
            font-size: 0.8125rem !important;
            font-weight: 700 !important;
            padding: 0.65rem 1.5rem !important;
            transition: all 0.2s ease !important;
        }
        .swal2-cancel:hover {
            background-color: #e2e8f0 !important;
            color: #334155 !important;
        }
        .swal2-toast {
            border-radius: 1rem !important;
            padding: 0.875rem 1.25rem !important;
            box-shadow: 0 15px 30px -5px rgba(11, 25, 44, 0.2) !important;
            font-family: 'Plus Jakarta Sans', sans-serif !important;
        }
        .swal2-toast .swal2-title {
            font-size: 0.875rem !important;
            font-weight: 700 !important;
        }
    </style>
    <script>
        /**
         * Global SweetAlert2 & Toast System
         * Menyediakan notifikasi popup, toast modern, konfirmasi aksi, dan auto-interceptor alert()
         */
        const Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true,
            didOpen: (toast) => {
                toast.addEventListener('mouseenter', Swal.stopTimer);
                toast.addEventListener('mouseleave', Swal.resumeTimer);
            }
        });

        window.triggerToast = function(message, type = 'success') {
            if (window.Swal) {
                Toast.fire({
                    icon: type,
                    title: message
                });
            } else {
                console.log(`[Toast ${type}]: ${message}`);
            }
        };

        window.AppSwal = {
            toast(message, type = 'success') {
                return window.triggerToast(message, type);
            },
            success(title, text) {
                if (window.Swal) {
                    return Swal.fire({
                        icon: 'success',
                        title: title || 'Berhasil!',
                        text: text,
                        confirmButtonText: 'Selesai'
                    });
                }
                alert((title ? title + '\n' : '') + text);
                return Promise.resolve({ isConfirmed: true });
            },
            error(title, text) {
                if (window.Swal) {
                    return Swal.fire({
                        icon: 'error',
                        title: title || 'Terjadi Kesalahan',
                        text: text,
                        confirmButtonText: 'Tutup'
                    });
                }
                alert((title ? title + '\n' : '') + text);
                return Promise.resolve({ isConfirmed: true });
            },
            warning(title, text) {
                if (window.Swal) {
                    return Swal.fire({
                        icon: 'warning',
                        title: title || 'Perhatian',
                        text: text,
                        confirmButtonText: 'Mengerti'
                    });
                }
                alert((title ? title + '\n' : '') + text);
                return Promise.resolve({ isConfirmed: true });
            },
            confirm({ title, text, confirmText = 'Ya, Lanjutkan', cancelText = 'Batal', icon = 'question', confirmColor }) {
                if (window.Swal) {
                    return Swal.fire({
                        title: title || 'Konfirmasi',
                        text: text,
                        icon: icon,
                        showCancelButton: true,
                        confirmButtonText: confirmText,
                        cancelButtonText: cancelText,
                        reverseButtons: true
                    });
                }
                const res = confirm((title ? title + '\n' : '') + text);
                return Promise.resolve({ isConfirmed: res });
            }
        };

        // Intercept native browser alert -> Mengubah alert browser default menjadi modern SweetAlert2/Toast
        const _nativeAlert = window.alert;
        window.alert = function(message) {
            if (!window.Swal) {
                if (_nativeAlert) _nativeAlert(message);
                return;
            }
            const msgLower = (message || '').toString().toLowerCase();
            let iconType = 'info';
            let titleText = 'Pemberitahuan';

            if (msgLower.includes('berhasil') || msgLower.includes('sukses') || msgLower.includes('terpotong') || msgLower.includes('disimpan')) {
                iconType = 'success';
                titleText = 'Berhasil!';
                Toast.fire({ icon: 'success', title: message });
                return;
            } else if (msgLower.includes('gagal') || msgLower.includes('kesalahan') || msgLower.includes('error') || msgLower.includes('rusak')) {
                iconType = 'error';
                titleText = 'Gagal!';
            } else if (msgLower.includes('perhatian') || msgLower.includes('wajib') || msgLower.includes('harus') || msgLower.includes('tidak boleh') || msgLower.includes('pilih') || msgLower.includes('silakan')) {
                iconType = 'warning';
                titleText = 'Perhatian';
            }

            Swal.fire({
                title: titleText,
                text: message,
                icon: iconType,
                confirmButtonText: 'Mengerti'
            });
        };
    </script>
    
    <!-- Tailwind CSS (Konfigurasi Palette & Custom Theme) -->
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
    
    {{-- Laravel Vite Compilation (CSS & JS Utama) --}}
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
        /* Custom Scrollbar Styling */
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

    {{-- =========================================================================
        2. STRUKTUR UTAMA APLIKASI (APP SHELL)
        Terdiri dari:
        A. Backdrop Mobile (Penutup layar saat sidebar mobile terbuka)
        B. Sidebar Navigasi (Menu utama, accordion submenu, status route aktif)
        C. Main Content Container (Header navbar atas + Area konten halaman @yield)
    ========================================================================= --}}
    <div class="flex h-screen overflow-hidden">
        
        {{-- A. Mobile Sidebar Backdrop --}}
        <div x-show="sidebarOpen" 
             x-transition:enter="transition-opacity ease-linear duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition-opacity ease-linear duration-300"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             @click="sidebarOpen = false"
             class="fixed inset-0 z-40 bg-slate-900/60 backdrop-blur-sm lg:hidden"></div>

        {{-- B. Sidebar Navigasi (Menu Samping) --}}
        <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'" 
               class="fixed inset-y-0 left-0 z-50 w-64 bg-[#031B4E] text-white flex flex-col transition-transform duration-300 ease-in-out lg:static lg:translate-x-0 shrink-0 shadow-2xl">
            
            {{-- Branding / Logo Header Sidebar --}}
            <div class="h-20 px-5 flex items-center justify-between border-b border-white/10">
                <a href="{{ route('dashboard') }}" class="flex items-center gap-3">
                    <img src="{{ asset('images/Logo aquafarm.png') }}" 
                         alt="Logo Aquafarm" 
                         class="h-9 w-auto object-contain shrink-0 drop-shadow">
                    <div>
                        <span class="font-extrabold text-base tracking-wide text-white block leading-tight">AMS BUDIDAYA</span>
                        <span class="text-[9px] tracking-[0.16em] uppercase text-sky-300 font-semibold block mt-0.5">AQUAFARM MANAGEMENT</span>
                    </div>
                </a>
                <button @click="sidebarOpen = false" class="lg:hidden text-slate-300 hover:text-white p-1">
                    <i class="fa-solid fa-xmark text-xl"></i>
                </button>
            </div>

            {{-- Menu Link & Accordion Group Navigasi --}}
            <nav class="flex-1 px-3.5 py-6 overflow-y-auto space-y-1.5"
                 x-data="{
                    openMaster: {{ (request()->routeIs('ikan*') || request()->routeIs('petugas*') || request()->routeIs('mitra*') || request()->routeIs('stok-pakan*')) ? 'true' : 'false' }},
                    openBudidaya: {{ (request()->routeIs('pembibitan*') || request()->routeIs('pembesaran*') || request()->routeIs('pembudidaya*') || request()->routeIs('log-pakan*')) ? 'true' : 'false' }},
                    openKeuangan: {{ request()->routeIs('keuangan*') ? 'true' : 'false' }}
                 }">
                
                {{-- 1. Dashboard --}}
                <a href="{{ route('dashboard') }}" 
                   class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl transition-all duration-200 text-xs font-semibold {{ request()->routeIs('dashboard') ? 'bg-[#0284C7] text-white shadow-md shadow-sky-600/30' : 'text-slate-300 hover:bg-white/10 hover:text-white' }}">
                    <i class="fa-solid fa-table-cells-large text-sm w-5 text-center"></i>
                    <span>Dashboard</span>
                </a>

                {{-- 2. Master Data (Accordion) --}}
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
                    
                    {{-- Submenu Master Data --}}
                    <div x-show="openMaster" 
                         x-collapse
                         class="pl-4 pr-1 space-y-1 pt-1 pb-1 ml-4 border-l border-white/15">
                        
                        <a href="{{ route('stok-pakan') }}" 
                           class="flex items-center gap-2.5 px-3 py-2 rounded-lg transition-all duration-200 text-xs font-medium {{ request()->routeIs('stok-pakan*') ? 'bg-[#0284C7] text-white font-bold shadow-xs' : 'text-slate-300 hover:text-white hover:bg-white/5' }}">
                            <i class="fa-solid fa-boxes-stacked text-xs w-4 text-center"></i>
                            <span>Master Stok Pakan</span>
                        </a>

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

                {{-- 3. Budidaya & Operasional (Accordion) --}}
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
                    
                    {{-- Submenu Budidaya --}}
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

                {{-- 4. Distribusi & Order (Single) --}}
                <a href="{{ route('distribusi') }}" 
                   class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl transition-all duration-200 text-xs font-semibold {{ request()->routeIs('distribusi*') ? 'bg-[#0284C7] text-white shadow-md shadow-sky-600/30' : 'text-slate-300 hover:bg-white/10 hover:text-white' }}">
                    <i class="fa-solid fa-truck text-sm w-5 text-center"></i>
                    <span>Distribusi & Order</span>
                </a>

                {{-- 5. Keuangan (Accordion) --}}
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
                    
                    {{-- Submenu Keuangan --}}
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

        {{-- C. Main Content Area (Header Navbar + Dynamic Content) --}}
        <div class="flex-1 flex flex-col min-w-0 overflow-hidden bg-[#F4F7FA]">

            {{-- 1. Top Header Navbar --}}
            <header class="h-16 bg-white border-b border-slate-200/80 px-6 flex items-center justify-between sticky top-0 z-30 shadow-xs">
                {{-- Sisi Kiri Header: Hamburger Mobile Button & Greeting User --}}
                <div class="flex items-center gap-4">
                    <button @click="sidebarOpen = true" class="lg:hidden p-2 text-slate-600 hover:bg-slate-100 rounded-xl transition-colors" title="Buka Menu">
                        <i class="fa-solid fa-bars text-lg"></i>
                    </button>
                    <span class="text-xs sm:text-sm font-medium text-slate-600">Selamat Datang, <strong>{{ Auth::user()->nama ?? 'Manajer' }}</strong></span>
                </div>
                
                {{-- Sisi Kanan Header: Profile & Account Dropdown Menu (Alpine.js) --}}
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

                    {{-- Dropdown Content --}}
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
                        
                        {{-- Header User Ringkas --}}
                        <div class="px-4 py-3 bg-slate-50/70">
                            <p class="text-xs font-bold text-slate-800 truncate">{{ Auth::user()->nama ?? 'Manajer' }}</p>
                            <p class="text-[11px] text-slate-500 truncate font-medium mt-0.5">{{ Auth::user()->email ?? '-' }}</p>
                            <span class="inline-block mt-1.5 px-2 py-0.5 text-[9px] font-extrabold uppercase tracking-wider rounded-md bg-sky-100 text-sky-700">
                                {{ Auth::user()->role ?? 'Manajer' }}
                            </span>
                        </div>

                        {{-- Opsi Pengaturan Profil --}}
                        <div class="py-1">
                            <a href="{{ route('pengaturan') }}" 
                               @click="open = false"
                               class="flex items-center gap-2.5 px-4 py-2.5 text-xs font-semibold text-slate-700 hover:bg-sky-50 hover:text-sky-700 transition-colors">
                                <i class="fa-solid fa-gear text-slate-400 group-hover:text-sky-600 w-4 text-center"></i>
                                <span>Pengaturan & Profil</span>
                            </a>
                        </div>

                        {{-- Opsi Logout Form --}}
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

            {{-- 2. Area Konten Utama Halaman (@yield('content')) --}}
            <main class="flex-1 overflow-y-auto p-4 sm:p-6 lg:p-8">
                @yield('content')
            </main>
        </div>

    </div>

    @stack('scripts')
</body>
</html>

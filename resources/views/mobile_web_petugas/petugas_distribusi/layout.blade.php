<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>@yield('title', 'Petugas Distribusi - AMS BUDIDAYA Mobile')</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Font Awesome 6 Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Leaflet JS (for geospatial maps preview if needed) -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    
    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    <!-- SweetAlert2 (Modal Dialog & Toast Notifikasi Elegan) -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
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
                        },
                        brand: {
                            blue: '#0F2C59',
                            accent: '#0284C7',
                            light: '#F8FAFC'
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

        /* Custom scrollbar for mobile elements */
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

        /* Responsive Wrapper for Mobile & Desktop */
        .mobile-screen-wrapper {
            width: 100%;
            min-height: 100vh;
            margin: 0 auto;
            background-color: #f8fafc;
            position: relative;
        }

        @media (min-width: 640px) {
            body {
                background-color: #f1f5f9;
            }
            .mobile-screen-wrapper {
                max-width: 48rem; /* max-w-3xl */
                min-height: 100vh;
                box-shadow: 0 4px 20px -2px rgba(0, 0, 0, 0.05);
                border-left: 1px solid #e2e8f0;
                border-right: 1px solid #e2e8f0;
            }
        }

        .glass-header {
            background: rgba(255, 255, 255, 0.92);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
        }

        .glass-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(8px);
        }

        .nav-tab-active {
            color: #0F2C59;
            font-weight: 700;
        }
        
        .pulse-subtle {
            animation: pulse-border 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
        }

        @keyframes pulse-border {
            0%, 100% { opacity: 1; transform: scale(1); }
            50% { opacity: 0.85; transform: scale(1.02); }
        }

        /* SweetAlert2 Modern Styling */
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

        // Intercept native browser alert -> Mengubah alert default menjadi modern SweetAlert2/Toast
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
    @stack('styles')
</head>
<body x-data="{ 
    searchOpen: false, 
    searchQuery: '',
    toastShow: false,
    toastMessage: '',
    toastType: 'success'
}">

    <div class="mobile-screen-wrapper flex flex-col justify-between">
        
        <!-- Header (Show on layout views unless explicitly disabled) -->
        @hasSection('hide_header')
        @else
        <header class="sticky top-0 z-40 glass-header border-b border-slate-100 px-4 py-3 flex items-center justify-between shadow-xs">
            <div class="flex items-center gap-3">
                <a href="{{ route('mobile.petugas.pengiriman') }}" class="flex items-center gap-2">
                    <div class="w-8 h-8 rounded-xl bg-navy-800 flex items-center justify-center text-white shadow-xs">
                        <i class="fa-solid fa-fish-fins text-sm"></i>
                    </div>
                    <div>
                        <span class="font-extrabold text-sm tracking-tight text-navy-900 block leading-none">AMS BUDIDAYA</span>
                        <span class="text-[9px] font-bold text-sky-600 tracking-wider uppercase block mt-0.5">Petugas Distribusi</span>
                    </div>
                </a>
            </div>

            <div class="flex items-center gap-2">
                <button @click="searchOpen = !searchOpen" 
                        class="w-9 h-9 rounded-full bg-slate-100 text-slate-600 flex items-center justify-center hover:bg-slate-200 transition-colors">
                    <i class="fa-solid fa-magnifying-glass text-xs"></i>
                </button>
                <a href="{{ route('mobile.petugas.akun') }}" class="relative w-9 h-9 rounded-full border border-sky-200 overflow-hidden shadow-2xs">
                    <img src="{{ Auth::user()->foto_profil_url ?? 'https://ui-avatars.com/api/?name=Distribusi&background=0284C7&color=ffffff' }}" 
                         alt="{{ Auth::user()->nama ?? 'Petugas' }}" 
                         class="w-full h-full object-cover">
                </a>
            </div>
        </header>

        <!-- Search Bar Modal / Overlay -->
        <div x-show="searchOpen" 
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 -translate-y-2"
             x-transition:enter-end="opacity-100 translate-y-0"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100 translate-y-0"
             x-transition:leave-end="opacity-0 -translate-y-2"
             class="sticky top-14 z-30 bg-white px-4 py-3 border-b border-slate-200 shadow-md">
            <div class="relative">
                <i class="fa-solid fa-magnifying-glass absolute left-3.5 top-3 text-slate-400 text-xs"></i>
                <input type="text" 
                       x-model="searchQuery" 
                       placeholder="Cari ID pengiriman, nama mitra, atau lokasi..." 
                       class="w-full pl-9 pr-8 py-2 bg-slate-100 text-xs font-semibold rounded-xl text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-navy-800">
                <button x-show="searchQuery" @click="searchQuery = ''" class="absolute right-3 top-2.5 text-slate-400 hover:text-slate-600">
                    <i class="fa-solid fa-circle-xmark text-xs"></i>
                </button>
            </div>
        </div>
        @endif

        <!-- Toast Notification Alert -->
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

        <!-- Main Page Content Area -->
        <main class="flex-1 overflow-y-auto pb-20">
            @yield('content')
        </main>

        <!-- Bottom Navigation Bar (Shown unless hide_nav is defined) -->
        @hasSection('hide_nav')
        @else
        <nav class="fixed bottom-0 left-0 right-0 z-40 bg-white/95 backdrop-blur-md border-t border-slate-200/80 px-4 py-2 flex items-center justify-around shadow-lg max-w-full sm:max-w-3xl sm:mx-auto sm:border-x sm:rounded-t-2xl">
            
            <!-- Tab 1: Pengiriman Aktif -->
            <a href="{{ route('mobile.petugas.pengiriman') }}" 
               class="flex flex-col items-center py-1 px-3 rounded-xl transition-all duration-200 {{ request()->routeIs('mobile.petugas.pengiriman*') || request()->routeIs('mobile.petugas.detail*') ? 'text-navy-800 font-bold' : 'text-slate-400 hover:text-slate-600 font-medium' }}">
                <div class="relative">
                    <i class="fa-solid fa-truck-fast text-lg {{ request()->routeIs('mobile.petugas.pengiriman*') || request()->routeIs('mobile.petugas.detail*') ? 'text-navy-800' : 'text-slate-400' }}"></i>
                    <span class="absolute -top-1 -right-2 w-2 h-2 bg-amber-500 rounded-full"></span>
                </div>
                <span class="text-[10px] mt-1 tracking-tight">Pengiriman</span>
            </a>

            <!-- Tab 2: Riwayat -->
            <a href="{{ route('mobile.petugas.riwayat') }}" 
               class="flex flex-col items-center py-1 px-3 rounded-xl transition-all duration-200 {{ request()->routeIs('mobile.petugas.riwayat*') ? 'text-navy-800 font-bold' : 'text-slate-400 hover:text-slate-600 font-medium' }}">
                <i class="fa-solid fa-clock-rotate-left text-lg {{ request()->routeIs('mobile.petugas.riwayat*') ? 'text-navy-800' : 'text-slate-400' }}"></i>
                <span class="text-[10px] mt-1 tracking-tight">Riwayat</span>
            </a>

            <!-- Tab 3: Akun -->
            <a href="{{ route('mobile.petugas.akun') }}" 
               class="flex flex-col items-center py-1 px-3 rounded-xl transition-all duration-200 {{ request()->routeIs('mobile.petugas.akun*') ? 'text-navy-800 font-bold' : 'text-slate-400 hover:text-slate-600 font-medium' }}">
                <i class="fa-solid fa-user-gear text-lg {{ request()->routeIs('mobile.petugas.akun*') ? 'text-navy-800' : 'text-slate-400' }}"></i>
                <span class="text-[10px] mt-1 tracking-tight">Akun</span>
            </a>

        </nav>
        @endif

    </div>

    @stack('scripts')
</body>
</html>

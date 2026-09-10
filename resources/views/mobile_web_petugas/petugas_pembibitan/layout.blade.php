<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>@yield('title', 'Petugas Pembibitan - AMS BUDIDAYA Mobile')</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Font Awesome 6 Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
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
                        aqua: {
                            50: '#f0fdfa',
                            100: '#ccfbf1',
                            500: '#14b8a6',
                            600: '#0d9488',
                            700: '#0f766e',
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
            background: rgba(255, 255, 255, 0.94);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
        }

        .no-scrollbar::-webkit-scrollbar {
            display: none;
        }
        .no-scrollbar {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }

        /* SweetAlert2 Modern Styling */
        .swal2-container {
            z-index: 99999 !important;
        }
        body.swal2-shown:not(.swal2-toast-shown) .swal2-container {
            background: rgba(15, 23, 42, 0.45) !important;
        }
        body.swal2-toast-shown .swal2-container,
        .swal2-container.swal2-top-end,
        .swal2-container.swal2-top,
        .swal2-container.swal2-top-start,
        .swal2-container.swal2-bottom-end,
        .swal2-container.swal2-bottom,
        .swal2-container.swal2-bottom-start {
            background: transparent !important;
            background-color: transparent !important;
            pointer-events: none !important;
        }
        .swal2-toast {
            pointer-events: auto !important;
        }
        .swal2-popup:not(.swal2-toast) {
            font-family: 'Plus Jakarta Sans', sans-serif !important;
            border-radius: 1.25rem !important;
            padding: 1.5rem 1.25rem !important;
            box-shadow: 0 20px 40px -10px rgba(2, 27, 78, 0.25) !important;
            border: 1px solid rgba(226, 232, 240, 0.9) !important;
            background: #ffffff !important;
        }
        .swal2-title {
            font-size: 1.15rem !important;
            font-weight: 800 !important;
            color: #051B44 !important;
            letter-spacing: -0.02em !important;
            margin-top: 0.35rem !important;
        }
        .swal2-html-container {
            font-size: 0.8125rem !important;
            color: #475569 !important;
            font-weight: 500 !important;
            line-height: 1.5 !important;
            margin-top: 0.35rem !important;
        }
        .swal2-confirm {
            background: #051B44 !important;
            color: #ffffff !important;
            border-radius: 0.75rem !important;
            font-size: 0.8125rem !important;
            font-weight: 700 !important;
            padding: 0.65rem 1.25rem !important;
            box-shadow: 0 4px 14px rgba(5, 27, 68, 0.25) !important;
            transition: all 0.2s ease !important;
            border: none !important;
        }
        .swal2-confirm.swal2-danger-btn {
            background: linear-gradient(135deg, #E11D48 0%, #BE123C 100%) !important;
            box-shadow: 0 4px 14px rgba(225, 29, 72, 0.3) !important;
        }
        .swal2-cancel {
            background-color: #f1f5f9 !important;
            color: #64748b !important;
            border-radius: 0.75rem !important;
            font-size: 0.8125rem !important;
            font-weight: 700 !important;
            padding: 0.65rem 1.25rem !important;
            border: 1px solid #e2e8f0 !important;
            transition: all 0.2s ease !important;
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
                        confirmButtonText: 'Selesai',
                        heightAuto: false,
                        customClass: { confirmButton: 'swal2-confirm' }
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
                        confirmButtonText: 'Tutup',
                        heightAuto: false,
                        customClass: { confirmButton: 'swal2-confirm' }
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
                        confirmButtonText: 'Mengerti',
                        heightAuto: false,
                        customClass: { confirmButton: 'swal2-confirm' }
                    });
                }
                alert((title ? title + '\n' : '') + text);
                return Promise.resolve({ isConfirmed: true });
            },
            confirm({ title, text, confirmText = 'Ya, Lanjutkan', cancelText = 'Batal', icon = 'question', isDanger = false }) {
                if (window.Swal) {
                    return Swal.fire({
                        title: title || 'Konfirmasi Tindakan',
                        text: text,
                        icon: icon,
                        showCancelButton: true,
                        confirmButtonText: confirmText,
                        cancelButtonText: cancelText,
                        reverseButtons: true,
                        heightAuto: false,
                        allowOutsideClick: true,
                        allowEscapeKey: true,
                        customClass: {
                            confirmButton: isDanger ? 'swal2-confirm swal2-danger-btn' : 'swal2-confirm',
                            cancelButton: 'swal2-cancel'
                        }
                    });
                }
                const res = confirm((title ? title + '\n' : '') + text);
                return Promise.resolve({ isConfirmed: res });
            },
            confirmDelete({ title = 'Hapus Data?', text = 'Apakah Anda yakin ingin menghapus data ini?', confirmText = 'Ya, Hapus', cancelText = 'Batal' }) {
                return this.confirm({
                    title: title,
                    text: text,
                    icon: 'warning',
                    confirmText: confirmText,
                    cancelText: cancelText,
                    isDanger: true
                });
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
        
        <!-- Header (AQUA-FIELD / AMS BUDIDAYA) -->
        @hasSection('hide_header')
        @else
        <header class="sticky top-0 z-40 glass-header border-b border-slate-100 px-4 py-3 flex items-center justify-between shadow-xs">
            <div class="flex items-center gap-2.5">
                <a href="{{ route('petugas.pembibitan.dashboard') }}" class="flex items-center gap-2">
                    <div class="w-8 h-8 rounded-xl bg-navy-800 flex items-center justify-center text-white shadow-xs">
                        <i class="fa-solid fa-droplet text-xs text-sky-400"></i>
                    </div>
                    <div>
                        <span class="font-extrabold text-xs tracking-tight text-navy-900 block leading-none">AQUA-FIELD</span>
                        <span class="text-[9px] font-bold text-slate-400 tracking-wider uppercase block mt-0.5">Petugas Pembibitan</span>
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
                       placeholder="Cari ID batch, varietas ikan, tank..." 
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

        <!-- Bottom Navigation Bar (Dashboard, Form, Profile) -->
        @hasSection('hide_nav')
        @else
        <nav class="fixed bottom-0 left-0 right-0 z-40 bg-white/95 backdrop-blur-md border-t border-slate-200/80 px-6 py-2 flex items-center justify-around shadow-lg max-w-full sm:max-w-3xl sm:mx-auto sm:border-x sm:rounded-t-2xl">
            
            <!-- Tab 1: Dashboard -->
            <a href="{{ route('petugas.pembibitan.dashboard') }}" 
               class="flex flex-col items-center py-1 px-3 rounded-xl transition-all duration-200 {{ request()->routeIs('petugas.pembibitan.dashboard*') ? 'text-navy-800 font-bold' : 'text-slate-400 hover:text-slate-600 font-medium' }}">
                <i class="fa-solid fa-table-cells-large text-lg {{ request()->routeIs('petugas.pembibitan.dashboard*') ? 'text-navy-800' : 'text-slate-400' }}"></i>
                <span class="text-[10px] mt-1 tracking-tight">Dashboard</span>
            </a>

            <!-- Tab 2: Pakan -->
            <a href="{{ route('petugas.pembibitan.log-pakan') }}" 
               class="flex flex-col items-center py-1 px-3 rounded-xl transition-all duration-200 {{ request()->routeIs('petugas.pembibitan.log-pakan*') || request()->routeIs('petugas.pembibitan.form*') ? 'text-navy-800 font-bold' : 'text-slate-400 hover:text-slate-600 font-medium' }}">
                <div class="relative">
                    <i class="fa-solid fa-bowl-food text-lg {{ request()->routeIs('petugas.pembibitan.log-pakan*') || request()->routeIs('petugas.pembibitan.form*') ? 'text-navy-800' : 'text-slate-400' }}"></i>
                    <span class="absolute -top-1 -right-1.5 w-2 h-2 bg-emerald-500 rounded-full"></span>
                </div>
                <span class="text-[10px] mt-1 tracking-tight">Pakan</span>
            </a>

            <!-- Tab 3: Profile -->
            <a href="{{ route('petugas.pembibitan.akun') }}" 
               class="flex flex-col items-center py-1 px-3 rounded-xl transition-all duration-200 {{ request()->routeIs('petugas.pembibitan.akun*') ? 'text-navy-800 font-bold' : 'text-slate-400 hover:text-slate-600 font-medium' }}">
                <i class="fa-solid fa-user text-lg {{ request()->routeIs('petugas.pembibitan.akun*') ? 'text-navy-800' : 'text-slate-400' }}"></i>
                <span class="text-[10px] mt-1 tracking-tight">Profile</span>
            </a>

        </nav>
        @endif

    </div>

    @stack('scripts')
</body>
</html>

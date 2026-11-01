<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', ($code ?? 'Error') . ' - Sistem Manajemen Budidaya Ikan | Aquafarm Priangan')</title>
    <link rel="icon" type="image/png" href="{{ asset('images/Logo aquafarm.png') }}">

    <!-- Google Fonts (Barlow & Plus Jakarta Sans) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Barlow:ital,wght@0,600;0,700;0,800;0,900;1,700&family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=JetBrains+Mono:wght@400;500;600&display=swap" rel="stylesheet">

    <!-- Font Awesome Icons 6.5.1 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'sans-serif'],
                        heading: ['"Barlow"', 'sans-serif'],
                        mono: ['"JetBrains Mono"', 'monospace'],
                    },
                    colors: {
                        itb: {
                            navy: '#0a1d47',
                            navydeep: '#06122c',
                            bluehead: '#004b93',
                            primary: '#005aab',
                            darkblue: '#003d82',
                            royal: '#0b2565',
                            gold: '#FF8F00',
                            yellow: '#FFC700',
                            golddark: '#E65100',
                            lightgrey: '#f1f3f5',
                            cardgrey: '#f8f9fa'
                        }
                    },
                    animation: {
                        'float-slow': 'float 6s ease-in-out infinite',
                        'float-medium': 'float 4s ease-in-out infinite alternate',
                        'pulse-glow': 'pulseGlow 2.5s cubic-bezier(0.4, 0, 0.6, 1) infinite',
                        'bubble-rise': 'bubbleRise 8s linear infinite',
                        'radar-sweep': 'radarSweep 4s linear infinite',
                    },
                    keyframes: {
                        float: {
                            '0%, 100%': { transform: 'translateY(0px) rotate(0deg)' },
                            '50%': { transform: 'translateY(-12px) rotate(1deg)' },
                        },
                        pulseGlow: {
                            '0%, 100%': { opacity: '0.4', transform: 'scale(1)' },
                            '50%': { opacity: '0.8', transform: 'scale(1.05)' },
                        },
                        radarSweep: {
                            '0%': { transform: 'rotate(0deg)' },
                            '100%': { transform: 'rotate(360deg)' },
                        },
                        bubbleRise: {
                            '0%': { transform: 'translateY(100vh) scale(0.6)', opacity: '0' },
                            '20%': { opacity: '0.4' },
                            '80%': { opacity: '0.3' },
                            '100%': { transform: 'translateY(-10vh) scale(1.1)', opacity: '0' },
                        }
                    }
                }
            }
        }
    </script>

    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <style>
        [x-cloak] { display: none !important; }

        /* Aquatic Ambient Background Pattern */
        .aqua-pattern {
            background-color: #071530;
            background-image: 
                radial-gradient(circle at 15% 20%, rgba(0, 90, 171, 0.28) 0%, transparent 40%),
                radial-gradient(circle at 85% 75%, rgba(255, 143, 0, 0.12) 0%, transparent 45%),
                radial-gradient(circle at 50% 50%, rgba(10, 29, 71, 0.9) 0%, transparent 100%),
                radial-gradient(rgba(255, 255, 255, 0.04) 1px, transparent 1px);
            background-size: 100% 100%, 100% 100%, 100% 100%, 32px 32px;
        }

        .glass-panel {
            background: rgba(13, 27, 62, 0.72);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 255, 255, 0.08);
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5), inset 0 1px 0 rgba(255, 255, 255, 0.1);
        }

        .glass-badge {
            background: rgba(255, 255, 255, 0.06);
            backdrop-filter: blur(8px);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        /* Subtle glowing text for error code */
        .glow-code {
            text-shadow: 0 0 35px rgba(0, 149, 255, 0.35);
        }

        /* Custom Scrollbar */
        ::-webkit-scrollbar {
            width: 6px;
            height: 6px;
        }
        ::-webkit-scrollbar-track {
            background: #06122c;
        }
        ::-webkit-scrollbar-thumb {
            background: #005aab;
            border-radius: 4px;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: #FF8F00;
        }
    </style>
</head>
<body class="aqua-pattern text-slate-200 min-h-screen flex flex-col font-sans selection:bg-[#FF8F00] selection:text-slate-950 relative overflow-x-hidden"
      x-data="{ 
          showDiagnostics: false, 
          showCodeDrawer: false,
          copied: false,
          copyDiagnostics() {
              const text = document.getElementById('diagnostics-data').innerText;
              navigator.clipboard.writeText(text).then(() => {
                  this.copied = true;
                  setTimeout(() => this.copied = false, 2500);
              });
          }
      }">

    {{-- Aquatic Bubbles Ambient Effect --}}
    <div class="fixed inset-0 pointer-events-none z-0 overflow-hidden" aria-hidden="true">
        <div class="absolute w-4 h-4 rounded-full bg-cyan-400/20 blur-[1px] animate-bubble-rise" style="left: 12%; animation-delay: 0s; animation-duration: 9s;"></div>
        <div class="absolute w-6 h-6 rounded-full bg-blue-400/15 blur-[2px] animate-bubble-rise" style="left: 28%; animation-delay: 2s; animation-duration: 11s;"></div>
        <div class="absolute w-3 h-3 rounded-full bg-amber-400/15 blur-[1px] animate-bubble-rise" style="left: 65%; animation-delay: 4s; animation-duration: 8s;"></div>
        <div class="absolute w-5 h-5 rounded-full bg-sky-400/20 blur-[1px] animate-bubble-rise" style="left: 82%; animation-delay: 1.5s; animation-duration: 10s;"></div>
        <div class="absolute w-2 h-2 rounded-full bg-white/20 animate-bubble-rise" style="left: 92%; animation-delay: 5s; animation-duration: 7s;"></div>
    </div>

    {{-- Top Navbar / Header --}}
    <header class="relative z-20 border-b border-white/10 bg-[#06122c]/75 backdrop-blur-md">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-20 flex items-center justify-between">
            {{-- Brand Logo --}}
            <a href="{{ route('landing') ?? url('/') }}" class="flex items-center gap-3.5 group transition-transform duration-200 hover:scale-[1.01]">
                <div class="w-11 h-11 rounded-2xl bg-gradient-to-br from-[#005aab] to-[#0a1d47] p-2 flex items-center justify-center border border-white/20 shadow-lg shadow-sky-950/40 group-hover:border-[#FFC700]/50 transition-colors">
                    <img src="{{ asset('images/Logo aquafarm.png') }}" alt="Logo Aquafarm Priangan" class="w-full h-full object-contain filter drop-shadow">
                </div>
                <div>
                    <div class="flex items-center gap-2">
                        <span class="font-heading font-black text-lg tracking-wider text-white uppercase group-hover:text-amber-400 transition-colors">
                            Aquafarm Priangan
                        </span>
                        <span class="hidden sm:inline-flex px-2 py-0.5 text-[10px] font-bold uppercase tracking-widest bg-sky-500/20 text-sky-300 border border-sky-500/30 rounded-full">
                            AMS BUDIDAYA
                        </span>
                    </div>
                    <p class="text-[11px] text-slate-400 font-medium tracking-tight">
                        Integrated Aquaculture & IoT Pond Monitoring
                    </p>
                </div>
            </a>

            {{-- Right Badges & Actions --}}
            <div class="flex items-center gap-3">
                {{-- Server Health Status Pill --}}
                <div class="hidden md:flex items-center gap-2 px-3 py-1.5 rounded-full bg-white/[0.04] border border-white/10 text-xs">
                    <span class="relative flex h-2 w-2">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
                    </span>
                    <span class="text-slate-300 font-medium text-[11px]">Node Cluster Online</span>
                </div>

                {{-- Link to Error Hub Catalog --}}
                <a href="{{ route('errors.index') ?? url('/errors') }}" 
                   class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-xl bg-white/[0.06] hover:bg-white/[0.12] text-xs font-semibold text-sky-200 border border-white/15 transition-all">
                    <i class="fa-solid fa-layer-group text-amber-400 text-xs"></i>
                    <span class="hidden sm:inline">Pusat Kode Error</span>
                    <span class="sm:hidden">Katalog</span>
                </a>

                {{-- Direct Home / Dashboard Link --}}
                @auth
                    <a href="{{ route('dashboard') ?? url('/dashboard') }}" 
                       class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-[#005aab] hover:bg-[#004b93] text-white text-xs font-bold uppercase tracking-wider shadow-md shadow-sky-950/50 transition-all">
                        <i class="fa-solid fa-gauge-high"></i>
                        <span class="hidden sm:inline">Dashboard</span>
                    </a>
                @else
                    <a href="{{ route('login') ?? url('/login') }}" 
                       class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-[#005aab] hover:bg-[#004b93] text-white text-xs font-bold uppercase tracking-wider shadow-md shadow-sky-950/50 transition-all">
                        <i class="fa-solid fa-right-to-bracket"></i>
                        <span>Login</span>
                    </a>
                @endauth
            </div>
        </div>
    </header>

    {{-- Main Error Container --}}
    <main class="relative z-10 flex-1 flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-5xl w-full mx-auto">
            
            {{-- Error Card --}}
            <div class="glass-panel rounded-3xl p-6 sm:p-10 lg:p-12 relative overflow-hidden">
                
                {{-- Decorative background glow --}}
                <div class="absolute -top-32 -right-32 w-96 h-96 bg-sky-500/10 rounded-full blur-3xl pointer-events-none"></div>
                <div class="absolute -bottom-32 -left-32 w-96 h-96 bg-amber-500/10 rounded-full blur-3xl pointer-events-none"></div>

                <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-12 items-center relative z-10">
                    
                    {{-- Left Column: Text & Actions --}}
                    <div class="lg:col-span-7 space-y-6 text-left">
                        
                        {{-- Badge Category & Status Code --}}
                        <div class="flex flex-wrap items-center gap-3">
                            <span class="px-3.5 py-1 rounded-full text-xs font-bold font-mono tracking-wider uppercase border @yield('status_badge_style', 'bg-sky-500/20 text-sky-300 border-sky-400/30')">
                                <i class="@yield('status_icon', 'fa-solid fa-triangle-exclamation') mr-1.5"></i>
                                HTTP @yield('code', $code ?? 'ERROR') • @yield('status_label', 'CLIENT / SERVER ISSUE')
                            </span>
                            <span class="text-xs text-slate-400 font-mono flex items-center gap-1.5">
                                <i class="fa-regular fa-clock text-slate-500"></i>
                                {{ now()->setTimezone('Asia/Jakarta')->format('d M Y, H:i:s') }} WIB
                            </span>
                        </div>

                        {{-- Main Heading & Numeric Glow --}}
                        <div>
                            <div class="font-heading font-black text-6xl sm:text-7xl lg:text-8xl tracking-tight text-white glow-code leading-none flex items-baseline gap-3">
                                <span>@yield('code', $code ?? '500')</span>
                                <span class="text-xs sm:text-sm font-sans font-medium text-slate-400 uppercase tracking-widest">
                                    / @yield('code_name', 'System Exception')
                                </span>
                            </div>
                            <h1 class="mt-3 font-heading font-extrabold text-2xl sm:text-3xl text-white tracking-wide">
                                @yield('heading', 'Terjadi Gangguan pada Sistem Budidaya')
                            </h1>
                        </div>

                        {{-- Friendly Indonesian Description --}}
                        <p class="text-slate-300 text-sm sm:text-base leading-relaxed font-normal">
                            @yield('description', 'Permintaan Anda saat ini tidak dapat diselesaikan oleh sistem monitoring tambak. Mohon tinjau panduan di bawah atau hubungi administrator.')
                        </p>

                        {{-- Quick Troubleshooting Causes & Solutions --}}
                        <div class="rounded-2xl bg-slate-900/60 border border-white/5 p-4 sm:p-5 space-y-3">
                            <h2 class="text-xs font-bold uppercase tracking-wider text-amber-400 flex items-center gap-2">
                                <i class="fa-solid fa-lightbulb"></i>
                                Rekomendasi Solusi & Tindakan
                            </h2>
                            <ul class="text-xs sm:text-[13px] text-slate-300 space-y-2 list-none">
                                @section('troubleshooting')
                                    <li class="flex items-start gap-2">
                                        <i class="fa-solid fa-circle-check text-sky-400 text-[11px] mt-1 shrink-0"></i>
                                        <span>Periksa kembali alamat tautan atau URL yang dituju.</span>
                                    </li>
                                    <li class="flex items-start gap-2">
                                        <i class="fa-solid fa-circle-check text-sky-400 text-[11px] mt-1 shrink-0"></i>
                                        <span>Segarkan (refresh) browser atau bersihkan cache sesi penjelajahan.</span>
                                    </li>
                                    <li class="flex items-start gap-2">
                                        <i class="fa-solid fa-circle-check text-sky-400 text-[11px] mt-1 shrink-0"></i>
                                        <span>Jika kendala berlanjut, hubungi tim helpdesk sistem via WhatsApp.</span>
                                    </li>
                                @show
                            </ul>
                        </div>

                        {{-- Action Buttons --}}
                        <div class="pt-2 flex flex-wrap items-center gap-3">
                            {{-- Primary Action: Back to Home / Dashboard --}}
                            @auth
                                <a href="{{ route('dashboard') ?? url('/dashboard') }}" 
                                   class="inline-flex items-center justify-center gap-2 px-5 py-3 rounded-xl bg-gradient-to-r from-[#005aab] to-[#004b93] hover:from-[#004b93] hover:to-[#0a1d47] text-white font-extrabold text-xs sm:text-sm uppercase tracking-wider shadow-lg shadow-sky-950/60 border border-sky-400/30 transition-all hover:scale-[1.02] active:scale-95">
                                    <i class="fa-solid fa-gauge-high"></i>
                                    <span>Kembali ke Dashboard</span>
                                </a>
                            @else
                                <a href="{{ route('landing') ?? url('/') }}" 
                                   class="inline-flex items-center justify-center gap-2 px-5 py-3 rounded-xl bg-gradient-to-r from-[#005aab] to-[#004b93] hover:from-[#004b93] hover:to-[#0a1d47] text-white font-extrabold text-xs sm:text-sm uppercase tracking-wider shadow-lg shadow-sky-950/60 border border-sky-400/30 transition-all hover:scale-[1.02] active:scale-95">
                                    <i class="fa-solid fa-house"></i>
                                    <span>Kembali ke Beranda</span>
                                </a>
                            @endauth

                            {{-- Secondary Action: Reload --}}
                            <button onclick="window.location.reload()" 
                                    type="button"
                                    class="inline-flex items-center justify-center gap-2 px-4 py-3 rounded-xl bg-white/[0.08] hover:bg-white/[0.15] text-white font-bold text-xs sm:text-sm border border-white/10 transition-all hover:border-white/20 active:scale-95">
                                <i class="fa-solid fa-rotate-right"></i>
                                <span>Muat Ulang</span>
                            </button>

                            {{-- Secondary Action: Go Back --}}
                            <button onclick="window.history.length > 1 ? window.history.back() : window.location.href='{{ url('/') }}'" 
                                    type="button"
                                    class="inline-flex items-center justify-center gap-2 px-4 py-3 rounded-xl bg-white/[0.04] hover:bg-white/[0.10] text-slate-300 font-bold text-xs sm:text-sm border border-white/5 transition-all active:scale-95">
                                <i class="fa-solid fa-arrow-left"></i>
                                <span>Halaman Sebelumnya</span>
                            </button>

                            {{-- WhatsApp Support Link --}}
                            @php
                                $waPhone = '6281234567890';
                                $currentUrl = request()->fullUrl();
                                $errCode = View::getSection('code', $code ?? '500');
                                $waMsg = "Halo Tim Helpdesk Aquafarm Priangan, saya mengalami kendala teknis pada sistem AMS Budidaya:\n\n• Kode Status: HTTP {$errCode}\n• Waktu: " . now()->setTimezone('Asia/Jakarta')->format('d/m/Y H:i') . " WIB\n• Halaman: {$currentUrl}\n\nMohon bantuannya untuk pengecekan. Terima kasih.";
                            @endphp
                            <a href="https://wa.me/{{ $waPhone }}?text={{ urlencode($waMsg) }}" 
                               target="_blank"
                               class="inline-flex items-center justify-center gap-2 px-4 py-3 rounded-xl bg-emerald-600/20 hover:bg-emerald-600/30 text-emerald-300 font-bold text-xs sm:text-sm border border-emerald-500/30 transition-all active:scale-95">
                                <i class="fa-brands fa-whatsapp text-emerald-400 text-base"></i>
                                <span>Bantuan Support</span>
                            </a>
                        </div>

                    </div>

                    {{-- Right Column: Interactive Thematic Graphic / Illustration --}}
                    <div class="lg:col-span-5 flex flex-col items-center justify-center relative">
                        <div class="w-full max-w-sm sm:max-w-md aspect-square rounded-3xl bg-gradient-to-b from-sky-950/40 via-[#0a1d47]/60 to-[#06122c]/80 border border-white/10 p-6 flex items-center justify-center relative shadow-2xl shadow-black/60 overflow-hidden">
                            
                            {{-- Water ripple radar rings --}}
                            <div class="absolute inset-0 flex items-center justify-center pointer-events-none opacity-25">
                                <div class="w-48 h-48 rounded-full border border-sky-400 animate-ping" style="animation-duration: 4s;"></div>
                                <div class="w-72 h-72 rounded-full border border-sky-400/50 animate-ping" style="animation-duration: 6s; animation-delay: 1.5s;"></div>
                            </div>

                            {{-- Code-Specific Illustration Slot --}}
                            <div class="relative z-10 w-full h-full flex items-center justify-center animate-float-slow">
                                @yield('illustration')
                            </div>

                            {{-- Floating Aquaculture Sensor Tag --}}
                            <div class="absolute bottom-4 left-4 right-4 bg-slate-950/80 backdrop-blur-md rounded-xl p-2.5 border border-white/10 flex items-center justify-between text-[11px] text-slate-400 font-mono">
                                <span class="flex items-center gap-1.5 text-sky-300">
                                    <i class="fa-solid fa-water text-[10px]"></i>
                                    Telemetry: Active
                                </span>
                                <span>Zone: Buniseuri-01</span>
                            </div>
                        </div>
                    </div>

                </div>

                {{-- Collapsible Technical Diagnostics Bar --}}
                <div class="mt-8 pt-6 border-t border-white/10">
                    <div class="flex flex-wrap items-center justify-between gap-3">
                        <button @click="showDiagnostics = !showDiagnostics" 
                                type="button"
                                class="inline-flex items-center gap-2 text-xs font-mono font-medium text-slate-400 hover:text-sky-300 transition-colors">
                            <i class="fa-solid" :class="showDiagnostics ? 'fa-chevron-down text-sky-400' : 'fa-chevron-right'"></i>
                            <span>Detail Teknis Diagnostik (Audit ID & Trace Telemetry)</span>
                            <span class="px-1.5 py-0.5 rounded text-[10px] bg-white/10 text-slate-300">Opsional</span>
                        </button>

                        <button @click="showCodeDrawer = !showCodeDrawer"
                                type="button"
                                class="inline-flex items-center gap-1.5 text-xs font-bold text-amber-400 hover:text-amber-300 transition-colors">
                            <i class="fa-solid fa-list-check"></i>
                            <span x-text="showCodeDrawer ? 'Tutup Switcher Kode' : 'Buka Switcher Kode Error (400 - 504)'"></span>
                        </button>
                    </div>

                    {{-- Diagnostics Content Panel --}}
                    <div x-show="showDiagnostics" 
                         x-cloak 
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0 -translate-y-2"
                         x-transition:enter-end="opacity-100 translate-y-0"
                         class="mt-4 p-4 rounded-2xl bg-black/50 border border-white/10 font-mono text-xs text-slate-300 space-y-3">
                        
                        <div class="flex items-center justify-between">
                            <span class="text-[11px] uppercase tracking-wider text-slate-400 font-bold">
                                Log Snapshot Telemetri
                            </span>
                            <button @click="copyDiagnostics()" 
                                    type="button"
                                    class="inline-flex items-center gap-1.5 px-3 py-1 rounded-lg bg-white/10 hover:bg-white/20 text-[11px] text-white transition-all">
                                <i class="fa-solid" :class="copied ? 'fa-check text-emerald-400' : 'fa-copy'"></i>
                                <span x-text="copied ? 'Disalin ke Clipboard!' : 'Salin Data Log'"></span>
                            </button>
                        </div>

                        @php
                            $incidentHash = 'AFP-' . date('Ymd') . '-' . strtoupper(substr(md5(request()->fullUrl() . microtime()), 0, 8));
                        @endphp

                        <pre id="diagnostics-data" class="bg-black/60 p-3 rounded-xl border border-white/5 overflow-x-auto text-[11px] leading-relaxed text-sky-200">
INCIDENT_ID   : {{ $incidentHash }}
HTTP_STATUS   : @yield('code', $code ?? '500') @yield('code_name', 'SYSTEM_EXCEPTION')
REQUEST_METHOD: {{ request()->method() }}
REQUEST_URL   : {{ request()->fullUrl() }}
CLIENT_IP     : {{ request()->ip() }}
USER_AGENT    : {{ Str::limit(request()->userAgent() ?? 'Web Browser Client', 80) }}
TIMESTAMP     : {{ now()->setTimezone('Asia/Jakarta')->toIso8601String() }}
ENVIRONMENT   : {{ app()->environment() }}
APP_SYSTEM    : Aquafarm Priangan (AMS BUDIDAYA v2.4)
EXCEPTION_MSG : {{ !empty($message) ? $message : (isset($exception) && is_object($exception) && method_exists($exception, 'getMessage') && $exception->getMessage() ? $exception->getMessage() : 'No explicit exception trace message provided.') }}
</pre>
                    </div>

                    {{-- Interactive Error Code Quick Navigator Drawer --}}
                    <div x-show="showCodeDrawer" 
                         x-cloak 
                         x-transition:enter="transition ease-out duration-250"
                         x-transition:enter-start="opacity-0 translate-y-3"
                         x-transition:enter-end="opacity-100 translate-y-0"
                         class="mt-4 p-5 rounded-2xl bg-gradient-to-r from-[#06122c] to-[#0a1d47] border border-sky-500/30 space-y-3 shadow-xl">
                        
                        <div class="flex items-center justify-between">
                            <div>
                                <h3 class="font-heading font-bold text-sm text-white uppercase tracking-wider flex items-center gap-2">
                                    <i class="fa-solid fa-compass text-amber-400"></i>
                                    Navigasi Cepat Semua Tipe Kode Error HTTP
                                </h3>
                                <p class="text-[11px] text-slate-300">
                                    Klik tombol kode untuk meninjau desain & penanganan setiap kode status secara langsung:
                                </p>
                            </div>
                            <a href="{{ route('errors.index') ?? url('/errors') }}" 
                               class="text-[11px] font-bold text-amber-400 hover:text-amber-300 underline underline-offset-4">
                                Buka Katalog Lengkap &rarr;
                            </a>
                        </div>

                        {{-- Code Chips --}}
                        <div class="grid grid-cols-2 sm:grid-cols-4 md:grid-cols-6 lg:grid-cols-11 gap-2 pt-1">
                            @php
                                $errorList = [
                                    '400' => ['name' => 'Bad Request', 'color' => 'hover:border-amber-400 hover:text-amber-300'],
                                    '401' => ['name' => 'Unauthorized', 'color' => 'hover:border-indigo-400 hover:text-indigo-300'],
                                    '402' => ['name' => 'Payment Req', 'color' => 'hover:border-emerald-400 hover:text-emerald-300'],
                                    '403' => ['name' => 'Forbidden', 'color' => 'hover:border-rose-400 hover:text-rose-300'],
                                    '404' => ['name' => 'Not Found', 'color' => 'hover:border-sky-400 hover:text-sky-300'],
                                    '419' => ['name' => 'Page Expired', 'color' => 'hover:border-yellow-400 hover:text-yellow-300'],
                                    '429' => ['name' => 'Too Many Req', 'color' => 'hover:border-purple-400 hover:text-purple-300'],
                                    '500' => ['name' => 'Server Error', 'color' => 'hover:border-red-400 hover:text-red-300'],
                                    '502' => ['name' => 'Bad Gateway', 'color' => 'hover:border-orange-400 hover:text-orange-300'],
                                    '503' => ['name' => 'Maintenance', 'color' => 'hover:border-cyan-400 hover:text-cyan-300'],
                                    '504' => ['name' => 'Gateway T/O', 'color' => 'hover:border-slate-400 hover:text-slate-200'],
                                ];
                                $currentCode = View::getSection('code', $code ?? '');
                            @endphp

                            @foreach($errorList as $c => $data)
                                <a href="{{ url('/errors/' . $c) }}" 
                                   class="px-2 py-2 rounded-xl text-center font-mono border transition-all {{ $currentCode == $c ? 'bg-sky-500/20 border-sky-400 text-sky-200 shadow-md ring-1 ring-sky-400/50 font-bold' : 'bg-white/[0.04] border-white/10 text-slate-300 ' . $data['color'] }}">
                                    <div class="text-sm font-extrabold">{{ $c }}</div>
                                    <div class="text-[9px] uppercase tracking-tighter truncate opacity-80">{{ $data['name'] }}</div>
                                </a>
                            @endforeach
                        </div>
                    </div>
                </div>

            </div>

        </div>
    </main>

    {{-- Footer --}}
    <footer class="relative z-10 border-t border-white/10 bg-[#06122c]/90 py-6 text-center text-xs text-slate-400">
        <div class="max-w-7xl mx-auto px-4 flex flex-col sm:flex-row items-center justify-between gap-3">
            <div class="flex items-center gap-2">
                <span class="font-bold text-slate-300">&copy; {{ date('Y') }} Aquafarm Priangan</span>
                <span>&bull;</span>
                <span>PT AMS Budidaya Nusantara</span>
            </div>
            <div class="flex items-center gap-4 text-slate-400">
                <a href="{{ route('landing') ?? url('/') }}" class="hover:text-amber-400 transition-colors">Beranda</a>
                <a href="{{ route('errors.index') ?? url('/errors') }}" class="hover:text-amber-400 transition-colors">Direktori Error</a>
                @auth
                    <a href="{{ route('dashboard') ?? url('/dashboard') }}" class="hover:text-amber-400 transition-colors">Dashboard Manajer</a>
                @endauth
            </div>
        </div>
    </footer>

</body>
</html>

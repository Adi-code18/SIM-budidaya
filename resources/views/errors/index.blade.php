<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pusat Direktori & Simulasi Kode Error - Aquafarm Priangan</title>
    <meta name="description" content="Katalog dan panduan komprehensif penanganan status kode error HTTP untuk platform manajemen budidaya ikan Aquafarm Priangan (AMS BUDIDAYA).">
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

        .aqua-pattern {
            background-color: #071530;
            background-image: 
                radial-gradient(circle at 10% 20%, rgba(0, 90, 171, 0.25) 0%, transparent 40%),
                radial-gradient(circle at 90% 80%, rgba(255, 143, 0, 0.12) 0%, transparent 45%),
                radial-gradient(circle at 50% 50%, rgba(10, 29, 71, 0.95) 0%, transparent 100%),
                radial-gradient(rgba(255, 255, 255, 0.04) 1px, transparent 1px);
            background-size: 100% 100%, 100% 100%, 100% 100%, 32px 32px;
        }

        .glass-card {
            background: rgba(13, 27, 62, 0.65);
            backdrop-filter: blur(14px);
            -webkit-backdrop-filter: blur(14px);
            border: 1px solid rgba(255, 255, 255, 0.08);
            transition: all 0.25s ease;
        }

        .glass-card:hover {
            transform: translateY(-4px);
            border-color: rgba(56, 189, 248, 0.35);
            box-shadow: 0 20px 30px -10px rgba(0, 0, 0, 0.5), 0 0 20px rgba(0, 90, 171, 0.2);
        }
    </style>
</head>
<body class="aqua-pattern text-slate-200 min-h-screen font-sans selection:bg-[#FF8F00] selection:text-slate-950"
      x-data="{
          search: '',
          activeTab: 'all',
          simCode: '404',
          simMessage: '',
          runSimulation() {
              const url = '{{ url('/errors') }}/' + this.simCode + (this.simMessage ? '?message=' + encodeURIComponent(this.simMessage) : '');
              window.open(url, '_blank');
          }
      }">

    {{-- Top Navbar --}}
    <header class="border-b border-white/10 bg-[#06122c]/80 backdrop-blur-md sticky top-0 z-30">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-20 flex items-center justify-between">
            <a href="{{ route('landing') ?? url('/') }}" class="flex items-center gap-3.5 group">
                <div class="w-11 h-11 rounded-2xl bg-gradient-to-br from-[#005aab] to-[#0a1d47] p-2 flex items-center justify-center border border-white/20 shadow-lg">
                    <img src="{{ asset('images/Logo aquafarm.png') }}" alt="Logo Aquafarm" class="w-full h-full object-contain filter drop-shadow">
                </div>
                <div>
                    <div class="flex items-center gap-2">
                        <span class="font-heading font-black text-lg tracking-wider text-white uppercase group-hover:text-amber-400 transition-colors">
                            Aquafarm Priangan
                        </span>
                        <span class="px-2 py-0.5 text-[10px] font-bold uppercase tracking-widest bg-sky-500/20 text-sky-300 border border-sky-500/30 rounded-full">
                            ERROR CENTER
                        </span>
                    </div>
                    <p class="text-[11px] text-slate-400 font-medium">
                        Direktori & Standar Penanganan Status Respon HTTP
                    </p>
                </div>
            </a>

            <div class="flex items-center gap-3">
                <a href="{{ route('landing') ?? url('/') }}" 
                   class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-white/[0.06] hover:bg-white/[0.12] text-xs font-bold text-white border border-white/15 transition-all">
                    <i class="fa-solid fa-house text-amber-400"></i>
                    <span class="hidden sm:inline">Landing Page Utama</span>
                </a>
                @auth
                    <a href="{{ route('dashboard') ?? url('/dashboard') }}" 
                       class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-[#005aab] hover:bg-[#004b93] text-white text-xs font-bold uppercase tracking-wider shadow-md transition-all">
                        <i class="fa-solid fa-gauge-high"></i>
                        <span>Dashboard</span>
                    </a>
                @else
                    <a href="{{ route('login') ?? url('/login') }}" 
                       class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-[#005aab] hover:bg-[#004b93] text-white text-xs font-bold uppercase tracking-wider shadow-md transition-all">
                        <i class="fa-solid fa-right-to-bracket"></i>
                        <span>Login</span>
                    </a>
                @endauth
            </div>
        </div>
    </header>

    {{-- Hero Section --}}
    <section class="relative pt-12 pb-16 px-4 sm:px-6 lg:px-8 border-b border-white/10">
        <div class="max-w-5xl mx-auto text-center space-y-6">
            
            <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-white/[0.06] border border-white/15 text-xs text-sky-300 font-mono">
                <i class="fa-solid fa-shield-halved text-amber-400"></i>
                <span>AMS BUDIDAYA SYSTEM RESILIENCE & ERROR HANDLING</span>
            </div>

            <h1 class="font-heading font-black text-3xl sm:text-5xl lg:text-6xl text-white tracking-tight leading-tight">
                Pusat Informasi & Direktori <br>
                <span class="text-transparent bg-clip-text bg-gradient-to-r from-sky-400 via-amber-300 to-amber-500">
                    Semua Tipe Kode Error HTTP
                </span>
            </h1>

            <p class="max-w-2xl mx-auto text-slate-300 text-sm sm:text-base leading-relaxed">
                Koleksi terpadu seluruh halaman penanganan error untuk platform budidaya Aquafarm Priangan. Dilengkapi ilustrasi khas budidaya, audit log diagnostik, serta rekomendasi penanganan teknis untuk operator dan pengembang.
            </p>

            {{-- Quick Stats Badges --}}
            <div class="pt-4 flex flex-wrap items-center justify-center gap-3 text-xs font-mono">
                <div class="px-4 py-2 rounded-xl bg-white/[0.04] border border-white/10 flex items-center gap-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-sky-400"></span>
                    <span class="text-white font-bold">11 Kode Standar</span>
                </div>
                <div class="px-4 py-2 rounded-xl bg-white/[0.04] border border-white/10 flex items-center gap-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-amber-400"></span>
                    <span class="text-white font-bold">Sisi Klien (4xx) & Server (5xx)</span>
                </div>
                <div class="px-4 py-2 rounded-xl bg-white/[0.04] border border-white/10 flex items-center gap-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-emerald-400"></span>
                    <span class="text-white font-bold">Siap Produksi Laravel 13</span>
                </div>
            </div>

            {{-- Search and Filter Controls --}}
            <div class="pt-6 max-w-xl mx-auto space-y-4">
                {{-- Search Bar --}}
                <div class="relative">
                    <i class="fa-solid fa-magnifying-glass absolute left-4 top-1/2 -translate-y-1/2 text-slate-400"></i>
                    <input type="text" 
                           x-model="search"
                           placeholder="Cari kode error (misal: 404, 500, 419, sesi, server)..."
                           class="w-full pl-11 pr-4 py-3 rounded-2xl bg-black/40 border border-white/15 text-white placeholder-slate-400 text-sm focus:outline-none focus:border-sky-400 focus:ring-1 focus:ring-sky-400 transition-all font-sans">
                </div>

                {{-- Filter Tabs --}}
                <div class="flex items-center justify-center gap-2">
                    <button @click="activeTab = 'all'" 
                            :class="activeTab === 'all' ? 'bg-[#005aab] text-white border-sky-400' : 'bg-white/[0.05] text-slate-300 border-white/10 hover:bg-white/[0.1]'"
                            class="px-4 py-1.5 rounded-xl text-xs font-bold border transition-all">
                        Semua Kode (11)
                    </button>
                    <button @click="activeTab = '4xx'" 
                            :class="activeTab === '4xx' ? 'bg-amber-600/60 text-white border-amber-400' : 'bg-white/[0.05] text-slate-300 border-white/10 hover:bg-white/[0.1]'"
                            class="px-4 py-1.5 rounded-xl text-xs font-bold border transition-all">
                        Error Klien (4xx)
                    </button>
                    <button @click="activeTab = '5xx'" 
                            :class="activeTab === '5xx' ? 'bg-red-700/60 text-white border-red-400' : 'bg-white/[0.05] text-slate-300 border-white/10 hover:bg-white/[0.1]'"
                            class="px-4 py-1.5 rounded-xl text-xs font-bold border transition-all">
                        Error Server (5xx)
                    </button>
                </div>
            </div>

        </div>
    </section>

    {{-- Cards Grid Section --}}
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
        
        @php
            $allErrors = [
                [
                    'code' => '400',
                    'group' => '4xx',
                    'name' => 'Bad Request',
                    'title' => 'Permintaan Tidak Valid',
                    'desc' => 'Parameter URL atau struktur data form yang dikirimkan klien tidak sesuai spesifikasi atau korup.',
                    'trigger' => 'Parameter sintaks keliru, format JSON cacat, atau payload di luar batas.',
                    'badge_color' => 'bg-amber-500/20 text-amber-300 border-amber-400/40',
                    'icon' => 'fa-solid fa-triangle-exclamation'
                ],
                [
                    'code' => '401',
                    'group' => '4xx',
                    'name' => 'Unauthorized',
                    'title' => 'Autentikasi Diperlukan',
                    'desc' => 'Pengguna belum melakukan login atau kredensial autentikasi tidak valid saat mengakses data privat.',
                    'trigger' => 'Sesi login belum dimulai atau header Authorization token hilang.',
                    'badge_color' => 'bg-indigo-500/20 text-indigo-300 border-indigo-400/40',
                    'icon' => 'fa-solid fa-user-lock'
                ],
                [
                    'code' => '402',
                    'group' => '4xx',
                    'name' => 'Payment Required',
                    'title' => 'Pembayaran Diperlukan',
                    'desc' => 'Akses fitur khusus analitik atau IoT lanjutan memerlukan langganan berbayar atau pelunasan lisensi.',
                    'trigger' => 'Masa berlaku paket habis atau kuota API berbayar belum diperpanjang.',
                    'badge_color' => 'bg-emerald-500/20 text-emerald-300 border-emerald-400/40',
                    'icon' => 'fa-solid fa-credit-card'
                ],
                [
                    'code' => '403',
                    'group' => '4xx',
                    'name' => 'Forbidden',
                    'title' => 'Akses Ditolak / Dilarang',
                    'desc' => 'Klien telah login, namun peran atau izin akunnya tidak mencukupi untuk membuka direktori/tindakan ini.',
                    'trigger' => 'Policy/Gate Laravel menolak akses (contoh: Petugas membuka halaman Keuangan Manajer).',
                    'badge_color' => 'bg-rose-500/20 text-rose-300 border-rose-400/40',
                    'icon' => 'fa-solid fa-ban'
                ],
                [
                    'code' => '404',
                    'group' => '4xx',
                    'name' => 'Not Found',
                    'title' => 'Halaman Tidak Ditemukan',
                    'desc' => 'Alamat URL salah, rute tidak terdaftar di sistem, atau data batch kolam telah dihapus dari basis data.',
                    'trigger' => 'ModelNotFoundException, Route::missing(), atau salah ketik URL.',
                    'badge_color' => 'bg-sky-500/20 text-sky-300 border-sky-400/40',
                    'icon' => 'fa-solid fa-compass-drafting'
                ],
                [
                    'code' => '419',
                    'group' => '4xx',
                    'name' => 'Page Expired',
                    'title' => 'Sesi Halaman Kedaluwarsa',
                    'desc' => 'Token keamanan CSRF formulir kedaluwarsa akibat jeda inaktivitas pengguna yang terlalu lama.',
                    'trigger' => 'TokenMismatchException saat submit form setelah berjam-jam tidak aktif.',
                    'badge_color' => 'bg-yellow-500/20 text-yellow-300 border-yellow-400/40',
                    'icon' => 'fa-solid fa-hourglass-end'
                ],
                [
                    'code' => '429',
                    'group' => '4xx',
                    'name' => 'Too Many Requests',
                    'title' => 'Terlalu Banyak Permintaan',
                    'desc' => 'Frekuensi permintaan melampaui batas kuota wajar (rate limit) untuk mencegah serangan DoS dan spamming.',
                    'trigger' => 'ThrottleRequests middleware (misal melebihi batas coba login atau polling sensor).',
                    'badge_color' => 'bg-purple-500/20 text-purple-300 border-purple-400/40',
                    'icon' => 'fa-solid fa-gauge-simple-high'
                ],
                [
                    'code' => '500',
                    'group' => '5xx',
                    'name' => 'Internal Server Error',
                    'title' => 'Gangguan Internal Server',
                    'desc' => 'Terjadi pengecualian tak terduga pada kode PHP atau database saat mengeksekusi logika sistem.',
                    'trigger' => 'Fatal Throwable, query database gagal, atau konfigurasi backend bermasalah.',
                    'badge_color' => 'bg-red-500/20 text-red-300 border-red-400/40',
                    'icon' => 'fa-solid fa-server'
                ],
                [
                    'code' => '502',
                    'group' => '5xx',
                    'name' => 'Bad Gateway',
                    'title' => 'Gerbang Jaringan Bermasalah',
                    'desc' => 'Reverse proxy web server menerima respon tidak valid atau gagal terhubung ke backend server hulu.',
                    'trigger' => 'PHP-FPM crash, Nginx gagal menghubungi container backend, atau port socket tertutup.',
                    'badge_color' => 'bg-orange-500/20 text-orange-300 border-orange-400/40',
                    'icon' => 'fa-solid fa-network-wired'
                ],
                [
                    'code' => '503',
                    'group' => '5xx',
                    'name' => 'Service Unavailable',
                    'title' => 'Sistem Dalam Pemeliharaan',
                    'desc' => 'Server sedang tidak dapat melayani permintaan karena sedang dalam masa perawatan rutin atau overload.',
                    'trigger' => 'Perintah php artisan down (Mode Maintenance) atau pembaruan klaster.',
                    'badge_color' => 'bg-cyan-500/20 text-cyan-300 border-cyan-400/40',
                    'icon' => 'fa-solid fa-wrench'
                ],
                [
                    'code' => '504',
                    'group' => '5xx',
                    'name' => 'Gateway Timeout',
                    'title' => 'Batas Waktu Respon Habis',
                    'desc' => 'Server gateway tidak kunjung menerima respon dari server upstream dalam durasi batas waktu yang ditetapkan.',
                    'trigger' => 'Query database berat atau ekspor rekapan data yang melampaui timeout socket (60s+).',
                    'badge_color' => 'bg-slate-500/20 text-slate-300 border-slate-400/40',
                    'icon' => 'fa-solid fa-clock-rotate-left'
                ],
            ];
        @endphp

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($allErrors as $err)
                <div class="glass-card rounded-3xl p-6 flex flex-col justify-between space-y-5"
                     x-show="(activeTab === 'all' || activeTab === '{{ $err['group'] }}') && 
                             ('{{ strtolower($err['code'] . ' ' . $err['name'] . ' ' . $err['title'] . ' ' . $err['desc']) }}'.includes(search.toLowerCase()))"
                     x-cloak>
                    
                    {{-- Header Card --}}
                    <div>
                        <div class="flex items-center justify-between gap-3 mb-3">
                            <span class="font-heading font-black text-4xl text-white tracking-tight">
                                {{ $err['code'] }}
                            </span>
                            <span class="px-3 py-1 rounded-full text-[11px] font-mono font-bold uppercase border {{ $err['badge_color'] }} flex items-center gap-1.5">
                                <i class="{{ $err['icon'] }}"></i>
                                <span>{{ $err['name'] }}</span>
                            </span>
                        </div>

                        <h2 class="font-heading font-bold text-lg text-white group-hover:text-amber-400 transition-colors">
                            {{ $err['title'] }}
                        </h2>

                        <p class="mt-2 text-xs text-slate-300 leading-relaxed font-normal">
                            {{ $err['desc'] }}
                        </p>
                    </div>

                    {{-- Technical Trigger Note --}}
                    <div class="rounded-xl bg-black/40 border border-white/5 p-3 space-y-1">
                        <div class="text-[10px] font-mono font-bold uppercase text-slate-400 flex items-center gap-1.5">
                            <i class="fa-solid fa-terminal text-sky-400 text-[9px]"></i>
                            <span>Pemicu Umum Laravel / Server:</span>
                        </div>
                        <p class="text-[11px] font-mono text-slate-300">
                            {{ $err['trigger'] }}
                        </p>
                    </div>

                    {{-- Action Links --}}
                    <div class="pt-2 flex items-center gap-2">
                        <a href="{{ url('/errors/' . $err['code']) }}" 
                           class="flex-1 inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl bg-white/[0.08] hover:bg-[#005aab] hover:border-sky-400 text-white font-bold text-xs uppercase tracking-wider border border-white/10 transition-all active:scale-95 shadow-md">
                            <span>Buka Halaman</span>
                            <i class="fa-solid fa-arrow-up-right-from-square text-[11px]"></i>
                        </a>

                        <a href="{{ url('/errors/abort/' . $err['code']) }}" 
                           title="Uji lemparan abort({{ $err['code'] }}) aktual Laravel"
                           class="inline-flex items-center justify-center px-3 py-2.5 rounded-xl bg-white/[0.04] hover:bg-white/[0.12] text-slate-300 hover:text-amber-400 border border-white/5 text-xs font-mono transition-all"
                           target="_blank">
                            <i class="fa-solid fa-bolt"></i>
                        </a>
                    </div>

                </div>
            @endforeach
        </div>

    </section>

    {{-- Interactive Simulator Section --}}
    <section class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 pb-20">
        <div class="glass-card rounded-3xl p-6 sm:p-10 border border-sky-500/30 relative overflow-hidden">
            <div class="absolute -right-20 -bottom-20 w-80 h-80 bg-sky-500/10 rounded-full blur-3xl pointer-events-none"></div>

            <div class="relative z-10 grid grid-cols-1 lg:grid-cols-12 gap-8 items-center">
                
                <div class="lg:col-span-7 space-y-4">
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-amber-500/20 text-amber-300 border border-amber-500/30 text-xs font-mono">
                        <i class="fa-solid fa-flask"></i>
                        <span>INTERACTIVE ERROR PLAYGROUND</span>
                    </div>

                    <h2 class="font-heading font-black text-2xl sm:text-3xl text-white">
                        Simulasi Tampilan Error Secara Kustom
                    </h2>

                    <p class="text-xs sm:text-sm text-slate-300 leading-relaxed">
                        Pilih kode status yang ingin Anda uji coba, tambahkan pesan khusus atau kondisi pengujian, lalu klik tombol simulasi untuk melihat tampilan landing error secara real-time.
                    </p>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 pt-2">
                        <div>
                            <label class="block text-[11px] font-mono text-slate-400 uppercase tracking-wider mb-1">
                                Pilih Kode Error:
                            </label>
                            <select x-model="simCode" 
                                    class="w-full px-3.5 py-2.5 rounded-xl bg-black/60 border border-white/15 text-white font-mono text-xs focus:outline-none focus:border-sky-400">
                                @foreach($allErrors as $err)
                                    <option value="{{ $err['code'] }}">{{ $err['code'] }} - {{ $err['name'] }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-[11px] font-mono text-slate-400 uppercase tracking-wider mb-1">
                                Pesan Error Kustom (Opsional):
                            </label>
                            <input type="text" 
                                   x-model="simMessage" 
                                   placeholder="Contoh: Sensor pH Kolam #03 tidak merespon..." 
                                   class="w-full px-3.5 py-2.5 rounded-xl bg-black/60 border border-white/15 text-white text-xs focus:outline-none focus:border-sky-400">
                        </div>
                    </div>

                    <div class="pt-2 flex items-center gap-3">
                        <button @click="runSimulation()" 
                                type="button"
                                class="inline-flex items-center gap-2 px-6 py-3 rounded-xl bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-600 hover:to-amber-700 text-slate-950 font-black text-xs uppercase tracking-wider shadow-lg shadow-amber-950/40 transition-all active:scale-95">
                            <i class="fa-solid fa-play"></i>
                            <span>Jalankan Simulasi Tampilan</span>
                        </button>
                    </div>
                </div>

                <div class="lg:col-span-5 bg-black/40 rounded-2xl p-5 border border-white/10 font-mono text-xs text-slate-300 space-y-3">
                    <div class="text-[11px] text-amber-400 font-bold uppercase tracking-wider flex items-center gap-2">
                        <i class="fa-solid fa-code"></i>
                        <span>Cara Pakai di Kode Controller Laravel:</span>
                    </div>

                    <pre class="bg-black/60 p-3 rounded-xl border border-white/5 text-[11px] text-sky-300 overflow-x-auto">
// 1. Lempar error 403 (Akses Ditolak)
abort(403, 'Akses khusus Manajer Budidaya.');

// 2. Lempar error 404 (Data Tidak Ditemukan)
abort(404, 'Batch bibit tidak terdaftar.');

// 3. Lempar error 503 (Mode Perawatan)
abort(503, 'Kalibrasi sensor kolam terjadwal.');
</pre>
                    <p class="text-[10px] text-slate-400 leading-tight">
                        Template otomatis mendeteksi kode HTTP exception dan menampilkan desain sesuai standar resmi Aquafarm Priangan.
                    </p>
                </div>

            </div>
        </div>
    </section>

    {{-- Footer --}}
    <footer class="border-t border-white/10 bg-[#06122c]/90 py-8 text-center text-xs text-slate-400">
        <div class="max-w-7xl mx-auto px-4 flex flex-col sm:flex-row items-center justify-between gap-4">
            <div class="flex items-center gap-2">
                <span class="font-bold text-slate-300">&copy; {{ date('Y') }} Aquafarm Priangan</span>
                <span>&bull;</span>
                <span>PT AMS Budidaya Nusantara</span>
            </div>
            <div class="flex items-center gap-4 text-slate-400">
                <a href="{{ route('landing') ?? url('/') }}" class="hover:text-amber-400 transition-colors">Beranda Publik</a>
                <a href="{{ url('/errors/404') }}" class="hover:text-amber-400 transition-colors">Uji 404</a>
                <a href="{{ url('/errors/500') }}" class="hover:text-amber-400 transition-colors">Uji 500</a>
                @auth
                    <a href="{{ route('dashboard') ?? url('/dashboard') }}" class="hover:text-amber-400 transition-colors">Dashboard Utama</a>
                @endauth
            </div>
        </div>
    </footer>

</body>
</html>


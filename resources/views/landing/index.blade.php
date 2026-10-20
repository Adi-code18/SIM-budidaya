<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Aquafarm Priangan - Institut & Fasilitas Budidaya Ikan Air Tawar Modern</title>
    <meta name="description" content="Penyedia komoditas ikan air tawar (Nila Merah Nirwana, Lele Sangkuriang, Patin Siam, Gurame Soang) berkualitas restoran di Jawa Barat. Standar budidaya terukur, bebas bau lumpur, dan garansi hidup DDP.">
    <link rel="icon" type="image/png" href="{{ asset('images/Logo aquafarm.png') }}">

    <!-- Google Fonts (Barlow & Plus Jakarta Sans - ITB Style) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Barlow:wght@400;500;600;700;800;900&family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Font Awesome Icons -->
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
                    },
                    colors: {
                        itb: {
                            navy: '#0a1d47',
                            bluehead: '#004b93',
                            primary: '#005aab',
                            darkblue: '#003d82',
                            royal: '#0b2565',
                            gold: '#FF8F00',
                            yellow: '#FFC700',
                            golddark: '#E65100',
                            lightgrey: '#f1f3f5',
                            cardgrey: '#f8f9fa'
                        },
                        wa: {
                            DEFAULT: '#25D366',
                            hover: '#1fb855'
                        }
                    }
                }
            }
        }
    </script>

    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/@alpinejs/collapse@3.x.x/dist/cdn.min.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <style>
        [x-cloak] { display: none !important; }

        /* Custom scrollbar for interactive dropdowns */
        .custom-scrollbar::-webkit-scrollbar {
            width: 5px;
        }
        .custom-scrollbar::-webkit-scrollbar-track {
            background: rgba(255, 255, 255, 0.04);
            border-radius: 9999px;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: rgba(255, 255, 255, 0.2);
            border-radius: 9999px;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background: #FFC700;
        }
        
        /* ITB Signature Decorative Divider */
        .itb-divider {
            height: 4px;
            width: 50px;
            background-color: #005aab;
            border-radius: 9999px;
            margin-top: 6px;
            margin-bottom: 24px;
        }
        .itb-divider-center {
            margin-left: auto;
            margin-right: auto;
        }
        .itb-divider-gold {
            background-color: #FF8F00;
        }

        /* ITB Image Card Zoom on Hover */
        .itb-post-image {
            overflow: hidden;
            position: relative;
        }
        .itb-post-image img {
            transition: transform 0.8s cubic-bezier(0.25, 1, 0.5, 1);
        }
        .itb-post-image:hover img {
            transform: scale(1.1);
        }

        /* ITB Button More (Outlined Blue) */
        .btn-itb-more {
            color: #005aab;
            border: 2px solid #005aab;
            font-weight: 600;
            transition: all 0.25s ease;
        }
        .btn-itb-more:hover {
            background-color: #005aab;
            color: #ffffff;
        }

        /* ITB Gold Button */
        .btn-itb-gold {
            background-color: #FF8F00;
            color: #ffffff;
            font-weight: 700;
            transition: all 0.2s ease;
        }
        .btn-itb-gold:hover {
            background-color: #e67e00;
            color: #ffffff;
        }
    </style>
</head>
<body class="font-sans bg-white text-slate-800 antialiased selection:bg-itb-primary selection:text-white" x-data="{ mobileMenu: false }">


    <!-- =========================================================================
        2. TIER 2: PRIMARY HEADER (LOGO & MAIN NAVIGATION - DINAMIS TEMA)
    ========================================================================= -->
    <header class="sticky top-0 z-50 {{ $settings['theme']['nav_bg'] ?? 'bg-[#004b93]' }} border-b border-white/10 shadow-md transition-colors duration-300">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-20">
                
                <!-- ITB Style Logo + Institutional Typography -->
                <a href="{{ route('landing') }}" class="flex items-center gap-3.5 group">
                    <div class="w-12 h-12 flex items-center justify-center p-1.5 bg-white rounded-full border-2 border-white/80 shadow-sm group-hover:scale-105 transition-transform shrink-0">
                        <img src="{{ asset('images/Logo aquafarm.png') }}" alt="Logo {{ $settings['brand_name'] ?? 'Aquafarm' }}" class="w-full h-full object-contain">
                    </div>
                    <div>
                        <div class="font-heading text-lg sm:text-xl font-black tracking-wider text-white leading-tight uppercase">
                            {{ $settings['brand_name'] ?? 'AQUAFARM PRIANGAN' }}
                        </div>
                        <p class="font-heading text-[10px] sm:text-[11px] font-semibold text-sky-200 uppercase tracking-widest leading-none mt-0.5">
                            {{ $settings['brand_subtitle'] ?? 'Fasilitas Budidaya Air Tawar Ciamis' }}
                        </p>
                    </div>
                </a>

                <!-- Desktop Navigation Menu (Crisp White Text like ITB) -->
                <nav class="hidden xl:flex items-center gap-6 font-heading text-[13px] font-semibold text-white tracking-wide">
                    <a href="#katalog" class="hover:text-[{{ $settings['theme']['accent_hex'] ?? '#FFC700' }}] transition py-2 relative group">
                        Tentang Kami
                        <span class="absolute bottom-0 left-0 w-0 h-0.5 bg-[{{ $settings['theme']['accent_hex'] ?? '#FFC700' }}] transition-all duration-300 group-hover:w-full"></span>
                    </a>
                    <a href="#katalog" class="hover:text-[{{ $settings['theme']['accent_hex'] ?? '#FFC700' }}] transition py-2 relative group">
                        Komoditas Ikan
                        <span class="absolute bottom-0 left-0 w-0 h-0.5 bg-[{{ $settings['theme']['accent_hex'] ?? '#FFC700' }}] transition-all duration-300 group-hover:w-full"></span>
                    </a>
                    <a href="#sop" class="hover:text-[{{ $settings['theme']['accent_hex'] ?? '#FFC700' }}] transition py-2 relative group">
                        Standar SOP Mutu
                        <span class="absolute bottom-0 left-0 w-0 h-0.5 bg-[{{ $settings['theme']['accent_hex'] ?? '#FFC700' }}] transition-all duration-300 group-hover:w-full"></span>
                    </a>
                    @if(($settings['show_agenda'] ?? '1') == '1')
                    <a href="#agenda" class="hover:text-[{{ $settings['theme']['accent_hex'] ?? '#FFC700' }}] transition py-2 relative group">
                        Jadwal Panen
                        <span class="absolute bottom-0 left-0 w-0 h-0.5 bg-[{{ $settings['theme']['accent_hex'] ?? '#FFC700' }}] transition-all duration-300 group-hover:w-full"></span>
                    </a>
                    @endif
                    @if(($settings['show_calculator'] ?? '1') == '1')
                    <a href="#kalkulator" class="hover:text-[{{ $settings['theme']['accent_hex'] ?? '#FFC700' }}] transition py-2 relative group">
                        Kalkulator Pesanan
                        <span class="absolute bottom-0 left-0 w-0 h-0.5 bg-[{{ $settings['theme']['accent_hex'] ?? '#FFC700' }}] transition-all duration-300 group-hover:w-full"></span>
                    </a>
                    @endif
                    @if(($settings['show_investor'] ?? '1') == '1')
                    <a href="#investor" class="hover:text-[{{ $settings['theme']['accent_hex'] ?? '#FFC700' }}] transition py-2 relative group">
                        Kemitraan Investor
                        <span class="absolute bottom-0 left-0 w-0 h-0.5 bg-[{{ $settings['theme']['accent_hex'] ?? '#FFC700' }}] transition-all duration-300 group-hover:w-full"></span>
                    </a>
                    @endif
                    <a href="#kontak" class="hover:text-[{{ $settings['theme']['accent_hex'] ?? '#FFC700' }}] transition py-2 relative group">
                        Lokasi Budidaya
                        <span class="absolute bottom-0 left-0 w-0 h-0.5 bg-[{{ $settings['theme']['accent_hex'] ?? '#FFC700' }}] transition-all duration-300 group-hover:w-full"></span>
                    </a>
                </nav>

                <!-- Right: ITB Square Hamburger Button -->
                <div class="flex items-center gap-3">
                    <button @click="mobileMenu = !mobileMenu" type="button" 
                            class="p-2.5 rounded-lg border border-white/30 text-white hover:bg-white/10 active:scale-95 transition"
                            aria-label="Menu Navigasi">
                        <i class="fa-solid" :class="mobileMenu ? 'fa-xmark text-xl' : 'fa-bars text-xl'"></i>
                    </button>
                </div>

            </div>
        </div>

        <!-- Mobile Drawer Menu -->
        <div x-show="mobileMenu" x-cloak 
             class="border-t border-white/10 {{ $settings['theme']['topbar_bg'] ?? 'bg-[#0a2768]' }} px-6 py-5 space-y-3 font-heading text-white">
            <a @click="mobileMenu = false" href="#katalog" class="block py-2 text-sm font-semibold hover:text-[{{ $settings['theme']['accent_hex'] ?? '#FFC700' }}] border-b border-white/10">Tentang Kami</a>
            <a @click="mobileMenu = false" href="#katalog" class="block py-2 text-sm font-semibold hover:text-[{{ $settings['theme']['accent_hex'] ?? '#FFC700' }}] border-b border-white/10">Komoditas Ikan</a>
            <a @click="mobileMenu = false" href="#sop" class="block py-2 text-sm font-semibold hover:text-[{{ $settings['theme']['accent_hex'] ?? '#FFC700' }}] border-b border-white/10">Standar SOP Mutu</a>
            @if(($settings['show_agenda'] ?? '1') == '1')
            <a @click="mobileMenu = false" href="#agenda" class="block py-2 text-sm font-semibold hover:text-[{{ $settings['theme']['accent_hex'] ?? '#FFC700' }}] border-b border-white/10">Jadwal Panen</a>
            @endif
            @if(($settings['show_calculator'] ?? '1') == '1')
            <a @click="mobileMenu = false" href="#kalkulator" class="block py-2 text-sm font-semibold hover:text-[{{ $settings['theme']['accent_hex'] ?? '#FFC700' }}] border-b border-white/10">Kalkulator Pesanan</a>
            @endif
            @if(($settings['show_investor'] ?? '1') == '1')
            <a @click="mobileMenu = false" href="#investor" class="block py-2 text-sm font-semibold hover:text-[{{ $settings['theme']['accent_hex'] ?? '#FFC700' }}] border-b border-white/10">Kemitraan Investor</a>
            @endif
            <a @click="mobileMenu = false" href="#kontak" class="block py-2 text-sm font-semibold hover:text-[{{ $settings['theme']['accent_hex'] ?? '#FFC700' }}]">Lokasi Budidaya</a>
            <div class="pt-3">
                <a href="https://wa.me/{{ $company['phone_wa'] }}?text={{ urlencode($settings['wa_template_text'] ?? 'Halo Sales Aquafarm, saya ingin memesan pasokan ikan.') }}" 
                   target="_blank"
                   class="w-full flex items-center justify-center gap-2 px-4 py-2.5 rounded-lg bg-[#ff8f00] text-white font-bold text-xs uppercase tracking-wider shadow-md">
                    <i class="fa-brands fa-whatsapp text-sm"></i>
                    <span>Order Panen WhatsApp</span>
                </a>
            </div>
        </div>
    </header>

    <!-- =========================================================================
        3. HERO SECTION (PERSIS BANNER UTAMA ITB FULL-WIDTH DENGAN 6 SLIDE HORIZONTAL GESER SUPER SMOOTH)
    ========================================================================= -->
    <section class="relative bg-[#004b93] text-white overflow-hidden select-none min-h-[580px] lg:min-h-[620px]"
             x-data="{
                activeSlide: 0,
                totalSlides: 6,
                autoTimer: null,
                isPaused: false,
                touchStartX: 0,
                touchEndX: 0,

                startAuto() {
                    this.stopAuto();
                    this.autoTimer = setInterval(() => {
                        if (!this.isPaused) {
                            this.next();
                        }
                    }, 5000);
                },
                stopAuto() {
                    if (this.autoTimer) clearInterval(this.autoTimer);
                },
                next() {
                    this.activeSlide = (this.activeSlide + 1) % this.totalSlides;
                },
                prev() {
                    this.activeSlide = (this.activeSlide - 1 + this.totalSlides) % this.totalSlides;
                },
                goTo(idx) {
                    this.activeSlide = idx;
                },
                handleTouchStart(e) {
                    this.touchStartX = e.changedTouches[0].screenX;
                },
                handleTouchEnd(e) {
                    this.touchEndX = e.changedTouches[0].screenX;
                    const diff = this.touchStartX - this.touchEndX;
                    if (Math.abs(diff) > 40) {
                        if (diff > 0) this.next();
                        else this.prev();
                    }
                }
             }"
             x-init="startAuto()"
             @mouseenter="isPaused = true"
             @mouseleave="isPaused = false"
             @touchstart.passive="handleTouchStart($event)"
             @touchend.passive="handleTouchEnd($event)">

        <!-- ================= TRACK SLIDER HORIZONTAL (6 SLIDE BERGELIR PERSIS ITB) ================= -->
        <div class="flex w-full min-h-[580px] lg:min-h-[620px] transition-transform duration-700 ease-[cubic-bezier(0.16,1,0.3,1)] will-change-transform"
             :style="'transform: translate3d(-' + (activeSlide * 100) + '%, 0, 0);'">

            <!-- ================= SLIDE 1: EXACT ITB COLLAGE / SHOWCASE BANNER (#1 DI JABAR) ================= -->
            <div class="w-full shrink-0 min-h-[580px] lg:min-h-[620px] relative py-12 sm:py-16 lg:py-20 flex items-center bg-gradient-to-r from-[#003d82] via-[#004b93] to-[#071d49] bg-cover bg-center"
                 style="{{ !empty($settings['hero_image']) ? 'background-image: url(' . $settings['hero_image_url'] . ');' : '' }}">
                
                @if(!empty($settings['hero_image']))
                <div class="absolute inset-0 bg-gradient-to-r from-[#002b5e]/95 via-[#004b93]/90 to-[#071d49]/85"></div>
                @endif

                <!-- ITB Blueprint Architectural Line-Art Background (Left & Top Right) -->
                <div class="absolute inset-y-0 left-0 w-96 pointer-events-none opacity-25 select-none hidden md:block">
                    <svg class="w-full h-full" viewBox="0 0 300 600" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M 30,50 A 40,40 0 0,1 110,50 L 110,130 A 40,40 0 0,1 30,130 Z" stroke="#38bdf8" stroke-width="1.5"/>
                        <path d="M 50,70 A 20,20 0 0,1 90,70 L 90,110 A 20,20 0 0,1 50,110 Z" stroke="#38bdf8" stroke-width="1"/>
                        <circle cx="70" cy="180" r="35" stroke="#38bdf8" stroke-width="1.5"/>
                        <circle cx="70" cy="180" r="22" stroke="#38bdf8" stroke-width="1" stroke-dasharray="4 3"/>
                        <circle cx="70" cy="180" r="10" stroke="#38bdf8" stroke-width="1"/>
                        <path d="M 30,250 C 60,230 80,270 110,250 C 140,230 160,270 190,250" stroke="#38bdf8" stroke-width="1.5"/>
                        <path d="M 30,270 C 60,250 80,290 110,270 C 140,250 160,290 190,270" stroke="#38bdf8" stroke-width="1"/>
                        <rect x="30" y="340" width="80" height="90" rx="40" stroke="#38bdf8" stroke-width="1.5"/>
                    </svg>
                </div>
                <div class="absolute top-0 right-0 w-80 h-80 pointer-events-none opacity-20 select-none hidden lg:block">
                    <svg class="w-full h-full" viewBox="0 0 200 200" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M 120,0 A 60,60 0 0,0 60,60 L 60,120 L 120,120 Z" stroke="#38bdf8" stroke-width="1.5"/>
                        <circle cx="90" cy="90" r="20" stroke="#38bdf8" stroke-width="1"/>
                    </svg>
                </div>

                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10 w-full">
                    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-12 items-center">
                        
                        <!-- Left: Authentic ITB Quad-Photo Collage OR Custom Upload Showcase -->
                        <div class="lg:col-span-6 relative">
                            @if(!empty($settings['hero_image']))
                            <!-- Tampilan Foto Khusus Unggahan Slide 1 dengan Frame Blueprint ITB -->
                            <div class="relative rounded-2xl overflow-hidden border-2 border-white/80 shadow-2xl group max-w-xl mx-auto">
                                <img src="{{ $settings['hero_image_url'] }}" 
                                     alt="Banner Utama Slide 1" 
                                     class="w-full h-72 sm:h-96 object-cover object-center group-hover:scale-105 transition-transform duration-700">
                                <div class="absolute inset-0 bg-gradient-to-t from-black/85 via-transparent to-transparent"></div>
                                <div class="absolute bottom-4 left-4 right-4 flex items-center justify-between text-white">
                                    <div>
                                        <span class="text-[10px] font-extrabold uppercase tracking-widest text-[#FFC700] block">{{ $settings['slide1_tag'] ?? 'TERAKREDITASI CBIB PRIMA 1' }}</span>
                                        <span class="text-sm sm:text-base font-black uppercase tracking-wide text-white">{{ $settings['brand_name'] ?? 'AQUAFARM PRIANGAN' }}</span>
                                    </div>
                                    <span class="px-2.5 py-1 rounded-full bg-emerald-500 text-slate-950 text-[10px] font-black uppercase tracking-wider">Terverifikasi</span>
                                </div>
                            </div>
                            @else
                            <!-- Tampilan Kolase 4 Foto Bawaan Resmi Gaya ITB QS Ranking -->
                            <div class="relative flex items-center justify-center py-4">
                                <div class="absolute -top-3 left-4 w-10 h-10 border-2 border-sky-400/50 rounded-lg -rotate-12 pointer-events-none hidden sm:block"></div>
                                <div class="absolute top-1/2 left-2 -translate-y-1/2 w-12 h-12 rounded-full border border-sky-300/40 flex items-center justify-center pointer-events-none hidden sm:flex">
                                    <div class="w-8 h-8 rounded-full border border-sky-300/40 border-dashed"></div>
                                </div>

                                <div class="grid grid-cols-12 gap-3 sm:gap-4 items-center w-full max-w-xl">
                                    <div class="col-span-5 space-y-3 sm:space-y-4">
                                        <div class="relative rounded-xl overflow-hidden border-2 border-white/70 shadow-2xl group/img">
                                            <img src="{{ $settings['collage_img1'] }}" alt="Fasilitas Budidaya Bioflok" class="w-full h-36 sm:h-44 object-cover group-hover/img:scale-105 transition-transform duration-500">
                                            <div class="absolute bottom-1.5 left-2 bg-black/60 backdrop-blur-xs px-2 py-0.5 rounded text-[9px] font-bold text-sky-200">Kolam Bioflok</div>
                                        </div>
                                        <div class="relative rounded-xl overflow-hidden border-2 border-white/70 shadow-2xl group/img">
                                            <img src="{{ $settings['collage_img2'] }}" alt="Panen Ikan Nila Segar" class="w-full h-32 sm:h-36 object-cover group-hover/img:scale-105 transition-transform duration-500">
                                            <div class="absolute bottom-1.5 left-2 bg-black/60 backdrop-blur-xs px-2 py-0.5 rounded text-[9px] font-bold text-amber-300">Panen Segar</div>
                                        </div>
                                    </div>
                                    <div class="col-span-4">
                                        <div class="relative rounded-xl overflow-hidden border-2 border-white/80 shadow-2xl h-72 sm:h-84 group/img">
                                            <img src="{{ $settings['collage_img3'] }}" alt="Laboratorium Kualitas Air" class="w-full h-full object-cover object-center group-hover/img:scale-105 transition-transform duration-500">
                                            <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-transparent to-transparent"></div>
                                            <div class="absolute bottom-3 left-3 right-3 text-left">
                                                <span class="text-[9px] font-extrabold uppercase tracking-widest text-[#FFC700] block">Riset &amp; Mutu</span>
                                                <span class="text-xs font-bold text-white leading-tight block">Laboratorium Air</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-span-3">
                                        <div class="relative rounded-xl overflow-hidden border-2 border-white/70 shadow-2xl h-64 sm:h-76 group/img">
                                            <img src="{{ $settings['collage_img4'] }}" alt="Chef Restoran Sajian Ikan" class="w-full h-full object-cover object-top group-hover/img:scale-105 transition-transform duration-500">
                                            <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-transparent to-transparent"></div>
                                            <div class="absolute bottom-2.5 left-2 right-2 text-left">
                                                <span class="text-[9px] font-bold text-emerald-300 leading-tight block">Standar Resto</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endif
                        </div>

                        <!-- Right: ITB Official Typography & Badges -->
                        <div class="lg:col-span-6 space-y-5 text-left">
                            <div class="flex items-center gap-3">
                                <div class="flex items-center gap-1.5 font-heading italic text-white tracking-tight">
                                    <span class="text-base sm:text-lg font-black lowercase">{{ explode(' ', $settings['slide1_subbrand'] ?? 'satu priangan')[0] ?? 'satu' }}</span>
                                    <span class="text-base sm:text-lg font-extrabold uppercase tracking-widest text-[#FFC700]">{{ explode(' ', $settings['slide1_subbrand'] ?? 'satu priangan')[1] ?? 'priangan' }}</span>
                                </div>
                                <div class="h-4 w-px bg-white/20"></div>
                                <div class="px-2.5 py-0.5 rounded-full bg-emerald-950/80 border border-emerald-400/40 text-emerald-300 text-[11px] font-heading font-bold">
                                    <i class="fa-solid fa-award text-[#FFC700] mr-1"></i>
                                    <span>{{ $settings['slide1_tag'] ?? 'TERAKREDITASI CBIB PRIMA 1' }}</span>
                                </div>
                            </div>

                            <div class="space-y-1">
                                <h2 class="font-heading text-base sm:text-lg font-extrabold uppercase tracking-widest text-sky-200">
                                    {{ $settings['brand_name'] ?? 'AQUAFARM PRIANGAN' }}
                                </h2>
                                <h1 class="font-heading font-black text-3xl sm:text-4xl lg:text-5xl tracking-tight text-[#FFC700] leading-tight drop-shadow-sm uppercase">
                                    {{ $settings['hero_headline'] ?? 'TERAKREDITASI #1 DI JAWA BARAT' }}
                                </h1>
                                <p class="font-heading text-xs sm:text-sm font-bold uppercase tracking-wider text-slate-200 pt-1">
                                    {{ $settings['slide1_subheadline'] ?? 'KOMODITAS AIR TAWAR • NILA MERAH • LELE • PATIN • GURAME' }}
                                </p>
                            </div>

                            <p class="text-xs sm:text-sm text-slate-200 leading-relaxed max-w-xl">
                                {{ $settings['hero_description'] ?? 'Fasilitas Akuakultur Terpadu & Pemasok Utama Ikan Air Tawar Bebas Bau Lumpur (Zero Geosmin) Melalui Standar Pemberokan 24 Jam.' }}
                            </p>

                            <!-- 3 Gold Shield Badges (Persis Tampilan 3 Logo QS Ranking ITB) -->
                            <div class="flex flex-wrap items-center gap-3 pt-1">
                                <div class="w-20 sm:w-24 rounded-lg bg-gradient-to-b from-amber-400 to-[#FF8F00] text-slate-950 p-2 sm:p-2.5 text-center shadow-lg border border-amber-300">
                                    <div class="text-[8px] uppercase tracking-wider font-extrabold leading-tight">{{ $settings['slide1_badge1_title'] ?? 'MUTU KKP' }}</div>
                                    <div class="font-heading text-xl sm:text-2xl font-black leading-tight text-slate-900">{{ $settings['slide1_badge1_val'] ?? '#1' }}</div>
                                    <div class="text-[7px] uppercase font-bold tracking-tight text-slate-950">{{ $settings['slide1_badge1_sub'] ?? 'CBIB PRIMA 1' }}</div>
                                </div>
                                <div class="w-20 sm:w-24 rounded-lg bg-gradient-to-b from-amber-400 to-[#FF8F00] text-slate-950 p-2 sm:p-2.5 text-center shadow-lg border border-amber-300">
                                    <div class="text-[8px] uppercase tracking-wider font-extrabold leading-tight">{{ $settings['slide1_badge2_title'] ?? 'AIR DERAS' }}</div>
                                    <div class="font-heading text-xl sm:text-2xl font-black leading-tight text-slate-900">{{ $settings['slide1_badge2_val'] ?? '0%' }}</div>
                                    <div class="text-[7px] uppercase font-bold tracking-tight text-slate-950">{{ $settings['slide1_badge2_sub'] ?? 'ZERO GEOSMIN' }}</div>
                                </div>
                                <div class="w-20 sm:w-24 rounded-lg bg-gradient-to-b from-amber-400 to-[#FF8F00] text-slate-950 p-2 sm:p-2.5 text-center shadow-lg border border-amber-300">
                                    <div class="text-[8px] uppercase tracking-wider font-extrabold leading-tight">{{ $settings['slide1_badge3_title'] ?? 'LOGISTIK DDP' }}</div>
                                    <div class="font-heading text-xl sm:text-2xl font-black leading-tight text-slate-900">{{ $settings['slide1_badge3_val'] ?? '≤2%' }}</div>
                                    <div class="text-[7px] uppercase font-bold tracking-tight text-slate-950">{{ $settings['slide1_badge3_sub'] ?? 'SUSUT HIDUP' }}</div>
                                </div>
                            </div>

                            <div class="flex flex-wrap items-center gap-3 pt-2">
                                <a href="https://wa.me/{{ $company['phone_wa'] }}?text={{ urlencode($settings['wa_template_text'] ?? 'Halo Aquafarm Priangan, saya tertarik memesan pasokan ikan panen segar.') }}" 
                                   target="_blank"
                                   class="inline-flex items-center gap-2 px-5 py-3 rounded-xl font-heading text-xs sm:text-sm uppercase tracking-wider font-extrabold text-slate-950 bg-[#FFC700] hover:bg-amber-300 active:scale-95 shadow-lg shadow-black/30 transition-all cursor-pointer">
                                    <i class="fa-brands fa-whatsapp text-base"></i>
                                    <span>Pesan Panen via WhatsApp</span>
                                </a>

                                @if(($settings['show_calculator'] ?? '1') == '1')
                                <a href="#kalkulator" 
                                   class="inline-flex items-center gap-2 px-5 py-3 rounded-xl font-heading text-xs sm:text-sm uppercase tracking-wider font-bold text-white bg-white/10 hover:bg-white/20 border border-white/30 backdrop-blur-xs transition-all cursor-pointer">
                                    <i class="fa-solid fa-calculator text-[#FFC700]"></i>
                                    <span>Hitung Kebutuhan Ikan</span>
                                </a>
                                @endif
                            </div>

                        </div>

                    </div>
                </div>
            </div>

            <!-- ================= SLIDE 2: FASILITAS BUDIDAYA BIOFLOK CIAMIS ================= -->
            <div class="w-full shrink-0 min-h-[580px] lg:min-h-[620px] relative py-16 sm:py-20 lg:py-24 bg-cover bg-center flex items-center"
                 style="background-image: url('{{ $settings['hero_slide2_url'] }}');">
                <div class="absolute inset-0 bg-gradient-to-r from-[#002244]/95 via-[#004b93]/85 to-[#002244]/75"></div>
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10 w-full">
                    <div class="max-w-3xl space-y-4">
                        <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-sky-500/20 border border-sky-300/30 text-sky-200 text-xs font-heading font-extrabold uppercase tracking-widest">
                            <i class="fa-solid fa-water"></i>
                            <span>{{ $settings['slide2_tag'] ?? 'FASILITAS BUNI SEURI CIAMIS' }}</span>
                        </span>
                        <h2 class="font-heading text-3xl sm:text-5xl font-black text-white leading-tight tracking-tight uppercase">
                            {{ $settings['slide2_title'] ?? 'Sirkulasi Air Mengalir Deras 24 Jam & Kolam Bioflok Modern' }}
                        </h2>
                        <p class="text-sm sm:text-base text-slate-200 leading-relaxed">
                            {{ $settings['slide2_desc'] ?? 'Memanfaatkan sumber air pegunungan alami yang mengalir deras dan beroksigen tinggi. Standar pemuasaan 24 jam menjamin daging ikan padat, gurih alami, dan bebas bau lumpur (Zero Geosmin).' }}
                        </p>
                        <div class="flex flex-wrap items-center gap-4 pt-2">
                            <div class="px-4 py-2 rounded-xl bg-white/10 backdrop-blur-md border border-white/20 text-xs">
                                <span class="font-black text-[#FFC700] text-lg block">50+ Kolam</span>
                                <span class="text-slate-300 text-[11px]">Bioflok Intensif</span>
                            </div>
                            <div class="px-4 py-2 rounded-xl bg-white/10 backdrop-blur-md border border-white/20 text-xs">
                                <span class="font-black text-emerald-400 text-lg block">24 Jam</span>
                                <span class="text-slate-300 text-[11px]">Aerasi &amp; Pemuasaan</span>
                            </div>
                            <div class="px-4 py-2 rounded-xl bg-white/10 backdrop-blur-md border border-white/20 text-xs">
                                <span class="font-black text-sky-400 text-lg block">Tonase Rutin</span>
                                <span class="text-slate-300 text-[11px]">Pasokan Terjadwal</span>
                            </div>
                        </div>
                        <div class="pt-3">
                            <a href="{{ $settings['slide2_btn_link'] ?? '#sop' }}" class="inline-flex items-center gap-2 px-6 py-3 rounded-xl bg-[#FFC700] hover:bg-amber-300 text-slate-900 font-extrabold text-xs uppercase tracking-wider transition">
                                <i class="fa-solid fa-clipboard-check"></i>
                                <span>{{ $settings['slide2_btn_text'] ?? 'Pelajari Standar Mutu Budidaya' }}</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ================= SLIDE 3: PANEN SEGAR MUTU CBIB PRIMA 1 ================= -->
            <div class="w-full shrink-0 min-h-[580px] lg:min-h-[620px] relative py-16 sm:py-20 lg:py-24 bg-cover bg-center flex items-center"
                 style="background-image: url('{{ $settings['hero_slide3_url'] }}');">
                <div class="absolute inset-0 bg-gradient-to-r from-[#001c38]/95 via-[#00386b]/80 to-transparent"></div>
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10 w-full">
                    <div class="max-w-3xl space-y-4">
                        <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-amber-500/20 border border-amber-300/30 text-amber-300 text-xs font-heading font-extrabold uppercase tracking-widest">
                            <i class="fa-solid fa-fish"></i>
                            <span>{{ $settings['slide3_tag'] ?? 'PANEN SEGAR AIR TAWAR' }}</span>
                        </span>
                        <h2 class="font-heading text-3xl sm:text-5xl font-black text-white leading-tight tracking-tight uppercase">
                            {{ $settings['slide3_title'] ?? 'Grading Ketat Komoditas Unggulan Siap Konsumsi' }}
                        </h2>
                        <p class="text-sm sm:text-base text-slate-200 leading-relaxed">
                            {{ $settings['slide3_desc'] ?? 'Nila Merah Nirwana, Lele Sangkuriang, Patin Siam, dan Gurame Soang dipanen segar setiap pagi dan sore hari. Daging padat manis tanpa residu antibiotik dan siap diolah dapur kuliner Anda.' }}
                        </p>
                        <div class="flex flex-wrap items-center gap-3 pt-2">
                            <span class="px-3 py-1 rounded-lg bg-white/10 text-white font-bold text-xs">Nila Merah &bull; Rp 32k/kg</span>
                            <span class="px-3 py-1 rounded-lg bg-white/10 text-white font-bold text-xs">Lele &bull; Rp 24k/kg</span>
                            <span class="px-3 py-1 rounded-lg bg-white/10 text-white font-bold text-xs">Patin &bull; Rp 28k/kg</span>
                            <span class="px-3 py-1 rounded-lg bg-white/10 text-white font-bold text-xs">Gurame &bull; Rp 48k/kg</span>
                        </div>
                        <div class="pt-3">
                            <a href="{{ $settings['slide3_btn_link'] ?? '#katalog' }}" class="inline-flex items-center gap-2 px-6 py-3 rounded-xl bg-[#005aab] hover:bg-sky-600 text-white font-extrabold text-xs uppercase tracking-wider border border-white/20 transition">
                                <i class="fa-solid fa-list-check"></i>
                                <span>{{ $settings['slide3_btn_text'] ?? 'Lihat Katalog Komoditas Ikan' }}</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ================= SLIDE 4: LABORATORIUM & RISET KUALITAS AIR ================= -->
            <div class="w-full shrink-0 min-h-[580px] lg:min-h-[620px] relative py-16 sm:py-20 lg:py-24 bg-cover bg-center flex items-center"
                 style="background-image: url('{{ $settings['hero_slide4_url'] }}');">
                <div class="absolute inset-0 bg-gradient-to-r from-[#071738]/95 via-[#004b93]/85 to-[#071738]/70"></div>
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10 w-full">
                    <div class="max-w-3xl space-y-4">
                        <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-emerald-500/20 border border-emerald-300/30 text-emerald-300 text-xs font-heading font-extrabold uppercase tracking-widest">
                            <i class="fa-solid fa-flask-vial"></i>
                            <span>{{ $settings['slide4_tag'] ?? 'STANDAR CBIB PRIMA 1 KKP RI' }}</span>
                        </span>
                        <h2 class="font-heading text-3xl sm:text-5xl font-black text-white leading-tight tracking-tight uppercase">
                            {{ $settings['slide4_title'] ?? 'Monitoring Kualitas Air Real-Time & Uji Mikrobiologi' }}
                        </h2>
                        <p class="text-sm sm:text-base text-slate-200 leading-relaxed">
                            {{ $settings['slide4_desc'] ?? 'Pengujian parameter pH, Dissolved Oxygen (DO), suhu, dan nitrit dilakukan berkala oleh tenaga ahli budidaya berpengalaman. Menjamin ikan hidup sehat secara alami tanpa penambahan zat kimia sintetis berbahaya.' }}
                        </p>
                        <div class="pt-3">
                            <a href="{{ ($settings['slide4_btn_link'] ?? 'wa') === 'wa' ? 'https://wa.me/' . $company['phone_wa'] . '?text=' . urlencode('Halo Aquafarm Priangan, saya ingin mendapatkan informasi uji lab & sertifikasi CBIB.') : $settings['slide4_btn_link'] }}" 
                               target="{{ ($settings['slide4_btn_link'] ?? 'wa') === 'wa' ? '_blank' : '_self' }}"
                               class="inline-flex items-center gap-2 px-6 py-3 rounded-xl bg-emerald-500 hover:bg-emerald-400 text-slate-950 font-extrabold text-xs uppercase tracking-wider transition">
                                <i class="fa-solid fa-file-circle-check"></i>
                                <span>{{ $settings['slide4_btn_text'] ?? 'Verifikasi Sertifikasi Mutu' }}</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ================= SLIDE 5: MITRA KULINER RESTORAN & GROSIR ================= -->
            <div class="w-full shrink-0 min-h-[580px] lg:min-h-[620px] relative py-16 sm:py-20 lg:py-24 bg-cover bg-center flex items-center"
                 style="background-image: url('{{ $settings['hero_slide5_url'] }}');">
                <div class="absolute inset-0 bg-gradient-to-r from-[#1c0803]/95 via-[#7c2d12]/80 to-[#1c0803]/75"></div>
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10 w-full">
                    <div class="max-w-3xl space-y-4">
                        <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-amber-500/20 border border-amber-300/30 text-amber-300 text-xs font-heading font-extrabold uppercase tracking-widest">
                            <i class="fa-solid fa-utensils"></i>
                            <span>{{ $settings['slide5_tag'] ?? 'PASOKAN RESTORAN & GROSIR' }}</span>
                        </span>
                        <h2 class="font-heading text-3xl sm:text-5xl font-black text-white leading-tight tracking-tight uppercase">
                            {{ $settings['slide5_title'] ?? 'Pilihan Utama 50+ Rumah Makan & Hotel Jawa Barat' }}
                        </h2>
                        <p class="text-sm sm:text-base text-slate-200 leading-relaxed">
                            {{ $settings['slide5_desc'] ?? 'Jaminan kontinuitas pasokan harian tanpa putus stok. Dapatkan kontrak pasok rutin dengan harga stabil, penjadwalan panen subuh, dan spesifikasi bobot yang sesuai dengan menu andalan resto Anda.' }}
                        </p>
                        <div class="pt-3">
                            <a href="{{ $settings['slide5_btn_link'] ?? '#kalkulator' }}" class="inline-flex items-center gap-2 px-6 py-3 rounded-xl bg-[#FFC700] hover:bg-amber-300 text-slate-900 font-extrabold text-xs uppercase tracking-wider transition">
                                <i class="fa-solid fa-calculator"></i>
                                <span>{{ $settings['slide5_btn_text'] ?? 'Simulasi Pesanan Restoran' }}</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ================= SLIDE 6: KAWASAN BUDIDAYA SUNRISE & ARMADA LOGISTIK ================= -->
            <div class="w-full shrink-0 min-h-[580px] lg:min-h-[620px] relative py-16 sm:py-20 lg:py-24 bg-cover bg-center flex items-center"
                 style="background-image: url('{{ $settings['hero_slide6_url'] }}');">
                <div class="absolute inset-0 bg-gradient-to-r from-[#031024]/95 via-[#004b93]/80 to-[#031024]/60"></div>
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10 w-full">
                    <div class="max-w-3xl space-y-4">
                        <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-sky-500/20 border border-sky-300/30 text-sky-200 text-xs font-heading font-extrabold uppercase tracking-widest">
                            <i class="fa-solid fa-truck-fast"></i>
                            <span>{{ $settings['slide6_tag'] ?? 'LOGISTIK KHUSUS AIR BER-OKSIGEN' }}</span>
                        </span>
                        <h2 class="font-heading text-3xl sm:text-5xl font-black text-white leading-tight tracking-tight uppercase">
                            {{ $settings['slide6_title'] ?? 'Armada Aerasi Cepat Menjangkau Seluruh Jawa Barat' }}
                        </h2>
                        <p class="text-sm sm:text-base text-slate-200 leading-relaxed">
                            {{ $settings['slide6_desc'] ?? 'Dilengkapi box aerasi motor untuk pengiriman cepat lokal Priangan Timur, pickup tangki drum aerasi aktif, hingga truk tangki oksigen murni untuk partai tonase ke Bandung Raya dan Jabodetabek.' }}
                        </p>
                        <div class="pt-3">
                            <a href="{{ ($settings['slide6_btn_link'] ?? 'wa') === 'wa' ? 'https://wa.me/' . $company['phone_wa'] . '?text=' . urlencode('Halo Aquafarm Priangan, saya ingin konfirmasi rute armada pengiriman ikan ke lokasi saya.') : $settings['slide6_btn_link'] }}" 
                               target="{{ ($settings['slide6_btn_link'] ?? 'wa') === 'wa' ? '_blank' : '_self' }}"
                               class="inline-flex items-center gap-2 px-6 py-3 rounded-xl bg-[#005aab] hover:bg-sky-500 text-white font-extrabold text-xs uppercase tracking-wider border border-white/20 transition">
                                <i class="fa-brands fa-whatsapp text-base"></i>
                                <span>{{ $settings['slide6_btn_text'] ?? 'Konsultasi Rute & Jadwal Armada' }}</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        <!-- ================= FLOATING NAVIGATION BUTTONS (< & >) ================= -->
        <button type="button" 
                @click="prev()" 
                class="absolute left-4 top-1/2 -translate-y-1/2 w-11 h-11 rounded-full bg-black/40 hover:bg-[#005aab] border border-white/25 text-white flex items-center justify-center transition shadow-2xl opacity-75 hover:opacity-100 hover:scale-110 active:scale-95 cursor-pointer z-30"
                aria-label="Slide Sebelumnya"
                title="Slide Sebelumnya">
            <i class="fa-solid fa-chevron-left text-sm"></i>
        </button>

        <button type="button" 
                @click="next()" 
                class="absolute right-4 top-1/2 -translate-y-1/2 w-11 h-11 rounded-full bg-black/40 hover:bg-[#005aab] border border-white/25 text-white flex items-center justify-center transition shadow-2xl opacity-75 hover:opacity-100 hover:scale-110 active:scale-95 cursor-pointer z-30"
                aria-label="Slide Berikutnya"
                title="Slide Berikutnya">
            <i class="fa-solid fa-chevron-right text-sm"></i>
        </button>

        <!-- ================= CAROUSEL INDICATOR DOTS AT BOTTOM CENTER (PERSIS 6 DOTS ITB) ================= -->
        <div class="absolute bottom-6 left-0 right-0 z-30 flex items-center justify-center gap-2.5">
            <template x-for="i in totalSlides" :key="'itb-dot-' + i">
                <button type="button"
                        @click="goTo(i - 1)"
                        :class="activeSlide === (i - 1) ? 'w-8 bg-white shadow-md' : 'w-2.5 h-2.5 bg-white/40 hover:bg-white/80'"
                        class="h-2.5 rounded-full transition-all duration-300 cursor-pointer"
                        :title="'Slide ' + i"
                        :aria-label="'Buka Slide ' + i">
                </button>
            </template>
        </div>


    </section>

        <!-- Bottom Stat Strip (ITB Fact Sheet) -->
        <div class="border-t border-white/15 bg-[#071738]/90 backdrop-blur-sm">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-center divide-x divide-white/10">
                    <div class="px-2">
                        <div class="font-heading text-2xl sm:text-3xl font-black text-white">{{ $displayKolam }} <span class="text-xs font-normal text-sky-300">Unit</span></div>
                        <div class="font-heading text-[11px] font-semibold text-slate-300 uppercase tracking-wider mt-0.5">Kolam Aktif</div>
                    </div>
                    <div class="px-2">
                        <div class="font-heading text-2xl sm:text-3xl font-black text-white">&plusmn;{{ number_format($displayBiomassa, 0, ',', '.') }} <span class="text-xs font-normal text-sky-300">kg</span></div>
                        <div class="font-heading text-[11px] font-semibold text-slate-300 uppercase tracking-wider mt-0.5">Biomassa Panen</div>
                    </div>
                    <div class="px-2">
                        <div class="font-heading text-2xl sm:text-3xl font-black text-[#FFC700]">{{ $company['fcr_avg'] }}</div>
                        <div class="font-heading text-[11px] font-semibold text-slate-300 uppercase tracking-wider mt-0.5">Rasio FCR Teruji</div>
                    </div>
                    <div class="px-2">
                        <div class="font-heading text-2xl sm:text-3xl font-black text-emerald-400">&le; 2%</div>
                        <div class="font-heading text-[11px] font-semibold text-slate-300 uppercase tracking-wider mt-0.5">Garansi DDP Susut</div>
                    </div>
                </div>
            </div>
        </div>



    <!-- =========================================================================
        4. SECTION: BERITA UTAMA / KOMODITAS UNGGULAN (PERSIS GAYA "HEADLINE NEWS" ITB)
    ========================================================================= -->
    <section id="katalog" class="py-16 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <!-- Section Header ITB Style -->
            <div class="flex flex-col md:flex-row md:items-end justify-between gap-3 mb-4">
                <div>
                    <span class="text-[11px] font-heading uppercase tracking-widest text-[#005aab] font-extrabold flex items-center gap-1.5">
                        <i class="fa-solid fa-award text-amber-500"></i> Fokus Produksi &amp; Alokasi Terbanyak
                    </span>
                    <h2 class="font-heading text-2xl sm:text-3xl font-bold text-slate-900 tracking-tight mt-0.5">Komoditas Utama</h2>
                    <div class="itb-divider"></div>
                </div>
                <p class="text-xs text-slate-500 max-w-md leading-relaxed">
                    Spesialisasi budidaya kami bulan ini dengan jumlah kolam dan fase pemeliharaan aktif terbanyak, memastikan kontinuitas pasokan harian Anda.
                </p>
            </div>

            <!-- ITB Card Overlay 3-Column Grid (Diurutkan dari Kolam & Fase Terbanyak) -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 pt-2">
                @foreach($ikans->take(3) as $index => $ikan)
                <div class="itb-post-image rounded-lg shadow-sm border border-slate-200 bg-slate-900 group relative h-80 sm:h-96">
                    <img src="{{ $ikan->foto_url }}" 
                         alt="{{ $ikan->nama_ikan }}" 
                         class="w-full h-full object-cover opacity-85 group-hover:opacity-100">
                    <div class="absolute inset-0 bg-gradient-to-t from-black/90 via-black/40 to-transparent"></div>

                    <!-- Indikator Spesialisasi & Jumlah Kolam/Fase -->
                    <div class="absolute top-4 inset-x-4 flex items-center justify-between gap-2 pointer-events-none">
                        @if($index === 0 && ($ikan->total_kolam_aktif ?? 0) > 0)
                        <span class="px-2.5 py-1 rounded-full text-[10px] font-heading font-black uppercase tracking-wider bg-amber-400 text-slate-950 shadow-md inline-flex items-center gap-1.5">
                            <i class="fa-solid fa-star text-amber-900"></i> Spesialis Kami Bulan Ini
                        </span>
                        @elseif(($ikan->total_kolam_aktif ?? 0) > 0)
                        <span class="px-2.5 py-1 rounded-full text-[10px] font-heading font-bold uppercase tracking-wider bg-white/95 text-slate-900 shadow-md inline-flex items-center gap-1.5">
                            <i class="fa-solid fa-water text-sky-600"></i> {{ $ikan->total_kolam_aktif }} Kolam Aktif
                        </span>
                        @else
                        <span class="px-2.5 py-1 rounded-full text-[10px] font-heading font-bold uppercase tracking-wider bg-white/90 text-slate-800 shadow-md inline-flex items-center gap-1.5">
                            <i class="fa-solid fa-circle-check text-emerald-600"></i> Siap Pasok
                        </span>
                        @endif

                        @if(($ikan->total_fase_aktif ?? 0) > 0)
                        <span class="px-2.5 py-1 rounded-full text-[10px] font-heading font-bold bg-black/60 backdrop-blur-xs text-sky-200 border border-white/20 shadow-xs">
                            {{ $ikan->total_fase_aktif }} Fase Budidaya
                        </span>
                        @endif
                    </div>

                    <div class="absolute bottom-0 inset-x-0 p-5 space-y-2">
                        <div class="flex items-center gap-2 text-xs">
                            <span class="text-sky-300 font-semibold">{{ $ikan->target_konsumsi }}</span>
                            @if(($ikan->total_kolam_aktif ?? 0) > 0)
                            <span class="text-white/40">•</span>
                            <span class="text-amber-300 font-bold">{{ $ikan->total_kolam_aktif }} Kolam Aktif</span>
                            @endif
                        </div>
                        <h3 class="font-heading text-xl font-bold text-white leading-snug group-hover:text-sky-300 transition-colors">
                            {{ $ikan->nama_ikan }}
                        </h3>
                        <p class="text-xs text-slate-300 line-clamp-2">
                            {{ $ikan->tagline }}
                        </p>
                        <div class="pt-2 flex items-center justify-between border-t border-white/20">
                            <span class="text-base font-black text-white font-heading">Rp {{ number_format($ikan->harga_jual_kg, 0, ',', '.') }} <span class="text-xs font-normal text-slate-300">/ kg</span></span>
                            <a href="https://wa.me/{{ $company['phone_wa'] }}?text={{ urlencode('Halo Aquafarm Priangan, saya tertarik memesan ' . $ikan->nama_ikan . ' (' . $ikan->target_konsumsi . ') untuk kebutuhan usaha saya. Mohon info kuota panen hari ini.') }}" 
                               target="_blank"
                               class="text-xs font-heading font-bold text-sky-400 hover:text-white inline-flex items-center gap-1 transition">
                                Pesan via WA <i class="fa-solid fa-arrow-right text-[10px]"></i>
                            </a>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>

        </div>
    </section>

    <!-- =========================================================================
        5. SECTION: KATALOG LENGKAP & SPESIFIKASI MUTU (PERSIS GAYA "BERITA TERBARU" ITB)
    ========================================================================= -->
    <section class="py-12 bg-itb-cardgrey border-t border-b border-slate-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <div class="mb-2">
                <h2 class="font-heading text-2xl font-bold text-slate-900 tracking-tight">Katalog Spesies &amp; Spesifikasi Mutu</h2>
                <div class="itb-divider"></div>
            </div>

            <!-- 4-Column Grid ITB Latest News Format with Real Photos -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 pt-2">
                @foreach($ikans as $ikan)
                <div class="bg-white rounded-lg p-4 border border-slate-200 shadow-sm hover:shadow-md transition flex flex-col justify-between">
                    <div>
                        <!-- Fish Photo Thumbnail (ITB Style Image Frame) -->
                        <div class="itb-post-image rounded-md mb-3 h-36 overflow-hidden bg-slate-100 border border-slate-200/80">
                            <img src="{{ $ikan->foto_url }}" alt="{{ $ikan->nama_ikan }}" class="w-full h-full object-cover">
                        </div>

                        <!-- Header Badges -->
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-[10px] font-heading font-bold uppercase tracking-wider px-2 py-0.5 rounded bg-itb-primary/10 text-itb-primary">
                                {{ $ikan->target_konsumsi }}
                            </span>
                            @if(($ikan->total_kolam_aktif ?? 0) > 0)
                            <span class="text-[10px] font-bold text-sky-700 bg-sky-50 border border-sky-200/60 px-2 py-0.5 rounded">
                                {{ $ikan->total_kolam_aktif }} Kolam Aktif
                            </span>
                            @else
                            <span class="text-[10px] font-semibold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded">
                                {{ $ikan->stok_status }}
                            </span>
                            @endif
                        </div>

                        <h3 class="font-heading text-base font-bold text-slate-900 mb-1 leading-snug">
                            {{ $ikan->nama_ikan }}
                        </h3>
                        <p class="text-xs text-slate-500 mb-3">{{ $ikan->tagline }}</p>

                        <!-- Key Bullets -->
                        <ul class="space-y-1.5 text-xs text-slate-600 mb-4">
                            @foreach($ikan->keunggulan as $k)
                            <li class="flex items-start gap-1.5 text-[11px]">
                                <i class="fa-solid fa-check text-itb-primary mt-0.5 shrink-0"></i>
                                <span>{{ $k }}</span>
                            </li>
                            @endforeach
                        </ul>
                    </div>

                    <div class="pt-3 border-t border-slate-100 flex items-center justify-between">
                        <div>
                            <span class="text-[10px] text-slate-400 block leading-none font-heading">Harga Patokan:</span>
                            <span class="font-heading text-sm font-bold text-slate-900">
                                Rp {{ number_format($ikan->harga_jual_kg, 0, ',', '.') }}/kg
                            </span>
                        </div>

                        <a href="https://wa.me/{{ $company['phone_wa'] }}?text={{ urlencode('Halo Tim Aquafarm, saya ingin memesan ikan ' . $ikan->nama_ikan . ' (' . $ikan->target_konsumsi . ') untuk usaha saya.') }}" 
                           target="_blank"
                           class="px-2.5 py-1.5 rounded bg-itb-navy hover:bg-itb-primary text-white font-heading text-xs font-bold transition inline-flex items-center gap-1">
                            <i class="fa-brands fa-whatsapp text-xs text-emerald-400"></i> Order
                        </a>
                    </div>
                </div>
                @endforeach
            </div>

        </div>
    </section>

    <!-- =========================================================================
        6. SECTION: AGENDA PANEN & PENGIRIMAN (PERSIS GAYA "AGENDA ITB" KOTAK TANGGAL)
    ========================================================================= -->
    @if(($settings['show_agenda'] ?? '1') == '1')
    <section id="agenda" class="py-16 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <div class="mb-2">
                <h2 class="font-heading text-2xl sm:text-3xl font-bold text-slate-900 tracking-tight">Jadwal &amp; Agenda Panen</h2>
                <div class="itb-divider"></div>
            </div>

            <!-- ITB Agenda Date Box Grid (3 Columns) -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 pt-2">
                
                <!-- Agenda 1 -->
                <div class="bg-itb-cardgrey rounded-lg p-5 border border-slate-200 shadow-sm flex items-start gap-4 hover:border-itb-primary transition">
                    <!-- ITB Date Block -->
                    <div class="w-16 h-16 rounded bg-itb-primary text-white flex flex-col items-center justify-center shrink-0 shadow-sm">
                        <span class="font-heading text-2xl font-black leading-none">19</span>
                        <span class="font-heading text-[11px] uppercase tracking-wider font-semibold">OKT</span>
                    </div>
                    <div class="space-y-1">
                        <span class="text-[10px] font-heading font-bold uppercase tracking-wider text-itb-primary bg-sky-50 px-2 py-0.5 rounded">
                            05.00 – 08.00 WIB
                        </span>
                        <h3 class="font-heading text-base font-bold text-slate-900 leading-snug">
                            Panen Nila Merah Bioflok Kolam B-02
                        </h3>
                        <p class="text-xs text-slate-500">
                            Kuota panen estimasi 350 kg untuk rute pengantaran Ciamis &amp; Tasikmalaya.
                        </p>
                    </div>
                </div>

                <!-- Agenda 2 -->
                <div class="bg-itb-cardgrey rounded-lg p-5 border border-slate-200 shadow-sm flex items-start gap-4 hover:border-itb-primary transition">
                    <!-- ITB Date Block -->
                    <div class="w-16 h-16 rounded bg-itb-darkblue text-white flex flex-col items-center justify-center shrink-0 shadow-sm">
                        <span class="font-heading text-2xl font-black leading-none">20</span>
                        <span class="font-heading text-[11px] uppercase tracking-wider font-semibold">OKT</span>
                    </div>
                    <div class="space-y-1">
                        <span class="text-[10px] font-heading font-bold uppercase tracking-wider text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded">
                            Pemberokan
                        </span>
                        <h3 class="font-heading text-base font-bold text-slate-900 leading-snug">
                            Pemberokan &amp; Grading Lele Kolam C-01
                        </h3>
                        <p class="text-xs text-slate-500">
                            Pemuasaan 24 jam persiapan pengiriman grosir Bandung &amp; Priangan Timur.
                        </p>
                    </div>
                </div>

                <!-- Agenda 3 -->
                <div class="bg-itb-cardgrey rounded-lg p-5 border border-slate-200 shadow-sm flex items-start gap-4 hover:border-itb-primary transition">
                    <!-- ITB Date Block -->
                    <div class="w-16 h-16 rounded bg-itb-gold text-white flex flex-col items-center justify-center shrink-0 shadow-sm">
                        <span class="font-heading text-2xl font-black leading-none">22</span>
                        <span class="font-heading text-[11px] uppercase tracking-wider font-semibold">OKT</span>
                    </div>
                    <div class="space-y-1">
                        <span class="text-[10px] font-heading font-bold uppercase tracking-wider text-amber-800 bg-amber-50 px-2 py-0.5 rounded">
                            Kemitraan
                        </span>
                        <h3 class="font-heading text-base font-bold text-slate-900 leading-snug">
                            Kunjungan &amp; Studi Lapangan Calon Mitra Plasma
                        </h3>
                        <p class="text-xs text-slate-500">
                            Fasilitas Budidaya Buniseuri, Cipaku, Ciamis. Terbuka untuk restoran &amp; petani.
                        </p>
                    </div>
                </div>

            </div>

        </div>
    </section>
    @endif

    <!-- =========================================================================
        7. SECTION: KALKULATOR PESANAN & WHATSAPP GENERATOR (PORTAL SERVICE ITB STYLE)
    ========================================================================= -->
    @if(($settings['show_calculator'] ?? '1') == '1')
    <section id="kalkulator" class="py-16 bg-itb-navy text-white relative"
             x-data="{
                ikanSelected: '{{ $ikans->first()->nama_ikan ?? 'Ikan Nila' }}',
                hargaPerKg: {{ $ikans->first()->harga_jual_kg ?? 32000 }},
                qtyKg: 100,
                tipeUsaha: 'Restoran / Rumah Makan',
                tujuanKirim: 'Ciamis & Sekitarnya (Priangan Timur)',
                catatan: 'Minta dipuasakan 24 jam sebelum pengiriman.',
                
                opsiUsaha: [
                    { value: 'Restoran / Rumah Makan', desc: 'Pasokan harian siap olah & kualitas stabil', icon: 'fa-solid fa-utensils' },
                    { value: 'Lapak Pasar Tradisional / Grosir', desc: 'Partai grosir rutin harga kompetitif', icon: 'fa-solid fa-shop' },
                    { value: 'Katering & Pengadaan Event', desc: 'Jadwal presisi & request tonase khusus', icon: 'fa-solid fa-bowl-food' },
                    { value: 'Distributor / Pengepul Ikan', desc: 'Kontrak tonase rutin harian & mingguan', icon: 'fa-solid fa-truck-ramp-box' },
                    { value: 'Hotel, Villa & Resort Rekreasi', desc: 'Ikan premium konsumsi & kolam pancing', icon: 'fa-solid fa-hotel' }
                ],

                opsiTujuan: [
                    { value: 'Ciamis & Sekitarnya (Priangan Timur)', desc: 'Pengantaran armada lokal prioritas', icon: 'fa-solid fa-location-dot' },
                    { value: 'Kota / Kab. Tasikmalaya', desc: 'Jalur pasokan harian Priangan Timur', icon: 'fa-solid fa-city' },
                    { value: 'Kota Banjar & Pangandaran', desc: 'Rute timur & pesisir Jawa Barat', icon: 'fa-solid fa-umbrella-beach' },
                    { value: 'Garut & Sekitarnya', desc: 'Distribusi jalur poros selatan', icon: 'fa-solid fa-mountain' },
                    { value: 'Bandung Raya (Kota/Kab)', desc: 'Armada tangki drum aerasi aktif', icon: 'fa-solid fa-road' },
                    { value: 'Jabodetabek (Partai Besar)', desc: 'Khusus pesanan tonase armada besar', icon: 'fa-solid fa-truck-fast' },
                    { value: 'Wilayah Lainnya di Jawa Barat', desc: 'Konfirmasi titik temu & rute logistik', icon: 'fa-solid fa-map-location-dot' }
                ],
                
                updateIkan(nama, harga) {
                    this.ikanSelected = nama;
                    this.hargaPerKg = harga;
                },

                setQty(val) {
                    let n = parseInt(val, 10);
                    if (isNaN(n) || n < 10) n = 10;
                    if (n > 25000) n = 25000;
                    this.qtyKg = n;
                },

                adjustQty(delta) {
                    let n = (this.qtyKg || 0) + delta;
                    if (n < 10) n = 10;
                    if (n > 25000) n = 25000;
                    this.qtyKg = n;
                },

                onQtyInput(e) {
                    let raw = e.target.value.replace(/[^0-9]/g, '');
                    raw = raw.replace(/^0+/, ''); // Cegah input 0000000 berulang
                    if (raw.length > 5) {
                        raw = raw.slice(0, 5); // Maksimal 5 digit (hingga 25.000 kg)
                    }
                    let n = parseInt(raw, 10);
                    if (isNaN(n) || n === 0) {
                        this.qtyKg = 0;
                        e.target.value = '';
                        return;
                    }
                    if (n > 25000) {
                        n = 25000;
                        raw = '25000';
                    }
                    this.qtyKg = n;
                    e.target.value = raw;
                },

                onQtyBlur(e) {
                    if (!this.qtyKg || this.qtyKg < 10) {
                        this.qtyKg = 10;
                        e.target.value = '10';
                    }
                },
                
                get totalHarga() {
                    return (this.qtyKg || 0) * this.hargaPerKg;
                },

                get armada() {
                    let q = this.qtyKg || 0;
                    if (q <= 50) return 'Armada Motor Khusus Box Aerasi';
                    if (q <= 400) return 'Pickup Tangki Drum Aerasi Aktif';
                    return 'Truk Tangki Akuakultur Oksigen Murni';
                },

                get waLink() {
                    let q = this.qtyKg > 0 ? this.qtyKg : 10;
                    let text = 'Halo Tim Sales ' + '{{ $settings['brand_name'] ?? 'Aquafarm Priangan' }}' + ',\n\n' +
                               'Saya ingin mengajukan estimasi pesanan ikan segar:\n' +
                               '• Komoditas: ' + this.ikanSelected + '\n' +
                               '• Estimasi Volume: ' + new Intl.NumberFormat('id-ID').format(q) + ' kg\n' +
                               '• Estimasi Nilai Patokan: Rp ' + new Intl.NumberFormat('id-ID').format(this.totalHarga) + '\n' +
                               '• Kategori Usaha: ' + this.tipeUsaha + '\n' +
                               '• Wilayah Tujuan: ' + this.tujuanKirim + '\n' +
                               '• Rekomendasi Armada: ' + this.armada + '\n' +
                               '• Catatan Tambahan: ' + (this.catatan || '-') + '\n\n' +
                               'Mohon konfirmasi ketersediaan slot panen dan penawaran resminya. Terima kasih!';
                    return 'https://wa.me/{{ $company['phone_wa'] }}?text=' + encodeURIComponent(text);
                }
             }">
        
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <div class="mb-4">
                <span class="text-xs font-heading uppercase tracking-widest text-sky-400 font-bold">Layanan Mandiri Pelanggan</span>
                <h2 class="font-heading text-2xl sm:text-3xl font-bold text-white tracking-tight mt-1">Kalkulator Estimasi Pesanan &amp; WhatsApp Draft</h2>
                <div class="itb-divider itb-divider-gold"></div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start pt-2">
                
                <!-- Left: Interactive Form -->
                <div class="lg:col-span-7 bg-white/5 rounded-2xl p-6 sm:p-8 border border-white/10 space-y-6">
                    
                    <!-- 1. Pilih Komoditas Ikan -->
                    <div>
                        <label class="block font-heading text-xs uppercase tracking-wider text-slate-300 font-bold mb-2 flex items-center justify-between">
                            <span>1. Pilih Komoditas Ikan</span>
                            <span class="text-[11px] font-semibold text-sky-300" x-text="'Dipilih: ' + ikanSelected"></span>
                        </label>
                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-2.5">
                            @foreach($ikans as $item)
                            <button type="button" 
                                    @click="updateIkan('{{ $item->nama_ikan }}', {{ $item->harga_jual_kg }})"
                                    :class="ikanSelected === '{{ $item->nama_ikan }}' ? 'bg-[#005aab] text-white border-[#FFC700] ring-2 ring-[#FFC700]/30 font-bold shadow-md' : 'bg-slate-900/60 text-slate-300 border-white/15 hover:bg-slate-900/90'"
                                    class="p-3 rounded-xl border text-xs text-center transition font-heading cursor-pointer">
                                <div class="font-bold">{{ $item->nama_ikan }}</div>
                                <div class="text-[11px] font-semibold text-sky-300 mt-0.5">Rp {{ number_format($item->harga_jual_kg/1000) }}k / kg</div>
                            </button>
                            @endforeach
                        </div>
                    </div>

                    <!-- 2. Volume Pesanan (Dengan Limit Input & Stepper) -->
                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <label class="font-heading text-xs uppercase tracking-wider text-slate-300 font-bold flex items-center gap-1.5">
                                <i class="fa-solid fa-weight-scale text-[#FFC700]"></i>
                                <span>2. Volume Pesanan (Kilogram)</span>
                            </label>
                            <span class="font-heading text-sm font-black text-[#FFC700]" 
                                  x-text="(qtyKg > 0 ? new Intl.NumberFormat('id-ID').format(qtyKg) : 0) + ' kg'"></span>
                        </div>

                        <!-- Stepper & Input dengan Limit Karakter & Batas Max 25.000 kg -->
                        <div class="flex items-center gap-2">
                            <button type="button" 
                                    @click="adjustQty(-50)" 
                                    class="w-12 h-12 rounded-xl bg-slate-800 hover:bg-slate-700 active:scale-95 border border-white/20 text-white font-black text-lg flex items-center justify-center transition cursor-pointer shrink-0"
                                    title="Kurangi 50 kg">
                                <i class="fa-solid fa-minus text-sm"></i>
                            </button>

                            <div class="relative flex-1">
                                <input type="text" 
                                       inputmode="numeric" 
                                       :value="qtyKg || ''" 
                                       @input="onQtyInput($event)" 
                                       @blur="onQtyBlur($event)"
                                       maxlength="5"
                                       placeholder="10 – 25.000"
                                       class="w-full bg-[#0a1b38] border-2 border-white/20 focus:border-[#FFC700] rounded-xl pl-4 pr-12 py-3 text-lg font-black text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-[#FFC700]/30 transition shadow-inner">
                                <span class="absolute right-4 top-1/2 -translate-y-1/2 text-xs font-bold text-slate-400 pointer-events-none">
                                    KG
                                </span>
                            </div>

                            <button type="button" 
                                    @click="adjustQty(50)" 
                                    class="w-12 h-12 rounded-xl bg-slate-800 hover:bg-slate-700 active:scale-95 border border-white/20 text-white font-black text-lg flex items-center justify-center transition cursor-pointer shrink-0"
                                    title="Tambah 50 kg">
                                <i class="fa-solid fa-plus text-sm"></i>
                            </button>
                        </div>
                        
                        <!-- Pilihan Cepat & Petunjuk Batas -->
                        <div class="flex flex-wrap items-center justify-between gap-2 mt-2.5">
                            <div class="flex flex-wrap items-center gap-1.5">
                                <span class="text-[10px] uppercase font-bold text-slate-400 mr-1">Pilihan Cepat:</span>
                                <button type="button" @click="setQty(50)" :class="qtyKg === 50 ? 'bg-[#FFC700] text-slate-900 font-bold' : 'bg-white/10 hover:bg-white/20 text-slate-200'" class="px-2.5 py-1 rounded-lg text-xs font-semibold transition cursor-pointer">50 kg</button>
                                <button type="button" @click="setQty(100)" :class="qtyKg === 100 ? 'bg-[#FFC700] text-slate-900 font-bold' : 'bg-white/10 hover:bg-white/20 text-slate-200'" class="px-2.5 py-1 rounded-lg text-xs font-semibold transition cursor-pointer">100 kg</button>
                                <button type="button" @click="setQty(250)" :class="qtyKg === 250 ? 'bg-[#FFC700] text-slate-900 font-bold' : 'bg-white/10 hover:bg-white/20 text-slate-200'" class="px-2.5 py-1 rounded-lg text-xs font-semibold transition cursor-pointer">250 kg</button>
                                <button type="button" @click="setQty(500)" :class="qtyKg === 500 ? 'bg-[#FFC700] text-slate-900 font-bold' : 'bg-white/10 hover:bg-white/20 text-slate-200'" class="px-2.5 py-1 rounded-lg text-xs font-semibold transition cursor-pointer">500 kg</button>
                                <button type="button" @click="setQty(1000)" :class="qtyKg === 1000 ? 'bg-[#FFC700] text-slate-900 font-bold' : 'bg-white/10 hover:bg-white/20 text-slate-200'" class="px-2.5 py-1 rounded-lg text-xs font-semibold transition cursor-pointer">1 Ton</button>
                                <button type="button" @click="setQty(5000)" :class="qtyKg === 5000 ? 'bg-[#FFC700] text-slate-900 font-bold' : 'bg-white/10 hover:bg-white/20 text-slate-200'" class="px-2.5 py-1 rounded-lg text-xs font-semibold transition cursor-pointer">5 Ton</button>
                            </div>
                            <span class="text-[10px] text-slate-400 font-medium">Batas: Min 10 kg &bull; Maks 25.000 kg</span>
                        </div>
                    </div>

                    <!-- 3. Layout Dropdown Modern & Rapi (Custom Glassmorphic Alpine Dropdowns) -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 sm:gap-5 pt-1">
                        
                        <!-- Dropdown 1: Kategori Usaha -->
                        <div class="space-y-1.5" x-data="{ openUsaha: false }" @click.outside="openUsaha = false">
                            <label class="block font-heading text-xs uppercase tracking-wider text-slate-300 font-bold flex items-center justify-between">
                                <span class="flex items-center gap-1.5">
                                    <i class="fa-solid fa-store text-sky-400 text-xs"></i>
                                    <span>3. Kategori Usaha Anda</span>
                                </span>
                            </label>

                            <div class="relative">
                                <!-- Trigger Button -->
                                <button type="button" 
                                        @click="openUsaha = !openUsaha"
                                        :class="openUsaha ? 'border-[#FFC700] ring-2 ring-[#FFC700]/30 bg-[#0e244d]' : 'border-white/20 hover:border-white/40 bg-[#0a1b38]'"
                                        class="w-full flex items-center justify-between border-2 rounded-xl px-4 py-3 text-xs sm:text-sm font-semibold text-white transition shadow-inner cursor-pointer text-left">
                                    <div class="flex items-center gap-2.5 truncate">
                                        <i class="fa-solid fa-store text-sky-400 text-xs shrink-0"></i>
                                        <span class="truncate font-heading" x-text="tipeUsaha"></span>
                                    </div>
                                    <i class="fa-solid fa-chevron-down text-xs text-sky-400 transition-transform duration-200 shrink-0 ml-2"
                                       :class="openUsaha ? 'rotate-180 text-[#FFC700]' : ''"></i>
                                </button>

                                <!-- Custom Glassmorphic Popup Menu -->
                                <div x-show="openUsaha" 
                                     x-cloak
                                     x-transition:enter="transition ease-out duration-150"
                                     x-transition:enter-start="opacity-0 -translate-y-1 scale-98"
                                     x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                                     x-transition:leave="transition ease-in duration-100"
                                     x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                                     x-transition:leave-end="opacity-0 -translate-y-1 scale-98"
                                     class="absolute z-50 left-0 right-0 mt-2 bg-[#091830]/95 backdrop-blur-xl border-2 border-white/20 rounded-xl shadow-2xl p-1.5 space-y-1 max-h-72 overflow-y-auto custom-scrollbar">
                                     
                                    <template x-for="item in opsiUsaha" :key="item.value">
                                        <button type="button"
                                                @click="tipeUsaha = item.value; openUsaha = false"
                                                :class="tipeUsaha === item.value ? 'bg-[#005aab] text-white font-bold shadow-md' : 'text-slate-200 hover:bg-white/10 hover:text-white'"
                                                class="w-full text-left px-3 py-2.5 rounded-lg text-xs transition flex items-center justify-between cursor-pointer group">
                                            <div class="flex items-start gap-2.5">
                                                <i :class="item.icon" class="text-xs text-sky-400 shrink-0 w-4 text-center mt-0.5 group-hover:scale-110 transition-transform"></i>
                                                <div>
                                                    <div class="font-semibold leading-snug" x-text="item.value"></div>
                                                    <div class="text-[10px] text-slate-400 leading-tight mt-0.5" x-text="item.desc"></div>
                                                </div>
                                            </div>
                                            <i x-show="tipeUsaha === item.value" class="fa-solid fa-check text-xs text-[#FFC700] ml-2 shrink-0"></i>
                                        </button>
                                    </template>
                                </div>
                            </div>
                        </div>

                        <!-- Dropdown 2: Wilayah Pengantaran -->
                        <div class="space-y-1.5" x-data="{ openTujuan: false }" @click.outside="openTujuan = false">
                            <label class="block font-heading text-xs uppercase tracking-wider text-slate-300 font-bold flex items-center justify-between">
                                <span class="flex items-center gap-1.5">
                                    <i class="fa-solid fa-location-dot text-sky-400 text-xs"></i>
                                    <span>4. Wilayah Pengantaran</span>
                                </span>
                            </label>

                            <div class="relative">
                                <!-- Trigger Button -->
                                <button type="button" 
                                        @click="openTujuan = !openTujuan"
                                        :class="openTujuan ? 'border-[#FFC700] ring-2 ring-[#FFC700]/30 bg-[#0e244d]' : 'border-white/20 hover:border-white/40 bg-[#0a1b38]'"
                                        class="w-full flex items-center justify-between border-2 rounded-xl px-4 py-3 text-xs sm:text-sm font-semibold text-white transition shadow-inner cursor-pointer text-left">
                                    <div class="flex items-center gap-2.5 truncate">
                                        <i class="fa-solid fa-location-dot text-sky-400 text-xs shrink-0"></i>
                                        <span class="truncate font-heading" x-text="tujuanKirim"></span>
                                    </div>
                                    <i class="fa-solid fa-chevron-down text-xs text-sky-400 transition-transform duration-200 shrink-0 ml-2"
                                       :class="openTujuan ? 'rotate-180 text-[#FFC700]' : ''"></i>
                                </button>

                                <!-- Custom Glassmorphic Popup Menu -->
                                <div x-show="openTujuan" 
                                     x-cloak
                                     x-transition:enter="transition ease-out duration-150"
                                     x-transition:enter-start="opacity-0 -translate-y-1 scale-98"
                                     x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                                     x-transition:leave="transition ease-in duration-100"
                                     x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                                     x-transition:leave-end="opacity-0 -translate-y-1 scale-98"
                                     class="absolute z-50 left-0 right-0 mt-2 bg-[#091830]/95 backdrop-blur-xl border-2 border-white/20 rounded-xl shadow-2xl p-1.5 space-y-1 max-h-72 overflow-y-auto custom-scrollbar">
                                     
                                    <template x-for="item in opsiTujuan" :key="item.value">
                                        <button type="button"
                                                @click="tujuanKirim = item.value; openTujuan = false"
                                                :class="tujuanKirim === item.value ? 'bg-[#005aab] text-white font-bold shadow-md' : 'text-slate-200 hover:bg-white/10 hover:text-white'"
                                                class="w-full text-left px-3 py-2.5 rounded-lg text-xs transition flex items-center justify-between cursor-pointer group">
                                            <div class="flex items-start gap-2.5">
                                                <i :class="item.icon" class="text-xs text-sky-400 shrink-0 w-4 text-center mt-0.5 group-hover:scale-110 transition-transform"></i>
                                                <div>
                                                    <div class="font-semibold leading-snug" x-text="item.value"></div>
                                                    <div class="text-[10px] text-slate-400 leading-tight mt-0.5" x-text="item.desc"></div>
                                                </div>
                                            </div>
                                            <i x-show="tujuanKirim === item.value" class="fa-solid fa-check text-xs text-[#FFC700] ml-2 shrink-0"></i>
                                        </button>
                                    </template>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- 4. Catatan Khusus -->
                    <div class="space-y-1.5 pt-1">
                        <label class="block font-heading text-xs uppercase tracking-wider text-slate-300 font-bold flex items-center gap-1.5">
                            <i class="fa-solid fa-note-sticky text-sky-400 text-xs"></i>
                            <span>5. Catatan Khusus Pesanan</span>
                        </label>
                        <div class="relative">
                            <input type="text" 
                                   x-model="catatan" 
                                   maxlength="200"
                                   placeholder="Contoh: Minta dipuasakan 24 jam, pengiriman subuh jam 06.00 WIB"
                                   class="w-full bg-[#0a1b38] border-2 border-white/20 focus:border-[#FFC700] rounded-xl px-4 py-3 text-xs sm:text-sm text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-[#FFC700]/30 transition shadow-inner">
                        </div>
                    </div>

                </div>

                <!-- Right: Summary Card -->
                <div class="lg:col-span-5 bg-white text-slate-900 rounded-2xl p-6 sm:p-8 shadow-2xl border border-slate-200">
                    <span class="text-[11px] font-heading uppercase tracking-wider text-[#005aab] font-bold block">
                        Rangkuman Estimasi Pesanan
                    </span>
                    <h3 class="font-heading text-2xl font-black text-slate-900 mt-1" 
                        x-text="ikanSelected + ' (' + new Intl.NumberFormat('id-ID').format(qtyKg > 0 ? qtyKg : 0) + ' kg)'"></h3>

                    <div class="my-5 space-y-3 text-xs text-slate-600 border-t border-b border-slate-100 py-4">
                        <div class="flex justify-between items-center">
                            <span>Harga Patokan Satuan:</span>
                            <span class="font-bold text-slate-900" x-text="'Rp ' + new Intl.NumberFormat('id-ID').format(hargaPerKg) + ' / kg'"></span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span>Kebutuhan Volume:</span>
                            <span class="font-bold text-slate-900" x-text="new Intl.NumberFormat('id-ID').format(qtyKg > 0 ? qtyKg : 0) + ' kg'"></span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span>Armada Logistik:</span>
                            <span class="font-bold text-[#005aab]" x-text="armada"></span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span>Standar Pemuasaan:</span>
                            <span class="font-bold text-emerald-600">18–24 Jam (Air Deras)</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span>Garansi Penyerahan:</span>
                            <span class="font-bold text-emerald-600">DDP (Susut Hidup &le; 2%)</span>
                        </div>
                    </div>

                    <div class="mb-5">
                        <span class="text-xs text-slate-500 block font-medium">Estimasi Nilai Total Patokan:</span>
                        <div class="font-heading text-3xl font-black text-[#004b93] mt-0.5" 
                             x-text="'Rp ' + new Intl.NumberFormat('id-ID').format(totalHarga)"></div>
                        <span class="text-[10px] text-slate-400 block mt-1">*Penyesuaian diskon volume berlaku untuk kontrak pasok rutin restoran.</span>
                    </div>

                    <!-- Direct Send to WhatsApp Button -->
                    <a :href="waLink" target="_blank"
                       :class="(!qtyKg || qtyKg < 10) ? 'opacity-60 cursor-not-allowed pointer-events-none' : ''"
                       class="w-full inline-flex items-center justify-center gap-2.5 px-6 py-4 rounded-xl font-heading text-sm uppercase tracking-wider font-extrabold text-white bg-emerald-600 hover:bg-emerald-500 shadow-lg shadow-emerald-600/30 active:scale-95 transition-all cursor-pointer">
                        <i class="fa-brands fa-whatsapp text-xl"></i>
                        <span>Kirim Rincian ke WhatsApp</span>
                    </a>

                    <p x-show="!qtyKg || qtyKg < 10" class="text-center text-[11px] text-amber-600 font-semibold mt-2">
                        *Masukkan minimal volume 10 kg untuk mengirim pesanan via WhatsApp.
                    </p>
                </div>

            </div>

        </div>
    </section>
    @endif

    <!-- =========================================================================
        8. SECTION: STANDAR MUTU & RISET AKUAKULTUR (PERSIS GAYA "RISET & INOVASI" ITB)
    ========================================================================= -->
    <section id="sop" class="py-16 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <div class="mb-2">
                <h2 class="font-heading text-2xl sm:text-3xl font-bold text-slate-900 tracking-tight">Standar Operasional Prosedur (SOP) Mutu</h2>
                <div class="itb-divider"></div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 pt-2">
                
                <div class="border border-slate-200 rounded-lg p-5 hover:border-itb-primary transition">
                    <div class="w-10 h-10 rounded bg-itb-primary text-white flex items-center justify-center text-lg mb-4">
                        <i class="fa-solid fa-faucet-detergent"></i>
                    </div>
                    <h3 class="font-heading text-base font-bold text-slate-900 mb-1.5">Pemberokan 24 Jam</h3>
                    <p class="text-xs text-slate-600 leading-relaxed">
                        Ikan dipindahkan ke bak air mengalir deras sebelum panen untuk membuang seluruh sisa pencernaan, melenyapkan bau lumpur (residu <em>geosmin</em>).
                    </p>
                </div>

                <div class="border border-slate-200 rounded-lg p-5 hover:border-itb-primary transition">
                    <div class="w-10 h-10 rounded bg-emerald-600 text-white flex items-center justify-center text-lg mb-4">
                        <i class="fa-solid fa-leaf"></i>
                    </div>
                    <h3 class="font-heading text-base font-bold text-slate-900 mb-1.5">Pakan Mix Nutrisi Tinggi</h3>
                    <p class="text-xs text-slate-600 leading-relaxed">
                        Formulasi pelet protein 30% berpadu dedaunan fermentasi (talas &amp; pepaya) serta probiotik bioflok aktif guna menekan FCR ke angka &lt; 1.2.
                    </p>
                </div>

                <div class="border border-slate-200 rounded-lg p-5 hover:border-itb-primary transition">
                    <div class="w-10 h-10 rounded bg-amber-500 text-white flex items-center justify-center text-lg mb-4">
                        <i class="fa-solid fa-truck-fast"></i>
                    </div>
                    <h3 class="font-heading text-base font-bold text-slate-900 mb-1.5">Garansi DDP &amp; Oksigen</h3>
                    <p class="text-xs text-slate-600 leading-relaxed">
                        Sistem <em>Delivery Duty Paid</em> (DDP): Risiko perjalanan ditanggung pihak budidaya hingga serah terima di dapur mitra. Toleransi susut &le; 2%.
                    </p>
                </div>

                <div class="border border-slate-200 rounded-lg p-5 hover:border-itb-primary transition">
                    <div class="w-10 h-10 rounded bg-rose-600 text-white flex items-center justify-center text-lg mb-4">
                        <i class="fa-solid fa-temperature-arrow-down"></i>
                    </div>
                    <h3 class="font-heading text-base font-bold text-slate-900 mb-1.5">Panen Suhu Dingin</h3>
                    <p class="text-xs text-slate-600 leading-relaxed">
                        Penjaringan hanya dilakukan saat suhu air sejuk (pagi 05.00–08.00 WIB atau sore 16.00–18.00 WIB) guna mencegah stres termal matahari.
                    </p>
                </div>

            </div>

        </div>
    </section>

    <!-- =========================================================================
        9. SECTION: KEMITRAAN & INVESTOR (PERSIS GAYA "PARTNERSHIP ITB")
    ========================================================================= -->
    @if(($settings['show_investor'] ?? '1') == '1')
    <section id="investor" class="py-16 bg-itb-lightgrey border-t border-b border-slate-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <div class="mb-2">
                <span class="text-xs font-heading uppercase tracking-widest text-itb-primary font-bold">Kolaborasi &amp; Investasi</span>
                <h2 class="font-heading text-2xl sm:text-3xl font-bold text-slate-900 tracking-tight mt-1">Peluang Kemitraan &amp; Skalabilitas Budidaya</h2>
                <div class="itb-divider"></div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 pt-2">
                
                <div class="bg-white rounded-lg p-6 border border-slate-200 shadow-sm flex flex-col justify-between">
                    <div>
                        <span class="px-2 py-0.5 rounded text-[10px] font-heading font-bold uppercase tracking-wider bg-itb-primary text-white">Skema 01</span>
                        <h3 class="font-heading text-lg font-bold text-slate-900 mt-2 mb-2">Kontrak Pasok Rutin Restoran</h3>
                        <p class="text-xs text-slate-600 leading-relaxed">
                            Bagi pengusaha restoran sunda, hotel, dan katering: Jaminan suplai harian/mingguan dengan harga kontrak stabil dan prioritas panen subuh bebas bau lumpur.
                        </p>
                    </div>
                    <div class="pt-4 mt-4 border-t border-slate-100">
                        <a href="https://wa.me/{{ $company['phone_wa'] }}?text={{ urlencode('Halo Aquafarm, saya pemilik restoran dan ingin membahas kontrak pasok ikan rutin.') }}" 
                           target="_blank" 
                           class="text-xs font-heading font-bold text-itb-primary hover:underline">
                            Konsultasi Pasok Resto &rarr;
                        </a>
                    </div>
                </div>

                <div class="bg-white rounded-lg p-6 border border-slate-200 shadow-sm flex flex-col justify-between">
                    <div>
                        <span class="px-2 py-0.5 rounded text-[10px] font-heading font-bold uppercase tracking-wider bg-emerald-600 text-white">Skema 02</span>
                        <h3 class="font-heading text-lg font-bold text-slate-900 mt-2 mb-2">Kemitraan Plasma Kolam Budidaya</h3>
                        <p class="text-xs text-slate-600 leading-relaxed">
                            Bagi pemilik lahan/kolam di Ciamis &amp; Tasikmalaya: Pasokan benih bersertifikat, pendampingan SOP manajemen pakan &amp; air, serta jaminan serap panen total.
                        </p>
                    </div>
                    <div class="pt-4 mt-4 border-t border-slate-100">
                        <a href="https://wa.me/{{ $company['phone_wa'] }}?text={{ urlencode('Halo Aquafarm, saya tertarik bergabung sebagai mitra plasma kolam budidaya.') }}" 
                           target="_blank" 
                           class="text-xs font-heading font-bold text-emerald-600 hover:underline">
                            Daftar Mitra Plasma &rarr;
                        </a>
                    </div>
                </div>

                <div class="bg-white rounded-lg p-6 border border-slate-200 shadow-sm flex flex-col justify-between">
                    <div>
                        <span class="px-2 py-0.5 rounded text-[10px] font-heading font-bold uppercase tracking-wider bg-itb-gold text-white">Skema 03</span>
                        <h3 class="font-heading text-lg font-bold text-slate-900 mt-2 mb-2">Investasi Modal Ekspansi Unit Kolam</h3>
                        <p class="text-xs text-slate-600 leading-relaxed">
                            Peluang penyertaan modal per unit kolam bioflok dengan sistem bagi hasil siklus panen riil, didukung transparansi buku kas terverifikasi sistem informasi.
                        </p>
                    </div>
                    <div class="pt-4 mt-4 border-t border-slate-100">
                        <a href="https://wa.me/{{ $company['phone_wa'] }}?text={{ urlencode('Halo Manajemen Aquafarm, saya tertarik mendiskusikan peluang investasi budidaya modern.') }}" 
                           target="_blank" 
                           class="text-xs font-heading font-bold text-amber-600 hover:underline">
                            Diskusi Investasi via WA &rarr;
                        </a>
                    </div>
                </div>

            </div>

        </div>
    </section>
    @endif

    <!-- =========================================================================
        10. SECTION: LOKASI & KONTAK RESMI (PERSIS GAYA MULTIKAMPUS / KONTAK ITB)
    ========================================================================= -->
    <section id="kontak" class="py-16 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <div class="mb-2">
                <h2 class="font-heading text-2xl sm:text-3xl font-bold text-slate-900 tracking-tight">Fasilitas &amp; Kontak Resmi</h2>
                <div class="itb-divider"></div>
            </div>

            <!-- ITB Contact Cards Grid (4 Cards with Blue Top Border) -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 pt-2">
                
                <div class="bg-white rounded-lg p-5 border border-slate-200 border-t-4 border-t-itb-primary shadow-sm space-y-2">
                    <h3 class="font-heading text-base font-bold text-itb-darkblue flex items-center gap-2">
                        <i class="fa-solid fa-map-location-dot text-itb-primary"></i> Lokasi Budidaya
                    </h3>
                    <p class="text-xs text-slate-600 leading-relaxed">
                        {{ $company['address'] }}
                    </p>
                    <span class="text-[11px] text-slate-400 block pt-1">Priangan Timur, Jawa Barat</span>
                </div>

                <div class="bg-white rounded-lg p-5 border border-slate-200 border-t-4 border-t-itb-primary shadow-sm space-y-2">
                    <h3 class="font-heading text-base font-bold text-itb-darkblue flex items-center gap-2">
                        <i class="fa-solid fa-clock text-itb-primary"></i> Jam Operasional
                    </h3>
                    <p class="text-xs text-slate-600 leading-relaxed">
                        {{ $company['harvest_time'] }}
                    </p>
                    <span class="text-[11px] text-emerald-600 font-semibold block pt-1">Buka Setiap Hari</span>
                </div>

                <div class="bg-white rounded-lg p-5 border border-slate-200 border-t-4 border-t-itb-primary shadow-sm space-y-2">
                    <h3 class="font-heading text-base font-bold text-itb-darkblue flex items-center gap-2">
                        <i class="fa-brands fa-whatsapp text-emerald-500"></i> Hotline Penjualan
                    </h3>
                    <p class="text-xs text-slate-600 leading-relaxed">
                        WhatsApp: {{ $company['phone_raw'] }}<br>
                        Email: {{ $company['email'] }}
                    </p>
                    <a href="https://wa.me/{{ $company['phone_wa'] }}" target="_blank" class="text-[11px] font-bold text-emerald-600 hover:underline block pt-1">
                        Chat Langsung &rarr;
                    </a>
                </div>

                <div class="bg-white rounded-lg p-5 border border-slate-200 border-t-4 border-t-itb-primary shadow-sm space-y-2">
                    <h3 class="font-heading text-base font-bold text-itb-darkblue flex items-center gap-2">
                        <i class="fa-solid fa-shield-halved text-itb-primary"></i> Jaminan Mutu
                    </h3>
                    <p class="text-xs text-slate-600 leading-relaxed">
                        Garansi DDP Toleransi Susut &le; 2% • Bebas Bau Lumpur • Pakan Nutrisi Alami
                    </p>
                    <span class="text-[11px] text-sky-700 font-semibold block pt-1">Standar SOP Resmi</span>
                </div>

            </div>

        </div>
    </section>

    <!-- =========================================================================
        11. FOOTER INSTITUSIONAL (PERSIS GAYA FOOTER DARK NAVY ITB #1a284d)
    ========================================================================= -->
    <footer class="bg-itb-navy text-slate-400 text-xs border-t border-navy-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8 mb-8">
                
                <div class="space-y-3 md:col-span-2">
                    <div class="flex items-center gap-3">
                        <img src="{{ asset('images/Logo aquafarm.png') }}" alt="Aquafarm" class="w-8 h-8 object-contain">
                        <span class="font-heading text-lg font-black text-white tracking-tight">AQUAFARM PRIANGAN</span>
                    </div>
                    <p class="text-xs text-slate-400 max-w-md leading-relaxed">
                        Sentra budidaya dan rantai pasok ikan air tawar modern di Ciamis, Jawa Barat. Menghasilkan ikan konsumsi berkualitas tinggi bebas bau lumpur dengan jaminan hidup DDP sampai tujuan.
                    </p>
                </div>

                <div>
                    <h4 class="font-heading text-xs font-bold text-white uppercase tracking-wider mb-3">Direktori Menu</h4>
                    <ul class="space-y-2 text-xs">
                        <li><a href="#katalog" class="hover:text-sky-300 transition">Komoditas Ikan</a></li>
                        <li><a href="#sop" class="hover:text-sky-300 transition">Standar Mutu (SOP)</a></li>
                        <li><a href="#kalkulator" class="hover:text-sky-300 transition">Kalkulator Pesanan</a></li>
                        <li><a href="#agenda" class="hover:text-sky-300 transition">Agenda Panen</a></li>
                        <li><a href="#investor" class="hover:text-sky-300 transition">Kemitraan &amp; Investor</a></li>
                    </ul>
                </div>

                <div>
                    <h4 class="font-heading text-xs font-bold text-white uppercase tracking-wider mb-3">Hubungi Kami</h4>
                    <ul class="space-y-2 text-xs">
                        <li><i class="fa-solid fa-location-dot text-sky-400 mr-1.5"></i> Buniseuri, Cipaku, Ciamis</li>
                        <li><i class="fa-solid fa-envelope text-sky-400 mr-1.5"></i> {{ $company['email'] }}</li>
                        <li><i class="fa-brands fa-whatsapp text-emerald-400 mr-1.5"></i> {{ $company['phone_raw'] }}</li>
                    </ul>
                </div>

            </div>

            <!-- Bottom Line with Copyright & Subtle Staff Portal -->
            <div class="pt-6 border-t border-white/10 flex flex-col sm:flex-row items-center justify-between gap-3 text-[11px] text-slate-400">
                <p>&copy; 2026 Aquafarm Priangan (PT AMS Budidaya Nusantara). Hak Cipta Dilindungi.</p>
                <div>
                    <a href="{{ route('login') }}" class="text-slate-400 hover:text-slate-400 transition text-[10px]">
                        Portal Staf &amp; Manajer
                    </a>
                </div>
            </div>
        </div>
    </footer>

    <!-- =========================================================================
        12. FLOATING WHATSAPP BUTTON (POJOK KANAN BAWAH)
    ========================================================================= -->
    <aside aria-label="Kontak Cepat WhatsApp" class="fixed bottom-6 right-6 z-50 flex items-center group">
        <div class="hidden sm:block mr-3 px-3 py-1.5 rounded bg-itb-navy text-white text-xs font-heading font-semibold shadow-xl border border-white/10 opacity-0 group-hover:opacity-100 transition-opacity duration-300 pointer-events-none">
            💬 Chat WhatsApp Tim Sales
        </div>
        <a href="https://wa.me/{{ $company['phone_wa'] }}?text={{ urlencode('Halo Aquafarm Priangan, saya ingin berkonsultasi mengenai pemesanan komoditas ikan air tawar.') }}" 
           target="_blank"
           class="w-14 h-14 rounded-full bg-emerald-500 hover:bg-emerald-400 text-white flex items-center justify-center shadow-xl shadow-emerald-500/40 hover:scale-105 active:scale-95 transition-all">
            <i class="fa-brands fa-whatsapp text-3xl"></i>
        </a>
    </aside>

</body>
</html>

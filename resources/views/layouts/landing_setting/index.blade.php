@extends('layouts.app')

@section('title', 'Kelola Tema & Konten Landing Page - AMS BUDIDAYA')

@section('content')
<div class="space-y-6" x-data="landingSettingComponent()">

    <!-- Top Flash Alert -->
    @if(session('status'))
    <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-bold flex items-center justify-between shadow-xs">
        <div class="flex items-center gap-3">
            <div class="w-8 h-8 rounded-xl bg-emerald-500 text-white flex items-center justify-center text-sm shadow-sm">
                <i class="fa-solid fa-circle-check"></i>
            </div>
            <span>{{ session('status') }}</span>
        </div>
        <button type="button" @click="$el.parentElement.remove()" class="text-emerald-600 hover:text-emerald-800">
            <i class="fa-solid fa-xmark text-sm"></i>
        </button>
    </div>
    @endif

    @if(isset($errors) && $errors->any())
    <div class="p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 text-xs font-bold space-y-1 shadow-xs">
        <div class="flex items-center gap-2 text-rose-700 font-extrabold text-sm mb-1">
            <i class="fa-solid fa-triangle-exclamation"></i>
            <span>Terdapat beberapa kesalahan pengisian:</span>
        </div>
        <ul class="list-disc list-inside space-y-0.5 font-semibold text-rose-600">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <!-- Top Header Banner -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 bg-white p-6 rounded-3xl border border-slate-200/80 shadow-xs">
        <div>
            <div class="flex items-center gap-2 text-xs font-extrabold uppercase tracking-wider text-[#005aab] mb-1">
                <i class="fa-solid fa-palette text-sm"></i>
                <span>Tampilan Publik &amp; Identitas Brand</span>
            </div>
            <h1 class="text-xl font-extrabold text-slate-900 tracking-tight">Kustomisasi Tema &amp; Konten Landing Page</h1>
            <p class="text-xs text-slate-500 font-medium mt-0.5">Ubah palet warna, tipografi hero, teks akreditasi, nomor WhatsApp, foto banner fasilitas, dan visibilitas section.</p>
        </div>

        <div class="flex items-center gap-3">
            <a href="{{ route('landing') }}" target="_blank" 
               class="px-4 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs flex items-center gap-2 transition-all">
                <i class="fa-solid fa-arrow-up-right-from-square text-xs text-[#005aab]"></i>
                <span>Lihat Landing Page</span>
            </a>
        </div>
    </div>

    <!-- Main Configuration Form -->
    <form action="{{ route('landing-setting.update') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
        @csrf

        <!-- 1. PILIHAN PALET WARNA TEMA -->
        <div class="bg-white rounded-3xl border border-slate-200/80 p-6 sm:p-7 shadow-xs space-y-5">
            <div class="border-b border-slate-100 pb-4 flex items-center justify-between">
                <div>
                    <h2 class="text-sm font-extrabold text-slate-900 uppercase tracking-wider flex items-center gap-2">
                        <i class="fa-solid fa-swatchbook text-[#005aab]"></i>
                        <span>1. Palet Tema Visual (Header &amp; Hero Background)</span>
                    </h2>
                    <p class="text-xs text-slate-500 font-medium mt-0.5">Pilih skema warna atmosfer yang langsung mengubah navbar, gradient hero canvas, dan aksen warna tombol.</p>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                @foreach($themes as $key => $theme)
                <label class="relative block rounded-2xl border-2 p-4 cursor-pointer transition-all duration-200"
                       :class="selectedTheme === '{{ $key }}' ? 'border-[#005aab] ring-4 ring-[#005aab]/15 bg-sky-50/20' : 'border-slate-200 hover:border-slate-300 bg-white'">
                    <input type="radio" name="theme_color" value="{{ $key }}" x-model="selectedTheme" class="sr-only">
                    
                    <!-- Color Swatch Strip -->
                    <div class="h-16 rounded-xl mb-3 overflow-hidden shadow-inner flex items-center justify-between px-4 text-white relative" 
                         style="{{ $theme['preview_css'] }}">
                        <div class="text-[10px] font-heading font-black uppercase tracking-wider opacity-90">Preview</div>
                        <span class="w-6 h-6 rounded-full border-2 border-white flex items-center justify-center text-xs" 
                              style="background-color: {{ $theme['accent_hex'] }};">
                            <i class="fa-solid fa-check text-[10px] text-slate-900" x-show="selectedTheme === '{{ $key }}'"></i>
                        </span>
                    </div>

                    <div class="space-y-1">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-bold text-slate-900 block leading-tight">{{ $theme['name'] }}</span>
                            <span x-show="selectedTheme === '{{ $key }}'" class="text-[10px] font-bold text-[#005aab] bg-sky-100 px-2 py-0.5 rounded-full">Aktif</span>
                        </div>
                        <p class="text-[11px] text-slate-500 leading-snug">{{ $theme['description'] }}</p>
                    </div>
                </label>
                @endforeach
            </div>
        </div>

        <!-- 2. IDENTITAS BRAND & PENGATURAN KONTAK WHATSAPP -->
        <div class="bg-white rounded-3xl border border-slate-200/80 p-6 sm:p-7 shadow-xs space-y-5">
            <div class="border-b border-slate-100 pb-4">
                <h2 class="text-sm font-extrabold text-slate-900 uppercase tracking-wider flex items-center gap-2">
                    <i class="fa-solid fa-id-card text-[#005aab]"></i>
                    <span>2. Identitas Brand Perusahaan &amp; Kontak Utama</span>
                </h2>
                <p class="text-xs text-slate-500 font-medium mt-0.5">Pengaturan identitas resmi institusional yang tampil pada header navigasi, topbar, dan footer halaman.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <!-- Brand Name -->
                <div class="space-y-1.5">
                    <label class="text-[11px] font-extrabold uppercase tracking-wider text-slate-700 block">
                        NAMA BRAND PERUSAHAAN <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" name="brand_name" value="{{ old('brand_name', $settings['brand_name']) }}" required
                           class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs font-bold text-slate-800 bg-white focus:ring-2 focus:ring-[#005aab] focus:outline-none transition">
                    <p class="text-[10px] text-slate-400">Contoh: AQUAFARM PRIANGAN</p>
                </div>

                <!-- Brand Subtitle / Lokasi -->
                <div class="space-y-1.5">
                    <label class="text-[11px] font-extrabold uppercase tracking-wider text-slate-700 block">
                        SUBTITLE BRAND / SENTRA BUDIDAYA
                    </label>
                    <input type="text" name="brand_subtitle" value="{{ old('brand_subtitle', $settings['brand_subtitle']) }}"
                           class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs font-semibold text-slate-800 bg-white focus:ring-2 focus:ring-[#005aab] focus:outline-none transition">
                    <p class="text-[10px] text-slate-400">Contoh: Fasilitas Budidaya Air Tawar Ciamis</p>
                </div>

                <!-- WhatsApp CS -->
                <div class="space-y-1.5">
                    <label class="text-[11px] font-extrabold uppercase tracking-wider text-slate-700 block">
                        NOMOR WHATSAPP SALES / CS <span class="text-rose-500">*</span>
                    </label>
                    <div class="relative">
                        <span class="absolute left-3.5 top-2.5 text-xs text-emerald-600 font-bold"><i class="fa-brands fa-whatsapp"></i></span>
                        <input type="text" name="phone_wa" value="{{ old('phone_wa', $settings['phone_wa']) }}" required
                               class="w-full pl-9 pr-3.5 py-2.5 rounded-xl border border-slate-200 text-xs font-bold text-slate-800 bg-white focus:ring-2 focus:ring-emerald-500 focus:outline-none transition">
                    </div>
                    <p class="text-[10px] text-slate-400">Awalan 08xxx atau 62xxx (langsung terhubung ke tombol aksi panen).</p>
                </div>

                <!-- Jam Panen -->
                <div class="space-y-1.5">
                    <label class="text-[11px] font-extrabold uppercase tracking-wider text-slate-700 block">
                        JADWAL / JAM OPERASIONAL PANEN
                    </label>
                    <input type="text" name="harvest_hours" value="{{ old('harvest_hours', $settings['harvest_hours']) }}"
                           class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs font-semibold text-slate-800 bg-white focus:ring-2 focus:ring-[#005aab] focus:outline-none transition">
                    <p class="text-[10px] text-slate-400">Contoh: Pagi: 05.00 – 08.00 WIB | Sore: 16.00 – 18.00 WIB</p>
                </div>
            </div>

            <!-- Pesan Template WhatsApp -->
            <div class="space-y-1.5 pt-2 border-t border-slate-100">
                <label class="text-[11px] font-extrabold uppercase tracking-wider text-slate-700 block">
                    PESAN TEMPLATE PEMBUKA WHATSAPP
                </label>
                <input type="text" name="wa_template_text" value="{{ old('wa_template_text', $settings['wa_template_text']) }}"
                       class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs font-medium text-slate-800 bg-white focus:ring-2 focus:ring-emerald-500 focus:outline-none transition">
                <p class="text-[10px] text-slate-400">Pesan otomatis yang muncul saat calon pembeli menekan tombol WhatsApp di website.</p>
            </div>
        </div>

        <!-- 3. MANAJEMEN 6 SLIDE BANNER HERO (SLIDER PERSIS ITB) -->
        <div class="bg-white rounded-3xl border border-slate-200/80 p-6 sm:p-7 shadow-xs space-y-6">
            <div class="border-b border-slate-100 pb-4 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                <div>
                    <h2 class="text-sm font-extrabold text-slate-900 uppercase tracking-wider flex items-center gap-2">
                        <i class="fa-solid fa-images text-[#005aab]"></i>
                        <span>3. Manajemen 6 Slide Hero Banner (Layar Penuh Persis ITB)</span>
                    </h2>
                    <p class="text-xs text-slate-500 font-medium mt-0.5">Kelola foto latar, headline, deskripsi, dan tombol untuk masing-masing 6 slide. Klik tab di bawah untuk mengedit slide yang diinginkan.</p>
                </div>
                <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-sky-50 text-sky-800 text-[11px] font-bold border border-sky-200 self-start sm:self-auto">
                    <i class="fa-solid fa-arrows-spin text-xs animate-spin text-[#005aab]"></i>
                    <span>Auto-Slide 5 Detik</span>
                </div>
            </div>

            <!-- TAB SELECTOR 1 S/D 6 -->
            <div class="flex items-center gap-2 border-b border-slate-200 overflow-x-auto pb-3">
                <button type="button" @click="activeSlideTab = 1"
                        :class="activeSlideTab === 1 ? 'bg-[#005aab] text-white shadow-md' : 'bg-slate-100 text-slate-600 hover:bg-slate-200'"
                        class="px-4 py-2.5 rounded-2xl font-black text-xs shrink-0 flex items-center gap-2 transition cursor-pointer">
                    <span class="w-5 h-5 rounded-full flex items-center justify-center text-[10px]" :class="activeSlideTab === 1 ? 'bg-white/20 text-white' : 'bg-slate-300 text-slate-700'">1</span>
                    <span>Slide 1: Banner Utama &amp; Prestasi</span>
                </button>

                <button type="button" @click="activeSlideTab = 2"
                        :class="activeSlideTab === 2 ? 'bg-[#005aab] text-white shadow-md' : 'bg-slate-100 text-slate-600 hover:bg-slate-200'"
                        class="px-4 py-2.5 rounded-2xl font-black text-xs shrink-0 flex items-center gap-2 transition cursor-pointer">
                    <span class="w-5 h-5 rounded-full flex items-center justify-center text-[10px]" :class="activeSlideTab === 2 ? 'bg-white/20 text-white' : 'bg-slate-300 text-slate-700'">2</span>
                    <span>Slide 2: Fasilitas Bioflok</span>
                </button>

                <button type="button" @click="activeSlideTab = 3"
                        :class="activeSlideTab === 3 ? 'bg-[#005aab] text-white shadow-md' : 'bg-slate-100 text-slate-600 hover:bg-slate-200'"
                        class="px-4 py-2.5 rounded-2xl font-black text-xs shrink-0 flex items-center gap-2 transition cursor-pointer">
                    <span class="w-5 h-5 rounded-full flex items-center justify-center text-[10px]" :class="activeSlideTab === 3 ? 'bg-white/20 text-white' : 'bg-slate-300 text-slate-700'">3</span>
                    <span>Slide 3: Panen Segar &amp; Mutu</span>
                </button>

                <button type="button" @click="activeSlideTab = 4"
                        :class="activeSlideTab === 4 ? 'bg-[#005aab] text-white shadow-md' : 'bg-slate-100 text-slate-600 hover:bg-slate-200'"
                        class="px-4 py-2.5 rounded-2xl font-black text-xs shrink-0 flex items-center gap-2 transition cursor-pointer">
                    <span class="w-5 h-5 rounded-full flex items-center justify-center text-[10px]" :class="activeSlideTab === 4 ? 'bg-white/20 text-white' : 'bg-slate-300 text-slate-700'">4</span>
                    <span>Slide 4: Riset &amp; Lab Air</span>
                </button>

                <button type="button" @click="activeSlideTab = 5"
                        :class="activeSlideTab === 5 ? 'bg-[#005aab] text-white shadow-md' : 'bg-slate-100 text-slate-600 hover:bg-slate-200'"
                        class="px-4 py-2.5 rounded-2xl font-black text-xs shrink-0 flex items-center gap-2 transition cursor-pointer">
                    <span class="w-5 h-5 rounded-full flex items-center justify-center text-[10px]" :class="activeSlideTab === 5 ? 'bg-white/20 text-white' : 'bg-slate-300 text-slate-700'">5</span>
                    <span>Slide 5: Mitra Resto &amp; Chef</span>
                </button>

                <button type="button" @click="activeSlideTab = 6"
                        :class="activeSlideTab === 6 ? 'bg-[#005aab] text-white shadow-md' : 'bg-slate-100 text-slate-600 hover:bg-slate-200'"
                        class="px-4 py-2.5 rounded-2xl font-black text-xs shrink-0 flex items-center gap-2 transition cursor-pointer">
                    <span class="w-5 h-5 rounded-full flex items-center justify-center text-[10px]" :class="activeSlideTab === 6 ? 'bg-white/20 text-white' : 'bg-slate-300 text-slate-700'">6</span>
                    <span>Slide 6: Panorama Sunrise &amp; Armada</span>
                </button>
            </div>

            <!-- ================= TAB CONTENT 1 (SLIDE 1) ================= -->
            <div x-show="activeSlideTab === 1" class="space-y-6">
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
                    <!-- Foto Slide 1 -->
                    <div class="lg:col-span-5 border border-slate-200 rounded-2xl p-4 bg-slate-50/60 space-y-3">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-black text-slate-800 uppercase tracking-wider">Foto Utama Slide 1</span>
                            <span class="text-[10px] font-bold text-sky-700 bg-sky-100 px-2 py-0.5 rounded">Default / Unggahan</span>
                        </div>
                        <div class="w-full h-48 rounded-xl overflow-hidden border border-slate-300 bg-[#071738] relative flex items-center justify-center p-2">
                            <img :src="heroPreview1 || '{{ $settings['hero_image_url'] }}'" alt="Slide 1 Preview" class="max-h-full max-w-full object-contain">
                        </div>
                        <div class="space-y-1.5">
                            <label class="text-[10px] font-bold text-slate-600 block uppercase">Unggah Foto Khusus Slide 1</label>
                            <input type="file" name="hero_image" @change="previewSlide($event, 1)"
                                   accept="image/png, image/jpeg, image/jpg, image/webp"
                                   class="block w-full text-[11px] text-slate-500 file:mr-2 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-[11px] file:font-bold file:bg-sky-100 file:text-sky-800 hover:file:bg-sky-200 cursor-pointer">
                            <p class="text-[10px] text-slate-400">Jika dikosongkan, Slide 1 menampilkan kolase 4 foto resmi institusional ITB.</p>
                            @if(!empty($settings['hero_image']))
                            <label class="inline-flex items-center gap-1.5 text-[11px] text-rose-600 font-semibold cursor-pointer pt-1">
                                <input type="checkbox" name="reset_hero_image" value="1" class="rounded text-rose-600">
                                <span>Reset Slide 1 ke Bawaan Resmi</span>
                            </label>
                            @endif
                        </div>
                    </div>

                    <!-- Teks Slide 1 -->
                    <div class="lg:col-span-7 space-y-4">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div class="space-y-1.5">
                                <label class="text-[11px] font-extrabold uppercase tracking-wider text-slate-700 block">TAG BADGE AKREDITASI</label>
                                <input type="text" name="slide1_tag" value="{{ old('slide1_tag', $settings['slide1_tag'] ?? 'TERAKREDITASI CBIB PRIMA 1') }}"
                                       class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs font-bold text-slate-800 bg-white focus:ring-2 focus:ring-[#005aab] focus:outline-none transition">
                            </div>
                            <div class="space-y-1.5">
                                <label class="text-[11px] font-extrabold uppercase tracking-wider text-slate-700 block">SUB-BRAND INSTITUSI</label>
                                <input type="text" name="slide1_subbrand" value="{{ old('slide1_subbrand', $settings['slide1_subbrand'] ?? 'satu priangan') }}"
                                       class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs font-bold text-slate-800 bg-white focus:ring-2 focus:ring-[#005aab] focus:outline-none transition">
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                            <div class="space-y-1.5">
                                <label class="text-[11px] font-extrabold uppercase tracking-wider text-slate-700 block">ANGKA RANK <span class="text-rose-500">*</span></label>
                                <input type="text" name="hero_rank" value="{{ old('hero_rank', $settings['hero_rank']) }}" required
                                       class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs font-black text-amber-600 bg-amber-50/40 focus:ring-2 focus:ring-amber-500 focus:outline-none transition">
                            </div>
                            <div class="sm:col-span-2 space-y-1.5">
                                <label class="text-[11px] font-extrabold uppercase tracking-wider text-slate-700 block">HEADLINE UTAMA EMAS <span class="text-rose-500">*</span></label>
                                <input type="text" name="hero_headline" value="{{ old('hero_headline', $settings['hero_headline']) }}" required
                                       class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs font-bold text-slate-800 bg-white focus:ring-2 focus:ring-[#005aab] focus:outline-none transition">
                            </div>
                        </div>

                        <div class="space-y-1.5">
                            <label class="text-[11px] font-extrabold uppercase tracking-wider text-slate-700 block">SUB-HEADLINE KOMODITAS</label>
                            <input type="text" name="slide1_subheadline" value="{{ old('slide1_subheadline', $settings['slide1_subheadline'] ?? 'KOMODITAS AIR TAWAR • NILA MERAH • LELE • PATIN • GURAME') }}"
                                   class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs font-semibold text-slate-800 bg-white focus:ring-2 focus:ring-[#005aab] focus:outline-none transition">
                        </div>

                        <div class="space-y-1.5">
                            <label class="text-[11px] font-extrabold uppercase tracking-wider text-slate-700 block">DESKRIPSI LENGKAP HERO SECTION <span class="text-rose-500">*</span></label>
                            <textarea name="hero_description" rows="2" required
                                      class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs font-medium text-slate-800 bg-white focus:ring-2 focus:ring-[#005aab] focus:outline-none transition">{{ old('hero_description', $settings['hero_description']) }}</textarea>
                        </div>

                        <!-- 3 Gold Shields Settings -->
                        <div class="p-3.5 rounded-2xl bg-amber-50/60 border border-amber-200 space-y-3">
                            <span class="text-[10px] font-black uppercase tracking-wider text-amber-900 block flex items-center gap-1.5">
                                <i class="fa-solid fa-shield-halved text-amber-600"></i>
                                <span>3 Kartu Shield QS-Style (Prestasi Slide 1)</span>
                            </span>
                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                                <!-- Shield 1 -->
                                <div class="bg-white p-2.5 rounded-xl border border-amber-200 space-y-1">
                                    <input type="text" name="slide1_badge1_title" value="{{ old('slide1_badge1_title', $settings['slide1_badge1_title'] ?? 'MUTU KKP') }}" placeholder="Judul (MUTU KKP)" class="w-full px-2 py-1 rounded text-[10px] font-bold border border-slate-200">
                                    <input type="text" name="slide1_badge1_val" value="{{ old('slide1_badge1_val', $settings['slide1_badge1_val'] ?? '#1') }}" placeholder="Nilai (#1)" class="w-full px-2 py-1 rounded text-xs font-black text-amber-600 border border-slate-200">
                                    <input type="text" name="slide1_badge1_sub" value="{{ old('slide1_badge1_sub', $settings['slide1_badge1_sub'] ?? 'CBIB PRIMA 1') }}" placeholder="Sub (CBIB PRIMA 1)" class="w-full px-2 py-1 rounded text-[10px] border border-slate-200">
                                </div>
                                <!-- Shield 2 -->
                                <div class="bg-white p-2.5 rounded-xl border border-amber-200 space-y-1">
                                    <input type="text" name="slide1_badge2_title" value="{{ old('slide1_badge2_title', $settings['slide1_badge2_title'] ?? 'AIR DERAS') }}" placeholder="Judul (AIR DERAS)" class="w-full px-2 py-1 rounded text-[10px] font-bold border border-slate-200">
                                    <input type="text" name="slide1_badge2_val" value="{{ old('slide1_badge2_val', $settings['slide1_badge2_val'] ?? '0%') }}" placeholder="Nilai (0%)" class="w-full px-2 py-1 rounded text-xs font-black text-amber-600 border border-slate-200">
                                    <input type="text" name="slide1_badge2_sub" value="{{ old('slide1_badge2_sub', $settings['slide1_badge2_sub'] ?? 'ZERO GEOSMIN') }}" placeholder="Sub (ZERO GEOSMIN)" class="w-full px-2 py-1 rounded text-[10px] border border-slate-200">
                                </div>
                                <!-- Shield 3 -->
                                <div class="bg-white p-2.5 rounded-xl border border-amber-200 space-y-1">
                                    <input type="text" name="slide1_badge3_title" value="{{ old('slide1_badge3_title', $settings['slide1_badge3_title'] ?? 'LOGISTIK DDP') }}" placeholder="Judul (LOGISTIK DDP)" class="w-full px-2 py-1 rounded text-[10px] font-bold border border-slate-200">
                                    <input type="text" name="slide1_badge3_val" value="{{ old('slide1_badge3_val', $settings['slide1_badge3_val'] ?? '≤2%') }}" placeholder="Nilai (≤2%)" class="w-full px-2 py-1 rounded text-xs font-black text-amber-600 border border-slate-200">
                                    <input type="text" name="slide1_badge3_sub" value="{{ old('slide1_badge3_sub', $settings['slide1_badge3_sub'] ?? 'SUSUT HIDUP') }}" placeholder="Sub (SUSUT HIDUP)" class="w-full px-2 py-1 rounded text-[10px] border border-slate-200">
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>

            <!-- ================= TAB CONTENT 2 (SLIDE 2) ================= -->
            <div x-show="activeSlideTab === 2" class="space-y-6">
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
                    <div class="lg:col-span-5 border border-slate-200 rounded-2xl p-4 bg-slate-50/60 space-y-3">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-black text-slate-800 uppercase tracking-wider">Foto Latar Slide 2</span>
                            <span class="text-[10px] font-bold text-emerald-700 bg-emerald-100 px-2 py-0.5 rounded">Kolam Bioflok</span>
                        </div>
                        <div class="w-full h-48 rounded-xl overflow-hidden border border-slate-300 bg-[#071738] relative">
                            <img :src="heroPreview2 || '{{ $settings['hero_slide2_url'] }}'" alt="Slide 2 Preview" class="w-full h-full object-cover">
                        </div>
                        <div class="space-y-1.5">
                            <label class="text-[10px] font-bold text-slate-600 block uppercase">Unggah File Slide 2</label>
                            <input type="file" name="hero_image_2" @change="previewSlide($event, 2)"
                                   accept="image/png, image/jpeg, image/jpg, image/webp"
                                   class="block w-full text-[11px] text-slate-500 file:mr-2 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-[11px] file:font-bold file:bg-emerald-100 file:text-emerald-800 hover:file:bg-emerald-200 cursor-pointer">
                            @if(!empty($settings['hero_image_2']))
                            <label class="inline-flex items-center gap-1.5 text-[11px] text-rose-600 font-semibold cursor-pointer pt-1">
                                <input type="checkbox" name="reset_hero_image_2" value="1" class="rounded text-rose-600">
                                <span>Reset Foto Slide 2 ke Bawaan</span>
                            </label>
                            @endif
                        </div>
                    </div>

                    <div class="lg:col-span-7 space-y-4">
                        <div class="space-y-1.5">
                            <label class="text-[11px] font-extrabold uppercase tracking-wider text-slate-700 block">KATEGORI / BADGE SLIDE 2</label>
                            <input type="text" name="slide2_tag" value="{{ old('slide2_tag', $settings['slide2_tag'] ?? 'FASILITAS BUNI SEURI CIAMIS') }}"
                                   class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs font-bold text-slate-800 bg-white focus:ring-2 focus:ring-[#005aab] focus:outline-none transition">
                        </div>
                        <div class="space-y-1.5">
                            <label class="text-[11px] font-extrabold uppercase tracking-wider text-slate-700 block">JUDUL UTAMA SLIDE 2</label>
                            <input type="text" name="slide2_title" value="{{ old('slide2_title', $settings['slide2_title'] ?? 'Sirkulasi Air Mengalir Deras 24 Jam & Kolam Bioflok Modern') }}"
                                   class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs font-extrabold text-slate-800 bg-white focus:ring-2 focus:ring-[#005aab] focus:outline-none transition">
                        </div>
                        <div class="space-y-1.5">
                            <label class="text-[11px] font-extrabold uppercase tracking-wider text-slate-700 block">DESKRIPSI LENGKAP SLIDE 2</label>
                            <textarea name="slide2_desc" rows="3"
                                      class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs font-medium text-slate-800 bg-white focus:ring-2 focus:ring-[#005aab] focus:outline-none transition">{{ old('slide2_desc', $settings['slide2_desc'] ?? 'Memanfaatkan sumber air pegunungan alami yang mengalir deras dan beroksigen tinggi. Standar pemuasaan 24 jam menjamin daging ikan padat, gurih alami, dan bebas bau lumpur (Zero Geosmin).') }}</textarea>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div class="space-y-1.5">
                                <label class="text-[11px] font-extrabold uppercase tracking-wider text-slate-700 block">TEKS TOMBOL AKSI</label>
                                <input type="text" name="slide2_btn_text" value="{{ old('slide2_btn_text', $settings['slide2_btn_text'] ?? 'Pelajari Standar Mutu Budidaya') }}"
                                       class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs font-bold text-slate-800 bg-white">
                            </div>
                            <div class="space-y-1.5">
                                <label class="text-[11px] font-extrabold uppercase tracking-wider text-slate-700 block">TAUTAN TOMBOL (URL/#SECTION)</label>
                                <input type="text" name="slide2_btn_link" value="{{ old('slide2_btn_link', $settings['slide2_btn_link'] ?? '#sop') }}"
                                       class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs font-semibold text-slate-800 bg-white">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ================= TAB CONTENT 3 (SLIDE 3) ================= -->
            <div x-show="activeSlideTab === 3" class="space-y-6">
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
                    <div class="lg:col-span-5 border border-slate-200 rounded-2xl p-4 bg-slate-50/60 space-y-3">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-black text-slate-800 uppercase tracking-wider">Foto Latar Slide 3</span>
                            <span class="text-[10px] font-bold text-amber-700 bg-amber-100 px-2 py-0.5 rounded">Panen Segar CBIB</span>
                        </div>
                        <div class="w-full h-48 rounded-xl overflow-hidden border border-slate-300 bg-[#071738] relative">
                            <img :src="heroPreview3 || '{{ $settings['hero_slide3_url'] }}'" alt="Slide 3 Preview" class="w-full h-full object-cover">
                        </div>
                        <div class="space-y-1.5">
                            <label class="text-[10px] font-bold text-slate-600 block uppercase">Unggah File Slide 3</label>
                            <input type="file" name="hero_image_3" @change="previewSlide($event, 3)"
                                   accept="image/png, image/jpeg, image/jpg, image/webp"
                                   class="block w-full text-[11px] text-slate-500 file:mr-2 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-[11px] file:font-bold file:bg-amber-100 file:text-amber-800 hover:file:bg-amber-200 cursor-pointer">
                            @if(!empty($settings['hero_image_3']))
                            <label class="inline-flex items-center gap-1.5 text-[11px] text-rose-600 font-semibold cursor-pointer pt-1">
                                <input type="checkbox" name="reset_hero_image_3" value="1" class="rounded text-rose-600">
                                <span>Reset Foto Slide 3 ke Bawaan</span>
                            </label>
                            @endif
                        </div>
                    </div>

                    <div class="lg:col-span-7 space-y-4">
                        <div class="space-y-1.5">
                            <label class="text-[11px] font-extrabold uppercase tracking-wider text-slate-700 block">KATEGORI / BADGE SLIDE 3</label>
                            <input type="text" name="slide3_tag" value="{{ old('slide3_tag', $settings['slide3_tag'] ?? 'PANEN SEGAR AIR TAWAR') }}"
                                   class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs font-bold text-slate-800 bg-white focus:ring-2 focus:ring-[#005aab] focus:outline-none transition">
                        </div>
                        <div class="space-y-1.5">
                            <label class="text-[11px] font-extrabold uppercase tracking-wider text-slate-700 block">JUDUL UTAMA SLIDE 3</label>
                            <input type="text" name="slide3_title" value="{{ old('slide3_title', $settings['slide3_title'] ?? 'Grading Ketat Komoditas Unggulan Siap Konsumsi') }}"
                                   class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs font-extrabold text-slate-800 bg-white focus:ring-2 focus:ring-[#005aab] focus:outline-none transition">
                        </div>
                        <div class="space-y-1.5">
                            <label class="text-[11px] font-extrabold uppercase tracking-wider text-slate-700 block">DESKRIPSI LENGKAP SLIDE 3</label>
                            <textarea name="slide3_desc" rows="3"
                                      class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs font-medium text-slate-800 bg-white focus:ring-2 focus:ring-[#005aab] focus:outline-none transition">{{ old('slide3_desc', $settings['slide3_desc'] ?? 'Nila Merah Nirwana, Lele Sangkuriang, Patin Siam, dan Gurame Soang dipanen segar setiap pagi dan sore hari. Daging padat manis tanpa residu antibiotik dan siap diolah dapur kuliner Anda.') }}</textarea>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div class="space-y-1.5">
                                <label class="text-[11px] font-extrabold uppercase tracking-wider text-slate-700 block">TEKS TOMBOL AKSI</label>
                                <input type="text" name="slide3_btn_text" value="{{ old('slide3_btn_text', $settings['slide3_btn_text'] ?? 'Lihat Katalog Komoditas Ikan') }}"
                                       class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs font-bold text-slate-800 bg-white">
                            </div>
                            <div class="space-y-1.5">
                                <label class="text-[11px] font-extrabold uppercase tracking-wider text-slate-700 block">TAUTAN TOMBOL (URL/#SECTION)</label>
                                <input type="text" name="slide3_btn_link" value="{{ old('slide3_btn_link', $settings['slide3_btn_link'] ?? '#katalog') }}"
                                       class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs font-semibold text-slate-800 bg-white">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ================= TAB CONTENT 4 (SLIDE 4) ================= -->
            <div x-show="activeSlideTab === 4" class="space-y-6">
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
                    <div class="lg:col-span-5 border border-slate-200 rounded-2xl p-4 bg-slate-50/60 space-y-3">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-black text-slate-800 uppercase tracking-wider">Foto Latar Slide 4</span>
                            <span class="text-[10px] font-bold text-teal-700 bg-teal-100 px-2 py-0.5 rounded">Laboratorium Air</span>
                        </div>
                        <div class="w-full h-48 rounded-xl overflow-hidden border border-slate-300 bg-[#071738] relative">
                            <img :src="heroPreview4 || '{{ $settings['hero_slide4_url'] }}'" alt="Slide 4 Preview" class="w-full h-full object-cover">
                        </div>
                        <div class="space-y-1.5">
                            <label class="text-[10px] font-bold text-slate-600 block uppercase">Unggah File Slide 4</label>
                            <input type="file" name="hero_image_4" @change="previewSlide($event, 4)"
                                   accept="image/png, image/jpeg, image/jpg, image/webp"
                                   class="block w-full text-[11px] text-slate-500 file:mr-2 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-[11px] file:font-bold file:bg-teal-100 file:text-teal-800 hover:file:bg-teal-200 cursor-pointer">
                            @if(!empty($settings['hero_image_4']))
                            <label class="inline-flex items-center gap-1.5 text-[11px] text-rose-600 font-semibold cursor-pointer pt-1">
                                <input type="checkbox" name="reset_hero_image_4" value="1" class="rounded text-rose-600">
                                <span>Reset Foto Slide 4 ke Bawaan</span>
                            </label>
                            @endif
                        </div>
                    </div>

                    <div class="lg:col-span-7 space-y-4">
                        <div class="space-y-1.5">
                            <label class="text-[11px] font-extrabold uppercase tracking-wider text-slate-700 block">KATEGORI / BADGE SLIDE 4</label>
                            <input type="text" name="slide4_tag" value="{{ old('slide4_tag', $settings['slide4_tag'] ?? 'STANDAR CBIB PRIMA 1 KKP RI') }}"
                                   class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs font-bold text-slate-800 bg-white focus:ring-2 focus:ring-[#005aab] focus:outline-none transition">
                        </div>
                        <div class="space-y-1.5">
                            <label class="text-[11px] font-extrabold uppercase tracking-wider text-slate-700 block">JUDUL UTAMA SLIDE 4</label>
                            <input type="text" name="slide4_title" value="{{ old('slide4_title', $settings['slide4_title'] ?? 'Monitoring Kualitas Air Real-Time & Uji Mikrobiologi') }}"
                                   class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs font-extrabold text-slate-800 bg-white focus:ring-2 focus:ring-[#005aab] focus:outline-none transition">
                        </div>
                        <div class="space-y-1.5">
                            <label class="text-[11px] font-extrabold uppercase tracking-wider text-slate-700 block">DESKRIPSI LENGKAP SLIDE 4</label>
                            <textarea name="slide4_desc" rows="3"
                                      class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs font-medium text-slate-800 bg-white focus:ring-2 focus:ring-[#005aab] focus:outline-none transition">{{ old('slide4_desc', $settings['slide4_desc'] ?? 'Pengujian parameter pH, Dissolved Oxygen (DO), suhu, dan nitrit dilakukan berkala oleh tenaga ahli budidaya berpengalaman. Menjamin ikan hidup sehat secara alami tanpa penambahan zat kimia sintetis berbahaya.') }}</textarea>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div class="space-y-1.5">
                                <label class="text-[11px] font-extrabold uppercase tracking-wider text-slate-700 block">TEKS TOMBOL AKSI</label>
                                <input type="text" name="slide4_btn_text" value="{{ old('slide4_btn_text', $settings['slide4_btn_text'] ?? 'Verifikasi Sertifikasi Mutu') }}"
                                       class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs font-bold text-slate-800 bg-white">
                            </div>
                            <div class="space-y-1.5">
                                <label class="text-[11px] font-extrabold uppercase tracking-wider text-slate-700 block">TAUTAN TOMBOL (Ketik 'wa' atau URL)</label>
                                <input type="text" name="slide4_btn_link" value="{{ old('slide4_btn_link', $settings['slide4_btn_link'] ?? 'wa') }}"
                                       class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs font-semibold text-slate-800 bg-white">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ================= TAB CONTENT 5 (SLIDE 5) ================= -->
            <div x-show="activeSlideTab === 5" class="space-y-6">
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
                    <div class="lg:col-span-5 border border-slate-200 rounded-2xl p-4 bg-slate-50/60 space-y-3">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-black text-slate-800 uppercase tracking-wider">Foto Latar Slide 5</span>
                            <span class="text-[10px] font-bold text-orange-700 bg-orange-100 px-2 py-0.5 rounded">Mitra Resto &amp; Chef</span>
                        </div>
                        <div class="w-full h-48 rounded-xl overflow-hidden border border-slate-300 bg-[#071738] relative">
                            <img :src="heroPreview5 || '{{ $settings['hero_slide5_url'] }}'" alt="Slide 5 Preview" class="w-full h-full object-cover">
                        </div>
                        <div class="space-y-1.5">
                            <label class="text-[10px] font-bold text-slate-600 block uppercase">Unggah File Slide 5</label>
                            <input type="file" name="hero_image_5" @change="previewSlide($event, 5)"
                                   accept="image/png, image/jpeg, image/jpg, image/webp"
                                   class="block w-full text-[11px] text-slate-500 file:mr-2 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-[11px] file:font-bold file:bg-orange-100 file:text-orange-800 hover:file:bg-orange-200 cursor-pointer">
                            @if(!empty($settings['hero_image_5']))
                            <label class="inline-flex items-center gap-1.5 text-[11px] text-rose-600 font-semibold cursor-pointer pt-1">
                                <input type="checkbox" name="reset_hero_image_5" value="1" class="rounded text-rose-600">
                                <span>Reset Foto Slide 5 ke Bawaan</span>
                            </label>
                            @endif
                        </div>
                    </div>

                    <div class="lg:col-span-7 space-y-4">
                        <div class="space-y-1.5">
                            <label class="text-[11px] font-extrabold uppercase tracking-wider text-slate-700 block">KATEGORI / BADGE SLIDE 5</label>
                            <input type="text" name="slide5_tag" value="{{ old('slide5_tag', $settings['slide5_tag'] ?? 'PASOKAN RESTORAN & GROSIR') }}"
                                   class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs font-bold text-slate-800 bg-white focus:ring-2 focus:ring-[#005aab] focus:outline-none transition">
                        </div>
                        <div class="space-y-1.5">
                            <label class="text-[11px] font-extrabold uppercase tracking-wider text-slate-700 block">JUDUL UTAMA SLIDE 5</label>
                            <input type="text" name="slide5_title" value="{{ old('slide5_title', $settings['slide5_title'] ?? 'Pilihan Utama 50+ Rumah Makan & Hotel Jawa Barat') }}"
                                   class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs font-extrabold text-slate-800 bg-white focus:ring-2 focus:ring-[#005aab] focus:outline-none transition">
                        </div>
                        <div class="space-y-1.5">
                            <label class="text-[11px] font-extrabold uppercase tracking-wider text-slate-700 block">DESKRIPSI LENGKAP SLIDE 5</label>
                            <textarea name="slide5_desc" rows="3"
                                      class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs font-medium text-slate-800 bg-white focus:ring-2 focus:ring-[#005aab] focus:outline-none transition">{{ old('slide5_desc', $settings['slide5_desc'] ?? 'Jaminan kontinuitas pasokan harian tanpa putus stok. Dapatkan kontrak pasok rutin dengan harga stabil, penjadwalan panen subuh, dan spesifikasi bobot yang sesuai dengan menu andalan resto Anda.') }}</textarea>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div class="space-y-1.5">
                                <label class="text-[11px] font-extrabold uppercase tracking-wider text-slate-700 block">TEKS TOMBOL AKSI</label>
                                <input type="text" name="slide5_btn_text" value="{{ old('slide5_btn_text', $settings['slide5_btn_text'] ?? 'Simulasi Pesanan Restoran') }}"
                                       class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs font-bold text-slate-800 bg-white">
                            </div>
                            <div class="space-y-1.5">
                                <label class="text-[11px] font-extrabold uppercase tracking-wider text-slate-700 block">TAUTAN TOMBOL (URL/#SECTION)</label>
                                <input type="text" name="slide5_btn_link" value="{{ old('slide5_btn_link', $settings['slide5_btn_link'] ?? '#kalkulator') }}"
                                       class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs font-semibold text-slate-800 bg-white">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ================= TAB CONTENT 6 (SLIDE 6) ================= -->
            <div x-show="activeSlideTab === 6" class="space-y-6">
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
                    <div class="lg:col-span-5 border border-slate-200 rounded-2xl p-4 bg-slate-50/60 space-y-3">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-black text-slate-800 uppercase tracking-wider">Foto Latar Slide 6</span>
                            <span class="text-[10px] font-bold text-indigo-700 bg-indigo-100 px-2 py-0.5 rounded">Armada Logistik</span>
                        </div>
                        <div class="w-full h-48 rounded-xl overflow-hidden border border-slate-300 bg-[#071738] relative">
                            <img :src="heroPreview6 || '{{ $settings['hero_slide6_url'] }}'" alt="Slide 6 Preview" class="w-full h-full object-cover">
                        </div>
                        <div class="space-y-1.5">
                            <label class="text-[10px] font-bold text-slate-600 block uppercase">Unggah File Slide 6</label>
                            <input type="file" name="hero_image_6" @change="previewSlide($event, 6)"
                                   accept="image/png, image/jpeg, image/jpg, image/webp"
                                   class="block w-full text-[11px] text-slate-500 file:mr-2 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-[11px] file:font-bold file:bg-indigo-100 file:text-indigo-800 hover:file:bg-indigo-200 cursor-pointer">
                            @if(!empty($settings['hero_image_6']))
                            <label class="inline-flex items-center gap-1.5 text-[11px] text-rose-600 font-semibold cursor-pointer pt-1">
                                <input type="checkbox" name="reset_hero_image_6" value="1" class="rounded text-rose-600">
                                <span>Reset Foto Slide 6 ke Bawaan</span>
                            </label>
                            @endif
                        </div>
                    </div>

                    <div class="lg:col-span-7 space-y-4">
                        <div class="space-y-1.5">
                            <label class="text-[11px] font-extrabold uppercase tracking-wider text-slate-700 block">KATEGORI / BADGE SLIDE 6</label>
                            <input type="text" name="slide6_tag" value="{{ old('slide6_tag', $settings['slide6_tag'] ?? 'LOGISTIK KHUSUS AIR BER-OKSIGEN') }}"
                                   class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs font-bold text-slate-800 bg-white focus:ring-2 focus:ring-[#005aab] focus:outline-none transition">
                        </div>
                        <div class="space-y-1.5">
                            <label class="text-[11px] font-extrabold uppercase tracking-wider text-slate-700 block">JUDUL UTAMA SLIDE 6</label>
                            <input type="text" name="slide6_title" value="{{ old('slide6_title', $settings['slide6_title'] ?? 'Armada Aerasi Cepat Menjangkau Seluruh Jawa Barat') }}"
                                   class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs font-extrabold text-slate-800 bg-white focus:ring-2 focus:ring-[#005aab] focus:outline-none transition">
                        </div>
                        <div class="space-y-1.5">
                            <label class="text-[11px] font-extrabold uppercase tracking-wider text-slate-700 block">DESKRIPSI LENGKAP SLIDE 6</label>
                            <textarea name="slide6_desc" rows="3"
                                      class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs font-medium text-slate-800 bg-white focus:ring-2 focus:ring-[#005aab] focus:outline-none transition">{{ old('slide6_desc', $settings['slide6_desc'] ?? 'Dilengkapi box aerasi motor untuk pengiriman cepat lokal Priangan Timur, pickup tangki drum aerasi aktif, hingga truk tangki oksigen murni untuk partai tonase ke Bandung Raya dan Jabodetabek.') }}</textarea>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div class="space-y-1.5">
                                <label class="text-[11px] font-extrabold uppercase tracking-wider text-slate-700 block">TEKS TOMBOL AKSI</label>
                                <input type="text" name="slide6_btn_text" value="{{ old('slide6_btn_text', $settings['slide6_btn_text'] ?? 'Konsultasi Rute & Jadwal Armada') }}"
                                       class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs font-bold text-slate-800 bg-white">
                            </div>
                            <div class="space-y-1.5">
                                <label class="text-[11px] font-extrabold uppercase tracking-wider text-slate-700 block">TAUTAN TOMBOL (Ketik 'wa' atau URL)</label>
                                <input type="text" name="slide6_btn_link" value="{{ old('slide6_btn_link', $settings['slide6_btn_link'] ?? 'wa') }}"
                                       class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs font-semibold text-slate-800 bg-white">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        <!-- 4. TOGGLE VISIBILITAS MODUL / SECTION -->
        <div class="bg-white rounded-3xl border border-slate-200/80 p-6 sm:p-7 shadow-xs space-y-5">
            <div class="border-b border-slate-100 pb-4">
                <h2 class="text-sm font-extrabold text-slate-900 uppercase tracking-wider flex items-center gap-2">
                    <i class="fa-solid fa-toggle-on text-[#005aab]"></i>
                    <span>4. Visibilitas Section di Landing Page</span>
                </h2>
                <p class="text-xs text-slate-500 font-medium mt-0.5">Aktifkan atau sembunyikan section tertentu sesuai dengan kesiapan operasional budidaya.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <!-- Toggle 1: Kalkulator -->
                <label class="flex items-start gap-3 p-4 rounded-2xl border border-slate-200 hover:bg-slate-50 cursor-pointer transition">
                    <input type="checkbox" name="show_calculator" value="1" {{ ($settings['show_calculator'] ?? '1') == '1' ? 'checked' : '' }}
                           class="mt-1 w-4 h-4 rounded text-[#005aab] focus:ring-[#005aab] border-slate-300">
                    <div>
                        <span class="text-xs font-bold text-slate-900 block">Kalkulator Kebutuhan Ikan</span>
                        <span class="text-[11px] text-slate-500 block leading-snug">Estimator hitung porsi konsumsi resto &amp; pesanan otomatis via WA.</span>
                    </div>
                </label>

                <!-- Toggle 2: Agenda Panen -->
                <label class="flex items-start gap-3 p-4 rounded-2xl border border-slate-200 hover:bg-slate-50 cursor-pointer transition">
                    <input type="checkbox" name="show_agenda" value="1" {{ ($settings['show_agenda'] ?? '1') == '1' ? 'checked' : '' }}
                           class="mt-1 w-4 h-4 rounded text-[#005aab] focus:ring-[#005aab] border-slate-300">
                    <div>
                        <span class="text-xs font-bold text-slate-900 block">Jadwal &amp; Agenda Panen</span>
                        <span class="text-[11px] text-slate-500 block leading-snug">Kotak kalender tanggal pengiriman ikan gaya agenda ITB.</span>
                    </div>
                </label>

                <!-- Toggle 3: Kemitraan Investor -->
                <label class="flex items-start gap-3 p-4 rounded-2xl border border-slate-200 hover:bg-slate-50 cursor-pointer transition">
                    <input type="checkbox" name="show_investor" value="1" {{ ($settings['show_investor'] ?? '1') == '1' ? 'checked' : '' }}
                           class="mt-1 w-4 h-4 rounded text-[#005aab] focus:ring-[#005aab] border-slate-300">
                    <div>
                        <span class="text-xs font-bold text-slate-900 block">Kemitraan &amp; Investor Budidaya</span>
                        <span class="text-[11px] text-slate-500 block leading-snug">Skema modal kolam, bagi hasil panen, dan garansi serap pasar.</span>
                    </div>
                </label>
            </div>
        </div>

        <!-- FORM ACTION BUTTONS -->
        <div class="flex items-center justify-end gap-3 pt-3">
            <a href="{{ route('landing') }}" target="_blank"
               class="px-5 py-3 rounded-2xl border border-slate-200 text-slate-600 hover:bg-slate-100 font-bold text-xs transition">
                Pratinjau Halaman
            </a>

            <button type="submit"
                    class="px-7 py-3 rounded-2xl bg-[#005aab] hover:bg-sky-700 active:scale-95 text-white font-extrabold text-xs shadow-lg shadow-sky-600/30 transition-all flex items-center gap-2 cursor-pointer">
                <i class="fa-solid fa-floppy-disk text-xs"></i>
                <span>Simpan Perubahan Landing Page</span>
            </button>
        </div>

    </form>

</div>

<script>
function landingSettingComponent() {
    return {
        selectedTheme: '{{ old('theme_color', $settings['theme_color'] ?? 'itb_royal') }}',
        activeSlideTab: 1,
        heroPreview1: null,
        heroPreview2: null,
        heroPreview3: null,
        heroPreview4: null,
        heroPreview5: null,
        heroPreview6: null,

        previewSlide(event, index) {
            const file = event.target.files[0];
            if (file) {
                if (file.size > 5 * 1024 * 1024) {
                    alert('Ukuran file maksimal 5MB.');
                    event.target.value = '';
                    return;
                }
                const reader = new FileReader();
                reader.onload = (e) => {
                    this['heroPreview' + index] = e.target.result;
                };
                reader.readAsDataURL(file);
            }
        }
    };
}
</script>
@endsection

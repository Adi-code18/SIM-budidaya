@extends('layouts.app')

@section('title', 'Manajemen Mitra - SIM-BUDIDAYA')

@section('content')
<div class="space-y-6" x-data="mitraApp()">
    <!-- Toast Notification -->
    <div x-show="showToast"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 translate-y-2 scale-95"
         x-transition:enter-end="opacity-100 translate-y-0 scale-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 translate-y-0 scale-100"
         x-transition:leave-end="opacity-0 translate-y-2 scale-95"
         class="fixed top-5 right-5 z-50 bg-[#051B44] text-white px-5 py-3 rounded-xl shadow-2xl border border-sky-400/30 flex items-center gap-3">
        <div class="w-7 h-7 rounded-lg bg-emerald-500/20 text-emerald-400 flex items-center justify-center shrink-0">
            <i class="fa-solid fa-circle-check text-base"></i>
        </div>
        <span class="text-xs font-semibold" x-text="toastMessage"></span>
    </div>

    <!-- Subtitle & Page Title Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-extrabold text-[#0B2570] tracking-tight">Manajemen Mitra</h1>
            <p class="text-xs text-slate-500 font-medium mt-0.5">Kelola hubungan dan distribusi hasil budidaya ke mitra strategis.</p>
        </div>
        <div>
            <button @click="if (showForm) { showForm = false; } else { openCreateForm(); }" 
                    class="px-4 py-2 rounded-xl bg-[#051B44] hover:bg-navy-900 text-white font-bold text-xs shadow-xs transition-all flex items-center gap-2">
                <i class="fa-solid" :class="showForm ? 'fa-list' : 'fa-plus'"></i>
                <span x-text="showForm ? 'Lihat Daftar Mitra' : 'Tambah Mitra Baru'"></span>
            </button>
        </div>
    </div>

    <!-- Input / Edit / View Form Section (Shown when showForm is true) -->
    <div x-show="showForm" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 -translate-y-2"
         x-transition:enter-end="opacity-100 translate-y-0"
         class="space-y-4">
        
        <div class="flex items-center justify-between">
            <h2 class="text-lg font-extrabold text-slate-900 tracking-tight"
                x-text="formMode === 'create' ? 'Input Manajemen Mitra Baru' : (formMode === 'edit' ? 'Edit Data Mitra — ' + form.id : 'View Lengkap Data Mitra — ' + form.id)"></h2>
            
            <span x-show="formMode === 'view'" class="px-3 py-1 rounded-full text-xs font-extrabold bg-amber-100 text-amber-800 border border-amber-300 flex items-center gap-1.5">
                <i class="fa-solid fa-lock"></i> Mode View Lengkap (Disabled)
            </span>
            <span x-show="formMode === 'edit'" class="px-3 py-1 rounded-full text-xs font-extrabold bg-sky-100 text-sky-800 border border-sky-300 flex items-center gap-1.5">
                <i class="fa-solid fa-pen"></i> Mode Edit Data
            </span>
        </div>

        <!-- Mode View Info Banner -->
        <div x-show="formMode === 'view'" class="p-3.5 rounded-xl bg-amber-50 border border-amber-200 text-amber-900 text-xs flex items-center justify-between gap-3">
            <div class="flex items-center gap-2.5">
                <i class="fa-solid fa-shield-halved text-amber-600 text-base shrink-0"></i>
                <span>Data mitra ditampilkan dalam <strong>mode lihat (read-only)</strong>. Semua bidang formulir dinonaktifkan agar data tidak sengaja diubah.</span>
            </div>
            <button @click="formMode = 'edit'; initMitraMap(form.lat, form.lng, false)" class="px-3 py-1.5 rounded-lg bg-amber-600 hover:bg-amber-700 text-white font-bold text-[11px] shrink-0 transition-colors flex items-center gap-1.5">
                <i class="fa-solid fa-pen-to-square"></i> Ubah Ke Mode Edit
            </button>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
            
            <!-- Left 5 Cols: Interactive Leaflet Map & Card -->
            <div class="lg:col-span-5 relative overflow-hidden rounded-2xl border border-slate-200 bg-slate-100 min-h-[480px] flex flex-col shadow-xs">
                
                <!-- Interactive Leaflet Map (Full clickable & draggable container) -->
                <div id="mitraMapPicker" class="absolute inset-0 z-0 h-full w-full"></div>
                
                <!-- Floating Top Bar (Controls & Status - non-blocking pointer events) -->
                <div class="relative z-10 p-3.5 flex items-center justify-between gap-2 pointer-events-none">
                    <span x-show="formMode !== 'view'" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-[11px] font-extrabold bg-white/95 text-[#051B44] shadow-md border border-slate-200/80 pointer-events-auto backdrop-blur-xs">
                        <i class="fa-solid fa-hand-pointer text-sky-600"></i> Seret Pin / Klik Peta
                    </span>
                    <span x-show="formMode === 'view'" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-[11px] font-extrabold bg-amber-500/90 text-white shadow-md pointer-events-auto backdrop-blur-xs">
                        <i class="fa-solid fa-lock"></i> Mode Terkunci (View Only)
                    </span>

                    <div class="flex items-center gap-1.5 pointer-events-auto">
                        <!-- Toggle Satelit / Peta Jalan -->
                        <button type="button" 
                                @click="toggleMapLayer()"
                                class="px-3 py-1.5 rounded-xl bg-white/95 text-slate-800 hover:bg-slate-100 text-xs font-bold shadow-md border border-slate-200/80 backdrop-blur-xs transition-all flex items-center gap-1.5 cursor-pointer"
                                title="Ganti mode peta satelit / jalan">
                            <i class="fa-solid" :class="mapLayerMode === 'satellite' ? 'fa-satellite text-sky-600' : 'fa-map text-emerald-600'"></i>
                            <span x-text="mapLayerMode === 'satellite' ? 'Satelit HD' : 'Peta Jalan'"></span>
                        </button>

                        <!-- Fokus Pin -->
                        <button type="button" 
                                onclick="if(window.recenterMitraMap) window.recenterMitraMap()"
                                class="px-3 py-1.5 rounded-xl bg-[#051B44] hover:bg-navy-900 text-white text-xs font-bold shadow-md transition-all flex items-center gap-1.5 cursor-pointer"
                                title="Fokuskan tampilan ke pin marker">
                            <i class="fa-solid fa-crosshairs"></i>
                            <span>Fokus Pin</span>
                        </button>

                        <!-- Custom Zoom Controls -->
                        <div class="flex items-center bg-white/95 rounded-xl shadow-md border border-slate-200/80 backdrop-blur-xs overflow-hidden">
                            <button type="button" 
                                    onclick="if(window.mitraMapZoomIn) window.mitraMapZoomIn()"
                                    class="w-7 h-7 flex items-center justify-center text-slate-700 hover:bg-slate-100 text-xs font-extrabold transition-colors cursor-pointer border-r border-slate-200"
                                    title="Perbesar Peta (+)">
                                <i class="fa-solid fa-plus text-[10px]"></i>
                            </button>
                            <button type="button" 
                                    onclick="if(window.mitraMapZoomOut) window.mitraMapZoomOut()"
                                    class="w-7 h-7 flex items-center justify-center text-slate-700 hover:bg-slate-100 text-xs font-extrabold transition-colors cursor-pointer"
                                    title="Perkecil Peta (-)">
                                <i class="fa-solid fa-minus text-[10px]"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Floating Bottom Help & Coordinates Bar (non-blocking pointer events) -->
                <div class="mt-auto relative z-10 p-3 pointer-events-none">
                    <div class="p-2.5 rounded-xl bg-white/95 text-slate-800 shadow-lg border border-slate-200/80 backdrop-blur-xs pointer-events-auto text-[11px] font-medium flex items-center justify-between gap-2">
                        <div class="flex items-center gap-2 truncate">
                            <div class="w-6 h-6 rounded-lg bg-sky-100 text-sky-700 flex items-center justify-center shrink-0">
                                <i class="fa-solid fa-map-pin text-xs"></i>
                            </div>
                            <span class="truncate">Titik Pin: <strong class="text-[#0B2570] font-mono" x-text="form.lat + ', ' + form.lng"></strong></span>
                        </div>
                        <span class="text-[10px] text-slate-400 font-semibold shrink-0">Geser bebas di peta</span>
                    </div>
                </div>
            </div>

            <!-- Right 7 Cols: Mitra Input Fields Form -->
            <div class="lg:col-span-7 bg-white p-6 sm:p-8 rounded-2xl border border-slate-200/80 shadow-xs space-y-5">
                
                <form action="#" method="POST" @submit.prevent="saveForm()" class="space-y-5">
                    
                    <!-- Section 1: Identitas Mitra -->
                    <div class="space-y-3">
                        <div class="flex items-center gap-2 text-xs font-bold text-slate-800 pb-2 border-b border-slate-100">
                            <i class="fa-solid fa-store text-sky-600"></i>
                            <span>Identitas Mitra</span>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                            <div class="sm:col-span-2">
                                <label class="text-[10px] font-extrabold uppercase tracking-wider text-slate-500 block mb-1">NAMA MITRA</label>
                                <input type="text" 
                                       x-model="form.nama"
                                       :disabled="formMode === 'view'"
                                       placeholder="PT. Global Akuakultur..." 
                                       :class="formMode === 'view' ? 'bg-slate-100 text-slate-500 cursor-not-allowed border-slate-200 font-bold' : 'bg-slate-50/70 focus:bg-white text-slate-700 font-semibold focus:ring-sky-500'"
                                       class="w-full px-3.5 py-2.5 rounded-xl border text-xs focus:outline-none focus:ring-2 transition-all">
                            </div>
                            <div>
                                <label class="text-[10px] font-extrabold uppercase tracking-wider text-slate-500 block mb-1">ID MITRA (AUTO)</label>
                                <input type="text" 
                                       x-model="form.id" 
                                       readonly 
                                       disabled
                                       class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs font-extrabold text-slate-500 bg-slate-100 cursor-not-allowed">
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="text-[10px] font-extrabold uppercase tracking-wider text-slate-500 block mb-1">NO. TELEPON / WHATSAPP</label>
                                <div x-data="{
                                    countryMenuOpen: false,
                                    countrySearch: '',
                                    countries: [
                                        { code: 'ID', dial: '+62', name: 'Indonesia', flag: '🇮🇩', placeholder: '812-3456-7890' },
                                        { code: 'US', dial: '+1', name: 'United States', flag: '🇺🇸', placeholder: '201-555-5555' },
                                        { code: 'CN', dial: '+86', name: 'China (中国)', flag: '🇨🇳', placeholder: '138-0013-8000' },
                                        { code: 'FR', dial: '+33', name: 'France', flag: '🇫🇷', placeholder: '6 12 34 56 78' },
                                        { code: 'IN', dial: '+91', name: 'India (भारत)', flag: '🇮🇳', placeholder: '98765-43210' },
                                        { code: 'GB', dial: '+44', name: 'United Kingdom', flag: '🇬🇧', placeholder: '7911 123456' },
                                        { code: 'MY', dial: '+60', name: 'Malaysia', flag: '🇲🇾', placeholder: '12-345 6789' },
                                        { code: 'SG', dial: '+65', name: 'Singapore', flag: '🇸🇬', placeholder: '8123 4567' },
                                        { code: 'JP', dial: '+81', name: 'Japan (日本)', flag: '🇯🇵', placeholder: '90-1234-5678' },
                                        { code: 'KR', dial: '+82', name: 'South Korea (대한민국)', flag: '🇰🇷', placeholder: '10-1234-5678' },
                                        { code: 'SA', dial: '+966', name: 'Saudi Arabia (السعودية)', flag: '🇸🇦', placeholder: '50 123 4567' },
                                        { code: 'AE', dial: '+971', name: 'United Arab Emirates (الإمارات)', flag: '🇦🇪', placeholder: '50 123 4567' },
                                        { code: 'AU', dial: '+61', name: 'Australia', flag: '🇦🇺', placeholder: '412 345 678' },
                                        { code: 'DE', dial: '+49', name: 'Germany (Deutschland)', flag: '🇩🇪', placeholder: '1512 3456789' },
                                        { code: 'NL', dial: '+31', name: 'Netherlands', flag: '🇳🇱', placeholder: '6 12345678' },
                                        { code: 'TH', dial: '+66', name: 'Thailand (ไทย)', flag: '🇹🇭', placeholder: '81 234 5678' },
                                        { code: 'VN', dial: '+84', name: 'Vietnam (Việt Nam)', flag: '🇻🇳', placeholder: '91 234 5678' },
                                        { code: 'PH', dial: '+63', name: 'Philippines', flag: '🇵🇭', placeholder: '917 123 4567' },
                                        { code: 'BR', dial: '+55', name: 'Brazil (Brasil)', flag: '🇧🇷', placeholder: '11 98765-4321' },
                                        { code: 'CA', dial: '+1', name: 'Canada', flag: '🇨🇦', placeholder: '416-555-0199' },
                                        { code: 'TR', dial: '+90', name: 'Turkey (Türkiye)', flag: '🇹🇷', placeholder: '532 123 45 67' },
                                        { code: 'RU', dial: '+7', name: 'Russia (Россия)', flag: '🇷🇺', placeholder: '912 345-67-89' },
                                        { code: 'ES', dial: '+34', name: 'Spain (España)', flag: '🇪🇸', placeholder: '612 34 56 78' },
                                        { code: 'IT', dial: '+39', name: 'Italy (Italia)', flag: '🇮🇹', placeholder: '312 345 6789' },
                                        { code: 'EG', dial: '+20', name: 'Egypt (مصر)', flag: '🇪🇬', placeholder: '100 123 4567' }
                                    ],
                                    selectedCountry: { code: 'ID', dial: '+62', name: 'Indonesia', flag: '🇮🇩', placeholder: '812-3456-7890' },
                                    phoneNum: '',
                                    init() {
                                        this.selectedCountry = this.countries[0];
                                        this.syncFromModel();
                                        this.$watch('form.kontak', () => this.syncFromModel());
                                    },
                                    syncFromModel() {
                                        let val = (form.kontak || '').trim();
                                        if (!val) { this.phoneNum = ''; return; }
                                        for (let c of this.countries) {
                                            if (val.startsWith(c.dial)) {
                                                this.selectedCountry = c;
                                                this.phoneNum = val.slice(c.dial.length).trim();
                                                return;
                                            }
                                        }
                                        if (val.startsWith('0')) {
                                            this.selectedCountry = this.countries[0];
                                            this.phoneNum = val.slice(1).trim();
                                        } else {
                                            this.phoneNum = val;
                                        }
                                    },
                                    updatePhone() {
                                        let num = (this.phoneNum || '').trim();
                                        if (num.startsWith('0')) num = num.substring(1).trim();
                                        form.kontak = num ? `${this.selectedCountry.dial} ${num}` : '';
                                    },
                                    selectCountry(c) {
                                        this.selectedCountry = c;
                                        this.countryMenuOpen = false;
                                        this.countrySearch = '';
                                        this.updatePhone();
                                        this.$nextTick(() => { if (this.$refs.phoneInputRef) this.$refs.phoneInputRef.focus(); });
                                    },
                                    get filteredCountries() {
                                        if (!this.countrySearch.trim()) return this.countries;
                                        let q = this.countrySearch.toLowerCase();
                                        return this.countries.filter(c => c.name.toLowerCase().includes(q) || c.dial.includes(q) || c.code.toLowerCase().includes(q));
                                    }
                                }" @click.outside="countryMenuOpen = false" class="relative">
                                    <div class="flex items-center rounded-xl border transition-all"
                                         :class="formMode === 'view' ? 'bg-slate-100 border-slate-200' : 'border-slate-200 bg-slate-50/70 focus-within:bg-white focus-within:ring-2 focus-within:ring-sky-500 focus-within:border-sky-500'">
                                        <!-- Flag and Dial Code Button -->
                                        <button type="button"
                                                @click="if (formMode !== 'view') countryMenuOpen = !countryMenuOpen"
                                                :disabled="formMode === 'view'"
                                                :class="formMode === 'view' ? 'cursor-not-allowed opacity-75' : 'hover:bg-slate-100/90 cursor-pointer'"
                                                class="px-3 py-2.5 text-xs font-bold text-slate-700 bg-slate-100/90 border-r border-slate-200 shrink-0 flex items-center gap-1.5 transition-colors select-none focus:outline-none rounded-l-xl">
                                            <span class="text-base leading-none" x-text="selectedCountry.flag">🇮🇩</span>
                                            <span class="text-xs font-extrabold text-slate-800" x-text="selectedCountry.dial">+62</span>
                                            <i class="fa-solid fa-chevron-down text-[9px] text-slate-400 transition-transform duration-200"
                                               :class="countryMenuOpen ? 'rotate-180 text-sky-600' : ''"></i>
                                        </button>
                                        <input type="tel" 
                                               x-ref="phoneInputRef"
                                               x-model="phoneNum"
                                               @input="updatePhone()"
                                               :disabled="formMode === 'view'"
                                               :placeholder="selectedCountry.placeholder" 
                                               :class="formMode === 'view' ? 'text-slate-500 cursor-not-allowed font-bold' : 'text-slate-800 font-semibold'"
                                               class="w-full px-3.5 py-2.5 text-xs bg-transparent border-0 focus:outline-none rounded-r-xl">
                                    </div>

                                    <!-- Dropdown Popover (Matching Screenshot) -->
                                    <div x-show="countryMenuOpen"
                                         x-transition:enter="transition ease-out duration-150"
                                         x-transition:enter-start="opacity-0 translate-y-1 scale-98"
                                         x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                                         x-transition:leave="transition ease-in duration-100"
                                         x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                                         x-transition:leave-end="opacity-0 translate-y-1 scale-98"
                                         class="absolute z-50 top-full left-0 mt-1.5 w-72 sm:w-80 bg-white rounded-xl shadow-2xl border border-slate-200 overflow-hidden"
                                         style="display: none;">
                                        
                                        <!-- Search Field -->
                                        <div class="p-2 border-b border-slate-100 bg-slate-50/80 sticky top-0 z-10">
                                            <div class="relative">
                                                <i class="fa-solid fa-magnifying-glass text-slate-400 text-xs absolute left-3 top-1/2 -translate-y-1/2"></i>
                                                <input type="text"
                                                       x-model="countrySearch"
                                                       @keydown.enter.prevent
                                                       placeholder="Cari negara atau kode (+62)..."
                                                       class="w-full pl-8 pr-3 py-1.5 text-xs rounded-lg border border-slate-200 bg-white focus:outline-none focus:ring-2 focus:ring-[#00897B] focus:border-[#00897B] font-medium">
                                            </div>
                                        </div>

                                        <!-- Scrollable List -->
                                        <div class="max-h-56 overflow-y-auto divide-y divide-slate-50 py-1">
                                            <template x-for="c in filteredCountries" :key="c.code + c.dial">
                                                <button type="button"
                                                        @click="selectCountry(c)"
                                                        :class="selectedCountry.code === c.code && selectedCountry.dial === c.dial ? 'bg-[#00897B] text-white hover:bg-[#00796B]' : 'text-slate-700 hover:bg-slate-100'"
                                                        class="w-full px-3.5 py-2.5 text-xs flex items-center justify-between text-left transition-colors font-medium">
                                                    <div class="flex items-center gap-2.5 truncate pr-2">
                                                        <span class="text-base shrink-0 leading-none" x-text="c.flag"></span>
                                                        <span class="truncate" x-text="c.name"></span>
                                                    </div>
                                                    <span class="font-extrabold shrink-0 text-[11px]"
                                                          :class="selectedCountry.code === c.code && selectedCountry.dial === c.dial ? 'text-white/95' : 'text-slate-500'"
                                                          x-text="c.dial"></span>
                                                </button>
                                            </template>
                                            
                                            <div x-show="filteredCountries.length === 0" class="px-4 py-6 text-center text-xs text-slate-400">
                                                <i class="fa-solid fa-earth-americas text-slate-300 text-lg mb-1 block"></i>
                                                Negara tidak ditemukan
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div>
                                <label class="text-[10px] font-extrabold uppercase tracking-wider text-slate-500 block mb-1">EMAIL MITRA</label>
                                <input type="email" 
                                       x-model="form.email"
                                       :disabled="formMode === 'view'"
                                       placeholder="mitra@perusahaan.com" 
                                       :class="formMode === 'view' ? 'bg-slate-100 text-slate-500 cursor-not-allowed border-slate-200 font-bold' : 'bg-slate-50/70 focus:bg-white text-slate-700 font-semibold focus:ring-sky-500'"
                                       class="w-full px-3.5 py-2.5 rounded-xl border text-xs focus:outline-none focus:ring-2 transition-all">
                            </div>
                        </div>
                    </div>

                    <!-- Section 2: Peran Operasional -->
                    <div class="space-y-3">
                        <div class="flex items-center gap-2 text-xs font-bold text-slate-800 pb-2 border-b border-slate-100">
                            <i class="fa-solid fa-briefcase text-sky-600"></i>
                            <span>Peran Operasional</span>
                        </div>

                        <div>
                            <label class="text-[10px] font-extrabold uppercase tracking-wider text-slate-500 block mb-1.5">TIPE MITRA</label>
                            <div class="flex flex-wrap items-center gap-2 p-1.5 bg-slate-100 rounded-xl text-xs font-bold" :class="formMode === 'view' ? 'opacity-80 pointer-events-none' : ''">
                                <button type="button" 
                                        @click="if(formMode !== 'view') form.tipeKey = 'distributor'" 
                                        :disabled="formMode === 'view'"
                                        :class="form.tipeKey === 'distributor' ? 'bg-[#051B44] text-white shadow-xs' : 'text-slate-600 hover:text-slate-900'" 
                                        class="px-3 py-2 rounded-lg transition-all text-center flex-1 min-w-[100px]">
                                    Distributor
                                </button>
                                <button type="button" 
                                        @click="if(formMode !== 'view') form.tipeKey = 'supplier'" 
                                        :disabled="formMode === 'view'"
                                        :class="form.tipeKey === 'supplier' ? 'bg-[#051B44] text-white shadow-xs' : 'text-slate-600 hover:text-slate-900'" 
                                        class="px-3 py-2 rounded-lg transition-all text-center flex-1 min-w-[100px]">
                                    Supplier
                                </button>
                                <button type="button" 
                                        @click="if(formMode !== 'view') form.tipeKey = 'restoran'" 
                                        :disabled="formMode === 'view'"
                                        :class="form.tipeKey === 'restoran' ? 'bg-[#051B44] text-white shadow-xs' : 'text-slate-600 hover:text-slate-900'" 
                                        class="px-3 py-2 rounded-lg transition-all text-center flex-1 min-w-[100px]">
                                    Restoran
                                </button>
                                <button type="button" 
                                        @click="if(formMode !== 'view') form.tipeKey = 'pasar'" 
                                        :disabled="formMode === 'view'"
                                        :class="form.tipeKey === 'pasar' ? 'bg-[#051B44] text-white shadow-xs' : 'text-slate-600 hover:text-slate-900'" 
                                        class="px-3 py-2 rounded-lg transition-all text-center flex-1 min-w-[100px]">
                                    Pasar
                                </button>
                                <button type="button" 
                                        @click="if(formMode !== 'view') form.tipeKey = 'eksportir'" 
                                        :disabled="formMode === 'view'"
                                        :class="form.tipeKey === 'eksportir' ? 'bg-[#051B44] text-white shadow-xs' : 'text-slate-600 hover:text-slate-900'" 
                                        class="px-3 py-2 rounded-lg transition-all text-center flex-1 min-w-[100px]">
                                    Eksportir
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Section 3: Data Geospatial -->
                    <div class="space-y-3">
                        <div class="flex items-center gap-2 text-xs font-bold text-slate-800 pb-2 border-b border-slate-100">
                            <i class="fa-solid fa-location-crosshairs text-sky-600"></i>
                            <span>Data Geospatial</span>
                        </div>

                        <!-- Live Address Search Box with Autocomplete Dropdown -->
                        <div x-show="formMode !== 'view'" class="relative" @click.outside="showAddressDropdown = false">
                            <label class="text-[10px] font-extrabold uppercase tracking-wider text-slate-500 block mb-1">
                                CARI ALAMAT / NAMA TEMPAT (AUTOCOMPLETE)
                            </label>
                            
                            <div class="flex items-center rounded-xl border border-slate-200 bg-slate-50/70 focus-within:bg-white focus-within:ring-2 focus-within:ring-sky-500 focus-within:border-sky-500 transition-all">
                                <div class="pl-3.5 pr-2 text-slate-400 flex items-center justify-center shrink-0">
                                    <i x-show="!isSearchingAddress" class="fa-solid fa-magnifying-glass text-xs text-slate-400"></i>
                                    <i x-show="isSearchingAddress" class="fa-solid fa-circle-notch fa-spin text-xs text-sky-600"></i>
                                </div>

                                <input type="text"
                                       x-model="searchAddressQuery"
                                       @input="onAddressInput()"
                                       @focus="if(addressSuggestions.length > 0) showAddressDropdown = true"
                                       @keydown.enter.prevent="fetchAddressSuggestions(searchAddressQuery)"
                                       placeholder="Cari jalan, gedung, pasar, kota, atau daerah..."
                                       class="w-full py-2.5 text-xs bg-transparent border-0 text-slate-700 font-semibold focus:outline-none placeholder:text-slate-400">

                                <div class="flex items-center gap-1.5 pr-1.5 shrink-0">
                                    <!-- Clear search button -->
                                    <button type="button"
                                            x-show="searchAddressQuery"
                                            @click="searchAddressQuery = ''; addressSuggestions = []; showAddressDropdown = false"
                                            class="w-6 h-6 rounded-lg text-slate-400 hover:text-slate-600 hover:bg-slate-200/60 flex items-center justify-center text-xs transition-colors"
                                            title="Hapus teks">
                                        <i class="fa-solid fa-xmark"></i>
                                    </button>
                                    
                                    <!-- Submit Search Button -->
                                    <button type="button"
                                            @click="fetchAddressSuggestions(searchAddressQuery)"
                                            class="px-3 py-1.5 bg-[#051B44] hover:bg-navy-900 text-white text-xs font-bold rounded-lg transition-all flex items-center gap-1 shadow-xs cursor-pointer">
                                        <span>Cari</span>
                                    </button>
                                </div>
                            </div>

                            <!-- Autocomplete Suggestions Popover List -->
                            <div x-show="showAddressDropdown && addressSuggestions.length > 0"
                                 x-transition:enter="transition ease-out duration-150"
                                 x-transition:enter-start="opacity-0 translate-y-1 scale-98"
                                 x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                                 x-transition:leave="transition ease-in duration-100"
                                 x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                                 x-transition:leave-end="opacity-0 translate-y-1 scale-98"
                                 class="absolute z-50 top-full left-0 mt-1.5 w-full bg-white rounded-xl shadow-2xl border border-slate-200 overflow-hidden divide-y divide-slate-100 max-h-60 overflow-y-auto"
                                 style="display: none;">
                                
                                <div class="px-3 py-1.5 bg-slate-50 text-[10px] font-bold text-slate-500 uppercase tracking-wider flex items-center justify-between">
                                    <span>Hasil Rekomendasi Lokasi</span>
                                    <span x-text="addressSuggestions.length + ' Ditemukan'"></span>
                                </div>

                                <template x-for="(item, idx) in addressSuggestions" :key="idx">
                                    <button type="button"
                                            @click="selectAddressSuggestion(item)"
                                            class="w-full px-3.5 py-2.5 text-left text-xs hover:bg-sky-50/80 transition-colors flex items-start gap-2.5 group cursor-pointer">
                                        <div class="w-6 h-6 rounded-lg bg-sky-100 text-sky-700 flex items-center justify-center shrink-0 mt-0.5 group-hover:bg-[#051B44] group-hover:text-white transition-colors">
                                            <i class="fa-solid fa-location-dot text-xs"></i>
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <div class="flex items-center justify-between gap-2">
                                                <p class="font-bold text-slate-800 line-clamp-1 group-hover:text-[#051B44]" x-text="item.name"></p>
                                                <span class="px-2 py-0.5 rounded text-[9px] font-extrabold border shrink-0" :class="item.badgeClass" x-text="item.categoryLabel"></span>
                                            </div>
                                            <p class="text-[11px] text-slate-500 line-clamp-2 mt-0.5" x-text="item.display_name"></p>
                                            <div class="flex items-center gap-2 mt-1 text-[10px] text-sky-600 font-mono">
                                                <span>Lat: <span x-text="parseFloat(item.lat).toFixed(4)"></span></span>
                                                <span>•</span>
                                                <span>Lng: <span x-text="parseFloat(item.lon).toFixed(4)"></span></span>
                                            </div>
                                        </div>
                                    </button>
                                </template>
                            </div>

                            <!-- Not Found Message -->
                            <div x-show="showAddressDropdown && addressSuggestions.length === 0 && !isSearchingAddress && searchAddressQuery.length >= 3"
                                 class="absolute z-50 top-full left-0 mt-1.5 w-full bg-white rounded-xl shadow-xl border border-slate-200 p-4 text-center text-xs text-slate-500"
                                 style="display: none;">
                                <i class="fa-solid fa-map-location text-slate-300 text-xl mb-1 block"></i>
                                Alamat tidak ditemukan. Coba masukkan kata kunci pencarian yang berbeda.
                            </div>
                        </div>

                        <!-- Textarea Alamat Lengkap Final -->
                        <div>
                            <label class="text-[10px] font-extrabold uppercase tracking-wider text-slate-500 block mb-1">
                                ALAMAT LENGKAP
                            </label>
                            <textarea rows="2" 
                                      x-model="form.alamat"
                                      :disabled="formMode === 'view'"
                                      placeholder="Jl. Raya Pelabuhan No. 45..." 
                                      :class="formMode === 'view' ? 'bg-slate-100 text-slate-500 cursor-not-allowed border-slate-200 font-bold' : 'bg-slate-50/70 focus:bg-white text-slate-700 font-semibold focus:ring-sky-500 border-slate-200'"
                                      class="w-full px-3.5 py-2.5 rounded-xl border text-xs focus:outline-none focus:ring-2 transition-all"></textarea>
                        </div>

                        <!-- Latitude & Longitude Inputs -->
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="text-[10px] font-extrabold uppercase tracking-wider text-slate-500 block mb-1">LATITUDE</label>
                                <input type="text" 
                                       id="latInput" 
                                       x-model="form.lat"
                                       readonly 
                                       :disabled="formMode === 'view'"
                                       :class="formMode === 'view' ? 'bg-slate-100 text-slate-500 cursor-not-allowed border-slate-200' : 'bg-slate-100/80 text-slate-700 font-bold cursor-pointer border-slate-200'"
                                       class="w-full px-3.5 py-2.5 rounded-xl border text-xs font-mono font-extrabold"
                                       title="Ambil otomatis dengan klik pada peta atau cari alamat">
                            </div>
                            <div>
                                <label class="text-[10px] font-extrabold uppercase tracking-wider text-slate-500 block mb-1">LONGITUDE</label>
                                <input type="text" 
                                       id="lngInput" 
                                       x-model="form.lng"
                                       readonly 
                                       :disabled="formMode === 'view'"
                                       :class="formMode === 'view' ? 'bg-slate-100 text-slate-500 cursor-not-allowed border-slate-200' : 'bg-slate-100/80 text-slate-700 font-bold cursor-pointer border-slate-200'"
                                       class="w-full px-3.5 py-2.5 rounded-xl border text-xs font-mono font-extrabold"
                                       title="Ambil otomatis dengan klik pada peta atau cari alamat">
                            </div>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
                        <button type="button" 
                                @click="showForm = false" 
                                class="px-5 py-2 rounded-xl border border-slate-200 text-xs font-bold text-slate-600 hover:bg-slate-50 transition-colors">
                            <span x-text="formMode === 'view' ? 'Tutup' : 'Batal'"></span>
                        </button>
                        <button x-show="formMode !== 'view'"
                                type="submit" 
                                :disabled="isSaving"
                                class="px-5 py-2 rounded-xl bg-[#051B44] hover:bg-navy-900 text-white font-bold text-xs shadow-xs transition-all flex items-center gap-2 disabled:opacity-50 disabled:cursor-not-allowed cursor-pointer">
                            <span x-text="isSaving ? 'Menyimpan ke Database...' : (formMode === 'create' ? 'Simpan Data Baru' : 'Simpan Perubahan')"></span>
                            <i class="fa-solid" :class="isSaving ? 'fa-circle-notch fa-spin text-xs text-sky-400' : 'fa-circle-check text-xs'"></i>
                        </button>
                        <button x-show="formMode === 'view'"
                                type="button"
                                @click="formMode = 'edit'; initMitraMap(form.lat, form.lng, false)"
                                class="px-5 py-2 rounded-xl bg-amber-600 hover:bg-amber-700 text-white font-bold text-xs shadow-xs transition-all flex items-center gap-2">
                            <span>Edit Data Ini</span>
                            <i class="fa-solid fa-pen-to-square text-xs"></i>
                        </button>
                    </div>

                </form>

            </div>

        </div>

    </div>

    <!-- Filters & Metric Header Row (Directory Mode) -->
    <div x-show="!showForm" class="grid grid-cols-1 md:grid-cols-4 gap-4">
        
        <!-- Search Box -->
        <div class="bg-white p-3.5 rounded-2xl border border-slate-200/80 shadow-xs flex items-center gap-3">
            <div class="w-8 h-8 rounded-xl bg-slate-100 text-slate-500 flex items-center justify-center shrink-0">
                <i class="fa-solid fa-magnifying-glass text-xs"></i>
            </div>
            <div class="flex-1">
                <span class="text-[9px] font-extrabold text-slate-400 uppercase tracking-widest block">CARI MITRA</span>
                <input type="text" x-model="searchQuery" placeholder="Nama, ID, atau alamat..." class="w-full bg-transparent text-xs font-bold text-slate-800 focus:outline-none mt-0.5 placeholder:font-medium placeholder:text-slate-400">
            </div>
        </div>

        <!-- Filter 1: Tipe Mitra -->
        <div class="bg-white p-3.5 rounded-2xl border border-slate-200/80 shadow-xs flex items-center gap-3">
            <div class="w-8 h-8 rounded-xl bg-slate-100 text-slate-500 flex items-center justify-center shrink-0">
                <i class="fa-solid fa-sliders text-xs"></i>
            </div>
            <div class="flex-1">
                <span class="text-[9px] font-extrabold text-slate-400 uppercase tracking-widest block">TIPE MITRA</span>
                <select x-model="filterTipe" class="w-full bg-transparent text-xs font-bold text-slate-800 focus:outline-none cursor-pointer mt-0.5">
                    <option value="">Semua Tipe</option>
                    <option value="restoran">Restoran</option>
                    <option value="supplier">Supplier Frozen Food</option>
                    <option value="pasar">Pasar Tradisional</option>
                    <option value="eksportir">Eksportir</option>
                    <option value="distributor">Distributor</option>
                </select>
            </div>
        </div>

        <!-- Filter 2: Wilayah -->
        <div class="bg-white p-3.5 rounded-2xl border border-slate-200/80 shadow-xs flex items-center gap-3">
            <div class="w-8 h-8 rounded-xl bg-slate-100 text-slate-500 flex items-center justify-center shrink-0">
                <i class="fa-solid fa-location-dot text-xs"></i>
            </div>
            <div class="flex-1">
                <span class="text-[9px] font-extrabold text-slate-400 uppercase tracking-widest block">WILAYAH</span>
                <select x-model="filterWilayah" class="w-full bg-transparent text-xs font-bold text-slate-800 focus:outline-none cursor-pointer mt-0.5">
                    <option value="">Seluruh Indonesia</option>
                    <option value="jakarta">DKI Jakarta</option>
                    <option value="jabar">Jawa Barat</option>
                    <option value="jateng">Jawa Tengah</option>
                </select>
            </div>
        </div>

        <!-- Metric Card: Total Mitra Aktif -->
        <div class="bg-white p-3.5 rounded-2xl border border-slate-200/80 shadow-xs flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl bg-[#BEE3F8]/60 text-[#006699] flex items-center justify-center shrink-0">
                    <i class="fa-solid fa-users text-sm"></i>
                </div>
                <div>
                    <span class="text-[9px] font-extrabold text-slate-400 uppercase tracking-widest block">TOTAL MITRA AKTIF</span>
                    <div class="flex items-baseline gap-2 mt-0.5">
                        <h3 class="text-lg font-extrabold text-slate-900" x-text="mitras.length"></h3>
                        <span class="text-[10px] font-extrabold text-emerald-600">+5 bln ini</span>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <!-- Mitra Directory Table Card (Directory Mode) -->
    <div x-show="!showForm" class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50/60 border-b border-slate-100 text-[10px] font-extrabold text-slate-400 uppercase tracking-wider">
                        <th class="py-3.5 px-6">INFO MITRA</th>
                        <th class="py-3.5 px-6">TIPE</th>
                        <th class="py-3.5 px-6">LOKASI &amp; ALAMAT</th>
                        <th class="py-3.5 px-6 text-right">AKSI</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-xs font-medium text-slate-700">
                    
                    <template x-for="(mitra, index) in filteredMitras" :key="mitra.id">
                        <tr class="hover:bg-slate-50/50 transition-colors">
                            <td class="py-4 px-6">
                                <div class="flex items-center gap-3">
                                    <template x-if="mitra.image">
                                        <img :src="mitra.image" 
                                             :alt="mitra.nama" 
                                             class="w-10 h-10 rounded-xl object-cover border border-slate-200">
                                    </template>
                                    <template x-if="!mitra.image">
                                        <div class="w-10 h-10 rounded-xl bg-sky-100 text-sky-700 flex items-center justify-center shrink-0 border border-sky-200 font-bold text-sm">
                                            <i class="fa-solid fa-store"></i>
                                        </div>
                                    </template>
                                    <div>
                                        <h4 class="font-extrabold text-[#0055CC] text-sm cursor-pointer hover:underline" @click="openViewForm(mitra)" x-text="mitra.nama"></h4>
                                        <span class="text-[10px] text-slate-400 block font-normal" x-text="'ID: ' + mitra.id"></span>
                                    </div>
                                </div>
                            </td>
                            <td class="py-4 px-6">
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold uppercase"
                                      :class="{
                                          'bg-[#E0F2FE] text-[#0284C7]': mitra.tipeKey === 'restoran' || mitra.tipe.toLowerCase().includes('restoran'),
                                          'bg-[#C6F6D5] text-[#22543D]': mitra.tipeKey === 'supplier' || mitra.tipe.toLowerCase().includes('supplier'),
                                          'bg-[#E2E8F0] text-[#475569]': mitra.tipeKey === 'pasar' || mitra.tipe.toLowerCase().includes('pasar'),
                                          'bg-[#051B44] text-white': mitra.tipeKey === 'eksportir' || mitra.tipe.toLowerCase().includes('eksportir'),
                                          'bg-amber-100 text-amber-800': mitra.tipeKey === 'distributor' || mitra.tipe.toLowerCase().includes('distributor')
                                      }"
                                      x-text="mitra.tipe">
                                </span>
                            </td>
                            <td class="py-4 px-6">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-8 rounded-lg bg-slate-100 border border-slate-200 flex items-center justify-center shrink-0 text-slate-400">
                                        <i class="fa-solid fa-map-location-dot text-xs"></i>
                                    </div>
                                    <span class="text-xs text-slate-700 font-medium" x-text="mitra.alamat"></span>
                                </div>
                            </td>
                            <td class="py-4 px-6 text-right">
                                <!-- Action Dropdown -->
                                <div class="relative inline-block text-left" x-data="{ open: false }">
                                    <button @click="open = !open" @click.away="open = false"
                                            class="w-8 h-8 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-600 flex items-center justify-center transition-colors">
                                        <i class="fa-solid fa-ellipsis-vertical text-sm"></i>
                                    </button>
                                    
                                    <div x-show="open"
                                         x-transition:enter="transition ease-out duration-100"
                                         x-transition:enter-start="transform opacity-0 scale-95"
                                         x-transition:enter-end="transform opacity-100 scale-100"
                                         x-transition:leave="transition ease-in duration-75"
                                         x-transition:leave-start="transform opacity-100 scale-100"
                                         x-transition:leave-end="transform opacity-0 scale-95"
                                         class="absolute right-0 mt-2 w-44 rounded-xl bg-white border border-slate-200 shadow-xl py-1.5 z-50 text-left">
                                        
                                        <!-- View Lengkap -->
                                        <button @click="open = false; openViewForm(mitra)" 
                                                class="w-full px-3.5 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-50 flex items-center gap-2.5">
                                            <i class="fa-solid fa-eye text-sky-600 w-4"></i>
                                            <span>View Lengkap</span>
                                        </button>

                                        <!-- Edit Data -->
                                        <button @click="open = false; openEditForm(mitra)" 
                                                class="w-full px-3.5 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-50 flex items-center gap-2.5">
                                            <i class="fa-solid fa-pen-to-square text-amber-600 w-4"></i>
                                            <span>Edit Data</span>
                                        </button>

                                        <div class="my-1 border-t border-slate-100"></div>

                                        <!-- Hapus Data -->
                                        <button @click="open = false; deleteMitra(mitra)" 
                                                class="w-full px-3.5 py-2 text-xs font-semibold text-red-600 hover:bg-red-50 flex items-center gap-2.5">
                                            <i class="fa-solid fa-trash-can text-red-500 w-4"></i>
                                            <span>Hapus Data</span>
                                        </button>

                                    </div>
                                </div>
                            </td>
                        </tr>
                    </template>

                    <!-- Empty State -->
                    <tr x-show="filteredMitras.length === 0">
                        <td colspan="4" class="py-12 text-center text-slate-400">
                            <i class="fa-solid fa-folder-open text-3xl mb-2 block"></i>
                            <span class="text-xs font-medium">Tidak ada data mitra yang sesuai dengan pencarian/filter.</span>
                        </td>
                    </tr>

                </tbody>
            </table>
        </div>

        <!-- Table Footer Pagination -->
        <div class="p-4 border-t border-slate-100 flex flex-col sm:flex-row items-center justify-between gap-4 text-xs font-medium text-slate-500">
            <span x-text="'Menampilkan 1-' + filteredMitras.length + ' dari ' + mitras.length + ' Mitra'"></span>
            <div class="flex items-center gap-1">
                <button class="w-7 h-7 rounded border border-slate-200 flex items-center justify-center text-slate-400 hover:bg-slate-50">&lt;</button>
                <button class="w-7 h-7 rounded bg-[#051B44] text-white font-bold flex items-center justify-center">1</button>
                <button class="w-7 h-7 rounded border border-slate-200 flex items-center justify-center text-slate-600 hover:bg-slate-50">&gt;</button>
            </div>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
    function mitraApp() {
        return {
            init() {
                window.mitraAppInstance = this;
            },
            showForm: false,
            formMode: "create",
            filterTipe: "",
            filterWilayah: "",
            searchQuery: "",
            toastMessage: "",
            showToast: false,
            isSaving: false,

            mitras: {!! json_encode($mitras ?? []) !!},

            form: {
                id_mitra: null,
                id: "",
                nama: "",
                tipeKey: "distributor",
                tipe: "Distributor",
                alamat: "",
                lat: "-6.200000",
                lng: "106.816666",
                kontak: "",
                email: "",
                image: ""
            },

            searchAddressQuery: "",
            addressSuggestions: [],
            isSearchingAddress: false,
            showAddressDropdown: false,
            searchAddressTimeout: null,
            mapLayerMode: "satellite",

            toggleMapLayer() {
                if (typeof window.toggleMitraMapLayer === "function") {
                    this.mapLayerMode = window.toggleMitraMapLayer();
                } else {
                    this.mapLayerMode = this.mapLayerMode === "satellite" ? "street" : "satellite";
                }
            },

            onAddressInput() {
                if (this.searchAddressTimeout) clearTimeout(this.searchAddressTimeout);
                const q = (this.searchAddressQuery || "").trim();
                if (q.length < 2) {
                    this.addressSuggestions = [];
                    this.showAddressDropdown = false;
                    this.isSearchingAddress = false;
                    return;
                }
                this.isSearchingAddress = true;
                this.showAddressDropdown = true;
                this.searchAddressTimeout = setTimeout(() => {
                    this.fetchAddressSuggestions(q);
                }, 350);
            },

            detectCategory(nameStr, typeStr, classStr) {
                const n = (nameStr || "").toLowerCase();
                const t = (typeStr || "").toLowerCase();
                const c = (classStr || "").toLowerCase();

                // 1. Rumah Makan / Kuliner
                if (t === "restaurant" || t === "cafe" || t === "fast_food" || t === "food_court" || n.includes("rumah makan") || n.includes("lesehan") || n.includes("resto") || n.includes("warung") || n.includes("kafe") || n.includes("cafe") || n.includes("kuliner") || n.includes("bakso") || n.includes("sate") || n.includes("seafood")) {
                    return { label: "Rumah Makan / Kuliner", badgeClass: "bg-rose-50 text-rose-700 border-rose-200" };
                }
                // 2. PT / Perusahaan / Gudang / Industri
                if (n.startsWith("pt ") || n.startsWith("pt.") || n.startsWith("cv ") || n.startsWith("cv.") || n.includes("gudang") || n.includes("pabrik") || n.includes("industri") || n.includes("aquaculture") || n.includes("akuakultur") || c === "industrial" || c === "office") {
                    return { label: "PT / Perusahaan / Gudang", badgeClass: "bg-indigo-50 text-indigo-700 border-indigo-200" };
                }
                // 3. Pasar / Niaga / Pertokoan
                if (t === "marketplace" || t === "supermarket" || c === "shop" || c === "commercial" || n.includes("pasar") || n.includes("toko") || n.includes("mart") || n.includes("swalayan") || n.includes("grosir") || n.includes("agen")) {
                    return { label: "Pasar / Niaga", badgeClass: "bg-purple-50 text-purple-700 border-purple-200" };
                }
                // 4. Dusun / Blok / Kampung / Gang
                if (t === "hamlet" || t === "allotments" || t === "isolated_dwelling" || n.includes("dusun") || n.includes("blok") || n.includes("kampung") || n.includes("kp.") || n.includes("gang") || n.includes("gg.")) {
                    return { label: "Dusun / Blok / Kampung", badgeClass: "bg-teal-50 text-teal-700 border-teal-200" };
                }
                // 5. Desa / Kelurahan
                if (t === "village" || t === "suburb" || t === "neighbourhood" || n.includes("desa") || n.includes("kelurahan")) {
                    return { label: "Desa / Kelurahan", badgeClass: "bg-emerald-50 text-emerald-700 border-emerald-200" };
                }
                // 6. Kecamatan / Kota / Kabupaten
                if (t === "administrative" || t === "city" || t === "town" || t === "subdistrict" || t === "district" || n.includes("kecamatan") || n.includes("kabupaten") || n.includes("kota")) {
                    return { label: "Kecamatan / Kota", badgeClass: "bg-sky-50 text-sky-700 border-sky-200" };
                }
                // 7. Jalan / Rute
                if (c === "highway" || t === "road" || t === "residential" || t === "primary" || t === "secondary" || t === "tertiary" || t === "track" || n.startsWith("jl") || n.startsWith("jalan") || n.includes("raya")) {
                    return { label: "Jalan / Rute", badgeClass: "bg-amber-50 text-amber-700 border-amber-200" };
                }
                // 8. Wisata & Alam
                if (c === "tourism" || t === "lake" || t === "water" || t === "wood" || t === "nature_reserve" || t === "park" || n.includes("situ") || n.includes("danau") || n.includes("wisata") || n.includes("curug") || n.includes("pantai")) {
                    return { label: "Wisata / Alam", badgeClass: "bg-cyan-50 text-cyan-700 border-cyan-200" };
                }
                // 9. Fasilitas Publik / Pemerintahan / Ibadah
                if (c === "amenity" || t === "place_of_worship" || t === "police" || t === "post_office" || t === "school" || t === "government" || n.includes("kantor") || n.includes("puskesmas") || n.includes("rs") || n.includes("sekolah") || n.includes("masjid") || n.includes("polsek") || n.includes("pos")) {
                    return { label: "Fasilitas Publik", badgeClass: "bg-blue-50 text-blue-700 border-blue-200" };
                }
                return { label: "Lokasi / Titik", badgeClass: "bg-slate-100 text-slate-700 border-slate-200" };
            },

            fetchAddressSuggestions(query) {
                let cleanQuery = (query || "").trim().replace(/[–—]/g, " ");
                if (!cleanQuery || cleanQuery.length < 2) {
                    this.addressSuggestions = [];
                    this.isSearchingAddress = false;
                    return;
                }

                this.isSearchingAddress = true;
                
                const queries = [cleanQuery];
                
                if (cleanQuery.includes(",")) {
                    const parts = cleanQuery.split(",").map(p => p.trim()).filter(Boolean);
                    if (parts.length >= 2) {
                        queries.push(parts[0] + " " + parts[1]);
                        queries.push(parts.slice(0, 3).join(" "));
                    }
                } else {
                    const words = cleanQuery.split(/\s+/);
                    if (words.length > 3) {
                        queries.push(words.slice(0, 3).join(" "));
                    }
                }

                const uniqueQueries = [...new Set(queries)].slice(0, 2);
                const fetchPromises = [];
                const userLat = parseFloat(this.form.lat) || -6.9175;
                const userLng = parseFloat(this.form.lng) || 107.6191;

                uniqueQueries.forEach(q => {
                    fetchPromises.push(
                        fetch("https://nominatim.openstreetmap.org/search?format=json&q=" + encodeURIComponent(q) + "&addressdetails=1&limit=10&countrycodes=id")
                            .then(r => r.json()).catch(() => [])
                    );
                    fetchPromises.push(
                        fetch("https://photon.komoot.io/api/?q=" + encodeURIComponent(q) + "&limit=35&lat=" + userLat + "&lon=" + userLng)
                            .then(r => r.json()).catch(() => ({ features: [] }))
                    );
                });

                Promise.allSettled(fetchPromises).then(results => {
                    let combined = [];
                    const seenKeys = new Set();

                    results.forEach(res => {
                        if (res.status !== "fulfilled" || !res.value) return;
                        const data = res.value;

                        // 1. Nominatim Results
                        if (Array.isArray(data)) {
                            data.forEach(item => {
                                if (!item.lat || !item.lon) return;
                                const nameTitle = item.name || item.display_name.split(",")[0] || "Lokasi";
                                const coordKey = parseFloat(item.lat).toFixed(4) + "_" + parseFloat(item.lon).toFixed(4);
                                const uniqueKey = nameTitle.toLowerCase().trim() + "_" + coordKey;
                                
                                if (!seenKeys.has(uniqueKey)) {
                                    seenKeys.add(uniqueKey);
                                    const cat = this.detectCategory(nameTitle + " " + item.display_name, item.type, item.class);
                                    combined.push({
                                        name: nameTitle,
                                        display_name: item.display_name,
                                        lat: item.lat,
                                        lon: item.lon,
                                        categoryLabel: cat.label,
                                        badgeClass: cat.badgeClass
                                    });
                                }
                            });
                        }

                        // 2. Photon Features (OpenStreetMap POI & Full-Text Engine)
                        if (data.features && Array.isArray(data.features)) {
                            data.features.forEach(f => {
                                if (!f.geometry || !f.geometry.coordinates) return;
                                const p = f.properties || {};
                                if (p.countrycode && p.countrycode.toUpperCase() !== "ID") return;

                                const lon = f.geometry.coordinates[0];
                                const lat = f.geometry.coordinates[1];
                                const nameTitle = p.name || p.street || "Lokasi";
                                const coordKey = parseFloat(lat).toFixed(4) + "_" + parseFloat(lon).toFixed(4);
                                const uniqueKey = nameTitle.toLowerCase().trim() + "_" + coordKey;

                                if (!seenKeys.has(uniqueKey)) {
                                    seenKeys.add(uniqueKey);
                                    const parts = [nameTitle, p.street, p.district, p.city || p.county, p.state, p.country].filter(Boolean);
                                    const uniqueParts = [...new Set(parts)];
                                    const cat = this.detectCategory(nameTitle + " " + (p.street || "") + " " + (p.district || ""), p.osm_value, p.osm_key);
                                    combined.push({
                                        name: nameTitle,
                                        display_name: uniqueParts.join(", "),
                                        lat: lat,
                                        lon: lon,
                                        categoryLabel: cat.label,
                                        badgeClass: cat.badgeClass
                                    });
                                }
                            });
                        }
                    });

                    this.addressSuggestions = combined;
                    this.isSearchingAddress = false;
                    this.showAddressDropdown = true;
                }).catch(err => {
                    console.error("Geocoder fetch error:", err);
                    this.isSearchingAddress = false;
                });
            },

            selectAddressSuggestion(item) {
                this.form.alamat = item.display_name;
                this.form.lat = parseFloat(item.lat).toFixed(6);
                this.form.lng = parseFloat(item.lon).toFixed(6);
                this.searchAddressQuery = item.display_name;
                this.showAddressDropdown = false;
                this.notify("Lokasi mitra terpilih & koordinat peta disinkronkan!");
                if (typeof window.flyToMitraLocation === "function") {
                    window.flyToMitraLocation(item.lat, item.lon, 16);
                }
            },

            openCreateForm() {
                this.formMode = "create";
                this.searchAddressQuery = "";
                this.addressSuggestions = [];
                this.showAddressDropdown = false;
                
                let maxId = 0;
                if (Array.isArray(this.mitras)) {
                    this.mitras.forEach(m => {
                        const num = parseInt(m.id_mitra || (m.id ? m.id.replace(/[^0-9]/g, '') : 0)) || 0;
                        if (num > maxId) maxId = num;
                    });
                }
                const nextIdStr = "MTR-2026-" + String(maxId + 1).padStart(3, '0');

                this.form = {
                    id_mitra: null,
                    id: nextIdStr,
                    nama: "",
                    tipeKey: "distributor",
                    tipe: "Distributor",
                    alamat: "",
                    lat: "-6.200000",
                    lng: "106.816666",
                    kontak: "",
                    email: "",
                    image: ""
                };
                this.showForm = true;
                this.$nextTick(() => initMitraMap(this.form.lat, this.form.lng, false));
            },

            openEditForm(mitra) {
                this.formMode = "edit";
                this.form = JSON.parse(JSON.stringify(mitra));
                if (!this.form.id_mitra && mitra.id_mitra) {
                    this.form.id_mitra = mitra.id_mitra;
                }
                if (!this.form.id) {
                    this.form.id = 'MTR-2026-' + String(mitra.id_mitra || 1).padStart(3, '0');
                }
                this.searchAddressQuery = mitra.alamat || "";
                this.addressSuggestions = [];
                this.showAddressDropdown = false;
                this.showForm = true;
                this.$nextTick(() => initMitraMap(this.form.lat, this.form.lng, false));
            },

            openViewForm(mitra) {
                this.formMode = "view";
                this.form = JSON.parse(JSON.stringify(mitra));
                if (!this.form.id) {
                    this.form.id = 'MTR-2026-' + String(mitra.id_mitra || 1).padStart(3, '0');
                }
                this.searchAddressQuery = mitra.alamat || "";
                this.addressSuggestions = [];
                this.showAddressDropdown = false;
                this.showForm = true;
                this.$nextTick(() => initMitraMap(this.form.lat, this.form.lng, true));
            },

            async saveForm() {
                if (this.formMode === "view" || this.isSaving) return;

                if (!this.form.nama || !this.form.nama.trim()) {
                    alert("Nama mitra wajib diisi!");
                    return;
                }
                if (!this.form.alamat || !this.form.alamat.trim()) {
                    alert("Alamat lengkap mitra wajib diisi!");
                    return;
                }

                const tipeMap = {
                    distributor: "Distributor",
                    supplier: "Supplier Frozen Food",
                    restoran: "Restoran",
                    pasar: "Pasar Tradisional",
                    eksportir: "Eksportir"
                };
                this.form.tipe = tipeMap[this.form.tipeKey] || "Distributor";

                this.isSaving = true;

                try {
                    if (this.formMode === "create") {
                        const response = await fetch("{{ route('mitra.store') }}", {
                            method: "POST",
                            headers: {
                                "Content-Type": "application/json",
                                "Accept": "application/json",
                                "X-CSRF-TOKEN": "{{ csrf_token() }}"
                            },
                            body: JSON.stringify(this.form)
                        });

                        const result = await response.json();

                        if (response.ok && result.success) {
                            this.mitras.unshift(result.data);
                            this.notify(result.message || "Data Mitra baru berhasil disimpan ke database!");
                            this.showForm = false;
                        } else {
                            const errDetail = result.errors ? Object.values(result.errors).flat().join("\n") : (result.message || "Gagal menyimpan mitra");
                            alert("Error: " + errDetail);
                        }
                    } else if (this.formMode === "edit") {
                        const targetId = this.form.id_mitra;
                        if (!targetId) {
                            alert("ID Mitra tidak ditemukan untuk pembaruan.");
                            this.isSaving = false;
                            return;
                        }

                        const response = await fetch("/mitra/" + targetId, {
                            method: "PUT",
                            headers: {
                                "Content-Type": "application/json",
                                "Accept": "application/json",
                                "X-CSRF-TOKEN": "{{ csrf_token() }}"
                            },
                            body: JSON.stringify(this.form)
                        });

                        const result = await response.json();

                        if (response.ok && result.success) {
                            const index = this.mitras.findIndex(m => m.id_mitra === targetId || m.id === this.form.id);
                            if (index !== -1) {
                                this.mitras[index] = result.data;
                            }
                            this.notify(result.message || "Data Mitra berhasil diperbarui di database!");
                            this.showForm = false;
                        } else {
                            const errDetail = result.errors ? Object.values(result.errors).flat().join("\n") : (result.message || "Gagal memperbarui mitra");
                            alert("Error: " + errDetail);
                        }
                    }
                } catch (err) {
                    console.error("Save mitra error:", err);
                    alert("Terjadi kesalahan koneksi server saat menyimpan mitra.");
                } finally {
                    this.isSaving = false;
                }
            },

            async deleteMitra(mitra) {
                if (!confirm('Apakah Anda yakin ingin menghapus data mitra "' + mitra.nama + '" dari database?')) {
                    return;
                }

                const targetId = mitra.id_mitra;
                if (!targetId) {
                    this.mitras = this.mitras.filter(m => m.id !== mitra.id);
                    this.notify('Data mitra "' + mitra.nama + '" berhasil dihapus!');
                    return;
                }

                try {
                    const response = await fetch("/mitra/" + targetId, {
                        method: "DELETE",
                        headers: {
                            "Content-Type": "application/json",
                            "Accept": "application/json",
                            "X-CSRF-TOKEN": "{{ csrf_token() }}"
                        }
                    });

                    const result = await response.json();

                    if (response.ok && result.success) {
                        this.mitras = this.mitras.filter(m => m.id_mitra !== targetId && m.id !== mitra.id);
                        this.notify(result.message || ('Data mitra "' + mitra.nama + '" berhasil dihapus!'));
                    } else {
                        alert(result.message || "Gagal menghapus mitra dari database.");
                    }
                } catch (err) {
                    console.error("Delete mitra error:", err);
                    alert("Terjadi kesalahan koneksi server saat menghapus mitra.");
                }
            },

            notify(msg) {
                this.toastMessage = msg;
                this.showToast = true;
                setTimeout(() => { this.showToast = false; }, 3500);
            },

            get filteredMitras() {
                return this.mitras.filter(m => {
                    const matchTipe = !this.filterTipe || m.tipeKey === this.filterTipe;
                    const matchWilayah = !this.filterWilayah || (m.wilayah && m.wilayah === this.filterWilayah) || (m.alamat && m.alamat.toLowerCase().includes(this.filterWilayah.toLowerCase()));
                    const matchSearch = !this.searchQuery || m.nama.toLowerCase().includes(this.searchQuery.toLowerCase()) || m.id.toLowerCase().includes(this.searchQuery.toLowerCase()) || m.alamat.toLowerCase().includes(this.searchQuery.toLowerCase());
                    return matchTipe && matchWilayah && matchSearch;
                });
            }
        };
    }

    let mitraMapInstance = null;
    let currentMarker = null;

    let currentMapMode = 'satellite';
    let activeTileLayer = null;

    function getMapTileLayer(mode) {
        if (mode === 'satellite') {
            return L.tileLayer('https://mt{s}.google.com/vt/lyrs=y&x={x}&y={y}&z={z}', {
                maxZoom: 20,
                subdomains: ['0', '1', '2', '3'],
                attribution: '© Google Satellite'
            });
        } else {
            return L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19,
                attribution: '© OpenStreetMap'
            });
        }
    }

    // Toggle layer satelit / jalan raya
    window.toggleMitraMapLayer = function() {
        if (!mitraMapInstance) return currentMapMode;
        
        currentMapMode = currentMapMode === 'satellite' ? 'street' : 'satellite';
        
        if (activeTileLayer) {
            mitraMapInstance.removeLayer(activeTileLayer);
        }
        
        activeTileLayer = getMapTileLayer(currentMapMode);
        activeTileLayer.addTo(mitraMapInstance);
        activeTileLayer.bringToBack();
        
        return currentMapMode;
    };

    function initMitraMap(latVal, lngVal, isReadOnly = false) {
        const latNum = parseFloat(latVal) || -6.200000;
        const lngNum = parseFloat(lngVal) || 106.816666;

        const mapContainer = document.getElementById('mitraMapPicker');
        if (!mapContainer) return;

        if (!mitraMapInstance) {
            mitraMapInstance = L.map('mitraMapPicker', {
                zoomControl: false,
                dragging: !isReadOnly,
                touchZoom: !isReadOnly,
                scrollWheelZoom: true,
                doubleClickZoom: true,
                boxZoom: true
            }).setView([latNum, lngNum], 14);

            activeTileLayer = getMapTileLayer(currentMapMode);
            activeTileLayer.addTo(mitraMapInstance);
        } else {
            mitraMapInstance.setView([latNum, lngNum], 14);
            if (isReadOnly) {
                mitraMapInstance.dragging.disable();
            } else {
                mitraMapInstance.dragging.enable();
            }
            setTimeout(() => {
                mitraMapInstance.invalidateSize();
            }, 250);
        }

        if (currentMarker) {
            mitraMapInstance.removeLayer(currentMarker);
        }

        currentMarker = L.marker([latNum, lngNum], { draggable: !isReadOnly }).addTo(mitraMapInstance);

        function updateCoords(l1, l2, performReverse = true) {
            const latVal = parseFloat(l1).toFixed(6);
            const lngVal = parseFloat(l2).toFixed(6);

            const latElem = document.getElementById('latInput');
            const lngElem = document.getElementById('lngInput');
            if (latElem) { latElem.value = latVal; latElem.dispatchEvent(new Event('input')); }
            if (lngElem) { lngElem.value = lngVal; lngElem.dispatchEvent(new Event('input')); }

            // Update Alpine active form state
            if (window.mitraAppInstance && window.mitraAppInstance.form) {
                window.mitraAppInstance.form.lat = latVal;
                window.mitraAppInstance.form.lng = lngVal;
            }

            if (window.Alpine && typeof window.Alpine.$data === 'function') {
                const rootElem = document.querySelector('[x-data]');
                if (rootElem) {
                    const data = window.Alpine.$data(rootElem);
                    if (data && data.form) {
                        data.form.lat = latVal;
                        data.form.lng = lngVal;
                    }
                }
            }

            if (performReverse) {
                reverseGeocode(l1, l2);
            }
        }

        mitraMapInstance.off('click');
        if (!isReadOnly) {
            mitraMapInstance.on('click', function(e) {
                const lat = e.latlng.lat;
                const lng = e.latlng.lng;
                currentMarker.setLatLng([lat, lng]);
                updateCoords(lat, lng, true);
            });

            currentMarker.on('dragend', function(e) {
                const latlng = currentMarker.getLatLng();
                updateCoords(latlng.lat, latlng.lng, true);
            });
        }
    }

    // Fungsi fokus kembali ke pin marker
    window.recenterMitraMap = function() {
        if (mitraMapInstance && currentMarker) {
            mitraMapInstance.flyTo(currentMarker.getLatLng(), 16, {
                animate: true,
                duration: 0.8
            });
        }
    };

    // Fungsi zoom in & zoom out
    window.mitraMapZoomIn = function() {
        if (mitraMapInstance) {
            mitraMapInstance.zoomIn();
        }
    };

    window.mitraMapZoomOut = function() {
        if (mitraMapInstance) {
            mitraMapInstance.zoomOut();
        }
    };

    // Fungsi terbang ke koordinat lokasi hasil pencarian alamat
    window.flyToMitraLocation = function(latVal, lngVal, zoomLevel = 16) {
        const latNum = parseFloat(latVal);
        const lngNum = parseFloat(lngVal);
        if (isNaN(latNum) || isNaN(lngNum)) return;

        if (!mitraMapInstance) {
            initMitraMap(latNum, lngNum, false);
            return;
        }

        mitraMapInstance.flyTo([latNum, lngNum], zoomLevel, {
            animate: true,
            duration: 1.2
        });

        if (currentMarker) {
            currentMarker.setLatLng([latNum, lngNum]);
        } else {
            currentMarker = L.marker([latNum, lngNum], { draggable: true }).addTo(mitraMapInstance);
        }
    };

    // Fungsi reverse geocoding saat marker digeser atau peta diklik
    function reverseGeocode(lat, lng) {
        fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}&zoom=18&addressdetails=1`, {
            headers: { 'Accept-Language': 'id,en' }
        })
        .then(res => res.json())
        .then(data => {
            if (data && data.display_name) {
                if (window.mitraAppInstance && window.mitraAppInstance.form && window.mitraAppInstance.formMode !== 'view') {
                    window.mitraAppInstance.form.alamat = data.display_name;
                    window.mitraAppInstance.searchAddressQuery = data.display_name;
                }
                if (window.Alpine && typeof window.Alpine.$data === 'function') {
                    const rootElem = document.querySelector('[x-data]');
                    if (rootElem) {
                        const alpineData = window.Alpine.$data(rootElem);
                        if (alpineData && alpineData.form && alpineData.formMode !== 'view') {
                            alpineData.form.alamat = data.display_name;
                            alpineData.searchAddressQuery = data.display_name;
                        }
                    }
                }
            }
        })
        .catch(err => {
            console.log('Reverse geocoding notice:', err);
        });
    }
</script>
@endpush

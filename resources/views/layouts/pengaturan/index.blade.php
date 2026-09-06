@extends('layouts.app')

@section('title', 'Pengaturan Sistem & Profil - SIM-BUDIDAYA')

@section('content')
<div x-data="{ 
    activeTab: 'profil', 
    showToast: false, 
    toastMessage: '' 
}" class="space-y-6">

    <!-- Flash Alert Status -->
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

    @if($errors->any())
    <div class="p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 text-xs font-bold space-y-1 shadow-xs">
        <div class="flex items-center gap-2 text-rose-700 font-extrabold text-sm mb-1">
            <i class="fa-solid fa-triangle-exclamation"></i>
            <span>Terdapat beberapa kesalahan:</span>
        </div>
        <ul class="list-disc list-inside space-y-0.5 font-semibold text-rose-600">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <!-- Page Title Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-extrabold text-[#0B2570] tracking-tight">Pengaturan & Profil</h1>
            <p class="text-xs text-slate-500 font-medium mt-0.5">Kelola informasi profil akun manajer dan status proteksi keamanan akun Anda.</p>
        </div>
    </div>

    <!-- Navigation Tabs Bar -->
    <div class="flex items-center gap-2 border-b border-slate-200/80 pb-3 overflow-x-auto">
        <button type="button" 
                @click="activeTab = 'profil'"
                :class="activeTab === 'profil' ? 'bg-[#051B44] text-white shadow-md shadow-[#051B44]/20' : 'bg-white text-slate-600 hover:bg-slate-100 border border-slate-200/80'"
                class="px-5 py-2.5 rounded-xl font-extrabold text-xs flex items-center gap-2.5 transition-all cursor-pointer shrink-0">
            <i class="fa-solid fa-user-gear text-xs"></i>
            <span>Profil & Akun</span>
        </button>

        <button type="button" 
                @click="activeTab = 'keamanan'"
                :class="activeTab === 'keamanan' ? 'bg-[#051B44] text-white shadow-md shadow-[#051B44]/20' : 'bg-white text-slate-600 hover:bg-slate-100 border border-slate-200/80'"
                class="px-5 py-2.5 rounded-xl font-extrabold text-xs flex items-center gap-2.5 transition-all cursor-pointer shrink-0">
            <i class="fa-solid fa-shield-halved text-xs"></i>
            <span>Keamanan & 2FA</span>
        </button>
    </div>

    <!-- TAB 1: PROFIL & AKUN -->
    <div x-show="activeTab === 'profil'" 
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 translate-y-1"
         x-transition:enter-end="opacity-100 translate-y-0"
         class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <!-- Left: Form Pengaturan Profil -->
        <div class="lg:col-span-2 bg-white rounded-3xl border border-slate-200/90 p-6 sm:p-7 shadow-xs space-y-6">
            <div class="flex items-center gap-3 border-b border-slate-100 pb-4">
                <div class="w-10 h-10 rounded-2xl bg-sky-50 text-sky-600 flex items-center justify-center text-base font-bold">
                    <i class="fa-solid fa-user-pen"></i>
                </div>
                <div>
                    <h3 class="text-base font-extrabold text-[#051B44]">Informasi Pengguna</h3>
                    <p class="text-xs text-slate-500 font-medium">Perbarui informasi profil dan kredensial akun manajer Anda.</p>
                </div>
            </div>

            <form action="{{ route('pengaturan.update-profile') }}" method="POST" enctype="multipart/form-data" class="space-y-6" x-data="{
                photoPreview: '{{ $user->foto_profil_url }}',
                hasCustomPhoto: {{ !empty($user->foto_profil) ? 'true' : 'false' }},
                deletePhoto: false,

                previewFile(e) {
                    const file = e.target.files[0];
                    if (file) {
                        this.deletePhoto = false;
                        const reader = new FileReader();
                        reader.onload = (event) => {
                            this.photoPreview = event.target.result;
                        };
                        reader.readAsDataURL(file);
                    }
                },
                removePhoto() {
                    this.deletePhoto = true;
                    this.photoPreview = 'https://ui-avatars.com/api/?name={{ urlencode($user->nama ?: 'User') }}&background=0B2570&color=ffffff&bold=true';
                    if (this.$refs.photoInput) this.$refs.photoInput.value = '';
                }
            }">
                @csrf
                @method('PUT')

                <!-- Photo Profile Upload Section -->
                <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200/80 flex flex-col sm:flex-row items-center gap-5">
                    <div class="relative shrink-0">
                        <img :src="photoPreview" alt="Foto Profil" class="w-20 h-20 rounded-full object-cover border-2 border-white shadow-md bg-slate-100">
                        <button type="button" @click="$refs.photoInput.click()" 
                                class="absolute bottom-0 right-0 w-7 h-7 rounded-full bg-[#051B44] hover:bg-sky-700 text-white flex items-center justify-center text-xs shadow-md transition-transform active:scale-95 cursor-pointer"
                                title="Pilih Foto Profil">
                            <i class="fa-solid fa-camera text-[11px]"></i>
                        </button>
                    </div>
                    <div class="space-y-1.5 text-center sm:text-left flex-1">
                        <h4 class="text-xs font-bold text-slate-800">Foto Profil</h4>
                        <p class="text-[11px] text-slate-500 font-medium">Unggah file foto profil baru (Format: JPG, PNG, WEBP, Maks. 2MB).</p>
                        <div class="flex flex-wrap items-center justify-center sm:justify-start gap-2 pt-1">
                            <button type="button" @click="$refs.photoInput.click()" class="px-3.5 py-1.5 rounded-lg bg-[#051B44] hover:bg-navy-900 text-white text-[11px] font-bold shadow-xs transition-colors flex items-center gap-1.5 cursor-pointer">
                                <i class="fa-solid fa-cloud-arrow-up text-[11px]"></i>
                                <span>Unggah Foto</span>
                            </button>
                            <template x-if="hasCustomPhoto && !deletePhoto">
                                <button type="button" @click="removePhoto()" class="px-3.5 py-1.5 rounded-lg bg-rose-50 border border-rose-200 hover:bg-rose-100 text-rose-600 text-[11px] font-bold transition-colors flex items-center gap-1.5 cursor-pointer">
                                    <i class="fa-solid fa-trash-can text-[10px]"></i>
                                    <span>Hapus Foto</span>
                                </button>
                            </template>
                        </div>
                    </div>
                    <input type="file" name="foto_profil" x-ref="photoInput" @change="previewFile" accept="image/jpeg,image/png,image/jpg,image/webp" class="hidden">
                    <input type="hidden" name="hapus_foto" :value="deletePhoto ? '1' : '0'">
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1.5">Nama Lengkap</label>
                        <input type="text" name="nama" value="{{ old('nama', $user->nama ?? '') }}" required
                               class="w-full px-4 py-2.5 rounded-xl bg-slate-50 border border-slate-200 text-xs font-bold text-slate-800 focus:bg-white focus:outline-none focus:border-sky-500 focus:ring-2 focus:ring-sky-500/10 transition-all">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1.5">Alamat Email</label>
                        <input type="email" name="email" value="{{ old('email', $user->email ?? '') }}" required
                               class="w-full px-4 py-2.5 rounded-xl bg-slate-50 border border-slate-200 text-xs font-bold text-slate-800 focus:bg-white focus:outline-none focus:border-sky-500 focus:ring-2 focus:ring-sky-500/10 transition-all">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1.5">Nomor WhatsApp / Telp</label>
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
                            fullValue: '{{ old('no_tlp', $user->no_tlp ?? '') }}',
                            init() {
                                this.selectedCountry = this.countries[0];
                                let val = this.fullValue.trim();
                                if (val) {
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
                                }
                            },
                            updatePhone() {
                                let num = (this.phoneNum || '').trim();
                                if (num.startsWith('0')) num = num.substring(1).trim();
                                this.fullValue = num ? `${this.selectedCountry.dial} ${num}` : '';
                            },
                            selectCountry(c) {
                                this.selectedCountry = c;
                                this.countryMenuOpen = false;
                                this.countrySearch = '';
                                this.updatePhone();
                                this.$nextTick(() => { if (this.$refs.phoneInput) this.$refs.phoneInput.focus(); });
                            },
                            get filteredCountries() {
                                if (!this.countrySearch.trim()) return this.countries;
                                let q = this.countrySearch.toLowerCase();
                                return this.countries.filter(c => c.name.toLowerCase().includes(q) || c.dial.includes(q) || c.code.toLowerCase().includes(q));
                            }
                        }" @click.outside="countryMenuOpen = false" class="relative">
                            <div class="flex items-center rounded-xl border border-slate-200 bg-slate-50 focus-within:bg-white focus-within:border-sky-500 focus-within:ring-2 focus-within:ring-sky-500/10 transition-all">
                                <input type="hidden" name="no_tlp" :value="fullValue">
                                
                                <button type="button"
                                        @click="countryMenuOpen = !countryMenuOpen"
                                        class="px-3 py-2.5 text-xs font-bold text-slate-700 bg-slate-100/90 border-r border-slate-200 hover:bg-slate-100 cursor-pointer shrink-0 flex items-center gap-1.5 transition-colors select-none focus:outline-none rounded-l-xl">
                                    <span class="text-base leading-none" x-text="selectedCountry.flag">🇮🇩</span>
                                    <span class="text-xs font-extrabold text-slate-800" x-text="selectedCountry.dial">+62</span>
                                    <i class="fa-solid fa-chevron-down text-[9px] text-slate-400 transition-transform duration-200"
                                       :class="countryMenuOpen ? 'rotate-180 text-sky-600' : ''"></i>
                                </button>
                                <input type="tel" 
                                       x-ref="phoneInput"
                                       x-model="phoneNum"
                                       @input="updatePhone()"
                                       :placeholder="selectedCountry.placeholder"
                                       class="w-full px-3.5 py-2.5 text-xs font-bold text-slate-800 bg-transparent border-0 focus:outline-none rounded-r-xl">
                            </div>

                            <!-- Dropdown -->
                            <div x-show="countryMenuOpen"
                                 x-transition:enter="transition ease-out duration-150"
                                 x-transition:enter-start="opacity-0 translate-y-1 scale-98"
                                 x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                                 x-transition:leave="transition ease-in duration-100"
                                 x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                                 x-transition:leave-end="opacity-0 translate-y-1 scale-98"
                                 class="absolute z-50 top-full left-0 mt-1.5 w-72 sm:w-80 bg-white rounded-xl shadow-2xl border border-slate-200 overflow-hidden"
                                 style="display: none;">
                                
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
                </div>

                <div class="pt-4 border-t border-slate-100">
                    <h4 class="text-xs font-extrabold text-[#051B44] uppercase tracking-wider mb-3">Ubah Password (Opsional)</h4>
                    
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1.5">Password Saat Ini</label>
                            <input type="password" name="password_saat_ini" placeholder="••••••••"
                                   class="w-full px-4 py-2.5 rounded-xl bg-slate-50 border border-slate-200 text-xs font-bold text-slate-800 focus:bg-white focus:outline-none focus:border-sky-500 focus:ring-2 focus:ring-sky-500/10 transition-all">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1.5">Password Baru</label>
                            <input type="password" name="password_baru" placeholder="Minimal 6 karakter"
                                   class="w-full px-4 py-2.5 rounded-xl bg-slate-50 border border-slate-200 text-xs font-bold text-slate-800 focus:bg-white focus:outline-none focus:border-sky-500 focus:ring-2 focus:ring-sky-500/10 transition-all">
                        </div>

                        <div class="sm:col-span-2">
                            <label class="block text-xs font-bold text-slate-700 mb-1.5">Konfirmasi Password Baru</label>
                            <input type="password" name="password_baru_confirmation" placeholder="Ulangi password baru"
                                   class="w-full px-4 py-2.5 rounded-xl bg-slate-50 border border-slate-200 text-xs font-bold text-slate-800 focus:bg-white focus:outline-none focus:border-sky-500 focus:ring-2 focus:ring-sky-500/10 transition-all">
                        </div>
                    </div>
                </div>

                <div class="flex items-center justify-end pt-2">
                    <button type="submit" 
                            class="px-6 py-2.5 rounded-xl bg-[#051B44] hover:bg-[#09265c] text-white font-bold text-xs flex items-center gap-2 shadow-md shadow-[#051B44]/20 transition-all cursor-pointer">
                        <i class="fa-solid fa-floppy-disk"></i>
                        <span>Simpan Perubahan Profil</span>
                    </button>
                </div>
            </form>
        </div>

        <!-- Right: Status Akun Card -->
        <div class="space-y-4">
            <div class="bg-white rounded-3xl border border-slate-200/90 p-6 shadow-xs space-y-4">
                <div class="flex flex-col items-center text-center pb-3 border-b border-slate-100">
                    <img src="{{ $user->foto_profil_url }}" alt="{{ $user->nama }}" class="w-16 h-16 rounded-full object-cover border-2 border-sky-100 shadow-sm">
                    <h4 class="mt-2.5 font-extrabold text-[#051B44] text-sm">{{ $user->nama }}</h4>
                    <span class="text-[11px] text-slate-400 font-semibold">{{ $user->email }}</span>
                </div>

                <div class="space-y-3 text-xs">
                    <div class="flex items-center justify-between p-3 rounded-2xl bg-slate-50 border border-slate-100">
                        <span class="font-bold text-slate-500">Role Akses</span>
                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold uppercase bg-sky-100 text-sky-800 border border-sky-200">
                            {{ $user->role ?? 'Manajer' }}
                        </span>
                    </div>

                    <div class="flex items-center justify-between p-3 rounded-2xl bg-slate-50 border border-slate-100">
                        <span class="font-bold text-slate-500">Proteksi Login</span>
                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold uppercase bg-emerald-100 text-emerald-800 border border-emerald-200">
                            Email OTP (Aktif)
                        </span>
                    </div>

                    <div class="flex items-center justify-between p-3 rounded-2xl bg-slate-50 border border-slate-100">
                        <span class="font-bold text-slate-500">Terakhir Login</span>
                        <span class="font-extrabold text-slate-700">Hari ini</span>
                    </div>
                </div>
            </div>

            <!-- Quick Tip Card -->
            <div class="bg-gradient-to-br from-sky-50 to-indigo-50/60 rounded-3xl border border-sky-100 p-5 space-y-2 text-xs">
                <div class="flex items-center gap-2 text-sky-800 font-extrabold">
                    <i class="fa-solid fa-lightbulb text-amber-500"></i>
                    <span>Tips Keamanan</span>
                </div>
                <p class="text-slate-600 font-medium leading-relaxed">
                    Setiap proses login akun Manajer wajib diverifikasi menggunakan <strong>Kode OTP 6-digit</strong> yang dikirimkan secara otomatis ke alamat email resmi Anda.
                </p>
            </div>
        </div>
    </div>

    <!-- TAB 2: KEAMANAN & EMAIL OTP -->
    <div x-show="activeTab === 'keamanan'" 
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 translate-y-1"
         x-transition:enter-end="opacity-100 translate-y-0"
         class="bg-white rounded-3xl border border-slate-200/90 p-6 sm:p-8 shadow-xs space-y-6">

        <div class="flex items-center gap-3 border-b border-slate-100 pb-4">
            <div class="w-10 h-10 rounded-2xl bg-indigo-50 text-indigo-600 flex items-center justify-center text-base font-bold">
                <i class="fa-solid fa-shield-halved"></i>
            </div>
            <div>
                <h3 class="text-base font-extrabold text-[#051B44]">Proteksi Keamanan Email OTP</h3>
                <p class="text-xs text-slate-500 font-medium">Akun Manajer dilindungi secara otomatis dengan verifikasi kode OTP setiap kali proses login.</p>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 items-center">
            <div class="md:col-span-2 space-y-3">
                <div class="flex items-center gap-3">
                    <span class="text-xs font-bold text-slate-700">Status Proteksi:</span>
                    <span class="px-3 py-1 rounded-full text-xs font-extrabold bg-emerald-100 text-emerald-800 border border-emerald-200 flex items-center gap-1.5">
                        <i class="fa-solid fa-circle-check text-emerald-600"></i>
                        <span>AKTIF (Email OTP)</span>
                    </span>
                </div>

                <p class="text-xs text-slate-600 leading-relaxed font-medium">
                    Kode verifikasi 6 digit akan otomatis dikirimkan ke email <strong>{{ $user->email ?? '-' }}</strong> saat login. Pastikan email ini selalu aktif dan hanya Anda yang memiliki akses.
                </p>
            </div>

            <div class="flex flex-col items-start md:items-end justify-center gap-3">
                <div class="p-3 bg-slate-50 rounded-2xl border border-slate-200 text-center w-full md:w-auto">
                    <span class="text-[11px] text-slate-400 font-semibold block">Email Terdaftar</span>
                    <span class="text-xs font-mono font-bold text-[#051B44]">{{ $user->email ?? '-' }}</span>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection

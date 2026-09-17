@extends('layouts.app')

@section('title', 'Manajemen Petugas & Akses Akun - AMS BUDIDAYA')

@section('content')
<div class="space-y-6" x-data="petugasComponent()">

    <!-- ========================================================================= -->
    <!-- TAB 1: TABEL UTAMA MANAJEMEN PETUGAS                                      -->
    <!-- ========================================================================= -->
    <div x-show="activeTab === 'daftar'"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 translate-y-1"
         x-transition:enter-end="opacity-100 translate-y-0"
         class="space-y-6">

        <!-- Title & Action Button -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-extrabold text-[#0B2570] tracking-tight">Manajemen Petugas &amp; Akses Akun</h1>
                <p class="text-xs text-slate-500 font-medium mt-0.5">Kelola akun staf operasional dan perbarui kata sandi petugas.</p>
            </div>
            <div>
                <button @click="activeTab = 'create'"
                        class="px-4 py-2.5 rounded-xl bg-[#051B44] hover:bg-navy-900 text-white font-extrabold text-xs shadow-md shadow-sky-950/20 transition-all flex items-center gap-2">
                    <i class="fa-solid fa-plus text-xs"></i>
                    <span>Tambah Petugas Baru</span>
                </button>
            </div>
        </div>

        <!-- Table Card Container -->
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50/70 border-b border-slate-100 text-[10px] font-extrabold text-slate-400 uppercase tracking-wider">
                            <th class="py-3.5 px-6">FOTO &amp; NAMA PETUGAS</th>
                            <th class="py-3.5 px-6">PERAN &amp; DIVISI</th>
                            <th class="py-3.5 px-6">EMAIL / TELP</th>
                            <th class="py-3.5 px-6">TGL BERGABUNG</th>
                            <th class="py-3.5 px-6 text-center">AKSI</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-xs font-medium text-slate-700">
                        @if(isset($users) && count($users) > 0)
                            @php
                                $roleBadges = [
                                    'manajer'            => ['bg' => 'bg-indigo-100 text-indigo-700', 'label' => 'Manajer Operasional'],
                                    'pembibitan'         => ['bg' => 'bg-purple-100 text-purple-700', 'label' => 'Teknisi Pembibitan'],
                                    'pembesaran'         => ['bg' => 'bg-cyan-100 text-cyan-800', 'label' => 'Teknisi Pembesaran'],
                                    'petugas_distribusi' => ['bg' => 'bg-sky-100 text-sky-700', 'label' => 'Logistik & Distribusi'],
                                ];
                            @endphp
                            @foreach($users as $idx => $u)
                                @php
                                    $roleInfo = $roleBadges[$u->role] ?? ['bg' => 'bg-slate-100 text-slate-700', 'label' => ucfirst(str_replace('_', ' ', $u->role))];
                                @endphp
                                <tr id="user-row-{{ $u->id_user }}" class="hover:bg-slate-50/60 transition-colors">
                                    <td class="py-4 px-6">
                                        <div class="flex items-center gap-3">
                                            <img src="{{ $u->foto_profil_url }}" alt="{{ $u->nama }}" class="w-10 h-10 rounded-full object-cover border border-slate-200 shrink-0">
                                            <div>
                                                <h4 class="font-extrabold text-slate-900">{{ $u->nama }}</h4>
                                                <span class="text-[10px] text-slate-400 font-mono">ID: USR-{{ str_pad($u->id_user, 4, '0', STR_PAD_LEFT) }}</span>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="py-4 px-6">
                                        <span class="px-2.5 py-1 rounded-lg text-[10px] font-extrabold {{ $roleInfo['bg'] }}">
                                            {{ $roleInfo['label'] }}
                                        </span>
                                    </td>
                                    <td class="py-4 px-6">
                                        <div class="space-y-0.5">
                                            <div class="text-slate-900 font-semibold flex items-center gap-1.5">
                                                <i class="fa-regular fa-envelope text-slate-400 text-[10px]"></i>
                                                <span>{{ $u->email }}</span>
                                            </div>
                                            <div class="text-[11px] text-slate-400 flex items-center gap-1.5">
                                                <i class="fa-solid fa-phone text-slate-400 text-[9px]"></i>
                                                <span>{{ $u->no_tlp ?? '-' }}</span>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="py-4 px-6 text-slate-600 font-semibold">
                                        {{ $u->created_at ? $u->created_at->translatedFormat('d M Y') : '01 Jan 2026' }}
                                    </td>
                                    <td class="py-4 px-6 text-center">
                                        <div class="relative inline-block text-left" 
                                             x-data="{ 
                                                 open: false,
                                                 menuStyle: '',
                                                 toggle(e) {
                                                     this.open = !this.open;
                                                     if (this.open) {
                                                         const rect = this.$refs.btn.getBoundingClientRect();
                                                         const spaceBelow = window.innerHeight - rect.bottom;
                                                         const menuH = 145;
                                                         const openUp = spaceBelow < menuH && rect.top > menuH;
                                                         const topPos = openUp ? (rect.top - menuH - 4) : (rect.bottom + 4);
                                                         const rightPos = window.innerWidth - rect.right;
                                                         this.menuStyle = `position: fixed; z-index: 99999; top: ${topPos}px; right: ${rightPos}px;`;
                                                     }
                                                 }
                                             }">
                                            <button x-ref="btn" 
                                                    @click="toggle($event)" 
                                                    @click.away="open = false"
                                                    class="w-8 h-8 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-600 flex items-center justify-center transition-colors cursor-pointer">
                                                <i class="fa-solid fa-ellipsis-vertical text-sm"></i>
                                            </button>
                                            <div x-show="open"
                                                 @scroll.window="open = false"
                                                 x-transition:enter="transition ease-out duration-100"
                                                 x-transition:enter-start="transform opacity-0 scale-95"
                                                 x-transition:enter-end="transform opacity-100 scale-100"
                                                 x-transition:leave="transition ease-in duration-75"
                                                 x-transition:leave-start="transform opacity-100 scale-100"
                                                 x-transition:leave-end="transform opacity-0 scale-95"
                                                 :style="menuStyle"
                                                 class="w-52 rounded-xl bg-white border border-slate-200 shadow-2xl py-1.5 z-50 text-left"
                                                 style="display: none;">
                                                
                                                <button @click="open = false; openEdit({{ json_encode($u) }})" class="w-full px-3.5 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-50 flex items-center gap-2.5">
                                                    <i class="fa-solid fa-pen-to-square text-sky-600 w-4"></i>
                                                    <span>Edit Data Profil</span>
                                                </button>

                                                <button @click="open = false; openQrModal({{ json_encode($u) }})" class="w-full px-3.5 py-2 text-xs font-semibold text-sky-700 hover:bg-sky-50/70 flex items-center gap-2.5">
                                                    <i class="fa-solid fa-qrcode text-sky-600 w-4"></i>
                                                    <span>Lihat QR Code 2FA</span>
                                                </button>

                                                <button @click="open = false; openSecurity({{ json_encode($u) }})" class="w-full px-3.5 py-2 text-xs font-semibold text-indigo-700 hover:bg-indigo-50/70 flex items-center gap-2.5">
                                                    <i class="fa-solid fa-key text-indigo-600 w-4"></i>
                                                    <span>Ganti Password</span>
                                                </button>

                                                <div class="my-1 border-t border-slate-100"></div>

                                                <button @click="open = false; confirmDelete({ id_user: {{ $u->id_user }}, nama: '{{ addslashes($u->nama) }}' })" class="w-full px-3.5 py-2 text-xs font-semibold text-red-600 hover:bg-red-50 flex items-center gap-2.5">
                                                    <i class="fa-solid fa-trash-can text-red-500 w-4"></i>
                                                    <span>Hapus Petugas</span>
                                                </button>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="5" class="py-8 text-center text-slate-400">
                                    Belum ada data petugas terdaftar.
                                </td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>

    </div>

    <!-- ========================================================================= -->
    <!-- TAB 2: FORM REGISTRASI PETUGAS BARU                                      -->
    <!-- ========================================================================= -->
    <div x-show="activeTab === 'create'"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 translate-y-1"
         x-transition:enter-end="opacity-100 translate-y-0"
         class="space-y-6 max-w-6xl mx-auto">

        <div class="flex items-center gap-3">
            <button @click="activeTab = 'daftar'" class="w-9 h-9 rounded-xl bg-white border border-slate-200 text-slate-600 hover:bg-slate-50 flex items-center justify-center transition-colors shadow-xs">
                <i class="fa-solid fa-arrow-left text-sm"></i>
            </button>
            <div>
                <h1 class="text-2xl font-extrabold text-[#0B2570] tracking-tight">Form Registrasi Petugas</h1>
                <p class="text-xs text-slate-500 font-medium mt-0.5">Daftarkan akun staf teknisi atau logistik baru ke sistem.</p>
            </div>
        </div>

        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs p-6 sm:p-8 space-y-6">
            <form action="{{ route('petugas.store') }}" method="POST" class="space-y-6">
                @csrf
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <div>
                        <label class="text-[10px] font-extrabold uppercase tracking-wider text-slate-500 block mb-1.5">Nama Lengkap Petugas *</label>
                        <input type="text" name="nama" x-model="formCreate.nama" required placeholder="Contoh: Budi Santoso"
                               class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs font-semibold text-slate-800 bg-slate-50/70 focus:bg-white focus:outline-none focus:ring-2 focus:ring-sky-500 transition-all">
                    </div>

                    <div>
                        <label class="text-[10px] font-extrabold uppercase tracking-wider text-slate-500 block mb-1.5">Email Login Akun *</label>
                        <input type="email" name="email" x-model="formCreate.email" required placeholder="budi@simbudidaya.id"
                               class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs font-semibold text-slate-800 bg-slate-50/70 focus:bg-white focus:outline-none focus:ring-2 focus:ring-sky-500 transition-all">
                    </div>

                    <div>
                        <label class="text-[10px] font-extrabold uppercase tracking-wider text-slate-500 block mb-1.5">Peran / Divisi *</label>
                        <select name="role" x-model="formCreate.role" required
                                class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs font-bold text-slate-700 bg-slate-50/70 focus:bg-white focus:outline-none focus:ring-2 focus:ring-sky-500 transition-all">
                            <option value="pembesaran">Teknisi Pembesaran Ikan</option>
                            <option value="pembibitan">Teknisi Hatchery / Pembibitan</option>
                            <option value="petugas_distribusi">Logistik &amp; Distribusi Pengiriman</option>
                            <option value="manajer">Manajer Operasional</option>
                        </select>
                    </div>

                    <div>
                        <label class="text-[10px] font-extrabold uppercase tracking-wider text-slate-500 block mb-1.5">Nomor WhatsApp / Telp</label>
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
                                this.$watch('formCreate.no_tlp', () => this.syncFromModel());
                            },
                            syncFromModel() {
                                let val = (formCreate.no_tlp || '').trim();
                                if (!val) { this.phoneNum = ''; return; }
                                for (let c of this.countries) {
                                    if (val.startsWith(c.dial)) {
                                        this.selectedCountry = c;
                                        this.phoneNum = val.slice(c.dial.length).replace(/[^0-9]/g, '');
                                        return;
                                    }
                                }
                                let cleanDigits = val.replace(/[^0-9]/g, '');
                                if (cleanDigits.startsWith('0')) {
                                    this.selectedCountry = this.countries[0];
                                    this.phoneNum = cleanDigits.slice(1);
                                } else {
                                    this.phoneNum = cleanDigits;
                                }
                            },
                            updatePhone() {
                                let num = (this.phoneNum || '').replace(/[^0-9]/g, '');
                                if (num.startsWith('0')) num = num.substring(1);
                                this.phoneNum = num;
                                formCreate.no_tlp = num ? `${this.selectedCountry.dial} ${num}` : '';
                            },
                            selectCountry(c) {
                                this.selectedCountry = c;
                                this.countryMenuOpen = false;
                                this.countrySearch = '';
                                this.updatePhone();
                                this.$nextTick(() => { if (this.$refs.createPhoneRef) this.$refs.createPhoneRef.focus(); });
                            },
                            get filteredCountries() {
                                if (!this.countrySearch.trim()) return this.countries;
                                let q = this.countrySearch.toLowerCase();
                                return this.countries.filter(c => c.name.toLowerCase().includes(q) || c.dial.includes(q) || c.code.toLowerCase().includes(q));
                            }
                        }" @click.outside="countryMenuOpen = false" class="relative">
                            <div class="flex items-center rounded-xl border border-slate-200 bg-slate-50/70 focus-within:bg-white focus-within:ring-2 focus-within:ring-sky-500 focus-within:border-sky-500 transition-all">
                                <input type="hidden" name="no_tlp" :value="formCreate.no_tlp">
                                
                                <button type="button"
                                        @click="countryMenuOpen = !countryMenuOpen"
                                        class="px-3 py-2.5 text-xs font-bold text-slate-700 bg-slate-100/90 border-r border-slate-200 hover:bg-slate-100 cursor-pointer shrink-0 flex items-center gap-1.5 transition-colors select-none focus:outline-none rounded-l-xl">
                                    <span class="text-base leading-none" x-text="selectedCountry.flag">🇮🇩</span>
                                    <span class="text-xs font-extrabold text-slate-800" x-text="selectedCountry.dial">+62</span>
                                    <i class="fa-solid fa-chevron-down text-[9px] text-slate-400 transition-transform duration-200"
                                       :class="countryMenuOpen ? 'rotate-180 text-sky-600' : ''"></i>
                                </button>
                                <input type="tel" 
                                       x-ref="createPhoneRef"
                                       x-model="phoneNum"
                                       @input="updatePhone()"
                                       @keydown="if (!/[0-9]/.test($event.key) && !['Backspace', 'Delete', 'ArrowLeft', 'ArrowRight', 'Tab', 'Enter'].includes($event.key) && !$event.ctrlKey && !$event.metaKey) $event.preventDefault()"
                                       inputmode="numeric"
                                       pattern="[0-9]*"
                                       :placeholder="selectedCountry.placeholder"
                                       class="w-full px-3.5 py-2.5 text-xs font-semibold text-slate-800 bg-transparent border-0 focus:outline-none rounded-r-xl">
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

                    <div class="sm:col-span-2">
                        <label class="text-[10px] font-extrabold uppercase tracking-wider text-slate-500 block mb-1.5">Password Awal *</label>
                        <input type="password" name="password" x-model="formCreate.password" required minlength="6" placeholder="Minimal 6 karakter"
                               class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs font-semibold text-slate-800 bg-slate-50/70 focus:bg-white focus:outline-none focus:ring-2 focus:ring-sky-500 transition-all">
                    </div>
                </div>

                <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
                    <button type="button" @click="activeTab = 'daftar'" class="px-5 py-2.5 rounded-xl border border-slate-200 text-xs font-bold text-slate-600 hover:bg-slate-50 transition-colors">
                        Batal
                    </button>
                    <button type="submit" class="px-6 py-2.5 rounded-xl bg-[#051B44] hover:bg-navy-900 text-white font-extrabold text-xs shadow-md shadow-sky-950/20 transition-all flex items-center gap-2">
                        <i class="fa-solid fa-floppy-disk text-xs"></i>
                        <span>Daftarkan Petugas</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- ========================================================================= -->
    <!-- TAB 3: FORM EDIT DATA PETUGAS                                            -->
    <!-- ========================================================================= -->
    <div x-show="activeTab === 'edit'"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 translate-y-1"
         x-transition:enter-end="opacity-100 translate-y-0"
         class="space-y-6 max-w-6xl mx-auto">

        <div class="flex items-center gap-3">
            <button @click="activeTab = 'daftar'" class="w-9 h-9 rounded-xl bg-white border border-slate-200 text-slate-600 hover:bg-slate-50 flex items-center justify-center transition-colors shadow-xs">
                <i class="fa-solid fa-arrow-left text-sm"></i>
            </button>
            <div>
                <h1 class="text-2xl font-extrabold text-[#0B2570] tracking-tight">Edit Data Petugas</h1>
                <p class="text-xs text-slate-500 font-medium mt-0.5">Perbarui informasi profil dan nomor kontak petugas.</p>
            </div>
        </div>

        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs p-6 sm:p-8 space-y-6">
            <form :action="'{{ url('/petugas') }}/' + formEdit.id_user" method="POST" class="space-y-6">
                @csrf
                @method('PUT')
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <div>
                        <label class="text-[10px] font-extrabold uppercase tracking-wider text-slate-500 block mb-1.5">Nama Lengkap Petugas *</label>
                        <input type="text" name="nama" x-model="formEdit.nama" required
                               class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs font-semibold text-slate-800 bg-slate-50/70 focus:bg-white focus:outline-none focus:ring-2 focus:ring-sky-500 transition-all">
                    </div>

                    <div>
                        <label class="text-[10px] font-extrabold uppercase tracking-wider text-slate-500 block mb-1.5">Email Login Akun *</label>
                        <input type="email" name="email" x-model="formEdit.email" required
                               class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs font-semibold text-slate-800 bg-slate-50/70 focus:bg-white focus:outline-none focus:ring-2 focus:ring-sky-500 transition-all">
                    </div>

                    <div>
                        <label class="text-[10px] font-extrabold uppercase tracking-wider text-slate-500 block mb-1.5">Peran / Divisi *</label>
                        <select name="role" x-model="formEdit.role" required
                                class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs font-bold text-slate-700 bg-slate-50/70 focus:bg-white focus:outline-none focus:ring-2 focus:ring-sky-500 transition-all">
                            <option value="pembesaran">Teknisi Pembesaran Ikan</option>
                            <option value="pembibitan">Teknisi Hatchery / Pembibitan</option>
                            <option value="petugas_distribusi">Logistik &amp; Distribusi Pengiriman</option>
                            <option value="manajer">Manajer Operasional</option>
                        </select>
                    </div>

                    <div>
                        <label class="text-[10px] font-extrabold uppercase tracking-wider text-slate-500 block mb-1.5">Nomor WhatsApp / Telp</label>
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
                                this.$watch('formEdit.no_tlp', () => this.syncFromModel());
                            },
                            syncFromModel() {
                                let val = (formEdit.no_tlp || '').trim();
                                if (!val) { this.phoneNum = ''; return; }
                                for (let c of this.countries) {
                                    if (val.startsWith(c.dial)) {
                                        this.selectedCountry = c;
                                        this.phoneNum = val.slice(c.dial.length).replace(/[^0-9]/g, '');
                                        return;
                                    }
                                }
                                let cleanDigits = val.replace(/[^0-9]/g, '');
                                if (cleanDigits.startsWith('0')) {
                                    this.selectedCountry = this.countries[0];
                                    this.phoneNum = cleanDigits.slice(1);
                                } else {
                                    this.phoneNum = cleanDigits;
                                }
                            },
                            updatePhone() {
                                let num = (this.phoneNum || '').replace(/[^0-9]/g, '');
                                if (num.startsWith('0')) num = num.substring(1);
                                this.phoneNum = num;
                                formEdit.no_tlp = num ? `${this.selectedCountry.dial} ${num}` : '';
                            },
                            selectCountry(c) {
                                this.selectedCountry = c;
                                this.countryMenuOpen = false;
                                this.countrySearch = '';
                                this.updatePhone();
                                this.$nextTick(() => { if (this.$refs.editPhoneRef) this.$refs.editPhoneRef.focus(); });
                            },
                            get filteredCountries() {
                                if (!this.countrySearch.trim()) return this.countries;
                                let q = this.countrySearch.toLowerCase();
                                return this.countries.filter(c => c.name.toLowerCase().includes(q) || c.dial.includes(q) || c.code.toLowerCase().includes(q));
                            }
                        }" @click.outside="countryMenuOpen = false" class="relative">
                            <div class="flex items-center rounded-xl border border-slate-200 bg-slate-50/70 focus-within:bg-white focus-within:ring-2 focus-within:ring-sky-500 focus-within:border-sky-500 transition-all">
                                <input type="hidden" name="no_tlp" :value="formEdit.no_tlp">
                                
                                <button type="button"
                                        @click="countryMenuOpen = !countryMenuOpen"
                                        class="px-3 py-2.5 text-xs font-bold text-slate-700 bg-slate-100/90 border-r border-slate-200 hover:bg-slate-100 cursor-pointer shrink-0 flex items-center gap-1.5 transition-colors select-none focus:outline-none rounded-l-xl">
                                    <span class="text-base leading-none" x-text="selectedCountry.flag">🇮🇩</span>
                                    <span class="text-xs font-extrabold text-slate-800" x-text="selectedCountry.dial">+62</span>
                                    <i class="fa-solid fa-chevron-down text-[9px] text-slate-400 transition-transform duration-200"
                                       :class="countryMenuOpen ? 'rotate-180 text-sky-600' : ''"></i>
                                </button>
                                <input type="tel" 
                                       x-ref="editPhoneRef"
                                       x-model="phoneNum"
                                       @input="updatePhone()"
                                       @keydown="if (!/[0-9]/.test($event.key) && !['Backspace', 'Delete', 'ArrowLeft', 'ArrowRight', 'Tab', 'Enter'].includes($event.key) && !$event.ctrlKey && !$event.metaKey) $event.preventDefault()"
                                       inputmode="numeric"
                                       pattern="[0-9]*"
                                       :placeholder="selectedCountry.placeholder"
                                       class="w-full px-3.5 py-2.5 text-xs font-semibold text-slate-800 bg-transparent border-0 focus:outline-none rounded-r-xl">
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

                <!-- Card Google Authenticator 2FA Petugas -->
                <div class="p-5 sm:p-6 rounded-2xl bg-gradient-to-br from-slate-50 to-sky-50/50 border border-sky-100/80 shadow-xs space-y-4">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-3 border-b border-sky-100">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-[#051B44] text-sky-400 flex items-center justify-center text-lg shadow-sm shrink-0">
                                <i class="fa-solid fa-shield-halved"></i>
                            </div>
                            <div>
                                <h4 class="text-sm font-extrabold text-slate-900">Google Authenticator (2FA Petugas)</h4>
                                <p class="text-[11px] text-slate-500 font-medium">Scan QR Code ini dengan aplikasi Google Authenticator di HP petugas untuk menghubungkan akun.</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-2 shrink-0">
                            <button type="button" @click="regenerate2fa(formEdit.id_user)" :disabled="isRegenerating2fa"
                                    class="px-3 py-1.5 rounded-xl border border-sky-200 bg-white hover:bg-sky-50 text-sky-800 text-[11px] font-bold shadow-2xs transition-all flex items-center gap-1.5 cursor-pointer disabled:opacity-50">
                                <i class="fa-solid text-[10px]" :class="isRegenerating2fa ? 'fa-spinner fa-spin' : 'fa-arrows-rotate'"></i>
                                <span x-text="isRegenerating2fa ? 'Membuat...' : 'Buat Ulang Kunci'"></span>
                            </button>
                        </div>
                    </div>

                    <div class="flex flex-col md:flex-row items-center gap-6 pt-1">
                        <!-- QR Code Container -->
                        <div class="bg-white p-3 rounded-2xl border border-slate-200/90 shadow-xs flex flex-col items-center justify-center shrink-0 w-44 h-44 overflow-hidden [&>svg]:w-full [&>svg]:h-full [&>img]:w-full [&>img]:h-full">
                            <template x-if="formEdit.qr_code_svg">
                                <div class="w-full h-full flex items-center justify-center" x-html="formEdit.qr_code_svg"></div>
                            </template>
                            <template x-if="!formEdit.qr_code_svg">
                                <div class="text-center text-slate-400 text-xs py-8">
                                    <i class="fa-solid fa-qrcode text-3xl block mb-1 text-slate-300"></i>
                                    <span>Memuat QR...</span>
                                </div>
                            </template>
                        </div>

                        <!-- Info & Secret Key -->
                        <div class="flex-1 space-y-3 text-left w-full">
                            <div class="space-y-1">
                                <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400 block">KODE KUNCI RAHASIA MANUAL (SECRET KEY)</span>
                                <div class="flex items-center gap-2 bg-white border border-slate-200 rounded-xl p-2 max-w-md shadow-2xs">
                                    <code class="text-xs font-mono font-extrabold text-[#051B44] tracking-widest px-2 truncate flex-1 select-all" x-text="formEdit.secret_key || '----------------'"></code>
                                    <button type="button" @click="copyToClipboard(formEdit.secret_key)"
                                            class="px-3 py-1.5 rounded-lg bg-slate-100 hover:bg-[#051B44] hover:text-white text-slate-700 font-bold text-xs transition-all flex items-center gap-1.5 cursor-pointer shrink-0">
                                        <i class="fa-regular" :class="copiedKey ? 'fa-circle-check text-emerald-600' : 'fa-copy'"></i>
                                        <span x-text="copiedKey ? 'Tersalin' : 'Salin'"></span>
                                    </button>
                                </div>
                            </div>

                            <div class="p-3 bg-white/80 rounded-xl border border-sky-100 text-xs text-slate-600 space-y-1">
                                <div class="flex items-center gap-1.5 font-bold text-sky-900">
                                    <i class="fa-solid fa-circle-info text-sky-600"></i>
                                    <span>Cara Kerja Login Petugas:</span>
                                </div>
                                <p class="text-[11px] text-slate-500 leading-relaxed">
                                    Setelah QR Code dipindai ke Google Authenticator, saat login petugas <strong>hanya perlu memasukkan 6-digit kode OTP</strong> dari aplikasi tanpa perlu scan ulang QR code.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
                    <button type="button" @click="activeTab = 'daftar'" class="px-5 py-2.5 rounded-xl border border-slate-200 text-xs font-bold text-slate-600 hover:bg-slate-50 transition-colors">
                        Batal
                    </button>
                    <button type="submit" class="px-6 py-2.5 rounded-xl bg-[#051B44] hover:bg-navy-900 text-white font-extrabold text-xs shadow-md shadow-sky-950/20 transition-all flex items-center gap-2">
                        <i class="fa-solid fa-floppy-disk text-xs"></i>
                        <span>Simpan Perubahan</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- ========================================================================= -->
    <!-- MODAL GANTI PASSWORD PETUGAS                                              -->
    <!-- ========================================================================= -->
    <div x-show="securityModalOpen" 
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 backdrop-blur-xs p-4"
         style="display: none;">
        
        <div @click.outside="securityModalOpen = false" 
             x-show="securityModalOpen"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 scale-95 translate-y-3"
             x-transition:enter-end="opacity-100 scale-100 translate-y-0"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100 scale-100 translate-y-0"
             x-transition:leave-end="opacity-0 scale-95 translate-y-3"
             class="bg-white w-full max-w-md rounded-3xl shadow-2xl border border-slate-100 p-6 sm:p-7 space-y-6 text-left">
            
            <!-- Header Modal -->
            <div class="flex items-center justify-between pb-4 border-b border-slate-100">
                <div class="flex items-center gap-3">
                    <div class="w-11 h-11 rounded-2xl bg-indigo-50 text-indigo-600 border border-indigo-100 flex items-center justify-center text-lg shadow-xs">
                        <i class="fa-solid fa-key"></i>
                    </div>
                    <div>
                        <h3 class="text-base font-extrabold text-slate-900">Ganti Password Petugas</h3>
                        <p class="text-xs text-slate-500 font-medium" x-text="securityUser.nama + ' (' + securityUser.email + ')'"></p>
                    </div>
                </div>
                <button type="button" @click="securityModalOpen = false" class="w-8 h-8 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-500 flex items-center justify-center transition-colors">
                    <i class="fa-solid fa-xmark text-xs"></i>
                </button>
            </div>

            <!-- Form: Ganti / Reset Password Petugas -->
            <form action="#" method="POST" @submit.prevent="submitPassword()" class="space-y-4">
                <div>
                    <label class="text-[10px] font-extrabold uppercase tracking-wider text-slate-500 block mb-1">Password Baru <span class="text-rose-500">*</span></label>
                    <input type="password" x-model="formPassword.password" required minlength="6" placeholder="Masukkan minimal 6 karakter..."
                           class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs font-semibold text-slate-800 bg-slate-50/70 focus:bg-white focus:outline-none focus:ring-2 focus:ring-sky-500 transition-all">
                </div>

                <div>
                    <label class="text-[10px] font-extrabold uppercase tracking-wider text-slate-500 block mb-1">Konfirmasi Password Baru <span class="text-rose-500">*</span></label>
                    <input type="password" x-model="formPassword.password_confirmation" required minlength="6" placeholder="Ulangi password baru..."
                           class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs font-semibold text-slate-800 bg-slate-50/70 focus:bg-white focus:outline-none focus:ring-2 focus:ring-sky-500 transition-all">
                </div>

                <div class="flex items-center justify-end gap-3 pt-3 border-t border-slate-100">
                    <button type="button" @click="securityModalOpen = false" class="px-4 py-2 rounded-xl border border-slate-200 text-xs font-bold text-slate-600 hover:bg-slate-50 transition-colors">
                        Batal
                    </button>
                    <button type="submit" :disabled="isUpdatingPassword"
                            class="px-5 py-2 rounded-xl bg-[#051B44] hover:bg-navy-900 text-white font-extrabold text-xs shadow-md shadow-sky-950/20 transition-all flex items-center gap-2 disabled:opacity-50">
                        <i class="fa-solid text-xs" :class="isUpdatingPassword ? 'fa-spinner fa-spin' : 'fa-lock'"></i>
                        <span x-text="isUpdatingPassword ? 'Menyimpan...' : 'Simpan Password Baru'"></span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- ========================================================================= -->
    <!-- CUSTOM CONFIRMATION MODAL (HAPUS PETUGAS)                                 -->
    <!-- ========================================================================= -->
    <div x-show="deleteModalOpen" 
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 backdrop-blur-xs p-4"
         style="display: none;">
        
        <div @click.outside="deleteModalOpen = false" 
             x-show="deleteModalOpen"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 scale-95 translate-y-3"
             x-transition:enter-end="opacity-100 scale-100 translate-y-0"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100 scale-100 translate-y-0"
             x-transition:leave-end="opacity-0 scale-95 translate-y-3"
             class="bg-white w-full max-w-md rounded-3xl shadow-2xl border border-slate-100 p-6 space-y-5 text-center">
            
            <div class="w-16 h-16 rounded-2xl bg-rose-50 text-rose-600 border border-rose-100 mx-auto flex items-center justify-center text-2xl shadow-xs">
                <i class="fa-solid fa-triangle-exclamation"></i>
            </div>

            <div class="space-y-1.5">
                <h3 class="text-lg font-extrabold text-slate-900">Hapus Data Petugas?</h3>
                <p class="text-xs text-slate-500 font-medium leading-relaxed">
                    Apakah Anda yakin ingin menghapus akun petugas <strong class="text-slate-800" x-text="selectedUser?.nama"></strong>? Seluruh catatan akun dan penugasan terkait akan dihapus dari sistem.
                </p>
            </div>

            <div class="grid grid-cols-2 gap-3 pt-2">
                <button type="button" @click="deleteModalOpen = false" :disabled="isDeleting"
                        class="w-full py-2.5 rounded-xl border border-slate-200 text-slate-700 hover:bg-slate-50 font-bold text-xs transition-colors disabled:opacity-50">
                    Batalkan
                </button>
                <button type="button" @click="executeDelete()" :disabled="isDeleting"
                        class="w-full py-2.5 rounded-xl bg-rose-600 hover:bg-rose-700 active:scale-[0.99] text-white font-bold text-xs shadow-md shadow-rose-950/20 transition-all flex items-center justify-center gap-2 disabled:opacity-50">
                    <i class="fa-solid text-xs" :class="isDeleting ? 'fa-spinner fa-spin' : 'fa-trash-can'"></i>
                    <span x-text="isDeleting ? 'Menghapus...' : 'Ya, Hapus'"></span>
                </button>
            </div>
        </div>
    </div>

    <!-- ========================================================================= -->
    <!-- MODAL QUICK VIEW QR CODE 2FA PETUGAS                                      -->
    <!-- ========================================================================= -->
    <div x-show="qrModalOpen" 
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 backdrop-blur-xs p-4"
         style="display: none;">
        
        <div @click.outside="qrModalOpen = false" 
             x-show="qrModalOpen"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 scale-95 translate-y-3"
             x-transition:enter-end="opacity-100 scale-100 translate-y-0"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100 scale-100 translate-y-0"
             x-transition:leave-end="opacity-0 scale-95 translate-y-3"
             class="bg-white w-full max-w-md rounded-3xl shadow-2xl border border-slate-100 p-6 sm:p-7 space-y-5 text-center">
            
            <!-- Header Modal -->
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <div class="flex items-center gap-3 text-left">
                    <div class="w-10 h-10 rounded-2xl bg-[#051B44] text-sky-400 flex items-center justify-center text-lg shadow-xs">
                        <i class="fa-solid fa-qrcode"></i>
                    </div>
                    <div>
                        <h3 class="text-base font-extrabold text-slate-900">Google Authenticator (2FA)</h3>
                        <p class="text-xs text-slate-500 font-medium" x-text="qrUser.nama + ' (' + qrUser.email + ')'"></p>
                    </div>
                </div>
                <button type="button" @click="qrModalOpen = false" class="w-8 h-8 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-500 flex items-center justify-center transition-colors">
                    <i class="fa-solid fa-xmark text-xs"></i>
                </button>
            </div>

            <!-- QR Code Box -->
            <div class="bg-slate-50 p-4 rounded-2xl border border-slate-200/90 flex flex-col items-center justify-center">
                <div class="bg-white p-3 rounded-2xl shadow-xs border border-slate-200 flex items-center justify-center w-48 h-48 overflow-hidden [&>svg]:w-full [&>svg]:h-full [&>img]:w-full [&>img]:h-full mb-3">
                    <template x-if="qrUser.qr_code_svg">
                        <div class="w-full h-full flex items-center justify-center" x-html="qrUser.qr_code_svg"></div>
                    </template>
                </div>

                <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400 block mb-1">KODE KUNCI RAHASIA MANUAL</span>
                <div class="flex items-center gap-1.5 bg-white border border-slate-200 rounded-xl px-2.5 py-1.5 shadow-2xs max-w-full">
                    <code class="text-xs font-mono font-extrabold text-[#051B44] tracking-widest px-1 truncate select-all" x-text="qrUser.secret_key || '----------------'"></code>
                    <button type="button" @click="copyToClipboard(qrUser.secret_key)"
                            class="px-2.5 py-1 rounded-lg bg-slate-100 hover:bg-[#051B44] hover:text-white text-slate-700 font-bold text-xs transition-all flex items-center gap-1 cursor-pointer shrink-0">
                        <i class="fa-regular" :class="copiedKey ? 'fa-circle-check text-emerald-600' : 'fa-copy'"></i>
                        <span x-text="copiedKey ? 'Tersalin' : 'Salin'"></span>
                    </button>
                </div>
            </div>

            <p class="text-[11px] text-slate-500 text-left leading-relaxed bg-sky-50/60 p-3 rounded-xl border border-sky-100">
                <i class="fa-solid fa-circle-info text-sky-600 mr-1"></i> Scan QR Code ini di aplikasi <strong>Google Authenticator</strong>. Saat login, petugas cukup memasukkan 6-digit kode OTP dari aplikasi tanpa perlu scan ulang.
            </p>

            <div class="flex items-center justify-between gap-3 pt-2 border-t border-slate-100">
                <button type="button" @click="regenerate2fa(qrUser.id_user)" :disabled="isRegenerating2fa"
                        class="px-4 py-2 rounded-xl border border-slate-200 text-slate-700 hover:bg-slate-50 font-bold text-xs transition-colors flex items-center gap-1.5 disabled:opacity-50">
                    <i class="fa-solid text-xs" :class="isRegenerating2fa ? 'fa-spinner fa-spin' : 'fa-arrows-rotate'"></i>
                    <span x-text="isRegenerating2fa ? 'Membuat...' : 'Buat Ulang Kunci'"></span>
                </button>
                <button type="button" @click="qrModalOpen = false"
                        class="px-5 py-2 rounded-xl bg-[#051B44] hover:bg-navy-900 text-white font-extrabold text-xs shadow-md shadow-sky-950/20 transition-all">
                    Selesai
                </button>
            </div>
        </div>
    </div>

    <!-- ========================================================================= -->
    <!-- TOAST NOTIFICATION                                                        -->
    <!-- ========================================================================= -->
    <div x-show="showToast"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 -translate-y-4 scale-95"
         x-transition:enter-end="opacity-100 translate-y-0 scale-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 translate-y-0 scale-100"
         x-transition:leave-end="opacity-0 -translate-y-4 scale-95"
         class="fixed top-6 right-6 z-50 max-w-sm rounded-2xl shadow-xl border p-4 flex items-center gap-3 backdrop-blur-md"
         :class="toastType === 'success' ? 'bg-[#051B44] text-white border-sky-500/50 shadow-sky-950/20' : 'bg-rose-900/95 text-white border-rose-500/50 shadow-rose-950/20'"
         style="display: none;">
        <div class="w-8 h-8 rounded-xl flex items-center justify-center shrink-0"
             :class="toastType === 'success' ? 'bg-emerald-500 text-white' : 'bg-rose-500 text-white'">
            <i class="fa-solid" :class="toastType === 'success' ? 'fa-check text-sm' : 'fa-xmark text-sm'"></i>
        </div>
        <div class="flex-1 text-xs font-bold leading-snug" x-text="toastMessage"></div>
        <button @click="showToast = false" class="text-white/70 hover:text-white transition-colors">
            <i class="fa-solid fa-xmark text-xs"></i>
        </button>
    </div>

</div>
@endsection

@push('scripts')
<script>
function petugasComponent() {
    return {
        activeTab: 'daftar',
        deleteModalOpen: false,
        securityModalOpen: false,
        qrModalOpen: false,
        selectedUser: null,
        securityUser: { id_user: null, nama: '', email: '', role: '' },
        qrUser: { id_user: null, nama: '', email: '', role: '', qr_code_svg: '', secret_key: '' },
        formPassword: { password: '', password_confirmation: '' },
        isDeleting: false,
        isUpdatingPassword: false,
        isRegenerating2fa: false,
        copiedKey: false,
        toastMessage: '',
        toastType: 'success',
        showToast: false,

        formCreate: {
            nama: '',
            email: '',
            password: '',
            role: 'pembesaran',
            no_tlp: ''
        },

        formEdit: {
            id_user: null,
            nama: '',
            email: '',
            role: 'pembesaran',
            no_tlp: '',
            password: '',
            qr_code_svg: '',
            secret_key: ''
        },

        openEdit(user) {
            this.formEdit = {
                id_user: user.id_user,
                nama: user.nama,
                email: user.email,
                role: user.role,
                no_tlp: user.no_tlp || '',
                password: '',
                qr_code_svg: user.qr_code_svg || '',
                secret_key: user.secret_key || ''
            };
            this.activeTab = 'edit';
            window.scrollTo({ top: 0, behavior: 'smooth' });
        },

        openQrModal(user) {
            this.qrUser = {
                id_user: user.id_user,
                nama: user.nama,
                email: user.email,
                role: user.role,
                qr_code_svg: user.qr_code_svg || '',
                secret_key: user.secret_key || ''
            };
            this.qrModalOpen = true;
        },

        openSecurity(user) {
            this.securityUser = {
                id_user: user.id_user,
                nama: user.nama,
                email: user.email,
                role: user.role
            };
            this.formPassword = { password: '', password_confirmation: '' };
            this.securityModalOpen = true;
        },

        async regenerate2fa(id) {
            if (!id) return;
            this.isRegenerating2fa = true;
            try {
                const res = await fetch('{{ url('/petugas') }}/' + id + '/regenerate-2fa', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                });
                const data = await res.json();
                if (res.ok && data.success) {
                    if (this.formEdit && this.formEdit.id_user == id) {
                        this.formEdit.secret_key = data.secret_key;
                        this.formEdit.qr_code_svg = data.qr_code_svg;
                    }
                    if (this.qrUser && this.qrUser.id_user == id) {
                        this.qrUser.secret_key = data.secret_key;
                        this.qrUser.qr_code_svg = data.qr_code_svg;
                    }
                    this.triggerToast(data.message || 'Kunci 2FA baru berhasil dibuat!', 'success');
                } else {
                    this.triggerToast(data.message || 'Gagal membuat ulang kunci 2FA.', 'error');
                }
            } catch(e) {
                this.triggerToast('Terjadi kesalahan saat membuat ulang kunci 2FA.', 'error');
            } finally {
                this.isRegenerating2fa = false;
            }
        },

        copyToClipboard(text) {
            if (!text) return;
            navigator.clipboard.writeText(text);
            this.copiedKey = true;
            this.triggerToast('Kunci rahasia 2FA berhasil disalin!', 'success');
            setTimeout(() => this.copiedKey = false, 2500);
        },

        async submitPassword() {
            if (!this.formPassword.password || this.formPassword.password.length < 6) {
                this.triggerToast('Password minimal harus 6 karakter.', 'error');
                return;
            }
            if (this.formPassword.password !== this.formPassword.password_confirmation) {
                this.triggerToast('Konfirmasi password baru tidak cocok.', 'error');
                return;
            }

            this.isUpdatingPassword = true;
            try {
                const res = await fetch('{{ url('/petugas') }}/' + this.securityUser.id_user + '/password', {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify(this.formPassword)
                });
                const data = await res.json();
                if (res.ok && data.success) {
                    this.securityModalOpen = false;
                    this.formPassword = { password: '', password_confirmation: '' };
                    this.triggerToast(data.message || 'Password berhasil diperbarui!', 'success');
                } else {
                    this.triggerToast(data.message || 'Gagal mengubah password.', 'error');
                }
            } catch(e) {
                this.triggerToast('Terjadi kesalahan koneksi server.', 'error');
            } finally {
                this.isUpdatingPassword = false;
            }
        },

        confirmDelete(user) {
            this.selectedUser = user;
            this.deleteModalOpen = true;
        },

        async executeDelete() {
            if (!this.selectedUser || this.isDeleting) return;
            this.isDeleting = true;
            const id = this.selectedUser.id_user;
            const name = this.selectedUser.nama;

            try {
                const res = await fetch('{{ url('/petugas') }}/' + id, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    }
                });
                const data = await res.json();

                if (res.ok && data.success) {
                    this.deleteModalOpen = false;
                    const rowEl = document.getElementById('user-row-' + id);
                    if (rowEl) {
                        rowEl.style.transition = 'all 0.4s ease';
                        rowEl.style.opacity = '0';
                        rowEl.style.transform = 'scale(0.95)';
                        setTimeout(() => rowEl.remove(), 400);
                    }
                    this.triggerToast(data.message || 'Data petugas ' + name + ' berhasil dihapus!', 'success');
                } else {
                    this.triggerToast(data.message || 'Gagal menghapus data petugas.', 'error');
                }
            } catch (err) {
                this.triggerToast('Terjadi kesalahan jaringan saat menghapus data.', 'error');
            } finally {
                this.isDeleting = false;
                this.selectedUser = null;
            }
        },

        triggerToast(message, type = 'success') {
            this.toastMessage = message;
            this.toastType = type;
            this.showToast = true;
            setTimeout(() => {
                this.showToast = false;
            }, 4000);
        }
    };
}
</script>
@endpush

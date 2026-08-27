@extends('mobile_web_petugas.petugas_pembibitan.layout')

@section('title', 'Profile & Akun - SIM-BUDIDAYA Mobile')

@section('content')
<div class="p-4 space-y-4" x-data="akunPetugasPembibitan()">

    <!-- Profile Header Card (Matches Screen 4 Mockup) -->
    <div class="bg-white rounded-3xl border border-slate-200/90 p-5 shadow-xs flex flex-col items-center text-center relative overflow-hidden">
        
        <!-- Top Gradient Banner -->
        <div class="w-full h-16 bg-gradient-to-r from-navy-800 to-sky-700 absolute top-0 left-0"></div>
        
        <!-- Avatar Picture with Checkmark Badge -->
        <div class="relative z-10 mt-4">
            <img src="https://images.unsplash.com/photo-1534528741775-53994a69daeb?w=300&auto=format&fit=crop&q=80" 
                 alt="User Profile" 
                 class="w-20 h-20 rounded-full border-4 border-white object-cover shadow-md mx-auto">
            <span class="absolute bottom-0 right-0 w-6 h-6 rounded-full bg-emerald-500 border-2 border-white flex items-center justify-center text-white text-[10px]">
                <i class="fa-solid fa-check"></i>
            </span>
        </div>

        <!-- Name & Title -->
        <div class="mt-3 relative z-10 space-y-1">
            <h1 class="text-base font-extrabold text-navy-900" x-text="userName">Budi Santoso</h1>
            <p class="text-xs text-slate-500 font-semibold" x-text="i18n[currentLang].role"></p>
            <span class="inline-block mt-1 px-3 py-0.5 rounded-full text-[10px] font-extrabold bg-sky-50 text-sky-700 border border-sky-200"
                  x-text="i18n[currentLang].division">
                Divisi Pembibitan & Hatchery
            </span>
        </div>

        <!-- Edit Profile Button -->
        <button @click="editProfileModal = true"
                class="mt-4 px-4 py-1.5 rounded-full border border-sky-600 text-sky-700 hover:bg-sky-50 text-xs font-extrabold flex items-center gap-1.5 transition-all">
            <i class="fa-solid fa-user-pen text-[11px]"></i>
            <span x-text="i18n[currentLang].editBtn">Edit Profil</span>
        </button>

    </div>

    <!-- Menu Items List Card -->
    <div class="bg-white rounded-3xl border border-slate-200/90 overflow-hidden shadow-xs divide-y divide-slate-100">
        
        <!-- Item 1: Pengaturan -->
        <button @click="settingsModal = true"
                class="w-full p-4 flex items-center justify-between hover:bg-slate-50 transition-colors text-left">
            <div class="flex items-center gap-3.5">
                <div class="w-9 h-9 rounded-xl bg-sky-50 text-sky-700 flex items-center justify-center text-sm">
                    <i class="fa-solid fa-gear"></i>
                </div>
                <div>
                    <h3 class="text-xs font-bold text-slate-900" x-text="i18n[currentLang].settings || 'Pengaturan'">Pengaturan</h3>
                    <p class="text-[10px] text-slate-400 font-medium" x-text="i18n[currentLang].settingsSub || 'Keamanan 2FA, notifikasi & preferensi'">Keamanan 2FA, notifikasi & preferensi</p>
                </div>
            </div>
            <i class="fa-solid fa-chevron-right text-slate-400 text-xs"></i>
        </button>

        <!-- Item 3: Bahasa -->
        <button @click="langModal = true"
                class="w-full p-4 flex items-center justify-between hover:bg-slate-50 transition-colors text-left">
            <div class="flex items-center gap-3.5">
                <div class="w-9 h-9 rounded-xl bg-amber-50 text-amber-700 flex items-center justify-center text-sm">
                    <i class="fa-solid fa-globe"></i>
                </div>
                <div>
                    <h3 class="text-xs font-bold text-slate-900" x-text="i18n[currentLang].language">Bahasa</h3>
                    <p class="text-[10px] text-slate-400 font-medium" x-text="currentLang === 'id' ? '🇮🇩 Bahasa Indonesia' : '🇬🇧 English'"></p>
                </div>
            </div>
            <div class="flex items-center gap-2">
                <span class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-slate-100 text-slate-600 uppercase" x-text="currentLang"></span>
                <i class="fa-solid fa-chevron-right text-slate-400 text-xs"></i>
            </div>
        </button>

    </div>

    <!-- Action Button: Keluar -->
    <div class="pt-2">
        <button @click="logoutModal = true" 
                class="w-full py-3 rounded-2xl bg-rose-50 border border-rose-200 hover:bg-rose-100 text-rose-600 font-extrabold text-xs flex items-center justify-center gap-2 shadow-2xs transition-all">
            <i class="fa-solid fa-arrow-right-from-bracket text-xs"></i>
            <span x-text="i18n[currentLang].logout">Keluar</span>
        </button>
    </div>

    <!-- Modals -->

    <!-- Edit Profile Modal -->
    <div x-show="editProfileModal" 
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
        
        <div class="bg-white rounded-3xl w-full max-w-sm overflow-hidden shadow-2xl p-5 space-y-4">
            <div class="flex items-center justify-between border-b border-slate-100 pb-2">
                <h3 class="text-xs font-bold text-navy-900" x-text="i18n[currentLang].editProfile">Edit Profile Petugas Pembibitan</h3>
                <button @click="editProfileModal = false" class="text-slate-400 hover:text-slate-600">
                    <i class="fa-solid fa-xmark text-sm"></i>
                </button>
            </div>

            <div class="space-y-3">
                <div>
                    <label class="text-[10px] font-extrabold uppercase text-slate-500 block mb-1" x-text="i18n[currentLang].fullName">NAMA LENGKAP</label>
                    <input type="text" x-model="userName" class="w-full px-3.5 py-2 rounded-xl border border-slate-200 text-xs font-semibold text-slate-800 focus:outline-none focus:border-sky-600">
                </div>
                <div>
                    <label class="text-[10px] font-extrabold uppercase text-slate-500 block mb-1" x-text="i18n[currentLang].phone">NOMOR HP</label>
                    <input type="text" x-model="userPhone" class="w-full px-3.5 py-2 rounded-xl border border-slate-200 text-xs font-semibold text-slate-800 focus:outline-none focus:border-sky-600">
                </div>
                <div>
                    <label class="text-[10px] font-extrabold uppercase text-slate-500 block mb-1" x-text="i18n[currentLang].jobTitle">JABATAN</label>
                    <input type="text" :value="i18n[currentLang].role" readonly class="w-full px-3.5 py-2 rounded-xl border border-slate-200 text-xs font-semibold text-slate-400 bg-slate-100 cursor-not-allowed">
                </div>
            </div>

            <button @click="editProfileModal = false; triggerToast(i18n[currentLang].profileUpdated, 'success')" 
                    class="w-full py-2.5 rounded-xl bg-navy-800 hover:bg-navy-900 text-white font-bold text-xs shadow-xs transition-colors"
                    x-text="i18n[currentLang].saveChanges">
                Simpan Perubahan
            </button>
        </div>
    </div>

    <!-- Pengaturan Modal (2FA & Notifikasi) -->
    <div x-show="settingsModal" 
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4"
         style="display: none;">
        
        <div class="bg-white rounded-3xl w-full max-w-sm overflow-hidden shadow-2xl p-5 space-y-4">
            <div class="flex items-center justify-between border-b border-slate-100 pb-2">
                <div class="flex items-center gap-2">
                    <i class="fa-solid fa-gear text-sky-600"></i>
                    <h3 class="text-xs font-bold text-navy-900">Pengaturan Akun & Sistem</h3>
                </div>
                <button @click="settingsModal = false" class="text-slate-400 hover:text-slate-600">
                    <i class="fa-solid fa-xmark text-sm"></i>
                </button>
            </div>

            <div class="space-y-3.5 text-xs">
                <!-- 2FA Status -->
                <div class="p-3.5 bg-slate-50 border border-slate-100 rounded-2xl space-y-2">
                    <div class="flex items-center justify-between">
                        <span class="font-extrabold text-navy-900 flex items-center gap-1.5">
                            <i class="fa-solid fa-shield-halved text-sky-600"></i> Autentikasi 2FA
                        </span>
                        <span class="px-2 py-0.5 rounded-full text-[9px] font-extrabold uppercase bg-emerald-50 text-emerald-700 border border-emerald-200">
                            Aktif
                        </span>
                    </div>
                    <p class="text-[10px] text-slate-500 font-medium">Verifikasi 6-digit OTP Google Authenticator saat login.</p>
                </div>

                <!-- Notifikasi Toggle -->
                <div class="p-3.5 bg-slate-50 border border-slate-100 rounded-2xl flex items-center justify-between">
                    <div>
                        <span class="font-extrabold text-navy-900 block">Notifikasi Push & Sound</span>
                        <span class="text-[10px] text-slate-400 font-medium">Alert suhu, pH & pakan</span>
                    </div>
                    <input type="checkbox" checked class="w-4 h-4 accent-navy-800 rounded cursor-pointer">
                </div>

                <!-- Alert Kematian WhatsApp -->
                <div class="p-3.5 bg-slate-50 border border-slate-100 rounded-2xl flex items-center justify-between">
                    <div>
                        <span class="font-extrabold text-navy-900 block">Alert WhatsApp</span>
                        <span class="text-[10px] text-slate-400 font-medium">Laporan kematian bibit</span>
                    </div>
                    <input type="checkbox" checked class="w-4 h-4 accent-navy-800 rounded cursor-pointer">
                </div>
            </div>

            <button @click="settingsModal = false; if(typeof triggerToast==='function') triggerToast('Pengaturan berhasil disimpan!', 'success')" 
                    class="w-full py-2.5 rounded-xl bg-navy-800 hover:bg-navy-900 text-white font-bold text-xs shadow-xs transition-colors">
                Simpan Pengaturan
            </button>
        </div>
    </div>

    <!-- Language Modal (Hanya Indonesia & English) -->
    <div x-show="langModal" 
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
        
        <div class="bg-white rounded-3xl w-full max-w-sm overflow-hidden shadow-2xl p-5 space-y-4 text-center">
            <div class="flex items-center justify-between border-b border-slate-100 pb-2">
                <h3 class="text-xs font-bold text-navy-900" x-text="i18n[currentLang].languageModalTitle">Pilih Bahasa / Language</h3>
                <button @click="langModal = false" class="text-slate-400 hover:text-slate-600">
                    <i class="fa-solid fa-xmark text-sm"></i>
                </button>
            </div>
            
            <div class="space-y-2.5 text-left">
                <!-- Option 1: Bahasa Indonesia -->
                <button @click="setLanguage('id')" 
                        class="w-full p-3.5 rounded-2xl border transition-all flex items-center justify-between font-bold text-xs cursor-pointer"
                        :class="currentLang === 'id' ? 'border-sky-500 bg-sky-50/70 text-navy-900 shadow-xs' : 'border-slate-200 hover:bg-slate-50 text-slate-700'">
                    <div class="flex items-center gap-3">
                        <span class="text-xl">🇮🇩</span>
                        <div>
                            <span class="block text-xs font-extrabold text-slate-900">Bahasa Indonesia</span>
                            <span class="block text-[10px] text-slate-400 font-normal">Gunakan Bahasa Indonesia sebagai bahasa utama</span>
                        </div>
                    </div>
                    <div class="w-6 h-6 rounded-full flex items-center justify-center" :class="currentLang === 'id' ? 'bg-sky-500 text-white' : 'border border-slate-300 text-transparent'">
                        <i class="fa-solid fa-check text-[11px]"></i>
                    </div>
                </button>

                <!-- Option 2: English -->
                <button @click="setLanguage('en')" 
                        class="w-full p-3.5 rounded-2xl border transition-all flex items-center justify-between font-bold text-xs cursor-pointer"
                        :class="currentLang === 'en' ? 'border-sky-500 bg-sky-50/70 text-navy-900 shadow-xs' : 'border-slate-200 hover:bg-slate-50 text-slate-700'">
                    <div class="flex items-center gap-3">
                        <span class="text-xl">🇬🇧</span>
                        <div>
                            <span class="block text-xs font-extrabold text-slate-900">English</span>
                            <span class="block text-[10px] text-slate-400 font-normal">Use English as the primary interface language</span>
                        </div>
                    </div>
                    <div class="w-6 h-6 rounded-full flex items-center justify-center" :class="currentLang === 'en' ? 'bg-sky-500 text-white' : 'border border-slate-300 text-transparent'">
                        <i class="fa-solid fa-check text-[11px]"></i>
                    </div>
                </button>
            </div>
        </div>
    </div>

    <!-- Logout Confirmation Modal -->
    <div x-show="logoutModal" 
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
        
        <div class="bg-white rounded-3xl w-full max-w-sm overflow-hidden shadow-2xl p-5 space-y-4 text-center">
            <div class="w-12 h-12 rounded-full bg-rose-100 text-rose-600 mx-auto flex items-center justify-center text-lg">
                <i class="fa-solid fa-arrow-right-from-bracket"></i>
            </div>
            
            <div>
                <h3 class="text-sm font-extrabold text-slate-900" x-text="i18n[currentLang].logoutConfirm">Konfirmasi Keluar</h3>
                <p class="text-xs text-slate-500 font-medium mt-1" x-text="i18n[currentLang].logoutMessage">Apakah Anda yakin ingin keluar dari aplikasi Petugas Pembibitan?</p>
            </div>

            <div class="grid grid-cols-2 gap-2 pt-2">
                <button @click="logoutModal = false" 
                        class="py-2.5 rounded-xl border border-slate-200 text-slate-700 font-bold text-xs hover:bg-slate-50"
                        x-text="i18n[currentLang].cancel">
                    Batal
                </button>
                <form action="{{ route('mobile.petugas.logout') }}" method="POST" class="w-full">
                    @csrf
                    <button type="submit" 
                            class="w-full py-2.5 rounded-xl bg-rose-600 hover:bg-rose-700 text-white font-bold text-xs flex items-center justify-center shadow-xs"
                            x-text="i18n[currentLang].yesLogout">
                        Ya, Keluar
                    </button>
                </form>
            </div>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
function akunPetugasPembibitan() {
    return {
        editProfileModal: false,
        settingsModal: false,
        langModal: false,
        logoutModal: false,
        userName: '{{ Auth::user()->nama ?? 'Tim Pembibitan' }}',
        userPhone: '{{ Auth::user()->no_tlp ?? '+62 812-9876-5432' }}',
        currentLang: localStorage.getItem('sim_lang') || 'id',

        i18n: {
            id: {
                role: 'Petugas Lapangan Pembibitan',
                division: 'Divisi Pembibitan & Hatchery',
                editBtn: 'Edit Profil',
                editProfile: 'Edit Profile Petugas Pembibitan',
                fullName: 'NAMA LENGKAP',
                phone: 'NOMOR HP',
                jobTitle: 'JABATAN',
                saveChanges: 'Simpan Perubahan',
                profileUpdated: 'Profil berhasil diperbarui!',
                notifications: 'Pengaturan Notifikasi',
                notificationsSub: 'Alert suhu, pH & jadwal pakan',
                notifSaved: 'Pengaturan notifikasi berhasil disimpan',
                help: 'Bantuan & Dukungan',
                helpSub: 'Panduan Hatchery & Support CS',
                language: 'Bahasa',
                languageModalTitle: 'Pilih Bahasa / Language',
                logout: 'Keluar',
                logoutConfirm: 'Konfirmasi Keluar',
                logoutMessage: 'Apakah Anda yakin ingin keluar dari aplikasi Petugas Pembibitan?',
                cancel: 'Batal',
                yesLogout: 'Ya, Keluar',
                langChanged: 'Bahasa berhasil diubah ke Bahasa Indonesia (🇮🇩)'
            },
            en: {
                role: 'Breeding & Hatchery Technician',
                division: 'Breeding & Hatchery Division',
                editBtn: 'Edit Profile',
                editProfile: 'Edit Breeding Officer Profile',
                fullName: 'FULL NAME',
                phone: 'PHONE NUMBER',
                jobTitle: 'POSITION',
                saveChanges: 'Save Changes',
                profileUpdated: 'Profile successfully updated!',
                notifications: 'Notification Settings',
                notificationsSub: 'Temperature, pH alerts & feeding schedule',
                notifSaved: 'Notification settings saved successfully',
                help: 'Help & Support',
                helpSub: 'Hatchery Guide & CS Support',
                language: 'Language',
                languageModalTitle: 'Select Language',
                logout: 'Log Out',
                logoutConfirm: 'Confirm Log Out',
                logoutMessage: 'Are you sure you want to log out of Breeding Officer app?',
                cancel: 'Cancel',
                yesLogout: 'Yes, Log Out',
                langChanged: 'Language successfully changed to English (🇬🇧)'
            }
        },

        setLanguage(lang) {
            this.currentLang = lang;
            localStorage.setItem('sim_lang', lang);
            this.langModal = false;
            if (typeof triggerToast === 'function') {
                triggerToast(this.i18n[lang].langChanged, 'success');
            }
        }
    };
}
</script>
@endpush


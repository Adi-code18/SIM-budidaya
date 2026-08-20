@extends('mobile_web_petugas.petugas_pembibitan.layout')

@section('title', 'Profile & Akun - SIM-BUDIDAYA Mobile')

@section('content')
<div class="p-4 space-y-4" x-data="{ 
    editProfileModal: false,
    langModal: false,
    logoutModal: false,
    userName: 'Budi Santoso',
    userRole: 'Field Technician / Petugas Pembibitan',
    userPhone: '+62 812-9876-5432',
    selectedLang: 'Indonesia'
}">

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
            <p class="text-xs text-slate-500 font-semibold" x-text="userRole">Field Technician</p>
            <span class="inline-block mt-1 px-3 py-0.5 rounded-full text-[10px] font-extrabold bg-sky-50 text-sky-700 border border-sky-200">
                Divisi Pembibitan & Hatchery
            </span>
        </div>

        <!-- Edit Profile Button -->
        <button @click="editProfileModal = true"
                class="mt-4 px-4 py-1.5 rounded-full border border-sky-600 text-sky-700 hover:bg-sky-50 text-xs font-extrabold flex items-center gap-1.5 transition-all">
            <i class="fa-solid fa-user-pen text-[11px]"></i>
            <span>Edit Profil</span>
        </button>

    </div>

    <!-- Menu Items List Card -->
    <div class="bg-white rounded-3xl border border-slate-200/90 overflow-hidden shadow-xs divide-y divide-slate-100">
        
        <!-- Item 1: Pengaturan Notifikasi -->
        <button @click="triggerToast('Pengaturan notifikasi berhasil disimpan', 'info')"
                class="w-full p-4 flex items-center justify-between hover:bg-slate-50 transition-colors text-left">
            <div class="flex items-center gap-3.5">
                <div class="w-9 h-9 rounded-xl bg-sky-50 text-sky-700 flex items-center justify-center text-sm">
                    <i class="fa-regular fa-bell"></i>
                </div>
                <div>
                    <h3 class="text-xs font-bold text-slate-900">Pengaturan Notifikasi</h3>
                    <p class="text-[10px] text-slate-400 font-medium">Alert suhu, pH & jadwal pakan</p>
                </div>
            </div>
            <i class="fa-solid fa-chevron-right text-slate-400 text-xs"></i>
        </button>

        <!-- Item 2: Bantuan & Dukungan -->
        <a href="https://wa.me/6281234567890?text=Halo%20Admin%20SIM-BUDIDAYA,%20saya%20petugas%20pembibitan%20butuh%20bantuan." target="_blank"
           class="w-full p-4 flex items-center justify-between hover:bg-slate-50 transition-colors text-left">
            <div class="flex items-center gap-3.5">
                <div class="w-9 h-9 rounded-xl bg-indigo-50 text-indigo-700 flex items-center justify-center text-sm">
                    <i class="fa-regular fa-circle-question"></i>
                </div>
                <div>
                    <h3 class="text-xs font-bold text-slate-900">Bantuan & Dukungan</h3>
                    <p class="text-[10px] text-slate-400 font-medium">Panduan Hatchery & Support CS</p>
                </div>
            </div>
            <i class="fa-solid fa-chevron-right text-slate-400 text-xs"></i>
        </a>

        <!-- Item 3: Bahasa -->
        <button @click="langModal = true"
                class="w-full p-4 flex items-center justify-between hover:bg-slate-50 transition-colors text-left">
            <div class="flex items-center gap-3.5">
                <div class="w-9 h-9 rounded-xl bg-amber-50 text-amber-700 flex items-center justify-center text-sm">
                    <i class="fa-solid fa-globe"></i>
                </div>
                <div>
                    <h3 class="text-xs font-bold text-slate-900">Bahasa</h3>
                    <p class="text-[10px] text-slate-400 font-medium" x-text="selectedLang">Bahasa Indonesia</p>
                </div>
            </div>
            <i class="fa-solid fa-chevron-right text-slate-400 text-xs"></i>
        </button>

    </div>

    <!-- Action Button: Keluar -->
    <div class="pt-2">
        <button @click="logoutModal = true" 
                class="w-full py-3 rounded-2xl bg-rose-50 border border-rose-200 hover:bg-rose-100 text-rose-600 font-extrabold text-xs flex items-center justify-center gap-2 shadow-2xs transition-all">
            <i class="fa-solid fa-arrow-right-from-bracket text-xs"></i>
            <span>Keluar</span>
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
                <h3 class="text-xs font-bold text-navy-900">Edit Profile Petugas Pembibitan</h3>
                <button @click="editProfileModal = false" class="text-slate-400 hover:text-slate-600">
                    <i class="fa-solid fa-xmark text-sm"></i>
                </button>
            </div>

            <div class="space-y-3">
                <div>
                    <label class="text-[10px] font-extrabold uppercase text-slate-500 block mb-1">NAMA LENGKAP</label>
                    <input type="text" x-model="userName" class="w-full px-3.5 py-2 rounded-xl border border-slate-200 text-xs font-semibold text-slate-800">
                </div>
                <div>
                    <label class="text-[10px] font-extrabold uppercase text-slate-500 block mb-1">NOMOR HP</label>
                    <input type="text" x-model="userPhone" class="w-full px-3.5 py-2 rounded-xl border border-slate-200 text-xs font-semibold text-slate-800">
                </div>
                <div>
                    <label class="text-[10px] font-extrabold uppercase text-slate-500 block mb-1">JABATAN</label>
                    <input type="text" x-model="userRole" readonly class="w-full px-3.5 py-2 rounded-xl border border-slate-200 text-xs font-semibold text-slate-400 bg-slate-100">
                </div>
            </div>

            <button @click="editProfileModal = false; triggerToast('Profil berhasil diperbarui', 'success')" 
                    class="w-full py-2.5 rounded-xl bg-navy-800 text-white font-bold text-xs shadow-xs">
                Simpan Perubahan
            </button>
        </div>
    </div>

    <!-- Language Modal -->
    <div x-show="langModal" 
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
        
        <div class="bg-white rounded-3xl w-full max-w-sm overflow-hidden shadow-2xl p-5 space-y-4 text-center">
            <h3 class="text-xs font-bold text-navy-900">Pilih Bahasa / Language</h3>
            
            <div class="space-y-2">
                <button @click="selectedLang = 'Bahasa Indonesia'; langModal = false" 
                        class="w-full p-3 rounded-xl border border-slate-200 flex items-center justify-between hover:bg-sky-50 font-bold text-xs">
                    <span>🇮🇩 Bahasa Indonesia</span>
                    <i class="fa-solid fa-check text-sky-600" x-show="selectedLang === 'Bahasa Indonesia'"></i>
                </button>
                <button @click="selectedLang = 'English'; langModal = false" 
                        class="w-full p-3 rounded-xl border border-slate-200 flex items-center justify-between hover:bg-sky-50 font-bold text-xs">
                    <span>🇬🇧 English</span>
                    <i class="fa-solid fa-check text-sky-600" x-show="selectedLang === 'English'"></i>
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
                <h3 class="text-sm font-extrabold text-slate-900">Konfirmasi Keluar</h3>
                <p class="text-xs text-slate-500 font-medium mt-1">Apakah Anda yakin ingin keluar dari aplikasi Petugas Pembibitan?</p>
            </div>

            <div class="grid grid-cols-2 gap-2 pt-2">
                <button @click="logoutModal = false" class="py-2.5 rounded-xl border border-slate-200 text-slate-700 font-bold text-xs">
                    Batal
                </button>
                <a href="{{ route('login') }}" class="py-2.5 rounded-xl bg-rose-600 text-white font-bold text-xs flex items-center justify-center">
                    Ya, Keluar
                </a>
            </div>
        </div>
    </div>

</div>
@endsection

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
            <p class="text-xs text-slate-500 font-medium mt-0.5">Kelola informasi akun manajer, keamanan 2FA, batas ambang alarm budidaya, dan preferensi sistem.</p>
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

        <button type="button" 
                @click="activeTab = 'notifikasi'"
                :class="activeTab === 'notifikasi' ? 'bg-[#051B44] text-white shadow-md shadow-[#051B44]/20' : 'bg-white text-slate-600 hover:bg-slate-100 border border-slate-200/80'"
                class="px-5 py-2.5 rounded-xl font-extrabold text-xs flex items-center gap-2.5 transition-all cursor-pointer shrink-0">
            <i class="fa-solid fa-bell text-xs"></i>
            <span>Notifikasi & Sistem</span>
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

            <form action="{{ route('pengaturan.update-profile') }}" method="POST" class="space-y-5">
                @csrf
                @method('PUT')

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
                        <div class="flex items-center rounded-xl border border-slate-200 bg-slate-50 overflow-hidden focus-within:bg-white focus-within:border-sky-500 focus-within:ring-2 focus-within:ring-sky-500/10 transition-all">
                            <span class="px-3 py-2.5 text-xs font-bold text-slate-500 bg-slate-100/90 border-r border-slate-200 shrink-0 flex items-center gap-1.5">
                                <span>🇮🇩</span>
                                <span>+62</span>
                            </span>
                            <input type="tel" name="no_tlp" value="{{ old('no_tlp', $user->no_tlp ?? '') }}" placeholder="812-3456-7890 / 081234567890"
                                   class="w-full px-3.5 py-2.5 text-xs font-bold text-slate-800 bg-transparent border-0 focus:outline-none">
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
                <h3 class="text-sm font-extrabold text-[#051B44] border-b border-slate-100 pb-3 flex items-center gap-2">
                    <i class="fa-solid fa-id-card text-sky-600"></i>
                    <span>Ringkasan Akun</span>
                </h3>

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

    <!-- TAB 3: NOTIFIKASI & SISTEM -->
    <div x-show="activeTab === 'notifikasi'" 
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 translate-y-1"
         x-transition:enter-end="opacity-100 translate-y-0"
         class="bg-white rounded-3xl border border-slate-200/90 p-6 sm:p-8 shadow-xs space-y-6">

        <div class="flex items-center gap-3 border-b border-slate-100 pb-4">
            <div class="w-10 h-10 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center text-base font-bold">
                <i class="fa-solid fa-bell"></i>
            </div>
            <div>
                <h3 class="text-base font-extrabold text-[#051B44]">Saluran Notifikasi & Alerts</h3>
                <p class="text-xs text-slate-500 font-medium">Atur media pemberitahuan saat terjadi perubahan stok, laporan panen, atau ajuan libur petugas.</p>
            </div>
        </div>

        <div class="space-y-4">
            <div class="flex items-center justify-between p-4 rounded-2xl bg-slate-50 border border-slate-100">
                <div class="space-y-0.5">
                    <h4 class="text-xs font-extrabold text-slate-800">Notifikasi Email Otomatis</h4>
                    <p class="text-[11px] text-slate-500 font-medium">Kirimkan ringkasan mingguan margin keuangan dan siklus panen ke email manajer.</p>
                </div>
                <input type="checkbox" checked class="w-5 h-5 accent-[#051B44] rounded cursor-pointer">
            </div>
        </div>
    </div>

</div>
@endsection

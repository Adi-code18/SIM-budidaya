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

    <!-- Page Header & Branding Banner -->
    <div class="bg-gradient-to-r from-[#031B4E] via-[#0B192C] to-[#0F2C59] rounded-3xl p-6 sm:p-8 text-white shadow-xl relative overflow-hidden flex flex-col md:flex-row md:items-center justify-between gap-6">
        <div class="space-y-2 relative z-10">
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/10 text-sky-300 text-[11px] font-extrabold tracking-wider uppercase backdrop-blur-md">
                <i class="fa-solid fa-sliders text-xs"></i>
                <span>Sistem & Pengaturan</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight text-white">Pengaturan & Profil</h1>
            <p class="text-xs sm:text-sm text-slate-300 max-w-xl font-medium">
                Kelola informasi akun manajer, keamanan 2FA, batas ambang alarm budidaya, serta preferensi notifikasi sistem.
            </p>
        </div>

        <!-- User Quick Info Card -->
        <div class="bg-white/10 backdrop-blur-md border border-white/15 rounded-2xl p-4 flex items-center gap-4 shrink-0 relative z-10">
            <div class="w-12 h-12 rounded-2xl bg-sky-500 text-white flex items-center justify-center font-extrabold text-lg shadow-lg shadow-sky-500/30">
                {{ strtoupper(substr($user->nama ?? 'M', 0, 1)) }}
            </div>
            <div>
                <h3 class="text-sm font-extrabold text-white">{{ $user->nama ?? 'Manajer Utama' }}</h3>
                <span class="text-[11px] font-bold text-sky-200 uppercase tracking-wide block">{{ $user->role ?? 'Manajer' }}</span>
                <span class="text-[10px] text-slate-300 font-medium block mt-0.5">{{ $user->email ?? 'manajer@simbudidaya.id' }}</span>
            </div>
        </div>

        <!-- Decorative Glow Background -->
        <div class="absolute -right-10 -bottom-10 w-60 h-60 bg-sky-500/10 rounded-full blur-3xl pointer-events-none"></div>
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
                @click="activeTab = 'budidaya'"
                :class="activeTab === 'budidaya' ? 'bg-[#051B44] text-white shadow-md shadow-[#051B44]/20' : 'bg-white text-slate-600 hover:bg-slate-100 border border-slate-200/80'"
                class="px-5 py-2.5 rounded-xl font-extrabold text-xs flex items-center gap-2.5 transition-all cursor-pointer shrink-0">
            <i class="fa-solid fa-fish-fins text-xs"></i>
            <span>Parameter Budidaya</span>
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

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
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
                        <span class="font-bold text-slate-500">Status 2FA</span>
                        @if($user->two_factor_secret && $user->two_factor_confirmed_at)
                            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold uppercase bg-emerald-100 text-emerald-800 border border-emerald-200">
                                Aktif (Aman)
                            </span>
                        @else
                            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold uppercase bg-amber-100 text-amber-800 border border-amber-200">
                                Nonaktif
                            </span>
                        @endif
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
                    Gunakan password yang kuat dan kombinasikan dengan <strong>Autentikasi Dua Langkah (2FA)</strong> untuk melindungi data budidaya dan keuangan dari akses tak berizin.
                </p>
            </div>
        </div>
    </div>

    <!-- TAB 2: KEAMANAN & 2FA -->
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
                <h3 class="text-base font-extrabold text-[#051B44]">Autentikasi Dua Langkah (2FA)</h3>
                <p class="text-xs text-slate-500 font-medium">Lindungi akun manajer dengan verifikasi OTP menggunakan Google Authenticator atau aplikasi sejenis.</p>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 items-center">
            <div class="md:col-span-2 space-y-3">
                <div class="flex items-center gap-3">
                    <span class="text-xs font-bold text-slate-700">Status Proteksi 2FA:</span>
                    @if($user->two_factor_secret && $user->two_factor_confirmed_at)
                        <span class="px-3 py-1 rounded-full text-xs font-extrabold bg-emerald-100 text-emerald-800 border border-emerald-200 flex items-center gap-1.5">
                            <i class="fa-solid fa-circle-check text-emerald-600"></i>
                            <span>AKTIF</span>
                        </span>
                    @else
                        <span class="px-3 py-1 rounded-full text-xs font-extrabold bg-amber-100 text-amber-800 border border-amber-200 flex items-center gap-1.5">
                            <i class="fa-solid fa-circle-exclamation text-amber-600"></i>
                            <span>BELUM AKTIF</span>
                        </span>
                    @endif
                </div>

                <p class="text-xs text-slate-600 leading-relaxed font-medium">
                    Jika 2FA diaktifkan, Anda akan diminta memasukkan 6 digit kode OTP dari aplikasi autentikator setiap kali melakukan proses login ke dalam portal manajemen SIM-BUDIDAYA.
                </p>
            </div>

            <div class="flex flex-col items-start md:items-end justify-center gap-3">
                @if($user->two_factor_secret && $user->two_factor_confirmed_at)
                    <form action="{{ route('2fa.disable') }}" method="POST">
                        @csrf
                        <button type="submit" 
                                onclick="return confirm('Apakah Anda yakin ingin menonaktifkan 2FA? Risiko keamanan akun akan meningkat.')"
                                class="px-5 py-2.5 rounded-xl bg-rose-50 hover:bg-rose-100 text-rose-700 border border-rose-200 font-bold text-xs flex items-center gap-2 transition-all cursor-pointer">
                            <i class="fa-solid fa-lock-open text-xs"></i>
                            <span>Nonaktifkan 2FA</span>
                        </button>
                    </form>
                @else
                    <a href="{{ route('2fa.setup') }}" 
                       class="px-5 py-2.5 rounded-xl bg-[#051B44] hover:bg-[#09265c] text-white font-bold text-xs flex items-center gap-2 shadow-md shadow-[#051B44]/20 transition-all">
                        <i class="fa-solid fa-qrcode text-xs"></i>
                        <span>Setup & Aktifkan 2FA Sekarang</span>
                    </a>
                @endif
            </div>
        </div>
    </div>

    <!-- TAB 3: PARAMETER BUDIDAYA -->
    <div x-show="activeTab === 'budidaya'" 
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 translate-y-1"
         x-transition:enter-end="opacity-100 translate-y-0"
         class="bg-white rounded-3xl border border-slate-200/90 p-6 sm:p-8 shadow-xs space-y-6">

        <div class="flex items-center gap-3 border-b border-slate-100 pb-4">
            <div class="w-10 h-10 rounded-2xl bg-teal-50 text-teal-600 flex items-center justify-center text-base font-bold">
                <i class="fa-solid fa-sliders"></i>
            </div>
            <div>
                <h3 class="text-base font-extrabold text-[#051B44]">Ambang Batas Alarm & Parameter Budidaya</h3>
                <p class="text-xs text-slate-500 font-medium">Tentukan toleransi alarm kesehatan batch dan target efisiensi pakan (FCR).</p>
            </div>
        </div>

        <form action="{{ route('pengaturan.update-preferences') }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div class="bg-slate-50 p-4 rounded-2xl border border-slate-100 space-y-2">
                    <label class="block text-xs font-extrabold text-slate-800">Target FCR Standar (Feed Conversion Ratio)</label>
                    <input type="number" step="0.01" name="target_fcr" value="{{ $settings['target_fcr'] }}" required
                           class="w-full px-4 py-2.5 rounded-xl bg-white border border-slate-200 text-xs font-bold text-slate-800 focus:outline-none focus:border-sky-500 focus:ring-2 focus:ring-sky-500/10 transition-all">
                    <p class="text-[11px] text-slate-500 font-medium">Batas FCR aman. Di atas batas ini, siklus akan ditandai perlu perhatian.</p>
                </div>

                <div class="bg-slate-50 p-4 rounded-2xl border border-slate-100 space-y-2">
                    <label class="block text-xs font-extrabold text-slate-800">Threshold Kematian Alarm (Ekor/Batch)</label>
                    <input type="number" name="threshold_kematian" value="{{ $settings['threshold_kematian'] }}" required
                           class="w-full px-4 py-2.5 rounded-xl bg-white border border-slate-200 text-xs font-bold text-slate-800 focus:outline-none focus:border-sky-500 focus:ring-2 focus:ring-sky-500/10 transition-all">
                    <p class="text-[11px] text-slate-500 font-medium">Jumlah kematian bibit yang akan memicu status alarm WASPADA pada batch.</p>
                </div>
            </div>

            <div class="flex items-center justify-end">
                <button type="submit" 
                        class="px-6 py-2.5 rounded-xl bg-[#051B44] hover:bg-[#09265c] text-white font-bold text-xs flex items-center gap-2 shadow-md shadow-[#051B44]/20 transition-all cursor-pointer">
                    <i class="fa-solid fa-floppy-disk"></i>
                    <span>Simpan Parameter Budidaya</span>
                </button>
            </div>
        </form>
    </div>

    <!-- TAB 4: NOTIFIKASI & SISTEM -->
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

            <div class="flex items-center justify-between p-4 rounded-2xl bg-slate-50 border border-slate-100">
                <div class="space-y-0.5">
                    <h4 class="text-xs font-extrabold text-slate-800">Alert WhatsApp Kematian Bibit</h4>
                    <p class="text-[11px] text-slate-500 font-medium">Kirim alert langsung ke WhatsApp manajer jika kematian bibit melampaui batas toleransi.</p>
                </div>
                <input type="checkbox" checked class="w-5 h-5 accent-[#051B44] rounded cursor-pointer">
            </div>
        </div>
    </div>

</div>
@endsection

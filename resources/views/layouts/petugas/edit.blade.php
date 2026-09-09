@extends('layouts.app')

@section('title', 'Edit Data Petugas - ' . ($user->nama ?? 'AMS BUDIDAYA'))

@section('content')
<div class="space-y-6 max-w-4xl mx-auto" x-data="{
    copied: false,
    copySecret(text) {
        if (!text) return;
        navigator.clipboard.writeText(text).then(() => {
            this.copied = true;
            setTimeout(() => this.copied = false, 2500);
        });
    }
}">
    <!-- Breadcrumb & Header Page -->
    <div class="space-y-1">
        <div class="flex items-center gap-2 text-xs font-semibold text-slate-400">
            <a href="{{ route('petugas') }}" class="hover:text-slate-600 transition-colors flex items-center gap-1.5">
                <i class="fa-solid fa-arrow-left text-xs"></i>
                <span>Kembali ke Manajemen Petugas</span>
            </a>
            <i class="fa-solid fa-chevron-right text-[10px]"></i>
            <span class="text-slate-600">Edit Petugas</span>
        </div>
        <h1 class="text-2xl font-extrabold text-[#0B2570] tracking-tight">Edit Data Petugas</h1>
        <p class="text-xs text-slate-500 font-medium">Perbarui informasi profil, nomor kontak, dan kelola Google Authenticator (2FA) petugas.</p>
    </div>

    @if(session('success'))
    <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-semibold flex items-center gap-2">
        <i class="fa-solid fa-circle-check text-emerald-600 text-sm"></i>
        <span>{{ session('success') }}</span>
    </div>
    @endif

    @if($errors->any())
    <div class="p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-xs font-semibold space-y-1">
        <div class="flex items-center gap-2 font-bold">
            <i class="fa-solid fa-circle-exclamation text-rose-600 text-sm"></i>
            <span>Terjadi kesalahan pada input:</span>
        </div>
        <ul class="list-disc list-inside space-y-0.5 text-rose-700 pl-2">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <!-- Edit Form Card Container -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs p-6 sm:p-8 space-y-6">
        
        <!-- Profile Header Avatar Banner -->
        <div class="flex items-center gap-4 pb-6 border-b border-slate-100">
            <div class="relative">
                <div class="w-16 h-16 rounded-2xl bg-gradient-to-tr from-sky-500 to-[#0B2570] text-white flex items-center justify-center font-black text-2xl shadow-md border-2 border-white">
                    {{ strtoupper(substr($user->nama ?? 'P', 0, 1)) }}
                </div>
                <div class="absolute bottom-0 right-0 w-4 h-4 bg-emerald-500 rounded-full border-2 border-white"></div>
            </div>
            <div>
                <div class="flex items-center gap-2.5">
                    <h3 class="text-lg font-extrabold text-slate-900">{{ $user->nama }}</h3>
                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold bg-emerald-100 text-emerald-700 uppercase">
                        Aktif
                    </span>
                </div>
                <span class="text-xs text-slate-400 font-medium">
                    @switch($user->role)
                        @case('pembibitan')
                            Teknisi Hatchery / Pembibitan
                            @break
                        @case('pembesaran')
                            Teknisi Pembesaran Ikan
                            @break
                        @case('petugas_distribusi')
                            Logistik & Distribusi Pengiriman
                            @break
                        @default
                            {{ ucfirst(str_replace('_', ' ', $user->role)) }}
                    @endswitch
                    • ID: #{{ $user->id_user }}
                </span>
            </div>
        </div>

        <!-- Inputs Form -->
        <form action="{{ route('petugas.update', $user->id_user) }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div>
                    <label class="text-[10px] font-extrabold uppercase tracking-wider text-slate-500 block mb-1.5">Nama Lengkap Petugas *</label>
                    <input type="text" 
                           name="nama"
                           value="{{ old('nama', $user->nama) }}" 
                           required
                           class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs font-semibold text-slate-800 bg-slate-50/70 focus:bg-white focus:outline-none focus:ring-2 focus:ring-sky-500 transition-all">
                </div>

                <div>
                    <label class="text-[10px] font-extrabold uppercase tracking-wider text-slate-500 block mb-1.5">Email Login Akun *</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                            <i class="fa-solid fa-envelope text-xs"></i>
                        </div>
                        <input type="email" 
                               name="email"
                               value="{{ old('email', $user->email) }}" 
                               required
                               class="w-full pl-9 pr-3.5 py-2.5 rounded-xl border border-slate-200 text-xs font-semibold text-slate-800 bg-slate-50/70 focus:bg-white focus:outline-none focus:ring-2 focus:ring-sky-500 transition-all">
                    </div>
                </div>

                <div>
                    <label class="text-[10px] font-extrabold uppercase tracking-wider text-slate-500 block mb-1.5">Peran / Divisi *</label>
                    <select name="role" required
                            class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs font-bold text-slate-700 bg-slate-50/70 focus:bg-white focus:outline-none focus:ring-2 focus:ring-sky-500 transition-all">
                        <option value="pembesaran" {{ old('role', $user->role) === 'pembesaran' ? 'selected' : '' }}>Teknisi Pembesaran Ikan</option>
                        <option value="pembibitan" {{ old('role', $user->role) === 'pembibitan' ? 'selected' : '' }}>Teknisi Hatchery / Pembibitan</option>
                        <option value="petugas_distribusi" {{ old('role', $user->role) === 'petugas_distribusi' ? 'selected' : '' }}>Logistik &amp; Distribusi Pengiriman</option>
                        <option value="manajer" {{ old('role', $user->role) === 'manajer' ? 'selected' : '' }}>Manajer Operasional</option>
                    </select>
                </div>

                <div>
                    <label class="text-[10px] font-extrabold uppercase tracking-wider text-slate-500 block mb-1.5">Nomor WhatsApp / Telp</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                            <i class="fa-solid fa-phone text-xs"></i>
                        </div>
                        <input type="text" 
                               name="no_tlp"
                               value="{{ old('no_tlp', $user->no_tlp) }}" 
                               placeholder="Contoh: 081234567890 atau +6281234567890"
                               class="w-full pl-9 pr-3.5 py-2.5 rounded-xl border border-slate-200 text-xs font-semibold text-slate-800 bg-slate-50/70 focus:bg-white focus:outline-none focus:ring-2 focus:ring-sky-500 transition-all">
                    </div>
                </div>
            </div>

            <!-- Card Google Authenticator 2FA Petugas -->
            <div class="p-5 sm:p-6 rounded-2xl bg-gradient-to-br from-slate-50 to-sky-50/50 border border-sky-100/80 shadow-xs space-y-4">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-3 border-b border-sky-100">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-sky-500 to-[#0B2570] text-white flex items-center justify-center shadow-xs">
                            <i class="fa-solid fa-shield-halved text-base"></i>
                        </div>
                        <div>
                            <h4 class="text-sm font-extrabold text-slate-900">Google Authenticator (2FA Setup)</h4>
                            <p class="text-[11px] text-slate-500 font-medium">Scan QR Code ini menggunakan aplikasi Google Authenticator untuk login.</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800 border border-emerald-200">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-600 animate-pulse"></span>
                            2FA Aktif
                        </span>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-5 items-center">
                    <!-- QR Code Display Box -->
                    <div class="flex flex-col items-center justify-center p-4 bg-white rounded-xl border border-slate-200/80 shadow-xs text-center">
                        <div class="p-2.5 bg-white rounded-lg border border-slate-100 shadow-inner flex items-center justify-center max-w-[140px] max-h-[140px]">
                            @if(!empty($user->two_factor_qr_code_svg))
                                <div class="w-full h-full flex items-center justify-center [&>svg]:w-full [&>svg]:h-auto [&>svg]:max-w-[120px]">
                                    {!! $user->two_factor_qr_code_svg !!}
                                </div>
                            @else
                                <div class="w-28 h-28 flex flex-col items-center justify-center text-slate-400 text-xs">
                                    <i class="fa-solid fa-qrcode text-3xl mb-1 text-slate-300"></i>
                                    <span>QR Belum Ada</span>
                                </div>
                            @endif
                        </div>
                        <span class="text-[10px] font-extrabold text-slate-400 mt-2 uppercase tracking-wider">Scan via Google Auth</span>
                    </div>

                    <!-- Secret Key Details -->
                    <div class="md:col-span-2 space-y-3">
                        <div>
                            <label class="text-[10px] font-extrabold uppercase tracking-wider text-slate-500 block mb-1">Kunci Rahasia Manual (Secret Key)</label>
                            <div class="flex items-center gap-2">
                                <div class="flex-1 px-3.5 py-2.5 bg-white border border-slate-200 rounded-xl font-mono text-xs font-bold text-slate-800 tracking-wider select-all overflow-x-auto truncate">
                                    {{ $user->two_factor_secret_decrypted ?? 'KUNCI-TIDAK-TERSEDIA' }}
                                </div>
                                <button type="button" 
                                        @click="copySecret('{{ $user->two_factor_secret_decrypted }}')"
                                        class="px-3 py-2.5 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 text-slate-600 text-xs font-bold transition-all flex items-center gap-1.5 shrink-0 shadow-xs active:scale-95">
                                    <i class="fa-solid" :class="copied ? 'fa-check text-emerald-600' : 'fa-copy'"></i>
                                    <span x-text="copied ? 'Tersalin!' : 'Salin'">Salin</span>
                                </button>
                            </div>
                        </div>

                        <div class="p-3 bg-sky-500/10 rounded-xl border border-sky-200/60 text-[11px] text-sky-900 leading-relaxed font-medium">
                            <i class="fa-solid fa-circle-info text-sky-600 mr-1"></i>
                            <strong>Petunjuk:</strong> Petugas cukup mendaftarkan QR Code atau Kode Kunci di atas ke aplikasi Google Authenticator sekali saja. Saat login di halaman OTP, petugas hanya perlu memasukkan 6 digit angka dari aplikasi.
                        </div>
                    </div>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="flex items-center justify-end gap-3 pt-6 border-t border-slate-100">
                <a href="{{ route('petugas') }}" 
                   class="px-5 py-2.5 rounded-xl border border-slate-200 text-xs font-bold text-slate-600 hover:bg-slate-50 transition-colors">
                    Batal
                </a>
                <button type="submit" 
                        class="px-6 py-2.5 rounded-xl bg-[#0B2570] hover:bg-[#081B52] text-white font-extrabold text-xs shadow-md shadow-sky-950/20 transition-all flex items-center gap-2">
                    <i class="fa-solid fa-floppy-disk"></i>
                    <span>Simpan Perubahan</span>
                </button>
            </div>
        </form>

    </div>
</div>
@endsection

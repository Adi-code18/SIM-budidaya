@extends('layouts.app')

@section('title', 'Manajemen Petugas & Keamanan - SIM-BUDIDAYA')

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
                <h1 class="text-2xl font-extrabold text-[#0B2570] tracking-tight">Manajemen Petugas &amp; Akses Keamanan</h1>
                <p class="text-xs text-slate-500 font-medium mt-0.5">Kelola akun staf operasional, ganti password, dan pantau status Google Authenticator (2FA).</p>
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
                            <th class="py-3.5 px-6 text-center">STATUS 2FA (AUTHENTICATOR)</th>
                            <th class="py-3.5 px-6">STATUS AKUN</th>
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
                                $avatars = [
                                    'https://images.unsplash.com/photo-1534528741775-53994a69daeb?auto=format&fit=crop&q=80&w=120',
                                    'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?auto=format&fit=crop&q=80&w=120',
                                    'https://images.unsplash.com/photo-1573496359142-b8d87734a5a2?auto=format&fit=crop&q=80&w=120',
                                    'https://images.unsplash.com/photo-1500648767791-00dcc994a43e?auto=format&fit=crop&q=80&w=120'
                                ];
                            @endphp
                            @foreach($users as $idx => $u)
                                @php
                                    $roleInfo = $roleBadges[$u->role] ?? ['bg' => 'bg-slate-100 text-slate-700', 'label' => ucfirst(str_replace('_', ' ', $u->role))];
                                    $avatar = $avatars[$idx % count($avatars)];
                                    $has2fa = !empty($u->two_factor_confirmed_at);
                                @endphp
                                <tr id="user-row-{{ $u->id_user }}" class="hover:bg-slate-50/60 transition-colors">
                                    <td class="py-4 px-6">
                                        <div class="flex items-center gap-3">
                                            <img src="{{ $avatar }}" alt="{{ $u->nama }}" class="w-10 h-10 rounded-full object-cover border border-slate-200 shrink-0">
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
                                        @if($has2fa)
                                            <span id="badge-2fa-{{ $u->id_user }}" class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-extrabold bg-emerald-50 text-emerald-700 border border-emerald-200 shadow-xs">
                                                <i class="fa-solid fa-shield-halved text-emerald-600"></i>
                                                <span>2FA Aktif</span>
                                            </span>
                                        @else
                                            <span id="badge-2fa-{{ $u->id_user }}" class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-extrabold bg-slate-100 text-slate-500 border border-slate-200">
                                                <i class="fa-solid fa-lock-open text-slate-400"></i>
                                                <span>Nonaktif</span>
                                            </span>
                                        @endif
                                    </td>
                                    <td class="py-4 px-6">
                                        <span class="px-2.5 py-1 rounded-full text-[10px] font-extrabold bg-emerald-100 text-emerald-700 uppercase">
                                            Aktif
                                        </span>
                                    </td>
                                    <td class="py-4 px-6 text-center">
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
                                                 class="absolute right-0 mt-2 w-52 rounded-xl bg-white border border-slate-200 shadow-xl py-1.5 z-50 text-left">
                                                
                                                <button @click="open = false; openEdit({{ json_encode($u) }})" class="w-full px-3.5 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-50 flex items-center gap-2.5">
                                                    <i class="fa-solid fa-pen-to-square text-sky-600 w-4"></i>
                                                    <span>Edit Data Profil</span>
                                                </button>

                                                <button @click="open = false; openSecurity({{ json_encode($u) }})" class="w-full px-3.5 py-2 text-xs font-semibold text-indigo-700 hover:bg-indigo-50/70 flex items-center gap-2.5">
                                                    <i class="fa-solid fa-key text-indigo-600 w-4"></i>
                                                    <span>Keamanan &amp; Sandi</span>
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
                                <td colspan="7" class="py-8 text-center text-slate-400">
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
                        <div class="flex items-center rounded-xl border border-slate-200 bg-slate-50/70 overflow-hidden focus-within:bg-white focus-within:ring-2 focus-within:ring-sky-500 focus-within:border-sky-500 transition-all">
                            <span class="px-3 py-2.5 text-xs font-bold text-slate-500 bg-slate-100/90 border-r border-slate-200 shrink-0 flex items-center gap-1.5">
                                <span>🇮🇩</span>
                                <span>+62</span>
                            </span>
                            <input type="tel" name="no_tlp" x-model="formCreate.no_tlp" placeholder="812-3456-7890 / 081234567890"
                                   class="w-full px-3.5 py-2.5 text-xs font-semibold text-slate-800 bg-transparent border-0 focus:outline-none">
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
                        <div class="flex items-center rounded-xl border border-slate-200 bg-slate-50/70 overflow-hidden focus-within:bg-white focus-within:ring-2 focus-within:ring-sky-500 focus-within:border-sky-500 transition-all">
                            <span class="px-3 py-2.5 text-xs font-bold text-slate-500 bg-slate-100/90 border-r border-slate-200 shrink-0 flex items-center gap-1.5">
                                <span>🇮🇩</span>
                                <span>+62</span>
                            </span>
                            <input type="tel" name="no_tlp" x-model="formEdit.no_tlp" placeholder="812-3456-7890 / 081234567890"
                                   class="w-full px-3.5 py-2.5 text-xs font-semibold text-slate-800 bg-transparent border-0 focus:outline-none">
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
    <!-- MODAL KEAMANAN & RESET PASSWORD / GOOGLE AUTHENTICATOR (2FA)              -->
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
             class="bg-white w-full max-w-lg rounded-3xl shadow-2xl border border-slate-100 p-6 sm:p-7 space-y-6 text-left">
            
            <!-- Header Modal -->
            <div class="flex items-center justify-between pb-4 border-b border-slate-100">
                <div class="flex items-center gap-3">
                    <div class="w-11 h-11 rounded-2xl bg-indigo-50 text-indigo-600 border border-indigo-100 flex items-center justify-center text-lg shadow-xs">
                        <i class="fa-solid fa-shield-keyhole"></i>
                    </div>
                    <div>
                        <h3 class="text-base font-extrabold text-slate-900">Keamanan &amp; Akses Akun</h3>
                        <p class="text-xs text-slate-500 font-medium" x-text="securityUser.nama + ' (' + securityUser.email + ')'"></p>
                    </div>
                </div>
                <button type="button" @click="securityModalOpen = false" class="w-8 h-8 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-500 flex items-center justify-center transition-colors">
                    <i class="fa-solid fa-xmark text-xs"></i>
                </button>
            </div>

            <!-- Section 1: Status Google Authenticator 2FA -->
            <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200/80 space-y-3">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2 text-xs font-extrabold text-slate-800">
                        <i class="fa-solid fa-mobile-screen-button text-indigo-600"></i>
                        <span>Google Authenticator (2FA)</span>
                    </div>
                    <template x-if="securityUser.has_2fa">
                        <span class="px-2.5 py-1 rounded-full text-[10px] font-extrabold bg-emerald-100 text-emerald-800 flex items-center gap-1">
                            <i class="fa-solid fa-circle-check text-emerald-600"></i>
                            <span>Aktif</span>
                        </span>
                    </template>
                    <template x-if="!securityUser.has_2fa">
                        <span class="px-2.5 py-1 rounded-full text-[10px] font-extrabold bg-slate-200 text-slate-600">
                            Nonaktif
                        </span>
                    </template>
                </div>
                <p class="text-[11px] text-slate-500 leading-relaxed">
                    Petugas mengamankan login mobile web menggunakan kode 6-digit Google Authenticator. Jika ponsel petugas hilang/berganti, Anda dapat meresetnya di sini.
                </p>
                <template x-if="securityUser.has_2fa">
                    <div class="pt-1">
                        <button type="button" @click="reset2fa()" :disabled="isResetting2fa"
                                class="px-3 py-1.5 rounded-xl bg-amber-500 hover:bg-amber-600 active:scale-[0.99] text-white font-extrabold text-[11px] transition-all flex items-center gap-1.5 shadow-xs disabled:opacity-50">
                            <i class="fa-solid text-[10px]" :class="isResetting2fa ? 'fa-spinner fa-spin' : 'fa-arrows-rotate'"></i>
                            <span x-text="isResetting2fa ? 'Mereset 2FA...' : 'Reset / Nonaktifkan 2FA'"></span>
                        </button>
                    </div>
                </template>
            </div>

            <!-- Section 2: Ganti / Reset Password Petugas -->
            <form action="#" method="POST" @submit.prevent="submitPassword()" class="space-y-4">
                <div class="flex items-center gap-2 text-xs font-extrabold text-slate-800 pt-1">
                    <i class="fa-solid fa-key text-sky-600"></i>
                    <span>Ganti / Reset Kata Sandi Petugas</span>
                </div>

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
                        Tutup
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
        selectedUser: null,
        securityUser: { id_user: null, nama: '', email: '', role: '', has_2fa: false },
        formPassword: { password: '', password_confirmation: '' },
        isDeleting: false,
        isUpdatingPassword: false,
        isResetting2fa: false,
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
            password: ''
        },

        openEdit(user) {
            this.formEdit = {
                id_user: user.id_user,
                nama: user.nama,
                email: user.email,
                role: user.role,
                no_tlp: user.no_tlp || '',
                password: ''
            };
            this.activeTab = 'edit';
        },

        openSecurity(user) {
            this.securityUser = {
                id_user: user.id_user,
                nama: user.nama,
                email: user.email,
                role: user.role,
                has_2fa: !!user.two_factor_confirmed_at
            };
            this.formPassword = { password: '', password_confirmation: '' };
            this.securityModalOpen = true;
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

        async reset2fa() {
            if (!confirm('Yakin ingin mereset/menonaktifkan Google Authenticator 2FA untuk ' + this.securityUser.nama + '?')) return;
            this.isResetting2fa = true;
            try {
                const res = await fetch('{{ url('/petugas') }}/' + this.securityUser.id_user + '/reset-2fa', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                });
                const data = await res.json();
                if (res.ok && data.success) {
                    this.securityUser.has_2fa = false;
                    this.triggerToast(data.message || '2FA berhasil direset!', 'success');
                    const badgeEl = document.getElementById('badge-2fa-' + this.securityUser.id_user);
                    if (badgeEl) {
                        badgeEl.className = 'inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-extrabold bg-slate-100 text-slate-500 border border-slate-200';
                        badgeEl.innerHTML = '<i class="fa-solid fa-lock-open text-slate-400"></i> <span>Nonaktif</span>';
                    }
                } else {
                    this.triggerToast(data.message || 'Gagal mereset 2FA.', 'error');
                }
            } catch(e) {
                this.triggerToast('Terjadi kesalahan koneksi server.', 'error');
            } finally {
                this.isResetting2fa = false;
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

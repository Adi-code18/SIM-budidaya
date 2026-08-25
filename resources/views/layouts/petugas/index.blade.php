@extends('layouts.app')

@section('title', 'Manajemen Petugas - SIM-BUDIDAYA')

@section('content')
<div class="space-y-6" x-data="{
    activeTab: 'daftar',
    deleteModalOpen: false,
    selectedUser: null,
    isDeleting: false,
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
}">

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
                <h1 class="text-2xl font-extrabold text-[#0B2570] tracking-tight">Manajemen Petugas</h1>
                <p class="text-xs text-slate-500 font-medium mt-0.5">Kelola data petugas lapangan, teknisi, dan staf operasional.</p>
            </div>
            <div>
                <button @click="activeTab = 'create'"
                        class="px-4 py-2.5 rounded-xl bg-[#031B4E] hover:bg-navy-900 text-white font-extrabold text-xs shadow-md shadow-sky-950/20 transition-all flex items-center gap-2">
                    <i class="fa-solid fa-plus text-xs"></i>
                    <span>Tambah Petugas Baru</span>
                </button>
            </div>
        </div>

        <!-- Filter Bar -->
        <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-xs flex flex-col md:flex-row items-center gap-3">
            <!-- Search Field -->
            <div class="relative flex-1 w-full">
                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                    <i class="fa-solid fa-magnifying-glass text-xs"></i>
                </div>
                <input type="text"
                       placeholder="Cari nama atau email..."
                       class="w-full pl-9 pr-3.5 py-2.5 rounded-xl border border-slate-200 text-xs font-medium text-slate-700 bg-slate-50/70 focus:bg-white focus:outline-none focus:ring-2 focus:ring-sky-500 transition-all">
            </div>

            <!-- Filter Dropdown 1: Semua Peran -->
            <div class="w-full md:w-48">
                <select class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs font-bold text-slate-700 bg-slate-50/70 focus:bg-white focus:outline-none focus:ring-2 focus:ring-sky-500 transition-all cursor-pointer">
                    <option value="">Semua Peran</option>
                    <option value="logistik">Logistik Pasar</option>
                    <option value="teknisi">Petugas kolam pembesaran</option>
                    <option value="pengawas">Pengawas Pembibitan</option>
                    <option value="manajer">Manajer Operasional</option>
                </select>
            </div>

            <!-- Filter Dropdown 2: Semua Status -->
            <div class="w-full md:w-44">
                <select class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs font-bold text-slate-700 bg-slate-50/70 focus:bg-white focus:outline-none focus:ring-2 focus:ring-sky-500 transition-all cursor-pointer">
                    <option value="">Semua Status</option>
                    <option value="aktif">Aktif</option>
                    <option value="cuti">Cuti</option>
                    <option value="nonaktif">Nonaktif</option>
                </select>
            </div>

            <!-- Filter Action Button -->
            <button class="w-full md:w-auto px-5 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-extrabold text-xs transition-colors flex items-center justify-center gap-2">
                <i class="fa-solid fa-filter text-xs"></i>
                <span>Filter</span>
            </button>
        </div>

        <!-- Table Card -->
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-visible">
            <div class="overflow-x-auto overflow-y-visible">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50/70 border-b border-slate-100 text-[10px] font-extrabold text-slate-400 uppercase tracking-wider">
                            <th class="py-3.5 px-6">FOTO &amp; NAMA PETUGAS</th>
                            <th class="py-3.5 px-6">PERAN</th>
                            <th class="py-3.5 px-6">EMAIL / TELP</th>
                            <th class="py-3.5 px-6">TGL MASUK</th>
                            <th class="py-3.5 px-6">TGL OUT</th>
                            <th class="py-3.5 px-6">KETERANGAN</th>
                            <th class="py-3.5 px-6">STATUS</th>
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
                                    $latestLibur = $u->pengajuanLibur ? $u->pengajuanLibur->first() : null;
                                    $statusPetugas = $latestLibur && $latestLibur->status === 'disetujui' ? 'Cuti' : 'Aktif';
                                @endphp
                                <tr id="user-row-{{ $u->id_user }}" class="hover:bg-slate-50/50 transition-colors">
                                    <td class="py-4 px-6">
                                        <div class="flex items-center gap-3">
                                            <img src="{{ $avatar }}"
                                                 alt="{{ $u->nama }}"
                                                 class="w-10 h-10 rounded-full object-cover border border-slate-200 shadow-xs">
                                            <div>
                                                <h4 class="font-extrabold text-slate-900 text-xs">{{ $u->nama }}</h4>
                                                <span class="text-[10px] text-slate-400 block font-normal">{{ $u->email }}</span>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="py-4 px-6">
                                        <span class="px-2.5 py-1 rounded-full text-[10px] font-extrabold {{ $roleInfo['bg'] }} uppercase">
                                            {{ $roleInfo['label'] }}
                                        </span>
                                    </td>
                                    <td class="py-4 px-6 font-semibold text-slate-600">
                                        {{ $u->no_hp ?? '081234567890' }}
                                    </td>
                                    <td class="py-4 px-6 text-slate-500">
                                        {{ $u->created_at ? $u->created_at->translatedFormat('d M Y') : '01 Jan 2024' }}
                                    </td>
                                    <td class="py-4 px-6 text-slate-400">
                                        -
                                    </td>
                                    <td class="py-4 px-6 text-slate-600 font-semibold">
                                        {{ $statusPetugas }}
                                    </td>
                                    <td class="py-4 px-6">
                                        <span class="px-2.5 py-1 rounded-full text-[10px] font-extrabold {{ $statusPetugas === 'Cuti' ? 'bg-amber-100 text-amber-700' : 'bg-emerald-100 text-emerald-700' }} uppercase">
                                            {{ $statusPetugas }}
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
                                                 class="absolute right-0 mt-2 w-48 rounded-xl bg-white border border-slate-200 shadow-xl py-1.5 z-50 text-left">
                                                <button @click="open = false; openEdit({{ json_encode($u) }})" class="w-full px-3.5 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-50 flex items-center gap-2.5">
                                                    <i class="fa-solid fa-pen-to-square text-sky-600 w-4"></i>
                                                    <span>Edit Data</span>
                                                </button>
                                                <a href="{{ route('petugas.libur.approval') }}" class="w-full px-3.5 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-50 flex items-center gap-2.5">
                                                    <i class="fa-solid fa-calendar-check text-emerald-600 w-4"></i>
                                                    <span>Tinjau Persetujuan Libur</span>
                                                </a>
                                                <a href="{{ route('petugas.libur.ajukan') }}" class="w-full px-3.5 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-50 flex items-center gap-2.5">
                                                    <i class="fa-solid fa-calendar-plus text-amber-600 w-4"></i>
                                                    <span>Ajukan Cuti / Libur</span>
                                                </a>
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
                        @endif
                    </tbody>
                </table>
            </div>

            <!-- Table Footer & Pagination -->
            <div class="p-4 border-t border-slate-100 flex flex-col sm:flex-row items-center justify-between gap-4 text-xs font-medium text-slate-500">
                <span>Menampilkan 1 hingga 3 dari 12 petugas</span>
                <div class="flex items-center gap-1">
                    <button class="w-7 h-7 rounded border border-slate-200 flex items-center justify-center text-slate-400 hover:bg-slate-50">&lt;</button>
                    <button class="w-7 h-7 rounded bg-[#031B4E] text-white font-bold flex items-center justify-center">1</button>
                    <button class="w-7 h-7 rounded border border-slate-200 flex items-center justify-center text-slate-600 hover:bg-slate-50">2</button>
                    <button class="w-7 h-7 rounded border border-slate-200 flex items-center justify-center text-slate-600 hover:bg-slate-50">3</button>
                    <button class="w-7 h-7 rounded border border-slate-200 flex items-center justify-center text-slate-400 hover:bg-slate-50">&gt;</button>
                </div>
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
                <h1 class="text-2xl font-extrabold text-[#0B2570] tracking-tight">Tambah Petugas Baru</h1>
                <p class="text-xs text-slate-500 font-medium mt-0.5">Lengkapi informasi di bawah untuk menambahkan staf baru.</p>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
            <div class="lg:col-span-8 space-y-6">
                <!-- Card 1: Informasi Personal -->
                <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-xs space-y-5">
                    <div class="flex items-center gap-2 text-xs font-bold text-[#0B2570] pb-3 border-b border-slate-100">
                        <i class="fa-solid fa-user-gear text-sky-600 text-sm"></i>
                        <span>Informasi Personal</span>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="text-[10px] font-extrabold uppercase tracking-wider text-slate-500 block mb-1.5">Full Name *</label>
                            <input type="text" value="Ahmad Rifat Septian" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs font-semibold text-slate-800 bg-slate-50/70 focus:bg-white focus:outline-none focus:ring-2 focus:ring-sky-500 transition-all">
                        </div>
                        <div>
                            <label class="text-[10px] font-extrabold uppercase tracking-wider text-slate-500 block mb-1.5">Gaji Per Bulan (IDR) *</label>
                            <input type="text" value="IDR 3.750.000,00" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs font-semibold text-slate-800 bg-slate-50/70 focus:bg-white focus:outline-none focus:ring-2 focus:ring-sky-500 transition-all">
                        </div>
                        <div>
                            <label class="text-[10px] font-extrabold uppercase tracking-wider text-slate-500 block mb-1.5">Email Aktif *</label>
                            <input type="email" value="rifat23@gmail.com" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs font-semibold text-slate-800 bg-slate-50/70 focus:bg-white focus:outline-none focus:ring-2 focus:ring-sky-500 transition-all">
                        </div>
                        <div>
                            <label class="text-[10px] font-extrabold uppercase tracking-wider text-slate-500 block mb-1.5">Phone Number *</label>
                            <input type="text" value="081234567890" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs font-semibold text-slate-800 bg-slate-50/70 focus:bg-white focus:outline-none focus:ring-2 focus:ring-sky-500 transition-all">
                        </div>
                    </div>
                </div>

                <!-- Card 2: Pengalaman Kerja -->
                <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-xs space-y-5">
                    <div class="flex items-center gap-2 text-xs font-bold text-[#0B2570] pb-3 border-b border-slate-100">
                        <i class="fa-solid fa-briefcase text-sky-600 text-sm"></i>
                        <span>Pengalaman Kerja</span>
                    </div>

                    <div class="space-y-4">
                        <div>
                            <label class="text-[10px] font-extrabold uppercase tracking-wider text-slate-500 block mb-1.5">Pilih Jabatan *</label>
                            <select class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs font-semibold text-slate-800 bg-slate-50/70 focus:bg-white focus:outline-none focus:ring-2 focus:ring-sky-500 transition-all cursor-pointer">
                                <option value="logistik">Logistik Pasar</option>
                                <option value="teknisi">Teknisi Kolam Pembesaran</option>
                                <option value="pengawas">Pengawas Pembibitan</option>
                                <option value="manajer">Manajer Operasional</option>
                            </select>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="text-[10px] font-extrabold uppercase tracking-wider text-slate-500 block mb-1.5">Status Pegawai *</label>
                                <select class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs font-semibold text-slate-800 bg-slate-50/70 focus:bg-white focus:outline-none focus:ring-2 focus:ring-sky-500 transition-all cursor-pointer">
                                    <option value="kontrak" selected>Kontrak</option>
                                    <option value="tetap">Tetap</option>
                                </select>
                            </div>
                            <div>
                                <label class="text-[10px] font-extrabold uppercase tracking-wider text-slate-500 block mb-1.5">Status *</label>
                                <select class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs font-semibold text-slate-800 bg-slate-50/70 focus:bg-white focus:outline-none focus:ring-2 focus:ring-sky-500 transition-all cursor-pointer">
                                    <option value="aktif" selected>Aktif</option>
                                    <option value="cuti">Cuti</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Action Card -->
            <div class="lg:col-span-4 space-y-6">
                <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-xs space-y-4">
                    <div class="flex items-center gap-2 text-xs font-bold text-[#0B2570] pb-3 border-b border-slate-100">
                        <i class="fa-solid fa-sliders text-sky-600 text-sm"></i>
                        <span>Tindakan</span>
                    </div>

                    <div class="space-y-3 pt-2">
                        <button type="button" @click="activeTab = 'daftar'" class="w-full py-3 rounded-xl bg-[#031B4E] hover:bg-navy-900 text-white font-extrabold text-xs shadow-md shadow-sky-950/20 transition-all flex items-center justify-center gap-2">
                            <i class="fa-solid fa-user-check"></i>
                            <span>Simpan &amp; Buat Akun</span>
                        </button>
                        <button type="button" @click="activeTab = 'daftar'" class="w-full py-3 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 text-slate-600 font-bold text-xs transition-colors block text-center">
                            Batal
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ========================================================================= -->
    <!-- TAB 3: FORM EDIT DATA PETUGAS                                             -->
    <!-- ========================================================================= -->
    <div x-show="activeTab === 'edit'"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 translate-y-1"
         x-transition:enter-end="opacity-100 translate-y-0"
         class="space-y-6 max-w-4xl mx-auto">

        <div class="space-y-1">
            <div class="flex items-center gap-2 text-xs font-semibold text-slate-400">
                <button @click="activeTab = 'daftar'" class="hover:text-slate-600 transition-colors">Manajemen Petugas</button>
                <i class="fa-solid fa-chevron-right text-[10px]"></i>
                <span class="text-slate-600">Edit Data Petugas</span>
            </div>
            <h1 class="text-2xl font-extrabold text-[#0B2570] tracking-tight">Edit Data Petugas</h1>
            <p class="text-xs text-slate-500 font-medium">Update information for staff Ahmad Rifat</p>
        </div>

        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs p-6 sm:p-8 space-y-6">
            <!-- Profile Banner -->
            <div class="flex items-center gap-4 pb-6 border-b border-slate-100">
                <div class="relative">
                    <img src="https://images.unsplash.com/photo-1534528741775-53994a69daeb?auto=format&fit=crop&q=80&w=160"
                         alt="Ahmad Rifat"
                         class="w-16 h-16 rounded-full object-cover border-2 border-sky-500/20 shadow-xs">
                    <div class="absolute bottom-0 right-0 w-4 h-4 bg-emerald-500 rounded-full border-2 border-white"></div>
                </div>
                <div>
                    <div class="flex items-center gap-2.5">
                        <h3 class="text-lg font-extrabold text-slate-900">Ahmad Rifat</h3>
                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold bg-emerald-100 text-emerald-700 uppercase">
                            Aktif
                        </span>
                    </div>
                    <span class="text-xs text-slate-400 font-medium">Logistik Pasar • ID: BUS-STF-088</span>
                </div>
            </div>

            <!-- Form -->
            <form action="#" method="POST" @submit.prevent="activeTab = 'daftar'" class="space-y-5">
                <div>
                    <label class="text-[10px] font-extrabold uppercase tracking-wider text-slate-500 block mb-1.5">Full Name</label>
                    <input type="text" value="Ahmad Rifat" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs font-semibold text-slate-800 bg-slate-50/70 focus:bg-white focus:outline-none focus:ring-2 focus:ring-sky-500 transition-all">
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="text-[10px] font-extrabold uppercase tracking-wider text-slate-500 block mb-1.5">Phone Number</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                                <i class="fa-solid fa-phone text-xs"></i>
                            </div>
                            <input type="text" value="+62 812-3456-7890" class="w-full pl-9 pr-3.5 py-2.5 rounded-xl border border-slate-200 text-xs font-semibold text-slate-800 bg-slate-50/70 focus:bg-white focus:outline-none focus:ring-2 focus:ring-sky-500 transition-all">
                        </div>
                    </div>

                    <div>
                        <label class="text-[10px] font-extrabold uppercase tracking-wider text-slate-500 block mb-1.5">Email Address</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                                <i class="fa-solid fa-envelope text-xs"></i>
                            </div>
                            <input type="email" value="ahmad.rifat@agribisnis-budidaya.co.id" class="w-full pl-9 pr-3.5 py-2.5 rounded-xl border border-slate-200 text-xs font-semibold text-slate-800 bg-slate-50/70 focus:bg-white focus:outline-none focus:ring-2 focus:ring-sky-500 transition-all">
                        </div>
                    </div>
                </div>

                <div class="flex items-center justify-end gap-3 pt-6 border-t border-slate-100">
                    <button type="button" @click="activeTab = 'daftar'" class="px-5 py-2.5 rounded-xl border border-slate-200 text-xs font-bold text-slate-600 hover:bg-slate-50 transition-colors">
                        Cancel
                    </button>
                    <button type="submit" class="px-6 py-2.5 rounded-xl bg-[#031B4E] hover:bg-navy-900 text-white font-extrabold text-xs shadow-md shadow-sky-950/20 transition-all">
                        Save Changes
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- ========================================================================= -->
    <!-- TAB 4: TINJAU PERSETUJUAN LIBUR (STATUS DISETUJUI ONLY)                   -->
    <!-- ========================================================================= -->
    <div x-show="activeTab === 'approval'"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 translate-y-1"
         x-transition:enter-end="opacity-100 translate-y-0"
         class="space-y-6 max-w-4xl mx-auto">

        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <div class="flex items-center gap-2 text-xs font-semibold text-slate-400 mb-1">
                    <button @click="activeTab = 'daftar'" class="hover:text-slate-600 transition-colors">Manajemen Petugas</button>
                    <i class="fa-solid fa-chevron-right text-[10px]"></i>
                    <span class="text-slate-600">Tinjau Persetujuan Libur</span>
                </div>
                <h1 class="text-2xl font-extrabold text-[#0B2570] tracking-tight">Tinjau Persetujuan Libur</h1>
                <p class="text-xs text-slate-500 font-medium mt-0.5">Detail informasi persetujuan izin dan durasi libur staf.</p>
            </div>

            <div>
                <span class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full text-xs font-extrabold bg-emerald-50 text-emerald-700 border border-emerald-200 shadow-xs">
                    <i class="fa-solid fa-circle-check text-emerald-600"></i>
                    <span>Cuti</span>
                </span>
            </div>
        </div>

        <!-- Detail Persetujuan Libur Card -->
        <div class="bg-white p-6 sm:p-8 rounded-2xl border border-slate-200/80 shadow-xs space-y-6">
            <div class="flex items-center gap-2 text-xs font-bold text-[#0B2570] pb-3 border-b border-slate-100">
                <i class="fa-solid fa-id-card text-sky-600 text-sm"></i>
                <span>Informasi Pemohon &amp; Status Libur</span>
            </div>

            <!-- Profile Detail Row -->
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 p-4 rounded-xl bg-slate-50 border border-slate-100">
                <div class="flex items-center gap-4">
                    <img src="https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?auto=format&fit=crop&q=80&w=160" alt="Budi Santoso" class="w-14 h-14 rounded-full object-cover border-2 border-white shadow-xs">
                    <div>
                        <div class="flex items-center gap-2">
                            <h3 class="text-base font-extrabold text-slate-900">Budi Santoso</h3>
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-emerald-100 text-emerald-700 uppercase">Cuti</span>
                        </div>
                        <p class="text-xs text-slate-500 font-medium">Teknisi Kolam Pembesaran - Sektor A</p>
                    </div>
                </div>

                <div class="flex items-center gap-2 text-xs">
                    <span class="px-2.5 py-1 rounded-lg bg-white border border-slate-200 text-slate-600 font-bold">ID: EMP-2023-047</span>
                    <span class="px-2.5 py-1 rounded-lg bg-white border border-slate-200 text-slate-600 font-bold">Bergabung: 01 Jan 2022</span>
                </div>
            </div>

            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 pt-2">
                <div class="p-3.5 rounded-xl bg-slate-50/70 border border-slate-100">
                    <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400 block mb-1">JENIS IZIN</span>
                    <div class="flex items-center gap-2 text-xs font-extrabold text-slate-800">
                        <i class="fa-solid fa-plane-departure text-sky-600"></i>
                        <span>Cuti Tahunan</span>
                    </div>
                </div>

                <div class="p-3.5 rounded-xl bg-slate-50/70 border border-slate-100">
                    <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400 block mb-1">DURASI LIBUR</span>
                    <div class="flex items-center gap-2 text-xs font-extrabold text-slate-800">
                        <i class="fa-regular fa-clock text-sky-600"></i>
                        <span>3 Hari Kerja</span>
                    </div>
                </div>

                <div class="p-3.5 rounded-xl bg-slate-50/70 border border-slate-100">
                    <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400 block mb-1">TANGGAL MULAI</span>
                    <div class="flex items-center gap-2 text-xs font-extrabold text-slate-800">
                        <i class="fa-regular fa-calendar-check text-emerald-600"></i>
                        <span>12 Oktober 2026</span>
                    </div>
                </div>

                <div class="p-3.5 rounded-xl bg-slate-50/70 border border-slate-100">
                    <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400 block mb-1">TANGGAL SELESAI</span>
                    <div class="flex items-center gap-2 text-xs font-extrabold text-slate-800">
                        <i class="fa-regular fa-calendar-xmark text-sky-600"></i>
                        <span>14 Oktober 2026</span>
                    </div>
                </div>
            </div>

            <div>
                <label class="text-[10px] font-extrabold uppercase tracking-wider text-slate-500 block mb-1.5">Alasan &amp; Keterangan Libur</label>
                <div class="p-4 rounded-xl bg-slate-50/80 border border-slate-200 text-xs font-medium text-slate-700 leading-relaxed">
                    "Mengikuti acara pernikahan keluarga di Sumedang, jadwal pemeliharaan kolam sektor A sudah diserahkan sementara ke Sdr. Fajar untuk penanganan harian."
                </div>
            </div>

            <div class="flex items-center justify-end pt-4 border-t border-slate-100">
                <button type="button" @click="activeTab = 'daftar'" class="px-5 py-2.5 rounded-xl bg-[#031B4E] hover:bg-navy-900 text-white font-extrabold text-xs shadow-md shadow-sky-950/20 transition-all flex items-center gap-2">
                    <i class="fa-solid fa-arrow-left text-xs"></i>
                    <span>Kembali ke Data Petugas</span>
                </button>
            </div>
        </div>
    </div>

    <!-- ========================================================================= -->
    <!-- TAB 5: FORM AJUKAN LIBUR PETUGAS                                         -->
    <!-- ========================================================================= -->
    <div x-show="activeTab === 'ajukan'"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 translate-y-1"
         x-transition:enter-end="opacity-100 translate-y-0"
         class="space-y-6 max-w-4xl mx-auto">

        <div class="space-y-1">
            <div class="flex items-center gap-2 text-xs font-semibold text-slate-400">
                <button @click="activeTab = 'daftar'" class="hover:text-slate-600 transition-colors">Manajemen Petugas</button>
                <i class="fa-solid fa-chevron-right text-[10px]"></i>
                <span class="text-slate-600">Form Ajukan Libur</span>
            </div>
            <h1 class="text-2xl font-extrabold text-[#0B2570] tracking-tight">Form Ajukan Libur</h1>
            <p class="text-xs text-slate-500 font-medium">Isi formulir di bawah ini untuk mengajukan jadwal libur atau izin kerja lapangan.</p>
        </div>

        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs p-6 sm:p-8 space-y-6">
            <div class="flex items-center gap-2 text-xs font-bold text-[#0B2570] pb-3 border-b border-slate-100">
                <i class="fa-solid fa-file-pen text-sky-600 text-sm"></i>
                <span>DETAIL PENGAJUAN</span>
            </div>

            <form action="#" method="POST" @submit.prevent="activeTab = 'daftar'" class="space-y-5">
                <div>
                    <label class="text-[10px] font-extrabold uppercase tracking-wider text-slate-500 block mb-1.5">AKUN PETUGAS</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                            <i class="fa-solid fa-shield-halved text-xs"></i>
                        </div>
                        <input type="text" value="BUS-CUTI-012" readonly class="w-full pl-9 pr-3.5 py-2.5 rounded-xl border border-slate-200 text-xs font-extrabold text-slate-700 bg-slate-100 cursor-not-allowed">
                    </div>
                    <p class="text-[10px] text-slate-400 font-medium mt-1">Permohonan izin cuti akan dicatat atas nama akun ini.</p>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="text-[10px] font-extrabold uppercase tracking-wider text-slate-500 block mb-1.5">Tanggal Mulai *</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                                <i class="fa-regular fa-calendar text-xs"></i>
                            </div>
                            <input type="text" value="17/08/2026" placeholder="dd/mm/yyyy" class="w-full pl-9 pr-3.5 py-2.5 rounded-xl border border-slate-200 text-xs font-semibold text-slate-800 bg-slate-50/70 focus:bg-white focus:outline-none focus:ring-2 focus:ring-sky-500 transition-all">
                        </div>
                    </div>

                    <div>
                        <label class="text-[10px] font-extrabold uppercase tracking-wider text-slate-500 block mb-1.5">Tanggal Selesai *</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                                <i class="fa-regular fa-calendar text-xs"></i>
                            </div>
                            <input type="text" value="19/08/2026" placeholder="dd/mm/yyyy" class="w-full pl-9 pr-3.5 py-2.5 rounded-xl border border-slate-200 text-xs font-semibold text-slate-800 bg-slate-50/70 focus:bg-white focus:outline-none focus:ring-2 focus:ring-sky-500 transition-all">
                        </div>
                    </div>
                </div>

                <div>
                    <label class="text-[10px] font-extrabold uppercase tracking-wider text-slate-500 block mb-1.5">Keterangan / Alasan *</label>
                    <textarea rows="4" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs font-semibold text-slate-800 bg-slate-50/70 focus:bg-white focus:outline-none focus:ring-2 focus:ring-sky-500 transition-all">Izin menghadiri pernikahan saudara dengan alasan...</textarea>
                </div>

                <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
                    <button type="button" @click="activeTab = 'daftar'" class="px-5 py-2.5 rounded-xl border border-slate-200 text-xs font-bold text-slate-600 hover:bg-slate-50 transition-colors">
                        Batal
                    </button>
                    <button type="submit" class="px-6 py-2.5 rounded-xl bg-[#031B4E] hover:bg-navy-900 text-white font-extrabold text-xs shadow-md shadow-sky-950/20 transition-all flex items-center gap-2">
                        <span>Kirim Pengajuan</span>
                        <i class="fa-solid fa-paper-plane text-xs"></i>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- ========================================================================= -->
    <!-- CUSTOM CONFIRMATION MODAL (MODERN ALERT)                                  -->
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
            
            <!-- Warning Icon -->
            <div class="w-16 h-16 rounded-2xl bg-rose-50 text-rose-600 border border-rose-100 mx-auto flex items-center justify-center text-2xl shadow-xs">
                <i class="fa-solid fa-triangle-exclamation"></i>
            </div>

            <div class="space-y-1.5">
                <h3 class="text-lg font-extrabold text-slate-900">Hapus Data Petugas?</h3>
                <p class="text-xs text-slate-500 font-medium leading-relaxed">
                    Apakah Anda yakin ingin menghapus petugas <strong class="text-slate-800" x-text="selectedUser?.nama"></strong>? Seluruh catatan akun dan penugasan terkait akan dihapus dari sistem.
                </p>
            </div>

            <!-- Action Buttons -->
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

@extends('layouts.app')

@section('title', 'Manajemen Kolam & Operasional - AMS BUDIDAYA')

@section('content')
<div class="space-y-6" x-data="kolamManagementComponent()">

    <!-- Breadcrumb -->
    <div>
        <div class="flex items-center gap-2 text-xs font-medium text-slate-400">
            <span>Budidaya &amp; Operasional</span>
            <i class="fa-solid fa-chevron-right text-[10px]"></i>
            <span class="text-slate-700 font-bold">Manajemen Kolam</span>
        </div>
    </div>

    <!-- Header & Primary Action -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2.5">
                <div class="w-10 h-10 rounded-2xl bg-[#0284C7]/10 text-[#0284C7] flex items-center justify-center text-lg font-black shrink-0">
                    <i class="fa-solid fa-layer-group"></i>
                </div>
                <div>
                    <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">Manajemen Kolam</h1>
                    <p class="text-xs sm:text-sm text-slate-500 font-medium">Kelola data kolam budidaya, kapasitas, penanggung jawab, dan investasi infrastruktur tercatat ke Keuangan.</p>
                </div>
            </div>
        </div>
        <div>
            <button @click="openCreateModal()" 
                    class="px-5 py-3 rounded-2xl bg-[#031B4E] hover:bg-sky-900 active:scale-[0.99] text-white font-extrabold text-xs sm:text-sm shadow-md shadow-sky-950/20 transition-all flex items-center justify-center gap-2 cursor-pointer">
                <i class="fa-solid fa-circle-plus text-sm text-sky-300"></i>
                <span>Tambah Kolam Baru</span>
            </button>
        </div>
    </div>

    <!-- Overview Stats Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        
        <!-- Total Kolam & Kapasitas -->
        <div class="bg-white p-5 rounded-3xl border border-slate-200/90 shadow-xs flex items-center gap-4">
            <div class="w-12 h-12 rounded-2xl bg-sky-50 text-sky-600 flex items-center justify-center shrink-0 text-xl border border-sky-100">
                <i class="fa-solid fa-water"></i>
            </div>
            <div>
                <p class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400">Total Unit Kolam</p>
                <h3 class="text-xl font-black text-slate-900 mt-0.5">
                    {{ $stats['totalKolamCount'] ?? count($kolams) }} <span class="text-xs font-bold text-slate-500">Kolam</span>
                </h3>
                <p class="text-[11px] text-slate-400 font-medium">Kapasitas: {{ $stats['totalKapasitas'] ?? 0 }} ekor</p>
            </div>
        </div>

        <!-- Total Investasi Kolam (Tercatat di Keuangan) -->
        <div class="bg-white p-5 rounded-3xl border border-slate-200/90 shadow-xs flex items-center gap-4">
            <div class="w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0 text-xl border border-emerald-100">
                <i class="fa-solid fa-coins"></i>
            </div>
            <div>
                <p class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400">Nilai Investasi Kolam</p>
                <h3 class="text-xl font-black text-emerald-700 mt-0.5">
                    {{ $stats['totalInvestasiRp'] ?? 'Rp 0' }}
                </h3>
                <span class="inline-flex items-center gap-1 text-[10px] font-bold text-emerald-600">
                    <i class="fa-solid fa-circle-check text-[9px]"></i> Tercatat di Keuangan
                </span>
            </div>
        </div>

        <!-- Total Benih Aktif -->
        <div class="bg-white p-5 rounded-3xl border border-slate-200/90 shadow-xs flex items-center gap-4">
            <div class="w-12 h-12 rounded-2xl bg-indigo-50 text-indigo-600 flex items-center justify-center shrink-0 text-xl border border-indigo-100">
                <i class="fa-solid fa-fish"></i>
            </div>
            <div>
                <p class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400">Populasi Benih Aktif</p>
                <h3 class="text-xl font-black text-slate-900 mt-0.5">
                    {{ $stats['totalBenih'] ?? '0' }} <span class="text-xs font-semibold text-slate-500">ekor</span>
                </h3>
                <p class="text-[11px] text-indigo-600 font-bold">SR: {{ $stats['keberhasilanRate'] ?? 99.1 }}%</p>
            </div>
        </div>

        <!-- Kolam Siap Panen -->
        <div class="bg-white p-5 rounded-3xl border border-slate-200/90 shadow-xs flex items-center gap-4">
            <div class="w-12 h-12 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center shrink-0 text-xl border border-amber-100">
                <i class="fa-solid fa-scale-balanced"></i>
            </div>
            <div>
                <p class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400">Kolam Siap Panen</p>
                <h3 class="text-xl font-black text-slate-900 mt-0.5">
                    {{ $stats['siapPanenCount'] ?? 0 }} <span class="text-xs font-bold text-amber-600">/ {{ $stats['totalKolamCount'] ?? count($kolams) }} Unit</span>
                </h3>
                <p class="text-[11px] text-slate-400 font-medium">Siap panen &amp; distribusi</p>
            </div>
        </div>

    </div>

    <!-- Filter Bar -->
    <div class="bg-white p-4 rounded-3xl border border-slate-200/90 shadow-xs flex flex-col sm:flex-row sm:items-center justify-between gap-3 relative z-20">
        <div class="flex items-center gap-2.5">
            <div class="w-8 h-8 rounded-xl bg-sky-50 text-[#0284C7] flex items-center justify-center text-xs font-bold border border-sky-100">
                <i class="fa-solid fa-sliders"></i>
            </div>
            <div>
                <span class="text-xs font-extrabold text-slate-900 block">Filter Status &amp; Tipe Kolam</span>
                <span class="text-[11px] text-slate-400 font-medium">Menampilkan <strong class="text-slate-700" x-text="filteredKolams.length"></strong> dari <strong class="text-slate-700" x-text="kolams.length"></strong> Unit Kolam</span>
            </div>
        </div>
        
        <div class="flex items-center gap-2.5 flex-wrap">
            <!-- Custom Modern Dropdown: Filter Status Kolam -->
            <div class="relative" @click.outside="openStatusDropdown = false">
                <button type="button" @click="openStatusDropdown = !openStatusDropdown; openTipeDropdown = false"
                        class="px-4 py-2.5 rounded-2xl border border-slate-200 text-xs font-bold bg-white hover:border-slate-300 focus:outline-none focus:ring-2 focus:ring-[#0284C7] transition-all cursor-pointer shadow-xs flex items-center justify-between gap-3 min-w-[170px]">
                    <div class="flex items-center gap-2">
                        <template x-if="statusFilter === 'semua'">
                            <div class="flex items-center gap-2 text-slate-700">
                                <i class="fa-solid fa-layer-group text-slate-400 text-xs"></i>
                                <span>Semua Status</span>
                            </div>
                        </template>
                        <template x-if="statusFilter === 'optimal'">
                            <div class="flex items-center gap-2 text-emerald-800">
                                <span class="w-2 h-2 rounded-full bg-emerald-500 shrink-0"></span>
                                <span>Terisi (Optimal)</span>
                            </div>
                        </template>
                        <template x-if="statusFilter === 'siap_panen'">
                            <div class="flex items-center gap-2 text-amber-800">
                                <span class="w-2 h-2 rounded-full bg-amber-500 shrink-0"></span>
                                <span>Siap Panen</span>
                            </div>
                        </template>
                        <template x-if="statusFilter === 'kosong'">
                            <div class="flex items-center gap-2 text-sky-800">
                                <span class="w-2 h-2 rounded-full bg-sky-500 shrink-0"></span>
                                <span>Kosong / Tersedia</span>
                            </div>
                        </template>
                    </div>
                    <i class="fa-solid fa-chevron-down text-[10px] text-slate-400 transition-transform duration-200" :class="openStatusDropdown ? 'rotate-180 text-[#0284C7]' : ''"></i>
                </button>

                <!-- Popover Menu -->
                <div x-show="openStatusDropdown"
                     x-transition:enter="transition ease-out duration-150"
                     x-transition:enter-start="opacity-0 translate-y-1 scale-95"
                     x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                     x-transition:leave="transition ease-in duration-100"
                     x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                     x-transition:leave-end="opacity-0 translate-y-1 scale-95"
                     class="absolute right-0 top-full mt-2 w-56 bg-white rounded-2xl shadow-2xl border border-slate-200/90 p-1.5 z-50 text-xs space-y-1"
                     style="display: none;">
                    
                    <button type="button" @click="statusFilter = 'semua'; openStatusDropdown = false"
                            :class="statusFilter === 'semua' ? 'bg-slate-100 text-slate-900 font-black' : 'text-slate-600 hover:bg-slate-50 font-bold'"
                            class="w-full px-3 py-2 rounded-xl flex items-center justify-between text-left transition-colors cursor-pointer">
                        <div class="flex items-center gap-2.5">
                            <div class="w-6 h-6 rounded-lg bg-slate-100 text-slate-500 flex items-center justify-center text-xs">
                                <i class="fa-solid fa-layer-group"></i>
                            </div>
                            <span>Semua Status</span>
                        </div>
                        <div class="flex items-center gap-1.5">
                            <span class="text-[10px] px-1.5 py-0.5 rounded bg-slate-200 text-slate-700 font-bold" x-text="kolams.length"></span>
                            <i x-show="statusFilter === 'semua'" class="fa-solid fa-check text-[#0284C7] text-xs"></i>
                        </div>
                    </button>

                    <button type="button" @click="statusFilter = 'optimal'; openStatusDropdown = false"
                            :class="statusFilter === 'optimal' ? 'bg-emerald-50 text-emerald-900 font-black' : 'text-slate-600 hover:bg-emerald-50/50 font-bold'"
                            class="w-full px-3 py-2 rounded-xl flex items-center justify-between text-left transition-colors cursor-pointer">
                        <div class="flex items-center gap-2.5">
                            <div class="w-6 h-6 rounded-lg bg-emerald-100 text-emerald-600 flex items-center justify-center text-xs">
                                <i class="fa-solid fa-circle-check"></i>
                            </div>
                            <span>Terisi (Optimal)</span>
                        </div>
                        <div class="flex items-center gap-1.5">
                            <span class="text-[10px] px-1.5 py-0.5 rounded bg-emerald-100 text-emerald-800 font-bold" x-text="kolams.filter(k => (k.status || '').toLowerCase().includes('optimal') || (k.status || '').toLowerCase().includes('terisi')).length"></span>
                            <i x-show="statusFilter === 'optimal'" class="fa-solid fa-check text-emerald-600 text-xs"></i>
                        </div>
                    </button>

                    <button type="button" @click="statusFilter = 'siap_panen'; openStatusDropdown = false"
                            :class="statusFilter === 'siap_panen' ? 'bg-amber-50 text-amber-900 font-black' : 'text-slate-600 hover:bg-amber-50/50 font-bold'"
                            class="w-full px-3 py-2 rounded-xl flex items-center justify-between text-left transition-colors cursor-pointer">
                        <div class="flex items-center gap-2.5">
                            <div class="w-6 h-6 rounded-lg bg-amber-100 text-amber-600 flex items-center justify-center text-xs">
                                <i class="fa-solid fa-scale-balanced"></i>
                            </div>
                            <span>Siap Panen</span>
                        </div>
                        <div class="flex items-center gap-1.5">
                            <span class="text-[10px] px-1.5 py-0.5 rounded bg-amber-100 text-amber-800 font-bold" x-text="kolams.filter(k => (k.status || '').toLowerCase().includes('panen')).length"></span>
                            <i x-show="statusFilter === 'siap_panen'" class="fa-solid fa-check text-amber-600 text-xs"></i>
                        </div>
                    </button>

                    <button type="button" @click="statusFilter = 'kosong'; openStatusDropdown = false"
                            :class="statusFilter === 'kosong' ? 'bg-sky-50 text-sky-900 font-black' : 'text-slate-600 hover:bg-sky-50/50 font-bold'"
                            class="w-full px-3 py-2 rounded-xl flex items-center justify-between text-left transition-colors cursor-pointer">
                        <div class="flex items-center gap-2.5">
                            <div class="w-6 h-6 rounded-lg bg-sky-100 text-sky-600 flex items-center justify-center text-xs">
                                <i class="fa-solid fa-water"></i>
                            </div>
                            <span>Kosong / Tersedia</span>
                        </div>
                        <div class="flex items-center gap-1.5">
                            <span class="text-[10px] px-1.5 py-0.5 rounded bg-sky-100 text-sky-800 font-bold" x-text="kolams.filter(k => (k.status || '').toLowerCase().includes('kosong') || (k.status || '').toLowerCase().includes('tersedia')).length"></span>
                            <i x-show="statusFilter === 'kosong'" class="fa-solid fa-check text-sky-600 text-xs"></i>
                        </div>
                    </button>
                </div>
            </div>

            <!-- Custom Modern Dropdown: Filter Tipe Kolam -->
            <div class="relative" @click.outside="openTipeDropdown = false">
                <button type="button" @click="openTipeDropdown = !openTipeDropdown; openStatusDropdown = false"
                        class="px-4 py-2.5 rounded-2xl border border-slate-200 text-xs font-bold bg-white hover:border-slate-300 focus:outline-none focus:ring-2 focus:ring-[#0284C7] transition-all cursor-pointer shadow-xs flex items-center justify-between gap-3 min-w-[190px]">
                    <div class="flex items-center gap-2 text-slate-700">
                        <i class="fa-solid fa-filter text-slate-400 text-xs"></i>
                        <span x-text="
                            tipeFilter === 'semua' ? 'Semua Tipe Kolam' :
                            tipeFilter === 'pembesaran' ? 'Tipe Pembesaran' :
                            tipeFilter === 'pemijahan' ? 'Hatchery / Pemijahan' :
                            tipeFilter === 'pendederan' ? 'Hatchery / Pendederan' :
                            tipeFilter === 'pemberokan' ? 'Stok / Pemberokan' : tipeFilter
                        "></span>
                    </div>
                    <i class="fa-solid fa-chevron-down text-[10px] text-slate-400 transition-transform duration-200" :class="openTipeDropdown ? 'rotate-180 text-[#0284C7]' : ''"></i>
                </button>

                <!-- Popover Menu -->
                <div x-show="openTipeDropdown"
                     x-transition:enter="transition ease-out duration-150"
                     x-transition:enter-start="opacity-0 translate-y-1 scale-95"
                     x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                     x-transition:leave="transition ease-in duration-100"
                     x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                     x-transition:leave-end="opacity-0 translate-y-1 scale-95"
                     class="absolute right-0 top-full mt-2 w-64 bg-white rounded-2xl shadow-2xl border border-slate-200/90 p-1.5 z-50 text-xs space-y-1"
                     style="display: none;">
                    
                    <button type="button" @click="tipeFilter = 'semua'; openTipeDropdown = false"
                            :class="tipeFilter === 'semua' ? 'bg-slate-100 text-slate-900 font-black' : 'text-slate-600 hover:bg-slate-50 font-bold'"
                            class="w-full px-3 py-2 rounded-xl flex items-center justify-between text-left transition-colors cursor-pointer">
                        <div class="flex items-center gap-2.5">
                            <div class="w-6 h-6 rounded-lg bg-slate-100 text-slate-500 flex items-center justify-center text-xs">
                                <i class="fa-solid fa-border-all"></i>
                            </div>
                            <span>Semua Tipe Kolam</span>
                        </div>
                        <i x-show="tipeFilter === 'semua'" class="fa-solid fa-check text-[#0284C7] text-xs"></i>
                    </button>

                    <button type="button" @click="tipeFilter = 'pembesaran'; openTipeDropdown = false"
                            :class="tipeFilter === 'pembesaran' ? 'bg-sky-50 text-sky-900 font-black' : 'text-slate-600 hover:bg-sky-50/50 font-bold'"
                            class="w-full px-3 py-2 rounded-xl flex items-center justify-between text-left transition-colors cursor-pointer">
                        <div class="flex items-center gap-2.5">
                            <div class="w-6 h-6 rounded-lg bg-sky-100 text-sky-600 flex items-center justify-center text-xs">
                                <i class="fa-solid fa-water"></i>
                            </div>
                            <span>Kolam Pembesaran</span>
                        </div>
                        <i x-show="tipeFilter === 'pembesaran'" class="fa-solid fa-check text-sky-600 text-xs"></i>
                    </button>

                    <button type="button" @click="tipeFilter = 'pemijahan'; openTipeDropdown = false"
                            :class="tipeFilter === 'pemijahan' ? 'bg-indigo-50 text-indigo-900 font-black' : 'text-slate-600 hover:bg-indigo-50/50 font-bold'"
                            class="w-full px-3 py-2 rounded-xl flex items-center justify-between text-left transition-colors cursor-pointer">
                        <div class="flex items-center gap-2.5">
                            <div class="w-6 h-6 rounded-lg bg-indigo-100 text-indigo-600 flex items-center justify-center text-xs">
                                <i class="fa-solid fa-egg"></i>
                            </div>
                            <span>Hatchery / Pemijahan</span>
                        </div>
                        <i x-show="tipeFilter === 'pemijahan'" class="fa-solid fa-check text-indigo-600 text-xs"></i>
                    </button>

                    <button type="button" @click="tipeFilter = 'pendederan'; openTipeDropdown = false"
                            :class="tipeFilter === 'pendederan' ? 'bg-teal-50 text-teal-900 font-black' : 'text-slate-600 hover:bg-teal-50/50 font-bold'"
                            class="w-full px-3 py-2 rounded-xl flex items-center justify-between text-left transition-colors cursor-pointer">
                        <div class="flex items-center gap-2.5">
                            <div class="w-6 h-6 rounded-lg bg-teal-100 text-teal-600 flex items-center justify-center text-xs">
                                <i class="fa-solid fa-fish-fins"></i>
                            </div>
                            <span>Hatchery / Pendederan</span>
                        </div>
                        <i x-show="tipeFilter === 'pendederan'" class="fa-solid fa-check text-teal-600 text-xs"></i>
                    </button>

                    <button type="button" @click="tipeFilter = 'pemberokan'; openTipeDropdown = false"
                            :class="tipeFilter === 'pemberokan' ? 'bg-amber-50 text-amber-900 font-black' : 'text-slate-600 hover:bg-amber-50/50 font-bold'"
                            class="w-full px-3 py-2 rounded-xl flex items-center justify-between text-left transition-colors cursor-pointer">
                        <div class="flex items-center gap-2.5">
                            <div class="w-6 h-6 rounded-lg bg-amber-100 text-amber-600 flex items-center justify-center text-xs">
                                <i class="fa-solid fa-warehouse"></i>
                            </div>
                            <span>Penampungan / Pemberokan</span>
                        </div>
                        <i x-show="tipeFilter === 'pemberokan'" class="fa-solid fa-check text-amber-600 text-xs"></i>
                    </button>
                </div>
            </div>

            <!-- Reset Filter Button if active -->
            <button type="button" 
                    x-show="statusFilter !== 'semua' || tipeFilter !== 'semua'"
                    @click="statusFilter = 'semua'; tipeFilter = 'semua'"
                    class="px-3.5 py-2.5 rounded-2xl bg-slate-100 hover:bg-slate-200 text-slate-600 text-xs font-bold transition-all flex items-center gap-1.5 cursor-pointer"
                    title="Reset Filter">
                <i class="fa-solid fa-rotate-left text-[11px]"></i>
                <span>Reset</span>
            </button>
        </div>
    </div>

    <!-- Kolam Table Container -->
    <div class="bg-white rounded-3xl border border-slate-200/90 shadow-xs overflow-visible">
        <div class="overflow-x-auto overflow-y-visible">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50/80 border-b border-slate-200/90 text-[10px] font-extrabold text-slate-400 uppercase tracking-wider">
                        <th class="py-4 px-6">KOLAM &amp; SPESIFIKASI</th>
                        <th class="py-4 px-6">PENANGGUNG JAWAB</th>
                        <th class="py-4 px-6">KAPASITAS &amp; POPULASI</th>
                        <th class="py-4 px-6">BIAYA INVESTASI (RP)</th>
                        <th class="py-4 px-6">KESEHATAN (PH)</th>
                        <th class="py-4 px-6">STATUS</th>
                        <th class="py-4 px-6 text-right">AKSI</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-xs font-medium text-slate-700">
                    <template x-if="filteredKolams.length === 0">
                        <tr>
                            <td colspan="7" class="py-12 text-center text-slate-400 text-xs font-medium">
                                <i class="fa-solid fa-water text-2xl text-slate-300 block mb-2"></i>
                                Tidak ada data kolam budidaya yang sesuai dengan filter yang dipilih.
                            </td>
                        </tr>
                    </template>

                    <template x-for="item in filteredKolams" :key="item.id_kolam || item.id">
                        <tr class="hover:bg-slate-50/70 transition-colors">
                            <!-- Kolam & Tipe -->
                            <td class="py-4 px-6">
                                <div class="font-extrabold text-slate-900 text-xs" x-text="item.nama_kolam || item.id"></div>
                                <div class="text-[11px] text-slate-400 font-semibold" x-text="item.tipe_kolam || item.lokasi"></div>
                            </td>

                            <!-- Penanggung Jawab -->
                            <td class="py-4 px-6">
                                <div class="flex items-center gap-2.5">
                                    <div class="w-7 h-7 rounded-full flex items-center justify-center font-black text-xs shrink-0" 
                                         :class="item.colorClass || 'bg-sky-100 text-sky-700'" 
                                         x-text="item.initials">
                                    </div>
                                    <span class="font-bold text-slate-800" x-text="item.pembudidaya"></span>
                                </div>
                            </td>

                            <!-- Kapasitas & Populasi -->
                            <td class="py-4 px-6">
                                <div class="font-black text-slate-900 text-xs" x-text="item.populasi"></div>
                                <div class="text-[10px] text-slate-400" x-text="'Maks: ' + Number(item.kapasitas || 0).toLocaleString('id-ID') + ' ekor'"></div>
                            </td>

                            <!-- Biaya Investasi Kolam -->
                            <td class="py-4 px-6">
                                <template x-if="Number(item.biaya_pembuatan) > 0">
                                    <div>
                                        <div class="font-black text-slate-900 text-xs" x-text="'Rp ' + Number(item.biaya_pembuatan).toLocaleString('id-ID')"></div>
                                        <span class="text-[9px] font-extrabold text-emerald-700 bg-emerald-50 px-1.5 py-0.5 rounded border border-emerald-200">
                                            Buku Keuangan
                                        </span>
                                    </div>
                                </template>
                                <template x-if="!Number(item.biaya_pembuatan) || Number(item.biaya_pembuatan) === 0">
                                    <span class="text-slate-400 text-xs italic">-</span>
                                </template>
                            </td>

                            <!-- pH Air -->
                            <td class="py-4 px-6">
                                <span class="px-2 py-0.5 rounded-md font-extrabold text-[11px]"
                                      :class="(item.kesehatan_ph_air >= 6.5 && item.kesehatan_ph_air <= 8.5) ? 'bg-sky-50 text-sky-700 border border-sky-200' : 'bg-rose-50 text-rose-700 border border-rose-200'"
                                      x-text="'pH ' + (Number(item.kesehatan_ph_air || 7.2).toFixed(1))">
                                </span>
                            </td>

                            <!-- Status -->
                            <td class="py-4 px-6">
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-extrabold" :class="item.statusClass">
                                    <span class="w-1.5 h-1.5 rounded-full" :class="item.dotClass"></span>
                                    <span x-text="item.status"></span>
                                </span>
                            </td>

                            <!-- Aksi Menu (3-Dots Dropdown) -->
                            <td class="py-4 px-6 text-right">
                                <div class="relative inline-block text-left" 
                                     x-data="{
                                         open: false,
                                         menuStyle: '',
                                         toggle(event) {
                                             this.open = !this.open;
                                             if (this.open) {
                                                 const rect = this.$refs.btn.getBoundingClientRect();
                                                 const topPos = rect.bottom + 6;
                                                 const rightPos = window.innerWidth - rect.right;
                                                 this.menuStyle = `position: fixed; z-index: 99999; top: ${topPos}px; right: ${rightPos}px;`;
                                             }
                                         }
                                     }">
                                    <button x-ref="btn" 
                                            type="button" 
                                            @click="toggle($event)" 
                                            @click.away="open = false" 
                                            title="Menu Aksi Kolam"
                                            class="w-8 h-8 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-600 inline-flex items-center justify-center transition-colors cursor-pointer shadow-2xs">
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
                                         class="w-48 rounded-2xl bg-white border border-slate-200 shadow-2xl p-1.5 text-left"
                                         style="display: none;">
                                        
                                        <!-- Detail Kolam -->
                                        <button type="button" @click="open = false; openDetail(item)" class="w-full px-3 py-2 rounded-xl text-xs font-semibold text-slate-700 hover:bg-sky-50 hover:text-sky-700 flex items-center gap-2.5 transition-colors cursor-pointer">
                                            <i class="fa-solid fa-eye text-sky-600 w-4"></i>
                                            <span>Detail Kolam</span>
                                        </button>

                                        <!-- Edit Kolam -->
                                        <button type="button" @click="open = false; openEdit(item)" class="w-full px-3 py-2 rounded-xl text-xs font-semibold text-slate-700 hover:bg-amber-50 hover:text-amber-700 flex items-center gap-2.5 transition-colors cursor-pointer">
                                            <i class="fa-solid fa-pen-to-square text-amber-600 w-4"></i>
                                            <span>Edit Kolam</span>
                                        </button>

                                        <div class="my-1 border-t border-slate-100"></div>

                                        <!-- Hapus Kolam -->
                                        <button type="button" @click="open = false; deleteKolam(item)" class="w-full px-3 py-2 rounded-xl text-xs font-semibold text-rose-600 hover:bg-rose-50 flex items-center gap-2.5 transition-colors cursor-pointer">
                                            <i class="fa-solid fa-trash-can text-rose-600 w-4"></i>
                                            <span>Hapus Kolam</span>
                                        </button>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>
    </div>

    <!-- ========================================== -->
    <!-- MODAL TAMBAH KOLAM BARU                    -->
    <!-- ========================================== -->
    <div x-show="modalOpen" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-95"
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm"
         style="display: none;">
        
        <div class="bg-white rounded-3xl max-w-lg w-full p-6 shadow-2xl space-y-4 border border-slate-200 max-h-[90vh] overflow-y-auto">
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-xl bg-sky-100 text-sky-700 flex items-center justify-center text-sm font-black">
                        <i class="fa-solid fa-layer-group"></i>
                    </div>
                    <div>
                        <h3 class="text-base font-extrabold text-slate-900">Tambah Kolam Baru</h3>
                        <p class="text-[11px] text-slate-400 font-medium">Input spesifikasi kolam &amp; otomatisasi biaya investasi.</p>
                    </div>
                </div>
                <button @click="modalOpen = false" class="text-slate-400 hover:text-slate-600 cursor-pointer">
                    <i class="fa-solid fa-xmark text-lg"></i>
                </button>
            </div>
            
            <form @submit.prevent="submitNewKolam()" class="space-y-4 text-xs font-medium">
                
                <!-- Nama Kolam -->
                <div>
                    <label class="block text-[10px] font-extrabold uppercase text-slate-500 mb-1">
                        Nama / Kode Kolam <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" x-model="newKolam.nama_kolam" required placeholder="Contoh: Kolam Pembesaran A3, Kolam Bioflok B2" 
                           class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-2xl focus:bg-white focus:ring-2 focus:ring-[#0284C7] text-slate-800 font-semibold focus:outline-none">
                </div>

                <!-- Tipe & Kapasitas Grid -->
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-[10px] font-extrabold uppercase text-slate-500 mb-1">
                            Tipe Kolam <span class="text-rose-500">*</span>
                        </label>
                        <div class="relative">
                            <select x-model="newKolam.tipe_kolam" required
                                    class="w-full px-3.5 py-2.5 bg-slate-50/80 hover:bg-white border border-slate-200 rounded-2xl focus:bg-white focus:border-[#0284C7] focus:ring-2 focus:ring-[#0284C7]/20 text-slate-800 text-xs font-semibold focus:outline-none cursor-pointer appearance-none pr-9 transition-all">
                                <option value="Kolam Pembesaran (Beton)">Kolam Beton (Pembesaran)</option>
                                <option value="Kolam Bioflok (Terpal)">Kolam Bioflok (Terpal)</option>
                                <option value="Kolam Tanah">Kolam Tanah Tradisional</option>
                                <option value="Hatchery / Pemijahan">Hatchery / Pemijahan</option>
                                <option value="Hatchery / Pendederan">Hatchery / Pendederan</option>
                                <option value="Kolam Penampungan Stok / Pemberokan">Penampungan Stok / Pemberokan</option>
                            </select>
                            <i class="fa-solid fa-chevron-down absolute right-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-xs pointer-events-none"></i>
                        </div>
                    </div>
                    <div>
                        <label class="block text-[10px] font-extrabold uppercase text-slate-500 mb-1">
                            Kapasitas Maksimal (Ekor) <span class="text-rose-500">*</span>
                        </label>
                        <input type="number" min="1" x-model="newKolam.kapasitas" required placeholder="Contoh: 2500" 
                               class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-2xl focus:bg-white focus:ring-2 focus:ring-[#0284C7] text-slate-800 font-semibold focus:outline-none">
                    </div>
                </div>

                <!-- Penanggung Jawab & pH Awal Grid -->
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-[10px] font-extrabold uppercase text-slate-500 mb-1">
                            Penanggung Jawab / Petugas
                        </label>
                        <div class="relative">
                            <select x-model="newKolam.id_user" 
                                    class="w-full px-3.5 py-2.5 bg-slate-50/80 hover:bg-white border border-slate-200 rounded-2xl focus:bg-white focus:border-[#0284C7] focus:ring-2 focus:ring-[#0284C7]/20 text-slate-800 text-xs font-semibold focus:outline-none cursor-pointer appearance-none pr-9 transition-all">
                                <template x-for="p in petugasList" :key="p.id_user">
                                    <option :value="p.id_user" x-text="p.nama + ' (' + (p.role || 'petugas') + ')'"></option>
                                </template>
                            </select>
                            <i class="fa-solid fa-chevron-down absolute right-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-xs pointer-events-none"></i>
                        </div>
                    </div>
                    <div>
                        <label class="block text-[10px] font-extrabold uppercase text-slate-500 mb-1">
                            pH Awal Air Kolam
                        </label>
                        <input type="number" step="0.1" min="5" max="9" x-model="newKolam.kesehatan_ph_air" placeholder="7.2" 
                               class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-2xl focus:bg-white focus:ring-2 focus:ring-[#0284C7] text-slate-800 font-semibold focus:outline-none">
                    </div>
                </div>

                <!-- ============================================== -->
                <!-- CARD SPESIAL: PENCATATAN INVESTASI KEUANGAN     -->
                <!-- ANTI PENGELUARAN GAIB (AUTO ENTRY KE KEUANGAN) -->
                <!-- ============================================== -->
                <div class="p-4 bg-emerald-50/70 border border-emerald-200 rounded-2xl space-y-3">
                    <div class="flex items-start gap-2.5">
                        <i class="fa-solid fa-receipt text-emerald-600 text-sm mt-0.5"></i>
                        <div>
                            <strong class="text-xs font-black text-emerald-950 block">Pencatatan Biaya Pembuatan Kolam</strong>
                            <p class="text-[11px] text-emerald-800 leading-relaxed font-normal">
                                Masukkan biaya pembangunan kolam (semen, terpal, aerasi). Sistem akan <strong>otomatis membukukan pengeluaran ini ke Laporan Keuangan</strong> agar tidak menjadi pengeluaran gaib.
                            </p>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-3 pt-1">
                        <div>
                            <label class="block text-[10px] font-extrabold uppercase text-emerald-900 mb-1">
                                Biaya Pembuatan (Rp)
                            </label>
                            <input type="number" min="0" step="50000" x-model="newKolam.biaya_pembuatan" placeholder="Contoh: 3500000" 
                                   class="w-full px-3.5 py-2.5 bg-white border border-emerald-300 rounded-xl focus:ring-2 focus:ring-emerald-500 text-slate-900 font-extrabold focus:outline-none">
                            <template x-if="Number(newKolam.biaya_pembuatan) > 0">
                                <span class="text-[10px] font-black text-emerald-700 block mt-1" x-text="'Preview: Rp ' + Number(newKolam.biaya_pembuatan).toLocaleString('id-ID')"></span>
                            </template>
                        </div>
                        <div>
                            <label class="block text-[10px] font-extrabold uppercase text-emerald-900 mb-1">
                                Tanggal Transaksi
                            </label>
                            <input type="date" x-model="newKolam.tgl_pembuatan" 
                                   class="w-full px-3.5 py-2.5 bg-white border border-emerald-300 rounded-xl focus:ring-2 focus:ring-emerald-500 text-slate-900 font-semibold focus:outline-none">
                        </div>
                    </div>
                </div>

                <!-- Catatan Konstruksi -->
                <div>
                    <label class="block text-[10px] font-extrabold uppercase text-slate-500 mb-1">Catatan Pembangunan / Spesifikasi</label>
                    <textarea x-model="newKolam.catatan" rows="2" placeholder="Contoh: Konstruksi kolam terpal bulat D-3m dengan aerasi blower 100 watt..."
                              class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-2xl focus:bg-white focus:ring-2 focus:ring-[#0284C7] text-slate-800 font-medium focus:outline-none"></textarea>
                </div>

                <!-- Action Buttons -->
                <div class="flex items-center justify-end gap-2.5 pt-3 border-t border-slate-100">
                    <button type="button" @click="modalOpen = false" 
                            class="px-4 py-2.5 rounded-xl bg-slate-100 text-slate-700 font-bold hover:bg-slate-200 transition-colors cursor-pointer">
                        Batal
                    </button>
                    <button type="submit" :disabled="isSubmitting"
                            class="px-5 py-2.5 rounded-xl bg-[#031B4E] hover:bg-sky-950 active:scale-[0.99] text-white font-extrabold shadow-md shadow-sky-950/20 transition-all cursor-pointer">
                        <span x-text="isSubmitting ? 'Menyimpan & Membukukan...' : 'Simpan Kolam & Catat Biaya'"></span>
                    </button>
                </div>

            </form>
        </div>
    </div>

    <!-- ========================================== -->
    <!-- MODAL DETAIL KOLAM                         -->
    <!-- ========================================== -->
    <div x-show="detailModalOpen" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-95"
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm"
         style="display: none;">
        
        <div class="bg-white rounded-3xl max-w-md w-full p-6 shadow-2xl space-y-4 border border-slate-200" x-show="selectedKolam">
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                <div>
                    <span class="text-[10px] font-extrabold uppercase text-slate-400 tracking-wider">Detail Data Kolam</span>
                    <h3 class="text-base font-extrabold text-slate-900" x-text="selectedKolam?.nama_kolam || selectedKolam?.id"></h3>
                </div>
                <button @click="detailModalOpen = false" class="text-slate-400 hover:text-slate-600 cursor-pointer">
                    <i class="fa-solid fa-xmark text-lg"></i>
                </button>
            </div>

            <div class="space-y-2.5 text-xs">
                <div class="p-3 bg-slate-50 rounded-2xl border border-slate-100 flex items-center justify-between">
                    <span class="text-slate-500 font-semibold">Tipe Kolam</span>
                    <span class="font-extrabold text-slate-900" x-text="selectedKolam?.tipe_kolam || selectedKolam?.lokasi"></span>
                </div>
                <div class="p-3 bg-slate-50 rounded-2xl border border-slate-100 flex items-center justify-between">
                    <span class="text-slate-500 font-semibold">Penanggung Jawab</span>
                    <span class="font-extrabold text-slate-900" x-text="selectedKolam?.pembudidaya"></span>
                </div>
                <div class="p-3 bg-slate-50 rounded-2xl border border-slate-100 flex items-center justify-between">
                    <span class="text-slate-500 font-semibold">Kapasitas Maksimal</span>
                    <span class="font-black text-slate-900" x-text="Number(selectedKolam?.kapasitas || 0).toLocaleString('id-ID') + ' Ekor'"></span>
                </div>
                <div class="p-3 bg-slate-50 rounded-2xl border border-slate-100 flex items-center justify-between">
                    <span class="text-slate-500 font-semibold">Populasi / Komoditas</span>
                    <span class="font-bold text-sky-800" x-text="selectedKolam?.populasi"></span>
                </div>
                <div class="p-3 bg-emerald-50/70 rounded-2xl border border-emerald-100 flex items-center justify-between">
                    <span class="text-emerald-900 font-semibold">Biaya Investasi Pembangunan</span>
                    <span class="font-black text-emerald-800" x-text="Number(selectedKolam?.biaya_pembuatan) > 0 ? ('Rp ' + Number(selectedKolam?.biaya_pembuatan).toLocaleString('id-ID')) : 'Tidak dicatat'"></span>
                </div>
                <div class="p-3 bg-slate-50 rounded-2xl border border-slate-100 flex items-center justify-between">
                    <span class="text-slate-500 font-semibold">pH Air Kolam</span>
                    <span class="font-black text-slate-900" x-text="'pH ' + Number(selectedKolam?.kesehatan_ph_air || 7.2).toFixed(1)"></span>
                </div>
                <div class="p-3 bg-slate-50 rounded-2xl border border-slate-100 flex items-center justify-between">
                    <span class="text-slate-500 font-semibold">Status Operasional</span>
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[10px] font-extrabold" :class="selectedKolam?.statusClass" x-text="selectedKolam?.status"></span>
                </div>
            </div>

            <div class="flex items-center justify-end pt-2">
                <button type="button" @click="detailModalOpen = false" 
                        class="px-5 py-2.5 rounded-xl bg-[#031B4E] text-white font-extrabold text-xs hover:bg-sky-950 transition-all cursor-pointer">
                    Tutup
                </button>
            </div>
        </div>
    </div>

    <!-- ========================================== -->
    <!-- MODAL EDIT DATA KOLAM                      -->
    <!-- ========================================== -->
    <div x-show="editModalOpen" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-95"
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm"
         style="display: none;">
        
        <div class="bg-white rounded-3xl max-w-lg w-full p-6 shadow-2xl space-y-4 border border-slate-200" x-show="selectedKolam">
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                <h3 class="text-base font-extrabold text-slate-900">Edit Data Kolam</h3>
                <button @click="editModalOpen = false" class="text-slate-400 hover:text-slate-600 cursor-pointer">
                    <i class="fa-solid fa-xmark text-lg"></i>
                </button>
            </div>
            
            <form @submit.prevent="saveEdit()" class="space-y-4 text-xs font-medium">
                <div>
                    <label class="block text-[10px] font-extrabold uppercase text-slate-500 mb-1">Nama / ID Kolam</label>
                    <input type="text" x-model="selectedKolam.nama_kolam" required
                           class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-2xl focus:bg-white focus:ring-2 focus:ring-[#0284C7] text-slate-800 font-semibold focus:outline-none">
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-[10px] font-extrabold uppercase text-slate-500 mb-1">Tipe Kolam</label>
                        <div class="relative">
                            <select x-model="selectedKolam.tipe_kolam" 
                                    class="w-full px-3.5 py-2.5 bg-slate-50/80 hover:bg-white border border-slate-200 rounded-2xl focus:bg-white focus:border-[#0284C7] focus:ring-2 focus:ring-[#0284C7]/20 text-slate-800 text-xs font-semibold focus:outline-none cursor-pointer appearance-none pr-9 transition-all">
                                <option value="Kolam Pembesaran (Beton)">Kolam Beton (Pembesaran)</option>
                                <option value="Kolam Bioflok (Terpal)">Kolam Bioflok (Terpal)</option>
                                <option value="Kolam Tanah">Kolam Tanah Tradisional</option>
                                <option value="Hatchery / Pemijahan">Hatchery / Pemijahan</option>
                                <option value="Hatchery / Pendederan">Hatchery / Pendederan</option>
                                <option value="Kolam Penampungan Stok / Pemberokan">Penampungan Stok / Pemberokan</option>
                            </select>
                            <i class="fa-solid fa-chevron-down absolute right-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-xs pointer-events-none"></i>
                        </div>
                    </div>
                    <div>
                        <label class="block text-[10px] font-extrabold uppercase text-slate-500 mb-1">Kapasitas (Ekor)</label>
                        <input type="number" min="1" x-model="selectedKolam.kapasitas" required
                               class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-2xl focus:bg-white focus:ring-2 focus:ring-[#0284C7] text-slate-800 font-semibold focus:outline-none">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-[10px] font-extrabold uppercase text-slate-500 mb-1">Penanggung Jawab</label>
                        <div class="relative">
                            <select x-model="selectedKolam.id_user" 
                                    class="w-full px-3.5 py-2.5 bg-slate-50/80 hover:bg-white border border-slate-200 rounded-2xl focus:bg-white focus:border-[#0284C7] focus:ring-2 focus:ring-[#0284C7]/20 text-slate-800 text-xs font-semibold focus:outline-none cursor-pointer appearance-none pr-9 transition-all">
                                <template x-for="p in petugasList" :key="p.id_user">
                                    <option :value="p.id_user" x-text="p.nama"></option>
                                </template>
                            </select>
                            <i class="fa-solid fa-chevron-down absolute right-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-xs pointer-events-none"></i>
                        </div>
                    </div>
                    <div>
                        <label class="block text-[10px] font-extrabold uppercase text-slate-500 mb-1">pH Air Kolam</label>
                        <input type="number" step="0.1" min="5" max="9" x-model="selectedKolam.kesehatan_ph_air" 
                               class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-2xl focus:bg-white focus:ring-2 focus:ring-[#0284C7] text-slate-800 font-semibold focus:outline-none">
                    </div>
                </div>

                <div>
                    <label class="block text-[10px] font-extrabold uppercase text-slate-500 mb-1">Biaya Investasi / Pembuatan (Rp)</label>
                    <input type="number" min="0" step="50000" x-model="selectedKolam.biaya_pembuatan" 
                           class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-2xl focus:bg-white focus:ring-2 focus:ring-[#0284C7] text-slate-800 font-semibold focus:outline-none">
                </div>

                <div class="flex items-center justify-end gap-2.5 pt-3 border-t border-slate-100">
                    <button type="button" @click="editModalOpen = false" 
                            class="px-4 py-2.5 rounded-xl bg-slate-100 text-slate-700 font-bold hover:bg-slate-200 transition-colors cursor-pointer">
                        Batal
                    </button>
                    <button type="submit" :disabled="isSubmitting"
                            class="px-5 py-2.5 rounded-xl bg-[#031B4E] hover:bg-sky-950 active:scale-[0.99] text-white font-extrabold shadow-md shadow-sky-950/20 transition-all cursor-pointer">
                        <span x-text="isSubmitting ? 'Menyimpan...' : 'Perbarui Kolam'"></span>
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
function kolamManagementComponent() {
    return {
        modalOpen: false,
        detailModalOpen: false,
        editModalOpen: false,
        isSubmitting: false,
        statusFilter: "semua",
        tipeFilter: "semua",
        openStatusDropdown: false,
        openTipeDropdown: false,
        selectedKolam: null,

        petugasList: {!! json_encode($petugasList ?? []) !!},
        kolams: {!! json_encode($kolams ?? []) !!},

        newKolam: {
            nama_kolam: "",
            tipe_kolam: "Kolam Pembesaran (Beton)",
            kapasitas: 2500,
            id_user: "",
            kesehatan_ph_air: 7.2,
            biaya_pembuatan: 0,
            tgl_pembuatan: new Date().toISOString().split('T')[0],
            catatan: ""
        },

        init() {
            if (this.petugasList.length > 0) {
                this.newKolam.id_user = this.petugasList[0].id_user;
            }
        },

        get filteredKolams() {
            return this.kolams.filter(item => {
                const tipe = (item.tipe_kolam || item.lokasi || '').toLowerCase();
                const statusLower = (item.status || '').toLowerCase();

                const matchStatus = this.statusFilter === "semua" || 
                    (this.statusFilter === "optimal" && (statusLower.includes("optimal") || statusLower.includes("terisi"))) ||
                    (this.statusFilter === "siap_panen" && statusLower.includes("panen")) ||
                    (this.statusFilter === "kosong" && (statusLower.includes("kosong") || statusLower.includes("tersedia")));

                const matchTipe = this.tipeFilter === "semua" ||
                    tipe.includes(this.tipeFilter.toLowerCase());

                return matchStatus && matchTipe;
            });
        },

        openCreateModal() {
            this.newKolam = {
                nama_kolam: "",
                tipe_kolam: "Kolam Pembesaran (Beton)",
                kapasitas: 2500,
                id_user: this.petugasList.length > 0 ? this.petugasList[0].id_user : "",
                kesehatan_ph_air: 7.2,
                biaya_pembuatan: 3500000,
                tgl_pembuatan: new Date().toISOString().split('T')[0],
                catatan: ""
            };
            this.modalOpen = true;
        },

        openDetail(item) {
            this.selectedKolam = item;
            this.detailModalOpen = true;
        },

        openEdit(item) {
            this.selectedKolam = JSON.parse(JSON.stringify(item));
            this.editModalOpen = true;
        },

        async submitNewKolam() {
            if (!this.newKolam.nama_kolam) {
                if (typeof triggerToast === 'function') {
                    triggerToast('Nama / ID Kolam wajib diisi!', 'warning');
                } else {
                    alert('Nama / ID Kolam wajib diisi!');
                }
                return;
            }

            this.isSubmitting = true;
            try {
                const res = await fetch('{{ route('kolam.store') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify(this.newKolam)
                });

                const data = await res.json();
                if (res.ok && data.success) {
                    if (data.kolam) {
                        this.kolams.unshift(data.kolam);
                    }
                    this.modalOpen = false;
                    if (typeof triggerToast === 'function') {
                        triggerToast(data.message, 'success');
                    } else {
                        alert(data.message);
                    }
                } else {
                    const errMsg = data.message || (data.errors ? Object.values(data.errors).flat().join('\n') : 'Gagal menambahkan kolam.');
                    if (typeof triggerToast === 'function') {
                        triggerToast(errMsg, 'error');
                    } else {
                        alert(errMsg);
                    }
                }
            } catch (err) {
                if (typeof triggerToast === 'function') {
                    triggerToast('Terjadi kesalahan jaringan.', 'error');
                } else {
                    alert('Terjadi kesalahan jaringan.');
                }
            } finally {
                this.isSubmitting = false;
            }
        },

        async saveEdit() {
            if (!this.selectedKolam || !this.selectedKolam.id_kolam) return;

            this.isSubmitting = true;
            try {
                const res = await fetch('{{ url('kolam') }}/' + this.selectedKolam.id_kolam, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        nama_kolam: this.selectedKolam.nama_kolam,
                        tipe_kolam: this.selectedKolam.tipe_kolam,
                        kapasitas: Number(this.selectedKolam.kapasitas),
                        id_user: this.selectedKolam.id_user,
                        kesehatan_ph_air: Number(this.selectedKolam.kesehatan_ph_air),
                        biaya_pembuatan: Number(this.selectedKolam.biaya_pembuatan || 0),
                        catatan: this.selectedKolam.catatan
                    })
                });

                const data = await res.json();
                if (res.ok && data.success) {
                    const idx = this.kolams.findIndex(k => k.id_kolam === this.selectedKolam.id_kolam);
                    if (idx !== -1) {
                        this.kolams[idx].nama_kolam = this.selectedKolam.nama_kolam;
                        this.kolams[idx].id = this.selectedKolam.nama_kolam;
                        this.kolams[idx].tipe_kolam = this.selectedKolam.tipe_kolam;
                        this.kolams[idx].lokasi = this.selectedKolam.tipe_kolam;
                        this.kolams[idx].kapasitas = this.selectedKolam.kapasitas;
                        this.kolams[idx].id_user = this.selectedKolam.id_user;
                        this.kolams[idx].kesehatan_ph_air = this.selectedKolam.kesehatan_ph_air;
                        this.kolams[idx].biaya_pembuatan = this.selectedKolam.biaya_pembuatan;

                        const matchedUser = this.petugasList.find(u => u.id_user == this.selectedKolam.id_user);
                        if (matchedUser) {
                            this.kolams[idx].pembudidaya = matchedUser.nama;
                        }
                    }
                    this.editModalOpen = false;
                    if (typeof triggerToast === 'function') {
                        triggerToast(data.message, 'success');
                    } else {
                        alert(data.message);
                    }
                } else {
                    const errMsg = data.message || 'Gagal memperbarui data kolam.';
                    if (typeof triggerToast === 'function') {
                        triggerToast(errMsg, 'error');
                    } else {
                        alert(errMsg);
                    }
                }
            } catch (err) {
                if (typeof triggerToast === 'function') {
                    triggerToast('Terjadi kesalahan jaringan.', 'error');
                } else {
                    alert('Terjadi kesalahan jaringan.');
                }
            } finally {
                this.isSubmitting = false;
            }
        },

        async deleteKolam(item) {
            const confirmed = await (typeof AppSwal !== 'undefined' 
                ? AppSwal.confirmDelete({
                    title: 'Hapus Kolam ' + (item.nama_kolam || item.id) + '?',
                    text: 'Apakah Anda yakin ingin menghapus kolam ini? Kolam yang memiliki siklus ikan aktif tidak dapat dihapus.',
                    confirmText: 'Ya, Hapus Kolam',
                    cancelText: 'Batal'
                })
                : Promise.resolve({ isConfirmed: confirm('Hapus kolam ' + (item.nama_kolam || item.id) + '?') }));

            if (!confirmed.isConfirmed) return;

            try {
                const res = await fetch('{{ url('kolam') }}/' + (item.id_kolam || item.id), {
                    method: 'DELETE',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                });

                const data = await res.json();
                if (res.ok && data.success) {
                    this.kolams = this.kolams.filter(k => (k.id_kolam || k.id) !== (item.id_kolam || item.id));
                    if (typeof triggerToast === 'function') {
                        triggerToast(data.message, 'success');
                    } else {
                        alert(data.message);
                    }
                } else {
                    const msg = data.message || 'Gagal menghapus kolam.';
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            icon: 'error',
                            title: 'Tidak Dapat Dihapus',
                            text: msg
                        });
                    } else {
                        alert(msg);
                    }
                }
            } catch (err) {
                if (typeof triggerToast === 'function') {
                    triggerToast('Terjadi kesalahan koneksi server.', 'error');
                } else {
                    alert('Terjadi kesalahan koneksi server.');
                }
            }
        }
    };
}
</script>
@endpush

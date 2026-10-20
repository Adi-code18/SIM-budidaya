@extends('layouts.app')

@section('title', 'Inventaris & Gudang Operasional - AMS BUDIDAYA')

@section('content')
<div class="space-y-6" x-data="gudangComponent()">

    <!-- Breadcrumb & Header Title -->
    <div>
        <div class="flex items-center gap-2 text-xs font-medium text-slate-400">
            <span>Budidaya &amp; Operasional</span>
            <i class="fa-solid fa-chevron-right text-[10px]"></i>
            <span class="text-slate-700 font-bold">Inventaris &amp; Gudang</span>
        </div>
    </div>

    <!-- Header & Primary Action -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2.5">
                <div class="w-10 h-10 rounded-2xl bg-[#031B4E]/10 text-[#031B4E] flex items-center justify-center text-lg font-black shrink-0">
                    <i class="fa-solid fa-boxes-packing"></i>
                </div>
                <div>
                    <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">Inventaris &amp; Gudang Operasional</h1>
                    <p class="text-xs sm:text-sm text-slate-500 font-medium">Tata kelola stok bahan habis pakai (probiotik, obat, kapur, vitamin) dan aset peralatan tambak terintegrasi ke Keuangan.</p>
                </div>
            </div>
        </div>
        <div class="flex items-center gap-2.5">
            <button @click="openCreateModal()" 
                    class="px-5 py-3 rounded-2xl bg-[#031B4E] hover:bg-sky-900 active:scale-[0.99] text-white font-extrabold text-xs sm:text-sm shadow-md shadow-sky-950/20 transition-all flex items-center justify-center gap-2 cursor-pointer">
                <i class="fa-solid fa-circle-plus text-sm text-sky-300"></i>
                <span>Tambah Item Baru</span>
            </button>
        </div>
    </div>

    <!-- Overview KPI Cards (4 Cards) -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        
        <!-- Total Nilai Aset Gudang -->
        <div class="bg-white p-5 rounded-3xl border border-slate-200/90 shadow-xs flex items-center gap-4">
            <div class="w-12 h-12 rounded-2xl bg-[#031B4E]/10 text-[#031B4E] flex items-center justify-center shrink-0 text-xl border border-sky-900/10">
                <i class="fa-solid fa-vault"></i>
            </div>
            <div>
                <p class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400">Total Nilai Gudang</p>
                <h3 class="text-xl font-black text-slate-900 mt-0.5">
                    Rp {{ number_format($totalNilaiAset, 0, ',', '.') }}
                </h3>
                <p class="text-[11px] text-slate-400 font-medium">{{ count($items) }} Varian Barang Tersimpan</p>
            </div>
        </div>

        <!-- Nilai Saprotan (Habis Pakai / HPP) -->
        <div class="bg-white p-5 rounded-3xl border border-slate-200/90 shadow-xs flex items-center gap-4">
            <div class="w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0 text-xl border border-emerald-100">
                <i class="fa-solid fa-prescription-bottle-medical"></i>
            </div>
            <div>
                <p class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400">Nilai Bahan Habis Pakai</p>
                <h3 class="text-xl font-black text-emerald-700 mt-0.5">
                    Rp {{ number_format($totalNilaiSaprotan, 0, ',', '.') }}
                </h3>
                <span class="inline-flex items-center gap-1 text-[10px] font-bold text-emerald-600">
                    <i class="fa-solid fa-circle-check text-[9px]"></i> Alokasi HPP Kolam
                </span>
            </div>
        </div>

        <!-- Peralatan & Mesin Aktif (Capex) -->
        <div class="bg-white p-5 rounded-3xl border border-slate-200/90 shadow-xs flex items-center gap-4">
            <div class="w-12 h-12 rounded-2xl bg-sky-50 text-sky-600 flex items-center justify-center shrink-0 text-xl border border-sky-100">
                <i class="fa-solid fa-gears"></i>
            </div>
            <div>
                <p class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400">Peralatan &amp; Mesin</p>
                <h3 class="text-xl font-black text-slate-900 mt-0.5">
                    Rp {{ number_format($totalNilaiPeralatan, 0, ',', '.') }}
                </h3>
                <p class="text-[11px] text-sky-600 font-bold">{{ $items->where('kategori_barang', 'peralatan')->count() }} Aset Operasional</p>
            </div>
        </div>

        <!-- Peringatan Kritis / Perlu Perhatian -->
        <div class="bg-white p-5 rounded-3xl border border-slate-200/90 shadow-xs flex items-center gap-4">
            <div class="w-12 h-12 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center shrink-0 text-xl border border-amber-100">
                <i class="fa-solid fa-triangle-exclamation"></i>
            </div>
            <div>
                <p class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400">Perlu Perhatian</p>
                <h3 class="text-xl font-black text-slate-900 mt-0.5">
                    {{ $kritisCount + $alatPerluServisCount }} <span class="text-xs font-bold text-amber-700">Item</span>
                </h3>
                <p class="text-[11px] text-slate-400 font-medium">{{ $kritisCount }} Kritis / {{ $alatPerluServisCount }} Butuh Servis</p>
            </div>
        </div>

    </div>

    <!-- Tab Navigasi & Filter Panel -->
    <div class="bg-white p-3 rounded-3xl border border-slate-200/90 shadow-xs flex flex-col md:flex-row md:items-center justify-between gap-3 relative z-20">
        
        <!-- Tab Pilihan -->
        <div class="flex items-center gap-1.5 overflow-x-auto no-scrollbar p-1">
            <button type="button" @click="activeTab = 'katalog'; filterKategori = 'semua'"
                    :class="activeTab === 'katalog' && filterKategori === 'semua' ? 'bg-[#031B4E] text-white shadow-sm' : 'text-slate-600 hover:bg-slate-100'"
                    class="px-4 py-2 rounded-2xl text-xs font-bold transition-all shrink-0 cursor-pointer flex items-center gap-2">
                <i class="fa-solid fa-boxes-stacked text-xs"></i>
                <span>Semua Item (<span x-text="items.length"></span>)</span>
            </button>

            <button type="button" @click="activeTab = 'katalog'; filterKategori = 'saprotan'"
                    :class="activeTab === 'katalog' && filterKategori === 'saprotan' ? 'bg-emerald-600 text-white shadow-sm' : 'text-slate-600 hover:bg-slate-100'"
                    class="px-4 py-2 rounded-2xl text-xs font-bold transition-all shrink-0 cursor-pointer flex items-center gap-2">
                <i class="fa-solid fa-flask text-xs"></i>
                <span>Bahan Habis Pakai (<span x-text="items.filter(i => i.kategori_barang === 'saprotan').length"></span>)</span>
            </button>

            <button type="button" @click="activeTab = 'katalog'; filterKategori = 'peralatan'"
                    :class="activeTab === 'katalog' && filterKategori === 'peralatan' ? 'bg-[#0284C7] text-white shadow-sm' : 'text-slate-600 hover:bg-slate-100'"
                    class="px-4 py-2 rounded-2xl text-xs font-bold transition-all shrink-0 cursor-pointer flex items-center gap-2">
                <i class="fa-solid fa-screwdriver-wrench text-xs"></i>
                <span>Peralatan &amp; Mesin (<span x-text="items.filter(i => i.kategori_barang === 'peralatan').length"></span>)</span>
            </button>

            <button type="button" @click="activeTab = 'riwayat'"
                    :class="activeTab === 'riwayat' ? 'bg-indigo-600 text-white shadow-sm' : 'text-slate-600 hover:bg-slate-100'"
                    class="px-4 py-2 rounded-2xl text-xs font-bold transition-all shrink-0 cursor-pointer flex items-center gap-2">
                <i class="fa-solid fa-clock-rotate-left text-xs"></i>
                <span>Riwayat Mutasi Gudang (<span x-text="riwayatMutasi.length"></span>)</span>
            </button>
        </div>

        <!-- Filter Dropdown Status Stok (Hanya di Tab Katalog) -->
        <div x-show="activeTab === 'katalog'" class="flex items-center gap-2 px-1">
            <div class="relative" @click.outside="openStatusFilter = false">
                <button type="button" @click="openStatusFilter = !openStatusFilter"
                        class="px-4 py-2 rounded-2xl border border-slate-200 text-xs font-bold bg-slate-50 hover:bg-white focus:outline-none focus:ring-2 focus:ring-[#0284C7] transition-all cursor-pointer shadow-xs flex items-center justify-between gap-2.5 min-w-[160px]">
                    <div class="flex items-center gap-1.5 text-slate-700">
                        <i class="fa-solid fa-filter text-slate-400 text-xs"></i>
                        <span x-text="
                            statusFilter === 'semua' ? 'Semua Status Stok' :
                            statusFilter === 'aman' ? 'Stok Aman' :
                            statusFilter === 'waspada' ? 'Perlu Restock' : 'Stok Kritis'
                        "></span>
                    </div>
                    <i class="fa-solid fa-chevron-down text-[10px] text-slate-400 transition-transform duration-200" :class="openStatusFilter ? 'rotate-180 text-[#0284C7]' : ''"></i>
                </button>

                <!-- Popover Menu -->
                <div x-show="openStatusFilter"
                     x-transition:enter="transition ease-out duration-150"
                     x-transition:enter-start="opacity-0 translate-y-1 scale-95"
                     x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                     x-transition:leave="transition ease-in duration-100"
                     x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                     x-transition:leave-end="opacity-0 translate-y-1 scale-95"
                     class="absolute right-0 top-full mt-2 w-52 bg-white rounded-2xl shadow-2xl border border-slate-200/90 p-1.5 z-50 text-xs space-y-1"
                     style="display: none;">
                    
                    <button type="button" @click="statusFilter = 'semua'; openStatusFilter = false"
                            :class="statusFilter === 'semua' ? 'bg-slate-100 text-slate-900 font-black' : 'text-slate-600 hover:bg-slate-50 font-bold'"
                            class="w-full px-3 py-2 rounded-xl flex items-center justify-between text-left transition-colors cursor-pointer">
                        <span>Semua Status</span>
                        <i x-show="statusFilter === 'semua'" class="fa-solid fa-check text-[#0284C7] text-xs"></i>
                    </button>

                    <button type="button" @click="statusFilter = 'aman'; openStatusFilter = false"
                            :class="statusFilter === 'aman' ? 'bg-emerald-50 text-emerald-900 font-black' : 'text-slate-600 hover:bg-emerald-50/50 font-bold'"
                            class="w-full px-3 py-2 rounded-xl flex items-center justify-between text-left transition-colors cursor-pointer">
                        <span class="flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                            <span>Stok Aman</span>
                        </span>
                        <i x-show="statusFilter === 'aman'" class="fa-solid fa-check text-emerald-600 text-xs"></i>
                    </button>

                    <button type="button" @click="statusFilter = 'waspada'; openStatusFilter = false"
                            :class="statusFilter === 'waspada' ? 'bg-amber-50 text-amber-900 font-black' : 'text-slate-600 hover:bg-amber-50/50 font-bold'"
                            class="w-full px-3 py-2 rounded-xl flex items-center justify-between text-left transition-colors cursor-pointer">
                        <span class="flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-amber-500"></span>
                            <span>Perlu Restock</span>
                        </span>
                        <i x-show="statusFilter === 'waspada'" class="fa-solid fa-check text-amber-600 text-xs"></i>
                    </button>

                    <button type="button" @click="statusFilter = 'kritis'; openStatusFilter = false"
                            :class="statusFilter === 'kritis' ? 'bg-rose-50 text-rose-900 font-black' : 'text-slate-600 hover:bg-rose-50/50 font-bold'"
                            class="w-full px-3 py-2 rounded-xl flex items-center justify-between text-left transition-colors cursor-pointer">
                        <span class="flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-rose-500 animate-pulse"></span>
                            <span>Stok Kritis / Habis</span>
                        </span>
                        <i x-show="statusFilter === 'kritis'" class="fa-solid fa-check text-rose-600 text-xs"></i>
                    </button>
                </div>
            </div>

            <!-- Tombol Reset Filter -->
            <button type="button" 
                    x-show="statusFilter !== 'semua' || filterKategori !== 'semua'"
                    @click="statusFilter = 'semua'; filterKategori = 'semua'"
                    class="px-3 py-2 rounded-2xl bg-slate-100 hover:bg-slate-200 text-slate-600 text-xs font-bold transition-all flex items-center gap-1.5 cursor-pointer"
                    title="Reset Filter">
                <i class="fa-solid fa-rotate-left text-[10px]"></i>
                <span>Reset</span>
            </button>
        </div>
    </div>

    <!-- ============================================================= -->
    <!-- TAB 1: KATALOG INVENTARIS GUDANG                              -->
    <!-- ============================================================= -->
    <div x-show="activeTab === 'katalog'" class="bg-white rounded-3xl border border-slate-200/90 shadow-xs overflow-visible">
        <div class="overflow-x-auto overflow-y-visible">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50/80 border-b border-slate-200/90 text-[10px] font-extrabold text-slate-400 uppercase tracking-wider">
                        <th class="py-4 px-6">KODE &amp; NAMA BARANG</th>
                        <th class="py-4 px-6">KATEGORI</th>
                        <th class="py-4 px-6">STOK TERSEDIA</th>
                        <th class="py-4 px-6">HARGA SATUAN</th>
                        <th class="py-4 px-6">TOTAL NILAI</th>
                        <th class="py-4 px-6">KONDISI / LOKASI</th>
                        <th class="py-4 px-6 text-right">AKSI</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-xs font-medium text-slate-700">
                    <template x-if="filteredItems.length === 0">
                        <tr>
                            <td colspan="7" class="py-12 text-center text-slate-400 text-xs font-medium">
                                <i class="fa-solid fa-boxes-packing text-2xl text-slate-300 block mb-2"></i>
                                Tidak ada data barang gudang yang sesuai dengan kriteria filter.
                            </td>
                        </tr>
                    </template>

                    <template x-for="item in filteredItems" :key="item.id_barang">
                        <tr class="hover:bg-slate-50/70 transition-colors">
                            
                            <!-- Kode & Nama -->
                            <td class="py-4 px-6">
                                <div class="flex items-center gap-2.5">
                                    <div class="w-8 h-8 rounded-xl flex items-center justify-center text-xs font-black shrink-0"
                                         :class="item.kategori_barang === 'saprotan' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-sky-50 text-sky-700 border border-sky-200'">
                                        <i class="fa-solid" :class="item.kategori_barang === 'saprotan' ? 'fa-flask' : 'fa-gear'"></i>
                                    </div>
                                    <div>
                                        <div class="font-extrabold text-slate-900 text-xs" x-text="item.nama_barang"></div>
                                        <span class="text-[10px] font-bold text-slate-400" x-text="item.kode_barang"></span>
                                    </div>
                                </div>
                            </td>

                            <!-- Kategori -->
                            <td class="py-4 px-6">
                                <template x-if="item.kategori_barang === 'saprotan'">
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-extrabold bg-emerald-50 text-emerald-800 border border-emerald-200">
                                        Habis Pakai (Saprotan)
                                    </span>
                                </template>
                                <template x-if="item.kategori_barang === 'peralatan'">
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-extrabold bg-sky-50 text-sky-800 border border-sky-200">
                                        Peralatan &amp; Mesin
                                    </span>
                                </template>
                            </td>

                            <!-- Stok Tersedia & Indikator -->
                            <td class="py-4 px-6">
                                <div class="flex items-center gap-2">
                                    <span class="font-black text-slate-900 text-xs" x-text="Number(item.stok_tersisa).toLocaleString('id-ID') + ' ' + item.satuan"></span>
                                    <template x-if="item.stok_tersisa <= item.min_stok">
                                        <span class="text-[9px] font-extrabold px-1.5 py-0.5 rounded bg-rose-100 text-rose-800">
                                            Kritis
                                        </span>
                                    </template>
                                    <template x-if="item.stok_tersisa > item.min_stok && item.stok_tersisa <= (item.min_stok * 1.5)">
                                        <span class="text-[9px] font-extrabold px-1.5 py-0.5 rounded bg-amber-100 text-amber-800">
                                            Waspada
                                        </span>
                                    </template>
                                </div>
                                <div class="text-[10px] text-slate-400 mt-0.5" x-text="'Batas Minimum: ' + Number(item.min_stok).toLocaleString('id-ID') + ' ' + item.satuan"></div>
                            </td>

                            <!-- Harga Satuan -->
                            <td class="py-4 px-6">
                                <div class="font-bold text-slate-900 text-xs" x-text="'Rp ' + Number(item.harga_satuan_terakhir).toLocaleString('id-ID')"></div>
                                <span class="text-[10px] text-slate-400">per <span x-text="item.satuan"></span></span>
                            </td>

                            <!-- Total Nilai Aset -->
                            <td class="py-4 px-6">
                                <div class="font-black text-slate-900 text-xs" x-text="'Rp ' + (Number(item.stok_tersisa) * Number(item.harga_satuan_terakhir)).toLocaleString('id-ID')"></div>
                                <template x-if="item.kategori_barang === 'saprotan'">
                                    <span class="text-[9px] font-bold text-emerald-600">Alokasi Beban Kolam</span>
                                </template>
                                <template x-if="item.kategori_barang === 'peralatan'">
                                    <span class="text-[9px] font-bold text-sky-600">Aset Modal (Capex)</span>
                                </template>
                            </td>

                            <!-- Kondisi & Lokasi -->
                            <td class="py-4 px-6">
                                <template x-if="item.kategori_barang === 'peralatan'">
                                    <div class="space-y-1">
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[10px] font-extrabold"
                                              :class="{
                                                  'bg-emerald-50 text-emerald-800 border border-emerald-200': item.kondisi_alat === 'normal',
                                                  'bg-amber-50 text-amber-800 border border-amber-200': item.kondisi_alat === 'perlu_servis',
                                                  'bg-rose-50 text-rose-800 border border-rose-200': item.kondisi_alat === 'rusak'
                                              }"
                                              x-text="item.kondisi_alat === 'normal' ? 'Normal / Beroperasi' : (item.kondisi_alat === 'perlu_servis' ? 'Perlu Servis' : 'Rusak')">
                                        </span>
                                        <div class="text-[10px] text-slate-400 truncate max-w-[150px]" x-text="item.lokasi_penyimpanan || '-'"></div>
                                    </div>
                                </template>
                                <template x-if="item.kategori_barang === 'saprotan'">
                                    <span class="text-[11px] text-slate-500 font-medium" x-text="item.lokasi_penyimpanan || 'Gudang Utama'"></span>
                                </template>
                            </td>

                            <!-- Aksi Menu (Titik 3 Vertical Dropdown) -->
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
                                            title="Menu Aksi"
                                            class="w-8 h-8 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-600 inline-flex items-center justify-center transition-colors cursor-pointer shadow-2xs">
                                        <i class="fa-solid fa-ellipsis-vertical text-sm"></i>
                                    </button>
                                    
                                    <!-- Popover Action Menu -->
                                    <div x-show="open" 
                                         @scroll.window="open = false"
                                         x-transition:enter="transition ease-out duration-100" 
                                         x-transition:enter-start="transform opacity-0 scale-95" 
                                         x-transition:enter-end="transform opacity-100 scale-100" 
                                         x-transition:leave="transition ease-in duration-75" 
                                         x-transition:leave-start="transform opacity-100 scale-100" 
                                         x-transition:leave-end="transform opacity-0 scale-95" 
                                         :style="menuStyle"
                                         class="w-52 rounded-2xl bg-white border border-slate-200 shadow-2xl p-1.5 text-left"
                                         style="display: none;">
                                        
                                        <!-- Restock Barang Masuk -->
                                        <button type="button" @click="open = false; openRestockModal(item)" class="w-full px-3 py-2 rounded-xl text-xs font-semibold text-emerald-700 hover:bg-emerald-50 flex items-center gap-2.5 transition-colors cursor-pointer">
                                            <i class="fa-solid fa-truck-ramp-box text-emerald-600 w-4"></i>
                                            <span>Restock Masuk (Beli)</span>
                                        </button>

                                        <!-- Pakai di Kolam (Khusus Saprotan) -->
                                        <template x-if="item.kategori_barang === 'saprotan'">
                                            <button type="button" @click="open = false; openPakaiModal(item)" class="w-full px-3 py-2 rounded-xl text-xs font-semibold text-sky-700 hover:bg-sky-50 flex items-center gap-2.5 transition-colors cursor-pointer">
                                                <i class="fa-solid fa-water text-sky-600 w-4"></i>
                                                <span>Pakai di Kolam</span>
                                            </button>
                                        </template>

                                        <!-- Edit Barang -->
                                        <button type="button" @click="open = false; openEditModal(item)" class="w-full px-3 py-2 rounded-xl text-xs font-semibold text-slate-700 hover:bg-amber-50 hover:text-amber-700 flex items-center gap-2.5 transition-colors cursor-pointer">
                                            <i class="fa-solid fa-pen-to-square text-amber-600 w-4"></i>
                                            <span>Edit Spesifikasi</span>
                                        </button>

                                        <div class="my-1 border-t border-slate-100"></div>

                                        <!-- Hapus Barang -->
                                        <button type="button" @click="open = false; deleteItem(item)" class="w-full px-3 py-2 rounded-xl text-xs font-semibold text-rose-600 hover:bg-rose-50 flex items-center gap-2.5 transition-colors cursor-pointer">
                                            <i class="fa-solid fa-trash-can text-rose-600 w-4"></i>
                                            <span>Hapus Barang</span>
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

    <!-- ============================================================= -->
    <!-- TAB 2: RIWAYAT MUTASI MASUK & KELUAR                          -->
    <!-- ============================================================= -->
    <div x-show="activeTab === 'riwayat'" class="bg-white rounded-3xl border border-slate-200/90 shadow-xs overflow-visible" style="display: none;">
        <div class="p-5 border-b border-slate-100 flex items-center justify-between">
            <div>
                <h3 class="text-base font-extrabold text-slate-900">Log Mutasi Inventaris Gudang</h3>
                <p class="text-xs text-slate-400">Pencatatan real-time seluruh pengadaan barang masuk dan pemakaian bahan/alat ke kolam.</p>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50/80 border-b border-slate-200/90 text-[10px] font-extrabold text-slate-400 uppercase tracking-wider">
                        <th class="py-4 px-6">NO. REFERENSI &amp; TANGGAL</th>
                        <th class="py-4 px-6">JENIS MUTASI</th>
                        <th class="py-4 px-6">NAMA BARANG</th>
                        <th class="py-4 px-6">JUMLAH</th>
                        <th class="py-4 px-6">TOTAL BIAYA</th>
                        <th class="py-4 px-6">KOLAM TUJUAN</th>
                        <th class="py-4 px-6">KETERANGAN &amp; PETUGAS</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-xs font-medium text-slate-700">
                    <template x-if="riwayatMutasi.length === 0">
                        <tr>
                            <td colspan="7" class="py-12 text-center text-slate-400 text-xs font-medium">
                                <i class="fa-solid fa-clock-rotate-left text-2xl text-slate-300 block mb-2"></i>
                                Belum ada riwayat mutasi transaksi gudang.
                            </td>
                        </tr>
                    </template>

                    <template x-for="mutasi in riwayatMutasi" :key="mutasi.id_transaksi_gudang">
                        <tr class="hover:bg-slate-50/70 transition-colors">
                            <td class="py-4 px-6">
                                <div class="font-extrabold text-slate-900 text-xs" x-text="mutasi.no_referensi"></div>
                                <span class="text-[10px] text-slate-400" x-text="mutasi.tanggal_mutasi"></span>
                            </td>

                            <td class="py-4 px-6">
                                <template x-if="mutasi.jenis_mutasi === 'masuk_pembelian'">
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-extrabold bg-emerald-50 text-emerald-800 border border-emerald-200">
                                        <i class="fa-solid fa-arrow-down-left text-[9px]"></i>
                                        <span>Masuk (Beli)</span>
                                    </span>
                                </template>
                                <template x-if="mutasi.jenis_mutasi === 'keluar_pemakaian_kolam'">
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-extrabold bg-sky-50 text-sky-800 border border-sky-200">
                                        <i class="fa-solid fa-arrow-up-right text-[9px]"></i>
                                        <span>Keluar (Kolam)</span>
                                    </span>
                                </template>
                            </td>

                            <td class="py-4 px-6 font-bold text-slate-800" x-text="mutasi.barang ? mutasi.barang.nama_barang : '-'"></td>

                            <td class="py-4 px-6 font-black" 
                                :class="mutasi.jenis_mutasi === 'masuk_pembelian' ? 'text-emerald-700' : 'text-slate-800'"
                                x-text="(mutasi.jenis_mutasi === 'masuk_pembelian' ? '+' : '-') + Number(mutasi.jumlah).toLocaleString('id-ID') + ' ' + (mutasi.barang ? mutasi.barang.satuan : '')">
                            </td>

                            <td class="py-4 px-6 font-extrabold text-slate-900" x-text="'Rp ' + Number(mutasi.total_biaya).toLocaleString('id-ID')"></td>

                            <td class="py-4 px-6">
                                <template x-if="mutasi.kolam">
                                    <span class="px-2 py-0.5 rounded-md font-extrabold text-[10px] bg-slate-100 text-slate-700 border border-slate-200" x-text="mutasi.kolam.nama_kolam"></span>
                                </template>
                                <template x-if="!mutasi.kolam">
                                    <span class="text-slate-400 italic text-[11px]">-</span>
                                </template>
                            </td>

                            <td class="py-4 px-6">
                                <div class="text-[11px] text-slate-700 font-semibold truncate max-w-xs" x-text="mutasi.keterangan || '-'"></div>
                                <span class="text-[10px] text-slate-400" x-text="'Oleh: ' + (mutasi.user ? mutasi.user.nama : 'Admin')"></span>
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>
    </div>

    <!-- ============================================================= -->
    <!-- MODAL 1: TAMBAH ITEM BARANG BARU                              -->
    <!-- ============================================================= -->
    <div x-show="modalOpen" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-95"
         class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 backdrop-blur-xs p-4"
         style="display: none;">
        
        <div @click.outside="modalOpen = false" 
             class="bg-white w-full max-w-xl rounded-3xl shadow-2xl border border-slate-100 overflow-hidden flex flex-col max-h-[90vh]">
            
            <div class="p-6 bg-[#031B4E] text-white flex items-center justify-between">
                <div>
                    <h3 class="font-extrabold text-base text-white">Daftarkan Item Barang Gudang</h3>
                    <p class="text-xs text-sky-200/80 mt-0.5">Tambah varian bahan habis pakai atau peralatan operasional tambak</p>
                </div>
                <button type="button" @click="modalOpen = false" class="w-8 h-8 rounded-full bg-white/10 hover:bg-white/20 text-white flex items-center justify-center transition-colors">
                    <i class="fa-solid fa-xmark text-sm"></i>
                </button>
            </div>

            <form @submit.prevent="submitNewItem()" class="p-6 space-y-4 overflow-y-auto text-xs">
                
                <!-- Nama Barang -->
                <div>
                    <label class="block text-[10px] font-extrabold uppercase text-slate-500 mb-1">
                        Nama Barang / Produk <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" x-model="newItem.nama_barang" required placeholder="Contoh: Kapur Dolomit Super, Pompa Celup Showfou 2HP" 
                           class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-2xl focus:bg-white focus:ring-2 focus:ring-[#0284C7] text-slate-800 font-semibold focus:outline-none">
                </div>

                <!-- Kategori & Tipe -->
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-[10px] font-extrabold uppercase text-slate-500 mb-1">
                            Kategori Barang <span class="text-rose-500">*</span>
                        </label>
                        <div class="relative">
                            <select x-model="newItem.kategori_barang" required
                                    class="w-full px-3.5 py-2.5 bg-slate-50/80 hover:bg-white border border-slate-200 rounded-2xl focus:bg-white focus:border-[#0284C7] focus:ring-2 focus:ring-[#0284C7]/20 text-slate-800 text-xs font-semibold focus:outline-none cursor-pointer appearance-none pr-9 transition-all">
                                <option value="saprotan">Bahan Habis Pakai (Saprotan)</option>
                                <option value="peralatan">Peralatan &amp; Mesin Tambak</option>
                            </select>
                            <i class="fa-solid fa-chevron-down absolute right-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-xs pointer-events-none"></i>
                        </div>
                    </div>

                    <div>
                        <label class="block text-[10px] font-extrabold uppercase text-slate-500 mb-1">
                            Satuan Kemasan <span class="text-rose-500">*</span>
                        </label>
                        <input type="text" x-model="newItem.satuan" required placeholder="Contoh: sak, botol, kg, unit, set" 
                               class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-2xl focus:bg-white focus:ring-2 focus:ring-[#0284C7] text-slate-800 font-semibold focus:outline-none">
                    </div>
                </div>

                <!-- Stok Awal & Harga Satuan -->
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-[10px] font-extrabold uppercase text-slate-500 mb-1">
                            Stok Awal Pengadaan
                        </label>
                        <input type="number" step="0.1" min="0" x-model="newItem.stok_awal" placeholder="0" 
                               class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-2xl focus:bg-white focus:ring-2 focus:ring-[#0284C7] text-slate-800 font-semibold focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-[10px] font-extrabold uppercase text-slate-500 mb-1">
                            Harga Satuan Pengadaan (Rp)
                        </label>
                        <input type="number" min="0" step="1000" x-model="newItem.harga_satuan" placeholder="0" 
                               class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-2xl focus:bg-white focus:ring-2 focus:ring-[#0284C7] text-slate-800 font-semibold focus:outline-none">
                    </div>
                </div>

                <!-- Alert Notice Finansial Otomatis -->
                <template x-if="Number(newItem.stok_awal) > 0 && Number(newItem.harga_satuan) > 0">
                    <div class="p-3 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-950 space-y-1">
                        <div class="flex items-center gap-1.5 font-bold text-xs">
                            <i class="fa-solid fa-receipt text-emerald-600"></i>
                            <span>Otomatis Masuk Buku Kas Keuangan</span>
                        </div>
                        <p class="text-[11px] text-emerald-800">
                            Pengadaan awal senilai <strong x-text="'Rp ' + (Number(newItem.stok_awal) * Number(newItem.harga_satuan)).toLocaleString('id-ID')"></strong> akan otomatis tercatat sebagai pengeluaran pada modul Keuangan.
                        </p>
                    </div>
                </template>

                <!-- Batas Minimum Stok & Lokasi Rak -->
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-[10px] font-extrabold uppercase text-slate-500 mb-1">
                            Batas Minimum Alert (Stok Kritis)
                        </label>
                        <input type="number" step="0.1" min="1" x-model="newItem.min_stok" placeholder="5" 
                               class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-2xl focus:bg-white focus:ring-2 focus:ring-[#0284C7] text-slate-800 font-semibold focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-[10px] font-extrabold uppercase text-slate-500 mb-1">
                            Lokasi Rak / Penyimpanan
                        </label>
                        <input type="text" x-model="newItem.lokasi_penyimpanan" placeholder="Contoh: Rak B-02, Pallet A1" 
                               class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-2xl focus:bg-white focus:ring-2 focus:ring-[#0284C7] text-slate-800 font-semibold focus:outline-none">
                    </div>
                </div>

                <div class="flex items-center justify-end gap-2.5 pt-3 border-t border-slate-100">
                    <button type="button" @click="modalOpen = false" 
                            class="px-4 py-2.5 rounded-xl bg-slate-100 text-slate-700 font-bold hover:bg-slate-200 transition-colors cursor-pointer">
                        Batal
                    </button>
                    <button type="submit" :disabled="isSubmitting"
                            class="px-5 py-2.5 rounded-xl bg-[#031B4E] hover:bg-sky-950 active:scale-[0.99] text-white font-extrabold shadow-md shadow-sky-950/20 transition-all cursor-pointer">
                        <span x-text="isSubmitting ? 'Menyimpan...' : 'Daftarkan Barang'"></span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- ============================================================= -->
    <!-- MODAL 2: RESTOCK / BARANG MASUK (AUTO-POST KEUANGAN)           -->
    <!-- ============================================================= -->
    <div x-show="restockModalOpen" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-95"
         class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 backdrop-blur-xs p-4"
         style="display: none;">
        
        <div @click.outside="restockModalOpen = false" 
             class="bg-white w-full max-w-lg rounded-3xl shadow-2xl border border-slate-100 overflow-hidden flex flex-col max-h-[90vh]">
            
            <div class="p-6 bg-emerald-800 text-white flex items-center justify-between">
                <div>
                    <h3 class="font-extrabold text-base text-white">Restock / Pembelian Barang Masuk</h3>
                    <p class="text-xs text-emerald-200 mt-0.5">Penambahan saldo stok &amp; otomatis tercatat di pengeluaran Keuangan</p>
                </div>
                <button type="button" @click="restockModalOpen = false" class="w-8 h-8 rounded-full bg-white/10 hover:bg-white/20 text-white flex items-center justify-center transition-colors">
                    <i class="fa-solid fa-xmark text-sm"></i>
                </button>
            </div>

            <form @submit.prevent="submitRestock()" class="p-6 space-y-4 overflow-y-auto text-xs">
                
                <!-- Info Banner Barang -->
                <div class="p-3.5 bg-slate-50 border border-slate-200 rounded-2xl flex items-center justify-between">
                    <div>
                        <strong class="text-xs font-black text-slate-900 block" x-text="selectedItem?.nama_barang"></strong>
                        <span class="text-[10px] font-bold text-slate-400" x-text="selectedItem?.kode_barang"></span>
                    </div>
                    <div class="text-right">
                        <span class="text-[10px] text-slate-400 font-bold block">SISA STOK SAAT INI</span>
                        <strong class="text-xs font-black text-slate-900" x-text="Number(selectedItem?.stok_tersisa || 0).toLocaleString('id-ID') + ' ' + (selectedItem?.satuan || '')"></strong>
                    </div>
                </div>

                <!-- Jumlah & Harga Beli -->
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-[10px] font-extrabold uppercase text-slate-500 mb-1">
                            Jumlah Restock Masuk *
                        </label>
                        <input type="number" step="0.1" min="0.1" x-model="restockForm.jumlah" required placeholder="0" 
                               class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-2xl focus:bg-white focus:ring-2 focus:ring-emerald-500 text-slate-800 font-semibold focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-[10px] font-extrabold uppercase text-slate-500 mb-1">
                            Harga Satuan Beli (Rp) *
                        </label>
                        <input type="number" min="0" step="1000" x-model="restockForm.harga_satuan" required placeholder="0" 
                               class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-2xl focus:bg-white focus:ring-2 focus:ring-emerald-500 text-slate-800 font-semibold focus:outline-none">
                    </div>
                </div>

                <!-- Total Pembelian Banner -->
                <div class="p-3.5 rounded-2xl bg-emerald-50 border border-emerald-200 flex items-center justify-between">
                    <span class="text-xs font-bold text-emerald-900">Total Pengeluaran Kas:</span>
                    <strong class="text-sm font-black text-emerald-700" 
                            x-text="'Rp ' + (Number(restockForm.jumlah || 0) * Number(restockForm.harga_satuan || 0)).toLocaleString('id-ID')">
                    </strong>
                </div>

                <!-- Tanggal & Keterangan -->
                <div>
                    <label class="block text-[10px] font-extrabold uppercase text-slate-500 mb-1">Tanggal Pembelian *</label>
                    <input type="date" x-model="restockForm.tanggal_mutasi" required
                           class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-2xl focus:bg-white focus:ring-2 focus:ring-emerald-500 text-slate-800 font-semibold focus:outline-none">
                </div>

                <div>
                    <label class="block text-[10px] font-extrabold uppercase text-slate-500 mb-1">Keterangan / Supplier</label>
                    <input type="text" x-model="restockForm.keterangan" placeholder="Contoh: Beli dari Toko Tani Maju, Faktur #INV-99" 
                           class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-2xl focus:bg-white focus:ring-2 focus:ring-emerald-500 text-slate-800 font-semibold focus:outline-none">
                </div>

                <div class="flex items-center justify-end gap-2.5 pt-3 border-t border-slate-100">
                    <button type="button" @click="restockModalOpen = false" 
                            class="px-4 py-2.5 rounded-xl bg-slate-100 text-slate-700 font-bold hover:bg-slate-200 transition-colors cursor-pointer">
                        Batal
                    </button>
                    <button type="submit" :disabled="isSubmitting"
                            class="px-5 py-2.5 rounded-xl bg-emerald-700 hover:bg-emerald-800 active:scale-[0.99] text-white font-extrabold shadow-md shadow-emerald-900/20 transition-all cursor-pointer">
                        <span x-text="isSubmitting ? 'Menyimpan...' : 'Simpan & Bukukan Kas'"></span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- ============================================================= -->
    <!-- MODAL 3: PAKAI BARANG KE KOLAM (BARANG KELUAR)                -->
    <!-- ============================================================= -->
    <div x-show="pakaiModalOpen" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-95"
         class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 backdrop-blur-xs p-4"
         style="display: none;">
        
        <div @click.outside="pakaiModalOpen = false" 
             class="bg-white w-full max-w-lg rounded-3xl shadow-2xl border border-slate-100 overflow-hidden flex flex-col max-h-[90vh]">
            
            <div class="p-6 bg-[#0284C7] text-white flex items-center justify-between">
                <div>
                    <h3 class="font-extrabold text-base text-white">Catat Pemakaian Bahan ke Kolam</h3>
                    <p class="text-xs text-sky-100 mt-0.5">Pengurangan stok gudang dialokasikan ke HPP kolam terpilih</p>
                </div>
                <button type="button" @click="pakaiModalOpen = false" class="w-8 h-8 rounded-full bg-white/10 hover:bg-white/20 text-white flex items-center justify-center transition-colors">
                    <i class="fa-solid fa-xmark text-sm"></i>
                </button>
            </div>

            <form @submit.prevent="submitPakai()" class="p-6 space-y-4 overflow-y-auto text-xs">
                
                <!-- Info Banner Barang -->
                <div class="p-3.5 bg-slate-50 border border-slate-200 rounded-2xl flex items-center justify-between">
                    <div>
                        <strong class="text-xs font-black text-slate-900 block" x-text="selectedItem?.nama_barang"></strong>
                        <span class="text-[10px] font-bold text-slate-400" x-text="selectedItem?.kode_barang"></span>
                    </div>
                    <div class="text-right">
                        <span class="text-[10px] text-slate-400 font-bold block">SISA STOK GUDANG</span>
                        <strong class="text-xs font-black text-emerald-700" x-text="Number(selectedItem?.stok_tersisa || 0).toLocaleString('id-ID') + ' ' + (selectedItem?.satuan || '')"></strong>
                    </div>
                </div>

                <!-- Pilih Kolam Tujuan -->
                <div>
                    <label class="block text-[10px] font-extrabold uppercase text-slate-500 mb-1">Kolam Tujuan Pemakaian *</label>
                    <div class="relative">
                        <select x-model="pakaiForm.id_kolam" required
                                class="w-full px-3.5 py-2.5 bg-slate-50/80 hover:bg-white border border-slate-200 rounded-2xl focus:bg-white focus:border-[#0284C7] focus:ring-2 focus:ring-[#0284C7]/20 text-slate-800 text-xs font-semibold focus:outline-none cursor-pointer appearance-none pr-9 transition-all">
                            <option value="">Pilih Kolam...</option>
                            <template x-for="k in kolamList" :key="k.id_kolam">
                                <option :value="k.id_kolam" x-text="k.nama_kolam + ' (' + k.tipe_kolam + ')'"></option>
                            </template>
                        </select>
                        <i class="fa-solid fa-chevron-down absolute right-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-xs pointer-events-none"></i>
                    </div>
                </div>

                <!-- Jumlah Pemakaian & Tanggal -->
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-[10px] font-extrabold uppercase text-slate-500 mb-1">Jumlah Pakai *</label>
                        <input type="number" step="0.1" min="0.1" :max="selectedItem?.stok_tersisa" x-model="pakaiForm.jumlah" required placeholder="0" 
                               class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-2xl focus:bg-white focus:ring-2 focus:ring-sky-500 text-slate-800 font-semibold focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-[10px] font-extrabold uppercase text-slate-500 mb-1">Tanggal Pemakaian *</label>
                        <input type="date" x-model="pakaiForm.tanggal_mutasi" required
                               class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-2xl focus:bg-white focus:ring-2 focus:ring-sky-500 text-slate-800 font-semibold focus:outline-none">
                    </div>
                </div>

                <!-- Keterangan Aplikasi -->
                <div>
                    <label class="block text-[10px] font-extrabold uppercase text-slate-500 mb-1">Tujuan / Dosis Aplikasi</label>
                    <input type="text" x-model="pakaiForm.keterangan" placeholder="Contoh: Pengapuran pasca hujan, aplikasi probiotik mingguan" 
                           class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-2xl focus:bg-white focus:ring-2 focus:ring-sky-500 text-slate-800 font-semibold focus:outline-none">
                </div>

                <div class="flex items-center justify-end gap-2.5 pt-3 border-t border-slate-100">
                    <button type="button" @click="pakaiModalOpen = false" 
                            class="px-4 py-2.5 rounded-xl bg-slate-100 text-slate-700 font-bold hover:bg-slate-200 transition-colors cursor-pointer">
                        Batal
                    </button>
                    <button type="submit" :disabled="isSubmitting"
                            class="px-5 py-2.5 rounded-xl bg-[#0284C7] hover:bg-sky-700 active:scale-[0.99] text-white font-extrabold shadow-md shadow-sky-900/20 transition-all cursor-pointer">
                        <span x-text="isSubmitting ? 'Memproses...' : 'Catat Pemakaian'"></span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- ============================================================= -->
    <!-- MODAL 4: EDIT SPESIFIKASI BARANG                              -->
    <!-- ============================================================= -->
    <div x-show="editModalOpen" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-95"
         class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 backdrop-blur-xs p-4"
         style="display: none;">
        
        <div @click.outside="editModalOpen = false" 
             class="bg-white w-full max-w-lg rounded-3xl shadow-2xl border border-slate-100 overflow-hidden flex flex-col max-h-[90vh]">
            
            <div class="p-6 bg-[#031B4E] text-white flex items-center justify-between">
                <div>
                    <h3 class="font-extrabold text-base text-white">Edit Data Barang</h3>
                    <p class="text-xs text-sky-200/80 mt-0.5" x-text="editItem?.kode_barang"></p>
                </div>
                <button type="button" @click="editModalOpen = false" class="w-8 h-8 rounded-full bg-white/10 hover:bg-white/20 text-white flex items-center justify-center transition-colors">
                    <i class="fa-solid fa-xmark text-sm"></i>
                </button>
            </div>

            <form @submit.prevent="submitEdit()" class="p-6 space-y-4 overflow-y-auto text-xs">
                <div>
                    <label class="block text-[10px] font-extrabold uppercase text-slate-500 mb-1">Nama Barang *</label>
                    <input type="text" x-model="editItem.nama_barang" required
                           class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-2xl focus:bg-white focus:ring-2 focus:ring-[#0284C7] text-slate-800 font-semibold focus:outline-none">
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-[10px] font-extrabold uppercase text-slate-500 mb-1">Satuan Kemasan *</label>
                        <input type="text" x-model="editItem.satuan" required
                               class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-2xl focus:bg-white focus:ring-2 focus:ring-[#0284C7] text-slate-800 font-semibold focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-[10px] font-extrabold uppercase text-slate-500 mb-1">Batas Minimum Alert</label>
                        <input type="number" step="0.1" min="1" x-model="editItem.min_stok" required
                               class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-2xl focus:bg-white focus:ring-2 focus:ring-[#0284C7] text-slate-800 font-semibold focus:outline-none">
                    </div>
                </div>

                <!-- Khusus Peralatan: Status Kondisi -->
                <template x-if="editItem.kategori_barang === 'peralatan'">
                    <div>
                        <label class="block text-[10px] font-extrabold uppercase text-slate-500 mb-1">Kondisi Fisik Alat</label>
                        <div class="relative">
                            <select x-model="editItem.kondisi_alat" 
                                    class="w-full px-3.5 py-2.5 bg-slate-50/80 hover:bg-white border border-slate-200 rounded-2xl focus:bg-white focus:border-[#0284C7] focus:ring-2 focus:ring-[#0284C7]/20 text-slate-800 text-xs font-semibold focus:outline-none cursor-pointer appearance-none pr-9 transition-all">
                                <option value="normal">Normal / Beroperasi Baik</option>
                                <option value="perlu_servis">Perlu Servis / Perbaikan</option>
                                <option value="rusak">Rusak / Tidak Dapat Dipakai</option>
                            </select>
                            <i class="fa-solid fa-chevron-down absolute right-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-xs pointer-events-none"></i>
                        </div>
                    </div>
                </template>

                <div>
                    <label class="block text-[10px] font-extrabold uppercase text-slate-500 mb-1">Lokasi Penyimpanan</label>
                    <input type="text" x-model="editItem.lokasi_penyimpanan"
                           class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-2xl focus:bg-white focus:ring-2 focus:ring-[#0284C7] text-slate-800 font-semibold focus:outline-none">
                </div>

                <div>
                    <label class="block text-[10px] font-extrabold uppercase text-slate-500 mb-1">Catatan Tambahan</label>
                    <textarea x-model="editItem.catatan" rows="2"
                              class="w-full px-3.5 py-2 bg-slate-50 border border-slate-200 rounded-2xl focus:bg-white focus:ring-2 focus:ring-[#0284C7] text-slate-800 font-semibold focus:outline-none"></textarea>
                </div>

                <div class="flex items-center justify-end gap-2.5 pt-3 border-t border-slate-100">
                    <button type="button" @click="editModalOpen = false" 
                            class="px-4 py-2.5 rounded-xl bg-slate-100 text-slate-700 font-bold hover:bg-slate-200 transition-colors cursor-pointer">
                        Batal
                    </button>
                    <button type="submit" :disabled="isSubmitting"
                            class="px-5 py-2.5 rounded-xl bg-[#031B4E] hover:bg-sky-950 active:scale-[0.99] text-white font-extrabold shadow-md shadow-sky-950/20 transition-all cursor-pointer">
                        <span x-text="isSubmitting ? 'Menyimpan...' : 'Perbarui Data'"></span>
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
function gudangComponent() {
    return {
        activeTab: 'katalog',
        filterKategori: 'semua',
        statusFilter: 'semua',
        openStatusFilter: false,
        isSubmitting: false,

        modalOpen: false,
        restockModalOpen: false,
        pakaiModalOpen: false,
        editModalOpen: false,

        items: {!! json_encode($items ?? []) !!},
        riwayatMutasi: {!! json_encode($riwayatMutasi ?? []) !!},
        kolamList: {!! json_encode($kolamList ?? []) !!},
        petugasList: {!! json_encode($petugasList ?? []) !!},

        selectedItem: null,
        editItem: {},

        newItem: {
            nama_barang: '',
            kategori_barang: 'saprotan',
            tipe_saprotan: 'obat_kimia',
            satuan: 'sak',
            stok_awal: 10,
            harga_satuan: 65000,
            min_stok: 5,
            lokasi_penyimpanan: 'Gudang Utama',
            catatan: ''
        },

        restockForm: {
            id_barang: '',
            jumlah: 10,
            harga_satuan: 0,
            tanggal_mutasi: new Date().toISOString().split('T')[0],
            keterangan: ''
        },

        pakaiForm: {
            id_barang: '',
            id_kolam: '',
            jumlah: 1,
            tanggal_mutasi: new Date().toISOString().split('T')[0],
            keterangan: ''
        },

        get filteredItems() {
            return this.items.filter(item => {
                // Filter Kategori
                const matchKategori = this.filterKategori === 'semua' || item.kategori_barang === this.filterKategori;

                // Filter Status Stok
                let matchStatus = true;
                if (this.statusFilter === 'aman') {
                    matchStatus = Number(item.stok_tersisa) > Number(item.min_stok * 1.5);
                } else if (this.statusFilter === 'waspada') {
                    matchStatus = Number(item.stok_tersisa) > Number(item.min_stok) && Number(item.stok_tersisa) <= Number(item.min_stok * 1.5);
                } else if (this.statusFilter === 'kritis') {
                    matchStatus = Number(item.stok_tersisa) <= Number(item.min_stok);
                }

                return matchKategori && matchStatus;
            });
        },

        openCreateModal() {
            this.newItem = {
                nama_barang: '',
                kategori_barang: 'saprotan',
                tipe_saprotan: 'desinfektan_kapur',
                satuan: 'sak',
                stok_awal: 10,
                harga_satuan: 65000,
                min_stok: 5,
                lokasi_penyimpanan: 'Gudang Utama - Rak A1',
                catatan: ''
            };
            this.modalOpen = true;
        },

        openRestockModal(item) {
            this.selectedItem = item;
            this.restockForm = {
                id_barang: item.id_barang,
                jumlah: 10,
                harga_satuan: item.harga_satuan_terakhir || 50000,
                tanggal_mutasi: new Date().toISOString().split('T')[0],
                keterangan: 'Restock pembelian ' + item.nama_barang
            };
            this.restockModalOpen = true;
        },

        openPakaiModal(item) {
            this.selectedItem = item;
            this.pakaiForm = {
                id_barang: item.id_barang,
                id_kolam: this.kolamList.length > 0 ? this.kolamList[0].id_kolam : '',
                jumlah: 1,
                tanggal_mutasi: new Date().toISOString().split('T')[0],
                keterangan: 'Aplikasi ' + item.nama_barang
            };
            this.pakaiModalOpen = true;
        },

        openEditModal(item) {
            this.selectedItem = item;
            this.editItem = JSON.parse(JSON.stringify(item));
            this.editModalOpen = true;
        },

        async submitNewItem() {
            if (!this.newItem.nama_barang) return;
            this.isSubmitting = true;

            try {
                const res = await fetch('{{ route('gudang.store') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify(this.newItem)
                });
                const data = await res.json();
                if (res.ok && data.success) {
                    if (data.barang) {
                        this.items.unshift(data.barang);
                    }
                    this.modalOpen = false;
                    if (typeof triggerToast === 'function') {
                        triggerToast(data.message, 'success');
                    } else {
                        alert(data.message);
                    }
                } else {
                    const msg = data.message || 'Gagal mendaftarkan barang baru.';
                    if (typeof triggerToast === 'function') triggerToast(msg, 'error'); else alert(msg);
                }
            } catch (err) {
                if (typeof triggerToast === 'function') triggerToast('Terjadi kesalahan koneksi.', 'error');
            } finally {
                this.isSubmitting = false;
            }
        },

        async submitRestock() {
            if (!this.restockForm.jumlah || !this.restockForm.harga_satuan) return;
            this.isSubmitting = true;

            try {
                const res = await fetch('{{ route('gudang.masuk') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify(this.restockForm)
                });
                const data = await res.json();
                if (res.ok && data.success) {
                    const idx = this.items.findIndex(i => i.id_barang === this.selectedItem.id_barang);
                    if (idx !== -1) {
                        this.items[idx].stok_tersisa = Number(this.items[idx].stok_tersisa) + Number(this.restockForm.jumlah);
                        this.items[idx].harga_satuan_terakhir = Number(this.restockForm.harga_satuan);
                    }
                    if (data.log) {
                        this.riwayatMutasi.unshift(data.log);
                    }
                    this.restockModalOpen = false;
                    if (typeof triggerToast === 'function') {
                        triggerToast(data.message, 'success');
                    } else {
                        alert(data.message);
                    }
                } else {
                    const msg = data.message || 'Gagal mencatat restock.';
                    if (typeof triggerToast === 'function') triggerToast(msg, 'error'); else alert(msg);
                }
            } catch (err) {
                if (typeof triggerToast === 'function') triggerToast('Terjadi kesalahan jaringan.', 'error');
            } finally {
                this.isSubmitting = false;
            }
        },

        async submitPakai() {
            if (!this.pakaiForm.jumlah) return;
            this.isSubmitting = true;

            try {
                const res = await fetch('{{ route('gudang.keluar') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify(this.pakaiForm)
                });
                const data = await res.json();
                if (res.ok && data.success) {
                    const idx = this.items.findIndex(i => i.id_barang === this.selectedItem.id_barang);
                    if (idx !== -1) {
                        this.items[idx].stok_tersisa = Number(this.items[idx].stok_tersisa) - Number(this.pakaiForm.jumlah);
                    }
                    if (data.log) {
                        this.riwayatMutasi.unshift(data.log);
                    }
                    this.pakaiModalOpen = false;
                    if (typeof triggerToast === 'function') {
                        triggerToast(data.message, 'success');
                    } else {
                        alert(data.message);
                    }
                } else {
                    const msg = data.message || 'Gagal mencatat pemakaian barang.';
                    if (typeof triggerToast === 'function') triggerToast(msg, 'error'); else alert(msg);
                }
            } catch (err) {
                if (typeof triggerToast === 'function') triggerToast('Terjadi kesalahan jaringan.', 'error');
            } finally {
                this.isSubmitting = false;
            }
        },

        async submitEdit() {
            if (!this.editItem || !this.editItem.id_barang) return;
            this.isSubmitting = true;

            try {
                const res = await fetch('{{ url('gudang') }}/' + this.editItem.id_barang, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify(this.editItem)
                });
                const data = await res.json();
                if (res.ok && data.success) {
                    const idx = this.items.findIndex(i => i.id_barang === this.editItem.id_barang);
                    if (idx !== -1) {
                        this.items[idx].nama_barang = this.editItem.nama_barang;
                        this.items[idx].satuan = this.editItem.satuan;
                        this.items[idx].min_stok = this.editItem.min_stok;
                        this.items[idx].kondisi_alat = this.editItem.kondisi_alat;
                        this.items[idx].lokasi_penyimpanan = this.editItem.lokasi_penyimpanan;
                        this.items[idx].catatan = this.editItem.catatan;
                    }
                    this.editModalOpen = false;
                    if (typeof triggerToast === 'function') {
                        triggerToast(data.message, 'success');
                    } else {
                        alert(data.message);
                    }
                } else {
                    const msg = data.message || 'Gagal memperbarui data barang.';
                    if (typeof triggerToast === 'function') triggerToast(msg, 'error'); else alert(msg);
                }
            } catch (err) {
                if (typeof triggerToast === 'function') triggerToast('Terjadi kesalahan jaringan.', 'error');
            } finally {
                this.isSubmitting = false;
            }
        },

        async deleteItem(item) {
            const confirmed = await (typeof AppSwal !== 'undefined'
                ? AppSwal.confirmDelete({
                    title: 'Hapus ' + item.nama_barang + '?',
                    text: 'Apakah Anda yakin ingin menghapus barang ini dari gudang? Seluruh riwayat mutasinya juga akan terhapus.',
                    confirmText: 'Ya, Hapus Barang',
                    cancelText: 'Batal'
                })
                : Promise.resolve({ isConfirmed: confirm('Hapus barang ' + item.nama_barang + '?') }));

            if (!confirmed.isConfirmed) return;

            try {
                const res = await fetch('{{ url('gudang') }}/' + item.id_barang, {
                    method: 'DELETE',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                });
                const data = await res.json();
                if (res.ok && data.success) {
                    this.items = this.items.filter(i => i.id_barang !== item.id_barang);
                    if (typeof triggerToast === 'function') {
                        triggerToast(data.message, 'success');
                    } else {
                        alert(data.message);
                    }
                } else {
                    const msg = data.message || 'Gagal menghapus barang.';
                    if (typeof triggerToast === 'function') triggerToast(msg, 'error'); else alert(msg);
                }
            } catch (err) {
                if (typeof triggerToast === 'function') triggerToast('Terjadi kesalahan server.', 'error');
            }
        }
    };
}
</script>
@endpush

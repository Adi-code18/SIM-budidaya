@extends('layouts.app')

@section('title', 'Master Stok Pakan - AMS BUDIDAYA')

@section('content')
<div class="space-y-6" x-data="stokPakanComponent()">

    <!-- Header & Action Buttons -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">Master Stok Pakan</h1>
            <p class="text-xs sm:text-sm text-slate-500 font-medium mt-1">
                Katalog master data pakan ikan, batas minimum gudang, serta pemantauan restock dari mitra supplier.
            </p>
        </div>
        
        <div class="flex items-center gap-2.5">
            <button type="button" @click="openBeliModal()"
                    class="px-4 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs sm:text-sm shadow-md shadow-emerald-600/20 transition-all flex items-center justify-center gap-2 cursor-pointer active:scale-98">
                <i class="fa-solid fa-cart-plus text-xs"></i>
                <span>Beli / Restock Pakan</span>
            </button>

            <button type="button" @click="openCreateModal()"
                    class="px-4 py-2.5 rounded-xl bg-[#031B4E] hover:bg-sky-950 text-white font-bold text-xs sm:text-sm shadow-md shadow-sky-950/20 transition-all flex items-center justify-center gap-2 cursor-pointer active:scale-98">
                <i class="fa-solid fa-circle-plus text-xs"></i>
                <span>Tambah Item Pakan</span>
            </button>
        </div>
    </div>

    <!-- Main Table Container -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-visible">
        
        <!-- Filter & Search Toolbar -->
        <div class="p-5 border-b border-slate-100 flex flex-col md:flex-row md:items-center justify-between gap-4 bg-slate-50/50 rounded-t-2xl">
            
            <!-- Search Input -->
            <div class="relative flex-1 max-w-md">
                <i class="fa-solid fa-magnifying-glass absolute left-3.5 top-3.5 text-slate-400 text-xs"></i>
                <input type="text" x-model="searchQuery"
                       placeholder="Cari nama pakan, kode pakan, atau satuan..."
                       class="w-full pl-9 pr-4 py-2.5 rounded-xl border border-slate-200 bg-white text-xs font-semibold text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-sky-500 focus:border-sky-500 transition-all shadow-xs">
            </div>

            <!-- Filter Badges / Selects -->
            <div class="flex flex-wrap items-center gap-2">
                <!-- Filter Kategori Peruntukan -->
                <div class="inline-flex p-1 bg-white border border-slate-200 rounded-xl text-xs font-bold shadow-xs">
                    <button type="button" @click="filterKategori = 'semua'"
                            :class="filterKategori === 'semua' ? 'bg-[#031B4E] text-white shadow-xs' : 'text-slate-600 hover:text-slate-900'"
                            class="px-3 py-1.5 rounded-lg transition-all cursor-pointer">
                        Semua (<span x-text="stokList.length"></span>)
                    </button>
                    <button type="button" @click="filterKategori = 'pembibitan'"
                            :class="filterKategori === 'pembibitan' ? 'bg-emerald-700 text-white shadow-xs' : 'text-slate-600 hover:text-emerald-700'"
                            class="px-3 py-1.5 rounded-lg transition-all cursor-pointer flex items-center gap-1.5">
                        <i class="fa-solid fa-seedling text-[10px]"></i>
                        <span>Pembibitan</span>
                    </button>
                    <button type="button" @click="filterKategori = 'pembesaran'"
                            :class="filterKategori === 'pembesaran' ? 'bg-blue-700 text-white shadow-xs' : 'text-slate-600 hover:text-blue-700'"
                            class="px-3 py-1.5 rounded-lg transition-all cursor-pointer flex items-center gap-1.5">
                        <i class="fa-solid fa-fish text-[10px]"></i>
                        <span>Pembesaran</span>
                    </button>
                </div>

                <!-- Filter Status Stok -->
                <select x-model="filterStatus"
                        class="px-3.5 py-2 rounded-xl border border-slate-200 text-xs font-bold text-slate-700 bg-white focus:outline-none focus:ring-2 focus:ring-sky-500 transition-all cursor-pointer shadow-xs">
                    <option value="semua">Semua Status Stok</option>
                    <option value="aman">🟢 Stok Aman</option>
                    <option value="waspada">🟡 Perlu Pesan</option>
                    <option value="kritis">🔴 Kritis (Segera Restock)</option>
                </select>
            </div>

        </div>

        <!-- Table Data -->
        <div class="overflow-x-auto overflow-y-visible">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50/70 border-b border-slate-200/80 text-[10px] font-extrabold text-slate-400 uppercase tracking-wider">
                        <th class="py-4 px-5">KODE</th>
                        <th class="py-4 px-5">NAMA PAKAN</th>
                        <th class="py-4 px-5">PERUNTUKAN</th>
                        <th class="py-4 px-5">STOK TERSISA</th>
                        <th class="py-4 px-5">BATAS MINIMUM</th>
                        <th class="py-4 px-5">STATUS STOK</th>
                        <th class="py-4 px-5">HARGA ACUAN</th>
                        <th class="py-4 px-5 text-right">AKSI</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-xs font-medium text-slate-700">
                    
                    <template x-if="paginatedList.length === 0">
                        <tr>
                            <td colspan="8" class="py-12 text-center text-slate-400 text-xs font-medium">
                                <i class="fa-solid fa-box-open text-3xl text-slate-300 block mb-2"></i>
                                Tidak ada data master pakan yang sesuai pencarian atau filter.<br>
                                <span class="text-[11px] text-slate-400">Klik tombol <strong>Tambah Item Pakan</strong> di atas untuk menambahkan item baru.</span>
                            </td>
                        </tr>
                    </template>

                    <template x-for="(item, index) in paginatedList" :key="item.id_stok_pakan">
                        <tr class="hover:bg-slate-50/80 transition-colors">
                            
                            <!-- Kode Pakan -->
                            <td class="py-4 px-5">
                                <span class="font-black text-[#031B4E] bg-slate-100 px-2.5 py-1 rounded-md text-[11px] border border-slate-200" x-text="item.kode_pakan"></span>
                            </td>

                            <!-- Nama Pakan -->
                            <td class="py-4 px-5">
                                <span class="font-extrabold text-slate-900 block text-xs" x-text="item.nama_pakan"></span>
                                <span class="text-[10px] text-slate-400 font-normal mt-0.5 block" x-text="item.keterangan || '-'"></span>
                            </td>

                            <!-- Kategori Peruntukan -->
                            <td class="py-4 px-5">
                                <template x-if="item.kategori_peruntukan === 'pembibitan'">
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-extrabold bg-emerald-50 text-emerald-800 border border-emerald-200">
                                        <i class="fa-solid fa-seedling text-[9px] text-emerald-600"></i>
                                        <span>Pembibitan</span>
                                    </span>
                                </template>
                                <template x-if="item.kategori_peruntukan === 'pembesaran'">
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-extrabold bg-blue-50 text-blue-800 border border-blue-200">
                                        <i class="fa-solid fa-fish text-[9px] text-blue-600"></i>
                                        <span>Pembesaran</span>
                                    </span>
                                </template>
                                <template x-if="item.kategori_peruntukan === 'semua'">
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-extrabold bg-slate-100 text-slate-700 border border-slate-200">
                                        <i class="fa-solid fa-boxes-stacked text-[9px] text-slate-500"></i>
                                        <span>Semua Fase</span>
                                    </span>
                                </template>
                            </td>

                            <!-- Stok Tersisa -->
                            <td class="py-4 px-5">
                                <div class="flex items-baseline gap-1">
                                    <span class="text-sm font-black text-slate-900" x-text="Number(item.stok_tersisa).toLocaleString('id-ID', { minimumFractionDigits: 1, maximumFractionDigits: 1 })"></span>
                                    <span class="text-[10px] font-extrabold text-slate-400 uppercase" x-text="item.satuan"></span>
                                </div>
                            </td>

                            <!-- Batas Minimum -->
                            <td class="py-4 px-5 font-bold text-slate-600">
                                <span x-text="Number(item.batas_minimum).toLocaleString('id-ID') + ' ' + item.satuan"></span>
                            </td>

                            <!-- Status Stok -->
                            <td class="py-4 px-5">
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-extrabold border" :class="item.status_badge">
                                    <span class="w-1.5 h-1.5 rounded-full" :class="item.dot_class"></span>
                                    <span x-text="item.status_label"></span>
                                </span>
                            </td>

                            <!-- Harga Acuan -->
                            <td class="py-4 px-5 font-extrabold text-emerald-700">
                                <span x-text="'Rp ' + Number(item.harga_per_satuan).toLocaleString('id-ID') + '/' + item.satuan"></span>
                            </td>

                            <!-- Aksi Menu Dropdown -->
                            <td class="py-4 px-5 text-right">
                                <div class="relative inline-block text-left" 
                                     x-data="{ 
                                         open: false,
                                         menuStyle: '',
                                         toggle(e) {
                                             this.open = !this.open;
                                             if (this.open) {
                                                 const rect = this.$refs.btn.getBoundingClientRect();
                                                 const spaceBelow = window.innerHeight - rect.bottom;
                                                 const menuH = 150;
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
                                            class="w-8 h-8 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-600 inline-flex items-center justify-center transition-colors cursor-pointer">
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
                                         class="w-44 rounded-xl bg-white border border-slate-200 shadow-2xl py-1.5 z-50 text-left"
                                         style="display: none;">
                                        
                                        <!-- Opsi 1: Restock / Beli Pakan -->
                                        <button type="button" @click="open = false; quickBeli(item)" class="w-full px-3.5 py-2 text-xs font-semibold text-emerald-700 hover:bg-emerald-50 flex items-center gap-2.5 cursor-pointer">
                                            <i class="fa-solid fa-cart-plus text-emerald-600 w-4"></i>
                                            <span>Restock Pakan</span>
                                        </button>

                                        <!-- Opsi 2: Edit Master Pakan -->
                                        <button type="button" @click="open = false; openEditModal(item)" class="w-full px-3.5 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-50 flex items-center gap-2.5 cursor-pointer">
                                            <i class="fa-solid fa-pen-to-square text-amber-600 w-4"></i>
                                            <span>Edit Item</span>
                                        </button>

                                        <div class="my-1 border-t border-slate-100"></div>

                                        <!-- Opsi 3: Hapus Item -->
                                        <button type="button" @click="open = false; openDeleteModal(item)" class="w-full px-3.5 py-2 text-xs font-semibold text-rose-600 hover:bg-rose-50 flex items-center gap-2.5 cursor-pointer">
                                            <i class="fa-solid fa-trash-can text-rose-500 w-4"></i>
                                            <span>Hapus Item</span>
                                        </button>
                                    </div>
                                </div>
                            </td>

                        </tr>
                    </template>

                </tbody>
            </table>
        </div>

        <!-- Table Pagination Footer -->
        <div class="p-4 border-t border-slate-100 flex flex-col sm:flex-row items-center justify-between gap-4 text-xs font-semibold text-slate-500">
            <span x-text="'Menampilkan ' + paginatedList.length + ' dari ' + filteredList.length + ' Item Pakan'"></span>
            
            <div class="flex items-center gap-1" x-show="totalPages > 1">
                <button type="button" @click="currentPage--" :disabled="currentPage === 1"
                        :class="currentPage === 1 ? 'opacity-40 cursor-not-allowed' : 'hover:bg-slate-100 cursor-pointer'"
                        class="w-7 h-7 rounded-lg border border-slate-200 flex items-center justify-center text-slate-500">
                    &lt;
                </button>
                
                <template x-for="p in totalPages" :key="p">
                    <button type="button" @click="currentPage = p"
                            :class="currentPage === p ? 'bg-[#031B4E] text-white font-bold' : 'hover:bg-slate-100 text-slate-600'"
                            class="w-7 h-7 rounded-lg border border-slate-200 flex items-center justify-center cursor-pointer"
                            x-text="p">
                    </button>
                </template>

                <button type="button" @click="currentPage++" :disabled="currentPage === totalPages"
                        :class="currentPage === totalPages ? 'opacity-40 cursor-not-allowed' : 'hover:bg-slate-100 cursor-pointer'"
                        class="w-7 h-7 rounded-lg border border-slate-200 flex items-center justify-center text-slate-500">
                    &gt;
                </button>
            </div>
        </div>

    </div>

    <!-- ========================================================================= -->
    <!-- MODAL 1: FORM TAMBAH / EDIT MASTER ITEM PAKAN -->
    <!-- ========================================================================= -->
    <div x-show="showMasterModal"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-95"
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs"
         style="display: none;">
        
        <div class="bg-white rounded-3xl max-w-lg w-full p-6 shadow-2xl space-y-5 border border-slate-200" @click.outside="showMasterModal = false">
            
            <!-- Modal Header -->
            <div class="flex items-center justify-between border-b border-slate-100 pb-4">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-2xl bg-sky-100 text-[#031B4E] flex items-center justify-center font-bold text-sm">
                        <i class="fa-solid fa-boxes-stacked"></i>
                    </div>
                    <div>
                        <span class="text-[10px] font-extrabold uppercase text-slate-400 tracking-wider block" x-text="masterMode === 'create' ? 'Tambah Master Item' : 'Perbarui Master Item'"></span>
                        <h3 class="text-base font-extrabold text-slate-900" x-text="masterMode === 'create' ? 'Tambah Jenis Pakan Baru' : 'Edit Jenis Pakan'"></h3>
                    </div>
                </div>
                <button type="button" @click="showMasterModal = false" class="w-8 h-8 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-500 flex items-center justify-center transition-colors cursor-pointer">
                    <i class="fa-solid fa-xmark text-sm"></i>
                </button>
            </div>

            <form @submit.prevent="handleSaveMaster()" class="space-y-4">
                
                <!-- Nama Pakan -->
                <div>
                    <label class="text-[10px] font-extrabold uppercase text-slate-500 block mb-1.5">NAMA JENIS PAKAN *</label>
                    <input type="text" x-model="masterForm.nama_pakan" required placeholder="Contoh: Pelet PF-500 Starter"
                           class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-xs font-bold text-slate-800 bg-slate-50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-sky-500 transition-all">
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <!-- Kategori Peruntukan -->
                    <div>
                        <label class="text-[10px] font-extrabold uppercase text-slate-500 block mb-1.5">PERUNTUKAN FASE *</label>
                        <select x-model="masterForm.kategori_peruntukan" required
                                class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs font-bold text-slate-800 bg-slate-50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-sky-500 transition-all cursor-pointer">
                            <option value="pembibitan">🌱 Pembibitan (Hatchery)</option>
                            <option value="pembesaran">🐟 Pembesaran</option>
                            <option value="semua">📦 Semua Fase</option>
                        </select>
                    </div>

                    <!-- Satuan -->
                    <div>
                        <label class="text-[10px] font-extrabold uppercase text-slate-500 block mb-1.5">SATUAN *</label>
                        <input type="text" x-model="masterForm.satuan" required placeholder="kg / tray / sak"
                               class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs font-bold text-slate-800 bg-slate-50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-sky-500 transition-all">
                    </div>
                </div>

                <div class="grid grid-cols-3 gap-3">
                    <!-- Stok Awal / Tersisa -->
                    <div>
                        <label class="text-[10px] font-extrabold uppercase text-slate-500 block mb-1.5">TOTAL STOK JENIS INI*</label>
                        <input type="number" step="0.1" min="0" x-model="masterForm.stok_tersisa" required
                               class="w-full px-3 py-2.5 rounded-xl border border-slate-200 text-xs font-bold text-slate-800 bg-slate-50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-sky-500 transition-all">
                    </div>

                    <!-- Batas Minimum -->
                    <div>
                        <label class="text-[10px] font-extrabold uppercase text-slate-500 block mb-1.5">BATAS MIN. *</label>
                        <input type="number" step="0.1" min="0" x-model="masterForm.batas_minimum" required
                               class="w-full px-3 py-2.5 rounded-xl border border-slate-200 text-xs font-bold text-amber-700 bg-slate-50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-sky-500 transition-all">
                    </div>

                    <!-- Harga Acuan Satuan -->
                    <div>
                        <label class="text-[10px] font-extrabold uppercase text-slate-500 block mb-1.5">HARGA/SATUAN *</label>
                        <input type="number" step="100" min="0" x-model="masterForm.harga_per_satuan" required
                               class="w-full px-3 py-2.5 rounded-xl border border-slate-200 text-xs font-bold text-emerald-700 bg-slate-50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-sky-500 transition-all">
                    </div>
                </div>

                <!-- Keterangan / Deskripsi -->
                <div>
                    <label class="text-[10px] font-extrabold uppercase text-slate-500 block mb-1.5">KETERANGAN / SPESIFIKASI (OPSIONAL)</label>
                    <textarea x-model="masterForm.keterangan" rows="2" placeholder="Catatan kandungan protein, pabrikan, atau instruksi simpan..."
                              class="w-full px-3.5 py-2 rounded-xl border border-slate-200 text-xs font-medium text-slate-800 bg-slate-50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-sky-500 transition-all"></textarea>
                </div>

                <!-- Modal Actions -->
                <div class="pt-3 flex items-center justify-end gap-2.5 border-t border-slate-100">
                    <button type="button" @click="showMasterModal = false"
                            class="px-4 py-2 rounded-xl border border-slate-200 text-xs font-bold text-slate-600 hover:bg-slate-50 cursor-pointer">
                        Batal
                    </button>
                    <button type="submit" :disabled="isSubmitting"
                            class="px-5 py-2 rounded-xl bg-[#031B4E] hover:bg-sky-950 text-white font-bold text-xs flex items-center gap-2 shadow-md shadow-sky-950/20 cursor-pointer">
                        <i class="fa-solid" :class="isSubmitting ? 'fa-spinner fa-spin' : 'fa-check'"></i>
                        <span x-text="masterMode === 'create' ? 'Simpan Master Pakan' : 'Perbarui Data'"></span>
                    </button>
                </div>
            </form>

        </div>
    </div>

    <!-- ========================================================================= -->
    <!-- MODAL 2: PEMBELIAN / RESTOCK PAKAN DARI SUPPLIER -->
    <!-- ========================================================================= -->
    <div x-show="showBeliModal"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-95"
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs"
         style="display: none;">
        
        <div class="bg-white rounded-3xl max-w-lg w-full p-6 shadow-2xl space-y-5 border border-slate-200" @click.outside="showBeliModal = false">
            
            <!-- Modal Header -->
            <div class="flex items-center justify-between border-b border-slate-100 pb-4">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-2xl bg-emerald-100 text-emerald-800 flex items-center justify-center font-bold text-sm">
                        <i class="fa-solid fa-cart-shopping"></i>
                    </div>
                    <div>
                        <span class="text-[10px] font-extrabold uppercase text-slate-400 tracking-wider block">RESTOCK &amp; PENGADAAN GUDANG</span>
                        <h3 class="text-base font-extrabold text-slate-900">Catat Pembelian Pakan</h3>
                    </div>
                </div>
                <button type="button" @click="showBeliModal = false" class="w-8 h-8 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-500 flex items-center justify-center transition-colors cursor-pointer">
                    <i class="fa-solid fa-xmark text-sm"></i>
                </button>
            </div>

            <form @submit.prevent="handleSaveBeli()" class="space-y-4">
                
                <!-- Pilih Jenis Pakan -->
                <div>
                    <label class="text-[10px] font-extrabold uppercase text-slate-500 block mb-1.5">PILIH ITEM PAKAN *</label>
                    <select x-model="beliForm.id_stok_pakan" @change="onBeliPakanChange()" required
                            class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-xs font-bold text-slate-800 bg-slate-50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-emerald-600 transition-all cursor-pointer">
                        <option value="">-- Pilih Jenis Pakan Gudang --</option>
                        <template x-for="sp in stokList" :key="sp.id_stok_pakan">
                            <option :value="sp.id_stok_pakan" x-text="sp.nama_pakan + ' (Saat ini: ' + Number(sp.stok_tersisa).toFixed(1) + ' ' + sp.satuan + ')'"></option>
                        </template>
                    </select>
                </div>

                <!-- Pilih Supplier & Tombol WA -->
                <div>
                    <div class="flex items-center justify-between mb-1.5">
                        <label class="text-[10px] font-extrabold uppercase text-slate-500 block">MITRA SUPPLIER / DISTRIBUTOR</label>
                        <template x-if="selectedSupplier">
                            <a :href="selectedSupplier.wa_link" target="_blank"
                               class="text-[10px] font-extrabold text-emerald-600 hover:text-emerald-700 flex items-center gap-1 transition-colors">
                                <i class="fa-brands fa-whatsapp text-emerald-500"></i>
                                <span>Hubungi WA Supplier</span>
                            </a>
                        </template>
                    </div>
                    <select x-model="beliForm.id_mitra"
                            class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-xs font-semibold text-slate-800 bg-slate-50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-emerald-600 transition-all cursor-pointer">
                        <option value="">-- Beli Langsung / Toko Lokal --</option>
                        <template x-for="m in suppliers" :key="m.id_mitra">
                            <option :value="m.id_mitra" x-text="m.nama_mitra + ' (' + m.tipe_mitra + ')'"></option>
                        </template>
                    </select>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <!-- Tanggal Pembelian -->
                    <div>
                        <label class="text-[10px] font-extrabold uppercase text-slate-500 block mb-1.5">TANGGAL PEMBELIAN *</label>
                        <input type="date" x-model="beliForm.tgl_beli" required
                               class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs font-semibold text-slate-800 bg-slate-50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-emerald-600 transition-all">
                    </div>

                    <!-- Jumlah Pembelian -->
                    <div>
                        <label class="text-[10px] font-extrabold uppercase text-slate-500 block mb-1.5">JUMLAH PASOKAN *</label>
                        <div class="flex items-center rounded-xl border border-slate-200 bg-slate-50 focus-within:bg-white focus-within:ring-2 focus-within:ring-emerald-600 overflow-hidden">
                            <input type="number" step="0.1" min="0.1" x-model="beliForm.jumlah" @input="recalculateBeliTotal()" required
                                   class="w-full px-3.5 py-2.5 text-xs font-black text-slate-900 border-0 bg-transparent focus:outline-none">
                            <span class="px-3 py-2.5 text-xs font-black text-slate-500 bg-slate-100 border-l border-slate-200" x-text="selectedBeliPakan ? selectedBeliPakan.satuan.toUpperCase() : 'KG'"></span>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <!-- Harga Satuan -->
                    <div>
                        <label class="text-[10px] font-extrabold uppercase text-slate-500 block mb-1.5">HARGA PER SATUAN (RP) *</label>
                        <input type="number" step="100" min="0" x-model="beliForm.harga_satuan" @input="recalculateBeliTotal()" required
                               class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs font-bold text-slate-900 bg-slate-50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-emerald-600 transition-all">
                    </div>

                    <!-- Total Pengeluaran Kas -->
                    <div>
                        <label class="text-[10px] font-extrabold uppercase text-slate-500 block mb-1.5">TOTAL BIAYA (RP)</label>
                        <div class="px-3.5 py-2.5 rounded-xl border border-emerald-200 bg-emerald-50/80 flex items-center justify-between text-xs">
                            <span class="font-bold text-emerald-800">Rp</span>
                            <span class="font-black text-emerald-800 text-sm" x-text="Number(beliForm.total_biaya || 0).toLocaleString('id-ID')"></span>
                        </div>
                    </div>
                </div>

                <!-- Info Auto Bookkeeping -->
                <div class="p-3 bg-sky-50/80 rounded-xl border border-sky-100 flex items-start gap-2 text-[11px] text-sky-900">
                    <i class="fa-solid fa-circle-info text-sky-600 mt-0.5 shrink-0"></i>
                    <span>Setelah disimpan, stok gudang otomatis bertambah dan nominal pembelian langsung tercatat sebagai pengeluaran di buku kas Keuangan.</span>
                </div>

                <!-- Modal Actions -->
                <div class="pt-3 flex items-center justify-end gap-2.5 border-t border-slate-100">
                    <button type="button" @click="showBeliModal = false"
                            class="px-4 py-2 rounded-xl border border-slate-200 text-xs font-bold text-slate-600 hover:bg-slate-50 cursor-pointer">
                        Batal
                    </button>
                    <button type="submit" :disabled="isSubmitting"
                            class="px-5 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs flex items-center gap-2 shadow-md shadow-emerald-600/20 cursor-pointer">
                        <i class="fa-solid" :class="isSubmitting ? 'fa-spinner fa-spin' : 'fa-check'"></i>
                        <span>Konfirmasi Pembelian</span>
                    </button>
                </div>
            </form>

        </div>
    </div>

    <!-- ========================================================================= -->
    <!-- MODAL 3: KONFIRMASI HAPUS ITEM PAKAN -->
    <!-- ========================================================================= -->
    <div x-show="showDeleteModal"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-95"
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs"
         style="display: none;">
        
        <div class="bg-white rounded-3xl max-w-sm w-full p-6 shadow-2xl space-y-4 border border-slate-100 text-center" @click.outside="showDeleteModal = false">
            <div class="w-14 h-14 rounded-2xl bg-rose-50 text-rose-600 mx-auto flex items-center justify-center text-2xl border border-rose-100 shadow-xs">
                <i class="fa-solid fa-trash-can"></i>
            </div>
            
            <div class="space-y-1">
                <h3 class="text-base font-extrabold text-slate-900">Hapus Master Pakan?</h3>
                <p class="text-xs text-slate-500 font-medium leading-relaxed">
                    Apakah Anda yakin ingin menghapus <strong class="text-slate-900" x-text="selectedItemToDelete?.nama_pakan"></strong>? Tindakan ini tidak dapat dibatalkan.
                </p>
            </div>

            <div class="grid grid-cols-2 gap-2.5 pt-2">
                <button type="button" @click="showDeleteModal = false"
                        class="w-full py-2.5 rounded-xl border border-slate-200 text-slate-700 font-bold text-xs hover:bg-slate-50 cursor-pointer">
                    Batal
                </button>
                <button type="button" @click="executeDeleteMaster()" :disabled="isSubmitting"
                        class="w-full py-2.5 rounded-xl bg-rose-600 hover:bg-rose-700 text-white font-bold text-xs shadow-md shadow-rose-950/20 flex items-center justify-center gap-1.5 cursor-pointer">
                    <i class="fa-solid" :class="isSubmitting ? 'fa-spinner fa-spin' : 'fa-trash-can'"></i>
                    <span>Ya, Hapus</span>
                </button>
            </div>
        </div>
    </div>

    <!-- Notification Toast -->
    <div x-show="showToast"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 -translate-y-4 scale-95"
         x-transition:enter-end="opacity-100 translate-y-0 scale-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 translate-y-0 scale-100"
         x-transition:leave-end="opacity-0 -translate-y-4 scale-95"
         class="fixed top-6 right-6 z-50 max-w-sm rounded-2xl shadow-xl border p-4 flex items-center gap-3 backdrop-blur-md bg-[#051B44] text-white border-sky-500/50 shadow-sky-950/20"
         style="display: none;">
        <div class="w-8 h-8 rounded-xl bg-emerald-500 text-white flex items-center justify-center shrink-0">
            <i class="fa-solid fa-check text-sm"></i>
        </div>
        <div class="flex-1 text-xs font-bold leading-snug" x-text="toastMessage"></div>
        <button @click="showToast = false" class="text-white/70 hover:text-white transition-colors cursor-pointer">
            <i class="fa-solid fa-xmark text-xs"></i>
        </button>
    </div>

</div>
@endsection

@push('scripts')
<script>
function stokPakanComponent() {
    return {
        stokList: {!! json_encode($stokPakan ?? []) !!},
        suppliers: {!! json_encode($suppliers ?? []) !!},
        
        searchQuery: '',
        filterKategori: 'semua',
        filterStatus: 'semua',
        
        currentPage: 1,
        perPage: 10,
        
        isSubmitting: false,
        showToast: false,
        toastMessage: '',

        // Modals state
        showMasterModal: false,
        masterMode: 'create', // 'create' or 'edit'
        editItemId: null,
        masterForm: {
            nama_pakan: '',
            kategori_peruntukan: 'pembesaran',
            satuan: 'kg',
            stok_tersisa: 50,
            batas_minimum: 15,
            harga_per_satuan: 12500,
            keterangan: ''
        },

        showBeliModal: false,
        beliForm: {
            id_stok_pakan: '',
            id_mitra: '',
            tgl_beli: new Date().toISOString().split('T')[0],
            jumlah: 50,
            harga_satuan: 12500,
            total_biaya: 625000,
            keterangan: ''
        },

        showDeleteModal: false,
        selectedItemToDelete: null,

        get filteredList() {
            return this.stokList.filter(item => {
                // Filter search
                const query = this.searchQuery.toLowerCase().trim();
                const matchSearch = !query || 
                    item.nama_pakan.toLowerCase().includes(query) || 
                    item.kode_pakan.toLowerCase().includes(query) || 
                    item.satuan.toLowerCase().includes(query);

                // Filter kategori
                const matchKategori = this.filterKategori === 'semua' || 
                    item.kategori_peruntukan === this.filterKategori || 
                    item.kategori_peruntukan === 'semua';

                // Filter status
                const matchStatus = this.filterStatus === 'semua' || 
                    item.status === this.filterStatus;

                return matchSearch && matchKategori && matchStatus;
            });
        },

        get totalPages() {
            return Math.ceil(this.filteredList.length / this.perPage) || 1;
        },

        get paginatedList() {
            const start = (this.currentPage - 1) * this.perPage;
            return this.filteredList.slice(start, start + this.perPage);
        },

        get selectedBeliPakan() {
            if (!this.beliForm.id_stok_pakan) return null;
            return this.stokList.find(s => s.id_stok_pakan == this.beliForm.id_stok_pakan) || null;
        },

        get selectedSupplier() {
            if (!this.beliForm.id_mitra) return null;
            return this.suppliers.find(m => m.id_mitra == this.beliForm.id_mitra) || null;
        },

        openCreateModal() {
            this.masterMode = 'create';
            this.editItemId = null;
            this.masterForm = {
                nama_pakan: '',
                kategori_peruntukan: 'pembesaran',
                satuan: 'kg',
                stok_tersisa: 50,
                batas_minimum: 15,
                harga_per_satuan: 12500,
                keterangan: ''
            };
            this.showMasterModal = true;
        },

        openEditModal(item) {
            this.masterMode = 'edit';
            this.editItemId = item.id_stok_pakan;
            this.masterForm = {
                nama_pakan: item.nama_pakan,
                kategori_peruntukan: item.kategori_peruntukan,
                satuan: item.satuan,
                stok_tersisa: item.stok_tersisa,
                batas_minimum: item.batas_minimum,
                harga_per_satuan: item.harga_per_satuan,
                keterangan: item.keterangan || ''
            };
            this.showMasterModal = true;
        },

        openDeleteModal(item) {
            this.selectedItemToDelete = item;
            this.showDeleteModal = true;
        },

        openBeliModal() {
            this.beliForm = {
                id_stok_pakan: this.stokList.length > 0 ? this.stokList[0].id_stok_pakan : '',
                id_mitra: this.suppliers.length > 0 ? this.suppliers[0].id_mitra : '',
                tgl_beli: new Date().toISOString().split('T')[0],
                jumlah: 50,
                harga_satuan: this.stokList.length > 0 ? this.stokList[0].harga_per_satuan : 12500,
                total_biaya: (this.stokList.length > 0 ? this.stokList[0].harga_per_satuan : 12500) * 50,
                keterangan: ''
            };
            this.showBeliModal = true;
        },

        quickBeli(item) {
            this.beliForm = {
                id_stok_pakan: item.id_stok_pakan,
                id_mitra: this.suppliers.length > 0 ? this.suppliers[0].id_mitra : '',
                tgl_beli: new Date().toISOString().split('T')[0],
                jumlah: item.batas_minimum ? item.batas_minimum * 2 : 50,
                harga_satuan: item.harga_per_satuan || 12500,
                total_biaya: (item.harga_per_satuan || 12500) * (item.batas_minimum ? item.batas_minimum * 2 : 50),
                keterangan: ''
            };
            this.showBeliModal = true;
        },

        onBeliPakanChange() {
            const item = this.selectedBeliPakan;
            if (item) {
                this.beliForm.harga_satuan = item.harga_per_satuan || 12500;
                this.recalculateBeliTotal();
            }
        },

        recalculateBeliTotal() {
            const qty = Number(this.beliForm.jumlah) || 0;
            const price = Number(this.beliForm.harga_satuan) || 0;
            this.beliForm.total_biaya = Math.round(qty * price);
        },

        async handleSaveMaster() {
            if (this.isSubmitting) return;
            this.isSubmitting = true;

            const url = this.masterMode === 'create' 
                ? '{{ route('stok-pakan.store') }}' 
                : `{{ url('/stok-pakan') }}/${this.editItemId}`;
            
            const method = this.masterMode === 'create' ? 'POST' : 'PUT';

            try {
                const res = await fetch(url, {
                    method: method,
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify(this.masterForm)
                });

                const data = await res.json();
                if (res.ok && data.success) {
                    this.triggerToast(data.message || 'Master item pakan berhasil disimpan!');
                    this.showMasterModal = false;
                    setTimeout(() => { window.location.reload(); }, 600);
                } else {
                    alert(data.message || 'Gagal menyimpan data master pakan.');
                }
            } catch (err) {
                console.error(err);
                alert('Terjadi kesalahan jaringan atau sistem.');
            } finally {
                this.isSubmitting = false;
            }
        },

        async executeDeleteMaster() {
            if (!this.selectedItemToDelete || this.isSubmitting) return;
            this.isSubmitting = true;

            try {
                const res = await fetch(`{{ url('/stok-pakan') }}/${this.selectedItemToDelete.id_stok_pakan}`, {
                    method: 'DELETE',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                });

                const data = await res.json();
                if (res.ok && data.success) {
                    this.stokList = this.stokList.filter(s => s.id_stok_pakan !== this.selectedItemToDelete.id_stok_pakan);
                    this.triggerToast(data.message || 'Master item pakan berhasil dihapus!');
                    this.showDeleteModal = false;
                } else {
                    alert(data.message || 'Gagal menghapus data.');
                }
            } catch (err) {
                console.error(err);
                alert('Terjadi kesalahan jaringan atau sistem.');
            } finally {
                this.isSubmitting = false;
            }
        },

        async handleSaveBeli() {
            if (this.isSubmitting) return;
            this.isSubmitting = true;

            try {
                const res = await fetch('{{ route('stok-pakan.beli') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify(this.beliForm)
                });

                const data = await res.json();
                if (res.ok && data.success) {
                    this.triggerToast(data.message || 'Pembelian pakan berhasil dicatat!');
                    this.showBeliModal = false;
                    setTimeout(() => { window.location.reload(); }, 600);
                } else {
                    alert(data.message || 'Gagal mencatat pembelian pakan.');
                }
            } catch (err) {
                console.error(err);
                alert('Terjadi kesalahan jaringan.');
            } finally {
                this.isSubmitting = false;
            }
        },

        triggerToast(msg) {
            this.toastMessage = msg;
            this.showToast = true;
            setTimeout(() => { this.showToast = false; }, 4000);
        }
    };
}
</script>
@endpush

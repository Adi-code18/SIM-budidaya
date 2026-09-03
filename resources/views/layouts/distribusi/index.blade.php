@extends('layouts.app')

@section('title', 'Distribusi & Order - SIM-BUDIDAYA')

@section('content')
<div class="space-y-6" x-data="distribusiComponent()">

    <!-- Subtitle & Page Title Header -->
    <div class="flex items-center gap-3">
        <h1 class="text-xl font-extrabold text-[#0B2570] tracking-tight">Manajemen Distribusi</h1>
        <span class="text-xs font-semibold text-slate-400">Logistik Utama</span>
    </div>

    <!-- ========= INPUT FORM SECTION ========= -->
    <div x-show="showForm"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 -translate-y-2"
         x-transition:enter-end="opacity-100 translate-y-0"
         class="space-y-5">

        <!-- Header -->
        <div class="bg-[#051B44] rounded-2xl p-6 sm:p-8 text-white shadow-xs">
            <h2 class="text-xl font-extrabold text-white">Distribusi & Order</h2>
            <p class="text-xs text-sky-200/80 font-medium mt-1">Catat detail transaksi distribusi hasil panen atau order masuk dari mitra. Pastikan ID Mitra terdaftar dan valid sebelum menyimpan.</p>
        </div>

        <div class="bg-white p-6 sm:p-8 rounded-2xl border border-slate-200/80 shadow-xs space-y-6">

            <form action="#" method="POST" @submit.prevent class="space-y-6">

                <!-- Section 1: Informasi Utama -->
                <div class="space-y-4">
                    <div class="flex items-center gap-2.5 text-sm font-bold text-slate-900">
                        <div class="w-8 h-8 rounded-xl bg-[#051B44] text-white flex items-center justify-center">
                            <i class="fa-solid fa-receipt text-xs"></i>
                        </div>
                        <span>Informasi Utama</span>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="text-[10px] font-extrabold uppercase tracking-wider text-slate-500 block mb-1">ID TRANSAKSI</label>
                            <input type="text" x-model="form.id" :readonly="formMode === 'edit'" :disabled="formMode === 'edit'"
                                   class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs font-extrabold text-slate-500 bg-slate-100 cursor-not-allowed">
                        </div>
                        <div>
                            <label class="text-[10px] font-extrabold uppercase tracking-wider text-slate-500 block mb-1">TANGGAL ORDER</label>
                            <input type="date" x-model="form.tanggal" :max="maxDate" :disabled="formMode === 'edit'"
                                   class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs font-semibold text-slate-700 bg-slate-50/70 focus:bg-white focus:outline-none focus:ring-2 focus:ring-sky-500 transition-all">
                        </div>
                    </div>
                </div>

                <!-- Section 2: Detail Mitra & Order -->
                <div class="space-y-4">
                    <div class="flex items-center gap-2.5 text-sm font-bold text-slate-900">
                        <div class="w-8 h-8 rounded-xl bg-[#0284C7] text-white flex items-center justify-center">
                            <i class="fa-solid fa-handshake text-xs"></i>
                        </div>
                        <span>Detail Mitra & Order</span>
                    </div>

                    <div>
                        <label class="text-[10px] font-extrabold uppercase tracking-wider text-slate-500 block mb-1">ID MITRA / NAMA MITRA (DATABASE MITRA) *</label>
                        <select x-model="form.id_mitra" @change="onMitraSelected()" :disabled="formMode === 'edit'" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs font-semibold text-slate-700 bg-slate-50/70 focus:bg-white focus:outline-none focus:ring-2 focus:ring-sky-500 transition-all">
                            <option value="">Pilih Mitra Distributor dari Database...</option>
                            <template x-for="m in mitraList" :key="m.id_mitra">
                                <option :value="m.id_mitra" x-text="m.label"></option>
                            </template>
                        </select>
                    </div>

                    <!-- Pilih Sumber Batch Pembesaran & Jenis Ikan -->
                    <div class="p-3.5 bg-sky-50/70 rounded-xl border border-sky-100 space-y-2">
                        <div class="flex items-center justify-between">
                            <label class="text-[10px] font-extrabold uppercase tracking-wider text-sky-900 block">SUMBER BATCH PEMBESARAN &amp; KOMODITAS IKAN *</label>
                            <a href="{{ route('pembesaran') }}" target="_blank" class="text-[10px] font-bold text-sky-700 hover:text-sky-900 flex items-center gap-1">
                                <i class="fa-solid fa-arrow-up-right-from-square text-[9px]"></i>
                                <span>Cek Kolam Pembesaran</span>
                            </a>
                        </div>
                        <select x-model="form.id_pembesaran" @change="onBatchSelected()" :disabled="formMode === 'edit'" class="w-full px-3.5 py-2.5 rounded-xl border border-sky-200 text-xs font-semibold text-slate-800 bg-white focus:outline-none focus:ring-2 focus:ring-sky-500">
                            <option value="">-- Pilih Batch Pembesaran / Jenis Ikan yang Tersedia --</option>
                            <template x-for="b in batches" :key="b.id_pembesaran">
                                <option :value="b.id_pembesaran" x-text="b.label"></option>
                            </template>
                        </select>
                        <div x-show="form.jenis_ikan" class="flex flex-wrap items-center gap-2 pt-1">
                            <span class="px-2.5 py-1 rounded-lg bg-sky-100 text-sky-800 border border-sky-200 text-[11px] font-extrabold flex items-center gap-1.5">
                                <i class="fa-solid fa-fish text-sky-600"></i>
                                <span>Jenis Ikan: <strong x-text="form.jenis_ikan"></strong></span>
                            </span>
                            <span x-show="form.kolam_asal" class="px-2.5 py-1 rounded-lg bg-white border border-slate-200 text-slate-700 text-[11px] font-semibold flex items-center gap-1">
                                <i class="fa-solid fa-warehouse text-slate-400"></i>
                                <span>Kolam: <strong x-text="form.kolam_asal"></strong></span>
                            </span>
                            <span x-show="form.stok_biomassa" class="px-2.5 py-1 rounded-lg bg-emerald-50 border border-emerald-200 text-emerald-800 text-[11px] font-bold">
                                Stok Siap: <span x-text="Number(form.stok_biomassa).toLocaleString('id-ID') + ' kg'"></span>
                            </span>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="text-[10px] font-extrabold uppercase tracking-wider text-slate-500 block mb-1.5">JENIS ORDER</label>
                            <div class="flex items-center gap-1.5 p-1 bg-slate-100 rounded-xl text-xs font-bold">
                                <button type="button" @click="jenisOrder = 'reguler'; form.jenisOrder = 'reguler'" :disabled="formMode === 'edit'"
                                        :class="jenisOrder === 'reguler' ? 'bg-[#051B44] text-white shadow-xs' : 'text-slate-600 hover:text-slate-900'"
                                        class="flex-1 py-1.5 rounded-lg transition-all text-center disabled:opacity-50 disabled:cursor-not-allowed">
                                    Reguler
                                </button>
                                <button type="button" @click="jenisOrder = 'ekspor'; form.jenisOrder = 'ekspor'" :disabled="formMode === 'edit'"
                                        :class="jenisOrder === 'ekspor' ? 'bg-[#051B44] text-white shadow-xs' : 'text-slate-600 hover:text-slate-900'"
                                        class="flex-1 py-1.5 rounded-lg transition-all text-center disabled:opacity-50 disabled:cursor-not-allowed">
                                    Ekspor
                                </button>
                                <button type="button" @click="jenisOrder = 'sampel'; form.jenisOrder = 'sampel'" :disabled="formMode === 'edit'"
                                        :class="jenisOrder === 'sampel' ? 'bg-[#051B44] text-white shadow-xs' : 'text-slate-600 hover:text-slate-900'"
                                        class="flex-1 py-1.5 rounded-lg transition-all text-center disabled:opacity-50 disabled:cursor-not-allowed">
                                    Sampel
                                </button>
                            </div>
                        </div>
                        <div>
                            <label class="text-[10px] font-extrabold uppercase tracking-wider text-slate-500 block mb-1">STATUS ORDER</label>
                            <select x-model="form.status" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs font-semibold text-slate-700 bg-slate-50/70 focus:bg-white focus:outline-none focus:ring-2 focus:ring-sky-500 transition-all">
                                <template x-for="option in statusOptions" :key="option.value">
                                    <option :value="option.value" x-text="option.label"></option>
                                </template>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Section 3: Kuantitas & Nilai -->
                <div class="space-y-4">
                    <div class="flex items-center gap-2.5 text-sm font-bold text-slate-900">
                        <div class="w-8 h-8 rounded-xl bg-[#10B981] text-white flex items-center justify-center">
                            <i class="fa-solid fa-weight-scale text-xs"></i>
                        </div>
                        <span>Kuantitas & Nilai</span>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="text-[10px] font-extrabold uppercase tracking-wider text-slate-500 block mb-1">TOTAL BERAT (KG)</label>
                            <div class="flex items-center gap-2">
                                <input type="number" x-model="form.totalBerat" placeholder="0.00" :disabled="formMode === 'edit'"
                                       class="flex-1 px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs font-semibold text-slate-700 bg-slate-50/70 focus:bg-white focus:outline-none focus:ring-2 focus:ring-sky-500 transition-all disabled:opacity-60 disabled:cursor-not-allowed">
                                <span class="text-xs font-bold text-slate-400">Kg</span>
                            </div>
                        </div>
                        <div>
                            <label class="text-[10px] font-extrabold uppercase tracking-wider text-slate-500 block mb-1">HARGA TOTAL (RP)</label>
                            <div class="flex items-center rounded-xl border border-slate-200 bg-slate-50/70 overflow-hidden focus-within:bg-white focus-within:ring-2 focus-within:ring-sky-500 transition-all">
                                <span class="px-3.5 py-2.5 text-xs font-extrabold text-slate-500 bg-slate-100/80 border-r border-slate-200 shrink-0">Rp</span>
                                <input type="text" x-model="form.totalHarga" placeholder="0" :disabled="formMode === 'edit'"
                                       class="w-full px-3.5 py-2.5 text-xs font-semibold text-slate-700 bg-transparent border-0 focus:outline-none disabled:opacity-60 disabled:cursor-not-allowed">
                            </div>
                        </div>
                    </div>

                    <p class="text-[10px] text-slate-400 italic flex items-center gap-1">
                        <i class="fa-solid fa-circle-info text-sky-400"></i>
                        *Harga per kg: <strong>Otomatis Dihitung</strong>
                    </p>
                </div>

                <!-- Form Actions -->
                <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
                    <button type="button" @click="showForm = false; formMode = 'create'"
                            class="px-5 py-2 rounded-xl border border-slate-200 text-xs font-bold text-slate-600 hover:bg-slate-50 transition-colors">
                        Batal
                    </button>
                    <button type="button" @click="saveForm()"
                            class="px-5 py-2 rounded-xl bg-[#051B44] hover:bg-navy-900 text-white font-bold text-xs shadow-xs transition-all flex items-center gap-2">
                        <i class="fa-solid fa-floppy-disk text-xs"></i>
                        <span x-text="formMode === 'edit' ? 'Simpan Perubahan' : 'Simpan Transaksi'"></span>
                    </button>
                </div>

            </form>

        </div>
    </div>

    <!-- ========= DIRECTORY / LIST MODE ========= -->

    <!-- 4 Top Metric KPI Cards Grid -->
    <div x-show="!showForm" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
        
        <!-- Card 1: Total Pesanan -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs flex items-center justify-between">
            <div>
                <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400 block">TOTAL PESANAN</span>
                <h3 class="text-3xl font-extrabold text-slate-900 mt-1" x-text="kpiTotal"></h3>
            </div>
            <div class="w-10 h-10 rounded-xl bg-[#BEE3F8]/60 text-[#006699] flex items-center justify-center">
                <i class="fa-solid fa-box text-base"></i>
            </div>
        </div>

        <!-- Card 2: Dalam Pemberokian -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs flex items-center justify-between">
            <div>
                <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400 block">DALAM PEMBEROKIAN</span>
                <h3 class="text-3xl font-extrabold text-slate-900 mt-1" x-text="kpiPemberokian"></h3>
            </div>
            <div class="w-10 h-10 rounded-xl bg-[#BEE3F8]/60 text-[#006699] flex items-center justify-center">
                <i class="fa-solid fa-hourglass-half text-base"></i>
            </div>
        </div>

        <!-- Card 3: Siap Kirim -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs flex items-center justify-between">
            <div>
                <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400 block">SIAP KIRIM</span>
                <h3 class="text-3xl font-extrabold text-slate-900 mt-1" x-text="kpiSiapKirim"></h3>
            </div>
            <div class="w-10 h-10 rounded-xl bg-[#BEE3F8]/60 text-[#006699] flex items-center justify-center">
                <i class="fa-solid fa-truck text-base"></i>
            </div>
        </div>

        <!-- Card 4: Selesai Hari Ini -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs flex items-center justify-between">
            <div>
                <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400 block">SELESAI (TERKIRIM)</span>
                <h3 class="text-3xl font-extrabold text-slate-900 mt-1" x-text="kpiSelesai"></h3>
            </div>
            <div class="w-10 h-10 rounded-xl bg-[#D1FAE5] text-[#059669] flex items-center justify-center">
                <i class="fa-regular fa-circle-check text-base"></i>
            </div>
        </div>

    </div>

    <!-- Filter Tabs & Input Order Action -->
    <div x-show="!showForm" class="bg-white p-3 rounded-2xl border border-slate-200/80 shadow-xs flex flex-col sm:flex-row items-center justify-between gap-4">
        <div class="flex items-center gap-2 overflow-x-auto w-full sm:w-auto">
            <button @click="activeTab = 'semua'" 
                    :class="activeTab === 'semua' ? 'bg-[#051B44] text-white font-bold' : 'text-slate-600 hover:bg-slate-100 font-semibold'" 
                    class="px-4 py-2 rounded-xl text-xs transition-all">
                Semua (<span x-text="kpiTotal"></span>)
            </button>
            <button @click="activeTab = 'pending'" 
                    :class="activeTab === 'pending' ? 'bg-[#051B44] text-white font-bold' : 'text-slate-600 hover:bg-slate-100 font-semibold'" 
                    class="px-4 py-2 rounded-xl text-xs transition-all">
                Pending (<span x-text="kpiPending"></span>)
            </button>
            <button @click="activeTab = 'pemberokian'" 
                    :class="activeTab === 'pemberokian' ? 'bg-[#051B44] text-white font-bold' : 'text-slate-600 hover:bg-slate-100 font-semibold'" 
                    class="px-4 py-2 rounded-xl text-xs transition-all">
                Pemberokian (<span x-text="kpiPemberokian"></span>)
            </button>
            <button @click="activeTab = 'siap_kirim'" 
                    :class="activeTab === 'siap_kirim' ? 'bg-[#051B44] text-white font-bold' : 'text-slate-600 hover:bg-slate-100 font-semibold'" 
                    class="px-4 py-2 rounded-xl text-xs transition-all">
                Siap Kirim (<span x-text="kpiSiapKirim"></span>)
            </button>
            <button @click="activeTab = 'selesai'" 
                    :class="activeTab === 'selesai' ? 'bg-[#051B44] text-white font-bold' : 'text-slate-600 hover:bg-slate-100 font-semibold'" 
                    class="px-4 py-2 rounded-xl text-xs transition-all">
                Selesai (<span x-text="kpiSelesai"></span>)
            </button>
        </div>

        <button @click="openCreateForm()" class="w-full sm:w-auto px-4 py-2.5 rounded-xl bg-[#006699] hover:bg-[#005580] text-white font-bold text-xs flex items-center justify-center gap-2 shadow-xs transition-all">
            <i class="fa-solid fa-cart-shopping text-xs"></i>
            <span>Input Order Baru</span>
        </button>
    </div>

    <!-- Order Cards Grid -->
    <div x-show="!showForm" class="grid grid-cols-1 md:grid-cols-3 gap-5">
        
        <div x-show="filteredOrders.length === 0" class="col-span-full py-12 bg-white rounded-2xl border border-slate-200/80 text-center text-slate-400 text-xs font-medium">
            <i class="fa-solid fa-truck text-2xl text-slate-300 block mb-2"></i>
            Tidak ada pesanan distribusi yang ditemukan di database.<br>
            <span class="text-[11px] text-slate-400">Klik tombol <strong>Input Order Baru</strong> untuk mencatat pengiriman baru.</span>
        </div>

        <template x-for="order in filteredOrders" :key="order.id">
            <div class="bg-white rounded-2xl border border-slate-200/80 p-5 shadow-xs flex flex-col justify-between space-y-4">
                <div class="space-y-3">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-bold text-[#0055CC]" x-text="order.id"></span>
                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold uppercase" :class="statusClass(order.status)" x-text="statusLabel(order.status)"></span>
                    </div>
                    <div class="flex items-start justify-between gap-2">
                        <div>
                            <h4 class="font-extrabold text-slate-900 text-sm" x-text="order.customer"></h4>
                            <span class="text-[10px] text-slate-400 font-semibold" x-text="order.tipe_mitra"></span>
                        </div>
                        <span class="px-2 py-0.5 rounded-lg bg-sky-50 text-sky-800 border border-sky-200 text-[10px] font-extrabold" x-text="order.jenis_ikan || 'Ikan Konsumsi'"></span>
                    </div>

                    <div class="bg-slate-50 border border-slate-100 p-3 rounded-xl flex items-center justify-between">
                        <div>
                            <span class="text-[10px] font-extrabold text-slate-400 uppercase tracking-widest block">VOLUME</span>
                            <h3 class="text-lg font-extrabold text-slate-900 mt-0.5" x-text="order.volume"></h3>
                        </div>
                        <div class="text-right" x-show="order.batch_code">
                            <span class="text-[10px] font-extrabold text-slate-400 uppercase tracking-widest block">BATCH ASAL</span>
                            <span class="text-xs font-extrabold text-sky-700 mt-0.5 block" x-text="order.batch_code"></span>
                        </div>
                    </div>

                    <div class="space-y-1 text-xs text-slate-500 font-medium">
                        <p><span class="font-bold text-slate-700">TANGGAL ORDER:</span> <span x-text="order.tanggal"></span></p>
                        <p class="text-[11px]" x-show="order.alamat !== '-'"><span class="font-bold text-slate-700">Alamat:</span> <span x-text="order.alamat"></span></p>
                    </div>
                </div>

                <div x-show="order.status !== 'selesai'" class="grid grid-cols-2 gap-2 pt-2 border-t border-slate-100">
                    <button type="button" @click="printLabel(order)" class="px-3 py-2 rounded-xl border border-slate-300 text-slate-700 hover:bg-slate-50 font-bold text-xs flex items-center justify-center gap-1.5 transition-colors">
                        <i class="fa-solid fa-print text-xs"></i>
                        <span>Cetak Label</span>
                    </button>
                    <button type="button" @click="openEditForm(order)"
                            class="bg-[#051B44] hover:bg-navy-900 text-white px-3 py-2 rounded-xl font-bold text-xs flex items-center justify-center gap-1.5 transition-all shadow-xs">
                        <span>Ubah Status</span>
                    </button>
                </div>

                <div x-show="order.status === 'selesai'" class="pt-2 border-t border-slate-100">
                    <button type="button" @click="openInvoice(order)" class="w-full py-2 rounded-xl border border-slate-300 text-slate-700 hover:bg-slate-50 font-bold text-xs flex items-center justify-center gap-2 transition-colors">
                        <i class="fa-regular fa-file-lines text-xs"></i>
                        <span>Lihat Invoice</span>
                    </button>
                </div>
            </div>
        </template>

        <!-- Empty State -->
        <div x-show="filteredOrders.length === 0" class="col-span-full bg-white rounded-2xl border border-slate-200/80 p-12 text-center">
            <div class="w-12 h-12 rounded-2xl bg-slate-100 text-slate-400 mx-auto flex items-center justify-center text-xl mb-3">
                <i class="fa-solid fa-inbox"></i>
            </div>
            <h4 class="text-sm font-bold text-slate-700">Tidak ada pesanan</h4>
            <p class="text-xs text-slate-400 mt-1">Belum ada data pesanan untuk kategori status yang dipilih.</p>
        </div>

    </div>

    <div x-show="showInvoice" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/50 p-4" style="display: none;">
        <div class="w-full max-w-lg rounded-2xl bg-white shadow-2xl border border-slate-200 overflow-hidden">
            <div class="flex items-center justify-between px-5 py-4 border-b border-slate-200 bg-slate-50">
                <div>
                    <p class="text-[10px] font-extrabold uppercase tracking-[0.2em] text-slate-400">Invoice</p>
                    <h3 class="text-lg font-extrabold text-slate-900" x-text="selectedInvoice?.id || '#INV-0000'"></h3>
                </div>
                <button type="button" @click="closeInvoice()" class="w-8 h-8 rounded-lg border border-slate-200 text-slate-600 hover:bg-white">✕</button>
            </div>

            <div class="p-5 space-y-4" x-show="selectedInvoice">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Pelanggan</span>
                    <span class="text-sm font-bold text-slate-800" x-text="selectedInvoice?.customer"></span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Komoditas / Ikan</span>
                    <span class="text-sm font-bold text-sky-900" x-text="(selectedInvoice?.jenis_ikan || 'Ikan Konsumsi') + (selectedInvoice?.batch_code ? ' (' + selectedInvoice.batch_code + ')' : '')"></span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Volume</span>
                    <span class="text-sm font-bold text-slate-800" x-text="selectedInvoice?.volume"></span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Tanggal Order</span>
                    <span class="text-sm font-bold text-slate-800" x-text="selectedInvoice?.tanggal"></span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Alamat</span>
                    <span class="text-sm font-bold text-slate-800 text-right" x-text="selectedInvoice?.alamat"></span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Status</span>
                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold uppercase" :class="statusClass(selectedInvoice?.status)" x-text="statusLabel(selectedInvoice?.status)"></span>
                </div>

                <div class="rounded-xl bg-slate-50 border border-slate-200 p-4">
                    <div class="flex items-center justify-between text-xs font-bold uppercase tracking-wider text-slate-400">
                        <span>Total Nilai Transaksi</span>
                        <span class="text-sm font-extrabold text-emerald-700" x-text="selectedInvoice?.harga_format || 'Rp 0'"></span>
                    </div>
                </div>
            </div>

            <div class="flex items-center justify-end gap-3 px-5 py-4 border-t border-slate-200 bg-slate-50">
                <button type="button" @click="closeInvoice()" class="px-4 py-2 rounded-xl border border-slate-200 text-xs font-bold text-slate-600 hover:bg-white">Tutup</button>
                <button type="button" @click="printLabel(selectedInvoice)" class="px-4 py-2 rounded-xl bg-[#051B44] text-white text-xs font-bold hover:bg-navy-900">Cetak</button>
            </div>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
function distribusiComponent() {
    return {
        activeTab: 'semua',
        showForm: false,
        showInvoice: false,
        formMode: 'create',
        jenisOrder: 'reguler',
        selectedInvoice: null,
        mitraList: {!! json_encode($mitraList ?? []) !!},
        batches: {!! json_encode($batches ?? []) !!},

        form: {
            id: '#ORD-2023-0001',
            id_transaksi: null,
            id_mitra: '',
            id_pembesaran: '',
            jenis_ikan: '',
            kolam_asal: '',
            stok_biomassa: null,
            tanggal: new Date().toISOString().split('T')[0],
            mitra: '',
            alamat: '',
            jenisOrder: 'reguler',
            status: 'pending',
            totalBerat: '',
            totalHarga: ''
        },

        orders: {!! isset($orders) && count($orders) > 0 ? json_encode($orders) : json_encode([]) !!},

        get maxDate() {
            const d = new Date();
            d.setDate(d.getDate() + 7);
            return d.toISOString().split('T')[0];
        },

        get kpiTotal() {
            return this.orders.length;
        },
        get kpiPemberokian() {
            return this.orders.filter(o => o.status === 'pemberokian' || o.status === 'dalam_pengiriman').length;
        },
        get kpiSiapKirim() {
            return this.orders.filter(o => o.status === 'siap_kirim').length;
        },
        get kpiSelesai() {
            return this.orders.filter(o => o.status === 'selesai').length;
        },
        get kpiPending() {
            return this.orders.filter(o => o.status === 'pending').length;
        },

        get filteredOrders() {
            if (this.activeTab === 'semua') {
                return this.orders;
            }
            return this.orders.filter(order => order.status === this.activeTab);
        },

        statusOptions: [
            { value: 'pending', label: 'Pending / Menunggu Konfirmasi' },
            { value: 'pemberokian', label: 'Dalam Pemberokian' },
            { value: 'siap_kirim', label: 'Siap Kirim / Dikirim' },
            { value: 'selesai', label: 'Selesai' }
        ],

        statusLabel(status) {
            const map = {
                pending: 'Pending',
                pemberokian: 'Pemberokian',
                siap_kirim: 'Siap Kirim',
                selesai: 'Selesai'
            };
            return map[status] || status;
        },

        statusClass(status) {
            const map = {
                pending: 'bg-[#FEE2E2] text-[#991B1B]',
                pemberokian: 'bg-[#E0F2FE] text-[#0284C7]',
                siap_kirim: 'bg-[#C6F6D5] text-[#22543D]',
                selesai: 'bg-[#E2E8F0] text-[#475569]'
            };
            return map[status] || 'bg-[#E2E8F0] text-[#475569]';
        },

        onMitraSelected() {
            const sel = this.mitraList.find(m => m.id_mitra == this.form.id_mitra);
            if (sel) {
                this.form.mitra = sel.nama_mitra;
                this.form.alamat = sel.alamat;
            }
        },

        onBatchSelected() {
            const sel = this.batches.find(b => b.id_pembesaran == this.form.id_pembesaran);
            if (sel) {
                this.form.jenis_ikan = sel.jenis_ikan;
                this.form.kolam_asal = sel.kolam;
                this.form.stok_biomassa = sel.biomassa_est;
                if (this.form.totalBerat && Number(this.form.totalBerat) > 0 && !this.form.totalHarga) {
                    this.form.totalHarga = 'Rp ' + (Number(this.form.totalBerat) * 35000).toLocaleString('id-ID');
                }
            } else {
                this.form.jenis_ikan = '';
                this.form.kolam_asal = '';
                this.form.stok_biomassa = null;
            }
        },

        openCreateForm() {
            this.formMode = 'create';
            this.showForm = true;
            this.form = {
                id: '#ORD-2023-' + String(this.orders.length + 1).padStart(4, '0'),
                id_transaksi: null,
                id_mitra: '',
                id_pembesaran: '',
                jenis_ikan: '',
                kolam_asal: '',
                stok_biomassa: null,
                tanggal: new Date().toISOString().split('T')[0],
                mitra: '',
                alamat: '',
                jenisOrder: 'reguler',
                status: 'pending',
                totalBerat: '',
                totalHarga: ''
            };
            this.jenisOrder = 'reguler';
        },

        openEditForm(order) {
            this.formMode = 'edit';
            this.showForm = true;
            const matchedMitra = this.mitraList.find(m => m.nama_mitra === order.customer);
            this.form = {
                id: order.id,
                id_transaksi: order.id_transaksi || null,
                id_mitra: order.id_mitra || (matchedMitra ? matchedMitra.id_mitra : ''),
                id_pembesaran: order.id_pembesaran || '',
                jenis_ikan: order.jenis_ikan || '',
                kolam_asal: order.kolam_asal || '',
                stok_biomassa: null,
                tanggal: order.tanggal,
                mitra: order.customer,
                alamat: order.alamat,
                jenisOrder: 'reguler',
                status: order.status,
                totalBerat: String(order.volume || '').replace(/[^0-9.]/g, ''),
                totalHarga: order.harga_format || ''
            };
            this.jenisOrder = 'reguler';
        },

        async saveForm() {
            if (this.formMode === 'edit') {
                const rawId = this.form.id_transaksi || String(this.form.id).replace(/[^0-9]/g, '');
                try {
                    const res = await fetch('/distribusi/' + rawId, {
                        method: 'PUT',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({
                            status_order: this.form.status,
                            Total_kg: Number(this.form.totalBerat) || 0,
                            harga_total: Number(String(this.form.totalHarga).replace(/[^0-9]/g, '')) || 0
                        })
                    });
                } catch(e) {}

                const target = this.orders.find(item => item.id === this.form.id || item.id_transaksi == rawId);
                if (target) {
                    target.status = this.form.status;
                }
            } else {
                if (this.form.tanggal && this.form.tanggal > this.maxDate) {
                    alert('Tanggal order tidak boleh melebihi batas maksimal minggu ini (' + this.maxDate + ')!');
                    return;
                }
                if (!this.form.id_mitra) {
                    alert('Silakan pilih Mitra Distributor dari database!');
                    return;
                }
                if (!this.form.id_pembesaran) {
                    alert('Silakan pilih Sumber Batch Pembesaran / Jenis Ikan!');
                    return;
                }
                if (!this.form.totalBerat || Number(this.form.totalBerat) <= 0) {
                    alert('Silakan isi total berat pesanan!');
                    return;
                }

                const totalKg = Number(this.form.totalBerat);
                const hargaTotal = this.form.totalHarga ? Number(String(this.form.totalHarga).replace(/[^0-9]/g, '')) : (totalKg * 35000);

                try {
                    const res = await fetch('{{ route('distribusi.store') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({
                            id_mitra: this.form.id_mitra,
                            id_pembesaran: this.form.id_pembesaran,
                            tanggal_order: this.form.tanggal,
                            Total_kg: totalKg,
                            harga_total: hargaTotal,
                            Jenis_order: this.form.jenis_ikan || this.form.jenisOrder,
                            status_order: this.form.status
                        })
                    });
                    const data = await res.json();
                    if (res.ok && data.success) {
                        const newTrx = data.transaksi;
                        const matchedBatch = this.batches.find(b => b.id_pembesaran == this.form.id_pembesaran);
                        this.orders.unshift({
                            id_transaksi: newTrx.id_transaksi,
                            id: '#ORD-2023-' + String(newTrx.id_transaksi).padStart(4, '0'),
                            id_mitra: newTrx.id_mitra,
                            id_pembesaran: newTrx.id_pembesaran,
                            customer: newTrx.mitra ? newTrx.mitra.nama_mitra : this.form.mitra,
                            volume: totalKg.toLocaleString('id-ID') + ' kg',
                            total_kg: totalKg,
                            harga_total: hargaTotal,
                            harga_format: 'Rp ' + Number(hargaTotal).toLocaleString('id-ID'),
                            jenis_ikan: this.form.jenis_ikan || (newTrx.batch_pembesaran ? newTrx.batch_pembesaran.jenis_ikan : 'Ikan Segar'),
                            kolam_asal: this.form.kolam_asal,
                            batch_code: this.form.id_pembesaran ? ('#PB-' + String(this.form.id_pembesaran).padStart(5, '0')) : null,
                            status: this.form.status,
                            alamat: this.form.alamat,
                            tanggal: newTrx.tanggal_order || new Date().toLocaleDateString('id-ID'),
                            label: true
                        });
                    } else {
                        alert(data.message || 'Gagal menyimpan transaksi pesanan.');
                    }
                } catch(e) {
                    alert('Terjadi kesalahan saat menyimpan pesanan.');
                }
            }
            this.showForm = false;
            this.formMode = 'create';
        },

        openInvoice(order) {
            this.selectedInvoice = {
                id: order.id,
                customer: order.customer,
                jenis_ikan: order.jenis_ikan,
                batch_code: order.batch_code,
                kolam_asal: order.kolam_asal,
                volume: order.volume,
                total_kg: order.total_kg,
                harga_format: order.harga_format || ('Rp ' + (Number(order.total_kg || 100) * 35000).toLocaleString('id-ID')),
                tanggal: order.tanggal,
                alamat: order.alamat,
                status: order.status,
                total: order.volume
            };
            this.showInvoice = true;
        },

        closeInvoice() {
            this.showInvoice = false;
            this.selectedInvoice = null;
        },

        updateStatus(order) {
            this.openEditForm(order);
        }
    };
}

function printLabel(order) {
    if (!order) return;
    const content = [
        'ORDER LABEL - DISTRIBUSI SIM-BUDIDAYA',
        '---------------------------------------',
        'ID Order    : ' + order.id,
        'Komoditas   : ' + (order.jenis_ikan || 'Ikan Konsumsi') + (order.batch_code ? ' (' + order.batch_code + ')' : ''),
        'Customer    : ' + order.customer,
        'Volume      : ' + order.volume,
        'Alamat      : ' + order.alamat,
        'Tgl Order   : ' + order.tanggal,
        'Status      : ' + (order.status === 'pending' ? 'Pending' : order.status === 'pemberokian' ? 'Pemberokian' : order.status === 'siap_kirim' ? 'Siap Kirim' : 'Selesai')
    ].join('\n');

    const printWindow = window.open('', '_blank');
    if (!printWindow) return;

    const printable = '<html><head><title>Label Order</title></head><body><pre style="font-family: sans-serif; padding: 24px; line-height: 1.6; font-size: 14px;">' + content.replace(/</g, '&lt;').replace(/>/g, '&gt;') + '</pre></body></html>';
    printWindow.document.write(printable);
    printWindow.document.close();
    printWindow.focus();
    setTimeout(() => printWindow.print(), 300);
}
</script>
@endpush

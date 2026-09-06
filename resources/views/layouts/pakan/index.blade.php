@extends('layouts.app')

@section('title', 'Manajemen Stok & Log Pakan - SIM-BUDIDAYA')

@section('content')
<div class="space-y-6" x-data="pakanHubComponent()">

    <!-- Flash Notification Messages -->
    @if(session('success'))
    <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-bold flex items-center gap-3 shadow-xs">
        <i class="fa-solid fa-circle-check text-emerald-500 text-base"></i>
        <span>{{ session('success') }}</span>
    </div>
    @endif

    @if(session('error'))
    <div class="p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 text-xs font-bold flex items-center gap-3 shadow-xs">
        <i class="fa-solid fa-circle-exclamation text-rose-500 text-base"></i>
        <span>{{ session('error') }}</span>
    </div>
    @endif

    <!-- Top Hero Banner & Quick Actions -->
    <div class="bg-[#051B44] p-6 sm:p-7 rounded-3xl text-white shadow-xl shadow-sky-950/10 relative overflow-hidden">

        <div class="relative z-10 flex flex-col lg:flex-row lg:items-center justify-between gap-6">
            <div class="space-y-2 max-w-2xl">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/10 backdrop-blur-md border border-white/15 text-[11px] font-bold text-sky-200">
                    <i class="fa-solid fa-boxes-stacked text-sky-300"></i>
                    <span>Sistem Rantai Pasok & Inventori Pakan Terpadu</span>
                </div>
                <h1 class="text-xl sm:text-2xl font-black tracking-tight">Manajemen Pasokan &amp; Log Pakan Ikan</h1>
                <p class="text-xs sm:text-sm text-sky-100/85 leading-relaxed">
                    Pantau stok pakan real-time untuk pembibitan & pembesaran, kalkulasi estimasi sisa hari pemakaian, hubungi supplier via WhatsApp, dan catat pembelian langsung terhubung ke Buku Kas Keuangan.
                </p>
            </div>

            <!-- Action Buttons -->
            <div class="flex flex-wrap items-center gap-2.5 shrink-0">
                <!-- Tombol Pesan via WhatsApp -->
                <button type="button" @click="openSupplierModal()" 
                        class="px-4 py-2.5 rounded-xl bg-emerald-500 hover:bg-emerald-600 active:scale-95 text-white text-xs font-extrabold shadow-lg shadow-emerald-500/25 transition-all flex items-center gap-2 cursor-pointer">
                    <i class="fa-brands fa-whatsapp text-sm"></i>
                    <span>Hubungi Supplier WA</span>
                </button>

                <!-- Tombol Catat Pembelian Pakan -->
                <button type="button" @click="openBeliModal()" 
                        class="px-4 py-2.5 rounded-xl bg-white hover:bg-sky-50 active:scale-95 text-[#051B44] text-xs font-black shadow-lg shadow-black/10 transition-all flex items-center gap-2 cursor-pointer">
                    <i class="fa-solid fa-cart-plus text-sky-600 text-sm"></i>
                    <span>Catat Beli Pakan (+Stok)</span>
                </button>

                <!-- Tombol Tambah Master Pakan -->
                <button type="button" @click="openMasterModal()" 
                        class="px-3.5 py-2.5 rounded-xl bg-white/15 hover:bg-white/25 active:scale-95 text-white text-xs font-bold backdrop-blur-md border border-white/20 transition-all flex items-center gap-1.5 cursor-pointer"
                        title="Kelola Master Item Pakan">
                    <i class="fa-solid fa-gear text-xs"></i>
                    <span>Master Item</span>
                </button>
            </div>
        </div>
    </div>

    <!-- Summary KPI Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-5">
        
        <!-- Card 1: Total Stok Gudang -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs hover:border-sky-300 transition-all">
            <div class="flex items-center justify-between">
                <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400">Total Stok Gudang</span>
                <div class="w-9 h-9 rounded-xl bg-sky-50 text-sky-600 flex items-center justify-center font-bold text-sm">
                    <i class="fa-solid fa-warehouse"></i>
                </div>
            </div>
            <div class="mt-3 flex items-baseline gap-1.5">
                <span class="text-2xl font-black text-slate-900" x-text="Number(summary.total_stok_kg).toFixed(1)"></span>
                <span class="text-xs font-extrabold text-slate-500">KG Total</span>
            </div>
            <p class="text-[11px] text-slate-400 mt-1">Gabungan seluruh jenis pakan aktif</p>
        </div>

        <!-- Card 2: Stok Pembibitan -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs hover:border-sky-300 transition-all">
            <div class="flex items-center justify-between">
                <span class="text-[10px] font-extrabold uppercase tracking-wider text-emerald-600 font-black">🌱 Khusus Pembibitan</span>
                <div class="w-9 h-9 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center font-bold text-sm">
                    <i class="fa-solid fa-seedling"></i>
                </div>
            </div>
            <div class="mt-3 flex items-baseline gap-1.5">
                <span class="text-2xl font-black text-emerald-700" x-text="Number(summary.stok_pembibitan_kg).toFixed(1)"></span>
                <span class="text-xs font-extrabold text-emerald-600">KG / Tray</span>
            </div>
            <p class="text-[11px] text-slate-400 mt-1">Cacing Sutra, Artemia, PF-500</p>
        </div>

        <!-- Card 3: Stok Pembesaran -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs hover:border-sky-300 transition-all">
            <div class="flex items-center justify-between">
                <span class="text-[10px] font-extrabold uppercase tracking-wider text-blue-600 font-black">🐟 Khusus Pembesaran</span>
                <div class="w-9 h-9 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center font-bold text-sm">
                    <i class="fa-solid fa-fish-fins"></i>
                </div>
            </div>
            <div class="mt-3 flex items-baseline gap-1.5">
                <span class="text-2xl font-black text-[#0B2570]" x-text="Number(summary.stok_pembesaran_kg).toFixed(1)"></span>
                <span class="text-xs font-extrabold text-slate-500">KG Pelet & Daun</span>
            </div>
            <p class="text-[11px] text-slate-400 mt-1">Pelet 781-1/2, Azolla, Maggot</p>
        </div>

        <!-- Card 4: Restock Alert Status -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs hover:border-sky-300 transition-all">
            <div class="flex items-center justify-between">
                <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400">Status Restock</span>
                <div class="w-9 h-9 rounded-xl flex items-center justify-center font-bold text-sm"
                     :class="summary.item_kritis_count > 0 ? 'bg-rose-50 text-rose-600' : (summary.item_waspada_count > 0 ? 'bg-amber-50 text-amber-600' : 'bg-emerald-50 text-emerald-600')">
                    <i class="fa-solid" :class="summary.item_kritis_count > 0 ? 'fa-triangle-exclamation' : (summary.item_waspada_count > 0 ? 'fa-clock-rotate-left' : 'fa-circle-check')"></i>
                </div>
            </div>
            <div class="mt-3 flex items-center gap-2">
                <template x-if="summary.item_kritis_count > 0">
                    <span class="px-2.5 py-1 rounded-full text-[11px] font-extrabold bg-rose-100 text-rose-700 border border-rose-200 flex items-center gap-1.5">
                        <span class="w-2 h-2 rounded-full bg-rose-500 animate-ping"></span>
                        <span x-text="summary.item_kritis_count + ' Item Kritis (< 2 Hari)'"></span>
                    </span>
                </template>
                <template x-if="summary.item_kritis_count === 0 && summary.item_waspada_count > 0">
                    <span class="px-2.5 py-1 rounded-full text-[11px] font-extrabold bg-amber-100 text-amber-800 border border-amber-200 flex items-center gap-1.5">
                        <i class="fa-solid fa-circle-exclamation text-amber-600"></i>
                        <span x-text="summary.item_waspada_count + ' Item Perlu Restock'"></span>
                    </span>
                </template>
                <template x-if="summary.item_kritis_count === 0 && summary.item_waspada_count === 0">
                    <span class="px-2.5 py-1 rounded-full text-[11px] font-extrabold bg-emerald-100 text-emerald-800 border border-emerald-200 flex items-center gap-1.5">
                        <i class="fa-solid fa-check"></i>
                        <span>Semua Stok Aman (> 7 Hari)</span>
                    </span>
                </template>
            </div>
            <p class="text-[11px] text-slate-400 mt-1.5">Berdasarkan burn-rate 7 hari terakhir</p>
        </div>

    </div>

    <!-- Inventory & Burn-Rate Restock Alert Section -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs p-5 sm:p-6 space-y-4">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 pb-4 border-b border-slate-100">
            <div>
                <div class="flex items-center gap-2">
                    <h3 class="text-sm sm:text-base font-extrabold text-slate-900">Katalog Stok Pakan &amp; Estimasi Sisa Hari</h3>
                    <span class="text-[10px] font-extrabold px-2.5 py-0.5 rounded-full bg-sky-50 text-sky-700 border border-sky-200">Real-time</span>
                </div>
                <p class="text-xs text-slate-500 font-medium mt-0.5">
                    Monitoring laju konsumsi pakan harian dan peringatan dini sebelum stok di gudang habis.
                </p>
            </div>

            <!-- Filter Tabs -->
            <div class="flex items-center p-1 bg-slate-100 rounded-xl text-xs font-extrabold text-slate-600">
                <button type="button" @click="stokFilter = 'semua'" 
                        :class="stokFilter === 'semua' ? 'bg-white text-slate-900 shadow-xs rounded-lg' : 'hover:text-slate-900'"
                        class="px-3 py-1.5 transition-all cursor-pointer">
                    Semua Pakan
                </button>
                <button type="button" @click="stokFilter = 'pembibitan'" 
                        :class="stokFilter === 'pembibitan' ? 'bg-white text-emerald-700 shadow-xs rounded-lg' : 'hover:text-emerald-700'"
                        class="px-3 py-1.5 transition-all cursor-pointer flex items-center gap-1.5">
                    <span>🌱 Pembibitan</span>
                </button>
                <button type="button" @click="stokFilter = 'pembesaran'" 
                        :class="stokFilter === 'pembesaran' ? 'bg-white text-[#0B2570] shadow-xs rounded-lg' : 'hover:text-[#0B2570]'"
                        class="px-3 py-1.5 transition-all cursor-pointer flex items-center gap-1.5">
                    <span>🐟 Pembesaran</span>
                </button>
            </div>
        </div>

        <!-- Stok Cards Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
            <template x-for="item in filteredStokList" :key="item.id_stok_pakan">
                <div class="rounded-2xl border p-4.5 flex flex-col justify-between transition-all hover:shadow-md"
                     :class="item.status === 'kritis' ? 'border-rose-300 bg-rose-50/30' : (item.status === 'waspada' ? 'border-amber-300 bg-amber-50/20' : 'border-slate-200/90 bg-white')">
                    
                    <div class="space-y-2.5">
                        <!-- Header Card -->
                        <div class="flex items-start justify-between gap-2">
                            <div>
                                <span class="text-[9px] font-black uppercase tracking-wider px-2 py-0.5 rounded-md"
                                      :class="item.kategori_peruntukan === 'pembibitan' ? 'bg-emerald-100 text-emerald-800' : (item.kategori_peruntukan === 'pembesaran' ? 'bg-blue-100 text-blue-800' : 'bg-slate-100 text-slate-700')"
                                      x-text="item.kategori_peruntukan === 'pembibitan' ? '🌱 Fase Pembibitan' : (item.kategori_peruntukan === 'pembesaran' ? '🐟 Fase Pembesaran' : '📦 Semua Fase')">
                                </span>
                                <h4 class="font-black text-slate-900 text-sm mt-1.5" x-text="item.nama_pakan"></h4>
                            </div>

                            <!-- Status Badge -->
                            <span class="text-[10px] font-extrabold px-2 py-0.5 rounded-full border shrink-0"
                                  :class="item.status_badge"
                                  x-text="item.status_label">
                            </span>
                        </div>

                        <!-- Info Stok & Sisa Hari -->
                        <div class="pt-2 border-t border-slate-100/80 grid grid-cols-2 gap-2">
                            <div>
                                <span class="text-[10px] font-bold text-slate-400 block uppercase">Sisa Stok</span>
                                <div class="flex items-baseline gap-1 mt-0.5">
                                    <span class="text-lg font-black text-slate-900" x-text="Number(item.stok_tersisa).toFixed(1)"></span>
                                    <span class="text-[11px] font-bold text-slate-500" x-text="item.satuan"></span>
                                </div>
                                <span class="text-[10px] text-slate-400">Min: <span x-text="item.batas_minimum + ' ' + item.satuan"></span></span>
                            </div>

                            <div>
                                <span class="text-[10px] font-bold text-slate-400 block uppercase">Estimasi Habis</span>
                                <div class="flex items-baseline gap-1 mt-0.5">
                                    <span class="text-lg font-black"
                                          :class="item.status === 'kritis' ? 'text-rose-600' : (item.status === 'waspada' ? 'text-amber-600' : 'text-emerald-600')"
                                          x-text="item.sisa_hari + ' Hari'">
                                    </span>
                                </div>
                                <span class="text-[10px] text-slate-400" x-text="'~' + item.burn_rate_harian + ' ' + item.satuan + '/hari'"></span>
                            </div>
                        </div>

                        <div class="text-[11px] text-slate-500 font-medium">
                            <span>Harga Acuan: </span>
                            <strong class="text-slate-800 font-extrabold" x-text="'Rp ' + Number(item.harga_per_satuan).toLocaleString('id-ID') + '/' + item.satuan"></strong>
                        </div>
                    </div>

                    <!-- Footer Action Buttons -->
                    <div class="pt-3 mt-3 border-t border-slate-100 flex items-center gap-2">
                        <button type="button" @click="quickBeli(item)" 
                                class="flex-1 py-1.5 px-2.5 rounded-lg bg-sky-50 hover:bg-sky-100 text-sky-700 text-xs font-extrabold transition-colors flex items-center justify-center gap-1.5 cursor-pointer">
                            <i class="fa-solid fa-cart-plus text-[11px]"></i>
                            <span>Beli Pakan</span>
                        </button>
                        <button type="button" @click="openSupplierModal(item.nama_pakan)" 
                                class="py-1.5 px-2.5 rounded-lg bg-emerald-50 hover:bg-emerald-100 text-emerald-700 text-xs font-extrabold transition-colors flex items-center justify-center gap-1 cursor-pointer"
                                title="Pesan via WhatsApp">
                            <i class="fa-brands fa-whatsapp text-sm"></i>
                        </button>
                    </div>

                </div>
            </template>
        </div>
    </div>

    <!-- Main Form Log Pemberian Pakan Harian -->
    <div class="bg-white p-6 sm:p-8 rounded-2xl border border-slate-200/80 shadow-xs space-y-6">
        
        <!-- Header inside Form -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-4 border-b border-slate-100">
            <div class="flex items-center gap-3.5">
                <div class="w-11 h-11 rounded-2xl bg-sky-50 text-sky-600 flex items-center justify-center shrink-0 text-lg border border-sky-100">
                    <i class="fa-regular fa-clipboard"></i>
                </div>
                <div>
                    <h3 class="text-base font-bold text-slate-900">Formulir Log Pemberian Pakan Harian</h3>
                    <p class="text-xs text-slate-500 mt-0.5">Catat pakan per kolam aktif & saldo stok otomatis berkurang secara real-time.</p>
                </div>
            </div>

            <!-- Toggle Kategori Fase -->
            <div class="flex items-center p-1 bg-slate-100 rounded-xl text-xs font-extrabold text-slate-600">
                <button type="button" @click="selectFase('pembesaran')" 
                        :class="form.kategori_fase === 'pembesaran' ? 'bg-[#051B44] text-white shadow-xs rounded-lg' : 'hover:text-slate-900'"
                        class="px-3.5 py-1.5 transition-all cursor-pointer flex items-center gap-1.5">
                    <i class="fa-solid fa-fish"></i>
                    <span>Kolam Pembesaran</span>
                </button>
                <button type="button" @click="selectFase('pembibitan')" 
                        :class="form.kategori_fase === 'pembibitan' ? 'bg-emerald-700 text-white shadow-xs rounded-lg' : 'hover:text-emerald-700'"
                        class="px-3.5 py-1.5 transition-all cursor-pointer flex items-center gap-1.5">
                    <i class="fa-solid fa-seedling"></i>
                    <span>Kolam Pembibitan (Hatchery)</span>
                </button>
            </div>
        </div>

        <!-- Form Elements -->
        <form @submit.prevent="handleSaveLog()" class="space-y-6">
            
            <!-- Row 1: Kolam & Tanggal -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label class="text-[10px] font-extrabold uppercase tracking-wider text-slate-500 block mb-1.5">
                        PILIH KOLAM AKTIF (<span x-text="form.kategori_fase.toUpperCase()"></span>) <span class="text-rose-500">*</span>
                    </label>
                    
                    <!-- Dropdown Kolam Pembesaran -->
                    <template x-if="form.kategori_fase === 'pembesaran'">
                        <div>
                            <template x-if="activeKolams.length > 0">
                                <select x-model="form.id_kolam" @change="onKolamChange()" 
                                        class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs font-semibold text-slate-800 bg-slate-50/70 focus:bg-white focus:outline-none focus:ring-2 focus:ring-sky-500 transition-all cursor-pointer">
                                    <option value="">Pilih Kolam Pembesaran Aktif...</option>
                                    <template x-for="k in activeKolams" :key="k.id_kolam">
                                        <option :value="k.id_kolam" x-text="k.label"></option>
                                    </template>
                                </select>
                            </template>
                            <template x-if="activeKolams.length === 0">
                                <div class="p-3 bg-amber-50 rounded-xl border border-amber-200 text-amber-900 text-xs font-semibold flex items-center justify-between gap-2">
                                    <div class="flex items-center gap-2">
                                        <i class="fa-solid fa-triangle-exclamation text-amber-600"></i>
                                        <span>Belum ada kolam pembesaran yang terisi ikan/siklus aktif.</span>
                                    </div>
                                    <a href="{{ route('pembudidaya') }}" class="px-2.5 py-1 bg-amber-200/70 hover:bg-amber-300 text-amber-900 rounded-lg text-[10px] font-extrabold transition-colors">
                                        Tebar Ikan
                                    </a>
                                </div>
                            </template>
                        </div>
                    </template>

                    <!-- Dropdown Kolam Pembibitan -->
                    <template x-if="form.kategori_fase === 'pembibitan'">
                        <div>
                            <template x-if="hatcheryKolams.length > 0">
                                <select x-model="form.id_kolam" @change="onHatcheryKolamChange()" 
                                        class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs font-semibold text-slate-800 bg-slate-50/70 focus:bg-white focus:outline-none focus:ring-2 focus:ring-sky-500 transition-all cursor-pointer">
                                    <option value="">Pilih Kolam Pembibitan Aktif...</option>
                                    <template x-for="hk in hatcheryKolams" :key="hk.id_kolam">
                                        <option :value="hk.id_kolam" x-text="hk.label"></option>
                                    </template>
                                </select>
                            </template>
                            <template x-if="hatcheryKolams.length === 0">
                                <div class="p-3 bg-emerald-50 rounded-xl border border-emerald-200 text-emerald-900 text-xs font-semibold flex items-center gap-2">
                                    <i class="fa-solid fa-seedling text-emerald-600"></i>
                                    <span>Belum ada kolam hatchery yang sedang terisi benih aktif.</span>
                                </div>
                            </template>
                        </div>
                    </template>
                </div>

                <div>
                    <label class="text-[10px] font-extrabold uppercase tracking-wider text-slate-500 block mb-1.5">
                        TANGGAL PEMBERIAN PAKAN <span class="text-rose-500">*</span>
                    </label>
                    <input type="date" x-model="form.tgl_log"
                           class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs font-semibold text-slate-800 bg-slate-50/70 focus:bg-white focus:outline-none focus:ring-2 focus:ring-sky-500 transition-all">
                </div>
            </div>

            <!-- Detail Batch Terpilih Info Card -->
            <template x-if="selectedKolamInfo">
                <div class="p-4 bg-sky-50/70 rounded-2xl border border-sky-100 flex flex-col sm:flex-row sm:items-center justify-between gap-3 text-xs">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-white text-sky-600 border border-sky-200 flex items-center justify-center font-bold">
                            <i class="fa-solid" :class="form.kategori_fase === 'pembibitan' ? 'fa-seedling text-emerald-600' : 'fa-fish text-sky-600'"></i>
                        </div>
                        <div>
                            <div class="flex items-center gap-2">
                                <span class="font-extrabold text-slate-900" x-text="selectedKolamInfo.nama_kolam"></span>
                                <span class="text-[10px] font-bold text-sky-700 bg-white px-2 py-0.5 rounded-md border border-sky-200" x-text="selectedKolamInfo.batch_id"></span>
                            </div>
                            <span class="text-[11px] text-slate-500" 
                                  x-text="form.kategori_fase === 'pembesaran' ? (selectedKolamInfo.jenis_ikan + ' • Estimasi Biomassa: ' + selectedKolamInfo.biomassa_format + ' kg') : (selectedKolamInfo.jenis_ikan + ' • Jumlah Benih: ' + Number(selectedKolamInfo.jumlah_bibit).toLocaleString('id-ID') + ' ekor')">
                            </span>
                        </div>
                    </div>

                    <div>
                        <template x-if="selectedKolamInfo.is_fed_today">
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[10px] font-extrabold bg-emerald-100 text-emerald-800 border border-emerald-300">
                                <i class="fa-solid fa-circle-check"></i>
                                <span>Sudah Diberi Pakan Hari Ini</span>
                            </span>
                        </template>
                        <template x-if="!selectedKolamInfo.is_fed_today">
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[10px] font-extrabold bg-amber-100 text-amber-800 border border-amber-300">
                                <i class="fa-solid fa-clock"></i>
                                <span>Belum Diberi Pakan Hari Ini</span>
                            </span>
                        </template>
                    </div>
                </div>
            </template>

            <!-- Section 1: Pemilihan Jenis Pakan & Jumlah Pemakaian -->
            <div>
                <div class="flex items-center gap-2 text-xs font-bold text-slate-800 pb-3 border-b border-slate-100">
                    <i class="fa-solid fa-bowl-food text-sky-600"></i>
                    <span>Pilihan Jenis Pakan &amp; Takaran</span>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-5 mt-4">
                    
                    <!-- Pilih Master Pakan (Dropdown) -->
                    <div>
                        <label class="text-[10px] font-extrabold uppercase tracking-wider text-slate-500 block mb-1.5">
                            ITEM PAKAN DARI GUDANG <span class="text-rose-500">*</span>
                        </label>
                        <select x-model="form.id_stok_pakan" @change="onStokPakanChange()" 
                                class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs font-semibold text-slate-800 bg-white focus:outline-none focus:ring-2 focus:ring-sky-500 transition-all cursor-pointer">
                            <option value="">Pilih Pakan Gudang...</option>
                            <template x-for="sp in relevantStokList" :key="sp.id_stok_pakan">
                                <option :value="sp.id_stok_pakan" x-text="sp.nama_pakan + ' (Sisa: ' + sp.stok_tersisa + ' ' + sp.satuan + ')'"></option>
                            </template>
                        </select>
                        <template x-if="selectedPakanItem">
                            <span class="text-[10px] font-semibold text-slate-500 mt-1 block">
                                Sisa Stok: <strong class="text-slate-800" x-text="selectedPakanItem.stok_tersisa + ' ' + selectedPakanItem.satuan"></strong> • Harga: <span x-text="'Rp ' + Number(selectedPakanItem.harga_per_satuan).toLocaleString('id-ID') + '/' + selectedPakanItem.satuan"></span>
                            </span>
                        </template>
                    </div>

                    <!-- Jumlah Pakan Utama (Pelet / Cacing) Box -->
                    <div class="bg-slate-50/80 p-3.5 rounded-xl border border-slate-100 space-y-1.5">
                        <label class="text-[10px] font-extrabold uppercase tracking-wider text-slate-500 block">
                            JUMLAH PAKAN UTAMA (KG) <span class="text-rose-500">*</span> <span class="text-[9px] text-slate-400 font-normal lowercase">(maks. 100 kg)</span>
                        </label>
                        <div class="flex items-center gap-2">
                            <input type="number" x-model="form.kg_pelet"
                                onkeydown="if(event.key === '-' || event.key === 'e' || event.key === 'E' || event.key === '+') event.preventDefault()"
                                @input="if(form.kg_pelet !== '' && Number(form.kg_pelet) < 0) form.kg_pelet = 0; if(Number(form.kg_pelet) > 100) form.kg_pelet = 100; recalculateCost()"
                                step="0.1" min="0" max="100" placeholder="0.0"
                                class="flex-1 px-3 py-2 rounded-xl border border-slate-200 text-xs font-extrabold text-slate-900 bg-white focus:outline-none focus:ring-2 focus:ring-sky-500 transition-all">
                            <span class="text-xs font-extrabold text-slate-400 px-1" x-text="selectedPakanItem ? selectedPakanItem.satuan.toUpperCase() : 'KG'"></span>
                        </div>
                    </div>

                    <!-- Pakan Suplemen Organik (Daun / Azolla) Box -->
                    <div class="bg-slate-50/80 p-3.5 rounded-xl border border-slate-100 space-y-1.5">
                        <label class="text-[10px] font-extrabold uppercase tracking-wider text-slate-500 block">
                            PAKAN TAMBAHAN / SUPLEMEN (KG) <span class="text-[9px] text-slate-400 font-normal lowercase">(maks. 100 kg)</span>
                        </label>
                        <div class="flex items-center gap-2">
                            <div class="w-1/2 flex items-center gap-1">
                                <input type="number" x-model="form.kg_daun"
                                       onkeydown="if(event.key === '-' || event.key === 'e' || event.key === 'E' || event.key === '+') event.preventDefault()"
                                       @input="if(form.kg_daun !== '' && Number(form.kg_daun) < 0) form.kg_daun = 0; if(Number(form.kg_daun) > 100) form.kg_daun = 100"
                                       step="0.1" min="0" max="100" placeholder="0.0"
                                       class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs font-extrabold text-slate-900 bg-white focus:outline-none focus:ring-2 focus:ring-sky-500 transition-all">
                            </div>
                            <select x-model="form.jenis_daun" class="flex-1 px-2.5 py-2 rounded-xl border border-slate-200 text-xs font-semibold text-slate-700 bg-white focus:outline-none focus:ring-2 focus:ring-sky-500 transition-all cursor-pointer">
                                <option value="">Jenis Tambahan...</option>
                                <option value="Daun Talas">Daun Talas</option>
                                <option value="Daun Singkong">Daun Singkong</option>
                                <option value="Daun Pepaya">Daun Pepaya</option>
                                <option value="Azolla / Lemna">Azolla / Lemna</option>
                                <option value="Maggot BSF">Maggot BSF</option>
                            </select>
                        </div>
                    </div>

                </div>
            </div>

            <!-- Section 2: Parameter Kualitas Air & Biaya -->
            <div>
                <div class="flex items-center gap-2 text-xs font-bold text-slate-800 pb-3 border-b border-slate-100">
                    <i class="fa-solid fa-droplet text-sky-600"></i>
                    <span>Parameter Kualitas Air &amp; Biaya Pakan</span>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5 mt-4">
                    <div>
                        <label class="text-[10px] font-extrabold uppercase tracking-wider text-slate-500 block mb-1.5">
                            ESTIMASI BIAYA KONSUMSI PAKAN (RP)
                        </label>
                        <div class="flex items-center rounded-xl border border-slate-200 bg-slate-50/70 overflow-hidden focus-within:bg-white focus-within:ring-2 focus-within:ring-sky-500 focus-within:border-sky-500 transition-all">
                            <span class="px-3.5 py-2.5 text-xs font-extrabold text-slate-500 bg-slate-100/80 border-r border-slate-200 shrink-0">Rp</span>
                            <input type="number" x-model="form.total_biaya" min="0"
                                   onkeydown="if(event.key === '-' || event.key === 'e' || event.key === 'E') event.preventDefault()"
                                   @input="if(form.total_biaya !== '' && Number(form.total_biaya) < 0) form.total_biaya = Math.abs(Number(form.total_biaya)) || 0"
                                   class="w-full px-3.5 py-2.5 text-xs font-extrabold text-slate-900 bg-transparent border-0 focus:outline-none">
                        </div>
                    </div>

                    <div>
                        <label class="text-[10px] font-extrabold uppercase tracking-wider text-slate-500 block mb-1.5">
                            PH AIR KOLAM
                        </label>
                        <div class="flex items-center rounded-xl border border-slate-200 bg-slate-50/70 overflow-hidden focus-within:bg-white focus-within:ring-2 focus-within:ring-sky-500 focus-within:border-sky-500 transition-all">
                            <input type="number" step="0.1" min="0" max="14" x-model="form.ph_air" placeholder="7.2"
                                   onkeydown="if(event.key === '-' || event.key === 'e' || event.key === 'E') event.preventDefault()"
                                   @input="if(form.ph_air !== '' && Number(form.ph_air) < 0) form.ph_air = 0"
                                   class="w-full px-3.5 py-2.5 text-xs font-semibold text-slate-800 bg-transparent border-0 focus:outline-none">
                            <span class="px-3.5 py-2.5 text-xs font-extrabold text-slate-500 bg-slate-100/80 border-l border-slate-200 shrink-0">pH</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
                <button type="button" @click="resetLogForm()" class="px-5 py-2.5 rounded-xl border border-slate-200 text-xs font-bold text-slate-600 hover:bg-slate-50 transition-colors cursor-pointer">
                    Reset
                </button>
                <button type="submit" 
                        :disabled="isSubmittingLog"
                        class="px-6 py-2.5 rounded-xl bg-[#0284C7] hover:bg-sky-600 active:scale-95 text-white font-extrabold text-xs shadow-md shadow-sky-600/20 transition-all flex items-center gap-2 cursor-pointer">
                    <i class="fa-solid fa-check text-xs"></i>
                    <span x-text="isSubmittingLog ? 'Menyimpan...' : 'Simpan Log & Potong Stok'"></span>
                </button>
            </div>

        </form>

    </div>

    <!-- Riwayat Tabs (Riwayat Log Konsumsi Harian & Riwayat Pembelian Masuk) -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden space-y-4 p-5">
        
        <!-- Header & Nav Tabs -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-4 border-b border-slate-100">
            <div class="flex items-center gap-2">
                <button type="button" @click="historyTab = 'log'" 
                        :class="historyTab === 'log' ? 'border-[#0284C7] text-[#0284C7] font-black' : 'border-transparent text-slate-500 hover:text-slate-800 font-bold'"
                        class="pb-2 border-b-2 text-sm transition-all cursor-pointer flex items-center gap-2">
                    <i class="fa-solid fa-clipboard-list"></i>
                    <span>Riwayat Log Pakan Harian</span>
                </button>
                <button type="button" @click="historyTab = 'pembelian'" 
                        :class="historyTab === 'pembelian' ? 'border-[#0284C7] text-[#0284C7] font-black' : 'border-transparent text-slate-500 hover:text-slate-800 font-bold'"
                        class="pb-2 border-b-2 text-sm transition-all cursor-pointer flex items-center gap-2">
                    <i class="fa-solid fa-cart-shopping"></i>
                    <span>Riwayat Pembelian Masuk dari Supplier</span>
                </button>
            </div>

            <!-- Tanggal Filter Controls (Khusus Tab Log Pakan) -->
            <template x-if="historyTab === 'log'">
                <div class="flex flex-wrap items-center gap-2">
                    <!-- Quick Filter Buttons -->
                    <div class="flex items-center p-1 bg-slate-100 rounded-xl text-[11px] font-extrabold text-slate-600">
                        <button type="button" @click="setQuickDateFilter('all')" 
                                :class="dateFilterType === 'all' ? 'bg-white text-slate-900 shadow-xs rounded-lg' : 'hover:text-slate-900'"
                                class="px-2.5 py-1 transition-all cursor-pointer">
                            Semua
                        </button>
                        <button type="button" @click="setQuickDateFilter('today')" 
                                :class="dateFilterType === 'today' ? 'bg-white text-slate-900 shadow-xs rounded-lg' : 'hover:text-slate-900'"
                                class="px-2.5 py-1 transition-all cursor-pointer">
                            Hari Ini
                        </button>
                        <button type="button" @click="setQuickDateFilter('7days')" 
                                :class="dateFilterType === '7days' ? 'bg-white text-slate-900 shadow-xs rounded-lg' : 'hover:text-slate-900'"
                                class="px-2.5 py-1 transition-all cursor-pointer">
                            7 Hari
                        </button>
                    </div>

                    <!-- Date Range Inputs -->
                    <div class="flex items-center gap-1.5">
                        <input type="date" x-model="filterStartDate" @change="dateFilterType = 'custom'; currentPage = 1"
                               class="px-2.5 py-1.5 rounded-xl border border-slate-200 text-xs font-semibold text-slate-700 bg-slate-50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-sky-500 transition-all">
                        <span class="text-xs text-slate-400 font-bold">s/d</span>
                        <input type="date" x-model="filterEndDate" @change="dateFilterType = 'custom'; currentPage = 1"
                               class="px-2.5 py-1.5 rounded-xl border border-slate-200 text-xs font-semibold text-slate-700 bg-slate-50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-sky-500 transition-all">
                    </div>
                </div>
            </template>
        </div>

        <!-- TAB 1: Tabel Riwayat Log Pakan Harian -->
        <div x-show="historyTab === 'log'" class="space-y-4">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-50/80 text-[10px] uppercase font-extrabold text-slate-400 border-b border-slate-100">
                        <tr>
                            <th class="py-3.5 px-4">TANGGAL</th>
                            <th class="py-3.5 px-4">KOLAM / FASE</th>
                            <th class="py-3.5 px-4">JENIS &amp; JUMLAH PAKAN</th>
                            <th class="py-3.5 px-4">SUPLEMEN DAUN</th>
                            <th class="py-3.5 px-4">TOTAL BIAYA</th>
                            <th class="py-3.5 px-4">PH AIR</th>
                            <th class="py-3.5 px-4 text-right">PETUGAS</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 font-medium text-slate-700">
                        <template x-if="paginatedLogs.length === 0">
                            <tr>
                                <td colspan="7" class="py-10 text-center text-slate-400 text-xs font-medium">
                                    <i class="fa-solid fa-calendar-xmark text-2xl text-slate-300 block mb-1.5"></i>
                                    Tidak ada catatan log pakan yang cocok dengan filter.
                                </td>
                            </tr>
                        </template>

                        <template x-for="log in paginatedLogs" :key="log.id_pakan">
                            <tr class="hover:bg-slate-50/60 transition-colors">
                                <td class="py-3.5 px-4 font-extrabold text-slate-900" x-text="log.tgl_log"></td>
                                <td class="py-3.5 px-4">
                                    <div class="flex items-center gap-1.5">
                                        <span class="font-extrabold text-[#0B2570]" x-text="log.kolam ? log.kolam.nama_kolam : 'Kolam #' + log.id_kolam"></span>
                                        <span class="text-[9px] font-black px-1.5 py-0.5 rounded"
                                              :class="log.kategori_fase === 'pembibitan' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-blue-50 text-blue-700 border border-blue-200'"
                                              x-text="log.kategori_fase === 'pembibitan' ? 'Bibit' : 'Besar'">
                                        </span>
                                    </div>
                                </td>
                                <td class="py-3.5 px-4 font-bold text-slate-800">
                                    <div class="flex items-center gap-1.5">
                                        <span class="px-2 py-0.5 rounded-md bg-slate-100 font-extrabold" x-text="Number(log.kg_pelet).toFixed(1) + ' kg'"></span>
                                        <template x-if="log.stok_pakan">
                                            <span class="text-[10px] text-slate-500 font-bold" x-text="'(' + log.stok_pakan.nama_pakan + ')'"></span>
                                        </template>
                                    </div>
                                </td>
                                <td class="py-3.5 px-4">
                                    <span x-text="Number(log.kg_daun) > 0 ? (Number(log.kg_daun).toFixed(1) + ' kg ' + (log.jenis_daun ? '(' + log.jenis_daun + ')' : '')) : '-'"></span>
                                </td>
                                <td class="py-3.5 px-4 font-extrabold text-emerald-700" x-text="'Rp ' + Number(log.total_biaya).toLocaleString('id-ID')"></td>
                                <td class="py-3.5 px-4">
                                    <span class="px-2 py-0.5 rounded-md text-[10px] font-extrabold bg-sky-50 text-sky-800 border border-sky-100" x-text="'pH ' + (log.ph_air || '7.2')"></span>
                                </td>
                                <td class="py-3.5 px-4 text-right text-slate-500 font-semibold" x-text="log.user ? (log.user.nama || log.user.name) : 'Petugas'"></td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>

            <!-- Pagination Controls -->
            <div x-show="filteredLogs.length > 0" class="pt-3 border-t border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-3 text-xs">
                <span class="text-slate-500 font-medium">
                    Menampilkan <strong class="text-slate-800" x-text="((currentPage - 1) * perPage) + 1"></strong> - <strong class="text-slate-800" x-text="Math.min(currentPage * perPage, filteredLogs.length)"></strong> dari <strong class="text-slate-800" x-text="filteredLogs.length"></strong> catatan
                </span>

                <div class="flex items-center gap-1" x-show="totalPages > 1">
                    <button type="button" @click="goToPage(currentPage - 1)" :disabled="currentPage === 1"
                            :class="currentPage === 1 ? 'text-slate-300 cursor-not-allowed' : 'text-slate-600 hover:bg-slate-100 cursor-pointer'"
                            class="w-8 h-8 rounded-lg flex items-center justify-center font-bold transition-all">
                        <i class="fa-solid fa-chevron-left text-xs"></i>
                    </button>
                    <template x-for="p in visiblePages" :key="p">
                        <button type="button" @click="goToPage(p)"
                                :class="currentPage === p ? 'bg-[#0284C7] text-white font-black' : 'text-slate-600 hover:bg-slate-100 font-semibold'"
                                class="w-8 h-8 rounded-lg flex items-center justify-center text-xs transition-all cursor-pointer"
                                x-text="p">
                        </button>
                    </template>
                    <button type="button" @click="goToPage(currentPage + 1)" :disabled="currentPage === totalPages"
                            :class="currentPage === totalPages ? 'text-slate-300 cursor-not-allowed' : 'text-slate-600 hover:bg-slate-100 cursor-pointer'"
                            class="w-8 h-8 rounded-lg flex items-center justify-center font-bold transition-all">
                        <i class="fa-solid fa-chevron-right text-xs"></i>
                    </button>
                </div>
            </div>
        </div>

        <!-- TAB 2: Tabel Riwayat Pembelian Pakan dari Supplier -->
        <div x-show="historyTab === 'pembelian'" class="space-y-4">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-50/80 text-[10px] uppercase font-extrabold text-slate-400 border-b border-slate-100">
                        <tr>
                            <th class="py-3.5 px-4">TANGGAL &amp; NO NOTA</th>
                            <th class="py-3.5 px-4">ITEM PAKAN</th>
                            <th class="py-3.5 px-4">SUPPLIER MITRA</th>
                            <th class="py-3.5 px-4">JUMLAH BELI</th>
                            <th class="py-3.5 px-4">HARGA SATUAN</th>
                            <th class="py-3.5 px-4">TOTAL BIAYA</th>
                            <th class="py-3.5 px-4 text-right">STATUS KEUANGAN</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 font-medium text-slate-700">
                        @forelse($riwayatPembelian as $pb)
                        <tr class="hover:bg-slate-50/60 transition-colors">
                            <td class="py-3.5 px-4">
                                <span class="font-extrabold text-slate-900 block">{{ \Carbon\Carbon::parse($pb->tgl_beli)->format('d M Y') }}</span>
                                <span class="text-[10px] font-bold text-slate-400">{{ $pb->no_nota }}</span>
                            </td>
                            <td class="py-3.5 px-4 font-bold text-[#0B2570]">
                                <span>{{ $pb->stokPakan ? $pb->stokPakan->nama_pakan : 'Pakan #' . $pb->id_stok_pakan }}</span>
                            </td>
                            <td class="py-3.5 px-4">
                                <div class="flex items-center gap-2">
                                    <div class="w-6 h-6 rounded-md bg-emerald-50 text-emerald-600 flex items-center justify-center font-bold text-[10px]">
                                        <i class="fa-solid fa-truck-field"></i>
                                    </div>
                                    <span class="font-bold text-slate-800">{{ $pb->mitra ? $pb->mitra->nama_mitra : 'Supplier Eksternal' }}</span>
                                </div>
                            </td>
                            <td class="py-3.5 px-4 font-extrabold text-slate-900">
                                <span class="px-2.5 py-1 rounded-lg bg-emerald-50 text-emerald-800 border border-emerald-200">
                                    +{{ number_format($pb->jumlah, 1, ',', '.') }} {{ $pb->stokPakan ? $pb->stokPakan->satuan : 'kg' }}
                                </span>
                            </td>
                            <td class="py-3.5 px-4 text-slate-600">
                                Rp {{ number_format($pb->harga_satuan, 0, ',', '.') }}
                            </td>
                            <td class="py-3.5 px-4 font-black text-rose-600">
                                Rp {{ number_format($pb->total_biaya, 0, ',', '.') }}
                            </td>
                            <td class="py-3.5 px-4 text-right">
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-extrabold bg-emerald-100 text-emerald-800 border border-emerald-200">
                                    <i class="fa-solid fa-check text-[9px]"></i>
                                    <span>Tercatat Kas Keluar</span>
                                </span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="py-10 text-center text-slate-400 text-xs font-medium">
                                <i class="fa-solid fa-cart-arrow-down text-2xl text-slate-300 block mb-1.5"></i>
                                Belum ada riwayat pembelian pakan dari supplier.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>

    <!-- MODAL 1: Catat Pembelian Pakan dari Mitra Supplier -->
    <div x-show="showBeliModal" 
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-xs"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         style="display: none;">
        
        <div class="bg-white rounded-3xl max-w-lg w-full p-6 sm:p-7 shadow-2xl border border-slate-200 space-y-5"
             @click.outside="showBeliModal = false">
            
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-2xl bg-sky-50 text-sky-600 flex items-center justify-center font-bold text-base border border-sky-100">
                        <i class="fa-solid fa-cart-shopping"></i>
                    </div>
                    <div>
                        <h3 class="font-extrabold text-slate-900 text-base">Catat Pembelian Pakan Baru</h3>
                        <p class="text-xs text-slate-500">Stok bertambah & otomatis tercatat ke Keuangan.</p>
                    </div>
                </div>
                <button type="button" @click="showBeliModal = false" class="text-slate-400 hover:text-slate-700 cursor-pointer">
                    <i class="fa-solid fa-xmark text-lg"></i>
                </button>
            </div>

            <form @submit.prevent="handleSavePembelian()" class="space-y-4">
                
                <!-- Pilih Pakan -->
                <div>
                    <label class="text-[10px] font-extrabold uppercase tracking-wider text-slate-500 block mb-1">
                        PILIH ITEM PAKAN <span class="text-rose-500">*</span>
                    </label>
                    <select x-model="beliForm.id_stok_pakan" @change="onBeliPakanChange()" required
                            class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs font-semibold text-slate-800 bg-white focus:outline-none focus:ring-2 focus:ring-sky-500 transition-all cursor-pointer">
                        <option value="">Pilih Item Pakan...</option>
                        <template x-for="sp in stokList" :key="sp.id_stok_pakan">
                            <option :value="sp.id_stok_pakan" x-text="sp.nama_pakan + ' (Saat ini: ' + sp.stok_tersisa + ' ' + sp.satuan + ')'"></option>
                        </template>
                    </select>
                </div>

                <!-- Pilih Supplier Mitra -->
                <div>
                    <label class="text-[10px] font-extrabold uppercase tracking-wider text-slate-500 block mb-1">
                        SUPPLIER MITRA
                    </label>
                    <select x-model="beliForm.id_mitra"
                            class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs font-semibold text-slate-800 bg-white focus:outline-none focus:ring-2 focus:ring-sky-500 transition-all cursor-pointer">
                        <option value="">Supplier Eksternal / Mitra Bebas</option>
                        <template x-for="sup in suppliers" :key="sup.id_mitra">
                            <option :value="sup.id_mitra" x-text="sup.nama_mitra + ' (' + sup.tipe_mitra + ')'"></option>
                        </template>
                    </select>
                </div>

                <!-- Row: Jumlah & Harga -->
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="text-[10px] font-extrabold uppercase tracking-wider text-slate-500 block mb-1">
                            JUMLAH DIBELI <span class="text-rose-500">*</span> <span class="text-[9px] text-slate-400 font-normal lowercase">(maks. 1.000)</span>
                        </label>
                        <div class="flex items-center rounded-xl border border-slate-200 overflow-hidden focus-within:ring-2 focus-within:ring-sky-500">
                            <input type="number" step="0.1" min="0.1" max="1000" x-model="beliForm.jumlah" 
                                   @keydown="if(['-', 'e', '+'].includes($event.key)) $event.preventDefault()"
                                   @input="if(Number(beliForm.jumlah) > 1000) beliForm.jumlah = 1000; calcBeliTotal()" 
                                   placeholder="Contoh: 50" required
                                   class="w-full px-3 py-2 text-xs font-extrabold text-slate-900 border-0 focus:outline-none">
                            <span class="px-2.5 py-2 bg-slate-100 text-slate-500 text-xs font-bold" x-text="selectedBeliPakan ? selectedBeliPakan.satuan : 'kg'"></span>
                        </div>
                    </div>

                    <div>
                        <label class="text-[10px] font-extrabold uppercase tracking-wider text-slate-500 block mb-1">
                            HARGA PER SATUAN (RP) <span class="text-rose-500">*</span>
                        </label>
                        <div class="flex items-center rounded-xl border border-slate-200 overflow-hidden focus-within:ring-2 focus-within:ring-sky-500">
                            <span class="px-2.5 py-2 bg-slate-100 text-slate-500 text-xs font-bold">Rp</span>
                            <input type="number" min="0" x-model="beliForm.harga_satuan" 
                                   @keydown="if(['-', 'e', '+'].includes($event.key)) $event.preventDefault()"
                                   @input="calcBeliTotal()" 
                                   placeholder="Contoh: 12500" required
                                   class="w-full px-3 py-2 text-xs font-extrabold text-slate-900 border-0 focus:outline-none">
                        </div>
                    </div>
                </div>

                <!-- Total Biaya & Tanggal -->
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="text-[10px] font-extrabold uppercase tracking-wider text-slate-500 block mb-1">
                            TOTAL PENGELUARAN (RP)
                        </label>
                        <div class="px-3.5 py-2 rounded-xl bg-slate-100 border border-slate-200 text-xs font-black text-rose-600">
                            Rp <span x-text="Number(beliForm.total_biaya || 0).toLocaleString('id-ID')"></span>
                        </div>
                    </div>

                    <div>
                        <label class="text-[10px] font-extrabold uppercase tracking-wider text-slate-500 block mb-1">
                            TANGGAL PEMBELIAN
                        </label>
                        <input type="date" x-model="beliForm.tgl_beli" required
                               class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs font-semibold text-slate-800 bg-white focus:outline-none focus:ring-2 focus:ring-sky-500">
                    </div>
                </div>

                <!-- Info Box Auto Buku Kas -->
                <div class="p-3.5 bg-emerald-50 rounded-2xl border border-emerald-200 text-[11px] text-emerald-900 font-semibold flex items-start gap-2.5">
                    <i class="fa-solid fa-circle-check text-emerald-600 text-sm mt-0.5"></i>
                    <p class="leading-relaxed">
                        Data ini akan otomatis menambah saldo stok di gudang dan mencatat transaksi pengeluaran operasional di modul <strong>Keuangan</strong>.
                    </p>
                </div>

                <div class="flex items-center justify-end gap-2.5 pt-3 border-t border-slate-100">
                    <button type="button" @click="showBeliModal = false" class="px-4 py-2 rounded-xl border border-slate-200 text-xs font-bold text-slate-600 hover:bg-slate-50 cursor-pointer">
                        Batal
                    </button>
                    <button type="submit" :disabled="isSubmittingBeli"
                            class="px-5 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-extrabold text-xs shadow-md shadow-emerald-600/20 transition-all flex items-center gap-2 cursor-pointer">
                        <i class="fa-solid fa-check"></i>
                        <span x-text="isSubmittingBeli ? 'Menyimpan...' : 'Simpan Pembelian'"></span>
                    </button>
                </div>

            </form>
        </div>
    </div>

    <!-- MODAL 2: Hubungi Supplier Mitra via WhatsApp -->
    <div x-show="showSupplierModal" 
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-xs"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         style="display: none;">
        
        <div class="bg-white rounded-3xl max-w-xl w-full p-6 sm:p-7 shadow-2xl border border-slate-200 space-y-5"
             @click.outside="showSupplierModal = false">
            
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center font-bold text-base border border-emerald-100">
                        <i class="fa-brands fa-whatsapp text-lg"></i>
                    </div>
                    <div>
                        <h3 class="font-extrabold text-slate-900 text-base">Kontak WhatsApp Supplier Pakan</h3>
                        <p class="text-xs text-slate-500">Pesan pasokan pakan langsung via WhatsApp di luar sistem.</p>
                    </div>
                </div>
                <button type="button" @click="showSupplierModal = false" class="text-slate-400 hover:text-slate-700 cursor-pointer">
                    <i class="fa-solid fa-xmark text-lg"></i>
                </button>
            </div>

            <!-- Daftar Supplier List -->
            <div class="space-y-3 max-h-96 overflow-y-auto pr-1">
                <template x-for="sup in suppliers" :key="sup.id_mitra">
                    <div class="p-4 rounded-2xl border border-slate-200 bg-slate-50/50 hover:bg-white hover:border-emerald-300 transition-all flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                        <div class="space-y-1">
                            <div class="flex items-center gap-2">
                                <h4 class="font-extrabold text-slate-900 text-xs sm:text-sm" x-text="sup.nama_mitra"></h4>
                                <span class="text-[9px] font-black uppercase px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-800" x-text="sup.tipe_mitra"></span>
                            </div>
                            <p class="text-[11px] text-slate-500 flex items-center gap-1.5">
                                <i class="fa-solid fa-location-dot text-slate-400 text-[10px]"></i>
                                <span x-text="sup.alamat"></span>
                            </p>
                            <p class="text-[11px] font-bold text-slate-700 flex items-center gap-1.5">
                                <i class="fa-solid fa-phone text-slate-400 text-[10px]"></i>
                                <span x-text="sup.telepon"></span>
                            </p>
                        </div>

                        <a :href="sup.wa_link" target="_blank"
                           class="px-3.5 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-extrabold text-xs shadow-sm flex items-center justify-center gap-2 transition-all shrink-0">
                            <i class="fa-brands fa-whatsapp text-sm"></i>
                            <span>Chat Order WA</span>
                        </a>
                    </div>
                </template>

                <template x-if="suppliers.length === 0">
                    <div class="py-8 text-center text-slate-400 text-xs">
                        <i class="fa-solid fa-users-slash text-2xl text-slate-300 block mb-1"></i>
                        Belum ada data supplier mitra yang terdaftar.
                    </div>
                </template>
            </div>

            <div class="pt-3 border-t border-slate-100 flex items-center justify-between text-xs">
                <a href="{{ route('mitra') }}" class="text-sky-600 font-bold hover:underline flex items-center gap-1">
                    <i class="fa-solid fa-arrow-up-right-from-square text-[10px]"></i>
                    <span>Kelola Mitra di Menu Manajemen Mitra</span>
                </a>
                <button type="button" @click="showSupplierModal = false" class="px-4 py-1.5 rounded-xl bg-slate-100 text-slate-600 font-bold hover:bg-slate-200 cursor-pointer">
                    Tutup
                </button>
            </div>

        </div>
    </div>

    <!-- MODAL 3: Kelola Master Item Pakan -->
    <div x-show="showMasterModal" 
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-xs"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         style="display: none;">
        
        <div class="bg-white rounded-3xl max-w-lg w-full p-6 sm:p-7 shadow-2xl border border-slate-200 space-y-5"
             @click.outside="showMasterModal = false">
            
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center font-bold text-base border border-blue-100">
                        <i class="fa-solid fa-boxes-stacked"></i>
                    </div>
                    <div>
                        <h3 class="font-extrabold text-slate-900 text-base">Tambah Master Item Pakan</h3>
                        <p class="text-xs text-slate-500">Daftarkan jenis pakan baru ke dalam sistem inventori.</p>
                    </div>
                </div>
                <button type="button" @click="showMasterModal = false" class="text-slate-400 hover:text-slate-700 cursor-pointer">
                    <i class="fa-solid fa-xmark text-lg"></i>
                </button>
            </div>

            <form @submit.prevent="handleSaveMasterPakan()" class="space-y-4">
                
                <div>
                    <label class="text-[10px] font-extrabold uppercase tracking-wider text-slate-500 block mb-1">
                        NAMA PAKAN <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" x-model="masterForm.nama_pakan" placeholder="Contoh: Pelet Starter PF-800" required
                           class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs font-semibold text-slate-800 bg-white focus:outline-none focus:ring-2 focus:ring-sky-500 transition-all">
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="text-[10px] font-extrabold uppercase tracking-wider text-slate-500 block mb-1">
                            PERUNTUKAN FASE <span class="text-rose-500">*</span>
                        </label>
                        <select x-model="masterForm.kategori_peruntukan" required
                                class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs font-semibold text-slate-800 bg-white focus:outline-none focus:ring-2 focus:ring-sky-500 transition-all cursor-pointer">
                            <option value="pembesaran">🐟 Pembesaran</option>
                            <option value="pembibitan">🌱 Pembibitan</option>
                            <option value="semua">📦 Semua Fase</option>
                        </select>
                    </div>

                    <div>
                        <label class="text-[10px] font-extrabold uppercase tracking-wider text-slate-500 block mb-1">
                            SATUAN <span class="text-rose-500">*</span>
                        </label>
                        <input type="text" x-model="masterForm.satuan" placeholder="kg / sak / tray" required
                               class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs font-semibold text-slate-800 bg-white focus:outline-none focus:ring-2 focus:ring-sky-500 transition-all">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="text-[10px] font-extrabold uppercase tracking-wider text-slate-500 block mb-1">
                            STOK AWAL
                        </label>
                        <input type="number" step="0.1" min="0" x-model="masterForm.stok_tersisa" placeholder="0"
                               class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs font-semibold text-slate-800 bg-white focus:outline-none focus:ring-2 focus:ring-sky-500 transition-all">
                    </div>

                    <div>
                        <label class="text-[10px] font-extrabold uppercase tracking-wider text-slate-500 block mb-1">
                            BATAS MINIMUM (ALERT)
                        </label>
                        <input type="number" step="0.1" min="0" x-model="masterForm.batas_minimum" placeholder="10"
                               class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs font-semibold text-slate-800 bg-white focus:outline-none focus:ring-2 focus:ring-sky-500 transition-all">
                    </div>
                </div>

                <div>
                    <label class="text-[10px] font-extrabold uppercase tracking-wider text-slate-500 block mb-1">
                        HARGA ACUAN PER SATUAN (RP)
                    </label>
                    <input type="number" min="0" x-model="masterForm.harga_per_satuan" placeholder="12500"
                           class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs font-semibold text-slate-800 bg-white focus:outline-none focus:ring-2 focus:ring-sky-500 transition-all">
                </div>

                <div class="flex items-center justify-end gap-2.5 pt-3 border-t border-slate-100">
                    <button type="button" @click="showMasterModal = false" class="px-4 py-2 rounded-xl border border-slate-200 text-xs font-bold text-slate-600 hover:bg-slate-50 cursor-pointer">
                        Batal
                    </button>
                    <button type="submit" :disabled="isSubmittingMaster"
                            class="px-5 py-2 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-extrabold text-xs shadow-md shadow-blue-600/20 transition-all flex items-center gap-2 cursor-pointer">
                        <i class="fa-solid fa-plus"></i>
                        <span x-text="isSubmittingMaster ? 'Menyimpan...' : 'Tambah Item'"></span>
                    </button>
                </div>

            </form>
        </div>
    </div>

    <!-- Toast Notification -->
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
function pakanHubComponent() {
    return {
        stokList: {!! json_encode($enrichedStokPakan ?? []) !!},
        summary: {!! json_encode($stokSummary ?? ['total_stok_kg' => 0, 'stok_pembibitan_kg' => 0, 'stok_pembesaran_kg' => 0, 'item_kritis_count' => 0, 'item_waspada_count' => 0, 'item_aman_count' => 0]) !!},
        suppliers: {!! json_encode($suppliers ?? []) !!},
        activeKolams: {!! json_encode($activeKolams ?? []) !!},
        hatcheryKolams: {!! json_encode($hatcheryKolams ?? []) !!},
        logs: {!! json_encode($logs ?? []) !!},

        stokFilter: 'semua',
        historyTab: 'log', // 'log' or 'pembelian'

        // Modal States
        showBeliModal: false,
        showSupplierModal: false,
        showMasterModal: false,

        // Submitting flags
        isSubmittingLog: false,
        isSubmittingBeli: false,
        isSubmittingMaster: false,

        // Toast
        showToast: false,
        toastMessage: '',

        // Pagination for Logs
        currentPage: 1,
        perPage: 8,
        dateFilterType: 'all',
        filterStartDate: '',
        filterEndDate: '',

        // Form Log Pakan
        form: {
            kategori_fase: 'pembesaran',
            id_kolam: '',
            id_stok_pakan: '',
            tgl_log: new Date().toISOString().split('T')[0],
            kg_pelet: 10,
            kg_daun: 0,
            jenis_daun: '',
            total_biaya: 125000,
            ph_air: 7.2
        },

        // Form Pembelian Pakan
        beliForm: {
            id_stok_pakan: '',
            id_mitra: '',
            tgl_beli: new Date().toISOString().split('T')[0],
            jumlah: 50,
            harga_satuan: 12500,
            total_biaya: 625000
        },

        // Form Master Item Pakan
        masterForm: {
            nama_pakan: '',
            kategori_peruntukan: 'pembesaran',
            satuan: 'kg',
            stok_tersisa: 50,
            batas_minimum: 15,
            harga_per_satuan: 12500
        },

        init() {
            // Auto select default pakan in log form
            if (this.relevantStokList.length > 0) {
                this.form.id_stok_pakan = this.relevantStokList[0].id_stok_pakan;
                this.recalculateCost();
            }
        },

        get filteredStokList() {
            if (this.stokFilter === 'semua') return this.stokList;
            return this.stokList.filter(item => item.kategori_peruntukan === this.stokFilter || item.kategori_peruntukan === 'semua');
        },

        get relevantStokList() {
            return this.stokList.filter(item => item.kategori_peruntukan === this.form.kategori_fase || item.kategori_peruntukan === 'semua');
        },

        get selectedKolamInfo() {
            if (!this.form.id_kolam) return null;
            if (this.form.kategori_fase === 'pembesaran') {
                return this.activeKolams.find(k => k.id_kolam == this.form.id_kolam) || null;
            } else {
                return this.hatcheryKolams.find(hk => hk.id_kolam == this.form.id_kolam) || null;
            }
        },

        get selectedPakanItem() {
            if (!this.form.id_stok_pakan) return null;
            return this.stokList.find(s => s.id_stok_pakan == this.form.id_stok_pakan) || null;
        },

        get selectedBeliPakan() {
            if (!this.beliForm.id_stok_pakan) return null;
            return this.stokList.find(s => s.id_stok_pakan == this.beliForm.id_stok_pakan) || null;
        },

        get filteredLogs() {
            let list = this.logs;
            if (this.filterStartDate) {
                list = list.filter(item => item.tgl_log >= this.filterStartDate);
            }
            if (this.filterEndDate) {
                list = list.filter(item => item.tgl_log <= this.filterEndDate);
            }
            return list;
        },

        get totalPages() {
            return Math.ceil(this.filteredLogs.length / this.perPage) || 1;
        },

        get paginatedLogs() {
            const start = (this.currentPage - 1) * this.perPage;
            return this.filteredLogs.slice(start, start + this.perPage);
        },

        get visiblePages() {
            const pages = [];
            for (let i = 1; i <= this.totalPages; i++) pages.push(i);
            return pages;
        },

        selectFase(fase) {
            this.form.kategori_fase = fase;
            this.form.id_kolam = '';
            
            // Switch default pakan
            const rel = this.relevantStokList;
            if (rel.length > 0) {
                this.form.id_stok_pakan = rel[0].id_stok_pakan;
            } else {
                this.form.id_stok_pakan = '';
            }

            if (fase === 'pembibitan') {
                this.form.kg_pelet = 1.5;
                this.form.kg_daun = 0;
            } else {
                this.form.kg_pelet = 10;
            }
            this.recalculateCost();
        },

        onKolamChange() {
            if (this.selectedKolamInfo && this.form.kategori_fase === 'pembesaran') {
                const estPelet = Math.max(1, Math.round(this.selectedKolamInfo.biomassa_est * 0.025 * 10) / 10);
                this.form.kg_pelet = estPelet;
                this.recalculateCost();
            }
        },

        onHatcheryKolamChange() {
            if (this.selectedKolamInfo && this.form.kategori_fase === 'pembibitan') {
                this.form.kg_pelet = 1.0;
                this.recalculateCost();
            }
        },

        onStokPakanChange() {
            this.recalculateCost();
        },

        recalculateCost() {
            const pelet = Number(this.form.kg_pelet) || 0;
            const item = this.selectedPakanItem;
            const price = item ? (Number(item.harga_per_satuan) || 12500) : 12500;
            this.form.total_biaya = Math.round(pelet * price);
        },

        goToPage(page) {
            if (page >= 1 && page <= this.totalPages) {
                this.currentPage = page;
            }
        },

        setQuickDateFilter(type) {
            this.dateFilterType = type;
            this.currentPage = 1;
            const now = new Date();

            if (type === 'all') {
                this.filterStartDate = '';
                this.filterEndDate = '';
            } else if (type === 'today') {
                const todayStr = now.toISOString().split('T')[0];
                this.filterStartDate = todayStr;
                this.filterEndDate = todayStr;
            } else if (type === '7days') {
                const d = new Date();
                d.setDate(d.getDate() - 7);
                this.filterStartDate = d.toISOString().split('T')[0];
                this.filterEndDate = now.toISOString().split('T')[0];
            }
        },

        resetLogForm() {
            this.form = {
                kategori_fase: 'pembesaran',
                id_kolam: '',
                id_stok_pakan: this.relevantStokList.length > 0 ? this.relevantStokList[0].id_stok_pakan : '',
                tgl_log: new Date().toISOString().split('T')[0],
                kg_pelet: 10,
                kg_daun: 0,
                jenis_daun: '',
                total_biaya: 125000,
                ph_air: 7.2
            };
        },

        openBeliModal() {
            this.beliForm = {
                id_stok_pakan: this.stokList.length > 0 ? this.stokList[0].id_stok_pakan : '',
                id_mitra: this.suppliers.length > 0 ? this.suppliers[0].id_mitra : '',
                tgl_beli: new Date().toISOString().split('T')[0],
                jumlah: 50,
                harga_satuan: this.stokList.length > 0 ? this.stokList[0].harga_per_satuan : 12500,
                total_biaya: (this.stokList.length > 0 ? this.stokList[0].harga_per_satuan : 12500) * 50
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
                total_biaya: (item.harga_per_satuan || 12500) * (item.batas_minimum ? item.batas_minimum * 2 : 50)
            };
            this.showBeliModal = true;
        },

        onBeliPakanChange() {
            if (this.selectedBeliPakan) {
                this.beliForm.harga_satuan = this.selectedBeliPakan.harga_per_satuan || 12500;
                this.calcBeliTotal();
            }
        },

        calcBeliTotal() {
            if (this.beliForm.jumlah !== '' && Number(this.beliForm.jumlah) < 0) {
                this.beliForm.jumlah = Math.abs(Number(this.beliForm.jumlah));
            }
            if (this.beliForm.harga_satuan !== '' && Number(this.beliForm.harga_satuan) < 0) {
                this.beliForm.harga_satuan = Math.abs(Number(this.beliForm.harga_satuan));
            }
            const jml = Math.max(0, Number(this.beliForm.jumlah) || 0);
            const hrg = Math.max(0, Number(this.beliForm.harga_satuan) || 0);
            this.beliForm.total_biaya = Math.round(jml * hrg);
        },

        openSupplierModal(pakanName = null) {
            this.showSupplierModal = true;
        },

        openMasterModal() {
            this.masterForm = {
                nama_pakan: '',
                kategori_peruntukan: 'pembesaran',
                satuan: 'kg',
                stok_tersisa: 50,
                batas_minimum: 15,
                harga_per_satuan: 12500
            };
            this.showMasterModal = true;
        },

        async handleSaveLog() {
            if (!this.form.id_kolam) {
                alert('Silakan pilih Kolam aktif terlebih dahulu!');
                return;
            }
            if (Number(this.form.kg_pelet || 0) <= 0 && Number(this.form.kg_daun || 0) <= 0) {
                alert('Silakan masukkan jumlah pakan lebih dari 0!');
                return;
            }
            if (Number(this.form.kg_pelet || 0) > 100) {
                alert('Pemberian pakan utama maksimal 100 kg per sesi!');
                return;
            }
            if (Number(this.form.kg_daun || 0) > 100) {
                alert('Pemberian pakan tambahan maksimal 100 kg per sesi!');
                return;
            }

            this.isSubmittingLog = true;
            try {
                const res = await fetch('{{ route('log-pakan.store') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        id_kolam: this.form.id_kolam,
                        id_stok_pakan: this.form.id_stok_pakan || null,
                        kategori_fase: this.form.kategori_fase,
                        tgl_log: this.form.tgl_log,
                        kg_pelet: Math.min(100, Math.max(0, Number(this.form.kg_pelet) || 0)),
                        kg_daun: Math.min(100, Math.max(0, Number(this.form.kg_daun) || 0)),
                        jenis_daun: this.form.jenis_daun || null,
                        total_biaya: Math.max(0, Number(this.form.total_biaya) || 0),
                        ph_air: Math.max(0, Math.min(14, Number(this.form.ph_air) || 7.2))
                    })
                });

                const data = await res.json();
                if (res.ok && data.success) {
                    if (data.log) {
                        this.logs.unshift(data.log);
                        this.currentPage = 1;
                    }

                    // Potong stok lokal di browser
                    const usedKg = (Number(this.form.kg_pelet) || 0) + (Number(this.form.kg_daun) || 0);
                    if (this.form.id_stok_pakan) {
                        const targetItem = this.stokList.find(s => s.id_stok_pakan == this.form.id_stok_pakan);
                        if (targetItem) {
                            targetItem.stok_tersisa = Math.max(0, Number(targetItem.stok_tersisa) - usedKg);
                        }
                    }

                    this.toastMessage = data.message || 'Log pakan berhasil dicatat & stok terpotong!';
                    this.showToast = true;
                    setTimeout(() => { this.showToast = false; }, 4000);
                    this.resetLogForm();
                } else {
                    alert(data.message || 'Gagal menyimpan log pakan.');
                }
            } catch (err) {
                alert('Terjadi kesalahan saat menyimpan log pakan.');
            } finally {
                this.isSubmittingLog = false;
            }
        },

        async handleSavePembelian() {
            if (!this.beliForm.id_stok_pakan) {
                alert('Silakan pilih Item Pakan!');
                return;
            }
            if (Number(this.beliForm.jumlah || 0) <= 0) {
                alert('Jumlah pembelian pakan harus lebih dari 0 (tidak boleh minus atau 0)!');
                return;
            }
            if (Number(this.beliForm.jumlah || 0) > 1000) {
                alert('Jumlah pembelian pakan maksimal 1.000 ' + (this.selectedBeliPakan ? this.selectedBeliPakan.satuan : 'kg') + ' per transaksi!');
                return;
            }
            if (Number(this.beliForm.harga_satuan || 0) < 0) {
                alert('Harga satuan tidak boleh minus (negatif)!');
                return;
            }

            this.isSubmittingBeli = true;
            try {
                const res = await fetch('{{ route('log-pakan.beli') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        id_stok_pakan: this.beliForm.id_stok_pakan,
                        id_mitra: this.beliForm.id_mitra || null,
                        tgl_beli: this.beliForm.tgl_beli,
                        jumlah: Number(this.beliForm.jumlah),
                        harga_satuan: Number(this.beliForm.harga_satuan),
                        total_biaya: Number(this.beliForm.total_biaya)
                    })
                });

                const data = await res.json();
                if (res.ok && data.success) {
                    // Update lokal stok item
                    const targetItem = this.stokList.find(s => s.id_stok_pakan == this.beliForm.id_stok_pakan);
                    if (targetItem) {
                        targetItem.stok_tersisa = Number(targetItem.stok_tersisa) + Number(this.beliForm.jumlah);
                        targetItem.harga_per_satuan = Number(this.beliForm.harga_satuan);
                    }

                    this.showBeliModal = false;
                    this.toastMessage = data.message || 'Pembelian pakan berhasil dicatat & masuk kas!';
                    this.showToast = true;
                    setTimeout(() => { 
                        this.showToast = false; 
                        window.location.reload(); 
                    }, 1500);
                } else {
                    alert(data.message || 'Gagal mencatat pembelian pakan.');
                }
            } catch (err) {
                alert('Terjadi kesalahan saat mencatat pembelian pakan.');
            } finally {
                this.isSubmittingBeli = false;
            }
        },

        async handleSaveMasterPakan() {
            if (!this.masterForm.nama_pakan) {
                alert('Nama pakan harus diisi!');
                return;
            }

            this.isSubmittingMaster = true;
            try {
                const res = await fetch('{{ route('log-pakan.stok.store') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify(this.masterForm)
                });

                const data = await res.json();
                if (res.ok && data.success) {
                    this.showMasterModal = false;
                    this.toastMessage = data.message || 'Item pakan baru berhasil ditambahkan!';
                    this.showToast = true;
                    setTimeout(() => { 
                        this.showToast = false; 
                        window.location.reload(); 
                    }, 1500);
                } else {
                    alert(data.message || 'Gagal menambahkan item pakan.');
                }
            } catch (err) {
                alert('Terjadi kesalahan saat menambahkan item pakan.');
            } finally {
                this.isSubmittingMaster = false;
            }
        }
    };
}
</script>
@endpush

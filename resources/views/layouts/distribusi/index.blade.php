@extends('layouts.app')

@section('title', 'Distribusi & Order - AMS BUDIDAYA')

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
                            <option value="">Pilih Mitra Distributor </option>
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
                            <span x-show="form.stok_biomassa !== null && form.stok_biomassa !== undefined && form.id_pembesaran" class="px-2.5 py-1 rounded-lg bg-emerald-50 border border-emerald-200 text-emerald-800 text-[11px] font-bold">
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

                    <div x-show="form.totalBerat && Number(form.totalBerat) > 0 && form.id_pembesaran" class="pt-2">
                        <!-- Skenario A: Stok Utama Mencukupi / Lebih -->
                        <template x-if="Number(form.totalBerat) <= Number(form.stok_biomassa || 0)">
                            <div class="p-3.5 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-900 text-xs space-y-2.5 shadow-xs">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-2 font-extrabold text-emerald-800">
                                        <i class="fa-solid fa-circle-check text-emerald-600"></i>
                                        <span>Stok Kolam Utama Mencukupi</span>
                                    </div>
                                    <span class="px-2 py-0.5 rounded-md bg-emerald-100 text-emerald-800 text-[10px] font-extrabold">
                                        Sisa di Kolam: <span x-text="(Number(form.stok_biomassa) - Number(form.totalBerat)).toLocaleString('id-ID') + ' kg'"></span>
                                    </span>
                                </div>

                                <div class="p-3 bg-white rounded-xl border border-emerald-200/80 space-y-2.5">
                                    <label class="flex items-center gap-2.5 cursor-pointer select-none">
                                        <input type="checkbox" x-model="panen_kuras" class="rounded text-emerald-600 focus:ring-emerald-500 w-4 h-4 cursor-pointer">
                                        <div class="space-y-0.5">
                                            <span class="text-xs font-extrabold text-slate-800 block">Panen Kuras Total Kolam</span>
                                            <span class="text-[11px] text-slate-500 font-medium block">Pindahkan sisa ikan surplus (<strong x-text="(Number(form.stok_biomassa) - Number(form.totalBerat)).toLocaleString('id-ID') + ' kg'"></strong>) ke Kolam Kosong atau Kolam Stok.</span>
                                        </div>
                                    </label>

                                    <div x-show="panen_kuras" x-transition class="pt-2.5 border-t border-slate-100 space-y-1.5">
                                        <label class="text-[10px] font-extrabold uppercase tracking-wider text-slate-700 block">
                                            PILIH KOLAM PENAMPUNGAN SISA IKAN *
                                        </label>
                                        <select x-model="id_kolam_surplus" class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs font-semibold text-slate-800 bg-slate-50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500 cursor-pointer">
                                            <option value="">-- Pilih Kolam Kosong / Kolam Stok --</option>
                                            <template x-for="kp in emptyAndStockPonds" :key="kp.id_kolam">
                                                <option :value="kp.id_kolam" x-text="kp.label"></option>
                                            </template>
                                        </select>
                                        <p class="text-[10px] text-emerald-700 font-medium">
                                            <i class="fa-solid fa-circle-info"></i> Kolam utama akan dikosongkan, dan sisa ikan otomatis masuk ke kolam tujuan terpilih.
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </template>

                        <!-- Skenario B: Stok Utama Kurang (Defisit Dipenuhi Cross-Batch / Buffer) -->
                        <template x-if="Number(form.totalBerat) > Number(form.stok_biomassa || 0)">
                            <div class="p-4 rounded-2xl bg-sky-50/80 border border-sky-200 text-sky-950 text-xs space-y-3.5 shadow-xs">
                                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 border-b border-sky-200/70 pb-2.5">
                                    <div class="flex items-center gap-2 font-extrabold text-sky-900">
                                        <i class="fa-solid fa-arrows-split-up-and-left text-sky-600"></i>
                                        <span>Alokasi Sumber Tambahan (Defisit Terdeteksi)</span>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <span class="px-2 py-0.5 rounded-md bg-rose-100 border border-rose-200 text-rose-800 text-[10px] font-extrabold">
                                            Defisit: <span x-text="'-' + defisitKg.toLocaleString('id-ID') + ' kg'"></span>
                                        </span>
                                        <button type="button" @click="autoFillDefisitFromBuffer()" class="px-2.5 py-1 rounded-lg bg-sky-600 hover:bg-sky-700 text-white text-[10px] font-bold shadow-2xs transition-all flex items-center gap-1 cursor-pointer">
                                            <i class="fa-solid fa-wand-magic-sparkles text-[9px]"></i>
                                            <span>Penuhi dari Kolam Stok</span>
                                        </button>
                                    </div>
                                </div>

                                <p class="text-[11px] text-sky-800 leading-relaxed">
                                    Stok kolam utama <strong x-text="form.kolam_asal || 'Kolam Utama'"></strong> hanya tersedia <strong x-text="Number(form.stok_biomassa || 0).toLocaleString('id-ID') + ' kg'"></strong>. Silakan tentukan sumber pemenuhan sisa <strong class="text-rose-700" x-text="defisitKg.toLocaleString('id-ID') + ' kg'"></strong>:
                                </p>

                                <!-- Multi-Source Input Rows -->
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-3 bg-white p-3.5 rounded-xl border border-sky-100">
                                    <!-- Source 1: Kolam Stok / Buffer -->
                                    <div class="space-y-1.5">
                                        <label class="text-[10px] font-extrabold uppercase tracking-wider text-slate-700 block flex items-center justify-between gap-1">
                                            <span class="flex items-center gap-1">
                                                <i class="fa-solid fa-boxes-stacked text-sky-600"></i>
                                                <span>1. AMBIL DARI KOLAM STOK / BUFFER</span>
                                            </span>
                                            <span class="text-[9px] text-sky-700 font-bold" x-show="form.jenis_ikan" x-text="'(Jenis: ' + form.jenis_ikan + ')'"></span>
                                        </label>
                                        <select x-model="alokasi.id_batch_buffer" class="w-full px-2.5 py-1.5 rounded-lg border border-slate-200 text-[11px] font-semibold text-slate-800 bg-slate-50 focus:bg-white focus:outline-none focus:ring-1 focus:ring-sky-500">
                                            <option value="" x-text="availableBufferBatches.length > 0 ? '-- Pilih Kolam Stok / Buffer --' : '-- Tidak ada stok buffer sejenis --'"></option>
                                            <template x-for="b in availableBufferBatches" :key="b.id_pembesaran">
                                                <option :value="b.id_pembesaran" x-text="b.label"></option>
                                            </template>
                                        </select>
                                        <div class="flex items-center gap-1.5">
                                            <input type="number" step="0.1" min="0" x-model="alokasi.buffer_kg" placeholder="0.0" class="flex-1 px-2.5 py-1.5 rounded-lg border border-slate-200 text-xs font-bold text-sky-800 bg-white focus:outline-none focus:ring-1 focus:ring-sky-500">
                                            <span class="text-[11px] font-bold text-slate-400">kg</span>
                                        </div>
                                    </div>

                                    <!-- Source 2: Kolam Pembesaran Lain (Cross-Batch) -->
                                    <div class="space-y-1.5">
                                        <label class="text-[10px] font-extrabold uppercase tracking-wider text-slate-700 block flex items-center justify-between gap-1">
                                            <span class="flex items-center gap-1">
                                                <i class="fa-solid fa-shuffle text-emerald-600"></i>
                                                <span>2. AMBIL DARI KOLAM LAIN (CROSS-BATCH)</span>
                                            </span>
                                            <span class="text-[9px] text-emerald-700 font-bold" x-show="form.jenis_ikan" x-text="'(Jenis: ' + form.jenis_ikan + ')'"></span>
                                        </label>
                                        <select x-model="alokasi.id_batch_cross" class="w-full px-2.5 py-1.5 rounded-lg border border-slate-200 text-[11px] font-semibold text-slate-800 bg-slate-50 focus:bg-white focus:outline-none focus:ring-1 focus:ring-emerald-500">
                                            <option value="" x-text="availableCrossBatches.length > 0 ? '-- Pilih Batch Kolam Lain --' : '-- Tidak ada kolam lain sejenis --'"></option>
                                            <template x-for="b in availableCrossBatches" :key="b.id_pembesaran">
                                                <option :value="b.id_pembesaran" x-text="b.label"></option>
                                            </template>
                                        </select>
                                        <div class="flex items-center gap-1.5">
                                            <input type="number" step="0.1" min="0" x-model="alokasi.cross_kg" placeholder="0.0" class="flex-1 px-2.5 py-1.5 rounded-lg border border-slate-200 text-xs font-bold text-emerald-800 bg-white focus:outline-none focus:ring-1 focus:ring-emerald-500">
                                            <span class="text-[11px] font-bold text-slate-400">kg</span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Live Fulfillment Balance Bar -->
                                <div class="p-2.5 rounded-xl border flex flex-wrap items-center justify-between gap-2"
                                     :class="totalAlokasiKg >= Number(form.totalBerat) ? 'bg-emerald-50 border-emerald-200 text-emerald-900' : 'bg-amber-50 border-amber-200 text-amber-900'">
                                    <div class="flex items-center gap-2 text-[11px] font-bold">
                                        <i class="fa-solid" :class="totalAlokasiKg >= Number(form.totalBerat) ? 'fa-circle-check text-emerald-600' : 'fa-triangle-exclamation text-amber-600'"></i>
                                        <span>Status Alokasi: <strong x-text="totalAlokasiKg.toLocaleString('id-ID') + ' / ' + Number(form.totalBerat).toLocaleString('id-ID') + ' kg'"></strong></span>
                                    </div>
                                    <div>
                                        <template x-if="totalAlokasiKg >= Number(form.totalBerat)">
                                            <span class="px-2 py-0.5 rounded-md bg-emerald-100 text-emerald-800 text-[10px] font-extrabold">✓ Terpenuhi Lengkap</span>
                                        </template>
                                        <template x-if="totalAlokasiKg < Number(form.totalBerat)">
                                            <span class="px-2 py-0.5 rounded-md bg-amber-100 text-amber-800 text-[10px] font-extrabold" x-text="'Kurang ' + (Number(form.totalBerat) - totalAlokasiKg).toLocaleString('id-ID') + ' kg lagi'"></span>
                                        </template>
                                    </div>
                                </div>
                            </div>
                        </template>
                    </div>

                    <p class="text-[10px] text-slate-400 italic flex items-center gap-1">
                        <i class="fa-solid fa-circle-info text-sky-400"></i>
                        *Harga per kg: <strong>Otomatis Dihitung Sesuai Berat Muat</strong>
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

    <!-- Info Banner Buffer & Cross-Batch System (Solid Clean Theme) -->
    <div x-show="!showForm" class="bg-white p-4 sm:p-5 rounded-2xl border border-slate-200 shadow-xs flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
        <div class="flex items-center gap-3.5">
            <div class="w-10 h-10 rounded-xl bg-sky-50 text-sky-700 border border-sky-200 flex items-center justify-center shrink-0 text-lg">
                <i class="fa-solid fa-shield-halved"></i>
            </div>
            <div>
                <h4 class="text-xs sm:text-sm font-extrabold tracking-wide uppercase text-slate-900">BUFFER STOK PEMBEROKAN &amp; CROSS-BATCH FULFILLMENT</h4>
                <p class="text-xs text-slate-500 font-medium mt-0.5">
                    Surplus panen otomatis masuk buffer pemberokan, sedangkan defisit order mitra terpenuhi melalui pasokan lintas kolam.
                </p>
            </div>
        </div>
        <div class="flex items-center gap-3 shrink-0">
            <div class="px-3.5 py-1.5 rounded-xl bg-slate-50 border border-slate-200 text-center">
                <span class="text-[9px] uppercase tracking-wider text-slate-400 block font-extrabold">STOK SIAP PANEN</span>
                <span class="text-xs font-extrabold text-slate-900">{{ number_format($totalStokSiapPanen ?? 0, 0, ',', '.') }} kg</span>
            </div>
            <div class="px-3.5 py-1.5 rounded-xl bg-emerald-50 border border-emerald-200 text-center">
                <span class="text-[9px] uppercase tracking-wider text-emerald-700 block font-extrabold">BUFFER PEMBEROKAN</span>
                <span class="text-xs font-extrabold text-emerald-800">{{ number_format($totalBufferPemberokan ?? 0, 0, ',', '.') }} kg</span>
            </div>
        </div>
    </div>

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

                    <div class="bg-slate-50 border border-slate-100 p-3 rounded-xl space-y-2.5">
                        <div class="grid grid-cols-2 gap-2">
                            <div>
                                <span class="text-[10px] font-extrabold text-slate-400 uppercase tracking-widest block">VOLUME</span>
                                <h3 class="text-base font-black text-slate-900 mt-0.5" x-text="order.volume"></h3>
                            </div>
                            <div class="text-right">
                                <span class="text-[10px] font-extrabold text-slate-400 uppercase tracking-widest block">TOTAL HARGA</span>
                                <h3 class="text-base font-black text-emerald-700 mt-0.5" x-text="order.harga_format || ('Rp ' + Number(order.harga_total || 0).toLocaleString('id-ID'))"></h3>
                            </div>
                        </div>

                        <div class="pt-2 border-t border-slate-200/60 flex items-center justify-between text-[11px]" x-show="order.batch_code">
                            <span class="text-slate-400 font-semibold">Batch Asal:</span>
                            <span class="font-extrabold text-sky-700 bg-sky-50 px-2 py-0.5 rounded-md border border-sky-100" x-text="order.batch_code"></span>
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

    <!-- Modal Detail Invoice / Bukti Order Distribusi -->
    <div x-show="showInvoice" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 backdrop-blur-xs p-4" style="display: none;">
        <div class="w-full max-w-xl rounded-3xl bg-white shadow-2xl border border-slate-200 overflow-hidden" @click.outside="closeInvoice()">
            <!-- Modal Header -->
            <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100 bg-slate-50/80">
                <div class="flex items-center gap-3">
                    <img src="{{ asset('images/logo sample di invoice.png') }}" alt="Logo Aquafarm" class="w-9 h-9 object-contain">
                    <div>
                        <p class="text-[10px] font-extrabold uppercase tracking-[0.2em] text-slate-400">FAKTUR PENGIRIMAN &amp; INVOICE</p>
                        <h3 class="text-base font-extrabold text-slate-900" x-text="selectedInvoice?.id || '#INV-0000'"></h3>
                    </div>
                </div>
                <button type="button" @click="closeInvoice()" class="w-8 h-8 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-500 flex items-center justify-center transition-colors">✕</button>
            </div>

            <!-- Modal Body -->
            <div class="p-6 space-y-4 max-h-[75vh] overflow-y-auto" x-show="selectedInvoice">
                
                <!-- Layout 2 Kolom Identitas Pengirim & Penerima -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <!-- Pengirim -->
                    <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-200/80 text-xs space-y-1.5">
                        <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-500 block">DARI (PENGIRIM)</span>
                        <div class="font-extrabold text-slate-800 text-xs">PT Aquafarm Nusantara (SIM Budidaya)</div>
                        <div class="text-[11px] text-slate-600 leading-relaxed">Kawasan Minapolitan Agribisnis No. 88, Blok Perikanan Terpadu, Jawa Barat 40123</div>
                        <div class="text-[11px] text-slate-700 font-semibold pt-0.5"><span class="text-slate-400 font-normal">Telp:</span> +62 812-8899-0011</div>
                    </div>

                    <!-- Penerima (Nomor Telp Disensor) -->
                    <div class="p-3.5 rounded-2xl bg-sky-50/60 border border-sky-200/80 text-xs space-y-1.5">
                        <div class="flex items-center justify-between">
                            <span class="text-[10px] font-extrabold uppercase tracking-wider text-sky-800 block">KEPADA (PENERIMA)</span>
                            <span class="text-[9px] font-bold text-sky-700 bg-white px-2 py-0.5 rounded-md border border-sky-200">Telp Terlindungi</span>
                        </div>
                        <div class="font-black text-slate-900 text-xs" x-text="selectedInvoice?.customer || '-'"></div>
                        <div class="text-[11px] text-slate-700 leading-relaxed" x-text="selectedInvoice?.alamat || '-'"></div>
                        <div class="text-[11px] text-slate-800 font-bold pt-0.5">
                            <span class="text-slate-400 font-normal">Telp:</span> <span class="font-mono text-sky-950" x-text="maskPhoneNumber(selectedInvoice?.telepon)"></span>
                        </div>
                    </div>
                </div>

                <!-- Rincian Komoditas & Muatan -->
                <div class="rounded-2xl border border-slate-200 p-4 space-y-3 bg-white">
                    <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400 block">RINCIAN MUATAN &amp; STATUS</span>
                    
                    <div class="grid grid-cols-2 gap-3 text-xs">
                        <div>
                            <span class="text-slate-400 text-[11px] block">Komoditas Ikan</span>
                            <span class="font-bold text-slate-800" x-text="(selectedInvoice?.jenis_ikan || 'Ikan Konsumsi') + (selectedInvoice?.batch_code ? ' (' + selectedInvoice.batch_code + ')' : '')"></span>
                        </div>
                        <div>
                            <span class="text-slate-400 text-[11px] block">Volume / Berat</span>
                            <span class="font-extrabold text-slate-900" x-text="selectedInvoice?.volume || '-'"></span>
                        </div>
                        <div>
                            <span class="text-slate-400 text-[11px] block">Tanggal Order</span>
                            <span class="font-semibold text-slate-700" x-text="selectedInvoice?.tanggal || '-'"></span>
                        </div>
                        <div>
                            <span class="text-slate-400 text-[11px] block">Status Pengiriman</span>
                            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold uppercase inline-block mt-0.5" :class="statusClass(selectedInvoice?.status)" x-text="statusLabel(selectedInvoice?.status)"></span>
                        </div>
                    </div>

                    <div class="pt-3 border-t border-slate-100 flex items-center justify-between">
                        <span class="text-xs font-bold text-slate-600">Total Nilai Transaksi</span>
                        <span class="text-base font-black text-emerald-700" x-text="selectedInvoice?.harga_format || 'Rp 0'"></span>
                    </div>
                </div>

                <!-- Notice Handle With Care -->
                <div class="p-3 rounded-xl bg-amber-50/80 border border-amber-200/80 flex items-center justify-between text-xs text-amber-900 font-bold">
                    <span class="tracking-widest uppercase text-[11px] flex items-center gap-2">
                        <i class="fa-solid fa-triangle-exclamation text-amber-600"></i>
                        <span>PLEASE HANDLE WITH CARE</span>
                    </span>
                    <span class="text-[10px] text-amber-700 font-medium">Ikan Hidup Beroksigen</span>
                </div>

            </div>

            <!-- Modal Footer -->
            <div class="flex items-center justify-between gap-3 px-6 py-4 border-t border-slate-100 bg-slate-50/80">
                <button type="button" @click="closeInvoice()" class="px-4 py-2 rounded-xl border border-slate-200 text-xs font-bold text-slate-600 hover:bg-white transition-colors">Tutup</button>
                <div class="flex items-center gap-2">
                    <button type="button" @click="printLabel(selectedInvoice)" class="px-4 py-2 rounded-xl bg-[#051B44] text-white text-xs font-bold hover:bg-sky-950 flex items-center gap-2 shadow-sm transition-all">
                        <i class="fa-solid fa-print text-xs"></i>
                        <span>Cetak Label Pengiriman</span>
                    </button>
                </div>
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
        emptyAndStockPonds: {!! json_encode($emptyAndStockPonds ?? []) !!},
        panen_kuras: false,
        id_kolam_surplus: '',

        alokasi: {
            buffer_kg: '',
            id_batch_buffer: '',
            cross_kg: '',
            id_batch_cross: ''
        },

        get availableBufferBatches() {
            const currentSpecies = (this.form.jenis_ikan || '').toLowerCase().trim();
            return (this.batches || []).filter(b => {
                const isDifferentBatch = b.id_pembesaran != this.form.id_pembesaran;
                const isSameSpecies = !currentSpecies || (b.jenis_ikan || '').toLowerCase().trim() === currentSpecies;
                const isBufferPond = b.is_stok || (b.kolam && (b.kolam.toLowerCase().includes('stok') || b.kolam.toLowerCase().includes('pemberokan') || b.kolam.toLowerCase().includes('penampungan')));
                return isDifferentBatch && isSameSpecies && isBufferPond;
            });
        },

        get availableCrossBatches() {
            const currentSpecies = (this.form.jenis_ikan || '').toLowerCase().trim();
            return (this.batches || []).filter(b => {
                const isDifferentBatch = b.id_pembesaran != this.form.id_pembesaran && b.id_pembesaran != this.alokasi.id_batch_buffer;
                const isSameSpecies = !currentSpecies || (b.jenis_ikan || '').toLowerCase().trim() === currentSpecies;
                return isDifferentBatch && isSameSpecies;
            });
        },

        get defisitKg() {
            return Math.max(0, Number(this.form.totalBerat || 0) - Number(this.form.stok_biomassa || 0));
        },

        get totalAlokasiKg() {
            const utama = Math.min(Number(this.form.totalBerat || 0), Number(this.form.stok_biomassa || 0));
            const buf = Number(this.alokasi.buffer_kg || 0);
            const crs = Number(this.alokasi.cross_kg || 0);
            return Number((utama + buf + crs).toFixed(1));
        },

        autoFillDefisitFromBuffer() {
            const deficit = this.defisitKg;
            this.alokasi.buffer_kg = deficit;
            if (!this.alokasi.id_batch_buffer && this.availableBufferBatches.length > 0) {
                this.alokasi.id_batch_buffer = this.availableBufferBatches[0].id_pembesaran;
            } else if (!this.alokasi.id_batch_cross && this.availableCrossBatches.length > 0) {
                this.alokasi.id_batch_cross = this.availableCrossBatches[0].id_pembesaran;
                this.alokasi.cross_kg = deficit;
                this.alokasi.buffer_kg = 0;
            }
        },

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
                // Pastikan id buffer & cross batch sesuai jenis ikan yang sama
                const validBuffer = this.availableBufferBatches.find(b => b.id_pembesaran == this.alokasi.id_batch_buffer);
                if (!validBuffer) {
                    this.alokasi.id_batch_buffer = this.availableBufferBatches.length > 0 ? this.availableBufferBatches[0].id_pembesaran : '';
                }
                const validCross = this.availableCrossBatches.find(b => b.id_pembesaran == this.alokasi.id_batch_cross);
                if (!validCross) {
                    this.alokasi.id_batch_cross = '';
                }
            } else {
                this.form.jenis_ikan = '';
                this.form.kolam_asal = '';
                this.form.stok_biomassa = null;
                this.alokasi.id_batch_buffer = '';
                this.alokasi.id_batch_cross = '';
            }
        },

        openCreateForm() {
            this.formMode = 'create';
            this.showForm = true;
            this.panen_kuras = false;
            this.id_kolam_surplus = '';
            this.alokasi = {
                buffer_kg: '',
                id_batch_buffer: this.availableBufferBatches.length > 0 ? this.availableBufferBatches[0].id_pembesaran : '',
                cross_kg: '',
                id_batch_cross: ''
            };
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
            const matchedMitra = this.mitraList.find(m => m.nama_mitra === order.customer || m.id_mitra == order.id_mitra);
            const matchedBatch = this.batches.find(b => b.id_pembesaran == order.id_pembesaran);
            this.form = {
                id: order.id,
                id_transaksi: order.id_transaksi || null,
                id_mitra: order.id_mitra || (matchedMitra ? matchedMitra.id_mitra : ''),
                id_pembesaran: order.id_pembesaran || (matchedBatch ? matchedBatch.id_pembesaran : ''),
                jenis_ikan: order.jenis_ikan || (matchedBatch ? matchedBatch.jenis_ikan : ''),
                kolam_asal: order.kolam_asal || (matchedBatch ? matchedBatch.kolam : ''),
                stok_biomassa: matchedBatch ? Number(matchedBatch.biomassa_est || 0) : (order.stok_biomassa !== undefined ? Number(order.stok_biomassa) : null),
                tanggal: order.tanggal,
                mitra: order.customer,
                alamat: order.alamat,
                jenisOrder: order.jenis_order || 'reguler',
                status: order.status,
                totalBerat: String(order.total_kg !== undefined ? order.total_kg : (order.volume || '')).replace(/[^0-9.]/g, ''),
                totalHarga: order.harga_format || ''
            };
            this.jenisOrder = order.jenis_order || 'reguler';
        },

        async saveForm() {
            if (this.formMode === 'edit') {
                const rawId = this.form.id_transaksi || String(this.form.id).replace(/[^0-9]/g, '');
                const totalKg = Number(this.form.totalBerat) || 0;
                const hargaTotal = this.form.totalHarga ? Number(String(this.form.totalHarga).replace(/[^0-9]/g, '')) : (totalKg * 35000);

                const editPayload = {
                    status_order: this.form.status,
                    Total_kg: totalKg,
                    harga_total: hargaTotal
                };

                if (this.panen_kuras && this.id_kolam_surplus) {
                    const surplusKg = Math.max(0, Number(this.form.stok_biomassa || 0) - totalKg);
                    editPayload.panen_kuras = true;
                    editPayload.id_kolam_surplus = this.id_kolam_surplus;
                    editPayload.surplus_kg = surplusKg;
                }

                if (totalKg > Number(this.form.stok_biomassa || 0) && this.form.status === 'pemberokian') {
                    const selBuf = this.batches.find(b => b.id_pembesaran == this.alokasi.id_batch_buffer);
                    const selCross = this.batches.find(b => b.id_pembesaran == this.alokasi.id_batch_cross);
                    editPayload.alokasi_detail = {
                        utama_kg: Math.min(totalKg, Number(this.form.stok_biomassa || 0)),
                        buffer_kg: Number(this.alokasi.buffer_kg || 0),
                        id_batch_buffer: this.alokasi.id_batch_buffer,
                        buffer_nama: selBuf ? (selBuf.kolam || selBuf.label) : 'Kolam Stok',
                        cross_kg: Number(this.alokasi.cross_kg || 0),
                        id_batch_cross: this.alokasi.id_batch_cross,
                        cross_nama: selCross ? (selCross.kolam || selCross.label) : 'Cross-Batch',
                    };
                }

                try {
                    const res = await fetch('/distribusi/' + rawId, {
                        method: 'PUT',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify(editPayload)
                    });
                    const data = await res.json();

                    if (!res.ok || !data.success) {
                        alert(data.message || 'Gagal mengubah status pesanan.');
                        return;
                    }

                    const target = this.orders.find(item => item.id === this.form.id || item.id_transaksi == rawId);
                    if (target) {
                        target.status = data.transaksi ? data.transaksi.status_order : this.form.status;
                        if (data.transaksi && data.transaksi.Jenis_order) {
                            target.jenis_ikan = data.transaksi.Jenis_order;
                        }
                    }

                    alert(data.message || 'Perubahan pesanan berhasil disimpan!');
                } catch(e) {
                    alert('Terjadi kesalahan koneksi saat memperbarui pesanan.');
                    return;
                }
            } else {
                if (this.form.tanggal && this.form.tanggal > this.maxDate) {
                    alert('Tanggal order tidak boleh melebihi batas maksimal minggu ini (' + this.maxDate + ')!');
                    return;
                }
                if (!this.form.id_mitra) {
                    alert('Silakan pilih Mitra Distributor ');
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

                const payload = {
                    id_mitra: this.form.id_mitra,
                    id_pembesaran: this.form.id_pembesaran,
                    tanggal_order: this.form.tanggal,
                    Total_kg: totalKg,
                    harga_total: hargaTotal,
                    Jenis_order: this.form.jenis_ikan || this.form.jenisOrder,
                    status_order: this.form.status
                };

                if (this.panen_kuras && this.id_kolam_surplus) {
                    const surplusKg = Math.max(0, Number(this.form.stok_biomassa || 0) - totalKg);
                    payload.panen_kuras = true;
                    payload.id_kolam_surplus = this.id_kolam_surplus;
                    payload.surplus_kg = surplusKg;
                }

                if (totalKg > Number(this.form.stok_biomassa || 0)) {
                    const selBuf = this.batches.find(b => b.id_pembesaran == this.alokasi.id_batch_buffer);
                    const selCross = this.batches.find(b => b.id_pembesaran == this.alokasi.id_batch_cross);
                    payload.alokasi_detail = {
                        utama_kg: Math.min(totalKg, Number(this.form.stok_biomassa || 0)),
                        buffer_kg: Number(this.alokasi.buffer_kg || 0),
                        id_batch_buffer: this.alokasi.id_batch_buffer,
                        buffer_nama: selBuf ? (selBuf.kolam || selBuf.label) : 'Kolam Stok',
                        cross_kg: Number(this.alokasi.cross_kg || 0),
                        id_batch_cross: this.alokasi.id_batch_cross,
                        cross_nama: selCross ? (selCross.kolam || selCross.label) : 'Cross-Batch',
                    };
                }

                try {
                    const res = await fetch('{{ route('distribusi.store') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify(payload)
                    });
                    const data = await res.json();
                    if (res.ok && data.success) {
                        const newTrx = data.transaksi;
                        const matchedBatch = this.batches.find(b => b.id_pembesaran == this.form.id_pembesaran);
                        
                        // Deduct local stocks
                        if (matchedBatch) {
                            const takenUtama = Math.min(totalKg, Number(this.form.stok_biomassa || 0));
                            matchedBatch.biomassa_est = Math.max(0, matchedBatch.biomassa_est - takenUtama);
                        }
                        if (payload.alokasi_detail && payload.alokasi_detail.id_batch_buffer) {
                            const bBuf = this.batches.find(b => b.id_pembesaran == payload.alokasi_detail.id_batch_buffer);
                            if (bBuf) {
                                bBuf.biomassa_est = Math.max(0, bBuf.biomassa_est - payload.alokasi_detail.buffer_kg);
                            }
                        }
                        if (payload.alokasi_detail && payload.alokasi_detail.id_batch_cross) {
                            const bCrs = this.batches.find(b => b.id_pembesaran == payload.alokasi_detail.id_batch_cross);
                            if (bCrs) {
                                bCrs.biomassa_est = Math.max(0, bCrs.biomassa_est - payload.alokasi_detail.cross_kg);
                            }
                        }

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
                            jenis_ikan: newTrx.Jenis_order || this.form.jenis_ikan || 'Ikan Segar',
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

        maskPhoneNumber(phone) {
            if (!phone || phone === '-' || phone === 'null') return '0812-****-5643';
            const clean = String(phone).trim();
            const digits = clean.replace(/\D/g, '');
            if (digits.length >= 8) {
                const prefix = digits.substring(0, 4);
                const suffix = digits.substring(digits.length - 4);
                return prefix + '-****-' + suffix;
            }
            return '0812-****-5643';
        },

        openInvoice(order) {
            this.selectedInvoice = {
                id: order.id,
                customer: order.customer,
                telepon: order.telepon,
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

function maskPhoneNumber(phone) {
    if (!phone || phone === '-' || phone === 'null') return '0812-****-5643';
    const clean = String(phone).trim();
    const digits = clean.replace(/\D/g, '');
    if (digits.length >= 8) {
        const prefix = digits.substring(0, 4);
        const suffix = digits.substring(digits.length - 4);
        return prefix + '-****-' + suffix;
    }
    return '0812-****-5643';
}

function formatLabelDate(dateStr) {
    if (!dateStr) dateStr = new Date();
    const d = new Date(dateStr);
    if (isNaN(d.getTime())) return dateStr;
    const months = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
    return `${months[d.getMonth()]} ${d.getDate()}, ${d.getFullYear()}`;
}

function printLabel(order) {
    if (!order) return;

    const user = {
        nama: @json(Auth::user()->nama ?? 'PT Aquafarm'),
        alamat: @json(Auth::user()->alamat ?? 'Jl. Raya Minapolitan Perikanan No. 88, Blok Agribisnis Terpadu, Jawa Barat 40123'),
        no_tlp: @json(Auth::user()->no_tlp ?? '+62 812-8899-0011')
    };

    if (!user.alamat || user.alamat.trim() === '') {
        user.alamat = 'Jl. Raya Minapolitan Perikanan No. 88, Blok Agribisnis Terpadu, Jawa Barat 40123';
    }
    if (!user.no_tlp || user.no_tlp.trim() === '') {
        user.no_tlp = '+62 812-8899-0011';
    }
    if (!user.nama || user.nama.trim() === '') {
        user.nama = 'PT Aquafarm';
    }

    const logoUrl = "{{ asset('images/logo sample di invoice.png') }}";
    const maskedPhone = maskPhoneNumber(order.telepon);
    const formattedDate = formatLabelDate(order.tanggal);

    const printableHtml = `<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Shipping Label - ${order.id || 'ORDER'}</title>
<style>
  @page {
    size: A5 landscape;
    margin: 6mm;
  }
  * {
    box-sizing: border-box;
    margin: 0;
    padding: 0;
    -webkit-print-color-adjust: exact !important;
    print-color-adjust: exact !important;
  }
  body {
    font-family: Arial, "Helvetica Neue", Helvetica, sans-serif;
    color: #111;
    background-color: #f3f4f6;
    padding: 24px;
    display: flex;
    justify-content: center;
    align-items: center;
    min-height: 100vh;
  }
  .label-card {
    width: 720px;
    max-width: 100%;
    background: #ffffff;
    border: 2px solid #000000;
    position: relative;
    box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1);
  }
  .grid-container {
    display: table;
    width: 100%;
    table-layout: fixed;
  }
  .column-left, .column-right {
    display: table-cell;
    vertical-align: top;
    padding: 16px 20px;
    width: 50%;
  }
  .column-left {
    border-right: 2px solid #000000;
  }
  .column-right {
    position: relative;
  }
  .header-tag {
    font-size: 15px;
    font-weight: 800;
    color: #000000;
    margin-bottom: 8px;
    letter-spacing: -0.2px;
  }
  .address-table {
    width: 100%;
    border-collapse: collapse;
    font-size: 12px;
    line-height: 1.45;
  }
  .address-table td {
    padding: 1.5px 0;
    vertical-align: top;
  }
  .address-table .label-cell {
    width: 62px;
    font-weight: 600;
    color: #111;
  }
  .address-table .colon-cell {
    width: 12px;
    text-align: center;
    font-weight: 600;
  }
  .address-table .value-cell {
    color: #222;
    font-weight: 500;
  }
  .address-table .value-cell.bold {
    font-weight: 700;
    color: #000;
  }
  .return-address-block {
    margin-top: 26px;
    font-size: 11px;
    line-height: 1.4;
  }
  .return-address-title {
    font-weight: 700;
    margin-bottom: 2px;
    color: #333;
  }
  .return-address-text {
    color: #555;
  }
  .additional-info-title {
    font-size: 13.5px;
    font-weight: 800;
    color: #000;
    margin-top: 14px;
    margin-bottom: 6px;
  }
  .additional-info-table {
    width: 100%;
    border-collapse: collapse;
    font-size: 11.5px;
    line-height: 1.4;
  }
  .additional-info-table td {
    padding: 1.5px 0;
    vertical-align: top;
  }
  .additional-info-table .lbl {
    width: 125px;
    font-weight: 600;
    color: #333;
  }
  .additional-info-table .sep {
    width: 10px;
  }
  .additional-info-table .val {
    color: #222;
  }
  .corner-logo {
    position: absolute;
    bottom: 8px;
    right: 12px;
    width: 54px;
    height: 54px;
    object-fit: contain;
    opacity: 0.95;
    background: transparent;
  }
  .bottom-row {
    display: table;
    width: 100%;
    table-layout: fixed;
    border-top: 2px solid #000000;
  }
  .handle-care-text {
    display: table-cell;
    vertical-align: middle;
    text-align: center;
    font-size: 13.5px;
    font-weight: 800;
    letter-spacing: 4px;
    text-transform: uppercase;
    color: #555;
    padding: 10px 14px;
    border-right: 2px solid #000000;
  }
  .date-text {
    display: table-cell;
    vertical-align: middle;
    width: 180px;
    text-align: center;
    font-size: 11.5px;
    font-weight: 700;
    color: #111;
    padding: 10px 12px;
    white-space: nowrap;
  }
  @media print {
    body {
      background-color: #fff !important;
      padding: 0 !important;
      min-height: auto !important;
    }
    .label-card {
      box-shadow: none !important;
      width: 100% !important;
      border: 2px solid #000000 !important;
      page-break-inside: avoid;
    }
  }
</style>
</head>
<body>
  <div class="label-card">
    <div class="grid-container">
      <!-- SENDER / FROM (KIRI) -->
      <div class="column-left">
        <div class="header-tag">From :</div>
        <table class="address-table">
          <tr>
            <td class="label-cell">Name</td>
            <td class="colon-cell">:</td>
            <td class="value-cell bold">PT Aquafarm</td>
          </tr>
          <tr>
            <td class="label-cell">Address</td>
            <td class="colon-cell">:</td>
            <td class="value-cell">${user.alamat}</td>
          </tr>
          <tr>
            <td class="label-cell">Phone</td>
            <td class="colon-cell">:</td>
            <td class="value-cell">${user.no_tlp}</td>
          </tr>
        </table>

        <div class="return-address-block">
          <div class="return-address-title">Return Address:</div>
          <div class="return-address-text">
           ${user.alamat}
          </div>
        </div>
      </div>

      <!-- RECIPIENT / TO (KANAN) -->
      <div class="column-right">
        <div class="header-tag">To :</div>
        <table class="address-table">
          <tr>
            <td class="label-cell">Name</td>
            <td class="colon-cell">:</td>
            <td class="value-cell bold">${order.customer || 'Pelanggan / Mitra'}</td>
          </tr>
          <tr>
            <td class="label-cell">Address</td>
            <td class="colon-cell">:</td>
            <td class="value-cell">${order.alamat || '-'}</td>
          </tr>
          <tr>
            <td class="label-cell">Phone</td>
            <td class="colon-cell">:</td>
            <td class="value-cell bold" style="letter-spacing: 0.5px;">${maskedPhone}</td>
          </tr>
        </table>

        <div class="additional-info-title">Additional information:</div>
        <table class="additional-info-table">
          <tr>
            <td class="lbl">Order ID / Resi</td>
            <td class="sep">:</td>
            <td class="val" style="font-weight: 700; color: #051b44;">${order.id || '-'}</td>
          </tr>
          <tr>
            <td class="lbl">Commodity</td>
            <td class="sep">:</td>
            <td class="val">${order.jenis_ikan || 'Ikan Konsumsi'}${order.batch_code ? ' (' + order.batch_code + ')' : ''}</td>
          </tr>
          <tr>
            <td class="lbl">Net Volume</td>
            <td class="sep">:</td>
            <td class="val" style="font-weight: 700;">${order.volume || '-'}</td>
          </tr>
        </table>

        <!-- Logo Aquafarm di Ujung Kanan Bawah Sesuai Permintaan -->
        <img src="${logoUrl}" alt="Logo Aquafarm" class="corner-logo">
      </div>
    </div>

    <!-- BOTTOM ROW: PLEASE HANDLE WITH CARE + DATE -->
    <div class="bottom-row">
      <div class="handle-care-text">PLEASE HANDLE WITH CARE</div>
      <div class="date-text">Date: ${formattedDate}</div>
    </div>
  </div>
</body>
</html>`;

    const printWindow = window.open('', '_blank');
    if (!printWindow) return;

    printWindow.document.write(printableHtml);
    printWindow.document.close();
    printWindow.focus();
    setTimeout(() => {
        printWindow.print();
    }, 450);
}
</script>
@endpush

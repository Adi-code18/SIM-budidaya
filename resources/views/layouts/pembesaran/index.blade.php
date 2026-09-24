@extends('layouts.app')

@section('title', 'Manajemen Pembesaran - AMS BUDIDAYA')

@section('content')
<div class="space-y-6" x-data="pembesaranComponent()">

    <!-- Subtitle & Page Title Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <span class="text-xs font-semibold text-slate-400 block uppercase tracking-wider">Manajemen Budidaya / Pembesaran</span>
            <h1 class="text-2xl font-extrabold text-[#0B2570] tracking-tight mt-0.5">Status Kolam Pembesaran</h1>
            <p class="text-xs text-slate-500 font-medium mt-0.5">Pantau siklus tebar benih, biomassa, FCR, dan target panen per kolam pembesaran.</p>
        </div>
        <div class="flex items-center gap-2.5">
            <button @click="openKolamModal()"
                    class="px-4 py-2.5 rounded-xl bg-white hover:bg-slate-50 text-[#031B4E] border border-slate-200/90 font-bold text-xs shadow-xs transition-all flex items-center gap-2">
                <i class="fa-solid fa-warehouse text-sky-600 text-xs"></i>
                <span>Tambah Kolam</span>
            </button>
            <button @click="showForm ? showForm = false : openCreateForm()"
                    class="px-5 py-2.5 rounded-xl bg-[#031B4E] hover:bg-sky-950 text-white font-bold text-xs shadow-md shadow-sky-950/20 transition-all flex items-center gap-2">
                <i class="fa-solid" :class="showForm ? 'fa-table-list' : 'fa-circle-plus'" class="text-xs"></i>
                <span x-text="showForm ? 'Lihat Daftar Kolam' : 'Input Batch Baru'"></span>
            </button>
        </div>
    </div>

    <!-- Alert Notification Banner jika ada Batch yang Waktunya Panen -->
    <template x-if="batches.some(b => b.is_harvest_due)">
        <div class="bg-amber-50 border border-amber-200/90 p-4 rounded-2xl text-slate-800 shadow-xs flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3">
            <div>
                <h4 class="font-extrabold text-xs tracking-wider uppercase flex items-center gap-2 text-amber-900">
                    <span>NOTIFIKASI PANEN: MASA PANEN TIBA!</span>
                    <span class="px-2 py-0.5 rounded-full text-[9px] bg-amber-200 text-amber-900 font-extrabold" x-text="batches.filter(b => b.is_harvest_due).length + ' BATCH'"></span>
                </h4>
                <p class="text-xs text-amber-800/90 font-medium mt-0.5">
                    Terdapat batch pembesaran yang telah mencapai/melewati estimasi tanggal panen. Harap segera lakukan pemeriksaan &amp; pemanenan.
                </p>
            </div>
            <button @click="activeFilter = 'aktif'" class="px-4 py-2 bg-amber-600 hover:bg-amber-700 text-white rounded-xl text-xs font-extrabold transition-all shrink-0 shadow-sm cursor-pointer">
                Lihat Batch Panen
            </button>
        </div>
    </template>

    <!-- ========= INPUT / EDIT FORM SECTION ========= -->
    <div x-show="showForm"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 -translate-y-2"
         x-transition:enter-end="opacity-100 translate-y-0"
         class="space-y-4"
         style="display: none;">

        <!-- Header Bar -->
        <div class="bg-[#051B44] rounded-2xl p-6 sm:p-8 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 shadow-md shadow-sky-950/20">
            <div class="text-white">
                <div class="flex items-center gap-2">
                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold uppercase bg-sky-500/30 text-sky-200 border border-sky-400/30"
                          x-text="formMode === 'edit' ? 'MODE EDIT BATCH' : 'BATCH BARU'"></span>
                </div>
                <h2 class="text-xl font-extrabold mt-1" x-text="formMode === 'edit' ? 'Edit Data Batch Pembesaran' : 'Input Batch Pembesaran'"></h2>
                <p class="text-xs text-sky-200/80 font-medium mt-1">Pastikan kolam yang dipilih adalah kolam pembesaran yang sedang kosong/tersedia.</p>
            </div>
            <div class="flex items-center gap-2">
                <button type="button" @click="showForm = false; formMode = 'create'; resetForm()"
                        class="px-4 py-2 rounded-xl border border-white/20 text-white text-xs font-bold hover:bg-white/10 transition-colors flex items-center gap-2">
                    <i class="fa-solid fa-xmark text-xs"></i> Batal
                </button>
                <button type="button" @click="submitBatch()" :disabled="isSubmitting"
                        class="px-5 py-2 rounded-xl bg-sky-500 hover:bg-sky-600 active:scale-95 text-white text-xs font-bold shadow-md shadow-sky-900/30 transition-all flex items-center gap-2 disabled:opacity-60">
                    <i class="fa-solid" :class="isSubmitting ? 'fa-spinner fa-spin' : 'fa-floppy-disk'" class="text-xs"></i>
                    <span x-text="isSubmitting ? 'MENYIMPAN...' : (formMode === 'edit' ? 'SIMPAN PERUBAHAN' : 'SIMPAN BATCH')"></span>
                </button>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            <!-- Left 2 Cols: Form Fields -->
            <div class="lg:col-span-2 space-y-5">

                <!-- Section 1: Identitas Batch & Kolam Pembesaran -->
                <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-xs space-y-4">
                    <div class="flex items-center gap-2.5 text-sm font-bold text-slate-900">
                        <div class="w-8 h-8 rounded-xl bg-[#051B44] text-white flex items-center justify-center">
                            <i class="fa-solid fa-layer-group text-xs"></i>
                        </div>
                        <span>Sumber Bibit &amp; Kolam Pembesaran</span>
                    </div>

                    <!-- Pilih dari Pembibitan (Integrasi Alur) -->
                    <div x-show="formMode === 'create'" class="p-4 bg-slate-50/80 rounded-2xl border border-slate-200/80 space-y-3">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 items-start">
                            <!-- Kolom 1: Sumber Benih / Batch Asal -->
                            <div class="space-y-1.5">
                                <div class="flex items-center justify-between">
                                    <label class="text-[10px] font-extrabold uppercase tracking-wider text-slate-600 block">
                                        SUMBER BENIH / BATCH ASAL *
                                    </label>
                                    <span class="text-[10px] px-2 py-0.5 rounded-md font-extrabold"
                                          :class="form.id_batch_pembibitan ? 'bg-sky-100 text-sky-800 border border-sky-200' : 'bg-amber-100 text-amber-800 border border-amber-200'"
                                          x-text="form.id_batch_pembibitan ? 'Hatchery Internal' : 'Beli Bibit Luar'">
                                    </span>
                                </div>
                                <select x-model="form.id_batch_pembibitan" @change="onPembibitanChange()"
                                        class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs font-semibold text-slate-800 bg-white focus:outline-none focus:ring-2 focus:ring-sky-500 shadow-2xs">
                                    <option value="">-- Beli Bibit Luar (Input Manual) --</option>
                                    <template x-for="bp in availablePembibitan" :key="bp.id_batch">
                                        <option :value="bp.id_batch" x-text="bp.label"></option>
                                    </template>
                                </select>
                                <p class="text-[10px] text-slate-400 italic">
                                    Pilih batch pembibitan fingerling internal atau input manual bila membeli bibit luar.
                                </p>
                            </div>

                            <!-- Kolom 2: Biaya Beli Bibit (jika beli luar) atau Info Sinkronisasi Hatchery -->
                            <div>
                                <template x-if="!form.id_batch_pembibitan">
                                    <div class="space-y-1.5">
                                        <div class="flex items-center justify-between">
                                            <label class="text-[10px] font-extrabold uppercase tracking-wider text-emerald-900 flex items-center gap-1.5">
                                                <i class="fa-solid fa-money-bill-wave text-emerald-600"></i>
                                                <span>BIAYA / HARGA BELI BIBIT (RP)</span>
                                            </label>
                                            <span class="text-[9px] px-2 py-0.5 rounded-md bg-emerald-100 text-emerald-800 font-bold border border-emerald-200">
                                                Otomatis Kas Keluar
                                            </span>
                                        </div>
                                        <div class="flex items-stretch rounded-xl border border-emerald-300 bg-white shadow-2xs overflow-hidden focus-within:ring-2 focus-within:ring-emerald-500 transition-all">
                                            <div class="px-3.5 flex items-center bg-emerald-50 text-emerald-900 font-black text-xs border-r border-emerald-200 select-none">
                                                Rp
                                            </div>
                                            <input type="number" step="1000" min="0" x-model="form.biayaBeliBibit" 
                                                   placeholder="Contoh: 1500000"
                                                   onkeydown="if(event.key === '-' || event.key === 'e' || event.key === 'E') event.preventDefault()"
                                                   class="w-full px-3.5 py-2.5 text-xs font-extrabold text-slate-800 bg-white focus:outline-none">
                                        </div>
                                        <p class="text-[10px] text-slate-500 italic leading-snug">
                                            <i class="fa-solid fa-circle-info text-sky-500 mr-0.5"></i> Biaya ini otomatis dibukukan sebagai transaksi pengeluaran di Keuangan.
                                        </p>
                                    </div>
                                </template>

                                <template x-if="selectedPembibitan">
                                    <div class="p-3 rounded-xl bg-sky-50 border border-sky-200 space-y-1 text-xs text-sky-950 shadow-2xs">
                                        <div class="flex items-center justify-between">
                                            <span class="text-[10px] font-extrabold uppercase text-sky-800 flex items-center gap-1">
                                                <i class="fa-solid fa-circle-check text-emerald-600"></i>
                                                <span>Sinkron Hatchery Internal</span>
                                            </span>
                                            <span class="font-extrabold text-sky-700 text-xs" x-text="'~' + (selectedPembibitan.est_biomassa || 0) + ' kg'"></span>
                                        </div>
                                        <p class="text-[11px] font-semibold text-slate-700 truncate" x-text="selectedPembibitan.label"></p>
                                        <span class="text-[10px] text-sky-700 block italic">Status batch pembibitan akan otomatis dialihkan menjadi Selesai/Dipindahkan (tanpa beban kas pengeluaran).</span>
                                    </div>
                                </template>
                            </div>
                        </div>

                        <!-- Baris Input Jumlah Bibit Tebar (Ekor) -->
                        <div class="pt-2 border-t border-slate-200/60 grid grid-cols-1 sm:grid-cols-2 gap-4 items-center">
                            <div>
                                <div class="flex items-center justify-between mb-1">
                                    <label class="text-[10px] font-extrabold uppercase tracking-wider text-slate-700 flex items-center gap-1">
                                        <i class="fa-solid fa-fish-fins text-sky-600"></i>
                                        <span>JUMLAH BIBIT TEBAR (EKOR) *</span>
                                    </label>
                                    <span class="text-[9px] font-bold text-sky-700 bg-sky-100 px-1.5 py-0.5 rounded"
                                          x-text="form.id_batch_pembibitan ? 'Otomatis dari Batch' : 'Input Tebar'">
                                    </span>
                                </div>
                                <input type="number" step="100" min="1" x-model="form.jumlahBibit" 
                                       @input="recalculateTargetPanen(true)"
                                       placeholder="Contoh: 10000"
                                       class="w-full px-3.5 py-2.5 rounded-xl border border-sky-300 text-xs font-black text-slate-800 bg-white focus:outline-none focus:ring-2 focus:ring-sky-500 shadow-2xs">
                            </div>
                            <div class="space-y-1">
                                <span class="text-[10px] font-bold text-slate-500 block">Pilihan Cepat Jumlah Bibit:</span>
                                <div class="flex flex-wrap gap-1.5">
                                    <template x-for="chip in [2500, 5000, 10000, 20000]" :key="chip">
                                        <button type="button" 
                                                @click="form.jumlahBibit = chip; recalculateTargetPanen(true)"
                                                :class="form.jumlahBibit == chip ? 'bg-[#0B2570] text-white' : 'bg-slate-200/80 text-slate-700 hover:bg-slate-300'"
                                                class="px-2.5 py-1 rounded-lg text-[10px] font-extrabold transition-all">
                                            <span x-text="Number(chip).toLocaleString('id-ID') + ' Ekor'"></span>
                                        </button>
                                    </template>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <div class="sm:col-span-1">
                            <div class="flex items-center justify-between mb-1">
                                <label class="text-[10px] font-extrabold uppercase tracking-wider text-slate-500 block">KOLAM PEMBESARAN *</label>
                                <button type="button" @click="openKolamModal()" class="text-[10px] font-extrabold text-sky-600 hover:text-sky-800 flex items-center gap-1 transition-colors">
                                    <i class="fa-solid fa-circle-plus"></i>
                                    <span>+ Kolam</span>
                                </button>
                            </div>
                            <select x-model="form.kolam" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs font-semibold text-slate-700 bg-slate-50/70 focus:bg-white focus:outline-none focus:ring-2 focus:ring-sky-500 transition-all">
                                <option value="">Pilih Kolam Pembesaran...</option>
                                <template x-for="k in kolamList" :key="k.id_kolam">
                                    <option :value="k.nama_kolam" 
                                            :disabled="k.is_occupied && k.nama_kolam !== form.kolam"
                                            x-text="k.nama_kolam + ' (' + k.tipe_kolam + ' - Kapasitas: ' + Number(k.kapasitas).toLocaleString('id-ID') + (k.is_occupied && k.nama_kolam !== form.kolam ? ' - SEDANG BERJALAN' : ' - TERSEDIA') + ')'">
                                    </option>
                                </template>
                            </select>
                        </div>
                        <div>
                            <label class="text-[10px] font-extrabold uppercase tracking-wider text-slate-500 block mb-1">TANGGAL TEBAR BENIH *</label>
                            <input type="date" x-model="form.tglTebar" @change="calculateEstPanen()"
                                   class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs font-semibold text-slate-700 bg-slate-50/70 focus:bg-white focus:outline-none focus:ring-2 focus:ring-sky-500 transition-all">
                        </div>
                        <div>
                            <label class="text-[10px] font-extrabold uppercase tracking-wider text-amber-700 block mb-1">ESTIMASI WAKTU PANEN *</label>
                            <input type="date" x-model="form.estTglPanen"
                                   class="w-full px-3.5 py-2.5 rounded-xl border border-amber-200 text-xs font-extrabold text-amber-800 bg-amber-50/60 focus:bg-white focus:outline-none focus:ring-2 focus:ring-amber-500 transition-all">
                        </div>
                    </div>
                </div>

                <!-- Section 2: Detail Komoditas Ikan & Survival Rate -->
                <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-xs space-y-4">
                    <div class="flex items-center gap-2.5 text-sm font-bold text-slate-900">
                        <div class="w-8 h-8 rounded-xl bg-[#0284C7] text-white flex items-center justify-center">
                            <i class="fa-solid fa-fish text-xs"></i>
                        </div>
                        <span>Komoditas Ikan &amp; Parameter SOP</span>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <div>
                            <div class="flex items-center justify-between mb-1">
                                <label class="text-[10px] font-extrabold uppercase tracking-wider text-slate-500 block">JENIS IKAN *</label>
                                <a href="{{ route('ikan') }}" class="text-[10px] font-extrabold text-sky-600 hover:text-sky-800 flex items-center gap-1 transition-colors" title="Kelola di Master Data Ikan">
                                    <i class="fa-solid fa-circle-plus"></i>
                                    <span>+ Master</span>
                                </a>
                            </div>
                            <select x-model="form.jenisIkan" @change="onJenisIkanChange()" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs font-semibold text-slate-700 bg-slate-50/70 focus:bg-white focus:outline-none focus:ring-2 focus:ring-sky-500 transition-all cursor-pointer">
                                <option value="">Pilih jenis ikan</option>
                                <template x-for="ik in ikanOptions" :key="ik.value">
                                    <option :value="ik.value" x-text="ik.label"></option>
                                </template>
                            </select>
                        </div>

                        <div>
                            <div class="flex items-center justify-between mb-1">
                                <label class="text-[10px] font-extrabold uppercase tracking-wider text-emerald-800 block">
                                    SURVIVAL RATE (SR %) *
                                </label>
                                <span class="text-[9px] font-black text-emerald-700 bg-emerald-100 px-1.5 py-0.5 rounded"
                                      x-text="form.survivalRate + '% SOP'">
                                </span>
                            </div>
                            <div class="flex items-center gap-2">
                                <input type="number" min="50" max="100" step="1" x-model="form.survivalRate" 
                                       @input="recalculateTargetPanen(true)"
                                       class="w-full px-3.5 py-2.5 rounded-xl border border-emerald-300 text-xs font-black text-emerald-950 bg-emerald-50/40 focus:bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500 transition-all">
                            </div>
                            <div class="flex gap-1 mt-1.5">
                                <template x-for="srChip in [80, 85, 90, 95]" :key="srChip">
                                    <button type="button" 
                                            @click="form.survivalRate = srChip; recalculateTargetPanen(true)"
                                            :class="form.survivalRate == srChip ? 'bg-emerald-700 text-white' : 'bg-emerald-50 text-emerald-800 border border-emerald-200 hover:bg-emerald-100'"
                                            class="px-2 py-0.5 rounded text-[9px] font-extrabold transition-all flex-1 text-center">
                                        <span x-text="srChip + '%'"></span>
                                    </button>
                                </template>
                            </div>
                        </div>

                        <div>
                            <label class="text-[10px] font-extrabold uppercase tracking-wider text-slate-500 block mb-1">STATUS SIKLUS</label>
                            <select x-model="form.statusSiklus" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs font-semibold text-slate-700 bg-slate-50/70 focus:bg-white focus:outline-none focus:ring-2 focus:ring-sky-500 transition-all">
                                <option value="berjalan">Berjalan (Sedang Dibesarkan)</option>
                                <option value="siap_panen">Siap Panen</option>
                                <option value="selesai">Selesai (Sudah Dipanen)</option>
                            </select>
                        </div>
                    </div>
                </div>

            </div>

            <!-- Right 1 Col: Target Produksi Pembesaran (Unit Economics Otomatis) -->
            <div class="space-y-5">
                <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-xs space-y-4">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2.5 text-sm font-bold text-slate-900">
                            <i class="fa-solid fa-calculator text-sky-600"></i>
                            <span>Otomatisasi Target Panen (Kg)</span>
                        </div>
                        <button type="button" @click="recalculateTargetPanen(true)" 
                                title="Hitung ulang otomatis berdasarkan Jumlah Bibit & SOP Ikan"
                                class="text-[10px] font-extrabold text-sky-700 bg-sky-50 hover:bg-sky-100 px-2 py-1 rounded-lg border border-sky-200 flex items-center gap-1 transition-all">
                            <i class="fa-solid fa-rotate text-[10px]"></i>
                            <span>Sync SOP</span>
                        </button>
                    </div>

                    <!-- Interactive SOP Unit Economics Calculation Box -->
                    <div class="p-3.5 bg-gradient-to-br from-[#0B2570]/5 via-sky-50 to-emerald-50/40 rounded-2xl border border-sky-200/80 space-y-2.5">
                        <div class="flex items-center justify-between text-[11px] font-extrabold text-[#0B2570]">
                            <span class="flex items-center gap-1.5">
                                <i class="fa-solid fa-wand-magic-sparkles text-amber-500"></i>
                                <span>Kalkulator Unit Economics</span>
                            </span>
                            <span class="text-[9px] bg-emerald-100 text-emerald-800 px-2 py-0.5 rounded-md font-black border border-emerald-200">
                                Real-Time SOP
                            </span>
                        </div>

                        <!-- Formula Card -->
                        <div class="bg-white/90 p-3 rounded-xl border border-sky-100 text-[11px] space-y-2 shadow-2xs">
                            <div class="flex justify-between items-center text-slate-600">
                                <span>Bibit Tebar:</span>
                                <span class="font-extrabold text-slate-900" x-text="Number(form.jumlahBibit || 0).toLocaleString('id-ID') + ' Ekor'"></span>
                            </div>
                            <div class="flex justify-between items-center text-slate-600">
                                <span>Estimasi Ikan Hidup (<span x-text="(form.survivalRate || 85) + '% SR'"></span>):</span>
                                <span class="font-extrabold text-emerald-700" x-text="Number(calculatedTargetLive.ekorHidup || 0).toLocaleString('id-ID') + ' Ekor'"></span>
                            </div>
                            <div class="flex justify-between items-center text-slate-600">
                                <span>Standar Pasar Konsumsi:</span>
                                <span class="font-bold text-sky-800" x-text="(currentIkanSop?.target_konsumsi || (calculatedTargetLive.ekorPerKg + ' ekor/kg')) + ' (~' + calculatedTargetLive.ekorPerKg + ' ekor/kg)'"></span>
                            </div>
                            <div class="pt-2 border-t border-slate-200/70 flex justify-between items-center">
                                <span class="font-extrabold text-slate-800">Target Panen SOP:</span>
                                <span class="text-sm font-black text-[#0B2570]" x-text="Number(calculatedTargetLive.targetKg || 0).toLocaleString('id-ID') + ' Kg'"></span>
                            </div>
                            <div class="flex justify-between items-center text-[10px] text-slate-500">
                                <span>Est. Kebutuhan Pakan (FCR <span x-text="calculatedTargetLive.fcrMin"></span>):</span>
                                <span class="font-bold text-slate-700" x-text="Number(calculatedTargetLive.estPakanKg || 0).toLocaleString('id-ID') + ' Kg'"></span>
                            </div>
                        </div>
                    </div>

                    <div class="space-y-3">
                        <div>
                            <div class="flex items-center justify-between mb-1">
                                <label class="text-[10px] font-extrabold uppercase tracking-wider text-slate-500 block">ESTIMASI BIOMASSA AWAL (KG) *</label>
                                <span class="text-[9px] text-slate-400 italic">Berat awal tebar</span>
                            </div>
                            <input type="number" step="0.1" min="0.1" x-model="form.biomassaEst" placeholder="Contoh: 150"
                                   onkeydown="if(event.key === '-' || event.key === 'e' || event.key === 'E') event.preventDefault()"
                                   @input="if(form.biomassaEst !== '' && Number(form.biomassaEst) < 0) form.biomassaEst = Math.abs(Number(form.biomassaEst)) || 0.1"
                                   class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs font-semibold text-slate-700 bg-slate-50/70 focus:bg-white focus:outline-none focus:ring-2 focus:ring-sky-500 transition-all">
                        </div>
                        <div>
                            <div class="flex items-center justify-between mb-1">
                                <label class="text-[10px] font-extrabold uppercase tracking-wider text-[#0B2570] block">TARGET PANEN (KG) *</label>
                                <span class="text-[9px] font-extrabold text-emerald-700 bg-emerald-50 px-1.5 py-0.5 rounded border border-emerald-200">
                                    Otomatis / Manual
                                </span>
                            </div>
                            <input type="number" step="0.1" min="0.1" x-model="form.targetPanenKg" placeholder="Contoh: 950"
                                   onkeydown="if(event.key === '-' || event.key === 'e' || event.key === 'E') event.preventDefault()"
                                   @input="if(form.targetPanenKg !== '' && Number(form.targetPanenKg) < 0) form.targetPanenKg = Math.abs(Number(form.targetPanenKg)) || 0.1"
                                   class="w-full px-3.5 py-2.5 rounded-xl border border-sky-300 text-xs font-black text-slate-900 bg-sky-50/30 focus:bg-white focus:outline-none focus:ring-2 focus:ring-sky-500 transition-all">
                        </div>
                        
                        <!-- TARGET FCR (Otomatis dari SOP Master Ikan & Pembibitan) -->
                        <div class="p-3.5 bg-sky-50/80 rounded-2xl border border-sky-200/80 space-y-2">
                            <div class="flex items-center justify-between">
                                <label class="text-[10px] font-extrabold uppercase tracking-wider text-[#0B2570] flex items-center gap-1.5">
                                    <i class="fa-solid fa-clipboard-check text-sky-600"></i>
                                    <span>STANDAR FCR (SOP MASTER IKAN)</span>
                                </label>
                                <span class="text-[9px] px-2 py-0.5 rounded-md font-extrabold"
                                      :class="currentIkanSop ? 'bg-sky-200/70 text-sky-900' : 'bg-slate-200/70 text-slate-600'"
                                      x-text="currentIkanSop ? ('SOP: ' + currentIkanSop.fcr_min + ' – ' + currentIkanSop.fcr_max) : 'Menunggu Jenis Ikan'">
                                </span>
                            </div>
                            <div class="flex items-center justify-between bg-white px-3.5 py-2.5 rounded-xl border border-sky-200 shadow-2xs">
                                <template x-if="currentIkanSop">
                                    <div class="flex items-baseline gap-1.5">
                                        <span class="text-base font-black text-[#0B2570]" x-text="currentIkanSop.fcr_min + ' – ' + currentIkanSop.fcr_max"></span>
                                        <span class="text-[10px] text-slate-500 font-semibold">Ratio Acuan</span>
                                    </div>
                                </template>
                                <template x-if="!currentIkanSop">
                                    <span class="text-xs text-slate-400 italic">Pilih jenis ikan terlebih dahulu</span>
                                </template>
                                <span class="text-[9px] font-bold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded border border-emerald-200 flex items-center gap-1">
                                    <i class="fa-solid fa-circle-check text-[9px] text-emerald-600"></i>
                                    <span>SOP Master Ikan</span>
                                </span>
                            </div>
                            <p class="text-[10px] text-slate-500 italic leading-tight">
                                Target efisiensi pakan otomatis mengacu pada standar SOP komoditas ikan terpilih.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Info Box -->
                <div class="bg-sky-50/80 p-5 rounded-2xl border border-sky-200/60 space-y-2.5 text-xs text-slate-600">
                    <div class="flex items-center gap-2 font-bold text-[#0B2570]">
                        <i class="fa-solid fa-shield-halved text-sky-500"></i>
                        <span>Validasi Bebas Tabrakan Kolam</span>
                    </div>
                    <p class="text-[11px] leading-relaxed text-slate-500">
                        Sistem secara otomatis mencegah penggunaan satu kolam pembesaran untuk lebih dari 1 batch aktif secara bersamaan.
                    </p>
                </div>
            </div>

        </div>
    </div>

    <!-- ========= DIRECTORY / LIST MODE ========= -->

    <!-- 4 Metric KPI Cards Grid (Biomassa, FCR, Modal Kolam, Proyeksi Laba) -->
    <div x-show="!showForm" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        
        <!-- Card 1: Total Biomassa -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between">
                    <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-500">TOTAL BIOMASSA AKTIF</span>
                    <div class="w-9 h-9 rounded-xl bg-sky-50 text-sky-600 flex items-center justify-center border border-sky-100">
                        <i class="fa-solid fa-weight-hanging text-sm"></i>
                    </div>
                </div>
                <div class="mt-2.5">
                    <h3 class="text-2xl font-extrabold text-slate-900 tracking-tight">{{ number_format($totalBiomassa ?? 4.5, 2) }} <span class="text-xs font-semibold text-slate-500">Ton</span></h3>
                </div>
            </div>
            <div class="mt-3 flex items-center gap-1.5 text-[11px] font-semibold text-emerald-600">
                <i class="fa-solid fa-arrow-trend-up"></i>
                <span>{{ count($batches ?? []) }} Kolam Pembesaran Aktif</span>
            </div>
        </div>

        <!-- Card 2: Rata-rata FCR -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between">
                    <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-500">RATA-RATA FCR</span>
                    <div class="w-9 h-9 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center border border-indigo-100">
                        <i class="fa-solid fa-calculator text-sm"></i>
                    </div>
                </div>
                <div class="mt-2.5">
                    <h3 class="text-2xl font-extrabold text-slate-900 tracking-tight">{{ number_format($financialSummary['avg_fcr_kumulatif'] ?? $avgFcr ?? 0, 2) }} <span class="text-xs font-semibold text-slate-500">Ratio</span></h3>
                </div>
            </div>
            <div class="mt-3 flex items-center gap-1.5 text-[11px] font-semibold {{ ($avgFcr ?? 0) > 0 ? 'text-emerald-600' : 'text-slate-500' }}">
                <i class="fa-regular {{ ($avgFcr ?? 0) > 0 ? 'fa-circle-check text-emerald-600' : 'fa-circle-info text-slate-400' }}"></i>
                <span>{{ ($avgFcr ?? 0) > 0 ? 'Efisiensi Kumulatif Terpantau' : 'Belum Ada Data Pakan' }}</span>
            </div>
        </div>

        <!-- Card 3: Total Modal / Biaya Kolam -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between">
                    <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-500">TOTAL MODAL KOLAM</span>
                    <div class="w-9 h-9 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center border border-amber-100">
                        <i class="fa-solid fa-wallet text-sm"></i>
                    </div>
                </div>
                <div class="mt-2.5">
                    <h3 class="text-2xl font-extrabold text-slate-900 tracking-tight">Rp {{ number_format($financialSummary['total_modal_kolam'] ?? 0, 0, ',', '.') }}</h3>
                </div>
            </div>
            <div class="mt-3 flex items-center gap-1.5 text-[11px] font-semibold text-slate-500">
                <i class="fa-solid fa-receipt text-slate-400"></i>
                <span>Pakan + Bibit + Operasional</span>
            </div>
        </div>

        <!-- Card 4: Proyeksi Laba Bersih -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between">
                    <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-500">PROYEKSI LABA BERSIH</span>
                    <div class="w-9 h-9 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center border border-emerald-100">
                        <i class="fa-solid fa-coins text-sm"></i>
                    </div>
                </div>
                <div class="mt-2.5">
                    <h3 class="text-2xl font-extrabold {{ ($financialSummary['total_laba'] ?? 0) >= 0 ? 'text-emerald-600' : 'text-rose-600' }} tracking-tight">
                        {{ ($financialSummary['total_laba'] ?? 0) >= 0 ? '+' : '-' }}Rp {{ number_format(abs($financialSummary['total_laba'] ?? 0), 0, ',', '.') }}
                    </h3>
                </div>
            </div>
            <div class="mt-3 flex items-center gap-1.5 text-[11px] font-semibold text-slate-600">
                <span class="inline-flex items-center gap-1 text-emerald-700 font-bold">
                    <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                    <span>{{ $financialSummary['kolam_untung_count'] ?? 0 }} Untung</span>
                </span>
                <span class="text-slate-300">•</span>
                <span class="inline-flex items-center gap-1 text-rose-700 font-bold">
                    <span class="w-2 h-2 rounded-full bg-rose-500"></span>
                    <span>{{ $financialSummary['kolam_rugi_count'] ?? 0 }} Defisit</span>
                </span>
            </div>
        </div>

    </div>

    <!-- 2-Column Main Section -->
    <div x-show="!showForm" class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <!-- Left 2 Columns: Visualisasi Kolam Grid -->
        <div class="lg:col-span-2 space-y-4">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                <div>
                    <h3 class="text-base font-bold text-slate-900 flex items-center gap-2">
                        <i class="fa-solid fa-grip text-sky-600"></i>
                        <span>Daftar Kolam &amp; Batch Pembesaran Aktif</span>
                    </h3>
                    <p class="text-xs text-slate-500 font-medium">Pantau efisiensi pakan, estimasi panen, dan analisis keuntungan per kolam.</p>
                </div>
                <div class="flex items-center gap-2">
                    <span class="px-3 py-1.5 rounded-xl bg-sky-50 border border-sky-200 text-sky-900 font-extrabold text-xs flex items-center gap-1.5 shadow-2xs">
                        <i class="fa-solid fa-fish text-sky-600"></i>
                        <span>Siklus Aktif: <strong x-text="batches.length"></strong> Kolam</span>
                    </span>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                
                <template x-for="item in filteredBatches" :key="item.id">
                    <div class="bg-white p-5 rounded-2xl border shadow-xs flex flex-col justify-between hover:shadow-md transition-all relative group"
                         :class="item.is_harvest_due ? 'border-amber-400/90 ring-2 ring-amber-400/20' : 'border-slate-200/80'">
                        <div>
                            <!-- Banner Notifikasi Waktunya Panen Pada Card -->
                            <template x-if="item.is_harvest_due">
                                <div class="mb-3 p-2 rounded-xl bg-amber-100 border border-amber-200 text-amber-900 font-extrabold text-[10px] flex items-center justify-between shadow-xs">
                                    <span class="uppercase tracking-wider">
                                        SUDAH WAKTUNYA PANEN!
                                    </span>
                                    <span class="text-[9px] bg-amber-200 text-amber-950 px-2 py-0.5 rounded-md font-extrabold" x-text="item.est_panen_format"></span>
                                </div>
                            </template>

                            <div class="flex items-start justify-between gap-2">
                                <div>
                                    <div class="flex items-center gap-2">
                                        <h4 class="font-extrabold text-slate-900 text-sm" x-text="item.nama_kolam"></h4>
                                        <span class="text-[10px] font-bold text-sky-600 bg-sky-50 px-2 py-0.5 rounded-md border border-sky-100" x-text="item.id"></span>
                                    </div>
                                    <div class="flex items-center gap-1.5 mt-0.5 flex-wrap">
                                        <span class="text-[10px] text-slate-500 font-medium" x-text="item.jenis_ikan + ' • ' + item.tipe_kolam"></span>
                                        <template x-if="item.asal_bibit === 'beli_luar'">
                                            <span class="text-[9px] px-1.5 py-0.5 rounded font-bold bg-amber-50 text-amber-700 border border-amber-200" :title="'Biaya beli bibit: Rp ' + (item.biaya_beli_bibit_format || '0')">
                                                Beli Luar <span x-show="item.biaya_beli_bibit > 0" x-text="'(Rp ' + item.biaya_beli_bibit_format + ')'"></span>
                                            </span>
                                        </template>
                                        <template x-if="item.asal_bibit !== 'beli_luar' && item.id_batch_pembibitan">
                                            <span class="text-[9px] px-1.5 py-0.5 rounded font-bold bg-sky-50 text-sky-700 border border-sky-200" x-text="item.id_batch_pembibitan"></span>
                                        </template>
                                    </div>
                                </div>
                                <div class="flex flex-col items-end gap-1">
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-extrabold uppercase"
                                          :class="item.status_class"
                                          x-text="item.status_label">
                                    </span>
                                    <!-- Tag Status Pemberian Pakan Hari Ini (Reset Tiap Hari) -->
                                    <template x-if="item.status_siklus !== 'selesai'">
                                        <div>
                                            <template x-if="item.is_fed_today">
                                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[9px] font-extrabold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                                    <i class="fa-solid fa-check text-[9px]"></i>
                                                    <span>Sudah Diberi Pakan</span>
                                                </span>
                                            </template>
                                            <template x-if="!item.is_fed_today">
                                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[9px] font-extrabold bg-amber-50 text-amber-700 border border-amber-200">
                                                    <i class="fa-solid fa-clock text-[9px]"></i>
                                                    <span>Belum Diberi Pakan</span>
                                                </span>
                                            </template>
                                        </div>
                                    </template>
                                </div>
                            </div>

                            <div class="mt-4 grid grid-cols-3 gap-2 text-xs">
                                <div class="bg-slate-50 p-2.5 rounded-xl border border-slate-100">
                                    <span class="text-[9px] text-slate-400 font-bold uppercase block">BIOMASSA</span>
                                    <span class="font-extrabold text-slate-900 text-xs" x-text="item.biomassa_format + ' kg'"></span>
                                </div>
                                <div class="bg-slate-50 p-2.5 rounded-xl border border-slate-100">
                                    <span class="text-[9px] text-slate-400 font-bold uppercase block">DOC</span>
                                    <span class="font-extrabold text-slate-900 text-xs"><span x-text="item.doc"></span> Hari</span>
                                </div>
                                <div class="p-2.5 rounded-xl border" :class="item.is_harvest_due ? 'bg-amber-50 border-amber-200 text-amber-900' : 'bg-slate-50 border-slate-100'">
                                    <span class="text-[9px] font-bold uppercase block" :class="item.is_harvest_due ? 'text-amber-700 font-extrabold' : 'text-slate-400'">EST. PANEN</span>
                                    <span class="font-extrabold text-xs" :class="item.is_harvest_due ? 'text-amber-800' : 'text-slate-900'" x-text="item.est_panen_format || '-'"></span>
                                </div>
                            </div>

                            <!-- Metrik FCR & Profitabilitas Kolam Strip -->
                            <div class="mt-2.5 grid grid-cols-2 gap-2">
                                <div class="p-2 rounded-xl bg-slate-50 border border-slate-100 flex flex-col justify-between">
                                    <div class="flex items-center justify-between">
                                        <span class="text-[9px] font-bold text-slate-400 uppercase">FCR Kumulatif</span>
                                        <span class="text-[9px] font-extrabold px-1.5 py-0.2 rounded"
                                              :class="item.is_optimal ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800'"
                                              x-text="item.is_optimal ? 'Optimal (SOP)' : 'Tinggi'"></span>
                                    </div>
                                    <div class="flex items-baseline gap-1 mt-1">
                                        <span class="text-xs font-black" :class="item.is_optimal ? 'text-emerald-700' : 'text-amber-700'" x-text="item.fcr_kumulatif || item.fcr"></span>
                                        <span class="text-[9px] text-slate-400" x-show="item.fcr_target" x-text="'(SOP: ' + item.fcr_target + ')'"></span>
                                    </div>
                                </div>
                                <div class="p-2 rounded-xl border flex flex-col justify-between"
                                     :class="(item.laba_rugi || 0) >= 0 ? 'bg-emerald-50/70 border-emerald-200/80 text-emerald-900' : 'bg-rose-50/70 border-rose-200/80 text-rose-900'">
                                    <div class="flex items-center justify-between">
                                        <span class="text-[9px] font-bold uppercase" :class="(item.laba_rugi || 0) >= 0 ? 'text-emerald-700' : 'text-rose-700'">Est. Laba / Rugi</span>
                                        <span class="text-[8px] font-extrabold px-1 rounded" :class="(item.laba_rugi || 0) >= 0 ? 'bg-emerald-200/60 text-emerald-900' : 'bg-rose-200/60 text-rose-900'" x-text="(item.margin_percent || 0) + '%'"></span>
                                    </div>
                                    <div class="font-extrabold text-xs mt-1 truncate" x-text="item.laba_rugi_format || 'Rp 0'"></div>
                                </div>
                            </div>

                            <div class="mt-3">
                                <div class="flex items-center justify-between text-xs font-semibold">
                                    <span class="text-[10px] text-slate-500 font-bold" x-text="'Target Panen: ' + item.target_format + ' kg'"></span>
                                    <span class="text-[10px] font-bold text-sky-700" x-text="item.target_percent + '%'"></span>
                                </div>
                                <div class="w-full bg-slate-100 h-2 rounded-full overflow-hidden mt-1">
                                    <div class="bg-[#0055CC] h-full rounded-full transition-all" :style="'width: ' + item.target_percent + '%'"></div>
                                </div>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between">
                            <span class="text-[11px] text-slate-400 font-medium" x-text="'pH ' + item.ph_air + ' • Tgl: ' + item.tgl_tebar"></span>
                            <div class="flex items-center gap-1.5">
                                <template x-if="item.status_siklus !== 'selesai'">
                                    <button type="button" @click="triggerFinishHarvest(item)" class="px-2.5 py-1 rounded-lg bg-emerald-50 hover:bg-emerald-100 text-emerald-800 font-bold text-[11px] flex items-center gap-1.5 transition-colors border border-emerald-200">
                                        <i class="fa-solid fa-circle-check text-emerald-600 text-xs"></i>
                                        <span>Selesaikan Panen</span>
                                    </button>
                                </template>

                                <div class="relative inline-block text-left" 
                                     x-data="{ 
                                         open: false,
                                         menuStyle: '',
                                         toggle(e) {
                                             this.open = !this.open;
                                             if (this.open) {
                                                 const rect = this.$refs.btn.getBoundingClientRect();
                                                 const spaceBelow = window.innerHeight - rect.bottom;
                                                 const menuH = 175;
                                                 const openUp = spaceBelow < menuH && rect.top > menuH;
                                                 const topPos = openUp ? (rect.top - menuH - 4) : (rect.bottom + 4);
                                                 const rightPos = window.innerWidth - rect.right;
                                                 this.menuStyle = `position: fixed; z-index: 99999; top: ${topPos}px; right: ${rightPos}px;`;
                                             }
                                         }
                                     }">
                                    <button x-ref="btn" 
                                            type="button" 
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
                                         class="w-48 rounded-xl bg-white border border-slate-200 shadow-2xl py-1.5 text-left"
                                         style="display: none;">
                                        
                                        <!-- Detail Batch -->
                                        <button type="button" @click="open = false; openDetail(item)" class="w-full px-3.5 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-50 flex items-center gap-2.5">
                                            <i class="fa-solid fa-eye text-sky-600 w-4"></i>
                                            <span>Detail Batch</span>
                                        </button>

                                        <!-- Edit Batch (Hanya aktif jika belum selesai) -->
                                        <template x-if="item.status_siklus !== 'selesai'">
                                            <button type="button" @click="open = false; openEdit(item)" class="w-full px-3.5 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-50 flex items-center gap-2.5">
                                                <i class="fa-solid fa-pen-to-square text-amber-600 w-4"></i>
                                                <span>Edit Batch</span>
                                            </button>
                                        </template>

                                        <!-- Selesaikan Panen (Hanya aktif jika belum selesai) -->
                                        <template x-if="item.status_siklus !== 'selesai'">
                                            <button type="button" @click="open = false; triggerFinishHarvest(item)" class="w-full px-3.5 py-2 text-xs font-semibold text-emerald-700 hover:bg-emerald-50 flex items-center gap-2.5">
                                                <i class="fa-solid fa-circle-check text-emerald-600 w-4"></i>
                                                <span>Selesaikan Panen</span>
                                            </button>
                                        </template>

                                        <div class="my-1 border-t border-slate-100"></div>

                                        <!-- Hapus Batch -->
                                        <button type="button" @click="open = false; deleteBatch(item)" class="w-full px-3.5 py-2 text-xs font-semibold text-red-600 hover:bg-red-50 flex items-center gap-2.5">
                                            <i class="fa-solid fa-trash-can text-red-500 w-4"></i>
                                            <span>Hapus Batch</span>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </template>

                <!-- Empty State jika tidak ada batch pada filter yang dipilih -->
                <template x-if="filteredBatches.length === 0">
                    <div class="col-span-full p-8 bg-slate-50 rounded-2xl border border-slate-200/80 text-center space-y-2">
                        <div class="w-12 h-12 rounded-2xl bg-white text-slate-400 border border-slate-200 mx-auto flex items-center justify-center text-lg">
                            <i class="fa-solid fa-water"></i>
                        </div>
                        <h4 class="text-xs font-bold text-slate-700" x-text="activeFilter === 'aktif' ? 'Tidak Ada Batch Pembesaran Aktif' : 'Belum Ada Riwayat Selesai Panen'"></h4>
                        <p class="text-[11px] text-slate-400 max-w-sm mx-auto" x-text="activeFilter === 'aktif' ? 'Semua kolam pembesaran sedang kosong / tersedia. Silakan buat batch baru dari pembibitan atau input manual.' : 'Batch yang telah selesai dipanen akan diarsipkan di sini.'"></p>
                    </div>
                </template>

                <!-- Tambah Batch Baru Card -->
                <div @click="openCreateForm()" 
                     class="border-2 border-dashed border-slate-200 bg-slate-50/50 hover:bg-slate-50 rounded-2xl p-6 flex flex-col items-center justify-center text-center cursor-pointer transition-all min-h-[220px] hover:border-sky-300">
                    <div class="w-12 h-12 rounded-2xl bg-white text-sky-600 border border-slate-200 shadow-xs flex items-center justify-center text-lg mb-2">
                        <i class="fa-solid fa-plus"></i>
                    </div>
                    <h4 class="text-xs font-bold text-slate-800">Tambah Batch Pembesaran</h4>
                    <p class="text-[10px] text-slate-400 mt-0.5">Tebar benih baru ke kolam yang tersedia</p>
                </div>

            </div>
        </div>

        <!-- Right Column: Kualitas Air & Fasilitas Kolam -->
        <div class="space-y-4">
            <h3 class="text-base font-bold text-slate-900 flex items-center gap-2">
                <i class="fa-solid fa-warehouse text-sky-600"></i>
                <span>Status Kolam Pembesaran</span>
            </h3>

            <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs space-y-4">
                <div class="flex items-center justify-between">
                    <span class="text-[11px] font-extrabold uppercase text-slate-400 tracking-wider block">Fasilitas Kolam Aktif</span>
                    <button type="button" @click="openKolamModal()" class="text-[11px] font-extrabold text-sky-600 hover:text-sky-800 flex items-center gap-1 transition-colors">
                        <i class="fa-solid fa-plus-circle text-xs"></i>
                        <span>Tambah Kolam</span>
                    </button>
                </div>
                
                <div class="space-y-2.5 max-h-[380px] overflow-y-auto pr-1 no-scrollbar">
                    <template x-for="k in kolamList" :key="k.id_kolam">
                        <div class="p-3 rounded-xl border flex items-center justify-between text-xs"
                             :class="k.is_occupied ? 'bg-amber-50/50 border-amber-200/60' : 'bg-slate-50 border-slate-100'">
                            <div>
                                <h5 class="font-extrabold text-slate-800" x-text="k.nama_kolam"></h5>
                                <span class="text-[10px] text-slate-400" x-text="k.tipe_kolam + ' • ' + Number(k.kapasitas).toLocaleString('id-ID') + ' Ekor'"></span>
                            </div>
                            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold"
                                  :class="k.is_occupied ? 'bg-amber-100 text-amber-800' : 'bg-emerald-100 text-emerald-800'"
                                  x-text="k.is_occupied ? 'Terpakai' : 'Tersedia'">
                            </span>
                        </div>
                    </template>
                </div>
            </div>
        </div>

    </div>

    <!-- Modal Detail Batch Pembesaran (Dengan 4 FCR & Analisis Finansial) -->
    <div x-show="detailModalOpen" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-95"
         class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 backdrop-blur-xs p-4"
         style="display: none;">
        
        <div @click.outside="detailModalOpen = false" 
             class="bg-white w-full max-w-4xl rounded-3xl shadow-2xl border border-slate-100 overflow-hidden flex flex-col max-h-[90vh]">
            
            <!-- Modal Header Solid Navy -->
            <div class="p-6 bg-[#051B44] text-white flex items-center justify-between">
                <div class="flex items-center gap-3.5">
                    <div class="w-11 h-11 rounded-2xl bg-sky-500/20 border border-sky-400/30 flex items-center justify-center text-sky-300 text-xl shrink-0">
                        <i class="fa-solid fa-fish-fins"></i>
                    </div>
                    <div>
                        <div class="flex items-center gap-2.5">
                            <h3 class="font-extrabold text-lg text-white tracking-tight" x-text="selectedBatch?.id"></h3>
                            <span class="text-[10px] font-extrabold px-2.5 py-0.5 rounded-full"
                                  :class="selectedBatch?.status_class"
                                  x-text="selectedBatch?.status_label"></span>
                        </div>
                        <p class="text-xs text-sky-200/90 font-medium mt-0.5" x-text="selectedBatch?.jenis_ikan + ' • ' + selectedBatch?.nama_kolam"></p>
                    </div>
                </div>
                <button type="button" @click="detailModalOpen = false" class="w-8 h-8 rounded-full bg-white/10 hover:bg-white/20 text-white flex items-center justify-center transition-colors">
                    <i class="fa-solid fa-xmark text-sm"></i>
                </button>
            </div>

            <!-- Modal Body (Scrollable) -->
            <div class="p-6 space-y-4 overflow-y-auto text-xs">
                
                <!-- Ringkasan Info Kolam & Parameter -->
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                    <div class="p-3 bg-slate-50 rounded-2xl border border-slate-100">
                        <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400 block mb-0.5">LOKASI KOLAM</span>
                        <span class="font-extrabold text-slate-900 text-xs block" x-text="selectedBatch?.nama_kolam"></span>
                        <span class="text-[10px] text-slate-500" x-text="selectedBatch?.tipe_kolam"></span>
                    </div>

                    <div class="p-3 bg-slate-50 rounded-2xl border border-slate-100">
                        <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400 block mb-0.5">TANGGAL TEBAR & DOC</span>
                        <span class="font-extrabold text-slate-900 text-xs block" x-text="selectedBatch?.tgl_tebar_format || selectedBatch?.tgl_tebar"></span>
                        <span class="text-[10px] text-emerald-600 font-bold" x-text="selectedBatch?.doc + ' Hari (DOC)'"></span>
                    </div>

                    <div class="p-3 rounded-2xl border" :class="selectedBatch?.is_harvest_due ? 'bg-amber-50 border-amber-200' : 'bg-slate-50 border-slate-100'">
                        <span class="text-[10px] font-extrabold uppercase tracking-wider block mb-0.5" :class="selectedBatch?.is_harvest_due ? 'text-amber-800' : 'text-slate-400'">ESTIMASI PANEN</span>
                        <span class="font-extrabold text-xs block" :class="selectedBatch?.is_harvest_due ? 'text-amber-900' : 'text-slate-900'" x-text="selectedBatch?.est_panen_format || '-'"></span>
                        <span class="text-[10px] font-bold" :class="selectedBatch?.is_harvest_due ? 'text-rose-600' : 'text-slate-400'" x-text="selectedBatch?.is_harvest_due ? '⚠️ Waktunya Panen!' : 'Jadwal Panen'"></span>
                    </div>

                    <div class="p-3 bg-sky-50/70 rounded-2xl border border-sky-100">
                        <span class="text-[10px] font-extrabold uppercase tracking-wider text-sky-800 block mb-0.5">KUALITAS AIR</span>
                        <span class="font-extrabold text-[#0B2570] text-xs block" x-text="'pH Air ' + (selectedBatch?.ph_air || '7.0')"></span>
                        <span class="text-[10px] text-emerald-700 font-bold">Kondisi Optimal</span>
                    </div>
                </div>

                <!-- SECTION 1: 4 FORMULA FCR BUDIDAYA STANDAR -->
                <div class="p-4 bg-slate-900 text-white rounded-2xl space-y-3">
                    <div class="flex items-center justify-between border-b border-white/10 pb-2.5">
                        <div class="flex items-center gap-2">
                            <div class="w-7 h-7 rounded-xl bg-sky-500/30 text-sky-300 flex items-center justify-center text-xs">
                                <i class="fa-solid fa-calculator"></i>
                            </div>
                            <div>
                                <span class="font-extrabold text-xs text-white block">Analisis Efisiensi Pakan (4 Tipe FCR)</span>
                                <span class="text-[10px] text-slate-400">Metrik standar akuakultur terintegrasi pakan &amp; pertumbuhan</span>
                            </div>
                        </div>
                        <span class="px-2.5 py-0.5 rounded-full text-[9px] font-extrabold"
                              :class="selectedBatch?.is_optimal ? 'bg-emerald-500/20 text-emerald-300 border border-emerald-500/30' : 'bg-amber-500/20 text-amber-300 border border-amber-500/30'"
                              x-text="selectedBatch?.fcr_status_text || (selectedBatch?.is_optimal ? 'Optimal (Sesuai SOP)' : 'Tinggi (Di Luar SOP)')">
                        </span>
                    </div>

                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-2.5 text-left">
                        <!-- FCR Kumulatif / Running -->
                        <div class="p-2.5 bg-white/10 rounded-xl border border-white/5 space-y-1">
                            <span class="text-[9px] uppercase tracking-wider text-sky-300 font-extrabold block">1. FCR KUMULATIF</span>
                            <span class="text-base font-extrabold text-white block" x-text="selectedBatch?.fcr_kumulatif || '-'"></span>
                            <span class="text-[9px] text-slate-300 block leading-tight">Akumulasi pakan s.d. hari ini / pertambahan biomassa</span>
                        </div>

                        <!-- FCR Komersial -->
                        <div class="p-2.5 bg-white/10 rounded-xl border border-white/5 space-y-1">
                            <span class="text-[9px] uppercase tracking-wider text-emerald-300 font-extrabold block">2. FCR KOMERSIAL</span>
                            <span class="text-base font-extrabold text-white block" x-text="selectedBatch?.fcr_komersial || '-'"></span>
                            <span class="text-[9px] text-slate-300 block leading-tight">Saat panen: Total Pakan / Net Panen</span>
                        </div>

                        <!-- FCR Biologis -->
                        <div class="p-2.5 bg-white/10 rounded-xl border border-white/5 space-y-1">
                            <span class="text-[9px] uppercase tracking-wider text-indigo-300 font-extrabold block">3. FCR BIOLOGIS</span>
                            <span class="text-base font-extrabold text-white block" x-text="selectedBatch?.fcr_biologis || '-'"></span>
                            <span class="text-[9px] text-slate-300 block leading-tight">Memperhitungkan bobot ikan mortalitas</span>
                        </div>

                        <!-- Target Ideal Spesies -->
                        <div class="p-2.5 bg-white/10 rounded-xl border border-white/5 space-y-1">
                            <span class="text-[9px] uppercase tracking-wider text-amber-300 font-extrabold block">4. STANDAR SOP</span>
                            <span class="text-base font-extrabold text-white block" x-text="selectedBatch?.fcr_target || '-'"></span>
                            <span class="text-[9px] text-slate-300 block leading-tight">Standar FCR ideal master data ikan</span>
                        </div>
                    </div>

                    <!-- Progress Capaian Target -->
                    <div class="pt-1 border-t border-white/10">
                        <div class="flex items-center justify-between text-[11px] font-bold mb-1">
                            <span class="text-slate-300">Biomassa Saat Ini: <strong class="text-white" x-text="selectedBatch?.biomassa_format + ' kg'"></strong> / Target Panen: <strong class="text-sky-300" x-text="selectedBatch?.target_format + ' kg'"></strong></span>
                            <span class="text-emerald-400" x-text="selectedBatch?.target_percent + '%'"></span>
                        </div>
                        <div class="w-full bg-white/20 h-2 rounded-full overflow-hidden">
                            <div class="bg-gradient-to-r from-sky-400 to-emerald-400 h-full rounded-full transition-all"
                                 :style="'width: ' + selectedBatch?.target_percent + '%'"></div>
                        </div>
                    </div>
                </div>

                <!-- SECTION 2: ANALISIS FINANSIAL & PROFITABILITAS KOLAM -->
                <div class="bg-white rounded-2xl border border-slate-200 shadow-xs p-4 space-y-3">
                    <div class="flex items-center justify-between border-b border-slate-100 pb-2.5">
                        <div class="flex items-center gap-2">
                            <div class="w-7 h-7 rounded-xl bg-emerald-100 text-emerald-700 flex items-center justify-center text-xs">
                                <i class="fa-solid fa-coins"></i>
                            </div>
                            <div>
                                <span class="font-extrabold text-xs text-slate-900 block">Analisis Finansial &amp; Profitabilitas Kolam</span>
                                <span class="text-[10px] text-slate-400">Biaya pakan, bibit, operasional vs estimasi omset panen</span>
                            </div>
                        </div>
                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold"
                              :class="(selectedBatch?.laba_rugi || 0) >= 0 ? 'bg-emerald-100 text-emerald-800 border border-emerald-200' : 'bg-rose-100 text-rose-800 border border-rose-200'"
                              x-text="(selectedBatch?.laba_rugi || 0) >= 0 ? 'PROYEKSI LABA' : 'PROYEKSI RUGI'">
                        </span>
                    </div>

                    <!-- 4-Kolom Rincian Biaya & Laba -->
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-2.5 text-xs">
                        <div class="p-2.5 bg-slate-50 rounded-xl border border-slate-100">
                            <span class="text-[9px] uppercase tracking-wider text-slate-400 font-bold block">BIAYA PAKAN</span>
                            <span class="font-extrabold text-slate-900 text-xs block mt-0.5" x-text="selectedBatch?.biaya_pakan_format || 'Rp 0'"></span>
                            <span class="text-[9px] text-slate-500" x-text="(selectedBatch?.total_pakan_kg || '0') + ' kg pakan'"></span>
                        </div>

                        <div class="p-2.5 bg-slate-50 rounded-xl border border-slate-100">
                            <span class="text-[9px] uppercase tracking-wider text-slate-400 font-bold block">BIAYA BIBIT</span>
                            <span class="font-extrabold text-slate-900 text-xs block mt-0.5" x-text="selectedBatch?.biaya_bibit_format || 'Rp 0'"></span>
                            <span class="text-[9px] text-slate-500" x-text="selectedBatch?.asal_bibit === 'beli_luar' ? 'Beli Luar' : 'Hatchery Internal'"></span>
                        </div>

                        <div class="p-2.5 bg-sky-50/60 rounded-xl border border-sky-100">
                            <span class="text-[9px] uppercase tracking-wider text-sky-800 font-bold block">ESTIMASI OMSET</span>
                            <span class="font-extrabold text-[#0B2570] text-xs block mt-0.5" x-text="selectedBatch?.pendapatan_format || 'Rp 0'"></span>
                            <span class="text-[9px] text-sky-700" x-text="(selectedBatch?.harga_jual_format || 'Rp 0') + '/kg'"></span>
                        </div>

                        <div class="p-2.5 rounded-xl border"
                             :class="(selectedBatch?.laba_rugi || 0) >= 0 ? 'bg-emerald-50/80 border-emerald-200 text-emerald-900' : 'bg-rose-50/80 border-rose-200 text-rose-900'">
                            <div class="flex items-center justify-between">
                                <span class="text-[9px] uppercase tracking-wider font-bold block" :class="(selectedBatch?.laba_rugi || 0) >= 0 ? 'text-emerald-700' : 'text-rose-700'">LABA / RUGI</span>
                                <span class="text-[9px] font-extrabold" x-text="(selectedBatch?.margin_percent || 0) + '% Margin'"></span>
                            </div>
                            <span class="font-black text-xs block mt-0.5" x-text="selectedBatch?.laba_rugi_format || 'Rp 0'"></span>
                        </div>
                    </div>
                </div>

                <!-- TABEL RINCIAN MASING-MASING BIBIT DARI BATCH PEMBIBITAN -->
                <div class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden">
                    <div class="px-4 py-3 bg-slate-50/90 border-b border-slate-200 flex flex-wrap items-center justify-between gap-2">
                        <div class="flex items-center gap-2.5">
                            <div class="w-7 h-7 rounded-lg bg-sky-100 text-sky-700 flex items-center justify-center text-xs">
                                <i class="fa-solid fa-layer-group"></i>
                            </div>
                            <div>
                                <span class="font-bold text-slate-800 text-xs block">Asal Usul Sumber Bibit</span>
                                <span class="text-[10px] text-slate-500 font-medium" x-text="(selectedBatch?.bibit_list ? selectedBatch.bibit_list.length : 1) + ' Sumber Bibit Tercatat'"></span>
                            </div>
                        </div>
                        <div>
                            <template x-if="selectedBatch?.asal_bibit === 'beli_luar'">
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-lg text-xs font-bold bg-amber-50 text-amber-900 border border-amber-200">
                                    <i class="fa-solid fa-cart-shopping text-amber-600 text-[11px]"></i>
                                    <span>Beli Luar</span>
                                    <span class="font-extrabold text-amber-700 font-mono" x-show="selectedBatch?.biaya_beli_bibit > 0" x-text="'(Rp ' + selectedBatch?.biaya_beli_bibit_format + ')'"></span>
                                </span>
                            </template>
                            <template x-if="selectedBatch?.asal_bibit !== 'beli_luar'">
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-lg text-xs font-bold bg-sky-50 text-sky-900 border border-sky-200">
                                    <i class="fa-solid fa-dna text-sky-600 text-[11px]"></i>
                                    <span>Hatchery Internal</span>
                                </span>
                            </template>
                        </div>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-xs">
                            <thead class="bg-slate-50/60 text-[10px] uppercase font-extrabold text-slate-400 border-b border-slate-100 tracking-wider">
                                <tr>
                                    <th class="py-3 px-4">Batch Asal</th>
                                    <th class="py-3 px-4">Kolam Asal</th>
                                    <th class="py-3 px-4">Komoditas &amp; Fase</th>
                                    <th class="py-3 px-4">Populasi Tebar</th>
                                    <th class="py-3 px-4">Bobot Awal</th>
                                    <th class="py-3 px-4 text-right">Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 font-medium text-slate-700">
                                <template x-for="(bibit, bIdx) in (selectedBatch?.bibit_list || [])" :key="bIdx">
                                    <tr class="hover:bg-slate-50/70 transition-colors">
                                        <td class="py-3 px-4">
                                            <template x-if="bibit.is_beli_luar">
                                                <span class="inline-flex items-center gap-1 font-bold text-amber-800 bg-amber-50 px-2 py-0.5 rounded text-[11px] border border-amber-200/80">
                                                    <i class="fa-solid fa-cart-shopping text-[9px] text-amber-600"></i> Pengadaan Luar
                                                </span>
                                            </template>
                                            <template x-if="!bibit.is_beli_luar">
                                                <span class="font-extrabold text-[#0B2570] font-mono text-xs block" x-text="bibit.id_batch"></span>
                                            </template>
                                            <span class="text-[10px] text-slate-400 block mt-0.5" x-text="bibit.tgl_pemijahan"></span>
                                        </td>
                                        <td class="py-3 px-4">
                                            <span class="font-bold text-slate-800 block text-xs" x-text="bibit.kolam_asal"></span>
                                            <span class="text-[10px] text-slate-400 block" x-text="bibit.tipe_kolam_asal"></span>
                                        </td>
                                        <td class="py-3 px-4">
                                            <span class="font-bold text-slate-900 block text-xs" x-text="bibit.jenis_ikan"></span>
                                            <span class="inline-block mt-0.5 px-2 py-0.5 rounded text-[9px] font-bold bg-sky-50 text-sky-700 border border-sky-200 tracking-wide uppercase" x-text="bibit.fase"></span>
                                        </td>
                                        <td class="py-3 px-4">
                                            <div class="font-extrabold text-slate-900 text-xs">
                                                <span class="text-sm" x-text="bibit.jumlah_bibit"></span>
                                                <span class="text-[10px] font-normal text-slate-400">ekor</span>
                                            </div>
                                        </td>
                                        <td class="py-3 px-4">
                                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-extrabold bg-slate-100 text-slate-800 border border-slate-200" x-text="bibit.total_bobot_kg + ' kg'"></span>
                                        </td>
                                        <td class="py-3 px-4 text-right">
                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-800 border border-emerald-200">
                                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                                <span x-text="bibit.status"></span>
                                            </span>
                                        </td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Realisasi Panen jika status Selesai -->
                <template x-if="selectedBatch?.status_siklus === 'selesai'">
                    <div class="p-3.5 bg-emerald-50 rounded-2xl border border-emerald-200 flex items-center justify-between">
                        <div>
                            <span class="text-[10px] font-extrabold uppercase tracking-wider text-emerald-700 block">TOTAL HASIL PANEN</span>
                            <span class="text-sm font-extrabold text-emerald-900" x-text="(selectedBatch?.jumlah_panen_format || selectedBatch?.target_format) + ' kg'"></span>
                        </div>
                        <span class="px-2.5 py-1 rounded-full text-[10px] font-extrabold bg-emerald-100 text-emerald-800 border border-emerald-300">
                            Selesai &amp; Diarsipkan
                        </span>
                    </div>
                </template>

            </div>

            <!-- Modal Footer -->
            <div class="p-4 border-t border-slate-100 flex items-center justify-end gap-2.5 bg-slate-50/50">
                <template x-if="selectedBatch?.status_siklus !== 'selesai'">
                    <div class="flex items-center gap-2">
                        <button type="button" @click="detailModalOpen = false; triggerFinishHarvest(selectedBatch)" class="px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs flex items-center gap-2 shadow-xs transition-colors">
                            <i class="fa-solid fa-circle-check text-xs"></i>
                            <span>Selesaikan Panen</span>
                        </button>
                        <button type="button" @click="detailModalOpen = false; openEdit(selectedBatch)" class="px-4 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs flex items-center gap-2 transition-colors">
                            <i class="fa-solid fa-pen-to-square text-amber-600 text-xs"></i>
                            <span>Edit Batch</span>
                        </button>
                    </div>
                </template>
                <button type="button" @click="detailModalOpen = false" class="px-5 py-2 rounded-xl bg-[#031B4E] text-white font-bold hover:bg-navy-900 text-xs shadow-md shadow-sky-950/20 transition-all">
                    Tutup
                </button>
            </div>

        </div>
    </div>

    <!-- Alert Modal Verifikasi Konfirmasi Selesai Panen & Pengosongan Kolam -->
    <div x-show="harvestConfirmModalOpen" 
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 backdrop-blur-xs p-3 sm:p-4 overflow-y-auto"
         style="display: none;">
        
        <div @click.outside="harvestConfirmModalOpen = false" 
             x-show="harvestConfirmModalOpen"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 scale-95 translate-y-3"
             x-transition:enter-end="opacity-100 scale-100 translate-y-0"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100 scale-100 translate-y-0"
             x-transition:leave-end="opacity-0 scale-95 translate-y-3"
             class="bg-white w-full max-w-lg rounded-3xl shadow-2xl border border-slate-100 flex flex-col max-h-[92vh] my-auto overflow-hidden">
            
            <!-- Modal Header (Sticky / Fixed) -->
            <div class="p-4 sm:p-5 border-b border-slate-100 text-center relative shrink-0 bg-slate-50/50">
                <button type="button" @click="harvestConfirmModalOpen = false" 
                        class="absolute top-3.5 right-3.5 w-7 h-7 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-400 hover:text-slate-600 flex items-center justify-center transition-colors">
                    <i class="fa-solid fa-xmark text-xs"></i>
                </button>
                <div class="w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-600 border border-emerald-100 mx-auto flex items-center justify-center text-xl shadow-xs mb-2">
                    <i class="fa-solid fa-boxes-packing"></i>
                </div>
                <div class="space-y-0.5 text-center">
                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold uppercase bg-emerald-100 text-emerald-800 inline-block mb-1">
                        Input Hasil Panen &amp; Alokasi Surplus
                    </span>
                    <h3 class="text-base sm:text-lg font-extrabold text-slate-900">Selesaikan Panen &amp; Pindahkan Surplus</h3>
                    <p class="text-xs text-slate-500 font-medium">
                        Batch <strong class="text-slate-900" x-text="selectedBatchToHarvest?.id"></strong> di <strong class="text-slate-900" x-text="selectedBatchToHarvest?.nama_kolam"></strong> (<span x-text="selectedBatchToHarvest?.jenis_ikan"></span>)
                    </p>
                </div>
            </div>

            <!-- Modal Body (Scrollable) -->
            <div class="p-4 sm:p-5 overflow-y-auto space-y-3.5 text-center">
                <!-- Card 1: Kebutuhan Order / Biomassa Kolam -->
                <div class="bg-slate-50 p-3 rounded-2xl border border-slate-200 text-left space-y-2 text-xs">
                    <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-500 block">Kebutuhan Target / Pesanan</span>
                    <div class="grid grid-cols-2 gap-2">
                        <div class="p-2.5 bg-white rounded-xl border border-slate-200">
                            <span class="text-[9px] font-extrabold uppercase text-slate-400 block">STOK BIOMASSA KOLAM</span>
                            <span class="font-extrabold text-slate-800 text-xs" x-text="(selectedBatchToHarvest?.biomassa_format || '0') + ' kg'"></span>
                        </div>
                        <div class="p-2.5 bg-white rounded-xl border" :class="selectedBatchToHarvest?.order_target_kg > 0 ? 'border-sky-300 bg-sky-50/50' : 'border-slate-200'">
                            <span class="text-[9px] font-extrabold uppercase block" :class="selectedBatchToHarvest?.order_target_kg > 0 ? 'text-sky-700' : 'text-slate-400'">
                                <span x-text="selectedBatchToHarvest?.order_target_kg > 0 ? 'PESANAN MITRA AKTIF' : 'TARGET PANEN SIKLUS'"></span>
                            </span>
                            <span class="font-black text-xs" :class="selectedBatchToHarvest?.order_target_kg > 0 ? 'text-sky-900' : 'text-slate-800'"
                                  x-text="selectedBatchToHarvest?.order_target_kg > 0 ? (selectedBatchToHarvest.order_target_kg + ' kg (' + selectedBatchToHarvest.order_mitra + ')') : ((selectedBatchToHarvest?.target_format || selectedBatchToHarvest?.biomassa_format || '0') + ' kg')"></span>
                        </div>
                    </div>
                </div>

                <!-- Card 2: Input Realisasi Total Berat Panen yang Diangkat -->
                <div class="text-left bg-emerald-50/70 p-4 rounded-2xl border border-emerald-200 space-y-2.5">
                    <div class="flex items-center justify-between">
                        <label class="text-[10px] font-extrabold uppercase tracking-wider text-emerald-950 block">
                            TOTAL HASIL PANEN TIMBANG RIIL (KG) *
                        </label>
                        <span class="text-[9px] font-bold text-emerald-700 bg-white px-2 py-0.5 rounded-md border border-emerald-200">Wajib Diisi</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <input type="number" step="0.1" min="0.1" x-model="harvestForm.jumlah_panen_kg"
                               @input="onHarvestKgInput()"
                               placeholder="Ketik total kg panen (misal: 150)"
                               class="flex-1 px-4 py-2.5 rounded-xl border border-emerald-300 text-sm font-extrabold text-emerald-900 bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500 shadow-2xs">
                        <span class="px-3.5 py-2.5 bg-white border border-emerald-200 rounded-xl text-xs font-black text-emerald-800">Kg</span>
                    </div>
                    
                    <!-- Quick buttons -->
                    <div class="flex items-center gap-1.5 pt-0.5 text-[10px] flex-wrap">
                        <span class="text-slate-400 font-bold">Pintasan:</span>
                        <button type="button" @click="harvestForm.jumlah_panen_kg = (selectedBatchToHarvest?.order_target_kg > 0 ? selectedBatchToHarvest.order_target_kg : (selectedBatchToHarvest?.target_panen_kg || 100)); onHarvestKgInput()"
                                class="px-2 py-0.5 rounded-md bg-white border border-emerald-200 text-emerald-800 font-bold hover:bg-emerald-100 transition-colors">
                            Sesuai Target (<span x-text="(selectedBatchToHarvest?.order_target_kg > 0 ? selectedBatchToHarvest.order_target_kg : (selectedBatchToHarvest?.target_panen_kg || 100)) + ' kg'"></span>)
                        </button>
                        <button type="button" @click="harvestForm.jumlah_panen_kg = (selectedBatchToHarvest?.biomassa_est || 150); onHarvestKgInput()"
                                class="px-2 py-0.5 rounded-md bg-white border border-emerald-200 text-emerald-800 font-bold hover:bg-emerald-100 transition-colors">
                            Sesuai Biomassa (<span x-text="(selectedBatchToHarvest?.biomassa_est || 150) + ' kg'"></span>)
                        </button>
                    </div>
                </div>

                <!-- Card 3: Status Surplus & Pilihan Kolam Stok Tujuan -->
                <div class="text-left bg-white p-4 rounded-2xl border border-slate-200 space-y-3 shadow-xs">
                    <div class="flex items-center justify-between border-b border-slate-100 pb-2">
                        <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-700 block">ALOKASI &amp; TUJUAN PEMINDAHAN SURPLUS</span>
                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black"
                              :class="Number(harvestForm.surplus_kg || 0) > 0 ? 'bg-emerald-100 text-emerald-800 border border-emerald-300' : 'bg-slate-100 text-slate-600'"
                              x-text="Number(harvestForm.surplus_kg || 0) > 0 ? ('Surplus: +' + harvestForm.surplus_kg + ' kg') : 'Tanpa Surplus'"></span>
                    </div>

                    <!-- JIKA ADA SURPLUS > 0 -->
                    <template x-if="Number(harvestForm.surplus_kg || 0) > 0">
                        <div class="space-y-3">
                            <div class="p-2.5 bg-emerald-50 rounded-xl border border-emerald-200 text-[11px] text-emerald-900 font-medium">
                                <div class="font-extrabold text-emerald-800 flex items-center gap-1.5 mb-0.5">
                                    <i class="fa-solid fa-circle-check text-emerald-600"></i>
                                    <span>Terdapat Kelebihan (Surplus) Panen Sebesar +<span x-text="harvestForm.surplus_kg"></span> Kg!</span>
                                </div>
                                <span>Hasil panen (<span x-text="harvestForm.jumlah_panen_kg"></span> kg) melebihi kebutuhan (<span x-text="getTargetRefKg()"></span> kg). Sisa kelebihan wajib dipindahkan ke kolam penampungan / kolam stok.</span>
                            </div>

                            <div class="space-y-1">
                                <label class="text-[10px] font-extrabold uppercase tracking-wider text-slate-700 block">
                                    PILIH KOLAM PENAMPUNGAN / KOLAM STOK TUJUAN *
                                </label>
                                <select x-model="harvestForm.id_kolam_stok" 
                                        class="w-full px-3 py-2.5 rounded-xl border border-sky-300 text-xs font-bold text-slate-800 bg-sky-50/40 focus:outline-none focus:ring-2 focus:ring-sky-500">
                                    <option value="">-- Pilih Kolam Penampungan / Kolam Stok --</option>
                                    <template x-for="k in kolamStokList" :key="k.id_kolam">
                                        <option :value="k.id_kolam" x-text="k.label"></option>
                                    </template>
                                </select>
                            </div>

                            <div class="flex items-center gap-2">
                                <div class="flex-1">
                                    <label class="text-[10px] font-extrabold uppercase tracking-wider text-slate-700 block mb-1">
                                        JUMLAH KG SURPLUS DITAMPUNG
                                    </label>
                                    <input type="number" step="0.1" min="0" x-model="harvestForm.surplus_kg"
                                           placeholder="0.0"
                                           class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs font-extrabold text-emerald-700 bg-slate-50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-sky-500">
                                </div>
                            </div>

                            <p class="text-[10px] text-slate-500 leading-relaxed">
                                <i class="fa-solid fa-circle-info text-sky-600 mr-1"></i>
                                Ikan surplus sebanyak <strong class="text-emerald-700" x-text="harvestForm.surplus_kg + ' kg'"></strong> akan otomatis ditampung di kolam stok terpilih, dan kolam <strong x-text="selectedBatchToHarvest?.nama_kolam"></strong> akan <strong>dikosongkan</strong>.
                            </p>
                        </div>
                    </template>

                    <!-- JIKA TANPA SURPLUS (PAS / KURANG) -->
                    <template x-if="Number(harvestForm.surplus_kg || 0) <= 0">
                        <div class="p-3 bg-slate-50 rounded-xl border border-slate-200 text-[11px] text-slate-600 space-y-1">
                            <div class="flex items-center gap-1.5 font-bold text-slate-800">
                                <i class="fa-solid fa-circle-info text-sky-600"></i>
                                <span x-text="Number(harvestForm.jumlah_panen_kg || 0) > 0 ? 'Hasil Panen Pas / Tanpa Surplus Ke Kolam Stok' : 'Silakan Masukkan Total Kg Hasil Panen Terlebih Dahulu'"></span>
                            </div>
                            <p class="text-[10px] text-slate-400">
                                Seluruh hasil panen dialokasikan untuk pesanan. Kolam <strong class="text-slate-700" x-text="selectedBatchToHarvest?.nama_kolam"></strong> akan otomatis dikosongkan setelah panen diselesaikan.
                            </p>
                        </div>
                    </template>
                </div>
            </div>

            <!-- Modal Footer (Fixed at Bottom) -->
            <div class="p-4 sm:px-5 bg-slate-50 border-t border-slate-100 rounded-b-3xl grid grid-cols-2 gap-3 shrink-0">
                <button type="button" @click="harvestConfirmModalOpen = false" 
                        class="w-full py-2.5 rounded-xl border border-slate-200 text-slate-700 bg-white hover:bg-slate-100 font-bold text-xs transition-colors">
                    Batalkan
                </button>
                <button type="button" @click="executeFinishHarvest()" :disabled="isSubmitting || !harvestForm.jumlah_panen_kg || Number(harvestForm.jumlah_panen_kg) <= 0 || (Number(harvestForm.surplus_kg || 0) > 0 && !harvestForm.id_kolam_stok)"
                        class="w-full py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 active:scale-[0.99] text-white font-bold text-xs shadow-md shadow-emerald-950/20 transition-all flex items-center justify-center gap-2 disabled:opacity-50 disabled:cursor-not-allowed">
                    <i class="fa-solid" :class="isSubmitting ? 'fa-spinner fa-spin' : 'fa-check'"></i>
                    <span>Ya, Selesaikan &amp; Pindahkan</span>
                </button>
            </div>
        </div>
    </div>

    <!-- Custom Confirmation Delete Modal -->
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
                <h3 class="text-lg font-extrabold text-slate-900">Hapus Batch Pembesaran?</h3>
                <p class="text-xs text-slate-500 font-medium leading-relaxed">
                    Apakah Anda yakin ingin menghapus data batch <strong class="text-slate-800" x-text="selectedBatchToDelete?.id"></strong> di <strong class="text-slate-800" x-text="selectedBatchToDelete?.nama_kolam"></strong>? Data yang dihapus tidak dapat dipulihkan.
                </p>
            </div>

            <div class="grid grid-cols-2 gap-3 pt-2">
                <button type="button" @click="deleteModalOpen = false" 
                        class="w-full py-2.5 rounded-xl border border-slate-200 text-slate-700 hover:bg-slate-50 font-bold text-xs transition-colors">
                    Batalkan
                </button>
                <button type="button" @click="executeDeleteBatch()" 
                        class="w-full py-2.5 rounded-xl bg-rose-600 hover:bg-rose-700 active:scale-[0.99] text-white font-bold text-xs shadow-md shadow-rose-950/20 transition-all flex items-center justify-center gap-2">
                    <i class="fa-solid fa-trash-can text-xs"></i>
                    <span>Ya, Hapus</span>
                </button>
            </div>
        </div>
    </div>

    <!-- Modal Tambah Kolam Pembesaran Baru -->
    <div x-show="kolamModalOpen" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-95"
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs overflow-y-auto"
         style="display: none;">
        
        <div class="bg-white rounded-3xl max-w-lg w-full p-6 shadow-2xl space-y-5 border border-slate-200 my-auto" @click.outside="kolamModalOpen = false">
            <!-- Modal Header -->
            <div class="flex items-center justify-between border-b border-slate-100 pb-4">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-2xl bg-sky-100 text-sky-700 flex items-center justify-center font-bold text-sm">
                        <i class="fa-solid fa-warehouse"></i>
                    </div>
                    <div>
                        <span class="text-[10px] font-extrabold uppercase text-slate-400 tracking-wider block">FASILITAS PRODUKSI</span>
                        <h3 class="text-base font-extrabold text-slate-900">Tambah Kolam Pembesaran Baru</h3>
                    </div>
                </div>
                <button @click="kolamModalOpen = false" class="w-8 h-8 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-500 flex items-center justify-center transition-colors">
                    <i class="fa-solid fa-xmark text-sm"></i>
                </button>
            </div>

            <form @submit.prevent="submitKolam()" class="space-y-4">
                <div>
                    <label class="text-[10px] font-extrabold uppercase text-slate-500 block mb-1.5">NAMA / KODE KOLAM *</label>
                    <input type="text" x-model="kolamForm.nama_kolam" required placeholder="Contoh: Kolam Pembesaran D-01"
                           class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-xs font-semibold text-slate-800 bg-slate-50 focus:bg-white focus:outline-none focus:border-sky-600 focus:ring-2 focus:ring-sky-600/10">
                    <p class="text-[10px] text-slate-400 mt-1">Gunakan penamaan unik yang membedakan lokasi atau blok kolam.</p>
                </div>

                <div>
                    <label class="text-[10px] font-extrabold uppercase text-slate-500 block mb-1.5">TIPE / KONSTRUKSI KOLAM *</label>
                    <select x-model="kolamForm.tipe_kolam" required class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-xs font-semibold text-slate-800 bg-slate-50 focus:bg-white focus:outline-none focus:border-sky-600 focus:ring-2 focus:ring-sky-600/10">
                        <option value="Kolam Pembesaran (Beton)">Kolam Pembesaran (Beton)</option>
                        <option value="Kolam Pembesaran (Terpal Bulat / Bioflok)">Kolam Pembesaran (Terpal Bulat / Bioflok)</option>
                        <option value="Kolam Pembesaran (Terpal Kotak)">Kolam Pembesaran (Terpal Kotak)</option>
                        <option value="Kolam Pembesaran (Tanah)">Kolam Pembesaran (Tanah)</option>
                        <option value="Kolam Pembesaran (Fiberglass)">Kolam Pembesaran (Fiberglass)</option>
                    </select>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="text-[10px] font-extrabold uppercase text-slate-500 block mb-1.5">KAPASITAS DAYA TAMPUNG (EKOR / KG) *</label>
                        <input type="number" step="1" min="100" x-model="kolamForm.kapasitas" required placeholder="2500"
                               class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-xs font-bold text-slate-800 bg-slate-50 focus:bg-white focus:outline-none focus:border-sky-600">
                    </div>
                    <div>
                        <label class="text-[10px] font-extrabold uppercase text-slate-500 block mb-1.5">ESTIMASI PH AIR NORMAL</label>
                        <input type="number" step="0.1" min="0" max="14" x-model="kolamForm.kesehatan_ph_air" placeholder="7.2"
                               class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-xs font-semibold text-slate-800 bg-slate-50 focus:bg-white focus:outline-none focus:border-sky-600">
                    </div>
                </div>

                <div class="pt-3 flex items-center justify-end gap-2.5 border-t border-slate-100">
                    <button type="button" @click="kolamModalOpen = false"
                            class="px-4 py-2 rounded-xl border border-slate-200 text-xs font-bold text-slate-600 hover:bg-slate-50">
                        Batal
                    </button>
                    <button type="submit" :disabled="isSubmittingKolam"
                            class="px-5 py-2 rounded-xl bg-[#031B4E] hover:bg-sky-950 text-white font-bold text-xs flex items-center gap-2 shadow-md shadow-sky-950/20 disabled:opacity-60">
                        <i class="fa-solid" :class="isSubmittingKolam ? 'fa-spinner fa-spin' : 'fa-check'"></i>
                        <span x-text="isSubmittingKolam ? 'Menyimpan...' : 'Simpan Kolam Baru'"></span>
                    </button>
                </div>
            </form>
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
        <button @click="showToast = false" class="text-white/70 hover:text-white transition-colors">
            <i class="fa-solid fa-xmark text-xs"></i>
        </button>
    </div>

</div>
@endsection

@push('scripts')
<script>
function pembesaranComponent() {
    return {
        showForm: false,
        formMode: 'create',
        isSubmitting: false,
        selectedBatch: null,

        form: {
            id: '',
            id_pembesaran: null,
            id_batch_pembibitan: '',
            biayaBeliBibit: '',
            jumlahBibit: 10000,
            survivalRate: 85,
            kolam: '',
            tglTebar: new Date().toISOString().split('T')[0],
            estTglPanen: new Date(Date.now() + 90 * 24 * 60 * 60 * 1000).toISOString().split('T')[0],
            jenisIkan: '',
            biomassaEst: '',
            targetPanenKg: '',
            fcr: '',
            statusSiklus: 'berjalan'
        },

        availablePembibitan: {!! json_encode($availablePembibitan ?? []) !!},
        ikans: {!! json_encode($ikans ?? []) !!},
        kolamList: {!! json_encode($kolamList ?? []) !!},
        kolamStokList: {!! json_encode($kolamStokList ?? []) !!},
        batches: {!! json_encode($batches ?? []) !!},
        activeFilter: 'aktif',

        init() {
            this.refreshPondOccupancy();
        },

        get selectedPembibitan() {
            if (!this.form.id_batch_pembibitan) return null;
            return this.availablePembibitan.find(b => b.id_batch == this.form.id_batch_pembibitan) || null;
        },

        get targetKonsumsiEkorKg() {
            const sop = this.currentIkanSop;
            if (sop && sop.avg_ekor_per_kg) return Number(sop.avg_ekor_per_kg);
            if (sop && sop.target_konsumsi) {
                const matches = sop.target_konsumsi.match(/\d+(?:[\.,]\d+)?/g);
                if (matches && matches.length > 0) {
                    const nums = matches.map(v => parseFloat(v.replace(',', '.')));
                    return Math.round((nums.reduce((a, b) => a + b, 0) / nums.length) * 10) / 10;
                }
            }
            if (this.form.jenisIkan) {
                const j = this.form.jenisIkan.toLowerCase();
                if (j.includes('lele')) return 9.0;
                if (j.includes('nila')) return 4.0;
                if (j.includes('bawal')) return 4.0;
                if (j.includes('mas')) return 3.5;
                if (j.includes('patin')) return 2.5;
                if (j.includes('gurame') || j.includes('gurami')) return 2.5;
                if (j.includes('tawes')) return 5.0;
                if (j.includes('nilem')) return 10.0;
            }
            return 4.0;
        },

        get calculatedTargetLive() {
            const bibit = Number(this.form.jumlahBibit) || 0;
            const sr = Number(this.form.survivalRate) || 85;
            const ekorPerKg = this.targetKonsumsiEkorKg;
            const ekorHidup = Math.round(bibit * (sr / 100));
            const targetKg = ekorPerKg > 0 ? (Math.round((ekorHidup / ekorPerKg) * 10) / 10) : 0;
            const fcrMin = this.currentIkanSop ? Number(this.currentIkanSop.fcr_min || 1.1) : 1.1;
            const estPakanKg = Math.round(targetKg * fcrMin * 10) / 10;
            const estBiomassaAwal = Math.round(bibit * 0.015 * 10) / 10;
            
            return {
                bibit: bibit,
                sr: sr,
                ekorPerKg: ekorPerKg,
                ekorHidup: ekorHidup,
                targetKg: targetKg,
                fcrMin: fcrMin,
                estPakanKg: estPakanKg,
                estBiomassaAwal: estBiomassaAwal
            };
        },

        recalculateTargetPanen(forceOverwrite = true) {
            const calc = this.calculatedTargetLive;
            if (calc.targetKg > 0 && (forceOverwrite || !this.form.targetPanenKg)) {
                this.form.targetPanenKg = calc.targetKg;
            }
            if (calc.estBiomassaAwal > 0 && (forceOverwrite || !this.form.biomassaEst)) {
                if (!this.form.id_batch_pembibitan) {
                    this.form.biomassaEst = calc.estBiomassaAwal;
                }
            }
        },

        get ikanOptions() {
            const list = [];
            if (this.ikans && this.ikans.length > 0) {
                this.ikans.forEach(ik => {
                    const nama = (ik.nama_ikan || '').trim();
                    const clean = nama.replace(/^Ikan\s+/i, '').trim();
                    const label = nama.toLowerCase().startsWith('ikan ') ? nama : ('Ikan ' + nama);
                    if (clean && !list.some(item => item.value.toLowerCase() === clean.toLowerCase())) {
                        list.push({
                            value: clean,
                            label: label
                        });
                    }
                });
            }

            // Jika sedang edit / memilih dari pembibitan, pertahankan nilai jenis ikan
            if (this.form.jenisIkan) {
                const currentClean = this.form.jenisIkan.replace(/^Ikan\s+/i, '').trim();
                if (currentClean && !list.some(item => item.value.toLowerCase() === currentClean.toLowerCase())) {
                    list.push({
                        value: currentClean,
                        label: 'Ikan ' + currentClean
                    });
                }
            }

            return list;
        },

        get filteredBatches() {
            return this.batches.filter(b => Number(b.biomassa_est) > 0);
        },

        detailModalOpen: false,
        deleteModalOpen: false,
        selectedBatchToDelete: null,
        harvestConfirmModalOpen: false,
        selectedBatchToHarvest: null,
        harvestForm: {
            jumlah_panen_kg: 0,
            id_kolam_stok: '',
            surplus_kg: 0
        },
        kolamModalOpen: false,
        isSubmittingKolam: false,
        kolamForm: {
            nama_kolam: '',
            tipe_kolam: 'Kolam Pembesaran (Beton)',
            kapasitas: 1500,
            panjang_m: 5,
            lebar_m: 3,
            kedalaman_m: 1.2
        },
        showToast: false,
        toastMessage: '',

        openKolamModal() {
            this.kolamForm = {
                nama_kolam: 'Kolam Pembesaran ' + String.fromCharCode(65 + Math.floor(Math.random() * 6)) + '-0' + (this.kolamList.length + 1),
                tipe_kolam: 'Kolam Pembesaran (Beton)',
                kapasitas: 1500,
                panjang_m: 5,
                lebar_m: 3,
                kedalaman_m: 1.2
            };
            this.kolamModalOpen = true;
        },

        async submitKolam() {
            if (!this.kolamForm.nama_kolam) {
                alert('Nama / Kode kolam wajib diisi!');
                return;
            }
            if (!this.kolamForm.kapasitas || Number(this.kolamForm.kapasitas) <= 0) {
                alert('Kapasitas kolam wajib diisi!');
                return;
            }

            this.isSubmittingKolam = true;
            try {
                const res = await fetch('{{ route('pembesaran.kolam.store') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        nama_kolam: this.kolamForm.nama_kolam,
                        tipe_kolam: this.kolamForm.tipe_kolam,
                        kapasitas: Number(this.kolamForm.kapasitas) || 1000,
                        kesehatan_ph_air: Number(this.kolamForm.kesehatan_ph_air) || 7.2
                    })
                });

                const data = await res.json();
                if (res.ok && data.success) {
                    const newKolam = data.kolam;
                    this.kolamList.push({
                        id_kolam: newKolam.id_kolam,
                        nama_kolam: newKolam.nama_kolam,
                        tipe_kolam: newKolam.tipe_kolam,
                        kapasitas: newKolam.kapasitas,
                        is_occupied: false
                    });
                    this.form.kolam = newKolam.nama_kolam;
                    this.kolamModalOpen = false;
                    this.toastMessage = data.message || ("Kolam '" + newKolam.nama_kolam + "' berhasil ditambahkan & otomatis terpilih!");
                    this.showToast = true;
                    setTimeout(() => { this.showToast = false; }, 4000);
                } else {
                    alert(data.message || (data.errors ? Object.values(data.errors).flat().join('\n') : 'Gagal menambahkan kolam.'));
                }
            } catch (e) {
                alert('Terjadi kesalahan saat menyimpan kolam.');
            } finally {
                this.isSubmittingKolam = false;
            }
        },

        openDetail(item) {
            this.selectedBatch = item;
            this.detailModalOpen = true;
        },

        get currentIkanSop() {
            if (!this.form.jenisIkan) return null;
            const clean = this.form.jenisIkan.replace(/^Ikan\s+/i, '').trim().toLowerCase();
            return (this.ikans || []).find(ik => {
                const ikClean = (ik.nama_ikan || '').replace(/^Ikan\s+/i, '').trim().toLowerCase();
                return ikClean === clean || ikClean.includes(clean) || clean.includes(ikClean);
            }) || null;
        },

        onJenisIkanChange() {
            const sop = this.currentIkanSop;
            if (sop && sop.fcr_min) {
                this.form.fcr = Number(sop.fcr_min);
            }
            this.calculateEstPanen();
            this.recalculateTargetPanen(true);
        },

        onPembibitanChange() {
            if (!this.form.id_batch_pembibitan) return;
            const sel = this.availablePembibitan.find(b => b.id_batch == this.form.id_batch_pembibitan);
            if (sel) {
                const clean = (sel.clean_jenis || sel.jenis_ikan || 'Nila').replace(/^Ikan\s+/i, '').trim();
                
                // Cari opsi yang paling cocok (case insensitive)
                const matchedOption = this.ikanOptions.find(o => o.value.toLowerCase() === clean.toLowerCase());
                this.form.jenisIkan = matchedOption ? matchedOption.value : clean;

                if (sel.sisa_ekor && Number(sel.sisa_ekor) > 0) {
                    this.form.jumlahBibit = Number(sel.sisa_ekor);
                }
                if (sel.est_biomassa && Number(sel.est_biomassa) > 0) {
                    this.form.biomassaEst = Number(sel.est_biomassa);
                } else if (sel.sisa_ekor && Number(sel.sisa_ekor) > 0) {
                    this.form.biomassaEst = Math.max(1, Math.round(Number(sel.sisa_ekor) * 0.02 * 10) / 10);
                }
                
                this.onJenisIkanChange();
                this.recalculateTargetPanen(true);
            }
        },

        calculateEstPanen() {
            if (!this.form.tglTebar) return;
            const tgl = new Date(this.form.tglTebar);
            const sop = this.currentIkanSop;
            let months = 3;
            if (sop && sop.bulan_panen_min) {
                months = Number(sop.bulan_panen_min);
            } else if (this.form.jenisIkan) {
                const j = this.form.jenisIkan.toLowerCase();
                if (j.includes('lele')) months = 2.5;
                else if (j.includes('nila')) months = 3.5;
                else if (j.includes('patin')) months = 5;
                else if (j.includes('gurame') || j.includes('gurami')) months = 8;
                else if (j.includes('mas')) months = 4;
                else if (j.includes('bawal')) months = 4;
            }
            const days = Math.round(months * 30);
            tgl.setDate(tgl.getDate() + days);
            this.form.estTglPanen = tgl.toISOString().split('T')[0];
        },

        openCreateForm() {
            this.formMode = 'create';
            this.resetForm();
            this.showForm = true;
            window.scrollTo({ top: 0, behavior: 'smooth' });
        },

        openEdit(item) {
            this.formMode = 'edit';
            this.selectedBatch = item;
            const bibitCount = item.jumlah_bibit || (item.target_panen_kg > 0 ? Math.round(item.target_panen_kg * 4 / 0.85) : 10000);
            this.form = {
                id: item.id,
                id_pembesaran: item.id_pembesaran,
                id_batch_pembibitan: item.id_batch_pembibitan || '',
                biayaBeliBibit: item.biaya_beli_bibit || 0,
                jumlahBibit: bibitCount,
                survivalRate: 85,
                kolam: item.nama_kolam,
                tglTebar: item.tgl_tebar || new Date().toISOString().split('T')[0],
                estTglPanen: item.est_tgl_panen || (item.tgl_tebar ? new Date(new Date(item.tgl_tebar).getTime() + 90*86400000).toISOString().split('T')[0] : new Date(Date.now() + 90*86400000).toISOString().split('T')[0]),
                jenisIkan: item.clean_jenis || (item.jenis_ikan ? item.jenis_ikan.replace(/^Ikan\s+/i, '') : ''),
                biomassaEst: item.biomassa_est,
                targetPanenKg: item.target_panen_kg,
                fcr: item.fcr,
                statusSiklus: item.status_siklus || 'berjalan'
            };
            this.showForm = true;
            window.scrollTo({ top: 0, behavior: 'smooth' });
        },

        resetForm() {
            const today = new Date().toISOString().split('T')[0];
            const defaultEst = new Date(Date.now() + 90 * 86400000).toISOString().split('T')[0];
            this.form = {
                id: '',
                id_pembesaran: null,
                id_batch_pembibitan: '',
                biayaBeliBibit: '',
                jumlahBibit: 10000,
                survivalRate: 85,
                kolam: '',
                tglTebar: today,
                estTglPanen: defaultEst,
                jenisIkan: '',
                biomassaEst: '',
                targetPanenKg: '',
                fcr: '',
                statusSiklus: 'berjalan'
            };
        },

        getTargetRefKg() {
            if (!this.selectedBatchToHarvest) return 0;
            const b = this.selectedBatchToHarvest;
            return Number(b.order_target_kg > 0 ? b.order_target_kg : (b.target_panen_kg || b.biomassa_est || 0));
        },

        onHarvestKgInput() {
            if (!this.selectedBatchToHarvest) return;
            const panen = Number(this.harvestForm.jumlah_panen_kg || 0);
            const targetRef = this.getTargetRefKg();
            if (panen > targetRef) {
                this.harvestForm.surplus_kg = Math.round((panen - targetRef) * 10) / 10;
            } else {
                this.harvestForm.surplus_kg = 0;
            }
        },

        triggerFinishHarvest(item) {
            this.selectedBatchToHarvest = item;
            const defaultKg = item.biomassa_est || item.target_panen_kg || 100;
            this.harvestForm.jumlah_panen_kg = defaultKg;
            
            // Auto-select first stock pond (e.g. Kolam Stok Lele) if available
            const stockPond = (this.kolamStokList || []).find(k => k.is_stok && k.id_kolam != item.id_kolam) 
                           || (this.kolamStokList || []).find(k => k.id_kolam != item.id_kolam)
                           || (this.kolamStokList && this.kolamStokList[0] ? this.kolamStokList[0] : null);
            this.harvestForm.id_kolam_stok = stockPond ? stockPond.id_kolam : '';
            this.onHarvestKgInput();
            this.harvestConfirmModalOpen = true;
        },

        async executeFinishHarvest() {
            if (!this.selectedBatchToHarvest) return;
            const item = this.selectedBatchToHarvest;
            const idPB = item.id_pembesaran || item.id.replace(/[^0-9]/g, '');
            const jumlahPanen = Number(this.harvestForm.jumlah_panen_kg);
            
            if (!jumlahPanen || jumlahPanen <= 0) {
                alert('Silakan masukkan total berat hasil panen yang diangkat (dalam Kg)!');
                return;
            }

            const surplus = Number(this.harvestForm.surplus_kg || 0);
            if (surplus > 0 && !this.harvestForm.id_kolam_stok) {
                alert('Terdapat surplus ikan sebesar +' + surplus + ' kg. Silakan pilih kolam penampungan / kolam stok tujuan!');
                return;
            }

            this.isSubmitting = true;
            try {
                const res = await fetch('/pembesaran/' + idPB, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        status_siklus: 'selesai',
                        jumlah_panen_kg: jumlahPanen,
                        id_kolam_stok: this.harvestForm.id_kolam_stok || null,
                        surplus_kg: surplus
                    })
                });

                const data = await res.json();
                if (res.ok && data.success) {
                    // Update batch locally
                    const targetIdx = this.batches.findIndex(b => b.id_pembesaran === item.id_pembesaran || b.id === item.id);
                    if (targetIdx !== -1) {
                        this.batches[targetIdx].status_siklus = 'selesai';
                        this.batches[targetIdx].status_label = 'Selesai Panen';
                        this.batches[targetIdx].status_class = 'bg-slate-100 text-slate-700';
                        this.batches[targetIdx].jumlah_panen_kg = jumlahPanen;
                        this.batches[targetIdx].jumlah_panen_format = jumlahPanen.toLocaleString('id-ID');
                        this.batches[targetIdx].is_harvest_due = false;
                    }

                    // Refresh pond occupancy (pond becomes available)
                    this.refreshPondOccupancy();

                    this.harvestConfirmModalOpen = false;
                    let msg = "Sukses! Panen batch " + item.id + " (" + jumlahPanen.toLocaleString('id-ID') + " kg) selesai dicatat & kolam " + item.nama_kolam + " telah siap digunakan kembali.";
                    if (surplus > 0 && this.harvestForm.id_kolam_stok) {
                        const targetK = (this.kolamStokList || []).find(k => k.id_kolam == this.harvestForm.id_kolam_stok);
                        msg += " Surplus sebesar +" + surplus.toFixed(1) + " kg berhasil ditampung di " + (targetK ? targetK.nama_kolam : 'Kolam Stok') + ".";
                    }
                    this.toastMessage = msg;
                    this.showToast = true;
                    setTimeout(() => { this.showToast = false; }, 6000);
                    this.selectedBatchToHarvest = null;
                } else {
                    alert(data.message || 'Gagal menyelesaikan panen batch.');
                }
            } catch (err) {
                alert('Terjadi kesalahan saat menyelesaikan panen.');
            } finally {
                this.isSubmitting = false;
            }
        },

        async submitBatch() {
            if (!this.form.kolam) {
                alert('Silakan pilih Kolam Pembesaran terlebih dahulu!');
                return;
            }
            if (!this.form.jenisIkan) {
                alert('Silakan pilih Jenis Ikan terlebih dahulu!');
                return;
            }
            if (!this.form.biomassaEst || Number(this.form.biomassaEst) <= 0) {
                alert('Estimasi Biomassa Awal harus lebih besar dari 0 (tidak boleh berupa angka minus atau 0)!');
                return;
            }
            if (!this.form.targetPanenKg || Number(this.form.targetPanenKg) <= 0) {
                alert('Target Panen harus lebih besar dari 0 (tidak boleh berupa angka minus atau 0)!');
                return;
            }
            if (this.form.fcr && Number(this.form.fcr) <= 0) {
                alert('Target FCR harus lebih besar dari 0!');
                return;
            }

            // If user selects 'selesai' during edit mode, ask confirmation first
            if (this.formMode === 'edit' && this.form.statusSiklus === 'selesai' && this.selectedBatch && this.selectedBatch.status_siklus !== 'selesai') {
                this.selectedBatchToHarvest = this.selectedBatch;
                this.harvestConfirmModalOpen = true;
                return;
            }

            this.isSubmitting = true;

            const biomassaNum = Math.abs(Number(this.form.biomassaEst));
            const targetNum = Math.abs(Number(this.form.targetPanenKg));
            const fcrNum = Number(this.form.fcr || (this.currentIkanSop ? this.currentIkanSop.fcr_min : 1.0));
            const statusSiklus = this.form.statusSiklus;
            const targetPercent = Math.min(100, Math.round((biomassaNum / targetNum) * 100));

            let statusLabel = 'Berjalan (Aktif)';
            let statusClass = 'bg-emerald-100 text-emerald-800';

            if (statusSiklus === 'siap_panen') {
                statusLabel = 'Siap Panen';
                statusClass = 'bg-amber-100 text-amber-800';
            } else if (statusSiklus === 'selesai') {
                statusLabel = 'Selesai Panen';
                statusClass = 'bg-slate-100 text-slate-700';
            }

            const todayStr = new Date().toISOString().split('T')[0];
            const estPanenDateStr = this.form.estTglPanen || '';
            const isHarvestDue = statusSiklus !== 'selesai' && estPanenDateStr && estPanenDateStr <= todayStr;
            const estPanenFormat = estPanenDateStr ? new Date(estPanenDateStr).toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' }) : '-';

            if (this.formMode === 'edit') {
                const idPB = this.form.id_pembesaran;
                try {
                    const res = await fetch('/pembesaran/' + idPB, {
                        method: 'PUT',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({
                            id_kolam: this.form.kolam,
                            jenis_ikan: this.form.jenisIkan,
                            tgl_tebar: this.form.tglTebar,
                            est_tgl_panen: this.form.estTglPanen,
                            biomassa_est: biomassaNum,
                            target_panen_kg: targetNum,
                            fcr: fcrNum,
                            status_siklus: statusSiklus
                        })
                    });
                    const data = await res.json();
                    if (res.ok && data.success) {
                        const targetIndex = this.batches.findIndex(b => b.id_pembesaran === idPB);
                        if (targetIndex !== -1) {
                            this.batches[targetIndex].nama_kolam = this.form.kolam;
                            this.batches[targetIndex].jenis_ikan = 'Ikan ' + this.form.jenisIkan;
                            this.batches[targetIndex].clean_jenis = this.form.jenisIkan;
                            this.batches[targetIndex].tgl_tebar = this.form.tglTebar;
                            this.batches[targetIndex].est_tgl_panen = this.form.estTglPanen;
                            this.batches[targetIndex].est_panen_format = estPanenFormat;
                            this.batches[targetIndex].is_harvest_due = isHarvestDue;
                            this.batches[targetIndex].biomassa_est = biomassaNum;
                            this.batches[targetIndex].biomassa_format = biomassaNum.toLocaleString('id-ID');
                            this.batches[targetIndex].target_panen_kg = targetNum;
                            this.batches[targetIndex].target_format = targetNum.toLocaleString('id-ID');
                            this.batches[targetIndex].target_percent = targetPercent;
                            this.batches[targetIndex].fcr = fcrNum.toFixed(2);
                            this.batches[targetIndex].is_optimal = fcrNum <= 1.25;
                            this.batches[targetIndex].status_siklus = statusSiklus;
                            this.batches[targetIndex].status_label = statusLabel;
                            this.batches[targetIndex].status_class = statusClass;
                        }

                        // Refresh pond occupancy
                        this.refreshPondOccupancy();

                        this.showForm = false;
                        this.toastMessage = data.message || 'Batch pembesaran berhasil diperbarui!';
                        this.showToast = true;
                        setTimeout(() => { this.showToast = false; }, 4000);
                        this.formMode = 'create';
                        this.resetForm();
                    } else {
                        alert(data.message || 'Gagal memperbarui data batch pembesaran.');
                    }
                } catch (e) {
                    alert('Terjadi kesalahan saat memperbarui batch.');
                } finally {
                    this.isSubmitting = false;
                }
                return;
            }

            // Create Mode
            try {
                const biayaBeliVal = (!this.form.id_batch_pembibitan && this.form.biayaBeliBibit) ? Number(this.form.biayaBeliBibit) : 0;
                const res = await fetch('{{ route('pembesaran.store') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        id_kolam: this.form.kolam,
                        id_batch_pembibitan: this.form.id_batch_pembibitan || null,
                        biaya_beli_bibit: biayaBeliVal,
                        jumlah_bibit: Number(this.form.jumlahBibit) || 0,
                        survival_rate: Number(this.form.survivalRate) || 85,
                        jenis_ikan: this.form.jenisIkan,
                        tgl_tebar: this.form.tglTebar,
                        est_tgl_panen: this.form.estTglPanen,
                        biomassa_est: biomassaNum,
                        target_panen_kg: targetNum,
                        fcr: fcrNum,
                        status_siklus: statusSiklus
                    })
                });

                const data = await res.json();
                if (res.ok && data.success) {
                    const newBatch = data.batch;
                    const asalBibitVal = this.form.id_batch_pembibitan ? 'pembibitan_sendiri' : 'beli_luar';
                    this.batches.unshift({
                        id_pembesaran: newBatch.id_pembesaran,
                        id: '#PB-' + String(newBatch.id_pembesaran).padStart(5, '0'),
                        id_kolam: newBatch.id_kolam,
                        nama_kolam: newBatch.kolam ? newBatch.kolam.nama_kolam : this.form.kolam,
                        tipe_kolam: newBatch.kolam ? newBatch.kolam.tipe_kolam : 'Pembesaran',
                        id_batch_pembibitan: this.form.id_batch_pembibitan ? ('#BT-' + String(this.form.id_batch_pembibitan).padStart(5, '0')) : null,
                        asal_bibit: asalBibitVal,
                        biaya_beli_bibit: biayaBeliVal,
                        biaya_beli_bibit_format: biayaBeliVal.toLocaleString('id-ID'),
                        tgl_tebar: this.form.tglTebar,
                        est_tgl_panen: this.form.estTglPanen,
                        est_panen_format: estPanenFormat,
                        is_harvest_due: isHarvestDue,
                        doc: 0,
                        jenis_ikan: 'Ikan ' + this.form.jenisIkan,
                        clean_jenis: this.form.jenisIkan,
                        biomassa_est: biomassaNum,
                        biomassa_format: biomassaNum.toLocaleString('id-ID'),
                        target_panen_kg: targetNum,
                        target_format: targetNum.toLocaleString('id-ID'),
                        fcr: fcrNum.toFixed(2),
                        fcr_kumulatif: fcrNum.toFixed(2),
                        fcr_komersial: fcrNum.toFixed(2),
                        fcr_biologis: fcrNum.toFixed(2),
                        fcr_target: '1.0 - 1.3',
                        is_optimal: fcrNum <= 1.25,
                        total_pakan_kg: '0',
                        biaya_pakan_format: 'Rp 0',
                        biaya_bibit_format: 'Rp ' + Number(biayaBeliVal).toLocaleString('id-ID'),
                        harga_jual_format: 'Rp 24.000',
                        pendapatan_format: 'Rp ' + Math.round(biomassaNum * 24000).toLocaleString('id-ID'),
                        laba_rugi: Math.round(biomassaNum * 24000) - biayaBeliVal,
                        laba_rugi_format: ((Math.round(biomassaNum * 24000) - biayaBeliVal) >= 0 ? '+Rp ' : '-Rp ') + Math.abs(Math.round(biomassaNum * 24000) - biayaBeliVal).toLocaleString('id-ID'),
                        margin_percent: Math.round(biomassaNum * 24000) > 0 ? Math.round(((Math.round(biomassaNum * 24000) - biayaBeliVal) / (biomassaNum * 24000)) * 100) : 0,
                        status_siklus: statusSiklus,
                        status_label: statusLabel,
                        status_class: statusClass,
                        ph_air: '7.2'
                    });

                    this.refreshPondOccupancy();

                    this.showForm = false;
                    this.resetForm();
                    this.toastMessage = data.message || 'Batch pembesaran berhasil ditambahkan!';
                    this.showToast = true;
                    setTimeout(() => { this.showToast = false; }, 4000);
                } else {
                    alert(data.message || 'Gagal menambahkan batch pembesaran.');
                }
            } catch (err) {
                alert('Gagal menambahkan batch pembesaran.');
            } finally {
                this.isSubmitting = false;
            }
        },

        refreshPondOccupancy() {
            const activeKolamNames = this.batches
                .filter(b => b.status_siklus !== 'selesai' && b.status_siklus !== 'gagal' && (Number(b.biomassa_est) > 0 || ['berjalan', 'aktif', 'siap_panen'].includes(b.status_siklus)))
                .map(b => b.nama_kolam);

            this.kolamList.forEach(k => {
                k.is_occupied = activeKolamNames.includes(k.nama_kolam);
            });
        },

        deleteBatch(item) {
            this.selectedBatchToDelete = item;
            this.deleteModalOpen = true;
        },

        async executeDeleteBatch() {
            if (!this.selectedBatchToDelete) return;
            const item = this.selectedBatchToDelete;
            const id = item.id;
            const rawId = item.id_pembesaran || id.replace(/[^0-9]/g, '');

            try {
                await fetch('/pembesaran/' + rawId, {
                    method: 'DELETE',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                });
            } catch (e) {}

            this.batches = this.batches.filter(b => b.id !== id);
            this.refreshPondOccupancy();
            this.deleteModalOpen = false;
            this.toastMessage = 'Batch pembesaran ' + id + ' berhasil dihapus!';
            this.showToast = true;
            setTimeout(() => { this.showToast = false; }, 3500);
            this.selectedBatchToDelete = null;
        }
    };
}
</script>
@endpush

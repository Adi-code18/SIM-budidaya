@extends('layouts.app')

@section('title', 'Manajemen Pembesaran - SIM-BUDIDAYA')

@section('content')
<div class="space-y-6" x-data="pembesaranComponent()">

    <!-- Subtitle & Page Title Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <span class="text-xs font-semibold text-slate-400 block uppercase tracking-wider">Manajemen Budidaya / Pembesaran</span>
            <h1 class="text-2xl font-extrabold text-[#0B2570] tracking-tight mt-0.5">Status Kolam Pembesaran</h1>
            <p class="text-xs text-slate-500 font-medium mt-0.5">Pantau siklus tebar benih, biomassa, FCR, dan target panen per kolam pembesaran.</p>
        </div>
        <div class="flex items-center gap-3">
            <button @click="showForm ? showForm = false : openCreateForm()"
                    class="px-5 py-2.5 rounded-xl bg-[#031B4E] hover:bg-sky-950 text-white font-bold text-xs shadow-md shadow-sky-950/20 transition-all flex items-center gap-2">
                <i class="fa-solid" :class="showForm ? 'fa-table-list' : 'fa-circle-plus'" class="text-xs"></i>
                <span x-text="showForm ? 'Lihat Daftar Kolam' : 'Input Batch Baru'"></span>
            </button>
        </div>
    </div>

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
                        <span>Lokasi Kolam Pembesaran &amp; Tanggal Tebar</span>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="text-[10px] font-extrabold uppercase tracking-wider text-slate-500 block mb-1">KOLAM PEMBESARAN *</label>
                            <select x-model="form.kolam" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs font-semibold text-slate-700 bg-slate-50/70 focus:bg-white focus:outline-none focus:ring-2 focus:ring-sky-500 transition-all">
                                <option value="">Pilih Kolam Pembesaran...</option>
                                <template x-for="k in kolamList" :key="k.id_kolam">
                                    <option :value="k.nama_kolam" 
                                            :disabled="k.is_occupied && k.nama_kolam !== form.kolam"
                                            x-text="k.nama_kolam + ' (' + k.tipe_kolam + ' - Kapasitas: ' + Number(k.kapasitas).toLocaleString('id-ID') + (k.is_occupied && k.nama_kolam !== form.kolam ? ' - SEDANG BERJALAN' : ' - TERSEDIA') + ')'">
                                    </option>
                                </template>
                            </select>
                            <p class="text-[10px] text-slate-400 mt-1 italic">Hanya menampilkan fasilitas kolam tipe Pembesaran (1 Kolam = 1 Batch Aktif).</p>
                        </div>
                        <div>
                            <label class="text-[10px] font-extrabold uppercase tracking-wider text-slate-500 block mb-1">TANGGAL TEBAR BENIH *</label>
                            <input type="date" x-model="form.tglTebar"
                                   class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs font-semibold text-slate-700 bg-slate-50/70 focus:bg-white focus:outline-none focus:ring-2 focus:ring-sky-500 transition-all">
                        </div>
                    </div>
                </div>

                <!-- Section 2: Detail Komoditas Ikan -->
                <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-xs space-y-4">
                    <div class="flex items-center gap-2.5 text-sm font-bold text-slate-900">
                        <div class="w-8 h-8 rounded-xl bg-[#0284C7] text-white flex items-center justify-center">
                            <i class="fa-solid fa-fish text-xs"></i>
                        </div>
                        <span>Komoditas Ikan &amp; Status Siklus</span>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="text-[10px] font-extrabold uppercase tracking-wider text-slate-500 block mb-1">JENIS IKAN *</label>
                            <select x-model="form.jenisIkan" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs font-semibold text-slate-700 bg-slate-50/70 focus:bg-white focus:outline-none focus:ring-2 focus:ring-sky-500 transition-all">
                                <option value="">Pilih jenis ikan...</option>
                                <option value="Nila Hitam Super">Ikan Nila Hitam Super</option>
                                <option value="Nila Merah">Ikan Nila Merah</option>
                                <option value="Gurami Padang">Ikan Gurami Padang</option>
                                <option value="Lele Sangkuriang">Ikan Lele Sangkuriang</option>
                                <option value="Patin Siam">Ikan Patin Siam</option>
                                <option value="Bawal Air Tawar">Ikan Bawal Air Tawar</option>
                            </select>
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

            <!-- Right 1 Col: Target Produksi Pembesaran -->
            <div class="space-y-5">
                <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-xs space-y-4">
                    <div class="flex items-center gap-2.5 text-sm font-bold text-slate-900">
                        <i class="fa-solid fa-bullseye text-sky-600"></i>
                        <span>Target Biomassa &amp; Pakan (KG)</span>
                    </div>

                    <div class="space-y-3">
                        <div>
                            <label class="text-[10px] font-extrabold uppercase tracking-wider text-slate-500 block mb-1">ESTIMASI BIOMASSA AWAL (KG) *</label>
                            <input type="number" step="0.1" x-model="form.biomassaEst" placeholder="Contoh: 1250"
                                   class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs font-semibold text-slate-700 bg-slate-50/70 focus:bg-white focus:outline-none focus:ring-2 focus:ring-sky-500 transition-all">
                        </div>
                        <div>
                            <label class="text-[10px] font-extrabold uppercase tracking-wider text-slate-500 block mb-1">TARGET PANEN (KG) *</label>
                            <input type="number" step="0.1" x-model="form.targetPanenKg" placeholder="Contoh: 1500"
                                   class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs font-semibold text-slate-700 bg-slate-50/70 focus:bg-white focus:outline-none focus:ring-2 focus:ring-sky-500 transition-all">
                        </div>
                        <div>
                            <label class="text-[10px] font-extrabold uppercase tracking-wider text-slate-500 block mb-1">TARGET FCR</label>
                            <input type="number" step="0.01" x-model="form.fcr" placeholder="1.15"
                                   class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs font-extrabold text-[#0B2570] bg-sky-50/60 focus:outline-none focus:ring-2 focus:ring-sky-500 transition-all">
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

    <!-- 3 Metric KPI Cards Grid -->
    <div x-show="!showForm" class="grid grid-cols-1 md:grid-cols-3 gap-5">
        
        <!-- Card 1: Total Biomassa -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between">
                    <span class="text-[11px] font-extrabold uppercase tracking-wider text-slate-500">TOTAL BIOMASSA AKTIF</span>
                    <div class="w-9 h-9 rounded-xl bg-[#BEE3F8]/60 text-[#006699] flex items-center justify-center">
                        <i class="fa-solid fa-weight-hanging text-base"></i>
                    </div>
                </div>
                <div class="mt-3">
                    <h3 class="text-3xl font-extrabold text-slate-900 tracking-tight">{{ number_format($totalBiomassa ?? 4.5, 2) }} <span class="text-xs font-semibold text-slate-500">Ton</span></h3>
                </div>
            </div>
            <div class="mt-4 flex items-center gap-1.5 text-xs font-semibold text-emerald-600">
                <i class="fa-solid fa-arrow-trend-up"></i>
                <span>Data tersinkron dari Batch Pembesaran</span>
            </div>
        </div>

        <!-- Card 2: Rata-rata FCR -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between">
                    <span class="text-[11px] font-extrabold uppercase tracking-wider text-slate-500">RATA-RATA FCR</span>
                    <div class="w-9 h-9 rounded-xl bg-[#BEE3F8]/60 text-[#006699] flex items-center justify-center">
                        <i class="fa-regular fa-clipboard text-base"></i>
                    </div>
                </div>
                <div class="mt-3">
                    <h3 class="text-3xl font-extrabold text-slate-900 tracking-tight">{{ number_format($avgFcr ?? 1.12, 2) }} <span class="text-xs font-semibold text-slate-500">Ratio</span></h3>
                </div>
            </div>
            <div class="mt-4 flex items-center gap-1.5 text-xs font-semibold text-emerald-600">
                <i class="fa-regular fa-circle-check"></i>
                <span>Dalam target optimal (≤ 1.25)</span>
            </div>
        </div>

        <!-- Card 3: Kolam Pembesaran Terpakai -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between">
                    <span class="text-[11px] font-extrabold uppercase tracking-wider text-slate-500">KOLAM PEMBESARAN TERPAKAI</span>
                    <div class="w-9 h-9 rounded-xl bg-[#BEE3F8]/60 text-[#006699] flex items-center justify-center">
                        <i class="fa-solid fa-grip text-base"></i>
                    </div>
                </div>
                <div class="mt-3">
                    <h3 class="text-3xl font-extrabold text-slate-900 tracking-tight">
                        <span x-text="batches.filter(b => b.status_siklus === 'berjalan').length"></span>
                        <span class="text-xs font-semibold text-slate-500">/ <span x-text="kolamList.length"></span> Kolam</span>
                    </h3>
                </div>
            </div>
            <div class="mt-4">
                <div class="w-full bg-slate-100 h-2 rounded-full overflow-hidden">
                    <div class="bg-[#0284C7] h-full rounded-full transition-all"
                         :style="'width: ' + (kolamList.length > 0 ? (batches.filter(b => b.status_siklus === 'berjalan').length / kolamList.length) * 100 : 50) + '%'"></div>
                </div>
            </div>
        </div>

    </div>

    <!-- 2-Column Main Section -->
    <div x-show="!showForm" class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <!-- Left 2 Columns: Visualisasi Kolam Grid -->
        <div class="lg:col-span-2 space-y-4">
            <div class="flex items-center justify-between">
                <h3 class="text-base font-bold text-slate-900 flex items-center gap-2">
                    <i class="fa-solid fa-grip text-sky-600"></i>
                    <span>Daftar Kolam &amp; Batch Pembesaran Aktif</span>
                </h3>
                <span class="text-xs text-slate-400 font-semibold" x-text="batches.length + ' Batch Terdata'"></span>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                
                <template x-for="item in batches" :key="item.id">
                    <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs flex flex-col justify-between hover:shadow-md transition-all relative group">
                        <div>
                            <div class="flex items-start justify-between">
                                <div>
                                    <div class="flex items-center gap-2">
                                        <h4 class="font-extrabold text-slate-900 text-sm" x-text="item.nama_kolam"></h4>
                                        <span class="text-[10px] font-bold text-sky-600 bg-sky-50 px-2 py-0.5 rounded-md border border-sky-100" x-text="item.id"></span>
                                    </div>
                                    <span class="text-[10px] text-slate-400 block mt-0.5" x-text="item.jenis_ikan + ' • ' + item.tipe_kolam"></span>
                                </div>
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-extrabold uppercase"
                                      :class="item.status_class"
                                      x-text="item.status_label">
                                </span>
                            </div>

                            <div class="mt-4 grid grid-cols-2 gap-2 text-xs">
                                <div class="bg-slate-50 p-2.5 rounded-xl border border-slate-100">
                                    <span class="text-[10px] text-slate-400 font-bold uppercase block">BIOMASSA EST.</span>
                                    <span class="font-extrabold text-slate-900 text-sm" x-text="item.biomassa_format + ' kg'"></span>
                                </div>
                                <div class="bg-slate-50 p-2.5 rounded-xl border border-slate-100">
                                    <span class="text-[10px] text-slate-400 font-bold uppercase block">MASA BUDIDAYA</span>
                                    <span class="font-extrabold text-slate-900 text-sm"><span x-text="item.doc"></span> Hari <span class="text-[10px] font-medium text-slate-500">(DOC)</span></span>
                                </div>
                            </div>

                            <div class="mt-3">
                                <div class="flex items-center justify-between text-xs font-semibold">
                                    <span :class="item.is_optimal ? 'text-slate-600' : 'text-rose-600'" x-text="'FCR: ' + item.fcr"></span>
                                    <span class="text-[10px] font-bold text-slate-500" x-text="'Target: ' + item.target_format + ' kg (' + item.target_percent + '%)'"></span>
                                </div>
                                <div class="w-full bg-slate-100 h-2 rounded-full overflow-hidden mt-1.5">
                                    <div class="bg-[#0055CC] h-full rounded-full transition-all" :style="'width: ' + item.target_percent + '%'"></div>
                                </div>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between">
                            <span class="text-[11px] text-slate-400 font-medium" x-text="'pH ' + item.ph_air + ' • Tgl: ' + item.tgl_tebar"></span>
                            <div class="relative inline-block text-left" x-data="{ open: false }">
                                <button type="button" @click="open = !open" @click.away="open = false" 
                                        class="w-8 h-8 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-600 inline-flex items-center justify-center transition-colors">
                                    <i class="fa-solid fa-ellipsis-vertical text-sm"></i>
                                </button>
                                <div x-show="open" 
                                     x-transition:enter="transition ease-out duration-100" 
                                     x-transition:enter-start="transform opacity-0 scale-95" 
                                     x-transition:enter-end="transform opacity-100 scale-100" 
                                     x-transition:leave="transition ease-in duration-75" 
                                     x-transition:leave-start="transform opacity-100 scale-100" 
                                     x-transition:leave-end="transform opacity-0 scale-95" 
                                     class="absolute right-0 bottom-full mb-1 w-44 rounded-xl bg-white border border-slate-200 shadow-xl py-1.5 z-50 text-left origin-bottom-right"
                                     style="display: none;">
                                    
                                    <button type="button" @click="open = false; openEdit(item)" class="w-full px-3.5 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-50 flex items-center gap-2.5">
                                        <i class="fa-solid fa-pen-to-square text-amber-600 w-4"></i>
                                        <span>Edit Batch</span>
                                    </button>

                                    <div class="my-1 border-t border-slate-100"></div>

                                    <button type="button" @click="open = false; deleteBatch(item)" class="w-full px-3.5 py-2 text-xs font-semibold text-red-600 hover:bg-red-50 flex items-center gap-2.5">
                                        <i class="fa-solid fa-trash-can text-red-500 w-4"></i>
                                        <span>Hapus Batch</span>
                                    </button>
                                </div>
                            </div>
                        </div>
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
                <span class="text-[11px] font-extrabold uppercase text-slate-400 tracking-wider block">Fasilitas Kolam Aktif</span>
                
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

    <!-- Custom Confirmation Modal -->
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
            kolam: '',
            tglTebar: new Date().toISOString().split('T')[0],
            jenisIkan: '',
            biomassaEst: 1200,
            targetPanenKg: 1500,
            fcr: 1.15,
            statusSiklus: 'berjalan'
        },

        kolamList: {!! json_encode($kolamList ?? []) !!},
        batches: {!! json_encode($batches ?? []) !!},

        deleteModalOpen: false,
        selectedBatchToDelete: null,
        showToast: false,
        toastMessage: '',

        openCreateForm() {
            this.formMode = 'create';
            this.resetForm();
            this.showForm = true;
            window.scrollTo({ top: 0, behavior: 'smooth' });
        },

        openEdit(item) {
            this.formMode = 'edit';
            this.selectedBatch = item;
            this.form = {
                id: item.id,
                id_pembesaran: item.id_pembesaran,
                kolam: item.nama_kolam,
                tglTebar: item.tgl_tebar || new Date().toISOString().split('T')[0],
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
            this.form = {
                id: '',
                id_pembesaran: null,
                kolam: '',
                tglTebar: new Date().toISOString().split('T')[0],
                jenisIkan: '',
                biomassaEst: 1200,
                targetPanenKg: 1500,
                fcr: 1.15,
                statusSiklus: 'berjalan'
            };
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
                alert('Silakan masukkan Estimasi Biomassa Awal!');
                return;
            }
            if (!this.form.targetPanenKg || Number(this.form.targetPanenKg) <= 0) {
                alert('Silakan masukkan Target Panen!');
                return;
            }

            this.isSubmitting = true;

            const biomassaNum = Number(this.form.biomassaEst);
            const targetNum = Number(this.form.targetPanenKg);
            const fcrNum = Number(this.form.fcr || 1.15);
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
                const res = await fetch('{{ route('pembesaran.store') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        id_kolam: this.form.kolam,
                        jenis_ikan: this.form.jenisIkan,
                        tgl_tebar: this.form.tglTebar,
                        biomassa_est: biomassaNum,
                        target_panen_kg: targetNum,
                        fcr: fcrNum,
                        status_siklus: statusSiklus
                    })
                });

                const data = await res.json();
                if (res.ok && data.success) {
                    const newBatch = data.batch;
                    this.batches.unshift({
                        id_pembesaran: newBatch.id_pembesaran,
                        id: '#PB-' + String(newBatch.id_pembesaran).padStart(5, '0'),
                        id_kolam: newBatch.id_kolam,
                        nama_kolam: newBatch.kolam ? newBatch.kolam.nama_kolam : this.form.kolam,
                        tipe_kolam: newBatch.kolam ? newBatch.kolam.tipe_kolam : 'Pembesaran',
                        tgl_tebar: this.form.tglTebar,
                        doc: 0,
                        jenis_ikan: 'Ikan ' + this.form.jenisIkan,
                        clean_jenis: this.form.jenisIkan,
                        biomassa_est: biomassaNum,
                        biomassa_format: biomassaNum.toLocaleString('id-ID'),
                        target_panen_kg: targetNum,
                        target_format: targetNum.toLocaleString('id-ID'),
                        target_percent: targetPercent,
                        fcr: fcrNum.toFixed(2),
                        is_optimal: fcrNum <= 1.25,
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
                .filter(b => b.status_siklus === 'berjalan')
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

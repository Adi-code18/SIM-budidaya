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
                    <div x-show="formMode === 'create'" class="p-3.5 bg-sky-50/70 rounded-xl border border-sky-100 space-y-1.5">
                        <label class="text-[10px] font-extrabold uppercase tracking-wider text-sky-900 block">AMBIL DARI BATCH PEMBIBITAN (OPSIONAL - MENCEGAH DOUBLE INPUT)</label>
                        <select x-model="form.id_batch_pembibitan" @change="onPembibitanChange()" class="w-full px-3.5 py-2 rounded-xl border border-sky-200 text-xs font-semibold text-slate-800 bg-white focus:outline-none focus:ring-2 focus:ring-sky-500">
                            <option value="">-- Input Manual (Bukan dari Pembibitan) --</option>
                            <template x-for="bp in availablePembibitan" :key="bp.id_batch">
                                <option :value="bp.id_batch" x-text="bp.label"></option>
                            </template>
                        </select>
                        <p class="text-[10px] text-sky-700 italic">Memilih batch pembibitan akan otomatis mengisi jenis ikan dan mengalihkan status pembibitan menjadi Selesai/Dipindahkan.</p>
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
                                <option value="Nila">Ikan Nila</option>
                                <option value="Gurami Padang">Ikan Gurami Padang</option>
                                <option value="Gurami">Ikan Gurami</option>
                                <option value="Lele Sangkuriang">Ikan Lele Sangkuriang</option>
                                <option value="Lele">Ikan Lele</option>
                                <option value="Patin Siam">Ikan Patin Siam</option>
                                <option value="Patin">Ikan Patin</option>
                                <option value="Bawal Air Tawar">Ikan Bawal Air Tawar</option>
                                <option value="Bawal">Ikan Bawal</option>
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
                            <input type="number" step="0.1" min="0.1" x-model="form.biomassaEst" placeholder="Contoh: 1250"
                                   onkeydown="if(event.key === '-' || event.key === 'e' || event.key === 'E') event.preventDefault()"
                                   @input="if(form.biomassaEst !== '' && Number(form.biomassaEst) < 0) form.biomassaEst = Math.abs(Number(form.biomassaEst)) || 0.1"
                                   class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs font-semibold text-slate-700 bg-slate-50/70 focus:bg-white focus:outline-none focus:ring-2 focus:ring-sky-500 transition-all">
                        </div>
                        <div>
                            <label class="text-[10px] font-extrabold uppercase tracking-wider text-slate-500 block mb-1">TARGET PANEN (KG) *</label>
                            <input type="number" step="0.1" min="0.1" x-model="form.targetPanenKg" placeholder="Contoh: 1500"
                                   onkeydown="if(event.key === '-' || event.key === 'e' || event.key === 'E') event.preventDefault()"
                                   @input="if(form.targetPanenKg !== '' && Number(form.targetPanenKg) < 0) form.targetPanenKg = Math.abs(Number(form.targetPanenKg)) || 0.1"
                                   class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs font-semibold text-slate-700 bg-slate-50/70 focus:bg-white focus:outline-none focus:ring-2 focus:ring-sky-500 transition-all">
                        </div>
                        <div>
                            <label class="text-[10px] font-extrabold uppercase tracking-wider text-slate-500 block mb-1">TARGET FCR</label>
                            <input type="number" step="0.01" min="0.01" x-model="form.fcr" placeholder="1.15"
                                   onkeydown="if(event.key === '-' || event.key === 'e' || event.key === 'E') event.preventDefault()"
                                   @input="if(form.fcr !== '' && Number(form.fcr) < 0) form.fcr = Math.abs(Number(form.fcr)) || 1.15"
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
                    <h3 class="text-3xl font-extrabold text-slate-900 tracking-tight">{{ number_format($avgFcr ?? 0, 2) }} <span class="text-xs font-semibold text-slate-500">Ratio</span></h3>
                </div>
            </div>
            <div class="mt-4 flex items-center gap-1.5 text-xs font-semibold {{ ($avgFcr ?? 0) > 0 && ($avgFcr ?? 0) <= 1.25 ? 'text-emerald-600' : 'text-slate-500' }}">
                <i class="fa-regular {{ ($avgFcr ?? 0) > 0 && ($avgFcr ?? 0) <= 1.25 ? 'fa-circle-check text-emerald-600' : 'fa-circle-info text-slate-400' }}"></i>
                <span>{{ ($avgFcr ?? 0) > 0 ? (($avgFcr ?? 0) <= 1.25 ? 'Dalam target optimal (≤ 1.25)' : 'Perlu evaluasi pakan') : 'Belum ada data pakan' }}</span>
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
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                <div>
                    <h3 class="text-base font-bold text-slate-900 flex items-center gap-2">
                        <i class="fa-solid fa-grip text-sky-600"></i>
                        <span>Daftar Kolam &amp; Batch Pembesaran Aktif</span>
                    </h3>
                    <p class="text-xs text-slate-500 font-medium">Hanya menampilkan kolam dengan siklus pembesaran yang sedang berjalan.</p>
                </div>
                <div class="flex items-center gap-2">
                    <div class="inline-flex p-1 bg-slate-100 rounded-xl text-[11px] font-bold">
                        <button type="button" @click="activeFilter = 'aktif'"
                                :class="activeFilter === 'aktif' ? 'bg-white text-slate-900 shadow-xs' : 'text-slate-500 hover:text-slate-800'"
                                class="px-3 py-1 rounded-lg transition-all">
                            Aktif (<span x-text="batches.filter(b => b.status_siklus !== 'selesai').length"></span>)
                        </button>
                        <button type="button" @click="activeFilter = 'selesai'"
                                :class="activeFilter === 'selesai' ? 'bg-white text-slate-900 shadow-xs' : 'text-slate-500 hover:text-slate-800'"
                                class="px-3 py-1 rounded-lg transition-all">
                            Riwayat Panen (<span x-text="batches.filter(b => b.status_siklus === 'selesai').length"></span>)
                        </button>
                    </div>
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
                                    <span class="text-[10px] text-slate-400 block mt-0.5" x-text="item.jenis_ikan + ' • ' + item.tipe_kolam"></span>
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
                                     class="absolute right-0 bottom-full mb-1 w-48 rounded-xl bg-white border border-slate-200 shadow-xl py-1.5 z-50 text-left origin-bottom-right"
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

    <!-- Modal Detail Batch Pembesaran (Dengan Tabel Bibit Hatchery Asal) -->
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
             class="bg-white w-full max-w-2xl rounded-3xl shadow-2xl border border-slate-100 overflow-hidden flex flex-col max-h-[90vh]">
            
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

                        <span class="text-[10px] font-extrabold uppercase tracking-wider text-sky-800 block mb-0.5">KUALITAS AIR</span>
                        <span class="font-extrabold text-[#0B2570] text-xs block" x-text="'pH Air ' + selectedBatch?.ph_air"></span>
                        <span class="text-[10px] text-emerald-700 font-bold">Kondisi Optimal</span>
                    </div>
                </div>

                <!-- Metrik Biomassa, Target & FCR Banner -->
                <div class="p-4 bg-slate-900 text-white rounded-2xl space-y-3">
                    <div class="grid grid-cols-3 gap-2 text-center">
                        <div class="p-2 bg-white/10 rounded-xl">
                            <span class="text-[9px] uppercase tracking-wider text-slate-300 font-bold block">BIOMASSA SAAT INI</span>
                            <span class="text-base font-extrabold text-emerald-400 mt-0.5 block" x-text="selectedBatch?.biomassa_format + ' kg'"></span>
                        </div>
                        <div class="p-2 bg-white/10 rounded-xl">
                            <span class="text-[9px] uppercase tracking-wider text-slate-300 font-bold block">TARGET PANEN</span>
                            <span class="text-base font-extrabold text-sky-300 mt-0.5 block" x-text="selectedBatch?.target_format + ' kg'"></span>
                        </div>
                        <div class="p-2 bg-white/10 rounded-xl">
                            <span class="text-[9px] uppercase tracking-wider text-slate-300 font-bold block">TARGET FCR</span>
                            <span class="text-base font-extrabold text-amber-300 mt-0.5 block" x-text="selectedBatch?.fcr"></span>
                        </div>
                    </div>

                    <!-- Progress Capaian Target -->
                    <div>
                        <div class="flex items-center justify-between text-[11px] font-bold mb-1">
                            <span class="text-slate-300">Capaian Target Panen</span>
                            <span class="text-emerald-400" x-text="selectedBatch?.target_percent + '%'"></span>
                        </div>
                        <div class="w-full bg-white/20 h-2 rounded-full overflow-hidden">
                            <div class="bg-gradient-to-r from-sky-400 to-emerald-400 h-full rounded-full transition-all"
                                 :style="'width: ' + selectedBatch?.target_percent + '%'"></div>
                        </div>
                    </div>
                </div>

                <!-- TABEL RINCIAN MASING-MASING BIBIT DARI BATCH PEMBIBITAN -->
                <div class="bg-white rounded-2xl border border-slate-200/90 overflow-hidden shadow-xs space-y-0">
                    <div class="p-3.5 bg-slate-50 border-b border-slate-200/80 flex items-center justify-between">
                        <div class="flex items-center gap-2 font-bold text-slate-800 text-xs">
                            <i class="fa-solid fa-table-list text-sky-600"></i>
                            <span>Daftar Benih dari Batch Pembibitan Asal (Hatchery)</span>
                        </div>
                        <span class="text-[10px] font-extrabold px-2.5 py-0.5 rounded-md bg-sky-100 text-sky-800"
                              x-text="(selectedBatch?.bibit_list ? selectedBatch.bibit_list.length : 1) + ' Sumber Bibit'"></span>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-xs">
                            <thead class="bg-slate-50/80 text-[10px] uppercase font-extrabold text-slate-400 border-b border-slate-100">
                                <tr>
                                    <th class="py-2.5 px-4">Batch Asal</th>
                                    <th class="py-2.5 px-4">Kolam Asal</th>
                                    <th class="py-2.5 px-4">Komoditas &amp; Fase</th>
                                    <th class="py-2.5 px-4">Populasi Tebar</th>
                                    <th class="py-2.5 px-4">Bobot Awal</th>
                                    <th class="py-2.5 px-4 text-right">Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 font-medium text-slate-700">
                                <template x-for="(bibit, bIdx) in (selectedBatch?.bibit_list || [])" :key="bIdx">
                                    <tr class="hover:bg-slate-50/60 transition-colors">
                                        <td class="py-3 px-4">
                                            <span class="font-extrabold text-[#0B2570] block text-xs" x-text="bibit.id_batch"></span>
                                            <span class="text-[10px] text-slate-400 block" x-text="bibit.tgl_pemijahan"></span>
                                        </td>
                                        <td class="py-3 px-4">
                                            <span class="font-bold text-slate-800 block" x-text="bibit.kolam_asal"></span>
                                            <span class="text-[10px] text-slate-400 block" x-text="bibit.tipe_kolam_asal"></span>
                                        </td>
                                        <td class="py-3 px-4">
                                            <span class="font-extrabold text-slate-900 block" x-text="bibit.jenis_ikan"></span>
                                            <span class="inline-block mt-0.5 px-2 py-0.5 rounded-md text-[9px] font-extrabold bg-sky-50 text-sky-700 border border-sky-200" x-text="bibit.fase"></span>
                                        </td>
                                        <td class="py-3 px-4 font-bold text-slate-900">
                                            <span x-text="bibit.jumlah_bibit"></span> <span class="text-[10px] font-normal text-slate-400">Ekor</span>
                                        </td>
                                        <td class="py-3 px-4">
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-lg text-xs font-extrabold bg-emerald-50 text-emerald-800 border border-emerald-200" x-text="bibit.total_bobot_kg + ' kg'"></span>
                                        </td>
                                        <td class="py-3 px-4 text-right">
                                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-extrabold bg-emerald-100 text-emerald-800">
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
         class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 backdrop-blur-xs p-4"
         style="display: none;">
        
        <div @click.outside="harvestConfirmModalOpen = false" 
             x-show="harvestConfirmModalOpen"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 scale-95 translate-y-3"
             x-transition:enter-end="opacity-100 scale-100 translate-y-0"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100 scale-100 translate-y-0"
             x-transition:leave-end="opacity-0 scale-95 translate-y-3"
             class="bg-white w-full max-w-md rounded-3xl shadow-2xl border border-slate-100 p-6 space-y-4 text-center">
            
            <div class="w-16 h-16 rounded-2xl bg-emerald-50 text-emerald-600 border border-emerald-100 mx-auto flex items-center justify-center text-2xl shadow-xs">
                <i class="fa-solid fa-boxes-packing"></i>
            </div>

            <div class="space-y-1.5">
                <span class="px-3 py-1 rounded-full text-[10px] font-extrabold uppercase bg-emerald-100 text-emerald-800 inline-block">
                    Verifikasi Selesai Panen
                </span>
                <h3 class="text-lg font-extrabold text-slate-900">Selesaikan Panen &amp; Kosongkan Kolam?</h3>
                <p class="text-xs text-slate-600 font-medium leading-relaxed">
                    Selesaikan siklus panen batch <strong class="text-slate-900" x-text="selectedBatchToHarvest?.id"></strong> di <strong class="text-slate-900" x-text="selectedBatchToHarvest?.nama_kolam"></strong>.
                </p>
            </div>

            <!-- Input Realisasi Berat Panen (KG) -->
            <div class="text-left bg-slate-50 p-4 rounded-2xl border border-slate-200 space-y-2">
                <label class="text-[10px] font-extrabold uppercase tracking-wider text-slate-700 block">TOTAL BERAT HASIL PANEN (KG) *</label>
                <input type="number" step="0.1" min="0.1" x-model="harvestForm.jumlah_panen_kg"
                       onkeydown="if(event.key === '-' || event.key === 'e' || event.key === 'E') event.preventDefault()"
                       @input="if(harvestForm.jumlah_panen_kg !== '' && Number(harvestForm.jumlah_panen_kg) < 0) harvestForm.jumlah_panen_kg = Math.abs(Number(harvestForm.jumlah_panen_kg)) || 0.1"
                       placeholder="Contoh: 500"
                       class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm font-extrabold text-emerald-700 bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500">
                <div class="flex items-center justify-between text-[10px] text-slate-500 pt-1">
                    <span>Target: <strong x-text="selectedBatchToHarvest?.target_format + ' kg'"></strong></span>
                    <span>Biomassa: <strong x-text="selectedBatchToHarvest?.biomassa_format + ' kg'"></strong></span>
                </div>
            </div>

            <div class="p-3 bg-amber-50 rounded-xl border border-amber-200/70 text-left text-xs text-amber-900 flex items-start gap-2.5">
                <i class="fa-solid fa-circle-info text-amber-600 text-sm mt-0.5 shrink-0"></i>
                <p class="text-[11px] leading-relaxed">
                    Setelah berstatus <strong>Selesai Panen</strong>, batch diarsipkan ke riwayat panen dan kolam <strong x-text="selectedBatchToHarvest?.nama_kolam"></strong> otomatis <strong>dikosongkan / tersedia</strong> kembali.
                </p>
            </div>

            <div class="grid grid-cols-2 gap-3 pt-1">
                <button type="button" @click="harvestConfirmModalOpen = false" 
                        class="w-full py-2.5 rounded-xl border border-slate-200 text-slate-700 hover:bg-slate-50 font-bold text-xs transition-colors">
                    Batalkan
                </button>
                <button type="button" @click="executeFinishHarvest()" :disabled="isSubmitting"
                        class="w-full py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 active:scale-[0.99] text-white font-bold text-xs shadow-md shadow-emerald-950/20 transition-all flex items-center justify-center gap-2 disabled:opacity-60">
                    <i class="fa-solid" :class="isSubmitting ? 'fa-spinner fa-spin' : 'fa-check'"></i>
                    <span>Ya, Selesaikan Panen</span>
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
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs"
         style="display: none;">
        
        <div class="bg-white rounded-3xl max-w-lg w-full p-6 shadow-2xl space-y-5 border border-slate-200" @click.outside="kolamModalOpen = false">
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
                        <input type="number" step="50" min="10" x-model="kolamForm.kapasitas" required placeholder="2500"
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
            kolam: '',
            tglTebar: new Date().toISOString().split('T')[0],
            estTglPanen: new Date(Date.now() + 90 * 24 * 60 * 60 * 1000).toISOString().split('T')[0],
            jenisIkan: '',
            biomassaEst: 1200,
            targetPanenKg: 1500,
            fcr: 1.15,
            statusSiklus: 'berjalan'
        },

        availablePembibitan: {!! json_encode($availablePembibitan ?? []) !!},
        kolamList: {!! json_encode($kolamList ?? []) !!},
        batches: {!! json_encode($batches ?? []) !!},
        activeFilter: 'aktif',

        get filteredBatches() {
            if (this.activeFilter === 'selesai') {
                return this.batches.filter(b => b.status_siklus === 'selesai');
            }
            // Default: hanya tampilkan batch aktif yang sedang berjalan / siap panen
            return this.batches.filter(b => b.status_siklus !== 'selesai');
        },

        detailModalOpen: false,
        deleteModalOpen: false,
        selectedBatchToDelete: null,
        harvestConfirmModalOpen: false,
        selectedBatchToHarvest: null,
        harvestForm: {
            jumlah_panen_kg: 0
        },
        kolamModalOpen: false,
        isSubmittingKolam: false,
        kolamForm: {
            nama_kolam: '',
            tipe_kolam: 'Kolam Pembesaran (Beton)',
            kapasitas: 2500,
            kesehatan_ph_air: 7.2
        },
        showToast: false,
        toastMessage: '',

        openKolamModal() {
            this.kolamForm = {
                nama_kolam: 'Kolam Pembesaran ' + String.fromCharCode(65 + Math.floor(Math.random() * 6)) + '-0' + (this.kolamList.length + 1),
                tipe_kolam: 'Kolam Pembesaran (Beton)',
                kapasitas: 2500,
                kesehatan_ph_air: 7.2
            };
            this.kolamModalOpen = true;
        },

        async submitKolam() {
            if (!this.kolamForm.nama_kolam || !this.kolamForm.kapasitas) {
                alert('Nama kolam dan kapasitas wajib diisi!');
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
                        kapasitas: Number(this.kolamForm.kapasitas),
                        kesehatan_ph_air: Number(this.kolamForm.kesehatan_ph_air || 7.2)
                    })
                });

                const data = await res.json();
                if (res.ok && data.success) {
                    this.kolamList.push(data.kolam);
                    if (this.showForm) {
                        this.form.kolam = data.kolam.nama_kolam;
                    }
                    this.kolamModalOpen = false;
                    this.toastMessage = data.message || 'Kolam baru berhasil ditambahkan!';
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

        onPembibitanChange() {
            if (!this.form.id_batch_pembibitan) return;
            const sel = this.availablePembibitan.find(b => b.id_batch == this.form.id_batch_pembibitan);
            if (sel) {
                let clean = sel.jenis_ikan.replace(/^Ikan\s+/i, '');
                this.form.jenisIkan = clean;
                if (sel.est_biomassa && sel.est_biomassa > 0) {
                    this.form.biomassaEst = Math.max(10, sel.est_biomassa);
                }
                this.form.targetPanenKg = Math.round(this.form.biomassaEst * 1.5);
            }
        },

        calculateEstPanen() {
            if (!this.form.tglTebar) return;
            const tgl = new Date(this.form.tglTebar);
            tgl.setDate(tgl.getDate() + 90);
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
            this.form = {
                id: item.id,
                id_pembesaran: item.id_pembesaran,
                id_batch_pembibitan: item.id_batch_pembibitan || '',
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
                kolam: '',
                tglTebar: today,
                estTglPanen: defaultEst,
                jenisIkan: '',
                biomassaEst: 1200,
                targetPanenKg: 1500,
                fcr: 1.15,
                statusSiklus: 'berjalan'
            };
        },

        triggerFinishHarvest(item) {
            this.selectedBatchToHarvest = item;
            this.harvestForm.jumlah_panen_kg = item.target_panen_kg || item.biomassa_est || 100;
            this.harvestConfirmModalOpen = true;
        },

        async executeFinishHarvest() {
            if (!this.selectedBatchToHarvest) return;
            const item = this.selectedBatchToHarvest;
            const idPB = item.id_pembesaran || item.id.replace(/[^0-9]/g, '');
            const jumlahPanen = Number(this.harvestForm.jumlah_panen_kg) || item.target_panen_kg || item.biomassa_est;

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
                        jumlah_panen_kg: jumlahPanen
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
                    this.toastMessage = "Sukses! Panen batch " + item.id + " (" + jumlahPanen.toLocaleString('id-ID') + " kg) selesai dicatat & kolam " + item.nama_kolam + " telah siap digunakan kembali.";
                    this.showToast = true;
                    setTimeout(() => { this.showToast = false; }, 5000);
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
            const fcrNum = Math.abs(Number(this.form.fcr || 1.15));
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
                    this.batches.unshift({
                        id_pembesaran: newBatch.id_pembesaran,
                        id: '#PB-' + String(newBatch.id_pembesaran).padStart(5, '0'),
                        id_kolam: newBatch.id_kolam,
                        nama_kolam: newBatch.kolam ? newBatch.kolam.nama_kolam : this.form.kolam,
                        tipe_kolam: newBatch.kolam ? newBatch.kolam.tipe_kolam : 'Pembesaran',
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

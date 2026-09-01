@extends('layouts.app')

@section('title', 'Manajemen Pembibitan - SIM-BUDIDAYA')

@section('content')
<div class="space-y-6" x-data="pembibitanComponent()">

    <!-- Header & Action -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">Manajemen Pembibitan</h1>
            <p class="text-xs sm:text-sm text-slate-500 font-medium mt-1">Data real-time fase pembibitan, populasi benih, dan status batch hatchery.</p>
        </div>
        <div>
            <button @click="showForm ? showForm = false : openCreateForm()"
                    class="px-5 py-2.5 rounded-xl bg-[#031B4E] hover:bg-sky-900 text-white font-bold text-xs sm:text-sm shadow-md shadow-sky-950/20 transition-all flex items-center justify-center gap-2">
                <i class="fa-solid" :class="showForm ? 'fa-table-list' : 'fa-circle-plus'" class="text-sm"></i>
                <span x-text="showForm ? 'Lihat Data Batch' : 'Input Batch Baru'"></span>
            </button>
        </div>
    </div>

    <!-- ========= INPUT FORM SECTION ========= -->
    <div x-show="showForm"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 -translate-y-2"
         x-transition:enter-end="opacity-100 translate-y-0"
         class="space-y-4">

        <div class="bg-[#051B44] rounded-2xl p-6 sm:p-8 text-white shadow-xs">
            <h2 class="text-xl font-extrabold text-white">Formulir Pembibitan</h2>
            <p class="text-xs text-sky-200/80 font-medium mt-1">Catat data penjadian dan pembuatan batch baru. Pastikan semua parameter lingkungan sesuai standar operasional.</p>
        </div>

        <div class="bg-white p-6 sm:p-8 rounded-2xl border border-slate-200/80 shadow-xs space-y-6">

            <!-- Section 1: Data Utama Batch & Fase Pertumbuhan -->
            <div class="space-y-4">
                <div class="flex items-center gap-2.5 text-sm font-bold text-slate-900 pb-2 border-b border-slate-100">
                    <div class="w-8 h-8 rounded-xl bg-[#051B44] text-white flex items-center justify-center">
                        <i class="fa-solid fa-fish text-xs"></i>
                    </div>
                    <span>Data Utama Batch &amp; Fase Pertumbuhan</span>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                    <div>
                        <label class="text-[10px] font-extrabold uppercase tracking-wider text-slate-500 block mb-1">ID BATCH (OTOMATIS)</label>
                        <input type="text" x-model="form.id" readonly
                               class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs font-extrabold text-slate-500 bg-slate-100 cursor-not-allowed">
                    </div>
                    <div>
                        <label class="text-[10px] font-extrabold uppercase tracking-wider text-slate-500 block mb-1">JENIS IKAN *</label>
                        <select x-model="form.jenisIkan" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs font-semibold text-slate-700 bg-slate-50/70 focus:bg-white focus:outline-none focus:ring-2 focus:ring-sky-500 transition-all">
                            <option value="">Pilih Spesies...</option>
                            <option value="Gurami">Gurami</option>
                            <option value="Lele">Lele</option>
                            <option value="Nila">Nila</option>
                            <option value="Patin">Patin</option>
                            <option value="Bawal">Bawal</option>
                        </select>
                    </div>
                    <div>
                        <label class="text-[10px] font-extrabold uppercase tracking-wider text-slate-500 block mb-1">FASE PERTUMBUHAN *</label>
                        <select x-model="form.fase_pertumbuhan" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs font-bold text-sky-800 bg-sky-50/60 focus:bg-white focus:outline-none focus:ring-2 focus:ring-sky-500 transition-all">
                            <option value="TELUR">TELUR (Masa Pemijahan &amp; Penetasan Awal)</option>
                            <option value="LARVA">LARVA (Benih Kecil / Post-Larva)</option>
                            <option value="FINGERLING">FINGERLING (Benih Siap Pembesaran)</option>
                        </select>
                    </div>
                    <div>
                        <label class="text-[10px] font-extrabold uppercase tracking-wider text-slate-500 block mb-1">JUMLAH BIBIT / TELUR AWAL *</label>
                        <div class="flex items-center gap-2">
                            <input type="number" x-model="form.jumlahBibitAwal" min="100" step="500" placeholder="Contoh: 250000"
                                   class="flex-1 px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs font-semibold text-slate-700 bg-slate-50/70 focus:bg-white focus:outline-none focus:ring-2 focus:ring-sky-500 transition-all">
                            <span class="text-xs font-bold text-slate-400">ekor/btr</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Section 2: Timeline, Mortalitas & Bobot Biomassa -->
            <div class="space-y-4">
                <div class="flex items-center gap-2.5 text-sm font-bold text-slate-900 pb-2 border-b border-slate-100">
                    <div class="w-8 h-8 rounded-xl bg-[#10B981] text-white flex items-center justify-center">
                        <i class="fa-regular fa-calendar text-xs"></i>
                    </div>
                    <span>Timeline Tanggal, Mortalitas &amp; Estimasi Bobot</span>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                    <div>
                        <label class="text-[10px] font-extrabold uppercase tracking-wider text-slate-500 block mb-1">TANGGAL PEMIJAHAN / TEBAR AWAL *</label>
                        <input type="date" x-model="form.tglPemijahan"
                               class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs font-semibold text-slate-700 bg-slate-50/70 focus:bg-white focus:outline-none focus:ring-2 focus:ring-sky-500 transition-all">
                    </div>
                    <div>
                        <label class="text-[10px] font-extrabold uppercase tracking-wider text-slate-500 block mb-1">PREDIKSI PENETASAN (HARI)</label>
                        <div class="flex items-center gap-2">
                            <button type="button" @click="form.prediksiHari = Math.max(1, Number(form.prediksiHari) - 1)" class="w-9 h-9 rounded-xl border border-slate-200 bg-white text-slate-500 hover:bg-slate-50 flex items-center justify-center font-bold text-sm transition-colors">−</button>
                            <input type="number" x-model="form.prediksiHari" min="1" max="30"
                                   class="flex-1 px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs font-extrabold text-center text-slate-700 bg-slate-50/70 focus:bg-white focus:outline-none focus:ring-2 focus:ring-sky-500 transition-all">
                            <button type="button" @click="form.prediksiHari = Number(form.prediksiHari) + 1" class="w-9 h-9 rounded-xl border border-slate-200 bg-white text-slate-500 hover:bg-slate-50 flex items-center justify-center font-bold text-sm transition-colors">+</button>
                        </div>
                    </div>
                    <div>
                        <label class="text-[10px] font-extrabold uppercase tracking-wider text-slate-500 block mb-1">JUMLAH KEMATIAN</label>
                        <div class="flex items-center gap-2">
                            <input type="number" x-model="form.jumlahKematian" min="0" placeholder="0"
                                   class="flex-1 px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs font-semibold text-slate-700 bg-slate-50/70 focus:bg-white focus:outline-none focus:ring-2 focus:ring-sky-500 transition-all">
                            <span class="text-xs font-bold text-slate-400">ekor</span>
                        </div>
                    </div>
                    <div>
                        <label class="text-[10px] font-extrabold uppercase tracking-wider text-slate-500 block mb-1">TOTAL BOBOT / BIOMASSA (KG) *</label>
                        <div class="flex items-center gap-2">
                            <input type="number" step="0.1" min="0.1" x-model="form.totalBobotKg" placeholder="Contoh: 25.0"
                                   class="flex-1 px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs font-bold text-emerald-700 bg-slate-50/70 focus:bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500 transition-all">
                            <span class="text-xs font-extrabold text-emerald-700">Kg</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Section 3: Status & Lokasi Kolam Hatchery -->
            <div class="space-y-4">
                <div class="flex items-center gap-2.5 text-sm font-bold text-slate-900 pb-2 border-b border-slate-100">
                    <div class="w-8 h-8 rounded-xl bg-[#0284C7] text-white flex items-center justify-center">
                        <i class="fa-solid fa-location-dot text-xs"></i>
                    </div>
                    <span>Status Siklus &amp; Lokasi Kolam Hatchery</span>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="text-[10px] font-extrabold uppercase tracking-wider text-slate-500 block mb-1.5">STATUS BATCH</label>
                        <div class="flex flex-wrap items-center gap-2">
                            <button type="button" @click="form.statusBatch = 'inkubasi'"
                                    :class="form.statusBatch === 'inkubasi' ? 'bg-[#051B44] text-white border-transparent shadow-xs' : 'bg-white text-slate-600 border-slate-200 hover:bg-slate-50'"
                                    class="px-3 py-1.5 rounded-xl border text-xs font-bold transition-all">
                                Proses Inkubasi
                            </button>
                            <button type="button" @click="form.statusBatch = 'menetas'"
                                    :class="form.statusBatch === 'menetas' ? 'bg-[#0284C7] text-white border-transparent shadow-xs' : 'bg-white text-slate-600 border-slate-200 hover:bg-slate-50'"
                                    class="px-3 py-1.5 rounded-xl border text-xs font-bold transition-all">
                                Mulai Menetas
                            </button>
                            <button type="button" @click="form.statusBatch = 'aktif'"
                                    :class="form.statusBatch === 'aktif' ? 'bg-emerald-600 text-white border-transparent shadow-xs' : 'bg-white text-slate-600 border-slate-200 hover:bg-slate-50'"
                                    class="px-3 py-1.5 rounded-xl border text-xs font-bold transition-all">
                                Aktif
                            </button>
                            <button type="button" @click="form.statusBatch = 'siap_pindah'"
                                    :class="form.statusBatch === 'siap_pindah' ? 'bg-teal-600 text-white border-transparent shadow-xs' : 'bg-white text-slate-600 border-slate-200 hover:bg-slate-50'"
                                    class="px-3 py-1.5 rounded-xl border text-xs font-bold transition-all">
                                Siap Pindah
                            </button>
                            <button type="button" @click="form.statusBatch = 'gagal'"
                                    :class="form.statusBatch === 'gagal' ? 'bg-rose-600 text-white border-transparent shadow-xs' : 'bg-white text-slate-600 border-slate-200 hover:bg-slate-50'"
                                    class="px-3 py-1.5 rounded-xl border text-xs font-bold transition-all">
                                Gagal
                            </button>
                        </div>
                    </div>

                    <div>
                        <label class="text-[10px] font-extrabold uppercase tracking-wider text-slate-500 block mb-1">LOKASI KOLAM HATCHERY / PEMBIBITAN *</label>
                        <select x-model="form.kolam" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs font-semibold text-slate-700 bg-slate-50/70 focus:bg-white focus:outline-none focus:ring-2 focus:ring-sky-500 transition-all">
                            <option value="">Pilih Kolam Pembibitan...</option>
                            @if(isset($kolams) && count($kolams) > 0)
                                @foreach($kolams as $k)
                                    <option value="{{ $k->nama_kolam }}">{{ $k->nama_kolam }} ({{ $k->tipe_kolam ?? 'Hatchery' }} - Kapasitas: {{ number_format($k->kapasitas, 0, ',', '.') }} Ekor)</option>
                                @endforeach
                            @else
                                <option value="Kolam Pemijahan A-01">Kolam Pemijahan A-01 (Hatchery / Pemijahan - Kapasitas: 10.000 Ekor)</option>
                                <option value="Kolam Penetasan B-02">Kolam Penetasan B-02 (Hatchery / Penetasan - Kapasitas: 15.000 Ekor)</option>
                                <option value="Kolam Pembibitan L-03">Kolam Pembibitan L-03 (Hatchery / Pendederan - Kapasitas: 20.000 Ekor)</option>
                            @endif
                        </select>
                        <p class="text-[10px] text-slate-400 mt-1 italic">Khusus fasilitas Hatchery Pembibitan: Kolam Pemijahan, Penetasan, atau Pendederan.</p>
                    </div>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
                <button type="button" @click="showForm = false; formMode = 'create'; resetForm()"
                        class="px-5 py-2.5 rounded-xl border border-slate-200 text-xs font-bold text-slate-600 hover:bg-slate-50 transition-colors">
                    Batal
                </button>
                <button type="button" @click="resetForm()"
                        class="px-5 py-2.5 rounded-xl border border-slate-200 text-xs font-bold text-slate-600 hover:bg-slate-50 transition-colors">
                    Reset Form
                </button>
                <button type="button" @click="submitBatch()" :disabled="isSubmitting"
                        class="px-6 py-2.5 rounded-xl bg-[#031B4E] hover:bg-navy-900 text-white font-bold text-xs shadow-md shadow-sky-950/20 transition-all flex items-center gap-2 disabled:opacity-60">
                    <i class="fa-solid" :class="isSubmitting ? 'fa-spinner fa-spin' : 'fa-floppy-disk'" class="text-xs"></i>
                    <span x-text="isSubmitting ? 'MENYIMPAN...' : (formMode === 'edit' ? 'SIMPAN PERUBAHAN' : 'SIMPAN DATA BATCH')"></span>
                </button>
            </div>

        </div>
    </div>

    <!-- ========= DIRECTORY / LIST MODE ========= -->

    <!-- 4 Metric KPI Cards Grid -->
    <div x-show="!showForm" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
        
        <!-- Card 1: Total Benih Aktif -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between">
                    <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400">TOTAL BENIH AKTIF</span>
                    <div class="w-9 h-9 rounded-xl bg-sky-50 text-sky-600 flex items-center justify-center">
                        <i class="fa-solid fa-users text-sm"></i>
                    </div>
                </div>
                <div class="mt-3">
                    <h3 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">{{ $kpis['totalBenih'] ?? '1,225,300' }}</h3>
                </div>
            </div>
            <div class="mt-4 flex items-center gap-1.5 text-xs font-bold text-emerald-600">
                <i class="fa-solid fa-arrow-trend-up"></i>
                <span>+12.5% bln ini</span>
            </div>
        </div>

        <!-- Card 2: Survival Rate (SR) Rata-rata -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between">
                    <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400">SURVIVAL RATE (SR)</span>
                    <div class="w-9 h-9 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center">
                        <i class="fa-regular fa-heart text-sm"></i>
                    </div>
                </div>
                <div class="mt-3">
                    <h3 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">{{ $kpis['srRate'] ?? '98.8' }}%</h3>
                </div>
            </div>
            <div class="mt-4">
                <div class="w-full bg-slate-100 h-2 rounded-full overflow-hidden">
                    <div class="bg-emerald-500 h-full rounded-full" style="width: {{ $kpis['srRate'] ?? '98.8' }}%"></div>
                </div>
            </div>
        </div>

        <!-- Card 3: Kapasitas Bak Terpakai -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between">
                    <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400">KAPASITAS BAK</span>
                    <div class="w-9 h-9 rounded-xl bg-sky-50 text-sky-600 flex items-center justify-center">
                        <i class="fa-regular fa-clipboard text-sm"></i>
                    </div>
                </div>
                <div class="mt-3 flex items-baseline gap-1.5">
                    <h3 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">{{ $kpis['bakTerpakai'] ?? 4 }}</h3>
                    <span class="text-xs font-extrabold text-slate-500">/ {{ $kpis['totalBak'] ?? 12 }} Bak</span>
                </div>
            </div>
            <div class="mt-4 text-xs font-semibold text-slate-500">
                {{ $kpis['bakTersedia'] ?? 8 }} Bak tersedia (Siap Pakai)
            </div>
        </div>

        <!-- Card 4: Kualitas Air Hatchery Widget -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between">
                    <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400">KUALITAS AIR HATCHERY</span>
                    <div class="w-9 h-9 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center">
                        <i class="fa-solid fa-droplet text-sm"></i>
                    </div>
                </div>
                <div class="mt-3 flex items-baseline gap-2">
                    <h3 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">{{ $kpis['avgPh'] ?? '7.2' }}</h3>
                    <span class="text-xs font-bold text-emerald-600">pH Normal</span>
                </div>
            </div>
            <div class="mt-4 text-xs font-semibold text-slate-500 flex items-center justify-between">
                <span>Suhu Rata-rata</span>
                <span class="font-extrabold text-slate-800">{{ $kpis['suhu'] ?? '28°C' }}</span>
            </div>
        </div>

    </div>

    <!-- Table Section: Batch Hatchery Aktif -->
    <div x-show="!showForm" class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-visible">
        <div class="p-5 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div>
                <h3 class="text-base font-extrabold text-slate-900" x-text="activeFilter === 'aktif' ? 'Batch Hatchery Aktif' : 'Batch Telah Dipindahkan ke Pembesaran'"></h3>
                <p class="text-xs text-slate-500 font-medium" x-text="activeFilter === 'aktif' ? 'Daftar kelompok benih yang sedang dalam masa pemijahan dan penetasan aktif.' : 'Daftar kelompok bibit yang telah selesai dibudidayakan di hatchery dan dialihkan ke pembesaran (View Only).'"></p>
            </div>
            <div class="flex items-center gap-2">
                <div class="inline-flex p-1 bg-slate-100 rounded-xl text-[11px] font-bold">
                    <button type="button" @click="activeFilter = 'aktif'"
                            :class="activeFilter === 'aktif' ? 'bg-white text-slate-900 shadow-xs' : 'text-slate-500 hover:text-slate-800'"
                            class="px-3 py-1.5 rounded-lg transition-all">
                        Batch Aktif (<span x-text="batches.filter(b => b.status !== 'selesai' && b.status !== 'gagal').length"></span>)
                    </button>
                    <button type="button" @click="activeFilter = 'selesai'"
                            :class="activeFilter === 'selesai' ? 'bg-white text-slate-900 shadow-xs' : 'text-slate-500 hover:text-slate-800'"
                            class="px-3 py-1.5 rounded-lg transition-all">
                        Dipindahkan (<span x-text="batches.filter(b => b.status === 'selesai').length"></span>)
                    </button>
                </div>
            </div>
        </div>

        <div class="overflow-x-auto overflow-y-visible">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50/70 border-b border-slate-200/80 text-[10px] font-extrabold text-slate-400 uppercase tracking-wider">
                        <th class="py-4 px-6">BATCH ID</th>
                        <th class="py-4 px-6">FASE PERTUMBUHAN</th>
                        <th class="py-4 px-6">USIA (HARI)</th>
                        <th class="py-4 px-6">JUMLAH (EKOR)</th>
                        <th class="py-4 px-6">TOTAL BOBOT (KG)</th>
                        <th class="py-4 px-6">JENIS IKAN</th>
                        <th class="py-4 px-6">STATUS BATCH</th>
                        <th class="py-4 px-6 text-right">AKSI</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-xs font-medium text-slate-700">
                    
                    <template x-for="item in filteredBatches" :key="item.id">
                        <tr class="hover:bg-slate-50/70 transition-colors">
                            <td class="py-4 px-6">
                                <span class="font-extrabold text-[#0055CC] block cursor-pointer hover:underline" @click="openDetail(item)" x-text="item.id"></span>
                                <span class="text-[10px] text-slate-400 font-normal" x-text="'Input: ' + item.inputDate"></span>
                            </td>
                            <td class="py-4 px-6">
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-extrabold uppercase" :class="item.faseClass" x-text="item.fase"></span>
                            </td>
                            <td class="py-4 px-6 text-slate-700 font-bold" x-text="item.usia"></td>
                            <td class="py-4 px-6 text-slate-900 font-extrabold" x-text="item.jumlah"></td>
                            <td class="py-4 px-6">
                                <span class="inline-flex items-center gap-1 bg-emerald-50 text-emerald-800 px-2.5 py-1 rounded-lg text-xs font-extrabold border border-emerald-200/60" x-text="item.totalBobotFormat || (item.totalBobotKg + ' kg')"></span>
                            </td>
                            <td class="py-4 px-6 font-extrabold text-slate-800" x-text="item.jenisIkan"></td>
                            <td class="py-4 px-6">
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-extrabold" :class="item.statusClass">
                                    <span class="w-1.5 h-1.5 rounded-full" :class="item.dotClass"></span>
                                    <span x-text="item.statusLabel || item.status"></span>
                                </span>
                            </td>
                            <td class="py-4 px-6 text-right">
                                <div class="relative inline-block text-left" x-data="{ open: false }">
                                    <button @click="open = !open" @click.away="open = false" 
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
                                         class="absolute right-0 top-full mt-1 w-48 rounded-xl bg-white border border-slate-200 shadow-xl py-1.5 z-50 text-left origin-top-right">
                                        
                                        <!-- Opsi 1: Detail Batch (Selalu Ada) -->
                                        <button @click="open = false; openDetail(item)" class="w-full px-3.5 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-50 flex items-center gap-2.5">
                                            <i class="fa-solid fa-eye text-sky-600 w-4"></i>
                                            <span>Detail Batch</span>
                                        </button>

                                        <!-- JIKA BATCH STATUS SELESAI (DIPINDAHKAN): CUMA VIEW DOANG & LIHAT DETAIL DI PEMBESARAN -->
                                        <template x-if="item.status === 'selesai'">
                                            <a href="{{ route('pembesaran') }}" class="w-full px-3.5 py-2 text-xs font-semibold text-emerald-700 hover:bg-emerald-50 flex items-center gap-2.5">
                                                <i class="fa-solid fa-arrow-up-right-from-square text-emerald-600 w-4"></i>
                                                <span>Lihat di Pembesaran</span>
                                            </a>
                                        </template>

                                        <!-- JIKA BATCH MASIH AKTIF: PINDAH KE PEMBESARAN, EDIT, DAN HAPUS -->
                                        <template x-if="item.status !== 'selesai'">
                                            <div>
                                                <button @click="open = false; openTransferModal(item)" class="w-full px-3.5 py-2 text-xs font-semibold text-emerald-700 hover:bg-emerald-50 flex items-center gap-2.5">
                                                    <i class="fa-solid fa-right-left text-emerald-600 w-4"></i>
                                                    <span>Pindah ke Pembesaran</span>
                                                </button>

                                                <button @click="open = false; openEdit(item)" class="w-full px-3.5 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-50 flex items-center gap-2.5">
                                                    <i class="fa-solid fa-pen-to-square text-amber-600 w-4"></i>
                                                    <span>Edit Batch</span>
                                                </button>

                                                <div class="my-1 border-t border-slate-100"></div>

                                                <button @click="open = false; deleteBatch(item)" class="w-full px-3.5 py-2 text-xs font-semibold text-red-600 hover:bg-red-50 flex items-center gap-2.5">
                                                    <i class="fa-solid fa-trash-can text-red-500 w-4"></i>
                                                    <span>Hapus Batch</span>
                                                </button>
                                            </div>
                                        </template>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    </template>

                    <!-- Empty state jika tidak ada baris data -->
                    <template x-if="filteredBatches.length === 0">
                        <tr>
                            <td colspan="8" class="py-8 text-center text-slate-400">
                                <i class="fa-solid fa-fish text-2xl mb-2 block text-slate-300"></i>
                                <span x-text="activeFilter === 'aktif' ? 'Tidak ada batch pembibitan aktif saat ini. Semua telah dipindahkan atau belum ada batch baru.' : 'Belum ada data batch yang dipindahkan.'"></span>
                            </td>
                        </tr>
                    </template>

                </tbody>
            </table>
        </div>

        <!-- Table Footer Pagination -->
        <div class="p-4 border-t border-slate-100 flex flex-col sm:flex-row items-center justify-between gap-4 text-xs font-semibold text-slate-500">
            <span x-text="'Menampilkan ' + filteredBatches.length + ' dari ' + batches.length + ' Batch Total'"></span>
            <div class="flex items-center gap-1">
                <button class="w-7 h-7 rounded-lg border border-slate-200 flex items-center justify-center text-slate-400 hover:bg-slate-50">&lt;</button>
                <button class="w-7 h-7 rounded-lg bg-[#031B4E] text-white font-bold flex items-center justify-center">1</button>
                <button class="w-7 h-7 rounded-lg border border-slate-200 flex items-center justify-center text-slate-400 hover:bg-slate-50">&gt;</button>
            </div>
        </div>
    </div>

    <!-- Modal Detail Batch Hatchery -->
    <div x-show="detailModalOpen" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-95"
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm"
         style="display: none;">
        
        <div class="bg-white rounded-2xl max-w-lg w-full p-6 shadow-2xl space-y-5 border border-slate-200" x-show="selectedBatch">
            
            <!-- Modal Header -->
            <div class="flex items-center justify-between border-b border-slate-100 pb-4">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-sky-100 text-sky-700 flex items-center justify-center font-bold text-sm">
                        <i class="fa-solid fa-fish"></i>
                    </div>
                    <div>
                        <span class="text-[10px] font-extrabold uppercase text-slate-400 tracking-wider block">Rincian Data Batch</span>
                        <h3 class="text-lg font-extrabold text-slate-900" x-text="selectedBatch?.id"></h3>
                    </div>
                </div>
                <button @click="detailModalOpen = false" class="w-8 h-8 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-500 flex items-center justify-center transition-colors">
                    <i class="fa-solid fa-xmark text-base"></i>
                </button>
            </div>

            <!-- Banner info jika sudah dipindahkan -->
            <template x-if="selectedBatch?.status === 'selesai'">
                <div class="p-3.5 bg-emerald-50/80 border border-emerald-200/80 rounded-xl space-y-1 text-xs">
                    <div class="flex items-center gap-2 font-bold text-emerald-800">
                        <i class="fa-solid fa-circle-check text-emerald-600"></i>
                        <span>Batch Telah Dipindahkan ke Pembesaran</span>
                    </div>
                    <p class="text-[11px] text-emerald-700">
                        Siklus pembibitan telah selesai dan bibit telah dipindahkan ke kolam pembesaran. Batch ini bersifat arsip (view-only).
                    </p>
                    <template x-if="selectedBatch?.kolam_pembesaran">
                        <div class="pt-1 text-[11px] text-emerald-900 font-semibold flex items-center gap-1.5">
                            <i class="fa-solid fa-location-dot text-emerald-600"></i>
                            <span>Kolam Tujuan: <strong x-text="selectedBatch?.kolam_pembesaran"></strong></span>
                            <span class="text-slate-400">•</span>
                            <span x-show="selectedBatch?.batch_pembesaran_id" x-text="'Batch: ' + selectedBatch?.batch_pembesaran_id"></span>
                        </div>
                    </template>
                </div>
            </template>

            <!-- Modal Content Info Grid -->
            <div class="grid grid-cols-2 sm:grid-cols-3 gap-3 text-xs">
                <div class="p-3.5 bg-slate-50 rounded-xl border border-slate-100">
                    <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400 block mb-1">FASE PERTUMBUHAN</span>
                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold uppercase inline-block" :class="selectedBatch?.faseClass" x-text="selectedBatch?.fase"></span>
                </div>
                <div class="p-3.5 bg-slate-50 rounded-xl border border-slate-100">
                    <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400 block mb-1">STATUS BATCH</span>
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[10px] font-extrabold" :class="selectedBatch?.statusClass">
                        <span class="w-1.5 h-1.5 rounded-full" :class="selectedBatch?.dotClass"></span>
                        <span x-text="selectedBatch?.statusLabel || selectedBatch?.status"></span>
                    </span>
                </div>
                <div class="p-3.5 bg-slate-50 rounded-xl border border-slate-100">
                    <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400 block mb-1">USIA BATCH (DOC)</span>
                    <span class="font-extrabold text-slate-900 text-sm" x-text="selectedBatch?.usia"></span>
                </div>

                <div class="p-3.5 bg-slate-50 rounded-xl border border-slate-100">
                    <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400 block mb-1">POPULASI (EKOR)</span>
                    <span class="font-extrabold text-slate-900 text-sm" x-text="selectedBatch?.jumlah + ' Ekor'"></span>
                </div>
                <div class="p-3.5 bg-emerald-50/80 rounded-xl border border-emerald-200/80">
                    <span class="text-[10px] font-extrabold uppercase tracking-wider text-emerald-700 block mb-1">TOTAL BOBOT (KG)</span>
                    <span class="font-extrabold text-emerald-800 text-sm" x-text="selectedBatch?.totalBobotFormat || (selectedBatch?.totalBobotKg + ' kg')"></span>
                </div>
                <div class="p-3.5 bg-slate-50 rounded-xl border border-slate-100">
                    <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400 block mb-1">JENIS IKAN</span>
                    <span class="font-extrabold text-slate-900" x-text="selectedBatch?.jenisIkan"></span>
                </div>

                <div class="p-3.5 bg-slate-50 rounded-xl border border-slate-100">
                    <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400 block mb-1">TANGGAL INPUT</span>
                    <span class="font-bold text-slate-700" x-text="selectedBatch?.inputDate"></span>
                </div>

                <div class="col-span-2 p-3.5 bg-sky-50/60 rounded-xl border border-sky-100 flex items-center justify-between">
                    <div>
                        <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400 block">LOKASI KOLAM ASAL</span>
                        <span class="font-extrabold text-[#0B2570] text-xs" x-text="selectedBatch?.kolam"></span>
                    </div>
                    <div class="text-right">
                        <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400 block">PARAMETER AIR</span>
                        <span class="font-extrabold text-slate-800 text-xs" x-text="'pH ' + selectedBatch?.phAir + ' • ' + selectedBatch?.suhuAir"></span>
                    </div>
                </div>
            </div>

            <!-- Modal Footer -->
            <div class="flex items-center justify-end gap-3 pt-3 border-t border-slate-100">
                <template x-if="selectedBatch?.status !== 'selesai'">
                    <button type="button" @click="openEdit(selectedBatch)" class="px-4 py-2 rounded-xl bg-slate-100 text-slate-700 font-bold hover:bg-slate-200 transition-colors text-xs flex items-center gap-2">
                        <i class="fa-solid fa-pen-to-square text-amber-600"></i>
                        <span>Edit Batch</span>
                    </button>
                </template>
                <template x-if="selectedBatch?.status === 'selesai'">
                    <a href="{{ route('pembesaran') }}" class="px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold transition-colors text-xs flex items-center gap-2 shadow-xs">
                        <i class="fa-solid fa-arrow-up-right-from-square text-xs"></i>
                        <span>Buka Menu Pembesaran</span>
                    </a>
                </template>
                <button type="button" @click="detailModalOpen = false" class="px-5 py-2 rounded-xl bg-[#031B4E] text-white font-extrabold hover:bg-navy-900 text-xs shadow-md shadow-sky-950/20 transition-all">
                    Tutup
                </button>
            </div>
        </div>
    </div>

    <!-- Modal Transfer Batch ke Pembesaran -->
    <div x-show="transferModalOpen" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-95"
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs"
         style="display: none;">
        
        <div class="bg-white rounded-3xl max-w-lg w-full p-6 shadow-2xl space-y-5 border border-slate-200" @click.outside="transferModalOpen = false">
            <!-- Modal Header -->
            <div class="flex items-center justify-between border-b border-slate-100 pb-4">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-2xl bg-emerald-100 text-emerald-700 flex items-center justify-center font-bold text-sm">
                        <i class="fa-solid fa-right-left"></i>
                    </div>
                    <div>
                        <span class="text-[10px] font-extrabold uppercase text-slate-400 tracking-wider block">MUTASI BATCH HASIL PEMBIBITAN</span>
                        <h3 class="text-base font-extrabold text-slate-900">Pindah Ke Kolam Pembesaran</h3>
                    </div>
                </div>
                <button @click="transferModalOpen = false" class="w-8 h-8 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-500 flex items-center justify-center transition-colors">
                    <i class="fa-solid fa-xmark text-sm"></i>
                </button>
            </div>

            <!-- Batch Summary Banner with Kg weight -->
            <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200/80 grid grid-cols-3 gap-2 text-xs">
                <div>
                    <span class="text-[10px] font-extrabold uppercase text-slate-400 block">BATCH ASAL</span>
                    <span class="font-extrabold text-[#031B4E]" x-text="selectedBatchToTransfer?.id"></span>
                    <span class="text-slate-500 block font-semibold mt-0.5" x-text="selectedBatchToTransfer?.jenisIkan"></span>
                </div>
                <div>
                    <span class="text-[10px] font-extrabold uppercase text-slate-400 block">SISA BIBIT</span>
                    <span class="font-extrabold text-slate-800 text-sm" x-text="(selectedBatchToTransfer?.jumlah || 0) + ' Ekor'"></span>
                </div>
                <div class="text-right">
                    <span class="text-[10px] font-extrabold uppercase text-slate-400 block">BOBOT AWAL SAAT INI</span>
                    <span class="font-extrabold text-emerald-600 text-sm" x-text="selectedBatchToTransfer?.totalBobotFormat || (selectedBatchToTransfer?.totalBobotKg + ' kg')"></span>
                </div>
            </div>

            <form @submit.prevent="submitTransfer()" class="space-y-4">
                <div>
                    <label class="text-[10px] font-extrabold uppercase text-slate-500 block mb-1.5">PILIH KOLAM PEMBESARAN TUJUAN *</label>
                    <select x-model="transferForm.id_kolam_pembesaran" required class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-xs font-semibold text-slate-800 bg-slate-50 focus:bg-white focus:outline-none focus:border-emerald-600 focus:ring-2 focus:ring-emerald-600/10">
                        <option value="">Pilih Kolam Pembesaran...</option>
                        @if(isset($kolamPembesaran) && count($kolamPembesaran) > 0)
                            @foreach($kolamPembesaran as $kp)
                                <option value="{{ $kp->nama_kolam }}">{{ $kp->nama_kolam }} ({{ $kp->tipe_kolam ?? 'Pembesaran' }} - Kapasitas: {{ number_format($kp->kapasitas, 0, ',', '.') }} kg)</option>
                            @endforeach
                        @else
                            <option value="Kolam Pembesaran A-01">Kolam Pembesaran A-01 (Beton - Kapasitas: 2.000 kg)</option>
                            <option value="Kolam Pembesaran B-02">Kolam Pembesaran B-02 (Terpal - Kapasitas: 1.500 kg)</option>
                            <option value="Kolam Pembesaran Bioflok C-03">Kolam Pembesaran Bioflok C-03 (Bioflok - Kapasitas: 3.000 kg)</option>
                        @endif
                    </select>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="text-[10px] font-extrabold uppercase text-slate-500 block mb-1.5">ESTIMASI BIOMASSA AWAL (KG) *</label>
                        <input type="number" step="0.1" x-model="transferForm.biomassa_est" required
                               class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-xs font-bold text-emerald-700 bg-slate-50 focus:bg-white focus:outline-none focus:border-emerald-600">
                        <p class="text-[10px] text-slate-400 mt-1">Otomatis sinkron dengan total bobot batch pembibitan.</p>
                    </div>
                    <div>
                        <label class="text-[10px] font-extrabold uppercase text-slate-500 block mb-1.5">TARGET PANEN (KG) *</label>
                        <input type="number" step="1" x-model="transferForm.target_panen_kg" required
                               class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-xs font-semibold text-slate-800 bg-slate-50 focus:bg-white focus:outline-none focus:border-emerald-600">
                    </div>
                </div>

                <div class="pt-3 flex items-center justify-end gap-2.5 border-t border-slate-100">
                    <button type="button" @click="transferModalOpen = false"
                            class="px-4 py-2 rounded-xl border border-slate-200 text-xs font-bold text-slate-600 hover:bg-slate-50">
                        Batal
                    </button>
                    <button type="submit" :disabled="isSubmitting"
                            class="px-5 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs flex items-center gap-2 shadow-md shadow-emerald-600/20">
                        <i class="fa-solid" :class="isSubmitting ? 'fa-spinner fa-spin' : 'fa-check'"></i>
                        <span>Konfirmasi Pindah ke Pembesaran</span>
                    </button>
                </div>
            </form>
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
                <h3 class="text-lg font-extrabold text-slate-900">Hapus Data Batch?</h3>
                <p class="text-xs text-slate-500 font-medium leading-relaxed">
                    Apakah Anda yakin ingin menghapus data batch <strong class="text-slate-800" x-text="selectedBatchToDelete?.id"></strong>? Data yang dihapus tidak dapat dipulihkan.
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
function pembibitanComponent() {
    return {
        showForm: false,
        formMode: 'create',
        detailModalOpen: false,
        selectedBatch: null,
        isSubmitting: false,

        form: {
            id: 'BCH-' + new Date().getFullYear() + '-' + String(Math.floor(10 + Math.random() * 90)) + '-A01',
            id_batch: null,
            jenisIkan: 'Lele',
            fase_pertumbuhan: 'TELUR',
            jumlahBibitAwal: 250000,
            totalBobotKg: 25.0,
            tglPemijahan: new Date().toISOString().split('T')[0],
            prediksiHari: 3,
            jumlahKematian: 0,
            statusBatch: 'menetas',
            kolam: ''
        },

        batches: {!! isset($batches) && count($batches) > 0 ? json_encode($batches) : json_encode([
            [
                'id_batch' => 1,
                'id' => '#BT-00124',
                'inputDate' => '12 Okt 2023',
                'tglPemijahan' => '2023-10-12',
                'fase' => 'TELUR',
                'faseClass' => 'bg-slate-100 text-slate-700',
                'usia' => '2 Hari',
                'jumlahBibitAwal' => 250000,
                'jumlahKematian' => 0,
                'jumlah' => '250,000',
                'totalBobotKg' => 25.0,
                'totalBobotFormat' => '25,0 kg',
                'jenisIkan' => 'GURAMI',
                'rawJenisIkan' => 'Gurami',
                'status' => 'menetas',
                'statusLabel' => 'Mulai Menetas',
                'statusClass' => 'bg-sky-100 text-sky-700',
                'dotClass' => 'bg-sky-500',
                'kolam' => 'Kolam Pemijahan A-01',
                'phAir' => '7.2',
                'suhuAir' => '28°C'
            ]
        ]) !!},
        activeFilter: 'aktif',

        get filteredBatches() {
            if (this.activeFilter === 'selesai') {
                return this.batches.filter(b => b.status === 'selesai');
            }
            if (this.activeFilter === 'all') {
                return this.batches;
            }
            // Default: hanya tampilkan batch hatchery aktif (bukan selesai / gagal)
            return this.batches.filter(b => b.status !== 'selesai' && b.status !== 'gagal');
        },

        deleteModalOpen: false,
        selectedBatchToDelete: null,
        transferModalOpen: false,
        selectedBatchToTransfer: null,
        transferForm: {
            id_kolam_pembesaran: '',
            target_panen_kg: 500,
            biomassa_est: 50
        },
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
                id_batch: item.id_batch,
                jenisIkan: item.rawJenisIkan || (item.jenisIkan ? item.jenisIkan.charAt(0) + item.jenisIkan.slice(1).toLowerCase() : 'Lele'),
                fase_pertumbuhan: item.fase || 'TELUR',
                jumlahBibitAwal: item.jumlahBibitAwal || 250000,
                totalBobotKg: item.totalBobotKg || 25.0,
                tglPemijahan: item.tglPemijahan || new Date().toISOString().split('T')[0],
                prediksiHari: 3,
                jumlahKematian: item.jumlahKematian || 0,
                statusBatch: item.status || 'aktif',
                kolam: item.kolam || ''
            };
            this.detailModalOpen = false;
            this.showForm = true;
            window.scrollTo({ top: 0, behavior: 'smooth' });
        },

        resetForm() {
            this.form = {
                id: 'BCH-' + new Date().getFullYear() + '-' + String(Math.floor(10 + Math.random() * 90)) + '-A01',
                id_batch: null,
                jenisIkan: '',
                fase_pertumbuhan: 'TELUR',
                jumlahBibitAwal: 250000,
                totalBobotKg: 25.0,
                tglPemijahan: new Date().toISOString().split('T')[0],
                prediksiHari: 3,
                jumlahKematian: 0,
                statusBatch: 'menetas',
                kolam: ''
            };
        },

        getFaseClass(fase) {
            const f = (fase || '').toUpperCase();
            if (f === 'TELUR') return 'bg-slate-100 text-slate-700';
            if (f === 'LARVA') return 'bg-sky-100 text-sky-700';
            return 'bg-indigo-100 text-indigo-700';
        },

        async submitBatch() {
            if (!this.form.jenisIkan) {
                alert('Silakan pilih Jenis Ikan terlebih dahulu!');
                return;
            }
            if (!this.form.jumlahBibitAwal || Number(this.form.jumlahBibitAwal) <= 0) {
                alert('Silakan masukkan Jumlah Bibit / Telur Awal!');
                return;
            }
            if (!this.form.kolam) {
                alert('Silakan pilih Lokasi Kolam Pemijahan terlebih dahulu!');
                return;
            }

            this.isSubmitting = true;
            const bibitAwalNum = Number(this.form.jumlahBibitAwal || 0);
            const matiNum = Number(this.form.jumlahKematian || 0);
            const bobotKgNum = Number(this.form.totalBobotKg || 0);
            const sisaBibit = Math.max(0, bibitAwalNum - matiNum);
            const rawStatus = this.form.statusBatch;
            const faseVal = (this.form.fase_pertumbuhan || 'TELUR').toUpperCase();

            let statusLabel = 'Aktif';
            let statusClass = 'bg-emerald-100 text-emerald-700';
            let dotClass = 'bg-emerald-500';

            if (rawStatus === 'inkubasi') {
                statusLabel = 'Proses Inkubasi';
                statusClass = 'bg-amber-100 text-amber-700';
                dotClass = 'bg-amber-500';
            } else if (rawStatus === 'menetas') {
                statusLabel = 'Mulai Menetas';
                statusClass = 'bg-sky-100 text-sky-700';
                dotClass = 'bg-sky-500';
            } else if (rawStatus === 'siap_pindah') {
                statusLabel = 'Siap Pindah';
                statusClass = 'bg-teal-100 text-teal-700';
                dotClass = 'bg-teal-500';
            } else if (rawStatus === 'selesai') {
                statusLabel = 'Selesai (Dipindahkan)';
                statusClass = 'bg-slate-100 text-slate-700';
                dotClass = 'bg-slate-500';
            } else if (rawStatus === 'gagal') {
                statusLabel = 'Gagal / Dibatalkan';
                statusClass = 'bg-rose-100 text-rose-700';
                dotClass = 'bg-rose-500';
            }

            if (this.formMode === 'edit') {
                const idBatch = this.form.id_batch || (this.selectedBatch ? this.selectedBatch.id_batch : null);
                try {
                    const res = await fetch('/pembibitan/' + idBatch, {
                        method: 'PUT',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({
                            jenis_ikan: this.form.jenisIkan,
                            id_kolam: this.form.kolam,
                            tgl_pemijahan: this.form.tglPemijahan,
                            fase_pertumbuhan: faseVal,
                            jumlah_bibitAwal: bibitAwalNum,
                            jumlah_kematian: matiNum,
                            total_bobot_kg: bobotKgNum,
                            status: rawStatus
                        })
                    });
                    const data = await res.json();
                    if (res.ok && data.success) {
                        const targetIndex = this.batches.findIndex(b => b.id_batch === idBatch || b.id === this.form.id);
                        if (targetIndex !== -1) {
                            this.batches[targetIndex].jenisIkan = this.form.jenisIkan.toUpperCase();
                            this.batches[targetIndex].rawJenisIkan = this.form.jenisIkan;
                            this.batches[targetIndex].fase = faseVal;
                            this.batches[targetIndex].faseClass = this.getFaseClass(faseVal);
                            this.batches[targetIndex].kolam = this.form.kolam;
                            this.batches[targetIndex].tglPemijahan = this.form.tglPemijahan;
                            this.batches[targetIndex].jumlahBibitAwal = bibitAwalNum;
                            this.batches[targetIndex].jumlahKematian = matiNum;
                            this.batches[targetIndex].jumlah = sisaBibit.toLocaleString('id-ID');
                            this.batches[targetIndex].totalBobotKg = bobotKgNum;
                            this.batches[targetIndex].totalBobotFormat = bobotKgNum.toLocaleString('id-ID', { minimumFractionDigits: 1, maximumFractionDigits: 2 }) + ' kg';
                            this.batches[targetIndex].status = rawStatus;
                            this.batches[targetIndex].statusLabel = statusLabel;
                            this.batches[targetIndex].statusClass = statusClass;
                            this.batches[targetIndex].dotClass = dotClass;
                        }
                        this.showForm = false;
                        this.toastMessage = data.message || 'Data batch berhasil diperbarui!';
                        this.showToast = true;
                        setTimeout(() => { this.showToast = false; }, 4000);
                        this.formMode = 'create';
                        this.resetForm();
                    } else {
                        alert(data.message || 'Gagal memperbarui data batch.');
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
                const res = await fetch('{{ route('pembibitan.store') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        jenis_ikan: this.form.jenisIkan,
                        id_kolam: this.form.kolam,
                        tgl_pemijahan: this.form.tglPemijahan,
                        fase_pertumbuhan: faseVal,
                        jumlah_bibitAwal: bibitAwalNum,
                        jumlah_kematian: matiNum,
                        total_bobot_kg: bobotKgNum,
                        status: rawStatus
                    })
                });

                const data = await res.json();
                if (res.ok && data.success) {
                    const newBatch = data.batch;
                    const diffDays = this.form.tglPemijahan ? Math.max(0, Math.floor((new Date() - new Date(this.form.tglPemijahan)) / (1000 * 60 * 60 * 24))) : 0;
                    const finalBobot = Number(newBatch.total_bobot_kg || bobotKgNum);
                    this.batches.unshift({
                        id_batch: newBatch.id_batch,
                        id: '#BT-' + String(newBatch.id_batch).padStart(5, '0'),
                        inputDate: new Date().toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' }),
                        tglPemijahan: this.form.tglPemijahan,
                        fase: faseVal,
                        faseClass: this.getFaseClass(faseVal),
                        usia: diffDays + ' Hari',
                        usiaDays: diffDays,
                        jumlahBibitAwal: bibitAwalNum,
                        jumlahKematian: matiNum,
                        jumlah: sisaBibit.toLocaleString('id-ID'),
                        jumlahRaw: sisaBibit,
                        totalBobotKg: finalBobot,
                        totalBobotFormat: finalBobot.toLocaleString('id-ID', { minimumFractionDigits: 1, maximumFractionDigits: 2 }) + ' kg',
                        jenisIkan: this.form.jenisIkan.toUpperCase(),
                        rawJenisIkan: this.form.jenisIkan,
                        status: rawStatus,
                        statusLabel: statusLabel,
                        statusClass: statusClass,
                        dotClass: dotClass,
                        kolam: this.form.kolam,
                        phAir: '7.2',
                        suhuAir: '28.0°C'
                    });

                    this.showForm = false;
                    this.resetForm();
                    this.toastMessage = data.message || 'Data batch pembibitan berhasil disimpan!';
                    this.showToast = true;
                    setTimeout(() => { this.showToast = false; }, 4000);
                } else {
                    alert(data.message || 'Gagal menyimpan data batch.');
                }
            } catch (err) {
                alert('Gagal menyimpan data batch.');
            } finally {
                this.isSubmitting = false;
            }
        },

        openDetail(item) {
            this.selectedBatch = item;
            this.detailModalOpen = true;
        },

        deleteBatch(item) {
            this.selectedBatchToDelete = item;
            this.deleteModalOpen = true;
        },

        async markBatchFinished(item) {
            const rawId = item.id_batch || item.id.replace(/[^0-9]/g, '');
            if (!confirm('Apakah Anda yakin ingin menyelesaikan masa pembibitan untuk batch ' + item.id + ' agar siap dipindahkan ke pembesaran?')) {
                return;
            }
            
            this.isSubmitting = true;
            try {
                const res = await fetch('/pembibitan/' + rawId, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        status: 'selesai',
                        fase_pertumbuhan: 'FINGERLING'
                    })
                });
                const data = await res.json();
                if (res.ok && data.success) {
                    const targetIdx = this.batches.findIndex(b => b.id === item.id || b.id_batch === rawId);
                    if (targetIdx !== -1) {
                        this.batches[targetIdx].status = 'selesai';
                        this.batches[targetIdx].fase = 'FINGERLING';
                        this.batches[targetIdx].faseClass = this.getFaseClass('FINGERLING');
                        this.batches[targetIdx].statusLabel = 'Selesai';
                        this.batches[targetIdx].statusClass = 'bg-slate-100 text-slate-700';
                        this.batches[targetIdx].dotClass = 'bg-slate-500';
                    }
                    this.toastMessage = 'Batch ' + item.id + ' telah diselesaikan dan siap dipindahkan ke pembesaran!';
                    this.showToast = true;
                    setTimeout(() => { this.showToast = false; }, 4000);
                } else {
                    alert(data.message || 'Gagal menyelesaikan batch pembibitan.');
                }
            } catch (e) {
                alert('Terjadi kesalahan saat menyelesaikan batch.');
            } finally {
                this.isSubmitting = false;
            }
        },

        async executeDeleteBatch() {
            if (!this.selectedBatchToDelete) return;
            const item = this.selectedBatchToDelete;
            const id = item.id;
            const rawId = item.id_batch || id.replace(/[^0-9]/g, '');

            try {
                await fetch('/pembibitan/' + rawId, {
                    method: 'DELETE',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                });
            } catch (e) {}

            this.batches = this.batches.filter(b => b.id !== id);
            this.deleteModalOpen = false;
            this.toastMessage = 'Data batch ' + id + ' berhasil dihapus!';
            this.showToast = true;
            setTimeout(() => { this.showToast = false; }, 3500);
            this.selectedBatchToDelete = null;
        },

        openTransferModal(item) {
            this.selectedBatchToTransfer = item;
            const sisaBibit = item.jumlahRaw || (item.jumlahBibitAwal - item.jumlahKematian) || 1000;
            const estBiomassa = item.totalBobotKg && item.totalBobotKg > 0 ? item.totalBobotKg : Math.max(10, Math.round(sisaBibit * 0.02));
            this.transferForm = {
                id_kolam_pembesaran: '',
                target_panen_kg: Math.max(100, Math.round(estBiomassa * 10)),
                biomassa_est: estBiomassa
            };
            this.transferModalOpen = true;
        },

        async submitTransfer() {
            if (!this.selectedBatchToTransfer) return;
            if (!this.transferForm.id_kolam_pembesaran) {
                alert('Silakan pilih Kolam Pembesaran tujuan!');
                return;
            }

            this.isSubmitting = true;
            const item = this.selectedBatchToTransfer;
            const rawId = item.id_batch || item.id.replace(/[^0-9]/g, '');

            try {
                const res = await fetch('/pembibitan/' + rawId + '/transfer', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        id_kolam_pembesaran: this.transferForm.id_kolam_pembesaran,
                        target_panen_kg: this.transferForm.target_panen_kg,
                        biomassa_est: this.transferForm.biomassa_est
                    })
                });

                const data = await res.json();
                if (res.ok && data.success) {
                    const targetIdx = this.batches.findIndex(b => b.id === item.id || b.id_batch === rawId);
                    if (targetIdx !== -1) {
                        this.batches[targetIdx].status = 'selesai';
                        this.batches[targetIdx].fase = 'FINGERLING';
                        this.batches[targetIdx].faseClass = this.getFaseClass('FINGERLING');
                        this.batches[targetIdx].statusLabel = 'Selesai (Dipindahkan)';
                        this.batches[targetIdx].statusClass = 'bg-slate-100 text-slate-700';
                        this.batches[targetIdx].dotClass = 'bg-slate-500';
                        this.batches[targetIdx].kolam_pembesaran = this.transferForm.id_kolam_pembesaran;
                        if (data.batch_pembesaran) {
                            this.batches[targetIdx].batch_pembesaran_id = '#PB-' + String(data.batch_pembesaran.id_pembesaran).padStart(5, '0');
                            this.batches[targetIdx].tgl_pindah = new Date().toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' });
                        }
                    }

                    this.transferModalOpen = false;
                    this.toastMessage = data.message || 'Batch berhasil dipindahkan ke Pembesaran!';
                    this.showToast = true;
                    setTimeout(() => { this.showToast = false; }, 4500);
                } else {
                    alert(data.message || 'Gagal memindahkan batch ke pembesaran.');
                }
            } catch (e) {
                alert('Terjadi kesalahan saat memindahkan batch.');
            } finally {
                this.isSubmitting = false;
            }
        }
    };
}
</script>
@endpush

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

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            <!-- Left 2 Cols: Form Fields -->
            <div class="lg:col-span-2 space-y-5">

                <!-- Section 1: Data Utama Batch -->
                <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-xs space-y-4">
                    <div class="flex items-center gap-2.5 text-sm font-bold text-slate-900">
                        <div class="w-8 h-8 rounded-xl bg-[#051B44] text-white flex items-center justify-center">
                            <i class="fa-solid fa-fish text-xs"></i>
                        </div>
                        <span>Data Utama Batch</span>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
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
                            <label class="text-[10px] font-extrabold uppercase tracking-wider text-slate-500 block mb-1">JUMLAH BIBIT / TELUR AWAL *</label>
                            <div class="flex items-center gap-2">
                                <input type="number" x-model="form.jumlahBibitAwal" min="100" step="500" placeholder="Contoh: 250000"
                                       class="flex-1 px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs font-semibold text-slate-700 bg-slate-50/70 focus:bg-white focus:outline-none focus:ring-2 focus:ring-sky-500 transition-all">
                                <span class="text-xs font-bold text-slate-400">ekor/btr</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Section 2: Timeline & Estimasi -->
                <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-xs space-y-4">
                    <div class="flex items-center gap-2.5 text-sm font-bold text-slate-900">
                        <div class="w-8 h-8 rounded-xl bg-[#10B981] text-white flex items-center justify-center">
                            <i class="fa-regular fa-calendar text-xs"></i>
                        </div>
                        <span>Timeline &amp; Estimasi</span>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="text-[10px] font-extrabold uppercase tracking-wider text-slate-500 block mb-1">TANGGAL PEMIJAHAN</label>
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
                    </div>

                    <div class="flex items-center justify-between gap-4">
                        <div class="flex-1">
                            <label class="text-[10px] font-extrabold uppercase tracking-wider text-slate-500 block mb-1">JUMLAH KEMATIAN TELUR (ESTIMASI)</label>
                            <input type="number" x-model="form.jumlahKematian" min="0" placeholder="Masukkan angka..."
                                   class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs font-semibold text-slate-700 bg-slate-50/70 focus:bg-white focus:outline-none focus:ring-2 focus:ring-sky-500 transition-all">
                        </div>
                        <span class="text-xs font-bold text-slate-400 mt-5">butir</span>
                    </div>
                </div>

                <!-- Section 3: Status & Lokasi -->
                <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-xs space-y-4">
                    <div class="flex items-center gap-2.5 text-sm font-bold text-slate-900">
                        <div class="w-8 h-8 rounded-xl bg-[#0284C7] text-white flex items-center justify-center">
                            <i class="fa-solid fa-location-dot text-xs"></i>
                        </div>
                        <span>Status &amp; Lokasi</span>
                    </div>

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
                            <button type="button" @click="form.statusBatch = 'selesai'"
                                    :class="form.statusBatch === 'selesai' ? 'bg-slate-700 text-white border-transparent shadow-xs' : 'bg-white text-slate-600 border-slate-200 hover:bg-slate-50'"
                                    class="px-3 py-1.5 rounded-xl border text-xs font-bold transition-all">
                                Selesai
                            </button>
                            <button type="button" @click="form.statusBatch = 'gagal'"
                                    :class="form.statusBatch === 'gagal' ? 'bg-rose-600 text-white border-transparent shadow-xs' : 'bg-white text-slate-600 border-slate-200 hover:bg-slate-50'"
                                    class="px-3 py-1.5 rounded-xl border text-xs font-bold transition-all">
                                Gagal / Dibatalkan
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
                        <p class="text-[10px] text-slate-400 mt-1 italic">Khusus fasilitas Hatchery Pembibitan: Kolam Pemijahan A-01, Kolam Penetasan B-02, Kolam Pembibitan L-03.</p>
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="flex items-center justify-end gap-3">
                    <button type="button" @click="showForm = false; formMode = 'create'; resetForm()"
                            class="px-5 py-2.5 rounded-xl border border-slate-200 text-xs font-bold text-slate-600 hover:bg-slate-50 transition-colors">
                        Batal
                    </button>
                    <button type="button" @click="resetForm()"
                            class="px-5 py-2.5 rounded-xl border border-slate-200 text-xs font-bold text-slate-600 hover:bg-slate-50 transition-colors">
                        Reset
                    </button>
                    <button type="button" @click="submitBatch()" :disabled="isSubmitting"
                            class="px-5 py-2.5 rounded-xl bg-[#031B4E] hover:bg-navy-900 text-white font-bold text-xs shadow-xs transition-all flex items-center gap-2 disabled:opacity-60">
                        <i class="fa-solid" :class="isSubmitting ? 'fa-spinner fa-spin' : 'fa-floppy-disk'" class="text-xs"></i>
                        <span x-text="isSubmitting ? 'MENYIMPAN...' : (formMode === 'edit' ? 'SIMPAN PERUBAHAN' : 'SIMPAN DATA BATCH')"></span>
                    </button>
                </div>

            </div>

            <!-- Right 1 Col: Suhu Air Optimal Widget -->
            <div class="space-y-5">
                <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-xs text-center space-y-4">
                    <div class="flex items-center justify-center gap-2 text-sm font-bold text-slate-900">
                        <i class="fa-solid fa-temperature-half text-sky-600"></i>
                        <span>Suhu Air Optimal</span>
                    </div>

                    <!-- Gauge Circle -->
                    <div class="relative w-36 h-36 mx-auto">
                        <svg class="w-full h-full -rotate-90" viewBox="0 0 120 120">
                            <circle cx="60" cy="60" r="52" fill="none" stroke="#F1F5F9" stroke-width="12"/>
                            <circle cx="60" cy="60" r="52" fill="none" stroke="#0284C7" stroke-width="12"
                                    stroke-dasharray="326.73" stroke-dashoffset="60"
                                    stroke-linecap="round"/>
                        </svg>
                        <div class="absolute inset-0 flex flex-col items-center justify-center">
                            <span class="text-3xl font-extrabold text-[#0B2570]">28</span>
                            <span class="text-xs font-bold text-slate-400">°C</span>
                        </div>
                    </div>

                    <!-- Params -->
                    <div class="space-y-2 text-xs">
                        <div class="flex items-center justify-between p-2 bg-slate-50 rounded-lg border border-slate-100">
                            <span class="font-semibold text-slate-500">pH Air</span>
                            <span class="font-extrabold text-[#0B2570]">6.5 - 7.5</span>
                        </div>
                    </div>
                </div>
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
        <div class="p-5 border-b border-slate-100 flex items-center justify-between">
            <div>
                <h3 class="text-base font-extrabold text-slate-900">Batch Hatchery Aktif</h3>
                <p class="text-xs text-slate-500 font-medium">Daftar kelompok benih yang sedang dalam masa pemijahan dan penetasan.</p>
            </div>
            <button class="flex items-center gap-2 px-3.5 py-2 rounded-xl border border-slate-200 bg-slate-50 hover:bg-slate-100 text-xs font-bold text-slate-700 transition-colors">
                <i class="fa-solid fa-sliders text-xs"></i>
                <span>Filter</span>
            </button>
        </div>

        <div class="overflow-x-auto overflow-y-visible">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50/70 border-b border-slate-200/80 text-[10px] font-extrabold text-slate-400 uppercase tracking-wider">
                        <th class="py-4 px-6">BATCH ID</th>
                        <th class="py-4 px-6">FASE PERTUMBUHAN</th>
                        <th class="py-4 px-6">USIA (HARI)</th>
                        <th class="py-4 px-6">JUMLAH (EKOR)</th>
                        <th class="py-4 px-6">JENIS IKAN</th>
                        <th class="py-4 px-6">STATUS BATCH</th>
                        <th class="py-4 px-6 text-right">AKSI</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-xs font-medium text-slate-700">
                    
                    <template x-for="item in batches" :key="item.id">
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
                                         class="absolute right-0 top-full mt-1 w-44 rounded-xl bg-white border border-slate-200 shadow-xl py-1.5 z-50 text-left origin-top-right">
                                        
                                        <button @click="open = false; openDetail(item)" class="w-full px-3.5 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-50 flex items-center gap-2.5">
                                            <i class="fa-solid fa-eye text-sky-600 w-4"></i>
                                            <span>Detail Batch</span>
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
                                </div>
                            </td>
                        </tr>
                    </template>

                </tbody>
            </table>
        </div>

        <!-- Table Footer Pagination -->
        <div class="p-4 border-t border-slate-100 flex flex-col sm:flex-row items-center justify-between gap-4 text-xs font-semibold text-slate-500">
            <span x-text="'Menampilkan 1 - ' + batches.length + ' dari ' + batches.length + ' Batch'"></span>
            <div class="flex items-center gap-1">
                <button class="w-7 h-7 rounded-lg border border-slate-200 flex items-center justify-center text-slate-400 hover:bg-slate-50">&lt;</button>
                <button class="w-7 h-7 rounded-lg bg-[#031B4E] text-white font-bold flex items-center justify-center">1</button>
                <button class="w-7 h-7 rounded-lg border border-slate-200 flex items-center justify-center text-slate-600 hover:bg-slate-50">2</button>
                <button class="w-7 h-7 rounded-lg border border-slate-200 flex items-center justify-center text-slate-600 hover:bg-slate-50">3</button>
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

            <!-- Modal Content Info Grid -->
            <div class="grid grid-cols-2 gap-3 text-xs">
                <div class="p-3.5 bg-slate-50 rounded-xl border border-slate-100">
                    <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400 block mb-1">FASE PERTUMBUHAN</span>
                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold uppercase inline-block" :class="selectedBatch?.faseClass" x-text="selectedBatch?.fase"></span>
                </div>
                <div class="p-3.5 bg-slate-50 rounded-xl border border-slate-100">
                    <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400 block mb-1">STATUS KESEHATAN</span>
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[10px] font-extrabold" :class="selectedBatch?.statusClass">
                        <span class="w-1.5 h-1.5 rounded-full" :class="selectedBatch?.dotClass"></span>
                        <span x-text="selectedBatch?.statusKesehatan"></span>
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
                        <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400 block">LOKASI KOLAM</span>
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
                <button type="button" @click="openEdit(selectedBatch)" class="px-4 py-2 rounded-xl bg-slate-100 text-slate-700 font-bold hover:bg-slate-200 transition-colors text-xs flex items-center gap-2">
                    <i class="fa-solid fa-pen-to-square text-amber-600"></i>
                    <span>Edit Batch</span>
                </button>
                <button type="button" @click="detailModalOpen = false" class="px-5 py-2 rounded-xl bg-[#031B4E] text-white font-extrabold hover:bg-navy-900 text-xs shadow-md shadow-sky-950/20 transition-all">
                    Tutup
                </button>
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
            jumlahBibitAwal: 250000,
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
                id_batch: item.id_batch,
                jenisIkan: item.rawJenisIkan || (item.jenisIkan ? item.jenisIkan.charAt(0) + item.jenisIkan.slice(1).toLowerCase() : 'Lele'),
                jumlahBibitAwal: item.jumlahBibitAwal || 250000,
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
                jumlahBibitAwal: 250000,
                tglPemijahan: new Date().toISOString().split('T')[0],
                prediksiHari: 3,
                jumlahKematian: 0,
                statusBatch: 'menetas',
                kolam: ''
            };
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
            const sisaBibit = Math.max(0, bibitAwalNum - matiNum);
            const rawStatus = this.form.statusBatch;

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
            } else if (rawStatus === 'selesai') {
                statusLabel = 'Selesai';
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
                            jumlah_bibitAwal: bibitAwalNum,
                            jumlah_kematian: matiNum,
                            status: rawStatus
                        })
                    });
                    const data = await res.json();
                    if (res.ok && data.success) {
                        const targetIndex = this.batches.findIndex(b => b.id_batch === idBatch || b.id === this.form.id);
                        if (targetIndex !== -1) {
                            this.batches[targetIndex].jenisIkan = this.form.jenisIkan.toUpperCase();
                            this.batches[targetIndex].rawJenisIkan = this.form.jenisIkan;
                            this.batches[targetIndex].kolam = this.form.kolam;
                            this.batches[targetIndex].tglPemijahan = this.form.tglPemijahan;
                            this.batches[targetIndex].jumlahBibitAwal = bibitAwalNum;
                            this.batches[targetIndex].jumlahKematian = matiNum;
                            this.batches[targetIndex].jumlah = sisaBibit.toLocaleString('id-ID');
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
                        jumlah_bibitAwal: bibitAwalNum,
                        jumlah_kematian: matiNum,
                        status: rawStatus
                    })
                });

                const data = await res.json();
                if (res.ok && data.success) {
                    const newBatch = data.batch;
                    this.batches.unshift({
                        id_batch: newBatch.id_batch,
                        id: '#BT-' + String(newBatch.id_batch).padStart(5, '0'),
                        inputDate: new Date().toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' }),
                        tglPemijahan: this.form.tglPemijahan,
                        fase: rawStatus === 'menetas' ? 'LARVA' : 'TELUR',
                        faseClass: rawStatus === 'menetas' ? 'bg-sky-100 text-sky-700' : 'bg-slate-100 text-slate-700',
                        usia: '0 Hari',
                        jumlahBibitAwal: bibitAwalNum,
                        jumlahKematian: matiNum,
                        jumlah: sisaBibit.toLocaleString('id-ID'),
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
        }
    };
}
</script>
@endpush

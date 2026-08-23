@extends('layouts.app')

@section('title', 'Manajemen Pembibitan - SIM-BUDIDAYA')

@section('content')
<div class="space-y-6" x-data='{
    showForm: false,
    detailModalOpen: false,
    statusBatch: "inkubasi",
    selectedBatch: null,

    batches: [
        {
            id: "#BT-00124",
            inputDate: "12 Okt 2023",
            fase: "TELUR",
            faseClass: "bg-slate-100 text-slate-700",
            usia: "2 Hari",
            jumlah: "250,000",
            jenisIkan: "GURAMI",
            statusKesehatan: "Sehat",
            statusClass: "bg-emerald-100 text-emerald-700",
            dotClass: "bg-emerald-500",
            kolam: "Kolam Pemijahan A-01",
            phAir: "7.2",
            suhuAir: "28°C"
        },
        {
            id: "#BT-00121",
            inputDate: "05 Okt 2023",
            fase: "LARVA",
            faseClass: "bg-sky-100 text-sky-700",
            usia: "9 Hari",
            jumlah: "480,000",
            jenisIkan: "GURAMI",
            statusKesehatan: "Sehat",
            statusClass: "bg-emerald-100 text-emerald-700",
            dotClass: "bg-emerald-500",
            kolam: "Kolam Penetasan B-02",
            phAir: "7.0",
            suhuAir: "27.8°C"
        },
        {
            id: "#BT-00118",
            inputDate: "28 Sep 2023",
            fase: "FINGERLING",
            faseClass: "bg-sky-100 text-sky-700",
            usia: "16 Hari",
            jumlah: "310,000",
            jenisIkan: "LELE",
            statusKesehatan: "Perlu Atensi",
            statusClass: "bg-rose-100 text-rose-700",
            dotClass: "bg-rose-500",
            kolam: "Kolam Pembibitan L-03",
            phAir: "6.4",
            suhuAir: "29.1°C"
        },
        {
            id: "#BT-00115",
            inputDate: "20 Sep 2023",
            fase: "FINGERLING",
            faseClass: "bg-sky-100 text-sky-700",
            usia: "24 Hari",
            jumlah: "200,500",
            jenisIkan: "GURAMI",
            statusKesehatan: "Sehat",
            statusClass: "bg-emerald-100 text-emerald-700",
            dotClass: "bg-emerald-500",
            kolam: "Kolam Pembibitan G-01",
            phAir: "7.1",
            suhuAir: "28.0°C"
        }
    ],

    openDetail(item) {
        this.selectedBatch = item;
        this.detailModalOpen = true;
    },

    deleteBatch(item) {
        if (confirm("Apakah Anda yakin ingin menghapus data batch " + item.id + "?")) {
            this.batches = this.batches.filter(b => b.id !== item.id);
        }
    }
}'>

    <!-- Header & Action -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">Manajemen Pembibitan</h1>
            <p class="text-xs sm:text-sm text-slate-500 font-medium mt-1">Data real-time fase pembibitan, populasi benih, dan status batch hatchery.</p>
        </div>
        <div>
            <button @click="showForm = !showForm"
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

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="text-[10px] font-extrabold uppercase tracking-wider text-slate-500 block mb-1">ID BATCH (OTOMATIS)</label>
                            <input type="text" value="BCH-2023-11-A01" readonly
                                   class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs font-extrabold text-slate-500 bg-slate-100 cursor-not-allowed">
                        </div>
                        <div>
                            <label class="text-[10px] font-extrabold uppercase tracking-wider text-slate-500 block mb-1">JENIS IKAN</label>
                            <select class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs font-semibold text-slate-700 bg-slate-50/70 focus:bg-white focus:outline-none focus:ring-2 focus:ring-sky-500 transition-all">
                                <option>Pilih Spesies...</option>
                                <option>Gurami</option>
                                <option>Lele</option>
                                <option>Nila</option>
                                <option>Patin</option>
                                <option>Bawal</option>
                            </select>
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
                            <input type="date"
                                   class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs font-semibold text-slate-700 bg-slate-50/70 focus:bg-white focus:outline-none focus:ring-2 focus:ring-sky-500 transition-all">
                        </div>
                        <div>
                            <label class="text-[10px] font-extrabold uppercase tracking-wider text-slate-500 block mb-1">PREDIKSI PENETASAN (HARI)</label>
                            <div class="flex items-center gap-2">
                                <button type="button" class="w-9 h-9 rounded-xl border border-slate-200 bg-white text-slate-500 hover:bg-slate-50 flex items-center justify-center font-bold text-sm transition-colors">−</button>
                                <input type="number" value="3" min="1" max="30"
                                       class="flex-1 px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs font-extrabold text-center text-slate-700 bg-slate-50/70 focus:bg-white focus:outline-none focus:ring-2 focus:ring-sky-500 transition-all">
                                <button type="button" class="w-9 h-9 rounded-xl border border-slate-200 bg-white text-slate-500 hover:bg-slate-50 flex items-center justify-center font-bold text-sm transition-colors">+</button>
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center justify-between gap-4">
                        <div class="flex-1">
                            <label class="text-[10px] font-extrabold uppercase tracking-wider text-slate-500 block mb-1">JUMLAH KEMATIAN TELUR (ESTIMASI)</label>
                            <input type="text" placeholder="Masukkan angka..."
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
                            <button type="button" @click="statusBatch = 'inkubasi'"
                                    :class="statusBatch === 'inkubasi' ? 'bg-[#051B44] text-white border-transparent' : 'bg-white text-slate-600 border-slate-200 hover:bg-slate-50'"
                                    class="px-3 py-1.5 rounded-xl border text-xs font-bold transition-all">
                                Proses Inkubasi
                            </button>
                            <button type="button" @click="statusBatch = 'menetas'"
                                    :class="statusBatch === 'menetas' ? 'bg-[#051B44] text-white border-transparent' : 'bg-white text-slate-600 border-slate-200 hover:bg-slate-50'"
                                    class="px-3 py-1.5 rounded-xl border text-xs font-bold transition-all">
                                Mulai Menetas
                            </button>
                            <button type="button" @click="statusBatch = 'gagal'"
                                    :class="statusBatch === 'gagal' ? 'bg-rose-600 text-white border-transparent' : 'bg-white text-slate-600 border-slate-200 hover:bg-slate-50'"
                                    class="px-3 py-1.5 rounded-xl border text-xs font-bold transition-all">
                                Gagal / Dibatalkan
                            </button>
                        </div>
                    </div>

                    <div>
                        <label class="text-[10px] font-extrabold uppercase tracking-wider text-slate-500 block mb-1">LOKASI KOLAM PEMIJAHAN</label>
                        <select class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs font-semibold text-slate-700 bg-slate-50/70 focus:bg-white focus:outline-none focus:ring-2 focus:ring-sky-500 transition-all">
                            <option>Pilih ID Kolam...</option>
                            <option>Kolam A-01</option>
                            <option>Kolam A-02</option>
                            <option>Kolam B-01</option>
                            <option>Kolam B-02</option>
                        </select>
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="flex items-center justify-end gap-3">
                    <button type="button" @click="showForm = false"
                            class="px-5 py-2.5 rounded-xl border border-slate-200 text-xs font-bold text-slate-600 hover:bg-slate-50 transition-colors">
                        RESET FORM
                    </button>
                    <button type="button"
                            class="px-5 py-2.5 rounded-xl bg-[#031B4E] hover:bg-navy-900 text-white font-bold text-xs shadow-xs transition-all flex items-center gap-2">
                        <i class="fa-solid fa-floppy-disk text-xs"></i>
                        <span>SIMPAN DATA BATCH</span>
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
                    <h3 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">1,240,500</h3>
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
                    <h3 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">94.2%</h3>
                </div>
            </div>
            <div class="mt-4">
                <div class="w-full bg-slate-100 h-2 rounded-full overflow-hidden">
                    <div class="bg-emerald-500 h-full rounded-full w-[94.2%]"></div>
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
                    <h3 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">42</h3>
                    <span class="text-xs font-extrabold text-slate-500">/ 60 Bak</span>
                </div>
            </div>
            <div class="mt-4 text-xs font-semibold text-slate-500">
                18 Bak tersedia (Siap Pakai)
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
                    <h3 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">7.2</h3>
                    <span class="text-xs font-bold text-emerald-600">pH Normal</span>
                </div>
            </div>
            <div class="mt-4 text-xs font-semibold text-slate-500 flex items-center justify-between">
                <span>Suhu Rata-rata</span>
                <span class="font-extrabold text-slate-800">28°C</span>
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
                        <th class="py-4 px-6">STATUS KESEHATAN</th>
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
                                    <span x-text="item.statusKesehatan"></span>
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

                                        <button @click="open = false; showForm = true" class="w-full px-3.5 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-50 flex items-center gap-2.5">
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
            <span x-text="'Menampilkan 1-' + batches.length + ' dari 12 Batch'"></span>
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
                <button type="button" @click="detailModalOpen = false; showForm = true" class="px-4 py-2 rounded-xl bg-slate-100 text-slate-700 font-bold hover:bg-slate-200 transition-colors text-xs flex items-center gap-2">
                    <i class="fa-solid fa-pen-to-square text-amber-600"></i>
                    <span>Edit Batch</span>
                </button>
                <button type="button" @click="detailModalOpen = false" class="px-5 py-2 rounded-xl bg-[#031B4E] text-white font-extrabold hover:bg-navy-900 text-xs shadow-md shadow-sky-950/20 transition-all">
                    Tutup
                </button>
            </div>

        </div>
    </div>

</div>
@endsection

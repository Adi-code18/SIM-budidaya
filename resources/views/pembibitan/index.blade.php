@extends('layouts.app')

@section('title', 'Manajemen Pembibitan - SIM-BUDIDAYA')

@section('content')
<div class="space-y-6" x-data="{ showForm: false, statusBatch: 'inkubasi' }">

    <!-- Page Title Header -->
    <div>
        <h1 class="text-xl font-extrabold text-slate-900 tracking-tight">Manajemen Pembibitan</h1>
    </div>

    <!-- Ringkasan Operasional Section -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-lg font-bold text-slate-900">Ringkasan Operasional</h2>
            <p class="text-xs text-slate-500 font-medium mt-0.5">Data real-time fase pembibitan hari ini.</p>
        </div>
        <div>
            <button @click="showForm = !showForm"
                    class="px-4 py-2.5 rounded-xl bg-[#051B44] hover:bg-navy-900 text-white font-bold text-xs flex items-center gap-2 shadow-xs transition-all">
                <i class="fa-solid" :class="showForm ? 'fa-table-list' : 'fa-circle-plus'" class="text-xs"></i>
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
                        <span>Timeline & Estimasi</span>
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
                        <span>Status & Lokasi</span>
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
                            class="px-5 py-2.5 rounded-xl bg-[#051B44] hover:bg-navy-900 text-white font-bold text-xs shadow-xs transition-all flex items-center gap-2">
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
                            <span class="font-semibold text-slate-500">Target Suhu</span>
                            <span class="font-extrabold text-[#0B2570]">27°C - 30°C</span>
                        </div>
                        <div class="flex items-center justify-between p-2 bg-slate-50 rounded-lg border border-slate-100">
                            <span class="font-semibold text-slate-500">pH</span>
                            <span class="font-extrabold text-[#0B2570]">6.5 - 7.5</span>
                        </div>
                        <div class="flex items-center justify-between p-2 bg-slate-50 rounded-lg border border-slate-100">
                            <span class="font-semibold text-slate-500">Oksigen Terlarut</span>
                            <span class="font-extrabold text-[#0B2570]">> 5 mg/L</span>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <!-- ========= DIRECTORY / LIST MODE ========= -->

    <!-- 3 Metric KPI Cards Grid -->
    <div x-show="!showForm" class="grid grid-cols-1 md:grid-cols-3 gap-5">
        
        <!-- Card 1: Total Benih Aktif -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between">
                    <span class="text-[11px] font-extrabold uppercase tracking-wider text-slate-500">TOTAL BENIH AKTIF</span>
                    <div class="w-9 h-9 rounded-xl bg-[#BEE3F8]/60 text-[#006699] flex items-center justify-center">
                        <i class="fa-solid fa-users text-base"></i>
                    </div>
                </div>
                <div class="mt-3">
                    <h3 class="text-3xl font-extrabold text-slate-900 tracking-tight">1,240,500</h3>
                </div>
            </div>
            <div class="mt-4 flex items-center gap-1.5 text-xs font-semibold text-emerald-600">
                <i class="fa-solid fa-arrow-trend-up"></i>
                <span>+12.5% bln ini</span>
            </div>
        </div>

        <!-- Card 2: Survival Rate (SR) Rata-rata -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between">
                    <span class="text-[11px] font-extrabold uppercase tracking-wider text-slate-500">SURVIVAL RATE (SR) RATA-RATA</span>
                    <div class="w-9 h-9 rounded-xl bg-[#D1FAE5] text-[#059669] flex items-center justify-center">
                        <i class="fa-regular fa-heart text-base"></i>
                    </div>
                </div>
                <div class="mt-3">
                    <h3 class="text-3xl font-extrabold text-slate-900 tracking-tight">94.2%</h3>
                </div>
            </div>
            <div class="mt-4">
                <div class="w-full bg-slate-100 h-2 rounded-full overflow-hidden">
                    <div class="bg-[#10B981] h-full rounded-full w-[94.2%]"></div>
                </div>
            </div>
        </div>

        <!-- Card 3: Kapasitas Bak Terpakai -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between">
                    <span class="text-[11px] font-extrabold uppercase tracking-wider text-slate-500">KAPASITAS BAK TERPAKAI</span>
                    <div class="w-9 h-9 rounded-xl bg-[#BEE3F8]/60 text-[#006699] flex items-center justify-center">
                        <i class="fa-regular fa-clipboard text-base"></i>
                    </div>
                </div>
                <div class="mt-3">
                    <h3 class="text-3xl font-extrabold text-slate-900 tracking-tight">42 <span class="text-sm font-semibold text-slate-500">/ 60 Bak</span></h3>
                </div>
            </div>
            <div class="mt-4 text-xs font-medium text-slate-500">
                18 Bak tersedia (Siap Pakai)
            </div>
        </div>

    </div>

    <!-- Table Section: Batch Hatchery Aktif -->
    <div x-show="!showForm" class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
        <div class="p-5 border-b border-slate-100 flex items-center justify-between">
            <h3 class="text-base font-bold text-slate-900">Batch Hatchery Aktif</h3>
            <button class="flex items-center gap-2 px-3 py-1.5 rounded-lg border border-slate-200 bg-white text-xs font-semibold text-slate-600 hover:bg-slate-50 transition-colors">
                <i class="fa-solid fa-sliders text-xs"></i>
                <span>Filter</span>
            </button>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50/60 border-b border-slate-100 text-[10px] font-extrabold text-slate-400 uppercase tracking-wider">
                        <th class="py-3 px-6">BATCH ID</th>
                        <th class="py-3 px-6">FASE PERTUMBUHAN</th>
                        <th class="py-3 px-6">USIA (HARI)</th>
                        <th class="py-3 px-6">JUMLAH (EKOR)</th>
                        <th class="py-3 px-6">JENIS IKAN</th>
                        <th class="py-3 px-6">STATUS KESEHATAN</th>
                        <th class="py-3 px-6 text-right">AKSI</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-xs font-medium text-slate-700">
                    
                    <!-- Row 1 -->
                    <tr class="hover:bg-slate-50/50 transition-colors">
                        <td class="py-4 px-6">
                            <span class="font-bold text-[#0055CC] block">#BT-00124</span>
                            <span class="text-[10px] text-slate-400 font-normal">Input: 12 Okt 2023</span>
                        </td>
                        <td class="py-4 px-6">
                            <span class="px-2.5 py-0.5 rounded-full text-[11px] font-extrabold bg-[#E2E8F0] text-[#475569] uppercase">
                                TELUR
                            </span>
                        </td>
                        <td class="py-4 px-6 text-slate-700 font-semibold">2 Hari</td>
                        <td class="py-4 px-6 text-slate-800 font-semibold">250,000</td>
                        <td class="py-4 px-6 font-bold text-slate-800">GURAMI</td>
                        <td class="py-4 px-6">
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[11px] font-extrabold bg-[#C6F6D5] text-[#22543D]">
                                <span class="w-1.5 h-1.5 rounded-full bg-[#38A169]"></span> Sehat
                            </span>
                        </td>
                        <td class="py-4 px-6 text-right">
                            <button class="text-slate-400 hover:text-slate-600 p-1">
                                <i class="fa-solid fa-ellipsis-vertical text-sm"></i>
                            </button>
                        </td>
                    </tr>

                    <!-- Row 2 -->
                    <tr class="hover:bg-slate-50/50 transition-colors">
                        <td class="py-4 px-6">
                            <span class="font-bold text-[#0055CC] block">#BT-00121</span>
                            <span class="text-[10px] text-slate-400 font-normal">Input: 05 Okt 2023</span>
                        </td>
                        <td class="py-4 px-6">
                            <span class="px-2.5 py-0.5 rounded-full text-[11px] font-extrabold bg-[#E0F2FE] text-[#0284C7] uppercase">
                                LARVA
                            </span>
                        </td>
                        <td class="py-4 px-6 text-slate-700 font-semibold">9 Hari</td>
                        <td class="py-4 px-6 text-slate-800 font-semibold">480,000</td>
                        <td class="py-4 px-6 font-bold text-slate-800">GURAMI</td>
                        <td class="py-4 px-6">
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[11px] font-extrabold bg-[#C6F6D5] text-[#22543D]">
                                <span class="w-1.5 h-1.5 rounded-full bg-[#38A169]"></span> Sehat
                            </span>
                        </td>
                        <td class="py-4 px-6 text-right">
                            <button class="text-slate-400 hover:text-slate-600 p-1">
                                <i class="fa-solid fa-ellipsis-vertical text-sm"></i>
                            </button>
                        </td>
                    </tr>

                    <!-- Row 3 -->
                    <tr class="hover:bg-slate-50/50 transition-colors">
                        <td class="py-4 px-6">
                            <span class="font-bold text-[#0055CC] block">#BT-00118</span>
                            <span class="text-[10px] text-slate-400 font-normal">Input: 28 Sep 2023</span>
                        </td>
                        <td class="py-4 px-6">
                            <span class="px-2.5 py-0.5 rounded-full text-[11px] font-extrabold bg-[#E0F2FE] text-[#0284C7] uppercase">
                                FINGERLING
                            </span>
                        </td>
                        <td class="py-4 px-6 text-slate-700 font-semibold">16 Hari</td>
                        <td class="py-4 px-6 text-slate-800 font-semibold">310,000</td>
                        <td class="py-4 px-6 font-bold text-slate-800">LELE</td>
                        <td class="py-4 px-6">
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[11px] font-extrabold bg-[#FEE2E2] text-[#991B1B]">
                                <span class="w-1.5 h-1.5 rounded-full bg-[#EF4444]"></span> Perlu Atensi
                            </span>
                        </td>
                        <td class="py-4 px-6 text-right">
                            <button class="text-slate-400 hover:text-slate-600 p-1">
                                <i class="fa-solid fa-ellipsis-vertical text-sm"></i>
                            </button>
                        </td>
                    </tr>

                    <!-- Row 4 -->
                    <tr class="hover:bg-slate-50/50 transition-colors">
                        <td class="py-4 px-6">
                            <span class="font-bold text-[#0055CC] block">#BT-00115</span>
                            <span class="text-[10px] text-slate-400 font-normal">Input: 20 Sep 2023</span>
                        </td>
                        <td class="py-4 px-6">
                            <span class="px-2.5 py-0.5 rounded-full text-[11px] font-extrabold bg-[#E0F2FE] text-[#0284C7] uppercase">
                                FINGERLING
                            </span>
                        </td>
                        <td class="py-4 px-6 text-slate-700 font-semibold">24 Hari</td>
                        <td class="py-4 px-6 text-slate-800 font-semibold">200,500</td>
                        <td class="py-4 px-6 font-bold text-slate-800">GURAMI</td>
                        <td class="py-4 px-6">
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[11px] font-extrabold bg-[#C6F6D5] text-[#22543D]">
                                <span class="w-1.5 h-1.5 rounded-full bg-[#38A169]"></span> Sehat
                            </span>
                        </td>
                        <td class="py-4 px-6 text-right">
                            <button class="text-slate-400 hover:text-slate-600 p-1">
                                <i class="fa-solid fa-ellipsis-vertical text-sm"></i>
                            </button>
                        </td>
                    </tr>

                </tbody>
            </table>
        </div>

        <!-- Table Footer Pagination -->
        <div class="p-4 border-t border-slate-100 flex flex-col sm:flex-row items-center justify-between gap-4 text-xs font-medium text-slate-500">
            <span>Menampilkan 1-4 dari 12 Batch</span>
            <div class="flex items-center gap-1">
                <button class="w-7 h-7 rounded border border-slate-200 flex items-center justify-center text-slate-400 hover:bg-slate-50">&lt;</button>
                <button class="w-7 h-7 rounded bg-[#051B44] text-white font-bold flex items-center justify-center">1</button>
                <button class="w-7 h-7 rounded border border-slate-200 flex items-center justify-center text-slate-600 hover:bg-slate-50">2</button>
                <button class="w-7 h-7 rounded border border-slate-200 flex items-center justify-center text-slate-600 hover:bg-slate-50">3</button>
                <button class="w-7 h-7 rounded border border-slate-200 flex items-center justify-center text-slate-400 hover:bg-slate-50">&gt;</button>
            </div>
        </div>
    </div>

    <!-- Bottom Widget: Kualitas Air Hatchery -->
    <div x-show="!showForm" class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs w-full sm:w-72">
        <h4 class="text-xs font-bold text-slate-800 text-center">Kualitas Air Hatchery (Rata-rata)</h4>
        <div class="bg-slate-50 p-4 rounded-xl text-center mt-3 border border-slate-100">
            <span class="text-[10px] font-extrabold text-slate-400 uppercase tracking-widest block">PH</span>
            <h3 class="text-2xl font-extrabold text-[#0B2570] mt-0.5">7.2</h3>
            <span class="text-xs text-emerald-600 font-semibold block mt-1">Stabil</span>
        </div>
    </div>

</div>
@endsection

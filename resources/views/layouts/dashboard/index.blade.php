@extends('layouts.app')

@section('title', 'Dashboard Utama - SIM-BUDIDAYA')

@section('content')
<div class="space-y-6" x-data="dashboardData()">

    <!-- Floating Toast Notification -->
    <div x-show="toast.show" 
         x-transition:enter="transition ease-out duration-300 transform"
         x-transition:enter-start="opacity-0 translate-y-[-20px] scale-95"
         x-transition:enter-end="opacity-100 translate-y-0 scale-100"
         x-transition:leave="transition ease-in duration-200 transform"
         x-transition:leave-start="opacity-100 translate-y-0 scale-100"
         x-transition:leave-end="opacity-0 translate-y-[-20px] scale-95"
         class="fixed top-5 right-5 z-50 flex items-center gap-3 px-4 py-3 rounded-2xl shadow-2xl border text-xs font-bold transition-all"
         :class="{
             'bg-[#0B6B2E] text-white border-emerald-500 shadow-emerald-900/20': toast.type === 'success',
             'bg-[#0B2570] text-white border-sky-500 shadow-sky-900/20': toast.type === 'info',
             'bg-rose-600 text-white border-rose-500 shadow-rose-900/20': toast.type === 'error'
         }"
         style="display: none;">
        <i class="fa-solid" :class="{
            'fa-circle-check text-emerald-300 text-sm': toast.type === 'success',
            'fa-circle-info text-sky-300 text-sm': toast.type === 'info',
            'fa-triangle-exclamation text-rose-300 text-sm': toast.type === 'error'
        }"></i>
        <span x-text="toast.message"></span>
        <button @click="toast.show = false" class="ml-2 text-white/70 hover:text-white">
            <i class="fa-solid fa-xmark"></i>
        </button>
    </div>

    <!-- Title & Top Filter Section -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-extrabold text-[#0B2570] tracking-tight">Dashboard</h1>
            <p class="text-xs text-slate-500 font-medium mt-0.5">Operasional budidaya & ringkasan analitik terkini.</p>
        </div>
        <div class="flex items-center gap-3 flex-wrap">
            
            <!-- Date Filter Dropdown -->
            <div class="relative" @click.outside="datePickerOpen = false">
                <button type="button" 
                        @click="datePickerOpen = !datePickerOpen"
                        class="flex items-center gap-2 px-3 py-1.5 rounded-lg border border-slate-300 bg-white text-xs font-medium text-slate-700 shadow-xs hover:bg-slate-50 hover:border-slate-400 transition-all cursor-pointer">
                    <i class="fa-regular fa-calendar text-[#0077C6] text-xs"></i>
                    <span x-text="periodLabel">01 Agustus - 07 Agustus 2026</span>
                    <i class="fa-solid fa-chevron-down text-[10px] text-slate-400 ml-1 transition-transform" :class="datePickerOpen ? 'rotate-180' : ''"></i>
                </button>

                <!-- Dropdown Menu Options -->
                <div x-show="datePickerOpen" 
                     x-transition:enter="transition ease-out duration-150"
                     x-transition:enter-start="opacity-0 translate-y-1 scale-95"
                     x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                     x-transition:leave="transition ease-in duration-100"
                     x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                     x-transition:leave-end="opacity-0 translate-y-1 scale-95"
                     class="absolute right-0 mt-2 w-64 bg-white rounded-2xl shadow-xl border border-slate-100 py-2 z-40 text-xs"
                     style="display: none;">
                    <div class="px-3 py-1.5 border-b border-slate-100 font-bold text-slate-400 text-[10px] uppercase tracking-wider">
                        PILIH PERIODE ANALISIS
                    </div>
                    <button type="button" 
                            @click="selectPeriod('Hari Ini (07 Agustus 2026)', '1,250', '1.12', '450', 'Restoran Madani')"
                            class="w-full px-4 py-2 text-left hover:bg-sky-50 text-slate-700 hover:text-sky-700 flex items-center justify-between font-medium">
                        <span>Hari Ini</span>
                        <span class="text-[10px] text-slate-400">07 Ags</span>
                    </button>
                    <button type="button" 
                            @click="selectPeriod('01 Agustus - 07 Agustus 2026', '1,250', '1.12', '450', 'Restoran Madani')"
                            class="w-full px-4 py-2 text-left hover:bg-sky-50 text-slate-700 hover:text-sky-700 flex items-center justify-between font-bold bg-sky-50/50">
                        <span>7 Hari Terakhir (Default)</span>
                        <i class="fa-solid fa-check text-sky-600 text-xs"></i>
                    </button>
                    <button type="button" 
                            @click="selectPeriod('15 Juli - 07 Agustus 2026', '1,420', '1.15', '580', 'Pasar Modern BSD')"
                            class="w-full px-4 py-2 text-left hover:bg-sky-50 text-slate-700 hover:text-sky-700 flex items-center justify-between font-medium">
                        <span>30 Hari Terakhir</span>
                        <span class="text-[10px] text-slate-400">1 Bulan</span>
                    </button>
                    <button type="button" 
                            @click="selectPeriod('Agustus 2026 (Bulan Berjalan)', '1,250', '1.12', '450', 'Restoran Madani')"
                            class="w-full px-4 py-2 text-left hover:bg-sky-50 text-slate-700 hover:text-sky-700 flex items-center justify-between font-medium">
                        <span>Bulan Ini (Agustus 2026)</span>
                        <span class="text-[10px] text-slate-400">Bln Ini</span>
                    </button>
                    <button type="button" 
                            @click="selectPeriod('Kuartal 3 - 2026 (Juli - Sep)', '1,560', '1.18', '720', 'Supplier Ekspor')"
                            class="w-full px-4 py-2 text-left hover:bg-sky-50 text-slate-700 hover:text-sky-700 flex items-center justify-between font-medium">
                        <span>Kuartal Ini (Q3 2026)</span>
                        <span class="text-[10px] text-slate-400">Q3</span>
                    </button>
                    <button type="button" 
                            @click="selectPeriod('Tahun Anggaran 2026', '5,840', '1.14', '2,400', 'Semua Mitra')"
                            class="w-full px-4 py-2 text-left hover:bg-sky-50 text-slate-700 hover:text-sky-700 flex items-center justify-between font-medium">
                        <span>Tahun 2026 Penuh</span>
                        <span class="text-[10px] text-slate-400">2026</span>
                    </button>
                </div>
            </div>

            <!-- Export Excel Button -->
            <button type="button"
                    @click="exportExcel()"
                    class="px-3.5 py-1.5 rounded-lg bg-[#0B6B2E] hover:bg-emerald-800 text-white font-bold text-xs flex items-center gap-2 shadow-xs hover:shadow-md active:scale-95 transition-all tracking-wide cursor-pointer">
                <i class="fa-solid fa-file-excel text-sm text-emerald-200"></i>
                <span>EKSPOR EXCEL</span>
            </button>

        </div>
    </div>

    <!-- 3 Top Metric KPI Cards Grid -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
        
        <!-- Card 1: Total Stok Ikan -->
        <div class="bg-[#DDF2FF] p-5 rounded-2xl border border-[#BEE3F8] shadow-xs flex flex-col justify-between hover:shadow-md transition-all">
            <div>
                <div class="flex items-center justify-between">
                    <span class="text-[11px] font-extrabold uppercase tracking-wider text-slate-600">TOTAL STOK IKAN</span>
                    <div class="w-9 h-9 rounded-xl bg-[#BEE3F8]/60 text-[#006699] flex items-center justify-center shadow-xs">
                        <i class="fa-solid fa-water text-base"></i>
                    </div>
                </div>
                <div class="mt-3">
                    <h3 class="text-3xl font-extrabold text-slate-900 tracking-tight">
                        <span x-text="metrics.totalStok">1,250</span> <span class="text-xs font-semibold text-slate-600">kg</span>
                    </h3>
                </div>
            </div>
            <div class="mt-4 flex items-center gap-1.5 text-xs font-semibold text-emerald-600">
                <i class="fa-solid fa-arrow-trend-up"></i>
                <span x-text="metrics.totalStokTrend">+4.2% dari minggu lalu</span>
            </div>
        </div>

        <!-- Card 2: FCR Rata-rata -->
        <div class="bg-[#DDF2FF] p-5 rounded-2xl border border-[#BEE3F8] shadow-xs flex flex-col justify-between hover:shadow-md transition-all">
            <div>
                <div class="flex items-center justify-between">
                    <span class="text-[11px] font-extrabold uppercase tracking-wider text-slate-600">FCR RATA-RATA</span>
                    <div class="w-9 h-9 rounded-xl bg-[#BEE3F8]/60 text-[#006699] flex items-center justify-center shadow-xs">
                        <i class="fa-solid fa-chart-line text-base"></i>
                    </div>
                </div>
                <div class="mt-3">
                    <h3 class="text-3xl font-extrabold text-slate-900 tracking-tight" x-text="metrics.fcr">1.12</h3>
                </div>
            </div>
            <div class="mt-4 flex items-center gap-1.5 text-xs font-semibold text-emerald-600">
                <i class="fa-regular fa-circle-check"></i>
                <span x-text="metrics.fcrStatus">Efisiensi Pakan Optimal</span>
            </div>
        </div>

        <!-- Card 3: Target Panen -->
        <div class="bg-[#DDF2FF] p-5 rounded-2xl border border-[#BEE3F8] shadow-xs flex flex-col justify-between hover:shadow-md transition-all">
            <div>
                <div class="flex items-center justify-between">
                    <span class="text-[11px] font-extrabold uppercase tracking-wider text-slate-600">TARGET PANEN</span>
                    <div class="w-9 h-9 rounded-xl bg-[#BEE3F8]/60 text-[#006699] flex items-center justify-center shadow-xs">
                        <i class="fa-regular fa-calendar text-base"></i>
                    </div>
                </div>
                <div class="mt-3">
                    <h3 class="text-3xl font-extrabold text-slate-900 tracking-tight">
                        <span x-text="metrics.targetPanen">450</span> <span class="text-xs font-semibold text-slate-600">kg</span>
                    </h3>
                </div>
            </div>
            <div class="mt-4 flex items-center gap-2 text-xs">
                <span class="px-2 py-0.5 rounded bg-[#E53E3E] text-white text-[10px] font-extrabold tracking-wider uppercase" x-text="metrics.targetPanenTag">CATATAN H-3</span>
                <span class="text-slate-600 font-medium truncate" x-text="metrics.targetPanenNote">Restoran Madani</span>
            </div>
        </div>

    </div>

    <!-- Main Chart Section: Analisis Konsumsi Pakan -->
    <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-xs">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
            <div>
                <h3 class="text-base font-bold text-slate-900">Analisis Konsumsi Pakan</h3>
                <p class="text-xs text-slate-400 mt-0.5">Perbandingan pelet komersial dan pakan dedaunan alami.</p>
            </div>
            
            <div class="flex items-center gap-5 text-xs font-medium flex-wrap">
                <!-- Legend 1 -->
                <div class="flex items-center gap-2 text-slate-700">
                    <span class="w-2.5 h-2.5 rounded-full bg-[#0B2570]"></span>
                    <span>Pelet Komersial</span>
                </div>
                <!-- Legend 2 -->
                <div class="flex items-center gap-2 text-slate-700">
                    <span class="w-2.5 h-2.5 rounded-full bg-[#10B981]"></span>
                    <span>Dedaunan Organik</span>
                </div>
                
                <!-- Filter Dropdown Chart -->
                <div class="relative" @click.outside="chartDropdownOpen = false">
                    <button type="button" 
                            @click="chartDropdownOpen = !chartDropdownOpen"
                            class="flex items-center gap-2 px-3 py-1.5 rounded-lg border border-slate-200 bg-white text-slate-700 text-xs font-medium hover:bg-slate-50 cursor-pointer shadow-xs">
                        <span x-text="chartPeriodLabel">7 Hari Terakhir</span>
                        <i class="fa-solid fa-chevron-down text-[10px] text-slate-400 transition-transform" :class="chartDropdownOpen ? 'rotate-180' : ''"></i>
                    </button>

                    <div x-show="chartDropdownOpen" 
                         x-transition:enter="transition ease-out duration-150"
                         x-transition:enter-start="opacity-0 translate-y-1 scale-95"
                         x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                         x-transition:leave="transition ease-in duration-100"
                         x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                         x-transition:leave-end="opacity-0 translate-y-1 scale-95"
                         class="absolute right-0 mt-2 w-44 bg-white rounded-xl shadow-xl border border-slate-100 py-1 z-30 text-xs"
                         style="display: none;">
                        <button type="button" 
                                @click="chartPeriodLabel = '7 Hari Terakhir'; chartDropdownOpen = false; window.setChartData('7d'); triggerToast('Grafik: 7 Hari Terakhir', 'info');"
                                class="w-full px-3 py-2 text-left hover:bg-sky-50 text-slate-700 hover:text-sky-700 flex items-center justify-between">
                            <span>7 Hari Terakhir</span>
                        </button>
                        <button type="button" 
                                @click="chartPeriodLabel = '14 Hari Terakhir'; chartDropdownOpen = false; window.setChartData('14d'); triggerToast('Grafik: 14 Hari Terakhir', 'info');"
                                class="w-full px-3 py-2 text-left hover:bg-sky-50 text-slate-700 hover:text-sky-700 flex items-center justify-between">
                            <span>14 Hari Terakhir</span>
                        </button>
                        <button type="button" 
                                @click="chartPeriodLabel = '30 Hari Terakhir'; chartDropdownOpen = false; window.setChartData('30d'); triggerToast('Grafik: 30 Hari Terakhir', 'info');"
                                class="w-full px-3 py-2 text-left hover:bg-sky-50 text-slate-700 hover:text-sky-700 flex items-center justify-between">
                            <span>30 Hari Terakhir</span>
                        </button>
                        <button type="button" 
                                @click="chartPeriodLabel = 'Bulan Ini (Agustus)'; chartDropdownOpen = false; window.setChartData('month'); triggerToast('Grafik: Bulan Ini', 'info');"
                                class="w-full px-3 py-2 text-left hover:bg-sky-50 text-slate-700 hover:text-sky-700 flex items-center justify-between">
                            <span>Bulan Ini (Agustus)</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div class="h-64 w-full relative">
            <canvas id="dashboardMainChart"></canvas>
        </div>
    </div>

    <!-- Table Section: Daftar Stok Siap Panen -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-visible" @click.outside="activeActionMenu = null">
        <div class="p-5 border-b border-slate-100 flex items-center justify-between">
            <div>
                <h3 class="text-base font-bold text-slate-900">Daftar Stok Siap Panen</h3>
                <p class="text-xs text-slate-400 mt-0.5">Kolam pembesaran yang telah mencapai kriteria bobot panen.</p>
            </div>
            <a href="{{ route('pembudidaya') }}" class="text-xs font-bold text-[#0055CC] hover:underline flex items-center gap-1">
                <span>Lihat Semua Kolam</span>
                <i class="fa-solid fa-arrow-right text-[10px]"></i>
            </a>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50/60 border-b border-slate-100 text-[10px] font-extrabold text-slate-400 uppercase tracking-wider">
                        <th class="py-3 px-6">KOLAM ID</th>
                        <th class="py-3 px-6">JENIS IKAN</th>
                        <th class="py-3 px-6">ESTIMASI BOBOT</th>
                        <th class="py-3 px-6">TUJUAN ALOKASI</th>
                        <th class="py-3 px-6">STATUS</th>
                        <th class="py-3 px-6 text-right">AKSI</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-xs font-medium text-slate-700">
                    <template x-for="(item, idx) in stokList" :key="item.id">
                        <tr class="hover:bg-slate-50/70 transition-colors group">
                            <td class="py-3.5 px-6">
                                <button type="button" @click="openDetail(item)" class="font-bold text-[#0055CC] hover:underline flex items-center gap-1.5">
                                    <i class="fa-solid fa-water text-xs text-sky-400"></i>
                                    <span x-text="item.id"></span>
                                </button>
                            </td>
                            <td class="py-3.5 px-6 font-semibold text-slate-700" x-text="item.jenisIkan"></td>
                            <td class="py-3.5 px-6 font-bold text-slate-900" x-text="item.bobot"></td>
                            <td class="py-3.5 px-6 text-slate-600" x-text="item.tujuan"></td>
                            <td class="py-3.5 px-6">
                                <button type="button" 
                                        @click="toggleStatus(item)"
                                        title="Klik untuk ubah status secara cepat"
                                        class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[11px] font-extrabold cursor-pointer transition-transform active:scale-95"
                                        :class="{
                                            'bg-[#C6F6D5] text-[#22543D] hover:bg-emerald-200': item.status === 'READY',
                                            'bg-[#EBF8FF] text-[#2B6CB0] hover:bg-sky-200': item.status === 'HOLD',
                                            'bg-amber-100 text-amber-800 hover:bg-amber-200': item.status === 'PANEN'
                                        }">
                                    <span class="w-1.5 h-1.5 rounded-full" 
                                          :class="{
                                              'bg-[#38A169]': item.status === 'READY',
                                              'bg-[#3182CE]': item.status === 'HOLD',
                                              'bg-amber-500': item.status === 'PANEN'
                                          }"></span>
                                    <span x-text="item.status"></span>
                                </button>
                            </td>
                            <td class="py-3.5 px-6 text-right relative">
                                <!-- Action Button -->
                                <button type="button" 
                                        @click.stop="activeActionMenu = (activeActionMenu === item.id ? null : item.id)"
                                        class="text-slate-400 hover:text-slate-700 p-1.5 rounded-lg hover:bg-slate-100 transition-colors">
                                    <i class="fa-solid fa-ellipsis-vertical text-sm"></i>
                                </button>

                                <!-- Dropdown Menu Per Baris -->
                                <div x-show="activeActionMenu === item.id" 
                                     x-transition:enter="transition ease-out duration-150"
                                     x-transition:enter-start="opacity-0 translate-y-1 scale-95"
                                     x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                                     x-transition:leave="transition ease-in duration-100"
                                     x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                                     x-transition:leave-end="opacity-0 translate-y-1 scale-95"
                                     class="absolute right-6 mt-1 w-48 bg-white rounded-2xl shadow-xl border border-slate-100 py-1.5 z-40 text-left text-xs"
                                     style="display: none;">
                                    <button type="button" 
                                            @click="openDetail(item)"
                                            class="w-full px-3.5 py-2 hover:bg-sky-50 text-slate-700 hover:text-sky-700 flex items-center gap-2 font-medium">
                                        <i class="fa-regular fa-eye text-sky-500 text-xs w-4"></i>
                                        <span>Lihat Detail Kolam</span>
                                    </button>
                                    <button type="button" 
                                            @click="toggleStatus(item)"
                                            class="w-full px-3.5 py-2 hover:bg-sky-50 text-slate-700 hover:text-sky-700 flex items-center gap-2 font-medium">
                                        <i class="fa-solid fa-rotate text-emerald-500 text-xs w-4"></i>
                                        <span>Ganti Status</span>
                                    </button>
                                    <button type="button" 
                                            @click="alokasikanMitra(item)"
                                            class="w-full px-3.5 py-2 hover:bg-sky-50 text-slate-700 hover:text-sky-700 flex items-center gap-2 font-medium">
                                        <i class="fa-regular fa-handshake text-indigo-500 text-xs w-4"></i>
                                        <span>Alokasikan Mitra</span>
                                    </button>
                                    <div class="my-1 border-t border-slate-100"></div>
                                    <button type="button" 
                                            @click="cetakSPK(item)"
                                            class="w-full px-3.5 py-2 hover:bg-rose-50 text-slate-700 hover:text-rose-700 flex items-center gap-2 font-medium">
                                        <i class="fa-solid fa-print text-rose-500 text-xs w-4"></i>
                                        <span>Cetak Surat Jalan</span>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Bottom 2 Quick Info Widgets -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
        
        <!-- Widget 1: Stok Gudang Pakan -->
        <div class="bg-[#051B44] text-white p-5 rounded-2xl shadow-xs flex items-center justify-between gap-4">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-white/10 border border-white/20 flex items-center justify-center shrink-0">
                    <i class="fa-regular fa-clipboard text-xl text-white"></i>
                </div>
                <div>
                    <h4 class="text-sm font-bold text-white">Stok Gudang Pakan</h4>
                    <p class="text-xs text-sky-100/80 mt-0.5">Total konsumsi tercatat <span class="font-bold text-white" x-text="metrics.totalPakan">1,240</span> kg. Estimasi persediaan pakan aman.</p>
                </div>
            </div>
            <a href="{{ route('log-pakan') }}" class="px-3 py-1.5 rounded-xl bg-white/10 hover:bg-white/20 text-white text-xs font-bold transition-colors shrink-0">
                Kelola Pakan
            </a>
        </div>

        <!-- Widget 2: Kualitas Air -->
        <div class="bg-[#E2E8F0] text-slate-900 p-5 rounded-2xl shadow-xs flex items-center justify-between gap-4">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-slate-300/60 flex items-center justify-center shrink-0">
                    <i class="fa-solid fa-droplet text-xl text-slate-700"></i>
                </div>
                <div>
                    <h4 class="text-sm font-bold text-slate-900">Kualitas Air</h4>
                    <p class="text-xs text-slate-600 mt-0.5">Rata-rata pH seluruh unit kolam normal (<span class="font-bold text-slate-800" x-text="metrics.avgPh">7.3</span> pH).</p>
                </div>
            </div>
            <button type="button" @click="triggerToast('Semua sensor IoT Kolam beroperasi optimal!', 'success')" class="px-3 py-1.5 rounded-xl bg-white text-slate-700 hover:bg-slate-100 text-xs font-bold transition-colors shadow-xs shrink-0">
                Cek Sensor
            </button>
        </div>

    </div>

    <!-- Modal Popup Detail Kolam & Siap Panen -->
    <div x-show="detailModalOpen" 
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 backdrop-blur-xs p-4"
         style="display: none;">
        
        <div @click.outside="detailModalOpen = false" 
             x-show="detailModalOpen"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 scale-95 translate-y-4"
             x-transition:enter-end="opacity-100 scale-100 translate-y-0"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100 scale-100 translate-y-0"
             x-transition:leave-end="opacity-0 scale-95 translate-y-4"
             class="bg-white w-full max-w-lg rounded-3xl shadow-2xl border border-slate-100 overflow-hidden">
            
            <!-- Modal Header -->
            <div class="px-6 py-5 bg-[#051B44] text-white flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-white/10 flex items-center justify-center text-sky-300">
                        <i class="fa-solid fa-water text-lg"></i>
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-white leading-tight" x-text="'Rincian ' + (selectedKolam ? selectedKolam.id : 'Kolam')"></h3>
                        <p class="text-xs text-sky-200" x-text="selectedKolam ? selectedKolam.jenisIkan : ''"></p>
                    </div>
                </div>
                <button type="button" @click="detailModalOpen = false" class="text-white/70 hover:text-white p-1 rounded-lg">
                    <i class="fa-solid fa-xmark text-lg"></i>
                </button>
            </div>

            <!-- Modal Body -->
            <div class="p-6 space-y-5 text-xs" x-if="selectedKolam">
                
                <!-- 4 Telemetry Metrics Grid -->
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 text-center">
                    <div class="p-3 bg-slate-50 rounded-2xl border border-slate-100">
                        <span class="text-[10px] text-slate-400 font-bold uppercase block">ESTIMASI BOBOT</span>
                        <span class="text-base font-extrabold text-slate-900 mt-1 block" x-text="selectedKolam?.bobot"></span>
                    </div>
                    <div class="p-3 bg-slate-50 rounded-2xl border border-slate-100">
                        <span class="text-[10px] text-slate-400 font-bold uppercase block">PH AIR</span>
                        <span class="text-base font-extrabold text-emerald-600 mt-1 block" x-text="selectedKolam?.ph"></span>
                    </div>
                    <div class="p-3 bg-slate-50 rounded-2xl border border-slate-100">
                        <span class="text-[10px] text-slate-400 font-bold uppercase block">SUHU AIR</span>
                        <span class="text-base font-extrabold text-sky-600 mt-1 block" x-text="selectedKolam?.suhu"></span>
                    </div>
                    <div class="p-3 bg-slate-50 rounded-2xl border border-slate-100">
                        <span class="text-[10px] text-slate-400 font-bold uppercase block">FCR KOLAM</span>
                        <span class="text-base font-extrabold text-indigo-600 mt-1 block" x-text="selectedKolam?.fcr"></span>
                    </div>
                </div>

                <!-- Info Table List -->
                <div class="space-y-2.5 bg-slate-50/70 p-4 rounded-2xl border border-slate-100">
                    <div class="flex justify-between py-1 border-b border-slate-200/60">
                        <span class="text-slate-500 font-semibold">Tujuan Alokasi Mitra:</span>
                        <span class="font-bold text-slate-900" x-text="selectedKolam?.tujuan"></span>
                    </div>
                    <div class="flex justify-between py-1 border-b border-slate-200/60">
                        <span class="text-slate-500 font-semibold">Jumlah Populasi Ikan:</span>
                        <span class="font-bold text-slate-900" x-text="selectedKolam?.populasi"></span>
                    </div>
                    <div class="flex justify-between py-1 border-b border-slate-200/60">
                        <span class="text-slate-500 font-semibold">Tanggal Tebar Bibit:</span>
                        <span class="font-bold text-slate-900" x-text="selectedKolam?.tglTebar"></span>
                    </div>
                    <div class="flex justify-between py-1">
                        <span class="text-slate-500 font-semibold">Status Operasional:</span>
                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold"
                              :class="{
                                  'bg-emerald-100 text-emerald-800': selectedKolam?.status === 'READY',
                                  'bg-sky-100 text-sky-800': selectedKolam?.status === 'HOLD',
                                  'bg-amber-100 text-amber-800': selectedKolam?.status === 'PANEN'
                              }" x-text="selectedKolam?.status"></span>
                    </div>
                </div>

                <!-- Actions Footer inside Modal -->
                <div class="flex items-center justify-end gap-3 pt-2">
                    <button type="button" 
                            @click="toggleStatus(selectedKolam)"
                            class="px-4 py-2.5 rounded-xl border border-slate-300 text-slate-700 font-bold hover:bg-slate-100 transition-colors">
                        Ganti Status
                    </button>
                    <button type="button" 
                            @click="cetakSPK(selectedKolam); detailModalOpen = false;"
                            class="px-4 py-2.5 rounded-xl bg-[#051B44] text-white font-bold hover:bg-navy-900 transition-colors flex items-center gap-2 shadow-xs">
                        <i class="fa-solid fa-print text-xs"></i>
                        <span>Cetak Surat Jalan</span>
                    </button>
                </div>

            </div>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
    function dashboardData() {
        return {
            datePickerOpen: false,
            periodLabel: '01 Agustus - 07 Agustus 2026',
            chartDropdownOpen: false,
            chartPeriodLabel: '7 Hari Terakhir',
            activeActionMenu: null,
            detailModalOpen: false,
            selectedKolam: null,
            
            // Toast State
            toast: {
                show: false,
                message: '',
                type: 'success'
            },
            triggerToast(msg, type = 'success') {
                this.toast.message = msg;
                this.toast.type = type;
                this.toast.show = true;
                setTimeout(() => { this.toast.show = false; }, 3500);
            },

            // KPI Metrics
            metrics: {!! isset($metrics) ? json_encode($metrics) : json_encode([
                'totalStok' => '1,250',
                'totalStokTrend' => '+4.2% dari minggu lalu',
                'fcr' => '1.12',
                'fcrStatus' => 'Efisiensi Pakan Optimal',
                'targetPanen' => '450',
                'targetPanenNote' => 'Restoran Madani',
                'targetPanenTag' => 'CATATAN H-3'
            ]) !!},

            // Data Stok Kolam
            stokList: {!! isset($stokList) && count($stokList) > 0 ? json_encode($stokList) : json_encode([
                [
                    'id' => 'Kolam A1',
                    'jenisIkan' => 'Ikan Nila Hitam Super',
                    'bobot' => '150 kg',
                    'bobotNum' => 150,
                    'tujuan' => 'Restoran Madani',
                    'status' => 'READY',
                    'ph' => '7.3',
                    'suhu' => '28.5°C',
                    'populasi' => '2,500 Ekor',
                    'tglTebar' => '15 Mei 2026',
                    'fcr' => '1.10'
                ]
            ]) !!},

            // Pilih Filter Periode Tanggal
            selectPeriod(label, stok, fcr, target, note) {
                this.periodLabel = label;
                this.datePickerOpen = false;
                this.metrics.totalStok = stok;
                this.metrics.fcr = fcr;
                this.metrics.targetPanen = target;
                this.metrics.targetPanenNote = note;
                this.triggerToast('Periode diperbarui: ' + label, 'info');
                window.updateChartPeriod(label);
            },

            // Buka Detail Modal Kolam
            openDetail(item) {
                this.selectedKolam = item;
                this.activeActionMenu = null;
                this.detailModalOpen = true;
            },

            // Ubah Status Kolam (READY / HOLD / PANEN)
            toggleStatus(item) {
                if (item.status === 'READY') {
                    item.status = 'HOLD';
                } else if (item.status === 'HOLD') {
                    item.status = 'PANEN';
                } else {
                    item.status = 'READY';
                }
                this.activeActionMenu = null;
                this.triggerToast(`Status ${item.id} diubah menjadi [${item.status}]`, 'success');
            },

            // Alokasi Mitra Baru
            alokasikanMitra(item) {
                const mitraBaru = prompt(`Ubah tujuan alokasi mitra untuk ${item.id}:`, item.tujuan);
                if (mitraBaru && mitraBaru.trim() !== '') {
                    item.tujuan = mitraBaru.trim();
                    this.triggerToast(`Alokasi ${item.id} diperbarui ke ${item.tujuan}`, 'success');
                }
                this.activeActionMenu = null;
            },

            // Cetak Dokumen / Surat Jalan
            cetakSPK(item) {
                this.activeActionMenu = null;
                this.triggerToast(`Mempersiapkan Dokumen Surat Pengantar Panen untuk ${item.id}...`, 'info');
                setTimeout(() => {
                    window.print();
                }, 500);
            },

            // Ekspor Excel / CSV Laporan Dashboard
            exportExcel() {
                this.triggerToast('Menghasilkan file Excel Laporan Dashboard...', 'info');

                const now = new Date();
                const dateStr = now.toLocaleDateString('id-ID', { year: 'numeric', month: 'long', day: 'numeric', hour: '2-digit', minute: '2-digit' });

                let csvContent = '\uFEFF'; // UTF-8 BOM untuk Microsoft Excel
                csvContent += 'LAPORAN OPERASIONAL SISTEM INFORMASI MANAJEMEN BUDIDAYA IKAN (SIM-BUDIDAYA)\r\n';
                csvContent += `Tanggal Cetak: "${dateStr}"\r\n`;
                csvContent += `Periode Data: "${this.periodLabel}"\r\n`;
                csvContent += `Diunduh Oleh: "Manajer SIM-Budidaya"\r\n\r\n`;

                // Section 1: KPI Summary
                csvContent += '--- 1. RINGKASAN EKSEKUTIF (KPI) ---\r\n';
                csvContent += 'Indikator,Nilai,Keterangan\r\n';
                csvContent += `"Total Stok Ikan","${this.metrics.totalStok} kg","${this.metrics.totalStokTrend}"\r\n`;
                csvContent += `"FCR Rata-rata","${this.metrics.fcr}","${this.metrics.fcrStatus}"\r\n`;
                csvContent += `"Target Panen Terdekat","${this.metrics.targetPanen} kg","${this.metrics.targetPanenNote} (${this.metrics.targetPanenTag})"\r\n`;
                csvContent += `"Stok Gudang Pakan","1240 kg","Estimasi cukup 14 hari"\r\n`;
                csvContent += `"Status Kualitas Air","Normal (pH 7.2 - 7.5)","Semua kolam optimal"\r\n\r\n`;

                // Section 2: Daftar Kolam Siap Panen
                csvContent += '--- 2. DAFTAR STOK SIAP PANEN ---\r\n';
                csvContent += 'KOLAM ID,JENIS IKAN,ESTIMASI BOBOT (KG),TUJUAN ALOKASI,STATUS,PH AIR,SUHU AIR,POPULASI,TGL TEBAR,FCR\r\n';
                this.stokList.forEach(k => {
                    csvContent += `"${k.id}","${k.jenisIkan}","${k.bobotNum}","${k.tujuan}","${k.status}","${k.ph}","${k.suhu}","${k.populasi}","${k.tglTebar}","${k.fcr}"\r\n`;
                });
                csvContent += '\r\n';

                // Section 3: Rekap Konsumsi Pakan
                csvContent += '--- 3. REKAP KONSUMSI PAKAN (7 HARI TERAKHIR) ---\r\n';
                csvContent += 'HARI,PELET KOMERSIAL (KG),DEDAUNAN ORGANIK (KG),TOTAL KONSUMSI (KG)\r\n';
                const pakanRekap = [
                    { hari: 'Senin', pelet: 18, daun: 12 },
                    { hari: 'Selasa', pelet: 24, daun: 16 },
                    { hari: 'Rabu', pelet: 22, daun: 20 },
                    { hari: 'Kamis', pelet: 30, daun: 18 },
                    { hari: 'Jumat', pelet: 35, daun: 22 },
                    { hari: 'Sabtu', pelet: 28, daun: 24 },
                    { hari: 'Minggu', pelet: 32, daun: 21 }
                ];
                pakanRekap.forEach(p => {
                    csvContent += `"${p.hari}","${p.pelet}","${p.daun}","${p.pelet + p.daun}"\r\n`;
                });

                // Trigger file download
                const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
                const url = URL.createObjectURL(blob);
                const link = document.createElement('a');
                const fileName = `Laporan_Dashboard_Budidaya_${now.toISOString().slice(0, 10)}.csv`;
                link.setAttribute('href', url);
                link.setAttribute('download', fileName);
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);
                URL.revokeObjectURL(url);

                setTimeout(() => {
                    this.triggerToast(`File [${fileName}] berhasil diunduh!`, 'success');
                }, 600);
            }
        };
    }

    let dashboardChartInstance = null;

    const chartDatasets = {
        '7d': {
            labels: ['Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab', 'Min'],
            pelet: [18, 24, 22, 30, 35, 28, 32],
            daun: [12, 16, 20, 18, 22, 24, 21]
        },
        '14d': {
            labels: ['H-13', 'H-11', 'H-9', 'H-7', 'H-5', 'H-3', 'H-1'],
            pelet: [20, 22, 25, 28, 30, 32, 35],
            daun: [14, 15, 18, 19, 21, 23, 22]
        },
        '30d': {
            labels: ['Mgg 1', 'Mgg 2', 'Mgg 3', 'Mgg 4'],
            pelet: [130, 155, 170, 185],
            daun: [90, 110, 125, 130]
        },
        'month': {
            labels: ['Mgg 1', 'Mgg 2', 'Mgg 3', 'Mgg 4'],
            pelet: [145, 160, 168, 192],
            daun: [95, 105, 118, 135]
        }
    };

    window.setChartData = function(periodKey) {
        if (!dashboardChartInstance) return;
        const data = chartDatasets[periodKey] || chartDatasets['7d'];
        dashboardChartInstance.data.labels = data.labels;
        dashboardChartInstance.data.datasets[0].data = data.pelet;
        dashboardChartInstance.data.datasets[1].data = data.daun;
        dashboardChartInstance.update('active');
    };

    window.updateChartPeriod = function(periodName) {
        if (periodName.includes('30') || periodName.includes('Bulan')) {
            window.setChartData('30d');
        } else if (periodName.includes('14')) {
            window.setChartData('14d');
        } else {
            window.setChartData('7d');
        }
    };

    document.addEventListener('DOMContentLoaded', function() {
        const canvas = document.getElementById('dashboardMainChart');
        if (!canvas) return;

        const ctx = canvas.getContext('2d');
        dashboardChartInstance = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: chartDatasets['7d'].labels,
                datasets: [
                    {
                        label: 'Pelet Komersial',
                        data: chartDatasets['7d'].pelet,
                        backgroundColor: '#0B2570',
                        borderRadius: 6,
                        borderSkipped: false,
                        barPercentage: 0.6,
                        categoryPercentage: 0.5
                    },
                    {
                        label: 'Dedaunan Organik',
                        data: chartDatasets['7d'].daun,
                        backgroundColor: '#10B981',
                        borderRadius: 6,
                        borderSkipped: false,
                        barPercentage: 0.6,
                        categoryPercentage: 0.5
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return context.dataset.label + ': ' + context.raw + ' kg';
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return value + ' kg';
                            },
                            font: { family: 'Plus Jakarta Sans', size: 11, weight: '500' },
                            color: '#94A3B8'
                        },
                        grid: {
                            color: '#F1F5F9'
                        },
                        border: {
                            display: false
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        },
                        ticks: {
                            font: { family: 'Plus Jakarta Sans', size: 11, weight: '500' },
                            color: '#94A3B8'
                        },
                        border: {
                            display: false
                        }
                    }
                }
            }
        });
    });
</script>
@endpush


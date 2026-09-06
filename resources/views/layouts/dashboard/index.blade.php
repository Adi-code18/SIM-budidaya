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
            
            <!-- Date / Period Calendar Filter Popover -->
            <div class="relative" @click.outside="datePickerOpen = false">
                <button type="button" 
                        @click="datePickerOpen = !datePickerOpen"
                        class="flex items-center gap-2 px-3.5 py-1.5 rounded-lg border border-slate-300 bg-white text-xs font-semibold text-slate-700 shadow-xs hover:bg-slate-50 hover:border-slate-400 transition-all cursor-pointer">
                    <i class="fa-regular fa-calendar-days text-[#0077C6] text-xs"></i>
                    <span x-text="periodLabel">Minggu 1, Ags 2026</span>
                    <i class="fa-solid fa-chevron-down text-[10px] text-slate-400 ml-1 transition-transform" :class="datePickerOpen ? 'rotate-180' : ''"></i>
                </button>

                <!-- Popover Calendar-Like Container -->
                <div x-show="datePickerOpen" 
                     x-transition:enter="transition ease-out duration-150"
                     x-transition:enter-start="opacity-0 translate-y-1 scale-95"
                     x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                     x-transition:leave="transition ease-in duration-100"
                     x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                     x-transition:leave-end="opacity-0 translate-y-1 scale-95"
                     class="absolute right-0 mt-2 w-80 bg-white rounded-3xl shadow-2xl border border-slate-200 p-4 z-40 text-xs"
                     style="display: none;">
                    
                    <!-- Top Mode Switcher (Mingguan | Bulanan | Tahunan) -->
                    <div class="flex items-center bg-slate-100 p-1 rounded-2xl mb-3.5">
                        <button type="button" 
                                @click="pickerMode = 'minggu'"
                                :class="pickerMode === 'minggu' ? 'bg-[#051B44] text-white shadow-xs font-bold' : 'text-slate-600 hover:text-slate-900 font-semibold'"
                                class="flex-1 py-1.5 rounded-xl text-center text-xs transition-all">
                            Mingguan
                        </button>
                        <button type="button" 
                                @click="pickerMode = 'bulan'"
                                :class="pickerMode === 'bulan' ? 'bg-[#051B44] text-white shadow-xs font-bold' : 'text-slate-600 hover:text-slate-900 font-semibold'"
                                class="flex-1 py-1.5 rounded-xl text-center text-xs transition-all">
                            Bulanan
                        </button>
                        <button type="button" 
                                @click="pickerMode = 'tahun'"
                                :class="pickerMode === 'tahun' ? 'bg-[#051B44] text-white shadow-xs font-bold' : 'text-slate-600 hover:text-slate-900 font-semibold'"
                                class="flex-1 py-1.5 rounded-xl text-center text-xs transition-all">
                            Tahunan
                        </button>
                    </div>

                    <!-- Header Navigasi Tahun (Untuk Mode Minggu & Bulan) -->
                    <div x-show="pickerMode !== 'tahun'" class="flex items-center justify-between px-2 pb-2.5 mb-2.5 border-b border-slate-100">
                        <button type="button" 
                                @click="prevYear()"
                                :disabled="pickerYear <= minYear"
                                :class="pickerYear <= minYear ? 'opacity-30 cursor-not-allowed' : 'hover:bg-slate-100 text-slate-700'"
                                class="w-7 h-7 rounded-lg flex items-center justify-center transition-colors">
                            <i class="fa-solid fa-chevron-left text-xs"></i>
                        </button>
                        <span class="font-extrabold text-sm text-[#051B44]" x-text="'Tahun ' + pickerYear"></span>
                        <button type="button" 
                                @click="nextYear()"
                                :disabled="pickerYear >= currentYear"
                                :class="pickerYear >= currentYear ? 'opacity-30 cursor-not-allowed' : 'hover:bg-slate-100 text-slate-700'"
                                class="w-7 h-7 rounded-lg flex items-center justify-center transition-colors">
                            <i class="fa-solid fa-chevron-right text-xs"></i>
                        </button>
                    </div>

                    <!-- TAB 1: MODE MINGGUAN -->
                    <div x-show="pickerMode === 'minggu'" class="space-y-3">
                        <!-- Pilih Bulan Horizontal Scroller -->
                        <div class="flex items-center gap-1 overflow-x-auto pb-1.5 scrollbar-thin">
                            <template x-for="m in monthList" :key="m.num">
                                <button type="button"
                                        @click="isMonthAvailable(m.num, pickerYear) && (pickerMonth = m.num)"
                                        :disabled="!isMonthAvailable(m.num, pickerYear)"
                                        :class="{
                                            'bg-[#051B44] text-white font-bold shadow-xs': pickerMonth === m.num && isMonthAvailable(m.num, pickerYear),
                                            'bg-slate-50 text-slate-700 hover:bg-sky-50 font-medium': pickerMonth !== m.num && isMonthAvailable(m.num, pickerYear),
                                            'opacity-30 cursor-not-allowed bg-slate-50 text-slate-400': !isMonthAvailable(m.num, pickerYear)
                                        }"
                                        class="px-2.5 py-1 rounded-lg text-[11px] shrink-0 transition-all"
                                        x-text="m.short">
                                </button>
                            </template>
                        </div>

                        <!-- Daftar Minggu di Bulan Terpilih -->
                        <div class="space-y-1.5 pt-1">
                            <div class="text-[10px] font-bold text-slate-400 uppercase tracking-wider px-1">
                                PILIH MINGGU (<span x-text="getMonthName(pickerMonth) + ' ' + pickerYear"></span>)
                            </div>
                            <template x-for="w in getWeeks(pickerMonth, pickerYear)" :key="w.index">
                                <button type="button"
                                        @click="w.available && applyWeek(w)"
                                        :disabled="!w.available"
                                        :class="{
                                            'border-sky-500 bg-sky-50/80 font-bold text-[#0055CC]': selectedPeriodKey === 'w_' + pickerYear + '_' + pickerMonth + '_' + w.index,
                                            'border-slate-100 hover:border-sky-200 hover:bg-slate-50 text-slate-700': selectedPeriodKey !== 'w_' + pickerYear + '_' + pickerMonth + '_' + w.index && w.available,
                                            'opacity-35 cursor-not-allowed border-dashed border-slate-200 bg-slate-50/50 text-slate-400': !w.available
                                        }"
                                        class="w-full p-2.5 rounded-xl border flex items-center justify-between text-left transition-all">
                                    <div class="flex items-center gap-2">
                                        <i class="fa-solid fa-calendar-week text-xs" :class="w.available ? 'text-sky-600' : 'text-slate-300'"></i>
                                        <span class="text-xs" x-text="w.label"></span>
                                    </div>
                                    <div class="flex items-center gap-1.5">
                                        <span class="text-[10px] font-mono text-slate-400" x-text="w.range"></span>
                                        <span x-show="!w.available" class="text-[9px] px-1.5 py-0.5 rounded bg-slate-100 text-slate-400 font-semibold">Belum Ada Data</span>
                                    </div>
                                </button>
                            </template>
                        </div>
                    </div>

                    <!-- TAB 2: MODE BULANAN -->
                    <div x-show="pickerMode === 'bulan'" class="space-y-2">
                        <div class="text-[10px] font-bold text-slate-400 uppercase tracking-wider px-1 mb-1">
                            PILIH BULAN REKAP (<span x-text="pickerYear"></span>)
                        </div>
                        <div class="grid grid-cols-3 gap-2">
                            <template x-for="m in monthList" :key="m.num">
                                <button type="button"
                                        @click="isMonthAvailable(m.num, pickerYear) && applyMonth(m.num, pickerYear)"
                                        :disabled="!isMonthAvailable(m.num, pickerYear)"
                                        :class="{
                                            'border-sky-500 bg-sky-50/80 font-bold text-[#0055CC]': selectedPeriodKey === 'm_' + pickerYear + '_' + m.num,
                                            'border-slate-100 hover:border-sky-200 hover:bg-slate-50 text-slate-700': selectedPeriodKey !== 'm_' + pickerYear + '_' + m.num && isMonthAvailable(m.num, pickerYear),
                                            'opacity-35 cursor-not-allowed border-dashed border-slate-200 bg-slate-50/50 text-slate-400': !isMonthAvailable(m.num, pickerYear)
                                        }"
                                        class="p-2.5 rounded-xl border text-center transition-all flex flex-col items-center justify-center">
                                    <span class="text-xs font-bold" x-text="m.name"></span>
                                    <span x-show="!isMonthAvailable(m.num, pickerYear)" class="text-[8px] text-slate-400 mt-0.5">Belum Ada Data</span>
                                    <span x-show="isMonthAvailable(m.num, pickerYear) && m.num === currentMonth && pickerYear === currentYear" class="text-[8px] text-emerald-600 font-extrabold mt-0.5">Bln Berjalan</span>
                                </button>
                            </template>
                        </div>
                    </div>

                    <!-- TAB 3: MODE TAHUNAN -->
                    <div x-show="pickerMode === 'tahun'" class="space-y-2">
                        <div class="text-[10px] font-bold text-slate-400 uppercase tracking-wider px-1 mb-1">
                            PILIH TAHUN REKAP
                        </div>
                        <div class="space-y-2">
                            <template x-for="y in yearList" :key="y.year">
                                <button type="button"
                                        @click="y.available && applyYear(y.year)"
                                        :disabled="!y.available"
                                        :class="{
                                            'border-sky-500 bg-sky-50/80 font-bold text-[#0055CC]': selectedPeriodKey === 'y_' + y.year,
                                            'border-slate-100 hover:border-sky-200 hover:bg-slate-50 text-slate-700': selectedPeriodKey !== 'y_' + y.year && y.available,
                                            'opacity-35 cursor-not-allowed border-dashed border-slate-200 bg-slate-50/50 text-slate-400': !y.available
                                        }"
                                        class="w-full p-3 rounded-xl border flex items-center justify-between text-left transition-all">
                                    <div>
                                        <div class="text-xs font-extrabold" x-text="'Tahun ' + y.year"></div>
                                        <div class="text-[10px] text-slate-400" x-text="y.description"></div>
                                    </div>
                                    <span class="px-2 py-0.5 rounded text-[10px] font-extrabold"
                                          :class="y.available ? (y.year === currentYear ? 'bg-emerald-100 text-emerald-800' : 'bg-sky-100 text-sky-800') : 'bg-slate-100 text-slate-400'"
                                          x-text="y.badge">
                                    </span>
                                </button>
                            </template>
                        </div>
                    </div>

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
                <span class="px-2 py-0.5 rounded bg-[#E53E3E] text-white text-[10px] font-extrabold tracking-wider uppercase" x-text="metrics.targetPanenTag">ORDER AKTIF</span>
                <span class="text-slate-600 font-medium truncate" x-text="metrics.targetPanenNote">Mitra Distribusi</span>
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

    <!-- Map Section: Peta Sebaran Titik Mitra Distributor (Leaflet.js) -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
        <div class="p-5 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-[#051B44] text-white flex items-center justify-center shadow-md shadow-[#051B44]/20 flex-shrink-0">
                    <i class="fa-solid fa-map-location-dot text-base"></i>
                </div>
                <div>
                    <h3 class="text-base font-bold text-slate-900">Peta Sebaran Titik Mitra Distributor</h3>
                    <p class="text-xs text-slate-400 mt-0.5">Pemetaan geolokasi outlet, restoran, dan eksportir mitra yang bekerjasama.</p>
                </div>
            </div>
            <div class="flex items-center gap-2">
                <!-- Toggle Satelit / Peta Jalan -->
                <button type="button" 
                        onclick="if(window.toggleDashboardMapLayer) window.toggleDashboardMapLayer()"
                        id="btnDashboardMapToggle"
                        class="px-3 py-1.5 rounded-xl bg-white border border-slate-200 hover:bg-slate-50 text-slate-800 text-xs font-bold shadow-xs transition-all flex items-center gap-1.5 cursor-pointer">
                    <i class="fa-solid fa-satellite text-sky-600" id="iconDashboardMapToggle"></i>
                    <span id="textDashboardMapToggle">Satelit HD</span>
                </button>

                <div class="hidden sm:flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-sky-50 border border-sky-200/60 text-[#051B44] text-xs font-bold">
                    <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                    <span>{{ count($mitraList ?? []) }} Titik Mitra Terdaftar</span>
                </div>
                <a href="{{ route('mitra') }}" class="px-3.5 py-1.5 rounded-xl border border-slate-200 hover:border-slate-300 bg-white text-xs font-bold text-[#0055CC] hover:bg-slate-50 transition-all flex items-center gap-1.5 shadow-xs">
                    <span>Kelola Mitra</span>
                    <i class="fa-solid fa-arrow-right text-[10px]"></i>
                </a>
            </div>
        </div>

        <!-- Leaflet Map Container & Side Info Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-12">
            <!-- Map Container -->
            <div class="lg:col-span-8 xl:col-span-9 relative min-h-[380px] lg:min-h-[440px] bg-slate-100 z-0">
                <div id="mitraDistributionMap" class="w-full h-full min-h-[380px] lg:min-h-[440px]"></div>
            </div>

            <!-- Side Mitra List Panel -->
            <div class="lg:col-span-4 xl:col-span-3 border-t lg:border-t-0 lg:border-l border-slate-100 bg-slate-50/50 p-4 sm:p-5 flex flex-col justify-between max-h-[440px] overflow-y-auto">
                <div>
                    <div class="flex items-center justify-between mb-3">
                        <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400">DAFTAR OUTLET MITRA</span>
                        <span class="text-[10px] font-bold text-sky-700 bg-sky-100 px-2 py-0.5 rounded-full">{{ count($mitraList ?? []) }} Lokasi</span>
                    </div>

                    <div class="space-y-2.5">
                        @forelse($mitraList as $idx => $mitra)
                        <div class="p-3 rounded-xl bg-white border border-slate-200/80 shadow-xs hover:border-sky-300 hover:shadow-md transition-all cursor-pointer group"
                             onclick="window.focusMitra({{ $mitra['lat'] }}, {{ $mitra['lng'] }}, '{{ addslashes($mitra['nama']) }}')">
                            <div class="flex items-start justify-between gap-2">
                                <div class="flex items-center gap-2">
                                    <span class="w-5 h-5 rounded-lg bg-[#051B44] text-white flex items-center justify-center text-[10px] font-bold shrink-0">
                                        {{ $idx + 1 }}
                                    </span>
                                    <h5 class="text-xs font-bold text-slate-800 group-hover:text-[#0055CC] transition-colors leading-snug">
                                        {{ $mitra['nama'] }}
                                    </h5>
                                </div>
                                <span class="px-2 py-0.5 rounded text-[9px] font-extrabold bg-sky-50 text-sky-700 border border-sky-100 shrink-0">
                                    {{ $mitra['tipe'] }}
                                </span>
                            </div>
                            <p class="text-[11px] text-slate-500 mt-1.5 line-clamp-1 leading-tight flex items-center gap-1">
                                <i class="fa-solid fa-location-dot text-rose-500 text-[10px] shrink-0"></i>
                                <span>{{ $mitra['alamat'] }}</span>
                            </p>
                        </div>
                        @empty
                        <div class="p-4 text-center text-slate-400 text-xs">
                            Belum ada mitra distributor terdaftar.
                        </div>
                        @endforelse
                    </div>
                </div>

                <div class="pt-3 mt-3 border-t border-slate-200/60 text-center">
                    <span class="text-[10px] text-slate-400 font-medium">Klik pada salah satu mitra untuk fokus lokasi di peta</span>
                </div>
            </div>
        </div>
    </div>



</div>
@endsection

@push('scripts')
<style>
    .leaflet-popup-content-wrapper {
        border-radius: 1rem !important;
        box-shadow: 0 20px 25px -5px rgb(0 0 0 / 0.1), 0 8px 10px -6px rgb(0 0 0 / 0.1) !important;
        padding: 4px !important;
        border: 1px solid #E2E8F0 !important;
    }
    .leaflet-popup-tip {
        background: white !important;
    }
    .custom-leaflet-marker {
        display: flex !important;
        align-items: center;
        justify-content: center;
    }
</style>
<script>
    function dashboardData() {
        return {
            datePickerOpen: false,
            pickerMode: 'minggu', // 'minggu' | 'bulan' | 'tahun'
            currentYear: 2026,
            currentMonth: 8, // Agustus 2026
            minYear: 2025,
            pickerYear: 2026,
            pickerMonth: 8,
            selectedPeriodKey: 'w_2026_8_1',
            periodLabel: 'Minggu 1, Ags 2026',
            fullPeriodLabel: 'Minggu 1 (01 - 07 Ags 2026)',
            chartDropdownOpen: false,
            chartPeriodLabel: '7 Hari Terakhir',
            
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
            metrics: {!! json_encode($metrics ?? [
                'totalStok' => '0',
                'totalStokTrend' => 'Belum Ada Batch Aktif',
                'fcr' => '0.00',
                'fcrStatus' => 'Belum Ada Data Pakan',
                'targetPanen' => '0',
                'targetPanenNote' => 'Belum Ada Order',
                'targetPanenTag' => 'TIDAK ADA ORDER'
            ]) !!},

            // Data Mitra
            mitraList: {!! isset($mitraList) ? json_encode($mitraList) : '[]' !!},

            // Data Rekap Pakan Nyata dari Database
            pakanRekap: {!! isset($pakanRekap) ? json_encode($pakanRekap) : '[]' !!},

            // Daftar Bulan
            monthList: [
                { num: 1, name: 'Januari', short: 'Jan' },
                { num: 2, name: 'Februari', short: 'Feb' },
                { num: 3, name: 'Maret', short: 'Mar' },
                { num: 4, name: 'April', short: 'Apr' },
                { num: 5, name: 'Mei', short: 'Mei' },
                { num: 6, name: 'Juni', short: 'Jun' },
                { num: 7, name: 'Juli', short: 'Jul' },
                { num: 8, name: 'Agustus', short: 'Ags' },
                { num: 9, name: 'September', short: 'Sep' },
                { num: 10, name: 'Oktober', short: 'Okt' },
                { num: 11, name: 'November', short: 'Nov' },
                { num: 12, name: 'Desember', short: 'Des' },
            ],

            // Daftar Tahun
            yearList: [
                { year: 2025, available: true, badge: 'Arsip 2025', description: 'Rekap Tahunan Periode 2025' },
                { year: 2026, available: true, badge: 'Tahun Berjalan', description: 'Rekap Tahunan Periode 2026' },
                { year: 2027, available: false, badge: 'Belum Ada Data', description: 'Tahun Mendatang (Sistem Belum Berjalan)' },
                { year: 2028, available: false, badge: 'Belum Ada Data', description: 'Tahun Mendatang (Sistem Belum Berjalan)' },
            ],

            prevYear() {
                if (this.pickerYear > this.minYear) {
                    this.pickerYear--;
                    if (this.pickerYear < this.currentYear) {
                        this.pickerMonth = 12;
                    }
                }
            },

            nextYear() {
                if (this.pickerYear < this.currentYear) {
                    this.pickerYear++;
                    if (this.pickerYear === this.currentYear && this.pickerMonth > this.currentMonth) {
                        this.pickerMonth = this.currentMonth;
                    }
                }
            },

            getMonthName(mNum) {
                const m = this.monthList.find(x => x.num === mNum);
                return m ? m.name : '';
            },

            isMonthAvailable(mNum, year) {
                if (year < this.minYear || year > this.currentYear) return false;
                if (year === this.currentYear && mNum > this.currentMonth) return false;
                return true;
            },

            getWeeks(mNum, year) {
                const isCurrentMonth = (year === this.currentYear && mNum === this.currentMonth);
                const mName = this.getMonthName(mNum);
                const lastDay = new Date(year, mNum, 0).getDate();

                return [
                    { index: 1, label: 'Minggu 1', range: `01 - 07 ${mName.substring(0,3)}`, available: true },
                    { index: 2, label: 'Minggu 2', range: `08 - 14 ${mName.substring(0,3)}`, available: !isCurrentMonth },
                    { index: 3, label: 'Minggu 3', range: `15 - 21 ${mName.substring(0,3)}`, available: !isCurrentMonth },
                    { index: 4, label: 'Minggu 4', range: `22 - 28 ${mName.substring(0,3)}`, available: !isCurrentMonth },
                    { index: 5, label: 'Minggu 5', range: `29 - ${lastDay} ${mName.substring(0,3)}`, available: !isCurrentMonth && lastDay > 28 }
                ];
            },

            applyWeek(weekObj) {
                const mName = this.getMonthName(this.pickerMonth);
                this.selectedPeriodKey = `w_${this.pickerYear}_${this.pickerMonth}_${weekObj.index}`;
                this.periodLabel = `${weekObj.label}, ${mName.substring(0,3)} ${this.pickerYear}`;
                this.fullPeriodLabel = `${weekObj.label} (${weekObj.range} ${this.pickerYear})`;
                this.datePickerOpen = false;
                this.triggerToast(`Periode ekspor dipilih: ${this.fullPeriodLabel}`, 'info');
            },

            applyMonth(mNum, year) {
                const mName = this.getMonthName(mNum);
                this.selectedPeriodKey = `m_${year}_${mNum}`;
                this.periodLabel = `${mName} ${year}`;
                this.fullPeriodLabel = `Bulan ${mName} ${year}`;
                this.datePickerOpen = false;
                this.triggerToast(`Periode ekspor dipilih: ${this.fullPeriodLabel}`, 'info');
            },

            applyYear(year) {
                this.selectedPeriodKey = `y_${year}`;
                this.periodLabel = `Tahun ${year}`;
                this.fullPeriodLabel = `Rekap Tahun Anggaran ${year}`;
                this.datePickerOpen = false;
                this.triggerToast(`Periode ekspor dipilih: ${this.fullPeriodLabel}`, 'info');
            },

            // Ekspor Excel Laporan Dashboard Lengkap & Rapi
            exportExcel() {
                const periodeExport = this.fullPeriodLabel || this.periodLabel;
                this.triggerToast(`Menyiapkan laporan Excel lengkap periode [${periodeExport}]...`, 'info');

                const periodKey = this.selectedPeriodKey || 'all';
                const exportUrl = `{{ route('dashboard.export-excel') }}?period=${encodeURIComponent(periodKey)}&label=${encodeURIComponent(periodeExport)}`;

                setTimeout(() => {
                    window.location.href = exportUrl;
                    setTimeout(() => {
                        this.triggerToast(`Laporan Excel [${periodeExport}] berhasil diunduh!`, 'success');
                    }, 1200);
                }, 300);
            }
        };
    }

    let dashboardChartInstance = null;

    const chartDatasets = {!! json_encode($chartDatasets ?? [
        '7d' => ['labels' => [], 'pelet' => [], 'daun' => []],
        '14d' => ['labels' => [], 'pelet' => [], 'daun' => []],
        '30d' => ['labels' => [], 'pelet' => [], 'daun' => []],
        'month' => ['labels' => [], 'pelet' => [], 'daun' => []]
    ]) !!};

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
        // 1. Inisialisasi Chart Analisis Pakan
        const canvas = document.getElementById('dashboardMainChart');
        if (canvas) {
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
        }

        // 2. Inisialisasi Leaflet Map Titik Mitra Distributor
        const mapElement = document.getElementById('mitraDistributionMap');
        if (mapElement && typeof L !== 'undefined') {
            const mitras = {!! json_encode($mitraList ?? []) !!};

            let currentMapMode = 'satellite';
            let activeTileLayer = null;

            function getDashboardTileLayer(mode) {
                if (mode === 'satellite') {
                    return L.tileLayer('https://mt{s}.google.com/vt/lyrs=y&x={x}&y={y}&z={z}', {
                        maxZoom: 20,
                        subdomains: ['0', '1', '2', '3'],
                        attribution: '© Google Satellite'
                    });
                } else {
                    return L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                        maxZoom: 19,
                        attribution: '© OpenStreetMap'
                    });
                }
            }

            // Fix leaflet default icon asset paths
            delete L.Icon.Default.prototype._getIconUrl;
            L.Icon.Default.mergeOptions({
                iconRetinaUrl: "{{ asset('leaflet/images/marker-icon-2x.png') }}",
                iconUrl: "{{ asset('leaflet/images/marker-icon.png') }}",
                shadowUrl: "{{ asset('leaflet/images/marker-shadow.png') }}",
            });

            const map = L.map('mitraDistributionMap', {
                zoomControl: true,
                scrollWheelZoom: false
            }).setView([-7.135, 108.27], 14);

            activeTileLayer = getDashboardTileLayer(currentMapMode);
            activeTileLayer.addTo(map);

            window.toggleDashboardMapLayer = function() {
                currentMapMode = currentMapMode === 'satellite' ? 'street' : 'satellite';
                if (activeTileLayer) {
                    map.removeLayer(activeTileLayer);
                }
                activeTileLayer = getDashboardTileLayer(currentMapMode);
                activeTileLayer.addTo(map);
                activeTileLayer.bringToBack();

                const txt = document.getElementById('textDashboardMapToggle');
                const ico = document.getElementById('iconDashboardMapToggle');
                if (txt) txt.textContent = currentMapMode === 'satellite' ? 'Satelit HD' : 'Peta Jalan';
                if (ico) {
                    ico.className = currentMapMode === 'satellite' ? 'fa-solid fa-satellite text-sky-600' : 'fa-solid fa-map text-emerald-600';
                }
                return currentMapMode;
            };

            const markers = [];
            const customIcon = L.divIcon({
                className: 'custom-leaflet-marker',
                html: `<div style="background-color: #051B44; width: 34px; height: 34px; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; border: 2.5px solid #38BDF8; box-shadow: 0 4px 10px rgba(5,27,68,0.35); font-size: 14px;">
                        <i class="fa-solid fa-store"></i>
                       </div>`,
                iconSize: [34, 34],
                iconAnchor: [17, 17],
                popupAnchor: [0, -18]
            });

            mitras.forEach(function(m) {
                if (m.lat && m.lng) {
                    const popupContent = `
                        <div style="font-family: 'Plus Jakarta Sans', sans-serif; padding: 4px 2px; min-width: 190px;">
                            <div style="display: flex; align-items: center; justify-content: space-between; gap: 8px; margin-bottom: 6px;">
                                <span style="font-size: 9px; font-weight: 800; text-transform: uppercase; letter-spacing: 0.5px; background-color: #E0F2FE; color: #0284C7; padding: 2px 8px; border-radius: 9999px;">
                                    ${m.tipe || 'Mitra'}
                                </span>
                            </div>
                            <h4 style="font-size: 13px; font-weight: 800; color: #051B44; margin: 0 0 4px 0; line-height: 1.3;">
                                ${m.nama}
                            </h4>
                            <p style="font-size: 11px; color: #475569; margin: 0 0 10px 0; line-height: 1.4;">
                                <i class="fa-solid fa-location-dot" style="color: #EF4444; margin-right: 4px;"></i>${m.alamat}
                            </p>
                            <a href="{{ route('distribusi') }}" style="display: inline-flex; align-items: center; justify-content: center; width: 100%; padding: 6px 12px; background-color: #051B44; color: #ffffff; border-radius: 8px; font-size: 11px; font-weight: 700; text-decoration: none;">
                                Buat Distribusi
                            </a>
                        </div>
                    `;

                    const marker = L.marker([m.lat, m.lng], { icon: customIcon })
                        .addTo(map)
                        .bindPopup(popupContent);

                    markers.push(marker);
                }
            });

            // Auto fit bounds to show all markers
            if (markers.length > 0) {
                const group = new L.featureGroup(markers);
                map.fitBounds(group.getBounds().pad(0.15));
                if (markers.length === 1) {
                    map.setZoom(16);
                }
            }

            // Global focus function for sidebar outlet list clicks
            window.focusMitra = function(lat, lng, nama) {
                map.flyTo([lat, lng], 17, { animate: true, duration: 1.2 });
                markers.forEach(function(marker) {
                    const pos = marker.getLatLng();
                    if (Math.abs(pos.lat - lat) < 0.0001 && Math.abs(pos.lng - lng) < 0.0001) {
                        marker.openPopup();
                    }
                });
            };
        }
    });
</script>
@endpush

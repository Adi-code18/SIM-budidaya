@extends('layouts.app')

@section('title', 'Dashboard Utama - AMS BUDIDAYA')

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

    <!-- ========================================================================= -->
    <!-- 1. TOP HEADER: TITLE, PERIODE & QUICK ACTIONS -->
    <!-- ========================================================================= -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2">
                <h1 class="text-2xl font-extrabold text-[#051B44] tracking-tight">Dashboard</h1>
                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold bg-sky-50 text-[#0066CC] border border-sky-200/80 uppercase tracking-wider">
                    Sistem Budidaya
                </span>
            </div>
            <p class="text-xs text-slate-500 font-medium mt-0.5">Operasional budidaya, analitik biomassa pakan, &amp; ringkasan keuangan terkini.</p>
        </div>

        <div class="flex items-center gap-2.5 flex-wrap">
            <!-- Date / Period Calendar Filter Popover -->
            <div class="relative" @click.outside="datePickerOpen = false">
                <button type="button" 
                        @click="datePickerOpen = !datePickerOpen"
                        class="flex items-center gap-2 px-3.5 py-2 rounded-xl border border-slate-200/90 bg-white text-xs font-semibold text-slate-700 shadow-xs hover:bg-slate-50 hover:border-slate-300 transition-all cursor-pointer">
                    <i class="fa-regular fa-calendar-days text-[#0066CC] text-xs"></i>
                    <span x-text="periodLabel">Minggu 1, Ags 2026</span>
                    <i class="fa-solid fa-chevron-down text-[10px] text-slate-400 ml-1 transition-transform" :class="datePickerOpen ? 'rotate-180' : ''"></i>
                </button>

                <!-- Popover Calendar Container -->
                <div x-show="datePickerOpen" 
                     x-transition:enter="transition ease-out duration-150"
                     x-transition:enter-start="opacity-0 translate-y-1 scale-95"
                     x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                     x-transition:leave="transition ease-in duration-100"
                     x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                     x-transition:leave-end="opacity-0 translate-y-1 scale-95"
                     class="absolute right-0 mt-2 w-80 bg-white rounded-2xl shadow-2xl border border-slate-200 p-4 z-40 text-xs"
                     style="display: none;">
                    
                    <!-- Top Mode Switcher (Mingguan | Bulanan | Tahunan) -->
                    <div class="flex items-center bg-slate-100 p-1 rounded-xl mb-3.5">
                        <button type="button" 
                                @click="pickerMode = 'minggu'"
                                :class="pickerMode === 'minggu' ? 'bg-[#051B44] text-white shadow-xs font-bold' : 'text-slate-600 hover:text-slate-900 font-semibold'"
                                class="flex-1 py-1.5 rounded-lg text-center text-xs transition-all">
                            Mingguan
                        </button>
                        <button type="button" 
                                @click="pickerMode = 'bulan'"
                                :class="pickerMode === 'bulan' ? 'bg-[#051B44] text-white shadow-xs font-bold' : 'text-slate-600 hover:text-slate-900 font-semibold'"
                                class="flex-1 py-1.5 rounded-lg text-center text-xs transition-all">
                            Bulanan
                        </button>
                        <button type="button" 
                                @click="pickerMode = 'tahun'"
                                :class="pickerMode === 'tahun' ? 'bg-[#051B44] text-white shadow-xs font-bold' : 'text-slate-600 hover:text-slate-900 font-semibold'"
                                class="flex-1 py-1.5 rounded-lg text-center text-xs transition-all">
                            Tahunan
                        </button>
                    </div>

                    <!-- Header Navigasi Tahun -->
                    <div x-show="pickerMode !== 'tahun'" class="flex items-center justify-between px-2 pb-2.5 mb-2.5 border-b border-slate-100">
                        <button type="button" 
                                @click="prevYear()"
                                :disabled="pickerYear <= minYear"
                                :class="pickerYear <= minYear ? 'opacity-30 cursor-not-allowed' : 'hover:bg-slate-100 text-slate-700'"
                                class="w-7 h-7 rounded-lg flex items-center justify-center transition-colors">
                            <i class="fa-solid fa-chevron-left text-xs"></i>
                        </button>
                        <span class="font-extrabold text-slate-800 text-xs" x-text="pickerYear"></span>
                        <button type="button" 
                                @click="nextYear()"
                                :disabled="pickerYear >= currentYear"
                                :class="pickerYear >= currentYear ? 'opacity-30 cursor-not-allowed' : 'hover:bg-slate-100 text-slate-700'"
                                class="w-7 h-7 rounded-lg flex items-center justify-center transition-colors">
                            <i class="fa-solid fa-chevron-right text-xs"></i>
                        </button>
                    </div>

                    <!-- MODE 1: MINGGUAN -->
                    <div x-show="pickerMode === 'minggu'" class="space-y-3">
                        <div class="grid grid-cols-4 gap-1 px-1">
                            <template x-for="m in monthList" :key="m.num">
                                <button type="button" 
                                        @click="pickerMonth = m.num"
                                        :disabled="!isMonthAvailable(m.num, pickerYear)"
                                        :class="{
                                            'bg-[#051B44] text-white font-bold shadow-xs': pickerMonth === m.num,
                                            'text-slate-700 hover:bg-slate-100 font-medium': pickerMonth !== m.num && isMonthAvailable(m.num, pickerYear),
                                            'text-slate-300 opacity-40 cursor-not-allowed': !isMonthAvailable(m.num, pickerYear)
                                        }"
                                        class="py-1 px-1 rounded-lg text-center text-[11px] transition-all"
                                        x-text="m.short">
                                </button>
                            </template>
                        </div>
                        <div class="pt-2 border-t border-slate-100">
                            <div class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1.5 px-1">Pilih Rentang Minggu:</div>
                            <div class="space-y-1">
                                <template x-for="w in getWeeks(pickerMonth, pickerYear)" :key="w.index">
                                    <button type="button" 
                                            @click="applyWeek(w)"
                                            :class="selectedPeriodKey === `w_${pickerYear}_${pickerMonth}_${w.index}` ? 'bg-sky-50 text-[#0066CC] font-bold border-sky-300' : 'hover:bg-slate-50 text-slate-700 border-transparent'"
                                            class="w-full text-left px-2.5 py-1.5 rounded-lg border text-xs flex items-center justify-between transition-colors">
                                        <span x-text="w.label"></span>
                                        <span class="text-[10px] text-slate-400 font-medium" x-text="w.range"></span>
                                    </button>
                                </template>
                            </div>
                        </div>
                    </div>

                    <!-- MODE 2: BULANAN -->
                    <div x-show="pickerMode === 'bulan'" class="space-y-2">
                        <div class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1.5 px-1">Pilih Rekap Bulan:</div>
                        <div class="grid grid-cols-3 gap-1.5">
                            <template x-for="m in monthList" :key="m.num">
                                <button type="button" 
                                        @click="applyMonth(m.num, pickerYear)"
                                        :disabled="!isMonthAvailable(m.num, pickerYear)"
                                        :class="{
                                            'bg-[#051B44] text-white font-bold shadow-xs': selectedPeriodKey === `m_${pickerYear}_${m.num}`,
                                            'bg-slate-50 hover:bg-slate-100 text-slate-700 font-medium': selectedPeriodKey !== `m_${pickerYear}_${m.num}` && isMonthAvailable(m.num, pickerYear),
                                            'text-slate-300 opacity-40 cursor-not-allowed bg-slate-50/50': !isMonthAvailable(m.num, pickerYear)
                                        }"
                                        class="py-2.5 px-2 rounded-xl text-center text-xs transition-all border border-slate-100"
                                        x-text="m.name">
                                </button>
                            </template>
                        </div>
                    </div>

                    <!-- MODE 3: TAHUNAN -->
                    <div x-show="pickerMode === 'tahun'" class="space-y-2">
                        <div class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1.5 px-1">Pilih Rekap Tahun:</div>
                        <div class="space-y-1.5">
                            <template x-for="y in getAvailableYears()" :key="y.year">
                                <button type="button" 
                                        @click="applyYear(y.year)"
                                        :disabled="!y.available"
                                        :class="{
                                            'bg-[#051B44] text-white font-bold shadow-xs': selectedPeriodKey === `y_${y.year}`,
                                            'bg-slate-50 hover:bg-slate-100 text-slate-700': selectedPeriodKey !== `y_${y.year}` && y.available,
                                            'text-slate-300 opacity-40 cursor-not-allowed bg-slate-50/50': !y.available
                                        }"
                                        class="w-full text-left p-2.5 rounded-xl border border-slate-100 text-xs flex items-center justify-between transition-all">
                                    <div>
                                        <div class="font-extrabold text-sm" x-text="'Tahun ' + y.year"></div>
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
                    class="px-4 py-2 rounded-xl bg-[#0B6B2E] hover:bg-emerald-800 text-white font-bold text-xs flex items-center gap-2 shadow-xs hover:shadow-md active:scale-95 transition-all tracking-wide cursor-pointer">
                <i class="fa-solid fa-file-excel text-sm text-emerald-200"></i>
                <span>EKSPOR EXCEL</span>
            </button>
        </div>
    </div>

    <!-- ========================================================================= -->
    <!-- 2. TOP METRIC KPI CARDS (ROW OF 4: 3 METRICS + 1 SALDO WALLET CARD) -->
    <!-- ========================================================================= -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
        
        <!-- Card 1: Total Stok Ikan (Biomassa) -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-[0_2px_10px_rgba(0,0,0,0.03)] flex flex-col justify-between hover:shadow-md transition-all relative overflow-hidden group">
            <div>
                <div class="flex items-center justify-between">
                    <span class="text-[11px] font-bold uppercase tracking-wider text-slate-500">Total Stok Ikan</span>
                    <span class="px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-emerald-50 text-emerald-600 border border-emerald-100 flex items-center gap-1">
                        <i class="fa-solid fa-arrow-trend-up text-[9px]"></i> +4.2%
                    </span>
                </div>
                <div class="mt-3 flex items-baseline gap-1.5">
                    <h3 class="text-2xl lg:text-3xl font-extrabold text-slate-900 tracking-tight" x-text="metrics.totalStok">1.250</h3>
                    <span class="text-xs font-bold text-slate-500">kg biomassa</span>
                </div>
            </div>
            
            <!-- Micro Sparkline SVG -->
            <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between text-xs">
                <div class="flex items-center gap-1.5 text-slate-500 font-medium">
                    <i class="fa-solid fa-circle text-[7px] text-emerald-500"></i>
                    <span x-text="metrics.totalStokTrend">Batch Aktif Terdata</span>
                </div>
                <svg class="w-16 h-5 text-emerald-500" viewBox="0 0 64 20" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M0 16 Q 16 4, 32 12 T 64 4" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </div>
        </div>

        <!-- Card 2: FCR Rata-rata (Efisiensi Pakan) -->
        <div @click="fcrModalOpen = true"
             class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-[0_2px_10px_rgba(0,0,0,0.03)] flex flex-col justify-between hover:shadow-md hover:border-sky-300 transition-all cursor-pointer relative overflow-hidden group">
            <div>
                <div class="flex items-center justify-between">
                    <span class="text-[11px] font-bold uppercase tracking-wider text-slate-500">FCR Rata-rata</span>
                    <span class="px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-sky-50 text-[#0066CC] border border-sky-100">
                        Target &lt; 1.2
                    </span>
                </div>
                <div class="mt-3 flex items-baseline gap-2">
                    <h3 class="text-2xl lg:text-3xl font-extrabold text-slate-900 tracking-tight" x-text="metrics.fcr">1.12</h3>
                    <span class="text-[11px] font-bold text-emerald-600 bg-emerald-50 px-1.5 py-0.5 rounded">Efisien</span>
                </div>
            </div>

            <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between text-xs">
                <div class="flex items-center gap-1.5 text-emerald-600 font-medium truncate">
                    <i class="fa-regular fa-circle-check text-xs"></i>
                    <span x-text="metrics.fcrStatus">Sesuai SOP Budidaya</span>
                </div>
                <svg class="w-16 h-5 text-[#0066CC]" viewBox="0 0 64 20" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M0 8 Q 20 16, 40 6 T 64 12" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </div>
        </div>

        <!-- Card 3: Target Panen Konsumsi -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-[0_2px_10px_rgba(0,0,0,0.03)] flex flex-col justify-between hover:shadow-md transition-all relative overflow-hidden group">
            <div>
                <div class="flex items-center justify-between">
                    <span class="text-[11px] font-bold uppercase tracking-wider text-slate-500">Target Panen</span>
                    <span class="px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-amber-50 text-amber-700 border border-amber-200" x-text="metrics.targetPanenTag">
                        ORDER AKTIF
                    </span>
                </div>
                <div class="mt-3 flex items-baseline gap-1.5">
                    <h3 class="text-2xl lg:text-3xl font-extrabold text-slate-900 tracking-tight" x-text="metrics.targetPanen">450</h3>
                    <span class="text-xs font-bold text-slate-500">kg siap panen</span>
                </div>
            </div>

            <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between text-xs">
                <div class="flex items-center gap-1.5 text-slate-600 font-medium truncate">
                    <i class="fa-regular fa-calendar-check text-slate-400"></i>
                    <span class="truncate" x-text="metrics.targetPanenNote">Mitra Distribusi</span>
                </div>
                <svg class="w-16 h-5 text-amber-500" viewBox="0 0 64 20" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M0 14 Q 24 2, 44 14 T 64 6" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </div>
        </div>

        <!-- Card 4: Saldo Kas Bersih & Margin (Sleek MasterCard / Wallet Style Card) -->
        <div class="bg-gradient-to-br from-[#0B2570] via-[#051B44] to-[#020D26] text-white p-5 rounded-2xl shadow-lg shadow-[#0B2570]/20 flex flex-col justify-between relative overflow-hidden group min-h-[145px]">
            <!-- Background Glow Circles -->
            <div class="absolute -right-4 -bottom-4 w-28 h-28 rounded-full bg-sky-500/10 blur-xl pointer-events-none"></div>
            
            <div>
                <div class="flex items-center justify-between">
                    <span class="text-[10px] font-extrabold uppercase tracking-widest text-sky-200/80">Saldo Kas Bersih</span>
                    <!-- Dual overlapping circles icon (Mastercard style) -->
                    <div class="flex items-center -space-x-2">
                        <div class="w-6 h-6 rounded-full bg-[#EB001B] opacity-90 shadow-xs"></div>
                        <div class="w-6 h-6 rounded-full bg-[#F79E1B] opacity-90 shadow-xs"></div>
                    </div>
                </div>
                <div class="mt-2.5">
                    <h3 class="text-2xl lg:text-3xl font-black tracking-tight text-white" x-text="currentFinancialData.laba_rugi_format || 'Rp 0'">
                        Rp 0
                    </h3>
                </div>
            </div>

            <div class="mt-3 pt-2.5 border-t border-white/10 flex items-center justify-between text-xs">
                <div>
                    <span class="text-[10px] font-mono tracking-widest text-sky-200/70 block">1234 •••• 8901 2026</span>
                    <span class="text-[10px] font-extrabold text-white tracking-wider uppercase block mt-0.5">AMS AQUAFARM</span>
                </div>
                <span class="px-2.5 py-1 rounded-full text-[9px] font-black uppercase tracking-wider"
                      :class="currentFinancialData.is_untung ? 'bg-emerald-400/20 text-emerald-300 border border-emerald-400/30' : 'bg-rose-400/20 text-rose-300 border border-rose-400/30'"
                      x-text="currentFinancialData.status_label">
                    Surplus
                </span>
            </div>
        </div>

    </div>

    <!-- ========================================================================= -->
    <!-- 3. MIDDLE SECTION: ANALYTICS CHART (8 COLS) + FINANCIAL CARDS (4 COLS) -->
    <!-- ========================================================================= -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-5">
        
        <!-- Main Chart Card: Analisis Konsumsi Pakan (lg:col-span-8) -->
        <div class="lg:col-span-8 bg-white p-5 sm:p-6 rounded-2xl border border-slate-200/80 shadow-[0_2px_10px_rgba(0,0,0,0.03)] flex flex-col justify-between">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-5">
                <div>
                    <h3 class="text-base font-bold text-slate-900 tracking-tight">Analisis Konsumsi Pakan</h3>
                    <p class="text-xs text-slate-400 mt-0.5">Monitoring perbandingan pelet komersial vs hijauan dedaunan alami harian.</p>
                </div>
                
                <div class="flex items-center gap-4 text-xs font-semibold flex-wrap">
                    <!-- Legend 1 -->
                    <div class="flex items-center gap-1.5 text-slate-700">
                        <span class="w-2.5 h-2.5 rounded-full bg-[#0B2570]"></span>
                        <span>Pelet Komersial</span>
                    </div>
                    <!-- Legend 2 -->
                    <div class="flex items-center gap-1.5 text-slate-700">
                        <span class="w-2.5 h-2.5 rounded-full bg-[#10B981]"></span>
                        <span>Dedaunan Organik</span>
                    </div>
                    
                    <!-- Filter Dropdown Chart -->
                    <div class="relative" @click.outside="chartDropdownOpen = false">
                        <button type="button" 
                                @click="chartDropdownOpen = !chartDropdownOpen"
                                class="flex items-center gap-2 px-3 py-1.5 rounded-xl border border-slate-200 bg-white text-slate-700 text-xs font-bold hover:bg-slate-50 cursor-pointer shadow-xs">
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
                             class="absolute right-0 mt-2 w-48 bg-white rounded-xl shadow-xl border border-slate-100 py-1 z-30 text-xs"
                             style="display: none;">
                            <button type="button" 
                                    @click="chartPeriodLabel = '7 Hari Terakhir'; chartDropdownOpen = false; window.setChartData('7d'); triggerToast('Grafik: 7 Hari Terakhir', 'info');"
                                    class="w-full px-3 py-2 text-left hover:bg-sky-50 text-slate-700 hover:text-sky-700 font-semibold">
                                7 Hari Terakhir
                            </button>
                            <button type="button" 
                                    @click="chartPeriodLabel = '14 Hari Terakhir'; chartDropdownOpen = false; window.setChartData('14d'); triggerToast('Grafik: 14 Hari Terakhir', 'info');"
                                    class="w-full px-3 py-2 text-left hover:bg-sky-50 text-slate-700 hover:text-sky-700 font-semibold">
                                14 Hari Terakhir
                            </button>
                            <button type="button" 
                                    @click="chartPeriodLabel = '30 Hari Terakhir'; chartDropdownOpen = false; window.setChartData('30d'); triggerToast('Grafik: 30 Hari Terakhir', 'info');"
                                    class="w-full px-3 py-2 text-left hover:bg-sky-50 text-slate-700 hover:text-sky-700 font-semibold">
                                30 Hari Terakhir
                            </button>
                            <button type="button" 
                                    @click="chartPeriodLabel = 'Bulan Ini'; chartDropdownOpen = false; window.setChartData('month'); triggerToast('Grafik: Bulan Ini', 'info');"
                                    class="w-full px-3 py-2 text-left hover:bg-sky-50 text-slate-700 hover:text-sky-700 font-semibold">
                                Bulan Ini
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="h-64 sm:h-72 w-full relative">
                <canvas id="dashboardMainChart"></canvas>
            </div>
        </div>

        <!-- Financial Summary Cards (lg:col-span-4) -->
        <div class="lg:col-span-4 space-y-4 flex flex-col justify-between">
            
            <!-- Quick Period Switcher Tabs -->
            <div class="bg-white p-2 rounded-2xl border border-slate-200/80 shadow-xs flex items-center justify-between gap-1">
                <button type="button" 
                        @click="finTab = 'bulan'"
                        :class="finTab === 'bulan' ? 'bg-[#051B44] text-white shadow-xs font-bold' : 'text-slate-600 hover:text-slate-900 font-semibold hover:bg-slate-100'"
                        class="flex-1 py-1.5 rounded-xl text-center text-xs transition-all">
                    Bulan Ini
                </button>
                <button type="button" 
                        @click="finTab = 'tahun'"
                        :class="finTab === 'tahun' ? 'bg-[#051B44] text-white shadow-xs font-bold' : 'text-slate-600 hover:text-slate-900 font-semibold hover:bg-slate-100'"
                        class="flex-1 py-1.5 rounded-xl text-center text-xs transition-all">
                    Tahun Ini
                </button>
                <button type="button" 
                        @click="finTab = 'breakdown'"
                        :class="finTab === 'breakdown' ? 'bg-[#051B44] text-white shadow-xs font-bold' : 'text-slate-600 hover:text-slate-900 font-semibold hover:bg-slate-100'"
                        class="flex-1 py-1.5 rounded-xl text-center text-xs transition-all">
                    12 Bulan
                </button>
            </div>

            <!-- Card Pemasukan (Omset) -->
            <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-[0_2px_10px_rgba(0,0,0,0.03)] flex items-center gap-4 hover:shadow-md transition-all">
                <div class="w-12 h-12 rounded-xl bg-teal-50 border border-teal-100 text-teal-600 flex items-center justify-center text-lg shrink-0">
                    <i class="fa-solid fa-arrow-down-left"></i>
                </div>
                <div class="flex-1 min-w-0">
                    <div class="flex items-center justify-between">
                        <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400">Total Pemasukan</span>
                        <span class="px-2 py-0.5 rounded-full text-[9px] font-bold bg-teal-50 text-teal-700 border border-teal-200">
                            Panen
                        </span>
                    </div>
                    <div class="text-xl font-extrabold text-slate-900 tracking-tight mt-1 truncate" x-text="currentFinancialData.pemasukan_format || 'Rp 0'">
                        Rp 0
                    </div>
                    <div class="text-[11px] text-slate-400 font-medium mt-0.5 flex items-center justify-between">
                        <span>Penjualan mitra distribusi</span>
                        <a href="{{ route('keuangan') }}" class="font-bold text-[#0066CC] hover:underline text-[10px]">Detail &rarr;</a>
                    </div>
                </div>
            </div>

            <!-- Card Pengeluaran (Biaya Operasional) -->
            <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-[0_2px_10px_rgba(0,0,0,0.03)] flex items-center gap-4 hover:shadow-md transition-all">
                <div class="w-12 h-12 rounded-xl bg-rose-50 border border-rose-100 text-rose-600 flex items-center justify-center text-lg shrink-0">
                    <i class="fa-solid fa-arrow-up-right"></i>
                </div>
                <div class="flex-1 min-w-0">
                    <div class="flex items-center justify-between">
                        <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400">Total Pengeluaran</span>
                        <span class="px-2 py-0.5 rounded-full text-[9px] font-bold bg-rose-50 text-rose-700 border border-rose-200" x-text="'Cost ' + (currentFinancialData.cost_ratio || 0) + '%'">
                            Cost
                        </span>
                    </div>
                    <div class="text-xl font-extrabold text-slate-900 tracking-tight mt-1 truncate" x-text="currentFinancialData.pengeluaran_format || 'Rp 0'">
                        Rp 0
                    </div>
                    <div class="text-[11px] text-slate-400 font-medium mt-0.5 flex items-center justify-between">
                        <span>Pakan, bibit &amp; kolam</span>
                        <span class="text-[10px] text-slate-400 font-semibold" x-text="finTab === 'bulan' ? 'Bulan Ini' : 'Tahun Ini'"></span>
                    </div>
                </div>
            </div>

            <!-- Profit Margin Metric Box -->
            <div class="bg-slate-50 p-4 rounded-2xl border border-slate-200/80 flex items-center justify-between">
                <div>
                    <span class="text-[10px] font-bold text-slate-500 uppercase tracking-wider">Margin Profitabilitas</span>
                    <div class="text-base font-extrabold mt-0.5" 
                         :class="currentFinancialData.is_untung ? 'text-emerald-700' : 'text-rose-700'">
                        <span x-text="currentFinancialData.margin_percent">0</span>% Bersih
                    </div>
                </div>
                <div class="text-right">
                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Evaluasi Kas</span>
                    <div class="text-xs font-bold text-slate-700 mt-0.5" x-text="currentFinancialData.status_label">
                        Surplus
                    </div>
                </div>
            </div>

        </div>

    </div>

    <!-- ========================================================================= -->
    <!-- 4. TAB REKAP 12 BULAN (JIKA DIPILIH PADA SWITCHER FINANSIAL) -->
    <!-- ========================================================================= -->
    <div x-show="finTab === 'breakdown'" 
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 translate-y-1"
         x-transition:enter-end="opacity-100 translate-y-0"
         class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs space-y-3"
         style="display: none;">
        <div class="flex items-center justify-between text-xs pb-2 border-b border-slate-100">
            <span class="font-bold text-slate-800">Rincian Profitabilitas Bulanan (Januari – Desember <span x-text="currentYear"></span>)</span>
            <span class="text-[11px] text-slate-400 italic">Bulan berjalan ditandai dengan sorotan khusus</span>
        </div>

        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-3">
            <template x-for="item in (financialSummary.monthly_breakdown || [])" :key="item.month_num">
                <div class="p-3.5 rounded-xl border transition-all flex flex-col justify-between"
                     :class="{
                         'bg-emerald-50/70 border-emerald-300 ring-2 ring-emerald-500/20 shadow-xs': item.is_current && item.is_profit && item.has_data,
                         'bg-rose-50/70 border-rose-300 ring-2 ring-rose-500/20 shadow-xs': item.is_current && !item.is_profit && item.has_data,
                         'bg-sky-50/60 border-sky-300 ring-2 ring-sky-500/20 shadow-xs': item.is_current && !item.has_data,
                         'bg-slate-50/60 border-slate-200/80 hover:border-sky-300': !item.is_current
                     }">
                    <div>
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-black text-slate-800" x-text="item.month_name"></span>
                            <span x-show="item.is_current" class="text-[8px] font-black uppercase px-1.5 py-0.5 rounded bg-[#051B44] text-white">Bln Ini</span>
                        </div>

                        <div class="mt-2 space-y-1 text-[11px]">
                            <div class="flex justify-between text-slate-400">
                                <span>Masuk:</span>
                                <span class="font-bold text-slate-700" x-text="item.has_data ? item.pemasukan_format : '-'"></span>
                            </div>
                            <div class="flex justify-between text-slate-400">
                                <span>Keluar:</span>
                                <span class="font-bold text-slate-700" x-text="item.has_data ? item.pengeluaran_format : '-'"></span>
                            </div>
                        </div>
                    </div>

                    <div class="mt-2.5 pt-2 border-t border-slate-100">
                        <div class="text-[9px] font-bold text-slate-400 uppercase">Laba / Rugi:</div>
                        <div class="text-xs font-black truncate mt-0.5"
                             :class="item.has_data ? (item.is_profit ? 'text-emerald-600' : 'text-rose-600') : 'text-slate-400'"
                             x-text="item.has_data ? item.laba_rugi_format : 'Rp 0'">
                        </div>
                    </div>
                </div>
            </template>
        </div>
    </div>

    <!-- ========================================================================= -->
    <!-- 5. OPERATIONAL GRID: RECENT ORDERS (LEFT 5 COLS) + STOCK REPORT (RIGHT 7 COLS) -->
    <!-- EXACT MATCH TO BOTTOM SECTION OF REFERENCE IMAGE -->
    <!-- ========================================================================= -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-5">
        
        <!-- Left Box: Pesanan Distribusi Terkini (Recent Orders) -->
        <div class="lg:col-span-5 bg-white p-5 sm:p-6 rounded-2xl border border-slate-200/80 shadow-[0_2px_10px_rgba(0,0,0,0.03)] flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h3 class="text-sm font-bold text-slate-900 tracking-tight">Pesanan Distribusi Terkini</h3>
                        <p class="text-[11px] text-slate-400">Daftar transaksi order &amp; pengiriman panen.</p>
                    </div>
                    <a href="{{ route('distribusi') }}" class="text-xs font-bold text-[#0066CC] hover:underline flex items-center gap-1">
                        <span>Lihat Semua</span>
                        <i class="fa-solid fa-arrow-right text-[10px]"></i>
                    </a>
                </div>

                <div class="divide-y divide-slate-100">
                    @forelse($recentOrders ?? [] as $order)
                    @php
                        $statusClass = match(strtolower($order->status_order ?? '')) {
                            'selesai'          => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                            'dalam_pengiriman' => 'bg-indigo-50 text-indigo-700 border-indigo-200',
                            'siap_kirim'       => 'bg-sky-50 text-sky-700 border-sky-200',
                            'pemberokian'      => 'bg-amber-50 text-amber-700 border-amber-200',
                            'dibatalkan'       => 'bg-rose-50 text-rose-700 border-rose-200',
                            default            => 'bg-slate-100 text-slate-700 border-slate-200',
                        };
                        $statusText = match(strtolower($order->status_order ?? '')) {
                            'selesai'          => 'Selesai',
                            'dalam_pengiriman' => 'Dikirim',
                            'siap_kirim'       => 'Siap Kirim',
                            'pemberokian'      => 'Pemberokan',
                            'dibatalkan'       => 'Batal',
                            default            => 'Pending',
                        };
                    @endphp
                    <div class="py-3 flex items-center justify-between gap-3 group">
                        <div class="flex items-center gap-3 min-w-0">
                            <div class="w-10 h-10 rounded-xl bg-sky-50 border border-sky-100 text-[#0066CC] flex items-center justify-center text-sm shrink-0 group-hover:bg-[#051B44] group-hover:text-white transition-colors">
                                <i class="fa-solid fa-truck-ramp-box"></i>
                            </div>
                            <div class="min-w-0">
                                <h4 class="text-xs font-bold text-slate-900 truncate group-hover:text-[#0066CC] transition-colors">
                                    {{ $order->mitra->nama_mitra ?? 'Mitra Distribusi' }}
                                </h4>
                                <p class="text-[11px] text-slate-400 mt-0.5 truncate">
                                    {{ $order->tanggal_order ? \Carbon\Carbon::parse($order->tanggal_order)->format('d M Y') : '-' }} • {{ $order->Total_kg }} kg
                                </p>
                            </div>
                        </div>

                        <div class="text-right shrink-0">
                            <div class="text-xs font-extrabold text-slate-900">
                                Rp {{ number_format($order->harga_total, 0, ',', '.') }}
                            </div>
                            <span class="inline-block mt-0.5 px-2 py-0.5 rounded-full text-[9px] font-bold border {{ $statusClass }}">
                                {{ $statusText }}
                            </span>
                        </div>
                    </div>
                    @empty
                    <div class="py-8 text-center text-xs text-slate-400">
                        Belum ada data transaksi distribusi tercatat.
                    </div>
                    @endforelse
                </div>
            </div>

            <div class="pt-3 mt-2 border-t border-slate-100 flex items-center justify-between text-[11px] text-slate-400 font-medium">
                <span>Kontrak Logistik DDP Aktif</span>
                <span class="font-bold text-emerald-600">Mortalitas &lt; 2%</span>
            </div>
        </div>

        <!-- Right Box: Laporan Stok Kolam & Batch Aktif (Stock Report Table) -->
        <div class="lg:col-span-7 bg-white p-5 sm:p-6 rounded-2xl border border-slate-200/80 shadow-[0_2px_10px_rgba(0,0,0,0.03)] flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h3 class="text-sm font-bold text-slate-900 tracking-tight">Laporan Siklus Kolam &amp; Batch Aktif</h3>
                        <p class="text-[11px] text-slate-400">Performa pertumbuhan, estimasi biomassa, &amp; FCR kolam.</p>
                    </div>
                    <a href="{{ route('pembesaran') }}" class="text-xs font-bold text-[#0066CC] hover:underline flex items-center gap-1">
                        <span>Kelola Batch</span>
                        <i class="fa-solid fa-arrow-right text-[10px]"></i>
                    </a>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead>
                            <tr class="text-[10px] uppercase font-extrabold text-slate-400 border-b border-slate-100">
                                <th class="pb-2.5 font-bold">Kolam &amp; Komoditas</th>
                                <th class="pb-2.5 font-bold">Tgl Tebar</th>
                                <th class="pb-2.5 font-bold text-right">Biomassa</th>
                                <th class="pb-2.5 font-bold text-center">FCR</th>
                                <th class="pb-2.5 font-bold text-right">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse($batchStatusList ?? [] as $batch)
                            @php
                                $statusBatchClass = match(strtolower($batch->status_siklus ?? '')) {
                                    'siap_panen' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                                    'berjalan'   => 'bg-sky-50 text-sky-700 border-sky-200',
                                    'selesai'    => 'bg-slate-100 text-slate-600 border-slate-200',
                                    default      => 'bg-amber-50 text-amber-700 border-amber-200',
                                };
                                $statusBatchText = match(strtolower($batch->status_siklus ?? '')) {
                                    'siap_panen' => 'Siap Panen',
                                    'berjalan'   => 'Berjalan',
                                    'selesai'    => 'Selesai',
                                    default      => ucfirst($batch->status_siklus ?? 'Aktif'),
                                };
                            @endphp
                            <tr class="hover:bg-slate-50/70 transition-colors">
                                <td class="py-3">
                                    <div class="font-bold text-slate-900">{{ $batch->kolam->nama_kolam ?? 'Kolam' }}</div>
                                    <div class="text-[10px] text-slate-400 mt-0.5">{{ $batch->jenis_ikan }}</div>
                                </td>
                                <td class="py-3 text-slate-500 font-medium">
                                    {{ $batch->tgl_tebar ? \Carbon\Carbon::parse($batch->tgl_tebar)->format('d/m/Y') : '-' }}
                                </td>
                                <td class="py-3 text-right">
                                    <span class="font-bold text-slate-900">{{ number_format($batch->biomassa_est, 1, ',', '.') }} kg</span>
                                    <div class="text-[10px] text-slate-400">Target {{ number_format($batch->target_panen_kg, 0, ',', '.') }} kg</div>
                                </td>
                                <td class="py-3 text-center">
                                    <span class="px-2 py-0.5 rounded font-mono font-bold text-[11px] {{ ($batch->fcr ?? 0) <= 1.25 ? 'text-emerald-700 bg-emerald-50' : 'text-amber-700 bg-amber-50' }}">
                                        {{ $batch->fcr ? number_format($batch->fcr, 2) : '1.15' }}
                                    </span>
                                </td>
                                <td class="py-3 text-right">
                                    <span class="inline-block px-2.5 py-0.5 rounded-full text-[9px] font-bold border {{ $statusBatchClass }}">
                                        {{ $statusBatchText }}
                                    </span>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="py-8 text-center text-xs text-slate-400">
                                    Belum ada siklus batch pembesaran aktif.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="pt-3 mt-2 border-t border-slate-100 flex items-center justify-between text-[11px] text-slate-400 font-medium">
                <span>Standar Mutu CBIB KKP</span>
                <span class="text-sky-600 font-bold">Aerasi Deras 24 Jam</span>
            </div>
        </div>

    </div>

    <!-- ========================================================================= -->
    <!-- 6. MAP SECTION: PETA SEBARAN TITIK MITRA DISTRIBUTOR (LEAFLET.JS) -->
    <!-- ========================================================================= -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-[0_2px_10px_rgba(0,0,0,0.03)] overflow-hidden">
        <div class="p-5 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-[#051B44] text-white flex items-center justify-center shadow-md shadow-[#051B44]/15 flex-shrink-0">
                    <i class="fa-solid fa-map-location-dot text-base"></i>
                </div>
                <div>
                    <h3 class="text-base font-bold text-slate-900 tracking-tight">Peta Sebaran Titik Mitra Distributor</h3>
                    <p class="text-xs text-slate-400 mt-0.5">Pemetaan geolokasi outlet restoran, pasar, &amp; eksportir mitra AquaFarm.</p>
                </div>
            </div>
            
            <div class="flex items-center gap-2">
                <!-- Toggle Satelit / Peta Jalan -->
                <button type="button" 
                        onclick="if(window.toggleDashboardMapLayer) window.toggleDashboardMapLayer()"
                        id="btnDashboardMapToggle"
                        class="px-3.5 py-2 rounded-xl bg-white border border-slate-200 hover:bg-slate-50 text-slate-800 text-xs font-bold shadow-xs transition-all flex items-center gap-1.5 cursor-pointer">
                    <i class="fa-solid fa-satellite text-sky-600" id="iconDashboardMapToggle"></i>
                    <span id="textDashboardMapToggle">Satelit HD</span>
                </button>

                <div class="hidden sm:flex items-center gap-1.5 px-3 py-2 rounded-xl bg-sky-50 border border-sky-100 text-[#051B44] text-xs font-bold">
                    <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                    <span>{{ count($mitraList ?? []) }} Titik Mitra</span>
                </div>
                
                <a href="{{ route('mitra') }}" class="px-3.5 py-2 rounded-xl border border-slate-200 hover:border-slate-300 bg-white text-xs font-bold text-[#0066CC] hover:bg-slate-50 transition-all flex items-center gap-1.5 shadow-xs">
                    <span>Kelola Mitra</span>
                    <i class="fa-solid fa-arrow-right text-[10px]"></i>
                </a>
            </div>
        </div>

        <!-- Leaflet Map Container & Side Info Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-12">
            <!-- Map Container -->
            <div class="lg:col-span-7 xl:col-span-8 relative min-h-[380px] lg:min-h-[420px] bg-slate-100 z-0">
                <div id="mitraDistributionMap" class="w-full h-full min-h-[380px] lg:min-h-[420px]"></div>
            </div>

            <!-- Side Mitra List Panel -->
            <div class="lg:col-span-5 xl:col-span-4 border-t lg:border-t-0 lg:border-l border-slate-100 bg-slate-50/50 p-4 sm:p-5 flex flex-col justify-between max-h-[420px] overflow-y-auto overflow-x-hidden custom-slim-scroll">
                <div>
                    <div class="flex items-center justify-between mb-3">
                        <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400">DAFTAR OUTLET MITRA</span>
                        <span class="text-[10px] font-bold text-sky-700 bg-sky-100 px-2 py-0.5 rounded-full">{{ count($mitraList ?? []) }} Lokasi</span>
                    </div>

                    <div class="space-y-2.5">
                        @forelse($mitraList as $idx => $mitra)
                        <div class="p-3 rounded-xl bg-white border border-slate-200/80 shadow-xs hover:border-sky-300 hover:shadow-md transition-all cursor-pointer group overflow-hidden"
                             onclick="window.focusMitra({{ $mitra['lat'] }}, {{ $mitra['lng'] }}, '{{ addslashes($mitra['nama']) }}')">
                            <div class="flex items-start justify-between gap-2.5">
                                <div class="flex items-start gap-2.5 min-w-0 flex-1">
                                    <span class="w-5 h-5 rounded-lg bg-[#051B44] text-white flex items-center justify-center text-[10px] font-bold shrink-0 mt-0.5">
                                        {{ $idx + 1 }}
                                    </span>
                                    <div class="min-w-0 flex-1">
                                        <h5 class="text-xs font-bold text-slate-800 group-hover:text-[#0066CC] transition-colors leading-snug truncate" title="{{ $mitra['nama'] }}">
                                            {{ $mitra['nama'] }}
                                        </h5>
                                        <p class="text-[11px] text-slate-500 mt-1 truncate flex items-center gap-1">
                                            <i class="fa-solid fa-location-dot text-rose-500 text-[10px] shrink-0"></i>
                                            <span class="truncate">{{ $mitra['alamat'] }}</span>
                                        </p>
                                    </div>
                                </div>
                                <span class="px-2 py-0.5 rounded text-[9px] font-extrabold bg-sky-50 text-sky-700 border border-sky-100 shrink-0 whitespace-nowrap self-start">
                                    {{ $mitra['tipe'] }}
                                </span>
                            </div>
                        </div>
                        @empty
                        <div class="p-4 text-center text-slate-400 text-xs">
                            Belum ada mitra distributor terdaftar.
                        </div>
                        @endforelse
                    </div>
                </div>

                <div class="pt-3 mt-3 border-t border-slate-200/60 text-center">
                    <span class="text-[10px] text-slate-400 font-medium">Klik outlet untuk fokus titik di peta satelit</span>
                </div>
            </div>
        </div>
    </div>

    <!-- ========================================================================= -->
    <!-- 7. MODAL: KALKULATOR & SIMULATOR AUDIT FCR (MULTI-METODE SOP) -->
    <!-- ========================================================================= -->
    <div x-show="fcrModalOpen" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4 overflow-y-auto"
         style="display: none;">
        
        <div @click.outside="fcrModalOpen = false"
             class="bg-white rounded-3xl border border-slate-200 shadow-2xl max-w-4xl w-full max-h-[92vh] flex flex-col overflow-hidden transform transition-all">
            
            <!-- Header Modal -->
            <div class="px-6 py-5 bg-gradient-to-r from-[#051B44] to-[#0B2570] text-white flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-2xl bg-white/10 flex items-center justify-center border border-white/20">
                        <i class="fa-solid fa-calculator text-sky-300 text-lg"></i>
                    </div>
                    <div>
                        <h3 class="text-base font-extrabold tracking-tight">Kalkulator &amp; Simulator Audit FCR SOP (Multi-Metode)</h3>
                        <p class="text-xs text-sky-200">Simulasi 4 varian FCR, evaluasi pakan terbuang (wasted feed), dan audit finansial.</p>
                    </div>
                </div>
                <button type="button" @click="fcrModalOpen = false" class="w-8 h-8 rounded-full bg-white/10 hover:bg-white/20 text-white flex items-center justify-center transition-colors">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>

            <!-- Modal Body Scrollable -->
            <div class="p-6 overflow-y-auto space-y-6">
                <!-- Preset Button Selection -->
                <div class="bg-slate-50 p-4 rounded-2xl border border-slate-200">
                    <div class="text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-2">Preset Komoditas Budidaya:</div>
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-2">
                        <button type="button" 
                                @click="setKomoditasPreset('lele')"
                                :class="simKomoditas === 'lele' ? 'bg-[#0B2570] text-white font-bold shadow-xs' : 'bg-white text-slate-700 hover:bg-slate-100 border border-slate-200'"
                                class="px-3 py-2 rounded-xl text-xs flex items-center justify-center gap-2 transition-all">
                            <span>🐟 Lele</span>
                        </button>
                        <button type="button" 
                                @click="setKomoditasPreset('nila')"
                                :class="simKomoditas === 'nila' ? 'bg-[#0B2570] text-white font-bold shadow-xs' : 'bg-white text-slate-700 hover:bg-slate-100 border border-slate-200'"
                                class="px-3 py-2 rounded-xl text-xs flex items-center justify-center gap-2 transition-all">
                            <span>🐠 Nila</span>
                        </button>
                        <button type="button" 
                                @click="setKomoditasPreset('patin')"
                                :class="simKomoditas === 'patin' ? 'bg-[#0B2570] text-white font-bold shadow-xs' : 'bg-white text-slate-700 hover:bg-slate-100 border border-slate-200'"
                                class="px-3 py-2 rounded-xl text-xs flex items-center justify-center gap-2 transition-all">
                            <span>🐋 Patin</span>
                        </button>
                        <button type="button" 
                                @click="setKomoditasPreset('custom')"
                                :class="simKomoditas === 'custom' ? 'bg-[#0B2570] text-white font-bold shadow-xs' : 'bg-white text-slate-700 hover:bg-slate-100 border border-slate-200'"
                                class="px-3 py-2 rounded-xl text-xs flex items-center justify-center gap-2 transition-all">
                            <span>⚙️ Kustom Manual</span>
                        </button>
                    </div>
                </div>

                <!-- Input Parameters Grid -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <!-- Kolom 1: Biomassa -->
                    <div class="p-4 rounded-2xl bg-white border border-slate-200 space-y-3">
                        <div class="text-xs font-extrabold text-[#0B2570] flex items-center gap-2">
                            <i class="fa-solid fa-weight-scale text-sky-600"></i>
                            <span>1. Parameter Biomassa</span>
                        </div>
                        <div>
                            <label class="block text-[11px] font-semibold text-slate-600 mb-1">Bobot Tebar Awal (W₀)</label>
                            <div class="relative">
                                <input type="text" inputmode="decimal" maxlength="6" x-model="simTebar" 
                                       @input="cleanSimInput($event, 'simTebar', 50000, 1)"
                                       @blur="blurSimInput($event, 'simTebar', 0)"
                                       class="w-full pl-3 pr-10 py-2 text-xs rounded-xl border border-slate-300 focus:ring-2 focus:ring-[#0B2570]">
                                <span class="absolute right-3 top-2 text-[10px] text-slate-400 font-bold pointer-events-none select-none">kg</span>
                            </div>
                        </div>
                        <div>
                            <label class="block text-[11px] font-semibold text-slate-600 mb-1">Bobot Panen / Akhir (Wₜ)</label>
                            <div class="relative">
                                <input type="text" inputmode="decimal" maxlength="7" x-model="simPanen" 
                                       @input="cleanSimInput($event, 'simPanen', 100000, 1)"
                                       @blur="blurSimInput($event, 'simPanen', 0)"
                                       class="w-full pl-3 pr-10 py-2 text-xs rounded-xl border border-slate-300 focus:ring-2 focus:ring-[#0B2570]">
                                <span class="absolute right-3 top-2 text-[10px] text-slate-400 font-bold pointer-events-none select-none">kg</span>
                            </div>
                        </div>
                        <div>
                            <label class="block text-[11px] font-semibold text-slate-600 mb-1">Total Pakan Diberikan (F)</label>
                            <div class="relative">
                                <input type="text" inputmode="decimal" maxlength="7" x-model="simPakan" 
                                       @input="cleanSimInput($event, 'simPakan', 100000, 1)"
                                       @blur="blurSimInput($event, 'simPakan', 0)"
                                       class="w-full pl-3 pr-10 py-2 text-xs rounded-xl border border-slate-300 focus:ring-2 focus:ring-[#0B2570]">
                                <span class="absolute right-3 top-2 text-[10px] text-slate-400 font-bold pointer-events-none select-none">kg</span>
                            </div>
                        </div>
                    </div>

                    <!-- Kolom 2: Mortalitas & Target SOP -->
                    <div class="p-4 rounded-2xl bg-white border border-slate-200 space-y-3">
                        <div class="text-xs font-extrabold text-[#0B2570] flex items-center gap-2">
                            <i class="fa-solid fa-skull-crossbones text-rose-500"></i>
                            <span>2. Mortalitas &amp; Batas SOP</span>
                        </div>
                        <div>
                            <label class="block text-[11px] font-semibold text-slate-600 mb-1">Ikan Mati Terdata (M)</label>
                            <div class="relative">
                                <input type="text" inputmode="decimal" maxlength="6" x-model="simMatiTerdata" 
                                       @input="cleanSimInput($event, 'simMatiTerdata', 50000, 1)"
                                       @blur="blurSimInput($event, 'simMatiTerdata', 0)"
                                       class="w-full pl-3 pr-10 py-2 text-xs rounded-xl border border-slate-300 focus:ring-2 focus:ring-[#0B2570]">
                                <span class="absolute right-3 top-2 text-[10px] text-slate-400 font-bold pointer-events-none select-none">kg</span>
                            </div>
                        </div>
                        <div>
                            <label class="block text-[11px] font-semibold text-slate-600 mb-1">Mati Tak Tercatat (Uncounted)</label>
                            <div class="relative">
                                <input type="text" inputmode="decimal" maxlength="6" x-model="simMatiTakTercatat" 
                                       @input="cleanSimInput($event, 'simMatiTakTercatat', 50000, 1)"
                                       @blur="blurSimInput($event, 'simMatiTakTercatat', 0)"
                                       class="w-full pl-3 pr-10 py-2 text-xs rounded-xl border border-slate-300 focus:ring-2 focus:ring-[#0B2570]">
                                <span class="absolute right-3 top-2 text-[10px] text-slate-400 font-bold pointer-events-none select-none">kg</span>
                            </div>
                        </div>
                        <div>
                            <label class="block text-[11px] font-semibold text-slate-600 mb-1">Target Maksimal FCR SOP</label>
                            <div class="relative">
                                <input type="text" inputmode="decimal" maxlength="4" x-model="simTargetFcr" 
                                       @input="cleanSimInput($event, 'simTargetFcr', 3.0, 2)"
                                       @blur="blurSimInput($event, 'simTargetFcr', 1.10)"
                                       class="w-full pl-3 pr-14 py-2 text-xs rounded-xl border border-slate-300 focus:ring-2 focus:ring-[#0B2570]">
                                <span class="absolute right-3 top-2 text-[10px] text-slate-400 font-bold pointer-events-none select-none">ratio</span>
                            </div>
                        </div>
                    </div>

                    <!-- Kolom 3: Harga Finansial -->
                    <div class="p-4 rounded-2xl bg-white border border-slate-200 space-y-3">
                        <div class="text-xs font-extrabold text-[#0B2570] flex items-center gap-2">
                            <i class="fa-solid fa-coins text-amber-500"></i>
                            <span>3. Harga &amp; Valuasi</span>
                        </div>
                        <div>
                            <label class="block text-[11px] font-semibold text-slate-600 mb-1">Harga Pakan / kg</label>
                            <div class="relative">
                                <span class="absolute left-3 top-2 text-[10px] text-slate-400 font-bold pointer-events-none select-none">Rp</span>
                                <input type="text" inputmode="numeric" maxlength="7" x-model="simHargaPakan" 
                                       @input="cleanSimInput($event, 'simHargaPakan', 1000000, 0)"
                                       @blur="blurSimInput($event, 'simHargaPakan', 0)"
                                       class="w-full pl-9 pr-3 py-2 text-xs rounded-xl border border-slate-300 focus:ring-2 focus:ring-[#0B2570]">
                            </div>
                        </div>
                        <div>
                            <label class="block text-[11px] font-semibold text-slate-600 mb-1">Harga Jual Ikan / kg</label>
                            <div class="relative">
                                <span class="absolute left-3 top-2 text-[10px] text-slate-400 font-bold pointer-events-none select-none">Rp</span>
                                <input type="text" inputmode="numeric" maxlength="7" x-model="simHargaJual" 
                                       @input="cleanSimInput($event, 'simHargaJual', 1000000, 0)"
                                       @blur="blurSimInput($event, 'simHargaJual', 0)"
                                       class="w-full pl-9 pr-3 py-2 text-xs rounded-xl border border-slate-300 focus:ring-2 focus:ring-[#0B2570]">
                            </div>
                        </div>
                        <div>
                            <label class="block text-[11px] font-semibold text-slate-600 mb-1">Pertumbuhan Bersih (ΔW)</label>
                            <div class="px-3 py-2 bg-slate-100 rounded-xl text-xs font-bold text-slate-700 flex justify-between items-center">
                                <span>Net Biomassa</span>
                                <span x-text="simBiomassaNetto + ' kg'">0 kg</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Simulation Output Metric Cards -->
                <div>
                    <div class="text-xs font-extrabold text-slate-800 uppercase tracking-wider mb-3">Hasil Evaluasi Multi-Metode:</div>
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                        <div class="p-4 rounded-2xl bg-sky-50 border border-sky-200">
                            <div class="text-[10px] font-extrabold uppercase text-sky-800">FCR KOMERSIAL</div>
                            <div class="text-2xl font-black text-[#0B2570] mt-1" x-text="simFcrKomersial">1.07</div>
                            <div class="text-[10px] text-slate-500 mt-1">Pakan ÷ Bobot Terjual</div>
                        </div>

                        <div class="p-4 rounded-2xl bg-indigo-50 border border-indigo-200">
                            <div class="text-[10px] font-extrabold uppercase text-indigo-800">FCR BIOLOGIS</div>
                            <div class="text-2xl font-black text-indigo-950 mt-1" x-text="simFcrBiologis">1.01</div>
                            <div class="text-[10px] text-slate-500 mt-1">Memperhitungkan Mati</div>
                        </div>

                        <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200">
                            <div class="text-[10px] font-extrabold uppercase text-emerald-800">FEED EFFICIENCY</div>
                            <div class="text-2xl font-black text-emerald-700 mt-1">
                                <span x-text="simFeedEfficiency">93.5</span>%
                            </div>
                            <div class="text-[10px] text-slate-500 mt-1">Konversi Daging Efektif</div>
                        </div>

                        <div class="p-4 rounded-2xl border transition-all"
                             :class="simWastedFeedKg > 0 ? 'bg-rose-50 border-rose-200' : 'bg-slate-50 border-slate-200'">
                            <div class="text-[10px] font-extrabold uppercase"
                                 :class="simWastedFeedKg > 0 ? 'text-rose-800' : 'text-slate-600'">
                                PAKAN TERBUANG
                            </div>
                            <div class="text-2xl font-black mt-1"
                                 :class="simWastedFeedKg > 0 ? 'text-rose-600' : 'text-slate-800'">
                                <span x-text="simWastedFeedKg">0</span> <span class="text-xs font-semibold">kg</span>
                            </div>
                            <div class="text-[10px] font-bold mt-1"
                                 :class="simWastedFeedKg > 0 ? 'text-rose-700' : 'text-emerald-600'"
                                 x-text="simWastedFeedKg > 0 ? 'Rupiah: Rp ' + simWastedFeedRp.toLocaleString('id-ID') : 'Nol Pemborosan (Aman)'">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Financial Loss & Profit Impact Assessment -->
                <div class="p-4 rounded-2xl border"
                     :class="simWastedFeedKg > 0 ? 'bg-rose-50/50 border-rose-200 text-rose-900' : 'bg-emerald-50/50 border-emerald-200 text-emerald-900'">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl flex items-center justify-center font-bold text-lg"
                                 :class="simWastedFeedKg > 0 ? 'bg-rose-100 text-rose-600' : 'bg-emerald-100 text-emerald-600'">
                                <i :class="simWastedFeedKg > 0 ? 'fa-solid fa-triangle-exclamation' : 'fa-solid fa-circle-check'"></i>
                            </div>
                            <div>
                                <div class="text-xs font-extrabold">Audit Finansial Dampak FCR</div>
                                <div class="text-xs font-semibold opacity-90 mt-0.5">
                                    <template x-if="simWastedFeedKg > 0">
                                        <span>Potensi kerugian pakan terbuang: <strong>Rp <span x-text="simWastedFeedRp.toLocaleString('id-ID')">0</span></strong></span>
                                    </template>
                                    <template x-if="simWastedFeedKg === 0">
                                        <span>Efisiensi pakan 100% terjaga sesuai target SOP, tidak terdeteksi kebocoran biaya pakan.</span>
                                    </template>
                                </div>
                            </div>
                        </div>
                        <div class="text-right">
                            <div class="text-[10px] uppercase font-bold text-slate-500">Nilai Jual Panen Bruto</div>
                            <div class="text-sm font-black text-slate-800">
                                Rp <span x-text="(simPanen * simHargaJual).toLocaleString('id-ID')">0</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Status Evaluasi Komprehensif -->
                <div class="p-4 rounded-2xl bg-slate-100 border border-slate-200 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-full flex items-center justify-center text-white"
                             :class="{
                                 'bg-emerald-500': simStatusEvaluation.color === 'emerald',
                                 'bg-sky-500': simStatusEvaluation.color === 'sky',
                                 'bg-amber-500': simStatusEvaluation.color === 'amber',
                                 'bg-rose-500': simStatusEvaluation.color === 'rose',
                                 'bg-slate-400': simStatusEvaluation.color === 'slate'
                             }">
                            <i class="fa-solid fa-info text-xs"></i>
                        </div>
                        <div>
                            <div class="text-sm font-extrabold" x-text="simStatusEvaluation.label">Sangat Efisien</div>
                            <p class="text-xs opacity-80" x-text="simStatusEvaluation.desc">FCR di bawah standar SOP</p>
                        </div>
                    </div>
                    <div class="text-xs font-semibold px-3 py-1.5 rounded-xl bg-white/80 border shadow-2xs">
                        Rasio Efisiensi Target: <span class="font-bold font-mono" x-text="simTargetFcr">1.10</span>
                    </div>
                </div>
            </div>

            <!-- Modal Footer -->
            <div class="px-6 py-4 bg-slate-50 border-t border-slate-200 flex items-center justify-between">
                <span class="text-xs text-slate-500">
                    <i class="fa-solid fa-shield-halved text-sky-600 mr-1"></i> Standar Operasional Budidaya AMS (Zero Miss Loss)
                </span>
                <button type="button" @click="fcrModalOpen = false" class="px-4 py-2 bg-[#051B44] text-white text-xs font-bold rounded-xl hover:bg-[#0B2570] transition-colors">
                    Tutup Simulator
                </button>
            </div>
        </div>
    </div>

</div>
@endsection

@push('styles')
<link rel="stylesheet" href="{{ asset('leaflet/leaflet.css') }}" />
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="{{ asset('leaflet/leaflet.js') }}"></script>
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
    .custom-slim-scroll::-webkit-scrollbar {
        width: 4px;
    }
    .custom-slim-scroll::-webkit-scrollbar-track {
        background: transparent;
    }
    .custom-slim-scroll::-webkit-scrollbar-thumb {
        background: #CBD5E1;
        border-radius: 9999px;
    }
    .custom-slim-scroll::-webkit-scrollbar-thumb:hover {
        background: #94A3B8;
    }
</style>
<script>
    function dashboardData() {
        const _now = new Date();
        const _curY = _now.getFullYear();
        const _curM = _now.getMonth() + 1;
        const _curD = _now.getDate();
        const _curW = Math.min(5, Math.ceil(_curD / 7));
        const _mNamesShort = ['', 'Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];

        return {
            datePickerOpen: false,
            pickerMode: 'minggu',
            currentYear: _curY,
            currentMonth: _curM,
            currentDate: _curD,
            minYear: _curY - 2,
            pickerYear: _curY,
            pickerMonth: _curM,
            selectedPeriodKey: `w_${_curY}_${_curM}_${_curW}`,
            periodLabel: `Minggu ${_curW}, ${_mNamesShort[_curM]} ${_curY}`,
            fullPeriodLabel: `Minggu ${_curW} (Periode Berjalan ${_mNamesShort[_curM]} ${_curY})`,
            chartDropdownOpen: false,
            chartPeriodLabel: '7 Hari Terakhir',
            
            // State Simulator FCR & Loss Audit
            fcrModalOpen: false,
            simKomoditas: 'lele',
            simTebar: 50,
            simPanen: 500,
            simPakan: 480,
            simMatiTerdata: 15,
            simMatiTakTercatat: 10,
            simHargaPakan: 12500,
            simHargaJual: 24000,
            simTargetFcr: '1.10',

            setKomoditasPreset(type) {
                this.simKomoditas = type;
                if (type === 'lele') {
                    this.simTargetFcr = '1.10';
                    this.simHargaPakan = 12500;
                    this.simHargaJual = 24000;
                } else if (type === 'nila') {
                    this.simTargetFcr = '1.35';
                    this.simHargaPakan = 11500;
                    this.simHargaJual = 32000;
                } else if (type === 'patin') {
                    this.simTargetFcr = '1.45';
                    this.simHargaPakan = 11000;
                    this.simHargaJual = 26000;
                }
            },

            cleanSimInput(event, prop, maxVal, maxDecimals = 1) {
                let el = event.target;
                let raw = el.value;
                let cleaned = String(raw).replace(/[^0-9.]/g, '');
                const dotIndex = cleaned.indexOf('.');
                if (dotIndex !== -1) {
                    cleaned = cleaned.slice(0, dotIndex + 1) + cleaned.slice(dotIndex + 1).replace(/\./g, '');
                }
                if (cleaned.length > 1 && cleaned.startsWith('0') && cleaned[1] !== '.') {
                    cleaned = cleaned.replace(/^0+/, '') || '0';
                }
                if (dotIndex !== -1 && maxDecimals >= 0) {
                    const parts = cleaned.split('.');
                    if (parts[1] && parts[1].length > maxDecimals) {
                        cleaned = parts[0] + '.' + parts[1].slice(0, maxDecimals);
                    }
                }
                if (cleaned !== '' && cleaned !== '.' && parseFloat(cleaned) > maxVal) {
                    cleaned = String(maxVal);
                }
                el.value = cleaned;
                this[prop] = cleaned;
            },

            blurSimInput(event, prop, defaultVal = 0) {
                let el = event.target;
                if (el.value === '' || el.value === '.' || isNaN(parseFloat(el.value))) {
                    el.value = String(defaultVal);
                    this[prop] = String(defaultVal);
                } else {
                    let val = parseFloat(el.value);
                    el.value = String(val);
                    this[prop] = String(val);
                }
            },

            get simBiomassaNetto() {
                const w0 = parseFloat(this.simTebar) || 0;
                const wt = parseFloat(this.simPanen) || 0;
                return Math.max(0, parseFloat((wt - w0).toFixed(1)));
            },

            get simFcrKomersial() {
                const net = this.simBiomassaNetto;
                if (net <= 0) return 0;
                const feed = parseFloat(this.simPakan) || 0;
                return parseFloat((feed / net).toFixed(2));
            },

            get simFcrBiologis() {
                const w0 = parseFloat(this.simTebar) || 0;
                const wt = parseFloat(this.simPanen) || 0;
                const m = parseFloat(this.simMatiTerdata) || 0;
                const u = parseFloat(this.simMatiTakTercatat) || 0;
                const feed = parseFloat(this.simPakan) || 0;
                const totalBio = (wt + m + u) - w0;
                if (totalBio <= 0) return 0;
                return parseFloat((feed / totalBio).toFixed(2));
            },

            get simFeedEfficiency() {
                const fcr = this.simFcrKomersial;
                if (fcr <= 0) return 0;
                return parseFloat(((1 / fcr) * 100).toFixed(1));
            },

            get simWastedFeedKg() {
                const net = this.simBiomassaNetto;
                const target = parseFloat(this.simTargetFcr) || 1.10;
                const feed = parseFloat(this.simPakan) || 0;
                const pakanIdeal = net * target;
                return Math.max(0, parseFloat((feed - pakanIdeal).toFixed(1)));
            },

            get simWastedFeedRp() {
                const wastedKg = this.simWastedFeedKg;
                const price = parseFloat(this.simHargaPakan) || 0;
                return Math.round(wastedKg * price);
            },

            get simStatusEvaluation() {
                const fcr = this.simFcrKomersial;
                const target = parseFloat(this.simTargetFcr) || 1.10;
                if (fcr === 0) return { label: 'Data Belum Lengkap', color: 'slate', desc: 'Isi parameter panen' };
                if (fcr <= target) return { label: 'Sangat Efisien & Prima', color: 'emerald', desc: 'FCR di bawah / setara standar SOP' };
                if (fcr <= target * 1.1) return { label: 'Optimal Wajar', color: 'sky', desc: 'FCR dalam batas toleransi budidaya' };
                if (fcr <= target * 1.25) return { label: 'Perlu Pengawasan Pakan', color: 'amber', desc: 'Pakan mulai inefisien, cek sisa pakan di kolam' };
                return { label: 'Inefisiensi Akut / Boros', color: 'rose', desc: 'Pakan terbuang tinggi, potensi overfeeding atau mortalitas tinggi' };
            },

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

            // Tab Finansial: 'bulan' | 'tahun' | 'breakdown'
            finTab: 'bulan',

            // Data Profitabilitas & Laba Rugi dari Backend
            financialSummary: {!! json_encode($financialSummary ?? []) !!},

            get currentFinancialData() {
                if (this.finTab === 'tahun') {
                    return this.financialSummary.tahun_ini || {
                        pemasukan_format: 'Rp 0',
                        pengeluaran_format: 'Rp 0',
                        laba_rugi_format: 'Rp 0',
                        is_untung: true,
                        status_label: 'Surplus',
                        margin_percent: 0,
                        cost_ratio: 0
                    };
                }
                return this.financialSummary.bulan_ini || {
                    pemasukan_format: 'Rp 0',
                    pengeluaran_format: 'Rp 0',
                    laba_rugi_format: 'Rp 0',
                    is_untung: true,
                    status_label: 'Surplus',
                    margin_percent: 0,
                    cost_ratio: 0
                };
            },

            metrics: {
                totalStok: '{{ $metrics["totalStok"] }}',
                totalStokTrend: '{{ $metrics["totalStokTrend"] }}',
                fcr: '{{ $metrics["fcr"] }}',
                fcrStatus: '{{ $metrics["fcrStatus"] }}',
                targetPanen: '{{ $metrics["targetPanen"] }}',
                targetPanenTag: '{{ $metrics["targetPanenTag"] }}',
                targetPanenNote: '{{ $metrics["targetPanenNote"] }}'
            },

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
                { num: 12, name: 'Desember', short: 'Des' }
            ],

            getAvailableYears() {
                const cy = this.currentYear;
                return [
                    { year: cy - 2, available: true, badge: `Arsip ${cy - 2}`, description: `Rekap Tahunan Periode ${cy - 2}` },
                    { year: cy - 1, available: true, badge: `Arsip ${cy - 1}`, description: `Rekap Tahunan Periode ${cy - 1}` },
                    { year: cy, available: true, badge: 'Tahun Berjalan', description: `Rekap Tahunan Periode ${cy}` },
                    { year: cy + 1, available: false, badge: 'Belum Ada Data', description: 'Tahun Mendatang (Belum Terjadi)' },
                ];
            },

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
                if (year < this.minYear) return false;
                if (year < this.currentYear) return true;
                if (year === this.currentYear) return mNum <= this.currentMonth;
                return false;
            },

            getWeeks(mNum, year) {
                const mName = this.getMonthName(mNum);
                const lastDay = new Date(year, mNum, 0).getDate();
                const rawWeeks = [
                    { index: 1, startDay: 1, endDay: 7 },
                    { index: 2, startDay: 8, endDay: 14 },
                    { index: 3, startDay: 15, endDay: 21 },
                    { index: 4, startDay: 22, endDay: 28 },
                    { index: 5, startDay: 29, endDay: lastDay }
                ];

                return rawWeeks.filter(w => w.startDay <= lastDay).map(w => {
                    const startLabel = `${w.startDay} ${mName.substring(0,3)}`;
                    const endLabel = `${w.endDay} ${mName.substring(0,3)}`;
                    return {
                        index: w.index,
                        label: `Minggu ${w.index}`,
                        range: `${startLabel} – ${endLabel}`,
                        startDay: w.startDay,
                        endDay: w.endDay
                    };
                });
            },

            applyWeek(weekObj) {
                const mName = this.getMonthName(this.pickerMonth);
                this.selectedPeriodKey = `w_${this.pickerYear}_${this.pickerMonth}_${weekObj.index}`;
                this.periodLabel = `${weekObj.label}, ${mName.substring(0,3)} ${this.pickerYear}`;
                this.fullPeriodLabel = `${weekObj.label} (${weekObj.range} ${this.pickerYear})`;
                this.datePickerOpen = false;
                this.triggerToast(`Periode dipilih: ${this.fullPeriodLabel}`, 'info');
            },

            applyMonth(mNum, year) {
                const mName = this.getMonthName(mNum);
                this.selectedPeriodKey = `m_${year}_${mNum}`;
                this.periodLabel = `${mName} ${year}`;
                this.fullPeriodLabel = `Bulan ${mName} ${year}`;
                this.datePickerOpen = false;
                this.triggerToast(`Periode dipilih: ${this.fullPeriodLabel}`, 'info');
            },

            applyYear(year) {
                this.selectedPeriodKey = `y_${year}`;
                this.periodLabel = `Tahun ${year}`;
                this.fullPeriodLabel = `Rekap Tahun Anggaran ${year}`;
                this.datePickerOpen = false;
                this.triggerToast(`Periode dipilih: ${this.fullPeriodLabel}`, 'info');
            },

            // Ekspor Excel
            exportExcel() {
                const periodeExport = this.fullPeriodLabel || this.periodLabel;
                this.triggerToast(`Menyiapkan laporan Excel [${periodeExport}]...`, 'info');

                const periodKey = this.selectedPeriodKey || 'all';
                const exportUrl = `{{ route('dashboard.export-excel') }}?period=${encodeURIComponent(periodKey)}&label=${encodeURIComponent(periodeExport)}`;

                setTimeout(() => {
                    window.location.href = exportUrl;
                }, 400);
            }
        };
    }

    // Chart Global Handler
    let dashboardChartInstance = null;
    const chartDatasets = {!! json_encode($chartDatasets ?? []) !!};

    window.setChartData = function(period) {
        if (!dashboardChartInstance || !chartDatasets[period]) return;
        const ds = chartDatasets[period];
        dashboardChartInstance.data.labels = ds.labels;
        dashboardChartInstance.data.datasets[0].data = ds.pelet;
        dashboardChartInstance.data.datasets[1].data = ds.daun;
        dashboardChartInstance.update();
    };

    document.addEventListener('DOMContentLoaded', function() {
        // 1. Inisialisasi Chart Analisis Pakan
        const canvas = document.getElementById('dashboardMainChart');
        if (canvas) {
            const ctx = canvas.getContext('2d');

            // Soft Gradient Area Fill
            const gradPelet = ctx.createLinearGradient(0, 0, 0, 260);
            gradPelet.addColorStop(0, 'rgba(11, 37, 112, 0.22)');
            gradPelet.addColorStop(1, 'rgba(11, 37, 112, 0.00)');

            const gradDaun = ctx.createLinearGradient(0, 0, 0, 260);
            gradDaun.addColorStop(0, 'rgba(16, 185, 129, 0.20)');
            gradDaun.addColorStop(1, 'rgba(16, 185, 129, 0.00)');

            dashboardChartInstance = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: chartDatasets['7d'].labels,
                    datasets: [
                        {
                            label: 'Pelet Komersial',
                            data: chartDatasets['7d'].pelet,
                            borderColor: '#0B2570',
                            backgroundColor: gradPelet,
                            fill: true,
                            tension: 0.42,
                            borderWidth: 2.8,
                            pointBackgroundColor: '#0B2570',
                            pointBorderColor: '#ffffff',
                            pointBorderWidth: 2,
                            pointRadius: 3.5,
                            pointHoverRadius: 6.5,
                        },
                        {
                            label: 'Dedaunan Organik',
                            data: chartDatasets['7d'].daun,
                            borderColor: '#10B981',
                            backgroundColor: gradDaun,
                            fill: true,
                            tension: 0.42,
                            borderWidth: 2.8,
                            pointBackgroundColor: '#10B981',
                            pointBorderColor: '#ffffff',
                            pointBorderWidth: 2,
                            pointRadius: 3.5,
                            pointHoverRadius: 6.5,
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    interaction: {
                        intersect: false,
                        mode: 'index',
                    },
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            backgroundColor: '#051B44',
                            titleFont: { family: 'Plus Jakarta Sans', size: 12, weight: '700' },
                            bodyFont: { family: 'Plus Jakarta Sans', size: 11, weight: '500' },
                            padding: 10,
                            cornerRadius: 10,
                            callbacks: {
                                label: function(context) {
                                    return ' ' + context.dataset.label + ': ' + context.raw + ' kg';
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

            if (markers.length > 0) {
                const group = new L.featureGroup(markers);
                map.fitBounds(group.getBounds().pad(0.15));
                if (markers.length === 1) {
                    map.setZoom(16);
                }
            }
            setTimeout(function() {
                map.invalidateSize();
            }, 300);

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

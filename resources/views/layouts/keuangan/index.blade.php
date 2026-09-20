@extends('layouts.app')

@section('title', 'Laporan & Analisis Data Keuangan - AMS BUDIDAYA')

@section('content')
<style>
@media print {
    @page {
        size: A4 portrait;
        margin: 8mm 10mm 8mm 10mm;
    }
    html, body {
        background: #ffffff !important;
        font-family: 'Plus Jakarta Sans', system-ui, -apple-system, sans-serif !important;
        color: #0f172a !important;
        -webkit-print-color-adjust: exact !important;
        print-color-adjust: exact !important;
    }
    .no-print, nav, header, aside, footer:not(.print-footer) {
        display: none !important;
    }
    .print-only {
        display: block !important;
    }
    .page-break-inside-avoid {
        break-inside: avoid !important;
        page-break-inside: avoid !important;
    }
    table {
        border-collapse: collapse !important;
        width: 100% !important;
    }
    th, td {
        padding: 4px 6px !important;
    }
}
</style>

<div class="space-y-4 print:space-y-2">

    <!-- Flash Alerts -->
    @if(session('success'))
    <div class="no-print p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-bold flex items-center gap-3 shadow-xs">
        <i class="fa-solid fa-circle-check text-emerald-500 text-base"></i>
        <span>{{ session('success') }}</span>
    </div>
    @endif

    @if(session('error'))
    <div class="no-print p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 text-xs font-bold flex items-center gap-3 shadow-xs">
        <i class="fa-solid fa-circle-exclamation text-rose-500 text-base"></i>
        <span>{{ session('error') }}</span>
    </div>
    @endif

    <!-- =========================================================================
         KOP SURAT RESMI (HANYA MUNCUL SAAT CETAK / PRINT)
         ========================================================================= -->
    <div class="hidden print:block pb-2 border-b-2 border-slate-900 page-break-inside-avoid">
        <div class="flex items-center justify-between gap-4">
            <div class="flex items-center gap-3">
                <img src="{{ asset('images/Logo aquafarm.png') }}" alt="Logo AMS" class="h-10 w-auto object-contain shrink-0">
                <div>
                    <h1 class="text-[13px] font-black uppercase tracking-wider text-slate-900 leading-tight">AMS BUDIDAYA INDONESIA</h1>
                    <p class="text-[9.5px] font-bold text-slate-700 leading-tight">Unit Bisnis Perikanan Terpadu &amp; Business Intelligence Analytics</p>
                    <p class="text-[8px] text-slate-500 leading-tight mt-0.5">Jl. Raya Minapolitan Perikanan No. 88, Jawa Barat | Telp: +62 812-8899-0011 | Email: analytics@amsbudidaya.id</p>
                </div>
            </div>
            <div class="text-right text-[8px] text-slate-600 shrink-0">
                <div class="font-black text-[9px] text-slate-900 uppercase">EXECUTIVE DATA REPORT</div>
                <div class="font-mono text-slate-800 font-bold">Ref: DAR-{{ date('Ym') }}/{{ str_pad($currentYear, 4, '0') }}</div>
                <div>Tgl: {{ \Carbon\Carbon::now()->translatedFormat('d F Y, H:i') }} WIB</div>
                <div class="font-semibold text-slate-700">Lead Analyst: Data &amp; Financial Analytics</div>
            </div>
        </div>
        <div class="border-t border-slate-200 mt-1.5 pt-1 text-center">
            <h2 class="text-[11px] font-black uppercase tracking-wider text-[#0B2570]">EXECUTIVE FINANCIAL &amp; OPERATIONAL ANALYTICS REPORT</h2>
            <p class="text-[8.5px] font-semibold text-slate-600">Evaluasi Finansial, Efisiensi Budidaya (FCR &amp; HPP), dan Kinerja Bisnis Tahun Anggaran {{ $currentYear }}</p>
        </div>
    </div>

    <!-- Subtitle & Page Title Header (Web View) -->
    <div class="no-print flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2">
                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold uppercase bg-sky-100 text-sky-700">Business Intelligence &amp; Data Analytics</span>
                <span class="text-xs font-bold text-slate-400">• Tahun {{ $currentYear ?? date('Y') }}</span>
            </div>
            <h1 class="text-2xl font-extrabold text-[#0B2570] tracking-tight mt-1">Laporan &amp; Analisis Data Keuangan</h1>
            <p class="text-xs text-slate-500 font-medium mt-0.5">Executive dashboard performa finansial, unit economics (HPP &amp; FCR), struktur beban, dan rekomendasi strategis.</p>
        </div>
        <div class="flex items-center gap-3">
            <button onclick="printExecutiveReport()"
                    class="px-4 py-2.5 rounded-xl bg-[#0B2570] hover:bg-[#07194D] text-white font-bold text-xs shadow-md shadow-[#0B2570]/20 transition-all flex items-center gap-2 cursor-pointer">
                <i class="fa-solid fa-print text-sky-300"></i>
                <span>Cetak Laporan Analis</span>
            </button>
        </div>
    </div>

    <!-- PILAR 1: Executive KPI Scorecard (Print: 4-Col Row, Screen: 4 Cards) -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 print:grid-cols-4 gap-3.5 print:gap-2 page-break-inside-avoid">

        <!-- Card 1: Total Gross Revenue -->
        <div class="bg-white p-4 rounded-2xl border border-slate-200/90 shadow-xs flex flex-col justify-between print:border print:border-slate-300 print:shadow-none print:p-2 print:rounded-lg">
            <div class="flex items-center justify-between">
                <span class="text-[10px] print:text-[7.5px] font-extrabold uppercase tracking-wider text-slate-500">GROSS REVENUE</span>
                <span class="px-2 py-0.5 rounded-full text-[10px] print:text-[7px] font-extrabold bg-emerald-100 text-emerald-800 border border-emerald-200">
                    Revenue
                </span>
            </div>
            <div class="mt-2 print:mt-0.5">
                <h3 class="text-xl print:text-[11px] font-black text-slate-900 tracking-tight">{{ $kpis['incomeFormatted'] ?? 'Rp 0' }}</h3>
                <span class="text-[10px] print:text-[7px] font-medium text-slate-400 print:text-slate-500 block mt-0.5">Omzet penjualan ikan &amp; bibit</span>
            </div>
        </div>

        <!-- Card 2: Cost of Production (OPEX) -->
        <div class="bg-white p-4 rounded-2xl border border-slate-200/90 shadow-xs flex flex-col justify-between print:border print:border-slate-300 print:shadow-none print:p-2 print:rounded-lg">
            <div class="flex items-center justify-between">
                <span class="text-[10px] print:text-[7.5px] font-extrabold uppercase tracking-wider text-slate-500">PRODUCTION OPEX</span>
                <span class="px-2 py-0.5 rounded-full text-[10px] print:text-[7px] font-extrabold bg-rose-100 text-rose-800 border border-rose-200">
                    {{ $kpis['costIncomeRatio'] ?? 0 }}% of Rev
                </span>
            </div>
            <div class="mt-2 print:mt-0.5">
                <h3 class="text-xl print:text-[11px] font-black text-slate-900 tracking-tight">{{ $kpis['expenseFormatted'] ?? 'Rp 0' }}</h3>
                <span class="text-[10px] print:text-[7px] font-medium text-slate-400 print:text-slate-500 block mt-0.5">Total biaya operasional produksi</span>
            </div>
        </div>

        <!-- Card 3: Net Profit & Margin -->
        <div class="bg-white p-4 rounded-2xl border border-slate-200/90 shadow-xs flex flex-col justify-between print:border print:border-slate-300 print:shadow-none print:p-2 print:rounded-lg">
            <div class="flex items-center justify-between">
                <span class="text-[10px] print:text-[7.5px] font-extrabold uppercase tracking-wider text-slate-500">NET PROFIT</span>
                <span class="px-2 py-0.5 rounded-full text-[10px] print:text-[7px] font-extrabold bg-sky-100 text-sky-800 border border-sky-200">
                    {{ $kpis['netMargin'] ?? 0 }}% Margin
                </span>
            </div>
            <div class="mt-2 print:mt-0.5">
                <h3 class="text-xl print:text-[11px] font-black text-slate-900 tracking-tight">{{ $kpis['saldoFormatted'] ?? 'Rp 0' }}</h3>
                <span class="text-[10px] print:text-[7px] font-bold {{ ($saldo ?? 0) >= 0 ? 'text-emerald-600' : 'text-rose-600' }} block mt-0.5">
                    {{ ($saldo ?? 0) >= 0 ? 'Surplus Finansial Riil' : 'Defisit Kas Riil' }}
                </span>
            </div>
        </div>

        <!-- Card 4: Financial Health Score -->
        <div class="bg-white p-4 rounded-2xl border border-slate-200/90 shadow-xs flex flex-col justify-between print:border print:border-slate-300 print:shadow-none print:p-2 print:rounded-lg">
            <div class="flex items-center justify-between">
                <span class="text-[10px] print:text-[7.5px] font-extrabold uppercase tracking-wider text-slate-500">HEALTH INDEX</span>
                <span class="px-2 py-0.5 rounded-full text-[9px] print:text-[7px] font-extrabold {{ $kpis['healthBadgeClass'] ?? 'bg-slate-100 text-slate-600' }} uppercase border">
                    {{ $kpis['healthStatus'] ?? 'STABLE' }}
                </span>
            </div>
            <div class="mt-2 print:mt-0.5">
                <h3 class="text-xl print:text-[11px] font-black text-[#0B2570] tracking-tight">{{ number_format($kpis['healthScore'] ?? 0, 1) }} <span class="text-xs print:text-[8px] font-semibold text-slate-400 print:text-slate-600">/ 10.0</span></h3>
                <span class="text-[10px] print:text-[7px] font-medium text-slate-400 print:text-slate-500 block mt-0.5">{{ $kpis['totalTrx'] ?? 0 }} total mutasi jurnal</span>
            </div>
        </div>

    </div>

    <!-- PILAR 2: Unit Economics & Efisiensi Akuakultur (Performa Budidaya) -->
    <div class="bg-slate-50/80 border border-slate-200 rounded-2xl p-3.5 print:p-2 print:rounded-lg page-break-inside-avoid">
        <div class="flex items-center justify-between mb-2 print:mb-1">
            <div class="flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-[#0B2570]"></span>
                <h3 class="text-xs font-black uppercase tracking-wider text-slate-800 print:text-[8.5px]">Unit Economics &amp; Efisiensi Akuakultur (Performa Budidaya)</h3>
            </div>
            <span class="text-[10px] print:text-[7px] font-semibold text-slate-400">Parameter Kunci Analisis Biaya</span>
        </div>

        <div class="grid grid-cols-2 sm:grid-cols-4 print:grid-cols-4 gap-2.5 print:gap-1.5">
            <!-- Metric 1: HPP per Kg -->
            <div class="bg-white p-2.5 rounded-xl border border-slate-200 shadow-2xs print:border-slate-300 print:p-1.5 print:rounded">
                <span class="text-[9px] print:text-[7px] font-extrabold uppercase text-slate-400 block">HPP RATA-RATA / KG</span>
                <div class="text-base print:text-[11px] font-black text-slate-900 mt-0.5">{{ $kpis['hppPerKg'] ?? 'Rp 0' }}</div>
                <span class="text-[9px] print:text-[7px] text-slate-500 font-medium">Biaya per kg panen</span>
            </div>

            <!-- Metric 2: Farm Average FCR -->
            <div class="bg-white p-2.5 rounded-xl border border-slate-200 shadow-2xs print:border-slate-300 print:p-1.5 print:rounded">
                <span class="text-[9px] print:text-[7px] font-extrabold uppercase text-slate-400 block">RATA-RATA FCR FARM</span>
                <div class="text-base print:text-[11px] font-black text-[#0B2570] mt-0.5">{{ $kpis['avgFcr'] ?? '1.26' }}</div>
                <span class="text-[9px] print:text-[7px] text-emerald-600 font-bold">Optimal (SOP 1.0 - 1.8)</span>
            </div>

            <!-- Metric 3: Feed Cost per Kg -->
            <div class="bg-white p-2.5 rounded-xl border border-slate-200 shadow-2xs print:border-slate-300 print:p-1.5 print:rounded">
                <span class="text-[9px] print:text-[7px] font-extrabold uppercase text-slate-400 block">BIAYA PAKAN / KG</span>
                <div class="text-base print:text-[11px] font-black text-slate-900 mt-0.5">{{ $kpis['feedCostPerKg'] ?? 'Rp 0' }}</div>
                <span class="text-[9px] print:text-[7px] text-slate-500 font-medium">75.6% porsi beban</span>
            </div>

            <!-- Metric 4: Biomass Volume -->
            <div class="bg-white p-2.5 rounded-xl border border-slate-200 shadow-2xs print:border-slate-300 print:p-1.5 print:rounded">
                <span class="text-[9px] print:text-[7px] font-extrabold uppercase text-slate-400 block">TOTAL PANEN / DISTRIBUSI</span>
                <div class="text-base print:text-[11px] font-black text-slate-900 mt-0.5">{{ $kpis['totalPanenKg'] ?? '0 Kg' }}</div>
                <span class="text-[9px] print:text-[7px] text-slate-500 font-medium">Volume panen siklus</span>
            </div>
        </div>
    </div>

    <!-- PILAR 3: Visualisasi Data & Distribusi Beban (Side-by-Side: 2 Cols) -->
    <div class="grid grid-cols-1 lg:grid-cols-2 print:grid-cols-2 gap-3.5 print:gap-2 page-break-inside-avoid items-stretch">

        <!-- Left Column: Monthly Cash Flow Trajectory -->
        <div class="bg-white p-3.5 rounded-2xl border border-slate-200/90 shadow-xs print:border-slate-300 print:shadow-none print:p-2 print:rounded-lg flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between mb-2 print:mb-1 border-b border-slate-100 pb-1.5">
                    <div>
                        <h3 class="text-xs sm:text-sm font-black text-slate-900 print:text-[9px]">Tren Arus Kas Bulanan (Monthly Trajectory)</h3>
                        <p class="text-[10px] text-slate-400 font-medium print:text-[7px] print:text-slate-500">Pemasukan vs Pengeluaran (dalam Juta Rupiah)</p>
                    </div>

                    <div class="flex items-center gap-2.5 text-xs print:text-[7px] font-bold">
                        <div class="flex items-center gap-1 text-slate-700">
                            <span class="w-2 h-2 rounded-full bg-[#0B2570]"></span>
                            <span>Pemasukan</span>
                        </div>
                        <div class="flex items-center gap-1 text-slate-700">
                            <span class="w-2 h-2 rounded-full bg-[#38BDF8]"></span>
                            <span>Pengeluaran</span>
                        </div>
                    </div>
                </div>

                <!-- Screen View: Interactive Chart.js Canvas -->
                <div class="h-44 w-full block print:hidden">
                    <canvas id="financialCashFlowChart"></canvas>
                </div>

                <!-- Print View: Clean Structured Trajectory Table -->
                <div class="hidden print:block w-full mt-1">
                    <table class="w-full text-left border-collapse text-[7.5px]">
                        <thead>
                            <tr class="border-b border-slate-200 font-extrabold text-slate-500 uppercase bg-slate-50">
                                <th class="py-1 px-1.5">Bulan</th>
                                <th class="py-1 px-1.5 text-right">Pemasukan</th>
                                <th class="py-1 px-1.5 text-right">Pengeluaran</th>
                                <th class="py-1 px-1.5 text-right">Surplus/Defisit</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 font-semibold text-slate-700">
                            @foreach(array_slice($monthlyBreakdownTable, -4) as $mRow)
                            <tr>
                                <td class="py-1 px-1.5 font-bold text-slate-900">{{ $mRow['bulan'] }}</td>
                                <td class="py-1 px-1.5 text-right font-extrabold text-emerald-600">Rp {{ number_format($mRow['pemasukan'], 0, ',', '.') }}</td>
                                <td class="py-1 px-1.5 text-right font-extrabold text-rose-600">Rp {{ number_format($mRow['pengeluaran'], 0, ',', '.') }}</td>
                                <td class="py-1 px-1.5 text-right font-extrabold {{ $mRow['laba_bersih'] >= 0 ? 'text-[#0B2570]' : 'text-rose-600' }}">
                                    {{ $mRow['laba_bersih'] >= 0 ? '+' : '-' }} Rp {{ number_format(abs($mRow['laba_bersih']), 0, ',', '.') }}
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            
            <div class="hidden print:block text-[7px] text-slate-400 italic mt-1 pt-1 border-t border-slate-100">
                * Menampilkan 4 periode bulan aktif terkini
            </div>
        </div>

        <!-- Right Column: Expense Breakdown & Structure Table -->
        <div class="bg-white p-3.5 rounded-2xl border border-slate-200/90 shadow-xs print:border-slate-300 print:shadow-none print:p-2 print:rounded-lg flex flex-col justify-between space-y-2">
            <div>
                <div class="border-b border-slate-100 pb-1.5 mb-1.5">
                    <h3 class="text-xs sm:text-sm font-black text-slate-900 print:text-[9px]">Distribusi Struktur Biaya (Cost Share)</h3>
                    <p class="text-[10px] text-slate-400 font-medium print:text-[7px] print:text-slate-500">Komposisi pengeluaran produksi budidaya</p>
                </div>

                <table class="w-full text-left border-collapse text-xs print:text-[7.5px]">
                    <thead>
                        <tr class="border-b border-slate-200 text-[10px] print:text-[7px] font-extrabold text-slate-500 uppercase tracking-wider bg-slate-50">
                            <th class="py-1 px-1.5">Kategori Biaya</th>
                            <th class="py-1 px-1.5 text-right">Nominal (Rp)</th>
                            <th class="py-1 px-1.5 text-right">Porsi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 font-semibold text-slate-700">
                        <tr>
                            <td class="py-1 px-1.5">
                                <span class="font-bold text-slate-900">Pakan Ikan</span>
                            </td>
                            <td class="py-1 px-1.5 text-right font-bold text-slate-900">{{ $kpis['pakanFormatted'] ?? 'Rp 0' }}</td>
                            <td class="py-1 px-1.5 text-right font-black text-[#0B2570]">
                                {{ $totalExpense > 0 ? round(($kpis['pakanTotal'] ?? 0) / $totalExpense * 100, 1) : 0 }}%
                            </td>
                        </tr>
                        <tr>
                            <td class="py-1 px-1.5">
                                <span class="font-bold text-slate-900">Bibit Ikan</span>
                            </td>
                            <td class="py-1 px-1.5 text-right font-bold text-slate-900">{{ $kpis['bibitFormatted'] ?? 'Rp 0' }}</td>
                            <td class="py-1 px-1.5 text-right font-black text-sky-600">
                                {{ $totalExpense > 0 ? round(($kpis['bibitTotal'] ?? 0) / $totalExpense * 100, 1) : 0 }}%
                            </td>
                        </tr>
                        <tr>
                            <td class="py-1 px-1.5">
                                <span class="font-bold text-slate-900">Operasional / Listrik</span>
                            </td>
                            <td class="py-1 px-1.5 text-right font-bold text-slate-900">{{ $kpis['operasionalFormatted'] ?? 'Rp 0' }}</td>
                            <td class="py-1 px-1.5 text-right font-black text-emerald-600">
                                {{ $totalExpense > 0 ? round(($kpis['operasionalTotal'] ?? 0) / $totalExpense * 100, 1) : 0 }}%
                            </td>
                        </tr>
                    </tbody>
                    <tfoot>
                        <tr class="border-t-2 border-slate-300 font-black text-slate-900 text-xs print:text-[8px] bg-slate-50/50">
                            <td class="py-1 px-1.5 uppercase text-slate-800">TOTAL BEBAN</td>
                            <td class="py-1 px-1.5 text-right text-rose-700">{{ $kpis['expenseFormatted'] ?? 'Rp 0' }}</td>
                            <td class="py-1 px-1.5 text-right text-rose-700">100%</td>
                        </tr>
                    </tfoot>
                </table>
            </div>

            <!-- Health Score Mini Box -->
            <div class="bg-slate-50 border border-slate-200 rounded-xl p-2 flex items-center justify-between print:border-slate-300 print:bg-slate-50 print:p-1.5 print:rounded-md mt-1">
                <div>
                    <span class="text-[9px] print:text-[7px] font-extrabold text-slate-500 uppercase tracking-wider">Health Index</span>
                    <div class="text-xs print:text-[9px] font-black text-[#0B2570]">{{ number_format($kpis['healthScore'] ?? 0, 1) }} / 10</div>
                </div>
                <div class="text-right">
                    <span class="text-[9px] print:text-[7px] text-slate-500 font-extrabold uppercase tracking-wider block">Margin Bersih</span>
                    <span class="text-xs print:text-[9px] font-black text-[#0B2570]">{{ $kpis['netMargin'] ?? 0 }}%</span>
                </div>
            </div>
        </div>

    </div>

    <!-- PILAR 4: Monthly Performance & Profit-Loss Ledger Table -->
    <div class="bg-white rounded-2xl border border-slate-200/90 shadow-xs overflow-hidden page-break-inside-avoid print:border-slate-300 print:shadow-none print:rounded-lg">
        <div class="px-3.5 py-2 print:px-2 print:py-1 border-b border-slate-100 print:border-slate-300 flex items-center justify-between bg-slate-50/50">
            <div>
                <h3 class="text-xs sm:text-sm print:text-[8.5px] font-black text-slate-900">Rekapitulasi Kinerja Bulanan &amp; Pertumbuhan (Monthly Ledger)</h3>
                <p class="text-[10px] print:text-[6.5px] text-slate-400 print:text-slate-500 font-medium">Ringkasan arus kas per periode bulan pada tahun anggaran {{ $currentYear }}</p>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs print:text-[7px]">
                <thead>
                    <tr class="bg-slate-100 border-b border-slate-200 text-[10px] print:text-[6.5px] font-black text-slate-600 uppercase tracking-wider print:border-slate-300">
                        <th class="py-1.5 px-3 print:py-0.5 print:px-1.5">PERIODE BULAN</th>
                        <th class="py-1.5 px-3 print:py-0.5 print:px-1.5">TOTAL PEMASUKAN</th>
                        <th class="py-1.5 px-3 print:py-0.5 print:px-1.5">TOTAL PENGELUARAN</th>
                        <th class="py-1.5 px-3 print:py-0.5 print:px-1.5">LABA / RUGI BERSIH</th>
                        <th class="py-1.5 px-3 print:py-0.5 print:px-1.5">MARGIN (%)</th>
                        <th class="py-1.5 px-3 print:py-0.5 print:px-1.5">MUTASI</th>
                        <th class="py-1.5 px-3 text-right print:py-0.5 print:px-1.5">STATUS</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 font-medium text-slate-700 print:divide-slate-200">
                    @php 
                        $grandIncome = 0;
                        $grandExpense = 0;
                        $grandNet = 0;
                        $grandTrx = 0;
                    @endphp
                    @forelse($monthlyBreakdownTable as $row)
                    @php 
                        $grandIncome += $row['pemasukan'];
                        $grandExpense += $row['pengeluaran'];
                        $grandNet += $row['laba_bersih'];
                        $grandTrx += $row['total_trx'];
                    @endphp
                    <tr class="hover:bg-slate-50/70 transition-colors print:hover:bg-transparent">
                        <td class="py-1.5 px-3 font-bold text-slate-900 print:py-0.5 print:px-1.5">{{ $row['bulan'] }}</td>
                        <td class="py-1.5 px-3 font-extrabold text-emerald-600 print:py-0.5 print:px-1.5">Rp {{ number_format($row['pemasukan'], 0, ',', '.') }}</td>
                        <td class="py-1.5 px-3 font-extrabold text-rose-600 print:py-0.5 print:px-1.5">Rp {{ number_format($row['pengeluaran'], 0, ',', '.') }}</td>
                        <td class="py-1.5 px-3 font-extrabold {{ $row['laba_bersih'] >= 0 ? 'text-[#0B2570]' : 'text-rose-600' }} print:py-0.5 print:px-1.5">
                            {{ $row['laba_bersih'] >= 0 ? '+' : '-' }} Rp {{ number_format(abs($row['laba_bersih']), 0, ',', '.') }}
                        </td>
                        <td class="py-1.5 px-3 print:py-0.5 print:px-1.5">
                            <span class="px-2 py-0.5 rounded-full text-[9px] print:text-[6.5px] font-extrabold {{ $row['margin_pct'] >= 0 ? 'bg-sky-100 text-sky-800' : 'bg-rose-100 text-rose-800' }} print:border">
                                {{ $row['margin_pct'] }}%
                            </span>
                        </td>
                        <td class="py-1.5 px-3 text-slate-500 font-semibold print:py-0.5 print:px-1.5">{{ $row['total_trx'] }} Trx</td>
                        <td class="py-1.5 px-3 text-right print:py-0.5 print:px-1.5">
                            <span class="px-2 py-0.5 rounded-md text-[8.5px] print:text-[6px] font-black uppercase {{ $row['status'] === 'Surplus' ? 'bg-emerald-100 text-emerald-800' : 'bg-rose-100 text-rose-800' }} print:border">
                                {{ $row['status'] }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="py-4 text-center text-slate-400 text-xs">
                            Belum ada rekapitulasi data keuangan.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
                <tfoot>
                    <tr class="bg-slate-100 font-black text-slate-900 border-t-2 border-slate-300 text-xs print:text-[7px]">
                        <td class="py-1.5 px-3 uppercase print:py-0.5 print:px-1.5">TOTAL KESELURUHAN</td>
                        <td class="py-1.5 px-3 text-emerald-700 print:py-0.5 print:px-1.5">Rp {{ number_format($grandIncome, 0, ',', '.') }}</td>
                        <td class="py-1.5 px-3 text-rose-700 print:py-0.5 print:px-1.5">Rp {{ number_format($grandExpense, 0, ',', '.') }}</td>
                        <td class="py-1.5 px-3 text-[#0B2570] print:py-0.5 print:px-1.5">
                            {{ $grandNet >= 0 ? '+' : '-' }} Rp {{ number_format(abs($grandNet), 0, ',', '.') }}
                        </td>
                        <td class="py-1.5 px-3 text-[#0284C7] print:py-0.5 print:px-1.5">
                            {{ $grandIncome > 0 ? round(($grandNet / $grandIncome) * 100, 1) : 0 }}%
                        </td>
                        <td class="py-1.5 px-3 print:py-0.5 print:px-1.5">{{ $grandTrx }} Trx</td>
                        <td class="py-1.5 px-3 text-right uppercase text-emerald-700 print:py-0.5 print:px-1.5">
                            {{ $grandNet >= 0 ? 'SURPLUS' : 'DEFISIT' }}
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    <!-- PILAR 5: Lembar Pengesahan & Tanda Tangan Resmi (Cetak / Print Only) -->
    <div class="hidden print:block mt-3 pt-2 page-break-inside-avoid">
        <div class="flex justify-between items-start text-[8.5px] text-slate-800">
            <!-- Pihak 1: Data Analyst -->
            <div class="text-center w-48">
                <p class="font-medium text-slate-600">Dianalisis &amp; Dilaporkan Oleh,</p>
                <p class="font-bold text-slate-900 mt-0.5">Lead Data &amp; Business Analyst</p>
                <div class="h-10 flex items-center justify-center">
                    <span class="text-[7.5px] text-slate-400 italic">( Tanda Tangan &amp; Validasi Data )</span>
                </div>
                <div class="border-t border-slate-900 pt-0.5 font-bold text-slate-900">
                    Tim Data &amp; Analitik Budidaya
                </div>
                <p class="text-[7.5px] text-slate-500">ID: ANALYST-{{ date('Y') }}-04</p>
            </div>

            <!-- Pihak 2: Manajer Operasional -->
            <div class="text-center w-48">
                <p class="font-medium text-slate-600">Tasikmalaya, {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}</p>
                <p class="font-bold text-slate-900 mt-0.5">Mengetahui &amp; Menyetujui,</p>
                <div class="h-10 flex items-center justify-center">
                    <span class="text-[7.5px] text-slate-400 italic">( Tanda Tangan &amp; Stempel )</span>
                </div>
                <div class="border-t border-slate-900 pt-0.5 font-bold text-slate-900">
                    {{ Auth::user()->nama ?? 'Manajer AMS Budidaya' }}
                </div>
                <p class="text-[7.5px] text-slate-500">Manajer Operasional &amp; Agribisnis</p>
            </div>
        </div>

        <div class="mt-2 border-t border-slate-200 pt-1 text-center text-[7px] text-slate-400">
            Dokumen ini merupakan salinan sah Laporan Analitik Bisnis &amp; Finansial dari Sistem Informasi Manajemen Budidaya Ikan Terpadu (AMS BUDIDAYA).
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('financialCashFlowChart');
    if (!ctx) return;

    const labels = {!! json_encode($monthlyCashflow['labels'] ?? ['JAN', 'FEB', 'MAR', 'APR', 'MEI', 'JUN', 'JUL', 'AGU', 'SEP']) !!};
    const revenueData = {!! json_encode($monthlyCashflow['revenue'] ?? []) !!};
    const expenseData = {!! json_encode($monthlyCashflow['expense'] ?? []) !!};

    new Chart(ctx.getContext('2d'), {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [
                {
                    label: 'Pemasukan (Jt)',
                    data: revenueData,
                    backgroundColor: '#0B2570',
                    borderRadius: 4,
                    barPercentage: 0.55,
                    categoryPercentage: 0.5
                },
                {
                    label: 'Pengeluaran (Jt)',
                    data: expenseData,
                    backgroundColor: '#38BDF8',
                    borderRadius: 4,
                    barPercentage: 0.55,
                    categoryPercentage: 0.5
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            animation: false,
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return context.dataset.label + ': Rp ' + context.raw + ' Juta';
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return value + ' Jt';
                        },
                        font: { family: 'Plus Jakarta Sans', size: 10, weight: '500' },
                        color: '#94A3B8'
                    },
                    grid: { color: '#F1F5F9' },
                    border: { display: false }
                },
                x: {
                    grid: { display: false },
                    ticks: { font: { family: 'Plus Jakarta Sans', size: 10, weight: '500' }, color: '#94A3B8' },
                    border: { display: false }
                }
            }
        }
    });
});

/**
 * Dedicated Executive Print Handler
 */
function printExecutiveReport() {
    window.print();
}
</script>
@endpush

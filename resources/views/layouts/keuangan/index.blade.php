@extends('layouts.app')

@section('title', 'Laporan & Analisis Keuangan - SIM-BUDIDAYA')

@section('content')
<div class="space-y-6">

    <!-- Flash Alerts -->
    @if(session('success'))
    <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-bold flex items-center gap-3 shadow-xs">
        <i class="fa-solid fa-circle-check text-emerald-500 text-base"></i>
        <span>{{ session('success') }}</span>
    </div>
    @endif

    @if(session('error'))
    <div class="p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 text-xs font-bold flex items-center gap-3 shadow-xs">
        <i class="fa-solid fa-circle-exclamation text-rose-500 text-base"></i>
        <span>{{ session('error') }}</span>
    </div>
    @endif

    <!-- Subtitle & Page Title Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2">
                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold uppercase bg-sky-100 text-sky-700">Financial Reports &amp; Analytics</span>
                <span class="text-xs font-bold text-slate-400">• Tahun {{ $currentYear ?? date('Y') }}</span>
            </div>
            <h1 class="text-2xl font-extrabold text-[#0B2570] tracking-tight mt-1">Laporan &amp; Analisis Keuangan</h1>
            <p class="text-xs text-slate-500 font-medium mt-0.5">Analisis komprehensif arus kas, profitabilitas, struktur pengeluaran, dan kesehatan finansial budidaya.</p>
        </div>
        <div class="flex items-center gap-3">
            <button onclick="window.print()"
                    class="px-4 py-2 rounded-xl bg-white hover:bg-slate-50 text-slate-700 border border-slate-200 font-bold text-xs shadow-2xs transition-all flex items-center gap-2 cursor-pointer">
                <i class="fa-solid fa-print text-slate-400"></i>
                <span>Cetak Laporan</span>
            </button>
        </div>
    </div>

    <!-- 4 Financial KPI Cards Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">

        <!-- Card 1: Total Revenue -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs flex flex-col justify-between">
            <div class="flex items-center justify-between">
                <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400">TOTAL PEMASUKAN</span>
                <span class="px-2.5 py-0.5 rounded-full text-[11px] font-extrabold bg-[#C6F6D5] text-[#22543D]">
                    <i class="fa-solid fa-arrow-trend-up text-[10px]"></i> Revenue
                </span>
            </div>
            <div class="mt-3">
                <h3 class="text-xl font-extrabold text-slate-900 tracking-tight">{{ $kpis['incomeFormatted'] ?? 'Rp 0' }}</h3>
                <span class="text-[10px] font-medium text-slate-400 block mt-0.5">Akumulasi seluruh penjualan panen &amp; benih</span>
            </div>
        </div>

        <!-- Card 2: Total Expenses -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs flex flex-col justify-between">
            <div class="flex items-center justify-between">
                <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400">TOTAL PENGELUARAN</span>
                <span class="px-2.5 py-0.5 rounded-full text-[11px] font-extrabold bg-[#FEE2E2] text-[#991B1B]">
                    <i class="fa-solid fa-arrow-trend-down text-[10px]"></i> Expenses
                </span>
            </div>
            <div class="mt-3">
                <h3 class="text-xl font-extrabold text-slate-900 tracking-tight">{{ $kpis['expenseFormatted'] ?? 'Rp 0' }}</h3>
                <span class="text-[10px] font-medium text-slate-400 block mt-0.5">Biaya pakan, listrik, dan operasional</span>
            </div>
        </div>

        <!-- Card 3: Net Profit -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs flex flex-col justify-between">
            <div class="flex items-center justify-between">
                <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400">LABA BERSIH (NET PROFIT)</span>
                <span class="px-2.5 py-0.5 rounded-full text-[11px] font-extrabold bg-[#E0F2FE] text-[#0284C7]">
                    <i class="fa-solid fa-chart-pie text-[10px]"></i> {{ $kpis['netMargin'] ?? 0 }}% Margin
                </span>
            </div>
            <div class="mt-3">
                <h3 class="text-xl font-extrabold text-slate-900 tracking-tight">{{ $kpis['saldoFormatted'] ?? 'Rp 0' }}</h3>
                <span class="text-[10px] font-semibold {{ ($saldo ?? 0) >= 0 ? 'text-emerald-600' : 'text-rose-600' }} block mt-0.5">
                    {{ ($saldo ?? 0) >= 0 ? 'Surplus Finansial Operasional' : 'Defisit Kas Finansial' }}
                </span>
            </div>
        </div>

        <!-- Card 4: Financial Health Score -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs flex flex-col justify-between">
            <div class="flex items-center justify-between">
                <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400">STATUS KESEHATAN KEUANGAN</span>
                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold {{ $kpis['healthBadgeClass'] ?? 'bg-slate-100 text-slate-600' }} uppercase">
                    {{ $kpis['healthStatus'] ?? 'STABLE' }}
                </span>
            </div>
            <div class="mt-3">
                <h3 class="text-xl font-extrabold text-[#0B2570] tracking-tight">{{ number_format($kpis['healthScore'] ?? 0, 1) }} <span class="text-xs font-semibold text-slate-400">/ 10.0</span></h3>
                <span class="text-[10px] font-medium text-slate-400 block mt-0.5">Berdasarkan {{ $kpis['totalTrx'] ?? 0 }} total mutasi tercatat</span>
            </div>
        </div>

    </div>

    <!-- Chart & Expense Breakdown (2 Columns) -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <!-- Left 2 Cols: Monthly Cash Flow Bar Chart -->
        <div class="lg:col-span-2 bg-white p-6 rounded-2xl border border-slate-200/80 shadow-xs">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h3 class="text-base font-bold text-slate-900">Arus Kas Bulanan (Monthly Cash Flow)</h3>
                    <p class="text-xs text-slate-400 font-medium">Perbandingan Pemasukan vs Pengeluaran (dalam Juta Rupiah)</p>
                </div>

                <div class="flex items-center gap-4 text-xs font-medium">
                    <div class="flex items-center gap-1.5 text-slate-700">
                        <span class="w-2.5 h-2.5 rounded-full bg-[#0B2570]"></span>
                        <span class="font-bold">Pemasukan</span>
                    </div>
                    <div class="flex items-center gap-1.5 text-slate-700">
                        <span class="w-2.5 h-2.5 rounded-full bg-[#38BDF8]"></span>
                        <span class="font-bold">Pengeluaran</span>
                    </div>
                </div>
            </div>

            <div class="h-64 w-full">
                <canvas id="financialCashFlowChart"></canvas>
            </div>
        </div>

        <!-- Right 1 Col: Expense Distribution & Analytics -->
        <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-xs flex flex-col justify-between space-y-6">
            <div>
                <h3 class="text-base font-bold text-slate-900 mb-1">Distribusi Beban Biaya</h3>
                <p class="text-xs text-slate-400 font-medium mb-4">Komposisi pengeluaran terbesar dalam budidaya</p>

                <div class="space-y-4 text-xs">

                    <!-- Item 1: Pakan -->
                    <div>
                        <div class="flex items-center justify-between font-bold text-slate-700 mb-1.5">
                            <span class="flex items-center gap-1.5">
                                <i class="fa-solid fa-wheat-awn text-[#0B2570]"></i>
                                <span>Pakan Ikan (Feed)</span>
                            </span>
                            <span class="font-extrabold text-slate-900">{{ $kpis['pakanFormatted'] ?? 'Rp 0' }}</span>
                        </div>
                        <div class="w-full bg-slate-100 h-2.5 rounded-full overflow-hidden">
                            <div class="bg-[#0B2570] h-full rounded-full transition-all" style="width: {{ $totalExpense > 0 ? min(100, round(($kpis['pakanTotal'] ?? 0) / $totalExpense * 100)) : 0 }}%"></div>
                        </div>
                        <span class="text-[10px] text-slate-400 font-semibold block mt-1">
                            {{ $totalExpense > 0 ? round(($kpis['pakanTotal'] ?? 0) / $totalExpense * 100, 1) : 0 }}% dari total pengeluaran
                        </span>
                    </div>

                    <!-- Item 2: Operasional -->
                    <div>
                        <div class="flex items-center justify-between font-bold text-slate-700 mb-1.5">
                            <span class="flex items-center gap-1.5">
                                <i class="fa-solid fa-gears text-[#10B981]"></i>
                                <span>Operasional &amp; Perawatan</span>
                            </span>
                            <span class="font-extrabold text-slate-900">{{ $kpis['operasionalFormatted'] ?? 'Rp 0' }}</span>
                        </div>
                        <div class="w-full bg-slate-100 h-2.5 rounded-full overflow-hidden">
                            <div class="bg-[#10B981] h-full rounded-full transition-all" style="width: {{ $totalExpense > 0 ? min(100, round(($kpis['operasionalTotal'] ?? 0) / $totalExpense * 100)) : 0 }}%"></div>
                        </div>
                        <span class="text-[10px] text-slate-400 font-semibold block mt-1">
                            {{ $totalExpense > 0 ? round(($kpis['operasionalTotal'] ?? 0) / $totalExpense * 100, 1) : 0 }}% dari total pengeluaran
                        </span>
                    </div>

                </div>
            </div>

            <!-- Financial Health Score Box -->
            <div class="bg-[#F4F7FA] border border-slate-200/70 rounded-xl p-4 flex items-center justify-between">
                <div>
                    <span class="text-[10px] font-bold text-slate-400 block uppercase tracking-wider">Health Index Score</span>
                    <h4 class="text-lg font-extrabold text-[#0B2570] mt-0.5">{{ number_format($kpis['healthScore'] ?? 0, 1) }} <span class="text-xs font-semibold text-slate-400">/ 10</span></h4>
                </div>
                <div class="text-right">
                    <span class="text-[10px] text-slate-500 font-medium block">Margin Bersih</span>
                    <span class="text-xs font-extrabold text-[#0B2570]">{{ $kpis['netMargin'] ?? 0 }}%</span>
                </div>
            </div>
        </div>

    </div>

    <!-- Monthly Breakdown Financial Statement Table -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
        <div class="p-5 border-b border-slate-100 flex items-center justify-between">
            <div>
                <h3 class="text-base font-bold text-slate-900">Rekapitulasi Laba Rugi Bulanan</h3>
                <p class="text-xs text-slate-400 font-medium">Ringkasan kinerja per periode bulan pada tahun berjalan</p>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50/70 border-b border-slate-100 text-[10px] font-extrabold text-slate-400 uppercase tracking-wider">
                        <th class="py-3.5 px-6">PERIODE BULAN</th>
                        <th class="py-3.5 px-6">TOTAL PEMASUKAN</th>
                        <th class="py-3.5 px-6">TOTAL PENGELUARAN</th>
                        <th class="py-3.5 px-6">LABA / RUGI BERSIH</th>
                        <th class="py-3.5 px-6">MARGIN (%)</th>
                        <th class="py-3.5 px-6">MUTASI</th>
                        <th class="py-3.5 px-6 text-right">STATUS</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-xs font-medium text-slate-700">
                    @forelse($monthlyBreakdownTable as $row)
                    <tr class="hover:bg-slate-50/70 transition-colors">
                        <td class="py-4 px-6 font-bold text-slate-900">{{ $row['bulan'] }}</td>
                        <td class="py-4 px-6 font-extrabold text-emerald-600">Rp {{ number_format($row['pemasukan'], 0, ',', '.') }}</td>
                        <td class="py-4 px-6 font-extrabold text-rose-600">Rp {{ number_format($row['pengeluaran'], 0, ',', '.') }}</td>
                        <td class="py-4 px-6 font-extrabold {{ $row['laba_bersih'] >= 0 ? 'text-[#0B2570]' : 'text-rose-600' }}">
                            {{ $row['laba_bersih'] >= 0 ? '+' : '-' }} Rp {{ number_format(abs($row['laba_bersih']), 0, ',', '.') }}
                        </td>
                        <td class="py-4 px-6">
                            <span class="px-2.5 py-0.5 rounded-full text-[11px] font-extrabold {{ $row['margin_pct'] >= 0 ? 'bg-[#E0F2FE] text-[#0284C7]' : 'bg-[#FEE2E2] text-[#991B1B]' }}">
                                {{ $row['margin_pct'] }}%
                            </span>
                        </td>
                        <td class="py-4 px-6 text-slate-500 font-semibold">{{ $row['total_trx'] }} Trx</td>
                        <td class="py-4 px-6 text-right">
                            <span class="px-2.5 py-1 rounded-lg text-[10px] font-extrabold uppercase {{ $row['status'] === 'Surplus' ? 'bg-[#C6F6D5] text-[#22543D]' : 'bg-[#FEE2E2] text-[#991B1B]' }}">
                                {{ $row['status'] }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="py-8 text-center text-slate-400 text-xs">
                            Belum ada rekapitulasi data keuangan.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
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
                    borderRadius: 6,
                    barPercentage: 0.6,
                    categoryPercentage: 0.5
                },
                {
                    label: 'Pengeluaran (Jt)',
                    data: expenseData,
                    backgroundColor: '#38BDF8',
                    borderRadius: 6,
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
                        font: { family: 'Plus Jakarta Sans', size: 11, weight: '500' },
                        color: '#94A3B8'
                    },
                    grid: { color: '#F1F5F9' },
                    border: { display: false }
                },
                x: {
                    grid: { display: false },
                    ticks: { font: { family: 'Plus Jakarta Sans', size: 11, weight: '500' }, color: '#94A3B8' },
                    border: { display: false }
                }
            }
        }
    });
});
</script>
@endpush

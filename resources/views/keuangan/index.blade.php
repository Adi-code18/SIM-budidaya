@extends('layouts.app')

@section('title', 'Financial Management - SIM-BUDIDAYA')

@section('content')
<div class="space-y-6">

    <!-- Header Title & Export Button -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">Financial Management</h1>
            <p class="text-sm text-slate-500 mt-1">Laporan arus kas, laba rugi budidaya, dan ringkasan pengeluaran pakan.</p>
        </div>
        <div class="flex items-center gap-3">
            <button class="px-4 py-2.5 rounded-xl bg-white border border-slate-200 text-slate-700 hover:bg-slate-50 font-bold text-xs sm:text-sm shadow-sm transition-all flex items-center gap-2">
                <i class="fa-solid fa-file-pdf text-rose-500"></i>
                <span>Cetak Laporan PDF</span>
            </button>
            <button class="px-4 py-2.5 rounded-xl bg-navy-800 hover:bg-navy-900 text-white font-bold text-xs sm:text-sm shadow-md transition-all flex items-center gap-2">
                <i class="fa-solid fa-plus"></i>
                <span>Catat Transaksi</span>
            </button>
        </div>
    </div>

    <!-- Financial KPI Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
        
        <!-- Card 1: Pendapatan -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Total Pendapatan</span>
                <span class="px-2 py-0.5 rounded-full text-[11px] font-bold bg-emerald-100 text-emerald-700">+12.5%</span>
            </div>
            <div class="mt-3">
                <h3 class="text-2xl font-extrabold text-slate-900">Rp 245,600,000</h3>
                <p class="text-xs text-slate-400 mt-1">Bulan Agustus 2026</p>
            </div>
        </div>

        <!-- Card 2: Pengeluaran -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Total Pengeluaran</span>
                <span class="px-2 py-0.5 rounded-full text-[11px] font-bold bg-emerald-100 text-emerald-700">-4.2% Efisien</span>
            </div>
            <div class="mt-3">
                <h3 class="text-2xl font-extrabold text-slate-900">Rp 124,300,000</h3>
                <p class="text-xs text-slate-400 mt-1">Termasuk Pakan & Bibit</p>
            </div>
        </div>

        <!-- Card 3: Laba Bersih -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Laba Bersih</span>
                <span class="px-2 py-0.5 rounded-full text-[11px] font-bold bg-emerald-100 text-emerald-700">+18.4%</span>
            </div>
            <div class="mt-3">
                <h3 class="text-2xl font-extrabold text-emerald-600">Rp 121,300,000</h3>
                <p class="text-xs text-slate-400 mt-1">Margin Profit 49.3%</p>
            </div>
        </div>

        <!-- Card 4: Saldo Kas Utama -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Saldo Kas Utama</span>
                <div class="w-8 h-8 rounded-lg bg-sky-50 text-sky-600 flex items-center justify-center font-bold text-xs">
                    <i class="fa-solid fa-building-columns"></i>
                </div>
            </div>
            <div class="mt-3">
                <h3 class="text-2xl font-extrabold text-slate-900">Rp 85,400,000</h3>
                <p class="text-xs text-slate-400 mt-1">Bank Mandiri / BCA</p>
            </div>
        </div>

    </div>

    <!-- Chart & Distribution Section -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        
        <!-- Bar Chart: Monthly Income vs Expenses (8 cols) -->
        <div class="lg:col-span-8 bg-white p-6 rounded-2xl border border-slate-200/80 shadow-sm space-y-4">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-lg font-bold text-slate-900">Grafik Keuangan Bulanan</h3>
                    <p class="text-xs text-slate-500">Perbandingan Pendapatan vs Pengeluaran 6 Bulan Terakhir</p>
                </div>
                <div class="flex items-center gap-3 text-xs font-semibold">
                    <span class="flex items-center gap-1.5 text-sky-600"><span class="w-3 h-3 rounded-full bg-sky-500"></span> Pendapatan</span>
                    <span class="flex items-center gap-1.5 text-rose-500"><span class="w-3 h-3 rounded-full bg-rose-400"></span> Pengeluaran</span>
                </div>
            </div>
            <div class="h-64 w-full">
                <canvas id="financialChart"></canvas>
            </div>
        </div>

        <!-- Expense Category Breakdown (4 cols) -->
        <div class="lg:col-span-4 bg-white p-6 rounded-2xl border border-slate-200/80 shadow-sm space-y-5">
            <h3 class="text-lg font-bold text-slate-900">Proporsi Pengeluaran</h3>
            <p class="text-xs text-slate-500">Alokasi dana pengeluaran operasional budidaya</p>

            <div class="space-y-4">
                <div>
                    <div class="flex justify-between text-xs font-bold mb-1">
                        <span class="text-slate-700">Pembelian Pakan Ikan</span>
                        <span class="text-sky-600">55% (Rp 68.3M)</span>
                    </div>
                    <div class="w-full h-2.5 bg-slate-100 rounded-full overflow-hidden">
                        <div class="h-full bg-sky-500 rounded-full" style="width: 55%"></div>
                    </div>
                </div>

                <div>
                    <div class="flex justify-between text-xs font-bold mb-1">
                        <span class="text-slate-700">Bibit & Benih Ikan</span>
                        <span class="text-emerald-600">25% (Rp 31.0M)</span>
                    </div>
                    <div class="w-full h-2.5 bg-slate-100 rounded-full overflow-hidden">
                        <div class="h-full bg-emerald-500 rounded-full" style="width: 25%"></div>
                    </div>
                </div>

                <div>
                    <div class="flex justify-between text-xs font-bold mb-1">
                        <span class="text-slate-700">Gaji & Operasional Mitra</span>
                        <span class="text-amber-600">15% (Rp 18.6M)</span>
                    </div>
                    <div class="w-full h-2.5 bg-slate-100 rounded-full overflow-hidden">
                        <div class="h-full bg-amber-500 rounded-full" style="width: 15%"></div>
                    </div>
                </div>

                <div>
                    <div class="flex justify-between text-xs font-bold mb-1">
                        <span class="text-slate-700">Logistik & Pemeliharaan</span>
                        <span class="text-purple-600">5% (Rp 6.4M)</span>
                    </div>
                    <div class="w-full h-2.5 bg-slate-100 rounded-full overflow-hidden">
                        <div class="h-full bg-purple-500 rounded-full" style="width: 5%"></div>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <!-- Transaction History Table -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
        <div class="p-6 border-b border-slate-100 flex items-center justify-between">
            <h3 class="text-lg font-bold text-slate-900">Riwayat Transaksi Keuangan</h3>
            <button class="text-xs font-bold text-sky-600 hover:text-sky-700">Filter Transaksi</button>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200/80 text-[11px] font-bold text-slate-500 uppercase tracking-wider">
                        <th class="py-3.5 px-6">Tanggal</th>
                        <th class="py-3.5 px-6">Keterangan Transaksi</th>
                        <th class="py-3.5 px-6">Kategori</th>
                        <th class="py-3.5 px-6">Tipe</th>
                        <th class="py-3.5 px-6">Jumlah (Rp)</th>
                        <th class="py-3.5 px-6">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-sm font-medium text-slate-700">
                    <tr class="hover:bg-slate-50/80">
                        <td class="py-4 px-6 text-xs text-slate-500">06 Ags 2026</td>
                        <td class="py-4 px-6 font-bold text-slate-900">Pembayaran Panen #ORD-2026-089 (PT Resto Seafood)</td>
                        <td class="py-4 px-6"><span class="px-2.5 py-1 rounded-lg bg-sky-50 text-sky-700 text-xs font-semibold">Penjualan</span></td>
                        <td class="py-4 px-6"><span class="text-emerald-600 font-bold">Pemasukan</span></td>
                        <td class="py-4 px-6 font-bold text-emerald-600">+ Rp 17,500,000</td>
                        <td class="py-4 px-6"><span class="px-2 py-0.5 rounded-full text-xs font-bold bg-emerald-100 text-emerald-700">Lunas</span></td>
                    </tr>

                    <tr class="hover:bg-slate-50/80">
                        <td class="py-4 px-6 text-xs text-slate-500">05 Ags 2026</td>
                        <td class="py-4 px-6 font-bold text-slate-900">Pembelian Pakan Hi-Pro-Vite 50 Karung</td>
                        <td class="py-4 px-6"><span class="px-2.5 py-1 rounded-lg bg-amber-50 text-amber-700 text-xs font-semibold">Pakan</span></td>
                        <td class="py-4 px-6"><span class="text-rose-600 font-bold">Pengeluaran</span></td>
                        <td class="py-4 px-6 font-bold text-rose-600">- Rp 12,800,000</td>
                        <td class="py-4 px-6"><span class="px-2 py-0.5 rounded-full text-xs font-bold bg-emerald-100 text-emerald-700">Lunas</span></td>
                    </tr>

                    <tr class="hover:bg-slate-50/80">
                        <td class="py-4 px-6 text-xs text-slate-500">03 Ags 2026</td>
                        <td class="py-4 px-6 font-bold text-slate-900">Pembayaran Panen #ORD-2026-090 (CV Pasar Mina)</td>
                        <td class="py-4 px-6"><span class="px-2.5 py-1 rounded-lg bg-sky-50 text-sky-700 text-xs font-semibold">Penjualan</span></td>
                        <td class="py-4 px-6"><span class="text-emerald-600 font-bold">Pemasukan</span></td>
                        <td class="py-4 px-6 font-bold text-emerald-600">+ Rp 19,200,000</td>
                        <td class="py-4 px-6"><span class="px-2 py-0.5 rounded-full text-xs font-bold bg-emerald-100 text-emerald-700">Lunas</span></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const ctx = document.getElementById('financialChart').getContext('2d');
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: ['Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Ags'],
                datasets: [
                    {
                        label: 'Pendapatan (Juta Rp)',
                        data: [180, 205, 220, 210, 235, 245.6],
                        backgroundColor: '#0284C7',
                        borderRadius: 6
                    },
                    {
                        label: 'Pengeluaran (Juta Rp)',
                        data: [110, 115, 130, 120, 128, 124.3],
                        backgroundColor: '#FB7185',
                        borderRadius: 6
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        grid: { color: '#F1F5F9' },
                        ticks: { font: { family: 'Plus Jakarta Sans', size: 11 } }
                    },
                    x: {
                        grid: { display: false },
                        ticks: { font: { family: 'Plus Jakarta Sans', size: 11 } }
                    }
                }
            }
        });
    });
</script>
@endpush

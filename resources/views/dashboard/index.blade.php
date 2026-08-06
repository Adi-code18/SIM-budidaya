@extends('layouts.app')

@section('title', 'Dashboard Utama - SIM-BUDIDAYA')

@section('content')
<div class="space-y-6">

    <!-- Header Title Section -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">Dashboard Utama</h1>
            <p class="text-sm text-slate-500 mt-1">Ringkasan performa budidaya, penggunaan pakan, dan status kolam hari ini.</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('log-pakan') }}" class="px-4 py-2.5 rounded-xl bg-sky-600 hover:bg-sky-700 text-white font-bold text-xs sm:text-sm shadow-md shadow-sky-600/20 transition-all flex items-center gap-2">
                <i class="fa-solid fa-plus text-xs"></i>
                <span>Log Pakan Baru</span>
            </a>
            <a href="{{ route('pembudidaya') }}" class="px-4 py-2.5 rounded-xl bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs sm:text-sm shadow-md transition-all flex items-center gap-2">
                <i class="fa-solid fa-water text-xs"></i>
                <span>Kelola Kolam</span>
            </a>
        </div>
    </div>

    <!-- Metric KPI Cards Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
        
        <!-- Card 1: Stok / Konsumsi Pakan -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-500">Stok Pakan Terdistribusi</span>
                <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center">
                    <i class="fa-solid fa-boxes-stacked text-lg"></i>
                </div>
            </div>
            <div class="mt-4">
                <div class="flex items-baseline gap-2">
                    <h3 class="text-2xl sm:text-3xl font-extrabold text-slate-900">1,254 <span class="text-sm font-semibold text-slate-500">kg</span></h3>
                </div>
                <div class="mt-2 flex items-center gap-1.5 text-xs font-semibold text-emerald-600">
                    <i class="fa-solid fa-arrow-trend-up"></i>
                    <span>+8.4% bulan ini</span>
                    <span class="text-slate-400 font-normal">vs bulan lalu</span>
                </div>
            </div>
        </div>

        <!-- Card 2: Kolam Aktif -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-500">Kolam Aktif Beroperasi</span>
                <div class="w-10 h-10 rounded-xl bg-sky-50 text-sky-600 flex items-center justify-center">
                    <i class="fa-solid fa-water text-lg"></i>
                </div>
            </div>
            <div class="mt-4">
                <div class="flex items-baseline gap-2">
                    <h3 class="text-2xl sm:text-3xl font-extrabold text-slate-900">132 <span class="text-sm font-semibold text-slate-500">kolam</span></h3>
                </div>
                <div class="mt-2 flex items-center gap-1.5 text-xs font-semibold text-sky-600">
                    <i class="fa-solid fa-circle-check"></i>
                    <span>94.2% Kapasitas terisi</span>
                </div>
            </div>
        </div>

        <!-- Card 3: Panen Bulan Ini -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-500">Panen Bulan Ini</span>
                <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center">
                    <i class="fa-solid fa-fish text-lg"></i>
                </div>
            </div>
            <div class="mt-4">
                <div class="flex items-baseline gap-2">
                    <h3 class="text-2xl sm:text-3xl font-extrabold text-slate-900">450 <span class="text-sm font-semibold text-slate-500">kg</span></h3>
                </div>
                <div class="mt-2 flex items-center gap-1.5 text-xs font-semibold text-emerald-600">
                    <i class="fa-solid fa-arrow-trend-up"></i>
                    <span>12 Batch Terdistribusi</span>
                </div>
            </div>
        </div>

        <!-- Card 4: FCR Rasio -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-500">Efisiensi FCR Rata-rata</span>
                <div class="w-10 h-10 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center">
                    <i class="fa-solid fa-bolt text-lg"></i>
                </div>
            </div>
            <div class="mt-4">
                <div class="flex items-baseline gap-2">
                    <h3 class="text-2xl sm:text-3xl font-extrabold text-slate-900">1.15 <span class="text-sm font-semibold text-emerald-600">Optimal</span></h3>
                </div>
                <div class="mt-2 flex items-center gap-1.5 text-xs font-semibold text-amber-600">
                    <i class="fa-solid fa-check"></i>
                    <span>Target &lt; 1.20 Tercapai</span>
                </div>
            </div>
        </div>

    </div>

    <!-- Main Chart Section -->
    <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-sm">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
            <div>
                <h3 class="text-lg font-bold text-slate-900">Grafik Pertumbuhan & Hasil Panen</h3>
                <p class="text-xs text-slate-500">Perbandingan jumlah pakan yang diberikan (kg) vs hasil panen ikan (kg)</p>
            </div>
            <div class="flex items-center gap-2 bg-slate-100 p-1 rounded-xl text-xs font-semibold text-slate-600">
                <button class="px-3 py-1.5 rounded-lg bg-white shadow text-slate-900 font-bold">Bulan Ini</button>
                <button class="px-3 py-1.5 rounded-lg hover:text-slate-900 transition-colors">3 Bulan</button>
                <button class="px-3 py-1.5 rounded-lg hover:text-slate-900 transition-colors">Tahun Ini</button>
            </div>
        </div>

        <div class="h-72 w-full">
            <canvas id="dashboardMainChart"></canvas>
        </div>
    </div>

    <!-- Table Section: Status Kolam Terbaru -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
        <div class="p-6 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h3 class="text-lg font-bold text-slate-900">Daftar Status Kolam Budidaya</h3>
                <p class="text-xs text-slate-500">Monitoring kondisi kesehatan, jenis ikan, dan estimasi tanggal panen</p>
            </div>
            <a href="{{ route('pembudidaya') }}" class="text-xs font-bold text-sky-600 hover:text-sky-700 flex items-center gap-1">
                <span>Lihat Semua Kolam</span>
                <i class="fa-solid fa-chevron-right text-[10px]"></i>
            </a>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200/80 text-[11px] font-bold text-slate-500 uppercase tracking-wider">
                        <th class="py-3.5 px-6">ID Kolam</th>
                        <th class="py-3.5 px-6">Jenis Ikan</th>
                        <th class="py-3.5 px-6">Penanggung Jawab</th>
                        <th class="py-3.5 px-6">Kapasitas / Nila</th>
                        <th class="py-3.5 px-6">Estimasi Panen</th>
                        <th class="py-3.5 px-6">Status Kolam</th>
                        <th class="py-3.5 px-6 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-sm font-medium text-slate-700">
                    <tr class="hover:bg-slate-50/80 transition-colors">
                        <td class="py-4 px-6 font-bold text-slate-900">KOLAM-01</td>
                        <td class="py-4 px-6">
                            <div class="flex items-center gap-2">
                                <div class="w-2.5 h-2.5 rounded-full bg-blue-500"></div>
                                <span>Ikan Nila Hitam</span>
                            </div>
                        </td>
                        <td class="py-4 px-6">Budi Santoso (Pembudidaya A)</td>
                        <td class="py-4 px-6 font-semibold">1,200 kg</td>
                        <td class="py-4 px-6">14 Ags 2026</td>
                        <td class="py-4 px-6">
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-700">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Sehat & Optimal
                            </span>
                        </td>
                        <td class="py-4 px-6 text-right">
                            <button class="px-3 py-1.5 rounded-lg bg-slate-100 hover:bg-sky-50 text-slate-600 hover:text-sky-600 text-xs font-bold transition-colors">
                                Detail
                            </button>
                        </td>
                    </tr>

                    <tr class="hover:bg-slate-50/80 transition-colors">
                        <td class="py-4 px-6 font-bold text-slate-900">KOLAM-02</td>
                        <td class="py-4 px-6">
                            <div class="flex items-center gap-2">
                                <div class="w-2.5 h-2.5 rounded-full bg-sky-500"></div>
                                <span>Ikan Lele Sangkuriang</span>
                            </div>
                        </td>
                        <td class="py-4 px-6">Ahmad Fauzi (Mitra Solo)</td>
                        <td class="py-4 px-6 font-semibold">850 kg</td>
                        <td class="py-4 px-6">08 Ags 2026</td>
                        <td class="py-4 px-6">
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold bg-amber-100 text-amber-700">
                                <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span> Siap Panen
                            </span>
                        </td>
                        <td class="py-4 px-6 text-right">
                            <button class="px-3 py-1.5 rounded-lg bg-slate-100 hover:bg-sky-50 text-slate-600 hover:text-sky-600 text-xs font-bold transition-colors">
                                Detail
                            </button>
                        </td>
                    </tr>

                    <tr class="hover:bg-slate-50/80 transition-colors">
                        <td class="py-4 px-6 font-bold text-slate-900">KOLAM-03</td>
                        <td class="py-4 px-6">
                            <div class="flex items-center gap-2">
                                <div class="w-2.5 h-2.5 rounded-full bg-indigo-500"></div>
                                <span>Ikan Gurame Super</span>
                            </div>
                        </td>
                        <td class="py-4 px-6">Siti Rahmawati (Unit Klaten)</td>
                        <td class="py-4 px-6 font-semibold">1,500 kg</td>
                        <td class="py-4 px-6">28 Sep 2026</td>
                        <td class="py-4 px-6">
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold bg-rose-100 text-rose-700">
                                <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span> Perlu Pakan Ekstra
                            </span>
                        </td>
                        <td class="py-4 px-6 text-right">
                            <button class="px-3 py-1.5 rounded-lg bg-slate-100 hover:bg-sky-50 text-slate-600 hover:text-sky-600 text-xs font-bold transition-colors">
                                Detail
                            </button>
                        </td>
                    </tr>

                    <tr class="hover:bg-slate-50/80 transition-colors">
                        <td class="py-4 px-6 font-bold text-slate-900">KOLAM-04</td>
                        <td class="py-4 px-6">
                            <div class="flex items-center gap-2">
                                <div class="w-2.5 h-2.5 rounded-full bg-blue-500"></div>
                                <span>Ikan Nila Merah</span>
                            </div>
                        </td>
                        <td class="py-4 px-6">Rahmat Hidayat (Pembudidaya B)</td>
                        <td class="py-4 px-6 font-semibold">950 kg</td>
                        <td class="py-4 px-6">19 Ags 2026</td>
                        <td class="py-4 px-6">
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-700">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Sehat & Optimal
                            </span>
                        </td>
                        <td class="py-4 px-6 text-right">
                            <button class="px-3 py-1.5 rounded-lg bg-slate-100 hover:bg-sky-50 text-slate-600 hover:text-sky-600 text-xs font-bold transition-colors">
                                Detail
                            </button>
                        </td>
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
        const ctx = document.getElementById('dashboardMainChart').getContext('2d');
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: ['1 Ags', '2 Ags', '3 Ags', '4 Ags', '5 Ags', '6 Ags', '7 Ags'],
                datasets: [
                    {
                        label: 'Log Pakan (kg)',
                        data: [120, 135, 140, 130, 155, 160, 175],
                        borderColor: '#0284C7',
                        backgroundColor: 'rgba(2, 132, 199, 0.1)',
                        fill: true,
                        tension: 0.4,
                        borderWidth: 3,
                        pointBackgroundColor: '#0284C7',
                        pointRadius: 4
                    },
                    {
                        label: 'Hasil Panen (kg)',
                        data: [80, 95, 110, 105, 130, 140, 160],
                        borderColor: '#10B981',
                        backgroundColor: 'rgba(16, 185, 129, 0.05)',
                        fill: true,
                        tension: 0.4,
                        borderWidth: 3,
                        pointBackgroundColor: '#10B981',
                        pointRadius: 4
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'top',
                        labels: {
                            font: { family: 'Plus Jakarta Sans', size: 12, weight: '600' },
                            usePointStyle: true,
                            padding: 20
                        }
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

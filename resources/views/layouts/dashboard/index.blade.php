@extends('layouts.app')

@section('title', 'Dashboard Utama - SIM-BUDIDAYA')

@section('content')
<div class="space-y-6">

    <!-- Title & Top Filter Section -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-extrabold text-[#0B2570] tracking-tight">Dashboard</h1>
            <p class="text-xs text-slate-500 font-medium mt-0.5">Operasional budidaya.</p>
        </div>
        <div class="flex items-center gap-3">
            <!-- Date Filter -->
            <div class="flex items-center gap-2 px-3 py-1.5 rounded-lg border border-slate-300 bg-white text-xs font-medium text-slate-600 shadow-xs cursor-pointer hover:bg-slate-50 transition-colors">
                <i class="fa-regular fa-calendar text-slate-400 text-xs"></i>
                <span>01 Agustus - 07 Agustus 2026</span>
                <i class="fa-solid fa-chevron-down text-[10px] text-slate-400 ml-1"></i>
            </div>

            <!-- Export Excel Button -->
            <button class="px-3.5 py-1.5 rounded-lg bg-[#0B6B2E] hover:bg-emerald-800 text-white font-bold text-xs flex items-center gap-2 shadow-xs transition-all tracking-wide">
                <i class="fa-solid fa-download text-xs"></i>
                <span>EKSPOR EXCEL</span>
            </button>
        </div>
    </div>

    <!-- 3 Top Metric KPI Cards Grid -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
        
        <!-- Card 1: Total Stok Ikan -->
        <div class="bg-[#DDF2FF] p-5 rounded-2xl border border-[#BEE3F8] shadow-xs flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between">
                    <span class="text-[11px] font-extrabold uppercase tracking-wider text-slate-600">TOTAL STOK IKAN</span>
                    <div class="w-9 h-9 rounded-xl bg-[#BEE3F8]/60 text-[#006699] flex items-center justify-center">
                        <i class="fa-solid fa-water text-base"></i>
                    </div>
                </div>
                <div class="mt-3">
                    <h3 class="text-3xl font-extrabold text-slate-900 tracking-tight">1,250 <span class="text-xs font-semibold text-slate-600">kg</span></h3>
                </div>
            </div>
            <div class="mt-4 flex items-center gap-1.5 text-xs font-semibold text-emerald-600">
                <i class="fa-solid fa-arrow-trend-up"></i>
                <span>+4.2% dari minggu lalu</span>
            </div>
        </div>

        <!-- Card 2: FCR Rata-rata -->
        <div class="bg-[#DDF2FF] p-5 rounded-2xl border border-[#BEE3F8] shadow-xs flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between">
                    <span class="text-[11px] font-extrabold uppercase tracking-wider text-slate-600">FCR RATA-RATA</span>
                    <div class="w-9 h-9 rounded-xl bg-[#BEE3F8]/60 text-[#006699] flex items-center justify-center">
                        <i class="fa-solid fa-chart-line text-base"></i>
                    </div>
                </div>
                <div class="mt-3">
                    <h3 class="text-3xl font-extrabold text-slate-900 tracking-tight">1.12</h3>
                </div>
            </div>
            <div class="mt-4 flex items-center gap-1.5 text-xs font-semibold text-emerald-600">
                <i class="fa-regular fa-circle-check"></i>
                <span>Efisiensi Pakan Optimal</span>
            </div>
        </div>

        <!-- Card 3: Target Panen -->
        <div class="bg-[#DDF2FF] p-5 rounded-2xl border border-[#BEE3F8] shadow-xs flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between">
                    <span class="text-[11px] font-extrabold uppercase tracking-wider text-slate-600">TARGET PANEN</span>
                    <div class="w-9 h-9 rounded-xl bg-[#BEE3F8]/60 text-[#006699] flex items-center justify-center">
                        <i class="fa-regular fa-calendar text-base"></i>
                    </div>
                </div>
                <div class="mt-3">
                    <h3 class="text-3xl font-extrabold text-slate-900 tracking-tight">450 <span class="text-xs font-semibold text-slate-600">kg</span></h3>
                </div>
            </div>
            <div class="mt-4 flex items-center gap-2 text-xs">
                <span class="px-2 py-0.5 rounded bg-[#E53E3E] text-white text-[10px] font-extrabold tracking-wider uppercase">CATATAN H-3</span>
                <span class="text-slate-600 font-medium">Restoran Madani</span>
            </div>
        </div>

    </div>

    <!-- Main Chart Section: Analisis Konsumsi Pakan -->
    <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-xs">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
            <h3 class="text-base font-bold text-slate-900">Analisis Konsumsi Pakan</h3>
            
            <div class="flex items-center gap-5 text-xs font-medium">
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
                <!-- Filter Dropdown -->
                <div class="flex items-center gap-2 px-3 py-1.5 rounded-lg border border-slate-200 bg-white text-slate-600 text-xs font-medium cursor-pointer shadow-xs">
                    <span>7 Hari Terakhir</span>
                    <i class="fa-solid fa-chevron-down text-[10px] text-slate-400"></i>
                </div>
            </div>
        </div>

        <div class="h-64 w-full">
            <canvas id="dashboardMainChart"></canvas>
        </div>
    </div>

    <!-- Table Section: Daftar Stok Siap Panen -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
        <div class="p-5 border-b border-slate-100 flex items-center justify-between">
            <h3 class="text-base font-bold text-slate-900">Daftar Stok Siap Panen</h3>
            <a href="{{ route('pembudidaya') }}" class="text-xs font-bold text-[#0055CC] hover:underline">
                Lihat Semua
            </a>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50/60 border-b border-slate-100 text-[10px] font-extrabold text-slate-400 uppercase tracking-wider">
                        <th class="py-3 px-6">KOLAM ID</th>
                        <th class="py-3 px-6">ESTIMASI BOBOT</th>
                        <th class="py-3 px-6">TUJUAN ALOKASI</th>
                        <th class="py-3 px-6">STATUS</th>
                        <th class="py-3 px-6 text-right">AKSI</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-xs font-medium text-slate-700">
                    
                    <!-- Row 1 -->
                    <tr class="hover:bg-slate-50/50 transition-colors">
                        <td class="py-3.5 px-6 font-bold text-[#0055CC]">Kolam A1</td>
                        <td class="py-3.5 px-6 font-semibold text-slate-800">150 kg</td>
                        <td class="py-3.5 px-6 text-slate-600">Restoran Madani</td>
                        <td class="py-3.5 px-6">
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[11px] font-extrabold bg-[#C6F6D5] text-[#22543D]">
                                <span class="w-1.5 h-1.5 rounded-full bg-[#38A169]"></span> READY
                            </span>
                        </td>
                        <td class="py-3.5 px-6 text-right">
                            <button class="text-slate-400 hover:text-slate-600 p-1">
                                <i class="fa-solid fa-ellipsis-vertical text-sm"></i>
                            </button>
                        </td>
                    </tr>

                    <!-- Row 2 -->
                    <tr class="hover:bg-slate-50/50 transition-colors">
                        <td class="py-3.5 px-6 font-bold text-[#0055CC]">Kolam B3</td>
                        <td class="py-3.5 px-6 font-semibold text-slate-800">210 kg</td>
                        <td class="py-3.5 px-6 text-slate-600">Pasar Modern BSD</td>
                        <td class="py-3.5 px-6">
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[11px] font-extrabold bg-[#C6F6D5] text-[#22543D]">
                                <span class="w-1.5 h-1.5 rounded-full bg-[#38A169]"></span> READY
                            </span>
                        </td>
                        <td class="py-3.5 px-6 text-right">
                            <button class="text-slate-400 hover:text-slate-600 p-1">
                                <i class="fa-solid fa-ellipsis-vertical text-sm"></i>
                            </button>
                        </td>
                    </tr>

                    <!-- Row 3 -->
                    <tr class="hover:bg-slate-50/50 transition-colors">
                        <td class="py-3.5 px-6 font-bold text-[#0055CC]">Kolam A4</td>
                        <td class="py-3.5 px-6 font-semibold text-slate-800">90 kg</td>
                        <td class="py-3.5 px-6 text-slate-600">Warung Seafood 88</td>
                        <td class="py-3.5 px-6">
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[11px] font-extrabold bg-[#EBF8FF] text-[#2B6CB0]">
                                <span class="w-1.5 h-1.5 rounded-full bg-[#3182CE]"></span> HOLD
                            </span>
                        </td>
                        <td class="py-3.5 px-6 text-right">
                            <button class="text-slate-400 hover:text-slate-600 p-1">
                                <i class="fa-solid fa-ellipsis-vertical text-sm"></i>
                            </button>
                        </td>
                    </tr>

                    <!-- Row 4 -->
                    <tr class="hover:bg-slate-50/50 transition-colors">
                        <td class="py-3.5 px-6 font-bold text-[#0055CC]">Kolam C2</td>
                        <td class="py-3.5 px-6 font-semibold text-slate-800">320 kg</td>
                        <td class="py-3.5 px-6 text-slate-600">Supplier Ekspor</td>
                        <td class="py-3.5 px-6">
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[11px] font-extrabold bg-[#C6F6D5] text-[#22543D]">
                                <span class="w-1.5 h-1.5 rounded-full bg-[#38A169]"></span> READY
                            </span>
                        </td>
                        <td class="py-3.5 px-6 text-right">
                            <button class="text-slate-400 hover:text-slate-600 p-1">
                                <i class="fa-solid fa-ellipsis-vertical text-sm"></i>
                            </button>
                        </td>
                    </tr>

                </tbody>
            </table>
        </div>
    </div>

    <!-- Bottom 2 Quick Info Widgets -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
        
        <!-- Widget 1: Stok Gudang Pakan -->
        <div class="bg-[#051B44] text-white p-5 rounded-2xl shadow-xs flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-white/10 border border-white/20 flex items-center justify-center shrink-0">
                <i class="fa-regular fa-clipboard text-xl text-white"></i>
            </div>
            <div>
                <h4 class="text-sm font-bold text-white">Stok Gudang Pakan</h4>
                <p class="text-xs text-sky-100/80 mt-0.5">Sisa stok 1,240 kg. Estimasi cukup untuk 14 hari kedepan.</p>
            </div>
        </div>

        <!-- Widget 2: Kualitas Air -->
        <div class="bg-[#E2E8F0] text-slate-900 p-5 rounded-2xl shadow-xs flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-slate-300/60 flex items-center justify-center shrink-0">
                <i class="fa-solid fa-droplet text-xl text-slate-700"></i>
            </div>
            <div>
                <h4 class="text-sm font-bold text-slate-900">Kualitas Air</h4>
                <p class="text-xs text-slate-600 mt-0.5">Semua kolam dalam parameter normal (pH 7.2 - 7.5).</p>
            </div>
        </div>

    </div>

</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const ctx = document.getElementById('dashboardMainChart').getContext('2d');
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: ['Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab', 'Min'],
                datasets: [
                    {
                        label: 'Pelet Komersial',
                        data: [18, 24, 22, 30, 35, 28, 32],
                        backgroundColor: '#0B2570',
                        borderRadius: 6,
                        borderSkipped: false,
                        barPercentage: 0.6,
                        categoryPercentage: 0.5
                    },
                    {
                        label: 'Dedaunan Organik',
                        data: [12, 16, 20, 18, 22, 24, 21],
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
                    }
                },
                scales: {
                    y: {
                        min: 0,
                        max: 50,
                        ticks: {
                            stepSize: 25,
                            callback: function(value) {
                                return value + 'kg';
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


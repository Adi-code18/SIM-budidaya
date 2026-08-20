@extends('mobile_web_petugas.petugas_pembibitan.layout')

@section('title', 'Dashboard Petugas Pembibitan - SIM-BUDIDAYA Mobile')

@section('content')
<div class="p-4 space-y-4">

    <!-- Header Section -->
    <div class="text-center pt-2 pb-1 space-y-1">
        <span class="text-[9px] font-extrabold uppercase text-slate-400 tracking-widest block">DASHBOARD</span>
        <h1 class="text-xl font-extrabold text-navy-900">Petugas Pembibitan</h1>
        <p class="text-xs text-slate-500 font-medium">Kelola pembibitan benih/indukan kamu.</p>
    </div>

    <!-- Primary Action Button: Input Batch Baru -->
    <a href="{{ route('petugas.pembibitan.form') }}" 
       class="w-full py-3.5 rounded-2xl bg-navy-800 hover:bg-navy-900 active:scale-[0.99] text-white font-extrabold text-xs flex items-center justify-center gap-2 shadow-md transition-all">
        <i class="fa-solid fa-plus text-xs"></i>
        <span>Input Batch Baru</span>
    </a>

    <!-- Key Metrics Highlight Card -->
    <div class="bg-gradient-to-br from-sky-100 via-sky-50 to-white rounded-3xl p-5 border border-sky-200/80 shadow-xs space-y-4">
        
        <!-- Metric 1: Total Benih Aktif -->
        <div class="space-y-1">
            <div class="flex items-center justify-between">
                <span class="text-[10px] font-extrabold uppercase text-slate-500 tracking-wider">TOTAL BENIH AKTIF</span>
                <span class="px-2 py-0.5 rounded-full text-[9px] font-bold bg-white text-sky-700 border border-sky-200 shadow-2xs">
                    +4.1% bulan ini
                </span>
            </div>
            <h2 class="text-3xl font-extrabold text-navy-900 tracking-tight">2.4M</h2>
        </div>

        <!-- Metrics Grid (FCR Air & Kapasitas Tank) -->
        <div class="grid grid-cols-2 gap-3 pt-3 border-t border-sky-200/60">
            
            <!-- FCR Air -->
            <div class="bg-white/80 rounded-2xl p-3 border border-sky-100/80 space-y-1">
                <div class="flex items-center gap-1.5 text-emerald-600">
                    <i class="fa-solid fa-droplet text-xs"></i>
                    <span class="text-[9px] font-extrabold uppercase text-slate-400">FCR AIR</span>
                </div>
                <h3 class="text-lg font-extrabold text-navy-900">85.2%</h3>
                <span class="text-[9px] text-emerald-600 font-bold block">Target: >80%</span>
            </div>

            <!-- Kapasitas Tank -->
            <div class="bg-white/80 rounded-2xl p-3 border border-sky-100/80 space-y-1">
                <div class="flex items-center gap-1.5 text-navy-800">
                    <i class="fa-solid fa-life-ring text-xs text-sky-600"></i>
                    <span class="text-[9px] font-extrabold uppercase text-slate-400">KAPASITAS TANK</span>
                </div>
                <h3 class="text-lg font-extrabold text-navy-900">42</h3>
                <span class="text-[9px] text-slate-500 font-medium block">Tank Aktif</span>
            </div>

        </div>

    </div>

    <!-- Active Hatchery Batches Section -->
    <div class="space-y-3 pt-1">
        <div class="flex items-center justify-between px-1">
            <h3 class="text-xs font-extrabold text-navy-900">Batch Hatchery Aktif</h3>
            <a href="{{ route('petugas.pembibitan.form') }}" class="text-[10px] font-extrabold text-sky-700 uppercase hover:underline tracking-wider">
                LIHAT SEMUA
            </a>
        </div>

        <!-- Cards List -->
        <div class="space-y-2.5">
            
            <!-- Batch Card 1 -->
            <a href="{{ route('petugas.pembibitan.log-pakan', ['batch' => 'Batch-H-042']) }}" 
               class="bg-white rounded-2xl border border-slate-200/90 p-3.5 shadow-xs flex items-center justify-between hover:shadow-md transition-all">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-slate-100 text-slate-600 flex items-center justify-center font-bold text-xs">
                        <i class="fa-solid fa-fish text-slate-500"></i>
                    </div>
                    <div>
                        <div class="flex items-center gap-2">
                            <h4 class="text-xs font-extrabold text-navy-900">Batch-H-042</h4>
                            <span class="px-2 py-0.2 rounded-full text-[9px] font-extrabold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                SEHAT
                            </span>
                        </div>
                        <p class="text-[11px] text-slate-500 font-medium mt-0.5">Pembibitan 4 / Nila Merah</p>
                    </div>
                </div>
                <i class="fa-solid fa-chevron-right text-slate-400 text-xs"></i>
            </a>

            <!-- Batch Card 2 -->
            <a href="{{ route('petugas.pembibitan.log-pakan', ['batch' => 'Batch-H-041']) }}" 
               class="bg-white rounded-2xl border border-slate-200/90 p-3.5 shadow-xs flex items-center justify-between hover:shadow-md transition-all">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-slate-100 text-slate-600 flex items-center justify-center font-bold text-xs">
                        <i class="fa-solid fa-circle-dot text-slate-500 text-xs"></i>
                    </div>
                    <div>
                        <div class="flex items-center gap-2">
                            <h4 class="text-xs font-extrabold text-navy-900">Batch-H-041</h4>
                            <span class="px-2 py-0.2 rounded-full text-[9px] font-extrabold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                SEHAT
                            </span>
                        </div>
                        <p class="text-[11px] text-slate-500 font-medium mt-0.5">Fase Penyerapan / Umur 28 Hari</p>
                    </div>
                </div>
                <i class="fa-solid fa-chevron-right text-slate-400 text-xs"></i>
            </a>

            <!-- Batch Card 3 -->
            <a href="{{ route('petugas.pembibitan.log-pakan', ['batch' => 'Batch-H-039']) }}" 
               class="bg-white rounded-2xl border border-slate-200/90 p-3.5 shadow-xs flex items-center justify-between hover:shadow-md transition-all">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-rose-50 text-rose-600 flex items-center justify-center font-bold text-xs">
                        <i class="fa-solid fa-triangle-exclamation text-rose-500 text-xs"></i>
                    </div>
                    <div>
                        <div class="flex items-center gap-2">
                            <h4 class="text-xs font-extrabold text-navy-900">Batch-H-039</h4>
                            <span class="px-2 py-0.2 rounded-full text-[9px] font-extrabold bg-rose-50 text-rose-700 border border-rose-200">
                                WASPADA
                            </span>
                        </div>
                        <p class="text-[11px] text-slate-500 font-medium mt-0.5">Fase Menetas / pH + Suhu Tinggi</p>
                    </div>
                </div>
                <i class="fa-solid fa-chevron-right text-slate-400 text-xs"></i>
            </a>

        </div>
    </div>

</div>
@endsection

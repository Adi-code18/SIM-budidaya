@extends('mobile_web_petugas.petugas_pembesaran.layout')

@section('title', 'Dashboard Petugas Pembesaran - SIM-BUDIDAYA Mobile')

@section('content')
<div class="p-4 space-y-4">

    <!-- Top Greeting & Action Header (Matches Screen 1 Mockup) -->
    <div class="flex items-center justify-between pt-2 pb-1">
        <div>
            <span class="text-[9px] font-extrabold uppercase text-slate-400 tracking-widest block">DASHBOARD</span>
            <h1 class="text-xl font-extrabold text-navy-900">Petugas Pembesaran</h1>
        </div>

        <a href="{{ route('petugas.pembesaran.log-pakan') }}" 
           class="px-4 py-2 rounded-xl bg-navy-800 hover:bg-navy-900 active:scale-[0.98] text-white font-extrabold text-xs flex items-center gap-1.5 shadow-sm transition-all">
            <i class="fa-solid fa-plus text-xs"></i>
            <span>Pakan Baru</span>
        </a>
    </div>

    <!-- Total Biomassa Big Navy Banner Card -->
    <div class="bg-gradient-to-r from-navy-900 via-navy-800 to-navy-700 rounded-3xl p-5 text-white shadow-md relative overflow-hidden flex items-center justify-between">
        <div class="space-y-1 relative z-10">
            <span class="text-[10px] font-extrabold uppercase tracking-widest text-sky-300">TOTAL BIOMASSA</span>
            <h2 class="text-3xl font-extrabold text-white tracking-tight">125.4 <span class="text-lg font-bold text-sky-200">Ton</span></h2>
        </div>
        <div class="w-12 h-12 rounded-2xl bg-white/10 border border-white/20 flex items-center justify-center text-sky-300 text-xl backdrop-blur-md">
            <i class="fa-solid fa-fish-fins"></i>
        </div>
    </div>

    <!-- Rerata FCR & Kualitas Metric Grid Cards -->
    <div class="grid grid-cols-2 gap-3">
        
        <!-- Metric 1: Rerata FCR -->
        <div class="bg-white rounded-2xl p-4 border border-slate-200/90 shadow-xs space-y-1">
            <span class="text-[9px] font-extrabold uppercase text-slate-400 block tracking-wider">RERATA FCR</span>
            <div class="flex items-center gap-2">
                <h3 class="text-2xl font-extrabold text-navy-900">1.24</h3>
                <span class="w-5 h-5 rounded-full bg-emerald-100 text-emerald-700 flex items-center justify-center text-[10px]">
                    <i class="fa-solid fa-check"></i>
                </span>
            </div>
        </div>

        <!-- Metric 2: Kuesioner Kualitas -->
        <div class="bg-white rounded-2xl p-4 border border-slate-200/90 shadow-xs space-y-1">
            <span class="text-[9px] font-extrabold uppercase text-slate-400 block tracking-wider">KONDISI AIR & PAKAN</span>
            <div class="pt-0.5">
                <span class="px-2.5 py-1 rounded-full text-[10px] font-extrabold bg-emerald-50 text-emerald-700 border border-emerald-200 inline-flex items-center gap-1">
                    <i class="fa-solid fa-circle-check text-[9px]"></i>
                    Bagus ✓
                </span>
            </div>
        </div>

    </div>

    <!-- Status Kolam Pembesaran List Section -->
    <div class="space-y-3 pt-1">
        <div class="flex items-center justify-between px-1">
            <h3 class="text-xs font-extrabold text-navy-900">Status Kolam Pembesaran</h3>
            <a href="{{ route('petugas.pembesaran.create-batch') }}" class="text-[10px] font-extrabold text-sky-700 uppercase hover:underline tracking-wider">
                + BATCH BARU
            </a>
        </div>

        <div class="space-y-2.5">
            
            <!-- Kolam Card 1: Kolam A-01 -->
            <a href="{{ route('petugas.pembesaran.log-pakan', ['kolam' => 'Kolam A-01']) }}" 
               class="bg-white rounded-2xl border border-slate-200/90 p-4 shadow-xs space-y-3 block hover:shadow-md transition-all">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <span class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span>
                        <h4 class="text-xs font-extrabold text-navy-900">Kolam A-01</h4>
                    </div>
                    <span class="px-2.5 py-0.5 rounded-full text-[9px] font-extrabold bg-emerald-50 text-emerald-700 border border-emerald-200">
                        OPTIMAL
                    </span>
                </div>

                <div class="grid grid-cols-3 gap-2 bg-slate-50 p-2.5 rounded-xl border border-slate-100 text-center">
                    <div>
                        <span class="text-[9px] font-bold text-slate-400 uppercase block">BIOMASSA</span>
                        <span class="text-xs font-extrabold text-navy-900">4.2 Ton</span>
                    </div>
                    <div>
                        <span class="text-[9px] font-bold text-slate-400 uppercase block">SIKLUS</span>
                        <span class="text-xs font-extrabold text-navy-900">DOC 85</span>
                    </div>
                    <div>
                        <span class="text-[9px] font-bold text-slate-400 uppercase block">FCR</span>
                        <span class="text-xs font-extrabold text-emerald-700">1.18</span>
                    </div>
                </div>
            </a>

            <!-- Kolam Card 2: Kolam B-03 -->
            <a href="{{ route('petugas.pembesaran.log-pakan', ['kolam' => 'Kolam B-03']) }}" 
               class="bg-white rounded-2xl border border-slate-200/90 p-4 shadow-xs space-y-3 block hover:shadow-md transition-all">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <span class="w-2.5 h-2.5 rounded-full bg-amber-500 animate-pulse"></span>
                        <h4 class="text-xs font-extrabold text-navy-900">Kolam B-03</h4>
                    </div>
                    <span class="px-2.5 py-0.5 rounded-full text-[9px] font-extrabold bg-amber-50 text-amber-700 border border-amber-200">
                        FAS. PANEN
                    </span>
                </div>

                <div class="grid grid-cols-3 gap-2 bg-slate-50 p-2.5 rounded-xl border border-slate-100 text-center">
                    <div>
                        <span class="text-[9px] font-bold text-slate-400 uppercase block">BIOMASSA</span>
                        <span class="text-xs font-extrabold text-navy-900">3.8 Ton</span>
                    </div>
                    <div>
                        <span class="text-[9px] font-bold text-slate-400 uppercase block">SIKLUS</span>
                        <span class="text-xs font-extrabold text-navy-900">DOC 120</span>
                    </div>
                    <div>
                        <span class="text-[9px] font-bold text-slate-400 uppercase block">FCR</span>
                        <span class="text-xs font-extrabold text-amber-700">1.45</span>
                    </div>
                </div>
            </a>

            <!-- Kolam Card 3: Kolam C-02 -->
            <a href="{{ route('petugas.pembesaran.log-pakan', ['kolam' => 'Kolam C-02']) }}" 
               class="bg-white rounded-2xl border border-slate-200/90 p-4 shadow-xs space-y-3 block hover:shadow-md transition-all">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <span class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span>
                        <h4 class="text-xs font-extrabold text-navy-900">Kolam C-02</h4>
                    </div>
                    <span class="px-2.5 py-0.5 rounded-full text-[9px] font-extrabold bg-emerald-50 text-emerald-700 border border-emerald-200">
                        OPTIMAL
                    </span>
                </div>

                <div class="grid grid-cols-3 gap-2 bg-slate-50 p-2.5 rounded-xl border border-slate-100 text-center">
                    <div>
                        <span class="text-[9px] font-bold text-slate-400 uppercase block">BIOMASSA</span>
                        <span class="text-xs font-extrabold text-navy-900">5.1 Ton</span>
                    </div>
                    <div>
                        <span class="text-[9px] font-bold text-slate-400 uppercase block">SIKLUS</span>
                        <span class="text-xs font-extrabold text-navy-900">DOC 105</span>
                    </div>
                    <div>
                        <span class="text-[9px] font-bold text-slate-400 uppercase block">FCR</span>
                        <span class="text-xs font-extrabold text-emerald-700">1.21</span>
                    </div>
                </div>
            </a>

        </div>
    </div>

</div>
@endsection

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
    @php
        $totalBiomassaKg = isset($batches) ? $batches->sum('biomassa_est') : 0;
    @endphp
    <div class="bg-gradient-to-r from-navy-900 via-navy-800 to-navy-700 rounded-3xl p-5 text-white shadow-md relative overflow-hidden flex items-center justify-between">
        <div class="space-y-1 relative z-10">
            <span class="text-[10px] font-extrabold uppercase tracking-widest text-sky-300">TOTAL BIOMASSA</span>
            <h2 class="text-3xl font-extrabold text-white tracking-tight">
                @if($totalBiomassaKg >= 1000)
                    {{ number_format($totalBiomassaKg / 1000, 1) }} <span class="text-lg font-bold text-sky-200">Ton</span>
                @else
                    {{ number_format($totalBiomassaKg, 0) }} <span class="text-lg font-bold text-sky-200">kg</span>
                @endif
            </h2>
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
                <h3 class="text-2xl font-extrabold text-navy-900">{{ number_format($avgFcr, 2) }}</h3>
                <span class="w-5 h-5 rounded-full {{ $avgFcr <= 1.25 ? 'bg-emerald-100 text-emerald-700' : 'bg-amber-100 text-amber-700' }} flex items-center justify-center text-[10px]">
                    <i class="fa-solid {{ $avgFcr <= 1.25 ? 'fa-check' : 'fa-triangle-exclamation' }}"></i>
                </span>
            </div>
            <span class="text-[9px] {{ $avgFcr <= 1.25 ? 'text-emerald-600' : 'text-amber-600' }} font-bold block">{{ $avgFcr <= 1.25 ? 'Efisiensi Optimal' : 'Perlu Evaluasi' }}</span>
        </div>

        <!-- Metric 2: Kualitas Air pH -->
        <div class="bg-white rounded-2xl p-4 border border-slate-200/90 shadow-xs space-y-1">
            <span class="text-[9px] font-extrabold uppercase text-slate-400 block tracking-wider">KUALITAS AIR (pH)</span>
            <div class="flex items-center gap-2">
                <h3 class="text-2xl font-extrabold text-navy-900">{{ ($avgPh ?? 0) > 0 ? number_format($avgPh, 1) : '-' }}</h3>
                <span class="w-5 h-5 rounded-full bg-emerald-100 text-emerald-700 flex items-center justify-center text-[10px]">
                    <i class="fa-solid fa-droplet text-sky-600"></i>
                </span>
            </div>
            <span class="text-[9px] {{ ($avgPh ?? 0) >= 6.5 && ($avgPh ?? 0) <= 8.5 ? 'text-emerald-600' : 'text-slate-400' }} font-bold block">{{ ($avgPh ?? 0) > 0 ? (($avgPh >= 6.5 && $avgPh <= 8.5) ? 'Parameter Normal ✓' : 'Perlu Perhatian') : 'Belum Ada Data' }}</span>
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
            @if(isset($batches) && count($batches) > 0)
                @foreach($batches as $b)
                    @php
                        $kolamName = $b->kolam ? $b->kolam->nama_kolam : 'Kolam #' . $b->id_kolam;
                        $doc = $b->tgl_tebar ? (int) abs(\Carbon\Carbon::parse($b->tgl_tebar)->startOfDay()->diffInDays(now()->startOfDay())) : 0;
                        $isPanen = $b->status_siklus === 'selesai' || $b->status_siklus === 'siap_panen';
                    @endphp
                    <a href="{{ route('petugas.pembesaran.log-pakan', ['kolam' => $kolamName]) }}" 
                       class="bg-white rounded-2xl border border-slate-200/90 p-4 shadow-xs space-y-3 block hover:shadow-md transition-all">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <span class="w-2.5 h-2.5 rounded-full {{ $isPanen ? 'bg-amber-500 animate-pulse' : 'bg-emerald-500' }}"></span>
                                <h4 class="text-xs font-extrabold text-navy-900">{{ $kolamName }}</h4>
                            </div>
                            <span class="px-2.5 py-0.5 rounded-full text-[9px] font-extrabold {{ $isPanen ? 'bg-amber-50 text-amber-700 border border-amber-200' : 'bg-emerald-50 text-emerald-700 border border-emerald-200' }}">
                                {{ $isPanen ? 'FAS. PANEN' : 'OPTIMAL' }}
                            </span>
                        </div>

                        <div class="grid grid-cols-3 gap-2 bg-slate-50 p-2.5 rounded-xl border border-slate-100 text-center">
                            <div>
                                <span class="text-[9px] font-bold text-slate-400 uppercase block">BIOMASSA</span>
                                <span class="text-xs font-extrabold text-navy-900">
                                    @if($b->biomassa_est >= 1000)
                                        {{ number_format($b->biomassa_est / 1000, 1) }} Ton
                                    @else
                                        {{ number_format($b->biomassa_est, 0) }} kg
                                    @endif
                                </span>
                            </div>
                            <div>
                                <span class="text-[9px] font-bold text-slate-400 uppercase block">SIKLUS</span>
                                <span class="text-xs font-extrabold text-navy-900">DOC {{ $doc }}</span>
                            </div>
                            <div>
                                <span class="text-[9px] font-bold text-slate-400 uppercase block">FCR</span>
                                <span class="text-xs font-extrabold {{ ($b->fcr ?? 1.2) <= 1.25 ? 'text-emerald-700' : 'text-amber-700' }}">{{ number_format($b->fcr ?? 1.15, 2) }}</span>
                            </div>
                        </div>
                    </a>
                @endforeach
            @else
                <!-- Fallback Kolam Card 1: Kolam A-01 -->
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
            @endif
        </div>
    </div>

</div>
@endsection

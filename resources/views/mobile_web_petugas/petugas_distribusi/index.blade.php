@extends('mobile_web_petugas.petugas_distribusi.layout')

@section('title', 'Logistik Pengiriman - SIM-BUDIDAYA Mobile')

@section('content')
<div class="p-4 space-y-4" x-data="{ activeFilter: 'semua' }">

    <!-- Header Greeting Card -->
    <div class="bg-gradient-to-r from-navy-800 to-navy-900 rounded-3xl p-5 text-white shadow-md relative overflow-hidden">
        <div class="absolute -right-6 -bottom-6 w-28 h-28 bg-sky-500/10 rounded-full blur-xl"></div>
        
        <div class="flex items-center justify-between relative z-10">
            <div>
                <span class="text-[10px] font-extrabold tracking-widest text-sky-300 uppercase block mb-1">LOGISTIK & PENGIRIMAN</span>
                <h1 class="text-xl font-extrabold tracking-tight text-white">Daftar Pengiriman</h1>
                <p class="text-xs text-slate-300 mt-1 font-medium">Pengiriman oleh <span class="font-bold text-sky-200">Adit Adi Septian</span> hari ini.</p>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-white/10 border border-white/20 flex items-center justify-center text-white backdrop-blur-md">
                <i class="fa-solid fa-truck-ramp-box text-xl text-sky-300"></i>
            </div>
        </div>

        <!-- Quick Metrics Pill -->
        <div class="grid grid-cols-3 gap-2 mt-4 pt-3 border-t border-white/10 text-center">
            <div class="bg-white/10 rounded-xl py-2 px-1 backdrop-blur-xs">
                <span class="text-[10px] text-slate-300 block">Total Tasks</span>
                <span class="text-sm font-extrabold text-white">3 Order</span>
            </div>
            <div class="bg-amber-500/20 border border-amber-500/30 rounded-xl py-2 px-1 backdrop-blur-xs">
                <span class="text-[10px] text-amber-200 block">Aktif</span>
                <span class="text-sm font-extrabold text-amber-300">1 Jalan</span>
            </div>
            <div class="bg-emerald-500/20 border border-emerald-500/30 rounded-xl py-2 px-1 backdrop-blur-xs">
                <span class="text-[10px] text-emerald-200 block">Selesai</span>
                <span class="text-sm font-extrabold text-emerald-300">2 Order</span>
            </div>
        </div>
    </div>

    <!-- Category Filter Tabs -->
    <div class="flex items-center gap-2 overflow-x-auto pb-1 no-scrollbar">
        <button @click="activeFilter = 'semua'"
                :class="activeFilter === 'semua' ? 'bg-navy-800 text-white shadow-sm font-bold' : 'bg-white text-slate-600 border border-slate-200 font-semibold'"
                class="px-4 py-2 rounded-xl text-xs whitespace-nowrap transition-all">
            Semua (3)
        </button>
        <button @click="activeFilter = 'dalam_pengiriman'"
                :class="activeFilter === 'dalam_pengiriman' ? 'bg-navy-800 text-white shadow-sm font-bold' : 'bg-white text-slate-600 border border-slate-200 font-semibold'"
                class="px-4 py-2 rounded-xl text-xs whitespace-nowrap transition-all flex items-center gap-1.5">
            <span class="w-2 h-2 rounded-full bg-amber-500"></span>
            Dalam Pengiriman (1)
        </button>
        <button @click="activeFilter = 'siap_kirim'"
                :class="activeFilter === 'siap_kirim' ? 'bg-navy-800 text-white shadow-sm font-bold' : 'bg-white text-slate-600 border border-slate-200 font-semibold'"
                class="px-4 py-2 rounded-xl text-xs whitespace-nowrap transition-all flex items-center gap-1.5">
            <span class="w-2 h-2 rounded-full bg-sky-500"></span>
            Siap Kirim (2)
        </button>
    </div>

    <!-- Delivery Task Cards List -->
    <div class="space-y-3.5">

        <!-- Card 1: Dalam Pengiriman -->
        <div x-show="activeFilter === 'semua' || activeFilter === 'dalam_pengiriman'"
             class="bg-white rounded-2xl border border-slate-200/90 p-4 shadow-xs space-y-3.5 hover:shadow-md transition-all">
            
            <div class="flex items-center justify-between">
                <div>
                    <span class="text-[10px] font-extrabold uppercase text-slate-400 tracking-wider">ID PENGIRIMAN</span>
                    <h3 class="text-sm font-extrabold text-navy-900">#ORD-9924A</h3>
                </div>
                <span class="px-3 py-1 rounded-full text-[10px] font-extrabold bg-amber-50 text-amber-700 border border-amber-200 flex items-center gap-1.5">
                    <span class="w-2 h-2 rounded-full bg-amber-500 animate-ping"></span>
                    DALAM PENGIRIMAN
                </span>
            </div>

            <!-- Destination Info Box -->
            <div class="bg-slate-50 rounded-xl p-3 border border-slate-100 space-y-2">
                <div class="flex items-start gap-2.5">
                    <div class="w-7 h-7 rounded-lg bg-navy-800/10 text-navy-800 flex items-center justify-center shrink-0 mt-0.5">
                        <i class="fa-solid fa-store text-xs"></i>
                    </div>
                    <div>
                        <h4 class="text-xs font-bold text-slate-900">Resto Kurama Jaya</h4>
                        <p class="text-[11px] text-slate-500 font-medium leading-snug">Jl. Merdeka No. 45, Kecamatan Sidomulyo, Kota Mataram</p>
                    </div>
                </div>

                <div class="flex items-center justify-between pt-2 border-t border-slate-200/60 text-[11px]">
                    <span class="text-slate-500 font-medium"><i class="fa-regular fa-clock text-sky-600 mr-1"></i>Estimasi Tiba: <strong class="text-slate-800">14:30 WIB</strong></span>
                    <span class="font-extrabold text-navy-800 bg-sky-50 px-2 py-0.5 rounded-lg border border-sky-100">350 KG</span>
                </div>
            </div>

            <!-- Action Button -->
            <a href="{{ route('mobile.petugas.detail', ['id' => 'ORD-9924A']) }}" 
               class="w-full py-2.5 rounded-xl bg-navy-800 hover:bg-navy-900 active:scale-[0.99] text-white font-bold text-xs flex items-center justify-center gap-2 shadow-sm transition-all">
                <span>Detail & Navigasi Pengiriman</span>
                <i class="fa-solid fa-arrow-right text-xs"></i>
            </a>
        </div>

        <!-- Card 2: Siap Kirim -->
        <div x-show="activeFilter === 'semua' || activeFilter === 'siap_kirim'"
             class="bg-white rounded-2xl border border-slate-200/90 p-4 shadow-xs space-y-3.5 hover:shadow-md transition-all">
            
            <div class="flex items-center justify-between">
                <div>
                    <span class="text-[10px] font-extrabold uppercase text-slate-400 tracking-wider">ID PENGIRIMAN</span>
                    <h3 class="text-sm font-extrabold text-navy-900">#ORD-8892B</h3>
                </div>
                <span class="px-3 py-1 rounded-full text-[10px] font-extrabold bg-sky-50 text-sky-700 border border-sky-200 flex items-center gap-1">
                    <i class="fa-solid fa-box text-[9px]"></i>
                    SIAP KIRIM
                </span>
            </div>

            <!-- Destination Info Box -->
            <div class="bg-slate-50 rounded-xl p-3 border border-slate-100 space-y-2">
                <div class="flex items-start gap-2.5">
                    <div class="w-7 h-7 rounded-lg bg-sky-100 text-sky-700 flex items-center justify-center shrink-0 mt-0.5">
                        <i class="fa-solid fa-building-wheat text-xs"></i>
                    </div>
                    <div>
                        <h4 class="text-xs font-bold text-slate-900">Pasar Induk Ikan</h4>
                        <p class="text-[11px] text-slate-500 font-medium leading-snug">Stand B12, Jl. Lintas Timur No. 12, Mataram</p>
                    </div>
                </div>

                <div class="flex items-center justify-between pt-2 border-t border-slate-200/60 text-[11px]">
                    <span class="text-slate-500 font-medium"><i class="fa-regular fa-clock text-sky-600 mr-1"></i>Jadwal Muat: <strong class="text-slate-800">15:00 WIB</strong></span>
                    <span class="font-extrabold text-navy-800 bg-sky-50 px-2 py-0.5 rounded-lg border border-sky-100">500 KG</span>
                </div>
            </div>

            <!-- Action Button -->
            <a href="{{ route('mobile.petugas.detail', ['id' => 'ORD-8892B']) }}" 
               class="w-full py-2.5 rounded-xl border border-navy-800 text-navy-800 hover:bg-navy-50 active:scale-[0.99] font-bold text-xs flex items-center justify-center gap-2 transition-all">
                <i class="fa-solid fa-truck-moving text-xs"></i>
                <span>Mulai Pengiriman</span>
            </a>
        </div>

        <!-- Card 3: Siap Kirim 2 -->
        <div x-show="activeFilter === 'semua' || activeFilter === 'siap_kirim'"
             class="bg-white rounded-2xl border border-slate-200/90 p-4 shadow-xs space-y-3.5 hover:shadow-md transition-all">
            
            <div class="flex items-center justify-between">
                <div>
                    <span class="text-[10px] font-extrabold uppercase text-slate-400 tracking-wider">ID PENGIRIMAN</span>
                    <h3 class="text-sm font-extrabold text-navy-900">#ORD-3104C</h3>
                </div>
                <span class="px-3 py-1 rounded-full text-[10px] font-extrabold bg-sky-50 text-sky-700 border border-sky-200 flex items-center gap-1">
                    <i class="fa-solid fa-box text-[9px]"></i>
                    SIAP KIRIM
                </span>
            </div>

            <!-- Destination Info Box -->
            <div class="bg-slate-50 rounded-xl p-3 border border-slate-100 space-y-2">
                <div class="flex items-start gap-2.5">
                    <div class="w-7 h-7 rounded-lg bg-sky-100 text-sky-700 flex items-center justify-center shrink-0 mt-0.5">
                        <i class="fa-solid fa-utensils text-xs"></i>
                    </div>
                    <div>
                        <h4 class="text-xs font-bold text-slate-900">Depot Fresh Fish</h4>
                        <p class="text-[11px] text-slate-500 font-medium leading-snug">Jl. Ahmad Yani No. 88, Ampenan, Mataram</p>
                    </div>
                </div>

                <div class="flex items-center justify-between pt-2 border-t border-slate-200/60 text-[11px]">
                    <span class="text-slate-500 font-medium"><i class="fa-regular fa-clock text-sky-600 mr-1"></i>Jadwal Muat: <strong class="text-slate-800">16:15 WIB</strong></span>
                    <span class="font-extrabold text-navy-800 bg-sky-50 px-2 py-0.5 rounded-lg border border-sky-100">200 KG</span>
                </div>
            </div>

            <!-- Action Button -->
            <a href="{{ route('mobile.petugas.detail', ['id' => 'ORD-3104C']) }}" 
               class="w-full py-2.5 rounded-xl border border-navy-800 text-navy-800 hover:bg-navy-50 active:scale-[0.99] font-bold text-xs flex items-center justify-center gap-2 transition-all">
                <i class="fa-solid fa-truck-moving text-xs"></i>
                <span>Mulai Pengiriman</span>
            </a>
        </div>

    </div>

</div>
@endsection

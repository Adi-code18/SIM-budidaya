@extends('mobile_web_petugas.petugas_distribusi.layout')

@section('title', 'SIM-BUDIDAYA Mobile')
@section('hide_header', true)
@section('hide_nav', true)

@section('content')
<div class="min-h-full flex flex-col items-center justify-between p-8 bg-gradient-to-b from-navy-800 via-navy-900 to-navy-950 text-white relative overflow-hidden"
     x-data="{ 
         init() {
             setTimeout(() => {
                 window.location.href = '{{ route('mobile.petugas.login') }}';
             }, 2500);
         }
     }">

    <!-- Background glowing graphics -->
    <div class="absolute -top-16 -left-16 w-64 h-64 bg-sky-500/10 rounded-full blur-3xl"></div>
    <div class="absolute -bottom-16 -right-16 w-64 h-64 bg-blue-600/10 rounded-full blur-3xl"></div>

    <div class="my-auto flex flex-col items-center text-center space-y-6 relative z-10">
        
        <!-- White Logo Box Container (Matches Screen 1 Mockup) -->
        <div class="w-36 h-36 rounded-3xl bg-white p-4 shadow-2xl flex flex-col items-center justify-center space-y-2 border border-white/20 transform transition-transform hover:scale-105">
            <div class="w-16 h-16 rounded-2xl bg-navy-800 text-sky-400 flex items-center justify-center text-3xl shadow-inner">
                <i class="fa-solid fa-fish-fins"></i>
            </div>
            <span class="text-xs font-black tracking-tighter text-navy-900 uppercase">SIM-BUDIDAYA</span>
        </div>

        <div class="space-y-1">
            <h1 class="text-2xl font-extrabold text-white tracking-wide">SIM-BUDIDAYA</h1>
            <p class="text-[10px] tracking-[0.25em] font-extrabold text-sky-400 uppercase">Mobile Petugas Distribusi</p>
        </div>

        <!-- Spinner -->
        <div class="pt-4">
            <div class="w-6 h-6 border-2 border-white/20 border-t-sky-400 rounded-full animate-spin mx-auto"></div>
        </div>

    </div>

    <!-- Bottom Click Alternative -->
    <div class="relative z-10 text-center pb-4">
        <a href="{{ route('mobile.petugas.login') }}" class="text-xs text-sky-300 font-bold hover:underline">
            Ketuk untuk melanjutkan &rarr;
        </a>
    </div>

</div>
@endsection

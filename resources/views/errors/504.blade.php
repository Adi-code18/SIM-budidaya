@extends('errors.layout')

@section('title', '504 - Batas Waktu Server Habis | Aquafarm Priangan')
@section('code', '504')
@section('code_name', 'Gateway Timeout')
@section('status_badge_style', 'bg-slate-500/20 text-slate-300 border-slate-400/40')
@section('status_icon', 'fa-solid fa-clock-rotate-left')
@section('status_label', 'SERVER TIMEOUT')

@section('heading', 'Batas Waktu Respon Server Habis (504)')

@section('description')
    {{ $message ?? 'Server gateway tidak menerima tanggapan tepat waktu dari server database atau microservice IoT tambak. Permintaan Anda membutuhkan waktu komputasi yang melebihi batas toleransi sistem.' }}
@endsection

@section('troubleshooting')
    <li class="flex items-start gap-2">
        <i class="fa-solid fa-circle-check text-slate-300 text-[11px] mt-1 shrink-0"></i>
        <span>Jika Anda sedang mengekspor laporan besar (misal rekapan PDF/Excel panjang), persempit rentang tanggal.</span>
    </li>
    <li class="flex items-start gap-2">
        <i class="fa-solid fa-circle-check text-slate-300 text-[11px] mt-1 shrink-0"></i>
        <span>Coba segarkan halaman atau ulangi permintaan saat lalu lintas jaringan lebih longgar.</span>
    </li>
    <li class="flex items-start gap-2">
        <i class="fa-solid fa-circle-check text-slate-300 text-[11px] mt-1 shrink-0"></i>
        <span>Hubungi tim teknis jika ekspor data atau sinkronisasi kolam terus mengalami batas waktu.</span>
    </li>
@endsection

@section('illustration')
    <svg class="w-full h-full max-w-[280px] max-h-[280px]" viewBox="0 0 240 240" fill="none" xmlns="http://www.w3.org/2000/svg">
        <!-- Outer Clock Dial -->
        <circle cx="120" cy="120" r="75" fill="#0f172a" stroke="#64748b" stroke-width="3" />
        <circle cx="120" cy="120" r="62" stroke="#475569" stroke-width="1.5" stroke-dasharray="6 6" />

        <!-- Hour Ticks -->
        <line x1="120" y1="52" x2="120" y2="60" stroke="#94a3b8" stroke-width="2.5" />
        <line x1="120" y1="180" x2="120" y2="188" stroke="#94a3b8" stroke-width="2.5" />
        <line x1="52" y1="120" x2="60" y2="120" stroke="#94a3b8" stroke-width="2.5" />
        <line x1="180" y1="120" x2="188" y2="120" stroke="#94a3b8" stroke-width="2.5" />

        <!-- Center Pivot -->
        <circle cx="120" cy="120" r="7" fill="#f59e0b" />

        <!-- Minute & Second Hands (Frozen / Spinning) -->
        <line x1="120" y1="120" x2="120" y2="70" stroke="#f1f5f9" stroke-width="3.5" stroke-linecap="round" />
        <line x1="120" y1="120" x2="155" y2="135" stroke="#ef4444" stroke-width="2" stroke-linecap="round" />
        <circle cx="155" cy="135" r="3" fill="#ef4444" />

        <!-- Timeout Arc Red Overlay -->
        <path d="M120 45 A75 75 0 0 1 185 158" stroke="#ef4444" stroke-width="3" stroke-linecap="round" fill="none" />

        <!-- Cloud Waiting Indicator -->
        <g transform="translate(60, 40)">
            <ellipse cx="20" cy="15" rx="14" ry="10" fill="#334155" />
            <ellipse cx="30" cy="12" rx="12" ry="9" fill="#334155" />
            <!-- Rain/Data drops timeout -->
            <line x1="15" y1="28" x2="12" y2="35" stroke="#64748b" stroke-width="1.5" stroke-dasharray="2 2" />
            <line x1="28" y1="28" x2="25" y2="35" stroke="#64748b" stroke-width="1.5" stroke-dasharray="2 2" />
        </g>

        <!-- Pulsing Radio Waves from Center -->
        <circle cx="120" cy="120" r="88" stroke="#94a3b8" stroke-width="1" stroke-dasharray="3 4" opacity="0.3" />
    </svg>
@endsection


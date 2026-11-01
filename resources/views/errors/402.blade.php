@extends('errors.layout')

@section('title', '402 - Pembayaran Diperlukan | Aquafarm Priangan')
@section('code', '402')
@section('code_name', 'Payment Required')
@section('status_badge_style', 'bg-emerald-500/20 text-emerald-300 border-emerald-400/40')
@section('status_icon', 'fa-solid fa-credit-card')
@section('status_label', 'SUBSCRIPTION & BILLING')

@section('heading', 'Pembayaran Diperlukan (Payment Required)')

@section('description')
    {{ $message ?? 'Akses ke modul analitik canggih, sinkronisasi IoT multi-kolam, atau laporan eksekutif ini membutuhkan langganan aktif atau pelunasan tagihan lisensi budidaya.' }}
@endsection

@section('troubleshooting')
    <li class="flex items-start gap-2">
        <i class="fa-solid fa-circle-check text-emerald-400 text-[11px] mt-1 shrink-0"></i>
        <span>Periksa status tagihan langganan AMS Budidaya pada menu Keuangan & Lisensi.</span>
    </li>
    <li class="flex items-start gap-2">
        <i class="fa-solid fa-circle-check text-emerald-400 text-[11px] mt-1 shrink-0"></i>
        <span>Lakukan konfirmasi pembayaran dengan mengunggah bukti transfer resmi.</span>
    </li>
    <li class="flex items-start gap-2">
        <i class="fa-solid fa-circle-check text-emerald-400 text-[11px] mt-1 shrink-0"></i>
        <span>Hubungi tim Administrasi & Keuangan Aquafarm Priangan untuk reaktivasi instan.</span>
    </li>
@endsection

@section('illustration')
    <svg class="w-full h-full max-w-[280px] max-h-[280px]" viewBox="0 0 240 240" fill="none" xmlns="http://www.w3.org/2000/svg">
        <!-- Safe / Vault Base -->
        <rect x="50" y="55" width="140" height="130" rx="20" fill="#06122c" stroke="#10b981" stroke-width="2.5" />
        
        <!-- Vault Door Ring -->
        <circle cx="120" cy="120" r="42" fill="#042f2e" stroke="#34d399" stroke-width="2" />
        <circle cx="120" cy="120" r="32" stroke="#10b981" stroke-width="1.5" stroke-dasharray="6 4" />

        <!-- Floating Golden Coin with Aquafarm Emblem -->
        <g transform="translate(120, 120)">
            <circle cx="0" cy="0" r="22" fill="#f59e0b" stroke="#fbbf24" stroke-width="2" />
            <circle cx="0" cy="0" r="17" fill="none" stroke="#d97706" stroke-width="1.5" stroke-dasharray="2 2" />
            <text x="0" y="7" font-family="'Barlow', sans-serif" font-size="20" font-weight="900" fill="#78350f" text-anchor="middle">Rp</text>
        </g>

        <!-- Credit Card Outline -->
        <rect x="135" y="40" width="70" height="44" rx="8" fill="#134e4a" stroke="#6ee7b7" stroke-width="1.8" transform="rotate(12 135 40)" />
        <line x1="145" y1="58" x2="195" y2="70" stroke="#042f2e" stroke-width="6" stroke-linecap="round" />
        <circle cx="150" cy="74" r="3" fill="#fbbf24" />
        <circle cx="157" cy="75" r="3" fill="#f87171" opacity="0.8" />

        <!-- Micro Waves -->
        <path d="M65 165 C85 160 100 170 120 165 C140 160 155 170 175 165" stroke="#10b981" stroke-width="1.5" stroke-linecap="round" opacity="0.6" />
    </svg>
@endsection


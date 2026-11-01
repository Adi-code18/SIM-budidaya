@extends('errors.layout')

@section('title', '503 - Sistem Dalam Pemeliharaan | Aquafarm Priangan')
@section('code', '503')
@section('code_name', 'Service Unavailable')
@section('status_badge_style', 'bg-cyan-500/20 text-cyan-300 border-cyan-400/40')
@section('status_icon', 'fa-solid fa-wrench')
@section('status_label', 'SCHEDULED MAINTENANCE')

@section('heading', 'Sistem Sedang Dalam Pemeliharaan (503)')

@section('description')
    {{ $message ?? 'Platform Manajemen Budidaya Aquafarm Priangan saat ini sedang menjalani proses pemeliharaan rutin atau pembaruan infrastruktur server demi meningkatkan performa dan keandalan sensor tambak.' }}
@endsection

@section('troubleshooting')
    <li class="flex items-start gap-2">
        <i class="fa-solid fa-circle-check text-cyan-400 text-[11px] mt-1 shrink-0"></i>
        <span>Pekerjaan pemeliharaan biasanya berlangsung antara 15 hingga 30 menit.</span>
    </li>
    <li class="flex items-start gap-2">
        <i class="fa-solid fa-circle-check text-cyan-400 text-[11px] mt-1 shrink-0"></i>
        <span>Data siklus kolam, biomassa, dan transaksi keuangan Anda tersimpan dengan aman di database.</span>
    </li>
    <li class="flex items-start gap-2">
        <i class="fa-solid fa-circle-check text-cyan-400 text-[11px] mt-1 shrink-0"></i>
        <span>Untuk urusan pengiriman darurat panen atau logistik, silakan hubungi tim via WhatsApp.</span>
    </li>
@endsection

@section('illustration')
    <svg class="w-full h-full max-w-[280px] max-h-[280px]" viewBox="0 0 240 240" fill="none" xmlns="http://www.w3.org/2000/svg">
        <!-- Maintenance Gear Large -->
        <g transform="translate(105, 110)">
            <circle cx="0" cy="0" r="42" fill="#082f49" stroke="#0284c7" stroke-width="2.5" />
            <circle cx="0" cy="0" r="18" fill="#06122c" stroke="#38bdf8" stroke-width="2" />
            <!-- Gear Teeth -->
            @for ($i = 0; $i < 8; $i++)
                <rect x="-6" y="-52" width="12" height="12" rx="3" fill="#0284c7" transform="rotate({{ $i * 45 }})" />
            @endfor
        </g>

        <!-- Maintenance Gear Small Interlocking -->
        <g transform="translate(160, 65)">
            <circle cx="0" cy="0" r="26" fill="#0c4a6e" stroke="#38bdf8" stroke-width="2" />
            <circle cx="0" cy="0" r="10" fill="#06122c" stroke="#7dd3fc" stroke-width="1.5" />
            @for ($i = 0; $i < 6; $i++)
                <rect x="-4" y="-33" width="8" height="8" rx="2" fill="#38bdf8" transform="rotate({{ $i * 60 }})" />
            @endfor
        </g>

        <!-- Wrench & Screwdriver Tool Cross -->
        <g transform="translate(55, 150) rotate(-25)">
            <!-- Wrench -->
            <path d="M0 0 L0 50 A6 6 0 0 0 12 50 L12 0 Z" fill="#64748b" stroke="#94a3b8" stroke-width="1.5" />
            <circle cx="6" cy="-8" r="12" fill="none" stroke="#94a3b8" stroke-width="3" />
            <rect x="0" y="-18" width="12" height="10" fill="#06122c" />
        </g>

        <!-- Construction Warning Cone -->
        <g transform="translate(155, 150)">
            <polygon points="20,0 35,40 5,40" fill="#ea580c" stroke="#f97316" stroke-width="1.5" />
            <polygon points="17,12 23,12 28,26 12,26" fill="#ffffff" />
            <rect x="0" y="40" width="40" height="6" rx="2" fill="#c2410c" />
        </g>

        <!-- Progress Pulsing Dots -->
        <circle cx="90" cy="205" r="4" fill="#38bdf8" />
        <circle cx="110" cy="205" r="4" fill="#38bdf8" opacity="0.6" />
        <circle cx="130" cy="205" r="4" fill="#38bdf8" opacity="0.3" />
    </svg>
@endsection


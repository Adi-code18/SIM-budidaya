@extends('errors.layout')

@section('title', '502 - Gerbang Jaringan Bermasalah | Aquafarm Priangan')
@section('code', '502')
@section('code_name', 'Bad Gateway')
@section('status_badge_style', 'bg-orange-500/20 text-orange-300 border-orange-400/40')
@section('status_icon', 'fa-solid fa-network-wired')
@section('status_label', 'UPSTREAM GATEWAY ERROR')

@section('heading', 'Gerbang Jaringan Bermasalah (Bad Gateway)')

@section('description')
    {{ $message ?? 'Server perantara (reverse proxy atau gateway web) menerima respon yang tidak valid atau terputus dari klaster server aplikasi budidaya hulu saat memproses permintaan Anda.' }}
@endsection

@section('troubleshooting')
    <li class="flex items-start gap-2">
        <i class="fa-solid fa-circle-check text-orange-400 text-[11px] mt-1 shrink-0"></i>
        <span>Kondisi ini umumnya bersifat sementara; coba muat ulang halaman setelah 30 detik.</span>
    </li>
    <li class="flex items-start gap-2">
        <i class="fa-solid fa-circle-check text-orange-400 text-[11px] mt-1 shrink-0"></i>
        <span>Periksa apakah koneksi VPN atau firewall jaringan kantor Anda menghalangi akses gateway.</span>
    </li>
    <li class="flex items-start gap-2">
        <i class="fa-solid fa-circle-check text-orange-400 text-[11px] mt-1 shrink-0"></i>
        <span>Hubungi tim DevOps / Jaringan Aquafarm jika gangguan terus berlanjut.</span>
    </li>
@endsection

@section('illustration')
    <svg class="w-full h-full max-w-[280px] max-h-[280px]" viewBox="0 0 240 240" fill="none" xmlns="http://www.w3.org/2000/svg">
        <!-- Gateway Proxy Node (Left) -->
        <rect x="35" y="85" width="60" height="70" rx="12" fill="#0f172a" stroke="#0284c7" stroke-width="2.5" />
        <circle cx="55" cy="110" r="4" fill="#38bdf8" />
        <line x1="45" y1="130" x2="85" y2="130" stroke="#0284c7" stroke-width="2" stroke-linecap="round" />
        <text x="65" y="100" font-family="'JetBrains Mono', monospace" font-size="9" fill="#94a3b8" text-anchor="middle">EDGE</text>

        <!-- Upstream App Node (Right) -->
        <rect x="145" y="85" width="60" height="70" rx="12" fill="#0f172a" stroke="#ea580c" stroke-width="2.5" />
        <circle cx="165" cy="110" r="4" fill="#ef4444" />
        <line x1="155" y1="130" x2="195" y2="130" stroke="#ea580c" stroke-width="2" stroke-linecap="round" />
        <text x="175" y="100" font-family="'JetBrains Mono', monospace" font-size="9" fill="#94a3b8" text-anchor="middle">UPSTREAM</text>

        <!-- Disconnected Cable Bridge with Broken Sparks -->
        <path d="M95 120 H112" stroke="#38bdf8" stroke-width="3" stroke-linecap="round" />
        <path d="M128 120 H145" stroke="#ea580c" stroke-width="3" stroke-linecap="round" stroke-dasharray="2 3" />

        <!-- Disconnection Cross / Cut in Middle -->
        <circle cx="120" cy="120" r="14" fill="#450a0a" stroke="#ea580c" stroke-width="2" />
        <line x1="113" y1="113" x2="127" y2="127" stroke="#fb923c" stroke-width="2.5" stroke-linecap="round" />
        <line x1="127" y1="113" x2="113" y2="127" stroke="#fb923c" stroke-width="2.5" stroke-linecap="round" />

        <!-- Floating Cloud Indicator -->
        <path d="M95 50 C95 42 102 35 110 35 C115 35 120 38 123 42 C126 40 130 40 133 42 C138 43 142 48 142 54 H95 Z" fill="#1e293b" stroke="#64748b" stroke-width="1.5" />
        <line x1="120" y1="54" x2="120" y2="80" stroke="#64748b" stroke-width="1.5" stroke-dasharray="3 3" />
    </svg>
@endsection


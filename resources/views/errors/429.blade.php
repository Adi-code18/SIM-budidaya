@extends('errors.layout')

@section('title', '429 - Terlalu Banyak Permintaan | Aquafarm Priangan')
@section('code', '429')
@section('code_name', 'Too Many Requests')
@section('status_badge_style', 'bg-purple-500/20 text-purple-300 border-purple-400/40')
@section('status_icon', 'fa-solid fa-gauge-simple-high')
@section('status_label', 'TRAFFIC RATE LIMIT REACHED')

@section('heading', 'Terlalu Banyak Permintaan (Rate Limit)')

@section('description')
    {{ $message ?? 'Sistem mendeteksi lonjakan frekuensi permintaan yang melampaui ambang batas aman dalam rentang waktu singkat. Fitur proteksi anti-spam dan stabilitas node otomatis menahan sementara koneksi Anda.' }}
@endsection

@section('troubleshooting')
    <li class="flex items-start gap-2">
        <i class="fa-solid fa-circle-check text-purple-400 text-[11px] mt-1 shrink-0"></i>
        <span>Harap tunggu beberapa detik (15–60 detik) sebelum menekan tombol atau memuat ulang.</span>
    </li>
    <li class="flex items-start gap-2">
        <i class="fa-solid fa-circle-check text-purple-400 text-[11px] mt-1 shrink-0"></i>
        <span>Hindari menekan tombol submit / proses berkali-kali secara beruntun.</span>
    </li>
    <li class="flex items-start gap-2">
        <i class="fa-solid fa-circle-check text-purple-400 text-[11px] mt-1 shrink-0"></i>
        <span>Jika Anda menggunakan skrip otomasi atau integrasi API sensor, sesuaikan jeda throttle interval.</span>
    </li>
@endsection

@section('illustration')
    <svg class="w-full h-full max-w-[280px] max-h-[280px]" viewBox="0 0 240 240" fill="none" xmlns="http://www.w3.org/2000/svg">
        <!-- Circular Speedometer Outer Shell -->
        <path d="M50 160 A85 85 0 1 1 190 160" stroke="#7e22ce" stroke-width="6" stroke-linecap="round" fill="none" opacity="0.4" />
        <path d="M50 160 A85 85 0 1 1 190 160" stroke="#a855f7" stroke-width="4" stroke-dasharray="12 8" stroke-linecap="round" fill="none" />

        <!-- High Pressure Red Zone Sector -->
        <path d="M150 70 A85 85 0 0 1 190 160" stroke="#ef4444" stroke-width="8" stroke-linecap="round" fill="none" />

        <!-- Center Needle Pivot -->
        <circle cx="120" cy="140" r="14" fill="#3b0764" stroke="#c084fc" stroke-width="2.5" />
        
        <!-- Needle Pushed into Red Max Range -->
        <line x1="120" y1="140" x2="175" y2="85" stroke="#f43f5e" stroke-width="4" stroke-linecap="round" />
        <circle cx="175" cy="85" r="4" fill="#f43f5e" />

        <!-- Water Flow Pipe & Overflow Bubbles -->
        <rect x="95" y="175" width="50" height="30" rx="8" fill="#1e1b4b" stroke="#a855f7" stroke-width="2" />
        <line x1="105" y1="190" x2="135" y2="190" stroke="#c084fc" stroke-width="2" stroke-linecap="round" stroke-dasharray="3 3" />
        
        <!-- Bursting Traffic Sparks -->
        <circle cx="185" cy="75" r="3" fill="#facc15" />
        <circle cx="195" cy="95" r="2.5" fill="#facc15" />
        <circle cx="170" cy="65" r="2" fill="#facc15" />

        <!-- Ticking Meter Numbers -->
        <text x="120" y="125" font-family="'JetBrains Mono', monospace" font-size="11" font-weight="bold" fill="#c084fc" text-anchor="middle">MAX RPM</text>
    </svg>
@endsection


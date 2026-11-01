@extends('errors.layout')

@section('title', '404 - Halaman Tidak Ditemukan | Aquafarm Priangan')
@section('code', '404')
@section('code_name', 'Not Found')
@section('status_badge_style', 'bg-sky-500/20 text-sky-300 border-sky-400/40')
@section('status_icon', 'fa-solid fa-compass-drafting')
@section('status_label', 'RESOURCE NOT FOUND')

@section('heading', 'Halaman Tidak Ditemukan (Not Found)')

@section('description')
    {{ $message ?? 'Koordinat halaman, data kolam, atau batch budidaya yang Anda tuju tidak terdeteksi pada radar sistem. Alamat URL mungkin telah diperbarui, dipindahkan, atau telah dinonaktifkan.' }}
@endsection

@section('troubleshooting')
    <li class="flex items-start gap-2">
        <i class="fa-solid fa-circle-check text-sky-400 text-[11px] mt-1 shrink-0"></i>
        <span>Periksa ketepatan penulisan tautan (URL) pada bilah alamat peramban Anda.</span>
    </li>
    <li class="flex items-start gap-2">
        <i class="fa-solid fa-circle-check text-sky-400 text-[11px] mt-1 shrink-0"></i>
        <span>Data batch atau transaksi mungkin telah selesai atau diarsipkan oleh sistem.</span>
    </li>
    <li class="flex items-start gap-2">
        <i class="fa-solid fa-circle-check text-sky-400 text-[11px] mt-1 shrink-0"></i>
        <span>Gunakan menu navigasi utama atau kembali ke Beranda untuk mencari modul terkait.</span>
    </li>
@endsection

@section('illustration')
    <svg class="w-full h-full max-w-[280px] max-h-[280px]" viewBox="0 0 240 240" fill="none" xmlns="http://www.w3.org/2000/svg">
        <!-- Radar Circle Outer Rings -->
        <circle cx="120" cy="120" r="95" stroke="#005aab" stroke-width="2" stroke-opacity="0.3" />
        <circle cx="120" cy="120" r="70" stroke="#005aab" stroke-width="1.5" stroke-opacity="0.5" />
        <circle cx="120" cy="120" r="45" stroke="#38bdf8" stroke-width="1.5" stroke-opacity="0.7" />
        <circle cx="120" cy="120" r="20" stroke="#38bdf8" stroke-width="1.5" />

        <!-- Crosshair Coordinate Lines -->
        <line x1="25" y1="120" x2="215" y2="120" stroke="#005aab" stroke-width="1.5" stroke-dasharray="4 4" stroke-opacity="0.6" />
        <line x1="120" y1="25" x2="120" y2="215" stroke="#005aab" stroke-width="1.5" stroke-dasharray="4 4" stroke-opacity="0.6" />

        <!-- Radar Sweep Cone Gradient -->
        <path d="M120 120 L185 55 A92 92 0 0 0 120 28 Z" fill="url(#radarGradient)" />
        <defs>
            <linearGradient id="radarGradient" x1="120" y1="120" x2="185" y2="55" gradientUnits="userSpaceOnUse">
                <stop stop-color="#38bdf8" stop-opacity="0.45" />
                <stop offset="1" stop-color="#0284c7" stop-opacity="0.0" />
            </linearGradient>
        </defs>

        <!-- Swimming Aquatic Fish Silhouette (Dotted/Lost) -->
        <g transform="translate(68, 75) scale(0.9)">
            <!-- Fish Body -->
            <path d="M15 25 C30 10 60 15 75 25 C60 35 30 40 15 25 Z" fill="#38bdf8" fill-opacity="0.25" stroke="#38bdf8" stroke-width="2" stroke-dasharray="3 3" />
            <!-- Fish Tail -->
            <polygon points="15,25 0,15 5,25 0,35" fill="#38bdf8" fill-opacity="0.4" stroke="#38bdf8" stroke-width="1.5" />
            <!-- Eye Question Mark -->
            <circle cx="62" cy="23" r="2.5" fill="#FFC700" />
            <text x="80" y="20" font-family="'Plus Jakarta Sans', sans-serif" font-size="14" font-weight="900" fill="#FFC700">?</text>
        </g>

        <!-- Blinking Missing Target Marker -->
        <circle cx="160" cy="155" r="7" fill="#ef4444" fill-opacity="0.4" />
        <circle cx="160" cy="155" r="3" fill="#ef4444" />
        <circle cx="160" cy="155" r="14" stroke="#ef4444" stroke-width="1.5" stroke-dasharray="2 2" />

        <!-- Center Hydro Sensor Node -->
        <circle cx="120" cy="120" r="5" fill="#38bdf8" />
    </svg>
@endsection


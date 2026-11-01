@extends('errors.layout')

@section('title', '403 - Akses Ditolak | Aquafarm Priangan')
@section('code', '403')
@section('code_name', 'Forbidden')
@section('status_badge_style', 'bg-rose-500/20 text-rose-300 border-rose-400/40')
@section('status_icon', 'fa-solid fa-ban')
@section('status_label', 'ACCESS RESTRICTED')

@section('heading', 'Akses Ditolak (Forbidden)')

@section('description')
    {{ $message ?? 'Anda tidak memiliki hak otorisasi atau peran (role) yang sesuai untuk mengakses modul atau tindakan ini. Area ini dibatasi khusus untuk manajer budidaya atau tim kepatuhan sistem.' }}
@endsection

@section('troubleshooting')
    <li class="flex items-start gap-2">
        <i class="fa-solid fa-circle-check text-rose-400 text-[11px] mt-1 shrink-0"></i>
        <span>Pastikan Anda telah masuk dengan peran akun yang tepat (Manajer, Petugas Distribusi, Petugas Pembesaran, atau Pembibitan).</span>
    </li>
    <li class="flex items-start gap-2">
        <i class="fa-solid fa-circle-check text-rose-400 text-[11px] mt-1 shrink-0"></i>
        <span>Jika Anda memerlukan wewenang tambahan, ajukan permintaan izin akses ke Manajer Operasional.</span>
    </li>
    <li class="flex items-start gap-2">
        <i class="fa-solid fa-circle-check text-rose-400 text-[11px] mt-1 shrink-0"></i>
        <span>Gunakan menu navigasi resmi untuk berpindah ke area kerja yang sesuai dengan akun Anda.</span>
    </li>
@endsection

@section('illustration')
    <svg class="w-full h-full max-w-[280px] max-h-[280px]" viewBox="0 0 240 240" fill="none" xmlns="http://www.w3.org/2000/svg">
        <!-- Octagonal Security Gate Warning -->
        <polygon points="120,20 190,45 220,115 190,185 120,210 50,185 20,115 50,45" fill="#1c1917" stroke="#e11d48" stroke-width="2.5" />
        <polygon points="120,35 178,56 202,115 178,174 120,195 62,174 38,115 62,56" fill="#881337" fill-opacity="0.25" stroke="#f43f5e" stroke-width="1" stroke-dasharray="6 3" />

        <!-- Security Red Cross Barrier / Laser -->
        <circle cx="120" cy="115" r="48" fill="#4c0519" stroke="#fb7185" stroke-width="2" />
        
        <!-- Hand / Stop Symbol or Crossed Key -->
        <line x1="88" y1="83" x2="152" y2="147" stroke="#e11d48" stroke-width="8" stroke-linecap="round" />
        <circle cx="120" cy="115" r="40" stroke="#e11d48" stroke-width="6" fill="none" />

        <!-- Biometric Scan Beams -->
        <line x1="40" y1="90" x2="200" y2="90" stroke="#f43f5e" stroke-width="1.5" stroke-dasharray="4 4" opacity="0.6" />
        <line x1="40" y1="140" x2="200" y2="140" stroke="#f43f5e" stroke-width="1.5" stroke-dasharray="4 4" opacity="0.6" />

        <!-- Hazard Warning Stripes -->
        <g transform="translate(65, 175)">
            <rect x="0" y="0" width="110" height="14" rx="4" fill="#fbbf24" stroke="#000" stroke-width="1" />
            <polygon points="10,0 20,0 10,14 0,14" fill="#000" />
            <polygon points="30,0 40,0 30,14 20,14" fill="#000" />
            <polygon points="50,0 60,0 50,14 40,14" fill="#000" />
            <polygon points="70,0 80,0 70,14 60,14" fill="#000" />
            <polygon points="90,0 100,0 90,14 80,14" fill="#000" />
        </g>
    </svg>
@endsection

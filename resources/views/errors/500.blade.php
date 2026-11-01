@extends('errors.layout')

@section('title', '500 - Gangguan Internal Server | Aquafarm Priangan')
@section('code', '500')
@section('code_name', 'Internal Server Error')
@section('status_badge_style', 'bg-red-500/20 text-red-300 border-red-400/40')
@section('status_icon', 'fa-solid fa-server')
@section('status_label', 'INTERNAL SERVER EXCEPTION')

@section('heading', 'Terjadi Gangguan Internal Server (500)')

@section('description')
    {{ $message ?? 'Sistem pemrosesan backend mengalami kegagalan tak terduga saat mengeksekusi instruksi data tambak. Seluruh jejak insiden telah dicatat ke dalam log telemetri server secara terenkripsi untuk investigasi tim pengembang.' }}
@endsection

@section('troubleshooting')
    <li class="flex items-start gap-2">
        <i class="fa-solid fa-circle-check text-red-400 text-[11px] mt-1 shrink-0"></i>
        <span>Tunggu beberapa saat (sekitar 1–2 menit) dan coba muat ulang halaman.</span>
    </li>
    <li class="flex items-start gap-2">
        <i class="fa-solid fa-circle-check text-red-400 text-[11px] mt-1 shrink-0"></i>
        <span>Buka panel <strong>Detail Teknis Diagnostik</strong> di bawah dan salin data log insiden.</span>
    </li>
    <li class="flex items-start gap-2">
        <i class="fa-solid fa-circle-check text-red-400 text-[11px] mt-1 shrink-0"></i>
        <span>Kirimkan laporan insiden melalui tombol WhatsApp Helpdesk agar segera ditangani tim teknisi.</span>
    </li>
@endsection

@section('illustration')
    <svg class="w-full h-full max-w-[280px] max-h-[280px]" viewBox="0 0 240 240" fill="none" xmlns="http://www.w3.org/2000/svg">
        <!-- Server Mainframe Rack Outer Body -->
        <rect x="65" y="45" width="110" height="150" rx="14" fill="#0f172a" stroke="#dc2626" stroke-width="2.5" />
        
        <!-- Server Unit 1 (Active) -->
        <rect x="75" y="60" width="90" height="28" rx="6" fill="#1e293b" stroke="#475569" stroke-width="1.5" />
        <circle cx="87" cy="74" r="3.5" fill="#10b981" />
        <circle cx="98" cy="74" r="3.5" fill="#38bdf8" />
        <line x1="115" y1="74" x2="152" y2="74" stroke="#64748b" stroke-width="2" stroke-linecap="round" stroke-dasharray="4 3" />

        <!-- Server Unit 2 (Glitching / Red Alarm) -->
        <rect x="75" y="100" width="90" height="28" rx="6" fill="#450a0a" stroke="#ef4444" stroke-width="2" />
        <circle cx="87" cy="114" r="4" fill="#ef4444" />
        <circle cx="98" cy="114" r="4" fill="#f59e0b" />
        <!-- Glitch lightning pulse inside unit 2 -->
        <path d="M120 108 L114 115 H124 L118 122" stroke="#facc15" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
        <line x1="134" y1="114" x2="154" y2="114" stroke="#f87171" stroke-width="2" stroke-linecap="round" stroke-dasharray="2 2" />

        <!-- Server Unit 3 (Standby) -->
        <rect x="75" y="140" width="90" height="28" rx="6" fill="#1e293b" stroke="#475569" stroke-width="1.5" />
        <circle cx="87" cy="154" r="3.5" fill="#64748b" />
        <circle cx="98" cy="154" r="3.5" fill="#64748b" />
        <line x1="115" y1="154" x2="152" y2="154" stroke="#475569" stroke-width="2" stroke-linecap="round" stroke-dasharray="4 3" />

        <!-- Warning Sparks -->
        <path d="M175 105 L190 95 L182 110 L198 108" stroke="#ef4444" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
        <circle cx="192" cy="120" r="3" fill="#facc15" />
        
        <!-- Cooling Fan Grille at Bottom -->
        <circle cx="120" cy="180" r="6" stroke="#64748b" stroke-width="1.5" stroke-dasharray="3 2" />
    </svg>
@endsection

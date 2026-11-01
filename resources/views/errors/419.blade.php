@extends('errors.layout')

@section('title', '419 - Sesi Kedaluwarsa | Aquafarm Priangan')
@section('code', '419')
@section('code_name', 'Page Expired')
@section('status_badge_style', 'bg-yellow-500/20 text-yellow-300 border-yellow-400/40')
@section('status_icon', 'fa-solid fa-hourglass-end')
@section('status_label', 'CSRF SECURITY TOKEN EXPIRED')

@section('heading', 'Sesi Halaman Kedaluwarsa (Page Expired)')

@section('description')
    {{ $message ?? 'Sesi keamanan formulir Anda telah berakhir karena halaman didiamkan terlalu lama tanpa aktivitas. Sistem menerapkan token CSRF otomatis demi melindungi integritas data tambak dari manipulasi pihak luar.' }}
@endsection

@section('troubleshooting')
    <li class="flex items-start gap-2">
        <i class="fa-solid fa-circle-check text-yellow-400 text-[11px] mt-1 shrink-0"></i>
        <span>Cukup klik tombol <strong>Muat Ulang</strong> di bawah untuk menyegarkan token sesi baru.</span>
    </li>
    <li class="flex items-start gap-2">
        <i class="fa-solid fa-circle-check text-yellow-400 text-[11px] mt-1 shrink-0"></i>
        <span>Kirimkan kembali formulir atau tindakan yang sebelumnya ingin Anda simpan.</span>
    </li>
    <li class="flex items-start gap-2">
        <i class="fa-solid fa-circle-check text-yellow-400 text-[11px] mt-1 shrink-0"></i>
        <span>Jika Anda sedang berada pada halaman login, silakan muat ulang dan input kembali kredensial.</span>
    </li>
@endsection

@section('illustration')
    <svg class="w-full h-full max-w-[280px] max-h-[280px]" viewBox="0 0 240 240" fill="none" xmlns="http://www.w3.org/2000/svg">
        <!-- Ambient Orbit Ring -->
        <circle cx="120" cy="120" r="90" stroke="#ca8a04" stroke-width="1.5" stroke-dasharray="8 6" opacity="0.4" />
        
        <!-- Hourglass Outer Body -->
        <path d="M75 50 H165 L130 115 L165 180 H75 L110 115 Z" fill="#06122c" stroke="#eab308" stroke-width="3" stroke-linejoin="round" />
        
        <!-- Sand in Top Chamber (Depleting) -->
        <path d="M86 65 H154 L120 112 Z" fill="#ca8a04" opacity="0.3" />
        
        <!-- Falling Sand Stream -->
        <line x1="120" y1="112" x2="120" y2="150" stroke="#facc15" stroke-width="2.5" stroke-dasharray="3 3" />

        <!-- Sand Accumulated in Bottom Chamber -->
        <path d="M85 170 H155 L138 140 H102 Z" fill="#eab308" />

        <!-- Floating CSRF Shield Badge -->
        <g transform="translate(150, 70)">
            <circle cx="20" cy="20" r="22" fill="#713f12" stroke="#facc15" stroke-width="2" />
            <path d="M12 18 L18 24 L28 14" stroke="#fef08a" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" />
            <!-- Tiny Clock Hands -->
            <line x1="20" y1="20" x2="20" y2="10" stroke="#facc15" stroke-width="2" stroke-linecap="round" />
            <line x1="20" y1="20" x2="26" y2="20" stroke="#facc15" stroke-width="2" stroke-linecap="round" />
        </g>

        <!-- Water Flow Micro Ripple -->
        <path d="M60 205 Q 120 190, 180 205" stroke="#facc15" stroke-width="2" stroke-linecap="round" opacity="0.6" />
    </svg>
@endsection


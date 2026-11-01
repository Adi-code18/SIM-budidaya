@extends('errors.layout')

@section('title', '401 - Belum Terautentikasi | Aquafarm Priangan')
@section('code', '401')
@section('code_name', 'Unauthorized')
@section('status_badge_style', 'bg-indigo-500/20 text-indigo-300 border-indigo-400/40')
@section('status_icon', 'fa-solid fa-user-lock')
@section('status_label', 'AUTHENTICATION REQUIRED')

@section('heading', 'Autentikasi Diperlukan (Unauthorized)')

@section('description')
    {{ $message ?? 'Sesi kredensial Anda belum terverifikasi atau telah kedaluwarsa. Sistem memerlukan login akun petugas/manajer yang valid untuk mengakses data tambak dan panel kontrol ini.' }}
@endsection

@section('troubleshooting')
    <li class="flex items-start gap-2">
        <i class="fa-solid fa-circle-check text-indigo-400 text-[11px] mt-1 shrink-0"></i>
        <span>Silakan masuk kembali (login) menggunakan akun resmi Anda.</span>
    </li>
    <li class="flex items-start gap-2">
        <i class="fa-solid fa-circle-check text-indigo-400 text-[11px] mt-1 shrink-0"></i>
        <span>Pastikan verifikasi Two-Factor (2FA) atau OTP email telah diselesaikan.</span>
    </li>
    <li class="flex items-start gap-2">
        <i class="fa-solid fa-circle-check text-indigo-400 text-[11px] mt-1 shrink-0"></i>
        <span>Jika Anda merasa ini keliru, hubungi Administrator Tambak untuk pembaruan akun.</span>
    </li>
@endsection

@section('illustration')
    <svg class="w-full h-full max-w-[280px] max-h-[280px]" viewBox="0 0 240 240" fill="none" xmlns="http://www.w3.org/2000/svg">
        <!-- Shield Background -->
        <path d="M120 25 L185 55 V115 C185 160 120 200 120 200 C120 200 55 160 55 115 V55 L120 25 Z" fill="#0f172a" stroke="#6366f1" stroke-width="2.5" />
        <path d="M120 38 L172 63 V115 C172 150 120 185 120 185 C120 185 68 150 68 115 V63 L120 38 Z" fill="#1e1b4b" fill-opacity="0.5" stroke="#6366f1" stroke-width="1" stroke-dasharray="4 3" />

        <!-- Padlock Body -->
        <rect x="92" y="105" width="56" height="48" rx="10" fill="#312e81" stroke="#818cf8" stroke-width="2" />
        <!-- Padlock Shackle -->
        <path d="M102 105 V88 C102 78 110 70 120 70 C130 70 138 78 138 88 V105" stroke="#818cf8" stroke-width="3" stroke-linecap="round" fill="none" />
        
        <!-- Keyhole with Fingerprint glow lines -->
        <circle cx="120" cy="123" r="4.5" fill="#c7d2fe" />
        <path d="M117 127 L115 138 H125 L123 127 Z" fill="#c7d2fe" />

        <!-- Biometric Scan Ring -->
        <circle cx="120" cy="120" r="45" stroke="#6366f1" stroke-width="1.5" stroke-dasharray="6 6" opacity="0.6" />
        <line x1="75" y1="120" x2="165" y2="120" stroke="#818cf8" stroke-width="1.5" stroke-linecap="round" opacity="0.75" />

        <!-- Floating Identity Tags -->
        <circle cx="65" cy="70" r="14" fill="#06122c" stroke="#818cf8" stroke-width="1.5" />
        <path d="M60 76 C60 72 62 70 65 70 C68 70 70 72 70 76" stroke="#818cf8" stroke-width="1.5" stroke-linecap="round" />
        <circle cx="65" cy="66" r="3" fill="#818cf8" />
    </svg>
@endsection


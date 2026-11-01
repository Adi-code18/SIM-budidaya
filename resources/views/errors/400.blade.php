@extends('errors.layout')

@section('title', '400 - Permintaan Tidak Valid | Aquafarm Priangan')
@section('code', '400')
@section('code_name', 'Bad Request')
@section('status_badge_style', 'bg-amber-500/20 text-amber-300 border-amber-400/40')
@section('status_icon', 'fa-solid fa-triangle-exclamation')
@section('status_label', 'CLIENT SYNTAX ERROR')

@section('heading', 'Permintaan Tidak Valid (Bad Request)')

@section('description')
    {{ $message ?? 'Server sistem budidaya tidak dapat memahami atau memproses format data yang dikirimkan. Struktur parameter URL atau muatan payload formulir terindikasi keliru atau korup.' }}
@endsection

@section('troubleshooting')
    <li class="flex items-start gap-2">
        <i class="fa-solid fa-circle-check text-amber-400 text-[11px] mt-1 shrink-0"></i>
        <span>Periksa kembali parameter query atau format angka/teks yang Anda input pada formulir.</span>
    </li>
    <li class="flex items-start gap-2">
        <i class="fa-solid fa-circle-check text-amber-400 text-[11px] mt-1 shrink-0"></i>
        <span>Pastikan tidak ada karakter khusus yang tidak valid dalam tautan URL.</span>
    </li>
    <li class="flex items-start gap-2">
        <i class="fa-solid fa-circle-check text-amber-400 text-[11px] mt-1 shrink-0"></i>
        <span>Hapus cookie & cache peramban, kemudian ulangi tindakan Anda.</span>
    </li>
@endsection

@section('illustration')
    <svg class="w-full h-full max-w-[280px] max-h-[280px]" viewBox="0 0 240 240" fill="none" xmlns="http://www.w3.org/2000/svg">
        <!-- Floating Backdrop Hexagons -->
        <polygon points="120,20 200,65 200,155 120,200 40,155 40,65" fill="#005aab" fill-opacity="0.12" stroke="#005aab" stroke-width="1.5" stroke-dasharray="4 4" />
        <polygon points="120,40 180,75 180,145 120,180 60,145 60,75" fill="#FF8F00" fill-opacity="0.08" stroke="#FF8F00" stroke-width="1" />

        <!-- Fragmented Data Packet Container -->
        <rect x="70" y="70" width="100" height="90" rx="16" fill="#06122c" stroke="#f59e0b" stroke-width="2.5" />
        
        <!-- Signal Glitch Lines -->
        <line x1="85" y1="95" x2="155" y2="95" stroke="#f59e0b" stroke-width="3" stroke-linecap="round" stroke-dasharray="6 4" />
        <line x1="85" y1="110" x2="135" y2="110" stroke="#f59e0b" stroke-width="3" stroke-linecap="round" />
        <line x1="85" y1="125" x2="145" y2="125" stroke="#38bdf8" stroke-width="2.5" stroke-linecap="round" stroke-dasharray="3 3" />

        <!-- Disconnected Nodes -->
        <circle cx="155" cy="110" r="5" fill="#ef4444" />
        <line x1="135" y1="110" x2="150" y2="110" stroke="#ef4444" stroke-width="2" stroke-dasharray="2 2" />

        <!-- Warning Triangle Badge -->
        <g transform="translate(145, 125)">
            <polygon points="20,5 37,35 3,35" fill="#f59e0b" stroke="#06122c" stroke-width="2.5" />
            <text x="20" y="29" font-family="'Plus Jakarta Sans', sans-serif" font-size="16" font-weight="bold" fill="#06122c" text-anchor="middle">!</text>
        </g>

        <!-- Wave Disturbance -->
        <path d="M45 210 Q 75 195, 105 210 T 165 210 T 225 210" fill="none" stroke="#f59e0b" stroke-width="2" opacity="0.4" />
        <path d="M30 220 Q 65 205, 100 220 T 170 220 T 235 220" fill="none" stroke="#005aab" stroke-width="2" opacity="0.5" />
    </svg>
@endsection


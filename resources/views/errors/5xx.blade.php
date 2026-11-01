@extends('errors.layout')

@section('title', ($exception->getStatusCode() ?? '5xx') . ' - Kesalahan Sisi Server | Aquafarm Priangan')
@section('code', $exception->getStatusCode() ?? '5xx')
@section('code_name', 'Server Error')
@section('status_badge_style', 'bg-red-500/20 text-red-300 border-red-400/40')
@section('status_icon', 'fa-solid fa-server')
@section('status_label', 'SERVER INFRASTRUCTURE EXCEPTION')

@section('heading', 'Terjadi Gangguan pada Server (5xx)')

@section('description')
    {{ $message ?? 'Server platform budidaya menghadapi masalah tak terduga dalam menangani permintaan. Insiden ini telah otomatis dicatat untuk penanganan teknis.' }}
@endsection

@section('troubleshooting')
    <li class="flex items-start gap-2">
        <i class="fa-solid fa-circle-check text-red-400 text-[11px] mt-1 shrink-0"></i>
        <span>Harap tunggu beberapa saat lalu muat ulang halaman.</span>
    </li>
    <li class="flex items-start gap-2">
        <i class="fa-solid fa-circle-check text-red-400 text-[11px] mt-1 shrink-0"></i>
        <span>Buka panel diagnostik di bawah untuk menyalin kode insiden dan laporkan via WhatsApp.</span>
    </li>
@endsection

@section('illustration')
    <svg class="w-full h-full max-w-[280px] max-h-[280px]" viewBox="0 0 240 240" fill="none" xmlns="http://www.w3.org/2000/svg">
        <rect x="60" y="60" width="120" height="120" rx="16" fill="#0f172a" stroke="#ef4444" stroke-width="2.5" />
        <line x1="80" y1="95" x2="160" y2="95" stroke="#ef4444" stroke-width="2" stroke-linecap="round" />
        <line x1="80" y1="120" x2="140" y2="120" stroke="#f87171" stroke-width="2" stroke-linecap="round" stroke-dasharray="4 4" />
        <line x1="80" y1="145" x2="160" y2="145" stroke="#ef4444" stroke-width="2" stroke-linecap="round" />
        <circle cx="160" cy="120" r="4" fill="#facc15" />
    </svg>
@endsection


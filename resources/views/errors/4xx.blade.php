@extends('errors.layout')

@section('title', ($exception->getStatusCode() ?? '4xx') . ' - Kesalahan Sisi Klien | Aquafarm Priangan')
@section('code', $exception->getStatusCode() ?? '4xx')
@section('code_name', 'Client Error')
@section('status_badge_style', 'bg-amber-500/20 text-amber-300 border-amber-400/40')
@section('status_icon', 'fa-solid fa-triangle-exclamation')
@section('status_label', 'CLIENT REQUEST EXCEPTION')

@section('heading', 'Terjadi Kesalahan pada Permintaan (4xx)')

@section('description')
    {{ $message ?? 'Sistem tidak dapat menyelesaikan tindakan karena format data permintaan dari peramban Anda tidak sesuai atau tidak diizinkan oleh sistem.' }}
@endsection

@section('troubleshooting')
    <li class="flex items-start gap-2">
        <i class="fa-solid fa-circle-check text-amber-400 text-[11px] mt-1 shrink-0"></i>
        <span>Periksa kembali input data dan alamat URL halaman yang Anda tuju.</span>
    </li>
    <li class="flex items-start gap-2">
        <i class="fa-solid fa-circle-check text-amber-400 text-[11px] mt-1 shrink-0"></i>
        <span>Coba segarkan halaman atau bersihkan sesi peramban Anda.</span>
    </li>
@endsection

@section('illustration')
    <svg class="w-full h-full max-w-[280px] max-h-[280px]" viewBox="0 0 240 240" fill="none" xmlns="http://www.w3.org/2000/svg">
        <circle cx="120" cy="120" r="80" fill="#0f172a" stroke="#f59e0b" stroke-width="2.5" />
        <circle cx="120" cy="120" r="60" stroke="#f59e0b" stroke-width="1.5" stroke-dasharray="6 4" opacity="0.6" />
        <polygon points="120,70 160,140 80,140" fill="#78350f" stroke="#fbbf24" stroke-width="2" />
        <text x="120" y="130" font-family="'Barlow', sans-serif" font-size="28" font-weight="900" fill="#fbbf24" text-anchor="middle">!</text>
        <circle cx="120" cy="165" r="5" fill="#f59e0b" />
    </svg>
@endsection


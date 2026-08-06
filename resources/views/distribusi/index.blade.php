@extends('layouts.app')

@section('title', 'Distribusi & Order - SIM-BUDIDAYA')

@section('content')
<div class="space-y-6" x-data="{ activeTab: 'all' }">

    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">Distribusi & Order Panen</h1>
            <p class="text-sm text-slate-500 mt-1">Kelola penjualan hasil panen, jadwal pengiriman, dan pesanan pembeli.</p>
        </div>
        <button class="px-5 py-2.5 rounded-xl bg-sky-600 hover:bg-sky-700 text-white font-bold text-sm shadow-md shadow-sky-600/20 transition-all flex items-center gap-2">
            <i class="fa-solid fa-cart-plus"></i>
            <span>Buat Order Panen Baru</span>
        </button>
    </div>

    <!-- Metric Stats Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm">
            <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Total Order Masuk</span>
            <div class="mt-3 flex items-baseline justify-between">
                <h3 class="text-2xl font-extrabold text-slate-900">128 <span class="text-xs font-normal text-slate-500">order</span></h3>
                <span class="px-2 py-1 bg-sky-100 text-sky-700 font-bold text-xs rounded-lg">+12 baru</span>
            </div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm">
            <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Dalam Pengiriman</span>
            <div class="mt-3 flex items-baseline justify-between">
                <h3 class="text-2xl font-extrabold text-slate-900">18 <span class="text-xs font-normal text-slate-500">truk</span></h3>
                <span class="px-2 py-1 bg-amber-100 text-amber-700 font-bold text-xs rounded-lg">Aktif</span>
            </div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm">
            <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Order Selesai</span>
            <div class="mt-3 flex items-baseline justify-between">
                <h3 class="text-2xl font-extrabold text-slate-900">105 <span class="text-xs font-normal text-slate-500">order</span></h3>
                <span class="px-2 py-1 bg-emerald-100 text-emerald-700 font-bold text-xs rounded-lg">98.2% Selesai</span>
            </div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm">
            <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Total Tonase Terkirim</span>
            <div class="mt-3 flex items-baseline justify-between">
                <h3 class="text-2xl font-extrabold text-slate-900">14.2 <span class="text-xs font-normal text-slate-500">Ton</span></h3>
                <span class="px-2 py-1 bg-blue-100 text-blue-700 font-bold text-xs rounded-lg">Agustus 2026</span>
            </div>
        </div>
    </div>

    <!-- Filter Tabs -->
    <div class="bg-white p-2 rounded-2xl border border-slate-200/80 shadow-sm flex items-center justify-between">
        <div class="flex items-center gap-2 overflow-x-auto">
            <button @click="activeTab = 'all'" :class="activeTab === 'all' ? 'bg-sky-600 text-white font-bold' : 'text-slate-600 hover:bg-slate-100 font-semibold'" class="px-4 py-2 rounded-xl text-xs transition-all">
                Semua Order (128)
            </button>
            <button @click="activeTab = 'pending'" :class="activeTab === 'pending' ? 'bg-sky-600 text-white font-bold' : 'text-slate-600 hover:bg-slate-100 font-semibold'" class="px-4 py-2 rounded-xl text-xs transition-all">
                Pending (12)
            </button>
            <button @click="activeTab = 'process'" :class="activeTab === 'process' ? 'bg-sky-600 text-white font-bold' : 'text-slate-600 hover:bg-slate-100 font-semibold'" class="px-4 py-2 rounded-xl text-xs transition-all">
                Dalam Proses (18)
            </button>
            <button @click="activeTab = 'completed'" :class="activeTab === 'completed' ? 'bg-sky-600 text-white font-bold' : 'text-slate-600 hover:bg-slate-100 font-semibold'" class="px-4 py-2 rounded-xl text-xs transition-all">
                Selesai (98)
            </button>
        </div>
    </div>

    <!-- Order Cards Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">

        <!-- Order Card 1 -->
        <div class="bg-white rounded-2xl border border-slate-200/80 p-5 shadow-sm space-y-4 hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                <div>
                    <span class="text-xs font-bold text-sky-600">#ORD-2026-089</span>
                    <h4 class="font-bold text-slate-900 text-base">PT Resto Seafood Nusantara</h4>
                </div>
                <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-amber-100 text-amber-700">Dalam Pengiriman</span>
            </div>

            <div class="space-y-2 text-xs text-slate-600">
                <div class="flex justify-between">
                    <span class="text-slate-400">Komoditas:</span>
                    <span class="font-bold text-slate-800">Ikan Nila Hitam (Fresh)</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-slate-400">Volume Panen:</span>
                    <span class="font-bold text-slate-800">500 kg (KOLAM-NW-01)</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-slate-400">Tujuan Pengiriman:</span>
                    <span class="font-bold text-slate-800">Semarang, Jawa Tengah</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-slate-400">Total Nilai Order:</span>
                    <span class="font-extrabold text-sky-600 text-sm">Rp 17,500,000</span>
                </div>
            </div>

            <div class="pt-3 border-t border-slate-100 flex items-center justify-between">
                <span class="text-[11px] text-slate-400">Estimasi Tiba: Hari ini, 16:00</span>
                <button class="px-3 py-1.5 rounded-lg bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs transition-colors">
                    Detail Logistik
                </button>
            </div>
        </div>

        <!-- Order Card 2 -->
        <div class="bg-white rounded-2xl border border-slate-200/80 p-5 shadow-sm space-y-4 hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                <div>
                    <span class="text-xs font-bold text-sky-600">#ORD-2026-090</span>
                    <h4 class="font-bold text-slate-900 text-base">CV Pasar Mina Utama</h4>
                </div>
                <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-700">Selesai</span>
            </div>

            <div class="space-y-2 text-xs text-slate-600">
                <div class="flex justify-between">
                    <span class="text-slate-400">Komoditas:</span>
                    <span class="font-bold text-slate-800">Ikan Lele Sangkuriang</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-slate-400">Volume Panen:</span>
                    <span class="font-bold text-slate-800">800 kg (KOLAM-SL-04)</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-slate-400">Tujuan Pengiriman:</span>
                    <span class="font-bold text-slate-800">Surakarta, Jawa Tengah</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-slate-400">Total Nilai Order:</span>
                    <span class="font-extrabold text-sky-600 text-sm">Rp 19,200,000</span>
                </div>
            </div>

            <div class="pt-3 border-t border-slate-100 flex items-center justify-between">
                <span class="text-[11px] text-slate-400">Diterima: 05 Ags 2026</span>
                <button class="px-3 py-1.5 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs transition-colors">
                    Faktur / Invoice
                </button>
            </div>
        </div>

        <!-- Order Card 3 -->
        <div class="bg-white rounded-2xl border border-slate-200/80 p-5 shadow-sm space-y-4 hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                <div>
                    <span class="text-xs font-bold text-sky-600">#ORD-2026-091</span>
                    <h4 class="font-bold text-slate-900 text-base">Supermarket FreshMart</h4>
                </div>
                <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-sky-100 text-sky-700">Pending Confirm</span>
            </div>

            <div class="space-y-2 text-xs text-slate-600">
                <div class="flex justify-between">
                    <span class="text-slate-400">Komoditas:</span>
                    <span class="font-bold text-slate-800">Ikan Gurame Super</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-slate-400">Volume Panen:</span>
                    <span class="font-bold text-slate-800">300 kg (KOLAM-KL-02)</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-slate-400">Tujuan Pengiriman:</span>
                    <span class="font-bold text-slate-800">Yogyakarta</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-slate-400">Total Nilai Order:</span>
                    <span class="font-extrabold text-sky-600 text-sm">Rp 13,500,000</span>
                </div>
            </div>

            <div class="pt-3 border-t border-slate-100 flex items-center justify-between">
                <span class="text-[11px] text-slate-400">Dibuat: Hari ini, 09:15</span>
                <button class="px-3 py-1.5 rounded-lg bg-sky-600 hover:bg-sky-700 text-white font-bold text-xs transition-colors">
                    Konfirmasi Order
                </button>
            </div>
        </div>

    </div>

</div>
@endsection

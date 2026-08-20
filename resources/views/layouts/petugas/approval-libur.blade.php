@extends('layouts.app')

@section('title', 'Tinjau Persetujuan Libur - SIM-BUDIDAYA')

@section('content')
<div class="space-y-6 max-w-4xl mx-auto">
    
    <!-- Top Header & Status Badge -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 text-xs font-semibold text-slate-400 mb-1">
                <a href="{{ route('petugas') }}" class="hover:text-slate-600 transition-colors">Manajemen Petugas</a>
                <i class="fa-solid fa-chevron-right text-[10px]"></i>
                <span class="text-slate-600">Tinjau Persetujuan Libur</span>
            </div>
            <h1 class="text-2xl font-extrabold text-[#0B2570] tracking-tight">Tinjau Persetujuan Libur</h1>
            <p class="text-xs text-slate-500 font-medium mt-0.5">Detail informasi persetujuan izin dan durasi libur staf.</p>
        </div>
        
        <div>
            <span class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full text-xs font-extrabold bg-emerald-50 text-emerald-700 border border-emerald-200 shadow-xs">
                <i class="fa-solid fa-circle-check text-emerald-600"></i>
                <span>Disetujui</span>
            </span>
        </div>
    </div>

    <!-- Detail Persetujuan Libur Card -->
    <div class="bg-white p-6 sm:p-8 rounded-2xl border border-slate-200/80 shadow-xs space-y-6">
        
        <div class="flex items-center gap-2 text-xs font-bold text-[#0B2570] pb-3 border-b border-slate-100">
            <i class="fa-solid fa-id-card text-sky-600 text-sm"></i>
            <span>Informasi Pemohon &amp; Status Libur</span>
        </div>

        <!-- Profile Detail Row -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 p-4 rounded-xl bg-slate-50 border border-slate-100">
            <div class="flex items-center gap-4">
                <img src="https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?auto=format&fit=crop&q=80&w=160" 
                     alt="Budi Santoso" 
                     class="w-14 h-14 rounded-full object-cover border-2 border-white shadow-xs">
                <div>
                    <div class="flex items-center gap-2">
                        <h3 class="text-base font-extrabold text-slate-900">Budi Santoso</h3>
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-emerald-100 text-emerald-700 uppercase">Disetujui</span>
                    </div>
                    <p class="text-xs text-slate-500 font-medium">Teknisi Kolam Pembesaran - Sektor A</p>
                </div>
            </div>

            <div class="flex items-center gap-2 text-xs">
                <span class="px-2.5 py-1 rounded-lg bg-white border border-slate-200 text-slate-600 font-bold">
                    ID: EMP-2023-047
                </span>
                <span class="px-2.5 py-1 rounded-lg bg-white border border-slate-200 text-slate-600 font-bold">
                    Bergabung: 01 Jan 2022
                </span>
            </div>
        </div>

        <!-- Detail Grid Fields -->
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 pt-2">
            <div class="p-3.5 rounded-xl bg-slate-50/70 border border-slate-100">
                <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400 block mb-1">JENIS IZIN</span>
                <div class="flex items-center gap-2 text-xs font-extrabold text-slate-800">
                    <i class="fa-solid fa-plane-departure text-sky-600"></i>
                    <span>Cuti Tahunan</span>
                </div>
            </div>

            <div class="p-3.5 rounded-xl bg-slate-50/70 border border-slate-100">
                <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400 block mb-1">DURASI LIBUR</span>
                <div class="flex items-center gap-2 text-xs font-extrabold text-slate-800">
                    <i class="fa-regular fa-clock text-sky-600"></i>
                    <span>3 Hari Kerja</span>
                </div>
            </div>

            <div class="p-3.5 rounded-xl bg-slate-50/70 border border-slate-100">
                <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400 block mb-1">TANGGAL MULAI</span>
                <div class="flex items-center gap-2 text-xs font-extrabold text-slate-800">
                    <i class="fa-regular fa-calendar-check text-emerald-600"></i>
                    <span>12 Oktober 2026</span>
                </div>
            </div>

            <div class="p-3.5 rounded-xl bg-slate-50/70 border border-slate-100">
                <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400 block mb-1">TANGGAL SELESAI</span>
                <div class="flex items-center gap-2 text-xs font-extrabold text-slate-800">
                    <i class="fa-regular fa-calendar-xmark text-sky-600"></i>
                    <span>14 Oktober 2026</span>
                </div>
            </div>
        </div>

        <!-- Alasan & Keterangan Pengajuan -->
        <div>
            <label class="text-[10px] font-extrabold uppercase tracking-wider text-slate-500 block mb-1.5">Alasan &amp; Keterangan Libur</label>
            <div class="p-4 rounded-xl bg-slate-50/80 border border-slate-200 text-xs font-medium text-slate-700 leading-relaxed">
                "Mengikuti acara pernikahan keluarga di Sumedang, jadwal pemeliharaan kolam sektor A sudah diserahkan sementara ke Sdr. Fajar untuk penanganan harian."
            </div>
        </div>

        <!-- Action Button -->
        <div class="flex items-center justify-end pt-4 border-t border-slate-100">
            <a href="{{ route('petugas') }}" class="px-5 py-2.5 rounded-xl bg-[#031B4E] hover:bg-navy-900 text-white font-extrabold text-xs shadow-md shadow-sky-950/20 transition-all flex items-center gap-2">
                <i class="fa-solid fa-arrow-left text-xs"></i>
                <span>Kembali ke Data Petugas</span>
            </a>
        </div>

    </div>

</div>
@endsection

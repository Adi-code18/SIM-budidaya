@extends('layouts.app')

@section('title', 'Form Ajukan Libur Petugas - SIM-BUDIDAYA')

@section('content')
<div class="space-y-6 max-w-4xl mx-auto">
    <!-- Breadcrumb & Header Page -->
    <div class="space-y-1">
        <div class="flex items-center gap-2 text-xs font-semibold text-slate-400">
            <a href="{{ route('petugas') }}" class="hover:text-slate-600 transition-colors">Manajemen Petugas</a>
            <i class="fa-solid fa-chevron-right text-[10px]"></i>
            <span class="text-slate-600">Form Ajukan Libur</span>
        </div>
        <h1 class="text-2xl font-extrabold text-[#0B2570] tracking-tight">Form Ajukan Libur</h1>
        <p class="text-xs text-slate-500 font-medium">Isi formulir di bawah ini untuk mengajukan jadwal libur atau izin kerja lapangan.</p>
    </div>

    <!-- Main Form Container Card -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs p-6 sm:p-8 space-y-6">
        
        <div class="flex items-center gap-2 text-xs font-bold text-[#0B2570] pb-3 border-b border-slate-100">
            <i class="fa-solid fa-file-pen text-sky-600 text-sm"></i>
            <span>DETAIL PENGAJUAN</span>
        </div>

        <form action="#" method="POST" @submit.prevent class="space-y-5">
            
            <!-- Field: Akun Petugas -->
            <div>
                <label class="text-[10px] font-extrabold uppercase tracking-wider text-slate-500 block mb-1.5">AKUN PETUGAS</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                        <i class="fa-solid fa-shield-halved text-xs"></i>
                    </div>
                    <input type="text" 
                           value="BUS-CUTI-012" 
                           readonly 
                           class="w-full pl-9 pr-3.5 py-2.5 rounded-xl border border-slate-200 text-xs font-extrabold text-slate-700 bg-slate-100 cursor-not-allowed">
                </div>
                <p class="text-[10px] text-slate-400 font-medium mt-1">Permohonan izin cuti akan dicatat atas nama akun ini.</p>
            </div>

            <!-- Grid 2 Cols: Tanggal Mulai & Tanggal Selesai -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="text-[10px] font-extrabold uppercase tracking-wider text-slate-500 block mb-1.5">Tanggal Mulai *</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                            <i class="fa-regular fa-calendar text-xs"></i>
                        </div>
                        <input type="text" 
                               value="17/08/2026" 
                               placeholder="dd/mm/yyyy" 
                               class="w-full pl-9 pr-3.5 py-2.5 rounded-xl border border-slate-200 text-xs font-semibold text-slate-800 bg-slate-50/70 focus:bg-white focus:outline-none focus:ring-2 focus:ring-sky-500 transition-all">
                    </div>
                </div>

                <div>
                    <label class="text-[10px] font-extrabold uppercase tracking-wider text-slate-500 block mb-1.5">Tanggal Selesai *</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                            <i class="fa-regular fa-calendar text-xs"></i>
                        </div>
                        <input type="text" 
                               value="19/08/2026" 
                               placeholder="dd/mm/yyyy" 
                               class="w-full pl-9 pr-3.5 py-2.5 rounded-xl border border-slate-200 text-xs font-semibold text-slate-800 bg-slate-50/70 focus:bg-white focus:outline-none focus:ring-2 focus:ring-sky-500 transition-all">
                    </div>
                </div>
            </div>

            <!-- Field: Keterangan / Alasan -->
            <div>
                <label class="text-[10px] font-extrabold uppercase tracking-wider text-slate-500 block mb-1.5">Keterangan / Alasan *</label>
                <textarea rows="4" 
                          placeholder="Tuliskan alasan pengajuan libur dengan jelas..." 
                          class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs font-semibold text-slate-800 bg-slate-50/70 focus:bg-white focus:outline-none focus:ring-2 focus:ring-sky-500 transition-all">Izin menghadiri pernikahan saudara dengan alasan...</textarea>
            </div>

            <!-- Action Buttons -->
            <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
                <a href="{{ route('petugas') }}" 
                   class="px-5 py-2.5 rounded-xl border border-slate-200 text-xs font-bold text-slate-600 hover:bg-slate-50 transition-colors">
                    Batal
                </a>
                <button type="submit" 
                        class="px-6 py-2.5 rounded-xl bg-[#031B4E] hover:bg-navy-900 text-white font-extrabold text-xs shadow-md shadow-sky-950/20 transition-all flex items-center gap-2">
                    <span>Kirim Pengajuan</span>
                    <i class="fa-solid fa-paper-plane text-xs"></i>
                </button>
            </div>

        </form>

    </div>
</div>
@endsection

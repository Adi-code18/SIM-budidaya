@extends('layouts.app')

@section('title', 'Tambah Petugas Baru - SIM-BUDIDAYA')

@section('content')
<div class="space-y-6 max-w-6xl mx-auto">
    <!-- Header Page -->
    <div class="flex items-center gap-3">
        <a href="{{ route('petugas') }}" class="w-9 h-9 rounded-xl bg-white border border-slate-200 text-slate-600 hover:bg-slate-50 flex items-center justify-center transition-colors shadow-xs">
            <i class="fa-solid fa-arrow-left text-sm"></i>
        </a>
        <div>
            <h1 class="text-2xl font-extrabold text-[#0B2570] tracking-tight">Tambah Petugas Baru</h1>
            <p class="text-xs text-slate-500 font-medium mt-0.5">Lengkapi informasi di bawah untuk menambahkan staf baru.</p>
        </div>
    </div>

    <!-- Main Grid Form -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        
        <!-- Left Section (8 cols): Personal Info & Work Experience -->
        <div class="lg:col-span-8 space-y-6">
            
            <!-- Card 1: Informasi Personal -->
            <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-xs space-y-5">
                <div class="flex items-center gap-2 text-xs font-bold text-[#0B2570] pb-3 border-b border-slate-100">
                    <i class="fa-solid fa-user-gear text-sky-600 text-sm"></i>
                    <span>Informasi Personal</span>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="text-[10px] font-extrabold uppercase tracking-wider text-slate-500 block mb-1.5">Full Name *</label>
                        <input type="text" 
                               value="Ahmad Rifat Septian" 
                               placeholder="Nama lengkap petugas..." 
                               class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs font-semibold text-slate-800 bg-slate-50/70 focus:bg-white focus:outline-none focus:ring-2 focus:ring-sky-500 transition-all">
                    </div>

                    <div>
                        <label class="text-[10px] font-extrabold uppercase tracking-wider text-slate-500 block mb-1.5">Gaji Per Bulan (IDR) *</label>
                        <input type="text" 
                               value="IDR 3.750.000,00" 
                               placeholder="Rp 0,00" 
                               class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs font-semibold text-slate-800 bg-slate-50/70 focus:bg-white focus:outline-none focus:ring-2 focus:ring-sky-500 transition-all">
                    </div>

                    <div>
                        <label class="text-[10px] font-extrabold uppercase tracking-wider text-slate-500 block mb-1.5">Email Aktif *</label>
                        <input type="email" 
                               value="rifat23@gmail.com" 
                               placeholder="email@domain.com" 
                               class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs font-semibold text-slate-800 bg-slate-50/70 focus:bg-white focus:outline-none focus:ring-2 focus:ring-sky-500 transition-all">
                    </div>

                    <div>
                        <label class="text-[10px] font-extrabold uppercase tracking-wider text-slate-500 block mb-1.5">Phone Number *</label>
                        <input type="text" 
                               value="081234567890" 
                               placeholder="081234567890" 
                               class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs font-semibold text-slate-800 bg-slate-50/70 focus:bg-white focus:outline-none focus:ring-2 focus:ring-sky-500 transition-all">
                    </div>
                </div>
            </div>

            <!-- Card 2: Pengalaman Kerja -->
            <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-xs space-y-5">
                <div class="flex items-center gap-2 text-xs font-bold text-[#0B2570] pb-3 border-b border-slate-100">
                    <i class="fa-solid fa-briefcase text-sky-600 text-sm"></i>
                    <span>Pengalaman Kerja</span>
                </div>

                <div class="space-y-4">
                    <div>
                        <label class="text-[10px] font-extrabold uppercase tracking-wider text-slate-500 block mb-1.5">Pilih Jabatan *</label>
                        <select class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs font-semibold text-slate-800 bg-slate-50/70 focus:bg-white focus:outline-none focus:ring-2 focus:ring-sky-500 transition-all cursor-pointer">
                            <option value="logistik">Logistik Pasar</option>
                            <option value="teknisi">Teknisi Kolam Pembesaran</option>
                            <option value="pengawas">Pengawas Pembibitan</option>
                            <option value="manajer">Manajer Operasional</option>
                            <option value="keuangan">Staf Keuangan</option>
                        </select>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="text-[10px] font-extrabold uppercase tracking-wider text-slate-500 block mb-1.5">Status Pegawai *</label>
                            <select class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs font-semibold text-slate-800 bg-slate-50/70 focus:bg-white focus:outline-none focus:ring-2 focus:ring-sky-500 transition-all cursor-pointer">
                                <option value="kontrak" selected>Kontrak</option>
                                <option value="tetap">Tetap</option>
                                <option value="magang">Magang</option>
                            </select>
                        </div>

                        <div>
                            <label class="text-[10px] font-extrabold uppercase tracking-wider text-slate-500 block mb-1.5">Status *</label>
                            <select class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs font-semibold text-slate-800 bg-slate-50/70 focus:bg-white focus:outline-none focus:ring-2 focus:ring-sky-500 transition-all cursor-pointer">
                                <option value="aktif" selected>Aktif</option>
                                <option value="cuti">Cuti</option>
                                <option value="nonaktif">Nonaktif</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        <!-- Right Section (4 cols): Action Card -->
        <div class="lg:col-span-4 space-y-6">
            <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-xs space-y-4">
                <div class="flex items-center gap-2 text-xs font-bold text-[#0B2570] pb-3 border-b border-slate-100">
                    <i class="fa-solid fa-sliders text-sky-600 text-sm"></i>
                    <span>Tindakan</span>
                </div>

                <div class="space-y-3 pt-2">
                    <button type="button" class="w-full py-3 rounded-xl bg-[#031B4E] hover:bg-navy-900 text-white font-extrabold text-xs shadow-md shadow-sky-950/20 transition-all flex items-center justify-center gap-2">
                        <i class="fa-solid fa-user-check"></i>
                        <span>Simpan &amp; Buat Akun</span>
                    </button>
                    
                    <a href="{{ route('petugas') }}" class="w-full py-3 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 text-slate-600 font-bold text-xs transition-colors block text-center">
                        Batal
                    </a>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection

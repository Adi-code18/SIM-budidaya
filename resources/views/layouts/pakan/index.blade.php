@extends('layouts.app')

@section('title', 'Log Pakan Harian - SIM-BUDIDAYA')

@section('content')
<div class="space-y-6 max-w-5xl mx-auto">

    <!-- Subtitle & Page Title Header -->
    <div>
        <span class="text-xs font-semibold text-slate-400 block">Log Pakan</span>
        <h1 class="text-2xl font-extrabold text-[#0B2570] tracking-tight mt-0.5">Log Pakan Harian</h1>
    </div>

    <!-- Main Form Card Container -->
    <div class="bg-white p-6 sm:p-8 rounded-2xl border border-slate-200/80 shadow-xs space-y-6">
        
        <!-- Header inside Form -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-4 border-b border-slate-100">
            <div class="flex items-center gap-3.5">
                <div class="w-11 h-11 rounded-xl bg-sky-100 text-sky-600 flex items-center justify-center shrink-0">
                    <i class="fa-regular fa-clipboard text-xl"></i>
                </div>
                <div>
                    <h3 class="text-base font-bold text-slate-900">Formulir Log Pakan</h3>
                    <p class="text-xs text-slate-500 mt-0.5">Catat rincian pemberian pakan dan kualitas air secara akurat.</p>
                </div>
            </div>
            <div>
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[11px] font-extrabold bg-[#C6F6D5] text-[#22543D] uppercase">
                    AKTIF: KOLAM A1 - A12
                </span>
            </div>
        </div>

        <!-- Form Elements -->
        <form action="#" method="POST" @submit.prevent class="space-y-6">
            
            <!-- Row 1: Kolam & Tanggal -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label class="text-[10px] font-extrabold uppercase tracking-wider text-slate-500 block mb-1.5">
                        PILIH KOLAM PEMBESARAN
                    </label>
                    <div class="relative">
                        <i class="fa-solid fa-water absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                        <select class="w-full pl-9 pr-8 py-2.5 rounded-xl border border-slate-200 text-xs font-semibold text-slate-700 bg-slate-50/70 focus:bg-white focus:outline-none focus:ring-2 focus:ring-sky-500 transition-all appearance-none cursor-pointer">
                            <option value="">Pilih unit kolam...</option>
                            <option value="A1">Kolam A1 (Ikan Nila Hitam)</option>
                            <option value="A2">Kolam A2 (Ikan Nila Merah)</option>
                            <option value="B3">Kolam B3 (Ikan Lele Sangkuriang)</option>
                        </select>
                        <i class="fa-solid fa-chevron-down absolute right-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-[10px]"></i>
                    </div>
                </div>

                <div>
                    <label class="text-[10px] font-extrabold uppercase tracking-wider text-slate-500 block mb-1.5">
                        TANGGAL & WAKTU LOG
                    </label>
                    <div class="relative">
                        <i class="fa-regular fa-calendar absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                        <input type="text" 
                               value="08/01/2026, 12:06 PM" 
                               class="w-full pl-9 pr-4 py-2.5 rounded-xl border border-slate-200 text-xs font-semibold text-slate-700 bg-slate-50/70 focus:bg-white focus:outline-none focus:ring-2 focus:ring-sky-500 transition-all">
                    </div>
                </div>
            </div>

            <!-- Section 1: Data Pemberian Pakan -->
            <div>
                <div class="flex items-center gap-2 text-xs font-bold text-slate-800 pb-3 border-b border-slate-100">
                    <i class="fa-solid fa-bowl-food text-sky-600"></i>
                    <span>Data Pemberian Pakan</span>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5 mt-4">
                    
                    <!-- Pakan Pelet Box -->
                    <div class="bg-slate-50/80 p-4 rounded-xl border border-slate-100 space-y-2">
                        <label class="text-[10px] font-extrabold uppercase tracking-wider text-slate-500 block">
                            PAKAN PELET KOMERSIAL (KG)
                        </label>
                        <div class="flex items-center gap-2">
                            <input type="number" 
                                   step="0.01" 
                                   value="0.00" 
                                   class="w-full px-3.5 py-2 rounded-lg border border-slate-200 text-sm font-extrabold text-slate-900 bg-white focus:outline-none focus:ring-2 focus:ring-sky-500">
                            <span class="text-xs font-extrabold text-slate-400">KG</span>
                        </div>
                        <span class="text-[10px] text-slate-400 font-medium block">Stok gudang: 454.1 kg</span>
                    </div>

                    <!-- Pakan Dedaunan Box -->
                    <div class="bg-slate-50/80 p-4 rounded-xl border border-slate-100 space-y-2">
                        <label class="text-[10px] font-extrabold uppercase tracking-wider text-slate-500 block">
                            PAKAN DEDAUNAN ORGANIK (KG)
                        </label>
                        <div class="grid grid-cols-5 gap-2">
                            <input type="number" 
                                   step="0.1" 
                                   value="0.0" 
                                   class="col-span-2 px-3.5 py-2 rounded-lg border border-slate-200 text-sm font-extrabold text-slate-900 bg-white focus:outline-none focus:ring-2 focus:ring-sky-500">
                            <select class="col-span-3 px-3 py-2 rounded-lg border border-slate-200 text-xs font-semibold text-slate-600 bg-white focus:outline-none focus:ring-2 focus:ring-sky-500">
                                <option value="">Jenis Daun...</option>
                                <option value="talas">Daun Talas</option>
                                <option value="singkong">Daun Singkong</option>
                                <option value="pepaya">Daun Pepaya</option>
                            </select>
                        </div>
                        <span class="text-[10px] text-slate-400 font-medium block">Membantu sistem imun alami.</span>
                    </div>

                </div>
            </div>

            <!-- Section 2: Parameter Kualitas Air & Biaya -->
            <div>
                <div class="flex items-center gap-2 text-xs font-bold text-slate-800 pb-3 border-b border-slate-100">
                    <i class="fa-solid fa-droplet text-sky-600"></i>
                    <span>Parameter Kualitas Air &amp; Harga Biaya Untuk Pangan</span>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5 mt-4">
                    <div>
                        <label class="text-[10px] font-extrabold uppercase tracking-wider text-slate-500 block mb-1.5">
                            BIAYA
                        </label>
                        <input type="text" 
                               value="Rp. 120.000" 
                               class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs font-semibold text-slate-700 bg-slate-50/70 focus:bg-white focus:outline-none focus:ring-2 focus:ring-sky-500 transition-all">
                    </div>

                    <div>
                        <label class="text-[10px] font-extrabold uppercase tracking-wider text-slate-500 block mb-1.5">
                            PH AIR
                        </label>
                        <div class="relative">
                            <i class="fa-solid fa-vial absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                            <input type="text" 
                                   value="7.0" 
                                   class="w-full pl-9 pr-10 py-2.5 rounded-xl border border-slate-200 text-xs font-semibold text-slate-700 bg-slate-50/70 focus:bg-white focus:outline-none focus:ring-2 focus:ring-sky-500 transition-all">
                            <span class="absolute right-3.5 top-1/2 -translate-y-1/2 text-xs font-bold text-slate-400">pH</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
                <button type="button" class="px-5 py-2 rounded-xl border border-slate-200 text-xs font-bold text-slate-600 hover:bg-slate-50 transition-colors">
                    Batal
                </button>
                <button type="submit" class="px-5 py-2 rounded-xl bg-[#051B44] hover:bg-navy-900 text-white font-bold text-xs shadow-xs transition-all">
                    Simpan Log Pakan
                </button>
            </div>

        </form>

    </div>

    <!-- Bottom 2 Quick Metrics -->
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
        
        <!-- Metric 1: FCR Terakhir -->
        <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-xs flex items-center gap-3.5">
            <div class="w-10 h-10 rounded-xl bg-sky-50 text-sky-600 flex items-center justify-center shrink-0">
                <i class="fa-solid fa-chart-line text-sm"></i>
            </div>
            <div>
                <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400 block">FCR TERAKHIR</span>
                <h4 class="text-base font-extrabold text-slate-900 mt-0.5">1.24</h4>
            </div>
        </div>

        <!-- Metric 2: Target Panen -->
        <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-xs flex items-center gap-3.5">
            <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0">
                <i class="fa-solid fa-paw text-sm"></i>
            </div>
            <div>
                <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400 block">TARGET PANEN</span>
                <h4 class="text-base font-extrabold text-slate-900 mt-0.5">12 Mei</h4>
            </div>
        </div>

    </div>

</div>
@endsection

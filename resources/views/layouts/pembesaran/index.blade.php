@extends('layouts.app')

@section('title', 'Manajemen Pembesaran - SIM-BUDIDAYA')

@section('content')
<div class="space-y-6" x-data="{ showForm: false, sumberBenih: 'hatchery' }">

    <!-- Subtitle & Page Title Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <span class="text-xs font-semibold text-slate-400 block uppercase tracking-wider">Manajemen Budidaya / Pembesaran</span>
            <h1 class="text-2xl font-extrabold text-[#0B2570] tracking-tight mt-0.5">Status Kolam Aktif</h1>
        </div>
        <div class="flex items-center gap-3">
            <button @click="showForm = !showForm"
                    class="px-4 py-2 rounded-xl bg-[#051B44] hover:bg-navy-900 text-white font-bold text-xs shadow-xs transition-all flex items-center gap-2">
                <i class="fa-solid" :class="showForm ? 'fa-table-list' : 'fa-circle-plus'" class="text-xs"></i>
                <span x-text="showForm ? 'Lihat Kolam' : 'Batch Baru'"></span>
            </button>
        </div>
    </div>

    <!-- ========= INPUT FORM SECTION ========= -->
    <div x-show="showForm"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 -translate-y-2"
         x-transition:enter-end="opacity-100 translate-y-0"
         class="space-y-4">

        <!-- Header Bar -->
        <div class="bg-[#051B44] rounded-2xl p-6 sm:p-8 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
            <div class="text-white">
                <h2 class="text-xl font-extrabold">Input Batch Pembesaran</h2>
                <p class="text-xs text-sky-200/80 font-medium mt-1">Catat data awal satu proses pembesaran ikan. Pastikan data yang dimasukan akurat untuk pelaporan performa pertumbuhan dan estimasi waktu panen yang optimal.</p>
            </div>
            <div class="flex items-center gap-2">
                <button type="button" @click="showForm = false"
                        class="px-4 py-2 rounded-xl border border-white/20 text-white text-xs font-bold hover:bg-white/10 transition-colors flex items-center gap-2">
                    <i class="fa-solid fa-xmark text-xs"></i> Reset
                </button>
                <button type="button"
                        class="px-4 py-2 rounded-xl bg-sky-500 hover:bg-sky-600 text-white text-xs font-bold shadow-xs transition-all flex items-center gap-2">
                    <i class="fa-solid fa-floppy-disk text-xs"></i> Simpan Batch
                </button>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            <!-- Left 2 Cols: Form Fields -->
            <div class="lg:col-span-2 space-y-5">

                <!-- Section 1: Identitas Batch -->
                <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-xs space-y-4">
                    <div class="flex items-center gap-2.5 text-sm font-bold text-slate-900">
                        <div class="w-8 h-8 rounded-xl bg-[#051B44] text-white flex items-center justify-center">
                            <i class="fa-solid fa-fingerprint text-xs"></i>
                        </div>
                        <span>Identitas Batch</span>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="text-[10px] font-extrabold uppercase tracking-wider text-slate-500 block mb-1">ID PEMBESARAN</label>
                            <input type="text" value="PB-202310-048" readonly
                                   class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs font-extrabold text-slate-500 bg-slate-100 cursor-not-allowed">
                        </div>
                        <div>
                            <label class="text-[10px] font-extrabold uppercase tracking-wider text-slate-500 block mb-1">TANGGAL TEBAR</label>
                            <input type="date"
                                   class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs font-semibold text-slate-700 bg-slate-50/70 focus:bg-white focus:outline-none focus:ring-2 focus:ring-sky-500 transition-all">
                        </div>
                    </div>

                    <p class="text-[10px] text-slate-400 italic flex items-center gap-1">
                        <i class="fa-solid fa-circle-info text-sky-400"></i>
                        ID diberikan secara unik otomatis
                    </p>
                </div>

                <!-- Section 2: Detail Budidaya -->
                <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-xs space-y-4">
                    <div class="flex items-center gap-2.5 text-sm font-bold text-slate-900">
                        <div class="w-8 h-8 rounded-xl bg-[#0284C7] text-white flex items-center justify-center">
                            <i class="fa-solid fa-shrimp text-xs"></i>
                        </div>
                        <span>Detail Budidaya</span>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="text-[10px] font-extrabold uppercase tracking-wider text-slate-500 block mb-1">JENIS IKAN</label>
                            <select class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs font-semibold text-slate-700 bg-slate-50/70 focus:bg-white focus:outline-none focus:ring-2 focus:ring-sky-500 transition-all">
                                <option>Pilih jenis ikan...</option>
                                <option>Gurami</option>
                                <option>Lele</option>
                                <option>Nila</option>
                                <option>Patin</option>
                                <option>Bawal</option>
                            </select>
                        </div>
                        <div>
                            <label class="text-[10px] font-extrabold uppercase tracking-wider text-slate-500 block mb-1">ID KOLAM / FASILITAS</label>
                            <select class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs font-semibold text-slate-700 bg-slate-50/70 focus:bg-white focus:outline-none focus:ring-2 focus:ring-sky-500 transition-all">
                                <option>Pilih lokasi aktif...</option>
                                <option>Kolam A-01</option>
                                <option>Kolam A-02</option>
                                <option>Kolam B-01</option>
                                <option>Kolam B-02</option>
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="text-[10px] font-extrabold uppercase tracking-wider text-slate-500 block mb-1.5">SUMBER BENIH</label>
                        <div class="flex items-center gap-3">
                            <button type="button" @click="sumberBenih = 'hatchery'"
                                    :class="sumberBenih === 'hatchery' ? 'bg-[#051B44] text-white border-transparent shadow-xs' : 'bg-white text-slate-600 border-slate-200 hover:bg-slate-50'"
                                    class="px-4 py-2 rounded-xl border text-xs font-bold transition-all">
                                Hatchery Internal
                            </button>
                            <button type="button" @click="sumberBenih = 'pemasok'"
                                    :class="sumberBenih === 'pemasok' ? 'bg-[#051B44] text-white border-transparent shadow-xs' : 'bg-white text-slate-600 border-slate-200 hover:bg-slate-50'"
                                    class="px-4 py-2 rounded-xl border text-xs font-bold transition-all">
                                Pemasok Eksternal
                            </button>
                        </div>
                    </div>
                </div>

            </div>

            <!-- Right 1 Col: Target Produksi Widget -->
            <div class="space-y-5">
                <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-xs space-y-4">
                    <div class="flex items-center gap-2.5 text-sm font-bold text-slate-900">
                        <i class="fa-solid fa-bullseye text-sky-600"></i>
                        <span>Target Produksi</span>
                    </div>

                    <div class="space-y-3">
                        <div>
                            <label class="text-[10px] font-extrabold uppercase tracking-wider text-slate-500 block mb-1">ESTIMASI BIOMASSA AWAL</label>
                            <input type="text" placeholder="Contoh: 120 kg"
                                   class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs font-semibold text-slate-700 bg-slate-50/70 focus:bg-white focus:outline-none focus:ring-2 focus:ring-sky-500 transition-all">
                        </div>
                        <div>
                            <label class="text-[10px] font-extrabold uppercase tracking-wider text-slate-500 block mb-1">TARGET PANEN</label>
                            <input type="date"
                                   class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs font-semibold text-slate-700 bg-slate-50/70 focus:bg-white focus:outline-none focus:ring-2 focus:ring-sky-500 transition-all">
                        </div>
                        <div>
                            <label class="text-[10px] font-extrabold uppercase tracking-wider text-slate-500 block mb-1">TARGET FCR</label>
                            <input type="text" value="1.20"
                                   class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs font-extrabold text-[#0B2570] bg-sky-50/60 focus:outline-none focus:ring-2 focus:ring-sky-500 transition-all">
                        </div>
                    </div>
                </div>

                <!-- Panduan Pengisian Widget -->
                <div class="bg-sky-50/80 p-5 rounded-2xl border border-sky-200/60 space-y-3">
                    <div class="flex items-center gap-2 text-xs font-bold text-[#0B2570]">
                        <i class="fa-solid fa-lightbulb text-sky-500"></i>
                        <span>Panduan Pengisian</span>
                    </div>
                    <ul class="text-[11px] text-slate-600 space-y-2 leading-relaxed list-none">
                        <li class="flex items-start gap-2">
                            <span class="w-4 h-4 rounded-full bg-sky-200/80 text-sky-700 flex items-center justify-center shrink-0 text-[9px] font-extrabold mt-0.5">1</span>
                            <span>Pastikan biomassa awal sudah dikalkulasi sebelumnya dan digunakan sebagai baseline awal.</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <span class="w-4 h-4 rounded-full bg-sky-200/80 text-sky-700 flex items-center justify-center shrink-0 text-[9px] font-extrabold mt-0.5">2</span>
                            <span>Data yang dimasukan akan disimpan 100% sesuai kolam terdaftar.</span>
                        </li>
                    </ul>
                </div>
            </div>

        </div>
    </div>

    <!-- ========= DIRECTORY / LIST MODE ========= -->

    <!-- 3 Metric KPI Cards Grid -->
    <div x-show="!showForm" class="grid grid-cols-1 md:grid-cols-3 gap-5">
        
        <!-- Card 1: Total Biomassa -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between">
                    <span class="text-[11px] font-extrabold uppercase tracking-wider text-slate-500">TOTAL BIOMASSA</span>
                    <div class="w-9 h-9 rounded-xl bg-[#BEE3F8]/60 text-[#006699] flex items-center justify-center">
                        <i class="fa-solid fa-box-archive text-base"></i>
                    </div>
                </div>
                <div class="mt-3">
                    <h3 class="text-3xl font-extrabold text-slate-900 tracking-tight">12.4 <span class="text-xs font-semibold text-slate-500">Ton</span></h3>
                </div>
            </div>
            <div class="mt-4 flex items-center gap-1.5 text-xs font-semibold text-emerald-600">
                <i class="fa-solid fa-arrow-trend-up"></i>
                <span>+8% dari bulan lalu</span>
            </div>
        </div>

        <!-- Card 2: Rata-rata FCR -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between">
                    <span class="text-[11px] font-extrabold uppercase tracking-wider text-slate-500">RATA-RATA FCR</span>
                    <div class="w-9 h-9 rounded-xl bg-[#BEE3F8]/60 text-[#006699] flex items-center justify-center">
                        <i class="fa-regular fa-clipboard text-base"></i>
                    </div>
                </div>
                <div class="mt-3">
                    <h3 class="text-3xl font-extrabold text-slate-900 tracking-tight">1.42 <span class="text-xs font-semibold text-slate-500">Ratio</span></h3>
                </div>
            </div>
            <div class="mt-4 flex items-center gap-1.5 text-xs font-semibold text-emerald-600">
                <i class="fa-regular fa-circle-check"></i>
                <span>Dalam target optimal</span>
            </div>
        </div>

        <!-- Card 3: Efisiensi Pakan -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between">
                    <span class="text-[11px] font-extrabold uppercase tracking-wider text-slate-500">EFISIENSI PAKAN</span>
                    <div class="w-9 h-9 rounded-xl bg-[#BEE3F8]/60 text-[#006699] flex items-center justify-center">
                        <i class="fa-solid fa-bolt text-base"></i>
                    </div>
                </div>
                <div class="mt-3">
                    <h3 class="text-3xl font-extrabold text-slate-900 tracking-tight">92.5 %</h3>
                </div>
            </div>
            <div class="mt-4">
                <div class="w-full bg-slate-100 h-2 rounded-full overflow-hidden">
                    <div class="bg-[#10B981] h-full rounded-full w-[92.5%]"></div>
                </div>
            </div>
        </div>

    </div>

    <!-- 2-Column Main Section -->
    <div x-show="!showForm" class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <!-- Left 2 Columns: Visualisasi Kolam Grid -->
        <div class="lg:col-span-2 space-y-4">
            <h3 class="text-base font-bold text-slate-900 flex items-center gap-2">
                <i class="fa-solid fa-grip text-sky-600"></i>
                <span>Visualisasi Kolam</span>
            </h3>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                
                <!-- Kolam Card 1: Kolam A-01 -->
                <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs flex flex-col justify-between">
                    <div>
                        <div class="flex items-start justify-between">
                            <div>
                                <h4 class="font-bold text-slate-900 text-sm">Kolam A-01</h4>
                                <span class="text-[10px] text-slate-400 block mt-0.5">Batch: BD-2023-09-01</span>
                            </div>
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-[#C6F6D5] text-[#22543D] uppercase">
                                SEHAT
                            </span>
                        </div>

                        <div class="mt-4 grid grid-cols-2 gap-2 text-xs">
                            <div>
                                <span class="text-[10px] text-slate-400 font-bold uppercase block">BIOMASSA EST.</span>
                                <span class="font-extrabold text-slate-900 text-sm">1,450 kg</span>
                            </div>
                            <div>
                                <span class="text-[10px] text-slate-400 font-bold uppercase block">MASA BUDIDAYA</span>
                                <span class="font-extrabold text-slate-900 text-sm">45 Hari <span class="text-[10px] font-medium text-slate-500">(DOC)</span></span>
                            </div>
                        </div>

                        <div class="mt-3">
                            <span class="text-xs font-semibold text-slate-600 block">FCR: 1.38</span>
                            <div class="mt-1.5 flex items-center justify-between text-[10px] font-bold text-slate-500">
                                <span>TARGET PANEN</span>
                                <span>65%</span>
                            </div>
                            <div class="w-full bg-slate-100 h-1.5 rounded-full overflow-hidden mt-1">
                                <div class="bg-[#0055CC] h-full rounded-full w-[65%]"></div>
                            </div>
                        </div>
                    </div>

                    <a href="{{ route('pembudidaya') }}" class="text-xs font-bold text-[#0055CC] hover:underline text-right block mt-4">
                        Detail &gt;
                    </a>
                </div>

                <!-- Kolam Card 2: Kolam A-02 -->
                <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs flex flex-col justify-between">
                    <div>
                        <div class="flex items-start justify-between">
                            <div>
                                <h4 class="font-bold text-slate-900 text-sm">Kolam A-02</h4>
                                <span class="text-[10px] text-slate-400 block mt-0.5">Batch: BD-2023-09-02</span>
                            </div>
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-[#FEFCBF] text-[#744210] uppercase">
                                PERHATIAN
                            </span>
                        </div>

                        <div class="mt-4 grid grid-cols-2 gap-2 text-xs">
                            <div>
                                <span class="text-[10px] text-slate-400 font-bold uppercase block">BIOMASSA EST.</span>
                                <span class="font-extrabold text-slate-900 text-sm">920 kg</span>
                            </div>
                            <div>
                                <span class="text-[10px] text-slate-400 font-bold uppercase block">MASA BUDIDAYA</span>
                                <span class="font-extrabold text-slate-900 text-sm">32 Hari <span class="text-[10px] font-medium text-slate-500">(DOC)</span></span>
                            </div>
                        </div>

                        <div class="mt-3">
                            <span class="text-xs font-semibold text-rose-600 block">FCR: 1.62</span>
                            <div class="mt-1.5 flex items-center justify-between text-[10px] font-bold text-slate-500">
                                <span>TARGET PANEN</span>
                                <span>43%</span>
                            </div>
                            <div class="w-full bg-slate-100 h-1.5 rounded-full overflow-hidden mt-1">
                                <div class="bg-[#0055CC] h-full rounded-full w-[43%]"></div>
                            </div>
                        </div>
                    </div>

                    <a href="{{ route('pembudidaya') }}" class="text-xs font-bold text-[#0055CC] hover:underline text-right block mt-4">
                        Detail &gt;
                    </a>
                </div>

                <!-- Kolam Card 3: Kolam B-01 -->
                <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs flex flex-col justify-between">
                    <div>
                        <div class="flex items-start justify-between">
                            <div>
                                <h4 class="font-bold text-slate-900 text-sm">Kolam B-01</h4>
                                <span class="text-[10px] text-slate-400 block mt-0.5">Batch: BD-2023-10-01</span>
                            </div>
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-[#C6F6D5] text-[#22543D] uppercase">
                                SEHAT
                            </span>
                        </div>

                        <div class="mt-4 grid grid-cols-2 gap-2 text-xs">
                            <div>
                                <span class="text-[10px] text-slate-400 font-bold uppercase block">BIOMASSA EST.</span>
                                <span class="font-extrabold text-slate-900 text-sm">340 kg</span>
                            </div>
                            <div>
                                <span class="text-[10px] text-slate-400 font-bold uppercase block">MASA BUDIDAYA</span>
                                <span class="font-extrabold text-slate-900 text-sm">12 Hari <span class="text-[10px] font-medium text-slate-500">(DOC)</span></span>
                            </div>
                        </div>

                        <div class="mt-3">
                            <span class="text-xs font-semibold text-slate-600 block">FCR: 1.15</span>
                            <div class="mt-1.5 flex items-center justify-between text-[10px] font-bold text-slate-500">
                                <span>TARGET PANEN</span>
                                <span>15%</span>
                            </div>
                            <div class="w-full bg-slate-100 h-1.5 rounded-full overflow-hidden mt-1">
                                <div class="bg-[#0055CC] h-full rounded-full w-[15%]"></div>
                            </div>
                        </div>
                    </div>

                    <a href="{{ route('pembudidaya') }}" class="text-xs font-bold text-[#0055CC] hover:underline text-right block mt-4">
                        Detail &gt;
                    </a>
                </div>

                <!-- Card 4: Tambah Lokasi Budidaya Card -->
                <div class="border-2 border-dashed border-slate-200 bg-slate-50/50 hover:bg-slate-50 rounded-2xl p-6 flex flex-col items-center justify-center text-center cursor-pointer transition-colors min-h-[180px]">
                    <div class="w-10 h-10 rounded-full bg-slate-200/80 text-slate-600 flex items-center justify-center text-lg mb-2">
                        <i class="fa-solid fa-plus"></i>
                    </div>
                    <h4 class="text-xs font-bold text-slate-800">Tambah Lokasi Budidaya</h4>
                    <p class="text-[10px] text-slate-400 mt-0.5">Inisiasi kolam baru atau pindah batch</p>
                </div>

            </div>
        </div>

        <!-- Right Column: Kualitas Air Real-time -->
        <div class="space-y-4">
            <h3 class="text-base font-bold text-slate-900 flex items-center gap-2">
                <i class="fa-solid fa-droplet text-sky-600"></i>
                <span>Kualitas Air Real-time</span>
            </h3>

            <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs">
                <div class="flex items-center justify-between text-xs text-slate-500">
                    <span>Update Terakhir: 10:45 WIB</span>
                    <button class="text-slate-400 hover:text-slate-600">
                        <i class="fa-solid fa-rotate-right text-xs"></i>
                    </button>
                </div>

                <div class="bg-slate-50 border border-slate-100 rounded-xl p-4 mt-4 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-xl bg-sky-100 text-sky-600 flex items-center justify-center">
                            <i class="fa-solid fa-vial text-sm"></i>
                        </div>
                        <div>
                            <span class="text-[10px] font-extrabold text-slate-400 uppercase tracking-widest block">PH LEVEL</span>
                            <h3 class="text-2xl font-extrabold text-[#0B2570]">7.8</h3>
                        </div>
                    </div>

                    <span class="px-3 py-1 rounded-full text-xs font-extrabold bg-[#C6F6D5] text-[#22543D] uppercase">
                        OPTIMAL
                    </span>
                </div>
            </div>
        </div>

    </div>

</div>
@endsection

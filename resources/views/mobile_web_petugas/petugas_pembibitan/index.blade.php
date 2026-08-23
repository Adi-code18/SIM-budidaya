@extends('mobile_web_petugas.petugas_pembibitan.layout')

@section('title', 'Dashboard Petugas Pembibitan - SIM-BUDIDAYA Mobile')

@section('content')
<div class="p-4 space-y-4" x-data="{
    showAllModal: false,
    filterStatus: 'semua',
    searchQuery: '',

    allBatches: [
        { id: 'Batch-H-042', status: 'SEHAT', statusClass: 'bg-emerald-50 text-emerald-700 border-emerald-200', dotColor: 'bg-emerald-500', detail: 'Pembibitan 4 / Nila Merah', populasi: '250.000 Ekor', umur: '14 Hari (DOC)', icon: 'fa-fish', iconBg: 'bg-slate-100 text-slate-600' },
        { id: 'Batch-H-041', status: 'SEHAT', statusClass: 'bg-emerald-50 text-emerald-700 border-emerald-200', dotColor: 'bg-emerald-500', detail: 'Fase Penyerapan / Umur 28 Hari', populasi: '480.000 Ekor', umur: '28 Hari (DOC)', icon: 'fa-circle-dot', iconBg: 'bg-slate-100 text-slate-600' },
        { id: 'Batch-H-039', status: 'WASPADA', statusClass: 'bg-rose-50 text-rose-700 border-rose-200', dotColor: 'bg-rose-500', detail: 'Fase Menetas / pH + Suhu Tinggi', populasi: '310.000 Ekor', umur: '3 Hari (DOC)', icon: 'fa-triangle-exclamation', iconBg: 'bg-rose-50 text-rose-600' },
        { id: 'Batch-H-038', status: 'SEHAT', statusClass: 'bg-emerald-50 text-emerald-700 border-emerald-200', dotColor: 'bg-emerald-500', detail: 'Inkubasi Telur / Tank A-02', populasi: '500.000 Ekor', umur: '2 Hari (DOC)', icon: 'fa-egg', iconBg: 'bg-sky-50 text-sky-600' },
        { id: 'Batch-H-035', status: 'SEHAT', statusClass: 'bg-emerald-50 text-emerald-700 border-emerald-200', dotColor: 'bg-emerald-500', detail: 'Fase Larva / Kolam Pemijahan 1', populasi: '180.000 Ekor', umur: '10 Hari (DOC)', icon: 'fa-water', iconBg: 'bg-indigo-50 text-indigo-600' },
        { id: 'Batch-H-032', status: 'WASPADA', statusClass: 'bg-amber-50 text-amber-700 border-amber-200', dotColor: 'bg-amber-500', detail: 'Fingerling / Tank L-02', populasi: '210.000 Ekor', umur: '35 Hari (DOC)', icon: 'fa-triangle-exclamation', iconBg: 'bg-amber-50 text-amber-600' }
    ],

    get filteredBatches() {
        return this.allBatches.filter(b => {
            const matchSearch = b.id.toLowerCase().includes(this.searchQuery.toLowerCase()) || 
                                b.detail.toLowerCase().includes(this.searchQuery.toLowerCase());
            const matchStatus = this.filterStatus === 'semua' || b.status.toLowerCase() === this.filterStatus.toLowerCase();
            return matchSearch && matchStatus;
        });
    }
}">

    <!-- Header Section -->
    <div class="text-center pt-2 pb-1 space-y-1">
        <span class="text-[9px] font-extrabold uppercase text-slate-400 tracking-widest block">DASHBOARD</span>
        <h1 class="text-xl font-extrabold text-navy-900">Petugas Pembibitan</h1>
        <p class="text-xs text-slate-500 font-medium">Kelola pembibitan benih/indukan kamu.</p>
    </div>

    <!-- Primary Action Button: Input Batch Baru -->
    <a href="{{ route('petugas.pembibitan.form') }}" 
       class="w-full py-3.5 rounded-2xl bg-navy-800 hover:bg-navy-900 active:scale-[0.99] text-white font-extrabold text-xs flex items-center justify-center gap-2 shadow-md transition-all">
        <i class="fa-solid fa-plus text-xs"></i>
        <span>Input Batch Baru</span>
    </a>

    <!-- Key Metrics Highlight Card -->
    <div class="bg-gradient-to-br from-sky-100 via-sky-50 to-white rounded-3xl p-5 border border-sky-200/80 shadow-xs space-y-4">
        
        <!-- Metric 1: Total Benih Aktif -->
        <div class="space-y-1">
            <div class="flex items-center justify-between">
                <span class="text-[10px] font-extrabold uppercase text-slate-500 tracking-wider">TOTAL BENIH AKTIF</span>
                <span class="px-2 py-0.5 rounded-full text-[9px] font-bold bg-white text-sky-700 border border-sky-200 shadow-2xs">
                    +4.1% bulan ini
                </span>
            </div>
            <h2 class="text-3xl font-extrabold text-navy-900 tracking-tight">2.4M</h2>
        </div>

        <!-- Metrics Grid (FCR Air & Kapasitas Tank) -->
        <div class="grid grid-cols-2 gap-3 pt-3 border-t border-sky-200/60">
            
            <!-- FCR Air -->
            <div class="bg-white/80 rounded-2xl p-3 border border-sky-100/80 space-y-1">
                <div class="flex items-center gap-1.5 text-emerald-600">
                    <i class="fa-solid fa-droplet text-xs"></i>
                    <span class="text-[9px] font-extrabold uppercase text-slate-400">FCR AIR</span>
                </div>
                <h3 class="text-lg font-extrabold text-navy-900">85.2%</h3>
                <span class="text-[9px] text-emerald-600 font-bold block">Target: >80%</span>
            </div>

            <!-- Kapasitas Tank -->
            <div class="bg-white/80 rounded-2xl p-3 border border-sky-100/80 space-y-1">
                <div class="flex items-center gap-1.5 text-navy-800">
                    <i class="fa-solid fa-life-ring text-xs text-sky-600"></i>
                    <span class="text-[9px] font-extrabold uppercase text-slate-400">KAPASITAS TANK</span>
                </div>
                <h3 class="text-lg font-extrabold text-navy-900">42</h3>
                <span class="text-[9px] text-slate-500 font-medium block">Tank Aktif</span>
            </div>

        </div>

    </div>

    <!-- Active Hatchery Batches Section -->
    <div class="space-y-3 pt-1">
        <div class="flex items-center justify-between px-1">
            <h3 class="text-xs font-extrabold text-navy-900">Batch Hatchery Aktif</h3>
            <button type="button" @click="showAllModal = true" class="text-[10px] font-extrabold text-sky-700 uppercase hover:underline tracking-wider">
                LIHAT SEMUA
            </button>
        </div>

        <!-- Cards List -->
        <div class="space-y-2.5">
            
            <!-- Batch Card 1 -->
            <a href="{{ route('petugas.pembibitan.log-pakan', ['batch' => 'Batch-H-042']) }}" 
               class="bg-white rounded-2xl border border-slate-200/90 p-3.5 shadow-xs flex items-center justify-between hover:shadow-md transition-all">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-slate-100 text-slate-600 flex items-center justify-center font-bold text-xs">
                        <i class="fa-solid fa-fish text-slate-500"></i>
                    </div>
                    <div>
                        <div class="flex items-center gap-2">
                            <h4 class="text-xs font-extrabold text-navy-900">Batch-H-042</h4>
                            <span class="px-2 py-0.2 rounded-full text-[9px] font-extrabold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                SEHAT
                            </span>
                        </div>
                        <p class="text-[11px] text-slate-500 font-medium mt-0.5">Pembibitan 4 / Nila Merah</p>
                    </div>
                </div>
                <i class="fa-solid fa-chevron-right text-slate-400 text-xs"></i>
            </a>

            <!-- Batch Card 2 -->
            <a href="{{ route('petugas.pembibitan.log-pakan', ['batch' => 'Batch-H-041']) }}" 
               class="bg-white rounded-2xl border border-slate-200/90 p-3.5 shadow-xs flex items-center justify-between hover:shadow-md transition-all">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-slate-100 text-slate-600 flex items-center justify-center font-bold text-xs">
                        <i class="fa-solid fa-circle-dot text-slate-500 text-xs"></i>
                    </div>
                    <div>
                        <div class="flex items-center gap-2">
                            <h4 class="text-xs font-extrabold text-navy-900">Batch-H-041</h4>
                            <span class="px-2 py-0.2 rounded-full text-[9px] font-extrabold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                SEHAT
                            </span>
                        </div>
                        <p class="text-[11px] text-slate-500 font-medium mt-0.5">Fase Penyerapan / Umur 28 Hari</p>
                    </div>
                </div>
                <i class="fa-solid fa-chevron-right text-slate-400 text-xs"></i>
            </a>

            <!-- Batch Card 3 -->
            <a href="{{ route('petugas.pembibitan.log-pakan', ['batch' => 'Batch-H-039']) }}" 
               class="bg-white rounded-2xl border border-slate-200/90 p-3.5 shadow-xs flex items-center justify-between hover:shadow-md transition-all">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-rose-50 text-rose-600 flex items-center justify-center font-bold text-xs">
                        <i class="fa-solid fa-triangle-exclamation text-rose-500 text-xs"></i>
                    </div>
                    <div>
                        <div class="flex items-center gap-2">
                            <h4 class="text-xs font-extrabold text-navy-900">Batch-H-039</h4>
                            <span class="px-2 py-0.2 rounded-full text-[9px] font-extrabold bg-rose-50 text-rose-700 border border-rose-200">
                                WASPADA
                            </span>
                        </div>
                        <p class="text-[11px] text-slate-500 font-medium mt-0.5">Fase Menetas / pH + Suhu Tinggi</p>
                    </div>
                </div>
                <i class="fa-solid fa-chevron-right text-slate-400 text-xs"></i>
            </a>

        </div>
    </div>

    <!-- Modal Lihat Semua Batch Hatchery Aktif -->
    <div x-show="showAllModal"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs flex items-end sm:items-center justify-center p-0 sm:p-4"
         style="display: none;">
        
        <div class="bg-white rounded-t-3xl sm:rounded-3xl w-full max-w-md max-h-[85vh] flex flex-col shadow-2xl overflow-hidden border border-slate-200"
             @click.away="showAllModal = false">
            
            <!-- Modal Header -->
            <div class="p-4 border-b border-slate-100 flex items-center justify-between bg-slate-50/80">
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-xl bg-navy-800 text-white flex items-center justify-center font-bold text-xs">
                        <i class="fa-solid fa-list-check"></i>
                    </div>
                    <div>
                        <h3 class="text-sm font-extrabold text-navy-900">Semua Batch Hatchery Aktif</h3>
                        <p class="text-[10px] text-slate-500 font-medium">Daftar kelompok benih yang sedang dipantau.</p>
                    </div>
                </div>
                <button type="button" @click="showAllModal = false" class="w-8 h-8 rounded-full bg-slate-200/80 hover:bg-slate-200 text-slate-600 flex items-center justify-center">
                    <i class="fa-solid fa-xmark text-xs"></i>
                </button>
            </div>

            <!-- Search & Filter Bar inside Modal -->
            <div class="p-3.5 bg-white border-b border-slate-100 space-y-2.5">
                <div class="relative">
                    <i class="fa-solid fa-magnifying-glass absolute left-3 top-3 text-slate-400 text-xs"></i>
                    <input type="text" 
                           x-model="searchQuery"
                           placeholder="Cari ID Batch atau jenis ikan..." 
                           class="w-full pl-8 pr-3 py-2 rounded-xl bg-slate-100 border border-slate-200 text-xs font-semibold text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-navy-800 transition-all">
                </div>

                <div class="flex items-center gap-1.5">
                    <button type="button" @click="filterStatus = 'semua'"
                            :class="filterStatus === 'semua' ? 'bg-navy-800 text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200'"
                            class="px-3 py-1 rounded-full text-[10px] font-extrabold transition-all">
                        Semua (<span x-text="allBatches.length"></span>)
                    </button>
                    <button type="button" @click="filterStatus = 'sehat'"
                            :class="filterStatus === 'sehat' ? 'bg-emerald-600 text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200'"
                            class="px-3 py-1 rounded-full text-[10px] font-extrabold transition-all">
                        Sehat
                    </button>
                    <button type="button" @click="filterStatus = 'waspada'"
                            :class="filterStatus === 'waspada' ? 'bg-rose-600 text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200'"
                            class="px-3 py-1 rounded-full text-[10px] font-extrabold transition-all">
                        Waspada
                    </button>
                </div>
            </div>

            <!-- Modal Scrollable Batch List -->
            <div class="p-3.5 space-y-2.5 overflow-y-auto max-h-[50vh]">
                <template x-for="item in filteredBatches" :key="item.id">
                    <a :href="'{{ route('petugas.pembibitan.log-pakan') }}?batch=' + item.id" 
                       class="bg-white rounded-2xl border border-slate-200 p-3 shadow-2xs flex items-center justify-between hover:bg-sky-50/50 transition-all">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-full flex items-center justify-center font-bold text-xs shrink-0" :class="item.iconBg">
                                <i class="fa-solid" :class="item.icon"></i>
                            </div>
                            <div>
                                <div class="flex items-center gap-2">
                                    <h4 class="text-xs font-extrabold text-navy-900" x-text="item.id"></h4>
                                    <span class="px-2 py-0.2 rounded-full text-[9px] font-extrabold border" :class="item.statusClass" x-text="item.status"></span>
                                </div>
                                <p class="text-[10px] text-slate-500 font-medium mt-0.5" x-text="item.detail + ' • ' + item.populasi"></p>
                            </div>
                        </div>
                        <i class="fa-solid fa-chevron-right text-slate-400 text-xs"></i>
                    </a>
                </template>

                <div x-show="filteredBatches.length === 0" class="text-center py-6 text-slate-400 text-xs font-medium">
                    Tidak ada batch hatchery yang sesuai pencarian.
                </div>
            </div>

            <!-- Modal Footer -->
            <div class="p-3 bg-slate-50 border-t border-slate-100 flex items-center justify-between">
                <span class="text-[10px] text-slate-500 font-bold" x-text="'Menampilkan ' + filteredBatches.length + ' Batch'"></span>
                <button type="button" @click="showAllModal = false" class="px-4 py-1.5 rounded-xl bg-navy-800 text-white font-extrabold text-xs hover:bg-navy-900 transition-all">
                    Tutup
                </button>
            </div>

        </div>
    </div>

</div>
@endsection

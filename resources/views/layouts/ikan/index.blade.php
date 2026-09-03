@extends('layouts.app')

@section('title', 'Master Data Jenis Ikan - SIM-BUDIDAYA')

@section('content')
<div class="space-y-6" x-data="ikanComponent()">

    <!-- Top Header Banner -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 bg-white p-6 rounded-2xl border border-slate-200/80 shadow-xs">
        <div>
            <div class="flex items-center gap-2 text-xs font-extrabold uppercase tracking-wider text-[#0284C7] mb-1">
                <i class="fa-solid fa-fish text-sm"></i>
                <span>Master Data &amp; Konfigurasi SOP</span>
            </div>
            <h1 class="text-xl font-extrabold text-slate-900 tracking-tight">Manajemen Jenis Ikan &amp; Pemetaan Pembibitan</h1>
            <p class="text-xs text-slate-500 font-medium mt-0.5">Kelola data spesifikasi ikan dan pemetaan durasi siklus penetasan telur hingga benih matang.</p>
        </div>

        <div class="flex items-center gap-3">
            <button type="button" 
                    @click="openCreateForm()" 
                    class="px-4 py-2.5 rounded-xl bg-[#0284C7] hover:bg-sky-600 active:scale-95 text-white font-extrabold text-xs flex items-center gap-2 shadow-md shadow-sky-600/20 transition-all cursor-pointer">
                <i class="fa-solid fa-plus text-xs"></i>
                <span>Tambah Jenis Ikan</span>
            </button>
        </div>
    </div>

    <!-- 4 KPI Summary Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Card 1: Total Spesies -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs flex items-center gap-4">
            <div class="w-12 h-12 rounded-2xl bg-sky-50 text-[#0284C7] flex items-center justify-center shrink-0">
                <i class="fa-solid fa-fish-fins text-xl"></i>
            </div>
            <div>
                <span class="text-[10px] font-extrabold text-slate-400 uppercase tracking-wider block">TOTAL SPESIES IKAN</span>
                <span class="text-xl font-black text-slate-900 block" x-text="ikans.length"></span>
                <span class="text-[11px] font-bold text-sky-700 mt-0.5 block">Master Data Terdaftar</span>
            </div>
        </div>

        <!-- Card 2: Rata-rata Penetasan -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs flex items-center gap-4">
            <div class="w-12 h-12 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center shrink-0">
                <i class="fa-solid fa-egg text-xl"></i>
            </div>
            <div>
                <span class="text-[10px] font-extrabold text-slate-400 uppercase tracking-wider block">RATA-RATA PENETASAN</span>
                <span class="text-xl font-black text-slate-900 block">{{ $kpis['avgPenetasan'] ?? '3 Hari' }}</span>
                <span class="text-[11px] font-bold text-amber-600 mt-0.5 block">Masa Telur → Larva</span>
            </div>
        </div>

        <!-- Card 3: Rata-rata Pembibitan -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs flex items-center gap-4">
            <div class="w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0">
                <i class="fa-solid fa-seedling text-xl"></i>
            </div>
            <div>
                <span class="text-[10px] font-extrabold text-slate-400 uppercase tracking-wider block">RATA-RATA PEMBIBITAN</span>
                <span class="text-xl font-black text-slate-900 block">{{ $kpis['avgPembibitan'] ?? '21 Hari' }}</span>
                <span class="text-[11px] font-bold text-emerald-600 mt-0.5 block">Larva → Fingerling Matang</span>
            </div>
        </div>

        <!-- Card 4: Total Siklus Hatchery -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs flex items-center gap-4">
            <div class="w-12 h-12 rounded-2xl bg-indigo-50 text-indigo-600 flex items-center justify-center shrink-0">
                <i class="fa-solid fa-timeline text-xl"></i>
            </div>
            <div>
                <span class="text-[10px] font-extrabold text-slate-400 uppercase tracking-wider block">TOTAL SIKLUS HATCHERY</span>
                <span class="text-xl font-black text-slate-900 block">{{ $kpis['totalSiklus'] ?? '24 Hari' }}</span>
                <span class="text-[11px] font-bold text-indigo-600 mt-0.5 block">Standar SOP Lengkap</span>
            </div>
        </div>
    </div>

    <!-- Form Tambah / Edit Jenis Ikan (Slide Down) -->
    <div x-show="showForm" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 -translate-y-4"
         x-transition:enter-end="opacity-100 translate-y-0"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 translate-y-0"
         x-transition:leave-end="opacity-0 -translate-y-4"
         class="bg-white rounded-2xl border-2 border-sky-400/80 p-6 shadow-xl space-y-6"
         style="display: none;">
        
        <div class="flex items-center justify-between pb-4 border-b border-slate-100">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-[#0284C7] text-white flex items-center justify-center">
                    <i class="fa-solid fa-sliders text-base"></i>
                </div>
                <div>
                    <h2 class="text-base font-extrabold text-slate-900" x-text="formMode === 'create' ? 'Tambah Jenis Ikan & Pemetaan Siklus' : 'Edit Data Spesies Ikan'"></h2>
                    <p class="text-xs text-slate-500 font-medium">Tentukan parameter nama varietas serta pemetaan waktu tiap fase pembibitan.</p>
                </div>
            </div>
            <button type="button" @click="showForm = false" class="text-slate-400 hover:text-slate-600 text-lg p-1">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>

        <form @submit.prevent="submitIkan()" class="space-y-6">
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                <!-- Field 1: Nama Ikan -->
                <div class="space-y-1.5">
                    <label class="text-[11px] font-extrabold uppercase tracking-wider text-slate-600 block">
                        NAMA JENIS / SPESIES IKAN <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" 
                           x-model="form.nama_ikan" 
                           placeholder="Contoh: Ikan Nila Hitam Super" 
                           required
                           class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs font-semibold text-slate-800 bg-slate-50/70 focus:bg-white focus:outline-none focus:ring-2 focus:ring-sky-500 transition-all">
                    <p class="text-[10px] text-slate-400 font-medium">Varietas atau komoditas budidaya.</p>
                </div>

                <!-- Field 2: Durasi Penetasan -->
                <div class="space-y-1.5">
                    <label class="text-[11px] font-extrabold uppercase tracking-wider text-slate-600 block">
                        DURASI MASA PENETASAN (HARI) <span class="text-rose-500">*</span>
                    </label>
                    <div class="flex items-center gap-2">
                        <button type="button" @click="form.durasi_penetasan = Math.max(1, Number(form.durasi_penetasan) - 1)" class="w-9 h-9 rounded-xl border border-slate-200 bg-white text-slate-500 hover:bg-slate-50 flex items-center justify-center font-bold text-sm">−</button>
                        <input type="number" 
                               x-model="form.durasi_penetasan" 
                               min="1" 
                               max="90"
                               required
                               onkeydown="if(event.key === '-' || event.key === 'e' || event.key === 'E') event.preventDefault()"
                               @input="if(form.durasi_penetasan !== '' && Number(form.durasi_penetasan) < 1) form.durasi_penetasan = 1"
                               class="flex-1 px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs font-extrabold text-center text-amber-700 bg-amber-50/50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-amber-500 transition-all">
                        <button type="button" @click="form.durasi_penetasan = Number(form.durasi_penetasan) + 1" class="w-9 h-9 rounded-xl border border-slate-200 bg-white text-slate-500 hover:bg-slate-50 flex items-center justify-center font-bold text-sm">+</button>
                    </div>
                    <p class="text-[10px] text-slate-400 font-medium">Masa inkubasi dari butir telur hingga menetas.</p>
                </div>

                <!-- Field 3: Durasi Pembibitan -->
                <div class="space-y-1.5">
                    <label class="text-[11px] font-extrabold uppercase tracking-wider text-slate-600 block">
                        DURASI MASA PEMBIBITAN (HARI) <span class="text-rose-500">*</span>
                    </label>
                    <div class="flex items-center gap-2">
                        <button type="button" @click="form.durasi_pembibitan = Math.max(1, Number(form.durasi_pembibitan) - 1)" class="w-9 h-9 rounded-xl border border-slate-200 bg-white text-slate-500 hover:bg-slate-50 flex items-center justify-center font-bold text-sm">−</button>
                        <input type="number" 
                               x-model="form.durasi_pembibitan" 
                               min="1" 
                               max="180"
                               required
                               onkeydown="if(event.key === '-' || event.key === 'e' || event.key === 'E') event.preventDefault()"
                               @input="if(form.durasi_pembibitan !== '' && Number(form.durasi_pembibitan) < 1) form.durasi_pembibitan = 1"
                               class="flex-1 px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs font-extrabold text-center text-emerald-700 bg-emerald-50/50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500 transition-all">
                        <button type="button" @click="form.durasi_pembibitan = Number(form.durasi_pembibitan) + 1" class="w-9 h-9 rounded-xl border border-slate-200 bg-white text-slate-500 hover:bg-slate-50 flex items-center justify-center font-bold text-sm">+</button>
                    </div>
                    <p class="text-[10px] text-slate-400 font-medium">Masa pemeliharaan larva hingga ukuran siap tebar.</p>
                </div>
            </div>

            <!-- Visual Pemetaan Tahapan Pembibitan Realtime -->
            <div class="bg-slate-50/90 rounded-2xl p-5 border border-slate-200/80 space-y-4">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-extrabold text-slate-800 uppercase tracking-wider flex items-center gap-2">
                        <i class="fa-solid fa-route text-sky-600"></i>
                        <span>Pemetaan Alur Siklus Pembibitan (<span x-text="form.nama_ikan || 'Spesies Baru'"></span>)</span>
                    </span>
                    <span class="px-3 py-1 rounded-full bg-[#051B44] text-white text-[11px] font-extrabold">
                        Total Siklus: <span x-text="(Number(form.durasi_penetasan || 0) + Number(form.durasi_pembibitan || 0)) + ' Hari'"></span>
                    </span>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                    <!-- Tahap 1: Telur & Penetasan -->
                    <div class="bg-white p-4 rounded-xl border-2 border-amber-200 shadow-xs space-y-2 relative overflow-hidden">
                        <div class="flex items-center justify-between">
                            <span class="px-2 py-0.5 rounded-md bg-amber-100 text-amber-800 text-[10px] font-black uppercase">FASE 1: TELUR</span>
                            <span class="text-xs font-extrabold text-amber-700" x-text="'0 - ' + (form.durasi_penetasan || 3) + ' Hari'"></span>
                        </div>
                        <h4 class="text-xs font-extrabold text-slate-900">Masa Inkubasi &amp; Penetasan</h4>
                        <p class="text-[11px] text-slate-500 font-medium leading-relaxed">
                            Telur berada di kolam penetasan hingga menetas menjadi larva aktif.
                        </p>
                    </div>

                    <!-- Tahap 2: Larva & Pendederan Awal -->
                    <div class="bg-white p-4 rounded-xl border-2 border-sky-200 shadow-xs space-y-2 relative overflow-hidden">
                        <div class="flex items-center justify-between">
                            <span class="px-2 py-0.5 rounded-md bg-sky-100 text-sky-800 text-[10px] font-black uppercase">FASE 2: LARVA</span>
                            <span class="text-xs font-extrabold text-sky-700" x-text="'Hari ke-' + (Number(form.durasi_penetasan || 3) + 1)"></span>
                        </div>
                        <h4 class="text-xs font-extrabold text-slate-900">Pemberian Pakan Awal</h4>
                        <p class="text-[11px] text-slate-500 font-medium leading-relaxed">
                            Penyerapan kuning telur, pemberian pakan alami, dan pembentukan organ tubuh.
                        </p>
                    </div>

                    <!-- Tahap 3: Benih Matang / Siap Pindah -->
                    <div class="bg-white p-4 rounded-xl border-2 border-emerald-200 shadow-xs space-y-2 relative overflow-hidden">
                        <div class="flex items-center justify-between">
                            <span class="px-2 py-0.5 rounded-md bg-emerald-100 text-emerald-800 text-[10px] font-black uppercase">FASE 3: BENIH MATANG</span>
                            <span class="text-xs font-extrabold text-emerald-700" x-text="'Hari ke-' + (Number(form.durasi_penetasan || 3) + Number(form.durasi_pembibitan || 21))"></span>
                        </div>
                        <h4 class="text-xs font-extrabold text-slate-900">Fingerling Siap Pembesaran</h4>
                        <p class="text-[11px] text-slate-500 font-medium leading-relaxed">
                            Benih mencapai bobot standar dan siap dipindahkan ke siklus kolam pembesaran.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="flex items-center justify-end gap-3 pt-2">
                <button type="button" 
                        @click="showForm = false" 
                        class="px-4 py-2.5 rounded-xl border border-slate-200 text-slate-600 hover:bg-slate-50 font-bold text-xs transition-colors cursor-pointer">
                    Batal
                </button>
                <button type="submit" 
                        :disabled="isSubmitting"
                        class="px-5 py-2.5 rounded-xl bg-[#0284C7] hover:bg-sky-600 active:scale-95 text-white font-extrabold text-xs shadow-md shadow-sky-600/20 transition-all flex items-center gap-2 cursor-pointer">
                    <i class="fa-solid fa-check text-xs"></i>
                    <span x-text="isSubmitting ? 'Menyimpan...' : (formMode === 'create' ? 'Simpan Jenis Ikan' : 'Perbarui Spesies')"></span>
                </button>
            </div>

        </form>

    </div>

    <!-- Data Table Card -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
        
        <!-- Table Toolbar -->
        <div class="p-5 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h3 class="text-sm font-extrabold text-slate-900">Daftar Varietas &amp; Durasi SOP Ikan</h3>
                <p class="text-xs text-slate-500 font-medium">Data acuan standar yang digunakan dalam kalkulasi estimasi proses pembibitan.</p>
            </div>
            
            <div class="flex items-center gap-3">
                <div class="relative w-full sm:w-64">
                    <i class="fa-solid fa-magnifying-glass absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                    <input type="text" 
                           x-model="searchQuery" 
                           placeholder="Cari jenis ikan..." 
                           class="w-full pl-9 pr-3.5 py-2 rounded-xl border border-slate-200 text-xs font-semibold text-slate-700 bg-slate-50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-sky-500 transition-all">
                </div>
            </div>
        </div>

        <!-- Table List -->
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50/70 border-b border-slate-200/80 text-[10px] font-extrabold text-slate-400 uppercase tracking-wider">
                        <th class="py-4 px-6">KODE &amp; SPESIES IKAN</th>
                        <th class="py-4 px-6">DURASI PENETASAN</th>
                        <th class="py-4 px-6">DURASI PEMBIBITAN</th>
                        <th class="py-4 px-6">TOTAL ESTIMASI HATCHERY</th>
                        <th class="py-4 px-6 text-right">AKSI</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-xs font-medium text-slate-700">
                    
                    <template x-if="filteredIkans.length === 0">
                        <tr>
                            <td colspan="5" class="py-12 text-center text-slate-400 text-xs font-medium">
                                <i class="fa-solid fa-fish text-3xl text-slate-300 block mb-2"></i>
                                Belum ada data jenis ikan yang terdaftar.<br>
                                <span class="text-[11px] text-slate-400">Klik tombol <strong>Tambah Jenis Ikan</strong> untuk menambahkan data baru.</span>
                            </td>
                        </tr>
                    </template>

                    <template x-for="item in filteredIkans" :key="item.id_ikan">
                        <tr class="hover:bg-slate-50/70 transition-colors">
                            <td class="py-4 px-6">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 rounded-xl bg-sky-100 text-[#0284C7] flex items-center justify-center font-extrabold text-xs shrink-0">
                                        <i class="fa-solid fa-fish"></i>
                                    </div>
                                    <div>
                                        <span class="font-extrabold text-slate-900 block" x-text="item.nama_ikan"></span>
                                        <span class="text-[10px] text-slate-400 font-medium" x-text="'ID: #IK-' + String(item.id_ikan).padStart(4, '0')"></span>
                                    </div>
                                </div>
                            </td>
                            <td class="py-4 px-6">
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-extrabold bg-amber-50 text-amber-700 border border-amber-200/60">
                                    <i class="fa-solid fa-egg text-[10px]"></i>
                                    <span x-text="item.durasi_penetasan + ' Hari'"></span>
                                </span>
                            </td>
                            <td class="py-4 px-6">
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-extrabold bg-emerald-50 text-emerald-700 border border-emerald-200/60">
                                    <i class="fa-solid fa-seedling text-[10px]"></i>
                                    <span x-text="item.durasi_pembibitan + ' Hari'"></span>
                                </span>
                            </td>
                            <td class="py-4 px-6">
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[11px] font-black bg-indigo-50 text-indigo-700 border border-indigo-200/60">
                                    <i class="fa-solid fa-clock text-[10px]"></i>
                                    <span x-text="(Number(item.durasi_penetasan) + Number(item.durasi_pembibitan)) + ' Hari'"></span>
                                </span>
                            </td>
                            <td class="py-4 px-6 text-right">
                                <div class="relative inline-block text-left" x-data="{ open: false }">
                                    <button @click="open = !open" @click.away="open = false" 
                                            class="w-8 h-8 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-600 inline-flex items-center justify-center transition-colors cursor-pointer">
                                        <i class="fa-solid fa-ellipsis-vertical text-sm"></i>
                                    </button>
                                    <div x-show="open" 
                                         x-transition:enter="transition ease-out duration-100" 
                                         x-transition:enter-start="transform opacity-0 scale-95" 
                                         x-transition:enter-end="transform opacity-100 scale-100" 
                                         x-transition:leave="transition ease-in duration-75" 
                                         x-transition:leave-start="transform opacity-100 scale-100" 
                                         x-transition:leave-end="transform opacity-0 scale-95" 
                                         class="absolute right-0 top-full mt-1 w-44 rounded-xl bg-white border border-slate-200 shadow-xl py-1.5 z-50 text-left origin-top-right"
                                         style="display: none;">
                                        <button @click="open = false; openEditForm(item)" class="w-full px-3.5 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-50 flex items-center gap-2.5 transition-colors cursor-pointer">
                                            <i class="fa-solid fa-pen-to-square text-sky-600 w-4"></i>
                                            <span>Edit Spesies</span>
                                        </button>
                                        <div class="my-1 border-t border-slate-100"></div>
                                        <button @click="open = false; confirmDelete(item)" class="w-full px-3.5 py-2 text-xs font-semibold text-rose-600 hover:bg-rose-50 flex items-center gap-2.5 transition-colors cursor-pointer">
                                            <i class="fa-solid fa-trash-can text-rose-600 w-4"></i>
                                            <span>Hapus Spesies</span>
                                        </button>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>

    </div>

    <!-- Notification Toast -->
    <div x-show="showToast"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 -translate-y-4 scale-95"
         x-transition:enter-end="opacity-100 translate-y-0 scale-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 translate-y-0 scale-100"
         x-transition:leave-end="opacity-0 -translate-y-4 scale-95"
         class="fixed top-6 right-6 z-50 max-w-sm rounded-2xl shadow-xl border p-4 flex items-center gap-3 backdrop-blur-md bg-[#051B44] text-white border-sky-500/50 shadow-sky-950/20"
         style="display: none;">
        <div class="w-8 h-8 rounded-xl bg-emerald-500 text-white flex items-center justify-center shrink-0">
            <i class="fa-solid fa-check text-sm"></i>
        </div>
        <div class="flex-1 text-xs font-bold leading-snug" x-text="toastMessage"></div>
        <button @click="showToast = false" class="text-white/70 hover:text-white transition-colors">
            <i class="fa-solid fa-xmark text-xs"></i>
        </button>
    </div>

</div>
@endsection

@push('scripts')
<script>
function ikanComponent() {
    return {
        showForm: false,
        formMode: 'create',
        isSubmitting: false,
        searchQuery: '',
        showToast: false,
        toastMessage: '',

        form: {
            id_ikan: null,
            nama_ikan: '',
            durasi_penetasan: 3,
            durasi_pembibitan: 21,
            id_batch: ''
        },

        ikans: {!! json_encode($ikans ?? []) !!},

        get filteredIkans() {
            if (!this.searchQuery.trim()) {
                return this.ikans;
            }
            const q = this.searchQuery.toLowerCase();
            return this.ikans.filter(item => 
                (item.nama_ikan && item.nama_ikan.toLowerCase().includes(q))
            );
        },

        openCreateForm() {
            this.formMode = 'create';
            this.resetForm();
            this.showForm = true;
            window.scrollTo({ top: 0, behavior: 'smooth' });
        },

        openEditForm(item) {
            this.formMode = 'edit';
            this.form = {
                id_ikan: item.id_ikan,
                nama_ikan: item.nama_ikan,
                durasi_penetasan: item.durasi_penetasan,
                durasi_pembibitan: item.durasi_pembibitan,
                id_batch: item.id_batch || ''
            };
            this.showForm = true;
            window.scrollTo({ top: 0, behavior: 'smooth' });
        },

        resetForm() {
            this.form = {
                id_ikan: null,
                nama_ikan: '',
                durasi_penetasan: 3,
                durasi_pembibitan: 21,
                id_batch: ''
            };
        },

        triggerToast(msg) {
            this.toastMessage = msg;
            this.showToast = true;
            setTimeout(() => { this.showToast = false; }, 4000);
        },

        async submitIkan() {
            if (!this.form.nama_ikan.trim()) {
                alert('Nama jenis ikan wajib diisi!');
                return;
            }
            if (!this.form.durasi_penetasan || Number(this.form.durasi_penetasan) < 1) {
                alert('Durasi penetasan harus minimal 1 hari!');
                return;
            }
            if (!this.form.durasi_pembibitan || Number(this.form.durasi_pembibitan) < 1) {
                alert('Durasi pembibitan harus minimal 1 hari!');
                return;
            }

            this.isSubmitting = true;

            if (this.formMode === 'edit') {
                try {
                    const res = await fetch('/ikan/' + this.form.id_ikan, {
                        method: 'PUT',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({
                            nama_ikan: this.form.nama_ikan,
                            durasi_penetasan: Number(this.form.durasi_penetasan),
                            durasi_pembibitan: Number(this.form.durasi_pembibitan),
                            id_batch: this.form.id_batch || null
                        })
                    });
                    const data = await res.json();
                    if (res.ok && data.success) {
                        const idx = this.ikans.findIndex(i => i.id_ikan === this.form.id_ikan);
                        if (idx !== -1) {
                            this.ikans[idx].nama_ikan = this.form.nama_ikan;
                            this.ikans[idx].durasi_penetasan = Number(this.form.durasi_penetasan);
                            this.ikans[idx].durasi_pembibitan = Number(this.form.durasi_pembibitan);
                            this.ikans[idx].id_batch = this.form.id_batch || null;
                        }
                        this.showForm = false;
                        this.resetForm();
                        this.triggerToast(data.message || 'Data jenis ikan berhasil diperbarui!');
                    } else {
                        alert(data.message || 'Gagal memperbarui data jenis ikan.');
                    }
                } catch (e) {
                    alert('Terjadi kesalahan saat memperbarui data.');
                } finally {
                    this.isSubmitting = false;
                }
                return;
            }

            // Create Mode
            try {
                const res = await fetch('{{ route('ikan.store') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        nama_ikan: this.form.nama_ikan,
                        durasi_penetasan: Number(this.form.durasi_penetasan),
                        durasi_pembibitan: Number(this.form.durasi_pembibitan),
                        id_batch: this.form.id_batch || null
                    })
                });
                const data = await res.json();
                if (res.ok && data.success) {
                    this.ikans.unshift(data.ikan);
                    this.showForm = false;
                    this.resetForm();
                    this.triggerToast(data.message || 'Data jenis ikan baru berhasil disimpan!');
                } else {
                    alert(data.message || 'Gagal menambahkan jenis ikan.');
                }
            } catch (e) {
                alert('Terjadi kesalahan saat menyimpan data.');
            } finally {
                this.isSubmitting = false;
            }
        },

        async confirmDelete(item) {
            if (!confirm(`Apakah Anda yakin ingin menghapus data spesies '${item.nama_ikan}'?`)) {
                return;
            }

            try {
                const res = await fetch('/ikan/' + item.id_ikan, {
                    method: 'DELETE',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                });
                const data = await res.json();
                if (res.ok && data.success) {
                    this.ikans = this.ikans.filter(i => i.id_ikan !== item.id_ikan);
                    this.triggerToast(data.message || 'Data jenis ikan berhasil dihapus.');
                } else {
                    alert(data.message || 'Gagal menghapus data jenis ikan.');
                }
            } catch (e) {
                alert('Terjadi kesalahan saat menghapus data.');
            }
        }
    };
}
</script>
@endpush

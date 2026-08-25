@extends('layouts.app')

@section('title', 'Manajemen Pembudidaya & Kolam - SIM-BUDIDAYA')

@section('content')
<div class="space-y-6" x-data='{
    modalOpen: false,
    detailModalOpen: false,
    editModalOpen: false,
    search: "",
    statusFilter: "semua",
    komoditasFilter: "semua",
    selectedKolam: null,
    
    newKolam: {
        id: "",
        lokasi: "",
        pembudidaya: "",
        initials: "",
        jenisIkan: "Ikan Nila Hitam",
        tebarBenih: "23 Aug 2026",
        populasi: 20000,
        status: "Optimal"
    },

    kolams: {!! isset($kolams) && count($kolams) > 0 ? json_encode($kolams) : json_encode([
        [
            'id' => "Kolam A1",
            'lokasi' => "Beton / Pembesaran",
            'pembudidaya' => "Budi Santoso",
            'initials' => "BS",
            'colorClass' => "bg-sky-100 text-sky-700",
            'jenisIkan' => "Ikan Nila Hitam Super",
            'tebarBenih' => "12 Mei 2026",
            'populasi' => "5,000",
            'populasiRaw' => 5000,
            'status' => "Optimal",
            'statusClass' => "bg-emerald-100 text-emerald-700",
            'dotClass' => "bg-emerald-500"
        ]
    ]) !!},

    get filteredKolams() {
        return this.kolams.filter(item => {
            const matchSearch = !this.search || 
                item.id.toLowerCase().includes(this.search.toLowerCase()) ||
                item.pembudidaya.toLowerCase().includes(this.search.toLowerCase()) ||
                item.lokasi.toLowerCase().includes(this.search.toLowerCase()) ||
                item.jenisIkan.toLowerCase().includes(this.search.toLowerCase());
            
            const matchStatus = this.statusFilter === "semua" || 
                (this.statusFilter === "optimal" && item.status.toLowerCase().includes("optimal")) ||
                (this.statusFilter === "siap_panen" && item.status.toLowerCase().includes("panen")) ||
                (this.statusFilter === "perhatian" && item.status.toLowerCase().includes("perhatian"));

            const matchKomoditas = this.komoditasFilter === "semua" ||
                item.jenisIkan.toLowerCase().includes(this.komoditasFilter.toLowerCase());

            return matchSearch && matchStatus && matchKomoditas;
        });
    },

    openDetail(item) {
        this.selectedKolam = item;
        this.detailModalOpen = true;
    },

    openEdit(item) {
        this.selectedKolam = JSON.parse(JSON.stringify(item));
        this.editModalOpen = true;
    },

    saveEdit() {
        if (!this.selectedKolam) return;
        const index = this.kolams.findIndex(k => k.id === this.selectedKolam.id);
        if (index !== -1) {
            this.kolams[index] = { ...this.selectedKolam };
        }
        this.editModalOpen = false;
    },

    deleteKolam(item) {
        if (confirm("Apakah Anda yakin ingin menghapus data kolam " + item.id + "?")) {
            this.kolams = this.kolams.filter(k => k.id !== item.id);
        }
    },

    submitNewKolam() {
        if (!this.newKolam.id || !this.newKolam.pembudidaya) {
            alert("ID Kolam dan Pembudidaya wajib diisi!");
            return;
        }
        const names = this.newKolam.pembudidaya.trim().split(" ");
        const initials = names.length > 1 ? (names[0][0] + names[1][0]).toUpperCase() : names[0].substring(0, 2).toUpperCase();
        
        let statusClass = "bg-emerald-100 text-emerald-700";
        let dotClass = "bg-emerald-500";
        if (this.newKolam.status === "Siap Panen") {
            statusClass = "bg-amber-100 text-amber-700";
            dotClass = "bg-amber-500";
        } else if (this.newKolam.status === "Perlu Perhatian") {
            statusClass = "bg-rose-100 text-rose-700";
            dotClass = "bg-rose-500";
        }

        const formattedPopulasi = Number(this.newKolam.populasi || 0).toLocaleString("en-US");

        this.kolams.unshift({
            id: this.newKolam.id.toUpperCase(),
            lokasi: this.newKolam.lokasi || "Sektor Utama",
            pembudidaya: this.newKolam.pembudidaya,
            initials: initials,
            colorClass: "bg-sky-100 text-sky-700",
            jenisIkan: this.newKolam.jenisIkan,
            tebarBenih: this.newKolam.tebarBenih,
            populasi: formattedPopulasi,
            populasiRaw: Number(this.newKolam.populasi || 0),
            status: this.newKolam.status,
            statusClass: statusClass,
            dotClass: dotClass
        });

        this.modalOpen = false;
        this.newKolam = {
            id: "",
            lokasi: "",
            pembudidaya: "",
            initials: "",
            jenisIkan: "Ikan Nila Hitam",
            tebarBenih: "23 Aug 2026",
            populasi: 20000,
            status: "Optimal"
        };
    }
}'>

    <!-- Breadcrumb & Back Navigation -->
    <div>
        <a href="{{ route('pembesaran') }}" class="inline-flex items-center gap-2 text-xs font-bold text-sky-600 hover:text-sky-800 transition-colors mb-2">
            <i class="fa-solid fa-arrow-left"></i>
            <span>Kembali ke Manajemen Pembesaran</span>
        </a>
        <div class="flex items-center gap-2 text-xs font-medium text-slate-400">
            <a href="{{ route('pembesaran') }}" class="hover:underline">Pembesaran</a>
            <i class="fa-solid fa-chevron-right text-[10px]"></i>
            <span class="text-slate-600 font-semibold">Detail Kolam &amp; Pembudidaya</span>
        </div>
    </div>

    <!-- Header & Primary Action -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">Detail Kolam &amp; Pembudidaya</h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-1 font-medium">Pengelolaan data kolam, populasi benih ikan, dan lokasi pembudidaya dalam unit pembesaran.</p>
        </div>
        <div>
            <button @click="modalOpen = true" class="px-5 py-2.5 rounded-xl bg-[#031B4E] hover:bg-sky-900 text-white font-bold text-xs sm:text-sm shadow-md shadow-sky-950/20 transition-all flex items-center justify-center gap-2">
                <i class="fa-solid fa-circle-plus text-sm"></i>
                <span>Tambah Kolam / Pembudidaya</span>
            </button>
        </div>
    </div>

    <!-- Overview Stats Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs flex items-center gap-4">
            <div class="w-12 h-12 rounded-2xl bg-sky-50 text-sky-600 flex items-center justify-center shrink-0">
                <i class="fa-solid fa-water text-xl"></i>
            </div>
            <div>
                <p class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400">Total Benih / Ekor</p>
                <h3 class="text-2xl font-extrabold text-slate-900 mt-0.5">1,240,500 <span class="text-xs font-semibold text-slate-500">ekor</span></h3>
            </div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs flex items-center gap-4">
            <div class="w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0">
                <i class="fa-solid fa-chart-line text-xl"></i>
            </div>
            <div>
                <p class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400">Tingkat Keberhasilan</p>
                <h3 class="text-2xl font-extrabold text-slate-900 mt-0.5">94.2% <span class="text-xs font-bold text-emerald-600">High Yield</span></h3>
            </div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs flex items-center gap-4">
            <div class="w-12 h-12 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center shrink-0">
                <i class="fa-solid fa-hourglass-half text-xl"></i>
            </div>
            <div>
                <p class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400">Kolam Siap Panen</p>
                <h3 class="text-2xl font-extrabold text-slate-900 mt-0.5">42 <span class="text-xs font-bold text-amber-600">Kolam</span></h3>
            </div>
        </div>
    </div>

    <!-- Search & Filter Bar -->
    <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-xs flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div class="relative flex-1 max-w-md">
            <i class="fa-solid fa-magnifying-glass absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></i>
            <input type="text" 
                   x-model="search"
                   placeholder="Cari ID Kolam, Pembudidaya, atau Lokasi..." 
                   class="w-full pl-10 pr-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs sm:text-sm font-medium text-slate-700 focus:bg-white focus:outline-none focus:ring-2 focus:ring-sky-500 transition-all">
        </div>
        <div class="flex items-center gap-3">
            <select x-model="statusFilter" class="px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-bold text-slate-700 focus:bg-white focus:outline-none focus:ring-2 focus:ring-sky-500 cursor-pointer">
                <option value="semua">Semua Status</option>
                <option value="optimal">Sehat / Optimal</option>
                <option value="siap_panen">Siap Panen</option>
                <option value="perhatian">Perlu Perhatian</option>
            </select>
            <select x-model="komoditasFilter" class="px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-bold text-slate-700 focus:bg-white focus:outline-none focus:ring-2 focus:ring-sky-500 cursor-pointer">
                <option value="semua">Semua Komoditas</option>
                <option value="nila">Ikan Nila</option>
                <option value="lele">Ikan Lele</option>
                <option value="gurame">Ikan Gurame</option>
            </select>
        </div>
    </div>

    <!-- Kolam Table Container -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-visible">
        <div class="overflow-x-auto overflow-y-visible">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50/70 border-b border-slate-200/80 text-[10px] font-extrabold text-slate-400 uppercase tracking-wider">
                        <th class="py-4 px-6">ID KOLAM &amp; LOKASI</th>
                        <th class="py-4 px-6">PEMBUDIDAYA</th>
                        <th class="py-4 px-6">JENIS IKAN</th>
                        <th class="py-4 px-6">TEBAR BENIH</th>
                        <th class="py-4 px-6">POPULASI (EKOR)</th>
                        <th class="py-4 px-6">STATUS</th>
                        <th class="py-4 px-6 text-right">AKSI</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-xs font-medium text-slate-700">
                    
                    <template x-for="item in filteredKolams" :key="item.id">
                        <tr class="hover:bg-slate-50/70 transition-colors">
                            <td class="py-4 px-6">
                                <div class="font-extrabold text-slate-900 text-xs" x-text="item.id"></div>
                                <div class="text-[11px] text-slate-400 font-normal" x-text="item.lokasi"></div>
                            </td>
                            <td class="py-4 px-6">
                                <div class="flex items-center gap-2.5">
                                    <div class="w-7 h-7 rounded-full flex items-center justify-center font-bold text-xs shrink-0" :class="item.colorClass" x-text="item.initials"></div>
                                    <span class="font-bold text-slate-800" x-text="item.pembudidaya"></span>
                                </div>
                            </td>
                            <td class="py-4 px-6 font-bold text-slate-800" x-text="item.jenisIkan"></td>
                            <td class="py-4 px-6 text-xs text-slate-500 font-semibold" x-text="item.tebarBenih"></td>
                            <td class="py-4 px-6 font-extrabold text-slate-900" x-text="item.populasi"></td>
                            <td class="py-4 px-6">
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-extrabold" :class="item.statusClass">
                                    <span class="w-1.5 h-1.5 rounded-full" :class="item.dotClass"></span>
                                    <span x-text="item.status"></span>
                                </span>
                            </td>
                            <td class="py-4 px-6 text-right">
                                <!-- 3-Dots Ellipsis Dropdown Action Menu -->
                                <div class="relative inline-block text-left" x-data="{ open: false }">
                                    <button @click="open = !open" @click.away="open = false"
                                            class="w-8 h-8 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-600 inline-flex items-center justify-center transition-colors">
                                        <i class="fa-solid fa-ellipsis-vertical text-sm"></i>
                                    </button>

                                    <div x-show="open"
                                         x-transition:enter="transition ease-out duration-100"
                                         x-transition:enter-start="transform opacity-0 scale-95"
                                         x-transition:enter-end="transform opacity-100 scale-100"
                                         x-transition:leave="transition ease-in duration-75"
                                         x-transition:leave-start="transform opacity-100 scale-100"
                                         x-transition:leave-end="transform opacity-0 scale-95"
                                         class="absolute right-0 top-full mt-1 w-48 rounded-xl bg-white border border-slate-200 shadow-xl py-1.5 z-50 text-left origin-top-right">
                                        
                                        <button @click="open = false; openDetail(item)" class="w-full px-3.5 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-50 flex items-center gap-2.5">
                                            <i class="fa-solid fa-eye text-sky-600 w-4"></i>
                                            <span>Detail Kolam</span>
                                        </button>
                                        
                                        <button @click="open = false; openEdit(item)" class="w-full px-3.5 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-50 flex items-center gap-2.5">
                                            <i class="fa-solid fa-pen-to-square text-amber-600 w-4"></i>
                                            <span>Edit Data</span>
                                        </button>

                                        <div class="my-1 border-t border-slate-100"></div>

                                        <button @click="open = false; deleteKolam(item)" class="w-full px-3.5 py-2 text-xs font-semibold text-red-600 hover:bg-red-50 flex items-center gap-2.5">
                                            <i class="fa-solid fa-trash-can text-red-500 w-4"></i>
                                            <span>Hapus Data</span>
                                        </button>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    </template>

                    <!-- Empty state -->
                    <tr x-show="filteredKolams.length === 0">
                        <td colspan="7" class="py-10 text-center text-slate-400">
                            <i class="fa-solid fa-inbox text-3xl mb-2 block"></i>
                            <span class="text-xs font-semibold">Tidak ada data kolam / pembudidaya yang sesuai pencarian.</span>
                        </td>
                    </tr>

                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal Tambah Kolam -->
    <div x-show="modalOpen" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-95"
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm"
         style="display: none;">
        
        <div class="bg-white rounded-2xl max-w-lg w-full p-6 shadow-2xl space-y-5 border border-slate-200">
            <div class="flex items-center justify-between border-b border-slate-100 pb-4">
                <h3 class="text-base font-extrabold text-slate-900">Tambah Kolam Baru</h3>
                <button @click="modalOpen = false" class="text-slate-400 hover:text-slate-600"><i class="fa-solid fa-xmark text-lg"></i></button>
            </div>
            
            <form @submit.prevent="submitNewKolam()" class="space-y-4 text-xs font-medium">
                <div>
                    <label class="block text-[10px] font-extrabold uppercase text-slate-500 mb-1">Kode / ID Kolam *</label>
                    <input type="text" x-model="newKolam.id" placeholder="Contoh: KOLAM-NW-05" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-sky-500 text-slate-800 font-semibold focus:outline-none">
                </div>
                <div>
                    <label class="block text-[10px] font-extrabold uppercase text-slate-500 mb-1">Sektor / Lokasi</label>
                    <input type="text" x-model="newKolam.lokasi" placeholder="Contoh: Sektor Barat - Magelang" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-sky-500 text-slate-800 font-semibold focus:outline-none">
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-[10px] font-extrabold uppercase text-slate-500 mb-1">Jenis Ikan</label>
                        <select x-model="newKolam.jenisIkan" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-sky-500 text-slate-800 font-semibold focus:outline-none cursor-pointer">
                            <option value="Ikan Nila Hitam">Ikan Nila Hitam</option>
                            <option value="Ikan Lele Sangkuriang">Ikan Lele Sangkuriang</option>
                            <option value="Ikan Gurame Super">Ikan Gurame Super</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-[10px] font-extrabold uppercase text-slate-500 mb-1">Populasi Benih (Ekor)</label>
                        <input type="number" x-model="newKolam.populasi" placeholder="20000" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-sky-500 text-slate-800 font-semibold focus:outline-none">
                    </div>
                </div>
                <div>
                    <label class="block text-[10px] font-extrabold uppercase text-slate-500 mb-1">Penanggung Jawab / Pembudidaya *</label>
                    <input type="text" x-model="newKolam.pembudidaya" placeholder="Nama Pembudidaya" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-sky-500 text-slate-800 font-semibold focus:outline-none">
                </div>
                <div>
                    <label class="block text-[10px] font-extrabold uppercase text-slate-500 mb-1">Status Kolam</label>
                    <select x-model="newKolam.status" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-sky-500 text-slate-800 font-semibold focus:outline-none cursor-pointer">
                        <option value="Optimal">Optimal</option>
                        <option value="Siap Panen">Siap Panen</option>
                        <option value="Perlu Perhatian">Perlu Perhatian</option>
                    </select>
                </div>
                <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
                    <button type="button" @click="modalOpen = false" class="px-4 py-2 rounded-xl bg-slate-100 text-slate-700 font-bold hover:bg-slate-200 transition-colors">Batal</button>
                    <button type="submit" class="px-5 py-2 rounded-xl bg-[#031B4E] text-white font-extrabold hover:bg-navy-900 shadow-md shadow-sky-950/20 transition-all">Simpan Kolam</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Detail Kolam -->
    <div x-show="detailModalOpen" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-95"
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm"
         style="display: none;">
        
        <div class="bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl space-y-5 border border-slate-200" x-show="selectedKolam">
            <div class="flex items-center justify-between border-b border-slate-100 pb-4">
                <div>
                    <span class="text-[10px] font-extrabold uppercase text-slate-400 tracking-wider">Detail Data Kolam</span>
                    <h3 class="text-lg font-extrabold text-slate-900" x-text="selectedKolam?.id"></h3>
                </div>
                <button @click="detailModalOpen = false" class="text-slate-400 hover:text-slate-600"><i class="fa-solid fa-xmark text-lg"></i></button>
            </div>

            <div class="space-y-3 text-xs">
                <div class="p-3.5 bg-slate-50 rounded-xl border border-slate-100 flex items-center justify-between">
                    <span class="text-slate-500 font-semibold">Penanggung Jawab</span>
                    <span class="font-extrabold text-slate-900" x-text="selectedKolam?.pembudidaya"></span>
                </div>
                <div class="p-3.5 bg-slate-50 rounded-xl border border-slate-100 flex items-center justify-between">
                    <span class="text-slate-500 font-semibold">Lokasi Sektor</span>
                    <span class="font-extrabold text-slate-900" x-text="selectedKolam?.lokasi"></span>
                </div>
                <div class="p-3.5 bg-slate-50 rounded-xl border border-slate-100 flex items-center justify-between">
                    <span class="text-slate-500 font-semibold">Jenis Ikan Komoditas</span>
                    <span class="font-extrabold text-slate-900" x-text="selectedKolam?.jenisIkan"></span>
                </div>
                <div class="p-3.5 bg-slate-50 rounded-xl border border-slate-100 flex items-center justify-between">
                    <span class="text-slate-500 font-semibold">Populasi Benih</span>
                    <span class="font-extrabold text-slate-900" x-text="selectedKolam?.populasi + ' Ekor'"></span>
                </div>
                <div class="p-3.5 bg-slate-50 rounded-xl border border-slate-100 flex items-center justify-between">
                    <span class="text-slate-500 font-semibold">Status Operasional</span>
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[10px] font-extrabold" :class="selectedKolam?.statusClass" x-text="selectedKolam?.status"></span>
                </div>
            </div>

            <div class="flex items-center justify-end pt-2">
                <button type="button" @click="detailModalOpen = false" class="px-5 py-2 rounded-xl bg-[#031B4E] text-white font-extrabold text-xs hover:bg-navy-900">Tutup</button>
            </div>
        </div>
    </div>

    <!-- Modal Edit Kolam -->
    <div x-show="editModalOpen" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-95"
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm"
         style="display: none;">
        
        <div class="bg-white rounded-2xl max-w-lg w-full p-6 shadow-2xl space-y-5 border border-slate-200" x-show="selectedKolam">
            <div class="flex items-center justify-between border-b border-slate-100 pb-4">
                <h3 class="text-base font-extrabold text-slate-900">Edit Data Kolam</h3>
                <button @click="editModalOpen = false" class="text-slate-400 hover:text-slate-600"><i class="fa-solid fa-xmark text-lg"></i></button>
            </div>
            
            <form @submit.prevent="saveEdit()" class="space-y-4 text-xs font-medium">
                <div>
                    <label class="block text-[10px] font-extrabold uppercase text-slate-500 mb-1">Kode / ID Kolam</label>
                    <input type="text" x-model="selectedKolam.id" readonly class="w-full px-3.5 py-2.5 bg-slate-100 border border-slate-200 rounded-xl text-slate-500 font-extrabold cursor-not-allowed">
                </div>
                <div>
                    <label class="block text-[10px] font-extrabold uppercase text-slate-500 mb-1">Penanggung Jawab / Pembudidaya</label>
                    <input type="text" x-model="selectedKolam.pembudidaya" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-sky-500 text-slate-800 font-semibold focus:outline-none">
                </div>
                <div>
                    <label class="block text-[10px] font-extrabold uppercase text-slate-500 mb-1">Sektor / Lokasi</label>
                    <input type="text" x-model="selectedKolam.lokasi" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-sky-500 text-slate-800 font-semibold focus:outline-none">
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-[10px] font-extrabold uppercase text-slate-500 mb-1">Jenis Ikan</label>
                        <select x-model="selectedKolam.jenisIkan" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-sky-500 text-slate-800 font-semibold focus:outline-none cursor-pointer">
                            <option value="Ikan Nila Hitam">Ikan Nila Hitam</option>
                            <option value="Ikan Lele Sangkuriang">Ikan Lele Sangkuriang</option>
                            <option value="Ikan Gurame Super">Ikan Gurame Super</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-[10px] font-extrabold uppercase text-slate-500 mb-1">Status</label>
                        <select x-model="selectedKolam.status" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-sky-500 text-slate-800 font-semibold focus:outline-none cursor-pointer">
                            <option value="Optimal">Optimal</option>
                            <option value="Siap Panen">Siap Panen</option>
                            <option value="Perlu Perhatian">Perlu Perhatian</option>
                        </select>
                    </div>
                </div>

                <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
                    <button type="button" @click="editModalOpen = false" class="px-4 py-2 rounded-xl bg-slate-100 text-slate-700 font-bold hover:bg-slate-200 transition-colors">Batal</button>
                    <button type="submit" class="px-5 py-2 rounded-xl bg-[#031B4E] text-white font-extrabold hover:bg-navy-900 shadow-md shadow-sky-950/20 transition-all">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection

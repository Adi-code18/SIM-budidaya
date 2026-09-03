@extends('layouts.app')

@section('title', 'Manajemen Mitra - SIM-BUDIDAYA')

@section('content')
<div class="space-y-6" x-data='{
    showForm: false,
    formMode: "create",
    filterTipe: "",
    filterWilayah: "",
    searchQuery: "",
    toastMessage: "",
    showToast: false,

    mitras: {!! json_encode($mitras ?? []) !!},

    form: {
        id: "",
        nama: "",
        tipeKey: "distributor",
        tipe: "Distributor",
        alamat: "",
        lat: "-6.200000",
        lng: "106.816666",
        kontak: "",
        email: "",
        image: ""
    },

    openCreateForm() {
        this.formMode = "create";
        this.form = {
            id: "MTR-2024-" + String(Math.floor(100 + Math.random() * 900)),
            nama: "",
            tipeKey: "distributor",
            tipe: "Distributor",
            alamat: "",
            lat: "-6.200000",
            lng: "106.816666",
            kontak: "",
            email: "",
            image: ""
        };
        this.showForm = true;
        this.$nextTick(() => initMitraMap(this.form.lat, this.form.lng, false));
    },

    openEditForm(mitra) {
        this.formMode = "edit";
        this.form = JSON.parse(JSON.stringify(mitra));
        this.showForm = true;
        this.$nextTick(() => initMitraMap(this.form.lat, this.form.lng, false));
    },

    openViewForm(mitra) {
        this.formMode = "view";
        this.form = JSON.parse(JSON.stringify(mitra));
        this.showForm = true;
        this.$nextTick(() => initMitraMap(this.form.lat, this.form.lng, true));
    },

    saveForm() {
        if (this.formMode === "view") return;

        if (!this.form.nama.trim()) {
            alert("Nama mitra wajib diisi!");
            return;
        }
        if (!this.form.alamat.trim()) {
            alert("Alamat lengkap wajib diisi!");
            return;
        }

        const tipeMap = {
            distributor: "Distributor",
            supplier: "Supplier Frozen Food",
            restoran: "Restoran",
            pasar: "Pasar Tradisional",
            eksportir: "Eksportir"
        };
        this.form.tipe = tipeMap[this.form.tipeKey] || "Distributor";

        if (this.formMode === "create") {
            this.mitras.unshift({ ...this.form });
            this.notify("Data Mitra baru berhasil ditambahkan!");
        } else if (this.formMode === "edit") {
            const index = this.mitras.findIndex(m => m.id === this.form.id);
            if (index !== -1) {
                this.mitras[index] = { ...this.form };
            }
            this.notify("Data Mitra berhasil diperbarui!");
        }
        this.showForm = false;
    },

    deleteMitra(mitra) {
        if (confirm("Apakah Anda yakin ingin menghapus data mitra \"" + mitra.nama + "\"?")) {
            this.mitras = this.mitras.filter(m => m.id !== mitra.id);
            this.notify("Data mitra \"" + mitra.nama + "\" berhasil dihapus!");
        }
    },

    notify(msg) {
        this.toastMessage = msg;
        this.showToast = true;
        setTimeout(() => { this.showToast = false; }, 3500);
    },

    get filteredMitras() {
        return this.mitras.filter(m => {
            const matchTipe = !this.filterTipe || m.tipeKey === this.filterTipe;
            const matchWilayah = !this.filterWilayah || (m.wilayah && m.wilayah === this.filterWilayah) || (m.alamat && m.alamat.toLowerCase().includes(this.filterWilayah.toLowerCase()));
            const matchSearch = !this.searchQuery || m.nama.toLowerCase().includes(this.searchQuery.toLowerCase()) || m.id.toLowerCase().includes(this.searchQuery.toLowerCase()) || m.alamat.toLowerCase().includes(this.searchQuery.toLowerCase());
            return matchTipe && matchWilayah && matchSearch;
        });
    }
}' >
    <!-- Toast Notification -->
    <div x-show="showToast"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 translate-y-2 scale-95"
         x-transition:enter-end="opacity-100 translate-y-0 scale-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 translate-y-0 scale-100"
         x-transition:leave-end="opacity-0 translate-y-2 scale-95"
         class="fixed top-5 right-5 z-50 bg-[#051B44] text-white px-5 py-3 rounded-xl shadow-2xl border border-sky-400/30 flex items-center gap-3">
        <div class="w-7 h-7 rounded-lg bg-emerald-500/20 text-emerald-400 flex items-center justify-center shrink-0">
            <i class="fa-solid fa-circle-check text-base"></i>
        </div>
        <span class="text-xs font-semibold" x-text="toastMessage"></span>
    </div>

    <!-- Subtitle & Page Title Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-extrabold text-[#0B2570] tracking-tight">Manajemen Mitra</h1>
            <p class="text-xs text-slate-500 font-medium mt-0.5">Kelola hubungan dan distribusi hasil budidaya ke mitra strategis.</p>
        </div>
        <div>
            <button @click="if (showForm) { showForm = false; } else { openCreateForm(); }" 
                    class="px-4 py-2 rounded-xl bg-[#051B44] hover:bg-navy-900 text-white font-bold text-xs shadow-xs transition-all flex items-center gap-2">
                <i class="fa-solid" :class="showForm ? 'fa-list' : 'fa-plus'"></i>
                <span x-text="showForm ? 'Lihat Daftar Mitra' : 'Tambah Mitra Baru'"></span>
            </button>
        </div>
    </div>

    <!-- Input / Edit / View Form Section (Shown when showForm is true) -->
    <div x-show="showForm" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 -translate-y-2"
         x-transition:enter-end="opacity-100 translate-y-0"
         class="space-y-4">
        
        <div class="flex items-center justify-between">
            <h2 class="text-lg font-extrabold text-slate-900 tracking-tight"
                x-text="formMode === 'create' ? 'Input Manajemen Mitra Baru' : (formMode === 'edit' ? 'Edit Data Mitra — ' + form.id : 'View Lengkap Data Mitra — ' + form.id)"></h2>
            
            <span x-show="formMode === 'view'" class="px-3 py-1 rounded-full text-xs font-extrabold bg-amber-100 text-amber-800 border border-amber-300 flex items-center gap-1.5">
                <i class="fa-solid fa-lock"></i> Mode View Lengkap (Disabled)
            </span>
            <span x-show="formMode === 'edit'" class="px-3 py-1 rounded-full text-xs font-extrabold bg-sky-100 text-sky-800 border border-sky-300 flex items-center gap-1.5">
                <i class="fa-solid fa-pen"></i> Mode Edit Data
            </span>
        </div>

        <!-- Mode View Info Banner -->
        <div x-show="formMode === 'view'" class="p-3.5 rounded-xl bg-amber-50 border border-amber-200 text-amber-900 text-xs flex items-center justify-between gap-3">
            <div class="flex items-center gap-2.5">
                <i class="fa-solid fa-shield-halved text-amber-600 text-base shrink-0"></i>
                <span>Data mitra ditampilkan dalam <strong>mode lihat (read-only)</strong>. Semua bidang formulir dinonaktifkan agar data tidak sengaja diubah.</span>
            </div>
            <button @click="formMode = 'edit'; initMitraMap(form.lat, form.lng, false)" class="px-3 py-1.5 rounded-lg bg-amber-600 hover:bg-amber-700 text-white font-bold text-[11px] shrink-0 transition-colors flex items-center gap-1.5">
                <i class="fa-solid fa-pen-to-square"></i> Ubah Ke Mode Edit
            </button>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
            
            <!-- Left 5 Cols: Interactive Leaflet Map & Overview Card -->
            <div class="lg:col-span-5 relative overflow-hidden rounded-2xl bg-[#051B44] min-h-[420px] flex flex-col justify-end p-6 text-white shadow-xs">
                
                <!-- Interactive Leaflet Map Background -->
                <div id="mitraMapPicker" class="absolute inset-0 z-0 h-full w-full"></div>
                
                <!-- Dark Gradient Overlay for text readability -->
                <div class="absolute inset-0 bg-gradient-to-t from-[#051B44] via-[#051B44]/65 to-transparent z-10 pointer-events-none"></div>

                <!-- Registration Card Info Overlay -->
                <div class="relative z-20 space-y-2">
                    <div class="w-10 h-10 rounded-xl bg-[#0B2570] text-white flex items-center justify-center shadow-md">
                        <i class="fa-solid fa-map-location-dot text-lg"></i>
                    </div>
                    <h3 class="text-xl font-extrabold text-white" x-text="formMode === 'create' ? 'Registrasi Mitra' : (formMode === 'edit' ? 'Lokasi & Data Mitra' : 'Detail Lokasi Mitra')"></h3>
                    <p class="text-xs text-sky-100/80 font-medium leading-relaxed">
                        Daftarkan entitas mitra rantai pasok baru. Pastikan titik koordinat akurat untuk keperluan perhitungan biaya distribusi dan estimasi waktu tiba (ETA).
                    </p>

                    <div class="pt-2">
                        <span x-show="formMode !== 'view'" class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[10px] font-extrabold bg-sky-500/20 text-sky-300 border border-sky-400/30">
                            <i class="fa-solid fa-hand-pointer"></i> Klik lokasi pada peta untuk mengambil Lat &amp; Lng otomatis
                        </span>
                        <span x-show="formMode === 'view'" class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[10px] font-extrabold bg-amber-500/20 text-amber-300 border border-amber-400/30">
                            <i class="fa-solid fa-lock"></i> Peta dalam mode terkunci (View Only)
                        </span>
                    </div>
                </div>
            </div>

            <!-- Right 7 Cols: Mitra Input Fields Form -->
            <div class="lg:col-span-7 bg-white p-6 sm:p-8 rounded-2xl border border-slate-200/80 shadow-xs space-y-5">
                
                <form action="#" method="POST" @submit.prevent="saveForm()" class="space-y-5">
                    
                    <!-- Section 1: Identitas Mitra -->
                    <div class="space-y-3">
                        <div class="flex items-center gap-2 text-xs font-bold text-slate-800 pb-2 border-b border-slate-100">
                            <i class="fa-solid fa-store text-sky-600"></i>
                            <span>Identitas Mitra</span>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                            <div class="sm:col-span-2">
                                <label class="text-[10px] font-extrabold uppercase tracking-wider text-slate-500 block mb-1">NAMA MITRA</label>
                                <input type="text" 
                                       x-model="form.nama"
                                       :disabled="formMode === 'view'"
                                       placeholder="PT. Global Akuakultur..." 
                                       :class="formMode === 'view' ? 'bg-slate-100 text-slate-500 cursor-not-allowed border-slate-200 font-bold' : 'bg-slate-50/70 focus:bg-white text-slate-700 font-semibold focus:ring-sky-500'"
                                       class="w-full px-3.5 py-2.5 rounded-xl border text-xs focus:outline-none focus:ring-2 transition-all">
                            </div>
                            <div>
                                <label class="text-[10px] font-extrabold uppercase tracking-wider text-slate-500 block mb-1">ID MITRA (AUTO)</label>
                                <input type="text" 
                                       x-model="form.id" 
                                       readonly 
                                       disabled
                                       class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs font-extrabold text-slate-500 bg-slate-100 cursor-not-allowed">
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="text-[10px] font-extrabold uppercase tracking-wider text-slate-500 block mb-1">NO. TELEPON / WHATSAPP</label>
                                <div class="flex items-center rounded-xl border overflow-hidden transition-all"
                                     :class="formMode === 'view' ? 'bg-slate-100 border-slate-200' : 'border-slate-200 bg-slate-50/70 focus-within:bg-white focus-within:ring-2 focus-within:ring-sky-500'">
                                    <span class="px-3 py-2.5 text-xs font-bold text-slate-500 bg-slate-100/90 border-r border-slate-200 shrink-0 flex items-center gap-1.5">
                                        <span>🇮🇩</span>
                                        <span>+62</span>
                                    </span>
                                    <input type="tel" 
                                           x-model="form.kontak"
                                           :disabled="formMode === 'view'"
                                           placeholder="812-3456-7890 / 081234567890" 
                                           :class="formMode === 'view' ? 'text-slate-500 cursor-not-allowed font-bold' : 'text-slate-700 font-semibold'"
                                           class="w-full px-3.5 py-2.5 text-xs bg-transparent border-0 focus:outline-none">
                                </div>
                            </div>
                            <div>
                                <label class="text-[10px] font-extrabold uppercase tracking-wider text-slate-500 block mb-1">EMAIL MITRA</label>
                                <input type="email" 
                                       x-model="form.email"
                                       :disabled="formMode === 'view'"
                                       placeholder="mitra@perusahaan.com" 
                                       :class="formMode === 'view' ? 'bg-slate-100 text-slate-500 cursor-not-allowed border-slate-200 font-bold' : 'bg-slate-50/70 focus:bg-white text-slate-700 font-semibold focus:ring-sky-500'"
                                       class="w-full px-3.5 py-2.5 rounded-xl border text-xs focus:outline-none focus:ring-2 transition-all">
                            </div>
                        </div>
                    </div>

                    <!-- Section 2: Peran Operasional -->
                    <div class="space-y-3">
                        <div class="flex items-center gap-2 text-xs font-bold text-slate-800 pb-2 border-b border-slate-100">
                            <i class="fa-solid fa-briefcase text-sky-600"></i>
                            <span>Peran Operasional</span>
                        </div>

                        <div>
                            <label class="text-[10px] font-extrabold uppercase tracking-wider text-slate-500 block mb-1.5">TIPE MITRA</label>
                            <div class="flex flex-wrap items-center gap-2 p-1.5 bg-slate-100 rounded-xl text-xs font-bold" :class="formMode === 'view' ? 'opacity-80 pointer-events-none' : ''">
                                <button type="button" 
                                        @click="if(formMode !== 'view') form.tipeKey = 'distributor'" 
                                        :disabled="formMode === 'view'"
                                        :class="form.tipeKey === 'distributor' ? 'bg-[#051B44] text-white shadow-xs' : 'text-slate-600 hover:text-slate-900'" 
                                        class="px-3 py-2 rounded-lg transition-all text-center flex-1 min-w-[100px]">
                                    Distributor
                                </button>
                                <button type="button" 
                                        @click="if(formMode !== 'view') form.tipeKey = 'supplier'" 
                                        :disabled="formMode === 'view'"
                                        :class="form.tipeKey === 'supplier' ? 'bg-[#051B44] text-white shadow-xs' : 'text-slate-600 hover:text-slate-900'" 
                                        class="px-3 py-2 rounded-lg transition-all text-center flex-1 min-w-[100px]">
                                    Supplier
                                </button>
                                <button type="button" 
                                        @click="if(formMode !== 'view') form.tipeKey = 'restoran'" 
                                        :disabled="formMode === 'view'"
                                        :class="form.tipeKey === 'restoran' ? 'bg-[#051B44] text-white shadow-xs' : 'text-slate-600 hover:text-slate-900'" 
                                        class="px-3 py-2 rounded-lg transition-all text-center flex-1 min-w-[100px]">
                                    Restoran
                                </button>
                                <button type="button" 
                                        @click="if(formMode !== 'view') form.tipeKey = 'pasar'" 
                                        :disabled="formMode === 'view'"
                                        :class="form.tipeKey === 'pasar' ? 'bg-[#051B44] text-white shadow-xs' : 'text-slate-600 hover:text-slate-900'" 
                                        class="px-3 py-2 rounded-lg transition-all text-center flex-1 min-w-[100px]">
                                    Pasar
                                </button>
                                <button type="button" 
                                        @click="if(formMode !== 'view') form.tipeKey = 'eksportir'" 
                                        :disabled="formMode === 'view'"
                                        :class="form.tipeKey === 'eksportir' ? 'bg-[#051B44] text-white shadow-xs' : 'text-slate-600 hover:text-slate-900'" 
                                        class="px-3 py-2 rounded-lg transition-all text-center flex-1 min-w-[100px]">
                                    Eksportir
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Section 3: Data Geospatial -->
                    <div class="space-y-3">
                        <div class="flex items-center gap-2 text-xs font-bold text-slate-800 pb-2 border-b border-slate-100">
                            <i class="fa-solid fa-location-crosshairs text-sky-600"></i>
                            <span>Data Geospatial</span>
                        </div>

                        <div>
                            <label class="text-[10px] font-extrabold uppercase tracking-wider text-slate-500 block mb-1">ALAMAT LENGKAP</label>
                            <textarea rows="2" 
                                      x-model="form.alamat"
                                      :disabled="formMode === 'view'"
                                      placeholder="Jl. Raya Pelabuhan No. 45..." 
                                      :class="formMode === 'view' ? 'bg-slate-100 text-slate-500 cursor-not-allowed border-slate-200 font-bold' : 'bg-slate-50/70 focus:bg-white text-slate-700 font-semibold focus:ring-sky-500'"
                                      class="w-full px-3.5 py-2.5 rounded-xl border text-xs focus:outline-none focus:ring-2 transition-all"></textarea>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="text-[10px] font-extrabold uppercase tracking-wider text-slate-500 block mb-1">LATITUDE</label>
                                <input type="text" 
                                       id="latInput" 
                                       x-model="form.lat"
                                       readonly 
                                       :disabled="formMode === 'view'"
                                       :class="formMode === 'view' ? 'bg-slate-100 text-slate-500 cursor-not-allowed border-slate-200' : 'bg-sky-50/60 text-[#0B2570] cursor-pointer border-slate-200'"
                                       class="w-full px-3.5 py-2.5 rounded-xl border text-xs font-extrabold"
                                       title="Ambil otomatis dengan klik pada peta">
                            </div>
                            <div>
                                <label class="text-[10px] font-extrabold uppercase tracking-wider text-slate-500 block mb-1">LONGITUDE</label>
                                <input type="text" 
                                       id="lngInput" 
                                       x-model="form.lng"
                                       readonly 
                                       :disabled="formMode === 'view'"
                                       :class="formMode === 'view' ? 'bg-slate-100 text-slate-500 cursor-not-allowed border-slate-200' : 'bg-sky-50/60 text-[#0B2570] cursor-pointer border-slate-200'"
                                       class="w-full px-3.5 py-2.5 rounded-xl border text-xs font-extrabold"
                                       title="Ambil otomatis dengan klik pada peta">
                            </div>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
                        <button type="button" 
                                @click="showForm = false" 
                                class="px-5 py-2 rounded-xl border border-slate-200 text-xs font-bold text-slate-600 hover:bg-slate-50 transition-colors">
                            <span x-text="formMode === 'view' ? 'Tutup' : 'Batal'"></span>
                        </button>
                        <button x-show="formMode !== 'view'"
                                type="submit" 
                                class="px-5 py-2 rounded-xl bg-[#051B44] hover:bg-navy-900 text-white font-bold text-xs shadow-xs transition-all flex items-center gap-2">
                            <span x-text="formMode === 'create' ? 'Simpan Data Baru' : 'Simpan Perubahan'"></span>
                            <i class="fa-solid fa-circle-check text-xs"></i>
                        </button>
                        <button x-show="formMode === 'view'"
                                type="button"
                                @click="formMode = 'edit'; initMitraMap(form.lat, form.lng, false)"
                                class="px-5 py-2 rounded-xl bg-amber-600 hover:bg-amber-700 text-white font-bold text-xs shadow-xs transition-all flex items-center gap-2">
                            <span>Edit Data Ini</span>
                            <i class="fa-solid fa-pen-to-square text-xs"></i>
                        </button>
                    </div>

                </form>

            </div>

        </div>

    </div>

    <!-- Filters & Metric Header Row (Directory Mode) -->
    <div x-show="!showForm" class="grid grid-cols-1 md:grid-cols-4 gap-4">
        
        <!-- Search Box -->
        <div class="bg-white p-3.5 rounded-2xl border border-slate-200/80 shadow-xs flex items-center gap-3">
            <div class="w-8 h-8 rounded-xl bg-slate-100 text-slate-500 flex items-center justify-center shrink-0">
                <i class="fa-solid fa-magnifying-glass text-xs"></i>
            </div>
            <div class="flex-1">
                <span class="text-[9px] font-extrabold text-slate-400 uppercase tracking-widest block">CARI MITRA</span>
                <input type="text" x-model="searchQuery" placeholder="Nama, ID, atau alamat..." class="w-full bg-transparent text-xs font-bold text-slate-800 focus:outline-none mt-0.5 placeholder:font-medium placeholder:text-slate-400">
            </div>
        </div>

        <!-- Filter 1: Tipe Mitra -->
        <div class="bg-white p-3.5 rounded-2xl border border-slate-200/80 shadow-xs flex items-center gap-3">
            <div class="w-8 h-8 rounded-xl bg-slate-100 text-slate-500 flex items-center justify-center shrink-0">
                <i class="fa-solid fa-sliders text-xs"></i>
            </div>
            <div class="flex-1">
                <span class="text-[9px] font-extrabold text-slate-400 uppercase tracking-widest block">TIPE MITRA</span>
                <select x-model="filterTipe" class="w-full bg-transparent text-xs font-bold text-slate-800 focus:outline-none cursor-pointer mt-0.5">
                    <option value="">Semua Tipe</option>
                    <option value="restoran">Restoran</option>
                    <option value="supplier">Supplier Frozen Food</option>
                    <option value="pasar">Pasar Tradisional</option>
                    <option value="eksportir">Eksportir</option>
                    <option value="distributor">Distributor</option>
                </select>
            </div>
        </div>

        <!-- Filter 2: Wilayah -->
        <div class="bg-white p-3.5 rounded-2xl border border-slate-200/80 shadow-xs flex items-center gap-3">
            <div class="w-8 h-8 rounded-xl bg-slate-100 text-slate-500 flex items-center justify-center shrink-0">
                <i class="fa-solid fa-location-dot text-xs"></i>
            </div>
            <div class="flex-1">
                <span class="text-[9px] font-extrabold text-slate-400 uppercase tracking-widest block">WILAYAH</span>
                <select x-model="filterWilayah" class="w-full bg-transparent text-xs font-bold text-slate-800 focus:outline-none cursor-pointer mt-0.5">
                    <option value="">Seluruh Indonesia</option>
                    <option value="jakarta">DKI Jakarta</option>
                    <option value="jabar">Jawa Barat</option>
                    <option value="jateng">Jawa Tengah</option>
                </select>
            </div>
        </div>

        <!-- Metric Card: Total Mitra Aktif -->
        <div class="bg-white p-3.5 rounded-2xl border border-slate-200/80 shadow-xs flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl bg-[#BEE3F8]/60 text-[#006699] flex items-center justify-center shrink-0">
                    <i class="fa-solid fa-users text-sm"></i>
                </div>
                <div>
                    <span class="text-[9px] font-extrabold text-slate-400 uppercase tracking-widest block">TOTAL MITRA AKTIF</span>
                    <div class="flex items-baseline gap-2 mt-0.5">
                        <h3 class="text-lg font-extrabold text-slate-900" x-text="mitras.length"></h3>
                        <span class="text-[10px] font-extrabold text-emerald-600">+5 bln ini</span>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <!-- Mitra Directory Table Card (Directory Mode) -->
    <div x-show="!showForm" class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50/60 border-b border-slate-100 text-[10px] font-extrabold text-slate-400 uppercase tracking-wider">
                        <th class="py-3.5 px-6">INFO MITRA</th>
                        <th class="py-3.5 px-6">TIPE</th>
                        <th class="py-3.5 px-6">LOKASI &amp; ALAMAT</th>
                        <th class="py-3.5 px-6 text-right">AKSI</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-xs font-medium text-slate-700">
                    
                    <template x-for="(mitra, index) in filteredMitras" :key="mitra.id">
                        <tr class="hover:bg-slate-50/50 transition-colors">
                            <td class="py-4 px-6">
                                <div class="flex items-center gap-3">
                                    <template x-if="mitra.image">
                                        <img :src="mitra.image" 
                                             :alt="mitra.nama" 
                                             class="w-10 h-10 rounded-xl object-cover border border-slate-200">
                                    </template>
                                    <template x-if="!mitra.image">
                                        <div class="w-10 h-10 rounded-xl bg-sky-100 text-sky-700 flex items-center justify-center shrink-0 border border-sky-200 font-bold text-sm">
                                            <i class="fa-solid fa-store"></i>
                                        </div>
                                    </template>
                                    <div>
                                        <h4 class="font-extrabold text-[#0055CC] text-sm cursor-pointer hover:underline" @click="openViewForm(mitra)" x-text="mitra.nama"></h4>
                                        <span class="text-[10px] text-slate-400 block font-normal" x-text="'ID: ' + mitra.id"></span>
                                    </div>
                                </div>
                            </td>
                            <td class="py-4 px-6">
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold uppercase"
                                      :class="{
                                          'bg-[#E0F2FE] text-[#0284C7]': mitra.tipeKey === 'restoran' || mitra.tipe.toLowerCase().includes('restoran'),
                                          'bg-[#C6F6D5] text-[#22543D]': mitra.tipeKey === 'supplier' || mitra.tipe.toLowerCase().includes('supplier'),
                                          'bg-[#E2E8F0] text-[#475569]': mitra.tipeKey === 'pasar' || mitra.tipe.toLowerCase().includes('pasar'),
                                          'bg-[#051B44] text-white': mitra.tipeKey === 'eksportir' || mitra.tipe.toLowerCase().includes('eksportir'),
                                          'bg-amber-100 text-amber-800': mitra.tipeKey === 'distributor' || mitra.tipe.toLowerCase().includes('distributor')
                                      }"
                                      x-text="mitra.tipe">
                                </span>
                            </td>
                            <td class="py-4 px-6">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-8 rounded-lg bg-slate-100 border border-slate-200 flex items-center justify-center shrink-0 text-slate-400">
                                        <i class="fa-solid fa-map-location-dot text-xs"></i>
                                    </div>
                                    <span class="text-xs text-slate-700 font-medium" x-text="mitra.alamat"></span>
                                </div>
                            </td>
                            <td class="py-4 px-6 text-right">
                                <!-- Action Dropdown -->
                                <div class="relative inline-block text-left" x-data="{ open: false }">
                                    <button @click="open = !open" @click.away="open = false"
                                            class="w-8 h-8 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-600 flex items-center justify-center transition-colors">
                                        <i class="fa-solid fa-ellipsis-vertical text-sm"></i>
                                    </button>
                                    
                                    <div x-show="open"
                                         x-transition:enter="transition ease-out duration-100"
                                         x-transition:enter-start="transform opacity-0 scale-95"
                                         x-transition:enter-end="transform opacity-100 scale-100"
                                         x-transition:leave="transition ease-in duration-75"
                                         x-transition:leave-start="transform opacity-100 scale-100"
                                         x-transition:leave-end="transform opacity-0 scale-95"
                                         class="absolute right-0 mt-2 w-44 rounded-xl bg-white border border-slate-200 shadow-xl py-1.5 z-50 text-left">
                                        
                                        <!-- View Lengkap -->
                                        <button @click="open = false; openViewForm(mitra)" 
                                                class="w-full px-3.5 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-50 flex items-center gap-2.5">
                                            <i class="fa-solid fa-eye text-sky-600 w-4"></i>
                                            <span>View Lengkap</span>
                                        </button>

                                        <!-- Edit Data -->
                                        <button @click="open = false; openEditForm(mitra)" 
                                                class="w-full px-3.5 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-50 flex items-center gap-2.5">
                                            <i class="fa-solid fa-pen-to-square text-amber-600 w-4"></i>
                                            <span>Edit Data</span>
                                        </button>

                                        <div class="my-1 border-t border-slate-100"></div>

                                        <!-- Hapus Data -->
                                        <button @click="open = false; deleteMitra(mitra)" 
                                                class="w-full px-3.5 py-2 text-xs font-semibold text-red-600 hover:bg-red-50 flex items-center gap-2.5">
                                            <i class="fa-solid fa-trash-can text-red-500 w-4"></i>
                                            <span>Hapus Data</span>
                                        </button>

                                    </div>
                                </div>
                            </td>
                        </tr>
                    </template>

                    <!-- Empty State -->
                    <tr x-show="filteredMitras.length === 0">
                        <td colspan="4" class="py-12 text-center text-slate-400">
                            <i class="fa-solid fa-folder-open text-3xl mb-2 block"></i>
                            <span class="text-xs font-medium">Tidak ada data mitra yang sesuai dengan pencarian/filter.</span>
                        </td>
                    </tr>

                </tbody>
            </table>
        </div>

        <!-- Table Footer Pagination -->
        <div class="p-4 border-t border-slate-100 flex flex-col sm:flex-row items-center justify-between gap-4 text-xs font-medium text-slate-500">
            <span x-text="'Menampilkan 1-' + filteredMitras.length + ' dari ' + mitras.length + ' Mitra'"></span>
            <div class="flex items-center gap-1">
                <button class="w-7 h-7 rounded border border-slate-200 flex items-center justify-center text-slate-400 hover:bg-slate-50">&lt;</button>
                <button class="w-7 h-7 rounded bg-[#051B44] text-white font-bold flex items-center justify-center">1</button>
                <button class="w-7 h-7 rounded border border-slate-200 flex items-center justify-center text-slate-600 hover:bg-slate-50">&gt;</button>
            </div>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
    let mitraMapInstance = null;
    let currentMarker = null;

    function initMitraMap(latVal, lngVal, isReadOnly = false) {
        const latNum = parseFloat(latVal) || -6.200000;
        const lngNum = parseFloat(lngVal) || 106.816666;

        const mapContainer = document.getElementById('mitraMapPicker');
        if (!mapContainer) return;

        if (!mitraMapInstance) {
            mitraMapInstance = L.map('mitraMapPicker', {
                zoomControl: false
            }).setView([latNum, lngNum], 13);

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19,
                attribution: '© OpenStreetMap'
            }).addTo(mitraMapInstance);

            L.control.zoom({ position: 'topright' }).addTo(mitraMapInstance);
        } else {
            mitraMapInstance.setView([latNum, lngNum], 13);
            setTimeout(() => {
                mitraMapInstance.invalidateSize();
            }, 200);
        }

        if (currentMarker) {
            mitraMapInstance.removeLayer(currentMarker);
        }

        currentMarker = L.marker([latNum, lngNum], { draggable: !isReadOnly }).addTo(mitraMapInstance);

        function updateCoords(l1, l2) {
            const latElem = document.getElementById('latInput');
            const lngElem = document.getElementById('lngInput');
            if (latElem) latElem.value = parseFloat(l1).toFixed(6);
            if (lngElem) lngElem.value = parseFloat(l2).toFixed(6);

            const rootElem = document.querySelector('[x-data]');
            if (rootElem && rootElem._x_dataStack) {
                const data = rootElem._x_dataStack[0];
                if (data && data.form) {
                    data.form.lat = parseFloat(l1).toFixed(6);
                    data.form.lng = parseFloat(l2).toFixed(6);
                }
            }
        }

        mitraMapInstance.off('click');
        if (!isReadOnly) {
            mitraMapInstance.on('click', function(e) {
                const lat = e.latlng.lat;
                const lng = e.latlng.lng;
                currentMarker.setLatLng([lat, lng]);
                updateCoords(lat, lng);
            });

            currentMarker.on('dragend', function(e) {
                const latlng = currentMarker.getLatLng();
                updateCoords(latlng.lat, latlng.lng);
            });
        }
    }
</script>
@endpush

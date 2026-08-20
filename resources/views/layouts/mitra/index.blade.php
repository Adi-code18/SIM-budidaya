@extends('layouts.app')

@section('title', 'Manajemen Mitra - SIM-BUDIDAYA')

@section('content')
<div class="space-y-6" x-data="{ showForm: false, tipeMitra: 'distributor' }">

    <!-- Subtitle & Page Title Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-extrabold text-[#0B2570] tracking-tight">Manajemen Mitra</h1>
            <p class="text-xs text-slate-500 font-medium mt-0.5">Kelola hubungan dan distribusi hasil budidaya ke mitra strategis.</p>
        </div>
        <div>
            <button @click="showForm = !showForm; if(showForm) { $nextTick(() => initMitraMap()); }" 
                    class="px-4 py-2 rounded-xl bg-[#051B44] hover:bg-navy-900 text-white font-bold text-xs shadow-xs transition-all flex items-center gap-2">
                <i class="fa-solid" :class="showForm ? 'fa-list' : 'fa-plus'"></i>
                <span x-text="showForm ? 'Lihat Daftar Mitra' : 'Tambah Mitra Baru'"></span>
            </button>
        </div>
    </div>

    <!-- Input Form Section (Shown when showForm is true) -->
    <div x-show="showForm" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 -translate-y-2"
         x-transition:enter-end="opacity-100 translate-y-0"
         class="space-y-4">
        
        <h2 class="text-lg font-extrabold text-slate-900 tracking-tight">Input Manajemen Mitra</h2>

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
                    <h3 class="text-xl font-extrabold text-white">Registrasi Mitra</h3>
                    <p class="text-xs text-sky-100/80 font-medium leading-relaxed">
                        Daftarkan entitas mitra rantai pasok baru. Pastikan titik koordinat akurat untuk keperluan perhitungan biaya distribusi dan estimasi waktu tiba (ETA).
                    </p>

                    <div class="pt-2">
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[10px] font-extrabold bg-sky-500/20 text-sky-300 border border-sky-400/30">
                            <i class="fa-solid fa-hand-pointer"></i> Klik lokasi pada peta untuk mengambil Lat &amp; Lng otomatis
                        </span>
                    </div>
                </div>
            </div>

            <!-- Right 7 Cols: Mitra Input Fields Form -->
            <div class="lg:col-span-7 bg-white p-6 sm:p-8 rounded-2xl border border-slate-200/80 shadow-xs space-y-5">
                
                <form action="#" method="POST" @submit.prevent class="space-y-5">
                    
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
                                       placeholder="PT. Global Akuakultur..." 
                                       class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs font-semibold text-slate-700 bg-slate-50/70 focus:bg-white focus:outline-none focus:ring-2 focus:ring-sky-500 transition-all">
                            </div>
                            <div>
                                <label class="text-[10px] font-extrabold uppercase tracking-wider text-slate-500 block mb-1">ID MITRA (AUTO)</label>
                                <input type="text" 
                                       value="MTR-2023-089" 
                                       readonly 
                                       class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs font-extrabold text-slate-500 bg-slate-100 cursor-not-allowed">
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
                            <div class="flex items-center gap-2 p-1 bg-slate-100 rounded-xl max-w-sm text-xs font-bold">
                                <button type="button" 
                                        @click="tipeMitra = 'distributor'" 
                                        :class="tipeMitra === 'distributor' ? 'bg-[#051B44] text-white shadow-xs' : 'text-slate-600 hover:text-slate-900'" 
                                        class="flex-1 py-2 rounded-lg transition-all text-center">
                                    Distributor
                                </button>
                                <button type="button" 
                                        @click="tipeMitra = 'supplier'" 
                                        :class="tipeMitra === 'supplier' ? 'bg-[#051B44] text-white shadow-xs' : 'text-slate-600 hover:text-slate-900'" 
                                        class="flex-1 py-2 rounded-lg transition-all text-center">
                                    Supplier
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
                                      placeholder="Jl. Raya Pelabuhan No. 45..." 
                                      class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs font-semibold text-slate-700 bg-slate-50/70 focus:bg-white focus:outline-none focus:ring-2 focus:ring-sky-500 transition-all"></textarea>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="text-[10px] font-extrabold uppercase tracking-wider text-slate-500 block mb-1">LATITUDE</label>
                                <input type="text" 
                                       id="latInput" 
                                       value="Lat: -6.200000" 
                                       readonly 
                                       class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs font-extrabold text-[#0B2570] bg-sky-50/60 cursor-pointer"
                                       title="Ambil otomatis dengan klik pada peta">
                            </div>
                            <div>
                                <label class="text-[10px] font-extrabold uppercase tracking-wider text-slate-500 block mb-1">LONGITUDE</label>
                                <input type="text" 
                                       id="lngInput" 
                                       value="Lng: 106.816666" 
                                       readonly 
                                       class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs font-extrabold text-[#0B2570] bg-sky-50/60 cursor-pointer"
                                       title="Ambil otomatis dengan klik pada peta">
                            </div>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
                        <button type="button" 
                                @click="showForm = false" 
                                class="px-5 py-2 rounded-xl border border-slate-200 text-xs font-bold text-slate-600 hover:bg-slate-50 transition-colors">
                            Batal
                        </button>
                        <button type="submit" 
                                class="px-5 py-2 rounded-xl bg-[#051B44] hover:bg-navy-900 text-white font-bold text-xs shadow-xs transition-all flex items-center gap-2">
                            <span>Simpan Data</span>
                            <i class="fa-solid fa-circle-check text-xs"></i>
                        </button>
                    </div>

                </form>

            </div>

        </div>

    </div>

    <!-- Filters & Metric Header Row (Directory Mode) -->
    <div x-show="!showForm" class="grid grid-cols-1 md:grid-cols-3 gap-5">
        
        <!-- Filter 1: Tipe Mitra -->
        <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-xs flex items-center gap-3">
            <div class="w-9 h-9 rounded-xl bg-slate-100 text-slate-500 flex items-center justify-center shrink-0">
                <i class="fa-solid fa-sliders text-sm"></i>
            </div>
            <div class="flex-1">
                <span class="text-[10px] font-extrabold text-slate-400 uppercase tracking-widest block">TIPE MITRA</span>
                <select class="w-full bg-transparent text-xs font-bold text-slate-800 focus:outline-none cursor-pointer mt-0.5">
                    <option value="">Semua Tipe</option>
                    <option value="restoran">Restoran</option>
                    <option value="supplier">Supplier Frozen Food</option>
                    <option value="pasar">Pasar Tradisional</option>
                    <option value="eksportir">Eksportir</option>
                </select>
            </div>
        </div>

        <!-- Filter 2: Wilayah -->
        <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-xs flex items-center gap-3">
            <div class="w-9 h-9 rounded-xl bg-slate-100 text-slate-500 flex items-center justify-center shrink-0">
                <i class="fa-solid fa-location-dot text-sm"></i>
            </div>
            <div class="flex-1">
                <span class="text-[10px] font-extrabold text-slate-400 uppercase tracking-widest block">WILAYAH</span>
                <select class="w-full bg-transparent text-xs font-bold text-slate-800 focus:outline-none cursor-pointer mt-0.5">
                    <option value="">Seluruh Indonesia</option>
                    <option value="jakarta">DKI Jakarta</option>
                    <option value="jabar">Jawa Barat</option>
                    <option value="jateng">Jawa Tengah</option>
                </select>
            </div>
        </div>

        <!-- Metric Card: Total Mitra Aktif -->
        <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-xs flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-[#BEE3F8]/60 text-[#006699] flex items-center justify-center shrink-0">
                    <i class="fa-solid fa-users text-base"></i>
                </div>
                <div>
                    <span class="text-[10px] font-extrabold text-slate-400 uppercase tracking-widest block">TOTAL MITRA AKTIF</span>
                    <div class="flex items-baseline gap-2 mt-0.5">
                        <h3 class="text-xl font-extrabold text-slate-900">124</h3>
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
                    
                    <!-- Row 1 -->
                    <tr class="hover:bg-slate-50/50 transition-colors">
                        <td class="py-4 px-6">
                            <div class="flex items-center gap-3">
                                <img src="https://images.unsplash.com/photo-1555396273-367ea4eb4db5?auto=format&fit=crop&q=80&w=120" 
                                     alt="The Ocean Grill" 
                                     class="w-10 h-10 rounded-xl object-cover border border-slate-200">
                                <div>
                                    <h4 class="font-extrabold text-[#0055CC] text-sm">The Ocean Grill</h4>
                                    <span class="text-[10px] text-slate-400 block font-normal">ID: MTR-2023-081</span>
                                </div>
                            </div>
                        </td>
                        <td class="py-4 px-6">
                            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold bg-[#E0F2FE] text-[#0284C7] uppercase">
                                RESTORAN
                            </span>
                        </td>
                        <td class="py-4 px-6">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-8 rounded-lg bg-slate-100 border border-slate-200 flex items-center justify-center shrink-0 text-slate-400">
                                    <i class="fa-solid fa-map-location-dot text-xs"></i>
                                </div>
                                <span class="text-xs text-slate-700 font-medium">Jl. Sudirman No. 45, Jakarta Pusat</span>
                            </div>
                        </td>
                        <td class="py-4 px-6 text-right">
                            <button class="text-slate-400 hover:text-slate-600 p-1">
                                <i class="fa-solid fa-ellipsis-vertical text-sm"></i>
                            </button>
                        </td>
                    </tr>

                    <!-- Row 2 -->
                    <tr class="hover:bg-slate-50/50 transition-colors">
                        <td class="py-4 px-6">
                            <div class="flex items-center gap-3">
                                <img src="https://images.unsplash.com/photo-1586528116311-ad8dd3c8310d?auto=format&fit=crop&q=80&w=120" 
                                     alt="IndoFrozen Supply" 
                                     class="w-10 h-10 rounded-xl object-cover border border-slate-200">
                                <div>
                                    <h4 class="font-extrabold text-[#0055CC] text-sm">IndoFrozen Supply</h4>
                                    <span class="text-[10px] text-slate-400 block font-normal">ID: MTR-2023-102</span>
                                </div>
                            </div>
                        </td>
                        <td class="py-4 px-6">
                            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold bg-[#C6F6D5] text-[#22543D] uppercase">
                                SUPPLIER FROZEN FOOD
                            </span>
                        </td>
                        <td class="py-4 px-6">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-8 rounded-lg bg-slate-100 border border-slate-200 flex items-center justify-center shrink-0 text-slate-400">
                                    <i class="fa-solid fa-map-location-dot text-xs"></i>
                                </div>
                                <span class="text-xs text-slate-700 font-medium">Kawasan Industri Jababeka, Bekasi</span>
                            </div>
                        </td>
                        <td class="py-4 px-6 text-right">
                            <button class="text-slate-400 hover:text-slate-600 p-1">
                                <i class="fa-solid fa-ellipsis-vertical text-sm"></i>
                            </button>
                        </td>
                    </tr>

                    <!-- Row 3 -->
                    <tr class="hover:bg-slate-50/50 transition-colors">
                        <td class="py-4 px-6">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-xl bg-slate-100 text-slate-500 flex items-center justify-center shrink-0 border border-slate-200">
                                    <i class="fa-solid fa-store text-sm"></i>
                                </div>
                                <div>
                                    <h4 class="font-extrabold text-[#0055CC] text-sm">Pasar Ikan Muara Baru</h4>
                                    <span class="text-[10px] text-slate-400 block font-normal">ID: MTR-2022-045</span>
                                </div>
                            </div>
                        </td>
                        <td class="py-4 px-6">
                            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold bg-[#E2E8F0] text-[#475569] uppercase">
                                PASAR TRADISIONAL
                            </span>
                        </td>
                        <td class="py-4 px-6">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-8 rounded-lg bg-slate-100 border border-slate-200 flex items-center justify-center shrink-0 text-slate-400">
                                    <i class="fa-solid fa-map-location-dot text-xs"></i>
                                </div>
                                <span class="text-xs text-slate-700 font-medium">Jl. Muara Baru Raya, Jakarta Utara</span>
                            </div>
                        </td>
                        <td class="py-4 px-6 text-right">
                            <button class="text-slate-400 hover:text-slate-600 p-1">
                                <i class="fa-solid fa-ellipsis-vertical text-sm"></i>
                            </button>
                        </td>
                    </tr>

                    <!-- Row 4 -->
                    <tr class="hover:bg-slate-50/50 transition-colors">
                        <td class="py-4 px-6">
                            <div class="flex items-center gap-3">
                                <img src="https://images.unsplash.com/photo-1541888946425-d0fbb186a5b3?auto=format&fit=crop&q=80&w=120" 
                                     alt="Global Seafood Corp" 
                                     class="w-10 h-10 rounded-xl object-cover border border-slate-200">
                                <div>
                                    <h4 class="font-extrabold text-[#0055CC] text-sm">Global Seafood Corp</h4>
                                    <span class="text-[10px] text-slate-400 block font-normal">ID: MTR-2023-156</span>
                                </div>
                            </div>
                        </td>
                        <td class="py-4 px-6">
                            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold bg-[#051B44] text-white uppercase">
                                EKSPORTIR
                            </span>
                        </td>
                        <td class="py-4 px-6">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-8 rounded-lg bg-slate-100 border border-slate-200 flex items-center justify-center shrink-0 text-slate-400">
                                    <i class="fa-solid fa-map-location-dot text-xs"></i>
                                </div>
                                <span class="text-xs text-slate-700 font-medium">Pelabuhan Tanjung Priok, Jakarta</span>
                            </div>
                        </td>
                        <td class="py-4 px-6 text-right">
                            <button class="text-slate-400 hover:text-slate-600 p-1">
                                <i class="fa-solid fa-ellipsis-vertical text-sm"></i>
                            </button>
                        </td>
                    </tr>

                </tbody>
            </table>
        </div>

        <!-- Table Footer Pagination -->
        <div class="p-4 border-t border-slate-100 flex flex-col sm:flex-row items-center justify-between gap-4 text-xs font-medium text-slate-500">
            <span>Menampilkan 1-4 dari 124 Mitra</span>
            <div class="flex items-center gap-1">
                <button class="w-7 h-7 rounded border border-slate-200 flex items-center justify-center text-slate-400 hover:bg-slate-50">&lt;</button>
                <button class="w-7 h-7 rounded bg-[#051B44] text-white font-bold flex items-center justify-center">1</button>
                <button class="w-7 h-7 rounded border border-slate-200 flex items-center justify-center text-slate-600 hover:bg-slate-50">2</button>
                <button class="w-7 h-7 rounded border border-slate-200 flex items-center justify-center text-slate-600 hover:bg-slate-50">3</button>
                <button class="w-7 h-7 rounded border border-slate-200 flex items-center justify-center text-slate-400 hover:bg-slate-50">&gt;</button>
            </div>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
    let mitraMapInstance = null;
    function initMitraMap() {
        if (mitraMapInstance) {
            mitraMapInstance.invalidateSize();
            return;
        }

        const mapContainer = document.getElementById('mitraMapPicker');
        if (!mapContainer) return;

        mitraMapInstance = L.map('mitraMapPicker', {
            zoomControl: false
        }).setView([-6.200000, 106.816666], 13);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '© OpenStreetMap'
        }).addTo(mitraMapInstance);

        L.control.zoom({ position: 'topright' }).addTo(mitraMapInstance);

        let marker = L.marker([-6.200000, 106.816666], { draggable: true }).addTo(mitraMapInstance);

        function updateCoords(lat, lng) {
            const latElem = document.getElementById('latInput');
            const lngElem = document.getElementById('lngInput');
            if (latElem) latElem.value = 'Lat: ' + parseFloat(lat).toFixed(6);
            if (lngElem) lngElem.value = 'Lng: ' + parseFloat(lng).toFixed(6);
        }

        mitraMapInstance.on('click', function(e) {
            const lat = e.latlng.lat;
            const lng = e.latlng.lng;
            marker.setLatLng([lat, lng]);
            updateCoords(lat, lng);
        });

        marker.on('dragend', function(e) {
            const latlng = marker.getLatLng();
            updateCoords(latlng.lat, latlng.lng);
        });
    }
</script>
@endpush

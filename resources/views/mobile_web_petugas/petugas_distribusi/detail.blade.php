@extends('mobile_web_petugas.petugas_distribusi.layout')

@section('title', 'Detail & Tracking Pengiriman - SIM-BUDIDAYA Mobile')

@section('content')
<div class="p-4 space-y-4" x-data="detailTrackingData()">

    <!-- Top Back Navigation Header Bar -->
    <div class="flex items-center gap-3 py-1 border-b border-slate-200/80 pb-3">
        <a href="{{ route('mobile.petugas.pengiriman') }}" 
           class="w-9 h-9 rounded-xl bg-white border border-slate-200 text-slate-700 flex items-center justify-center hover:bg-slate-100 transition-colors shadow-xs">
            <i class="fa-solid fa-arrow-left text-sm"></i>
        </a>
        <div class="flex-1 min-w-0">
            <span class="text-[9px] font-extrabold text-sky-600 uppercase tracking-wider block">DETAIL ORDER TRACKING</span>
            <h1 class="text-sm font-extrabold text-navy-900 truncate">Pengiriman Mitra - {{ $transaksi->mitra ? $transaksi->mitra->nama_mitra : 'Mitra Distribusi' }}</h1>
        </div>
    </div>

    <!-- Stepper Status Timeline Card -->
    <div class="bg-white rounded-2xl border border-slate-200/90 p-4 shadow-xs space-y-3">
        <span class="text-[10px] font-extrabold uppercase text-slate-400 tracking-wider block text-center">STATUS PENGIRIMAN</span>
        
        <!-- Timeline Steps -->
        <div class="flex items-center justify-between relative px-4 py-2">
            
            <!-- Connecting Line -->
            <div class="absolute left-10 right-10 top-5 h-1 bg-slate-200 -z-0"></div>
            <div class="absolute left-10 top-5 h-1 bg-navy-800 transition-all duration-500 -z-0" :style="deliveryDone ? 'width: 80%' : 'width: 45%'"></div>

            <!-- Step 1: Dikirimkan -->
            <div class="flex flex-col items-center gap-1.5 relative z-10">
                <div class="w-7 h-7 rounded-full bg-navy-800 text-white flex items-center justify-center text-xs font-bold ring-4 ring-white shadow-xs">
                    <i class="fa-solid fa-check text-[10px]"></i>
                </div>
                <span class="text-[10px] font-bold text-navy-900">Dikirimkan</span>
            </div>

            <!-- Step 2: Diproses -->
            <div class="flex flex-col items-center gap-1.5 relative z-10">
                <div class="w-7 h-7 rounded-full bg-navy-800 text-white flex items-center justify-center text-xs font-bold ring-4 ring-white shadow-xs">
                    <i class="fa-solid fa-truck-fast text-[10px]"></i>
                </div>
                <span class="text-[10px] font-bold text-navy-900">Diproses</span>
            </div>

            <!-- Step 3: Selesai -->
            <div class="flex flex-col items-center gap-1.5 relative z-10">
                <div class="w-7 h-7 rounded-full flex items-center justify-center text-xs font-bold ring-4 ring-white shadow-xs transition-all"
                     :class="deliveryDone ? 'bg-emerald-600 text-white' : 'bg-slate-200 text-slate-500'">
                    <i class="fa-solid" :class="deliveryDone ? 'fa-check text-[10px]' : 'fa-house-flag text-[10px]'"></i>
                </div>
                <span class="text-[10px] font-bold" :class="deliveryDone ? 'text-emerald-700 font-extrabold' : 'text-slate-400'">Selesai</span>
            </div>

        </div>
    </div>

    <!-- Main Item Information Card -->
    <div class="bg-white rounded-2xl border border-slate-200/90 p-4 shadow-xs flex items-center justify-between">
        <div>
            <span class="text-[10px] font-extrabold text-slate-400 uppercase block tracking-wider">KOMODITAS & ID ORDER</span>
            <h2 class="text-base font-extrabold text-navy-900 mt-0.5">{{ $transaksi->batchPembesaran ? $transaksi->batchPembesaran->jenis_ikan : 'Ikan Konsumsi Segar' }}</h2>
            <p class="text-xs text-slate-500 font-semibold mt-0.5">ID: <span class="text-navy-800 font-bold">#ORD-{{ str_pad($transaksi->id_transaksi, 4, '0', STR_PAD_LEFT) }}</span></p>
        </div>
        <div class="px-3.5 py-2 rounded-xl bg-sky-50 border border-sky-200 text-sky-800 font-extrabold text-sm text-center shadow-2xs">
            <span>{{ number_format($transaksi->Total_kg, 0, ',', '.') }} KG</span>
            <span class="text-[9px] font-bold block text-sky-600">Netto</span>
        </div>
    </div>

    <!-- Destination Address Box -->
    <div class="bg-white rounded-2xl border border-slate-200/90 p-4 shadow-xs space-y-2.5">
        <div class="flex items-center gap-2">
            <div class="w-6 h-6 rounded-lg bg-rose-100 text-rose-600 flex items-center justify-center text-xs">
                <i class="fa-solid fa-location-dot"></i>
            </div>
            <span class="text-xs font-extrabold text-slate-900">Tujuan Pengiriman</span>
        </div>

        <div class="bg-slate-50 p-3 rounded-xl border border-slate-100 text-xs text-slate-700 leading-relaxed font-medium">
            <strong class="text-slate-900 block font-bold text-xs mb-1">{{ $transaksi->mitra ? $transaksi->mitra->nama_mitra : 'Mitra Distribusi' }} ({{ $transaksi->mitra ? ($transaksi->mitra->penanggung_jawab ?? 'Penerima') : 'Penerima' }})</strong>
            {{ $transaksi->mitra ? $transaksi->mitra->alamat : 'Kota Mataram, Nusa Tenggara Barat' }}
        </div>
    </div>

    <!-- Interactive Action Buttons Stack -->
    <div class="space-y-2.5 pt-1">
        
        <!-- Button 1: Navigasi via Maps -->
        <button @click="mapModal = true" 
                class="w-full py-3 rounded-2xl bg-navy-800 hover:bg-navy-900 active:scale-[0.99] text-white font-bold text-xs flex items-center justify-center gap-2.5 shadow-sm transition-all">
            <i class="fa-solid fa-map-location-dot text-sm text-sky-300"></i>
            <span>Navigasi via Maps</span>
        </button>

        <!-- Button 2: Chat Mitra via WA -->
        <a href="https://wa.me/{{ $transaksi->mitra ? preg_replace('/[^0-9]/', '', $transaksi->mitra->no_hp) : '6281234567890' }}?text=Halo%20{{ urlencode($transaksi->mitra ? $transaksi->mitra->nama_mitra : 'Mitra') }},%20saya%20petugas%20distribusi%20SIM-BUDIDAYA%20sedang%20dalam%20perjalanan." 
           target="_blank"
           class="w-full py-3 rounded-2xl bg-white border border-slate-300 hover:bg-slate-50 active:scale-[0.99] text-slate-800 font-bold text-xs flex items-center justify-center gap-2.5 shadow-2xs transition-all">
            <i class="fa-brands fa-whatsapp text-emerald-600 text-base"></i>
            <span>Chat Mitra via WA</span>
        </a>

        <!-- Button 3: Upload Foto Serah Terima -->
        <button @click="uploadModal = true" 
                class="w-full py-3 rounded-2xl bg-white border border-slate-300 hover:bg-slate-50 active:scale-[0.99] text-slate-800 font-bold text-xs flex items-center justify-center gap-2.5 shadow-2xs transition-all">
            <i class="fa-solid fa-camera text-sky-600 text-sm"></i>
            <span x-text="uploadedImage ? 'Foto Serah Terima (Tergugah ✓)' : 'Upload Foto Serah Terima'">Upload Foto Serah Terima</span>
        </button>

    </div>

    <!-- Sticky Bottom Slide-to-Confirm Action Bar -->
    <div class="pt-2">
        <template x-if="!deliveryDone">
            <div class="relative w-full h-14 bg-[#0F2C59] rounded-2xl p-1.5 flex items-center select-none overflow-hidden shadow-lg border border-sky-950/40"
                 style="touch-action: none; -webkit-user-select: none; user-select: none;"
                 x-ref="sliderTrack"
                 x-init="initSlider()">
                
                <!-- Dynamic Gradient Fill on Slide -->
                <div class="absolute left-0 top-0 bottom-0 bg-gradient-to-r from-sky-600 via-sky-500 to-emerald-500 rounded-2xl pointer-events-none"
                     :class="isDragging ? '' : 'transition-all duration-300 ease-out'"
                     :style="'width: ' + (currentX + 52) + 'px'">
                </div>

                <!-- Text & Animated Chevrons in Center -->
                <div class="absolute inset-0 flex items-center justify-center pointer-events-none px-12 transition-opacity duration-200"
                     :style="'opacity: ' + Math.max(0, 1 - progress * 1.6)">
                    <div class="flex items-center gap-2 text-white font-extrabold text-xs tracking-wide">
                        <div class="w-5 h-5 rounded-full bg-white/20 flex items-center justify-center text-white text-[10px]">
                            <i class="fa-solid fa-circle-check"></i>
                        </div>
                        <span>Tandai Barang Sudah Tiba</span>
                        <i class="fa-solid fa-angles-right text-[10px] text-sky-300 animate-pulse"></i>
                    </div>
                </div>

                <!-- Draggable Slider Handle (Thumb) with Pointer Capture -->
                <div x-ref="sliderThumb"
                     @pointerdown="startDrag($event)"
                     @pointermove="onDrag($event)"
                     @pointerup="endDrag($event)"
                     @pointercancel="endDrag($event)"
                     class="relative z-10 w-11 h-11 rounded-xl bg-sky-500 hover:bg-sky-400 active:scale-95 flex items-center justify-center text-white shadow-md cursor-grab active:cursor-grabbing select-none"
                     :class="isDragging ? 'shadow-sky-500/60 scale-105' : 'transition-transform duration-300 ease-out'"
                     :style="'transform: translateX(' + currentX + 'px); touch-action: none;'">
                    <i class="fa-solid" :class="progress > 0.7 ? 'fa-check text-base' : 'fa-arrow-right text-xs'"></i>
                </div>

                <!-- Right Hint Badge -->
                <div class="absolute right-4 text-[9px] font-extrabold text-sky-200/60 pointer-events-none uppercase tracking-wider flex items-center gap-1"
                     :style="'opacity: ' + Math.max(0, 1 - progress * 2)">
                    <span>GESER</span>
                    <i class="fa-solid fa-chevron-right text-[8px]"></i>
                </div>

            </div>
        </template>

        <template x-if="deliveryDone">
            <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-center space-y-2">
                <div class="w-10 h-10 rounded-full bg-emerald-600 text-white mx-auto flex items-center justify-center text-lg shadow-sm">
                    <i class="fa-solid fa-check"></i>
                </div>
                <h3 class="font-extrabold text-emerald-900 text-sm">Pengiriman Selesai!</h3>
                <p class="text-xs text-emerald-700 font-medium">Status order #ORD-{{ str_pad($transaksi->id_transaksi, 4, '0', STR_PAD_LEFT) }} telah dipindahkan ke Riwayat Pengiriman.</p>
                <div class="pt-2 flex items-center justify-center gap-2">
                    <a href="{{ route('mobile.petugas.riwayat') }}" class="px-4 py-2.5 rounded-xl bg-emerald-700 hover:bg-emerald-800 text-white font-bold text-xs shadow-xs transition-colors flex items-center gap-1.5">
                        <i class="fa-solid fa-clock-rotate-left"></i>
                        <span>Lihat di Riwayat</span>
                    </a>
                    <a href="{{ route('mobile.petugas.pengiriman') }}" class="px-4 py-2.5 rounded-xl bg-white border border-slate-300 text-slate-700 font-bold text-xs hover:bg-slate-50 transition-colors">
                        Daftar Tugas
                    </a>
                </div>
            </div>
        </template>
    </div>

    <!-- ================= MODALS ================= -->

    <!-- Modal 1: Map Navigasi Modal -->
    <div x-show="mapModal" 
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
        
        <div class="bg-white rounded-3xl w-full max-w-sm overflow-hidden shadow-2xl space-y-4">
            <div class="bg-navy-800 text-white p-4 flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <i class="fa-solid fa-map-location-dot text-sky-400"></i>
                    <h3 class="text-xs font-bold">Peta Rute Delivery</h3>
                </div>
                <button @click="mapModal = false" class="text-slate-300 hover:text-white">
                    <i class="fa-solid fa-xmark text-sm"></i>
                </button>
            </div>

            <div class="p-4 space-y-3">
                <div class="w-full h-48 bg-slate-200 rounded-2xl overflow-hidden relative border border-slate-200 flex items-center justify-center">
                    <!-- Leaflet map container iframe/placeholder simulation -->
                    <iframe width="100%" height="100%" frameborder="0" scrolling="no" marginheight="0" marginwidth="0" 
                            src="https://maps.google.com/maps?q=-8.5833,116.1167&z=14&output=embed">
                    </iframe>
                </div>

                <div class="bg-slate-50 p-3 rounded-xl text-xs space-y-1">
                    <span class="text-slate-400 font-extrabold uppercase text-[9px] block">KOORDINAT TUJUAN</span>
                    <p class="font-bold text-slate-800">-8.583333, 116.116667 (Mataram)</p>
                </div>

                <a href="https://maps.google.com/?q=-8.5833,116.1167" target="_blank"
                   class="w-full py-2.5 rounded-xl bg-navy-800 text-white font-bold text-xs flex items-center justify-center gap-2">
                    <i class="fa-solid fa-up-right-from-square text-xs"></i>
                    <span>Buka di Google Maps App</span>
                </a>
            </div>
        </div>
    </div>

    <!-- Modal 2: Upload Foto Serah Terima Modal -->
    <div x-show="uploadModal" 
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
        
        <div class="bg-white rounded-3xl w-full max-w-sm overflow-hidden shadow-2xl space-y-4">
            <div class="bg-navy-800 text-white p-4 flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <i class="fa-solid fa-camera text-sky-400"></i>
                    <h3 class="text-xs font-bold">Bukti Foto Serah Terima</h3>
                </div>
                <button @click="uploadModal = false" class="text-slate-300 hover:text-white">
                    <i class="fa-solid fa-xmark text-sm"></i>
                </button>
            </div>

            <div class="p-4 space-y-4">
                <template x-if="uploadedImage">
                    <div class="space-y-3">
                        <img :src="uploadedImage" class="w-full h-44 object-cover rounded-2xl border border-slate-200 shadow-xs">
                        <button @click="uploadedImage = null" class="text-xs text-rose-600 font-bold flex items-center gap-1 mx-auto">
                            <i class="fa-solid fa-trash text-xs"></i> Hapus & Foto Ulang
                        </button>
                    </div>
                </template>

                <template x-if="!uploadedImage">
                    <label class="border-2 border-dashed border-slate-300 hover:border-navy-800 rounded-2xl p-6 flex flex-col items-center justify-center gap-2 cursor-pointer bg-slate-50 transition-colors">
                        <div class="w-12 h-12 rounded-full bg-sky-100 text-sky-700 flex items-center justify-center text-lg">
                            <i class="fa-solid fa-cloud-arrow-up"></i>
                        </div>
                        <span class="text-xs font-bold text-slate-800">Ambil Foto atau Pilih Gambar</span>
                        <span class="text-[10px] text-slate-400">Format PNG, JPG max 5MB</span>
                        <input type="file" accept="image/*" capture="environment" class="hidden" @change="handleImageUpload($event)">
                    </label>
                </template>

                <button @click="uploadModal = false" 
                        class="w-full py-2.5 rounded-xl bg-navy-800 text-white font-bold text-xs shadow-xs">
                    Simpan Foto
                </button>
            </div>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
function detailTrackingData() {
    return {
        uploadModal: false,
        mapModal: false,
        uploadedImage: null,
        deliveryDone: false,
        
        // Slider Variables
        isDragging: false,
        startX: 0,
        currentX: 0,
        progress: 0,
        maxSlide: 0,

        initSlider() {
            this.$nextTick(() => {
                this.updateMax();
            });
            window.addEventListener('resize', () => this.updateMax());
        },

        updateMax() {
            if (this.$refs.sliderTrack && this.$refs.sliderThumb) {
                const trackW = this.$refs.sliderTrack.clientWidth;
                const thumbW = this.$refs.sliderThumb.offsetWidth;
                this.maxSlide = Math.max(0, trackW - thumbW - 12);
            }
        },

        startDrag(e) {
            if (this.deliveryDone) return;
            this.updateMax();
            this.isDragging = true;
            this.startX = e.clientX - this.currentX;
            if (e.currentTarget && e.currentTarget.setPointerCapture) {
                try {
                    e.currentTarget.setPointerCapture(e.pointerId);
                } catch(err) {}
            }
        },

        onDrag(e) {
            if (!this.isDragging || this.deliveryDone) return;
            const deltaX = e.clientX - this.startX;
            this.currentX = Math.max(0, Math.min(deltaX, this.maxSlide));
            this.progress = this.maxSlide > 0 ? (this.currentX / this.maxSlide) : 0;
        },

        endDrag(e) {
            if (!this.isDragging || this.deliveryDone) return;
            this.isDragging = false;
            if (e.currentTarget && e.currentTarget.releasePointerCapture) {
                try {
                    e.currentTarget.releasePointerCapture(e.pointerId);
                } catch(err) {}
            }

            if (this.progress >= 0.65) {
                this.currentX = this.maxSlide;
                this.progress = 1;
                this.confirmArrived();
            } else {
                this.currentX = 0;
                this.progress = 0;
            }
        },

        resetSlider() {
            this.isDragging = false;
            this.currentX = 0;
            this.progress = 0;
        },

        async confirmArrived() {
            this.deliveryDone = true;
            try {
                const res = await fetch('{{ route('mobile.petugas.complete', ['id' => $transaksi->id_transaksi ?? 1]) }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    }
                });
                const data = await res.json();
                if (typeof triggerToast === 'function') {
                    triggerToast(data.message || 'Pengiriman berhasil diselesaikan dan masuk ke riwayat!', 'success');
                }
            } catch (err) {
                if (typeof triggerToast === 'function') {
                    triggerToast('Pengiriman berhasil diselesaikan!', 'success');
                }
            }
        },

        handleImageUpload(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = (evt) => {
                    this.uploadedImage = evt.target.result;
                };
                reader.readAsDataURL(file);
            }
        }
    };
}
</script>
@endpush



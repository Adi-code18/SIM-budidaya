@extends('mobile_web_petugas.petugas_distribusi.layout')

@section('title', 'Detail & Tracking Pengiriman - AMS BUDIDAYA Mobile')

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
        <div class="flex items-center justify-between">
            <span class="text-[10px] font-extrabold uppercase text-slate-400 tracking-wider">STATUS PENGIRIMAN</span>
            @if($transaksi->status_order === 'pending')
                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold bg-amber-100 text-amber-800 border border-amber-200 flex items-center gap-1">
                    <i class="fa-solid fa-clock"></i> Pending (Persiapan)
                </span>
            @elseif($transaksi->status_order === 'pemberokian')
                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold bg-sky-100 text-sky-800 border border-sky-200 flex items-center gap-1">
                    <i class="fa-solid fa-water"></i> Dalam Pemberokian
                </span>
            @elseif($transaksi->status_order === 'siap_kirim')
                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold bg-indigo-100 text-indigo-800 border border-indigo-200 flex items-center gap-1">
                    <i class="fa-solid fa-box-check"></i> Siap Kirim
                </span>
            @elseif($transaksi->status_order === 'dalam_pengiriman' || $transaksi->status_order === 'dikirim')
                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold bg-blue-100 text-blue-800 border border-blue-200 flex items-center gap-1 animate-pulse">
                    <i class="fa-solid fa-truck-fast"></i> Sedang Diperjalanan
                </span>
            @elseif($transaksi->status_order === 'selesai')
                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold bg-emerald-100 text-emerald-800 border border-emerald-200 flex items-center gap-1">
                    <i class="fa-solid fa-circle-check"></i> Telah Selesai
                </span>
            @else
                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold bg-slate-100 text-slate-800 border border-slate-200">
                    {{ ucfirst($transaksi->status_order) }}
                </span>
            @endif
        </div>
        
        <!-- Timeline Steps -->
        <div class="flex items-center justify-between relative px-2 py-2">
            
            <!-- Connecting Line Base -->
            <div class="absolute left-6 right-6 top-5 h-1 bg-slate-200 -z-0"></div>
            @php
                $statusOrder = $transaksi->status_order ?? 'pending';
                $progressWidth = '0%';
                if ($statusOrder === 'pemberokian') $progressWidth = '33%';
                elseif ($statusOrder === 'siap_kirim') $progressWidth = '66%';
                elseif (in_array($statusOrder, ['dalam_pengiriman', 'dikirim'])) $progressWidth = '85%';
                elseif ($statusOrder === 'selesai') $progressWidth = '100%';
            @endphp
            <div class="absolute left-6 top-5 h-1 bg-navy-800 transition-all duration-500 -z-0" style="width: {{ $progressWidth }}"></div>

            <!-- Step 1: Pending / Persiapan -->
            <div class="flex flex-col items-center gap-1.5 relative z-10">
                <div class="w-7 h-7 rounded-full {{ in_array($statusOrder, ['pending', 'pemberokian', 'siap_kirim', 'dalam_pengiriman', 'dikirim', 'selesai']) ? 'bg-navy-800 text-white' : 'bg-slate-200 text-slate-500' }} flex items-center justify-center text-xs font-bold ring-4 ring-white shadow-xs">
                    <i class="fa-solid fa-hourglass-start text-[10px]"></i>
                </div>
                <span class="text-[9px] font-bold text-navy-900">Pending</span>
            </div>

            <!-- Step 2: Pemberokian -->
            <div class="flex flex-col items-center gap-1.5 relative z-10">
                <div class="w-7 h-7 rounded-full {{ in_array($statusOrder, ['pemberokian', 'siap_kirim', 'dalam_pengiriman', 'dikirim', 'selesai']) ? 'bg-navy-800 text-white' : 'bg-slate-200 text-slate-500' }} flex items-center justify-center text-xs font-bold ring-4 ring-white shadow-xs">
                    <i class="fa-solid fa-water text-[10px]"></i>
                </div>
                <span class="text-[9px] font-bold text-navy-900">Pemberokan</span>
            </div>

            <!-- Step 3: Siap Kirim -->
            <div class="flex flex-col items-center gap-1.5 relative z-10">
                <div class="w-7 h-7 rounded-full {{ in_array($statusOrder, ['siap_kirim', 'dalam_pengiriman', 'dikirim', 'selesai']) ? 'bg-navy-800 text-white' : 'bg-slate-200 text-slate-500' }} flex items-center justify-center text-xs font-bold ring-4 ring-white shadow-xs">
                    <i class="fa-solid fa-box text-[10px]"></i>
                </div>
                <span class="text-[9px] font-bold text-navy-900">Siap Kirim</span>
            </div>

            <!-- Step 4: Kirim -->
            <div class="flex flex-col items-center gap-1.5 relative z-10">
                <div class="w-7 h-7 rounded-full {{ in_array($statusOrder, ['dalam_pengiriman', 'dikirim', 'selesai']) ? 'bg-navy-800 text-white' : 'bg-slate-200 text-slate-500' }} flex items-center justify-center text-xs font-bold ring-4 ring-white shadow-xs">
                    <i class="fa-solid fa-truck-fast text-[10px]"></i>
                </div>
                <span class="text-[9px] font-bold text-navy-900">Kirim</span>
            </div>

            <!-- Step 5: Selesai -->
            <div class="flex flex-col items-center gap-1.5 relative z-10">
                <div class="w-7 h-7 rounded-full flex items-center justify-center text-xs font-bold ring-4 ring-white shadow-xs transition-all {{ $statusOrder === 'selesai' ? 'bg-emerald-600 text-white' : 'bg-slate-200 text-slate-500' }}">
                    <i class="fa-solid {{ $statusOrder === 'selesai' ? 'fa-check text-[10px]' : 'fa-flag-checkered text-[10px]' }}"></i>
                </div>
                <span class="text-[9px] font-bold {{ $statusOrder === 'selesai' ? 'text-emerald-700 font-extrabold' : 'text-slate-400' }}">Selesai</span>
            </div>

        </div>
    </div>

    <!-- Main Item Information Card -->
    <div class="bg-white rounded-2xl border border-slate-200/90 p-4 shadow-xs flex items-center justify-between">
        <div>
            <span class="text-[10px] font-extrabold text-slate-400 uppercase block tracking-wider">KOMODITAS & ID ORDER</span>
            <h2 class="text-base font-extrabold text-navy-900 mt-0.5">{{ $transaksi->batchPembesaran ? $transaksi->batchPembesaran->jenis_ikan : 'Ikan Konsumsi Segar' }}</h2>
            <div class="flex items-center gap-1.5 text-xs text-slate-500 font-semibold mt-0.5">
                <span>ID: <strong class="text-navy-800">#ORD-{{ str_pad($transaksi->id_transaksi, 4, '0', STR_PAD_LEFT) }}</strong></span>
                <span class="text-slate-300">•</span>
                <span class="text-sky-700 font-bold flex items-center gap-1">
                    <i class="fa-solid fa-warehouse text-[10px]"></i>
                    <span>{{ $transaksi->batchPembesaran && $transaksi->batchPembesaran->kolam ? $transaksi->batchPembesaran->kolam->nama_kolam : 'Kolam Pembesaran / Buffer Pemberokan' }}</span>
                </span>
            </div>
        </div>
        <div class="px-3.5 py-2 rounded-xl bg-sky-50 border border-sky-200 text-sky-800 font-extrabold text-sm text-center shadow-2xs">
            <span>{{ number_format($transaksi->Total_kg, 0, ',', '.') }} KG</span>
            <span class="text-[9px] font-bold block text-sky-600">Netto Muat</span>
        </div>
    </div>

    @php
        $mitra = $transaksi->mitra;
        $mitraNama = $mitra ? $mitra->nama_mitra : 'Mitra Distribusi';
        $mitraAlamat = $mitra && $mitra->alamat ? $mitra->alamat : 'Kota Tasikmalaya, Jawa Barat';
        
        // Cek koordinat presisi
        $hasCoords = $mitra && !empty($mitra->latitude) && !empty($mitra->longitude);
        $lat = $hasCoords ? (float) $mitra->latitude : -7.3274;
        $lng = $hasCoords ? (float) $mitra->longitude : 108.2207;
        $coordDisplay = number_format($lat, 6) . ', ' . number_format($lng, 6);
        
        $mapQuery = $hasCoords ? ($lat . ',' . $lng) : urlencode($mitraAlamat);
        $embedMapUrl = "https://maps.google.com/maps?q={$mapQuery}&z=15&output=embed";
        $navDirectionUrl = "https://www.google.com/maps/dir/?api=1&destination={$mapQuery}";
    @endphp

    <!-- Destination Address Box -->
    <div class="bg-white rounded-2xl border border-slate-200/90 p-4 shadow-xs space-y-2.5">
        <div class="flex items-center gap-2">
            <div class="w-6 h-6 rounded-lg bg-rose-100 text-rose-600 flex items-center justify-center text-xs">
                <i class="fa-solid fa-location-dot"></i>
            </div>
            <span class="text-xs font-extrabold text-slate-900">Tujuan Pengiriman</span>
        </div>

        <div class="bg-slate-50 p-3 rounded-xl border border-slate-100 text-xs text-slate-700 leading-relaxed font-medium">
            <strong class="text-slate-900 block font-bold text-xs mb-1">{{ $mitraNama }} ({{ $mitra ? ($mitra->penanggung_jawab ?? 'Penerima') : 'Penerima' }})</strong>
            {{ $mitraAlamat }}
        </div>
    </div>

    <!-- STATUS WORKFLOW CONDITIONALS -->

    <!-- Case 1: Status PENDING atau PEMBEROKIAN -->
    @if(in_array($transaksi->status_order, ['pending', 'pemberokian']))
        <div class="p-4 rounded-2xl bg-amber-50/90 border border-amber-200 text-amber-900 space-y-3">
            <div class="flex items-start gap-3">
                <div class="w-8 h-8 rounded-xl bg-amber-200 text-amber-800 flex items-center justify-center shrink-0 mt-0.5">
                    <i class="fa-solid fa-lock text-sm"></i>
                </div>
                <div>
                    <h4 class="font-extrabold text-xs text-amber-900">
                        @if($transaksi->status_order === 'pending')
                            Pesanan Masih Dalam Persiapan (Pending)
                        @else
                            Ikan Masih Dalam Proses Pemberokan
                        @endif
                    </h4>
                    <p class="text-[11px] text-amber-800 mt-1 leading-relaxed">
                        @if($transaksi->status_order === 'pending')
                            Menunggu verifikasi dan persiapan muatan dari manajer operasional. Navigasi dan konfirmasi kirim masih dikunci.
                        @else
                            Ikan sedang dikarantina/dibersihkan di kolam pemberokan. Tombol kirim akan aktif otomatis saat status berubah menjadi <strong>Siap Kirim</strong>.
                        @endif
                    </p>
                </div>
            </div>
            
            <div class="pt-1">
                <a href="{{ route('mobile.petugas.pengiriman') }}" 
                   class="w-full py-2.5 rounded-xl bg-white border border-amber-300 text-amber-900 font-bold text-xs flex items-center justify-center gap-2 shadow-2xs hover:bg-amber-100 transition-colors">
                    <i class="fa-solid fa-arrow-left text-xs"></i>
                    <span>Kembali ke Daftar Tugas</span>
                </a>
            </div>
        </div>

    <!-- Case 2: Status SIAP KIRIM (Tampilkan Tombol Konfirmasi Kirim Sekarang) -->
    @elseif($transaksi->status_order === 'siap_kirim')
        <div class="p-4 rounded-2xl bg-indigo-50/90 border border-indigo-200 text-indigo-950 space-y-3">
            <div class="flex items-start gap-3">
                <div class="w-8 h-8 rounded-xl bg-indigo-600 text-white flex items-center justify-center shrink-0 mt-0.5 shadow-sm">
                    <i class="fa-solid fa-truck-ramp-box text-sm"></i>
                </div>
                <div>
                    <h4 class="font-extrabold text-xs text-indigo-950">Pesanan Siap Dikirim!</h4>
                    <p class="text-[11px] text-indigo-800 mt-0.5 leading-relaxed">
                        Ikan sudah dimuat ke armada. Tekan tombol di bawah untuk mulai perjalanan dan membuka rute navigasi Google Maps.
                    </p>
                </div>
            </div>

            <form action="{{ route('mobile.petugas.startDelivery', ['id' => $transaksi->id_transaksi]) }}" method="POST">
                @csrf
                <button type="submit" 
                        class="w-full py-3.5 rounded-2xl bg-gradient-to-r from-navy-800 via-navy-900 to-sky-900 text-white font-extrabold text-xs flex items-center justify-center gap-2.5 shadow-md hover:shadow-lg transition-all active:scale-[0.98]">
                    <i class="fa-solid fa-paper-plane text-sky-300 text-sm"></i>
                    <span>Konfirmasi Mulai Kirim Sekarang & Buka Navigasi</span>
                </button>
            </form>
        </div>

    <!-- Case 3: Status DALAM PENGIRIMAN -->
    @elseif(in_array($transaksi->status_order, ['dalam_pengiriman', 'dikirim']))
        
        <!-- Notice Wajib Upload Foto -->
        <div class="p-3.5 rounded-2xl bg-sky-50 border border-sky-200 text-sky-900 flex items-center justify-between gap-3">
            <div class="flex items-center gap-2.5">
                <div class="w-7 h-7 rounded-lg bg-sky-600 text-white flex items-center justify-center text-xs shrink-0">
                    <i class="fa-solid fa-camera"></i>
                </div>
                <div class="text-[11px] leading-tight">
                    <strong class="font-extrabold block text-navy-900">Wajib Foto Serah Terima</strong>
                    <span class="text-sky-700" x-text="uploadedImage ? 'Foto sudah terlampir. Anda siap menyelesaikan pengiriman!' : 'Ambil foto fisik serah terima sebelum menandai selesai.'"></span>
                </div>
            </div>
            <template x-if="uploadedImage">
                <span class="px-2 py-1 bg-emerald-100 text-emerald-800 font-extrabold text-[10px] rounded-lg border border-emerald-300 shrink-0">
                    ✓ Terpasang
                </span>
            </template>
        </div>

        <!-- Interactive Action Buttons Stack -->
        <div class="space-y-2.5 pt-1">
            
            <!-- Button 1: Navigasi via Maps -->
            <button @click="mapModal = true" 
                    class="w-full py-3 rounded-2xl bg-navy-800 hover:bg-navy-900 active:scale-[0.99] text-white font-bold text-xs flex items-center justify-center gap-2.5 shadow-sm transition-all">
                <i class="fa-solid fa-map-location-dot text-sm text-sky-300"></i>
                <span>Navigasi via Maps</span>
            </button>

            <!-- Button 2: Upload Foto Serah Terima -->
            <button @click="uploadModal = true" 
                    class="w-full py-3 rounded-2xl border active:scale-[0.99] font-bold text-xs flex items-center justify-center gap-2.5 shadow-2xs transition-all"
                    :class="uploadedImage ? 'bg-emerald-50 border-emerald-300 text-emerald-800' : 'bg-white border-slate-300 text-slate-800 hover:bg-slate-50'">
                <i class="fa-solid fa-camera text-sm" :class="uploadedImage ? 'text-emerald-600' : 'text-sky-600'"></i>
                <span x-text="uploadedImage ? 'Foto Serah Terima Terlampir (Klik Ubah)' : 'Ambil / Upload Foto Serah Terima *'">Ambil / Upload Foto Serah Terima *</span>
            </button>

        </div>

        <!-- Sticky Bottom Slide-to-Confirm Action Bar -->
        <div class="pt-2">
            <template x-if="!deliveryDone">
                <div>
                    <!-- Warning Prompt when user tries without photo -->
                    <div x-show="!uploadedImage" class="text-center pb-2">
                        <span class="text-[10px] font-bold text-amber-700 bg-amber-50 px-3 py-1 rounded-full border border-amber-200 inline-flex items-center gap-1.5">
                            <i class="fa-solid fa-lock text-[9px]"></i> Geser akan aktif setelah Foto Serah Terima diunggah
                        </span>
                    </div>

                    <div class="relative w-full h-14 rounded-2xl p-1.5 flex items-center select-none overflow-hidden shadow-lg border transition-colors"
                         :class="uploadedImage ? 'bg-[#0F2C59] border-sky-950/40 cursor-grab' : 'bg-slate-700/80 border-slate-600 cursor-not-allowed opacity-80'"
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
                                <div class="w-5 h-5 rounded-full flex items-center justify-center text-white text-[10px]"
                                     :class="uploadedImage ? 'bg-white/20' : 'bg-amber-500/50'">
                                    <i class="fa-solid" :class="uploadedImage ? 'fa-circle-check' : 'fa-lock'"></i>
                                </div>
                                <span x-text="uploadedImage ? 'Tandai Barang Diterima' : 'Kunci: Upload Foto Dulu'">Tandai Barang Diterima</span>
                                <i x-show="uploadedImage" class="fa-solid fa-angles-right text-[10px] text-sky-300 animate-pulse"></i>
                            </div>
                        </div>

                        <!-- Draggable Slider Handle (Thumb) with Pointer Capture -->
                        <div x-ref="sliderThumb"
                             @pointerdown="startDrag($event)"
                             @pointermove="onDrag($event)"
                             @pointerup="endDrag($event)"
                             @pointercancel="endDrag($event)"
                             class="relative z-10 w-11 h-11 rounded-xl flex items-center justify-center text-white shadow-md select-none transition-transform duration-150"
                             :class="[
                                 uploadedImage ? 'bg-sky-500 hover:bg-sky-400 cursor-grab active:cursor-grabbing' : 'bg-slate-500 cursor-not-allowed',
                                 isDragging ? 'shadow-sky-500/60 scale-105' : 'transition-transform duration-300 ease-out'
                             ]"
                             :style="'transform: translateX(' + currentX + 'px); touch-action: none;'">
                            <i class="fa-solid" :class="!uploadedImage ? 'fa-lock text-xs' : (progress > 0.7 ? 'fa-check text-base' : 'fa-arrow-right text-xs')"></i>
                        </div>

                        <!-- Right Hint Badge -->
                        <div class="absolute right-4 text-[9px] font-extrabold text-sky-200/60 pointer-events-none uppercase tracking-wider flex items-center gap-1"
                             :style="'opacity: ' + Math.max(0, 1 - progress * 2)">
                            <span x-text="uploadedImage ? 'GESER' : 'TERKUNCI'">GESER</span>
                            <i class="fa-solid fa-chevron-right text-[8px]"></i>
                        </div>

                    </div>
                </div>
            </template>

            <template x-if="deliveryDone">
                <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-center space-y-2">
                    <div class="w-10 h-10 rounded-full bg-emerald-600 text-white mx-auto flex items-center justify-center text-lg shadow-sm">
                        <i class="fa-solid fa-check"></i>
                    </div>
                    <h3 class="font-extrabold text-emerald-900 text-sm">Pengiriman Berhasil Diselesaikan!</h3>
                    <p class="text-xs text-emerald-700 font-medium">Status order #ORD-{{ str_pad($transaksi->id_transaksi, 4, '0', STR_PAD_LEFT) }} telah selesai dan tersimpan ke riwayat.</p>
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

    <!-- Case 4: Status SELESAI -->
    @elseif($transaksi->status_order === 'selesai')
        <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 space-y-3">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl bg-emerald-600 text-white flex items-center justify-center text-base shrink-0 shadow-xs">
                    <i class="fa-solid fa-circle-check"></i>
                </div>
                <div>
                    <h4 class="font-extrabold text-xs text-emerald-900">Pengiriman Selesai Diterima</h4>
                    <p class="text-[11px] text-emerald-700">Barang telah diserahterimakan kepada mitra.</p>
                </div>
            </div>

            @if($transaksi->Bukti_sampai)
                <div class="pt-2 space-y-1">
                    <span class="text-[10px] font-extrabold text-slate-500 uppercase tracking-wider block">Foto Bukti Serah Terima:</span>
                    <img src="{{ asset('storage/' . $transaksi->Bukti_sampai) }}" 
                         alt="Bukti Serah Terima" 
                         class="w-full h-44 object-cover rounded-2xl border border-emerald-200 shadow-xs">
                </div>
            @endif

            <div class="pt-2 flex items-center gap-2">
                <a href="{{ route('mobile.petugas.riwayat') }}" class="flex-1 py-2.5 rounded-xl bg-emerald-700 hover:bg-emerald-800 text-white font-bold text-xs text-center shadow-xs transition-colors">
                    Lihat di Riwayat
                </a>
                <a href="{{ route('mobile.petugas.pengiriman') }}" class="flex-1 py-2.5 rounded-xl bg-white border border-slate-300 text-slate-700 font-bold text-xs text-center hover:bg-slate-50 transition-colors">
                    Daftar Tugas
                </a>
            </div>
        </div>
    @endif

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
                    <iframe width="100%" height="100%" frameborder="0" scrolling="no" marginheight="0" marginwidth="0" 
                            src="{{ $embedMapUrl }}">
                    </iframe>
                </div>

                <div class="bg-slate-50 p-3 rounded-xl text-xs space-y-1">
                    <span class="text-slate-400 font-extrabold uppercase text-[9px] block">LOKASI &amp; KOORDINAT TUJUAN</span>
                    <p class="font-bold text-slate-800">{{ $coordDisplay }}</p>
                    <p class="text-[11px] text-slate-600 font-medium">{{ $mitraAlamat }}</p>
                </div>

                <a href="{{ $navDirectionUrl }}" target="_blank"
                   class="w-full py-2.5 rounded-xl bg-navy-800 hover:bg-navy-900 text-white font-bold text-xs flex items-center justify-center gap-2 shadow-xs transition-colors">
                    <i class="fa-solid fa-diamond-turn-right text-sky-400 text-xs"></i>
                    <span>Buka Rute di Google Maps</span>
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
                    <h3 class="text-xs font-bold">Bukti Foto Serah Terima Fisik</h3>
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
        isSubmitting: false,
        
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
            if (this.deliveryDone || this.isSubmitting) return;

            // VALIDASI: Wajib foto terlebih dahulu
            if (!this.uploadedImage) {
                if (typeof triggerToast === 'function') {
                    triggerToast('Harap ambil/upload Foto Serah Terima terlebih dahulu!', 'warning');
                } else {
                    alert('Harap ambil/upload Foto Serah Terima terlebih dahulu!');
                }
                this.uploadModal = true;
                return;
            }

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
            if (!this.isDragging || this.deliveryDone || !this.uploadedImage) return;
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

            if (!this.uploadedImage) {
                this.resetSlider();
                return;
            }

            if (this.progress >= 0.65) {
                this.currentX = this.maxSlide;
                this.progress = 1;
                this.confirmArrived();
            } else {
                this.resetSlider();
            }
        },

        resetSlider() {
            this.isDragging = false;
            this.currentX = 0;
            this.progress = 0;
        },

        async confirmArrived() {
            if (!this.uploadedImage) {
                if (typeof triggerToast === 'function') {
                    triggerToast('Foto bukti serah terima wajib diunggah!', 'error');
                }
                this.resetSlider();
                this.uploadModal = true;
                return;
            }

            this.isSubmitting = true;

            try {
                const res = await fetch('{{ route('mobile.petugas.complete', ['id' => $transaksi->id_transaksi ?? 1]) }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        foto_base64: this.uploadedImage
                    })
                });

                const data = await res.json();
                
                if (res.ok && data.success) {
                    this.deliveryDone = true;
                    if (typeof triggerToast === 'function') {
                        triggerToast(data.message || 'Pengiriman berhasil diselesaikan dan masuk ke riwayat!', 'success');
                    }
                } else {
                    this.resetSlider();
                    if (typeof triggerToast === 'function') {
                        triggerToast(data.message || 'Gagal menyelesaikan pengiriman.', 'error');
                    } else {
                        alert(data.message || 'Gagal menyelesaikan pengiriman.');
                    }
                }
            } catch (err) {
                this.resetSlider();
                if (typeof triggerToast === 'function') {
                    triggerToast('Terjadi kesalahan saat memproses data.', 'error');
                }
            } finally {
                this.isSubmitting = false;
            }
        },

        handleImageUpload(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = (evt) => {
                    this.uploadedImage = evt.target.result;
                    if (typeof triggerToast === 'function') {
                        triggerToast('Foto serah terima berhasil diunggah! Sekarang Anda dapat menggeser tombol konfirmasi.', 'success');
                    }
                };
                reader.readAsDataURL(file);
            }
        }
    };
}
</script>
@endpush



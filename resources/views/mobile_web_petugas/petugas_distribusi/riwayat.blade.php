@extends('mobile_web_petugas.petugas_distribusi.layout')

@section('title', 'Riwayat Pengiriman - SIM-BUDIDAYA Mobile')

@section('content')
<div class="p-4 space-y-4" x-data="{ 
    selectedReceipt: null,
    proofModal: false,
    selectedImage: ''
}">

    <!-- Content Title Banner -->
    <div class="bg-white rounded-2xl border border-slate-200/90 p-4 shadow-xs flex items-center justify-between">
        <div>
            <span class="text-[10px] font-extrabold uppercase text-sky-600 tracking-wider">REKAP LOGISTIK</span>
            <h1 class="text-lg font-extrabold text-navy-900">Riwayat Pengiriman</h1>
            <p class="text-xs text-slate-500 font-medium mt-0.5">Status pengiriman yang telah diselesaikan.</p>
        </div>
        <div class="w-10 h-10 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-700 flex items-center justify-center font-bold shadow-2xs">
            <i class="fa-solid fa-square-check text-lg"></i>
        </div>
    </div>

    <!-- Filter & Search Summary Bar -->
    <div class="flex items-center justify-between">
        <span class="text-xs font-extrabold text-slate-600">Total {{ isset($riwayats) ? count($riwayats) : 0 }} Pengiriman Selesai</span>
        <span class="px-2.5 py-1 rounded-full text-[10px] font-extrabold bg-emerald-100 text-emerald-800 border border-emerald-200 flex items-center gap-1">
            <i class="fa-solid fa-circle-check text-[9px]"></i> Filter: Selesai
        </span>
    </div>

    <!-- History Cards List -->
    <div class="space-y-3.5">
        @if(isset($riwayats) && count($riwayats) > 0)
            @foreach($riwayats as $r)
                <div class="bg-white rounded-2xl border border-slate-200/90 p-4 shadow-xs space-y-3 hover:shadow-md transition-all">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-bold text-navy-800 tracking-tight">#ORD-{{ str_pad($r->id_transaksi, 4, '0', STR_PAD_LEFT) }}</span>
                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold bg-emerald-50 text-emerald-700 border border-emerald-200 flex items-center gap-1">
                            <i class="fa-solid fa-check text-[9px]"></i>
                            Selesai
                        </span>
                    </div>

                    <div class="space-y-1">
                        <h3 class="text-xs font-extrabold text-slate-900">{{ $r->mitra ? $r->mitra->nama_mitra : 'Mitra Distribusi' }}</h3>
                        <p class="text-[11px] text-slate-500 font-medium">{{ $r->mitra ? $r->mitra->alamat : 'Kota Mataram' }} • <span class="font-bold text-slate-700">{{ number_format($r->Total_kg, 0, ',', '.') }} KG</span></p>
                        <p class="text-[10px] text-slate-400 font-medium"><i class="fa-regular fa-calendar-check mr-1"></i>Tanggal Order: {{ $r->tanggal_order }}</p>
                    </div>

                    <div class="pt-2 border-t border-slate-100 flex items-center gap-2">
                        <button @click="selectedImage = 'https://images.unsplash.com/photo-1544551763-46a013bb70d5?w=500&auto=format&fit=crop&q=60'; proofModal = true"
                                class="flex-1 py-2 rounded-xl border border-navy-800 text-navy-800 hover:bg-navy-50 font-bold text-xs flex items-center justify-center gap-1.5 transition-all">
                            <i class="fa-regular fa-image text-xs"></i>
                            <span>Bukti Foto</span>
                        </button>
                    </div>
                </div>
            @endforeach
        @else
            <!-- Empty State -->
            <div class="bg-white rounded-2xl border border-slate-200/90 p-8 text-center space-y-3">
                <div class="w-12 h-12 rounded-2xl bg-slate-100 text-slate-400 mx-auto flex items-center justify-center text-xl">
                    <i class="fa-solid fa-box-archive"></i>
                </div>
                <div>
                    <h3 class="text-sm font-bold text-slate-800">Belum Ada Riwayat</h3>
                    <p class="text-xs text-slate-400 mt-0.5">Pengiriman yang telah diselesaikan akan tercatat di sini.</p>
                </div>
            </div>
        @endif

    </div>

    <!-- Proof Photo View Modal -->
    <div x-show="proofModal" 
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 bg-slate-900/70 backdrop-blur-xs flex items-center justify-center p-4"
         style="display: none;">
        
        <div class="bg-white rounded-3xl w-full max-w-sm overflow-hidden shadow-2xl space-y-3 p-4 text-center">
            <div class="flex items-center justify-between border-b border-slate-100 pb-2">
                <h3 class="text-xs font-bold text-navy-900">Foto Serah Terima</h3>
                <button @click="proofModal = false" class="text-slate-400 hover:text-slate-600">
                    <i class="fa-solid fa-xmark text-sm"></i>
                </button>
            </div>

            <img :src="selectedImage" class="w-full h-56 object-cover rounded-2xl border border-slate-200 shadow-xs">
            
            <p class="text-[11px] text-slate-500 font-medium">Foto bukti penerimaan barang oleh mitra distributor.</p>
            
            <button @click="proofModal = false" class="w-full py-2.5 rounded-xl bg-navy-800 text-white font-bold text-xs">
                Tutup
            </button>
        </div>
    </div>

</div>
@endsection

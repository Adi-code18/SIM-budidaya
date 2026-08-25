@extends('mobile_web_petugas.petugas_pembesaran.layout')

@section('title', 'Input Batch Pembesaran - SIM-BUDIDAYA Mobile')

@section('content')
<div class="p-4 space-y-4" x-data="{
    idPembesaran: 'PB-2026-BG-101',
    tanggalTebar: new Date().toISOString().split('T')[0],
    jenisIkan: '',
    kolamTebar: '',
    sumberBenih: 'Hatchery Internal',
    biomassaAwal: 0,
    targetPanenTgl: '',
    targetPanenKg: 1000,
    handleSubmit() {
        if (!this.jenisIkan || !this.kolamTebar) {
            triggerToast('Mohon pilih Jenis Ikan dan Kolam Tebar!', 'error');
            return;
        }
        triggerToast('Siklus pembesaran ' + this.idPembesaran + ' berhasil dimulai!', 'success');
        setTimeout(() => {
            window.location.href = '{{ route('petugas.pembesaran.dashboard') }}';
        }, 1200);
    }
}">

    <!-- Title Header Box Card -->
    <div class="bg-white rounded-3xl border border-slate-200/90 p-4 shadow-xs space-y-1">
        <h1 class="text-base font-extrabold text-navy-900">Batch Pembesaran</h1>
        <p class="text-xs text-slate-500 font-medium">Input data untuk memulai siklus pembesaran baru.</p>
    </div>

    <!-- Main Form Card -->
    <div class="bg-white rounded-3xl border border-slate-200/90 p-5 shadow-xs">
        
        <form @submit.prevent="handleSubmit()" class="space-y-4">
            
            <!-- Field 1: ID Pembesaran -->
            <div class="space-y-1">
                <label class="text-[10px] font-extrabold uppercase text-slate-500 tracking-wider block">ID PEMBESARAN</label>
                <input type="text" x-model="idPembesaran" readonly
                       class="w-full px-3.5 py-2.5 rounded-2xl border border-slate-200 text-xs font-extrabold text-slate-500 bg-slate-100 cursor-not-allowed">
                <span class="text-[10px] text-slate-400 font-medium block italic">*ID di generate otomatis oleh sistem.</span>
            </div>

            <!-- Field 2: Tanggal Tebar -->
            <div class="space-y-1">
                <label class="text-[10px] font-extrabold uppercase text-slate-500 tracking-wider block">TANGGAL TEBAR</label>
                <input type="date" x-model="tanggalTebar"
                       class="w-full px-3.5 py-2.5 rounded-2xl border border-slate-200 text-xs font-semibold text-slate-800 bg-slate-50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-navy-800">
            </div>

            <!-- Field 3: Jenis Ikan -->
            <div class="space-y-1">
                <label class="text-[10px] font-extrabold uppercase text-slate-500 tracking-wider block">JENIS IKAN *</label>
                <select x-model="jenisIkan" 
                        class="w-full px-3.5 py-2.5 rounded-2xl border border-slate-200 text-xs font-semibold text-slate-800 bg-slate-50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-navy-800">
                    <option value="">Pilih Komoditas...</option>
                    <option value="Ikan Nila Merah">Ikan Nila Merah</option>
                    <option value="Ikan Nila Hitam">Ikan Nila Hitam</option>
                    <option value="Ikan Gurame Soang">Ikan Gurame Soang</option>
                    <option value="Ikan Lele Sangkuriang">Ikan Lele Sangkuriang</option>
                    <option value="Ikan Patin">Ikan Patin</option>
                </select>
            </div>

            <!-- Field 4: Kolam Tebar -->
            <div class="space-y-1">
                <label class="text-[10px] font-extrabold uppercase text-slate-500 tracking-wider block">KOLAM TEBAR *</label>
                <select x-model="kolamTebar" 
                        class="w-full px-3.5 py-2.5 rounded-2xl border border-slate-200 text-xs font-semibold text-slate-800 bg-slate-50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-navy-800">
                    <option value="">Pilih kolam pembesaran...</option>
                    @if(isset($kolams))
                        @foreach($kolams as $k)
                            <option value="{{ $k->nama_kolam }}">{{ $k->nama_kolam }} (Kapasitas: {{ number_format($k->kapasitas, 0, ',', '.') }} Ekor)</option>
                        @endforeach
                    @else
                        <option value="Kolam A-01">Kolam A-01</option>
                        <option value="Kolam B-03">Kolam B-03</option>
                    @endif
                </select>
            </div>

            <!-- Field 5: Sumber Benih -->
            <div class="space-y-2 pt-1">
                <label class="text-[10px] font-extrabold uppercase text-slate-500 tracking-wider block">SUMBER BENIH *</label>
                
                <div class="space-y-2">
                    <label class="flex items-start gap-2.5 p-3 rounded-2xl border border-slate-200 bg-slate-50 hover:bg-slate-100/80 cursor-pointer transition-colors">
                        <input type="radio" name="sumber" value="Hatchery Internal" x-model="sumberBenih" class="mt-0.5 accent-navy-800">
                        <div>
                            <span class="text-xs font-bold text-slate-900 block">Hatchery Internal</span>
                            <span class="text-[10px] text-slate-400 block font-medium">Dari unit pembibitan sendiri</span>
                        </div>
                    </label>

                    <label class="flex items-start gap-2.5 p-3 rounded-2xl border border-slate-200 bg-slate-50 hover:bg-slate-100/80 cursor-pointer transition-colors">
                        <input type="radio" name="sumber" value="Pemasok Eksternal" x-model="sumberBenih" class="mt-0.5 accent-navy-800">
                        <div>
                            <span class="text-xs font-bold text-slate-900 block">Pemasok Eksternal</span>
                            <span class="text-[10px] text-slate-400 block font-medium">Beli dari pihak luar</span>
                        </div>
                    </label>
                </div>
            </div>

            <!-- Field 6: Estimasi Biomassa Awal -->
            <div class="space-y-1">
                <label class="text-[10px] font-extrabold uppercase text-slate-500 tracking-wider block">ESTIMASI BIOMASSA AWAL *</label>
                <div class="flex items-center gap-2">
                    <input type="number" x-model="biomassaAwal" min="0"
                           class="flex-1 px-3.5 py-2.5 rounded-2xl border border-slate-200 text-xs font-semibold text-slate-800 bg-slate-50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-navy-800">
                    <span class="text-xs font-bold text-slate-500 px-2">Kg</span>
                </div>
            </div>

            <!-- Field 7: Target Panen (Tanggal & Kg) -->
            <div class="space-y-3 pt-1">
                <div class="space-y-1">
                    <label class="text-[10px] font-extrabold uppercase text-slate-500 tracking-wider block">TARGET TANGGAL PANEN</label>
                    <input type="date" x-model="targetPanenTgl"
                           class="w-full px-3.5 py-2.5 rounded-2xl border border-slate-200 text-xs font-semibold text-slate-800 bg-slate-50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-navy-800">
                </div>

                <div class="space-y-1">
                    <label class="text-[10px] font-extrabold uppercase text-slate-500 tracking-wider block">TARGET PANEN (KG)</label>
                    <div class="flex items-center gap-2">
                        <input type="number" x-model="targetPanenKg" min="1"
                               class="flex-1 px-3.5 py-2.5 rounded-2xl border border-slate-200 text-xs font-semibold text-slate-800 bg-slate-50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-navy-800">
                        <span class="text-xs font-bold text-slate-500 px-2">Kg</span>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="pt-4 space-y-2">
                <button type="submit" 
                        class="w-full py-3.5 rounded-2xl bg-navy-800 hover:bg-navy-900 active:scale-[0.99] text-white font-extrabold text-xs shadow-md transition-all">
                    Mulai Siklus Pembesaran
                </button>
                <a href="{{ route('petugas.pembesaran.dashboard') }}" 
                   class="w-full py-3 rounded-2xl border border-slate-200 text-slate-600 hover:bg-slate-50 font-bold text-xs flex items-center justify-center transition-colors">
                    Batal
                </a>
            </div>

        </form>

    </div>

</div>
@endsection

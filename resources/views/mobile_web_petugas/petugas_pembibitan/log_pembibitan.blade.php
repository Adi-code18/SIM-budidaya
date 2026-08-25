@extends('mobile_web_petugas.petugas_pembibitan.layout')

@section('title', 'Log Pembibitan Baru - SIM-BUDIDAYA Mobile')

@section('content')
<div class="p-4 space-y-4" x-data="{
    batchId: 'BCH-2026-BG-201',
    jenisIkan: '',
    kolam: '',
    tanggalPenetasan: new Date().toISOString().split('T')[0],
    prediksiHari: 3,
    statusBatch: 'Proses Menetas',
    kematianTelur: 0,
    handleSubmit() {
        if (!this.jenisIkan || !this.kolam) {
            triggerToast('Mohon pilih Jenis Ikan dan Kolam Hatchery!', 'error');
            return;
        }
        triggerToast('Data pembibitan ' + this.batchId + ' berhasil disimpan!', 'success');
        setTimeout(() => {
            window.location.href = '{{ route('petugas.pembibitan.dashboard') }}';
        }, 1200);
    }
}">

    <!-- Top Blue Header Banner (Matches Screen 2 Mockup) -->
    <div class="bg-navy-800 rounded-3xl p-5 text-white shadow-md space-y-2 relative overflow-hidden">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-2xl bg-white/10 flex items-center justify-center text-sky-300">
                <i class="fa-solid fa-clipboard-check text-lg"></i>
            </div>
            <div>
                <h1 class="text-base font-extrabold text-white">Log Pembibitan Baru</h1>
                <p class="text-xs text-sky-200/80 font-medium">Catat parameter awal untuk pembibitan baru. Pastikan data sudah sesuai.</p>
            </div>
        </div>
    </div>

    <!-- Form Container Card -->
    <div class="bg-white rounded-3xl border border-slate-200/90 p-5 shadow-xs">
        
        <form @submit.prevent="handleSubmit()" class="space-y-4">
            
            <!-- Field 1: ID Batch -->
            <div class="space-y-1">
                <label class="text-[10px] font-extrabold uppercase text-slate-500 tracking-wider block">ID BATCH</label>
                <input type="text" x-model="batchId" readonly
                       class="w-full px-3.5 py-2.5 rounded-2xl border border-slate-200 text-xs font-extrabold text-slate-500 bg-slate-100 cursor-not-allowed">
            </div>

            <!-- Field 2: Jenis Ikan -->
            <div class="space-y-1">
                <label class="text-[10px] font-extrabold uppercase text-slate-500 tracking-wider block">JENIS IKAN</label>
                <select x-model="jenisIkan" 
                        class="w-full px-3.5 py-2.5 rounded-2xl border border-slate-200 text-xs font-semibold text-slate-800 bg-slate-50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-navy-800">
                    <option value="">Pilih varietas ikan...</option>
                    <option value="Ikan Nila Merah">Ikan Nila Merah</option>
                    <option value="Ikan Nila Hitam">Ikan Nila Hitam</option>
                    <option value="Ikan Gurame Soang">Ikan Gurame Soang</option>
                    <option value="Ikan Lele Sangkuriang">Ikan Lele Sangkuriang</option>
                    <option value="Ikan Patin">Ikan Patin</option>
                </select>
            </div>

            <!-- Field 3: Kolam / Tank Hatchery -->
            <div class="space-y-1">
                <label class="text-[10px] font-extrabold uppercase text-slate-500 tracking-wider block">PENETASAN HATCHERY</label>
                <select x-model="kolam" 
                        class="w-full px-3.5 py-2.5 rounded-2xl border border-slate-200 text-xs font-semibold text-slate-800 bg-slate-50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-navy-800">
                    <option value="">Pilih unit kolam...</option>
                    @if(isset($kolams))
                        @foreach($kolams as $k)
                            <option value="{{ $k->nama_kolam }}">{{ $k->nama_kolam }} ({{ $k->tipe_kolam }})</option>
                        @endforeach
                    @else
                        <option value="Kolam Pemijahan A-01">Kolam Pemijahan A-01 (Hatchery / Pemijahan)</option>
                        <option value="Kolam Penetasan B-02">Kolam Penetasan B-02 (Hatchery / Penetasan)</option>
                        <option value="Kolam Pembibitan L-03">Kolam Pembibitan L-03 (Hatchery / Pendederan)</option>
                    @endif
                </select>
            </div>

            <!-- Field 4: Tanggal Penetasan -->
            <div class="space-y-1">
                <label class="text-[10px] font-extrabold uppercase text-slate-500 tracking-wider block">TANGGAL PENETASAN</label>
                <input type="date" x-model="tanggalPenetasan"
                       class="w-full px-3.5 py-2.5 rounded-2xl border border-slate-200 text-xs font-semibold text-slate-800 bg-slate-50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-navy-800">
            </div>

            <!-- Field 5: Prediksi Waktu Menetas -->
            <div class="space-y-1">
                <label class="text-[10px] font-extrabold uppercase text-slate-500 tracking-wider block">PREDIKSI WAKTU MENETAS</label>
                <div class="flex items-center gap-2">
                    <input type="number" x-model="prediksiHari" min="1"
                           class="flex-1 px-3.5 py-2.5 rounded-2xl border border-slate-200 text-xs font-semibold text-slate-800 bg-slate-50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-navy-800">
                    <span class="text-xs font-bold text-slate-500 px-2">Hari</span>
                </div>
            </div>

            <!-- Field 6: Status Batch -->
            <div class="space-y-1">
                <label class="text-[10px] font-extrabold uppercase text-slate-500 tracking-wider block">STATUS BATCH</label>
                <select x-model="statusBatch" 
                        class="w-full px-3.5 py-2.5 rounded-2xl border border-slate-200 text-xs font-semibold text-slate-800 bg-slate-50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-navy-800">
                    <option value="Proses Menetas">Proses Menetas</option>
                    <option value="Fase Penyerapan">Fase Penyerapan</option>
                    <option value="Siap Tebar">Siap Tebar</option>
                </select>
            </div>

            <!-- Field 7: Jumlah Kematian Telur (Optional) -->
            <div class="space-y-1">
                <label class="text-[10px] font-extrabold uppercase text-slate-500 tracking-wider block">JUMLAH KEMATIAN TELUR (OPTIONAL)</label>
                <div class="flex items-center gap-2">
                    <input type="number" x-model="kematianTelur" min="0"
                           class="flex-1 px-3.5 py-2.5 rounded-2xl border border-slate-200 text-xs font-semibold text-slate-800 bg-slate-50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-navy-800">
                    <span class="text-xs font-bold text-slate-500 px-2">Ekor</span>
                </div>
            </div>

            <!-- Buttons -->
            <div class="pt-4 space-y-2">
                <button type="submit" 
                        class="w-full py-3.5 rounded-2xl bg-navy-800 hover:bg-navy-900 active:scale-[0.99] text-white font-extrabold text-xs shadow-md transition-all">
                    Simpan Data Pembibitan
                </button>
                <a href="{{ route('petugas.pembibitan.dashboard') }}" 
                   class="w-full py-3 rounded-2xl border border-slate-200 text-slate-600 hover:bg-slate-50 font-bold text-xs flex items-center justify-center transition-colors">
                    Batal
                </a>
            </div>

        </form>

    </div>

</div>
@endsection

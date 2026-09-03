@extends('mobile_web_petugas.petugas_pembibitan.layout')

@section('title', 'Log Pembibitan Baru - SIM-BUDIDAYA Mobile')

@section('content')
<div class="p-4 space-y-4" x-data="{
    batchId: 'BCH-{{ date('Y') }}-{{ rand(10, 99) }}',
    kolam: '',
    selectedIkanId: '',
    ikansList: {!! json_encode($ikans ?? []) !!},
    isEstLocked: true,
    tglPemijahan: new Date().toISOString().split('T')[0],
    estPrcsPembibitaan: '',
    jumlahBibitAwal: 100000,
    statusBatch: 'aktif',
    kematianTelur: 0,
    isSubmitting: false,

    onIkanSelected() {
        if (!this.selectedIkanId) return;
        const found = this.ikansList.find(i => String(i.id_ikan) === String(this.selectedIkanId));
        if (found && this.tglPemijahan) {
            const totalDays = Number(found.durasi_penetasan || 0) + Number(found.durasi_pembibitan || 0);
            const d = new Date(this.tglPemijahan);
            d.setDate(d.getDate() + totalDays);
            this.estPrcsPembibitaan = d.toISOString().split('T')[0];
            this.isEstLocked = true;
        }
    },

    async handleSubmit() {
        if (!this.kolam) {
            triggerToast('Mohon pilih Kolam Hatchery!', 'error');
            return;
        }
        this.isSubmitting = true;
        try {
            const res = await fetch('{{ route('petugas.pembibitan.batch.store') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    id_kolam: this.kolam,
                    id_ikan: this.selectedIkanId || null,
                    tgl_pemijahan: this.tglPemijahan,
                    est_prcs_pembibitaan: this.estPrcsPembibitaan,
                    jumlah_bibitAwal: this.jumlahBibitAwal,
                    status: this.statusBatch
                })
            });
            const data = await res.json();
            if (res.ok && data.success) {
                triggerToast('Data pembibitan ' + this.batchId + ' berhasil disimpan!', 'success');
                setTimeout(() => {
                    window.location.href = '{{ route('petugas.pembibitan.dashboard') }}';
                }, 1200);
            } else {
                triggerToast(data.message || 'Gagal menyimpan log pembibitan!', 'error');
            }
        } catch (e) {
            triggerToast('Terjadi kesalahan jaringan.', 'error');
        } finally {
            this.isSubmitting = false;
        }
    }
}">

    <!-- Top Blue Header Banner -->
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
                <label class="text-[10px] font-extrabold uppercase text-slate-500 tracking-wider block">ID BATCH SISTEM</label>
                <input type="text" x-model="batchId" readonly
                       class="w-full px-3.5 py-2.5 rounded-2xl border border-slate-200 text-xs font-extrabold text-slate-500 bg-slate-100 cursor-not-allowed">
            </div>

            <!-- Field 2: Spesies Ikan (SOP Otomatis) -->
            <div class="space-y-1">
                <label class="text-[10px] font-extrabold uppercase text-slate-500 tracking-wider block">PILIH SPESIES IKAN (SOP OTOMATIS)</label>
                <select x-model="selectedIkanId" 
                        @change="onIkanSelected()"
                        class="w-full px-3.5 py-2.5 rounded-2xl border border-slate-200 text-xs font-bold text-sky-800 bg-sky-50/60 focus:bg-white focus:outline-none focus:ring-2 focus:ring-navy-800">
                    <option value="">-- Manual / Tanpa SOP --</option>
                    @if(isset($ikans))
                        @foreach($ikans as $ik)
                            <option value="{{ $ik->id_ikan }}">{{ $ik->nama_ikan }} (SOP: {{ $ik->durasi_penetasan + $ik->durasi_pembibitan }} Hari)</option>
                        @endforeach
                    @endif
                </select>
            </div>

            <!-- Field 3: Kolam / Tank Hatchery -->
            <div class="space-y-1">
                <label class="text-[10px] font-extrabold uppercase text-slate-500 tracking-wider block">LOKASI KOLAM HATCHERY *</label>
                <select x-model="kolam" 
                        class="w-full px-3.5 py-2.5 rounded-2xl border border-slate-200 text-xs font-semibold text-slate-800 bg-slate-50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-navy-800">
                    <option value="">Pilih unit kolam...</option>
                    @if(isset($kolams))
                        @foreach($kolams as $k)
                            <option value="{{ $k->nama_kolam }}">{{ $k->nama_kolam }} ({{ $k->tipe_kolam }})</option>
                        @endforeach
                    @endif
                </select>
            </div>

            <!-- Field 4: Jumlah Bibit Awal -->
            <div class="space-y-1">
                <label class="text-[10px] font-extrabold uppercase text-slate-500 tracking-wider block">JUMLAH BIBIT / TELUR AWAL *</label>
                <div class="flex items-center gap-2">
                    <input type="number" x-model="jumlahBibitAwal" min="1"
                           class="flex-1 px-3.5 py-2.5 rounded-2xl border border-slate-200 text-xs font-semibold text-slate-800 bg-slate-50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-navy-800">
                    <span class="text-xs font-bold text-slate-500 px-2">Ekor/Btr</span>
                </div>
            </div>

            <!-- Field 5: Tanggal Pemijahan / Tebar -->
            <div class="space-y-1">
                <label class="text-[10px] font-extrabold uppercase text-slate-500 tracking-wider block">TANGGAL PEMIJAHAN / TEBAR AWAL *</label>
                <input type="date" x-model="tglPemijahan"
                       @change="onIkanSelected()"
                       class="w-full px-3.5 py-2.5 rounded-2xl border border-slate-200 text-xs font-semibold text-slate-800 bg-slate-50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-navy-800">
            </div>

            <!-- Field 6: Estimasi Selesai Pembibitan (Lock/Unlock) -->
            <div class="space-y-1">
                <div class="flex items-center justify-between">
                    <label class="text-[10px] font-extrabold uppercase text-slate-500 tracking-wider block">ESTIMASI SELESAI PEMBIBITAN</label>
                    <button type="button" 
                            @click="isEstLocked = !isEstLocked" 
                            class="text-[10px] font-extrabold flex items-center gap-1 transition-colors cursor-pointer"
                            :class="isEstLocked ? 'text-amber-600 hover:text-amber-700' : 'text-sky-600 hover:text-sky-700'">
                        <i class="fa-solid" :class="isEstLocked ? 'fa-lock text-amber-600' : 'fa-lock-open text-sky-600'"></i>
                        <span x-text="isEstLocked ? 'Buka Kunci' : 'Kunci SOP'"></span>
                    </button>
                </div>
                <div class="relative">
                    <input type="date" 
                           x-model="estPrcsPembibitaan"
                           :readonly="isEstLocked"
                           :class="isEstLocked ? 'bg-slate-100/90 text-slate-600 cursor-not-allowed border-slate-200' : 'bg-slate-50 text-slate-800 focus:bg-white focus:ring-2 focus:ring-navy-800'"
                           class="w-full px-3.5 py-2.5 rounded-2xl border text-xs font-semibold transition-all">
                </div>
                <span x-show="isEstLocked && selectedIkanId" class="text-[10px] font-semibold text-slate-400 mt-0.5 block">
                    🔒 Terkunci SOP (Klik <strong>Buka Kunci</strong> untuk edit manual)
                </span>
            </div>

            <!-- Buttons -->
            <div class="pt-4 space-y-2">
                <button type="submit" 
                        :disabled="isSubmitting"
                        class="w-full py-3.5 rounded-2xl bg-navy-800 hover:bg-navy-900 active:scale-[0.99] text-white font-extrabold text-xs shadow-md transition-all flex items-center justify-center gap-2 cursor-pointer">
                    <span x-text="isSubmitting ? 'Menyimpan...' : 'Simpan Data Pembibitan'"></span>
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

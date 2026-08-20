@extends('mobile_web_petugas.petugas_pembesaran.layout')

@section('title', 'Log Pakan Harian - SIM-BUDIDAYA Mobile')

@section('content')
<div class="p-4 space-y-4" x-data="{
    kolam: 'Kolam A-01',
    waktuTanggal: '2026-08-20T05:25',
    peletKg: 0.0,
    daunKg: 0.0,
    jenisDaun: 'Daun Talas, Kangkung',
    hargaPerKg: 14500,
    phAir: 7.0,
    get totalBiaya() {
        return (parseFloat(this.peletKg || 0) + parseFloat(this.daunKg || 0)) * this.hargaPerKg;
    },
    handleSave() {
        if (!this.kolam) {
            triggerToast('Mohon pilih kolam pembesaran!', 'error');
            return;
        }
        triggerToast('Log pakan harian ' + this.kolam + ' berhasil disimpan!', 'success');
        setTimeout(() => {
            window.location.href = '{{ route('petugas.pembesaran.dashboard') }}';
        }, 1200);
    }
}">

    <!-- Title Header Box Card -->
    <div class="bg-white rounded-3xl border border-slate-200/90 p-4 shadow-xs">
        <h1 class="text-base font-extrabold text-navy-900">Log Pakan Harian</h1>
        <p class="text-xs text-slate-500 font-medium mt-0.5">Catat pemberian pakan dan kondisi kolam hari ini.</p>
    </div>

    <!-- Form Card -->
    <div class="bg-white rounded-3xl border border-slate-200/90 p-5 shadow-xs">
        
        <form @submit.prevent="handleSave()" class="space-y-4">
            
            <!-- Field 1: Pilih Kolam -->
            <div class="space-y-1">
                <label class="text-[10px] font-extrabold uppercase text-slate-500 tracking-wider block">PILIH KOLAM</label>
                <select x-model="kolam" 
                        class="w-full px-3.5 py-2.5 rounded-2xl border border-slate-200 text-xs font-semibold text-slate-800 bg-slate-50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-navy-800">
                    <option value="">Pilih Kolam...</option>
                    <option value="Kolam A-01">Kolam A-01</option>
                    <option value="Kolam B-03">Kolam B-03</option>
                    <option value="Kolam C-02">Kolam C-02</option>
                    <option value="Kolam D-05">Kolam D-05</option>
                </select>
            </div>

            <!-- Field 2: Waktu & Tanggal -->
            <div class="space-y-1">
                <label class="text-[10px] font-extrabold uppercase text-slate-500 tracking-wider block">WAKTU & TANGGAL</label>
                <input type="datetime-local" x-model="waktuTanggal"
                       class="w-full px-3.5 py-2.5 rounded-2xl border border-slate-200 text-xs font-semibold text-slate-800 bg-slate-50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-navy-800">
            </div>

            <!-- Field 3: Detail Pakan Grid (Pelet & Daun) -->
            <div class="space-y-2 pt-1">
                <div class="flex items-center gap-2 text-xs font-bold text-slate-900">
                    <i class="fa-solid fa-wheat-awn text-emerald-600"></i>
                    <span>Detail Pakan</span>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="text-[10px] font-extrabold uppercase text-slate-400 block mb-1">PELET (KG)</label>
                        <input type="number" step="0.1" x-model="peletKg"
                               class="w-full px-3.5 py-2.5 rounded-2xl border border-slate-200 text-xs font-semibold text-slate-800 bg-slate-50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-navy-800">
                    </div>
                    <div>
                        <label class="text-[10px] font-extrabold uppercase text-slate-400 block mb-1">DAUN (KG)</label>
                        <input type="number" step="0.1" x-model="daunKg"
                               class="w-full px-3.5 py-2.5 rounded-2xl border border-slate-200 text-xs font-semibold text-slate-800 bg-slate-50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-navy-800">
                    </div>
                </div>
            </div>

            <!-- Field 4: Jenis Daun -->
            <div class="space-y-1">
                <label class="text-[10px] font-extrabold uppercase text-slate-500 tracking-wider block">JENIS DAUN</label>
                <input type="text" x-model="jenisDaun" placeholder="Contoh: Daun Talas, Kangkung"
                       class="w-full px-3.5 py-2.5 rounded-2xl border border-slate-200 text-xs font-semibold text-slate-800 bg-slate-50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-navy-800">
            </div>

            <!-- Field 5: Total Biaya Pakan (EST) -->
            <div class="space-y-1">
                <label class="text-[10px] font-extrabold uppercase text-slate-500 tracking-wider block">TOTAL BIAYA PAKAN (EST)</label>
                <div class="px-3.5 py-2.5 rounded-2xl border border-slate-200 bg-slate-50 flex items-center justify-between text-xs">
                    <span class="font-bold text-slate-400">Rp</span>
                    <span class="font-extrabold text-navy-900 text-sm" x-text="totalBiaya.toLocaleString('id-ID')">0</span>
                </div>
            </div>

            <!-- Field 6: pH Air Parameter Slider -->
            <div class="bg-emerald-50/70 p-4 rounded-2xl border border-emerald-200/80 space-y-2">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <i class="fa-solid fa-droplet text-emerald-600 text-sm"></i>
                        <span class="text-xs font-extrabold text-navy-900">pH Air</span>
                    </div>
                    <span class="text-lg font-black text-navy-900" x-text="parseFloat(phAir).toFixed(1)">7.0</span>
                </div>

                <input type="range" min="5.0" max="9.0" step="0.1" x-model="phAir"
                       class="w-full accent-navy-800 cursor-pointer">

                <div class="grid grid-cols-3 gap-1 text-[9px] font-extrabold text-center pt-1 text-slate-500">
                    <span :class="phAir < 6.5 ? 'text-amber-600 font-black' : ''">5.0 (Asam)</span>
                    <span :class="phAir >= 6.5 && phAir <= 7.5 ? 'text-emerald-600 font-black' : ''">7.0 (Optimal)</span>
                    <span :class="phAir > 7.5 ? 'text-rose-600 font-black' : ''">9.0 (Basa)</span>
                </div>
            </div>

            <!-- Primary Action Button -->
            <button type="submit" 
                    class="w-full py-3.5 rounded-2xl bg-navy-800 hover:bg-navy-900 active:scale-[0.99] text-white font-extrabold text-xs flex items-center justify-center gap-2 shadow-md transition-all pt-3">
                <i class="fa-solid fa-floppy-disk text-xs"></i>
                <span>Simpan Log Pakan</span>
            </button>

        </form>

    </div>

</div>
@endsection

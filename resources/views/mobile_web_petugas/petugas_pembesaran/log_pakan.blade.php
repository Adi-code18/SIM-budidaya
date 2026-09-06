@extends('mobile_web_petugas.petugas_pembesaran.layout')

@section('title', 'Log Pakan Pembesaran - SIM-BUDIDAYA Mobile')

@section('content')
<div class="p-4 space-y-4" x-data="petugasPembesaranLogComponent()">

    <!-- Header Card -->
    <div class="bg-[#051B44] rounded-3xl p-5 text-white shadow-lg shadow-sky-950/20">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-2xl bg-white/15 backdrop-blur-md flex items-center justify-center text-lg border border-white/20">
                <i class="fa-solid fa-fish-fins text-sky-200"></i>
            </div>
            <div>
                <h1 class="text-base font-extrabold text-white">Log Pakan Pembesaran</h1>
                <p class="text-[11px] text-sky-100 font-medium">Catat pakan harian kolam & potong stok otomatis.</p>
            </div>
        </div>
    </div>

    <!-- Main Form Card -->
    <div class="bg-white rounded-3xl border border-slate-200/90 p-5 shadow-xs">
        
        <template x-if="activeBatches.length === 0">
            <div class="p-4 bg-amber-50 rounded-2xl border border-amber-200 text-amber-900 text-xs font-medium space-y-3 mb-4">
                <div class="flex items-start gap-2.5">
                    <i class="fa-solid fa-triangle-exclamation text-amber-600 text-base mt-0.5"></i>
                    <div>
                        <strong class="font-extrabold block text-amber-950">Belum Ada Kolam Pembesaran yang Terisi Ikan</strong>
                        <span>Seluruh kolam pembesaran saat ini sedang kosong. Anda harus memulai siklus pembesaran (tebar benih) terlebih dahulu sebelum dapat mencatat pemberian pakan.</span>
                    </div>
                </div>
                <a href="{{ route('petugas.pembesaran.create-batch') }}" 
                   class="w-full py-2.5 px-4 rounded-xl bg-amber-600 hover:bg-amber-700 text-white font-extrabold text-xs flex items-center justify-center gap-2 shadow-xs transition-colors">
                    <i class="fa-solid fa-plus text-xs"></i>
                    <span>Mulai Siklus / Tebar Benih Baru</span>
                </a>
            </div>
        </template>

        <form @submit.prevent="handleSave()" class="space-y-4">
            
            <!-- Field 1: Pilih Kolam Pembesaran Aktif -->
            <div class="space-y-1">
                <label class="text-[10px] font-extrabold uppercase text-slate-500 tracking-wider block">
                    KOLAM PEMBESARAN AKTIF <span class="text-rose-500">*</span>
                </label>
                <select x-model="form.id_kolam" @change="onKolamChange()" :disabled="activeBatches.length === 0" required
                        class="w-full px-3.5 py-2.5 rounded-2xl border border-slate-200 text-xs font-semibold text-slate-800 bg-slate-50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#0284C7] transition-all cursor-pointer disabled:bg-slate-100 disabled:text-slate-400">
                    <option value="">Pilih Kolam Pembesaran...</option>
                    <template x-for="b in activeBatches" :key="b.id_pembesaran">
                        <option :value="b.id_kolam" 
                                x-text="(b.kolam ? b.kolam.nama_kolam : 'Kolam #' + b.id_kolam) + ' - #PB-' + String(b.id_pembesaran).padStart(5, '0') + ' (' + b.jenis_ikan + ' • ' + Number(b.biomassa_est).toFixed(1) + ' kg)'">
                        </option>
                    </template>
                </select>

                <template x-if="selectedBatch">
                    <div class="p-2.5 bg-sky-50/70 rounded-xl border border-sky-100 flex items-center justify-between text-[11px] text-sky-950 mt-1.5">
                        <span class="font-bold" x-text="'Ikan: ' + selectedBatch.jenis_ikan"></span>
                        <span class="font-extrabold" x-text="'Biomassa: ' + Number(selectedBatch.biomassa_est).toLocaleString('id-ID') + ' kg'"></span>
                    </div>
                </template>
            </div>

            <!-- Field 2: Tanggal Pemberian -->
            <div class="space-y-1">
                <label class="text-[10px] font-extrabold uppercase text-slate-500 tracking-wider block">
                    TANGGAL PEMBERIAN <span class="text-rose-500">*</span>
                </label>
                <input type="date" x-model="form.tgl_log" required
                       class="w-full px-3.5 py-2.5 rounded-2xl border border-slate-200 text-xs font-semibold text-slate-800 bg-slate-50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#0284C7] transition-all">
            </div>

            <!-- Field 3: Pilih Item Pakan Gudang -->
            <div class="space-y-1">
                <label class="text-[10px] font-extrabold uppercase text-slate-500 tracking-wider block">
                    ITEM PAKAN DARI GUDANG <span class="text-rose-500">*</span>
                </label>
                <select x-model="form.id_stok_pakan" @change="recalculateCost()" required
                        class="w-full px-3.5 py-2.5 rounded-2xl border border-slate-200 text-xs font-semibold text-slate-800 bg-slate-50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#0284C7] transition-all cursor-pointer">
                    <option value="">Pilih Pakan Gudang...</option>
                    <template x-for="pakan in stokPakanList" :key="pakan.id_stok_pakan">
                        <option :value="pakan.id_stok_pakan" 
                                x-text="pakan.nama_pakan + ' (Sisa: ' + pakan.stok_tersisa + ' ' + pakan.satuan + ')'">
                        </option>
                    </template>
                </select>

                <template x-if="selectedPakan">
                    <div class="flex items-center justify-between text-[11px] text-slate-500 px-1 pt-0.5">
                        <span>Stok Tersisa: <strong class="text-slate-800" x-text="selectedPakan.stok_tersisa + ' ' + selectedPakan.satuan"></strong></span>
                        <span>Harga: <strong class="text-[#0284C7]" x-text="'Rp ' + Number(selectedPakan.harga_per_satuan).toLocaleString('id-ID') + '/' + selectedPakan.satuan"></strong></span>
                    </div>
                </template>
            </div>

            <!-- Field 4: Detail Pakan Grid (Pelet & Daun) -->
            <div class="space-y-2 pt-1">
                <div class="flex items-center gap-2 text-xs font-bold text-slate-900">
                    <i class="fa-solid fa-wheat-awn text-[#0284C7]"></i>
                    <span>Takaran Pakan</span>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="text-[10px] font-extrabold uppercase text-slate-400 block mb-1">
                            PAKAN UTAMA (KG) <span class="text-rose-500">*</span> <span class="text-[9px] text-slate-400 font-normal lowercase">(maks. 100)</span>
                        </label>
                        <input type="number" step="0.1" min="0" max="100" x-model="form.kg_pelet" 
                               @keydown="if(['-', 'e', '+'].includes($event.key)) $event.preventDefault()"
                               @input="if(Number(form.kg_pelet) > 100) form.kg_pelet = 100; recalculateCost()"
                               class="w-full px-3.5 py-2.5 rounded-2xl border border-slate-200 text-xs font-extrabold text-slate-900 bg-slate-50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#0284C7]">
                    </div>
                    <div>
                        <label class="text-[10px] font-extrabold uppercase text-slate-400 block mb-1">
                            DAUN / ORGANIK (KG) <span class="text-[9px] text-slate-400 font-normal lowercase">(maks. 100)</span>
                        </label>
                        <input type="number" step="0.1" min="0" max="100" x-model="form.kg_daun"
                               @keydown="if(['-', 'e', '+'].includes($event.key)) $event.preventDefault()"
                               @input="if(Number(form.kg_daun) > 100) form.kg_daun = 100"
                               class="w-full px-3.5 py-2.5 rounded-2xl border border-slate-200 text-xs font-extrabold text-slate-900 bg-slate-50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#0284C7]">
                    </div>
                </div>
            </div>

            <!-- Field 5: Jenis Daun / Suplemen Tambahan -->
            <div class="space-y-1">
                <label class="text-[10px] font-extrabold uppercase text-slate-500 tracking-wider block">JENIS DAUN / SUPLEMEN</label>
                <select x-model="form.jenis_daun" 
                        class="w-full px-3.5 py-2.5 rounded-2xl border border-slate-200 text-xs font-semibold text-slate-800 bg-slate-50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#0284C7] transition-all cursor-pointer">
                    <option value="">Tidak ada tambahan daun</option>
                    <option value="Daun Talas">Daun Talas</option>
                    <option value="Daun Singkong">Daun Singkong</option>
                    <option value="Daun Pepaya">Daun Pepaya</option>
                    <option value="Azolla / Lemna">Azolla / Lemna</option>
                    <option value="Maggot BSF">Maggot BSF</option>
                </select>
            </div>

            <!-- Field 6: Total Biaya Pakan (EST) -->
            <div class="space-y-1">
                <label class="text-[10px] font-extrabold uppercase text-slate-500 tracking-wider block">TOTAL BIAYA PAKAN (EST)</label>
                <div class="px-3.5 py-2.5 rounded-2xl border border-slate-200 bg-slate-50 flex items-center justify-between text-xs">
                    <span class="font-bold text-slate-400">Rp</span>
                    <span class="font-black text-[#0B2570] text-sm" x-text="Number(form.total_biaya || 0).toLocaleString('id-ID')">0</span>
                </div>
            </div>

            <!-- Field 7: pH Air Parameter Slider -->
            <div class="bg-sky-50/70 p-4 rounded-2xl border border-sky-200/80 space-y-2">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <i class="fa-solid fa-droplet text-sky-600 text-sm"></i>
                        <span class="text-xs font-extrabold text-slate-800">pH Air Kolam</span>
                    </div>
                    <span class="text-lg font-black text-[#0B2570]" x-text="parseFloat(form.ph_air).toFixed(1)">7.2</span>
                </div>

                <input type="range" min="5.0" max="9.0" step="0.1" x-model="form.ph_air"
                       class="w-full accent-[#0284C7] cursor-pointer">

                <div class="grid grid-cols-3 gap-1 text-[9px] font-extrabold text-center pt-1 text-slate-500">
                    <span :class="form.ph_air < 6.5 ? 'text-amber-600 font-black' : ''">5.0 (Asam)</span>
                    <span :class="form.ph_air >= 6.5 && form.ph_air <= 7.5 ? 'text-emerald-600 font-black' : ''">7.0 - 7.5 (Optimal)</span>
                    <span :class="form.ph_air > 7.5 ? 'text-rose-600 font-black' : ''">9.0 (Basa)</span>
                </div>
            </div>

            <!-- Primary Action Button -->
            <button type="submit" :disabled="isSubmitting"
                    class="w-full py-3.5 rounded-2xl bg-[#0284C7] hover:bg-sky-700 active:scale-[0.99] text-white font-extrabold text-xs flex items-center justify-center gap-2 shadow-md shadow-sky-600/20 transition-all cursor-pointer">
                <i class="fa-solid fa-floppy-disk text-xs"></i>
                <span x-text="isSubmitting ? 'Menyimpan & Memotong Stok...' : 'Simpan Log Pakan & Potong Stok'"></span>
            </button>

        </form>

    </div>

    <!-- Riwayat Log Pakan Pembesaran Terkini -->
    <div class="bg-white rounded-3xl border border-slate-200/90 p-4 sm:p-5 shadow-xs space-y-3">
        <div class="flex items-center justify-between pb-2 border-b border-slate-100">
            <h3 class="text-xs font-extrabold text-slate-900 flex items-center gap-1.5">
                <i class="fa-solid fa-clock-rotate-left text-sky-600"></i>
                <span>Riwayat Log Pakan Terkini</span>
            </h3>
            <span class="text-[10px] font-bold text-slate-400" x-text="logs.length + ' Catatan'"></span>
        </div>

        <div class="space-y-2.5">
            <template x-for="log in logs" :key="log.id_pakan">
                <div class="p-3 rounded-2xl bg-slate-50 border border-slate-100 flex items-center justify-between text-xs">
                    <div class="space-y-0.5">
                        <div class="flex items-center gap-2">
                            <span class="font-extrabold text-slate-900" x-text="log.kolam ? log.kolam.nama_kolam : 'Kolam #' + log.id_kolam"></span>
                            <span class="text-[10px] font-bold text-sky-700 bg-sky-100/60 px-2 py-0.5 rounded-md" 
                                  x-text="log.stok_pakan ? log.stok_pakan.nama_pakan : 'Pelet'">
                            </span>
                        </div>
                        <p class="text-[11px] text-slate-400" x-text="log.tgl_log + ' • ' + (Number(log.kg_daun) > 0 ? ('+' + Number(log.kg_daun).toFixed(1) + ' kg daun • ') : '') + 'pH: ' + (log.ph_air || '7.2')"></p>
                    </div>

                    <div class="text-right">
                        <span class="font-black text-slate-900 block" x-text="Number(log.kg_pelet).toFixed(1) + ' kg'"></span>
                        <span class="text-[10px] font-bold text-emerald-700" x-text="'Rp ' + Number(log.total_biaya).toLocaleString('id-ID')"></span>
                    </div>
                </div>
            </template>

            <template x-if="logs.length === 0">
                <div class="py-6 text-center text-slate-400 text-xs">
                    <i class="fa-solid fa-clipboard-question text-xl text-slate-300 block mb-1"></i>
                    Belum ada riwayat pemberian pakan pembesaran.
                </div>
            </template>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
function petugasPembesaranLogComponent() {
    return {
        activeBatches: {!! json_encode($activeBatches ?? []) !!},
        stokPakanList: {!! json_encode($stokPakanList ?? []) !!},
        logs: {!! json_encode($logs ?? []) !!},
        isSubmitting: false,

        form: {
            id_kolam: '',
            id_stok_pakan: '',
            tgl_log: new Date().toISOString().split('T')[0],
            kg_pelet: 10,
            kg_daun: 0,
            jenis_daun: '',
            total_biaya: 125000,
            ph_air: 7.2
        },

        init() {
            if (this.activeBatches.length > 0) {
                this.form.id_kolam = this.activeBatches[0].id_kolam;
                this.onKolamChange();
            }
            if (this.stokPakanList.length > 0) {
                this.form.id_stok_pakan = this.stokPakanList[0].id_stok_pakan;
                this.recalculateCost();
            }
        },

        get selectedBatch() {
            if (!this.form.id_kolam) return null;
            return this.activeBatches.find(b => b.id_kolam == this.form.id_kolam) || null;
        },

        get selectedPakan() {
            if (!this.form.id_stok_pakan) return null;
            return this.stokPakanList.find(p => p.id_stok_pakan == this.form.id_stok_pakan) || null;
        },

        onKolamChange() {
            if (this.selectedBatch) {
                const estPelet = Math.max(1, Math.round(this.selectedBatch.biomassa_est * 0.025 * 10) / 10);
                this.form.kg_pelet = estPelet;
                this.recalculateCost();
            }
        },

        recalculateCost() {
            const kg = Number(this.form.kg_pelet) || 0;
            const item = this.selectedPakan;
            const price = item ? (Number(item.harga_per_satuan) || 12500) : 12500;
            this.form.total_biaya = Math.round(kg * price);
        },

        async handleSave() {
            if (!this.form.id_kolam) {
                if (typeof triggerToast === 'function') {
                    triggerToast('Pilih kolam pembesaran aktif terlebih dahulu!', 'error');
                } else {
                    alert('Pilih kolam pembesaran aktif terlebih dahulu!');
                }
                return;
            }
            const totalKg = (Number(this.form.kg_pelet) || 0) + (Number(this.form.kg_daun) || 0);
            if (totalKg <= 0) {
                if (typeof triggerToast === 'function') {
                    triggerToast('Jumlah pakan (pelet atau daun) harus lebih dari 0 kg!', 'error');
                } else {
                    alert('Jumlah pakan (pelet atau daun) harus lebih dari 0 kg!');
                }
                return;
            }

            this.isSubmitting = true;
            try {
                const res = await fetch('{{ route('petugas.pembesaran.log-pakan.store') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        id_kolam: this.form.id_kolam,
                        id_stok_pakan: this.form.id_stok_pakan || null,
                        tgl_log: this.form.tgl_log,
                        kg_pelet: Number(this.form.kg_pelet) || 0,
                        kg_daun: Number(this.form.kg_daun) || 0,
                        jenis_daun: this.form.jenis_daun || null,
                        total_biaya: Number(this.form.total_biaya) || 0,
                        ph_air: Number(this.form.ph_air) || 7.2
                    })
                });

                const data = await res.json();
                if (res.ok && data.success) {
                    if (data.log) {
                        this.logs.unshift(data.log);
                    }
                    // Kurangi stok lokal
                    if (this.selectedPakan && Number(this.form.kg_pelet) > 0) {
                        this.selectedPakan.stok_tersisa = Math.max(0, Number(this.selectedPakan.stok_tersisa) - Number(this.form.kg_pelet));
                    }

                    if (typeof triggerToast === 'function') {
                        triggerToast(data.message || 'Log pakan pembesaran berhasil disimpan!', 'success');
                    } else {
                        alert(data.message || 'Log pakan pembesaran berhasil disimpan!');
                    }

                    this.form.kg_pelet = 10;
                    this.form.kg_daun = 0;
                    this.recalculateCost();
                } else {
                    if (typeof triggerToast === 'function') {
                        triggerToast(data.message || 'Gagal menyimpan log pakan.', 'error');
                    } else {
                        alert(data.message || 'Gagal menyimpan log pakan.');
                    }
                }
            } catch (err) {
                if (typeof triggerToast === 'function') {
                    triggerToast('Terjadi kesalahan jaringan.', 'error');
                } else {
                    alert('Terjadi kesalahan jaringan.');
                }
            } finally {
                this.isSubmitting = false;
            }
        }
    };
}
</script>
@endpush

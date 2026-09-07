@extends('mobile_web_petugas.petugas_pembibitan.layout')

@section('title', 'Log Pakan Pembibitan - SIM-BUDIDAYA Mobile')

@section('content')
<div class="p-4 space-y-4" x-data="petugasPembibitanLogComponent()">

    <!-- Header Box -->
    <div class="bg-emerald-800 rounded-3xl p-5 text-white shadow-lg shadow-emerald-950/20">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-2xl bg-white/15 backdrop-blur-md flex items-center justify-center text-lg border border-white/20">
                <i class="fa-solid fa-seedling text-emerald-200"></i>
            </div>
            <div>
                <h1 class="text-base font-extrabold text-white">Log Pakan Pembibitan</h1>
                <p class="text-[11px] text-emerald-100 font-medium">Catat pakan larva/benih & potong stok otomatis.</p>
            </div>
        </div>
    </div>

    <!-- Main Form Card -->
    <div class="bg-white rounded-3xl border border-slate-200/90 p-5 shadow-xs">
        
        <template x-if="activeBatches.length === 0">
            <div class="p-4 bg-emerald-50 rounded-2xl border border-emerald-200 text-emerald-900 text-xs font-medium space-y-3 mb-4">
                <div class="flex items-start gap-2.5">
                    <i class="fa-solid fa-triangle-exclamation text-emerald-600 text-base mt-0.5"></i>
                    <div>
                        <strong class="font-extrabold block text-emerald-950">Belum Ada Kolam Hatchery yang Terisi Benih</strong>
                        <span>Seluruh kolam hatchery saat ini sedang kosong. Anda harus menginput data siklus pembibitan (pemijahan/tebar telur) terlebih dahulu sebelum dapat mencatat pemberian pakan.</span>
                    </div>
                </div>
                <a href="{{ route('petugas.pembibitan.form') }}" 
                   class="w-full py-2.5 px-4 rounded-xl bg-emerald-700 hover:bg-emerald-800 text-white font-extrabold text-xs flex items-center justify-center gap-2 shadow-xs transition-colors">
                    <i class="fa-solid fa-plus text-xs"></i>
                    <span>Catat Log Pembibitan Baru</span>
                </a>
            </div>
        </template>

        <form @submit.prevent="handleSave()" class="space-y-4">
            
            <!-- Field 1: Pilih Kolam Hatchery Aktif -->
            <div class="space-y-1">
                <label class="text-[10px] font-extrabold uppercase text-slate-500 tracking-wider block">
                    KOLAM HATCHERY AKTIF <span class="text-rose-500">*</span>
                </label>
                <select x-model="form.id_kolam" @change="onKolamChange()" :disabled="activeBatches.length === 0" required
                        class="w-full px-3.5 py-2.5 rounded-2xl border border-slate-200 text-xs font-semibold text-slate-800 bg-slate-50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-emerald-700 transition-all cursor-pointer disabled:bg-slate-100 disabled:text-slate-400">
                    <option value="">Pilih Kolam Hatchery...</option>
                    <template x-for="b in activeBatches" :key="b.id_batch">
                        <option :value="b.id_kolam" 
                                x-text="(b.kolam ? b.kolam.nama_kolam : 'Kolam #' + b.id_kolam) + ' - Hari ke-' + b.doc + ' (DOC ' + b.doc + ') • ' + b.fase + ' (' + (b.jenis_ikan || 'Benih') + ')'">
                        </option>
                    </template>
                </select>

                <template x-if="selectedBatch">
                    <div class="p-3 bg-emerald-50/80 rounded-2xl border border-emerald-200 space-y-2 text-xs text-emerald-950 mt-2">
                        <div class="flex items-center justify-between gap-2">
                            <span class="font-extrabold" x-text="'Benih: ' + (selectedBatch.jenis_ikan || 'Bibit Ikan')"></span>
                            <div class="flex items-center gap-1.5 shrink-0">
                                <span class="text-[10px] font-black px-2 py-0.5 rounded-md bg-white border border-emerald-200 text-emerald-800"
                                      x-text="'Hari ke-' + selectedBatch.doc + ' (DOC ' + selectedBatch.doc + ')'">
                                </span>
                                <span class="text-[10px] font-black px-2 py-0.5 rounded-md border"
                                      :class="selectedBatch.fase_badge || 'bg-emerald-100 text-emerald-800 border-emerald-200'"
                                      x-text="selectedBatch.fase">
                                </span>
                            </div>
                        </div>

                        <div class="pt-2 border-t border-emerald-200/70 flex flex-col gap-1.5 text-[11px]">
                            <div class="flex items-center justify-between text-slate-600">
                                <span>Populasi Benih:</span>
                                <strong class="text-slate-900 font-extrabold" x-text="Number(selectedBatch.jumlah_bibitAwal - selectedBatch.jumlah_kematian).toLocaleString('id-ID') + ' ekor'"></strong>
                            </div>
                            <div class="flex items-center gap-1.5 text-emerald-950 font-bold bg-white/80 p-2.5 rounded-xl border border-emerald-100 text-[11px]">
                                <i class="fa-solid fa-lightbulb text-amber-500 shrink-0"></i>
                                <span>Rekomendasi Fase: <strong class="text-emerald-900" x-text="selectedBatch.rekomendasi_pakan"></strong></span>
                            </div>
                        </div>
                    </div>
                </template>
            </div>

            <!-- Field 2: Tanggal & Waktu -->
            <div class="space-y-1">
                <label class="text-[10px] font-extrabold uppercase text-slate-500 tracking-wider block">
                    TANGGAL PEMBERIAN <span class="text-rose-500">*</span>
                </label>
                <input type="date" x-model="form.tgl_log" required
                       class="w-full px-3.5 py-2.5 rounded-2xl border border-slate-200 text-xs font-semibold text-slate-800 bg-slate-50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-emerald-700 transition-all">
            </div>

            <!-- Field 3: Pilih Jenis Pakan dari Gudang (Khusus Pembibitan) -->
            <div class="space-y-1">
                <label class="text-[10px] font-extrabold uppercase text-slate-500 tracking-wider block">
                    JENIS PAKAN BENIH (GUDANG) <span class="text-rose-500">*</span>
                </label>
                <select x-model="form.id_stok_pakan" @change="recalculateCost()" required
                        class="w-full px-3.5 py-2.5 rounded-2xl border border-slate-200 text-xs font-semibold text-slate-800 bg-slate-50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-emerald-700 transition-all cursor-pointer">
                    <option value="">Pilih Pakan Benih...</option>
                    <template x-for="pakan in stokPakanList" :key="pakan.id_stok_pakan">
                        <option :value="pakan.id_stok_pakan" 
                                x-text="pakan.nama_pakan + ' (Sisa: ' + pakan.stok_tersisa + ' ' + pakan.satuan + ')'">
                        </option>
                    </template>
                </select>

                <template x-if="selectedPakan">
                    <div class="flex items-center justify-between text-[11px] text-slate-500 px-1 pt-0.5">
                        <span>Stok Tersisa: <strong class="text-slate-800" x-text="selectedPakan.stok_tersisa + ' ' + selectedPakan.satuan"></strong></span>
                        <span>Harga: <strong class="text-emerald-700" x-text="'Rp ' + Number(selectedPakan.harga_per_satuan).toLocaleString('id-ID') + '/' + selectedPakan.satuan"></strong></span>
                    </div>
                </template>
            </div>

            <!-- Field 4: Jumlah Pakan Diberikan (KG) -->
            <div class="space-y-1">
                <label class="text-[10px] font-extrabold uppercase text-slate-500 tracking-wider block">
                    JUMLAH PAKAN DIBERIKAN <span class="text-rose-500">*</span> <span class="text-[9px] text-slate-400 font-normal lowercase">(maks. 100 kg)</span>
                </label>
                <div class="flex items-center rounded-2xl border border-slate-200 overflow-hidden bg-slate-50 focus-within:bg-white focus-within:ring-2 focus-within:ring-emerald-700 transition-all">
                    <input type="number" step="0.01" min="0.01" max="100" x-model="form.kg_pelet" 
                           @keydown="if(['-', 'e', '+'].includes($event.key)) $event.preventDefault()"
                           @input="if(Number(form.kg_pelet) > 100) form.kg_pelet = 100; recalculateCost()" required
                           class="w-full px-3.5 py-2.5 text-xs font-extrabold text-slate-900 border-0 bg-transparent focus:outline-none"
                           placeholder="Contoh: 1.5">
                    <span class="px-3 py-2.5 bg-slate-100 text-slate-500 text-xs font-black uppercase" x-text="selectedPakan ? selectedPakan.satuan : 'KG'">KG</span>
                </div>
            </div>

            <!-- Field 5: Estimasi Biaya Pakan (RP) -->
            <div class="space-y-1">
                <label class="text-[10px] font-extrabold uppercase text-slate-500 tracking-wider block">ESTIMASI BIAYA (RP)</label>
                <div class="px-3.5 py-2.5 rounded-2xl border border-slate-200 bg-slate-50 flex items-center justify-between text-xs">
                    <span class="font-bold text-slate-400">Rp</span>
                    <span class="font-black text-emerald-800 text-sm" x-text="Number(form.total_biaya || 0).toLocaleString('id-ID')">0</span>
                </div>
            </div>

            <!-- Field 6: Parameter pH Air Slider -->
            <div class="bg-emerald-50/60 p-4 rounded-2xl border border-emerald-200/80 space-y-2">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <i class="fa-solid fa-droplet text-emerald-600 text-sm"></i>
                        <span class="text-xs font-extrabold text-slate-800">pH Air Kolam Hatchery</span>
                    </div>
                    <span class="text-lg font-black text-emerald-800" x-text="parseFloat(form.ph_air).toFixed(1)">7.2</span>
                </div>

                <input type="range" min="5.0" max="9.0" step="0.1" x-model="form.ph_air"
                       class="w-full accent-emerald-700 cursor-pointer">

                <div class="grid grid-cols-3 gap-1 text-[9px] font-extrabold text-center pt-1 text-slate-500">
                    <span :class="form.ph_air < 6.5 ? 'text-amber-600 font-black' : ''">5.0 (Asam)</span>
                    <span :class="form.ph_air >= 6.5 && form.ph_air <= 7.5 ? 'text-emerald-700 font-black' : ''">7.0 - 7.5 (Optimal)</span>
                    <span :class="form.ph_air > 7.5 ? 'text-rose-600 font-black' : ''">9.0 (Basa)</span>
                </div>
            </div>

            <!-- Submit Button -->
            <button type="submit" :disabled="isSubmitting"
                    class="w-full py-3.5 rounded-2xl bg-emerald-700 hover:bg-emerald-800 active:scale-[0.99] text-white font-extrabold text-xs flex items-center justify-center gap-2 shadow-md shadow-emerald-700/20 transition-all cursor-pointer">
                <i class="fa-solid fa-floppy-disk text-xs"></i>
                <span x-text="isSubmitting ? 'Menyimpan & Memotong Stok...' : 'Simpan Log Pakan & Potong Stok'"></span>
            </button>

        </form>

    </div>

    <!-- Riwayat Log Pakan Pembibitan Terkini -->
    <div class="bg-white rounded-3xl border border-slate-200/90 p-4 sm:p-5 shadow-xs space-y-3">
        <div class="flex items-center justify-between pb-2 border-b border-slate-100">
            <h3 class="text-xs font-extrabold text-slate-900 flex items-center gap-1.5">
                <i class="fa-solid fa-clock-rotate-left text-emerald-600"></i>
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
                            <span class="text-[10px] font-bold text-emerald-700 bg-emerald-100/60 px-2 py-0.5 rounded-md" 
                                  x-text="log.stok_pakan ? log.stok_pakan.nama_pakan : 'Pakan Benih'">
                            </span>
                        </div>
                        <p class="text-[11px] text-slate-400" x-text="log.tgl_log + ' • pH: ' + (log.ph_air || '7.0')"></p>
                    </div>

                    <div class="text-right">
                        <span class="font-black text-slate-900 block" x-text="Number(log.kg_pelet).toFixed(2) + ' kg'"></span>
                        <span class="text-[10px] font-bold text-emerald-700" x-text="'Rp ' + Number(log.total_biaya).toLocaleString('id-ID')"></span>
                    </div>
                </div>
            </template>

            <template x-if="logs.length === 0">
                <div class="py-6 text-center text-slate-400 text-xs">
                    <i class="fa-solid fa-clipboard-question text-xl text-slate-300 block mb-1"></i>
                    Belum ada riwayat pemberian pakan pembibitan.
                </div>
            </template>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
function petugasPembibitanLogComponent() {
    return {
        activeBatches: {!! json_encode($activeBatches ?? []) !!},
        stokPakanList: {!! json_encode($stokPakanList ?? []) !!},
        logs: {!! json_encode($logs ?? []) !!},
        isSubmitting: false,

        form: {
            id_kolam: '',
            id_stok_pakan: '',
            tgl_log: new Date().toISOString().split('T')[0],
            kg_pelet: 1.0,
            total_biaya: 15000,
            ph_air: 7.2
        },

        init() {
            if (this.activeBatches.length > 0) {
                this.form.id_kolam = this.activeBatches[0].id_kolam;
                this.onKolamChange();
            } else if (this.stokPakanList.length > 0) {
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
                // Auto porsi pakan benih sesuai kalkulasi fase DOC
                this.form.kg_pelet = this.selectedBatch.est_pakan_kg || 1.0;

                // Auto sarankan pakan di stok gudang yang paling pas
                if (this.stokPakanList.length > 0) {
                    const fKey = (this.selectedBatch.fase_key || '').toLowerCase();
                    let matchedPakan = null;
                    if (fKey === 'telur') {
                        matchedPakan = this.stokPakanList.find(p => /kuning|telur|artemia|larva/i.test(p.nama_pakan));
                    } else if (fKey === 'larva') {
                        matchedPakan = this.stokPakanList.find(p => /cacing|sutra|artemia|nauplii/i.test(p.nama_pakan));
                    } else if (fKey === 'fingerling') {
                        matchedPakan = this.stokPakanList.find(p => /pf-500|pf-800|starter|benih/i.test(p.nama_pakan));
                    }
                    if (matchedPakan) {
                        this.form.id_stok_pakan = matchedPakan.id_stok_pakan;
                    } else if (!this.form.id_stok_pakan && this.stokPakanList.length > 0) {
                        this.form.id_stok_pakan = this.stokPakanList[0].id_stok_pakan;
                    }
                }

                this.recalculateCost();
            }
        },

        recalculateCost() {
            const kg = Number(this.form.kg_pelet) || 0;
            const item = this.selectedPakan;
            const price = item ? (Number(item.harga_per_satuan) || 15000) : 15000;
            this.form.total_biaya = Math.round(kg * price);
        },

        async handleSave() {
            if (!this.form.id_kolam) {
                if (typeof triggerToast === 'function') {
                    triggerToast('Pilih kolam hatchery aktif terlebih dahulu!', 'error');
                } else {
                    alert('Pilih kolam hatchery aktif terlebih dahulu!');
                }
                return;
            }
            if (Number(this.form.kg_pelet || 0) <= 0) {
                if (typeof triggerToast === 'function') {
                    triggerToast('Jumlah pakan harus lebih dari 0 kg!', 'error');
                } else {
                    alert('Jumlah pakan harus lebih dari 0 kg!');
                }
                return;
            }

            this.isSubmitting = true;
            try {
                const res = await fetch('{{ route('petugas.pembibitan.log-pakan.store') }}', {
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
                        kg_pelet: Number(this.form.kg_pelet),
                        total_biaya: Number(this.form.total_biaya),
                        ph_air: Number(this.form.ph_air)
                    })
                });

                const data = await res.json();
                if (res.ok && data.success) {
                    if (data.log) {
                        this.logs.unshift(data.log);
                    }
                    // Kurangi stok lokal
                    if (this.selectedPakan) {
                        this.selectedPakan.stok_tersisa = Math.max(0, Number(this.selectedPakan.stok_tersisa) - Number(this.form.kg_pelet));
                    }

                    if (typeof triggerToast === 'function') {
                        triggerToast(data.message || 'Log pakan berhasil disimpan!', 'success');
                    } else {
                        alert(data.message || 'Log pakan berhasil disimpan!');
                    }

                    this.form.kg_pelet = 1.0;
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

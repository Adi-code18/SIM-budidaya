@extends('layouts.app')

@section('title', 'Log Pakan Harian - SIM-BUDIDAYA')

@section('content')
<div class="space-y-6 max-w-5xl mx-auto" x-data="logPakanComponent()">

    <!-- Subtitle & Page Title Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <span class="text-xs font-semibold text-slate-400 block uppercase tracking-wider">Pemberian Pakan &amp; Nutrisi</span>
            <h1 class="text-2xl font-extrabold text-[#0B2570] tracking-tight mt-0.5">Log Pakan Harian</h1>
            <p class="text-xs text-slate-500 font-medium mt-0.5">Catat pakan pelet, dedaunan organik, dan kualitas air kolam pembesaran aktif.</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('pembesaran') }}" class="px-4 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs flex items-center gap-2 transition-colors">
                <i class="fa-solid fa-fish text-xs text-sky-600"></i>
                <span>Lihat Batch Pembesaran</span>
            </a>
        </div>
    </div>

    <!-- Alert Jika Tidak Ada Batch Pembesaran Aktif -->
    <template x-if="activeKolams.length === 0">
        <div class="p-5 bg-amber-50 rounded-2xl border border-amber-200/80 flex items-start gap-3.5 text-amber-900">
            <div class="w-10 h-10 rounded-xl bg-amber-100 text-amber-700 flex items-center justify-center shrink-0 text-lg">
                <i class="fa-solid fa-triangle-exclamation"></i>
            </div>
            <div>
                <h4 class="font-extrabold text-sm text-amber-900">Tidak Ada Batch Pembesaran Aktif</h4>
                <p class="text-xs text-amber-800/90 mt-0.5 leading-relaxed">
                    Formulir log pakan hanya berlaku untuk kolam yang memiliki siklus pembesaran yang sedang berjalan (aktif). Silakan tebar benih baru atau pindahkan bibit dari hatchery terlebih dahulu.
                </p>
                <a href="{{ route('pembesaran') }}" class="inline-flex items-center gap-1.5 mt-2.5 px-3.5 py-1.5 rounded-lg bg-amber-700 text-white text-xs font-bold hover:bg-amber-800 transition-colors">
                    <i class="fa-solid fa-plus text-[10px]"></i>
                    <span>Buka Menu Pembesaran</span>
                </a>
            </div>
        </div>
    </template>

    <!-- Main Form Card Container -->
    <div class="bg-white p-6 sm:p-8 rounded-2xl border border-slate-200/80 shadow-xs space-y-6">
        
        <!-- Header inside Form -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-4 border-b border-slate-100">
            <div class="flex items-center gap-3.5">
                <div class="w-11 h-11 rounded-2xl bg-sky-50 text-sky-600 flex items-center justify-center shrink-0 text-lg border border-sky-100">
                    <i class="fa-regular fa-clipboard"></i>
                </div>
                <div>
                    <h3 class="text-base font-bold text-slate-900">Formulir Log Pakan Harian</h3>
                    <p class="text-xs text-slate-500 mt-0.5">Khusus batch pembesaran yang sedang berjalan / ada penghuninya.</p>
                </div>
            </div>
            <span class="text-[11px] font-extrabold px-3 py-1 rounded-full bg-emerald-50 text-emerald-800 border border-emerald-200 flex items-center gap-1.5">
                <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                <span>Reset Otomatis Setiap Hari</span>
            </span>
        </div>

        <!-- Form Elements -->
        <form @submit.prevent="handleSave()" class="space-y-6">
            
            <!-- Row 1: Kolam & Tanggal -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label class="text-[10px] font-extrabold uppercase tracking-wider text-slate-500 block mb-1.5">
                        PILIH KOLAM PEMBESARAN (HANYA BATCH AKTIF) *
                    </label>
                    <div class="relative">
                        <i class="fa-solid fa-water absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                        <select x-model="form.id_kolam" @change="onKolamChange()" 
                                class="w-full pl-9 pr-8 py-2.5 rounded-xl border border-slate-200 text-xs font-semibold text-slate-700 bg-slate-50/70 focus:bg-white focus:outline-none focus:ring-2 focus:ring-sky-500 transition-all appearance-none cursor-pointer">
                            <option value="">Pilih Kolam Pembesaran Aktif...</option>
                            <template x-for="k in activeKolams" :key="k.id_kolam">
                                <option :value="k.id_kolam" x-text="k.label"></option>
                            </template>
                        </select>
                        <i class="fa-solid fa-chevron-down absolute right-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-[10px]"></i>
                    </div>
                </div>

                <div>
                    <label class="text-[10px] font-extrabold uppercase tracking-wider text-slate-500 block mb-1.5">
                        TANGGAL PEMBERIAN PAKAN *
                    </label>
                    <div class="relative">
                        <i class="fa-regular fa-calendar absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                        <input type="date" x-model="form.tgl_log"
                               class="w-full pl-9 pr-4 py-2.5 rounded-xl border border-slate-200 text-xs font-semibold text-slate-700 bg-slate-50/70 focus:bg-white focus:outline-none focus:ring-2 focus:ring-sky-500 transition-all">
                    </div>
                </div>
            </div>

            <!-- Detail Batch Terpilih Info Card -->
            <template x-if="selectedKolamInfo">
                <div class="p-4 bg-sky-50/70 rounded-2xl border border-sky-100 flex flex-col sm:flex-row sm:items-center justify-between gap-3 text-xs">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-white text-sky-600 border border-sky-200 flex items-center justify-center font-bold">
                            <i class="fa-solid fa-fish"></i>
                        </div>
                        <div>
                            <div class="flex items-center gap-2">
                                <span class="font-extrabold text-slate-900" x-text="selectedKolamInfo.nama_kolam"></span>
                                <span class="text-[10px] font-bold text-sky-700 bg-white px-2 py-0.5 rounded-md border border-sky-200" x-text="selectedKolamInfo.batch_id"></span>
                            </div>
                            <span class="text-[11px] text-slate-500" x-text="selectedKolamInfo.jenis_ikan + ' • Estimasi Biomassa: ' + selectedKolamInfo.biomassa_format + ' kg'"></span>
                        </div>
                    </div>

                    <div>
                        <template x-if="selectedKolamInfo.is_fed_today">
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[10px] font-extrabold bg-emerald-100 text-emerald-800 border border-emerald-300">
                                <i class="fa-solid fa-circle-check"></i>
                                <span>Sudah Diberi Pakan Hari Ini</span>
                            </span>
                        </template>
                        <template x-if="!selectedKolamInfo.is_fed_today">
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[10px] font-extrabold bg-amber-100 text-amber-800 border border-amber-300">
                                <i class="fa-solid fa-clock"></i>
                                <span>Belum Diberi Pakan Hari Ini</span>
                            </span>
                        </template>
                    </div>
                </div>
            </template>

            <!-- Section 1: Data Pemberian Pakan -->
            <div>
                <div class="flex items-center gap-2 text-xs font-bold text-slate-800 pb-3 border-b border-slate-100">
                    <i class="fa-solid fa-bowl-food text-sky-600"></i>
                    <span>Data Pemberian Pakan</span>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5 mt-4">
                    
                    <!-- Pakan Pelet Box -->
                    <div class="bg-slate-50/80 p-4 rounded-xl border border-slate-100 space-y-2">
                        <label class="text-[10px] font-extrabold uppercase tracking-wider text-slate-500 block">
                            PAKAN PELET KOMERSIAL (KG) *
                        </label>
                        <div class="flex items-center gap-2">
                            <input type="number" x-model="form.kg_pelet" @input="recalculateCost()"
                                step="0.1" min="0" placeholder="0.0"
                                class="w-full px-3.5 py-2 rounded-lg border border-slate-200 text-sm font-extrabold text-slate-900 bg-white focus:outline-none focus:ring-2 focus:ring-sky-500">
                            <span class="text-xs font-extrabold text-slate-400">KG</span>
                        </div>
                        <span class="text-[10px] text-slate-400 font-medium block">Standar pakan protein tinggi / apung.</span>
                    </div>

                    <!-- Pakan Dedaunan Box -->
                    <div class="bg-slate-50/80 p-4 rounded-xl border border-slate-100 space-y-2">
                        <label class="text-[10px] font-extrabold uppercase tracking-wider text-slate-500 block">
                            PAKAN DEDAUNAN ORGANIK (KG)
                        </label>
                        <div class="grid grid-cols-5 gap-2">
                            <input type="number" x-model="form.kg_daun"
                                   step="0.1" min="0" placeholder="0.0"
                                   class="col-span-2 px-3.5 py-2 rounded-lg border border-slate-200 text-sm font-extrabold text-slate-900 bg-white focus:outline-none focus:ring-2 focus:ring-sky-500">
                            <select x-model="form.jenis_daun" class="col-span-3 px-3 py-2 rounded-lg border border-slate-200 text-xs font-semibold text-slate-600 bg-white focus:outline-none focus:ring-2 focus:ring-sky-500">
                                <option value="">Pilih Daun...</option>
                                <option value="Daun Talas">Daun Talas</option>
                                <option value="Daun Singkong">Daun Singkong</option>
                                <option value="Daun Pepaya">Daun Pepaya</option>
                                <option value="Azolla">Azolla / Lemna</option>
                            </select>
                        </div>
                        <span class="text-[10px] text-slate-400 font-medium block">Pakan suplemen alami daya tahan ikan.</span>
                    </div>

                </div>
            </div>

            <!-- Section 2: Parameter Kualitas Air & Biaya -->
            <div>
                <div class="flex items-center gap-2 text-xs font-bold text-slate-800 pb-3 border-b border-slate-100">
                    <i class="fa-solid fa-droplet text-sky-600"></i>
                    <span>Parameter Kualitas Air &amp; Estimasi Biaya Pakan</span>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5 mt-4">
                    <div>
                        <label class="text-[10px] font-extrabold uppercase tracking-wider text-slate-500 block mb-1.5">
                            ESTIMASI BIAYA PAKAN (RP)
                        </label>
                        <div class="relative">
                            <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-xs font-bold text-slate-400">Rp</span>
                            <input type="number" x-model="form.total_biaya"
                                   class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-slate-200 text-xs font-extrabold text-slate-900 bg-slate-50/70 focus:bg-white focus:outline-none focus:ring-2 focus:ring-sky-500 transition-all">
                        </div>
                    </div>

                    <div>
                        <label class="text-[10px] font-extrabold uppercase tracking-wider text-slate-500 block mb-1.5">
                            PH AIR KOLAM
                        </label>
                        <div class="relative">
                            <i class="fa-solid fa-vial absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                            <input type="number" step="0.1" x-model="form.ph_air" placeholder="7.2"
                                class="w-full pl-9 pr-10 py-2.5 rounded-xl border border-slate-200 text-xs font-semibold text-slate-700 bg-slate-50/70 focus:bg-white focus:outline-none focus:ring-2 focus:ring-sky-500 transition-all">
                            <span class="absolute right-3.5 top-1/2 -translate-y-1/2 text-xs font-bold text-slate-400">pH</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
                <button type="button" @click="resetForm()" class="px-5 py-2.5 rounded-xl border border-slate-200 text-xs font-bold text-slate-600 hover:bg-slate-50 transition-colors">
                    Reset
                </button>
                <button type="submit" :disabled="isSubmitting"
                        class="px-6 py-2.5 rounded-xl bg-[#051B44] hover:bg-navy-900 text-white font-bold text-xs shadow-md shadow-sky-950/20 transition-all flex items-center gap-2 disabled:opacity-60">
                    <i class="fa-solid" :class="isSubmitting ? 'fa-spinner fa-spin' : 'fa-floppy-disk'"></i>
                    <span x-text="isSubmitting ? 'Menyimpan...' : 'Simpan Log Pakan'"></span>
                </button>
            </div>

        </form>

    </div>

    <!-- Tabel Riwayat Log Pakan Terbaru -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
        <div class="p-5 border-b border-slate-100 flex items-center justify-between">
            <div>
                <h3 class="text-base font-extrabold text-slate-900">Riwayat Pemberian Pakan Terbaru</h3>
                <p class="text-xs text-slate-500 mt-0.5">Catatan log pakan pada siklus pembesaran terkini.</p>
            </div>
            <span class="text-xs font-bold text-slate-400" x-text="logs.length + ' Catatan'"></span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50/80 text-[10px] uppercase font-extrabold text-slate-400 border-b border-slate-100">
                    <tr>
                        <th class="py-3 px-5">Tanggal</th>
                        <th class="py-3 px-5">Kolam Pembesaran</th>
                        <th class="py-3 px-5">Pakan Pelet</th>
                        <th class="py-3 px-5">Pakan Daun</th>
                        <th class="py-3 px-5">Total Biaya</th>
                        <th class="py-3 px-5">pH Air</th>
                        <th class="py-3 px-5 text-right">Petugas</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 font-medium text-slate-700">
                    <template x-for="log in logs" :key="log.id_pakan">
                        <tr class="hover:bg-slate-50/60 transition-colors">
                            <td class="py-3 px-5 font-bold text-slate-900" x-text="log.tgl_log"></td>
                            <td class="py-3 px-5">
                                <span class="font-extrabold text-[#0B2570]" x-text="log.kolam ? log.kolam.nama_kolam : 'Kolam #' + log.id_kolam"></span>
                            </td>
                            <td class="py-3 px-5 font-bold text-slate-800" x-text="Number(log.kg_pelet).toFixed(1) + ' kg'"></td>
                            <td class="py-3 px-5">
                                <span x-text="Number(log.kg_daun) > 0 ? (Number(log.kg_daun).toFixed(1) + ' kg ' + (log.jenis_daun ? '(' + log.jenis_daun + ')' : '')) : '-'"></span>
                            </td>
                            <td class="py-3 px-5 font-bold text-emerald-700" x-text="'Rp ' + Number(log.total_biaya).toLocaleString('id-ID')"></td>
                            <td class="py-3 px-5">
                                <span class="px-2 py-0.5 rounded-md text-[10px] font-extrabold bg-sky-50 text-sky-800 border border-sky-100" x-text="'pH ' + (log.ph_air || '7.2')"></span>
                            </td>
                            <td class="py-3 px-5 text-right text-slate-500" x-text="log.user ? log.user.name : 'Petugas'"></td>
                        </tr>
                    </template>

                    <template x-if="logs.length === 0">
                        <tr>
                            <td colspan="7" class="py-8 text-center text-slate-400">
                                Belum ada data log pakan yang tercatat.
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Notification Toast -->
    <div x-show="showToast"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 -translate-y-4 scale-95"
         x-transition:enter-end="opacity-100 translate-y-0 scale-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 translate-y-0 scale-100"
         x-transition:leave-end="opacity-0 -translate-y-4 scale-95"
         class="fixed top-6 right-6 z-50 max-w-sm rounded-2xl shadow-xl border p-4 flex items-center gap-3 backdrop-blur-md bg-[#051B44] text-white border-sky-500/50 shadow-sky-950/20"
         style="display: none;">
        <div class="w-8 h-8 rounded-xl bg-emerald-500 text-white flex items-center justify-center shrink-0">
            <i class="fa-solid fa-check text-sm"></i>
        </div>
        <div class="flex-1 text-xs font-bold leading-snug" x-text="toastMessage"></div>
        <button @click="showToast = false" class="text-white/70 hover:text-white transition-colors">
            <i class="fa-solid fa-xmark text-xs"></i>
        </button>
    </div>

</div>
@endsection

@push('scripts')
<script>
function logPakanComponent() {
    return {
        activeKolams: {!! json_encode($activeKolams ?? []) !!},
        logs: {!! json_encode($logs ?? []) !!},
        isSubmitting: false,
        showToast: false,
        toastMessage: '',

        form: {
            id_kolam: '',
            tgl_log: new Date().toISOString().split('T')[0],
            kg_pelet: 10,
            kg_daun: 0,
            jenis_daun: '',
            total_biaya: 125000,
            ph_air: 7.2
        },

        get selectedKolamInfo() {
            if (!this.form.id_kolam) return null;
            return this.activeKolams.find(k => k.id_kolam == this.form.id_kolam) || null;
        },

        onKolamChange() {
            if (this.selectedKolamInfo) {
                // Estimasi pakan 2% - 3% dari biomassa
                const estPelet = Math.max(1, Math.round(this.selectedKolamInfo.biomassa_est * 0.025 * 10) / 10);
                this.form.kg_pelet = estPelet;
                this.recalculateCost();
            }
        },

        recalculateCost() {
            const pelet = Number(this.form.kg_pelet) || 0;
            this.form.total_biaya = Math.round(pelet * 12500); // 12.500 per kg pelet
        },

        resetForm() {
            this.form = {
                id_kolam: '',
                tgl_log: new Date().toISOString().split('T')[0],
                kg_pelet: 10,
                kg_daun: 0,
                jenis_daun: '',
                total_biaya: 125000,
                ph_air: 7.2
            };
        },

        async handleSave() {
            if (!this.form.id_kolam) {
                alert('Silakan pilih Kolam Pembesaran yang aktif terlebih dahulu!');
                return;
            }
            if (Number(this.form.kg_pelet) <= 0 && Number(this.form.kg_daun) <= 0) {
                alert('Silakan masukkan jumlah pakan (pelet atau daun)!');
                return;
            }

            this.isSubmitting = true;
            try {
                const res = await fetch('{{ route('log-pakan.store') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        id_kolam: this.form.id_kolam,
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
                    // Update state locally
                    if (data.log) {
                        this.logs.unshift(data.log);
                    }

                    // Tandai kolam sudah diberi pakan hari ini
                    const kolamIdx = this.activeKolams.findIndex(k => k.id_kolam == this.form.id_kolam);
                    if (kolamIdx !== -1) {
                        this.activeKolams[kolamIdx].is_fed_today = true;
                        this.activeKolams[kolamIdx].label = this.activeKolams[kolamIdx].label.replace('[Belum Diberi Pakan]', '[Sudah Diberi Pakan Hari Ini]');
                    }

                    this.toastMessage = data.message || 'Log pakan berhasil dicatat!';
                    this.showToast = true;
                    setTimeout(() => { this.showToast = false; }, 4000);
                    this.resetForm();
                } else {
                    alert(data.message || 'Gagal menyimpan log pakan.');
                }
            } catch (err) {
                alert('Terjadi kesalahan saat menyimpan log pakan.');
            } finally {
                this.isSubmitting = false;
            }
        }
    };
}
</script>
@endpush

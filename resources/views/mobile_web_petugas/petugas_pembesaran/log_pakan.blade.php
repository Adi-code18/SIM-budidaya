@extends('mobile_web_petugas.petugas_pembesaran.layout')

@section('title', 'Log Pakan & Sampling Pembesaran - AMS BUDIDAYA Mobile')

@section('content')
<div class="p-4 space-y-4" x-data="petugasPembesaranLogComponent()">

    <!-- Header Card -->
    <div class="bg-[#051B44] rounded-3xl p-5 text-white shadow-lg shadow-sky-950/20">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-2xl bg-white/15 backdrop-blur-md flex items-center justify-center text-lg border border-white/20">
                <i class="fa-solid fa-fish-fins text-sky-200"></i>
            </div>
            <div>
                <h1 class="text-base font-extrabold text-white">Operasional Pembesaran</h1>
                <p class="text-[11px] text-sky-100 font-medium">Log pakan harian, kontrol kualitas air, dan sampling bobot.</p>
            </div>
        </div>
    </div>

    <!-- Tab Switcher Navigation -->
    <div class="grid grid-cols-2 gap-1.5 p-1 bg-slate-200/80 rounded-2xl">
        <button type="button" @click="activeTab = 'pakan'"
                :class="activeTab === 'pakan' ? 'bg-[#0284C7] text-white shadow-xs font-black' : 'text-slate-600 hover:text-slate-900 font-bold'"
                class="py-2.5 px-3 rounded-xl text-xs flex items-center justify-center gap-2 transition-all cursor-pointer">
            <i class="fa-solid fa-wheat-awn"></i>
            <span>Log Pakan & Air</span>
        </button>
        <button type="button" @click="activeTab = 'sampling'"
                :class="activeTab === 'sampling' ? 'bg-[#0284C7] text-white shadow-xs font-black' : 'text-slate-600 hover:text-slate-900 font-bold'"
                class="py-2.5 px-3 rounded-xl text-xs flex items-center justify-center gap-2 transition-all cursor-pointer">
            <i class="fa-solid fa-weight-scale"></i>
            <span>Sampling Bobot</span>
        </button>
    </div>

    <!-- ========================================== -->
    <!-- TAB 1: LOG PAKAN & KUALITAS AIR            -->
    <!-- ========================================== -->
    <div x-show="activeTab === 'pakan'" x-transition class="space-y-4">

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
                                    x-text="(b.kolam ? b.kolam.nama_kolam : 'Kolam #' + b.id_kolam) + ' - Hari ke-' + b.doc + ' (DOC ' + b.doc + ') • ' + b.fase + ' (' + b.jenis_ikan + ')'">
                            </option>
                        </template>
                    </select>

                    <template x-if="selectedBatch">
                        <div class="p-3 bg-sky-50/80 rounded-2xl border border-sky-200 space-y-2 text-xs text-sky-950 mt-2">
                            <div class="flex items-center justify-between gap-2">
                                <span class="font-extrabold" x-text="'Ikan: ' + selectedBatch.jenis_ikan"></span>
                                <div class="flex items-center gap-1.5 shrink-0">
                                    <span class="text-[10px] font-black px-2 py-0.5 rounded-md bg-white border border-sky-200 text-sky-800"
                                          x-text="'Hari ke-' + selectedBatch.doc + ' (DOC ' + selectedBatch.doc + ')'">
                                    </span>
                                    <span class="text-[10px] font-black px-2 py-0.5 rounded-md border"
                                          :class="selectedBatch.fase_badge || 'bg-sky-100 text-sky-800 border-sky-200'"
                                          x-text="selectedBatch.fase">
                                    </span>
                                </div>
                            </div>

                            <div class="pt-2 border-t border-sky-200/70 flex flex-col gap-1.5 text-[11px]">
                                <div class="flex items-center justify-between text-slate-600">
                                    <span>Biomassa Kolam:</span>
                                    <strong class="text-slate-900 font-extrabold" x-text="Number(selectedBatch.biomassa_est).toLocaleString('id-ID') + ' kg'"></strong>
                                </div>
                                <div class="flex items-center gap-1.5 text-sky-950 font-bold bg-white/80 p-2.5 rounded-xl border border-sky-100 text-[11px]">
                                    <i class="fa-solid fa-lightbulb text-amber-500 shrink-0"></i>
                                    <span>Rekomendasi Fase: <strong class="text-[#051B44]" x-text="selectedBatch.rekomendasi_pakan"></strong></span>
                                </div>
                            </div>
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
                                   @input="if(Number(form.kg_daun) > 100) form.kg_daun = 100; recalculateCost()"
                                   class="w-full px-3.5 py-2.5 rounded-2xl border border-slate-200 text-xs font-extrabold text-slate-900 bg-slate-50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#0284C7]">
                        </div>
                    </div>
                </div>

                <!-- Field 5: Jenis Daun / Suplemen Tambahan -->
                <div class="space-y-1">
                    <label class="text-[10px] font-extrabold uppercase text-slate-500 tracking-wider block">JENIS DAUN / SUPLEMEN</label>
                    <select x-model="form.id_stok_suplemen" @change="onSuplemenChange()"
                            class="w-full px-3.5 py-2.5 rounded-2xl border border-slate-200 text-xs font-semibold text-slate-800 bg-slate-50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#0284C7] transition-all cursor-pointer">
                        <option value="">Tidak ada tambahan daun / suplemen</option>
                        <template x-for="sup in suplemenList" :key="sup.id_stok_pakan">
                            <option :value="sup.id_stok_pakan" 
                                    x-text="sup.nama_pakan + ' (Sisa: ' + sup.stok_tersisa + ' ' + sup.satuan + ' - Rp ' + Number(sup.harga_per_satuan).toLocaleString('id-ID') + '/' + sup.satuan + ')'">
                            </option>
                        </template>
                    </select>

                    <template x-if="selectedSuplemen">
                        <div class="flex items-center justify-between text-[11px] text-emerald-800 bg-emerald-50 px-2.5 py-1 rounded-xl border border-emerald-200/80 mt-1 font-medium">
                            <span>Sisa: <strong class="text-emerald-950 font-extrabold" x-text="selectedSuplemen.stok_tersisa + ' ' + selectedSuplemen.satuan"></strong></span>
                            <span>Harga: <strong class="text-emerald-900 font-extrabold" x-text="'Rp ' + Number(selectedSuplemen.harga_per_satuan).toLocaleString('id-ID') + '/' + selectedSuplemen.satuan"></strong></span>
                        </div>
                    </template>
                </div>

                <!-- Field 6: Total Biaya Pakan (EST) -->
                <div class="space-y-1">
                    <label class="text-[10px] font-extrabold uppercase text-slate-500 tracking-wider block">TOTAL BIAYA PAKAN (EST)</label>
                    <div class="px-3.5 py-2.5 rounded-2xl border border-slate-200 bg-slate-50 flex items-center justify-between text-xs">
                        <span class="font-bold text-slate-400">Rp</span>
                        <span class="font-black text-[#0B2570] text-sm" x-text="Number(form.total_biaya || 0).toLocaleString('id-ID')">0</span>
                    </div>
                </div>

                <!-- Parameter Kualitas Air Kolam (Suhu, pH, Kondisi) -->
                <div class="bg-sky-50/70 p-4 rounded-2xl border border-sky-200/80 space-y-3">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <i class="fa-solid fa-droplet text-sky-600 text-sm"></i>
                            <span class="text-xs font-extrabold text-slate-800">Kontrol Kualitas Air</span>
                        </div>
                        <span class="text-[11px] font-black text-sky-700 bg-sky-100 px-2 py-0.5 rounded-md">Standar CBIB</span>
                    </div>

                    <!-- pH Air Slider -->
                    <div class="space-y-1">
                        <div class="flex items-center justify-between text-xs">
                            <span class="font-bold text-slate-600">pH Air:</span>
                            <span class="text-sm font-black text-[#0B2570]" x-text="parseFloat(form.ph_air).toFixed(1)">7.2</span>
                        </div>
                        <input type="range" min="5.0" max="9.0" step="0.1" x-model="form.ph_air"
                               class="w-full accent-[#0284C7] cursor-pointer">
                        <div class="grid grid-cols-3 gap-1 text-[9px] font-extrabold text-center text-slate-500">
                            <span :class="form.ph_air < 6.5 ? 'text-amber-600 font-black' : ''">5.0 (Asam)</span>
                            <span :class="form.ph_air >= 6.5 && form.ph_air <= 7.5 ? 'text-emerald-600 font-black' : ''">6.5 - 7.5 (Optimal)</span>
                            <span :class="form.ph_air > 7.5 ? 'text-rose-600 font-black' : ''">9.0 (Basa)</span>
                        </div>
                    </div>

                    <!-- Suhu Air & Visual Kondisi Air -->
                    <div class="grid grid-cols-2 gap-2.5 pt-1">
                        <div>
                            <label class="text-[10px] font-extrabold uppercase text-slate-500 block mb-1">
                                SUHU AIR (°C)
                            </label>
                            <input type="number" step="0.1" min="15" max="40" x-model="form.suhu_air"
                                   class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs font-extrabold text-slate-800 bg-white focus:outline-none focus:ring-2 focus:ring-[#0284C7]">
                        </div>
                        <div>
                            <label class="text-[10px] font-extrabold uppercase text-slate-500 block mb-1">
                                KONDISI FISIK AIR
                            </label>
                            <select x-model="form.kondisi_air"
                                    class="w-full px-2.5 py-2 rounded-xl border border-slate-200 text-xs font-semibold text-slate-800 bg-white focus:outline-none focus:ring-2 focus:ring-[#0284C7]">
                                <option value="Normal / Kehijauan Sehat">Hijau Sehat</option>
                                <option value="Jernih (Baru Ganti)">Jernih</option>
                                <option value="Cokelat Keruh">Cokelat Keruh</option>
                                <option value="Berbusa / Bau Pekat">Berbusa / Bau</option>
                            </select>
                        </div>
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

    <!-- ========================================== -->
    <!-- TAB 2: SAMPLING BOBOT MINGGUAN (CBIB)      -->
    <!-- ========================================== -->
    <div x-show="activeTab === 'sampling'" x-transition class="space-y-4">

        <!-- Standar CBIB Info Banner -->
        <div class="bg-indigo-50 border border-indigo-200 rounded-3xl p-4 text-indigo-950 text-xs space-y-1.5">
            <div class="flex items-center gap-2 font-black text-indigo-900">
                <i class="fa-solid fa-calculator text-indigo-600"></i>
                <span>Standar CBIB Sampling Bobot (7-10 Hari Sekali)</span>
            </div>
            <p class="text-[11px] text-indigo-800 leading-relaxed">
                Timbang 20–30 ekor ikan acak untuk mendapatkan <strong>ABW (Average Body Weight)</strong>. Sistem otomatis menghitung <strong>Size Ekor/Kg</strong> dan merekalibrasi proyeksi biomassa kolam.
            </p>
        </div>

        <!-- Sampling Form Card -->
        <div class="bg-white rounded-3xl border border-slate-200/90 p-5 shadow-xs space-y-4">
            <form @submit.prevent="handleSaveSampling()" class="space-y-4">

                <!-- Pilih Batch Pembesaran -->
                <div class="space-y-1">
                    <label class="text-[10px] font-extrabold uppercase text-slate-500 tracking-wider block">
                        KOLAM & BATCH PEMBESARAN <span class="text-rose-500">*</span>
                    </label>
                    <select x-model="samplingForm.id_pembesaran" required
                            class="w-full px-3.5 py-2.5 rounded-2xl border border-slate-200 text-xs font-semibold text-slate-800 bg-slate-50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#0284C7] transition-all cursor-pointer">
                        <option value="">Pilih Batch Pembesaran...</option>
                        <template x-for="b in activeBatches" :key="b.id_pembesaran">
                            <option :value="b.id_pembesaran" 
                                    x-text="(b.kolam ? b.kolam.nama_kolam : 'Kolam #' + b.id_kolam) + ' - Hari ke-' + b.doc + ' (' + b.jenis_ikan + ') • Est Biomassa: ' + b.biomassa_est + ' kg'">
                            </option>
                        </template>
                    </select>
                </div>

                <!-- Tanggal Sampling -->
                <div class="space-y-1">
                    <label class="text-[10px] font-extrabold uppercase text-slate-500 tracking-wider block">
                        TANGGAL SAMPLING <span class="text-rose-500">*</span>
                    </label>
                    <input type="date" x-model="samplingForm.tgl_sampling" required
                           class="w-full px-3.5 py-2.5 rounded-2xl border border-slate-200 text-xs font-semibold text-slate-800 bg-slate-50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#0284C7] transition-all">
                </div>

                <!-- Sample Parameters (Jumlah Ekor & Bobot Gram) -->
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="text-[10px] font-extrabold uppercase text-slate-500 block mb-1">
                            JUMLAH IKAN (EKOR) <span class="text-rose-500">*</span>
                        </label>
                        <input type="number" min="1" max="500" x-model="samplingForm.jumlah_sample_ekor" required
                               class="w-full px-3.5 py-2.5 rounded-2xl border border-slate-200 text-xs font-extrabold text-slate-900 bg-slate-50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#0284C7]">
                    </div>
                    <div>
                        <label class="text-[10px] font-extrabold uppercase text-slate-500 block mb-1">
                            TOTAL BOBOT (GRAM) <span class="text-rose-500">*</span>
                        </label>
                        <input type="number" step="0.1" min="1" x-model="samplingForm.total_bobot_sample_gr" required
                               class="w-full px-3.5 py-2.5 rounded-2xl border border-slate-200 text-xs font-extrabold text-slate-900 bg-slate-50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#0284C7]">
                    </div>
                </div>

                <!-- Live Auto-Calculation Box -->
                <div class="p-4 bg-sky-50/80 rounded-2xl border border-sky-200 space-y-2.5">
                    <div class="flex items-center justify-between text-xs font-bold text-sky-950 pb-2 border-b border-sky-200/60">
                        <span>Hasil Perhitungan Otomatis:</span>
                        <span class="text-[10px] px-2 py-0.5 rounded-md bg-white border border-sky-200 font-extrabold text-[#0284C7]">Real-Time</span>
                    </div>

                    <div class="grid grid-cols-2 gap-2 text-xs">
                        <div class="p-2.5 rounded-xl bg-white border border-sky-100 space-y-0.5">
                            <span class="text-[10px] text-slate-500 font-semibold block">Rata-rata Bobot (ABW):</span>
                            <span class="text-base font-black text-[#0B2570]" x-text="calculatedAbw + ' gr/ekor'"></span>
                        </div>
                        <div class="p-2.5 rounded-xl bg-white border border-sky-100 space-y-0.5">
                            <span class="text-[10px] text-slate-500 font-semibold block">Perkiraan Size Pasar:</span>
                            <span class="text-base font-black text-emerald-700" x-text="calculatedSize + ' ekor / kg'"></span>
                        </div>
                    </div>
                </div>

                <!-- Catatan Pengamatan -->
                <div class="space-y-1">
                    <label class="text-[10px] font-extrabold uppercase text-slate-500 tracking-wider block">
                        CATATAN PENGAMATAN FISIK IKAN
                    </label>
                    <textarea x-model="samplingForm.catatan" rows="2" placeholder="Contoh: Nafsu makan agresif, warna kulit bersih berkilau, tidak ada jamur..."
                              class="w-full px-3.5 py-2.5 rounded-2xl border border-slate-200 text-xs font-medium text-slate-800 bg-slate-50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#0284C7] transition-all"></textarea>
                </div>

                <!-- Tombol Simpan Sampling -->
                <button type="submit" :disabled="isSubmittingSampling"
                        class="w-full py-3.5 rounded-2xl bg-[#0B2570] hover:bg-[#051B44] active:scale-[0.99] text-white font-extrabold text-xs flex items-center justify-center gap-2 shadow-md shadow-slate-900/10 transition-all cursor-pointer">
                    <i class="fa-solid fa-floppy-disk text-xs"></i>
                    <span x-text="isSubmittingSampling ? 'Menyimpan & Mengupdate Biomassa...' : 'Simpan Data Sampling Bobot'"></span>
                </button>

            </form>
        </div>

        <!-- Riwayat Sampling Terkini -->
        <div class="bg-white rounded-3xl border border-slate-200/90 p-4 sm:p-5 shadow-xs space-y-3">
            <div class="flex items-center justify-between pb-2 border-b border-slate-100">
                <h3 class="text-xs font-extrabold text-slate-900 flex items-center gap-1.5">
                    <i class="fa-solid fa-clock-rotate-left text-sky-600"></i>
                    <span>Riwayat Sampling Bobot Terkini</span>
                </h3>
                <span class="text-[10px] font-bold text-slate-400" x-text="recentSamplings.length + ' Catatan'"></span>
            </div>

            <div class="space-y-2.5">
                <template x-for="item in recentSamplings" :key="item.id_sampling">
                    <div class="p-3 rounded-2xl bg-slate-50 border border-slate-100 flex items-center justify-between text-xs">
                        <div class="space-y-0.5">
                            <div class="flex items-center gap-2">
                                <span class="font-extrabold text-slate-900" 
                                      x-text="item.batch_pembesaran && item.batch_pembesaran.kolam ? item.batch_pembesaran.kolam.nama_kolam : 'Batch #' + item.id_pembesaran">
                                </span>
                                <span class="text-[10px] font-extrabold text-indigo-700 bg-indigo-50 border border-indigo-200 px-2 py-0.5 rounded-md"
                                      x-text="item.abw_gram ? (item.abw_gram + ' gr/ekor') : '-'">
                                </span>
                            </div>
                            <p class="text-[11px] text-slate-400" 
                               x-text="item.tgl_sampling + ' • ' + item.jumlah_sample_ekor + ' ekor (' + item.total_bobot_sample_gr + ' gr)' + (item.catatan ? (' • ' + item.catatan) : '')">
                            </p>
                        </div>

                        <div class="text-right">
                            <span class="text-[10px] font-bold text-slate-400 block">Perkiraan Size:</span>
                            <span class="font-black text-emerald-700" 
                                  x-text="item.abw_gram > 0 ? (Math.round(1000 / item.abw_gram) + ' /kg') : '-'">
                            </span>
                        </div>
                    </div>
                </template>

                <template x-if="recentSamplings.length === 0">
                    <div class="py-6 text-center text-slate-400 text-xs">
                        <i class="fa-solid fa-scale-balanced text-xl text-slate-300 block mb-1"></i>
                        Belum ada riwayat sampling bobot pertumbuhan.
                    </div>
                </template>
            </div>
        </div>

    </div>

</div>
@endsection

@push('scripts')
<script>
function petugasPembesaranLogComponent() {
    return {
        activeTab: 'pakan',
        activeBatches: {!! json_encode($activeBatches ?? []) !!},
        stokPakanList: {!! json_encode($stokPakanList ?? []) !!},
        logs: {!! json_encode($logs ?? []) !!},
        recentSamplings: {!! json_encode($recentSamplings ?? []) !!},
        recentWaterQuality: {!! json_encode($recentWaterQuality ?? []) !!},
        isSubmitting: false,
        isSubmittingSampling: false,

        form: {
            id_kolam: '',
            id_stok_pakan: '',
            id_stok_suplemen: '',
            tgl_log: new Date().toISOString().split('T')[0],
            kg_pelet: 10,
            kg_daun: 0,
            jenis_daun: '',
            total_biaya: 125000,
            ph_air: 7.2,
            suhu_air: 28.5,
            kondisi_air: 'Normal / Kehijauan Sehat'
        },

        samplingForm: {
            id_pembesaran: '',
            tgl_sampling: new Date().toISOString().split('T')[0],
            jumlah_sample_ekor: 20,
            total_bobot_sample_gr: 3000,
            catatan: ''
        },

        init() {
            if (this.activeBatches.length > 0) {
                this.form.id_kolam = this.activeBatches[0].id_kolam;
                this.samplingForm.id_pembesaran = this.activeBatches[0].id_pembesaran;
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

        get suplemenList() {
            return this.stokPakanList.filter(item => {
                const name = (item.nama_pakan || '').toLowerCase();
                return name.includes('daun') || name.includes('singkong') || name.includes('talas') || name.includes('pepaya') || name.includes('azolla') || name.includes('lemna') || name.includes('maggot') || name.includes('kangkung') || name.includes('suplemen') || name.includes('organik');
            });
        },

        get selectedSuplemen() {
            if (!this.form.id_stok_suplemen) return null;
            return this.stokPakanList.find(p => p.id_stok_pakan == this.form.id_stok_suplemen) || null;
        },

        get calculatedAbw() {
            const jml = Number(this.samplingForm.jumlah_sample_ekor) || 1;
            const bobot = Number(this.samplingForm.total_bobot_sample_gr) || 0;
            return Math.max(0, Math.round((bobot / jml) * 10) / 10);
        },

        get calculatedSize() {
            const abw = this.calculatedAbw;
            if (abw <= 0) return 0;
            return Math.round(1000 / abw);
        },

        onKolamChange() {
            if (this.selectedBatch) {
                this.form.kg_pelet = this.selectedBatch.est_pelet_kg || Math.max(1, Math.round(this.selectedBatch.biomassa_est * 0.025 * 10) / 10);

                if (this.stokPakanList.length > 0) {
                    const fKey = (this.selectedBatch.fase_key || '').toLowerCase();
                    let matchedPakan = null;
                    if (fKey === 'starter') {
                        matchedPakan = this.stokPakanList.find(p => /starter|pf-1000|781-1|benih/i.test(p.nama_pakan));
                    } else if (fKey === 'grower') {
                        matchedPakan = this.stokPakanList.find(p => /grower|781-2|apung/i.test(p.nama_pakan));
                    } else if (fKey === 'finisher') {
                        matchedPakan = this.stokPakanList.find(p => /finisher|781-3|panen|hi-pro/i.test(p.nama_pakan));
                    }
                    if (matchedPakan) {
                        this.form.id_stok_pakan = matchedPakan.id_stok_pakan;
                    }
                }

                this.recalculateCost();
            }
        },

        onSuplemenChange() {
            if (this.selectedSuplemen) {
                this.form.jenis_daun = this.selectedSuplemen.nama_pakan;
            } else {
                this.form.jenis_daun = '';
            }
            this.recalculateCost();
        },

        recalculateCost() {
            const kgPelet = Number(this.form.kg_pelet) || 0;
            const kgDaun = Number(this.form.kg_daun) || 0;

            const itemPelet = this.selectedPakan;
            const pricePelet = itemPelet ? (Number(itemPelet.harga_per_satuan) || 12500) : 12500;

            const itemDaun = this.selectedSuplemen;
            const priceDaun = itemDaun ? (Number(itemDaun.harga_per_satuan) || 0) : 0;

            this.form.total_biaya = Math.round((kgPelet * pricePelet) + (kgDaun * priceDaun));
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
                        id_stok_suplemen: this.form.id_stok_suplemen || null,
                        tgl_log: this.form.tgl_log,
                        kg_pelet: Number(this.form.kg_pelet) || 0,
                        kg_daun: Number(this.form.kg_daun) || 0,
                        jenis_daun: this.selectedSuplemen ? this.selectedSuplemen.nama_pakan : (this.form.jenis_daun || null),
                        total_biaya: Number(this.form.total_biaya) || 0,
                        ph_air: Number(this.form.ph_air) || 7.2,
                        suhu_air: Number(this.form.suhu_air) || 28.5,
                        kondisi_air: this.form.kondisi_air || 'Normal / Kehijauan Sehat'
                    })
                });

                const data = await res.json();
                if (res.ok && data.success) {
                    if (data.log) {
                        this.logs.unshift(data.log);
                    }
                    const usedPelet = Number(this.form.kg_pelet) || 0;
                    const usedDaun = Number(this.form.kg_daun) || 0;

                    if (this.selectedPakan && usedPelet > 0) {
                        this.selectedPakan.stok_tersisa = Math.max(0, Number(this.selectedPakan.stok_tersisa) - usedPelet);
                    }
                    if (this.selectedSuplemen && usedDaun > 0) {
                        this.selectedSuplemen.stok_tersisa = Math.max(0, Number(this.selectedSuplemen.stok_tersisa) - usedDaun);
                    }

                    if (typeof triggerToast === 'function') {
                        triggerToast(data.message || 'Log pakan pembesaran berhasil disimpan!', 'success');
                    } else {
                        alert(data.message || 'Log pakan pembesaran berhasil disimpan!');
                    }

                    this.form.kg_pelet = 10;
                    this.form.kg_daun = 0;
                    this.form.id_stok_suplemen = '';
                    this.form.jenis_daun = '';
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
        },

        async handleSaveSampling() {
            if (!this.samplingForm.id_pembesaran) {
                if (typeof triggerToast === 'function') {
                    triggerToast('Pilih batch pembesaran terlebih dahulu!', 'error');
                } else {
                    alert('Pilih batch pembesaran terlebih dahulu!');
                }
                return;
            }

            this.isSubmittingSampling = true;
            try {
                const res = await fetch('{{ route('petugas.pembesaran.sampling.store') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        id_pembesaran: this.samplingForm.id_pembesaran,
                        tgl_sampling: this.samplingForm.tgl_sampling,
                        jumlah_sample_ekor: Number(this.samplingForm.jumlah_sample_ekor),
                        total_bobot_sample_gr: Number(this.samplingForm.total_bobot_sample_gr),
                        catatan: this.samplingForm.catatan || null
                    })
                });

                const data = await res.json();
                if (res.ok && data.success) {
                    if (data.sampling) {
                        const targetBatch = this.activeBatches.find(b => b.id_pembesaran == this.samplingForm.id_pembesaran);
                        if (targetBatch) {
                            data.sampling.batch_pembesaran = {
                                kolam: targetBatch.kolam
                            };
                            if (data.sampling.biomassa_koreksi_kg) {
                                targetBatch.biomassa_est = data.sampling.biomassa_koreksi_kg;
                            }
                        }
                        this.recentSamplings.unshift(data.sampling);
                    }

                    if (typeof triggerToast === 'function') {
                        triggerToast(data.message || 'Data sampling pertumbuhan berhasil disimpan!', 'success');
                    } else {
                        alert(data.message || 'Data sampling pertumbuhan berhasil disimpan!');
                    }

                    this.samplingForm.catatan = '';
                } else {
                    if (typeof triggerToast === 'function') {
                        triggerToast(data.message || 'Gagal menyimpan sampling.', 'error');
                    } else {
                        alert(data.message || 'Gagal menyimpan sampling.');
                    }
                }
            } catch (err) {
                if (typeof triggerToast === 'function') {
                    triggerToast('Terjadi kesalahan jaringan.', 'error');
                } else {
                    alert('Terjadi kesalahan jaringan.');
                }
            } finally {
                this.isSubmittingSampling = false;
            }
        }
    };
}
</script>
@endpush

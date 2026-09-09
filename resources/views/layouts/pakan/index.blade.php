@extends('layouts.app')

@section('title', 'Manajemen Stok & Log Pakan - AMS BUDIDAYA')

@section('content')
<div class="space-y-6" x-data="pakanHubComponent()">

    <!-- Flash Notification Messages -->
    @if(session('success'))
    <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-bold flex items-center gap-3 shadow-xs">
        <i class="fa-solid fa-circle-check text-emerald-500 text-base"></i>
        <span>{{ session('success') }}</span>
    </div>
    @endif

    @if(session('error'))
    <div class="p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 text-xs font-bold flex items-center gap-3 shadow-xs">
        <i class="fa-solid fa-circle-exclamation text-rose-500 text-base"></i>
        <span>{{ session('error') }}</span>
    </div>
    @endif

    <!-- Page Header & Action -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-2">
        <div>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">Log Pemberian Pakan</h1>
            <p class="text-xs sm:text-sm text-slate-500 font-medium mt-1">
                Pencatatan konsumsi pakan harian kolam pembibitan &amp; pembesaran, serta monitoring riwayat pemberian pakan.
            </p>
        </div>
        <div class="flex items-center gap-2.5">
            <a href="{{ route('stok-pakan') }}" 
               class="px-4 py-2.5 rounded-xl bg-[#031B4E] hover:bg-sky-950 text-white font-bold text-xs sm:text-sm shadow-md shadow-sky-950/20 transition-all flex items-center justify-center gap-2 cursor-pointer active:scale-98">
                <i class="fa-solid fa-boxes-stacked text-xs"></i>
                <span>Kelola Master Stok Pakan</span>
            </a>
        </div>
    </div>

    <!-- Main Form Log Pemberian Pakan Harian -->
    <div class="bg-white rounded-3xl border border-slate-200/90 shadow-sm p-6 sm:p-8 space-y-7">
        
        <!-- Header inside Form -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-5 border-b border-slate-100">
            <div class="flex items-center gap-3.5">
                <div class="w-12 h-12 rounded-2xl bg-sky-50 text-sky-600 flex items-center justify-center shrink-0 text-xl border border-sky-100/80 shadow-xs">
                    <i class="fa-solid fa-clipboard-list"></i>
                </div>
                <div>
                    <h3 class="text-base sm:text-lg font-black text-slate-900">Formulir Log Pemberian Pakan Harian</h3>
                    <p class="text-xs text-slate-500 mt-0.5">Catat pakan per kolam aktif &amp; saldo stok otomatis berkurang secara real-time.</p>
                </div>
            </div>

            <!-- Toggle Kategori Fase -->
            <div class="flex items-center p-1.5 bg-slate-100 rounded-2xl text-xs font-extrabold text-slate-600 border border-slate-200/60">
                <button type="button" @click="selectFase('pembesaran')" 
                        :class="form.kategori_fase === 'pembesaran' ? 'bg-[#051B44] text-white shadow-sm rounded-xl' : 'hover:text-slate-900'"
                        class="px-4 py-2 transition-all cursor-pointer flex items-center gap-2">
                    <i class="fa-solid fa-fish"></i>
                    <span>Kolam Pembesaran</span>
                </button>
                <button type="button" @click="selectFase('pembibitan')" 
                        :class="form.kategori_fase === 'pembibitan' ? 'bg-emerald-700 text-white shadow-sm rounded-xl' : 'hover:text-emerald-700'"
                        class="px-4 py-2 transition-all cursor-pointer flex items-center gap-2">
                    <i class="fa-solid fa-seedling"></i>
                    <span>Kolam Pembibitan (Hatchery)</span>
                </button>
            </div>
        </div>

        <!-- Form Elements -->
        <form @submit.prevent="handleSaveLog()" class="space-y-6">
            
            <!-- SECTION 1: Target Kolam & Tanggal Log -->
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-5 items-start">
                
                <!-- Kolam Aktif (7 cols) -->
                <div class="lg:col-span-7 space-y-1.5">
                    <label class="text-[11px] font-black uppercase tracking-wider text-slate-600 flex items-center gap-1.5">
                        <i class="fa-solid fa-water text-sky-500 text-xs"></i>
                        <span>Pilih Kolam Aktif (<span x-text="form.kategori_fase.toUpperCase()"></span>)</span>
                        <span class="text-rose-500">*</span>
                    </label>
                    
                    <!-- Dropdown Kolam Pembesaran -->
                    <template x-if="form.kategori_fase === 'pembesaran'">
                        <div>
                            <template x-if="activeKolams.length > 0">
                                <select x-model="form.id_kolam" @change="onKolamChange()" 
                                        class="w-full px-4 py-3 rounded-2xl border border-slate-200 text-xs font-bold text-slate-800 bg-slate-50/50 hover:bg-white focus:bg-white focus:outline-none focus:ring-2 focus:ring-sky-500 focus:border-sky-500 transition-all cursor-pointer shadow-xs">
                                    <option value="">-- Pilih Kolam Pembesaran yang Sedang Aktif --</option>
                                    <template x-for="k in activeKolams" :key="k.id_kolam">
                                        <option :value="k.id_kolam" x-text="k.label"></option>
                                    </template>
                                </select>
                            </template>
                            <template x-if="activeKolams.length === 0">
                                <div class="p-3.5 bg-amber-50 rounded-2xl border border-amber-200 text-amber-900 text-xs font-semibold flex items-center justify-between gap-2">
                                    <div class="flex items-center gap-2">
                                        <i class="fa-solid fa-triangle-exclamation text-amber-600"></i>
                                        <span>Belum ada kolam pembesaran yang terisi ikan/siklus aktif.</span>
                                    </div>
                                    <a href="{{ route('pembudidaya') }}" class="px-3 py-1.5 bg-amber-200 hover:bg-amber-300 text-amber-950 rounded-xl text-xs font-black transition-colors">
                                        Tebar Ikan
                                    </a>
                                </div>
                            </template>
                        </div>
                    </template>

                    <!-- Dropdown Kolam Pembibitan -->
                    <template x-if="form.kategori_fase === 'pembibitan'">
                        <div>
                            <template x-if="hatcheryKolams.length > 0">
                                <select x-model="form.id_kolam" @change="onHatcheryKolamChange()" 
                                        class="w-full px-4 py-3 rounded-2xl border border-slate-200 text-xs font-bold text-slate-800 bg-slate-50/50 hover:bg-white focus:bg-white focus:outline-none focus:ring-2 focus:ring-sky-500 focus:border-sky-500 transition-all cursor-pointer shadow-xs">
                                    <option value="">-- Pilih Kolam Pembibitan yang Sedang Aktif --</option>
                                    <template x-for="hk in hatcheryKolams" :key="hk.id_kolam">
                                        <option :value="hk.id_kolam" x-text="hk.label"></option>
                                    </template>
                                </select>
                            </template>
                            <template x-if="hatcheryKolams.length === 0">
                                <div class="p-3.5 bg-emerald-50 rounded-2xl border border-emerald-200 text-emerald-900 text-xs font-semibold flex items-center gap-2">
                                    <i class="fa-solid fa-seedling text-emerald-600"></i>
                                    <span>Belum ada kolam hatchery yang sedang terisi benih aktif.</span>
                                </div>
                            </template>
                        </div>
                    </template>
                </div>

                <!-- Tanggal Log (5 cols) -->
                <div class="lg:col-span-5 space-y-1.5">
                    <label class="text-[11px] font-black uppercase tracking-wider text-slate-600 flex items-center gap-1.5">
                        <i class="fa-regular fa-calendar-check text-sky-500 text-xs"></i>
                        <span>Tanggal Pemberian Pakan</span>
                        <span class="text-rose-500">*</span>
                    </label>
                    <input type="date" x-model="form.tgl_log"
                           class="w-full px-4 py-3 rounded-2xl border border-slate-200 text-xs font-bold text-slate-800 bg-slate-50/50 hover:bg-white focus:bg-white focus:outline-none focus:ring-2 focus:ring-sky-500 focus:border-sky-500 transition-all shadow-xs">
                </div>
            </div>

            <!-- Detail Batch Terpilih Info Card -->
            <template x-if="selectedKolamInfo">
                <div class="space-y-3">
                    <div class="p-4 sm:p-5 bg-gradient-to-r from-sky-50/90 via-blue-50/40 to-white rounded-2xl border border-sky-200/70 flex flex-col sm:flex-row sm:items-center justify-between gap-4 text-xs shadow-xs">
                        <div class="flex items-center gap-3.5">
                            <div class="w-11 h-11 rounded-2xl bg-white text-sky-600 border border-sky-200 flex items-center justify-center font-bold text-lg shrink-0 shadow-xs">
                                <i class="fa-solid" :class="form.kategori_fase === 'pembibitan' ? 'fa-seedling text-emerald-600' : 'fa-fish text-sky-600'"></i>
                            </div>
                            <div class="space-y-1">
                                <div class="flex flex-wrap items-center gap-2">
                                    <span class="font-black text-slate-900 text-sm" x-text="selectedKolamInfo.nama_kolam"></span>
                                    <span class="text-[10px] font-bold text-sky-700 bg-white px-2.5 py-0.5 rounded-lg border border-sky-200" x-text="selectedKolamInfo.batch_id"></span>
                                    <span class="text-[10px] font-black px-2.5 py-0.5 rounded-lg bg-white border border-sky-200 text-sky-900"
                                          x-text="'Hari ke-' + selectedKolamInfo.doc + ' (DOC ' + selectedKolamInfo.doc + ')'">
                                    </span>
                                    <span class="text-[10px] font-black px-2.5 py-0.5 rounded-lg"
                                          :class="isTelurPhase ? 'bg-amber-100 text-amber-900 border border-amber-200' : 'bg-sky-100 text-sky-800 border border-sky-200'"
                                          x-text="'Fase: ' + selectedKolamInfo.fase">
                                    </span>
                                </div>
                                <div class="text-xs text-slate-600 flex flex-wrap items-center gap-2 pt-0.5">
                                    <span class="font-medium" x-text="form.kategori_fase === 'pembesaran' ? (selectedKolamInfo.jenis_ikan + ' • Estimasi Biomassa: ' + selectedKolamInfo.biomassa_format + ' kg') : (selectedKolamInfo.jenis_ikan + ' • Jumlah Benih: ' + Number(selectedKolamInfo.jumlah_bibit).toLocaleString('id-ID') + ' ekor')"></span>
                                    <span class="text-slate-300">•</span>
                                    <span class="font-bold text-sky-950 flex items-center gap-1.5 bg-white/80 px-2 py-0.5 rounded-md border border-sky-100">
                                        <i class="fa-solid fa-lightbulb text-amber-500"></i>
                                        <span>Rekomendasi: <strong :class="isTelurPhase ? 'text-amber-800' : 'text-sky-900'" x-text="selectedKolamInfo.rekomendasi_pakan"></strong></span>
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div class="shrink-0">
                            <template x-if="isTelurPhase">
                                <span class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-full text-xs font-extrabold bg-amber-100 text-amber-900 border border-amber-300 shadow-xs">
                                    <i class="fa-solid fa-ban text-amber-600"></i>
                                    <span>Tanpa Pakan (Fase Telur)</span>
                                </span>
                            </template>
                            <template x-if="!isTelurPhase && selectedKolamInfo.is_fed_today">
                                <span class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-full text-xs font-extrabold bg-emerald-100 text-emerald-800 border border-emerald-300 shadow-xs">
                                    <i class="fa-solid fa-circle-check text-emerald-600"></i>
                                    <span>Sudah Diberi Pakan Hari Ini</span>
                                </span>
                            </template>
                            <template x-if="!isTelurPhase && !selectedKolamInfo.is_fed_today">
                                <span class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-full text-xs font-extrabold bg-amber-100 text-amber-800 border border-amber-300 shadow-xs">
                                    <i class="fa-solid fa-clock text-amber-600"></i>
                                    <span>Belum Diberi Pakan Hari Ini</span>
                                </span>
                            </template>
                        </div>
                    </div>

                    <!-- Warning Alert Banner Khusus Fase Telur -->
                    <template x-if="isTelurPhase">
                        <div class="p-3.5 bg-amber-50/90 rounded-2xl border border-amber-200 text-amber-900 text-xs flex items-start gap-2.5 shadow-xs">
                            <i class="fa-solid fa-triangle-exclamation text-amber-600 mt-0.5 shrink-0 text-sm"></i>
                            <div class="space-y-0.5">
                                <span class="font-black text-amber-950 block">Perhatian: Kolam Masih dalam Fase Telur / Inkubasi!</span>
                                <p class="text-[11px] text-amber-800 leading-relaxed">
                                    Telur dalam masa inkubasi belum memerlukan pakan buatan. Memberi pakan pada fase ini dapat menyebabkan pembusukan organik dan merusak kualitas air penetasan. Pemberian pakan akan otomatis aktif setelah telur menetas menjadi larva.
                                </p>
                            </div>
                        </div>
                    </template>
                </div>
            </template>

            <!-- SECTION 2: Rincian Pakan Utama & Suplemen -->
            <div class="p-5 sm:p-6 bg-slate-50/70 rounded-3xl border border-slate-200/80 space-y-5"
                 :class="isTelurPhase ? 'opacity-60 pointer-events-none' : ''">
                <div class="flex items-center justify-between pb-3 border-b border-slate-200/70">
                    <div class="flex items-center gap-2.5">
                        <div class="w-8 h-8 rounded-xl bg-sky-100 text-sky-600 flex items-center justify-center text-sm font-bold">
                            <i class="fa-solid fa-bowl-food"></i>
                        </div>
                        <div>
                            <h4 class="text-xs sm:text-sm font-black text-slate-900">Pakan Utama &amp; Takaran Harian</h4>
                            <p class="text-[11px] text-slate-400 font-medium">Pilih pakan dari stok gudang dan masukkan jumlah yang diberikan</p>
                        </div>
                    </div>
                    <span class="text-[10px] font-black uppercase px-2.5 py-1 rounded-lg"
                          :class="isTelurPhase ? 'bg-amber-100 text-amber-800' : 'bg-sky-100 text-sky-700'"
                          x-text="isTelurPhase ? 'Terkunci (Fase Telur)' : 'Wajib Diisi'">
                    </span>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-12 gap-5">
                    
                    <!-- Item Pakan Gudang (7 cols) -->
                    <div class="md:col-span-7 space-y-1.5">
                        <label class="text-[11px] font-extrabold uppercase tracking-wider text-slate-600 block">
                            ITEM PAKAN DARI GUDANG <span class="text-rose-500">*</span>
                        </label>
                        <select x-model="form.id_stok_pakan" @change="onStokPakanChange()" 
                                :disabled="isTelurPhase"
                                class="w-full px-4 py-3 rounded-2xl border border-slate-200 text-xs font-bold text-slate-800 bg-white focus:outline-none focus:ring-2 focus:ring-sky-500 focus:border-sky-500 transition-all cursor-pointer shadow-xs disabled:bg-slate-100 disabled:cursor-not-allowed">
                            <option value="">-- Pilih Jenis Pakan Gudang --</option>
                            <template x-for="sp in relevantStokList" :key="sp.id_stok_pakan">
                                <option :value="sp.id_stok_pakan" x-text="sp.nama_pakan + ' (Sisa: ' + sp.stok_tersisa + ' ' + sp.satuan + ')'"></option>
                            </template>
                        </select>
                        
                        <template x-if="selectedPakanItem">
                            <div class="flex items-center justify-between px-3 py-2 bg-white rounded-xl border border-slate-200/70 text-xs font-medium text-slate-600 mt-2">
                                <span>Sisa Stok: <strong class="text-slate-900 font-extrabold" x-text="selectedPakanItem.stok_tersisa + ' ' + selectedPakanItem.satuan"></strong></span>
                                <span>Harga Acuan: <strong class="text-[#051B44] font-extrabold" x-text="'Rp ' + Number(selectedPakanItem.harga_per_satuan).toLocaleString('id-ID') + '/' + selectedPakanItem.satuan"></strong></span>
                            </div>
                        </template>
                    </div>

                    <!-- Jumlah Pakan Utama (5 cols) -->
                    <div class="md:col-span-5 space-y-1.5">
                        <label class="text-[11px] font-extrabold uppercase tracking-wider text-slate-600 block">
                            JUMLAH TAKARAN UTAMA <span class="text-rose-500">*</span>
                        </label>
                        <div class="flex items-center rounded-2xl border border-slate-200 bg-white focus-within:ring-2 focus-within:ring-sky-500 focus-within:border-sky-500 transition-all overflow-hidden shadow-xs">
                            <input type="number" x-model="form.kg_pelet"
                                :disabled="isTelurPhase"
                                onkeydown="if(event.key === '-' || event.key === 'e' || event.key === 'E' || event.key === '+') event.preventDefault()"
                                @input="if(form.kg_pelet !== '' && Number(form.kg_pelet) < 0) form.kg_pelet = 0; if(Number(form.kg_pelet) > 100) form.kg_pelet = 100; recalculateCost()"
                                step="0.1" min="0" max="100" :placeholder="isTelurPhase ? '0 (Fase Telur)' : '0.0'"
                                class="w-full px-4 py-3 text-sm font-black text-slate-900 bg-transparent border-0 focus:outline-none disabled:bg-slate-100 disabled:cursor-not-allowed">
                            <span class="px-4 py-3 text-xs font-black text-slate-600 bg-slate-100 border-l border-slate-200 shrink-0" x-text="selectedPakanItem ? selectedPakanItem.satuan.toUpperCase() : 'KG'"></span>
                        </div>
                        <span class="text-[10px] text-slate-400 block px-1">Maksimal 100 per pencatatan log</span>
                    </div>

                </div>

                <!-- Sub-baris: Pakan Tambahan / Daun / Suplemen (Hanya untuk Kolam Pembesaran, Terintegrasi Master Stok Pakan) -->
                <div x-show="form.kategori_fase === 'pembesaran'" 
                     x-transition:enter="transition ease-out duration-200"
                     x-transition:enter-start="opacity-0 -translate-y-2"
                     x-transition:enter-end="opacity-100 translate-y-0"
                     class="pt-4 border-t border-slate-200/60 grid grid-cols-1 md:grid-cols-12 gap-5 items-start">
                    <div class="md:col-span-7 space-y-1.5">
                        <label class="text-[11px] font-extrabold uppercase tracking-wider text-slate-500 block">
                            JENIS PAKAN TAMBAHAN / SUPLEMEN <span class="text-[10px] text-slate-400 font-normal lowercase">(opsional - khusus pembesaran)</span>
                        </label>
                        <select x-model="form.id_stok_suplemen" @change="onSuplemenChange()" 
                                class="w-full px-4 py-2.5 rounded-2xl border border-slate-200 text-xs font-bold text-slate-700 bg-white focus:outline-none focus:ring-2 focus:ring-sky-500 transition-all cursor-pointer shadow-xs">
                            <option value="">-- Tidak Ada / Pilih Pakan Suplemen --</option>
                            <template x-for="sup in suplemenList" :key="sup.id_stok_pakan">
                                <option :value="sup.id_stok_pakan" x-text="sup.nama_pakan + ' (Sisa: ' + sup.stok_tersisa + ' ' + sup.satuan + ' - Rp ' + Number(sup.harga_per_satuan).toLocaleString('id-ID') + '/' + sup.satuan + ')'"></option>
                            </template>
                        </select>

                        <template x-if="selectedSuplemenItem">
                            <div class="flex items-center justify-between px-3 py-1.5 bg-emerald-50/70 rounded-xl border border-emerald-200/70 text-[11px] font-medium text-emerald-800 mt-1.5">
                                <span>Sisa Suplemen: <strong class="text-emerald-950 font-extrabold" x-text="selectedSuplemenItem.stok_tersisa + ' ' + selectedSuplemenItem.satuan"></strong></span>
                                <span>Harga: <strong class="text-emerald-900 font-extrabold" x-text="'Rp ' + Number(selectedSuplemenItem.harga_per_satuan).toLocaleString('id-ID') + '/' + selectedSuplemenItem.satuan"></strong></span>
                            </div>
                        </template>
                    </div>

                    <div class="md:col-span-5 space-y-1.5">
                        <label class="text-[11px] font-extrabold uppercase tracking-wider text-slate-500 block">
                            JUMLAH PAKAN TAMBAHAN <span class="text-[10px] text-slate-400 font-normal lowercase">(opsional)</span>
                        </label>
                        <div class="flex items-center rounded-2xl border border-slate-200 bg-white focus-within:ring-2 focus-within:ring-sky-500 focus-within:border-sky-500 transition-all overflow-hidden shadow-xs">
                            <input type="number" x-model="form.kg_daun"
                                onkeydown="if(event.key === '-' || event.key === 'e' || event.key === 'E' || event.key === '+') event.preventDefault()"
                                @input="if(form.kg_daun !== '' && Number(form.kg_daun) < 0) form.kg_daun = 0; if(Number(form.kg_daun) > 100) form.kg_daun = 100; recalculateCost()"
                                step="0.1" min="0" max="100" placeholder="0.0"
                                class="w-full px-4 py-2.5 text-xs font-bold text-slate-900 bg-transparent border-0 focus:outline-none">
                            <span class="px-4 py-2.5 text-xs font-black text-slate-500 bg-slate-100 border-l border-slate-200 shrink-0" x-text="selectedSuplemenItem ? selectedSuplemenItem.satuan.toUpperCase() : 'KG'"></span>
                        </div>
                        <span class="text-[10px] text-slate-400 block px-1">Biaya: Takaran suplemen × Harga acuan suplemen</span>
                    </div>
                </div>

            </div>

            <!-- SECTION 3: Parameter Kolam & Finansial -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                
                <!-- pH Air Kolam Card -->
                <div class="p-5 bg-slate-50/70 rounded-3xl border border-slate-200/80 space-y-2.5">
                    <div class="flex items-center justify-between">
                        <label class="text-[11px] font-black uppercase tracking-wider text-slate-600 flex items-center gap-1.5">
                            <i class="fa-solid fa-droplet text-sky-500"></i>
                            <span>PH Air Kolam</span>
                        </label>
                        <span :class="Number(form.ph_air) >= 6.8 && Number(form.ph_air) <= 8.0 ? 'text-emerald-700 bg-emerald-100' : (Number(form.ph_air) > 0 ? 'text-amber-700 bg-amber-100' : 'text-slate-500 bg-slate-100')"
                              class="text-[10px] font-black px-2.5 py-0.5 rounded-full">
                            <span x-text="Number(form.ph_air) >= 6.8 && Number(form.ph_air) <= 8.0 ? 'Normal (Ideal)' : (Number(form.ph_air) > 0 ? 'Perlu Perhatian' : '-')"></span>
                        </span>
                    </div>

                    <div class="flex items-center rounded-2xl border border-slate-200 bg-white overflow-hidden focus-within:ring-2 focus-within:ring-sky-500 focus-within:border-sky-500 transition-all shadow-xs">
                        <input type="number" step="0.1" min="0" max="14" x-model="form.ph_air" placeholder="7.2"
                               onkeydown="if(event.key === '-' || event.key === 'e' || event.key === 'E') event.preventDefault()"
                               @input="if(form.ph_air !== '' && Number(form.ph_air) < 0) form.ph_air = 0"
                               class="w-full px-4 py-3 text-sm font-black text-slate-900 bg-transparent border-0 focus:outline-none">
                        <span class="px-4 py-3 text-xs font-black text-slate-500 bg-slate-100 border-l border-slate-200 shrink-0">pH</span>
                    </div>
                    <span class="text-[10px] text-slate-400 block px-1">Standar ideal kualitas air: 6.8 s/d 8.0 pH</span>
                </div>

                <!-- Estimasi Biaya Konsumsi Pakan Card -->
                <div class="p-5 bg-slate-50/70 rounded-3xl border border-slate-200/80 space-y-2.5">
                    <div class="flex items-center justify-between">
                        <label class="text-[11px] font-black uppercase tracking-wider text-slate-600 flex items-center gap-1.5">
                            <i class="fa-solid fa-calculator text-sky-500"></i>
                            <span>Estimasi Biaya Pakan</span>
                        </label>
                        <span class="text-[10px] font-extrabold text-sky-700 bg-sky-100 px-2 py-0.5 rounded-md">Otomatis Terkalkulasi</span>
                    </div>

                    <div class="flex items-center rounded-2xl border border-slate-200 bg-white overflow-hidden focus-within:ring-2 focus-within:ring-sky-500 focus-within:border-sky-500 transition-all shadow-xs">
                        <span class="px-4 py-3 text-xs font-black text-slate-600 bg-slate-100 border-r border-slate-200 shrink-0">Rp</span>
                        <input type="number" x-model="form.total_biaya" min="0"
                               onkeydown="if(event.key === '-' || event.key === 'e' || event.key === 'E') event.preventDefault()"
                               @input="if(form.total_biaya !== '' && Number(form.total_biaya) < 0) form.total_biaya = Math.abs(Number(form.total_biaya)) || 0"
                               class="w-full px-4 py-3 text-sm font-black text-slate-900 bg-transparent border-0 focus:outline-none">
                    </div>
                    <span class="text-[10px] text-slate-400 block px-1">Dihitung dari: Takaran kg × Harga acuan per kg</span>
                </div>

            </div>

            <!-- Form Actions (Full Width Footer) -->
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pt-5 border-t border-slate-100 mt-6">
                <div class="text-xs text-slate-500 flex items-center gap-2">
                    <div class="w-6 h-6 rounded-full bg-sky-100 text-sky-600 flex items-center justify-center shrink-0 text-xs">
                        <i class="fa-solid fa-shield-halved"></i>
                    </div>
                    <span class="font-medium">Stok gudang otomatis berkurang &amp; tercatat di log harian saat disimpan.</span>
                </div>
                <div class="flex items-center justify-end gap-3 shrink-0">
                    <button type="button" @click="resetLogForm()" 
                            class="px-5 py-3 rounded-2xl border border-slate-200 text-xs font-bold text-slate-600 hover:bg-slate-100 active:scale-95 transition-all cursor-pointer">
                        Reset Form
                    </button>
                    <button type="submit" 
                            :disabled="isSubmittingLog || isTelurPhase"
                            :class="isTelurPhase ? 'bg-slate-300 text-slate-500 cursor-not-allowed shadow-none' : 'bg-sky-600 hover:bg-sky-700 active:scale-95 text-white shadow-md shadow-sky-600/30'"
                            class="px-6 py-3 rounded-2xl font-black text-xs transition-all flex items-center gap-2 cursor-pointer disabled:opacity-50">
                        <i class="fa-solid text-xs" :class="isTelurPhase ? 'fa-ban' : 'fa-check text-white'"></i>
                        <span x-text="isTelurPhase ? 'Tidak Dapat Diberi Pakan (Fase Telur)' : (isSubmittingLog ? 'Menyimpan Data...' : 'Simpan Log &amp; Potong Stok')"></span>
                    </button>
                </div>
            </div>
        </form>
    </div>

    <!-- Riwayat Tabs (Riwayat Log Konsumsi Harian & Riwayat Pembelian Masuk) -->
    <div class="bg-white rounded-3xl border border-slate-200/90 shadow-sm space-y-5 p-6 sm:p-7 relative">
        
        <!-- Header & Nav Tabs + Single Unified Filter -->
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 pb-4 border-b border-slate-100">
            <!-- Tabs Switcher -->
            <div class="flex items-center gap-1.5 p-1 bg-slate-100 rounded-2xl border border-slate-200/60">
                <button type="button" @click="historyTab = 'log'; currentPage = 1" 
                        :class="historyTab === 'log' ? 'bg-[#051B44] text-white shadow-xs font-black' : 'text-slate-600 hover:text-slate-900 font-bold'"
                        class="px-4 py-2 rounded-xl text-xs transition-all cursor-pointer flex items-center gap-2">
                    <i class="fa-solid fa-clipboard-list"></i>
                    <span>Riwayat Log Pakan Harian</span>
                </button>
                <button type="button" @click="historyTab = 'pembelian'; currentPage = 1" 
                        :class="historyTab === 'pembelian' ? 'bg-[#051B44] text-white shadow-xs font-black' : 'text-slate-600 hover:text-slate-900 font-bold'"
                        class="px-4 py-2 rounded-xl text-xs transition-all cursor-pointer flex items-center gap-2">
                    <i class="fa-solid fa-cart-shopping"></i>
                    <span>Riwayat Masuk Supplier</span>
                </button>
            </div>

            <!-- Industry Standard Popover Calendar Date Picker -->
            <div class="relative" @click.outside="datePickerOpen = false">
                <div class="flex items-center gap-2">
                    <button type="button" 
                            @click="datePickerOpen = !datePickerOpen"
                            class="flex items-center gap-2.5 px-4 py-2 rounded-2xl border border-slate-200 bg-white text-xs font-bold text-slate-700 shadow-xs hover:bg-slate-50 hover:border-slate-300 transition-all cursor-pointer">
                        <i class="fa-regular fa-calendar-days text-[#0077C6] text-xs"></i>
                        <span x-text="periodLabel">Semua Periode</span>
                        <i class="fa-solid fa-chevron-down text-[10px] text-slate-400 ml-1 transition-transform" :class="datePickerOpen ? 'rotate-180' : ''"></i>
                    </button>

                    <template x-if="filterStartDate || filterEndDate">
                        <button type="button" 
                                @click="applyAll()"
                                title="Reset Filter ke Semua Catatan"
                                class="w-8 h-8 rounded-xl bg-slate-100 hover:bg-rose-50 hover:text-rose-600 text-slate-500 flex items-center justify-center transition-colors cursor-pointer">
                            <i class="fa-solid fa-xmark text-xs"></i>
                        </button>
                    </template>
                </div>

                <!-- Popover Calendar-Like Container -->
                <div x-show="datePickerOpen" 
                     x-transition:enter="transition ease-out duration-150"
                     x-transition:enter-start="opacity-0 translate-y-1 scale-95"
                     x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                     x-transition:leave="transition ease-in duration-100"
                     x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                     x-transition:leave-end="opacity-0 translate-y-1 scale-95"
                     class="absolute right-0 top-full mt-2 w-80 bg-white rounded-3xl shadow-2xl border border-slate-200 p-4 z-50 text-xs"
                     style="display: none;">
                    
                    <!-- Top Mode Switcher (Mingguan | Bulanan | Tahunan) -->
                    <div class="flex items-center bg-slate-100 p-1 rounded-2xl mb-3.5">
                        <button type="button" 
                                @click="pickerMode = 'minggu'"
                                :class="pickerMode === 'minggu' ? 'bg-[#051B44] text-white shadow-xs font-bold' : 'text-slate-600 hover:text-slate-900 font-semibold'"
                                class="flex-1 py-1.5 rounded-xl text-center text-xs transition-all cursor-pointer">
                            Mingguan
                        </button>
                        <button type="button" 
                                @click="pickerMode = 'bulan'"
                                :class="pickerMode === 'bulan' ? 'bg-[#051B44] text-white shadow-xs font-bold' : 'text-slate-600 hover:text-slate-900 font-semibold'"
                                class="flex-1 py-1.5 rounded-xl text-center text-xs transition-all cursor-pointer">
                            Bulanan
                        </button>
                        <button type="button" 
                                @click="pickerMode = 'tahun'"
                                :class="pickerMode === 'tahun' ? 'bg-[#051B44] text-white shadow-xs font-bold' : 'text-slate-600 hover:text-slate-900 font-semibold'"
                                class="flex-1 py-1.5 rounded-xl text-center text-xs transition-all cursor-pointer">
                            Tahunan
                        </button>
                    </div>

                    <!-- Header Navigasi Tahun (Untuk Mode Minggu & Bulan) -->
                    <div x-show="pickerMode !== 'tahun'" class="flex items-center justify-between px-2 pb-2.5 mb-2.5 border-b border-slate-100">
                        <button type="button" 
                                @click="prevYear()"
                                :disabled="pickerYear <= minYear"
                                :class="pickerYear <= minYear ? 'opacity-30 cursor-not-allowed' : 'hover:bg-slate-100 text-slate-700 cursor-pointer'"
                                class="w-7 h-7 rounded-lg flex items-center justify-center transition-colors">
                            <i class="fa-solid fa-chevron-left text-xs"></i>
                        </button>
                        <span class="font-extrabold text-sm text-[#051B44]" x-text="'Tahun ' + pickerYear"></span>
                        <button type="button" 
                                @click="nextYear()"
                                :disabled="pickerYear >= currentYear"
                                :class="pickerYear >= currentYear ? 'opacity-30 cursor-not-allowed' : 'hover:bg-slate-100 text-slate-700 cursor-pointer'"
                                class="w-7 h-7 rounded-lg flex items-center justify-center transition-colors">
                            <i class="fa-solid fa-chevron-right text-xs"></i>
                        </button>
                    </div>

                    <!-- TAB 1: MODE MINGGUAN -->
                    <div x-show="pickerMode === 'minggu'" class="space-y-3">
                        <!-- Pilih Bulan Horizontal Scroller -->
                        <div class="flex items-center gap-1 overflow-x-auto pb-1.5 scrollbar-thin">
                            <template x-for="m in monthList" :key="m.num">
                                <button type="button"
                                        @click="isMonthAvailable(m.num, pickerYear) && (pickerMonth = m.num)"
                                        :disabled="!isMonthAvailable(m.num, pickerYear)"
                                        :class="{
                                            'bg-[#051B44] text-white font-bold shadow-xs': pickerMonth === m.num && isMonthAvailable(m.num, pickerYear),
                                            'bg-slate-50 text-slate-700 hover:bg-sky-50 font-medium cursor-pointer': pickerMonth !== m.num && isMonthAvailable(m.num, pickerYear),
                                            'opacity-30 cursor-not-allowed bg-slate-50 text-slate-400': !isMonthAvailable(m.num, pickerYear)
                                        }"
                                        class="px-2.5 py-1 rounded-lg text-[11px] shrink-0 transition-all"
                                        x-text="m.short">
                                </button>
                            </template>
                        </div>

                        <!-- Daftar Minggu di Bulan Terpilih -->
                        <div class="space-y-1.5 pt-1 max-h-56 overflow-y-auto pr-1">
                            <div class="text-[10px] font-bold text-slate-400 uppercase tracking-wider px-1">
                                PILIH MINGGU (<span x-text="getMonthName(pickerMonth) + ' ' + pickerYear"></span>)
                            </div>
                            <template x-for="w in getWeeks(pickerMonth, pickerYear)" :key="w.index">
                                <button type="button"
                                        @click="w.available && applyWeek(w)"
                                        :disabled="!w.available"
                                        :class="{
                                            'border-sky-500 bg-sky-50/80 font-bold text-[#0055CC]': selectedPeriodKey === 'w_' + pickerYear + '_' + pickerMonth + '_' + w.index,
                                            'border-slate-100 hover:border-sky-200 hover:bg-slate-50 text-slate-700 cursor-pointer': selectedPeriodKey !== 'w_' + pickerYear + '_' + pickerMonth + '_' + w.index && w.available,
                                            'opacity-35 cursor-not-allowed border-dashed border-slate-200 bg-slate-50/50 text-slate-400': !w.available
                                        }"
                                        class="w-full p-2.5 rounded-xl border flex items-center justify-between text-left transition-all">
                                    <div class="flex items-center gap-2">
                                        <i class="fa-solid fa-calendar-week text-xs" :class="w.available ? 'text-sky-600' : 'text-slate-300'"></i>
                                        <span class="text-xs font-semibold" x-text="w.label"></span>
                                    </div>
                                    <div class="flex items-center gap-1.5">
                                        <span class="text-[10px] font-mono text-slate-400" x-text="w.range"></span>
                                        <span x-show="!w.available" class="text-[9px] px-1.5 py-0.5 rounded bg-slate-100 text-slate-400 font-semibold">Belum Terjadi</span>
                                    </div>
                                </button>
                            </template>
                        </div>
                    </div>

                    <!-- TAB 2: MODE BULANAN -->
                    <div x-show="pickerMode === 'bulan'" class="space-y-2">
                        <div class="text-[10px] font-bold text-slate-400 uppercase tracking-wider px-1 mb-1">
                            PILIH BULAN REKAP (<span x-text="pickerYear"></span>)
                        </div>
                        <div class="grid grid-cols-3 gap-2">
                            <template x-for="m in monthList" :key="m.num">
                                <button type="button"
                                        @click="isMonthAvailable(m.num, pickerYear) && applyMonth(m.num, pickerYear)"
                                        :disabled="!isMonthAvailable(m.num, pickerYear)"
                                        :class="{
                                            'border-sky-500 bg-sky-50/80 font-bold text-[#0055CC]': selectedPeriodKey === 'm_' + pickerYear + '_' + m.num,
                                            'border-slate-100 hover:border-sky-200 hover:bg-slate-50 text-slate-700 cursor-pointer': selectedPeriodKey !== 'm_' + pickerYear + '_' + m.num && isMonthAvailable(m.num, pickerYear),
                                            'opacity-35 cursor-not-allowed border-dashed border-slate-200 bg-slate-50/50 text-slate-400': !isMonthAvailable(m.num, pickerYear)
                                        }"
                                        class="p-2.5 rounded-xl border text-center transition-all flex flex-col items-center justify-center">
                                    <span class="text-xs font-bold" x-text="m.name"></span>
                                    <span x-show="!isMonthAvailable(m.num, pickerYear)" class="text-[8px] text-slate-400 mt-0.5">Belum Terjadi</span>
                                    <span x-show="isMonthAvailable(m.num, pickerYear) && m.num === currentMonth && pickerYear === currentYear" class="text-[8px] text-emerald-600 font-extrabold mt-0.5">Bln Berjalan</span>
                                </button>
                            </template>
                        </div>
                    </div>

                    <!-- TAB 3: MODE TAHUNAN -->
                    <div x-show="pickerMode === 'tahun'" class="space-y-2">
                        <div class="text-[10px] font-bold text-slate-400 uppercase tracking-wider px-1 mb-1">
                            PILIH TAHUN REKAP
                        </div>
                        <div class="space-y-2">
                            <template x-for="y in yearList" :key="y.year">
                                <button type="button"
                                        @click="y.available && applyYear(y.year)"
                                        :disabled="!y.available"
                                        :class="{
                                            'border-sky-500 bg-sky-50/80 font-bold text-[#0055CC]': selectedPeriodKey === 'y_' + y.year,
                                            'border-slate-100 hover:border-sky-200 hover:bg-slate-50 text-slate-700 cursor-pointer': selectedPeriodKey !== 'y_' + y.year && y.available,
                                            'opacity-35 cursor-not-allowed border-dashed border-slate-200 bg-slate-50/50 text-slate-400': !y.available
                                        }"
                                        class="w-full p-3 rounded-xl border flex items-center justify-between text-left transition-all">
                                    <div>
                                        <div class="text-xs font-extrabold" x-text="'Tahun ' + y.year"></div>
                                        <div class="text-[10px] text-slate-400" x-text="y.description"></div>
                                    </div>
                                    <span class="px-2 py-0.5 rounded text-[10px] font-extrabold"
                                          :class="y.available ? (y.year === currentYear ? 'bg-emerald-100 text-emerald-800' : 'bg-sky-100 text-sky-800') : 'bg-slate-100 text-slate-400'"
                                          x-text="y.badge">
                                    </span>
                                </button>
                            </template>
                        </div>
                    </div>

                    <!-- Bottom Quick Actions: Tampilkan Semua -->
                    <div class="pt-3 mt-3 border-t border-slate-100 flex items-center justify-between">
                        <button type="button" 
                                @click="applyAll()"
                                class="text-xs font-bold text-sky-600 hover:text-sky-800 transition-colors cursor-pointer">
                            Tampilkan Semua Catatan
                        </button>
                        <button type="button" 
                                @click="datePickerOpen = false"
                                class="px-3 py-1 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-600 font-bold text-xs transition-colors cursor-pointer">
                            Tutup
                        </button>
                    </div>

                </div>
            </div>
        </div>

        <!-- TAB 1: Tabel Riwayat Log Pakan Harian -->
        <div x-show="historyTab === 'log'" class="space-y-4">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-50/80 text-[10px] uppercase font-extrabold text-slate-400 border-b border-slate-100">
                        <tr>
                            <th class="py-3.5 px-4">TANGGAL</th>
                            <th class="py-3.5 px-4">KOLAM / FASE</th>
                            <th class="py-3.5 px-4">JENIS &amp; JUMLAH PAKAN</th>
                            <th class="py-3.5 px-4">SUPLEMEN DAUN</th>
                            <th class="py-3.5 px-4">TOTAL BIAYA</th>
                            <th class="py-3.5 px-4">PH AIR</th>
                            <th class="py-3.5 px-4 text-right">PETUGAS</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 font-medium text-slate-700">
                        <template x-if="paginatedLogs.length === 0">
                            <tr>
                                <td colspan="7" class="py-10 text-center text-slate-400 text-xs font-medium">
                                    <i class="fa-solid fa-calendar-xmark text-2xl text-slate-300 block mb-1.5"></i>
                                    Tidak ada catatan log pakan pada periode filter ini.
                                </td>
                            </tr>
                        </template>

                        <template x-for="log in paginatedLogs" :key="log.id_pakan">
                            <tr class="hover:bg-slate-50/60 transition-colors">
                                <td class="py-3.5 px-4">
                                    <span class="font-extrabold text-slate-900 block" x-text="log.tgl_log_formatted || log.tgl_log"></span>
                                    <span class="text-[10px] font-bold text-slate-400" x-text="log.waktu || 'Tercatat'"></span>
                                </td>
                                <td class="py-3.5 px-4">
                                    <div class="flex items-center gap-1.5">
                                        <span class="font-extrabold text-[#0B2570]" x-text="log.kolam ? log.kolam.nama_kolam : (log.nama_kolam || 'Kolam #' + log.id_kolam)"></span>
                                        <span class="text-[9px] font-black px-1.5 py-0.5 rounded"
                                              :class="log.kategori_fase === 'pembibitan' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-blue-50 text-blue-700 border border-blue-200'"
                                              x-text="log.kategori_fase === 'pembibitan' ? 'Bibit' : 'Besar'">
                                        </span>
                                    </div>
                                </td>
                                <td class="py-3.5 px-4 font-bold text-slate-800">
                                    <div class="flex items-center gap-1.5">
                                        <span class="px-2 py-0.5 rounded-md bg-slate-100 font-extrabold" x-text="Number(log.kg_pelet).toFixed(1) + ' kg'"></span>
                                        <template x-if="log.stok_pakan || log.nama_pakan">
                                            <span class="text-[10px] text-slate-500 font-bold" x-text="'(' + (log.stok_pakan ? log.stok_pakan.nama_pakan : log.nama_pakan) + ')'"></span>
                                        </template>
                                    </div>
                                </td>
                                <td class="py-3.5 px-4">
                                    <span x-text="Number(log.kg_daun) > 0 ? (Number(log.kg_daun).toFixed(1) + ' kg ' + (log.jenis_daun ? '(' + log.jenis_daun + ')' : '')) : '-'"></span>
                                </td>
                                <td class="py-3.5 px-4 font-extrabold text-emerald-700" x-text="'Rp ' + Number(log.total_biaya).toLocaleString('id-ID')"></td>
                                <td class="py-3.5 px-4">
                                    <span class="px-2 py-0.5 rounded-md text-[10px] font-extrabold bg-sky-50 text-sky-800 border border-sky-100" x-text="'pH ' + (log.ph_air || '7.2')"></span>
                                </td>
                                <td class="py-3.5 px-4 text-right text-slate-500 font-semibold" x-text="log.user ? (log.user.nama || log.user.name) : (log.petugas || 'Petugas')"></td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>

            <!-- Pagination Controls (Tab 1) -->
            <div x-show="filteredLogs.length > 0" class="pt-3 border-t border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-3 text-xs">
                <span class="text-slate-500 font-medium">
                    Menampilkan <strong class="text-slate-800" x-text="((currentPage - 1) * perPage) + 1"></strong> - <strong class="text-slate-800" x-text="Math.min(currentPage * perPage, filteredLogs.length)"></strong> dari <strong class="text-slate-800" x-text="filteredLogs.length"></strong> catatan
                </span>

                <div class="flex items-center gap-1" x-show="totalPages > 1">
                    <button type="button" @click="goToPage(currentPage - 1)" :disabled="currentPage === 1"
                            :class="currentPage === 1 ? 'text-slate-300 cursor-not-allowed' : 'text-slate-600 hover:bg-slate-100 cursor-pointer'"
                            class="w-8 h-8 rounded-lg flex items-center justify-center font-bold transition-all">
                        <i class="fa-solid fa-chevron-left text-xs"></i>
                    </button>
                    <template x-for="p in visiblePages" :key="p">
                        <button type="button" @click="goToPage(p)"
                                :class="currentPage === p ? 'bg-[#0284C7] text-white font-black' : 'text-slate-600 hover:bg-slate-100 font-semibold'"
                                class="w-8 h-8 rounded-lg flex items-center justify-center text-xs transition-all cursor-pointer"
                                x-text="p">
                        </button>
                    </template>
                    <button type="button" @click="goToPage(currentPage + 1)" :disabled="currentPage === totalPages"
                            :class="currentPage === totalPages ? 'text-slate-300 cursor-not-allowed' : 'text-slate-600 hover:bg-slate-100 cursor-pointer'"
                            class="w-8 h-8 rounded-lg flex items-center justify-center font-bold transition-all">
                        <i class="fa-solid fa-chevron-right text-xs"></i>
                    </button>
                </div>
            </div>
        </div>

        <!-- TAB 2: Tabel Riwayat Pembelian Pakan dari Supplier -->
        <div x-show="historyTab === 'pembelian'" class="space-y-4">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-50/80 text-[10px] uppercase font-extrabold text-slate-400 border-b border-slate-100">
                        <tr>
                            <th class="py-3.5 px-4">TANGGAL &amp; NO NOTA</th>
                            <th class="py-3.5 px-4">ITEM PAKAN</th>
                            <th class="py-3.5 px-4">SUPPLIER MITRA</th>
                            <th class="py-3.5 px-4">JUMLAH BELI</th>
                            <th class="py-3.5 px-4">HARGA SATUAN</th>
                            <th class="py-3.5 px-4">TOTAL BIAYA</th>
                            <th class="py-3.5 px-4 text-right">STATUS KEUANGAN</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 font-medium text-slate-700">
                        <template x-if="paginatedPembelian.length === 0">
                            <tr>
                                <td colspan="7" class="py-10 text-center text-slate-400 text-xs font-medium">
                                    <i class="fa-solid fa-cart-arrow-down text-2xl text-slate-300 block mb-1.5"></i>
                                    Tidak ada catatan pembelian supplier pada periode filter ini.
                                </td>
                            </tr>
                        </template>

                        <template x-for="pb in paginatedPembelian" :key="pb.id_pembelian">
                            <tr class="hover:bg-slate-50/60 transition-colors">
                                <td class="py-3.5 px-4">
                                    <span class="font-extrabold text-slate-900 block" x-text="pb.tgl_beli_formatted || pb.tgl_beli"></span>
                                    <span class="text-[10px] font-bold text-slate-400" x-text="pb.no_nota"></span>
                                </td>
                                <td class="py-3.5 px-4 font-bold text-[#0B2570]">
                                    <span x-text="pb.stok_pakan ? pb.stok_pakan.nama_pakan : (pb.nama_pakan || 'Pakan #' + pb.id_stok_pakan)"></span>
                                </td>
                                <td class="py-3.5 px-4">
                                    <div class="flex items-center gap-2">
                                        <div class="w-6 h-6 rounded-md bg-emerald-50 text-emerald-600 flex items-center justify-center font-bold text-[10px]">
                                            <i class="fa-solid fa-truck-field"></i>
                                        </div>
                                        <span class="font-bold text-slate-800" x-text="pb.mitra ? pb.mitra.nama_mitra : (pb.nama_mitra || 'Supplier Mitra')"></span>
                                    </div>
                                </td>
                                <td class="py-3.5 px-4 font-extrabold text-slate-900">
                                    <span class="px-2.5 py-1 rounded-lg bg-emerald-50 text-emerald-800 border border-emerald-200"
                                          x-text="'+' + Number(pb.jumlah).toLocaleString('id-ID') + ' ' + (pb.satuan || 'kg')">
                                    </span>
                                </td>
                                <td class="py-3.5 px-4 text-slate-600" x-text="'Rp ' + Number(pb.harga_satuan).toLocaleString('id-ID')"></td>
                                <td class="py-3.5 px-4 font-black text-rose-600" x-text="'Rp ' + Number(pb.total_biaya).toLocaleString('id-ID')"></td>
                                <td class="py-3.5 px-4 text-right">
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-extrabold bg-emerald-100 text-emerald-800 border border-emerald-200">
                                        <i class="fa-solid fa-check text-[9px]"></i>
                                        <span>Tercatat Kas Keluar</span>
                                    </span>
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>

            <!-- Pagination Controls (Tab 2) -->
            <div x-show="filteredPembelian.length > 0" class="pt-3 border-t border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-3 text-xs">
                <span class="text-slate-500 font-medium">
                    Menampilkan <strong class="text-slate-800" x-text="((currentPage - 1) * perPage) + 1"></strong> - <strong class="text-slate-800" x-text="Math.min(currentPage * perPage, filteredPembelian.length)"></strong> dari <strong class="text-slate-800" x-text="filteredPembelian.length"></strong> catatan
                </span>

                <div class="flex items-center gap-1" x-show="totalPages > 1">
                    <button type="button" @click="goToPage(currentPage - 1)" :disabled="currentPage === 1"
                            :class="currentPage === 1 ? 'text-slate-300 cursor-not-allowed' : 'text-slate-600 hover:bg-slate-100 cursor-pointer'"
                            class="w-8 h-8 rounded-lg flex items-center justify-center font-bold transition-all">
                        <i class="fa-solid fa-chevron-left text-xs"></i>
                    </button>
                    <template x-for="p in visiblePages" :key="p">
                        <button type="button" @click="goToPage(p)"
                                :class="currentPage === p ? 'bg-[#0284C7] text-white font-black' : 'text-slate-600 hover:bg-slate-100 font-semibold'"
                                class="w-8 h-8 rounded-lg flex items-center justify-center text-xs transition-all cursor-pointer"
                                x-text="p">
                        </button>
                    </template>
                    <button type="button" @click="goToPage(currentPage + 1)" :disabled="currentPage === totalPages"
                            :class="currentPage === totalPages ? 'text-slate-300 cursor-not-allowed' : 'text-slate-600 hover:bg-slate-100 cursor-pointer'"
                            class="w-8 h-8 rounded-lg flex items-center justify-center font-bold transition-all">
                        <i class="fa-solid fa-chevron-right text-xs"></i>
                    </button>
                </div>
            </div>
        </div>

    </div>

    <!-- MODAL 1: Catat Pembelian Pakan dari Mitra Supplier -->
    <div x-show="showBeliModal" 
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-xs"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         style="display: none;">
        
        <div class="bg-white rounded-3xl max-w-lg w-full p-6 sm:p-7 shadow-2xl border border-slate-200 space-y-5"
             @click.outside="showBeliModal = false">
            
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-2xl bg-sky-50 text-sky-600 flex items-center justify-center font-bold text-base border border-sky-100">
                        <i class="fa-solid fa-cart-shopping"></i>
                    </div>
                    <div>
                        <h3 class="font-extrabold text-slate-900 text-base">Catat Pembelian Pakan Baru</h3>
                        <p class="text-xs text-slate-500">Stok bertambah & otomatis tercatat ke Keuangan.</p>
                    </div>
                </div>
                <button type="button" @click="showBeliModal = false" class="text-slate-400 hover:text-slate-700 cursor-pointer">
                    <i class="fa-solid fa-xmark text-lg"></i>
                </button>
            </div>

            <form @submit.prevent="handleSavePembelian()" class="space-y-4">
                
                <!-- Pilih Pakan -->
                <div>
                    <label class="text-[10px] font-extrabold uppercase tracking-wider text-slate-500 block mb-1">
                        PILIH ITEM PAKAN <span class="text-rose-500">*</span>
                    </label>
                    <select x-model="beliForm.id_stok_pakan" @change="onBeliPakanChange()" required
                            class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs font-semibold text-slate-800 bg-white focus:outline-none focus:ring-2 focus:ring-sky-500 transition-all cursor-pointer">
                        <option value="">Pilih Item Pakan...</option>
                        <template x-for="sp in stokList" :key="sp.id_stok_pakan">
                            <option :value="sp.id_stok_pakan" x-text="sp.nama_pakan + ' (Saat ini: ' + sp.stok_tersisa + ' ' + sp.satuan + ')'"></option>
                        </template>
                    </select>
                </div>

                <!-- Pilih Supplier Mitra -->
                <div>
                    <label class="text-[10px] font-extrabold uppercase tracking-wider text-slate-500 block mb-1">
                        SUPPLIER MITRA
                    </label>
                    <select x-model="beliForm.id_mitra"
                            class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs font-semibold text-slate-800 bg-white focus:outline-none focus:ring-2 focus:ring-sky-500 transition-all cursor-pointer">
                        <option value="">Supplier Eksternal / Mitra Bebas</option>
                        <template x-for="sup in suppliers" :key="sup.id_mitra">
                            <option :value="sup.id_mitra" x-text="sup.nama_mitra + ' (' + sup.tipe_mitra + ')'"></option>
                        </template>
                    </select>
                </div>

                <!-- Row: Jumlah & Harga -->
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="text-[10px] font-extrabold uppercase tracking-wider text-slate-500 block mb-1">
                            JUMLAH DIBELI <span class="text-rose-500">*</span> <span class="text-[9px] text-slate-400 font-normal lowercase">(maks. 1.000)</span>
                        </label>
                        <div class="flex items-center rounded-xl border border-slate-200 overflow-hidden focus-within:ring-2 focus-within:ring-sky-500">
                            <input type="number" step="0.1" min="0.1" max="1000" x-model="beliForm.jumlah" 
                                   @keydown="if(['-', 'e', '+'].includes($event.key)) $event.preventDefault()"
                                   @input="if(Number(beliForm.jumlah) > 1000) beliForm.jumlah = 1000; calcBeliTotal()" 
                                   placeholder="Contoh: 50" required
                                   class="w-full px-3 py-2 text-xs font-extrabold text-slate-900 border-0 focus:outline-none">
                            <span class="px-2.5 py-2 bg-slate-100 text-slate-500 text-xs font-bold" x-text="selectedBeliPakan ? selectedBeliPakan.satuan : 'kg'"></span>
                        </div>
                    </div>

                    <div>
                        <label class="text-[10px] font-extrabold uppercase tracking-wider text-slate-500 block mb-1">
                            HARGA PER SATUAN (RP) <span class="text-rose-500">*</span>
                        </label>
                        <div class="flex items-center rounded-xl border border-slate-200 overflow-hidden focus-within:ring-2 focus-within:ring-sky-500">
                            <span class="px-2.5 py-2 bg-slate-100 text-slate-500 text-xs font-bold">Rp</span>
                            <input type="number" min="0" x-model="beliForm.harga_satuan" 
                                   @keydown="if(['-', 'e', '+'].includes($event.key)) $event.preventDefault()"
                                   @input="calcBeliTotal()" 
                                   placeholder="Contoh: 12500" required
                                   class="w-full px-3 py-2 text-xs font-extrabold text-slate-900 border-0 focus:outline-none">
                        </div>
                    </div>
                </div>

                <!-- Total Biaya & Tanggal -->
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="text-[10px] font-extrabold uppercase tracking-wider text-slate-500 block mb-1">
                            TOTAL PENGELUARAN (RP)
                        </label>
                        <div class="px-3.5 py-2 rounded-xl bg-slate-100 border border-slate-200 text-xs font-black text-rose-600">
                            Rp <span x-text="Number(beliForm.total_biaya || 0).toLocaleString('id-ID')"></span>
                        </div>
                    </div>

                    <div>
                        <label class="text-[10px] font-extrabold uppercase tracking-wider text-slate-500 block mb-1">
                            TANGGAL PEMBELIAN
                        </label>
                        <input type="date" x-model="beliForm.tgl_beli" required
                               class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs font-semibold text-slate-800 bg-white focus:outline-none focus:ring-2 focus:ring-sky-500">
                    </div>
                </div>

                <!-- Info Box Auto Buku Kas -->
                <div class="p-3.5 bg-emerald-50 rounded-2xl border border-emerald-200 text-[11px] text-emerald-900 font-semibold flex items-start gap-2.5">
                    <i class="fa-solid fa-circle-check text-emerald-600 text-sm mt-0.5"></i>
                    <p class="leading-relaxed">
                        Data ini akan otomatis menambah saldo stok di gudang dan mencatat transaksi pengeluaran operasional di modul <strong>Keuangan</strong>.
                    </p>
                </div>

                <div class="flex items-center justify-end gap-2.5 pt-3 border-t border-slate-100">
                    <button type="button" @click="showBeliModal = false" class="px-4 py-2 rounded-xl border border-slate-200 text-xs font-bold text-slate-600 hover:bg-slate-50 cursor-pointer">
                        Batal
                    </button>
                    <button type="submit" :disabled="isSubmittingBeli"
                            class="px-5 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-extrabold text-xs shadow-md shadow-emerald-600/20 transition-all flex items-center gap-2 cursor-pointer">
                        <i class="fa-solid fa-check"></i>
                        <span x-text="isSubmittingBeli ? 'Menyimpan...' : 'Simpan Pembelian'"></span>
                    </button>
                </div>

            </form>
        </div>
    </div>

    <!-- MODAL 2: Hubungi Supplier Mitra via WhatsApp -->
    <div x-show="showSupplierModal" 
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-xs"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         style="display: none;">
        
        <div class="bg-white rounded-3xl max-w-xl w-full p-6 sm:p-7 shadow-2xl border border-slate-200 space-y-5"
             @click.outside="showSupplierModal = false">
            
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center font-bold text-base border border-emerald-100">
                        <i class="fa-brands fa-whatsapp text-lg"></i>
                    </div>
                    <div>
                        <h3 class="font-extrabold text-slate-900 text-base">Kontak WhatsApp Supplier Pakan</h3>
                        <p class="text-xs text-slate-500">Pesan pasokan pakan langsung via WhatsApp di luar sistem.</p>
                    </div>
                </div>
                <button type="button" @click="showSupplierModal = false" class="text-slate-400 hover:text-slate-700 cursor-pointer">
                    <i class="fa-solid fa-xmark text-lg"></i>
                </button>
            </div>

            <!-- Daftar Supplier List -->
            <div class="space-y-3 max-h-96 overflow-y-auto pr-1">
                <template x-for="sup in suppliers" :key="sup.id_mitra">
                    <div class="p-4 rounded-2xl border border-slate-200 bg-slate-50/50 hover:bg-white hover:border-emerald-300 transition-all flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                        <div class="space-y-1">
                            <div class="flex items-center gap-2">
                                <h4 class="font-extrabold text-slate-900 text-xs sm:text-sm" x-text="sup.nama_mitra"></h4>
                                <span class="text-[9px] font-black uppercase px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-800" x-text="sup.tipe_mitra"></span>
                            </div>
                            <p class="text-[11px] text-slate-500 flex items-center gap-1.5">
                                <i class="fa-solid fa-location-dot text-slate-400 text-[10px]"></i>
                                <span x-text="sup.alamat"></span>
                            </p>
                            <p class="text-[11px] font-bold text-slate-700 flex items-center gap-1.5">
                                <i class="fa-solid fa-phone text-slate-400 text-[10px]"></i>
                                <span x-text="sup.telepon"></span>
                            </p>
                        </div>

                        <a :href="sup.wa_link" target="_blank"
                           class="px-3.5 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-extrabold text-xs shadow-sm flex items-center justify-center gap-2 transition-all shrink-0">
                            <i class="fa-brands fa-whatsapp text-sm"></i>
                            <span>Chat Order WA</span>
                        </a>
                    </div>
                </template>

                <template x-if="suppliers.length === 0">
                    <div class="py-8 text-center text-slate-400 text-xs">
                        <i class="fa-solid fa-users-slash text-2xl text-slate-300 block mb-1"></i>
                        Belum ada data supplier mitra yang terdaftar.
                    </div>
                </template>
            </div>

            <div class="pt-3 border-t border-slate-100 flex items-center justify-between text-xs">
                <a href="{{ route('mitra') }}" class="text-sky-600 font-bold hover:underline flex items-center gap-1">
                    <i class="fa-solid fa-arrow-up-right-from-square text-[10px]"></i>
                    <span>Kelola Mitra di Menu Manajemen Mitra</span>
                </a>
                <button type="button" @click="showSupplierModal = false" class="px-4 py-1.5 rounded-xl bg-slate-100 text-slate-600 font-bold hover:bg-slate-200 cursor-pointer">
                    Tutup
                </button>
            </div>

        </div>
    </div>

    <!-- MODAL 3: Kelola Master Item Pakan -->
    <div x-show="showMasterModal" 
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-xs"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         style="display: none;">
        
        <div class="bg-white rounded-3xl max-w-lg w-full p-6 sm:p-7 shadow-2xl border border-slate-200 space-y-5"
             @click.outside="showMasterModal = false">
            
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center font-bold text-base border border-blue-100">
                        <i class="fa-solid fa-boxes-stacked"></i>
                    </div>
                    <div>
                        <h3 class="font-extrabold text-slate-900 text-base">Tambah Master Item Pakan</h3>
                        <p class="text-xs text-slate-500">Daftarkan jenis pakan baru ke dalam sistem inventori.</p>
                    </div>
                </div>
                <button type="button" @click="showMasterModal = false" class="text-slate-400 hover:text-slate-700 cursor-pointer">
                    <i class="fa-solid fa-xmark text-lg"></i>
                </button>
            </div>

            <form @submit.prevent="handleSaveMasterPakan()" class="space-y-4">
                
                <div>
                    <label class="text-[10px] font-extrabold uppercase tracking-wider text-slate-500 block mb-1">
                        NAMA PAKAN <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" x-model="masterForm.nama_pakan" placeholder="Contoh: Pelet Starter PF-800" required
                           class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs font-semibold text-slate-800 bg-white focus:outline-none focus:ring-2 focus:ring-sky-500 transition-all">
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="text-[10px] font-extrabold uppercase tracking-wider text-slate-500 block mb-1">
                            PERUNTUKAN FASE <span class="text-rose-500">*</span>
                        </label>
                        <select x-model="masterForm.kategori_peruntukan" required
                                class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs font-semibold text-slate-800 bg-white focus:outline-none focus:ring-2 focus:ring-sky-500 transition-all cursor-pointer">
                            <option value="pembesaran">🐟 Pembesaran</option>
                            <option value="pembibitan">🌱 Pembibitan</option>
                            <option value="semua">📦 Semua Fase</option>
                        </select>
                    </div>

                    <div>
                        <label class="text-[10px] font-extrabold uppercase tracking-wider text-slate-500 block mb-1">
                            SATUAN <span class="text-rose-500">*</span>
                        </label>
                        <input type="text" x-model="masterForm.satuan" placeholder="kg / sak / tray" required
                               class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs font-semibold text-slate-800 bg-white focus:outline-none focus:ring-2 focus:ring-sky-500 transition-all">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="text-[10px] font-extrabold uppercase tracking-wider text-slate-500 block mb-1">
                            STOK AWAL
                        </label>
                        <input type="number" step="0.1" min="0" x-model="masterForm.stok_tersisa" placeholder="0"
                               class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs font-semibold text-slate-800 bg-white focus:outline-none focus:ring-2 focus:ring-sky-500 transition-all">
                    </div>

                    <div>
                        <label class="text-[10px] font-extrabold uppercase tracking-wider text-slate-500 block mb-1">
                            BATAS MINIMUM (ALERT)
                        </label>
                        <input type="number" step="0.1" min="0" x-model="masterForm.batas_minimum" placeholder="10"
                               class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs font-semibold text-slate-800 bg-white focus:outline-none focus:ring-2 focus:ring-sky-500 transition-all">
                    </div>
                </div>

                <div>
                    <label class="text-[10px] font-extrabold uppercase tracking-wider text-slate-500 block mb-1">
                        HARGA ACUAN PER SATUAN (RP)
                    </label>
                    <input type="number" min="0" x-model="masterForm.harga_per_satuan" placeholder="12500"
                           class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs font-semibold text-slate-800 bg-white focus:outline-none focus:ring-2 focus:ring-sky-500 transition-all">
                </div>

                <div class="flex items-center justify-end gap-2.5 pt-3 border-t border-slate-100">
                    <button type="button" @click="showMasterModal = false" class="px-4 py-2 rounded-xl border border-slate-200 text-xs font-bold text-slate-600 hover:bg-slate-50 cursor-pointer">
                        Batal
                    </button>
                    <button type="submit" :disabled="isSubmittingMaster"
                            class="px-5 py-2 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-extrabold text-xs shadow-md shadow-blue-600/20 transition-all flex items-center gap-2 cursor-pointer">
                        <i class="fa-solid fa-plus"></i>
                        <span x-text="isSubmittingMaster ? 'Menyimpan...' : 'Tambah Item'"></span>
                    </button>
                </div>

            </form>
        </div>
    </div>

    <!-- Toast Notification -->
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
        <button @click="showToast = false" class="text-white/70 hover:text-white transition-colors cursor-pointer">
            <i class="fa-solid fa-xmark text-xs"></i>
        </button>
    </div>

</div>
@endsection

@push('scripts')
<script>
function pakanHubComponent() {
    const todayObj = new Date();
    const curY = todayObj.getFullYear();
    const curM = todayObj.getMonth() + 1;
    const curD = todayObj.getDate();

    return {
        stokList: {!! json_encode($enrichedStokPakan ?? []) !!},
        summary: {!! json_encode($stokSummary ?? ['total_stok_kg' => 0, 'stok_pembibitan_kg' => 0, 'stok_pembesaran_kg' => 0, 'item_kritis_count' => 0, 'item_waspada_count' => 0, 'item_aman_count' => 0]) !!},
        suppliers: {!! json_encode($suppliers ?? []) !!},
        activeKolams: {!! json_encode($activeKolams ?? []) !!},
        hatcheryKolams: {!! json_encode($hatcheryKolams ?? []) !!},
        logs: {!! json_encode($logs ?? []) !!},
        pembelianList: {!! json_encode($riwayatPembelian ?? []) !!},

        stokFilter: 'semua',
        historyTab: 'log', // 'log' or 'pembelian'

        // Date Picker State
        datePickerOpen: false,
        pickerMode: 'minggu', // 'minggu' | 'bulan' | 'tahun'
        currentYear: curY,
        currentMonth: curM,
        currentDate: curD,
        minYear: curY - 2,
        pickerYear: curY,
        pickerMonth: curM,
        selectedPeriodKey: 'all',
        periodLabel: 'Semua Periode',
        filterStartDate: '',
        filterEndDate: '',

        monthList: [
            { num: 1, name: 'Januari', short: 'Jan' },
            { num: 2, name: 'Februari', short: 'Feb' },
            { num: 3, name: 'Maret', short: 'Mar' },
            { num: 4, name: 'April', short: 'Apr' },
            { num: 5, name: 'Mei', short: 'Mei' },
            { num: 6, name: 'Juni', short: 'Jun' },
            { num: 7, name: 'Juli', short: 'Jul' },
            { num: 8, name: 'Agustus', short: 'Agu' },
            { num: 9, name: 'September', short: 'Sep' },
            { num: 10, name: 'Oktober', short: 'Okt' },
            { num: 11, name: 'November', short: 'Nov' },
            { num: 12, name: 'Desember', short: 'Des' },
        ],

        get yearList() {
            const cy = this.currentYear;
            return [
                { year: cy - 2, available: true, badge: `Arsip ${cy - 2}`, description: `Rekap Tahunan Periode ${cy - 2}` },
                { year: cy - 1, available: true, badge: `Arsip ${cy - 1}`, description: `Rekap Tahunan Periode ${cy - 1}` },
                { year: cy, available: true, badge: 'Tahun Berjalan', description: `Rekap Tahunan Periode ${cy}` },
                { year: cy + 1, available: false, badge: 'Belum Ada Data', description: 'Tahun Mendatang (Belum Terjadi)' },
            ];
        },

        // Modal States
        showBeliModal: false,
        showSupplierModal: false,
        showMasterModal: false,

        // Submitting flags
        isSubmittingLog: false,
        isSubmittingBeli: false,
        isSubmittingMaster: false,

        // Toast
        showToast: false,
        toastMessage: '',

        // Pagination
        currentPage: 1,
        perPage: 8,

        // Form Log Pakan
        form: {
            kategori_fase: 'pembesaran',
            id_kolam: '',
            id_stok_pakan: '',
            id_stok_suplemen: '',
            tgl_log: new Date().toISOString().split('T')[0],
            kg_pelet: 10,
            kg_daun: 0,
            jenis_daun: '',
            total_biaya: 125000,
            ph_air: 7.2
        },

        // Form Pembelian Pakan
        beliForm: {
            id_stok_pakan: '',
            id_mitra: '',
            tgl_beli: new Date().toISOString().split('T')[0],
            jumlah: 50,
            harga_satuan: 12500,
            total_biaya: 625000
        },

        // Form Master Item Pakan
        masterForm: {
            nama_pakan: '',
            kategori_peruntukan: 'pembesaran',
            satuan: 'kg',
            stok_tersisa: 50,
            batas_minimum: 15,
            harga_per_satuan: 12500
        },

        init() {
            // Auto select default pakan in log form
            if (this.relevantStokList.length > 0) {
                this.form.id_stok_pakan = this.relevantStokList[0].id_stok_pakan;
                this.recalculateCost();
            }
        },

        prevYear() {
            if (this.pickerYear > this.minYear) {
                this.pickerYear--;
                if (this.pickerYear < this.currentYear) {
                    this.pickerMonth = 12;
                }
            }
        },

        nextYear() {
            if (this.pickerYear < this.currentYear) {
                this.pickerYear++;
                if (this.pickerYear === this.currentYear && this.pickerMonth > this.currentMonth) {
                    this.pickerMonth = this.currentMonth;
                }
            }
        },

        getMonthName(mNum) {
            const m = this.monthList.find(x => x.num === mNum);
            return m ? m.name : '';
        },

        isMonthAvailable(mNum, year) {
            if (year < this.minYear) return false;
            if (year < this.currentYear) return true;
            if (year === this.currentYear) return mNum <= this.currentMonth;
            return false;
        },

        getWeeks(mNum, year) {
            const mName = this.getMonthName(mNum);
            const lastDay = new Date(year, mNum, 0).getDate();
            const isPastMonth = (year < this.currentYear) || (year === this.currentYear && mNum < this.currentMonth);
            const isCurrentMonth = (year === this.currentYear && mNum === this.currentMonth);

            const rawWeeks = [
                { index: 1, startDay: 1, endDay: 7 },
                { index: 2, startDay: 8, endDay: 14 },
                { index: 3, startDay: 15, endDay: 21 },
                { index: 4, startDay: 22, endDay: 28 },
                { index: 5, startDay: 29, endDay: lastDay }
            ];

            return rawWeeks.filter(w => w.startDay <= lastDay).map(w => {
                const actualEnd = Math.min(w.endDay, lastDay);
                const sPad = String(w.startDay).padStart(2, '0');
                const ePad = String(actualEnd).padStart(2, '0');
                const mPad = String(mNum).padStart(2, '0');
                
                const startDateStr = `${year}-${mPad}-${sPad}`;
                const endDateStr = `${year}-${mPad}-${ePad}`;
                
                let available = false;
                if (isPastMonth) {
                    available = true;
                } else if (isCurrentMonth) {
                    available = (w.startDay <= this.currentDate);
                }

                return {
                    index: w.index,
                    label: `Minggu ${w.index}`,
                    range: `${sPad} - ${ePad} ${mName.substring(0,3)}`,
                    startDate: startDateStr,
                    endDate: endDateStr,
                    available: available
                };
            });
        },

        applyWeek(weekObj) {
            const mName = this.getMonthName(this.pickerMonth);
            this.selectedPeriodKey = `w_${this.pickerYear}_${this.pickerMonth}_${weekObj.index}`;
            this.periodLabel = `${weekObj.label}, ${mName.substring(0,3)} ${this.pickerYear}`;
            this.filterStartDate = weekObj.startDate;
            this.filterEndDate = weekObj.endDate;
            this.currentPage = 1;
            this.datePickerOpen = false;
        },

        applyMonth(mNum, year) {
            const mName = this.getMonthName(mNum);
            const lastDay = new Date(year, mNum, 0).getDate();
            const mPad = String(mNum).padStart(2, '0');
            
            this.selectedPeriodKey = `m_${year}_${mNum}`;
            this.periodLabel = `${mName} ${year}`;
            this.filterStartDate = `${year}-${mPad}-01`;
            this.filterEndDate = `${year}-${mPad}-${String(lastDay).padStart(2, '0')}`;
            this.currentPage = 1;
            this.datePickerOpen = false;
        },

        applyYear(year) {
            this.selectedPeriodKey = `y_${year}`;
            this.periodLabel = `Tahun ${year}`;
            this.filterStartDate = `${year}-01-01`;
            this.filterEndDate = `${year}-12-31`;
            this.currentPage = 1;
            this.datePickerOpen = false;
        },

        applyAll() {
            this.selectedPeriodKey = 'all';
            this.periodLabel = 'Semua Periode';
            this.filterStartDate = '';
            this.filterEndDate = '';
            this.currentPage = 1;
            this.datePickerOpen = false;
        },

        get filteredStokList() {
            if (this.stokFilter === 'semua') return this.stokList;
            return this.stokList.filter(item => item.kategori_peruntukan === this.stokFilter || item.kategori_peruntukan === 'semua');
        },

        get relevantStokList() {
            return this.stokList.filter(item => item.kategori_peruntukan === this.form.kategori_fase || item.kategori_peruntukan === 'semua');
        },

        get suplemenList() {
            return this.stokList.filter(item => {
                const name = (item.nama_pakan || '').toLowerCase();
                return name.includes('daun') || name.includes('singkong') || name.includes('talas') || name.includes('pepaya') || name.includes('azolla') || name.includes('lemna') || name.includes('maggot') || name.includes('kangkung') || name.includes('suplemen') || name.includes('organik') || (item.kategori_peruntukan === 'pembesaran' && /daun|suplemen|organik|maggot|azolla/i.test(item.nama_pakan));
            });
        },

        get selectedSuplemenItem() {
            if (!this.form.id_stok_suplemen) return null;
            return this.stokList.find(s => s.id_stok_pakan == this.form.id_stok_suplemen) || null;
        },

        get isTelurPhase() {
            return this.form.kategori_fase === 'pembibitan' && this.selectedKolamInfo && this.selectedKolamInfo.fase_key === 'telur';
        },

        get selectedKolamInfo() {
            if (!this.form.id_kolam) return null;
            if (this.form.kategori_fase === 'pembesaran') {
                return this.activeKolams.find(k => k.id_kolam == this.form.id_kolam) || null;
            } else {
                return this.hatcheryKolams.find(hk => hk.id_kolam == this.form.id_kolam) || null;
            }
        },

        get selectedPakanItem() {
            if (!this.form.id_stok_pakan) return null;
            return this.stokList.find(s => s.id_stok_pakan == this.form.id_stok_pakan) || null;
        },

        get selectedBeliPakan() {
            if (!this.beliForm.id_stok_pakan) return null;
            return this.stokList.find(s => s.id_stok_pakan == this.beliForm.id_stok_pakan) || null;
        },

        get filteredLogs() {
            let list = this.logs;
            if (this.filterStartDate && this.filterEndDate) {
                list = list.filter(item => {
                    const d = item.tgl_log_raw || item.tgl_log;
                    return d >= this.filterStartDate && d <= this.filterEndDate;
                });
            }
            return list;
        },

        get filteredPembelian() {
            let list = this.pembelianList;
            if (this.filterStartDate && this.filterEndDate) {
                list = list.filter(item => {
                    const d = item.tgl_beli_raw || item.tgl_beli;
                    return d >= this.filterStartDate && d <= this.filterEndDate;
                });
            }
            return list;
        },

        get currentActiveList() {
            return this.historyTab === 'log' ? this.filteredLogs : this.filteredPembelian;
        },

        get totalPages() {
            return Math.ceil(this.currentActiveList.length / this.perPage) || 1;
        },

        get paginatedLogs() {
            const start = (this.currentPage - 1) * this.perPage;
            return this.filteredLogs.slice(start, start + this.perPage);
        },

        get paginatedPembelian() {
            const start = (this.currentPage - 1) * this.perPage;
            return this.filteredPembelian.slice(start, start + this.perPage);
        },

        get visiblePages() {
            const pages = [];
            for (let i = 1; i <= this.totalPages; i++) pages.push(i);
            return pages;
        },

        selectFase(fase) {
            this.form.kategori_fase = fase;
            this.form.id_kolam = '';
            
            // Switch default pakan
            const rel = this.relevantStokList;
            if (rel.length > 0) {
                this.form.id_stok_pakan = rel[0].id_stok_pakan;
            } else {
                this.form.id_stok_pakan = '';
            }

            if (fase === 'pembibitan') {
                this.form.kg_pelet = 1.5;
                this.form.kg_daun = 0;
                this.form.id_stok_suplemen = '';
                this.form.jenis_daun = '';
            } else {
                this.form.kg_pelet = 10;
                this.form.kg_daun = 0;
                this.form.id_stok_suplemen = '';
                this.form.jenis_daun = '';
            }
            this.recalculateCost();
        },

        onKolamChange() {
            if (this.selectedKolamInfo && this.form.kategori_fase === 'pembesaran') {
                const estPelet = this.selectedKolamInfo.est_pelet_kg || Math.max(1, Math.round(this.selectedKolamInfo.biomassa_est * 0.025 * 10) / 10);
                this.form.kg_pelet = estPelet;

                // Auto sarankan pakan di stok gudang yang paling sesuai
                const rel = this.relevantStokList;
                if (rel.length > 0) {
                    const fKey = (this.selectedKolamInfo.fase_key || '').toLowerCase();
                    let matchedPakan = null;
                    if (fKey === 'starter') {
                        matchedPakan = rel.find(p => /starter|pf-1000|781-1|benih/i.test(p.nama_pakan));
                    } else if (fKey === 'grower') {
                        matchedPakan = rel.find(p => /grower|781-2|apung/i.test(p.nama_pakan));
                    } else if (fKey === 'finisher') {
                        matchedPakan = rel.find(p => /finisher|781-3|panen|hi-pro/i.test(p.nama_pakan));
                    }
                    if (matchedPakan) {
                        this.form.id_stok_pakan = matchedPakan.id_stok_pakan;
                    }
                }

                this.recalculateCost();
            }
        },

        onHatcheryKolamChange() {
            if (this.selectedKolamInfo && this.form.kategori_fase === 'pembibitan') {
                const fKey = (this.selectedKolamInfo.fase_key || '').toLowerCase();

                if (fKey === 'telur') {
                    this.form.kg_pelet = 0;
                    this.form.id_stok_pakan = '';
                    this.form.id_stok_suplemen = '';
                    this.form.total_biaya = 0;
                    return;
                }

                this.form.kg_pelet = this.selectedKolamInfo.est_pakan_kg || 1.0;

                // Auto sarankan pakan benih di stok gudang yang paling sesuai
                const rel = this.relevantStokList;
                if (rel.length > 0) {
                    let matchedPakan = null;
                    if (fKey === 'larva') {
                        matchedPakan = rel.find(p => /cacing|sutra|artemia|nauplii/i.test(p.nama_pakan));
                    } else if (fKey === 'fingerling') {
                        matchedPakan = rel.find(p => /pf-500|pf-800|starter|benih/i.test(p.nama_pakan));
                    }
                    if (matchedPakan) {
                        this.form.id_stok_pakan = matchedPakan.id_stok_pakan;
                    }
                }

                this.recalculateCost();
            }
        },

        onStokPakanChange() {
            this.recalculateCost();
        },

        onSuplemenChange() {
            if (this.selectedSuplemenItem) {
                this.form.jenis_daun = this.selectedSuplemenItem.nama_pakan;
            } else {
                this.form.jenis_daun = '';
            }
            this.recalculateCost();
        },

        recalculateCost() {
            const pelet = Number(this.form.kg_pelet) || 0;
            const daun = (this.form.kategori_fase === 'pembibitan') ? 0 : (Number(this.form.kg_daun) || 0);

            const itemPelet = this.selectedPakanItem;
            const pricePelet = itemPelet ? (Number(itemPelet.harga_per_satuan) || 12500) : 12500;

            const itemDaun = this.selectedSuplemenItem;
            const priceDaun = itemDaun ? (Number(itemDaun.harga_per_satuan) || 0) : 0;

            // Total Biaya = (kg_pelet * harga_pelet) + (kg_daun * harga_suplemen)
            this.form.total_biaya = Math.round((pelet * pricePelet) + (daun * priceDaun));
        },

        goToPage(page) {
            if (page >= 1 && page <= this.totalPages) {
                this.currentPage = page;
            }
        },

        resetLogForm() {
            this.form = {
                kategori_fase: 'pembesaran',
                id_kolam: '',
                id_stok_pakan: this.relevantStokList.length > 0 ? this.relevantStokList[0].id_stok_pakan : '',
                id_stok_suplemen: '',
                tgl_log: new Date().toISOString().split('T')[0],
                kg_pelet: 10,
                kg_daun: 0,
                jenis_daun: '',
                total_biaya: 125000,
                ph_air: 7.2
            };
        },

        openBeliModal() {
            this.beliForm = {
                id_stok_pakan: this.stokList.length > 0 ? this.stokList[0].id_stok_pakan : '',
                id_mitra: this.suppliers.length > 0 ? this.suppliers[0].id_mitra : '',
                tgl_beli: new Date().toISOString().split('T')[0],
                jumlah: 50,
                harga_satuan: this.stokList.length > 0 ? this.stokList[0].harga_per_satuan : 12500,
                total_biaya: (this.stokList.length > 0 ? this.stokList[0].harga_per_satuan : 12500) * 50
            };
            this.showBeliModal = true;
        },

        quickBeli(item) {
            this.beliForm = {
                id_stok_pakan: item.id_stok_pakan,
                id_mitra: this.suppliers.length > 0 ? this.suppliers[0].id_mitra : '',
                tgl_beli: new Date().toISOString().split('T')[0],
                jumlah: item.batas_minimum ? item.batas_minimum * 2 : 50,
                harga_satuan: item.harga_per_satuan || 12500,
                total_biaya: (item.harga_per_satuan || 12500) * (item.batas_minimum ? item.batas_minimum * 2 : 50)
            };
            this.showBeliModal = true;
        },

        onBeliPakanChange() {
            if (this.selectedBeliPakan) {
                this.beliForm.harga_satuan = this.selectedBeliPakan.harga_per_satuan || 12500;
                this.calcBeliTotal();
            }
        },

        calcBeliTotal() {
            if (this.beliForm.jumlah !== '' && Number(this.beliForm.jumlah) < 0) {
                this.beliForm.jumlah = Math.abs(Number(this.beliForm.jumlah));
            }
            if (this.beliForm.harga_satuan !== '' && Number(this.beliForm.harga_satuan) < 0) {
                this.beliForm.harga_satuan = Math.abs(Number(this.beliForm.harga_satuan));
            }
            const jml = Math.max(0, Number(this.beliForm.jumlah) || 0);
            const hrg = Math.max(0, Number(this.beliForm.harga_satuan) || 0);
            this.beliForm.total_biaya = Math.round(jml * hrg);
        },

        openSupplierModal(pakanName = null) {
            this.showSupplierModal = true;
        },

        openMasterModal() {
            this.masterForm = {
                nama_pakan: '',
                kategori_peruntukan: 'pembesaran',
                satuan: 'kg',
                stok_tersisa: 50,
                batas_minimum: 15,
                harga_per_satuan: 12500
            };
            this.showMasterModal = true;
        },

        async handleSaveLog() {
            if (!this.form.id_kolam) {
                alert('Silakan pilih Kolam aktif terlebih dahulu!');
                return;
            }
            if (this.isTelurPhase) {
                alert('Kolam hatchery ini masih dalam fase Telur/Inkubasi dan tidak dapat diberi pakan!');
                return;
            }
            if (Number(this.form.kg_pelet || 0) <= 0 && Number(this.form.kg_daun || 0) <= 0) {
                alert('Silakan masukkan jumlah pakan lebih dari 0!');
                return;
            }
            if (Number(this.form.kg_pelet || 0) > 100) {
                alert('Pemberian pakan utama maksimal 100 kg per sesi!');
                return;
            }
            if (Number(this.form.kg_daun || 0) > 100) {
                alert('Pemberian pakan tambahan maksimal 100 kg per sesi!');
                return;
            }

            this.isSubmittingLog = true;
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
                        id_stok_pakan: this.form.id_stok_pakan || null,
                        id_stok_suplemen: (this.form.kategori_fase === 'pembibitan' ? null : (this.form.id_stok_suplemen || null)),
                        kategori_fase: this.form.kategori_fase,
                        tgl_log: this.form.tgl_log,
                        kg_pelet: Math.min(100, Math.max(0, Number(this.form.kg_pelet) || 0)),
                        kg_daun: this.form.kategori_fase === 'pembibitan' ? 0 : Math.min(100, Math.max(0, Number(this.form.kg_daun) || 0)),
                        jenis_daun: this.form.kategori_fase === 'pembibitan' ? null : (this.selectedSuplemenItem ? this.selectedSuplemenItem.nama_pakan : (this.form.jenis_daun || null)),
                        total_biaya: Math.max(0, Number(this.form.total_biaya) || 0),
                        ph_air: Math.max(0, Math.min(14, Number(this.form.ph_air) || 7.2))
                    })
                });

                const data = await res.json();
                if (res.ok && data.success) {
                    if (data.log) {
                        this.logs.unshift(data.log);
                        this.currentPage = 1;
                    }

                    // Potong stok lokal di browser
                    const usedPelet = Number(this.form.kg_pelet) || 0;
                    const usedDaun = (this.form.kategori_fase === 'pembibitan') ? 0 : (Number(this.form.kg_daun) || 0);

                    if (this.form.id_stok_pakan && usedPelet > 0) {
                        const targetPelet = this.stokList.find(s => s.id_stok_pakan == this.form.id_stok_pakan);
                        if (targetPelet) {
                            targetPelet.stok_tersisa = Math.max(0, Number(targetPelet.stok_tersisa) - usedPelet);
                        }
                    }

                    if (this.form.id_stok_suplemen && usedDaun > 0) {
                        const targetDaun = this.stokList.find(s => s.id_stok_pakan == this.form.id_stok_suplemen);
                        if (targetDaun) {
                            targetDaun.stok_tersisa = Math.max(0, Number(targetDaun.stok_tersisa) - usedDaun);
                        }
                    }

                    this.toastMessage = data.message || 'Log pakan berhasil dicatat & stok terpotong!';
                    this.showToast = true;
                    setTimeout(() => { this.showToast = false; }, 4000);
                    this.resetLogForm();
                } else {
                    alert(data.message || 'Gagal menyimpan log pakan.');
                }
            } catch (err) {
                alert('Terjadi kesalahan saat menyimpan log pakan.');
            } finally {
                this.isSubmittingLog = false;
            }
        },

        async handleSavePembelian() {
            if (!this.beliForm.id_stok_pakan) {
                alert('Silakan pilih Item Pakan!');
                return;
            }
            if (Number(this.beliForm.jumlah || 0) <= 0) {
                alert('Jumlah pembelian pakan harus lebih dari 0 (tidak boleh minus atau 0)!');
                return;
            }
            if (Number(this.beliForm.jumlah || 0) > 1000) {
                alert('Jumlah pembelian pakan maksimal 1.000 ' + (this.selectedBeliPakan ? this.selectedBeliPakan.satuan : 'kg') + ' per transaksi!');
                return;
            }
            if (Number(this.beliForm.harga_satuan || 0) < 0) {
                alert('Harga satuan tidak boleh minus (negatif)!');
                return;
            }

            this.isSubmittingBeli = true;
            try {
                const res = await fetch('{{ route('log-pakan.beli') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        id_stok_pakan: this.beliForm.id_stok_pakan,
                        id_mitra: this.beliForm.id_mitra || null,
                        tgl_beli: this.beliForm.tgl_beli,
                        jumlah: Number(this.beliForm.jumlah),
                        harga_satuan: Number(this.beliForm.harga_satuan),
                        total_biaya: Number(this.beliForm.total_biaya)
                    })
                });

                const data = await res.json();
                if (res.ok && data.success) {
                    // Update lokal stok item
                    const targetItem = this.stokList.find(s => s.id_stok_pakan == this.beliForm.id_stok_pakan);
                    if (targetItem) {
                        targetItem.stok_tersisa = Number(targetItem.stok_tersisa) + Number(this.beliForm.jumlah);
                        targetItem.harga_per_satuan = Number(this.beliForm.harga_satuan);
                    }

                    this.showBeliModal = false;
                    this.toastMessage = data.message || 'Pembelian pakan berhasil dicatat & masuk kas!';
                    this.showToast = true;
                    setTimeout(() => { 
                        this.showToast = false; 
                        window.location.reload(); 
                    }, 1500);
                } else {
                    alert(data.message || 'Gagal mencatat pembelian pakan.');
                }
            } catch (err) {
                alert('Terjadi kesalahan saat mencatat pembelian pakan.');
            } finally {
                this.isSubmittingBeli = false;
            }
        },

        async handleSaveMasterPakan() {
            if (!this.masterForm.nama_pakan) {
                alert('Nama pakan harus diisi!');
                return;
            }

            this.isSubmittingMaster = true;
            try {
                const res = await fetch('{{ route('stok-pakan.store') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify(this.masterForm)
                });

                const data = await res.json();
                if (res.ok && data.success) {
                    this.showMasterModal = false;
                    this.toastMessage = data.message || 'Item pakan baru berhasil ditambahkan!';
                    this.showToast = true;
                    setTimeout(() => { 
                        this.showToast = false; 
                        window.location.reload(); 
                    }, 1500);
                } else {
                    alert(data.message || 'Gagal menambahkan item pakan.');
                }
            } catch (err) {
                alert('Terjadi kesalahan saat menambahkan item pakan.');
            } finally {
                this.isSubmittingMaster = false;
            }
        }
    };
}
</script>
@endpush

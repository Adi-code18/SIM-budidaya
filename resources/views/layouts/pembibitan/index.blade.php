@extends('layouts.app')

@section('title', 'Manajemen Pembibitan - AMS BUDIDAYA')

@section('content')
<div class="space-y-6" x-data="pembibitanComponent()">

    <!-- Header & Action -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">Manajemen Pembibitan</h1>
            <p class="text-xs sm:text-sm text-slate-500 font-medium mt-1">Data real-time fase pembibitan, populasi benih, dan status batch hatchery.</p>
        </div>
        <div>
            <button @click="showForm ? showForm = false : openCreateForm()"
                    class="px-5 py-2.5 rounded-xl bg-[#031B4E] hover:bg-sky-900 text-white font-bold text-xs sm:text-sm shadow-md shadow-sky-950/20 transition-all flex items-center justify-center gap-2">
                <i class="fa-solid" :class="showForm ? 'fa-table-list' : 'fa-circle-plus'" class="text-sm"></i>
                <span x-text="showForm ? 'Lihat Data Batch' : 'Input Batch Baru'"></span>
            </button>
        </div>
    </div>

    <!-- ========= INPUT FORM SECTION ========= -->
    <div x-show="showForm"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 -translate-y-2"
         x-transition:enter-end="opacity-100 translate-y-0"
         class="space-y-4">

        <div class="bg-[#051B44] rounded-2xl p-6 sm:p-8 text-white shadow-xs">
            <h2 class="text-xl font-extrabold text-white">Formulir Pembibitan</h2>
            <p class="text-xs text-sky-200/80 font-medium mt-1">Catat data penjadian dan pembuatan batch baru. Pastikan semua parameter lingkungan sesuai standar operasional.</p>
        </div>

        <div class="bg-white p-6 sm:p-8 rounded-2xl border border-slate-200/80 shadow-xs space-y-6">

            <!-- Section 1: Data Utama Batch & Fase Pertumbuhan -->
            <div class="space-y-4">
                <div class="flex items-center gap-2.5 text-sm font-bold text-slate-900 pb-2 border-b border-slate-100">
                    <div class="w-8 h-8 rounded-xl bg-[#051B44] text-white flex items-center justify-center">
                        <i class="fa-solid fa-fish text-xs"></i>
                    </div>
                    <span>Data Utama Batch &amp; Fase Pertumbuhan</span>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                    <div>
                        <label class="text-[10px] font-extrabold uppercase tracking-wider text-slate-500 block mb-1">ID BATCH SISTEM</label>
                        <input type="text" x-model="form.id" readonly
                               class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs font-extrabold text-slate-500 bg-slate-100 cursor-not-allowed">
                    </div>
                    <div>
                        <label class="text-[10px] font-extrabold uppercase tracking-wider text-slate-500 block mb-1">PILIH SPESIES IKAN (SOP OTOMATIS)</label>
                        <select x-model="selectedIkanId" 
                                @change="onIkanSelected()"
                                class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs font-bold text-sky-800 bg-sky-50/60 focus:bg-white focus:outline-none focus:ring-2 focus:ring-sky-500 transition-all">
                            <option value="">-- Pilih Cuy --</option>
                            @foreach($ikans ?? [] as $ik)
                                <option value="{{ $ik->id_ikan }}">{{ $ik->nama_ikan }} (SOP: {{ $ik->durasi_penetasan + $ik->durasi_pembibitan }} Hari)</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="text-[10px] font-extrabold uppercase tracking-wider text-slate-500 block mb-1">FASE PERTUMBUHAN *</label>
                        <select x-model="form.fase_pertumbuhan"
                                @change="onFaseChange()"
                                class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs font-semibold text-slate-700 bg-slate-50/70 focus:bg-white focus:outline-none focus:ring-2 focus:ring-sky-500 transition-all">
                            <option value="TELUR">TELUR (Masa Pemijahan &amp; Penetasan Awal)</option>
                            <option value="LARVA">LARVA (Benih Kecil / Post-Larva)</option>
                            <option value="FINGERLING">FINGERLING (Benih Siap Pembesaran)</option>
                        </select>
                    </div>
                    <div>
                        <label class="text-[10px] font-extrabold uppercase tracking-wider text-slate-500 block mb-1">JUMLAH BIBIT / TELUR AWAL *</label>
                        <div class="flex items-center gap-2">
                            <input type="number" x-model="form.jumlahBibitAwal" min="1" step="1" placeholder="Contoh: 250000"
                                   onkeydown="if(event.key === '-' || event.key === 'e' || event.key === 'E') event.preventDefault()"
                                   @input="if(form.jumlahBibitAwal !== '' && Number(form.jumlahBibitAwal) < 0) form.jumlahBibitAwal = Math.abs(Number(form.jumlahBibitAwal)) || 0"
                                   class="flex-1 px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs font-semibold text-slate-700 bg-slate-50/70 focus:bg-white focus:outline-none focus:ring-2 focus:ring-sky-500 transition-all">
                            <span class="text-xs font-bold text-slate-400">ekor/btr</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Section 2: Timeline, Mortalitas & Bobot Biomassa -->
            <div class="space-y-4">
                <div class="flex items-center gap-2.5 text-sm font-bold text-slate-900 pb-2 border-b border-slate-100">
                    <div class="w-8 h-8 rounded-xl bg-[#10B981] text-white flex items-center justify-center">
                        <i class="fa-regular fa-calendar text-xs"></i>
                    </div>
                    <span>Timeline Tanggal, Mortalitas &amp; Estimasi Bobot</span>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                    <div>
                        <label class="text-[10px] font-extrabold uppercase tracking-wider text-slate-500 block mb-1">TANGGAL PEMIJAHAN / TEBAR AWAL *</label>
                        <input type="date" x-model="form.tglPemijahan"
                               @change="syncFaseAndStatusFromSOP()"
                               @input="syncFaseAndStatusFromSOP()"
                               class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs font-semibold text-slate-700 bg-slate-50/70 focus:bg-white focus:outline-none focus:ring-2 focus:ring-sky-500 transition-all">
                    </div>
                    <div>
                        <div class="flex items-center justify-between mb-1">
                            <label class="text-[10px] font-extrabold uppercase tracking-wider text-slate-500 block">ESTIMASI SELESAI</label>
                            <button type="button" 
                                    @click="isEstLocked = !isEstLocked" 
                                    class="text-[10px] font-extrabold flex items-center gap-1 transition-colors cursor-pointer"
                                    :class="isEstLocked ? 'text-amber-600 hover:text-amber-700' : 'text-sky-600 hover:text-sky-700'"
                                    :title="isEstLocked ? 'Klik untuk membuka kunci dan edit tanggal manual' : 'Klik untuk mengunci kembali ke mode SOP'">
                                <i class="fa-solid" :class="isEstLocked ? 'fa-lock text-amber-600' : 'fa-lock-open text-sky-600'"></i>
                                <span x-text="isEstLocked ? 'Buka Kunci' : 'Kunci SOP'"></span>
                            </button>
                        </div>
                        <div class="relative">
                            <input type="date" 
                                   x-model="form.est_prcs_pembibitaan"
                                   :readonly="isEstLocked"
                                   :class="isEstLocked ? 'bg-slate-100/90 text-slate-600 cursor-not-allowed border-slate-200 focus:ring-0' : 'bg-slate-50/70 text-slate-700 focus:bg-white focus:outline-none focus:ring-2 focus:ring-sky-500'"
                                   class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs font-semibold transition-all">
                        </div>
                        <span x-show="isEstLocked && selectedIkanId" class="text-[10px] font-semibold text-slate-400 mt-1 block">
                            🔒 Terkunci SOP (Klik <strong>Buka Kunci</strong> untuk edit)
                        </span>
                    </div>
                    <div>
                        <label class="text-[10px] font-extrabold uppercase tracking-wider text-slate-500 block mb-1">JUMLAH KEMATIAN</label>
                        <div class="flex items-center gap-2">
                            <input type="number" x-model="form.jumlahKematian" min="0" placeholder="0"
                                   :disabled="form.fase_pertumbuhan === 'TELUR'"
                                   onkeydown="if(event.key === '-' || event.key === 'e' || event.key === 'E') event.preventDefault()"
                                   @input="if(form.jumlahKematian !== '' && Number(form.jumlahKematian) < 0) form.jumlahKematian = 0"
                                   :class="form.fase_pertumbuhan === 'TELUR' ? 'bg-slate-100/90 text-slate-400 cursor-not-allowed border-slate-200' : 'bg-slate-50/70 text-slate-700 focus:bg-white focus:ring-sky-500'"
                                   class="flex-1 px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs font-semibold focus:outline-none focus:ring-2 transition-all">
                            <span class="text-xs font-bold text-slate-400">ekor</span>
                        </div>
                        <span x-show="form.fase_pertumbuhan === 'TELUR'" class="text-[10px] font-semibold text-slate-400 mt-1 block">* Disabled pada masa Telur</span>
                    </div>
                    <div>
                        <label class="text-[10px] font-extrabold uppercase tracking-wider text-slate-500 block mb-1">TOTAL BOBOT / BIOMASSA (KG) *</label>
                        <div class="flex items-center gap-2">
                            <input type="number" step="0.1" min="0.01" x-model="form.totalBobotKg" placeholder="Contoh: 25.0"
                                   onkeydown="if(event.key === '-' || event.key === 'e' || event.key === 'E') event.preventDefault()"
                                   @input="if(form.totalBobotKg !== '' && Number(form.totalBobotKg) < 0) form.totalBobotKg = Math.abs(Number(form.totalBobotKg)) || 0.1"
                                   class="flex-1 px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs font-bold text-emerald-700 bg-slate-50/70 focus:bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500 transition-all">
                            <span class="text-xs font-extrabold text-emerald-700">Kg</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Section 3: Status & Lokasi Kolam Hatchery -->
            <div class="space-y-4">
                <div class="flex items-center gap-2.5 text-sm font-bold text-slate-900 pb-2 border-b border-slate-100">
                    <div class="w-8 h-8 rounded-xl bg-[#0284C7] text-white flex items-center justify-center">
                        <i class="fa-solid fa-location-dot text-xs"></i>
                    </div>
                    <span>Status Siklus &amp; Lokasi Kolam Hatchery</span>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="text-[10px] font-extrabold uppercase tracking-wider text-slate-500 block mb-1.5">STATUS BATCH</label>
                        <div class="flex flex-wrap items-center gap-2">
                            <button type="button" @click="form.statusBatch = 'inkubasi'"
                                    :disabled="form.fase_pertumbuhan !== 'TELUR'"
                                    :class="form.statusBatch === 'inkubasi' ? 'bg-[#051B44] text-white border-transparent shadow-xs' : (form.fase_pertumbuhan !== 'TELUR' ? 'bg-slate-100 text-slate-400 border-slate-200 cursor-not-allowed opacity-40' : 'bg-white text-slate-600 border-slate-200 hover:bg-slate-50')"
                                    class="px-3 py-1.5 rounded-xl border text-xs font-bold transition-all">
                                Proses Inkubasi
                            </button>
                            <button type="button" @click="form.statusBatch = 'menetas'"
                                    :disabled="form.fase_pertumbuhan !== 'TELUR'"
                                    :class="form.statusBatch === 'menetas' ? 'bg-[#0284C7] text-white border-transparent shadow-xs' : (form.fase_pertumbuhan !== 'TELUR' ? 'bg-slate-100 text-slate-400 border-slate-200 cursor-not-allowed opacity-40' : 'bg-white text-slate-600 border-slate-200 hover:bg-slate-50')"
                                    class="px-3 py-1.5 rounded-xl border text-xs font-bold transition-all">
                                Mulai Menetas
                            </button>
                            <button type="button" @click="form.statusBatch = 'aktif'"
                                    :disabled="form.fase_pertumbuhan === 'TELUR'"
                                    :class="form.statusBatch === 'aktif' ? 'bg-emerald-600 text-white border-transparent shadow-xs' : (form.fase_pertumbuhan === 'TELUR' ? 'bg-slate-100 text-slate-400 border-slate-200 cursor-not-allowed opacity-40' : 'bg-white text-slate-600 border-slate-200 hover:bg-slate-50')"
                                    class="px-3 py-1.5 rounded-xl border text-xs font-bold transition-all">
                                Aktif
                            </button>
                            <button type="button" @click="form.statusBatch = 'siap_pindah'"
                                    :disabled="form.fase_pertumbuhan !== 'FINGERLING'"
                                    :class="form.statusBatch === 'siap_pindah' ? 'bg-teal-600 text-white border-transparent shadow-xs' : (form.fase_pertumbuhan !== 'FINGERLING' ? 'bg-slate-100 text-slate-400 border-slate-200 cursor-not-allowed opacity-40' : 'bg-white text-slate-600 border-slate-200 hover:bg-slate-50')"
                                    class="px-3 py-1.5 rounded-xl border text-xs font-bold transition-all">
                                Siap Pindah
                            </button>
                            <button type="button" @click="handleStatusGagalClick()"
                                    :class="form.statusBatch === 'gagal' ? 'bg-rose-600 text-white border-transparent shadow-xs' : 'bg-white text-rose-600 border-rose-200 hover:bg-rose-50'"
                                    class="px-3 py-1.5 rounded-xl border text-xs font-bold transition-all">
                                Gagal (Hapus Batch)
                            </button>
                        </div>
                    </div>

                    <div>
                        <label class="text-[10px] font-extrabold uppercase tracking-wider text-slate-500 block mb-1">LOKASI KOLAM HATCHERY / PEMBIBITAN *</label>
                        <select x-model="form.kolam" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs font-semibold text-slate-700 bg-slate-50/70 focus:bg-white focus:outline-none focus:ring-2 focus:ring-sky-500 transition-all">
                            <option value="">Pilih Kolam Pembibitan...</option>
                            @if(isset($kolams) && count($kolams) > 0)
                                @foreach($kolams as $k)
                                    <option value="{{ $k->nama_kolam }}">{{ $k->nama_kolam }} ({{ $k->tipe_kolam ?? 'Hatchery' }} - Kapasitas: {{ number_format($k->kapasitas, 0, ',', '.') }} Ekor)</option>
                                @endforeach
                            @else
                                <option value="Kolam Pemijahan A-01">Kolam Pemijahan A-01 (Hatchery / Pemijahan - Kapasitas: 10.000 Ekor)</option>
                                <option value="Kolam Penetasan B-02">Kolam Penetasan B-02 (Hatchery / Penetasan - Kapasitas: 15.000 Ekor)</option>
                                <option value="Kolam Pembibitan L-03">Kolam Pembibitan L-03 (Hatchery / Pendederan - Kapasitas: 20.000 Ekor)</option>
                            @endif
                        </select>
                        <p class="text-[10px] text-slate-400 mt-1 italic">Khusus fasilitas Hatchery Pembibitan: Kolam Pemijahan, Penetasan, atau Pendederan.</p>
                    </div>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
                <button type="button" @click="showForm = false; formMode = 'create'; resetForm()"
                        class="px-5 py-2.5 rounded-xl border border-slate-200 text-xs font-bold text-slate-600 hover:bg-slate-50 transition-colors">
                    Batal
                </button>
                <button type="button" @click="resetForm()"
                        class="px-5 py-2.5 rounded-xl border border-slate-200 text-xs font-bold text-slate-600 hover:bg-slate-50 transition-colors">
                    Reset Form
                </button>
                <button type="button" @click="submitBatch()" :disabled="isSubmitting"
                        class="px-6 py-2.5 rounded-xl bg-[#031B4E] hover:bg-navy-900 text-white font-bold text-xs shadow-md shadow-sky-950/20 transition-all flex items-center gap-2 disabled:opacity-60">
                    <i class="fa-solid" :class="isSubmitting ? 'fa-spinner fa-spin' : 'fa-floppy-disk'" class="text-xs"></i>
                    <span x-text="isSubmitting ? 'MENYIMPAN...' : (formMode === 'edit' ? 'SIMPAN PERUBAHAN' : 'SIMPAN DATA BATCH')"></span>
                </button>
            </div>

        </div>
    </div>

    <!-- ========= DIRECTORY / LIST MODE ========= -->

    <!-- 4 Metric KPI Cards Grid -->
    <div x-show="!showForm" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
        
        <!-- Card 1: Total Benih Aktif -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between">
                    <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400">TOTAL BENIH AKTIF</span>
                    <div class="w-9 h-9 rounded-xl bg-sky-50 text-sky-600 flex items-center justify-center">
                        <i class="fa-solid fa-users text-sm"></i>
                    </div>
                </div>
                <div class="mt-3">
                    <h3 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">{{ $kpis['totalBenih'] ?? '1,225,300' }}</h3>
                </div>
            </div>
            <div class="mt-4 flex items-center gap-1.5 text-xs font-bold text-emerald-600">
                <i class="fa-solid fa-circle-check"></i>
                <span>{{ $kpis['activeBatchCount'] ?? count($batches) }} Batch Aktif Terdata</span>
            </div>
        </div>

        <!-- Card 2: Survival Rate (SR) Rata-rata -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between">
                    <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400">SURVIVAL RATE (SR)</span>
                    <div class="w-9 h-9 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center">
                        <i class="fa-regular fa-heart text-sm"></i>
                    </div>
                </div>
                <div class="mt-3">
                    <h3 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">{{ ($kpis['totalAwal'] ?? 0) > 0 ? ($kpis['srRate'] ?? '0.0') . '%' : '0.0%' }}</h3>
                </div>
            </div>
            <div class="mt-4">
                <div class="w-full bg-slate-100 h-2 rounded-full overflow-hidden">
                    <div class="bg-emerald-500 h-full rounded-full" style="width: {{ ($kpis['totalAwal'] ?? 0) > 0 ? ($kpis['srRateRaw'] ?? 0) : 0 }}%"></div>
                </div>
            </div>
        </div>

        <!-- Card 3: Kapasitas Bak Terpakai -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between">
                    <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400">KAPASITAS BAK</span>
                    <div class="w-9 h-9 rounded-xl bg-sky-50 text-sky-600 flex items-center justify-center">
                        <i class="fa-regular fa-clipboard text-sm"></i>
                    </div>
                </div>
                <div class="mt-3 flex items-baseline gap-1.5">
                    <h3 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">{{ $kpis['bakTerpakai'] ?? 0 }}</h3>
                    <span class="text-xs font-extrabold text-slate-500">/ {{ $kpis['totalBak'] ?? 0 }} Bak</span>
                </div>
            </div>
            <div class="mt-4 text-xs font-semibold text-slate-500">
                {{ $kpis['bakTersedia'] ?? 0 }} Bak tersedia (Siap Pakai)
            </div>
        </div>

        <!-- Card 4: Kualitas Air Hatchery Widget -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between">
                    <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400">KUALITAS AIR (pH) HATCHERY</span>
                    <div class="w-9 h-9 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center">
                        <i class="fa-solid fa-droplet text-sm"></i>
                    </div>
                </div>
                <div class="mt-3 flex items-baseline gap-2">
                    <h3 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">{{ $kpis['avgPh'] }}</h3>
                    <span class="text-xs font-bold {{ $kpis['phStatusClass'] ?? 'text-slate-400' }}">{{ $kpis['phStatus'] ?? 'Belum Ada Data' }}</span>
                </div>
            </div>
            <div class="mt-4 text-xs font-semibold text-slate-500 flex items-center justify-between">
                <span>Rentang Standar pH</span>
                <span class="font-bold text-slate-700">6.8 - 8.0 pH</span>
            </div>
        </div>

    </div>

    <!-- Table Section: Batch Hatchery Aktif -->
    <div x-show="!showForm" class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-visible">
        <div class="p-5 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div>
                <h3 class="text-base font-extrabold text-slate-900" x-text="activeFilter === 'aktif' ? 'Batch Hatchery Aktif' : 'Batch Telah Dipindahkan ke Pembesaran'"></h3>
                <p class="text-xs text-slate-500 font-medium" x-text="activeFilter === 'aktif' ? 'Daftar kelompok benih yang sedang dalam masa pemijahan dan penetasan aktif.' : 'Daftar kelompok bibit yang telah selesai dibudidayakan di hatchery dan dialihkan ke pembesaran (View Only).'"></p>
            </div>
            <div class="flex items-center gap-2">
                <div class="inline-flex p-1 bg-slate-100 rounded-xl text-[11px] font-bold">
                    <button type="button" @click="activeFilter = 'aktif'"
                            :class="activeFilter === 'aktif' ? 'bg-white text-slate-900 shadow-xs' : 'text-slate-500 hover:text-slate-800'"
                            class="px-3 py-1.5 rounded-lg transition-all">
                        Batch Aktif (<span x-text="batches.filter(b => b.status !== 'selesai' && b.status !== 'gagal').length"></span>)
                    </button>
                    <button type="button" @click="activeFilter = 'selesai'"
                            :class="activeFilter === 'selesai' ? 'bg-white text-slate-900 shadow-xs' : 'text-slate-500 hover:text-slate-800'"
                            class="px-3 py-1.5 rounded-lg transition-all">
                        Dipindahkan (<span x-text="batches.filter(b => b.status === 'selesai').length"></span>)
                    </button>
                </div>
            </div>
        </div>

        <div class="overflow-x-auto overflow-y-visible">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50/70 border-b border-slate-200/80 text-[10px] font-extrabold text-slate-400 uppercase tracking-wider">
                        <th class="py-4 px-6">BATCH ID</th>
                        <th class="py-4 px-6">JENIS IKAN</th>
                        <th class="py-4 px-6">FASE PERTUMBUHAN</th>
                        <th class="py-4 px-6">USIA (HARI)</th>
                        <th class="py-4 px-6">JUMLAH (EKOR)</th>
                        <th class="py-4 px-6">TOTAL BOBOT (KG)</th>
                        <th class="py-4 px-6">ESTIMASI SELESAI</th>
                        <th class="py-4 px-6">STATUS BATCH</th>
                        <th class="py-4 px-6 text-right">AKSI</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-xs font-medium text-slate-700">
                    
                    <template x-if="filteredBatches.length === 0">
                        <tr>
                            <td colspan="9" class="py-12 text-center text-slate-400 text-xs font-medium">
                                <i class="fa-solid fa-folder-open text-2xl text-slate-300 block mb-2"></i>
                                Belum ada data batch pembibitan yang tercatat di database.<br>
                                <span class="text-[11px] text-slate-400">Klik tombol <strong>Input Batch Baru</strong> di atas untuk menambahkan data.</span>
                            </td>
                        </tr>
                    </template>

                    <template x-for="item in filteredBatches" :key="item.id">
                        <tr class="hover:bg-slate-50/70 transition-colors">
                            <td class="py-4 px-6">
                                <span class="font-extrabold text-[#0055CC] block cursor-pointer hover:underline text-xs" @click="openDetail(item)" x-text="item.id"></span>
                                <span class="text-[10px] text-slate-400 font-normal" x-text="'Input: ' + item.inputDate"></span>
                            </td>
                            <td class="py-4 px-6">
                                <div class="flex items-center gap-2">
                                    <span class="w-7 h-7 rounded-lg bg-sky-50 text-sky-600 flex items-center justify-center text-xs border border-sky-100/80">
                                        <i class="fa-solid fa-fish"></i>
                                    </span>
                                    <div>
                                        <span class="font-extrabold text-slate-800 text-xs block" x-text="item.jenis_ikan || 'Ikan Nila'"></span>
                                        <span class="text-[10px] text-slate-400 font-medium" x-text="item.kolam || '-'"></span>
                                    </div>
                                </div>
                            </td>
                            <td class="py-4 px-6">
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-extrabold uppercase" :class="item.faseClass" x-text="item.fase"></span>
                            </td>
                            <td class="py-4 px-6 text-slate-700 font-bold" x-text="item.usia"></td>
                            <td class="py-4 px-6 text-slate-900 font-extrabold" x-text="item.jumlah"></td>
                            <td class="py-4 px-6">
                                <span class="inline-flex items-center gap-1 bg-emerald-50 text-emerald-800 px-2.5 py-1 rounded-lg text-xs font-extrabold border border-emerald-200/60" x-text="item.totalBobotFormat || (item.totalBobotKg + ' kg')"></span>
                            </td>
                            <td class="py-4 px-6 font-bold text-slate-800" x-text="item.est_prcs_pembibitaan || '-'"></td>
                            <td class="py-4 px-6">
                                <div class="flex flex-col gap-1 items-start">
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-extrabold" :class="item.statusClass">
                                        <span class="w-1.5 h-1.5 rounded-full" :class="item.dotClass"></span>
                                        <span x-text="item.statusLabel || item.status"></span>
                                    </span>
                                    <!-- Tag Status Pemberian Pakan Hari Ini (Reset Tiap Hari) -->
                                    <template x-if="item.status !== 'selesai' && item.status !== 'gagal'">
                                        <div>
                                            <template x-if="item.fase === 'TELUR' || item.fase_pertumbuhan === 'TELUR' || item.status === 'inkubasi'">
                                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[9px] font-extrabold bg-slate-100 text-slate-600 border border-slate-200" title="Fase telur tidak memerlukan pakan">
                                                    <i class="fa-solid fa-ban text-[8px] text-slate-400"></i>
                                                    <span>Tanpa Pakan (Telur)</span>
                                                </span>
                                            </template>
                                            <template x-if="item.fase !== 'TELUR' && item.fase_pertumbuhan !== 'TELUR' && item.status !== 'inkubasi'">
                                                <div>
                                                    <template x-if="item.is_fed_today">
                                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[9px] font-extrabold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                                            <i class="fa-solid fa-check text-[9px]"></i>
                                                            <span>Sudah Diberi Pakan</span>
                                                        </span>
                                                    </template>
                                                    <template x-if="!item.is_fed_today">
                                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[9px] font-extrabold bg-amber-50 text-amber-700 border border-amber-200">
                                                            <i class="fa-solid fa-clock text-[9px]"></i>
                                                            <span>Belum Diberi Pakan</span>
                                                        </span>
                                                    </template>
                                                </div>
                                            </template>
                                        </div>
                                    </template>
                                </div>
                            </td>
                            <td class="py-4 px-6 text-right">
                                <div class="relative inline-block text-left" 
                                     x-data="{ 
                                         open: false,
                                         menuStyle: '',
                                         toggle(e) {
                                             this.open = !this.open;
                                             if (this.open) {
                                                 const rect = this.$refs.btn.getBoundingClientRect();
                                                 const spaceBelow = window.innerHeight - rect.bottom;
                                                 const menuH = 175;
                                                 const openUp = spaceBelow < menuH && rect.top > menuH;
                                                 const topPos = openUp ? (rect.top - menuH - 4) : (rect.bottom + 4);
                                                 const rightPos = window.innerWidth - rect.right;
                                                 this.menuStyle = `position: fixed; z-index: 99999; top: ${topPos}px; right: ${rightPos}px;`;
                                             }
                                         }
                                     }">
                                    <button x-ref="btn" 
                                            @click="toggle($event)" 
                                            @click.away="open = false" 
                                            class="w-8 h-8 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-600 inline-flex items-center justify-center transition-colors cursor-pointer">
                                        <i class="fa-solid fa-ellipsis-vertical text-sm"></i>
                                    </button>
                                    <div x-show="open" 
                                         @scroll.window="open = false"
                                         x-transition:enter="transition ease-out duration-100" 
                                         x-transition:enter-start="transform opacity-0 scale-95" 
                                         x-transition:enter-end="transform opacity-100 scale-100" 
                                         x-transition:leave="transition ease-in duration-75" 
                                         x-transition:leave-start="transform opacity-100 scale-100" 
                                         x-transition:leave-end="transform opacity-0 scale-95" 
                                         :style="menuStyle"
                                         class="w-48 rounded-xl bg-white border border-slate-200 shadow-2xl py-1.5 z-50 text-left"
                                         style="display: none;">
                                        
                                        <!-- Opsi 1: Detail Batch (Selalu Ada) -->
                                        <button @click="open = false; openDetail(item)" class="w-full px-3.5 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-50 flex items-center gap-2.5">
                                            <i class="fa-solid fa-eye text-sky-600 w-4"></i>
                                            <span>Detail Batch</span>
                                        </button>

                                        <!-- JIKA BATCH STATUS SELESAI (DIPINDAHKAN): CUMA VIEW DOANG & LIHAT DETAIL DI PEMBESARAN -->
                                        <template x-if="item.status === 'selesai'">
                                            <a href="{{ route('pembesaran') }}" class="w-full px-3.5 py-2 text-xs font-semibold text-emerald-700 hover:bg-emerald-50 flex items-center gap-2.5">
                                                <i class="fa-solid fa-arrow-up-right-from-square text-emerald-600 w-4"></i>
                                                <span>Lihat di Pembesaran</span>
                                            </a>
                                        </template>

                                        <!-- JIKA BATCH MASIH AKTIF: PINDAH KE PEMBESARAN, EDIT, DAN HAPUS -->
                                        <template x-if="item.status !== 'selesai'">
                                            <div>
                                                <button @click="open = false; openTransferModal(item)" class="w-full px-3.5 py-2 text-xs font-semibold text-emerald-700 hover:bg-emerald-50 flex items-center gap-2.5">
                                                    <i class="fa-solid fa-right-left text-emerald-600 w-4"></i>
                                                    <span>Pindah ke Pembesaran</span>
                                                </button>

                                                <button @click="open = false; openEdit(item)" class="w-full px-3.5 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-50 flex items-center gap-2.5">
                                                    <i class="fa-solid fa-pen-to-square text-amber-600 w-4"></i>
                                                    <span>Edit Batch</span>
                                                </button>

                                                <div class="my-1 border-t border-slate-100"></div>

                                                <button @click="open = false; deleteBatch(item)" class="w-full px-3.5 py-2 text-xs font-semibold text-red-600 hover:bg-red-50 flex items-center gap-2.5">
                                                    <i class="fa-solid fa-trash-can text-red-500 w-4"></i>
                                                    <span>Hapus Batch</span>
                                                </button>
                                            </div>
                                        </template>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    </template>

                    <!-- Empty state jika tidak ada baris data -->
                    <template x-if="filteredBatches.length === 0">
                        <tr>
                            <td colspan="8" class="py-8 text-center text-slate-400">
                                <i class="fa-solid fa-fish text-2xl mb-2 block text-slate-300"></i>
                                <span x-text="activeFilter === 'aktif' ? 'Tidak ada batch pembibitan aktif saat ini. Semua telah dipindahkan atau belum ada batch baru.' : 'Belum ada data batch yang dipindahkan.'"></span>
                            </td>
                        </tr>
                    </template>

                </tbody>
            </table>
        </div>

        <!-- Table Footer Pagination -->
        <div class="p-4 border-t border-slate-100 flex flex-col sm:flex-row items-center justify-between gap-4 text-xs font-semibold text-slate-500">
            <span x-text="'Menampilkan ' + filteredBatches.length + ' dari ' + batches.length + ' Batch Total'"></span>
            <div class="flex items-center gap-1">
                <button class="w-7 h-7 rounded-lg border border-slate-200 flex items-center justify-center text-slate-400 hover:bg-slate-50">&lt;</button>
                <button class="w-7 h-7 rounded-lg bg-[#031B4E] text-white font-bold flex items-center justify-center">1</button>
                <button class="w-7 h-7 rounded-lg border border-slate-200 flex items-center justify-center text-slate-400 hover:bg-slate-50">&gt;</button>
            </div>
        </div>
    </div>

    <!-- Modal Detail Batch Hatchery -->
    <div x-show="detailModalOpen" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-95"
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm"
         style="display: none;">
        
        <div class="bg-white rounded-2xl max-w-lg w-full p-6 shadow-2xl space-y-5 border border-slate-200" x-show="selectedBatch">
            
            <!-- Modal Header -->
            <div class="flex items-center justify-between border-b border-slate-100 pb-4">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-sky-100 text-sky-700 flex items-center justify-center font-bold text-sm">
                        <i class="fa-solid fa-fish"></i>
                    </div>
                    <div>
                        <span class="text-[10px] font-extrabold uppercase text-slate-400 tracking-wider block">Rincian Data Batch</span>
                        <div class="flex items-center gap-2">
                            <h3 class="text-lg font-extrabold text-slate-900" x-text="selectedBatch?.id"></h3>
                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-extrabold bg-sky-50 text-sky-700 border border-sky-200">
                                <i class="fa-solid fa-fish text-[10px]"></i>
                                <span x-text="selectedBatch?.jenis_ikan || 'Ikan Nila'"></span>
                            </span>
                        </div>
                    </div>
                </div>
                <button @click="detailModalOpen = false" class="w-8 h-8 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-500 flex items-center justify-center transition-colors">
                    <i class="fa-solid fa-xmark text-base"></i>
                </button>
            </div>

            <!-- Banner info jika sudah dipindahkan -->
            <template x-if="selectedBatch?.status === 'selesai'">
                <div class="p-3.5 bg-emerald-50/80 border border-emerald-200/80 rounded-xl space-y-1 text-xs">
                    <div class="flex items-center gap-2 font-bold text-emerald-800">
                        <i class="fa-solid fa-circle-check text-emerald-600"></i>
                        <span>Batch Telah Dipindahkan ke Pembesaran</span>
                    </div>
                    <p class="text-[11px] text-emerald-700">
                        Siklus pembibitan telah selesai dan bibit telah dipindahkan ke kolam pembesaran. Batch ini bersifat arsip (view-only).
                    </p>
                    <template x-if="selectedBatch?.kolam_pembesaran">
                        <div class="pt-1 text-[11px] text-emerald-900 font-semibold flex items-center gap-1.5">
                            <i class="fa-solid fa-location-dot text-emerald-600"></i>
                            <span>Kolam Tujuan: <strong x-text="selectedBatch?.kolam_pembesaran"></strong></span>
                            <span class="text-slate-400">•</span>
                            <span x-show="selectedBatch?.batch_pembesaran_id" x-text="'Batch: ' + selectedBatch?.batch_pembesaran_id"></span>
                        </div>
                    </template>
                </div>
            </template>

            <!-- Modal Content Info Grid -->
            <div class="grid grid-cols-2 sm:grid-cols-3 gap-3 text-xs">
                <div class="p-3.5 bg-slate-50 rounded-xl border border-slate-100">
                    <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400 block mb-1">FASE PERTUMBUHAN</span>
                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold uppercase inline-block" :class="selectedBatch?.faseClass" x-text="selectedBatch?.fase"></span>
                </div>
                <div class="p-3.5 bg-slate-50 rounded-xl border border-slate-100">
                    <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400 block mb-1">STATUS BATCH</span>
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[10px] font-extrabold" :class="selectedBatch?.statusClass">
                        <span class="w-1.5 h-1.5 rounded-full" :class="selectedBatch?.dotClass"></span>
                        <span x-text="selectedBatch?.statusLabel || selectedBatch?.status"></span>
                    </span>
                    <template x-if="selectedBatch?.status !== 'selesai' && selectedBatch?.status !== 'gagal'">
                        <div class="mt-1.5">
                            <template x-if="selectedBatch?.fase === 'TELUR' || selectedBatch?.fase_pertumbuhan === 'TELUR' || selectedBatch?.status === 'inkubasi'">
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[9px] font-extrabold bg-slate-100 text-slate-600 border border-slate-200">
                                    <i class="fa-solid fa-ban text-[8px] text-slate-400"></i>
                                    <span>Tanpa Pakan (Fase Telur)</span>
                                </span>
                            </template>
                            <template x-if="selectedBatch?.fase !== 'TELUR' && selectedBatch?.fase_pertumbuhan !== 'TELUR' && selectedBatch?.status !== 'inkubasi'">
                                <div>
                                    <template x-if="selectedBatch?.is_fed_today">
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[9px] font-extrabold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                            <i class="fa-solid fa-check text-[9px]"></i>
                                            <span>Sudah Diberi Pakan</span>
                                        </span>
                                    </template>
                                    <template x-if="!selectedBatch?.is_fed_today">
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[9px] font-extrabold bg-amber-50 text-amber-700 border border-amber-200">
                                            <i class="fa-solid fa-clock text-[9px]"></i>
                                            <span>Belum Diberi Pakan</span>
                                        </span>
                                    </template>
                                </div>
                            </template>
                        </div>
                    </template>
                </div>
                <div class="p-3.5 bg-slate-50 rounded-xl border border-slate-100">
                    <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400 block mb-1">USIA BATCH (DOC)</span>
                    <span class="font-extrabold text-slate-900 text-sm" x-text="selectedBatch?.usia"></span>
                </div>

                <div class="p-3.5 bg-slate-50 rounded-xl border border-slate-100">
                    <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400 block mb-1">POPULASI (EKOR)</span>
                    <span class="font-extrabold text-slate-900 text-sm" x-text="selectedBatch?.jumlah + ' Ekor'"></span>
                </div>
                <div class="p-3.5 bg-emerald-50/80 rounded-xl border border-emerald-200/80">
                    <span class="text-[10px] font-extrabold uppercase tracking-wider text-emerald-700 block mb-1">TOTAL BOBOT (KG)</span>
                    <span class="font-extrabold text-emerald-800 text-sm" x-text="selectedBatch?.totalBobotFormat || (selectedBatch?.totalBobotKg + ' kg')"></span>
                </div>
                <div class="p-3.5 bg-slate-50 rounded-xl border border-slate-100">
                    <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400 block mb-1">ESTIMASI SELESAI</span>
                    <span class="font-extrabold text-slate-900" x-text="selectedBatch?.est_prcs_pembibitaan || '-'"></span>
                </div>

                <div class="p-3.5 bg-slate-50 rounded-xl border border-slate-100">
                    <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400 block mb-1">KOMODITAS IKAN</span>
                    <span class="font-extrabold text-slate-900 text-sm flex items-center gap-1.5">
                        <i class="fa-solid fa-fish text-sky-500 text-xs"></i>
                        <span x-text="selectedBatch?.jenis_ikan || 'Ikan Nila'"></span>
                    </span>
                </div>

                <div class="p-3.5 bg-slate-50 rounded-xl border border-slate-100">
                    <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400 block mb-1">TANGGAL INPUT</span>
                    <span class="font-bold text-slate-700 text-sm" x-text="selectedBatch?.inputDate"></span>
                </div>

                <div class="p-3.5 bg-sky-50/60 rounded-xl border border-sky-100 flex items-center justify-between">
                    <div>
                        <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400 block">LOKASI KOLAM ASAL</span>
                        <span class="font-extrabold text-[#0B2570] text-xs" x-text="selectedBatch?.kolam"></span>
                    </div>
                    <div class="text-right">
                        <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400 block">KUALITAS AIR</span>
                        <span class="font-extrabold text-slate-800 text-xs" x-text="'pH ' + selectedBatch?.phAir"></span>
                    </div>
                </div>
            </div>

            <!-- Modal Footer -->
            <div class="flex items-center justify-end gap-3 pt-3 border-t border-slate-100">
                <template x-if="selectedBatch?.status !== 'selesai'">
                    <button type="button" @click="openEdit(selectedBatch)" class="px-4 py-2 rounded-xl bg-slate-100 text-slate-700 font-bold hover:bg-slate-200 transition-colors text-xs flex items-center gap-2">
                        <i class="fa-solid fa-pen-to-square text-amber-600"></i>
                        <span>Edit Batch</span>
                    </button>
                </template>
                <template x-if="selectedBatch?.status === 'selesai'">
                    <a href="{{ route('pembesaran') }}" class="px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold transition-colors text-xs flex items-center gap-2 shadow-xs">
                        <i class="fa-solid fa-arrow-up-right-from-square text-xs"></i>
                        <span>Buka Menu Pembesaran</span>
                    </a>
                </template>
                <button type="button" @click="detailModalOpen = false" class="px-5 py-2 rounded-xl bg-[#031B4E] text-white font-extrabold hover:bg-navy-900 text-xs shadow-md shadow-sky-950/20 transition-all">
                    Tutup
                </button>
            </div>
        </div>
    </div>

    <!-- Modal Transfer Batch ke Pembesaran -->
    <div x-show="transferModalOpen" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-95"
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs"
         style="display: none;">
        
        <div class="bg-white rounded-3xl max-w-2xl w-full p-6 shadow-2xl space-y-4 border border-slate-200 overflow-y-auto max-h-[90vh]" @click.outside="transferModalOpen = false">
            <!-- Modal Header -->
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-2xl bg-emerald-100 text-emerald-700 flex items-center justify-center font-bold text-sm">
                        <i class="fa-solid fa-right-left"></i>
                    </div>
                    <div>
                        <span class="text-[10px] font-extrabold uppercase text-slate-400 tracking-wider block">MUTASI BATCH HASIL PEMBIBITAN</span>
                        <h3 class="text-base font-extrabold text-slate-900">Pindah Ke Kolam Pembesaran</h3>
                    </div>
                </div>
                <button @click="transferModalOpen = false" class="w-8 h-8 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-500 flex items-center justify-center transition-colors">
                    <i class="fa-solid fa-xmark text-sm"></i>
                </button>
            </div>

            <!-- Batch Summary Banner with Remaining Calculation -->
            <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-200/80 grid grid-cols-2 sm:grid-cols-4 gap-2 text-xs">
                <div>
                    <span class="text-[10px] font-extrabold uppercase text-slate-400 block">BATCH ASAL</span>
                    <span class="font-extrabold text-[#031B4E]" x-text="selectedBatchToTransfer?.id"></span>
                    <span class="text-slate-500 block font-semibold text-[11px]" x-text="selectedBatchToTransfer?.jenis_ikan || selectedBatchToTransfer?.jenisIkan || 'Ikan Nila'"></span>
                </div>
                <div>
                    <span class="text-[10px] font-extrabold uppercase text-slate-400 block">SISA BIBIT AWAL</span>
                    <span class="font-extrabold text-slate-800 text-xs sm:text-sm" x-text="sisaBibitTersedia.toLocaleString('id-ID') + ' Ekor'"></span>
                </div>
                <div>
                    <span class="text-[10px] font-extrabold uppercase text-slate-400 block">BOBOT BENIH TOTAL</span>
                    <span class="font-extrabold text-emerald-600 text-xs sm:text-sm" x-text="selectedBatchToTransfer?.totalBobotFormat || (selectedBatchToTransfer?.totalBobotKg + ' kg')"></span>
                </div>
                <div class="text-left sm:text-right">
                    <span class="text-[10px] font-extrabold uppercase text-slate-400 block">SISA SETELAH PINDAH</span>
                    <span class="font-extrabold text-xs sm:text-sm" :class="sisaBibitSetelahTransfer <= 0 ? 'text-slate-400' : 'text-sky-700'" x-text="sisaBibitSetelahTransfer.toLocaleString('id-ID') + ' Ekor'"></span>
                </div>
            </div>

            <!-- Formula SOP Info Pill -->
            <div class="px-3 py-2 rounded-xl bg-sky-50/90 border border-sky-200 text-sky-950 text-xs flex flex-wrap items-center justify-between gap-2">
                <div class="flex items-center gap-2">
                    <i class="fa-solid fa-calculator text-sky-600"></i>
                    <span>
                        <strong>Standar SOP Panen (<span x-text="selectedBatchToTransfer?.jenis_ikan || 'Nila'"></span>):</strong>
                        Target Ukuran <strong><span x-text="selectedBatchToTransfer?.avg_ekor_per_kg || 4"></span> Ekor/kg</strong> (~250 gr/ekor) • Survival Rate (SR) <strong>85%</strong>
                    </span>
                </div>
                <span class="text-[10px] font-bold text-sky-700 bg-white px-2 py-0.5 rounded border border-sky-200 shadow-2xs">
                    Target Panen (kg) = (Bibit × 85%) ÷ <span x-text="selectedBatchToTransfer?.avg_ekor_per_kg || 4"></span>
                </span>
            </div>

            <!-- Mode Selector Switch: 1 Kolam vs Auto-Distribusi Multi-Kolam -->
            <div class="flex items-center p-1 bg-slate-100 rounded-xl text-xs font-bold gap-1">
                <button type="button" @click="transferMode = 'single'"
                        :class="transferMode === 'single' ? 'bg-[#031B4E] text-white shadow-xs' : 'text-slate-600 hover:text-slate-900'"
                        class="flex-1 py-2 rounded-lg transition-all flex items-center justify-center gap-1.5 cursor-pointer">
                    <i class="fa-solid fa-water"></i>
                    <span>Transfer ke 1 Kolam (Manual)</span>
                </button>
                <button type="button" @click="transferMode = 'multi'"
                        :class="transferMode === 'multi' ? 'bg-[#031B4E] text-white shadow-xs' : 'text-slate-600 hover:text-slate-900'"
                        class="flex-1 py-2 rounded-lg transition-all flex items-center justify-center gap-1.5 cursor-pointer">
                    <i class="fa-solid fa-layer-group"></i>
                    <span>Auto-Distribusi Multi-Kolam</span>
                </button>
            </div>

            <form @submit.prevent="submitTransfer()" class="space-y-4">
                
                <!-- ================= MODE 1: SINGLE KOLAM ================= -->
                <div x-show="transferMode === 'single'" class="space-y-3.5">
                    <div>
                        <label class="text-[10px] font-extrabold uppercase text-slate-500 block mb-1">PILIH KOLAM PEMBESARAN TUJUAN *</label>
                        <select x-model="transferForm.id_kolam_pembesaran" @change="fillBySinglePondCapacity()" required
                                class="w-full px-3.5 py-2 rounded-xl border border-slate-200 text-xs font-semibold text-slate-800 bg-slate-50 focus:bg-white focus:outline-none focus:border-emerald-600">
                            <option value="">-- Pilih Kolam Pembesaran --</option>
                            <template x-for="kp in kolamPembesaranList" :key="kp.id_kolam">
                                <option :value="kp.id_kolam" x-text="kp.label"></option>
                            </template>
                        </select>
                        <div x-show="selectedSinglePond" class="flex items-center justify-between text-[11px] pt-1 text-slate-500 font-medium">
                            <span>Kapasitas Kolam: <strong class="text-slate-800" x-text="Number(selectedSinglePond?.kapasitas || 0).toLocaleString('id-ID') + ' Ekor'"></strong></span>
                            <span class="text-sky-700 font-bold" x-show="singlePondCapacity > 0" 
                                  x-text="'Target Panen Kapasitas Penuh: ~' + Math.round((singlePondCapacity * 0.85) / (selectedBatchToTransfer?.avg_ekor_per_kg || 4)).toLocaleString('id-ID') + ' kg'"></span>
                        </div>
                    </div>

                    <!-- Input Kuantitas Bibit & Transfer Loss -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <div class="flex items-center justify-between mb-1">
                                <label class="text-[10px] font-extrabold uppercase text-slate-500 block">JUMLAH BIBIT PINDAH (EKOR) *</label>
                                <span class="text-[9px] font-bold text-sky-700 bg-sky-50 px-1.5 py-0.5 rounded border border-sky-200" x-text="'Tersedia: ' + sisaBibitTersedia.toLocaleString('id-ID') + ' Ekor'"></span>
                            </div>
                            <input type="number" step="1" min="1" :max="sisaBibitTersedia" x-model="transferForm.jumlah_bibit_transfer" @input="onSingleBibitChange()" required
                                   class="w-full px-3.5 py-2 rounded-xl border border-slate-200 text-xs font-bold text-slate-900 bg-white focus:outline-none focus:border-emerald-600">
                            <!-- Quick Buttons -->
                            <div class="flex items-center gap-1.5 pt-1.5 flex-wrap">
                                <button type="button" @click="fillBySinglePondCapacity()" x-show="singlePondCapacity > 0"
                                        class="px-2 py-0.5 rounded-md bg-emerald-50 hover:bg-emerald-100 text-emerald-800 border border-emerald-200 text-[10px] font-bold transition-colors">
                                    ⚡ Sesuai Kapasitas (<span x-text="singlePondCapacity.toLocaleString('id-ID') + ' Ekor'"></span>)
                                </button>
                                <button type="button" @click="fillAllBibitSingle()"
                                        class="px-2 py-0.5 rounded-md bg-slate-100 hover:bg-slate-200 text-slate-700 text-[10px] font-bold transition-colors">
                                    Pindahkan Semua (<span x-text="sisaBibitTersedia.toLocaleString('id-ID')"></span>)
                                </button>
                            </div>
                        </div>

                        <div>
                            <label class="text-[10px] font-extrabold uppercase text-slate-500 block mb-1">
                                SUSUT / MATI SAAT TRANSFER (EKOR)
                                <span class="text-[9px] text-slate-400 font-normal lowercase">(opsional afkir/loss)</span>
                            </label>
                            <input type="number" step="1" min="0" x-model="transferForm.jumlah_mati_transfer" placeholder="0"
                                   class="w-full px-3.5 py-2 rounded-xl border border-slate-200 text-xs font-semibold text-rose-700 bg-white focus:outline-none focus:border-rose-500">
                            <p class="text-[10px] text-slate-400 italic pt-1">
                                *Ikan mati/afkir saat penangkapan/grading otomatis dicatat di histori kematian.
                            </p>
                        </div>
                    </div>

                    <!-- 3 Column Metrics -->
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 pt-1">
                        <div>
                            <label class="text-[10px] font-extrabold uppercase text-slate-500 block mb-1">BIOMASSA AWAL (KG)</label>
                            <input type="number" step="0.01" x-model="transferForm.biomassa_est" required
                                   class="w-full px-3 py-1.5 rounded-xl border border-slate-200 text-xs font-bold text-emerald-700 bg-slate-50 focus:bg-white focus:outline-none focus:border-emerald-600">
                            <p class="text-[9px] text-slate-400 italic pt-0.5">Proporsional dari bobot benih saat ini</p>
                        </div>
                        <div>
                            <div class="flex items-center justify-between mb-1">
                                <label class="text-[10px] font-extrabold uppercase text-slate-500 block">TARGET PANEN (KG)</label>
                                <span class="text-[9px] font-bold text-emerald-600 bg-emerald-50 px-1 rounded">SOP 85%</span>
                            </div>
                            <input type="number" step="1" x-model="transferForm.target_panen_kg" required
                                   class="w-full px-3 py-1.5 rounded-xl border border-slate-200 text-xs font-black text-slate-900 bg-slate-50 focus:bg-white focus:outline-none focus:border-emerald-600">
                            <p class="text-[9px] text-slate-500 font-semibold pt-0.5">
                                *~<span x-text="Math.round(Number(transferForm.jumlah_bibit_transfer || 0) * 0.85).toLocaleString('id-ID')"></span> ekor hidup ÷ <span x-text="selectedBatchToTransfer?.avg_ekor_per_kg || 4"></span> ekor/kg
                            </p>
                        </div>
                        <div>
                            <label class="text-[10px] font-extrabold uppercase text-slate-500 block mb-1">ESTIMASI TGL PANEN</label>
                            <input type="date" x-model="transferForm.est_tgl_panen" required
                                   class="w-full px-3 py-1.5 rounded-xl border border-slate-200 text-xs font-bold text-amber-700 bg-slate-50 focus:bg-white focus:outline-none focus:border-emerald-600">
                            <p class="text-[9px] text-slate-400 italic pt-0.5">Sesuai SOP siklus pembesaran</p>
                        </div>
                    </div>

                    <!-- FCR Safe / Overcapacity Indicator -->
                    <div x-show="selectedSinglePond && singlePondCapacity > 0" class="p-3 rounded-xl border text-xs transition-all"
                         :class="isSingleOvercapacity ? 'bg-amber-50/90 border-amber-200 text-amber-950' : 'bg-emerald-50/80 border-emerald-200 text-emerald-950'">
                        <div class="flex items-center justify-between font-bold">
                            <div class="flex items-center gap-1.5">
                                <i class="fa-solid" :class="isSingleOvercapacity ? 'fa-triangle-exclamation text-amber-600' : 'fa-circle-check text-emerald-600'"></i>
                                <span x-text="isSingleOvercapacity ? 'Peringatan: Jumlah Bibit Melebihi Kapasitas Kolam (+ ' + singleOvercapacityPercent + '%)' : 'Kepadatan Kolam Aman & Optimal (Sesuai Kapasitas)'"></span>
                            </div>
                            <span class="text-[11px] font-extrabold" x-text="Number(transferForm.jumlah_bibit_transfer || 0).toLocaleString('id-ID') + ' / ' + singlePondCapacity.toLocaleString('id-ID') + ' Ekor'"></span>
                        </div>
                        
                        <div x-show="isSingleOvercapacity" class="pt-2 mt-2 border-t border-amber-200/80 space-y-1.5">
                            <p class="text-[11px] text-amber-800 leading-relaxed">
                                Kepadatan berlebih (*overstocking*) dapat menyebabkan stres pada ikan, penurunan kualitas air, dan <strong>pembengkakan rasio FCR (boros pakan)</strong>.
                            </p>
                            <label class="flex items-center gap-2 cursor-pointer pt-0.5 select-none font-semibold text-[11px] text-amber-900">
                                <input type="checkbox" x-model="riskAcknowledged" class="rounded text-amber-600 focus:ring-amber-500 w-4 h-4 cursor-pointer">
                                <span>Saya memahami risiko overcapacity terhadap FCR dan kualitas air kolam.</span>
                            </label>
                        </div>
                    </div>
                </div>

                <!-- ================= MODE 2: AUTO-DISTRIBUSI MULTI-KOLAM ================= -->
                <div x-show="transferMode === 'multi'" class="space-y-3.5">
                    
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <div class="flex items-center justify-between mb-1">
                                <label class="text-[10px] font-extrabold uppercase text-slate-500 block">TOTAL BIBIT YANG DIDISTRIBUSIKAN (EKOR) *</label>
                                <span class="text-[9px] font-bold text-sky-700 bg-sky-50 px-1.5 py-0.5 rounded border border-sky-200" x-text="'Tersedia: ' + sisaBibitTersedia.toLocaleString('id-ID') + ' Ekor'"></span>
                            </div>
                            <input type="number" step="1" min="1" :max="sisaBibitTersedia" x-model="multiTotalBibitTransfer" required
                                   class="w-full px-3.5 py-2 rounded-xl border border-slate-200 text-xs font-bold text-slate-900 bg-white focus:outline-none focus:border-emerald-600">
                            
                            <!-- Quick Buttons for Multi Distribution -->
                            <div class="flex items-center gap-1.5 pt-1.5 flex-wrap">
                                <button type="button" @click="fillMultiByCapacity()" x-show="multiTotalCapacity > 0"
                                        class="px-2 py-0.5 rounded-md bg-emerald-50 hover:bg-emerald-100 text-emerald-800 border border-emerald-200 text-[10px] font-bold transition-colors">
                                    ⚡ Sesuai Total Kapasitas (<span x-text="multiTotalCapacity.toLocaleString('id-ID') + ' Ekor'"></span>)
                                </button>
                                <button type="button" @click="multiTotalBibitTransfer = sisaBibitTersedia" 
                                        class="px-2 py-0.5 rounded-md bg-slate-100 hover:bg-slate-200 text-slate-700 text-[10px] font-bold transition-colors">
                                    Distribusi Seluruh Sisa (<span x-text="sisaBibitTersedia.toLocaleString('id-ID') + ' Ekor'"></span>)
                                </button>
                            </div>
                        </div>

                        <div>
                            <label class="text-[10px] font-extrabold uppercase text-slate-500 block mb-1">
                                SUSUT / MATI SAAT TRANSFER (EKOR)
                                <span class="text-[9px] text-slate-400 font-normal lowercase">(opsional loss)</span>
                            </label>
                            <input type="number" step="1" min="0" x-model="transferForm.jumlah_mati_transfer" placeholder="0"
                                   class="w-full px-3.5 py-2 rounded-xl border border-slate-200 text-xs font-semibold text-rose-700 bg-white focus:outline-none focus:border-rose-500">
                            <p class="text-[10px] text-slate-400 italic pt-1">*Otomatis dicatat di histori mortalitas.</p>
                        </div>
                    </div>

                    <!-- Checklist Kolam Tujuan -->
                    <div>
                        <div class="flex items-center justify-between mb-1.5">
                            <label class="text-[10px] font-extrabold uppercase text-slate-500 block">PILIH KOLAM PEMBESARAN TUJUAN *</label>
                            <span class="text-[10px] font-bold text-sky-700" x-text="multiSelectedPonds.length + ' Kolam Terpilih (Kapasitas Total: ' + multiTotalCapacity.toLocaleString('id-ID') + ' Ekor)'"></span>
                        </div>
                        
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 max-h-40 overflow-y-auto p-2 bg-slate-50 rounded-xl border border-slate-200">
                            <template x-for="kp in kolamPembesaranList" :key="kp.id_kolam">
                                <label class="flex items-center gap-2 p-2 rounded-lg bg-white border border-slate-200 hover:border-emerald-300 cursor-pointer text-xs transition-colors">
                                    <input type="checkbox" :value="kp.id_kolam" x-model="multiSelectedPonds" @change="if(multiTotalBibitTransfer <= 0) fillMultiByCapacity()" class="rounded text-emerald-600 focus:ring-emerald-500 w-4 h-4 cursor-pointer">
                                    <div class="flex-1 min-w-0">
                                        <div class="font-extrabold text-slate-800 text-[11px] truncate" x-text="kp.nama_kolam"></div>
                                        <div class="text-[10px] text-slate-400 font-semibold" x-text="(kp.tipe_kolam || 'Pembesaran') + ' • Kap: ' + Number(kp.kapasitas || 0).toLocaleString('id-ID') + ' Ekor'"></div>
                                    </div>
                                </label>
                            </template>
                        </div>
                    </div>

                    <!-- Live Breakdown Alokasi Proporsional Table -->
                    <div x-show="multiAllocations.length > 0" class="space-y-2">
                        <span class="text-[10px] font-extrabold uppercase text-slate-400 block tracking-wider">HASIL ALOKASI OTOMATIS (PROPORSIONAL KAPASITAS)</span>
                        <div class="overflow-x-auto rounded-xl border border-slate-200 bg-white shadow-2xs">
                            <table class="w-full text-[11px] text-left">
                                <thead class="bg-slate-50 text-[10px] font-extrabold uppercase text-slate-500 border-b border-slate-200">
                                    <tr>
                                        <th class="px-3 py-2">Kolam</th>
                                        <th class="px-2 py-2 text-right">Kapasitas</th>
                                        <th class="px-2 py-2 text-right">Bibit Ditebar</th>
                                        <th class="px-2 py-2 text-right">Est. Hidup (85%)</th>
                                        <th class="px-2 py-2 text-right">Biomassa Awal</th>
                                        <th class="px-2 py-2 text-right">Target Panen</th>
                                        <th class="px-3 py-2 text-center">Beban Kolam</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100 font-medium">
                                    <template x-for="alloc in multiAllocations" :key="alloc.id_kolam">
                                        <tr class="hover:bg-slate-50/50">
                                            <td class="px-3 py-2 font-bold text-slate-800" x-text="alloc.nama_kolam"></td>
                                            <td class="px-2 py-2 text-right font-medium text-slate-500" x-text="alloc.kapasitas.toLocaleString('id-ID') + ' Ekor'"></td>
                                            <td class="px-2 py-2 text-right font-extrabold text-sky-900" x-text="alloc.bibit_ekor.toLocaleString('id-ID') + ' Ekor'"></td>
                                            <td class="px-2 py-2 text-right text-slate-600" x-text="alloc.est_hidup_ekor.toLocaleString('id-ID') + ' Ekor'"></td>
                                            <td class="px-2 py-2 text-right text-slate-600" x-text="alloc.biomassa_kg + ' kg'"></td>
                                            <td class="px-2 py-2 text-right">
                                                <span class="font-black text-emerald-700 block" x-text="alloc.target_panen_kg.toLocaleString('id-ID') + ' kg'"></span>
                                                <span class="text-[9px] text-slate-400 block" x-text="'(' + alloc.est_hidup_ekor.toLocaleString('id-ID') + ' ÷ ' + (selectedBatchToTransfer?.avg_ekor_per_kg || 4) + ')'"></span>
                                            </td>
                                            <td class="px-3 py-2 text-center">
                                                <span class="px-2 py-0.5 rounded text-[10px] font-bold"
                                                      :class="alloc.is_overcapacity ? 'bg-rose-100 text-rose-800' : 'bg-emerald-100 text-emerald-800'"
                                                      x-text="alloc.percent_of_cap + '% (' + (alloc.is_overcapacity ? 'Over' : 'Aman') + ')'"></span>
                                            </td>
                                        </tr>
                                    </template>
                                </tbody>
                            </table>
                        </div>

                        <!-- Multi Overcapacity Risk Checkbox -->
                        <div x-show="isMultiOvercapacity" class="p-3 rounded-xl bg-amber-50 border border-amber-200 text-xs space-y-1.5">
                            <div class="flex items-center gap-1.5 font-bold text-amber-900">
                                <i class="fa-solid fa-triangle-exclamation text-amber-600"></i>
                                <span>Sebagian kolam mengalami kepadatan di atas kapasitas rekomendasi.</span>
                            </div>
                            <label class="flex items-center gap-2 cursor-pointer pt-0.5 select-none font-semibold text-[11px] text-amber-900">
                                <input type="checkbox" x-model="riskAcknowledged" class="rounded text-amber-600 focus:ring-amber-500 w-4 h-4 cursor-pointer">
                                <span>Saya memahami risiko overcapacity terhadap FCR dan kualitas air kolam.</span>
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Modal Footer -->
                <div class="pt-3 flex items-center justify-end gap-2.5 border-t border-slate-100">
                    <button type="button" @click="transferModalOpen = false"
                            class="px-4 py-2 rounded-xl border border-slate-200 text-xs font-bold text-slate-600 hover:bg-slate-50 transition-colors">
                        Batal
                    </button>
                    <button type="submit" :disabled="isSubmitting || ((isSingleOvercapacity || isMultiOvercapacity) && !riskAcknowledged)"
                            class="px-5 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 disabled:opacity-50 disabled:cursor-not-allowed text-white font-bold text-xs flex items-center gap-2 shadow-md shadow-emerald-600/20 transition-all">
                        <i class="fa-solid" :class="isSubmitting ? 'fa-spinner fa-spin' : 'fa-check'"></i>
                        <span x-text="transferMode === 'multi' ? 'Konfirmasi Distribusi ke ' + multiSelectedPonds.length + ' Kolam' : 'Konfirmasi Pindah ke Pembesaran'"></span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Custom Confirmation Modal -->
    <div x-show="deleteModalOpen" 
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 backdrop-blur-xs p-4"
         style="display: none;">
        
        <div @click.outside="deleteModalOpen = false" 
             x-show="deleteModalOpen"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 scale-95 translate-y-3"
             x-transition:enter-end="opacity-100 scale-100 translate-y-0"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100 scale-100 translate-y-0"
             x-transition:leave-end="opacity-0 scale-95 translate-y-3"
             class="bg-white w-full max-w-md rounded-3xl shadow-2xl border border-slate-100 p-6 space-y-5 text-center">
            
            <div class="w-16 h-16 rounded-2xl bg-rose-50 text-rose-600 border border-rose-100 mx-auto flex items-center justify-center text-2xl shadow-xs">
                <i class="fa-solid fa-triangle-exclamation"></i>
            </div>

            <div class="space-y-1.5">
                <h3 class="text-lg font-extrabold text-slate-900">Hapus Data Batch?</h3>
                <p class="text-xs text-slate-500 font-medium leading-relaxed">
                    Apakah Anda yakin ingin menghapus data batch <strong class="text-slate-800" x-text="selectedBatchToDelete?.id"></strong>? Data yang dihapus tidak dapat dipulihkan.
                </p>
            </div>

            <div class="grid grid-cols-2 gap-3 pt-2">
                <button type="button" @click="deleteModalOpen = false" 
                        class="w-full py-2.5 rounded-xl border border-slate-200 text-slate-700 hover:bg-slate-50 font-bold text-xs transition-colors">
                    Batalkan
                </button>
                <button type="button" @click="executeDeleteBatch()" 
                        class="w-full py-2.5 rounded-xl bg-rose-600 hover:bg-rose-700 active:scale-[0.99] text-white font-bold text-xs shadow-md shadow-rose-950/20 transition-all flex items-center justify-center gap-2">
                    <i class="fa-solid fa-trash-can text-xs"></i>
                    <span>Ya, Hapus</span>
                </button>
            </div>
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
function pembibitanComponent() {
    return {
        showForm: false,
        formMode: 'create',
        detailModalOpen: false,
        selectedBatch: null,
        isSubmitting: false,

        selectedIkanId: '',
        ikansList: {!! json_encode($ikans ?? []) !!},
        isEstLocked: true,

        form: {
            id: 'BCH-' + new Date().getFullYear() + '-' + String(Math.floor(10 + Math.random() * 90)) + '-A01',
            id_batch: null,
            fase_pertumbuhan: 'TELUR',
            jumlahBibitAwal: 250000,
            totalBobotKg: 25.0,
            tglPemijahan: new Date().toISOString().split('T')[0],
            est_prcs_pembibitaan: '',
            jumlahKematian: 0,
            statusBatch: 'inkubasi',
            kolam: ''
        },

        batches: {!! json_encode($batches ?? []) !!},
        activeFilter: 'aktif',

        get filteredBatches() {
            if (this.activeFilter === 'selesai') {
                return this.batches.filter(b => b.status === 'selesai');
            }
            if (this.activeFilter === 'all') {
                return this.batches;
            }
            // Default: hanya tampilkan batch hatchery aktif (bukan selesai / gagal)
            return this.batches.filter(b => b.status !== 'selesai' && b.status !== 'gagal');
        },

        deleteModalOpen: false,
        selectedBatchToDelete: null,
        transferModalOpen: false,
        selectedBatchToTransfer: null,
        kolamPembesaranList: {!! json_encode($kolamPembesaranList ?? []) !!},
        transferMode: 'single',
        riskAcknowledged: false,
        multiSelectedPonds: [],
        multiTotalBibitTransfer: 0,
        transferForm: {
            id_kolam_pembesaran: '',
            jumlah_bibit_transfer: 0,
            biomassa_est: 0,
            target_panen_kg: 0,
            est_tgl_panen: '',
            jumlah_mati_transfer: 0
        },
        showToast: false,
        toastMessage: '',

        get selectedSinglePond() {
            if (!this.transferForm.id_kolam_pembesaran) return null;
            return (this.kolamPembesaranList || []).find(k => k.id_kolam == this.transferForm.id_kolam_pembesaran || k.nama_kolam === this.transferForm.id_kolam_pembesaran);
        },

        get singlePondCapacity() {
            return this.selectedSinglePond ? Number(this.selectedSinglePond.kapasitas || 0) : 0;
        },

        get isSingleOvercapacity() {
            if (this.singlePondCapacity <= 0) return false;
            return Number(this.transferForm.jumlah_bibit_transfer || 0) > this.singlePondCapacity;
        },

        get singleOvercapacityPercent() {
            if (this.singlePondCapacity <= 0) return 0;
            const bibit = Number(this.transferForm.jumlah_bibit_transfer || 0);
            const diff = bibit - this.singlePondCapacity;
            return diff > 0 ? Math.round((diff / this.singlePondCapacity) * 100) : 0;
        },

        get sisaBibitTersedia() {
            if (!this.selectedBatchToTransfer) return 0;
            const item = this.selectedBatchToTransfer;
            return Number(item.jumlahRaw !== undefined ? item.jumlahRaw : ((item.jumlahBibitAwal || 0) - (item.jumlahKematian || 0))) || 0;
        },

        get sisaBibitSetelahTransfer() {
            const loss = Number(this.transferForm.jumlah_mati_transfer || 0);
            if (this.transferMode === 'single') {
                const tr = Number(this.transferForm.jumlah_bibit_transfer || 0);
                return Math.max(0, this.sisaBibitTersedia - tr - loss);
            } else {
                const tr = Number(this.multiTotalBibitTransfer || 0);
                return Math.max(0, this.sisaBibitTersedia - tr - loss);
            }
        },

        get multiSelectedPondsDetails() {
            const selectedIds = (this.multiSelectedPonds || []).map(id => String(id));
            return (this.kolamPembesaranList || []).filter(k => selectedIds.includes(String(k.id_kolam)));
        },

        get multiTotalCapacity() {
            return this.multiSelectedPondsDetails.reduce((sum, k) => sum + Number(k.kapasitas || 0), 0);
        },

        get multiAllocations() {
            const totalCap = this.multiTotalCapacity;
            const totalBibit = Number(this.multiTotalBibitTransfer || 0);
            const item = this.selectedBatchToTransfer;
            const avgEkorPerKg = item ? (Number(item.avg_ekor_per_kg || 4.0) > 0 ? Number(item.avg_ekor_per_kg || 4.0) : 4.0) : 4.0;
            const totalBobotKg = item ? Number(item.totalBobotKg || 0) : 0;
            const sisaBibitAwal = this.sisaBibitTersedia;
            const harvestMonths = item ? Number(item.bulan_panen_max || 3.0) : 3.0;
            const harvestDays = Math.max(30, Math.round(harvestMonths * 30));
            const estHarvestDate = new Date(Date.now() + harvestDays * 24 * 60 * 60 * 1000).toISOString().split('T')[0];

            return this.multiSelectedPondsDetails.map(k => {
                const cap = Number(k.kapasitas || 0);
                const ratio = totalCap > 0 ? (cap / totalCap) : (1 / Math.max(1, this.multiSelectedPondsDetails.length));
                const bibitAlloc = Math.round(totalBibit * ratio);
                const estHidup = Math.round(bibitAlloc * 0.85);
                const targetPanen = Math.round(estHidup / avgEkorPerKg);
                const biomassaAlloc = sisaBibitAwal > 0 ? Number(((bibitAlloc / sisaBibitAwal) * totalBobotKg).toFixed(2)) : 0;
                const isOver = cap > 0 && bibitAlloc > cap;

                return {
                    id_kolam: k.id_kolam,
                    nama_kolam: k.nama_kolam,
                    tipe_kolam: k.tipe_kolam,
                    kapasitas: cap,
                    bibit_ekor: bibitAlloc,
                    est_hidup_ekor: estHidup,
                    biomassa_kg: biomassaAlloc,
                    target_panen_kg: targetPanen,
                    est_tgl_panen: estHarvestDate,
                    is_overcapacity: isOver,
                    percent_of_cap: cap > 0 ? Math.round((bibitAlloc / cap) * 100) : 0
                };
            });
        },

        get isMultiOvercapacity() {
            return this.multiAllocations.some(a => a.is_overcapacity);
        },

        onIkanSelected() {
            this.syncFaseAndStatusFromSOP();
        },

        syncFaseAndStatusFromSOP() {
            if (!this.form.tglPemijahan) return;

            let penetasan = 3;
            let pembibitan = 21;
            if (this.selectedIkanId) {
                const found = (this.ikansList || []).find(i => String(i.id_ikan) === String(this.selectedIkanId));
                if (found) {
                    penetasan = Number(found.durasi_penetasan || 3);
                    pembibitan = Number(found.durasi_pembibitan || 21);
                }
            }

            const totalDays = penetasan + pembibitan;
            const larvaEndDay = penetasan + Math.round(pembibitan * 0.5);

            // Hitung usia hari dari tanggal pemijahan s/d hari ini
            const tgl = new Date(this.form.tglPemijahan);
            const today = new Date();
            const diffTime = today.setHours(0,0,0,0) - tgl.setHours(0,0,0,0);
            const days = Math.max(0, Math.floor(diffTime / (1000 * 60 * 60 * 24)));

            // Sinkronkan fase pertumbuhan berdasarkan SOP & Usia Hari
            if (days <= penetasan) {
                this.form.fase_pertumbuhan = 'TELUR';
                this.form.jumlahKematian = 0;
                if (this.form.statusBatch !== 'gagal') {
                    this.form.statusBatch = (days <= 1) ? 'inkubasi' : 'menetas';
                }
            } else if (days <= larvaEndDay) {
                this.form.fase_pertumbuhan = 'LARVA';
                if (this.form.statusBatch !== 'gagal') {
                    this.form.statusBatch = 'aktif';
                }
            } else {
                this.form.fase_pertumbuhan = 'FINGERLING';
                if (this.form.statusBatch !== 'gagal') {
                    this.form.statusBatch = (days >= totalDays) ? 'siap_pindah' : 'aktif';
                }
            }

            // Sinkronkan estimasi tanggal selesai proses pembibitan
            if (this.isEstLocked) {
                const d = new Date(this.form.tglPemijahan);
                d.setDate(d.getDate() + totalDays);
                this.form.est_prcs_pembibitaan = d.toISOString().split('T')[0];
            }
        },

        openCreateForm() {
            this.formMode = 'create';
            this.selectedIkanId = '';
            this.isEstLocked = true;
            this.resetForm();
            this.syncFaseAndStatusFromSOP();
            this.showForm = true;
            window.scrollTo({ top: 0, behavior: 'smooth' });
        },

        openEdit(item) {
            this.formMode = 'edit';
            this.selectedIkanId = item.id_ikan ? String(item.id_ikan) : '';
            if (!this.selectedIkanId && item.jenis_ikan && this.ikansList) {
                const found = this.ikansList.find(ik => ik.nama_ikan.toLowerCase() === item.jenis_ikan.toLowerCase());
                if (found) this.selectedIkanId = String(found.id_ikan);
            }
            this.isEstLocked = false;
            this.selectedBatch = item;
            const itemFase = (item.fase || 'TELUR').toUpperCase();
            this.form = {
                id: item.id,
                id_batch: item.id_batch,
                fase_pertumbuhan: itemFase,
                jumlahBibitAwal: item.jumlahBibitAwal || 250000,
                totalBobotKg: item.totalBobotKg || 25.0,
                tglPemijahan: item.tglPemijahan || new Date().toISOString().split('T')[0],
                est_prcs_pembibitaan: item.est_prcs_raw || (item.est_prcs_pembibitaan && item.est_prcs_pembibitaan !== '-' ? item.est_prcs_pembibitaan : ''),
                jumlahKematian: itemFase === 'TELUR' ? 0 : (item.jumlahKematian || 0),
                statusBatch: item.status || 'aktif',
                kolam: item.kolam || ''
            };
            this.detailModalOpen = false;
            this.showForm = true;
            window.scrollTo({ top: 0, behavior: 'smooth' });
        },

        resetForm() {
            this.selectedIkanId = '';
            this.form = {
                id: 'BCH-' + new Date().getFullYear() + '-' + String(Math.floor(10 + Math.random() * 90)) + '-A01',
                id_batch: null,
                fase_pertumbuhan: 'TELUR',
                jumlahBibitAwal: 250000,
                totalBobotKg: 25.0,
                tglPemijahan: new Date().toISOString().split('T')[0],
                est_prcs_pembibitaan: '',
                jumlahKematian: 0,
                statusBatch: 'inkubasi',
                kolam: ''
            };
        },

        onFaseChange() {
            const fase = (this.form.fase_pertumbuhan || 'TELUR').toUpperCase();
            if (fase === 'TELUR') {
                this.form.jumlahKematian = 0;
                if (this.form.statusBatch !== 'inkubasi' && this.form.statusBatch !== 'menetas' && this.form.statusBatch !== 'gagal') {
                    this.form.statusBatch = 'inkubasi';
                }
            } else if (fase === 'LARVA') {
                if (this.form.statusBatch !== 'aktif' && this.form.statusBatch !== 'gagal') {
                    this.form.statusBatch = 'aktif';
                }
            } else if (fase === 'FINGERLING') {
                if (this.form.statusBatch !== 'aktif' && this.form.statusBatch !== 'siap_pindah' && this.form.statusBatch !== 'gagal') {
                    this.form.statusBatch = 'aktif';
                }
            }
        },

        async handleStatusGagalClick() {
            this.form.statusBatch = 'gagal';
            const res = await AppSwal.confirmDelete({
                title: 'Set Batch Gagal & Hapus?',
                text: 'Menyatakan batch ini GAGAL akan langsung MENGHAPUS data batch dari sistem. Apakah Anda yakin?',
                confirmText: 'Ya, Tandai Gagal & Hapus',
                cancelText: 'Batal'
            });
            if (res.isConfirmed) {
                this.confirmDeleteGagalBatch();
            }
        },

        async confirmDeleteGagalBatch() {
            if (this.formMode === 'edit' && (this.form.id_batch || this.form.id)) {
                this.selectedBatchToDelete = { id: this.form.id, id_batch: this.form.id_batch };
                await this.executeDeleteBatch();
                this.showForm = false;
                this.resetForm();
            } else {
                AppSwal.toast('Data batch dengan status Gagal dibatalkan dan tidak disimpan.', 'info');
                this.showForm = false;
                this.resetForm();
            }
        },

        resetForm() {
            this.form = {
                id: 'BCH-' + new Date().getFullYear() + '-' + String(Math.floor(10 + Math.random() * 90)) + '-A01',
                id_batch: null,
                fase_pertumbuhan: 'TELUR',
                jumlahBibitAwal: 250000,
                totalBobotKg: 25.0,
                tglPemijahan: new Date().toISOString().split('T')[0],
                est_prcs_pembibitaan: '',
                jumlahKematian: 0,
                statusBatch: 'inkubasi',
                kolam: ''
            };
        },

        getFaseClass(fase) {
            const f = (fase || '').toUpperCase();
            if (f === 'TELUR') return 'bg-slate-100 text-slate-700';
            if (f === 'LARVA') return 'bg-sky-100 text-sky-700';
            return 'bg-indigo-100 text-indigo-700';
        },

        async submitBatch() {
            if (this.form.statusBatch === 'gagal') {
                const res = await AppSwal.confirmDelete({
                    title: 'Status Batch Gagal',
                    text: 'Status batch diset GAGAL! Batch ini akan dihapus dari sistem. Lanjutkan?',
                    confirmText: 'Ya, Hapus',
                    cancelText: 'Batal'
                });
                if (res.isConfirmed) {
                    await this.confirmDeleteGagalBatch();
                }
                return;
            }

            if (!this.form.jumlahBibitAwal || Number(this.form.jumlahBibitAwal) <= 0) {
                alert('Jumlah Bibit / Telur Awal harus lebih besar dari 0 (tidak boleh angka minus atau 0)!');
                return;
            }
            if (this.form.fase_pertumbuhan === 'TELUR') {
                this.form.jumlahKematian = 0;
            }
            if (Number(this.form.jumlahKematian || 0) < 0) {
                alert('Jumlah Kematian tidak boleh berupa angka minus!');
                return;
            }
            if (Number(this.form.jumlahKematian || 0) > Number(this.form.jumlahBibitAwal)) {
                alert('Jumlah Kematian (' + this.form.jumlahKematian + ') tidak boleh melebihi Jumlah Bibit Awal (' + this.form.jumlahBibitAwal + ')!');
                return;
            }
            if (!this.form.totalBobotKg || Number(this.form.totalBobotKg) <= 0) {
                alert('Total Bobot / Biomassa harus lebih besar dari 0 kg!');
                return;
            }
            if (!this.form.kolam) {
                alert('Silakan pilih Lokasi Kolam Pemijahan terlebih dahulu!');
                return;
            }

            this.isSubmitting = true;
            const faseVal = (this.form.fase_pertumbuhan || 'TELUR').toUpperCase();
            const bibitAwalNum = Math.abs(Number(this.form.jumlahBibitAwal || 0));
            const matiNum = faseVal === 'TELUR' ? 0 : Math.max(0, Number(this.form.jumlahKematian || 0));
            const bobotKgNum = Math.abs(Number(this.form.totalBobotKg || 0));
            const sisaBibit = Math.max(0, bibitAwalNum - matiNum);
            const rawStatus = this.form.statusBatch;

            let statusLabel = 'Aktif';
            let statusClass = 'bg-emerald-100 text-emerald-700';
            let dotClass = 'bg-emerald-500';

            if (rawStatus === 'inkubasi') {
                statusLabel = 'Proses Inkubasi';
                statusClass = 'bg-amber-100 text-amber-700';
                dotClass = 'bg-amber-500';
            } else if (rawStatus === 'menetas') {
                statusLabel = 'Mulai Menetas';
                statusClass = 'bg-sky-100 text-sky-700';
                dotClass = 'bg-sky-500';
            } else if (rawStatus === 'siap_pindah') {
                statusLabel = 'Siap Pindah';
                statusClass = 'bg-teal-100 text-teal-700';
                dotClass = 'bg-teal-500';
            } else if (rawStatus === 'selesai') {
                statusLabel = 'Selesai (Dipindahkan)';
                statusClass = 'bg-slate-100 text-slate-700';
                dotClass = 'bg-slate-500';
            } else if (rawStatus === 'gagal') {
                statusLabel = 'Gagal / Dibatalkan';
                statusClass = 'bg-rose-100 text-rose-700';
                dotClass = 'bg-rose-500';
            }

            if (this.formMode === 'edit') {
                const idBatch = this.form.id_batch || (this.selectedBatch ? this.selectedBatch.id_batch : null);
                try {
                    const res = await fetch('/pembibitan/' + idBatch, {
                        method: 'PUT',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({
                            id_kolam: this.form.kolam,
                            id_ikan: this.selectedIkanId ? Number(this.selectedIkanId) : null,
                            tgl_pemijahan: this.form.tglPemijahan,
                            est_prcs_pembibitaan: this.form.est_prcs_pembibitaan,
                            fase_pertumbuhan: faseVal,
                            jumlah_bibitAwal: bibitAwalNum,
                            jumlah_kematian: matiNum,
                            total_bobot_kg: bobotKgNum,
                            status: rawStatus
                        })
                    });
                    const data = await res.json();
                    if (res.ok && data.success) {
                        const targetIndex = this.batches.findIndex(b => b.id_batch === idBatch || b.id === this.form.id);
                        if (targetIndex !== -1) {
                            let editIkName = this.batches[targetIndex].jenis_ikan || 'Ikan Nila';
                            if (data.batch && data.batch.ikan && data.batch.ikan.nama_ikan) {
                                editIkName = data.batch.ikan.nama_ikan;
                            } else if (data.batch && data.batch.jenis_ikan) {
                                editIkName = data.batch.jenis_ikan;
                            } else if (this.selectedIkanId) {
                                const fIk = this.ikansList.find(i => String(i.id_ikan) === String(this.selectedIkanId));
                                if (fIk) editIkName = fIk.nama_ikan;
                            }
                            this.batches[targetIndex].id_ikan = this.selectedIkanId ? Number(this.selectedIkanId) : null;
                            this.batches[targetIndex].jenis_ikan = editIkName;
                            this.batches[targetIndex].est_prcs_pembibitaan = this.form.est_prcs_pembibitaan ? new Date(this.form.est_prcs_pembibitaan).toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' }) : '-';
                            this.batches[targetIndex].est_prcs_raw = this.form.est_prcs_pembibitaan;
                            this.batches[targetIndex].fase = faseVal;
                            this.batches[targetIndex].faseClass = this.getFaseClass(faseVal);
                            this.batches[targetIndex].kolam = this.form.kolam;
                            this.batches[targetIndex].tglPemijahan = this.form.tglPemijahan;
                            this.batches[targetIndex].jumlahBibitAwal = bibitAwalNum;
                            this.batches[targetIndex].jumlahKematian = matiNum;
                            this.batches[targetIndex].jumlah = sisaBibit.toLocaleString('id-ID');
                            this.batches[targetIndex].totalBobotKg = bobotKgNum;
                            this.batches[targetIndex].totalBobotFormat = bobotKgNum.toLocaleString('id-ID', { minimumFractionDigits: 1, maximumFractionDigits: 2 }) + ' kg';
                            this.batches[targetIndex].status = rawStatus;
                            this.batches[targetIndex].statusLabel = statusLabel;
                            this.batches[targetIndex].statusClass = statusClass;
                            this.batches[targetIndex].dotClass = dotClass;
                        }
                        this.showForm = false;
                        this.toastMessage = data.message || 'Data batch berhasil diperbarui!';
                        this.showToast = true;
                        setTimeout(() => { this.showToast = false; }, 4000);
                        this.formMode = 'create';
                        this.resetForm();
                    } else {
                        alert(data.message || 'Gagal memperbarui data batch.');
                    }
                } catch (e) {
                    alert('Terjadi kesalahan saat memperbarui batch.');
                } finally {
                    this.isSubmitting = false;
                }
                return;
            }

            // Create Mode
            try {
                const res = await fetch('{{ route('pembibitan.store') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        id_kolam: this.form.kolam,
                        id_ikan: this.selectedIkanId ? Number(this.selectedIkanId) : null,
                        tgl_pemijahan: this.form.tglPemijahan,
                        est_prcs_pembibitaan: this.form.est_prcs_pembibitaan,
                        fase_pertumbuhan: faseVal,
                        jumlah_bibitAwal: bibitAwalNum,
                        jumlah_kematian: matiNum,
                        total_bobot_kg: bobotKgNum,
                        status: rawStatus
                    })
                });

                const data = await res.json();
                if (res.ok && data.success) {
                    const newBatch = data.batch;
                    const diffDays = this.form.tglPemijahan ? Math.max(0, Math.floor((new Date() - new Date(this.form.tglPemijahan)) / (1000 * 60 * 60 * 24))) : 0;
                    const finalBobot = Number(newBatch.total_bobot_kg || bobotKgNum);
                    let ikName = 'Ikan Nila';
                    if (newBatch && newBatch.ikan && newBatch.ikan.nama_ikan) {
                        ikName = newBatch.ikan.nama_ikan;
                    } else if (newBatch && newBatch.jenis_ikan) {
                        ikName = newBatch.jenis_ikan;
                    } else if (this.selectedIkanId) {
                        const foundIk = this.ikansList.find(i => String(i.id_ikan) === String(this.selectedIkanId));
                        if (foundIk) ikName = foundIk.nama_ikan;
                    }
                    this.batches.unshift({
                        id_batch: newBatch.id_batch,
                        id: '#BT-' + String(newBatch.id_batch).padStart(5, '0'),
                        id_ikan: newBatch.id_ikan || (this.selectedIkanId ? Number(this.selectedIkanId) : null),
                        jenis_ikan: ikName,
                        inputDate: new Date().toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' }),
                        tglPemijahan: this.form.tglPemijahan,
                        est_prcs_pembibitaan: this.form.est_prcs_pembibitaan ? new Date(this.form.est_prcs_pembibitaan).toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' }) : '-',
                        est_prcs_raw: this.form.est_prcs_pembibitaan,
                        fase: faseVal,
                        faseClass: this.getFaseClass(faseVal),
                        usia: diffDays + ' Hari',
                        usiaDays: diffDays,
                        jumlahBibitAwal: bibitAwalNum,
                        jumlahKematian: matiNum,
                        jumlah: sisaBibit.toLocaleString('id-ID'),
                        jumlahRaw: sisaBibit,
                        totalBobotKg: finalBobot,
                        totalBobotFormat: finalBobot.toLocaleString('id-ID', { minimumFractionDigits: 1, maximumFractionDigits: 2 }) + ' kg',
                        status: rawStatus,
                        statusLabel: statusLabel,
                        statusClass: statusClass,
                        dotClass: dotClass,
                        kolam: this.form.kolam,
                        phAir: '-'
                    });

                    this.showForm = false;
                    this.resetForm();
                    this.toastMessage = data.message || 'Data batch pembibitan berhasil disimpan!';
                    this.showToast = true;
                    setTimeout(() => { this.showToast = false; }, 4000);
                } else {
                    alert(data.message || 'Gagal menyimpan data batch.');
                }
            } catch (err) {
                alert('Gagal menyimpan data batch.');
            } finally {
                this.isSubmitting = false;
            }
        },

        openDetail(item) {
            this.selectedBatch = item;
            this.detailModalOpen = true;
        },

        deleteBatch(item) {
            this.selectedBatchToDelete = item;
            this.deleteModalOpen = true;
        },

        async markBatchFinished(item) {
            const rawId = item.id_batch || item.id.replace(/[^0-9]/g, '');
            const res = await AppSwal.confirm({
                title: 'Selesaikan Masa Pembibitan?',
                text: 'Apakah Anda yakin ingin menyelesaikan masa pembibitan untuk batch "' + item.id + '" agar siap dipindahkan ke kolam pembesaran?',
                confirmText: 'Ya, Selesaikan',
                cancelText: 'Batal',
                icon: 'question'
            });
            if (!res.isConfirmed) {
                return;
            }
            
            this.isSubmitting = true;
            try {
                const res = await fetch('/pembibitan/' + rawId, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        status: 'selesai',
                        fase_pertumbuhan: 'FINGERLING'
                    })
                });
                const data = await res.json();
                if (res.ok && data.success) {
                    const targetIdx = this.batches.findIndex(b => b.id === item.id || b.id_batch === rawId);
                    if (targetIdx !== -1) {
                        this.batches[targetIdx].status = 'selesai';
                        this.batches[targetIdx].fase = 'FINGERLING';
                        this.batches[targetIdx].faseClass = this.getFaseClass('FINGERLING');
                        this.batches[targetIdx].statusLabel = 'Selesai';
                        this.batches[targetIdx].statusClass = 'bg-slate-100 text-slate-700';
                        this.batches[targetIdx].dotClass = 'bg-slate-500';
                    }
                    this.toastMessage = 'Batch ' + item.id + ' telah diselesaikan dan siap dipindahkan ke pembesaran!';
                    this.showToast = true;
                    setTimeout(() => { this.showToast = false; }, 4000);
                } else {
                    alert(data.message || 'Gagal menyelesaikan batch pembibitan.');
                }
            } catch (e) {
                alert('Terjadi kesalahan saat menyelesaikan batch.');
            } finally {
                this.isSubmitting = false;
            }
        },

        async executeDeleteBatch() {
            if (!this.selectedBatchToDelete) return;
            const item = this.selectedBatchToDelete;
            const id = item.id;
            const rawId = item.id_batch || id.replace(/[^0-9]/g, '');

            try {
                await fetch('/pembibitan/' + rawId, {
                    method: 'DELETE',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                });
            } catch (e) {}

            this.batches = this.batches.filter(b => b.id !== id);
            this.deleteModalOpen = false;
            this.toastMessage = 'Data batch ' + id + ' berhasil dihapus!';
            this.showToast = true;
            setTimeout(() => { this.showToast = false; }, 3500);
            this.selectedBatchToDelete = null;
        },

        fillBySinglePondCapacity() {
            if (!this.selectedBatchToTransfer) return;
            const cap = this.singlePondCapacity;
            if (cap <= 0) return;
            const idealBibit = Math.min(this.sisaBibitTersedia, cap);
            this.transferForm.jumlah_bibit_transfer = idealBibit;
            this.onSingleBibitChange();
        },

        fillAllBibitSingle() {
            this.transferForm.jumlah_bibit_transfer = this.sisaBibitTersedia;
            this.onSingleBibitChange();
        },

        onSingleBibitChange() {
            if (!this.selectedBatchToTransfer) return;
            const item = this.selectedBatchToTransfer;
            const bibit = Number(this.transferForm.jumlah_bibit_transfer || 0);
            const sisaAwal = this.sisaBibitTersedia;
            const totalBobot = Number(item.totalBobotKg || 0);
            const avgEkorPerKg = Number(item.avg_ekor_per_kg || 4.0) > 0 ? Number(item.avg_ekor_per_kg || 4.0) : 4.0;

            if (sisaAwal > 0 && totalBobot > 0) {
                this.transferForm.biomassa_est = Number(((bibit / sisaAwal) * totalBobot).toFixed(2));
            } else {
                this.transferForm.biomassa_est = Math.max(0.1, Number((bibit * 0.0005).toFixed(2)));
            }

            this.transferForm.target_panen_kg = Math.max(1, Math.round((bibit * 0.85) / avgEkorPerKg));
        },

        fillMultiByCapacity() {
            const totalCap = this.multiTotalCapacity;
            if (totalCap > 0) {
                this.multiTotalBibitTransfer = Math.min(this.sisaBibitTersedia, totalCap);
            }
        },

        openTransferModal(item) {
            this.selectedBatchToTransfer = item;
            this.transferMode = 'single';
            this.riskAcknowledged = false;
            this.multiSelectedPonds = [];
            
            const sisaBibit = Number(item.jumlahRaw !== undefined ? item.jumlahRaw : ((item.jumlahBibitAwal || 0) - (item.jumlahKematian || 0))) || 1000;
            const avgEkorPerKg = Number(item.avg_ekor_per_kg || 4.0) > 0 ? Number(item.avg_ekor_per_kg || 4.0) : 4.0;
            const totalBobot = Number(item.totalBobotKg || 0);
            
            const firstPond = (this.kolamPembesaranList && this.kolamPembesaranList.length > 0) ? this.kolamPembesaranList[0] : null;
            const firstCap = firstPond ? Number(firstPond.kapasitas || 2500) : 2500;
            
            const idealBibit = Math.min(sisaBibit, firstCap);
            const initialBibit = idealBibit > 0 ? idealBibit : sisaBibit;
            
            const autoTargetPanen = Math.max(1, Math.round((initialBibit * 0.85) / avgEkorPerKg));
            const estBiomassa = (sisaBibit > 0 && totalBobot > 0) ? Number(((initialBibit / sisaBibit) * totalBobot).toFixed(2)) : Math.max(0.1, Number((initialBibit * 0.0005).toFixed(2)));
            
            const harvestMonths = Number(item.bulan_panen_max || 3.0);
            const harvestDays = Math.max(30, Math.round(harvestMonths * 30));
            const estHarvestDate = new Date(Date.now() + harvestDays * 24 * 60 * 60 * 1000).toISOString().split('T')[0];

            this.transferForm = {
                id_kolam_pembesaran: firstPond ? firstPond.id_kolam : '',
                jumlah_bibit_transfer: initialBibit,
                biomassa_est: estBiomassa,
                target_panen_kg: autoTargetPanen,
                est_tgl_panen: estHarvestDate,
                jumlah_mati_transfer: 0
            };

            if (this.kolamPembesaranList && this.kolamPembesaranList.length > 0) {
                this.multiSelectedPonds = this.kolamPembesaranList.slice(0, Math.min(3, this.kolamPembesaranList.length)).map(k => k.id_kolam);
            }
            
            // Default multi distribution matches total capacity of selected ponds
            const initMultiCap = this.multiTotalCapacity;
            this.multiTotalBibitTransfer = initMultiCap > 0 ? Math.min(sisaBibit, initMultiCap) : sisaBibit;

            this.transferModalOpen = true;
        },

        async submitTransfer() {
            if (!this.selectedBatchToTransfer) return;
            const item = this.selectedBatchToTransfer;
            const rawId = item.id_batch || item.id.replace(/[^0-9]/g, '');

            let payload = {};

            if (this.transferMode === 'single') {
                if (!this.transferForm.id_kolam_pembesaran) {
                    alert('Silakan pilih Kolam Pembesaran tujuan!');
                    return;
                }
                const trBibit = Number(this.transferForm.jumlah_bibit_transfer || 0);
                if (trBibit <= 0) {
                    alert('Jumlah bibit yang dipindahkan harus lebih dari 0!');
                    return;
                }
                if (trBibit > this.sisaBibitTersedia) {
                    alert('Jumlah bibit yang dipindahkan melebihi sisa bibit yang tersedia (' + this.sisaBibitTersedia.toLocaleString('id-ID') + ' ekor)!');
                    return;
                }
                if (this.isSingleOvercapacity && !this.riskAcknowledged) {
                    alert('Peringatan: Kepadatan kolam melebihi kapasitas rekomendasi! Silakan centang persetujuan risiko overcapacity sebelum melanjutkan.');
                    return;
                }

                payload = {
                    mode: 'single',
                    id_kolam_pembesaran: this.transferForm.id_kolam_pembesaran,
                    jumlah_bibit_transfer: trBibit,
                    jumlah_mati_transfer: Number(this.transferForm.jumlah_mati_transfer || 0),
                    target_panen_kg: this.transferForm.target_panen_kg,
                    biomassa_est: this.transferForm.biomassa_est,
                    est_tgl_panen: this.transferForm.est_tgl_panen
                };
            } else {
                // Multi mode
                if (this.multiSelectedPonds.length === 0) {
                    alert('Silakan pilih minimal 1 kolam pembesaran tujuan untuk auto-distribusi!');
                    return;
                }
                const trBibit = Number(this.multiTotalBibitTransfer || 0);
                if (trBibit <= 0) {
                    alert('Total bibit yang didistribusikan harus lebih dari 0!');
                    return;
                }
                if (trBibit > this.sisaBibitTersedia) {
                    alert('Total bibit yang didistribusikan melebihi sisa bibit yang tersedia (' + this.sisaBibitTersedia.toLocaleString('id-ID') + ' ekor)!');
                    return;
                }
                if (this.isMultiOvercapacity && !this.riskAcknowledged) {
                    alert('Peringatan: Satu atau lebih kolam mengalami overcapacity! Silakan centang persetujuan risiko overcapacity sebelum melanjutkan.');
                    return;
                }

                payload = {
                    mode: 'multi',
                    jumlah_mati_transfer: Number(this.transferForm.jumlah_mati_transfer || 0),
                    allocations: this.multiAllocations.map(a => ({
                        id_kolam: a.id_kolam,
                        bibit_ekor: a.bibit_ekor,
                        biomassa_kg: a.biomassa_kg,
                        target_panen_kg: a.target_panen_kg,
                        est_tgl_panen: a.est_tgl_panen
                    }))
                };
            }

            this.isSubmitting = true;
            try {
                const res = await fetch('/pembibitan/' + rawId + '/transfer', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify(payload)
                });

                const data = await res.json();
                if (res.ok && data.success) {
                    const targetIdx = this.batches.findIndex(b => b.id === item.id || b.id_batch === rawId);
                    if (targetIdx !== -1) {
                        const sisaAkhir = data.sisa_bibit_akhir !== undefined ? data.sisa_bibit_akhir : 0;
                        this.batches[targetIdx].jumlahRaw = sisaAkhir;
                        this.batches[targetIdx].jumlah = sisaAkhir.toLocaleString('id-ID');
                        
                        if (data.is_completed || sisaAkhir <= 0) {
                            this.batches[targetIdx].status = 'selesai';
                            this.batches[targetIdx].fase = 'FINGERLING';
                            this.batches[targetIdx].faseClass = this.getFaseClass('FINGERLING');
                            this.batches[targetIdx].statusLabel = 'Selesai (Dipindahkan)';
                            this.batches[targetIdx].statusClass = 'bg-slate-100 text-slate-700';
                            this.batches[targetIdx].dotClass = 'bg-slate-500';
                        } else {
                            this.batches[targetIdx].status = 'siap_pindah';
                            this.batches[targetIdx].statusLabel = 'Siap Pindah (Sebagian Selesai)';
                            this.batches[targetIdx].statusClass = 'bg-teal-100 text-teal-700';
                            this.batches[targetIdx].dotClass = 'bg-teal-500';
                        }
                    }

                    this.transferModalOpen = false;
                    this.toastMessage = data.message || 'Batch berhasil dipindahkan ke Pembesaran!';
                    this.showToast = true;
                    setTimeout(() => { this.showToast = false; }, 4500);
                } else {
                    alert(data.message || 'Gagal memindahkan batch ke pembesaran.');
                }
            } catch (e) {
                alert('Terjadi kesalahan saat memindahkan batch.');
            } finally {
                this.isSubmitting = false;
            }
        }
    };
}
</script>
@endpush

@extends('layouts.app')

@section('title', 'Log Pakan Harian - SIM-BUDIDAYA')

@section('content')
<div class="space-y-6" x-data="logPakanComponent()">

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

    <!-- Alert jika tidak ada Kolam Pembesaran yang Aktif -->
    <template x-if="activeKolams.length === 0">
        <div class="p-5 bg-amber-50 border border-amber-200 rounded-2xl flex items-start gap-4 text-amber-900 shadow-xs">
            <div class="w-10 h-10 rounded-xl bg-amber-100 flex items-center justify-center shrink-0 text-amber-700 text-lg">
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
                        PILIH KOLAM PEMBESARAN (HANYA BATCH AKTIF) <span class="text-rose-500">*</span>
                    </label>
                    <select x-model="form.id_kolam" @change="onKolamChange()" 
                            class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs font-semibold text-slate-800 bg-slate-50/70 focus:bg-white focus:outline-none focus:ring-2 focus:ring-sky-500 transition-all cursor-pointer">
                        <option value="">Pilih Kolam Pembesaran Aktif...</option>
                        <template x-for="k in activeKolams" :key="k.id_kolam">
                            <option :value="k.id_kolam" x-text="k.label"></option>
                        </template>
                    </select>
                </div>

                <div>
                    <label class="text-[10px] font-extrabold uppercase tracking-wider text-slate-500 block mb-1.5">
                        TANGGAL PEMBERIAN PAKAN <span class="text-rose-500">*</span>
                    </label>
                    <input type="date" x-model="form.tgl_log"
                           class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs font-semibold text-slate-800 bg-slate-50/70 focus:bg-white focus:outline-none focus:ring-2 focus:ring-sky-500 transition-all">
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
                            PAKAN PELET KOMERSIAL (KG) <span class="text-rose-500">*</span>
                        </label>
                        <div class="flex items-center gap-2">
                            <input type="number" x-model="form.kg_pelet"
                                onkeydown="if(event.key === '-' || event.key === 'e' || event.key === 'E') event.preventDefault()"
                                @input="if(form.kg_pelet !== '' && Number(form.kg_pelet) < 0) form.kg_pelet = Math.abs(Number(form.kg_pelet)) || 0; recalculateCost()"
                                step="0.1" min="0" placeholder="0.0"
                                class="flex-1 px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs font-extrabold text-slate-900 bg-white focus:outline-none focus:ring-2 focus:ring-sky-500 transition-all">
                            <span class="text-xs font-extrabold text-slate-400 px-1">KG</span>
                        </div>
                        <span class="text-[10px] text-slate-400 font-medium block">Standar pakan protein tinggi / apung.</span>
                    </div>

                    <!-- Pakan Dedaunan Box -->
                    <div class="bg-slate-50/80 p-4 rounded-xl border border-slate-100 space-y-2">
                        <label class="text-[10px] font-extrabold uppercase tracking-wider text-slate-500 block">
                            PAKAN DEDAUNAN ORGANIK (KG)
                        </label>
                        <div class="flex items-center gap-2">
                            <div class="w-2/5 flex items-center gap-1.5">
                                <input type="number" x-model="form.kg_daun"
                                       onkeydown="if(event.key === '-' || event.key === 'e' || event.key === 'E') event.preventDefault()"
                                       @input="if(form.kg_daun !== '' && Number(form.kg_daun) < 0) form.kg_daun = Math.abs(Number(form.kg_daun)) || 0"
                                       step="0.1" min="0" placeholder="0.0"
                                       class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs font-extrabold text-slate-900 bg-white focus:outline-none focus:ring-2 focus:ring-sky-500 transition-all">
                                <span class="text-xs font-extrabold text-slate-400">KG</span>
                            </div>
                            <select x-model="form.jenis_daun" class="flex-1 px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs font-semibold text-slate-700 bg-white focus:outline-none focus:ring-2 focus:ring-sky-500 transition-all cursor-pointer">
                                <option value="">Pilih Jenis Daun...</option>
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
                        <div class="flex items-center rounded-xl border border-slate-200 bg-slate-50/70 overflow-hidden focus-within:bg-white focus-within:ring-2 focus-within:ring-sky-500 focus-within:border-sky-500 transition-all">
                            <span class="px-3.5 py-2.5 text-xs font-extrabold text-slate-500 bg-slate-100/80 border-r border-slate-200 shrink-0">Rp</span>
                            <input type="number" x-model="form.total_biaya" min="0"
                                   onkeydown="if(event.key === '-' || event.key === 'e' || event.key === 'E') event.preventDefault()"
                                   @input="if(form.total_biaya !== '' && Number(form.total_biaya) < 0) form.total_biaya = Math.abs(Number(form.total_biaya)) || 0"
                                   class="w-full px-3.5 py-2.5 text-xs font-extrabold text-slate-900 bg-transparent border-0 focus:outline-none">
                        </div>
                    </div>

                    <div>
                        <label class="text-[10px] font-extrabold uppercase tracking-wider text-slate-500 block mb-1.5">
                            PH AIR KOLAM
                        </label>
                        <div class="flex items-center rounded-xl border border-slate-200 bg-slate-50/70 overflow-hidden focus-within:bg-white focus-within:ring-2 focus-within:ring-sky-500 focus-within:border-sky-500 transition-all">
                            <input type="number" step="0.1" min="0" max="14" x-model="form.ph_air" placeholder="7.2"
                                   onkeydown="if(event.key === '-' || event.key === 'e' || event.key === 'E') event.preventDefault()"
                                   @input="if(form.ph_air !== '' && Number(form.ph_air) < 0) form.ph_air = 0"
                                   class="w-full px-3.5 py-2.5 text-xs font-semibold text-slate-800 bg-transparent border-0 focus:outline-none">
                            <span class="px-3.5 py-2.5 text-xs font-extrabold text-slate-500 bg-slate-100/80 border-l border-slate-200 shrink-0">pH</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
                <button type="button" @click="resetForm()" class="px-5 py-2.5 rounded-xl border border-slate-200 text-xs font-bold text-slate-600 hover:bg-slate-50 transition-colors cursor-pointer">
                    Reset
                </button>
                <button type="submit" 
                        :disabled="isSubmitting"
                        class="px-6 py-2.5 rounded-xl bg-[#0284C7] hover:bg-sky-600 active:scale-95 text-white font-extrabold text-xs shadow-md shadow-sky-600/20 transition-all flex items-center gap-2 cursor-pointer">
                    <i class="fa-solid fa-check text-xs"></i>
                    <span x-text="isSubmitting ? 'Menyimpan...' : 'Simpan Log Pakan'"></span>
                </button>
            </div>

        </form>

    </div>

    <!-- Tabel Riwayat Log Pakan dengan Filter Tanggal & Pagination -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden space-y-4 p-5">
        
        <!-- Filter Toolbar -->
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4 pb-4 border-b border-slate-100">
            <div>
                <h3 class="text-sm font-extrabold text-slate-900">Riwayat Pemberian Pakan</h3>
                <p class="text-xs text-slate-500 font-medium">Rekap pencatatan pakan dan kondisi pH air harian.</p>
            </div>

            <!-- Tanggal & Kolam Filters -->
            <div class="flex flex-wrap items-center gap-2.5">
                
                <!-- Quick Filter Period Buttons -->
                <div class="flex items-center p-1 bg-slate-100 rounded-xl text-[11px] font-extrabold text-slate-600">
                    <button type="button" 
                            @click="setQuickDateFilter('all')" 
                            :class="dateFilterType === 'all' ? 'bg-white text-slate-900 shadow-xs rounded-lg' : 'hover:text-slate-900'"
                            class="px-2.5 py-1 transition-all cursor-pointer">
                        Semua
                    </button>
                    <button type="button" 
                            @click="setQuickDateFilter('today')" 
                            :class="dateFilterType === 'today' ? 'bg-white text-slate-900 shadow-xs rounded-lg' : 'hover:text-slate-900'"
                            class="px-2.5 py-1 transition-all cursor-pointer">
                        Hari Ini
                    </button>
                    <button type="button" 
                            @click="setQuickDateFilter('7days')" 
                            :class="dateFilterType === '7days' ? 'bg-white text-slate-900 shadow-xs rounded-lg' : 'hover:text-slate-900'"
                            class="px-2.5 py-1 transition-all cursor-pointer">
                        7 Hari
                    </button>
                    <button type="button" 
                            @click="setQuickDateFilter('30days')" 
                            :class="dateFilterType === '30days' ? 'bg-white text-slate-900 shadow-xs rounded-lg' : 'hover:text-slate-900'"
                            class="px-2.5 py-1 transition-all cursor-pointer">
                        30 Hari
                    </button>
                </div>

                <!-- Custom Date Range -->
                <div class="flex items-center gap-1.5">
                    <input type="date" 
                           x-model="filterStartDate" 
                           @change="dateFilterType = 'custom'; currentPage = 1"
                           class="px-2.5 py-1.5 rounded-xl border border-slate-200 text-xs font-semibold text-slate-700 bg-slate-50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-sky-500 transition-all">
                    <span class="text-xs text-slate-400 font-bold">s/d</span>
                    <input type="date" 
                           x-model="filterEndDate" 
                           @change="dateFilterType = 'custom'; currentPage = 1"
                           class="px-2.5 py-1.5 rounded-xl border border-slate-200 text-xs font-semibold text-slate-700 bg-slate-50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-sky-500 transition-all">
                </div>

                <!-- Reset Filter Button -->
                <button type="button" 
                        x-show="filterStartDate || filterEndDate || dateFilterType !== 'all'"
                        @click="setQuickDateFilter('all')" 
                        class="px-2.5 py-1.5 rounded-xl border border-rose-200 text-rose-600 bg-rose-50 hover:bg-rose-100 text-xs font-bold transition-colors cursor-pointer"
                        title="Reset Filter Tanggal">
                    <i class="fa-solid fa-rotate-left text-[10px]"></i>
                    <span>Reset</span>
                </button>

            </div>
        </div>

        <!-- Table List -->
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50/80 text-[10px] uppercase font-extrabold text-slate-400 border-b border-slate-100">
                    <tr>
                        <th class="py-3.5 px-4">TANGGAL</th>
                        <th class="py-3.5 px-4">KOLAM PEMBESARAN</th>
                        <th class="py-3.5 px-4">PAKAN PELET</th>
                        <th class="py-3.5 px-4">PAKAN DAUN</th>
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
                                Tidak ada catatan log pakan yang cocok dengan filter tanggal yang dipilih.
                            </td>
                        </tr>
                    </template>

                    <template x-for="log in paginatedLogs" :key="log.id_pakan">
                        <tr class="hover:bg-slate-50/60 transition-colors">
                            <td class="py-3.5 px-4 font-extrabold text-slate-900" x-text="log.tgl_log"></td>
                            <td class="py-3.5 px-4">
                                <span class="font-extrabold text-[#0B2570]" x-text="log.kolam ? log.kolam.nama_kolam : 'Kolam #' + log.id_kolam"></span>
                            </td>
                            <td class="py-3.5 px-4 font-bold text-slate-800">
                                <span class="px-2 py-0.5 rounded-md bg-slate-100 font-extrabold" x-text="Number(log.kg_pelet).toFixed(1) + ' kg'"></span>
                            </td>
                            <td class="py-3.5 px-4">
                                <span x-text="Number(log.kg_daun) > 0 ? (Number(log.kg_daun).toFixed(1) + ' kg ' + (log.jenis_daun ? '(' + log.jenis_daun + ')' : '')) : '-'"></span>
                            </td>
                            <td class="py-3.5 px-4 font-extrabold text-emerald-700" x-text="'Rp ' + Number(log.total_biaya).toLocaleString('id-ID')"></td>
                            <td class="py-3.5 px-4">
                                <span class="px-2 py-0.5 rounded-md text-[10px] font-extrabold bg-sky-50 text-sky-800 border border-sky-100" x-text="'pH ' + (log.ph_air || '7.2')"></span>
                            </td>
                            <td class="py-3.5 px-4 text-right text-slate-500 font-semibold" x-text="log.user ? (log.user.nama || log.user.name) : 'Petugas'"></td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>

        <!-- Pagination Controls (1, 2, 3...) -->
        <div x-show="filteredLogs.length > 0" class="pt-3 border-t border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-3 text-xs">
            <span class="text-slate-500 font-medium">
                Menampilkan <strong class="text-slate-800" x-text="((currentPage - 1) * perPage) + 1"></strong> - <strong class="text-slate-800" x-text="Math.min(currentPage * perPage, filteredLogs.length)"></strong> dari <strong class="text-slate-800" x-text="filteredLogs.length"></strong> catatan
            </span>

            <div class="flex items-center gap-1" x-show="totalPages > 1">
                <!-- Prev Button -->
                <button type="button" 
                        @click="goToPage(currentPage - 1)" 
                        :disabled="currentPage === 1"
                        :class="currentPage === 1 ? 'text-slate-300 cursor-not-allowed' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900 cursor-pointer'"
                        class="w-8 h-8 rounded-lg flex items-center justify-center font-bold transition-all">
                    <i class="fa-solid fa-chevron-left text-xs"></i>
                </button>

                <!-- Page Number Buttons -->
                <template x-for="p in visiblePages" :key="p">
                    <button type="button" 
                            @click="goToPage(p)"
                            :class="currentPage === p ? 'bg-[#0284C7] text-white shadow-xs font-black' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900 font-semibold'"
                            class="w-8 h-8 rounded-lg flex items-center justify-center text-xs transition-all cursor-pointer"
                            x-text="p">
                    </button>
                </template>

                <!-- Next Button -->
                <button type="button" 
                        @click="goToPage(currentPage + 1)" 
                        :disabled="currentPage === totalPages"
                        :class="currentPage === totalPages ? 'text-slate-300 cursor-not-allowed' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900 cursor-pointer'"
                        class="w-8 h-8 rounded-lg flex items-center justify-center font-bold transition-all">
                    <i class="fa-solid fa-chevron-right text-xs"></i>
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
function logPakanComponent() {
    return {
        activeKolams: {!! json_encode($activeKolams ?? []) !!},
        logs: {!! json_encode($logs ?? []) !!},
        isSubmitting: false,
        showToast: false,
        toastMessage: '',

        // Pagination State
        currentPage: 1,
        perPage: 8,

        // Date Filter State
        dateFilterType: 'all', // 'all', 'today', '7days', '30days', 'custom'
        filterStartDate: '',
        filterEndDate: '',

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

        get filteredLogs() {
            let list = this.logs;

            if (this.filterStartDate) {
                list = list.filter(item => item.tgl_log >= this.filterStartDate);
            }
            if (this.filterEndDate) {
                list = list.filter(item => item.tgl_log <= this.filterEndDate);
            }

            return list;
        },

        get totalPages() {
            return Math.ceil(this.filteredLogs.length / this.perPage) || 1;
        },

        get paginatedLogs() {
            const start = (this.currentPage - 1) * this.perPage;
            return this.filteredLogs.slice(start, start + this.perPage);
        },

        get visiblePages() {
            const pages = [];
            const total = this.totalPages;
            for (let i = 1; i <= total; i++) {
                pages.push(i);
            }
            return pages;
        },

        goToPage(page) {
            if (page >= 1 && page <= this.totalPages) {
                this.currentPage = page;
            }
        },

        setQuickDateFilter(type) {
            this.dateFilterType = type;
            this.currentPage = 1;
            const now = new Date();

            if (type === 'all') {
                this.filterStartDate = '';
                this.filterEndDate = '';
            } else if (type === 'today') {
                const todayStr = now.toISOString().split('T')[0];
                this.filterStartDate = todayStr;
                this.filterEndDate = todayStr;
            } else if (type === '7days') {
                const d = new Date();
                d.setDate(d.getDate() - 7);
                this.filterStartDate = d.toISOString().split('T')[0];
                this.filterEndDate = now.toISOString().split('T')[0];
            } else if (type === '30days') {
                const d = new Date();
                d.setDate(d.getDate() - 30);
                this.filterStartDate = d.toISOString().split('T')[0];
                this.filterEndDate = now.toISOString().split('T')[0];
            }
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
            if (Number(this.form.kg_pelet || 0) < 0 || Number(this.form.kg_daun || 0) < 0) {
                alert('Jumlah pakan (pelet atau daun) tidak boleh berupa angka minus!');
                return;
            }
            if (Number(this.form.kg_pelet || 0) <= 0 && Number(this.form.kg_daun || 0) <= 0) {
                alert('Silakan masukkan jumlah pakan (pelet atau daun) lebih dari 0 kg!');
                return;
            }
            if (Number(this.form.total_biaya || 0) < 0) {
                alert('Estimasi biaya pakan tidak boleh berupa angka minus!');
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
                        kg_pelet: Math.abs(Number(this.form.kg_pelet) || 0),
                        kg_daun: Math.abs(Number(this.form.kg_daun) || 0),
                        jenis_daun: this.form.jenis_daun || null,
                        total_biaya: Math.abs(Number(this.form.total_biaya) || 0),
                        ph_air: Math.max(0, Number(this.form.ph_air) || 7.2)
                    })
                });

                const data = await res.json();
                if (res.ok && data.success) {
                    // Update state locally
                    if (data.log) {
                        this.logs.unshift(data.log);
                        this.currentPage = 1;
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

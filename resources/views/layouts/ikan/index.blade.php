@extends('layouts.app')

@section('title', 'Master Data Jenis Ikan - AMS BUDIDAYA')

@section('content')
<div class="space-y-6" x-data="ikanComponent()">

    <!-- Top Header Banner -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 bg-white p-6 rounded-2xl border border-slate-200/80 shadow-xs">
        <div>
            <div class="flex items-center gap-2 text-xs font-extrabold uppercase tracking-wider text-[#0284C7] mb-1">
                <i class="fa-solid fa-fish text-sm"></i>
                <span>Master Data &amp; Konfigurasi Standar SOP</span>
            </div>
            <h1 class="text-xl font-extrabold text-slate-900 tracking-tight">Manajemen Jenis Ikan &amp; Parameter Budidaya</h1>
            <p class="text-xs text-slate-500 font-medium mt-0.5">Kelola spesifikasi ikan, benchmark FCR ideal, target panen, dan alur siklus pembibitan.</p>
        </div>

        <div class="flex items-center gap-3">
            <button type="button" 
                    @click="openCreateForm()" 
                    class="px-4 py-2.5 rounded-xl bg-[#0284C7] hover:bg-sky-600 active:scale-95 text-white font-extrabold text-xs flex items-center gap-2 shadow-md shadow-sky-600/20 transition-all cursor-pointer">
                <i class="fa-solid fa-plus text-xs"></i>
                <span>Tambah Jenis Ikan</span>
            </button>
        </div>
    </div>

    <!-- 4 KPI Summary Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Card 1: Total Spesies -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs flex items-center gap-4">
            <div class="w-12 h-12 rounded-2xl bg-sky-50 text-[#0284C7] flex items-center justify-center shrink-0">
                <i class="fa-solid fa-fish-fins text-xl"></i>
            </div>
            <div>
                <span class="text-[10px] font-extrabold text-slate-400 uppercase tracking-wider block">TOTAL SPESIES IKAN</span>
                <span class="text-xl font-black text-slate-900 block" x-text="ikans.length"></span>
                <span class="text-[11px] font-bold text-sky-700 mt-0.5 block">Master Data Terdaftar</span>
            </div>
        </div>

        <!-- Card 2: Rata-rata Penetasan -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs flex items-center gap-4">
            <div class="w-12 h-12 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center shrink-0">
                <i class="fa-solid fa-egg text-xl"></i>
            </div>
            <div>
                <span class="text-[10px] font-extrabold text-slate-400 uppercase tracking-wider block">RATA-RATA PENETASAN</span>
                <span class="text-xl font-black text-slate-900 block" x-text="avgPenetasan">{{ $kpis['avgPenetasan'] ?? '3 Hari' }}</span>
                <span class="text-[11px] font-bold text-amber-600 mt-0.5 block">Masa Telur → Larva</span>
            </div>
        </div>

        <!-- Card 3: Rata-rata Pembibitan -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs flex items-center gap-4">
            <div class="w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0">
                <i class="fa-solid fa-seedling text-xl"></i>
            </div>
            <div>
                <span class="text-[10px] font-extrabold text-slate-400 uppercase tracking-wider block">RATA-RATA PEMBIBITAN</span>
                <span class="text-xl font-black text-slate-900 block" x-text="avgPembibitan">{{ $kpis['avgPembibitan'] ?? '21 Hari' }}</span>
                <span class="text-[11px] font-bold text-emerald-600 mt-0.5 block">Larva → Fingerling Matang</span>
            </div>
        </div>

        <!-- Card 4: Total Siklus Hatchery -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs flex items-center gap-4">
            <div class="w-12 h-12 rounded-2xl bg-indigo-50 text-indigo-600 flex items-center justify-center shrink-0">
                <i class="fa-solid fa-timeline text-xl"></i>
            </div>
            <div>
                <span class="text-[10px] font-extrabold text-slate-400 uppercase tracking-wider block">STANDAR FCR IDEAL</span>
                <span class="text-xl font-black text-slate-900 block">1,0 – 1,8</span>
                <span class="text-[11px] font-bold text-indigo-600 mt-0.5 block">Tergantung Spesies Ikan</span>
            </div>
        </div>
    </div>

    <!-- Form Tambah / Edit Jenis Ikan (Slide Down) -->
    <div x-show="showForm" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 -translate-y-4"
         x-transition:enter-end="opacity-100 translate-y-0"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 translate-y-0"
         x-transition:leave-end="opacity-0 -translate-y-4"
         class="bg-white rounded-2xl border-2 border-sky-400/80 p-6 shadow-xl space-y-6"
         style="display: none;">
        
        <div class="flex items-center justify-between pb-4 border-b border-slate-100">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-[#0284C7] text-white flex items-center justify-center">
                    <i class="fa-solid fa-sliders text-base"></i>
                </div>
                <div>
                    <h2 class="text-base font-extrabold text-slate-900" x-text="formMode === 'create' ? 'Tambah Jenis Ikan & Parameter Budidaya' : 'Edit Data Spesies Ikan'"></h2>
                    <p class="text-xs text-slate-500 font-medium">Tentukan parameter nama varietas, patokan FCR ideal, target panen, dan SOP pembibitan.</p>
                </div>
            </div>
            <button type="button" :disabled="isSubmitting" @click="showForm = false; resetForm();" class="text-slate-400 hover:text-slate-600 text-lg p-1 disabled:opacity-50 cursor-pointer">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>

        <form @submit.prevent="submitIkan()" class="space-y-6">
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                <!-- Field 1: Nama Ikan -->
                <div class="space-y-1.5">
                    <label class="text-[11px] font-extrabold uppercase tracking-wider text-slate-600 block">
                        NAMA JENIS / SPESIES IKAN <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" 
                           x-model="form.nama_ikan" 
                           :disabled="isSubmitting"
                           placeholder="Contoh: Ikan Lele / Ikan Nila" 
                           required
                           class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs font-semibold text-slate-800 bg-slate-50/70 focus:bg-white focus:outline-none focus:ring-2 focus:ring-sky-500 disabled:opacity-60 disabled:cursor-not-allowed transition-all">
                    <p class="text-[10px] text-slate-400 font-medium">Varietas komoditas budidaya.</p>
                </div>

                <!-- Field 2: Durasi Penetasan -->
                <div class="space-y-1.5">
                    <label class="text-[11px] font-extrabold uppercase tracking-wider text-slate-600 block">
                        DURASI MASA PENETASAN (HARI) <span class="text-rose-500">*</span>
                    </label>
                    <div class="flex items-center gap-2">
                        <button type="button" :disabled="isSubmitting" @click="form.durasi_penetasan = Math.max(1, Number(form.durasi_penetasan) - 1)" class="w-9 h-9 rounded-xl border border-slate-200 bg-white text-slate-500 hover:bg-slate-50 flex items-center justify-center font-bold text-sm disabled:opacity-50 disabled:cursor-not-allowed">−</button>
                        <input type="number" 
                               x-model="form.durasi_penetasan" 
                               :disabled="isSubmitting"
                               min="1" 
                               max="90"
                               required
                               class="flex-1 px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs font-extrabold text-center text-amber-700 bg-amber-50/50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-amber-500 disabled:opacity-60 disabled:cursor-not-allowed transition-all">
                        <button type="button" :disabled="isSubmitting" @click="form.durasi_penetasan = Number(form.durasi_penetasan) + 1" class="w-9 h-9 rounded-xl border border-slate-200 bg-white text-slate-500 hover:bg-slate-50 flex items-center justify-center font-bold text-sm disabled:opacity-50 disabled:cursor-not-allowed">+</button>
                    </div>
                    <p class="text-[10px] text-slate-400 font-medium">Masa inkubasi dari butir telur hingga menetas.</p>
                </div>

                <!-- Field 3: Durasi Pembibitan -->
                <div class="space-y-1.5">
                    <label class="text-[11px] font-extrabold uppercase tracking-wider text-slate-600 block">
                        DURASI MASA PEMBIBITAN (HARI) <span class="text-rose-500">*</span>
                    </label>
                    <div class="flex items-center gap-2">
                        <button type="button" :disabled="isSubmitting" @click="form.durasi_pembibitan = Math.max(1, Number(form.durasi_pembibitan) - 1)" class="w-9 h-9 rounded-xl border border-slate-200 bg-white text-slate-500 hover:bg-slate-50 flex items-center justify-center font-bold text-sm disabled:opacity-50 disabled:cursor-not-allowed">−</button>
                        <input type="number" 
                               x-model="form.durasi_pembibitan" 
                               :disabled="isSubmitting"
                               min="1" 
                               max="180"
                               required
                               class="flex-1 px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs font-extrabold text-center text-emerald-700 bg-emerald-50/50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500 disabled:opacity-60 disabled:cursor-not-allowed transition-all">
                        <button type="button" :disabled="isSubmitting" @click="form.durasi_pembibitan = Number(form.durasi_pembibitan) + 1" class="w-9 h-9 rounded-xl border border-slate-200 bg-white text-slate-500 hover:bg-slate-50 flex items-center justify-center font-bold text-sm disabled:opacity-50 disabled:cursor-not-allowed">+</button>
                    </div>
                    <p class="text-[10px] text-slate-400 font-medium">Masa pemeliharaan larva hingga siap tebar.</p>
                </div>
            </div>

            <!-- Row 2: FCR Benchmark & Spesifikasi Panen -->
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4 p-4 bg-sky-50/40 rounded-2xl border border-sky-100">
                <!-- Field 4: FCR Ideal (Min - Max) -->
                <div class="space-y-1.5">
                    <label class="text-[11px] font-extrabold uppercase tracking-wider text-slate-700 block">
                        PATOKAN FCR IDEAL (MIN - MAX)
                    </label>
                    <div class="grid grid-cols-2 gap-2">
                        <input type="number" step="0.01" min="0.1" max="5.0"
                               x-model="form.fcr_min" 
                               :disabled="isSubmitting"
                               placeholder="Min (1.00)" 
                               class="px-3 py-2 rounded-xl border border-slate-200 text-xs font-bold text-center text-sky-800 bg-white focus:ring-2 focus:ring-sky-500 transition-all">
                        <input type="number" step="0.01" min="0.1" max="5.0"
                               x-model="form.fcr_max" 
                               :disabled="isSubmitting"
                               placeholder="Max (1.20)" 
                               class="px-3 py-2 rounded-xl border border-slate-200 text-xs font-bold text-center text-sky-800 bg-white focus:ring-2 focus:ring-sky-500 transition-all">
                    </div>
                    <p class="text-[10px] text-slate-400 font-medium">Standar efisiensi pakan ikan.</p>
                </div>

                <!-- Field 5: Bulan Panen (Min - Max) -->
                <div class="space-y-1.5">
                    <label class="text-[11px] font-extrabold uppercase tracking-wider text-slate-700 block">
                        SIKLUS PANEN (BULAN)
                    </label>
                    <div class="grid grid-cols-2 gap-2">
                        <input type="number" step="0.1" min="0.5" max="36"
                               x-model="form.bulan_panen_min" 
                               :disabled="isSubmitting"
                               placeholder="Min (2.5)" 
                               class="px-3 py-2 rounded-xl border border-slate-200 text-xs font-bold text-center text-indigo-800 bg-white focus:ring-2 focus:ring-indigo-500 transition-all">
                        <input type="number" step="0.1" min="0.5" max="36"
                               x-model="form.bulan_panen_max" 
                               :disabled="isSubmitting"
                               placeholder="Max (3.0)" 
                               class="px-3 py-2 rounded-xl border border-slate-200 text-xs font-bold text-center text-indigo-800 bg-white focus:ring-2 focus:ring-indigo-500 transition-all">
                    </div>
                    <p class="text-[10px] text-slate-400 font-medium">Estimasi lama tebar s.d panen.</p>
                </div>

                <!-- Field 6: Target Konsumsi -->
                <div class="space-y-1.5">
                    <label class="text-[11px] font-extrabold uppercase tracking-wider text-slate-700 block">
                        TARGET KONSUMSI
                    </label>
                    <input type="text" 
                           x-model="form.target_konsumsi" 
                           :disabled="isSubmitting"
                           placeholder="Contoh: 8–10 ekor / kg" 
                           class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs font-semibold text-slate-800 bg-white focus:ring-2 focus:ring-sky-500 transition-all">
                    <p class="text-[10px] text-slate-400 font-medium">Ukuran standar panen siap jual.</p>
                </div>

                <!-- Field 7: Rekomendasi Pakan -->
                <div class="space-y-1.5">
                    <label class="text-[11px] font-extrabold uppercase tracking-wider text-slate-700 block">
                        REKOMENDASI PAKAN
                    </label>
                    <input type="text" 
                           x-model="form.jenis_pakan_didukung" 
                           :disabled="isSubmitting"
                           placeholder="Pelet + Vitamin + Daun" 
                           class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs font-semibold text-slate-800 bg-white focus:ring-2 focus:ring-sky-500 transition-all">
                    <p class="text-[10px] text-slate-400 font-medium">Nutrisi pakan pendukung.</p>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="flex items-center justify-end gap-3 pt-2">
                <button type="button" 
                        :disabled="isSubmitting"
                        @click="showForm = false; resetForm();" 
                        class="px-4 py-2.5 rounded-xl border border-slate-200 text-slate-600 hover:bg-slate-50 font-bold text-xs transition-colors cursor-pointer disabled:opacity-50 disabled:cursor-not-allowed">
                    Batal
                </button>
                <button type="submit" 
                        :disabled="isSubmitting"
                        class="px-5 py-2.5 rounded-xl bg-[#0284C7] hover:bg-sky-600 active:scale-95 text-white font-extrabold text-xs shadow-md shadow-sky-600/20 transition-all flex items-center gap-2 cursor-pointer disabled:opacity-60 disabled:cursor-not-allowed">
                    <i :class="isSubmitting ? 'fa-solid fa-circle-notch fa-spin text-xs' : 'fa-solid fa-check text-xs'"></i>
                    <span x-text="isSubmitting ? 'Menyimpan...' : (formMode === 'create' ? 'Simpan Jenis Ikan' : 'Perbarui Spesies')"></span>
                </button>
            </div>

        </form>

    </div>

    <!-- Data Table Card -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
        
        <!-- Table Toolbar -->
        <div class="p-5 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h3 class="text-sm font-extrabold text-slate-900">Daftar Varietas, Standar FCR &amp; Siklus Panen</h3>
                <p class="text-xs text-slate-500 font-medium">Standar acuan budidaya yang digunakan untuk evaluasi efisiensi pakan dan estimasi panen.</p>
            </div>
            
            <div class="flex items-center gap-3">
                <div class="relative w-full sm:w-64">
                    <i class="fa-solid fa-magnifying-glass absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                    <input type="text" 
                           x-model="searchQuery" 
                           placeholder="Cari jenis ikan..." 
                           class="w-full pl-9 pr-3.5 py-2 rounded-xl border border-slate-200 text-xs font-semibold text-slate-700 bg-slate-50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-sky-500 transition-all">
                </div>
            </div>
        </div>

        <!-- Table List -->
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50/70 border-b border-slate-200/80 text-[10px] font-extrabold text-slate-400 uppercase tracking-wider">
                        <th class="py-4 px-6">SPESIES IKAN</th>
                        <th class="py-4 px-6">FCR IDEAL (TARGET)</th>
                        <th class="py-4 px-6">SIKLUS PANEN</th>
                        <th class="py-4 px-6">TARGET KONSUMSI &amp; PAKAN</th>
                        <th class="py-4 px-6">DURASI HATCHERY</th>
                        <th class="py-4 px-6 text-right">AKSI</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-xs font-medium text-slate-700">
                    
                    <template x-if="filteredIkans.length === 0">
                        <tr>
                            <td colspan="6" class="py-12 text-center text-slate-400 text-xs font-medium">
                                <i class="fa-solid fa-fish text-3xl text-slate-300 block mb-2"></i>
                                Belum ada data jenis ikan yang terdaftar.<br>
                                <span class="text-[11px] text-slate-400">Klik tombol <strong>Tambah Jenis Ikan</strong> untuk menambahkan data baru.</span>
                            </td>
                        </tr>
                    </template>

                    <template x-for="item in filteredIkans" :key="item.id_ikan">
                        <tr class="hover:bg-slate-50/70 transition-colors">
                            <td class="py-4 px-6">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 rounded-xl bg-sky-100 text-[#0284C7] flex items-center justify-center font-extrabold text-xs shrink-0">
                                        <i class="fa-solid fa-fish"></i>
                                    </div>
                                    <div>
                                        <span class="font-extrabold text-slate-900 block" x-text="item.nama_ikan"></span>
                                        <span class="text-[10px] text-slate-400 font-medium" x-text="'ID: #IK-' + String(item.id_ikan).padStart(4, '0')"></span>
                                    </div>
                                </div>
                            </td>
                            <td class="py-4 px-6">
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[11px] font-black bg-emerald-50 text-emerald-800 border border-emerald-200/60 shadow-xs">
                                    <i class="fa-solid fa-scale-balanced text-[10px] text-emerald-600"></i>
                                    <span x-text="(item.fcr_min ? Number(item.fcr_min).toFixed(1) : '1.0') + ' – ' + (item.fcr_max ? Number(item.fcr_max).toFixed(1) : '1.2')"></span>
                                </span>
                            </td>
                            <td class="py-4 px-6">
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-extrabold bg-indigo-50 text-indigo-700 border border-indigo-200/60">
                                    <i class="fa-regular fa-calendar-check text-[10px]"></i>
                                    <span x-text="item.bulan_panen_min ? (item.bulan_panen_min + ' – ' + item.bulan_panen_max + ' Bulan') : '3 – 4 Bulan'"></span>
                                </span>
                            </td>
                            <td class="py-4 px-6">
                                <div>
                                    <span class="font-bold text-slate-800 block text-[11px]" x-text="item.target_konsumsi || '3–5 ekor / kg'"></span>
                                    <span class="text-[10px] text-slate-500 font-medium block" x-text="item.jenis_pakan_didukung || 'Pelet + Vitamin'"></span>
                                </div>
                            </td>
                            <td class="py-4 px-6">
                                <div class="space-y-0.5">
                                    <span class="text-[10px] text-amber-700 font-bold block" x-text="'Penetasan: ' + item.durasi_penetasan + 'h'"></span>
                                    <span class="text-[10px] text-emerald-700 font-bold block" x-text="'Pembibitan: ' + item.durasi_pembibitan + 'h'"></span>
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
                                                 const menuH = 110;
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
                                         class="w-44 rounded-xl bg-white border border-slate-200 shadow-2xl py-1.5 text-left"
                                         style="display: none;">
                                        <button @click="open = false; openEditForm(item)" class="w-full px-3.5 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-50 flex items-center gap-2.5 transition-colors cursor-pointer">
                                            <i class="fa-solid fa-pen-to-square text-sky-600 w-4"></i>
                                            <span>Edit Spesies</span>
                                        </button>
                                        <div class="my-1 border-t border-slate-100"></div>
                                        <button @click="open = false; confirmDelete(item)" class="w-full px-3.5 py-2 text-xs font-semibold text-rose-600 hover:bg-rose-50 flex items-center gap-2.5 transition-colors cursor-pointer">
                                            <i class="fa-solid fa-trash-can text-rose-600 w-4"></i>
                                            <span>Hapus Spesies</span>
                                        </button>
                                    </div>
                                </div>
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
         x-transition:enter-start="opacity-0 translate-y-2"
         x-transition:enter-end="opacity-100 translate-y-0"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 translate-y-0"
         x-transition:leave-end="opacity-0 translate-y-2"
         class="fixed bottom-5 right-5 z-50 bg-[#051B44] text-white px-5 py-3.5 rounded-2xl shadow-2xl border border-sky-400/30 flex items-center gap-3 text-xs font-bold"
         style="display: none;">
        <i class="fa-solid fa-circle-check text-sky-400 text-sm"></i>
        <span x-text="toastMessage"></span>
    </div>

</div>

<script>
function ikanComponent() {
    return {
        showForm: false,
        formMode: 'create',
        searchQuery: '',
        isSubmitting: false,
        showToast: false,
        toastMessage: '',

        form: {
            id_ikan: null,
            nama_ikan: '',
            durasi_penetasan: 3,
            durasi_pembibitan: 21,
            fcr_min: 1.00,
            fcr_max: 1.20,
            bulan_panen_min: 2.5,
            bulan_panen_max: 3.0,
            target_konsumsi: '8–10 ekor / kg',
            jenis_pakan_didukung: 'Pelet + Vitamin',
            id_batch: ''
        },

        ikans: {!! json_encode($ikans ?? []) !!},

        get filteredIkans() {
            if (!this.searchQuery.trim()) {
                return this.ikans;
            }
            const q = this.searchQuery.toLowerCase();
            return this.ikans.filter(item => 
                (item.nama_ikan && item.nama_ikan.toLowerCase().includes(q))
            );
        },

        get avgPenetasan() {
            if (!this.ikans || !this.ikans.length) return '0 Hari';
            const sum = this.ikans.reduce((acc, i) => acc + (Number(i.durasi_penetasan) || 0), 0);
            return Math.round(sum / this.ikans.length) + ' Hari';
        },

        get avgPembibitan() {
            if (!this.ikans || !this.ikans.length) return '0 Hari';
            const sum = this.ikans.reduce((acc, i) => acc + (Number(i.durasi_pembibitan) || 0), 0);
            return Math.round(sum / this.ikans.length) + ' Hari';
        },

        get avgTotalSiklus() {
            if (!this.ikans || !this.ikans.length) return '0 Hari';
            const sumPenetasan = this.ikans.reduce((acc, i) => acc + (Number(i.durasi_penetasan) || 0), 0);
            const sumPembibitan = this.ikans.reduce((acc, i) => acc + (Number(i.durasi_pembibitan) || 0), 0);
            return Math.round((sumPenetasan + sumPembibitan) / this.ikans.length) + ' Hari';
        },

        openCreateForm() {
            this.formMode = 'create';
            this.resetForm();
            this.showForm = true;
            window.scrollTo({ top: 0, behavior: 'smooth' });
        },

        openEditForm(item) {
            this.formMode = 'edit';
            this.form = {
                id_ikan: item.id_ikan,
                nama_ikan: item.nama_ikan,
                durasi_penetasan: item.durasi_penetasan,
                durasi_pembibitan: item.durasi_pembibitan,
                fcr_min: item.fcr_min ?? 1.00,
                fcr_max: item.fcr_max ?? 1.20,
                bulan_panen_min: item.bulan_panen_min ?? 2.5,
                bulan_panen_max: item.bulan_panen_max ?? 3.0,
                target_konsumsi: item.target_konsumsi ?? '',
                jenis_pakan_didukung: item.jenis_pakan_didukung ?? '',
                id_batch: item.id_batch || ''
            };
            this.showForm = true;
            window.scrollTo({ top: 0, behavior: 'smooth' });
        },

        resetForm() {
            this.form = {
                id_ikan: null,
                nama_ikan: '',
                durasi_penetasan: 3,
                durasi_pembibitan: 21,
                fcr_min: 1.00,
                fcr_max: 1.20,
                bulan_panen_min: 2.5,
                bulan_panen_max: 3.0,
                target_konsumsi: '',
                jenis_pakan_didukung: '',
                id_batch: ''
            };
        },

        triggerToast(msg) {
            this.toastMessage = msg;
            this.showToast = true;
            setTimeout(() => { this.showToast = false; }, 4000);
        },

        async submitIkan() {
            if (!this.form.nama_ikan.trim()) {
                if (window.AppSwal) {
                    AppSwal.warning('Validasi Data', 'Nama jenis / spesies ikan wajib diisi!');
                } else {
                    alert('Nama jenis / spesies ikan wajib diisi!');
                }
                return;
            }
            if (!this.form.durasi_penetasan || Number(this.form.durasi_penetasan) < 1) {
                if (window.AppSwal) {
                    AppSwal.warning('Validasi Data', 'Durasi masa penetasan harus minimal 1 hari!');
                } else {
                    alert('Durasi masa penetasan harus minimal 1 hari!');
                }
                return;
            }
            if (!this.form.durasi_pembibitan || Number(this.form.durasi_pembibitan) < 1) {
                if (window.AppSwal) {
                    AppSwal.warning('Validasi Data', 'Durasi masa pembibitan harus minimal 1 hari!');
                } else {
                    alert('Durasi masa pembibitan harus minimal 1 hari!');
                }
                return;
            }

            const isEdit = (this.formMode === 'edit');
            const actionLabel = isEdit ? 'memperbarui data spesies' : 'menambahkan jenis ikan baru';

            if (window.AppSwal) {
                const resConfirm = await AppSwal.confirm({
                    title: isEdit ? 'Konfirmasi Perubahan' : 'Konfirmasi Simpan Data',
                    text: `Apakah Anda yakin ingin ${actionLabel} "${this.form.nama_ikan.trim()}"?`,
                    confirmText: isEdit ? 'Ya, Perbarui' : 'Ya, Simpan',
                    cancelText: 'Periksa Kembali',
                    icon: 'question',
                    confirmColor: '#0284C7'
                });
                if (!resConfirm.isConfirmed) {
                    return;
                }
            }

            this.isSubmitting = true;

            try {
                const url = isEdit ? `/ikan/${this.form.id_ikan}` : '/ikan';
                const method = isEdit ? 'PUT' : 'POST';

                const response = await fetch(url, {
                    method: method,
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        nama_ikan: this.form.nama_ikan.trim(),
                        durasi_penetasan: Number(this.form.durasi_penetasan),
                        durasi_pembibitan: Number(this.form.durasi_pembibitan),
                        fcr_min: this.form.fcr_min ? Number(this.form.fcr_min) : 1.00,
                        fcr_max: this.form.fcr_max ? Number(this.form.fcr_max) : 1.20,
                        bulan_panen_min: this.form.bulan_panen_min ? Number(this.form.bulan_panen_min) : null,
                        bulan_panen_max: this.form.bulan_panen_max ? Number(this.form.bulan_panen_max) : null,
                        target_konsumsi: this.form.target_konsumsi ? this.form.target_konsumsi.trim() : null,
                        jenis_pakan_didukung: this.form.jenis_pakan_didukung ? this.form.jenis_pakan_didukung.trim() : null,
                        id_batch: this.form.id_batch || null
                    })
                });

                const data = await response.json();

                if (response.ok && data.status === 'success') {
                    const resultIkan = data.ikan || data.data;

                    if (isEdit) {
                        const idx = this.ikans.findIndex(i => i.id_ikan === this.form.id_ikan);
                        if (idx !== -1) {
                            if (resultIkan) {
                                this.ikans[idx] = resultIkan;
                            } else {
                                this.ikans[idx].nama_ikan = this.form.nama_ikan.trim();
                                this.ikans[idx].durasi_penetasan = Number(this.form.durasi_penetasan);
                                this.ikans[idx].durasi_pembibitan = Number(this.form.durasi_pembibitan);
                                this.ikans[idx].fcr_min = this.form.fcr_min;
                                this.ikans[idx].fcr_max = this.form.fcr_max;
                                this.ikans[idx].bulan_panen_min = this.form.bulan_panen_min;
                                this.ikans[idx].bulan_panen_max = this.form.bulan_panen_max;
                                this.ikans[idx].target_konsumsi = this.form.target_konsumsi;
                                this.ikans[idx].jenis_pakan_didukung = this.form.jenis_pakan_didukung;
                            }
                        }
                    } else {
                        if (resultIkan) {
                            this.ikans.unshift(resultIkan);
                        } else {
                            this.ikans.unshift({
                                id_ikan: Date.now(),
                                nama_ikan: this.form.nama_ikan.trim(),
                                durasi_penetasan: Number(this.form.durasi_penetasan),
                                durasi_pembibitan: Number(this.form.durasi_pembibitan),
                                fcr_min: this.form.fcr_min,
                                fcr_max: this.form.fcr_max,
                                bulan_panen_min: this.form.bulan_panen_min,
                                bulan_panen_max: this.form.bulan_panen_max,
                                target_konsumsi: this.form.target_konsumsi,
                                jenis_pakan_didukung: this.form.jenis_pakan_didukung,
                            });
                        }
                    }

                    this.showForm = false;
                    this.resetForm();

                    if (window.AppSwal) {
                        AppSwal.success('Berhasil!', data.message || (isEdit ? 'Data jenis ikan berhasil diperbarui!' : 'Jenis ikan baru berhasil disimpan!'));
                    } else {
                        this.triggerToast(data.message || (isEdit ? 'Data diperbarui!' : 'Jenis ikan tersimpan!'));
                    }
                } else {
                    const errorMsg = data.message || 'Gagal menyimpan data jenis ikan.';
                    if (window.AppSwal) {
                        AppSwal.error('Penyimpanan Gagal', errorMsg);
                    } else {
                        alert(errorMsg);
                    }
                }
            } catch (err) {
                console.error(err);
                if (window.AppSwal) {
                    AppSwal.error('Error Server', 'Terjadi kesalahan sistem saat menghubungi server.');
                } else {
                    alert('Terjadi kesalahan jaringan.');
                }
            } finally {
                this.isSubmitting = false;
            }
        },

        async confirmDelete(item) {
            if (window.AppSwal) {
                const res = await AppSwal.confirm({
                    title: 'Hapus Jenis Ikan?',
                    text: `Apakah Anda yakin ingin menghapus data spesies "${item.nama_ikan}"? Tindakan ini tidak dapat dibatalkan.`,
                    confirmText: 'Ya, Hapus Data',
                    cancelText: 'Batalkan',
                    icon: 'warning',
                    confirmColor: '#E11D48'
                });
                if (!res.isConfirmed) {
                    return;
                }
            } else {
                if (!confirm(`Hapus spesies ${item.nama_ikan}?`)) {
                    return;
                }
            }

            try {
                const response = await fetch(`/ikan/${item.id_ikan}`, {
                    method: 'DELETE',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                });

                const data = await response.json();
                if (response.ok && data.status === 'success') {
                    this.ikans = this.ikans.filter(i => i.id_ikan !== item.id_ikan);
                    if (window.AppSwal) {
                        AppSwal.success('Terhapus!', data.message || 'Spesies ikan berhasil dihapus.');
                    } else {
                        this.triggerToast(data.message || 'Spesies ikan dihapus.');
                    }
                } else {
                    const err = data.message || 'Gagal menghapus jenis ikan.';
                    if (window.AppSwal) {
                        AppSwal.error('Gagal', err);
                    } else {
                        alert(err);
                    }
                }
            } catch (err) {
                console.error(err);
                if (window.AppSwal) {
                    AppSwal.error('Error', 'Terjadi kesalahan koneksi server.');
                } else {
                    alert('Gagal menghapus data.');
                }
            }
        }
    };
}
</script>
@endsection

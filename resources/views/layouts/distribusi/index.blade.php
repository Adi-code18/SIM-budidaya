@extends('layouts.app')

@section('title', 'Distribusi & Order - SIM-BUDIDAYA')

@section('content')
<div class="space-y-6" x-data="{ activeTab: 'semua', showForm: false, jenisOrder: 'reguler' }">

    <!-- Subtitle & Page Title Header -->
    <div class="flex items-center gap-3">
        <h1 class="text-xl font-extrabold text-[#0B2570] tracking-tight">Manajemen Distribusi</h1>
        <span class="text-xs font-semibold text-slate-400">Logistik Utama</span>
    </div>

    <!-- ========= INPUT FORM SECTION ========= -->
    <div x-show="showForm"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 -translate-y-2"
         x-transition:enter-end="opacity-100 translate-y-0"
         class="space-y-5">

        <!-- Header -->
        <div class="bg-[#051B44] rounded-2xl p-6 sm:p-8 text-white shadow-xs">
            <h2 class="text-xl font-extrabold text-white">Distribusi & Order</h2>
            <p class="text-xs text-sky-200/80 font-medium mt-1">Catat detail transaksi distribusi hasil panen atau order masuk dari mitra. Pastikan ID Mitra terdaftar dan valid sebelum menyimpan.</p>
        </div>

        <div class="bg-white p-6 sm:p-8 rounded-2xl border border-slate-200/80 shadow-xs space-y-6">

            <form action="#" method="POST" @submit.prevent class="space-y-6">

                <!-- Section 1: Informasi Utama -->
                <div class="space-y-4">
                    <div class="flex items-center gap-2.5 text-sm font-bold text-slate-900">
                        <div class="w-8 h-8 rounded-xl bg-[#051B44] text-white flex items-center justify-center">
                            <i class="fa-solid fa-receipt text-xs"></i>
                        </div>
                        <span>Informasi Utama</span>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="text-[10px] font-extrabold uppercase tracking-wider text-slate-500 block mb-1">ID TRANSAKSI</label>
                            <input type="text" value="#TRX-202310-001" readonly
                                   class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs font-extrabold text-slate-500 bg-slate-100 cursor-not-allowed">
                        </div>
                        <div>
                            <label class="text-[10px] font-extrabold uppercase tracking-wider text-slate-500 block mb-1">TANGGAL ORDER</label>
                            <input type="date"
                                   class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs font-semibold text-slate-700 bg-slate-50/70 focus:bg-white focus:outline-none focus:ring-2 focus:ring-sky-500 transition-all">
                        </div>
                    </div>
                </div>

                <!-- Section 2: Detail Mitra & Order -->
                <div class="space-y-4">
                    <div class="flex items-center gap-2.5 text-sm font-bold text-slate-900">
                        <div class="w-8 h-8 rounded-xl bg-[#0284C7] text-white flex items-center justify-center">
                            <i class="fa-solid fa-handshake text-xs"></i>
                        </div>
                        <span>Detail Mitra & Order</span>
                    </div>

                    <div>
                        <label class="text-[10px] font-extrabold uppercase tracking-wider text-slate-500 block mb-1">ID MITRA / NAMA MITRA</label>
                        <select class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs font-semibold text-slate-700 bg-slate-50/70 focus:bg-white focus:outline-none focus:ring-2 focus:ring-sky-500 transition-all">
                            <option>Pilih Mitra Budidaya...</option>
                            <option>MTR-2023-081 — The Ocean Grill</option>
                            <option>MTR-2023-102 — IndoFrozen Supply</option>
                            <option>MTR-2022-045 — Pasar Ikan Muara Baru</option>
                            <option>MTR-2023-156 — Global Seafood Corp</option>
                        </select>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="text-[10px] font-extrabold uppercase tracking-wider text-slate-500 block mb-1.5">JENIS ORDER</label>
                            <div class="flex items-center gap-1.5 p-1 bg-slate-100 rounded-xl text-xs font-bold">
                                <button type="button" @click="jenisOrder = 'reguler'"
                                        :class="jenisOrder === 'reguler' ? 'bg-[#051B44] text-white shadow-xs' : 'text-slate-600 hover:text-slate-900'"
                                        class="flex-1 py-1.5 rounded-lg transition-all text-center">
                                    Reguler
                                </button>
                                <button type="button" @click="jenisOrder = 'ekspor'"
                                        :class="jenisOrder === 'ekspor' ? 'bg-[#051B44] text-white shadow-xs' : 'text-slate-600 hover:text-slate-900'"
                                        class="flex-1 py-1.5 rounded-lg transition-all text-center">
                                    Ekspor
                                </button>
                                <button type="button" @click="jenisOrder = 'sampel'"
                                        :class="jenisOrder === 'sampel' ? 'bg-[#051B44] text-white shadow-xs' : 'text-slate-600 hover:text-slate-900'"
                                        class="flex-1 py-1.5 rounded-lg transition-all text-center">
                                    Sampel
                                </button>
                            </div>
                        </div>
                        <div>
                            <label class="text-[10px] font-extrabold uppercase tracking-wider text-slate-500 block mb-1">STATUS ORDER</label>
                            <select class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs font-semibold text-slate-700 bg-slate-50/70 focus:bg-white focus:outline-none focus:ring-2 focus:ring-sky-500 transition-all">
                                <option>Pending / Menunggu Konfirmasi</option>
                                <option>Dalam Pemberokian</option>
                                <option>Siap Kirim</option>
                                <option>Selesai</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Section 3: Kuantitas & Nilai -->
                <div class="space-y-4">
                    <div class="flex items-center gap-2.5 text-sm font-bold text-slate-900">
                        <div class="w-8 h-8 rounded-xl bg-[#10B981] text-white flex items-center justify-center">
                            <i class="fa-solid fa-weight-scale text-xs"></i>
                        </div>
                        <span>Kuantitas & Nilai</span>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="text-[10px] font-extrabold uppercase tracking-wider text-slate-500 block mb-1">TOTAL BERAT (KG)</label>
                            <div class="flex items-center gap-2">
                                <input type="number" placeholder="0.00"
                                       class="flex-1 px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs font-semibold text-slate-700 bg-slate-50/70 focus:bg-white focus:outline-none focus:ring-2 focus:ring-sky-500 transition-all">
                                <span class="text-xs font-bold text-slate-400">Kg</span>
                            </div>
                        </div>
                        <div>
                            <label class="text-[10px] font-extrabold uppercase tracking-wider text-slate-500 block mb-1">HARGA TOTAL (RP)</label>
                            <input type="text" placeholder="Rp"
                                   class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs font-semibold text-slate-700 bg-slate-50/70 focus:bg-white focus:outline-none focus:ring-2 focus:ring-sky-500 transition-all">
                        </div>
                    </div>

                    <p class="text-[10px] text-slate-400 italic flex items-center gap-1">
                        <i class="fa-solid fa-circle-info text-sky-400"></i>
                        *Harga per kg: <strong>Otomatis Dihitung</strong>
                    </p>
                </div>

                <!-- Form Actions -->
                <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
                    <button type="button" @click="showForm = false"
                            class="px-5 py-2 rounded-xl border border-slate-200 text-xs font-bold text-slate-600 hover:bg-slate-50 transition-colors">
                        Batal
                    </button>
                    <button type="submit"
                            class="px-5 py-2 rounded-xl bg-[#051B44] hover:bg-navy-900 text-white font-bold text-xs shadow-xs transition-all flex items-center gap-2">
                        <i class="fa-solid fa-floppy-disk text-xs"></i>
                        <span>Simpan Transaksi</span>
                    </button>
                </div>

            </form>

        </div>
    </div>

    <!-- ========= DIRECTORY / LIST MODE ========= -->

    <!-- 4 Top Metric KPI Cards Grid -->
    <div x-show="!showForm" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
        
        <!-- Card 1: Total Pesanan -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs flex items-center justify-between">
            <div>
                <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400 block">TOTAL PESANAN</span>
                <h3 class="text-3xl font-extrabold text-slate-900 mt-1">124</h3>
            </div>
            <div class="w-10 h-10 rounded-xl bg-[#BEE3F8]/60 text-[#006699] flex items-center justify-center">
                <i class="fa-solid fa-box text-base"></i>
            </div>
        </div>

        <!-- Card 2: Dalam Pemberokian -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs flex items-center justify-between">
            <div>
                <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400 block">DALAM PEMBEROKIAN</span>
                <h3 class="text-3xl font-extrabold text-slate-900 mt-1">42</h3>
            </div>
            <div class="w-10 h-10 rounded-xl bg-[#BEE3F8]/60 text-[#006699] flex items-center justify-center">
                <i class="fa-solid fa-hourglass-half text-base"></i>
            </div>
        </div>

        <!-- Card 3: Siap Kirim -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs flex items-center justify-between">
            <div>
                <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400 block">SIAP KIRIM</span>
                <h3 class="text-3xl font-extrabold text-slate-900 mt-1">18</h3>
            </div>
            <div class="w-10 h-10 rounded-xl bg-[#BEE3F8]/60 text-[#006699] flex items-center justify-center">
                <i class="fa-solid fa-truck text-base"></i>
            </div>
        </div>

        <!-- Card 4: Selesai Hari Ini -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs flex items-center justify-between">
            <div>
                <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400 block">SELESAI (HARI INI)</span>
                <h3 class="text-3xl font-extrabold text-slate-900 mt-1">64</h3>
            </div>
            <div class="w-10 h-10 rounded-xl bg-[#D1FAE5] text-[#059669] flex items-center justify-center">
                <i class="fa-regular fa-circle-check text-base"></i>
            </div>
        </div>

    </div>

    <!-- Filter Tabs & Input Order Action -->
    <div x-show="!showForm" class="bg-white p-3 rounded-2xl border border-slate-200/80 shadow-xs flex flex-col sm:flex-row items-center justify-between gap-4">
        <div class="flex items-center gap-2 overflow-x-auto w-full sm:w-auto">
            <button @click="activeTab = 'semua'" 
                    :class="activeTab === 'semua' ? 'bg-[#051B44] text-white font-bold' : 'text-slate-600 hover:bg-slate-100 font-semibold'" 
                    class="px-4 py-2 rounded-xl text-xs transition-all">
                Semua
            </button>
            <button @click="activeTab = 'pending'" 
                    :class="activeTab === 'pending' ? 'bg-[#051B44] text-white font-bold' : 'text-slate-600 hover:bg-slate-100 font-semibold'" 
                    class="px-4 py-2 rounded-xl text-xs transition-all">
                Pending
            </button>
            <button @click="activeTab = 'pemberokian'" 
                    :class="activeTab === 'pemberokian' ? 'bg-[#051B44] text-white font-bold' : 'text-slate-600 hover:bg-slate-100 font-semibold'" 
                    class="px-4 py-2 rounded-xl text-xs transition-all">
                Pemberokian (RM)
            </button>
            <button @click="activeTab = 'siap_kirim'" 
                    :class="activeTab === 'siap_kirim' ? 'bg-[#051B44] text-white font-bold' : 'text-slate-600 hover:bg-slate-100 font-semibold'" 
                    class="px-4 py-2 rounded-xl text-xs transition-all">
                Siap Kirim
            </button>
            <button @click="activeTab = 'selesai'" 
                    :class="activeTab === 'selesai' ? 'bg-[#051B44] text-white font-bold' : 'text-slate-600 hover:bg-slate-100 font-semibold'" 
                    class="px-4 py-2 rounded-xl text-xs transition-all">
                Selesai
            </button>
        </div>

        <button @click="showForm = true" class="w-full sm:w-auto px-4 py-2.5 rounded-xl bg-[#006699] hover:bg-[#005580] text-white font-bold text-xs flex items-center justify-center gap-2 shadow-xs transition-all">
            <i class="fa-solid fa-cart-shopping text-xs"></i>
            <span>Input Order Baru</span>
        </button>
    </div>

    <!-- Order Cards Grid -->
    <div x-show="!showForm" class="grid grid-cols-1 md:grid-cols-3 gap-5">
        
        <!-- Order Card 1: Pemberokian -->
        <div class="bg-white rounded-2xl border border-slate-200/80 p-5 shadow-xs flex flex-col justify-between space-y-4">
            <div class="space-y-3">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold text-[#0055CC]">#ORD-2023-9021</span>
                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold bg-[#E0F2FE] text-[#0284C7] uppercase">
                        Pemberokian
                    </span>
                </div>
                <h4 class="font-extrabold text-slate-900 text-sm">CV. Bahari Makmur</h4>

                <!-- Volume Box -->
                <div class="bg-slate-50 border border-slate-100 p-3 rounded-xl">
                    <span class="text-[10px] font-extrabold text-slate-400 uppercase tracking-widest block">VOLUME</span>
                    <h3 class="text-lg font-extrabold text-slate-900 mt-0.5">250 kg</h3>
                </div>

                <div class="space-y-1 text-xs text-slate-500 font-medium">
                    <p><span class="font-bold text-slate-700">TANGGAL ORDER:</span> 5/8/26</p>
                    <p class="text-[11px]"><span class="font-bold text-slate-700">Alamat:</span> Panjalu, Ciamis, Jalan HJ Abdul Hamid</p>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-2 pt-2 border-t border-slate-100">
                <button class="px-3 py-2 rounded-xl border border-slate-300 text-slate-700 hover:bg-slate-50 font-bold text-xs flex items-center justify-center gap-1.5 transition-colors">
                    <i class="fa-solid fa-print text-xs"></i>
                    <span>Cetak Label</span>
                </button>
                <button class="px-3 py-2 rounded-xl bg-[#051B44] hover:bg-navy-900 text-white font-bold text-xs flex items-center justify-center gap-1.5 transition-all shadow-xs">
                    <span>Update Status</span>
                </button>
            </div>
        </div>

        <!-- Order Card 2: Siap Kirim -->
        <div class="bg-white rounded-2xl border border-slate-200/80 p-5 shadow-xs flex flex-col justify-between space-y-4">
            <div class="space-y-3">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold text-[#0055CC]">#ORD-2023-9025</span>
                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold bg-[#C6F6D5] text-[#22543D] uppercase">
                        Siap Kirim
                    </span>
                </div>
                <h4 class="font-extrabold text-slate-900 text-sm">Sinar Mas Frozen</h4>

                <!-- Volume Box -->
                <div class="bg-slate-50 border border-slate-100 p-3 rounded-xl">
                    <span class="text-[10px] font-extrabold text-slate-400 uppercase tracking-widest block">VOLUME</span>
                    <h3 class="text-lg font-extrabold text-slate-900 mt-0.5">1,200 kg</h3>
                </div>

                <div class="space-y-1 text-xs text-slate-500 font-medium">
                    <p><span class="font-bold text-slate-700">TANGGAL ORDER:</span> 5/8/26</p>
                    <p class="text-[11px]"><span class="font-bold text-slate-700">Alamat:</span> Ciamis, Buniseuri, Jalan HJ Nurjamil</p>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-2 pt-2 border-t border-slate-100">
                <button class="px-3 py-2 rounded-xl border border-slate-300 text-slate-700 hover:bg-slate-50 font-bold text-xs flex items-center justify-center gap-1.5 transition-colors">
                    <i class="fa-solid fa-print text-xs"></i>
                    <span>Cetak Label</span>
                </button>
                <button class="px-3 py-2 rounded-xl bg-[#051B44] hover:bg-navy-900 text-white font-bold text-xs flex items-center justify-center gap-1.5 transition-all shadow-xs">
                    <span>Konfirmasi Kurir</span>
                </button>
            </div>
        </div>

        <!-- Order Card 3: Pending -->
        <div class="bg-white rounded-2xl border border-slate-200/80 p-5 shadow-xs flex flex-col justify-between space-y-4">
            <div class="space-y-3">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold text-[#0055CC]">#ORD-2023-9030</span>
                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold bg-[#FEE2E2] text-[#991B1B] uppercase">
                        Pending
                    </span>
                </div>
                <h4 class="font-extrabold text-slate-900 text-sm">Indo Seafood Utama</h4>

                <!-- Volume Box -->
                <div class="bg-slate-50 border border-slate-100 p-3 rounded-xl">
                    <span class="text-[10px] font-extrabold text-slate-400 uppercase tracking-widest block">VOLUME</span>
                    <h3 class="text-lg font-extrabold text-slate-900 mt-0.5">500 kg</h3>
                </div>

                <div class="space-y-1 text-xs text-slate-500 font-medium">
                    <p><span class="font-bold text-slate-700">TANGGAL ORDER:</span> 4/8/26</p>
                    <p class="text-[11px]"><span class="font-bold text-slate-700">Alamat:</span> Pelabuhan Tanjung Priok Jakarta Pusat</p>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-2 pt-2 border-t border-slate-100">
                <button class="px-3 py-2 rounded-xl border border-slate-300 text-slate-700 hover:bg-slate-50 font-bold text-xs flex items-center justify-center gap-1.5 transition-colors">
                    <i class="fa-solid fa-print text-xs"></i>
                    <span>Cetak Label</span>
                </button>
                <button class="px-3 py-2 rounded-xl bg-[#051B44] hover:bg-navy-900 text-white font-bold text-xs flex items-center justify-center gap-1.5 transition-all shadow-xs">
                    <span>Update Status</span>
                </button>
            </div>
        </div>

    </div>

    <!-- Selesai Order Card Row -->
    <div x-show="!showForm" class="max-w-xs">
        <div class="bg-white rounded-2xl border border-slate-200/80 p-5 shadow-xs space-y-4">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-slate-400">#ORD-2023-8999</span>
                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold bg-[#E2E8F0] text-[#475569] uppercase">
                    Selesai
                </span>
            </div>
            <h4 class="font-extrabold text-slate-900 text-sm">Koperasi Nelayan Jaya</h4>

            <div class="flex items-center justify-between text-xs text-slate-500 font-medium">
                <div class="flex items-center gap-1">
                    <i class="fa-regular fa-circle-check text-emerald-600"></i>
                    <span>TANGGAL ORDER: 12/4/26</span>
                </div>
                <span class="font-extrabold text-slate-800">400 kg</span>
            </div>

            <button class="w-full py-2 rounded-xl border border-slate-300 text-slate-700 hover:bg-slate-50 font-bold text-xs flex items-center justify-center gap-2 transition-colors">
                <i class="fa-regular fa-file-lines text-xs"></i>
                <span>Lihat Invoice</span>
            </button>
        </div>
    </div>

</div>
@endsection

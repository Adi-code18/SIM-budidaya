@extends('layouts.app')

@section('title', 'Transaksi Keuangan - SIM-BUDIDAYA')

@section('content')
<div class="space-y-6" x-data="transaksiKeuanganComponent()">

    <!-- Flash Alerts -->
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

    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2">
                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold uppercase bg-sky-100 text-sky-700">Buku Kas & Jurnal</span>
            </div>
            <h1 class="text-2xl font-extrabold text-[#0B2570] tracking-tight mt-1">Transaksi Keuangan</h1>
            <p class="text-xs text-slate-500 font-medium mt-0.5">Catat transaksi pemasukan/pengeluaran kas operasional dan kelola riwayat mutasi kas.</p>
        </div>
        <div class="flex items-center gap-3">
            <button @click="showForm ? (showForm = false) : openCreateForm()"
                    class="px-4 py-2 rounded-xl bg-[#051B44] hover:bg-navy-900 text-white font-bold text-xs shadow-xs transition-all flex items-center gap-2 cursor-pointer">
                <i class="fa-solid" :class="showForm ? 'fa-table-list' : 'fa-plus'"></i>
                <span x-text="showForm ? 'Tutup Form' : 'Catat Transaksi Baru'"></span>
            </button>
        </div>
    </div>

    <!-- Quick Balance Summary Mini Strip -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-xs flex items-center justify-between">
            <div>
                <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400 block">TOTAL PEMASUKAN</span>
                <span class="text-lg font-extrabold text-emerald-600 tracking-tight">Rp {{ number_format($totalIncome, 0, ',', '.') }}</span>
            </div>
            <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center">
                <i class="fa-solid fa-arrow-down-long text-sm"></i>
            </div>
        </div>
        <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-xs flex items-center justify-between">
            <div>
                <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400 block">TOTAL PENGELUARAN</span>
                <span class="text-lg font-extrabold text-rose-600 tracking-tight">Rp {{ number_format($totalExpense, 0, ',', '.') }}</span>
            </div>
            <div class="w-10 h-10 rounded-xl bg-rose-50 text-rose-600 flex items-center justify-center">
                <i class="fa-solid fa-arrow-up-long text-sm"></i>
            </div>
        </div>
        <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-xs flex items-center justify-between">
            <div>
                <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400 block">SALDO KAS OPERASIONAL</span>
                <span class="text-lg font-extrabold {{ $saldo >= 0 ? 'text-[#0B2570]' : 'text-rose-600' }} tracking-tight">Rp {{ number_format($saldo, 0, ',', '.') }}</span>
            </div>
            <div class="w-10 h-10 rounded-xl bg-sky-50 text-[#0B2570] flex items-center justify-center">
                <i class="fa-solid fa-wallet text-sm"></i>
            </div>
        </div>
    </div>

    <!-- ========= INPUT FORM SECTION ========= -->
    <div x-show="showForm"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 -translate-y-2"
         x-transition:enter-end="opacity-100 translate-y-0"
         class="space-y-5">

        <!-- Header Form -->
        <div class="bg-[#051B44] rounded-2xl p-6 sm:p-8 text-white shadow-xs">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-xl font-extrabold text-white" x-text="formMode === 'view' ? 'Detail Transaksi Keuangan' : (formMode === 'edit' ? 'Edit Transaksi Keuangan' : 'Pencatatan Transaksi Keuangan Baru')"></h2>
                    <p class="text-xs text-sky-200/80 font-medium mt-1" x-text="formMode === 'view' ? 'Melihat rincian transaksi kas dan jurnal operasional budidaya.' : 'Catat transaksi masuk (pemasukan) atau keluar (pengeluaran) untuk operasional budidaya.'"></p>
                </div>
                <div x-show="formMode === 'view'">
                    <span class="px-3 py-1 rounded-full text-xs font-bold bg-sky-500/20 text-sky-200 border border-sky-400/30">Mode Baca (Read Only)</span>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            <!-- Left 2 Cols: Form Fields -->
            <div class="lg:col-span-2 bg-white p-6 sm:p-8 rounded-2xl border border-slate-200/80 shadow-xs space-y-6">

                <form @submit.prevent="saveForm()" class="space-y-6">

                    <!-- Section 1: Informasi Dasar -->
                    <div class="space-y-4">
                        <div class="flex items-center gap-2.5 text-sm font-bold text-slate-900">
                            <div class="w-8 h-8 rounded-xl bg-[#051B44] text-white flex items-center justify-center">
                                <i class="fa-solid fa-circle-info text-xs"></i>
                            </div>
                            <span>Informasi Dasar</span>
                        </div>

                        <!-- Tipe Transaksi Toggle -->
                        <div>
                            <label class="text-[10px] font-extrabold uppercase tracking-wider text-slate-500 block mb-1.5">TIPE TRANSAKSI</label>
                            <div class="flex items-center gap-2 p-1 bg-slate-100 rounded-xl max-w-sm text-xs font-bold" :class="formMode === 'view' ? 'opacity-70 pointer-events-none' : ''">
                                <button type="button" @click="if (formMode !== 'view') { tipeTransaksi = 'income'; form.tipe = 'income'; }"
                                        :class="tipeTransaksi === 'income' ? 'bg-[#051B44] text-white shadow-xs' : 'text-slate-600 hover:text-slate-900'"
                                        :disabled="formMode === 'view'"
                                        class="flex-1 py-2 rounded-lg transition-all text-center flex items-center justify-center gap-1.5 cursor-pointer">
                                    <i class="fa-solid fa-arrow-down text-[10px]"></i> Pemasukan (Income)
                                </button>
                                <button type="button" @click="if (formMode !== 'view') { tipeTransaksi = 'expense'; form.tipe = 'expense'; }"
                                        :class="tipeTransaksi === 'expense' ? 'bg-[#051B44] text-white shadow-xs' : 'text-slate-600 hover:text-slate-900'"
                                        :disabled="formMode === 'view'"
                                        class="flex-1 py-2 rounded-lg transition-all text-center flex items-center justify-center gap-1.5 cursor-pointer">
                                    <i class="fa-solid fa-arrow-up text-[10px]"></i> Pengeluaran (Expense)
                                </button>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="text-[10px] font-extrabold uppercase tracking-wider text-slate-500 block mb-1">ID TRANSAKSI (REF)</label>
                                <input type="text" x-model="form.id" readonly
                                       class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs font-extrabold text-slate-500 bg-slate-100 cursor-not-allowed">
                            </div>
                            <div>
                                <label class="text-[10px] font-extrabold uppercase tracking-wider text-slate-500 block mb-1">TANGGAL TRANSAKSI</label>
                                <input type="date" x-model="form.tanggal" @change="onDateChange()" :disabled="formMode === 'view'" required
                                       class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs font-semibold text-slate-700 bg-slate-50/70 focus:bg-white focus:outline-none focus:ring-2 focus:ring-sky-500 transition-all disabled:bg-slate-100 disabled:cursor-not-allowed">
                            </div>
                        </div>
                    </div>

                    <!-- Section 2: Detail Finansial -->
                    <div class="space-y-4">
                        <div class="flex items-center gap-2.5 text-sm font-bold text-slate-900">
                            <div class="w-8 h-8 rounded-xl bg-[#10B981] text-white flex items-center justify-center">
                                <i class="fa-solid fa-coins text-xs"></i>
                            </div>
                            <span>Detail Finansial</span>
                        </div>

                        <div>
                            <label class="text-[10px] font-extrabold uppercase tracking-wider text-slate-500 block mb-1">NOMINAL (Rp) <span class="text-rose-500">*</span></label>
                            <div class="flex items-center rounded-xl border border-slate-200 bg-slate-50/70 overflow-hidden focus-within:bg-white focus-within:ring-2 focus-within:ring-sky-500 focus-within:border-sky-500 transition-all">
                                <span class="px-4 py-3 text-sm font-extrabold text-slate-500 bg-slate-100/80 border-r border-slate-200 shrink-0">Rp</span>
                                <input type="number" x-model="form.nominal" :disabled="formMode === 'view'" placeholder="0" required min="1"
                                       onkeydown="if(event.key === '-' || event.key === 'e' || event.key === 'E' || event.key === '+') event.preventDefault()"
                                       @input="if(form.nominal !== '' && Number(form.nominal) < 0) form.nominal = Math.abs(Number(form.nominal)) || ''"
                                       class="w-full px-3.5 py-3 text-lg font-extrabold text-slate-900 bg-transparent border-0 focus:outline-none disabled:bg-slate-100 disabled:cursor-not-allowed">
                            </div>
                        </div>

                        <div>
                            <label class="text-[10px] font-extrabold uppercase tracking-wider text-slate-500 block mb-1">KATEGORI <span class="text-rose-500">*</span></label>
                            <select x-model="form.kategori" :disabled="formMode === 'view'" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs font-semibold text-slate-700 bg-slate-50/70 focus:bg-white focus:outline-none focus:ring-2 focus:ring-sky-500 transition-all disabled:bg-slate-100 disabled:cursor-not-allowed">
                                <option value="">Pilih Kategori...</option>
                                <option value="Penjualan Panen">Penjualan Panen</option>
                                <option value="Penjualan Ekspor Ikan Patin">Penjualan Ekspor Ikan Patin</option>
                                <option value="Penjualan Panen Ikan Nila">Penjualan Panen Ikan Nila</option>
                                <option value="Penjualan Benih Bibit Ikan">Penjualan Benih Bibit Ikan</option>
                                <option value="Pembelian Pakan Pelet">Pembelian Pakan Pelet</option>
                                <option value="Pembelian Obat &amp; Probiotik">Pembelian Obat &amp; Probiotik</option>
                                <option value="Biaya Listrik &amp; Operasional Aerator">Biaya Listrik &amp; Operasional Aerator</option>
                                <option value="Gaji &amp; Honor Petugas">Gaji &amp; Honor Petugas</option>
                                <option value="Operasional &amp; Perawatan">Operasional &amp; Perawatan</option>
                                <option value="Transportasi &amp; Distribusi">Transportasi &amp; Distribusi</option>
                                <option value="Lain-lain">Lain-lain</option>
                            </select>
                        </div>

                        <div>
                            <label class="text-[10px] font-extrabold uppercase tracking-wider text-slate-500 block mb-1">ALOKASI KOLAM (OPSIONAL)</label>
                            <select x-model="form.id_kolam" :disabled="formMode === 'view'" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs font-semibold text-slate-700 bg-slate-50/70 focus:bg-white focus:outline-none focus:ring-2 focus:ring-sky-500 transition-all disabled:bg-slate-100 disabled:cursor-not-allowed">
                                <option value="">Tidak dialokasikan ke kolam spesifik</option>
                                @foreach($kolams as $kolam)
                                    <option value="{{ $kolam->id_kolam }}">{{ $kolam->nama_kolam }} ({{ $kolam->tipe_kolam }})</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="text-[10px] font-extrabold uppercase tracking-wider text-slate-500 block mb-1">KETERANGAN / DESKRIPSI</label>
                            <textarea rows="3" x-model="form.keterangan" :disabled="formMode === 'view'" placeholder="Tuliskan catatan transaksi, nomor nota, atau detail pembelian..."
                                      class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs font-semibold text-slate-700 bg-slate-50/70 focus:bg-white focus:outline-none focus:ring-2 focus:ring-sky-500 transition-all disabled:bg-slate-100 disabled:cursor-not-allowed"></textarea>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
                        <button type="button" @click="showForm = false"
                                class="px-5 py-2.5 rounded-xl border border-slate-200 hover:bg-slate-100 text-slate-700 font-bold text-xs transition-all cursor-pointer">
                            Tutup
                        </button>
                        <button type="submit" x-show="formMode !== 'view'" :disabled="isLoading"
                                class="px-6 py-2.5 rounded-xl bg-[#051B44] hover:bg-navy-900 text-white font-bold text-xs shadow-sm transition-all flex items-center gap-2 cursor-pointer disabled:opacity-50">
                            <i class="fa-solid fa-spinner fa-spin" x-show="isLoading"></i>
                            <span x-text="formMode === 'edit' ? 'Simpan Perubahan' : 'Simpan Transaksi'"></span>
                        </button>
                    </div>

                </form>

            </div>

            <!-- Right 1 Col: Preview / Help Box -->
            <div class="space-y-6">

                <!-- Ringkasan Info Transaksi Box -->
                <div class="bg-[#051B44] rounded-2xl p-6 text-white shadow-xs space-y-4">
                    <div class="flex items-center justify-between pb-3 border-b border-white/10">
                        <span class="text-xs font-bold text-sky-200 uppercase tracking-wider">Ringkasan Input</span>
                        <span class="px-2 py-0.5 rounded text-[10px] font-extrabold"
                              :class="tipeTransaksi === 'income' ? 'bg-emerald-500/30 text-emerald-300' : 'bg-rose-500/30 text-rose-300'"
                              x-text="tipeTransaksi === 'income' ? 'Pemasukan' : 'Pengeluaran'"></span>
                    </div>

                    <div class="space-y-3 text-xs">
                        <div class="flex justify-between">
                            <span class="text-slate-300">Nominal:</span>
                            <span class="font-extrabold text-base" :class="tipeTransaksi === 'income' ? 'text-emerald-400' : 'text-rose-400'"
                                  x-text="formatCurrency(form.nominal)"></span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-slate-300">Kategori:</span>
                            <span class="font-bold text-white text-right" x-text="form.kategori || '-'"></span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-slate-300">Tanggal:</span>
                            <span class="font-semibold text-white" x-text="form.tanggal || '-'"></span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-slate-300">Ref ID:</span>
                            <span class="font-mono text-sky-200 font-bold" x-text="form.id || '-'"></span>
                        </div>
                    </div>
                </div>

                <!-- Panduan Input Widget -->
                <div class="bg-sky-50/80 p-5 rounded-2xl border border-sky-200/60 space-y-3">
                    <div class="flex items-center gap-2 text-xs font-bold text-[#0B2570]">
                        <i class="fa-solid fa-lightbulb text-sky-500"></i>
                        <span>Panduan Input</span>
                    </div>
                    <ul class="text-[11px] text-slate-600 space-y-2.5 leading-relaxed list-none">
                        <li class="flex items-start gap-2">
                            <span class="w-4 h-4 rounded-full bg-sky-200/80 text-sky-700 flex items-center justify-center shrink-0 text-[9px] font-extrabold mt-0.5">1</span>
                            <span>Pastikan tipe transaksi sesuai dengan bukti fisik kwitansi/faktur.</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <span class="w-4 h-4 rounded-full bg-sky-200/80 text-sky-700 flex items-center justify-center shrink-0 text-[9px] font-extrabold mt-0.5">2</span>
                            <span>Gunakan Ref ID yang valid seperti Nomor Invoice, Nota, atau Kode Struk untuk audit.</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <span class="w-4 h-4 rounded-full bg-sky-200/80 text-sky-700 flex items-center justify-center shrink-0 text-[9px] font-extrabold mt-0.5">3</span>
                            <span>Alokasikan transaksi ke unit Kolam jika beban biaya khusus untuk kolam tersebut.</span>
                        </li>
                    </ul>
                </div>

            </div>

        </div>
    </div>

    <!-- ========= TRANSACTION HISTORY TABLE SECTION ========= -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden space-y-0">
        
        <!-- Filter and Search Bar -->
        <div class="p-5 border-b border-slate-100 flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h3 class="text-base font-bold text-slate-900">Riwayat Mutasi Transaksi</h3>
                <p class="text-xs text-slate-400 font-medium">Daftar lengkap seluruh arus transaksi kas operasional budidaya.</p>
            </div>

            <div class="flex flex-wrap items-center gap-3">
                <!-- Search Input -->
                <div class="relative">
                    <i class="fa-solid fa-magnifying-glass absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                    <input type="text" x-model="searchQuery" placeholder="Cari keterangan / ref..." 
                           class="pl-9 pr-3.5 py-2 rounded-xl border border-slate-200 bg-slate-50/70 focus:bg-white text-xs font-semibold text-slate-700 focus:outline-none focus:ring-2 focus:ring-sky-500 transition-all w-52 sm:w-64">
                </div>

                <!-- Type Filter -->
                <select x-model="filterType" class="px-3 py-2 rounded-xl border border-slate-200 bg-slate-50/70 focus:bg-white text-xs font-semibold text-slate-700 focus:outline-none focus:ring-2 focus:ring-sky-500 transition-all">
                    <option value="">Semua Tipe</option>
                    <option value="income">Pemasukan (Income)</option>
                    <option value="expense">Pengeluaran (Expense)</option>
                </select>

                <!-- Counter Badge -->
                <div class="flex items-center gap-2 px-3 py-2 rounded-xl border border-slate-200 bg-slate-50 text-xs font-bold text-slate-700">
                    <i class="fa-solid fa-receipt text-sky-600"></i>
                    <span x-text="filteredTransactions.length + ' Data'"></span>
                </div>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50/70 border-b border-slate-100 text-[10px] font-extrabold text-slate-400 uppercase tracking-wider">
                        <th class="py-3.5 px-6">TANGGAL</th>
                        <th class="py-3.5 px-6">DESKRIPSI &amp; REF</th>
                        <th class="py-3.5 px-6">KATEGORI</th>
                        <th class="py-3.5 px-6">ALOKASI KOLAM</th>
                        <th class="py-3.5 px-6">NOMINAL</th>
                        <th class="py-3.5 px-6 text-right">AKSI</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-xs font-medium text-slate-700">

                    <template x-if="filteredTransactions.length === 0">
                        <tr>
                            <td colspan="6" class="py-12 text-center text-slate-400 text-xs">
                                <i class="fa-regular fa-folder-open text-3xl text-slate-300 block mb-2"></i>
                                Tidak ada data transaksi yang sesuai filter atau pencarian.
                            </td>
                        </tr>
                    </template>

                    <template x-for="transaction in filteredTransactions" :key="transaction.raw_id || transaction.id">
                        <tr class="hover:bg-slate-50/70 transition-colors">
                            <td class="py-4 px-6 text-slate-500 font-semibold" x-text="transaction.tanggal"></td>
                            <td class="py-4 px-6">
                                <div class="font-bold text-slate-900" x-text="transaction.keterangan || '-'"></div>
                                <div class="text-[10px] text-slate-400 font-mono font-semibold mt-0.5" x-text="transaction.ref"></div>
                            </td>
                            <td class="py-4 px-6">
                                <span class="px-2.5 py-0.5 rounded-full text-[11px] font-extrabold"
                                      :class="transaction.tipe === 'income' ? 'bg-[#C6F6D5] text-[#22543D]' : 'bg-[#E0F2FE] text-[#0284C7]'"
                                      x-text="transaction.kategori"></span>
                            </td>
                            <td class="py-4 px-6 text-slate-500 font-medium" x-text="transaction.kolam"></td>
                            <td class="py-4 px-6 font-extrabold" :class="transaction.tipe === 'income' ? 'text-emerald-600' : 'text-rose-600'"
                                x-text="(transaction.tipe === 'income' ? '+ ' : '- ') + formatCurrency(transaction.nominal)"></td>
                            <td class="py-4 px-6 text-right">
                                <div class="relative inline-block text-left" x-data="{ open: false }">
                                    <button @click="open = !open" @click.away="open = false" class="text-slate-400 hover:text-slate-600 p-1.5 rounded-lg hover:bg-slate-100 transition-colors cursor-pointer">
                                        <i class="fa-solid fa-ellipsis-vertical text-sm"></i>
                                    </button>

                                    <div x-show="open"
                                         x-transition:enter="transition ease-out duration-100"
                                         x-transition:enter-start="transform opacity-0 scale-95"
                                         x-transition:enter-end="transform opacity-100 scale-100"
                                         x-transition:leave="transition ease-in duration-75"
                                         x-transition:leave-start="transform opacity-100 scale-100"
                                         x-transition:leave-end="transform opacity-0 scale-95"
                                         class="absolute right-0 mt-2 w-44 rounded-xl bg-white border border-slate-200 shadow-xl py-1.5 z-50 text-left">
                                        
                                        <!-- View Detail Button -->
                                        <button type="button" @click="open = false; openViewForm(transaction)" class="w-full px-3.5 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-50 flex items-center gap-2.5 cursor-pointer">
                                            <i class="fa-solid fa-eye text-sky-600 w-4"></i>
                                            <span>Lihat Detail</span>
                                        </button>

                                        <!-- Edit / Update Button -->
                                        <button type="button" @click="open = false; openEditForm(transaction)" class="w-full px-3.5 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-50 flex items-center gap-2.5 cursor-pointer">
                                            <i class="fa-solid fa-pen-to-square text-amber-600 w-4"></i>
                                            <span>Edit / Ubah</span>
                                        </button>

                                        <div class="my-1 border-t border-slate-100"></div>

                                        <!-- Delete Button -->
                                        <button type="button" @click="open = false; deleteTransaction(transaction)" class="w-full px-3.5 py-2 text-xs font-semibold text-red-600 hover:bg-red-50 flex items-center gap-2.5 cursor-pointer">
                                            <i class="fa-solid fa-trash-can text-red-500 w-4"></i>
                                            <span>Hapus Data</span>
                                        </button>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    </template>

                </tbody>
            </table>
        </div>

        <!-- Table Footer Pagination -->
        <div class="p-4 border-t border-slate-100 flex flex-col sm:flex-row items-center justify-between gap-4 text-xs font-medium text-slate-500">
            <span>Menampilkan <strong class="text-slate-800" x-text="filteredTransactions.length"></strong> dari <strong class="text-slate-800" x-text="transactions.length"></strong> total transaksi</span>
            <div class="flex items-center gap-1">
                <span class="text-[11px] text-slate-400">Pencatatan real-time tersinkronisasi</span>
            </div>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
function transaksiKeuanganComponent() {
    return {
        showForm: false,
        formMode: "create",
        tipeTransaksi: "income",
        isLoading: false,
        searchQuery: "",
        filterType: "",
        transactions: {!! isset($transactions) && count($transactions) > 0 ? json_encode($transactions) : "[]" !!},
        form: {
            raw_id: null,
            id: "",
            tanggal: "{{ date('Y-m-d') }}",
            tipe: "income",
            nominal: "",
            kategori: "",
            ref: "",
            id_kolam: "",
            kolam: "Tidak dialokasikan",
            keterangan: ""
        },

        get filteredTransactions() {
            return this.transactions.filter(item => {
                const matchType = !this.filterType || item.tipe === this.filterType;
                const query = this.searchQuery.toLowerCase().trim();
                const matchQuery = !query || 
                    (item.keterangan && item.keterangan.toLowerCase().includes(query)) ||
                    (item.ref && item.ref.toLowerCase().includes(query)) ||
                    (item.kategori && item.kategori.toLowerCase().includes(query)) ||
                    (item.kolam && item.kolam.toLowerCase().includes(query));
                return matchType && matchQuery;
            });
        },

        formatCurrency(value) {
            return new Intl.NumberFormat("id-ID", { style: "currency", currency: "IDR", maximumFractionDigits: 0 }).format(value || 0);
        },

        generateSopCode(dateStr) {
            const d = dateStr ? new Date(dateStr) : new Date();
            const yy = String(d.getFullYear()).slice(-2);
            const mm = String(d.getMonth() + 1).padStart(2, '0');
            const prefix = 'TRX-' + yy + mm + '-';
            const countThisMonth = this.transactions.filter(t => t.ref && t.ref.startsWith(prefix)).length + 1;
            return prefix + String(countThisMonth).padStart(3, '0');
        },

        onDateChange() {
            if (this.formMode === 'create' && this.form.tanggal) {
                const autoId = this.generateSopCode(this.form.tanggal);
                this.form.id = autoId;
                this.form.ref = autoId;
            }
        },

        openCreateForm() {
            this.formMode = "create";
            this.showForm = true;
            this.tipeTransaksi = "income";
            const today = new Date().toISOString().split("T")[0];
            const autoId = this.generateSopCode(today);
            this.form = {
                raw_id: null,
                id: autoId,
                tanggal: today,
                tipe: "income",
                nominal: "",
                kategori: "",
                ref: autoId,
                id_kolam: "",
                kolam: "Tidak dialokasikan",
                keterangan: ""
            };
        },

        openViewForm(item) {
            this.formMode = "view";
            this.showForm = true;
            this.tipeTransaksi = item.tipe;
            this.form = { 
                raw_id: item.raw_id,
                id: item.id,
                tanggal: item.tanggal,
                tipe: item.tipe,
                nominal: item.nominal,
                kategori: item.kategori,
                ref: item.ref,
                id_kolam: item.id_kolam || "",
                kolam: item.kolam,
                keterangan: item.keterangan === "-" ? "" : item.keterangan
            };
        },

        openEditForm(item) {
            this.formMode = "edit";
            this.showForm = true;
            this.tipeTransaksi = item.tipe;
            this.form = { 
                raw_id: item.raw_id,
                id: item.id,
                tanggal: item.tanggal,
                tipe: item.tipe,
                nominal: item.nominal,
                kategori: item.kategori,
                ref: item.ref,
                id_kolam: item.id_kolam || "",
                kolam: item.kolam,
                keterangan: item.keterangan === "-" ? "" : item.keterangan
            };
        },

        saveForm() {
            if (!this.form.tanggal) {
                alert("Silakan pilih tanggal transaksi.");
                return;
            }
            if (!this.form.nominal || Number(this.form.nominal) <= 0) {
                alert("Silakan masukkan nominal transaksi yang valid.");
                return;
            }
            if (!this.form.kategori) {
                alert("Silakan pilih kategori transaksi.");
                return;
            }

            this.isLoading = true;
            const url = this.formMode === "create" ? "{{ route('keuangan.store') }}" : ("/keuangan/" + this.form.raw_id);
            const method = this.formMode === "create" ? "POST" : "PUT";

            const payload = {
                tanggal_transaksi: this.form.tanggal,
                tipe_transaksi: this.tipeTransaksi,
                nominal: Number(this.form.nominal),
                kategori: this.form.kategori,
                ref_id: this.form.ref,
                id_kolam: this.form.id_kolam,
                keterangan: this.form.keterangan
            };

            fetch(url, {
                method: method,
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": "{{ csrf_token() }}",
                    "Accept": "application/json"
                },
                body: JSON.stringify(payload)
            })
            .then(res => res.json())
            .then(data => {
                this.isLoading = false;
                if (data.success) {
                    window.location.reload();
                } else {
                    alert(data.message || "Gagal menyimpan transaksi.");
                }
            })
            .catch(err => {
                this.isLoading = false;
                console.error(err);
                alert("Terjadi kesalahan koneksi server saat menyimpan.");
            });
        },

        deleteTransaction(item) {
            if (confirm("Apakah Anda yakin ingin menghapus transaksi \"" + (item.ref || item.id) + "\"?")) {
                this.isLoading = true;
                fetch("/keuangan/" + item.raw_id, {
                    method: "DELETE",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": "{{ csrf_token() }}",
                        "Accept": "application/json"
                    }
                })
                .then(res => res.json())
                .then(data => {
                    this.isLoading = false;
                    if (data.success) {
                        window.location.reload();
                    } else {
                        alert(data.message || "Gagal menghapus transaksi.");
                    }
                })
                .catch(err => {
                    this.isLoading = false;
                    console.error(err);
                    alert("Terjadi kesalahan koneksi server saat menghapus.");
                });
            }
        }
    };
}
</script>
@endpush

@extends('layouts.app')

@section('title', 'Financial Management - SIM-BUDIDAYA')

@section('content')
<div class="space-y-6" x-data="keuanganComponent()">

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

    <!-- Subtitle & Page Title Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-extrabold text-[#0B2570] tracking-tight">Financial Management</h1>
            <p class="text-xs text-slate-500 font-medium mt-0.5">Monitor revenue streams, expenses, and overall aquaculture profitability.</p>
        </div>
        <div class="flex items-center gap-3">
            <button @click="showForm ? (showForm = false) : openCreateForm()"
                    class="px-4 py-2 rounded-xl bg-[#051B44] hover:bg-navy-900 text-white font-bold text-xs shadow-xs transition-all flex items-center gap-2">
                <i class="fa-solid" :class="showForm ? 'fa-table-list' : 'fa-plus'" class="text-xs"></i>
                <span x-text="showForm ? 'Lihat Data' : 'Add Transaction'"></span>
            </button>
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
                    <h2 class="text-xl font-extrabold text-white" x-text="formMode === 'view' ? 'Detail Transaksi Keuangan' : (formMode === 'edit' ? 'Edit Transaksi Keuangan' : 'Pencatatan Keuangan Baru')"></h2>
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
                                <label class="text-[10px] font-extrabold uppercase tracking-wider text-slate-500 block mb-1">ID KEUANGAN</label>
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
                            <textarea rows="3" x-model="form.keterangan" :disabled="formMode === 'view'" placeholder="Tambahkan catatan khusus terkait transaksi ini..."
                                      class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs font-semibold text-slate-700 bg-slate-50/70 focus:bg-white focus:outline-none focus:ring-2 focus:ring-sky-500 transition-all disabled:bg-slate-100 disabled:cursor-not-allowed"></textarea>
                        </div>
                    </div>

                    <!-- Form Actions -->
                    <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
                        <button type="button" @click="showForm = false; formMode = 'create'"
                                class="px-5 py-2 rounded-xl border border-slate-200 text-xs font-bold text-slate-600 hover:bg-slate-50 transition-colors">
                            <span x-text="formMode === 'view' ? 'Tutup' : 'Batalkan'"></span>
                        </button>

                        <button x-show="formMode !== 'view'" type="submit" :disabled="isLoading"
                                class="px-5 py-2 rounded-xl bg-[#051B44] hover:bg-navy-900 text-white font-bold text-xs shadow-xs transition-all flex items-center gap-2 disabled:opacity-50">
                            <i class="fa-solid" :class="isLoading ? 'fa-spinner fa-spin' : 'fa-floppy-disk'" class="text-xs"></i>
                            <span x-text="isLoading ? 'Menyimpan...' : (formMode === 'create' ? 'Simpan Transaksi' : 'Simpan Perubahan')"></span>
                        </button>

                        <button x-show="formMode === 'view'" type="button" @click="formMode = 'edit'"
                                class="px-5 py-2 rounded-xl bg-amber-600 hover:bg-amber-700 text-white font-bold text-xs shadow-xs transition-all flex items-center gap-2">
                            <i class="fa-solid fa-pen-to-square text-xs"></i>
                            <span>Edit / Ubah Data</span>
                        </button>
                    </div>

                </form>

            </div>

            <!-- Right 1 Col: Status & Panduan Widgets -->
            <div class="space-y-5">

                <!-- Status Keuangan Summary Card -->
                <div class="bg-[#051B44] p-5 rounded-2xl text-white space-y-4 shadow-xs">
                    <h4 class="text-xs font-bold text-sky-200/80 uppercase tracking-wider">STATUS KEUANGAN SAAT INI</h4>

                    <div class="space-y-3">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-semibold text-sky-100/70">Pemasukan</span>
                            <span class="text-xs font-extrabold text-emerald-400">+ {{ $kpis['incomeFormatted'] ?? 'Rp 0' }}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-semibold text-sky-100/70">Pengeluaran</span>
                            <span class="text-xs font-extrabold text-rose-400">- {{ $kpis['expenseFormatted'] ?? 'Rp 0' }}</span>
                        </div>
                        <hr class="border-white/10">
                        <div class="text-center">
                            <span class="text-2xl font-extrabold text-white">{{ ($saldo ?? 0) >= 0 ? 'Surplus / Sehat' : 'Defisit' }}</span>
                            <span class="text-xs text-sky-200/80 block mt-0.5">Saldo Bersih: {{ $kpis['saldoFormatted'] ?? 'Rp 0' }}</span>
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

    <!-- ========= DIRECTORY / LIST MODE ========= -->

    <!-- 4 Financial KPI Cards Grid -->
    <div x-show="!showForm" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">

        <!-- Card 1: Total Revenue -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs flex flex-col justify-between">
            <div class="flex items-center justify-between">
                <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400">TOTAL REVENUE (PEMASUKAN)</span>
                <span class="px-2.5 py-0.5 rounded-full text-[11px] font-extrabold bg-[#C6F6D5] text-[#22543D]">
                    <i class="fa-solid fa-arrow-trend-up text-[10px]"></i> Aktif
                </span>
            </div>
            <div class="mt-3">
                <h3 class="text-xl font-extrabold text-slate-900 tracking-tight">{{ $kpis['incomeFormatted'] ?? 'Rp 0' }}</h3>
            </div>
        </div>

        <!-- Card 2: Total Expenses -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs flex flex-col justify-between">
            <div class="flex items-center justify-between">
                <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400">TOTAL EXPENSES (BIAYA)</span>
                <span class="px-2.5 py-0.5 rounded-full text-[11px] font-extrabold bg-[#FEE2E2] text-[#991B1B]">
                    <i class="fa-solid fa-arrow-trend-down text-[10px]"></i> Terkendali
                </span>
            </div>
            <div class="mt-3">
                <h3 class="text-xl font-extrabold text-slate-900 tracking-tight">{{ $kpis['expenseFormatted'] ?? 'Rp 0' }}</h3>
            </div>
        </div>

        <!-- Card 3: Net Profit -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs flex flex-col justify-between">
            <div class="flex items-center justify-between">
                <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400">NET PROFIT (SALDO KAS)</span>
                <span class="px-2.5 py-0.5 rounded-full text-[11px] font-extrabold bg-[#E0F2FE] text-[#0284C7]">
                    <i class="fa-solid fa-chart-pie text-[10px]"></i> {{ $kpis['netMargin'] ?? 0 }}% Margin
                </span>
            </div>
            <div class="mt-3">
                <h3 class="text-xl font-extrabold text-slate-900 tracking-tight">{{ $kpis['saldoFormatted'] ?? 'Rp 0' }}</h3>
                <span class="text-[10px] font-medium {{ ($saldo ?? 0) >= 0 ? 'text-emerald-600' : 'text-rose-600' }} block mt-0.5">
                    {{ ($saldo ?? 0) >= 0 ? 'Surplus Kas Operasional' : 'Defisit Kas Operasional' }}
                </span>
            </div>
        </div>

        <!-- Card 4: Total Transaksi -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs flex flex-col justify-between">
            <div class="flex items-center justify-between">
                <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400">TOTAL TRANSAKSI</span>
                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold bg-[#E2E8F0] text-[#475569]">
                    {{ $kpis['totalTrx'] ?? 0 }} Transaksi
                </span>
            </div>
            <div class="mt-3">
                <h3 class="text-xl font-extrabold text-slate-900 tracking-tight">{{ $kpis['totalTrx'] ?? 0 }} <span class="text-xs font-semibold text-slate-500">Record</span></h3>
                <span class="text-[10px] font-medium text-slate-400 block mt-0.5">Tercatat di sistem buku kas</span>
            </div>
        </div>

    </div>

    <!-- Chart & Expense Breakdown (2 Columns) -->
    <div x-show="!showForm" class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <!-- Left 2 Cols: Monthly Cash Flow Bar Chart -->
        <div class="lg:col-span-2 bg-white p-6 rounded-2xl border border-slate-200/80 shadow-xs">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-base font-bold text-slate-900">Monthly Cash Flow</h3>

                <div class="flex items-center gap-4 text-xs font-medium">
                    <div class="flex items-center gap-1.5 text-slate-700">
                        <span class="w-2.5 h-2.5 rounded-full bg-[#0B2570]"></span>
                        <span>Revenue</span>
                    </div>
                    <div class="flex items-center gap-1.5 text-slate-700">
                        <span class="w-2.5 h-2.5 rounded-full bg-[#38BDF8]"></span>
                        <span>Expense</span>
                    </div>
                </div>
            </div>

            <div class="h-64 w-full">
                <canvas id="financialCashFlowChart"></canvas>
            </div>
        </div>

        <!-- Right 1 Col: Expense Distribution -->
        <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-xs flex flex-col justify-between space-y-6">
            <div>
                <h3 class="text-base font-bold text-slate-900 mb-4">Expense Distribution</h3>

                <div class="space-y-4 text-xs">

                    <!-- Item 1: Pakan -->
                    <div>
                        <div class="flex items-center justify-between font-bold text-slate-700 mb-1.5">
                            <span>Pakan (Feed)</span>
                            <span class="font-extrabold text-slate-900">{{ $kpis['pakanFormatted'] ?? 'Rp 0' }}</span>
                        </div>
                        <div class="w-full bg-slate-100 h-2 rounded-full overflow-hidden">
                            <div class="bg-[#0B2570] h-full rounded-full" style="width: {{ $totalExpense > 0 ? min(100, round(($kpis['pakanTotal'] ?? 0) / $totalExpense * 100)) : 0 }}%"></div>
                        </div>
                    </div>

                    <!-- Item 2: Operasional -->
                    <div>
                        <div class="flex items-center justify-between font-bold text-slate-700 mb-1.5">
                            <span>Operasional &amp; Perawatan</span>
                            <span class="font-extrabold text-slate-900">{{ $kpis['operasionalFormatted'] ?? 'Rp 0' }}</span>
                        </div>
                        <div class="w-full bg-slate-100 h-2 rounded-full overflow-hidden">
                            <div class="bg-[#10B981] h-full rounded-full" style="width: {{ $totalExpense > 0 ? min(100, round(($kpis['operasionalTotal'] ?? 0) / $totalExpense * 100)) : 0 }}%"></div>
                        </div>
                    </div>

                </div>
            </div>

            <!-- Financial Health Score Box -->
            <div class="bg-[#F4F7FA] border border-slate-200/70 rounded-xl p-4 flex items-center justify-between">
                <div>
                    <span class="text-[10px] font-bold text-slate-400 block uppercase tracking-wider">Financial Health Score</span>
                    <h4 class="text-lg font-extrabold text-[#0B2570] mt-0.5">{{ number_format($kpis['healthScore'] ?? 0, 1) }} <span class="text-xs font-semibold text-slate-400">/ 10</span></h4>
                </div>
                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold {{ $kpis['healthBadgeClass'] ?? 'bg-slate-100 text-slate-600' }} uppercase">
                    {{ $kpis['healthStatus'] ?? 'STABLE' }}
                </span>
            </div>
        </div>

    </div>

    <!-- Transaction History Table Card -->
    <div x-show="!showForm" class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
        <div class="p-5 border-b border-slate-100 flex items-center justify-between">
            <h3 class="text-base font-bold text-slate-900">Transaction History</h3>

            <div class="flex items-center gap-2 px-3 py-1.5 rounded-lg border border-slate-200 bg-white text-xs font-semibold text-slate-600 shadow-xs">
                <i class="fa-solid fa-list-check text-slate-400"></i>
                <span x-text="transactions.length + ' Total Transaksi'"></span>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50/60 border-b border-slate-100 text-[10px] font-extrabold text-slate-400 uppercase tracking-wider">
                        <th class="py-3 px-6">DATE</th>
                        <th class="py-3 px-6">DESCRIPTION</th>
                        <th class="py-3 px-6">CATEGORY</th>
                        <th class="py-3 px-6">KOLAM</th>
                        <th class="py-3 px-6">AMOUNT</th>
                        <th class="py-3 px-6 text-right">ACTION</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-xs font-medium text-slate-700">

                    <template x-if="transactions.length === 0">
                        <tr>
                            <td colspan="6" class="py-8 text-center text-slate-400 text-xs">
                                Belum ada transaksi keuangan yang tercatat. Klik tombol <strong>Add Transaction</strong> untuk menambah.
                            </td>
                        </tr>
                    </template>

                    <template x-for="transaction in transactions" :key="transaction.raw_id || transaction.id">
                        <tr class="hover:bg-slate-50/50 transition-colors">
                            <td class="py-4 px-6 text-slate-400 font-semibold" x-text="transaction.tanggal"></td>
                            <td class="py-4 px-6">
                                <div class="font-bold text-slate-900" x-text="transaction.keterangan || '-'"></div>
                                <div class="text-[10px] text-slate-400 font-semibold" x-text="transaction.ref"></div>
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
                                    <button @click="open = !open" @click.away="open = false" class="text-slate-400 hover:text-slate-600 p-1.5 rounded-lg hover:bg-slate-100 transition-colors">
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
                                        <button type="button" @click="open = false; openViewForm(transaction)" class="w-full px-3.5 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-50 flex items-center gap-2.5">
                                            <i class="fa-solid fa-eye text-sky-600 w-4"></i>
                                            <span>View Detail</span>
                                        </button>

                                        <!-- Edit / Update Button -->
                                        <button type="button" @click="open = false; openEditForm(transaction)" class="w-full px-3.5 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-50 flex items-center gap-2.5">
                                            <i class="fa-solid fa-pen-to-square text-amber-600 w-4"></i>
                                            <span>Edit / Ubah Data</span>
                                        </button>

                                        <div class="my-1 border-t border-slate-100"></div>

                                        <!-- Delete Button -->
                                        <button type="button" @click="open = false; deleteTransaction(transaction)" class="w-full px-3.5 py-2 text-xs font-semibold text-red-600 hover:bg-red-50 flex items-center gap-2.5">
                                            <i class="fa-solid fa-trash-can text-red-500 w-4"></i>
                                            <span>Hapus / Delete</span>
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
            <span>Menampilkan <strong class="text-slate-800" x-text="transactions.length"></strong> data transaksi</span>
            <div class="flex items-center gap-1">
                <button class="w-7 h-7 rounded bg-[#051B44] text-white font-bold flex items-center justify-center">1</button>
            </div>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
function keuanganComponent() {
    return {
        showForm: false,
        formMode: "create",
        tipeTransaksi: "income",
        isLoading: false,
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

    document.addEventListener('DOMContentLoaded', function() {
        const ctx = document.getElementById('financialCashFlowChart');
        if (!ctx) return;

        const labels = {!! json_encode($monthlyCashflow['labels'] ?? ['JAN', 'FEB', 'MAR', 'APR', 'MEI', 'JUN', 'JUL', 'AGU', 'SEP']) !!};
        const revenueData = {!! json_encode($monthlyCashflow['revenue'] ?? []) !!};
        const expenseData = {!! json_encode($monthlyCashflow['expense'] ?? []) !!};

        new Chart(ctx.getContext('2d'), {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [
                    {
                        label: 'Revenue (Jt)',
                        data: revenueData,
                        backgroundColor: '#0B2570',
                        borderRadius: 4,
                        barPercentage: 0.6,
                        categoryPercentage: 0.5
                    },
                    {
                        label: 'Expense (Jt)',
                        data: expenseData,
                        backgroundColor: '#38BDF8',
                        borderRadius: 4,
                        barPercentage: 0.6,
                        categoryPercentage: 0.5
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return context.dataset.label + ': Rp ' + context.raw + ' Juta';
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return value + ' Jt';
                            },
                            font: { family: 'Plus Jakarta Sans', size: 11, weight: '500' },
                            color: '#94A3B8'
                        },
                        grid: { color: '#F1F5F9' },
                        border: { display: false }
                    },
                    x: {
                        grid: { display: false },
                        ticks: { font: { family: 'Plus Jakarta Sans', size: 11, weight: '500' }, color: '#94A3B8' },
                        border: { display: false }
                    }
                }
            }
        });
    });
</script>
@endpush

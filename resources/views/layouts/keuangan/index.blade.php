@extends('layouts.app')

@section('title', 'Financial Management - SIM-BUDIDAYA')

@section('content')
<div class="space-y-6" x-data="{ showForm: false, tipeTransaksi: 'income' }">

    <!-- Subtitle & Page Title Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-extrabold text-[#0B2570] tracking-tight">Financial Management</h1>
            <p class="text-xs text-slate-500 font-medium mt-0.5">Monitor revenue streams, expenses, and overall aquaculture profitability.</p>
        </div>
        <div class="flex items-center gap-3">
            <button class="px-3.5 py-2 rounded-xl border border-slate-300 bg-white text-xs font-bold text-slate-700 shadow-xs hover:bg-slate-50 transition-colors flex items-center gap-2">
                <i class="fa-solid fa-download text-xs text-slate-500"></i>
                <span>Export Report</span>
            </button>
            <button @click="showForm = !showForm"
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

        <!-- Header -->
        <div class="bg-[#051B44] rounded-2xl p-6 sm:p-8 text-white shadow-xs">
            <h2 class="text-xl font-extrabold text-white">Pencatatan Keuangan</h2>
            <p class="text-xs text-sky-200/80 font-medium mt-1">Catat transaksi masuk (pemasukan) atau keluar (pengeluaran) untuk operasional budidaya. Pastikan referensi yang jelas dengan ID lain yang sesuai.</p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            <!-- Left 2 Cols: Form Fields -->
            <div class="lg:col-span-2 bg-white p-6 sm:p-8 rounded-2xl border border-slate-200/80 shadow-xs space-y-6">

                <form action="#" method="POST" @submit.prevent class="space-y-6">

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
                            <div class="flex items-center gap-2 p-1 bg-slate-100 rounded-xl max-w-sm text-xs font-bold">
                                <button type="button" @click="tipeTransaksi = 'income'"
                                        :class="tipeTransaksi === 'income' ? 'bg-[#051B44] text-white shadow-xs' : 'text-slate-600 hover:text-slate-900'"
                                        class="flex-1 py-2 rounded-lg transition-all text-center flex items-center justify-center gap-1.5">
                                    <i class="fa-solid fa-arrow-down text-[10px]"></i> Pemasukan (Income)
                                </button>
                                <button type="button" @click="tipeTransaksi = 'expense'"
                                        :class="tipeTransaksi === 'expense' ? 'bg-[#051B44] text-white shadow-xs' : 'text-slate-600 hover:text-slate-900'"
                                        class="flex-1 py-2 rounded-lg transition-all text-center flex items-center justify-center gap-1.5">
                                    <i class="fa-solid fa-arrow-up text-[10px]"></i> Pengeluaran (Expense)
                                </button>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="text-[10px] font-extrabold uppercase tracking-wider text-slate-500 block mb-1">ID KEUANGAN (Auto generated)</label>
                                <input type="text" value="#TRX-202310-0482" readonly
                                       class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs font-extrabold text-slate-500 bg-slate-100 cursor-not-allowed">
                            </div>
                            <div>
                                <label class="text-[10px] font-extrabold uppercase tracking-wider text-slate-500 block mb-1">TANGGAL TRANSAKSI</label>
                                <input type="date" value="2026-08-06"
                                       class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs font-semibold text-slate-700 bg-slate-50/70 focus:bg-white focus:outline-none focus:ring-2 focus:ring-sky-500 transition-all">
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
                            <label class="text-[10px] font-extrabold uppercase tracking-wider text-slate-500 block mb-1">NOMINAL (Rp)</label>
                            <div class="relative">
                                <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-xs font-bold text-slate-400">Rp</span>
                                <input type="text" placeholder="0"
                                       class="w-full pl-10 pr-3.5 py-3 rounded-xl border border-slate-200 text-lg font-extrabold text-slate-900 bg-slate-50/70 focus:bg-white focus:outline-none focus:ring-2 focus:ring-sky-500 transition-all">
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="text-[10px] font-extrabold uppercase tracking-wider text-slate-500 block mb-1">KATEGORI</label>
                                <select class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs font-semibold text-slate-700 bg-slate-50/70 focus:bg-white focus:outline-none focus:ring-2 focus:ring-sky-500 transition-all">
                                    <option>Pilih Kategori...</option>
                                    <option>Pakan</option>
                                    <option>Bibit</option>
                                    <option>Operasional</option>
                                    <option>Penjualan</option>
                                    <option>Transportasi</option>
                                </select>
                            </div>
                            <div>
                                <label class="text-[10px] font-extrabold uppercase tracking-wider text-slate-500 block mb-1">REF ID / No. NOTA</label>
                                <input type="text" placeholder="Contoh: INV/2023/10/099"
                                       class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs font-semibold text-slate-700 bg-slate-50/70 focus:bg-white focus:outline-none focus:ring-2 focus:ring-sky-500 transition-all">
                            </div>
                        </div>

                        <div>
                            <label class="text-[10px] font-extrabold uppercase tracking-wider text-slate-500 block mb-1">ALOKASI KOLAM (OPSIONAL)</label>
                            <select class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs font-semibold text-slate-700 bg-slate-50/70 focus:bg-white focus:outline-none focus:ring-2 focus:ring-sky-500 transition-all">
                                <option>Tidak dialokasikan ke kolam spesifik</option>
                                <option>Kolam A-01</option>
                                <option>Kolam A-02</option>
                                <option>Kolam B-01</option>
                            </select>
                        </div>

                        <div>
                            <label class="text-[10px] font-extrabold uppercase tracking-wider text-slate-500 block mb-1">KETERANGAN / DESKRIPSI</label>
                            <textarea rows="3" placeholder="Tambahkan catatan khusus terkait transaksi ini..."
                                      class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs font-semibold text-slate-700 bg-slate-50/70 focus:bg-white focus:outline-none focus:ring-2 focus:ring-sky-500 transition-all"></textarea>
                        </div>
                    </div>

                    <!-- Form Actions -->
                    <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
                        <button type="button" @click="showForm = false"
                                class="px-5 py-2 rounded-xl border border-slate-200 text-xs font-bold text-slate-600 hover:bg-slate-50 transition-colors">
                            Batalkan
                        </button>
                        <button type="submit"
                                class="px-5 py-2 rounded-xl bg-[#051B44] hover:bg-navy-900 text-white font-bold text-xs shadow-xs transition-all flex items-center gap-2">
                            <i class="fa-solid fa-floppy-disk text-xs"></i>
                            <span>Simpan Transaksi</span>
                        </button>
                    </div>

                </form>

            </div>

            <!-- Right 1 Col: Status & Panduan Widgets -->
            <div class="space-y-5">

                <!-- Status Keuangan Summary Card -->
                <div class="bg-[#051B44] p-5 rounded-2xl text-white space-y-4 shadow-xs">
                    <h4 class="text-xs font-bold text-sky-200/80 uppercase tracking-wider">STATUS KEUANGAN BULAN INI</h4>

                    <div class="space-y-3">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-semibold text-sky-100/70">Pemasukan</span>
                            <span class="text-xs font-extrabold text-emerald-400">+ Rp 44.2M</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-semibold text-sky-100/70">Pengeluaran</span>
                            <span class="text-xs font-extrabold text-rose-400">- Rp 16.5M</span>
                        </div>
                        <hr class="border-white/10">
                        <div class="text-center">
                            <span class="text-2xl font-extrabold text-white">Sehat</span>
                            <span class="text-xs text-sky-200/80 block mt-0.5">Saldo positif terhadap target bulanan</span>
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
                            <span>Pastikan tipe transaksi sesuai dengan tarif hak atas masa sana.</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <span class="w-4 h-4 rounded-full bg-sky-200/80 text-sky-700 flex items-center justify-center shrink-0 text-[9px] font-extrabold mt-0.5">2</span>
                            <span>Gunakan Ref ID yang valid seperti Nomor Invoice, Nota, atau Kode Struk untuk memudahkan audit.</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <span class="w-4 h-4 rounded-full bg-sky-200/80 text-sky-700 flex items-center justify-center shrink-0 text-[9px] font-extrabold mt-0.5">3</span>
                            <span>Alokasikan transaksi ke Kolam tertentu hanya jika 100% terkait kolam tersebut.</span>
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
                <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400">TOTAL REVENUE</span>
                <span class="px-2.5 py-0.5 rounded-full text-[11px] font-extrabold bg-[#C6F6D5] text-[#22543D]">
                    <i class="fa-solid fa-arrow-trend-up text-[10px]"></i> +12.5%
                </span>
            </div>
            <div class="mt-3">
                <h3 class="text-xl font-extrabold text-slate-900 tracking-tight">Rp1.450.000.000</h3>
            </div>
        </div>

        <!-- Card 2: Total Expenses -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs flex flex-col justify-between">
            <div class="flex items-center justify-between">
                <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400">TOTAL EXPENSES</span>
                <span class="px-2.5 py-0.5 rounded-full text-[11px] font-extrabold bg-[#FEE2E2] text-[#991B1B]">
                    <i class="fa-solid fa-arrow-trend-down text-[10px]"></i> -4.2%
                </span>
            </div>
            <div class="mt-3">
                <h3 class="text-xl font-extrabold text-slate-900 tracking-tight">Rp842.200.000</h3>
            </div>
        </div>

        <!-- Card 3: Net Profit -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs flex flex-col justify-between">
            <div class="flex items-center justify-between">
                <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400">NET PROFIT</span>
                <span class="px-2.5 py-0.5 rounded-full text-[11px] font-extrabold bg-[#E0F2FE] text-[#0284C7]">
                    <i class="fa-solid fa-chart-pie text-[10px]"></i> +18.0%
                </span>
            </div>
            <div class="mt-3">
                <h3 class="text-xl font-extrabold text-slate-900 tracking-tight">Rp607.800.000</h3>
                <span class="text-[10px] font-medium text-slate-400 block mt-0.5">(+38.4% vs last quarter)</span>
            </div>
        </div>

        <!-- Card 4: Pending Payments -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs flex flex-col justify-between">
            <div class="flex items-center justify-between">
                <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400">PENDING PAYMENTS</span>
                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold bg-[#E2E8F0] text-[#475569]">
                    12 Items
                </span>
            </div>
            <div class="mt-3">
                <h3 class="text-xl font-extrabold text-slate-900 tracking-tight">Rp125.400.000</h3>
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
                            <span class="font-extrabold text-slate-900">Rp 450.2M</span>
                        </div>
                        <div class="w-full bg-slate-100 h-2 rounded-full overflow-hidden">
                            <div class="bg-[#0B2570] h-full rounded-full w-[60%]"></div>
                        </div>
                    </div>

                    <!-- Item 2: Bibit -->
                    <div>
                        <div class="flex items-center justify-between font-bold text-slate-700 mb-1.5">
                            <span>Bibit (Seed)</span>
                            <span class="font-extrabold text-slate-900">Rp 120.5M</span>
                        </div>
                        <div class="w-full bg-slate-100 h-2 rounded-full overflow-hidden">
                            <div class="bg-[#38BDF8] h-full rounded-full w-[25%]"></div>
                        </div>
                    </div>

                    <!-- Item 3: Operasional -->
                    <div>
                        <div class="flex items-center justify-between font-bold text-slate-700 mb-1.5">
                            <span>Operasional</span>
                            <span class="font-extrabold text-slate-900">Rp 271.5M</span>
                        </div>
                        <div class="w-full bg-slate-100 h-2 rounded-full overflow-hidden">
                            <div class="bg-[#10B981] h-full rounded-full w-[40%]"></div>
                        </div>
                    </div>

                </div>
            </div>

            <!-- Financial Health Score Box -->
            <div class="bg-[#F4F7FA] border border-slate-200/70 rounded-xl p-4 flex items-center justify-between">
                <div>
                    <span class="text-[10px] font-bold text-slate-400 block uppercase tracking-wider">Financial Health Score</span>
                    <h4 class="text-lg font-extrabold text-[#0B2570] mt-0.5">8.4 <span class="text-xs font-semibold text-slate-400">/ 10</span></h4>
                </div>
                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold bg-[#C6F6D5] text-[#22543D] uppercase">
                    STABLE
                </span>
            </div>
        </div>

    </div>

    <!-- Transaction History Table Card -->
    <div x-show="!showForm" class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
        <div class="p-5 border-b border-slate-100 flex items-center justify-between">
            <h3 class="text-base font-bold text-slate-900">Transaction History</h3>

            <div class="flex items-center gap-2 px-3 py-1.5 rounded-lg border border-slate-200 bg-white text-xs font-semibold text-slate-600 shadow-xs cursor-pointer">
                <span>This Month</span>
                <i class="fa-solid fa-chevron-down text-[10px] text-slate-400 ml-1"></i>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50/60 border-b border-slate-100 text-[10px] font-extrabold text-slate-400 uppercase tracking-wider">
                        <th class="py-3 px-6">DATE</th>
                        <th class="py-3 px-6">DESCRIPTION</th>
                        <th class="py-3 px-6">CATEGORY</th>
                        <th class="py-3 px-6">AMOUNT</th>
                        <th class="py-3 px-6 text-right">ACTION</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-xs font-medium text-slate-700">

                    <!-- Row 1 -->
                    <tr class="hover:bg-slate-50/50 transition-colors">
                        <td class="py-4 px-6 text-slate-400 font-semibold">Oct 24, 2023</td>
                        <td class="py-4 px-6 font-bold text-slate-900">Bulk Feed Purchase - Grade A</td>
                        <td class="py-4 px-6">
                            <span class="px-2.5 py-0.5 rounded-full text-[11px] font-extrabold bg-[#E0F2FE] text-[#0284C7]">
                                Pakan
                            </span>
                        </td>
                        <td class="py-4 px-6 font-extrabold text-rose-600">- Rp 45.000.000</td>
                        <td class="py-4 px-6 text-right">
                            <button class="text-slate-400 hover:text-slate-600 p-1">
                                <i class="fa-solid fa-ellipsis-vertical text-sm"></i>
                            </button>
                        </td>
                    </tr>

                    <!-- Row 2 -->
                    <tr class="hover:bg-slate-50/50 transition-colors">
                        <td class="py-4 px-6 text-slate-400 font-semibold">Oct 22, 2023</td>
                        <td class="py-4 px-6 font-bold text-slate-900">Harvest Sales - Pool 04 (Mitra B)</td>
                        <td class="py-4 px-6">
                            <span class="px-2.5 py-0.5 rounded-full text-[11px] font-extrabold bg-[#C6F6D5] text-[#22543D]">
                                Penjualan
                            </span>
                        </td>
                        <td class="py-4 px-6 font-extrabold text-emerald-600">+ Rp128.500.000</td>
                        <td class="py-4 px-6 text-right">
                            <button class="text-slate-400 hover:text-slate-600 p-1">
                                <i class="fa-solid fa-ellipsis-vertical text-sm"></i>
                            </button>
                        </td>
                    </tr>

                    <!-- Row 3 -->
                    <tr class="hover:bg-slate-50/50 transition-colors">
                        <td class="py-4 px-6 text-slate-400 font-semibold">Oct 20, 2023</td>
                        <td class="py-4 px-6 font-bold text-slate-900">New Fingerling Stock - Catfish</td>
                        <td class="py-4 px-6">
                            <span class="px-2.5 py-0.5 rounded-full text-[11px] font-extrabold bg-[#E0F2FE] text-[#0284C7]">
                                Bibit
                            </span>
                        </td>
                        <td class="py-4 px-6 font-extrabold text-rose-600">- Rp 12.400.000</td>
                        <td class="py-4 px-6 text-right">
                            <button class="text-slate-400 hover:text-slate-600 p-1">
                                <i class="fa-solid fa-ellipsis-vertical text-sm"></i>
                            </button>
                        </td>
                    </tr>

                    <!-- Row 4 -->
                    <tr class="hover:bg-slate-50/50 transition-colors">
                        <td class="py-4 px-6 text-slate-400 font-semibold">Oct 18, 2023</td>
                        <td class="py-4 px-6 font-bold text-slate-900">Electricity &amp; Water Utilities</td>
                        <td class="py-4 px-6">
                            <span class="px-2.5 py-0.5 rounded-full text-[11px] font-extrabold bg-[#E2E8F0] text-[#475569]">
                                Operasional
                            </span>
                        </td>
                        <td class="py-4 px-6 font-extrabold text-rose-600">- Rp 5.200.000</td>
                        <td class="py-4 px-6 text-right">
                            <button class="text-slate-400 hover:text-slate-600 p-1">
                                <i class="fa-solid fa-ellipsis-vertical text-sm"></i>
                            </button>
                        </td>
                    </tr>

                </tbody>
            </table>
        </div>

        <!-- Table Footer Pagination -->
        <div class="p-4 border-t border-slate-100 flex flex-col sm:flex-row items-center justify-between gap-4 text-xs font-medium text-slate-500">
            <span>Showing 4 of 240 transactions</span>
            <div class="flex items-center gap-1">
                <button class="px-3 py-1.5 rounded border border-slate-200 text-slate-600 hover:bg-slate-50 font-semibold text-xs">Previous</button>
                <button class="w-7 h-7 rounded bg-[#051B44] text-white font-bold flex items-center justify-center">1</button>
                <button class="w-7 h-7 rounded border border-slate-200 flex items-center justify-center text-slate-600 hover:bg-slate-50">2</button>
                <button class="px-3 py-1.5 rounded border border-slate-200 text-slate-600 hover:bg-slate-50 font-semibold text-xs">Next</button>
            </div>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const ctx = document.getElementById('financialCashFlowChart');
        if (!ctx) return;
        new Chart(ctx.getContext('2d'), {
            type: 'bar',
            data: {
                labels: ['JAN', 'FEB', 'MAR', 'APR', 'MEI', 'JUN'],
                datasets: [
                    {
                        label: 'Revenue',
                        data: [250, 310, 280, 360, 420, 380],
                        backgroundColor: '#0B2570',
                        borderRadius: 4,
                        barPercentage: 0.6,
                        categoryPercentage: 0.5
                    },
                    {
                        label: 'Expense',
                        data: [180, 210, 190, 240, 260, 290],
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
                    }
                },
                scales: {
                    y: {
                        min: 0,
                        max: 500,
                        ticks: {
                            stepSize: 250,
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

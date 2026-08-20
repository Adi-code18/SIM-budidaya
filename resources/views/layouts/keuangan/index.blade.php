@extends('layouts.app')

@section('title', 'Financial Management - SIM-BUDIDAYA')

@section('content')
<div class="space-y-6" x-data='{
    showForm: false,
    formMode: "create",
    tipeTransaksi: "income",
    transactions: [
        { id: "#TRX-202310-0482", tanggal: "2026-08-06", tipe: "income", nominal: 45000000, kategori: "Pakan", ref: "INV/2023/10/099", kolam: "Kolam A-01", keterangan: "Pembelian pakan harian" },
        { id: "#TRX-202310-0483", tanggal: "2026-08-05", tipe: "income", nominal: 128500000, kategori: "Penjualan", ref: "SO-2458", kolam: "Kolam B-02", keterangan: "Penjualan hasil panen mitra B" },
        { id: "#TRX-202310-0484", tanggal: "2026-08-04", tipe: "expense", nominal: 12400000, kategori: "Bibit", ref: "PO-2204", kolam: "Kolam A-02", keterangan: "Pembelian bibit ikan" },
        { id: "#TRX-202310-0485", tanggal: "2026-08-03", tipe: "expense", nominal: 5200000, kategori: "Operasional", ref: "UTIL-88", kolam: "Tidak dialokasikan", keterangan: "Listrik dan air" }
    ],
    form: {
        id: "",
        tanggal: "",
        tipe: "income",
        nominal: "",
        kategori: "",
        ref: "",
        kolam: "Tidak dialokasikan",
        keterangan: ""
    },

    formatCurrency(value) {
        return new Intl.NumberFormat("id-ID", { style: "currency", currency: "IDR", maximumFractionDigits: 0 }).format(value || 0);
    },

    openCreateForm() {
        this.formMode = "create";
        this.showForm = true;
        this.tipeTransaksi = "income";
        this.form = {
            id: "#TRX-" + new Date().getFullYear() + "-" + String(Math.floor(1000 + Math.random() * 9000)),
            tanggal: "",
            tipe: "income",
            nominal: "",
            kategori: "",
            ref: "",
            kolam: "Tidak dialokasikan",
            keterangan: ""
        };
    },

    openViewForm(item) {
        this.formMode = "view";
        this.showForm = true;
        this.tipeTransaksi = item.tipe;
        this.form = { ...item };
    },

    openEditForm(item) {
        this.formMode = "edit";
        this.showForm = true;
        this.tipeTransaksi = item.tipe;
        this.form = { ...item };
    },

    saveForm() {
        const payload = {
            ...this.form,
            tipe: this.tipeTransaksi,
            nominal: Number(this.form.nominal || 0)
        };

        if (this.formMode === "create") {
            this.transactions.unshift(payload);
        } else if (this.formMode === "edit") {
            const index = this.transactions.findIndex(t => t.id === payload.id);
            if (index !== -1) {
                this.transactions[index] = payload;
            }
        }

        this.showForm = false;
    },

    deleteTransaction(item) {
        if (confirm("Apakah Anda yakin ingin menghapus transaksi \"" + item.ref + "\"?")) {
            this.transactions = this.transactions.filter(t => t.id !== item.id);
        }
    },

    exportReport() {
        const header = ["DATE", "DESCRIPTION", "CATEGORY", "TYPE", "AMOUNT", "REF"];
        const rows = this.transactions.map(item => [
            item.tanggal,
            item.keterangan,
            item.kategori,
            item.tipe === "income" ? "Pemasukan" : "Pengeluaran",
            item.nominal,
            item.ref
        ]);
        const csv = [header, ...rows]
            .map(row => row.map(value => "\"" + String(value).replace(/"/g, "\"\"") + "\"").join(","))
            .join("\n");

        const blob = new Blob([csv], { type: "text/csv;charset=utf-8;" });
        const url = URL.createObjectURL(blob);
        const link = document.createElement("a");
        link.href = url;
        link.download = "financial_report.csv";
        document.body.appendChild(link);
        link.click();
        link.remove();
        URL.revokeObjectURL(url);
    }
}' >

    <!-- Subtitle & Page Title Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-extrabold text-[#0B2570] tracking-tight">Financial Management</h1>
            <p class="text-xs text-slate-500 font-medium mt-0.5">Monitor revenue streams, expenses, and overall aquaculture profitability.</p>
        </div>
        <div class="flex items-center gap-3">
            <button @click="exportReport()" class="px-3.5 py-2 rounded-xl border border-slate-300 bg-white text-xs font-bold text-slate-700 shadow-xs hover:bg-slate-50 transition-colors flex items-center gap-2">
                <i class="fa-solid fa-download text-xs text-slate-500"></i>
                <span>Export Report</span>
            </button>
            <button @click="openCreateForm()"
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
                            <div class="flex items-center gap-2 p-1 bg-slate-100 rounded-xl max-w-sm text-xs font-bold" :class="formMode === 'view' ? 'opacity-70 pointer-events-none' : ''">
                                <button type="button" @click="if (formMode !== 'view') { tipeTransaksi = 'income'; form.tipe = 'income'; }"
                                        :class="tipeTransaksi === 'income' ? 'bg-[#051B44] text-white shadow-xs' : 'text-slate-600 hover:text-slate-900'"
                                        :disabled="formMode === 'view'"
                                        class="flex-1 py-2 rounded-lg transition-all text-center flex items-center justify-center gap-1.5">
                                    <i class="fa-solid fa-arrow-down text-[10px]"></i> Pemasukan (Income)
                                </button>
                                <button type="button" @click="if (formMode !== 'view') { tipeTransaksi = 'expense'; form.tipe = 'expense'; }"
                                        :class="tipeTransaksi === 'expense' ? 'bg-[#051B44] text-white shadow-xs' : 'text-slate-600 hover:text-slate-900'"
                                        :disabled="formMode === 'view'"
                                        class="flex-1 py-2 rounded-lg transition-all text-center flex items-center justify-center gap-1.5">
                                    <i class="fa-solid fa-arrow-up text-[10px]"></i> Pengeluaran (Expense)
                                </button>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="text-[10px] font-extrabold uppercase tracking-wider text-slate-500 block mb-1">ID KEUANGAN</label>
                                <input type="text" x-model="form.id" readonly
                                       :disabled="formMode === 'view'"
                                       class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs font-extrabold text-slate-500 bg-slate-100 cursor-not-allowed">
                            </div>
                            <div>
                                <label class="text-[10px] font-extrabold uppercase tracking-wider text-slate-500 block mb-1">TANGGAL TRANSAKSI</label>
                                <input type="date" x-model="form.tanggal" :disabled="formMode === 'view'"
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
                                <input type="number" x-model="form.nominal" :disabled="formMode === 'view'" placeholder="0"
                                       class="w-full pl-10 pr-3.5 py-3 rounded-xl border border-slate-200 text-lg font-extrabold text-slate-900 bg-slate-50/70 focus:bg-white focus:outline-none focus:ring-2 focus:ring-sky-500 transition-all">
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="text-[10px] font-extrabold uppercase tracking-wider text-slate-500 block mb-1">KATEGORI</label>
                                <select x-model="form.kategori" :disabled="formMode === 'view'" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs font-semibold text-slate-700 bg-slate-50/70 focus:bg-white focus:outline-none focus:ring-2 focus:ring-sky-500 transition-all">
                                    <option value="">Pilih Kategori...</option>
                                    <option>Pakan</option>
                                    <option>Bibit</option>
                                    <option>Operasional</option>
                                    <option>Penjualan</option>
                                    <option>Transportasi</option>
                                </select>
                            </div>
                            <div>
                                <label class="text-[10px] font-extrabold uppercase tracking-wider text-slate-500 block mb-1">REF ID / No. NOTA</label>
                                <input type="text" x-model="form.ref" :disabled="formMode === 'view'" placeholder="Contoh: INV/2023/10/099"
                                       class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs font-semibold text-slate-700 bg-slate-50/70 focus:bg-white focus:outline-none focus:ring-2 focus:ring-sky-500 transition-all">
                            </div>
                        </div>

                        <div>
                            <label class="text-[10px] font-extrabold uppercase tracking-wider text-slate-500 block mb-1">ALOKASI KOLAM (OPSIONAL)</label>
                            <select x-model="form.kolam" :disabled="formMode === 'view'" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs font-semibold text-slate-700 bg-slate-50/70 focus:bg-white focus:outline-none focus:ring-2 focus:ring-sky-500 transition-all">
                                <option>Tidak dialokasikan ke kolam spesifik</option>
                                <option>Kolam A-01</option>
                                <option>Kolam A-02</option>
                                <option>Kolam B-01</option>
                                <option>Kolam B-02</option>
                            </select>
                        </div>

                        <div>
                            <label class="text-[10px] font-extrabold uppercase tracking-wider text-slate-500 block mb-1">KETERANGAN / DESKRIPSI</label>
                            <textarea rows="3" x-model="form.keterangan" :disabled="formMode === 'view'" placeholder="Tambahkan catatan khusus terkait transaksi ini..."
                                      class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs font-semibold text-slate-700 bg-slate-50/70 focus:bg-white focus:outline-none focus:ring-2 focus:ring-sky-500 transition-all"></textarea>
                        </div>
                    </div>

                    <!-- Form Actions -->
                    <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
                        <button type="button" @click="showForm = false; formMode = 'create'"
                                class="px-5 py-2 rounded-xl border border-slate-200 text-xs font-bold text-slate-600 hover:bg-slate-50 transition-colors">
                            <span x-text="formMode === 'view' ? 'Tutup' : 'Batalkan'"></span>
                        </button>

                        <button x-show="formMode !== 'view'" type="submit" @click="saveForm()"
                                class="px-5 py-2 rounded-xl bg-[#051B44] hover:bg-navy-900 text-white font-bold text-xs shadow-xs transition-all flex items-center gap-2">
                            <i class="fa-solid fa-floppy-disk text-xs"></i>
                            <span x-text="formMode === 'create' ? 'Simpan Transaksi' : 'Simpan Perubahan'"></span>
                        </button>

                        <button x-show="formMode === 'view'" type="button" @click="formMode = 'edit'"
                                class="px-5 py-2 rounded-xl bg-amber-600 hover:bg-amber-700 text-white font-bold text-xs shadow-xs transition-all flex items-center gap-2">
                            <i class="fa-solid fa-pen-to-square text-xs"></i>
                            <span>Update</span>
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

                    <template x-for="transaction in transactions" :key="transaction.id">
                        <tr class="hover:bg-slate-50/50 transition-colors">
                            <td class="py-4 px-6 text-slate-400 font-semibold" x-text="transaction.tanggal"></td>
                            <td class="py-4 px-6 font-bold text-slate-900" x-text="transaction.keterangan"></td>
                            <td class="py-4 px-6">
                                <span class="px-2.5 py-0.5 rounded-full text-[11px] font-extrabold"
                                      :class="transaction.tipe === 'income' ? 'bg-[#C6F6D5] text-[#22543D]' : 'bg-[#E0F2FE] text-[#0284C7]'"
                                      x-text="transaction.kategori"></span>
                            </td>
                            <td class="py-4 px-6 font-extrabold" :class="transaction.tipe === 'income' ? 'text-emerald-600' : 'text-rose-600'"
                                x-text="(transaction.tipe === 'income' ? '+ ' : '- ') + formatCurrency(transaction.nominal)"></td>
                            <td class="py-4 px-6 text-right">
                                <div class="relative inline-block text-left" x-data="{ open: false }">
                                    <button @click="open = !open" @click.away="open = false" class="text-slate-400 hover:text-slate-600 p-1">
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
                                        <button @click="open = false; openViewForm(transaction)" class="w-full px-3.5 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-50 flex items-center gap-2.5">
                                            <i class="fa-solid fa-eye text-sky-600 w-4"></i>
                                            <span>View</span>
                                        </button>
                                        <button @click="open = false; openEditForm(transaction)" class="w-full px-3.5 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-50 flex items-center gap-2.5">
                                            <i class="fa-solid fa-pen-to-square text-amber-600 w-4"></i>
                                            <span>Update</span>
                                        </button>
                                        <div class="my-1 border-t border-slate-100"></div>
                                        <button @click="open = false; deleteTransaction(transaction)" class="w-full px-3.5 py-2 text-xs font-semibold text-red-600 hover:bg-red-50 flex items-center gap-2.5">
                                            <i class="fa-solid fa-trash-can text-red-500 w-4"></i>
                                            <span>Delete</span>
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

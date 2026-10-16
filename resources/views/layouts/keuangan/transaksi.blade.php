@extends('layouts.app')

@section('title', 'Buku Kas & Transaksi Keuangan - AMS BUDIDAYA')

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

    <!-- =========================================================================
         KOP SURAT RESMI (HANYA MUNCUL SAAT CETAK / PRINT)
         ========================================================================= -->
    <div class="hidden print:block mb-6 pb-4 border-b-2 border-slate-900">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-4">
                <img src="{{ asset('images/Logo aquafarm.png') }}" alt="Logo AMS" class="h-16 w-auto object-contain shrink-0">
                <div>
                    <h1 class="text-xl font-extrabold uppercase tracking-wide text-slate-900 leading-tight">AMS BUDIDAYA INDONESIA</h1>
                    <p class="text-xs font-bold text-slate-700">Unit Bisnis Perikanan Terpadu & Akuakultur Modern Minapolitan</p>
                    <p class="text-[10px] text-slate-500 leading-tight mt-0.5">Jl. Raya Minapolitan Perikanan No. 88, Jawa Barat | Telp: +62 812-8899-0011 | Email: finance@amsbudidaya.id</p>
                </div>
            </div>
            <div class="text-right text-[10px] text-slate-600">
                <div class="font-extrabold text-xs text-slate-900 uppercase">BUKU KAS OPERASIONAL</div>
                <div class="font-mono text-slate-700">No: JRN-KAS/{{ date('Ym') }}</div>
                <div>Tgl Cetak: {{ \Carbon\Carbon::now()->translatedFormat('d F Y, H:i') }} WIB</div>
                <div>Oleh: {{ Auth::user()->nama ?? 'Manajer AMS Budidaya' }}</div>
            </div>
        </div>
        <div class="border-t border-slate-400 mt-3 pt-2 text-center">
            <h2 class="text-base font-extrabold uppercase tracking-wider text-slate-900">BUKU KAS &amp; MUTASI TRANSAKSI (CASH LEDGER)</h2>
            <p class="text-[11px] font-semibold text-slate-600">Rekapitulasi Arus Kas Debit (Penerimaan), Kredit (Pengeluaran) &amp; Saldo Berjalan</p>
        </div>
    </div>

    <!-- Header Section -->
    <div class="no-print flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2">
                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold uppercase bg-sky-100 text-sky-700">Farm Cash Ledger</span>
                <span class="text-xs font-bold text-slate-400">• Buku Kas &amp; Saldo Berjalan</span>
            </div>
            <h1 class="text-2xl font-extrabold text-[#0B2570] tracking-tight mt-1">Buku Kas &amp; Transaksi Keuangan</h1>
            <p class="text-xs text-slate-500 font-medium mt-0.5">Pencatatan kas operasional terstruktur dengan kolom Debit (Masuk), Kredit (Keluar), dan Saldo Berjalan.</p>
        </div>
        <div class="flex items-center gap-3">
            <button onclick="window.print()"
                    class="px-4 py-2 rounded-xl bg-white hover:bg-slate-50 text-slate-700 border border-slate-200 font-bold text-xs shadow-2xs transition-all flex items-center gap-2 cursor-pointer">
                <i class="fa-solid fa-print text-slate-400"></i>
                <span>Cetak Buku Kas</span>
            </button>
            <button @click="showForm ? (showForm = false) : openCreateForm()"
                    class="px-4 py-2 rounded-xl bg-[#0B2570] hover:bg-[#07194D] text-white font-bold text-xs shadow-xs transition-all flex items-center gap-2 cursor-pointer">
                <i class="fa-solid" :class="showForm ? 'fa-table-list' : 'fa-plus'"></i>
                <span x-text="showForm ? 'Tutup Form' : 'Catat Transaksi Baru'"></span>
            </button>
        </div>
    </div>

    <!-- Quick Balance Summary Mini Strip (Debit, Kredit, Saldo) -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-xs flex items-center justify-between">
            <div>
                <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400 block">TOTAL PENERIMAAN KAS (DEBIT)</span>
                <span class="text-lg font-extrabold text-emerald-600 tracking-tight">Rp {{ number_format($totalIncome, 0, ',', '.') }}</span>
                <span class="text-[10px] text-slate-400 font-medium block mt-0.5">Penjualan panen &amp; hasil budidaya</span>
            </div>
            <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0">
                <i class="fa-solid fa-arrow-down-long text-sm"></i>
            </div>
        </div>
        <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-xs flex items-center justify-between">
            <div>
                <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400 block">TOTAL PENGELUARAN KAS (KREDIT)</span>
                <span class="text-lg font-extrabold text-rose-600 tracking-tight">Rp {{ number_format($totalExpense, 0, ',', '.') }}</span>
                <span class="text-[10px] text-slate-400 font-medium block mt-0.5">HPP pakan, bibit &amp; beban operasional</span>
            </div>
            <div class="w-10 h-10 rounded-xl bg-rose-50 text-rose-600 flex items-center justify-center shrink-0">
                <i class="fa-solid fa-arrow-up-long text-sm"></i>
            </div>
        </div>
        <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-xs flex items-center justify-between">
            <div>
                <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400 block">SALDO KAS BERJALAN (NET BALANCE)</span>
                <span class="text-lg font-extrabold {{ $saldo >= 0 ? 'text-[#0B2570]' : 'text-rose-600' }} tracking-tight">
                    {{ ($saldo >= 0 ? '+ ' : '- ') }}Rp {{ number_format(abs($saldo), 0, ',', '.') }}
                </span>
                <span class="text-[10px] font-bold {{ $saldo >= 0 ? 'text-emerald-600' : 'text-rose-600' }} block mt-0.5">
                    {{ $saldo >= 0 ? 'Kas Surplus & Likuid' : 'Defisit Kas Operasional' }}
                </span>
            </div>
            <div class="w-10 h-10 rounded-xl bg-sky-50 text-[#0B2570] flex items-center justify-center shrink-0">
                <i class="fa-solid fa-wallet text-sm"></i>
            </div>
        </div>
    </div>

    <!-- ========= INPUT FORM SECTION ========= -->
    <div x-show="showForm"
         x-cloak
         style="display: none;"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 -translate-y-2"
         x-transition:enter-end="opacity-100 translate-y-0"
         class="space-y-5">

        <!-- Header Form -->
        <div class="bg-[#0B2570] rounded-2xl p-6 sm:p-8 text-white shadow-xs">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-xl font-extrabold text-white" x-text="formMode === 'view' ? 'Detail Transaksi Kas' : (formMode === 'edit' ? 'Edit Transaksi Kas' : 'Pencatatan Transaksi Kas Baru')"></h2>
                    <p class="text-xs text-sky-200/80 font-medium mt-1" x-text="formMode === 'view' ? 'Melihat rincian mutasi kas, kategori akun, dan alokasi kolam.' : 'Catat penerimaan (debit) atau pengeluaran (kredit) untuk pembukuan operasional budidaya.'"></p>
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
                            <div class="w-8 h-8 rounded-xl bg-[#0B2570] text-white flex items-center justify-center">
                                <i class="fa-solid fa-circle-info text-xs"></i>
                            </div>
                            <span>Tipe Arus Kas &amp; Referensi</span>
                        </div>

                        <!-- Tipe Transaksi Toggle -->
                        <div>
                            <label class="text-[10px] font-extrabold uppercase tracking-wider text-slate-500 block mb-1.5">TIPE ARUS KAS</label>
                            <div class="flex items-center gap-2 p-1 bg-slate-100 rounded-xl max-w-sm text-xs font-bold" :class="formMode === 'view' ? 'opacity-70 pointer-events-none' : ''">
                                <button type="button" @click="if (formMode !== 'view') { tipeTransaksi = 'income'; form.tipe = 'income'; }"
                                        :class="tipeTransaksi === 'income' ? 'bg-[#0B2570] text-white shadow-xs' : 'text-slate-600 hover:text-slate-900'"
                                        :disabled="formMode === 'view'"
                                        class="flex-1 py-2 rounded-lg transition-all text-center flex items-center justify-center gap-1.5 cursor-pointer">
                                    <i class="fa-solid fa-arrow-down text-[10px]"></i> Penerimaan / Debit
                                </button>
                                <button type="button" @click="if (formMode !== 'view') { tipeTransaksi = 'expense'; form.tipe = 'expense'; }"
                                        :class="tipeTransaksi === 'expense' ? 'bg-[#0B2570] text-white shadow-xs' : 'text-slate-600 hover:text-slate-900'"
                                        :disabled="formMode === 'view'"
                                        class="flex-1 py-2 rounded-lg transition-all text-center flex items-center justify-center gap-1.5 cursor-pointer">
                                    <i class="fa-solid fa-arrow-up text-[10px]"></i> Pengeluaran / Kredit
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

                    <!-- Section 2: Detail Finansial & Klasifikasi Akun -->
                    <div class="space-y-4">
                        <div class="flex items-center gap-2.5 text-sm font-bold text-slate-900">
                            <div class="w-8 h-8 rounded-xl bg-emerald-600 text-white flex items-center justify-center">
                                <i class="fa-solid fa-coins text-xs"></i>
                            </div>
                            <span>Detail Nominal &amp; Klasifikasi Akun</span>
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
                            <label class="text-[10px] font-extrabold uppercase tracking-wider text-slate-500 block mb-1">KLASIFIKASI KATEGORI AKUN <span class="text-rose-500">*</span></label>
                            <select x-model="form.kategori" :disabled="formMode === 'view'" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs font-semibold text-slate-700 bg-slate-50/70 focus:bg-white focus:outline-none focus:ring-2 focus:ring-sky-500 transition-all disabled:bg-slate-100 disabled:cursor-not-allowed">
                                <option value="">Pilih Kategori Akun...</option>
                                
                                <optgroup label="── Penerimaan Kas (Debit / Inflow) ──">
                                    <option value="Penjualan Panen Ikan">Penjualan Panen Ikan</option>
                                    <option value="Penjualan Panen Ikan Nila">Penjualan Panen Ikan Nila</option>
                                    <option value="Penjualan Panen Ikan Lele">Penjualan Panen Ikan Lele</option>
                                    <option value="Penjualan Panen Ikan Patin">Penjualan Panen Ikan Patin</option>
                                    <option value="Penjualan Bibit / Fingerling">Penjualan Bibit / Fingerling</option>
                                    <option value="Pendapatan Non-Operasional">Pendapatan Non-Operasional</option>
                                </optgroup>

                                <optgroup label="── Harga Pokok Produksi / HPP Budidaya (Kredit) ──">
                                    <option value="Pembelian Pakan Pelet">Pembelian Pakan Pelet</option>
                                    <option value="Pembelian Pakan Alami (Cacing/Maggot)">Pembelian Pakan Alami (Cacing/Maggot)</option>
                                    <option value="Pembelian Bibit Ikan">Pembelian Bibit Ikan (Beli Luar)</option>
                                </optgroup>

                                <optgroup label="── Beban Operasional / OPEX Fasilitas (Kredit) ──">
                                    <option value="Biaya Listrik &amp; Operasional Aerator">Biaya Listrik, Pompa &amp; Aerator</option>
                                    <option value="Pembelian Obat &amp; Probiotik">Pembelian Probiotik, Vitamin &amp; Obat Air</option>
                                    <option value="Operasional &amp; Perawatan">Perawatan Filter &amp; Kolam</option>
                                    <option value="Gaji &amp; Honor Petugas">Gaji &amp; Upah Tenaga Kerja Lapangan</option>
                                    <option value="Transportasi &amp; Distribusi">Beban Logistik &amp; Armada</option>
                                    <option value="Operasional Umum &amp; Lain-lain">Operasional Umum &amp; Lain-lain</option>
                                </optgroup>
                            </select>
                        </div>

                        <div>
                            <label class="text-[10px] font-extrabold uppercase tracking-wider text-slate-500 block mb-1">ALOKASI KOLAM BUDIDAYA (OPSIONAL)</label>
                            <select x-model="form.id_kolam" :disabled="formMode === 'view'" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs font-semibold text-slate-700 bg-slate-50/70 focus:bg-white focus:outline-none focus:ring-2 focus:ring-sky-500 transition-all disabled:bg-slate-100 disabled:cursor-not-allowed">
                                <option value="">Tidak dialokasikan ke kolam spesifik (Beban Farm Umum)</option>
                                @foreach($kolams as $kolam)
                                    <option value="{{ $kolam->id_kolam }}">{{ $kolam->nama_kolam }} ({{ $kolam->tipe_kolam }})</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="text-[10px] font-extrabold uppercase tracking-wider text-slate-500 block mb-1">URAIAN / CATATAN TRANSAKSI</label>
                            <textarea rows="3" x-model="form.keterangan" :disabled="formMode === 'view'" placeholder="Tuliskan keterangan transaksi, nomor nota/kwitansi, atau detail rekanan..."
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
                                class="px-6 py-2.5 rounded-xl bg-[#0B2570] hover:bg-[#07194D] text-white font-bold text-xs shadow-sm transition-all flex items-center gap-2 cursor-pointer disabled:opacity-50">
                            <i class="fa-solid fa-spinner fa-spin" x-show="isLoading"></i>
                            <span x-text="formMode === 'edit' ? 'Simpan Perubahan' : 'Bukukan Transaksi'"></span>
                        </button>
                    </div>

                </form>

            </div>

            <!-- Right 1 Col: Preview / Help Box -->
            <div class="space-y-6">

                <!-- Ringkasan Info Transaksi Box -->
                <div class="bg-[#0B2570] rounded-2xl p-6 text-white shadow-xs space-y-4">
                    <div class="flex items-center justify-between pb-3 border-b border-white/10">
                        <span class="text-xs font-bold text-sky-200 uppercase tracking-wider">Ringkasan Entri Kas</span>
                        <span class="text-[10px] px-2 py-0.5 rounded-md font-extrabold"
                              :class="tipeTransaksi === 'income' ? 'bg-emerald-500/20 text-emerald-300 border border-emerald-400/30' : 'bg-rose-500/20 text-rose-300 border border-rose-400/30'"
                              x-text="tipeTransaksi === 'income' ? 'Debit (Kas Masuk)' : 'Kredit (Kas Keluar)'"></span>
                    </div>

                    <div class="space-y-3 text-xs">
                        <div class="flex justify-between items-baseline">
                            <span class="text-slate-300">Nominal:</span>
                            <span class="text-lg font-black text-white" x-text="form.nominal ? formatCurrency(form.nominal) : 'Rp 0'"></span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-slate-300">Akun:</span>
                            <span class="font-bold text-sky-200 truncate max-w-[150px]" x-text="form.kategori || '-'"></span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-slate-300">Tanggal:</span>
                            <span class="font-semibold text-slate-200" x-text="form.tanggal || '-'"></span>
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
                        <span>Prinsip Pembukuan Kas Budidaya</span>
                    </div>
                    <ul class="text-[11px] text-slate-600 space-y-2.5 leading-relaxed list-none">
                        <li class="flex items-start gap-2">
                            <span class="w-4 h-4 rounded-full bg-sky-200/80 text-sky-700 flex items-center justify-center shrink-0 text-[9px] font-extrabold mt-0.5">1</span>
                            <span><strong>Penerimaan (Debit):</strong> Catat penjualan ikan panen atau hasil bibit.</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <span class="w-4 h-4 rounded-full bg-sky-200/80 text-sky-700 flex items-center justify-center shrink-0 text-[9px] font-extrabold mt-0.5">2</span>
                            <span><strong>HPP Budidaya (Kredit):</strong> Biaya pakan pelet dan pengadaan bibit yang langsung membentuk bobot ikan.</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <span class="w-4 h-4 rounded-full bg-sky-200/80 text-sky-700 flex items-center justify-center shrink-0 text-[9px] font-extrabold mt-0.5">3</span>
                            <span><strong>Beban Operasional (Kredit):</strong> Listrik aerasi, obat probiotik, upah kerja, dan perawatan kolam.</span>
                        </li>
                    </ul>
                </div>

            </div>

        </div>
    </div>

    <!-- ========= TRANSACTION HISTORY TABLE SECTION ========= -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden space-y-0">
        
        <!-- Filter and Search Bar -->
        <div class="no-print p-5 border-b border-slate-100 flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h3 class="text-base font-bold text-slate-900">Buku Kas &amp; Mutasi Saldo Berjalan (Cash Ledger)</h3>
                <p class="text-xs text-slate-400 font-medium">Rekapitulasi kronologis seluruh mutasi debit (masuk), kredit (keluar), dan saldo kas akumulatif.</p>
            </div>

            <div class="flex flex-wrap items-center gap-3">
                <!-- Search Input -->
                <div class="relative flex items-center">
                    <i class="fa-solid fa-magnifying-glass text-slate-400 text-xs absolute left-3.5 pointer-events-none"></i>
                    <input type="text" x-model="searchQuery" placeholder="Cari keterangan / ref..." 
                           style="padding-left: 2.25rem !important;"
                           class="pl-9 pr-3.5 py-2.5 rounded-xl border border-slate-200 bg-slate-50/70 focus:bg-white text-xs font-semibold text-slate-700 focus:outline-none focus:ring-2 focus:ring-sky-500 transition-all w-52 sm:w-64">
                </div>

                <!-- Type Filter -->
                <select x-model="filterType" class="px-3 py-2 rounded-xl border border-slate-200 bg-slate-50/70 focus:bg-white text-xs font-semibold text-slate-700 focus:outline-none focus:ring-2 focus:ring-sky-500 transition-all">
                    <option value="">Semua Mutasi</option>
                    <option value="income">Debit (Pemasukan)</option>
                    <option value="expense">Kredit (Pengeluaran)</option>
                </select>

                <!-- Counter Badge -->
                <div class="flex items-center gap-2 px-3 py-2 rounded-xl border border-slate-200 bg-slate-50 text-xs font-bold text-slate-700">
                    <i class="fa-solid fa-receipt text-sky-600"></i>
                    <span x-text="filteredTransactions.length + ' Entri Kas'"></span>
                </div>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse print:text-[10px]">
                <thead>
                    <tr class="bg-slate-50/70 border-b border-slate-100 text-[10px] font-extrabold text-slate-400 uppercase tracking-wider print:bg-slate-100 print:border-slate-300 print:text-slate-700">
                        <th class="py-3.5 px-4 print:py-2 print:px-2">TANGGAL</th>
                        <th class="py-3.5 px-4 print:py-2 print:px-2">NO. REF &amp; URAIAN</th>
                        <th class="py-3.5 px-4 print:py-2 print:px-2">KATEGORI AKUN</th>
                        <th class="py-3.5 px-4 print:py-2 print:px-2">KOLAM</th>
                        <th class="py-3.5 px-4 text-right print:py-2 print:px-2 text-emerald-700">DEBIT (MASUK)</th>
                        <th class="py-3.5 px-4 text-right print:py-2 print:px-2 text-rose-700">KREDIT (KELUAR)</th>
                        <th class="py-3.5 px-4 text-right print:py-2 print:px-2 text-[#0B2570]">SALDO BERJALAN</th>
                        <th class="no-print py-3.5 px-4 text-center">AKSI</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-xs font-medium text-slate-700 print:divide-slate-200 print:text-[10px]">

                    <template x-if="filteredTransactions.length === 0">
                        <tr>
                            <td colspan="8" class="py-12 text-center text-slate-400 text-xs">
                                <i class="fa-regular fa-folder-open text-3xl text-slate-300 block mb-2"></i>
                                Tidak ada data transaksi yang sesuai filter atau pencarian.
                            </td>
                        </tr>
                    </template>

                    <template x-for="transaction in filteredTransactions" :key="transaction.raw_id || transaction.id">
                        <tr class="hover:bg-slate-50/70 transition-colors print:hover:bg-transparent">
                            <td class="py-3.5 px-4 text-slate-500 font-semibold print:py-2 print:px-2 whitespace-nowrap" x-text="transaction.tanggal_format || transaction.tanggal"></td>
                            <td class="py-3.5 px-4 print:py-2 print:px-2">
                                <div class="font-bold text-slate-900" x-text="transaction.keterangan || '-'"></div>
                                <div class="text-[10px] text-slate-400 font-mono font-semibold mt-0.5 print:text-slate-600 flex items-center gap-1.5">
                                    <span class="w-1.5 h-1.5 rounded-full" :class="transaction.tipe === 'income' ? 'bg-emerald-500' : 'bg-rose-500'"></span>
                                    <span x-text="transaction.ref"></span>
                                </div>
                            </td>
                            <td class="py-3.5 px-4 print:py-2 print:px-2">
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold print:border whitespace-nowrap"
                                      :class="transaction.tipe === 'income' ? 'bg-[#C6F6D5] text-[#22543D]' : 'bg-[#E0F2FE] text-[#0284C7]'"
                                      x-text="transaction.kategori"></span>
                            </td>
                            <td class="py-3.5 px-4 text-slate-600 font-semibold print:py-2 print:px-2 whitespace-nowrap" x-text="transaction.kolam || 'Umum'"></td>
                            
                            <!-- DEBIT -->
                            <td class="py-3.5 px-4 text-right font-extrabold print:py-2 print:px-2 whitespace-nowrap">
                                <span class="text-emerald-600 font-mono" x-show="transaction.debit > 0" x-text="formatCurrency(transaction.debit)"></span>
                                <span class="text-slate-300 font-mono" x-show="!transaction.debit || transaction.debit <= 0">-</span>
                            </td>

                            <!-- KREDIT -->
                            <td class="py-3.5 px-4 text-right font-extrabold print:py-2 print:px-2 whitespace-nowrap">
                                <span class="text-rose-600 font-mono" x-show="transaction.kredit > 0" x-text="formatCurrency(transaction.kredit)"></span>
                                <span class="text-slate-300 font-mono" x-show="!transaction.kredit || transaction.kredit <= 0">-</span>
                            </td>

                            <!-- SALDO BERJALAN -->
                            <td class="py-3.5 px-4 text-right font-black print:py-2 print:px-2 text-[#0B2570] font-mono whitespace-nowrap"
                                x-text="formatCurrency(transaction.saldo_berjalan)"></td>

                            <!-- AKSI -->
                            <td class="no-print py-3.5 px-4 text-center">
                                <div class="inline-block text-left" 
                                     x-data="{ 
                                         open: false, 
                                         menuStyle: '',
                                         toggle(event) {
                                             this.open = !this.open;
                                             if (this.open) {
                                                 const rect = this.$refs.btn.getBoundingClientRect();
                                                 const menuHeight = 140;
                                                 const spaceBelow = window.innerHeight - rect.bottom;
                                                 let topPos;
                                                 if (spaceBelow < menuHeight) {
                                                     topPos = Math.max(10, rect.top - menuHeight + 5);
                                                 } else {
                                                     topPos = rect.bottom + 5;
                                                 }
                                                 const rightPos = Math.max(10, window.innerWidth - rect.right);
                                                 this.menuStyle = `position: fixed; z-index: 99999; top: ${topPos}px; right: ${rightPos}px;`;
                                             }
                                         }
                                     }">
                                    <button x-ref="btn" 
                                            @click="toggle($event)" 
                                            @click.away="open = false" 
                                            class="text-slate-400 hover:text-slate-600 p-1.5 rounded-lg hover:bg-slate-100 transition-colors cursor-pointer">
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
                                         class="w-44 rounded-xl bg-white border border-slate-200 shadow-2xl py-1.5 z-50 text-left"
                                         style="display: none;">
                                        
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
                                        <button type="button" @click="open = false; deleteTransaction(transaction)" class="w-full px-3.5 py-2 text-xs font-semibold text-rose-600 hover:bg-rose-50 flex items-center gap-2.5 cursor-pointer">
                                            <i class="fa-solid fa-trash-can text-rose-600 w-4"></i>
                                            <span>Hapus Transaksi</span>
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

</div>
@endsection

@push('scripts')
<script>
function transaksiKeuanganComponent() {
    return {
        transactions: {!! json_encode($transactions ?? []) !!},
        searchQuery: "",
        filterType: "",
        showForm: false,
        formMode: "create", // 'create', 'edit', 'view'
        isLoading: false,
        tipeTransaksi: "income",
        form: {
            raw_id: null,
            id: "",
            tanggal: new Date().toISOString().split("T")[0],
            tipe: "income",
            nominal: "",
            kategori: "",
            ref: "",
            id_kolam: "",
            kolam: "Tidak dialokasikan",
            keterangan: ""
        },

        get filteredTransactions() {
            let list = this.transactions;
            if (this.filterType) {
                list = list.filter(t => t.tipe === this.filterType);
            }
            if (this.searchQuery && this.searchQuery.trim() !== "") {
                const q = this.searchQuery.toLowerCase();
                list = list.filter(t => 
                    (t.keterangan && t.keterangan.toLowerCase().includes(q)) ||
                    (t.kategori && t.kategori.toLowerCase().includes(q)) ||
                    (t.ref && t.ref.toLowerCase().includes(q)) ||
                    (t.kolam && t.kolam.toLowerCase().includes(q))
                );
            }
            return list;
        },

        formatCurrency(num) {
            return "Rp " + Number(num || 0).toLocaleString("id-ID");
        },

        generateSopCode(dateStr) {
            const date = dateStr ? new Date(dateStr) : new Date();
            const yy = String(date.getFullYear()).slice(-2);
            const mm = String(date.getMonth() + 1).padStart(2, '0');
            const prefix = `TRX-${yy}${mm}-`;
            const randomNum = String(Math.floor(Math.random() * 900) + 100);
            return `${prefix}${randomNum}`;
        },

        onDateChange() {
            if (this.formMode === "create") {
                this.form.id = this.generateSopCode(this.form.tanggal);
                this.form.ref = this.form.id;
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
                alert("Silakan pilih kategori akun transaksi.");
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

        async deleteTransaction(item) {
            const res = await AppSwal.confirmDelete({
                title: 'Hapus Transaksi Keuangan?',
                text: 'Apakah Anda yakin ingin menghapus catatan transaksi "' + (item.ref || item.id) + '"? Data yang dihapus tidak dapat dipulihkan.',
                confirmText: 'Ya, Hapus Transaksi',
                cancelText: 'Batal'
            });
            if (!res.isConfirmed) return;

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
                    AppSwal.toast('Transaksi berhasil dihapus!', 'success');
                    setTimeout(() => window.location.reload(), 600);
                } else {
                    AppSwal.error('Gagal Menghapus', data.message || "Gagal menghapus transaksi.");
                }
            })
            .catch(err => {
                this.isLoading = false;
                console.error(err);
                AppSwal.error('Kesalahan Jaringan', "Terjadi kesalahan koneksi server saat menghapus.");
            });
        }
    };
}
</script>
@endpush

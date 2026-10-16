<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Keuangan & Laba Rugi Budidaya - {{ $periodTitle }}</title>
    <style>
        @page {
            margin: 12mm 14mm 12mm 14mm;
        }
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            color: #0f172a;
            font-size: 8.5pt;
            line-height: 1.35;
        }
        .header {
            border-bottom: 2px solid #0B2570;
            padding-bottom: 8px;
            margin-bottom: 12px;
        }
        .title {
            font-size: 13pt;
            font-weight: 800;
            color: #0B2570;
            margin: 0;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .subtitle {
            font-size: 8pt;
            color: #64748b;
            margin: 2px 0 0 0;
        }
        .section-title {
            font-size: 9pt;
            font-weight: 800;
            color: #0B2570;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin: 14px 0 6px 0;
            border-bottom: 1.5px solid #cbd5e1;
            padding-bottom: 3px;
        }
        .summary-grid {
            width: 100%;
            margin-bottom: 10px;
        }
        .summary-card {
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 5px;
            padding: 6px 10px;
            width: 23%;
            vertical-align: top;
        }
        .lbl { font-size: 7pt; color: #64748b; font-weight: bold; text-transform: uppercase; }
        .val { font-size: 11pt; font-weight: 800; margin-top: 2px; }
        .val-in { color: #15803d; }
        .val-out { color: #be123c; }
        .val-gross { color: #0284c7; }
        .val-net { color: #0B2570; }

        table.pnl-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 12px;
        }
        table.pnl-table th {
            background-color: #f1f5f9;
            color: #334155;
            font-size: 7.5pt;
            font-weight: bold;
            text-transform: uppercase;
            padding: 5px 8px;
            border: 1px solid #cbd5e1;
            text-align: left;
        }
        table.pnl-table td {
            font-size: 8pt;
            padding: 4px 8px;
            border: 1px solid #e2e8f0;
        }
        .row-group {
            background-color: #f8fafc;
            font-weight: bold;
            color: #0B2570;
        }
        .row-subtotal {
            background-color: #f1f5f9;
            font-weight: bold;
        }
        .row-total {
            background-color: #e0f2fe;
            font-weight: 800;
            color: #0369a1;
        }
        .row-net {
            background-color: #dbeafe;
            font-weight: 800;
            color: #0B2570;
            font-size: 8.5pt;
        }

        table.data-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 6px;
        }
        table.data-table th {
            background-color: #0B2570;
            color: #ffffff;
            font-size: 7.5pt;
            font-weight: bold;
            text-transform: uppercase;
            padding: 5px 6px;
            border: 1px solid #0B2570;
            text-align: left;
        }
        table.data-table td {
            font-size: 7.5pt;
            padding: 4px 6px;
            border: 1px solid #cbd5e1;
        }
        table.data-table tr:nth-child(even) td {
            background-color: #f8fafc;
        }
        .text-center { text-align: center; }
        .text-right { text-align: right; }

        .sig-table {
            width: 100%;
            margin-top: 20px;
            page-break-inside: avoid;
        }
        .sig-cell {
            width: 50%;
            text-align: center;
        }
        .sig-line {
            display: inline-block;
            min-width: 160px;
            border-bottom: 1px solid #64748b;
            margin-top: 45px;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="header">
        <table style="width: 100%;">
            <tr>
                <td>
                    <h1 class="title">LAPORAN LABA RUGI &amp; BIAYA BUDIDAYA (COST ACCOUNTING)</h1>
                    <p class="subtitle">AMS BUDIDAYA • Unit Bisnis Perikanan Terpadu • Periode: {{ $periodTitle }}</p>
                </td>
                <td style="text-align: right; font-size: 7.5pt; color: #64748b;">
                    Dicetak: {{ \Carbon\Carbon::now()->translatedFormat('d F Y, H:i') }} WIB<br>
                    Dokumen: Form Finansial Eksekutif
                </td>
            </tr>
        </table>
    </div>

    <!-- 1. Ringkasan Eksekutif Kartu -->
    <table class="summary-grid" cellpadding="0" cellspacing="0">
        <tr>
            <td class="summary-card">
                <div class="lbl">Total Pendapatan (Omzet)</div>
                <div class="val val-in">Rp {{ number_format($pnl['totalIncome'] ?? $totalIncome, 0, ',', '.') }}</div>
            </td>
            <td style="width: 2.6%;"></td>
            <td class="summary-card">
                <div class="lbl">HPP Pokok (Bibit + Pakan)</div>
                <div class="val val-out">Rp {{ number_format($pnl['totalHpp'] ?? 0, 0, ',', '.') }}</div>
            </td>
            <td style="width: 2.6%;"></td>
            <td class="summary-card">
                <div class="lbl">Laba Kotor (Gross Profit)</div>
                <div class="val val-gross">Rp {{ number_format($pnl['labaKotor'] ?? 0, 0, ',', '.') }} ({{ $pnl['grossMargin'] ?? 0 }}%)</div>
            </td>
            <td style="width: 2.6%;"></td>
            <td class="summary-card">
                <div class="lbl">Laba Bersih (Net Profit)</div>
                <div class="val val-net">{{ ($saldo >= 0 ? '+Rp ' : '-Rp ') . number_format(abs($saldo), 0, ',', '.') }} ({{ $pnl['netMargin'] ?? 0 }}%)</div>
            </td>
        </tr>
    </table>

    <!-- 2. Laporan Laba Rugi Komprehensif (Income Statement) -->
    <div class="section-title">I. Laporan Laba Rugi Operasional (Income Statement)</div>
    <table class="pnl-table">
        <thead>
            <tr>
                <th style="width: 60%;">Uraian Akun &amp; Kategori</th>
                <th style="width: 20%;" class="text-right">Nominal (Rp)</th>
                <th style="width: 20%;" class="text-right">Porsi (%)</th>
            </tr>
        </thead>
        <tbody>
            <!-- PENDAPATAN OPERASIONAL -->
            <tr class="row-group">
                <td colspan="3">A. PENDAPATAN OPERASIONAL (OPERATING REVENUE)</td>
            </tr>
            <tr>
                <td style="padding-left: 16px;">Penjualan Panen Ikan &amp; Hasil Budidaya</td>
                <td class="text-right" style="color: #15803d; font-weight: bold;">Rp {{ number_format($pnl['totalIncome'] ?? $totalIncome, 0, ',', '.') }}</td>
                <td class="text-right">100.0%</td>
            </tr>
            <tr class="row-subtotal">
                <td>TOTAL PENDAPATAN OPERASIONAL</td>
                <td class="text-right" style="color: #15803d;">Rp {{ number_format($pnl['totalIncome'] ?? $totalIncome, 0, ',', '.') }}</td>
                <td class="text-right">100.0%</td>
            </tr>

            <!-- HPP / COGS -->
            <tr class="row-group">
                <td colspan="3">B. BIAYA POKOK PRODUKSI / HPP BUDIDAYA (COST OF GOODS SOLD)</td>
            </tr>
            <tr>
                <td style="padding-left: 16px;">Biaya Pakan Ikan (Pelet Komersial &amp; Hijauan Alami)</td>
                <td class="text-right" style="color: #be123c;">Rp {{ number_format($pnl['pakanTotal'] ?? 0, 0, ',', '.') }}</td>
                <td class="text-right">{{ $totalIncome > 0 ? round(($pnl['pakanTotal'] ?? 0) / $totalIncome * 100, 1) : 0 }}%</td>
            </tr>
            <tr>
                <td style="padding-left: 16px;">Biaya Pengadaan Bibit Ikan (Fingerling / Larva)</td>
                <td class="text-right" style="color: #be123c;">Rp {{ number_format($pnl['bibitTotal'] ?? 0, 0, ',', '.') }}</td>
                <td class="text-right">{{ $totalIncome > 0 ? round(($pnl['bibitTotal'] ?? 0) / $totalIncome * 100, 1) : 0 }}%</td>
            </tr>
            <tr class="row-subtotal">
                <td>TOTAL HPP BUDIDAYA</td>
                <td class="text-right" style="color: #be123c;">Rp {{ number_format($pnl['totalHpp'] ?? 0, 0, ',', '.') }}</td>
                <td class="text-right">{{ $totalIncome > 0 ? round(($pnl['totalHpp'] ?? 0) / $totalIncome * 100, 1) : 0 }}%</td>
            </tr>

            <!-- LABA KOTOR -->
            <tr class="row-total">
                <td>LABA KOTOR (GROSS PROFIT)</td>
                <td class="text-right">Rp {{ number_format($pnl['labaKotor'] ?? 0, 0, ',', '.') }}</td>
                <td class="text-right">{{ $pnl['grossMargin'] ?? 0 }}%</td>
            </tr>

            <!-- BEBAN OPERASIONAL -->
            <tr class="row-group">
                <td colspan="3">C. BEBAN OPERASIONAL FASILITAS (OPERATING EXPENSES)</td>
            </tr>
            <tr>
                <td style="padding-left: 16px;">Biaya Listrik, Pompa, Aerasi Continuous &amp; Perawatan Filter</td>
                <td class="text-right" style="color: #be123c;">Rp {{ number_format($pnl['operasionalTotal'] ?? 0, 0, ',', '.') }}</td>
                <td class="text-right">{{ $totalIncome > 0 ? round(($pnl['operasionalTotal'] ?? 0) / $totalIncome * 100, 1) : 0 }}%</td>
            </tr>
            <tr class="row-subtotal">
                <td>TOTAL BEBAN OPERASIONAL</td>
                <td class="text-right" style="color: #be123c;">Rp {{ number_format($pnl['operasionalTotal'] ?? 0, 0, ',', '.') }}</td>
                <td class="text-right">{{ $totalIncome > 0 ? round(($pnl['operasionalTotal'] ?? 0) / $totalIncome * 100, 1) : 0 }}%</td>
            </tr>

            <!-- LABA BERSIH -->
            <tr class="row-net">
                <td>LABA BERSIH OPERASIONAL (NET OPERATING PROFIT)</td>
                <td class="text-right">{{ ($saldo >= 0 ? '+Rp ' : '-Rp ') . number_format(abs($saldo), 0, ',', '.') }}</td>
                <td class="text-right">{{ $pnl['netMargin'] ?? 0 }}%</td>
            </tr>
        </tbody>
    </table>

    <!-- 3. Parameter Efisiensi Unit Economics -->
    <table style="width: 100%; border: 1px solid #cbd5e1; background-color: #f8fafc; border-radius: 4px; padding: 6px; margin-bottom: 12px; font-size: 7.5pt;">
        <tr>
            <td style="width: 25%;"><strong>Total Panen/Distribusi:</strong> {{ number_format($pnl['totalPanenKg'] ?? 0, 0, ',', '.') }} Kg</td>
            <td style="width: 25%;"><strong>HPP per Kg Ikan:</strong> Rp {{ number_format($pnl['hppPerKg'] ?? 0, 0, ',', '.') }}</td>
            <td style="width: 25%;"><strong>Harga Jual Acuan:</strong> Rp {{ number_format($pnl['avgSellingPrice'] ?? 0, 0, ',', '.') }} / Kg</td>
            <td style="width: 25%;"><strong>Status Finansial:</strong> <span style="color: #15803d; font-weight: bold;">{{ $saldo >= 0 ? 'SURPLUS & SEHAT' : 'DEFISIT' }}</span></td>
        </tr>
    </table>

    <!-- 4. Buku Mutasi Kas Operasional (Cash Ledger with Running Balance) -->
    <div class="section-title">II. Buku Kas Operasional &amp; Saldo Berjalan (Cash Ledger)</div>
    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 4%;" class="text-center">No</th>
                <th style="width: 14%;">Ref ID</th>
                <th style="width: 10%;">Tanggal</th>
                <th style="width: 15%;">Kolam</th>
                <th style="width: 27%;">Uraian Transaksi</th>
                <th style="width: 10%;" class="text-right">Debit (Masuk)</th>
                <th style="width: 10%;" class="text-right">Kredit (Keluar)</th>
                <th style="width: 10%;" class="text-right">Saldo Kas</th>
            </tr>
        </thead>
        <tbody>
            @forelse($transaksiList as $idx => $t)
            <tr>
                <td class="text-center">{{ $idx + 1 }}</td>
                <td><strong>{{ $t->ref_id ?? ('TRX-' . $t->id_keuangan) }}</strong></td>
                <td>{{ \Carbon\Carbon::parse($t->tanggal_transaksi)->format('d/m/Y') }}</td>
                <td>{{ $t->kolam ? $t->kolam->nama_kolam : 'Umum' }}</td>
                <td>{{ $t->keterangan ?: ($t->kategori ?? '-') }}</td>
                <td class="text-right" style="color: #15803d; font-weight: bold;">
                    {{ ($t->debit ?? 0) > 0 ? ('Rp ' . number_format($t->debit, 0, ',', '.')) : '-' }}
                </td>
                <td class="text-right" style="color: #be123c; font-weight: bold;">
                    {{ ($t->kredit ?? 0) > 0 ? ('Rp ' . number_format($t->kredit, 0, ',', '.')) : '-' }}
                </td>
                <td class="text-right" style="color: #0B2570; font-weight: bold;">
                    Rp {{ number_format($t->saldo_berjalan ?? 0, 0, ',', '.') }}
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="8" class="text-center" style="padding: 12px; color: #94a3b8;">Belum ada data transaksi keuangan pada periode ini.</td>
            </tr>
            @endforelse
            <tr style="background-color: #f1f5f9; font-weight: bold;">
                <td colspan="5" class="text-right">TOTAL MUTASI ARUS KAS:</td>
                <td class="text-right" style="color: #15803d; font-size: 8pt;">Rp {{ number_format($totalIncome, 0, ',', '.') }}</td>
                <td class="text-right" style="color: #be123c; font-size: 8pt;">Rp {{ number_format($totalExpense, 0, ',', '.') }}</td>
                <td class="text-right" style="color: #0B2570; font-size: 8pt;">Rp {{ number_format($saldo, 0, ',', '.') }}</td>
            </tr>
        </tbody>
    </table>

    <!-- 5. Lembar Pengesahan -->
    <table class="sig-table">
        <tr>
            <td class="sig-cell">
                <div>Diverifikasi Oleh (Administrasi &amp; Kasir)</div>
                <div class="sig-line">&nbsp;</div>
                <div style="font-size: 7pt; color: #64748b; margin-top: 3px;">Staff Administrasi Keuangan Farm</div>
            </td>
            <td class="sig-cell">
                <div>Disetujui Oleh (Manajer Operasional)</div>
                <div class="sig-line">{{ Auth::user()->nama ?? 'Manajer Operasional' }}</div>
                <div style="font-size: 7pt; color: #64748b; margin-top: 3px;">Manajer Bisnis &amp; Akuakultur AMS Budidaya</div>
            </td>
        </tr>
    </table>
</body>
</html>

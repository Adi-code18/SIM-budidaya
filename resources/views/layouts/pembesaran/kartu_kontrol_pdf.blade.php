<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Kartu Kontrol Kolam - #PB-{{ str_pad($batch->id_pembesaran, 5, '0', STR_PAD_LEFT) }}</title>
    <style>
        @page {
            margin: 15mm 15mm 15mm 15mm;
        }
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            color: #1e293b;
            font-size: 9pt;
            line-height: 1.35;
        }
        .header-table {
            width: 100%;
            border-bottom: 2px solid #0B2570;
            padding-bottom: 8px;
            margin-bottom: 12px;
        }
        .title {
            font-size: 15pt;
            font-weight: 800;
            color: #0B2570;
            margin: 0;
            text-transform: uppercase;
        }
        .subtitle {
            font-size: 8.5pt;
            color: #64748b;
            margin: 2px 0 0 0;
        }
        .meta-grid {
            width: 100%;
            margin-bottom: 12px;
        }
        .meta-box {
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            padding: 8px 10px;
            width: 48%;
            vertical-align: top;
        }
        .box-title {
            font-size: 8pt;
            font-weight: bold;
            color: #0B2570;
            text-transform: uppercase;
            border-bottom: 1px dashed #cbd5e1;
            padding-bottom: 3px;
            margin-bottom: 5px;
        }
        .row-item {
            font-size: 8.5pt;
            margin-bottom: 2px;
        }
        .lbl {
            color: #64748b;
            display: inline-block;
            width: 110px;
        }
        .val {
            font-weight: bold;
            color: #0f172a;
        }
        .kpi-table {
            width: 100%;
            margin-bottom: 12px;
        }
        .kpi-card {
            background-color: #eff6ff;
            border: 1px solid #bfdbfe;
            border-radius: 6px;
            padding: 6px 10px;
            width: 23%;
            vertical-align: top;
        }
        .kpi-card.warn {
            background-color: #fff1f2;
            border-color: #fecdd3;
        }
        .kpi-card.success {
            background-color: #f0fdf4;
            border-color: #bbf7d0;
        }
        .kpi-title { font-size: 7pt; font-weight: bold; color: #475569; text-transform: uppercase; }
        .kpi-val { font-size: 11pt; font-weight: 800; color: #0B2570; margin-top: 2px; }
        .kpi-card.warn .kpi-val { color: #be123c; }
        .kpi-card.success .kpi-val { color: #15803d; }
        .kpi-sub { font-size: 7.5pt; color: #64748b; margin-top: 2px; }
        table.data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 12px;
        }
        table.data-table th {
            background-color: #0B2570;
            color: #ffffff;
            font-size: 8pt;
            font-weight: bold;
            text-transform: uppercase;
            padding: 5px 7px;
            border: 1px solid #0B2570;
            text-align: left;
        }
        table.data-table td {
            font-size: 8pt;
            padding: 5px 7px;
            border: 1px solid #cbd5e1;
        }
        table.data-table tr:nth-child(even) td {
            background-color: #f8fafc;
        }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .sig-table {
            width: 100%;
            margin-top: 25px;
            page-break-inside: avoid;
        }
        .sig-cell {
            width: 50%;
            text-align: center;
            vertical-align: top;
        }
        .sig-line {
            display: inline-block;
            min-width: 150px;
            border-bottom: 1px solid #64748b;
            margin-top: 45px;
            font-weight: bold;
            font-size: 9pt;
        }
    </style>
</head>
<body>

    <table class="header-table" cellpadding="0" cellspacing="0">
        <tr>
            <td>
                <h1 class="title">KARTU KONTROL KOLAM PEMBESARAN</h1>
                <p class="subtitle">Sistem Informasi Manajemen Budidaya Ikan • Standar Operasional Prosedur FCR &amp; Panen</p>
            </td>
            <td style="text-align: right;">
                <div style="font-size: 13pt; font-weight: 800; color: #0284c7;">
                    #PB-{{ str_pad($batch->id_pembesaran, 5, '0', STR_PAD_LEFT) }}
                </div>
                <div style="font-size: 8pt; color: #64748b;">
                    Status: <strong>{{ strtoupper(str_replace('_', ' ', $batch->status_siklus ?? 'BERJALAN')) }}</strong>
                </div>
            </td>
        </tr>
    </table>

    <!-- Info Kolam & Batch -->
    <table class="meta-grid" cellpadding="0" cellspacing="0">
        <tr>
            <td class="meta-box">
                <div class="box-title">IDENTITAS KOLAM &amp; KOMODITAS</div>
                <div class="row-item"><span class="lbl">Kolam Budidaya:</span> <span class="val">{{ $batch->kolam ? $batch->kolam->nama_kolam : 'Kolam #' . $batch->id_kolam }} ({{ $batch->kolam ? $batch->kolam->tipe_kolam : '-' }})</span></div>
                <div class="row-item"><span class="lbl">Komoditas Ikan:</span> <span class="val">{{ $batch->jenis_ikan }}</span></div>
                <div class="row-item"><span class="lbl">Asal Bibit:</span> <span class="val">{{ $batch->asal_bibit === 'beli_luar' ? 'Pengadaan Supplier Luar' : ($batch->batchPembibitan ? '#BT-' . str_pad($batch->batchPembibitan->id_batch, 5, '0', STR_PAD_LEFT) : 'Internal Farm') }}</span></div>
                <div class="row-item"><span class="lbl">Tanggal Tebar:</span> <span class="val">{{ \Carbon\Carbon::parse($batch->tgl_tebar)->translatedFormat('d F Y') }}</span></div>
            </td>
            <td style="width: 4%;"></td>
            <td class="meta-box">
                <div class="box-title">PARAMETER TARGET &amp; BIOLOGIS</div>
                <div class="row-item"><span class="lbl">Usia Tebar (DOC):</span> <span class="val">{{ abs(\Carbon\Carbon::parse($batch->tgl_tebar)->diffInDays(now())) }} Hari</span></div>
                <div class="row-item"><span class="lbl">Biomassa Awal:</span> <span class="val">{{ number_format($batch->getBiomassaAwalKg(), 1, ',', '.') }} kg ({{ number_format($batch->jumlah_tebar_ekor ?: 0, 0, ',', '.') }} ekor)</span></div>
                <div class="row-item"><span class="lbl">Biomassa Saat Ini:</span> <span class="val">{{ number_format($batch->biomassa_est, 1, ',', '.') }} kg</span></div>
                <div class="row-item"><span class="lbl">Target Panen:</span> <span class="val">{{ number_format($batch->target_panen_kg, 1, ',', '.') }} kg</span></div>
            </td>
        </tr>
    </table>

    <!-- Kartu Metrik FCR & Pakan -->
    <table class="kpi-table" cellpadding="0" cellspacing="0">
        <tr>
            <td class="kpi-card {{ ($batch->calculateActualFcr() <= ($ikanRef->fcr_max ?? 1.35)) ? 'success' : 'warn' }}">
                <div class="kpi-title">FCR Kumulatif</div>
                <div class="kpi-val">{{ number_format($batch->calculateActualFcr(), 2) }}</div>
                <div class="kpi-sub">SOP Target: {{ $ikanRef ? $ikanRef->fcr_min . ' - ' . $ikanRef->fcr_max : '1.0 - 1.3' }}</div>
            </td>
            <td style="width: 2.6%;"></td>
            <td class="kpi-card">
                <div class="kpi-title">Total Konsumsi Pakan</div>
                <div class="kpi-val">{{ number_format($batch->getTotalPakanKg(), 1, ',', '.') }} kg</div>
                <div class="kpi-sub">Pelet Komersial + Daun</div>
            </td>
            <td style="width: 2.6%;"></td>
            <td class="kpi-card {{ $wasted['is_inefficient'] ? 'warn' : 'success' }}">
                <div class="kpi-title">Analisis Inefisiensi</div>
                <div class="kpi-val">{{ $wasted['is_inefficient'] ? '+' . $wasted['excess_fcr'] . ' FCR' : 'Optimal' }}</div>
                <div class="kpi-sub">{{ $wasted['is_inefficient'] ? $wasted['wasted_pakan_kg'] . ' kg terbuang (' . $wasted['wasted_format'] . ')' : 'Pakan sesuai SOP' }}</div>
            </td>
            <td style="width: 2.6%;"></td>
            <td class="kpi-card {{ $financials['laba_rugi'] >= 0 ? 'success' : 'warn' }}">
                <div class="kpi-title">Estimasi Laba/Rugi</div>
                <div class="kpi-val">{{ ($financials['laba_rugi'] >= 0 ? '+Rp ' : '-Rp ') . number_format(abs($financials['laba_rugi']), 0, ',', '.') }}</div>
                <div class="kpi-sub">Margin: {{ $financials['margin_percent'] }}% • HPP: Rp {{ number_format($financials['total_biaya_kolam'], 0, ',', '.') }}</div>
            </td>
        </tr>
    </table>

    <!-- Rincian Biaya HPP Kolam -->
    <table class="data-table" style="margin-bottom: 15px;">
        <thead>
            <tr>
                <th colspan="4" style="background-color: #031B4E;">RINCIAN STRUKTUR BIAYA &amp; HPP KOLAM (HARGA POKOK PRODUKSI)</th>
            </tr>
            <tr style="background-color: #f1f5f9; color: #0f172a;">
                <th style="background-color: #e2e8f0; color: #0f172a;">Komponen Biaya</th>
                <th style="background-color: #e2e8f0; color: #0f172a;" class="text-right">Volume / Satuan</th>
                <th style="background-color: #e2e8f0; color: #0f172a;" class="text-right">Harga Acuan</th>
                <th style="background-color: #e2e8f0; color: #0f172a;" class="text-right">Total Nominal</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Biaya Pakan Terpakai</td>
                <td class="text-right">{{ number_format($financials['total_pakan_kg'], 1, ',', '.') }} kg</td>
                <td class="text-right">Rp {{ number_format($financials['avg_harga_pakan'], 0, ',', '.') }}/kg</td>
                <td class="text-right font-bold">Rp {{ number_format($financials['biaya_pakan'], 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td>Biaya Benih / Bibit Awal</td>
                <td class="text-right">{{ number_format($batch->jumlah_tebar_ekor ?: 0, 0, ',', '.') }} ekor</td>
                <td class="text-right">-</td>
                <td class="text-right font-bold">Rp {{ number_format($financials['biaya_bibit'], 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td>Biaya Operasional Kolam (Listrik, Perawatan, dll)</td>
                <td class="text-right">-</td>
                <td class="text-right">-</td>
                <td class="text-right font-bold">Rp {{ number_format($financials['biaya_operasional'], 0, ',', '.') }}</td>
            </tr>
            <tr style="background-color: #f8fafc; font-weight: bold;">
                <td colspan="3" class="text-right">Total Modal / HPP Kolam:</td>
                <td class="text-right" style="color: #0B2570; font-size: 9.5pt;">Rp {{ number_format($financials['total_biaya_kolam'], 0, ',', '.') }}</td>
            </tr>
            <tr style="background-color: #f1f5f9; font-weight: bold;">
                <td colspan="3" class="text-right">Estimasi Omset Penjualan ({{ number_format($financials['biomassa_jual_kg'], 1, ',', '.') }} kg @ Rp {{ number_format($financials['harga_jual_per_kg'], 0, ',', '.') }}):</td>
                <td class="text-right" style="color: #15803d; font-size: 9.5pt;">Rp {{ number_format($financials['pendapatan_estimasi'], 0, ',', '.') }}</td>
            </tr>
        </tbody>
    </table>

    <!-- Riwayat Log Pakan Terakhir -->
    <div style="font-size: 8.5pt; font-weight: bold; color: #0B2570; margin-bottom: 5px;">
        RIWAYAT LOG PAKAN HARIAN KOLAM (MAKSIMAL 15 CATATAN TERBARU)
    </div>
    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 5%;" class="text-center">No</th>
                <th style="width: 15%;">Tanggal</th>
                <th style="width: 15%;" class="text-right">Pelet (Kg)</th>
                <th style="width: 15%;" class="text-right">Dedaunan (Kg)</th>
                <th style="width: 15%;" class="text-right">Total Pakan</th>
                <th style="width: 12%;" class="text-center">pH Air</th>
                <th style="width: 12%;" class="text-center">Suhu (°C)</th>
                <th style="width: 11%;">Petugas</th>
            </tr>
        </thead>
        <tbody>
            @forelse($pakanLogs->take(15) as $idx => $pl)
            <tr>
                <td class="text-center">{{ $idx + 1 }}</td>
                <td>{{ \Carbon\Carbon::parse($pl->tgl_log)->format('d/m/Y') }}</td>
                <td class="text-right">{{ number_format($pl->kg_pelet, 1, ',', '.') }}</td>
                <td class="text-right">{{ number_format($pl->kg_daun, 1, ',', '.') }}</td>
                <td class="text-right font-bold">{{ number_format($pl->kg_pelet + $pl->kg_daun, 1, ',', '.') }} kg</td>
                <td class="text-center">{{ $pl->ph_air ?: '-' }}</td>
                <td class="text-center">{{ $pl->suhu_air ? $pl->suhu_air . '°C' : '-' }}</td>
                <td>{{ $pl->user ? $pl->user->name : 'Teknisi' }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="8" class="text-center" style="padding: 10px; color: #94a3b8;">Belum ada log pakan yang tercatat untuk kolam ini.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <!-- Tanda Tangan -->
    <table class="sig-table">
        <tr>
            <td class="sig-cell">
                <div>Teknisi Lapangan Pembesaran</div>
                <div class="sig-line">&nbsp;</div>
                <div style="font-size: 7.5pt; color: #64748b; margin-top: 3px;">Penanggung Jawab Kolam</div>
            </td>
            <td class="sig-cell">
                <div>Manajer Operasional Tambak</div>
                <div class="sig-line">{{ Auth::user()->name ?? 'Manajer Operasional' }}</div>
                <div style="font-size: 7.5pt; color: #64748b; margin-top: 3px;">AMS BUDIDAYA Aquaculture</div>
            </td>
        </tr>
    </table>

</body>
</html>

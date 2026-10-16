<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Rekapitulasi Distribusi &amp; Logistik Pengiriman - AMS BUDIDAYA</title>
    <style>
        @page {
            margin: 15mm 12mm 15mm 12mm;
        }
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            color: #1e293b;
            font-size: 9pt;
            line-height: 1.35;
        }
        .header {
            border-bottom: 2px solid #0B2570;
            padding-bottom: 10px;
            margin-bottom: 12px;
        }
        .title {
            font-size: 14pt;
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
        .summary-cards {
            width: 100%;
            margin-bottom: 12px;
        }
        .summary-card {
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            padding: 8px 12px;
            width: 23%;
            vertical-align: top;
        }
        .card-lbl { font-size: 7.5pt; color: #64748b; text-transform: uppercase; font-weight: bold; }
        .card-val { font-size: 12pt; font-weight: 800; color: #0B2570; margin-top: 2px; }
        table.data-table {
            width: 100%;
            border-collapse: collapse;
        }
        table.data-table th {
            background-color: #0B2570;
            color: #ffffff;
            font-size: 8pt;
            font-weight: bold;
            text-transform: uppercase;
            padding: 6px 8px;
            border: 1px solid #0B2570;
            text-align: left;
        }
        table.data-table td {
            font-size: 8pt;
            padding: 6px 8px;
            border: 1px solid #cbd5e1;
        }
        table.data-table tr:nth-child(even) td {
            background-color: #f8fafc;
        }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .badge {
            display: inline-block;
            padding: 1px 5px;
            border-radius: 3px;
            font-size: 7pt;
            font-weight: bold;
            text-transform: uppercase;
        }
        .badge-selesai { background-color: #dcfce7; color: #15803d; }
        .badge-siap { background-color: #e0f2fe; color: #0369a1; }
        .badge-pemberokan { background-color: #fef3c7; color: #b45309; }
        .badge-pending { background-color: #f1f5f9; color: #475569; }
        .footer-note {
            margin-top: 15px;
            font-size: 7.5pt;
            color: #94a3b8;
            text-align: right;
        }
    </style>
</head>
<body>
    <div class="header">
        <table style="width: 100%;">
            <tr>
                <td>
                    <h1 class="title">REKAPITULASI DISTRIBUSI &amp; PENGIRIMAN IKAN</h1>
                    <p class="subtitle">AMS BUDIDAYA Aquaculture Management • Logistik Armada &amp; Pengiriman Mitra</p>
                </td>
                <td style="text-align: right; font-size: 8pt; color: #64748b;">
                    Dicetak: {{ \Carbon\Carbon::now()->translatedFormat('d F Y H:i') }} WIB
                </td>
            </tr>
        </table>
    </div>

    <!-- Summary Metrics -->
    <table class="summary-cards" cellpadding="0" cellspacing="0">
        <tr>
            <td class="summary-card">
                <div class="card-lbl">Total Transaksi</div>
                <div class="card-val">{{ $orders->count() }} Order</div>
            </td>
            <td style="width: 2.6%;"></td>
            <td class="summary-card">
                <div class="card-lbl">Total Volume Terkirim</div>
                <div class="card-val">{{ number_format($totalKg, 1, ',', '.') }} kg</div>
            </td>
            <td style="width: 2.6%;"></td>
            <td class="summary-card">
                <div class="card-lbl">Total Nilai Distribusi</div>
                <div class="card-val">Rp {{ number_format($totalNilai, 0, ',', '.') }}</div>
            </td>
            <td style="width: 2.6%;"></td>
            <td class="summary-card">
                <div class="card-lbl">Rata-rata Order</div>
                <div class="card-val">{{ $orders->count() > 0 ? number_format($totalKg / $orders->count(), 1, ',', '.') : 0 }} kg</div>
            </td>
        </tr>
    </table>

    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 3%;" class="text-center">No</th>
                <th style="width: 10%;">No. Surat Jalan</th>
                <th style="width: 8%;">Tanggal</th>
                <th style="width: 18%;">Mitra Tujuan</th>
                <th style="width: 15%;">Komoditas &amp; Grading</th>
                <th style="width: 12%;">Kolam Asal</th>
                <th style="width: 8%;" class="text-right">Volume</th>
                <th style="width: 13%;" class="text-right">Nilai Tagihan</th>
                <th style="width: 13%;" class="text-center">Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($orders as $index => $o)
            @php
                $st = strtolower($o->status_order ?? 'pending');
                $badgeClass = 'badge-pending';
                if ($st === 'selesai') $badgeClass = 'badge-selesai';
                elseif ($st === 'siap_kirim' || $st === 'dalam_pengiriman') $badgeClass = 'badge-siap';
                elseif (str_contains($st, 'pemberokan')) $badgeClass = 'badge-pemberokan';
            @endphp
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td><strong>{{ $o->no_surat_jalan ?? ('#ORD-' . str_pad($o->id_transaksi, 4, '0', STR_PAD_LEFT)) }}</strong></td>
                <td>{{ \Carbon\Carbon::parse($o->tanggal_order)->format('d/m/Y') }}</td>
                <td>
                    <strong>{{ $o->mitra ? $o->mitra->nama_mitra : 'Mitra #' . $o->id_mitra }}</strong>
                    <div style="font-size: 7pt; color: #64748b;">{{ $o->mitra ? $o->mitra->tipe_mitra : '' }}</div>
                </td>
                <td>
                    {{ $o->batchPembesaran ? $o->batchPembesaran->jenis_ikan : ($o->Jenis_order ?? 'Ikan Segar') }}
                    <div style="font-size: 7pt; color: #0284c7;">{{ $o->grading_size ?? 'Grade Standar' }}</div>
                </td>
                <td>{{ $o->batchPembesaran && $o->batchPembesaran->kolam ? $o->batchPembesaran->kolam->nama_kolam : 'Kolam' }}</td>
                <td class="text-right font-bold">{{ number_format($o->Total_kg, 1, ',', '.') }} kg</td>
                <td class="text-right font-bold">Rp {{ number_format($o->harga_total, 0, ',', '.') }}</td>
                <td class="text-center">
                    <span class="badge {{ $badgeClass }}">{{ strtoupper(str_replace('_', ' ', $o->status_order)) }}</span>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="9" class="text-center" style="padding: 15px; color: #94a3b8;">Belum ada catatan order distribusi.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer-note">
        Laporan Ekspor Distribusi Terverifikasi • SIM-BUDIDAYA Supply Chain Logistics
    </div>
</body>
</html>

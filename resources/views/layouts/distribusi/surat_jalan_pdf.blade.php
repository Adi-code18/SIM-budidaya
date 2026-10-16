<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Surat Jalan - {{ $order->no_surat_jalan ?? 'SJ-' . $order->id_transaksi }}</title>
    <style>
        @page {
            margin: 20mm 15mm 20mm 15mm;
        }
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            color: #1e293b;
            font-size: 11pt;
            line-height: 1.4;
            margin: 0;
            padding: 0;
        }
        .header-table {
            width: 100%;
            border-bottom: 2px solid #0B2570;
            padding-bottom: 12px;
            margin-bottom: 15px;
        }
        .company-title {
            font-size: 18pt;
            font-weight: bold;
            color: #0B2570;
            letter-spacing: 0.5px;
            margin: 0;
        }
        .company-subtitle {
            font-size: 9pt;
            color: #64748b;
            margin: 3px 0 0 0;
        }
        .doc-title-box {
            text-align: right;
        }
        .doc-title {
            font-size: 16pt;
            font-weight: 800;
            color: #031B4E;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin: 0;
        }
        .doc-number {
            font-size: 11pt;
            font-weight: bold;
            color: #0284C7;
            margin: 3px 0 0 0;
        }
        .info-table {
            width: 100%;
            margin-bottom: 20px;
        }
        .info-box {
            width: 48%;
            vertical-align: top;
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            padding: 10px 12px;
        }
        .info-title {
            font-size: 9pt;
            font-weight: bold;
            color: #0B2570;
            text-transform: uppercase;
            border-bottom: 1px dashed #cbd5e1;
            padding-bottom: 4px;
            margin-bottom: 6px;
        }
        .info-row {
            font-size: 9.5pt;
            margin-bottom: 3px;
        }
        .info-label {
            color: #64748b;
            display: inline-block;
            width: 90px;
        }
        .info-val {
            font-weight: 600;
            color: #0f172a;
        }
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .items-table th {
            background-color: #0B2570;
            color: #ffffff;
            font-size: 9pt;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            padding: 8px 10px;
            text-align: left;
            border: 1px solid #0B2570;
        }
        .items-table td {
            font-size: 9.5pt;
            padding: 8px 10px;
            border: 1px solid #cbd5e1;
            vertical-align: middle;
        }
        .items-table tr:nth-child(even) td {
            background-color: #f8fafc;
        }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .badge {
            display: inline-block;
            padding: 2px 6px;
            font-size: 8pt;
            font-weight: bold;
            border-radius: 4px;
            background-color: #e0f2fe;
            color: #0369a1;
        }
        .sop-box {
            background-color: #f0fdf4;
            border: 1px solid #bbf7d0;
            border-radius: 6px;
            padding: 8px 12px;
            margin-bottom: 25px;
            font-size: 8.5pt;
            color: #166534;
        }
        .signatures-table {
            width: 100%;
            margin-top: 30px;
            page-break-inside: avoid;
        }
        .sig-cell {
            width: 33.33%;
            text-align: center;
            vertical-align: top;
            padding: 0 10px;
        }
        .sig-title {
            font-size: 9pt;
            font-weight: bold;
            color: #475569;
            margin-bottom: 60px;
        }
        .sig-name {
            font-size: 9.5pt;
            font-weight: bold;
            color: #0f172a;
            border-bottom: 1px solid #94a3b8;
            padding-bottom: 2px;
            display: inline-block;
            min-width: 130px;
        }
        .sig-role {
            font-size: 8pt;
            color: #64748b;
            margin-top: 3px;
        }
        .footer-note {
            margin-top: 35px;
            font-size: 8pt;
            color: #94a3b8;
            text-align: center;
            border-top: 1px solid #e2e8f0;
            padding-top: 8px;
        }
    </style>
</head>
<body>

    <!-- Header Dokumen -->
    <table class="header-table" cellpadding="0" cellspacing="0">
        <tr>
            <td style="vertical-align: middle;">
                <h1 class="company-title">AMS BUDIDAYA</h1>
                <p class="company-subtitle">
                    Aquafarm Smart Aquaculture &amp; Supply Chain Logistics<br>
                    Jl. Raya Perikanan Tambak No. 18 • Telp: (0265) 7890123 • info@budidaya.com
                </p>
            </td>
            <td class="doc-title-box" style="vertical-align: middle;">
                <div class="doc-title">SURAT JALAN</div>
                <div class="doc-number">{{ $order->no_surat_jalan ?? ('SJ/' . date('Y/m/') . str_pad($order->id_transaksi, 3, '0', STR_PAD_LEFT)) }}</div>
                <div style="font-size: 8.5pt; color: #64748b; margin-top: 2px;">
                    Order Ref: #ORD-{{ str_pad($order->id_transaksi, 4, '0', STR_PAD_LEFT) }}
                </div>
            </td>
        </tr>
    </table>

    <!-- Rincian Mitra & Pengiriman -->
    <table class="info-table" cellpadding="0" cellspacing="0">
        <tr>
            <td class="info-box">
                <div class="info-title">PENGIRIM (FARM OPERASIONAL)</div>
                <div class="info-row"><span class="info-label">Asal Tambak:</span> <span class="info-val">Central Farm Tambak Aquafarm</span></div>
                <div class="info-row"><span class="info-label">Petugas Kirim:</span> <span class="info-val">{{ $order->user ? $order->user->name : 'Divisi Logistik & Distribusi' }}</span></div>
                <div class="info-row"><span class="info-label">Tgl Kirim:</span> <span class="info-val">{{ \Carbon\Carbon::parse($order->tanggal_order ?? now())->translatedFormat('d F Y') }}</span></div>
                <div class="info-row"><span class="info-label">Status SOP:</span> <span class="badge">{{ strtoupper(str_replace('_', ' ', $order->status_order ?? 'SIAP KIRIM')) }}</span></div>
            </td>
            <td style="width: 4%;"></td>
            <td class="info-box">
                <div class="info-title">PENERIMA (TUJUAN MITRA)</div>
                <div class="info-row"><span class="info-label">Nama Mitra:</span> <span class="info-val">{{ $order->mitra ? $order->mitra->nama_mitra : 'Mitra Toko/Restoran' }}</span></div>
                <div class="info-row"><span class="info-label">Kategori Mitra:</span> <span class="info-val">{{ $order->mitra ? $order->mitra->tipe_mitra : 'Distributor' }}</span></div>
                <div class="info-row"><span class="info-label">Alamat:</span> <span class="info-val">{{ $order->mitra ? $order->mitra->alamat : '-' }}</span></div>
                <div class="info-row"><span class="info-label">Telepon/WA:</span> <span class="info-val">{{ ($order->mitra && $order->mitra->user) ? $order->mitra->user->no_tlp : ($order->mitra->kontak ?? '-') }}</span></div>
            </td>
        </tr>
    </table>

    <!-- Tabel Komoditas Ikan -->
    <table class="items-table">
        <thead>
            <tr>
                <th style="width: 5%;" class="text-center">No</th>
                <th style="width: 30%;">Jenis Ikan / Komoditas</th>
                <th style="width: 20%;">Kolam Asal Panen</th>
                <th style="width: 25%;">Kelas Grading / Ukuran</th>
                <th style="width: 20%;" class="text-right">Volume (Kg)</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="text-center">1</td>
                <td>
                    <strong>{{ $order->batchPembesaran ? $order->batchPembesaran->jenis_ikan : ($order->Jenis_order ?? 'Ikan Segar') }}</strong>
                    <div style="font-size: 8pt; color: #64748b;">Kondisi: Hidup / Segar Terpilih</div>
                </td>
                <td>
                    {{ $order->batchPembesaran && $order->batchPembesaran->kolam ? $order->batchPembesaran->kolam->nama_kolam : 'Kolam Panen' }}
                </td>
                <td>
                    <strong>{{ $order->grading_size ?? 'Grade A (Standar)' }}</strong>
                    @if(!empty($order->catatan_grading))
                        <div style="font-size: 8pt; color: #475569;">{{ $order->catatan_grading }}</div>
                    @endif
                </td>
                <td class="text-right" style="font-size: 11pt; font-weight: bold; color: #0B2570;">
                    {{ number_format($order->Total_kg, 1, ',', '.') }} kg
                </td>
            </tr>
            @if($order->harga_total > 0)
            <tr style="background-color: #f1f5f9; font-weight: bold;">
                <td colspan="4" class="text-right" style="padding: 10px;">Total Tagihan / Nilai Order:</td>
                <td class="text-right" style="color: #031B4E; font-size: 11pt;">
                    Rp {{ number_format($order->harga_total, 0, ',', '.') }}
                </td>
            </tr>
            @endif
        </tbody>
    </table>

    <!-- Info SOP Pemberokan & Pengiriman -->
    <div class="sop-box">
        <strong>Ketentuan SOP Pengiriman DDP (Delivery Duty Paid) AMS BUDIDAYA:</strong><br>
        • Wajib melewati masa pemberokan/pemuasaan 12–24 jam guna pengosongan isi perut dan higienitas bau lumpur.<br>
        • Ambang batas toleransi kematian perjalanan maksimal &lt; 2%. Wajib diperiksa dan ditimbang bersama kurir saat serah terima.
    </div>

    <!-- Tanda Tangan Serah Terima -->
    <table class="signatures-table" cellpadding="0" cellspacing="0">
        <tr>
            <td class="sig-cell">
                <div class="sig-title">Dibuat Oleh (Admin/Manajer)</div>
                <div class="sig-name">{{ Auth::user()->name ?? 'Manajer Operasional' }}</div>
                <div class="sig-role">AMS BUDIDAYA Aquaculture</div>
            </td>
            <td class="sig-cell">
                <div class="sig-title">Dibawa Oleh (Kurir Armada)</div>
                <div class="sig-name">&nbsp;</div>
                <div class="sig-role">Petugas Transportasi</div>
            </td>
            <td class="sig-cell">
                <div class="sig-title">Diterima Oleh (Mitra / Outlet)</div>
                <div class="sig-name">&nbsp;</div>
                <div class="sig-role">Cap &amp; Tanda Tangan Penerima</div>
            </td>
        </tr>
    </table>

    <div class="footer-note">
        Dokumen resmi Surat Jalan dicetak otomatis melalui AMS BUDIDAYA System pada {{ \Carbon\Carbon::now()->translatedFormat('d F Y H:i:s') }} WIB.
    </div>

</body>
</html>

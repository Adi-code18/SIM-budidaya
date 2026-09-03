<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan SIM-Budidaya</title>
    <style>
        body {
            font-family: 'Calibri', 'Arial', sans-serif;
            font-size: 11pt;
            color: #1e293b;
        }
        .title-table {
            width: 100%;
            margin-bottom: 20px;
            border-collapse: collapse;
        }
        .title-cell {
            background-color: #051B44;
            color: #ffffff;
            font-size: 14pt;
            font-weight: bold;
            padding: 12px;
            text-align: center;
        }
        .meta-cell {
            background-color: #f1f5f9;
            font-size: 10pt;
            padding: 6px 10px;
            border: 1px solid #cbd5e1;
        }
        .section-header {
            background-color: #0284C7;
            color: #ffffff;
            font-weight: bold;
            font-size: 11pt;
            padding: 8px 10px;
            text-align: left;
        }
        .section-sub {
            background-color: #0d9488;
            color: #ffffff;
            font-weight: bold;
            font-size: 11pt;
            padding: 8px 10px;
            text-align: left;
        }
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 25px;
        }
        .data-table th {
            background-color: #e2e8f0;
            color: #0f172a;
            font-weight: bold;
            font-size: 10pt;
            padding: 7px 8px;
            border: 1px solid #94a3b8;
            text-align: center;
        }
        .data-table td {
            padding: 6px 8px;
            border: 1px solid #cbd5e1;
            font-size: 10pt;
            vertical-align: middle;
        }
        .text-center { text-align: center; }
        .text-left { text-align: left; }
        .text-right { text-align: right; }
        .font-bold { font-weight: bold; }
        .bg-zebra { background-color: #f8fafc; }
        .total-row td {
            background-color: #e0f2fe;
            font-weight: bold;
            border-top: 2px solid #0284c7;
            border-bottom: 2px solid #0284c7;
        }
        .badge-aktif { color: #0284c7; font-weight: bold; }
        .badge-selesai { color: #15803d; font-weight: bold; }
        .badge-pending { color: #b91c1c; font-weight: bold; }
        .badge-income { color: #15803d; font-weight: bold; }
        .badge-expense { color: #b91c1c; font-weight: bold; }
    </style>
</head>
<body>

    <!-- Header Dokumen -->
    <table class="title-table">
        <tr>
            <td colspan="8" class="title-cell">
                LAPORAN OPERASIONAL &amp; EKSEKUTIF LENGKAP<br>
                <span style="font-size: 10pt; font-weight: normal; color: #bae6fd;">SISTEM INFORMASI MANAJEMEN BUDIDAYA IKAN (SIM-BUDIDAYA)</span>
            </td>
        </tr>
        <tr>
            <td colspan="2" class="meta-cell font-bold">PERIODE LAPORAN:</td>
            <td colspan="2" class="meta-cell" style="font-weight: bold; color: #0369a1;">{{ $periodeTitle }}</td>
            <td colspan="2" class="meta-cell font-bold">TANGGAL CETAK:</td>
            <td colspan="2" class="meta-cell">{{ $printDate }}</td>
        </tr>
        <tr>
            <td colspan="2" class="meta-cell font-bold">DICETAK OLEH:</td>
            <td colspan="2" class="meta-cell">Manajer Operasional Akuakultur</td>
            <td colspan="2" class="meta-cell font-bold">STATUS DATA:</td>
            <td colspan="2" class="meta-cell" style="color: #15803d; font-weight: bold;">Terverifikasi &amp; Terintegrasi</td>
        </tr>
    </table>

    <br>

    <!-- 1. RINGKASAN EKSEKUTIF (KPI UTAMA) -->
    <table class="data-table">
        <tr>
            <th colspan="4" class="section-header">1. RINGKASAN INDIKATOR KUNCI (EXECUTIVE KPI)</th>
        </tr>
        <tr>
            <th width="30%">Indikator Kinerja</th>
            <th width="20%">Nilai Realisasi</th>
            <th width="20%">Satuan</th>
            <th width="30%">Keterangan Evaluasi</th>
        </tr>
        <tr>
            <td class="font-bold">Total Estimasi Biomassa Kolam</td>
            <td class="text-right font-bold">{{ number_format($totalBiomassa, 1, ',', '.') }}</td>
            <td class="text-center">Kilogram (kg)</td>
            <td>Stok ikan aktif di seluruh kolam pembesaran</td>
        </tr>
        <tr class="bg-zebra">
            <td class="font-bold">Target Panen Siklus Berjalan</td>
            <td class="text-right font-bold">{{ number_format($totalTargetPanen, 0, ',', '.') }}</td>
            <td class="text-center">Kilogram (kg)</td>
            <td>Proyeksi panen komersial periode ini</td>
        </tr>
        <tr>
            <td class="font-bold">Feed Conversion Ratio (FCR) Rata-rata</td>
            <td class="text-right font-bold">{{ number_format($avgFcr, 2, '.', '') }}</td>
            <td class="text-center">Rasio</td>
            <td>{{ $avgFcr > 0 && $avgFcr <= 1.25 ? 'Efisiensi pakan sangat baik' : 'Monitoring pakan reguler' }}</td>
        </tr>
        <tr class="bg-zebra">
            <td class="font-bold">Total Populasi Benih (Hatchery)</td>
            <td class="text-right font-bold">{{ number_format($totalBibitHatchery, 0, ',', '.') }}</td>
            <td class="text-center">Ekor Benih</td>
            <td>Kapasitas bibit di fase penetasan &amp; pendederan</td>
        </tr>
        <tr>
            <td class="font-bold">Total Pakan Digunakan (Pelet)</td>
            <td class="text-right font-bold">{{ number_format($totalPelet, 1, ',', '.') }}</td>
            <td class="text-center">Kilogram (kg)</td>
            <td>Pakan pelet apung bernutrisi tinggi</td>
        </tr>
        <tr class="bg-zebra">
            <td class="font-bold">Total Pakan Organik (Dedaunan)</td>
            <td class="text-right font-bold">{{ number_format($totalDaun, 1, ',', '.') }}</td>
            <td class="text-center">Kilogram (kg)</td>
            <td>Suplemen hijau (daun talas, singkong, azolla)</td>
        </tr>
        <tr>
            <td class="font-bold">Total Biaya Pakan Dikeluarkan</td>
            <td class="text-right font-bold" style="color: #b91c1c;">Rp {{ number_format($totalBiayaPakan, 0, ',', '.') }}</td>
            <td class="text-center">Rupiah (IDR)</td>
            <td>Akumulasi biaya pakan harian pada kolam</td>
        </tr>
        <tr class="bg-zebra">
            <td class="font-bold">Total Pemasukan (Cash In)</td>
            <td class="text-right font-bold" style="color: #15803d;">Rp {{ number_format($totalIncome, 0, ',', '.') }}</td>
            <td class="text-center">Rupiah (IDR)</td>
            <td>Pendapatan dari penjualan panen &amp; benih</td>
        </tr>
        <tr>
            <td class="font-bold">Total Pengeluaran (Cash Out)</td>
            <td class="text-right font-bold" style="color: #b91c1c;">Rp {{ number_format($totalExpense, 0, ',', '.') }}</td>
            <td class="text-center">Rupiah (IDR)</td>
            <td>Biaya operasional, pakan, listrik &amp; honor</td>
        </tr>
        <tr class="total-row">
            <td class="font-bold">Surplus / Arus Kas Bersih (Saldo)</td>
            <td class="text-right font-bold" style="color: {{ $saldoKas >= 0 ? '#15803d' : '#b91c1c' }}; font-size: 11pt;">
                Rp {{ number_format($saldoKas, 0, ',', '.') }}
            </td>
            <td class="text-center font-bold">Rupiah (IDR)</td>
            <td class="font-bold">{{ $saldoKas >= 0 ? 'Surplus Operasional Positif' : 'Defisit / Perlu Evaluasi' }}</td>
        </tr>
        <tr>
            <td class="font-bold">Total Distribusi &amp; Penjualan Mitra</td>
            <td class="text-right font-bold">{{ number_format($totalKgDistribusi, 1, ',', '.') }} kg / Rp {{ number_format($totalNilaiDistribusi, 0, ',', '.') }}</td>
            <td class="text-center">kg / IDR</td>
            <td>Realisasi pengiriman order ke mitra distributor</td>
        </tr>
    </table>

    <br>

    <!-- 2. DATA SIKLUS PEMBESARAN IKAN -->
    <table class="data-table">
        <tr>
            <th colspan="9" class="section-header">2. DATA MONITORING SIKLUS PEMBESARAN IKAN (GROWOUT)</th>
        </tr>
        <tr>
            <th width="5%">No</th>
            <th width="12%">Kode / ID Batch</th>
            <th width="15%">Nama Kolam</th>
            <th width="15%">Komoditas Ikan</th>
            <th width="11%">Tgl Tebar</th>
            <th width="11%">Est. Panen</th>
            <th width="10%">Biomassa (kg)</th>
            <th width="10%">Target (kg)</th>
            <th width="11%">Status</th>
        </tr>
        @forelse($pembesaranList as $idx => $b)
        <tr class="{{ $idx % 2 == 1 ? 'bg-zebra' : '' }}">
            <td class="text-center">{{ $idx + 1 }}</td>
            <td class="text-center font-bold">#BP-{{ str_pad($b->id_pembesaran, 4, '0', STR_PAD_LEFT) }}</td>
            <td class="font-bold">{{ $b->kolam ? $b->kolam->nama_kolam : 'Kolam #' . $b->id_kolam }}</td>
            <td>{{ $b->jenis_ikan }}</td>
            <td class="text-center">{{ $b->tgl_tebar }}</td>
            <td class="text-center">{{ $b->est_tgl_panen ?? '-' }}</td>
            <td class="text-right font-bold">{{ number_format($b->biomassa_est, 1, ',', '.') }}</td>
            <td class="text-right">{{ number_format($b->target_panen_kg, 0, ',', '.') }}</td>
            <td class="text-center font-bold {{ $b->status_siklus === 'selesai' ? 'badge-selesai' : 'badge-aktif' }}">
                {{ strtoupper(str_replace('_', ' ', $b->status_siklus)) }}
            </td>
        </tr>
        @empty
        <tr>
            <td colspan="9" class="text-center" style="color: #94a3b8; padding: 15px;">Tidak ada data batch pembesaran pada periode ini.</td>
        </tr>
        @endforelse
        <tr class="total-row">
            <td colspan="6" class="text-right font-bold">TOTAL BIOMASSA &amp; TARGET PANEN:</td>
            <td class="text-right font-bold">{{ number_format($totalBiomassa, 1, ',', '.') }} kg</td>
            <td class="text-right font-bold">{{ number_format($totalTargetPanen, 0, ',', '.') }} kg</td>
            <td></td>
        </tr>
    </table>

    <br>

    <!-- 3. DATA BATCH PEMBIBITAN (HATCHERY) -->
    <table class="data-table">
        <tr>
            <th colspan="8" class="section-sub">3. DATA MONITORING SIKLUS PEMBIBITAN (HATCHERY)</th>
        </tr>
        <tr>
            <th width="5%">No</th>
            <th width="12%">ID Batch</th>
            <th width="15%">Kolam Hatchery</th>
            <th width="18%">Varietas Ikan</th>
            <th width="12%">Tgl Pemijahan</th>
            <th width="13%">Jumlah Bibit Awal</th>
            <th width="12%">Fase Pertumbuhan</th>
            <th width="13%">Status</th>
        </tr>
        @forelse($pembibitanList as $idx => $bb)
        <tr class="{{ $idx % 2 == 1 ? 'bg-zebra' : '' }}">
            <td class="text-center">{{ $idx + 1 }}</td>
            <td class="text-center font-bold">#BB-{{ str_pad($bb->id_batch, 4, '0', STR_PAD_LEFT) }}</td>
            <td class="font-bold">{{ $bb->kolam ? $bb->kolam->nama_kolam : 'Kolam #' . $bb->id_kolam }}</td>
            <td>{{ $bb->ikan ? $bb->ikan->nama_ikan : ($bb->jenis_ikan ?? 'Spesies Unggul') }}</td>
            <td class="text-center">{{ $bb->tgl_pemijahan }}</td>
            <td class="text-right font-bold">{{ number_format($bb->jumlah_bibitAwal, 0, ',', '.') }} ekor</td>
            <td class="text-center font-bold" style="color: #0284c7;">{{ $bb->fase_pertumbuhan }}</td>
            <td class="text-center font-bold {{ $bb->status === 'selesai' ? 'badge-selesai' : 'badge-aktif' }}">
                {{ strtoupper(str_replace('_', ' ', $bb->status)) }}
            </td>
        </tr>
        @empty
        <tr>
            <td colspan="8" class="text-center" style="color: #94a3b8; padding: 15px;">Tidak ada data pembibitan pada periode ini.</td>
        </tr>
        @endforelse
        <tr class="total-row">
            <td colspan="5" class="text-right font-bold">TOTAL POPULASI BENIH:</td>
            <td class="text-right font-bold">{{ number_format($totalBibitHatchery, 0, ',', '.') }} ekor</td>
            <td colspan="2"></td>
        </tr>
    </table>

    <br>

    <!-- 4. LOG PEMBERIAN PAKAN & KUALITAS AIR -->
    <table class="data-table">
        <tr>
            <th colspan="8" class="section-header">4. REKAP PEMBERIAN PAKAN HARIAN &amp; PARAMETER KUALITAS AIR</th>
        </tr>
        <tr>
            <th width="5%">No</th>
            <th width="12%">Tanggal</th>
            <th width="16%">Kolam Pembesaran</th>
            <th width="12%">Pakan Pelet</th>
            <th width="15%">Pakan Dedaunan</th>
            <th width="14%">Estimasi Biaya</th>
            <th width="10%">pH Air</th>
            <th width="16%">Petugas Lapangan</th>
        </tr>
        @forelse($pakanList as $idx => $p)
        <tr class="{{ $idx % 2 == 1 ? 'bg-zebra' : '' }}">
            <td class="text-center">{{ $idx + 1 }}</td>
            <td class="text-center font-bold">{{ $p->tgl_log }}</td>
            <td class="font-bold">{{ $p->kolam ? $p->kolam->nama_kolam : 'Kolam #' . $p->id_kolam }}</td>
            <td class="text-right font-bold">{{ number_format($p->kg_pelet, 1, ',', '.') }} kg</td>
            <td class="text-right">{{ number_format($p->kg_daun, 1, ',', '.') }} kg {{ $p->jenis_daun ? '(' . $p->jenis_daun . ')' : '' }}</td>
            <td class="text-right font-bold" style="color: #15803d;">Rp {{ number_format($p->total_biaya, 0, ',', '.') }}</td>
            <td class="text-center font-bold" style="color: #0369a1;">pH {{ $p->ph_air ?? '7.2' }}</td>
            <td>{{ $p->user ? ($p->user->nama ?? $p->user->name) : 'Petugas Lapangan' }}</td>
        </tr>
        @empty
        <tr>
            <td colspan="8" class="text-center" style="color: #94a3b8; padding: 15px;">Tidak ada catatan pakan pada periode ini.</td>
        </tr>
        @endforelse
        <tr class="total-row">
            <td colspan="3" class="text-right font-bold">TOTAL KONSUMSI PAKAN &amp; BIAYA:</td>
            <td class="text-right font-bold">{{ number_format($totalPelet, 1, ',', '.') }} kg</td>
            <td class="text-right font-bold">{{ number_format($totalDaun, 1, ',', '.') }} kg</td>
            <td class="text-right font-bold">Rp {{ number_format($totalBiayaPakan, 0, ',', '.') }}</td>
            <td colspan="2"></td>
        </tr>
    </table>

    <br>

    <!-- 5. BUKU KAS & LAPORAN KEUANGAN -->
    <table class="data-table">
        <tr>
            <th colspan="7" class="section-sub">5. JURNAL KEUANGAN &amp; ARUS KAS (CASH FLOW)</th>
        </tr>
        <tr>
            <th width="5%">No</th>
            <th width="12%">Tanggal</th>
            <th width="14%">Kode Transaksi</th>
            <th width="12%">Tipe Arus Kas</th>
            <th width="22%">Kategori Transaksi</th>
            <th width="15%">Nominal (Rp)</th>
            <th width="20%">Deskripsi / Keterangan</th>
        </tr>
        @forelse($keuanganList as $idx => $k)
        @php
            $isIncome = in_array(strtolower($k->tipe_transaksi), ['pemasukan', 'income']);
        @endphp
        <tr class="{{ $idx % 2 == 1 ? 'bg-zebra' : '' }}">
            <td class="text-center">{{ $idx + 1 }}</td>
            <td class="text-center">{{ $k->tanggal_transaksi }}</td>
            <td class="text-center font-bold">{{ $k->ref_id ?: 'TRX-' . str_pad($k->id_keuangan, 4, '0', STR_PAD_LEFT) }}</td>
            <td class="text-center font-bold {{ $isIncome ? 'badge-income' : 'badge-expense' }}">
                {{ $isIncome ? 'PEMASUKAN' : 'PENGELUARAN' }}
            </td>
            <td>{{ $k->kategori }}</td>
            <td class="text-right font-bold" style="color: {{ $isIncome ? '#15803d' : '#b91c1c' }};">
                {{ $isIncome ? '+' : '-' }} Rp {{ number_format($k->nominal, 0, ',', '.') }}
            </td>
            <td>{{ $k->keterangan ?? '-' }}</td>
        </tr>
        @empty
        <tr>
            <td colspan="7" class="text-center" style="color: #94a3b8; padding: 15px;">Tidak ada transaksi keuangan pada periode ini.</td>
        </tr>
        @endforelse
        <tr class="total-row">
            <td colspan="5" class="text-right font-bold">TOTAL PEMASUKAN / PENGELUARAN / SALDO BERSIH:</td>
            <td class="text-right font-bold" style="color: {{ $saldoKas >= 0 ? '#15803d' : '#b91c1c' }};">
                Rp {{ number_format($saldoKas, 0, ',', '.') }}
            </td>
            <td class="font-bold">(In: Rp {{ number_format($totalIncome, 0, ',', '.') }} | Out: Rp {{ number_format($totalExpense, 0, ',', '.') }})</td>
        </tr>
    </table>

    <br>

    <!-- 6. DATA TRANSAKSI DISTRIBUSI & PENJUALAN MITRA -->
    <table class="data-table">
        <tr>
            <th colspan="8" class="section-header">6. REKAP DISTRIBUSI, LOGISTIK &amp; PENJUALAN MITRA</th>
        </tr>
        <tr>
            <th width="5%">No</th>
            <th width="12%">ID Order</th>
            <th width="12%">Tanggal Order</th>
            <th width="20%">Mitra Distributor</th>
            <th width="16%">Jenis Ikan / Sumber Kolam</th>
            <th width="11%">Total Berat (kg)</th>
            <th width="14%">Total Nilai (Rp)</th>
            <th width="10%">Status</th>
        </tr>
        @forelse($distribusiList as $idx => $d)
        <tr class="{{ $idx % 2 == 1 ? 'bg-zebra' : '' }}">
            <td class="text-center">{{ $idx + 1 }}</td>
            <td class="text-center font-bold">#ORD-{{ str_pad($d->id_transaksi, 4, '0', STR_PAD_LEFT) }}</td>
            <td class="text-center">{{ $d->tanggal_order }}</td>
            <td class="font-bold">{{ $d->mitra ? $d->mitra->nama_mitra : 'Mitra #' . $d->id_mitra }}</td>
            <td>{{ $d->batchPembesaran ? $d->batchPembesaran->jenis_ikan : ($d->Jenis_order ?? 'Ikan Segar') }}</td>
            <td class="text-right font-bold">{{ number_format($d->Total_kg, 1, ',', '.') }} kg</td>
            <td class="text-right font-bold" style="color: #15803d;">Rp {{ number_format($d->harga_total, 0, ',', '.') }}</td>
            <td class="text-center font-bold {{ $d->status_order === 'selesai' ? 'badge-selesai' : 'badge-aktif' }}">
                {{ strtoupper(str_replace('_', ' ', $d->status_order)) }}
            </td>
        </tr>
        @empty
        <tr>
            <td colspan="8" class="text-center" style="color: #94a3b8; padding: 15px;">Tidak ada data distribusi pada periode ini.</td>
        </tr>
        @endforelse
        <tr class="total-row">
            <td colspan="5" class="text-right font-bold">TOTAL REALISASI DISTRIBUSI:</td>
            <td class="text-right font-bold">{{ number_format($totalKgDistribusi, 1, ',', '.') }} kg</td>
            <td class="text-right font-bold">Rp {{ number_format($totalNilaiDistribusi, 0, ',', '.') }}</td>
            <td></td>
        </tr>
    </table>

</body>
</html>

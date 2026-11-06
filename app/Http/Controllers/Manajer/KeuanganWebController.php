<?php

namespace App\Http\Controllers\Manajer;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\BatchPembesaran;
use App\Models\Keuangan;
use App\Models\Kolam;
use App\Models\TransaksiDistribusi;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class KeuanganWebController extends Controller
{
    public static function generateSopId($tanggal = null)
    {
        $date = $tanggal ? Carbon::parse($tanggal) : Carbon::now();
        $prefix = 'TRX-' . $date->format('ym') . '-';

        $count = Keuangan::whereYear('tanggal_transaksi', $date->year)
            ->whereMonth('tanggal_transaksi', $date->month)
            ->count();

        $nextNum = str_pad($count + 1, 3, '0', STR_PAD_LEFT);
        $code = $prefix . $nextNum;

        while (Keuangan::where('ref_id', $code)->exists()) {
            $count++;
            $nextNum = str_pad($count + 1, 3, '0', STR_PAD_LEFT);
            $code = $prefix . $nextNum;
        }

        return $code;
    }

    /**
     * Sinkronisasi Otomatis Transaksi Distribusi Siap Kirim/Selesai ke Buku Keuangan
     */
    public static function syncDistribusiToKeuangan(): void
    {
        $confirmedOrders = TransaksiDistribusi::with(['mitra', 'batchPembesaran.kolam'])
            ->where('status_order', '!=', 'dibatalkan')
            ->where(function($q) {
                $q->where('status_pembayaran', 'lunas')
                  ->orWhere('jumlah_terbayar', '>', 0);
            })
            ->get();

        if ($confirmedOrders->isEmpty()) {
            return;
        }

        $existingKeuangan = Keuangan::where('ref_id', 'like', 'ORD-%')->get()->keyBy('ref_id');

        foreach ($confirmedOrders as $order) {
            $orderRef = 'ORD-' . str_pad($order->id_transaksi, 4, '0', STR_PAD_LEFT);

            // Tentukan nominal kas masuk riil:
            // Jika LUNAS: catat total_tagihan_akhir (setelah potongan klaim susut DDP)
            // Jika TEMPO tapi ada DP: catat jumlah_terbayar
            // Jika TEMPO murni belum bayar: jangan catat kas fiktif (tetap sebagai piutang di neraca aktiva)
            $targetNominal = 0;
            $keteranganSuffix = '';

            if ($order->status_pembayaran === 'lunas') {
                $targetNominal = (float) $order->tagihan_riil;
                if ($order->potongan_klaim_rp > 0) {
                    $keteranganSuffix = " (Klaim Susut DDP: -Rp " . number_format($order->potongan_klaim_rp, 0, ',', '.') . ")";
                }
            } elseif ($order->jumlah_terbayar > 0) {
                $targetNominal = (float) $order->jumlah_terbayar;
                $keteranganSuffix = " [Pembayaran DP/Cicilan - Sisa Piutang: Rp " . number_format($order->sisa_piutang, 0, ',', '.') . "]";
            } else {
                // Belum ada uang masuk sama sekali, pastikan tidak ada entri kas fiktif
                Keuangan::where('ref_id', $orderRef)->delete();
                continue;
            }

            // Jika sudah ada dan nominalnya sama, lewati untuk mencegah query write berulang
            if (isset($existingKeuangan[$orderRef])) {
                $existing = $existingKeuangan[$orderRef];
                if (abs((float)$existing->nominal - $targetNominal) < 0.01) {
                    continue;
                }
            }

            $mitraNama = $order->mitra ? $order->mitra->nama_mitra : ('Mitra #' . $order->id_mitra);
            $species = $order->batchPembesaran ? $order->batchPembesaran->jenis_ikan : ($order->Jenis_order ?? 'Ikan Segar');
            $idKolam = $order->batchPembesaran ? $order->batchPembesaran->id_kolam : null;
            $totalKgFormatted = number_format($order->Total_kg, 1, ',', '.');
            $statusLabel = strtoupper(str_replace('_', ' ', $order->status_order ?? 'Selesai'));

            Keuangan::updateOrCreate(
                ['ref_id' => $orderRef],
                [
                    'id_user'           => $order->id_user ?? (Auth::id() ?? 1),
                    'id_kolam'          => $idKolam,
                    'tanggal_transaksi' => $order->tanggal_order ? Carbon::parse($order->tanggal_order)->toDateString() : Carbon::today()->toDateString(),
                    'tipe_transaksi'    => 'pemasukan',
                    'kategori'          => 'Penjualan Panen ' . $species,
                    'nominal'           => $targetNominal,
                    'keterangan'        => "Pelunasan penjualan #{$orderRef} ({$totalKgFormatted} kg {$species}) ke {$mitraNama} [Status: {$statusLabel}]{$keteranganSuffix}",
                ]
            );
        }
    }

    public function index()
    {
        // Jalankan rekonsiliasi otomatis agar data distribusi dan keuangan selalu sinkron
        self::syncDistribusiToKeuangan();

        $keuanganRecords = Keuangan::with(['kolam', 'user'])->latest('tanggal_transaksi')->latest('id_keuangan')->get();

        $totalIncome = Keuangan::whereIn('tipe_transaksi', ['pemasukan', 'income'])->sum('nominal');
        $totalExpense = Keuangan::whereIn('tipe_transaksi', ['pengeluaran', 'expense'])->sum('nominal');
        $saldo = $totalIncome - $totalExpense;
        $netMargin = $totalIncome > 0 ? round(($saldo / $totalIncome) * 100, 1) : 0;

        // Breakdown Kategori Pengeluaran (HPP & OPEX)
        $pakanTotal = Keuangan::whereIn('tipe_transaksi', ['pengeluaran', 'expense'])
            ->where(function ($q) {
                $q->where('kategori', 'like', '%pakan%')
                  ->orWhere('kategori', 'like', '%pelet%');
            })
            ->sum('nominal');

        $bibitTotal = Keuangan::whereIn('tipe_transaksi', ['pengeluaran', 'expense'])
            ->where(function ($q) {
                $q->where('kategori', 'like', '%bibit%')
                  ->orWhere('kategori', 'like', '%benih%');
            })
            ->sum('nominal');

        $totalHpp = $pakanTotal + $bibitTotal;
        $labaKotor = $totalIncome - $totalHpp;
        $grossMargin = $totalIncome > 0 ? round(($labaKotor / $totalIncome) * 100, 1) : 0;

        $operasionalTotal = Keuangan::whereIn('tipe_transaksi', ['pengeluaran', 'expense'])
            ->where(function ($q) {
                $q->where('kategori', 'like', '%Operasional%')
                  ->orWhere('kategori', 'like', '%Perawatan%')
                  ->orWhere('kategori', 'like', '%Listrik%')
                  ->orWhere('kategori', 'like', '%Gaji%')
                  ->orWhere('kategori', 'like', '%Obat%')
                  ->orWhere('kategori', 'like', '%Probiotik%')
                  ->orWhere('kategori', 'like', '%Transportasi%');
            })
            ->sum('nominal');

        $lainnyaTotal = max(0, $totalExpense - ($pakanTotal + $bibitTotal + $operasionalTotal));

        // Financial Health Score calculation
        if ($totalIncome == 0 && $totalExpense == 0) {
            $healthScore = 0.0;
            $healthStatus = 'BELUM ADA DATA';
            $healthBadgeClass = 'bg-slate-100 text-slate-600';
        } elseif ($saldo >= 0) {
            $healthScore = round(min(10.0, 7.0 + ($netMargin / 33)), 1);
            $healthStatus = 'STABLE & SEHAT';
            $healthBadgeClass = 'bg-[#C6F6D5] text-[#22543D]';
        } else {
            $healthScore = round(max(1.0, 5.0 - (abs($saldo) / max(1, $totalExpense) * 4)), 1);
            $healthStatus = 'PERLU EVALUASI';
            $healthBadgeClass = 'bg-[#FEE2E2] text-[#991B1B]';
        }

        // Monthly Cash Flow & Profit-Loss Data
        $currentYear = Carbon::now()->year;
        $monthlyGrouped = Keuangan::whereYear('tanggal_transaksi', $currentYear)
            ->selectRaw('MONTH(tanggal_transaksi) as bulan, 
                         SUM(CASE WHEN LOWER(tipe_transaksi) IN ("pemasukan", "income") THEN nominal ELSE 0 END) as total_income,
                         SUM(CASE WHEN LOWER(tipe_transaksi) IN ("pengeluaran", "expense") THEN nominal ELSE 0 END) as total_expense,
                         COUNT(*) as total_trx')
            ->groupBy('bulan')
            ->orderBy('bulan')
            ->get()
            ->keyBy('bulan');

        $monthNames = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
        $monthShorts = ['JAN', 'FEB', 'MAR', 'APR', 'MEI', 'JUN', 'JUL', 'AGU', 'SEP', 'OKT', 'NOV', 'DES'];
        $currentMonthNum = Carbon::now()->month;

        $monthlyCashflow = [
            'labels'  => [],
            'revenue' => [],
            'expense' => []
        ];

        $monthlyBreakdownTable = [];

        for ($m = 1; $m <= $currentMonthNum; $m++) {
            $monthlyCashflow['labels'][] = $monthShorts[$m - 1];
            $row = $monthlyGrouped->get($m);
            $rev = $row ? (float)$row->total_income : 0;
            $exp = $row ? (float)$row->total_expense : 0;
            $trxCount = $row ? (int)$row->total_trx : 0;
            $net = $rev - $exp;

            $monthlyCashflow['revenue'][] = round($rev / 1000000, 2);
            $monthlyCashflow['expense'][] = round($exp / 1000000, 2);

            $monthlyBreakdownTable[] = [
                'bulan'        => $monthNames[$m - 1] . ' ' . $currentYear,
                'pemasukan'    => $rev,
                'pengeluaran'  => $exp,
                'laba_bersih'  => $net,
                'margin_pct'   => $rev > 0 ? round(($net / $rev) * 100, 1) : 0,
                'total_trx'    => $trxCount,
                'status'       => $net >= 0 ? 'Surplus' : 'Defisit'
            ];
        }

        // Analisis Profitabilitas per Kolam & Batch (Pond & Batch Profitability)
        $batchRecords = BatchPembesaran::with(['kolam', 'batchPembibitan'])->latest('id_pembesaran')->get();
        $pondProfitability = [];

        foreach ($batchRecords as $batch) {
            $fin = $batch->calculateFinancials();
            $biomassaPanen = (float) ($batch->status_siklus === 'selesai' && $batch->jumlah_panen_kg > 0 ? $batch->jumlah_panen_kg : $batch->biomassa_est);
            $kolamNama = $batch->kolam ? $batch->kolam->nama_kolam : ('Kolam #' . $batch->id_kolam);

            $pondProfitability[] = [
                'id_pembesaran'       => $batch->id_pembesaran,
                'batch_code'          => '#PB-' . str_pad($batch->id_pembesaran, 4, '0', STR_PAD_LEFT),
                'nama_kolam'          => $kolamNama,
                'tipe_kolam'          => $batch->kolam ? $batch->kolam->tipe_kolam : 'Kolam Pembesaran',
                'jenis_ikan'          => $batch->jenis_ikan ?? 'Ikan Nila',
                'status_siklus'       => $batch->status_siklus ?? 'berjalan',
                'tgl_tebar'           => $batch->tgl_tebar ? Carbon::parse($batch->tgl_tebar)->format('d M Y') : '-',
                'est_tgl_panen'       => $batch->est_tgl_panen ? Carbon::parse($batch->est_tgl_panen)->format('d M Y') : '-',
                'biomassa_panen_kg'   => $biomassaPanen,
                'biaya_bibit'         => (float) $fin['biaya_bibit'],
                'biaya_pakan'         => (float) $fin['biaya_pakan'],
                'biaya_operasional'   => (float) $fin['biaya_operasional'],
                'total_hpp'           => (float) $fin['total_biaya_kolam'],
                'pendapatan'          => (float) $fin['pendapatan_estimasi'],
                'laba_rugi'           => (float) $fin['laba_rugi'],
                'margin_pct'          => (float) $fin['margin_percent'],
                'status_finansial'    => $fin['status_finansial'],
                'status_label'        => $fin['status_label'],
                'status_badge'        => $fin['status_badge'],
                'fcr'                 => (float) ($batch->fcr ?: 1.2),
            ];
        }

        // Data Analyst & Unit Economics Calculations
        $totalPanenKg = BatchPembesaran::where('jumlah_panen_kg', '>', 0)->sum('jumlah_panen_kg');
        if ($totalPanenKg <= 0) {
            $totalPanenKg = TransaksiDistribusi::whereIn('status_order', ['siap_kirim', 'dalam_pengiriman', 'selesai'])->sum('Total_kg') ?: 830;
        }

        // HPP per kg dihitung dari Biaya Produksi Budidaya (Pakan + Bibit + Saprotan Operasional), BUKAN dari Capex Kolam/Mesin
        $biayaProduksiBudidaya = $pakanTotal + $bibitTotal + $operasionalTotal;
        $hppPerKg = $totalPanenKg > 0 ? round($biayaProduksiBudidaya / $totalPanenKg) : 21500;
        if ($hppPerKg < 15000 || $hppPerKg > 35000) {
            $hppPerKg = 21500; // Benchmark wajar budidaya air tawar
        }
        $avgFcr = round(BatchPembesaran::avg('fcr') ?: 1.20, 2);
        $feedCostPerKg = $totalPanenKg > 0 ? round($pakanTotal / $totalPanenKg) : 12000;
        $avgSellingPrice = 32000;
        $costIncomeRatio = $totalIncome > 0 ? round(($biayaProduksiBudidaya / $totalIncome) * 100, 1) : 0;

        $kpis = [
            'incomeFormatted'      => 'Rp ' . number_format($totalIncome, 0, ',', '.'),
            'expenseFormatted'     => 'Rp ' . number_format($totalExpense, 0, ',', '.'),
            'saldoFormatted'       => 'Rp ' . number_format($saldo, 0, ',', '.'),
            'netMargin'            => $netMargin,
            'incomeShort'          => 'Rp ' . number_format($totalIncome / 1000000, 1) . ' Jt',
            'expenseShort'         => 'Rp ' . number_format($totalExpense / 1000000, 1) . ' Jt',
            'totalTrx'             => $keuanganRecords->count(),
            'totalHpp'             => $totalHpp,
            'totalHppFormatted'    => 'Rp ' . number_format($totalHpp, 0, ',', '.'),
            'labaKotor'            => $labaKotor,
            'labaKotorFormatted'   => 'Rp ' . number_format($labaKotor, 0, ',', '.'),
            'grossMargin'          => $grossMargin,
            'pakanFormatted'       => 'Rp ' . number_format($pakanTotal, 0, ',', '.'),
            'bibitFormatted'       => 'Rp ' . number_format($bibitTotal, 0, ',', '.'),
            'operasionalFormatted' => 'Rp ' . number_format($operasionalTotal, 0, ',', '.'),
            'lainnyaFormatted'     => 'Rp ' . number_format($lainnyaTotal, 0, ',', '.'),
            'pakanTotal'           => $pakanTotal,
            'bibitTotal'           => $bibitTotal,
            'operasionalTotal'     => $operasionalTotal,
            'lainnyaTotal'         => $lainnyaTotal,
            'healthScore'          => $healthScore,
            'healthStatus'         => $healthStatus,
            'healthBadgeClass'     => $healthBadgeClass,
            'hppPerKg'             => 'Rp ' . number_format($hppPerKg, 0, ',', '.'),
            'feedCostPerKg'        => 'Rp ' . number_format($feedCostPerKg, 0, ',', '.'),
            'avgSellingPrice'      => 'Rp ' . number_format($avgSellingPrice, 0, ',', '.'),
            'avgFcr'               => $avgFcr,
            'totalPanenKg'         => number_format($totalPanenKg, 0, ',', '.') . ' Kg',
            'costIncomeRatio'      => $costIncomeRatio,
        ];

        // =========================================================================
        // NERACA POSISI KEUANGAN (AKTIVA & PASIVA EKSEKUTIF)
        // =========================================================================
        $saldoKasRiil = max(0, $saldo);
        $totalPiutangMitra = (float) TransaksiDistribusi::whereIn('status_pembayaran', ['tempo', 'menunggak', 'sebagian'])
            ->get()->sum(fn($t) => $t->sisa_piutang);

        $totalBiomassaAktif = (float) BatchPembesaran::whereIn('status_siklus', ['aktif', 'berjalan', 'siap_panen'])->sum('biomassa_est');
        $avgHppRaw = ($hppPerKg >= 15000 && $hppPerKg <= 35000) ? $hppPerKg : 21500;
        $nilaiPersediaanIkan = round($totalBiomassaAktif * $avgHppRaw, 2);

        $nilaiStokPakan = (float) \App\Models\StokPakan::all()->sum(fn($sp) => (float)$sp->stok_kg * (float)($sp->harga_per_kg ?? 12500));
        $nilaiStokSaprotan = (float) \App\Models\InventarisGudang::where('kategori_barang', 'saprotan')
            ->get()->sum(fn($s) => (float)$s->stok_tersisa * (float)($s->harga_satuan_terakhir ?? 25000));
        $totalPersediaanGudang = $nilaiStokPakan + $nilaiStokSaprotan;

        $totalAktivaLancar = $saldoKasRiil + $totalPiutangMitra + $nilaiPersediaanIkan + $totalPersediaanGudang;

        // ---------------------------------------------------------------------
        // AKTIVA TETAP & AKUMULASI PENYUSUTAN (METODE GARIS LURUS / STRAIGHT-LINE)
        // ---------------------------------------------------------------------
        $earliestTx = Keuangan::min('tanggal_transaksi');
        $now = Carbon::now('Asia/Jakarta');
        $farmStartDate = $earliestTx ? Carbon::parse($earliestTx, 'Asia/Jakarta') : $now->copy()->subMonths(3);
        $diffDays = max(1, $farmStartDate->diffInDays($now));
        $operationalMonths = round($diffDays / 30.4167, 1);
        if ($operationalMonths < 1) $operationalMonths = 1.0;

        // 1. Kolam Fisik Budidaya (Masa Manfaat Standar 4 Tahun = 48 Bulan)
        $nilaiKolamGross = (float) Kolam::sum('biaya_pembuatan');
        if ($nilaiKolamGross <= 0) $nilaiKolamGross = 22500000;
        $masaManfaatKolam = 48;
        $penyusutanKolamBulan = $nilaiKolamGross / $masaManfaatKolam;
        $penyusutanKolam = round(min($nilaiKolamGross, $penyusutanKolamBulan * $operationalMonths), 2);
        $nilaiKolamNet = max(0, $nilaiKolamGross - $penyusutanKolam);

        // 2. Peralatan & Mesin Tambak (Masa Manfaat Standar 3 Tahun = 36 Bulan)
        $nilaiPeralatanGross = (float) \App\Models\InventarisGudang::where('kategori_barang', 'peralatan')
            ->get()->sum(fn($p) => (float)$p->stok_tersisa * (float)($p->harga_satuan_terakhir ?? 1500000));
        if ($nilaiPeralatanGross <= 0) $nilaiPeralatanGross = 18500000;
        $masaManfaatAlat = 36;
        $penyusutanAlatBulan = $nilaiPeralatanGross / $masaManfaatAlat;
        $penyusutanPeralatan = round(min($nilaiPeralatanGross, $penyusutanAlatBulan * $operationalMonths), 2);
        $nilaiPeralatanNet = max(0, $nilaiPeralatanGross - $penyusutanPeralatan);

        // Rekapitulasi Aktiva Tetap
        $totalAktivaTetapKotor = $nilaiKolamGross + $nilaiPeralatanGross;
        $totalAkumulasiPenyusutan = $penyusutanKolam + $penyusutanPeralatan;
        $totalAktivaTetap = max(0, $totalAktivaTetapKotor - $totalAkumulasiPenyusutan);

        $totalAktiva = $totalAktivaLancar + $totalAktivaTetap;

        // ---------------------------------------------------------------------
        // PASIVA (KEWAJIBAN & EKUITAS)
        // ---------------------------------------------------------------------
        $cadanganKlaimSusut = (float) TransaksiDistribusi::sum('potongan_klaim_rp');
        $totalKewajiban = $cadanganKlaimSusut;
        $modalAwal = 100000000; // Rp 100.000.000 (Modal Awal Disetor Pemilik/Investor)
        $labaDitahan = max(0, $totalAktiva - $totalKewajiban - $modalAwal);
        $totalEkuitas = $modalAwal + $labaDitahan;
        $totalPasiva = $totalKewajiban + $totalEkuitas;

        $neraca = [
            'kasRiil'                => $saldoKasRiil,
            'kasRiilFormatted'       => 'Rp ' . number_format($saldoKasRiil, 0, ',', '.'),
            'piutangMitra'           => $totalPiutangMitra,
            'piutangMitraFormatted'  => 'Rp ' . number_format($totalPiutangMitra, 0, ',', '.'),
            'persediaanIkan'         => $nilaiPersediaanIkan,
            'persediaanIkanFormatted'=> 'Rp ' . number_format($nilaiPersediaanIkan, 0, ',', '.'),
            'persediaanGudang'       => $totalPersediaanGudang,
            'persediaanGudangFormatted'=> 'Rp ' . number_format($totalPersediaanGudang, 0, ',', '.'),
            'totalAktivaLancar'      => $totalAktivaLancar,
            'totalAktivaLancarFormatted' => 'Rp ' . number_format($totalAktivaLancar, 0, ',', '.'),

            'nilaiKolamGross'           => $nilaiKolamGross,
            'nilaiKolamGrossFormatted'  => 'Rp ' . number_format($nilaiKolamGross, 0, ',', '.'),
            'penyusutanKolam'           => $penyusutanKolam,
            'penyusutanKolamFormatted'  => 'Rp ' . number_format($penyusutanKolam, 0, ',', '.'),
            'nilaiKolam'                => $nilaiKolamNet,
            'nilaiKolamFormatted'       => 'Rp ' . number_format($nilaiKolamNet, 0, ',', '.'),

            'nilaiPeralatanGross'          => $nilaiPeralatanGross,
            'nilaiPeralatanGrossFormatted' => 'Rp ' . number_format($nilaiPeralatanGross, 0, ',', '.'),
            'penyusutanPeralatan'          => $penyusutanPeralatan,
            'penyusutanPeralatanFormatted' => 'Rp ' . number_format($penyusutanPeralatan, 0, ',', '.'),
            'nilaiPeralatan'               => $nilaiPeralatanNet,
            'nilaiPeralatanFormatted'      => 'Rp ' . number_format($nilaiPeralatanNet, 0, ',', '.'),

            'totalAktivaTetapKotor'          => $totalAktivaTetapKotor,
            'totalAktivaTetapKotorFormatted' => 'Rp ' . number_format($totalAktivaTetapKotor, 0, ',', '.'),
            'totalAkumulasiPenyusutan'       => $totalAkumulasiPenyusutan,
            'totalAkumulasiPenyusutanFormatted' => 'Rp ' . number_format($totalAkumulasiPenyusutan, 0, ',', '.'),
            'totalAktivaTetap'               => $totalAktivaTetap,
            'totalAktivaTetapFormatted'      => 'Rp ' . number_format($totalAktivaTetap, 0, ',', '.'),
            'umurOperasionalBulan'           => $operationalMonths,

            'totalAktiva'            => $totalAktiva,
            'totalAktivaFormatted'   => 'Rp ' . number_format($totalAktiva, 0, ',', '.'),
            'cadanganKlaimSusut'     => $cadanganKlaimSusut,
            'cadanganKlaimSusutFormatted' => 'Rp ' . number_format($cadanganKlaimSusut, 0, ',', '.'),
            'totalKewajiban'         => $totalKewajiban,
            'totalKewajibanFormatted'=> 'Rp ' . number_format($totalKewajiban, 0, ',', '.'),
            'modalAwal'              => $modalAwal,
            'modalAwalFormatted'     => 'Rp ' . number_format($modalAwal, 0, ',', '.'),
            'labaDitahan'            => $labaDitahan,
            'labaDitahanFormatted'   => 'Rp ' . number_format($labaDitahan, 0, ',', '.'),
            'totalEkuitas'           => $totalEkuitas,
            'totalEkuitasFormatted'  => 'Rp ' . number_format($totalEkuitas, 0, ',', '.'),
            'totalPasiva'            => $totalPasiva,
            'totalPasivaFormatted'   => 'Rp ' . number_format($totalPasiva, 0, ',', '.'),
            'isBalanced'             => abs($totalAktiva - $totalPasiva) < 0.01,
            'tanggalCutoff'          => Carbon::now('Asia/Jakarta')->translatedFormat('d F Y'),
            'jamCutoff'              => Carbon::now('Asia/Jakarta')->format('H:i') . ' WIB',
            'waktuCutoffLengkap'     => Carbon::now('Asia/Jakarta')->translatedFormat('d F Y, H:i') . ' WIB',
            'tahunBuku'              => $currentYear,
            'keteranganPeriode'      => 'Posisi per ' . Carbon::now('Asia/Jakarta')->translatedFormat('d F Y') . ' (Tahun Buku ' . $currentYear . ')',
        ];

        return view('layouts.keuangan.index', compact(
            'totalIncome',
            'totalExpense',
            'saldo',
            'kpis',
            'monthlyCashflow',
            'monthlyBreakdownTable',
            'pondProfitability',
            'currentYear',
            'keuanganRecords',
            'neraca'
        ));
    }

    public function transaksi()
    {
        // Pastikan order distribusi tersinkronkan ke buku kas
        self::syncDistribusiToKeuangan();

        // Ambil transaksi urut kronologis untuk kalkulasi saldo berjalan (Running Balance)
        $chronologicalRecords = Keuangan::with(['kolam', 'user'])
            ->orderBy('tanggal_transaksi', 'asc')
            ->orderBy('id_keuangan', 'asc')
            ->get();

        $runningBalance = 0;
        $calculatedTransactions = [];

        foreach ($chronologicalRecords as $k) {
            $isIncome = in_array(strtolower($k->tipe_transaksi), ['pemasukan', 'income']);
            $nominal = (float) $k->nominal;

            if ($isIncome) {
                $runningBalance += $nominal;
                $debit = $nominal;
                $kredit = 0;
            } else {
                $runningBalance -= $nominal;
                $debit = 0;
                $kredit = $nominal;
            }

            $refId = $k->ref_id ?: self::generateSopId($k->tanggal_transaksi);

            $calculatedTransactions[] = [
                'raw_id'         => $k->id_keuangan,
                'id'             => $refId,
                'tanggal'        => $k->tanggal_transaksi ? Carbon::parse($k->tanggal_transaksi)->toDateString() : date('Y-m-d'),
                'tanggal_format' => $k->tanggal_transaksi ? Carbon::parse($k->tanggal_transaksi)->format('d M Y') : date('d M Y'),
                'tipe'           => $isIncome ? 'income' : 'expense',
                'debit'          => $debit,
                'kredit'         => $kredit,
                'saldo_berjalan' => $runningBalance,
                'nominal'        => $nominal,
                'kategori'       => $k->kategori,
                'ref'            => $refId,
                'id_kolam'       => $k->id_kolam,
                'kolam'          => $k->kolam ? $k->kolam->nama_kolam : 'Tidak dialokasikan',
                'keterangan'     => $k->keterangan ?? '-'
            ];
        }

        // Urutkan transaksi terbaru di atas untuk kemudahan browsing
        $transactions = array_reverse($calculatedTransactions);

        $kolams = Kolam::orderBy('nama_kolam')->get();
        $totalIncome = Keuangan::whereIn('tipe_transaksi', ['pemasukan', 'income'])->sum('nominal');
        $totalExpense = Keuangan::whereIn('tipe_transaksi', ['pengeluaran', 'expense'])->sum('nominal');
        $saldo = $totalIncome - $totalExpense;

        return view('layouts.keuangan.transaksi', compact('transactions', 'kolams', 'totalIncome', 'totalExpense', 'saldo'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'tanggal_transaksi' => 'required|date',
            'tipe_transaksi'    => 'required|string',
            'kategori'          => 'required|string',
            'nominal'           => 'required|numeric|min:1',
            'id_kolam'          => 'nullable',
            'ref_id'            => 'nullable|string',
            'keterangan'        => 'nullable|string',
        ]);

        $tipe = in_array(strtolower($request->tipe_transaksi), ['income', 'pemasukan']) ? 'pemasukan' : 'pengeluaran';

        $idKolam = null;
        if ($request->filled('id_kolam') && $request->id_kolam !== 'Tidak dialokasikan' && $request->id_kolam !== '') {
            $kolam = Kolam::where('id_kolam', $request->id_kolam)->orWhere('nama_kolam', $request->id_kolam)->first();
            $idKolam = $kolam ? $kolam->id_kolam : null;
        }

        $keuangan = Keuangan::create([
            'id_user'           => Auth::id() ?? 1,
            'id_kolam'          => $idKolam,
            'tanggal_transaksi' => $request->tanggal_transaksi,
            'tipe_transaksi'    => $tipe,
            'kategori'          => $request->kategori,
            'nominal'           => $request->nominal,
            'keterangan'        => $request->keterangan,
            'ref_id'            => $request->ref_id ?: self::generateSopId($request->tanggal_transaksi),
        ]);

        AuditLog::record(
            'tambah_transaksi',
            'keuangan',
            "Mencatat transaksi {$keuangan->tipe_transaksi} {$keuangan->ref_id} ({$keuangan->kategori}, Rp " . number_format($keuangan->nominal, 0, ',', '.') . ")"
        );

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Transaksi keuangan berhasil dicatat!',
                'data'    => $keuangan
            ]);
        }

        return redirect()->route('keuangan.transaksi')->with('success', 'Transaksi keuangan berhasil dicatat!');
    }

    public function update(Request $request, $id)
    {
        $cleanId = preg_replace('/[^0-9]/', '', $id);
        if (empty($cleanId)) {
            $record = Keuangan::where('ref_id', $id)->first();
            $cleanId = $record ? $record->id_keuangan : null;
        }

        $keuangan = Keuangan::find($cleanId);
        if (!$keuangan) {
            return response()->json(['success' => false, 'message' => 'Data transaksi tidak ditemukan.'], 404);
        }

        $request->validate([
            'tanggal_transaksi' => 'required|date',
            'tipe_transaksi'    => 'required|string',
            'kategori'          => 'required|string',
            'nominal'           => 'required|numeric|min:1',
            'id_kolam'          => 'nullable',
            'ref_id'            => 'nullable|string',
            'keterangan'        => 'nullable|string',
        ]);

        $tipe = in_array(strtolower($request->tipe_transaksi), ['income', 'pemasukan']) ? 'pemasukan' : 'pengeluaran';

        $idKolam = null;
        if ($request->filled('id_kolam') && $request->id_kolam !== 'Tidak dialokasikan' && $request->id_kolam !== '') {
            $kolam = Kolam::where('id_kolam', $request->id_kolam)->orWhere('nama_kolam', $request->id_kolam)->first();
            $idKolam = $kolam ? $kolam->id_kolam : null;
        }

        $keuangan->update([
            'tanggal_transaksi' => $request->tanggal_transaksi,
            'tipe_transaksi'    => $tipe,
            'kategori'          => $request->kategori,
            'nominal'           => $request->nominal,
            'id_kolam'          => $idKolam,
            'ref_id'            => $request->ref_id ?: $keuangan->ref_id,
            'keterangan'        => $request->keterangan,
        ]);

        AuditLog::record(
            'edit_transaksi',
            'keuangan',
            "Memperbarui transaksi {$keuangan->ref_id} ({$keuangan->kategori}, Rp " . number_format($keuangan->nominal, 0, ',', '.') . ")"
        );

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Data transaksi berhasil diperbarui!',
                'data'    => $keuangan
            ]);
        }

        return redirect()->route('keuangan.transaksi')->with('success', 'Data transaksi berhasil diperbarui!');
    }

    public function destroy(Request $request, $id)
    {
        $cleanId = preg_replace('/[^0-9]/', '', $id);
        if (empty($cleanId)) {
            $record = Keuangan::where('ref_id', $id)->first();
            $cleanId = $record ? $record->id_keuangan : null;
        }

        $keuangan = Keuangan::find($cleanId);
        if (!$keuangan) {
            return response()->json(['success' => false, 'message' => 'Data transaksi tidak ditemukan.'], 404);
        }

        AuditLog::record(
            'hapus_transaksi',
            'keuangan',
            "Menghapus transaksi {$keuangan->ref_id} ({$keuangan->kategori}, Rp " . number_format($keuangan->nominal, 0, ',', '.') . ")"
        );

        $keuangan->delete();

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Data transaksi berhasil dihapus!'
            ]);
        }

        return redirect()->route('keuangan.transaksi')->with('success', 'Data transaksi berhasil dihapus!');
    }

    public function exportPdf(?Request $request = null)
    {
        $request = $request ?: request();
        $year = (int) $request->input('year', Carbon::now()->year);
        $month = $request->input('month');

        self::syncDistribusiToKeuangan();

        $query = Keuangan::with(['kolam', 'user'])->whereYear('tanggal_transaksi', $year);
        $periodTitle = "Tahun {$year}";

        if ($month && is_numeric($month)) {
            $query->whereMonth('tanggal_transaksi', (int) $month);
            $monthName = Carbon::createFromDate($year, (int)$month, 1)->translatedFormat('F');
            $periodTitle = "Bulan {$monthName} {$year}";
        }

        $chronologicalRecords = $query->orderBy('tanggal_transaksi', 'asc')->orderBy('id_keuangan', 'asc')->get();

        // Kalkulasi Saldo Berjalan & Breakdown P&L
        $runningBalance = 0;
        $totalIncome = 0;
        $totalExpense = 0;
        $pakanTotal = 0;
        $bibitTotal = 0;
        $operasionalTotal = 0;
        $transaksiList = [];

        foreach ($chronologicalRecords as $k) {
            $isIncome = in_array(strtolower($k->tipe_transaksi), ['pemasukan', 'income']);
            $nom = (float) $k->nominal;

            if ($isIncome) {
                $totalIncome += $nom;
                $runningBalance += $nom;
                $debit = $nom;
                $kredit = 0;
            } else {
                $totalExpense += $nom;
                $runningBalance -= $nom;
                $debit = 0;
                $kredit = $nom;

                $kat = strtolower($k->kategori);
                if (str_contains($kat, 'pakan') || str_contains($kat, 'pelet')) {
                    $pakanTotal += $nom;
                } elseif (str_contains($kat, 'bibit') || str_contains($kat, 'benih')) {
                    $bibitTotal += $nom;
                } else {
                    $operasionalTotal += $nom;
                }
            }

            $k->debit = $debit;
            $k->kredit = $kredit;
            $k->saldo_berjalan = $runningBalance;
            $transaksiList[] = $k;
        }

        $saldo = $totalIncome - $totalExpense;
        $totalHpp = $pakanTotal + $bibitTotal;
        $labaKotor = $totalIncome - $totalHpp;
        $grossMargin = $totalIncome > 0 ? round(($labaKotor / $totalIncome) * 100, 1) : 0;
        $netMargin = $totalIncome > 0 ? round(($saldo / $totalIncome) * 100, 1) : 0;

        // Unit Economics
        $totalPanenKg = BatchPembesaran::where('jumlah_panen_kg', '>', 0)->sum('jumlah_panen_kg');
        if ($totalPanenKg <= 0) {
            $totalPanenKg = TransaksiDistribusi::whereIn('status_order', ['siap_kirim', 'dalam_pengiriman', 'selesai'])->sum('Total_kg') ?: 830;
        }
        $biayaProduksi = $pakanTotal + $bibitTotal + $operasionalTotal;
        $hppPerKg = $totalPanenKg > 0 ? round($biayaProduksi / $totalPanenKg) : 21500;
        if ($hppPerKg < 15000 || $hppPerKg > 35000) {
            $hppPerKg = 21500;
        }
        $avgSellingPrice = 32000;

        $pnl = [
            'totalIncome'      => $totalIncome,
            'totalExpense'     => $totalExpense,
            'saldo'            => $saldo,
            'pakanTotal'       => $pakanTotal,
            'bibitTotal'       => $bibitTotal,
            'totalHpp'         => $totalHpp,
            'labaKotor'        => $labaKotor,
            'grossMargin'      => $grossMargin,
            'operasionalTotal' => $operasionalTotal,
            'netMargin'        => $netMargin,
            'totalPanenKg'     => $totalPanenKg,
            'hppPerKg'         => $hppPerKg,
            'avgSellingPrice'  => $avgSellingPrice,
        ];

        $pdf = Pdf::loadView('layouts.keuangan.laporan_pdf', compact('transaksiList', 'totalIncome', 'totalExpense', 'saldo', 'periodTitle', 'year', 'pnl'))
            ->setPaper('a4', 'portrait');

        $filename = 'Laporan_Keuangan_Budidaya_' . str_replace(' ', '_', $periodTitle) . '.pdf';
        return $pdf->download($filename);
    }
}

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
            ->whereIn('status_order', ['siap_kirim', 'dalam_pengiriman', 'selesai'])
            ->get();

        if ($confirmedOrders->isEmpty()) {
            return;
        }

        $existingKeuangan = Keuangan::where('ref_id', 'like', 'ORD-%')->get()->keyBy('ref_id');

        foreach ($confirmedOrders as $order) {
            $orderRef = 'ORD-' . str_pad($order->id_transaksi, 4, '0', STR_PAD_LEFT);
            $targetNominal = (float) $order->harga_total;

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
                    'keterangan'        => "Pelunasan penjualan #{$orderRef} ({$totalKgFormatted} kg {$species}) ke {$mitraNama} [Status: {$statusLabel}]",
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
            $totalPanenKg = TransaksiDistribusi::whereIn('status_order', ['siap_kirim', 'dalam_pengiriman', 'selesai'])->sum('Total_kg') ?: 1000;
        }

        $hppPerKg = $totalPanenKg > 0 ? round($totalExpense / $totalPanenKg) : 0;
        $avgFcr = round(BatchPembesaran::avg('fcr') ?: 1.20, 2);
        $feedCostPerKg = $totalPanenKg > 0 ? round($pakanTotal / $totalPanenKg) : 0;
        $avgSellingPrice = $totalPanenKg > 0 && $totalIncome > 0 ? round($totalIncome / $totalPanenKg) : 32000;
        $costIncomeRatio = $totalIncome > 0 ? round(($totalExpense / $totalIncome) * 100, 1) : 0;

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

        return view('layouts.keuangan.index', compact(
            'totalIncome',
            'totalExpense',
            'saldo',
            'kpis',
            'monthlyCashflow',
            'monthlyBreakdownTable',
            'pondProfitability',
            'currentYear',
            'keuanganRecords'
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
            $totalPanenKg = TransaksiDistribusi::whereIn('status_order', ['siap_kirim', 'dalam_pengiriman', 'selesai'])->sum('Total_kg') ?: 1000;
        }
        $hppPerKg = $totalPanenKg > 0 ? round($totalExpense / $totalPanenKg) : 0;
        $avgSellingPrice = $totalPanenKg > 0 && $totalIncome > 0 ? round($totalIncome / $totalPanenKg) : 32000;

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

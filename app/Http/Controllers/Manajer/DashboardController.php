<?php

namespace App\Http\Controllers\Manajer;

use App\Http\Controllers\Controller;
use App\Models\BatchPembesaran;
use App\Models\BatchPembibitan;
use App\Models\Ikan;
use App\Models\Keuangan;
use App\Models\Kolam;
use App\Models\ManajemenPakan;
use App\Models\MitraDistributor;
use App\Models\TransaksiDistribusi;
use Illuminate\Http\Request;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        // 1. KPI Calculations from database
        $activeBatches = BatchPembesaran::where('status_siklus', '!=', 'gagal')->get();
        $totalStok = $activeBatches->where('status_siklus', '!=', 'selesai')->sum('biomassa_est');
        if ($totalStok == 0) {
            $totalStok = $activeBatches->sum('biomassa_est');
        }

        $avgFcrVal = BatchPembesaran::whereNotNull('fcr')->where('fcr', '>', 0)->avg('fcr');
        if (!$avgFcrVal || $avgFcrVal <= 0) {
            $totalPakanSemua = ManajemenPakan::sum('kg_pelet') + ManajemenPakan::sum('kg_daun');
            $avgFcrVal = $totalStok > 0 ? round($totalPakanSemua / $totalStok, 2) : 0;
        }
        $avgFcr = round((float)$avgFcrVal, 2);

        $targetPanen = BatchPembesaran::where('status_siklus', '!=', 'selesai')->sum('target_panen_kg');
        if ($targetPanen == 0) {
            $targetPanen = BatchPembesaran::sum('target_panen_kg');
        }

        // Total pakan dari database
        $totalPakan = ManajemenPakan::sum('kg_pelet') + ManajemenPakan::sum('kg_daun');
        $avgPhVal = ManajemenPakan::whereNotNull('ph_air')->where('ph_air', '>', 0)->avg('ph_air');
        $avgPh = $avgPhVal ? round((float)$avgPhVal, 1) : 0.0;

        // Active batch count & trend label
        $activeBatchCount = $activeBatches->where('status_siklus', 'berjalan')->count();
        $totalStokTrend = $activeBatchCount > 0 ? ($activeBatchCount . ' Batch Aktif Terpantau') : 'Stok Kolam Terdata';

        // Get latest active distribution order for target note
        $latestOrder = TransaksiDistribusi::with('mitra')->where('status_order', '!=', 'selesai')->latest('tanggal_order')->first();
        if (!$latestOrder) {
            $latestOrder = TransaksiDistribusi::with('mitra')->latest('tanggal_order')->first();
        }
        $targetNote = $latestOrder && $latestOrder->mitra ? $latestOrder->mitra->nama_mitra : 'Mitra Distribusi';
        $targetTag = $latestOrder ? strtoupper(str_replace('_', ' ', $latestOrder->status_order ?? 'ORDER AKTIF')) : 'ORDER AKTIF';

        $metrics = [
            'totalStok'       => number_format($totalStok, 0, ',', '.'),
            'totalStokTrend'  => $totalStokTrend,
            'fcr'             => $avgFcr > 0 ? number_format($avgFcr, 2, '.', '') : '0.00',
            'fcrStatus'       => $avgFcr > 0 ? ($avgFcr <= 1.25 ? 'Efisiensi Pakan Optimal' : 'Perlu Evaluasi Pakan') : 'Belum Ada Data Pakan',
            'targetPanen'     => number_format($targetPanen, 0, ',', '.'),
            'targetPanenNote' => $targetNote,
            'targetPanenTag'  => $targetTag,
            'totalPakan'      => number_format($totalPakan, 0, ',', '.'),
            'avgPh'           => $avgPh > 0 ? number_format($avgPh, 1, '.', '') : '0.0'
        ];

        // 2. Daftar Titik Koordinat Mitra Distributor untuk Leaflet Map
        $mitras = MitraDistributor::withCount('transaksiDistribusi')->get();
        $mitraList = [];

        foreach ($mitras as $m) {
            $mitraList[] = [
                'id'        => $m->id_mitra,
                'nama'      => $m->nama_mitra,
                'tipe'      => $m->tipe_mitra,
                'alamat'    => $m->alamat,
                'lat'       => (float) ($m->latitude ?? -8.5833),
                'lng'       => (float) ($m->longitude ?? 116.1166),
                'total_trx' => $m->transaksi_distribusi_count ?? 0,
            ];
        }

        // 3. Rekap Konsumsi Pakan Terbaru
        $pakanLogs = ManajemenPakan::latest('tgl_log')->take(7)->get();
        $pakanRekap = [];
        foreach ($pakanLogs as $log) {
            $pakanRekap[] = [
                'hari'  => Carbon::parse($log->tgl_log)->translatedFormat('l'),
                'pelet' => (float) $log->kg_pelet,
                'daun'  => (float) $log->kg_daun
            ];
        }

        // 4. Data Dynamic Chart Pakan (7d, 14d, 30d, month) Berdasarkan Data Nyata ManajemenPakan
        $startDate30 = Carbon::now()->subDays(29)->startOfDay();
        $pakanGrouped = ManajemenPakan::where('tgl_log', '>=', $startDate30)
            ->selectRaw('DATE(tgl_log) as tanggal, SUM(kg_pelet) as total_pelet, SUM(kg_daun) as total_daun')
            ->groupBy('tanggal')
            ->orderBy('tanggal', 'asc')
            ->get()
            ->keyBy('tanggal');

        // 7 Hari Terakhir
        $chart7d = ['labels' => [], 'pelet' => [], 'daun' => []];
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $dateStr = $date->toDateString();
            $rec = $pakanGrouped->get($dateStr);
            $chart7d['labels'][] = $date->translatedFormat('D') . ' (' . $date->format('d/m') . ')';
            $chart7d['pelet'][]  = $rec ? round((float) $rec->total_pelet, 1) : 0;
            $chart7d['daun'][]   = $rec ? round((float) $rec->total_daun, 1) : 0;
        }

        // 14 Hari Terakhir
        $chart14d = ['labels' => [], 'pelet' => [], 'daun' => []];
        for ($i = 13; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $dateStr = $date->toDateString();
            $rec = $pakanGrouped->get($dateStr);
            $chart14d['labels'][] = $date->format('d/m');
            $chart14d['pelet'][]  = $rec ? round((float) $rec->total_pelet, 1) : 0;
            $chart14d['daun'][]   = $rec ? round((float) $rec->total_daun, 1) : 0;
        }

        // 30 Hari Terakhir
        $chart30d = ['labels' => [], 'pelet' => [], 'daun' => []];
        for ($i = 29; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $dateStr = $date->toDateString();
            $rec = $pakanGrouped->get($dateStr);
            $chart30d['labels'][] = $date->format('d/m');
            $chart30d['pelet'][]  = $rec ? round((float) $rec->total_pelet, 1) : 0;
            $chart30d['daun'][]   = $rec ? round((float) $rec->total_daun, 1) : 0;
        }

        // Bulan Ini
        $chartMonth = ['labels' => [], 'pelet' => [], 'daun' => []];
        $daysInMonth = Carbon::now()->day;
        for ($d = 1; $d <= $daysInMonth; $d++) {
            $date = Carbon::now()->startOfMonth()->addDays($d - 1);
            $dateStr = $date->toDateString();
            $rec = $pakanGrouped->get($dateStr);
            $chartMonth['labels'][] = $date->format('d/m');
            $chartMonth['pelet'][]  = $rec ? round((float) $rec->total_pelet, 1) : 0;
            $chartMonth['daun'][]   = $rec ? round((float) $rec->total_daun, 1) : 0;
        }

        $chartDatasets = [
            '7d'    => $chart7d,
            '14d'   => $chart14d,
            '30d'   => $chart30d,
            'month' => $chartMonth,
        ];

        return view('layouts.dashboard.index', compact('metrics', 'mitraList', 'pakanRekap', 'chartDatasets'));
    }

    public function exportExcel(Request $request)
    {
        $period = $request->query('period', 'all');
        $customLabel = $request->query('label');

        $startDate = null;
        $endDate = null;
        $periodeTitle = 'Semua Periode';

        if (str_starts_with($period, 'w_')) {
            // Format: w_YYYY_M_INDEX
            $parts = explode('_', $period);
            if (count($parts) >= 4) {
                $year = (int)$parts[1];
                $month = (int)$parts[2];
                $weekIndex = (int)$parts[3];

                $startDay = ($weekIndex - 1) * 7 + 1;
                $lastDayInMonth = Carbon::createFromDate($year, $month, 1)->endOfMonth()->day;
                $endDay = min($weekIndex * 7, $lastDayInMonth);
                if ($weekIndex >= 5) {
                    $endDay = $lastDayInMonth;
                }

                $startDate = Carbon::createFromDate($year, $month, $startDay)->startOfDay();
                $endDate = Carbon::createFromDate($year, $month, $endDay)->endOfDay();
                $monthName = Carbon::createFromDate($year, $month, 1)->translatedFormat('F');
                $periodeTitle = "Minggu ke-{$weekIndex} ({$startDay} - {$endDay} {$monthName} {$year})";
            }
        } elseif (str_starts_with($period, 'm_')) {
            // Format: m_YYYY_M
            $parts = explode('_', $period);
            if (count($parts) >= 3) {
                $year = (int)$parts[1];
                $month = (int)$parts[2];
                $startDate = Carbon::createFromDate($year, $month, 1)->startOfDay();
                $endDate = Carbon::createFromDate($year, $month, 1)->endOfMonth()->endOfDay();
                $monthName = $startDate->translatedFormat('F');
                $periodeTitle = "Bulan {$monthName} {$year}";
            }
        } elseif (str_starts_with($period, 'y_')) {
            // Format: y_YYYY
            $parts = explode('_', $period);
            if (count($parts) >= 2) {
                $year = (int)$parts[1];
                $startDate = Carbon::createFromDate($year, 1, 1)->startOfDay();
                $endDate = Carbon::createFromDate($year, 12, 31)->endOfDay();
                $periodeTitle = "Tahun Anggaran {$year}";
            }
        }

        if ($customLabel && $periodeTitle === 'Semua Periode') {
            $periodeTitle = $customLabel;
        }

        // Queries with eager loading & date filters
        $pembesaranQuery = BatchPembesaran::with(['kolam', 'user']);
        $pembibitanQuery = BatchPembibitan::with(['kolam', 'ikan', 'user']);
        $pakanQuery = ManajemenPakan::with(['kolam', 'user']);
        $keuanganQuery = Keuangan::with(['kolam', 'user']);
        $distribusiQuery = TransaksiDistribusi::with(['mitra', 'batchPembesaran.kolam', 'user']);

        if ($startDate && $endDate) {
            $pembesaranQuery->whereBetween('tgl_tebar', [$startDate->toDateString(), $endDate->toDateString()]);
            $pembibitanQuery->whereBetween('tgl_pemijahan', [$startDate->toDateString(), $endDate->toDateString()]);
            $pakanQuery->whereBetween('tgl_log', [$startDate->toDateString(), $endDate->toDateString()]);
            $keuanganQuery->whereBetween('tanggal_transaksi', [$startDate->toDateString(), $endDate->toDateString()]);
            $distribusiQuery->whereBetween('tanggal_order', [$startDate->toDateString(), $endDate->toDateString()]);
        }

        $pembesaranList = $pembesaranQuery->latest('tgl_tebar')->get();
        $pembibitanList = $pembibitanQuery->latest('tgl_pemijahan')->get();
        $pakanList = $pakanQuery->latest('tgl_log')->get();
        $keuanganList = $keuanganQuery->latest('tanggal_transaksi')->get();
        $distribusiList = $distribusiQuery->latest('tanggal_order')->get();
        $mitraList = MitraDistributor::withCount('transaksiDistribusi')->get();

        // Calculations
        $totalBiomassa = $pembesaranList->sum('biomassa_est');
        $totalTargetPanen = $pembesaranList->sum('target_panen_kg');
        $avgFcr = $pembesaranList->where('fcr', '>', 0)->avg('fcr') ?: 0;
        $totalPelet = $pakanList->sum('kg_pelet');
        $totalDaun = $pakanList->sum('kg_daun');
        $totalBiayaPakan = $pakanList->sum('total_biaya');
        $totalIncome = $keuanganList->whereIn('tipe_transaksi', ['pemasukan', 'income'])->sum('nominal');
        $totalExpense = $keuanganList->whereIn('tipe_transaksi', ['pengeluaran', 'expense'])->sum('nominal');
        $saldoKas = $totalIncome - $totalExpense;
        $totalKgDistribusi = $distribusiList->sum('Total_kg');
        $totalNilaiDistribusi = $distribusiList->sum('harga_total');
        $totalBibitHatchery = $pembibitanList->sum('jumlah_bibitAwal');

        $printDate = Carbon::now()->translatedFormat('l, d F Y H:i:s');
        $filename = 'Laporan_SIM_Budidaya_' . ($startDate ? $startDate->format('Ymd') : 'ALL') . '.xls';

        return response()->view('layouts.dashboard.excel_report', compact(
            'periodeTitle',
            'printDate',
            'totalBiomassa',
            'totalTargetPanen',
            'avgFcr',
            'totalPelet',
            'totalDaun',
            'totalBiayaPakan',
            'totalIncome',
            'totalExpense',
            'saldoKas',
            'totalKgDistribusi',
            'totalNilaiDistribusi',
            'totalBibitHatchery',
            'pembesaranList',
            'pembibitanList',
            'pakanList',
            'keuanganList',
            'distribusiList',
            'mitraList'
        ))->withHeaders([
            'Content-Type' => 'application/vnd.ms-excel; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            'Pragma' => 'no-cache',
            'Expires' => '0'
        ]);
    }
}

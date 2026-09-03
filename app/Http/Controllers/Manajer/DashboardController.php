<?php

namespace App\Http\Controllers\Manajer;

use App\Http\Controllers\Controller;
use App\Models\BatchPembesaran;
use App\Models\BatchPembibitan;
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
        $totalStok = BatchPembesaran::sum('biomassa_est');
        $avgFcr = BatchPembesaran::whereNotNull('fcr')->where('fcr', '>', 0)->avg('fcr') ?? 1.12;
        $targetPanen = BatchPembesaran::sum('target_panen_kg');

        // Total pakan dari database
        $totalPakan = ManajemenPakan::sum('kg_pelet') + ManajemenPakan::sum('kg_daun');
        $avgPh = Kolam::avg('kesehatan_ph_air') ?? 7.3;

        // Get latest active distribution order for target note
        $latestOrder = TransaksiDistribusi::with('mitra')->latest('tanggal_order')->first();
        $targetNote = $latestOrder && $latestOrder->mitra ? $latestOrder->mitra->nama_mitra : 'Mitra Distribusi';

        $metrics = [
            'totalStok'       => number_format($totalStok, 0, ',', '.'),
            'totalStokTrend'  => '+4.2% dari minggu lalu',
            'fcr'             => number_format($avgFcr, 2, '.', ''),
            'fcrStatus'       => $avgFcr <= 1.2 ? 'Efisiensi Pakan Optimal' : 'Perlu Evaluasi Pakan',
            'targetPanen'     => number_format($targetPanen, 0, ',', '.'),
            'targetPanenNote' => $targetNote,
            'targetPanenTag'  => 'ORDER AKTIF',
            'totalPakan'      => number_format($totalPakan, 0, ',', '.'),
            'avgPh'           => number_format($avgPh, 1, '.', '')
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
}

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

class DashboardController extends Controller
{
    public function index()
    {
        // 1. KPI Calculations from database
        $totalStok = BatchPembesaran::sum('biomassa_est');
        $avgFcr = BatchPembesaran::whereNotNull('fcr')->where('fcr', '>', 0)->avg('fcr') ?? 1.12;
        $targetPanen = BatchPembesaran::sum('target_panen_kg');

        // Total pakan dari seeder
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

        // 3. Rekap Konsumsi Pakan 7 Hari
        $pakanLogs = ManajemenPakan::latest('tgl_log')->take(7)->get();
        $pakanRekap = [];
        foreach ($pakanLogs as $log) {
            $pakanRekap[] = [
                'hari'  => \Carbon\Carbon::parse($log->tgl_log)->translatedFormat('l'),
                'pelet' => (float) $log->kg_pelet,
                'daun'  => (float) $log->kg_daun
            ];
        }

        return view('layouts.dashboard.index', compact('metrics', 'mitraList', 'pakanRekap'));
    }
}

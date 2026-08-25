<?php

namespace App\Http\Controllers;

use App\Models\BatchPembesaran;
use App\Models\BatchPembibitan;
use App\Models\Kolam;
use App\Models\ManajemenPakan;
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

        // 2. Daftar Stok Siap Panen / Kolam Berjalan
        $batches = BatchPembesaran::with(['kolam', 'user'])->get();
        $stokList = [];

        foreach ($batches as $b) {
            $status = 'READY';
            if ($b->status_siklus === 'selesai') {
                $status = 'PANEN';
            } elseif ($b->status_siklus === 'persiapan') {
                $status = 'HOLD';
            }

            $stokList[] = [
                'id'        => $b->kolam ? $b->kolam->nama_kolam : 'Kolam #' . $b->id_kolam,
                'jenisIkan' => $b->jenis_ikan,
                'bobot'     => number_format($b->biomassa_est, 0, ',', '.') . ' kg',
                'bobotNum'  => (float) $b->biomassa_est,
                'tujuan'    => 'Mitra Distribusi',
                'status'    => $status,
                'ph'        => $b->kolam ? ($b->kolam->kesehatan_ph_air ?? '7.2') : '7.2',
                'suhu'      => '28.5°C',
                'populasi'  => number_format($b->biomassa_est * 2, 0, ',', '.') . ' Ekor',
                'tglTebar'  => $b->tgl_tebar ? \Carbon\Carbon::parse($b->tgl_tebar)->translatedFormat('d F Y') : '-',
                'fcr'       => number_format($b->fcr ?? 1.10, 2, '.', '')
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

        return view('layouts.dashboard.index', compact('metrics', 'stokList', 'pakanRekap'));
    }
}

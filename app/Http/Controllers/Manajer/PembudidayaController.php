<?php

namespace App\Http\Controllers\Manajer;

use App\Http\Controllers\Controller;
use App\Models\BatchPembesaran;
use App\Models\BatchPembibitan;
use App\Models\Kolam;
use Carbon\Carbon;
use Illuminate\Http\Request;

class PembudidayaController extends Controller
{
    public function index()
    {
        $kolamRecords = Kolam::with(['user', 'batchPembesaran', 'batchPembibitan'])->get();

        $kolams = [];
        $colorClasses = [
            'bg-sky-100 text-sky-700',
            'bg-amber-100 text-amber-700',
            'bg-purple-100 text-purple-700',
            'bg-emerald-100 text-emerald-700'
        ];

        foreach ($kolamRecords as $idx => $k) {
            $latestBatch = $k->batchPembesaran()->latest('id_pembesaran')->first();
            $pembudidaya = $k->user ? $k->user->nama : 'Petugas Budidaya';
            $words = explode(' ', $pembudidaya);
            $initials = strtoupper(substr($words[0] ?? 'P', 0, 1) . substr($words[1] ?? ($words[0] ?? 'B'), 0, 1));

            $status = 'Optimal';
            $statusClass = 'bg-emerald-100 text-emerald-700';
            $dotClass = 'bg-emerald-500';

            if ($latestBatch && $latestBatch->status_siklus === 'selesai') {
                $status = 'Siap Panen';
                $statusClass = 'bg-amber-100 text-amber-700';
                $dotClass = 'bg-amber-500';
            } elseif ($k->kesehatan_ph_air && ($k->kesehatan_ph_air < 6.8 || $k->kesehatan_ph_air > 8.0)) {
                $status = 'Perlu Perhatian';
                $statusClass = 'bg-rose-100 text-rose-700';
                $dotClass = 'bg-rose-500';
            }

            $kolams[] = [
                'id'           => $k->nama_kolam,
                'lokasi'       => $k->tipe_kolam,
                'pembudidaya'  => $pembudidaya,
                'initials'     => $initials,
                'colorClass'   => $colorClasses[$idx % count($colorClasses)],
                'jenisIkan'    => $latestBatch ? $latestBatch->jenis_ikan : 'Ikan Budidaya',
                'tebarBenih'   => $latestBatch && $latestBatch->tgl_tebar ? \Carbon\Carbon::parse($latestBatch->tgl_tebar)->translatedFormat('d M Y') : ($k->created_at ? \Carbon\Carbon::parse($k->created_at)->translatedFormat('d M Y') : '-'),
                'populasi'     => number_format($k->kapasitas, 0, ',', '.'),
                'populasiRaw'  => $k->kapasitas,
                'status'       => $status,
                'statusClass'  => $statusClass,
                'dotClass'     => $dotClass
            ];
        }

        // Statistik Ringkasan Nyata dari Database
        $totalKapasitas = Kolam::sum('kapasitas');
        $totalBenihAktif = \App\Models\BatchPembibitan::where('status', '!=', 'selesai')->where('status', '!=', 'gagal')->get()->sum(function($b) {
            return max(0, $b->jumlah_bibitAwal - $b->jumlah_kematian);
        });
        if ($totalBenihAktif <= 0) {
            $totalBenihAktif = $totalKapasitas;
        }

        $totalAwalBibit = \App\Models\BatchPembibitan::sum('jumlah_bibitAwal');
        $totalMatiBibit = \App\Models\BatchPembibitan::sum('jumlah_kematian');
        $keberhasilanRate = $totalAwalBibit > 0 ? round((($totalAwalBibit - $totalMatiBibit) / $totalAwalBibit) * 100, 1) : 98.8;

        $kolamSiapPanenCount = \App\Models\BatchPembesaran::whereIn('status_siklus', ['siap_panen', 'selesai'])->count();
        $totalKolamCount = Kolam::count();

        $stats = [
            'totalBenih'       => number_format($totalBenihAktif, 0, ',', '.'),
            'keberhasilanRate' => $keberhasilanRate,
            'siapPanenCount'   => $kolamSiapPanenCount,
            'totalKolamCount'  => $totalKolamCount,
        ];

        return view('layouts.pembudidaya.index', compact('kolams', 'stats'));
    }
}

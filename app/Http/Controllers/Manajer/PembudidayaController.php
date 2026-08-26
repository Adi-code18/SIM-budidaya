<?php

namespace App\Http\Controllers\Manajer;

use App\Http\Controllers\Controller;
use App\Models\Kolam;
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
                'tebarBenih'   => $latestBatch && $latestBatch->tgl_tebar ? \Carbon\Carbon::parse($latestBatch->tgl_tebar)->translatedFormat('d M Y') : '10 Mei 2026',
                'populasi'     => number_format($k->kapasitas, 0, ',', '.'),
                'populasiRaw'  => $k->kapasitas,
                'status'       => $status,
                'statusClass'  => $statusClass,
                'dotClass'     => $dotClass
            ];
        }

        return view('layouts.pembudidaya.index', compact('kolams'));
    }
}

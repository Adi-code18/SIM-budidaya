<?php

namespace App\Http\Controllers;

use App\Models\BatchPembibitan;
use App\Models\Kolam;
use Illuminate\Http\Request;

class PembibitanController extends Controller
{
    public function index()
    {
        $batchRecords = BatchPembibitan::with(['kolam', 'user'])->latest('id_batch')->get();
        $kolams = Kolam::all();

        // Calculate KPI boxes from seeders
        $totalAwal = BatchPembibitan::sum('jumlah_bibitAwal');
        $totalMati = BatchPembibitan::sum('jumlah_kematian');
        $totalBenihAktif = $totalAwal - $totalMati;

        $srRate = $totalAwal > 0 ? (100 - (($totalMati / $totalAwal) * 100)) : 98.8;

        $kolamBibitCount = Kolam::where('tipe_kolam', 'like', '%Pembibitan%')
            ->orWhere('tipe_kolam', 'like', '%Hatchery%')
            ->orWhere('tipe_kolam', 'like', '%Pemijahan%')
            ->count();
        if ($kolamBibitCount == 0) {
            $kolamBibitCount = BatchPembibitan::distinct('id_kolam')->count('id_kolam');
        }
        $totalKolamCount = Kolam::count();

        $avgPh = Kolam::avg('kesehatan_ph_air') ?? 7.2;

        $kpis = [
            'totalBenih'      => number_format($totalBenihAktif, 0, ',', '.'),
            'srRate'          => number_format($srRate, 1, '.', ''),
            'bakTerpakai'     => $kolamBibitCount > 0 ? $kolamBibitCount : 4,
            'totalBak'        => $totalKolamCount > 0 ? $totalKolamCount : 12,
            'bakTersedia'     => max(0, ($totalKolamCount > 0 ? $totalKolamCount : 12) - ($kolamBibitCount > 0 ? $kolamBibitCount : 4)),
            'avgPh'           => number_format($avgPh, 1, '.', ''),
            'suhu'            => '28°C'
        ];

        $batches = [];
        foreach ($batchRecords as $b) {
            $fase = 'LARVA';
            $faseClass = 'bg-sky-100 text-sky-700';
            $days = $b->tgl_pemijahan ? \Carbon\Carbon::parse($b->tgl_pemijahan)->diffInDays(now()) : 2;

            if ($days <= 3) {
                $fase = 'TELUR';
                $faseClass = 'bg-slate-100 text-slate-700';
            } elseif ($days > 14) {
                $fase = 'FINGERLING';
                $faseClass = 'bg-indigo-100 text-indigo-700';
            }

            $statusKesehatan = 'Sehat';
            $statusClass = 'bg-emerald-100 text-emerald-700';
            $dotClass = 'bg-emerald-500';

            if ($b->jumlah_kematian > 3000 || ($b->kolam && $b->kolam->kesehatan_ph_air < 6.5)) {
                $statusKesehatan = 'Perlu Atensi';
                $statusClass = 'bg-rose-100 text-rose-700';
                $dotClass = 'bg-rose-500';
            }

            $batches[] = [
                'id'              => '#BT-' . str_pad($b->id_batch, 5, '0', STR_PAD_LEFT),
                'inputDate'       => $b->tgl_pemijahan ? \Carbon\Carbon::parse($b->tgl_pemijahan)->translatedFormat('d M Y') : '-',
                'fase'            => $fase,
                'faseClass'       => $faseClass,
                'usia'            => $days . ' Hari',
                'jumlah'          => number_format($b->jumlah_bibitAwal - $b->jumlah_kematian, 0, ',', '.'),
                'jenisIkan'       => strtoupper(explode(' ', $b->jenis_ikan)[1] ?? $b->jenis_ikan),
                'statusKesehatan' => $statusKesehatan,
                'statusClass'     => $statusClass,
                'dotClass'        => $dotClass,
                'kolam'           => $b->kolam ? $b->kolam->nama_kolam : 'Kolam #' . $b->id_kolam,
                'phAir'           => $b->kolam ? ($b->kolam->kesehatan_ph_air ?? '7.2') : '7.2',
                'suhuAir'         => '28.0°C'
            ];
        }

        return view('layouts.pembibitan.index', compact('batches', 'kolams', 'kpis'));
    }
}

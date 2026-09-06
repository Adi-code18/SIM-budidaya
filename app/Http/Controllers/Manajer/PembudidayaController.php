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
            $activePembesaran = $k->batchPembesaran()
                ->where('status_siklus', '!=', 'selesai')
                ->where('status_siklus', '!=', 'gagal')
                ->latest('id_pembesaran')
                ->first();

            $activePembibitan = $k->batchPembibitan()
                ->where('status', '!=', 'selesai')
                ->where('status', '!=', 'gagal')
                ->latest('id_batch')
                ->first();

            $pembudidaya = $k->user ? $k->user->nama : 'Petugas Budidaya';
            $words = explode(' ', $pembudidaya);
            $initials = strtoupper(substr($words[0] ?? 'P', 0, 1) . substr($words[1] ?? ($words[0] ?? 'B'), 0, 1));

            if ($activePembesaran) {
                $status = ($activePembesaran->status_siklus === 'siap_panen') ? 'Siap Panen' : 'Terisi (Pembesaran)';
                $statusClass = ($activePembesaran->status_siklus === 'siap_panen') ? 'bg-amber-100 text-amber-800' : 'bg-emerald-100 text-emerald-800';
                $dotClass = ($activePembesaran->status_siklus === 'siap_panen') ? 'bg-amber-500' : 'bg-emerald-500';
                $jenisIkan = $activePembesaran->jenis_ikan;
                $tebarBenih = $activePembesaran->tgl_tebar ? Carbon::parse($activePembesaran->tgl_tebar)->translatedFormat('d M Y') : '-';
                $populasi = number_format($activePembesaran->biomassa_est ?: 0, 1, ',', '.') . ' kg';
                $populasiRaw = (float) $activePembesaran->biomassa_est;
            } elseif ($activePembibitan) {
                $benihHidup = max(0, ((int) $activePembibitan->jumlah_bibitAwal) - ((int) $activePembibitan->jumlah_kematian));
                $status = 'Terisi (Pembibitan)';
                $statusClass = 'bg-sky-100 text-sky-800';
                $dotClass = 'bg-sky-500';
                $jenisIkan = $activePembibitan->jenis_ikan ?? ($activePembibitan->ikan ? $activePembibitan->ikan->nama_ikan : 'Bibit Ikan');
                $tebarBenih = $activePembibitan->tgl_pemijahan ? Carbon::parse($activePembibitan->tgl_pemijahan)->translatedFormat('d M Y') : '-';
                $populasi = number_format($benihHidup, 0, ',', '.') . ' ekor';
                $populasiRaw = $benihHidup;
            } else {
                // Kolam Kosong / Belum Diisi
                $status = 'Kosong / Tersedia';
                $statusClass = 'bg-slate-100 text-slate-600';
                $dotClass = 'bg-slate-400';
                $jenisIkan = 'Kosong (Belum Diisi)';
                $tebarBenih = '-';
                $populasi = '0 (Kapasitas: ' . number_format($k->kapasitas, 0, ',', '.') . ')';
                $populasiRaw = 0;
            }

            $kolams[] = [
                'id'           => $k->nama_kolam,
                'lokasi'       => $k->tipe_kolam,
                'pembudidaya'  => $pembudidaya,
                'initials'     => $initials,
                'colorClass'   => $colorClasses[$idx % count($colorClasses)],
                'jenisIkan'    => $jenisIkan,
                'tebarBenih'   => $tebarBenih,
                'populasi'     => $populasi,
                'populasiRaw'  => $populasiRaw,
                'status'       => $status,
                'statusClass'  => $statusClass,
                'dotClass'     => $dotClass
            ];
        }

        // Statistik Ringkasan Nyata dari Database
        $totalKapasitas = Kolam::sum('kapasitas');
        $totalBenihAktif = BatchPembibitan::where('status', '!=', 'selesai')->where('status', '!=', 'gagal')->get()->sum(function($b) {
            return max(0, $b->jumlah_bibitAwal - $b->jumlah_kematian);
        });

        $totalAwalBibit = BatchPembibitan::sum('jumlah_bibitAwal');
        $totalMatiBibit = BatchPembibitan::sum('jumlah_kematian');
        $keberhasilanRate = $totalAwalBibit > 0 ? round((($totalAwalBibit - $totalMatiBibit) / $totalAwalBibit) * 100, 1) : 100.0;

        $kolamSiapPanenCount = BatchPembesaran::whereIn('status_siklus', ['siap_panen', 'selesai'])->count();
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

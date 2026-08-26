<?php

namespace App\Http\Controllers\Manajer;

use App\Http\Controllers\Controller;
use App\Models\Keuangan;
use App\Models\Kolam;
use Illuminate\Http\Request;

class KeuanganWebController extends Controller
{
    public function index()
    {
        $keuanganRecords = Keuangan::with(['kolam', 'user'])->latest('tanggal_transaksi')->get();
        $kolams = Kolam::all();

        $transactions = [];
        foreach ($keuanganRecords as $k) {
            $transactions[] = [
                'id'         => '#TRX-' . ($k->ref_id ?? ('202310-' . str_pad($k->id_keuangan, 4, '0', STR_PAD_LEFT))),
                'tanggal'    => $k->tanggal_transaksi ? \Carbon\Carbon::parse($k->tanggal_transaksi)->toDateString() : '2026-08-06',
                'tipe'       => $k->tipe_transaksi === 'pemasukan' ? 'income' : 'expense',
                'nominal'    => (float) $k->nominal,
                'kategori'   => $k->kategori,
                'ref'        => $k->ref_id ?? ('INV/' . date('Y') . '/' . $k->id_keuangan),
                'kolam'      => $k->kolam ? $k->kolam->nama_kolam : 'Tidak dialokasikan',
                'keterangan' => $k->keterangan ?? '-'
            ];
        }

        $totalIncome = Keuangan::where('tipe_transaksi', 'pemasukan')->sum('nominal');
        $totalExpense = Keuangan::where('tipe_transaksi', 'pengeluaran')->sum('nominal');
        $saldo = $totalIncome - $totalExpense;
        $netMargin = $totalIncome > 0 ? round(($saldo / $totalIncome) * 100, 1) : 0;

        $kpis = [
            'incomeFormatted'   => 'Rp' . number_format($totalIncome, 0, ',', '.'),
            'expenseFormatted'  => 'Rp' . number_format($totalExpense, 0, ',', '.'),
            'saldoFormatted'    => 'Rp' . number_format($saldo, 0, ',', '.'),
            'netMargin'         => $netMargin,
            'incomeShort'       => 'Rp ' . number_format($totalIncome / 1000000, 1) . ' Jt',
            'expenseShort'      => 'Rp ' . number_format($totalExpense / 1000000, 1) . ' Jt',
            'totalTrx'          => $keuanganRecords->count(),
        ];

        return view('layouts.keuangan.index', compact('transactions', 'kolams', 'totalIncome', 'totalExpense', 'saldo', 'kpis'));
    }
}

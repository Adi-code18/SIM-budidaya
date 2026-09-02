<?php

namespace App\Http\Controllers\Manajer;

use App\Http\Controllers\Controller;
use App\Models\Keuangan;
use App\Models\Kolam;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class KeuanganWebController extends Controller
{
    public function index()
    {
        $keuanganRecords = Keuangan::with(['kolam', 'user'])->latest('tanggal_transaksi')->latest('id_keuangan')->get();
        $kolams = Kolam::orderBy('nama_kolam')->get();

        $transactions = [];
        foreach ($keuanganRecords as $k) {
            $transactions[] = [
                'raw_id'     => $k->id_keuangan,
                'id'         => '#TRX-' . ($k->ref_id ? preg_replace('/[^A-Za-z0-9\-]/', '', $k->ref_id) : ('2026-' . str_pad($k->id_keuangan, 4, '0', STR_PAD_LEFT))),
                'tanggal'    => $k->tanggal_transaksi ? Carbon::parse($k->tanggal_transaksi)->toDateString() : date('Y-m-d'),
                'tipe'       => in_array(strtolower($k->tipe_transaksi), ['pemasukan', 'income']) ? 'income' : 'expense',
                'nominal'    => (float) $k->nominal,
                'kategori'   => $k->kategori,
                'ref'        => $k->ref_id ?? ('TRX-' . str_pad($k->id_keuangan, 4, '0', STR_PAD_LEFT)),
                'id_kolam'   => $k->id_kolam,
                'kolam'      => $k->kolam ? $k->kolam->nama_kolam : 'Tidak dialokasikan',
                'keterangan' => $k->keterangan ?? '-'
            ];
        }

        $totalIncome = Keuangan::whereIn('tipe_transaksi', ['pemasukan', 'income'])->sum('nominal');
        $totalExpense = Keuangan::whereIn('tipe_transaksi', ['pengeluaran', 'expense'])->sum('nominal');
        $saldo = $totalIncome - $totalExpense;
        $netMargin = $totalIncome > 0 ? round(($saldo / $totalIncome) * 100, 1) : 0;

        // Breakdown Kategori Pengeluaran
        $pakanTotal = Keuangan::whereIn('tipe_transaksi', ['pengeluaran', 'expense'])
            ->where('kategori', 'like', '%Pakan%')
            ->sum('nominal');
        $operasionalTotal = Keuangan::whereIn('tipe_transaksi', ['pengeluaran', 'expense'])
            ->where(function ($q) {
                $q->where('kategori', 'like', '%Operasional%')
                  ->orWhere('kategori', 'like', '%Perawatan%')
                  ->orWhere('kategori', 'like', '%Listrik%')
                  ->orWhere('kategori', 'like', '%Gaji%')
                  ->orWhere('kategori', 'like', '%Obat%');
            })
            ->sum('nominal');

        $kpis = [
            'incomeFormatted'   => 'Rp' . number_format($totalIncome, 0, ',', '.'),
            'expenseFormatted'  => 'Rp' . number_format($totalExpense, 0, ',', '.'),
            'saldoFormatted'    => 'Rp' . number_format($saldo, 0, ',', '.'),
            'netMargin'         => $netMargin,
            'incomeShort'       => 'Rp ' . number_format($totalIncome / 1000000, 1) . ' Jt',
            'expenseShort'      => 'Rp ' . number_format($totalExpense / 1000000, 1) . ' Jt',
            'totalTrx'          => $keuanganRecords->count(),
            'pakanFormatted'    => 'Rp ' . number_format($pakanTotal, 0, ',', '.'),
            'operasionalFormatted' => 'Rp ' . number_format($operasionalTotal, 0, ',', '.'),
        ];

        return view('layouts.keuangan.index', compact('transactions', 'kolams', 'totalIncome', 'totalExpense', 'saldo', 'kpis'));
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
            'ref_id'            => $request->ref_id ?: ('TRX-' . strtoupper(substr($tipe, 0, 2)) . '-' . rand(100, 999)),
        ]);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Transaksi keuangan berhasil dicatat!',
                'data'    => $keuangan
            ]);
        }

        return redirect()->route('keuangan')->with('success', 'Transaksi keuangan berhasil dicatat!');
    }

    public function update(Request $request, $id)
    {
        $cleanId = is_numeric($id) ? $id : (int) preg_replace('/[^0-9]/', '', $id);
        $keuangan = Keuangan::find($cleanId);

        if (!$keuangan) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'message' => 'Data transaksi tidak ditemukan.'], 404);
            }
            return redirect()->route('keuangan')->with('error', 'Data transaksi tidak ditemukan.');
        }

        $request->validate([
            'tanggal_transaksi' => 'nullable|date',
            'tipe_transaksi'    => 'nullable|string',
            'kategori'          => 'nullable|string',
            'nominal'           => 'nullable|numeric|min:1',
            'id_kolam'          => 'nullable',
            'ref_id'            => 'nullable|string',
            'keterangan'        => 'nullable|string',
        ]);

        if ($request->filled('tipe_transaksi')) {
            $keuangan->tipe_transaksi = in_array(strtolower($request->tipe_transaksi), ['income', 'pemasukan']) ? 'pemasukan' : 'pengeluaran';
        }

        if ($request->filled('tanggal_transaksi')) {
            $keuangan->tanggal_transaksi = $request->tanggal_transaksi;
        }

        if ($request->filled('kategori')) {
            $keuangan->kategori = $request->kategori;
        }

        if ($request->filled('nominal')) {
            $keuangan->nominal = $request->nominal;
        }

        if ($request->has('id_kolam')) {
            if (empty($request->id_kolam) || $request->id_kolam === 'Tidak dialokasikan') {
                $keuangan->id_kolam = null;
            } else {
                $kolam = Kolam::where('id_kolam', $request->id_kolam)->orWhere('nama_kolam', $request->id_kolam)->first();
                $keuangan->id_kolam = $kolam ? $kolam->id_kolam : null;
            }
        }

        if ($request->has('ref_id')) {
            $keuangan->ref_id = $request->ref_id;
        }

        if ($request->has('keterangan')) {
            $keuangan->keterangan = $request->keterangan;
        }

        $keuangan->save();

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Data transaksi berhasil diperbarui!',
                'data'    => $keuangan
            ]);
        }

        return redirect()->route('keuangan')->with('success', 'Data transaksi berhasil diperbarui!');
    }

    public function destroy(Request $request, $id)
    {
        $cleanId = is_numeric($id) ? $id : (int) preg_replace('/[^0-9]/', '', $id);
        $keuangan = Keuangan::find($cleanId);

        if (!$keuangan) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'message' => 'Data transaksi tidak ditemukan.'], 404);
            }
            return redirect()->route('keuangan')->with('error', 'Data transaksi tidak ditemukan.');
        }

        $keuangan->delete();

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Data transaksi berhasil dihapus!'
            ]);
        }

        return redirect()->route('keuangan')->with('success', 'Data transaksi berhasil dihapus!');
    }
}

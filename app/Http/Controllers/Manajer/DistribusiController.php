<?php

namespace App\Http\Controllers\Manajer;

use App\Http\Controllers\Controller;
use App\Models\BatchPembesaran;
use App\Models\MitraDistributor;
use App\Models\TransaksiDistribusi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class DistribusiController extends Controller
{
    public function index()
    {
        $transaksiRecords = TransaksiDistribusi::with(['mitra', 'batchPembesaran', 'user'])->latest('id_transaksi')->get();
        $mitraRecords = MitraDistributor::all();
        $batchRecords = BatchPembesaran::with('kolam')->where('status_siklus', '!=', 'gagal')->latest('id_pembesaran')->get();

        $orders = [];
        foreach ($transaksiRecords as $t) {
            $status = $t->status_order;
            if ($status === 'dalam_pengiriman') {
                $status = 'pemberokian'; // UI key
            }

            $orders[] = [
                'id_transaksi' => $t->id_transaksi,
                'id'           => '#ORD-2023-' . str_pad($t->id_transaksi, 4, '0', STR_PAD_LEFT),
                'id_mitra'     => $t->id_mitra,
                'customer'     => $t->mitra ? $t->mitra->nama_mitra : 'Mitra #' . $t->id_mitra,
                'tipe_mitra'   => $t->mitra ? $t->mitra->tipe_mitra : 'Distributor',
                'volume'       => number_format($t->Total_kg, 0, ',', '.') . ' kg',
                'total_kg'     => (float) $t->Total_kg,
                'harga_total'  => (float) $t->harga_total,
                'harga_format' => 'Rp ' . number_format($t->harga_total, 0, ',', '.'),
                'jenis_order'  => $t->Jenis_order ?? 'Ikan Segar',
                'status'       => $status,
                'alamat'       => $t->mitra ? $t->mitra->alamat : '-',
                'tanggal'      => $t->tanggal_order ? Carbon::parse($t->tanggal_order)->toDateString() : Carbon::today()->toDateString(),
                'label'        => true
            ];
        }

        $mitraList = $mitraRecords->map(function ($m) {
            return [
                'id_mitra'   => $m->id_mitra,
                'nama_mitra' => $m->nama_mitra,
                'tipe_mitra' => $m->tipe_mitra,
                'alamat'     => $m->alamat,
                'label'      => 'MTR-' . str_pad($m->id_mitra, 3, '0', STR_PAD_LEFT) . ' — ' . $m->nama_mitra . ' (' . $m->tipe_mitra . ')',
            ];
        });

        $batches = $batchRecords->map(function ($b) {
            return [
                'id_pembesaran' => $b->id_pembesaran,
                'label'         => '#PB-' . str_pad($b->id_pembesaran, 5, '0', STR_PAD_LEFT) . ' (' . $b->jenis_ikan . ' - ' . ($b->kolam ? $b->kolam->nama_kolam : 'Kolam') . ')',
                'jenis_ikan'    => $b->jenis_ikan,
            ];
        });

        return view('layouts.distribusi.index', compact('orders', 'mitraList', 'mitraRecords', 'batches'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'id_mitra'      => 'required|exists:mitra_distributor,id_mitra',
            'id_pembesaran' => 'nullable|exists:batch_pembesaran,id_pembesaran',
            'tanggal_order' => 'nullable|date',
            'Total_kg'      => 'required|numeric|min:1',
            'harga_total'   => 'nullable|numeric|min:0',
            'Jenis_order'   => 'nullable|string',
            'status_order'  => 'nullable|string',
        ]);

        $status = $request->status_order ?? 'pending';
        if ($status === 'pemberokian') {
            $status = 'dalam_pengiriman';
        }

        $totalKg = (float) $request->Total_kg;
        $hargaTotal = (float) ($request->harga_total ?? ($totalKg * 35000)); // default 35.000 / kg

        $transaksi = TransaksiDistribusi::create([
            'id_user'       => Auth::id() ?? 1,
            'id_mitra'      => $request->id_mitra,
            'id_pembesaran' => $request->id_pembesaran ?? BatchPembesaran::first()?->id_pembesaran ?? 1,
            'tanggal_order' => $request->tanggal_order ?? Carbon::today()->toDateString(),
            'Total_kg'      => $totalKg,
            'harga_total'   => $hargaTotal,
            'status_order'  => $status,
            'Jenis_order'   => $request->Jenis_order ?? 'Ikan Segar',
        ]);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success'   => true,
                'message'   => 'Pesanan distribusi baru berhasil ditambahkan!',
                'transaksi' => $transaksi->load(['mitra', 'batchPembesaran'])
            ]);
        }

        return redirect()->route('distribusi')->with('success', 'Pesanan distribusi berhasil disimpan!');
    }

    public function update(Request $request, $id)
    {
        $cleanId = is_numeric($id) ? $id : (int) preg_replace('/[^0-9]/', '', $id);
        $transaksi = TransaksiDistribusi::find($cleanId);

        if (!$transaksi) {
            return response()->json(['success' => false, 'message' => 'Transaksi tidak ditemukan.'], 404);
        }

        if ($request->filled('status_order')) {
            $status = $request->status_order;
            if ($status === 'pemberokian') {
                $status = 'dalam_pengiriman';
            }
            $transaksi->status_order = $status;
        }

        if ($request->filled('Total_kg')) {
            $transaksi->Total_kg = $request->Total_kg;
        }

        if ($request->filled('harga_total')) {
            $transaksi->harga_total = $request->harga_total;
        }

        $transaksi->save();

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success'   => true,
                'message'   => 'Status order distribusi berhasil diperbarui!',
                'transaksi' => $transaksi->load(['mitra', 'batchPembesaran'])
            ]);
        }

        return redirect()->route('distribusi')->with('success', 'Transaksi berhasil diperbarui!');
    }
}

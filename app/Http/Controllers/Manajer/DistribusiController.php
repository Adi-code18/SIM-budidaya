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
            $status = $t->status_order ?: 'pending';

            $orders[] = [
                'id_transaksi'  => $t->id_transaksi,
                'id'            => '#ORD-2023-' . str_pad($t->id_transaksi, 4, '0', STR_PAD_LEFT),
                'id_mitra'      => $t->id_mitra,
                'id_pembesaran' => $t->id_pembesaran,
                'customer'      => $t->mitra ? $t->mitra->nama_mitra : 'Mitra #' . $t->id_mitra,
                'tipe_mitra'    => $t->mitra ? $t->mitra->tipe_mitra : 'Distributor',
                'volume'        => number_format($t->Total_kg, 0, ',', '.') . ' kg',
                'total_kg'      => (float) $t->Total_kg,
                'harga_total'   => (float) $t->harga_total,
                'harga_format'  => 'Rp ' . number_format($t->harga_total, 0, ',', '.'),
                'jenis_ikan'    => $t->batchPembesaran ? $t->batchPembesaran->jenis_ikan : ($t->Jenis_order ?? 'Ikan Segar'),
                'kolam_asal'    => $t->batchPembesaran && $t->batchPembesaran->kolam ? $t->batchPembesaran->kolam->nama_kolam : 'Kolam Pembesaran',
                'batch_code'    => $t->batchPembesaran ? ('#PB-' . str_pad($t->id_pembesaran, 5, '0', STR_PAD_LEFT)) : null,
                'jenis_order'   => $t->Jenis_order ?? 'Ikan Segar',
                'status'        => $status,
                'alamat'        => $t->mitra ? $t->mitra->alamat : '-',
                'tanggal'       => $t->tanggal_order ? Carbon::parse($t->tanggal_order)->toDateString() : Carbon::today()->toDateString(),
                'label'         => true
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

        $batchRecords = BatchPembesaran::with('kolam')
            ->where('status_siklus', '!=', 'gagal')
            ->where('status_siklus', '!=', 'selesai')
            ->latest('id_pembesaran')->get();

        $batches = $batchRecords->map(function ($b) {
            $kolamName = $b->kolam ? $b->kolam->nama_kolam : 'Kolam #' . $b->id_kolam;
            $tipeKolam = $b->kolam ? $b->kolam->tipe_kolam : '';
            $isStok = stripos($kolamName, 'Stok') !== false || stripos($tipeKolam, 'Pemberokan') !== false || stripos($tipeKolam, 'Penampungan') !== false;
            $stokBiomassa = number_format($b->biomassa_est, 0, ',', '.');
            return [
                'id_pembesaran' => $b->id_pembesaran,
                'id_kolam'      => $b->id_kolam,
                'label'         => '#PB-' . str_pad($b->id_pembesaran, 5, '0', STR_PAD_LEFT) . ' — ' . $b->jenis_ikan . ' (' . $kolamName . ' - Stok: ' . $stokBiomassa . ' kg)' . ($isStok ? ' [Kolam Stok / Buffer]' : ''),
                'jenis_ikan'    => $b->jenis_ikan,
                'kolam'         => $kolamName,
                'is_stok'       => $isStok,
                'biomassa_est'  => (float) $b->biomassa_est,
            ];
        });

        $stockPonds = \App\Models\Kolam::where(function ($q) {
            $q->where('nama_kolam', 'like', '%Stok%')
              ->orWhere('tipe_kolam', 'like', '%Pemberokan%')
              ->orWhere('tipe_kolam', 'like', '%Penampungan%');
        })->get()->map(function ($k) {
            return [
                'id_kolam'   => $k->id_kolam,
                'nama_kolam' => $k->nama_kolam,
                'tipe_kolam' => $k->tipe_kolam,
                'kapasitas'  => $k->kapasitas,
            ];
        });

        $totalStokSiapPanen = (float) BatchPembesaran::where('status_siklus', '!=', 'gagal')
            ->where('status_siklus', '!=', 'selesai')
            ->sum('biomassa_est');

        $totalBufferPemberokan = (float) TransaksiDistribusi::whereIn('status_order', ['dalam_pengiriman', 'pemberokian', 'siap_kirim'])
            ->sum('Total_kg');

        return view('layouts.distribusi.index', compact(
            'orders', 
            'mitraList', 
            'mitraRecords', 
            'batches',
            'stockPonds',
            'totalStokSiapPanen',
            'totalBufferPemberokan'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'id_mitra'      => 'required|exists:mitra_distributor,id_mitra',
            'id_pembesaran' => 'required|exists:batch_pembesaran,id_pembesaran',
            'tanggal_order' => 'nullable|date',
            'Total_kg'      => 'required|numeric|min:1',
            'harga_total'   => 'nullable|numeric|min:0',
            'Jenis_order'   => 'nullable|string',
            'status_order'  => 'nullable|string',
        ], [
            'id_mitra.required'      => 'Mitra Distributor wajib dipilih.',
            'id_pembesaran.required' => 'Batch Pembesaran / Jenis Ikan wajib dipilih.',
            'Total_kg.required'      => 'Total berat (kg) wajib diisi.',
        ]);

        $status = $request->status_order ?? 'pending';
        if ($status === 'pemberokian') {
            $status = 'dalam_pengiriman';
        }

        $totalKg = (float) $request->Total_kg;
        $hargaTotal = (float) ($request->harga_total ?? ($totalKg * 35000)); // default 35.000 / kg

        $jenisOrder = $request->Jenis_order ?? 'Ikan Segar';

        // Handle multi-source / deficit notes if supplied
        $primaryBatch = BatchPembesaran::with('kolam')->find($request->id_pembesaran);
        $alokasiDetail = $request->alokasi_detail;
        if (!empty($alokasiDetail) && is_array($alokasiDetail)) {
            $parts = [];
            if (!empty($alokasiDetail['utama_kg'])) {
                $kolamUtamaNama = $primaryBatch && $primaryBatch->kolam ? $primaryBatch->kolam->nama_kolam : 'Kolam Utama';
                $parts[] = "{$kolamUtamaNama}: {$alokasiDetail['utama_kg']}kg";
                if ($primaryBatch) {
                    $primaryBatch->biomassa_est = max(0, $primaryBatch->biomassa_est - (float)$alokasiDetail['utama_kg']);
                    $primaryBatch->save();
                }
            }
            if (!empty($alokasiDetail['buffer_kg']) && (float)$alokasiDetail['buffer_kg'] > 0) {
                $bufferNama = $alokasiDetail['buffer_nama'] ?? 'Kolam Stok';
                $parts[] = "{$bufferNama}: {$alokasiDetail['buffer_kg']}kg";
                if (!empty($alokasiDetail['id_batch_buffer'])) {
                    $bufBatch = BatchPembesaran::find($alokasiDetail['id_batch_buffer']);
                    if ($bufBatch) {
                        $bufBatch->biomassa_est = max(0, $bufBatch->biomassa_est - (float)$alokasiDetail['buffer_kg']);
                        $bufBatch->save();
                    }
                }
            }
            if (!empty($alokasiDetail['cross_kg']) && (float)$alokasiDetail['cross_kg'] > 0) {
                $crossNama = $alokasiDetail['cross_nama'] ?? 'Cross-Batch';
                $parts[] = "{$crossNama}: {$alokasiDetail['cross_kg']}kg";
                if (!empty($alokasiDetail['id_batch_cross'])) {
                    $crossBatch = BatchPembesaran::find($alokasiDetail['id_batch_cross']);
                    if ($crossBatch) {
                        $crossBatch->biomassa_est = max(0, $crossBatch->biomassa_est - (float)$alokasiDetail['cross_kg']);
                        $crossBatch->save();
                    }
                }
            }
            if (count($parts) > 1) {
                $jenisOrder = "Ikan Segar (Multi-Kolam: " . implode(', ', $parts) . ")";
            }
        }

        $transaksi = TransaksiDistribusi::create([
            'id_user'       => Auth::id() ?? 1,
            'id_mitra'      => $request->id_mitra,
            'id_pembesaran' => $request->id_pembesaran,
            'tanggal_order' => $request->tanggal_order ?? Carbon::today()->toDateString(),
            'Total_kg'      => $totalKg,
            'harga_total'   => $hargaTotal,
            'status_order'  => $status,
            'Jenis_order'   => $jenisOrder,
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
            $transaksi->status_order = $request->status_order;
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

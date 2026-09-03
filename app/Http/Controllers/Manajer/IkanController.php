<?php

namespace App\Http\Controllers\Manajer;

use App\Http\Controllers\Controller;
use App\Models\Ikan;
use App\Models\BatchPembibitan;
use Illuminate\Http\Request;

class IkanController extends Controller
{
    public function index(Request $request)
    {
        $ikans = Ikan::with('batchPembibitan')->latest('id_ikan')->get();

        // Hitung metrik ringkasan
        $totalSpesies = $ikans->count();
        $avgPenetasan = $totalSpesies > 0 ? round($ikans->avg('durasi_penetasan'), 1) : 0;
        $avgPembibitan = $totalSpesies > 0 ? round($ikans->avg('durasi_pembibitan'), 1) : 0;
        $totalSiklus = $avgPenetasan + $avgPembibitan;

        $kpis = [
            'totalSpesies'   => $totalSpesies,
            'avgPenetasan'   => $avgPenetasan . ' Hari',
            'avgPembibitan'  => $avgPembibitan . ' Hari',
            'totalSiklus'    => $totalSiklus . ' Hari',
        ];

        $batches = BatchPembibitan::latest('id_batch')->get();

        return view('layouts.ikan.index', compact('ikans', 'kpis', 'batches'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_ikan'          => 'required|string|max:255',
            'durasi_penetasan'   => 'required|integer|min:1',
            'durasi_pembibitan'  => 'required|integer|min:1',
            'id_batch'           => 'nullable|exists:batch_pembibitan,id_batch',
        ], [
            'nama_ikan.required'         => 'Nama jenis ikan wajib diisi.',
            'durasi_penetasan.required'  => 'Durasi masa penetasan wajib diisi.',
            'durasi_pembibitan.required' => 'Durasi masa pembibitan wajib diisi.',
        ]);

        $ikan = Ikan::create([
            'nama_ikan'          => $request->nama_ikan,
            'durasi_penetasan'   => $request->durasi_penetasan,
            'durasi_pembibitan'  => $request->durasi_pembibitan,
            'id_batch'           => $request->id_batch ?: null,
        ]);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => "Jenis Ikan '{$ikan->nama_ikan}' berhasil ditambahkan ke sistem!",
                'ikan'    => $ikan
            ]);
        }

        return redirect()->route('ikan')->with('success', "Jenis Ikan '{$ikan->nama_ikan}' berhasil ditambahkan!");
    }

    public function update(Request $request, $id)
    {
        $ikan = Ikan::findOrFail($id);

        $request->validate([
            'nama_ikan'          => 'required|string|max:255',
            'durasi_penetasan'   => 'required|integer|min:1',
            'durasi_pembibitan'  => 'required|integer|min:1',
            'id_batch'           => 'nullable|exists:batch_pembibitan,id_batch',
        ]);

        $ikan->update([
            'nama_ikan'          => $request->nama_ikan,
            'durasi_penetasan'   => $request->durasi_penetasan,
            'durasi_pembibitan'  => $request->durasi_pembibitan,
            'id_batch'           => $request->id_batch ?: null,
        ]);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => "Data jenis ikan '{$ikan->nama_ikan}' berhasil diperbarui!",
                'ikan'    => $ikan
            ]);
        }

        return redirect()->route('ikan')->with('success', "Data jenis ikan '{$ikan->nama_ikan}' berhasil diperbarui!");
    }

    public function destroy($id)
    {
        $ikan = Ikan::findOrFail($id);
        $nama = $ikan->nama_ikan;
        $ikan->delete();

        if (request()->wantsJson() || request()->ajax()) {
            return response()->json([
                'success' => true,
                'message' => "Jenis Ikan '{$nama}' berhasil dihapus dari sistem."
            ]);
        }

        return redirect()->route('ikan')->with('success', "Jenis Ikan '{$nama}' berhasil dihapus.");
    }
}

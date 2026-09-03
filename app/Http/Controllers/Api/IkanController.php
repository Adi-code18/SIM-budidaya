<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Ikan;
use Illuminate\Http\Request;

class IkanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Ikan::with('batchPembibitan');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('nama_ikan', 'like', "%{$search}%");
        }

        if ($request->has('all') && $request->boolean('all')) {
            $data = $query->orderBy('nama_ikan', 'asc')->get();
        } else {
            $perPage = $request->input('per_page', 15);
            $data = $query->latest('id_ikan')->paginate($perPage);
        }

        return response()->json([
            'status'  => 'success',
            'message' => 'Data jenis ikan berhasil diambil',
            'data'    => $data
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_ikan'          => 'required|string|max:255',
            'durasi_penetasan'   => 'required|integer|min:1',
            'durasi_pembibitan'  => 'required|integer|min:1',
            'id_batch'           => 'nullable|exists:batch_pembibitan,id_batch',
        ]);

        $ikan = Ikan::create($validated);
        $ikan->load('batchPembibitan');

        return response()->json([
            'status'  => 'success',
            'message' => 'Data jenis ikan berhasil ditambahkan',
            'data'    => $ikan
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $ikan = Ikan::with('batchPembibitan')->find($id);

        if (!$ikan) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Data jenis ikan tidak ditemukan'
            ], 404);
        }

        return response()->json([
            'status'  => 'success',
            'message' => 'Detail jenis ikan berhasil diambil',
            'data'    => $ikan
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $ikan = Ikan::find($id);

        if (!$ikan) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Data jenis ikan tidak ditemukan'
            ], 404);
        }

        $validated = $request->validate([
            'nama_ikan'          => 'sometimes|required|string|max:255',
            'durasi_penetasan'   => 'sometimes|required|integer|min:1',
            'durasi_pembibitan'  => 'sometimes|required|integer|min:1',
            'id_batch'           => 'nullable|exists:batch_pembibitan,id_batch',
        ]);

        $ikan->update($validated);
        $ikan->load('batchPembibitan');

        return response()->json([
            'status'  => 'success',
            'message' => 'Data jenis ikan berhasil diperbarui',
            'data'    => $ikan
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $ikan = Ikan::find($id);

        if (!$ikan) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Data jenis ikan tidak ditemukan'
            ], 404);
        }

        $ikan->delete();

        return response()->json([
            'status'  => 'success',
            'message' => 'Data jenis ikan berhasil dihapus'
        ], 200);
    }
}

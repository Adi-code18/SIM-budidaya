<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\BatchPembibitan;
use Illuminate\Http\Request;

class BatchPembibitanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = BatchPembibitan::with([
            'kolam:id_kolam,nama_kolam,tipe_kolam,status',
            'user:id_user,nama,email,role'
        ]);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('id_kolam')) {
            $query->where('id_kolam', $request->id_kolam);
        }

        if ($request->filled('jenis_ikan')) {
            $query->where('jenis_ikan', 'like', '%' . $request->jenis_ikan . '%');
        }

        $perPage = $request->input('per_page', 15);
        $data = $request->has('all') && $request->boolean('all')
            ? $query->latest('id_batch')->get()
            : $query->latest('id_batch')->paginate($perPage);

        return response()->json([
            'status' => 'success',
            'message' => 'Data batch pembibitan berhasil diambil',
            'data' => $data
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'id_kolam'          => 'required|exists:kolam,id_kolam',
            'tgl_pemijahan'     => 'required|date',
            'jumlah_bibitAwal'  => 'required|integer|min:1',
            'jenis_ikan'        => 'required|string|max:255',
            'jumlah_kematian'   => 'nullable|integer|min:0',
            'total_bobot_kg'    => 'nullable|numeric|min:0',
            'status'            => 'nullable|string|in:aktif,selesai,gagal',
            'id_user'           => 'nullable|exists:users,id_user',
        ]);

        $validated['id_user'] = $validated['id_user'] ?? $request->user()->id_user;
        $validated['jumlah_kematian'] = $validated['jumlah_kematian'] ?? 0;
        $validated['status'] = $validated['status'] ?? 'aktif';

        $batch = BatchPembibitan::create($validated);
        $batch->load(['kolam', 'user:id_user,nama,email,role']);

        return response()->json([
            'status' => 'success',
            'message' => 'Batch pembibitan berhasil dibuat',
            'data' => $batch
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $batch = BatchPembibitan::with([
            'kolam',
            'user:id_user,nama,email,role'
        ])->find($id);

        if (!$batch) {
            return response()->json([
                'status' => 'error',
                'message' => 'Batch pembibitan tidak ditemukan'
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Detail batch pembibitan berhasil diambil',
            'data' => $batch
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $batch = BatchPembibitan::find($id);

        if (!$batch) {
            return response()->json([
                'status' => 'error',
                'message' => 'Batch pembibitan tidak ditemukan'
            ], 404);
        }

        $validated = $request->validate([
            'id_kolam'          => 'sometimes|required|exists:kolam,id_kolam',
            'tgl_pemijahan'     => 'sometimes|required|date',
            'jumlah_bibitAwal'  => 'sometimes|required|integer|min:1',
            'jenis_ikan'        => 'sometimes|required|string|max:255',
            'jumlah_kematian'   => 'nullable|integer|min:0',
            'total_bobot_kg'    => 'nullable|numeric|min:0',
            'status'            => 'nullable|string|in:aktif,selesai,gagal',
            'id_user'           => 'nullable|exists:users,id_user',
        ]);

        $batch->update($validated);
        $batch->load(['kolam', 'user:id_user,nama,email,role']);

        return response()->json([
            'status' => 'success',
            'message' => 'Data batch pembibitan berhasil diperbarui',
            'data' => $batch
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $batch = BatchPembibitan::find($id);

        if (!$batch) {
            return response()->json([
                'status' => 'error',
                'message' => 'Batch pembibitan tidak ditemukan'
            ], 404);
        }

        $batch->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Batch pembibitan berhasil dihapus'
        ], 200);
    }
}

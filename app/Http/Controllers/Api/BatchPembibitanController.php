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
            'id_kolam'              => 'required|exists:kolam,id_kolam',
            'tgl_pemijahan'         => 'required|date',
            'est_prcs_pembibitaan'  => 'nullable|date',
            'jumlah_bibitAwal'      => 'required|integer|min:1',
            'jumlah_kematian'       => 'nullable|integer|min:0',
            'total_bobot_kg'        => 'nullable|numeric|min:0',
            'status'                => 'nullable|string|in:aktif,selesai,gagal',
            'id_user'               => 'nullable|exists:users,id_user',
        ]);

        $batch = BatchPembibitan::create([
            'id_kolam'              => $validated['id_kolam'],
            'id_user'               => $validated['id_user'] ?? (Auth::id() ?? 1),
            'tgl_pemijahan'         => $validated['tgl_pemijahan'],
            'est_prcs_pembibitaan'  => $validated['est_prcs_pembibitaan'] ?? null,
            'jumlah_bibitAwal'      => $validated['jumlah_bibitAwal'],
            'jumlah_kematian'       => $validated['jumlah_kematian'] ?? 0,
            'total_bobot_kg'        => $validated['total_bobot_kg'] ?? 0,
            'status'                => $validated['status'] ?? 'aktif',
        ]);

        $batch->load(['kolam', 'user:id_user,nama,email,role']);

        return response()->json([
            'status' => 'success',
            'message' => 'Data batch pembibitan berhasil ditambahkan',
            'data' => $batch
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $batch = BatchPembibitan::with(['kolam', 'user:id_user,nama,email,role'])->find($id);

        if (!$batch) {
            return response()->json([
                'status' => 'error',
                'message' => 'Data batch pembibitan tidak ditemukan'
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
                'message' => 'Data batch pembibitan tidak ditemukan'
            ], 404);
        }

        $validated = $request->validate([
            'id_kolam'              => 'sometimes|required|exists:kolam,id_kolam',
            'tgl_pemijahan'         => 'sometimes|required|date',
            'est_prcs_pembibitaan'  => 'nullable|date',
            'jumlah_bibitAwal'      => 'sometimes|required|integer|min:1',
            'jumlah_kematian'       => 'nullable|integer|min:0',
            'total_bobot_kg'        => 'nullable|numeric|min:0',
            'status'                => 'nullable|string|in:aktif,selesai,gagal',
            'id_user'               => 'nullable|exists:users,id_user',
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

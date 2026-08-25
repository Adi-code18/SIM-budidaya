<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\BatchPembesaran;
use Illuminate\Http\Request;

class BatchPembesaranController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = BatchPembesaran::with([
            'kolam:id_kolam,nama_kolam,tipe_kolam,status',
            'user:id_user,nama,email,role'
        ]);

        if ($request->filled('status_siklus')) {
            $query->where('status_siklus', $request->status_siklus);
        }

        if ($request->filled('id_kolam')) {
            $query->where('id_kolam', $request->id_kolam);
        }

        if ($request->filled('jenis_ikan')) {
            $query->where('jenis_ikan', 'like', '%' . $request->jenis_ikan . '%');
        }

        $perPage = $request->input('per_page', 15);
        $data = $request->has('all') && $request->boolean('all')
            ? $query->latest('id_pembesaran')->get()
            : $query->latest('id_pembesaran')->paginate($perPage);

        return response()->json([
            'status' => 'success',
            'message' => 'Data batch pembesaran berhasil diambil',
            'data' => $data
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'id_kolam'        => 'required|exists:kolam,id_kolam',
            'tgl_tebar'       => 'required|date',
            'jenis_ikan'      => 'required|string|max:255',
            'biomassa_est'    => 'nullable|numeric|min:0',
            'fcr'             => 'nullable|numeric|min:0',
            'target_panen_kg' => 'nullable|numeric|min:0',
            'jumlah_panen_kg' => 'nullable|numeric|min:0',
            'status_siklus'   => 'nullable|string|in:persiapan,berjalan,siap_panen,panen_selesai,gagal',
            'id_user'         => 'nullable|exists:users,id_user',
        ]);

        $validated['id_user'] = $validated['id_user'] ?? $request->user()->id_user;
        $validated['biomassa_est'] = $validated['biomassa_est'] ?? 0;
        $validated['target_panen_kg'] = $validated['target_panen_kg'] ?? 0;
        $validated['jumlah_panen_kg'] = $validated['jumlah_panen_kg'] ?? 0;
        $validated['status_siklus'] = $validated['status_siklus'] ?? 'berjalan';

        $batch = BatchPembesaran::create($validated);
        $batch->load(['kolam', 'user:id_user,nama,email,role']);

        return response()->json([
            'status' => 'success',
            'message' => 'Batch pembesaran berhasil dibuat',
            'data' => $batch
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $batch = BatchPembesaran::with([
            'kolam',
            'user:id_user,nama,email,role'
        ])->find($id);

        if (!$batch) {
            return response()->json([
                'status' => 'error',
                'message' => 'Batch pembesaran tidak ditemukan'
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Detail batch pembesaran berhasil diambil',
            'data' => $batch
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $batch = BatchPembesaran::find($id);

        if (!$batch) {
            return response()->json([
                'status' => 'error',
                'message' => 'Batch pembesaran tidak ditemukan'
            ], 404);
        }

        $validated = $request->validate([
            'id_kolam'        => 'sometimes|required|exists:kolam,id_kolam',
            'tgl_tebar'       => 'sometimes|required|date',
            'jenis_ikan'      => 'sometimes|required|string|max:255',
            'biomassa_est'    => 'nullable|numeric|min:0',
            'fcr'             => 'nullable|numeric|min:0',
            'target_panen_kg' => 'nullable|numeric|min:0',
            'jumlah_panen_kg' => 'nullable|numeric|min:0',
            'status_siklus'   => 'nullable|string|in:persiapan,berjalan,siap_panen,panen_selesai,gagal',
            'id_user'         => 'nullable|exists:users,id_user',
        ]);

        $batch->update($validated);
        $batch->load(['kolam', 'user:id_user,nama,email,role']);

        return response()->json([
            'status' => 'success',
            'message' => 'Data batch pembesaran berhasil diperbarui',
            'data' => $batch
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $batch = BatchPembesaran::find($id);

        if (!$batch) {
            return response()->json([
                'status' => 'error',
                'message' => 'Batch pembesaran tidak ditemukan'
            ], 404);
        }

        $batch->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Batch pembesaran berhasil dihapus'
        ], 200);
    }
}

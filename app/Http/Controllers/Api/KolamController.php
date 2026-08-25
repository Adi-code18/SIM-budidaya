<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Kolam;
use Illuminate\Http\Request;

class KolamController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Kolam::with(['user:id_user,nama,email,role']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('tipe_kolam')) {
            $query->where('tipe_kolam', 'like', '%' . $request->tipe_kolam . '%');
        }

        if ($request->filled('search')) {
            $query->where('nama_kolam', 'like', '%' . $request->search . '%');
        }

        $perPage = $request->input('per_page', 15);
        $kolam = $request->has('all') && $request->boolean('all')
            ? $query->latest()->get()
            : $query->latest()->paginate($perPage);

        return response()->json([
            'status' => 'success',
            'message' => 'Data kolam berhasil diambil',
            'data' => $kolam
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_kolam'       => 'required|string|max:255|unique:kolam,nama_kolam',
            'tipe_kolam'       => 'required|string|max:255',
            'kapasitas'        => 'required|integer|min:1',
            'status'           => 'nullable|string|in:aktif,tidak_aktif,dalam_perawatan',
            'kesehatan_ph_air' => 'nullable|numeric|between:0,14',
            'id_user'          => 'nullable|exists:users,id_user',
        ]);

        $validated['id_user'] = $validated['id_user'] ?? $request->user()->id_user;
        $validated['status'] = $validated['status'] ?? 'aktif';

        $kolam = Kolam::create($validated);
        $kolam->load('user:id_user,nama,email,role');

        return response()->json([
            'status' => 'success',
            'message' => 'Kolam berhasil ditambahkan',
            'data' => $kolam
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $kolam = Kolam::with([
            'user:id_user,nama,email,role',
            'manajemenPakan',
            'batchPembibitan',
            'batchPembesaran'
        ])->find($id);

        if (!$kolam) {
            return response()->json([
                'status' => 'error',
                'message' => 'Kolam tidak ditemukan'
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Detail kolam berhasil diambil',
            'data' => $kolam
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $kolam = Kolam::find($id);

        if (!$kolam) {
            return response()->json([
                'status' => 'error',
                'message' => 'Kolam tidak ditemukan'
            ], 404);
        }

        $validated = $request->validate([
            'nama_kolam'       => 'sometimes|required|string|max:255|unique:kolam,nama_kolam,' . $kolam->id_kolam . ',id_kolam',
            'tipe_kolam'       => 'sometimes|required|string|max:255',
            'kapasitas'        => 'sometimes|required|integer|min:1',
            'status'           => 'nullable|string|in:aktif,tidak_aktif,dalam_perawatan',
            'kesehatan_ph_air' => 'nullable|numeric|between:0,14',
            'id_user'          => 'nullable|exists:users,id_user',
        ]);

        $kolam->update($validated);
        $kolam->load('user:id_user,nama,email,role');

        return response()->json([
            'status' => 'success',
            'message' => 'Data kolam berhasil diperbarui',
            'data' => $kolam
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $kolam = Kolam::find($id);

        if (!$kolam) {
            return response()->json([
                'status' => 'error',
                'message' => 'Kolam tidak ditemukan'
            ], 404);
        }

        $kolam->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Kolam berhasil dihapus'
        ], 200);
    }
}

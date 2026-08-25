<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ManajemenPakan;
use Illuminate\Http\Request;

class ManajemenPakanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = ManajemenPakan::with([
            'kolam:id_kolam,nama_kolam,tipe_kolam',
            'user:id_user,nama,email,role'
        ]);

        if ($request->filled('id_kolam')) {
            $query->where('id_kolam', $request->id_kolam);
        }

        if ($request->filled('start_date')) {
            $query->whereDate('tgl_log', '>=', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $query->whereDate('tgl_log', '<=', $request->end_date);
        }

        $perPage = $request->input('per_page', 15);
        $data = $request->has('all') && $request->boolean('all')
            ? $query->latest('tgl_log')->get()
            : $query->latest('tgl_log')->paginate($perPage);

        return response()->json([
            'status' => 'success',
            'message' => 'Data log pakan berhasil diambil',
            'data' => $data
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'id_kolam'    => 'required|exists:kolam,id_kolam',
            'tgl_log'     => 'required|date',
            'kg_pelet'    => 'nullable|numeric|min:0',
            'kg_daun'     => 'nullable|numeric|min:0',
            'jenis_daun'  => 'nullable|string|max:255',
            'total_biaya' => 'nullable|numeric|min:0',
            'ph_air'      => 'nullable|numeric|between:0,14',
            'id_user'     => 'nullable|exists:users,id_user',
        ]);

        $validated['id_user'] = $validated['id_user'] ?? $request->user()->id_user;
        $validated['kg_pelet'] = $validated['kg_pelet'] ?? 0;
        $validated['kg_daun'] = $validated['kg_daun'] ?? 0;
        $validated['total_biaya'] = $validated['total_biaya'] ?? 0;

        $logPakan = ManajemenPakan::create($validated);
        $logPakan->load(['kolam', 'user:id_user,nama,email,role']);

        return response()->json([
            'status' => 'success',
            'message' => 'Log pakan berhasil dicatat',
            'data' => $logPakan
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $logPakan = ManajemenPakan::with([
            'kolam',
            'user:id_user,nama,email,role'
        ])->find($id);

        if (!$logPakan) {
            return response()->json([
                'status' => 'error',
                'message' => 'Log pakan tidak ditemukan'
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Detail log pakan berhasil diambil',
            'data' => $logPakan
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $logPakan = ManajemenPakan::find($id);

        if (!$logPakan) {
            return response()->json([
                'status' => 'error',
                'message' => 'Log pakan tidak ditemukan'
            ], 404);
        }

        $validated = $request->validate([
            'id_kolam'    => 'sometimes|required|exists:kolam,id_kolam',
            'tgl_log'     => 'sometimes|required|date',
            'kg_pelet'    => 'nullable|numeric|min:0',
            'kg_daun'     => 'nullable|numeric|min:0',
            'jenis_daun'  => 'nullable|string|max:255',
            'total_biaya' => 'nullable|numeric|min:0',
            'ph_air'      => 'nullable|numeric|between:0,14',
            'id_user'     => 'nullable|exists:users,id_user',
        ]);

        $logPakan->update($validated);
        $logPakan->load(['kolam', 'user:id_user,nama,email,role']);

        return response()->json([
            'status' => 'success',
            'message' => 'Log pakan berhasil diperbarui',
            'data' => $logPakan
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $logPakan = ManajemenPakan::find($id);

        if (!$logPakan) {
            return response()->json([
                'status' => 'error',
                'message' => 'Log pakan tidak ditemukan'
            ], 404);
        }

        $logPakan->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Log pakan berhasil dihapus'
        ], 200);
    }
}

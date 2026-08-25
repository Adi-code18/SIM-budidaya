<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PengajuanLibur;
use Illuminate\Http\Request;

class PengajuanLiburController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $user = $request->user();
        $query = PengajuanLibur::with(['user:id_user,nama,email,role']);

        // If not manager, only see own leave requests
        if ($user->role !== 'manajer') {
            $query->where('id_user', $user->id_user);
        } elseif ($request->filled('id_user')) {
            $query->where('id_user', $request->id_user);
        }

        if ($request->filled('status_pengajuan')) {
            $query->where('status_pengajuan', $request->status_pengajuan);
        }

        if ($request->filled('start_date')) {
            $query->whereDate('tanggal_mulai', '>=', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $query->whereDate('tanggal_selesai', '<=', $request->end_date);
        }

        $perPage = $request->input('per_page', 15);
        $data = $query->latest('id_libur')->paginate($perPage);

        return response()->json([
            'status' => 'success',
            'message' => 'Data pengajuan libur berhasil diambil',
            'data' => $data
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'tanggal_mulai'   => 'required|date',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
            'keterangan'      => 'nullable|string|max:1000',
            'id_user'         => 'nullable|exists:users,id_user',
        ]);

        // Default to current user if not provided or if not manager
        if ($request->user()->role !== 'manajer' || empty($validated['id_user'])) {
            $validated['id_user'] = $request->user()->id_user;
        }

        $validated['status_pengajuan'] = 'menunggu'; // default newly submitted leave request

        $pengajuan = PengajuanLibur::create($validated);
        $pengajuan->load('user:id_user,nama,email,role');

        return response()->json([
            'status' => 'success',
            'message' => 'Pengajuan libur berhasil diajukan',
            'data' => $pengajuan
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, string $id)
    {
        $pengajuan = PengajuanLibur::with(['user:id_user,nama,email,role'])->find($id);

        if (!$pengajuan) {
            return response()->json([
                'status' => 'error',
                'message' => 'Data pengajuan libur tidak ditemukan'
            ], 404);
        }

        // Authorization check: only owner or manager
        if ($request->user()->role !== 'manajer' && $pengajuan->id_user !== $request->user()->id_user) {
            return response()->json([
                'status' => 'error',
                'message' => 'Akses ditolak'
            ], 403);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Detail pengajuan libur berhasil diambil',
            'data' => $pengajuan
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $pengajuan = PengajuanLibur::find($id);

        if (!$pengajuan) {
            return response()->json([
                'status' => 'error',
                'message' => 'Data pengajuan libur tidak ditemukan'
            ], 404);
        }

        // Authorization check
        if ($request->user()->role !== 'manajer' && $pengajuan->id_user !== $request->user()->id_user) {
            return response()->json([
                'status' => 'error',
                'message' => 'Akses ditolak'
            ], 403);
        }

        $rules = [
            'tanggal_mulai'   => 'sometimes|required|date',
            'tanggal_selesai' => 'sometimes|required|date|after_or_equal:tanggal_mulai',
            'keterangan'      => 'nullable|string|max:1000',
        ];

        // Only manager can update status_pengajuan
        if ($request->user()->role === 'manajer') {
            $rules['status_pengajuan'] = 'sometimes|required|string|in:menunggu,setuju,ditolak';
        }

        $validated = $request->validate($rules);
        $pengajuan->update($validated);
        $pengajuan->load('user:id_user,nama,email,role');

        return response()->json([
            'status' => 'success',
            'message' => 'Pengajuan libur berhasil diperbarui',
            'data' => $pengajuan
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, string $id)
    {
        $pengajuan = PengajuanLibur::find($id);

        if (!$pengajuan) {
            return response()->json([
                'status' => 'error',
                'message' => 'Data pengajuan libur tidak ditemukan'
            ], 404);
        }

        if ($request->user()->role !== 'manajer' && $pengajuan->id_user !== $request->user()->id_user) {
            return response()->json([
                'status' => 'error',
                'message' => 'Akses ditolak'
            ], 403);
        }

        $pengajuan->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Pengajuan libur berhasil dihapus'
        ], 200);
    }
}

<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Keuangan;
use Illuminate\Http\Request;

class KeuanganController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Keuangan::with([
            'user:id_user,nama,email,role',
            'kolam:id_kolam,nama_kolam'
        ]);

        if ($request->filled('tipe_transaksi')) {
            $query->where('tipe_transaksi', $request->tipe_transaksi);
        }

        if ($request->filled('kategori')) {
            $query->where('kategori', $request->kategori);
        }

        if ($request->filled('id_kolam')) {
            $query->where('id_kolam', $request->id_kolam);
        }

        if ($request->filled('start_date')) {
            $query->whereDate('tanggal_transaksi', '>=', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $query->whereDate('tanggal_transaksi', '<=', $request->end_date);
        }

        $perPage = $request->input('per_page', 15);
        $data = $request->has('all') && $request->boolean('all')
            ? $query->latest('tanggal_transaksi')->get()
            : $query->latest('tanggal_transaksi')->paginate($perPage);

        return response()->json([
            'status' => 'success',
            'message' => 'Data keuangan berhasil diambil',
            'data' => $data
        ], 200);
    }

    /**
     * Summary statistics of financial records (total pemasukan, pengeluaran, saldo).
     */
    public function summary(Request $request)
    {
        $query = Keuangan::query();

        if ($request->filled('start_date')) {
            $query->whereDate('tanggal_transaksi', '>=', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $query->whereDate('tanggal_transaksi', '<=', $request->end_date);
        }

        $totalPemasukan = (clone $query)->where('tipe_transaksi', 'pemasukan')->sum('nominal');
        $totalPengeluaran = (clone $query)->where('tipe_transaksi', 'pengeluaran')->sum('nominal');
        $saldo = $totalPemasukan - $totalPengeluaran;

        return response()->json([
            'status' => 'success',
            'message' => 'Ringkasan keuangan berhasil diambil',
            'data' => [
                'total_pemasukan' => (float) $totalPemasukan,
                'total_pengeluaran' => (float) $totalPengeluaran,
                'saldo' => (float) $saldo
            ]
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'id_kolam'          => 'nullable|exists:kolam,id_kolam',
            'tanggal_transaksi' => 'required|date',
            'tipe_transaksi'    => 'required|string|in:pemasukan,pengeluaran',
            'kategori'          => 'required|string|max:255',
            'nominal'           => 'required|numeric|min:0.01',
            'keterangan'        => 'nullable|string',
            'ref_id'            => 'nullable|string|max:255',
            'id_user'           => 'nullable|exists:users,id_user',
        ]);

        $validated['id_user'] = $validated['id_user'] ?? $request->user()->id_user;

        $keuangan = Keuangan::create($validated);
        $keuangan->load(['user:id_user,nama,email,role', 'kolam:id_kolam,nama_kolam']);

        return response()->json([
            'status' => 'success',
            'message' => 'Catatan keuangan berhasil dibuat',
            'data' => $keuangan
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $keuangan = Keuangan::with([
            'user:id_user,nama,email,role',
            'kolam:id_kolam,nama_kolam'
        ])->find($id);

        if (!$keuangan) {
            return response()->json([
                'status' => 'error',
                'message' => 'Data keuangan tidak ditemukan'
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Detail keuangan berhasil diambil',
            'data' => $keuangan
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $keuangan = Keuangan::find($id);

        if (!$keuangan) {
            return response()->json([
                'status' => 'error',
                'message' => 'Data keuangan tidak ditemukan'
            ], 404);
        }

        $validated = $request->validate([
            'id_kolam'          => 'nullable|exists:kolam,id_kolam',
            'tanggal_transaksi' => 'sometimes|required|date',
            'tipe_transaksi'    => 'sometimes|required|string|in:pemasukan,pengeluaran',
            'kategori'          => 'sometimes|required|string|max:255',
            'nominal'           => 'sometimes|required|numeric|min:0.01',
            'keterangan'        => 'nullable|string',
            'ref_id'            => 'nullable|string|max:255',
            'id_user'           => 'nullable|exists:users,id_user',
        ]);

        $keuangan->update($validated);
        $keuangan->load(['user:id_user,nama,email,role', 'kolam:id_kolam,nama_kolam']);

        return response()->json([
            'status' => 'success',
            'message' => 'Data keuangan berhasil diperbarui',
            'data' => $keuangan
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $keuangan = Keuangan::find($id);

        if (!$keuangan) {
            return response()->json([
                'status' => 'error',
                'message' => 'Data keuangan tidak ditemukan'
            ], 404);
        }

        $keuangan->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Catatan keuangan berhasil dihapus'
        ], 200);
    }
}

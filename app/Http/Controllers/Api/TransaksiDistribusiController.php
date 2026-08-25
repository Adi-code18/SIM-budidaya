<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\TransaksiDistribusi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class TransaksiDistribusiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = TransaksiDistribusi::with([
            'user:id_user,nama,email,role',
            'mitra:id_mitra,nama_mitra,tipe_mitra,alamat',
            'batchPembesaran:id_pembesaran,jenis_ikan,status_siklus'
        ]);

        if ($request->filled('status_order')) {
            $query->where('status_order', $request->status_order);
        }

        if ($request->filled('id_mitra')) {
            $query->where('id_mitra', $request->id_mitra);
        }

        if ($request->filled('id_pembesaran')) {
            $query->where('id_pembesaran', $request->id_pembesaran);
        }

        if ($request->filled('start_date')) {
            $query->whereDate('tanggal_order', '>=', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $query->whereDate('tanggal_order', '<=', $request->end_date);
        }

        $perPage = $request->input('per_page', 15);
        $data = $request->has('all') && $request->boolean('all')
            ? $query->latest('id_transaksi')->get()
            : $query->latest('id_transaksi')->paginate($perPage);

        return response()->json([
            'status' => 'success',
            'message' => 'Data transaksi distribusi berhasil diambil',
            'data' => $data
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'id_mitra'       => 'required|exists:mitra_distributor,id_mitra',
            'id_pembesaran'  => 'required|exists:batch_pembesaran,id_pembesaran',
            'tanggal_order'  => 'required|date',
            'Total_kg'       => 'required|numeric|min:0.01',
            'harga_total'    => 'required|numeric|min:0',
            'status_order'   => 'nullable|string|in:pending,proses,dikirim,selesai,dibatalkan',
            'Jenis_order'    => 'required|string|max:255',
            'Bukti_sampai'   => 'nullable',
            'id_user'        => 'nullable|exists:users,id_user',
        ]);

        $validated['id_user'] = $validated['id_user'] ?? $request->user()->id_user;
        $validated['status_order'] = $validated['status_order'] ?? 'pending';

        if ($request->hasFile('Bukti_sampai')) {
            $path = $request->file('Bukti_sampai')->store('bukti_pengiriman', 'public');
            $validated['Bukti_sampai'] = $path;
        }

        $transaksi = TransaksiDistribusi::create($validated);
        $transaksi->load([
            'user:id_user,nama,email,role',
            'mitra',
            'batchPembesaran'
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Transaksi distribusi berhasil dibuat',
            'data' => $transaksi
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $transaksi = TransaksiDistribusi::with([
            'user:id_user,nama,email,role',
            'mitra',
            'batchPembesaran'
        ])->find($id);

        if (!$transaksi) {
            return response()->json([
                'status' => 'error',
                'message' => 'Transaksi distribusi tidak ditemukan'
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Detail transaksi distribusi berhasil diambil',
            'data' => $transaksi
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $transaksi = TransaksiDistribusi::find($id);

        if (!$transaksi) {
            return response()->json([
                'status' => 'error',
                'message' => 'Transaksi distribusi tidak ditemukan'
            ], 404);
        }

        $validated = $request->validate([
            'id_mitra'       => 'sometimes|required|exists:mitra_distributor,id_mitra',
            'id_pembesaran'  => 'sometimes|required|exists:batch_pembesaran,id_pembesaran',
            'tanggal_order'  => 'sometimes|required|date',
            'Total_kg'       => 'sometimes|required|numeric|min:0.01',
            'harga_total'    => 'sometimes|required|numeric|min:0',
            'status_order'   => 'nullable|string|in:pending,proses,dikirim,selesai,dibatalkan',
            'Jenis_order'    => 'sometimes|required|string|max:255',
            'Bukti_sampai'   => 'nullable',
            'id_user'        => 'nullable|exists:users,id_user',
        ]);

        if ($request->hasFile('Bukti_sampai')) {
            if ($transaksi->Bukti_sampai && Storage::disk('public')->exists($transaksi->Bukti_sampai)) {
                Storage::disk('public')->delete($transaksi->Bukti_sampai);
            }
            $path = $request->file('Bukti_sampai')->store('bukti_pengiriman', 'public');
            $validated['Bukti_sampai'] = $path;
        }

        $transaksi->update($validated);
        $transaksi->load([
            'user:id_user,nama,email,role',
            'mitra',
            'batchPembesaran'
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Transaksi distribusi berhasil diperbarui',
            'data' => $transaksi
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $transaksi = TransaksiDistribusi::find($id);

        if (!$transaksi) {
            return response()->json([
                'status' => 'error',
                'message' => 'Transaksi distribusi tidak ditemukan'
            ], 404);
        }

        if ($transaksi->Bukti_sampai && Storage::disk('public')->exists($transaksi->Bukti_sampai)) {
            Storage::disk('public')->delete($transaksi->Bukti_sampai);
        }

        $transaksi->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Transaksi distribusi berhasil dihapus'
        ], 200);
    }
}

<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\MitraDistributor;
use Illuminate\Http\Request;

class MitraDistributorController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = MitraDistributor::with(['user:id_user,nama,email,role']);

        if ($request->filled('tipe_mitra')) {
            $query->where('tipe_mitra', 'like', '%' . $request->tipe_mitra . '%');
        }

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('nama_mitra', 'like', '%' . $request->search . '%')
                  ->orWhere('alamat', 'like', '%' . $request->search . '%');
            });
        }

        $perPage = $request->input('per_page', 15);
        $data = $request->has('all') && $request->boolean('all')
            ? $query->latest('id_mitra')->get()
            : $query->latest('id_mitra')->paginate($perPage);

        return response()->json([
            'status' => 'success',
            'message' => 'Data mitra distributor berhasil diambil',
            'data' => $data
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_mitra' => 'required|string|max:255',
            'tipe_mitra' => 'required|string|max:255',
            'alamat'     => 'required|string',
            'longitude'  => 'nullable|numeric|between:-180,180',
            'latitude'   => 'nullable|numeric|between:-90,90',
            'id_user'    => 'nullable|exists:users,id_user',
        ]);

        $validated['id_user'] = $validated['id_user'] ?? $request->user()->id_user;

        $mitra = MitraDistributor::create($validated);
        $mitra->load('user:id_user,nama,email,role');

        return response()->json([
            'status' => 'success',
            'message' => 'Mitra distributor berhasil ditambahkan',
            'data' => $mitra
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $mitra = MitraDistributor::with([
            'user:id_user,nama,email,role',
            'transaksiDistribusi'
        ])->find($id);

        if (!$mitra) {
            return response()->json([
                'status' => 'error',
                'message' => 'Mitra distributor tidak ditemukan'
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Detail mitra distributor berhasil diambil',
            'data' => $mitra
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $mitra = MitraDistributor::find($id);

        if (!$mitra) {
            return response()->json([
                'status' => 'error',
                'message' => 'Mitra distributor tidak ditemukan'
            ], 404);
        }

        $validated = $request->validate([
            'nama_mitra' => 'sometimes|required|string|max:255',
            'tipe_mitra' => 'sometimes|required|string|max:255',
            'alamat'     => 'sometimes|required|string',
            'longitude'  => 'nullable|numeric|between:-180,180',
            'latitude'   => 'nullable|numeric|between:-90,90',
            'id_user'    => 'nullable|exists:users,id_user',
        ]);

        $mitra->update($validated);
        $mitra->load('user:id_user,nama,email,role');

        return response()->json([
            'status' => 'success',
            'message' => 'Data mitra distributor berhasil diperbarui',
            'data' => $mitra
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $mitra = MitraDistributor::find($id);

        if (!$mitra) {
            return response()->json([
                'status' => 'error',
                'message' => 'Mitra distributor tidak ditemukan'
            ], 404);
        }

        $mitra->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Mitra distributor berhasil dihapus'
        ], 200);
    }
}

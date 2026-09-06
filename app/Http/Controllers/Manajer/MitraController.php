<?php

namespace App\Http\Controllers\Manajer;

use App\Http\Controllers\Controller;
use App\Models\MitraDistributor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class MitraController extends Controller
{
    public function index()
    {
        $mitraRecords = MitraDistributor::with(['user', 'transaksiDistribusi'])
            ->orderBy('id_mitra', 'desc')
            ->get();

        $mitras = [];
        $images = [
            'https://images.unsplash.com/photo-1555396273-367ea4eb4db5?auto=format&fit=crop&q=80&w=120',
            'https://images.unsplash.com/photo-1586528116311-ad8dd3c8310d?auto=format&fit=crop&q=80&w=120',
            'https://images.unsplash.com/photo-1541888946425-d0fbb186a5b3?auto=format&fit=crop&q=80&w=120'
        ];

        foreach ($mitraRecords as $idx => $m) {
            $tipeKey = strtolower(explode(' ', $m->tipe_mitra)[0] ?? 'distributor');

            $mitras[] = [
                'id_mitra'  => $m->id_mitra,
                'id'        => 'MTR-2024-' . str_pad($m->id_mitra, 3, '0', STR_PAD_LEFT),
                'nama'      => $m->nama_mitra,
                'tipe'      => $m->tipe_mitra,
                'tipeKey'   => $tipeKey,
                'alamat'    => $m->alamat,
                'wilayah'   => 'indonesia',
                'lat'       => (string) ($m->latitude ?? -6.208800),
                'lng'       => (string) ($m->longitude ?? 106.845600),
                'kontak'    => '+62 812-3456-7890',
                'email'     => 'contact@' . strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $m->nama_mitra)) . '.id',
                'image'     => $images[$idx % count($images)]
            ];
        }

        return view('layouts.mitra.index', compact('mitras'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama'      => 'required|string|max:255',
            'tipe'      => 'required|string|max:100',
            'tipeKey'   => 'nullable|string|max:50',
            'alamat'    => 'required|string',
            'lat'       => 'nullable|numeric',
            'lng'       => 'nullable|numeric',
            'kontak'    => 'nullable|string|max:50',
            'email'     => 'nullable|string|max:100',
        ]);

        $userId = Auth::user()->id_user ?? 1;

        $mitra = MitraDistributor::create([
            'id_user'    => $userId,
            'nama_mitra' => $validated['nama'],
            'tipe_mitra' => $validated['tipe'],
            'alamat'     => $validated['alamat'],
            'latitude'   => $validated['lat'] ?? null,
            'longitude'  => $validated['lng'] ?? null,
        ]);

        $tipeKey = strtolower(explode(' ', $mitra->tipe_mitra)[0] ?? 'distributor');

        $formatted = [
            'id_mitra'  => $mitra->id_mitra,
            'id'        => 'MTR-2024-' . str_pad($mitra->id_mitra, 3, '0', STR_PAD_LEFT),
            'nama'      => $mitra->nama_mitra,
            'tipe'      => $mitra->tipe_mitra,
            'tipeKey'   => $tipeKey,
            'alamat'    => $mitra->alamat,
            'wilayah'   => 'indonesia',
            'lat'       => (string) ($mitra->latitude ?? -6.208800),
            'lng'       => (string) ($mitra->longitude ?? 106.845600),
            'kontak'    => $request->kontak ?: '+62 812-3456-7890',
            'email'     => $request->email ?: ('contact@' . strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $mitra->nama_mitra)) . '.id'),
            'image'     => 'https://images.unsplash.com/photo-1555396273-367ea4eb4db5?auto=format&fit=crop&q=80&w=120'
        ];

        return response()->json([
            'success' => true,
            'message' => 'Mitra baru berhasil disimpan ke database!',
            'data'    => $formatted
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $mitra = MitraDistributor::findOrFail($id);

        $validated = $request->validate([
            'nama'      => 'required|string|max:255',
            'tipe'      => 'required|string|max:100',
            'tipeKey'   => 'nullable|string|max:50',
            'alamat'    => 'required|string',
            'lat'       => 'nullable|numeric',
            'lng'       => 'nullable|numeric',
            'kontak'    => 'nullable|string|max:50',
            'email'     => 'nullable|string|max:100',
        ]);

        $mitra->update([
            'nama_mitra' => $validated['nama'],
            'tipe_mitra' => $validated['tipe'],
            'alamat'     => $validated['alamat'],
            'latitude'   => $validated['lat'] ?? null,
            'longitude'  => $validated['lng'] ?? null,
        ]);

        $tipeKey = strtolower(explode(' ', $mitra->tipe_mitra)[0] ?? 'distributor');

        $formatted = [
            'id_mitra'  => $mitra->id_mitra,
            'id'        => 'MTR-2024-' . str_pad($mitra->id_mitra, 3, '0', STR_PAD_LEFT),
            'nama'      => $mitra->nama_mitra,
            'tipe'      => $mitra->tipe_mitra,
            'tipeKey'   => $tipeKey,
            'alamat'    => $mitra->alamat,
            'wilayah'   => 'indonesia',
            'lat'       => (string) ($mitra->latitude ?? -6.208800),
            'lng'       => (string) ($mitra->longitude ?? 106.845600),
            'kontak'    => $request->kontak ?: '+62 812-3456-7890',
            'email'     => $request->email ?: ('contact@' . strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $mitra->nama_mitra)) . '.id'),
            'image'     => 'https://images.unsplash.com/photo-1555396273-367ea4eb4db5?auto=format&fit=crop&q=80&w=120'
        ];

        return response()->json([
            'success' => true,
            'message' => 'Data mitra berhasil diperbarui di database!',
            'data'    => $formatted
        ]);
    }

    public function destroy($id)
    {
        $mitra = MitraDistributor::findOrFail($id);
        $mitraName = $mitra->nama_mitra;
        $mitra->delete();

        return response()->json([
            'success' => true,
            'message' => "Data mitra '{$mitraName}' berhasil dihapus dari database!"
        ]);
    }
}

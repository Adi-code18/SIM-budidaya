<?php

namespace App\Http\Controllers\Manajer;

use App\Http\Controllers\Controller;
use App\Models\MitraDistributor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

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
            $tipeKey = $this->getTipeKey($m->tipe_mitra);
            $kategori = $this->getKategori($tipeKey);

            $mitras[] = [
                'id_mitra'    => $m->id_mitra,
                'id'          => 'MTR-2024-' . str_pad($m->id_mitra, 3, '0', STR_PAD_LEFT),
                'nama'        => $m->nama_mitra,
                'tipe'        => $m->tipe_mitra,
                'tipeKey'     => $tipeKey,
                'kategori'    => $kategori,
                'logo_mitra'  => $m->logo_mitra,
                'alamat'      => $m->alamat,
                'wilayah'     => 'indonesia',
                'lat'         => (string) ($m->latitude ?? -6.208800),
                'lng'         => (string) ($m->longitude ?? 106.845600),
                'kontak'      => '+62 812-3456-7890',
                'email'       => 'contact@' . strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $m->nama_mitra)) . '.id',
                'image'       => $m->logo_url
            ];
        }

        return view('layouts.mitra.index', compact('mitras'));
    }

    private function getTipeKey(?string $tipe): string
    {
        $raw = strtolower(trim($tipe ?? ''));
        if (str_contains($raw, 'bibit') || str_contains($raw, 'benih') || str_contains($raw, 'hatchery')) {
            return 'supplier_bibit';
        }
        if (str_contains($raw, 'pakan') || str_contains($raw, 'pelet') || str_contains($raw, 'supplier')) {
            return 'supplier_pakan';
        }
        if (str_contains($raw, 'rumah') || str_contains($raw, 'makan') || str_contains($raw, 'warung') || str_starts_with($raw, 'rm ') || str_starts_with($raw, 'rm.')) {
            return 'rumah_makan';
        }
        if (str_contains($raw, 'pasar')) {
            return 'pasar';
        }
        if (str_contains($raw, 'eksportir') || str_contains($raw, 'ekspor')) {
            return 'eksportir';
        }
        if (str_contains($raw, 'resto') || str_contains($raw, 'cafe') || str_contains($raw, 'kafe')) {
            return 'restoran';
        }
        return 'restoran';
    }

    private function getKategori(?string $tipeKey): string
    {
        if (in_array($tipeKey, ['supplier_pakan', 'supplier_bibit', 'supplier'])) {
            return 'supplier';
        }
        return 'client';
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama'        => 'required|string|max:255',
            'tipe'        => 'required|string|max:100',
            'tipeKey'     => 'nullable|string|max:50',
            'logo_mitra'  => 'nullable|string',
            'logo_file'   => 'nullable|image|mimes:jpeg,png,jpg,webp,svg|max:3072',
            'alamat'      => 'required|string',
            'lat'         => 'nullable|numeric',
            'lng'         => 'nullable|numeric',
            'kontak'      => [
                'nullable',
                'string',
                'max:50',
                function ($attribute, $value, $fail) {
                    if (!empty($value)) {
                        $digits = preg_replace('/[^0-9]/', '', $value);
                        if (strlen($digits) < 7 || strlen($digits) > 15) {
                            $fail('Nomor telepon tidak valid! Sesuai aturan ITU, format nomor internasional resmi harus berukuran 7 hingga 15 digit angka (termasuk kode negara).');
                        }
                    }
                },
            ],
            'email'       => 'nullable|string|max:100',
        ]);

        $userId = Auth::user()->id_user ?? 1;

        $logoPath = $validated['logo_mitra'] ?? null;
        if ($request->hasFile('logo_file')) {
            $logoPath = $request->file('logo_file')->store('mitra', 'public');
        }

        $mitra = MitraDistributor::create([
            'id_user'    => $userId,
            'nama_mitra' => $validated['nama'],
            'tipe_mitra' => $validated['tipe'],
            'logo_mitra' => $logoPath,
            'alamat'     => $validated['alamat'],
            'latitude'   => $validated['lat'] ?? null,
            'longitude'  => $validated['lng'] ?? null,
        ]);

        $tipeKey = $this->getTipeKey($mitra->tipe_mitra);
        $kategori = $this->getKategori($tipeKey);

        $formatted = [
            'id_mitra'    => $mitra->id_mitra,
            'id'          => 'MTR-2024-' . str_pad($mitra->id_mitra, 3, '0', STR_PAD_LEFT),
            'nama'        => $mitra->nama_mitra,
            'tipe'        => $mitra->tipe_mitra,
            'tipeKey'     => $tipeKey,
            'kategori'    => $kategori,
            'logo_mitra'  => $mitra->logo_mitra,
            'alamat'      => $mitra->alamat,
            'wilayah'     => 'indonesia',
            'lat'         => (string) ($mitra->latitude ?? -6.208800),
            'lng'         => (string) ($mitra->longitude ?? 106.845600),
            'kontak'      => $request->kontak ?: '+62 812-3456-7890',
            'email'       => $request->email ?: ('contact@' . strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $mitra->nama_mitra)) . '.id'),
            'image'       => $mitra->logo_url
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
            'nama'        => 'required|string|max:255',
            'tipe'        => 'required|string|max:100',
            'tipeKey'     => 'nullable|string|max:50',
            'logo_mitra'  => 'nullable|string',
            'logo_file'   => 'nullable|image|mimes:jpeg,png,jpg,webp,svg|max:3072',
            'hapus_logo'  => 'nullable|boolean',
            'alamat'      => 'required|string',
            'lat'         => 'nullable|numeric',
            'lng'         => 'nullable|numeric',
            'kontak'      => [
                'nullable',
                'string',
                'max:50',
                function ($attribute, $value, $fail) {
                    if (!empty($value)) {
                        $digits = preg_replace('/[^0-9]/', '', $value);
                        if (strlen($digits) < 7 || strlen($digits) > 15) {
                            $fail('Nomor telepon tidak valid! Sesuai aturan ITU, format nomor internasional resmi harus berukuran 7 hingga 15 digit angka (termasuk kode negara).');
                        }
                    }
                },
            ],
            'email'       => 'nullable|string|max:100',
        ]);

        $logoPath = $mitra->logo_mitra;
        if ($request->hasFile('logo_file')) {
            if ($mitra->logo_mitra && Storage::disk('public')->exists($mitra->logo_mitra)) {
                Storage::disk('public')->delete($mitra->logo_mitra);
            }
            $logoPath = $request->file('logo_file')->store('mitra', 'public');
        } elseif ($request->boolean('hapus_logo')) {
            if ($mitra->logo_mitra && Storage::disk('public')->exists($mitra->logo_mitra)) {
                Storage::disk('public')->delete($mitra->logo_mitra);
            }
            $logoPath = null;
        } elseif ($request->filled('logo_mitra')) {
            $logoPath = $validated['logo_mitra'];
        }

        $mitra->update([
            'nama_mitra' => $validated['nama'],
            'tipe_mitra' => $validated['tipe'],
            'logo_mitra' => $logoPath,
            'alamat'     => $validated['alamat'],
            'latitude'   => $validated['lat'] ?? null,
            'longitude'  => $validated['lng'] ?? null,
        ]);

        $tipeKey = $this->getTipeKey($mitra->tipe_mitra);
        $kategori = $this->getKategori($tipeKey);

        $formatted = [
            'id_mitra'    => $mitra->id_mitra,
            'id'          => 'MTR-2024-' . str_pad($mitra->id_mitra, 3, '0', STR_PAD_LEFT),
            'nama'        => $mitra->nama_mitra,
            'tipe'        => $mitra->tipe_mitra,
            'tipeKey'     => $tipeKey,
            'kategori'    => $kategori,
            'logo_mitra'  => $mitra->logo_mitra,
            'alamat'      => $mitra->alamat,
            'wilayah'     => 'indonesia',
            'lat'         => (string) ($mitra->latitude ?? -6.208800),
            'lng'         => (string) ($mitra->longitude ?? 106.845600),
            'kontak'      => $request->kontak ?: '+62 812-3456-7890',
            'email'       => $request->email ?: ('contact@' . strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $mitra->nama_mitra)) . '.id'),
            'image'       => $mitra->logo_url
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

        if ($mitra->logo_mitra && Storage::disk('public')->exists($mitra->logo_mitra)) {
            Storage::disk('public')->delete($mitra->logo_mitra);
        }

        $mitra->delete();

        return response()->json([
            'success' => true,
            'message' => "Data mitra '{$mitraName}' berhasil dihapus dari database!"
        ]);
    }
}

<?php

namespace App\Http\Controllers\Petugas;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class PetugasProfileController extends Controller
{
    /**
     * Update Profil Petugas Lapangan (Pembibitan, Pembesaran, Distribusi)
     */
    public function update(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'nama'        => 'required|string|max:255',
            'no_tlp'      => 'nullable|string|max:30',
            'foto_profil' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ], [
            'nama.required'     => 'Nama lengkap wajib diisi.',
            'foto_profil.image' => 'File harus berupa foto/gambar.',
            'foto_profil.mimes' => 'Format foto harus jpeg, png, jpg, atau webp.',
            'foto_profil.max'   => 'Ukuran foto maksimal 2MB.',
        ]);

        if ($request->hasFile('foto_profil')) {
            if ($user->foto_profil && Storage::disk('public')->exists($user->foto_profil)) {
                Storage::disk('public')->delete($user->foto_profil);
            }
            $user->foto_profil = $request->file('foto_profil')->store('profil', 'public');
        } elseif ($request->boolean('hapus_foto')) {
            if ($user->foto_profil && Storage::disk('public')->exists($user->foto_profil)) {
                Storage::disk('public')->delete($user->foto_profil);
            }
            $user->foto_profil = null;
        }

        $user->nama = $request->nama;
        if ($request->filled('no_tlp')) {
            $user->no_tlp = $request->no_tlp;
        }
        $user->save();

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Profil dan foto berhasil diperbarui!',
                'user' => [
                    'nama' => $user->nama,
                    'no_tlp' => $user->no_tlp,
                    'foto_profil_url' => $user->foto_profil_url,
                ]
            ]);
        }

        return back()->with('success', 'Profil dan foto berhasil diperbarui!');
    }
}

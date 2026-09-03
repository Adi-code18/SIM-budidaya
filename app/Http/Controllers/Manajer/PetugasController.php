<?php

namespace App\Http\Controllers\Manajer;

use App\Http\Controllers\Controller;
use App\Models\BatchPembesaran;
use App\Models\BatchPembibitan;
use App\Models\Kolam;
use App\Models\ManajemenPakan;
use App\Models\TransaksiDistribusi;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class PetugasController extends Controller
{
    public function index()
    {
        $users = User::all();
        return view('layouts.petugas.index', compact('users'));
    }

    public function create()
    {
        return view('layouts.petugas.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|min:6',
            'role'     => 'required|in:manajer,pembibitan,pembesaran,petugas_distribusi',
            'no_tlp'   => 'nullable|phone:AUTO,ID',
        ], [
            'no_tlp.phone' => 'Format nomor telepon/HP tidak valid (contoh: 081234567890 atau +6281234567890).',
        ]);

        $noTlp = $request->no_tlp ?? '081234567890';
        if ($request->filled('no_tlp')) {
            try {
                $noTlp = phone($request->no_tlp, 'ID')->formatNational();
            } catch (\Exception $e) {
                $noTlp = $request->no_tlp;
            }
        }

        $user = User::create([
            'nama'     => $request->nama,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
            'role'     => $request->role,
            'no_tlp'   => $noTlp,
        ]);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => "Data petugas {$user->nama} berhasil ditambahkan!",
                'user'    => $user
            ]);
        }

        return redirect()->route('petugas')->with('success', "Data petugas {$user->nama} berhasil ditambahkan!");
    }

    public function edit($id)
    {
        $user = User::findOrFail($id);
        return view('layouts.petugas.edit', compact('user'));
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'nama'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email,' . $user->id_user . ',id_user',
            'password' => 'nullable|min:6',
            'role'     => 'required|in:manajer,pembibitan,pembesaran,petugas_distribusi',
            'no_tlp'   => 'nullable|phone:AUTO,ID',
        ], [
            'no_tlp.phone' => 'Format nomor telepon/HP tidak valid (contoh: 081234567890 atau +6281234567890).',
        ]);

        $noTlp = $user->no_tlp;
        if ($request->filled('no_tlp')) {
            try {
                $noTlp = phone($request->no_tlp, 'ID')->formatNational();
            } catch (\Exception $e) {
                $noTlp = $request->no_tlp;
            }
        }

        $data = [
            'nama'   => $request->nama,
            'email'  => $request->email,
            'role'   => $request->role,
            'no_tlp' => $noTlp,
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => "Data petugas {$user->nama} berhasil diperbarui!",
                'user'    => $user
            ]);
        }

        return redirect()->route('petugas')->with('success', "Data petugas {$user->nama} berhasil diperbarui!");
    }

    public function destroy($id)
    {
        $user = User::find($id);

        if (!$user) {
            if (request()->wantsJson() || request()->ajax()) {
                return response()->json(['success' => false, 'message' => 'Data petugas tidak ditemukan.'], 404);
            }
            return redirect()->route('petugas')->with('error', 'Data petugas tidak ditemukan.');
        }

        // Cegah menghapus akun yang sedang login
        if (Auth::id() && Auth::id() == $user->id_user) {
            if (request()->wantsJson() || request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tidak dapat menghapus akun Anda sendiri yang sedang aktif digunakan!'
                ], 422);
            }
            return redirect()->route('petugas')->with('error', 'Tidak dapat menghapus akun Anda sendiri yang sedang aktif digunakan!');
        }

        $nama = $user->nama;

        // Lepas foreign key references agar tidak error constraint
        try {
            Kolam::where('id_user', $id)->update(['id_user' => null]);
            BatchPembesaran::where('id_user', $id)->update(['id_user' => null]);
            BatchPembibitan::where('id_user', $id)->update(['id_user' => null]);
            ManajemenPakan::where('id_user', $id)->update(['id_user' => null]);
            TransaksiDistribusi::where('id_user', $id)->update(['id_user' => null]);

            $user->delete();

            if (request()->wantsJson() || request()->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => "Data petugas \"{$nama}\" berhasil dihapus dari database!"
                ]);
            }

            return redirect()->route('petugas')->with('success', "Data petugas \"{$nama}\" berhasil dihapus!");
        } catch (\Exception $e) {
            if (request()->wantsJson() || request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal menghapus data petugas: ' . $e->getMessage()
                ], 500);
            }
            return redirect()->route('petugas')->with('error', 'Gagal menghapus data petugas: ' . $e->getMessage());
        }
    }

    public function updatePassword(Request $request, $id)
    {
        $request->validate([
            'password'              => 'required|string|min:6|confirmed',
            'password_confirmation' => 'required|string|min:6',
        ], [
            'password.required'  => 'Password baru wajib diisi.',
            'password.min'       => 'Password minimal harus 6 karakter.',
            'password.confirmed' => 'Konfirmasi password baru tidak cocok.',
        ]);

        $user = User::findOrFail($id);
        $user->password = Hash::make($request->password);
        $user->save();

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => "Password akun petugas \"{$user->nama}\" berhasil diperbarui!"
            ]);
        }

        return redirect()->route('petugas')->with('success', "Password akun petugas \"{$user->nama}\" berhasil diperbarui!");
    }

    public function reset2fa(Request $request, $id)
    {
        $user = User::findOrFail($id);
        $user->two_factor_secret = null;
        $user->two_factor_confirmed_at = null;
        $user->save();

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => "Google Authenticator 2FA untuk \"{$user->nama}\" berhasil dinonaktifkan/direset!"
            ]);
        }

        return redirect()->route('petugas')->with('success', "Google Authenticator 2FA untuk \"{$user->nama}\" berhasil direset!");
    }
}

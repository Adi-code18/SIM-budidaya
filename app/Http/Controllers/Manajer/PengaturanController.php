<?php

namespace App\Http\Controllers\Manajer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class PengaturanController extends Controller
{
    /**
     * Tampilkan Halaman Pengaturan Utama
     */
    public function index()
    {
        $user = Auth::user();

        // Data dummy atau default preferensi sistem budidaya
        $settings = [
            'nama_tambak' => config('app.name', 'SIM-BUDIDAYA Aquafarm'),
            'email_notifikasi' => $user->email ?? 'manajer@simbudidaya.id',
            'notif_wa' => true,
            'notif_email' => true,
            'threshold_kematian' => 3000,
            'target_fcr' => 1.25,
            'satuan_berat' => 'kg',
            'mode_sistem' => 'otomatis',
        ];

        return view('layouts.pengaturan.index', compact('user', 'settings'));
    }

    /**
     * Update Profil & Keamanan Pengguna
     */
    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'nama' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $user->id_user . ',id_user',
            'no_tlp' => 'nullable|phone:AUTO,ID',
            'password_saat_ini' => 'nullable|required_with:password_baru',
            'password_baru' => ['nullable', 'confirmed', Password::min(6)],
        ], [
            'nama.required' => 'Nama pengguna wajib diisi.',
            'email.required' => 'Email wajib diisi.',
            'email.unique' => 'Email sudah digunakan oleh pengguna lain.',
            'no_tlp.phone' => 'Format nomor telepon/WhatsApp tidak valid (contoh: 081234567890 atau +6281234567890).',
            'password_saat_ini.required_with' => 'Password saat ini wajib diisi untuk menginstal password baru.',
            'password_baru.confirmed' => 'Konfirmasi password baru tidak cocok.',
            'password_baru.min' => 'Password baru minimal 6 karakter.',
        ]);

        // Verifikasi password saat ini jika ganti password
        if ($request->filled('password_baru')) {
            if (!Hash::check($request->password_saat_ini, $user->password)) {
                return back()->withErrors(['password_saat_ini' => 'Password saat ini tidak sesuai.'])->withInput();
            }
            $user->password = Hash::make($request->password_baru);
        }

        $noTlp = $user->no_tlp;
        if ($request->filled('no_tlp')) {
            try {
                $noTlp = phone($request->no_tlp, 'ID')->formatNational();
            } catch (\Exception $e) {
                $noTlp = $request->no_tlp;
            }
        }

        $user->nama = $request->nama;
        $user->email = $request->email;
        $user->no_tlp = $noTlp;
        $user->save();

        return back()->with('status', 'Profil & Keamanan berhasil diperbarui!');
    }

    /**
     * Update Preferensi Sistem Budidaya
     */
    public function updatePreferences(Request $request)
    {
        // Dalam implementasi nyata ini bisa disimpan ke database atau file config
        return back()->with('status', 'Preferensi & Notifikasi Budidaya berhasil disimpan!');
    }
}

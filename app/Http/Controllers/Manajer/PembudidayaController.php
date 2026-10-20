<?php

namespace App\Http\Controllers\Manajer;

use App\Http\Controllers\Controller;
use App\Models\BatchPembesaran;
use App\Models\BatchPembibitan;
use App\Models\Keuangan;
use App\Models\Kolam;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PembudidayaController extends Controller
{
    /**
     * Tampilan Utama Manajemen & Data Kolam Budidaya.
     */
    public function index()
    {
        $kolamRecords = Kolam::with(['user', 'batchPembesaran', 'batchPembibitan'])->get();

        $kolams = [];
        $colorClasses = [
            'bg-sky-100 text-sky-700',
            'bg-amber-100 text-amber-700',
            'bg-purple-100 text-purple-700',
            'bg-emerald-100 text-emerald-700'
        ];

        foreach ($kolamRecords as $idx => $k) {
            $activePembesaran = $k->batchPembesaran()
                ->where('status_siklus', '!=', 'selesai')
                ->where('status_siklus', '!=', 'gagal')
                ->latest('id_pembesaran')
                ->first();

            $activePembibitan = $k->batchPembibitan()
                ->where('status', '!=', 'selesai')
                ->where('status', '!=', 'gagal')
                ->latest('id_batch')
                ->first();

            $pembudidaya = $k->user ? $k->user->nama : 'Petugas Budidaya';
            $words = explode(' ', $pembudidaya);
            $initials = strtoupper(substr($words[0] ?? 'P', 0, 1) . substr($words[1] ?? ($words[0] ?? 'B'), 0, 1));

            if ($activePembesaran) {
                $status = ($activePembesaran->status_siklus === 'siap_panen') ? 'Siap Panen' : 'Terisi (Pembesaran)';
                $statusClass = ($activePembesaran->status_siklus === 'siap_panen') ? 'bg-amber-100 text-amber-800' : 'bg-emerald-100 text-emerald-800';
                $dotClass = ($activePembesaran->status_siklus === 'siap_panen') ? 'bg-amber-500' : 'bg-emerald-500';
                $jenisIkan = $activePembesaran->jenis_ikan;
                $tebarBenih = $activePembesaran->tgl_tebar ? Carbon::parse($activePembesaran->tgl_tebar)->translatedFormat('d M Y') : '-';
                $populasi = number_format($activePembesaran->biomassa_est ?: 0, 1, ',', '.') . ' kg';
                $populasiRaw = (float) $activePembesaran->biomassa_est;
            } elseif ($activePembibitan) {
                $benihHidup = max(0, ((int) $activePembibitan->jumlah_bibitAwal) - ((int) $activePembibitan->jumlah_kematian));
                $status = 'Terisi (Pembibitan)';
                $statusClass = 'bg-sky-100 text-sky-800';
                $dotClass = 'bg-sky-500';
                $jenisIkan = $activePembibitan->jenis_ikan ?? ($activePembibitan->ikan ? $activePembibitan->ikan->nama_ikan : 'Bibit Ikan');
                $tebarBenih = $activePembibitan->tgl_pemijahan ? Carbon::parse($activePembibitan->tgl_pemijahan)->translatedFormat('d M Y') : '-';
                $populasi = number_format($benihHidup, 0, ',', '.') . ' ekor';
                $populasiRaw = $benihHidup;
            } else {
                // Kolam Kosong / Belum Diisi
                $status = 'Kosong / Tersedia';
                $statusClass = 'bg-slate-100 text-slate-600';
                $dotClass = 'bg-slate-400';
                $jenisIkan = 'Kosong (Belum Diisi)';
                $tebarBenih = '-';
                $populasi = '0 (Kapasitas: ' . number_format($k->kapasitas, 0, ',', '.') . ')';
                $populasiRaw = 0;
            }

            $kolams[] = [
                'id_kolam'         => $k->id_kolam,
                'id'               => $k->nama_kolam,
                'nama_kolam'       => $k->nama_kolam,
                'lokasi'           => $k->tipe_kolam,
                'tipe_kolam'       => $k->tipe_kolam,
                'kapasitas'        => (int) $k->kapasitas,
                'kesehatan_ph_air' => (float) ($k->kesehatan_ph_air ?? 7.20),
                'biaya_pembuatan'  => (float) ($k->biaya_pembuatan ?? 0),
                'catatan'          => $k->catatan,
                'id_user'          => $k->id_user,
                'pembudidaya'      => $pembudidaya,
                'initials'         => $initials,
                'colorClass'       => $colorClasses[$idx % count($colorClasses)],
                'jenisIkan'        => $jenisIkan,
                'tebarBenih'       => $tebarBenih,
                'populasi'         => $populasi,
                'populasiRaw'      => $populasiRaw,
                'status'           => $status,
                'statusClass'      => $statusClass,
                'dotClass'         => $dotClass
            ];
        }

        // Statistik Ringkasan Nyata dari Database
        $totalKapasitas = Kolam::sum('kapasitas');
        $totalInvestasi = Kolam::sum('biaya_pembuatan');
        $totalBenihAktif = BatchPembibitan::where('status', '!=', 'selesai')->where('status', '!=', 'gagal')->get()->sum(function($b) {
            return max(0, $b->jumlah_bibitAwal - $b->jumlah_kematian);
        });

        $totalAwalBibit = BatchPembibitan::sum('jumlah_bibitAwal');
        $totalMatiBibit = BatchPembibitan::sum('jumlah_kematian');
        $keberhasilanRate = $totalAwalBibit > 0 ? round((($totalAwalBibit - $totalMatiBibit) / $totalAwalBibit) * 100, 1) : 100.0;

        $kolamSiapPanenCount = BatchPembesaran::whereIn('status_siklus', ['siap_panen', 'selesai'])->count();
        $totalKolamCount = Kolam::count();

        $stats = [
            'totalBenih'          => number_format($totalBenihAktif, 0, ',', '.'),
            'keberhasilanRate'    => $keberhasilanRate,
            'siapPanenCount'      => $kolamSiapPanenCount,
            'totalKolamCount'     => $totalKolamCount,
            'totalKapasitas'      => number_format($totalKapasitas, 0, ',', '.'),
            'totalInvestasiRp'    => 'Rp ' . number_format($totalInvestasi, 0, ',', '.'),
            'totalInvestasiRaw'   => $totalInvestasi,
        ];

        $petugasList = User::orderBy('nama', 'asc')->get();

        return view('layouts.pembudidaya.index', compact('kolams', 'stats', 'petugasList'));
    }

    /**
     * Tambah Kolam Baru & Otomatis Bukukan Biaya Investasi ke Laporan Keuangan.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nama_kolam'       => 'required|string|max:255|unique:kolam,nama_kolam',
            'tipe_kolam'       => 'required|string|max:255',
            'kapasitas'        => 'required|integer|min:1',
            'kesehatan_ph_air' => 'nullable|numeric|between:0,14',
            'id_user'          => 'nullable|exists:users,id_user',
            'biaya_pembuatan'  => 'nullable|numeric|min:0',
            'tgl_pembuatan'    => 'nullable|date',
            'catatan'          => 'nullable|string',
        ], [
            'nama_kolam.unique' => 'Nama / ID Kolam ini sudah terdaftar! Gunakan penamaan kolam lain.',
            'kapasitas.min'     => 'Kapasitas kolam minimal 1 ekor.',
        ]);

        $biaya = (float) ($request->biaya_pembuatan ?? 0);
        $userId = $request->id_user ?? Auth::id() ?? 1;

        $kolam = Kolam::create([
            'id_user'          => $userId,
            'nama_kolam'       => $request->nama_kolam,
            'tipe_kolam'       => $request->tipe_kolam,
            'kapasitas'        => (int) $request->kapasitas,
            'status'           => 'aktif',
            'kesehatan_ph_air' => $request->kesehatan_ph_air ?? 7.20,
            'biaya_pembuatan'  => $biaya,
            'catatan'          => $request->catatan,
        ]);

        // JANGAN SAMPAI JADI PENGELUARAN GAIB:
        // Jika ada biaya pembuatan kolam, otomatis catat transaksi pengeluaran ke Laporan Keuangan!
        if ($biaya > 0) {
            $tglTrx = $request->tgl_pembuatan ? Carbon::parse($request->tgl_pembuatan)->toDateString() : now()->toDateString();
            $refId = 'KOLAM-INV-' . str_pad($kolam->id_kolam, 4, '0', STR_PAD_LEFT);

            Keuangan::create([
                'id_user'           => Auth::id() ?? $userId,
                'id_kolam'          => $kolam->id_kolam,
                'tanggal_transaksi' => $tglTrx,
                'tipe_transaksi'    => 'pengeluaran',
                'kategori'          => 'Pembangunan Kolam',
                'nominal'           => $biaya,
                'keterangan'        => "Biaya pembuatan/investasi kolam baru {$kolam->nama_kolam} ({$kolam->tipe_kolam}, kapasitas " . number_format($kolam->kapasitas, 0, ',', '.') . " ekor)",
                'ref_id'            => $refId,
            ]);
        }

        $user = User::find($userId);
        $pembudidaya = $user ? $user->nama : 'Petugas Budidaya';
        $words = explode(' ', $pembudidaya);
        $initials = strtoupper(substr($words[0] ?? 'P', 0, 1) . substr($words[1] ?? ($words[0] ?? 'B'), 0, 1));

        $itemData = [
            'id_kolam'         => $kolam->id_kolam,
            'id'               => $kolam->nama_kolam,
            'nama_kolam'       => $kolam->nama_kolam,
            'lokasi'           => $kolam->tipe_kolam,
            'tipe_kolam'       => $kolam->tipe_kolam,
            'kapasitas'        => (int) $kolam->kapasitas,
            'kesehatan_ph_air' => (float) ($kolam->kesehatan_ph_air ?? 7.20),
            'biaya_pembuatan'  => $biaya,
            'catatan'          => $kolam->catatan,
            'id_user'          => $kolam->id_user,
            'pembudidaya'      => $pembudidaya,
            'initials'         => $initials,
            'colorClass'       => 'bg-sky-100 text-sky-700',
            'jenisIkan'        => 'Kosong (Belum Diisi)',
            'tebarBenih'       => '-',
            'populasi'         => '0 (Kapasitas: ' . number_format($kolam->kapasitas, 0, ',', '.') . ')',
            'populasiRaw'      => 0,
            'status'           => 'Kosong / Tersedia',
            'statusClass'      => 'bg-slate-100 text-slate-600',
            'dotClass'         => 'bg-slate-400'
        ];

        $message = "Kolam {$kolam->nama_kolam} berhasil ditambahkan!";
        if ($biaya > 0) {
            $message .= " Biaya investasi Rp " . number_format($biaya, 0, ',', '.') . " otomatis dibukukan ke Laporan Keuangan.";
        }

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => $message,
                'kolam'   => $itemData
            ]);
        }

        return redirect()->route('kolam')->with('success', $message);
    }

    /**
     * Edit Data Kolam.
     */
    public function update(Request $request, $id)
    {
        $kolam = Kolam::findOrFail($id);

        $request->validate([
            'nama_kolam'       => 'required|string|max:255|unique:kolam,nama_kolam,' . $kolam->id_kolam . ',id_kolam',
            'tipe_kolam'       => 'required|string|max:255',
            'kapasitas'        => 'required|integer|min:1',
            'id_user'          => 'nullable|exists:users,id_user',
            'kesehatan_ph_air' => 'nullable|numeric|between:0,14',
            'biaya_pembuatan'  => 'nullable|numeric|min:0',
            'status'           => 'nullable|string',
            'catatan'          => 'nullable|string',
        ]);

        $oldBiaya = (float) $kolam->biaya_pembuatan;
        $newBiaya = (float) ($request->biaya_pembuatan ?? $oldBiaya);

        $kolam->update([
            'nama_kolam'       => $request->nama_kolam,
            'tipe_kolam'       => $request->tipe_kolam,
            'kapasitas'        => (int) $request->kapasitas,
            'id_user'          => $request->id_user ?? $kolam->id_user,
            'kesehatan_ph_air' => $request->kesehatan_ph_air ?? $kolam->kesehatan_ph_air,
            'biaya_pembuatan'  => $newBiaya,
            'status'           => $request->status ?? $kolam->status,
            'catatan'          => $request->catatan,
        ]);

        // Sinkronisasi dengan laporan keuangan jika nominal investasi berubah
        if ($newBiaya > 0) {
            $refId = 'KOLAM-INV-' . str_pad($kolam->id_kolam, 4, '0', STR_PAD_LEFT);
            Keuangan::updateOrCreate(
                ['ref_id' => $refId],
                [
                    'id_user'           => Auth::id() ?? $kolam->id_user,
                    'id_kolam'          => $kolam->id_kolam,
                    'tanggal_transaksi' => now()->toDateString(),
                    'tipe_transaksi'    => 'pengeluaran',
                    'kategori'          => 'Pembangunan Kolam',
                    'nominal'           => $newBiaya,
                    'keterangan'        => "Biaya pembuatan/investasi kolam {$kolam->nama_kolam} ({$kolam->tipe_kolam})",
                ]
            );
        }

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => "Data kolam {$kolam->nama_kolam} berhasil diperbarui!",
                'kolam'   => $kolam
            ]);
        }

        return redirect()->route('kolam')->with('success', "Data kolam {$kolam->nama_kolam} berhasil diperbarui!");
    }

    /**
     * Hapus Kolam.
     */
    public function destroy(Request $request, $id)
    {
        $kolam = Kolam::findOrFail($id);

        // Proteksi: Jangan hapus jika ada batch pembesaran atau pembibitan yang sedang aktif berjalan
        $hasActiveBatch = $kolam->batchPembesaran()->whereNotIn('status_siklus', ['selesai', 'gagal'])->exists()
            || $kolam->batchPembibitan()->whereNotIn('status', ['selesai', 'gagal'])->exists();

        if ($hasActiveBatch) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => "Kolam {$kolam->nama_kolam} tidak dapat dihapus karena masih memiliki siklus ikan aktif yang sedang berjalan!"
                ], 422);
            }
            return redirect()->back()->with('error', "Kolam {$kolam->nama_kolam} sedang aktif dan tidak dapat dihapus!");
        }

        $namaKolam = $kolam->nama_kolam;
        $kolam->delete();

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => "Kolam {$namaKolam} berhasil dihapus dari sistem."
            ]);
        }

        return redirect()->route('kolam')->with('success', "Kolam {$namaKolam} berhasil dihapus.");
    }
}

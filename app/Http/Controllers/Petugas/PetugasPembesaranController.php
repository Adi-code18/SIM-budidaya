<?php

namespace App\Http\Controllers\Petugas;

use App\Http\Controllers\Controller;
use App\Models\BatchPembesaran;
use App\Models\Kolam;
use App\Models\ManajemenPakan;
use App\Models\StokPakan;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PetugasPembesaranController extends Controller
{
    /**
     * Dashboard Operasional Petugas Pembesaran.
     */
    public function index()
    {
        $batches = BatchPembesaran::with('kolam')->latest('id_pembesaran')->get();
        $totalBiomassaKg = $batches->where('status_siklus', '!=', 'selesai')->sum('biomassa_est');
        if ($totalBiomassaKg == 0) {
            $totalBiomassaKg = $batches->sum('biomassa_est');
        }
        $totalBiomassa = $totalBiomassaKg / 1000;

        $avgFcrVal = BatchPembesaran::whereNotNull('fcr')->where('fcr', '>', 0)->avg('fcr');
        if (!$avgFcrVal || $avgFcrVal <= 0) {
            $totalPakan = ManajemenPakan::sum('kg_pelet') + ManajemenPakan::sum('kg_daun');
            $avgFcrVal = $totalBiomassaKg > 0 ? round($totalPakan / $totalBiomassaKg, 2) : 0.0;
        }
        $avgFcr = round((float)$avgFcrVal, 2);

        $pakanPh = ManajemenPakan::whereNotNull('ph_air')->where('ph_air', '>', 0)->avg('ph_air');
        $avgPh = $pakanPh ? round((float)$pakanPh, 1) : 0.0;

        return view('mobile_web_petugas.petugas_pembesaran.index', compact('batches', 'totalBiomassa', 'avgFcr', 'avgPh'));
    }

    /**
     * Form Mulai Siklus / Tebar Benih Baru ke Kolam Pembesaran.
     */
    public function createBatch()
    {
        $occupiedKolamIds = BatchPembesaran::where('status_siklus', '!=', 'selesai')
            ->where('status_siklus', '!=', 'gagal')
            ->pluck('id_kolam')
            ->toArray();

        $kolams = Kolam::whereNotIn('id_kolam', $occupiedKolamIds)->get();
        return view('mobile_web_petugas.petugas_pembesaran.create_batch', compact('kolams'));
    }

    /**
     * Simpan Siklus Pembesaran Baru.
     */
    public function storeBatch(Request $request)
    {
        $request->validate([
            'jenis_ikan'       => 'required|string',
            'id_kolam'         => 'required',
            'tgl_tebar'        => 'required|date',
            'biomassa_est'     => 'required|numeric|min:0.1',
            'target_panen_kg'  => 'required|numeric|min:1',
        ]);

        $kolam = Kolam::where('id_kolam', $request->id_kolam)
            ->orWhere('nama_kolam', $request->id_kolam)
            ->first();

        if (!$kolam) {
            return response()->json([
                'success' => false,
                'message' => 'Kolam tebar tidak ditemukan!'
            ], 422);
        }

        // Cek apakah kolam sudah terisi siklus aktif lain
        $isOccupied = BatchPembesaran::where('id_kolam', $kolam->id_kolam)
            ->where('status_siklus', '!=', 'selesai')
            ->where('status_siklus', '!=', 'gagal')
            ->exists();

        if ($isOccupied) {
            return response()->json([
                'success' => false,
                'message' => "Kolam '{$kolam->nama_kolam}' saat ini masih terisi batch aktif. Silakan pilih kolam lain yang kosong!"
            ], 422);
        }

        $batch = BatchPembesaran::create([
            'id_kolam'         => $kolam->id_kolam,
            'id_user'          => Auth::id() ?? 1,
            'tgl_tebar'        => Carbon::parse($request->tgl_tebar)->toDateString(),
            'biomassa_est'     => (float) $request->biomassa_est,
            'fcr'              => 1.10,
            'target_panen_kg'  => (float) $request->target_panen_kg,
            'jenis_ikan'       => $request->jenis_ikan,
            'status_siklus'    => 'berjalan',
        ]);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => "Siklus pembesaran di {$kolam->nama_kolam} berhasil dimulai!",
                'batch'   => $batch
            ]);
        }

        return redirect()->route('petugas.pembesaran.dashboard')->with('success', 'Siklus pembesaran berhasil dimulai!');
    }

    /**
     * Halaman Log Konsumsi Pakan Pembesaran (Mobile Web Petugas).
     */
    public function logPakan(Request $request)
    {
        // 1. Ambil Batch Pembesaran yang Sedang Aktif
        $activeBatches = BatchPembesaran::with('kolam')
            ->where('status_siklus', '!=', 'selesai')
            ->where('status_siklus', '!=', 'gagal')
            ->latest('id_pembesaran')
            ->get();

        // 2. Ambil Master Stok Pakan khusus Pembesaran & Semua
        $stokPakanList = StokPakan::whereIn('kategori_peruntukan', ['pembesaran', 'semua'])->get();

        // 3. Ambil Riwayat Log Pakan Pembesaran Terkini
        $logs = ManajemenPakan::with(['kolam', 'stokPakan', 'user'])
            ->where('kategori_fase', '!=', 'pembibitan')
            ->latest('tgl_log')
            ->take(15)
            ->get();

        return view('mobile_web_petugas.petugas_pembesaran.log_pakan', compact('activeBatches', 'stokPakanList', 'logs'));
    }

    /**
     * Simpan Log Pakan Pembesaran & Otomatis Potong Saldo Stok
     */
    public function storeLogPakan(Request $request)
    {
        $request->validate([
            'id_kolam'      => 'required|exists:kolam,id_kolam',
            'id_stok_pakan' => 'nullable|exists:stok_pakan,id_stok_pakan',
            'tgl_log'       => 'nullable|date',
            'kg_pelet'      => 'nullable|numeric|min:0|max:100',
            'kg_daun'       => 'nullable|numeric|min:0|max:100',
            'jenis_daun'    => 'nullable|string',
            'total_biaya'   => 'nullable|numeric|min:0',
            'ph_air'        => 'nullable|numeric|min:0|max:14',
        ], [
            'kg_pelet.max'  => 'Pemberian pelet maksimal 100 kg per sesi.',
            'kg_daun.max'   => 'Pemberian pakan daun maksimal 100 kg per sesi.',
        ]);

        // Validasi: Kolam harus memiliki batch pembesaran yang sedang aktif
        $hasActiveBatch = BatchPembesaran::where('id_kolam', $request->id_kolam)
            ->where('status_siklus', '!=', 'selesai')
            ->where('status_siklus', '!=', 'gagal')
            ->exists();

        if (!$hasActiveBatch) {
            return response()->json([
                'success' => false,
                'message' => 'Kolam ini belum diisi ikan aktif. Anda harus memulai siklus tebar benih terlebih dahulu!'
            ], 422);
        }

        $tgl = $request->tgl_log ? Carbon::parse($request->tgl_log)->toDateString() : Carbon::today()->toDateString();
        $kgPelet = (float) ($request->kg_pelet ?? 0);
        $kgDaun = (float) ($request->kg_daun ?? 0);
        $totalKg = $kgPelet + $kgDaun;

        if ($totalKg <= 0) {
            return response()->json([
                'success' => false,
                'message' => 'Jumlah pakan (pelet atau daun) harus lebih dari 0 kg!'
            ], 422);
        }

        $stokItem = $request->id_stok_pakan ? StokPakan::find($request->id_stok_pakan) : null;
        $hargaPerKg = $stokItem ? (float) $stokItem->harga_per_satuan : 12500;
        $totalBiaya = (float) ($request->total_biaya ?: ($kgPelet * $hargaPerKg));

        $log = ManajemenPakan::create([
            'id_user'       => Auth::id() ?? 1,
            'id_kolam'      => $request->id_kolam,
            'id_stok_pakan' => $request->id_stok_pakan,
            'kategori_fase' => 'pembesaran',
            'tgl_log'       => $tgl,
            'kg_pelet'      => $kgPelet,
            'kg_daun'       => $kgDaun,
            'jenis_daun'    => $request->jenis_daun ?: ($stokItem ? $stokItem->nama_pakan : null),
            'total_biaya'   => $totalBiaya,
            'ph_air'        => $request->ph_air ?? 7.0,
        ]);

        // POTONG STOK OTOMATIS
        if ($stokItem && $kgPelet > 0) {
            $stokItem->update([
                'stok_tersisa' => max(0, (float) $stokItem->stok_tersisa - $kgPelet)
            ]);
        } elseif ($kgPelet > 0) {
            $defaultPelet = StokPakan::where('nama_pakan', 'like', '%Pelet%')->first();
            if ($defaultPelet) {
                $defaultPelet->update([
                    'stok_tersisa' => max(0, (float) $defaultPelet->stok_tersisa - $kgPelet)
                ]);
            }
        }

        // Update pH Air Kolam
        if ($request->filled('ph_air')) {
            Kolam::where('id_kolam', $request->id_kolam)->update([
                'kesehatan_ph_air' => $request->ph_air
            ]);
        }

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Log pakan pembesaran berhasil dicatat & stok terpotong!',
                'log'     => $log->load(['kolam', 'stokPakan'])
            ]);
        }

        return redirect()->route('petugas.pembesaran.log-pakan')->with('success', 'Log pakan pembesaran berhasil dicatat & stok terpotong!');
    }
}

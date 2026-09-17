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
        $batches = BatchPembesaran::with(['kolam', 'batchPembibitan.ikan'])->latest('id_pembesaran')->get();
        $totalBiomassaKg = $batches->where('status_siklus', '!=', 'selesai')->sum('biomassa_est');
        if ($totalBiomassaKg == 0) {
            $totalBiomassaKg = $batches->sum('biomassa_est');
        }
        $totalBiomassa = $totalBiomassaKg / 1000;

        $fcrList = [];
        foreach ($batches as $b) {
            $fcrVal = $b->fcr > 0 ? (float)$b->fcr : $b->calculateActualFcr();
            if ($fcrVal > 0) {
                $fcrList[] = $fcrVal;
            }
        }
        $avgFcr = count($fcrList) > 0 ? round(array_sum($fcrList) / count($fcrList), 2) : 0.0;
        if ($avgFcr <= 0) {
            $totalPakan = ManajemenPakan::sum('kg_pelet') + ManajemenPakan::sum('kg_daun');
            $avgFcr = $totalBiomassaKg > 0 ? round($totalPakan / $totalBiomassaKg, 2) : 0.0;
        }

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
        $ikans = \App\Models\Ikan::orderBy('nama_ikan', 'asc')->get();
        return view('mobile_web_petugas.petugas_pembesaran.create_batch', compact('kolams', 'ikans'));
    }

    /**
     * Simpan Siklus Pembesaran Baru.
     */
    public function storeBatch(Request $request)
    {
        $request->validate([
            'id_kolam'        => 'required|exists:kolam,id_kolam',
            'jenis_ikan'      => 'required|string|max:255',
            'biomassa_est'    => 'required|numeric|min:1',
            'target_panen_kg' => 'required|numeric|min:1',
            'tgl_tebar'       => 'required|date',
        ]);

        $kolam = Kolam::findOrFail($request->id_kolam);

        $sumberBenih = $request->sumber_benih ?? 'Hatchery Internal';
        $asalBibit = ($sumberBenih === 'Pemasok Eksternal' || $request->asal_bibit === 'beli_luar') ? 'beli_luar' : 'pembibitan_sendiri';
        $biayaBeliBibit = $asalBibit === 'beli_luar' ? (float) ($request->biaya_beli_bibit ?? 0) : 0.0;
        $tglTebar = Carbon::parse($request->tgl_tebar)->toDateString();

        $cleanName = trim(preg_replace('/^(ikan\s+)/i', '', $request->jenis_ikan));
        $ikanRef = \App\Models\Ikan::where('nama_ikan', 'LIKE', '%' . $cleanName . '%')->first();
        $defaultFcr = $ikanRef ? (float)$ikanRef->fcr_min : 1.10;
        $estBulan = $ikanRef && $ikanRef->bulan_panen_max ? (float)$ikanRef->bulan_panen_max : 3.0;

        $batch = BatchPembesaran::create([
            'id_kolam'         => $kolam->id_kolam,
            'id_user'          => Auth::id() ?? 1,
            'asal_bibit'       => $asalBibit,
            'biaya_beli_bibit' => $biayaBeliBibit,
            'tgl_tebar'        => $tglTebar,
            'est_tgl_panen'    => Carbon::parse($tglTebar)->addDays(round($estBulan * 30))->toDateString(),
            'biomassa_est'     => (float) $request->biomassa_est,
            'fcr'              => $defaultFcr,
            'target_panen_kg'  => (float) $request->target_panen_kg,
            'jenis_ikan'       => $request->jenis_ikan,
            'status_siklus'    => 'berjalan',
        ]);

        // Catat otomatis ke Keuangan jika beli bibit dari luar dan ada biaya
        if ($asalBibit === 'beli_luar' && $biayaBeliBibit > 0) {
            $pbRef = 'BELI-BIBIT-PB-' . str_pad($batch->id_pembesaran, 4, '0', STR_PAD_LEFT);
            \App\Models\Keuangan::updateOrCreate(
                ['ref_id' => $pbRef],
                [
                    'id_user'           => Auth::id() ?? 1,
                    'id_kolam'          => $kolam->id_kolam,
                    'tanggal_transaksi' => $tglTebar,
                    'tipe_transaksi'    => 'pengeluaran',
                    'kategori'          => 'Pembelian Bibit ' . $request->jenis_ikan,
                    'nominal'           => $biayaBeliBibit,
                    'keterangan'        => "Pembelian bibit luar {$request->jenis_ikan} (" . number_format($batch->biomassa_est, 1, ',', '.') . " kg) untuk tebar di {$kolam->nama_kolam} (#PB-" . str_pad($batch->id_pembesaran, 5, '0', STR_PAD_LEFT) . ")",
                ]
            );
        }

        if ($request->wantsJson() || $request->ajax()) {
            $msg = "Siklus pembesaran di {$kolam->nama_kolam} berhasil dimulai!";
            if ($asalBibit === 'beli_luar' && $biayaBeliBibit > 0) {
                $msg .= " Biaya bibit Rp " . number_format($biayaBeliBibit, 0, ',', '.') . " otomatis dibukukan ke Keuangan.";
            }
            return response()->json([
                'success' => true,
                'message' => $msg,
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
        $now = Carbon::now();

        // 1. Ambil Batch Pembesaran yang Sedang Aktif dengan Sinkronisasi DOC & Fase
        $activeBatches = BatchPembesaran::with('kolam')
            ->where('status_siklus', '!=', 'selesai')
            ->where('status_siklus', '!=', 'gagal')
            ->latest('id_pembesaran')
            ->get()
            ->map(function ($b) use ($now) {
                $tgl = $b->tgl_tebar ? Carbon::parse($b->tgl_tebar) : Carbon::parse($b->created_at);
                $doc = max(1, (int) $tgl->diffInDays($now) + 1);

                if ($doc <= 20) {
                    $fase = 'Starter (Awal)';
                    $faseKey = 'starter';
                    $faseBadge = 'bg-sky-100 text-sky-800 border-sky-200';
                    $rekomendasiPakan = 'Pelet Starter Mikro (PF-1000 / 781-1)';
                    $ratePelet = 0.035; // 3.5% biomassa
                } elseif ($doc <= 60) {
                    $fase = 'Grower (Pertumbuhan)';
                    $faseKey = 'grower';
                    $faseBadge = 'bg-indigo-100 text-indigo-800 border-indigo-200';
                    $rekomendasiPakan = 'Pelet Apung Grower (781-2)';
                    $ratePelet = 0.028; // 2.8% biomassa
                } else {
                    $fase = 'Finisher (Siap Panen)';
                    $faseKey = 'finisher';
                    $faseBadge = 'bg-emerald-100 text-emerald-800 border-emerald-200';
                    $rekomendasiPakan = 'Pelet Apung Finisher (781-3)';
                    $ratePelet = 0.022; // 2.2% biomassa
                }

                $biomassa = (float) $b->biomassa_est;
                $estPelet = max(1, round($biomassa * $ratePelet, 1));

                $b->doc = $doc;
                $b->fase = $fase;
                $b->fase_key = $faseKey;
                $b->fase_badge = $faseBadge;
                $b->rekomendasi_pakan = $rekomendasiPakan;
                $b->est_pelet_kg = $estPelet;

                return $b;
            });

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
            'id_kolam'          => 'required|exists:kolam,id_kolam',
            'id_stok_pakan'     => 'nullable|exists:stok_pakan,id_stok_pakan',
            'id_stok_suplemen'  => 'nullable|exists:stok_pakan,id_stok_pakan',
            'tgl_log'           => 'nullable|date',
            'kg_pelet'          => 'nullable|numeric|min:0|max:100',
            'kg_daun'           => 'nullable|numeric|min:0|max:100',
            'jenis_daun'        => 'nullable|string',
            'total_biaya'       => 'nullable|numeric|min:0',
            'ph_air'            => 'nullable|numeric|min:0|max:14',
        ], [
            'kg_pelet.max'      => 'Pemberian pelet maksimal 100 kg per sesi.',
            'kg_daun.max'       => 'Pemberian pakan daun maksimal 100 kg per sesi.',
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

        // 1. Ambil pakan utama (pelet)
        $stokItem = $request->id_stok_pakan ? StokPakan::find($request->id_stok_pakan) : null;
        $hargaPelet = $stokItem ? (float) $stokItem->harga_per_satuan : 12500;

        // 2. Ambil pakan suplemen / dedaunan dari Master Stok Pakan
        $suplemenItem = null;
        if ($request->filled('id_stok_suplemen')) {
            $suplemenItem = StokPakan::find($request->id_stok_suplemen);
        } elseif ($request->filled('jenis_daun')) {
            $suplemenItem = StokPakan::where('nama_pakan', 'like', '%' . $request->jenis_daun . '%')->first();
        }
        $hargaSuplemen = $suplemenItem ? (float) $suplemenItem->harga_per_satuan : 0;
        $jenisDaun = $suplemenItem ? $suplemenItem->nama_pakan : ($request->jenis_daun ?: null);

        // Kalkulasi: Total Biaya = (kg_pelet * harga_pelet) + (kg_daun * harga_suplemen)
        $calcBiaya = ($kgPelet * $hargaPelet) + ($kgDaun * $hargaSuplemen);
        $totalBiaya = (float) ($request->total_biaya ?? $calcBiaya);
        if ($totalBiaya <= 0 && ($kgPelet > 0 || $kgDaun > 0)) {
            $totalBiaya = $calcBiaya;
        }

        $log = ManajemenPakan::create([
            'id_user'       => Auth::id() ?? 1,
            'id_kolam'      => $request->id_kolam,
            'id_stok_pakan' => $request->id_stok_pakan,
            'kategori_fase' => 'pembesaran',
            'tgl_log'       => $tgl,
            'kg_pelet'      => $kgPelet,
            'kg_daun'       => $kgDaun,
            'jenis_daun'    => $jenisDaun,
            'total_biaya'   => $totalBiaya,
            'ph_air'        => $request->ph_air ?? 7.0,
        ]);

        // POTONG STOK OTOMATIS
        // 1. Potong Stok Pelet / Pakan Utama
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

        // 2. Potong Stok Dedaunan / Pakan Suplemen
        if ($suplemenItem && $kgDaun > 0) {
            $suplemenItem->update([
                'stok_tersisa' => max(0, (float) $suplemenItem->stok_tersisa - $kgDaun)
            ]);
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

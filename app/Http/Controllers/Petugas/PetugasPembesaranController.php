<?php

namespace App\Http\Controllers\Petugas;

use App\Http\Controllers\Controller;
use App\Models\BatchPembesaran;
use App\Models\Kolam;
use App\Models\ManajemenPakan;
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
        $kolams = Kolam::all();
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
            'biomassa_est'     => 'required|numeric',
            'target_panen_kg'  => 'required|numeric',
        ]);

        $kolam = Kolam::where('nama_kolam', $request->id_kolam)->orWhere('id_kolam', $request->id_kolam)->first();

        $batch = BatchPembesaran::create([
            'id_kolam'         => $kolam ? $kolam->id_kolam : 1,
            'id_user'          => Auth::id() ?? 1,
            'tgl_tebar'        => $request->tgl_tebar,
            'biomassa_est'     => $request->biomassa_est,
            'fcr'              => 1.12,
            'target_panen_kg'  => $request->target_panen_kg,
            'jenis_ikan'       => $request->jenis_ikan,
            'status_siklus'    => 'aktif',
        ]);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Siklus pembesaran berhasil dimulai!',
                'batch'   => $batch
            ]);
        }

        return redirect()->route('petugas.pembesaran.dashboard')->with('success', 'Siklus pembesaran berhasil dimulai!');
    }

    /**
     * Log Konsumsi Pakan Pembesaran.
     */
    public function logPakan(Request $request)
    {
        $kolams = Kolam::all();
        $logs = ManajemenPakan::with('kolam')->latest('tgl_log')->get();
        $selectedKolam = $request->input('kolam', '');

        return view('mobile_web_petugas.petugas_pembesaran.log_pakan', compact('kolams', 'logs', 'selectedKolam'));
    }
}

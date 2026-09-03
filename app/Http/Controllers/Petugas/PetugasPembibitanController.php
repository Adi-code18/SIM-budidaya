<?php

namespace App\Http\Controllers\Petugas;

use App\Http\Controllers\Controller;
use App\Models\BatchPembibitan;
use App\Models\Kolam;
use App\Models\ManajemenPakan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PetugasPembibitanController extends Controller
{
    /**
     * Dashboard Operasional Petugas Pembibitan.
     */
    public function index()
    {
        $batches = BatchPembibitan::with('kolam')->latest('id_batch')->get();
        $totalAwal = $batches->sum('jumlah_bibitAwal');
        $totalMati = $batches->sum('jumlah_kematian');
        $srRate = $totalAwal > 0 ? round((($totalAwal - $totalMati) / $totalAwal) * 100, 1) : 0.0;

        $activeBatches = $batches->where('status', '!=', 'selesai')->where('status', '!=', 'gagal');
        $totalBenih = $activeBatches->sum(function($b) {
            return max(0, $b->jumlah_bibitAwal - $b->jumlah_kematian);
        });
        if ($totalBenih == 0) {
            $totalBenih = max(0, $totalAwal - $totalMati);
        }

        $hatcheryKolams = Kolam::where(function($q) {
            $q->where('tipe_kolam', 'like', '%Hatchery%')
              ->orWhere('tipe_kolam', 'like', '%Pemijahan%')
              ->orWhere('tipe_kolam', 'like', '%Penetasan%')
              ->orWhere('tipe_kolam', 'like', '%Pendederan%')
              ->orWhere('tipe_kolam', 'like', '%Pembibitan%');
        })->get();

        $hatcheryCount = $hatcheryKolams->count();
        $totalTank = $hatcheryCount > 0 ? $hatcheryCount : Kolam::count();

        $pakanPh = ManajemenPakan::whereIn('id_kolam', $hatcheryKolams->pluck('id_kolam'))->whereNotNull('ph_air')->where('ph_air', '>', 0)->avg('ph_air');
        $avgPh = $pakanPh ? round((float)$pakanPh, 1) : 0.0;

        return view('mobile_web_petugas.petugas_pembibitan.index', compact('batches', 'totalBenih', 'totalTank', 'srRate', 'avgPh', 'totalAwal'));
    }

    /**
     * Form Input Batch / Log Pembibitan Baru.
     */
    public function form()
    {
        $kolams = Kolam::where(function($q) {
            $q->where('tipe_kolam', 'like', '%Hatchery%')
              ->orWhere('tipe_kolam', 'like', '%Pemijahan%')
              ->orWhere('tipe_kolam', 'like', '%Penetasan%')
              ->orWhere('tipe_kolam', 'like', '%Pendederan%')
              ->orWhere('tipe_kolam', 'like', '%Pembibitan%');
        })->get();

        $ikans = \App\Models\Ikan::orderBy('nama_ikan', 'asc')->get();

        return view('mobile_web_petugas.petugas_pembibitan.log_pembibitan', compact('kolams', 'ikans'));
    }

    /**
     * Simpan Batch Pembibitan Baru.
     */
    public function storeBatch(Request $request)
    {
        $request->validate([
            'id_kolam'              => 'required',
            'id_ikan'               => 'nullable',
            'jenis_ikan'            => 'nullable|string',
            'est_prcs_pembibitaan'  => 'nullable|date',
            'jumlah_bibitAwal'      => 'nullable|numeric',
        ]);

        $kolam = Kolam::where('nama_kolam', $request->id_kolam)->orWhere('id_kolam', $request->id_kolam)->first();

        $idIkan = $request->id_ikan ?: null;
        $jenisIkan = $request->jenis_ikan;
        if ($idIkan && !$jenisIkan) {
            $ik = \App\Models\Ikan::find($idIkan);
            if ($ik) $jenisIkan = $ik->nama_ikan;
        }

        $batch = BatchPembibitan::create([
            'id_kolam'             => $kolam ? $kolam->id_kolam : 10,
            'id_user'              => Auth::id() ?? 1,
            'id_ikan'              => $idIkan,
            'jenis_ikan'           => $jenisIkan,
            'tgl_pemijahan'        => $request->tgl_pemijahan ?? $request->tgl_tebar ?? now(),
            'jumlah_bibitAwal'     => $request->jumlah_bibitAwal ?? 100000,
            'jumlah_kematian'      => 0,
            'status'               => 'aktif',
        ]);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Log pembibitan baru berhasil disimpan!',
                'batch'   => $batch
            ]);
        }

        return redirect()->route('petugas.pembibitan.dashboard')->with('success', 'Log pembibitan baru berhasil disimpan!');
    }

    /**
     * Log Konsumsi Pakan Pembibitan.
     */
    public function logPakan(Request $request)
    {
        $kolams = Kolam::all();
        $logs = ManajemenPakan::with('kolam')->latest('tgl_log')->get();
        $selectedBatch = $request->input('batch', 'Batch-H-042');

        return view('mobile_web_petugas.petugas_pembibitan.log_pakan', compact('kolams', 'logs', 'selectedBatch'));
    }
}

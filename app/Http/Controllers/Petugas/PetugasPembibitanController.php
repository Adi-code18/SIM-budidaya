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
        $totalBenih = $batches->sum('jumlah_bibitAwal') - $batches->sum('jumlah_kematian');
        $totalTank = Kolam::count();

        return view('mobile_web_petugas.petugas_pembibitan.index', compact('batches', 'totalBenih', 'totalTank'));
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

        return view('mobile_web_petugas.petugas_pembibitan.log_pembibitan', compact('kolams'));
    }

    /**
     * Simpan Batch Pembibitan Baru.
     */
    public function storeBatch(Request $request)
    {
        $request->validate([
            'jenis_ikan'       => 'required|string',
            'id_kolam'         => 'required',
            'jumlah_bibitAwal' => 'nullable|numeric',
        ]);

        $kolam = Kolam::where('nama_kolam', $request->id_kolam)->orWhere('id_kolam', $request->id_kolam)->first();

        $batch = BatchPembibitan::create([
            'id_kolam'         => $kolam ? $kolam->id_kolam : 10,
            'id_user'          => Auth::id() ?? 1,
            'tgl_pemijahan'    => $request->tgl_pemijahan ?? $request->tgl_tebar ?? now(),
            'jumlah_bibitAwal' => $request->jumlah_bibitAwal ?? 100000,
            'jenis_ikan'       => $request->jenis_ikan,
            'jumlah_kematian'  => 0,
            'status'           => 'aktif',
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

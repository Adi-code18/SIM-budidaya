<?php

namespace App\Http\Controllers;

use App\Models\BatchPembesaran;
use App\Models\BatchPembibitan;
use App\Models\Kolam;
use App\Models\ManajemenPakan;
use App\Models\TransaksiDistribusi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MobilePetugasController extends Controller
{
    // ========================================================
    // 1. PETUGAS DISTRIBUSI
    // ========================================================

    public function distribusiIndex()
    {
        // Only active tasks (not yet completed)
        $orders = TransaksiDistribusi::where('status_order', '!=', 'selesai')
            ->with(['mitra', 'batchPembesaran'])
            ->latest('id_transaksi')
            ->get();

        $allOrders = TransaksiDistribusi::all();
        $user = Auth::user();

        $activeCount = $allOrders->where('status_order', 'dalam_pengiriman')->count();
        $siapCount = $allOrders->whereIn('status_order', ['siap_kirim', 'pending'])->count();
        $selesaiCount = $allOrders->where('status_order', 'selesai')->count();
        $totalCount = $orders->count();

        return view('mobile_web_petugas.petugas_distribusi.index', compact(
            'orders', 'user', 'activeCount', 'siapCount', 'selesaiCount', 'totalCount'
        ));
    }

    public function distribusiRiwayat()
    {
        $riwayats = TransaksiDistribusi::where('status_order', 'selesai')
            ->with(['mitra', 'batchPembesaran'])
            ->latest('tanggal_order')
            ->get();

        $totalSelesai = $riwayats->count();

        return view('mobile_web_petugas.petugas_distribusi.riwayat', compact('riwayats', 'totalSelesai'));
    }

    public function distribusiDetail($id = null)
    {
        $transaksi = null;
        if ($id && is_numeric($id)) {
            $transaksi = TransaksiDistribusi::with(['mitra', 'batchPembesaran'])->find($id);
        } elseif ($id) {
            $transaksi = TransaksiDistribusi::with(['mitra', 'batchPembesaran'])->first();
        }

        if (!$transaksi) {
            $transaksi = TransaksiDistribusi::with(['mitra', 'batchPembesaran'])->first();
        }

        return view('mobile_web_petugas.petugas_distribusi.detail', compact('id', 'transaksi'));
    }

    public function distribusiComplete(Request $request, $id)
    {
        $transaksi = TransaksiDistribusi::find($id);
        if (!$transaksi) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'message' => 'Transaksi pengiriman tidak ditemukan.'], 404);
            }
            return redirect()->route('mobile.petugas.pengiriman')->with('error', 'Transaksi tidak ditemukan.');
        }

        $transaksi->status_order = 'selesai';
        $transaksi->save();

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Pengiriman #' . str_pad($transaksi->id_transaksi, 4, '0', STR_PAD_LEFT) . ' telah selesai dan dipindahkan ke Riwayat!',
                'redirect' => route('mobile.petugas.riwayat')
            ]);
        }

        return redirect()->route('mobile.petugas.riwayat')->with('success', 'Pengiriman telah selesai!');
    }

    // ========================================================
    // 2. PETUGAS PEMBIBITAN
    // ========================================================

    public function pembibitanIndex()
    {
        $batches = BatchPembibitan::with('kolam')->latest('id_batch')->get();
        $totalBenih = $batches->sum('jumlah_bibitAwal') - $batches->sum('jumlah_kematian');
        $totalTank = Kolam::count();

        return view('mobile_web_petugas.petugas_pembibitan.index', compact('batches', 'totalBenih', 'totalTank'));
    }

    public function pembibitanForm()
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

    public function pembibitanStoreBatch(Request $request)
    {
        $request->validate([
            'jenis_ikan'         => 'required|string',
            'id_kolam'           => 'required',
            'jumlah_bibitAwal'   => 'nullable|numeric',
        ]);

        $kolam = Kolam::where('nama_kolam', $request->id_kolam)->orWhere('id_kolam', $request->id_kolam)->first();

        $batch = BatchPembibitan::create([
            'id_kolam'           => $kolam ? $kolam->id_kolam : 10,
            'id_user'            => Auth::id() ?? 1,
            'tgl_pemijahan'      => $request->tgl_pemijahan ?? $request->tgl_tebar ?? now(),
            'jumlah_bibitAwal'   => $request->jumlah_bibitAwal ?? 100000,
            'jenis_ikan'         => $request->jenis_ikan,
            'jumlah_kematian'    => 0,
            'status'             => 'aktif',
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

    public function pembibitanLogPakan(Request $request)
    {
        $kolams = Kolam::all();
        $logs = ManajemenPakan::with('kolam')->latest('tgl_log')->get();
        $selectedBatch = $request->input('batch', 'Batch-H-042');

        return view('mobile_web_petugas.petugas_pembibitan.log_pakan', compact('kolams', 'logs', 'selectedBatch'));
    }

    // ========================================================
    // 3. PETUGAS PEMBESARAN
    // ========================================================

    public function pembesaranIndex()
    {
        $batches = BatchPembesaran::with('kolam')->latest('id_pembesaran')->get();
        $totalBiomassa = BatchPembesaran::sum('biomassa_est') / 1000;
        $avgFcr = BatchPembesaran::whereNotNull('fcr')->where('fcr', '>', 0)->avg('fcr') ?? 1.24;

        return view('mobile_web_petugas.petugas_pembesaran.index', compact('batches', 'totalBiomassa', 'avgFcr'));
    }

    public function pembesaranCreateBatch()
    {
        $kolams = Kolam::all();
        return view('mobile_web_petugas.petugas_pembesaran.create_batch', compact('kolams'));
    }

    public function pembesaranStoreBatch(Request $request)
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

    public function pembesaranLogPakan(Request $request)
    {
        $kolams = Kolam::all();
        $logs = ManajemenPakan::with('kolam')->latest('tgl_log')->get();
        $selectedKolam = $request->input('kolam', '');

        return view('mobile_web_petugas.petugas_pembesaran.log_pakan', compact('kolams', 'logs', 'selectedKolam'));
    }
}

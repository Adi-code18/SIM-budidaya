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
        $orders = TransaksiDistribusi::with(['mitra', 'batchPembesaran'])->latest('id_transaksi')->get();
        $user = Auth::user();

        $activeCount = $orders->where('status_order', 'dalam_pengiriman')->count();
        $siapCount = $orders->where('status_order', 'siap_kirim')->count();
        $selesaiCount = $orders->where('status_order', 'selesai')->count();
        $totalCount = $orders->count();

        return view('mobile_web_petugas.petugas_distribusi.index', compact(
            'orders', 'user', 'activeCount', 'siapCount', 'selesaiCount', 'totalCount'
        ));
    }

    public function distribusiRiwayat()
    {
        $riwayats = TransaksiDistribusi::where('status_order', 'selesai')->with(['mitra', 'batchPembesaran'])->latest('tanggal_order')->get();
        return view('mobile_web_petugas.petugas_distribusi.riwayat', compact('riwayats'));
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
        $kolams = Kolam::all();
        return view('mobile_web_petugas.petugas_pembibitan.log_pembibitan', compact('kolams'));
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

    public function pembesaranLogPakan(Request $request)
    {
        $kolams = Kolam::all();
        $logs = ManajemenPakan::with('kolam')->latest('tgl_log')->get();
        $selectedKolam = $request->input('kolam', '');

        return view('mobile_web_petugas.petugas_pembesaran.log_pakan', compact('kolams', 'logs', 'selectedKolam'));
    }
}

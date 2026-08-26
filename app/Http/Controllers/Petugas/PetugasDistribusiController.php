<?php

namespace App\Http\Controllers\Petugas;

use App\Http\Controllers\Controller;
use App\Models\TransaksiDistribusi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PetugasDistribusiController extends Controller
{
    /**
     * Dashboard / Daftar Pengiriman Aktif Petugas Distribusi.
     */
    public function index()
    {
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

    /**
     * Riwayat Pengiriman Selesai.
     */
    public function riwayat()
    {
        $riwayats = TransaksiDistribusi::where('status_order', 'selesai')
            ->with(['mitra', 'batchPembesaran'])
            ->latest('tanggal_order')
            ->get();

        $totalSelesai = $riwayats->count();

        return view('mobile_web_petugas.petugas_distribusi.riwayat', compact('riwayats', 'totalSelesai'));
    }

    /**
     * Detail Pengiriman & Rute Navigasi Mitra.
     */
    public function detail($id = null)
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

    /**
     * Selesaikan Pengiriman & Upload Bukti Penerimaan.
     */
    public function complete(Request $request, $id)
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
                'success'  => true,
                'message'  => 'Pengiriman #' . str_pad($transaksi->id_transaksi, 4, '0', STR_PAD_LEFT) . ' telah selesai dan dipindahkan ke Riwayat!',
                'redirect' => route('mobile.petugas.riwayat')
            ]);
        }

        return redirect()->route('mobile.petugas.riwayat')->with('success', 'Pengiriman telah selesai!');
    }
}

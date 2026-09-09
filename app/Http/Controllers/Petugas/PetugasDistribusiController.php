<?php

namespace App\Http\Controllers\Petugas;

use App\Http\Controllers\Controller;
use App\Models\TransaksiDistribusi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PetugasDistribusiController extends Controller
{
    /**
     * Dashboard / Daftar Pengiriman Aktif Petugas Distribusi.
     */
    public function index()
    {
        $orders = TransaksiDistribusi::where('status_order', '!=', 'selesai')
            ->where('status_order', '!=', 'dibatalkan')
            ->with(['mitra', 'batchPembesaran'])
            ->latest('id_transaksi')
            ->get();

        $allOrders = TransaksiDistribusi::all();
        $user = Auth::user();

        $pendingCount = $allOrders->where('status_order', 'pending')->count();
        $pemberokianCount = $allOrders->where('status_order', 'pemberokian')->count();
        $siapCount = $allOrders->where('status_order', 'siap_kirim')->count();
        $activeCount = $allOrders->whereIn('status_order', ['dalam_pengiriman', 'dikirim'])->count();
        $selesaiCount = $allOrders->where('status_order', 'selesai')->count();
        $totalCount = $orders->count();

        return view('mobile_web_petugas.petugas_distribusi.index', compact(
            'orders', 'user', 'pendingCount', 'pemberokianCount', 'activeCount', 'siapCount', 'selesaiCount', 'totalCount'
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
     * Mulai Pengiriman Sekarang (Ubah status siap_kirim -> dalam_pengiriman).
     */
    public function startDelivery(Request $request, $id)
    {
        $transaksi = TransaksiDistribusi::find($id);
        if (!$transaksi) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'message' => 'Transaksi pengiriman tidak ditemukan.'], 404);
            }
            return redirect()->route('mobile.petugas.pengiriman')->with('error', 'Transaksi tidak ditemukan.');
        }

        // Validasi: hanya status 'siap_kirim' yang dapat diberangkatkan
        if ($transaksi->status_order === 'pending') {
            $msg = 'Pesanan masih dalam status Pending (Menunggu Persiapan & Verifikasi Manajer).';
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'message' => $msg], 422);
            }
            return redirect()->back()->with('error', $msg);
        }

        if ($transaksi->status_order === 'pemberokian') {
            $msg = 'Ikan masih dalam proses pemberokan di kolam. Belum siap diberangkatkan.';
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'message' => $msg], 422);
            }
            return redirect()->back()->with('error', $msg);
        }

        if ($transaksi->status_order === 'selesai') {
            $msg = 'Pengiriman ini sudah diselesaikan sebelumnya.';
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'message' => $msg], 422);
            }
            return redirect()->route('mobile.petugas.riwayat')->with('info', $msg);
        }

        // Ubah status ke dalam_pengiriman
        $transaksi->status_order = 'dalam_pengiriman';
        $transaksi->save();

        $orderCode = '#ORD-' . str_pad($transaksi->id_transaksi, 4, '0', STR_PAD_LEFT);
        $successMsg = "Pengiriman {$orderCode} telah dimulai! Silakan lanjutkan navigasi ke lokasi mitra.";

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success'  => true,
                'message'  => $successMsg,
                'redirect' => route('mobile.petugas.detail', ['id' => $transaksi->id_transaksi])
            ]);
        }

        return redirect()->route('mobile.petugas.detail', ['id' => $transaksi->id_transaksi])->with('success', $successMsg);
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

        if (!$transaksi) {
            return redirect()->route('mobile.petugas.pengiriman')->with('info', 'Belum ada data pengiriman aktif.');
        }

        return view('mobile_web_petugas.petugas_distribusi.detail', compact('id', 'transaksi'));
    }

    /**
     * Selesaikan Pengiriman & Upload Bukti Penerimaan (Wajib Foto).
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

        // Cek apakah order masih pending atau pemberokian
        if (in_array($transaksi->status_order, ['pending', 'pemberokian'])) {
            $msg = 'Pengiriman belum diberangkatkan. Harap mulai pengiriman terlebih dahulu.';
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'message' => $msg], 422);
            }
            return redirect()->back()->with('error', $msg);
        }

        $imagePath = $transaksi->Bukti_sampai;

        // 1. Handle file upload jika dikirim via multipart form
        if ($request->hasFile('foto_bukti')) {
            $file = $request->file('foto_bukti');
            $filename = 'bukti_delivery_' . $transaksi->id_transaksi . '_' . time() . '.' . $file->getClientOriginalExtension();
            $imagePath = $file->storeAs('bukti_pengiriman', $filename, 'public');
        } 
        // 2. Handle base64 upload jika dikirim dari JavaScript canvas/reader
        elseif ($request->filled('foto_base64')) {
            $base64Data = $request->input('foto_base64');
            if (preg_match('/^data:image\/(\w+);base64,/', $base64Data, $type)) {
                $base64Data = substr($base64Data, strpos($base64Data, ',') + 1);
                $type = strtolower($type[1]);
                if (!in_array($type, ['jpg', 'jpeg', 'gif', 'png', 'webp'])) {
                    $type = 'jpg';
                }
                $imageData = base64_decode($base64Data);
                if ($imageData !== false) {
                    $filename = 'bukti_delivery_' . $transaksi->id_transaksi . '_' . time() . '.' . $type;
                    Storage::disk('public')->put('bukti_pengiriman/' . $filename, $imageData);
                    $imagePath = 'bukti_pengiriman/' . $filename;
                }
            }
        }

        // Validasi: Wajib ada foto bukti serah terima
        if (empty($imagePath)) {
            $errorMsg = 'Foto bukti serah terima fisik wajib diunggah sebelum pengiriman diselesaikan!';
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => $errorMsg
                ], 422);
            }
            return redirect()->back()->with('error', $errorMsg);
        }

        $transaksi->Bukti_sampai = $imagePath;
        $transaksi->status_order = 'selesai';
        $transaksi->save();

        $orderCode = '#ORD-' . str_pad($transaksi->id_transaksi, 4, '0', STR_PAD_LEFT);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success'  => true,
                'message'  => "Pengiriman {$orderCode} telah selesai dan dipindahkan ke Riwayat!",
                'redirect' => route('mobile.petugas.riwayat')
            ]);
        }

        return redirect()->route('mobile.petugas.riwayat')->with('success', "Pengiriman {$orderCode} telah selesai!");
    }
}


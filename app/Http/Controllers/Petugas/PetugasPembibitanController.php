<?php

namespace App\Http\Controllers\Petugas;

use App\Http\Controllers\Controller;
use App\Models\BatchPembibitan;
use App\Models\Kolam;
use App\Models\ManajemenPakan;
use App\Models\StokPakan;
use Carbon\Carbon;
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
        $occupiedHatcheryIds = BatchPembibitan::where('status', '!=', 'selesai')
            ->where('status', '!=', 'gagal')
            ->pluck('id_kolam')
            ->toArray();

        $kolams = Kolam::where(function($q) {
            $q->where('tipe_kolam', 'like', '%Hatchery%')
              ->orWhere('tipe_kolam', 'like', '%Pemijahan%')
              ->orWhere('tipe_kolam', 'like', '%Penetasan%')
              ->orWhere('tipe_kolam', 'like', '%Pendederan%')
              ->orWhere('tipe_kolam', 'like', '%Pembibitan%');
        })->whereNotIn('id_kolam', $occupiedHatcheryIds)->get();

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
            'jumlah_bibitAwal'      => 'nullable|numeric|min:1',
        ]);

        $kolam = Kolam::where('id_kolam', $request->id_kolam)
            ->orWhere('nama_kolam', $request->id_kolam)
            ->first();

        if (!$kolam) {
            return response()->json([
                'success' => false,
                'message' => 'Kolam hatchery tidak ditemukan!'
            ], 422);
        }

        // Cek apakah kolam sudah terisi batch aktif lain
        $isOccupied = BatchPembibitan::where('id_kolam', $kolam->id_kolam)
            ->where('status', '!=', 'selesai')
            ->where('status', '!=', 'gagal')
            ->exists();

        if ($isOccupied) {
            return response()->json([
                'success' => false,
                'message' => "Kolam '{$kolam->nama_kolam}' saat ini masih terisi batch pembibitan aktif. Silakan pilih kolam lain!"
            ], 422);
        }

        $idIkan = $request->id_ikan ?: null;
        $jenisIkan = $request->jenis_ikan;
        if ($idIkan && !$jenisIkan) {
            $ik = \App\Models\Ikan::find($idIkan);
            if ($ik) $jenisIkan = $ik->nama_ikan;
        }

        $batch = BatchPembibitan::create([
            'id_kolam'             => $kolam->id_kolam,
            'id_user'              => Auth::id() ?? 1,
            'id_ikan'              => $idIkan,
            'jenis_ikan'           => $jenisIkan ?: 'Bibit Ikan',
            'tgl_pemijahan'        => $request->tgl_pemijahan ? Carbon::parse($request->tgl_pemijahan)->toDateString() : ($request->tgl_tebar ? Carbon::parse($request->tgl_tebar)->toDateString() : now()->toDateString()),
            'jumlah_bibitAwal'     => (int) ($request->jumlah_bibitAwal ?? 100000),
            'jumlah_kematian'      => 0,
            'status'               => 'aktif',
        ]);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => "Batch pembibitan di {$kolam->nama_kolam} berhasil disimpan!",
                'batch'   => $batch
            ]);
        }

        return redirect()->route('petugas.pembibitan.dashboard')->with('success', 'Batch pembibitan baru berhasil disimpan!');
    }

    /**
     * Halaman Log Konsumsi Pakan Pembibitan (Mobile Web Petugas).
     */
    public function logPakan(Request $request)
    {
        // 1. Ambil Batch Pembibitan Aktif
        $activeBatches = BatchPembibitan::with(['kolam', 'ikan'])
            ->where('status', '!=', 'selesai')
            ->where('status', '!=', 'gagal')
            ->latest('id_batch')
            ->get();

        // 2. Ambil Master Stok Pakan khusus Pembibitan & Semua
        $stokPakanList = StokPakan::whereIn('kategori_peruntukan', ['pembibitan', 'semua'])->get();

        // 3. Ambil Riwayat Log Pakan Pembibitan Terkini
        $logs = ManajemenPakan::with(['kolam', 'stokPakan', 'user'])
            ->where('kategori_fase', 'pembibitan')
            ->latest('tgl_log')
            ->take(15)
            ->get();

        return view('mobile_web_petugas.petugas_pembibitan.log_pakan', compact('activeBatches', 'stokPakanList', 'logs'));
    }

    /**
     * Simpan Log Pakan Pembibitan & Otomatis Potong Saldo Stok
     */
    public function storeLogPakan(Request $request)
    {
        $request->validate([
            'id_kolam'      => 'required|exists:kolam,id_kolam',
            'id_stok_pakan' => 'nullable|exists:stok_pakan,id_stok_pakan',
            'tgl_log'       => 'nullable|date',
            'kg_pelet'      => 'required|numeric|min:0.01|max:100',
            'total_biaya'   => 'nullable|numeric|min:0',
            'ph_air'        => 'nullable|numeric|min:0|max:14',
        ], [
            'kg_pelet.max'  => 'Pemberian pakan benih maksimal 100 kg per sesi.',
        ]);

        // Validasi: Kolam harus memiliki batch pembibitan yang sedang aktif
        $hasActiveBatch = BatchPembibitan::where('id_kolam', $request->id_kolam)
            ->where('status', '!=', 'selesai')
            ->where('status', '!=', 'gagal')
            ->exists();

        if (!$hasActiveBatch) {
            return response()->json([
                'success' => false,
                'message' => 'Kolam ini belum diisi benih aktif. Anda harus mencatat batch pemijahan terlebih dahulu!'
            ], 422);
        }

        $tgl = $request->tgl_log ? Carbon::parse($request->tgl_log)->toDateString() : Carbon::today()->toDateString();
        $kgPakan = (float) $request->kg_pelet;

        $stokItem = $request->id_stok_pakan ? StokPakan::find($request->id_stok_pakan) : null;
        $hargaPerKg = $stokItem ? (float) $stokItem->harga_per_satuan : 15000;
        $totalBiaya = (float) ($request->total_biaya ?: ($kgPakan * $hargaPerKg));

        $log = ManajemenPakan::create([
            'id_user'       => Auth::id() ?? 1,
            'id_kolam'      => $request->id_kolam,
            'id_stok_pakan' => $request->id_stok_pakan,
            'kategori_fase' => 'pembibitan',
            'tgl_log'       => $tgl,
            'kg_pelet'      => $kgPakan,
            'kg_daun'       => 0,
            'total_biaya'   => $totalBiaya,
            'ph_air'        => $request->ph_air ?? 7.0,
        ]);

        // POTONG STOK OTOMATIS
        if ($stokItem && $kgPakan > 0) {
            $stokItem->update([
                'stok_tersisa' => max(0, (float) $stokItem->stok_tersisa - $kgPakan)
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
                'message' => 'Log pakan pembibitan berhasil dicatat & stok terpotong!',
                'log'     => $log->load(['kolam', 'stokPakan'])
            ]);
        }

        return redirect()->route('petugas.pembibitan.log-pakan')->with('success', 'Log pakan pembibitan berhasil dicatat & stok terpotong!');
    }
}

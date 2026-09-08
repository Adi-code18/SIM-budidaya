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

        $today = Carbon::today()->toDateString();
        $fedTodayKolamIds = ManajemenPakan::whereDate('tgl_log', $today)->pluck('id_kolam')->toArray();

        return view('mobile_web_petugas.petugas_pembibitan.index', compact('batches', 'totalBenih', 'totalTank', 'srRate', 'avgPh', 'totalAwal', 'fedTodayKolamIds'));
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
        $ik = null;
        if ($idIkan) {
            $ik = \App\Models\Ikan::find($idIkan);
            if ($ik && !$jenisIkan) $jenisIkan = $ik->nama_ikan;
        }

        $tglPemijahan = $request->tgl_pemijahan ? Carbon::parse($request->tgl_pemijahan)->toDateString() : ($request->tgl_tebar ? Carbon::parse($request->tgl_tebar)->toDateString() : now()->toDateString());
        $sopDays = $ik ? (($ik->durasi_penetasan ?? 14) + ($ik->durasi_pembibitan ?? 30)) : 44;
        $estPrcs = $request->est_prcs_pembibitaan ?: Carbon::parse($tglPemijahan)->addDays($sopDays)->toDateString();

        $batch = BatchPembibitan::create([
            'id_kolam'             => $kolam->id_kolam,
            'id_user'              => Auth::id() ?? 1,
            'id_ikan'              => $idIkan,
            'jenis_ikan'           => $jenisIkan ?: 'Bibit Ikan',
            'tgl_pemijahan'        => $tglPemijahan,
            'est_prcs_pembibitaan' => $estPrcs,
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
        $now = Carbon::now();

        // 1. Ambil Batch Pembibitan Aktif dengan Sinkronisasi Presisi (DOC, Fase DB, & Spesies)
        $activeBatches = BatchPembibitan::with(['kolam', 'ikan'])
            ->where('status', '!=', 'selesai')
            ->where('status', '!=', 'gagal')
            ->latest('id_batch')
            ->get()
            ->map(function ($b) use ($now) {
                // Perhitungan usia hari sinkron dengan modul Manajer
                $days = $b->tgl_pemijahan ? (int) abs(Carbon::parse($b->tgl_pemijahan)->startOfDay()->diffInDays($now->copy()->startOfDay())) : 0;
                $doc = $days;

                // Biologically accurate phase based on age
                if ($doc <= 3) {
                    $fase = 'Telur';
                    $faseKey = 'telur';
                    $faseBadge = 'bg-amber-100 text-amber-800 border-amber-200';
                    $rekomendasiPakan = 'Tanpa Pakan (Fase Telur/Inkubasi)';
                    $estKg = 0;
                } elseif ($doc <= 14) {
                    $fase = 'Larva';
                    $faseKey = 'larva';
                    $faseBadge = 'bg-sky-100 text-sky-800 border-sky-200';
                    $rekomendasiPakan = 'Cacing Sutra Segar / Artemia';
                    $estKg = 0.5;
                } else {
                    $fase = 'Benih / Fingerling';
                    $faseKey = 'fingerling';
                    $faseBadge = 'bg-emerald-100 text-emerald-800 border-emerald-200';
                    $rekomendasiPakan = 'Pelet Mikro Starter Benih (PF-500)';
                    $estKg = 1.0;
                }

                $namaSpesies = $b->ikan ? $b->ikan->nama_ikan : ($b->jenis_ikan ?: 'Bibit Ikan');

                $b->doc = $doc;
                $b->fase = $fase;
                $b->fase_key = $faseKey;
                $b->fase_badge = $faseBadge;
                $b->rekomendasi_pakan = $rekomendasiPakan;
                $b->est_pakan_kg = $estKg;
                $b->spesies = $namaSpesies;
                $b->jenis_ikan = $namaSpesies;
                $b->label = ($b->kolam ? $b->kolam->nama_kolam : 'Kolam #' . $b->id_kolam) . ' – Hari ke-' . $doc . ' (DOC ' . $doc . ') • ' . $fase . ' (' . $namaSpesies . ')';

                return $b;
            });

        // 2. Ambil Riwayat Pencatatan Log Pakan Pembibitan
        $logs = ManajemenPakan::with(['kolam', 'user', 'stokPakan'])
            ->where('kategori_fase', 'pembibitan')
            ->latest('tgl_log')
            ->latest('id_manajemen_pakan')
            ->take(20)
            ->get();

        // 3. Stok Pakan Starter Pembibitan
        $stokPakanList = StokPakan::where('stok_kg', '>', 0)
            ->where(function ($q) {
                $q->where('jenis_pakan', 'like', '%Starter%')
                  ->orWhere('jenis_pakan', 'like', '%PF%')
                  ->orWhere('jenis_pakan', 'like', '%Feng%')
                  ->orWhere('jenis_pakan', 'like', '%Cacing%')
                  ->orWhere('jenis_pakan', 'like', '%Artemia%')
                  ->orWhere('jenis_pakan', 'like', '%Benih%')
                  ->orWhere('jenis_pakan', 'like', '%Pelet%');
            })
            ->get();

        if ($stokPakanList->isEmpty()) {
            $stokPakanList = StokPakan::where('stok_kg', '>', 0)->get();
        }

        return view('mobile_web_petugas.petugas_pembibitan.log_pakan', compact('activeBatches', 'logs', 'stokPakanList'));
    }

    /**
     * Simpan Pencatatan Log Pakan Pembibitan Baru via AJAX / POST.
     */
    public function storeLogPakan(Request $request)
    {
        $request->validate([
            'id_kolam'       => 'required|exists:kolam,id_kolam',
            'id_stok_pakan'  => 'nullable|exists:stok_pakan,id_stok_pakan',
            'kg_pelet'       => 'required|numeric|min:0.01',
            'tgl_log'        => 'nullable|date',
            'waktu_pemberian'=> 'nullable|string',
            'total_biaya'    => 'nullable|numeric|min:0',
        ], [
            'id_kolam.required' => 'Pilih kolam pemeliharaan benih.',
            'kg_pelet.required' => 'Jumlah pakan (kg) wajib diisi.',
            'kg_pelet.min'      => 'Jumlah pakan minimal 0.01 kg.',
        ]);

        // Cari batch aktif di kolam ini
        $activeBatch = BatchPembibitan::where('id_kolam', $request->id_kolam)
            ->where('status', '!=', 'selesai')
            ->where('status', '!=', 'gagal')
            ->latest('id_batch')
            ->first();

        if (!$activeBatch) {
            return response()->json([
                'success' => false,
                'message' => 'Kolam ini belum diisi benih aktif. Anda harus mencatat batch pemijahan terlebih dahulu!'
            ], 422);
        }

        // Validasi Fase: Hanya batch usia <= 3 hari (Telur) yang tidak boleh diberi pakan
        $days = $activeBatch->tgl_pemijahan ? (int) abs(Carbon::parse($activeBatch->tgl_pemijahan)->startOfDay()->diffInDays(Carbon::now()->startOfDay())) : 0;
        $faseBatch = strtoupper(trim($activeBatch->fase_pertumbuhan ?? ''));
        $isTelur = ($days <= 3) && (in_array($faseBatch, ['TELUR', 'INKUBASI']) || str_contains($faseBatch, 'TELUR') || $faseBatch === '');

        if ($isTelur) {
            return response()->json([
                'success' => false,
                'message' => 'Kolam ini masih dalam fase Telur/Inkubasi (DOC ' . $days . ') dan belum dapat diberi pakan pelet/cacing agar kualitas air penetasan tidak rusak!'
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

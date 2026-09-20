<?php

namespace App\Http\Controllers\Manajer;

use App\Http\Controllers\Controller;
use App\Models\BatchPembesaran;
use App\Models\Keuangan;
use App\Models\Kolam;
use App\Models\ManajemenPakan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class PembesaranController extends Controller
{
    public function index()
    {
        $batchRecords = BatchPembesaran::with(['kolam', 'user', 'batchPembibitan.kolam'])
            ->where('status_siklus', '!=', 'selesai')
            ->where('status_siklus', '!=', 'gagal')
            ->where('biomassa_est', '>', 0)
            ->latest('id_pembesaran')
            ->get();
        
        // Load Pembesaran ponds, holding ponds, and stock buffer ponds
        $kolams = Kolam::where(function ($q) {
            $q->where('tipe_kolam', 'like', '%Pembesaran%')
              ->orWhere('tipe_kolam', 'like', '%Pemberokan%')
              ->orWhere('tipe_kolam', 'like', '%Penampungan%')
              ->orWhere('nama_kolam', 'like', '%Stok%');
        })->get();
        
        // List dedicated for stock / buffer / holding destination
        $kolamStokList = $kolams->map(function ($k) {
            $isStok = stripos($k->nama_kolam, 'Stok') !== false || stripos($k->tipe_kolam, 'Pemberokan') !== false || stripos($k->tipe_kolam, 'Penampungan') !== false;
            $tipeShort = str_ireplace(['Kolam Pembesaran ', 'Kolam '], '', $k->tipe_kolam);
            return [
                'id_kolam'   => $k->id_kolam,
                'nama_kolam' => $k->nama_kolam,
                'tipe_kolam' => $k->tipe_kolam,
                'kapasitas'  => $k->kapasitas,
                'is_stok'    => $isStok,
                'label'      => $k->nama_kolam . ' (' . $tipeShort . ' • Kap: ' . number_format($k->kapasitas, 0, ',', '.') . ' Ekor)' . ($isStok ? ' ★' : ''),
            ];
        })->sortByDesc('is_stok')->values();
        
        // Find which ponds are currently occupied by active batches
        $activeBatchKolamIds = BatchPembesaran::where('status_siklus', '!=', 'selesai')
            ->where('status_siklus', '!=', 'gagal')
            ->where(function ($q) {
                $q->where('biomassa_est', '>', 0)
                  ->orWhereIn('status_siklus', ['berjalan', 'aktif', 'siap_panen']);
            })
            ->pluck('id_kolam')->toArray();

        $activeKolamIdsFromStatus = Kolam::where('status', 'aktif')->pluck('id_kolam')->toArray();
        $allOccupiedKolamIds = array_unique(array_merge($activeBatchKolamIds, $activeKolamIdsFromStatus));

        $kolamList = $kolams->map(function ($k) use ($allOccupiedKolamIds) {
            $isOccupied = in_array($k->id_kolam, $allOccupiedKolamIds);
            return [
                'id_kolam'    => $k->id_kolam,
                'nama_kolam'  => $k->nama_kolam,
                'tipe_kolam'  => $k->tipe_kolam,
                'kapasitas'   => $k->kapasitas,
                'is_occupied' => $isOccupied,
                'ph_air'      => $k->kesehatan_ph_air ?? '7.2',
            ];
        });

        $totalBiomassaKg = BatchPembesaran::where('status_siklus', '!=', 'selesai')
            ->where('status_siklus', '!=', 'gagal')
            ->where('biomassa_est', '>', 0)
            ->sum('biomassa_est');
        $totalBiomassa = $totalBiomassaKg / 1000; // in Ton

        $today = Carbon::today()->toDateString();
        $fedTodayKolamIds = \App\Models\ManajemenPakan::whereDate('tgl_log', $today)->pluck('id_kolam')->toArray();

        $batches = [];
        $fcrAccum = [];
        foreach ($batchRecords as $b) {
            $doc = $b->tgl_tebar ? (int) abs(Carbon::parse($b->tgl_tebar)->startOfDay()->diffInDays(now()->startOfDay())) : 0;
            $targetPercent = $b->target_panen_kg > 0 ? min(100, round(($b->biomassa_est / $b->target_panen_kg) * 100)) : 0;

            $ikanRef = $b->ikan_ref;
            $fcrMin = $ikanRef ? (float)$ikanRef->fcr_min : 1.00;
            $fcrMax = $ikanRef ? (float)$ikanRef->fcr_max : 1.30;
            $targetKonsumsi = $ikanRef ? $ikanRef->target_konsumsi : '-';
            $jenisPakan = $ikanRef ? $ikanRef->jenis_pakan_didukung : 'Pelet';

            $fcrKumulatif = $b->calculateCumulativeFcr();
            $fcrKomersial = $b->calculateCommercialFcr();
            $fcrBiologis = $b->calculateBiologicalFcr();

            if ($fcrKumulatif !== null) {
                $fcrAccum[] = $fcrKumulatif;
                $isOptimal = ($fcrKumulatif <= $fcrMax);
                $fcrStatusText = $isOptimal ? 'Optimal (Sesuai SOP)' : 'Tinggi (Di Luar SOP)';
                $fcrDisplay = number_format($fcrKumulatif, 2);
            } else {
                $isOptimal = true;
                $fcrStatusText = 'Belum Ada Log Pakan';
                $fcrDisplay = '0.00';
            }

            // Analisis Finansial Laba / Rugi Kolam
            $fin = $b->calculateFinancials();

            $statusSiklus = strtolower($b->status_siklus ?? 'berjalan');
            $statusLabel = 'Berjalan (Aktif)';
            $statusClass = 'bg-emerald-100 text-emerald-800';

            if ($statusSiklus === 'siap_panen') {
                $statusLabel = 'Siap Panen';
                $statusClass = 'bg-amber-100 text-amber-800';
            } elseif ($statusSiklus === 'selesai') {
                $statusLabel = 'Selesai Panen';
                $statusClass = 'bg-slate-100 text-slate-700';
            }

            $cleanJenis = $b->jenis_ikan;
            if (stripos($cleanJenis, 'Ikan ') === 0) {
                $cleanJenis = substr($cleanJenis, 5);
            }

            $isFedToday = in_array($b->id_kolam, $fedTodayKolamIds);

            $bibitList = [];
            if ($b->batchPembibitan) {
                $bp = $b->batchPembibitan;
                $sisaBibit = max(0, $bp->jumlah_bibitAwal - $bp->jumlah_kematian);
                $bibitList[] = [
                    'id_batch'         => '#BT-' . str_pad($bp->id_batch, 5, '0', STR_PAD_LEFT),
                    'is_beli_luar'     => false,
                    'kolam_asal'       => $bp->kolam ? $bp->kolam->nama_kolam : 'Kolam Hatchery',
                    'tipe_kolam_asal'  => 'Hatchery Internal',
                    'jenis_ikan'       => $bp->jenis_ikan,
                    'fase'             => $bp->fase_pertumbuhan ?? 'FINGERLING',
                    'tgl_pemijahan'    => $bp->tgl_pemijahan ? Carbon::parse($bp->tgl_pemijahan)->translatedFormat('d M Y') : '-',
                    'jumlah_bibit'     => number_format($sisaBibit, 0, ',', '.'),
                    'total_bobot_kg'   => number_format($bp->total_bobot_kg > 0 ? $bp->total_bobot_kg : $b->biomassa_est, 1, ',', '.'),
                    'status'           => 'Dipindahkan',
                ];
            } else {
                $isBeliLuar = ($b->asal_bibit === 'beli_luar');
                $bibitList[] = [
                    'id_batch'         => $isBeliLuar ? 'Pengadaan Eksternal' : 'Tebar Mandiri',
                    'is_beli_luar'     => $isBeliLuar,
                    'kolam_asal'       => $isBeliLuar ? 'Supplier Bibit Luar' : ($b->kolam ? $b->kolam->nama_kolam : 'Kolam Pembesaran'),
                    'tipe_kolam_asal'  => $isBeliLuar ? ('Tebar ke ' . ($b->kolam ? $b->kolam->nama_kolam : 'Kolam')) : 'Input Mandiri',
                    'jenis_ikan'       => $b->jenis_ikan,
                    'fase'             => 'FINGERLING',
                    'tgl_pemijahan'    => $b->tgl_tebar ? Carbon::parse($b->tgl_tebar)->translatedFormat('d M Y') : '-',
                    'jumlah_bibit'     => number_format(round($b->biomassa_est * 40), 0, ',', '.'),
                    'total_bobot_kg'   => number_format($b->biomassa_est, 1, ',', '.'),
                    'status'           => 'Aktif di Pembesaran',
                ];
            }

            // Hitung estimasi panen berdasarkan SOP Master Ikan (bulan_panen_min s/d bulan_panen_max)
            $estBulanMin = $ikanRef && $ikanRef->bulan_panen_min ? (float)$ikanRef->bulan_panen_min : 2.5;
            $estBulanMax = $ikanRef && $ikanRef->bulan_panen_max ? (float)$ikanRef->bulan_panen_max : 3.0;
            $estDays = round($estBulanMax * 30);
            $estDaysMin = round($estBulanMin * 30);

            $estTglPanen = $b->est_tgl_panen ?? ($b->tgl_tebar ? Carbon::parse($b->tgl_tebar)->addDays($estDays)->toDateString() : null);
            $isHarvestDue = ($statusSiklus !== 'selesai') && ($estTglPanen ? Carbon::today()->gte(Carbon::parse($estTglPanen)) : ($doc >= $estDaysMin));

            // Active order linked to this batch
            $activeOrder = \App\Models\TransaksiDistribusi::with('mitra')
                ->where('id_pembesaran', $b->id_pembesaran)
                ->where('status_order', '!=', 'selesai')
                ->latest('id_transaksi')
                ->first();

            $orderTargetKg = $activeOrder ? (float)$activeOrder->Total_kg : 0;
            $orderMitraNama = $activeOrder && $activeOrder->mitra ? $activeOrder->mitra->nama_mitra : null;

            $batches[] = [
                'id_pembesaran'       => $b->id_pembesaran,
                'id'                  => '#PB-' . str_pad($b->id_pembesaran, 5, '0', STR_PAD_LEFT),
                'id_kolam'            => $b->id_kolam,
                'nama_kolam'          => $b->kolam ? $b->kolam->nama_kolam : 'Kolam #' . $b->id_kolam,
                'tipe_kolam'          => $b->kolam ? $b->kolam->tipe_kolam : 'Pembesaran',
                'kapasitas_kolam'     => $b->kolam ? number_format($b->kolam->kapasitas, 0, ',', '.') : '0',
                'id_batch_pembibitan' => $b->id_batch_pembibitan ? ('#BT-' . str_pad($b->id_batch_pembibitan, 5, '0', STR_PAD_LEFT)) : null,
                'asal_pembibitan'     => $b->batchPembibitan ? ('#BT-' . str_pad($b->id_batch_pembibitan, 5, '0', STR_PAD_LEFT)) : 'Input Manual (Bukan Bibit)',
                'bibit_list'          => $bibitList,
                'is_fed_today'        => $isFedToday,
                'fed_status_label'    => $isFedToday ? 'Sudah Diberi Pakan' : 'Belum Diberi Pakan',
                'tgl_tebar'           => $b->tgl_tebar,
                'tgl_tebar_format'    => $b->tgl_tebar ? Carbon::parse($b->tgl_tebar)->translatedFormat('d M Y') : '-',
                'est_tgl_panen'       => $estTglPanen,
                'est_panen_format'    => $estTglPanen ? Carbon::parse($estTglPanen)->translatedFormat('d M Y') : '-',
                'est_siklus_panen'    => "{$estBulanMin} – {$estBulanMax} Bulan",
                'is_harvest_due'      => $isHarvestDue,
                'order_target_kg'     => $orderTargetKg,
                'order_mitra'         => $orderMitraNama,
                'doc'                 => $doc,
                'jenis_ikan'          => $b->jenis_ikan,
                'clean_jenis'         => $cleanJenis,
                'biomassa_est'        => (float) $b->biomassa_est,
                'biomassa_format'     => number_format($b->biomassa_est, 1, ',', '.'),
                'target_panen_kg'     => (float) $b->target_panen_kg,
                'target_format'       => number_format($b->target_panen_kg, 1, ',', '.'),
                'target_percent'      => $targetPercent,
                'jumlah_panen_kg'     => (float) $b->jumlah_panen_kg,
                'jumlah_panen_format' => number_format($b->jumlah_panen_kg, 1, ',', '.'),
                
                // 4 Jenis Nilai FCR Sesuai SOP
                'fcr'                 => $fcrDisplay,
                'fcr_kumulatif'       => $fcrKumulatif !== null ? number_format($fcrKumulatif, 2) : '-',
                'fcr_komersial'       => $fcrKomersial !== null ? number_format($fcrKomersial, 2) : '-',
                'fcr_biologis'        => $fcrBiologis !== null ? number_format($fcrBiologis, 2) : '-',
                'fcr_target'          => number_format($fcrMin, 1) . ' – ' . number_format($fcrMax, 1),
                'fcr_min'             => $fcrMin,
                'fcr_max'             => $fcrMax,
                'fcr_status_text'     => $fcrStatusText,
                'is_optimal'          => $isOptimal,
                'target_konsumsi'     => $targetKonsumsi,
                'jenis_pakan'         => $jenisPakan,
                'status_siklus'       => $statusSiklus,
                'status_label'        => $statusLabel,
                'status_class'        => $statusClass,
                'asal_bibit'          => $b->asal_bibit ?? ($b->id_batch_pembibitan ? 'pembibitan_sendiri' : 'beli_luar'),
                'ph_air'              => ($logPh = ManajemenPakan::where('id_kolam', $b->id_kolam)->whereNotNull('ph_air')->where('ph_air', '>', 0)->latest('tgl_log')->value('ph_air')) ? number_format($logPh, 1) : '-',

                // Data Finansial Laba / Rugi Kolam
                'total_pakan_kg'      => number_format($fin['total_pakan_kg'], 1, ',', '.'),
                'biaya_pakan'         => (float) $fin['biaya_pakan'],
                'biaya_pakan_format'  => 'Rp ' . number_format($fin['biaya_pakan'], 0, ',', '.'),
                'biaya_bibit'         => (float) $fin['biaya_bibit'],
                'biaya_bibit_format'  => 'Rp ' . number_format($fin['biaya_bibit'], 0, ',', '.'),
                'biaya_operasional'   => (float) $fin['biaya_operasional'],
                'biaya_op_format'     => 'Rp ' . number_format($fin['biaya_operasional'], 0, ',', '.'),
                'total_biaya_kolam'   => (float) $fin['total_biaya_kolam'],
                'total_biaya_format'  => 'Rp ' . number_format($fin['total_biaya_kolam'], 0, ',', '.'),
                'harga_jual_per_kg'   => (float) $fin['harga_jual_per_kg'],
                'harga_jual_format'   => 'Rp ' . number_format($fin['harga_jual_per_kg'], 0, ',', '.'),
                'pendapatan_estimasi' => (float) $fin['pendapatan_estimasi'],
                'pendapatan_format'   => 'Rp ' . number_format($fin['pendapatan_estimasi'], 0, ',', '.'),
                'laba_rugi'           => (float) $fin['laba_rugi'],
                'laba_rugi_format'    => ($fin['laba_rugi'] >= 0 ? '+Rp ' : '-Rp ') . number_format(abs($fin['laba_rugi']), 0, ',', '.'),
                'margin_percent'      => $fin['margin_percent'],
                'status_finansial'    => $fin['status_finansial'],
                'status_fin_label'    => $fin['status_label'],
                'status_fin_badge'    => $fin['status_badge'],
            ];
        }

        $avgFcrVal = count($fcrAccum) > 0 ? (array_sum($fcrAccum) / count($fcrAccum)) : BatchPembesaran::whereNotNull('fcr')->where('fcr', '>', 0)->avg('fcr');
        $avgFcr = $avgFcrVal ? round((float)$avgFcrVal, 2) : 0;

        // Ringkasan Finansial Keseluruhan Pembesaran
        $totalModalSemuaKolam = array_sum(array_column($batches, 'total_biaya_kolam'));
        $totalProyeksiOmset   = array_sum(array_column($batches, 'pendapatan_estimasi'));
        $totalProyeksiLaba    = $totalProyeksiOmset - $totalModalSemuaKolam;
        $totalKolamUntung     = count(array_filter($batches, fn($x) => $x['laba_rugi'] > 0));
        $totalKolamRugi       = count(array_filter($batches, fn($x) => $x['laba_rugi'] < 0));

        $financialSummary = [
            'total_modal_kolam'   => $totalModalSemuaKolam,
            'total_modal_format'  => 'Rp ' . number_format($totalModalSemuaKolam, 0, ',', '.'),
            'total_omset'         => $totalProyeksiOmset,
            'total_omset_format'  => 'Rp ' . number_format($totalProyeksiOmset, 0, ',', '.'),
            'total_laba'          => $totalProyeksiLaba,
            'total_laba_format'   => ($totalProyeksiLaba >= 0 ? '+Rp ' : '-Rp ') . number_format(abs($totalProyeksiLaba), 0, ',', '.'),
            'kolam_untung_count'  => $totalKolamUntung,
            'kolam_rugi_count'    => $totalKolamRugi,
        ];

        $availablePembibitan = \App\Models\BatchPembibitan::with(['kolam', 'batchPembesaran', 'ikan'])
            ->where('status', '!=', 'gagal')
            ->where('status', '!=', 'selesai')
            ->whereDoesntHave('batchPembesaran')
            ->where(function ($q) {
                $q->whereIn('fase_pertumbuhan', ['BENIH', 'FINGERLING', 'siap_pindah'])
                  ->orWhere('fase_pertumbuhan', 'like', '%FINGERLING%')
                  ->orWhere('fase_pertumbuhan', 'like', '%BENIH%')
                  ->orWhere('status', 'siap_pindah');
            })
            ->whereNotIn('fase_pertumbuhan', ['TELUR', 'INKUBASI', 'LARVA'])
            ->latest('id_batch')
            ->get()
            ->map(function ($bp) {
                $sisa = max(0, $bp->jumlah_bibitAwal - $bp->jumlah_kematian);
                $statusText = $bp->status === 'siap_pindah' ? 'Siap Pindah' : ($bp->status === 'selesai' ? 'Selesai' : ucfirst($bp->status));
                $namaIkan = $bp->jenis_ikan ?: ($bp->ikan ? $bp->ikan->nama_ikan : 'Ikan Lele');
                $cleanJenis = preg_replace('/^Ikan\s+/i', '', $namaIkan);
                $faseLabel = $bp->fase_pertumbuhan ?: 'FINGERLING';
                return [
                    'id_batch'         => $bp->id_batch,
                    'label'            => '#BB-' . str_pad($bp->id_batch, 5, '0', STR_PAD_LEFT) . ' - ' . $namaIkan . ' (' . number_format($sisa, 0, ',', '.') . ' Ekor - Fase ' . $faseLabel . ')',
                    'jenis_ikan'       => $namaIkan,
                    'clean_jenis'      => $cleanJenis,
                    'sisa_ekor'        => $sisa,
                    'est_biomassa'     => round($sisa * 0.02, 1),
                    'fase'             => $faseLabel,
                    'status'           => $bp->status,
                ];
            });

        $ikans = \App\Models\Ikan::orderBy('nama_ikan', 'asc')->get();

        return view('layouts.pembesaran.index', compact('batches', 'kolamList', 'kolams', 'kolamStokList', 'totalBiomassa', 'avgFcr', 'financialSummary', 'availablePembibitan', 'ikans'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'id_kolam'             => 'required',
            'jenis_ikan'           => 'required|string',
            'id_batch_pembibitan'  => 'nullable|numeric',
            'biaya_beli_bibit'     => 'nullable|numeric|min:0',
            'jumlah_bibit'         => 'nullable|numeric|min:1',
            'survival_rate'        => 'nullable|numeric|min:10|max:100',
            'tgl_tebar'            => 'nullable|date',
            'est_tgl_panen'        => 'nullable|date',
            'biomassa_est'         => 'nullable|numeric|min:0.1',
            'target_panen_kg'      => 'nullable|numeric|min:0.1',
            'fcr'                  => 'nullable|numeric|min:0.5',
            'status_siklus'        => 'nullable|string',
        ]);

        $kolam = Kolam::where('nama_kolam', $request->id_kolam)->orWhere('id_kolam', $request->id_kolam)->first();

        if (!$kolam) {
            return response()->json(['success' => false, 'message' => 'Kolam pembesaran tidak ditemukan.'], 404);
        }

        // Check if pond is already occupied by an active running batch
        $statusSiklus = strtolower($request->status_siklus ?? 'berjalan');
        if ($statusSiklus === 'berjalan') {
            $occupied = BatchPembesaran::where('id_kolam', $kolam->id_kolam)
                ->where('status_siklus', 'berjalan')
                ->exists();

            if ($occupied) {
                return response()->json([
                    'success' => false,
                    'message' => "Kolam {$kolam->nama_kolam} saat ini sedang aktif digunakan oleh batch pembesaran lain! Silakan pilih kolam yang tersedia."
                ], 422);
            }
        }

        // Validasi: Jika mengambil dari pembibitan, pastikan batch pembibitan sudah fase Fingerling / Benih
        $jumlahBibitAwal = (float) ($request->jumlah_bibit ?? 0);
        if ($request->filled('id_batch_pembibitan')) {
            $sourceBatch = \App\Models\BatchPembibitan::find($request->id_batch_pembibitan);
            if ($sourceBatch) {
                $fase = strtoupper(trim($sourceBatch->fase_pertumbuhan ?? ''));
                if (in_array($fase, ['TELUR', 'INKUBASI', 'LARVA'])) {
                    return response()->json([
                        'success' => false,
                        'message' => "Batch pembibitan #BB-" . str_pad($sourceBatch->id_batch, 5, '0', STR_PAD_LEFT) . " masih dalam fase {$fase}! Bibit belum dapat dipindahkan ke kolam pembesaran sebelum memasuki fase FINGERLING / BENIH."
                    ], 422);
                }
                if ($jumlahBibitAwal <= 0) {
                    $jumlahBibitAwal = max(0, (float)($sourceBatch->jumlah_bibitAwal - $sourceBatch->jumlah_kematian));
                }
            }
        }

        $jenis = $request->jenis_ikan;
        if (stripos($jenis, 'Ikan ') !== 0) {
            $jenis = 'Ikan ' . $jenis;
        }

        $ikanRef = \App\Models\Ikan::where('nama_ikan', 'LIKE', '%' . trim(preg_replace('/^(ikan\s+)/i', '', $jenis)) . '%')->first();
        $defaultFcr = $ikanRef ? (float)$ikanRef->fcr_min : 1.15;
        $estBulan = $ikanRef && $ikanRef->bulan_panen_max ? (float)$ikanRef->bulan_panen_max : 3.0;
        $ekorPerKg = $ikanRef ? $ikanRef->avg_ekor_per_kg : 4.0;

        $tglTebar = $request->tgl_tebar ?? now();
        $estDays = round($estBulan * 30);
        $estTglPanen = $request->est_tgl_panen ?? ($request->tgl_tebar ? Carbon::parse($request->tgl_tebar)->addDays($estDays)->toDateString() : now()->addDays($estDays)->toDateString());

        $asalBibit = $request->filled('id_batch_pembibitan') ? 'pembibitan_sendiri' : 'beli_luar';
        $biayaBeliBibit = $asalBibit === 'beli_luar' ? (float) ($request->biaya_beli_bibit ?? 0) : 0.0;

        $srPercent = (float) ($request->survival_rate ?? 85.0);
        if ($srPercent <= 0) $srPercent = 85.0;

        // Otomatisasi Target Panen (Kg) jika tidak diisi atau 0
        if ($request->filled('target_panen_kg') && (float)$request->target_panen_kg > 0) {
            $targetPanenKg = (float) $request->target_panen_kg;
        } elseif ($jumlahBibitAwal > 0) {
            $targetPanenKg = \App\Models\Ikan::calculateTargetPanen($jumlahBibitAwal, $srPercent, $ekorPerKg);
        } else {
            $targetPanenKg = 500.0;
        }

        // Otomatisasi Biomassa Awal (Kg) jika tidak diisi atau 0
        if ($request->filled('biomassa_est') && (float)$request->biomassa_est > 0) {
            $biomassaEst = (float) $request->biomassa_est;
        } elseif ($jumlahBibitAwal > 0) {
            $biomassaEst = round($jumlahBibitAwal * 0.015, 1);
        } else {
            $biomassaEst = round($targetPanenKg * 0.1, 1);
        }

        $batch = BatchPembesaran::create([
            'id_kolam'            => $kolam->id_kolam,
            'id_user'             => Auth::id() ?? 1,
            'id_batch_pembibitan' => $request->id_batch_pembibitan ?: null,
            'asal_bibit'          => $asalBibit,
            'biaya_beli_bibit'    => $biayaBeliBibit,
            'tgl_tebar'           => $tglTebar,
            'est_tgl_panen'       => $estTglPanen,
            'biomassa_est'        => $biomassaEst,
            'fcr'                 => $request->fcr ?? $defaultFcr,
            'target_panen_kg'     => $targetPanenKg,
            'jumlah_panen_kg'     => 0.00,
            'jenis_ikan'          => $jenis,
            'status_siklus'       => $statusSiklus,
        ]);

        // If derived from Pembibitan, mark that pembibitan batch as selesai
        if ($request->filled('id_batch_pembibitan')) {
            \App\Models\BatchPembibitan::where('id_batch', $request->id_batch_pembibitan)->update([
                'status'           => 'selesai',
                'fase_pertumbuhan' => 'FINGERLING',
            ]);
        }

        // OTOMATISASI KAS KELUAR: Jika Beli Bibit Luar dan ada biaya pembelian bibit
        if ($asalBibit === 'beli_luar' && $biayaBeliBibit > 0) {
            $pbRef = 'BELI-BIBIT-PB-' . str_pad($batch->id_pembesaran, 4, '0', STR_PAD_LEFT);
            \App\Models\Keuangan::updateOrCreate(
                ['ref_id' => $pbRef],
                [
                    'id_user'           => Auth::id() ?? 1,
                    'id_kolam'          => $kolam->id_kolam,
                    'tanggal_transaksi' => Carbon::parse($tglTebar)->toDateString(),
                    'tipe_transaksi'    => 'pengeluaran',
                    'kategori'          => 'Pembelian Bibit ' . $jenis,
                    'nominal'           => $biayaBeliBibit,
                    'keterangan'        => "Pembelian bibit luar {$jenis} (" . number_format($batch->biomassa_est, 1, ',', '.') . " kg) untuk tebar di {$kolam->nama_kolam} (#PB-" . str_pad($batch->id_pembesaran, 5, '0', STR_PAD_LEFT) . ")",
                ]
            );
        }

        if ($request->wantsJson() || $request->ajax()) {
            $msg = "Batch pembesaran {$batch->jenis_ikan} di {$kolam->nama_kolam} berhasil ditambahkan!";
            if ($asalBibit === 'beli_luar' && $biayaBeliBibit > 0) {
                $msg .= " Biaya pembelian bibit sebesar Rp " . number_format($biayaBeliBibit, 0, ',', '.') . " telah otomatis dibukukan ke Keuangan.";
            }
            return response()->json([
                'success' => true,
                'message' => $msg,
                'batch'   => $batch->load('kolam')
            ]);
        }

        return redirect()->route('pembesaran')->with('success', 'Batch pembesaran berhasil disimpan!');
    }

    public function update(Request $request, $id)
    {
        $cleanId = is_numeric($id) ? $id : (int) preg_replace('/[^0-9]/', '', $id);
        $batch = BatchPembesaran::find($cleanId);

        if (!$batch) {
            return response()->json(['success' => false, 'message' => 'Batch pembesaran tidak ditemukan.'], 404);
        }

        $request->validate([
            'id_kolam'        => 'nullable',
            'jenis_ikan'      => 'nullable|string',
            'tgl_tebar'       => 'nullable|date',
            'est_tgl_panen'   => 'nullable|date',
            'biomassa_est'    => 'nullable|numeric',
            'target_panen_kg' => 'nullable|numeric',
            'jumlah_panen_kg' => 'nullable|numeric',
            'fcr'             => 'nullable|numeric',
            'status_siklus'   => 'nullable|string',
        ]);

        if ($request->filled('id_kolam')) {
            $kolam = Kolam::where('nama_kolam', $request->id_kolam)->orWhere('id_kolam', $request->id_kolam)->first();
            if ($kolam) {
                // Check occupation if changing to another pond
                $targetStatus = strtolower($request->status_siklus ?? $batch->status_siklus);
                if ($kolam->id_kolam != $batch->id_kolam && $targetStatus === 'berjalan') {
                    $occupied = BatchPembesaran::where('id_kolam', $kolam->id_kolam)
                        ->where('status_siklus', 'berjalan')
                        ->where('id_pembesaran', '!=', $batch->id_pembesaran)
                        ->exists();

                    if ($occupied) {
                        return response()->json([
                            'success' => false,
                            'message' => "Kolam {$kolam->nama_kolam} saat ini sedang aktif digunakan oleh batch pembesaran lain!"
                        ], 422);
                    }
                }
                $batch->id_kolam = $kolam->id_kolam;
            }
        }

        if ($request->filled('jenis_ikan')) {
            $jenis = $request->jenis_ikan;
            if (stripos($jenis, 'Ikan ') !== 0) {
                $jenis = 'Ikan ' . $jenis;
            }
            $batch->jenis_ikan = $jenis;
        }

        if ($request->filled('tgl_tebar')) {
            $batch->tgl_tebar = $request->tgl_tebar;
        }

        if ($request->filled('est_tgl_panen')) {
            $batch->est_tgl_panen = $request->est_tgl_panen;
        }

        if ($request->has('biomassa_est')) {
            $batch->biomassa_est = $request->biomassa_est;
        }

        if ($request->has('target_panen_kg')) {
            $batch->target_panen_kg = $request->target_panen_kg;
        }

        if ($request->has('jumlah_panen_kg')) {
            $batch->jumlah_panen_kg = $request->jumlah_panen_kg;
        }

        if ($request->has('fcr')) {
            $batch->fcr = $request->fcr;
        }

        if ($request->filled('status_siklus')) {
            $batch->status_siklus = strtolower($request->status_siklus);
            
            // If finishing harvest
            if ($batch->status_siklus === 'selesai' || $batch->biomassa_est <= 0) {
                // Handle surplus fish allocation to stock/buffer pond
                $targetKolamStokId = $request->id_kolam_stok;
                $surplusKg = (float) ($request->surplus_kg ?? 0);

                if ($targetKolamStokId && $surplusKg > 0) {
                    $targetKolam = Kolam::find($targetKolamStokId);
                    if ($targetKolam) {
                        $existingStockBatch = BatchPembesaran::where('id_kolam', $targetKolam->id_kolam)
                            ->where('status_siklus', '!=', 'selesai')
                            ->where('status_siklus', '!=', 'gagal')
                            ->first();

                        if ($existingStockBatch) {
                            $existingStockBatch->biomassa_est += $surplusKg;
                            $existingStockBatch->target_panen_kg += $surplusKg;
                            $existingStockBatch->save();
                        } else {
                            $cleanJenis = $batch->jenis_ikan;
                            if (stripos($cleanJenis, 'Ikan ') !== 0) {
                                $cleanJenis = 'Ikan ' . $cleanJenis;
                            }
                            BatchPembesaran::create([
                                'id_kolam'            => $targetKolam->id_kolam,
                                'id_user'             => Auth::id() ?? 1,
                                'id_batch_pembibitan' => null,
                                'tgl_tebar'           => now()->toDateString(),
                                'est_tgl_panen'       => now()->toDateString(),
                                'biomassa_est'        => $surplusKg,
                                'target_panen_kg'     => $surplusKg,
                                'jumlah_panen_kg'     => 0.00,
                                'fcr'                 => 1.00,
                                'jenis_ikan'          => $cleanJenis,
                                'status_siklus'       => 'siap_panen',
                            ]);
                            $targetKolam->update(['status' => 'aktif']);
                        }
                    }
                }

                // Kosongkan kolam asal agar siap ditebar kembali
                $kolamAsal = $batch->kolam;
                $batch->biomassa_est = 0;
                $batch->status_siklus = 'selesai';
                $batch->save();

                if ($kolamAsal) {
                    $otherActive = BatchPembesaran::where('id_kolam', $kolamAsal->id_kolam)
                        ->where('id_pembesaran', '!=', $batch->id_pembesaran)
                        ->where('status_siklus', '!=', 'selesai')
                        ->where('biomassa_est', '>', 0)
                        ->exists();
                    if (!$otherActive) {
                        $kolamAsal->update(['status' => 'kosong']);
                    }
                }

                if ($request->wantsJson() || $request->ajax()) {
                    return response()->json([
                        'success' => true,
                        'message' => "Panen selesai! Kolam telah dikosongkan dan siap digunakan untuk tebar batch baru.",
                        'batch'   => null
                    ]);
                }

                return redirect()->route('pembesaran')->with('success', 'Panen selesai dan kolam siap digunakan kembali!');
            }
        }

        $batch->save();

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => "Data batch pembesaran #PB-" . str_pad($cleanId, 5, '0', STR_PAD_LEFT) . " berhasil diperbarui!",
                'batch'   => $batch->load('kolam')
            ]);
        }

        return redirect()->route('pembesaran')->with('success', 'Batch pembesaran berhasil diperbarui!');
    }

    public function destroy($id)
    {
        $cleanId = is_numeric($id) ? $id : (int) preg_replace('/[^0-9]/', '', $id);
        $batch = BatchPembesaran::find($cleanId);

        if (!$batch) {
            return response()->json(['success' => false, 'message' => 'Batch pembesaran tidak ditemukan.'], 404);
        }

        // Hapus entri kas pembelian bibit jika ada
        $pbRef = 'BELI-BIBIT-PB-' . str_pad($cleanId, 4, '0', STR_PAD_LEFT);
        Keuangan::where('ref_id', $pbRef)->delete();

        $batch->delete();

        if (request()->wantsJson() || request()->ajax()) {
            return response()->json([
                'success' => true,
                'message' => "Batch pembesaran #PB-" . str_pad($cleanId, 5, '0', STR_PAD_LEFT) . " berhasil dihapus!"
            ]);
        }

        return redirect()->route('pembesaran')->with('success', 'Batch pembesaran berhasil dihapus!');
    }

    public function storeKolam(Request $request)
    {
        $request->validate([
            'nama_kolam'       => 'required|string|max:255|unique:kolam,nama_kolam',
            'tipe_kolam'       => 'required|string|max:255',
            'kapasitas'        => 'required|numeric|min:10',
            'kesehatan_ph_air' => 'nullable|numeric|between:0,14',
        ], [
            'nama_kolam.required' => 'Nama / Kode Kolam wajib diisi.',
            'nama_kolam.unique'   => 'Nama / Kode Kolam sudah terdaftar dalam sistem.',
            'tipe_kolam.required' => 'Tipe / Konstruksi Kolam wajib dipilih.',
            'kapasitas.required'  => 'Kapasitas kolam wajib diisi.',
            'kapasitas.min'       => 'Kapasitas minimal 10 kg / ekor.',
        ]);

        $kolam = Kolam::create([
            'id_user'          => Auth::id() ?? 1,
            'nama_kolam'       => $request->nama_kolam,
            'tipe_kolam'       => $request->tipe_kolam,
            'kapasitas'        => $request->kapasitas,
            'status'           => 'aktif',
            'kesehatan_ph_air' => $request->kesehatan_ph_air ?? 7.2,
        ]);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => "Fasilitas kolam {$kolam->nama_kolam} ({$kolam->tipe_kolam}) berhasil ditambahkan!",
                'kolam'   => [
                    'id_kolam'    => $kolam->id_kolam,
                    'nama_kolam'  => $kolam->nama_kolam,
                    'tipe_kolam'  => $kolam->tipe_kolam,
                    'kapasitas'   => $kolam->kapasitas,
                    'is_occupied' => false,
                    'ph_air'      => (string) ($kolam->kesehatan_ph_air ?? '7.2'),
                ]
            ], 201);
        }

        return redirect()->route('pembesaran')->with('success', "Kolam {$kolam->nama_kolam} berhasil ditambahkan!");
    }
}

<?php

namespace App\Http\Controllers\Manajer;

use App\Http\Controllers\Controller;
use App\Models\BatchPembesaran;
use App\Models\Kolam;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class PembesaranController extends Controller
{
    public function index()
    {
        $batchRecords = BatchPembesaran::with(['kolam', 'user', 'batchPembibitan.kolam'])->latest('id_pembesaran')->get();
        
        // Only load Pembesaran ponds (Beton, Terpal, Tanah, Bioflok for Pembesaran)
        $kolams = Kolam::where('tipe_kolam', 'like', '%Pembesaran%')->get();
        
        // Find which ponds are currently occupied by active batches
        $activeBatchKolamIds = BatchPembesaran::where('status_siklus', 'berjalan')->pluck('id_kolam')->toArray();

        $kolamList = $kolams->map(function ($k) use ($activeBatchKolamIds) {
            $isOccupied = in_array($k->id_kolam, $activeBatchKolamIds);
            return [
                'id_kolam'    => $k->id_kolam,
                'nama_kolam'  => $k->nama_kolam,
                'tipe_kolam'  => $k->tipe_kolam,
                'kapasitas'   => $k->kapasitas,
                'is_occupied' => $isOccupied,
                'ph_air'      => $k->kesehatan_ph_air ?? '7.2',
            ];
        });

        $totalBiomassa = BatchPembesaran::where('status_siklus', '!=', 'selesai')->sum('biomassa_est') / 1000; // in Ton
        if ($totalBiomassa == 0) {
            $totalBiomassa = BatchPembesaran::sum('biomassa_est') / 1000;
        }

        $avgFcr = BatchPembesaran::whereNotNull('fcr')->where('fcr', '>', 0)->avg('fcr') ?? 1.12;

        $today = Carbon::today()->toDateString();
        $fedTodayKolamIds = \App\Models\ManajemenPakan::whereDate('tgl_log', $today)->pluck('id_kolam')->toArray();

        $batches = [];
        foreach ($batchRecords as $b) {
            $doc = $b->tgl_tebar ? (int) abs(Carbon::parse($b->tgl_tebar)->startOfDay()->diffInDays(now()->startOfDay())) : 0;
            $targetPercent = $b->target_panen_kg > 0 ? min(100, round(($b->biomassa_est / $b->target_panen_kg) * 100)) : 0;
            $isOptimal = ($b->fcr ?? 1.10) <= 1.25;

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
                    'kolam_asal'       => $bp->kolam ? $bp->kolam->nama_kolam : 'Kolam Hatchery',
                    'tipe_kolam_asal'  => $bp->kolam ? $bp->kolam->tipe_kolam : 'Hatchery',
                    'jenis_ikan'       => $bp->jenis_ikan,
                    'fase'             => $bp->fase_pertumbuhan ?? 'FINGERLING',
                    'tgl_pemijahan'    => $bp->tgl_pemijahan ? Carbon::parse($bp->tgl_pemijahan)->translatedFormat('d M Y') : '-',
                    'jumlah_bibit'     => number_format($sisaBibit, 0, ',', '.'),
                    'total_bobot_kg'   => number_format($bp->total_bobot_kg > 0 ? $bp->total_bobot_kg : $b->biomassa_est, 1, ',', '.'),
                    'status'           => 'Dipindahkan ke Pembesaran',
                ];
            } else {
                $bibitList[] = [
                    'id_batch'         => 'Tebar Mandiri',
                    'kolam_asal'       => $b->kolam ? $b->kolam->nama_kolam : 'Kolam Pembesaran',
                    'tipe_kolam_asal'  => 'Input Langsung',
                    'jenis_ikan'       => $b->jenis_ikan,
                    'fase'             => 'FINGERLING',
                    'tgl_pemijahan'    => $b->tgl_tebar ? Carbon::parse($b->tgl_tebar)->translatedFormat('d M Y') : '-',
                    'jumlah_bibit'     => number_format(round($b->biomassa_est * 40), 0, ',', '.'),
                    'total_bobot_kg'   => number_format($b->biomassa_est, 1, ',', '.'),
                    'status'           => 'Aktif di Pembesaran',
                ];
            }

            $batches[] = [
                'id_pembesaran'       => $b->id_pembesaran,
                'id'                  => '#PB-' . str_pad($b->id_pembesaran, 5, '0', STR_PAD_LEFT),
                'id_kolam'            => $b->id_kolam,
                'nama_kolam'          => $b->kolam ? $b->kolam->nama_kolam : 'Kolam #' . $b->id_kolam,
                'tipe_kolam'          => $b->kolam ? $b->kolam->tipe_kolam : 'Pembesaran',
                'kapasitas_kolam'     => $b->kolam ? number_format($b->kolam->kapasitas, 0, ',', '.') : '0',
                'id_batch_pembibitan' => $b->id_batch_pembibitan ? ('#BT-' . str_pad($b->id_batch_pembibitan, 5, '0', STR_PAD_LEFT)) : null,
                'asal_pembibitan'     => $b->batchPembibitan ? ('#BT-' . str_pad($b->id_batch_pembibitan, 5, '0', STR_PAD_LEFT) . ' (' . $b->batchPembibitan->jenis_ikan . ')') : 'Input Manual (Bukan Bibit)',
                'bibit_list'          => $bibitList,
                'is_fed_today'        => $isFedToday,
                'fed_status_label'    => $isFedToday ? 'Sudah Diberi Pakan' : 'Belum Diberi Pakan',
                'tgl_tebar'           => $b->tgl_tebar,
                'tgl_tebar_format'    => $b->tgl_tebar ? Carbon::parse($b->tgl_tebar)->translatedFormat('d M Y') : '-',
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
                'fcr'                 => number_format($b->fcr ?? 1.10, 2),
                'is_optimal'          => $isOptimal,
                'status_siklus'       => $statusSiklus,
                'status_label'        => $statusLabel,
                'status_class'        => $statusClass,
                'ph_air'              => $b->kolam ? ($b->kolam->kesehatan_ph_air ?? '7.2') : '7.2',
                'suhu_air'            => '28.5°C',
            ];
        }

        $availablePembibitan = \App\Models\BatchPembibitan::with('kolam')
            ->where('status', '!=', 'gagal')
            ->latest('id_batch')
            ->get()
            ->map(function ($bp) {
                $sisa = max(0, $bp->jumlah_bibitAwal - $bp->jumlah_kematian);
                return [
                    'id_batch'         => $bp->id_batch,
                    'label'            => '#BT-' . str_pad($bp->id_batch, 5, '0', STR_PAD_LEFT) . ' (' . $bp->jenis_ikan . ' - ' . number_format($sisa, 0, ',', '.') . ' Ekor' . ($bp->status === 'selesai' ? ' - Siap Pindah' : ' - ' . ucfirst($bp->status)) . ')',
                    'jenis_ikan'       => $bp->jenis_ikan,
                    'sisa_ekor'        => $sisa,
                    'est_biomassa'     => round($sisa * 0.02, 1),
                    'status'           => $bp->status,
                ];
            });

        return view('layouts.pembesaran.index', compact('batches', 'kolamList', 'kolams', 'totalBiomassa', 'avgFcr', 'availablePembibitan'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'id_kolam'             => 'required',
            'jenis_ikan'           => 'required|string',
            'id_batch_pembibitan'  => 'nullable|numeric',
            'tgl_tebar'            => 'nullable|date',
            'biomassa_est'         => 'required|numeric|min:1',
            'target_panen_kg'      => 'required|numeric|min:1',
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

        $jenis = $request->jenis_ikan;
        if (stripos($jenis, 'Ikan ') !== 0) {
            $jenis = 'Ikan ' . $jenis;
        }

        $batch = BatchPembesaran::create([
            'id_kolam'            => $kolam->id_kolam,
            'id_user'             => Auth::id() ?? 1,
            'id_batch_pembibitan' => $request->id_batch_pembibitan,
            'tgl_tebar'           => $request->tgl_tebar ?? now(),
            'biomassa_est'        => $request->biomassa_est,
            'fcr'                 => $request->fcr ?? 1.15,
            'target_panen_kg'     => $request->target_panen_kg,
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

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => "Batch pembesaran {$batch->jenis_ikan} di {$kolam->nama_kolam} berhasil ditambahkan!",
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
            // If finishing harvest and jumlah_panen_kg not explicitly set, default to target or biomassa_est
            if ($batch->status_siklus === 'selesai' && (!$batch->jumlah_panen_kg || $batch->jumlah_panen_kg == 0)) {
                $batch->jumlah_panen_kg = $request->jumlah_panen_kg ?? $batch->target_panen_kg ?? $batch->biomassa_est;
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

        $batch->delete();

        if (request()->wantsJson() || request()->ajax()) {
            return response()->json([
                'success' => true,
                'message' => "Batch pembesaran #PB-" . str_pad($cleanId, 5, '0', STR_PAD_LEFT) . " berhasil dihapus!"
            ]);
        }

        return redirect()->route('pembesaran')->with('success', 'Batch pembesaran berhasil dihapus!');
    }
}

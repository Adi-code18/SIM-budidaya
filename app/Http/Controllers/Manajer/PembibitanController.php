<?php

namespace App\Http\Controllers\Manajer;

use App\Http\Controllers\Controller;
use App\Models\BatchPembibitan;
use App\Models\Kolam;
use App\Models\ManajemenPakan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class PembibitanController extends Controller
{
    public function index()
    {
        $batchRecords = BatchPembibitan::with(['kolam', 'user', 'batchPembesaran.kolam'])->latest('id_batch')->get();
        $kolams = Kolam::where(function($q) {
            $q->where('tipe_kolam', 'like', '%Hatchery%')
              ->orWhere('tipe_kolam', 'like', '%Pemijahan%')
              ->orWhere('tipe_kolam', 'like', '%Penetasan%')
              ->orWhere('tipe_kolam', 'like', '%Pendederan%')
              ->orWhere('tipe_kolam', 'like', '%Pembibitan%');
        })->get();

        // Calculate KPI boxes accurately from batch_pembibitan table
        $totalAwal = BatchPembibitan::where('status', '!=', 'gagal')->sum('jumlah_bibitAwal');
        $totalMati = BatchPembibitan::where('status', '!=', 'gagal')->sum('jumlah_kematian');
        
        $totalBenihAktif = BatchPembibitan::where('status', '!=', 'selesai')
            ->where('status', '!=', 'gagal')
            ->get()
            ->sum(function ($b) {
                return max(0, $b->jumlah_bibitAwal - $b->jumlah_kematian);
            });

        if ($totalBenihAktif == 0) {
            $totalBenihAktif = max(0, $totalAwal - $totalMati);
        }

        // Jika tidak ada bibit terdaftar (totalAwal == 0), SR adalah 0.0 (bukan 100.0)
        $srRate = $totalAwal > 0 ? round((($totalAwal - $totalMati) / $totalAwal) * 100, 1) : 0.0;

        $bakTerpakaiCount = BatchPembibitan::where('status', '!=', 'selesai')
            ->where('status', '!=', 'gagal')
            ->distinct('id_kolam')
            ->count('id_kolam');

        $totalBakCount = $kolams->count();
        if ($totalBakCount == 0) {
            $totalBakCount = Kolam::count();
        }

        $activeBatchCount = BatchPembibitan::where('status', '!=', 'selesai')->where('status', '!=', 'gagal')->count();
        
        // Ambil pH murni dari log pencatatan riil tabel manajemen_pakan kolam hatchery
        $hatcheryKolamIds = $kolams->pluck('id_kolam')->toArray();
        $pakanPh = ManajemenPakan::whereIn('id_kolam', $hatcheryKolamIds)
            ->whereNotNull('ph_air')
            ->where('ph_air', '>', 0)
            ->avg('ph_air');

        $avgPh = $pakanPh ? round((float)$pakanPh, 1) : 0.0;

        if ($avgPh <= 0) {
            $phStatus = 'Belum Ada Data';
            $phStatusClass = 'text-slate-400';
        } elseif ($avgPh >= 6.8 && $avgPh <= 8.0) {
            $phStatus = 'pH Normal';
            $phStatusClass = 'text-emerald-600';
        } else {
            $phStatus = 'Perlu Perhatian';
            $phStatusClass = 'text-rose-600';
        }

        $kpis = [
            'totalBenih'        => number_format($totalBenihAktif, 0, ',', '.'),
            'totalBenihRaw'     => $totalBenihAktif,
            'totalAwal'         => $totalAwal,
            'srRate'            => number_format($srRate, 1, '.', ''),
            'srRateRaw'         => $srRate,
            'bakTerpakai'       => $bakTerpakaiCount,
            'totalBak'          => $totalBakCount,
            'bakTersedia'       => max(0, $totalBakCount - $bakTerpakaiCount),
            'activeBatchCount'  => $activeBatchCount,
            'avgPh'             => $avgPh > 0 ? number_format($avgPh, 1, '.', '') : '0',
            'avgPhRaw'          => $avgPh,
            'phStatus'          => $phStatus,
            'phStatusClass'     => $phStatusClass,
        ];

        $batches = [];
        foreach ($batchRecords as $b) {
            $days = $b->tgl_pemijahan ? (int) abs(Carbon::parse($b->tgl_pemijahan)->startOfDay()->diffInDays(now()->startOfDay())) : 0;

            // Use DB fase_pertumbuhan if available, otherwise calculate by age
            $dbFase = strtoupper(trim($b->fase_pertumbuhan ?? ''));
            if (in_array($dbFase, ['TELUR', 'LARVA', 'FINGERLING'])) {
                $fase = $dbFase;
            } else {
                if ($days <= 3) {
                    $fase = 'TELUR';
                } elseif ($days <= 14) {
                    $fase = 'LARVA';
                } else {
                    $fase = 'FINGERLING';
                }
            }

            if ($fase === 'TELUR') {
                $faseClass = 'bg-slate-100 text-slate-700';
            } elseif ($fase === 'LARVA') {
                $faseClass = 'bg-sky-100 text-sky-700';
            } else {
                $faseClass = 'bg-indigo-100 text-indigo-700';
            }

            // Sync with actual DB status column
            $rawStatus = strtolower($b->status ?? 'aktif');
            $statusLabel = 'Aktif';
            $statusClass = 'bg-emerald-100 text-emerald-700';
            $dotClass = 'bg-emerald-500';

            if ($rawStatus === 'inkubasi') {
                $statusLabel = 'Proses Inkubasi';
                $statusClass = 'bg-amber-100 text-amber-700';
                $dotClass = 'bg-amber-500';
            } elseif ($rawStatus === 'menetas') {
                $statusLabel = 'Mulai Menetas';
                $statusClass = 'bg-sky-100 text-sky-700';
                $dotClass = 'bg-sky-500';
            } elseif ($rawStatus === 'siap_pindah') {
                $statusLabel = 'Siap Pindah';
                $statusClass = 'bg-teal-100 text-teal-700';
                $dotClass = 'bg-teal-500';
            } elseif ($rawStatus === 'selesai') {
                $statusLabel = 'Selesai (Dipindahkan)';
                $statusClass = 'bg-slate-100 text-slate-700';
                $dotClass = 'bg-slate-500';
            } elseif ($rawStatus === 'gagal' || $rawStatus === 'dibatalkan') {
                $statusLabel = 'Gagal / Dibatalkan';
                $statusClass = 'bg-rose-100 text-rose-700';
                $dotClass = 'bg-rose-500';
            }

            $firstPb = $b->batchPembesaran ? $b->batchPembesaran->first() : null;
            $sisaEkor = max(0, $b->jumlah_bibitAwal - $b->jumlah_kematian);
            
            $rawBobotKg = (float) ($b->total_bobot_kg ?? 0);
            if ($rawBobotKg <= 0) {
                if ($fase === 'TELUR') {
                    $rawBobotKg = round($sisaEkor * 0.0001, 2);
                } elseif ($fase === 'LARVA') {
                    $rawBobotKg = round($sisaEkor * 0.0008, 2);
                } else {
                    $rawBobotKg = round($sisaEkor * 0.02, 2);
                }
            }
            $bobotKgFormat = number_format($rawBobotKg, $rawBobotKg >= 100 ? 1 : 2, ',', '.') . ' kg';

            $batches[] = [
                'id_batch'             => $b->id_batch,
                'id'                   => '#BT-' . str_pad($b->id_batch, 5, '0', STR_PAD_LEFT),
                'inputDate'            => $b->tgl_pemijahan ? Carbon::parse($b->tgl_pemijahan)->translatedFormat('d M Y') : '-',
                'tglPemijahan'         => $b->tgl_pemijahan,
                'jenis_ikan'           => $b->ikan ? $b->ikan->nama_ikan : ($b->jenis_ikan ?? 'Ikan Nila'),
                'id_ikan'              => $b->id_ikan,
                'est_prcs_pembibitaan' => $b->est_prcs_pembibitaan ? Carbon::parse($b->est_prcs_pembibitaan)->translatedFormat('d M Y') : '-',
                'est_prcs_raw'         => $b->est_prcs_pembibitaan ? Carbon::parse($b->est_prcs_pembibitaan)->format('Y-m-d') : '',
                'fase'                 => $fase,
                'faseClass'            => $faseClass,
                'usia'                 => $days . ' Hari',
                'usiaDays'             => $days,
                'jumlahBibitAwal'      => $b->jumlah_bibitAwal,
                'jumlahKematian'       => $b->jumlah_kematian,
                'jumlah'               => number_format($sisaEkor, 0, ',', '.'),
                'jumlahRaw'            => $sisaEkor,
                'totalBobotKg'         => $rawBobotKg,
                'totalBobotFormat'     => $bobotKgFormat,
                'status'               => $rawStatus,
                'statusLabel'          => $statusLabel,
                'statusClass'          => $statusClass,
                'dotClass'             => $dotClass,
                'kolam'                => $b->kolam ? $b->kolam->nama_kolam : 'Kolam #' . $b->id_kolam,
                'phAir'                => ($logPh = ManajemenPakan::where('id_kolam', $b->id_kolam)->whereNotNull('ph_air')->where('ph_air', '>', 0)->latest('tgl_log')->value('ph_air')) ? number_format($logPh, 1) : '-',
                'batch_pembesaran_id'  => $firstPb ? '#PB-' . str_pad($firstPb->id_pembesaran, 5, '0', STR_PAD_LEFT) : null,
                'kolam_pembesaran'     => $firstPb && $firstPb->kolam ? $firstPb->kolam->nama_kolam : ($firstPb ? 'Kolam #' . $firstPb->id_kolam : null),
                'tgl_pindah'           => $firstPb && $firstPb->tgl_tebar ? Carbon::parse($firstPb->tgl_tebar)->translatedFormat('d M Y') : null,
            ];
        }

        $kolamPembesaran = Kolam::where('tipe_kolam', 'like', '%Pembesaran%')->orWhere('tipe_kolam', 'like', '%Besar%')->get();
        if ($kolamPembesaran->isEmpty()) {
            $kolamPembesaran = Kolam::all();
        }

        $ikans = \App\Models\Ikan::orderBy('nama_ikan', 'asc')->get();

        return view('layouts.pembibitan.index', compact('batches', 'kolams', 'kolamPembesaran', 'kpis', 'ikans'));
    }

    public function transferKePembesaran(Request $request, $id)
    {
        $cleanId = is_numeric($id) ? $id : (int) preg_replace('/[^0-9]/', '', $id);
        $batchPembibitan = BatchPembibitan::find($cleanId);

        if (!$batchPembibitan) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'message' => 'Data batch pembibitan tidak ditemukan.'], 404);
            }
            return redirect()->route('pembibitan')->with('error', 'Data batch pembibitan tidak ditemukan.');
        }

        $request->validate([
            'id_kolam_pembesaran' => 'required',
            'target_panen_kg'     => 'required|numeric|min:1',
            'biomassa_est'        => 'nullable|numeric|min:0.1',
        ]);

        $kolamBesar = Kolam::where('nama_kolam', $request->id_kolam_pembesaran)
            ->orWhere('id_kolam', $request->id_kolam_pembesaran)
            ->first();

        if (!$kolamBesar) {
            $kolamBesar = Kolam::where('tipe_kolam', 'like', '%Pembesaran%')->first() ?? Kolam::first();
        }

        $biomassa = $request->biomassa_est ? (float) $request->biomassa_est : ((float) $batchPembibitan->total_bobot_kg > 0 ? (float) $batchPembibitan->total_bobot_kg : 50.0);

        $pembesaran = BatchPembesaran::create([
            'id_kolam'             => $kolamBesar ? $kolamBesar->id_kolam : 1,
            'id_user'              => Auth::id() ?? 1,
            'id_batch_pembibitan'  => $batchPembibitan->id_batch,
            'tgl_tebar'            => now()->toDateString(),
            'biomassa_est'         => $biomassa,
            'fcr'                  => null,
            'target_panen_kg'      => $request->target_panen_kg,
            'jumlah_panen_kg'      => 0,
            'jenis_ikan'           => $batchPembibitan->ikan ? $batchPembibitan->ikan->nama_ikan : ($batchPembibitan->jenis_ikan ?? 'Ikan Air Tawar'),
            'status_siklus'        => 'berjalan',
        ]);

        $batchPembibitan->update([
            'status'           => 'selesai',
            'fase_pertumbuhan' => 'FINGERLING'
        ]);

        $kodePB = '#PB-' . str_pad($pembesaran->id_pembesaran, 5, '0', STR_PAD_LEFT);
        $namaKolam = $kolamBesar ? $kolamBesar->nama_kolam : 'Kolam Pembesaran';

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => "Batch berhasil dipindahkan ke Pembesaran ({$kodePB}) di {$namaKolam}!",
                'data'    => $pembesaran
            ]);
        }

        return redirect()->route('pembibitan')->with('success', "Batch berhasil dipindahkan ke Pembesaran ({$kodePB}) di {$namaKolam}!");
    }

    public function store(Request $request)
    {
        $request->validate([
            'id_kolam'              => 'required',
            'id_ikan'               => 'nullable',
            'jenis_ikan'            => 'nullable|string',
            'tgl_pemijahan'         => 'nullable|date',
            'fase_pertumbuhan'      => 'nullable|string',
            'jumlah_bibitAwal'      => 'required|numeric|min:1',
            'jumlah_kematian'       => 'nullable|numeric|min:0',
            'total_bobot_kg'        => 'nullable|numeric|min:0',
        ]);

        $kolam = Kolam::where('nama_kolam', $request->id_kolam)->orWhere('id_kolam', $request->id_kolam)->first();

        $idIkan = $request->id_ikan ?: null;
        $jenisIkan = $request->jenis_ikan;
        if ($idIkan && !$jenisIkan) {
            $ik = \App\Models\Ikan::find($idIkan);
            if ($ik) $jenisIkan = $ik->nama_ikan;
        }

        $tglPemijahan = $request->tgl_pemijahan ?? now();
        $fase = $request->fase_pertumbuhan;
        if (!$fase) {
            $days = (int) abs(Carbon::parse($tglPemijahan)->startOfDay()->diffInDays(now()->startOfDay()));
            $fase = $days <= 3 ? 'TELUR' : ($days <= 14 ? 'LARVA' : 'FINGERLING');
        } else {
            $fase = strtoupper($fase);
        }

        $rawBobot = $request->total_bobot_kg;
        if (!$rawBobot || $rawBobot <= 0) {
            $sisa = max(0, $request->jumlah_bibitAwal - ($request->jumlah_kematian ?? 0));
            $rawBobot = $fase === 'TELUR' ? ($sisa * 0.0001) : ($fase === 'LARVA' ? ($sisa * 0.0008) : ($sisa * 0.02));
        }

        $jumlahKematian = ($fase === 'TELUR') ? 0 : ($request->jumlah_kematian ?? 0);

        $batch = BatchPembibitan::create([
            'id_kolam'             => $kolam ? $kolam->id_kolam : 1,
            'id_user'              => Auth::id() ?? 1,
            'id_ikan'              => $idIkan,
            'jenis_ikan'           => $jenisIkan,
            'tgl_pemijahan'        => $tglPemijahan,
            'est_prcs_pembibitaan' => $request->est_prcs_pembibitaan,
            'jumlah_bibitAwal'     => $request->jumlah_bibitAwal,
            'jumlah_kematian'      => $jumlahKematian,
            'total_bobot_kg'       => round($rawBobot, 2),
            'fase_pertumbuhan'     => $fase,
            'status'               => strtolower($request->status ?? 'aktif'),
        ]);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => "Data batch #BT-" . str_pad($batch->id_batch, 5, '0', STR_PAD_LEFT) . " (" . number_format($batch->jumlah_bibitAwal, 0, ',', '.') . " ekor, " . number_format($batch->total_bobot_kg, 2, ',', '.') . " kg) berhasil disimpan!",
                'batch'   => $batch
            ]);
        }

        return redirect()->route('pembibitan')->with('success', 'Data batch pembibitan berhasil disimpan!');
    }

    public function update(Request $request, $id)
    {
        $cleanId = is_numeric($id) ? $id : (int) preg_replace('/[^0-9]/', '', $id);
        $batch = BatchPembibitan::find($cleanId);

        if (!$batch) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'message' => 'Data batch tidak ditemukan.'], 404);
            }
            return redirect()->route('pembibitan')->with('error', 'Data batch tidak ditemukan.');
        }

        $request->validate([
            'id_kolam'              => 'nullable',
            'id_ikan'               => 'nullable',
            'jenis_ikan'            => 'nullable|string',
            'tgl_pemijahan'         => 'nullable|date',
            'est_prcs_pembibitaan'  => 'nullable|date',
            'fase_pertumbuhan'      => 'nullable|string',
            'jumlah_bibitAwal'      => 'nullable|numeric',
            'jumlah_kematian'       => 'nullable|numeric',
            'total_bobot_kg'        => 'nullable|numeric',
            'status'                => 'nullable|string',
        ]);

        if ($request->filled('id_kolam')) {
            $kolam = Kolam::where('nama_kolam', $request->id_kolam)->orWhere('id_kolam', $request->id_kolam)->first();
            if ($kolam) {
                $batch->id_kolam = $kolam->id_kolam;
            }
        }

        if ($request->filled('tgl_pemijahan')) {
            $batch->tgl_pemijahan = $request->tgl_pemijahan;
        }

        if ($request->has('est_prcs_pembibitaan')) {
            $batch->est_prcs_pembibitaan = $request->est_prcs_pembibitaan;
        }

        if ($request->filled('fase_pertumbuhan')) {
            $batch->fase_pertumbuhan = strtoupper($request->fase_pertumbuhan);
        }

        if ($request->has('jumlah_bibitAwal')) {
            $batch->jumlah_bibitAwal = $request->jumlah_bibitAwal;
        }

        if ($batch->fase_pertumbuhan === 'TELUR') {
            $batch->jumlah_kematian = 0;
        } elseif ($request->has('jumlah_kematian')) {
            $batch->jumlah_kematian = $request->jumlah_kematian;
        }

        if ($request->has('total_bobot_kg')) {
            $batch->total_bobot_kg = $request->total_bobot_kg;
        }

        if ($request->filled('status')) {
            $statusVal = strtolower($request->status);
            if ($statusVal === 'gagal') {
                $batch->delete();
                if ($request->wantsJson() || $request->ajax()) {
                    return response()->json([
                        'success' => true,
                        'message' => "Batch #BT-" . str_pad($cleanId, 5, '0', STR_PAD_LEFT) . " dinyatakan GAGAL dan telah dihapus dari sistem.",
                        'deleted' => true
                    ]);
                }
                return redirect()->route('pembibitan')->with('success', "Batch telah dihapus karena status GAGAL.");
            }
            $batch->status = $statusVal;
        }

        $batch->save();

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => "Data batch #BT-" . str_pad($cleanId, 5, '0', STR_PAD_LEFT) . " berhasil diperbarui!",
                'batch'   => $batch
            ]);
        }

        return redirect()->route('pembibitan')->with('success', 'Data batch berhasil diperbarui!');
    }

    public function destroy($id)
    {
        $cleanId = is_numeric($id) ? $id : (int) preg_replace('/[^0-9]/', '', $id);
        $batch = BatchPembibitan::find($cleanId);

        if (!$batch) {
            if (request()->wantsJson() || request()->ajax()) {
                return response()->json(['success' => false, 'message' => 'Data batch tidak ditemukan.'], 404);
            }
            return redirect()->route('pembibitan')->with('error', 'Data batch tidak ditemukan.');
        }

        $batch->delete();

        if (request()->wantsJson() || request()->ajax()) {
            return response()->json([
                'success' => true,
                'message' => "Data batch #BT-" . str_pad($cleanId, 5, '0', STR_PAD_LEFT) . " berhasil dihapus!"
            ]);
        }

        return redirect()->route('pembibitan')->with('success', 'Data batch berhasil dihapus!');
    }
}

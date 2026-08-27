<?php

namespace App\Http\Controllers\Manajer;

use App\Http\Controllers\Controller;
use App\Models\BatchPembibitan;
use App\Models\Kolam;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class PembibitanController extends Controller
{
    public function index()
    {
        $batchRecords = BatchPembibitan::with(['kolam', 'user'])->latest('id_batch')->get();
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

        $srRate = $totalAwal > 0 ? (100 - (($totalMati / $totalAwal) * 100)) : 98.8;

        $kolamBibitCount = Kolam::where('tipe_kolam', 'like', '%Pembibitan%')
            ->orWhere('tipe_kolam', 'like', '%Hatchery%')
            ->orWhere('tipe_kolam', 'like', '%Pemijahan%')
            ->count();
        if ($kolamBibitCount == 0) {
            $kolamBibitCount = BatchPembibitan::where('status', 'aktif')->orWhere('status', 'menetas')->distinct('id_kolam')->count('id_kolam');
        }
        $totalKolamCount = Kolam::count();

        $avgPh = Kolam::avg('kesehatan_ph_air') ?? 7.2;

        $kpis = [
            'totalBenih'      => number_format($totalBenihAktif, 0, ',', '.'),
            'srRate'          => number_format($srRate, 1, '.', ''),
            'bakTerpakai'     => $kolamBibitCount > 0 ? $kolamBibitCount : 4,
            'totalBak'        => $totalKolamCount > 0 ? $totalKolamCount : 12,
            'bakTersedia'     => max(0, ($totalKolamCount > 0 ? $totalKolamCount : 12) - ($kolamBibitCount > 0 ? $kolamBibitCount : 4)),
            'avgPh'           => number_format($avgPh, 1, '.', ''),
            'suhu'            => '28°C'
        ];

        $batches = [];
        foreach ($batchRecords as $b) {
            $days = $b->tgl_pemijahan ? (int) abs(Carbon::parse($b->tgl_pemijahan)->startOfDay()->diffInDays(now()->startOfDay())) : 0;

            $fase = 'LARVA';
            $faseClass = 'bg-sky-100 text-sky-700';

            if ($days <= 3) {
                $fase = 'TELUR';
                $faseClass = 'bg-slate-100 text-slate-700';
            } elseif ($days > 14) {
                $fase = 'FINGERLING';
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
            } elseif ($rawStatus === 'selesai') {
                $statusLabel = 'Selesai';
                $statusClass = 'bg-slate-100 text-slate-700';
                $dotClass = 'bg-slate-500';
            } elseif ($rawStatus === 'gagal' || $rawStatus === 'dibatalkan') {
                $statusLabel = 'Gagal / Dibatalkan';
                $statusClass = 'bg-rose-100 text-rose-700';
                $dotClass = 'bg-rose-500';
            }

            // Extract clean species name
            $cleanJenis = $b->jenis_ikan;
            if (stripos($cleanJenis, 'Ikan ') === 0) {
                $cleanJenis = substr($cleanJenis, 5);
            }
            $cleanJenis = explode(' ', $cleanJenis)[0];

            $batches[] = [
                'id_batch'        => $b->id_batch,
                'id'              => '#BT-' . str_pad($b->id_batch, 5, '0', STR_PAD_LEFT),
                'inputDate'       => $b->tgl_pemijahan ? Carbon::parse($b->tgl_pemijahan)->translatedFormat('d M Y') : '-',
                'tglPemijahan'    => $b->tgl_pemijahan,
                'fase'            => $fase,
                'faseClass'       => $faseClass,
                'usia'            => $days . ' Hari',
                'jumlahBibitAwal' => $b->jumlah_bibitAwal,
                'jumlahKematian'  => $b->jumlah_kematian,
                'jumlah'          => number_format(max(0, $b->jumlah_bibitAwal - $b->jumlah_kematian), 0, ',', '.'),
                'jumlahRaw'       => max(0, $b->jumlah_bibitAwal - $b->jumlah_kematian),
                'jenisIkan'       => strtoupper($cleanJenis),
                'rawJenisIkan'    => $cleanJenis,
                'status'          => $rawStatus,
                'statusLabel'     => $statusLabel,
                'statusClass'     => $statusClass,
                'dotClass'        => $dotClass,
                'kolam'           => $b->kolam ? $b->kolam->nama_kolam : 'Kolam #' . $b->id_kolam,
                'phAir'           => $b->kolam ? ($b->kolam->kesehatan_ph_air ?? '7.2') : '7.2',
                'suhuAir'         => '28.0°C'
            ];
        }

        $kolamPembesaran = Kolam::where('tipe_kolam', 'like', '%Pembesaran%')->orWhere('tipe_kolam', 'like', '%Besar%')->get();
        if ($kolamPembesaran->isEmpty()) {
            $kolamPembesaran = Kolam::all();
        }

        return view('layouts.pembibitan.index', compact('batches', 'kolams', 'kolamPembesaran', 'kpis'));
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
        ], [
            'id_kolam_pembesaran.required' => 'Kolam pembesaran tujuan wajib dipilih.',
            'target_panen_kg.required'     => 'Target panen (kg) wajib diisi.',
        ]);

        $kolamTujuan = Kolam::where('nama_kolam', $request->id_kolam_pembesaran)
            ->orWhere('id_kolam', $request->id_kolam_pembesaran)
            ->first();

        if (!$kolamTujuan) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'message' => 'Kolam pembesaran tujuan tidak ditemukan.'], 404);
            }
            return redirect()->route('pembibitan')->with('error', 'Kolam pembesaran tujuan tidak ditemukan.');
        }

        $sisaBibit = max(0, $batchPembibitan->jumlah_bibitAwal - $batchPembibitan->jumlah_kematian);
        $biomassaEst = $request->biomassa_est ?? round(($sisaBibit * 0.02), 2); // 20 gr/ekor est
        if ($biomassaEst <= 0) {
            $biomassaEst = 50.00;
        }

        // Create Batch Pembesaran
        $batchPembesaran = \App\Models\BatchPembesaran::create([
            'id_kolam'        => $kolamTujuan->id_kolam,
            'id_user'         => Auth::id() ?? 1,
            'tgl_tebar'       => now(),
            'biomassa_est'    => $biomassaEst,
            'fcr'             => 1.10,
            'target_panen_kg' => $request->target_panen_kg,
            'jumlah_panen_kg' => 0.00,
            'jenis_ikan'      => $batchPembibitan->jenis_ikan,
            'status_siklus'   => 'berjalan',
        ]);

        // Mark Pembibitan Batch as finished (selesai)
        $batchPembibitan->status = 'selesai';
        $batchPembibitan->save();

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success'          => true,
                'message'          => "Sukses! Batch Pembibitan #BT-" . str_pad($cleanId, 5, '0', STR_PAD_LEFT) . " ({$batchPembibitan->jenis_ikan}) berhasil dipindahkan ke Kolam Pembesaran '{$kolamTujuan->nama_kolam}'! Batch Pembesaran #PB-" . str_pad($batchPembesaran->id_pembesaran, 5, '0', STR_PAD_LEFT) . " telah aktif.",
                'batch_pembesaran' => $batchPembesaran
            ]);
        }

        return redirect()->route('pembesaran')->with('success', "Batch berhasil dipindahkan ke Pembesaran!");
    }

    public function store(Request $request)
    {
        $request->validate([
            'jenis_ikan'       => 'required|string',
            'id_kolam'         => 'required',
            'tgl_pemijahan'    => 'nullable|date',
            'jumlah_bibitAwal' => 'required|numeric|min:1',
            'jumlah_kematian'  => 'nullable|numeric|min:0',
        ]);

        $kolam = Kolam::where('nama_kolam', $request->id_kolam)->orWhere('id_kolam', $request->id_kolam)->first();

        $batch = BatchPembibitan::create([
            'id_kolam'         => $kolam ? $kolam->id_kolam : 1,
            'id_user'          => Auth::id() ?? 1,
            'tgl_pemijahan'    => $request->tgl_pemijahan ?? now(),
            'jumlah_bibitAwal' => $request->jumlah_bibitAwal,
            'jumlah_kematian'  => $request->jumlah_kematian ?? 0,
            'jenis_ikan'       => 'Ikan ' . $request->jenis_ikan,
            'status'           => strtolower($request->status ?? 'aktif'),
        ]);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => "Data batch {$batch->jenis_ikan} (" . number_format($batch->jumlah_bibitAwal, 0, ',', '.') . " ekor) berhasil disimpan!",
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
            'jenis_ikan'       => 'nullable|string',
            'id_kolam'         => 'nullable',
            'tgl_pemijahan'    => 'nullable|date',
            'jumlah_bibitAwal' => 'nullable|numeric',
            'jumlah_kematian'  => 'nullable|numeric',
            'status'           => 'nullable|string',
        ]);

        if ($request->filled('id_kolam')) {
            $kolam = Kolam::where('nama_kolam', $request->id_kolam)->orWhere('id_kolam', $request->id_kolam)->first();
            if ($kolam) {
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

        if ($request->filled('tgl_pemijahan')) {
            $batch->tgl_pemijahan = $request->tgl_pemijahan;
        }

        if ($request->has('jumlah_bibitAwal')) {
            $batch->jumlah_bibitAwal = $request->jumlah_bibitAwal;
        }

        if ($request->has('jumlah_kematian')) {
            $batch->jumlah_kematian = $request->jumlah_kematian;
        }

        if ($request->filled('status')) {
            $batch->status = strtolower($request->status);
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

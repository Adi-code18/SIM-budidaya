<?php

namespace App\Http\Controllers\Manajer;

use App\Http\Controllers\Controller;
use App\Models\BatchPembesaran;
use App\Models\Kolam;
use App\Models\ManajemenPakan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class PakanController extends Controller
{
    public function index()
    {
        $today = Carbon::today()->toDateString();

        // Hanya tampilkan kolam / batch pembesaran yang sedang berjalan (ada penghuninya / aktif)
        $activeBatches = BatchPembesaran::with(['kolam', 'batchPembibitan'])
            ->where('status_siklus', 'berjalan')
            ->latest('id_pembesaran')
            ->get();

        // Kolam-kolam yang sudah dicatat pemberian pakannya HARI INI
        $fedTodayKolamIds = ManajemenPakan::whereDate('tgl_log', $today)
            ->pluck('id_kolam')
            ->toArray();

        $activeKolams = $activeBatches->map(function ($b) use ($fedTodayKolamIds) {
            $isFedToday = in_array($b->id_kolam, $fedTodayKolamIds);
            return [
                'id_kolam'          => $b->id_kolam,
                'id_pembesaran'     => $b->id_pembesaran,
                'batch_id'          => '#PB-' . str_pad($b->id_pembesaran, 5, '0', STR_PAD_LEFT),
                'nama_kolam'        => $b->kolam ? $b->kolam->nama_kolam : 'Kolam #' . $b->id_kolam,
                'tipe_kolam'        => $b->kolam ? $b->kolam->tipe_kolam : 'Pembesaran',
                'jenis_ikan'        => $b->jenis_ikan,
                'biomassa_est'      => (float) $b->biomassa_est,
                'biomassa_format'   => number_format($b->biomassa_est, 1, ',', '.'),
                'is_fed_today'      => $isFedToday,
                'label'             => ($b->kolam ? $b->kolam->nama_kolam : 'Kolam #' . $b->id_kolam) . ' - #PB-' . str_pad($b->id_pembesaran, 5, '0', STR_PAD_LEFT) . ' (' . $b->jenis_ikan . ' • ' . number_format($b->biomassa_est, 1, ',', '.') . ' kg)' . ($isFedToday ? ' [Sudah Diberi Pakan Hari Ini]' : ' [Belum Diberi Pakan]'),
            ];
        });

        // Riwayat log pakan terbaru
        $logs = ManajemenPakan::with(['kolam', 'user'])->latest('tgl_log')->take(20)->get();

        return view('layouts.pakan.index', compact('activeKolams', 'activeBatches', 'logs'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'id_kolam'    => 'required|exists:kolam,id_kolam',
            'tgl_log'     => 'nullable|date',
            'kg_pelet'    => 'nullable|numeric|min:0',
            'kg_daun'     => 'nullable|numeric|min:0',
            'jenis_daun'  => 'nullable|string',
            'total_biaya' => 'nullable|numeric|min:0',
            'ph_air'      => 'nullable|numeric',
        ]);

        $tgl = $request->tgl_log ? Carbon::parse($request->tgl_log)->toDateString() : Carbon::today()->toDateString();
        $kgPelet = (float) ($request->kg_pelet ?? 0);
        $kgDaun = (float) ($request->kg_daun ?? 0);
        $totalBiaya = (float) ($request->total_biaya ?? ($kgPelet * 12500)); // estimasi 12.500/kg

        $log = ManajemenPakan::create([
            'id_user'     => Auth::id() ?? 1,
            'id_kolam'    => $request->id_kolam,
            'tgl_log'     => $tgl,
            'kg_pelet'    => $kgPelet,
            'kg_daun'     => $kgDaun,
            'jenis_daun'  => $request->jenis_daun,
            'total_biaya' => $totalBiaya,
            'ph_air'      => $request->ph_air ?? 7.0,
        ]);

        // Update kolam ph air jika diisi
        if ($request->filled('ph_air')) {
            Kolam::where('id_kolam', $request->id_kolam)->update([
                'kesehatan_ph_air' => $request->ph_air
            ]);
        }

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Log pemberian pakan berhasil dicatat!',
                'log'     => $log->load(['kolam', 'user'])
            ]);
        }

        return redirect()->route('log-pakan')->with('success', 'Log pemberian pakan berhasil dicatat!');
    }
}

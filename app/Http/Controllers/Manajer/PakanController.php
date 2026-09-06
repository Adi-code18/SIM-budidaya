<?php

namespace App\Http\Controllers\Manajer;

use App\Http\Controllers\Controller;
use App\Models\BatchPembesaran;
use App\Models\BatchPembibitan;
use App\Models\Keuangan;
use App\Models\Kolam;
use App\Models\ManajemenPakan;
use App\Models\MitraDistributor;
use App\Models\PembelianPakan;
use App\Models\StokPakan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PakanController extends Controller
{
    public function index()
    {
        $today = Carbon::today()->toDateString();

        // 1. Ambil Semua Master Stok Pakan dengan Kalkulasi Burn Rate & Sisa Hari
        $stokPakanList = StokPakan::with('pembelian.mitra')->get();
        
        // Hitung pemakaian 7 hari terakhir per pakan untuk burn rate
        $startDate7 = Carbon::now()->subDays(6)->startOfDay();
        $pakanUsage7d = ManajemenPakan::where('tgl_log', '>=', $startDate7)
            ->selectRaw('id_stok_pakan, SUM(COALESCE(kg_pelet, 0) + COALESCE(kg_daun, 0)) as total_kg')
            ->groupBy('id_stok_pakan')
            ->pluck('total_kg', 'id_stok_pakan');

        // Total pemakaian pembibitan & pembesaran 7 hari terakhir
        $pembibitanUsage7d = ManajemenPakan::where('tgl_log', '>=', $startDate7)
            ->where('kategori_fase', 'pembibitan')
            ->sum(DB::raw('COALESCE(kg_pelet, 0) + COALESCE(kg_daun, 0)'));

        $pembesaranUsage7d = ManajemenPakan::where('tgl_log', '>=', $startDate7)
            ->where('kategori_fase', '!=', 'pembibitan')
            ->sum(DB::raw('COALESCE(kg_pelet, 0) + COALESCE(kg_daun, 0)'));

        $stokSummary = [
            'total_stok_kg'       => $stokPakanList->sum('stok_tersisa'),
            'stok_pembibitan_kg'  => $stokPakanList->where('kategori_peruntukan', 'pembibitan')->sum('stok_tersisa'),
            'stok_pembesaran_kg'  => $stokPakanList->whereIn('kategori_peruntukan', ['pembesaran', 'semua'])->sum('stok_tersisa'),
            'item_kritis_count'   => 0,
            'item_waspada_count'  => 0,
            'item_aman_count'     => 0,
        ];

        $enrichedStokPakan = $stokPakanList->map(function ($item) use ($pakanUsage7d, &$stokSummary) {
            $used7d = (float) ($pakanUsage7d->get($item->id_stok_pakan) ?? 0);
            
            // Fallback: Jika belum ada log spesifik id_stok_pakan, gunakan estimasi berbasis kategori
            if ($used7d <= 0) {
                $used7d = $item->kategori_peruntukan === 'pembibitan' ? 2.5 : 25.0;
            }

            $burnRateHarian = round($used7d / 7, 2);
            $sisaStok = (float) $item->stok_tersisa;
            $sisaHari = $burnRateHarian > 0 ? round($sisaStok / $burnRateHarian, 0) : ($sisaStok > 0 ? 99 : 0);

            // Status: 'kritis' (<= 2 hari atau stok <= batas_minimum), 'waspada' (3-7 hari), 'aman' (> 7 hari)
            if ($sisaStok <= $item->batas_minimum || $sisaHari <= 2) {
                $status = 'kritis';
                $statusLabel = 'Kritis (Segera Restock)';
                $statusBadge = 'bg-rose-100 text-rose-700 border-rose-200';
                $stokSummary['item_kritis_count']++;
            } elseif ($sisaHari <= 7) {
                $status = 'waspada';
                $statusLabel = 'Perlu Pesan';
                $statusBadge = 'bg-amber-100 text-amber-800 border-amber-200';
                $stokSummary['item_waspada_count']++;
            } else {
                $status = 'aman';
                $statusLabel = 'Stok Aman';
                $statusBadge = 'bg-emerald-100 text-emerald-800 border-emerald-200';
                $stokSummary['item_aman_count']++;
            }

            return [
                'id_stok_pakan'       => $item->id_stok_pakan,
                'nama_pakan'          => $item->nama_pakan,
                'kategori_peruntukan' => $item->kategori_peruntukan,
                'satuan'              => $item->satuan,
                'stok_tersisa'        => $sisaStok,
                'batas_minimum'       => (float) $item->batas_minimum,
                'harga_per_satuan'    => (float) $item->harga_per_satuan,
                'keterangan'          => $item->keterangan,
                'burn_rate_harian'    => $burnRateHarian,
                'sisa_hari'           => $sisaHari,
                'status'              => $status,
                'status_label'        => $statusLabel,
                'status_badge'        => $statusBadge,
            ];
        });

        // 2. Ambil Mitra Khusus Supplier untuk Modal Order WA & Pembelian
        $suppliers = MitraDistributor::where('tipe_mitra', 'like', '%supplier%')
            ->orWhere('tipe_mitra', 'like', '%distributor%')
            ->orderBy('id_mitra', 'desc')
            ->get()
            ->map(function ($s) {
                // Generate WhatsApp clean phone format
                $phone = '+62 812-3456-7890';
                $cleanPhone = preg_replace('/[^0-9]/', '', $phone);
                if (str_starts_with($cleanPhone, '0')) {
                    $cleanPhone = '62' . substr($cleanPhone, 1);
                }

                return [
                    'id_mitra'     => $s->id_mitra,
                    'nama_mitra'   => $s->nama_mitra,
                    'tipe_mitra'   => $s->tipe_mitra,
                    'alamat'       => $s->alamat,
                    'telepon'      => $phone,
                    'wa_link'      => 'https://wa.me/' . $cleanPhone . '?text=' . urlencode("Halo {$s->nama_mitra}, saya dari SIM-BUDIDAYA ingin memesan pasokan pakan ikan. Apakah stok pakan tersedia?"),
                ];
            });

        // 3. Kolam Aktif untuk Input Log Pakan (Pembesaran & Pembibitan)
        $activeBatches = BatchPembesaran::with(['kolam', 'batchPembibitan'])
            ->where('status_siklus', '!=', 'selesai')
            ->where('status_siklus', '!=', 'gagal')
            ->latest('id_pembesaran')
            ->get();

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

        // Kolam Pembibitan Aktif
        $activeHatcheryBatches = BatchPembibitan::with('kolam')
            ->where('status', '!=', 'selesai')
            ->where('status', '!=', 'gagal')
            ->latest('id_batch')
            ->get();

        $hatcheryKolams = $activeHatcheryBatches->map(function ($hb) {
            $benihHidup = max(0, ((int) $hb->jumlah_bibitAwal) - ((int) $hb->jumlah_kematian));
            return [
                'id_kolam'     => $hb->id_kolam,
                'id_batch'     => $hb->id_batch,
                'batch_id'     => '#BB-' . str_pad($hb->id_batch, 5, '0', STR_PAD_LEFT),
                'nama_kolam'   => $hb->kolam ? $hb->kolam->nama_kolam : 'Hatchery #' . $hb->id_kolam,
                'jenis_ikan'   => $hb->jenis_ikan ?? ($hb->ikan ? $hb->ikan->nama_ikan : 'Bibit Ikan'),
                'jumlah_bibit' => $benihHidup,
                'label'        => ($hb->kolam ? $hb->kolam->nama_kolam : 'Hatchery #' . $hb->id_kolam) . ' - #BB-' . str_pad($hb->id_batch, 5, '0', STR_PAD_LEFT) . ' (' . ($hb->jenis_ikan ?? 'Bibit') . ' • ' . number_format($benihHidup, 0, ',', '.') . ' ekor)',
            ];
        });

        // 4. Riwayat Transaksi Pembelian Pakan dari Supplier
        $riwayatPembelian = PembelianPakan::with(['stokPakan', 'mitra', 'user'])
            ->latest('tgl_beli')
            ->take(20)
            ->get();

        // 5. Riwayat Log Pemberian Pakan Harian
        $logs = ManajemenPakan::with(['kolam', 'user', 'stokPakan'])
            ->latest('tgl_log')
            ->take(25)
            ->get();

        // 6. Dynamic Chart Pakan 7 Hari Terakhir
        $pakanGrouped = ManajemenPakan::where('tgl_log', '>=', $startDate7)
            ->selectRaw('DATE(tgl_log) as tanggal, SUM(kg_pelet) as total_pelet, SUM(kg_daun) as total_daun')
            ->groupBy('tanggal')
            ->orderBy('tanggal', 'asc')
            ->get()
            ->keyBy('tanggal');

        $chart7d = ['labels' => [], 'pelet' => [], 'daun' => []];
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $dateStr = $date->toDateString();
            $rec = $pakanGrouped->get($dateStr);
            $chart7d['labels'][] = $date->translatedFormat('D') . ' (' . $date->format('d/m') . ')';
            $chart7d['pelet'][]  = $rec ? round((float) $rec->total_pelet, 1) : 0;
            $chart7d['daun'][]   = $rec ? round((float) $rec->total_daun, 1) : 0;
        }

        return view('layouts.pakan.index', compact(
            'stokPakanList',
            'enrichedStokPakan',
            'stokSummary',
            'suppliers',
            'activeKolams',
            'hatcheryKolams',
            'riwayatPembelian',
            'logs',
            'chart7d'
        ));
    }

    /**
     * Catat Log Pemberian Pakan Harian & Otomatis Potong Saldo Stok Pakan
     */
    public function store(Request $request)
    {
        $request->validate([
            'id_kolam'       => 'required|exists:kolam,id_kolam',
            'id_stok_pakan'  => 'nullable|exists:stok_pakan,id_stok_pakan',
            'kategori_fase'  => 'nullable|in:pembibitan,pembesaran',
            'tgl_log'        => 'nullable|date',
            'kg_pelet'       => 'nullable|numeric|min:0|max:100',
            'kg_daun'        => 'nullable|numeric|min:0|max:100',
            'jenis_daun'     => 'nullable|string',
            'total_biaya'    => 'nullable|numeric|min:0',
            'ph_air'         => 'nullable|numeric',
        ], [
            'kg_pelet.max'   => 'Pemberian pelet maksimal 100 kg per sesi.',
            'kg_daun.max'    => 'Pemberian pakan daun maksimal 100 kg per sesi.',
        ]);

        $fase = $request->kategori_fase ?? 'pembesaran';

        // Validasi Sinkronisasi: Kolam WAJIB memiliki siklus/batch aktif yang belum selesai
        if ($fase === 'pembibitan') {
            $hasActiveBatch = BatchPembibitan::where('id_kolam', $request->id_kolam)
                ->where('status', '!=', 'selesai')
                ->where('status', '!=', 'gagal')
                ->exists();
            if (!$hasActiveBatch) {
                if ($request->wantsJson() || $request->ajax()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Kolam ini belum diisi benih/ikan (tidak ada batch pembibitan aktif). Silakan mulai siklus pembibitan terlebih dahulu!'
                    ], 422);
                }
                return back()->with('error', 'Kolam ini belum diisi benih aktif. Silakan mulai siklus pembibitan terlebih dahulu!');
            }
        } else {
            $hasActiveBatch = BatchPembesaran::where('id_kolam', $request->id_kolam)
                ->where('status_siklus', '!=', 'selesai')
                ->where('status_siklus', '!=', 'gagal')
                ->exists();
            if (!$hasActiveBatch) {
                if ($request->wantsJson() || $request->ajax()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Kolam ini belum diisi ikan (tidak ada batch pembesaran aktif). Silakan tebar benih / mulai siklus pembesaran terlebih dahulu!'
                    ], 422);
                }
                return back()->with('error', 'Kolam ini belum diisi ikan aktif. Silakan tebar benih / mulai siklus pembesaran terlebih dahulu!');
            }
        }

        $tgl = $request->tgl_log ? Carbon::parse($request->tgl_log)->toDateString() : Carbon::today()->toDateString();
        $kgPelet = (float) ($request->kg_pelet ?? 0);
        $kgDaun = (float) ($request->kg_daun ?? 0);
        $totalKg = $kgPelet + $kgDaun;

        // Ambil harga referensi pakan jika ada
        $stokItem = $request->id_stok_pakan ? StokPakan::find($request->id_stok_pakan) : null;
        $hargaPerKg = $stokItem ? (float) $stokItem->harga_per_satuan : 12500;
        $totalBiaya = (float) ($request->total_biaya ?? ($totalKg * $hargaPerKg));

        $log = ManajemenPakan::create([
            'id_user'        => Auth::id() ?? 1,
            'id_kolam'       => $request->id_kolam,
            'id_stok_pakan'  => $request->id_stok_pakan,
            'kategori_fase'  => $request->kategori_fase ?? ($stokItem ? $stokItem->kategori_peruntukan : 'pembesaran'),
            'tgl_log'        => $tgl,
            'kg_pelet'       => $kgPelet,
            'kg_daun'        => $kgDaun,
            'jenis_daun'     => $request->jenis_daun ?: ($stokItem ? $stokItem->nama_pakan : null),
            'total_biaya'    => $totalBiaya,
            'ph_air'         => $request->ph_air ?? 7.0,
        ]);

        // POTONG SALDO STOK PAKAN OTOMATIS
        if ($stokItem && $totalKg > 0) {
            $newStok = max(0, (float) $stokItem->stok_tersisa - $totalKg);
            $stokItem->update(['stok_tersisa' => $newStok]);
        } elseif ($kgPelet > 0) {
            // Fallback potong stok pelet default jika ada
            $defaultPelet = StokPakan::where('nama_pakan', 'like', '%Pelet%')->first();
            if ($defaultPelet) {
                $defaultPelet->update(['stok_tersisa' => max(0, (float) $defaultPelet->stok_tersisa - $kgPelet)]);
            }
        }

        // Update kolam ph air jika diisi
        if ($request->filled('ph_air')) {
            Kolam::where('id_kolam', $request->id_kolam)->update([
                'kesehatan_ph_air' => $request->ph_air
            ]);
        }

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Log pemberian pakan berhasil dicatat dan stok pakan telah otomatis disesuaikan!',
                'log'     => $log->load(['kolam', 'user', 'stokPakan'])
            ]);
        }

        return redirect()->route('log-pakan')->with('success', 'Log pemberian pakan berhasil dicatat & stok otomatis terpotong!');
    }

    /**
     * Catat Pembelian Pakan dari Mitra Supplier (Otomatis Tambah Stok & Masuk Buku Kas Keuangan)
     */
    public function storePembelian(Request $request)
    {
        $validated = $request->validate([
            'id_stok_pakan' => 'required|exists:stok_pakan,id_stok_pakan',
            'id_mitra'      => 'nullable|exists:mitra_distributor,id_mitra',
            'tgl_beli'      => 'required|date',
            'jumlah'        => 'required|numeric|min:0.1|max:1000',
            'harga_satuan'  => 'required|numeric|min:0',
            'total_biaya'   => 'nullable|numeric|min:0',
            'no_nota'       => 'nullable|string|max:100',
            'catatan'       => 'nullable|string',
        ], [
            'jumlah.max'    => 'Jumlah pembelian pakan maksimal 1.000 satuan/kg per transaksi.',
            'jumlah.min'    => 'Jumlah pembelian pakan minimal 0.1 satuan/kg.',
        ]);

        $stokItem = StokPakan::findOrFail($validated['id_stok_pakan']);
        $mitra = $validated['id_mitra'] ? MitraDistributor::find($validated['id_mitra']) : null;
        $jumlah = (float) $validated['jumlah'];
        $hargaSatuan = (float) $validated['harga_satuan'];
        $totalBiaya = (float) ($validated['total_biaya'] ?: ($jumlah * $hargaSatuan));

        // 1. Simpan Transaksi Pembelian Pakan
        $pembelian = PembelianPakan::create([
            'id_stok_pakan' => $stokItem->id_stok_pakan,
            'id_mitra'      => $validated['id_mitra'] ?? null,
            'id_user'       => Auth::id() ?? 1,
            'tgl_beli'      => Carbon::parse($validated['tgl_beli'])->toDateString(),
            'jumlah'        => $jumlah,
            'harga_satuan'  => $hargaSatuan,
            'total_biaya'   => $totalBiaya,
            'no_nota'       => $validated['no_nota'] ?? ('PO-PKN-' . date('Ymd') . '-' . rand(100, 999)),
            'catatan'       => $validated['catatan'] ?? null,
        ]);

        // 2. Tambahkan Saldo Stok Pakan di Gudang
        $stokItem->increment('stok_tersisa', $jumlah);
        if ($hargaSatuan > 0) {
            $stokItem->update(['harga_per_satuan' => $hargaSatuan]);
        }

        // 3. Otomatis Catat Pengeluaran Operasional di Tabel Keuangan
        $namaSupplier = $mitra ? $mitra->nama_mitra : 'Supplier Eksternal';
        Keuangan::create([
            'id_user'           => Auth::id() ?? 1,
            'tanggal_transaksi' => $pembelian->tgl_beli,
            'tipe_transaksi'    => 'pengeluaran',
            'kategori'          => 'pakan',
            'nominal'           => $totalBiaya,
            'keterangan'        => "Pembelian {$stokItem->nama_pakan} ({$jumlah} {$stokItem->satuan}) dari {$namaSupplier}",
            'ref_id'            => 'BELI-PAKAN-' . $pembelian->id_pembelian,
        ]);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => "Pembelian pakan berhasil dicatat! Stok {$stokItem->nama_pakan} bertambah {$jumlah} {$stokItem->satuan} & otomatis dibukukan ke Keuangan.",
                'data'    => $pembelian->load(['stokPakan', 'mitra'])
            ], 201);
        }

        return redirect()->route('log-pakan')->with('success', "Pembelian pakan berhasil dicatat! Stok bertambah & pengeluaran telah dibukukan ke Keuangan.");
    }

    /**
     * Tambah Item Master Stok Pakan Baru
     */
    public function storeStokPakan(Request $request)
    {
        $validated = $request->validate([
            'nama_pakan'          => 'required|string|max:255',
            'kategori_peruntukan' => 'required|in:pembibitan,pembesaran,semua',
            'satuan'              => 'required|string|max:20',
            'stok_tersisa'        => 'nullable|numeric|min:0',
            'batas_minimum'       => 'nullable|numeric|min:0',
            'harga_per_satuan'    => 'nullable|numeric|min:0',
            'keterangan'          => 'nullable|string',
        ]);

        $item = StokPakan::create([
            'nama_pakan'          => $validated['nama_pakan'],
            'kategori_peruntukan' => $validated['kategori_peruntukan'],
            'satuan'              => $validated['satuan'] ?? 'kg',
            'stok_tersisa'        => (float) ($validated['stok_tersisa'] ?? 0),
            'batas_minimum'       => (float) ($validated['batas_minimum'] ?? 10),
            'harga_per_satuan'    => (float) ($validated['harga_per_satuan'] ?? 0),
            'keterangan'          => $validated['keterangan'] ?? null,
        ]);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Item stok pakan baru berhasil ditambahkan!',
                'data'    => $item
            ], 201);
        }

        return redirect()->route('log-pakan')->with('success', 'Item stok pakan baru berhasil ditambahkan!');
    }

    /**
     * Update Data Item Master Stok Pakan
     */
    public function updateStokPakan(Request $request, $id)
    {
        $item = StokPakan::findOrFail($id);

        $validated = $request->validate([
            'nama_pakan'          => 'required|string|max:255',
            'kategori_peruntukan' => 'required|in:pembibitan,pembesaran,semua',
            'satuan'              => 'required|string|max:20',
            'stok_tersisa'        => 'required|numeric|min:0',
            'batas_minimum'       => 'required|numeric|min:0',
            'harga_per_satuan'    => 'required|numeric|min:0',
            'keterangan'          => 'nullable|string',
        ]);

        $item->update($validated);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Data item stok pakan berhasil diperbarui!',
                'data'    => $item
            ]);
        }

        return redirect()->route('log-pakan')->with('success', 'Data item stok pakan berhasil diperbarui!');
    }

    /**
     * Hapus Item Master Stok Pakan
     */
    public function destroyStokPakan($id)
    {
        $item = StokPakan::findOrFail($id);
        $nama = $item->nama_pakan;
        $item->delete();

        return response()->json([
            'success' => true,
            'message' => "Item stok pakan '{$nama}' berhasil dihapus!"
        ]);
    }
}

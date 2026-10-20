<?php

namespace App\Http\Controllers\Manajer;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\BatchPembesaran;
use App\Models\Keuangan;
use App\Models\MitraDistributor;
use App\Models\TransaksiDistribusi;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class DistribusiController extends Controller
{
    public function index()
    {
        $transaksiRecords = TransaksiDistribusi::with(['mitra.user', 'batchPembesaran', 'user'])->latest('id_transaksi')->get();
        $mitraRecords = MitraDistributor::with('user')->get();
        $batchRecords = BatchPembesaran::with('kolam')->where('status_siklus', '!=', 'gagal')->latest('id_pembesaran')->get();

        $orders = [];
        foreach ($transaksiRecords as $t) {
            $status = $t->status_order ?: 'pending';
            $mitraPhone = ($t->mitra && $t->mitra->user && $t->mitra->user->no_tlp)
                ? $t->mitra->user->no_tlp
                : ('08' . (12 + ($t->id_mitra % 7)) . '-' . (1000 + ($t->id_transaksi * 137) % 8999) . '-' . (1234 + ($t->id_mitra * 173) % 8765));

            $orders[] = [
                'id_transaksi'  => $t->id_transaksi,
                'id'            => '#ORD-2023-' . str_pad($t->id_transaksi, 4, '0', STR_PAD_LEFT),
                'id_mitra'      => $t->id_mitra,
                'id_pembesaran' => $t->id_pembesaran,
                'customer'      => $t->mitra ? $t->mitra->nama_mitra : 'Mitra #' . $t->id_mitra,
                'tipe_mitra'    => $t->mitra ? $t->mitra->tipe_mitra : 'Distributor',
                'telepon'       => $mitraPhone,
                'volume'        => number_format($t->Total_kg, 0, ',', '.') . ' kg',
                'total_kg'      => (float) $t->Total_kg,
                'harga_total'   => (float) $t->harga_total,
                'harga_format'  => 'Rp ' . number_format($t->harga_total, 0, ',', '.'),
                'jenis_ikan'    => $t->batchPembesaran ? $t->batchPembesaran->jenis_ikan : ($t->Jenis_order ?? 'Ikan Segar'),
                'kolam_asal'    => $t->batchPembesaran && $t->batchPembesaran->kolam ? $t->batchPembesaran->kolam->nama_kolam : 'Kolam Pembesaran',
                'stok_biomassa' => $t->batchPembesaran ? (float) $t->batchPembesaran->biomassa_est : 0,
                'batch_code'    => $t->batchPembesaran ? ('#PB-' . str_pad($t->id_pembesaran, 5, '0', STR_PAD_LEFT)) : null,
                'jenis_order'   => $t->Jenis_order ?? 'Ikan Segar',
                'status'        => $status,
                'no_surat_jalan'          => $t->getOrGenerateSuratJalan(),
                'waktu_mulai_pemberokan'  => $t->waktu_mulai_pemberokan ? Carbon::parse($t->waktu_mulai_pemberokan)->toIso8601String() : null,
                'waktu_selesai_pemberokan'=> $t->waktu_selesai_pemberokan ? Carbon::parse($t->waktu_selesai_pemberokan)->toIso8601String() : null,
                'durasi_pemberokan_jam'   => $t->durasi_pemberokan_jam ?? 18,
                'is_pemberokan_active'    => $t->isPemberokanActive(),
                'sisa_pemberokan_menit'   => $t->getSisaPemberokanMenit(),
                'grading_size'            => $t->grading_size ?? 'Grade Standar',
                'catatan_grading'         => $t->catatan_grading ?? '',
                'alamat'        => $t->mitra ? $t->mitra->alamat : 'Alamat mitra belum diset',
                'tanggal'       => $t->tanggal_order ? Carbon::parse($t->tanggal_order)->toDateString() : Carbon::today()->toDateString(),
                'tgl_rencana_kirim' => $t->tgl_rencana_kirim ? Carbon::parse($t->tgl_rencana_kirim)->toDateString() : ($t->tanggal_order ? Carbon::parse($t->tanggal_order)->toDateString() : null),
                'tipe_panen'        => $t->tipe_panen ?: 'parsial',
                'berat_aktual_kg'   => $t->berat_aktual_kg !== null ? (float) $t->berat_aktual_kg : (float) $t->Total_kg,
                'susut_bobot_kg'    => (float) ($t->susut_bobot_kg ?? 0),
                'mortalitas_antar_kg' => (float) ($t->mortalitas_antar_kg ?? 0),
                'persentase_susut'  => (float) $t->persentase_susut,
                'is_ddp_aman'       => (bool) $t->is_ddp_aman,
                'is_preorder'       => (bool) ($t->tgl_rencana_kirim && Carbon::parse($t->tgl_rencana_kirim)->isFuture()),
                'bukti_sampai'   => $t->Bukti_sampai,
                'bukti_foto_url' => $t->bukti_sampai_url,
                'petugas_nama'   => $t->user ? $t->user->nama : 'Tim Distribusi Lapangan',
                'waktu_selesai'  => ($status === 'selesai' && $t->updated_at) ? $t->updated_at->translatedFormat('d M Y, H:i') : null,
                'label'         => true
            ];
        }

        $mitraList = $mitraRecords->map(function ($m) {
            return [
                'id_mitra'   => $m->id_mitra,
                'nama_mitra' => $m->nama_mitra,
                'tipe_mitra' => $m->tipe_mitra,
                'alamat'     => $m->alamat,
                'label'      => 'MTR-' . str_pad($m->id_mitra, 3, '0', STR_PAD_LEFT) . ' — ' . $m->nama_mitra . ' (' . $m->tipe_mitra . ')',
            ];
        });

        $batchRecords = BatchPembesaran::with('kolam')
            ->where('status_siklus', '!=', 'gagal')
            ->where('status_siklus', '!=', 'selesai')
            ->latest('id_pembesaran')->get();

        $batches = $batchRecords->map(function ($b) {
            $kolamName = $b->kolam ? $b->kolam->nama_kolam : 'Kolam #' . $b->id_kolam;
            $tipeKolam = $b->kolam ? $b->kolam->tipe_kolam : '';
            $isStok = stripos($kolamName, 'Stok') !== false || stripos($tipeKolam, 'Pemberokan') !== false || stripos($tipeKolam, 'Penampungan') !== false;
            $stokBiomassa = number_format($b->biomassa_est, 0, ',', '.');
            return [
                'id_pembesaran' => $b->id_pembesaran,
                'id_kolam'      => $b->id_kolam,
                'label'         => '#PB-' . str_pad($b->id_pembesaran, 5, '0', STR_PAD_LEFT) . ' — ' . $b->jenis_ikan . ' (' . $kolamName . ' - Stok: ' . $stokBiomassa . ' kg)' . ($isStok ? ' [Kolam Stok / Buffer]' : ''),
                'jenis_ikan'    => $b->jenis_ikan,
                'kolam'         => $kolamName,
                'is_stok'       => $isStok,
                'biomassa_est'  => (float) $b->biomassa_est,
            ];
        });

        $emptyAndStockPonds = \App\Models\Kolam::where(function ($q) {
            $q->where('status', 'kosong')
              ->orWhere('status', '!=', 'aktif')
              ->orWhere('nama_kolam', 'like', '%Stok%')
              ->orWhere('tipe_kolam', 'like', '%Pemberokan%')
              ->orWhere('tipe_kolam', 'like', '%Penampungan%');
        })->get()->map(function ($k) {
            $isKosong = ($k->status === 'kosong' || $k->status !== 'aktif');
            return [
                'id_kolam'   => $k->id_kolam,
                'nama_kolam' => $k->nama_kolam,
                'tipe_kolam' => $k->tipe_kolam,
                'kapasitas'  => $k->kapasitas,
                'is_kosong'  => $isKosong,
                'label'      => $k->nama_kolam . ' (' . $k->tipe_kolam . ') • ' . ($isKosong ? '[Kolam Kosong]' : '[Kolam Penampungan/Buffer]'),
            ];
        });

        $stockPonds = \App\Models\Kolam::where(function ($q) {
            $q->where('nama_kolam', 'like', '%Stok%')
              ->orWhere('tipe_kolam', 'like', '%Pemberokan%')
              ->orWhere('tipe_kolam', 'like', '%Penampungan%');
        })->get()->map(function ($k) {
            return [
                'id_kolam'   => $k->id_kolam,
                'nama_kolam' => $k->nama_kolam,
                'tipe_kolam' => $k->tipe_kolam,
                'kapasitas'  => $k->kapasitas,
            ];
        });

        $totalStokSiapPanen = (float) BatchPembesaran::where('status_siklus', '!=', 'gagal')
            ->where('status_siklus', '!=', 'selesai')
            ->sum('biomassa_est');

        $totalBufferPemberokan = (float) TransaksiDistribusi::whereIn('status_order', ['dalam_pengiriman', 'pemberokian', 'siap_kirim'])
            ->sum('Total_kg');

        return view('layouts.distribusi.index', compact(
            'orders', 
            'mitraList', 
            'mitraRecords', 
            'batches',
            'stockPonds',
            'emptyAndStockPonds',
            'totalStokSiapPanen',
            'totalBufferPemberokan'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'id_mitra'      => 'required|exists:mitra_distributor,id_mitra',
            'id_pembesaran' => 'required|exists:batch_pembesaran,id_pembesaran',
            'tanggal_order' => 'nullable|date',
            'Total_kg'      => 'required|numeric|min:1',
            'harga_total'   => 'nullable|numeric|min:0',
            'Jenis_order'   => 'nullable|string',
            'status_order'  => 'nullable|string',
            'id_kolam_surplus' => 'nullable|exists:kolam,id_kolam',
        ], [
            'id_mitra.required'      => 'Mitra Distributor wajib dipilih.',
            'id_pembesaran.required' => 'Batch Pembesaran / Jenis Ikan wajib dipilih.',
            'Total_kg.required'      => 'Total berat (kg) wajib diisi.',
        ]);

        $status = $request->status_order ?? 'pending';
        $totalKg = (float) $request->Total_kg;
        $hargaTotal = (float) ($request->harga_total ?? ($totalKg * 35000));
        $jenisOrder = $request->Jenis_order ?? 'Ikan Segar';

        $primaryBatch = BatchPembesaran::with('kolam')->find($request->id_pembesaran);
        if (!$primaryBatch) {
            return response()->json(['success' => false, 'message' => 'Batch pembesaran tidak ditemukan.'], 404);
        }

        // Jika status langsung 'pemberokian' atau 'siap_kirim', eksekusi alokasi stok ikan
        if (in_array($status, ['pemberokian', 'siap_kirim'])) {
            $allocationResult = $this->allocateFishStock(
                $primaryBatch,
                $totalKg,
                $request->boolean('panen_kuras'),
                $request->id_kolam_surplus,
                $request->alokasi_detail
            );

            if (!$allocationResult['success']) {
                // Jika stok di seluruh kolam sejenis tidak mencukupi, paksa status tetap 'pending'
                $status = 'pending';
                if ($request->wantsJson() || $request->ajax()) {
                    return response()->json([
                        'success' => false,
                        'message' => $allocationResult['message'] . ' Pesanan disimpan dengan status PENDING.'
                    ], 422);
                }
            } else {
                $jenisOrder = $allocationResult['jenis_order'] ?? $jenisOrder;
            }
        }

        $noSuratJalan = TransaksiDistribusi::generateNoSuratJalan();
        $durasiPemberokan = (int) ($request->durasi_pemberokan_jam ?: 18);
        $waktuMulaiPemberokan = null;
        $waktuSelesaiPemberokan = null;

        if (in_array($status, ['pemberokan', 'pemberokian'])) {
            $waktuMulaiPemberokan = now();
            $waktuSelesaiPemberokan = now()->addHours($durasiPemberokan);
        }

        $tglOrder = $request->tanggal_order ? Carbon::parse($request->tanggal_order)->toDateString() : Carbon::today()->toDateString();
        $tglKirim = $request->tgl_rencana_kirim ? Carbon::parse($request->tgl_rencana_kirim)->toDateString() : $tglOrder;
        $tipePanen = $request->tipe_panen ?: ($request->boolean('panen_kuras') ? 'kuras' : 'parsial');

        // Jika pesanan adalah Pre-Order (dikirim lebih dari 1 hari ke depan) dan status dipilih 'pemberokian',
        // sistem menetapkan status awal 'pending' (booking) agar ikan tetap makan normal di kolam asal sampai H-1!
        $isPreOrder = Carbon::parse($tglKirim)->isFuture() && Carbon::parse($tglKirim)->diffInDays(Carbon::today()) >= 1;
        if ($isPreOrder && in_array($status, ['pemberokian', 'pemberokan'])) {
            $status = 'pending';
        }

        $transaksi = TransaksiDistribusi::create([
            'no_surat_jalan'           => $noSuratJalan,
            'id_user'                  => Auth::id() ?? 1,
            'id_mitra'                 => $request->id_mitra,
            'id_pembesaran'            => $request->id_pembesaran,
            'tanggal_order'            => $tglOrder,
            'tgl_rencana_kirim'        => $tglKirim,
            'tipe_panen'               => $tipePanen,
            'Total_kg'                 => $totalKg,
            'berat_aktual_kg'          => $totalKg,
            'susut_bobot_kg'           => 0,
            'mortalitas_antar_kg'      => 0,
            'harga_total'              => $hargaTotal,
            'status_order'             => $status,
            'waktu_mulai_pemberokan'   => $waktuMulaiPemberokan,
            'waktu_selesai_pemberokan' => $waktuSelesaiPemberokan,
            'durasi_pemberokan_jam'    => $durasiPemberokan,
            'grading_size'             => $request->grading_size ?? 'Grade Standar',
            'catatan_grading'          => $request->catatan_grading,
            'Jenis_order'              => $jenisOrder,
        ]);

        AuditLog::record(
            'tambah_order',
            'distribusi',
            "Membuat order distribusi baru #ORD-" . str_pad($transaksi->id_transaksi, 4, '0', STR_PAD_LEFT) . " ({$noSuratJalan}, {$totalKg} kg {$jenisOrder})"
        );

        // Otomatisasi Kas Masuk ke Buku Keuangan saat status SIAP KIRIM (SOP Pelunasan di Awal)
        if ($transaksi->status_order === 'siap_kirim') {
            $this->recordIncomeToKeuangan($transaksi);
        }

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success'   => true,
                'message'   => 'Pesanan distribusi baru berhasil ditambahkan' . ($status === 'pending' ? ' (Status: PENDING - Menunggu Panen/Verifikasi)' : ($status === 'siap_kirim' ? ' (Status: SIAP KIRIM - Kas Masuk Otomatis Terbukukan ke Keuangan)' : ' dan dialokasikan ke pemberokian!')) . '!',
                'transaksi' => $transaksi->load(['mitra', 'batchPembesaran'])
            ]);
        }

        return redirect()->route('distribusi')->with('success', 'Pesanan distribusi berhasil disimpan!');
    }

    public function update(Request $request, $id)
    {
        $cleanId = is_numeric($id) ? $id : (int) preg_replace('/[^0-9]/', '', $id);
        $transaksi = TransaksiDistribusi::with(['batchPembesaran.kolam', 'mitra'])->find($cleanId);

        if (!$transaksi) {
            return response()->json(['success' => false, 'message' => 'Transaksi tidak ditemukan.'], 404);
        }

        $oldStatus = $transaksi->status_order ?? 'pending';
        $newStatus = $request->input('status_order', $oldStatus);

        // Validasi: Status hanya dapat dibatalkan jika saat ini masih 'pending'
        if ($newStatus === 'dibatalkan' && $oldStatus !== 'pending') {
            $msg = 'Pesanan tidak dapat dibatalkan karena sudah masuk tahap ' . strtoupper(str_replace('_', ' ', $oldStatus)) . '. Pembatalan hanya diperbolehkan saat status masih Pending.';
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => $msg
                ], 422);
            }
            return redirect()->back()->with('error', $msg);
        }

        if ($request->filled('Total_kg')) {
            $transaksi->Total_kg = (float) $request->Total_kg;
        }

        if ($request->filled('harga_total')) {
            $transaksi->harga_total = (float) $request->harga_total;
        }

        // Jika status berubah dari 'pending' menjadi 'pemberokian' atau 'siap_kirim'
        if ($oldStatus === 'pending' && in_array($newStatus, ['pemberokian', 'siap_kirim'])) {
            $primaryBatch = $transaksi->batchPembesaran;

            if (!$primaryBatch) {
                return response()->json([
                    'success' => false,
                    'message' => 'Batch pembesaran asal tidak ditemukan.'
                ], 404);
            }

            $allocationResult = $this->allocateFishStock(
                $primaryBatch,
                (float) $transaksi->Total_kg,
                $request->boolean('panen_kuras'),
                $request->id_kolam_surplus,
                $request->alokasi_detail
            );

            if (!$allocationResult['success']) {
                // Jangan ubah status, tetap PENDING
                return response()->json([
                    'success' => false,
                    'message' => $allocationResult['message']
                ], 422);
            }

            if (!empty($allocationResult['jenis_order'])) {
                $transaksi->Jenis_order = $allocationResult['jenis_order'];
            }
        }

        if ($request->filled('durasi_pemberokan_jam')) {
            $transaksi->durasi_pemberokan_jam = (int) $request->durasi_pemberokan_jam;
        }
        if ($request->filled('grading_size')) {
            $transaksi->grading_size = $request->grading_size;
        }
        if ($request->filled('catatan_grading')) {
            $transaksi->catatan_grading = $request->catatan_grading;
        }
        if ($request->filled('tgl_rencana_kirim')) {
            $transaksi->tgl_rencana_kirim = Carbon::parse($request->tgl_rencana_kirim)->toDateString();
        }
        if ($request->filled('tipe_panen')) {
            $transaksi->tipe_panen = $request->tipe_panen;
        }
        if ($request->filled('berat_aktual_kg')) {
            $transaksi->berat_aktual_kg = (float) $request->berat_aktual_kg;
            $transaksi->susut_bobot_kg = max(0, (float)$transaksi->Total_kg - (float)$request->berat_aktual_kg);
        }
        if ($request->filled('mortalitas_antar_kg')) {
            $transaksi->mortalitas_antar_kg = (float) $request->mortalitas_antar_kg;
        }

        if (in_array($newStatus, ['pemberokan', 'pemberokian']) && !$transaksi->waktu_mulai_pemberokan) {
            $durasi = $transaksi->durasi_pemberokan_jam ?: 18;
            $transaksi->waktu_mulai_pemberokan = now();
            $transaksi->waktu_selesai_pemberokan = now()->addHours($durasi);
        }

        if (empty($transaksi->no_surat_jalan)) {
            $transaksi->no_surat_jalan = $transaksi->getOrGenerateSuratJalan();
        }

        $transaksi->status_order = $newStatus;
        $transaksi->save();

        AuditLog::record(
            'ubah_status_distribusi',
            'distribusi',
            "Mengubah status order #ORD-" . str_pad($transaksi->id_transaksi, 4, '0', STR_PAD_LEFT) . " dari " . strtoupper(str_replace('_', ' ', $oldStatus)) . " ke " . strtoupper(str_replace('_', ' ', $newStatus))
        );

        // Otomatisasi Kas Masuk ke Buku Keuangan saat status Siap Kirim, Dalam Pengiriman, atau Selesai
        if (in_array($newStatus, ['siap_kirim', 'dalam_pengiriman', 'selesai'])) {
            $this->recordIncomeToKeuangan($transaksi);
        } elseif (in_array($newStatus, ['pending', 'pemberokian', 'dibatalkan'])) {
            $orderRef = 'ORD-' . str_pad($transaksi->id_transaksi, 4, '0', STR_PAD_LEFT);
            Keuangan::where('ref_id', $orderRef)->delete();
        }

        if ($request->wantsJson() || $request->ajax()) {
            $msg = 'Status order distribusi berhasil diperbarui ke: ' . strtoupper(str_replace('_', ' ', $newStatus)) . '!';
            if (in_array($newStatus, ['siap_kirim', 'dalam_pengiriman', 'selesai'])) {
                $msg .= ' Kas masuk telah otomatis dibukukan ke Keuangan.';
            }
            return response()->json([
                'success'   => true,
                'message'   => $msg,
                'transaksi' => $transaksi->load(['mitra', 'batchPembesaran'])
            ]);
        }

        return redirect()->route('distribusi')->with('success', 'Transaksi berhasil diperbarui!');
    }

    /**
     * Otomatis Catat Kas Masuk (Pemasukan Penjualan Ikan) saat status menjadi Siap Kirim, Dalam Pengiriman, atau Selesai
     */
    private function recordIncomeToKeuangan(TransaksiDistribusi $transaksi)
    {
        $transaksi->loadMissing(['mitra', 'batchPembesaran.kolam']);
        $mitraNama = $transaksi->mitra ? $transaksi->mitra->nama_mitra : ('Mitra #' . $transaksi->id_mitra);
        $species = $transaksi->batchPembesaran ? $transaksi->batchPembesaran->jenis_ikan : ($transaksi->Jenis_order ?? 'Ikan Segar');
        $idKolam = $transaksi->batchPembesaran ? $transaksi->batchPembesaran->id_kolam : null;
        $orderRef = 'ORD-' . str_pad($transaksi->id_transaksi, 4, '0', STR_PAD_LEFT);
        $totalKgFormatted = number_format($transaksi->Total_kg, 1, ',', '.');
        $statusLabel = strtoupper(str_replace('_', ' ', $transaksi->status_order ?? 'Siap Kirim'));

        Keuangan::updateOrCreate(
            ['ref_id' => $orderRef],
            [
                'id_user'           => Auth::id() ?? 1,
                'id_kolam'          => $idKolam,
                'tanggal_transaksi' => $transaksi->tanggal_order ? Carbon::parse($transaksi->tanggal_order)->toDateString() : Carbon::today()->toDateString(),
                'tipe_transaksi'    => 'pemasukan',
                'kategori'          => 'Penjualan Panen ' . $species,
                'nominal'           => (float) $transaksi->harga_total,
                'keterangan'        => "Pelunasan transaksi #{$orderRef} ({$totalKgFormatted} kg {$species}) oleh {$mitraNama} [Status: {$statusLabel}]",
            ]
        );
    }

    /**
     * Helper untuk alokasi stok ikan ke pemberokian, transfer surplus ke kolam stok, dan cross-batch.
     */
    private function allocateFishStock(BatchPembesaran $primaryBatch, float $totalKg, bool $panenKuras = false, $idKolamSurplus = null, $alokasiDetail = null)
    {
        $species = $primaryBatch->jenis_ikan ?? 'Ikan Segar';
        $primaryAvailable = (float) $primaryBatch->biomassa_est;

        // Cari kolam stok / buffer penampungan default jika tidak dipilih
        $targetKolamSurplus = null;
        if (!empty($idKolamSurplus)) {
            $targetKolamSurplus = \App\Models\Kolam::find($idKolamSurplus);
        }
        if (!$targetKolamSurplus) {
            $targetKolamSurplus = \App\Models\Kolam::where('status', 'kosong')
                ->orWhere('nama_kolam', 'like', '%Stok%')
                ->orWhere('tipe_kolam', 'like', '%Pemberokan%')
                ->orWhere('tipe_kolam', 'like', '%Penampungan%')
                ->first();
        }

        // 1. Jika pengguna memberikan rincian alokasi manual (multi-kolam)
        if (!empty($alokasiDetail) && is_array($alokasiDetail)) {
            $parts = [];
            $allocatedTotal = 0;

            if (!empty($alokasiDetail['utama_kg'])) {
                $takeUtama = min($primaryAvailable, (float)$alokasiDetail['utama_kg']);
                $kolamUtamaNama = $primaryBatch->kolam ? $primaryBatch->kolam->nama_kolam : 'Kolam Utama';
                $parts[] = "{$kolamUtamaNama}: {$takeUtama}kg";
                $primaryBatch->biomassa_est = max(0, $primaryBatch->biomassa_est - $takeUtama);
                if ($primaryBatch->biomassa_est <= 0) {
                    $primaryBatch->status_siklus = 'selesai';
                    if ($primaryBatch->kolam) $primaryBatch->kolam->update(['status' => 'kosong']);
                }
                $primaryBatch->save();
                $allocatedTotal += $takeUtama;
            }

            if (!empty($alokasiDetail['buffer_kg']) && (float)$alokasiDetail['buffer_kg'] > 0 && !empty($alokasiDetail['id_batch_buffer'])) {
                $bufBatch = BatchPembesaran::with('kolam')->find($alokasiDetail['id_batch_buffer']);
                if ($bufBatch) {
                    $takeBuf = min((float)$bufBatch->biomassa_est, (float)$alokasiDetail['buffer_kg']);
                    $bufferNama = $bufBatch->kolam ? $bufBatch->kolam->nama_kolam : 'Kolam Stok';
                    $parts[] = "{$bufferNama}: {$takeBuf}kg";
                    $bufBatch->biomassa_est = max(0, $bufBatch->biomassa_est - $takeBuf);
                    if ($bufBatch->biomassa_est <= 0) {
                        $bufBatch->status_siklus = 'selesai';
                        if ($bufBatch->kolam) $bufBatch->kolam->update(['status' => 'kosong']);
                    }
                    $bufBatch->save();
                    $allocatedTotal += $takeBuf;
                }
            }

            if (!empty($alokasiDetail['cross_kg']) && (float)$alokasiDetail['cross_kg'] > 0 && !empty($alokasiDetail['id_batch_cross'])) {
                $crossBatch = BatchPembesaran::with('kolam')->find($alokasiDetail['id_batch_cross']);
                if ($crossBatch) {
                    $takeCross = min((float)$crossBatch->biomassa_est, (float)$alokasiDetail['cross_kg']);
                    $crossNama = $crossBatch->kolam ? $crossBatch->kolam->nama_kolam : 'Cross-Batch';
                    $parts[] = "{$crossNama}: {$takeCross}kg";
                    $crossBatch->biomassa_est = max(0, $crossBatch->biomassa_est - $takeCross);
                    if ($crossBatch->biomassa_est <= 0) {
                        $crossBatch->status_siklus = 'selesai';
                        if ($crossBatch->kolam) $crossBatch->kolam->update(['status' => 'kosong']);
                    }
                    $crossBatch->save();
                    $allocatedTotal += $takeCross;
                }
            }

            if ($allocatedTotal < $totalKg) {
                return [
                    'success' => false,
                    'message' => "Total alokasi ({$allocatedTotal} kg) masih kurang dari kebutuhan order ({$totalKg} kg)."
                ];
            }

            return [
                'success'     => true,
                'jenis_order' => "{$species} (Multi-Kolam: " . implode(', ', $parts) . ")"
            ];
        }

        // 2. Skenario Otomatis: Stok Kolam Utama Mencukupi / Surplus
        if ($primaryAvailable >= $totalKg) {
            $surplusKg = $primaryAvailable - $totalKg;

            if ($panenKuras && $targetKolamSurplus && $surplusKg > 0) {
                // Kuras kolam utama, habiskan batch asal
                $primaryBatch->biomassa_est = 0;
                $primaryBatch->status_siklus = 'selesai';
                $primaryBatch->save();
                if ($primaryBatch->kolam) {
                    $primaryBatch->kolam->update(['status' => 'kosong']);
                }

                // Pindahkan surplus ke kolam stok/penampungan
                $targetBatch = BatchPembesaran::where('id_kolam', $targetKolamSurplus->id_kolam)
                    ->whereIn('status_siklus', ['aktif', 'siap_panen'])
                    ->where('jenis_ikan', $species)
                    ->first();

                if ($targetBatch) {
                    $targetBatch->biomassa_est = (float)$targetBatch->biomassa_est + $surplusKg;
                    $targetBatch->save();
                } else {
                    BatchPembesaran::create([
                        'id_kolam'            => $targetKolamSurplus->id_kolam,
                        'id_user'             => Auth::id() ?? 1,
                        'id_batch_pembibitan' => $primaryBatch->id_batch_pembibitan,
                        'tgl_tebar'           => Carbon::today()->toDateString(),
                        'biomassa_est'        => $surplusKg,
                        'target_panen_kg'     => $surplusKg,
                        'jenis_ikan'          => $species,
                        'status_siklus'       => 'siap_panen',
                    ]);
                    $targetKolamSurplus->update(['status' => 'aktif']);
                }

                return [
                    'success'     => true,
                    'jenis_order' => "{$species} (Panen Kuras: {$totalKg}kg ke Pemberokian, sisa {$surplusKg}kg -> {$targetKolamSurplus->nama_kolam})"
                ];
            } else {
                // Sisakan di kolam utama
                $primaryBatch->biomassa_est = $surplusKg;
                if ($surplusKg <= 0) {
                    $primaryBatch->status_siklus = 'selesai';
                    if ($primaryBatch->kolam) $primaryBatch->kolam->update(['status' => 'kosong']);
                }
                $primaryBatch->save();

                return [
                    'success'     => true,
                    'jenis_order' => "{$species}"
                ];
            }
        }

        // 3. Skenario Otomatis: Stok Kolam Utama Kurang (Defisit) -> Cari Kolam Lain Sejenis
        $deficitKg = $totalKg - $primaryAvailable;

        // Cari kolam lain yang memiliki jenis ikan yang SAMA dan status aktif / siap_panen
        $otherBatches = BatchPembesaran::with('kolam')
            ->where('id_pembesaran', '!=', $primaryBatch->id_pembesaran)
            ->where('jenis_ikan', $species)
            ->whereIn('status_siklus', ['aktif', 'siap_panen'])
            ->where('biomassa_est', '>', 0)
            ->orderByDesc('biomassa_est')
            ->get();

        $totalOtherAvailable = (float) $otherBatches->sum('biomassa_est');
        $totalPoolAvailable = $primaryAvailable + $totalOtherAvailable;

        // VALIDASI KETAT: Jika seluruh kolam sejenis belum mencukupi -> GAGAL / PENDING
        if ($totalPoolAvailable < $totalKg) {
            return [
                'success' => false,
                'message' => "Stok ikan {$species} dari seluruh kolam belum mencukupi (Total tersedia: {$totalPoolAvailable} kg, Order: {$totalKg} kg). Tidak ada kolam lain dengan jenis ikan dan bobot yang memenuhi. Status pesanan tetap PENDING hingga kolam siap panen."
            ];
        }

        // Jika mencukupi: Kuras kolam utama dulu
        $parts = [];
        $kolamUtamaNama = $primaryBatch->kolam ? $primaryBatch->kolam->nama_kolam : 'Kolam Utama';
        $parts[] = "{$kolamUtamaNama}: {$primaryAvailable}kg";

        $primaryBatch->biomassa_est = 0;
        $primaryBatch->status_siklus = 'selesai';
        if ($primaryBatch->kolam) $primaryBatch->kolam->update(['status' => 'kosong']);
        $primaryBatch->save();

        // Ambil sisa defisit dari kolam-kolam lain yang sejenis
        $remainingNeeded = $deficitKg;
        foreach ($otherBatches as $other) {
            if ($remainingNeeded <= 0) break;

            $avail = (float) $other->biomassa_est;
            $take = min($avail, $remainingNeeded);
            $remainingNeeded -= $take;

            $otherKolamNama = $other->kolam ? $other->kolam->nama_kolam : 'Kolam #' . $other->id_kolam;
            $parts[] = "{$otherKolamNama}: {$take}kg";

            $other->biomassa_est = max(0, $avail - $take);
            if ($other->biomassa_est <= 0) {
                $other->status_siklus = 'selesai';
                if ($other->kolam) $other->kolam->update(['status' => 'kosong']);
            }
            $other->save();
        }

        return [
            'success'     => true,
            'jenis_order' => "{$species} (Lintas Kolam: " . implode(', ', $parts) . ")"
        ];
    }

    public function destroy($id)
    {
        $cleanId = is_numeric($id) ? $id : (int) preg_replace('/[^0-9]/', '', $id);
        $transaksi = TransaksiDistribusi::find($cleanId);

        if (!$transaksi) {
            return response()->json(['success' => false, 'message' => 'Pesanan distribusi tidak ditemukan.'], 404);
        }

        if (!in_array($transaksi->status_order, ['pending', 'dibatalkan'])) {
            return response()->json([
                'success' => false,
                'message' => 'Pesanan yang sedang dalam proses pengiriman atau selesai tidak dapat dihapus.'
            ], 422);
        }

        $orderRef = 'ORD-' . str_pad($transaksi->id_transaksi, 4, '0', STR_PAD_LEFT);
        Keuangan::where('ref_id', $orderRef)->delete();

        AuditLog::record(
            'hapus_order',
            'distribusi',
            "Menghapus order distribusi #ORD-" . str_pad($cleanId, 4, '0', STR_PAD_LEFT)
        );

        $transaksi->delete();

        if (request()->wantsJson() || request()->ajax()) {
            return response()->json([
                'success' => true,
                'message' => "Order distribusi #ORD-" . str_pad($cleanId, 4, '0', STR_PAD_LEFT) . " berhasil dihapus!"
            ]);
        }

        return redirect()->route('distribusi')->with('success', 'Order distribusi berhasil dihapus!');
    }

    public function cetakSuratJalanPdf(Request $request, $id)
    {
        $cleanId = is_numeric($id) ? $id : (int) preg_replace('/[^0-9]/', '', $id);
        $order = TransaksiDistribusi::with(['mitra.user', 'batchPembesaran.kolam', 'user'])->findOrFail($cleanId);
        $order->getOrGenerateSuratJalan();

        $pdf = Pdf::loadView('layouts.distribusi.surat_jalan_pdf', compact('order'))
            ->setPaper('a4', 'portrait');

        $safeNo = str_replace(['/', '\\'], '-', $order->no_surat_jalan ?: ('SJ-' . $order->id_transaksi));
        if ($request->query('download') === '1') {
            return $pdf->download("Surat_Jalan_{$safeNo}.pdf");
        }
        return $pdf->stream("Surat_Jalan_{$safeNo}.pdf");
    }

    public function exportRekapPdf(?Request $request = null)
    {
        $orders = TransaksiDistribusi::with(['mitra', 'batchPembesaran.kolam', 'user'])
            ->latest('tanggal_order')
            ->get();

        $totalKg = $orders->sum('Total_kg');
        $totalNilai = $orders->sum('harga_total');

        $pdf = Pdf::loadView('layouts.distribusi.rekap_pdf', compact('orders', 'totalKg', 'totalNilai'))
            ->setPaper('a4', 'landscape');

        return $pdf->download('Rekapitulasi_Distribusi_' . date('Ymd') . '.pdf');
    }
}

<?php

namespace App\Http\Controllers\Manajer;

use App\Http\Controllers\Controller;
use App\Models\Keuangan;
use App\Models\ManajemenPakan;
use App\Models\MitraDistributor;
use App\Models\PembelianPakan;
use App\Models\StokPakan;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class StokPakanController extends Controller
{
    /**
     * Tampilkan Halaman Master Stok Pakan (Tabel Only)
     */
    public function index(Request $request)
    {
        // 1. Ambil Semua Master Stok Pakan dengan Burn Rate & Sisa Hari
        $stokPakanList = StokPakan::with('pembelian.mitra')->latest('id_stok_pakan')->get();

        // Hitung pemakaian 7 hari terakhir per pakan untuk burn rate
        $startDate7 = Carbon::now()->subDays(6)->startOfDay();
        $pakanUsage7d = ManajemenPakan::where('tgl_log', '>=', $startDate7)
            ->selectRaw('id_stok_pakan, SUM(COALESCE(kg_pelet, 0) + COALESCE(kg_daun, 0)) as total_kg')
            ->groupBy('id_stok_pakan')
            ->pluck('total_kg', 'id_stok_pakan');

        $stokSummary = [
            'total_items'         => $stokPakanList->count(),
            'total_stok_kg'       => $stokPakanList->sum('stok_tersisa'),
            'stok_pembibitan_kg'  => $stokPakanList->where('kategori_peruntukan', 'pembibitan')->sum('stok_tersisa'),
            'stok_pembesaran_kg'  => $stokPakanList->whereIn('kategori_peruntukan', ['pembesaran', 'semua'])->sum('stok_tersisa'),
            'item_kritis_count'   => 0,
            'item_waspada_count'  => 0,
            'item_aman_count'     => 0,
        ];

        $enrichedStokPakan = $stokPakanList->map(function ($item) use ($pakanUsage7d, &$stokSummary) {
            $used7d = (float) ($pakanUsage7d->get($item->id_stok_pakan) ?? 0);

            // Fallback estimasi jika belum ada riwayat log
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
                $dotClass = 'bg-rose-500';
                $stokSummary['item_kritis_count']++;
            } elseif ($sisaHari <= 7) {
                $status = 'waspada';
                $statusLabel = 'Perlu Pesan';
                $statusBadge = 'bg-amber-100 text-amber-800 border-amber-200';
                $dotClass = 'bg-amber-500';
                $stokSummary['item_waspada_count']++;
            } else {
                $status = 'aman';
                $statusLabel = 'Stok Aman';
                $statusBadge = 'bg-emerald-100 text-emerald-800 border-emerald-200';
                $dotClass = 'bg-emerald-500';
                $stokSummary['item_aman_count']++;
            }

            return [
                'id_stok_pakan'       => $item->id_stok_pakan,
                'kode_pakan'          => 'PKN-' . str_pad($item->id_stok_pakan, 4, '0', STR_PAD_LEFT),
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
                'dot_class'           => $dotClass,
            ];
        });

        // 2. Ambil Mitra Khusus Supplier untuk Modal Order WA & Pembelian
        $suppliers = MitraDistributor::where('tipe_mitra', 'like', '%supplier%')
            ->orWhere('tipe_mitra', 'like', '%distributor%')
            ->orderBy('id_mitra', 'desc')
            ->get()
            ->map(function ($s) {
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
                    'wa_link'      => 'https://wa.me/' . $cleanPhone . '?text=' . urlencode("Halo {$s->nama_mitra}, saya dari AMS BUDIDAYA ingin memesan pasokan pakan ikan. Apakah stok pakan tersedia?"),
                ];
            });

        return view('layouts.stok_pakan.index', [
            'stokPakan'    => $enrichedStokPakan,
            'stokSummary'  => $stokSummary,
            'suppliers'    => $suppliers,
        ]);
    }

    /**
     * Tambah Master Item Pakan Baru
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_pakan'          => 'required|string|max:255',
            'kategori_peruntukan' => 'required|in:pembibitan,pembesaran,semua',
            'satuan'              => 'required|string|max:50',
            'stok_tersisa'        => 'required|numeric|min:0',
            'batas_minimum'       => 'required|numeric|min:0',
            'harga_per_satuan'    => 'required|numeric|min:0',
            'keterangan'          => 'nullable|string',
        ], [
            'nama_pakan.required'          => 'Nama jenis pakan wajib diisi.',
            'kategori_peruntukan.required' => 'Kategori peruntukan pakan wajib dipilih.',
            'satuan.required'              => 'Satuan pakan wajib diisi.',
            'stok_tersisa.required'        => 'Jumlah stok awal wajib diisi.',
            'batas_minimum.required'       => 'Batas minimum peringatan wajib diisi.',
            'harga_per_satuan.required'    => 'Harga acuan per satuan wajib diisi.',
        ]);

        $stok = StokPakan::create([
            'nama_pakan'          => $validated['nama_pakan'],
            'kategori_peruntukan' => $validated['kategori_peruntukan'],
            'satuan'              => strtolower($validated['satuan']),
            'stok_tersisa'        => $validated['stok_tersisa'],
            'batas_minimum'       => $validated['batas_minimum'],
            'harga_per_satuan'    => $validated['harga_per_satuan'],
            'keterangan'          => $validated['keterangan'] ?? null,
        ]);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => "Master item pakan '{$stok->nama_pakan}' berhasil ditambahkan ke katalog!",
                'item'    => $stok
            ]);
        }

        return redirect()->route('stok-pakan')->with('success', "Master item pakan '{$stok->nama_pakan}' berhasil ditambahkan!");
    }

    /**
     * Update Master Item Pakan
     */
    public function update(Request $request, $id)
    {
        $stok = StokPakan::findOrFail($id);

        $validated = $request->validate([
            'nama_pakan'          => 'required|string|max:255',
            'kategori_peruntukan' => 'required|in:pembibitan,pembesaran,semua',
            'satuan'              => 'required|string|max:50',
            'stok_tersisa'        => 'required|numeric|min:0',
            'batas_minimum'       => 'required|numeric|min:0',
            'harga_per_satuan'    => 'required|numeric|min:0',
            'keterangan'          => 'nullable|string',
        ]);

        $stok->update([
            'nama_pakan'          => $validated['nama_pakan'],
            'kategori_peruntukan' => $validated['kategori_peruntukan'],
            'satuan'              => strtolower($validated['satuan']),
            'stok_tersisa'        => $validated['stok_tersisa'],
            'batas_minimum'       => $validated['batas_minimum'],
            'harga_per_satuan'    => $validated['harga_per_satuan'],
            'keterangan'          => $validated['keterangan'] ?? null,
        ]);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => "Master item pakan '{$stok->nama_pakan}' berhasil diperbarui!",
                'item'    => $stok
            ]);
        }

        return redirect()->route('stok-pakan')->with('success', "Master item pakan '{$stok->nama_pakan}' berhasil diperbarui!");
    }

    /**
     * Hapus Master Item Pakan
     */
    public function destroy($id)
    {
        $stok = StokPakan::findOrFail($id);
        $nama = $stok->nama_pakan;
        $stok->delete();

        if (request()->wantsJson() || request()->ajax()) {
            return response()->json([
                'success' => true,
                'message' => "Master item pakan '{$nama}' berhasil dihapus dari sistem."
            ]);
        }

        return redirect()->route('stok-pakan')->with('success', "Master item pakan '{$nama}' berhasil dihapus.");
    }

    /**
     * Catat Pembelian / Restock Pakan dari Mitra Supplier
     */
    public function storePembelian(Request $request)
    {
        $validated = $request->validate([
            'id_stok_pakan' => 'required|exists:stok_pakan,id_stok_pakan',
            'id_mitra'      => 'nullable|exists:mitra_distributor,id_mitra',
            'tgl_beli'      => 'required|date',
            'jumlah'        => 'required|numeric|min:0.1|max:10000',
            'harga_satuan'  => 'required|numeric|min:0',
            'total_biaya'   => 'nullable|numeric|min:0',
        ], [
            'id_stok_pakan.required' => 'Silakan pilih jenis pakan yang dibeli.',
            'jumlah.required'        => 'Jumlah pasokan pakan wajib diisi.',
            'harga_satuan.required'  => 'Harga per satuan wajib diisi.',
        ]);

        $stokItem = StokPakan::find($validated['id_stok_pakan']);
        $mitra = $validated['id_mitra'] ? MitraDistributor::find($validated['id_mitra']) : null;
        $jumlahBeli = (float) $validated['jumlah'];
        $hargaSatuan = (float) $validated['harga_satuan'];
        $totalBiaya = (float) ($request->total_biaya ?: ($jumlahBeli * $hargaSatuan));
        $tglBeli = Carbon::parse($validated['tgl_beli'])->toDateString();

        // 1. Simpan Rekap Pembelian Pakan
        $pembelian = PembelianPakan::create([
            'id_user'        => Auth::id() ?? 1,
            'id_stok_pakan'  => $stokItem->id_stok_pakan,
            'id_mitra'       => $mitra ? $mitra->id_mitra : null,
            'nama_pakan'     => $stokItem->nama_pakan,
            'tgl_beli'       => $tglBeli,
            'jumlah'         => $jumlahBeli,
            'harga_satuan'   => $hargaSatuan,
            'total_biaya'    => $totalBiaya,
            'keterangan'     => $request->keterangan ?? "Restock pakan {$stokItem->nama_pakan} dari " . ($mitra ? $mitra->nama_mitra : 'Supplier'),
        ]);

        // 2. OTOMATIS TAMBAHKAN STOK DI GUDANG
        $stokItem->update([
            'stok_tersisa'     => (float) $stokItem->stok_tersisa + $jumlahBeli,
            'harga_per_satuan' => $hargaSatuan > 0 ? $hargaSatuan : $stokItem->harga_per_satuan,
        ]);

        // 3. OTOMATIS CATAT PENGELUARAN KE BUKU KAS KEUANGAN
        Keuangan::create([
            'id_user'           => Auth::id() ?? 1,
            'id_kolam'          => null,
            'tanggal_transaksi' => $tglBeli,
            'tipe_transaksi'    => 'pengeluaran',
            'kategori'          => 'pakan',
            'nominal'           => $totalBiaya,
            'keterangan'        => "Pembelian {$jumlahBeli} {$stokItem->satuan} {$stokItem->nama_pakan} (" . ($mitra ? $mitra->nama_mitra : 'Supplier') . ")",
            'ref_id'            => 'BELI-PAKAN-' . $pembelian->id_pembelian,
        ]);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => "Pembelian {$jumlahBeli} {$stokItem->satuan} '{$stokItem->nama_pakan}' berhasil dicatat. Saldo stok bertambah dan pengeluaran kas telah otomatis terbukukan!",
                'stok'    => $stokItem->fresh()
            ]);
        }

        return redirect()->route('stok-pakan')->with('success', "Pembelian {$jumlahBeli} {$stokItem->satuan} pakan berhasil dicatat!");
    }
}

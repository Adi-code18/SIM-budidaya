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

            // Ambil transaksi pembelian terakhir untuk mendapatkan supplier terakhir
            $latestPembelian = $item->pembelian ? $item->pembelian->sortByDesc('id_pembelian')->first() : null;
            $latestMitraId = $latestPembelian?->id_mitra;
            $latestMitraNama = $latestPembelian?->mitra?->nama_mitra;

            return [
                'id_stok_pakan'       => $item->id_stok_pakan,
                'kode_pakan'          => 'PKN-' . str_pad($item->id_stok_pakan, 4, '0', STR_PAD_LEFT),
                'nama_pakan'          => $item->nama_pakan,
                'kategori_peruntukan' => $item->kategori_peruntukan,
                'satuan'              => $item->satuan,
                'stok_tersisa'        => $sisaStok,
                'batas_minimum'       => (float) $item->batas_minimum,
                'harga_per_satuan'    => (float) $item->harga_per_satuan,
                'id_mitra'            => $latestMitraId,
                'nama_mitra_terakhir' => $latestMitraNama,
                'keterangan'          => $item->keterangan,
                'burn_rate_harian'    => $burnRateHarian,
                'sisa_hari'           => $sisaHari,
                'status'              => $status,
                'status_label'        => $statusLabel,
                'status_badge'        => $statusBadge,
                'dot_class'           => $dotClass,
            ];
        });

        // 2. Ambil Mitra KHUSUS Supplier Pakan (Eksklusif Supplier Pakan / Pelet)
        $suppliers = MitraDistributor::where(function ($q) {
            $q->where('tipe_mitra', 'like', '%pakan%')
              ->orWhere('tipe_mitra', 'like', '%pelet%')
              ->orWhere(function ($sub) {
                  $sub->where('tipe_mitra', 'like', '%supplier%')
                      ->where('tipe_mitra', 'not like', '%bibit%')
                      ->where('tipe_mitra', 'not like', '%benih%');
              });
        })
        ->where('tipe_mitra', 'not like', '%restoran%')
        ->where('tipe_mitra', 'not like', '%resto%')
        ->where('tipe_mitra', 'not like', '%rumah makan%')
        ->where('tipe_mitra', 'not like', '%pasar%')
        ->where('tipe_mitra', 'not like', '%ekspor%')
        ->orderBy('nama_mitra', 'asc')
        ->get()
        ->map(function ($s) {
            $phone = $s->kontak ?? $s->telepon ?? '+62 812-3456-7890';
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
     * Tambah Master Item Pakan Baru (Otomatis Masuk Keuangan jika ada stok awal & harga)
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
            'id_mitra'            => [
                'nullable',
                'exists:mitra_distributor,id_mitra',
                function ($attribute, $value, $fail) {
                    if ($value) {
                        $m = MitraDistributor::find($value);
                        if ($m) {
                            $raw = strtolower($m->tipe_mitra);
                            if (str_contains($raw, 'resto') || str_contains($raw, 'makan') || str_contains($raw, 'bibit') || str_contains($raw, 'benih') || str_contains($raw, 'pasar') || str_contains($raw, 'ekspor')) {
                                $fail('Mitra yang dipilih harus berstatus Supplier Pakan.');
                            }
                        }
                    }
                }
            ],
            'keterangan'          => 'nullable|string',
        ], [
            'nama_pakan.required'          => 'Nama jenis pakan wajib diisi.',
            'kategori_peruntukan.required' => 'Kategori peruntukan pakan wajib dipilih.',
            'satuan.required'              => 'Satuan pakan wajib diisi.',
            'stok_tersisa.required'        => 'Jumlah stok awal wajib diisi.',
            'batas_minimum.required'       => 'Batas minimum peringatan wajib diisi.',
            'harga_per_satuan.required'    => 'Harga acuan per satuan wajib diisi.',
        ]);

        // Validasi Duplikasi Nama Pakan (Case-Insensitive & Trimmed)
        $cleanNamaPakan = trim($request->nama_pakan ?? '');
        $existingPakan = StokPakan::whereRaw('LOWER(TRIM(nama_pakan)) = ?', [strtolower($cleanNamaPakan)])->first();
        if ($existingPakan) {
            $msg = "Nama jenis pakan '{$existingPakan->nama_pakan}' sudah terdaftar dalam tabel gudang (Stok saat ini: {$existingPakan->stok_tersisa} {$existingPakan->satuan}). Tidak dapat menambahkan nama pakan yang sama. Jika ingin menambah pasokan pakan, silakan gunakan fitur 'Catat Pembelian / Restock Pakan'.";
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => $msg,
                    'is_duplicate' => true,
                    'duplicate_item' => [
                        'id_stok_pakan' => $existingPakan->id_stok_pakan,
                        'nama_pakan'    => $existingPakan->nama_pakan,
                        'stok_tersisa'  => $existingPakan->stok_tersisa,
                        'satuan'        => $existingPakan->satuan
                    ]
                ], 422);
            }
            return redirect()->back()->withErrors(['nama_pakan' => $msg])->withInput();
        }

        $stok = StokPakan::create([
            'nama_pakan'          => $validated['nama_pakan'],
            'kategori_peruntukan' => $validated['kategori_peruntukan'],
            'satuan'              => strtolower($validated['satuan']),
            'stok_tersisa'        => $validated['stok_tersisa'],
            'batas_minimum'       => $validated['batas_minimum'],
            'harga_per_satuan'    => $validated['harga_per_satuan'],
            'keterangan'          => $validated['keterangan'] ?? null,
        ]);

        $stokAwal = (float) $validated['stok_tersisa'];
        $hargaSatuan = (float) $validated['harga_per_satuan'];
        $totalBiaya = $stokAwal * $hargaSatuan;
        $mitra = !empty($validated['id_mitra']) ? MitraDistributor::find($validated['id_mitra']) : null;
        $today = Carbon::now()->toDateString();
        $idUser = Auth::id() ?? 1;

        // 1. Simpan riwayat transaksi Pembelian Pakan jika stok awal > 0
        $pembelian = null;
        if ($stokAwal > 0) {
            $pembelian = PembelianPakan::create([
                'id_user'        => $idUser,
                'id_stok_pakan'  => $stok->id_stok_pakan,
                'id_mitra'       => $mitra ? $mitra->id_mitra : null,
                'nama_pakan'     => $stok->nama_pakan,
                'tgl_beli'       => $today,
                'jumlah'         => $stokAwal,
                'harga_satuan'   => $hargaSatuan,
                'total_biaya'    => $totalBiaya,
                'keterangan'     => "Pengadaan stok awal {$stok->nama_pakan}" . ($mitra ? " dari {$mitra->nama_mitra}" : ""),
            ]);
        }

        // 2. OTOMATIS CATAT PENGELUARAN KE KEUANGAN (BUKU KAS)
        if ($totalBiaya > 0) {
            Keuangan::create([
                'id_user'           => $idUser,
                'id_kolam'          => null,
                'tanggal_transaksi' => $today,
                'tipe_transaksi'    => 'pengeluaran',
                'kategori'          => 'pakan',
                'nominal'           => $totalBiaya,
                'keterangan'        => "Pengadaan stok awal {$stokAwal} {$stok->satuan} {$stok->nama_pakan}" . ($mitra ? " ({$mitra->nama_mitra})" : " (Supplier Pakan)"),
                'ref_id'            => $pembelian ? ('BELI-PAKAN-' . $pembelian->id_pembelian) : ('STOK-AWAL-' . $stok->id_stok_pakan),
            ]);
        }

        $message = "Master item pakan '{$stok->nama_pakan}' berhasil ditambahkan";
        if ($totalBiaya > 0) {
            $message .= " dan pengeluaran awal sebesar Rp " . number_format($totalBiaya, 0, ',', '.') . " telah otomatis dicatat ke Keuangan!";
        } else {
            $message .= "!";
        }

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => $message,
                'item'    => $stok
            ]);
        }

        return redirect()->route('stok-pakan')->with('success', $message);
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

        // Validasi Duplikasi Nama Pakan terhadap item lain (Case-Insensitive & Trimmed)
        $cleanNamaPakan = trim($request->nama_pakan ?? '');
        $existingPakan = StokPakan::where('id_stok_pakan', '!=', $id)
            ->whereRaw('LOWER(TRIM(nama_pakan)) = ?', [strtolower($cleanNamaPakan)])
            ->first();
        if ($existingPakan) {
            $msg = "Nama jenis pakan '{$existingPakan->nama_pakan}' sudah digunakan oleh item pakan lain di gudang (PKN-" . str_pad($existingPakan->id_stok_pakan, 4, '0', STR_PAD_LEFT) . "). Mohon gunakan nama yang berbeda.";
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => $msg,
                    'is_duplicate' => true,
                ], 422);
            }
            return redirect()->back()->withErrors(['nama_pakan' => $msg])->withInput();
        }

        // Validasi dan simpan data update master
        $stok->update([
            'nama_pakan'          => $validated['nama_pakan'],
            'kategori_peruntukan' => $validated['kategori_peruntukan'],
            'satuan'              => strtolower($validated['satuan']),
            'stok_tersisa'        => $validated['stok_tersisa'],
            'batas_minimum'       => $validated['batas_minimum'],
            'harga_per_satuan'    => $validated['harga_per_satuan'],
            'keterangan'          => $validated['keterangan'] ?? null,
        ]);

        // Sinkronisasi mitra supplier terakhir jika dipilih di modal edit
        if ($request->has('id_mitra')) {
            $latestPembelian = $stok->pembelian()->latest('id_pembelian')->first();
            if ($latestPembelian) {
                $latestPembelian->update([
                    'id_mitra' => $request->id_mitra ?: null
                ]);
            }
        }

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

        // 2. OTOMATIS TAMBAHKAN STOK DI GUDANG DENGAN HARGA RATA-RATA TERTIMBANG (MOVING AVERAGE)
        $stokLama = (float) $stokItem->stok_tersisa;
        $hargaLama = (float) $stokItem->harga_per_satuan;
        $stokBaru = $stokLama + $jumlahBeli;

        // Hitung Moving Weighted Average Price (Harga Rata-Rata Tertimbang)
        if ($stokBaru > 0) {
            $totalNilaiLama = max(0, $stokLama) * $hargaLama;
            $totalNilaiBaru = $jumlahBeli * $hargaSatuan;
            $hargaRataRata = round(($totalNilaiLama + $totalNilaiBaru) / $stokBaru);
        } else {
            $hargaRataRata = $hargaSatuan;
        }

        $stokItem->update([
            'stok_tersisa'     => $stokBaru,
            'harga_per_satuan' => $hargaRataRata,
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

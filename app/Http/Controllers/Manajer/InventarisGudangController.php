<?php

namespace App\Http\Controllers\Manajer;

use App\Http\Controllers\Controller;
use App\Models\InventarisGudang;
use App\Models\LogTransaksiGudang;
use App\Models\Kolam;
use App\Models\User;
use App\Models\Keuangan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class InventarisGudangController extends Controller
{
    /**
     * Tampilkan halaman utama Manajemen Inventaris & Gudang
     */
    public function index()
    {
        $items = InventarisGudang::orderBy('kategori_barang', 'asc')
            ->orderBy('nama_barang', 'asc')
            ->get();

        // Hitung KPI Statistik Finansial & Operasional
        $totalNilaiSaprotan = $items->where('kategori_barang', 'saprotan')->sum(fn($i) => $i->stok_tersisa * $i->harga_satuan_terakhir);
        $totalNilaiPeralatan = $items->where('kategori_barang', 'peralatan')->sum(fn($i) => $i->stok_tersisa * $i->harga_satuan_terakhir);
        $totalNilaiAset = $totalNilaiSaprotan + $totalNilaiPeralatan;
        $kritisCount = $items->where('kategori_barang', 'saprotan')->filter(fn($i) => $i->stok_tersisa <= $i->min_stok)->count();
        $alatPerluServisCount = $items->where('kategori_barang', 'peralatan')->whereIn('kondisi_alat', ['perlu_servis', 'rusak'])->count();

        // Riwayat Mutasi Terkini
        $riwayatMutasi = LogTransaksiGudang::with(['barang', 'kolam', 'user'])
            ->latest('tanggal_mutasi')
            ->latest('id_transaksi_gudang')
            ->limit(50)
            ->get();

        // Data Master Kolam & Petugas untuk dropdown pilihan form
        $kolamList = Kolam::orderBy('nama_kolam')->get();
        $petugasList = User::whereIn('role', ['manajer', 'pembesaran', 'pembibitan', 'petugas_distribusi'])
            ->orderBy('nama')
            ->get();

        return view('layouts.gudang.index', compact(
            'items',
            'totalNilaiSaprotan',
            'totalNilaiPeralatan',
            'totalNilaiAset',
            'kritisCount',
            'alatPerluServisCount',
            'riwayatMutasi',
            'kolamList',
            'petugasList'
        ));
    }

    /**
     * Daftarkan item barang baru ke gudang
     */
    public function store(Request $request)
    {
        $request->validate([
            'nama_barang' => 'required|string|max:150',
            'kategori_barang' => 'required|in:saprotan,peralatan',
            'satuan' => 'required|string|max:30',
            'stok_awal' => 'nullable|numeric|min:0',
            'harga_satuan' => 'nullable|numeric|min:0',
            'min_stok' => 'nullable|numeric|min:0',
            'tipe_saprotan' => 'nullable|string',
            'kondisi_alat' => 'nullable|in:normal,perlu_servis,rusak',
            'lokasi_penyimpanan' => 'nullable|string|max:100',
            'catatan' => 'nullable|string'
        ]);

        return DB::transaction(function () use ($request) {
            // Generate Kode Barang Otomatis
            $prefix = $request->kategori_barang === 'saprotan' ? 'SPT' : 'ALT';
            $count = InventarisGudang::where('kategori_barang', $request->kategori_barang)->count() + 1;
            $kodeBarang = $prefix . '-' . str_pad($count, 4, '0', STR_PAD_LEFT);

            // Cek jika kode sudah ada
            while (InventarisGudang::where('kode_barang', $kodeBarang)->exists()) {
                $count++;
                $kodeBarang = $prefix . '-' . str_pad($count, 4, '0', STR_PAD_LEFT);
            }

            $stokAwal = (float) ($request->stok_awal ?? 0);
            $hargaSatuan = (float) ($request->harga_satuan ?? 0);

            $barang = InventarisGudang::create([
                'kode_barang' => $kodeBarang,
                'nama_barang' => $request->nama_barang,
                'kategori_barang' => $request->kategori_barang,
                'tipe_saprotan' => $request->kategori_barang === 'saprotan' ? ($request->tipe_saprotan ?? 'obat_kimia') : null,
                'satuan' => $request->satuan,
                'stok_tersisa' => $stokAwal,
                'min_stok' => (float) ($request->min_stok ?? 5),
                'harga_satuan_terakhir' => $hargaSatuan,
                'kondisi_alat' => $request->kategori_barang === 'peralatan' ? ($request->kondisi_alat ?? 'normal') : null,
                'lokasi_penyimpanan' => $request->lokasi_penyimpanan,
                'catatan' => $request->catatan
            ]);

            // Jika ada stok awal bernilai > 0, catat mutasi masuk dan auto-post ke Keuangan
            if ($stokAwal > 0 && $hargaSatuan > 0) {
                $totalBiaya = $stokAwal * $hargaSatuan;
                $noRef = 'TRX-GDG-IN-' . date('Ymd') . '-' . rand(1000, 9999);

                LogTransaksiGudang::create([
                    'no_referensi' => $noRef,
                    'jenis_mutasi' => 'masuk_pembelian',
                    'id_barang' => $barang->id_barang,
                    'jumlah' => $stokAwal,
                    'harga_satuan' => $hargaSatuan,
                    'total_biaya' => $totalBiaya,
                    'id_user' => Auth::id(),
                    'tanggal_mutasi' => now()->toDateString(),
                    'keterangan' => 'Saldo awal / pengadaan pertama barang ' . $barang->nama_barang
                ]);

                // Auto-post Keuangan
                Keuangan::create([
                    'id_user' => Auth::id() ?? 1,
                    'id_kolam' => null,
                    'tanggal_transaksi' => now()->toDateString(),
                    'tipe_transaksi' => 'pengeluaran',
                    'kategori' => $barang->kategori_barang === 'saprotan' ? 'Pengadaan Saprotan' : 'Peralatan Tambak',
                    'nominal' => $totalBiaya,
                    'keterangan' => 'Pengadaan Awal: ' . $barang->nama_barang . ' (' . $stokAwal . ' ' . $barang->satuan . ')',
                    'ref_id' => $noRef
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Item ' . $barang->nama_barang . ' (' . $barang->kode_barang . ') berhasil didaftarkan!',
                'barang' => $barang
            ]);
        });
    }

    /**
     * Perbarui data spesifikasi barang
     */
    public function update(Request $request, $id)
    {
        $barang = InventarisGudang::findOrFail($id);

        $request->validate([
            'nama_barang' => 'required|string|max:150',
            'satuan' => 'required|string|max:30',
            'min_stok' => 'nullable|numeric|min:0',
            'tipe_saprotan' => 'nullable|string',
            'kondisi_alat' => 'nullable|in:normal,perlu_servis,rusak',
            'lokasi_penyimpanan' => 'nullable|string|max:100',
            'catatan' => 'nullable|string'
        ]);

        $barang->update([
            'nama_barang' => $request->nama_barang,
            'tipe_saprotan' => $barang->kategori_barang === 'saprotan' ? ($request->tipe_saprotan ?? $barang->tipe_saprotan) : null,
            'satuan' => $request->satuan,
            'min_stok' => (float) ($request->min_stok ?? $barang->min_stok),
            'kondisi_alat' => $barang->kategori_barang === 'peralatan' ? ($request->kondisi_alat ?? $barang->kondisi_alat) : null,
            'lokasi_penyimpanan' => $request->lokasi_penyimpanan,
            'catatan' => $request->catatan
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Data barang ' . $barang->nama_barang . ' berhasil diperbarui!',
            'barang' => $barang
        ]);
    }

    /**
     * Hapus barang dari gudang
     */
    public function destroy($id)
    {
        $barang = InventarisGudang::findOrFail($id);
        $nama = $barang->nama_barang;

        $barang->delete();

        return response()->json([
            'success' => true,
            'message' => 'Barang ' . $nama . ' berhasil dihapus dari inventaris gudang.'
        ]);
    }

    /**
     * Catat Barang Masuk / Restock Pembelian (Auto-post ke Keuangan)
     */
    public function masuk(Request $request)
    {
        $request->validate([
            'id_barang' => 'required|exists:inventaris_gudang,id_barang',
            'jumlah' => 'required|numeric|min:0.01',
            'harga_satuan' => 'required|numeric|min:0',
            'tanggal_mutasi' => 'required|date',
            'keterangan' => 'nullable|string|max:255'
        ]);

        return DB::transaction(function () use ($request) {
            $barang = InventarisGudang::lockForUpdate()->findOrFail($request->id_barang);
            $jumlah = (float) $request->jumlah;
            $hargaSatuan = (float) $request->harga_satuan;
            $totalBiaya = $jumlah * $hargaSatuan;

            // Tambah stok & update harga satuan terakhir
            $barang->stok_tersisa += $jumlah;
            $barang->harga_satuan_terakhir = $hargaSatuan;
            $barang->save();

            $noRef = 'TRX-GDG-IN-' . date('Ymd') . '-' . rand(1000, 9999);

            $log = LogTransaksiGudang::create([
                'no_referensi' => $noRef,
                'jenis_mutasi' => 'masuk_pembelian',
                'id_barang' => $barang->id_barang,
                'jumlah' => $jumlah,
                'harga_satuan' => $hargaSatuan,
                'total_biaya' => $totalBiaya,
                'id_user' => Auth::id(),
                'tanggal_mutasi' => $request->tanggal_mutasi,
                'keterangan' => $request->keterangan ?: ('Restock pembelian ' . $barang->nama_barang)
            ]);

            // Auto-post Pengeluaran Kas Keuangan
            Keuangan::create([
                'id_user' => Auth::id() ?? 1,
                'id_kolam' => null,
                'tanggal_transaksi' => $request->tanggal_mutasi,
                'tipe_transaksi' => 'pengeluaran',
                'kategori' => $barang->kategori_barang === 'saprotan' ? 'Pengadaan Saprotan' : 'Peralatan Tambak',
                'nominal' => $totalBiaya,
                'keterangan' => 'Restock Gudang: ' . $barang->nama_barang . ' (' . $jumlah . ' ' . $barang->satuan . ' @ Rp ' . number_format($hargaSatuan, 0, ',', '.') . ')',
                'ref_id' => $noRef
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Restock ' . $barang->nama_barang . ' (' . $jumlah . ' ' . $barang->satuan . ') berhasil dicatat & dibukukan ke Keuangan!',
                'barang' => $barang,
                'log' => $log
            ]);
        });
    }

    /**
     * Catat Pemakaian Barang ke Kolam (Barang Keluar)
     */
    public function keluar(Request $request)
    {
        $request->validate([
            'id_barang' => 'required|exists:inventaris_gudang,id_barang',
            'jumlah' => 'required|numeric|min:0.01',
            'id_kolam' => 'nullable|exists:kolam,id_kolam',
            'tanggal_mutasi' => 'required|date',
            'keterangan' => 'nullable|string|max:255'
        ]);

        return DB::transaction(function () use ($request) {
            $barang = InventarisGudang::lockForUpdate()->findOrFail($request->id_barang);
            $jumlah = (float) $request->jumlah;

            // Validasi sisa stok
            if ($barang->stok_tersisa < $jumlah) {
                return response()->json([
                    'success' => false,
                    'message' => 'Stok tidak mencukupi! Sisa stok ' . $barang->nama_barang . ' saat ini hanya ' . $barang->stok_tersisa . ' ' . $barang->satuan . '.'
                ], 422);
            }

            // Kurangi stok
            $barang->stok_tersisa -= $jumlah;
            $barang->save();

            $hargaSatuan = $barang->harga_satuan_terakhir;
            $totalBiaya = $jumlah * $hargaSatuan;

            $kolamNama = null;
            if ($request->id_kolam) {
                $kolamObj = Kolam::find($request->id_kolam);
                $kolamNama = $kolamObj ? $kolamObj->nama_kolam : null;
            }

            $noRef = 'TRX-GDG-OUT-' . date('Ymd') . '-' . rand(1000, 9999);

            $log = LogTransaksiGudang::create([
                'no_referensi' => $noRef,
                'jenis_mutasi' => 'keluar_pemakaian_kolam',
                'id_barang' => $barang->id_barang,
                'jumlah' => $jumlah,
                'harga_satuan' => $hargaSatuan,
                'total_biaya' => $totalBiaya,
                'id_kolam' => $request->id_kolam,
                'id_user' => Auth::id(),
                'tanggal_mutasi' => $request->tanggal_mutasi,
                'keterangan' => $request->keterangan ?: ('Pemakaian ' . $barang->nama_barang . ($kolamNama ? (' untuk ' . $kolamNama) : ''))
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Pemakaian ' . $jumlah . ' ' . $barang->satuan . ' ' . $barang->nama_barang . ($kolamNama ? (' untuk ' . $kolamNama) : '') . ' berhasil dicatat!',
                'barang' => $barang,
                'log' => $log
            ]);
        });
    }
}

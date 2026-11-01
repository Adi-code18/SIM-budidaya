<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Keuangan;
use App\Models\Kolam;
use App\Models\User;
use App\Models\PembelianPakan;
use App\Models\BatchPembesaran;
use App\Models\TransaksiDistribusi;
use App\Models\LogTransaksiGudang;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class KeuanganSeeder extends Seeder
{
    /**
     * Seeder Ledger Keuangan Terintegrasi AquaFarm.
     * Mengacu langsung pada BRD REQ-FIN-01 s/d 04, BR-11 (Integrasi Keuangan), dan UAC-12.
     * Mengotomatisasi rekonsiliasi:
     * 1. Pemasukan Penjualan Panen Distribusi (Status: selesai) -> ref_id: ORD-xxxx
     * 2. Pengeluaran Pembelian Pakan dari Supplier -> ref_id: BELI-PAKAN-xxxx
     * 3. Pengeluaran Pembelian Bibit Luar -> ref_id: BELI-BIBIT-PB-xxxx
     * 4. Pengeluaran Saprotan & Log Gudang -> ref_id: GUDANG-LOG-xxxx
     * 5. Pengeluaran Investasi Fasilitas Kolam -> ref_id: KOLAM-INV-xxxx
     */
    public function run(): void
    {
        $manajer = User::where('role', 'manajer')->first() ?? User::first();
        $userId = $manajer ? $manajer->id_user : 1;

        $kolamPembesaran = Kolam::where('nama_kolam', 'like', '%Pembesaran%')->first();
        $idKolamDefault = $kolamPembesaran ? $kolamPembesaran->id_kolam : null;

        // 1. REKONSILIASI PEMASUKAN: Transaksi Distribusi yang Selesai / Lunas (REQ-FIN-04 & BR-11)
        $completedDistribusi = TransaksiDistribusi::where('status_order', 'selesai')->with(['mitra', 'batchPembesaran'])->get();
        foreach ($completedDistribusi as $trx) {
            $orderRef = 'ORD-' . str_pad($trx->id_transaksi, 4, '0', STR_PAD_LEFT);
            $namaMitra = $trx->mitra ? $trx->mitra->nama_mitra : 'Mitra Distribusi';
            $species = $trx->batchPembesaran ? $trx->batchPembesaran->jenis_ikan : ($trx->Jenis_order ?? 'Ikan Air Tawar');
            $idKolamTrx = $trx->batchPembesaran ? $trx->batchPembesaran->id_kolam : $idKolamDefault;

            Keuangan::create([
                'id_user'           => $userId,
                'id_kolam'          => $idKolamTrx,
                'tanggal_transaksi' => $trx->tanggal_order,
                'tipe_transaksi'    => 'pemasukan',
                'kategori'          => 'Penjualan Panen ' . $species,
                'nominal'           => (float) $trx->harga_total,
                'keterangan'        => "Pelunasan penjualan panen ({$trx->Total_kg} kg) ke {$namaMitra} [No Surat Jalan: {$trx->no_surat_jalan}]",
                'ref_id'            => $orderRef,
            ]);
        }

        // 2. REKONSILIASI PENGELUARAN: Pembelian Restock Pakan Gudang (REQ-FIN-02, REQ-INV-01 & BR-11)
        $pembelianPakanList = PembelianPakan::with(['stokPakan', 'mitra'])->get();
        foreach ($pembelianPakanList as $pb) {
            $namaPakan = $pb->stokPakan ? $pb->stokPakan->nama_pakan : 'Pakan';
            $namaSupplier = $pb->mitra ? $pb->mitra->nama_mitra : 'CV Sentosa Pakan Mandiri';
            $refId = 'BELI-PAKAN-' . $pb->id_pembelian;

            Keuangan::create([
                'id_user'           => $userId,
                'id_kolam'          => $idKolamDefault,
                'tanggal_transaksi' => $pb->tgl_beli,
                'tipe_transaksi'    => 'pengeluaran',
                'kategori'          => 'Pembelian Pakan',
                'nominal'           => (float) $pb->total_biaya,
                'keterangan'        => "Pembelian {$namaPakan} ({$pb->jumlah} kg @ Rp " . number_format($pb->harga_satuan, 0, ',', '.') . ") dari {$namaSupplier} [Nota: {$pb->no_nota}]",
                'ref_id'            => $refId,
            ]);
        }

        // 3. REKONSILIASI PENGELUARAN: Pembelian Bibit Luar pada Batch Pembesaran (REQ-FIN-03 & BR-11)
        $batchBeliLuar = BatchPembesaran::where('asal_bibit', 'beli_luar')->where('biaya_beli_bibit', '>', 0)->get();
        foreach ($batchBeliLuar as $b) {
            $pbRef = 'BELI-BIBIT-PB-' . str_pad($b->id_pembesaran, 4, '0', STR_PAD_LEFT);
            $namaKolam = $b->kolam ? $b->kolam->nama_kolam : 'Kolam Pembesaran';

            Keuangan::create([
                'id_user'           => $userId,
                'id_kolam'          => $b->id_kolam,
                'tanggal_transaksi' => $b->tgl_tebar,
                'tipe_transaksi'    => 'pengeluaran',
                'kategori'          => 'Pembelian Bibit ' . $b->jenis_ikan,
                'nominal'           => (float) $b->biaya_beli_bibit,
                'keterangan'        => "Pengadaan benih unggul {$b->jenis_ikan} ({$b->jumlah_tebar_ekor} ekor) untuk tebar di {$namaKolam} (#PB-" . str_pad($b->id_pembesaran, 4, '0', STR_PAD_LEFT) . ")",
                'ref_id'            => $pbRef,
            ]);
        }

        // 4. REKONSILIASI PENGELUARAN: Saprotan / Log Transaksi Gudang (Kapur Dolomit & Garam Krosok)
        if (Schema::hasTable('log_transaksi_gudang')) {
            $logGudangMasuk = LogTransaksiGudang::where('jenis_mutasi', 'masuk_pembelian')->with('barang')->take(2)->get();
            foreach ($logGudangMasuk as $lg) {
                $namaBarang = $lg->barang ? $lg->barang->nama_barang : 'Saprotan';
                Keuangan::create([
                    'id_user'           => $userId,
                    'id_kolam'          => $lg->id_kolam ?? $idKolamDefault,
                    'tanggal_transaksi' => $lg->tanggal_mutasi,
                    'tipe_transaksi'    => 'pengeluaran',
                    'kategori'          => 'Sarana Produksi Kolam (Saprotan)',
                    'nominal'           => (float) $lg->total_biaya,
                    'keterangan'        => "Pengadaan logistik gudang: {$namaBarang} ({$lg->jumlah} unit) [Ref: {$lg->no_referensi}]",
                    'ref_id'            => 'GUDANG-LOG-' . str_pad($lg->id_transaksi_gudang, 4, '0', STR_PAD_LEFT),
                ]);
            }
        }

        // 5. INVESTASI ASAL: Biaya Pembangunan Fasilitas Kolam Budidaya AquaFarm
        $kolamWithCost = Kolam::where('biaya_pembuatan', '>', 0)->get();
        foreach ($kolamWithCost as $k) {
            Keuangan::create([
                'id_user'           => $userId,
                'id_kolam'          => $k->id_kolam,
                'tanggal_transaksi' => now()->subDays(60)->toDateString(),
                'tipe_transaksi'    => 'pengeluaran',
                'kategori'          => 'Pembangunan Fasilitas Kolam',
                'nominal'           => (float) $k->biaya_pembuatan,
                'keterangan'        => "Biaya pembuatan/konstruksi kolam {$k->nama_kolam} ({$k->tipe_kolam}) kapasitas {$k->kapasitas} ekor",
                'ref_id'            => 'KOLAM-INV-' . str_pad($k->id_kolam, 4, '0', STR_PAD_LEFT),
            ]);
        }
    }
}

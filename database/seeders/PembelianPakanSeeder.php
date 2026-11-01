<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PembelianPakan;
use App\Models\StokPakan;
use App\Models\MitraDistributor;
use App\Models\User;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class PembelianPakanSeeder extends Seeder
{
    /**
     * Seeder Pembelian Restock Pakan Gudang.
     * Mengacu pada BRD REQ-INV-01 (Pencatatan Pembelian Pakan dari Supplier)
     * dan BRD REQ-FIN-02 (Pencatatan Pengeluaran Pakan ke Modul Keuangan).
     */
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();
        if (Schema::hasTable('pembelian_pakan')) {
            DB::table('pembelian_pakan')->truncate();
        }
        Schema::enableForeignKeyConstraints();

        $manajer = User::where('role', 'manajer')->first() ?? User::first();
        $userId = $manajer ? $manajer->id_user : 1;

        $supplierPakan = MitraDistributor::where('tipe_mitra', 'like', '%Supplier Pakan%')->first()
            ?? MitraDistributor::first();

        $pakanGrower = StokPakan::where('nama_pakan', 'like', '%Grower%')->first();
        $pakanStarter = StokPakan::where('nama_pakan', 'like', '%Starter%')->first();
        $pakanCacing = StokPakan::where('nama_pakan', 'like', '%Cacing%')->first();
        $pakanProbiotik = StokPakan::where('nama_pakan', 'like', '%Probiotik%')->first();

        $purchases = [];

        if ($pakanGrower) {
            $jumlah = 500.00;
            $harga = (float) $pakanGrower->harga_per_satuan;
            $purchases[] = [
                'id_stok_pakan' => $pakanGrower->id_stok_pakan,
                'id_mitra'      => $supplierPakan ? $supplierPakan->id_mitra : null,
                'id_user'       => $userId,
                'tgl_beli'      => now()->subDays(10)->toDateString(),
                'jumlah'        => $jumlah,
                'harga_satuan'  => $harga,
                'total_biaya'   => $jumlah * $harga,
                'no_nota'       => 'NOTA-PKN-2026-001',
                'catatan'       => 'Restock 10 sak (500 kg) Pelet Apung Grower 781-2 untuk pembesaran siklus berjalan.',
            ];
        }

        if ($pakanStarter) {
            $jumlah = 200.00;
            $harga = (float) $pakanStarter->harga_per_satuan;
            $purchases[] = [
                'id_stok_pakan' => $pakanStarter->id_stok_pakan,
                'id_mitra'      => $supplierPakan ? $supplierPakan->id_mitra : null,
                'id_user'       => $userId,
                'tgl_beli'      => now()->subDays(15)->toDateString(),
                'jumlah'        => $jumlah,
                'harga_satuan'  => $harga,
                'total_biaya'   => $jumlah * $harga,
                'no_nota'       => 'NOTA-PKN-2026-002',
                'catatan'       => 'Pengadaan pakan starter butiran halus untuk bibit baru tebar di kolam pembesaran.',
            ];
        }

        if ($pakanCacing) {
            $jumlah = 25.00;
            $harga = (float) $pakanCacing->harga_per_satuan;
            $purchases[] = [
                'id_stok_pakan' => $pakanCacing->id_stok_pakan,
                'id_mitra'      => $supplierPakan ? $supplierPakan->id_mitra : null,
                'id_user'       => $userId,
                'tgl_beli'      => now()->subDays(4)->toDateString(),
                'jumlah'        => $jumlah,
                'harga_satuan'  => $harga,
                'total_biaya'   => $jumlah * $harga,
                'no_nota'       => 'NOTA-PKN-2026-003',
                'catatan'       => 'Pasokan cacing sutra segar steril untuk pakan burayak pendederan kolam pembenihan.',
            ];
        }

        if ($pakanProbiotik) {
            $jumlah = 20.00;
            $harga = (float) $pakanProbiotik->harga_per_satuan;
            $purchases[] = [
                'id_stok_pakan' => $pakanProbiotik->id_stok_pakan,
                'id_mitra'      => $supplierPakan ? $supplierPakan->id_mitra : null,
                'id_user'       => $userId,
                'tgl_beli'      => now()->subDays(20)->toDateString(),
                'jumlah'        => $jumlah,
                'harga_satuan'  => $harga,
                'total_biaya'   => $jumlah * $harga,
                'no_nota'       => 'NOTA-PKN-2026-004',
                'catatan'       => 'Pembelian 20 botol Probiotik EM4 Perikanan untuk fermentasi pakan dan bioremediasi kolam.',
            ];
        }

        foreach ($purchases as $p) {
            PembelianPakan::create($p);
        }
    }
}

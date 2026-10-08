<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Keuangan;
use App\Models\Kolam;
use App\Models\User;

class KeuanganSeeder extends Seeder
{
    public function run(): void
    {
        $manajer = User::where('role', 'manajer')->first() ?? User::first();
        $userId = $manajer->id_user;

        $kolamA1 = Kolam::where('nama_kolam', 'Kolam Pembesaran A1')->first();
        $kolamA2 = Kolam::where('nama_kolam', 'Kolam Pembesaran A2')->first();
        $kolamA3 = Kolam::where('nama_kolam', 'Kolam Pembesaran A3')->first();
        $kolamC1 = Kolam::where('nama_kolam', 'like', '%Pendederan%')->first();

        $transaksiKeuangan = [
            // 1. Pemasukan Penjualan Panen Selesai (Batch Lele Kolam A1 - 950 kg)
            [
                'id_user'           => $userId,
                'id_kolam'          => $kolamA1 ? $kolamA1->id_kolam : null,
                'tanggal_transaksi' => now()->subDays(4)->toDateString(),
                'tipe_transaksi'    => 'pemasukan',
                'kategori'          => 'Penjualan Panen Ikan Lele',
                'nominal'           => 23750000.00,
                'keterangan'        => 'Pelunasan penjualan panen total lele ke Pasar Cikurubuk (950 kg @ Rp 25.000)',
                'ref_id'            => 'TRX-IN-001',
            ],
            // 2. Pemasukan Uang Muka Pesanan Ikan Nila (Pasar Cikurubuk - 250 kg)
            [
                'id_user'           => $userId,
                'id_kolam'          => $kolamA2 ? $kolamA2->id_kolam : null,
                'tanggal_transaksi' => now()->toDateString(),
                'tipe_transaksi'    => 'pemasukan',
                'kategori'          => 'Uang Muka Pesanan Nila',
                'nominal'           => 4375000.00,
                'keterangan'        => 'Uang muka (50%) pesanan ikan nila segar 250 kg dalam masa karantina pemberokan',
                'ref_id'            => 'TRX-IN-002',
            ],
            // 3. Pengeluaran Pengadaan Pakan Pelet Komersial
            [
                'id_user'           => $userId,
                'id_kolam'          => $kolamA1 ? $kolamA1->id_kolam : null,
                'tanggal_transaksi' => now()->subDays(8)->toDateString(),
                'tipe_transaksi'    => 'pengeluaran',
                'kategori'          => 'Pembelian Pakan Pelet',
                'nominal'           => 3125000.00,
                'keterangan'        => 'Pembelian pelet apung grower 781-2 (250 kg) dari CV Sentosa Pakan Mandiri',
                'ref_id'            => 'TRX-OUT-001',
            ],
            // 4. Pengeluaran Pembelian Bibit Luar (Gurami Batch A3)
            [
                'id_user'           => $userId,
                'id_kolam'          => $kolamA3 ? $kolamA3->id_kolam : null,
                'tanggal_transaksi' => now()->subDays(15)->toDateString(),
                'tipe_transaksi'    => 'pengeluaran',
                'kategori'          => 'Pembelian Bibit Ikan',
                'nominal'           => 1500000.00,
                'keterangan'        => 'Pengadaan bibit gurami unggul (Balai Pembenihan Unggul Mina Raya)',
                'ref_id'            => 'TRX-OUT-002',
            ],
            // 5. Pengeluaran Pakan Alami Pembibitan (Cacing Sutra)
            [
                'id_user'           => $userId,
                'id_kolam'          => $kolamC1 ? $kolamC1->id_kolam : null,
                'tanggal_transaksi' => now()->subDays(5)->toDateString(),
                'tipe_transaksi'    => 'pengeluaran',
                'kategori'          => 'Pakan Alami Pembibitan',
                'nominal'           => 700000.00,
                'keterangan'        => 'Pembelian 35 kg cacing sutra segar untuk fase larva & fingerling',
                'ref_id'            => 'TRX-OUT-003',
            ],
            // 6. Pengeluaran Operasional & Listrik Aerator Kolam
            [
                'id_user'           => $userId,
                'id_kolam'          => null,
                'tanggal_transaksi' => now()->subDays(2)->toDateString(),
                'tipe_transaksi'    => 'pengeluaran',
                'kategori'          => 'Operasional Kolam',
                'nominal'           => 650000.00,
                'keterangan'        => 'Biaya listrik aerasi continuous & perawatan filter air kolam',
                'ref_id'            => 'TRX-OUT-004',
            ],
        ];

        foreach ($transaksiKeuangan as $keu) {
            Keuangan::updateOrCreate(
                ['ref_id' => $keu['ref_id']],
                $keu
            );
        }
    }
}

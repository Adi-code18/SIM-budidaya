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

        $kolamA1 = Kolam::where('nama_kolam', 'Kolam A1')->first();
        $kolamB3 = Kolam::where('nama_kolam', 'Kolam B3')->first();
        $kolamC2 = Kolam::where('nama_kolam', 'Kolam C2')->first();
        $kolamB1 = Kolam::where('nama_kolam', 'Kolam B1')->first();

        $transaksiKeuangan = [
            // Pemasukan
            [
                'id_user' => $userId,
                'id_kolam' => $kolamB1 ? $kolamB1->id_kolam : null,
                'tanggal_transaksi' => now()->subDays(5)->toDateString(),
                'tipe_transaksi' => 'pemasukan',
                'kategori' => 'Penjualan Panen Ikan Nila',
                'nominal' => 14000000.00,
                'keterangan' => 'Penjualan panen ikan nila merah ke RM Padang Berkah Utama (400 kg)',
                'ref_id' => 'TRX-IN-001',
            ],
            [
                'id_user' => $userId,
                'id_kolam' => $kolamC2 ? $kolamC2->id_kolam : null,
                'tanggal_transaksi' => now()->subDays(3)->toDateString(),
                'tipe_transaksi' => 'pemasukan',
                'kategori' => 'Penjualan Ekspor Ikan Patin',
                'nominal' => 32000000.00,
                'keterangan' => 'Penjualan ikan patin fillet ke PT Bahari Ekspor Indo (1000 kg)',
                'ref_id' => 'TRX-IN-002',
            ],
            [
                'id_user' => $userId,
                'id_kolam' => null,
                'tanggal_transaksi' => now()->subDays(1)->toDateString(),
                'tipe_transaksi' => 'pemasukan',
                'kategori' => 'Penjualan Benih Bibit Ikan',
                'nominal' => 8500000.00,
                'keterangan' => 'Penjualan bibit nila & gurami ke mitra pembudidaya lokal',
                'ref_id' => 'TRX-IN-003',
            ],

            // Pengeluaran
            [
                'id_user' => $userId,
                'id_kolam' => $kolamA1 ? $kolamA1->id_kolam : null,
                'tanggal_transaksi' => now()->subDays(12)->toDateString(),
                'tipe_transaksi' => 'pengeluaran',
                'kategori' => 'Pembelian Pakan Pelet',
                'nominal' => 7500000.00,
                'keterangan' => 'Pembelian pelet komersial protein tinggi 500 kg',
                'ref_id' => 'TRX-OUT-001',
            ],
            [
                'id_user' => $userId,
                'id_kolam' => $kolamB3 ? $kolamB3->id_kolam : null,
                'tanggal_transaksi' => now()->subDays(8)->toDateString(),
                'tipe_transaksi' => 'pengeluaran',
                'kategori' => 'Pembelian Obat & Probiotik',
                'nominal' => 1250000.00,
                'keterangan' => 'Vitamin imun booster dan probiotik pengurai air kolam',
                'ref_id' => 'TRX-OUT-002',
            ],
            [
                'id_user' => $userId,
                'id_kolam' => null,
                'tanggal_transaksi' => now()->subDays(7)->toDateString(),
                'tipe_transaksi' => 'pengeluaran',
                'kategori' => 'Biaya Listrik & Operasional Aerator',
                'nominal' => 2800000.00,
                'keterangan' => 'Tagihan listrik operasional aerator & pompa sirkulasi',
                'ref_id' => 'TRX-OUT-003',
            ],
            [
                'id_user' => $userId,
                'id_kolam' => null,
                'tanggal_transaksi' => now()->subDays(2)->toDateString(),
                'tipe_transaksi' => 'pengeluaran',
                'kategori' => 'Gaji & Honor Petugas',
                'nominal' => 15000000.00,
                'keterangan' => 'Gaji bulanan teknisi pembibitan, pembesaran & kurir distribusi',
                'ref_id' => 'TRX-OUT-004',
            ],
        ];

        foreach ($transaksiKeuangan as $keu) {
            Keuangan::create($keu);
        }
    }
}

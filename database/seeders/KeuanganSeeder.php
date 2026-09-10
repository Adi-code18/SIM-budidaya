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

        $kolamPembesaran = Kolam::where('nama_kolam', 'like', '%Pembesaran%')->first();

        $transaksiKeuangan = [
            // 1. Kondisi Arus Kas Pemasukan
            [
                'id_user' => $userId,
                'id_kolam' => $kolamPembesaran ? $kolamPembesaran->id_kolam : null,
                'tanggal_transaksi' => now()->subDays(3)->toDateString(),
                'tipe_transaksi' => 'pemasukan',
                'kategori' => 'Penjualan Panen Ikan Nila',
                'nominal' => 10500000.00,
                'keterangan' => 'Penjualan panen ikan nila merah ke Pasar Cikurubuk (300 kg)',
                'ref_id' => 'TRX-IN-001',
            ],
            // 2. Kondisi Arus Kas Pengeluaran
            [
                'id_user' => $userId,
                'id_kolam' => $kolamPembesaran ? $kolamPembesaran->id_kolam : null,
                'tanggal_transaksi' => now()->subDays(7)->toDateString(),
                'tipe_transaksi' => 'pengeluaran',
                'kategori' => 'Pembelian Pakan Pelet',
                'nominal' => 3125000.00,
                'keterangan' => 'Pembelian pelet apung komersial grower 250 kg',
                'ref_id' => 'TRX-OUT-001',
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

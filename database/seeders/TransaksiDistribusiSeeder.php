<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\TransaksiDistribusi;
use App\Models\MitraDistributor;
use App\Models\BatchPembesaran;
use App\Models\User;

class TransaksiDistribusiSeeder extends Seeder
{
    public function run(): void
    {
        $petugasDistribusi = User::where('role', 'petugas_distribusi')->first() ?? User::first();
        $userId = $petugasDistribusi->id_user;

        $restoQu = MitraDistributor::where('nama_mitra', 'like', '%Resto Resto Qu%')->first() ?? MitraDistributor::first();
        $pasarModern = MitraDistributor::where('nama_mitra', 'like', '%Pasar Modern%')->first() ?? MitraDistributor::first();
        $warung88 = MitraDistributor::where('nama_mitra', 'like', '%Warung Seafood 88%')->first() ?? MitraDistributor::first();
        $eksporBahari = MitraDistributor::where('nama_mitra', 'like', '%PT Bahari%')->first() ?? MitraDistributor::first();
        $rmBerkah = MitraDistributor::where('nama_mitra', 'like', '%Berkah Utama%')->first() ?? MitraDistributor::first();

        $batches = BatchPembesaran::all();
        $batch1 = $batches->get(0) ?? BatchPembesaran::first();
        $batch2 = $batches->get(1) ?? $batch1;
        $batch3 = $batches->get(2) ?? $batch1;
        $batch4 = $batches->get(3) ?? $batch1;
        $batch5 = $batches->get(4) ?? $batch1;

        $transaksiList = [
            [
                'id_user' => $userId,
                'id_mitra' => $restoQu->id_mitra,
                'id_pembesaran' => $batch1->id_pembesaran,
                'tanggal_order' => now()->toDateString(),
                'Total_kg' => 350.00,
                'harga_total' => 12250000.00,
                'status_order' => 'dalam_pengiriman',
                'Jenis_order' => 'Ikan Nila Segar Hidup',
                'Bukti_sampai' => null,
            ],
            [
                'id_user' => $userId,
                'id_mitra' => $pasarModern->id_mitra,
                'id_pembesaran' => $batch2->id_pembesaran,
                'tanggal_order' => now()->toDateString(),
                'Total_kg' => 500.00,
                'harga_total' => 25000000.00,
                'status_order' => 'siap_kirim',
                'Jenis_order' => 'Ikan Gurami Padang',
                'Bukti_sampai' => null,
            ],
            [
                'id_user' => $userId,
                'id_mitra' => $warung88->id_mitra,
                'id_pembesaran' => $batch3->id_pembesaran,
                'tanggal_order' => now()->toDateString(),
                'Total_kg' => 200.00,
                'harga_total' => 5000000.00,
                'status_order' => 'siap_kirim',
                'Jenis_order' => 'Ikan Lele Sangkuriang',
                'Bukti_sampai' => null,
            ],
            [
                'id_user' => $userId,
                'id_mitra' => $eksporBahari->id_mitra,
                'id_pembesaran' => $batch4->id_pembesaran,
                'tanggal_order' => now()->subDays(3)->toDateString(),
                'Total_kg' => 1000.00,
                'harga_total' => 32000000.00,
                'status_order' => 'selesai',
                'Jenis_order' => 'Ikan Patin Fillet Ekspor',
                'Bukti_sampai' => 'https://images.unsplash.com/photo-1544551763-46a013bb70d5?w=500&auto=format&fit=crop&q=80',
            ],
            [
                'id_user' => $userId,
                'id_mitra' => $rmBerkah->id_mitra,
                'id_pembesaran' => $batch5->id_pembesaran,
                'tanggal_order' => now()->subDays(5)->toDateString(),
                'Total_kg' => 400.00,
                'harga_total' => 14000000.00,
                'status_order' => 'selesai',
                'Jenis_order' => 'Ikan Nila Merah',
                'Bukti_sampai' => 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?w=500&auto=format&fit=crop&q=80',
            ],
        ];

        foreach ($transaksiList as $transaksi) {
            TransaksiDistribusi::create($transaksi);
        }
    }
}

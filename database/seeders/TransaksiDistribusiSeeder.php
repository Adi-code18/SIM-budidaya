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

        $mitraResto = MitraDistributor::where('tipe_mitra', 'like', '%Restoran%')->first() ?? MitraDistributor::first();
        $mitraPasar = MitraDistributor::where('tipe_mitra', 'like', '%Pasar%')->first() ?? MitraDistributor::first();
        $mitraEkspor = MitraDistributor::where('tipe_mitra', 'like', '%Eksportir%')->first() ?? MitraDistributor::first();

        $batchAktif = BatchPembesaran::where('status_siklus', 'berjalan')->first() ?? BatchPembesaran::first();
        $batchPanen = BatchPembesaran::where('status_siklus', 'selesai')->first() ?? $batchAktif;

        $transaksiList = [
            // 1. Kondisi Status: 'pending' (Order baru masuk menunggu konfirmasi)
            [
                'id_user' => $userId,
                'id_mitra' => $mitraResto->id_mitra,
                'id_pembesaran' => $batchAktif->id_pembesaran,
                'tanggal_order' => now()->toDateString(),
                'Total_kg' => 100.00,
                'harga_total' => 3500000.00,
                'status_order' => 'pending',
                'Jenis_order' => 'Ikan Nila Segar Hidup',
                'Bukti_sampai' => null,
            ],
            // 2. Kondisi Status: 'pemberokian' (Ikan sedang dikarantina / dipuasakan)
            [
                'id_user' => $userId,
                'id_mitra' => $mitraPasar->id_mitra,
                'id_pembesaran' => $batchAktif->id_pembesaran,
                'tanggal_order' => now()->toDateString(),
                'Total_kg' => 200.00,
                'harga_total' => 7000000.00,
                'status_order' => 'pemberokian',
                'Jenis_order' => 'Ikan Nila Super',
                'Bukti_sampai' => null,
            ],
            // 3. Kondisi Status: 'siap_kirim' (Ikan telah dipacking & siap diangkut)
            [
                'id_user' => $userId,
                'id_mitra' => $mitraResto->id_mitra,
                'id_pembesaran' => $batchAktif->id_pembesaran,
                'tanggal_order' => now()->toDateString(),
                'Total_kg' => 150.00,
                'harga_total' => 5250000.00,
                'status_order' => 'siap_kirim',
                'Jenis_order' => 'Ikan Gurami Segar',
                'Bukti_sampai' => null,
            ],
            // 4. Kondisi Status: 'dalam_pengiriman' (Kurir sedang dalam perjalanan menuju mitra)
            [
                'id_user' => $userId,
                'id_mitra' => $mitraEkspor->id_mitra,
                'id_pembesaran' => $batchPanen->id_pembesaran,
                'tanggal_order' => now()->toDateString(),
                'Total_kg' => 500.00,
                'harga_total' => 17500000.00,
                'status_order' => 'dalam_pengiriman',
                'Jenis_order' => 'Ikan Gurami Padang Fillet',
                'Bukti_sampai' => null,
            ],
            // 5. Kondisi Status: 'selesai' (Order selesai diterima dengan bukti serah terima)
            [
                'id_user' => $userId,
                'id_mitra' => $mitraPasar->id_mitra,
                'id_pembesaran' => $batchPanen->id_pembesaran,
                'tanggal_order' => now()->subDays(3)->toDateString(),
                'Total_kg' => 300.00,
                'harga_total' => 10500000.00,
                'status_order' => 'selesai',
                'Jenis_order' => 'Ikan Nila Merah Segar',
                'Bukti_sampai' => 'https://images.unsplash.com/photo-1544551763-46a013bb70d5?w=500&auto=format&fit=crop&q=80',
            ],
            // 6. Kondisi Status: 'dibatalkan' (Order dibatalkan)
            [
                'id_user' => $userId,
                'id_mitra' => $mitraResto->id_mitra,
                'id_pembesaran' => $batchAktif->id_pembesaran,
                'tanggal_order' => now()->subDays(5)->toDateString(),
                'Total_kg' => 50.00,
                'harga_total' => 1750000.00,
                'status_order' => 'dibatalkan',
                'Jenis_order' => 'Ikan Lele Sangkuriang',
                'Bukti_sampai' => null,
            ],
        ];

        foreach ($transaksiList as $index => $transaksi) {
            TransaksiDistribusi::updateOrCreate(
                ['id_transaksi' => $index + 1],
                $transaksi
            );
        }
    }
}

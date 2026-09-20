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

        $mitraResto = MitraDistributor::where('tipe_mitra', 'like', '%Restoran%')->orWhere('tipe_mitra', 'like', '%Rumah Makan%')->first() ?? MitraDistributor::first();
        $mitraPasar = MitraDistributor::where('tipe_mitra', 'like', '%Pasar%')->first() ?? MitraDistributor::first();
        $mitraEkspor = MitraDistributor::where('tipe_mitra', 'like', '%Eksportir%')->first() ?? MitraDistributor::first();

        $batchLele = BatchPembesaran::where('jenis_ikan', 'like', '%Lele%')->where('status_siklus', 'berjalan')->first() ?? BatchPembesaran::first();
        $batchNila = BatchPembesaran::where('jenis_ikan', 'like', '%Nila%')->first() ?? BatchPembesaran::first();
        $batchPatin = BatchPembesaran::where('jenis_ikan', 'like', '%Patin%')->first() ?? BatchPembesaran::first();
        $batchSelesai = BatchPembesaran::where('status_siklus', 'selesai')->first() ?? $batchLele;

        $transaksiList = [
            // 1. Kondisi Status: 'pending' (Pesanan baru masuk dari resto)
            [
                'id_user'       => $userId,
                'id_mitra'      => $mitraResto->id_mitra,
                'id_pembesaran' => $batchLele->id_pembesaran,
                'tanggal_order' => now()->toDateString(),
                'Total_kg'      => 80.00,
                'harga_total'   => 2000000.00,
                'status_order'  => 'pending',
                'Jenis_order'   => 'Ikan Lele Segar Ukuran Konsumsi',
                'Bukti_sampai'  => null,
            ],
            // 2. Kondisi Status: 'pemberokian' (Ikan sedang karantina/puasa di kolam pemberokan)
            [
                'id_user'       => $userId,
                'id_mitra'      => $mitraPasar->id_mitra,
                'id_pembesaran' => $batchNila->id_pembesaran,
                'tanggal_order' => now()->toDateString(),
                'Total_kg'      => 250.00,
                'harga_total'   => 8750000.00,
                'status_order'  => 'pemberokian',
                'Jenis_order'   => 'Ikan Nila Super (Karantina Pemberokan 24 Jam)',
                'Bukti_sampai'  => null,
            ],
            // 3. Kondisi Status: 'siap_kirim' (Selesai pemberokan, dipacking beroksigen)
            [
                'id_user'       => $userId,
                'id_mitra'      => $mitraResto->id_mitra,
                'id_pembesaran' => $batchNila->id_pembesaran,
                'tanggal_order' => now()->toDateString(),
                'Total_kg'      => 120.00,
                'harga_total'   => 4200000.00,
                'status_order'  => 'siap_kirim',
                'Jenis_order'   => 'Ikan Nila Merah Hidup Beroksigen',
                'Bukti_sampai'  => null,
            ],
            // 4. Kondisi Status: 'dalam_pengiriman' (Kurir logistik sedang mengirim armada)
            [
                'id_user'       => $userId,
                'id_mitra'      => $mitraEkspor->id_mitra,
                'id_pembesaran' => $batchPatin->id_pembesaran,
                'tanggal_order' => now()->toDateString(),
                'Total_kg'      => 600.00,
                'harga_total'   => 15000000.00,
                'status_order'  => 'dalam_pengiriman',
                'Jenis_order'   => 'Ikan Patin Segar Standar Ekspor',
                'Bukti_sampai'  => null,
            ],
            // 5. Kondisi Status: 'selesai' (Order sukses diterima & upload bukti foto serah terima)
            [
                'id_user'       => $userId,
                'id_mitra'      => $mitraPasar->id_mitra,
                'id_pembesaran' => $batchSelesai->id_pembesaran,
                'tanggal_order' => now()->subDays(4)->toDateString(),
                'Total_kg'      => 950.00,
                'harga_total'   => 23750000.00,
                'status_order'  => 'selesai',
                'Jenis_order'   => 'Ikan Lele Panen Total Siklus Selesai',
                'Bukti_sampai'  => 'https://images.unsplash.com/photo-1544551763-46a013bb70d5?w=500&auto=format&fit=crop&q=80',
            ],
            // 6. Kondisi Status: 'dibatalkan' (Order dibatalkan karena armada pelanggan terkendala)
            [
                'id_user'       => $userId,
                'id_mitra'      => $mitraResto->id_mitra,
                'id_pembesaran' => $batchLele->id_pembesaran,
                'tanggal_order' => now()->subDays(6)->toDateString(),
                'Total_kg'      => 50.00,
                'harga_total'   => 1250000.00,
                'status_order'  => 'dibatalkan',
                'Jenis_order'   => 'Ikan Lele Konsumsi',
                'Bukti_sampai'  => null,
            ],
        ];

        foreach ($transaksiList as $index => $transaksi) {
            TransaksiDistribusi::updateOrCreate(
                [
                    'id_mitra'      => $transaksi['id_mitra'],
                    'status_order'  => $transaksi['status_order'],
                    'tanggal_order' => $transaksi['tanggal_order'],
                ],
                $transaksi
            );
        }
    }
}

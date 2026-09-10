<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\BatchPembesaran;
use App\Models\BatchPembibitan;
use App\Models\Kolam;
use App\Models\User;

class BatchPembesaranSeeder extends Seeder
{
    public function run(): void
    {
        $petugasPembesaran = User::where('role', 'pembesaran')->first() ?? User::first();
        $userId = $petugasPembesaran->id_user;

        $kolamPembesaran = Kolam::where('nama_kolam', 'like', '%Pembesaran%')->first() ?? Kolam::first();
        $batchBibitSelesai = BatchPembibitan::where('status', 'selesai')->first();

        $batches = [
            // 1. Kondisi Siklus Berjalan (Aktif dalam proses pembesaran)
            [
                'id_kolam' => $kolamPembesaran->id_kolam,
                'id_user' => $userId,
                'id_batch_pembibitan' => $batchBibitSelesai ? $batchBibitSelesai->id_batch : null,
                'asal_bibit' => 'pembibitan_sendiri',
                'biaya_beli_bibit' => 0.00,
                'tgl_tebar' => now()->subDays(60)->toDateString(),
                'est_tgl_panen' => now()->addDays(30)->toDateString(),
                'biomassa_est' => 1200.00,
                'fcr' => 1.10,
                'target_panen_kg' => 1500.00,
                'jumlah_panen_kg' => 0.00,
                'jenis_ikan' => 'Ikan Nila Hitam Super',
                'status_siklus' => 'berjalan',
            ],
            // 2. Kondisi Siklus Selesai (Telah dipanen tuntas)
            [
                'id_kolam' => $kolamPembesaran->id_kolam,
                'id_user' => $userId,
                'id_batch_pembibitan' => null,
                'asal_bibit' => 'beli_luar',
                'biaya_beli_bibit' => 1500000.00,
                'tgl_tebar' => now()->subDays(120)->toDateString(),
                'est_tgl_panen' => now()->subDays(10)->toDateString(),
                'biomassa_est' => 1000.00,
                'fcr' => 1.15,
                'target_panen_kg' => 1000.00,
                'jumlah_panen_kg' => 1050.00,
                'jenis_ikan' => 'Ikan Gurami Padang',
                'status_siklus' => 'selesai',
            ],
        ];

        foreach ($batches as $batch) {
            BatchPembesaran::updateOrCreate(
                [
                    'id_kolam' => $batch['id_kolam'],
                    'jenis_ikan' => $batch['jenis_ikan'],
                    'status_siklus' => $batch['status_siklus'],
                ],
                $batch
            );
        }
    }
}

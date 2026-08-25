<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\BatchPembesaran;
use App\Models\Kolam;
use App\Models\User;

class BatchPembesaranSeeder extends Seeder
{
    public function run(): void
    {
        $petugasPembesaran = User::where('role', 'pembesaran')->first() ?? User::first();
        $userId = $petugasPembesaran->id_user;

        $kolamA1 = Kolam::where('nama_kolam', 'Kolam A1')->first() ?? Kolam::first();
        $kolamB3 = Kolam::where('nama_kolam', 'Kolam B3')->first() ?? Kolam::first();
        $kolamA4 = Kolam::where('nama_kolam', 'Kolam A4')->first() ?? Kolam::first();
        $kolamC2 = Kolam::where('nama_kolam', 'Kolam C2')->first() ?? Kolam::first();
        $kolamB1 = Kolam::where('nama_kolam', 'Kolam B1')->first() ?? Kolam::first();

        $batches = [
            [
                'id_kolam' => $kolamA1->id_kolam,
                'id_user' => $userId,
                'tgl_tebar' => now()->subDays(60)->toDateString(),
                'biomassa_est' => 1250.00,
                'fcr' => 1.10,
                'target_panen_kg' => 1500.00,
                'jumlah_panen_kg' => 0.00,
                'jenis_ikan' => 'Ikan Nila Hitam Super',
                'status_siklus' => 'berjalan',
            ],
            [
                'id_kolam' => $kolamB3->id_kolam,
                'id_user' => $userId,
                'tgl_tebar' => now()->subDays(90)->toDateString(),
                'biomassa_est' => 980.00,
                'fcr' => 1.14,
                'target_panen_kg' => 1200.00,
                'jumlah_panen_kg' => 0.00,
                'jenis_ikan' => 'Ikan Gurami Padang',
                'status_siklus' => 'berjalan',
            ],
            [
                'id_kolam' => $kolamA4->id_kolam,
                'id_user' => $userId,
                'tgl_tebar' => now()->subDays(45)->toDateString(),
                'biomassa_est' => 850.00,
                'fcr' => 1.08,
                'target_panen_kg' => 1000.00,
                'jumlah_panen_kg' => 0.00,
                'jenis_ikan' => 'Ikan Lele Sangkuriang',
                'status_siklus' => 'berjalan',
            ],
            [
                'id_kolam' => $kolamC2->id_kolam,
                'id_user' => $userId,
                'tgl_tebar' => now()->subDays(120)->toDateString(),
                'biomassa_est' => 1600.00,
                'fcr' => 1.15,
                'target_panen_kg' => 2000.00,
                'jumlah_panen_kg' => 0.00,
                'jenis_ikan' => 'Ikan Patin Siam Ekspor',
                'status_siklus' => 'berjalan',
            ],
            [
                'id_kolam' => $kolamB1->id_kolam,
                'id_user' => $userId,
                'tgl_tebar' => now()->subDays(150)->toDateString(),
                'biomassa_est' => 1350.00,
                'fcr' => 1.11,
                'target_panen_kg' => 1300.00,
                'jumlah_panen_kg' => 1350.00,
                'jenis_ikan' => 'Ikan Nila Merah',
                'status_siklus' => 'selesai',
            ],
        ];

        foreach ($batches as $batch) {
            BatchPembesaran::create($batch);
        }
    }
}

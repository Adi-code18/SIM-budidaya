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

        $kolamA1 = Kolam::where('nama_kolam', 'Kolam Pembesaran A1')->first() ?? Kolam::first();
        $kolamA2 = Kolam::where('nama_kolam', 'Kolam Pembesaran A2')->first() ?? Kolam::first();
        $kolamA3 = Kolam::where('nama_kolam', 'Kolam Pembesaran A3')->first() ?? Kolam::first();
        $kolamA4 = Kolam::where('nama_kolam', 'Kolam Pembesaran A4')->first() ?? Kolam::first();

        $batchBibitSelesai = BatchPembibitan::where('status', 'selesai')->first();
        $batchBibitFingerling = BatchPembibitan::where('fase_pertumbuhan', 'FINGERLING')->first();

        $batches = [
            // Sample 1: Kondisi Pembesaran Lele (DOC 40 Hari, FCR Optimal, Pertumbuhan Cepat)
            [
                'id_kolam'            => $kolamA1->id_kolam,
                'id_user'             => $userId,
                'id_batch_pembibitan' => $batchBibitFingerling ? $batchBibitFingerling->id_batch : null,
                'asal_bibit'          => 'pembibitan_sendiri',
                'biaya_beli_bibit'    => 0.00,
                'tgl_tebar'           => now()->subDays(40)->toDateString(),
                'est_tgl_panen'       => now()->addDays(45)->toDateString(),
                'biomassa_est'        => 450.00,
                'fcr'                 => 1.05,
                'target_panen_kg'     => 800.00,
                'jumlah_panen_kg'     => 0.00,
                'jenis_ikan'          => 'Ikan Lele',
                'status_siklus'       => 'berjalan',
            ],
            // Sample 2: Kondisi Pembesaran Nila (DOC 90 Hari, Pakan Kombinasi Pelet + Daun)
            [
                'id_kolam'            => $kolamA2->id_kolam,
                'id_user'             => $userId,
                'id_batch_pembibitan' => null,
                'asal_bibit'          => 'pembibitan_sendiri',
                'biaya_beli_bibit'    => 0.00,
                'tgl_tebar'           => now()->subDays(90)->toDateString(),
                'est_tgl_panen'       => now()->addDays(60)->toDateString(),
                'biomassa_est'        => 850.00,
                'fcr'                 => 1.25,
                'target_panen_kg'     => 1200.00,
                'jumlah_panen_kg'     => 0.00,
                'jenis_ikan'          => 'Ikan Nila',
                'status_siklus'       => 'berjalan',
            ],
            // Sample 3: Kondisi Pembesaran Gurami (DOC 240 Hari, Beli Bibit Luar, Margin Nilai Tinggi)
            [
                'id_kolam'            => $kolamA3->id_kolam,
                'id_user'             => $userId,
                'id_batch_pembibitan' => null,
                'asal_bibit'          => 'beli_luar',
                'biaya_beli_bibit'    => 1500000.00,
                'tgl_tebar'           => now()->subDays(240)->toDateString(),
                'est_tgl_panen'       => now()->addDays(70)->toDateString(),
                'biomassa_est'        => 650.00,
                'fcr'                 => 1.60,
                'target_panen_kg'     => 1000.00,
                'jumlah_panen_kg'     => 0.00,
                'jenis_ikan'          => 'Ikan Gurami',
                'status_siklus'       => 'berjalan',
            ],
            // Sample 4: Kondisi Siap Panen / Waktunya Panen Tiba (Ikan Patin, Melewati Target Waktu Panen)
            [
                'id_kolam'            => $kolamA4->id_kolam,
                'id_user'             => $userId,
                'id_batch_pembibitan' => $batchBibitSelesai ? $batchBibitSelesai->id_batch : null,
                'asal_bibit'          => 'pembibitan_sendiri',
                'biaya_beli_bibit'    => 0.00,
                'tgl_tebar'           => now()->subDays(170)->toDateString(),
                'est_tgl_panen'       => now()->subDays(2)->toDateString(),
                'biomassa_est'        => 1480.00,
                'fcr'                 => 1.30,
                'target_panen_kg'     => 1500.00,
                'jumlah_panen_kg'     => 0.00,
                'jenis_ikan'          => 'Ikan Patin',
                'status_siklus'       => 'siap_panen',
            ],
            // Sample 5: Kondisi Batch Selesai (Riwayat Panen Lengkap)
            [
                'id_kolam'            => $kolamA1->id_kolam,
                'id_user'             => $userId,
                'id_batch_pembibitan' => null,
                'asal_bibit'          => 'beli_luar',
                'biaya_beli_bibit'    => 800000.00,
                'tgl_tebar'           => now()->subDays(130)->toDateString(),
                'est_tgl_panen'       => now()->subDays(40)->toDateString(),
                'biomassa_est'        => 950.00,
                'fcr'                 => 1.10,
                'target_panen_kg'     => 900.00,
                'jumlah_panen_kg'     => 950.00,
                'jenis_ikan'          => 'Ikan Lele',
                'status_siklus'       => 'selesai',
            ],
        ];

        foreach ($batches as $batch) {
            BatchPembesaran::updateOrCreate(
                [
                    'id_kolam'      => $batch['id_kolam'],
                    'jenis_ikan'    => $batch['jenis_ikan'],
                    'status_siklus' => $batch['status_siklus'],
                    'tgl_tebar'     => $batch['tgl_tebar'],
                ],
                $batch
            );
        }
    }
}

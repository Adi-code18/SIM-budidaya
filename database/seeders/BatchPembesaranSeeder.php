<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\BatchPembesaran;
use App\Models\BatchPembibitan;
use App\Models\Kolam;
use App\Models\User;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class BatchPembesaranSeeder extends Seeder
{
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();
        DB::table('batch_pembesaran')->truncate();
        Schema::enableForeignKeyConstraints();

        $petugasPembesaran = User::where('role', 'pembesaran')->first() ?? User::first();
        $userId = $petugasPembesaran ? $petugasPembesaran->id_user : 1;

        $kolamA1 = Kolam::where('nama_kolam', 'like', '%A1%')->first() ?? Kolam::first();
        $kolamA2 = Kolam::where('nama_kolam', 'like', '%A2%')->first() ?? $kolamA1;
        $batchFingerling = BatchPembibitan::where('fase_pertumbuhan', 'FINGERLING')->first();
        $batchBibitSelesai = BatchPembibitan::where('status', 'selesai')->first();

        // 1 Kondisi 1 Contoh untuk setiap status_siklus ('berjalan', 'siap_panen', 'selesai')
        $batches = [
            // 1. Kondisi Siklus: 'berjalan' (Fase pemeliharaan harian berjalan aktif di Kolam A1)
            [
                'id_kolam'            => $kolamA1->id_kolam,
                'id_user'             => $userId,
                'id_batch_pembibitan' => $batchFingerling ? $batchFingerling->id_batch : null,
                'asal_bibit'          => 'pembibitan_sendiri',
                'jumlah_tebar_ekor'   => 2000,
                'biomassa_awal_kg'    => 45.00,
                'biaya_beli_bibit'    => 0.00,
                'tgl_tebar'           => now()->subDays(45)->toDateString(),
                'est_tgl_panen'       => now()->addDays(45)->toDateString(),
                'biomassa_est'        => 420.00,
                'fcr'                 => 1.15,
                'target_panen_kg'     => 750.00,
                'jumlah_panen_kg'     => 0.00,
                'jumlah_panen_ekor'   => 0,
                'jenis_ikan'          => 'Ikan Nila',
                'status_siklus'       => 'berjalan',
            ],
            // 2. Kondisi Siklus: 'siap_panen' (Mencapai bobot target di Kolam A2)
            [
                'id_kolam'            => $kolamA2->id_kolam,
                'id_user'             => $userId,
                'id_batch_pembibitan' => $batchBibitSelesai ? $batchBibitSelesai->id_batch : null,
                'asal_bibit'          => 'pembibitan_sendiri',
                'jumlah_tebar_ekor'   => 2200,
                'biomassa_awal_kg'    => 50.00,
                'biaya_beli_bibit'    => 0.00,
                'tgl_tebar'           => now()->subDays(90)->toDateString(),
                'est_tgl_panen'       => now()->toDateString(),
                'biomassa_est'        => 780.00,
                'fcr'                 => 1.20,
                'target_panen_kg'     => 800.00,
                'jumlah_panen_kg'     => 0.00,
                'jumlah_panen_ekor'   => 0,
                'jenis_ikan'          => 'Ikan Nila',
                'status_siklus'       => 'siap_panen',
            ],
            // 3. Kondisi Siklus: 'selesai' (Panen tuntas dan data siklus ditutup)
            [
                'id_kolam'            => $kolamA1->id_kolam,
                'id_user'             => $userId,
                'id_batch_pembibitan' => null,
                'asal_bibit'          => 'beli_luar',
                'jumlah_tebar_ekor'   => 2000,
                'biomassa_awal_kg'    => 40.00,
                'biaya_beli_bibit'    => 600000.00,
                'tgl_tebar'           => now()->subDays(120)->toDateString(),
                'est_tgl_panen'       => now()->subDays(30)->toDateString(),
                'biomassa_est'        => 850.00,
                'fcr'                 => 1.18,
                'target_panen_kg'     => 800.00,
                'jumlah_panen_kg'     => 830.00,
                'jumlah_panen_ekor'   => 1920,
                'jenis_ikan'          => 'Ikan Lele',
                'status_siklus'       => 'selesai',
            ],
        ];

        foreach ($batches as $batch) {
            BatchPembesaran::create($batch);
        }
    }
}

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
    /**
     * Seeder Batch Pembesaran (Grow-out) AquaFarm.
     * Mengacu pada SOP Doc 2.0 Bab 2 Bagian B (Fase Pembesaran 60-360 hari, Target Konsumsi,
     * Kalkulasi FCR, dan Efisiensi Pakan FCR < 1.2).
     * Memenuhi BRD REQ-GROW-01 s/d 04, REQ-HARV-01, BR-04, BR-08, UAC-06, dan UAC-07.
     */
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();
        DB::table('batch_pembesaran')->truncate();
        Schema::enableForeignKeyConstraints();

        $petugasPembesaran = User::where('role', 'pembesaran')->first() ?? User::first();
        $userId = $petugasPembesaran ? $petugasPembesaran->id_user : 1;

        $kolamA1 = Kolam::where('nama_kolam', 'like', '%A1%')->first() ?? Kolam::first();
        $kolamA2 = Kolam::where('nama_kolam', 'like', '%A2%')->first() ?? $kolamA1;
        $kolamB1 = Kolam::where('nama_kolam', 'like', '%B1 (Bioflok)%')->first() ?? $kolamA1;

        // Ambil batch fingerling pembibitan untuk traceability benih (REQ-GROW-02 & UAC-07)
        $batchFingerling = BatchPembibitan::where('status', 'siap_pindah')->first();
        $batchBibitSelesai = BatchPembibitan::where('status', 'selesai')->first() ?? $batchFingerling;

        $batches = [
            // 1. Kondisi Siklus: 'berjalan' (Pembesaran Aktif di Kolam A1 dari Benih Sendiri)
            // SOP: Ikan Nila target panen 5-6 bulan, FCR ideal 1.2-1.4, efisiensi FCR < 1.2
            [
                'id_kolam'            => $kolamA1->id_kolam,
                'id_user'             => $userId,
                'id_batch_pembibitan' => $batchFingerling ? $batchFingerling->id_batch : null,
                'asal_bibit'          => 'pembibitan_sendiri',
                'jumlah_tebar_ekor'   => 2500,
                'biomassa_awal_kg'    => 48.00,
                'biaya_beli_bibit'    => 0.00,
                'tgl_tebar'           => now()->subDays(45)->toDateString(),
                'est_tgl_panen'       => now()->addDays(75)->toDateString(),
                'biomassa_est'        => 420.00,
                'fcr'                 => 1.15, // Efisien sesuai SOP sumber (FCR < 1.2)
                'target_panen_kg'     => 750.00,
                'jumlah_panen_kg'     => 0.00,
                'jumlah_panen_ekor'   => 0,
                'jenis_ikan'          => 'Ikan Nila',
                'status_siklus'       => 'berjalan',
            ],

            // 2. Kondisi Siklus: 'siap_panen' (Mencapai Bobot Konsumsi di Kolam A2 Siap Alokasi Order)
            // Memenuhi REQ-HARV-01 & BR-08 (Order dialokasikan dari batch berstatus siap_panen)
            [
                'id_kolam'            => $kolamA2->id_kolam,
                'id_user'             => $userId,
                'id_batch_pembibitan' => $batchBibitSelesai ? $batchBibitSelesai->id_batch : null,
                'asal_bibit'          => 'pembibitan_sendiri',
                'jumlah_tebar_ekor'   => 2600,
                'biomassa_awal_kg'    => 52.00,
                'biaya_beli_bibit'    => 0.00,
                'tgl_tebar'           => now()->subDays(120)->toDateString(),
                'est_tgl_panen'       => now()->toDateString(),
                'biomassa_est'        => 780.00,
                'fcr'                 => 1.20,
                'target_panen_kg'     => 800.00,
                'jumlah_panen_kg'     => 0.00,
                'jumlah_panen_ekor'   => 0,
                'jenis_ikan'          => 'Ikan Nila',
                'status_siklus'       => 'siap_panen',
            ],

            // 3. Kondisi Siklus: 'selesai' (Panen Total Siklus Selesai - Asal Beli Luar)
            // Memenuhi REQ-FIN-03 & BR-11 (Biaya beli bibit luar dicatat ke ledger keuangan)
            [
                'id_kolam'            => $kolamB1->id_kolam,
                'id_user'             => $userId,
                'id_batch_pembibitan' => null,
                'asal_bibit'          => 'beli_luar',
                'jumlah_tebar_ekor'   => 2000,
                'biomassa_awal_kg'    => 40.00,
                'biaya_beli_bibit'    => 600000.00, // Biaya bibit dari Balai Benih Luar
                'tgl_tebar'           => now()->subDays(90)->toDateString(),
                'est_tgl_panen'       => now()->subDays(5)->toDateString(),
                'biomassa_est'        => 850.00,
                'fcr'                 => 1.12, // FCR Lele sangat efisien (SOP target: 1.0 - 1.2)
                'target_panen_kg'     => 800.00,
                'jumlah_panen_kg'     => 830.00,
                'jumlah_panen_ekor'   => 1940,
                'jenis_ikan'          => 'Ikan Lele',
                'status_siklus'       => 'selesai',
            ],
        ];

        foreach ($batches as $batch) {
            BatchPembesaran::create($batch);
        }
    }
}

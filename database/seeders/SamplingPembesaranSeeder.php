<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\SamplingPembesaran;
use App\Models\BatchPembesaran;
use App\Models\User;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class SamplingPembesaranSeeder extends Seeder
{
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();
        if (Schema::hasTable('sampling_pembesaran')) {
            DB::table('sampling_pembesaran')->truncate();
        }
        Schema::enableForeignKeyConstraints();

        $petugasPembesaran = User::where('role', 'pembesaran')->first() ?? User::first();
        $userId = $petugasPembesaran ? $petugasPembesaran->id_user : 1;

        $batchBerjalan = BatchPembesaran::where('status_siklus', 'berjalan')->first();
        $batchSiapPanen = BatchPembesaran::where('status_siklus', 'siap_panen')->first();

        $samplings = [];

        if ($batchBerjalan) {
            $samplings[] = [
                'id_pembesaran'         => $batchBerjalan->id_pembesaran,
                'id_user'               => $userId,
                'tgl_sampling'          => now()->subDays(30)->toDateString(),
                'jumlah_sample_ekor'    => 25,
                'total_bobot_sample_gr' => 625.00,
                'abw_gram'              => 25.00,
                'biomassa_koreksi_kg'   => 42.50,
                'catatan'               => 'Sampling DOC 15. Nafsu makan starter aktif dan pertumbuhan benih seragam.',
            ];
            $samplings[] = [
                'id_pembesaran'         => $batchBerjalan->id_pembesaran,
                'id_user'               => $userId,
                'tgl_sampling'          => now()->subDays(15)->toDateString(),
                'jumlah_sample_ekor'    => 25,
                'total_bobot_sample_gr' => 2250.00,
                'abw_gram'              => 90.00,
                'biomassa_koreksi_kg'   => 153.00,
                'catatan'               => 'Sampling DOC 30. Masuk fase grower (Pelet 781-2), ikan lincah dan tidak berlendir berlebih.',
            ];
            $samplings[] = [
                'id_pembesaran'         => $batchBerjalan->id_pembesaran,
                'id_user'               => $userId,
                'tgl_sampling'          => now()->toDateString(),
                'jumlah_sample_ekor'    => 20,
                'total_bobot_sample_gr' => 4200.00,
                'abw_gram'              => 210.00,
                'biomassa_koreksi_kg'   => 357.00,
                'catatan'               => 'Sampling DOC 45. Rata-rata bobot 210 gr/ekor (Size ~5 ekor/kg). Target FCR 1.15 tercapai.',
            ];
        }

        if ($batchSiapPanen) {
            $samplings[] = [
                'id_pembesaran'         => $batchSiapPanen->id_pembesaran,
                'id_user'               => $userId,
                'tgl_sampling'          => now()->subDays(14)->toDateString(),
                'jumlah_sample_ekor'    => 20,
                'total_bobot_sample_gr' => 5800.00,
                'abw_gram'              => 290.00,
                'biomassa_koreksi_kg'   => 542.30,
                'catatan'               => 'Sampling DOC 76. Bobot mendekati target panen pasar restoran.',
            ];
            $samplings[] = [
                'id_pembesaran'         => $batchSiapPanen->id_pembesaran,
                'id_user'               => $userId,
                'tgl_sampling'          => now()->subDays(2)->toDateString(),
                'jumlah_sample_ekor'    => 20,
                'total_bobot_sample_gr' => 7100.00,
                'abw_gram'              => 355.00,
                'biomassa_koreksi_kg'   => 663.85,
                'catatan'               => 'Sampling DOC 88. Rata-rata bobot 355 gr/ekor (Size ~2.8 ekor/kg). Ikan prima dan siap panen.',
            ];
        }

        foreach ($samplings as $s) {
            SamplingPembesaran::create($s);
        }
    }
}

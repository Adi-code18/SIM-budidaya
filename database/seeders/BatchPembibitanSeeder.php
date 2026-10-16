<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\BatchPembibitan;
use App\Models\Kolam;
use App\Models\Ikan;
use App\Models\User;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class BatchPembibitanSeeder extends Seeder
{
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();
        DB::table('batch_pembibitan')->truncate();
        Schema::enableForeignKeyConstraints();

        $petugasPembibitan = User::where('role', 'pembibitan')->first() ?? User::first();
        $userId = $petugasPembibitan ? $petugasPembibitan->id_user : 1;

        $kolamPemijahan = Kolam::where('nama_kolam', 'like', '%Pemijahan%')->first() ?? Kolam::first();
        $kolamPendederan = Kolam::where('nama_kolam', 'like', '%Pendederan%')->first() ?? Kolam::first();

        $ikanNila = Ikan::where('nama_ikan', 'like', '%Nila%')->first() ?? Ikan::first();
        $ikanLele = Ikan::where('nama_ikan', 'like', '%Lele%')->first() ?? $ikanNila;

        // 1 Kondisi 1 Contoh untuk setiap fase pertumbuhan & status pembibitan
        $batches = [
            // 1. Kondisi Fase TELUR (Inkubasi penetasan - status: aktif)
            [
                'id_kolam'              => $kolamPemijahan->id_kolam,
                'id_user'               => $userId,
                'id_ikan'               => $ikanNila ? $ikanNila->id_ikan : null,
                'tgl_pemijahan'         => now()->subDays(1)->toDateString(),
                'est_prcs_pembibitaan'  => now()->addDays(20)->toDateString(),
                'jumlah_telur'          => 150000,
                'jumlah_menetas'        => 0,
                'survival_rate'         => null,
                'jumlah_bibitAwal'      => 150000,
                'jenis_ikan'            => $ikanNila ? $ikanNila->nama_ikan : 'Ikan Nila',
                'fase_pertumbuhan'      => 'TELUR',
                'jumlah_kematian'       => 200,
                'total_bobot_kg'        => 8.00,
                'status'                => 'aktif',
            ],
            // 2. Kondisi Fase LARVA (Perawatan burayak - status: aktif)
            [
                'id_kolam'              => $kolamPendederan->id_kolam,
                'id_user'               => $userId,
                'id_ikan'               => $ikanNila ? $ikanNila->id_ikan : null,
                'tgl_pemijahan'         => now()->subDays(7)->toDateString(),
                'est_prcs_pembibitaan'  => now()->addDays(14)->toDateString(),
                'jumlah_telur'          => 120000,
                'jumlah_menetas'        => 105000,
                'survival_rate'         => 87.50,
                'jumlah_bibitAwal'      => 105000,
                'jenis_ikan'            => $ikanNila ? $ikanNila->nama_ikan : 'Ikan Nila',
                'fase_pertumbuhan'      => 'LARVA',
                'jumlah_kematian'       => 1100,
                'total_bobot_kg'        => 20.00,
                'status'                => 'aktif',
            ],
            // 3. Kondisi Fase FINGERLING (Benih siap tebar/pindah ke kolam pembesaran - status: siap_pindah)
            [
                'id_kolam'              => $kolamPendederan->id_kolam,
                'id_user'               => $userId,
                'id_ikan'               => $ikanNila ? $ikanNila->id_ikan : null,
                'tgl_pemijahan'         => now()->subDays(21)->toDateString(),
                'est_prcs_pembibitaan'  => now()->toDateString(),
                'jumlah_telur'          => 90000,
                'jumlah_menetas'        => 80000,
                'survival_rate'         => 88.00,
                'jumlah_bibitAwal'      => 80000,
                'jenis_ikan'            => $ikanNila ? $ikanNila->nama_ikan : 'Ikan Nila',
                'fase_pertumbuhan'      => 'FINGERLING',
                'jumlah_kematian'       => 1200,
                'total_bobot_kg'        => 45.00,
                'status'                => 'siap_pindah',
            ],
            // 4. Kondisi Batch Selesai (Telah ditransfer ke kolam pembesaran - status: selesai)
            [
                'id_kolam'              => $kolamPendederan->id_kolam,
                'id_user'               => $userId,
                'id_ikan'               => $ikanLele ? $ikanLele->id_ikan : null,
                'tgl_pemijahan'         => now()->subDays(40)->toDateString(),
                'est_prcs_pembibitaan'  => now()->subDays(15)->toDateString(),
                'jumlah_telur'          => 85000,
                'jumlah_menetas'        => 76000,
                'survival_rate'         => 89.40,
                'jumlah_bibitAwal'      => 76000,
                'jenis_ikan'            => $ikanLele ? $ikanLele->nama_ikan : 'Ikan Lele',
                'fase_pertumbuhan'      => 'FINGERLING',
                'jumlah_kematian'       => 1500,
                'total_bobot_kg'        => 50.00,
                'status'                => 'selesai',
            ],
        ];

        foreach ($batches as $batch) {
            BatchPembibitan::create($batch);
        }
    }
}

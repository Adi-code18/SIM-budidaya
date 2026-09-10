<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\BatchPembibitan;
use App\Models\Kolam;
use App\Models\Ikan;
use App\Models\User;

class BatchPembibitanSeeder extends Seeder
{
    public function run(): void
    {
        $petugasPembibitan = User::where('role', 'pembibitan')->first() ?? User::first();
        $userId = $petugasPembibitan->id_user;

        $kolamPemijahan = Kolam::where('nama_kolam', 'Kolam Pemijahan B1')->first() ?? Kolam::first();
        $kolamPendederan = Kolam::where('nama_kolam', 'Kolam Pendederan C1')->first() ?? Kolam::first();

        $ikanNila = Ikan::where('nama_ikan', 'like', '%Nila%')->first();
        $ikanGurami = Ikan::where('nama_ikan', 'like', '%Gurami%')->first();
        $ikanLele = Ikan::where('nama_ikan', 'like', '%Lele%')->first();

        $batches = [
            // 1. Kondisi Fase TELUR (Aktif)
            [
                'id_kolam' => $kolamPemijahan->id_kolam,
                'id_user' => $userId,
                'id_ikan' => $ikanNila ? $ikanNila->id_ikan : null,
                'tgl_pemijahan' => now()->subDays(1)->toDateString(),
                'est_prcs_pembibitaan' => now()->addDays(20)->toDateString(),
                'jumlah_bibitAwal' => 200000,
                'jenis_ikan' => $ikanNila ? $ikanNila->nama_ikan : 'Ikan Nila Hitam Super',
                'fase_pertumbuhan' => 'TELUR',
                'jumlah_kematian' => 500,
                'total_bobot_kg' => 15.00,
                'status' => 'aktif',
            ],
            // 2. Kondisi Fase LARVA (Aktif)
            [
                'id_kolam' => $kolamPendederan->id_kolam,
                'id_user' => $userId,
                'id_ikan' => $ikanGurami ? $ikanGurami->id_ikan : null,
                'tgl_pemijahan' => now()->subDays(7)->toDateString(),
                'est_prcs_pembibitaan' => now()->addDays(23)->toDateString(),
                'jumlah_bibitAwal' => 150000,
                'jenis_ikan' => $ikanGurami ? $ikanGurami->nama_ikan : 'Ikan Gurami Padang',
                'fase_pertumbuhan' => 'LARVA',
                'jumlah_kematian' => 1200,
                'total_bobot_kg' => 28.00,
                'status' => 'aktif',
            ],
            // 3. Kondisi Fase FINGERLING (Aktif)
            [
                'id_kolam' => $kolamPendederan->id_kolam,
                'id_user' => $userId,
                'id_ikan' => $ikanLele ? $ikanLele->id_ikan : null,
                'tgl_pemijahan' => now()->subDays(14)->toDateString(),
                'est_prcs_pembibitaan' => now()->addDays(1)->toDateString(),
                'jumlah_bibitAwal' => 100000,
                'jenis_ikan' => $ikanLele ? $ikanLele->nama_ikan : 'Ikan Lele Sangkuriang',
                'fase_pertumbuhan' => 'FINGERLING',
                'jumlah_kematian' => 800,
                'total_bobot_kg' => 45.00,
                'status' => 'aktif',
            ],
            // 4. Kondisi Batch Selesai (Selesai dipanen & dipindahkan ke pembesaran)
            [
                'id_kolam' => $kolamPendederan->id_kolam,
                'id_user' => $userId,
                'id_ikan' => $ikanNila ? $ikanNila->id_ikan : null,
                'tgl_pemijahan' => now()->subDays(30)->toDateString(),
                'est_prcs_pembibitaan' => now()->subDays(5)->toDateString(),
                'jumlah_bibitAwal' => 180000,
                'jenis_ikan' => $ikanNila ? $ikanNila->nama_ikan : 'Ikan Nila Hitam Super',
                'fase_pertumbuhan' => 'FINGERLING',
                'jumlah_kematian' => 1500,
                'total_bobot_kg' => 75.00,
                'status' => 'selesai',
            ],
        ];

        foreach ($batches as $batch) {
            BatchPembibitan::updateOrCreate(
                [
                    'id_kolam' => $batch['id_kolam'],
                    'jenis_ikan' => $batch['jenis_ikan'],
                    'fase_pertumbuhan' => $batch['fase_pertumbuhan'],
                    'status' => $batch['status'],
                ],
                $batch
            );
        }
    }
}

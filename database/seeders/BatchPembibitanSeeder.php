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

        $kolamPemijahan = Kolam::where('nama_kolam', 'like', '%Pemijahan%')->first() ?? Kolam::first();
        $kolamPendederan = Kolam::where('nama_kolam', 'like', '%Pendederan%')->first() ?? Kolam::first();

        $ikanLele = Ikan::where('nama_ikan', 'like', '%Lele%')->first();
        $ikanGurami = Ikan::where('nama_ikan', 'like', '%Gurami%')->first();
        $ikanNila = Ikan::where('nama_ikan', 'like', '%Nila%')->first();
        $ikanPatin = Ikan::where('nama_ikan', 'like', '%Patin%')->first();

        $batches = [
            // Sample 1: Kondisi Fase TELUR (Baru Memijah - DOC 1 Hari)
            [
                'id_kolam'              => $kolamPemijahan->id_kolam,
                'id_user'               => $userId,
                'id_ikan'               => $ikanLele ? $ikanLele->id_ikan : null,
                'tgl_pemijahan'         => now()->subDays(1)->toDateString(),
                'est_prcs_pembibitaan'  => now()->addDays(14)->toDateString(),
                'jumlah_bibitAwal'      => 180000,
                'jenis_ikan'            => $ikanLele ? $ikanLele->nama_ikan : 'Ikan Lele',
                'fase_pertumbuhan'      => 'TELUR',
                'jumlah_kematian'       => 200,
                'total_bobot_kg'        => 12.00,
                'status'                => 'aktif',
            ],
            // Sample 2: Kondisi Fase LARVA (Perawatan Larva Aktif - DOC 6 Hari)
            [
                'id_kolam'              => $kolamPendederan->id_kolam,
                'id_user'               => $userId,
                'id_ikan'               => $ikanGurami ? $ikanGurami->id_ikan : null,
                'tgl_pemijahan'         => now()->subDays(6)->toDateString(),
                'est_prcs_pembibitaan'  => now()->addDays(35)->toDateString(),
                'jumlah_bibitAwal'      => 120000,
                'jenis_ikan'            => $ikanGurami ? $ikanGurami->nama_ikan : 'Ikan Gurami',
                'fase_pertumbuhan'      => 'LARVA',
                'jumlah_kematian'       => 950,
                'total_bobot_kg'        => 22.50,
                'status'                => 'aktif',
            ],
            // Sample 3: Kondisi Fase FINGERLING (Benih Siap Tebar / Siap Pindah ke Pembesaran)
            [
                'id_kolam'              => $kolamPendederan->id_kolam,
                'id_user'               => $userId,
                'id_ikan'               => $ikanNila ? $ikanNila->id_ikan : null,
                'tgl_pemijahan'         => now()->subDays(22)->toDateString(),
                'est_prcs_pembibitaan'  => now()->toDateString(),
                'jumlah_bibitAwal'      => 85000,
                'jenis_ikan'            => $ikanNila ? $ikanNila->nama_ikan : 'Ikan Nila',
                'fase_pertumbuhan'      => 'FINGERLING',
                'jumlah_kematian'       => 1200,
                'total_bobot_kg'        => 42.00,
                'status'                => 'siap_pindah',
            ],
            // Sample 4: Kondisi Batch Selesai (Telah Ditransfer ke Kolam Pembesaran)
            [
                'id_kolam'              => $kolamPendederan->id_kolam,
                'id_user'               => $userId,
                'id_ikan'               => $ikanPatin ? $ikanPatin->id_ikan : null,
                'tgl_pemijahan'         => now()->subDays(45)->toDateString(),
                'est_prcs_pembibitaan'  => now()->subDays(15)->toDateString(),
                'jumlah_bibitAwal'      => 90000,
                'jenis_ikan'            => $ikanPatin ? $ikanPatin->nama_ikan : 'Ikan Patin',
                'fase_pertumbuhan'      => 'FINGERLING',
                'jumlah_kematian'       => 1500,
                'total_bobot_kg'        => 50.00,
                'status'                => 'selesai',
            ],
        ];

        foreach ($batches as $batch) {
            BatchPembibitan::updateOrCreate(
                [
                    'id_kolam'         => $batch['id_kolam'],
                    'jenis_ikan'       => $batch['jenis_ikan'],
                    'fase_pertumbuhan' => $batch['fase_pertumbuhan'],
                    'status'           => $batch['status'],
                ],
                $batch
            );
        }
    }
}

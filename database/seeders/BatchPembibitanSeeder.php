<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\BatchPembibitan;
use App\Models\Kolam;
use App\Models\User;

class BatchPembibitanSeeder extends Seeder
{
    public function run(): void
    {
        $petugasPembibitan = User::where('role', 'pembibitan')->first() ?? User::first();
        $userId = $petugasPembibitan->id_user;

        $kolamPemijahan = Kolam::where('nama_kolam', 'Kolam Pemijahan A-01')->first() ?? Kolam::first();
        $kolamPenetasan = Kolam::where('nama_kolam', 'Kolam Penetasan B-02')->first() ?? Kolam::first();
        $kolamPendederan = Kolam::where('nama_kolam', 'Kolam Pembibitan L-03')->first() ?? Kolam::first();

        $batches = [
            [
                'id_kolam' => $kolamPemijahan->id_kolam,
                'id_user' => $userId,
                'tgl_pemijahan' => now()->subDays(2)->toDateString(),
                'jumlah_bibitAwal' => 250000,
                'jenis_ikan' => 'Ikan Gurami Padang Super',
                'fase_pertumbuhan' => 'TELUR',
                'jumlah_kematian' => 1200,
                'total_bobot_kg' => 25.0,
                'status' => 'aktif',
            ],
            [
                'id_kolam' => $kolamPenetasan->id_kolam,
                'id_user' => $userId,
                'tgl_pemijahan' => now()->subDays(9)->toDateString(),
                'jumlah_bibitAwal' => 480000,
                'jenis_ikan' => 'Ikan Nila Hitam Super',
                'fase_pertumbuhan' => 'LARVA',
                'jumlah_kematian' => 4500,
                'total_bobot_kg' => 48.0,
                'status' => 'aktif',
            ],
            [
                'id_kolam' => $kolamPendederan->id_kolam,
                'id_user' => $userId,
                'tgl_pemijahan' => now()->subDays(16)->toDateString(),
                'jumlah_bibitAwal' => 310000,
                'jenis_ikan' => 'Ikan Lele Sangkuriang',
                'fase_pertumbuhan' => 'FINGERLING',
                'jumlah_kematian' => 3800,
                'total_bobot_kg' => 62.0,
                'status' => 'aktif',
            ],
            [
                'id_kolam' => $kolamPenetasan->id_kolam,
                'id_user' => $userId,
                'tgl_pemijahan' => now()->subDays(40)->toDateString(),
                'jumlah_bibitAwal' => 200000,
                'jenis_ikan' => 'Ikan Patin Siam',
                'fase_pertumbuhan' => 'FINGERLING',
                'jumlah_kematian' => 5200,
                'total_bobot_kg' => 40.0,
                'status' => 'selesai',
            ],
        ];

        foreach ($batches as $batch) {
            BatchPembibitan::create($batch);
        }
    }
}

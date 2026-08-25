<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ManajemenPakan;
use App\Models\Kolam;
use App\Models\User;

class ManajemenPakanSeeder extends Seeder
{
    public function run(): void
    {
        $petugasPembesaran = User::where('role', 'pembesaran')->first() ?? User::first();
        $userId = $petugasPembesaran->id_user;

        $kolamA1 = Kolam::where('nama_kolam', 'Kolam A1')->first() ?? Kolam::first();
        $kolamB3 = Kolam::where('nama_kolam', 'Kolam B3')->first() ?? Kolam::first();
        $kolamA4 = Kolam::where('nama_kolam', 'Kolam A4')->first() ?? Kolam::first();

        $feedLogs = [
            // 7 Hari Terakhir untuk Kolam A1
            [
                'id_user' => $userId,
                'id_kolam' => $kolamA1->id_kolam,
                'tgl_log' => now()->subDays(6)->toDateString(),
                'kg_pelet' => 18.00,
                'kg_daun' => 12.00,
                'jenis_daun' => 'Daun Talas',
                'total_biaya' => 216000.00,
                'ph_air' => 7.20,
            ],
            [
                'id_user' => $userId,
                'id_kolam' => $kolamA1->id_kolam,
                'tgl_log' => now()->subDays(5)->toDateString(),
                'kg_pelet' => 24.00,
                'kg_daun' => 16.00,
                'jenis_daun' => 'Daun Pepaya',
                'total_biaya' => 288000.00,
                'ph_air' => 7.30,
            ],
            [
                'id_user' => $userId,
                'id_kolam' => $kolamA1->id_kolam,
                'tgl_log' => now()->subDays(4)->toDateString(),
                'kg_pelet' => 22.00,
                'kg_daun' => 20.00,
                'jenis_daun' => 'Kangkung Air',
                'total_biaya' => 264000.00,
                'ph_air' => 7.10,
            ],
            [
                'id_user' => $userId,
                'id_kolam' => $kolamA1->id_kolam,
                'tgl_log' => now()->subDays(3)->toDateString(),
                'kg_pelet' => 30.00,
                'kg_daun' => 18.00,
                'jenis_daun' => 'Daun Singkong',
                'total_biaya' => 360000.00,
                'ph_air' => 7.40,
            ],
            [
                'id_user' => $userId,
                'id_kolam' => $kolamA1->id_kolam,
                'tgl_log' => now()->subDays(2)->toDateString(),
                'kg_pelet' => 35.00,
                'kg_daun' => 22.00,
                'jenis_daun' => 'Daun Talas',
                'total_biaya' => 420000.00,
                'ph_air' => 7.30,
            ],
            [
                'id_user' => $userId,
                'id_kolam' => $kolamA1->id_kolam,
                'tgl_log' => now()->subDays(1)->toDateString(),
                'kg_pelet' => 28.00,
                'kg_daun' => 24.00,
                'jenis_daun' => 'Daun Pepaya',
                'total_biaya' => 336000.00,
                'ph_air' => 7.20,
            ],
            [
                'id_user' => $userId,
                'id_kolam' => $kolamA1->id_kolam,
                'tgl_log' => now()->toDateString(),
                'kg_pelet' => 32.00,
                'kg_daun' => 21.00,
                'jenis_daun' => 'Daun Talas',
                'total_biaya' => 384000.00,
                'ph_air' => 7.30,
            ],

            // Kolam B3 & A4
            [
                'id_user' => $userId,
                'id_kolam' => $kolamB3->id_kolam,
                'tgl_log' => now()->toDateString(),
                'kg_pelet' => 25.00,
                'kg_daun' => 15.00,
                'jenis_daun' => 'Daun Talas',
                'total_biaya' => 300000.00,
                'ph_air' => 7.40,
            ],
            [
                'id_user' => $userId,
                'id_kolam' => $kolamA4->id_kolam,
                'tgl_log' => now()->toDateString(),
                'kg_pelet' => 20.00,
                'kg_daun' => 0.00,
                'jenis_daun' => null,
                'total_biaya' => 240000.00,
                'ph_air' => 6.90,
            ],
        ];

        foreach ($feedLogs as $log) {
            ManajemenPakan::create($log);
        }
    }
}

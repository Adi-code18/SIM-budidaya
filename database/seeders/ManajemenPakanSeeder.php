<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ManajemenPakan;
use App\Models\Kolam;
use App\Models\StokPakan;
use App\Models\User;

class ManajemenPakanSeeder extends Seeder
{
    public function run(): void
    {
        $petugasPembesaran = User::where('role', 'pembesaran')->first() ?? User::first();
        $petugasPembibitan = User::where('role', 'pembibitan')->first() ?? User::first();

        $kolamA1 = Kolam::where('nama_kolam', 'Kolam Pembesaran A1')->first() ?? Kolam::first();
        $kolamA2 = Kolam::where('nama_kolam', 'Kolam Pembesaran A2')->first() ?? Kolam::first();
        $kolamA3 = Kolam::where('nama_kolam', 'Kolam Pembesaran A3')->first() ?? Kolam::first();
        $kolamA4 = Kolam::where('nama_kolam', 'Kolam Pembesaran A4')->first() ?? Kolam::first();
        $kolamC1 = Kolam::where('nama_kolam', 'like', '%Pendederan%')->first() ?? Kolam::first();

        $pakanStarter = StokPakan::where('nama_pakan', 'like', '%Starter%')->first();
        $pakanGrower = StokPakan::where('nama_pakan', 'like', '%Grower%')->first();
        $pakanFinisher = StokPakan::where('nama_pakan', 'like', '%Finisher%')->first();
        $pakanCacing = StokPakan::where('nama_pakan', 'like', '%Cacing%')->first();

        $feedLogs = [
            // 1. Kolam A1 (Lele - DOC 40 Hari) - Pakan Pelet Apung Grower
            [
                'id_user'       => $petugasPembesaran->id_user,
                'id_kolam'      => $kolamA1->id_kolam,
                'id_stok_pakan' => $pakanGrower ? $pakanGrower->id_stok_pakan : null,
                'kategori_fase' => 'pembesaran',
                'tgl_log'       => now()->subDays(2)->toDateString(),
                'kg_pelet'      => 15.00,
                'kg_daun'       => 0.00,
                'jenis_daun'    => null,
                'total_biaya'   => 187500.00,
                'ph_air'        => 7.20,
            ],
            [
                'id_user'       => $petugasPembesaran->id_user,
                'id_kolam'      => $kolamA1->id_kolam,
                'id_stok_pakan' => $pakanGrower ? $pakanGrower->id_stok_pakan : null,
                'kategori_fase' => 'pembesaran',
                'tgl_log'       => now()->toDateString(),
                'kg_pelet'      => 16.50,
                'kg_daun'       => 0.00,
                'jenis_daun'    => null,
                'total_biaya'   => 206250.00,
                'ph_air'        => 7.10,
            ],

            // 2. Kolam A2 (Nila - DOC 90 Hari) - Pakan Kombinasi Pelet + Daun Talas
            [
                'id_user'       => $petugasPembesaran->id_user,
                'id_kolam'      => $kolamA2->id_kolam,
                'id_stok_pakan' => $pakanGrower ? $pakanGrower->id_stok_pakan : null,
                'kategori_fase' => 'pembesaran',
                'tgl_log'       => now()->subDays(1)->toDateString(),
                'kg_pelet'      => 22.00,
                'kg_daun'       => 8.00,
                'jenis_daun'    => 'Daun Talas',
                'total_biaya'   => 275000.00,
                'ph_air'        => 7.40,
            ],
            [
                'id_user'       => $petugasPembesaran->id_user,
                'id_kolam'      => $kolamA2->id_kolam,
                'id_stok_pakan' => $pakanGrower ? $pakanGrower->id_stok_pakan : null,
                'kategori_fase' => 'pembesaran',
                'tgl_log'       => now()->toDateString(),
                'kg_pelet'      => 24.00,
                'kg_daun'       => 10.00,
                'jenis_daun'    => 'Daun Talas',
                'total_biaya'   => 300000.00,
                'ph_air'        => 7.35,
            ],

            // 3. Kolam A3 (Gurami - DOC 240 Hari) - Pelet + Suplemen Daun Senthe
            [
                'id_user'       => $petugasPembesaran->id_user,
                'id_kolam'      => $kolamA3->id_kolam,
                'id_stok_pakan' => $pakanFinisher ? $pakanFinisher->id_stok_pakan : null,
                'kategori_fase' => 'pembesaran',
                'tgl_log'       => now()->subDays(3)->toDateString(),
                'kg_pelet'      => 12.00,
                'kg_daun'       => 15.00,
                'jenis_daun'    => 'Daun Senthe',
                'total_biaya'   => 144000.00,
                'ph_air'        => 7.15,
            ],
            [
                'id_user'       => $petugasPembesaran->id_user,
                'id_kolam'      => $kolamA3->id_kolam,
                'id_stok_pakan' => $pakanFinisher ? $pakanFinisher->id_stok_pakan : null,
                'kategori_fase' => 'pembesaran',
                'tgl_log'       => now()->toDateString(),
                'kg_pelet'      => 14.00,
                'kg_daun'       => 18.00,
                'jenis_daun'    => 'Daun Senthe',
                'total_biaya'   => 168000.00,
                'ph_air'        => 7.20,
            ],

            // 4. Kolam A4 (Patin - DOC 170 Hari, Siap Panen) - Pelet Finisher
            [
                'id_user'       => $petugasPembesaran->id_user,
                'id_kolam'      => $kolamA4->id_kolam,
                'id_stok_pakan' => $pakanFinisher ? $pakanFinisher->id_stok_pakan : null,
                'kategori_fase' => 'pembesaran',
                'tgl_log'       => now()->subDays(4)->toDateString(),
                'kg_pelet'      => 35.00,
                'kg_daun'       => 0.00,
                'jenis_daun'    => null,
                'total_biaya'   => 420000.00,
                'ph_air'        => 7.30,
            ],

            // 5. Kolam Pendederan C1 (Fase Pembibitan - Larva & Fingerling) - Cacing Sutra
            [
                'id_user'       => $petugasPembibitan->id_user,
                'id_kolam'      => $kolamC1->id_kolam,
                'id_stok_pakan' => $pakanCacing ? $pakanCacing->id_stok_pakan : null,
                'kategori_fase' => 'pembibitan',
                'tgl_log'       => now()->subDays(2)->toDateString(),
                'kg_pelet'      => 3.50,
                'kg_daun'       => 0.00,
                'jenis_daun'    => null,
                'total_biaya'   => 70000.00,
                'ph_air'        => 6.90,
            ],
            [
                'id_user'       => $petugasPembibitan->id_user,
                'id_kolam'      => $kolamC1->id_kolam,
                'id_stok_pakan' => $pakanCacing ? $pakanCacing->id_stok_pakan : null,
                'kategori_fase' => 'pembibitan',
                'tgl_log'       => now()->toDateString(),
                'kg_pelet'      => 4.00,
                'kg_daun'       => 0.00,
                'jenis_daun'    => null,
                'total_biaya'   => 80000.00,
                'ph_air'        => 6.85,
            ],
        ];

        foreach ($feedLogs as $log) {
            ManajemenPakan::updateOrCreate(
                [
                    'id_kolam'      => $log['id_kolam'],
                    'tgl_log'       => $log['tgl_log'],
                    'kategori_fase' => $log['kategori_fase'],
                ],
                $log
            );
        }
    }
}

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

        $kolamPembesaran = Kolam::where('nama_kolam', 'like', '%Pembesaran%')->first() ?? Kolam::first();
        $kolamPembibitan = Kolam::where('nama_kolam', 'like', '%Pendederan%')->first() ?? Kolam::first();

        $pakanPelet = StokPakan::where('kategori_peruntukan', 'pembesaran')->first();
        $pakanCacing = StokPakan::where('kategori_peruntukan', 'pembibitan')->first();

        $feedLogs = [
            // 1. Kondisi Log Pakan Fase Pembesaran
            [
                'id_user' => $petugasPembesaran->id_user,
                'id_kolam' => $kolamPembesaran->id_kolam,
                'id_stok_pakan' => $pakanPelet ? $pakanPelet->id_stok_pakan : null,
                'kategori_fase' => 'pembesaran',
                'tgl_log' => now()->toDateString(),
                'kg_pelet' => 25.00,
                'kg_daun' => 10.00,
                'jenis_daun' => 'Daun Talas',
                'total_biaya' => 342500.00,
                'ph_air' => 7.20,
            ],
            // 2. Kondisi Log Pakan Fase Pembibitan
            [
                'id_user' => $petugasPembibitan->id_user,
                'id_kolam' => $kolamPembibitan->id_kolam,
                'id_stok_pakan' => $pakanCacing ? $pakanCacing->id_stok_pakan : null,
                'kategori_fase' => 'pembibitan',
                'tgl_log' => now()->toDateString(),
                'kg_pelet' => 5.00,
                'kg_daun' => 0.00,
                'jenis_daun' => null,
                'total_biaya' => 100000.00,
                'ph_air' => 6.80,
            ],
        ];

        foreach ($feedLogs as $log) {
            ManajemenPakan::updateOrCreate(
                [
                    'id_kolam' => $log['id_kolam'],
                    'tgl_log' => $log['tgl_log'],
                    'kategori_fase' => $log['kategori_fase'],
                ],
                $log
            );
        }
    }
}

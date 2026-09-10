<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Kolam;
use App\Models\User;

class KolamSeeder extends Seeder
{
    public function run(): void
    {
        $manajer = User::where('role', 'manajer')->first() ?? User::first();
        $userId = $manajer ? $manajer->id_user : 1;

        $kolamList = [
            // 1. Kolam untuk Fase Pembesaran
            [
                'nama_kolam' => 'Kolam A1 (Pembesaran)',
                'tipe_kolam' => 'Beton / Pembesaran',
                'kapasitas' => 1000,
                'status' => 'aktif',
                'kesehatan_ph_air' => 7.20,
            ],
            // 2. Kolam untuk Fase Pemijahan (Hatchery)
            [
                'nama_kolam' => 'Kolam Pemijahan B1',
                'tipe_kolam' => 'Hatchery / Pemijahan',
                'kapasitas' => 500,
                'status' => 'aktif',
                'kesehatan_ph_air' => 7.00,
            ],
            // 3. Kolam untuk Fase Pendederan / Penetasan Benih
            [
                'nama_kolam' => 'Kolam Pendederan C1',
                'tipe_kolam' => 'Hatchery / Pendederan',
                'kapasitas' => 10000,
                'status' => 'aktif',
                'kesehatan_ph_air' => 6.80,
            ],
        ];

        foreach ($kolamList as $kolam) {
            Kolam::updateOrCreate(
                ['nama_kolam' => $kolam['nama_kolam']],
                array_merge($kolam, ['id_user' => $userId])
            );
        }
    }
}

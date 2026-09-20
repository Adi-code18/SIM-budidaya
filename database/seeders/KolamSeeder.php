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
            // Kolam Pembesaran
            [
                'nama_kolam'       => 'Kolam Pembesaran A1',
                'tipe_kolam'       => 'Kolam Pembesaran (Beton)',
                'kapasitas'        => 2500,
                'status'           => 'aktif',
                'kesehatan_ph_air' => 7.20,
            ],
            [
                'nama_kolam'       => 'Kolam Pembesaran A2',
                'tipe_kolam'       => 'Kolam Pembesaran (Terpal Bulat / Bioflok)',
                'kapasitas'        => 3000,
                'status'           => 'aktif',
                'kesehatan_ph_air' => 7.40,
            ],
            [
                'nama_kolam'       => 'Kolam Pembesaran A3',
                'tipe_kolam'       => 'Kolam Pembesaran (Tanah)',
                'kapasitas'        => 2000,
                'status'           => 'aktif',
                'kesehatan_ph_air' => 7.10,
            ],
            [
                'nama_kolam'       => 'Kolam Pembesaran A4',
                'tipe_kolam'       => 'Kolam Pembesaran (Beton)',
                'kapasitas'        => 2500,
                'status'           => 'aktif',
                'kesehatan_ph_air' => 7.30,
            ],
            // Kolam Pembibitan / Hatchery
            [
                'nama_kolam'       => 'Kolam Pemijahan B1',
                'tipe_kolam'       => 'Hatchery / Pemijahan',
                'kapasitas'        => 500,
                'status'           => 'aktif',
                'kesehatan_ph_air' => 7.00,
            ],
            [
                'nama_kolam'       => 'Kolam Pendederan C1',
                'tipe_kolam'       => 'Hatchery / Pendederan',
                'kapasitas'        => 15000,
                'status'           => 'aktif',
                'kesehatan_ph_air' => 6.90,
            ],
            // Kolam Pemberokan / Karantina & Penampungan Stok
            [
                'nama_kolam'       => 'Kolam Stok & Pemberokan D1',
                'tipe_kolam'       => 'Kolam Penampungan Stok / Pemberokan',
                'kapasitas'        => 1500,
                'status'           => 'aktif',
                'kesehatan_ph_air' => 7.20,
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

<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Kolam;
use App\Models\User;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class KolamSeeder extends Seeder
{
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();
        DB::table('kolam')->truncate();
        Schema::enableForeignKeyConstraints();

        $manajer = User::where('role', 'manajer')->first() ?? User::first();
        $userId = $manajer ? $manajer->id_user : 1;

        // 1 Kondisi 1 Contoh untuk setiap fungsi kolam
        $kolamList = [
            // 1. Kondisi Tipe: Hatchery / Pemijahan
            [
                'nama_kolam'       => 'Kolam Pemijahan B1',
                'tipe_kolam'       => 'Hatchery / Pemijahan',
                'kapasitas'        => 500,
                'status'           => 'aktif',
                'kesehatan_ph_air' => 7.00,
            ],
            // 2. Kondisi Tipe: Hatchery / Pendederan
            [
                'nama_kolam'       => 'Kolam Pendederan C1',
                'tipe_kolam'       => 'Hatchery / Pendederan',
                'kapasitas'        => 15000,
                'status'           => 'aktif',
                'kesehatan_ph_air' => 6.90,
            ],
            // 3. Kondisi Tipe: Pembesaran
            [
                'nama_kolam'       => 'Kolam Pembesaran A1',
                'tipe_kolam'       => 'Kolam Pembesaran (Beton)',
                'kapasitas'        => 2500,
                'status'           => 'aktif',
                'kesehatan_ph_air' => 7.20,
            ],
            // 4. Kondisi Tipe: Penampungan Stok & Pemberokan
            [
                'nama_kolam'       => 'Kolam Stok & Pemberokan D1',
                'tipe_kolam'       => 'Kolam Penampungan Stok / Pemberokan',
                'kapasitas'        => 1500,
                'status'           => 'aktif',
                'kesehatan_ph_air' => 7.20,
            ],
        ];

        foreach ($kolamList as $kolam) {
            Kolam::create(array_merge($kolam, ['id_user' => $userId]));
        }
    }
}

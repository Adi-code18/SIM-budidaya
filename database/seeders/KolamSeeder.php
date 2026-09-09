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
            [
                'nama_kolam' => 'Kolam A1',
                'tipe_kolam' => 'Beton / Pembesaran',
                'kapasitas' => 500,
                'status' => 'aktif',
                'kesehatan_ph_air' => 7.30,
            ],
            [
                'nama_kolam' => 'Kolam A2',
                'tipe_kolam' => 'Terpal / Pembesaran',
                'kapasitas' => 400,
                'status' => 'aktif',
                'kesehatan_ph_air' => 7.10,
            ],
            [
                'nama_kolam' => 'Kolam A3',
                'tipe_kolam' => 'Tanah / Pembesaran',
                'kapasitas' => 600,
                'status' => 'aktif',
                'kesehatan_ph_air' => 7.50,
            ],
            [
                'nama_kolam' => 'Kolam A4',
                'tipe_kolam' => 'Beton / Pembesaran',
                'kapasitas' => 350,
                'status' => 'aktif',
                'kesehatan_ph_air' => 6.90,
            ],
            [
                'nama_kolam' => 'Kolam B1',
                'tipe_kolam' => 'Beton / Pembesaran',
                'kapasitas' => 500,
                'status' => 'aktif',
                'kesehatan_ph_air' => 7.40,
            ],
            [
                'nama_kolam' => 'Kolam B2',
                'tipe_kolam' => 'Terpal / Pembesaran',
                'kapasitas' => 450,
                'status' => 'aktif',
                'kesehatan_ph_air' => 7.20,
            ],
            [
                'nama_kolam' => 'Kolam B3',
                'tipe_kolam' => 'Beton / Pembesaran',
                'kapasitas' => 550,
                'status' => 'aktif',
                'kesehatan_ph_air' => 7.40,
            ],
            [
                'nama_kolam' => 'Kolam C1',
                'tipe_kolam' => 'Bioflok / Pembesaran',
                'kapasitas' => 300,
                'status' => 'aktif',
                'kesehatan_ph_air' => 7.00,
            ],
            [
                'nama_kolam' => 'Kolam C2',
                'tipe_kolam' => 'Bioflok / Pembesaran',
                'kapasitas' => 400,
                'status' => 'aktif',
                'kesehatan_ph_air' => 7.20,
            ],
            [
                'nama_kolam' => 'Kolam Pemijahan A-01',
                'tipe_kolam' => 'Hatchery / Pemijahan',
                'kapasitas' => 1000,
                'status' => 'aktif',
                'kesehatan_ph_air' => 7.20,
            ],
            [
                'nama_kolam' => 'Kolam Penetasan B-02',
                'tipe_kolam' => 'Hatchery / Penetasan',
                'kapasitas' => 15000,
                'status' => 'aktif',
                'kesehatan_ph_air' => 7.00,
            ],
            [
                'nama_kolam' => 'Kolam Pembibitan L-03',
                'tipe_kolam' => 'Hatchery / Pendederan',
                'kapasitas' => 20000,
                'status' => 'aktif',
                'kesehatan_ph_air' => 6.40,
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

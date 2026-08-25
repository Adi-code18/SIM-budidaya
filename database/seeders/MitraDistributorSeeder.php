<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\MitraDistributor;
use App\Models\User;

class MitraDistributorSeeder extends Seeder
{
    public function run(): void
    {
        $manajer = User::where('role', 'manajer')->first() ?? User::first();
        $userId = $manajer->id_user;

        $mitraList = [
            [
                'id_user' => $userId,
                'nama_mitra' => 'Resto Resto Qu (Pak Adi)',
                'tipe_mitra' => 'Restoran Seafood',
                'alamat' => 'Jl. Mataram No. 12, Kel. Kebonagung, Kec. Sidomulyo, Kota Mataram, NTB 83116',
                'longitude' => 116.11666700,
                'latitude' => -8.58333300,
            ],
            [
                'id_user' => $userId,
                'nama_mitra' => 'Restoran Madani',
                'tipe_mitra' => 'Restoran Keluarga',
                'alamat' => 'Jl. Pejanggik No. 88, Cakranegara, Kota Mataram, NTB',
                'longitude' => 116.12890000,
                'latitude' => -8.59120000,
            ],
            [
                'id_user' => $userId,
                'nama_mitra' => 'Pasar Modern BSD Mataram',
                'tipe_mitra' => 'Pasar Modern & Grosir',
                'alamat' => 'Stand B12, Jl. Lintas Timur No. 12, Ampenan, Kota Mataram, NTB',
                'longitude' => 116.08230000,
                'latitude' => -8.57210000,
            ],
            [
                'id_user' => $userId,
                'nama_mitra' => 'Warung Seafood 88',
                'tipe_mitra' => 'Rumah Makan',
                'alamat' => 'Jl. Raya Senggigi KM 5, Batu Layar, Lombok Barat, NTB',
                'longitude' => 116.05410000,
                'latitude' => -8.50230000,
            ],
            [
                'id_user' => $userId,
                'nama_mitra' => 'Supplier Ekspor PT Bahari Indo',
                'tipe_mitra' => 'Eksportir & Pengolah Fillet',
                'alamat' => 'Kawasan Pelabuhan Lembar No. 9, Lembar, Lombok Barat, NTB',
                'longitude' => 116.07120000,
                'latitude' => -8.72910000,
            ],
            [
                'id_user' => $userId,
                'nama_mitra' => 'RM Padang Berkah Utama',
                'tipe_mitra' => 'Rumah Makan Tradisional',
                'alamat' => 'Jl. Majapahit No. 40, Selaparang, Kota Mataram, NTB',
                'longitude' => 116.10450000,
                'latitude' => -8.56780000,
            ],
        ];

        foreach ($mitraList as $mitra) {
            MitraDistributor::updateOrCreate(
                ['nama_mitra' => $mitra['nama_mitra']],
                $mitra
            );
        }
    }
}

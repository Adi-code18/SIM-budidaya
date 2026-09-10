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
            // 1. Kategori Mitra Restoran / Kuliner
            [
                'id_user'    => $userId,
                'nama_mitra' => 'Resto Seafood Tasikmalaya',
                'tipe_mitra' => 'Restoran ',
                'alamat'     => 'Jl. Gudang Jero II No. 15, Cipedes, Kota Tasikmalaya, Jawa Barat 46112',
                'latitude'   => -7.31938300,
                'longitude'  => 108.21510000,
            ],
            // 2. Kategori Mitra Pasar Tradisional / Modern Grosir
            [
                'id_user'    => $userId,
                'nama_mitra' => 'Pasar Cikurubuk Grosir Tasikmalaya',
                'tipe_mitra' => 'Pasar Modern ',
                'alamat'     => 'Kompleks Pasar Cikurubuk Blok B-12, Mangkubumi, Kota Tasikmalaya, Jawa Barat 46181',
                'latitude'   => -7.33820000,
                'longitude'  => 108.20540000,
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

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
            // SUPPLIER
            [
                'id_user'    => $userId,
                'nama_mitra' => 'CV Sentosa Pakan Mandiri',
                'tipe_mitra' => 'Supplier Pakan',
                'alamat'     => 'Jl. Industri Pergudangan No. 88, Purbaratu, Kota Tasikmalaya, Jawa Barat',
                'latitude'   => -7.32550000,
                'longitude'  => 108.24120000,
            ],
            [
                'id_user'    => $userId,
                'nama_mitra' => 'Balai Pembenihan Unggul Mina Raya',
                'tipe_mitra' => 'Supplier Bibit',
                'alamat'     => 'Jl. Raya Singaparna KM 11, Singaparna, Kab. Tasikmalaya, Jawa Barat',
                'latitude'   => -7.35120000,
                'longitude'  => 108.15230000,
            ],
            // CLIENT / DISTRIBUTOR
            [
                'id_user'    => $userId,
                'nama_mitra' => 'RM Lesehan Gurame Saung Sunda',
                'tipe_mitra' => 'Rumah Makan',
                'alamat'     => 'Jl. Letnan Harun No. 24, Sukarindi, Kota Tasikmalaya, Jawa Barat',
                'latitude'   => -7.30890000,
                'longitude'  => 108.21850000,
            ],
            [
                'id_user'    => $userId,
                'nama_mitra' => 'Resto Seafood & Grill Tasik',
                'tipe_mitra' => 'Restoran',
                'alamat'     => 'Jl. Gudang Jero II No. 15, Cipedes, Kota Tasikmalaya, Jawa Barat 46112',
                'latitude'   => -7.31938300,
                'longitude'  => 108.21510000,
            ],
            [
                'id_user'    => $userId,
                'nama_mitra' => 'Pasar Induk Cikurubuk Grosir Ikan',
                'tipe_mitra' => 'Pasar',
                'alamat'     => 'Kompleks Pasar Cikurubuk Blok B-12, Mangkubumi, Kota Tasikmalaya, Jawa Barat 46181',
                'latitude'   => -7.33820000,
                'longitude'  => 108.20540000,
            ],
            [
                'id_user'    => $userId,
                'nama_mitra' => 'PT Nusantara Aqua Global Export',
                'tipe_mitra' => 'Eksportir',
                'alamat'     => 'Kawasan Berikat Pelabuhan Ratu Raya No. 102, Jawa Barat',
                'latitude'   => -6.98540000,
                'longitude'  => 106.54120000,
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

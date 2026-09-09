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
                'id_user'    => $userId,
                'nama_mitra' => 'Resto Resto Qu (Pak Adi)',
                'tipe_mitra' => 'Restoran Seafood',
                'alamat'     => 'Jl. Gudang Jero II, Cipedes, Kota Tasikmalaya, Jawa Barat 46112',
                'latitude'   => -7.31938300,
                'longitude'  => 108.21510000,
            ],
            [
                'id_user'    => $userId,
                'nama_mitra' => 'Restoran Madani',
                'tipe_mitra' => 'Restoran Keluarga',
                'alamat'     => 'Jl. KH. Z. Mustofa No. 120, Cihideung, Kota Tasikmalaya, Jawa Barat 46122',
                'latitude'   => -7.33250000,
                'longitude'  => 108.21980000,
            ],
            [
                'id_user'    => $userId,
                'nama_mitra' => 'Pasar Cikurubuk Modern Tasikmalaya',
                'tipe_mitra' => 'Pasar Modern & Grosir',
                'alamat'     => 'Kompleks Pasar Cikurubuk Blok B-12, Mangkubumi, Kota Tasikmalaya, Jawa Barat 46181',
                'latitude'   => -7.33820000,
                'longitude'  => 108.20540000,
            ],
            [
                'id_user'    => $userId,
                'nama_mitra' => 'Warung Seafood 88 Priangan',
                'tipe_mitra' => 'Rumah Makan',
                'alamat'     => 'Jl. Ir. H. Juanda No. 88, Sukamulya, Bungursari, Kota Tasikmalaya, Jawa Barat 46151',
                'latitude'   => -7.31120000,
                'longitude'  => 108.20450000,
            ],
            [
                'id_user'    => $userId,
                'nama_mitra' => 'Supplier Ekspor PT Bahari Indo',
                'tipe_mitra' => 'Eksportir & Pengolah Fillet',
                'alamat'     => 'Kawasan Sentra Perikanan Manonjaya KM 4, Manonjaya, Kabupaten Tasikmalaya, Jawa Barat 46197',
                'latitude'   => -7.36210000,
                'longitude'  => 108.30560000,
            ],
            [
                'id_user'    => $userId,
                'nama_mitra' => 'RM Padang Berkah Utama',
                'tipe_mitra' => 'Rumah Makan Tradisional',
                'alamat'     => 'Jl. Dr. Sukardjo No. 45, Tawangsari, Tawang, Kota Tasikmalaya, Jawa Barat 46112',
                'latitude'   => -7.32450000,
                'longitude'  => 108.22340000,
            ],
            [
                'id_user'    => $userId,
                'nama_mitra' => 'Kedai Mitra91',
                'tipe_mitra' => 'Kedai Kuliner',
                'alamat'     => 'Jalan Mitra Batik, Cipedes, Tasikmalaya, Jawa Barat, 46112, Indonesia',
                'latitude'   => -7.32740000,
                'longitude'  => 108.22070000,
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

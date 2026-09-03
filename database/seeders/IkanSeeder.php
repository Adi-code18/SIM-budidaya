<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Ikan;

class IkanSeeder extends Seeder
{
    public function run(): void
    {
        $ikans = [
            [
                'nama_ikan'         => 'Ikan Nila Hitam Super',
                'durasi_penetasan'  => 3,
                'durasi_pembibitan' => 21,
            ],
            [
                'nama_ikan'         => 'Ikan Nila Merah',
                'durasi_penetasan'  => 3,
                'durasi_pembibitan' => 21,
            ],
            [
                'nama_ikan'         => 'Ikan Lele Sangkuriang',
                'durasi_penetasan'  => 2,
                'durasi_pembibitan' => 15,
            ],
            [
                'nama_ikan'         => 'Ikan Gurami Padang',
                'durasi_penetasan'  => 4,
                'durasi_pembibitan' => 30,
            ],
            [
                'nama_ikan'         => 'Ikan Patin Siam',
                'durasi_penetasan'  => 2,
                'durasi_pembibitan' => 25,
            ],
            [
                'nama_ikan'         => 'Ikan Mas Majalaya',
                'durasi_penetasan'  => 3,
                'durasi_pembibitan' => 20,
            ],
            [
                'nama_ikan'         => 'Ikan Bawal Bintang',
                'durasi_penetasan'  => 3,
                'durasi_pembibitan' => 20,
            ],
        ];

        foreach ($ikans as $item) {
            Ikan::create($item);
        }
    }
}

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
                'nama_ikan'         => 'Ikan Gurami Padang',
                'durasi_penetasan'  => 4,
                'durasi_pembibitan' => 30,
            ],
            [
                'nama_ikan'         => 'Ikan Lele Sangkuriang',
                'durasi_penetasan'  => 2,
                'durasi_pembibitan' => 15,
            ],
        ];

        foreach ($ikans as $item) {
            Ikan::updateOrCreate(
                ['nama_ikan' => $item['nama_ikan']],
                $item
            );
        }
    }
}

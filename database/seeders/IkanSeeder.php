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
                'nama_ikan'            => 'Ikan Lele',
                'durasi_penetasan'     => 2,
                'durasi_pembibitan'    => 15,
                'target_konsumsi'      => '8–10 ekor / kg',
                'bulan_panen_min'      => 2.5,
                'bulan_panen_max'      => 3.0,
                'fcr_min'              => 1.00,
                'fcr_max'              => 1.20,
                'jenis_pakan_didukung' => 'Pelet + Vitamin',
            ],
            [
                'nama_ikan'            => 'Ikan Bawal',
                'durasi_penetasan'     => 3,
                'durasi_pembibitan'    => 21,
                'target_konsumsi'      => '3–5 ekor / kg',
                'bulan_panen_min'      => 3.5,
                'bulan_panen_max'      => 4.0,
                'fcr_min'              => 1.20,
                'fcr_max'              => 1.30,
                'jenis_pakan_didukung' => 'Pelet + Vitamin',
            ],
            [
                'nama_ikan'            => 'Ikan Mas',
                'durasi_penetasan'     => 3,
                'durasi_pembibitan'    => 25,
                'target_konsumsi'      => '3–4 ekor / kg',
                'bulan_panen_min'      => 4.5,
                'bulan_panen_max'      => 5.5,
                'fcr_min'              => 1.20,
                'fcr_max'              => 1.50,
                'jenis_pakan_didukung' => 'Pelet + Vitamin + Azola',
            ],
            [
                'nama_ikan'            => 'Ikan Tawes',
                'durasi_penetasan'     => 3,
                'durasi_pembibitan'    => 25,
                'target_konsumsi'      => '4–6 ekor / kg',
                'bulan_panen_min'      => 4.5,
                'bulan_panen_max'      => 5.5,
                'fcr_min'              => 1.30,
                'fcr_max'              => 1.60,
                'jenis_pakan_didukung' => 'Pelet + Vitamin + Dedaunan',
            ],
            [
                'nama_ikan'            => 'Ikan Patin',
                'durasi_penetasan'     => 4,
                'durasi_pembibitan'    => 30,
                'target_konsumsi'      => '2–3 ekor / kg',
                'bulan_panen_min'      => 5.0,
                'bulan_panen_max'      => 6.0,
                'fcr_min'              => 1.20,
                'fcr_max'              => 1.40,
                'jenis_pakan_didukung' => 'Pelet + Vitamin',
            ],
            [
                'nama_ikan'            => 'Ikan Nila',
                'durasi_penetasan'     => 3,
                'durasi_pembibitan'    => 21,
                'target_konsumsi'      => '3–5 ekor / kg',
                'bulan_panen_min'      => 5.0,
                'bulan_panen_max'      => 6.0,
                'fcr_min'              => 1.20,
                'fcr_max'              => 1.40,
                'jenis_pakan_didukung' => 'Pelet + Vitamin + Dedaunan',
            ],
            [
                'nama_ikan'            => 'Ikan Nilem',
                'durasi_penetasan'     => 4,
                'durasi_pembibitan'    => 30,
                'target_konsumsi'      => '8–12 ekor / kg',
                'bulan_panen_min'      => 6.0,
                'bulan_panen_max'      => 7.0,
                'fcr_min'              => 1.40,
                'fcr_max'              => 1.60,
                'jenis_pakan_didukung' => 'Pelet + Vitamin + Dedaunan',
            ],
            [
                'nama_ikan'            => 'Ikan Gurami',
                'durasi_penetasan'     => 5,
                'durasi_pembibitan'    => 45,
                'target_konsumsi'      => '2–3 ekor / kg',
                'bulan_panen_min'      => 10.0,
                'bulan_panen_max'      => 12.0,
                'fcr_min'              => 1.50,
                'fcr_max'              => 1.80,
                'jenis_pakan_didukung' => 'Pelet + Vitamin + Dedaunan',
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

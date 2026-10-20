<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Ikan;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class IkanSeeder extends Seeder
{
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();
        DB::table('ikan')->truncate();
        Schema::enableForeignKeyConstraints();

        // 1 Kondisi 1 Contoh komoditas ikan
        $ikans = [
            // 1. Kondisi Ikan Budidaya Utama (Air Tawar Omnivora)
            [
                'nama_ikan'            => 'Ikan Nila',
                'durasi_penetasan'     => 3,
                'durasi_pembibitan'    => 21,
                'target_konsumsi'      => '3–5 ekor / kg',
                'bulan_panen_min'      => 4.0,
                'bulan_panen_max'      => 5.0,
                'fcr_min'              => 1.15,
                'fcr_max'              => 1.30,
                'jenis_pakan_didukung' => 'Pelet + Vitamin + Dedaunan',
                'harga_jual_kg'        => 32000.00,
            ],
            // 2. Kondisi Ikan Siklus Cepat (Air Tawar Karnivora)
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
                'harga_jual_kg'        => 24000.00,
            ],
            // 3. Ikan Patin Siam (Pangasius)
            [
                'nama_ikan'            => 'Ikan Patin',
                'durasi_penetasan'     => 2,
                'durasi_pembibitan'    => 30,
                'target_konsumsi'      => '2–3 ekor / kg',
                'bulan_panen_min'      => 5.0,
                'bulan_panen_max'      => 6.0,
                'fcr_min'              => 1.20,
                'fcr_max'              => 1.40,
                'jenis_pakan_didukung' => 'Pelet Apung + Vitamin',
                'harga_jual_kg'        => 28000.00,
            ],
            // 4. Ikan Gurame Soang
            [
                'nama_ikan'            => 'Ikan Gurame',
                'durasi_penetasan'     => 4,
                'durasi_pembibitan'    => 45,
                'target_konsumsi'      => '2–3 ekor / kg',
                'bulan_panen_min'      => 10.0,
                'bulan_panen_max'      => 12.0,
                'fcr_min'              => 1.50,
                'fcr_max'              => 1.80,
                'jenis_pakan_didukung' => 'Pelet + Dedaunan Fermentasi',
                'harga_jual_kg'        => 48000.00,
            ],
        ];

        foreach ($ikans as $item) {
            Ikan::create($item);
        }
    }
}

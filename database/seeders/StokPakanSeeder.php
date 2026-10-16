<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\StokPakan;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class StokPakanSeeder extends Seeder
{
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();
        DB::table('stok_pakan')->truncate();
        Schema::enableForeignKeyConstraints();

        // 1 Kondisi 1 Contoh untuk setiap kategori_peruntukan ('pembibitan', 'pembesaran', 'semua')
        $items = [
            // 1. Kondisi Kategori: pembibitan (Pakan alami larva burayak)
            [
                'nama_pakan'          => 'Cacing Sutra Segar',
                'kategori_peruntukan' => 'pembibitan',
                'satuan'              => 'kg',
                'stok_tersisa'        => 25.00,
                'batas_minimum'       => 5.00,
                'harga_per_satuan'    => 20000.00,
                'keterangan'          => 'Pakan alami protein tinggi khusus fase larva burayak pembibitan.',
            ],
            // 2. Kondisi Kategori: pembesaran (Pelet komersial pertumbuhan)
            [
                'nama_pakan'          => 'Pelet Apung Grower (781-2)',
                'kategori_peruntukan' => 'pembesaran',
                'satuan'              => 'kg',
                'stok_tersisa'        => 250.00,
                'batas_minimum'       => 30.00,
                'harga_per_satuan'    => 12500.00,
                'keterangan'          => 'Pelet apung protein 32% untuk fase pertumbuhan kolam pembesaran.',
            ],
            // 3. Kondisi Kategori: semua (Suplemen / Probiotik kesehatan)
            [
                'nama_pakan'          => 'Probiotik EM4 Perikanan',
                'kategori_peruntukan' => 'semua',
                'satuan'              => 'liter',
                'stok_tersisa'        => 15.00,
                'batas_minimum'       => 3.00,
                'harga_per_satuan'    => 30000.00,
                'keterangan'          => 'Probiotik bibis pakan dan pengurai kualitas air untuk semua fase.',
            ],
        ];

        foreach ($items as $item) {
            StokPakan::create($item);
        }
    }
}

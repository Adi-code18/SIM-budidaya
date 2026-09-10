<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\StokPakan;

class StokPakanSeeder extends Seeder
{
    public function run(): void
    {
        $items = [
            // 1. Pakan Khusus Pembibitan (Larva / Benih)
            [
                'nama_pakan' => 'Cacing Sutra Segar',
                'kategori_peruntukan' => 'pembibitan',
                'satuan' => 'kg',
                'stok_tersisa' => 30.00,
                'batas_minimum' => 5.00,
                'harga_per_satuan' => 20000.00,
                'keterangan' => 'Pakan alami kaya protein untuk fase larva pembibitan.',
            ],
            // 2. Pakan Utama Pembesaran
            [
                'nama_pakan' => 'Pelet Apung Grower (781-2)',
                'kategori_peruntukan' => 'pembesaran',
                'satuan' => 'kg',
                'stok_tersisa' => 250.00,
                'batas_minimum' => 30.00,
                'harga_per_satuan' => 12500.00,
                'keterangan' => 'Pelet komersial fase pertumbuhan aktif kolam pembesaran.',
            ],
            // 3. Pakan Tambahan / Suplemen untuk Semua Kategori
            [
                'nama_pakan' => 'Maggot BSF Organik',
                'kategori_peruntukan' => 'semua',
                'satuan' => 'kg',
                'stok_tersisa' => 50.00,
                'batas_minimum' => 10.00,
                'harga_per_satuan' => 8000.00,
                'keterangan' => 'Pakan ekstra protein alternatif kaya nutrisi untuk segala jenis ikan.',
            ],
        ];

        foreach ($items as $item) {
            StokPakan::updateOrCreate(
                ['nama_pakan' => $item['nama_pakan']],
                $item
            );
        }
    }
}

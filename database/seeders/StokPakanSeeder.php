<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\StokPakan;

class StokPakanSeeder extends Seeder
{
    public function run(): void
    {
        $items = [
            // Pakan Pembibitan (Hatchery / Larva / Benih)
            [
                'nama_pakan' => 'Cacing Sutra Segar',
                'kategori_peruntukan' => 'pembibitan',
                'satuan' => 'kg',
                'stok_tersisa' => 25.00,
                'batas_minimum' => 5.00,
                'harga_per_satuan' => 20000.00,
                'keterangan' => 'Pakan alami kaya protein untuk fase larva pembibitan.',
            ],
            [
                'nama_pakan' => 'Pelet Mikro Starter (PF-500)',
                'kategori_peruntukan' => 'pembibitan',
                'satuan' => 'kg',
                'stok_tersisa' => 60.00,
                'batas_minimum' => 10.00,
                'harga_per_satuan' => 16000.00,
                'keterangan' => 'Pelet serbuk mikro halus untuk fingerling / benih ikan.',
            ],
            [
                'nama_pakan' => 'Tepung (Mash)',
                'kategori_peruntukan' => 'pembibitan',
                'satuan' => 'kg',
                'stok_tersisa' => 150.00,
                'batas_minimum' => 15.00,
                'harga_per_satuan' => 10000.00,
                'keterangan' => 'Pakan tepung starter buat larva & benih ikan.',
            ],
            [
                'nama_pakan' => 'Artemia Salina (Kista)',
                'kategori_peruntukan' => 'pembibitan',
                'satuan' => 'kg',
                'stok_tersisa' => 12.00,
                'batas_minimum' => 3.00,
                'harga_per_satuan' => 35000.00,
                'keterangan' => 'Pakan nauplii hidup untuk larva baru menetas.',
            ],

            // Pakan Utama Pembesaran (Pelet Pabrikan)
            [
                'nama_pakan' => 'Pelet Starter Mikro (PF-1000)',
                'kategori_peruntukan' => 'pembesaran',
                'satuan' => 'kg',
                'stok_tersisa' => 120.00,
                'batas_minimum' => 20.00,
                'harga_per_satuan' => 14000.00,
                'keterangan' => 'Pelet awal fase starter kolam pembesaran (DOC 1-20).',
            ],
            [
                'nama_pakan' => 'Pelet Apung Grower (781-2)',
                'kategori_peruntukan' => 'pembesaran',
                'satuan' => 'kg',
                'stok_tersisa' => 250.00,
                'batas_minimum' => 30.00,
                'harga_per_satuan' => 12500.00,
                'keterangan' => 'Pelet fase pertumbuhan aktif pembesaran (DOC 21-60).',
            ],
            [
                'nama_pakan' => 'Pelet Apung Finisher (781-3)',
                'kategori_peruntukan' => 'pembesaran',
                'satuan' => 'kg',
                'stok_tersisa' => 180.00,
                'batas_minimum' => 25.00,
                'harga_per_satuan' => 12000.00,
                'keterangan' => 'Pelet fase finisher menjelang masa panen (DOC > 60).',
            ],

            // Pakan Tambahan / Suplemen & Dedaunan Organik
            [
                'nama_pakan' => 'Daun Talas (Organik)',
                'kategori_peruntukan' => 'pembesaran',
                'satuan' => 'kg',
                'stok_tersisa' => 85.00,
                'batas_minimum' => 15.00,
                'harga_per_satuan' => 3000.00,
                'keterangan' => 'Pakan hijau organik berserat tinggi untuk gurame & nila.',
            ],
            [
                'nama_pakan' => 'Daun Singkong (Organik)',
                'kategori_peruntukan' => 'pembesaran',
                'satuan' => 'kg',
                'stok_tersisa' => 70.00,
                'batas_minimum' => 15.00,
                'harga_per_satuan' => 2500.00,
                'keterangan' => 'Pakan suplemen hijau kaya nutrisi nabati.',
            ],
            [
                'nama_pakan' => 'Daun Pepaya (Antibiotik Alami)',
                'kategori_peruntukan' => 'pembesaran',
                'satuan' => 'kg',
                'stok_tersisa' => 50.00,
                'batas_minimum' => 10.00,
                'harga_per_satuan' => 4000.00,
                'keterangan' => 'Suplemen herbal pencegah bakteri & meningkatkan imunitas ikan.',
            ],
            [
                'nama_pakan' => 'Azolla / Lemna (Tinggi Protein)',
                'kategori_peruntukan' => 'pembesaran',
                'satuan' => 'kg',
                'stok_tersisa' => 90.00,
                'batas_minimum' => 15.00,
                'harga_per_satuan' => 5000.00,
                'keterangan' => 'Tanaman paku air kaya protein nabati pengganti pelet.',
            ],
            [
                'nama_pakan' => 'Maggot BSF (Segar / Kering)',
                'kategori_peruntukan' => 'pembesaran',
                'satuan' => 'kg',
                'stok_tersisa' => 40.00,
                'batas_minimum' => 10.00,
                'harga_per_satuan' => 8000.00,
                'keterangan' => 'Pakan ekstra protein hewani tinggi untuk mempercepat pertumbuhan bobot ikan.',
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

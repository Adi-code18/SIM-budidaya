<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\StokPakan;

class StokPakanSeeder extends Seeder
{
    public function run(): void
    {
        $items = [
            // 1. Pakan Khusus Pembibitan / Larva
            [
                'nama_pakan' => 'Cacing Sutra Segar',
                'kategori_peruntukan' => 'pembibitan',
                'satuan' => 'kg',
                'stok_tersisa' => 35.00,
                'batas_minimum' => 5.00,
                'harga_per_satuan' => 20000.00,
                'keterangan' => 'Pakan alami kaya protein untuk fase larva awal pembibitan.',
            ],
            [
                'nama_pakan' => 'Artemia Cyste Premium',
                'kategori_peruntukan' => 'pembibitan',
                'satuan' => 'kaleng',
                'stok_tersisa' => 12.00,
                'batas_minimum' => 2.00,
                'harga_per_satuan' => 250000.00,
                'keterangan' => 'Pakan mikroskopis hidup untuk penetasan dan burayak.',
            ],
            // 2. Pakan Komersial Fase Pembesaran
            [
                'nama_pakan' => 'Pelet Starter (781-1)',
                'kategori_peruntukan' => 'pembesaran',
                'satuan' => 'kg',
                'stok_tersisa' => 180.00,
                'batas_minimum' => 20.00,
                'harga_per_satuan' => 13500.00,
                'keterangan' => 'Pelet protein tinggi 32% untuk benih baru tebar di kolam pembesaran.',
            ],
            [
                'nama_pakan' => 'Pelet Apung Grower (781-2)',
                'kategori_peruntukan' => 'pembesaran',
                'satuan' => 'kg',
                'stok_tersisa' => 500.00,
                'batas_minimum' => 50.00,
                'harga_per_satuan' => 12500.00,
                'keterangan' => 'Pelet utama masa pertumbuhan aktif harian.',
            ],
            [
                'nama_pakan' => 'Pelet Finisher (781-3)',
                'kategori_peruntukan' => 'pembesaran',
                'satuan' => 'kg',
                'stok_tersisa' => 350.00,
                'batas_minimum' => 40.00,
                'harga_per_satuan' => 12000.00,
                'keterangan' => 'Pelet finishing pembobotan akhir menjelang jadwal panen.',
            ],
            // 3. Pakan Tambahan: Daun & Hijauan Alami
            [
                'nama_pakan' => 'Daun Talas Segar',
                'kategori_peruntukan' => 'pembesaran',
                'satuan' => 'kg',
                'stok_tersisa' => 120.00,
                'batas_minimum' => 20.00,
                'harga_per_satuan' => 2500.00,
                'keterangan' => 'Pakan hijauan alami kaya serat untuk ikan gurame, nila, dan tawes.',
            ],
            [
                'nama_pakan' => 'Daun Azolla Microphylla',
                'kategori_peruntukan' => 'pembesaran',
                'satuan' => 'kg',
                'stok_tersisa' => 100.00,
                'batas_minimum' => 15.00,
                'harga_per_satuan' => 3000.00,
                'keterangan' => 'Pakan hijauan air kaya protein nabati 25-30% penghemat pelet.',
            ],
            [
                'nama_pakan' => 'Daun Singkong / Kangkung Cacah',
                'kategori_peruntukan' => 'pembesaran',
                'satuan' => 'kg',
                'stok_tersisa' => 75.00,
                'batas_minimum' => 15.00,
                'harga_per_satuan' => 2000.00,
                'keterangan' => 'Pakan selingan hijauan fermentasi untuk memperlancar pencernaan ikan.',
            ],
            // 4. Pakan Tambahan: Protein Alternatif
            [
                'nama_pakan' => 'Maggot BSF Kering Organik',
                'kategori_peruntukan' => 'semua',
                'satuan' => 'kg',
                'stok_tersisa' => 80.00,
                'batas_minimum' => 10.00,
                'harga_per_satuan' => 8500.00,
                'keterangan' => 'Pakan ekstra protein alternatif 40%+ dan suplemen daya tahan ikan.',
            ],
            [
                'nama_pakan' => 'Maggot BSF Hidup Segar',
                'kategori_peruntukan' => 'pembesaran',
                'satuan' => 'kg',
                'stok_tersisa' => 60.00,
                'batas_minimum' => 10.00,
                'harga_per_satuan' => 6000.00,
                'keterangan' => 'Pakan hidup pemacu pertumbuhan dan respon nafsu makan ikan.',
            ],
            // 5. Suplemen, Vitamin & Probiotik
            [
                'nama_pakan' => 'Vitamin C & Anti-Stres Ikan',
                'kategori_peruntukan' => 'semua',
                'satuan' => 'kg',
                'stok_tersisa' => 15.00,
                'batas_minimum' => 3.00,
                'harga_per_satuan' => 45000.00,
                'keterangan' => 'Suplemen vitamin campur pakan untuk kekebalan tubuh dan anti-stres cuaca.',
            ],
            [
                'nama_pakan' => 'Probiotik EM4 Perikanan + Molase',
                'kategori_peruntukan' => 'semua',
                'satuan' => 'liter',
                'stok_tersisa' => 25.00,
                'batas_minimum' => 5.00,
                'harga_per_satuan' => 30000.00,
                'keterangan' => 'Probiotik bibis pakan untuk meningkatkan kecernaan pakan (efisiensi FCR).',
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

<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\StokPakan;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class StokPakanSeeder extends Seeder
{
    /**
     * Seeder Stok Pakan Gudang AquaFarm.
     * Mengacu pada SOP Doc 2.0 Bab 1 Bagian C.3 & Bab 2 (Pakan Campuran Pelet + Dedaunan,
     * Pakan Alami Pembibitan Cacing Sutra/Artemia, serta Suplemen & Probiotik).
     * Memenuhi BRD REQ-MST-03, REQ-FEED-01, REQ-FEED-03, dan BR-03.
     */
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();
        DB::table('stok_pakan')->truncate();
        Schema::enableForeignKeyConstraints();

        $items = [
            // ================= PAKAN PEMBIBITAN (PEMBENIHAN) =================
            [
                'nama_pakan'          => 'Cacing Sutra Segar',
                'kategori_peruntukan' => 'pembibitan',
                'satuan'              => 'kg',
                'stok_tersisa'        => 30.00,
                'batas_minimum'       => 5.00,
                'harga_per_satuan'    => 20000.00,
                'keterangan'          => 'Pakan hidup protein tinggi (48-55%) untuk pakan awal burayak larva usia 5-15 hari.',
            ],
            [
                'nama_pakan'          => 'Kista Artemia Salina Grade Super',
                'kategori_peruntukan' => 'pembibitan',
                'satuan'              => 'kaleng',
                'stok_tersisa'        => 8.00,
                'batas_minimum'       => 2.00,
                'harga_per_satuan'    => 185000.00,
                'keterangan'          => 'Kista artemia penetasan tinggi untuk pakan nauplius burayak usia dini pasca kuning telur.',
            ],
            [
                'nama_pakan'          => 'Mikro Pelet Benih PF-500',
                'kategori_peruntukan' => 'pembibitan',
                'satuan'              => 'kg',
                'stok_tersisa'        => 60.00,
                'batas_minimum'       => 15.00,
                'harga_per_satuan'    => 22000.00,
                'keterangan'          => 'Pakan serbuk mikro (0.5 mm) protein 40% untuk masa transisi pendederan burayak ke fingerling.',
            ],

            // ================= PAKAN PEMBESARAN KOMERSIAL (PELET APUNG) =================
            [
                'nama_pakan'          => 'Pelet Apung Starter (781-1)',
                'kategori_peruntukan' => 'pembesaran',
                'satuan'              => 'kg',
                'stok_tersisa'        => 200.00,
                'batas_minimum'       => 40.00,
                'harga_per_satuan'    => 13000.00,
                'keterangan'          => 'Pelet apung butiran kecil (1 mm) protein 33% untuk bibit tebar baru usia 1-4 minggu.',
            ],
            [
                'nama_pakan'          => 'Pelet Apung Grower (781-2)',
                'kategori_peruntukan' => 'pembesaran',
                'satuan'              => 'kg',
                'stok_tersisa'        => 650.00,
                'batas_minimum'       => 80.00,
                'harga_per_satuan'    => 12500.00,
                'keterangan'          => 'Pelet apung butiran sedang (2 mm) protein 31-33% untuk fase pertumbuhan aktif pembesaran.',
            ],
            [
                'nama_pakan'          => 'Pelet Apung Finisher (781)',
                'kategori_peruntukan' => 'pembesaran',
                'satuan'              => 'kg',
                'stok_tersisa'        => 450.00,
                'batas_minimum'       => 60.00,
                'harga_per_satuan'    => 12000.00,
                'keterangan'          => 'Pelet apung butiran besar (3 mm) untuk pembesaran akhir hingga mencapai bobot panen konsumsi.',
            ],

            // ================= PAKAN HIJAUAN ALAMI / DEDAUNAN ORGANIK (SOP BAB 1 C.3) =================
            [
                'nama_pakan'          => 'Dedaunan Hijau Segar (Daun Talas & Kangkung)',
                'kategori_peruntukan' => 'pembesaran',
                'satuan'              => 'kg',
                'stok_tersisa'        => 95.00,
                'batas_minimum'       => 20.00,
                'harga_per_satuan'    => 3500.00,
                'keterangan'          => 'Pakan hijauan organik segar untuk pakan campuran Gurami, Nila, dan Tawes guna menekan nilai FCR.',
            ],
            [
                'nama_pakan'          => 'Tanaman Air Azolla Microphylla Segar',
                'kategori_peruntukan' => 'pembesaran',
                'satuan'              => 'kg',
                'stok_tersisa'        => 75.00,
                'batas_minimum'       => 15.00,
                'harga_per_satuan'    => 3000.00,
                'keterangan'          => 'Pakan nabati berprotein 25-30% hasil budidaya air terpadu untuk campuran pakan Ikan Mas, Nila, dan Gurami.',
            ],
            [
                'nama_pakan'          => 'Daun Pepaya Segar (Pencegah Parasit & Nafsu Makan)',
                'kategori_peruntukan' => 'pembesaran',
                'satuan'              => 'kg',
                'stok_tersisa'        => 50.00,
                'batas_minimum'       => 10.00,
                'harga_per_satuan'    => 4000.00,
                'keterangan'          => 'Pakan suplemen herbal alami untuk melancarkan pencernaan ikan dan pencegahan parasit usus.',
            ],

            // ================= SUPLEMEN & PROBIOTIK KUALITAS AIR (SEMUA FASE) =================
            [
                'nama_pakan'          => 'Vitamin C & Asam Amino Mix',
                'kategori_peruntukan' => 'semua',
                'satuan'              => 'kg',
                'stok_tersisa'        => 20.00,
                'batas_minimum'       => 5.00,
                'harga_per_satuan'    => 45000.00,
                'keterangan'          => 'Premix multivitamin dan asam amino untuk antibodi ikan serta meningkatkan penyerapan nutrisi pakan.',
            ],
            [
                'nama_pakan'          => 'Probiotik EM4 Perikanan',
                'kategori_peruntukan' => 'semua',
                'satuan'              => 'liter',
                'stok_tersisa'        => 25.00,
                'batas_minimum'       => 5.00,
                'harga_per_satuan'    => 30000.00,
                'keterangan'          => 'Probiotik bakteri fermentasi bibis pakan dan pengurai amoniak dasar kolam.',
            ],
        ];

        foreach ($items as $item) {
            StokPakan::create($item);
        }
    }
}

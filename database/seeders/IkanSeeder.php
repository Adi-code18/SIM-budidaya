<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Ikan;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class IkanSeeder extends Seeder
{
    /**
     * Seeder Master Ikan Budidaya.
     * Mengacu langsung pada Standar Operasional Prosedur (SOP Doc 2.0 Bab 2 Halaman 5)
     * dan Kebutuhan Bisnis (BRD REQ-MST-01).
     */
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();
        DB::table('ikan')->truncate();
        Schema::enableForeignKeyConstraints();

        $ikans = [
            // 1. Ikan Lele (Siklus Cepat & FCR Paling Efisien)
            [
                'nama_ikan'            => 'Ikan Lele',
                'durasi_penetasan'     => 2,
                'durasi_pembibitan'    => 21,
                'target_konsumsi'      => '8–10 ekor / kg',
                'bulan_panen_min'      => 2.5,
                'bulan_panen_max'      => 3.0,
                'fcr_min'              => 1.00,
                'fcr_max'              => 1.20,
                'jenis_pakan_didukung' => 'Pelet + Vitamin',
                'harga_jual_kg'        => 24000.00,
            ],
            // 2. Ikan Bawal (Pertumbuhan Cepat & Nafsu Makan Agresif)
            [
                'nama_ikan'            => 'Ikan Bawal',
                'durasi_penetasan'     => 2,
                'durasi_pembibitan'    => 25,
                'target_konsumsi'      => '3–5 ekor / kg',
                'bulan_panen_min'      => 3.5,
                'bulan_panen_max'      => 4.0,
                'fcr_min'              => 1.20,
                'fcr_max'              => 1.30,
                'jenis_pakan_didukung' => 'Pelet + Vitamin',
                'harga_jual_kg'        => 28000.00,
            ],
            // 3. Ikan Mas (Kebutuhan Pasar Segar Tradisional & Azolla)
            [
                'nama_ikan'            => 'Ikan Mas',
                'durasi_penetasan'     => 3,
                'durasi_pembibitan'    => 30,
                'target_konsumsi'      => '3–4 ekor / kg',
                'bulan_panen_min'      => 4.5,
                'bulan_panen_max'      => 5.5,
                'fcr_min'              => 1.20,
                'fcr_max'              => 1.50,
                'jenis_pakan_didukung' => 'Pelet + Vitamin + Azola',
                'harga_jual_kg'        => 36000.00,
            ],
            // 4. Ikan Tawes (Konsumsi Sayuran & Pakan Dedaunan)
            [
                'nama_ikan'            => 'Ikan Tawes',
                'durasi_penetasan'     => 2,
                'durasi_pembibitan'    => 25,
                'target_konsumsi'      => '4–6 ekor / kg',
                'bulan_panen_min'      => 4.5,
                'bulan_panen_max'      => 5.5,
                'fcr_min'              => 1.30,
                'fcr_max'              => 1.60,
                'jenis_pakan_didukung' => 'Pelet + Vitamin + Dedaunan',
                'harga_jual_kg'        => 26000.00,
            ],
            // 5. Ikan Patin (Komoditas Utama Daging Putih / Fillet)
            [
                'nama_ikan'            => 'Ikan Patin',
                'durasi_penetasan'     => 2,
                'durasi_pembibitan'    => 30,
                'target_konsumsi'      => '2–3 ekor / kg',
                'bulan_panen_min'      => 5.0,
                'bulan_panen_max'      => 6.0,
                'fcr_min'              => 1.20,
                'fcr_max'              => 1.40,
                'jenis_pakan_didukung' => 'Pelet + Vitamin',
                'harga_jual_kg'        => 28000.00,
            ],
            // 6. Ikan Nila (Komoditas Unggulan Air Tawar Segar)
            [
                'nama_ikan'            => 'Ikan Nila',
                'durasi_penetasan'     => 3,
                'durasi_pembibitan'    => 25,
                'target_konsumsi'      => '3–5 ekor / kg',
                'bulan_panen_min'      => 5.0,
                'bulan_panen_max'      => 6.0,
                'fcr_min'              => 1.20,
                'fcr_max'              => 1.40,
                'jenis_pakan_didukung' => 'Pelet + Vitamin + Dedaunan',
                'harga_jual_kg'        => 32000.00,
            ],
            // 7. Ikan Nilem (Ikan Endemik Priangan / Gurih Crispy)
            [
                'nama_ikan'            => 'Ikan Nilem',
                'durasi_penetasan'     => 3,
                'durasi_pembibitan'    => 30,
                'target_konsumsi'      => '8–12 ekor / kg',
                'bulan_panen_min'      => 6.0,
                'bulan_panen_max'      => 7.0,
                'fcr_min'              => 1.40,
                'fcr_max'              => 1.60,
                'jenis_pakan_didukung' => 'Pelet + Vitamin + Dedaunan',
                'harga_jual_kg'        => 42000.00,
            ],
            // 8. Ikan Gurami (Komoditas Premium Restoran & Pakan Dedaunan Organik)
            [
                'nama_ikan'            => 'Ikan Gurami',
                'durasi_penetasan'     => 4,
                'durasi_pembibitan'    => 45,
                'target_konsumsi'      => '2–3 ekor / kg',
                'bulan_panen_min'      => 10.0,
                'bulan_panen_max'      => 12.0,
                'fcr_min'              => 1.50,
                'fcr_max'              => 1.80,
                'jenis_pakan_didukung' => 'Pelet + Vitamin + Dedaunan',
                'harga_jual_kg'        => 48000.00,
            ],
        ];

        foreach ($ikans as $item) {
            Ikan::create($item);
        }
    }
}

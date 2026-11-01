<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ManajemenPakan;
use App\Models\Kolam;
use App\Models\StokPakan;
use App\Models\User;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class ManajemenPakanSeeder extends Seeder
{
    /**
     * Seeder Log Pemberian Pakan Harian & Kualitas Air AquaFarm.
     * Mengacu pada SOP Doc 2.0 Bab 1 Bagian C.3 (Pakan Campuran Pelet + Dedaunan untuk menekan FCR)
     * dan Bab 2 Bagian B (Pemberian pakan teratur, fermentasi/peracikan pakan hijau, pengukuran pH).
     * Memenuhi BRD REQ-FEED-01, REQ-FEED-02, REQ-FEED-03, BR-03, BR-05, dan UAC-04.
     */
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();
        DB::table('manajemen_pakan')->truncate();
        Schema::enableForeignKeyConstraints();

        $petugasPembesaran = User::where('role', 'pembesaran')->first() ?? User::first();
        $petugasPembibitan = User::where('role', 'pembibitan')->first() ?? User::first();

        $kolamA1 = Kolam::where('nama_kolam', 'like', '%A1%')->first() ?? Kolam::first();
        $kolamA2 = Kolam::where('nama_kolam', 'like', '%A2%')->first() ?? $kolamA1;
        $kolamPendederan = Kolam::where('nama_kolam', 'like', '%Pendederan%')->first() ?? Kolam::first();

        $pakanGrower = StokPakan::where('nama_pakan', 'like', '%Grower%')->first() ?? StokPakan::first();
        $pakanFinisher = StokPakan::where('nama_pakan', 'like', '%Finisher%')->first() ?? $pakanGrower;
        $pakanCacing = StokPakan::where('nama_pakan', 'like', '%Cacing%')->first() ?? StokPakan::first();

        $feedLogs = [
            // 1. Log H-2: Pakan Kombinasi Pelet + Daun Talas & Kangkung di Kolam Pembesaran A1
            // Sesuai BR-03: Memisahkan pelet komersial dan dedaunan organik
            [
                'id_user'       => $petugasPembesaran ? $petugasPembesaran->id_user : 1,
                'id_kolam'      => $kolamA1->id_kolam,
                'id_stok_pakan' => $pakanGrower ? $pakanGrower->id_stok_pakan : null,
                'kategori_fase' => 'pembesaran',
                'tgl_log'       => now()->subDays(2)->toDateString(),
                'kg_pelet'      => 16.00,
                'kg_daun'       => 3.00,
                'jenis_daun'    => 'Daun Talas & Kangkung Segar',
                'total_biaya'   => 16.00 * (float) ($pakanGrower->harga_per_satuan ?? 12500),
                'ph_air'        => 7.15,
            ],

            // 2. Log H-1: Pakan Kombinasi Pelet + Tanaman Azolla di Kolam Pembesaran A1
            [
                'id_user'       => $petugasPembesaran ? $petugasPembesaran->id_user : 1,
                'id_kolam'      => $kolamA1->id_kolam,
                'id_stok_pakan' => $pakanGrower ? $pakanGrower->id_stok_pakan : null,
                'kategori_fase' => 'pembesaran',
                'tgl_log'       => now()->subDay()->toDateString(),
                'kg_pelet'      => 17.50,
                'kg_daun'       => 2.50,
                'jenis_daun'    => 'Tanaman Air Azolla Microphylla',
                'total_biaya'   => 17.50 * (float) ($pakanGrower->harga_per_satuan ?? 12500),
                'ph_air'        => 7.20,
            ],

            // 3. Log Hari Ini: Pakan Pagi Pelet Grower + Daun Pepaya Segar di Kolam Pembesaran A1
            [
                'id_user'       => $petugasPembesaran ? $petugasPembesaran->id_user : 1,
                'id_kolam'      => $kolamA1->id_kolam,
                'id_stok_pakan' => $pakanGrower ? $pakanGrower->id_stok_pakan : null,
                'kategori_fase' => 'pembesaran',
                'tgl_log'       => now()->toDateString(),
                'kg_pelet'      => 18.00,
                'kg_daun'       => 2.00,
                'jenis_daun'    => 'Daun Pepaya Segar (Pencegah Parasit)',
                'total_biaya'   => 18.00 * (float) ($pakanGrower->harga_per_satuan ?? 12500),
                'ph_air'        => 7.20,
            ],

            // 4. Log H-1: Pemberian Pakan Finisher di Kolam Siap Panen A2
            [
                'id_user'       => $petugasPembesaran ? $petugasPembesaran->id_user : 1,
                'id_kolam'      => $kolamA2->id_kolam,
                'id_stok_pakan' => $pakanFinisher ? $pakanFinisher->id_stok_pakan : null,
                'kategori_fase' => 'pembesaran',
                'tgl_log'       => now()->subDay()->toDateString(),
                'kg_pelet'      => 20.00,
                'kg_daun'       => 3.00,
                'jenis_daun'    => 'Daun Talas Fermentasi',
                'total_biaya'   => 20.00 * (float) ($pakanFinisher->harga_per_satuan ?? 12000),
                'ph_air'        => 7.30,
            ],

            // 5. Log Hari Ini: Pakan Alami Pembibitan (Cacing Sutra) di Kolam Pendederan C1
            [
                'id_user'       => $petugasPembibitan ? $petugasPembibitan->id_user : 1,
                'id_kolam'      => $kolamPendederan->id_kolam,
                'id_stok_pakan' => $pakanCacing ? $pakanCacing->id_stok_pakan : null,
                'kategori_fase' => 'pembibitan',
                'tgl_log'       => now()->toDateString(),
                'kg_pelet'      => 4.00, // cacing steril
                'kg_daun'       => 0.00,
                'jenis_daun'    => null,
                'total_biaya'   => 4.00 * (float) ($pakanCacing->harga_per_satuan ?? 20000),
                'ph_air'        => 6.90,
            ],
        ];

        foreach ($feedLogs as $log) {
            ManajemenPakan::create($log);
        }
    }
}

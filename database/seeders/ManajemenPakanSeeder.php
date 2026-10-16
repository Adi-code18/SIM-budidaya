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
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();
        DB::table('manajemen_pakan')->truncate();
        Schema::enableForeignKeyConstraints();

        $petugasPembesaran = User::where('role', 'pembesaran')->first() ?? User::first();
        $petugasPembibitan = User::where('role', 'pembibitan')->first() ?? User::first();

        $kolamPembesaran = Kolam::where('nama_kolam', 'like', '%Pembesaran%')->first() ?? Kolam::first();
        $kolamPendederan = Kolam::where('nama_kolam', 'like', '%Pendederan%')->first() ?? Kolam::first();

        $pakanGrower = StokPakan::where('kategori_peruntukan', 'pembesaran')->first() ?? StokPakan::first();
        $pakanCacing = StokPakan::where('kategori_peruntukan', 'pembibitan')->first() ?? StokPakan::first();

        // 1 Kondisi 1 Contoh untuk setiap kategori_fase ('pembesaran', 'pembibitan')
        $feedLogs = [
            // 1. Kondisi Fase: 'pembesaran' (Pemberian pelet grower di kolam pembesaran)
            [
                'id_user'       => $petugasPembesaran ? $petugasPembesaran->id_user : 1,
                'id_kolam'      => $kolamPembesaran->id_kolam,
                'id_stok_pakan' => $pakanGrower ? $pakanGrower->id_stok_pakan : null,
                'kategori_fase' => 'pembesaran',
                'tgl_log'       => now()->toDateString(),
                'kg_pelet'      => 15.00,
                'kg_daun'       => 2.00,
                'jenis_daun'    => 'Daun Azolla',
                'total_biaya'   => 187500.00,
                'ph_air'        => 7.20,
            ],
            // 2. Kondisi Fase: 'pembibitan' (Pemberian pakan alami cacing di kolam pendederan)
            [
                'id_user'       => $petugasPembibitan ? $petugasPembibitan->id_user : 1,
                'id_kolam'      => $kolamPendederan->id_kolam,
                'id_stok_pakan' => $pakanCacing ? $pakanCacing->id_stok_pakan : null,
                'kategori_fase' => 'pembibitan',
                'tgl_log'       => now()->toDateString(),
                'kg_pelet'      => 3.50,
                'kg_daun'       => 0.00,
                'jenis_daun'    => null,
                'total_biaya'   => 70000.00,
                'ph_air'        => 6.90,
            ],
        ];

        foreach ($feedLogs as $log) {
            ManajemenPakan::create($log);
        }
    }
}

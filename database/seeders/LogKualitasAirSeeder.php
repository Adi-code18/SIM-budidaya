<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\LogKualitasAir;
use App\Models\Kolam;
use App\Models\User;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class LogKualitasAirSeeder extends Seeder
{
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();
        if (Schema::hasTable('log_kualitas_air')) {
            DB::table('log_kualitas_air')->truncate();
        }
        Schema::enableForeignKeyConstraints();

        $petugasPembesaran = User::where('role', 'pembesaran')->first() ?? User::first();
        $userId = $petugasPembesaran ? $petugasPembesaran->id_user : 1;

        $kolams = Kolam::all();
        if ($kolams->isEmpty()) {
            return;
        }

        $logs = [];

        foreach ($kolams as $k) {
            // Log H-2
            $logs[] = [
                'id_kolam'    => $k->id_kolam,
                'id_user'     => $userId,
                'tgl_catat'   => now()->subDays(2)->setTime(8, 30),
                'suhu_air'    => 28.2,
                'ph_air'      => (float) ($k->kesehatan_ph_air ?? 7.2),
                'kondisi_air' => 'Normal / Kehijauan Sehat',
                'catatan'     => 'Kondisi air stabil, fitoplankton tumbuh normal dan aerasi berjalan baik.',
            ];

            // Log H-1
            $logs[] = [
                'id_kolam'    => $k->id_kolam,
                'id_user'     => $userId,
                'tgl_catat'   => now()->subDay()->setTime(8, 15),
                'suhu_air'    => 28.5,
                'ph_air'      => (float) ($k->kesehatan_ph_air ?? 7.2),
                'kondisi_air' => 'Normal / Kehijauan Sehat',
                'catatan'     => 'Kadar oksigen terlarut mencukupi, ikan bergerak aktif.',
            ];

            // Log Hari Ini
            $logs[] = [
                'id_kolam'    => $k->id_kolam,
                'id_user'     => $userId,
                'tgl_catat'   => now()->setTime(7, 45),
                'suhu_air'    => 28.0,
                'ph_air'      => (float) ($k->kesehatan_ph_air ?? 7.2),
                'kondisi_air' => 'Normal / Kehijauan Sehat',
                'catatan'     => 'Pengukuran pagi standar CBIB. Parameter suhu dan pH memenuhi baku mutu budidaya.',
            ];
        }

        foreach ($logs as $log) {
            LogKualitasAir::create($log);
        }
    }
}

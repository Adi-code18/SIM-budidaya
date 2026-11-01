<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\BatchPembibitan;
use App\Models\Kolam;
use App\Models\Ikan;
use App\Models\User;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class BatchPembibitanSeeder extends Seeder
{
    /**
     * Seeder Batch Pembenihan (Pembibitan) AquaFarm.
     * Mengacu langsung pada SOP Doc 2.0 Bab 2 Bagian A (Fase Penetasan Telur 1-2 hari,
     * Perawatan Larva 5-7 hari, Pendederan I 14-21 hari, target SR > 75%).
     * Memenuhi BRD REQ-SEED-01 s/d 05, BR-01, BR-02, dan UAC-05.
     */
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();
        DB::table('batch_pembibitan')->truncate();
        Schema::enableForeignKeyConstraints();

        $petugasPembibitan = User::where('role', 'pembibitan')->first() ?? User::first();
        $userId = $petugasPembibitan ? $petugasPembibitan->id_user : 1;

        $kolamPemijahan = Kolam::where('nama_kolam', 'like', '%Pemijahan%')->first() ?? Kolam::first();
        $kolamPendederan = Kolam::where('nama_kolam', 'like', '%Pendederan%')->first() ?? Kolam::first();

        $ikanNila = Ikan::where('nama_ikan', 'like', '%Nila%')->first();
        $ikanGurami = Ikan::where('nama_ikan', 'like', '%Gurami%')->first() ?? $ikanNila;
        $ikanPatin = Ikan::where('nama_ikan', 'like', '%Patin%')->first() ?? $ikanNila;
        $ikanLele = Ikan::where('nama_ikan', 'like', '%Lele%')->first() ?? $ikanNila;

        $batches = [
            // 1. Fase TELUR: Pemijahan Baru (SOP: 1-2 hari / 24-48 jam penetasan - status: aktif)
            // Sesuai BR-02: Jika belum menetas, SR belum ditampilkan final (null).
            [
                'id_kolam'              => $kolamPemijahan->id_kolam,
                'id_user'               => $userId,
                'id_ikan'               => $ikanGurami ? $ikanGurami->id_ikan : null,
                'tgl_pemijahan'         => now()->subDays(1)->toDateString(),
                'est_prcs_pembibitaan'  => now()->addDays(40)->toDateString(),
                'jumlah_telur'          => 60000,
                'jumlah_menetas'        => null,
                'survival_rate'         => null,
                'jumlah_bibitAwal'      => 60000,
                'jenis_ikan'            => $ikanGurami ? $ikanGurami->nama_ikan : 'Ikan Gurami',
                'fase_pertumbuhan'      => 'TELUR',
                'jumlah_kematian'       => 150,
                'total_bobot_kg'        => 3.50,
                'status'                => 'aktif',
            ],

            // 2. Fase LARVA: Perawatan Burayak (SOP: 5-7 hari, pakan alami cacing sutra/artemia - status: aktif)
            // Sesuai BR-02 & UAC-05: SR dihitung = (Jumlah Menetas / Total Telur) * 100%
            [
                'id_kolam'              => $kolamPendederan->id_kolam,
                'id_user'               => $userId,
                'id_ikan'               => $ikanPatin ? $ikanPatin->id_ikan : null,
                'tgl_pemijahan'         => now()->subDays(6)->toDateString(),
                'est_prcs_pembibitaan'  => now()->addDays(24)->toDateString(),
                'jumlah_telur'          => 120000,
                'jumlah_menetas'        => 102000,
                'survival_rate'         => 85.00, // (102.000 / 120.000) * 100% = 85.00% (SOP: Target SR > 75%)
                'jumlah_bibitAwal'      => 102000,
                'jenis_ikan'            => $ikanPatin ? $ikanPatin->nama_ikan : 'Ikan Patin',
                'fase_pertumbuhan'      => 'LARVA',
                'jumlah_kematian'       => 850,
                'total_bobot_kg'        => 12.50,
                'status'                => 'aktif',
            ],

            // 3. Fase BENIH: Pendederan I Ukuran 2-3 cm (SOP: 14-21 hari, toleransi kematian < 20% - status: aktif)
            [
                'id_kolam'              => $kolamPendederan->id_kolam,
                'id_user'               => $userId,
                'id_ikan'               => $ikanLele ? $ikanLele->id_ikan : null,
                'tgl_pemijahan'         => now()->subDays(16)->toDateString(),
                'est_prcs_pembibitaan'  => now()->addDays(5)->toDateString(),
                'jumlah_telur'          => 100000,
                'jumlah_menetas'        => 88000,
                'survival_rate'         => 88.00,
                'jumlah_bibitAwal'      => 88000,
                'jenis_ikan'            => $ikanLele ? $ikanLele->nama_ikan : 'Ikan Lele',
                'fase_pertumbuhan'      => 'BENIH',
                'jumlah_kematian'       => 1200,
                'total_bobot_kg'        => 32.00,
                'status'                => 'aktif',
            ],

            // 4. Fase FINGERLING: Benih Unggul 3-5 cm Siap Tebar (SOP: SR > 75%, ukuran seragam - status: siap_pindah)
            // Memenuhi REQ-SEED-05 & UAC-07: Batch fingerling siap_pindah untuk dihubungkan ke pembesaran
            [
                'id_kolam'              => $kolamPendederan->id_kolam,
                'id_user'               => $userId,
                'id_ikan'               => $ikanNila ? $ikanNila->id_ikan : null,
                'tgl_pemijahan'         => now()->subDays(25)->toDateString(),
                'est_prcs_pembibitaan'  => now()->toDateString(),
                'jumlah_telur'          => 90000,
                'jumlah_menetas'        => 78000,
                'survival_rate'         => 86.67, // (78.000 / 90.000) * 100% = 86.67%
                'jumlah_bibitAwal'      => 78000,
                'jenis_ikan'            => $ikanNila ? $ikanNila->nama_ikan : 'Ikan Nila',
                'fase_pertumbuhan'      => 'FINGERLING',
                'jumlah_kematian'       => 1150,
                'total_bobot_kg'        => 48.00,
                'status'                => 'siap_pindah',
            ],

            // 5. Batch Selesai: Telah ditransfer ke Siklus Pembesaran (status: selesai)
            [
                'id_kolam'              => $kolamPendederan->id_kolam,
                'id_user'               => $userId,
                'id_ikan'               => $ikanNila ? $ikanNila->id_ikan : null,
                'tgl_pemijahan'         => now()->subDays(95)->toDateString(),
                'est_prcs_pembibitaan'  => now()->subDays(70)->toDateString(),
                'jumlah_telur'          => 85000,
                'jumlah_menetas'        => 75000,
                'survival_rate'         => 88.24,
                'jumlah_bibitAwal'      => 75000,
                'jenis_ikan'            => $ikanNila ? $ikanNila->nama_ikan : 'Ikan Nila',
                'fase_pertumbuhan'      => 'FINGERLING',
                'jumlah_kematian'       => 1400,
                'total_bobot_kg'        => 52.00,
                'status'                => 'selesai',
            ],
        ];

        foreach ($batches as $batch) {
            BatchPembibitan::create($batch);
        }
    }
}

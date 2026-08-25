<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PengajuanLibur;
use App\Models\User;

class PengajuanLiburSeeder extends Seeder
{
    public function run(): void
    {
        $pekerja1 = User::where('email', 'budi@example.com')->first() ?? User::first();
        $pekerja2 = User::where('email', 'eko@example.com')->first() ?? User::first();
        $pembesaran = User::where('email', 'pembesaran@example.com')->first() ?? User::first();

        $pengajuanList = [
            [
                'id_user' => $pekerja1->id_user,
                'tanggal_mulai' => now()->subDays(10)->toDateString(),
                'tanggal_selesai' => now()->subDays(8)->toDateString(),
                'keterangan' => 'Cuti tahunan - Acara keluarga di luar kota',
                'status_pengajuan' => 'setuju',
            ],
            [
                'id_user' => $pekerja2->id_user,
                'tanggal_mulai' => now()->subDays(3)->toDateString(),
                'tanggal_selesai' => now()->subDays(1)->toDateString(),
                'keterangan' => 'Izin sakit flu berat dan demam',
                'status_pengajuan' => 'setuju',
            ],
            [
                'id_user' => $pembesaran->id_user,
                'tanggal_mulai' => now()->addDays(5)->toDateString(),
                'tanggal_selesai' => now()->addDays(7)->toDateString(),
                'keterangan' => 'Cuti keperluan keluarga penting',
                'status_pengajuan' => 'pending',
            ],
            [
                'id_user' => $pekerja1->id_user,
                'tanggal_mulai' => now()->addDays(12)->toDateString(),
                'tanggal_selesai' => now()->addDays(14)->toDateString(),
                'keterangan' => 'Pengajuan libur cuti lebaran tambahan',
                'status_pengajuan' => 'pending',
            ],
        ];

        foreach ($pengajuanList as $pengajuan) {
            PengajuanLibur::create($pengajuan);
        }
    }
}

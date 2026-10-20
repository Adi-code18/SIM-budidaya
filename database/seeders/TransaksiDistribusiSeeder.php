<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\TransaksiDistribusi;
use App\Models\MitraDistributor;
use App\Models\BatchPembesaran;
use App\Models\User;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class TransaksiDistribusiSeeder extends Seeder
{
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();
        DB::table('transaksi_distribusi')->truncate();
        Schema::enableForeignKeyConstraints();

        $petugasDistribusi = User::where('role', 'petugas_distribusi')->first() ?? User::first();
        $userId = $petugasDistribusi ? $petugasDistribusi->id_user : 1;

        $mitraResto = MitraDistributor::where('tipe_mitra', 'like', '%Restoran%')->first() ?? MitraDistributor::first();
        $mitraPasar = MitraDistributor::where('tipe_mitra', 'like', '%Pasar%')->first() ?? MitraDistributor::first();
        $mitraEkspor = MitraDistributor::where('tipe_mitra', 'like', '%Eksportir%')->first() ?? MitraDistributor::first();

        $batchBerjalan = BatchPembesaran::where('status_siklus', 'berjalan')->first() ?? BatchPembesaran::first();
        $batchSiapPanen = BatchPembesaran::where('status_siklus', 'siap_panen')->first() ?? $batchBerjalan;
        $batchSelesai = BatchPembesaran::where('status_siklus', 'selesai')->first() ?? $batchBerjalan;

        $transaksiList = [
            // 1. Pre-Order Masa Depan (Skenario Lapangan: Pasar A pesan 70 kg untuk 1 minggu ke depan)
            [
                'no_surat_jalan'           => null,
                'id_user'                  => $userId,
                'id_mitra'                 => $mitraPasar ? $mitraPasar->id_mitra : 1,
                'id_pembesaran'            => $batchSiapPanen ? $batchSiapPanen->id_pembesaran : 1,
                'tanggal_order'            => now()->toDateString(),
                'tgl_rencana_kirim'        => now()->addDays(7)->toDateString(),
                'tipe_panen'               => 'parsial',
                'Total_kg'                 => 70.00,
                'berat_aktual_kg'          => null,
                'susut_bobot_kg'           => 0.00,
                'mortalitas_antar_kg'      => 0.00,
                'harga_total'              => 2240000.00,
                'status_order'             => 'pending',
                'waktu_mulai_pemberokan'   => null,
                'waktu_selesai_pemberokan' => null,
                'durasi_pemberokan_jam'    => 18,
                'grading_size'             => 'Ukuran Konsumsi (3-4 ekor/kg)',
                'catatan_grading'          => 'Pre-Order 1 minggu ke depan. Ikan tetap dirawat dan diberi pakan di Kolam A2 hingga H-1 keberangkatan.',
                'Jenis_order'              => 'Pre-Order Ikan Nila Segar (Pasar A - Booking H+7)',
                'Bukti_sampai'             => null,
            ],
            // 2. Kondisi Status: 'pemberokian' (Ikan sedang karantina/puasa 18-24 jam sebelum kirim)
            [
                'no_surat_jalan'           => null,
                'id_user'                  => $userId,
                'id_mitra'                 => $mitraResto ? $mitraResto->id_mitra : 1,
                'id_pembesaran'            => $batchSiapPanen ? $batchSiapPanen->id_pembesaran : 1,
                'tanggal_order'            => now()->toDateString(),
                'tgl_rencana_kirim'        => now()->addHours(10)->toDateString(),
                'tipe_panen'               => 'parsial',
                'Total_kg'                 => 100.00,
                'berat_aktual_kg'          => null,
                'susut_bobot_kg'           => 0.00,
                'mortalitas_antar_kg'      => 0.00,
                'harga_total'              => 3200000.00,
                'status_order'             => 'pemberokian',
                'waktu_mulai_pemberokan'   => now()->subHours(8),
                'waktu_selesai_pemberokan' => null,
                'durasi_pemberokan_jam'    => 18,
                'grading_size'             => 'Ukuran Sedang (4-5 ekor/kg)',
                'catatan_grading'          => 'Ikan aktif dan sehat, proses karantina dan pembersihan lumpur lambung berjalan optimal',
                'Jenis_order'              => 'Ikan Nila Segar (Karantina Pemberokan H-1)',
                'Bukti_sampai'             => null,
            ],
            // 3. Kondisi Status: 'siap_kirim' (Pemberokan selesai, dipacking beroksigen dan surat jalan terbit)
            [
                'no_surat_jalan'           => 'SJ-' . date('Ymd') . '-001',
                'id_user'                  => $userId,
                'id_mitra'                 => $mitraResto ? $mitraResto->id_mitra : 1,
                'id_pembesaran'            => $batchSiapPanen ? $batchSiapPanen->id_pembesaran : 1,
                'tanggal_order'            => now()->toDateString(),
                'tgl_rencana_kirim'        => now()->toDateString(),
                'tipe_panen'               => 'parsial',
                'Total_kg'                 => 120.00,
                'berat_aktual_kg'          => null,
                'susut_bobot_kg'           => 0.00,
                'mortalitas_antar_kg'      => 0.00,
                'harga_total'              => 3840000.00,
                'status_order'             => 'siap_kirim',
                'waktu_mulai_pemberokan'   => now()->subHours(20),
                'waktu_selesai_pemberokan' => now()->subHours(2),
                'durasi_pemberokan_jam'    => 18,
                'grading_size'             => 'Ukuran Besar (3-4 ekor/kg)',
                'catatan_grading'          => 'Karantina 18 jam tuntas, packing plastik beroksigen siap muat armada',
                'Jenis_order'              => 'Ikan Nila Hidup Packing Beroksigen',
                'Bukti_sampai'             => null,
            ],
            // 4. Kondisi Status: 'dalam_pengiriman' (Kurir logistik sedang membawa pesanan menuju lokasi mitra)
            [
                'no_surat_jalan'           => 'SJ-' . date('Ymd') . '-002',
                'id_user'                  => $userId,
                'id_mitra'                 => $mitraEkspor ? $mitraEkspor->id_mitra : 1,
                'id_pembesaran'            => $batchBerjalan ? $batchBerjalan->id_pembesaran : 1,
                'tanggal_order'            => now()->toDateString(),
                'tgl_rencana_kirim'        => now()->toDateString(),
                'tipe_panen'               => 'parsial',
                'Total_kg'                 => 200.00,
                'berat_aktual_kg'          => null,
                'susut_bobot_kg'           => 0.00,
                'mortalitas_antar_kg'      => 0.00,
                'harga_total'              => 6400000.00,
                'status_order'             => 'dalam_pengiriman',
                'waktu_mulai_pemberokan'   => now()->subHours(22),
                'waktu_selesai_pemberokan' => now()->subHours(4),
                'durasi_pemberokan_jam'    => 18,
                'grading_size'             => 'Ukuran Super (2-3 ekor/kg)',
                'catatan_grading'          => 'Armada Pickup box beroksigen berangkat menuju fasilitas cold storage eksportir',
                'Jenis_order'              => 'Ikan Nila Segar Standar Ekspor',
                'Bukti_sampai'             => null,
            ],
            // 5. Kondisi Status: 'selesai' dengan Panen Kuras Total & Verifikasi Kontrak DDP (Susut < 2%)
            [
                'no_surat_jalan'           => 'SJ-' . now()->subDays(2)->format('Ymd') . '-003',
                'id_user'                  => $userId,
                'id_mitra'                 => $mitraPasar ? $mitraPasar->id_mitra : 1,
                'id_pembesaran'            => $batchSelesai ? $batchSelesai->id_pembesaran : 1,
                'tanggal_order'            => now()->subDays(2)->toDateString(),
                'tgl_rencana_kirim'        => now()->subDays(2)->toDateString(),
                'tipe_panen'               => 'kuras_total',
                'Total_kg'                 => 500.00,
                'berat_aktual_kg'          => 494.50, // Susut 5.5 kg = 1.1% (Sangat aman, di bawah batas kontrak 2%)
                'susut_bobot_kg'           => 5.50,
                'mortalitas_antar_kg'      => 1.20,
                'harga_total'              => 16000000.00,
                'status_order'             => 'selesai',
                'waktu_mulai_pemberokan'   => now()->subDays(2)->subHours(20),
                'waktu_selesai_pemberokan' => now()->subDays(2)->subHours(2),
                'durasi_pemberokan_jam'    => 18,
                'grading_size'             => 'Ukuran Campur Konsumsi (3-5 ekor/kg)',
                'catatan_grading'          => 'Panen kuras total siklus selesai. Sisa ikan dipindahkan ke kolam buffer D1. Timbangan serah terima DDP lolos verifikasi.',
                'Jenis_order'              => 'Ikan Nila Panen Total Siklus Selesai',
                'Bukti_sampai'             => 'https://images.unsplash.com/photo-1544551763-46a013bb70d5?w=500&auto=format&fit=crop&q=80',
            ],
            // 6. Kondisi Status: 'dibatalkan' (Order dibatalkan karena kendala pihak pemesan)
            [
                'no_surat_jalan'           => null,
                'id_user'                  => $userId,
                'id_mitra'                 => $mitraResto ? $mitraResto->id_mitra : 1,
                'id_pembesaran'            => $batchBerjalan ? $batchBerjalan->id_pembesaran : 1,
                'tanggal_order'            => now()->subDays(3)->toDateString(),
                'tgl_rencana_kirim'        => now()->subDays(3)->toDateString(),
                'tipe_panen'               => 'parsial',
                'Total_kg'                 => 40.00,
                'berat_aktual_kg'          => null,
                'susut_bobot_kg'           => 0.00,
                'mortalitas_antar_kg'      => 0.00,
                'harga_total'              => 1280000.00,
                'status_order'             => 'dibatalkan',
                'waktu_mulai_pemberokan'   => null,
                'waktu_selesai_pemberokan' => null,
                'durasi_pemberokan_jam'    => 18,
                'grading_size'             => null,
                'catatan_grading'          => 'Dibatalkan oleh restoran karena kendala teknis penampungan internal pemesan',
                'Jenis_order'              => 'Ikan Nila Konsumsi',
                'Bukti_sampai'             => null,
            ],
        ];

        foreach ($transaksiList as $transaksi) {
            TransaksiDistribusi::create($transaksi);
        }
    }
}

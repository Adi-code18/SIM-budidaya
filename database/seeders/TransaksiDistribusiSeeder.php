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
    /**
     * Seeder Transaksi Distribusi & Logistik Pengiriman AquaFarm.
     * Mengacu langsung pada SOP Doc 2.0 Bab 1 Bagian C.4, Bab 2 Bagian B, Bab 3 Bagian C (Flowchart),
     * Bab 4 Bagian D (Mutu Baku: Susut & Mortalitas < 2%), dan Bab 5 Bagian C, D, E (DDP & Jam Panen).
     * Memenuhi BRD REQ-DIST-01 s/d 06, BR-06 s/d BR-10, BR-13, UAC-08 s/d UAC-11.
     */
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();
        DB::table('transaksi_distribusi')->truncate();
        Schema::enableForeignKeyConstraints();

        $petugasDistribusi = User::where('role', 'petugas_distribusi')->first() ?? User::first();
        $userId = $petugasDistribusi ? $petugasDistribusi->id_user : 1;

        $mitraResto1 = MitraDistributor::where('nama_mitra', 'like', '%Saung Gurame%')->first();
        $mitraResto2 = MitraDistributor::where('nama_mitra', 'like', '%Lesehan Alam%')->first() ?? $mitraResto1;
        $mitraPasar1 = MitraDistributor::where('nama_mitra', 'like', '%Cikurubuk%')->first();
        $mitraPasar2 = MitraDistributor::where('nama_mitra', 'like', '%Pasar Manis%')->first() ?? $mitraPasar1;
        $mitraEkspor = MitraDistributor::where('tipe_mitra', 'like', '%Eksportir%')->first() ?? $mitraResto1;

        $batchSiapPanen = BatchPembesaran::where('status_siklus', 'siap_panen')->first();
        $batchBerjalan  = BatchPembesaran::where('status_siklus', 'berjalan')->first() ?? $batchSiapPanen;
        $batchSelesai   = BatchPembesaran::where('status_siklus', 'selesai')->first() ?? $batchSiapPanen;

        $transaksiList = [
            // 1. Pre-Order Pasar Tradisional (Booking Panen H+5 - Status: pending)
            // SOP: Pasar tradisional mengikuti segregasi pemesanan tanpa wajib karantina pemberokan panjang.
            [
                'no_surat_jalan'           => null,
                'id_user'                  => $userId,
                'id_mitra'                 => $mitraPasar2 ? $mitraPasar2->id_mitra : 1,
                'id_pembesaran'            => $batchSiapPanen ? $batchSiapPanen->id_pembesaran : 1,
                'tanggal_order'            => now()->toDateString(),
                'tgl_rencana_kirim'        => now()->addDays(5)->toDateString(),
                'tipe_panen'               => 'parsial',
                'Total_kg'                 => 75.00,
                'berat_aktual_kg'          => null,
                'susut_bobot_kg'           => 0.00,
                'mortalitas_antar_kg'      => 0.00,
                'harga_total'              => 2400000.00, // 75 kg @ Rp 32.000 (Harga snapshot per BR-13)
                'status_order'             => 'pending',
                'waktu_mulai_pemberokan'   => null,
                'waktu_selesai_pemberokan' => null,
                'durasi_pemberokan_jam'    => 18,
                'grading_size'             => 'Ukuran Konsumsi Pasar (4-5 ekor/kg)',
                'catatan_grading'          => 'Pre-Order Pasar Manis Ciamis. Pengambilan subuh (05:00 WIB) sesuai toleransi suhu sejuk.',
                'Jenis_order'              => 'Pasar Tradisional - Ikan Nila Segar Booking',
                'Bukti_sampai'             => null,
            ],

            // 2. Order Rumah Makan Dalam Tahap Karantina (Status: pemberokian)
            // SOP: Rumah makan wajib puasa/pemberokan 12-24 jam di kolam buffer air deras untuk Zero Geosmin.
            // Memenuhi BR-06, BR-07, dan UAC-08.
            [
                'no_surat_jalan'           => null,
                'id_user'                  => $userId,
                'id_mitra'                 => $mitraResto1 ? $mitraResto1->id_mitra : 1,
                'id_pembesaran'            => $batchSiapPanen ? $batchSiapPanen->id_pembesaran : 1,
                'tanggal_order'            => now()->toDateString(),
                'tgl_rencana_kirim'        => now()->addHours(10)->toDateString(),
                'tipe_panen'               => 'parsial',
                'Total_kg'                 => 100.00,
                'berat_aktual_kg'          => null,
                'susut_bobot_kg'           => 0.00,
                'mortalitas_antar_kg'      => 0.00,
                'harga_total'              => 3200000.00, // 100 kg @ Rp 32.000
                'status_order'             => 'pemberokian',
                'waktu_mulai_pemberokan'   => now()->subHours(8),
                'waktu_selesai_pemberokan' => null,
                'durasi_pemberokan_jam'    => 18,
                'grading_size'             => 'Ukuran Sedang Resto (3-4 ekor/kg)',
                'catatan_grading'          => 'Ikan sedang dipuasakan di Kolam Pemberokan D1 (8 jam berjalan dari target 18 jam). Bebas lumpur.',
                'Jenis_order'              => 'Rumah Makan - Ikan Nila Segar Karantina Puasa',
                'Bukti_sampai'             => null,
            ],

            // 3. Order Rumah Makan Siap Kirim (Status: siap_kirim)
            // SOP: Pemberokan tuntas 18 jam, grading QC lulus, dikemas kantong plastik beroksigen, Surat Jalan terbit.
            // Memenuhi BR-09, REQ-DIST-03, dan UAC-10.
            [
                'no_surat_jalan'           => 'SJ-' . date('Ymd') . '-001',
                'id_user'                  => $userId,
                'id_mitra'                 => $mitraResto2 ? $mitraResto2->id_mitra : 1,
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
                'grading_size'             => 'Ukuran Porsi Bakar (2-3 ekor/kg)',
                'catatan_grading'          => 'Pemberokan 18 jam selesai sempurna. Packing kantong plastik beroksigen murni siap muat armada.',
                'Jenis_order'              => 'Rumah Makan - Ikan Nila Hidup Packing Beroksigen',
                'Bukti_sampai'             => null,
            ],

            // 4. Order Eksportir Sedang Dalam Perjalanan (Status: dalam_pengiriman)
            // SOP: Logistik DDP perjalanan jauh, tangki hidup dengan aerasi aktif, Surat Jalan SJ-002 aktif.
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
                'grading_size'             => 'Ukuran Super Standar Ekspor (2-3 ekor/kg)',
                'catatan_grading'          => 'Armada Pickup box ber-aerator sedang dalam perjalanan menuju dermaga/gudang eksportir.',
                'Jenis_order'              => 'Eksportir - Ikan Nila Hidup Standar Ekspor',
                'Bukti_sampai'             => null,
            ],

            // 5. Transaksi Selesai: Panen Total Siklus & Verifikasi Serah Terima DDP (Status: selesai)
            // SOP: Timbang terima DDP lolos verifikasi (susut 1.1% < 2% & mortalitas < 2%), foto bukti sampai sah, pelunasan tuntas.
            // Memenuhi BR-10, BR-11, UAC-11, dan UAC-12.
            [
                'no_surat_jalan'           => 'SJ-' . now()->subDays(2)->format('Ymd') . '-003',
                'id_user'                  => $userId,
                'id_mitra'                 => $mitraPasar1 ? $mitraPasar1->id_mitra : 1,
                'id_pembesaran'            => $batchSelesai ? $batchSelesai->id_pembesaran : 1,
                'tanggal_order'            => now()->subDays(2)->toDateString(),
                'tgl_rencana_kirim'        => now()->subDays(2)->toDateString(),
                'tipe_panen'               => 'kuras_total',
                'Total_kg'                 => 500.00,
                'berat_aktual_kg'          => 494.50, // Susut 5.5 kg = 1.1% (SOP Mutu Baku: batas susut < 2%)
                'susut_bobot_kg'           => 5.50,
                'mortalitas_antar_kg'      => 1.20, // Mortalitas 0.24% (SOP Mutu Baku: mortalitas transportasi < 2%)
                'harga_total'              => 16000000.00, // 500 kg @ Rp 32.000
                'status_order'             => 'selesai',
                'waktu_mulai_pemberokan'   => now()->subDays(2)->subHours(20),
                'waktu_selesai_pemberokan' => now()->subDays(2)->subHours(2),
                'durasi_pemberokan_jam'    => 18,
                'grading_size'             => 'Ukuran Campur Konsumsi (3-5 ekor/kg)',
                'catatan_grading'          => 'Panen kuras total pagi hari (06:30 WIB) suhu sejuk. Timbangan serah terima DDP terverifikasi lolos. Pelunasan diterima.',
                'Jenis_order'              => 'Pasar Tradisional - Ikan Nila Panen Kuras Total',
                'Bukti_sampai'             => 'https://images.unsplash.com/photo-1544551763-46a013bb70d5?w=500&auto=format&fit=crop&q=80',
            ],

            // 6. Transaksi Dibatalkan (Status: dibatalkan)
            // Mencatat kasus penolakan/pembatalan order secara transparan untuk evaluasi operasional.
            [
                'no_surat_jalan'           => null,
                'id_user'                  => $userId,
                'id_mitra'                 => $mitraResto1 ? $mitraResto1->id_mitra : 1,
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
                'catatan_grading'          => 'Dibatalkan oleh pihak pemesan karena kendala teknis penampungan internal restoran mitra.',
                'Jenis_order'              => 'Rumah Makan - Ikan Nila Konsumsi',
                'Bukti_sampai'             => null,
            ],
        ];

        foreach ($transaksiList as $transaksi) {
            TransaksiDistribusi::create($transaksi);
        }
    }
}

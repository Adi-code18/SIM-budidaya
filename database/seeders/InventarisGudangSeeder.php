<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\InventarisGudang;
use App\Models\LogTransaksiGudang;
use App\Models\Kolam;
use App\Models\User;
use App\Models\Keuangan;

class InventarisGudangSeeder extends Seeder
{
    public function run(): void
    {
        $manajer = User::where('role', 'manajer')->first() ?? User::first();
        $userId = $manajer ? $manajer->id_user : 1;
        $kolams = Kolam::all();
        $kolam1 = $kolams->first();
        $kolam2 = $kolams->skip(1)->first() ?? $kolam1;

        $items = [
            // SAPROTAN (Bahan Habis Pakai)
            [
                'kode_barang' => 'SPT-0001',
                'nama_barang' => 'Kapur Dolomit Super (Penstabil pH Air)',
                'kategori_barang' => 'saprotan',
                'tipe_saprotan' => 'desinfektan_kapur',
                'satuan' => 'sak',
                'stok_tersisa' => 25,
                'min_stok' => 5,
                'harga_satuan_terakhir' => 65000,
                'kondisi_alat' => null,
                'lokasi_penyimpanan' => 'Gudang Utama - Rak A1',
                'catatan' => 'Dolomit murni mesh 100 untuk pengapuran rutin pasca hujan & sterilisasi dasar kolam.'
            ],
            [
                'kode_barang' => 'SPT-0002',
                'nama_barang' => 'Garam Krosok Non-Yodium (Desinfeksi Air)',
                'kategori_barang' => 'saprotan',
                'tipe_saprotan' => 'desinfektan_kapur',
                'satuan' => 'karung',
                'stok_tersisa' => 15,
                'min_stok' => 3,
                'harga_satuan_terakhir' => 85000,
                'kondisi_alat' => null,
                'lokasi_penyimpanan' => 'Gudang Utama - Pallet A2',
                'catatan' => 'Untuk karantina bibit baru, pengobatan parasit kulit, dan pemberokan sebelum panen.'
            ],
            [
                'kode_barang' => 'SPT-0003',
                'nama_barang' => 'Probiotik EM4 Perikanan (Bakteri Nitrifikasi)',
                'kategori_barang' => 'saprotan',
                'tipe_saprotan' => 'obat_kimia',
                'satuan' => 'botol',
                'stok_tersisa' => 18,
                'min_stok' => 5,
                'harga_satuan_terakhir' => 45000,
                'kondisi_alat' => null,
                'lokasi_penyimpanan' => 'Gudang Farmasi - Lemari B1',
                'catatan' => 'Pengurai sisa pakan amonia & pembentuk flok bakteri probiotik kolam intensif.'
            ],
            [
                'kode_barang' => 'SPT-0004',
                'nama_barang' => 'Vitamin C & Imunostimulan Aquavit 1kg',
                'kategori_barang' => 'saprotan',
                'tipe_saprotan' => 'vitamin_suplemen',
                'satuan' => 'bungkus',
                'stok_tersisa' => 8,
                'min_stok' => 3,
                'harga_satuan_terakhir' => 95000,
                'kondisi_alat' => null,
                'lokasi_penyimpanan' => 'Gudang Farmasi - Lemari B2',
                'catatan' => 'Dicampur pakan untuk meningkatkan nafsu makan & daya tahan stres cuaca ekstrem.'
            ],
            [
                'kode_barang' => 'SPT-0005',
                'nama_barang' => 'Methylene Blue Konsentrat 500ml',
                'kategori_barang' => 'saprotan',
                'tipe_saprotan' => 'obat_kimia',
                'satuan' => 'botol',
                'stok_tersisa' => 6,
                'min_stok' => 2,
                'harga_satuan_terakhir' => 35000,
                'kondisi_alat' => null,
                'lokasi_penyimpanan' => 'Gudang Farmasi - Lemari B3',
                'catatan' => 'Obat biru untuk infeksi jamur Saprolegnia pada telur hatchery dan benih lecet.'
            ],
            [
                'kode_barang' => 'SPT-0006',
                'nama_barang' => 'Plastik Packing Benih Beroksigen (Roll 50m)',
                'kategori_barang' => 'saprotan',
                'tipe_saprotan' => 'pengepakan_panen',
                'satuan' => 'roll',
                'stok_tersisa' => 4,
                'min_stok' => 2,
                'harga_satuan_terakhir' => 120000,
                'kondisi_alat' => null,
                'lokasi_penyimpanan' => 'Gudang Packing - Rak C1',
                'catatan' => 'Plastik PE tebal 0.08mm khusus pengiriman bibit fingerling luar kota.'
            ],
            [
                'kode_barang' => 'SPT-0007',
                'nama_barang' => 'Zeolit Granul Penjerap Toksin (Sak 25kg)',
                'kategori_barang' => 'saprotan',
                'tipe_saprotan' => 'desinfektan_kapur',
                'satuan' => 'sak',
                'stok_tersisa' => 2, // Kritis
                'min_stok' => 5,
                'harga_satuan_terakhir' => 55000,
                'kondisi_alat' => null,
                'lokasi_penyimpanan' => 'Gudang Utama - Pallet A3',
                'catatan' => 'Menyerap gas amonia dan hidrogen sulfida pada dasar kolam tanah/terpal.'
            ],

            // PERALATAN & MESIN TAMBAK (Aset Operasional)
            [
                'kode_barang' => 'ALT-0001',
                'nama_barang' => 'Pompa Celup Submersible Showfou 2HP (3 Inch)',
                'kategori_barang' => 'peralatan',
                'tipe_saprotan' => null,
                'satuan' => 'unit',
                'stok_tersisa' => 2,
                'min_stok' => 1,
                'harga_satuan_terakhir' => 2450000,
                'kondisi_alat' => 'normal',
                'lokasi_penyimpanan' => 'Gudang Mesin - Rak D1',
                'catatan' => 'Pompa kuras cepat kolam pembesaran & sirkulasi air tawar debit tinggi.'
            ],
            [
                'kode_barang' => 'ALT-0002',
                'nama_barang' => 'Blower Aerator Diafragma Hiblow HP-100',
                'kategori_barang' => 'peralatan',
                'tipe_saprotan' => null,
                'satuan' => 'unit',
                'stok_tersisa' => 3,
                'min_stok' => 1,
                'harga_satuan_terakhir' => 3200000,
                'kondisi_alat' => 'normal',
                'lokasi_penyimpanan' => 'Gudang Mesin - Rak D2',
                'catatan' => 'Suplai oksigen terlarut 24 jam untuk hatchery penetasan telur & bioflok.'
            ],
            [
                'kode_barang' => 'ALT-0003',
                'nama_barang' => 'Timbangan Duduk Digital 150kg Stainless',
                'kategori_barang' => 'peralatan',
                'tipe_saprotan' => null,
                'satuan' => 'unit',
                'stok_tersisa' => 2,
                'min_stok' => 1,
                'harga_satuan_terakhir' => 850000,
                'kondisi_alat' => 'normal',
                'lokasi_penyimpanan' => 'Pos Penimbangan Panen',
                'catatan' => 'Timbangan presisi bersertifikat tera untuk grading & sampling biomassa panen.'
            ],
            [
                'kode_barang' => 'ALT-0004',
                'nama_barang' => 'DO Meter Pen Digital Milwaukee DO-600',
                'kategori_barang' => 'peralatan',
                'tipe_saprotan' => null,
                'satuan' => 'unit',
                'stok_tersisa' => 1,
                'min_stok' => 1,
                'harga_satuan_terakhir' => 1750000,
                'kondisi_alat' => 'normal',
                'lokasi_penyimpanan' => 'Laboratorium Kontrol Kualitas',
                'catatan' => 'Ukur kadar oksigen terlarut (mg/L) secara real-time pada kolam intensif.'
            ],
            [
                'kode_barang' => 'ALT-0005',
                'nama_barang' => 'pH Tester Digital Hanna Instruments HI98107',
                'kategori_barang' => 'peralatan',
                'tipe_saprotan' => null,
                'satuan' => 'unit',
                'stok_tersisa' => 2,
                'min_stok' => 1,
                'harga_satuan_terakhir' => 650000,
                'kondisi_alat' => 'perlu_servis',
                'lokasi_penyimpanan' => 'Laboratorium Kontrol Kualitas',
                'catatan' => 'Elektroda elektroda butuh kalibrasi cairan buffer 7.01 & 4.01.'
            ],
            [
                'kode_barang' => 'ALT-0006',
                'nama_barang' => 'Jaring Panen Tarik Nylon 30 Meter',
                'kategori_barang' => 'peralatan',
                'tipe_saprotan' => null,
                'satuan' => 'set',
                'stok_tersisa' => 1,
                'min_stok' => 1,
                'harga_satuan_terakhir' => 1200000,
                'kondisi_alat' => 'normal',
                'lokasi_penyimpanan' => 'Gudang Panen - Rak E1',
                'catatan' => 'Jaring kantong mata 1.5 inch dengan pelampung busa eva & timbel bawah.'
            ],
            [
                'kode_barang' => 'ALT-0007',
                'nama_barang' => 'Mesin Pencacah / Chopper Dedaunan Basah 6.5 HP',
                'kategori_barang' => 'peralatan',
                'tipe_saprotan' => null,
                'satuan' => 'unit',
                'stok_tersisa' => 1,
                'min_stok' => 1,
                'harga_satuan_terakhir' => 2850000,
                'kondisi_alat' => 'normal',
                'lokasi_penyimpanan' => 'Gudang Pakan & Pencacahan',
                'catatan' => 'Peralatan pakan SOP Bab 2 E untuk mencacah daun talas, kangkung, dan daun pepaya pakan campuran.'
            ],
            [
                'kode_barang' => 'ALT-0008',
                'nama_barang' => 'Drum Tangki Fiber Pengangkut Ikan Ber-Aerator 1000L',
                'kategori_barang' => 'peralatan',
                'tipe_saprotan' => null,
                'satuan' => 'unit',
                'stok_tersisa' => 2,
                'min_stok' => 1,
                'harga_satuan_terakhir' => 3800000,
                'kondisi_alat' => 'normal',
                'lokasi_penyimpanan' => 'Pool Armada Logistik',
                'catatan' => 'Peralatan distribusi SOP Bab 2 E untuk pengiriman ikan hidup sistem DDP bebas kematian massal.'
            ],
            [
                'kode_barang' => 'ALT-0009',
                'nama_barang' => 'Tabung Silinder Oksigen Murni 6m³ + Regulator Medis',
                'kategori_barang' => 'peralatan',
                'tipe_saprotan' => null,
                'satuan' => 'tabung',
                'stok_tersisa' => 3,
                'min_stok' => 1,
                'harga_satuan_terakhir' => 1450000,
                'kondisi_alat' => 'normal',
                'lokasi_penyimpanan' => 'Gudang Packing & Pengemasan',
                'catatan' => 'Peralatan SOP Bab 2 E untuk aerasi packing kantong plastik beroksigen kirim jarak jauh.'
            ]
        ];

        foreach ($items as $data) {
            $barang = InventarisGudang::updateOrCreate(
                ['kode_barang' => $data['kode_barang']],
                $data
            );

            // Log Mutasi Masuk Awal
            $noRef = 'TRX-GDG-IN-' . date('Ym') . '-' . str_pad($barang->id_barang, 4, '0', STR_PAD_LEFT);
            $totalBiaya = $barang->stok_tersisa * $barang->harga_satuan_terakhir;

            LogTransaksiGudang::firstOrCreate(
                ['no_referensi' => $noRef],
                [
                    'jenis_mutasi' => 'masuk_pembelian',
                    'id_barang' => $barang->id_barang,
                    'jumlah' => $barang->stok_tersisa,
                    'harga_satuan' => $barang->harga_satuan_terakhir,
                    'total_biaya' => $totalBiaya,
                    'id_user' => $userId,
                    'tanggal_mutasi' => now()->subDays(rand(5, 20))->toDateString(),
                    'keterangan' => 'Pengadaan stok awal: ' . $barang->nama_barang
                ]
            );

            // Auto-post Keuangan untuk barang berharga > 0
            if ($totalBiaya > 0) {
                Keuangan::firstOrCreate(
                    ['ref_id' => $noRef],
                    [
                        'id_user' => $userId,
                        'id_kolam' => null,
                        'tanggal_transaksi' => now()->subDays(rand(5, 20))->toDateString(),
                        'tipe_transaksi' => 'pengeluaran',
                        'kategori' => $barang->kategori_barang === 'saprotan' ? 'Pengadaan Saprotan' : 'Peralatan Tambak',
                        'nominal' => $totalBiaya,
                        'keterangan' => 'Pengadaan Gudang: ' . $barang->nama_barang . ' (' . $barang->stok_tersisa . ' ' . $barang->satuan . ')',
                    ]
                );
            }
        }

        // Buat 2 contoh Log Pemakaian Barang Keluar untuk Kolam
        $kapur = InventarisGudang::where('kode_barang', 'SPT-0001')->first();
        if ($kapur && $kolam1) {
            LogTransaksiGudang::firstOrCreate(
                ['no_referensi' => 'TRX-GDG-OUT-' . date('Ym') . '-0001'],
                [
                    'jenis_mutasi' => 'keluar_pemakaian_kolam',
                    'id_barang' => $kapur->id_barang,
                    'jumlah' => 2,
                    'harga_satuan' => $kapur->harga_satuan_terakhir,
                    'total_biaya' => 2 * $kapur->harga_satuan_terakhir,
                    'id_kolam' => $kolam1->id_kolam,
                    'id_user' => $userId,
                    'tanggal_mutasi' => now()->subDays(2)->toDateString(),
                    'keterangan' => 'Pengapuran dasar kolam dan penstabil pH pasca hujan deras untuk ' . $kolam1->nama_kolam
                ]
            );
        }

        $probiotik = InventarisGudang::where('kode_barang', 'SPT-0003')->first();
        if ($probiotik && $kolam2) {
            LogTransaksiGudang::firstOrCreate(
                ['no_referensi' => 'TRX-GDG-OUT-' . date('Ym') . '-0002'],
                [
                    'jenis_mutasi' => 'keluar_pemakaian_kolam',
                    'id_barang' => $probiotik->id_barang,
                    'jumlah' => 1,
                    'harga_satuan' => $probiotik->harga_satuan_terakhir,
                    'total_biaya' => 1 * $probiotik->harga_satuan_terakhir,
                    'id_kolam' => $kolam2->id_kolam,
                    'id_user' => $userId,
                    'tanggal_mutasi' => now()->subDays(1)->toDateString(),
                    'keterangan' => 'Aplikasi probiotik mingguan pemeliharaan kualitas air flok untuk ' . $kolam2->nama_kolam
                ]
            );
        }
    }
}

<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Keuangan;
use App\Models\Kolam;
use App\Models\User;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class KeuanganSeeder extends Seeder
{
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();
        DB::table('keuangan')->truncate();
        Schema::enableForeignKeyConstraints();

        $manajer = User::where('role', 'manajer')->first() ?? User::first();
        $userId = $manajer ? $manajer->id_user : 1;

        $kolamPembesaran = Kolam::where('nama_kolam', 'like', '%Pembesaran%')->first();

        // 1 Kondisi 1 Contoh untuk setiap tipe_transaksi ('pemasukan', 'pengeluaran')
        $transaksiKeuangan = [
            // 1. Kondisi Tipe: 'pemasukan' (Penjualan panen ikan)
            [
                'id_user'           => $userId,
                'id_kolam'          => $kolamPembesaran ? $kolamPembesaran->id_kolam : null,
                'tanggal_transaksi' => now()->subDays(2)->toDateString(),
                'tipe_transaksi'    => 'pemasukan',
                'kategori'          => 'Penjualan Panen Ikan Nila',
                'nominal'           => 16000000.00,
                'keterangan'        => 'Pelunasan penjualan panen total ikan nila ke Pasar Cikurubuk (500 kg @ Rp 32.000)',
                'ref_id'            => 'TRX-IN-001',
            ],
            // 2. Kondisi Tipe: 'pengeluaran' (Pembelian inventori pakan komersial)
            [
                'id_user'           => $userId,
                'id_kolam'          => $kolamPembesaran ? $kolamPembesaran->id_kolam : null,
                'tanggal_transaksi' => now()->subDays(5)->toDateString(),
                'tipe_transaksi'    => 'pengeluaran',
                'kategori'          => 'Pembelian Pakan Pelet',
                'nominal'           => 3125000.00,
                'keterangan'        => 'Pengadaan pelet apung grower 781-2 (250 kg @ Rp 12.500) dari CV Sentosa Pakan Mandiri',
                'ref_id'            => 'TRX-OUT-001',
            ],
        ];

        foreach ($transaksiKeuangan as $keu) {
            Keuangan::create($keu);
        }

        // Catat Biaya Investasi Kolam ke Laporan Keuangan (Anti Pengeluaran Gaib)
        $kolamWithCost = Kolam::where('biaya_pembuatan', '>', 0)->get();
        foreach ($kolamWithCost as $k) {
            Keuangan::create([
                'id_user'           => $userId,
                'id_kolam'          => $k->id_kolam,
                'tanggal_transaksi' => now()->subDays(60)->toDateString(),
                'tipe_transaksi'    => 'pengeluaran',
                'kategori'          => 'Pembangunan Kolam',
                'nominal'           => (float) $k->biaya_pembuatan,
                'keterangan'        => "Biaya pembuatan/investasi kolam {$k->nama_kolam} ({$k->tipe_kolam})",
                'ref_id'            => 'KOLAM-INV-' . str_pad($k->id_kolam, 4, '0', STR_PAD_LEFT),
            ]);
        }
    }
}

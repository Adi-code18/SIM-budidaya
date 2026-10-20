<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Bersihkan data lama pada tabel operasional (TANPA menyentuh tabel users)
        Schema::disableForeignKeyConstraints();
        if (Schema::hasTable('landing_settings')) {
            DB::table('landing_settings')->truncate();
        }
        if (Schema::hasTable('audit_logs')) {
            DB::table('audit_logs')->truncate();
        }
        if (Schema::hasTable('pembelian_pakan')) {
            DB::table('pembelian_pakan')->truncate();
        }
        if (Schema::hasTable('sampling_pembesaran')) {
            DB::table('sampling_pembesaran')->truncate();
        }
        if (Schema::hasTable('log_kualitas_air')) {
            DB::table('log_kualitas_air')->truncate();
        }
        if (Schema::hasTable('log_transaksi_gudang')) {
            DB::table('log_transaksi_gudang')->truncate();
        }
        if (Schema::hasTable('inventaris_gudang')) {
            DB::table('inventaris_gudang')->truncate();
        }
        DB::table('transaksi_distribusi')->truncate();
        DB::table('keuangan')->truncate();
        DB::table('manajemen_pakan')->truncate();
        DB::table('batch_pembesaran')->truncate();
        DB::table('batch_pembibitan')->truncate();
        DB::table('mitra_distributor')->truncate();
        DB::table('stok_pakan')->truncate();
        DB::table('kolam')->truncate();
        DB::table('ikan')->truncate();
        Schema::enableForeignKeyConstraints();

        $this->call([
            UserSeeder::class, // Seeder user tetap utuh dan tidak diotak-atik
            LandingSettingSeeder::class,
            KolamSeeder::class,
            IkanSeeder::class,
            StokPakanSeeder::class,
            InventarisGudangSeeder::class,
            BatchPembibitanSeeder::class,
            BatchPembesaranSeeder::class,
            ManajemenPakanSeeder::class,
            SamplingPembesaranSeeder::class,
            LogKualitasAirSeeder::class,
            MitraDistributorSeeder::class,
            TransaksiDistribusiSeeder::class,
            KeuanganSeeder::class,
        ]);
    }
}

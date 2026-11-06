<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('transaksi_distribusi', function (Blueprint $table) {
            $table->string('metode_pembayaran', 30)->default('tunai')->after('harga_total');
            $table->string('status_pembayaran', 30)->default('lunas')->after('metode_pembayaran');
            $table->integer('termin_hari')->default(0)->after('status_pembayaran');
            $table->date('tgl_jatuh_tempo')->nullable()->after('termin_hari');
            $table->decimal('toleransi_susut_persen', 5, 2)->default(2.00)->after('mortalitas_antar_kg');
            $table->decimal('susut_diluar_toleransi_kg', 8, 2)->default(0.00)->after('toleransi_susut_persen');
            $table->decimal('potongan_klaim_rp', 15, 2)->default(0.00)->after('susut_diluar_toleransi_kg');
            $table->decimal('total_tagihan_akhir', 15, 2)->default(0.00)->after('potongan_klaim_rp');
            $table->decimal('jumlah_terbayar', 15, 2)->default(0.00)->after('total_tagihan_akhir');
            $table->date('tgl_lunas')->nullable()->after('jumlah_terbayar');
            $table->text('catatan_klaim')->nullable()->after('tgl_lunas');
        });

        Schema::table('mitra_distributor', function (Blueprint $table) {
            $table->decimal('plafon_kredit', 15, 2)->default(20000000.00)->after('alamat');
            $table->integer('termin_default_hari')->default(14)->after('plafon_kredit');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transaksi_distribusi', function (Blueprint $table) {
            $table->dropColumn([
                'metode_pembayaran',
                'status_pembayaran',
                'termin_hari',
                'tgl_jatuh_tempo',
                'toleransi_susut_persen',
                'susut_diluar_toleransi_kg',
                'potongan_klaim_rp',
                'total_tagihan_akhir',
                'jumlah_terbayar',
                'tgl_lunas',
                'catatan_klaim',
            ]);
        });

        Schema::table('mitra_distributor', function (Blueprint $table) {
            $table->dropColumn([
                'plafon_kredit',
                'termin_default_hari',
            ]);
        });
    }
};


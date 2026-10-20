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
        Schema::create('inventaris_gudang', function (Blueprint $table) {
            $table->id('id_barang');
            $table->string('kode_barang', 50)->unique();
            $table->string('nama_barang', 150);
            $table->enum('kategori_barang', ['saprotan', 'peralatan'])->default('saprotan');
            $table->string('tipe_saprotan', 60)->nullable();
            $table->string('satuan', 30)->default('unit');
            $table->decimal('stok_tersisa', 12, 2)->default(0.00);
            $table->decimal('min_stok', 12, 2)->default(5.00);
            $table->decimal('harga_satuan_terakhir', 15, 2)->default(0.00);
            $table->enum('kondisi_alat', ['normal', 'perlu_servis', 'rusak'])->nullable();
            $table->string('lokasi_penyimpanan', 100)->nullable();
            $table->text('catatan')->nullable();
            $table->timestamps();
        });

        Schema::create('log_transaksi_gudang', function (Blueprint $table) {
            $table->id('id_transaksi_gudang');
            $table->string('no_referensi', 60)->unique();
            $table->enum('jenis_mutasi', ['masuk_pembelian', 'keluar_pemakaian_kolam', 'penyesuaian_rusak'])->default('masuk_pembelian');
            $table->foreignId('id_barang')->constrained('inventaris_gudang', 'id_barang')->onDelete('cascade');
            $table->decimal('jumlah', 12, 2)->default(1.00);
            $table->decimal('harga_satuan', 15, 2)->default(0.00);
            $table->decimal('total_biaya', 15, 2)->default(0.00);
            $table->foreignId('id_kolam')->nullable()->constrained('kolam', 'id_kolam')->onDelete('set null');
            $table->foreignId('id_user')->nullable()->constrained('users', 'id_user')->onDelete('set null');
            $table->date('tanggal_mutasi');
            $table->string('keterangan', 255)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('log_transaksi_gudang');
        Schema::dropIfExists('inventaris_gudang');
    }
};

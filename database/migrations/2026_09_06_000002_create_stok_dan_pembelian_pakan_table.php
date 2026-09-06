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
        // 1. Tabel Master Stok & Inventori Pakan
        Schema::create('stok_pakan', function (Blueprint $table) {
            $table->id('id_stok_pakan');
            $table->string('nama_pakan'); // Contoh: Cacing Sutra, Artemia, Pelet 781-1, Dedaunan Organik
            $table->enum('kategori_peruntukan', ['pembibitan', 'pembesaran', 'semua'])->default('pembesaran');
            $table->string('satuan')->default('kg'); // kg, sak, tray, liter
            $table->decimal('stok_tersisa', 10, 2)->default(0);
            $table->decimal('batas_minimum', 10, 2)->default(10); // Threshold alert restock
            $table->decimal('harga_per_satuan', 12, 2)->default(0); // Harga referensi per kg/sak
            $table->string('keterangan')->nullable();
            $table->timestamps();
        });

        // 2. Tabel Transaksi Pembelian Pakan dari Mitra Supplier
        Schema::create('pembelian_pakan', function (Blueprint $table) {
            $table->id('id_pembelian');
            $table->foreignId('id_stok_pakan')->constrained('stok_pakan', 'id_stok_pakan')->onDelete('cascade');
            $table->foreignId('id_mitra')->nullable()->constrained('mitra_distributor', 'id_mitra')->onDelete('set null');
            $table->foreignId('id_user')->constrained('users', 'id_user')->onDelete('cascade');
            $table->date('tgl_beli');
            $table->decimal('jumlah', 10, 2); // Jumlah kg/sak yang dibeli
            $table->decimal('harga_satuan', 12, 2);
            $table->decimal('total_biaya', 14, 2);
            $table->string('no_nota')->nullable();
            $table->text('catatan')->nullable();
            $table->timestamps();
        });

        // 3. Tambahkan kolom relasi ke manajemen_pakan jika belum ada
        if (Schema::hasTable('manajemen_pakan')) {
            Schema::table('manajemen_pakan', function (Blueprint $table) {
                if (!Schema::hasColumn('manajemen_pakan', 'id_stok_pakan')) {
                    $table->foreignId('id_stok_pakan')->nullable()->after('id_kolam')->constrained('stok_pakan', 'id_stok_pakan')->onDelete('set null');
                }
                if (!Schema::hasColumn('manajemen_pakan', 'kategori_fase')) {
                    $table->enum('kategori_fase', ['pembibitan', 'pembesaran'])->default('pembesaran')->after('id_stok_pakan');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('manajemen_pakan')) {
            Schema::table('manajemen_pakan', function (Blueprint $table) {
                if (Schema::hasColumn('manajemen_pakan', 'id_stok_pakan')) {
                    $table->dropForeign(['id_stok_pakan']);
                    $table->dropColumn('id_stok_pakan');
                }
                if (Schema::hasColumn('manajemen_pakan', 'kategori_fase')) {
                    $table->dropColumn('kategori_fase');
                }
            });
        }

        Schema::dropIfExists('pembelian_pakan');
        Schema::dropIfExists('stok_pakan');
    }
};

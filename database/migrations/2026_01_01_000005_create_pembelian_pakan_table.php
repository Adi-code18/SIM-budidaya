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
        Schema::create('pembelian_pakan', function (Blueprint $table) {
            $table->id('id_pembelian');
            $table->foreignId('id_stok_pakan')->constrained('stok_pakan', 'id_stok_pakan')->onDelete('cascade');
            $table->foreignId('id_mitra')->nullable()->constrained('mitra_distributor', 'id_mitra')->onDelete('set null');
            $table->foreignId('id_user')->constrained('users', 'id_user')->onDelete('cascade');
            $table->date('tgl_beli');
            $table->decimal('jumlah', 10, 2);
            $table->decimal('harga_satuan', 12, 2);
            $table->decimal('total_biaya', 14, 2);
            $table->string('no_nota')->nullable();
            $table->text('catatan')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pembelian_pakan');
    }
};

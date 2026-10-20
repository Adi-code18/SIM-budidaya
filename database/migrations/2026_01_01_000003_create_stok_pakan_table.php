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
        Schema::create('stok_pakan', function (Blueprint $table) {
            $table->id('id_stok_pakan');
            $table->string('nama_pakan');
            $table->enum('kategori_peruntukan', ['pembibitan', 'pembesaran', 'semua'])->default('pembesaran');
            $table->string('satuan')->default('kg');
            $table->decimal('stok_tersisa', 10, 2)->default(0.00);
            $table->decimal('batas_minimum', 10, 2)->default(10.00);
            $table->decimal('harga_per_satuan', 12, 2)->default(0.00);
            $table->string('keterangan')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stok_pakan');
    }
};

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
        Schema::create('transaksi_distribusi', function (Blueprint $table) {
            $table->id('id_transaksi');
            $table->foreignId('id_user')->constrained('users', 'id_user')->onDelete('cascade');
            $table->foreignId('id_mitra')->constrained('mitra_distributor', 'id_mitra')->onDelete('cascade');
            $table->date('tanggal_order');
            $table->decimal('Total_kg', 10, 2);
            $table->decimal('harga_total', 15, 2);
            $table->string('status_order')->default('pending');
            $table->string('Jenis_order');
            $table->string('Bukti_sampai')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transaksi_distribusi');
    }
};

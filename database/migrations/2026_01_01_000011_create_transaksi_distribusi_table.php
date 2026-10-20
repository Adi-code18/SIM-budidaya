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
            $table->string('no_surat_jalan', 50)->nullable()->unique();
            $table->foreignId('id_user')->constrained('users', 'id_user')->onDelete('cascade');
            $table->foreignId('id_mitra')->constrained('mitra_distributor', 'id_mitra')->onDelete('cascade');
            $table->foreignId('id_pembesaran')->constrained('batch_pembesaran', 'id_pembesaran')->onDelete('cascade');
            $table->date('tanggal_order');
            $table->date('tgl_rencana_kirim')->nullable();
            $table->string('tipe_panen', 30)->default('parsial');
            $table->decimal('Total_kg', 10, 2);
            $table->decimal('berat_aktual_kg', 10, 2)->nullable();
            $table->decimal('susut_bobot_kg', 8, 2)->default(0.00);
            $table->decimal('mortalitas_antar_kg', 8, 2)->default(0.00);
            $table->decimal('harga_total', 15, 2);
            $table->string('status_order')->default('pending');
            $table->dateTime('waktu_mulai_pemberokan')->nullable();
            $table->dateTime('waktu_selesai_pemberokan')->nullable();
            $table->integer('durasi_pemberokan_jam')->default(18);
            $table->string('grading_size', 100)->nullable();
            $table->text('catatan_grading')->nullable();
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

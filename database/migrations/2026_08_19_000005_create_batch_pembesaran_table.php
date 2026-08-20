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
        Schema::create('batch_pembesaran', function (Blueprint $table) {
            $table->id('id_pembesaran');
            $table->foreignId('id_kolam')->constrained('kolam', 'id_kolam')->onDelete('cascade');
            $table->foreignId('id_user')->constrained('users', 'id_user')->onDelete('cascade');
            $table->date('tgl_tebar');
            $table->decimal('biomassa_est', 8, 2)->default(0);
            $table->decimal('fcr', 4, 2)->nullable();
            $table->decimal('target_panen_kg', 8, 2)->default(0);
            $table->decimal('jumlah_panen_kg', 8, 2)->default(0);
            $table->string('jenis_ikan');
            $table->string('status_siklus')->default('berjalan');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('batch_pembesaran');
    }
};

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
        Schema::create('batch_pembibitan', function (Blueprint $table) {
            $table->id('id_batch');
            $table->foreignId('id_kolam')->constrained('kolam', 'id_kolam')->onDelete('cascade');
            $table->foreignId('id_user')->constrained('users', 'id_user')->onDelete('cascade');
            $table->foreignId('id_ikan')->nullable()->constrained('ikan', 'id_ikan')->onDelete('set null');
            $table->date('tgl_pemijahan');
            $table->integer('jumlah_bibitAwal');
            $table->string('jenis_ikan')->nullable();
            $table->string('fase_pertumbuhan')->default('TELUR');
            $table->integer('jumlah_kematian')->default(0);
            $table->decimal('total_bobot_kg', 8, 2)->default(0);
            $table->string('status')->default('aktif');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('batch_pembibitan');
    }
};

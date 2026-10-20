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
        Schema::create('sampling_pembesaran', function (Blueprint $table) {
            $table->id('id_sampling');
            $table->foreignId('id_pembesaran')->constrained('batch_pembesaran', 'id_pembesaran')->onDelete('cascade');
            $table->foreignId('id_user')->constrained('users', 'id_user')->onDelete('cascade');
            $table->date('tgl_sampling');
            $table->integer('jumlah_sample_ekor')->default(20);
            $table->decimal('total_bobot_sample_gr', 10, 2);
            $table->decimal('abw_gram', 8, 2);
            $table->decimal('biomassa_koreksi_kg', 10, 2)->nullable();
            $table->text('catatan')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sampling_pembesaran');
    }
};

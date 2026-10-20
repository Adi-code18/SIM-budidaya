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
        Schema::create('log_kualitas_air', function (Blueprint $table) {
            $table->id('id_log_air');
            $table->foreignId('id_kolam')->constrained('kolam', 'id_kolam')->onDelete('cascade');
            $table->foreignId('id_user')->constrained('users', 'id_user')->onDelete('cascade');
            $table->dateTime('tgl_catat');
            $table->decimal('suhu_air', 4, 1)->nullable();
            $table->decimal('ph_air', 3, 1)->nullable();
            $table->string('kondisi_air', 50)->default('normal');
            $table->text('catatan')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('log_kualitas_air');
    }
};

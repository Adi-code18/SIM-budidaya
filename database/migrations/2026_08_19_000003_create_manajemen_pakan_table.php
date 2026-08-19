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
        Schema::create('manajemen_pakan', function (Blueprint $table) {
            $table->id('id_pakan');
            $table->foreignId('id_user')->constrained('users', 'id_user')->onDelete('cascade');
            $table->foreignId('id_kolam')->constrained('kolam', 'id_kolam')->onDelete('cascade');
            $table->date('tgl_log');
            $table->decimal('kg_pelet', 8, 2)->default(0);
            $table->decimal('kg_daun', 8, 2)->default(0);
            $table->string('jenis_daun')->nullable();
            $table->decimal('total_biaya', 12, 2)->default(0);
            $table->decimal('ph_air', 4, 2)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('manajemen_pakan');
    }
};

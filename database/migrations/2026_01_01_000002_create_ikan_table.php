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
        Schema::create('ikan', function (Blueprint $table) {
            $table->id('id_ikan');
            $table->string('nama_ikan');
            $table->integer('durasi_penetasan')->default(3)->comment('Durasi masa penetasan dalam hari');
            $table->integer('durasi_pembibitan')->default(21)->comment('Durasi masa pembibitan dalam hari');
            $table->decimal('fcr_min', 4, 2)->default(1.00)->comment('Batas bawah FCR ideal');
            $table->decimal('fcr_max', 4, 2)->default(1.40)->comment('Batas atas FCR ideal');
            $table->decimal('bulan_panen_min', 4, 1)->nullable()->comment('Siklus panen min (bulan)');
            $table->decimal('bulan_panen_max', 4, 1)->nullable()->comment('Siklus panen max (bulan)');
            $table->string('target_konsumsi', 50)->nullable()->comment('Target konsumsi misal 8-10 ekor / kg');
            $table->string('jenis_pakan_didukung', 100)->nullable()->comment('Rekomendasi jenis pakan');
            $table->decimal('harga_jual_kg', 12, 2)->default(0.00)->comment('Harga pasar/jual acuan per kg (Rp)');
            $table->string('foto')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ikan');
    }
};

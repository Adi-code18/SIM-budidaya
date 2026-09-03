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

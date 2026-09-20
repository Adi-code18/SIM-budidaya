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
        Schema::table('mitra_distributor', function (Blueprint $table) {
            $table->string('logo_mitra')->nullable()->after('tipe_mitra')->comment('Path atau file logo/gambar mitra');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('mitra_distributor', function (Blueprint $table) {
            $table->dropColumn('logo_mitra');
        });
    }
};

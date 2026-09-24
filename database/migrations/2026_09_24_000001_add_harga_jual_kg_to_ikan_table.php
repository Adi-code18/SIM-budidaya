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
        Schema::table('ikan', function (Blueprint $table) {
            if (!Schema::hasColumn('ikan', 'harga_jual_kg')) {
                $table->decimal('harga_jual_kg', 12, 2)->default(0)->after('jenis_pakan_didukung')->comment('Harga pasar/jual acuan per kg (Rp)');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ikan', function (Blueprint $table) {
            if (Schema::hasColumn('ikan', 'harga_jual_kg')) {
                $table->dropColumn('harga_jual_kg');
            }
        });
    }
};

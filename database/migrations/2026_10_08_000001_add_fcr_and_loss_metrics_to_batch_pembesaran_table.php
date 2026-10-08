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
        Schema::table('batch_pembesaran', function (Blueprint $table) {
            if (!Schema::hasColumn('batch_pembesaran', 'jumlah_tebar_ekor')) {
                $table->integer('jumlah_tebar_ekor')->default(0)->nullable()->after('asal_bibit');
            }
            if (!Schema::hasColumn('batch_pembesaran', 'biomassa_awal_kg')) {
                $table->decimal('biomassa_awal_kg', 8, 2)->default(0)->nullable()->after('jumlah_tebar_ekor');
            }
            if (!Schema::hasColumn('batch_pembesaran', 'jumlah_panen_ekor')) {
                $table->integer('jumlah_panen_ekor')->default(0)->nullable()->after('jumlah_panen_kg');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('batch_pembesaran', function (Blueprint $table) {
            $table->dropColumn(['jumlah_tebar_ekor', 'biomassa_awal_kg', 'jumlah_panen_ekor']);
        });
    }
};

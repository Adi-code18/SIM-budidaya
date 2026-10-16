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
        Schema::table('batch_pembibitan', function (Blueprint $table) {
            if (!Schema::hasColumn('batch_pembibitan', 'jumlah_telur')) {
                $table->integer('jumlah_telur')->nullable()->after('est_prcs_pembibitaan');
            }
            if (!Schema::hasColumn('batch_pembibitan', 'jumlah_menetas')) {
                $table->integer('jumlah_menetas')->nullable()->after('jumlah_telur');
            }
            if (!Schema::hasColumn('batch_pembibitan', 'survival_rate')) {
                $table->decimal('survival_rate', 5, 2)->nullable()->after('jumlah_menetas');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('batch_pembibitan', function (Blueprint $table) {
            $table->dropColumn(['jumlah_telur', 'jumlah_menetas', 'survival_rate']);
        });
    }
};

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
            if (!Schema::hasColumn('batch_pembesaran', 'est_tgl_panen')) {
                $table->date('est_tgl_panen')->nullable()->after('tgl_tebar');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('batch_pembesaran', function (Blueprint $table) {
            if (Schema::hasColumn('batch_pembesaran', 'est_tgl_panen')) {
                $table->dropColumn('est_tgl_panen');
            }
        });
    }
};

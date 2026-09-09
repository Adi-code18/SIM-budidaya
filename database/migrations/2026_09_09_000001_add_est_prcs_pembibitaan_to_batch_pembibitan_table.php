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
            if (!Schema::hasColumn('batch_pembibitan', 'est_prcs_pembibitaan')) {
                $table->date('est_prcs_pembibitaan')->nullable()->after('tgl_pemijahan');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('batch_pembibitan', function (Blueprint $table) {
            if (Schema::hasColumn('batch_pembibitan', 'est_prcs_pembibitaan')) {
                $table->dropColumn('est_prcs_pembibitaan');
            }
        });
    }
};

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
            if (!Schema::hasColumn('batch_pembesaran', 'asal_bibit')) {
                $table->string('asal_bibit', 50)->default('pembibitan_sendiri')->after('id_batch_pembibitan');
            }
            if (!Schema::hasColumn('batch_pembesaran', 'biaya_beli_bibit')) {
                $table->decimal('biaya_beli_bibit', 15, 2)->default(0)->after('asal_bibit');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('batch_pembesaran', function (Blueprint $table) {
            if (Schema::hasColumn('batch_pembesaran', 'asal_bibit')) {
                $table->dropColumn('asal_bibit');
            }
            if (Schema::hasColumn('batch_pembesaran', 'biaya_beli_bibit')) {
                $table->dropColumn('biaya_beli_bibit');
            }
        });
    }
};

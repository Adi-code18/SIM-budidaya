<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('batch_pembibitan', function (Blueprint $table) {
            if (!Schema::hasColumn('batch_pembibitan', 'total_bobot_kg')) {
                $table->decimal('total_bobot_kg', 10, 2)->default(0.00)->after('jumlah_kematian');
            }
        });
    }

    public function down(): void
    {
        Schema::table('batch_pembibitan', function (Blueprint $table) {
            if (Schema::hasColumn('batch_pembibitan', 'total_bobot_kg')) {
                $table->dropColumn('total_bobot_kg');
            }
        });
    }
};

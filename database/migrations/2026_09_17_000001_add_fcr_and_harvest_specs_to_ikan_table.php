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
            $table->decimal('fcr_min', 4, 2)->default(1.00)->after('durasi_pembibitan')->comment('Batas bawah FCR ideal');
            $table->decimal('fcr_max', 4, 2)->default(1.40)->after('fcr_min')->comment('Batas atas FCR ideal');
            $table->decimal('bulan_panen_min', 4, 1)->nullable()->after('fcr_max')->comment('Siklus panen min (bulan)');
            $table->decimal('bulan_panen_max', 4, 1)->nullable()->after('bulan_panen_min')->comment('Siklus panen max (bulan)');
            $table->string('target_konsumsi', 50)->nullable()->after('bulan_panen_max')->comment('Target konsumsi misal 8-10 ekor / kg');
            $table->string('jenis_pakan_didukung', 100)->nullable()->after('target_konsumsi')->comment('Rekomendasi jenis pakan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ikan', function (Blueprint $table) {
            $table->dropColumn([
                'fcr_min',
                'fcr_max',
                'bulan_panen_min',
                'bulan_panen_max',
                'target_konsumsi',
                'jenis_pakan_didukung'
            ]);
        });
    }
};

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
            if (!Schema::hasColumn('batch_pembibitan', 'fase_pertumbuhan')) {
                $table->string('fase_pertumbuhan')->default('TELUR')->after('jenis_ikan');
            }
        });

        Schema::table('batch_pembesaran', function (Blueprint $table) {
            if (!Schema::hasColumn('batch_pembesaran', 'id_batch_pembibitan')) {
                $table->foreignId('id_batch_pembibitan')->nullable()->after('id_user')->constrained('batch_pembibitan', 'id_batch')->nullOnDelete();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('batch_pembesaran', function (Blueprint $table) {
            if (Schema::hasColumn('batch_pembesaran', 'id_batch_pembibitan')) {
                $table->dropForeign(['id_batch_pembibitan']);
                $table->dropColumn('id_batch_pembibitan');
            }
        });

        Schema::table('batch_pembibitan', function (Blueprint $table) {
            if (Schema::hasColumn('batch_pembibitan', 'fase_pertumbuhan')) {
                $table->dropColumn('fase_pertumbuhan');
            }
        });
    }
};

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
        if (!Schema::hasColumn('users', 'last_session_id')) {
            Schema::table('users', function (Blueprint $table) {
                $table->string('last_session_id')->nullable()->after('two_factor_confirmed_at');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('users', 'last_session_id')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('last_session_id');
            });
        }
    }
};

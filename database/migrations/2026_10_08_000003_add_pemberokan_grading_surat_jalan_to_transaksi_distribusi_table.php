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
        Schema::table('transaksi_distribusi', function (Blueprint $table) {
            if (!Schema::hasColumn('transaksi_distribusi', 'no_surat_jalan')) {
                $table->string('no_surat_jalan', 50)->nullable()->unique()->after('id_transaksi');
            }
            if (!Schema::hasColumn('transaksi_distribusi', 'waktu_mulai_pemberokan')) {
                $table->dateTime('waktu_mulai_pemberokan')->nullable()->after('status_order');
            }
            if (!Schema::hasColumn('transaksi_distribusi', 'waktu_selesai_pemberokan')) {
                $table->dateTime('waktu_selesai_pemberokan')->nullable()->after('waktu_mulai_pemberokan');
            }
            if (!Schema::hasColumn('transaksi_distribusi', 'durasi_pemberokan_jam')) {
                $table->integer('durasi_pemberokan_jam')->default(18)->after('waktu_selesai_pemberokan');
            }
            if (!Schema::hasColumn('transaksi_distribusi', 'grading_size')) {
                $table->string('grading_size', 100)->nullable()->after('durasi_pemberokan_jam');
            }
            if (!Schema::hasColumn('transaksi_distribusi', 'catatan_grading')) {
                $table->text('catatan_grading')->nullable()->after('grading_size');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transaksi_distribusi', function (Blueprint $table) {
            $table->dropColumn([
                'no_surat_jalan',
                'waktu_mulai_pemberokan',
                'waktu_selesai_pemberokan',
                'durasi_pemberokan_jam',
                'grading_size',
                'catatan_grading',
            ]);
        });
    }
};

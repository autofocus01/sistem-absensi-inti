<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('attendance_import_errors', function (Blueprint $table) {
            // 'rekap_bulanan' -> dari import:absensi (Excel rekap bulanan), bisa dikoreksi lewat form Rekap Absensi.
            // 'absensi_harian' -> dari import:absensi-harian (log fingerprint/Face ID), belum ada form koreksi manual.
            $table->enum('jenis', ['rekap_bulanan', 'absensi_harian'])->default('rekap_bulanan')->after('bulan');
        });
    }

    public function down(): void
    {
        Schema::table('attendance_import_errors', function (Blueprint $table) {
            $table->dropColumn('jenis');
        });
    }
};
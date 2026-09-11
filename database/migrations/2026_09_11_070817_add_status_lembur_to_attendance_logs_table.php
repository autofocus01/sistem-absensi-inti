<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('attendance_logs', function (Blueprint $table) {
            $table->enum('status_lembur', ['pending', 'disetujui', 'ditolak'])->default('pending')->after('jumlah_scan');
            $table->foreignId('disetujui_oleh')->nullable()->after('status_lembur')->constrained('users')->nullOnDelete();
            $table->timestamp('disetujui_pada')->nullable()->after('disetujui_oleh');
        });
    }

    public function down(): void
    {
        Schema::table('attendance_logs', function (Blueprint $table) {
            $table->dropConstrainedForeignId('disetujui_oleh');
            $table->dropColumn(['status_lembur', 'disetujui_pada']);
        });
    }
};
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('attendance_import_errors', function (Blueprint $table) {
            $table->id();
            $table->string('nipeg')->nullable();
            $table->string('nama')->nullable();
            $table->unsignedSmallInteger('tahun');
            $table->unsignedTinyInteger('bulan');
            $table->unsignedInteger('baris_excel')->nullable();
            $table->text('alasan');           // pesan exception dari validasi (lihat AttendanceRecap::booted())
            $table->json('data_mentah');      // snapshot semua kolom dari Excel, buat ditinjau/dikoreksi manual
            $table->enum('status', ['pending', 'selesai'])->default('pending');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attendance_import_errors');
    }
};
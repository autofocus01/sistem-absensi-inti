<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('attendance_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained()->cascadeOnDelete();

            $table->date('tanggal');
            $table->time('jam_masuk')->nullable();
            $table->time('jam_pulang')->nullable();

            // Asal data: mesin_fingerprint, face_id, atau manual (input HR).
            $table->string('sumber')->default('mesin_fingerprint');

            // Berapa kali tap tercatat hari itu (audit trail, bukan untuk kalkulasi).
            $table->unsignedInteger('jumlah_scan')->default(0);

            $table->timestamps();

            $table->unique(['employee_id', 'tanggal']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attendance_logs');
    }
};

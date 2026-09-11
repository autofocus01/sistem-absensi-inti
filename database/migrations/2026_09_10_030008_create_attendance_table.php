<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('attendance_recaps', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained()->cascadeOnDelete();

            $table->unsignedSmallInteger('tahun');
            $table->unsignedTinyInteger('bulan');

            $table->unsignedTinyInteger('hari_kerja');
            $table->unsignedTinyInteger('hadir')->default(0);
            $table->unsignedTinyInteger('perdin')->default(0);
            $table->unsignedTinyInteger('cuti')->default(0);
            $table->unsignedTinyInteger('sakit')->default(0);
            $table->unsignedTinyInteger('ijin')->default(0);
            $table->unsignedTinyInteger('alpha')->default(0);

            $table->unsignedTinyInteger('telat_hari')->default(0);
            $table->unsignedInteger('menit_telat')->default(0);

            $table->decimal('persen_kehadiran', 5, 1)->default(0);

            $table->unsignedTinyInteger('lokasi_bandung')->default(0);
            $table->unsignedTinyInteger('lokasi_jakarta')->default(0);
            $table->unsignedTinyInteger('koreksi_by_admin')->default(0);

            $table->timestamps();

            $table->unique(['employee_id', 'tahun', 'bulan']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attendance_recaps');
    }
};
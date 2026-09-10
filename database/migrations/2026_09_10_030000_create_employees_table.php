<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->string('nipeg')->unique();       // NIP karyawan
            $table->string('nama');
            $table->string('jabatan')->nullable();
            $table->foreignId('division_id')->constrained()->cascadeOnUpdate()->restrictOnDelete();

            // Opsional: kalau karyawan juga punya akun login (pakai tabel users bawaan Laravel)
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('employees');
    }
};
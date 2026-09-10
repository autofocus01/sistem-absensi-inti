<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('divisions', function (Blueprint $table) {
            $table->id();
            $table->string('nama');                 // e.g. "Divisi Bisnis & Teknologi"
            $table->string('lini')->nullable();      // e.g. "SEVP" / "Direktur Langsung" - dipakai buat bagan struktur di dashboard Dirut
            $table->timestamps();

            $table->unique('nama');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('divisions');
    }
};
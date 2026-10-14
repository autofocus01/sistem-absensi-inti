<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('import_batches')) {
            return;
        }

        Schema::create('import_batches', function (Blueprint $table) {
            $table->id();

            /*
             * user_id sengaja TIDAK dibuat foreign key ke users (konsisten
             * dengan audit_logs): batch impor harus tetap mengingat operator
             * historis walaupun akunnya sudah dihapus.
             */
            $table->unsignedBigInteger('user_id')->nullable();

            $table->string('jenis', 30);            // absensi_harian | master_karyawan
            $table->string('nama_file');            // nama file ASLI dari pengunggah
            $table->string('file_hash', 64);        // sha256 isi file — dasar deteksi duplikat
            $table->string('format', 20)->nullable(); // paired | scan (absensi saja)
            $table->unsignedInteger('total_baris')->default(0);
            $table->unsignedInteger('berhasil')->default(0);
            $table->unsignedInteger('gagal')->default(0);
            $table->json('periode')->nullable();    // periode absensi yang tersinkron, e.g. ["2026-09"]
            $table->boolean('duplikat')->default(false); // isi file ini pernah diimpor sebelumnya
            $table->timestamps();

            $table->index(['jenis', 'created_at']);
            $table->index('file_hash');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('import_batches');
    }
};

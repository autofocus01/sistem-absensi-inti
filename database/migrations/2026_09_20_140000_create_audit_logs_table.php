<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('audit_logs')) {
            return;
        }

        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();

            /*
             * user_id sengaja TIDAK dibuat sebagai foreign key ke users.
             *
             * Alasannya:
             * audit log harus tetap menyimpan ID user sebagai
             * historical actor walaupun akun user tersebut sudah
             * dihapus.
             *
             * Jika memakai:
             * ->constrained('users')->nullOnDelete()
             *
             * maka ketika user dihapus, user_id audit akan berubah
             * menjadi NULL dan audit trail kehilangan identitas
             * aktor historisnya.
             */
            $table->unsignedBigInteger('user_id')->nullable();

            $table->string('action', 100);
            $table->string('subject_type')->nullable();
            $table->string('subject_id', 100)->nullable();
            $table->json('before_data')->nullable();
            $table->json('after_data')->nullable();
            $table->text('notes')->nullable();
            $table->ipAddress('ip_address')->nullable();
            $table->text('user_agent')->nullable();
            $table->string('request_id', 100)->nullable()->index();
            $table->timestamps();

            $table->index(['action', 'created_at']);
            $table->index(['subject_type', 'subject_id']);
            $table->index(['user_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('audit_logs');
    }
};
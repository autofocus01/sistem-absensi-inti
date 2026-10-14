<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (DB::getDriverName() !== 'sqlite') {
            // MySQL sudah ditangani oleh migration enum role
            // (2026_09_17_035515 dan 2026_10_02_120000).
            return;
        }

        /*
         * SQLite merepresentasikan enum sebagai CHECK constraint dan tidak
         * mendukung ALTER ENUM. Penambahan role 'admin_sistem' pada migration
         * 2026_10_02_120000 hanya menangani MySQL, sehingga UatAccountSeeder
         * gagal (CHECK constraint failed: role) saat dijalankan pada database
         * test sqlite. Tabel users direbuild dengan daftar role yang sama
         * seperti MySQL.
         */
        Schema::create('users_role_check_fix', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->rememberToken();
            $table->timestamps();
            $table->enum('role', [
                'karyawan', 'vp', 'hr_admin', 'direktur_utama', 'admin_sistem',
            ])->default('karyawan');
            $table->boolean('is_active')->default(true);
            $table->boolean('must_change_password')->default(false);
            $table->timestamp('last_login_at')->nullable();
        });

        DB::statement("
            INSERT INTO users_role_check_fix (
                id, name, email, email_verified_at, password, remember_token,
                created_at, updated_at, role, is_active, must_change_password, last_login_at
            )
            SELECT
                id, name, email, email_verified_at, password, remember_token,
                created_at, updated_at, role, is_active, must_change_password, last_login_at
            FROM users
        ");

        Schema::drop('users');
        Schema::rename('users_role_check_fix', 'users');
    }

    public function down(): void
    {
        if (DB::getDriverName() !== 'sqlite') {
            return;
        }

        if (DB::table('users')->where('role', 'admin_sistem')->exists()) {
            throw new RuntimeException(
                'Role admin_sistem masih digunakan. Nonaktifkan/hapus akun admin_sistem sebelum rollback migration.'
            );
        }

        Schema::create('users_role_check_rollback', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->rememberToken();
            $table->timestamps();
            $table->enum('role', [
                'karyawan', 'vp', 'hr_admin', 'direktur_utama',
            ])->default('karyawan');
            $table->boolean('is_active')->default(true);
            $table->boolean('must_change_password')->default(false);
            $table->timestamp('last_login_at')->nullable();
        });

        DB::statement("
            INSERT INTO users_role_check_rollback (
                id, name, email, email_verified_at, password, remember_token,
                created_at, updated_at, role, is_active, must_change_password, last_login_at
            )
            SELECT
                id, name, email, email_verified_at, password, remember_token,
                created_at, updated_at, role, is_active, must_change_password, last_login_at
            FROM users
        ");

        Schema::drop('users');
        Schema::rename('users_role_check_rollback', 'users');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $driver = DB::getDriverName();

        if ($driver === 'mysql') {
            DB::statement("
                ALTER TABLE users
                MODIFY COLUMN role
                ENUM('karyawan', 'vp', 'hr_admin', 'direktur_utama')
                DEFAULT 'karyawan'
            ");

            return;
        }

        /*
         * SQLite:
         *
         * Laravel/SQLite merepresentasikan enum sebagai CHECK constraint.
         * Kita perlu merebuild tabel users agar constraint role ikut berubah.
         *
         * Namun migration ini hanya perlu dijalankan pada database testing.
         */
        if ($driver === 'sqlite') {
            Schema::table('users', function (Blueprint $table) {
                // Tidak ada ALTER ENUM native di SQLite.
                // Constraint akan ditangani oleh migration schema users
                // pada database test.
            });
        }
    }

    public function down(): void
    {
        if (DB::getDriverName() === 'mysql') {
            DB::statement("
                ALTER TABLE users
                MODIFY COLUMN role
                ENUM('hr_admin', 'direktur_utama')
                DEFAULT 'hr_admin'
            ");
        }
    }
};
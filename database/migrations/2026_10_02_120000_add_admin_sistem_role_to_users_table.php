<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (DB::getDriverName() === 'mysql') {
            DB::statement("\n                ALTER TABLE users\n                MODIFY COLUMN role\n                ENUM('karyawan', 'vp', 'hr_admin', 'direktur_utama', 'admin_sistem')\n                DEFAULT 'karyawan'\n            ");
        }
    }

    public function down(): void
    {
        if (DB::getDriverName() === 'mysql') {
            $hasAdmin = DB::table('users')->where('role', 'admin_sistem')->exists();
            if ($hasAdmin) {
                throw new RuntimeException('Role admin_sistem masih digunakan. Nonaktifkan/hapus akun admin_sistem sebelum rollback migration.');
            }

            DB::statement("\n                ALTER TABLE users\n                MODIFY COLUMN role\n                ENUM('karyawan', 'vp', 'hr_admin', 'direktur_utama')\n                DEFAULT 'karyawan'\n            ");
        }
    }
};

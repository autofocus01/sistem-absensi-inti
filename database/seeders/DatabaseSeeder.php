<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            DivisionSeeder::class,
        ]);

        // Data karyawan & rekap absensi TIDAK di-seed di sini - itu datanya real
        // (bukan dummy), jadi masuknya lewat import Excel, bukan factory/seeder acak.
        // Lihat perintah: php artisan import:absensi {path-ke-file.xlsx}
    }
}
<?php

namespace Database\Seeders;

use App\Models\Employee;
use App\Models\User;
use Illuminate\Database\Seeder;

class UatUserSeeder extends Seeder
{
    public function run(): void
    {
        $karyawan = User::updateOrCreate(
            ['email' => 'karyawan@test.local'],
            ['name' => 'Karyawan UAT', 'password' => 'password', 'role' => 'karyawan', 'email_verified_at' => now()]
        );
        Employee::query()->where('nipeg', '90010001')->update(['user_id' => $karyawan->id, 'band_code' => 2]);

        $vpBisnis = User::updateOrCreate(
            ['email' => 'vp@test.local'],
            ['name' => 'VP Bisnis & Teknologi', 'password' => 'password', 'role' => 'vp', 'email_verified_at' => now()]
        );
        Employee::query()->where('nipeg', '90010002')->update(['user_id' => $vpBisnis->id, 'band_code' => 3]);

        $vpHukum = User::updateOrCreate(
            ['email' => 'vp.hukum@test.local'],
            ['name' => 'VP Hukum, Man. Risk & Kepatuhan', 'password' => 'password', 'role' => 'vp', 'email_verified_at' => now()]
        );
        Employee::query()->where('nipeg', '90010023')->update(['user_id' => $vpHukum->id, 'band_code' => 3]);

        User::updateOrCreate(
            ['email' => 'hr@test.local'],
            ['name' => 'HR Administrator UAT', 'password' => 'password', 'role' => 'hr_admin', 'email_verified_at' => now()]
        );

        User::updateOrCreate(
            ['email' => 'direktur@test.local'],
            ['name' => 'Direktur UAT', 'password' => 'password', 'role' => 'direktur_utama', 'email_verified_at' => now()]
        );

        $this->command?->info('User UAT berhasil disiapkan. Password seluruh akun UAT: password');
    }
}

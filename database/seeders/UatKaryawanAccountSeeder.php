<?php

namespace Database\Seeders;

use App\Models\Employee;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UatKaryawanAccountSeeder extends Seeder
{
    public function run(): void
    {
        $accounts = [
            [
                'email' => 'karyawan.band2@test.local',
                'name' => 'Ahmad Fauzan',
                'nipeg' => '90010001',
                'band_code' => 2,
            ],
            [
                'email' => 'karyawan.band3@test.local',
                'name' => 'Kartika Sari',
                'nipeg' => '90010011',
                'band_code' => 3,
            ],
            [
                'email' => 'karyawan.band4@test.local',
                'name' => 'Vina Oktaviani',
                'nipeg' => '90010021',
                'band_code' => 4,
            ],
        ];

        foreach ($accounts as $account) {
            $employee = Employee::where('nipeg', $account['nipeg'])->firstOrFail();

            // Pastikan data employee yang dipakai UAT konsisten dengan akun.
            $employee->band_code = $account['band_code'];
            $employee->save();

            $user = User::updateOrCreate(
                ['email' => $account['email']],
                [
                    'name' => $account['name'],
                    'password' => Hash::make('password'),
                    'role' => 'karyawan',
                    'email_verified_at' => now(),
                ]
            );

            // user_id sengaja tidak dimasukkan ke $fillable Employee.
            $employee->user()->associate($user);
            $employee->save();
        }

        $this->command?->info(
            'Akun UAT karyawan tersedia: Band 2, Band 3 (eligible) dan Band 4 (non-eligible). Password: password.'
        );
    }
}

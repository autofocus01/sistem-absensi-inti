<?php

namespace Database\Seeders;

use App\Models\Employee;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DummyKaryawanBandSeeder extends Seeder
{
    /**
     * Akun UAT karyawan yang memang eligible lembur.
     *
     * Band 2: Ahmad Fauzan / 90010001
     * Band 3: Kartika Sari / 90010011
     *
     * Password UAT keduanya: password
     */
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
        ];

        foreach ($accounts as $account) {
            $employee = Employee::query()
                ->where('nipeg', $account['nipeg'])
                ->first();

            if (! $employee) {
                $this->command?->warn("Employee {$account['nipeg']} belum tersedia. Jalankan DummyEmployeeSeeder terlebih dahulu.");
                continue;
            }

            $employee->band_code = $account['band_code'];
            $employee->save();

            $user = User::updateOrCreate(
                ['email' => $account['email']],
                [
                    'name' => $account['name'],
                    'role' => 'karyawan',
                    'password' => Hash::make('password'),
                ]
            );

            $employee->user()->associate($user);
            $employee->save();
        }

        $this->command?->info('Akun UAT Band 2/3 siap:');
        $this->command?->info('  Band 2: karyawan.band2@test.local / password');
        $this->command?->info('  Band 3: karyawan.band3@test.local / password');
    }
}

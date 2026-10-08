<?php

namespace Database\Seeders;

use App\Models\Division;
use App\Models\Employee;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UatAccountSeeder extends Seeder
{
    /**
     * Menyiapkan seluruh akun UAT yang diperlukan untuk pengujian role.
     *
     * Karyawan:
     * - Band 2 = eligible lembur.
     * - Band 3 = eligible lembur.
     * - Band 4 = non-eligible sebagai pembanding.
     *
     * Struktural:
     * - VP = role vp pada Divisi Bisnis & Teknologi.
     * - VP Hukum = role vp pada Divisi Hukum, Man. Risk & Kepatuhan.
     * - HR = role hr_admin.
     * - Direktur = role direktur_utama.
     *
     * Seeder dibuat idempotent agar aman dijalankan berulang kali,
     * termasuk setelah php artisan migrate:fresh --seed.
     */
    public function run(): void
    {
        $this->seedKaryawanAccounts();
        $this->seedStructuralAccounts();

        $this->command?->info(
            'Akun UAT karyawan dan akun struktural (VP, VP Hukum, HR, Direktur) berhasil disiapkan.'
        );
    }

    private function seedKaryawanAccounts(): void
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
            $employee = Employee::where('nipeg', $account['nipeg'])->first();

            if (! $employee) {
                $this->command?->warn(
                    "Employee {$account['nipeg']} tidak ditemukan. Akun {$account['email']} dilewati."
                );
                continue;
            }

            $employee->band_code = $account['band_code'];
            $employee->save();

            $user = $this->upsertUser(
                email: $account['email'],
                name: $account['name'],
                role: 'karyawan',
            );

            $employee->user()->associate($user);
            $employee->save();
        }
    }

    private function seedStructuralAccounts(): void
    {
        $businessDivision = Division::firstOrCreate(
            ['nama' => 'Divisi Bisnis & Teknologi'],
            ['lini' => 'SEVP'],
        );

        $legalDivision = Division::firstOrCreate(
            ['nama' => 'Divisi Hukum, Man. Risk & Kepatuhan'],
            ['lini' => 'Direktur Langsung'],
        );

        // VP biasa: harus mempunyai Employee agar scope divisinya tersedia.
        $vpEmployee = Employee::updateOrCreate(
            ['nipeg' => '80000001'],
            [
                'nama' => 'VP UAT Bisnis & Teknologi',
                'jabatan' => 'VP',
                'band_code' => null,
                'division_id' => $businessDivision->id,
            ],
        );

        $vpUser = $this->upsertUser(
            email: 'vp@test.local',
            name: 'VP UAT Bisnis & Teknologi',
            role: 'vp',
        );

        $vpEmployee->user()->associate($vpUser);
        $vpEmployee->save();

        // VP Hukum menggunakan role vp yang sama, tetapi scope divisinya
        // berasal dari Employee yang berada pada Divisi Hukum.
        $vpLegalEmployee = Employee::updateOrCreate(
            ['nipeg' => '80000002'],
            [
                'nama' => 'VP UAT Hukum',
                'jabatan' => 'VP Hukum',
                'band_code' => null,
                'division_id' => $legalDivision->id,
            ],
        );

        $vpLegalUser = $this->upsertUser(
            email: 'vp.hukum@test.local',
            name: 'VP UAT Hukum',
            role: 'vp',
        );

        $vpLegalEmployee->user()->associate($vpLegalUser);
        $vpLegalEmployee->save();

        // HR dan Direktur tidak membutuhkan Employee untuk role access.
        $this->upsertUser(
            email: 'hr@test.local',
            name: 'HR UAT',
            role: 'hr_admin',
        );

        $this->upsertUser(
            email: 'direktur@test.local',
            name: 'Direktur UAT',
            role: 'direktur_utama',
        );

        $this->upsertUser(
            email: 'admin.sistem@test.local',
            name: 'Administrator Sistem UAT',
            role: 'admin_sistem',
        );
    }

    private function upsertUser(string $email, string $name, string $role): User
    {
        return User::updateOrCreate(
            ['email' => $email],
            [
                'name' => $name,
                'password' => Hash::make('password'),
                'role' => $role,
                'is_active' => true,
                'must_change_password' => false,
            ],
        );
    }
}

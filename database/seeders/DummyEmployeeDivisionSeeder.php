<?php

namespace Database\Seeders;

use App\Models\Division;
use App\Models\Employee;
use Illuminate\Database\Seeder;
use RuntimeException;

class DummyEmployeeDivisionSeeder extends Seeder
{
    public function run(): void
    {
        /*
        |--------------------------------------------------------------------------
        | Mapping master dummy karyawan
        |--------------------------------------------------------------------------
        | Sumber:
        | Rekap_Kehadiran_2026.xlsx
        |
        | 90010001 - 90010022 : Bisnis & Teknologi
        | 90010023 - 90010027 : Hukum, Man. Risk & Kepatuhan
        | 90010028 - 90010034 : Keuangan & Akuntansi
        | 90010035 - 90010043 : Operasional
        | 90010044 - 90010050 : Pengadaan Strategis
        |--------------------------------------------------------------------------
        */

        $divisionNames = [
            'bisnis' => 'DIVISI BISNIS & TEKNOLOGI',
            'hukum' => 'DIVISI HUKUM, MAN. RISK & KEPATUHAN',
            'keuangan' => 'DIVISI KEUANGAN & AKUNTANSI',
            'operasional' => 'DIVISI OPERASIONAL',
            'pengadaan' => 'DIVISI PENGADAAN STRATEGIS',
        ];

        $divisions = [];

        foreach ($divisionNames as $key => $name) {
            $division = Division::query()
                ->where('nama', $name)
                ->first();

            if (! $division) {
                throw new RuntimeException(
                    "Divisi '{$name}' belum tersedia di tabel divisions."
                );
            }

            $divisions[$key] = $division->id;
        }

        /*
        |--------------------------------------------------------------------------
        | Mapping NIP -> Division
        |--------------------------------------------------------------------------
        */

        $mapping = [];

        // 90010001 - 90010022
        for ($i = 1; $i <= 22; $i++) {
            $mapping[] = [
                'nipeg' => '900100' . str_pad((string) $i, 2, '0', STR_PAD_LEFT),
                'division_id' => $divisions['bisnis'],
            ];
        }

        // 90010023 - 90010027
        for ($i = 23; $i <= 27; $i++) {
            $mapping[] = [
                'nipeg' => '900100' . str_pad((string) $i, 2, '0', STR_PAD_LEFT),
                'division_id' => $divisions['hukum'],
            ];
        }

        // 90010028 - 90010034
        for ($i = 28; $i <= 34; $i++) {
            $mapping[] = [
                'nipeg' => '900100' . str_pad((string) $i, 2, '0', STR_PAD_LEFT),
                'division_id' => $divisions['keuangan'],
            ];
        }

        // 90010035 - 90010043
        for ($i = 35; $i <= 43; $i++) {
            $mapping[] = [
                'nipeg' => '900100' . str_pad((string) $i, 2, '0', STR_PAD_LEFT),
                'division_id' => $divisions['operasional'],
            ];
        }

        // 90010044 - 90010050
        for ($i = 44; $i <= 50; $i++) {
            $mapping[] = [
                'nipeg' => '900100' . str_pad((string) $i, 2, '0', STR_PAD_LEFT),
                'division_id' => $divisions['pengadaan'],
            ];
        }

        /*
        |--------------------------------------------------------------------------
        | Update master employee
        |--------------------------------------------------------------------------
        */

        $updated = 0;
        $missing = [];

        foreach ($mapping as $item) {
            $employee = Employee::query()
                ->where('nipeg', $item['nipeg'])
                ->first();

            if (! $employee) {
                $missing[] = $item['nipeg'];
                continue;
            }

            $employee->forceFill([
                'division_id' => $item['division_id'],
            ])->save();

            $updated++;
        }

        $this->command->info(
            "Master divisi karyawan selesai diperbaiki: {$updated} karyawan."
        );

        if ($missing !== []) {
            $this->command->warn(
                'NIP berikut tidak ditemukan di master employees:'
            );

            foreach ($missing as $nipeg) {
                $this->command->warn("- {$nipeg}");
            }
        }
    }
}
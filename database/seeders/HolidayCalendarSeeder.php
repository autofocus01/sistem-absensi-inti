<?php

namespace Database\Seeders;

use App\Models\HolidayCalendar;
use Illuminate\Database\Seeder;

class HolidayCalendarSeeder extends Seeder
{
    public function run(): void
    {
        $holidays = [
            [
                'tanggal' => '2026-09-21',
                'nama' => 'UAT Dummy National Holiday',
                'jenis' => 'NATIONAL_HOLIDAY',
                'is_active' => true,
                'source_document' => 'UAT-DEMO',
                'notes' => 'Data dummy untuk pengujian sistem. Bukan data kalender produksi.',
            ],
        ];

        foreach ($holidays as $holiday) {
            HolidayCalendar::updateOrCreate(
                [
                    'tanggal' => $holiday['tanggal'],
                ],
                $holiday
            );
        }
    }
}
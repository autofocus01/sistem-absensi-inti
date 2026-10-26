<?php

namespace Database\Seeders;

use App\Models\HolidayCalendar;
use Illuminate\Database\Seeder;

class HolidayCalendarSeeder extends Seeder
{
    public function run(): void
    {
        // Hari libur nasional resmi 2026 berdasarkan SKB 3 Menteri.
        // Cuti bersama tidak dimasukkan sebagai NATIONAL_HOLIDAY karena
        // pelaksanaannya untuk perusahaan swasta diatur oleh pimpinan masing-masing.
        $holidays = [
            [
                'tanggal' => '2026-01-01',
                'nama' => 'Tahun Baru 2026 Masehi',
            ],
            [
                'tanggal' => '2026-01-16',
                'nama' => 'Isra Mikraj Nabi Muhammad saw.',
            ],
            [
                'tanggal' => '2026-02-17',
                'nama' => 'Tahun Baru Imlek 2577 Kongzili',
            ],
            [
                'tanggal' => '2026-03-19',
                'nama' => 'Hari Suci Nyepi (Tahun Baru Saka 1948)',
            ],
            [
                'tanggal' => '2026-03-21',
                'nama' => 'Idulfitri 1447 H',
            ],
            [
                'tanggal' => '2026-03-22',
                'nama' => 'Idulfitri 1447 H',
            ],
            [
                'tanggal' => '2026-04-03',
                'nama' => 'Wafat Yesus Kristus',
            ],
            [
                'tanggal' => '2026-04-05',
                'nama' => 'Kebangkitan Yesus Kristus (Paskah)',
            ],
            [
                'tanggal' => '2026-05-01',
                'nama' => 'Hari Buruh Internasional',
            ],
            [
                'tanggal' => '2026-05-14',
                'nama' => 'Kenaikan Yesus Kristus',
            ],
            [
                'tanggal' => '2026-05-27',
                'nama' => 'Iduladha 1447 H',
            ],
            [
                'tanggal' => '2026-05-31',
                'nama' => 'Hari Raya Waisak 2570 BE',
            ],
            [
                'tanggal' => '2026-06-01',
                'nama' => 'Hari Lahir Pancasila',
            ],
            [
                'tanggal' => '2026-06-16',
                'nama' => '1 Muharam Tahun Baru Islam 1448 H',
            ],
            [
                'tanggal' => '2026-08-17',
                'nama' => 'Proklamasi Kemerdekaan',
            ],
            [
                'tanggal' => '2026-08-25',
                'nama' => 'Maulid Nabi Muhammad saw.',
            ],
            [
                'tanggal' => '2026-12-25',
                'nama' => 'Kelahiran Yesus Kristus',
            ],
        ];

        foreach ($holidays as $holiday) {
            HolidayCalendar::updateOrCreate(
                [
                    'tanggal' => $holiday['tanggal'],
                ],
                [
                    ...$holiday,
                    'jenis' => 'NATIONAL_HOLIDAY',
                    'is_active' => true,
                    'source_document' => 'SKB 3 Menteri 2026',
                    'notes' => 'Hari libur nasional resmi tahun 2026.',
                ]
            );
        }

        // Cuti bersama 2026 (SKB 3 Menteri). Diselaraskan dengan aturan
        // pemerintah/BUMN: hari libur nasional + cuti bersama = hari libur.
        // Daftar ini dapat disesuaikan oleh HR bila SKB resmi berbeda.
        $cutiBersama = [
            ['tanggal' => '2026-02-16', 'nama' => 'Cuti Bersama Tahun Baru Imlek 2577 Kongzili'],
            ['tanggal' => '2026-03-20', 'nama' => 'Cuti Bersama Hari Suci Nyepi'],
            ['tanggal' => '2026-03-23', 'nama' => 'Cuti Bersama Idulfitri 1447 H'],
            ['tanggal' => '2026-03-24', 'nama' => 'Cuti Bersama Idulfitri 1447 H'],
            ['tanggal' => '2026-12-24', 'nama' => 'Cuti Bersama Kelahiran Yesus Kristus'],
        ];

        foreach ($cutiBersama as $holiday) {
            HolidayCalendar::updateOrCreate(
                [
                    'tanggal' => $holiday['tanggal'],
                ],
                [
                    ...$holiday,
                    'jenis' => 'CUTI_BERSAMA',
                    'is_active' => true,
                    'source_document' => 'SKB 3 Menteri 2026 (Cuti Bersama)',
                    'notes' => 'Cuti bersama. Verifikasi terhadap SKB resmi; dapat dinonaktifkan oleh HR.',
                ]
            );
        }

        // Tetap tersedia untuk UAT Demo Mode, tetapi tidak boleh dianggap
        // sebagai bagian dari kalender produksi resmi.
        if (! app()->environment('production')) {
            HolidayCalendar::updateOrCreate(
                [
                    'tanggal' => '2026-09-21',
                ],
                [
                    'nama' => 'UAT Dummy National Holiday',
                    'jenis' => 'NATIONAL_HOLIDAY',
                    'is_active' => true,
                    'source_document' => 'UAT-DEMO',
                    'notes' => 'Data dummy untuk pengujian sistem. Bukan data kalender produksi.',
                ]
            );
        }
    }
}

<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Data master/configuration yang aman untuk seluruh environment.
        $this->call([
            OvertimePolicySeeder::class,
            DivisionSeeder::class,
            HolidayCalendarSeeder::class,
        ]);

        // Dummy employee dan akun @test.local hanya untuk UAT/staging.
        // Jangan pernah membuat data UAT otomatis di production.
        if (! app()->environment('production')) {
            $this->call([
                DummyEmployeeSeeder::class,
                UatAccountSeeder::class,
            ]);
        }
    }
}

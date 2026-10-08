<?php

namespace App\Console\Commands;

use App\Models\AttendanceLog;
use App\Models\AttendanceRecap;
use App\Models\Employee;
use App\Models\OvertimeSubmission;
use App\Services\Demo\DemoClock;
use Database\Seeders\DummyEmployeeSeeder;
use Database\Seeders\HolidayCalendarSeeder;
use Database\Seeders\UatAccountSeeder;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class DemoSeed extends Command
{
    protected $signature = 'demo:seed';
    protected $description = 'Menyiapkan akun dan data bersih untuk presentasi Demo Mode.';

    public function handle(DemoClock $clock): int
    {
        if (app()->environment('production') || ! $clock->enabled()) {
            $this->error('Perintah demo:seed tidak boleh dijalankan pada production atau saat DEMO_MODE=false.');
            return self::FAILURE;
        }

        DB::transaction(function () use ($clock): void {
            $this->callSilent('db:seed', ['--class' => DummyEmployeeSeeder::class]);
            $this->callSilent('db:seed', ['--class' => UatAccountSeeder::class]);
            $this->callSilent('db:seed', ['--class' => HolidayCalendarSeeder::class]);

            $employee = Employee::query()->where('nipeg', '90010001')->first();
            if (! $employee) {
                throw new \RuntimeException('Employee demo 90010001 tidak ditemukan.');
            }

            $date = $clock->now()->toDateString();

            $submissionIds = OvertimeSubmission::query()
                ->where('user_id', $employee->user_id)
                ->whereDate('overtime_date', $date)
                ->pluck('id');

            if ($submissionIds->isNotEmpty()) {
                DB::table('overtime_policy_snapshots')
                    ->whereIn('overtime_submission_id', $submissionIds->all())
                    ->delete();
                OvertimeSubmission::query()->whereIn('id', $submissionIds->all())->delete();
            }

            AttendanceLog::query()
                ->where('employee_id', $employee->id)
                ->whereDate('tanggal', $date)
                ->delete();

            AttendanceRecap::query()
                ->where('employee_id', $employee->id)
                ->where('tahun', $clock->now()->year)
                ->where('bulan', $clock->now()->month)
                ->delete();

            $clock->reset();
        });

        $this->info('Data demo berhasil disiapkan.');
        $this->line('Karyawan: karyawan.band2@test.local / password');
        $this->line('VP:       vp@test.local / password');
        $this->line('HR:       hr@test.local / password');
        $this->line('Direktur: direktur@test.local / password');
        $this->line('Presensi demo dimulai dari kondisi kosong pada tanggal demo.');

        return self::SUCCESS;
    }
}

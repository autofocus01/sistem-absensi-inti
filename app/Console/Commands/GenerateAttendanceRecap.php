<?php

namespace App\Console\Commands;

use App\Models\Employee;
use App\Services\Attendance\AttendanceRecapSyncService;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class GenerateAttendanceRecap extends Command
{
    protected $signature = 'attendance:generate-recap
        {--month= : Bulan target, 1-12. Default bulan berjalan.}
        {--year= : Tahun target. Default tahun berjalan.}
        {--employee= : Filter NIPEG tertentu.}
        {--dry-run : Tampilkan hasil perhitungan tanpa menulis ke database.}';

    protected $description = 'Generate AttendanceRecap bulanan dari AttendanceLog. '
        . 'Perhitungan didelegasikan ke AttendanceRecapSyncService agar satu sumber kebenaran.';

    public function handle(AttendanceRecapSyncService $sync): int
    {
        $month = (int) ($this->option('month') ?: now()->month);
        $year = (int) ($this->option('year') ?: now()->year);

        if ($month < 1 || $month > 12) {
            $this->error('Opsi --month harus 1 sampai 12.');

            return self::FAILURE;
        }

        if ($year < 2000 || $year > 2100) {
            $this->error('Opsi --year harus berada pada rentang 2000-2100.');

            return self::FAILURE;
        }

        $employeeIds = null;

        if ($this->option('employee')) {
            $employee = Employee::query()->where('nipeg', $this->option('employee'))->first();

            if (! $employee) {
                $this->error("Employee dengan NIPEG {$this->option('employee')} tidak ditemukan.");

                return self::FAILURE;
            }

            $employeeIds = [$employee->id];
        }

        if ($this->option('dry-run')) {
            return $this->dryRun($sync, $year, $month, $employeeIds);
        }

        $count = $sync->syncPeriod($year, $month, $employeeIds);

        $this->info("Rekap {$month}/{$year} disinkronkan untuk {$count} employee.");

        return self::SUCCESS;
    }

    /**
     * Hitung tanpa menulis: jalankan di dalam transaksi lalu rollback.
     *
     * AttendanceRecapSyncService menyimpan data non-presensi (cuti/sakit/ijin/
     * perdin, koreksi admin, lokasi) sebagaimana adanya, sehingga rollback aman
     * dan hasil dry-run identik dengan run nyata.
     */
    private function dryRun(
        AttendanceRecapSyncService $sync,
        int $year,
        int $month,
        ?array $employeeIds
    ): int {
        $periodStart = Carbon::create($year, $month, 1)->startOfDay();
        $periodEnd = $periodStart->copy()->endOfMonth()->endOfDay();

        $count = DB::transaction(function () use ($sync, $year, $month, $employeeIds): int {
            $result = $sync->syncPeriod($year, $month, $employeeIds);

            DB::rollBack();

            return $result;
        });

        $this->info("Dry-run rekap {$month}/{$year}: {$count} employee akan disinkronkan "
            . "(sumber log {$periodStart->toDateString()} s/d {$periodEnd->toDateString()}).");

        return self::SUCCESS;
    }
}

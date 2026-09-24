<?php

namespace App\Console\Commands;

use App\Models\AttendanceLog;
use App\Models\AttendanceRecap;
use App\Models\Employee;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class GenerateAttendanceRecap extends Command
{
    protected $signature = 'attendance:generate-recap
        {--month= : Bulan target, 1-12. Default bulan berjalan.}
        {--year= : Tahun target. Default tahun berjalan.}
        {--from= : Tanggal awal sumber log, format Y-m-d. Default awal bulan target.}
        {--to= : Tanggal akhir sumber log, format Y-m-d. Default akhir bulan target.}
        {--employee= : Filter NIPEG tertentu.}
        {--force : Timpa rekap yang sudah ada. Tanpa ini, rekap existing dilewati.}
        {--dry-run : Hitung dan tampilkan hasil tanpa menulis ke database.}';

    protected $description = 'Generate AttendanceRecap bulanan dari AttendanceLog yang sudah tersimpan.';

    public function handle(): int
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

        $periodStart = Carbon::create($year, $month, 1)->startOfDay();
        $periodEnd = $periodStart->copy()->endOfMonth()->endOfDay();

        $from = $this->option('from')
            ? Carbon::parse($this->option('from'))->startOfDay()
            : $periodStart->copy();

        $to = $this->option('to')
            ? Carbon::parse($this->option('to'))->endOfDay()
            : $periodEnd->copy();

        if ($from->gt($to)) {
            $this->error('Tanggal --from tidak boleh lebih besar dari --to.');
            return self::FAILURE;
        }

        if ($from->lt($periodStart) || $to->gt($periodEnd)) {
            $this->error('Rentang --from/--to harus berada di dalam bulan dan tahun target.');
            return self::FAILURE;
        }

        $logsQuery = AttendanceLog::query()
            ->with('employee.division')
            ->whereBetween('tanggal', [$from->toDateString(), $to->toDateString()])
            ->whereNotNull('employee_id')
            ->whereNotNull('jam_masuk');

        if ($this->option('employee')) {
            $logsQuery->whereHas('employee', function ($query) {
                $query->where('nipeg', $this->option('employee'));
            });
        }

        $logs = $logsQuery
            ->orderBy('employee_id')
            ->orderBy('tanggal')
            ->get();

        if ($logs->isEmpty()) {
            $this->warn('Tidak ada AttendanceLog berpasangan/ber-Jam Masuk pada rentang yang dipilih. Tidak ada rekap dibuat.');
            return self::SUCCESS;
        }

        $workdays = 0;
        for ($date = $periodStart->copy(); $date->lte($periodEnd); $date->addDay()) {
            if ($date->isWeekday()) {
                $workdays++;
            }
        }

        $grouped = $logs->groupBy('employee_id');

        $created = 0;
        $updated = 0;
        $skipped = 0;

        $this->info(sprintf(
            'Generate rekap %02d/%d | sumber log %s s/d %s | hari kerja kalender: %d | employee dengan log: %d',
            $month,
            $year,
            $from->toDateString(),
            $to->toDateString(),
            $workdays,
            $grouped->count()
        ));

        foreach ($grouped as $employeeId => $employeeLogs) {
            /** @var AttendanceLog $firstLog */
            $firstLog = $employeeLogs->first();
            $employee = $firstLog->employee;

            if (! $employee) {
                $this->warn("employee_id {$employeeId} tidak punya master Employee. Dilewati.");
                $skipped++;
                continue;
            }

            // Satu hari hanya dihitung sekali. Ini mencegah duplicate AttendanceLog
            // membuat hadir/menit telat terhitung dua kali. Jika ada beberapa log pada
            // tanggal yang sama, record dengan id terbesar dipakai sebagai fakta terbaru.
            $dailyLogs = $employeeLogs
                ->sortByDesc('id')
                ->unique(fn (AttendanceLog $log) => $log->tanggal?->format('Y-m-d'))
                ->values();

            $hadir = $dailyLogs
                ->filter(fn (AttendanceLog $log) => $log->tanggal?->isWeekday())
                ->count();

            $menitTelat = (int) $dailyLogs->sum(
                fn (AttendanceLog $log) => $log->menitTelat()
            );

            $telatHari = (int) $dailyLogs->filter(
                fn (AttendanceLog $log) => $log->menitTelat() > 0
            )->count();

            $alpha = max(0, $workdays - $hadir);
            $persenKehadiran = $workdays > 0
                ? round(($hadir / $workdays) * 100, 1)
                : 0.0;

            /*
             * AttendanceLog saat ini tidak menyimpan lokasi Bandung/Jakarta,
             * sehingga command TIDAK mengarang angka lokasi.
             */
            $payload = [
                'employee_id' => $employee->id,
                'tahun' => $year,
                'bulan' => $month,
                'hari_kerja' => $workdays,
                'hadir' => $hadir,
                'alpha' => $alpha,
                'perdin' => 0,
                'cuti' => 0,
                'sakit' => 0,
                'ijin' => 0,
                'menit_telat' => $menitTelat,
                'telat_hari' => $telatHari,
                'persen_kehadiran' => $persenKehadiran,
                'lokasi_bandung' => 0,
                'lokasi_jakarta' => 0,
            ];

            $existing = AttendanceRecap::query()
                ->where('employee_id', $employee->id)
                ->where('tahun', $year)
                ->where('bulan', $month)
                ->first();

            $action = $existing ? 'SKIP' : 'CREATE';

            if ($existing && $this->option('force')) {
                $action = 'UPDATE';
            }

            $this->line(sprintf(
                '%-8s %-24s | hadir=%2d | alpha=%2d | telat=%3d menit (%2d hari) | %6.1f%% | %s',
                $employee->nipeg,
                mb_substr($employee->nama, 0, 24),
                $hadir,
                $alpha,
                $menitTelat,
                $telatHari,
                $persenKehadiran,
                $action
            ));

            if ($this->option('dry-run')) {
                continue;
            }

            if ($existing && ! $this->option('force')) {
                $skipped++;
                continue;
            }

            DB::transaction(function () use ($existing, $payload, &$created, &$updated) {
                if ($existing) {
                    $existing->update($payload);
                    $updated++;
                    return;
                }

                AttendanceRecap::create($payload);
                $created++;
            });
        }

        if ($this->option('dry-run')) {
            $this->newLine();
            $this->info('DRY-RUN selesai. Tidak ada perubahan database.');
            return self::SUCCESS;
        }

        $this->newLine();
        $this->info("CREATE: {$created}");
        $this->info("UPDATE: {$updated}");
        $this->info("SKIP  : {$skipped}");
        $this->info('Generate AttendanceRecap selesai.');

        return self::SUCCESS;
    }
}

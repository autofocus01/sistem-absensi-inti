<?php

namespace App\Services\Reporting;

use App\Models\AttendanceLog;
use App\Models\AttendanceRecap;
use App\Models\Division;
use App\Models\Employee;
use App\Models\HolidayCalendar;
use App\Services\Overtime\OvertimeReportingService;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class ProductionReportService
{
    public function __construct(private readonly OvertimeReportingService $overtime)
    {
    }

    public function period(int $year, int $month, ?int $divisionId = null): array
    {
        $start = Carbon::create($year, $month, 1)->startOfMonth();
        $end = $start->copy()->endOfMonth();

        $employees = Employee::query()
            ->with('division')
            ->when($divisionId !== null, fn ($q) => $q->where('division_id', $divisionId))
            ->orderBy('nama')
            ->get();

        // AttendanceLog adalah sumber fakta presensi; AttendanceRecap dapat tertinggal
        // jika belum diregenerasi setelah import.
        $logsByEmployee = AttendanceLog::query()
            ->with('employee.division')
            ->whereBetween('tanggal', [$start->toDateString(), $end->toDateString()])
            ->when(
                $divisionId !== null,
                fn ($q) => $q->whereHas(
                    'employee',
                    fn ($e) => $e->where('division_id', $divisionId)
                )
            )
            ->orderBy('tanggal')
            ->get()
            ->groupBy('employee_id');

        // Hari libur nasional aktif tidak dihitung sebagai hari kerja normal.
        $holidayDates = HolidayCalendar::query()
            ->whereBetween('tanggal', [$start->toDateString(), $end->toDateString()])
            ->where('is_active', true)
            ->whereIn('jenis', ['NATIONAL_HOLIDAY', 'CUTI_BERSAMA'])
            ->pluck('tanggal')
            ->map(fn ($date) => Carbon::parse($date)->toDateString())
            ->flip();

        $workingDays = $this->workingDays($start, $end, $holidayDates);

        $officialOt = $this->overtime->officialMinutes($year, $month, $divisionId);
        $pendingVp = $this->overtime->pendingVpMinutes($year, $month, $divisionId);
        $pendingHr = $this->overtime->pendingHrMinutes($year, $month, $divisionId);

        $rows = $employees->map(function (Employee $employee) use (
            $logsByEmployee,
            $workingDays,
            $holidayDates,
            $year,
            $month
        ) {
            $allLogs = $logsByEmployee->get($employee->id, collect());

            /*
             * Klasifikasi yang dikunci:
             * - Hadir normal hanya dihitung pada Senin-Jumat yang bukan libur nasional.
             * - Log Sabtu/Minggu/libur nasional tidak menambah angka Hadir.
             * - Lembur resmi/pending dihitung dari workflow OvertimeSubmission melalui
             *   OvertimeReportingService, bukan dari keberadaan AttendanceLog saja.
             * Dengan demikian satu log weekend tidak otomatis menjadi kehadiran normal.
             */
            $normalWorkLogs = $allLogs->filter(function (AttendanceLog $log) use ($holidayDates) {
                if (! $log->jam_masuk || ! $log->tanggal) {
                    return false;
                }

                $date = Carbon::parse($log->tanggal)->toDateString();

                return Carbon::parse($date)->isWeekday()
                    && ! $holidayDates->has($date);
            });

            $hadir = $normalWorkLogs->count();
            $telatHari = $normalWorkLogs
                ->filter(fn (AttendanceLog $log) => $log->menitTelat() > 0)
                ->count();
            $menitTelat = (int) $normalWorkLogs->sum(
                fn (AttendanceLog $log) => $log->menitTelat()
            );

            // Alpha hanya dibandingkan dengan hari kerja normal yang dijadwalkan.
            $alpha = max(0, $workingDays - $hadir);
            $persenKehadiran = $workingDays > 0
                ? round(min(100, ($hadir / $workingDays) * 100), 1)
                : 0.0;

            return [
                'employee_id' => $employee->id,
                'nipeg' => $employee->nipeg,
                'nama' => $employee->nama,
                'divisi' => $employee->division?->nama ?? '-',
                'hadir' => $hadir,
                'telat_hari' => $telatHari,
                'menit_telat' => $menitTelat,
                'alpha' => $alpha,
                'persen_kehadiran' => $persenKehadiran,
                'official_ot_minutes' => $this->overtime->monthlyOfficialMinutes(
                    $employee->id,
                    $year,
                    $month
                ),
            ];
        });

        return [
            'year' => $year,
            'month' => $month,
            'start' => $start,
            'end' => $end,
            'employees' => $employees,
            'divisions' => Division::query()->orderBy('nama')->get(),
            'rows' => $rows,
            // Riwayat log tetap dikembalikan untuk audit; hanya agregasi Hadir yang dibatasi.
            'logs' => $logsByEmployee->flatten(),
            'recaps' => AttendanceRecap::query()
                ->where('tahun', $year)
                ->where('bulan', $month)
                ->when(
                    $divisionId !== null,
                    fn ($q) => $q->whereHas(
                        'employee',
                        fn ($e) => $e->where('division_id', $divisionId)
                    )
                )
                ->get()
                ->keyBy('employee_id'),
            'summary' => [
                'employee_count' => $employees->count(),
                'attendance_avg' => round((float) ($rows->avg('persen_kehadiran') ?? 0), 1),
                'present_days' => (int) $rows->sum('hadir'),
                'late_days' => (int) $rows->sum('telat_hari'),
                'late_minutes' => (int) $rows->sum('menit_telat'),
                'alpha_days' => (int) $rows->sum('alpha'),
                'official_ot_minutes' => $officialOt,
                'pending_vp_minutes' => $pendingVp,
                'pending_hr_minutes' => $pendingHr,
            ],
        ];
    }

    /**
     * Hitung hari kerja normal: Senin-Jumat dikurangi hari libur nasional aktif.
     */
    private function workingDays(Carbon $start, Carbon $end, Collection $holidayDates): int
    {
        $days = 0;

        for ($date = $start->copy(); $date->lte($end); $date->addDay()) {
            if ($date->isWeekday() && ! $holidayDates->has($date->toDateString())) {
                $days++;
            }
        }

        return $days;
    }

    public function formatMinutes(int $minutes): string
    {
        $minutes = max(0, $minutes);

        return intdiv($minutes, 60)
            . 'j '
            . str_pad((string) ($minutes % 60), 2, '0', STR_PAD_LEFT)
            . 'm';
    }
}

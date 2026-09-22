<?php

namespace App\Services\Reporting;

use App\Models\AttendanceLog;
use App\Models\AttendanceRecap;
use App\Models\Division;
use App\Models\Employee;
use App\Services\Overtime\OvertimeReportingService;
use Carbon\Carbon;

class ProductionReportService
{
    public function __construct(private readonly OvertimeReportingService $overtime) {}

    public function period(int $year, int $month, ?int $divisionId = null): array
    {
        $start = Carbon::create($year, $month, 1)->startOfMonth();
        $end = $start->copy()->endOfMonth();

        $employees = Employee::query()->with('division')
            ->when($divisionId, fn ($q) => $q->where('division_id', $divisionId))
            ->orderBy('nama')->get();
        $recaps = AttendanceRecap::query()->with('employee.division')
            ->where('tahun', $year)->where('bulan', $month)
            ->when($divisionId, fn ($q) => $q->whereHas('employee', fn ($e) => $e->where('division_id', $divisionId)))
            ->get()->keyBy('employee_id');
        $logs = AttendanceLog::query()->with('employee.division')
            ->whereBetween('tanggal', [$start->toDateString(), $end->toDateString()])
            ->when($divisionId, fn ($q) => $q->whereHas('employee', fn ($e) => $e->where('division_id', $divisionId)))
            ->orderBy('tanggal')->get();

        $officialOt = $this->overtime->officialMinutes($year, $month, $divisionId);
        $pendingVp = $this->overtime->pendingVpMinutes($year, $month, $divisionId);
        $pendingHr = $this->overtime->pendingHrMinutes($year, $month, $divisionId);

        $rows = $employees->map(function (Employee $employee) use ($recaps, $year, $month) {
            $recap = $recaps->get($employee->id);
            return [
                'employee_id' => $employee->id,
                'nipeg' => $employee->nipeg,
                'nama' => $employee->nama,
                'divisi' => $employee->division?->nama ?? '-',
                'hadir' => (int) ($recap?->hadir ?? 0),
                'telat_hari' => (int) ($recap?->telat_hari ?? 0),
                'menit_telat' => (int) ($recap?->menit_telat ?? 0),
                'alpha' => (int) ($recap?->alpha ?? 0),
                'persen_kehadiran' => (float) ($recap?->persen_kehadiran ?? 0),
                'official_ot_minutes' => $this->overtime->monthlyOfficialMinutes($employee->id, $year, $month),
            ];
        });

        return [
            'year' => $year, 'month' => $month, 'start' => $start, 'end' => $end,
            'employees' => $employees, 'divisions' => Division::orderBy('nama')->get(),
            'rows' => $rows, 'logs' => $logs, 'recaps' => $recaps,
            'summary' => [
                'employee_count' => $employees->count(),
                'attendance_avg' => round((float) ($recaps->avg('persen_kehadiran') ?? 0), 1),
                'present_days' => (int) $recaps->sum('hadir'),
                'late_days' => (int) $recaps->sum('telat_hari'),
                'late_minutes' => (int) $recaps->sum('menit_telat'),
                'alpha_days' => (int) $recaps->sum('alpha'),
                'official_ot_minutes' => $officialOt,
                'pending_vp_minutes' => $pendingVp,
                'pending_hr_minutes' => $pendingHr,
            ],
        ];
    }

    public function formatMinutes(int $minutes): string
    {
        $minutes = max(0, $minutes);
        return intdiv($minutes, 60) . 'j ' . str_pad((string) ($minutes % 60), 2, '0', STR_PAD_LEFT) . 'm';
    }
}

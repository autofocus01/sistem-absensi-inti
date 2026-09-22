<?php

namespace App\Services\Overtime;

use App\Models\OvertimeSubmission;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class OvertimeReportingService
{
    public const STATUS_PENDING_VP = 'PENDING_VP';
    public const STATUS_APPROVED_VP = 'APPROVED_VP';
    public const STATUS_VERIFIED_HR = 'VERIFIED_HR';
    public const STATUS_REJECTED_VP = 'REJECTED_VP';
    public const STATUS_REJECTED_HR = 'REJECTED_HR';

    public function periodQuery(
        int $year,
        int $month,
        ?int $divisionId = null,
        ?int $employeeId = null,
    ): Builder {
        return OvertimeSubmission::query()
            ->whereYear('overtime_date', $year)
            ->whereMonth('overtime_date', $month)
            ->when($divisionId !== null, fn (Builder $q) => $q->where('division_id', $divisionId))
            ->when($employeeId !== null, fn (Builder $q) => $q->whereHas(
                'user.employee',
                fn (Builder $employee) => $employee->whereKey($employeeId)
            ));
    }

    /**
     * Official overtime is immutable from AttendanceLog status fields:
     * only HR-verified + eligible + recognized minutes > 0 count.
     */
    public function officialEntries(
        int $employeeId,
        string $startDate,
        string $endDate,
    ): Collection {
        return OvertimeSubmission::query()
            ->whereHas('user.employee', fn (Builder $employee) => $employee->whereKey($employeeId))
            ->whereBetween('overtime_date', [$startDate, $endDate])
            ->where('status', self::STATUS_VERIFIED_HR)
            ->where('eligibility_status', 'ELIGIBLE')
            ->where('recognized_minutes', '>', 0)
            ->with(['division', 'attendanceLog'])
            ->select('overtime_submissions.*')
            ->selectRaw('overtime_date as tanggal')
            ->orderBy('overtime_date')
            ->get();
    }

    public function officialMinutes(
        int $year,
        int $month,
        ?int $divisionId = null,
        ?int $employeeId = null,
    ): int {
        return (int) $this->periodQuery($year, $month, $divisionId, $employeeId)
            ->where('status', self::STATUS_VERIFIED_HR)
            ->where('eligibility_status', 'ELIGIBLE')
            ->where('recognized_minutes', '>', 0)
            ->sum('recognized_minutes');
    }

    public function monthlyOfficialMinutes(int $employeeId, int $year, int $month): int
    {
        return $this->officialMinutes($year, $month, null, $employeeId);
    }

    public function pendingVpMinutes(
        int $year,
        int $month,
        ?int $divisionId = null,
        ?int $employeeId = null,
    ): int {
        return (int) $this->periodQuery($year, $month, $divisionId, $employeeId)
            ->where('status', self::STATUS_PENDING_VP)
            ->where('eligibility_status', 'ELIGIBLE')
            ->where('recognized_minutes', '>', 0)
            ->sum('recognized_minutes');
    }

    public function pendingHrMinutes(
        int $year,
        int $month,
        ?int $divisionId = null,
        ?int $employeeId = null,
    ): int {
        return (int) $this->periodQuery($year, $month, $divisionId, $employeeId)
            ->where('status', self::STATUS_APPROVED_VP)
            ->where('eligibility_status', 'ELIGIBLE')
            ->where('recognized_minutes', '>', 0)
            ->sum('recognized_minutes');
    }

    public function statusCounts(
        int $year,
        int $month,
        ?int $divisionId = null,
    ): array {
        $rows = $this->periodQuery($year, $month, $divisionId)
            ->select(['status'])
            ->get();

        return [
            'Pending VP' => $rows->where('status', self::STATUS_PENDING_VP)->count(),
            'Disetujui VP' => $rows->where('status', self::STATUS_APPROVED_VP)->count(),
            'Verified HR' => $rows->where('status', self::STATUS_VERIFIED_HR)->count(),
            'Ditolak' => $rows->whereIn('status', [self::STATUS_REJECTED_VP, self::STATUS_REJECTED_HR])->count(),
        ];
    }

    public function forEmployeeMonth(int $employeeId, int $year, int $month): Collection
    {
        return $this->periodQuery($year, $month, null, $employeeId)
            ->with(['division', 'attendanceLog'])
            ->latest('overtime_date')
            ->get();
    }

    public function formatMinutes(int $minutes): string
    {
        if ($minutes < 0) {
            throw new \InvalidArgumentException('Minutes cannot be negative.');
        }

        return intdiv($minutes, 60) . 'j ' . str_pad((string) ($minutes % 60), 2, '0', STR_PAD_LEFT) . 'm';
    }
}

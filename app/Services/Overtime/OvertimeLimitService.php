<?php

namespace App\Services\Overtime;

use App\Models\OvertimeSubmission;
use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Builder;
use RuntimeException;

class OvertimeLimitService
{
    /**
     * Status yang masih dianggap "reserved/active" untuk mencegah
     * employee mengajukan OT melewati batas sebelum workflow selesai.
     */
    private const ACTIVE_STATUSES = [
        'PENDING_VP',
        'APPROVED_VP',
        'VERIFIED_HR',
    ];

    /**
     * Validate candidate recognized minutes against the effective policy.
     *
     * Legal weekly limit from PP 35/2021 applies to workday OT only;
     * overtime on weekly rest/public holidays is excluded from that limit.
     */
    public function validate(
        int $employeeId,
        CarbonInterface $date,
        int $candidateMinutes,
        array $policy,
        ?string $excludeSubmissionId = null,
    ): array {
        $violations = [];

        if ($candidateMinutes < 0) {
            throw new RuntimeException('Candidate overtime minutes cannot be negative.');
        }

        $dayType = $policy['day_type'] ?? null;

        if (! in_array($dayType, ['WORKDAY', 'WEEKEND', 'NATIONAL_HOLIDAY'], true)) {
            throw new RuntimeException('Invalid overtime day type for limit validation.');
        }

        $dailyExisting = $this->activeMinutesForDate($employeeId, $date, $excludeSubmissionId);
        $dailyTotal = $dailyExisting + $candidateMinutes;
        $dailyLimit = $this->nullableInt($policy['legal_maximum_minutes_daily'] ?? null);

        // PP 35/2021 Pasal 26(1)-(2): the 4-hour/day and 18-hour/week
        // general limits exclude overtime on weekly rest/public holidays.
        if ($dailyLimit !== null && $dayType === 'WORKDAY' && $dailyTotal > $dailyLimit) {
            $violations[] = [
                'code' => 'LEGAL_DAILY_LIMIT_EXCEEDED',
                'limit_minutes' => $dailyLimit,
                'existing_minutes' => $dailyExisting,
                'candidate_minutes' => $candidateMinutes,
                'total_minutes' => $dailyTotal,
            ];
        }

        $weeklyExisting = 0;
        $weeklyLimit = $this->nullableInt($policy['legal_maximum_minutes_weekly'] ?? null);

        // Pasal 26(2): the 18-hour weekly limit does not include
        // overtime performed on weekly rest/public holidays.
        if ($weeklyLimit !== null && $dayType === 'WORKDAY') {
            $weeklyExisting = $this->activeWorkdayMinutesForWeek($employeeId, $date, $excludeSubmissionId);
            $weeklyTotal = $weeklyExisting + $candidateMinutes;

            if ($weeklyTotal > $weeklyLimit) {
                $violations[] = [
                    'code' => 'LEGAL_WEEKLY_LIMIT_EXCEEDED',
                    'limit_minutes' => $weeklyLimit,
                    'existing_minutes' => $weeklyExisting,
                    'candidate_minutes' => $candidateMinutes,
                    'total_minutes' => $weeklyTotal,
                ];
            }
        }

        $monthlyExisting = $this->activeMinutesForMonth($employeeId, $date, $excludeSubmissionId);
        $monthlyTotal = $monthlyExisting + $candidateMinutes;

        $companyMonthlyLimit = $this->nullableInt(
            $policy['maximum_minutes_monthly'] ?? null
        );

        if ($companyMonthlyLimit !== null && $monthlyTotal > $companyMonthlyLimit) {
            $violations[] = [
                'code' => 'COMPANY_MONTHLY_LIMIT_EXCEEDED',
                'limit_minutes' => $companyMonthlyLimit,
                'existing_minutes' => $monthlyExisting,
                'candidate_minutes' => $candidateMinutes,
                'total_minutes' => $monthlyTotal,
            ];
        }

        $legalMonthlyLimit = $this->nullableInt(
            $policy['legal_maximum_minutes_monthly'] ?? null
        );

        if ($legalMonthlyLimit !== null && $monthlyTotal > $legalMonthlyLimit) {
            $violations[] = [
                'code' => 'LEGAL_MONTHLY_LIMIT_EXCEEDED',
                'limit_minutes' => $legalMonthlyLimit,
                'existing_minutes' => $monthlyExisting,
                'candidate_minutes' => $candidateMinutes,
                'total_minutes' => $monthlyTotal,
            ];
        }

        return [
            'allowed' => $violations === [],
            'candidate_minutes' => $candidateMinutes,
            'daily_existing_minutes' => $dailyExisting,
            'weekly_existing_minutes' => $weeklyExisting,
            'monthly_existing_minutes' => $monthlyExisting,
            'violations' => $violations,
        ];
    }

    private function baseQuery(int $employeeId, ?string $excludeSubmissionId = null): Builder
    {
        return OvertimeSubmission::query()
            ->whereHas(
                'user.employee',
                fn (Builder $employee) => $employee->whereKey($employeeId)
            )
            ->whereIn('status', self::ACTIVE_STATUSES)
            ->where('eligibility_status', 'ELIGIBLE')
            ->where('recognized_minutes', '>', 0)
            ->when($excludeSubmissionId !== null, fn (Builder $query) => $query->where($this->qualifySubmissionKey(), '!=', $excludeSubmissionId));
    }

    public function activeMinutesForDate(
        int $employeeId,
        CarbonInterface $date,
        ?string $excludeSubmissionId = null,
    ): int {
        return (int) $this->baseQuery($employeeId, $excludeSubmissionId)
            ->whereDate('overtime_date', $date->toDateString())
            ->sum('recognized_minutes');
    }

    public function activeWorkdayMinutesForWeek(
        int $employeeId,
        CarbonInterface $date,
        ?string $excludeSubmissionId = null,
    ): int {
        $start = $date->copy()->startOfWeek();
        $end = $date->copy()->endOfWeek();

        return (int) $this->baseQuery($employeeId, $excludeSubmissionId)
            ->whereBetween('overtime_date', [
                $start->toDateString(),
                $end->toDateString(),
            ])
            ->where('category', 'hari_kerja')
            ->sum('recognized_minutes');
    }

    public function activeMinutesForMonth(
        int $employeeId,
        CarbonInterface $date,
        ?string $excludeSubmissionId = null,
    ): int {
        return (int) $this->baseQuery($employeeId, $excludeSubmissionId)
            ->whereYear('overtime_date', $date->year)
            ->whereMonth('overtime_date', $date->month)
            ->sum('recognized_minutes');
    }

    private function qualifySubmissionKey(): string
    {
        return (new OvertimeSubmission())->qualifyColumn('id');
    }

    private function nullableInt(mixed $value): ?int
    {
        return $value === null ? null : (int) $value;
    }
}

<?php

namespace App\Services\Overtime;

use App\Models\AttendanceLog;
use App\Models\OvertimeSubmission;
use Carbon\Carbon;
use Carbon\CarbonInterface;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class OvertimeReconciliationService
{
    public function __construct(
        private readonly OvertimeCalculationService $calculator,
        private readonly OvertimePolicyResolver $policyResolver,
        private readonly OvertimeLimitService $limitService,
    ) {
    }

    /**
     * Reconcile active PRE_SUBMITTED overtime after the attendance clock-out
     * has been recorded.
     *
     * The attendance log is the source of truth for actual clock-out.
     * Requested end_time / estimated_hours are never overwritten.
     */
    public function reconcileAfterClockOut(
        AttendanceLog $attendance,
        ?CarbonInterface $clockOutAt = null,
    ): ?OvertimeSubmission {
        if (! $attendance->jam_pulang) {
            return null;
        }

        $date = Carbon::parse($attendance->tanggal)->startOfDay();
        $clockOut = $clockOutAt
            ? $clockOutAt->copy()
            : Carbon::parse($date->format('Y-m-d') . ' ' . $attendance->jam_pulang);

        $submission = OvertimeSubmission::query()
            ->whereHas(
                'user.employee',
                fn ($employeeQuery) => $employeeQuery->whereKey($attendance->employee_id)
            )
            ->whereDate('overtime_date', $date->toDateString())
            ->where('submission_mode', 'PRE_SUBMITTED')
            ->whereIn('status', [
                'PENDING_VP',
                'APPROVED_VP',
            ])
            ->lockForUpdate()
            ->latest('created_at')
            ->first();

        if (! $submission) {
            return null;
        }

        return $this->reconcile($submission, $attendance, $clockOut);
    }

    /**
     * Reconcile one submission using the already locked attendance row.
     */
    public function reconcile(
        OvertimeSubmission $submission,
        AttendanceLog $attendance,
        ?CarbonInterface $clockOutAt = null,
    ): OvertimeSubmission {
        if (! $attendance->jam_pulang) {
            throw new RuntimeException('Attendance clock-out belum tersedia untuk reconciliation.');
        }

        $date = Carbon::parse($submission->overtime_date)->startOfDay();

        $requestedStart = Carbon::parse(
            $date->format('Y-m-d') . ' ' . $submission->start_time
        );

        $actualEnd = $clockOutAt
            ? $clockOutAt->copy()
            : Carbon::parse(
                $date->format('Y-m-d') . ' ' . $attendance->jam_pulang
            );

        $policy = $this->snapshotPolicy($submission);

        $calculation = $this->calculator->calculate(
            $date,
            $requestedStart,
            $actualEnd,
            $policy,
        );

        $candidateMinutes = (int) $calculation['recognized_minutes'];

        // Exclude this submission's old recognized minutes from aggregate
        // limits; otherwise reconciliation would count the same OT twice.
        $limitCheck = $this->limitService->validate(
            $attendance->employee_id,
            $date,
            $candidateMinutes,
            $policy,
            $submission->id,
        );

        $eligibility = $calculation['eligibility'];
        $recognizedMinutes = $candidateMinutes;

        if (! $limitCheck['allowed']) {
            $eligibility = 'NOT_ELIGIBLE_LIMIT';
            $recognizedMinutes = 0;
        }

        $submission->forceFill([
            'attendance_log_id' => $attendance->id,
            'actual_start_time' => $requestedStart->format('H:i:s'),
            'actual_end_time' => $actualEnd->format('H:i:s'),
            'actual_minutes' => (int) $calculation['actual_minutes'],
            'recognized_minutes' => $recognizedMinutes,
            'eligibility_status' => $eligibility,
        ])->save();

        return $submission->refresh();
    }

    /**
     * Prefer the policy captured at submission time so a later policy change
     * cannot silently rewrite an already-submitted transaction.
     */
    private function snapshotPolicy(OvertimeSubmission $submission): array
    {
        $snapshot = DB::table('overtime_policy_snapshots')
            ->where('overtime_submission_id', $submission->id)
            ->value('policy');

        if ($snapshot) {
            $decoded = json_decode($snapshot, true);

            if (is_array($decoded) && isset($decoded['day_type'])) {
                return $decoded;
            }
        }

        return $this->policyResolver->resolve(
            Carbon::parse($submission->overtime_date)
        );
    }
}

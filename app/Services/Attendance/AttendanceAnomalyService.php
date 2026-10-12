<?php

namespace App\Services\Attendance;

use App\Models\AttendanceAnomaly;
use App\Models\AttendanceLog;
use App\Models\OvertimeSubmission;
use Illuminate\Support\Facades\DB;

class AttendanceAnomalyService
{
    /**
     * Scan attendance and overtime records for reviewable anomalies.
     * The scan does not modify source attendance or overtime records.
     */
    public function scan(?string $from = null, ?string $until = null): array
    {
        $logs = AttendanceLog::query()
            ->with('employee')
            ->when($from, fn ($query) => $query->whereDate('tanggal', '>=', $from))
            ->when($until, fn ($query) => $query->whereDate('tanggal', '<=', $until))
            ->get();

        $found = [];

        foreach ($logs as $log) {
            if ($log->jam_pulang && ! $log->jam_masuk) {
                $found[] = $this->candidate(
                    'MISSING_CLOCK_IN', 'critical', $log, null,
                    ['message' => 'Ada clock-out tanpa clock-in.']
                );
            }

            if ($log->jam_masuk && $log->jam_pulang && $log->jam_pulang < $log->jam_masuk) {
                $found[] = $this->candidate(
                    'INVALID_TIME_ORDER', 'critical', $log, null,
                    ['message' => 'Jam pulang lebih awal dari jam masuk.']
                );
            }

            if ((int) $log->jumlah_scan > 2) {
                $found[] = $this->candidate(
                    'DUPLICATE_SCAN', 'warning', $log, null,
                    ['jumlah_scan' => (int) $log->jumlah_scan]
                );
            }

            if (
                $log->jam_masuk
                && $log->jam_pulang
                && $log->tanggal?->isWeekday()
                && $log->jam_pulang < '07:30:00'
            ) {
                $found[] = $this->candidate(
                    'SUSPICIOUS_CLOCK_OUT', 'warning', $log, null,
                    ['message' => 'Jam pulang sebelum jam kerja dimulai.']
                );
            }
        }

        $overtimes = OvertimeSubmission::query()
            ->with(['user.employee', 'attendanceLog'])
            ->when($from, fn ($query) => $query->whereDate('overtime_date', '>=', $from))
            ->when($until, fn ($query) => $query->whereDate('overtime_date', '<=', $until))
            ->get();

        foreach ($overtimes as $overtime) {
            $actualMinutes = $overtime->actual_minutes;
            $recognizedMinutes = $overtime->recognized_minutes;
            $excessMinutes = (int) ($overtime->excess_minutes ?? 0);

            // A recorded excess is reviewable information, not automatic rejection.
            if ($excessMinutes > 0) {
                $found[] = $this->candidate(
                    'OT_EXCESS_RECORDED', 'warning', $overtime->attendanceLog, $overtime,
                    [
                        'message' => 'Durasi lembur aktual melebihi durasi yang diakui; excess tidak dihitung ke kuota.',
                        'actual_minutes' => $actualMinutes === null ? null : (int) $actualMinutes,
                        'recognized_minutes' => $recognizedMinutes === null ? null : (int) $recognizedMinutes,
                        'excess_minutes' => $excessMinutes,
                    ]
                );
            }

            if (
                $actualMinutes !== null
                && $recognizedMinutes !== null
                && (int) $recognizedMinutes > (int) $actualMinutes
            ) {
                $found[] = $this->candidate(
                    'RECOGNIZED_EXCEEDS_ACTUAL', 'critical', $overtime->attendanceLog, $overtime,
                    [
                        'message' => 'Durasi lembur yang diakui melebihi durasi aktual.',
                        'actual_minutes' => (int) $actualMinutes,
                        'recognized_minutes' => (int) $recognizedMinutes,
                    ]
                );
            }

            if ($overtime->actual_end_time && ! $overtime->attendance_log_id) {
                $found[] = $this->candidate(
                    'OT_WITHOUT_ATTENDANCE_LINK', 'critical', null, $overtime,
                    ['message' => 'Actual end time tidak memiliki sumber AttendanceLog.']
                );
            }

            if (
                $overtime->status === 'VERIFIED_HR'
                && ($overtime->eligibility_status !== 'ELIGIBLE' || (int) $recognizedMinutes <= 0)
            ) {
                $found[] = $this->candidate(
                    'INVALID_VERIFIED_OT', 'critical', $overtime->attendanceLog, $overtime,
                    [
                        'message' => 'Pengajuan berstatus VERIFIED_HR tetapi eligibility atau durasi yang diakui tidak valid.',
                        'eligibility_status' => $overtime->eligibility_status,
                        'recognized_minutes' => $recognizedMinutes === null ? null : (int) $recognizedMinutes,
                    ]
                );
            }
        }

        return $found;
    }

    /**
     * Persist current scan results without resetting an already reviewed status.
     */
    public function sync(?string $from = null, ?string $until = null): array
    {
        $candidates = $this->scan($from, $until);

        DB::transaction(function () use ($candidates): void {
            foreach ($candidates as $candidate) {
                $fingerprint = $candidate['fingerprint'];
                $attributes = $candidate;
                unset($attributes['fingerprint'], $attributes['status']);

                $anomaly = AttendanceAnomaly::query()->firstOrNew([
                    'fingerprint' => $fingerprint,
                ]);

                $isNew = ! $anomaly->exists;
                $anomaly->fill($attributes);

                if ($isNew) {
                    $anomaly->status = 'OPEN';
                }

                $anomaly->save();
            }
        });

        return $candidates;
    }

    public function resolve(
        AttendanceAnomaly $anomaly,
        int $userId,
        string $status,
        ?string $notes
    ): AttendanceAnomaly {
        abort_unless(in_array($status, ['REVIEWED', 'RESOLVED', 'IGNORED'], true), 422);

        $anomaly->update([
            'status' => $status,
            'resolved_by' => $userId,
            'resolved_at' => now(),
            'resolution_notes' => $notes,
        ]);

        return $anomaly->fresh();
    }

    private function candidate(
        string $type,
        string $severity,
        ?AttendanceLog $log,
        ?OvertimeSubmission $overtime,
        array $details
    ): array {
        $employeeId = $log?->employee_id ?? $overtime?->user?->employee?->id;
        $date = $log?->tanggal?->toDateString() ?? $overtime?->overtime_date?->toDateString();

        $fingerprint = hash('sha256', implode('|', [
            $type,
            (string) ($employeeId ?? ''),
            (string) ($log?->id ?? ''),
            (string) ($overtime?->id ?? ''),
            (string) ($date ?? ''),
            (string) json_encode($details),
        ]));

        return [
            'fingerprint' => $fingerprint,
            'type' => $type,
            'severity' => $severity,
            'employee_id' => $employeeId,
            'attendance_log_id' => $log?->id,
            'overtime_submission_id' => $overtime?->id,
            'tanggal' => $date,
            'details' => $details,
        ];
    }
}

<?php

namespace App\Services\Attendance;

use App\Models\AttendanceAnomaly;
use App\Models\AttendanceLog;
use App\Models\OvertimeSubmission;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class AttendanceAnomalyService
{
    public function scan(?string $from = null, ?string $until = null): array
    {
        $logs = AttendanceLog::query()->with('employee')->when($from, fn($q) => $q->whereDate('tanggal', '>=', $from))->when($until, fn($q) => $q->whereDate('tanggal', '<=', $until))->get();
        $found = [];
        foreach ($logs as $log) {
            if ($log->jam_pulang && ! $log->jam_masuk) $found[] = $this->candidate('MISSING_CLOCK_IN', 'critical', $log, null, ['message'=>'Ada clock-out tanpa clock-in.']);
            if ($log->jam_masuk && $log->jam_pulang && $log->jam_pulang < $log->jam_masuk) $found[] = $this->candidate('INVALID_TIME_ORDER', 'critical', $log, null, ['message'=>'Jam pulang lebih awal dari jam masuk.']);
            if ((int) $log->jumlah_scan > 2) $found[] = $this->candidate('DUPLICATE_SCAN', 'warning', $log, null, ['jumlah_scan'=>$log->jumlah_scan]);
            if ($log->jam_masuk && $log->jam_pulang && $log->tanggal?->isWeekday() && $log->jam_pulang < '07:30:00') $found[] = $this->candidate('SUSPICIOUS_CLOCK_OUT', 'warning', $log, null, ['message'=>'Jam pulang sebelum jam kerja dimulai.']);
        }

        $ots = OvertimeSubmission::query()->with('user.employee')->when($from, fn($q) => $q->whereDate('overtime_date', '>=', $from))->when($until, fn($q) => $q->whereDate('overtime_date', '<=', $until))->get();
        foreach ($ots as $ot) {
            if ($ot->actual_minutes !== null && $ot->recognized_minutes !== null && $ot->recognized_minutes > $ot->actual_minutes) $found[] = $this->candidate('RECOGNIZED_EXCEEDS_ACTUAL', 'critical', $ot->attendanceLog, $ot, ['actual_minutes'=>$ot->actual_minutes,'recognized_minutes'=>$ot->recognized_minutes]);
            if ($ot->actual_end_time && ! $ot->attendance_log_id) $found[] = $this->candidate('OT_WITHOUT_ATTENDANCE_LINK', 'critical', null, $ot, ['message'=>'Actual end time tidak memiliki sumber AttendanceLog.']);
            if ($ot->status === 'VERIFIED_HR' && ($ot->eligibility_status !== 'ELIGIBLE' || (int) $ot->recognized_minutes <= 0)) $found[] = $this->candidate('INVALID_VERIFIED_OT', 'critical', $ot->attendanceLog, $ot, ['eligibility_status'=>$ot->eligibility_status,'recognized_minutes'=>$ot->recognized_minutes]);
        }

        return $found;
    }

    public function sync(?string $from = null, ?string $until = null): array
    {
        $candidates = $this->scan($from, $until);
        foreach ($candidates as $candidate) {
            AttendanceAnomaly::updateOrCreate(['fingerprint'=>$candidate['fingerprint']], $candidate);
        }
        return $candidates;
    }

    public function resolve(AttendanceAnomaly $anomaly, int $userId, string $status, ?string $notes): AttendanceAnomaly
    {
        abort_unless(in_array($status, ['REVIEWED','RESOLVED','IGNORED'], true), 422);
        $before = $anomaly->toArray();
        $anomaly->update(['status'=>$status,'resolved_by'=>$userId,'resolved_at'=>now(),'resolution_notes'=>$notes]);
        return $anomaly->fresh();
    }

    private function candidate(string $type, string $severity, ?AttendanceLog $log, ?OvertimeSubmission $ot, array $details): array
    {
        $employeeId = $log?->employee_id ?? $ot?->user?->employee?->id;
        $date = $log?->tanggal?->toDateString() ?? $ot?->overtime_date?->toDateString();
        $fingerprint = hash('sha256', implode('|', [$type, $employeeId, $log?->id, $ot?->id, $date, json_encode($details)]));
        return compact('fingerprint','type','severity','employeeId','date','details') + ['employee_id'=>$employeeId,'attendance_log_id'=>$log?->id,'overtime_submission_id'=>$ot?->id,'tanggal'=>$date,'status'=>'OPEN'];
    }
}

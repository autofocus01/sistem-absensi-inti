<?php

use App\Models\AttendanceLog;
use App\Models\Division;
use App\Models\Employee;
use App\Models\OvertimeSubmission;
use App\Models\User;
use App\Services\Overtime\OvertimeReconciliationService;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

function reconciliationEmployee(): array
{
    $user = User::factory()->create([
        'role' => 'karyawan',
    ]);

    $division = Division::create([
        'nama' => 'Reconciliation Test ' . $user->id,
        'lini' => 'Test',
    ]);

    $employee = Employee::create([
        'nipeg' => 'REC-' . $user->id,
        'nama' => $user->name,
        'division_id' => $division->id,
        'user_id' => $user->id,
    ]);

    return [$user, $employee, $division];
}

function reconciliationAttendance(Employee $employee, string $date, string $clockOut): AttendanceLog
{
    return AttendanceLog::forceCreate([
        'employee_id' => $employee->id,
        'tanggal' => $date,
        'jam_masuk' => '07:30:00',
        'jam_pulang' => $clockOut,
        'sumber' => 'self_service',
        'jumlah_scan' => 2,
    ]);
}

function reconciliationSubmission(
    User $user,
    Division $division,
    string $date,
    string $requestedEnd = '19:00:00',
): OvertimeSubmission {
    $companyRuleId = DB::table('company_overtime_rules')->insertGetId([
        'rule_code' => 'TEST-REC-' . $user->id,
        'rule_name' => 'Reconciliation Test',
        'day_type' => 'WORKDAY',
        'normal_end' => '16:30:00',
        'minimum_minutes' => 30,
        'maximum_minutes_event' => null,
        'maximum_minutes_monthly' => null,
        'rounding_method' => 'floor',
        'rounding_interval' => 30,
        'requires_request' => true,
        'allows_post_submission' => true,
        'requires_vp_approval' => true,
        'requires_hr_approval' => true,
        'effective_from' => '2026-01-01',
        'effective_until' => null,
        'status' => 'active',
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    $submission = OvertimeSubmission::create([
        'user_id' => $user->id,
        'division_id' => $division->id,
        'overtime_date' => $date,
        'start_time' => '16:30:00',
        'end_time' => $requestedEnd,
        'estimated_hours' => 2.5,
        'category' => 'hari_kerja',
        'reason' => 'Reconciliation test',
        'submission_mode' => 'PRE_SUBMITTED',
        'status' => 'PENDING_VP',
        'actual_start_time' => '16:30:00',
        'actual_end_time' => $requestedEnd,
        'actual_minutes' => 150,
        'recognized_minutes' => 150,
        'eligibility_status' => 'ELIGIBLE',
    ]);

    DB::table('overtime_policy_snapshots')->insert([
        'overtime_submission_id' => $submission->id,
        'legal_rule_id' => null,
        'company_rule_id' => $companyRuleId,
        'policy' => json_encode([
            'day_type' => 'WORKDAY',
            'normal_end' => '16:30:00',
            'minimum_minutes' => 30,
            'rounding_method' => 'floor',
            'rounding_interval' => 30,
            'maximum_minutes_event' => null,
            'legal_maximum_minutes_daily' => 240,
            'legal_maximum_minutes_weekly' => 1080,
            'legal_maximum_minutes_monthly' => null,
            'maximum_minutes_monthly' => null,
        ]),
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    return $submission;
}

test('PRE_SUBMITTED direkonsiliasi memakai clock-out aktual dan tidak mengubah requested end', function () {
    [$user, $employee, $division] = reconciliationEmployee();

    $attendance = reconciliationAttendance(
        $employee,
        '2026-09-21',
        '18:12:00',
    );

    $submission = reconciliationSubmission(
        $user,
        $division,
        '2026-09-21',
        '19:00:00',
    );

    $result = app(OvertimeReconciliationService::class)
        ->reconcile($submission, $attendance);

    expect($result->end_time)->toBe('19:00:00')
->and($result->estimated_hours)->toBe(2.5)
        ->and($result->actual_end_time)->toBe('18:12:00')
        ->and($result->actual_minutes)->toBe(102)
        ->and($result->recognized_minutes)->toBe(90)
        ->and($result->eligibility_status)->toBe('ELIGIBLE');
});

test('clock-out sebelum minimum OT membuat submission tidak eligible', function () {
    [$user, $employee, $division] = reconciliationEmployee();

    $attendance = reconciliationAttendance(
        $employee,
        '2026-09-22',
        '16:59:00',
    );

    $submission = reconciliationSubmission(
        $user,
        $division,
        '2026-09-22',
        '18:00:00',
    );

    $result = app(OvertimeReconciliationService::class)
        ->reconcile($submission, $attendance);

    expect($result->actual_minutes)->toBe(29)
        ->and($result->recognized_minutes)->toBe(0)
        ->and($result->eligibility_status)->toBe('NOT_ELIGIBLE');
});

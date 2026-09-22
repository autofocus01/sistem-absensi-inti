<?php

use App\Models\AttendanceLog;
use App\Models\CompanyOvertimeRule;
use App\Models\Division;
use App\Models\Employee;
use App\Models\LegalOvertimeRule;
use App\Models\OvertimeSubmission;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;

uses(RefreshDatabase::class);

function makeClockOutEmployee(): array
{
    $user = User::factory()->create(['role' => 'karyawan']);

    $division = Division::create([
        'nama' => 'Clock Out OT ' . $user->id,
        'lini' => 'Test',
    ]);

    $employee = Employee::create([
        'nipeg' => 'CLK-' . $user->id,
        'nama' => $user->name,
        'division_id' => $division->id,
        'user_id' => $user->id,
    ]);

    LegalOvertimeRule::create([
        'rule_code' => 'LEGAL-CLK-' . $user->id,
        'regulation_name' => 'Test Regulation',
        'regulation_number' => 'TEST',
        'article_reference' => 'TEST',
        'day_type' => 'WORKDAY',
        'minimum_minutes' => 30,
        'maximum_minutes_daily' => 240,
        'maximum_minutes_weekly' => 1080,
        'maximum_minutes_monthly' => null,
        'effective_from' => '2026-01-01',
        'effective_until' => null,
        'status' => 'active',
        'source_document' => 'TEST',
    ]);

    $companyRule = CompanyOvertimeRule::create([
        'rule_code' => 'COMP-CLK-' . $user->id,
        'rule_name' => 'Clock-out Reconciliation',
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
    ]);

    return [$user, $employee, $division, $companyRule];
}

test('employee clock-out automatically reconciles a pre-submitted overtime request', function () {
    [$user, $employee, $division, $companyRule] = makeClockOutEmployee();

    Carbon::setTestNow(Carbon::parse('2026-09-21 18:12:00', 'Asia/Jakarta'));

    $date = '2026-09-21';

    $attendance = AttendanceLog::create([
        'employee_id' => $employee->id,
        'tanggal' => $date,
        'jam_masuk' => '07:30:00',
        'jam_pulang' => null,
        'sumber' => 'self_service',
        'jumlah_scan' => 1,
    ]);

    $submission = OvertimeSubmission::create([
        'user_id' => $user->id,
        'division_id' => $division->id,
        'overtime_date' => $date,
        'start_time' => '16:30:00',
        'end_time' => '19:00:00',
        'estimated_hours' => 2.5,
        'category' => 'hari_kerja',
        'reason' => 'Integration test',
        'submission_mode' => 'PRE_SUBMITTED',
        'status' => 'PENDING_VP',
        'actual_start_time' => '16:30:00',
        'actual_end_time' => '19:00:00',
        'actual_minutes' => 150,
        'recognized_minutes' => 150,
        'eligibility_status' => 'ELIGIBLE',
    ]);

    DB::table('overtime_policy_snapshots')->insert([
        'overtime_submission_id' => $submission->id,
        'legal_rule_id' => LegalOvertimeRule::query()->where('rule_code', 'LEGAL-CLK-' . $user->id)->value('id'),
        'company_rule_id' => $companyRule->id,
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

    $this->actingAs($user)
        ->post(route('attendance.my.clock-out'))
        ->assertRedirect();

    $attendance->refresh();
    $submission->refresh();

    expect($attendance->jam_pulang)->toBe('18:12:00')
        ->and($submission->actual_end_time)->toBe('18:12:00')
        ->and($submission->actual_minutes)->toBe(102)
        ->and($submission->recognized_minutes)->toBe(90)
        ->and($submission->eligibility_status)->toBe('ELIGIBLE')
        ->and($submission->end_time)->toBe('19:00:00')
        ->and($submission->estimated_hours)->toBe(2.5);
})->afterEach(function () {
    Carbon::setTestNow();
});

test('employee clock-out before requested overtime start is recorded but recognized overtime stays zero', function () {
    [$user, $employee, $division, $companyRule] = makeClockOutEmployee();

    Carbon::setTestNow(Carbon::parse('2026-09-21 10:00:00', 'Asia/Jakarta'));

    $date = '2026-09-21';

    $attendance = AttendanceLog::create([
        'employee_id' => $employee->id,
        'tanggal' => $date,
        'jam_masuk' => '07:30:00',
        'jam_pulang' => null,
        'sumber' => 'self_service',
        'jumlah_scan' => 1,
    ]);

    $submission = OvertimeSubmission::create([
        'user_id' => $user->id,
        'division_id' => $division->id,
        'overtime_date' => $date,
        'start_time' => '16:30:00',
        'end_time' => '19:00:00',
        'estimated_hours' => 2.5,
        'category' => 'hari_kerja',
        'reason' => 'Early clock-out regression test',
        'submission_mode' => 'PRE_SUBMITTED',
        'status' => 'PENDING_VP',
        'actual_start_time' => '16:30:00',
        'actual_end_time' => '19:00:00',
        'actual_minutes' => 150,
        'recognized_minutes' => 150,
        'eligibility_status' => 'ELIGIBLE',
    ]);

    DB::table('overtime_policy_snapshots')->insert([
        'overtime_submission_id' => $submission->id,
        'legal_rule_id' => LegalOvertimeRule::query()->where('rule_code', 'LEGAL-CLK-' . $user->id)->value('id'),
        'company_rule_id' => $companyRule->id,
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

    $this->actingAs($user)
        ->post(route('attendance.my.clock-out'))
        ->assertRedirect();

    $attendance->refresh();
    $submission->refresh();

    expect($attendance->jam_pulang)->toBe('10:00:00')
        ->and($submission->actual_end_time)->toBe('10:00:00')
        ->and($submission->actual_minutes)->toBe(0)
        ->and($submission->recognized_minutes)->toBe(0)
        ->and($submission->eligibility_status)->toBe('INVALID_INTERVAL');
})->afterEach(function () {
    Carbon::setTestNow();
});

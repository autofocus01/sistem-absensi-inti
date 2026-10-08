<?php

use App\Models\AttendanceLog;
use App\Models\CompanyOvertimeRule;
use App\Models\Division;
use App\Models\LegalOvertimeRule;
use App\Models\SystemConfiguration;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

function seedOvertimeSubmissionPolicy(): void
{
    SystemConfiguration::create([
        'key' => 'overtime.timezone',
        'value' => 'Asia/Jakarta',
        'value_type' => 'string',
        'status' => 'active',
    ]);

    foreach (['WORKDAY', 'WEEKEND', 'NATIONAL_HOLIDAY'] as $dayType) {
        LegalOvertimeRule::create([
            'rule_code' => 'REG-' . $dayType,
            'regulation_name' => 'Test Regulation',
            'day_type' => $dayType,
            'effective_from' => '2026-01-01',
            'status' => 'active',
            'maximum_minutes_daily' => $dayType === 'WORKDAY' ? 240 : null,
            'maximum_minutes_weekly' => $dayType === 'WORKDAY' ? 1080 : null,
        ]);

        CompanyOvertimeRule::create([
            'rule_code' => 'COMP-' . $dayType,
            'rule_name' => 'Test ' . $dayType,
            'day_type' => $dayType,
            'normal_end' => $dayType === 'WORKDAY' ? '16:30:00' : null,
            'minimum_minutes' => 30,
            'rounding_method' => 'floor',
            'rounding_interval' => 30,
            'maximum_minutes_event' => 240,
            'maximum_minutes_monthly' => 1000,
            'requires_request' => true,
            'allows_post_submission' => true,
            'requires_vp_approval' => true,
            'requires_hr_approval' => true,
            'effective_from' => '2026-01-01',
            'status' => 'active',
        ]);
    }
}

function makeOvertimeSubmissionEmployee(): array
{
    $user = User::factory()->create(['role' => 'karyawan']);
    $division = Division::create([
        'nama' => 'OT Business Logic ' . $user->id,
        'lini' => 'Test',
    ]);

    $employee = testPersistEmployee([
        'nipeg' => 'OT-BL-' . $user->id,
        'nama' => $user->name,
        'jabatan' => 'Staff',
        'division_id' => $division->id,
        'user_id' => $user->id,
        'band_code' => 2,
    ]);

    AttendanceLog::create([
        'employee_id' => $employee->id,
        'tanggal' => '2026-09-18',
        'jam_masuk' => '07:30:00',
        'jam_pulang' => '17:30:00',
        'sumber' => 'self_service',
        'jumlah_scan' => 2,
    ]);

    return [$user, $employee, $division];
}

beforeEach(function () {
    seedOvertimeSubmissionPolicy();
});

it('rejects an overtime interval that differs from attendance evidence', function () {
    [$user] = makeOvertimeSubmissionEmployee();

    $response = $this->actingAs($user)->postJson(
        route('overtime.store'),
        [
            'overtime_date' => '2026-09-18',
            'submission_mode' => 'POST_SUBMITTED',
            'start_time' => '16:30',
            'end_time' => '17:00',
            'reason' => 'Interval mismatch regression',
            'employee_consent' => '1',
        ]
    );

    $response->assertStatus(422)
        ->assertJsonPath('attendance.overtime_start', '16:30')
        ->assertJsonPath('attendance.overtime_end', '17:30');
});

it('persists canonical overtime interval from attendance evidence', function () {
    [$user] = makeOvertimeSubmissionEmployee();

    $response = $this->actingAs($user)->postJson(
        route('overtime.store'),
        [
            'overtime_date' => '2026-09-18',
            'submission_mode' => 'POST_SUBMITTED',
            'start_time' => '16:30',
            'end_time' => '17:30',
            'reason' => 'Evidence aligned regression',
            'employee_consent' => '1',
        ]
    );

    $response->assertCreated()
        ->assertJsonPath('data.start_time', '16:30:00')
        ->assertJsonPath('data.end_time', '17:30:00')
        ->assertJsonPath('data.actual_minutes', 60)
        ->assertJsonPath('data.recognized_minutes', 60);
});



it('submits four recognized hours and records one hour excess when weekday overtime is five hours', function () {
    [$user, $employee] = makeOvertimeSubmissionEmployee();

    AttendanceLog::query()
        ->where('employee_id', $employee->id)
        ->whereDate('tanggal', '2026-09-18')
        ->update(['jam_pulang' => '21:30:00']);

    $response = $this->actingAs($user)->postJson(
        route('overtime.store'),
        [
            'overtime_date' => '2026-09-18',
            'submission_mode' => 'POST_SUBMITTED',
            'start_time' => '16:30',
            'end_time' => '21:30',
            'reason' => 'Five-hour overtime cap regression',
            'employee_consent' => '1',
        ]
    );

    $response->assertCreated()
        ->assertJsonPath('data.actual_minutes', 300)
        ->assertJsonPath('data.recognized_minutes', 240)
        ->assertJsonPath('data.excess_minutes', 60)
        ->assertJsonPath('calculation.recognized_minutes', 240)
        ->assertJsonPath('calculation.excess_minutes', 60);

    $submission = \App\Models\OvertimeSubmission::query()
        ->where('user_id', $user->id)
        ->firstOrFail();

    expect($submission->recognized_minutes)->toBe(240)
        ->and($submission->excess_minutes)->toBe(60)
        ->and($submission->quota_reserved_minutes)->toBe(240)
        ->and($submission->quota_used_minutes)->toBe(0);
});

it('does not create a zombie submission when recognized overtime is zero', function () {
    [$user, $employee] = makeOvertimeSubmissionEmployee();

    AttendanceLog::query()
        ->where('employee_id', $employee->id)
        ->whereDate('tanggal', '2026-09-18')
        ->update(['jam_pulang' => '16:59:00']);

    $response = $this->actingAs($user)->postJson(
        route('overtime.store'),
        [
            'overtime_date' => '2026-09-18',
            'submission_mode' => 'POST_SUBMITTED',
            'start_time' => '16:30',
            'end_time' => '16:59',
            'reason' => 'Below minimum regression',
            'employee_consent' => '1',
        ]
    );

    $response->assertStatus(422);

    expect(\App\Models\OvertimeSubmission::query()
        ->where('user_id', $user->id)
        ->count())->toBe(0);
});

<?php

use App\Models\AttendanceLog;
use App\Models\Division;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

function makeClockOutEmployeeForTest(): array
{
    $user = User::factory()->create(['role' => 'karyawan']);
    $division = Division::create(['nama' => 'Clock Out Test ' . $user->id, 'lini' => 'Test']);
    $employee = testPersistEmployee([
        'nipeg' => 'CLK-' . $user->id,
        'nama' => $user->name,
        'division_id' => $division->id,
        'user_id' => $user->id,
        'band_code' => 2,
    ]);
    return [$user, $employee, $division];
}

test('clock out only records attendance and does not reconcile overtime', function () {
    [$user, $employee, $division] = makeClockOutEmployeeForTest();
    Carbon::setTestNow(Carbon::parse('2026-09-22 18:12:00', 'Asia/Jakarta'));
    $date = '2026-09-22';
    $attendance = AttendanceLog::create([
        'employee_id' => $employee->id,
        'tanggal' => $date,
        'jam_masuk' => '07:30:00',
        'jam_pulang' => null,
        'sumber' => 'self_service',
        'jumlah_scan' => 1,
    ]);
    $submission = testPersistOvertimeSubmission([
        'user_id' => $user->id,
        'division_id' => $division->id,
        'overtime_date' => $date,
        'start_time' => '16:30:00',
        'end_time' => '19:00:00',
        'estimated_hours' => 2.5,
        'category' => 'hari_kerja',
        'reason' => 'Regression test',
        'submission_mode' => 'PRE_SUBMITTED',
        'status' => 'PENDING_VP',
        'actual_start_time' => '16:30:00',
        'actual_end_time' => '19:00:00',
        'actual_minutes' => 150,
        'recognized_minutes' => 150,
        'eligibility_status' => 'ELIGIBLE',
    ]);
    $before = $submission->only(['actual_end_time', 'actual_minutes', 'recognized_minutes', 'eligibility_status', 'end_time', 'estimated_hours']);
    $this->actingAs($user)->post(route('attendance.my.clock-out'))->assertRedirect()->assertSessionHas('status');
    $attendance->refresh();
    $submission->refresh();
    expect($attendance->jam_pulang)->toBe('18:12:00')
        ->and($submission->only(array_keys($before)))->toBe($before);
})->afterEach(fn () => Carbon::setTestNow());

test('early clock out records attendance without changing existing overtime submission', function () {
    [$user, $employee, $division] = makeClockOutEmployeeForTest();
    Carbon::setTestNow(Carbon::parse('2026-09-22 10:00:00', 'Asia/Jakarta'));
    $date = '2026-09-22';
    AttendanceLog::create([
        'employee_id' => $employee->id,
        'tanggal' => $date,
        'jam_masuk' => '07:30:00',
        'jam_pulang' => null,
        'sumber' => 'self_service',
        'jumlah_scan' => 1,
    ]);
    $submission = testPersistOvertimeSubmission([
        'user_id' => $user->id,
        'division_id' => $division->id,
        'overtime_date' => $date,
        'start_time' => '16:30:00',
        'end_time' => '19:00:00',
        'estimated_hours' => 2.5,
        'category' => 'hari_kerja',
        'reason' => 'Early clock-out regression',
        'submission_mode' => 'PRE_SUBMITTED',
        'status' => 'PENDING_VP',
        'actual_start_time' => '16:30:00',
        'actual_end_time' => '19:00:00',
        'actual_minutes' => 150,
        'recognized_minutes' => 150,
        'eligibility_status' => 'ELIGIBLE',
    ]);
    $before = $submission->only(['actual_end_time', 'actual_minutes', 'recognized_minutes', 'eligibility_status', 'end_time', 'estimated_hours']);
    $this->actingAs($user)->post(route('attendance.my.clock-out'))->assertRedirect()->assertSessionHas('status');
    $submission->refresh();
    expect($submission->only(array_keys($before)))->toBe($before);
})->afterEach(fn () => Carbon::setTestNow());

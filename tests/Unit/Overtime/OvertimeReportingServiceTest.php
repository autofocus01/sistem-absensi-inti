<?php

use App\Models\Division;
use App\Models\Employee;
use App\Models\OvertimeSubmission;
use App\Models\User;
use App\Services\Overtime\OvertimeReportingService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

function reportingEmployee(): array
{
    $user = User::factory()->create([
        'role' => 'karyawan',
    ]);

    $division = Division::create([
        'nama' => 'Reporting Test ' . $user->id,
        'lini' => 'Test',
    ]);

    $employee = testPersistEmployee([
        'nipeg' => 'RPT-' . $user->id,
        'nama' => $user->name,
        'division_id' => $division->id,
        'user_id' => $user->id,
    ]);

    return [$user, $employee, $division];
}

test('official overtime hanya menghitung VERIFIED_HR yang eligible', function () {
    [$user, $employee, $division] = reportingEmployee();

    $base = [
        'user_id' => $user->id,
        'division_id' => $division->id,
        'overtime_date' => '2026-09-21',
        'start_time' => '16:30',
        'end_time' => '17:30',

        // WAJIB: overtime_submissions.estimated_hours NOT NULL
        'estimated_hours' => 1.0,

        'actual_minutes' => 60,
        'recognized_minutes' => 60,
        'eligibility_status' => 'ELIGIBLE',
        'category' => 'WORKDAY',
        'reason' => 'Test',
    ];

    // Belum disetujui VP → bukan official
    testPersistOvertimeSubmission([
        ...$base,
        'status' => 'PENDING_VP',
    ]);

    // Sudah disetujui VP tetapi belum diverifikasi HR → bukan official
    testPersistOvertimeSubmission([
        ...$base,
        'status' => 'APPROVED_VP',
        'overtime_date' => '2026-09-22',
    ]);

    // Sudah VERIFIED_HR + ELIGIBLE → official
    testPersistOvertimeSubmission([
        ...$base,
        'status' => 'VERIFIED_HR',
        'overtime_date' => '2026-09-23',
    ]);

    // VERIFIED_HR tetapi NOT_ELIGIBLE → bukan official
    testPersistOvertimeSubmission([
        ...$base,
        'status' => 'VERIFIED_HR',
        'eligibility_status' => 'NOT_ELIGIBLE',
        'overtime_date' => '2026-09-24',
    ]);

    $service = app(OvertimeReportingService::class);

    expect(
        $service->monthlyOfficialMinutes(
            $employee->id,
            2026,
            9
        )
    )->toBe(60);

    expect(
        $service->officialEntries(
            $employee->id,
            '2026-09-01',
            '2026-09-30'
        )
    )->toHaveCount(1);
});

test('formatMinutes konsisten untuk timesheet', function () {
    $service = app(OvertimeReportingService::class);

    expect($service->formatMinutes(0))
        ->toBe('0j 00m')
        ->and($service->formatMinutes(30))
        ->toBe('0j 30m')
        ->and($service->formatMinutes(90))
        ->toBe('1j 30m');
});

test('returned to vp dihitung sebagai antrean VP, bukan official atau antrean HR', function () {
    [$user, $employee, $division] = reportingEmployee();

    testPersistOvertimeSubmission([
        'user_id' => $user->id,
        'division_id' => $division->id,
        'overtime_date' => '2026-09-25',
        'start_time' => '16:30',
        'end_time' => '17:30',
        'estimated_hours' => 1.0,
        'actual_minutes' => 60,
        'recognized_minutes' => 60,
        'eligibility_status' => 'ELIGIBLE',
        'category' => 'hari_kerja',
        'reason' => 'Test return to VP',
        'status' => 'RETURNED_TO_VP',
    ]);

    $service = app(OvertimeReportingService::class);

    expect($service->pendingVpMinutes(2026, 9, $division->id))->toBe(60);
    expect($service->pendingHrMinutes(2026, 9, $division->id))->toBe(0);
    expect($service->officialMinutes(2026, 9, $division->id))->toBe(0);
});

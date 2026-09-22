<?php

use App\Models\Division;
use App\Models\Employee;
use App\Models\OvertimeSubmission;
use App\Models\User;
use App\Services\Overtime\OvertimeLimitService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

function limitTestEmployee(): array
{
    $user = User::factory()->create([
        'role' => 'karyawan',
    ]);

    $division = Division::create([
        'nama' => 'Limit Test ' . $user->id,
        'lini' => 'Test',
    ]);

    $employee = Employee::create([
        'nipeg' => 'LIM-' . $user->id,
        'nama' => $user->name,
        'division_id' => $division->id,
        'user_id' => $user->id,
    ]);

    return [$user, $employee, $division];
}

function activeOvertime(
    User $user,
    Division $division,
    string $date,
    int $minutes,
    string $category = 'hari_kerja',
): OvertimeSubmission {
    return OvertimeSubmission::create([
        'user_id' => $user->id,
        'division_id' => $division->id,
        'overtime_date' => $date,
        'start_time' => '16:30',
        'end_time' => '17:00',
        'estimated_hours' => round($minutes / 60, 2),
        'category' => $category,
        'reason' => 'Limit test',
        'status' => 'PENDING_VP',
        'recognized_minutes' => $minutes,
        'eligibility_status' => 'ELIGIBLE',
    ]);
}

test('batas legal harian hari kerja ditegakkan', function () {
    [$user, $employee, $division] = limitTestEmployee();

    activeOvertime($user, $division, '2026-09-21', 210);

    $policy = [
        'day_type' => 'WORKDAY',
        'legal_maximum_minutes_daily' => 240,
        'legal_maximum_minutes_weekly' => 1080,
        'legal_maximum_minutes_monthly' => null,
        'maximum_minutes_monthly' => null,
    ];

    $service = app(OvertimeLimitService::class);

    expect($service->validate(
        $employee->id,
        Carbon::parse('2026-09-21'),
        30,
        $policy,
    )['allowed'])->toBeTrue();

    $result = $service->validate(
        $employee->id,
        Carbon::parse('2026-09-21'),
        60,
        $policy,
    );

    expect($result['allowed'])->toBeFalse()
        ->and($result['violations'][0]['code'])
        ->toBe('LEGAL_DAILY_LIMIT_EXCEEDED');
});

test('batas legal mingguan hanya menghitung lembur hari kerja', function () {
    [$user, $employee, $division] = limitTestEmployee();

    // 17 jam 30 menit OT pada hari kerja dalam minggu yang sama.
    activeOvertime($user, $division, '2026-09-21', 1050);

    // OT weekend tidak boleh ikut menghabiskan kuota 18 jam Pasal 26.
    activeOvertime($user, $division, '2026-09-26', 240, 'akhir_pekan');

    $policy = [
        'day_type' => 'WORKDAY',
        'legal_maximum_minutes_daily' => 240,
        'legal_maximum_minutes_weekly' => 1080,
        'legal_maximum_minutes_monthly' => null,
        'maximum_minutes_monthly' => null,
    ];

    $result = app(OvertimeLimitService::class)->validate(
        $employee->id,
        Carbon::parse('2026-09-25'),
        60,
        $policy,
    );

    expect($result['allowed'])->toBeFalse()
        ->and($result['weekly_existing_minutes'])->toBe(1050)
        ->and($result['violations'][0]['code'])
        ->toBe('LEGAL_WEEKLY_LIMIT_EXCEEDED');
});

test('batas bulanan perusahaan ditegakkan terhadap seluruh lembur aktif', function () {
    [$user, $employee, $division] = limitTestEmployee();

    activeOvertime($user, $division, '2026-09-10', 90);
    activeOvertime($user, $division, '2026-09-12', 60, 'akhir_pekan');

    $policy = [
        'day_type' => 'WORKDAY',
        'legal_maximum_minutes_daily' => 240,
        'legal_maximum_minutes_weekly' => 1080,
        'legal_maximum_minutes_monthly' => null,
        'maximum_minutes_monthly' => 180,
    ];

    $result = app(OvertimeLimitService::class)->validate(
        $employee->id,
        Carbon::parse('2026-09-21'),
        60,
        $policy,
    );

    expect($result['allowed'])->toBeFalse()
        ->and($result['monthly_existing_minutes'])->toBe(150)
        ->and($result['violations'][0]['code'])
        ->toBe('COMPANY_MONTHLY_LIMIT_EXCEEDED');
});

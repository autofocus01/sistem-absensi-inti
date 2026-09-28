<?php

use App\Models\Division;
use App\Models\Employee;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

function makeBandEmployee(int $band): array
{
    $user = User::factory()->create([
        'role' => 'karyawan',
    ]);

    $division = Division::create([
        'nama' => 'Divisi Band Test ' . $user->id,
        'lini' => 'Test',
    ]);

    // user_id sengaja tidak termasuk $fillable pada Employee untuk mencegah
    // mass-assignment/IDOR. Hubungkan relasi secara eksplisit di test.
    $employee = Employee::create([
        'nipeg' => 'BAND-' . $user->id,
        'nama' => 'Employee Band ' . $band,
        'jabatan' => 'Staff',
        'band_code' => $band,
        'division_id' => $division->id,
    ]);

    $employee->user()->associate($user);
    $employee->save();

    return [$user->fresh(), $employee->fresh()];
}

test('band 2 dapat mengajukan lembur', function () {
    [$user] = makeBandEmployee(2);

    $this->actingAs($user)
        ->get(route('overtime.index'))
        ->assertOk();
});

test('band 3 dapat mengajukan lembur', function () {
    [$user] = makeBandEmployee(3);

    $this->actingAs($user)
        ->get(route('overtime.index'))
        ->assertOk();
});

test('band selain 2 dan 3 tidak memenuhi eligibility lembur', function () {
    [, $employee] = makeBandEmployee(4);

    expect(
        app(\App\Services\Overtime\OvertimeEligibilityService::class)
            ->canSubmit($employee)
    )->toBeFalse();
});

test('band 2 memenuhi eligibility lembur', function () {
    [, $employee] = makeBandEmployee(2);

    expect(
        app(\App\Services\Overtime\OvertimeEligibilityService::class)
            ->canSubmit($employee)
    )->toBeTrue();
});

test('band 3 memenuhi eligibility lembur', function () {
    [, $employee] = makeBandEmployee(3);

    expect(
        app(\App\Services\Overtime\OvertimeEligibilityService::class)
            ->canSubmit($employee)
    )->toBeTrue();
});

test('band tanpa kode tidak memenuhi eligibility lembur', function () {
    [, $employee] = makeBandEmployee(0);

    $employee->update([
        'band_code' => null,
    ]);

    expect(
        app(\App\Services\Overtime\OvertimeEligibilityService::class)
            ->canSubmit($employee)
    )->toBeFalse();
});
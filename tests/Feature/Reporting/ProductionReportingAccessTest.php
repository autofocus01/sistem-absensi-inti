<?php

use App\Models\Division;
use App\Models\Employee;
use App\Models\User;

function createReportingEmployee(User $user, string $jabatan = 'Karyawan'): Employee
{
    $division = Division::create([
        'nama' => 'Divisi Test Reporting ' . $user->id,
        'lini' => 'Test',
    ]);

    return Employee::create([
        'user_id' => $user->id,
        'nipeg' => 'UAT-' . $user->id,
        'nama' => 'User Reporting ' . $user->id,
        'jenis_kelamin' => 'L',
        'jabatan' => $jabatan,
        'no_hp' => '081234567891',
        'alamat' => 'Data UAT',
        'division_id' => $division->id,
    ]);
}

it('allows HR admin to open attendance anomalies', function () {
    $user = User::factory()->create([
        'role' => 'hr_admin',
    ]);

    $this->actingAs($user)
        ->get(route('attendance.anomalies'))
        ->assertOk();
});

it('blocks karyawan from HR attendance anomalies', function () {
    $user = User::factory()->create([
        'role' => 'karyawan',
    ]);

    createReportingEmployee($user);

    $this->actingAs($user)
        ->get(route('attendance.anomalies'))
        ->assertForbidden();
});

it('blocks karyawan from team recap', function () {
    $user = User::factory()->create([
        'role' => 'karyawan',
    ]);

    createReportingEmployee($user);

    $this->actingAs($user)
        ->get(route('reports.team'))
        ->assertForbidden();
});

it('allows VP with employee and division to open team recap', function () {
    $user = User::factory()->create([
        'role' => 'vp',
    ]);

    createReportingEmployee($user, 'Vice President');

    $this->actingAs($user)
        ->get(route('reports.team'))
        ->assertOk();
});

it('allows karyawan to open timesheet', function () {
    $user = User::factory()->create([
        'role' => 'karyawan',
    ]);

    createReportingEmployee($user);

    $this->actingAs($user)
        ->get(route('timesheet.index'))
        ->assertOk();
});

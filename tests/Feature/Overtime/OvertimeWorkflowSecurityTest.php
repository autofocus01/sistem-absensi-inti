<?php

use App\Models\Division;
use App\Models\Employee;
use App\Models\OvertimeSubmission;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

/*
|--------------------------------------------------------------------------
| Test Helpers
|--------------------------------------------------------------------------
*/

function createTestDivision(string $name): Division
{
    return Division::create([
        'nama' => $name,
        'lini' => 'Lini Test',
    ]);
}

function createTestEmployee(
    User $user,
    Division $division,
    string $nipeg
): Employee {
    return Employee::create([
        'nipeg' => $nipeg,
        'nama' => $user->name,
        'jabatan' => 'Karyawan Test',
        'division_id' => $division->id,
        'user_id' => $user->id,
    ]);
}

function createTestUser(string $role, string $name = 'Test User'): User
{
    return User::factory()->create([
        'name' => $name,
        'role' => $role,
    ]);
}

function createTestOvertime(
    User $employeeUser,
    Division $division,
    string $status = 'PENDING_VP'
): OvertimeSubmission {
    return OvertimeSubmission::create([
        'user_id' => $employeeUser->id,
        'division_id' => $division->id,

        'overtime_date' => '2026-09-18',
        'start_time' => '16:30:00',
        'end_time' => '17:30:00',

        'estimated_hours' => 1.00,
        'category' => 'hari_kerja',
        'reason' => 'Test security workflow',

        'submission_mode' => 'PRE_SUBMITTED',
        'actual_start_time' => '16:30:00',
        'actual_end_time' => '17:30:00',
        'actual_minutes' => 60,
        'recognized_minutes' => 60,
        'eligibility_status' => 'ELIGIBLE',

        'status' => $status,
    ]);
}

/*
|--------------------------------------------------------------------------
| VP Division Boundary
|--------------------------------------------------------------------------
*/

test('vp hanya dapat melihat pengajuan pending dari division sendiri', function () {
    $divisionA = createTestDivision('Divisi Test A');
    $divisionB = createTestDivision('Divisi Test B');

    $vp = createTestUser('vp', 'VP Division A');
    createTestEmployee($vp, $divisionA, 'VP001');

    $employeeA = createTestUser('karyawan', 'Employee Division A');
    createTestEmployee($employeeA, $divisionA, 'EMP001');

    $employeeB = createTestUser('karyawan', 'Employee Division B');
    createTestEmployee($employeeB, $divisionB, 'EMP002');

    $submissionA = createTestOvertime(
        $employeeA,
        $divisionA,
        'PENDING_VP'
    );

    $submissionB = createTestOvertime(
        $employeeB,
        $divisionB,
        'PENDING_VP'
    );

    $response = $this
        ->actingAs($vp)
        ->getJson(route('overtime.vp.pending'));

    $response
        ->assertSuccessful()
        ->assertJsonFragment([
            'id' => $submissionA->id,
        ])
        ->assertJsonMissing([
            'id' => $submissionB->id,
        ]);
});


test('vp tanpa employee atau division ditolak dan tidak dapat melihat seluruh pending', function () {
    $division = createTestDivision('Divisi Test');

    $employee = createTestUser(
        'karyawan',
        'Employee Test'
    );

    createTestEmployee(
        $employee,
        $division,
        'EMP003'
    );

    createTestOvertime(
        $employee,
        $division,
        'PENDING_VP'
    );

    $vp = createTestUser(
        'vp',
        'VP Tanpa Division'
    );

    /*
     * Sengaja tidak membuat Employee untuk VP.
     *
     * Controller harus fail-closed.
     */
    $response = $this
        ->actingAs($vp)
        ->getJson(route('overtime.vp.pending'));

    $response->assertForbidden();
});


test('vp tidak dapat approve lembur dari division lain', function () {
    $divisionA = createTestDivision('Divisi Test A');
    $divisionB = createTestDivision('Divisi Test B');

    $vp = createTestUser(
        'vp',
        'VP Division A'
    );

    createTestEmployee(
        $vp,
        $divisionA,
        'VP002'
    );

    $employeeB = createTestUser(
        'karyawan',
        'Employee Division B'
    );

    createTestEmployee(
        $employeeB,
        $divisionB,
        'EMP004'
    );

    $submission = createTestOvertime(
        $employeeB,
        $divisionB,
        'PENDING_VP'
    );

    $response = $this
        ->actingAs($vp)
        ->postJson(
            route('overtime.vp.approve', $submission->id),
            [
                'action' => 'APPROVE',
            ]
        );

    $response->assertForbidden();

    expect($submission->fresh()->status)
        ->toBe('PENDING_VP');
});


/*
|--------------------------------------------------------------------------
| VP State Machine
|--------------------------------------------------------------------------
*/

test('vp hanya dapat memproses submission PENDING_VP', function () {
    $division = createTestDivision('Divisi Test');

    $vp = createTestUser('vp', 'VP Test');

    createTestEmployee(
        $vp,
        $division,
        'VP003'
    );

    $employee = createTestUser(
        'karyawan',
        'Employee Test'
    );

    createTestEmployee(
        $employee,
        $division,
        'EMP005'
    );

    $submission = createTestOvertime(
        $employee,
        $division,
        'APPROVED_VP'
    );

    $response = $this
        ->actingAs($vp)
        ->postJson(
            route('overtime.vp.approve', $submission->id),
            [
                'action' => 'APPROVE',
            ]
        );

    $response->assertStatus(409);

    expect($submission->fresh()->status)
        ->toBe('APPROVED_VP');
});


test('vp approve mengubah PENDING_VP menjadi APPROVED_VP', function () {
    $division = createTestDivision('Divisi Test');

    $vp = createTestUser('vp', 'VP Test');

    createTestEmployee(
        $vp,
        $division,
        'VP004'
    );

    $employee = createTestUser(
        'karyawan',
        'Employee Test'
    );

    createTestEmployee(
        $employee,
        $division,
        'EMP006'
    );

    $submission = createTestOvertime(
        $employee,
        $division,
        'PENDING_VP'
    );

    $response = $this
        ->actingAs($vp)
        ->postJson(
            route('overtime.vp.approve', $submission->id),
            [
                'action' => 'APPROVE',
                'notes' => 'Disetujui untuk test.',
            ]
        );

    $response
        ->assertSuccessful()
        ->assertJsonPath(
            'data.status',
            'APPROVED_VP'
        );

    expect($submission->fresh()->status)
        ->toBe('APPROVED_VP');
});


/*
|--------------------------------------------------------------------------
| HR State Boundary
|--------------------------------------------------------------------------
*/

test('hr tidak dapat verify submission yang masih PENDING_VP', function () {
    $division = createTestDivision('Divisi Test');

    $employee = createTestUser(
        'karyawan',
        'Employee Test'
    );

    createTestEmployee(
        $employee,
        $division,
        'EMP007'
    );

    $hr = createTestUser(
        'hr_admin',
        'HR Test'
    );

    $submission = createTestOvertime(
        $employee,
        $division,
        'PENDING_VP'
    );

    $response = $this
        ->actingAs($hr)
        ->postJson(
            route('overtime.hr.verify', $submission->id),
            [
                'action' => 'VERIFY',
            ]
        );

    $response->assertStatus(409);

    expect($submission->fresh()->status)
        ->toBe('PENDING_VP');
});


test('hr dapat verify hanya submission APPROVED_VP', function () {
    $division = createTestDivision('Divisi Test');

    $employee = createTestUser(
        'karyawan',
        'Employee Test'
    );

    createTestEmployee(
        $employee,
        $division,
        'EMP008'
    );

    $hr = createTestUser(
        'hr_admin',
        'HR Test'
    );

    $submission = createTestOvertime(
        $employee,
        $division,
        'APPROVED_VP'
    );

    $response = $this
        ->actingAs($hr)
        ->postJson(
            route('overtime.hr.verify', $submission->id),
            [
                'action' => 'VERIFY',
                'notes' => 'Diverifikasi HR.',
            ]
        );

    $response
        ->assertSuccessful()
        ->assertJsonPath(
            'data.status',
            'VERIFIED_HR'
        );

    expect($submission->fresh()->status)
        ->toBe('VERIFIED_HR');
});


test('hr tidak dapat verify ulang submission VERIFIED_HR', function () {
    $division = createTestDivision('Divisi Test');

    $employee = createTestUser(
        'karyawan',
        'Employee Test'
    );

    createTestEmployee(
        $employee,
        $division,
        'EMP009'
    );

    $hr = createTestUser(
        'hr_admin',
        'HR Test'
    );

    $submission = createTestOvertime(
        $employee,
        $division,
        'VERIFIED_HR'
    );

    $response = $this
        ->actingAs($hr)
        ->postJson(
            route('overtime.hr.verify', $submission->id),
            [
                'action' => 'VERIFY',
            ]
        );

    $response->assertStatus(409);

    expect($submission->fresh()->status)
        ->toBe('VERIFIED_HR');
});


/*
|--------------------------------------------------------------------------
| Role Boundary
|--------------------------------------------------------------------------
*/

test('karyawan tidak dapat approve lembur sebagai vp', function () {
    $division = createTestDivision('Divisi Test');

    $employee = createTestUser(
        'karyawan',
        'Employee Test'
    );

    createTestEmployee(
        $employee,
        $division,
        'EMP010'
    );

    $submission = createTestOvertime(
        $employee,
        $division,
        'PENDING_VP'
    );

    $response = $this
        ->actingAs($employee)
        ->postJson(
            route('overtime.vp.approve', $submission->id),
            [
                'action' => 'APPROVE',
            ]
        );

    $response->assertForbidden();

    expect($submission->fresh()->status)
        ->toBe('PENDING_VP');
});


test('karyawan tidak dapat verify lembur sebagai hr', function () {
    $division = createTestDivision('Divisi Test');

    $employee = createTestUser(
        'karyawan',
        'Employee Test'
    );

    createTestEmployee(
        $employee,
        $division,
        'EMP011'
    );

    $submission = createTestOvertime(
        $employee,
        $division,
        'APPROVED_VP'
    );

    $response = $this
        ->actingAs($employee)
        ->postJson(
            route('overtime.hr.verify', $submission->id),
            [
                'action' => 'VERIFY',
            ]
        );

    $response->assertForbidden();

    expect($submission->fresh()->status)
        ->toBe('APPROVED_VP');
});


test('vp tidak dapat mengakses endpoint verifikasi hr', function () {
    $division = createTestDivision('Divisi Test');

    $vp = createTestUser(
        'vp',
        'VP Test'
    );

    createTestEmployee(
        $vp,
        $division,
        'VP005'
    );

    $employee = createTestUser(
        'karyawan',
        'Employee Test'
    );

    createTestEmployee(
        $employee,
        $division,
        'EMP012'
    );

    $submission = createTestOvertime(
        $employee,
        $division,
        'APPROVED_VP'
    );

    $response = $this
        ->actingAs($vp)
        ->postJson(
            route('overtime.hr.verify', $submission->id),
            [
                'action' => 'VERIFY',
            ]
        );

    $response->assertForbidden();

    expect($submission->fresh()->status)
        ->toBe('APPROVED_VP');
});


/*
|--------------------------------------------------------------------------
| HR Pending Queue
|--------------------------------------------------------------------------
*/

test('hr pending hanya menampilkan submission APPROVED_VP', function () {
    $division = createTestDivision('Divisi Test');

    $employee = createTestUser(
        'karyawan',
        'Employee Test'
    );

    createTestEmployee(
        $employee,
        $division,
        'EMP013'
    );

    $hr = createTestUser(
        'hr_admin',
        'HR Test'
    );

    $pending = createTestOvertime(
        $employee,
        $division,
        'PENDING_VP'
    );

    $approved = createTestOvertime(
        $employee,
        $division,
        'APPROVED_VP'
    );

    $verified = createTestOvertime(
        $employee,
        $division,
        'VERIFIED_HR'
    );

    $response = $this
        ->actingAs($hr)
        ->getJson(route('overtime.hr.pending'));

    $response
        ->assertSuccessful()
        ->assertJsonFragment([
            'id' => $approved->id,
        ])
        ->assertJsonMissing([
            'id' => $pending->id,
        ])
        ->assertJsonMissing([
            'id' => $verified->id,
        ]);
});
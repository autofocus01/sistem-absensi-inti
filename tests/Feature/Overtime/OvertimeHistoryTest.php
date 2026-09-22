<?php

use App\Models\Division;
use App\Models\Employee;
use App\Models\OvertimeSubmission;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

function historyDivision(string $name): Division
{
    return Division::create(['nama' => $name, 'lini' => 'Lini Test']);
}

function historyUser(string $role, string $name): User
{
    return User::factory()->create(['role' => $role, 'name' => $name]);
}

function historyEmployee(User $user, Division $division, string $nipeg): Employee
{
    return Employee::create([
        'user_id' => $user->id,
        'nipeg' => $nipeg,
        'nama' => $user->name,
        'jabatan' => 'Staff Test',
        'division_id' => $division->id,
    ]);
}

function historySubmission(User $user, Division $division, string $status): OvertimeSubmission
{
    return OvertimeSubmission::create([
        'user_id' => $user->id,
        'division_id' => $division->id,
        'overtime_date' => '2026-09-22',
        'start_time' => '16:30:00',
        'end_time' => '17:30:00',
        'estimated_hours' => 1,
        'category' => 'hari_kerja',
        'reason' => 'Riwayat test',
        'submission_mode' => 'PRE_SUBMITTED',
        'actual_start_time' => '16:30:00',
        'actual_end_time' => '17:30:00',
        'actual_minutes' => 60,
        'recognized_minutes' => 60,
        'eligibility_status' => 'ELIGIBLE',
        'status' => $status,
    ]);
}

test('karyawan hanya melihat riwayat pengajuan lemburnya sendiri', function () {
    $division = historyDivision('Divisi History');
    $employee = historyUser('karyawan', 'Karyawan History');
    historyEmployee($employee, $division, 'H001');

    $other = historyUser('karyawan', 'Karyawan Lain');
    historyEmployee($other, $division, 'H002');

    $own = historySubmission($employee, $division, 'REJECTED_VP');
    historySubmission($other, $division, 'REJECTED_VP');

    $this->actingAs($employee)
        ->get(route('overtime.history'))
        ->assertOk()
        ->assertSee($own->ticket_number)
        ->assertSee('Ditolak VP');
});

test('vp melihat hanya keputusan yang dibuat oleh dirinya sendiri dalam divisinya', function () {
    $division = historyDivision('Divisi VP History');
    $vp = historyUser('vp', 'VP History');
    historyEmployee($vp, $division, 'VP-H001');

    $otherVp = historyUser('vp', 'VP Lain');
    historyEmployee($otherVp, $division, 'VP-H002');

    $employee = historyUser('karyawan', 'Employee History');
    historyEmployee($employee, $division, 'EMP-H001');

    $ownDecision = historySubmission($employee, $division, 'REJECTED_VP');
    $ownDecision->update([
        'vp_approver_id' => $vp->id,
        'vp_approved_at' => now(),
        'vp_notes' => 'Ditolak untuk pengujian',
    ]);

    $otherDecision = historySubmission($employee, $division, 'APPROVED_VP');
    $otherDecision->update([
        'vp_approver_id' => $otherVp->id,
        'vp_approved_at' => now(),
    ]);

    $this->actingAs($vp)
        ->get(route('overtime.vp.history'))
        ->assertOk()
        ->assertSee($ownDecision->ticket_number)
        ->assertSee('Ditolak')
        ->assertDontSee($otherDecision->ticket_number);
});

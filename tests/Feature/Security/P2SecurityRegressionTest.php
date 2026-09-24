<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;

uses(RefreshDatabase::class);

test('karyawan cannot enter hr employee administration', function () {
    $user = User::factory()->create(['role' => 'karyawan']);

    $this->actingAs($user)
        ->get(route('employees.index'))
        ->assertForbidden();
});

test('vp cannot enter hr employee administration', function () {
    $user = User::factory()->create(['role' => 'vp']);

    $this->actingAs($user)
        ->get(route('employees.index'))
        ->assertForbidden();
});

test('hr cannot enter vp approval endpoint', function () {
    $user = User::factory()->create(['role' => 'hr_admin']);

    $this->actingAs($user)
        ->get(route('overtime.vp.pending'))
        ->assertForbidden();
});

test('vp cannot enter hr verification endpoint', function () {
    $user = User::factory()->create(['role' => 'vp']);

    $this->actingAs($user)
        ->get(route('overtime.hr.pending'))
        ->assertForbidden();
});

test('karyawan cannot view audit trail', function () {
    $user = User::factory()->create(['role' => 'karyawan']);

    $this->actingAs($user)
        ->get(route('audit-logs.index'))
        ->assertForbidden();
});

test('direktur cannot enter hr employee administration', function () {
    $user = User::factory()->create(['role' => 'direktur_utama']);

    $this->actingAs($user)
        ->get(route('employees.index'))
        ->assertForbidden();
});

test('karyawan cannot enter direktur dashboard', function () {
    $user = User::factory()->create(['role' => 'karyawan']);

    $this->actingAs($user)
        ->get(route('direktur.dashboard'))
        ->assertForbidden();
});

test('employee import commit cannot be hijacked by another hr account', function () {
    Storage::fake('local');

    $owner = User::factory()->create(['role' => 'hr_admin']);
    $otherHr = User::factory()->create(['role' => 'hr_admin']);

    $path = 'employee-imports/p2-owner-test.csv';
    Storage::put($path, "NIPEG,Nama,Divisi\nP2-001,Test,Divisi Test\n");

    $this->actingAs($owner)
        ->withSession([
            'employee_import' => [
                'path' => $path,
                'user_id' => $owner->id,
                'created_at' => now()->timestamp,
            ],
        ]);

    $this->actingAs($otherHr)
        ->post(route('employees.import.commit'))
        ->assertSessionHasErrors('file');
});

test('employee import commit rejects an expired preview', function () {
    Storage::fake('local');

    $hr = User::factory()->create(['role' => 'hr_admin']);
    $path = 'employee-imports/p2-expired-test.csv';
    Storage::put($path, "NIPEG,Nama,Divisi\nP2-002,Test,Divisi Test\n");

    $this->actingAs($hr)
        ->withSession([
            'employee_import' => [
                'path' => $path,
                'user_id' => $hr->id,
                'created_at' => now()->subMinutes(31)->timestamp,
            ],
        ])
        ->post(route('employees.import.commit'))
        ->assertSessionHasErrors('file');
});

test('production attendance import commit cannot be hijacked by another hr account', function () {
    Storage::fake('local');

    $owner = User::factory()->create(['role' => 'hr_admin']);
    $otherHr = User::factory()->create(['role' => 'hr_admin']);

    $path = 'attendance-imports/p2-owner-test.csv';
    Storage::put($path, "NIPEG,Tanggal\nP2-001,2026-09-22\n");

    $this->actingAs($otherHr)
        ->withSession([
            'attendance_import' => [
                'path' => $path,
                'user_id' => $owner->id,
                'created_at' => now()->timestamp,
            ],
        ])
        ->post(route('attendance.import.production.commit'))
        ->assertSessionHasErrors('file');
});

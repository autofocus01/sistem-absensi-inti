<?php

use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('hr can view audit trail while karyawan cannot', function () {
    $hr = User::factory()->create(['role' => 'hr_admin']);
    $employee = User::factory()->create(['role' => 'karyawan']);

    AuditLog::create([
        'user_id' => $hr->id,
        'action' => 'TEST_ACTION',
        'subject_type' => User::class,
        'subject_id' => (string) $employee->id,
        'before_data' => ['status' => 'before'],
        'after_data' => ['status' => 'after'],
        'request_id' => 'test-request',
    ]);

    $this->actingAs($hr)
        ->get(route('audit-logs.index'))
        ->assertOk()
        ->assertSee('TEST_ACTION');

    $this->actingAs($employee)
        ->get(route('audit-logs.index'))
        ->assertForbidden();
});

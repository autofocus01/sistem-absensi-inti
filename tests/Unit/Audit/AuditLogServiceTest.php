<?php

use App\Models\AuditLog;
use App\Models\User;
use App\Services\Audit\AuditLogService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

it('records an audit event with actor and before/after data', function () {
    $user = User::factory()->create(['role' => 'hr_admin']);
    $this->actingAs($user);

    $log = app(AuditLogService::class)->record(
        action: 'ATTENDANCE_CORRECTED',
        before: ['jam_pulang' => '16:30:00'],
        after: ['jam_pulang' => '17:00:00'],
        notes: 'Correction by HR',
    );

    expect($log)->toBeInstanceOf(AuditLog::class)
        ->and($log->user_id)->toBe($user->id)
        ->and($log->action)->toBe('ATTENDANCE_CORRECTED')
        ->and($log->before_data)->toBe(['jam_pulang' => '16:30:00'])
        ->and($log->after_data)->toBe(['jam_pulang' => '17:00:00']);
});

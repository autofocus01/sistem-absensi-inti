<?php

namespace App\Services\Audit;

use App\Models\AuditLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AuditLogService
{
    public function record(
        string $action,
        ?Model $subject = null,
        ?array $before = null,
        ?array $after = null,
        ?string $notes = null,
        ?Request $request = null,
    ): AuditLog {
        $request ??= app()->bound('request') ? request() : null;

        return AuditLog::create([
            'user_id' => auth()->id(),
            'action' => $action,
            'subject_type' => $subject?->getMorphClass(),
            'subject_id' => $subject?->getKey() !== null ? (string) $subject->getKey() : null,
            'before_data' => $before,
            'after_data' => $after,
            'notes' => $notes,
            'ip_address' => $request?->ip(),
            'user_agent' => $request?->userAgent(),
            'request_id' => $request?->header('X-Request-ID') ?: (string) Str::uuid(),
        ]);
    }

    public function transition(
        Model $subject,
        string $action,
        ?string $from,
        ?string $to,
        array $extra = [],
        ?string $notes = null,
    ): AuditLog {
        return $this->record(
            action: $action,
            subject: $subject,
            before: array_merge(['status' => $from], $extra['before'] ?? []),
            after: array_merge(['status' => $to], $extra['after'] ?? []),
            notes: $notes,
        );
    }
}

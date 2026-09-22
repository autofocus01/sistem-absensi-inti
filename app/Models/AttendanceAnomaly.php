<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AttendanceAnomaly extends Model
{
    use HasFactory;

    protected $fillable = [
        'fingerprint', 'type', 'severity', 'employee_id', 'attendance_log_id',
        'overtime_submission_id', 'tanggal', 'details', 'status',
        'resolved_by', 'resolved_at', 'resolution_notes',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'details' => 'array',
        'resolved_at' => 'datetime',
    ];

    public function employee(): BelongsTo { return $this->belongsTo(Employee::class); }
    public function attendanceLog(): BelongsTo { return $this->belongsTo(AttendanceLog::class); }
    public function overtimeSubmission(): BelongsTo { return $this->belongsTo(OvertimeSubmission::class); }
    public function resolver(): BelongsTo { return $this->belongsTo(User::class, 'resolved_by'); }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class OvertimeSubmission extends Model
{
    use HasFactory;

    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'id',
        'ticket_number',
        'user_id',
        'division_id',
        'submission_mode',
        'attendance_log_id',
        'actual_start_time',
        'actual_end_time',
        'actual_minutes',
        'recognized_minutes',
        'eligibility_status',
        'overtime_date',
        'start_time',
        'end_time',
        'estimated_hours',
        'category',
        'reason',
        'employee_consent',
        'employee_consent_at',
        'employee_consent_ip',
        'employee_consent_user_agent',
        'employee_consent_source',
        'employee_consent_version',
        'employee_consent_text',
        'status',
        'vp_approver_id',
        'vp_approved_at',
        'vp_notes',
        'hr_verifier_id',
        'hr_verified_at',
        'hr_notes',
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            if (empty($model->id)) {
                $model->id = (string) Str::uuid();
            }
            if (empty($model->ticket_number)) {
                $model->ticket_number = 'INTI-OVT-' . date('Ymd') . '-' . strtoupper(Str::random(4));
            }
        });
    }

    protected $casts = [
        'overtime_date' => 'date',
        'employee_consent' => 'boolean',
        'employee_consent_at' => 'datetime',
        'vp_approved_at' => 'datetime',
        'hr_verified_at' => 'datetime',
    ];

    // Relasi
  public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function division()
    {
        return $this->belongsTo(Division::class);
    }

    public function attendanceLog()
    {
        return $this->belongsTo(AttendanceLog::class, 'attendance_log_id');
    }

    public function vpApprover()
    {
        return $this->belongsTo(User::class, 'vp_approver_id');
    }

    public function hrVerifier()
    {
        return $this->belongsTo(User::class, 'hr_verifier_id');
    }
}
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Employee extends Model
{
    use HasFactory;

    protected $fillable = ['nipeg', 'nama', 'jabatan', 'division_id', 'user_id'];

    public function division(): BelongsTo
    {
        return $this->belongsTo(Division::class);
    }

    public function attendanceRecaps(): HasMany
    {
        return $this->hasMany(AttendanceRecap::class);
    }

    public function attendanceLogs(): HasMany
    {
        return $this->hasMany(AttendanceLog::class);
    }
}
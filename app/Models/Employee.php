<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    protected $fillable = [
        'nipeg',
        'nama',
        'jenis_kelamin',
        'jabatan',
        'band_code',
        'no_hp',
        'alamat',
        'division_id',
    ];

    public function division()
    {
        return $this->belongsTo(Division::class);
    }

    public function attendanceRecaps()
    {
        return $this->hasMany(AttendanceRecap::class);
    }

    public function attendanceLogs()
    {
        return $this->hasMany(AttendanceLog::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

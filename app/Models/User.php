<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'email', 'password', 'role'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function employee(): HasOne
    {
        return $this->hasOne(Employee::class);
    }

    // Relasi ke Pengajuan Lembur
    public function overtimeSubmissions(): HasMany
    {
        return $this->hasMany(OvertimeSubmission::class, 'user_id');
    }

    // Helper Role Checks
    public function isDirekturUtama(): bool
    {
        return $this->role === 'direktur_utama';
    }

    public function isHrAdmin(): bool
    {
        return $this->role === 'hr_admin';
    }

    public function isVp(): bool
    {
        return $this->role === 'vp';
    }

    public function isKaryawan(): bool
    {
        return $this->role === 'karyawan';
    }
}
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AuditLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'action',
        'subject_type',
        'subject_id',
        'before_data',
        'after_data',
        'notes',
        'ip_address',
        'user_agent',
        'request_id',
    ];

    protected $casts = [
        'before_data' => 'array',
        'after_data' => 'array',
    ];

    protected static function booted(): void
    {
        static::updating(function (): void {
            throw new \LogicException('Audit log bersifat immutable dan tidak boleh diubah.');
        });

        static::deleting(function (): void {
            throw new \LogicException('Audit log bersifat immutable dan tidak boleh dihapus.');
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

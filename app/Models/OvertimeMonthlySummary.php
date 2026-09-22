<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OvertimeMonthlySummary extends Model
{
    protected $fillable = [
        'employee_id', 'year', 'month', 'weekday_minutes', 'weekend_minutes',
        'national_holiday_minutes', 'total_minutes', 'event_count',
    ];

    protected function casts(): array
    {
        return [
            'year' => 'integer', 'month' => 'integer', 'weekday_minutes' => 'integer',
            'weekend_minutes' => 'integer', 'national_holiday_minutes' => 'integer',
            'total_minutes' => 'integer', 'event_count' => 'integer',
        ];
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }
}

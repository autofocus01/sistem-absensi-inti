<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LegalOvertimeRule extends Model
{
    protected $fillable = [
        'rule_code', 'regulation_name', 'regulation_number', 'article_reference',
        'day_type', 'minimum_minutes', 'maximum_minutes_daily', 'maximum_minutes_weekly',
        'maximum_minutes_monthly', 'effective_from', 'effective_until', 'status',
        'source_document', 'notes',
    ];

    protected function casts(): array
    {
        return [
            'effective_from' => 'date',
            'effective_until' => 'date',
        ];
    }
}

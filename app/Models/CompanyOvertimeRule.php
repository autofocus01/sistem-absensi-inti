<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CompanyOvertimeRule extends Model
{
    protected $fillable = [
        'rule_code', 'rule_name', 'day_type', 'normal_end', 'minimum_minutes',
        'maximum_minutes_event', 'maximum_minutes_monthly', 'rounding_method',
        'rounding_interval', 'requires_request', 'allows_post_submission',
        'requires_vp_approval', 'requires_hr_approval', 'effective_from',
        'effective_until', 'status', 'source_document', 'notes',
    ];

    protected function casts(): array
    {
        return [
            'effective_from' => 'date',
            'effective_until' => 'date',
            'requires_request' => 'boolean',
            'allows_post_submission' => 'boolean',
            'requires_vp_approval' => 'boolean',
            'requires_hr_approval' => 'boolean',
        ];
    }
}

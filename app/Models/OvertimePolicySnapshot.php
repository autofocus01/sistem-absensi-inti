<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OvertimePolicySnapshot extends Model
{
    protected $fillable = ['overtime_submission_id', 'legal_rule_id', 'company_rule_id', 'policy'];

    protected function casts(): array
    {
        return ['policy' => 'array'];
    }
}

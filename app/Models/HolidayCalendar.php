<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HolidayCalendar extends Model
{
    use HasFactory;

    protected $fillable = [
        'tanggal',
        'nama',
        'jenis',
        'is_active',
        'source_document',
        'notes',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'is_active' => 'boolean',
    ];

    public function isNationalHoliday(): bool
    {
        return $this->is_active && $this->jenis === 'NATIONAL_HOLIDAY';
    }
}

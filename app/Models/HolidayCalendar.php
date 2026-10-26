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

    public function isCutiBersama(): bool
    {
        return $this->is_active && $this->jenis === 'CUTI_BERSAMA';
    }

    /**
     * Hari libur yang tidak dihitung sebagai hari kerja normal:
     * libur nasional (SKB 3 Menteri) maupun cuti bersama (BUMN/pemerintah).
     */
    public function isHoliday(): bool
    {
        return $this->is_active
            && in_array($this->jenis, ['NATIONAL_HOLIDAY', 'CUTI_BERSAMA'], true);
    }
}

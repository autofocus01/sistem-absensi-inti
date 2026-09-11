<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AttendanceImportError extends Model
{
    use HasFactory;

    protected $fillable = [
        'nipeg', 'nama', 'tahun', 'bulan', 'jenis', 'baris_excel', 'alasan', 'data_mentah', 'status',
    ];

    protected $casts = [
        'data_mentah' => 'array',
    ];
}
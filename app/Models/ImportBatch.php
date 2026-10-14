<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Register provenance setiap commit impor (Fase 1).
 *
 * Menjawab pertanyaan: "Angka ini dari file siapa, kapan, berapa baris
 * masuk/gagal, dan apakah file ini pernah diimpor sebelumnya?"
 */
class ImportBatch extends Model
{
    protected $fillable = [
        'user_id', 'jenis', 'nama_file', 'file_hash', 'format',
        'total_baris', 'berhasil', 'gagal', 'periode', 'duplikat',
    ];

    protected $casts = [
        'periode' => 'array',
        'duplikat' => 'boolean',
    ];

    public const JENIS_ABSENSI = 'absensi_harian';
    public const JENIS_KARYAWAN = 'master_karyawan';
    public const JENIS_LEMBUR = 'lembur';

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}

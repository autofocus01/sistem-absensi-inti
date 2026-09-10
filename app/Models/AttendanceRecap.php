<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AttendanceRecap extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id', 'tahun', 'bulan',
        'hari_kerja', 'hadir', 'perdin', 'cuti', 'sakit', 'ijin', 'alpha',
        'telat_hari', 'menit_telat',
        'lokasi_bandung', 'lokasi_jakarta', 'koreksi_by_admin',
        // 'persen_kehadiran' SENGAJA tidak di-fillable - lihat boot() di bawah.
        // Ini langsung menutup celah yang ada di data lama: baris dengan
        // % Kehadiran 105% atau Hadir=0 tapi % Kehadiran 15.8% cuma bisa terjadi
        // kalau kolom itu diisi manual/lepas dari hitungan hari kerja & hadir.
    ];

    protected static function booted(): void
    {
        static::saving(function (AttendanceRecap $recap) {
            // 1) Persen kehadiran SELALU dihitung ulang dari hadir/hari_kerja, tidak pernah dipercaya dari input.
            $recap->persen_kehadiran = $recap->hari_kerja > 0
                ? round(min(100, ($recap->hadir / $recap->hari_kerja) * 100), 1)
                : 0;

            // 2) Total kategori kehadiran wajib pas dengan hari kerja (kasus "Total hari=20 != Hari Kerja=19").
            $totalHari = $recap->hadir + $recap->perdin + $recap->cuti
                + $recap->sakit + $recap->ijin + $recap->alpha;

            if ($totalHari !== (int) $recap->hari_kerja) {
                throw new \InvalidArgumentException(
                    "Total hari (hadir+perdin+cuti+sakit+ijin+alpha = {$totalHari}) tidak sama dengan hari kerja ({$recap->hari_kerja})."
                );
            }

            // 3) Batas wajar menit telat per hari telat (kasus 8898 menit ~148 jam di data lama).
            //    Asumsi: maksimum wajar 240 menit (4 jam) keterlambatan per hari telat.
            $batasMenitTelat = $recap->telat_hari * 240;
            if ($recap->menit_telat > $batasMenitTelat) {
                throw new \InvalidArgumentException(
                    "Menit telat ({$recap->menit_telat}) melebihi batas wajar untuk {$recap->telat_hari} hari telat (maks {$batasMenitTelat} menit). Kemungkinan salah input atau indikasi manipulasi."
                );
            }
        });
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }
}
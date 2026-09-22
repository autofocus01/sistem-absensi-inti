<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AttendanceLog extends Model
{
    use HasFactory;

    /**
     * Jam operasional resmi kantor:
     * Senin-Jumat, 07:30 - 16:30, tanpa shift.
     */
    public const JAM_MASUK_NORMAL = '07:30:00';
    public const JAM_PULANG_NORMAL = '16:30:00';

    protected $fillable = [
        'employee_id',
        'tanggal',
        'jam_masuk',
        'jam_pulang',
        'sumber',
        'jumlah_scan',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'disetujui_pada' => 'datetime',
    ];

    /**
     * User yang melakukan approval/koreksi presensi,
     * jika field tersebut masih digunakan oleh modul presensi.
     */
    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'disetujui_oleh');
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    /**
     * Hari kerja normal = Senin-Jumat.
     *
     * Catatan:
     * Status lembur TIDAK ditentukan di model AttendanceLog.
     * Lembur diproses melalui OvertimeSubmission.
     */
    public function isHariKerja(): bool
    {
        return $this->tanggal?->isWeekday() ?? false;
    }

    /**
     * Menghitung keterlambatan masuk terhadap 07:30.
     *
     * Tidak ada grace period.
     */
    public function menitTelat(): int
    {
        if (! $this->isHariKerja() || ! $this->jam_masuk) {
            return 0;
        }

        $tanggal = $this->tanggal->format('Y-m-d');

        $masukNormal = Carbon::parse(
            "{$tanggal} " . self::JAM_MASUK_NORMAL
        );

        $masukAktual = Carbon::parse(
            "{$tanggal} {$this->jam_masuk}"
        );

        return $masukAktual->greaterThan($masukNormal)
            ? $masukNormal->diffInMinutes($masukAktual)
            : 0;
    }

    /**
     * Durasi aktual antara jam masuk dan jam pulang.
     *
     * Ini adalah fakta presensi, bukan durasi lembur.
     */
    public function durasiKerjaMenit(): ?int
    {
        if (! $this->jam_masuk || ! $this->jam_pulang) {
            return null;
        }

        $tanggal = $this->tanggal->format('Y-m-d');

        $masuk = Carbon::parse(
            "{$tanggal} {$this->jam_masuk}"
        );

        $pulang = Carbon::parse(
            "{$tanggal} {$this->jam_pulang}"
        );

        return max(0, $masuk->diffInMinutes($pulang));
    }

    public function durasiKerjaFormat(): string
    {
        $menit = $this->durasiKerjaMenit();

        if ($menit === null) {
            return '-';
        }

        return intdiv($menit, 60)
            . 'j '
            . str_pad(
                (string) ($menit % 60),
                2,
                '0',
                STR_PAD_LEFT
            )
            . 'm';
    }

    /**
     * Status presensi dasar.
     *
     * PENTING:
     * AttendanceLog tidak lagi menentukan apakah seseorang lembur.
     * Lembur resmi berasal dari OvertimeSubmission yang telah melalui
     * workflow approval/verification.
     */
    public function status(): string
    {
        if (! $this->isHariKerja()) {
            return 'Di Luar Hari Kerja';
        }

        if (! $this->jam_masuk) {
            return 'Tidak Ada Presensi';
        }

        if (! $this->jam_pulang) {
            return 'Belum Tap Pulang';
        }

        return 'Hadir';
    }


}
<?php

namespace App\Services\Attendance;

use Carbon\CarbonInterface;
use InvalidArgumentException;

/**
 * Aturan dasar presensi yang tidak bergantung pada controller/UI.
 *
 * Catatan penting:
 * - Jam kerja normal: 07:30-16:30, Senin-Jumat.
 * - Tidak ada grace period.
 * - Lembur yang diakui TIDAK ditentukan service ini; pengakuan lembur
 *   tetap menjadi tanggung jawab workflow OvertimeSubmission + policy.
 */
final class AttendanceBusinessRules
{
    public const NORMAL_START = '07:30:00';
    public const NORMAL_END = '16:30:00';

    public function latenessMinutes(CarbonInterface $date, string $clockIn): int
    {
        if ($date->isWeekend()) {
            return 0;
        }

        $actual = $this->atDateTime($date, $clockIn);
        $normal = $this->atDateTime($date, self::NORMAL_START);

        return $actual->greaterThan($normal)
            ? $normal->diffInMinutes($actual)
            : 0;
    }

    public function workDurationMinutes(CarbonInterface $date, string $clockIn, string $clockOut): int
    {
        $start = $this->atDateTime($date, $clockIn);
        $end = $this->atDateTime($date, $clockOut);

        if ($end->lessThan($start)) {
            throw new InvalidArgumentException('Clock-out tidak boleh lebih awal dari clock-in.');
        }

        return $start->diffInMinutes($end);
    }

    public function isAfterNormalEnd(CarbonInterface $date, string $clockOut): bool
    {
        return ! $date->isWeekend()
            && $this->atDateTime($date, $clockOut)
                ->greaterThan($this->atDateTime($date, self::NORMAL_END));
    }

    private function atDateTime(CarbonInterface $date, string $time): CarbonInterface
    {
        if (! preg_match('/^\d{2}:\d{2}(?::\d{2})?$/', $time)) {
            throw new InvalidArgumentException("Format waktu tidak valid: {$time}");
        }

        return $date->copy()->setTimeFromTimeString($time);
    }
}

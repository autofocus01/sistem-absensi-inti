<?php

namespace App\Services\Period;

use App\Models\AttendanceLog;
use App\Models\AttendanceRecap;
use App\Models\OvertimeSubmission;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class PeriodOptionsService
{
    public const MONTH_NAMES = [
        1 => 'Januari',
        2 => 'Februari',
        3 => 'Maret',
        4 => 'April',
        5 => 'Mei',
        6 => 'Juni',
        7 => 'Juli',
        8 => 'Agustus',
        9 => 'September',
        10 => 'Oktober',
        11 => 'November',
        12 => 'Desember',
    ];

    public const SHORT_MONTH_NAMES = [
        1 => 'Jan',
        2 => 'Feb',
        3 => 'Mar',
        4 => 'Apr',
        5 => 'Mei',
        6 => 'Jun',
        7 => 'Jul',
        8 => 'Agu',
        9 => 'Sep',
        10 => 'Okt',
        11 => 'Nov',
        12 => 'Des',
    ];

    /**
     * Tahun yang benar-benar tersedia pada data aplikasi.
     *
     * Tahun berjalan selalu disertakan agar filter tidak kosong
     * pada instalasi baru.
     */
    public function availableYears(): Collection
    {
        $driver = DB::connection()->getDriverName();

        /*
         * Jangan menggunakan sprintf() untuk expression SQLite.
         *
         * SQLite menggunakan:
         *
         *     strftime('%Y', tanggal)
         *
         * Sedangkan %Y akan dianggap sebagai format specifier
         * oleh sprintf() pada PHP 8+ dan menyebabkan:
         *
         *     ValueError: Unknown format specifier "Y"
         *
         * Karena itu expression dibuat langsung melalui closure.
         */
        $yearExpression = match ($driver) {
            'sqlite' => fn (string $column): string =>
                "CAST(strftime('%Y', {$column}) AS INTEGER) as tahun",

            'pgsql' => fn (string $column): string =>
                "EXTRACT(YEAR FROM {$column})::integer as tahun",

            default => fn (string $column): string =>
                "YEAR({$column}) as tahun",
        };

        $attendanceYearExpression = $yearExpression('tanggal');
        $overtimeYearExpression = $yearExpression('overtime_date');

        return collect()
            ->merge(
                AttendanceLog::query()
                    ->selectRaw($attendanceYearExpression)
                    ->distinct()
                    ->pluck('tahun')
            )
            ->merge(
                AttendanceRecap::query()
                    ->select('tahun')
                    ->distinct()
                    ->pluck('tahun')
            )
            ->merge(
                OvertimeSubmission::query()
                    ->selectRaw($overtimeYearExpression)
                    ->distinct()
                    ->pluck('tahun')
            )
            ->push(now()->year)
            ->map(fn ($year) => (int) $year)
            ->filter(
                fn (int $year) =>
                    $year >= 2000 && $year <= 2100
            )
            ->unique()
            ->sortDesc()
            ->values();
    }

    public function monthNames(): array
    {
        return self::MONTH_NAMES;
    }

    public function shortMonthNames(): array
    {
        return self::SHORT_MONTH_NAMES;
    }
}
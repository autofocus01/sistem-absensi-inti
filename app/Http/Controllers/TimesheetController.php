<?php

namespace App\Http\Controllers;

use App\Models\AttendanceLog;
use App\Services\Overtime\OvertimeReportingService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TimesheetController extends Controller
{
    public function __construct(
        private readonly OvertimeReportingService $overtimeReportingService
    ) {
    }

    public function index(Request $request)
    {
        $user = Auth::user();

        abort_unless($user, 403);

        $employee = $user->employee;

        /*
         * Timesheet personal:
         * Karyawan / VP hanya melihat data dirinya sendiri.
         */
        abort_unless($employee, 403);

        /*
         * Terima dua format parameter:
         *
         * ?month=9&year=2026
         * atau
         * ?bulan=9&tahun=2026
         *
         * Ini membuat Timesheet kompatibel dengan modul lain
         * yang menggunakan penamaan bulan/tahun.
         */
        $month = (int) (
            $request->input('month')
            ?? $request->input('bulan')
            ?? now()->month
        );

        $year = (int) (
            $request->input('year')
            ?? $request->input('tahun')
            ?? now()->year
        );

        /*
         * Validasi bulan.
         */
        if ($month < 1 || $month > 12) {
            $month = now()->month;
        }

        /*
         * Validasi tahun.
         *
         * Jangan menerima tahun ekstrem yang tidak masuk akal.
         */
        if ($year < 2000 || $year > 2100) {
            $year = now()->year;
        }

        $startDate = Carbon::create(
            $year,
            $month,
            1
        )->startOfDay();

        $endDate = $startDate
            ->copy()
            ->endOfMonth()
            ->endOfDay();

        /*
         * ============================================================
         * FAKTA PRESENSI
         * ============================================================
         *
         * AttendanceLog adalah source of truth untuk:
         * - jam masuk
         * - jam pulang
         * - keterlambatan
         * - durasi kerja
         */
        $attendanceLogs = AttendanceLog::query()
            ->where('employee_id', $employee->id)
            ->whereBetween('tanggal', [
                $startDate->toDateString(),
                $endDate->toDateString(),
            ])
            ->orderBy('tanggal')
            ->get()
            ->keyBy(
                fn (AttendanceLog $attendance) =>
                    $attendance->tanggal->format('Y-m-d')
            );

        /*
         * ============================================================
         * LEMBUR RESMI
         * ============================================================
         *
         * Hanya lembur VERIFIED_HR + ELIGIBLE yang masuk timesheet.
         */
        $officialOvertime = $this->overtimeReportingService
            ->officialEntries(
                $employee->id,
                $startDate->toDateString(),
                $endDate->toDateString()
            )
            ->groupBy(
                fn ($overtime) =>
                    Carbon::parse($overtime->tanggal)->format('Y-m-d')
            );

        $totalOfficialOvertimeMinutes =
            $this->overtimeReportingService->monthlyOfficialMinutes(
                $employee->id,
                $year,
                $month
            );

        /*
         * ============================================================
         * BENTUK TIMESHEET HARIAN
         * ============================================================
         */
        $timesheet = collect();

        for (
            $date = $startDate->copy();
            $date->lte($endDate);
            $date->addDay()
        ) {
            $dateKey = $date->format('Y-m-d');

            $attendance = $attendanceLogs->get($dateKey);

            $overtimeEntries = $officialOvertime->get(
                $dateKey,
                collect()
            );

            $officialOvertimeMinutes = (int) $overtimeEntries->sum(
                fn ($overtime) => (int) $overtime->recognized_minutes
            );

            $status = $attendance?->status();

            if ($status === null) {
                $status = $date->isWeekday()
                    ? 'Tidak Ada Presensi'
                    : 'Di Luar Hari Kerja';
            }

            $timesheet->push([
                'tanggal' => $date->copy(),

                'attendance' => $attendance,

                'jam_masuk' => $attendance?->jam_masuk,

                'jam_pulang' => $attendance?->jam_pulang,

                'menit_telat' => $attendance?->menitTelat() ?? 0,

                'durasi_kerja_menit' =>
                    $attendance?->durasiKerjaMenit(),

                'lembur_resmi_menit' =>
                    $officialOvertimeMinutes,

                'lembur_resmi_format' =>
                    $this->overtimeReportingService->formatMinutes(
                        $officialOvertimeMinutes
                    ),

                'overtime_entries' => $overtimeEntries,

                'status' => $status,
            ]);
        }

        /*
         * Tahun yang tersedia di dropdown.
         *
         * 3 tahun ke belakang + tahun sekarang + 1 tahun ke depan.
         */
        $currentYear = now()->year;

        $availableYears = range(
            $currentYear + 1,
            $currentYear - 3
        );

        /*
         * range() di atas menghasilkan urutan descending.
         * Kita balik agar tampil:
         * 2023, 2024, 2025, 2026, 2027
         */
        $availableYears = collect($availableYears)
            ->sort()
            ->values()
            ->all();

        return view('timesheet.index', [
            'employee' => $employee,

            'timesheet' => $timesheet,

            'month' => $month,

            'year' => $year,

            'startDate' => $startDate,

            'endDate' => $endDate,

            'totalOfficialOvertimeMinutes' =>
                $totalOfficialOvertimeMinutes,

            'totalOfficialOvertimeFormat' =>
                $this->overtimeReportingService->formatMinutes(
                    $totalOfficialOvertimeMinutes
                ),

            'availableYears' => $availableYears,
        ]);
    }
}
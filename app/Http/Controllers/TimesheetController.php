<?php

namespace App\Http\Controllers;

use App\Models\AttendanceLog;
use App\Models\Employee;
use App\Services\Overtime\OvertimeReportingService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Pagination\LengthAwarePaginator;

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

        $isHrAdmin = method_exists($user, 'isHrAdmin')
            ? $user->isHrAdmin()
            : $user->role === 'hr_admin';

        $isPersonalRole = method_exists($user, 'isKaryawan')
            ? ($user->isKaryawan() || $user->isVp())
            : in_array($user->role, ['karyawan', 'vp'], true);

        abort_unless($isHrAdmin || $isPersonalRole, 403);

        /*
         * ============================================================
         * PERIODE
         * ============================================================
         *
         * Mendukung:
         * ?month=9&year=2026
         * atau:
         * ?bulan=9&tahun=2026
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

        if ($month < 1 || $month > 12) {
            $month = now()->month;
        }

        if ($year < 2000 || $year > 2100) {
            $year = now()->year;
        }

        $startDate = Carbon::create($year, $month, 1)->startOfDay();
        $endDate = $startDate->copy()->endOfMonth()->endOfDay();

        /*
         * ============================================================
         * SCOPE DATA
         * ============================================================
         *
         * Karyawan / VP:
         *   hanya Employee miliknya sendiri.
         *
         * HR Admin:
         *   tidak wajib mempunyai record Employee.
         *   HR melihat seluruh master karyawan.
         *
         * HR dapat memfilter:
         *   ?employee_id=123
         *   ?division_id=1
         *   ?q=nama/nipeg
         */
        $employeeId = $request->integer('employee_id');
        $divisionId = $request->integer('division_id');
        $search = trim((string) $request->input('q', ''));

        if ($isPersonalRole && ! $isHrAdmin && $user->isVp()) {
            $vpEmployee = $user->employee;
            abort_unless($vpEmployee && $vpEmployee->division_id, 403, 'Akun VP belum terhubung ke divisi.');

            // VP hanya boleh melihat karyawan dalam divisinya sendiri.
            $divisionId = (int) $vpEmployee->division_id;
            $employeeId = $request->integer('employee_id');
            $search = trim((string) $request->input('q', ''));

            $employees = Employee::query()
                ->with('division')
                ->where('division_id', $divisionId)
                ->when($employeeId, fn ($query) => $query->whereKey($employeeId))
                ->when($search !== '', fn ($query) => $query->where(function ($q) use ($search) {
                    $q->where('nama', 'like', "%{$search}%")
                        ->orWhere('nipeg', 'like', "%{$search}%");
                }))
                ->orderBy('nama')
                ->get();
        } elseif (! $isHrAdmin) {
            $employee = $user->employee;

            abort_unless(
                $employee,
                403,
                'Akun belum terhubung ke data karyawan.'
            );

            $employee = Employee::query()
                ->whereKey($employee->id)
                ->with('division')
                ->firstOrFail();

            $employees = collect([$employee]);
            $employeeId = $employee->id;
            $divisionId = null;
            $search = '';
        } else {
            $employees = Employee::query()
                ->with('division')
                ->when(
                    $employeeId,
                    fn ($query) => $query->whereKey($employeeId)
                )
                ->when(
                    $divisionId,
                    fn ($query) => $query->where('division_id', $divisionId)
                )
                ->when(
                    $search !== '',
                    fn ($query) => $query->where(function ($q) use ($search) {
                        $q->where('nama', 'like', "%{$search}%")
                            ->orWhere('nipeg', 'like', "%{$search}%");
                    })
                )
                ->orderBy('nama')
                ->get();
        }

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
        $employeeIds = $employees->pluck('id')->filter()->values();

        $attendanceLogs = AttendanceLog::query()
            ->whereIn('employee_id', $employeeIds)
            ->whereBetween('tanggal', [
                $startDate->toDateString(),
                $endDate->toDateString(),
            ])
            ->orderBy('tanggal')
            ->get()
            ->groupBy(function (AttendanceLog $attendance) {
                return $attendance->employee_id . ':' .
                    $attendance->tanggal->format('Y-m-d');
            });

        /*
         * ============================================================
         * LEMBUR RESMI
         * ============================================================
         *
         * Timesheet hanya menghitung:
         * VERIFIED_HR + ELIGIBLE + recognized_minutes > 0.
         *
         * OvertimeReportingService tetap menjadi sumber aturan reporting.
         */
        $officialOvertime = $this->overtimeReportingService
            ->periodQuery($year, $month)
            ->with([
                'user.employee.division',
                'division',
                'attendanceLog',
            ])
            ->where('status', 'VERIFIED_HR')
            ->where('eligibility_status', 'ELIGIBLE')
            ->where('recognized_minutes', '>', 0)
            ->get();

        if ($employeeIds->isNotEmpty()) {
            $officialOvertime = $officialOvertime
                ->filter(function ($overtime) use ($employeeIds) {
                    $overtimeEmployeeId = $overtime->user?->employee?->id;

                    return $overtimeEmployeeId
                        && $employeeIds->contains((int) $overtimeEmployeeId);
                })
                ->values();
        } else {
            $officialOvertime = collect();
        }

        $officialOvertimeByEmployeeDate = $officialOvertime->groupBy(
            function ($overtime) {
                $overtimeEmployeeId = $overtime->user?->employee?->id;

                return $overtimeEmployeeId . ':' .
                    Carbon::parse($overtime->overtime_date)->format('Y-m-d');
            }
        );

        $totalOfficialOvertimeMinutes = (int) $officialOvertime->sum(
            fn ($overtime) => (int) $overtime->recognized_minutes
        );

        /*
         * ============================================================
         * BENTUK TIMESHEET
         * ============================================================
         *
         * Untuk karyawan/VP: seluruh hari pada bulan tersebut.
         *
         * Untuk HR: seluruh hari pada bulan tersebut untuk setiap
         * karyawan dalam scope. Kolom identitas karyawan ditampilkan
         * oleh view HR.
         */
        $timesheet = collect();

        foreach ($employees as $employee) {
            for (
                $date = $startDate->copy();
                $date->lte($endDate);
                $date->addDay()
            ) {
                $dateKey = $date->format('Y-m-d');
                $attendance = $attendanceLogs
                    ->get($employee->id . ':' . $dateKey)
                    ?->first();

                $overtimeEntries = $officialOvertimeByEmployeeDate->get(
                    $employee->id . ':' . $dateKey,
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

                if (
                    $attendance !== null &&
                    $officialOvertimeMinutes > 0
                ) {
                    $status = 'Hadir + Lembur';
                }

                $timesheet->push([
                    'employee_id' => $employee->id,
                    'employee_name' => $employee->nama,
                    'employee_nipeg' => $employee->nipeg,
                    'employee_division' => $employee->division?->nama,

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
        }

        /*
         * Total hari hadir / telat dihitung dari baris timesheet.
         */
        $totalAttendanceDays = $timesheet
            ->filter(fn ($row) => ($row['attendance'] ?? null) !== null)
            ->count();

        $totalLateDays = $timesheet
            ->filter(fn ($row) => (int) ($row['menit_telat'] ?? 0) > 0)
            ->count();

        /*
         * Tahun tersedia untuk filter periode.
         */
        $currentYear = now()->year;

        $availableYears = collect(
            range($currentYear - 3, $currentYear + 1)
        )->values()->all();

        /*
         * Daftar divisi untuk HR filter.
         */
        $divisions = ($isHrAdmin || ($isPersonalRole && $user->isVp()))
            ? \App\Models\Division::query()
                ->when($user->isVp(), fn ($q) => $q->whereKey($divisionId))
                ->orderBy('nama')
                ->get()
            : collect();

        // Batasi panjang tabel agar HR/VP tidak mendapat ratusan baris dalam satu scroll.
        $perPage = max(10, min(25, (int) $request->input('per_page', 20)));
        $currentPage = LengthAwarePaginator::resolveCurrentPage('page');
        $timesheetPage = $timesheet->forPage($currentPage, $perPage)->values();
        $timesheetPaginator = new LengthAwarePaginator(
            $timesheetPage,
            $timesheet->count(),
            $perPage,
            $currentPage,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        return view('timesheet.index', [
            'employee' => ($isHrAdmin || ($isPersonalRole && $user->isVp())) ? null : $employees->first(),

            'employees' => $employees,

            'timesheet' => $timesheetPaginator,

            'totalTimesheetRows' => $timesheet->count(),
            'perPage' => $perPage,

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

            'totalAttendanceDays' => $totalAttendanceDays,

            'totalLateDays' => $totalLateDays,

            'availableYears' => $availableYears,

            'divisions' => $divisions,

            'selectedEmployeeId' => $employeeId,

            'selectedDivisionId' => $divisionId,

            'search' => $search,

            'isHrAdmin' => $isHrAdmin,

            'isVp' => $isPersonalRole && $user->isVp(),
        ]);
    }
}

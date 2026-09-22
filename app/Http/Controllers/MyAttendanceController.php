<?php

namespace App\Http\Controllers;

use App\Models\AttendanceLog;
use App\Services\Audit\AuditLogService;
use App\Services\Overtime\OvertimeReconciliationService;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Throwable;

class MyAttendanceController extends Controller
{
    private const TIMEZONE = 'Asia/Jakarta';

    public function __construct(
        private readonly AuditLogService $audit,
        private readonly OvertimeReconciliationService $reconciliation,
    ) {}

    public function index(Request $request): View
    {
        $user = $request->user();
        abort_unless(
            in_array((string) ($user?->role ?? ''), ['karyawan', 'vp'], true),
            403
        );

        $employee = $user->employee;
        abort_unless($employee, 403, 'Akun karyawan belum terhubung ke data karyawan.');

        $now = Carbon::now(self::TIMEZONE);
        $month = (int) $request->input('month', $now->month);
        $year = (int) $request->input('year', $now->year);

        if ($month < 1 || $month > 12) {
            $month = $now->month;
        }

        if ($year < 2000 || $year > 2100) {
            $year = $now->year;
        }

        $today = $now->copy()->startOfDay();

        $todayAttendance = AttendanceLog::query()
            ->where('employee_id', $employee->id)
            ->whereDate('tanggal', $today->toDateString())
            ->first();

        $startDate = Carbon::create($year, $month, 1, 0, 0, 0, self::TIMEZONE)->startOfMonth();
        $endDate = $startDate->copy()->endOfMonth();

        $history = AttendanceLog::query()
            ->where('employee_id', $employee->id)
            ->whereBetween('tanggal', [
                $startDate->copy()->startOfDay(),
                $endDate->copy()->endOfDay(),
            ])
            ->orderByDesc('tanggal')
            ->paginate(10)
            ->withQueryString();

        return view('attendance.my', [
            'employee' => $employee->loadMissing('division'),
            'today' => $today,
            'todayAttendance' => $todayAttendance,
            'history' => $history,
            'month' => $month,
            'year' => $year,
            'monthLabel' => $startDate->translatedFormat('F Y'),
        ]);
    }

    public function clockIn(Request $request): RedirectResponse
    {
        $user = $request->user();
        abort_unless(
            in_array((string) ($user?->role ?? ''), ['karyawan', 'vp'], true),
            403
        );

        $employee = $user->employee;
        abort_unless($employee, 403, 'Akun karyawan belum terhubung ke data karyawan.');

        $now = Carbon::now(self::TIMEZONE);
        $date = $now->toDateString();
        $time = $now->format('H:i:s');

        try {
            DB::transaction(function () use ($employee, $date, $time): void {
                $attendance = AttendanceLog::query()
                    ->where('employee_id', $employee->id)
                    ->whereDate('tanggal', $date)
                    ->lockForUpdate()
                    ->first();

                if ($attendance) {
                    if ($attendance->jam_masuk) {
                        throw new \RuntimeException(
                            'Jam masuk hari ini sudah tercatat pada ' . substr($attendance->jam_masuk, 0, 5) . '.'
                        );
                    }

                    $before = $attendance->toArray();
                    $attendance->forceFill([
                        'jam_masuk' => $time,
                        'jumlah_scan' => (int) $attendance->jumlah_scan + 1,
                    ])->save();
                    $this->audit->record('CLOCK_IN', $attendance, $before, $attendance->fresh()->toArray(), 'Clock In melengkapi log presensi yang sudah ada.');

                    return;
                }

                $attendance = AttendanceLog::create([
                    'employee_id' => $employee->id,
                    'tanggal' => $date,
                    'jam_masuk' => $time,
                    'jam_pulang' => null,
                    'sumber' => 'self_service',
                    'jumlah_scan' => 1,
                ]);

                $this->audit->record('CLOCK_IN', $attendance, null, $attendance->toArray(), 'Clock In mandiri karyawan.');
            });
        } catch (\RuntimeException $e) {
            return back()->withErrors(['attendance' => $e->getMessage()]);
        } catch (Throwable $e) {
            report($e);

            return back()->withErrors([
                'attendance' => 'Clock In gagal disimpan. Silakan coba lagi.',
            ]);
        }

        return back()->with('status', 'Clock In berhasil dicatat pada ' . Carbon::parse($time)->format('H:i') . '.');
    }

    public function clockOut(Request $request): RedirectResponse
    {
        $user = $request->user();
        abort_unless(
            in_array((string) ($user?->role ?? ''), ['karyawan', 'vp'], true),
            403
        );

        $employee = $user->employee;
        abort_unless($employee, 403, 'Akun karyawan belum terhubung ke data karyawan.');

        $now = Carbon::now(self::TIMEZONE);
        $date = $now->toDateString();
        $time = $now->format('H:i:s');

        try {
            DB::transaction(function () use ($employee, $date, $time): void {
                $attendance = AttendanceLog::query()
                    ->where('employee_id', $employee->id)
                    ->whereDate('tanggal', $date)
                    ->lockForUpdate()
                    ->first();

                if (! $attendance || ! $attendance->jam_masuk) {
                    throw new \RuntimeException('Clock In belum tercatat. Lakukan Clock In terlebih dahulu.');
                }

                if ($attendance->jam_pulang) {
                    throw new \RuntimeException(
                        'Jam pulang hari ini sudah tercatat pada ' . substr($attendance->jam_pulang, 0, 5) . '.'
                    );
                }

                $before = $attendance->toArray();
                $attendance->forceFill([
                    'jam_pulang' => $time,
                    'jumlah_scan' => (int) $attendance->jumlah_scan + 1,
                ])->save();
                $this->audit->record('CLOCK_OUT', $attendance, $before, $attendance->fresh()->toArray(), 'Clock Out mandiri karyawan.');
                $submission = $this->reconciliation->reconcileAfterClockOut($attendance);
                if ($submission) {
                    $this->audit->record('OT_RECONCILED', $submission, null, $submission->toArray(), 'Rekonsiliasi OT otomatis setelah Clock Out.');
                }
            });
        } catch (\RuntimeException $e) {
            return back()->withErrors(['attendance' => $e->getMessage()]);
        } catch (Throwable $e) {
            report($e);

            return back()->withErrors([
                'attendance' => 'Clock Out gagal disimpan. Silakan coba lagi.',
            ]);
        }

        return back()->with('status', 'Clock Out berhasil dicatat pada ' . Carbon::parse($time)->format('H:i') . '.');
    }
}

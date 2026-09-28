<?php

namespace App\Http\Controllers;

use App\Models\AttendanceLog;
use App\Models\HolidayCalendar;
use App\Services\Audit\AuditLogService;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Throwable;

class MyAttendanceController extends Controller
{
    private const TIMEZONE = 'Asia/Jakarta';
    private const NORMAL_END = '16:30:00';
    private const WEEKDAY_MIN_WORK_MINUTES = 480;
    private const OVERTIME_MINUTES = 30;

    public function __construct(private readonly AuditLogService $audit) {}

    public function index(Request $request): View
    {
        $user = $request->user();
        abort_unless(in_array((string) ($user?->role ?? ''), ['karyawan', 'vp'], true), 403);
        $employee = $user->employee;
        abort_unless($employee, 403, 'Akun karyawan belum terhubung ke data karyawan.');
        $now = Carbon::now(self::TIMEZONE);
        $month = (int) $request->input('month', $now->month);
        $year = (int) $request->input('year', $now->year);
        if ($month < 1 || $month > 12) $month = $now->month;
        if ($year < 2000 || $year > 2100) $year = $now->year;
        $today = $now->copy()->startOfDay();
        $todayAttendance = AttendanceLog::query()->where('employee_id', $employee->id)->whereDate('tanggal', $today->toDateString())->first();
        $startDate = Carbon::create($year, $month, 1, 0, 0, 0, self::TIMEZONE)->startOfMonth();
        $history = AttendanceLog::query()->where('employee_id', $employee->id)->whereBetween('tanggal', [$startDate->copy()->startOfDay(), $startDate->copy()->endOfMonth()->endOfDay()])->orderByDesc('tanggal')->paginate(10)->withQueryString();
        return view('attendance.my', compact('employee', 'today', 'todayAttendance', 'history', 'month', 'year') + ['employee' => $employee->loadMissing('division'), 'monthLabel' => $startDate->translatedFormat('F Y')]);
    }

    public function clockIn(Request $request): RedirectResponse
    {
        $user = $request->user();
        abort_unless(in_array((string) ($user?->role ?? ''), ['karyawan', 'vp'], true), 403);
        $employee = $user->employee;
        abort_unless($employee, 403, 'Akun karyawan belum terhubung ke data karyawan.');
        $now = Carbon::now(self::TIMEZONE);
        $date = $now->toDateString();
        $time = $now->format('H:i:s');
        try {
            DB::transaction(function () use ($employee, $date, $time): void {
                $attendance = AttendanceLog::query()->where('employee_id', $employee->id)->whereDate('tanggal', $date)->lockForUpdate()->first();
                if ($attendance) {
                    if ($attendance->jam_masuk) throw new \RuntimeException('Jam masuk hari ini sudah tercatat pada ' . substr($attendance->jam_masuk, 0, 5) . '.');
                    $before = $attendance->toArray();
                    $attendance->forceFill(['jam_masuk' => $time, 'jumlah_scan' => (int) $attendance->jumlah_scan + 1])->save();
                    $this->audit->record('CLOCK_IN', $attendance, $before, $attendance->fresh()->toArray(), 'Clock In melengkapi log presensi yang sudah ada.');
                    return;
                }
                $attendance = AttendanceLog::create(['employee_id' => $employee->id, 'tanggal' => $date, 'jam_masuk' => $time, 'jam_pulang' => null, 'sumber' => 'self_service', 'jumlah_scan' => 1]);
                $this->audit->record('CLOCK_IN', $attendance, null, $attendance->toArray(), 'Clock In mandiri karyawan.');
            });
        } catch (\RuntimeException $e) {
            return back()->withErrors(['attendance' => $e->getMessage()]);
        } catch (Throwable $e) {
            report($e);
            return back()->withErrors(['attendance' => 'Clock In gagal disimpan. Silakan coba lagi.']);
        }
        return back()->with('status', 'Clock In berhasil dicatat pada ' . Carbon::parse($time)->format('H:i') . '.');
    }

    public function clockOut(Request $request): RedirectResponse
    {
        $user = $request->user();
        abort_unless(in_array((string) ($user?->role ?? ''), ['karyawan', 'vp'], true), 403);
        $employee = $user->employee;
        abort_unless($employee, 403, 'Akun karyawan belum terhubung ke data karyawan.');
        $now = Carbon::now(self::TIMEZONE);
        $date = $now->toDateString();
        $time = $now->format('H:i:s');
        $overtimePrompt = null;
        try {
            DB::transaction(function () use ($employee, $user, $date, $time, &$overtimePrompt): void {
                $attendance = AttendanceLog::query()->where('employee_id', $employee->id)->whereDate('tanggal', $date)->lockForUpdate()->first();
                if (! $attendance || ! $attendance->jam_masuk) throw new \RuntimeException('Clock In belum tercatat. Lakukan Clock In terlebih dahulu.');
                if ($attendance->jam_pulang) throw new \RuntimeException('Jam pulang hari ini sudah tercatat pada ' . substr($attendance->jam_pulang, 0, 5) . '.');
                $before = $attendance->toArray();
                $attendance->forceFill(['jam_pulang' => $time, 'jumlah_scan' => (int) $attendance->jumlah_scan + 1])->save();
                $fresh = $attendance->fresh();
                $this->audit->record('CLOCK_OUT', $fresh, $before, $fresh->toArray(), 'Clock Out mandiri karyawan. Tidak membuat pengajuan lembur otomatis.');

                // Jangan resolve legal/company overtime policy saat Clock-Out.
                if (! $user->isKaryawan() || ! in_array((int) $employee->band_code, [2, 3], true)) return;
                $clockIn = Carbon::parse($date . ' ' . $fresh->jam_masuk, self::TIMEZONE);
                $clockOut = Carbon::parse($date . ' ' . $fresh->jam_pulang, self::TIMEZONE);
                $actualWorkMinutes = max(0, $clockIn->diffInMinutes($clockOut));
                $holiday = HolidayCalendar::query()->whereDate('tanggal', $date)->where('is_active', true)->exists();
                $dayType = $holiday ? 'NATIONAL_HOLIDAY' : ($clockOut->isWeekend() ? 'WEEKEND' : 'WORKDAY');

                if ($dayType === 'WORKDAY') {
                    $normalEnd = Carbon::parse($date . ' ' . self::NORMAL_END, self::TIMEZONE);
                    if (! $clockOut->greaterThan($normalEnd) || $actualWorkMinutes < self::WEEKDAY_MIN_WORK_MINUTES) return;
                    $start = $normalEnd;
                } else {
                    if ($actualWorkMinutes < self::OVERTIME_MINUTES) return;
                    $start = $clockIn;
                }

                $overtimePrompt = [
                    'date' => $date,
                    'clock_in' => $fresh->jam_masuk,
                    'clock_out' => $fresh->jam_pulang,
                    'band_code' => (int) $employee->band_code,
                    'day_type' => $dayType,
                    'start_time' => $start->format('H:i'),
                    'end_time' => $clockOut->format('H:i'),
                    'actual_work_minutes' => $actualWorkMinutes,
                    'minimum_minutes' => self::OVERTIME_MINUTES,
                ];
            });
        } catch (\RuntimeException $e) {
            return back()->withErrors(['attendance' => $e->getMessage()]);
        } catch (Throwable $e) {
            report($e);
            return back()->withErrors(['attendance' => 'Clock Out gagal disimpan. Silakan coba lagi.']);
        }
        $response = back()->with('status', 'Clock Out berhasil dicatat pada ' . Carbon::parse($time)->format('H:i') . '.');
        if ($overtimePrompt !== null) {
            session()->put('overtime.draft', $overtimePrompt);
            session()->flash('overtime.prompt', $overtimePrompt);
        } else {
            session()->forget('overtime.draft');
        }
        return $response;
    }

    public function dismissOvertimePrompt(Request $request): RedirectResponse
    {
        abort_unless($request->user()?->isKaryawan(), 403);
        session()->forget('overtime.draft');
        return back()->with('status', 'Presensi tetap dicatat sebagai hadir. Tidak ada pengajuan lembur dibuat.');
    }
}

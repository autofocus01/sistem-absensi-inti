<?php

namespace App\Services\Attendance;

use App\Models\AttendanceLog;
use App\Models\AttendanceRecap;
use App\Models\Employee;
use App\Models\HolidayCalendar;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class AttendanceRecapSyncService
{
    public function syncPeriod(int $year, int $month, ?array $employeeIds = null): int
    {
        $periodStart = Carbon::create($year, $month, 1)->startOfDay();
        $periodEnd = $periodStart->copy()->endOfMonth()->endOfDay();

        $holidayDates = HolidayCalendar::query()
            ->whereBetween('tanggal', [$periodStart->toDateString(), $periodEnd->toDateString()])
            ->where('is_active', true)
            ->whereIn('jenis', ['NATIONAL_HOLIDAY', 'CUTI_BERSAMA'])
            ->pluck('tanggal')
            ->map(fn ($date) => Carbon::parse($date)->toDateString())
            ->flip();

        $workdays = 0;
        for ($date = $periodStart->copy(); $date->lte($periodEnd); $date->addDay()) {
            if ($date->isWeekday() && ! $holidayDates->has($date->toDateString())) {
                $workdays++;
            }
        }

        $logsQuery = AttendanceLog::query()
            ->whereBetween('tanggal', [$periodStart->toDateString(), $periodEnd->toDateString()])
            ->whereNotNull('jam_masuk')
            ->when($employeeIds !== null, fn ($q) => $q->whereIn('employee_id', $employeeIds));

        $logs = $logsQuery
            ->orderBy('employee_id')
            ->orderBy('tanggal')
            ->orderByDesc('id')
            ->get();

        // Sinkronisasi juga harus bisa menghapus nilai fakta lama ketika seluruh log
        // pada suatu periode sudah dihapus/koreksi. Karena itu employee yang sudah
        // memiliki recap ikut diproses walaupun bulan tersebut tidak punya log baru.
        $employeeIdsToSync = collect($employeeIds ?? [])
            ->merge($logs->pluck('employee_id'))
            ->merge(AttendanceRecap::query()
                ->where('tahun', $year)
                ->where('bulan', $month)
                ->when($employeeIds !== null, fn ($q) => $q->whereIn('employee_id', $employeeIds))
                ->pluck('employee_id'))
            ->filter()
            ->unique()
            ->values();

        if ($employeeIdsToSync->isEmpty()) {
            return 0;
        }

        $synced = 0;

        foreach ($employeeIdsToSync as $employeeId) {
            $employee = Employee::find($employeeId);
            if (! $employee) {
                continue;
            }

            $employeeLogs = $logs->where('employee_id', $employee->id);

            // Satu hari = satu fakta presensi. Jika ada lebih dari satu row,
            // gunakan row terbaru berdasarkan ID.
            $dailyLogs = $employeeLogs
                ->unique(fn (AttendanceLog $log) => $log->tanggal?->format('Y-m-d'))
                ->values();

            $hadir = (int) $dailyLogs
                ->filter(fn (AttendanceLog $log) => $log->tanggal?->isWeekday() && ! $holidayDates->has($log->tanggal->toDateString()))
                ->count();

            $menitTelat = (int) $dailyLogs->sum(fn (AttendanceLog $log) => $log->menitTelat());
            $telatHari = (int) $dailyLogs->filter(fn (AttendanceLog $log) => $log->menitTelat() > 0)->count();

            DB::transaction(function () use (
                $employee,
                $year,
                $month,
                $workdays,
                $hadir,
                $menitTelat,
                $telatHari,
                &$synced
            ): void {
                $recap = AttendanceRecap::query()
                    ->where('employee_id', $employee->id)
                    ->where('tahun', $year)
                    ->where('bulan', $month)
                    ->lockForUpdate()
                    ->first();

                // Data non-presensi (cuti/sakit/ijin/perdin) adalah input HR.
                // Jangan ditimpa ketika sinkronisasi dari AttendanceLog.
                $perdin = (int) ($recap?->perdin ?? 0);
                $cuti = (int) ($recap?->cuti ?? 0);
                $sakit = (int) ($recap?->sakit ?? 0);
                $ijin = (int) ($recap?->ijin ?? 0);

                $alpha = max(0, $workdays - $hadir - $perdin - $cuti - $sakit - $ijin);

                $payload = [
                    'employee_id' => $employee->id,
                    'tahun' => $year,
                    'bulan' => $month,
                    'hari_kerja' => $workdays,
                    'hadir' => $hadir,
                    'perdin' => $perdin,
                    'cuti' => $cuti,
                    'sakit' => $sakit,
                    'ijin' => $ijin,
                    'alpha' => $alpha,
                    'telat_hari' => $telatHari,
                    'menit_telat' => $menitTelat,
                    'lokasi_bandung' => (int) ($recap?->lokasi_bandung ?? 0),
                    'lokasi_jakarta' => (int) ($recap?->lokasi_jakarta ?? 0),
                    'koreksi_by_admin' => (int) ($recap?->koreksi_by_admin ?? 0),
                ];

                if ($recap) {
                    $recap->update($payload);
                } else {
                    AttendanceRecap::create($payload);
                }

                $synced++;
            });
        }

        return $synced;
    }
}

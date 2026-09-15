<?php

namespace App\Http\Controllers;

use App\Models\AttendanceLog;
use App\Models\AttendanceRecap;
use App\Models\Employee;
use Illuminate\Http\Request;

class TimesheetController extends Controller
{
    public function index(Request $request)
    {
        $q = $request->input('q');

        $employees = Employee::orderBy('nama')
            ->when($q, function ($query) use ($q) {
                $query->where(function ($qr) use ($q) {
                    $qr->where('nama', 'like', "%{$q}%")->orWhere('nipeg', 'like', "%{$q}%");
                });
            })
            ->get();

        $employeeId = (int) $request->input('employee_id', $employees->first()?->id);
        $bulan = (int) $request->input('bulan', now()->month);
        $tahun = (int) $request->input('tahun', now()->year);

        $baseQuery = AttendanceLog::query()
            ->where('employee_id', $employeeId)
            ->whereYear('tanggal', $tahun)
            ->whereMonth('tanggal', $bulan);

        $semuaLogBulanIni = (clone $baseQuery)->get();

        $totalMenitLembur = $semuaLogBulanIni->sum(fn (AttendanceLog $log) => $log->menitLembur());
        $totalHariHadir = $semuaLogBulanIni->whereNotNull('jam_masuk')->count();
        $totalHariTelat = $semuaLogBulanIni->filter(fn (AttendanceLog $log) => $log->menitTelat() > 0)->count();

        // Perdin/Cuti/Izin/Sakit/Alpha & lokasi Bandung/Jakarta datanya dari rekap BULANAN,
        // bukan dari log harian - jadi diambil terpisah untuk karyawan+periode yang sama.
        $rekapBulanIni = AttendanceRecap::where('employee_id', $employeeId)
            ->where('tahun', $tahun)
            ->where('bulan', $bulan)
            ->first();

        $logs = (clone $baseQuery)
            ->with('employee')
            ->orderByDesc('tanggal')
            ->paginate(15)
            ->withQueryString();

        return view('timesheet.index', [
            'logs'           => $logs,
            'employees'      => $employees,
            'employeeId'     => $employeeId,
            'q'              => $q,
            'bulan'          => $bulan,
            'tahun'          => $tahun,
            'totalJamLembur' => round($totalMenitLembur / 60, 1),
            'totalHariHadir' => $totalHariHadir,
            'totalHariTelat' => $totalHariTelat,
            'rekapBulanIni'  => $rekapBulanIni,
        ]);
    }
}
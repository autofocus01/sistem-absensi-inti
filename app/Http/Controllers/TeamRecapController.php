<?php

namespace App\Http\Controllers;

use App\Models\AttendanceLog;
use App\Models\Division;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;

class TeamRecapController extends Controller
{
    public function index(Request $request)
    {
        $divisions = Division::orderBy('nama')->get();
        $divisionId = $request->input('division_id');
        $bulan = (int) $request->input('bulan', now()->month);
        $tahun = (int) $request->input('tahun', now()->year);

        $logsQuery = AttendanceLog::with(['employee.division', 'approver'])
            ->whereYear('tanggal', $tahun)
            ->whereMonth('tanggal', $bulan)
            ->when($divisionId, fn ($q) => $q->whereHas('employee', fn ($e) => $e->where('division_id', $divisionId)))
            ->where('jam_masuk', '!=', null);

        $semuaLog = (clone $logsQuery)->get();

        // Cuma baris yang beneran ada lemburnya yang relevan buat direview - dihitung ulang dari
        // method model (bukan kolom tersimpan), biar konsisten dengan yang tampil di Timesheet.
        $logLembur = $semuaLog->filter(fn (AttendanceLog $log) => $log->menitLembur() > 0)
            ->sortByDesc('tanggal')
            ->values();

        $totalPersonel = $semuaLog->pluck('employee_id')->unique()->count();
        $totalMenitLemburPending = $logLembur->where('status_lembur', 'pending')->sum(fn (AttendanceLog $l) => $l->menitLembur());
        $totalMenitLemburDisetujui = $logLembur->where('status_lembur', 'disetujui')->sum(fn (AttendanceLog $l) => $l->menitLembur());
        $totalHariTelat = $semuaLog->filter(fn (AttendanceLog $log) => $log->menitTelat() > 0)->count();

        $page = (int) $request->input('page', 1);
        $perPage = 20;
        $logLemburPaginated = new LengthAwarePaginator(
            $logLembur->forPage($page, $perPage)->values(),
            $logLembur->count(),
            $perPage,
            $page,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        return view('team-recap.index', [
            'divisions'                 => $divisions,
            'divisionId'                => $divisionId,
            'bulan'                     => $bulan,
            'tahun'                     => $tahun,
            'logs'                      => $logLemburPaginated,
            'totalLogLembur'            => $logLembur->count(),
            'totalPersonel'             => $totalPersonel,
            'totalJamLemburPending'     => round($totalMenitLemburPending / 60, 1),
            'totalJamLemburDisetujui'   => round($totalMenitLemburDisetujui / 60, 1),
            'totalHariTelat'            => $totalHariTelat,
        ]);
    }

    public function approve(Request $request, AttendanceLog $attendanceLog)
    {
        $attendanceLog->approve(Auth::user());

        return back()->with('status', 'Lembur disetujui.');
    }

    public function reject(Request $request, AttendanceLog $attendanceLog)
    {
        $attendanceLog->reject(Auth::user());

        return back()->with('status', 'Lembur ditolak.');
    }

    public function approveAllPending(Request $request)
    {
        $bulan = (int) $request->input('bulan', now()->month);
        $tahun = (int) $request->input('tahun', now()->year);
        $divisionId = $request->input('division_id');

        $logs = AttendanceLog::with('employee')
            ->whereYear('tanggal', $tahun)
            ->whereMonth('tanggal', $bulan)
            ->where('status_lembur', 'pending')
            ->when($divisionId, fn ($q) => $q->whereHas('employee', fn ($e) => $e->where('division_id', $divisionId)))
            ->get()
            ->filter(fn (AttendanceLog $log) => $log->menitLembur() > 0);

        foreach ($logs as $log) {
            $log->approve(Auth::user());
        }

        return back()->with('status', "{$logs->count()} lembur pending berhasil disetujui sekaligus.");
    }
}
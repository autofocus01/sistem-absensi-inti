<?php

namespace App\Http\Controllers;

use App\Models\AttendanceImportError;
use App\Services\Audit\AuditLogService;
use Illuminate\Http\Request;

class AttendanceImportErrorController extends Controller
{
    public function __construct(
        private readonly AuditLogService $audit,
    ) {
    }

    public function index()
    {
        $importErrors = AttendanceImportError::orderBy('status')
            ->orderByDesc('created_at')
            ->paginate(20);

        $totalPending = AttendanceImportError::where('status', 'pending')->count();
        $totalSelesai = AttendanceImportError::where('status', 'selesai')->count();
        $sebaranJenis = [
            'Absensi Harian' => AttendanceImportError::where('jenis', 'absensi_harian')->count(),
            'Rekap Bulanan'  => AttendanceImportError::where('jenis', 'rekap_bulanan')->count(),
        ];

        return view('attendance.import-errors', compact('importErrors', 'totalPending', 'totalSelesai', 'sebaranJenis'));
    }

    public function resolve(Request $request, AttendanceImportError $importError)
    {
        if ($importError->status === 'selesai') {
            return back()->with('status', 'Baris anomali sudah berstatus selesai.');
        }

        $before = $importError->toArray();
        $importError->update(['status' => 'selesai']);
        $fresh = $importError->fresh();

        $this->audit->record(
            'ATTENDANCE_IMPORT_ERROR_RESOLVED',
            $fresh,
            $before,
            $fresh->toArray(),
            'Baris error import ditandai selesai oleh HR.',
            $request
        );

        return back()->with('status', 'Baris anomali ditandai selesai.');
    }
}

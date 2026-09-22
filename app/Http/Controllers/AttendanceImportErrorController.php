<?php

namespace App\Http\Controllers;

use App\Models\AttendanceImportError;
use App\Services\Audit\AuditLogService;
use Illuminate\Http\Request;

class AttendanceImportErrorController extends Controller
{
    public function index()
    {
        $importErrors = AttendanceImportError::orderBy('status')
            ->orderByDesc('created_at')
            ->paginate(20);

        // Ringkasan buat KPI & grafik - dihitung dari SELURUH data, bukan cuma halaman yang lagi tampil.
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
        $before = $importError->toArray();
        $importError->update(['status' => 'selesai']);

        $this->audit->record(
            'ATTENDANCE_IMPORT_ERROR_RESOLVED',
            $importError,
            $before,
            $importError->fresh()->toArray(),
            'Baris error import ditandai selesai oleh HR.',
            $request
        );

        return back()->with('status', 'Baris anomali ditandai selesai.');
    }
}
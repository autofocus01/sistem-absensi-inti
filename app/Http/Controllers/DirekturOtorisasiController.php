<?php

namespace App\Http\Controllers;

use App\Models\AttendanceLog;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DirekturOtorisasiController extends Controller
{
    /**
     * Otorisasi khusus adalah jalur eksekutif terpisah dari
     * approval lembur normal Karyawan -> VP -> HR.
     *
     * Route sudah dilindungi role:direktur_utama; pengecekan role
     * di controller dipertahankan sebagai defense in depth.
     */
    public function approve(Request $request, AttendanceLog $attendanceLog): RedirectResponse
    {
        abort_unless($request->user()?->isDirekturUtama(), 403);

        DB::transaction(function () use ($attendanceLog, $request): void {
            $log = AttendanceLog::query()
                ->lockForUpdate()
                ->findOrFail($attendanceLog->getKey());

            $this->assertPendingSpecialAuthorization($log);

            $log->approve($request->user());
        });

        return redirect()
            ->to(route('direktur.dashboard') . '#otorisasi')
            ->with('status', 'Lembur khusus disetujui.');
    }

    public function reject(Request $request, AttendanceLog $attendanceLog): RedirectResponse
    {
        abort_unless($request->user()?->isDirekturUtama(), 403);

        DB::transaction(function () use ($attendanceLog, $request): void {
            $log = AttendanceLog::query()
                ->lockForUpdate()
                ->findOrFail($attendanceLog->getKey());

            $this->assertPendingSpecialAuthorization($log);

            $log->reject($request->user());
        });

        return redirect()
            ->to(route('direktur.dashboard') . '#otorisasi')
            ->with('status', 'Lembur khusus ditolak.');
    }

    private function assertPendingSpecialAuthorization(AttendanceLog $log): void
    {
        abort_unless($log->status_lembur === 'pending', 409);

        abort_unless(
            $log->jam_pulang !== null
            && $log->butuhOtorisasiKhusus(),
            409
        );
    }
}

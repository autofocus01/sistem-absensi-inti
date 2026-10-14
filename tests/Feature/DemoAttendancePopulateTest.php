<?php

use App\Models\AttendanceAnomaly;
use App\Models\AttendanceLog;
use App\Models\AttendanceRecap;
use App\Models\AuditLog;
use App\Models\Employee;
use App\Models\OvertimeSubmission;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;

it('mengisi data demo kehadiran, rekap, lembur, dan anomali secara konsisten', function () {
    config(['app.demo_mode' => true]);
    $this->seed();

    $exit = Artisan::call('demo:attendance-populate', [
        '--months' => 2,
        '--seed' => 42,
        '--fresh' => true,
        '--no-interaction' => true,
    ]);

    expect($exit)->toBe(0);

    $employees = Employee::count();
    expect($employees)->toBeGreaterThan(40);

    // Log terisi untuk dua bulan penuh, rekap ada untuk semua karyawan.
    expect(AttendanceLog::count())->toBeGreaterThan(1000);
    expect(AttendanceRecap::count())->toBe($employees * 2);

    // Invariant model AttendanceRecap: total hari = hari kerja.
    AttendanceRecap::query()->each(function (AttendanceRecap $recap) {
        $total = $recap->hadir + $recap->perdin + $recap->cuti
            + $recap->sakit + $recap->ijin + $recap->alpha;

        expect($total)->toBe((int) $recap->hari_kerja);
    });

    // Kesehatan chart untuk presentasi: bulan berjalan ikut terisi sehingga
    // rata-rata kehadiran tetap tinggi dan alpha tetap kecil (donut sehat).
    expect(AttendanceRecap::query()->avg('persen_kehadiran'))->toBeGreaterThan(80);
    expect(AttendanceRecap::query()->min('persen_kehadiran'))->toBeGreaterThan(40);
    expect((int) AttendanceRecap::query()->sum('alpha'))
        ->toBeLessThan((int) (AttendanceRecap::query()->sum('hadir') * 0.1));

    // Lembur dihitung lewat policy asli, lengkap dengan snapshot dan audit trail.
    expect(OvertimeSubmission::count())->toBeGreaterThan(0);
    expect(OvertimeSubmission::where('status', 'VERIFIED_HR')->count())->toBeGreaterThan(0);
    // Variasi status cukup lebar untuk menampilkan distribusi workflow.
    expect(OvertimeSubmission::query()->distinct()->count('status'))->toBeGreaterThanOrEqual(3);
    expect(OvertimeSubmission::count())
        ->toBe(OvertimeSubmission::where('eligibility_status', 'ELIGIBLE')->count());
    expect(DB::table('overtime_policy_snapshots')->count())->toBe(OvertimeSubmission::count());
    expect(
        AuditLog::query()->where('subject_type', (new OvertimeSubmission)->getMorphClass())->count()
    )->toBeGreaterThan(0);

    // Anomali terdeteksi lewat AttendanceAnomalyService (mis. duplicate scan).
    expect(AttendanceAnomaly::query()->count())->toBeGreaterThan(0);

    // Idempoten: run ulang tanpa --fresh tidak menambah atau mengubah jumlah data.
    $snapshotCounts = [
        AttendanceLog::count(),
        AttendanceRecap::count(),
        OvertimeSubmission::count(),
        AttendanceAnomaly::count(),
        AuditLog::query()->where('subject_type', (new OvertimeSubmission)->getMorphClass())->count(),
    ];

    $exit = Artisan::call('demo:attendance-populate', [
        '--months' => 2,
        '--seed' => 42,
        '--no-interaction' => true,
    ]);

    expect($exit)->toBe(0);
    expect([
        AttendanceLog::count(),
        AttendanceRecap::count(),
        OvertimeSubmission::count(),
        AttendanceAnomaly::count(),
        AuditLog::query()->where('subject_type', (new OvertimeSubmission)->getMorphClass())->count(),
    ])->toBe($snapshotCounts);
});

it('menolak dijalankan ketika DEMO_MODE dimatikan', function () {
    config(['app.demo_mode' => false]);
    $this->seed();

    $exit = Artisan::call('demo:attendance-populate', ['--no-interaction' => true]);

    expect($exit)->toBe(1);
    expect(AttendanceLog::count())->toBe(0);
});

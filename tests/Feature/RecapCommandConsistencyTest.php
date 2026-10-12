<?php

use App\Models\AttendanceLog;
use App\Models\AttendanceRecap;
use App\Models\Division;
use App\Models\Employee;
use Illuminate\Support\Facades\Artisan;

function makeRecapTestEmployee(string $suffix): Employee
{
    $division = Division::create(['nama' => 'Div ' . $suffix, 'lini' => 'UAT']);
    return Employee::create(['nipeg' => 'RC' . $suffix, 'nama' => 'Test ' . $suffix, 'division_id' => $division->id, 'band_code' => 2]);
}

it('generate-recap mempertahankan cuti/sakit/ijin milik HR (tidak direset jadi 0)', function () {
    $employee = makeRecapTestEmployee(uniqid());

    // Recap dengan input HR: cuti 3 hari
    AttendanceRecap::create([
        'employee_id' => $employee->id, 'tahun' => 2026, 'bulan' => 9,
        'hari_kerja' => 22, 'hadir' => 5, 'alpha' => 14,
        'cuti' => 3, 'sakit' => 0, 'ijin' => 0, 'perdin' => 0,
    ]);

    // Log presensi aktual: hadir 1 hari kerja
    AttendanceLog::create([
        'employee_id' => $employee->id, 'tanggal' => '2026-09-08',
        'jam_masuk' => '07:30:00', 'jam_pulang' => '16:30:00', 'sumber' => 'self_service', 'jumlah_scan' => 2,
    ]);

    Artisan::call('attendance:generate-recap', ['--month' => '9', '--year' => '2026', '--employee' => $employee->nipeg]);

    $recap = AttendanceRecap::where('employee_id', $employee->id)->where('tahun', 2026)->where('bulan', 9)->first();
    expect($recap->cuti)->toBe(3); // input HR dipertahankan
    expect($recap->hadir)->toBe(1); // dari AttendanceLog
    expect($recap->alpha)->toBe(22 - 1 - 3); // workdays - hadir - cuti (konsisten dengan sync service)
});

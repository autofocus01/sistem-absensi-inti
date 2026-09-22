<?php

use App\Models\AttendanceLog;
use App\Models\Division;
use App\Models\Employee;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

function makeHrImportUser(): User
{
    return User::factory()->create(['role' => 'hr_admin']);
}

function makeImportEmployee(): Employee
{
    $division = Division::create(['nama' => 'Import Test '.uniqid(), 'lini' => 'Test']);
    return Employee::create(['nipeg' => 'IMP'.uniqid(), 'nama' => 'Import Test', 'jabatan' => 'Staff', 'division_id' => $division->id]);
}

it('previews and commits a valid paired attendance file idempotently', function () {
    Storage::fake('local');
    $user = makeHrImportUser();
    $employee = makeImportEmployee();

    $csv = "NIP,Tanggal,Jam Masuk,Jam Pulang\n{$employee->nipeg},2026-09-21,07:30,18:12\n";
    $file = UploadedFile::fake()->createWithContent('absensi.csv', $csv);

    $this->actingAs($user)->post(route('attendance.import.production.preview'), ['file' => $file])->assertOk();
    $path = session('attendance_import_path');
    expect($path)->not->toBeNull();

    $this->actingAs($user)->post(route('attendance.import.production.commit'))->assertRedirect(route('attendance.import.production'));
    expect(AttendanceLog::where('employee_id', $employee->id)->whereDate('tanggal', '2026-09-21')->count())->toBe(1);
    expect(AttendanceLog::where('employee_id', $employee->id)->first()->jam_pulang)->toBe('18:12:00');
});

it('rejects unknown employees without creating attendance logs', function () {
    Storage::fake('local');
    $user = makeHrImportUser();
    $csv = "NIP,Tanggal,Jam Masuk,Jam Pulang\nUNKNOWN,2026-09-21,07:30,16:30\n";
    $file = UploadedFile::fake()->createWithContent('absensi.csv', $csv);

    $response = $this->actingAs($user)->post(route('attendance.import.production.preview'), ['file' => $file]);
    $response->assertOk()->assertSee('NIP tidak ditemukan');
    expect(AttendanceLog::count())->toBe(0);
});

it('aggregates scan-format attendance into earliest in and latest out', function () {
    Storage::fake('local');
    $user = makeHrImportUser();
    $employee = makeImportEmployee();

    $csv = "NIP,Tanggal,Jam\n{$employee->nipeg},2026-09-22,07:29\n{$employee->nipeg},2026-09-22,07:31\n{$employee->nipeg},2026-09-22,16:30\n{$employee->nipeg},2026-09-22,18:12\n";
    $file = UploadedFile::fake()->createWithContent('scan.csv', $csv);

    $this->actingAs($user)->post(route('attendance.import.production.preview'), ['file' => $file])->assertOk();
    $this->actingAs($user)->post(route('attendance.import.production.commit'))->assertRedirect(route('attendance.import.production'));

    $attendance = AttendanceLog::where('employee_id', $employee->id)->whereDate('tanggal', '2026-09-22')->first();
    expect($attendance)->not->toBeNull();
    expect($attendance->jam_masuk)->toBe('07:29:00');
    expect($attendance->jam_pulang)->toBe('18:12:00');
    expect($attendance->jumlah_scan)->toBe(4);
});

it('does not silently overwrite a non-import attendance source when values differ', function () {
    Storage::fake('local');
    $user = makeHrImportUser();
    $employee = makeImportEmployee();

    AttendanceLog::create([
        'employee_id' => $employee->id,
        'tanggal' => '2026-09-22',
        'jam_masuk' => '07:30:00',
        'jam_pulang' => '16:30:00',
        'sumber' => 'face_id',
        'jumlah_scan' => 2,
    ]);

    $csv = "NIP,Tanggal,Jam Masuk,Jam Pulang\n{$employee->nipeg},2026-09-22,07:29,18:12\n";
    $file = UploadedFile::fake()->createWithContent('absensi.csv', $csv);

    $this->actingAs($user)->post(route('attendance.import.production.preview'), ['file' => $file])->assertOk();
    $this->actingAs($user)->post(route('attendance.import.production.commit'))->assertRedirect(route('attendance.import.production'));

    $attendance = AttendanceLog::where('employee_id', $employee->id)->whereDate('tanggal', '2026-09-22')->first();
    expect($attendance->jam_masuk)->toBe('07:30:00');
    expect($attendance->jam_pulang)->toBe('16:30:00');
    expect($attendance->sumber)->toBe('face_id');
});

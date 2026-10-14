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
    return testPersistEmployee(['nipeg' => 'IMP'.uniqid(), 'nama' => 'Import Test', 'jabatan' => 'Staff', 'division_id' => $division->id]);
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


it('normalizes Excel-like numeric NIP and preserves real Excel row number after a title row', function () {
    Storage::fake('local');
    $user = makeHrImportUser();
    $employee = makeImportEmployee();
    $employee->update(['nipeg' => '90010001']);

    $csv = "LAPORAN ABSENSI,,,,\n"
        . "NIP,Tanggal,Jam Masuk,Jam Pulang\n"
        . "90010001.0,2026-09-23,07:30,16:30\n"
        . "UNKNOWN.0,2026-09-23,07:30,16:30\n";

    $file = UploadedFile::fake()->createWithContent('absensi-title.csv', $csv);

    $response = $this->actingAs($user)
        ->post(route('attendance.import.production.preview'), ['file' => $file]);

    $response->assertOk()
        ->assertSee('Baris 4')
        ->assertSee('NIP tidak ditemukan');

    expect(session('attendance_import_path'))->not->toBeNull();
});

/*
|--------------------------------------------------------------------------
| REGRESSION: commit import harus menyinkronkan AttendanceRecap untuk
| periode yang terpengaruh. Sebelumnya upsertAttendance dipanggil tanpa
| parameter by-reference $syncPeriods, sehingga syncPeriod tidak pernah
| dijalankan dan rekap HR menampilkan data stale setelah import.
|--------------------------------------------------------------------------
*/
it('syncs attendance recap after a successful import commit', function () {
    Storage::fake('local');
    $user = makeHrImportUser();
    $employee = makeImportEmployee();

    $csv = "NIP,Tanggal,Jam Masuk,Jam Pulang\n{$employee->nipeg},2026-09-24,07:30,16:30\n";
    $file = UploadedFile::fake()->createWithContent('absensi.csv', $csv);

    $this->actingAs($user)->post(route('attendance.import.production.preview'), ['file' => $file])->assertOk();
    $this->actingAs($user)->post(route('attendance.import.production.commit'))->assertRedirect(route('attendance.import.production'));

    $recap = \App\Models\AttendanceRecap::query()
        ->where('employee_id', $employee->id)
        ->where('tahun', 2026)
        ->where('bulan', 9)
        ->first();

    expect($recap)->not->toBeNull('AttendanceRecap harus tersinkron setelah commit import.');
    expect($recap->hadir)->toBeGreaterThanOrEqual(1);
});

/*
|--------------------------------------------------------------------------
| FASE 1: PROVENANCE — setiap commit impor harus mencatat batch-nya
| (nama file asli, sha256, operator, jumlah baris) dan menandai file
| yang pernah diimpor sebelumnya (deteksi duplikat via hash isi file).
|--------------------------------------------------------------------------
*/
it('records import batch provenance and flags a re-imported file', function () {
    Storage::fake('local');
    $user = makeHrImportUser();
    $employee = makeImportEmployee();

    $csv = "NIP,Tanggal,Jam Masuk,Jam Pulang\n{$employee->nipeg},2026-09-25,07:30,16:30\n";

    $file = UploadedFile::fake()->createWithContent('absensi-provenance.csv', $csv);
    $this->actingAs($user)->post(route('attendance.import.production.preview'), ['file' => $file])->assertOk();
    $this->actingAs($user)
        ->post(route('attendance.import.production.commit'))
        ->assertRedirect(route('attendance.import.production'))
        ->assertSessionHas('import_result.batch');

    $batch = \App\Models\ImportBatch::latest('id')->first();
    expect($batch)->not->toBeNull();
    expect($batch->jenis)->toBe('absensi_harian');
    expect($batch->nama_file)->toBe('absensi-provenance.csv');
    expect($batch->file_hash)->toHaveLength(64);
    expect($batch->user_id)->toBe($user->id);
    expect($batch->format)->toBe('paired');
    expect($batch->total_baris)->toBe(1);
    expect($batch->berhasil)->toBe(1);
    expect($batch->gagal)->toBe(0);
    expect($batch->periode)->toBe(['2026-09']);
    expect($batch->duplikat)->toBeFalse();

    // File dengan isi identik diimpor ulang -> batch kedua ditandai duplikat.
    $file2 = UploadedFile::fake()->createWithContent('absensi-provenance.csv', $csv);
    $this->actingAs($user)->post(route('attendance.import.production.preview'), ['file' => $file2])->assertOk();
    $this->actingAs($user)
        ->post(route('attendance.import.production.commit'))
        ->assertRedirect(route('attendance.import.production'))
        ->assertSessionHas('import_result.peringatan');

    $batches = \App\Models\ImportBatch::latest('id')->get();
    expect($batches)->toHaveCount(2);
    expect($batches->first()->duplikat)->toBeTrue();
    expect($batches->last()->duplikat)->toBeFalse();
});

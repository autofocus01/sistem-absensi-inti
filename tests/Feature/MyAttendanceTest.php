<?php

use App\Models\AttendanceLog;
use App\Models\Division;
use App\Models\Employee;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Date;

uses(RefreshDatabase::class);

function makeKaryawanWithEmployee(): array
{
    $user = User::factory()->create([
        'role' => 'karyawan',
    ]);

    $division = Division::create([
        'nama' => 'Divisi Test ' . $user->id,
        'lini' => 'Test',
    ]);

    $employee = Employee::create([
        'nipeg' => 'TEST-' . $user->id,
        'nama' => $user->name,
        'division_id' => $division->id,
        'user_id' => $user->id,
    ]);

    return [$user, $employee];
}

test('karyawan dapat membuka halaman absensi saya', function () {
    [$user] = makeKaryawanWithEmployee();

    $this->actingAs($user)
        ->get(route('attendance.my'))
        ->assertOk()
        ->assertSee('Absensi Saya')
        ->assertSee('Clock In');
});

test('role selain karyawan tidak dapat membuka absensi saya', function () {
    [$user] = makeKaryawanWithEmployee();
    $user->forceFill(['role' => 'hr_admin'])->save();

    $this->actingAs($user)
        ->get(route('attendance.my'))
        ->assertForbidden();
});

test('clock in membuat attendance log untuk employee yang sedang login', function () {
    [$user, $employee] = makeKaryawanWithEmployee();

    Date::setTestNow('2026-09-21 07:45:00');

    $this->actingAs($user)
        ->post(route('attendance.my.clock-in'))
        ->assertRedirect()
        ->assertSessionHas('status');

    $this->assertDatabaseHas('attendance_logs', [
        'employee_id' => $employee->id,
        'tanggal' => '2026-09-21 00:00:00',
        'jam_masuk' => '07:45:00',
        'jam_pulang' => null,
        'sumber' => 'self_service',
        'jumlah_scan' => 1,
    ]);
});

test('clock in kedua tidak menimpa jam masuk yang sudah tercatat', function () {
    [$user, $employee] = makeKaryawanWithEmployee();

    AttendanceLog::create([
        'employee_id' => $employee->id,
        'tanggal' => '2026-09-21 00:00:00',
        'jam_masuk' => '07:40:00',
        'sumber' => 'mesin_fingerprint',
        'jumlah_scan' => 1,
    ]);

    Date::setTestNow('2026-09-21 08:00:00');

    $this->actingAs($user)
        ->post(route('attendance.my.clock-in'))
        ->assertRedirect()
        ->assertSessionHasErrors('attendance');

    $this->assertDatabaseHas('attendance_logs', [
        'employee_id' => $employee->id,
        'tanggal' => '2026-09-21 00:00:00',
        'jam_masuk' => '07:40:00',
        'jumlah_scan' => 1,
    ]);
});

test('clock out hanya mengubah attendance milik employee yang login', function () {
    [$user, $employee] = makeKaryawanWithEmployee();

    AttendanceLog::create([
        'employee_id' => $employee->id,
        'tanggal' => '2026-09-21 00:00:00',
        'jam_masuk' => '07:35:00',
        'sumber' => 'self_service',
        'jumlah_scan' => 1,
    ]);

    Date::setTestNow('2026-09-21 16:45:00');

    $this->actingAs($user)
        ->post(route('attendance.my.clock-out'))
        ->assertRedirect()
        ->assertSessionHas('status');

    $this->assertDatabaseHas('attendance_logs', [
        'employee_id' => $employee->id,
        'tanggal' => '2026-09-21 00:00:00',
        'jam_masuk' => '07:35:00',
        'jam_pulang' => '16:45:00',
        'jumlah_scan' => 2,
    ]);
});

test('clock out ditolak jika clock in belum ada', function () {
    [$user] = makeKaryawanWithEmployee();

    Date::setTestNow('2026-09-21 16:45:00');

    $this->actingAs($user)
        ->post(route('attendance.my.clock-out'))
        ->assertRedirect()
        ->assertSessionHasErrors('attendance');
});


test('riwayat absensi menampilkan record datetime di dalam bulan yang dipilih', function () {
    [$user, $employee] = makeKaryawanWithEmployee();

    AttendanceLog::create([
        'employee_id' => $employee->id,
        'tanggal' => '2026-09-21 00:00:00',
        'jam_masuk' => '07:40:00',
        'jam_pulang' => '16:45:00',
        'sumber' => 'self_service',
        'jumlah_scan' => 2,
    ]);

    $this->actingAs($user)
        ->get(route('attendance.my', ['month' => 9, 'year' => 2026]))
        ->assertOk()
        ->assertSee('21 Sep 2026')
        ->assertSee('07:40')
        ->assertSee('16:45');
});

test('clock in mengabaikan employee tanggal dan jam yang dikirim client', function () {
    [$user, $employee] = makeKaryawanWithEmployee();
    [, $otherEmployee] = makeKaryawanWithEmployee();

    Date::setTestNow('2026-09-21 07:45:00');

    $this->actingAs($user)
        ->post(route('attendance.my.clock-in'), [
            'employee_id' => $otherEmployee->id,
            'tanggal' => '2020-01-01',
            'jam_masuk' => '01:01:01',
        ])
        ->assertRedirect()
        ->assertSessionHas('status');

    $this->assertDatabaseHas('attendance_logs', [
        'employee_id' => $employee->id,
        'tanggal' => '2026-09-21 00:00:00',
        'jam_masuk' => '07:45:00',
    ]);

    $this->assertDatabaseMissing('attendance_logs', [
        'employee_id' => $otherEmployee->id,
        'tanggal' => '2020-01-01',
        'jam_masuk' => '01:01:01',
    ]);
});

test('clock out mengabaikan employee tanggal dan jam yang dikirim client', function () {
    [$user, $employee] = makeKaryawanWithEmployee();
    [, $otherEmployee] = makeKaryawanWithEmployee();

    AttendanceLog::create([
        'employee_id' => $employee->id,
        'tanggal' => '2026-09-21 00:00:00',
        'jam_masuk' => '07:35:00',
        'sumber' => 'self_service',
        'jumlah_scan' => 1,
    ]);

    Date::setTestNow('2026-09-21 16:45:00');

    $this->actingAs($user)
        ->post(route('attendance.my.clock-out'), [
            'employee_id' => $otherEmployee->id,
            'tanggal' => '2020-01-01',
            'jam_pulang' => '01:01:01',
        ])
        ->assertRedirect()
        ->assertSessionHas('status');

    $this->assertDatabaseHas('attendance_logs', [
        'employee_id' => $employee->id,
        'tanggal' => '2026-09-21 00:00:00',
        'jam_pulang' => '16:45:00',
    ]);
});

test('keterlambatan mengikuti batas tepat 07:30 tanpa grace period', function () {
    [$user, $employee] = makeKaryawanWithEmployee();

    AttendanceLog::create([
        'employee_id' => $employee->id,
        'tanggal' => '2026-09-21 00:00:00',
        'jam_masuk' => '07:31:00',
        'sumber' => 'self_service',
        'jumlah_scan' => 1,
    ]);

    $this->actingAs($user)
        ->get(route('attendance.my', ['month' => 9, 'year' => 2026]))
        ->assertOk()
        ->assertSee('1 menit');
});

<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('direktur utama dapat membuka dashboard eksekutif tanpa legacy AttendanceLog overtime method', function () {
    $user = User::factory()->create([
        'role' => 'direktur_utama',
    ]);

    $response = $this->actingAs($user)
        ->get(route('direktur.dashboard'));

    $response->assertOk();
});

test('non direktur tidak dapat membuka dashboard eksekutif', function () {
    $user = User::factory()->create([
        'role' => 'karyawan',
    ]);

    $response = $this->actingAs($user)
        ->get(route('direktur.dashboard'));

    $response->assertForbidden();
});
/*
|--------------------------------------------------------------------------
| FASE 3: KUALITAS DATA — dashboard direktur wajib menampilkan indikator
| integritas data (asal angka, impor terakhir, error pending) dan badge
| sumber per baris presensi.
|--------------------------------------------------------------------------
*/
test('dashboard direktur menampilkan indikator kualitas data dan badge sumber presensi', function () {
    $user = User::factory()->create([
        'role' => 'direktur_utama',
    ]);

    $division = \App\Models\Division::create(['nama' => 'Divisi Direksi', 'lini' => 'SEVP']);
    $employee = testPersistEmployee([
        'nipeg' => '90030001',
        'nama' => 'Karyawan Badge Sumber',
        'division_id' => $division->id,
    ]);

    \App\Models\AttendanceLog::create([
        'employee_id' => $employee->id,
        'tanggal' => now()->toDateString(),
        'jam_masuk' => '07:30:00',
        'jam_pulang' => '16:30:00',
        'sumber' => 'face_id',
        'jumlah_scan' => 2,
    ]);

    $response = $this->actingAs($user)
        ->get(route('direktur.dashboard'));

    $response->assertOk()
        ->assertSee('Kualitas Data')
        ->assertSee('Sumber Data Bulan Ini')
        ->assertSee('Impor Terakhir')
        ->assertSee('Face ID'); // badge sumber pada tabel riwayat/realtime
});

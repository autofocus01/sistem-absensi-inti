<?php

use App\Models\Division;
use App\Models\Employee;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

function makeEmployeeImportHr(): User
{
    return User::factory()->create(['role' => 'hr_admin']);
}

it('previews and commits a valid employee master import with create and update', function () {
    Storage::fake('local');
    $user = makeEmployeeImportHr();
    $division = Division::create([
        'nama' => 'Divisi Operasional',
        'lini' => 'SEVP',
    ]);

    $existing = Employee::create([
        'nipeg' => '90010001',
        'nama' => 'Nama Lama',
        'jabatan' => 'Staff Lama',
        'division_id' => $division->id,
    ]);

    $csv = implode("\n", [
        'NIPEG,Nama,Jenis Kelamin,Jabatan,No HP,Alamat,Divisi',
        '90010001,Nama Baru,L,Manager,08123456789,Bandung,Divisi Operasional',
        '90010002,Karyawan Baru,P,Staff,08129876543,Cimahi,Divisi Operasional',
    ]);

    $file = UploadedFile::fake()->createWithContent('karyawan.csv', $csv);

    $this->actingAs($user)
        ->post(route('employees.import.preview'), ['file' => $file])
        ->assertOk()
        ->assertSee('90010001')
        ->assertSee('UPDATE')
        ->assertSee('90010002')
        ->assertSee('CREATE');

    $this->actingAs($user)
        ->post(route('employees.import.commit'))
        ->assertRedirect(route('employees.import'));

    expect(Employee::where('nipeg', '90010001')->count())->toBe(1)
        ->and(Employee::where('nipeg', '90010001')->first()->nama)->toBe('Nama Baru')
        ->and(Employee::where('nipeg', '90010002')->count())->toBe(1)
        ->and(Employee::where('nipeg', '90010002')->first()->division_id)->toBe($division->id)
        ->and($existing->fresh()->user_id)->toBeNull();
});

it('rejects an unknown division before commit', function () {
    Storage::fake('local');
    $user = makeEmployeeImportHr();

    $csv = "NIPEG,Nama,Divisi\n90010003,Karyawan Baru,Divisi Tidak Ada\n";
    $file = UploadedFile::fake()->createWithContent('karyawan.csv', $csv);

    $this->actingAs($user)
        ->post(route('employees.import.preview'), ['file' => $file])
        ->assertOk()
        ->assertSee('Divisi \'Divisi Tidak Ada\' tidak ditemukan');

    $this->actingAs($user)
        ->post(route('employees.import.commit'))
        ->assertRedirect(route('employees.import'));

    expect(Employee::where('nipeg', '90010003')->exists())->toBeFalse();
});

it('rejects duplicate NIPEG inside one file', function () {
    Storage::fake('local');
    $user = makeEmployeeImportHr();
    Division::create(['nama' => 'Divisi Operasional', 'lini' => 'SEVP']);

    $csv = implode("\n", [
        'NIPEG,Nama,Divisi',
        '90010004,Karyawan A,Divisi Operasional',
        '90010004,Karyawan B,Divisi Operasional',
    ]);

    $file = UploadedFile::fake()->createWithContent('duplikat.csv', $csv);

    $this->actingAs($user)
        ->post(route('employees.import.preview'), ['file' => $file])
        ->assertOk()
        ->assertSee('NIPEG duplikat di dalam file');

    expect(Employee::where('nipeg', '90010004')->exists())->toBeFalse();
});

/*
|--------------------------------------------------------------------------
| FASE 1: PROVENANCE — import master karyawan juga wajib mencatat batch
| (nama file asli, sha256, operator, jumlah baris).
|--------------------------------------------------------------------------
*/
it('records provenance batch for master karyawan import', function () {
    Storage::fake('local');
    $user = makeEmployeeImportHr();
    $division = Division::create(['nama' => 'Divisi Provenance', 'lini' => 'SEVP']);

    $csv = implode("\n", [
        'NIPEG,Nama,Jenis Kelamin,Jabatan,No HP,Alamat,Divisi',
        '90020001,Karyawan Provenance,L,Staff,081200000001,Bandung,Divisi Provenance',
    ]);
    $file = UploadedFile::fake()->createWithContent('master-karyawan.csv', $csv);

    $this->actingAs($user)
        ->post(route('employees.import.preview'), ['file' => $file])
        ->assertOk();

    $this->actingAs($user)
        ->post(route('employees.import.commit'))
        ->assertRedirect(route('employees.import'))
        ->assertSessionHas('import_result.batch');

    $batch = \App\Models\ImportBatch::latest('id')->first();
    expect($batch)->not->toBeNull();
    expect($batch->jenis)->toBe('master_karyawan');
    expect($batch->nama_file)->toBe('master-karyawan.csv');
    expect($batch->file_hash)->toHaveLength(64);
    expect($batch->user_id)->toBe($user->id);
    expect($batch->total_baris)->toBe(1);
    expect($batch->berhasil)->toBe(1);
    expect($batch->gagal)->toBe(0);
    expect($batch->duplikat)->toBeFalse();
});

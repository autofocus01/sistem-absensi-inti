<?php

use App\Models\ImportBatch;
use App\Models\OvertimeSubmission;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

/**
 * Bangun fixture XLSX dua sheet meniru struktur file SPKL perusahaan:
 * HC_SPKL (induk) + HC_SPKL_PERSON (rincian NIPEG + durasi LAMA).
 */
function makeSpklFile(array $spklRows, array $personRows): UploadedFile
{
    $spreadsheet = new Spreadsheet();

    $spklSheet = $spreadsheet->getActiveSheet();
    $spklSheet->setTitle('HC_SPKL');
    $spklSheet->fromArray(array_merge(
        [[
            'NOSPKL', 'KETERANGAN', 'TGLMULAI', 'TGLSELESAI', 'KDKRJ_DIV',
            'NIPEG_AJUKAN', 'TGLPENGAJUAN', 'DITERIMAOLEH', 'TGLTERIMASDM',
            'DITERIMAVP', 'TGLTERIMAVP',
        ]],
        $spklRows,
    ));

    $personSheet = $spreadsheet->createSheet();
    $personSheet->setTitle('HC_SPKL_PERSON');
    $personSheet->fromArray(array_merge(
        [[
            'NOSPKL', 'NIPEG', 'PIN', 'KDKERJ', 'IDJOB', 'PANGKAT',
            'LEMBURLIBUR', 'LEMBURKERJA', 'HITUNGGAJI', 'HITUNGOLEH',
            'XUSER', 'XUPDATE', 'LAMA', 'KODEBAND',
        ]],
        $personRows,
    ));

    $path = tempnam(sys_get_temp_dir(), 'spkl') . '.xlsx';
    (new Xlsx($spreadsheet))->save($path);
    $spreadsheet->disconnectWorksheets();

    return new UploadedFile(
        $path,
        'spkl.xlsx',
        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        null,
        true,
    );
}

function spklHeadRow(string $code = '3401/010100/2026', string $date = '2026-08-04'): array
{
    return [
        $code,
        'DUKUNGAN PEMELIHARAAN SISTEM',
        $date . ' 00:00:00',
        $date . ' 00:00:00',
        '010100',
        '90010001',
        '2026-08-03 10:05:00',
        'ADMIN_HC',
        '2026-08-05 09:10:00',
        '90010050',
        '2026-08-03 13:10:00',
    ];
}

function spklPersonRow(string $nipeg, string $code = '3401/010100/2026', int $lama = 67, string $band = '0002'): array
{
    return [
        $code,
        $nipeg,
        '35' . substr($nipeg, -2),
        '010100',
        '8001' . substr($nipeg, -4),
        'STAF',
        '',
        '',
        '',
        '',
        '90010001',
        '2026-08-03 10:05:00',
        $lama,
        $band,
    ];
}

it('mengimpor file SPKL menjadi pengajuan lembur lengkap dengan provenance', function () {
    Storage::fake('local');
    $this->seed();

    $hr = User::query()->where('role', 'hr_admin')->firstOrFail();

    $file = makeSpklFile(
        [spklHeadRow()],
        [spklPersonRow('90010002')],
    );

    $this->actingAs($hr)
        ->post(route('overtime.import.preview'), ['file' => $file])
        ->assertOk()
        ->assertSee('Valid');

    $this->actingAs($hr)
        ->post(route('overtime.import.commit'))
        ->assertRedirect(route('overtime.import'));

    $employee = \App\Models\Employee::where('nipeg', '90010002')->firstOrFail();

    $submission = OvertimeSubmission::query()
        ->where('user_id', $employee->user_id)
        ->whereDate('overtime_date', '2026-08-04')
        ->first();

    expect($submission)->not->toBeNull()
        ->and($submission->status)->toBe('VERIFIED_HR')
        ->and($submission->source_ref)->toBe('3401/010100/2026')
        ->and($submission->recognized_minutes)->toBeGreaterThan(0)
        ->and($submission->eligibility_status)->toBe('ELIGIBLE');

    // Snapshot kebijakan + audit trail sama dengan jalur produksi.
    expect(DB::table('overtime_policy_snapshots')
        ->where('overtime_submission_id', $submission->id)->count())->toBe(1)
        ->and(\App\Models\AuditLog::query()
            ->where('subject_type', $submission->getMorphClass())
            ->where('subject_id', $submission->id)->count())->toBeGreaterThanOrEqual(3);

    // Provenance batch tampil di Riwayat Impor.
    $batch = ImportBatch::query()->where('jenis', ImportBatch::JENIS_LEMBUR)->first();
    expect($batch)->not->toBeNull()
        ->and($batch->nama_file)->toBe('spkl.xlsx')
        ->and((int) $batch->berhasil)->toBe(1);

    $this->actingAs($hr)
        ->get(route('overtime.import'))
        ->assertOk()
        ->assertSee('spkl.xlsx');
});

it('menolak baris dengan NIP tidak dikenal tanpa memblokir baris valid lain', function () {
    Storage::fake('local');
    $this->seed();

    $hr = User::query()->where('role', 'hr_admin')->firstOrFail();

    $file = makeSpklFile(
        [spklHeadRow()],
        [
            spklPersonRow('99999999'),
            spklPersonRow('90010003', lama: 90),
        ],
    );

    $this->actingAs($hr)
        ->post(route('overtime.import.preview'), ['file' => $file])
        ->assertOk()
        ->assertSee('NIP tidak ditemukan di master karyawan');

    $this->actingAs($hr)
        ->post(route('overtime.import.commit'))
        ->assertRedirect(route('overtime.import'));

    expect(OvertimeSubmission::count())->toBe(1)
        ->and(ImportBatch::query()->where('jenis', ImportBatch::JENIS_LEMBUR)->first()->gagal)->toBe(1);
});

it('melewati pengajuan duplikat ketika file SPKL yang sama diimpor ulang', function () {
    Storage::fake('local');
    $this->seed();

    $hr = User::query()->where('role', 'hr_admin')->firstOrFail();

    $import = function () use ($hr) {
        $file = makeSpklFile([spklHeadRow()], [spklPersonRow('90010002')]);

        $this->actingAs($hr)
            ->post(route('overtime.import.preview'), ['file' => $file])
            ->assertOk();

        return $this->actingAs($hr)
            ->post(route('overtime.import.commit'))
            ->assertRedirect(route('overtime.import'));
    };

    $import();
    expect(OvertimeSubmission::count())->toBe(1);

    $import();
    expect(OvertimeSubmission::count())->toBe(1)
        ->and(session('import_result.skipped'))->toBe(1);

    // Batch kedua menandai file sebagai terulang (provenance jujur).
    expect(
        ImportBatch::query()->where('jenis', ImportBatch::JENIS_LEMBUR)->latest('id')->first()->duplikat
    )->toBeTrue();
});

it('menolak karyawan non-eligible dan tanggal di luar kebijakan lembur', function () {
    Storage::fake('local');
    $this->seed();

    $hr = User::query()->where('role', 'hr_admin')->firstOrFail();

    $file = makeSpklFile(
        [
            spklHeadRow('3401/010100/2026', '2026-08-04'),
            spklHeadRow('3402/010100/2026', '2025-06-15'),
        ],
        [
            // 90010021 = band 4 (pembanding non-eligible).
            spklPersonRow('90010021', '3401/010100/2026', 67, '0004'),
            spklPersonRow('90010004', '3402/010100/2026', 60),
        ],
    );

    $this->actingAs($hr)
        ->post(route('overtime.import.preview'), ['file' => $file])
        ->assertOk()
        ->assertSee('tidak eligible mengajukan lembur')
        ->assertSee('Kebijakan lembur belum berlaku');

    $this->actingAs($hr)
        ->post(route('overtime.import.commit'))
        ->assertRedirect(route('overtime.import'));

    expect(OvertimeSubmission::count())->toBe(0);
});

it('menolak akses halaman import lembur untuk peran selain HR', function () {
    Storage::fake('local');
    $this->seed();

    $karyawan = User::query()->where('role', 'karyawan')->firstOrFail();

    $this->actingAs($karyawan)->get(route('overtime.import'))->assertForbidden();
    $this->actingAs($karyawan)->post(route('overtime.import.preview'))->assertForbidden();
    $this->actingAs($karyawan)->post(route('overtime.import.commit'))->assertForbidden();
});

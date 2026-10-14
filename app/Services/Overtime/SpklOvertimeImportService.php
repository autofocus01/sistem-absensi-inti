<?php

namespace App\Services\Overtime;

use App\Models\AttendanceLog;
use App\Models\AuditLog;
use App\Models\Employee;
use App\Models\ImportBatch;
use App\Models\OvertimePolicySnapshot;
use App\Models\OvertimeSubmission;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\IOFactory;
use RuntimeException;

/**
 * Import pengajuan lembur dari file SPKL (Surat Perintah Kerja Lembur).
 *
 * File sumber punya dua sheet yang saling terkait:
 * - HC_SPKL: induk SPKL (nomor, keterangan, tanggal, status approve).
 * - HC_SPKL_PERSON: rincian per karyawan (NIPEG + durasi LAMA dalam menit).
 *
 * Setiap baris PERSON menjadi satu pengajuan lembur. Durasi dihitung lewat
 * OvertimePolicyResolver + OvertimeCalculationService — pipeline yang sama
 * dengan jalur produksi — lengkap dengan snapshot kebijakan dan audit trail.
 */
class SpklOvertimeImportService
{
    private const MAX_ROWS = 5000;

    private const SHEET_SPKL = 'HC_SPKL';

    private const SHEET_PERSON = 'HC_SPKL_PERSON';

    /**
     * File SPKL hanya memuat durasi (LAMA, menit), bukan jam mulai/selesai.
     * Jam kerja berakhir pukul 17:00, jadi lembur diasumsikan mulai tepat
     * setelah jam kerja dan berdurasi sesuai LAMA.
     */
    private const DEFAULT_START = '17:00:00';

    public function __construct(
        private readonly OvertimePolicyResolver $policyResolver,
        private readonly OvertimeCalculationService $calculator,
        private readonly OvertimeEligibilityService $eligibility,
    ) {}

    public function store(UploadedFile $file): string
    {
        $extension = strtolower($file->getClientOriginalExtension());

        if (! in_array($extension, ['xlsx', 'xls'], true)) {
            throw new RuntimeException(
                'Format file harus XLSX atau XLS (file SPKL dua sheet).'
            );
        }

        return $file->storeAs(
            'overtime-imports',
            Str::uuid() . '.' . $extension
        );
    }

    public function preview(string $path): array
    {
        [$spkl, $personRows, $fatal] = $this->readWorkbook($path);

        if ($fatal !== null) {
            return [
                'format' => null,
                'total_rows' => 0,
                'valid' => 0,
                'errors' => [['row' => 1, 'reason' => $fatal]],
                'duplikat' => [],
            ];
        }

        $result = [
            'format' => 'spkl',
            'total_rows' => count($personRows),
            'valid' => 0,
            'errors' => [],
            'duplikat' => [],
        ];

        foreach ($personRows as $index => $row) {
            $line = $index + 1;

            try {
                $this->normalizeRow($spkl, $row);
                $result['valid']++;
            } catch (\Throwable $e) {
                $result['errors'][] = [
                    'row' => $line,
                    'reason' => $e->getMessage(),
                    'data' => $row['raw'] ?? [],
                ];
            }
        }

        return $result;
    }

    /**
     * @param  array{file_name?: string, file_hash?: string}|null  $meta
     */
    public function commit(string $path, int $userId, ?array $meta = null): array
    {
        [$spkl, $personRows, $fatal] = $this->readWorkbook($path);

        if ($fatal !== null) {
            throw new RuntimeException($fatal);
        }

        $success = 0;
        $skipped = 0;
        $errors = [];
        $syncedPeriods = [];
        $vpUser = User::query()->where('role', 'vp')->with('employee')->get();
        $hrUser = User::query()->where('role', 'hr_admin')->first();

        if (! $hrUser) {
            throw new RuntimeException('Akun HR tidak ditemukan; import lembur dibatalkan.');
        }

        DB::transaction(function () use ($spkl, $personRows, $userId, $vpUser, $hrUser, &$success, &$skipped, &$errors, &$syncedPeriods): void {
            foreach ($personRows as $index => $row) {
                $line = $index + 1;

                try {
                    $data = $this->normalizeRow($spkl, $row);

                    // Deduplikasi: file yang sama diimpor ulang tidak boleh
                    // menggandakan pengajuan. Baris dilewati dengan peringatan.
                    $exists = OvertimeSubmission::query()
                        ->where('user_id', $data['user']->id)
                        ->whereDate('overtime_date', $data['tanggal'])
                        ->where('source_ref', $data['nospkl'])
                        ->exists();

                    if ($exists) {
                        $skipped++;
                        continue;
                    }

                    $this->createSubmission($data, $userId, $vpUser, $hrUser, $syncedPeriods);
                    $success++;
                } catch (\Throwable $e) {
                    $errors[] = ['row' => $line, 'reason' => $e->getMessage()];
                }
            }
        });

        $periode = array_keys($syncedPeriods);
        sort($periode);

        $batch = $this->recordBatch($meta, $userId, count($personRows), $success, count($errors), $periode);

        return [
            'success' => $success,
            'skipped' => $skipped,
            'errors' => $errors,
            'batch' => $batch,
            'peringatan' => $batch['duplikat']
                ? 'File ini pernah diimpor sebelumnya. Pengajuan dengan nomor SPKL sama tidak digandakan (lihat Riwayat Impor).'
                : ($skipped > 0
                    ? "{$skipped} baris dilewati karena pengajuan dengan nomor SPKL dan tanggal sama sudah ada di sistem."
                    : null),
        ];
    }

    public function forget(string $path): void
    {
        if (Storage::exists($path)) {
            Storage::delete($path);
        }
    }

    /**
     * Baca kedua sheet dan gabungkan induk SPKL ke setiap baris person.
     *
     * @return array{0: array<string, array>, 1: list<array>, 2: ?string}
     */
    private function readWorkbook(string $path): array
    {
        if (! Storage::exists($path)) {
            return [[], [], 'File import tidak ditemukan atau sudah kedaluwarsa.'];
        }

        try {
            $reader = IOFactory::createReaderForFile(Storage::path($path));
            $reader->setReadDataOnly(true);
            $workbook = $reader->load(Storage::path($path));
        } catch (\Throwable) {
            return [[], [], 'File tidak dapat dibaca sebagai Excel.'];
        }

        $names = $workbook->getSheetNames();

        if (! in_array(self::SHEET_SPKL, $names, true) || ! in_array(self::SHEET_PERSON, $names, true)) {
            return [[], [], 'Sheet HC_SPKL atau HC_SPKL_PERSON tidak ditemukan.'];
        }

        $spklSheet = $workbook->getSheetByName(self::SHEET_SPKL);
        $personSheet = $workbook->getSheetByName(self::SHEET_PERSON);

        $spkl = [];

        foreach ($this->sheetRecords($spklSheet) as $record) {
            $code = trim((string) ($record['nospkl'] ?? ''));

            if ($code === '' || isset($spkl[$code])) {
                continue;
            }

            $spkl[$code] = $record;
        }

        $personRows = [];

        foreach ($this->sheetRecords($personSheet) as $index => $record) {
            if ($index >= self::MAX_ROWS) {
                break;
            }

            $record['raw'] = $record;

            if (trim((string) ($record['nipeg'] ?? '')) === '' && trim((string) ($record['nospkl'] ?? '')) === '') {
                continue; // Baris kosong di akhir sheet.
            }

            $personRows[] = $record;
        }

        $workbook->disconnectWorksheets();

        return [$spkl, $personRows, null];
    }

    /**
     * @return list<list<mixed>>
     */
    private function sheetRows(\PhpOffice\PhpSpreadsheet\Worksheet\Worksheet $sheet): array
    {
        $highestRow = $sheet->getHighestRow();
        $highestColumn = Coordinate::columnIndexFromString($sheet->getHighestColumn());
        $rows = [];

        for ($r = 1; $r <= $highestRow; $r++) {
            $values = [];

            for ($c = 1; $c <= min($highestColumn, 60); $c++) {
                $values[] = $sheet->getCell(Coordinate::stringFromColumnIndex($c) . $r)->getValue();
            }

            $rows[] = $values;
        }

        return $rows;
    }

    /**
     * Ubah sheet menjadi daftar baris assoc: baris pertama = header,
     * sisanya data. Header dinormalisasi (huruf kecil, spasi tunggal).
     *
     * @return list<array<string, mixed>>
     */
    private function sheetRecords(\PhpOffice\PhpSpreadsheet\Worksheet\Worksheet $sheet): array
    {
        $rows = $this->sheetRows($sheet);
        $headerRow = array_shift($rows) ?? [];

        $headers = array_values(array_filter(
            array_map(
                fn ($header) => $this->normalizeHeader((string) $header),
                $headerRow
            ),
            fn (string $header) => $header !== ''
        ));

        $records = [];

        foreach ($rows as $row) {
            $record = [];

            foreach ($headers as $index => $header) {
                $record[$header] = $row[$index] ?? null;
            }

            $records[] = $record;
        }

        return $records;
    }

    private function normalizeHeader(string $header): string
    {
        $header = str_replace(["\xEF\xBB\xBF", "\xC2\xA0"], ' ', $header);
        $header = strtolower(trim($header));
        $header = preg_replace('/[\s_]+/', ' ', $header) ?? '';

        return $header;
    }

    /**
     * Validasi dan gabungkan baris PERSON dengan induk SPKL.
     *
     * @param  array<string, array>  $spkl
     * @return array{
     *     nospkl: string, nipeg: string, lama: int, tanggal: string,
     *     keterangan: string, employee: Employee, user: User,
     *     status: string, vp_at: ?string, hr_at: ?string
     * }
     */
    private function normalizeRow(array $spkl, array $row): array
    {
        $nospkl = trim((string) ($row['nospkl'] ?? ''));

        if ($nospkl === '') {
            throw new RuntimeException('Nomor SPKL kosong.');
        }

        $head = $spkl[$nospkl] ?? null;

        if ($head === null) {
            throw new RuntimeException("Nomor SPKL {$nospkl} tidak ada di sheet HC_SPKL.");
        }

        $nipeg = $this->normalizeNip((string) ($row['nipeg'] ?? ''));

        if ($nipeg === '') {
            throw new RuntimeException('NIPEG kosong.');
        }

        $employee = Employee::query()->where('nipeg', $nipeg)->first();

        if (! $employee) {
            throw new RuntimeException('NIP tidak ditemukan di master karyawan.');
        }

        $user = $employee->user_id
            ? User::query()->find($employee->user_id)
            : null;

        if (! $user) {
            throw new RuntimeException("Akun karyawan untuk NIP {$nipeg} belum tersedia.");
        }

        if (! $this->eligibility->canSubmit($employee)) {
            throw new RuntimeException("Karyawan {$nipeg} tidak eligible mengajukan lembur (band di luar 2/3).");
        }

        $lama = (int) ($row['lama'] ?? 0);

        if ($lama <= 0) {
            throw new RuntimeException('Durasi LAMA tidak valid.');
        }

        try {
            $tanggal = Carbon::parse((string) ($head['tglmulai'] ?? ''))->toDateString();
        } catch (\Throwable) {
            throw new RuntimeException('Tanggal mulai SPKL tidak dikenali.');
        }

        try {
            $policy = $this->policyResolver->resolve(Carbon::parse($tanggal));
        } catch (\RuntimeException $e) {
            throw new RuntimeException(
                'Kebijakan lembur belum berlaku pada tanggal ' . $tanggal . '.'
            );
        }

        $status = filled((string) ($head['tglterimasdm'] ?? ''))
            ? 'VERIFIED_HR'
            : (filled((string) ($head['tglterimavp'] ?? '')) ? 'APPROVED_VP' : 'PENDING_VP');

        return [
            'nospkl' => $nospkl,
            'nipeg' => $nipeg,
            'lama' => $lama,
            'tanggal' => $tanggal,
            'keterangan' => trim((string) ($head['keterangan'] ?? 'Lembur SPKL')),
            'employee' => $employee,
            'user' => $user,
            'status' => $status,
            'vp_at' => $this->parseTimestamp($head['tglterimavp'] ?? null),
            'hr_at' => $this->parseTimestamp($head['tglterimasdm'] ?? null),
            'policy' => $policy,
        ];
    }

    /**
     * Buat satu pengajuan lembur lengkap dengan snapshot kebijakan
     * dan audit trail — meniru field demi field jalur produksi.
     *
     * @param  array<string, list<User>>  $vpUser
     * @param  array<string, bool>  $syncedPeriods
     */
    private function createSubmission(array $data, int $userId, $vpUser, User $hrUser, array &$syncedPeriods): void
    {
        $employee = $data['employee'];
        $policy = $data['policy'];

        $actualStart = Carbon::parse($data['tanggal'] . ' ' . self::DEFAULT_START);
        $actualEnd = $actualStart->copy()->addMinutes($data['lama']);

        $calc = $this->calculator->calculate(
            Carbon::parse($data['tanggal']),
            $actualStart,
            $actualEnd,
            $policy
        );

        if (($calc['eligibility'] ?? null) !== 'ELIGIBLE' || (int) $calc['recognized_minutes'] <= 0) {
            throw new RuntimeException('Durasi lembur di bawah minimum kebijakan.');
        }

        $vpApprover = $vpUser
            ->first(fn (User $candidate) => (int) $candidate->employee?->division_id === (int) $employee->division_id)
            ?? $vpUser->first();

        if (! $vpApprover) {
            throw new RuntimeException('Akun VP tidak ditemukan.');
        }

        $submitAt = $data['vp_at']
            ? Carbon::parse($data['vp_at'])->subDay()->setTime(10, 5)
            : $actualStart->copy()->subDay()->setTime(10, 5);

        $vpAt = $data['vp_at'] ? Carbon::parse($data['vp_at']) : null;
        $hrAt = $data['hr_at'] ? Carbon::parse($data['hr_at']) : null;

        $status = $data['status'];

        $overtime = new OvertimeSubmission();
        $overtime->forceFill([
            'user_id' => $data['user']->id,
            'division_id' => $employee->division_id,
            'overtime_date' => $data['tanggal'],
            'start_time' => $actualStart->format('H:i:s'),
            'end_time' => $actualEnd->format('H:i:s'),
            'estimated_hours' => round($data['lama'] / 60, 2),
            'category' => match ($calc['day_type']) {
                'NATIONAL_HOLIDAY' => 'hari_libur_nasional',
                'WEEKEND' => 'akhir_pekan',
                default => 'hari_kerja',
            },
            'reason' => $data['keterangan'],
            'source_ref' => $data['nospkl'],
            'employee_consent' => true,
            'employee_consent_at' => $submitAt,
            'employee_consent_ip' => '127.0.0.1',
            'employee_consent_user_agent' => 'SpklOvertimeImportService (impor file HR)',
            'employee_consent_source' => 'IMPORT_XLSX',
            'employee_consent_version' => '1.0',
            'employee_consent_text' => 'Pengajuan lembur disetujui melalui SPKL resmi yang diimpor oleh HR.',
            'submission_mode' => 'PRE_SUBMITTED',
            'attendance_log_id' => $this->attendanceLogId($employee->id, $data['tanggal']),
            'actual_start_time' => $actualStart->format('H:i:s'),
            'actual_end_time' => $actualEnd->format('H:i:s'),
            'actual_minutes' => $calc['actual_minutes'],
            'recognized_minutes' => $calc['recognized_minutes'],
            'rounded_minutes' => $calc['rounded_minutes'],
            'excess_minutes' => $calc['excess_minutes'],
            'quota_reserved_minutes' => $calc['recognized_minutes'],
            'anomaly_codes' => $calc['excess_minutes'] > 0 ? ['EXCESS_OVERTIME'] : null,
            'eligibility_status' => $calc['eligibility'],
            'status' => $status,
            'vp_approver_id' => in_array($status, ['APPROVED_VP', 'VERIFIED_HR'], true) ? $vpApprover->id : null,
            'vp_approved_at' => $vpAt,
            'vp_notes' => $vpAt ? 'Disetujui melalui SPKL ' . $data['nospkl'] : null,
            'hr_verifier_id' => $status === 'VERIFIED_HR' ? $hrUser->id : null,
            'hr_verified_at' => $hrAt,
            'hr_notes' => $hrAt ? 'Diverifikasi melalui impor SPKL ' . $data['nospkl'] : null,
            'created_at' => $submitAt,
            'updated_at' => $hrAt ?? $vpAt ?? $submitAt,
        ])->save();

        OvertimePolicySnapshot::query()->create([
            'overtime_submission_id' => $overtime->id,
            'legal_rule_id' => $policy['legal_rule_id'],
            'company_rule_id' => $policy['company_rule_id'],
            'policy' => $policy,
        ]);

        $this->recordAudit(
            'OT_SUBMITTED',
            $overtime,
            null,
            ['status' => 'PENDING_VP'],
            'Pengajuan lembur dari impor SPKL ' . $data['nospkl'] . '. User ID: ' . $userId,
            $data['user'],
            $submitAt,
        );

        if ($vpAt) {
            $this->recordAudit(
                'VP_APPROVED',
                $overtime,
                ['status' => 'PENDING_VP'],
                ['status' => 'APPROVED_VP'],
                'Persetujuan VP tercatat pada SPKL ' . $data['nospkl'],
                $vpApprover,
                $vpAt,
            );
        }

        if ($hrAt && $status === 'VERIFIED_HR') {
            $this->recordAudit(
                'HR_VERIFIED',
                $overtime,
                ['status' => 'APPROVED_VP'],
                ['status' => 'VERIFIED_HR'],
                'Verifikasi HR tercatat pada SPKL ' . $data['nospkl'],
                $hrUser,
                $hrAt,
            );
        }

        $syncedPeriods[substr($data['tanggal'], 0, 7)] = true;
    }

    private function attendanceLogId(int $employeeId, string $tanggal): ?int
    {
        return AttendanceLog::query()
            ->where('employee_id', $employeeId)
            ->whereDate('tanggal', $tanggal)
            ->value('id');
    }

    private function recordAudit(
        string $action,
        OvertimeSubmission $overtime,
        ?array $before,
        ?array $after,
        ?string $notes,
        User $actor,
        Carbon $at,
    ): void {
        (new AuditLog)->forceFill([
            'user_id' => $actor->id,
            'action' => $action,
            'subject_type' => $overtime->getMorphClass(),
            'subject_id' => (string) $overtime->getKey(),
            'before_data' => $before,
            'after_data' => $after,
            'notes' => $notes,
            'request_id' => (string) Str::uuid(),
            'created_at' => $at,
            'updated_at' => $at,
        ])->save();
    }

    /**
     * @param  list<string>  $periode
     * @return array{nama_file: string, waktu: string, duplikat: bool, berhasil: int, gagal: int, dilewati: int}
     */
    private function recordBatch(
        ?array $meta,
        int $userId,
        int $totalBaris,
        int $berhasil,
        int $gagal,
        array $periode,
    ): array {
        $meta ??= [];
        $namaFile = (string) ($meta['file_name'] ?? 'file_import');
        $fileHash = (string) ($meta['file_hash'] ?? '');

        $duplikat = $fileHash !== ''
            && ImportBatch::query()
                ->where('jenis', ImportBatch::JENIS_LEMBUR)
                ->where('file_hash', $fileHash)
                ->exists();

        $batch = ImportBatch::create([
            'user_id' => $userId,
            'jenis' => ImportBatch::JENIS_LEMBUR,
            'nama_file' => $namaFile,
            'file_hash' => $fileHash,
            'format' => 'spkl',
            'total_baris' => $totalBaris,
            'berhasil' => $berhasil,
            'gagal' => $gagal,
            'periode' => $periode,
            'duplikat' => $duplikat,
        ]);

        return [
            'nama_file' => $batch->nama_file,
            'waktu' => $batch->created_at->format('d M Y H:i'),
            'duplikat' => $duplikat,
            'berhasil' => $berhasil,
            'gagal' => $gagal,
        ];
    }

    private function parseTimestamp(mixed $raw): ?Carbon
    {
        if ($raw === null || trim((string) $raw) === '') {
            return null;
        }

        try {
            return Carbon::parse((string) $raw);
        } catch (\Throwable) {
            return null;
        }
    }

    private function normalizeNip(string $nip): string
    {
        return trim(str_replace(['.', ' '], '', $nip));
    }
}

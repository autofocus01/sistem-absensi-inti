<?php

namespace App\Services;

use App\Models\Division;
use App\Models\Employee;
use App\Services\Audit\AuditLogService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use PhpOffice\PhpSpreadsheet\IOFactory;
use RuntimeException;

class EmployeeImportService
{
    public function __construct(private readonly AuditLogService $audit) {}

    public function store(UploadedFile $file): string
    {
        $extension = strtolower($file->getClientOriginalExtension());
        if (! in_array($extension, ['xlsx', 'xls', 'csv'], true)) {
            throw new RuntimeException('Format file harus XLSX, XLS, atau CSV.');
        }

        return $file->storeAs('employee-imports', Str::uuid().'.'.$extension);
    }

    public function preview(string $path): array
    {
        [$rows, $headers] = $this->rows($path);
        $errors = [];
        $valid = 0;
        $seen = [];
        $previewRows = [];

        foreach ($rows as $offset => $row) {
            $line = $offset + 2;
            try {
                $data = $this->normalizeRow($headers, $row);
                $this->validateRow($data, $seen);

                $division = $this->findDivision($data['divisi']);
                if (! $division) {
                    throw new RuntimeException("Divisi '{$data['divisi']}' tidak ditemukan di master divisi.");
                }

                $seen[$data['nipeg']] = true;
                $existing = Employee::query()->where('nipeg', $data['nipeg'])->first();

                $previewRows[] = [
                    'row' => $line,
                    'nipeg' => $data['nipeg'],
                    'nama' => $data['nama'],
                    'jenis_kelamin' => $data['jenis_kelamin'],
                    'jabatan' => $data['jabatan'],
                    'no_hp' => $data['no_hp'],
                    'alamat' => $data['alamat'],
                    'divisi' => $division->nama,
                    'action' => $existing ? 'UPDATE' : 'CREATE',
                ];

                $valid++;
            } catch (\Throwable $e) {
                $errors[] = [
                    'row' => $line,
                    'reason' => $e->getMessage(),
                    'data' => $row,
                ];
            }
        }

        return [
            'headers' => $headers,
            'total_rows' => count($rows),
            'valid' => $valid,
            'errors' => $errors,
            'rows' => $previewRows,
        ];
    }

    public function commit(string $path, int $userId): array
    {
        [$rows, $headers] = $this->rows($path);
        $success = 0;
        $updated = 0;
        $created = 0;
        $errors = [];
        $seen = [];

        DB::transaction(function () use ($rows, $headers, $userId, &$success, &$updated, &$created, &$errors, &$seen): void {
            foreach ($rows as $offset => $row) {
                $line = $offset + 2;
                try {
                    $data = $this->normalizeRow($headers, $row);
                    $this->validateRow($data, $seen);
                    $seen[$data['nipeg']] = true;

                    $division = $this->findDivision($data['divisi']);
                    if (! $division) {
                        throw new RuntimeException("Divisi '{$data['divisi']}' tidak ditemukan di master divisi.");
                    }

                    $employee = Employee::query()->where('nipeg', $data['nipeg'])->lockForUpdate()->first();
                    $before = $employee?->toArray();
                    $isNew = ! $employee;
                    $employee ??= new Employee();

                    $payload = [
                        'nipeg' => $data['nipeg'],
                        'nama' => $data['nama'],
                        'jenis_kelamin' => $data['jenis_kelamin'],
                        'jabatan' => $data['jabatan'],
                        'no_hp' => $data['no_hp'],
                        'alamat' => $data['alamat'],
                        'division_id' => $division->id,
                    ];

                    $employee->forceFill($payload)->save();
                    $after = $employee->fresh()->toArray();

                    $this->audit->record(
                        $isNew ? 'EMPLOYEE_IMPORTED_CREATED' : 'EMPLOYEE_IMPORTED_UPDATED',
                        $employee,
                        $before,
                        $after,
                        'Import master karyawan oleh HR. User ID: '.$userId.', baris: '.$line,
                        request()
                    );

                    $success++;
                    $isNew ? $created++ : $updated++;
                } catch (\Throwable $e) {
                    $errors[] = ['row' => $line, 'reason' => $e->getMessage(), 'data' => $row];
                }
            }

            $this->audit->record(
                'EMPLOYEE_IMPORT_COMMITTED',
                null,
                null,
                ['success' => $success, 'created' => $created, 'updated' => $updated, 'errors' => count($errors)],
                'Import master karyawan selesai.',
                request()
            );
        });

        return compact('success', 'created', 'updated', 'errors');
    }

    public function forget(string $path): void
    {
        if (Storage::exists($path)) {
            Storage::delete($path);
        }
    }

    private function rows(string $path): array
    {
        if (! Storage::exists($path)) {
            throw new RuntimeException('File import tidak ditemukan atau sudah kedaluwarsa.');
        }

        $sheet = IOFactory::load(Storage::path($path))->getActiveSheet();
        $raw = $sheet->toArray(null, true, true, false);
        if (count($raw) < 2) {
            throw new RuntimeException('File kosong atau tidak memiliki data.');
        }

        $headerIndex = null;
        $headers = [];
        foreach (array_slice($raw, 0, 10, true) as $index => $candidate) {
            $normalized = array_map(fn ($v) => $this->normalizeHeader((string) $v), $candidate);
            if (
                $this->find($normalized, ['nipeg', 'nip', 'nomor induk pegawai']) !== null
                && $this->find($normalized, ['nama', 'nama karyawan', 'name']) !== null
                && $this->find($normalized, ['divisi', 'division']) !== null
            ) {
                $headerIndex = $index;
                $headers = $normalized;
                break;
            }
        }

        if ($headerIndex === null) {
            throw new RuntimeException('Header wajib NIPEG/NIP, Nama, dan Divisi tidak ditemukan.');
        }

        return [array_slice($raw, $headerIndex + 1), $headers];
    }

    private function normalizeRow(array $headers, array $row): array
    {
        $value = fn (array $names) => trim((string) ($row[$this->find($headers, $names) ?? -1] ?? ''));
        $nipeg = preg_replace('/\s+/', '', $value(['nipeg', 'nip', 'nomor induk pegawai'])) ?? '';
        if (preg_match('/^\d+\.0+$/', $nipeg)) {
            $nipeg = preg_replace('/\.0+$/', '', $nipeg);
        }

        return [
            'nipeg' => $nipeg,
            'nama' => $value(['nama', 'nama karyawan', 'name']),
            'jenis_kelamin' => strtoupper($value(['jenis kelamin', 'gender', 'jk'])) ?: null,
            'jabatan' => $value(['jabatan', 'position']) ?: null,
            'no_hp' => $value(['no hp', 'no. hp', 'nomor hp', 'phone', 'telepon']) ?: null,
            'alamat' => $value(['alamat', 'address']) ?: null,
            'divisi' => $value(['divisi', 'division']),
        ];
    }

    private function validateRow(array $data, array $seen): void
    {
        if ($data['nipeg'] === '') {
            throw new RuntimeException('NIPEG/NIP kosong.');
        }
        if (mb_strlen($data['nipeg']) > 20) {
            throw new RuntimeException('NIPEG/NIP melebihi 20 karakter.');
        }
        if ($data['nama'] === '') {
            throw new RuntimeException('Nama kosong.');
        }
        if ($data['divisi'] === '') {
            throw new RuntimeException('Divisi kosong.');
        }
        if ($data['jenis_kelamin'] !== null && ! in_array($data['jenis_kelamin'], ['L', 'P'], true)) {
            throw new RuntimeException('Jenis kelamin harus L atau P.');
        }
        if (isset($seen[$data['nipeg']])) {
            // Keep this wording compatible with the existing regression test and UI.
            throw new RuntimeException('NIPEG duplikat di dalam file.');
        }
    }

    private function findDivision(string $name): ?Division
    {
        $normalized = $this->normalizeText($name);
        return Division::query()->get()->first(
            fn (Division $d) => $this->normalizeText($d->nama) === $normalized
        );
    }

    private function find(array $headers, array $names): ?int
    {
        foreach ($names as $name) {
            $i = array_search($this->normalizeHeader($name), $headers, true);
            if ($i !== false) {
                return $i;
            }
        }
        return null;
    }

    private function normalizeHeader(string $value): string
    {
        $value = str_replace(
            ["\xEF\xBB\xBF", "\xC2\xA0", '_', '-', '/'],
            [' ', ' ', ' ', ' ', ' '],
            strtolower(trim($value))
        );
        return trim(preg_replace('/\s+/', ' ', $value) ?? $value);
    }

    private function normalizeText(string $value): string
    {
        return trim(mb_strtolower(preg_replace('/\s+/', ' ', $value) ?? $value));
    }
}

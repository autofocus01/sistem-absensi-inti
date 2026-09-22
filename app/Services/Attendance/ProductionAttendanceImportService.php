<?php

namespace App\Services\Attendance;

use App\Models\AttendanceImportError;
use App\Models\AttendanceLog;
use App\Models\Employee;
use App\Services\Audit\AuditLogService;
use Carbon\Carbon;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;
use RuntimeException;

class ProductionAttendanceImportService
{
    public function __construct(
        private readonly AuditLogService $audit
    ) {}

    public function store(UploadedFile $file): string
    {
        $extension = strtolower($file->getClientOriginalExtension());

        if (! in_array($extension, ['xlsx', 'xls', 'csv'], true)) {
            throw new RuntimeException(
                'Format file harus XLSX, XLS, atau CSV.'
            );
        }

        return $file->storeAs(
            'attendance-imports',
            Str::uuid() . '.' . $extension
        );
    }

    public function preview(string $path): array
    {
        [$rows, $headers] = $this->rows($path);

        $format = $this->detectFormat($headers);

        $result = [
            'format' => $format,
            'headers' => $headers,
            'total_rows' => count($rows),
            'valid' => 0,
            'errors' => [],
        ];

        if ($format === null) {
            $result['errors'][] = [
                'row' => 1,
                'reason' => 'Header tidak memiliki kombinasi kolom tanggal/jam yang didukung.',
            ];

            return $result;
        }

        $seen = [];
        $scanGroups = [];

        foreach ($rows as $offset => $row) {
            $line = $offset + 2;

            try {
                $normalized = $this->normalizeRow($headers, $row, $format);
                $this->validateNormalized($normalized);

                $key = $normalized['nipeg'] . '|' . $normalized['tanggal'];

                if ($format === 'paired') {
                    if (isset($seen[$key])) {
                        throw new RuntimeException('Duplikat NIP dan tanggal di dalam file.');
                    }
                    $seen[$key] = true;
                } else {
                    $scanGroups[$key][] = $normalized;
                }

                $result['valid']++;
            } catch (\Throwable $e) {
                $result['errors'][] = [
                    'row' => $line,
                    'reason' => $e->getMessage(),
                    'data' => $row,
                ];
            }
        }

        if ($format === 'scan') {
            foreach ($scanGroups as $key => $group) {
                $times = array_column($group, 'jam');
                sort($times);
                if (count($times) > 1 && $times[0] === $times[count($times) - 1]) {
                    $result['errors'][] = [
                        'row' => null,
                        'reason' => 'Scan masuk/pulang identik; data hanya akan diperlakukan sebagai satu scan.',
                        'data' => ['key' => $key, 'jumlah_scan' => count($times)],
                    ];
                }
            }
        }

        return $result;
    }

    public function commit(string $path, int $userId): array
    {
        [$rows, $headers] = $this->rows($path);

        $format = $this->detectFormat($headers);

        if ($format === null) {
            throw new RuntimeException(
                'Format header tidak dikenali.'
            );
        }

        $success = 0;
        $errors = [];
        $seen = [];
        $scanGroups = [];

        DB::transaction(function () use ($rows, $headers, $format, $userId, &$success, &$errors, &$seen, &$scanGroups): void {
            foreach ($rows as $offset => $row) {
                $line = $offset + 2;
                try {
                    $data = $this->normalizeRow($headers, $row, $format);
                    $this->validateNormalized($data);
                    $key = $data['nipeg'] . '|' . $data['tanggal'];

                    if ($format === 'scan') {
                        $scanGroups[$key][] = ['data' => $data, 'line' => $line, 'row' => $row];
                        continue;
                    }

                    if (isset($seen[$key])) {
                        throw new RuntimeException('Duplikat baris dalam file untuk NIP dan tanggal yang sama.');
                    }
                    $seen[$key] = true;
                    $this->upsertAttendance($data, $userId, $line, $row, $success, $errors);
                } catch (\Throwable $e) {
                    $errors[] = ['row' => $line, 'reason' => $e->getMessage(), 'data' => $row];
                    $this->saveError($row, $headers, $line, $e->getMessage());
                }
            }

            foreach ($scanGroups as $group) {
                try {
                    $data = $this->aggregateScanGroup(array_column($group, 'data'));
                    $line = $group[0]['line'];
                    $row = $group[0]['row'];
                    $this->upsertAttendance($data, $userId, $line, $row, $success, $errors);
                } catch (\Throwable $e) {
                    $line = $group[0]['line'];
                    $row = $group[0]['row'];
                    $errors[] = ['row' => $line, 'reason' => $e->getMessage(), 'data' => $row];
                    $this->saveError($row, $headers, $line, $e->getMessage());
                }
            }

            if ($success > 0) {
                $this->audit->record(
                    'ATTENDANCE_IMPORT_COMMITTED',
                    null,
                    null,
                    [
                        'success' => $success,
                        'errors' => count($errors),
                    ],
                    'Import absensi production selesai.',
                    request()
                );
            }
        });

        return [
            'success' => $success,
            'errors' => $errors,
        ];
    }

    private function aggregateScanGroup(array $rows): array
    {
        if ($rows === []) {
            throw new RuntimeException('Tidak ada data scan.');
        }

        $times = array_values(array_filter(array_column($rows, 'jam')));
        sort($times);

        return [
            'nipeg' => $rows[0]['nipeg'],
            'tanggal' => $rows[0]['tanggal'],
            'jam_masuk' => $times[0] ?? null,
            'jam_pulang' => count($times) > 1 ? $times[count($times) - 1] : null,
            'jumlah_scan' => count($times),
        ];
    }

    private function upsertAttendance(
        array $data,
        int $userId,
        int $line,
        array $row,
        int &$success,
        array &$errors
    ): void {
        $employee = $this->findEmployeeByNip($data['nipeg']);
        if (! $employee) {
            throw new RuntimeException('NIP tidak ditemukan di master karyawan.');
        }

        $attendance = AttendanceLog::query()
            ->where('employee_id', $employee->id)
            ->whereDate('tanggal', $data['tanggal'])
            ->lockForUpdate()
            ->first();

        $payload = [
            'employee_id' => $employee->id,
            'tanggal' => $data['tanggal'],
            'jam_masuk' => $data['jam_masuk'] ?? null,
            'jam_pulang' => $data['jam_pulang'] ?? null,
            'sumber' => 'import_production',
            'jumlah_scan' => (int) ($data['jumlah_scan'] ?? 0),
        ];

        if ($attendance) {
            $current = [
                'jam_masuk' => $attendance->jam_masuk,
                'jam_pulang' => $attendance->jam_pulang,
                'jumlah_scan' => (int) $attendance->jumlah_scan,
            ];
            $incoming = [
                'jam_masuk' => $payload['jam_masuk'],
                'jam_pulang' => $payload['jam_pulang'],
                'jumlah_scan' => $payload['jumlah_scan'],
            ];

            if ($current !== $incoming && $attendance->sumber !== 'import_production') {
                throw new RuntimeException('Presensi sudah ada dan berasal dari sumber lain. Perubahan ditahan untuk review HR.');
            }
        }

        $before = $attendance?->toArray();
        $attendance ??= new AttendanceLog();
        $attendance->forceFill($payload)->save();

        $this->audit->record(
            'ATTENDANCE_IMPORTED',
            $attendance,
            $before,
            $attendance->fresh()->toArray(),
            'Import absensi production oleh HR. User ID: ' . $userId . ', baris: ' . $line,
            request()
        );

        $success++;
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
            throw new RuntimeException(
                'File import tidak ditemukan atau sudah kedaluwarsa.'
            );
        }

        $spreadsheet = IOFactory::load(
            Storage::path($path)
        );

        $rawRows = $spreadsheet
            ->getActiveSheet()
            ->toArray(null, true, true, false);

        if (count($rawRows) < 2) {
            throw new RuntimeException(
                'File kosong atau tidak memiliki data.'
            );
        }

        /*
         * Beberapa file production mempunyai baris judul/keterangan
         * sebelum header.
         *
         * Kita cari header yang benar pada beberapa baris pertama.
         */
        $headerRowIndex = null;
        $headers = [];

        foreach (
            array_slice(
                $rawRows,
                0,
                min(10, count($rawRows)),
                true
            ) as $index => $candidateRow
        ) {
            $candidateHeaders = array_map(
                fn ($h) => $this->normalizeHeader((string) $h),
                $candidateRow
            );

            $hasNip = $this->find(
                $candidateHeaders,
                [
                    'nip',
                    'nipeg',
                    'pin',
                    'nik',
                    'nip pegawai',
                    'nomor induk pegawai',
                ]
            ) !== null;

            $hasDate = $this->find(
                $candidateHeaders,
                [
                    'tanggal',
                    'date',
                    'tgl',
                    'tgl masuk',
                    'tanggal masuk',
                    'tanggal absensi',
                    'tgl absensi',
                ]
            ) !== null;

            $hasDateTime = $this->find(
                $candidateHeaders,
                [
                    'datetime',
                    'date time',
                    'tanggal waktu',
                    'waktu scan',
                    'scan time',
                    'tanggal dan waktu',
                ]
            ) !== null;

            if ($hasNip && ($hasDate || $hasDateTime)) {
                $headerRowIndex = $index;
                $headers = $candidateHeaders;
                break;
            }
        }

        if ($headerRowIndex === null) {
            /*
             * Tetap gunakan baris pertama supaya halaman Preview
             * memberikan pesan error yang informatif.
             */
            $headers = array_map(
                fn ($h) => $this->normalizeHeader((string) $h),
                $rawRows[0]
            );

            return [
                array_slice($rawRows, 1),
                $headers,
            ];
        }

        return [
            array_slice(
                $rawRows,
                $headerRowIndex + 1
            ),
            $headers,
        ];
    }

    private function detectFormat(array $headers): ?string
    {
        $hasNip = $this->find(
            $headers,
            [
                'nip',
                'nipeg',
                'pin',
                'nik',
                'nip pegawai',
                'nomor induk pegawai',
            ]
        ) !== null;

        /*
         * IMPORTANT:
         * TGL_MASUK setelah normalizeHeader()
         * menjadi "tgl masuk".
         */
        $hasDate = $this->find(
            $headers,
            [
                'tanggal',
                'date',
                'tgl',
                'tgl masuk',
                'tanggal masuk',
                'tanggal absensi',
                'tgl absensi',
            ]
        ) !== null;

        $hasIn = $this->find(
            $headers,
            [
                'jam masuk',
                'jam masuk kerja',
                'clock in',
                'time in',
                'check in',
                'waktu masuk',
            ]
        ) !== null;

        $hasOut = $this->find(
            $headers,
            [
                'jam pulang',
                'jam keluar',
                'clock out',
                'time out',
                'check out',
                'waktu pulang',
            ]
        ) !== null;

        $hasDateTime = $this->find(
            $headers,
            [
                'datetime',
                'date time',
                'tanggal waktu',
                'waktu scan',
                'scan time',
                'tanggal dan waktu',
            ]
        ) !== null;

        $hasSingle = $this->find(
            $headers,
            [
                'jam',
                'waktu',
                'time',
                'jam scan',
                'waktu scan',
            ]
        ) !== null;

        if (! $hasNip) {
            return null;
        }

        if ($hasDate && $hasIn && $hasOut) {
            return 'paired';
        }

        if (
            ($hasDate || $hasDateTime)
            && ($hasSingle || $hasDateTime)
        ) {
            return 'scan';
        }

        return null;
    }

    private function normalizeRow(
        array $headers,
        array $row,
        string $format
    ): array {
        $nipIndex = $this->find(
            $headers,
            [
                'nip',
                'nipeg',
                'pin',
                'nik',
                'nip pegawai',
                'nomor induk pegawai',
            ]
        );

        $nip = trim(
            (string) (
                $row[$nipIndex] ?? ''
            )
        );

        /*
         * FORMAT PAIRED
         *
         * Contoh dummy:
         *
         * NIPEG
         * TGL_MASUK
         * JAM_MASUK
         * JAM_PULANG
         *
         * Setelah normalizeHeader():
         *
         * NIPEG      -> nipeg
         * TGL_MASUK  -> tgl masuk
         * JAM_MASUK  -> jam masuk
         * JAM_PULANG -> jam pulang
         */
        if ($format === 'paired') {
            $dateIndex = $this->find(
                $headers,
                [
                    'tgl masuk',
                    'tanggal',
                    'date',
                    'tgl',
                    'tanggal masuk',
                    'tanggal absensi',
                    'tgl absensi',
                ]
            );

            if ($dateIndex === null) {
                throw new RuntimeException(
                    'Kolom tanggal tidak ditemukan. Kolom yang didukung: TGL_MASUK, tanggal, date, tgl.'
                );
            }

            $inIndex = $this->find(
                $headers,
                [
                    'jam masuk',
                    'jam masuk kerja',
                    'clock in',
                    'time in',
                    'check in',
                    'waktu masuk',
                ]
            );

            if ($inIndex === null) {
                throw new RuntimeException(
                    'Kolom jam masuk tidak ditemukan.'
                );
            }

            $outIndex = $this->find(
                $headers,
                [
                    'jam pulang',
                    'jam keluar',
                    'clock out',
                    'time out',
                    'check out',
                    'waktu pulang',
                ]
            );

            if ($outIndex === null) {
                throw new RuntimeException(
                    'Kolom jam pulang tidak ditemukan.'
                );
            }

            $date = $this->parseDate(
                $row[$dateIndex] ?? null
            );

            $in = $this->parseTime(
                $row[$inIndex] ?? null
            );

            $out = $this->parseTime(
                $row[$outIndex] ?? null
            );

            return [
                'nipeg' => $nip,
                'tanggal' => $date,
                'jam_masuk' => $in,
                'jam_pulang' => $out,
                'jumlah_scan' => 2,
            ];
        }

        /*
         * FORMAT SCAN
         */
        $dateIndex = $this->find(
            $headers,
            [
                'tgl masuk',
                'tanggal',
                'date',
                'tgl',
                'tanggal masuk',
                'tanggal absensi',
                'tgl absensi',
            ]
        );

        $dateTimeIndex = $this->find(
            $headers,
            [
                'datetime',
                'date time',
                'tanggal waktu',
                'waktu scan',
                'scan time',
                'tanggal dan waktu',
            ]
        );

        $timeIndex = $this->find(
            $headers,
            [
                'jam',
                'waktu',
                'time',
                'jam scan',
                'waktu scan',
            ]
        );

        if ($dateTimeIndex !== null) {
            $when = $this->parseDateTime(
                $row[$dateTimeIndex] ?? null
            );
        } else {
            if ($dateIndex === null) {
                throw new RuntimeException(
                    'Kolom tanggal tidak ditemukan.'
                );
            }

            if ($timeIndex === null) {
                throw new RuntimeException(
                    'Kolom waktu tidak ditemukan.'
                );
            }

            $date = $this->parseDate(
                $row[$dateIndex] ?? null
            );

            $time = $this->parseTime(
                $row[$timeIndex] ?? null
            );

            if ($time === null) {
                throw new RuntimeException(
                    'Jam/waktu kosong.'
                );
            }

            $when = Carbon::parse(
                $date . ' ' . $time
            );
        }

        return [
            'nipeg' => $nip,
            'tanggal' => $when->toDateString(),
            'jam' => $when->format('H:i:s'),
        ];
    }

    private function normalizeNip(mixed $value): string
{
    if ($value === null) {
        return '';
    }

    $value = trim((string) $value);

    // Excel kadang membaca NIP numerik sebagai 90010001.0
    if (preg_match('/^\d+\.0+$/', $value)) {
        $value = preg_replace('/\.0+$/', '', $value);
    }

    // Hilangkan whitespace yang mungkin ikut dari Excel.
    $value = preg_replace('/\s+/', '', $value) ?? $value;

    return $value;
}

    private function validateNormalized(array $data): void
{
    $nip = $this->normalizeNip($data['nipeg'] ?? '');

    if ($nip === '') {
        throw new RuntimeException('NIP kosong.');
    }

    if (! isset($data['tanggal']) || $data['tanggal'] === null || $data['tanggal'] === '') {
        throw new RuntimeException('Tanggal tidak valid.');
    }

    if (
        isset($data['jam_masuk'], $data['jam_pulang'])
        && $data['jam_masuk']
        && $data['jam_pulang']
        && $data['jam_pulang'] < $data['jam_masuk']
    ) {
        throw new RuntimeException('Jam pulang lebih awal dari jam masuk.');
    }

    $employee = $this->findEmployeeByNip($nip);

    if (! $employee) {
        throw new RuntimeException('NIP tidak ditemukan di master karyawan.');
    }
}

    private function findEmployeeByNip(string $nip): ?Employee
{
    $normalizedNip = $this->normalizeNip($nip);

    if ($normalizedNip === '') {
        return null;
    }

    // Pencarian utama.
    $employee = Employee::query()
        ->where('nipeg', $normalizedNip)
        ->first();

    if ($employee) {
        return $employee;
    }

    // Fallback untuk database yang menyimpan NIP sebagai
    // string dengan whitespace.
    return Employee::query()
        ->whereRaw(
            "REPLACE(REPLACE(nipeg, ' ', ''), '\\t', '') = ?",
            [$normalizedNip]
        )
        ->first();
}

    private function saveError(
        array $row,
        array $headers,
        int $line,
        string $reason
    ): void {
        $nipIndex = $this->find(
            $headers,
            [
                'nip',
                'nipeg',
                'pin',
                'nik',
                'nip pegawai',
                'nomor induk pegawai',
            ]
        );

        $nip = $nipIndex !== null
            ? trim((string) ($row[$nipIndex] ?? ''))
            : null;

        /*
         * IMPORTANT:
         * TGL_MASUK juga harus dikenali ketika menyimpan
         * error import.
         */
        $rawDateIndex = $this->find(
            $headers,
            [
                'tgl masuk',
                'tanggal',
                'date',
                'tgl',
                'tanggal masuk',
                'tanggal absensi',
                'tgl absensi',
            ]
        );

        $date = null;

        try {
            if ($rawDateIndex !== null) {
                $date = $this->parseDate(
                    $row[$rawDateIndex] ?? null
                );
            }
        } catch (\Throwable) {
            // fallback ke tanggal hari ini
        }

        $date ??= now()->toDateString();

        AttendanceImportError::create([
            'nipeg' => $nip,
            'nama' => null,
            'tahun' => (int) substr($date, 0, 4),
            'bulan' => (int) substr($date, 5, 2),
            'jenis' => 'absensi_harian',
            'baris_excel' => $line,
            'alasan' => $reason,
            'data_mentah' => array_combine(
                $headers,
                array_pad(
                    $row,
                    count($headers),
                    null
                )
            ),
        ]);
    }

    private function find(
        array $headers,
        array $candidates
    ): ?int {
        foreach ($candidates as $candidate) {
            $normalizedCandidate =
                $this->normalizeHeader($candidate);

            $index = array_search(
                $normalizedCandidate,
                $headers,
                true
            );

            if ($index !== false) {
                return $index;
            }
        }

        return null;
    }

    private function normalizeHeader(
        string $header
    ): string {
        /*
         * Bersihkan BOM dan whitespace khusus Excel.
         */
        $header = str_replace(
            [
                "\xEF\xBB\xBF",
                "\xC2\xA0",
            ],
            ' ',
            $header
        );

        $header = strtolower(
            trim($header)
        );

        /*
         * Semua variasi:
         *
         * TGL_MASUK
         * TGL-MASUK
         * TGL/MASUK
         *
         * akan menjadi:
         *
         * tgl masuk
         */
        $header = str_replace(
            [
                '_',
                '-',
                '/',
                '\\',
            ],
            ' ',
            $header
        );

        $header = preg_replace(
            '/\s+/',
            ' ',
            $header
        ) ?? $header;

        return trim($header);
    }

    private function parseDate(
        mixed $raw
    ): string {
        try {
            if (
                $raw === null
                || trim((string) $raw) === ''
            ) {
                throw new RuntimeException(
                    'Tanggal kosong.'
                );
            }

            /*
             * Excel serial date.
             */
            if (is_numeric($raw)) {
                return ExcelDate
                    ::excelToDateTimeObject($raw)
                    ->format('Y-m-d');
            }

            $value = trim((string) $raw);

            /*
             * Format yang umum dari Excel:
             *
             * 2026-08-14
             * 2026-08-14 00:00:00
             * 2026-08-14 00:00:00.000
             * 14/08/2026
             * 14-08-2026
             */
            $formats = [
                'Y-m-d H:i:s.v',
                'Y-m-d H:i:s',
                'Y-m-d H:i',
                'Y-m-d',
                'd/m/Y H:i:s',
                'd/m/Y H:i',
                'd/m/Y',
                'd-m-Y H:i:s',
                'd-m-Y H:i',
                'd-m-Y',
            ];

            foreach ($formats as $format) {
                try {
                    return Carbon::createFromFormat(
                        $format,
                        $value
                    )->format('Y-m-d');
                } catch (\Throwable) {
                    // lanjutkan ke format berikutnya
                }
            }

            /*
             * Fallback Carbon.
             */
            return Carbon::parse(
                $value
            )->format('Y-m-d');
        } catch (\Throwable $e) {
            throw new RuntimeException(
                'Format tanggal tidak dikenali.'
            );
        }
    }

    private function parseTime(
        mixed $raw
    ): ?string {
        try {
            if (
                $raw === null
                || trim((string) $raw) === ''
            ) {
                return null;
            }

            /*
             * Excel time serial.
             */
            if (is_numeric($raw)) {
                return ExcelDate
                    ::excelToDateTimeObject($raw)
                    ->format('H:i:s');
            }

            $value = trim((string) $raw);

            $formats = [
                'H:i:s.v',
                'H:i:s',
                'H:i',
                'g:i:s A',
                'g:i A',
            ];

            foreach ($formats as $format) {
                try {
                    return Carbon::createFromFormat(
                        $format,
                        $value
                    )->format('H:i:s');
                } catch (\Throwable) {
                    // lanjutkan ke format berikutnya
                }
            }

            return Carbon::parse(
                $value
            )->format('H:i:s');
        } catch (\Throwable $e) {
            throw new RuntimeException(
                'Format jam tidak dikenali.'
            );
        }
    }

    private function parseDateTime(
        mixed $raw
    ): Carbon {
        try {
            if (
                $raw === null
                || trim((string) $raw) === ''
            ) {
                throw new RuntimeException(
                    'Datetime kosong.'
                );
            }

            if (is_numeric($raw)) {
                return Carbon::instance(
                    ExcelDate::excelToDateTimeObject($raw)
                );
            }

            return Carbon::parse(
                (string) $raw
            );
        } catch (\Throwable $e) {
            throw new RuntimeException(
                'Format datetime tidak dikenali.'
            );
        }
    }
}
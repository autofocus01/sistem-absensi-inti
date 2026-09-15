<?php

namespace App\Console\Commands;

use App\Models\AttendanceImportError;
use App\Models\AttendanceLog;
use App\Models\Employee;
use Carbon\Carbon;
use Illuminate\Console\Command;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;

class ImportAbsensiHarian extends Command
{
    protected $signature = 'import:absensi-harian {path : Path ke file Excel/CSV log mesin fingerprint/Face ID}';

    protected $description = 'Import log presensi harian (jam masuk/jam pulang) dari export mesin fingerprint/Face ID ke tabel attendance_logs';

    public function handle(): int
    {
        $path = $this->argument('path');

        if (! file_exists($path)) {
            $this->error("File tidak ditemukan: {$path}");
            return self::FAILURE;
        }

        $spreadsheet = IOFactory::load($path);
        $sheet = $spreadsheet->getActiveSheet();
        $rows = $sheet->toArray(null, true, true, false);

        if (empty($rows)) {
            $this->error('File kosong.');
            return self::FAILURE;
        }

        $header = array_map(fn ($h) => str_replace('_', ' ', strtolower(trim((string) $h))), $rows[0]);
        $dataRows = array_slice($rows, 1);

        $idxNip = $this->cariKolom($header, ['nip', 'nipeg', 'pin', 'nik']);
        $idxTanggal = $this->cariKolom($header, ['tanggal', 'date', 'tgl masuk', 'tgl', 'tanggal masuk']);
        $idxJamMasuk = $this->cariKolom($header, ['jam masuk', 'clock in', 'time in', 'check in']);
        $idxJamPulang = $this->cariKolom($header, ['jam pulang', 'clock out', 'time out', 'check out']);
        $idxJamTunggal = $this->cariKolom($header, ['jam', 'waktu', 'time']);
        $idxDateTime = $this->cariKolom($header, ['datetime', 'tanggal waktu', 'waktu scan', 'scan time']);

        if ($idxNip === null) {
            $this->error('Kolom NIP/PIN tidak ditemukan di header file.');
            return self::FAILURE;
        }

        $formatBerpasangan = $idxJamMasuk !== null && $idxJamPulang !== null && $idxTanggal !== null;

        $sukses = 0;
        $ditolak = [];

        if ($formatBerpasangan) {
            foreach ($dataRows as $i => $row) {
                $nip = trim((string) ($row[$idxNip] ?? ''));
                if (blank($nip)) {
                    continue;
                }

                try {
                    $tanggal = $this->parseTanggal($row[$idxTanggal] ?? null);
                } catch (\Throwable) {
                    $ditolak[] = $this->catatError($nip, $i + 2, 'Format tanggal tidak dikenali.', $row, Carbon::now());
                    continue;
                }

                $employee = Employee::where('nipeg', $nip)->first();
                if (! $employee) {
                    $ditolak[] = $this->catatError($nip, $i + 2, "NIP {$nip} tidak ditemukan di data karyawan.", $row, $tanggal);
                    continue;
                }

                AttendanceLog::updateOrCreate(
                    ['employee_id' => $employee->id, 'tanggal' => $tanggal->format('Y-m-d')],
                    [
                        'jam_masuk'   => $this->parseJam($row[$idxJamMasuk] ?? null),
                        'jam_pulang'  => $this->parseJam($row[$idxJamPulang] ?? null),
                        'sumber'      => 'mesin_fingerprint',
                        'jumlah_scan' => 2,
                    ]
                );

                $sukses++;
            }
        } else {
            if ($idxTanggal === null && $idxDateTime === null) {
                $this->error('Kolom Tanggal (atau DateTime gabungan) tidak ditemukan di header file.');
                return self::FAILURE;
            }
            if ($idxJamTunggal === null && $idxDateTime === null) {
                $this->error('Kolom Jam/Waktu tidak ditemukan di header file.');
                return self::FAILURE;
            }

            $grouped = [];

            foreach ($dataRows as $i => $row) {
                $nip = trim((string) ($row[$idxNip] ?? ''));
                if (blank($nip)) {
                    continue;
                }

                try {
                    if ($idxDateTime !== null) {
                        $waktu = $this->parseTanggal($row[$idxDateTime] ?? null);
                    } else {
                        $tanggal = $this->parseTanggal($row[$idxTanggal] ?? null);
                        $jam = $this->parseJam($row[$idxJamTunggal] ?? null);
                        $waktu = Carbon::parse($tanggal->format('Y-m-d') . ' ' . $jam);
                    }
                } catch (\Throwable) {
                    $ditolak[] = $this->catatError($nip, $i + 2, 'Format tanggal/jam tidak dikenali pada baris ini.', $row, Carbon::now());
                    continue;
                }

                $grouped[$nip][$waktu->format('Y-m-d')][] = $waktu;
            }

            foreach ($grouped as $nip => $tanggalList) {
                $employee = Employee::where('nipeg', $nip)->first();

                if (! $employee) {
                    foreach ($tanggalList as $tanggalKey => $scans) {
                        $ditolak[] = $this->catatError($nip, null, "NIP {$nip} tidak ditemukan di data karyawan.", ['tanggal' => $tanggalKey, 'jumlah_scan' => count($scans)], Carbon::parse($tanggalKey));
                    }
                    continue;
                }

                foreach ($tanggalList as $tanggalKey => $scans) {
                    sort($scans);
                    $jamMasuk = $scans[0]->format('H:i:s');
                    $jamPulang = count($scans) > 1 ? end($scans)->format('H:i:s') : null;

                    AttendanceLog::updateOrCreate(
                        ['employee_id' => $employee->id, 'tanggal' => $tanggalKey],
                        [
                            'jam_masuk'   => $jamMasuk,
                            'jam_pulang'  => $jamPulang,
                            'sumber'      => 'mesin_fingerprint',
                            'jumlah_scan' => count($scans),
                        ]
                    );

                    $sukses++;
                }
            }
        }

        $this->info("Berhasil diimport: {$sukses} log presensi harian.");

        if ($ditolak) {
            $this->warn(count($ditolak) . ' baris/entri ditolak & disimpan ke tabel attendance_import_errors untuk ditinjau lewat halaman /attendance-import-errors:');
            $this->table(['Baris', 'NIP', 'Alasan'], array_map(fn ($d) => [$d['baris_excel'] ?? '-', $d['nipeg'], $d['alasan']], $ditolak));
        }

        return self::SUCCESS;
    }

    private function cariKolom(array $header, array $kandidat): ?int
    {
        foreach ($kandidat as $nama) {
            $idx = array_search($nama, $header, true);
            if ($idx !== false) {
                return $idx;
            }
        }

        return null;
    }

    private function parseTanggal(mixed $raw): Carbon
    {
        if ($raw instanceof \DateTimeInterface) {
            return Carbon::instance($raw);
        }

        if (is_numeric($raw)) {
            return Carbon::instance(ExcelDate::excelToDateTimeObject($raw));
        }

        if (blank($raw)) {
            throw new \InvalidArgumentException('Tanggal kosong.');
        }

        return Carbon::parse((string) $raw);
    }

    private function parseJam(mixed $raw): ?string
    {
        if (blank($raw)) {
            return null;
        }

        if (is_numeric($raw)) {
            return ExcelDate::excelToDateTimeObject($raw)->format('H:i:s');
        }

        return Carbon::parse((string) $raw)->format('H:i:s');
    }

    private function catatError(string $nip, ?int $baris, string $alasan, array $dataMentah, Carbon $tanggal): array
    {
        AttendanceImportError::create([
            'nipeg'       => $nip,
            'nama'        => null,
            'tahun'       => $tanggal->year,
            'bulan'       => $tanggal->month,
            'jenis'       => 'absensi_harian',
            'baris_excel' => $baris,
            'alasan'      => $alasan,
            'data_mentah' => $dataMentah,
        ]);

        return ['baris_excel' => $baris, 'nipeg' => $nip, 'alasan' => $alasan];
    }
}
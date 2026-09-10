<?php

namespace App\Console\Commands;

use App\Models\AttendanceRecap;
use App\Models\Division;
use App\Models\Employee;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\IOFactory;

// Butuh: composer require phpoffice/phpspreadsheet
// Jalankan: php artisan import:absensi "/path/ke/Rekap_Kehadiran_2026.xlsx"

class ImportAbsensiExcel extends Command
{
    protected $signature = 'import:absensi {path : Path ke file Rekap_Kehadiran_2026.xlsx}';

    protected $description = 'Import sheet "Rekap Per Karyawan" ke tabel employees & attendance_recaps, menolak baris yang melanggar aturan wajar';

    public function handle(): int
    {
        $path = $this->argument('path');

        if (! file_exists($path)) {
            $this->error("File tidak ditemukan: {$path}");
            return self::FAILURE;
        }

        $spreadsheet = IOFactory::load($path);
        $sheet = $spreadsheet->getSheetByName('Rekap Per Karyawan');

        if (! $sheet) {
            $this->error('Sheet "Rekap Per Karyawan" tidak ditemukan di file ini.');
            return self::FAILURE;
        }

        $rows = $sheet->toArray(null, true, true, false);
        // Data mulai dari baris ke-7 di Excel (index 6, karena ada 2 baris judul + 2 baris header gabungan di atasnya).
        $dataRows = array_slice($rows, 6);

        $sukses = 0;
        $ditolak = [];

        foreach ($dataRows as $i => $row) {
            // Kolom A..T -> index 0..19
            [$no, $tahun, $bulan, $nipeg, $nama, $jabatan, $divisiNama, $hariKerja,
             $hadir, $perdin, $cuti, $sakit, $ijin, $alpha, $telatHari, $menitTelat,
             $pctMentah, $lokBandung, $lokJakarta, $koreksi] = array_pad($row, 20, null);

            if (blank($no) || blank($nipeg)) {
                continue; // baris kosong / footer
            }

            $division = Division::firstOrCreate(['nama' => trim((string) $divisiNama)]);

            $employee = Employee::firstOrCreate(
                ['nipeg' => trim((string) $nipeg)],
                ['nama' => trim((string) $nama), 'jabatan' => trim((string) $jabatan), 'division_id' => $division->id]
            );

            try {
                DB::transaction(function () use (
                    $employee, $tahun, $bulan, $hariKerja, $hadir, $perdin, $cuti,
                    $sakit, $ijin, $alpha, $telatHari, $menitTelat, $lokBandung, $lokJakarta, $koreksi
                ) {
                    AttendanceRecap::updateOrCreate(
                        ['employee_id' => $employee->id, 'tahun' => (int) $tahun, 'bulan' => (int) $bulan],
                        [
                            'hari_kerja'       => (int) $hariKerja,
                            'hadir'            => (int) $hadir,
                            'perdin'           => (int) $perdin,
                            'cuti'             => (int) $cuti,
                            'sakit'            => (int) $sakit,
                            'ijin'             => (int) $ijin,
                            'alpha'            => (int) $alpha,
                            'telat_hari'       => (int) $telatHari,
                            'menit_telat'      => (int) $menitTelat,
                            'lokasi_bandung'   => (int) $lokBandung,
                            'lokasi_jakarta'   => (int) $lokJakarta,
                            'koreksi_by_admin' => (int) $koreksi,
                        ]
                    );
                });

                $sukses++;
            } catch (\InvalidArgumentException $e) {
                // Ini yang menangkap 35 baris anomali (menit telat tidak wajar, total hari != hari kerja, dst).
                $ditolak[] = [
                    'baris_excel' => $i + 7,
                    'nama'        => $nama,
                    'periode'     => "{$bulan}/{$tahun}",
                    'alasan'      => $e->getMessage(),
                ];
            }
        }

        $this->info("Berhasil diimport: {$sukses} rekap.");

        if ($ditolak) {
            $this->warn(count($ditolak) . ' baris ditolak karena melanggar validasi:');
            $this->table(['Baris Excel', 'Nama', 'Periode', 'Alasan'], $ditolak);
        }

        return self::SUCCESS;
    }
}
<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\User;

class OvertimeExcelSeeder extends Seeder
{
    public function run()
    {
        // Mengambil pegawai pertama sebagai contoh (sesuaikan jika ingin menargetkan ID pegawai tertentu)
        $employee = User::where('role', '!=', 'hr_admin')->first();

        if (!$employee) {
            $this->command->info('Tidak ada user karyawan di database!');
            return;
        }

        $dataExcel = [
            [
                'ticket_number'   => '3401/010100/2026',
                'user_id'         => $employee->id,
                'department_id'   => $employee->department_id ?? 1,
                'overtime_date'   => '2026-08-04',
                'start_time'      => '16:30',
                'end_time'        => '19:30',
                'estimated_hours' => 3.0,
                'category'        => 'hari_kerja',
                'reason'          => 'DUKUNGAN PEMELIHARAAN SISTEM DAN INFRASTRUKTUR',
                'status'          => 'VERIFIED_HR',
                'created_at'      => now(),
                'updated_at'      => now(),
            ],
            [
                'ticket_number'   => '3402/010100/2026',
                'user_id'         => $employee->id,
                'department_id'   => $employee->department_id ?? 1,
                'overtime_date'   => '2026-08-12',
                'start_time'      => '16:30',
                'end_time'        => '19:30',
                'estimated_hours' => 3.0,
                'category'        => 'hari_kerja',
                'reason'          => 'PERSIAPAN DATA UNTUK EVALUASI KINERJA',
                'status'          => 'VERIFIED_HR',
                'created_at'      => now(),
                'updated_at'      => now(),
            ],
            [
                'ticket_number'   => '3403/010100/2026',
                'user_id'         => $employee->id,
                'department_id'   => $employee->department_id ?? 1,
                'overtime_date'   => '2026-08-20',
                'start_time'      => '16:30',
                'end_time'        => '19:30',
                'estimated_hours' => 3.0,
                'category'        => 'hari_kerja',
                'reason'          => 'PENYELESAIAN TARGET PEKERJAAN PROYEK',
                'status'          => 'VERIFIED_HR',
                'created_at'      => now(),
                'updated_at'      => now(),
            ],
            [
                'ticket_number'   => '3404/010100/2026',
                'user_id'         => $employee->id,
                'department_id'   => $employee->department_id ?? 1,
                'overtime_date'   => '2026-08-24',
                'start_time'      => '16:30',
                'end_time'        => '19:30',
                'estimated_hours' => 3.0,
                'category'        => 'hari_kerja',
                'reason'          => 'DUKUNGAN PROSES CLOSING AKHIR BULAN',
                'status'          => 'VERIFIED_HR',
                'created_at'      => now(),
                'updated_at'      => now(),
            ],
            [
                'ticket_number'   => '3405/010100/2026',
                'user_id'         => $employee->id,
                'department_id'   => $employee->department_id ?? 1,
                'overtime_date'   => '2026-08-28',
                'start_time'      => '16:30',
                'end_time'        => '19:30',
                'estimated_hours' => 3.0,
                'category'        => 'hari_kerja',
                'reason'          => 'PENYELESAIAN LAPORAN OPERASIONAL BULANAN',
                'status'          => 'VERIFIED_HR',
                'created_at'      => now(),
                'updated_at'      => now(),
            ],
            [
                'ticket_number'   => '3406/010100/2026',
                'user_id'         => $employee->id,
                'department_id'   => $employee->department_id ?? 1,
                'overtime_date'   => '2026-08-05',
                'start_time'      => '16:30',
                'end_time'        => '19:30',
                'estimated_hours' => 3.0,
                'category'        => 'hari_kerja',
                'reason'          => 'RAPAT EVALUASI PROGRESS MINGGUAN',
                'status'          => 'VERIFIED_HR',
                'created_at'      => now(),
                'updated_at'      => now(),
            ],
            [
                'ticket_number'   => '3407/010100/2026',
                'user_id'         => $employee->id,
                'department_id'   => $employee->department_id ?? 1,
                'overtime_date'   => '2026-08-14',
                'start_time'      => '16:30',
                'end_time'        => '19:30',
                'estimated_hours' => 3.0,
                'category'        => 'hari_kerja',
                'reason'          => 'PERSIAPAN MATERI PRESENTASI MANAJEMEN',
                'status'          => 'VERIFIED_HR',
                'created_at'      => now(),
                'updated_at'      => now(),
            ],
            [
                'ticket_number'   => '3408/010100/2026',
                'user_id'         => $employee->id,
                'department_id'   => $employee->department_id ?? 1,
                'overtime_date'   => '2026-08-19',
                'start_time'      => '16:30',
                'end_time'        => '19:30',
                'estimated_hours' => 3.0,
                'category'        => 'hari_kerja',
                'reason'          => 'FINALISASI DOKUMEN KONTRAK VENDOR',
                'status'          => 'VERIFIED_HR',
                'created_at'      => now(),
                'updated_at'      => now(),
            ],
            [
                'ticket_number'   => '3409/010100/2026',
                'user_id'         => $employee->id,
                'department_id'   => $employee->department_id ?? 1,
                'overtime_date'   => '2026-08-25',
                'start_time'      => '16:30',
                'end_time'        => '19:30',
                'estimated_hours' => 3.0,
                'category'        => 'hari_kerja',
                'reason'          => 'PENGECEKAN DAN REKONSILIASI DATA',
                'status'          => 'VERIFIED_HR',
                'created_at'      => now(),
                'updated_at'      => now(),
            ],
            [
                'ticket_number'   => '3410/010100/2026',
                'user_id'         => $employee->id,
                'department_id'   => $employee->department_id ?? 1,
                'overtime_date'   => '2026-08-27',
                'start_time'      => '16:30',
                'end_time'        => '19:30',
                'estimated_hours' => 3.0,
                'category'        => 'hari_kerja',
                'reason'          => 'DUKUNGAN MIGRASI SERVER DATABASE',
                'status'          => 'VERIFIED_HR',
                'created_at'      => now(),
                'updated_at'      => now(),
            ],
            // 5 Data diset APPROVED_VP agar mengisi antrean di Halaman Verifikasi HR
            [
                'ticket_number'   => '3420/010100/2026',
                'user_id'         => $employee->id,
                'department_id'   => $employee->department_id ?? 1,
                'overtime_date'   => '2026-08-03',
                'start_time'      => '16:30',
                'end_time'        => '19:30',
                'estimated_hours' => 3.0,
                'category'        => 'hari_kerja',
                'reason'          => 'MAINTENANCE APLIKASI INTERNAL',
                'status'          => 'APPROVED_VP',
                'created_at'      => now(),
                'updated_at'      => now(),
            ],
            [
                'ticket_number'   => '3421/010100/2026',
                'user_id'         => $employee->id,
                'department_id'   => $employee->department_id ?? 1,
                'overtime_date'   => '2026-08-09',
                'start_time'      => '16:30',
                'end_time'        => '19:30',
                'estimated_hours' => 3.0,
                'category'        => 'hari_kerja',
                'reason'          => 'PENYELESAIAN MODUL KEUANGAN BARU',
                'status'          => 'APPROVED_VP',
                'created_at'      => now(),
                'updated_at'      => now(),
            ],
            [
                'ticket_number'   => '3422/010100/2026',
                'user_id'         => $employee->id,
                'department_id'   => $employee->department_id ?? 1,
                'overtime_date'   => '2026-08-16',
                'start_time'      => '16:30',
                'end_time'        => '19:30',
                'estimated_hours' => 3.0,
                'category'        => 'hari_kerja',
                'reason'          => 'BACKUP DATA SERVER UTAMA',
                'status'          => 'APPROVED_VP',
                'created_at'      => now(),
                'updated_at'      => now(),
            ],
            [
                'ticket_number'   => '3423/010100/2026',
                'user_id'         => $employee->id,
                'department_id'   => $employee->department_id ?? 1,
                'overtime_date'   => '2026-08-22',
                'start_time'      => '16:30',
                'end_time'        => '19:30',
                'estimated_hours' => 3.0,
                'category'        => 'hari_kerja',
                'reason'          => 'PENYUSUNAN LAPORAN TRIWULAN',
                'status'          => 'APPROVED_VP',
                'created_at'      => now(),
                'updated_at'      => now(),
            ],
            [
                'ticket_number'   => '3424/010100/2026',
                'user_id'         => $employee->id,
                'department_id'   => $employee->department_id ?? 1,
                'overtime_date'   => '2026-08-30',
                'start_time'      => '16:30',
                'end_time'        => '19:30',
                'estimated_hours' => 3.0,
                'category'        => 'hari_kerja',
                'reason'          => 'UAT (USER ACCEPTANCE TEST) APLIKASI',
                'status'          => 'APPROVED_VP',
                'created_at'      => now(),
                'updated_at'      => now(),
            ]
        ];

        DB::table('overtime_submissions')->insert($dataExcel);

        // Otomatis sinkronkan yang statusnya VERIFIED_HR ke Rekapitulasi Absensi (Attendance Logs)
        foreach ($dataExcel as $row) {
            if ($row['status'] === 'VERIFIED_HR') {
                DB::table('attendance_logs')->updateOrInsert(
                    [
                        'user_id' => $row['user_id'],
                        'date'    => $row['overtime_date'],
                    ],
                    [
                        'overtime_hours'  => $row['estimated_hours'],
                        'overtime_status' => 'VERIFIED',
                        'updated_at'      => now(),
                    ]
                );
            }
        }

        $this->command->info('Data lembur dari Excel berhasil dimasukkan dan disinkronkan ke rekapitulasi HR!');
    }
}
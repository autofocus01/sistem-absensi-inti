<?php

namespace Database\Seeders;

use App\Models\Division;
use App\Models\Employee;
use Illuminate\Database\Seeder;

class DummyEmployeeSeeder extends Seeder
{
    public function run(): void
    {
        $division = Division::firstOrCreate(
            ['nama' => 'Divisi Bisnis & Teknologi'],
            ['lini' => 'UAT']
        );

        $employees = [
            // Band 2 - eligible lembur (34 karyawan teratas untuk lembur)
// Ini disesuaikan agar mencocokkan dengan 34 NIpeg yang sudah diasuransikan di DB
            ['90010001', 'Ahmad Fauzan', 2],
            ['90010002', 'Budi Santoso', 2],
            ['90010003', 'Citra Lestari', 2],
            ['90010004', 'Dimas Pratama', 2],
            ['90010005', 'Eka Putri Maharani', 2],
            ['90010006', 'Fajar Hidayat', 2],
            ['90010007', 'Galih Ramadhan', 2],
            ['90010008', 'Hana Safitri', 2],
            ['90010009', 'Indra Kurniawan', 2],
            ['90010010', 'Joko Susanto', 2],
            ['90010011', 'Kartika Sari', 2],
            ['90010012', 'Lukman Hakim', 2],
            ['90010013', 'Maya Anggraini', 2],
            ['90010014', 'Nugroho Setiawan', 2],
            ['90010015', 'Olivia Permata', 2],
            ['90010016', 'Putra Wijaya', 2],
            ['90010017', 'Rani Wulandari', 2],
            ['90010018', 'Satria Nugraha', 2],
            ['90010019', 'Tania Amelia', 2],
            ['90010020', 'Ujang Firmansyah', 2],
            ['90010021', 'Vina Oktaviani', 2],
            ['90010022', 'Wahyu Ramadhan', 2],
            ['90010023', 'Yuni Kartikasari', 2],
            ['90010024', 'Zaki Maulana', 2],
            ['90010025', 'Aditya Saputra', 2],
            ['90010026', 'Bella Novitasari', 2],
            ['90010027', 'Chandra Wijaya', 2],
            ['90010028', 'Dewi Maharani', 2],
            ['90010029', 'Eko Prasetyo', 2],
            ['90010030', 'Fitri Handayani', 2],

            // Band 4 - non-eligible lembur (sisa 16 karyawan sebagai pembanding)
            ['90010031', 'Gilang Permadi', 4],
            ['90010032', 'Intan Puspitasari', 4],
            ['90010033', 'Reza Kurniawan', 4],
            ['90010034', 'Siska Amelia', 4],
            ['90010035', 'Teguh Setiawan', 4],
            ['90010036', 'Anisa Rahmawati', 4],
            ['90010037', 'Bayu Pangestu', 4],
            ['90010038', 'Deni Firmansyah', 4],
            ['90010039', 'Farah Nabila', 4],
            ['90010040', 'Hendra Gunawan', 4],
            ['90010041', 'Laila Nuraini', 4],
            ['90010042', 'M Rizky Maulana', 4],
            ['90010043', 'Nama Baris Ke-43', 4],
            ['90010044', 'Nama Baris Ke-44', 4],
            ['90010045', 'Nama Baris Ke-45', 4],
            ['90010046', 'Nama Baris Ke-46', 4],
            ['90010047', 'Nama Baris Ke-47', 4],
            ['90010048', 'Nama Baris Ke-48', 4],
            ['90010049', 'Nama Baris Ke-49', 4],
            ['90010050', 'Nama Baris Ke-50', 4],
            ['90010043', 'Nanda Permatasari', 5],
            ['90010044', 'Oki Setiawan', 5],
            ['90010045', 'Raka Adiputra', 5],
            ['90010046', 'Sari Indah Sari', 5],
            ['90010047', 'Taufik Hidayat', 5],
            ['90010048', 'Wulan Sari', 5],
            ['90010049', 'Yudha Pranata', 5],
            ['90010050', 'Zahra Aulia', 5],
        ];

        foreach ($employees as [$nipeg, $nama, $bandCode]) {
            $employee = Employee::updateOrCreate(
                ['nipeg' => $nipeg],
                [
                    'nama' => $nama,
                    'jenis_kelamin' => null,
                    'jabatan' => 'Karyawan UAT',
                    'band_code' => $bandCode,
                    'no_hp' => null,
                    'alamat' => null,
                    'division_id' => $division->id,
                ]
            );

            // Akun UAT dikelola oleh UatAccountSeeder.
            // Jangan mengosongkan user_id di sini karena seeder ini
            // juga dapat dijalankan ulang setelah akun UAT dibuat.
            $employee->save();
        }

        $this->command?->info(
            'Dummy employee UAT berhasil disiapkan: ' . count($employees) . ' karyawan.'
        );
    }
}

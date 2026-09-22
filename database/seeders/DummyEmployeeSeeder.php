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
            ['90010001', 'Ahmad Fauzan'],
            ['90010002', 'Budi Santoso'],
            ['90010003', 'Citra Lestari'],
            ['90010004', 'Dimas Pratama'],
            ['90010005', 'Eka Putri Maharani'],
            ['90010006', 'Fajar Hidayat'],
            ['90010007', 'Galih Ramadhan'],
            ['90010008', 'Hana Safitri'],
            ['90010009', 'Indra Kurniawan'],
            ['90010010', 'Joko Susanto'],
            ['90010011', 'Kartika Sari'],
            ['90010012', 'Lukman Hakim'],
            ['90010013', 'Maya Anggraini'],
            ['90010014', 'Nugroho Setiawan'],
            ['90010015', 'Olivia Permata'],
            ['90010016', 'Putra Wijaya'],
            ['90010017', 'Rani Wulandari'],
            ['90010018', 'Satria Nugraha'],
            ['90010019', 'Tania Amelia'],
            ['90010020', 'Ujang Firmansyah'],
            ['90010021', 'Vina Oktaviani'],
            ['90010022', 'Wahyu Ramadhan'],
            ['90010023', 'Yuni Kartikasari'],
            ['90010024', 'Zaki Maulana'],
            ['90010025', 'Aditya Saputra'],
            ['90010026', 'Bella Novitasari'],
            ['90010027', 'Chandra Wijaya'],
            ['90010028', 'Dewi Maharani'],
            ['90010029', 'Eko Prasetyo'],
            ['90010030', 'Fitri Handayani'],
            ['90010031', 'Gilang Permadi'],
            ['90010032', 'Intan Puspitasari'],
            ['90010033', 'Reza Kurniawan'],
            ['90010034', 'Siska Amelia'],
            ['90010035', 'Teguh Setiawan'],
            ['90010036', 'Anisa Rahmawati'],
            ['90010037', 'Bayu Pangestu'],
            ['90010038', 'Deni Firmansyah'],
            ['90010039', 'Farah Nabila'],
            ['90010040', 'Hendra Gunawan'],
            ['90010041', 'Laila Nuraini'],
            ['90010042', 'M Rizky Maulana'],
            ['90010043', 'Nanda Permatasari'],
            ['90010044', 'Oki Setiawan'],
            ['90010045', 'Raka Adiputra'],
            ['90010046', 'Sari Indah Sari'],
            ['90010047', 'Taufik Hidayat'],
            ['90010048', 'Wulan Sari'],
            ['90010049', 'Yudha Pranata'],
            ['90010050', 'Zahra Aulia'],
        ];

        foreach ($employees as [$nipeg, $nama]) {
            Employee::updateOrCreate(
                ['nipeg' => $nipeg],
                [
                    'nama' => $nama,
                    'jenis_kelamin' => null,
                    'jabatan' => 'Karyawan UAT',
                    'no_hp' => null,
                    'alamat' => null,
                    'division_id' => $division->id,
                    'user_id' => null,
                ]
            );
        }

        $this->command?->info(
            'Dummy employee UAT berhasil disiapkan: ' . count($employees) . ' karyawan.'
        );
    }
}
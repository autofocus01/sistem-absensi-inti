<?php

namespace Database\Seeders;

use App\Models\Division;
use Illuminate\Database\Seeder;

class DivisionSeeder extends Seeder
{
    public function run(): void
    {
        $divisions = [
            ['nama' => 'Divisi Bisnis & Teknologi', 'lini' => 'SEVP'],
            ['nama' => 'Divisi Operasional', 'lini' => 'SEVP'],
            ['nama' => 'Divisi Keuangan & Akuntansi', 'lini' => 'Direktur Langsung'],
            ['nama' => 'Divisi Hukum, Man. Risk & Kepatuhan', 'lini' => 'Direktur Langsung'],
            ['nama' => 'Divisi Pengadaan Strategis', 'lini' => 'SEVP'],
        ];

        foreach ($divisions as $division) {
            Division::firstOrCreate(['nama' => $division['nama']], $division);
        }
    }
}
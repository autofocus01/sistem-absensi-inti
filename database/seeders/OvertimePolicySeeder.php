<?php

namespace Database\Seeders;

use App\Models\CompanyOvertimeRule;
use App\Models\LegalOvertimeRule;
use App\Models\SystemConfiguration;
use Illuminate\Database\Seeder;

class OvertimePolicySeeder extends Seeder
{
    public function run(): void
    {
        LegalOvertimeRule::updateOrCreate(
            ['rule_code' => 'LEGAL-PP35-WORKDAY-2021'],
            [
                'regulation_name' => 'Peraturan Pemerintah Nomor 35 Tahun 2021',
                'regulation_number' => 'PP 35/2021',
                'article_reference' => 'Pasal 26',
                'day_type' => 'WORKDAY',
                'minimum_minutes' => null,
                'maximum_minutes_daily' => 240,
                'maximum_minutes_weekly' => 1080,
                'maximum_minutes_monthly' => null,
                'effective_from' => '2021-11-02',
                'status' => 'active',
                'source_document' => 'PP 35/2021',
                'notes' => 'Batas 4 jam/hari dan 18 jam/minggu; pengecualian untuk lembur pada istirahat mingguan/hari libur resmi mengikuti ketentuan Pasal 26.',
            ]
        );

        foreach (['WEEKEND', 'NATIONAL_HOLIDAY'] as $dayType) {
            LegalOvertimeRule::updateOrCreate(
                ['rule_code' => "LEGAL-PP35-{$dayType}-2021"],
                [
                    'regulation_name' => 'Peraturan Pemerintah Nomor 35 Tahun 2021',
                    'regulation_number' => 'PP 35/2021',
                    'article_reference' => 'Pasal 31-32',
                    'day_type' => $dayType,
                    'minimum_minutes' => null,
                    'maximum_minutes_daily' => null,
                    'maximum_minutes_weekly' => null,
                    'maximum_minutes_monthly' => null,
                    'effective_from' => '2021-11-02',
                    'status' => 'active',
                    'source_document' => 'PP 35/2021',
                    'notes' => 'Untuk pola 5 hari kerja, skema upah lembur hari istirahat mingguan/hari libur resmi: jam 1-8 = 2x, jam ke-9 = 3x, jam ke-10-11 = 4x.',
                ]
            );
        }

        $rules = [
            [
                'rule_code' => 'COMPANY-OT-WORKDAY-V1',
                'rule_name' => 'Lembur Hari Kerja',
                'day_type' => 'WORKDAY',
                'normal_end' => '16:30:00',
                'minimum_minutes' => 30,
                'maximum_minutes_event' => null,
                'maximum_minutes_monthly' => null,
                'rounding_method' => 'floor',
                'rounding_interval' => 30,
                'allows_post_submission' => true,
            ],
            [
                'rule_code' => 'COMPANY-OT-WEEKEND-V1',
                'rule_name' => 'Lembur Weekend',
                'day_type' => 'WEEKEND',
                'normal_end' => null,
                'minimum_minutes' => 60,
                'maximum_minutes_event' => null,
                'maximum_minutes_monthly' => null,
                'rounding_method' => 'floor',
                'rounding_interval' => 60,
                'allows_post_submission' => true,
            ],
            [
                'rule_code' => 'COMPANY-OT-HOLIDAY-V1',
                'rule_name' => 'Lembur Hari Libur Nasional',
                'day_type' => 'NATIONAL_HOLIDAY',
                'normal_end' => null,
                'minimum_minutes' => 60,
                'maximum_minutes_event' => null,
                'maximum_minutes_monthly' => null,
                'rounding_method' => 'floor',
                'rounding_interval' => 60,
                'allows_post_submission' => true,
            ],
        ];

        foreach ($rules as $rule) {
            CompanyOvertimeRule::updateOrCreate(
                ['rule_code' => $rule['rule_code']],
                array_merge($rule, [
                    'requires_request' => true,
                    'requires_vp_approval' => true,
                    'requires_hr_approval' => true,
                    'effective_from' => '2026-09-18',
                    'status' => 'active',
                    'source_document' => 'Kebijakan internal PT INTI - baseline proyek',
                ])
            );
        }

        SystemConfiguration::updateOrCreate(
            ['key' => 'overtime.timezone'],
            ['value' => 'Asia/Jakarta', 'value_type' => 'string', 'status' => 'active', 'description' => 'Timezone resmi engine absensi/lembur.']
        );
    }
}

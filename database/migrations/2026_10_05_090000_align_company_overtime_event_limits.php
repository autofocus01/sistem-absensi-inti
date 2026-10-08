<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('company_overtime_rules')) {
            return;
        }

        DB::table('company_overtime_rules')
            ->where('day_type', 'WORKDAY')
            ->where('rule_code', 'COMPANY-OT-WORKDAY-V1')
            ->update([
                'maximum_minutes_event' => 240,
                'updated_at' => now(),
            ]);

        DB::table('company_overtime_rules')
            ->whereIn('day_type', ['WEEKEND', 'NATIONAL_HOLIDAY'])
            ->whereIn('rule_code', [
                'COMPANY-OT-WEEKEND-V1',
                'COMPANY-OT-HOLIDAY-V1',
            ])
            ->update([
                'maximum_minutes_event' => 360,
                'updated_at' => now(),
            ]);
    }

    public function down(): void
    {
        if (! Schema::hasTable('company_overtime_rules')) {
            return;
        }

        DB::table('company_overtime_rules')
            ->where('rule_code', 'COMPANY-OT-WORKDAY-V1')
            ->update([
                'maximum_minutes_event' => null,
                'updated_at' => now(),
            ]);
    }
};

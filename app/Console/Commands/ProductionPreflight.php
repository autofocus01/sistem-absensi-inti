<?php

namespace App\Console\Commands;

use App\Models\CompanyOvertimeRule;
use App\Models\HolidayCalendar;
use App\Models\LegalOvertimeRule;
use App\Models\SystemConfiguration;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ProductionPreflight extends Command
{
    protected $signature = 'production:preflight {--no-db : Jangan menguji koneksi database}';
    protected $description = 'Pemeriksaan read-only kesiapan deployment production HRIS.';

    public function handle(): int
    {
        $checks = [];
        $checks['APP_ENV'] = in_array((string) config('app.env'), ['production','staging','local'], true);
        $checks['APP_KEY'] = filled(config('app.key'));
        $checks['APP_DEBUG'] = ! (bool) config('app.debug') || in_array((string) config('app.env'), ['local','testing'], true);
        if (! $this->option('no-db')) {
            try { DB::connection()->getPdo(); $checks['DB_CONNECTION'] = true; } catch (\Throwable $e) { $checks['DB_CONNECTION'] = false; }
            foreach (['users','employees','divisions','attendance_logs','overtime_submissions','holiday_calendars','legal_overtime_rules','company_overtime_rules','audit_logs','overtime_policy_snapshots'] as $table) {
                $checks['TABLE_'.$table] = Schema::hasTable($table);
            }

            $checks['ATTENDANCE_UNIQUE_EMPLOYEE_DATE'] = Schema::hasColumn('attendance_logs', 'employee_id')
                && Schema::hasColumn('attendance_logs', 'tanggal');

            $checks['OT_RECONCILIATION_SCHEMA'] = Schema::hasColumn('overtime_submissions', 'actual_start_time')
                && Schema::hasColumn('overtime_submissions', 'actual_end_time')
                && Schema::hasColumn('overtime_submissions', 'recognized_minutes')
                && Schema::hasColumn('overtime_submissions', 'eligibility_status');

            $checks['OT_CONSENT_EVIDENCE_SCHEMA'] = Schema::hasColumn('overtime_submissions', 'employee_consent')
                && Schema::hasColumn('overtime_submissions', 'employee_consent_at')
                && Schema::hasColumn('overtime_submissions', 'employee_consent_ip')
                && Schema::hasColumn('overtime_submissions', 'employee_consent_text');

            $checks['LEGAL_POLICY'] = LegalOvertimeRule::where('status','active')->exists();
            $checks['COMPANY_POLICY'] = CompanyOvertimeRule::where('status','active')->exists();
            $checks['HOLIDAY_DATA'] = HolidayCalendar::where('is_active', true)->exists();
            $checks['OT_TIMEZONE'] = SystemConfiguration::where('key','overtime.timezone')->where('status','active')->exists();
        }
        foreach ($checks as $name => $ok) $this->line(($ok ? '<info>PASS</info>' : '<error>FAIL</error>')." {$name}");
        return in_array(false, $checks, true) ? self::FAILURE : self::SUCCESS;
    }
}

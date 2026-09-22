<?php

namespace App\Console\Commands;

use App\Models\CompanyOvertimeRule;
use App\Models\LegalOvertimeRule;
use App\Services\Overtime\OvertimePolicyConflictValidator;
use Illuminate\Console\Command;

class OvertimePolicyHealthCheck extends Command
{
    protected $signature = 'overtime:policy-health {--date= : Effective date YYYY-MM-DD}';
    protected $description = 'Checks active overtime legal/company policy conflicts for the effective date.';

    public function handle(OvertimePolicyConflictValidator $validator): int
    {
        $date = $this->option('date') ?: now()->toDateString();
        $hasErrors = false;

        foreach (['WORKDAY', 'WEEKEND', 'NATIONAL_HOLIDAY'] as $dayType) {
            $legal = LegalOvertimeRule::query()
                ->where('day_type', $dayType)
                ->where('effective_from', '<=', $date)
                ->where(fn ($q) => $q->whereNull('effective_until')->orWhere('effective_until', '>=', $date))
                ->where('status', 'active')
                ->orderByDesc('effective_from')
                ->first();

            $company = CompanyOvertimeRule::query()
                ->where('day_type', $dayType)
                ->where('effective_from', '<=', $date)
                ->where(fn ($q) => $q->whereNull('effective_until')->orWhere('effective_until', '>=', $date))
                ->where('status', 'active')
                ->orderByDesc('effective_from')
                ->first();

            if (! $legal || ! $company) {
                $this->warn("{$dayType}: missing active legal/company rule for {$date}");
                $hasErrors = true;
                continue;
            }

            $result = $validator->validate(
                $legal->toArray(),
                $company->toArray(),
            );

            if (! $result['valid']) {
                $hasErrors = true;
                $this->error("{$dayType}: policy conflict");
                foreach ($result['conflicts'] as $conflict) {
                    $this->line('  - ' . $conflict['message']);
                }
                continue;
            }

            $this->info("{$dayType}: OK");
        }

        return $hasErrors ? self::FAILURE : self::SUCCESS;
    }
}

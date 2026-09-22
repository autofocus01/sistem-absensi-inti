<?php

namespace App\Services\Overtime;

use App\Models\CompanyOvertimeRule;
use App\Models\LegalOvertimeRule;
use App\Models\SystemConfiguration;
use App\Services\Calendar\HolidayCalendarService;
use Carbon\CarbonInterface;
use RuntimeException;

class OvertimePolicyResolver
{
    public function __construct(
        private readonly HolidayCalendarService $holidayCalendar,
    ) {}

    public function resolve(CarbonInterface $date): array
    {
        // Day type is server-derived. Never trust a client-supplied holiday flag.
        $dayType = $this->holidayCalendar->dayType($date);

        $legal = LegalOvertimeRule::query()
            ->where('day_type', $dayType)
            ->where('effective_from', '<=', $date->toDateString())
            ->where(function ($q) use ($date) {
                $q->whereNull('effective_until')
                    ->orWhere('effective_until', '>=', $date->toDateString());
            })
            ->where('status', 'active')
            ->orderByDesc('effective_from')
            ->first();

        if (! $legal) {
            throw new RuntimeException(
                "Legal overtime rule is not configured for {$dayType} on {$date->toDateString()}."
            );
        }

        $company = CompanyOvertimeRule::query()
            ->where('day_type', $dayType)
            ->where('effective_from', '<=', $date->toDateString())
            ->where(function ($q) use ($date) {
                $q->whereNull('effective_until')
                    ->orWhere('effective_until', '>=', $date->toDateString());
            })
            ->where('status', 'active')
            ->orderByDesc('effective_from')
            ->first();

        if (! $company) {
            throw new RuntimeException(
                "Company overtime policy is not configured for {$dayType}."
            );
        }

        $system = SystemConfiguration::query()
            ->where('key', 'overtime.timezone')
            ->where('status', 'active')
            ->value('value') ?? 'Asia/Jakarta';

        return [
            'day_type' => $dayType,
            'normal_end' => $company->normal_end,
            'minimum_minutes' => $company->minimum_minutes,
            'rounding_method' => $company->rounding_method,
            'rounding_interval' => $company->rounding_interval,
            'maximum_minutes_event' => $company->maximum_minutes_event,
            'legal_maximum_minutes_daily' => $legal->maximum_minutes_daily,
            'legal_maximum_minutes_weekly' => $legal->maximum_minutes_weekly,
            'legal_maximum_minutes_monthly' => $legal->maximum_minutes_monthly,
            'maximum_minutes_monthly' => $company->maximum_minutes_monthly,
            'allows_post_submission' => $company->allows_post_submission,
            'requires_request' => $company->requires_request,
            'requires_vp_approval' => $company->requires_vp_approval,
            'requires_hr_approval' => $company->requires_hr_approval,
            'legal_rule_id' => $legal->id,
            'company_rule_id' => $company->id,
            'timezone' => $system,
        ];
    }
}

<?php

use App\Models\CompanyOvertimeRule;
use App\Models\HolidayCalendar;
use App\Models\LegalOvertimeRule;
use App\Models\SystemConfiguration;
use App\Services\Calendar\HolidayCalendarService;
use App\Services\Overtime\OvertimePolicyResolver;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

pest()->extend(TestCase::class)->use(RefreshDatabase::class);

beforeEach(function () {
    $this->resolver = app(OvertimePolicyResolver::class);

    SystemConfiguration::create([
        'key' => 'overtime.timezone',
        'value' => 'Asia/Jakarta',
        'value_type' => 'string',
        'status' => 'active',
    ]);

    foreach (['WORKDAY', 'WEEKEND', 'NATIONAL_HOLIDAY'] as $dayType) {
        LegalOvertimeRule::create([
            'rule_code' => "TEST-LEGAL-{$dayType}",
            'regulation_name' => 'Test Regulation',
            'day_type' => $dayType,
            'effective_from' => '2026-01-01',
            'status' => 'active',
            'maximum_minutes_daily' => $dayType === 'WORKDAY' ? 240 : null,
        ]);

        CompanyOvertimeRule::create([
            'rule_code' => "TEST-COMPANY-{$dayType}",
            'rule_name' => "Test {$dayType}",
            'day_type' => $dayType,
            'normal_end' => $dayType === 'WORKDAY' ? '16:30:00' : null,
            'minimum_minutes' => $dayType === 'WORKDAY' ? 30 : 60,
            'rounding_method' => 'floor',
            'rounding_interval' => $dayType === 'WORKDAY' ? 30 : 60,
            'requires_request' => true,
            'allows_post_submission' => true,
            'requires_vp_approval' => true,
            'requires_hr_approval' => true,
            'effective_from' => '2026-01-01',
            'status' => 'active',
        ]);
    }
});

it('resolves an ordinary weekday as WORKDAY even when the client would claim holiday', function () {
    $date = Carbon::parse('2026-09-18'); // Friday

    $policy = $this->resolver->resolve($date);

    expect($policy['day_type'])->toBe('WORKDAY');
});

it('resolves an active holiday calendar date as NATIONAL_HOLIDAY', function () {
    HolidayCalendar::create([
        'tanggal' => '2026-09-18',
        'nama' => 'Test National Holiday',
        'jenis' => 'NATIONAL_HOLIDAY',
        'is_active' => true,
    ]);

    $policy = $this->resolver->resolve(Carbon::parse('2026-09-18'));

    expect($policy['day_type'])->toBe('NATIONAL_HOLIDAY');
});

it('resolves an inactive holiday calendar entry as WORKDAY on a weekday', function () {
    HolidayCalendar::create([
        'tanggal' => '2026-09-18',
        'nama' => 'Inactive Holiday',
        'jenis' => 'NATIONAL_HOLIDAY',
        'is_active' => false,
    ]);

    $policy = $this->resolver->resolve(Carbon::parse('2026-09-18'));

    expect($policy['day_type'])->toBe('WORKDAY');
});

it('resolves a weekend as WEEKEND when no active holiday exists', function () {
    $policy = $this->resolver->resolve(Carbon::parse('2026-09-19')); // Saturday

    expect($policy['day_type'])->toBe('WEEKEND');
});

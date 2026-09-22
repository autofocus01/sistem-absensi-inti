<?php

namespace Tests\Unit\Overtime;

use App\Services\Overtime\OvertimeCalculationService;
use Carbon\Carbon;
use Tests\TestCase;

class OvertimeCalculationServiceTest extends TestCase
{
    private OvertimeCalculationService $service;

    protected function setUp(): void
    {
        parent::setUp();

        $this->service = new OvertimeCalculationService();
    }

    /*
    |--------------------------------------------------------------------------
    | WORKDAY
    |--------------------------------------------------------------------------
    */

    public function test_workday_16_30_has_zero_overtime(): void
    {
        $result = $this->service->calculate(
            Carbon::parse('2026-09-14'),
            Carbon::parse('2026-09-14 07:30:00'),
            Carbon::parse('2026-09-14 16:30:00'),
            $this->workdayPolicy(),
        );

        $this->assertSame('WORKDAY', $result['day_type']);
        $this->assertSame(0, $result['actual_minutes']);
        $this->assertSame(0, $result['recognized_minutes']);
        $this->assertSame('NOT_ELIGIBLE', $result['eligibility']);
    }

    public function test_workday_16_31_has_zero_recognized_overtime(): void
    {
        $result = $this->service->calculate(
            Carbon::parse('2026-09-14'),
            Carbon::parse('2026-09-14 07:30:00'),
            Carbon::parse('2026-09-14 16:31:00'),
            $this->workdayPolicy(),
        );

        $this->assertSame('WORKDAY', $result['day_type']);
        $this->assertSame(1, $result['actual_minutes']);
        $this->assertSame(0, $result['recognized_minutes']);
        $this->assertSame('NOT_ELIGIBLE', $result['eligibility']);
    }

    public function test_workday_16_59_has_zero_recognized_overtime(): void
    {
        $result = $this->service->calculate(
            Carbon::parse('2026-09-14'),
            Carbon::parse('2026-09-14 07:30:00'),
            Carbon::parse('2026-09-14 16:59:00'),
            $this->workdayPolicy(),
        );

        $this->assertSame('WORKDAY', $result['day_type']);
        $this->assertSame(29, $result['actual_minutes']);
        $this->assertSame(0, $result['recognized_minutes']);
        $this->assertSame('NOT_ELIGIBLE', $result['eligibility']);
    }

    public function test_workday_17_00_recognizes_30_minutes(): void
    {
        $result = $this->service->calculate(
            Carbon::parse('2026-09-14'),
            Carbon::parse('2026-09-14 07:30:00'),
            Carbon::parse('2026-09-14 17:00:00'),
            $this->workdayPolicy(),
        );

        $this->assertSame('WORKDAY', $result['day_type']);
        $this->assertSame(30, $result['actual_minutes']);
        $this->assertSame(30, $result['recognized_minutes']);
        $this->assertSame('ELIGIBLE', $result['eligibility']);
    }

    public function test_workday_17_01_recognizes_30_minutes(): void
    {
        $result = $this->service->calculate(
            Carbon::parse('2026-09-14'),
            Carbon::parse('2026-09-14 07:30:00'),
            Carbon::parse('2026-09-14 17:01:00'),
            $this->workdayPolicy(),
        );

        $this->assertSame('WORKDAY', $result['day_type']);
        $this->assertSame(31, $result['actual_minutes']);
        $this->assertSame(30, $result['recognized_minutes']);
        $this->assertSame('ELIGIBLE', $result['eligibility']);
    }

    public function test_workday_17_17_recognizes_30_minutes(): void
    {
        $result = $this->service->calculate(
            Carbon::parse('2026-09-14'),
            Carbon::parse('2026-09-14 07:30:00'),
            Carbon::parse('2026-09-14 17:17:00'),
            $this->workdayPolicy(),
        );

        $this->assertSame('WORKDAY', $result['day_type']);
        $this->assertSame(47, $result['actual_minutes']);
        $this->assertSame(30, $result['recognized_minutes']);
        $this->assertSame('ELIGIBLE', $result['eligibility']);
    }

    public function test_workday_17_29_recognizes_30_minutes(): void
    {
        $result = $this->service->calculate(
            Carbon::parse('2026-09-14'),
            Carbon::parse('2026-09-14 07:30:00'),
            Carbon::parse('2026-09-14 17:29:00'),
            $this->workdayPolicy(),
        );

        $this->assertSame('WORKDAY', $result['day_type']);
        $this->assertSame(59, $result['actual_minutes']);
        $this->assertSame(30, $result['recognized_minutes']);
        $this->assertSame('ELIGIBLE', $result['eligibility']);
    }

    public function test_workday_17_30_recognizes_60_minutes(): void
    {
        $result = $this->service->calculate(
            Carbon::parse('2026-09-14'),
            Carbon::parse('2026-09-14 07:30:00'),
            Carbon::parse('2026-09-14 17:30:00'),
            $this->workdayPolicy(),
        );

        $this->assertSame('WORKDAY', $result['day_type']);
        $this->assertSame(60, $result['actual_minutes']);
        $this->assertSame(60, $result['recognized_minutes']);
        $this->assertSame('ELIGIBLE', $result['eligibility']);
    }

    /*
    |--------------------------------------------------------------------------
    | WEEKEND
    |--------------------------------------------------------------------------
    */

    public function test_weekend_60_minutes_recognizes_60_minutes(): void
    {
        $result = $this->service->calculate(
            Carbon::parse('2026-09-19'),
            Carbon::parse('2026-09-19 08:00:00'),
            Carbon::parse('2026-09-19 09:00:00'),
            $this->weekendPolicy(),
        );

        $this->assertSame('WEEKEND', $result['day_type']);
        $this->assertSame(60, $result['actual_minutes']);
        $this->assertSame(60, $result['recognized_minutes']);
        $this->assertSame('ELIGIBLE', $result['eligibility']);
    }

    public function test_weekend_59_minutes_is_not_eligible(): void
    {
        $result = $this->service->calculate(
            Carbon::parse('2026-09-19'),
            Carbon::parse('2026-09-19 08:00:00'),
            Carbon::parse('2026-09-19 08:59:00'),
            $this->weekendPolicy(),
        );

        $this->assertSame('WEEKEND', $result['day_type']);
        $this->assertSame(59, $result['actual_minutes']);
        $this->assertSame(0, $result['recognized_minutes']);
        $this->assertSame('NOT_ELIGIBLE', $result['eligibility']);
    }

    public function test_weekend_90_minutes_is_rounded_down_to_60_minutes(): void
    {
        $result = $this->service->calculate(
            Carbon::parse('2026-09-19'),
            Carbon::parse('2026-09-19 08:00:00'),
            Carbon::parse('2026-09-19 09:30:00'),
            $this->weekendPolicy(),
        );

        $this->assertSame('WEEKEND', $result['day_type']);
        $this->assertSame(90, $result['actual_minutes']);
        $this->assertSame(60, $result['recognized_minutes']);
        $this->assertSame('ELIGIBLE', $result['eligibility']);
    }

    /*
    |--------------------------------------------------------------------------
    | NATIONAL HOLIDAY
    |--------------------------------------------------------------------------
    */

    public function test_national_holiday_60_minutes_recognizes_60_minutes(): void
    {
        $result = $this->service->calculate(
            Carbon::parse('2026-08-17'),
            Carbon::parse('2026-08-17 08:00:00'),
            Carbon::parse('2026-08-17 09:00:00'),
            $this->holidayPolicy(),
        );

        $this->assertSame('NATIONAL_HOLIDAY', $result['day_type']);
        $this->assertSame(60, $result['actual_minutes']);
        $this->assertSame(60, $result['recognized_minutes']);
        $this->assertSame('ELIGIBLE', $result['eligibility']);
    }

    public function test_national_holiday_59_minutes_is_not_eligible(): void
    {
        $result = $this->service->calculate(
            Carbon::parse('2026-08-17'),
            Carbon::parse('2026-08-17 08:00:00'),
            Carbon::parse('2026-08-17 08:59:00'),
            $this->holidayPolicy(),
        );

        $this->assertSame('NATIONAL_HOLIDAY', $result['day_type']);
        $this->assertSame(59, $result['actual_minutes']);
        $this->assertSame(0, $result['recognized_minutes']);
        $this->assertSame('NOT_ELIGIBLE', $result['eligibility']);
    }

    /*
    |--------------------------------------------------------------------------
    | INVALID INTERVAL
    |--------------------------------------------------------------------------
    */

    public function test_invalid_interval_is_rejected(): void
    {
        $result = $this->service->calculate(
            Carbon::parse('2026-09-14'),
            Carbon::parse('2026-09-14 17:00:00'),
            Carbon::parse('2026-09-14 16:30:00'),
            $this->workdayPolicy(),
        );

        $this->assertSame('WORKDAY', $result['day_type']);
        $this->assertSame(0, $result['actual_minutes']);
        $this->assertSame(0, $result['recognized_minutes']);
        $this->assertSame('INVALID_INTERVAL', $result['eligibility']);
    }

    /*
    |--------------------------------------------------------------------------
    | MAXIMUM OVERTIME PER EVENT
    |--------------------------------------------------------------------------
    */

    public function test_maximum_minutes_event_is_applied(): void
    {
        $policy = $this->workdayPolicy([
            'maximum_minutes_event' => 120,
        ]);

        $result = $this->service->calculate(
            Carbon::parse('2026-09-14'),
            Carbon::parse('2026-09-14 07:30:00'),
            Carbon::parse('2026-09-14 20:00:00'),
            $policy,
        );

        /*
         * 16:30 -> 20:00 = 210 actual overtime minutes.
         * Maximum event = 120 minutes.
         */
        $this->assertSame('WORKDAY', $result['day_type']);
        $this->assertSame(210, $result['actual_minutes']);
        $this->assertSame(120, $result['recognized_minutes']);
        $this->assertSame('ELIGIBLE', $result['eligibility']);
    }

    /*
    |--------------------------------------------------------------------------
    | POLICY DAY TYPE
    |--------------------------------------------------------------------------
    */

    public function test_workday_policy_produces_workday(): void
    {
        $result = $this->service->calculate(
            Carbon::parse('2026-09-14'),
            Carbon::parse('2026-09-14 07:30:00'),
            Carbon::parse('2026-09-14 17:00:00'),
            $this->workdayPolicy(),
        );

        $this->assertSame('WORKDAY', $result['day_type']);
    }

    public function test_weekend_policy_produces_weekend(): void
    {
        /*
         * day_type berasal dari policy yang sudah di-resolve.
         * CalculationService tidak menentukan sendiri tipe hari.
         */
        $result = $this->service->calculate(
            Carbon::parse('2026-09-14'),
            Carbon::parse('2026-09-14 08:00:00'),
            Carbon::parse('2026-09-14 09:00:00'),
            $this->weekendPolicy(),
        );

        $this->assertSame('WEEKEND', $result['day_type']);
        $this->assertSame(60, $result['recognized_minutes']);
    }

    public function test_national_holiday_policy_produces_national_holiday(): void
    {
        /*
         * Sengaja memakai tanggal weekday biasa.
         *
         * CalculationService menerima day_type dari policy.
         * Validasi apakah tanggal benar-benar holiday adalah
         * tanggung jawab HolidayCalendarService/PolicyResolver.
         */
        $result = $this->service->calculate(
            Carbon::parse('2026-09-14'),
            Carbon::parse('2026-09-14 08:00:00'),
            Carbon::parse('2026-09-14 09:00:00'),
            $this->holidayPolicy(),
        );

        $this->assertSame('NATIONAL_HOLIDAY', $result['day_type']);
        $this->assertSame(60, $result['recognized_minutes']);
    }

    /*
    |--------------------------------------------------------------------------
    | HELPERS
    |--------------------------------------------------------------------------
    */

    private function workdayPolicy(array $overrides = []): array
    {
        return array_merge([
            'day_type' => 'WORKDAY',
            'normal_end' => '16:30:00',
            'minimum_minutes' => 30,
            'rounding_method' => 'floor',
            'rounding_interval' => 30,
            'maximum_minutes_event' => null,
        ], $overrides);
    }

    private function weekendPolicy(array $overrides = []): array
    {
        return array_merge([
            'day_type' => 'WEEKEND',
            'normal_end' => '16:30:00',
            'minimum_minutes' => 60,
            'rounding_method' => 'floor',
            'rounding_interval' => 60,
            'maximum_minutes_event' => null,
        ], $overrides);
    }

    private function holidayPolicy(array $overrides = []): array
    {
        return array_merge([
            'day_type' => 'NATIONAL_HOLIDAY',
            'normal_end' => '16:30:00',
            'minimum_minutes' => 60,
            'rounding_method' => 'floor',
            'rounding_interval' => 60,
            'maximum_minutes_event' => null,
        ], $overrides);
    }
}
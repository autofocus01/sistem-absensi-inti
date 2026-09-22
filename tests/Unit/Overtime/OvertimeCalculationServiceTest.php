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

    public function test_weekend_60_minutes_is_eligible(): void
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

    public function test_national_holiday_60_minutes_is_eligible(): void
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

        $this->assertSame('WORKDAY', $result['day_type']);
        $this->assertSame(210, $result['actual_minutes']);
        $this->assertSame(120, $result['recognized_minutes']);
        $this->assertSame('ELIGIBLE', $result['eligibility']);
    }

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


test('same-date clock-out before overtime start is not eligible and never becomes overnight overtime', function () {
    $service = app(\App\Services\Overtime\OvertimeCalculationService::class);

    $result = $service->calculate(
        Carbon::parse('2026-09-21 00:00:00'),
        Carbon::parse('2026-09-21 16:30:00'),
        Carbon::parse('2026-09-21 10:00:00'),
        [
            'day_type' => 'WORKDAY',
            'normal_end' => '16:30:00',
            'minimum_minutes' => 30,
            'rounding_method' => 'floor',
            'rounding_interval' => 30,
            'maximum_minutes_event' => null,
        ],
    );

    expect($result['actual_minutes'])->toBe(0)
        ->and($result['recognized_minutes'])->toBe(0)
        ->and($result['eligibility'])->toBe('INVALID_INTERVAL');
});

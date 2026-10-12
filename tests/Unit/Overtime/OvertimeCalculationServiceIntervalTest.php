<?php

use App\Services\Overtime\OvertimeCalculationService;
use Carbon\Carbon;

/*
 * Dipisahkan dari OvertimeCalculationServiceTest.php (class-based) agar Pest
 * tidak mengambil alih file tersebut. File berisi class + closure menyebabkan
 * Pest hanya mendaftarkan closure dan mengabaikan seluruh method test_*.
 */
test('same-date clock-out before overtime start is not eligible and never becomes overnight overtime', function () {
    $service = app(OvertimeCalculationService::class);

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

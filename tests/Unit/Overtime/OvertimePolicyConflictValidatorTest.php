<?php

use App\Services\Overtime\OvertimePolicyConflictValidator;

it('accepts company limits below legal limits', function () {
    $result = app(OvertimePolicyConflictValidator::class)->validate(
        ['maximum_minutes_daily' => 240, 'maximum_minutes_monthly' => 1000],
        ['maximum_minutes_event' => 180, 'maximum_minutes_monthly' => 900],
    );

    expect($result['valid'])->toBeTrue()
        ->and($result['conflicts'])->toBe([]);
});

it('detects company event limit above legal daily limit', function () {
    $result = app(OvertimePolicyConflictValidator::class)->validate(
        ['maximum_minutes_daily' => 240],
        ['maximum_minutes_event' => 300],
    );

    expect($result['valid'])->toBeFalse()
        ->and($result['conflicts'])->toHaveCount(1)
        ->and($result['conflicts'][0]['scope'])->toBe('event');
});

it('detects company monthly limit above legal monthly limit', function () {
    $result = app(OvertimePolicyConflictValidator::class)->validate(
        ['maximum_minutes_monthly' => 1000],
        ['maximum_minutes_monthly' => 1200],
    );

    expect($result['valid'])->toBeFalse()
        ->and($result['conflicts'][0]['scope'])->toBe('monthly');
});

it('does not invent a conflict when legal upper bound is not configured', function () {
    $result = app(OvertimePolicyConflictValidator::class)->validate(
        ['maximum_minutes_daily' => null, 'maximum_minutes_monthly' => null],
        ['maximum_minutes_event' => 480, 'maximum_minutes_monthly' => 5000],
    );

    expect($result['valid'])->toBeTrue();
});

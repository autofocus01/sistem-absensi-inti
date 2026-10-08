<?php

namespace App\Services\Overtime;

use Carbon\CarbonInterface;
use InvalidArgumentException;

class OvertimeCalculationService
{
    /**
     * Calculate overtime using server-resolved policy.
     *
     * Maximum event is a CAP on recognized overtime, not a hard rejection.
     * Example: 300 actual minutes with a 240-minute maximum becomes
     * 240 recognized minutes + 60 excess minutes.
     */
    public function calculate(
        CarbonInterface $date,
        CarbonInterface $actualStart,
        CarbonInterface $actualEnd,
        array $policy,
    ): array {
        $dayType = $policy['day_type'] ?? null;

        if (! in_array($dayType, ['WORKDAY', 'WEEKEND', 'NATIONAL_HOLIDAY'], true)) {
            throw new InvalidArgumentException('Invalid or missing overtime day type.');
        }

        if ($actualEnd->lessThanOrEqualTo($actualStart)) {
            return $this->result($dayType, 0, $policy, 'INVALID_INTERVAL');
        }

        if ($dayType === 'WORKDAY') {
            $normalEnd = $actualStart->copy()->setTimeFromTimeString(
                $policy['normal_end'] ?? '16:30:00'
            );

            $overtimeStart = $actualStart->greaterThan($normalEnd)
                ? $actualStart
                : $normalEnd;

            $actualMinutes = $overtimeStart->lessThan($actualEnd)
                ? $overtimeStart->diffInMinutes($actualEnd)
                : 0;
        } else {
            $actualMinutes = $actualStart->diffInMinutes($actualEnd);
        }

        $minimum = (int) ($policy['minimum_minutes'] ?? 30);
        $interval = max(1, (int) ($policy['rounding_interval'] ?? 30));
        $rounding = $policy['rounding_method'] ?? 'floor';

        $roundedMinutes = match ($rounding) {
            'ceil' => (int) (ceil($actualMinutes / $interval) * $interval),
            'nearest' => (int) (round($actualMinutes / $interval) * $interval),
            default => intdiv($actualMinutes, $interval) * $interval,
        };

        $maximumEvent = $this->nullableInt($policy['maximum_minutes_event'] ?? null);

        // Recognized OT is capped at the policy maximum. Excess is never
        // recognized and therefore never participates in quota calculations.
        $recognizedMinutes = $maximumEvent === null
            ? $roundedMinutes
            : min($roundedMinutes, $maximumEvent);

        $excessMinutes = $maximumEvent !== null
            ? max(0, $actualMinutes - $maximumEvent)
            : 0;

        $eligibility = $recognizedMinutes >= $minimum
            ? 'ELIGIBLE'
            : 'NOT_ELIGIBLE';

        return $this->result(
            $dayType,
            $actualMinutes,
            $policy,
            $eligibility,
            $recognizedMinutes,
            $roundedMinutes,
            $excessMinutes,
        );
    }

    private function nullableInt(mixed $value): ?int
    {
        if ($value === null || $value === '') {
            return null;
        }

        return (int) $value;
    }

    private function result(
        string $dayType,
        int $actualMinutes,
        array $policy,
        string $eligibility,
        int $recognizedMinutes = 0,
        ?int $roundedMinutes = null,
        int $excessMinutes = 0,
    ): array {
        $roundedMinutes ??= $recognizedMinutes;

        return [
            'day_type' => $dayType,
            'actual_minutes' => $actualMinutes,
            'rounded_minutes' => $roundedMinutes,
            'recognized_minutes' => $recognizedMinutes,
            'excess_minutes' => $excessMinutes,
            'actual_hours' => round($actualMinutes / 60, 2),
            'recognized_hours' => round($recognizedMinutes / 60, 2),
            'excess_hours' => round($excessMinutes / 60, 2),
            'eligibility' => $eligibility,
            'minimum_minutes' => (int) ($policy['minimum_minutes'] ?? 30),
            'maximum_minutes_event' => $this->nullableInt($policy['maximum_minutes_event'] ?? null),
            'rounding_method' => $policy['rounding_method'] ?? 'floor',
            'rounding_interval' => (int) ($policy['rounding_interval'] ?? 30),
        ];
    }
}

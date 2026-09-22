<?php

namespace App\Services\Overtime;

use Carbon\CarbonInterface;
use InvalidArgumentException;

class OvertimeCalculationService
{
    /**
     * Calculate recognized overtime from actual attendance time
     * and an effective policy.
     *
     * IMPORTANT:
     *
     * Day type is determined upstream by OvertimePolicyResolver
     * using HolidayCalendarService.
     *
     * This service MUST NOT trust:
     *
     *     policy['is_holiday']
     *
     * or any client supplied holiday flag.
     *
     * The single source of truth is:
     *
     *     policy['day_type']
     *
     * Supported day types:
     *
     *     WORKDAY
     *     WEEKEND
     *     NATIONAL_HOLIDAY
     */
    public function calculate(
        CarbonInterface $date,
        CarbonInterface $actualStart,
        CarbonInterface $actualEnd,
        array $policy,
    ): array {
        /*
         * HARD RULE:
         *
         * Day type must already have been resolved by
         * OvertimePolicyResolver.
         *
         * Do not derive it from request input here.
         */
        $dayType = $policy['day_type'] ?? null;

        if (! in_array(
            $dayType,
            [
                'WORKDAY',
                'WEEKEND',
                'NATIONAL_HOLIDAY',
            ],
            true
        )) {
            throw new InvalidArgumentException(
                'Invalid or missing overtime day type.'
            );
        }

        /*
         * Invalid time interval.
         */
        if ($actualEnd->lessThanOrEqualTo($actualStart)) {
            return $this->result(
                $dayType,
                0,
                $policy,
                'INVALID_INTERVAL'
            );
        }

        /*
         * WORKDAY
         *
         * Overtime starts only after the company's normal end time.
         *
         * Example:
         *
         * 16:30 -> 0
         * 16:59 -> 0
         * 17:00 -> 30
         * 17:30 -> 60
         */
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
            /*
             * WEEKEND / NATIONAL_HOLIDAY
             *
             * Overtime duration is the actual interval supplied
             * by the caller.
             */
            $actualMinutes = $actualStart->diffInMinutes($actualEnd);
        }

        $minimum = (int) (
            $policy['minimum_minutes'] ?? 30
        );

        $interval = max(
            1,
            (int) (
                $policy['rounding_interval'] ?? 30
            )
        );

        $rounding = $policy['rounding_method'] ?? 'floor';

        /*
         * Apply company rounding rule.
         */
        $recognizedMinutes = match ($rounding) {
            'ceil' => (int) (
                ceil($actualMinutes / $interval)
                * $interval
            ),

            'nearest' => (int) (
                round($actualMinutes / $interval)
                * $interval
            ),

            default => intdiv(
                $actualMinutes,
                $interval
            ) * $interval,
        };

        /*
         * Apply minimum OT requirement.
         */
        if ($recognizedMinutes < $minimum) {
            $recognizedMinutes = 0;
            $eligibility = 'NOT_ELIGIBLE';
        } else {
            $eligibility = 'ELIGIBLE';
        }

        /*
         * Apply company maximum per event.
         *
         * IMPORTANT:
         *
         * Legal daily maximum is also supplied by PolicyResolver.
         * That limit will be enforced separately as part of the
         * legal/company aggregation hardening.
         */
if (
    ! empty($policy['maximum_minutes_event'])
    && $recognizedMinutes > (int) $policy['maximum_minutes_event']
) {
    $recognizedMinutes = (int) $policy['maximum_minutes_event'];

    /*
     * Re-apply floor rounding after maximum cap.
     */
    $recognizedMinutes = intdiv(
        $recognizedMinutes,
        $interval
    ) * $interval;

    $eligibility = $recognizedMinutes >= $minimum
        ? 'ELIGIBLE'
        : 'NOT_ELIGIBLE';
}

        return $this->result(
            $dayType,
            $actualMinutes,
            $policy,
            $eligibility,
            $recognizedMinutes
        );
    }

    private function result(
        string $dayType,
        int $actualMinutes,
        array $policy,
        string $eligibility,
        int $recognizedMinutes = 0,
    ): array {
        return [
            'day_type' => $dayType,

            'actual_minutes' => $actualMinutes,

            'recognized_minutes' => $recognizedMinutes,

            'actual_hours' => round(
                $actualMinutes / 60,
                2
            ),

            'recognized_hours' => round(
                $recognizedMinutes / 60,
                2
            ),

            'eligibility' => $eligibility,

            'minimum_minutes' => (int) (
                $policy['minimum_minutes'] ?? 30
            ),

            'rounding_method' => (
                $policy['rounding_method'] ?? 'floor'
            ),

            'rounding_interval' => (int) (
                $policy['rounding_interval'] ?? 30
            ),
        ];
    }
}
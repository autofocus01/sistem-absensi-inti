<?php

namespace App\Services\Overtime;

use Illuminate\Support\Collection;

class MonthlyOvertimeAggregator
{
    /**
     * Aggregate recognized overtime events without treating the monthly total as the source of truth.
     */
    public function summarize(Collection|array $events): array
    {
        $events = collect($events);

        return [
            'weekday_minutes' => (int) $events->where('day_type', 'WORKDAY')->sum('recognized_minutes'),
            'weekend_minutes' => (int) $events->where('day_type', 'WEEKEND')->sum('recognized_minutes'),
            'national_holiday_minutes' => (int) $events->where('day_type', 'NATIONAL_HOLIDAY')->sum('recognized_minutes'),
            'total_minutes' => (int) $events->sum('recognized_minutes'),
            'event_count' => $events->count(),
        ];
    }
}

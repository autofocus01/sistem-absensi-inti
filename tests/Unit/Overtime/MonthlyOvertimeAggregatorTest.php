<?php

use App\Services\Overtime\MonthlyOvertimeAggregator;
use Illuminate\Support\Collection;

it('keeps weekday, weekend and holiday monthly totals separate', function () {
    $result = (new MonthlyOvertimeAggregator())->summarize(new Collection([
        ['day_type' => 'WORKDAY', 'recognized_minutes' => 120],
        ['day_type' => 'WEEKEND', 'recognized_minutes' => 480],
        ['day_type' => 'NATIONAL_HOLIDAY', 'recognized_minutes' => 60],
    ]));

    expect($result)->toMatchArray([
        'weekday_minutes' => 120,
        'weekend_minutes' => 480,
        'national_holiday_minutes' => 60,
        'total_minutes' => 660,
        'event_count' => 3,
    ]);
});

<?php

namespace App\Services\Calendar;

use App\Models\HolidayCalendar;
use Carbon\CarbonInterface;

class HolidayCalendarService
{
    public function find(CarbonInterface $date): ?HolidayCalendar
    {
        return HolidayCalendar::query()
            ->whereDate('tanggal', $date->toDateString())
            ->where('is_active', true)
            ->first();
    }

    public function isNationalHoliday(CarbonInterface $date): bool
    {
        $holiday = $this->find($date);

        return $holiday?->isNationalHoliday() ?? false;
    }

    public function dayType(CarbonInterface $date): string
    {
        if ($this->isNationalHoliday($date)) {
            return 'NATIONAL_HOLIDAY';
        }

        if ($date->isWeekend()) {
            return 'WEEKEND';
        }

        return 'WORKDAY';
    }
}

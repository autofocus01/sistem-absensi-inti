<?php

namespace App\Services\Demo;

use Carbon\Carbon;
use InvalidArgumentException;

final class DemoClockService
{
    public function enabled(): bool
    {
        return (bool) config('demo.enabled', false);
    }

    public function now(): Carbon
    {
        $timezone = (string) config('demo.timezone', config('app.timezone', 'Asia/Jakarta'));

        if (! $this->enabled()) {
            return Carbon::now($timezone);
        }

        $stored = session('demo.datetime');

        if (is_string($stored) && $stored !== '') {
            try {
                return Carbon::parse($stored, $timezone)->setTimezone($timezone);
            } catch (\Throwable) {
                session()->forget('demo.datetime');
            }
        }

        return Carbon::now($timezone);
    }

    public function setTime(string $time): Carbon
    {
        if (! preg_match('/^\d{2}:\d{2}$/', $time)) {
            throw new InvalidArgumentException('Format waktu demo harus HH:MM.');
        }

        [$hour, $minute] = array_map('intval', explode(':', $time));

        if ($hour > 23 || $minute > 59) {
            throw new InvalidArgumentException('Waktu demo tidak valid.');
        }

        $now = $this->now();
        $now->setTime($hour, $minute, 0);
        session()->put('demo.datetime', $now->toDateTimeString());

        return $now;
    }

    public function reset(): void
    {
        session()->forget('demo.datetime');
    }

    public function isSimulated(): bool
    {
        return $this->enabled() && is_string(session('demo.datetime')) && session('demo.datetime') !== '';
    }

    public function presets(): array
    {
        return (array) config('demo.presets', []);
    }
}

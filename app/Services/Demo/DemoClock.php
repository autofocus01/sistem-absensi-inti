<?php

namespace App\Services\Demo;

use Carbon\Carbon;

class DemoClock
{
    private const SESSION_KEY = 'demo.datetime';

    public function enabled(): bool
    {
        return (bool) config('app.demo_mode', false);
    }

    public function now(): Carbon
    {
        $timezone = config('app.demo_timezone', config('app.timezone', 'Asia/Jakarta'));
        $value = session(self::SESSION_KEY);

        if ($this->enabled() && $value) {
            try {
                return Carbon::parse($value, $timezone);
            } catch (\Throwable) {
                session()->forget(self::SESSION_KEY);
            }
        }

        return Carbon::now($timezone);
    }

    public function set(string $datetime): Carbon
    {
        abort_unless($this->enabled(), 404);

        $timezone = config('app.demo_timezone', config('app.timezone', 'Asia/Jakarta'));
        $parsed = Carbon::parse($datetime, $timezone);
        session()->put(self::SESSION_KEY, $parsed->format('Y-m-d H:i:s'));

        return $parsed;
    }

    public function setTime(string $time): Carbon
    {
        $base = $this->now();
        return $this->set($base->copy()->format('Y-m-d') . ' ' . $time . ':00');
    }

    public function reset(): void
    {
        session()->forget(self::SESSION_KEY);
    }

    public function active(): bool
    {
        return $this->enabled() && session()->has(self::SESSION_KEY);
    }

    public function value(): ?string
    {
        return $this->active() ? $this->now()->format('Y-m-d H:i:s') : null;
    }
}

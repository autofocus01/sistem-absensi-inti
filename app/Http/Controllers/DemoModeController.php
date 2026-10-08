<?php

namespace App\Http\Controllers;

use App\Services\Calendar\HolidayCalendarService;
use App\Services\Demo\DemoClock;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DemoModeController extends Controller
{
    public function __construct(
        private readonly DemoClock $clock,
        private readonly HolidayCalendarService $holidayCalendar,
    ) {}

    private function ensureEnabled(): void
    {
        abort_unless($this->clock->enabled(), 404);
    }

    public function index(Request $request): View
    {
        $this->ensureEnabled();
        abort_unless($request->user(), 403);

        return view('demo.index', [
            'now' => $this->clock->now(),
            'active' => $this->clock->active(),
            'dayType' => $this->dayType($this->clock->now()),
        ]);
    }

    public function setTime(Request $request): RedirectResponse
    {
        $this->ensureEnabled();
        abort_unless($request->user(), 403);

        $validated = $request->validate([
            'time' => ['required', 'date_format:H:i'],
        ]);

        $time = $this->clock->setTime($validated['time']);

        return back()->with('status', 'Waktu demo diatur ke ' . $time->format('d/m/Y H:i') . ' WIB.');
    }

    public function setDateTime(Request $request): RedirectResponse
    {
        $this->ensureEnabled();
        abort_unless($request->user(), 403);

        $validated = $request->validate([
            'date' => ['required', 'date_format:Y-m-d'],
            'time' => ['required', 'date_format:H:i'],
        ]);

        $datetime = $this->clock->set($validated['date'] . ' ' . $validated['time'] . ':00');

        return back()->with('status', 'Tanggal dan waktu demo diatur ke ' . $datetime->format('d/m/Y H:i') . ' WIB.');
    }

    private function dayType(\Carbon\Carbon $date): string
    {
        return $this->holidayCalendar->dayType($date);
    }

    public function reset(Request $request): RedirectResponse
    {
        $this->ensureEnabled();
        abort_unless($request->user(), 403);

        $this->clock->reset();

        return back()->with('status', 'Waktu demo dikembalikan ke waktu sistem.');
    }
}

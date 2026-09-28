# P3 UI Business Rules Release

## Scope
P1 visual system remains the baseline. This release adds the P3 business-information layer without redesigning the locked P1 shell.

## UI changes
- Employee overtime page is single-day / today-only after Clock-out.
- Overtime start/end shown from attendance-derived evidence; no manual date selection.
- Actual, recognized, rounded and excess minutes are surfaced.
- Weekday 8-hour gate is shown only for WORKDAY; weekend/holiday does not show an 8-hour requirement.
- Overtime policy summary and monthly quota usage are surfaced.
- Contextual anomaly messages are shown without deleting attendance evidence.
- VP approval queue surfaces attendance evidence, actual/recognized/excess and anomaly codes.
- HR verification queue surfaces the same evidence and final workflow state.
- Employee overtime history surfaces actual/rounded/recognized/excess and anomaly codes.

## UAT data
`UatUserSeeder` creates verified UAT accounts and links:
- karyawan@test.local -> employee 90010001, band 2
- vp@test.local -> employee 90010002, band 3
- vp.hukum@test.local -> employee 90010023, band 3
- hr@test.local -> HR admin
- direktur@test.local -> director

Password for UAT accounts: `password`

## Validation
- PHP lint passed for changed PHP files.
- Blade compiler validation passed for changed views.
- Full `php artisan` UI cache/preflight could not run in the build environment because PHP `DOMDocument` is unavailable.

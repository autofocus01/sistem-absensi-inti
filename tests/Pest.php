<?php

use App\Models\Employee;
use App\Models\OvertimeSubmission;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/*
|--------------------------------------------------------------------------
| Test Case
|--------------------------------------------------------------------------
|
| The closure you provide to your test functions is always bound to a specific PHPUnit test
| case class. By default, that class is "PHPUnit\Framework\TestCase". Of course, you may
| need to change it using the "pest()" function to bind a different classes or traits.
|
*/

pest()->extend(TestCase::class)
    ->use(RefreshDatabase::class)
    ->in('Feature');

/*
|--------------------------------------------------------------------------
| Expectations
|--------------------------------------------------------------------------
|
| When you're writing tests, you often need to check that values meet certain conditions. The
| "expect()" function gives you access to a set of "expectations" methods that you can use
| to assert different things. Of course, you may extend the Expectation API at any time.
|
*/

expect()->extend('toBeOne', function () {
    return $this->toBe(1);
});

/*
|--------------------------------------------------------------------------
| Functions
|--------------------------------------------------------------------------
|
| While Pest is very powerful out-of-the-box, you may have some testing code specific to your
| project that you don't want to repeat in every file. Here you can also expose helpers as
| global functions to help you to reduce the number of lines of code in your test files.
|
*/

function testPersistEmployee(array $attributes): Employee
{
    $userId = $attributes['user_id'] ?? null;
    unset($attributes['user_id']);

    $employee = new Employee();
    $employee->fill($attributes);

    if ($userId !== null) {
        $employee->forceFill(['user_id' => $userId]);
    }

    $employee->save();

    return $employee;
}

function testPersistOvertimeSubmission(array $attributes): OvertimeSubmission
{
    $serverControlled = [
        'id',
        'ticket_number',
        'user_id',
        'division_id',
        'attendance_log_id',
        'actual_start_time',
        'actual_end_time',
        'actual_minutes',
        'recognized_minutes',
        'eligibility_status',
        'estimated_hours',
        'status',
        'vp_approver_id',
        'vp_approved_at',
        'vp_notes',
        'hr_verifier_id',
        'hr_verified_at',
        'hr_notes',
    ];

    $business = array_diff_key($attributes, array_flip($serverControlled));
    $server = array_intersect_key($attributes, array_flip($serverControlled));

    $overtime = new OvertimeSubmission();
    $overtime->fill($business);
    $overtime->forceFill($server);
    $overtime->save();

    return $overtime;
}

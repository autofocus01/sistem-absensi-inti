<?php

use App\Http\Requests\StoreOvertimeRequest;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Validator;

uses(RefreshDatabase::class);

test('overtime request requires explicit employee consent', function () {
    $request = StoreOvertimeRequest::create('/overtime/submit', 'POST', [
        'overtime_date' => '2026-09-21',
        'submission_mode' => 'PRE_SUBMITTED',
        'start_time' => '16:30',
        'end_time' => '18:00',
        'reason' => 'Pekerjaan test',
    ]);

    $validator = Validator::make(
        $request->all(),
        $request->rules(),
    );

    expect($validator->fails())->toBeTrue()
        ->and($validator->errors()->has('employee_consent'))->toBeTrue();
});

test('overtime request accepts explicit employee consent', function () {
    $request = StoreOvertimeRequest::create('/overtime/submit', 'POST', [
        'overtime_date' => '2026-09-21',
        'submission_mode' => 'PRE_SUBMITTED',
        'start_time' => '16:30',
        'end_time' => '18:00',
        'reason' => 'Pekerjaan test',
        'employee_consent' => '1',
    ]);

    $validator = Validator::make(
        $request->all(),
        $request->rules(),
    );

    expect($validator->fails())->toBeFalse();
});

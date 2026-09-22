<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreOvertimeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isKaryawan() ?? false;
    }

    public function rules(): array
    {
        return [
            'overtime_date' => [
                'required',
                'date',
            ],

            'submission_mode' => [
                'required',
                Rule::in([
                    'PRE_SUBMITTED',
                    'POST_SUBMITTED',
                ]),
            ],

            'start_time' => [
                'required',
                'date_format:H:i',
            ],

            'end_time' => [
                'required',
                'date_format:H:i',
            ],

            'reason' => [
                'required',
                'string',
                'max:1000',
            ],

            'employee_consent' => [
                'required',
                'accepted',
            ],
        ];
    }
}
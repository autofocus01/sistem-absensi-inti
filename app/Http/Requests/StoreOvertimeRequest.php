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

            /*
             * Dua mode tetap dikenali oleh validator.
             *
             * Keputusan bisnis bahwa lembur reguler hanya
             * boleh POST_SUBMITTED dilakukan di OvertimeController.
             */
            'submission_mode' => [
                'required',
                Rule::in([
                    'PRE_SUBMITTED',
                    'POST_SUBMITTED',
                ]),
            ],

            'category' => [
                'nullable',
                'string',
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

            /*
             * Consent karyawan WAJIB.
             */
            'employee_consent' => [
                'required',
                'accepted',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'employee_consent.required' =>
                'Persetujuan karyawan wajib diberikan.',

            'employee_consent.accepted' =>
                'Persetujuan karyawan harus dicentang.',
        ];
    }
}
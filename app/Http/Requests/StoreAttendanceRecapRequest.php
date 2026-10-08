<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreAttendanceRecapRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isHrAdmin() === true;
    }

    public function rules(): array
    {
        $recapId = $this->route('attendance')?->id;

        return [
            'employee_id' => [
                'required',
                'integer',
                'exists:employees,id',
                Rule::unique('attendance_recaps', 'employee_id')
                    ->where(fn ($query) => $query
                        ->where('tahun', $this->integer('tahun'))
                        ->where('bulan', $this->integer('bulan'))
                    )
                    ->ignore($recapId),
            ],
            'tahun' => ['required', 'integer', 'between:2000,2100'],
            'bulan' => ['required', 'integer', 'between:1,12'],
            'hari_kerja' => ['required', 'integer', 'between:0,31'],
            'hadir' => ['required', 'integer', 'between:0,31'],
            'perdin' => ['required', 'integer', 'between:0,31'],
            'cuti' => ['required', 'integer', 'between:0,31'],
            'sakit' => ['required', 'integer', 'between:0,31'],
            'ijin' => ['required', 'integer', 'between:0,31'],
            'alpha' => ['required', 'integer', 'between:0,31'],
            'telat_hari' => ['required', 'integer', 'between:0,31'],
            'menit_telat' => ['required', 'integer', 'min:0', 'max:7440'],
            'lokasi_bandung' => ['nullable', 'integer', 'between:0,31'],
            'lokasi_jakarta' => ['nullable', 'integer', 'between:0,31'],
            'koreksi_by_admin' => ['nullable', 'integer', 'between:0,31'],
            'resolve_error_id' => ['nullable', 'integer', 'exists:attendance_import_errors,id'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'lokasi_bandung' => $this->input('lokasi_bandung', 0),
            'lokasi_jakarta' => $this->input('lokasi_jakarta', 0),
            'koreksi_by_admin' => $this->input('koreksi_by_admin', 0),
        ]);
    }
}

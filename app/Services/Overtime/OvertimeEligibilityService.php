<?php

namespace App\Services\Overtime;

use App\Models\Employee;

class OvertimeEligibilityService
{
    /**
     * Band yang diperbolehkan mengajukan lembur.
     */
    private const ELIGIBLE_BANDS = [2, 3];

    /**
     * Mengembalikan daftar band yang eligible.
     */
    public function eligibleBandCodes(): array
    {
        return self::ELIGIBLE_BANDS;
    }

    /**
     * Mengecek apakah band boleh mengajukan lembur.
     */
    public function isEligible(?int $bandCode): bool
    {
        return $bandCode !== null
            && in_array($bandCode, self::ELIGIBLE_BANDS, true);
    }

    /**
     * Mengecek apakah employee boleh mengajukan lembur.
     *
     * Band 2 dan 3 diperbolehkan.
     * Band lain atau tanpa band_code tidak diperbolehkan.
     */
    public function canSubmit(Employee $employee): bool
    {
        return $this->isEligible($employee->band_code);
    }

    /**
     * Status eligibility untuk disimpan pada pengajuan.
     */
    public function status(?int $bandCode): string
    {
        return $this->isEligible($bandCode)
            ? 'ELIGIBLE'
            : 'NOT_ELIGIBLE';
    }
}
<?php

namespace App\Services\Overtime;

/**
 * Validates that company overtime limits do not exceed configured legal limits.
 * Null legal limits mean that no legal upper bound is configured by this rule.
 */
class OvertimePolicyConflictValidator
{
    public function validate(array $legal, array $company): array
    {
        $conflicts = [];

        $this->checkUpperBound(
            $conflicts,
            'event',
            $company['maximum_minutes_event'] ?? null,
            $legal['maximum_minutes_daily'] ?? null,
        );

        $this->checkUpperBound(
            $conflicts,
            'monthly',
            $company['maximum_minutes_monthly'] ?? null,
            $legal['maximum_minutes_monthly'] ?? null,
        );

        return [
            'valid' => $conflicts === [],
            'conflicts' => $conflicts,
        ];
    }

    private function checkUpperBound(
        array &$conflicts,
        string $scope,
        mixed $companyMaximum,
        mixed $legalMaximum,
    ): void {
        if ($companyMaximum === null || $legalMaximum === null) {
            return;
        }

        if ((int) $companyMaximum > (int) $legalMaximum) {
            $conflicts[] = [
                'scope' => $scope,
                'company_maximum_minutes' => (int) $companyMaximum,
                'legal_maximum_minutes' => (int) $legalMaximum,
                'message' => "Company {$scope} overtime maximum exceeds legal maximum.",
            ];
        }
    }
}

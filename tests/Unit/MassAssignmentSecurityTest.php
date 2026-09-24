<?php

namespace Tests\Unit;

use App\Models\Employee;
use App\Models\OvertimeSubmission;
use PHPUnit\Framework\TestCase;

class MassAssignmentSecurityTest extends TestCase
{
    public function test_employee_user_id_is_not_mass_assignable(): void
    {
        $employee = new Employee();
        $employee->fill(['nama' => 'Test', 'user_id' => 999]);
        $this->assertFalse($employee->isDirty('user_id'));
        $this->assertSame('Test', $employee->nama);
    }

    public function test_overtime_workflow_fields_are_not_mass_assignable(): void
    {
        $overtime = new OvertimeSubmission();
        $overtime->fill([
            'reason' => 'Test',
            'user_id' => 999,
            'division_id' => 999,
            'status' => 'VERIFIED_HR',
            'vp_approver_id' => 999,
            'hr_verifier_id' => 999,
        ]);

        $this->assertSame('Test', $overtime->reason);
        $this->assertFalse($overtime->isDirty('user_id'));
        $this->assertFalse($overtime->isDirty('division_id'));
        $this->assertFalse($overtime->isDirty('status'));
        $this->assertFalse($overtime->isDirty('vp_approver_id'));
        $this->assertFalse($overtime->isDirty('hr_verifier_id'));
    }
}

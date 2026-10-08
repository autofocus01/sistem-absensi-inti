<?php

namespace Database\Seeders;

use App\Models\AttendanceLog;
use App\Models\AttendanceRecap;
use App\Models\AttendanceAnomaly;
use App\Models\AuditLog;
use App\Models\OvertimeSubmission;
use App\Models\OvertimePolicySnapshot;
use App\Models\Employee;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DemoScenarioSeeder extends Seeder
{
    public function run(): void
    {
        if (app()->environment('production')) {
            $this->command?->error('demo:seed ditolak pada production.');
            return;
        }

        $this->call([
            OvertimePolicySeeder::class,
            DivisionSeeder::class,
            DummyEmployeeSeeder::class,
            UatAccountSeeder::class,
        ]);

        $employee = Employee::query()
            ->where('nipeg', '90010001')
            ->firstOrFail();
        $user = User::query()->where('email', config('demo.employee_email'))->firstOrFail();

        $overtimeIds = OvertimeSubmission::query()->where('user_id', $user->id)->pluck('id');
        if ($overtimeIds->isNotEmpty()) {
            OvertimePolicySnapshot::query()->whereIn('overtime_submission_id', $overtimeIds)->delete();
        }
        OvertimeSubmission::query()->where('user_id', $user->id)->delete();
        AttendanceAnomaly::query()->where('employee_id', $employee->id)->delete();
        AttendanceRecap::query()->where('employee_id', $employee->id)->delete();
        AttendanceLog::query()->where('employee_id', $employee->id)->delete();

        $this->command?->info('Demo siap: Ahmad Fauzan / Band 2, VP, HR, dan Direktur tersedia.');
        $this->command?->info('Password seluruh akun UAT: password');
        $this->command?->info('Skenario: Demo Mode 07:30 -> Clock In -> 18:00 -> Clock Out -> Ajukan Lembur -> VP Approve -> HR Verify.');
    }
}

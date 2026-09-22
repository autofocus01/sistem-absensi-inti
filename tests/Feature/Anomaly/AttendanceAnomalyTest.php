<?php

use App\Models\AttendanceAnomaly;
use App\Models\AttendanceLog;
use App\Models\Division;
use App\Models\Employee;
use App\Models\User;
use App\Services\Attendance\AttendanceAnomalyService;
use Carbon\Carbon;

function anomalyEmployee(): Employee
{
    $division = Division::create(['nama' => 'Anomaly Test '.uniqid(), 'lini' => 'Test']);
    return Employee::create(['nipeg' => 'AN'.uniqid(), 'nama' => 'Anomaly Test', 'jabatan' => 'Staff', 'division_id' => $division->id]);
}

it('detects invalid attendance time order', function () {
    $employee = anomalyEmployee();
    AttendanceLog::create(['employee_id'=>$employee->id,'tanggal'=>'2026-09-21','jam_masuk'=>'08:00:00','jam_pulang'=>'07:30:00','sumber'=>'manual','jumlah_scan'=>2]);
    $found = app(AttendanceAnomalyService::class)->sync('2026-09-21','2026-09-21');
    expect(collect($found)->pluck('type'))->toContain('INVALID_TIME_ORDER');
    expect(AttendanceAnomaly::where('type','INVALID_TIME_ORDER')->exists())->toBeTrue();
});

it('allows hr to resolve an anomaly', function () {
    $employee = anomalyEmployee();
    $log = AttendanceLog::create(['employee_id'=>$employee->id,'tanggal'=>'2026-09-21','jam_masuk'=>'08:00:00','jam_pulang'=>'07:30:00','sumber'=>'manual','jumlah_scan'=>2]);
    app(AttendanceAnomalyService::class)->sync('2026-09-21','2026-09-21');
    $anomaly = AttendanceAnomaly::firstOrFail();
    $hr = User::factory()->create(['role'=>'hr_admin']);
    $this->actingAs($hr)->patch(route('attendance.anomalies.resolve',$anomaly), ['status'=>'RESOLVED','resolution_notes'=>'Dikoreksi HR'])->assertRedirect();
    expect($anomaly->fresh()->status)->toBe('RESOLVED');
});

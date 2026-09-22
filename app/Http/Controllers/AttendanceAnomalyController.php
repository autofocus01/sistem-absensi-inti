<?php

namespace App\Http\Controllers;

use App\Models\AttendanceAnomaly;
use App\Services\Attendance\AttendanceAnomalyService;
use App\Services\Audit\AuditLogService;
use Illuminate\Http\Request;

class AttendanceAnomalyController extends Controller
{
    public function __construct(private readonly AttendanceAnomalyService $anomalies, private readonly AuditLogService $audit) {}

    public function index(Request $request)
    {
        abort_unless($request->user()?->isHrAdmin(), 403);
        if ($request->boolean('scan')) {
            $candidates = $this->anomalies->sync($request->input('from'), $request->input('until'));
            $this->audit->record(
                'ATTENDANCE_ANOMALY_SCAN',
                null,
                null,
                ['count' => count($candidates), 'from' => $request->input('from'), 'until' => $request->input('until')],
                'Scan anomali presensi/lembur dijalankan oleh HR.',
                $request
            );
        }
        $query = AttendanceAnomaly::query()->with(['employee','resolver'])->latest();
        if ($request->filled('status')) $query->where('status', $request->input('status'));
        if ($request->filled('severity')) $query->where('severity', $request->input('severity'));
        if ($request->filled('type')) $query->where('type', $request->input('type'));
        return view('attendance.anomalies.index', ['anomalies'=>$query->paginate(25)->withQueryString(), 'filters'=>$request->only(['status','severity','type','from','until'])]);
    }

    public function resolve(Request $request, AttendanceAnomaly $anomaly)
    {
        abort_unless($request->user()?->isHrAdmin(), 403);
        $validated = $request->validate(['status'=>['required','in:REVIEWED,RESOLVED,IGNORED'],'resolution_notes'=>['nullable','string','max:1000']]);
        $before = $anomaly->toArray();
        $updated = $this->anomalies->resolve($anomaly, $request->user()->id, $validated['status'], $validated['resolution_notes'] ?? null);
        $this->audit->record('ATTENDANCE_ANOMALY_RESOLVED', $updated, $before, $updated->toArray(), 'Status anomaly diubah oleh HR.', $request);
        return back()->with('status', 'Anomali diperbarui.');
    }
}

<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreOvertimeRequest;
use App\Models\AttendanceLog;
use App\Models\OvertimeSubmission;
use App\Services\Overtime\OvertimeCalculationService;
use App\Services\Overtime\OvertimeLimitService;
use App\Services\Audit\AuditLogService;
use App\Services\Overtime\OvertimePolicyResolver;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OvertimeController extends Controller
{
    private const EMPLOYEE_CONSENT_TEXT =
        'Saya menyatakan bahwa data pengajuan lembur ini benar dan saya memberikan persetujuan untuk diproses melalui alur Karyawan, VP Divisi, dan HR sesuai kebijakan yang berlaku.';
    public function __construct(
        private readonly OvertimePolicyResolver $policyResolver,
        private readonly OvertimeCalculationService $calculator,
        private readonly OvertimeLimitService $limitService,
        private readonly AuditLogService $audit,
    ) {
    }

    /**
     * KARYAWAN
     *
     * Membuat pengajuan lembur.
     *
     * Semua operasi yang menentukan duplicate/limit dilakukan dalam
     * transaction dan employee row dikunci agar dua request bersamaan
     * tidak dapat melewati pemeriksaan yang sama.
     */
    public function store(StoreOvertimeRequest $request)
    {
        $user = $request->user();

        abort_unless($user?->isKaryawan(), 403);

        $employee = $user->employee;

        if (! $employee) {
            return response()->json([
                'message' => 'Akun belum terhubung ke data karyawan.',
            ], 422);
        }

        if (! $employee->division_id) {
            return response()->json([
                'message' => 'Data karyawan belum memiliki division.',
            ], 422);
        }

        $date = Carbon::parse($request->overtime_date);
        $mode = $request->submission_mode;

        return DB::transaction(function () use (
            $request,
            $user,
            $employee,
            $date,
            $mode
        ) {
            /*
             * Lock employee row as the serialization point for OT submission
             * by this employee. This closes the check-then-insert race.
             */
            $employee = $employee->newQuery()
                ->whereKey($employee->id)
                ->lockForUpdate()
                ->firstOrFail();

            $attendance = AttendanceLog::query()
                ->where('employee_id', $employee->id)
                ->whereDate('tanggal', $date)
                ->lockForUpdate()
                ->first();

            /*
             * Day type is authoritative from HolidayCalendarService via
             * OvertimePolicyResolver. Never trust client holiday flags.
             */
            $policy = $this->policyResolver->resolve($date);

            $duplicateExists = OvertimeSubmission::query()
                ->where('user_id', $user->id)
                ->whereDate('overtime_date', $date)
                ->whereNotIn('status', [
                    'REJECTED_VP',
                    'REJECTED_HR',
                ])
                ->exists();

            if ($duplicateExists) {
                return response()->json([
                    'message' =>
                        'Sudah ada pengajuan lembur aktif untuk tanggal tersebut. ' .
                        'Selesaikan atau tunggu hasil pengajuan sebelumnya sebelum ' .
                        'membuat pengajuan baru.',
                ], 422);
            }

            if (
                $mode === 'POST_SUBMITTED'
                && ! $policy['allows_post_submission']
            ) {
                return response()->json([
                    'message' =>
                        'Pengajuan setelah Clock-out tidak diizinkan ' .
                        'oleh kebijakan yang berlaku.',
                ], 422);
            }

            if (
                $mode === 'POST_SUBMITTED'
                && ! $attendance?->jam_pulang
            ) {
                return response()->json([
                    'message' =>
                        'Pengajuan post-submission membutuhkan Clock-out ' .
                        'yang sudah tercatat.',
                ], 422);
            }

            $start = Carbon::parse(
                $date->format('Y-m-d') . ' ' . $request->start_time
            );

            $end = Carbon::parse(
                $date->format('Y-m-d') . ' ' . $request->end_time
            );

            /*
             * PT INTI uses a fixed 07:30-16:30 schedule with no shifts.
             * A same-date end <= start is therefore an invalid request,
             * not an overnight shift.
             */
            if ($end->lessThanOrEqualTo($start)) {
                return response()->json([
                    'message' => 'Jam selesai lembur harus lebih besar dari jam mulai pada tanggal yang sama. Sistem tidak menggunakan pola shift/overnight.',
                ], 422);
            }

            $actualStart = $start;

            $actualEnd = $mode === 'POST_SUBMITTED'
                ? Carbon::parse(
                    $date->format('Y-m-d') . ' ' . $attendance->jam_pulang
                )
                : $end;

            $calculation = $this->calculator->calculate(
                $date,
                $actualStart,
                $actualEnd,
                $policy
            );

            /*
             * Hard enforcement of configured legal/company aggregate limits.
             * A rejected limit check creates no submission.
             */
            $limitCheck = $this->limitService->validate(
                $employee->id,
                $date,
                (int) $calculation['recognized_minutes'],
                $policy
            );

            if (! $limitCheck['allowed']) {
                return response()->json([
                    'message' =>
                        'Pengajuan lembur melebihi batas lembur yang berlaku.',
                    'limit_check' => $limitCheck,
                ], 422);
            }

            $overtime = OvertimeSubmission::create([
                'user_id' => $user->id,
                'division_id' => $employee->division_id,
                'overtime_date' => $date->toDateString(),
                'start_time' => $request->start_time,
                'end_time' => $request->end_time,
                'estimated_hours' => round(
                    $start->diffInMinutes($end) / 60,
                    2
                ),
                'category' =>
                    $calculation['day_type'] === 'NATIONAL_HOLIDAY'
                        ? 'hari_libur_nasional'
                        : (
                            $calculation['day_type'] === 'WEEKEND'
                                ? 'akhir_pekan'
                                : 'hari_kerja'
                        ),
                'reason' => $request->reason,
                'employee_consent' => true,
                'employee_consent_at' => now(),
                'employee_consent_ip' => $request->ip(),
                'employee_consent_user_agent' => $request->userAgent(),
                'employee_consent_source' => 'WEB_FORM',
                'employee_consent_version' => '1.0',
                'employee_consent_text' => self::EMPLOYEE_CONSENT_TEXT,
                'submission_mode' => $mode,
                'attendance_log_id' => $attendance?->id,
                'actual_start_time' => $actualStart->format('H:i:s'),
                'actual_end_time' => $actualEnd->format('H:i:s'),
                'actual_minutes' => $calculation['actual_minutes'],
                'recognized_minutes' => $calculation['recognized_minutes'],
                'eligibility_status' => $calculation['eligibility'],
                'status' => 'PENDING_VP',
            ]);

            DB::table('overtime_policy_snapshots')->insert([
                'overtime_submission_id' => $overtime->id,
                'legal_rule_id' => $policy['legal_rule_id'],
                'company_rule_id' => $policy['company_rule_id'],
                'policy' => json_encode($policy),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $this->audit->record('OT_SUBMITTED', $overtime, null, $overtime->toArray(), 'Pengajuan lembur dibuat.');

            return response()->json([
                'success' => true,
                'message' => 'Pengajuan lembur berhasil dikirim ke VP Divisi.',
                'data' => $overtime->load('division'),
                'calculation' => $calculation,
                'limit_check' => $limitCheck,
            ], 201);
        });
    }

    /**
     * KARYAWAN
     *
     * Riwayat seluruh pengajuan lembur milik user yang sedang login.
     * Data dibatasi berdasarkan user_id agar karyawan tidak dapat melihat
     * pengajuan milik karyawan lain.
     */
    public function employeeHistory(Request $request)
    {
        $user = $request->user();

        abort_unless($user?->isKaryawan(), 403);

        $submissions = OvertimeSubmission::query()
            ->with([
                'division',
                'vpApprover',
                'hrVerifier',
            ])
            ->where('user_id', $user->id)
            ->latest('created_at')
            ->paginate(20)
            ->withQueryString();

        return view('overtime.history', compact('submissions'));
    }

    /**
     * VP
     *
     * Riwayat keputusan yang dibuat oleh VP yang sedang login.
     * Filter memakai vp_approver_id, bukan hanya division_id, sehingga
     * halaman ini hanya menampilkan keputusan yang benar-benar dibuat
     * oleh VP tersebut.
     */
    public function vpHistory(Request $request)
    {
        $vp = $request->user();

        abort_unless($vp?->isVp(), 403);

        $vpEmployee = $vp->employee;

        abort_unless(
            $vpEmployee && $vpEmployee->division_id,
            403
        );

        $submissions = OvertimeSubmission::query()
            ->with([
                'user.employee',
                'division',
                'hrVerifier',
            ])
            ->where('division_id', $vpEmployee->division_id)
            ->where('vp_approver_id', $vp->id)
            ->whereNotNull('vp_approved_at')
            ->latest('vp_approved_at')
            ->paginate(20)
            ->withQueryString();

        return view('overtime.vp-history', compact('submissions'));
    }

    public function vpPendingList(Request $request)
    {
        $vp = $request->user();

        abort_unless(
            $vp?->isVp(),
            403
        );

        $employee = $vp->employee;

        /*
         * FAIL CLOSED.
         *
         * VP tanpa employee/division tidak boleh melihat
         * daftar pengajuan sama sekali.
         */
        abort_unless(
            $employee && $employee->division_id,
            403
        );

        $query = OvertimeSubmission::query()
            ->with([
                'user.employee.division',
                'division',
                'attendanceLog',
            ])
            ->where(
                'status',
                'PENDING_VP'
            )
            ->where(
                'division_id',
                $employee->division_id
            );

        return response()->json([
            'data' => $query
                ->latest()
                ->get(),
        ]);
    }

    /**
     * VP
     *
     * APPROVE / REJECT.
     *
     * Hanya:
     *
     * PENDING_VP
     *       ↓
     * APPROVED_VP
     *
     * atau
     *
     * PENDING_VP
     *       ↓
     * REJECTED_VP
     */
    public function vpApprove(
        Request $request,
        string $id
    ) {
        $request->validate([
            'action' =>
                'required|in:APPROVE,REJECT',

            'notes' =>
                'nullable|string|max:255',
        ]);

        $vp = $request->user();

        abort_unless(
            $vp?->isVp(),
            403
        );

        $vpEmployee = $vp->employee;

        abort_unless(
            $vpEmployee && $vpEmployee->division_id,
            403
        );

        /*
         * Transaction + row lock mencegah dua request simultan
         * memproses submission yang sama.
         */
        $overtime = DB::transaction(
            function () use (
                $id,
                $vp,
                $vpEmployee,
                $request
            ) {
                $overtime = OvertimeSubmission::query()
                    ->lockForUpdate()
                    ->findOrFail($id);

                /*
                 * HARD DIVISION BOUNDARY.
                 *
                 * Tidak cukup percaya division_id dari request.
                 * Bandingkan dengan division milik VP yang sedang login.
                 */
                abort_unless(
                    (int) $vpEmployee->division_id
                        === (int) $overtime->division_id,
                    403
                );

                /*
                 * HARD STATE BOUNDARY.
                 *
                 * VP hanya boleh memproses submission
                 * yang masih berada di PENDING_VP.
                 */
                if (
                    $overtime->status
                    !== 'PENDING_VP'
                ) {
                    abort(
                        409,
                        'Pengajuan sudah diproses sebelumnya dan tidak dapat diproses ulang.'
                    );
                }

                $approved =
                    $request->action === 'APPROVE';

                if (
                    $approved
                    && (
                        $overtime->eligibility_status !== 'ELIGIBLE'
                        || (int) $overtime->recognized_minutes <= 0
                    )
                ) {
                    abort(422, 'Pengajuan lembur tidak memenuhi syarat berdasarkan presensi aktual dan tidak dapat disetujui VP.');
                }

                $before = $overtime->toArray();
                $overtime->update([
                    'status' =>
                        $approved
                            ? 'APPROVED_VP'
                            : 'REJECTED_VP',

                    'vp_approver_id' =>
                        $vp->id,

                    'vp_approved_at' =>
                        now(),

                    'vp_notes' =>
                        $request->notes,
                ]);

                $fresh = $overtime->fresh();
                $this->audit->record($approved ? 'VP_APPROVED' : 'VP_REJECTED', $fresh, $before, $fresh->toArray(), $request->notes);
                return $fresh;
            }
        );

        return response()->json([
            'success' => true,

            'data' => $overtime,
        ]);
    }

    /**
     * HR
     *
     * Hanya melihat submission yang sudah APPROVED_VP.
     */
    public function hrPendingList(Request $request)
    {
        $hr = $request->user();

        abort_unless(
            $hr?->isHrAdmin(),
            403
        );

        return response()->json([
            'success' => true,

            'data' => OvertimeSubmission::query()
                ->with([
                    'user.employee.division',
                    'division',
                    'vpApprover',
                    'attendanceLog',
                ])
                ->where(
                    'status',
                    'APPROVED_VP'
                )
                ->latest()
                ->get(),
        ]);
    }

    /**
     * HR
     *
     * VERIFY / REJECT.
     *
     * APPROVED_VP
     *       ↓
     * VERIFIED_HR
     *
     * atau
     *
     * APPROVED_VP
     *       ↓
     * REJECTED_HR
     */
    public function hrVerify(
        Request $request,
        string $id
    ) {
        $request->validate([
            'action' =>
                'required|in:VERIFY,REJECT',

            'notes' =>
                'nullable|string|max:255',
        ]);

        $hr = $request->user();

        abort_unless(
            $hr?->isHrAdmin(),
            403
        );

        $overtime = DB::transaction(
            function () use (
                $id,
                $hr,
                $request
            ) {
                /*
                 * Lock submission selama proses verifikasi.
                 */
                $overtime = OvertimeSubmission::query()
                    ->lockForUpdate()
                    ->findOrFail($id);

                /*
                 * HARD STATE BOUNDARY.
                 *
                 * HR hanya dapat memproses APPROVED_VP.
                 */
                if (
                    $overtime->status
                    !== 'APPROVED_VP'
                ) {
                    abort(
                        409,
                        'Pengajuan belum berada pada tahap verifikasi HR atau sudah diproses sebelumnya.'
                    );
                }

                $verified =
                    $request->action === 'VERIFY';

                if (
                    $verified
                    && (
                        $overtime->eligibility_status !== 'ELIGIBLE'
                        || (int) $overtime->recognized_minutes <= 0
                    )
                ) {
                    abort(422, 'Pengajuan lembur tidak memenuhi syarat berdasarkan presensi aktual dan tidak dapat diverifikasi HR.');
                }

                $before = $overtime->toArray();
                $overtime->update([
                    'status' =>
                        $verified
                            ? 'VERIFIED_HR'
                            : 'REJECTED_HR',

                    'hr_verifier_id' =>
                        $hr->id,

                    'hr_verified_at' =>
                        now(),

                    'hr_notes' =>
                        $request->notes,
                ]);

                $fresh = $overtime->fresh();
                $this->audit->record($verified ? 'HR_VERIFIED' : 'HR_REJECTED', $fresh, $before, $fresh->toArray(), $request->notes);
                return $fresh;
            }
        );

        return response()->json([
            'success' => true,

            'data' => $overtime,
        ]);
    }
}
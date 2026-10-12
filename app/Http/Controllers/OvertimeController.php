<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreOvertimeRequest;
use App\Models\AttendanceLog;
use App\Models\OvertimeSubmission;
use App\Services\Audit\AuditLogService;
use App\Services\Demo\DemoClock;
use App\Services\Overtime\OvertimeCalculationService;
use App\Services\Overtime\OvertimeEligibilityService;
use App\Services\Overtime\OvertimeLimitService;
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
        private readonly OvertimeEligibilityService $eligibilityService,
        private readonly OvertimeLimitService $limitService,
        private readonly AuditLogService $audit,
        private readonly DemoClock $clock,
    ) {
    }

    public function create(Request $request)
    {
        $user = $request->user();
        abort_unless($user?->isKaryawan(), 403);

        $employee = $user->employee;
        abort_unless($employee, 403, 'Akun belum terhubung ke data karyawan.');

        $bandCode = $employee->band_code;
        $eligible = $this->eligibilityService->isEligible($bandCode);
        abort_unless(
            $eligible,
            403,
            'Pengajuan lembur hanya tersedia untuk Band Code 2 atau 3.'
        );

        // AttendanceLog adalah sumber kebenaran. Jangan gunakan nilai waktu
        // atau hasil kalkulasi lama dari session sebagai evidence terbaru.
        $sessionDraft = session('overtime.draft');
        $date = isset($sessionDraft['date'])
            ? Carbon::parse($sessionDraft['date'])
            : $this->clock->now();

        $attendance = AttendanceLog::query()
            ->where('employee_id', $employee->id)
            ->whereDate('tanggal', $date->toDateString())
            ->first();

        if (! $attendance?->jam_masuk || ! $attendance?->jam_pulang) {
            session()->forget('overtime.draft');

            return view('overtime.index', [
                'employee' => $employee->loadMissing('division'),
                'bandCode' => $bandCode,
                'overtimeEligible' => true,
                'overtimeAvailable' => false,
                'overtimeDraft' => null,
                'availabilityMessage' => 'Pengajuan lembur hanya tersedia setelah Clock-in dan Clock-out tercatat.',
            ]);
        }

        $policy = $this->policyResolver->resolve($date);
        $dateString = $date->toDateString();
        $clockIn = Carbon::parse($dateString . ' ' . $attendance->jam_masuk);
        $clockOut = Carbon::parse($dateString . ' ' . $attendance->jam_pulang);
        $normalEnd = Carbon::parse($dateString . ' ' . ($policy['normal_end'] ?? '16:30:00'));
        $actualWorkMinutes = max(0, (int) $clockIn->diffInMinutes($clockOut));
        $isWorkday = ($policy['day_type'] ?? 'WORKDAY') === 'WORKDAY';

        // Weekday: perlu melewati 16:30 dan memenuhi minimal 8 jam aktual.
        // Weekend/libur: aturan minimum mengikuti policy, tanpa gate 8 jam.
        $available = $isWorkday
            ? $clockOut->greaterThan($normalEnd) && $actualWorkMinutes >= 480
            : $actualWorkMinutes >= (int) ($policy['minimum_minutes'] ?? 30);

        if (! $available) {
            session()->forget('overtime.draft');

            return view('overtime.index', [
                'employee' => $employee->loadMissing('division'),
                'bandCode' => $bandCode,
                'overtimeEligible' => true,
                'overtimeAvailable' => false,
                'overtimeDraft' => null,
                'availabilityMessage' => $isWorkday && ! $clockOut->greaterThan($normalEnd)
                    ? 'Clock-out pada atau sebelum 16:30 tidak menghasilkan waktu lembur hari kerja.'
                    : 'Presensi hari ini belum memenuhi prasyarat pengajuan lembur.',
            ]);
        }

        $startTime = $isWorkday ? $normalEnd->format('H:i') : $clockIn->format('H:i');
        $previewStart = $isWorkday ? $normalEnd->copy() : $clockIn->copy();
        $previewCalculation = $this->calculator->calculate(
            $date,
            $previewStart,
            $clockOut->copy(),
            $policy
        );

        // Selalu membangun ulang draft dari evidence terkini. Sebelumnya,
        // session draft lama dapat mempertahankan nilai OT 0 walau Clock-out
        // sudah berubah menjadi 18:00 atau lebih.
        $draft = [
            'date' => $dateString,
            'clock_in' => $attendance->jam_masuk,
            'clock_out' => $attendance->jam_pulang,
            'band_code' => (int) $bandCode,
            'day_type' => $policy['day_type'] ?? 'WORKDAY',
            'start_time' => $startTime,
            'end_time' => $clockOut->format('H:i'),
            'actual_work_minutes' => $actualWorkMinutes,
            'minimum_minutes' => (int) ($policy['minimum_minutes'] ?? 30),
            'maximum_minutes_event' => (int) ($policy['maximum_minutes_event'] ?? 0),
            'actual_overtime_minutes' => (int) ($previewCalculation['actual_minutes'] ?? 0),
            'rounded_overtime_minutes' => (int) ($previewCalculation['rounded_minutes'] ?? 0),
            'recognized_overtime_minutes' => (int) ($previewCalculation['recognized_minutes'] ?? 0),
            'excess_minutes' => (int) ($previewCalculation['excess_minutes'] ?? 0),
        ];

        session(['overtime.draft' => $draft]);

        return view('overtime.index', [
            'employee' => $employee->loadMissing('division'),
            'bandCode' => $bandCode,
            'overtimeEligible' => true,
            'overtimeAvailable' => true,
            'overtimeDraft' => $draft,
            'availabilityMessage' => null,
        ]);
    }

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

        /*
         * Eligibility berdasarkan master Employee.
         *
         * HANYA Band 2 dan Band 3 yang boleh membuat
         * pengajuan lembur.
         */
        if (! $this->eligibilityService->isEligible(
            $employee->band_code
        )) {
            return response()->json([
                'message' => 'Karyawan dengan Band Code ' .
                    ($employee->band_code ?? 'NULL') .
                    ' tidak memiliki hak untuk mengajukan lembur.',
                'eligibility_status' => 'NOT_ELIGIBLE',
                'band_code' => $employee->band_code,
                'eligible_band_codes' =>
                    $this->eligibilityService->eligibleBandCodes(),
            ], 422);
        }

        $date = Carbon::parse($request->overtime_date);
        $mode = $request->submission_mode;

        // Lembur reguler hanya boleh diajukan SETELAH Clock-out.
        // PRE_SUBMITTED tidak lagi menjadi jalur pengajuan karyawan.
        if ($mode !== 'POST_SUBMITTED') {
            return response()->json([
                'message' => 'Pengajuan lembur hanya dapat dilakukan setelah Clock-out.',
            ], 422);
        }

        return DB::transaction(
            function () use (
                $request,
                $user,
                $employee,
                $date,
                $mode
            ) {
                /*
                 * Lock employee agar data master tidak berubah
                 * di tengah proses pengajuan.
                 */
                $employee = $employee
                    ->newQuery()
                    ->whereKey($employee->id)
                    ->lockForUpdate()
                    ->firstOrFail();

                /*
                 * Cek ulang eligibility setelah lock.
                 *
                 * Ini mencegah bypass apabila band berubah
                 * setelah pemeriksaan awal.
                 */
                if (! $this->eligibilityService->isEligible(
                    $employee->band_code
                )) {
                    return response()->json([
                        'message' =>
                            'Band Code karyawan tidak memenuhi syarat pengajuan lembur.',
                        'eligibility_status' => 'NOT_ELIGIBLE',
                        'band_code' => $employee->band_code,
                    ], 422);
                }

                $attendance = AttendanceLog::query()
                    ->where('employee_id', $employee->id)
                    ->whereDate('tanggal', $date)
                    ->lockForUpdate()
                    ->first();

                $policy = $this->policyResolver->resolve($date);

                /*
                 * Syarat presensi untuk lembur reguler:
                 * 1. Clock-in ada
                 * 2. Clock-out ada
                 * 3. Durasi kerja minimal 8 jam (480 menit)
                 *
                 * Jam setelah 16:30 tetap merupakan bagian dari jam kerja
                 * aktual di AttendanceLog. Baru setelah karyawan Clock-out
                 * dan mengajukan lembur, kelebihan waktu di atas jam normal
                 * dihitung sebagai lembur.
                 */
                if (! $attendance?->jam_masuk) {
                    return response()->json([
                        'message' => 'Clock-in pada tanggal tersebut belum tercatat. Lakukan Clock-in terlebih dahulu.',
                    ], 422);
                }

                if (! $attendance?->jam_pulang) {
                    return response()->json([
                        'message' => 'Clock-out pada tanggal tersebut belum tercatat. Pengajuan lembur hanya dapat dilakukan setelah Clock-out.',
                    ], 422);
                }

                $durasiKerjaMenit = $attendance->durasiKerjaMenit();

                // Minimum 8 jam hanya berlaku pada hari kerja. Weekend/libur
                // tidak memakai gate 8 jam; durasi aktual menjadi evidence utama.
                if (
                    ($policy['day_type'] ?? 'WORKDAY') === 'WORKDAY'
                    && ($durasiKerjaMenit === null || $durasiKerjaMenit < 480)
                ) {
                    return response()->json([
                        'message' => 'Pengajuan lembur ditolak. Presensi hari kerja belum memenuhi minimal 8 jam kerja.',
                        'attendance' => [
                            'jam_masuk' => $attendance->jam_masuk,
                            'jam_pulang' => $attendance->jam_pulang,
                            'durasi_kerja_menit' => $durasiKerjaMenit,
                            'minimum_menit' => 480,
                            'day_type' => $policy['day_type'] ?? 'WORKDAY',
                        ],
                    ], 422);
                }

                /*
                 * Satu pengajuan aktif per karyawan per tanggal.
                 */
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
                            'Selesaikan atau tunggu hasil pengajuan sebelumnya ' .
                            'sebelum membuat pengajuan baru.',
                    ], 422);
                }

                if (! ($policy['allows_post_submission'] ?? false)) {
                    return response()->json([
                        'message' => 'Pengajuan lembur setelah Clock-out tidak diizinkan oleh kebijakan yang berlaku.',
                    ], 422);
                }

                $start = Carbon::parse(
                    $date->format('Y-m-d') .
                    ' ' .
                    $request->start_time
                );

                $end = Carbon::parse(
                    $date->format('Y-m-d') .
                    ' ' .
                    $request->end_time
                );

                if ($end->lessThanOrEqualTo($start)) {
                    return response()->json([
                        'message' =>
                            'Jam selesai lembur harus lebih besar ' .
                            'dari jam mulai pada tanggal yang sama. ' .
                            'Sistem tidak menggunakan pola shift/overnight.',
                    ], 422);
                }

                /*
                 * Untuk pengajuan setelah Clock-out, waktu lembur yang
                 * diakui berasal dari kelebihan waktu setelah jam kerja normal
                 * sampai Clock-out aktual.
                 *
                 * Contoh:
                 * 07:30 -> 17:17 = jam kerja aktual 9j47m
                 * 16:30 -> 17:17 = lembur 47m
                 * recognized setelah pembulatan 30m.
                 */
                $normalEndTime = $policy['normal_end'] ?? '16:30:00';

                $actualStart = ($policy['day_type'] ?? 'WORKDAY') === 'WORKDAY'
                    ? Carbon::parse($date->format('Y-m-d') . ' ' . $normalEndTime)
                    : Carbon::parse($date->format('Y-m-d') . ' ' . $attendance->jam_masuk);

                $actualEnd = Carbon::parse(
                    $date->format('Y-m-d') . ' ' . $attendance->jam_pulang
                );

                if (
                    ($policy['day_type'] ?? 'WORKDAY') === 'WORKDAY'
                    && $actualEnd->lessThanOrEqualTo($actualStart)
                ) {
                    return response()->json([
                        'message' => 'Clock-out tidak melewati jam kerja normal. Tidak terdapat waktu lembur yang dapat diajukan.',
                    ], 422);
                }

                /*
                 * Evidence presensi adalah sumber kebenaran.
                 *
                 * Request tidak boleh memilih interval lembur yang berbeda
                 * dari interval yang dibuktikan oleh AttendanceLog. Jika
                 * dibiarkan, estimated_hours dapat 30 menit sementara
                 * recognized_minutes dihitung dari 60 menit evidence aktual.
                 */
                if (
                    $start->format('H:i') !== $actualStart->format('H:i')
                    || $end->format('H:i') !== $actualEnd->format('H:i')
                ) {
                    return response()->json([
                        'message' => 'Rentang waktu lembur harus sama dengan evidence presensi aktual: ' .
                            $actualStart->format('H:i') . ' - ' .
                            $actualEnd->format('H:i') . '.',
                        'attendance' => [
                            'jam_masuk' => $attendance->jam_masuk,
                            'jam_pulang' => $attendance->jam_pulang,
                            'overtime_start' => $actualStart->format('H:i'),
                            'overtime_end' => $actualEnd->format('H:i'),
                        ],
                    ], 422);
                }

                /*
                 * Setelah evidence tervalidasi, gunakan nilai server-side
                 * untuk persistence dan calculation. Jangan gunakan kembali
                 * input client sebagai sumber durasi.
                 */
                $start = $actualStart->copy();
                $end = $actualEnd->copy();

                $calculation = $this->calculator->calculate(
                    $date,
                    $actualStart,
                    $actualEnd,
                    $policy
                );

                /*
                 * Maximum event adalah CAP OT yang diakui, bukan hard reject.
                 * Jika actual OT melebihi batas, bagian sampai batas maksimum
                 * tetap dapat diajukan. Selisihnya disimpan sebagai excess dan
                 * tidak ikut quota karena quota selalu memakai recognized_minutes.
                 */
                if (
                    $calculation['eligibility'] !== 'ELIGIBLE'
                    || (int) $calculation['recognized_minutes'] <= 0
                ) {
                    return response()->json([
                        'message' => 'Interval presensi aktual belum memenuhi minimum lembur yang berlaku.',
                        'calculation' => $calculation,
                    ], 422);
                }

                /*
                 * Band 2/3 eligible mengajukan,
                 * tetapi hasil calculation tetap menentukan
                 * apakah ada recognized overtime.
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

                $overtime = new OvertimeSubmission();

                $overtime->forceFill([
                    'user_id' => $user->id,
                    'division_id' => $employee->division_id,

                    'overtime_date' => $date->toDateString(),

                    'start_time' => $start->format('H:i:s'),
                    'end_time' => $end->format('H:i:s'),

                    'estimated_hours' =>
                        round(
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

                    /*
                     * Consent sudah dipastikan oleh
                     * StoreOvertimeRequest dengan rule accepted.
                     */
                    'employee_consent' => true,
                    'employee_consent_at' => now(),
                    'employee_consent_ip' => $request->ip(),
                    'employee_consent_user_agent' =>
                        $request->userAgent(),
                    'employee_consent_source' => 'WEB_FORM',
                    'employee_consent_version' => '1.0',
                    'employee_consent_text' =>
                        self::EMPLOYEE_CONSENT_TEXT,

                    'submission_mode' => $mode,

                    'attendance_log_id' =>
                        $attendance?->id,

                    'actual_start_time' =>
                        $actualStart->format('H:i:s'),

                    'actual_end_time' =>
                        $actualEnd->format('H:i:s'),

                    'actual_minutes' =>
                        $calculation['actual_minutes'],

                    'recognized_minutes' =>
                        $calculation['recognized_minutes'],

                    'rounded_minutes' =>
                        $calculation['rounded_minutes'],

                    'excess_minutes' =>
                        $calculation['excess_minutes'],

                    'quota_reserved_minutes' =>
                        $calculation['recognized_minutes'],

                    'anomaly_codes' =>
                        $calculation['excess_minutes'] > 0
                            ? ['EXCESS_OVERTIME']
                            : null,

                    /*
                     * Eligibility berasal dari hasil calculation.
                     */
                    'eligibility_status' => $calculation['eligibility'],

                    'status' => 'PENDING_VP',
                ])->save();

                DB::table('overtime_policy_snapshots')->insert([
                    'overtime_submission_id' => $overtime->id,
                    'legal_rule_id' =>
                        $policy['legal_rule_id'],
                    'company_rule_id' =>
                        $policy['company_rule_id'],
                    'policy' => json_encode($policy),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                $this->audit->record(
                    'OT_SUBMITTED',
                    $overtime,
                    null,
                    $overtime->toArray(),
                    'Pengajuan lembur dibuat.'
                );

                return response()->json([
                    'success' => true,
                    'message' =>
                        $calculation['excess_minutes'] > 0
                            ? 'Pengajuan lembur sebesar ' . ($calculation['recognized_minutes'] / 60) . ' jam berhasil dikirim ke VP Divisi. Excess ' . ($calculation['excess_minutes'] / 60) . ' jam tidak diperhitungkan ke quota.'
                            : 'Pengajuan lembur berhasil dikirim ke VP Divisi.',
                    'data' =>
                        $overtime->load('division'),
                    'calculation' =>
                        $calculation,
                    'limit_check' =>
                        $limitCheck,
                ], 201);
            }
        );
    }

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

        return view(
            'overtime.history',
            compact('submissions')
        );
    }

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
            ->where(
                'division_id',
                $vpEmployee->division_id
            )
            ->where(
                'vp_approver_id',
                $vp->id
            )
            ->whereNotNull('vp_approved_at')
            ->latest('vp_approved_at')
            ->paginate(20)
            ->withQueryString();

        return view(
            'overtime.vp-history',
            compact('submissions')
        );
    }

    public function vpPendingList(Request $request)
    {
        $vp = $request->user();

        abort_unless($vp?->isVp(), 403);

        $employee = $vp->employee;

        abort_unless(
            $employee && $employee->division_id,
            403
        );

        $query = OvertimeSubmission::query()
            ->with([
                'user.employee.division',
                'division',
                'attendanceLog',
                'hrReturnedBy',
            ])
            ->whereIn('status', ['PENDING_VP', 'RETURNED_TO_VP'])
            ->where(
                'division_id',
                $employee->division_id
            );

        return response()->json([
            'data' => $query->latest()->get(),
        ]);
    }

    public function vpApprove(
        Request $request,
        string $id
    ) {
        $request->validate([
            'action' => 'required|in:APPROVE,REJECT',
            'notes' => 'nullable|string|max:1000',
        ]);

        $vp = $request->user();

        abort_unless($vp?->isVp(), 403);

        $vpEmployee = $vp->employee;

        abort_unless(
            $vpEmployee && $vpEmployee->division_id,
            403
        );

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

                abort_unless(
                    (int) $vpEmployee->division_id ===
                    (int) $overtime->division_id,
                    403
                );

                $isReturnedForReverification = $overtime->status === 'RETURNED_TO_VP';

                if (! in_array($overtime->status, ['PENDING_VP', 'RETURNED_TO_VP'], true)) {
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
                        $overtime->eligibility_status !==
                        'ELIGIBLE'
                        || (int) $overtime->recognized_minutes <= 0
                    )
                ) {
                    abort(
                        422,
                        'Pengajuan lembur tidak memenuhi syarat berdasarkan presensi aktual dan tidak dapat disetujui VP.'
                    );
                }

                $before = $overtime->toArray();

                $now = now();
                $overtime->forceFill([
                    'status' =>
                        $approved
                            ? 'APPROVED_VP'
                            : 'REJECTED_VP',

                    'vp_approver_id' => $vp->id,
                    'vp_approved_at' => $now,
                ]);

                if ($isReturnedForReverification) {
                    $overtime->vp_reverified_at = $now;
                    $overtime->vp_reverification_notes = $request->notes;
                } else {
                    $overtime->vp_notes = $request->notes;
                }

                $overtime->save();

                $fresh = $overtime->fresh();

                $this->audit->record(
                    $isReturnedForReverification
                        ? ($approved ? 'VP_REVERIFIED' : 'VP_REJECTED_AFTER_HR_RETURN')
                        : ($approved ? 'VP_APPROVED' : 'VP_REJECTED'),
                    $fresh,
                    $before,
                    $fresh->toArray(),
                    $request->notes
                );

                return $fresh;
            }
        );

        return response()->json([
            'success' => true,
            'message' => $overtime->vp_reverified_at
                ? ($overtime->status === 'APPROVED_VP' ? 'Pengajuan berhasil diverifikasi ulang dan dikirim kembali ke HR.' : 'Pengajuan ditolak setelah verifikasi ulang VP.')
                : ($overtime->status === 'APPROVED_VP' ? 'Pengajuan lembur disetujui VP.' : 'Pengajuan lembur ditolak VP.'),
            'data' => $overtime,
        ]);
    }

    public function hrPendingList(Request $request)
    {
        $hr = $request->user();

        abort_unless($hr?->isHrAdmin(), 403);

        return response()->json([
            'success' => true,
            'data' => OvertimeSubmission::query()
                ->with([
                    'user.employee.division',
                    'division',
                    'vpApprover',
                    'attendanceLog',
                ])
                ->where('status', 'APPROVED_VP')
                ->latest()
                ->get(),
        ]);
    }

    public function hrVerify(
        Request $request,
        string $id
    ) {
        $request->validate([
            'action' => 'required|in:VERIFY,REJECT,RETURN_TO_VP',
            'notes' => 'nullable|string|max:1000',
        ]);

        $hr = $request->user();

        abort_unless($hr?->isHrAdmin(), 403);

        $overtime = DB::transaction(
            function () use (
                $id,
                $hr,
                $request
            ) {
                $overtime = OvertimeSubmission::query()
                    ->lockForUpdate()
                    ->findOrFail($id);

                if ($overtime->status !== 'APPROVED_VP') {
                    abort(
                        409,
                        'Pengajuan belum berada pada tahap verifikasi HR atau sudah diproses sebelumnya.'
                    );
                }

                if ($request->action === 'RETURN_TO_VP' && blank($request->notes)) {
                    abort(422, 'Alasan anomaly wajib diisi saat pengajuan dikembalikan ke VP.');
                }

                $verified = $request->action === 'VERIFY';
                $returnedToVp = $request->action === 'RETURN_TO_VP';

                if (
                    $verified
                    && (
                        $overtime->eligibility_status !==
                        'ELIGIBLE'
                        || (int) $overtime->recognized_minutes <= 0
                    )
                ) {
                    abort(
                        422,
                        'Pengajuan lembur tidak memenuhi syarat berdasarkan presensi aktual dan tidak dapat diverifikasi HR.'
                    );
                }

                $before = $overtime->toArray();

                $now = now();
                $overtime->forceFill([
                    'status' => $returnedToVp
                        ? 'RETURNED_TO_VP'
                        : ($verified ? 'VERIFIED_HR' : 'REJECTED_HR'),
                    'hr_notes' => $returnedToVp ? $overtime->hr_notes : $request->notes,
                ]);

                if ($returnedToVp) {
                    $overtime->hr_returned_by_id = $hr->id;
                    $overtime->hr_returned_at = $now;
                    $overtime->hr_return_reason = $request->notes;
                } else {
                    $overtime->hr_verifier_id = $hr->id;
                    $overtime->hr_verified_at = $now;
                }

                $overtime->save();

                $fresh = $overtime->fresh();

                $this->audit->record(
                    $returnedToVp
                        ? 'HR_RETURNED_TO_VP'
                        : ($verified ? 'HR_VERIFIED' : 'HR_REJECTED'),
                    $fresh,
                    $before,
                    $fresh->toArray(),
                    $request->notes
                );

                return $fresh;
            }
        );

        return response()->json([
            'success' => true,
            'message' => $overtime->status === 'RETURNED_TO_VP'
                ? 'Pengajuan dikembalikan ke VP untuk verifikasi ulang.'
                : ($overtime->status === 'VERIFIED_HR' ? 'Pengajuan lembur berhasil diverifikasi final oleh HR.' : 'Pengajuan lembur ditolak HR.'),
            'data' => $overtime,
        ]);
    }
}
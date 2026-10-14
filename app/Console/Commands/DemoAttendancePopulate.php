<?php

namespace App\Console\Commands;

use App\Models\AttendanceAnomaly;
use App\Models\AttendanceLog;
use App\Models\AttendanceRecap;
use App\Models\AuditLog;
use App\Models\Employee;
use App\Models\HolidayCalendar;
use App\Models\OvertimeSubmission;
use App\Models\User;
use App\Services\Attendance\AttendanceAnomalyService;
use App\Services\Attendance\AttendanceRecapSyncService;
use App\Services\Demo\DemoClock;
use App\Services\Overtime\OvertimeCalculationService;
use App\Services\Overtime\OvertimeEligibilityService;
use App\Services\Overtime\OvertimePolicyResolver;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Generator data demo untuk presentasi dan UAT.
 *
 * Prinsip: semua angka dihasilkan lewat pipeline asli sistem —
 * AttendanceLog -> AttendanceRecapSyncService -> AttendanceAnomalyService,
 * dan lembur dihitung dengan OvertimePolicyResolver + OvertimeCalculationService
 * (sama persis dengan jalur pengajuan lembur di OvertimeController).
 * Nilai hadir/alpha/telat/persen rekap selalu hasil sinkronisasi dari log.
 *
 * Catatan periode: seluruh bulan dalam rentang (termasuk sisa bulan berjalan
 * setelah hari ini) diisi log agar KPI bulan berjalan tidak menampilkan
 * alpha rakitan dari hari-hari yang belum terjadi. Lembur dan anomali tetap
 * dibatasi tanggal <= hari ini.
 *
 * Data yang dihasilkan deterministik (--seed) dan idempoten
 * (tanpa --fresh, bulan yang sudah ber-rekap akan dilewati).
 */
class DemoAttendancePopulate extends Command
{
    protected $signature = 'demo:attendance-populate
        {--months=6 : Jumlah bulan riwayat termasuk bulan berjalan}
        {--seed=20261014 : Seed acak deterministik agar hasil bisa diulang}
        {--fresh : Hapus log, rekap, lembur, anomali, dan audit lembur sebelum generate}';

    protected $description = 'Mengisi data kehadiran, rekap, lembur, dan anomali demo untuk presentasi/UAT (hanya DEMO_MODE, bukan production).';

    /** Bobot sumber presensi per 1000 scan. */
    private const SUMBER_ROLLS = [
        'mesin_fingerprint' => 620,
        'face_id' => 250,
        'self_service' => 130,
    ];

    /**
     * Ambang kumulatif (per 1000 roll) status akhir pengajuan lembur.
     * VERIFIED 50%, APPROVED 14%, PENDING 8%, REJECTED_VP 11%,
     * REJECTED_HR 9%, RETURNED 8% — hanya untuk tanggal yang cukup tua.
     */
    private const STATUS_ROLLS = [
        'VERIFIED_HR' => 500,
        'APPROVED_VP' => 640,
        'PENDING_VP' => 720,
        'REJECTED_VP' => 830,
        'REJECTED_HR' => 920,
        'RETURNED_TO_VP' => 1000,
    ];

    /**
     * Enam lembur pertama memakai siklus status ini agar seluruh tahapan
     * workflow (submit → VP → HR, termasuk tolak dan pengembalian) selalu
     * terwakili untuk demo UAT; sisanya memakai roll acak pickStatus().
     */
    private const STATUS_CYCLE = [
        'VERIFIED_HR',
        'APPROVED_VP',
        'PENDING_VP',
        'REJECTED_VP',
        'REJECTED_HR',
        'RETURNED_TO_VP',
    ];

    private const REASONS = [
        'Penyelesaian laporan produksi bulanan.',
        'Dukungan go-live sistem internal.',
        'Pengejaran target proyek klien.',
        'Pemeliharaan server di luar jam kerja.',
        'Rekonsiliasi data sebelum tutup buku.',
    ];

    private const VP_NOTES = [
        'Disetujui sesuai kebutuhan operasional divisi.',
        'Lembur untuk penyelesaian target mingguan.',
        'Oke, sesuai rencana kerja divisi.',
    ];

    private const HR_NOTES = [
        'Dokumen dan bukti presensi sesuai.',
        'Verifikasi final HR: data konsisten.',
    ];

    private const HR_RETURN_REASONS = [
        'Anomali durasi lembur: mohon lampirkan bukti pendukung.',
        'Selisih evidence presensi, kembalikan ke VP untuk klarifikasi.',
    ];

    private const VP_REJECT_NOTES = [
        'Kebutuhan lembur tidak dapat dipenuhi pada tanggal tersebut.',
        'Bertentangan dengan jadwal kerja divisi.',
    ];

    private const HR_REJECT_NOTES = [
        'Bukti pendukung tidak memadai untuk verifikasi HR.',
    ];

    /** Cache resolusi policy per tanggal (legal + company rule). */
    private array $policyCache = [];

    /** Posisi siklus status demo (enam lembur pertama). */
    private int $statusDraw = 0;

    public function handle(
        DemoClock $clock,
        AttendanceRecapSyncService $recapSync,
        AttendanceAnomalyService $anomalySync,
        OvertimePolicyResolver $policyResolver,
        OvertimeCalculationService $calculator,
        OvertimeEligibilityService $eligibility,
    ): int {
        if (app()->environment('production') || ! $clock->enabled()) {
            $this->error('Perintah demo:attendance-populate hanya untuk DEMO_MODE non-production.');

            return self::FAILURE;
        }

        $months = max(1, min(24, (int) $this->option('months')));
        mt_srand((int) $this->option('seed'));

        $today = Carbon::today();
        $now = Carbon::now();
        $start = $today->copy()->subMonths($months - 1)->startOfMonth();

        if ($this->option('fresh')) {
            if (
                $this->input->isInteractive()
                && ! $this->confirm(
                    'Hapus semua log absensi, rekap, lembur, anomali, dan audit lembur yang ada? '
                    . '(Riwayat impor TIDAK dihapus — jalankan hanya jika tidak butuh data impor yang sudah masuk.)'
                )
            ) {
                $this->warn('Dibatalkan.');

                return self::FAILURE;
            }

            $this->wipeDemoData();
            $this->info('Data demo lama dihapus.');
        }

        // Key "employee_id|tanggal" log yang sudah ada, agar rerun parsial konsisten.
        $existingLogKeys = AttendanceLog::query()
            ->get(['employee_id', 'tanggal'])
            ->reduce(function (array $map, AttendanceLog $log): array {
                $map[$log->employee_id . '|' . $log->tanggal->toDateString()] = true;

                return $map;
            }, []);

        // Key "employee_id|tahun|bulan" rekap yang sudah ada: bulan tersebut dilewati.
        $existingRecapKeys = AttendanceRecap::query()
            ->get(['employee_id', 'tahun', 'bulan'])
            ->reduce(function (array $map, AttendanceRecap $recap): array {
                $map[$recap->employee_id . '|' . $recap->tahun . '|' . $recap->bulan] = true;

                return $map;
            }, []);

        $periodEnd = $start->copy()->addMonths($months - 1)->endOfMonth();

        $holidayDates = HolidayCalendar::query()
            ->whereBetween('tanggal', [$start->toDateString(), $periodEnd->toDateString()])
            ->where('is_active', true)
            ->where('jenis', 'NATIONAL_HOLIDAY')
            ->pluck('tanggal')
            ->map(fn ($date) => Carbon::parse($date)->toDateString())
            ->flip()
            ->all();

        $employees = Employee::query()->orderBy('nipeg')->get();

        if ($employees->isEmpty()) {
            $this->error('Tidak ada data karyawan. Jalankan database:seed terlebih dahulu.');

            return self::FAILURE;
        }

        $this->info(sprintf(
            'Generate log presensi %s .. %s untuk %d karyawan (seed %d).',
            $start->toDateString(),
            $today->toDateString(),
            $employees->count(),
            (int) $this->option('seed'),
        ));

        // -------------------------------------------------------------------
        // 1. Log presensi + counter HR (perdin/cuti/sakit/ijin) per bulan.
        // -------------------------------------------------------------------
        $logCount = 0;
        $recapCount = 0;

        foreach ($employees as $employee) {
            // Sebaran lokasi kerja karyawan (untuk lokasi_bandung / lokasi_jakarta).
            $bandungShare = mt_rand(55, 100) / 100;

            for ($offset = 0; $offset < $months; $offset++) {
                $cursor = $start->copy()->addMonths($offset);
                $year = $cursor->year;
                $month = $cursor->month;
                $recapKey = $employee->id . '|' . $year . '|' . $month;

                if (isset($existingRecapKeys[$recapKey])) {
                    continue; // Bulan sudah berisi rekap: jangan ditimpa.
                }

                $counters = ['perdin' => 0, 'cuti' => 0, 'sakit' => 0, 'ijin' => 0, 'hadir' => 0];
                $workdays = 0;

                for ($day = 1; $day <= $cursor->daysInMonth; $day++) {
                    $date = Carbon::create($year, $month, $day);
                    $isWorkday = $date->isWeekday() && ! isset($holidayDates[$date->toDateString()]);

                    if ($isWorkday) {
                        $workdays++;
                    }

                    $logKey = $employee->id . '|' . $date->toDateString();
                    if (isset($existingLogKeys[$logKey])) {
                        if ($isWorkday) {
                            $counters['hadir']++; // Log lama = fakta hadir, roll tidak diulang.
                        }
                        continue;
                    }

                    if (! $isWorkday) {
                        continue; // Akhir pekan/libur: tanpa log (lembur ditangani terpisah).
                    }

                    if ($date->equalTo($today)) {
                        if ($this->generateTodayLog($employee, $date, $now, $existingLogKeys)) {
                            $counters['hadir']++;
                            $logCount++;
                        }
                        continue;
                    }

                    $outcome = $this->rollDayOutcome();
                    if ($outcome === 'hadir') {
                        $this->createWorkdayLog($employee, $date, $existingLogKeys);
                        $counters['hadir']++;
                        $logCount++;
                    } elseif ($outcome !== 'alpha') {
                        $counters[$outcome]++; // perdin / cuti / sakit / ijin
                    }
                }

                // Rekap awal wajib konsisten agar lolos validasi model
                // (total hari = hari kerja). hadir/alpha/telat/persen akan
                // ditimpa ulang oleh syncPeriod dari fakta log; counter HR
                // (perdin/cuti/sakit/ijin) dan lokasi justru dipertahankan sync.
                $hadirDays = $counters['hadir'];
                $countersSum = $counters['perdin'] + $counters['cuti'] + $counters['sakit'] + $counters['ijin'];
                $bandung = min($hadirDays, (int) round($hadirDays * $bandungShare));

                AttendanceRecap::create([
                    'employee_id' => $employee->id,
                    'tahun' => $year,
                    'bulan' => $month,
                    'hari_kerja' => $workdays,
                    'hadir' => $hadirDays,
                    'perdin' => $counters['perdin'],
                    'cuti' => $counters['cuti'],
                    'sakit' => $counters['sakit'],
                    'ijin' => $counters['ijin'],
                    'alpha' => max(0, $workdays - $hadirDays - $countersSum),
                    'telat_hari' => 0,
                    'menit_telat' => 0,
                    'lokasi_bandung' => $bandung,
                    'lokasi_jakarta' => $hadirDays - $bandung,
                ]);

                $existingRecapKeys[$recapKey] = true;
                $recapCount++;
            }
        }

        $this->info("Log baru: {$logCount}, rekap baru: {$recapCount}.");

        // -------------------------------------------------------------------
        // 2. Sinkronisasi rekap (satu-satunya sumber hadir/alpha/telat/persen).
        // -------------------------------------------------------------------
        $synced = 0;
        for ($offset = 0; $offset < $months; $offset++) {
            $cursor = $start->copy()->addMonths($offset);
            $synced += $recapSync->syncPeriod($cursor->year, $cursor->month);
        }
        $this->info("Sinkronisasi rekap: {$synced} baris.");

        // -------------------------------------------------------------------
        // 3. Pengajuan lembur (hanya karyawan band eligible, lewat hitungan policy asli).
        // -------------------------------------------------------------------
        $overtimeCount = $this->generateOvertime(
            $start,
            $today,
            $eligibility,
            $policyResolver,
            $calculator,
        );

        // -------------------------------------------------------------------
        // 4. Deteksi anomali lewat service asli.
        // -------------------------------------------------------------------
        $anomalySync->sync($start->toDateString(), $today->toDateString());
        $anomalyCount = AttendanceAnomaly::query()->count();

        $this->newLine();
        $this->table(
            ['Metrik', 'Jumlah'],
            [
                ['Log presensi', AttendanceLog::query()->count()],
                ['Rekap bulanan', AttendanceRecap::query()->count()],
                ['Pengajuan lembur', OvertimeSubmission::query()->count()],
                ['Anomali terdeteksi', $anomalyCount],
                ['Audit lembur', AuditLog::query()->where('subject_type', (new OvertimeSubmission)->getMorphClass())->count()],
            ],
        );
        $this->comment('Selesai. Grafik dashboard kini terisi. Gunakan --fresh untuk generate ulang dari nol.');

        return self::SUCCESS;
    }

    /** Hapus seluruh data fakta demo (bukan data master, bukan riwayat impor). */
    private function wipeDemoData(): void
    {
        DB::transaction(function (): void {
            OvertimeSubmission::query()->delete();
            DB::table('overtime_policy_snapshots')->delete();
            AttendanceLog::query()->delete();
            AttendanceRecap::query()->delete();
            AttendanceAnomaly::query()->delete();
            AuditLog::query()
                ->where('subject_type', (new OvertimeSubmission)->getMorphClass())
                ->delete();
        });
    }

    /**
     * Roll jenis hari kerja (per mille): alpha 20, perdin 25, cuti 25,
     * sakit 25, ijin 25, sisanya hadir.
     */
    private function rollDayOutcome(): string
    {
        $roll = mt_rand(1, 1000);

        return match (true) {
            $roll <= 20 => 'alpha',
            $roll <= 45 => 'perdin',
            $roll <= 70 => 'cuti',
            $roll <= 95 => 'sakit',
            $roll <= 120 => 'ijin',
            default => 'hadir',
        };
    }

    /** Log hari kerja biasa: jam masuk 88% tepat waktu, jam pulang 12% lembur. */
    private function createWorkdayLog(Employee $employee, Carbon $date, array &$existingLogKeys): void
    {
        AttendanceLog::create([
            'employee_id' => $employee->id,
            'tanggal' => $date->toDateString(),
            'jam_masuk' => $this->pickJamMasuk(),
            'jam_pulang' => $this->pickJamPulang(),
            'sumber' => $this->pickSumber(),
            'jumlah_scan' => mt_rand(1, 1000) <= 10 ? 4 : 2, // 1% memicu DUPLICATE_SCAN
        ]);

        $existingLogKeys[$employee->id . '|' . $date->toDateString()] = true;
    }

    /**
     * Log hari berjalan: 8% belum presensi; sisanya masuk dengan sebagian
     * masih bekerja (jam pulang kosong) jika jam kantor belum lewat.
     */
    private function generateTodayLog(Employee $employee, Carbon $date, Carbon $now, array &$existingLogKeys): bool
    {
        if (mt_rand(1, 1000) <= 80) {
            return false; // Belum presensi
        }

        $officeOver = ($now->hour * 60 + $now->minute) >= (16 * 60 + 30);
        $jamPulang = null;
        if ($officeOver && mt_rand(1, 1000) > 80) {
            $jamPulang = $this->pickJamPulang();
        }

        AttendanceLog::create([
            'employee_id' => $employee->id,
            'tanggal' => $date->toDateString(),
            'jam_masuk' => $this->pickJamMasuk(),
            'jam_pulang' => $jamPulang,
            'sumber' => $this->pickSumber(),
            'jumlah_scan' => 2,
        ]);

        $existingLogKeys[$employee->id . '|' . $date->toDateString()] = true;

        return true;
    }

    private function pickJamMasuk(): string
    {
        $roll = mt_rand(1, 1000);
        $minutes = $roll <= 880
            ? mt_rand(7 * 60 + 12, 7 * 60 + 33)      // 07:12 - 07:33, tepat waktu
            : ($roll <= 960
                ? mt_rand(7 * 60 + 45, 8 * 60 + 20)   // telat ringan
                : mt_rand(8 * 60 + 20, 8 * 60 + 50)); // telat berat

        return sprintf('%02d:%02d:00', intdiv($minutes, 60), $minutes % 60);
    }

    private function pickJamPulang(): string
    {
        $roll = mt_rand(1, 1000);
        $minutes = $roll <= 50
            ? mt_rand(15 * 60 + 40, 16 * 60 + 15)     // pulang lebih awal
            : ($roll <= 880
                ? mt_rand(16 * 60 + 20, 16 * 60 + 45) // normal
                : ($roll <= 975
                    ? mt_rand(17 * 60, 18 * 60 + 30)  // lembur ringan
                    : mt_rand(19 * 60, 20 * 60 + 30))); // lembur berat (maks 4 jam, batas legal)

        return sprintf('%02d:%02d:00', intdiv($minutes, 60), $minutes % 60);
    }

    private function pickSumber(): string
    {
        $roll = mt_rand(1, 1000);
        $acc = 0;
        foreach (self::SUMBER_ROLLS as $sumber => $limit) {
            $acc += $limit;
            if ($roll <= $acc) {
                return $sumber;
            }
        }

        return 'mesin_fingerprint';
    }

    /** Status untuk lembur demo: siklus dulu (lengkap), baru acak. */
    private function nextStatus(): string
    {
        if ($this->statusDraw < count(self::STATUS_CYCLE)) {
            return self::STATUS_CYCLE[$this->statusDraw++];
        }

        return $this->pickStatus();
    }

    private function pickStatus(): string
    {
        $roll = mt_rand(1, 1000);

        foreach (self::STATUS_ROLLS as $status => $threshold) {
            if ($roll <= $threshold) {
                return $status;
            }
        }

        return 'PENDING_VP';
    }

    private function pick(array $options): string
    {
        return $options[array_rand($options)];
    }

    /**
     * Generate pengajuan lembur untuk karyawan band 2/3 yang punya akun.
     *
     * Durasi dihitung OvertimeCalculationService dengan policy hasil
     * OvertimePolicyResolver (rounding, cap, minimum sama dengan produksi).
     */
    private function generateOvertime(
        Carbon $start,
        Carbon $today,
        OvertimeEligibilityService $eligibility,
        OvertimePolicyResolver $policyResolver,
        OvertimeCalculationService $calculator,
    ): int {
        $periodOvertime = OvertimeSubmission::query()
            ->whereBetween('overtime_date', [$start->toDateString(), $today->toDateString()])
            ->count();

        if ($periodOvertime > 0) {
            $this->line("Lembur sudah ada ({$periodOvertime} baris), fase lembur dilewati.");

            return $periodOvertime;
        }

        $submitterUsers = User::query()
            ->where('role', 'karyawan')
            ->with('employee')
            ->get()
            ->filter(fn (User $user) => $user->employee && $eligibility->canSubmit($user->employee))
            ->values();

        if ($submitterUsers->isEmpty()) {
            $this->warn('Tidak ada akun karyawan band 2/3 eligible; fase lembur dilewati.');

            return 0;
        }

        $hrUser = User::query()->where('role', 'hr_admin')->first();

        if (! $hrUser) {
            $this->warn('Akun HR tidak ditemukan; fase lembur dilewati.');

            return 0;
        }

        $created = 0;
        $freshLimit = $today->copy()->subDays(2);

        foreach ($submitterUsers as $user) {
            $employee = $user->employee;

            $vpUser = User::query()->where('role', 'vp')->with('employee')->get()
                ->first(fn (User $candidate) => (int) $candidate->employee?->division_id === (int) $employee->division_id)
                ?? User::query()->where('role', 'vp')->first();

            if (! $vpUser) {
                $this->warn('Akun VP tidak ditemukan; lembur karyawan ini dilewati.');
                continue;
            }

            // (a) Lembur hari kerja: berpasangan dengan log pulang lewat 16:45.
            $logs = AttendanceLog::query()
                ->where('employee_id', $employee->id)
                ->whereBetween('tanggal', [$start->toDateString(), $today->toDateString()])
                ->whereNotNull('jam_pulang')
                ->where('jam_pulang', '>', '16:45:00')
                ->orderBy('tanggal')
                ->get();

            foreach ($logs as $log) {
                if (mt_rand(1, 1000) > 850) {
                    continue; // 85% kandidat lembur diajukan
                }

                $date = $log->tanggal->copy();

                if ($this->createSubmission([
                    'date' => $date,
                    'startStr' => '16:30:00',
                    'endStr' => $log->jam_pulang,
                    'attendanceLog' => $log,
                    'user' => $user,
                    'employee' => $employee,
                    'vpUser' => $vpUser,
                    'hrUser' => $hrUser,
                    'freshLimit' => $freshLimit,
                    'policyResolver' => $policyResolver,
                    'calculator' => $calculator,
                ])) {
                    $created++;
                }
            }

            // (b) Lembur akhir pekan: 1-2 Sabtu acak per bulan, tanpa log.
            for ($offset = 0; $offset < $this->monthsOption(); $offset++) {
                $cursor = $start->copy()->addMonths($offset);
                $saturdays = collect(range(1, $cursor->daysInMonth))
                    ->map(fn (int $day) => Carbon::create($cursor->year, $cursor->month, $day))
                    ->filter(fn (Carbon $date) => $date->isSaturday() && $date->lte($today))
                    ->values();

                if ($saturdays->isEmpty()) {
                    continue;
                }

                foreach ($saturdays->shuffle()->take(mt_rand(1, min(2, $saturdays->count()))) as $date) {
                    $endStr = mt_rand(1, 1000) <= 700 ? '14:00:00' : '15:30:00'; // > 6 jam memicu excess

                    if ($this->createSubmission([
                        'date' => $date,
                        'startStr' => '08:00:00',
                        'endStr' => $endStr,
                        'attendanceLog' => null,
                        'user' => $user,
                        'employee' => $employee,
                        'vpUser' => $vpUser,
                        'hrUser' => $hrUser,
                        'freshLimit' => $freshLimit,
                        'policyResolver' => $policyResolver,
                        'calculator' => $calculator,
                    ])) {
                        $created++;
                    }
                }
            }

            // (c) Lembur hari libur nasional dalam periode (contoh kasus kategori).
            $holidays = HolidayCalendar::query()
                ->whereBetween('tanggal', [$start->toDateString(), $today->toDateString()])
                ->where('is_active', true)
                ->where('jenis', 'NATIONAL_HOLIDAY')
                ->pluck('tanggal')
                ->map(fn ($date) => Carbon::parse($date))
                ->filter(fn (Carbon $date) => mt_rand(1, 1000) <= 600);

            foreach ($holidays as $date) {
                if ($this->createSubmission([
                    'date' => $date,
                    'startStr' => '08:00:00',
                    'endStr' => '14:00:00',
                    'attendanceLog' => null,
                    'user' => $user,
                    'employee' => $employee,
                    'vpUser' => $vpUser,
                    'hrUser' => $hrUser,
                    'freshLimit' => $freshLimit,
                    'policyResolver' => $policyResolver,
                    'calculator' => $calculator,
                ])) {
                    $created++;
                }
            }
        }

        $this->info("Pengajuan lembur dibuat: {$created}.");

        return $created;
    }

    private function monthsOption(): int
    {
        return max(1, min(24, (int) $this->option('months')));
    }

    /**
     * Satu pengajuan lembur lengkap dengan snapshot policy, alur status,
     * dan audit trail — meniru field demi field jalur produksi.
     */
    private function createSubmission(array $ctx): bool
    {
        $date = $ctx['date'];

        try {
            $policy = $this->policyCache[$date->toDateString()] ??= $ctx['policyResolver']->resolve($date);
        } catch (\RuntimeException) {
            // Kebijakan lembur belum berlaku pada tanggal tersebut
            // (effective_from) atau day type belum dikonfigurasi — lewati.
            return false;
        }

        $actualStart = Carbon::parse($date->toDateString() . ' ' . $ctx['startStr']);
        $actualEnd = Carbon::parse($date->toDateString() . ' ' . $ctx['endStr']);

        $calc = $ctx['calculator']->calculate($date, $actualStart, $actualEnd, $policy);

        if (($calc['eligibility'] ?? null) !== 'ELIGIBLE' || (int) $calc['recognized_minutes'] <= 0) {
            return false; // Sama dengan guard toko: di bawah minimum lembur.
        }

        $status = $date->greaterThan($ctx['freshLimit'])
            ? 'PENDING_VP' // Tanggal dekat: keputusan tidak boleh berada di masa depan.
            : $this->nextStatus();

        $submitAt = $actualEnd->copy()->addMinutes(mt_rand(10, 40));
        $now = Carbon::now();
        if ($submitAt->isFuture()) {
            $submitAt = $now->copy()->subMinutes(mt_rand(1, 20));
        }

        $vpAt = $submitAt->copy()->addDay()->setTime(mt_rand(9, 16), mt_rand(0, 59));
        $hrAt = $vpAt->copy()->addDay()->setTime(mt_rand(9, 16), mt_rand(0, 59));
        $updatedAt = match ($status) {
            'VERIFIED_HR', 'REJECTED_HR', 'RETURNED_TO_VP' => $hrAt,
            'APPROVED_VP', 'REJECTED_VP' => $vpAt,
            default => $submitAt,
        };

        $overtime = new OvertimeSubmission();
        $overtime->forceFill([
            'user_id' => $ctx['user']->id,
            'division_id' => $ctx['employee']->division_id,
            'overtime_date' => $date->toDateString(),
            'start_time' => $ctx['startStr'],
            'end_time' => $ctx['endStr'],
            'estimated_hours' => round($actualStart->diffInMinutes($actualEnd) / 60, 2),
            'category' => match ($calc['day_type']) {
                'NATIONAL_HOLIDAY' => 'hari_libur_nasional',
                'WEEKEND' => 'akhir_pekan',
                default => 'hari_kerja',
            },
            'reason' => $this->pick(self::REASONS),
            'employee_consent' => true,
            'employee_consent_at' => $submitAt,
            'employee_consent_ip' => '127.0.0.1',
            'employee_consent_user_agent' => 'DemoAttendancePopulate/1.0 (UAT)',
            'employee_consent_source' => 'WEB_FORM',
            'employee_consent_version' => '1.0',
            'employee_consent_text' => 'Saya menyetujui pengajuan lembur sesuai bukti presensi aktual (data demo UAT).',
            'submission_mode' => 'PRE_SUBMITTED',
            'attendance_log_id' => $ctx['attendanceLog']?->id,
            'actual_start_time' => $actualStart->format('H:i:s'),
            'actual_end_time' => $actualEnd->format('H:i:s'),
            'actual_minutes' => $calc['actual_minutes'],
            'recognized_minutes' => $calc['recognized_minutes'],
            'rounded_minutes' => $calc['rounded_minutes'],
            'excess_minutes' => $calc['excess_minutes'],
            'quota_reserved_minutes' => $calc['recognized_minutes'],
            'anomaly_codes' => $calc['excess_minutes'] > 0 ? ['EXCESS_OVERTIME'] : null,
            'eligibility_status' => $calc['eligibility'],
            'status' => $status,
            'vp_approver_id' => in_array($status, ['APPROVED_VP', 'REJECTED_VP', 'VERIFIED_HR', 'REJECTED_HR', 'RETURNED_TO_VP'], true)
                ? $ctx['vpUser']->id
                : null,
            'vp_approved_at' => in_array($status, ['APPROVED_VP', 'REJECTED_VP', 'VERIFIED_HR', 'REJECTED_HR', 'RETURNED_TO_VP'], true)
                ? $vpAt
                : null,
            'vp_notes' => $status === 'REJECTED_VP'
                ? $this->pick(self::VP_REJECT_NOTES)
                : ($status === 'PENDING_VP' ? null : $this->pick(self::VP_NOTES)),
            'hr_verifier_id' => in_array($status, ['VERIFIED_HR', 'REJECTED_HR'], true) ? $ctx['hrUser']->id : null,
            'hr_verified_at' => in_array($status, ['VERIFIED_HR', 'REJECTED_HR'], true) ? $hrAt : null,
            'hr_notes' => in_array($status, ['VERIFIED_HR', 'REJECTED_HR'], true)
                ? ($status === 'REJECTED_HR' ? $this->pick(self::HR_REJECT_NOTES) : $this->pick(self::HR_NOTES))
                : null,
            'hr_returned_by_id' => $status === 'RETURNED_TO_VP' ? $ctx['hrUser']->id : null,
            'hr_returned_at' => $status === 'RETURNED_TO_VP' ? $hrAt : null,
            'hr_return_reason' => $status === 'RETURNED_TO_VP' ? $this->pick(self::HR_RETURN_REASONS) : null,
            'created_at' => $submitAt,
            'updated_at' => $updatedAt,
        ])->save();

        DB::table('overtime_policy_snapshots')->insert([
            'overtime_submission_id' => $overtime->id,
            'legal_rule_id' => $policy['legal_rule_id'],
            'company_rule_id' => $policy['company_rule_id'],
            'policy' => json_encode($policy),
            'created_at' => $submitAt,
            'updated_at' => $submitAt,
        ]);

        $this->recordAudit('OT_SUBMITTED', $overtime, null, $overtime->toArray(), 'Pengajuan lembur dibuat.', $ctx['user'], $submitAt);

        if (in_array($status, ['APPROVED_VP', 'VERIFIED_HR', 'REJECTED_VP', 'REJECTED_HR', 'RETURNED_TO_VP'], true)) {
            $this->recordAudit(
                $status === 'REJECTED_VP' ? 'VP_REJECTED' : 'VP_APPROVED',
                $overtime,
                ['status' => 'PENDING_VP'],
                ['status' => $status === 'REJECTED_VP' ? 'REJECTED_VP' : 'APPROVED_VP'],
                $overtime->vp_notes,
                $ctx['vpUser'],
                $vpAt,
            );
        }

        if (in_array($status, ['VERIFIED_HR', 'REJECTED_HR', 'RETURNED_TO_VP'], true)) {
            $this->recordAudit(
                match ($status) {
                    'VERIFIED_HR' => 'HR_VERIFIED',
                    'REJECTED_HR' => 'HR_REJECTED',
                    default => 'HR_RETURNED_TO_VP',
                },
                $overtime,
                ['status' => 'APPROVED_VP'],
                ['status' => $status],
                $status === 'RETURNED_TO_VP' ? $overtime->hr_return_reason : $overtime->hr_notes,
                $ctx['hrUser'],
                $hrAt,
            );
        }

        return true;
    }

    /** Audit trail dengan aktor dan waktu keputusan yang realistis. */
    private function recordAudit(
        string $action,
        OvertimeSubmission $overtime,
        ?array $before,
        ?array $after,
        ?string $notes,
        User $actor,
        Carbon $at,
    ): void {
        (new AuditLog)->forceFill([
            'user_id' => $actor->id,
            'action' => $action,
            'subject_type' => $overtime->getMorphClass(),
            'subject_id' => (string) $overtime->getKey(),
            'before_data' => $before,
            'after_data' => $after,
            'notes' => $notes,
            'request_id' => (string) Str::uuid(),
            'created_at' => $at,
            'updated_at' => $at,
        ])->save();
    }
}

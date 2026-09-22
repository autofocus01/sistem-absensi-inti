<?php

namespace App\Http\Controllers;

use App\Models\AttendanceLog;
use App\Models\Division;
use App\Models\Employee;
use App\Services\Overtime\OvertimeReportingService;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

class TeamRecapController extends Controller
{
    public function __construct(
        private readonly OvertimeReportingService $overtimeReporting,
    ) {
    }

    public function index(Request $request)
    {
        $user = $request->user();

        abort_unless($user, 403);

        // VP selalu dibatasi ke divisinya sendiri.
        // Jangan pernah mengambil division_id VP dari query string.
        if ($user->role === 'vp') {
            $vpEmployee = $user->employee;

            // Fallback aman jika relasi employee belum tersedia/ter-load.
            if (! $vpEmployee) {
                $vpEmployee = Employee::query()
                    ->where('user_id', $user->id)
                    ->first();
            }

            abort_unless($vpEmployee?->division_id, 403, 'VP belum terhubung ke divisi.');

            $divisionId = (int) $vpEmployee->division_id;
        } else {
            // Jalur ini dipertahankan untuk pemanggilan controller dari role lain
            // yang memang diizinkan oleh route/middleware.
            $divisionId = $request->filled('division_id')
                ? (int) $request->input('division_id')
                : null;
        }

        $divisions = $user->role === 'vp'
            ? Division::query()->whereKey($divisionId)->get()
            : Division::query()->orderBy('nama')->get();

        $bulan = (int) $request->input('bulan', now()->month);
        $tahun = (int) $request->input('tahun', now()->year);
        $q = $request->input('q');

        $logsQuery = AttendanceLog::with(['employee.division', 'approver'])
            ->whereYear('tanggal', $tahun)
            ->whereMonth('tanggal', $bulan)
            ->when(
                $divisionId !== null,
                fn ($query) => $query->whereHas(
                    'employee',
                    fn ($employee) => $employee->where('division_id', $divisionId)
                )
            )
            ->when($q, fn ($query) => $query->whereHas('employee', function ($employee) use ($q) {
                $employee
                    ->where('nama', 'like', "%{$q}%")
                    ->orWhere('nipeg', 'like', "%{$q}%");
            }))
            ->whereNotNull('jam_masuk');

        $semuaLog = (clone $logsQuery)->get();

        // OvertimeSubmission adalah satu-satunya sumber status dan angka lembur.
        // AttendanceLog hanya untuk fakta absensi: jam masuk/pulang dan keterlambatan.
        $overtimeQuery = $this->overtimeReporting
            ->periodQuery($tahun, $bulan, $divisionId)
            ->with([
                'user.employee.division',
                'division',
                'vpApprover',
                'hrVerifier',
                'attendanceLog',
            ])
            ->where('eligibility_status', 'ELIGIBLE')
            ->where('recognized_minutes', '>', 0);

        $logLembur = $overtimeQuery->latest('overtime_date')->get();

        $totalPersonel = $semuaLog->pluck('employee_id')->unique()->count();
        $totalMenitLemburPending = $this->overtimeReporting
            ->pendingVpMinutes($tahun, $bulan, $divisionId);
        $totalMenitLemburDisetujui = $this->overtimeReporting
            ->officialMinutes($tahun, $bulan, $divisionId);
        $totalHariTelat = $semuaLog
            ->filter(fn (AttendanceLog $log) => $log->menitTelat() > 0)
            ->count();

        $statusLemburCounts = $this->overtimeReporting
            ->statusCounts($tahun, $bulan, $divisionId);

        // Tren kehadiran mingguan.
        // Hari kerja = Senin-Jumat, dikurangi tanggal libur nasional tetap.
        $liburNasionalTanggalTetap = [
            '01-01',
            '05-01',
            '06-01',
            '08-17',
            '12-25',
        ];

        $jumlahKaryawanScope = Employee::query()
            ->when(
                $divisionId !== null,
                fn ($query) => $query->where('division_id', $divisionId)
            )
            ->count();

        $akhirBulan = \Carbon\Carbon::create($tahun, $bulan, 1)->endOfMonth()->day;
        $trenMingguan = [];

        for ($mulaiHari = 1; $mulaiHari <= $akhirBulan; $mulaiHari += 7) {
            $akhirHari = min($mulaiHari + 6, $akhirBulan);
            $tanggalMulai = \Carbon\Carbon::create($tahun, $bulan, $mulaiHari);
            $tanggalAkhir = \Carbon\Carbon::create($tahun, $bulan, $akhirHari);

            $hariKerjaMinggu = 0;

            for ($d = $tanggalMulai->copy(); $d->lte($tanggalAkhir); $d->addDay()) {
                $isLiburTetap = in_array(
                    $d->format('m-d'),
                    $liburNasionalTanggalTetap,
                    true
                );

                if ($d->dayOfWeekIso <= 5 && ! $isLiburTetap) {
                    $hariKerjaMinggu++;
                }
            }

            $hadirMinggu = $semuaLog
                ->filter(fn (AttendanceLog $log) =>
                    $log->tanggal->between($tanggalMulai, $tanggalAkhir)
                )
                ->count();

            $ekspektasi = $jumlahKaryawanScope * $hariKerjaMinggu;
            $rate = $ekspektasi > 0
                ? round(($hadirMinggu / $ekspektasi) * 100, 1)
                : 0;

            $trenMingguan[] = [
                'label' => 'Minggu ' . (count($trenMingguan) + 1),
                'rentang' => $tanggalMulai->format('d') . '-' . $tanggalAkhir->format('d M'),
                'rate' => min($rate, 100),
            ];
        }

        $semuaRate = array_column($trenMingguan, 'rate');
        $rateMin = max(0, (min($semuaRate) ?: 0) - 5);
        $rateMax = min(100, (max($semuaRate) ?: 100) + 5);

        if ($rateMax - $rateMin < 5) {
            $rateMin = max(0, $rateMin - 5);
            $rateMax = min(100, $rateMax + 5);
        }

        $page = max(1, (int) $request->input('page', 1));
        $perPage = 20;

        $logLemburPaginated = new LengthAwarePaginator(
            $logLembur->forPage($page, $perPage)->values(),
            $logLembur->count(),
            $perPage,
            $page,
            [
                'path' => $request->url(),
                'query' => $request->query(),
            ]
        );

        return view('team-recap.index', [
            'divisions' => $divisions,
            'divisionId' => $divisionId,
            'bulan' => $bulan,
            'tahun' => $tahun,
            'q' => $q,
            'logs' => $logLemburPaginated,
            'totalLogLembur' => $logLembur->count(),
            'totalPersonel' => $totalPersonel,
            'totalJamLemburPending' => round($totalMenitLemburPending / 60, 1),
            'totalJamLemburDisetujui' => round($totalMenitLemburDisetujui / 60, 1),
            'totalHariTelat' => $totalHariTelat,
            'trenMingguan' => $trenMingguan,
            'rateMin' => round($rateMin, 1),
            'rateMax' => round($rateMax, 1),
            'statusLemburCounts' => $statusLemburCounts,
        ]);
    }
}

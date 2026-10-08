<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAttendanceRecapRequest;
use App\Models\AttendanceImportError;
use App\Models\AttendanceRecap;
use App\Models\Division;
use App\Models\Employee;
use App\Services\Audit\AuditLogService;
use App\Services\Attendance\AttendanceRecapSyncService;
use App\Services\Period\PeriodOptionsService;
use Illuminate\Http\Request;

class AttendanceRecapController extends Controller
{
    public function __construct(
        private readonly AuditLogService $audit,
        private readonly AttendanceRecapSyncService $recapSync,
        private readonly PeriodOptionsService $periodOptions,
    ) {
    }

    public function index(Request $request)
{
    $divisionId = $request->filled('division_id')
        ? (int) $request->input('division_id')
        : null;

    /*
     * Jika tahun kosong, gunakan tahun berjalan.
     * Ini membuat tabel dan chart memakai periode yang sama.
     */
    // Filter tabel: kosong berarti benar-benar 'Semua Tahun/Bulan'.
    $tahunFilter = $request->filled('tahun')
        ? (int) $request->input('tahun')
        : null;

    $bulanFilter = $request->filled('bulan')
        ? (int) $request->input('bulan')
        : null;

    // Grafik tetap punya periode default agar tidak kosong saat halaman pertama dibuka.
    $tahunChart = $tahunFilter ?? now()->year;
    $bulanChart = $bulanFilter;

    // Sinkronisasi rekap TIDAK dilakukan pada setiap GET/filter.
    // Rekap diperbarui saat Clock In/Out dan saat import produksi.
    // Menjalankan sinkronisasi di sini membuat request filter rentan gagal
    // karena masalah database/transaksi dan memperlambat halaman.

    $recaps = AttendanceRecap::with('employee.division')
        ->when(
            $request->filled('q'),
            fn ($query) => $query->whereHas(
                'employee',
                fn ($q) => $q
                    ->where(
                        'nama',
                        'like',
                        '%' . $request->input('q') . '%'
                    )
                    ->orWhere(
                        'nipeg',
                        'like',
                        '%' . $request->input('q') . '%'
                    )
            )
        )
        ->when(
            $divisionId !== null,
            fn ($query) => $query->whereHas(
                'employee',
                fn ($q) => $q->where(
                    'division_id',
                    $divisionId
                )
            )
        )
        ->when(
            $tahunFilter !== null,
            fn ($query) => $query->where('tahun', $tahunFilter)
        )
        ->when(
            $bulanFilter !== null,
            fn ($query) => $query->where('bulan', $bulanFilter)
        )
        ->when(
            $request->input('lokasi') === 'bandung',
            fn ($query) => $query->where(
                'lokasi_bandung',
                '>',
                0
            )
        )
        ->when(
            $request->input('lokasi') === 'jakarta',
            fn ($query) => $query->where(
                'lokasi_jakarta',
                '>',
                0
            )
        )
        ->orderByDesc('tahun')
        ->orderByDesc('bulan')
        ->paginate(20)
        ->withQueryString();

    // Tahun filter dipusatkan agar Rekap Absensi, Timesheet, dan Rekap Tim
    // menampilkan periode yang sama berdasarkan data yang benar-benar tersedia.
    $tahunTersedia = $this->periodOptions->availableYears();

    $divisions = Division::orderBy('nama')->get();

    $scopeChart = AttendanceRecap::query()
        ->when(
            $tahunFilter !== null,
            fn ($q) => $q->where('tahun', $tahunFilter),
            fn ($q) => $q->where('tahun', $tahunChart)
        )
        ->when(
            $bulanFilter !== null,
            fn ($q) => $q->where('bulan', $bulanFilter)
        )
        ->when(
            $divisionId !== null,
            fn ($q) => $q->whereHas(
                'employee',
                fn ($e) => $e->where(
                    'division_id',
                    $divisionId
                )
            )
        );

    $totalRekapChart = (clone $scopeChart)->count();

    $rataKehadiranChart = round(
        (clone $scopeChart)->avg('persen_kehadiran') ?? 0,
        1
    );

    $totalAlphaChart = (clone $scopeChart)->sum('alpha');

    $totalMenitTelatChart = (clone $scopeChart)->sum(
        'menit_telat'
    );

    $totalBandungChart = (clone $scopeChart)->sum(
        'lokasi_bandung'
    );

    $totalJakartaChart = (clone $scopeChart)->sum(
        'lokasi_jakarta'
    );

    $kehadiranPerDivisi = $divisions
        ->map(function (Division $division) use (
            $tahunChart,
            $bulanChart
        ) {
            $query = AttendanceRecap::query()
                ->whereHas(
                    'employee',
                    fn ($q) => $q->where(
                        'division_id',
                        $division->id
                    )
                )
                ->where(
                    'tahun',
                    $tahunChart
                )
                ->when(
                    $bulanChart !== null,
                    fn ($q) => $q->where(
                        'bulan',
                        $bulanChart
                    )
                );

            return [
                'nama' => $division->nama,
                'rata_kehadiran' => round(
                    (clone $query)->avg(
                        'persen_kehadiran'
                    ) ?? 0,
                    1
                ),
            ];
        })
        ->values();

    $trenBulananTahunIni = collect(range(1, 12))
        ->map(function (int $m) use (
            $tahunChart,
            $divisionId
        ) {
            $query = AttendanceRecap::query()
                ->where('tahun', $tahunChart)
                ->where('bulan', $m)
                ->when(
                    $divisionId !== null,
                    fn ($q) => $q->whereHas(
                        'employee',
                        fn ($e) => $e->where(
                            'division_id',
                            $divisionId
                        )
                    )
                );

            return [
                'label' => \Carbon\Carbon::create()
                    ->month($m)
                    ->translatedFormat('M'),

                'rata_kehadiran' => round(
                    (clone $query)->avg(
                        'persen_kehadiran'
                    ) ?? 0,
                    1
                ),

                'ada_data' => (clone $query)->exists(),
            ];
        })
        ->values();

    return view('attendance.index', compact(
        'recaps',
        'tahunTersedia',
        'divisions',
        'totalRekapChart',
        'rataKehadiranChart',
        'totalAlphaChart',
        'totalMenitTelatChart',
        'totalBandungChart',
        'totalJakartaChart',
        'kehadiranPerDivisi',
        'trenBulananTahunIni',
        'tahunChart'
    ));
}

    public function create(Request $request)
    {
        $employees = Employee::orderBy('nama')->get();
        $recap = new AttendanceRecap();
        $resolveErrorId = null;

        if ($request->filled('from_error')) {
            $error = AttendanceImportError::find($request->input('from_error'));
            if ($error && $error->jenis === 'rekap_bulanan') {
                $employee = Employee::where('nipeg', $error->nipeg)->first();
                $recap->fill(array_merge($error->data_mentah, ['employee_id' => $employee?->id, 'tahun' => $error->tahun, 'bulan' => $error->bulan]));
                $resolveErrorId = $error->id;
            }
        }

        return view('attendance.form', compact('recap', 'employees', 'resolveErrorId'));
    }

    public function store(StoreAttendanceRecapRequest $request)
    {
        try {
            $recap = AttendanceRecap::create($request->validated());
        } catch (\InvalidArgumentException $e) {
            return back()->withInput()->withErrors(['hari_kerja' => $e->getMessage()]);
        }

        $fresh = $recap->fresh();
        $this->audit->record('ATTENDANCE_RECAP_CREATED', $fresh, null, $fresh->toArray(), 'Rekap absensi dibuat oleh HR.', $request);
        $this->resolveImportErrorIfMatches($request, $fresh);

        return redirect()->route('attendance.index')->with('status', 'Rekap absensi berhasil disimpan.');
    }

    public function edit(AttendanceRecap $attendance)
    {
        $employees = Employee::orderBy('nama')->get();
        return view('attendance.form', ['recap' => $attendance, 'employees' => $employees]);
    }

    public function update(StoreAttendanceRecapRequest $request, AttendanceRecap $attendance)
    {
        try {
            $before = $attendance->toArray();
            $attendance->update($request->validated());
        } catch (\InvalidArgumentException $e) {
            return back()->withInput()->withErrors(['hari_kerja' => $e->getMessage()]);
        }

        $fresh = $attendance->fresh();
        $this->audit->record('ATTENDANCE_RECAP_UPDATED', $fresh, $before, $fresh->toArray(), 'Rekap absensi diperbarui oleh HR.', $request);

        return redirect()->route('attendance.index')->with('status', 'Rekap absensi berhasil diperbarui.');
    }

    public function destroy(Request $request, AttendanceRecap $attendance)
    {
        $before = $attendance->toArray();
        $attendance->delete();
        $this->audit->record('ATTENDANCE_RECAP_DELETED', $attendance, $before, null, 'Rekap absensi dihapus oleh HR.', $request);

        return redirect()->route('attendance.index')->with('status', 'Rekap absensi berhasil dihapus.');
    }

    private function resolveImportErrorIfMatches(Request $request, AttendanceRecap $recap): void
    {
        if (! $request->filled('resolve_error_id')) {
            return;
        }

        $employee = $recap->employee;
        $error = AttendanceImportError::query()
            ->whereKey($request->input('resolve_error_id'))
            ->where('jenis', 'rekap_bulanan')
            ->where('status', 'pending')
            ->where('nipeg', $employee?->nipeg)
            ->where('tahun', $recap->tahun)
            ->where('bulan', $recap->bulan)
            ->first();

        if (! $error) {
            abort(422, 'Error import yang dipilih tidak cocok dengan rekap yang sedang disimpan.');
        }

        $before = $error->toArray();
        $error->update(['status' => 'selesai']);
        $fresh = $error->fresh();
        $this->audit->record('ATTENDANCE_IMPORT_ERROR_RESOLVED', $fresh, $before, $fresh->toArray(), 'Error import rekap diselesaikan setelah rekap valid disimpan.', $request);
    }
}

<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAttendanceRecapRequest;
use App\Models\AttendanceImportError;
use App\Models\AttendanceRecap;
use App\Models\Division;
use App\Models\Employee;
use Illuminate\Http\Request;

class AttendanceRecapController extends Controller
{
    public function index(Request $request)
    {
        $recaps = AttendanceRecap::with('employee.division')
            ->when($request->filled('q'), function ($query) use ($request) {
                $query->whereHas('employee', function ($q) use ($request) {
                    $q->where('nama', 'like', '%' . $request->input('q') . '%')
                      ->orWhere('nipeg', 'like', '%' . $request->input('q') . '%');
                });
            })
            ->when($request->filled('division_id'), function ($query) use ($request) {
                $query->whereHas('employee', fn ($q) => $q->where('division_id', $request->input('division_id')));
            })
            ->when($request->filled('tahun'), fn ($query) => $query->where('tahun', $request->input('tahun')))
            ->when($request->filled('bulan'), fn ($query) => $query->where('bulan', $request->input('bulan')))
            ->when($request->input('lokasi') === 'bandung', fn ($query) => $query->where('lokasi_bandung', '>', 0))
            ->when($request->input('lokasi') === 'jakarta', fn ($query) => $query->where('lokasi_jakarta', '>', 0))
            ->orderByDesc('tahun')->orderByDesc('bulan')
            ->paginate(20)
            ->withQueryString();

        $tahunTersedia = AttendanceRecap::select('tahun')->distinct()->orderByDesc('tahun')->pluck('tahun');
        $divisions = Division::orderBy('nama')->get();

        // ===== Data grafik: pakai scope yang sama (tahun/bulan/divisi) TAPI tanpa filter teks/lokasi/paginasi,
        // biar chart tetap jadi gambaran utuh periode yang lagi dilihat, bukan cuma baris yang ketemu pencarian.
        $divisionId = $request->input('division_id');
        $tahunChart = $request->input('tahun') ?: now()->year;
        $bulanChart = $request->input('bulan');

        $scopeChart = AttendanceRecap::where('tahun', $tahunChart)
            ->when($bulanChart, fn ($q) => $q->where('bulan', $bulanChart))
            ->when($divisionId, fn ($q) => $q->whereHas('employee', fn ($e) => $e->where('division_id', $divisionId)));

        $totalRekapChart    = (clone $scopeChart)->count();
        $rataKehadiranChart = round((clone $scopeChart)->avg('persen_kehadiran') ?? 0, 1);
        $totalAlphaChart    = (clone $scopeChart)->sum('alpha');
        $totalMenitTelatChart = (clone $scopeChart)->sum('menit_telat');
        $totalBandungChart  = (clone $scopeChart)->sum('lokasi_bandung');
        $totalJakartaChart  = (clone $scopeChart)->sum('lokasi_jakarta');

        $kehadiranPerDivisi = $divisions->map(function (Division $division) use ($tahunChart, $bulanChart) {
            $recapsDivisi = AttendanceRecap::whereHas('employee', fn ($q) => $q->where('division_id', $division->id))
                ->where('tahun', $tahunChart)
                ->when($bulanChart, fn ($q) => $q->where('bulan', $bulanChart))
                ->get();

            return [
                'nama'           => $division->nama,
                'rata_kehadiran' => round($recapsDivisi->avg('persen_kehadiran') ?? 0, 1),
            ];
        })->values();

        // Tren rata-rata kehadiran per bulan sepanjang tahun yang dipilih (Jan-Des), hormati filter divisi.
        $trenBulananTahunIni = collect(range(1, 12))->map(function (int $m) use ($tahunChart, $divisionId) {
            $recapBulan = AttendanceRecap::where('tahun', $tahunChart)->where('bulan', $m)
                ->when($divisionId, fn ($q) => $q->whereHas('employee', fn ($e) => $e->where('division_id', $divisionId)));

            return [
                'label'          => \Carbon\Carbon::create()->month($m)->translatedFormat('M'),
                'rata_kehadiran' => round((clone $recapBulan)->avg('persen_kehadiran') ?? 0, 1),
                'ada_data'       => (clone $recapBulan)->exists(),
            ];
        })->values();

        return view('attendance.index', compact(
            'recaps', 'tahunTersedia', 'divisions',
            'totalRekapChart', 'rataKehadiranChart', 'totalAlphaChart', 'totalMenitTelatChart',
            'totalBandungChart', 'totalJakartaChart', 'kehadiranPerDivisi', 'trenBulananTahunIni', 'tahunChart'
        ));
    }

    public function create(Request $request)
    {
        $employees = Employee::orderBy('nama')->get();
        $recap = new AttendanceRecap();
        $resolveErrorId = null;

        if ($request->filled('from_error')) {
            $error = AttendanceImportError::find($request->input('from_error'));

            // Prefill cuma valid untuk error dari import rekap bulanan - strukturnya beda dengan
            // error dari import absensi harian (jam masuk/pulang), jadi jangan dipaksa isi ke sini.
            if ($error && $error->jenis === 'rekap_bulanan') {
                $employee = Employee::where('nipeg', $error->nipeg)->first();

                $recap->fill(array_merge($error->data_mentah, [
                    'employee_id' => $employee?->id,
                    'tahun'       => $error->tahun,
                    'bulan'       => $error->bulan,
                ]));

                $resolveErrorId = $error->id;
            }
        }

        return view('attendance.form', compact('recap', 'employees', 'resolveErrorId'));
    }

    public function store(StoreAttendanceRecapRequest $request)
    {
        try {
            AttendanceRecap::create($request->validated());
        } catch (\InvalidArgumentException $e) {
            return back()->withInput()->withErrors(['hari_kerja' => $e->getMessage()]);
        }

        if ($request->filled('resolve_error_id')) {
            AttendanceImportError::where('id', $request->input('resolve_error_id'))
                ->update(['status' => 'selesai']);
        }

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
            $attendance->update($request->validated());
        } catch (\InvalidArgumentException $e) {
            return back()->withInput()->withErrors(['hari_kerja' => $e->getMessage()]);
        }

        return redirect()->route('attendance.index')->with('status', 'Rekap absensi berhasil diperbarui.');
    }

    public function destroy(AttendanceRecap $attendance)
    {
        $attendance->delete();

        return redirect()->route('attendance.index')->with('status', 'Rekap absensi berhasil dihapus.');
    }
}
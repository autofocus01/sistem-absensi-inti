<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAttendanceRecapRequest;
use App\Models\AttendanceImportError;
use App\Models\AttendanceRecap;
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
            ->when($request->filled('tahun'), fn ($query) => $query->where('tahun', $request->input('tahun')))
            ->when($request->filled('bulan'), fn ($query) => $query->where('bulan', $request->input('bulan')))
            ->when($request->input('lokasi') === 'bandung', fn ($query) => $query->where('lokasi_bandung', '>', 0))
            ->when($request->input('lokasi') === 'jakarta', fn ($query) => $query->where('lokasi_jakarta', '>', 0))
            ->orderByDesc('tahun')->orderByDesc('bulan')
            ->paginate(20)
            ->withQueryString();

        $tahunTersedia = AttendanceRecap::select('tahun')->distinct()->orderByDesc('tahun')->pluck('tahun');

        return view('attendance.index', compact('recaps', 'tahunTersedia'));
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
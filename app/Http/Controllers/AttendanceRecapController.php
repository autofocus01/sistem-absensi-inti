<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAttendanceRecapRequest;
use App\Models\AttendanceRecap;
use App\Models\Employee;

class AttendanceRecapController extends Controller
{
    public function index()
    {
        $recaps = AttendanceRecap::with('employee.division')
            ->orderByDesc('tahun')->orderByDesc('bulan')
            ->paginate(20);

        return view('attendance.index', compact('recaps'));
    }

    public function create()
    {
        $employees = Employee::orderBy('nama')->get();

        return view('attendance.form', ['recap' => new AttendanceRecap(), 'employees' => $employees]);
    }

    public function store(StoreAttendanceRecapRequest $request)
    {
        try {
            AttendanceRecap::create($request->validated());
        } catch (\InvalidArgumentException $e) {
            // Ini jaring pengaman kedua kalau validasi lolos di FormRequest
            // tapi tetap ketahuan janggal di model (lihat AttendanceRecap::booted()).
            return back()->withInput()->withErrors(['hari_kerja' => $e->getMessage()]);
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
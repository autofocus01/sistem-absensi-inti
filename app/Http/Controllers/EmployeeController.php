<?php

namespace App\Http\Controllers;

use App\Models\Division;
use App\Models\Employee;
use Illuminate\Http\Request;

class EmployeeController extends Controller
{
    public function index(Request $request)
    {
        $employees = Employee::with('division')
            ->when($request->filled('q'), function ($query) use ($request) {
                $query->where(function ($q) use ($request) {
                    $q->where('nama', 'like', '%' . $request->input('q') . '%')
                      ->orWhere('nipeg', 'like', '%' . $request->input('q') . '%');
                });
            })
            ->when($request->filled('division_id'), function ($query) use ($request) {
                $query->where('division_id', $request->input('division_id'));
            })
            ->orderBy('nama')
            ->paginate(15)
            ->withQueryString();

        $divisions = Division::orderBy('nama')->get();

        return view('employees.index', compact('employees', 'divisions'));
    }

    public function create()
    {
        $divisions = Division::orderBy('nama')->get();

        return view('employees.form', ['employee' => new Employee(), 'divisions' => $divisions]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nipeg'       => ['required', 'string', 'max:20', 'unique:employees,nipeg'],
            'nama'        => ['required', 'string', 'max:255'],
            'jabatan'     => ['nullable', 'string', 'max:255'],
            'division_id' => ['required', 'exists:divisions,id'],
        ]);

        Employee::create($data);

        return redirect()->route('employees.index')->with('status', 'Karyawan berhasil ditambahkan.');
    }

    public function edit(Employee $employee)
    {
        $divisions = Division::orderBy('nama')->get();

        return view('employees.form', compact('employee', 'divisions'));
    }

    public function update(Request $request, Employee $employee)
    {
        $data = $request->validate([
            'nipeg'       => ['required', 'string', 'max:20', 'unique:employees,nipeg,' . $employee->id],
            'nama'        => ['required', 'string', 'max:255'],
            'jabatan'     => ['nullable', 'string', 'max:255'],
            'division_id' => ['required', 'exists:divisions,id'],
        ]);

        $employee->update($data);

        return redirect()->route('employees.index')->with('status', 'Data karyawan berhasil diperbarui.');
    }

    public function destroy(Employee $employee)
    {
        $employee->delete();

        return redirect()->route('employees.index')->with('status', 'Karyawan berhasil dihapus.');
    }
}
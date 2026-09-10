<?php

namespace App\Http\Controllers;

use App\Models\Division;
use App\Models\Employee;
use Illuminate\Http\Request;

class EmployeeController extends Controller
{
    public function index()
    {
        $employees = Employee::with('division')->orderBy('nama')->paginate(15);

        return view('employees.index', compact('employees'));
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
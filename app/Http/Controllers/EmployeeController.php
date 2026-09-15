<?php

namespace App\Http\Controllers;

use App\Models\Division;
use App\Models\Employee;
use App\Models\User;
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
        $availableUsers = $this->availableUsers();

        return view('employees.form', ['employee' => new Employee(), 'divisions' => $divisions, 'availableUsers' => $availableUsers]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nipeg'         => ['required', 'string', 'max:20', 'unique:employees,nipeg'],
            'nama'          => ['required', 'string', 'max:255'],
            'jenis_kelamin' => ['nullable', 'in:L,P'],
            'jabatan'       => ['nullable', 'string', 'max:255'],
            'no_hp'         => ['nullable', 'string', 'max:20'],
            'alamat'        => ['nullable', 'string', 'max:1000'],
            'division_id'   => ['required', 'exists:divisions,id'],
            'user_id'       => ['nullable', 'exists:users,id', 'unique:employees,user_id'],
        ]);

        Employee::create($data);

        return redirect()->route('employees.index')->with('status', 'Karyawan berhasil ditambahkan.');
    }

    public function edit(Employee $employee)
    {
        $divisions = Division::orderBy('nama')->get();
        $availableUsers = $this->availableUsers($employee);

        return view('employees.form', compact('employee', 'divisions', 'availableUsers'));
    }

    public function update(Request $request, Employee $employee)
    {
        $data = $request->validate([
            'nipeg'         => ['required', 'string', 'max:20', 'unique:employees,nipeg,' . $employee->id],
            'nama'          => ['required', 'string', 'max:255'],
            'jenis_kelamin' => ['nullable', 'in:L,P'],
            'jabatan'       => ['nullable', 'string', 'max:255'],
            'no_hp'         => ['nullable', 'string', 'max:20'],
            'alamat'        => ['nullable', 'string', 'max:1000'],
            'division_id'   => ['required', 'exists:divisions,id'],
            'user_id'       => ['nullable', 'exists:users,id', 'unique:employees,user_id,' . $employee->id],
        ]);

        $employee->update($data);

        return redirect()->route('employees.index')->with('status', 'Data karyawan berhasil diperbarui.');
    }

    public function destroy(Employee $employee)
    {
        $employee->delete();

        return redirect()->route('employees.index')->with('status', 'Karyawan berhasil dihapus.');
    }

    /**
     * User yang belum dihubungkan ke karyawan manapun (kecuali user yang sudah terhubung
     * ke $employee ini sendiri, biar tetap muncul di dropdown pas edit).
     */
    private function availableUsers(?Employee $employee = null)
    {
        return User::whereDoesntHave('employee')
            ->orWhere('id', $employee?->user_id)
            ->orderBy('name')
            ->get();
    }
}
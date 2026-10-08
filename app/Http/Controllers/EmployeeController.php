<?php

namespace App\Http\Controllers;

use App\Models\Division;
use App\Models\Employee;
use App\Services\Audit\AuditLogService;
use Illuminate\Http\Request;

class EmployeeController extends Controller
{
    public function __construct(
        private readonly AuditLogService $audit,
    ) {
    }

    public function index(Request $request)
    {
        $employees = Employee::with(['division', 'user'])
            ->when($request->filled('q'), function ($query) use ($request) {
                $query->where(function ($q) use ($request) {
                    $q->where('nama', 'like', '%' . $request->input('q') . '%')
                        ->orWhere('nipeg', 'like', '%' . $request->input('q') . '%');
                });
            })
            ->when(
                $request->filled('division_id'),
                fn ($query) => $query->where(
                    'division_id',
                    $request->input('division_id')
                )
            )
            ->orderBy('nama')
            ->paginate(15)
            ->withQueryString();

        $divisions = Division::orderBy('nama')->get();

        $sebaranDivisi = Division::withCount('employees')
            ->orderBy('nama')
            ->get()
            ->map(fn (Division $d) => [
                'nama' => $d->nama,
                'jumlah' => $d->employees_count,
            ]);

        $sebaranGender = [
            'Laki-laki' => Employee::where('jenis_kelamin', 'L')->count(),
            'Perempuan' => Employee::where('jenis_kelamin', 'P')->count(),
            'Tidak diisi' => Employee::whereNull('jenis_kelamin')->count(),
        ];

        return view('employees.index', compact(
            'employees',
            'divisions',
            'sebaranDivisi',
            'sebaranGender'
        ));
    }

    public function create()
    {
        $divisions = Division::orderBy('nama')->get();
        return view('employees.form', [
            'employee' => new Employee(),
            'divisions' => $divisions,
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nipeg' => [
                'required',
                'string',
                'max:20',
                'unique:employees,nipeg',
            ],
            'nama' => [
                'required',
                'string',
                'max:255',
            ],
            'jenis_kelamin' => [
                'nullable',
                'in:L,P',
            ],
            'jabatan' => [
                'nullable',
                'string',
                'max:255',
            ],
            'band_code' => [
                'nullable',
                'string',
                'max:20',
            ],
            'no_hp' => [
                'nullable',
                'string',
                'max:20',
            ],
            'alamat' => [
                'nullable',
                'string',
                'max:1000',
            ],
            'division_id' => [
                'required',
                'exists:divisions,id',
            ],
        ]);

        $employee = Employee::create($data);


        $fresh = $employee->fresh();

        $this->audit->record(
            'EMPLOYEE_CREATED',
            $fresh,
            null,
            $fresh->toArray(),
            'Master karyawan dibuat oleh HR.',
            $request
        );

        return redirect()
            ->route('employees.index')
            ->with('status', 'Karyawan berhasil ditambahkan.');
    }

    public function edit(Employee $employee)
    {
        $divisions = Division::orderBy('nama')->get();
        return view('employees.form', compact(
            'employee',
            'divisions'
        ));
    }

    public function update(
        Request $request,
        Employee $employee
    ) {
        $data = $request->validate([
            'nipeg' => [
                'required',
                'string',
                'max:20',
                'unique:employees,nipeg,' . $employee->id,
            ],
            'nama' => [
                'required',
                'string',
                'max:255',
            ],
            'jenis_kelamin' => [
                'nullable',
                'in:L,P',
            ],
            'jabatan' => [
                'nullable',
                'string',
                'max:255',
            ],
            'band_code' => [
                'nullable',
                'string',
                'max:20',
            ],
            'no_hp' => [
                'nullable',
                'string',
                'max:20',
            ],
            'alamat' => [
                'nullable',
                'string',
                'max:1000',
            ],
            'division_id' => [
                'required',
                'exists:divisions,id',
            ],
        ]);

        $before = $employee->toArray();


        $employee->update($data);

        if ($employee->user) {
            $employee->user->forceFill([
                'name' => $employee->nama,
            ])->save();
        }

        $fresh = $employee->fresh(['user']);

        $this->audit->record(
            'EMPLOYEE_UPDATED',
            $fresh,
            $before,
            $fresh->toArray(),
            'Master karyawan diperbarui oleh HR.',
            $request
        );

        return redirect()
            ->route('employees.index')
            ->with('status', 'Data karyawan berhasil diperbarui.');
    }

    public function destroy(
        Request $request,
        Employee $employee
    ) {
        if ($employee->user) {
            return redirect()
                ->route('employees.index')
                ->with('status', 'Karyawan tidak dapat dihapus selama masih memiliki akun login. Nonaktifkan akun terlebih dahulu.');
        }

        $before = $employee->toArray();

        $employee->delete();

        $this->audit->record(
            'EMPLOYEE_DELETED',
            $employee,
            $before,
            null,
            'Master karyawan dihapus oleh HR.',
            $request
        );

        return redirect()
            ->route('employees.index')
            ->with('status', 'Karyawan berhasil dihapus.');
    }

}

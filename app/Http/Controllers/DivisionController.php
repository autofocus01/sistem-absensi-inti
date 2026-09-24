<?php

namespace App\Http\Controllers;

use App\Models\Division;
use App\Services\Audit\AuditLogService;
use Illuminate\Http\Request;

class DivisionController extends Controller
{
    public function __construct(
        private readonly AuditLogService $audit,
    ) {
    }

    public function index()
    {
        $divisions = Division::withCount('employees')->orderBy('nama')->get();
        return view('divisions.index', compact('divisions'));
    }

    public function create()
    {
        return view('divisions.form', ['division' => new Division()]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nama' => ['required', 'string', 'max:255', 'unique:divisions,nama'],
            'lini' => ['nullable', 'string', 'max:255'],
        ]);

        $division = Division::create($data);
        $fresh = $division->fresh();
        $this->audit->record('DIVISION_CREATED', $fresh, null, $fresh->toArray(), 'Master divisi dibuat oleh HR.', $request);

        return redirect()->route('divisions.index')->with('status', 'Divisi berhasil ditambahkan.');
    }

    public function edit(Division $division)
    {
        return view('divisions.form', compact('division'));
    }

    public function update(Request $request, Division $division)
    {
        $data = $request->validate([
            'nama' => ['required', 'string', 'max:255', 'unique:divisions,nama,' . $division->id],
            'lini' => ['nullable', 'string', 'max:255'],
        ]);

        $before = $division->toArray();
        $division->update($data);
        $fresh = $division->fresh();
        $this->audit->record('DIVISION_UPDATED', $fresh, $before, $fresh->toArray(), 'Master divisi diperbarui oleh HR.', $request);

        return redirect()->route('divisions.index')->with('status', 'Divisi berhasil diperbarui.');
    }

    public function destroy(Request $request, Division $division)
    {
        if ($division->employees()->exists()) {
            return back()->withErrors(['nama' => 'Divisi ini masih punya karyawan terdaftar, pindahkan dulu karyawannya sebelum menghapus.']);
        }

        $before = $division->toArray();
        $division->delete();
        $this->audit->record('DIVISION_DELETED', $division, $before, null, 'Master divisi dihapus oleh HR.', $request);

        return redirect()->route('divisions.index')->with('status', 'Divisi berhasil dihapus.');
    }
}

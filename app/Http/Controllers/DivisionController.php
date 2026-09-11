<?php

namespace App\Http\Controllers;

use App\Models\Division;
use Illuminate\Http\Request;

class DivisionController extends Controller
{
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

        Division::create($data);

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

        $division->update($data);

        return redirect()->route('divisions.index')->with('status', 'Divisi berhasil diperbarui.');
    }

    public function destroy(Division $division)
    {
        if ($division->employees()->exists()) {
            return back()->withErrors(['nama' => 'Divisi ini masih punya karyawan terdaftar, pindahkan dulu karyawannya sebelum menghapus.']);
        }

        $division->delete();

        return redirect()->route('divisions.index')->with('status', 'Divisi berhasil dihapus.');
    }
}
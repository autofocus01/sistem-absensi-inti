@extends('layouts.absensi')
@section('title', 'Data Karyawan')

@section('content')
<div class="flex items-center justify-between mb-6">
  <h1 class="text-2xl font-bold text-primary">Data Karyawan</h1>
  <a href="{{ route('employees.create') }}" class="px-4 py-2 rounded-lg bg-primary-container text-white font-semibold">+ Tambah Karyawan</a>
</div>

<div class="bg-surface-container-lowest rounded-xl shadow-sm overflow-hidden">
  <table class="w-full text-left">
    <thead>
      <tr class="bg-surface-container-low text-on-surface-variant text-xs uppercase tracking-wider">
        <th class="py-3 px-4">NIPEG</th>
        <th class="py-3 px-4">Nama</th>
        <th class="py-3 px-4">Jabatan</th>
        <th class="py-3 px-4">Divisi</th>
        <th class="py-3 px-4 text-right">Aksi</th>
      </tr>
    </thead>
    <tbody class="divide-y divide-surface-container-low">
      @forelse ($employees as $employee)
      <tr>
        <td class="py-3 px-4 tabular-nums">{{ $employee->nipeg }}</td>
        <td class="py-3 px-4 font-semibold">{{ $employee->nama }}</td>
        <td class="py-3 px-4">{{ $employee->jabatan }}</td>
        <td class="py-3 px-4">{{ $employee->division->nama }}</td>
        <td class="py-3 px-4 text-right space-x-2">
          <a href="{{ route('employees.edit', $employee) }}" class="text-secondary font-semibold">Edit</a>
          <form action="{{ route('employees.destroy', $employee) }}" method="POST" class="inline" onsubmit="return confirm('Hapus karyawan ini?')">
            @csrf @method('DELETE')
            <button type="submit" class="text-error font-semibold">Hapus</button>
          </form>
        </td>
      </tr>
      @empty
      <tr><td colspan="5" class="py-6 px-4 text-center text-on-surface-variant">Belum ada data karyawan.</td></tr>
      @endforelse
    </tbody>
  </table>
</div>

<div class="mt-4">{{ $employees->links() }}</div>
@endsection
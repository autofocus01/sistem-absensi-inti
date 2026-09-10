@extends('layouts.absensi')
@section('title', $employee->exists ? 'Edit Karyawan' : 'Tambah Karyawan')

@section('content')
<h1 class="text-2xl font-bold text-primary mb-6">{{ $employee->exists ? 'Edit Karyawan' : 'Tambah Karyawan' }}</h1>

<form method="POST"
      action="{{ $employee->exists ? route('employees.update', $employee) : route('employees.store') }}"
      class="bg-surface-container-lowest rounded-xl shadow-sm p-6 max-w-lg space-y-4">
  @csrf
  @if ($employee->exists) @method('PUT') @endif

  <div>
    <label class="block text-sm font-semibold mb-1">NIPEG</label>
    <input type="text" name="nipeg" value="{{ old('nipeg', $employee->nipeg) }}"
           class="w-full rounded-lg border-gray-300" required>
  </div>

  <div>
    <label class="block text-sm font-semibold mb-1">Nama</label>
    <input type="text" name="nama" value="{{ old('nama', $employee->nama) }}"
           class="w-full rounded-lg border-gray-300" required>
  </div>

  <div>
    <label class="block text-sm font-semibold mb-1">Jabatan</label>
    <input type="text" name="jabatan" value="{{ old('jabatan', $employee->jabatan) }}"
           class="w-full rounded-lg border-gray-300">
  </div>

  <div>
    <label class="block text-sm font-semibold mb-1">Divisi</label>
    <select name="division_id" class="w-full rounded-lg border-gray-300" required>
      <option value="">-- Pilih Divisi --</option>
      @foreach ($divisions as $division)
        <option value="{{ $division->id }}" @selected(old('division_id', $employee->division_id) == $division->id)>
          {{ $division->nama }}
        </option>
      @endforeach
    </select>
  </div>

  <button type="submit" class="px-4 py-2 rounded-lg bg-primary-container text-white font-semibold">Simpan</button>
  <a href="{{ route('employees.index') }}" class="ml-2 text-on-surface-variant">Batal</a>
</form>
@endsection
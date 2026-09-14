@extends('layouts.absensi')
@section('title', $employee->exists ? 'Edit Karyawan' : 'Tambah Karyawan')

@section('content')
<div class="flex flex-col gap-1 pb-space-lg">
  <span class="font-label-sm text-label-sm text-on-surface-variant uppercase tracking-wider">DATA MASTER &bull; SDM</span>
  <h1 class="font-headline-lg text-headline-lg text-primary tracking-tight">{{ $employee->exists ? 'Edit Karyawan' : 'Tambah Karyawan' }}</h1>
</div>

<form method="POST"
      action="{{ $employee->exists ? route('employees.update', $employee) : route('employees.store') }}"
      class="bg-surface-container-lowest rounded-xl shadow-sm p-space-lg max-w-lg space-y-space-base">
  @csrf
  @if ($employee->exists) @method('PUT') @endif

  <div>
    <label class="block font-title-sm text-title-sm text-on-surface mb-1">NIPEG</label>
    <input type="text" name="nipeg" value="{{ old('nipeg', $employee->nipeg) }}"
           class="w-full rounded-lg border-0 bg-surface-container-low font-body-md text-body-md focus:ring-2 focus:ring-primary-container" required>
  </div>

  <div>
    <label class="block font-title-sm text-title-sm text-on-surface mb-1">Nama</label>
    <input type="text" name="nama" value="{{ old('nama', $employee->nama) }}"
           class="w-full rounded-lg border-0 bg-surface-container-low font-body-md text-body-md focus:ring-2 focus:ring-primary-container" required>
  </div>

  <div>
    <label class="block font-title-sm text-title-sm text-on-surface mb-1">Jabatan</label>
    <input type="text" name="jabatan" value="{{ old('jabatan', $employee->jabatan) }}"
           class="w-full rounded-lg border-0 bg-surface-container-low font-body-md text-body-md focus:ring-2 focus:ring-primary-container">
  </div>

  <div>
    <label class="block font-title-sm text-title-sm text-on-surface mb-1">Divisi</label>
    <select name="division_id" class="w-full rounded-lg border-0 bg-surface-container-low font-body-md text-body-md focus:ring-2 focus:ring-primary-container" required>
      <option value="">-- Pilih Divisi --</option>
      @foreach ($divisions as $division)
        <option value="{{ $division->id }}" @selected(old('division_id', $employee->division_id) == $division->id)>
          {{ $division->nama }}
        </option>
      @endforeach
    </select>
  </div>

  <div class="flex items-center gap-3 pt-2">
    <button type="submit" class="px-4 py-2 rounded-lg bg-primary-container text-on-primary font-title-sm text-title-sm shadow-sm hover:bg-primary transition-colors">Simpan</button>
    <a href="{{ route('employees.index') }}" class="font-title-sm text-title-sm text-on-surface-variant">Batal</a>
  </div>
</form>
@endsection
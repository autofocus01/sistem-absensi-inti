@extends('layouts.absensi')
@section('title', $recap->exists ? 'Edit Rekap Absensi' : 'Input Rekap Absensi')

@section('content')
<h1 class="text-2xl font-bold text-primary mb-2">{{ $recap->exists ? 'Edit Rekap Absensi' : 'Input Rekap Absensi' }}</h1>
<p class="text-sm text-on-surface-variant mb-6">
  % Kehadiran dihitung otomatis dari Hadir / Hari Kerja &mdash; tidak bisa diisi manual.
  Total (Hadir+Perdin+Cuti+Sakit+Ijin+Alpha) wajib sama dengan Hari Kerja.
</p>

<form method="POST"
      action="{{ $recap->exists ? route('attendance.update', $recap) : route('attendance.store') }}"
      class="bg-surface-container-lowest rounded-xl shadow-sm p-6 max-w-2xl">
  @csrf
  @if ($recap->exists) @method('PUT') @endif

  <div class="grid grid-cols-2 gap-4">
    <div class="col-span-2">
      <label class="block text-sm font-semibold mb-1">Karyawan</label>
      <select name="employee_id" class="w-full rounded-lg border-gray-300" required>
        <option value="">-- Pilih Karyawan --</option>
        @foreach ($employees as $employee)
          <option value="{{ $employee->id }}" @selected(old('employee_id', $recap->employee_id) == $employee->id)>
            {{ $employee->nipeg }} &mdash; {{ $employee->nama }}
          </option>
        @endforeach
      </select>
    </div>

    <div>
      <label class="block text-sm font-semibold mb-1">Tahun</label>
      <input type="number" name="tahun" value="{{ old('tahun', $recap->tahun ?? now()->year) }}" class="w-full rounded-lg border-gray-300" required>
    </div>
    <div>
      <label class="block text-sm font-semibold mb-1">Bulan</label>
      <select name="bulan" class="w-full rounded-lg border-gray-300" required>
        @foreach (range(1, 12) as $m)
          <option value="{{ $m }}" @selected(old('bulan', $recap->bulan) == $m)>{{ \Carbon\Carbon::create()->month($m)->translatedFormat('F') }}</option>
        @endforeach
      </select>
    </div>

    <div>
      <label class="block text-sm font-semibold mb-1">Hari Kerja</label>
      <input type="number" name="hari_kerja" value="{{ old('hari_kerja', $recap->hari_kerja) }}" class="w-full rounded-lg border-gray-300" required>
    </div>
    <div>
      <label class="block text-sm font-semibold mb-1">Hadir</label>
      <input type="number" name="hadir" value="{{ old('hadir', $recap->hadir) }}" class="w-full rounded-lg border-gray-300" required>
    </div>
    <div>
      <label class="block text-sm font-semibold mb-1">Perdin</label>
      <input type="number" name="perdin" value="{{ old('perdin', $recap->perdin) }}" class="w-full rounded-lg border-gray-300" required>
    </div>
    <div>
      <label class="block text-sm font-semibold mb-1">Cuti</label>
      <input type="number" name="cuti" value="{{ old('cuti', $recap->cuti) }}" class="w-full rounded-lg border-gray-300" required>
    </div>
    <div>
      <label class="block text-sm font-semibold mb-1">Sakit</label>
      <input type="number" name="sakit" value="{{ old('sakit', $recap->sakit) }}" class="w-full rounded-lg border-gray-300" required>
    </div>
    <div>
      <label class="block text-sm font-semibold mb-1">Ijin</label>
      <input type="number" name="ijin" value="{{ old('ijin', $recap->ijin) }}" class="w-full rounded-lg border-gray-300" required>
    </div>
    <div>
      <label class="block text-sm font-semibold mb-1">Alpha/Mangkir</label>
      <input type="number" name="alpha" value="{{ old('alpha', $recap->alpha) }}" class="w-full rounded-lg border-gray-300" required>
    </div>
    <div>
      <label class="block text-sm font-semibold mb-1">Telat (hari)</label>
      <input type="number" name="telat_hari" value="{{ old('telat_hari', $recap->telat_hari) }}" class="w-full rounded-lg border-gray-300" required>
    </div>
    <div>
      <label class="block text-sm font-semibold mb-1">Menit Telat</label>
      <input type="number" name="menit_telat" value="{{ old('menit_telat', $recap->menit_telat) }}" class="w-full rounded-lg border-gray-300" required>
    </div>
    <div>
      <label class="block text-sm font-semibold mb-1">Lokasi Bandung (hari)</label>
      <input type="number" name="lokasi_bandung" value="{{ old('lokasi_bandung', $recap->lokasi_bandung) }}" class="w-full rounded-lg border-gray-300">
    </div>
    <div>
      <label class="block text-sm font-semibold mb-1">Lokasi Jakarta (hari)</label>
      <input type="number" name="lokasi_jakarta" value="{{ old('lokasi_jakarta', $recap->lokasi_jakarta) }}" class="w-full rounded-lg border-gray-300">
    </div>
  </div>

  <div class="mt-6">
    <button type="submit" class="px-4 py-2 rounded-lg bg-primary-container text-white font-semibold">Simpan</button>
    <a href="{{ route('attendance.index') }}" class="ml-2 text-on-surface-variant">Batal</a>
  </div>
</form>
@endsection
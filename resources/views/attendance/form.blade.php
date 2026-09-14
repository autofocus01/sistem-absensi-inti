@extends('layouts.absensi')
@section('title', $recap->exists ? 'Edit Rekap Absensi' : 'Input Rekap Absensi')

@section('content')
<div class="flex flex-col gap-1 pb-space-lg">
  <span class="font-label-sm text-label-sm text-on-surface-variant uppercase tracking-wider">REKAP KEHADIRAN BULANAN</span>
  <h1 class="font-headline-lg text-headline-lg text-primary tracking-tight">{{ $recap->exists ? 'Edit Rekap Absensi' : 'Input Rekap Absensi' }}</h1>
  <p class="font-body-md text-body-md text-on-surface-variant max-w-2xl">
    % Kehadiran dihitung otomatis dari Hadir / Hari Kerja &mdash; tidak bisa diisi manual.
    Total (Hadir+Perdin+Cuti+Sakit+Ijin+Alpha) wajib sama dengan Hari Kerja.
  </p>
</div>

<form method="POST"
      action="{{ $recap->exists ? route('attendance.update', $recap) : route('attendance.store') }}"
      class="bg-surface-container-lowest rounded-xl shadow-sm p-space-lg max-w-2xl">
  @csrf
  @if ($recap->exists) @method('PUT') @endif
  @if (!empty($resolveErrorId))
    <input type="hidden" name="resolve_error_id" value="{{ $resolveErrorId }}">
    <div class="mb-4 px-3 py-2 rounded-lg bg-secondary/10 text-secondary text-sm">
      Form ini terisi dari data anomali yang ditolak saat import. Koreksi angkanya sesuai temuan sebenarnya, lalu simpan.
    </div>
  @endif

  <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
    <div class="col-span-2">
      <label class="block font-title-sm text-title-sm text-on-surface mb-1">Karyawan</label>
      <select name="employee_id" class="w-full rounded-lg border-0 bg-surface-container-low font-body-md text-body-md focus:ring-2 focus:ring-primary-container" required>
        <option value="">-- Pilih Karyawan --</option>
        @foreach ($employees as $employee)
          <option value="{{ $employee->id }}" @selected(old('employee_id', $recap->employee_id) == $employee->id)>
            {{ $employee->nipeg }} &mdash; {{ $employee->nama }}
          </option>
        @endforeach
      </select>
    </div>

    <div>
      <label class="block font-title-sm text-title-sm text-on-surface mb-1">Tahun</label>
      <input type="number" name="tahun" value="{{ old('tahun', $recap->tahun ?? now()->year) }}" class="w-full rounded-lg border-0 bg-surface-container-low font-body-md text-body-md focus:ring-2 focus:ring-primary-container" required>
    </div>
    <div>
      <label class="block font-title-sm text-title-sm text-on-surface mb-1">Bulan</label>
      <select name="bulan" class="w-full rounded-lg border-0 bg-surface-container-low font-body-md text-body-md focus:ring-2 focus:ring-primary-container" required>
        @foreach (range(1, 12) as $m)
          <option value="{{ $m }}" @selected(old('bulan', $recap->bulan) == $m)>{{ \Carbon\Carbon::create()->month($m)->translatedFormat('F') }}</option>
        @endforeach
      </select>
    </div>

    <div>
      <label class="block font-title-sm text-title-sm text-on-surface mb-1">Hari Kerja</label>
      <input type="number" name="hari_kerja" value="{{ old('hari_kerja', $recap->hari_kerja) }}" class="w-full rounded-lg border-0 bg-surface-container-low font-body-md text-body-md focus:ring-2 focus:ring-primary-container" required>
    </div>
    <div>
      <label class="block font-title-sm text-title-sm text-on-surface mb-1">Hadir</label>
      <input type="number" name="hadir" value="{{ old('hadir', $recap->hadir) }}" class="w-full rounded-lg border-0 bg-surface-container-low font-body-md text-body-md focus:ring-2 focus:ring-primary-container" required>
    </div>
    <div>
      <label class="block font-title-sm text-title-sm text-on-surface mb-1">Perdin</label>
      <input type="number" name="perdin" value="{{ old('perdin', $recap->perdin) }}" class="w-full rounded-lg border-0 bg-surface-container-low font-body-md text-body-md focus:ring-2 focus:ring-primary-container" required>
    </div>
    <div>
      <label class="block font-title-sm text-title-sm text-on-surface mb-1">Cuti</label>
      <input type="number" name="cuti" value="{{ old('cuti', $recap->cuti) }}" class="w-full rounded-lg border-0 bg-surface-container-low font-body-md text-body-md focus:ring-2 focus:ring-primary-container" required>
    </div>
    <div>
      <label class="block font-title-sm text-title-sm text-on-surface mb-1">Sakit</label>
      <input type="number" name="sakit" value="{{ old('sakit', $recap->sakit) }}" class="w-full rounded-lg border-0 bg-surface-container-low font-body-md text-body-md focus:ring-2 focus:ring-primary-container" required>
    </div>
    <div>
      <label class="block font-title-sm text-title-sm text-on-surface mb-1">Ijin</label>
      <input type="number" name="ijin" value="{{ old('ijin', $recap->ijin) }}" class="w-full rounded-lg border-0 bg-surface-container-low font-body-md text-body-md focus:ring-2 focus:ring-primary-container" required>
    </div>
    <div>
      <label class="block font-title-sm text-title-sm text-on-surface mb-1">Alpha/Mangkir</label>
      <input type="number" name="alpha" value="{{ old('alpha', $recap->alpha) }}" class="w-full rounded-lg border-0 bg-surface-container-low font-body-md text-body-md focus:ring-2 focus:ring-primary-container" required>
    </div>
    <div>
      <label class="block font-title-sm text-title-sm text-on-surface mb-1">Telat (hari)</label>
      <input type="number" name="telat_hari" value="{{ old('telat_hari', $recap->telat_hari) }}" class="w-full rounded-lg border-0 bg-surface-container-low font-body-md text-body-md focus:ring-2 focus:ring-primary-container" required>
    </div>
    <div>
      <label class="block font-title-sm text-title-sm text-on-surface mb-1">Menit Telat</label>
      <input type="number" name="menit_telat" value="{{ old('menit_telat', $recap->menit_telat) }}" class="w-full rounded-lg border-0 bg-surface-container-low font-body-md text-body-md focus:ring-2 focus:ring-primary-container" required>
    </div>
    <div>
      <label class="block font-title-sm text-title-sm text-on-surface mb-1">Lokasi Bandung (hari)</label>
      <input type="number" name="lokasi_bandung" value="{{ old('lokasi_bandung', $recap->lokasi_bandung) }}" class="w-full rounded-lg border-0 bg-surface-container-low font-body-md text-body-md focus:ring-2 focus:ring-primary-container">
    </div>
    <div>
      <label class="block font-title-sm text-title-sm text-on-surface mb-1">Lokasi Jakarta (hari)</label>
      <input type="number" name="lokasi_jakarta" value="{{ old('lokasi_jakarta', $recap->lokasi_jakarta) }}" class="w-full rounded-lg border-0 bg-surface-container-low font-body-md text-body-md focus:ring-2 focus:ring-primary-container">
    </div>
  </div>

  <div class="mt-space-base">
    <button type="submit" class="px-4 py-2 rounded-lg bg-primary-container text-on-primary font-title-sm text-title-sm shadow-sm hover:bg-primary transition-colors">Simpan</button>
    <a href="{{ route('attendance.index') }}" class="ml-2 font-title-sm text-title-sm text-on-surface-variant">Batal</a>
  </div>
</form>
@endsection
@extends('layouts.absensi')
@section('title', $division->exists ? 'Edit Divisi' : 'Tambah Divisi')

@section('content')
<div class="flex flex-col gap-1 pb-space-lg">
  <span class="font-label-sm text-label-sm text-on-surface-variant uppercase tracking-wider">STRUKTUR ORGANISASI</span>
  <h1 class="font-headline-lg text-headline-lg text-primary tracking-tight">{{ $division->exists ? 'Edit Divisi' : 'Tambah Divisi' }}</h1>
</div>

<form method="POST"
      action="{{ $division->exists ? route('divisions.update', $division) : route('divisions.store') }}"
      class="bg-surface-container-lowest rounded-xl shadow-sm p-space-lg max-w-lg space-y-space-base">
  @csrf
  @if ($division->exists) @method('PUT') @endif

  <div>
    <label class="block font-title-sm text-title-sm text-on-surface mb-1">Nama Divisi</label>
    <input type="text" name="nama" value="{{ old('nama', $division->nama) }}"
           class="w-full rounded-lg border-0 bg-surface-container-low font-body-md text-body-md focus:ring-2 focus:ring-primary-container" required>
  </div>

  <div>
    <label class="block font-title-sm text-title-sm text-on-surface mb-1">Lini (opsional)</label>
    <input type="text" name="lini" value="{{ old('lini', $division->lini) }}" placeholder="mis. SEVP, Direktur Langsung"
           class="w-full rounded-lg border-0 bg-surface-container-low font-body-md text-body-md focus:ring-2 focus:ring-primary-container">
  </div>

  <button type="submit" class="px-4 py-2 rounded-lg bg-primary-container text-on-primary font-title-sm text-title-sm shadow-sm hover:bg-primary transition-colors">Simpan</button>
  <a href="{{ route('divisions.index') }}" class="ml-2 text-on-surface-variant">Batal</a>
</form>
@endsection
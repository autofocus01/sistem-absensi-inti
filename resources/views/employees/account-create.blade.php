@extends('layouts.absensi')
@section('title', 'Buat Akun Karyawan')

@section('content')
<div class="max-w-2xl mx-auto">
  <div class="flex flex-col gap-1 pb-space-lg">
    <span class="font-label-sm text-label-sm text-on-surface-variant uppercase tracking-wider">MANAJEMEN AKUN &bull; SDM</span>
    <h1 class="font-headline-lg text-headline-lg text-primary tracking-tight">Buat Akun Karyawan</h1>
    <p class="font-body-md text-body-md text-on-surface-variant">HR membuat akun login untuk karyawan yang sudah terdaftar pada master data.</p>
  </div>

  <div class="bg-surface-container-lowest rounded-2xl shadow-sm p-space-xl space-y-space-lg">
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-space-base">
      <div class="rounded-xl bg-surface-container-low p-space-md">
        <span class="font-label-sm text-label-sm text-on-surface-variant uppercase">NIPEG</span>
        <p class="font-title-md text-title-md text-on-surface mt-1">{{ $employee->nipeg }}</p>
      </div>
      <div class="rounded-xl bg-surface-container-low p-space-md">
        <span class="font-label-sm text-label-sm text-on-surface-variant uppercase">Nama</span>
        <p class="font-title-md text-title-md text-on-surface mt-1">{{ $employee->nama }}</p>
      </div>
      <div class="rounded-xl bg-surface-container-low p-space-md">
        <span class="font-label-sm text-label-sm text-on-surface-variant uppercase">Band Code</span>
        <p class="font-title-md text-title-md text-on-surface mt-1">{{ $employee->band_code !== null ? 'Band '.$employee->band_code : '-' }}</p>
      </div>
      <div class="rounded-xl bg-surface-container-low p-space-md">
        <span class="font-label-sm text-label-sm text-on-surface-variant uppercase">Divisi</span>
        <p class="font-title-md text-title-md text-on-surface mt-1">{{ $employee->division?->nama ?? '-' }}</p>
      </div>
    </div>

    <div class="rounded-xl bg-primary/5 border border-primary/10 p-space-md">
      <div class="flex items-start gap-space-sm">
        <span class="material-symbols-outlined text-primary">info</span>
        <div>
          <p class="font-title-sm text-title-sm text-on-surface">Aturan akun</p>
          <ul class="font-body-sm text-body-sm text-on-surface-variant mt-1 list-disc pl-5 space-y-1">
            <li>Role otomatis <strong>Karyawan</strong>.</li>
            <li>Nama akun mengikuti master data karyawan.</li>
            <li>Password di bawah menjadi password sementara dan wajib diganti saat login pertama.</li>
            <li>Band Code tetap berasal dari master karyawan dan tidak dapat diubah melalui akun.</li>
          </ul>
        </div>
      </div>
    </div>

    <form method="POST" action="{{ route('employees.account.store', $employee) }}" class="space-y-space-base">
      @csrf
      <div>
        <label class="block font-title-sm text-title-sm text-on-surface mb-1">Email Login</label>
        <input type="email" name="email" value="{{ old('email') }}" autocomplete="email" required
               class="w-full rounded-lg border-0 bg-surface-container-low font-body-md text-body-md focus:ring-2 focus:ring-primary-container">
        @error('email') <p class="text-error text-sm mt-1">{{ $message }}</p> @enderror
      </div>

      <div>
        <label class="block font-title-sm text-title-sm text-on-surface mb-1">Password Sementara</label>
        <input type="password" name="password" autocomplete="new-password" required
               class="w-full rounded-lg border-0 bg-surface-container-low font-body-md text-body-md focus:ring-2 focus:ring-primary-container">
        @error('password') <p class="text-error text-sm mt-1">{{ $message }}</p> @enderror
      </div>

      <div>
        <label class="block font-title-sm text-title-sm text-on-surface mb-1">Konfirmasi Password Sementara</label>
        <input type="password" name="password_confirmation" autocomplete="new-password" required
               class="w-full rounded-lg border-0 bg-surface-container-low font-body-md text-body-md focus:ring-2 focus:ring-primary-container">
      </div>

      <div class="flex flex-wrap items-center gap-3 pt-2">
        <button type="submit" class="px-4 py-2.5 rounded-lg bg-primary-container text-on-primary font-title-sm text-title-sm shadow-sm hover:bg-primary transition-colors">
          <span class="material-symbols-outlined align-middle text-[18px] mr-1">person_add</span>
          Buat Akun
        </button>
        <a href="{{ route('employees.index') }}" class="px-4 py-2.5 rounded-lg text-on-surface-variant font-title-sm text-title-sm">Batal</a>
      </div>
    </form>
  </div>
</div>
@endsection

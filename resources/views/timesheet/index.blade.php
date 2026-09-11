@extends('layouts.absensi')
@section('title', 'Riwayat & Timesheet')

@section('content')
<div class="flex flex-col sm:flex-row sm:items-center justify-between gap-space-base pb-space-lg">
  <div class="flex flex-col gap-1">
    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full bg-primary-container text-surface-container-lowest font-label-sm text-label-sm uppercase tracking-wider w-fit">
      <span class="material-symbols-outlined text-[14px]">calendar_clock</span> RIWAYAT &amp; TIMESHEET
    </span>
    <h1 class="font-headline-lg text-headline-lg text-primary tracking-tight">Riwayat Presensi &amp; Lembur</h1>
    <p class="font-body-md text-body-md text-on-surface-variant">
      Jam operasional 07:30 &ndash; 16:30 WIB, Senin&ndash;Jumat. Data jam masuk/pulang diimport dari mesin fingerprint/Face ID.
    </p>
  </div>
</div>

<form method="GET" class="flex flex-col sm:flex-row gap-2 mb-space-lg bg-surface-container-lowest rounded-xl shadow-sm p-space-sm">
  <select name="employee_id" onchange="this.form.submit()" class="flex-1 rounded-lg border-0 bg-surface-container-low font-body-md text-body-md">
    @foreach ($employees as $employee)
      <option value="{{ $employee->id }}" @selected($employeeId == $employee->id)>{{ $employee->nama }} — {{ $employee->nipeg }}</option>
    @endforeach
  </select>
  <select name="bulan" onchange="this.form.submit()" class="rounded-lg border-0 bg-surface-container-low font-body-md text-body-md">
    @foreach (range(1, 12) as $m)
      <option value="{{ $m }}" @selected($bulan == $m)>{{ \Carbon\Carbon::create()->month($m)->translatedFormat('F') }}</option>
    @endforeach
  </select>
  <select name="tahun" onchange="this.form.submit()" class="rounded-lg border-0 bg-surface-container-low font-body-md text-body-md">
    @foreach (range(now()->year, now()->year - 3) as $y)
      <option value="{{ $y }}" @selected($tahun == $y)>{{ $y }}</option>
    @endforeach
  </select>
</form>

<div class="grid grid-cols-1 sm:grid-cols-3 gap-space-base mb-space-lg">
  <div class="bg-surface-container-lowest rounded-xl shadow-sm p-space-base">
    <div class="flex items-center justify-between">
      <span class="font-label-sm text-label-sm text-on-surface-variant uppercase tracking-wider">Lembur Disetujui</span>
      <span class="material-symbols-outlined text-[20px] text-tertiary">more_time</span>
    </div>
    <p class="font-headline-md text-headline-md text-primary mt-1">{{ $totalJamLembur }} <span class="font-body-md text-body-md text-on-surface-variant">Jam</span></p>
  </div>
  <div class="bg-surface-container-lowest rounded-xl shadow-sm p-space-base">
    <div class="flex items-center justify-between">
      <span class="font-label-sm text-label-sm text-on-surface-variant uppercase tracking-wider">Hari Hadir</span>
      <span class="material-symbols-outlined text-[20px] text-secondary">fact_check</span>
    </div>
    <p class="font-headline-md text-headline-md text-primary mt-1">{{ $totalHariHadir }} <span class="font-body-md text-body-md text-on-surface-variant">Hari</span></p>
  </div>
  <div class="bg-surface-container-lowest rounded-xl shadow-sm p-space-base">
    <div class="flex items-center justify-between">
      <span class="font-label-sm text-label-sm text-on-surface-variant uppercase tracking-wider">Hari Telat</span>
      <span class="material-symbols-outlined text-[20px] text-error">timer_off</span>
    </div>
    <p class="font-headline-md text-headline-md text-primary mt-1">{{ $totalHariTelat }} <span class="font-body-md text-body-md text-on-surface-variant">Hari</span></p>
  </div>
</div>

<div class="bg-surface-container-lowest rounded-xl shadow-sm overflow-x-auto">
  <table class="w-full text-left min-w-[860px]">
    <thead>
      <tr class="bg-surface-container-low text-on-surface-variant font-label-sm text-label-sm uppercase tracking-wider">
        <th class="py-3 px-space-base rounded-l-lg">Tanggal</th>
        <th class="py-3 px-space-base">Jam Masuk</th>
        <th class="py-3 px-space-base">Jam Pulang</th>
        <th class="py-3 px-space-base">Durasi</th>
        <th class="py-3 px-space-base">Status</th>
        <th class="py-3 px-space-base text-right rounded-r-lg">Lembur</th>
      </tr>
    </thead>
    <tbody class="divide-y divide-surface-container-low font-body-md text-body-md">
      @forelse ($logs as $log)
      <tr class="hover:bg-surface-container-low/40 transition-colors">
        <td class="py-space-base px-space-base">
          <div class="font-title-sm text-title-sm text-primary">{{ $log->tanggal->translatedFormat('l, d M Y') }}</div>
          @unless ($log->isHariKerja())
            <span class="font-label-sm text-label-sm text-on-surface-variant">Di luar hari kerja</span>
          @endunless
        </td>
        <td class="py-space-base px-space-base tabular-nums">{{ $log->jam_masuk ? \Carbon\Carbon::parse($log->jam_masuk)->format('H:i') : '-' }}</td>
        <td class="py-space-base px-space-base tabular-nums">{{ $log->jam_pulang ? \Carbon\Carbon::parse($log->jam_pulang)->format('H:i') : '-' }}</td>
        <td class="py-space-base px-space-base tabular-nums">{{ $log->durasiKerjaFormat() }}</td>
        <td class="py-space-base px-space-base">
          @php($status = $log->status())
          <span class="inline-flex items-center px-2 py-0.5 rounded-full font-label-sm text-label-sm font-semibold
            {{ $status === 'Hadir + Lembur' ? 'bg-tertiary-container text-on-tertiary-container' : ($status === 'Hadir' ? 'bg-secondary-container/50 text-secondary' : 'bg-surface-container text-on-surface-variant') }}">
            {{ $status }}
          </span>
        </td>
        <td class="py-space-base px-space-base text-right tabular-nums font-title-sm text-title-sm {{ $log->menitLembur() > 0 ? 'text-tertiary' : 'text-on-surface-variant' }}">
          {{ $log->lemburFormat() }}
        </td>
      </tr>
      @empty
      <tr><td colspan="6" class="py-8 px-4 text-center text-on-surface-variant">Belum ada data presensi harian untuk periode ini.</td></tr>
      @endforelse
    </tbody>
  </table>
</div>

<div class="mt-space-base">{{ $logs->links() }}</div>
@endsection

@extends('layouts.absensi')
@section('title', 'Riwayat & Timesheet')

@section('content')
<div class="flex flex-col sm:flex-row sm:items-center justify-between gap-space-base pb-space-lg">
  <div class="flex flex-col gap-1">
    <span class="font-label-sm text-label-sm text-on-surface-variant uppercase tracking-wider">RIWAYAT &amp; TIMESHEET</span>
    <h1 class="font-headline-lg text-headline-lg text-primary tracking-tight">Riwayat Presensi &amp; Lembur</h1>
    <p class="font-body-md text-body-md text-on-surface-variant">
      Jam operasional 07:30 &ndash; 16:30 WIB, Senin&ndash;Jumat. Data jam masuk/pulang diimport dari mesin fingerprint/Face ID.
    </p>
  </div>
</div>

<form method="GET" class="flex flex-col gap-2 mb-space-lg bg-surface-container-lowest rounded-xl shadow-sm p-space-sm">
  <div class="flex flex-col sm:flex-row gap-2">
    <input type="text" name="q" value="{{ $q }}" placeholder="Cari nama atau NIPEG untuk mempersempit daftar..."
           class="flex-1 rounded-lg border-0 bg-surface-container-low font-body-md text-body-md focus:ring-2 focus:ring-primary-container">
    <button type="submit" class="px-4 py-2 rounded-lg bg-primary-container text-on-primary font-title-sm text-title-sm">Cari</button>
    @if ($q)
      <a href="{{ route('timesheet.index') }}" class="px-4 py-2 rounded-lg text-on-surface-variant font-title-sm text-title-sm text-center">Reset</a>
    @endif
  </div>
  <div class="flex flex-col sm:flex-row gap-2">
    <select name="employee_id" onchange="this.form.submit()" class="flex-1 rounded-lg border-0 bg-surface-container-low font-body-md text-body-md">
      @forelse ($employees as $employee)
        <option value="{{ $employee->id }}" @selected($employeeId == $employee->id)>{{ $employee->nama }} — {{ $employee->nipeg }}</option>
      @empty
        <option value="">Tidak ada karyawan yang cocok</option>
      @endforelse
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
  </div>
</form>

<div class="grid grid-cols-1 sm:grid-cols-3 gap-space-base mb-space-lg">
  <div class="bg-surface-container-lowest rounded-xl shadow-sm p-space-base">
    <div class="flex items-center justify-between">
      <span class="font-label-sm text-label-sm text-on-surface-variant uppercase tracking-wider">Lembur Disetujui</span>
      <span class="material-symbols-outlined text-[20px] text-primary">more_time</span>
    </div>
    <p class="font-headline-md text-headline-md text-primary mt-1">{{ $totalJamLembur }} <span class="font-body-md text-body-md text-on-surface-variant">Jam</span></p>
  </div>
  <div class="bg-surface-container-lowest rounded-xl shadow-sm p-space-base">
    <div class="flex items-center justify-between">
      <span class="font-label-sm text-label-sm text-on-surface-variant uppercase tracking-wider">Hari Hadir</span>
      <span class="material-symbols-outlined text-[20px] text-primary">fact_check</span>
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

{{-- Ringkasan bulanan dari Rekap HR (bukan dari log harian) - Perdin/Cuti/Izin/Sakit/Alpha & lokasi --}}
<div class="bg-surface-container-lowest rounded-xl shadow-sm p-space-lg mb-space-lg">
  <div class="flex items-center justify-between pb-space-base border-b border-surface-container-low">
    <span class="font-headline-sm text-headline-sm text-on-surface">Ringkasan Bulanan (Rekap HR)</span>
    <span class="font-body-sm text-body-sm text-on-surface-variant">{{ \Carbon\Carbon::create()->month($bulan)->translatedFormat('F') }} {{ $tahun }}</span>
  </div>

  @if ($rekapBulanIni)
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-space-sm pt-space-base">
      <div class="p-space-sm rounded-lg bg-surface-container-low text-center">
        <div class="font-headline-sm text-headline-sm text-primary tabular-nums">{{ $rekapBulanIni->perdin }}</div>
        <div class="font-label-sm text-label-sm text-on-surface-variant uppercase">Perdin</div>
      </div>
      <div class="p-space-sm rounded-lg bg-surface-container-low text-center">
        <div class="font-headline-sm text-headline-sm text-primary tabular-nums">{{ $rekapBulanIni->cuti }}</div>
        <div class="font-label-sm text-label-sm text-on-surface-variant uppercase">Cuti</div>
      </div>
      <div class="p-space-sm rounded-lg bg-surface-container-low text-center">
        <div class="font-headline-sm text-headline-sm text-primary tabular-nums">{{ $rekapBulanIni->ijin }}</div>
        <div class="font-label-sm text-label-sm text-on-surface-variant uppercase">Izin</div>
      </div>
      <div class="p-space-sm rounded-lg bg-surface-container-low text-center">
        <div class="font-headline-sm text-headline-sm text-primary tabular-nums">{{ $rekapBulanIni->sakit }}</div>
        <div class="font-label-sm text-label-sm text-on-surface-variant uppercase">Sakit</div>
      </div>
      <div class="p-space-sm rounded-lg {{ $rekapBulanIni->alpha > 0 ? 'bg-error-container' : 'bg-surface-container-low' }} text-center">
        <div class="font-headline-sm text-headline-sm {{ $rekapBulanIni->alpha > 0 ? 'text-error' : 'text-primary' }} tabular-nums">{{ $rekapBulanIni->alpha }}</div>
        <div class="font-label-sm text-label-sm {{ $rekapBulanIni->alpha > 0 ? 'text-error' : 'text-on-surface-variant' }} uppercase">Alpha</div>
      </div>
      <div class="p-space-sm rounded-lg bg-surface-container-low text-center">
        <div class="font-headline-sm text-headline-sm text-primary tabular-nums">{{ $rekapBulanIni->lokasi_bandung }}/{{ $rekapBulanIni->lokasi_jakarta }}</div>
        <div class="font-label-sm text-label-sm text-on-surface-variant uppercase">Bandung/Jakarta</div>
      </div>
    </div>
  @else
    <p class="font-body-md text-body-md text-on-surface-variant pt-space-base">Belum ada rekap bulanan HR untuk karyawan &amp; periode ini.</p>
  @endif

  <p class="font-body-sm text-body-sm text-on-surface-variant pt-space-base">
    Catatan: kategori WFH/WFO belum ada di sistem &mdash; data lokasi yang tersedia baru sebatas on-site Bandung/Jakarta dari rekap bulanan HR.
  </p>
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
        <td class="py-space-base px-space-base text-right tabular-nums font-title-sm text-title-sm {{ $log->menitLembur() > 0 ? 'text-primary' : 'text-on-surface-variant' }}">
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
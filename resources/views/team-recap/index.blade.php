@extends('layouts.absensi')
@section('title', 'Rekapitulasi Tim (HR)')

@section('content')
<div class="flex flex-col gap-1 pb-space-lg">
  <span class="font-label-sm text-label-sm text-on-surface-variant uppercase tracking-wider">HR MANAGEMENT &amp; TEAM OVERSIGHT</span>
  <h1 class="font-headline-lg text-headline-lg text-primary tracking-tight">Rekapitulasi Tim &amp; Persetujuan Lembur</h1>
  <p class="font-body-md text-body-md text-on-surface-variant max-w-2xl">
    Tinjau dan setujui lembur karyawan yang tercatat dari mesin fingerprint/Face ID (jam pulang &gt; 16:30, dibulatkan ke bawah per 30 menit).
  </p>
</div>

<form method="GET" class="flex flex-col sm:flex-row gap-2 mb-space-lg bg-surface-container-lowest rounded-xl shadow-sm p-space-sm">
  <input type="text" name="q" value="{{ $q }}" placeholder="Cari nama atau NIPEG..."
         class="flex-1 rounded-lg border-0 bg-surface-container-low font-body-md text-body-md focus:ring-2 focus:ring-primary-container">
  <select name="division_id" onchange="this.form.submit()" class="rounded-lg border-0 bg-surface-container-low font-body-md text-body-md">
    <option value="">Seluruh Divisi PT. INTI</option>
    @foreach ($divisions as $division)
      <option value="{{ $division->id }}" @selected($divisionId == $division->id)>{{ $division->nama }}</option>
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
  <button type="submit" class="px-4 py-2 rounded-lg bg-primary-container text-on-primary font-title-sm text-title-sm">Cari</button>
</form>

<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-space-base mb-space-lg">
  <div class="bg-surface-container-lowest rounded-xl shadow-sm p-space-base">
    <div class="flex items-center justify-between">
      <span class="font-label-sm text-label-sm text-on-surface-variant uppercase tracking-wider">Personel Tercatat</span>
      <span class="material-symbols-outlined text-[20px] text-primary">groups</span>
    </div>
    <p class="font-headline-md text-headline-md text-primary mt-1">{{ $totalPersonel }}</p>
  </div>
  <div class="bg-surface-container-lowest rounded-xl shadow-sm p-space-base">
    <div class="flex items-center justify-between">
      <span class="font-label-sm text-label-sm text-on-surface-variant uppercase tracking-wider">Lembur Pending</span>
      <span class="material-symbols-outlined text-[20px] text-error">pending_actions</span>
    </div>
    <p class="font-headline-md text-headline-md text-error mt-1">{{ $totalJamLemburPending }} <span class="font-body-md text-body-md text-on-surface-variant">Jam</span></p>
  </div>
  <div class="bg-surface-container-lowest rounded-xl shadow-sm p-space-base">
    <div class="flex items-center justify-between">
      <span class="font-label-sm text-label-sm text-on-surface-variant uppercase tracking-wider">Lembur Disetujui</span>
      <span class="material-symbols-outlined text-[20px] text-primary">more_time</span>
    </div>
    <p class="font-headline-md text-headline-md text-primary mt-1">{{ $totalJamLemburDisetujui }} <span class="font-body-md text-body-md text-on-surface-variant">Jam</span></p>
  </div>
  <div class="bg-surface-container-lowest rounded-xl shadow-sm p-space-base">
    <div class="flex items-center justify-between">
      <span class="font-label-sm text-label-sm text-on-surface-variant uppercase tracking-wider">Hari Telat</span>
      <span class="material-symbols-outlined text-[20px] text-error">timer_off</span>
    </div>
    <p class="font-headline-md text-headline-md text-primary mt-1">{{ $totalHariTelat }} <span class="font-body-md text-body-md text-on-surface-variant">Hari</span></p>
  </div>
</div>

{{-- Tren Kehadiran Mingguan - SVG polyline murni, data real dari attendance_logs --}}
<div class="bg-surface-container-lowest rounded-xl shadow-sm p-space-lg mb-space-lg">
  <div class="flex items-center gap-2 pb-space-base border-b border-surface-container-low">
    <span class="material-symbols-outlined text-primary text-[22px]">show_chart</span>
    <h2 class="font-headline-md text-headline-md text-primary">Tren Kehadiran Mingguan</h2>
  </div>

  <div class="pt-space-base overflow-x-auto">
    <svg viewBox="0 0 {{ $lebarChart }} {{ $tinggiChart }}" class="w-full min-w-[420px] h-36" preserveAspectRatio="none">
      <line x1="20" y1="20" x2="{{ $lebarChart - 20 }}" y2="20" stroke="#eff4ff" stroke-width="1" />
      <line x1="20" y1="70" x2="{{ $lebarChart - 20 }}" y2="70" stroke="#eff4ff" stroke-width="1" />
      <line x1="20" y1="120" x2="{{ $lebarChart - 20 }}" y2="120" stroke="#c3c6ce" stroke-width="1" />

      <polyline points="{{ $svgPolyline }}" fill="none" stroke="#0f2942" stroke-width="2.5" stroke-linejoin="round" stroke-linecap="round" />

      @foreach ($svgPoints as $p)
        <circle cx="{{ $p['x'] }}" cy="{{ $p['y'] }}" r="4" fill="#0f2942" />
        <text x="{{ $p['x'] }}" y="{{ $p['y'] - 10 }}" text-anchor="middle" font-size="11" fill="#001428" font-weight="600">{{ $p['rate'] }}%</text>
        <text x="{{ $p['x'] }}" y="134" text-anchor="middle" font-size="10" fill="#43474d">{{ $p['label'] }}</text>
      @endforeach
    </svg>
  </div>
  <p class="font-body-sm text-body-sm text-on-surface-variant pt-space-sm">
    Persentase kehadiran per minggu (hari hadir tercatat / total hari kerja Senin-Jumat &times; jumlah karyawan pada cakupan filter).
  </p>
</div>

@if ($totalJamLemburPending > 0)
<form method="POST" action="{{ route('team-recap.approve-all') }}" class="mb-space-lg" onsubmit="return confirm('Setujui SEMUA lembur pending pada filter ini sekaligus?')">
  @csrf
  <input type="hidden" name="division_id" value="{{ $divisionId }}">
  <input type="hidden" name="bulan" value="{{ $bulan }}">
  <input type="hidden" name="tahun" value="{{ $tahun }}">
  <button type="submit" class="flex items-center gap-2 px-4 py-2.5 rounded-lg bg-primary-container text-on-primary font-title-sm text-title-sm shadow-sm hover:bg-primary transition-colors w-fit">
    <span class="material-symbols-outlined text-[18px]">done_all</span> Setujui Semua Pending
  </button>
</form>
@endif

<div class="bg-surface-container-lowest rounded-xl shadow-sm overflow-x-auto">
  <table class="w-full text-left min-w-[900px]">
    <thead>
      <tr class="bg-surface-container-low text-on-surface-variant font-label-sm text-label-sm uppercase tracking-wider">
        <th class="py-3 px-space-base rounded-l-lg">Tanggal</th>
        <th class="py-3 px-space-base">Karyawan</th>
        <th class="py-3 px-space-base">Divisi</th>
        <th class="py-3 px-space-base">Jam Pulang</th>
        <th class="py-3 px-space-base">Lembur</th>
        <th class="py-3 px-space-base">Status</th>
        <th class="py-3 px-space-base text-right rounded-r-lg">Aksi</th>
      </tr>
    </thead>
    <tbody class="divide-y divide-surface-container-low font-body-md text-body-md">
      @forelse ($logs as $log)
      <tr class="hover:bg-surface-container-low/40 transition-colors">
        <td class="py-space-base px-space-base">{{ $log->tanggal->translatedFormat('d M Y') }}</td>
        <td class="py-space-base px-space-base font-title-sm text-title-sm text-primary">{{ $log->employee->nama }}</td>
        <td class="py-space-base px-space-base"><span class="px-2 py-0.5 rounded bg-surface-container text-primary font-label-sm text-xs font-semibold">{{ $log->employee->division->nama }}</span></td>
        <td class="py-space-base px-space-base tabular-nums">{{ \Carbon\Carbon::parse($log->jam_pulang)->format('H:i') }}</td>
        <td class="py-space-base px-space-base tabular-nums font-title-sm text-title-sm text-primary">{{ $log->lemburFormat() }}</td>
        <td class="py-space-base px-space-base">
          @if ($log->status_lembur === 'disetujui')
            <span class="px-2 py-0.5 rounded-full bg-secondary-container/50 text-secondary font-label-sm text-label-sm font-semibold">Disetujui</span>
          @elseif ($log->status_lembur === 'ditolak')
            <span class="px-2 py-0.5 rounded-full bg-error-container text-error font-label-sm text-label-sm font-semibold">Ditolak</span>
          @else
            <span class="px-2 py-0.5 rounded-full bg-surface-container text-on-surface-variant font-label-sm text-label-sm font-semibold">Pending</span>
          @endif
        </td>
        <td class="py-space-base px-space-base text-right space-x-3 whitespace-nowrap">
          @if ($log->status_lembur === 'pending' && $log->butuhOtorisasiKhusus())
            <span class="px-2 py-0.5 rounded-full bg-tertiary-container text-on-tertiary font-label-sm text-label-sm font-semibold">Perlu Otorisasi Direktur</span>
          @elseif ($log->status_lembur === 'pending')
            <form action="{{ route('team-recap.approve', $log) }}" method="POST" class="inline">
              @csrf
              <button type="submit" class="text-secondary font-title-sm text-title-sm">Setuju</button>
            </form>
            <form action="{{ route('team-recap.reject', $log) }}" method="POST" class="inline" onsubmit="return confirm('Tolak lembur ini?')">
              @csrf
              <button type="submit" class="text-error font-title-sm text-title-sm">Tolak</button>
            </form>
          @else
            <span class="font-body-sm text-body-sm text-on-surface-variant">
              oleh {{ $log->approver?->name ?? '-' }}
            </span>
          @endif
        </td>
      </tr>
      @empty
      <tr><td colspan="7" class="py-8 px-4 text-center text-on-surface-variant">Tidak ada lembur tercatat untuk filter ini.</td></tr>
      @endforelse
    </tbody>
  </table>
</div>

<div class="mt-space-base">{{ $logs->links() }}</div>
@endsection
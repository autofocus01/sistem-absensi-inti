@extends('layouts.absensi')
@section('title', 'Rekapitulasi Tim')

@section('content')
<div class="flex flex-col sm:flex-row sm:items-center justify-between gap-space-base pb-space-lg">
  <div class="flex flex-col gap-1">
    <span class="font-label-sm text-label-sm text-on-surface-variant uppercase tracking-wider">PERSETUJUAN LEMBUR</span>
    <h1 class="font-headline-lg text-headline-lg text-primary tracking-tight">Rekapitulasi Tim</h1>
    <p class="font-body-md text-body-md text-on-surface-variant">{{ $totalLogLembur }} baris lembur tercatat pada filter ini.</p>
  </div>
</div>

<form method="GET" class="flex flex-col sm:flex-row gap-2 mb-space-lg bg-surface-container-lowest rounded-xl shadow-sm p-space-sm">
  <input type="text" name="q" value="{{ $q }}" placeholder="Cari nama atau NIPEG..."
         class="flex-1 rounded-lg border-0 bg-surface-container-low font-body-md text-body-md focus:ring-2 focus:ring-primary-container">
  <select name="division_id" onchange="this.form.submit()" class="rounded-lg border-0 bg-surface-container-low font-body-md text-body-md">
    <option value="">Semua Divisi</option>
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
      <span class="font-label-sm text-label-sm text-on-surface-variant uppercase tracking-wider">Jam Lembur Pending</span>
      <span class="material-symbols-outlined text-[20px] text-error">pending_actions</span>
    </div>
    <p class="font-headline-md text-headline-md text-primary mt-1">{{ $totalJamLemburPending }} <span class="font-body-md text-body-md text-on-surface-variant">Jam</span></p>
  </div>
  <div class="bg-surface-container-lowest rounded-xl shadow-sm p-space-base">
    <div class="flex items-center justify-between">
      <span class="font-label-sm text-label-sm text-on-surface-variant uppercase tracking-wider">Jam Lembur Disetujui</span>
      <span class="material-symbols-outlined text-[20px] text-secondary">task_alt</span>
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

{{-- Tren Kehadiran Mingguan & Status Lembur --}}
<div class="grid grid-cols-1 lg:grid-cols-3 gap-space-md mb-space-lg">
  <div class="lg:col-span-2 bg-surface-container-lowest rounded-xl shadow-sm p-space-lg">
    <div class="flex items-center gap-2 pb-space-base border-b border-surface-container-low">
      <span class="material-symbols-outlined text-primary text-[22px]">show_chart</span>
      <h2 class="font-headline-md text-headline-md text-primary">Tren Kehadiran Mingguan</h2>
    </div>
    <div class="pt-space-base h-72">
      <canvas id="chart-tren-mingguan"></canvas>
    </div>
    <p class="font-body-sm text-body-sm text-on-surface-variant pt-space-sm">
      Persentase kehadiran per minggu (hari hadir tercatat / total hari kerja Senin-Jumat &times; jumlah karyawan pada cakupan filter).
      Skala sumbu vertikal otomatis nyesuain rentang data ({{ $rateMin }}%&ndash;{{ $rateMax }}%), bukan tetap 0-100%.
    </p>
  </div>

  <div class="bg-surface-container-lowest rounded-xl shadow-sm p-space-lg">
    <div class="flex items-center gap-2 pb-space-base border-b border-surface-container-low">
      <span class="material-symbols-outlined text-primary text-[22px]">donut_large</span>
      <h2 class="font-headline-md text-headline-md text-primary">Status Lembur</h2>
    </div>
    <div class="pt-space-base h-72">
      <canvas id="chart-status-lembur"></canvas>
    </div>
  </div>
</div>

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
        @php
          $employee = $log->user?->employee;
          $division = $log->division ?? $employee?->division;
          $attendance = $log->attendanceLog;
          $recognizedMinutes = (int) ($log->recognized_minutes ?? 0);

          $statusLabel = match ($log->status ?? null) {
              'PENDING_VP' => 'Pending VP',
              'APPROVED_VP' => 'Disetujui VP',
              'VERIFIED_HR' => 'Verified HR',
              'REJECTED_VP' => 'Ditolak VP',
              'REJECTED_HR' => 'Ditolak HR',
              default => $log->status ?? 'Pending',
          };

          $statusClass = match ($log->status ?? null) {
              'VERIFIED_HR', 'APPROVED_VP' => 'bg-secondary-container/50 text-secondary',
              'REJECTED_VP', 'REJECTED_HR' => 'bg-error-container text-error',
              default => 'bg-surface-container text-on-surface-variant',
          };

          $approverName = $log->hrVerifier?->name
              ?? $log->vpApprover?->name
              ?? '-';
        @endphp

        <tr class="hover:bg-surface-container-low/40 transition-colors">
          <td class="py-space-base px-space-base">
            {{ $log->overtime_date ? \Carbon\Carbon::parse($log->overtime_date)->translatedFormat('d M Y') : '-' }}
          </td>

          <td class="py-space-base px-space-base">
            @if ($employee)
              <div class="font-title-sm text-title-sm text-primary">{{ $employee->nama }}</div>
              <div class="font-label-sm text-label-sm text-on-surface-variant">{{ $employee->nipeg }}</div>
            @else
              <span class="text-error">Data karyawan tidak ditemukan</span>
            @endif
          </td>

          <td class="py-space-base px-space-base">
            @if ($division)
              <span class="px-2 py-0.5 rounded bg-surface-container text-primary font-label-sm text-xs font-semibold">
                {{ $division->nama }}
              </span>
            @else
              <span class="text-error">Divisi tidak ditemukan</span>
            @endif
          </td>

          <td class="py-space-base px-space-base tabular-nums">
            {{ $attendance?->jam_pulang ? \Carbon\Carbon::parse($attendance->jam_pulang)->format('H:i') : '-' }}
          </td>

          <td class="py-space-base px-space-base tabular-nums font-title-sm text-title-sm text-primary">
            {{ intdiv($recognizedMinutes, 60) }}j {{ str_pad((string) ($recognizedMinutes % 60), 2, '0', STR_PAD_LEFT) }}m
          </td>

          <td class="py-space-base px-space-base">
            <span class="px-2 py-0.5 rounded-full {{ $statusClass }} font-label-sm text-label-sm font-semibold">
              {{ $statusLabel }}
            </span>
          </td>

          <td class="py-space-base px-space-base text-right">
            <span class="font-body-sm text-body-sm text-on-surface-variant">
              {{ $approverName }}
            </span>
          </td>
        </tr>
      @empty
        <tr>
          <td colspan="7" class="py-8 px-4 text-center text-on-surface-variant">
            Tidak ada lembur tercatat untuk filter ini.
          </td>
        </tr>
      @endforelse
    </tbody>
  </table>
</div>

<div class="mt-space-base">{{ $logs->links() }}</div>
@endsection

@section('scripts')
<script>
  const trenMingguan = @json(collect($trenMingguan)->map(fn ($m) => ['label' => $m['label'] . ' (' . $m['rentang'] . ')', 'rate' => $m['rate']]));
  const statusLembur = @json($statusLemburCounts);

  new Chart(document.getElementById('chart-tren-mingguan'), {
    type: 'line',
    data: {
      labels: trenMingguan.map(m => m.label),
      datasets: [{
        label: 'Kehadiran (%)',
        data: trenMingguan.map(m => m.rate),
        borderColor: chartPalet.primary,
        backgroundColor: chartPalet.primary,
        tension: 0.35,
        fill: false,
        pointRadius: 5,
        pointHoverRadius: 7,
      }],
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      plugins: { legend: { display: false } },
      scales: {
        y: { min: {{ $rateMin }}, max: {{ $rateMax }}, grid: { color: chartPalet.grid }, title: { display: true, text: 'Kehadiran (%)' } },
        x: { grid: { display: false } },
      },
    },
  });

  const totalStatusLembur = Object.values(statusLembur).reduce((a, b) => a + b, 0);
  if (totalStatusLembur > 0) {
    new Chart(document.getElementById('chart-status-lembur'), {
      type: 'doughnut',
      data: {
        labels: Object.keys(statusLembur),
        datasets: [{
          data: Object.values(statusLembur),
          backgroundColor: [chartPalet.outline, chartPalet.secondary, chartPalet.error],
          borderWidth: 2,
          borderColor: '#ffffff',
        }],
      },
      options: {
        cutout: '65%',
        responsive: true,
        maintainAspectRatio: false,
        plugins: { legend: { position: 'bottom', labels: { boxWidth: 10, padding: 12 } } },
      },
    });
  }
</script>
@endsection
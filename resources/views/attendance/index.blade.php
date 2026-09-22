@extends('layouts.absensi')
@section('title', 'Rekap Absensi')

@section('content')
<div class="flex flex-col sm:flex-row sm:items-center justify-between gap-space-base pb-space-lg">
  <div class="flex flex-col gap-1">
    <span class="font-label-sm text-label-sm text-on-surface-variant uppercase tracking-wider">REKAP KEHADIRAN BULANAN</span>
    <h1 class="font-headline-lg text-headline-lg text-primary tracking-tight">Rekap Absensi</h1>
    <p class="font-body-md text-body-md text-on-surface-variant">{{ $recaps->total() }} rekap tercatat, terverifikasi otomatis lewat sistem.</p>
  </div>
  <a href="{{ route('attendance.create') }}" class="flex items-center gap-2 px-4 py-2.5 rounded-lg bg-primary-container text-on-primary font-title-sm text-title-sm shadow-sm hover:bg-primary transition-colors w-fit">
    <span class="material-symbols-outlined text-[18px]">add_task</span> Input Rekap
  </a>
</div>

<form method="GET" class="flex flex-col sm:flex-row gap-2 mb-space-lg bg-surface-container-lowest rounded-xl shadow-sm p-space-sm">
  <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari nama atau NIPEG..."
         class="flex-1 rounded-lg border-0 bg-surface-container-low font-body-md text-body-md focus:ring-2 focus:ring-primary-container">
  <select name="division_id" onchange="this.form.submit()" class="rounded-lg border-0 bg-surface-container-low font-body-md text-body-md">
    <option value="">Semua Divisi</option>
    @foreach ($divisions as $division)
      <option value="{{ $division->id }}" @selected(request('division_id') == $division->id)>{{ $division->nama }}</option>
    @endforeach
  </select>
  <select name="bulan" onchange="this.form.submit()" class="rounded-lg border-0 bg-surface-container-low font-body-md text-body-md">
    <option value="">Semua Bulan</option>
    @foreach (range(1, 12) as $m)
      <option value="{{ $m }}" @selected(request('bulan') == $m)>{{ \Carbon\Carbon::create()->month($m)->translatedFormat('F') }}</option>
    @endforeach
  </select>
  <select name="tahun" onchange="this.form.submit()" class="rounded-lg border-0 bg-surface-container-low font-body-md text-body-md">
    <option value="">Semua Tahun</option>
    @foreach ($tahunTersedia as $tahun)
      <option value="{{ $tahun }}" @selected(request('tahun') == $tahun)>{{ $tahun }}</option>
    @endforeach
  </select>
  <select name="lokasi" onchange="this.form.submit()" class="rounded-lg border-0 bg-surface-container-low font-body-md text-body-md">
    <option value="">Semua Lokasi</option>
    <option value="bandung" @selected(request('lokasi') === 'bandung')>Bandung</option>
    <option value="jakarta" @selected(request('lokasi') === 'jakarta')>Jakarta</option>
  </select>
  <button type="submit" class="px-4 py-2 rounded-lg bg-primary-container text-on-primary font-title-sm text-title-sm">Cari</button>
  @if (request('q') || request('bulan') || request('tahun') || request('lokasi') || request('division_id'))
    <a href="{{ route('attendance.index') }}" class="px-4 py-2 rounded-lg text-on-surface-variant font-title-sm text-title-sm text-center">Reset</a>
  @endif
</form>

{{-- KPI ringkas untuk scope tahun/bulan/divisi yang lagi difilter --}}
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-space-base mb-space-lg">
  <div class="bg-surface-container-lowest rounded-xl shadow-sm p-space-base">
    <div class="flex items-center justify-between">
      <span class="font-label-sm text-label-sm text-on-surface-variant uppercase tracking-wider">Rekap pada Scope Ini</span>
      <span class="material-symbols-outlined text-[20px] text-primary">fact_check</span>
    </div>
    <p class="font-headline-md text-headline-md text-primary mt-1">{{ number_format($totalRekapChart) }}</p>
  </div>
  <div class="bg-surface-container-lowest rounded-xl shadow-sm p-space-base">
    <div class="flex items-center justify-between">
      <span class="font-label-sm text-label-sm text-on-surface-variant uppercase tracking-wider">Rata-rata Kehadiran</span>
      <span class="material-symbols-outlined text-[20px] text-primary">how_to_reg</span>
    </div>
    <p class="font-headline-md text-headline-md text-primary mt-1">{{ $rataKehadiranChart }}%</p>
  </div>
  <div class="bg-surface-container-lowest rounded-xl shadow-sm p-space-base">
    <div class="flex items-center justify-between">
      <span class="font-label-sm text-label-sm text-on-surface-variant uppercase tracking-wider">Total Alpha</span>
      <span class="material-symbols-outlined text-[20px] {{ $totalAlphaChart > 0 ? 'text-error' : 'text-primary' }}">person_off</span>
    </div>
    <p class="font-headline-md text-headline-md {{ $totalAlphaChart > 0 ? 'text-error' : 'text-primary' }} mt-1">{{ $totalAlphaChart }}</p>
  </div>
  <div class="bg-surface-container-lowest rounded-xl shadow-sm p-space-base">
    <div class="flex items-center justify-between">
      <span class="font-label-sm text-label-sm text-on-surface-variant uppercase tracking-wider">Total Menit Telat</span>
      <span class="material-symbols-outlined text-[20px] text-primary">timer</span>
    </div>
    <p class="font-headline-md text-headline-md text-primary mt-1">{{ number_format($totalMenitTelatChart) }}</p>
  </div>
</div>

{{-- Grafik: tren tahunan + per divisi + sebaran lokasi --}}
<div class="bg-surface-container-lowest rounded-xl shadow-sm p-space-lg mb-space-lg">
  <div class="flex items-center justify-between pb-space-base border-b border-surface-container-low">
    <div class="flex items-center gap-2">
      <span class="material-symbols-outlined text-primary text-[22px]">trending_up</span>
      <h2 class="font-headline-md text-headline-md text-primary">Tren Rata-rata Kehadiran Tahun {{ $tahunChart }}</h2>
    </div>
  </div>
  <div class="pt-space-base h-64">
    <canvas id="chart-tren-tahunan"></canvas>
  </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-space-md mb-space-lg">
  <div class="bg-surface-container-lowest rounded-xl shadow-sm p-space-lg">
    <div class="flex items-center gap-2 pb-space-base border-b border-surface-container-low">
      <span class="material-symbols-outlined text-primary text-[22px]">bar_chart</span>
      <h2 class="font-headline-md text-headline-md text-primary">Rata-rata Kehadiran per Divisi</h2>
    </div>
    <div class="pt-space-base h-72">
      <canvas id="chart-kehadiran-divisi"></canvas>
    </div>
  </div>
  <div class="bg-surface-container-lowest rounded-xl shadow-sm p-space-lg">
    <div class="flex items-center gap-2 pb-space-base border-b border-surface-container-low">
      <span class="material-symbols-outlined text-primary text-[22px]">location_city</span>
      <h2 class="font-headline-md text-headline-md text-primary">Sebaran Lokasi Presensi</h2>
    </div>
    <div class="pt-space-base h-72">
      <canvas id="chart-lokasi-rekap"></canvas>
    </div>
  </div>
</div>

<div class="bg-surface-container-lowest rounded-xl shadow-sm overflow-x-auto">
  <table class="w-full text-left min-w-[980px]">
    <thead>
      <tr class="bg-surface-container-low text-on-surface-variant font-label-sm text-label-sm uppercase tracking-wider">
        <th class="py-3 px-space-base rounded-l-lg">Periode</th>
        <th class="py-3 px-space-base">Karyawan</th>
        <th class="py-3 px-space-base">Divisi</th>
        <th class="py-3 px-space-base">Lokasi</th>
        <th class="py-3 px-space-base">Hadir/Hari Kerja</th>
        <th class="py-3 px-space-base">Kehadiran</th>
        <th class="py-3 px-space-base">Menit Telat</th>
        <th class="py-3 px-space-base text-right rounded-r-lg">Aksi</th>
      </tr>
    </thead>
    <tbody class="divide-y divide-surface-container-low font-body-md text-body-md">
      @forelse ($recaps as $recap)
      @php($pct = $recap->persen_kehadiran)
      <tr class="hover:bg-surface-container-low/40 transition-colors">
        <td class="py-space-base px-space-base">{{ \Carbon\Carbon::create()->month($recap->bulan)->translatedFormat('F') }} {{ $recap->tahun }}</td>
        <td class="py-space-base px-space-base font-title-sm text-title-sm text-primary">{{ $recap->employee->nama }}</td>
        <td class="py-space-base px-space-base"><span class="px-2 py-0.5 rounded bg-surface-container text-primary font-label-sm text-xs font-semibold">{{ $recap->employee->division->nama }}</span></td>
        <td class="py-space-base px-space-base whitespace-nowrap">
          @if ($recap->lokasi_bandung > 0)
            <span class="inline-flex items-center gap-1 font-body-sm text-body-sm text-on-surface">
              <span class="material-symbols-outlined text-[14px] text-on-surface-variant">corporate_fare</span> Bandung ({{ $recap->lokasi_bandung }})
            </span>
          @endif
          @if ($recap->lokasi_bandung > 0 && $recap->lokasi_jakarta > 0)
            <br>
          @endif
          @if ($recap->lokasi_jakarta > 0)
            <span class="inline-flex items-center gap-1 font-body-sm text-body-sm text-on-surface">
              <span class="material-symbols-outlined text-[14px] text-on-surface-variant">apartment</span> Jakarta ({{ $recap->lokasi_jakarta }})
            </span>
          @endif
          @if ($recap->lokasi_bandung == 0 && $recap->lokasi_jakarta == 0)
            <span class="font-body-sm text-body-sm text-on-surface-variant">-</span>
          @endif
        </td>
        <td class="py-space-base px-space-base tabular-nums">{{ $recap->hadir }}/{{ $recap->hari_kerja }}</td>
        <td class="py-space-base px-space-base">
          <div class="flex items-center gap-2">
            <span class="font-title-sm text-title-sm tabular-nums {{ $pct >= 95 ? 'text-secondary' : ($pct >= 80 ? 'text-primary' : 'text-error') }}">{{ $pct }}%</span>
            <span class="inline-flex items-center px-2 py-0.5 rounded-full font-label-sm text-label-sm font-semibold
              {{ $pct >= 95 ? 'bg-secondary-container/50 text-secondary' : ($pct >= 80 ? 'bg-surface-container text-on-surface' : 'bg-error-container text-error') }}">
              {{ $pct >= 95 ? 'Sangat Baik' : ($pct >= 80 ? 'Normal' : 'Perlu Ditinjau') }}
            </span>
          </div>
        </td>
        <td class="py-space-base px-space-base tabular-nums">{{ $recap->menit_telat }}</td>
        <td class="py-space-base px-space-base text-right space-x-3 whitespace-nowrap">
          <a href="{{ route('attendance.edit', $recap) }}" class="text-secondary font-title-sm text-title-sm">Edit</a>
          <form action="{{ route('attendance.destroy', $recap) }}" method="POST" class="inline" onsubmit="return confirm('Hapus rekap ini?')">
            @csrf @method('DELETE')
            <button type="submit" class="text-error font-title-sm text-title-sm">Hapus</button>
          </form>
        </td>
      </tr>
      @empty
      <tr><td colspan="8" class="py-8 px-4 text-center text-on-surface-variant">Belum ada rekap absensi.</td></tr>
      @endforelse
    </tbody>
  </table>
</div>

<div class="mt-space-base">{{ $recaps->links() }}</div>
@endsection

@section('scripts')
<script>
  const trenTahunan   = @json($trenBulananTahunIni);
  const kehadiranDivisi = @json($kehadiranPerDivisi);
  const lokasiRekap   = { bandung: {{ $totalBandungChart }}, jakarta: {{ $totalJakartaChart }} };

  new Chart(document.getElementById('chart-tren-tahunan'), {
    type: 'line',
    data: {
      labels: trenTahunan.map(t => t.label),
      datasets: [{
        label: 'Rata-rata Kehadiran (%)',
        data: trenTahunan.map(t => t.ada_data ? t.rata_kehadiran : null),
        borderColor: chartPalet.primary,
        backgroundColor: chartPalet.primary,
        tension: 0.35,
        spanGaps: true,
        pointRadius: 4,
        pointHoverRadius: 6,
      }],
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      plugins: { legend: { display: false } },
      scales: {
        y: { min: 0, max: 100, grid: { color: chartPalet.grid }, title: { display: true, text: 'Kehadiran (%)' } },
        x: { grid: { display: false } },
      },
    },
  });

  new Chart(document.getElementById('chart-kehadiran-divisi'), {
    type: 'bar',
    data: {
      labels: kehadiranDivisi.map(d => d.nama),
      datasets: [{
        label: 'Rata-rata Kehadiran (%)',
        data: kehadiranDivisi.map(d => d.rata_kehadiran),
        backgroundColor: chartPalet.secondary,
        borderRadius: 6,
        maxBarThickness: 32,
      }],
    },
    options: {
      indexAxis: 'y',
      responsive: true,
      maintainAspectRatio: false,
      plugins: { legend: { display: false } },
      scales: {
        x: { min: 0, max: 100, grid: { color: chartPalet.grid } },
        y: { grid: { display: false } },
      },
    },
  });

  if (lokasiRekap.bandung + lokasiRekap.jakarta > 0) {
    new Chart(document.getElementById('chart-lokasi-rekap'), {
      type: 'doughnut',
      data: {
        labels: ['Bandung', 'Jakarta'],
        datasets: [{
          data: [lokasiRekap.bandung, lokasiRekap.jakarta],
          backgroundColor: [chartPalet.primary, chartPalet.secondary],
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
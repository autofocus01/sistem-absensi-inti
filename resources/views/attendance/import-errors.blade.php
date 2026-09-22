@extends('layouts.absensi')
@section('title', 'Review Anomali Import')

@section('content')
<div class="flex flex-col gap-1 pb-space-lg">
  <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full bg-error text-white font-label-sm text-label-sm uppercase tracking-wider w-fit">
    <span class="material-symbols-outlined text-[14px]">fact_check</span> KENDALI MUTU DATA
  </span>
  <h1 class="font-headline-lg text-headline-lg text-primary tracking-tight">Review Anomali Import</h1>
  <p class="font-body-md text-body-md text-on-surface-variant max-w-2xl">
    Baris dari Excel yang ditolak sistem karena melanggar aturan wajar (menit telat tidak masuk akal,
    total hari tidak sama dengan hari kerja, dsb). Tinjau satu per satu, koreksi angkanya lewat "Buat Rekap",
    atau tandai selesai kalau memang mau diabaikan.
  </p>
</div>

{{-- Ringkasan status & jenis anomali --}}
<div class="grid grid-cols-1 sm:grid-cols-2 gap-space-md my-space-lg">
  <div class="bg-surface-container-lowest rounded-xl shadow-sm p-space-base">
    <div class="flex items-center justify-between">
      <span class="font-label-sm text-label-sm text-on-surface-variant uppercase tracking-wider">Anomali Pending</span>
      <span class="material-symbols-outlined text-[20px] {{ $totalPending > 0 ? 'text-error' : 'text-primary' }}">pending_actions</span>
    </div>
    <p class="font-headline-md text-headline-md {{ $totalPending > 0 ? 'text-error' : 'text-primary' }} mt-1">{{ $totalPending }}</p>
  </div>
  <div class="bg-surface-container-lowest rounded-xl shadow-sm p-space-base">
    <div class="flex items-center justify-between">
      <span class="font-label-sm text-label-sm text-on-surface-variant uppercase tracking-wider">Sudah Selesai</span>
      <span class="material-symbols-outlined text-[20px] text-secondary">task_alt</span>
    </div>
    <p class="font-headline-md text-headline-md text-secondary mt-1">{{ $totalSelesai }}</p>
  </div>
</div>

@if (($totalPending + $totalSelesai) > 0)
<div class="grid grid-cols-1 lg:grid-cols-2 gap-space-md mb-space-lg">
  <div class="bg-surface-container-lowest rounded-xl shadow-sm p-space-lg">
    <div class="flex items-center gap-2 pb-space-base border-b border-surface-container-low">
      <span class="material-symbols-outlined text-primary text-[22px]">donut_large</span>
      <h2 class="font-headline-md text-headline-md text-primary">Status Anomali</h2>
    </div>
    <div class="pt-space-base h-64">
      <canvas id="chart-status-anomali"></canvas>
    </div>
  </div>
  <div class="bg-surface-container-lowest rounded-xl shadow-sm p-space-lg">
    <div class="flex items-center gap-2 pb-space-base border-b border-surface-container-low">
      <span class="material-symbols-outlined text-primary text-[22px]">bar_chart</span>
      <h2 class="font-headline-md text-headline-md text-primary">Sebaran Jenis Anomali</h2>
    </div>
    <div class="pt-space-base h-64">
      <canvas id="chart-jenis-anomali"></canvas>
    </div>
  </div>
</div>
@endif

<div class="bg-surface-container-lowest rounded-xl shadow-sm overflow-x-auto">
  <table class="w-full text-left min-w-[920px]">
    <thead>
      <tr class="bg-surface-container-low text-on-surface-variant font-label-sm text-label-sm uppercase tracking-wider">
        <th class="py-3 px-space-base rounded-l-lg">Baris</th>
        <th class="py-3 px-space-base">Jenis</th>
        <th class="py-3 px-space-base">NIPEG</th>
        <th class="py-3 px-space-base">Nama</th>
        <th class="py-3 px-space-base">Periode</th>
        <th class="py-3 px-space-base">Alasan Ditolak</th>
        <th class="py-3 px-space-base">Status</th>
        <th class="py-3 px-space-base text-right rounded-r-lg">Aksi</th>
      </tr>
    </thead>
    <tbody class="divide-y divide-surface-container-low font-body-md text-body-md">
      @forelse ($importErrors as $error)
      <tr class="hover:bg-surface-container-low/40 transition-colors">
        <td class="py-space-base px-space-base tabular-nums">{{ $error->baris_excel ?? '-' }}</td>
        <td class="py-space-base px-space-base">
          @if ($error->jenis === 'absensi_harian')
            <span class="px-2 py-0.5 rounded bg-surface-container-high text-on-surface-variant font-label-sm text-xs font-semibold">Absensi Harian</span>
          @else
            <span class="px-2 py-0.5 rounded bg-surface-container text-primary font-label-sm text-xs font-semibold">Rekap Bulanan</span>
          @endif
        </td>
        <td class="py-space-base px-space-base tabular-nums">{{ $error->nipeg }}</td>
        <td class="py-space-base px-space-base font-title-sm text-title-sm text-primary">{{ $error->nama ?? '-' }}</td>
        <td class="py-space-base px-space-base">{{ \Carbon\Carbon::create()->month($error->bulan)->translatedFormat('F') }} {{ $error->tahun }}</td>
        <td class="py-space-base px-space-base max-w-xs text-error font-body-sm text-body-sm">{{ $error->alasan }}</td>
        <td class="py-space-base px-space-base">
          @if ($error->status === 'selesai')
            <span class="px-2 py-0.5 rounded-full bg-secondary-container/50 text-secondary font-label-sm text-label-sm font-semibold">Selesai</span>
          @else
            <span class="px-2 py-0.5 rounded-full bg-error-container text-error font-label-sm text-label-sm font-semibold">Pending</span>
          @endif
        </td>
        <td class="py-space-base px-space-base text-right space-x-3 whitespace-nowrap">
          @if ($error->status !== 'selesai')
            @if ($error->jenis === 'rekap_bulanan')
              <a href="{{ route('attendance.create', ['from_error' => $error->id]) }}" class="text-secondary font-title-sm text-title-sm">Buat Rekap</a>
            @else
              <span class="font-body-sm text-body-sm text-on-surface-variant" title="Perbaiki di file sumber (mesin fingerprint/Face ID), lalu jalankan ulang import:absensi-harian">
                Perbaiki di file &amp; import ulang
              </span>
            @endif
            <form action="{{ route('attendance.import-errors.resolve', $error) }}" method="POST" class="inline" onsubmit="return confirm('Tandai baris ini selesai tanpa membuat rekap?')">
              @csrf
              <button type="submit" class="text-on-surface-variant font-title-sm text-title-sm">Tandai Selesai</button>
            </form>
          @endif
        </td>
      </tr>
      @empty
      <tr><td colspan="8" class="py-8 px-4 text-center text-on-surface-variant">Tidak ada anomali. Semua data import bersih.</td></tr>
      @endforelse
    </tbody>
  </table>
</div>

<div class="mt-space-base">{{ $importErrors->links() }}</div>
@endsection

@section('scripts')
<script>
  const sebaranJenisAnomali = @json($sebaranJenis);
  const statusAnomali = { pending: {{ $totalPending }}, selesai: {{ $totalSelesai }} };

  if (statusAnomali.pending + statusAnomali.selesai > 0) {
    new Chart(document.getElementById('chart-status-anomali'), {
      type: 'doughnut',
      data: {
        labels: ['Pending', 'Selesai'],
        datasets: [{
          data: [statusAnomali.pending, statusAnomali.selesai],
          backgroundColor: [chartPalet.error, chartPalet.secondary],
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

    new Chart(document.getElementById('chart-jenis-anomali'), {
      type: 'bar',
      data: {
        labels: Object.keys(sebaranJenisAnomali),
        datasets: [{
          label: 'Jumlah Baris',
          data: Object.values(sebaranJenisAnomali),
          backgroundColor: [chartPalet.outline, chartPalet.primary],
          borderRadius: 6,
          maxBarThickness: 48,
        }],
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: { legend: { display: false } },
        scales: {
          x: { grid: { display: false } },
          y: { beginAtZero: true, ticks: { precision: 0 }, grid: { color: chartPalet.grid } },
        },
      },
    });
  }
</script>
@endsection
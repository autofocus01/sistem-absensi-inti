@extends('layouts.absensi')
@section('title', 'Data Karyawan')

@section('content')
<div class="flex flex-col sm:flex-row sm:items-center justify-between gap-space-base pb-space-lg">
  <div class="flex flex-col gap-1">
    <span class="font-label-sm text-label-sm text-on-surface-variant uppercase tracking-wider">DATA MASTER &bull; SDM PT. INTI</span>
    <h1 class="font-headline-lg text-headline-lg text-primary tracking-tight">Data Karyawan</h1>
    <p class="font-body-md text-body-md text-on-surface-variant">{{ $employees->total() }} karyawan terdaftar di sistem.</p>
  </div>
  <a href="{{ route('employees.create') }}" class="flex items-center gap-2 px-4 py-2.5 rounded-lg bg-primary-container text-on-primary font-title-sm text-title-sm shadow-sm hover:bg-primary transition-colors w-fit">
    <span class="material-symbols-outlined text-[18px]">person_add</span> Tambah Karyawan
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
  <button type="submit" class="px-4 py-2 rounded-lg bg-primary-container text-on-primary font-title-sm text-title-sm">Cari</button>
  @if (request('q') || request('division_id'))
    <a href="{{ route('employees.index') }}" class="px-4 py-2 rounded-lg text-on-surface-variant font-title-sm text-title-sm text-center">Reset</a>
  @endif
</form>

<div class="bg-surface-container-lowest rounded-xl shadow-sm overflow-x-auto">
  <table class="w-full text-left min-w-[820px]">
    <thead>
      <tr class="bg-surface-container-low text-on-surface-variant font-label-sm text-label-sm uppercase tracking-wider">
        <th class="py-3 px-space-base rounded-l-lg">NIPEG</th>
        <th class="py-3 px-space-base">Nama</th>
        <th class="py-3 px-space-base">L/P</th>
        <th class="py-3 px-space-base">Jabatan</th>
        <th class="py-3 px-space-base">No. HP</th>
        <th class="py-3 px-space-base">Divisi</th>
        <th class="py-3 px-space-base text-right rounded-r-lg">Aksi</th>
      </tr>
    </thead>
    <tbody class="divide-y divide-surface-container-low font-body-md text-body-md">
      @forelse ($employees as $employee)
      <tr class="hover:bg-surface-container-low/40 transition-colors">
        <td class="py-space-base px-space-base tabular-nums text-on-surface-variant">{{ $employee->nipeg }}</td>
        <td class="py-space-base px-space-base font-title-sm text-title-sm text-primary">{{ $employee->nama }}</td>
        <td class="py-space-base px-space-base">{{ $employee->jenis_kelamin ?: '-' }}</td>
        <td class="py-space-base px-space-base">{{ $employee->jabatan }}</td>
        <td class="py-space-base px-space-base whitespace-nowrap">{{ $employee->no_hp ?: '-' }}</td>
        <td class="py-space-base px-space-base"><span class="px-2 py-0.5 rounded bg-surface-container text-primary font-label-sm text-xs font-semibold">{{ $employee->division->nama }}</span></td>
        <td class="py-space-base px-space-base text-right space-x-3 whitespace-nowrap">
          <a href="{{ route('employees.edit', $employee) }}" class="text-secondary font-title-sm text-title-sm">Edit</a>
          <form action="{{ route('employees.destroy', $employee) }}" method="POST" class="inline" onsubmit="return confirm('Hapus karyawan ini?')">
            @csrf @method('DELETE')
            <button type="submit" class="text-error font-title-sm text-title-sm">Hapus</button>
          </form>
        </td>
      </tr>
      @empty
      <tr><td colspan="7" class="py-8 px-4 text-center text-on-surface-variant">Belum ada data karyawan.</td></tr>
      @endforelse
    </tbody>
  </table>
</div>

<div class="mt-space-base">{{ $employees->links() }}</div>

{{-- Grafik gambaran seluruh karyawan --}}
<div class="grid grid-cols-1 lg:grid-cols-3 gap-space-md mt-space-lg">
  <div class="lg:col-span-2 bg-surface-container-lowest rounded-xl shadow-sm p-space-lg">
    <div class="flex items-center gap-2 pb-space-base border-b border-surface-container-low">
      <span class="material-symbols-outlined text-primary text-[22px]">account_tree</span>
      <h2 class="font-headline-md text-headline-md text-primary">Sebaran Karyawan per Divisi</h2>
    </div>
    <div class="pt-space-base h-72">
      <canvas id="chart-karyawan-divisi"></canvas>
    </div>
  </div>

  <div class="bg-surface-container-lowest rounded-xl shadow-sm p-space-lg">
    <div class="flex items-center gap-2 pb-space-base border-b border-surface-container-low">
      <span class="material-symbols-outlined text-primary text-[22px]">wc</span>
      <h2 class="font-headline-md text-headline-md text-primary">Komposisi Jenis Kelamin</h2>
    </div>
    <div class="pt-space-base h-72">
      <canvas id="chart-karyawan-gender"></canvas>
    </div>
  </div>
</div>
@endsection

@section('scripts')
<script>
  const sebaranDivisi = @json($sebaranDivisi);
  const sebaranGender = @json($sebaranGender);

  new Chart(document.getElementById('chart-karyawan-divisi'), {
    type: 'bar',
    data: {
      labels: sebaranDivisi.map(d => d.nama),
      datasets: [{
        label: 'Jumlah Karyawan',
        data: sebaranDivisi.map(d => d.jumlah),
        backgroundColor: chartPalet.primary,
        borderRadius: 6,
        maxBarThickness: 40,
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

  new Chart(document.getElementById('chart-karyawan-gender'), {
    type: 'doughnut',
    data: {
      labels: Object.keys(sebaranGender),
      datasets: [{
        data: Object.values(sebaranGender),
        backgroundColor: [chartPalet.primary, chartPalet.secondary, chartPalet.outline],
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
</script>
@endsection
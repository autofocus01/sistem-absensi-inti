@extends('layouts.absensi')
@section('title', 'Rekap Absensi')

@section('content')
<div class="flex items-center justify-between mb-6">
  <h1 class="text-2xl font-bold text-primary">Rekap Absensi Bulanan</h1>
  <a href="{{ route('attendance.create') }}" class="px-4 py-2 rounded-lg bg-primary-container text-white font-semibold">+ Input Rekap</a>
</div>

<div class="bg-surface-container-lowest rounded-xl shadow-sm overflow-x-auto">
  <table class="w-full text-left">
    <thead>
      <tr class="bg-surface-container-low text-on-surface-variant text-xs uppercase tracking-wider">
        <th class="py-3 px-4">Periode</th>
        <th class="py-3 px-4">Karyawan</th>
        <th class="py-3 px-4">Divisi</th>
        <th class="py-3 px-4">Hadir/Hari Kerja</th>
        <th class="py-3 px-4">% Kehadiran</th>
        <th class="py-3 px-4">Menit Telat</th>
        <th class="py-3 px-4 text-right">Aksi</th>
      </tr>
    </thead>
    <tbody class="divide-y divide-surface-container-low">
      @forelse ($recaps as $recap)
      <tr>
        <td class="py-3 px-4">{{ \Carbon\Carbon::create()->month($recap->bulan)->translatedFormat('F') }} {{ $recap->tahun }}</td>
        <td class="py-3 px-4 font-semibold">{{ $recap->employee->nama }}</td>
        <td class="py-3 px-4">{{ $recap->employee->division->nama }}</td>
        <td class="py-3 px-4 tabular-nums">{{ $recap->hadir }}/{{ $recap->hari_kerja }}</td>
        <td class="py-3 px-4 tabular-nums font-semibold text-secondary">{{ $recap->persen_kehadiran }}%</td>
        <td class="py-3 px-4 tabular-nums">{{ $recap->menit_telat }}</td>
        <td class="py-3 px-4 text-right space-x-2">
          <a href="{{ route('attendance.edit', $recap) }}" class="text-secondary font-semibold">Edit</a>
          <form action="{{ route('attendance.destroy', $recap) }}" method="POST" class="inline" onsubmit="return confirm('Hapus rekap ini?')">
            @csrf @method('DELETE')
            <button type="submit" class="text-error font-semibold">Hapus</button>
          </form>
        </td>
      </tr>
      @empty
      <tr><td colspan="7" class="py-6 px-4 text-center text-on-surface-variant">Belum ada rekap absensi.</td></tr>
      @endforelse
    </tbody>
  </table>
</div>

<div class="mt-4">{{ $recaps->links() }}</div>
@endsection
@extends('layouts.absensi')
@section('title', 'Data Divisi')

@section('content')
<div class="flex flex-col sm:flex-row sm:items-center justify-between gap-space-base pb-space-lg">
  <div class="flex flex-col gap-1">
    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full bg-primary-container text-surface-container-lowest font-label-sm text-label-sm uppercase tracking-wider w-fit">
      <span class="material-symbols-outlined text-[14px]">account_tree</span> STRUKTUR ORGANISASI
    </span>
    <h1 class="font-headline-lg text-headline-lg text-primary tracking-tight">Data Divisi</h1>
    <p class="font-body-md text-body-md text-on-surface-variant">{{ $divisions->count() }} divisi resmi terdaftar.</p>
  </div>
  <a href="{{ route('divisions.create') }}" class="flex items-center gap-2 px-4 py-2.5 rounded-lg bg-primary-container text-on-primary font-title-sm text-title-sm shadow-sm hover:bg-primary transition-colors w-fit">
    <span class="material-symbols-outlined text-[18px]">add_business</span> Tambah Divisi
  </a>
</div>

<div class="bg-surface-container-lowest rounded-xl shadow-sm overflow-x-auto">
  <table class="w-full text-left min-w-[560px]">
    <thead>
      <tr class="bg-surface-container-low text-on-surface-variant font-label-sm text-label-sm uppercase tracking-wider">
        <th class="py-3 px-space-base rounded-l-lg">Nama Divisi</th>
        <th class="py-3 px-space-base">Lini</th>
        <th class="py-3 px-space-base">Jumlah Karyawan</th>
        <th class="py-3 px-space-base text-right rounded-r-lg">Aksi</th>
      </tr>
    </thead>
    <tbody class="divide-y divide-surface-container-low font-body-md text-body-md">
      @forelse ($divisions as $division)
      <tr class="hover:bg-surface-container-low/40 transition-colors">
        <td class="py-space-base px-space-base font-title-sm text-title-sm text-primary">{{ $division->nama }}</td>
        <td class="py-space-base px-space-base">
          @if ($division->lini)
            <span class="px-2 py-0.5 rounded bg-surface-container text-primary font-label-sm text-xs font-semibold">{{ $division->lini }}</span>
          @else
            <span class="text-on-surface-variant">-</span>
          @endif
        </td>
        <td class="py-space-base px-space-base tabular-nums">{{ $division->employees_count }}</td>
        <td class="py-space-base px-space-base text-right space-x-3 whitespace-nowrap">
          <a href="{{ route('divisions.edit', $division) }}" class="text-secondary font-title-sm text-title-sm">Edit</a>
          <form action="{{ route('divisions.destroy', $division) }}" method="POST" class="inline" onsubmit="return confirm('Hapus divisi ini?')">
            @csrf @method('DELETE')
            <button type="submit" class="text-error font-title-sm text-title-sm">Hapus</button>
          </form>
        </td>
      </tr>
      @empty
      <tr><td colspan="4" class="py-8 px-4 text-center text-on-surface-variant">Belum ada divisi.</td></tr>
      @endforelse
    </tbody>
  </table>
</div>
@endsection
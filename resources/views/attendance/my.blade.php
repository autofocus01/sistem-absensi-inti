@extends('layouts.karyawan')

@section('title', 'Absensi Saya - PT. INTI')

@section('content')
<div class="flex flex-col gap-space-lg">
  <div class="flex flex-col md:flex-row md:items-end md:justify-between gap-space-md">
    <div>
      <span class="text-[11px] font-semibold text-on-surface-variant uppercase tracking-wider">PRESENSI PRIBADI</span>
      <h1 class="font-headline-lg text-primary tracking-tight mt-1">Absensi Saya</h1>
      <p class="text-on-surface-variant mt-1">Kelola Clock In, Clock Out, dan lihat riwayat presensi Anda.</p>
    </div>
    <div class="text-sm text-on-surface-variant">{{ $today->translatedFormat('l, d F Y') }}</div>
  </div>

  <div class="grid grid-cols-1 lg:grid-cols-3 gap-space-lg">
    <div class="lg:col-span-2 bg-surface-container-lowest rounded-2xl shadow-[0_1px_8px_rgba(0,0,0,0.05)] p-space-xl">
      <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-space-md">
        <div>
          <div class="text-sm text-on-surface-variant">{{ $employee->nama }}</div>
          <div class="font-title-sm text-primary mt-1">{{ $employee->nipeg }} · {{ $employee->division?->nama ?? 'Divisi belum tersedia' }}</div>
        </div>
        <span class="inline-flex w-fit items-center gap-2 px-3 py-1.5 rounded-full text-xs font-semibold {{ $todayAttendance?->jam_pulang ? 'bg-secondary/10 text-secondary' : ($todayAttendance?->jam_masuk ? 'bg-primary-container text-on-primary' : 'bg-surface-container-low text-on-surface-variant') }}">
          <span class="w-2 h-2 rounded-full bg-current"></span>
          {{ $todayAttendance?->jam_pulang ? 'Sudah Pulang' : ($todayAttendance?->jam_masuk ? 'Sedang Bekerja' : 'Belum Absen') }}
        </span>
      </div>

      <div class="grid grid-cols-1 sm:grid-cols-2 gap-space-md mt-space-xl">
        <div class="rounded-xl bg-surface-container-low p-space-lg">
          <div class="flex items-center gap-2 text-on-surface-variant text-sm"><span class="material-symbols-outlined text-[20px]">login</span> Jam Masuk</div>
          <div class="text-3xl font-bold text-primary mt-2">{{ $todayAttendance?->jam_masuk ? substr($todayAttendance->jam_masuk, 0, 5) : '--:--' }}</div>
          <div class="text-xs text-on-surface-variant mt-1">Jam normal 07:30</div>
        </div>
        <div class="rounded-xl bg-surface-container-low p-space-lg">
          <div class="flex items-center gap-2 text-on-surface-variant text-sm"><span class="material-symbols-outlined text-[20px]">logout</span> Jam Pulang</div>
          <div class="text-3xl font-bold text-primary mt-2">{{ $todayAttendance?->jam_pulang ? substr($todayAttendance->jam_pulang, 0, 5) : '--:--' }}</div>
          <div class="text-xs text-on-surface-variant mt-1">Jam normal 16:30</div>
        </div>
      </div>

      <div class="flex flex-col sm:flex-row gap-3 mt-space-lg">
        @if (! $todayAttendance?->jam_masuk)
          <form method="POST" action="{{ route('attendance.my.clock-in') }}" class="flex-1">
            @csrf
            <button type="submit" class="w-full inline-flex items-center justify-center gap-2 rounded-xl bg-primary-container text-on-primary px-5 py-3 font-semibold shadow-sm hover:opacity-90 transition">
              <span class="material-symbols-outlined">login</span> Clock In
            </button>
          </form>
        @elseif (! $todayAttendance?->jam_pulang)
          <form method="POST" action="{{ route('attendance.my.clock-out') }}" class="flex-1" onsubmit="return confirm('Catat Clock Out sekarang?')">
            @csrf
            <button type="submit" class="w-full inline-flex items-center justify-center gap-2 rounded-xl bg-primary-container text-on-primary px-5 py-3 font-semibold shadow-sm hover:opacity-90 transition">
              <span class="material-symbols-outlined">logout</span> Clock Out
            </button>
          </form>
        @else
          <div class="flex-1 rounded-xl bg-secondary/10 text-secondary px-5 py-3 text-center font-semibold">Presensi hari ini sudah lengkap.</div>
        @endif
      </div>

      @if ($todayAttendance)
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 mt-space-lg pt-space-lg border-t border-outline-variant/40">
          <div><div class="text-xs text-on-surface-variant">Keterlambatan</div><div class="font-semibold mt-1">{{ $todayAttendance->menitTelat() }} menit</div></div>
          <div><div class="text-xs text-on-surface-variant">Durasi Kerja</div><div class="font-semibold mt-1">{{ $todayAttendance->durasiKerjaFormat() }}</div></div>
          <div><div class="text-xs text-on-surface-variant">Sumber</div><div class="font-semibold mt-1">{{ $todayAttendance->sumber === 'self_service' ? 'Self Service' : ucfirst(str_replace('_', ' ', $todayAttendance->sumber)) }}</div></div>
        </div>
      @endif
    </div>

    <div class="bg-surface-container-lowest rounded-2xl shadow-[0_1px_8px_rgba(0,0,0,0.05)] p-space-lg h-fit">
      <div class="flex items-center gap-2"><span class="material-symbols-outlined text-secondary">info</span><h2 class="font-title-sm text-primary">Aturan Presensi</h2></div>
      <ul class="mt-space-md space-y-3 text-sm text-on-surface-variant">
        <li class="flex gap-2"><span class="text-secondary">•</span><span>Jam kerja normal Senin–Jumat: <strong class="text-on-surface">07:30–16:30</strong>.</span></li>
        <li class="flex gap-2"><span class="text-secondary">•</span><span>Tidak ada grace period. Keterlambatan dihitung dari selisih aktual setelah 07:30.</span></li>
        <li class="flex gap-2"><span class="text-secondary">•</span><span>Clock Out tetap mencatat jam pulang aktual; lembur diproses melalui modul Overtime dan otorisasi.</span></li>
      </ul>
    </div>
  </div>

  <section class="bg-surface-container-lowest rounded-2xl shadow-[0_1px_8px_rgba(0,0,0,0.05)] overflow-hidden">
    <div class="p-space-lg border-b border-outline-variant/40 flex flex-col md:flex-row md:items-center md:justify-between gap-space-md">
      <div>
        <h2 class="font-headline-md text-primary">Riwayat Presensi</h2>
        <p class="text-sm text-on-surface-variant mt-1">{{ $monthLabel }}</p>
      </div>
      <form method="GET" action="{{ route('attendance.my') }}" class="flex gap-2">
        <select name="month" class="rounded-lg border-outline-variant bg-surface-container-low text-sm">
          @foreach (range(1, 12) as $m)
            <option value="{{ $m }}" @selected($month === $m)>{{ \Carbon\Carbon::create()->month($m)->translatedFormat('F') }}</option>
          @endforeach
        </select>
        <input type="number" name="year" min="2000" max="2100" value="{{ $year }}" class="w-24 rounded-lg border-outline-variant bg-surface-container-low text-sm">
        <button class="rounded-lg bg-primary-container text-on-primary px-4 py-2 text-sm font-semibold">Tampilkan</button>
      </form>
    </div>
    <div class="overflow-x-auto">
      <table class="w-full min-w-[760px] text-left">
        <thead class="bg-surface-container-low text-xs uppercase tracking-wider text-on-surface-variant">
          <tr><th class="px-space-lg py-3">Tanggal</th><th class="px-space-lg py-3">Masuk</th><th class="px-space-lg py-3">Pulang</th><th class="px-space-lg py-3">Keterlambatan</th><th class="px-space-lg py-3">Durasi</th><th class="px-space-lg py-3">Status</th></tr>
        </thead>
        <tbody class="divide-y divide-outline-variant/30">
        @forelse ($history as $attendance)
          <tr class="hover:bg-surface-container-low/40">
            <td class="px-space-lg py-3 font-semibold">{{ $attendance->tanggal->translatedFormat('d M Y') }}</td>
            <td class="px-space-lg py-3">{{ $attendance->jam_masuk ? substr($attendance->jam_masuk,0,5) : '--:--' }}</td>
            <td class="px-space-lg py-3">{{ $attendance->jam_pulang ? substr($attendance->jam_pulang,0,5) : '--:--' }}</td>
            <td class="px-space-lg py-3">{{ $attendance->menitTelat() }} menit</td>
            <td class="px-space-lg py-3">{{ $attendance->durasiKerjaFormat() }}</td>
            <td class="px-space-lg py-3"><span class="px-2.5 py-1 rounded-full bg-surface-container-low text-xs font-semibold">{{ $attendance->status() }}</span></td>
          </tr>
        @empty
          <tr><td colspan="6" class="px-space-lg py-10 text-center text-on-surface-variant">Belum ada data presensi untuk periode ini.</td></tr>
        @endforelse
        </tbody>
      </table>
    </div>
    @if ($history->hasPages())
      <div class="p-space-lg border-t border-outline-variant/30">{{ $history->links() }}</div>
    @endif
  </section>
</div>
@endsection

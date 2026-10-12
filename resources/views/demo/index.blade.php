@extends('layouts.absensi')

@section('title', 'Kontrol Waktu Demo - PT. INTI')

@section('content')
<div class="flex flex-col gap-space-lg w-full max-w-5xl">
    <div class="rounded-xl border border-amber-200 bg-amber-50 p-space-xl">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-space-md">
            <div>
                <div class="inline-flex items-center gap-2 px-2.5 py-1 rounded-full bg-amber-100 text-amber-900 text-xs font-bold">
                    <span class="material-symbols-outlined text-[16px]">science</span> MODE DEMO AKTIF
                </div>
                <h1 class="font-headline-lg text-headline-lg text-primary mt-3">Kontrol Waktu Presentasi</h1>
                <p class="font-body-md text-body-md text-on-surface-variant mt-1">Gunakan waktu simulasi untuk mencoba jam masuk → jam pulang → lembur tanpa menunggu jam kerja nyata.</p>
            </div>
            <div class="text-left md:text-right">
                <div class="text-xs text-on-surface-variant">Waktu aplikasi</div>
                <div class="text-2xl font-bold text-amber-900">{{ $now->format('H:i') }} WIB</div>
                <div class="text-xs text-on-surface-variant">{{ $now->format('d/m/Y') }}</div>
                <div class="mt-2 inline-flex items-center rounded-full bg-white/80 px-2.5 py-1 text-xs font-semibold text-amber-900">Jenis hari: {{ $dayType === 'NATIONAL_HOLIDAY' ? 'Libur Nasional' : ($dayType === 'WEEKEND' ? 'Akhir Pekan' : 'Hari Kerja') }}</div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-space-md">
        @foreach([['07:30','Clock In','login'],['16:30','Jam Kerja Selesai','schedule'],['18:00','Simulasi Lembur','more_time']] as [$time,$label,$icon])
            <form method="POST" action="{{ route('demo.time') }}" class="bg-surface-container-lowest rounded-xl shadow-[0_1px_8px_rgba(0,0,0,0.04)] p-space-lg">
                @csrf
                <input type="hidden" name="time" value="{{ $time }}">
                <div class="w-10 h-10 rounded-lg bg-primary-container text-on-primary flex items-center justify-center mb-4">
                    <span class="material-symbols-outlined">{{ $icon }}</span>
                </div>
                <div class="text-xs uppercase tracking-wider text-on-surface-variant">{{ $time }} WIB</div>
                <div class="font-title-md text-title-md text-primary mt-1">{{ $label }}</div>
                <button class="mt-4 w-full px-4 py-2.5 rounded-lg bg-primary text-on-primary font-title-sm hover:opacity-90">Gunakan Waktu Ini</button>
            </form>
        @endforeach
    </div>

    <div class="bg-surface-container-lowest rounded-xl shadow-[0_1px_8px_rgba(0,0,0,0.04)] p-space-xl">
        <h2 class="font-headline-sm text-headline-sm text-primary">Atur Tanggal & Waktu Manual</h2>
        <p class="text-sm text-on-surface-variant mt-1 mb-4">Gunakan tanggal simulasi untuk menguji Hari Kerja, Akhir Pekan, dan Libur Nasional. Aturan bisnis tetap menggunakan tanggal simulasi yang dipilih.</p>
        <form method="POST" action="{{ route('demo.datetime') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-[1fr_1fr_auto] gap-3">
            @csrf
            <label class="text-sm text-on-surface-variant">Tanggal simulasi
                <input type="date" name="date" value="{{ $now->format('Y-m-d') }}" required class="mt-1 w-full rounded-lg border border-outline-variant px-3 py-2.5">
            </label>
            <label class="text-sm text-on-surface-variant">Jam simulasi
                <input type="time" name="time" value="{{ $now->format('H:i') }}" required class="mt-1 w-full rounded-lg border border-outline-variant px-3 py-2.5">
            </label>
            <button class="self-end px-5 py-2.5 rounded-lg bg-secondary text-white font-title-sm">Terapkan</button>
        </form>
        <div class="mt-4 rounded-lg bg-surface-container-low px-4 py-3 text-sm text-on-surface-variant">
            <strong>UAT:</strong> pilih Sabtu/Minggu untuk menguji <strong>WEEKEND</strong>, atau tanggal holiday aktif untuk menguji <strong>NATIONAL_HOLIDAY</strong>. Jenis hari saat ini: <strong>{{ $dayType === 'NATIONAL_HOLIDAY' ? 'Libur Nasional' : ($dayType === 'WEEKEND' ? 'Akhir Pekan' : 'Hari Kerja') }}</strong>.
        </div>
    </div>

    <div class="bg-surface-container-lowest rounded-xl shadow-[0_1px_8px_rgba(0,0,0,0.04)] p-space-xl">
        <h2 class="font-headline-sm text-headline-sm text-primary">Skenario Lembur</h2>
        <ol class="mt-4 space-y-3 text-sm text-on-surface-variant list-decimal list-inside">
            <li>Set <strong>07:30</strong>, lalu catat jam masuk sebagai Karyawan.</li>
            <li>Set <strong>18:00</strong>, lalu catat jam pulang.</li>
            <li>Ajukan lembur dari menu Pengajuan Lembur.</li>
            <li>Login VP → Setujui.</li>
            <li>Login HR → Verifikasi Final, atau <strong>Kembalikan ke VP</strong> jika ingin mendemokan anomaly.</li>
            <li>Jika dikembalikan, login VP → <strong>Verifikasi Ulang</strong> → HR Verifikasi Final.</li>
        </ol>
        <form method="POST" action="{{ route('demo.reset') }}" class="mt-6">
            @csrf
            <button class="px-4 py-2.5 rounded-lg border border-outline-variant text-primary font-title-sm hover:bg-surface-container-low">Reset Waktu ke Sistem</button>
        </form>
    </div>
</div>
@endsection

@extends('layouts.absensi')

@section('title', 'Riwayat & Timesheet')

@section('content')
@php
    $timesheetRows = $timesheet ?? collect();

    $displayStartDate = isset($startDate) && $startDate
        ? ($startDate instanceof \Carbon\CarbonInterface
            ? $startDate
            : \Carbon\Carbon::parse($startDate))
        : now();

    $displayEmployee = $employee ?? null;

    $displayTotalOfficialOvertime =
        $totalOfficialOvertimeFormat ?? '0j 00m';

    $hrMode = (bool) ($isHrAdmin ?? false);

    $displayAttendanceDays = $totalAttendanceDays
        ?? $timesheetRows
            ->filter(fn ($row) => ($row['attendance'] ?? null) !== null)
            ->count();

    $displayLateDays = $totalLateDays
        ?? $timesheetRows
            ->filter(fn ($row) => (int) ($row['menit_telat'] ?? 0) > 0)
            ->count();
@endphp

<div class="flex flex-col gap-space-base">

    {{-- ============================================================
         HEADER
         ============================================================ --}}
    <div class="flex flex-col lg:flex-row lg:items-start lg:justify-between gap-space-base">

        <div class="flex flex-col gap-1">
            <span class="font-label-sm text-label-sm text-on-surface-variant uppercase tracking-wider">
                RIWAYAT &amp; TIMESHEET
            </span>

            <h1 class="font-headline-lg text-headline-lg text-primary tracking-tight">
                {{ $hrMode ? 'Timesheet Administrasi' : 'Riwayat Presensi & Lembur' }}
            </h1>

            <p class="font-body-md text-body-md text-on-surface-variant">
                @if ($hrMode)
                    Rekap presensi dan lembur resmi seluruh karyawan sesuai periode yang dipilih.
                @elseif ($displayEmployee)
                    {{ $displayEmployee->nama ?? 'Karyawan' }}
                    @if (!empty($displayEmployee->nipeg))
                        — {{ $displayEmployee->nipeg }}
                    @endif
                    · {{ $displayStartDate->translatedFormat('F Y') }}
                @else
                    {{ $displayStartDate->translatedFormat('F Y') }}
                @endif
            </p>
        </div>

        @if ($hrMode)
            <div class="px-3 py-2 rounded-lg bg-primary-container/30 text-primary font-label-sm">
                Mode HR Administrasi
            </div>
        @endif
    </div>

    {{-- ============================================================
         FILTER HR
         ============================================================ --}}
    @if ($hrMode)
        <form method="GET"
              action="{{ route('timesheet.index') }}"
              class="bg-surface-container-lowest rounded-xl shadow-sm p-space-base">

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-space-base">

                <div class="lg:col-span-2">
                    <label for="q"
                           class="block font-label-sm text-label-sm text-on-surface-variant mb-1">
                        Cari Karyawan
                    </label>

                    <input
                        id="q"
                        name="q"
                        type="text"
                        value="{{ $search ?? '' }}"
                        placeholder="Nama atau NIPEG"
                        class="w-full rounded-lg border border-outline bg-surface px-3 py-2 text-on-surface focus:outline-none focus:ring-2 focus:ring-primary/30"
                    >
                </div>

                <div>
                    <label for="division_id"
                           class="block font-label-sm text-label-sm text-on-surface-variant mb-1">
                        Divisi
                    </label>

                    <select
                        id="division_id"
                        name="division_id"
                        class="w-full rounded-lg border border-outline bg-surface px-3 py-2 text-on-surface focus:outline-none focus:ring-2 focus:ring-primary/30"
                    >
                        <option value="">Semua Divisi</option>

                        @foreach (($divisions ?? collect()) as $division)
                            <option
                                value="{{ $division->id }}"
                                @selected((int) ($selectedDivisionId ?? 0) === (int) $division->id)
                            >
                                {{ $division->nama }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="month"
                           class="block font-label-sm text-label-sm text-on-surface-variant mb-1">
                        Bulan
                    </label>

                    <select
                        id="month"
                        name="month"
                        class="w-full rounded-lg border border-outline bg-surface px-3 py-2 text-on-surface focus:outline-none focus:ring-2 focus:ring-primary/30"
                    >
                        @for ($m = 1; $m <= 12; $m++)
                            <option value="{{ $m }}" @selected((int) $month === $m)>
                                {{ \Carbon\Carbon::create()->month($m)->translatedFormat('F') }}
                            </option>
                        @endfor
                    </select>
                </div>

                <div>
                    <label for="year"
                           class="block font-label-sm text-label-sm text-on-surface-variant mb-1">
                        Tahun
                    </label>

                    <select
                        id="year"
                        name="year"
                        class="w-full rounded-lg border border-outline bg-surface px-3 py-2 text-on-surface focus:outline-none focus:ring-2 focus:ring-primary/30"
                    >
                        @foreach (($availableYears ?? []) as $availableYear)
                            <option
                                value="{{ $availableYear }}"
                                @selected((int) $year === (int) $availableYear)
                            >
                                {{ $availableYear }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="flex flex-wrap items-center gap-2 mt-space-base">
                <button
                    type="submit"
                    class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-primary text-on-primary font-title-sm"
                >
                    <span class="material-symbols-outlined text-[18px]">search</span>
                    Tampilkan
                </button>

                <a
                    href="{{ route('timesheet.index') }}"
                    class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-surface-container text-on-surface font-title-sm"
                >
                    <span class="material-symbols-outlined text-[18px]">restart_alt</span>
                    Reset
                </a>
            </div>
        </form>
    @endif

    {{-- ============================================================
         RINGKASAN
         ============================================================ --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-space-base">

        <div class="bg-surface-container-lowest rounded-xl shadow-sm p-space-base">
            <div class="flex items-center justify-between">
                <span class="font-label-sm text-label-sm text-on-surface-variant uppercase tracking-wider">
                    Lembur Resmi
                </span>

                <span class="material-symbols-outlined text-[20px] text-primary">
                    more_time
                </span>
            </div>

            <p class="font-headline-md text-headline-md text-primary mt-1">
                {{ $displayTotalOfficialOvertime }}
            </p>

            <p class="font-body-sm text-body-sm text-on-surface-variant mt-1">
                Sudah Verified HR
            </p>
        </div>

        <div class="bg-surface-container-lowest rounded-xl shadow-sm p-space-base">
            <div class="flex items-center justify-between">
                <span class="font-label-sm text-label-sm text-on-surface-variant uppercase tracking-wider">
                    Hari Hadir
                </span>

                <span class="material-symbols-outlined text-[20px] text-primary">
                    fact_check
                </span>
            </div>

            <p class="font-headline-md text-headline-md text-primary mt-1">
                {{ $displayAttendanceDays }}
                <span class="font-body-md text-body-md text-on-surface-variant">
                    Hari
                </span>
            </p>
        </div>

        <div class="bg-surface-container-lowest rounded-xl shadow-sm p-space-base">
            <div class="flex items-center justify-between">
                <span class="font-label-sm text-label-sm text-on-surface-variant uppercase tracking-wider">
                    Hari Telat
                </span>

                <span class="material-symbols-outlined text-[20px] text-error">
                    timer_off
                </span>
            </div>

            <p class="font-headline-md text-headline-md text-primary mt-1">
                {{ $displayLateDays }}
                <span class="font-body-md text-body-md text-on-surface-variant">
                    Hari
                </span>
            </p>
        </div>
    </div>

    {{-- ============================================================
         TABEL
         ============================================================ --}}
    <div class="bg-surface-container-lowest rounded-xl shadow-sm overflow-x-auto">

        <table class="w-full text-left min-w-[980px]">

            <thead>
                <tr class="bg-surface-container-low text-on-surface-variant font-label-sm text-label-sm uppercase tracking-wider">

                    @if ($hrMode)
                        <th class="py-3 px-space-base rounded-l-lg">
                            Karyawan
                        </th>

                        <th class="py-3 px-space-base">
                            Divisi
                        </th>
                    @else
                        <th class="py-3 px-space-base rounded-l-lg">
                            Tanggal
                        </th>
                    @endif

                    @if ($hrMode)
                        <th class="py-3 px-space-base">
                            Tanggal
                        </th>
                    @endif

                    <th class="py-3 px-space-base">
                        Jam Masuk
                    </th>

                    <th class="py-3 px-space-base">
                        Jam Pulang
                    </th>

                    <th class="py-3 px-space-base">
                        Durasi
                    </th>

                    <th class="py-3 px-space-base">
                        Keterlambatan
                    </th>

                    <th class="py-3 px-space-base">
                        Status
                    </th>

                    <th class="py-3 px-space-base text-right rounded-r-lg">
                        Lembur Resmi
                    </th>
                </tr>
            </thead>

            <tbody class="divide-y divide-surface-container-low font-body-md text-body-md">

                @forelse ($timesheetRows as $row)

                    @php
                        $attendance = $row['attendance'] ?? null;
                        $status = $row['status'] ?? 'Belum ada data';
                        $lateMinutes = (int) ($row['menit_telat'] ?? 0);
                        $overtimeMinutes = (int) ($row['lembur_resmi_menit'] ?? 0);

                        $tanggal = isset($row['tanggal']) && $row['tanggal']
                            ? ($row['tanggal'] instanceof \Carbon\CarbonInterface
                                ? $row['tanggal']
                                : \Carbon\Carbon::parse($row['tanggal']))
                            : null;
                    @endphp

                    <tr class="hover:bg-surface-container-low/40 transition-colors">

                        @if ($hrMode)
                            <td class="py-space-base px-space-base">
                                <div class="font-title-sm text-title-sm text-primary">
                                    {{ $row['employee_name'] ?? '-' }}
                                </div>

                                @if (!empty($row['employee_nipeg']))
                                    <div class="font-label-sm text-label-sm text-on-surface-variant mt-0.5">
                                        {{ $row['employee_nipeg'] }}
                                    </div>
                                @endif
                            </td>

                            <td class="py-space-base px-space-base">
                                {{ $row['employee_division'] ?? '-' }}
                            </td>

                            <td class="py-space-base px-space-base">
                                <div class="font-title-sm text-title-sm text-primary">
                                    {{ $tanggal ? $tanggal->translatedFormat('l, d M Y') : '-' }}
                                </div>

                                @if ($tanggal && $tanggal->isWeekend())
                                    <span class="font-label-sm text-label-sm text-on-surface-variant">
                                        Akhir pekan
                                    </span>
                                @endif
                            </td>
                        @else
                            <td class="py-space-base px-space-base">
                                <div class="font-title-sm text-title-sm text-primary">
                                    {{ $tanggal ? $tanggal->translatedFormat('l, d M Y') : '-' }}
                                </div>

                                @if ($tanggal && $tanggal->isWeekend())
                                    <span class="font-label-sm text-label-sm text-on-surface-variant">
                                        Akhir pekan
                                    </span>
                                @endif
                            </td>
                        @endif

                        <td class="py-space-base px-space-base tabular-nums">
                            {{ !empty($row['jam_masuk'])
                                ? \Carbon\Carbon::parse($row['jam_masuk'])->format('H:i')
                                : '-' }}
                        </td>

                        <td class="py-space-base px-space-base tabular-nums">
                            {{ !empty($row['jam_pulang'])
                                ? \Carbon\Carbon::parse($row['jam_pulang'])->format('H:i')
                                : '-' }}
                        </td>

                        <td class="py-space-base px-space-base tabular-nums">
                            @if (
                                isset($row['durasi_kerja_menit'])
                                && $row['durasi_kerja_menit'] !== null
                            )
                                {{ intdiv((int) $row['durasi_kerja_menit'], 60) }}j
                                {{ str_pad(
                                    (string) ((int) $row['durasi_kerja_menit'] % 60),
                                    2,
                                    '0',
                                    STR_PAD_LEFT
                                ) }}m
                            @else
                                -
                            @endif
                        </td>

                        <td class="py-space-base px-space-base tabular-nums">
                            @if ($lateMinutes > 0)
                                <span class="text-error">
                                    {{ $lateMinutes }} menit
                                </span>
                            @else
                                <span class="text-on-surface-variant">
                                    0 menit
                                </span>
                            @endif
                        </td>

                        <td class="py-space-base px-space-base">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full font-label-sm text-label-sm font-semibold
                                {{
                                    $status === 'Hadir'
                                        ? 'bg-secondary-container/50 text-secondary'
                                        : (
                                            $status === 'Hadir + Lembur'
                                                ? 'bg-tertiary-container text-on-tertiary-container'
                                                : 'bg-surface-container text-on-surface-variant'
                                        )
                                }}">
                                {{ $status }}
                            </span>
                        </td>

                        <td class="py-space-base px-space-base text-right tabular-nums font-title-sm text-title-sm
                            {{ $overtimeMinutes > 0 ? 'text-primary' : 'text-on-surface-variant' }}">

                            @if ($overtimeMinutes > 0)
                                {{
                                    $row['lembur_resmi_format']
                                    ?? (
                                        intdiv($overtimeMinutes, 60)
                                        . 'j '
                                        . str_pad(
                                            (string) ($overtimeMinutes % 60),
                                            2,
                                            '0',
                                            STR_PAD_LEFT
                                        )
                                        . 'm'
                                    )
                                }}

                                <div class="font-label-sm text-label-sm text-on-surface-variant mt-0.5">
                                    Verified HR
                                </div>
                            @else
                                -
                            @endif
                        </td>
                    </tr>

                @empty

                    <tr>
                        <td
                            colspan="{{ $hrMode ? 9 : 7 }}"
                            class="py-10 px-4 text-center text-on-surface-variant"
                        >
                            @if ($hrMode)
                                Tidak ada data karyawan atau presensi untuk filter yang dipilih.
                            @else
                                Belum ada data timesheet untuk periode ini.
                            @endif
                        </td>
                    </tr>

                @endforelse

            </tbody>
        </table>
    </div>

    {{-- ============================================================
         FOOTER INFO
         ============================================================ --}}
    <div class="flex flex-wrap gap-2 text-body-sm text-on-surface-variant">
        <span>
            Periode: {{ $displayStartDate->translatedFormat('F Y') }}
        </span>

        <span>•</span>

        @if ($hrMode)
            <span>
                Scope:
                {{ $employees->count() }} karyawan
            </span>

            <span>•</span>
        @endif

        <span>
            Lembur resmi hanya dihitung dari pengajuan yang sudah
            <strong>Verified HR</strong>.
        </span>
    </div>

</div>
@endsection

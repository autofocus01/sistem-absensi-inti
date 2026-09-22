@extends('layouts.absensi')

@section('title', 'Riwayat & Timesheet')

@section('content')

@php
    $timesheetRows = $timesheet ?? collect();

    $displayStartDate = isset($startDate) && $startDate
        ? (
            $startDate instanceof \Carbon\CarbonInterface
                ? $startDate
                : \Carbon\Carbon::parse($startDate)
        )
        : now();

    $displayEmployee = $employee ?? null;

    $displayTotalOfficialOvertime =
        $totalOfficialOvertimeFormat ?? '0j 00m';

    $selectedMonth = (int) ($month ?? now()->month);

    $selectedYear = (int) ($year ?? now()->year);

    $years = $availableYears ?? range(
        now()->year - 3,
        now()->year + 1
    );
@endphp


{{-- ============================================================
     HEADER
============================================================ --}}

<div class="flex flex-col sm:flex-row sm:items-center justify-between gap-space-base pb-space-lg">

    <div class="flex flex-col gap-1">

        <span class="font-label-sm text-label-sm text-on-surface-variant uppercase tracking-wider">
            RIWAYAT &amp; TIMESHEET
        </span>

        <h1 class="font-headline-lg text-headline-lg text-primary tracking-tight">
            Riwayat Presensi &amp; Lembur
        </h1>

        <p class="font-body-md text-body-md text-on-surface-variant">

            @if ($displayEmployee)

                {{ $displayEmployee->nama ?? 'Karyawan' }}

                @if (!empty($displayEmployee->nipeg))
                    — {{ $displayEmployee->nipeg }}
                @endif

                ·

            @endif

            {{ $displayStartDate->translatedFormat('F Y') }}

        </p>

    </div>

</div>


{{-- ============================================================
     FILTER PERIODE
============================================================ --}}

<div class="bg-surface-container-lowest rounded-xl shadow-sm p-space-base mb-space-lg">

    <form
        method="GET"
        action="{{ route('timesheet.index') }}"
        class="flex flex-col lg:flex-row lg:items-end gap-space-base"
    >

        {{-- BULAN --}}
        <div class="flex flex-col gap-1.5">

            <label
                for="bulan"
                class="font-label-sm text-label-sm text-on-surface-variant"
            >
                Bulan
            </label>

            <select
                id="bulan"
                name="bulan"
                class="min-w-[180px] rounded-lg border border-outline-variant/50 bg-surface-container-lowest text-on-surface font-body-md text-body-md px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-primary"
            >

                @foreach (range(1, 12) as $m)

                    @php
                        $monthName = \Carbon\Carbon::create(
                            2000,
                            $m,
                            1
                        )->translatedFormat('F');
                    @endphp

                    <option
                        value="{{ $m }}"
                        @selected($selectedMonth === $m)
                    >
                        {{ $monthName }}
                    </option>

                @endforeach

            </select>

        </div>


        {{-- TAHUN --}}
        <div class="flex flex-col gap-1.5">

            <label
                for="tahun"
                class="font-label-sm text-label-sm text-on-surface-variant"
            >
                Tahun
            </label>

            <select
                id="tahun"
                name="tahun"
                class="min-w-[140px] rounded-lg border border-outline-variant/50 bg-surface-container-lowest text-on-surface font-body-md text-body-md px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-primary"
            >

                @foreach ($years as $availableYear)

                    <option
                        value="{{ $availableYear }}"
                        @selected($selectedYear === (int) $availableYear)
                    >
                        {{ $availableYear }}
                    </option>

                @endforeach

            </select>

        </div>


        {{-- TOMBOL CARI --}}
        <div class="flex gap-2">

            <button
                type="submit"
                class="inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-lg bg-primary-container text-on-primary font-title-sm text-title-sm shadow-sm hover:opacity-90 transition"
            >

                <span class="material-symbols-outlined text-[18px]">
                    search
                </span>

                Tampilkan

            </button>


            {{-- RESET --}}
            @if (
                $selectedMonth !== now()->month ||
                $selectedYear !== now()->year
            )

                <a
                    href="{{ route('timesheet.index') }}"
                    class="inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-lg bg-surface-container-low text-on-surface font-title-sm text-title-sm hover:bg-surface-container transition"
                >

                    <span class="material-symbols-outlined text-[18px]">
                        restart_alt
                    </span>

                    Bulan Ini

                </a>

            @endif

        </div>

    </form>

</div>


{{-- ============================================================
     RINGKASAN
============================================================ --}}

<div class="grid grid-cols-1 sm:grid-cols-3 gap-space-base mb-space-lg">


    {{-- LEMBUR --}}
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


    {{-- HADIR --}}
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

            {{ $timesheetRows->filter(
                fn ($row) => ($row['attendance'] ?? null) !== null
            )->count() }}

            <span class="font-body-md text-body-md text-on-surface-variant">
                Hari
            </span>

        </p>

    </div>


    {{-- TELAT --}}
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

            {{ $timesheetRows->filter(
                fn ($row) => (int) ($row['menit_telat'] ?? 0) > 0
            )->count() }}

            <span class="font-body-md text-body-md text-on-surface-variant">
                Hari
            </span>

        </p>

    </div>

</div>


{{-- ============================================================
     TABEL TIMESHEET
============================================================ --}}

<div class="bg-surface-container-lowest rounded-xl shadow-sm overflow-x-auto">

    <table class="w-full text-left min-w-[980px]">

        <thead>

            <tr class="bg-surface-container-low text-on-surface-variant font-label-sm text-label-sm uppercase tracking-wider">

                <th class="py-3 px-space-base rounded-l-lg">
                    Tanggal
                </th>

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

                    $lateMinutes =
                        (int) ($row['menit_telat'] ?? 0);

                    $overtimeMinutes =
                        (int) ($row['lembur_resmi_menit'] ?? 0);

                    $rowDate = isset($row['tanggal']) && $row['tanggal']
                        ? (
                            $row['tanggal'] instanceof \Carbon\CarbonInterface
                                ? $row['tanggal']
                                : \Carbon\Carbon::parse($row['tanggal'])
                        )
                        : null;

                @endphp


                <tr class="hover:bg-surface-container-low/40 transition-colors">


                    {{-- TANGGAL --}}
                    <td class="py-space-base px-space-base">

                        @if ($rowDate)

                            <div class="font-title-sm text-title-sm text-primary">
                                {{ $rowDate->translatedFormat('l, d M Y') }}
                            </div>

                            @if ($rowDate->isWeekend())

                                <span class="font-label-sm text-label-sm text-on-surface-variant">
                                    Akhir pekan
                                </span>

                            @endif

                        @else

                            -

                        @endif

                    </td>


                    {{-- MASUK --}}
                    <td class="py-space-base px-space-base tabular-nums">

                        @if (!empty($row['jam_masuk']))

                            {{ \Carbon\Carbon::parse(
                                $row['jam_masuk']
                            )->format('H:i') }}

                        @else

                            -

                        @endif

                    </td>


                    {{-- PULANG --}}
                    <td class="py-space-base px-space-base tabular-nums">

                        @if (!empty($row['jam_pulang']))

                            {{ \Carbon\Carbon::parse(
                                $row['jam_pulang']
                            )->format('H:i') }}

                        @else

                            -

                        @endif

                    </td>


                    {{-- DURASI --}}
                    <td class="py-space-base px-space-base tabular-nums">

                        @if (
                            isset($row['durasi_kerja_menit']) &&
                            $row['durasi_kerja_menit'] !== null
                        )

                            {{ intdiv(
                                (int) $row['durasi_kerja_menit'],
                                60
                            ) }}j

                            {{ str_pad(
                                (string) (
                                    (int) $row['durasi_kerja_menit'] % 60
                                ),
                                2,
                                '0',
                                STR_PAD_LEFT
                            ) }}m

                        @else

                            -

                        @endif

                    </td>


                    {{-- KETERLAMBATAN --}}
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


                    {{-- STATUS --}}
                    <td class="py-space-base px-space-base">

                        <span
                            class="inline-flex items-center px-2 py-0.5 rounded-full font-label-sm text-label-sm font-semibold
                            {{
                                $status === 'Hadir'
                                    ? 'bg-secondary-container/50 text-secondary'
                                    : (
                                        $status === 'Hadir + Lembur'
                                            ? 'bg-tertiary-container text-on-tertiary-container'
                                            : 'bg-surface-container text-on-surface-variant'
                                    )
                            }}"
                        >
                            {{ $status }}
                        </span>

                    </td>


                    {{-- LEMBUR --}}
                    <td
                        class="py-space-base px-space-base text-right tabular-nums font-title-sm text-title-sm
                        {{
                            $overtimeMinutes > 0
                                ? 'text-primary'
                                : 'text-on-surface-variant'
                        }}"
                    >

                        @if ($overtimeMinutes > 0)

                            {{
                                $row['lembur_resmi_format']
                                ?? (
                                    intdiv(
                                        $overtimeMinutes,
                                        60
                                    )
                                    . 'j '
                                    . str_pad(
                                        (string) (
                                            $overtimeMinutes % 60
                                        ),
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
                        colspan="7"
                        class="py-8 px-4 text-center text-on-surface-variant"
                    >
                        Belum ada data timesheet untuk periode ini.
                    </td>

                </tr>

            @endforelse

        </tbody>

    </table>

</div>


{{-- ============================================================
     FOOTER INFO
============================================================ --}}

<div class="mt-space-base flex flex-wrap gap-2 text-body-sm text-on-surface-variant">

    <span>
        Periode:
        <strong>
            {{ $displayStartDate->translatedFormat('F Y') }}
        </strong>
    </span>

    <span>•</span>

    <span>
        Lembur resmi hanya dihitung dari pengajuan yang sudah
        <strong>Verified HR</strong>.
    </span>

</div>

@endsection
@extends('layouts.absensi')

@section('title', 'Riwayat & Timesheet')

@section('p1_head')
    <style>
        .timesheet-page {
            min-width: 0;
        }

        .timesheet-card {
            border: 1px solid rgba(15, 23, 42, 0.07);
            box-shadow: 0 1px 3px rgba(15, 23, 42, 0.04);
        }

        .timesheet-filter {
            border: 1px solid rgba(15, 23, 42, 0.08);
            background: var(--surface-container-lowest, #fff);
        }

        .timesheet-input {
            min-height: 44px;
            transition:
                border-color 160ms ease,
                box-shadow 160ms ease,
                background-color 160ms ease;
        }

        .timesheet-input:focus {
            outline: none;
            border-color: var(--primary, #1d4ed8);
            box-shadow: 0 0 0 3px rgba(29, 78, 216, 0.12);
        }

        .timesheet-kpi {
            min-height: 132px;
        }

        .timesheet-table-wrap {
            width: 100%;
            overflow-x: auto;
            overscroll-behavior-x: contain;
            -webkit-overflow-scrolling: touch;
            scrollbar-width: thin;
        }

        .timesheet-table {
            min-width: 1060px;
            border-collapse: separate;
            border-spacing: 0;
        }

        .timesheet-table th {
            white-space: nowrap;
        }

        .timesheet-table tbody tr {
            transition: background-color 140ms ease;
        }

        .timesheet-table tbody tr:hover {
            background: rgba(15, 23, 42, 0.025);
        }

        .timesheet-number {
            font-variant-numeric: tabular-nums;
            font-feature-settings: "tnum";
        }

        .timesheet-badge {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            min-height: 26px;
            padding: 4px 9px;
            border-radius: 999px;
            white-space: nowrap;
            font-size: 12px;
            line-height: 1.1;
            font-weight: 700;
        }

        .timesheet-status {
            display: inline-flex;
            align-items: center;
            min-height: 28px;
            padding: 5px 10px;
            border-radius: 999px;
            white-space: nowrap;
            font-size: 12px;
            font-weight: 700;
        }

        .timesheet-mobile-note {
            display: none;
        }

        @media (max-width: 1023px) {
            .timesheet-kpi {
                min-height: 118px;
            }

            .timesheet-mobile-note {
                display: flex;
            }
        }

        @media (max-width: 767px) {
            .timesheet-kpi {
                min-height: 110px;
            }

            .timesheet-filter-actions {
                width: 100%;
            }

            .timesheet-filter-actions > * {
                flex: 1 1 auto;
            }
        }

        @media (max-width: 379px) {
            .timesheet-filter-actions {
                flex-direction: column;
            }

            .timesheet-filter-actions > * {
                width: 100%;
            }
        }

        @media (prefers-reduced-motion: reduce) {
            .timesheet-input,
            .timesheet-table tbody tr {
                transition: none;
            }
        }
    </style>
@endsection

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

    $monthNames = [
        1 => 'Januari',
        2 => 'Februari',
        3 => 'Maret',
        4 => 'April',
        5 => 'Mei',
        6 => 'Juni',
        7 => 'Juli',
        8 => 'Agustus',
        9 => 'September',
        10 => 'Oktober',
        11 => 'November',
        12 => 'Desember',
    ];

    $hrMode = (bool) ($isHrAdmin ?? false);
    $vpMode = (bool) ($isVp ?? false);

    $displayAttendanceDays = $totalAttendanceDays
        ?? $timesheetRows
            ->filter(fn ($row) => ($row['attendance'] ?? null) !== null)
            ->count();

    $displayLateDays = $totalLateDays
        ?? $timesheetRows
            ->filter(fn ($row) => (int) ($row['menit_telat'] ?? 0) > 0)
            ->count();
@endphp

<div class="timesheet-page flex flex-col gap-space-base">

    {{-- ============================================================
         PAGE HEADER
         ============================================================ --}}
    <section class="flex flex-col lg:flex-row lg:items-end lg:justify-between gap-space-base">

        <div class="min-w-0">
            <div class="flex flex-wrap items-center gap-2 mb-2">
                <span class="font-label-sm text-label-sm text-on-surface-variant uppercase tracking-[0.12em]">
                    RIWAYAT &amp; TIMESHEET
                </span>

                @if ($hrMode)
                    <span class="timesheet-badge bg-primary-container/40 text-primary">
                        <span class="material-symbols-outlined text-[15px]">admin_panel_settings</span>
                        HR
                    </span>
                @elseif ($vpMode)
                    <span class="timesheet-badge bg-primary-container/40 text-primary">
                        <span class="material-symbols-outlined text-[15px]">groups</span>
                        VP Divisi
                    </span>
                @endif
            </div>

            <h1 class="font-headline-lg text-headline-lg text-primary tracking-tight">
                @if ($hrMode)
                    Timesheet Administrasi
                @elseif ($vpMode)
                    Timesheet Divisi
                @else
                    Riwayat Presensi &amp; Lembur
                @endif
            </h1>

            <p class="mt-1 max-w-3xl font-body-md text-body-md text-on-surface-variant">
                @if ($hrMode)
                    Rekap presensi dan lembur resmi seluruh karyawan berdasarkan periode yang dipilih.
                @elseif ($vpMode)
                    Rekap presensi dan lembur resmi karyawan dalam cakupan divisi VP.
                @elseif ($displayEmployee)
                    {{ $displayEmployee->nama ?? 'Karyawan' }}

                    @if (($displayEmployee->band_code ?? null) !== null)
                        <span class="inline-flex ml-1 px-2 py-0.5 rounded-full bg-primary-container/30 text-primary text-xs font-semibold">
                            Band {{ $displayEmployee->band_code }}
                        </span>
                    @endif

                    @if (!empty($displayEmployee->nipeg))
                        <span class="mx-1">•</span>
                        {{ $displayEmployee->nipeg }}
                    @endif

                    <span class="mx-1">•</span>
                    {{ $displayStartDate->translatedFormat('F Y') }}
                @else
                    Periode {{ $displayStartDate->translatedFormat('F Y') }}
                @endif
            </p>
        </div>

        <div class="flex flex-wrap items-center gap-2 shrink-0">
            <div class="inline-flex items-center gap-2 px-3 py-2 rounded-lg bg-surface-container-low text-on-surface-variant">
                <span class="material-symbols-outlined text-[18px]">calendar_month</span>
                <span class="font-label-sm text-label-sm">
                    {{ $displayStartDate->translatedFormat('F Y') }}
                </span>
            </div>
        </div>
    </section>


    {{-- ============================================================
         FILTER
         ============================================================ --}}
    @if ($hrMode || $vpMode)
        <section class="timesheet-filter rounded-xl p-space-base">

            <div class="flex flex-col gap-1 mb-space-base">
                <div class="flex items-center gap-2">
                    <span class="material-symbols-outlined text-[20px] text-primary">
                        tune
                    </span>

                    <h2 class="font-title-md text-title-md text-on-surface">
                        Filter Timesheet
                    </h2>
                </div>

                <p class="font-body-sm text-body-sm text-on-surface-variant">
                    Gunakan filter untuk mempersempit data berdasarkan karyawan, divisi, dan periode.
                </p>
            </div>

            <form
                data-auto-filter
                method="GET"
                action="{{ route('timesheet.index') }}"
            >

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-space-base">

                    {{-- SEARCH --}}
                    <div class="lg:col-span-2">
                        <label
                            for="q"
                            class="block font-label-sm text-label-sm text-on-surface-variant mb-1.5"
                        >
                            Cari Karyawan
                        </label>

                        <div class="relative">
                            <span
                                class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-[20px] text-on-surface-variant pointer-events-none"
                            >
                                search
                            </span>

                            <input
                                id="q"
                                name="q"
                                type="text"
                                value="{{ $search ?? '' }}"
                                placeholder="Nama atau NIPEG"
                                autocomplete="off"
                                class="timesheet-input w-full rounded-lg border border-outline bg-surface text-on-surface pl-10 pr-3 py-2"
                            >
                        </div>
                    </div>

                    {{-- DIVISION --}}
                    <div>
                        <label
                            for="division_id"
                            class="block font-label-sm text-label-sm text-on-surface-variant mb-1.5"
                        >
                            Divisi
                        </label>

                        <select
                            id="division_id"
                            name="division_id"
                            class="timesheet-input w-full rounded-lg border border-outline bg-surface text-on-surface px-3 py-2"
                        >
                            @if ($hrMode)
                                <option value="">Semua Divisi</option>
                            @endif

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

                    {{-- MONTH --}}
                    <div>
                        <label
                            for="bulan"
                            class="block font-label-sm text-label-sm text-on-surface-variant mb-1.5"
                        >
                            Bulan
                        </label>

                        <select
                            id="bulan"
                            name="bulan"
                            class="timesheet-input w-full rounded-lg border border-outline bg-surface text-on-surface px-3 py-2"
                        >
                            @for ($m = 1; $m <= 12; $m++)
                                <option
                                    value="{{ $m }}"
                                    @selected((int) $month === $m)
                                >
                                    {{ $monthNames[$m] }}
                                </option>
                            @endfor
                        </select>
                    </div>

                    {{-- YEAR --}}
                    <div>
                        <label
                            for="tahun"
                            class="block font-label-sm text-label-sm text-on-surface-variant mb-1.5"
                        >
                            Tahun
                        </label>

                        <select
                            id="tahun"
                            name="tahun"
                            class="timesheet-input w-full rounded-lg border border-outline bg-surface text-on-surface px-3 py-2"
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

                <input
                    type="hidden"
                    name="per_page"
                    value="20"
                >

                <div class="timesheet-filter-actions flex flex-wrap items-center gap-2 mt-space-base">

                    <button
                        type="submit"
                        class="inline-flex items-center justify-center gap-2 min-h-[44px] px-4 py-2 rounded-lg bg-primary text-on-primary font-title-sm hover:opacity-90 focus:outline-none focus:ring-2 focus:ring-primary/30"
                    >
                        <span class="material-symbols-outlined text-[18px]">
                            search
                        </span>
                        Tampilkan
                    </button>

                    <a
                        href="{{ route('timesheet.index') }}"
                        class="inline-flex items-center justify-center gap-2 min-h-[44px] px-4 py-2 rounded-lg bg-surface-container text-on-surface font-title-sm hover:bg-surface-container-high focus:outline-none focus:ring-2 focus:ring-primary/20"
                    >
                        <span class="material-symbols-outlined text-[18px]">
                            restart_alt
                        </span>
                        Atur Ulang
                    </a>
                </div>
            </form>

            <div class="timesheet-mobile-note mt-space-base items-start gap-2 rounded-lg bg-surface-container-low px-3 py-2 text-body-sm text-on-surface-variant">
                <span class="material-symbols-outlined text-[18px] shrink-0">
                    swipe
                </span>

                <span>
                    Pada layar kecil, tabel dapat digeser secara horizontal untuk melihat seluruh kolom.
                </span>
            </div>

            <div class="mt-3 font-body-sm text-body-sm text-on-surface-variant">
                Menampilkan
                <strong class="text-on-surface">
                    {{ $timesheetRows->count() }}
                </strong>
                dari
                <strong class="text-on-surface">
                    {{ number_format($totalTimesheetRows ?? $timesheetRows->count()) }}
                </strong>
                baris timesheet.
            </div>
        </section>
    @endif


    {{-- ============================================================
         KPI
         ============================================================ --}}
    <section
        class="grid grid-cols-1 sm:grid-cols-3 gap-space-base"
        aria-label="Ringkasan timesheet"
    >

        {{-- OT --}}
        <article class="timesheet-card timesheet-kpi bg-surface-container-lowest rounded-xl p-space-base">

            <div class="flex items-start justify-between gap-3">

                <div class="min-w-0">
                    <p class="font-label-sm text-label-sm text-on-surface-variant uppercase tracking-wider">
                        Lembur Resmi
                    </p>

                    <p class="mt-2 font-headline-md text-headline-md text-primary timesheet-number">
                        {{ $displayTotalOfficialOvertime }}
                    </p>
                </div>

                <div class="w-10 h-10 rounded-lg bg-primary-container/40 text-primary flex items-center justify-center shrink-0">
                    <span class="material-symbols-outlined text-[22px]">
                        more_time
                    </span>
                </div>
            </div>

            <p class="mt-2 font-body-sm text-body-sm text-on-surface-variant">
                Pengajuan yang sudah Verified HR
            </p>
        </article>


        {{-- ATTENDANCE --}}
        <article class="timesheet-card timesheet-kpi bg-surface-container-lowest rounded-xl p-space-base">

            <div class="flex items-start justify-between gap-3">

                <div class="min-w-0">
                    <p class="font-label-sm text-label-sm text-on-surface-variant uppercase tracking-wider">
                        Hari Hadir
                    </p>

                    <p class="mt-2 font-headline-md text-headline-md text-primary timesheet-number">
                        {{ $displayAttendanceDays }}
                        <span class="font-body-md text-body-md text-on-surface-variant">
                            hari
                        </span>
                    </p>
                </div>

                <div class="w-10 h-10 rounded-lg bg-secondary-container/50 text-secondary flex items-center justify-center shrink-0">
                    <span class="material-symbols-outlined text-[22px]">
                        event_available
                    </span>
                </div>
            </div>

            <p class="mt-2 font-body-sm text-body-sm text-on-surface-variant">
                Hari dengan data presensi
            </p>
        </article>


        {{-- LATE --}}
        <article class="timesheet-card timesheet-kpi bg-surface-container-lowest rounded-xl p-space-base">

            <div class="flex items-start justify-between gap-3">

                <div class="min-w-0">
                    <p class="font-label-sm text-label-sm text-on-surface-variant uppercase tracking-wider">
                        Hari Telat
                    </p>

                    <p class="mt-2 font-headline-md text-headline-md text-primary timesheet-number">
                        {{ $displayLateDays }}
                        <span class="font-body-md text-body-md text-on-surface-variant">
                            hari
                        </span>
                    </p>
                </div>

                <div class="w-10 h-10 rounded-lg bg-error/10 text-error flex items-center justify-center shrink-0">
                    <span class="material-symbols-outlined text-[22px]">
                        timer_off
                    </span>
                </div>
            </div>

            <p class="mt-2 font-body-sm text-body-sm text-on-surface-variant">
                Terdapat keterlambatan masuk
            </p>
        </article>
    </section>


    {{-- ============================================================
         TABLE
         ============================================================ --}}
    <section class="timesheet-card bg-surface-container-lowest rounded-xl overflow-hidden">

        <div class="px-space-base py-4 border-b border-surface-container-low flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">

            <div>
                <h2 class="font-title-md text-title-md text-on-surface">
                    Detail Timesheet
                </h2>

                <p class="mt-0.5 font-body-sm text-body-sm text-on-surface-variant">
                    Presensi harian, keterlambatan, status, dan lembur resmi.
                </p>
            </div>

            <div class="inline-flex items-center gap-2 text-body-sm text-on-surface-variant">
                <span class="material-symbols-outlined text-[18px]">
                    verified
                </span>
                Verified HR
            </div>
        </div>

        <div class="timesheet-table-wrap">

            <table class="timesheet-table w-full text-left">

                <thead>
                    <tr class="bg-surface-container-low text-on-surface-variant font-label-sm text-label-sm uppercase tracking-wider">

                        @if ($hrMode || $vpMode)

                            <th class="py-3 px-space-base">
                                Karyawan
                            </th>

                            <th class="py-3 px-space-base">
                                Band
                            </th>

                            <th class="py-3 px-space-base">
                                Divisi
                            </th>

                            <th class="py-3 px-space-base">
                                Tanggal
                            </th>

                        @else

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

                        <th class="py-3 px-space-base text-right">
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

                        <tr>

                            {{-- KARYAWAN / TANGGAL --}}
                            @if ($hrMode || $vpMode)

                                <td class="py-4 px-space-base align-top">

                                    <div class="font-title-sm text-title-sm text-primary">
                                        {{ $row['employee_name'] ?? '-' }}
                                    </div>

                                    @if (!empty($row['employee_nipeg']))
                                        <div class="mt-1 font-label-sm text-label-sm text-on-surface-variant">
                                            NIPEG {{ $row['employee_nipeg'] }}
                                        </div>
                                    @endif

                                </td>

                                <td class="py-4 px-space-base align-top">

                                    @if (($row['employee_band'] ?? null) !== null)
                                        <span class="timesheet-badge bg-primary-container/40 text-primary">
                                            Band {{ $row['employee_band'] }}
                                        </span>
                                    @else
                                        <span class="text-on-surface-variant">
                                            -
                                        </span>
                                    @endif

                                </td>

                                <td class="py-4 px-space-base align-top">
                                    <span class="text-on-surface">
                                        {{ $row['employee_division'] ?? '-' }}
                                    </span>
                                </td>

                                <td class="py-4 px-space-base align-top">

                                    @if ($tanggal)

                                        <div class="font-title-sm text-title-sm text-primary">
                                            {{ $tanggal->translatedFormat('l, d M Y') }}
                                        </div>

                                        @if ($tanggal->isWeekend())
                                            <span class="mt-1 inline-flex items-center gap-1 text-label-sm text-on-surface-variant">
                                                <span class="material-symbols-outlined text-[15px]">
                                                    event
                                                </span>
                                                Akhir pekan
                                            </span>
                                        @endif

                                    @else
                                        -
                                    @endif

                                </td>

                            @else

                                <td class="py-4 px-space-base align-top">

                                    @if ($tanggal)

                                        <div class="font-title-sm text-title-sm text-primary">
                                            {{ $tanggal->translatedFormat('l, d M Y') }}
                                        </div>

                                        @if ($tanggal->isWeekend())
                                            <span class="mt-1 inline-flex items-center gap-1 text-label-sm text-on-surface-variant">
                                                <span class="material-symbols-outlined text-[15px]">
                                                    event
                                                </span>
                                                Akhir pekan
                                            </span>
                                        @endif

                                    @else
                                        -
                                    @endif

                                </td>

                            @endif


                            {{-- JAM MASUK --}}
                            <td class="py-4 px-space-base align-top">
                                <span class="timesheet-number font-title-sm text-title-sm text-on-surface">
                                    {{ !empty($row['jam_masuk'])
                                        ? \Carbon\Carbon::parse($row['jam_masuk'])->format('H:i')
                                        : '-' }}
                                </span>
                            </td>


                            {{-- JAM PULANG --}}
                            <td class="py-4 px-space-base align-top">
                                <span class="timesheet-number font-title-sm text-title-sm text-on-surface">
                                    {{ !empty($row['jam_pulang'])
                                        ? \Carbon\Carbon::parse($row['jam_pulang'])->format('H:i')
                                        : '-' }}
                                </span>
                            </td>


                            {{-- DURASI --}}
                            <td class="py-4 px-space-base align-top">

                                @if (
                                    isset($row['durasi_kerja_menit'])
                                    && $row['durasi_kerja_menit'] !== null
                                )

                                    @php
                                        $durationMinutes = (int) $row['durasi_kerja_menit'];
                                    @endphp

                                    <span class="timesheet-number font-title-sm text-title-sm text-on-surface">
                                        {{ intdiv($durationMinutes, 60) }}j
                                        {{ str_pad(
                                            (string) ($durationMinutes % 60),
                                            2,
                                            '0',
                                            STR_PAD_LEFT
                                        ) }}m
                                    </span>

                                @else
                                    <span class="text-on-surface-variant">-</span>
                                @endif

                            </td>


                            {{-- TELAT --}}
                            <td class="py-4 px-space-base align-top">

                                @if ($lateMinutes > 0)

                                    <span class="timesheet-number inline-flex items-center gap-1 font-title-sm text-title-sm text-error">
                                        <span class="material-symbols-outlined text-[17px]">
                                            schedule
                                        </span>

                                        {{ $lateMinutes }} menit
                                    </span>

                                @else

                                    <span class="font-body-sm text-body-sm text-on-surface-variant">
                                        0 menit
                                    </span>

                                @endif

                            </td>


                            {{-- STATUS --}}
                            <td class="py-4 px-space-base align-top">

                                @php
                                    $statusClass =
                                        $status === 'Hadir'
                                            ? 'bg-secondary-container/50 text-secondary'
                                            : (
                                                $status === 'Hadir + Lembur'
                                                    ? 'bg-tertiary-container text-on-tertiary-container'
                                                    : 'bg-surface-container text-on-surface-variant'
                                            );
                                @endphp

                                <span class="timesheet-status {{ $statusClass }}">
                                    {{ $status }}
                                </span>

                            </td>


                            {{-- OT --}}
                            <td class="py-4 px-space-base align-top text-right">

                                @if ($overtimeMinutes > 0)

                                    <div class="font-title-sm text-title-sm text-primary timesheet-number">
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
                                    </div>

                                    <div class="mt-1 inline-flex items-center justify-end gap-1 font-label-sm text-label-sm text-on-surface-variant">
                                        <span class="material-symbols-outlined text-[14px]">
                                            verified
                                        </span>
                                        Verified HR
                                    </div>

                                @else

                                    <span class="text-on-surface-variant">
                                        -
                                    </span>

                                @endif

                            </td>

                        </tr>

                    @empty

                        <tr>
                            <td
                                colspan="{{ ($hrMode || $vpMode) ? 10 : 7 }}"
                                class="py-16 px-6 text-center"
                            >

                                <div class="mx-auto max-w-md">

                                    <div class="mx-auto w-12 h-12 rounded-full bg-surface-container flex items-center justify-center text-on-surface-variant">
                                        <span class="material-symbols-outlined text-[25px]">
                                            event_busy
                                        </span>
                                    </div>

                                    <h3 class="mt-4 font-title-md text-title-md text-on-surface">
                                        Belum ada data timesheet
                                    </h3>

                                    <p class="mt-1 font-body-sm text-body-sm text-on-surface-variant">
                                        @if ($hrMode || $vpMode)
                                            Tidak ada data karyawan atau presensi untuk filter yang dipilih.
                                        @else
                                            Belum ada data timesheet untuk periode ini.
                                        @endif
                                    </p>

                                </div>

                            </td>
                        </tr>

                    @endforelse

                </tbody>
            </table>
        </div>
    </section>


    {{-- ============================================================
         PAGINATION
         ============================================================ --}}
    @if ($timesheetRows instanceof \Illuminate\Pagination\LengthAwarePaginator)

        <div class="pt-1">
            {{ $timesheetRows->links() }}
        </div>

    @endif


    {{-- ============================================================
         FOOTER INFORMATION
         ============================================================ --}}
    <section class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 text-body-sm text-on-surface-variant">

        <div class="flex flex-wrap items-center gap-x-2 gap-y-1">
            <span>
                Periode:
                <strong class="text-on-surface">
                    {{ $displayStartDate->translatedFormat('F Y') }}
                </strong>
            </span>

            @if ($hrMode || $vpMode)
                <span aria-hidden="true">•</span>

                <span>
                    Scope:
                    <strong class="text-on-surface">
                        {{ $employees->count() }} karyawan
                    </strong>
                </span>
            @endif
        </div>

        <div class="inline-flex items-center gap-1.5">
            <span class="material-symbols-outlined text-[16px]">
                verified
            </span>

            <span>
                Lembur resmi hanya berasal dari pengajuan
                <strong class="text-on-surface">
                    Verified HR
                </strong>.
            </span>
        </div>

    </section>

</div>
@endsection
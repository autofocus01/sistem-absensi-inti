@extends('layouts.absensi')


@section('title', 'Laporan HR - PT. INTI')

@section('content')
<div class="inti-page p1-hr-governance">

    {{-- =========================================================
         HEADER
         ========================================================= --}}
    <section class="mb-6">
        <div class="flex flex-col gap-4 xl:flex-row xl:items-end xl:justify-between">

            <div class="min-w-0">
                <div class="mb-2 flex items-center gap-2">
                    <span class="material-symbols-outlined text-[20px] text-primary">
                        summarize
                    </span>

                    <span class="text-[11px] font-extrabold uppercase tracking-[0.12em] text-on-surface-variant">
                        REPORTING • HR
                    </span>
                </div>

                <h1 class="font-headline-lg text-headline-lg tracking-tight text-primary">
                    Laporan HR
                </h1>

                <p class="mt-1 max-w-3xl text-sm leading-6 text-on-surface-variant">
                    Rekap presensi dan lembur resmi yang telah diverifikasi HR berdasarkan periode dan divisi.
                </p>
            </div>


            {{-- EXPORT --}}
            <div class="flex w-full flex-col gap-2 sm:w-auto sm:flex-row">

                <a
                    href="{{ route('reports.excel', request()->query()) }}"
                    class="inline-flex min-h-11 items-center justify-center gap-2 rounded-xl border border-secondary/30 bg-secondary/10 px-4 py-2.5 text-sm font-extrabold text-secondary transition hover:bg-secondary/20 focus:outline-none focus-visible:ring-2 focus-visible:ring-secondary focus-visible:ring-offset-2"
                >
                    <span class="material-symbols-outlined text-[19px]">
                        table_view
                    </span>
                    Excel
                </a>

                <a
                    href="{{ route('reports.pdf', request()->query()) }}"
                    class="inline-flex min-h-11 items-center justify-center gap-2 rounded-xl bg-primary-container px-4 py-2.5 text-sm font-extrabold text-on-primary shadow-sm transition hover:bg-primary focus:outline-none focus-visible:ring-2 focus-visible:ring-primary focus-visible:ring-offset-2"
                >
                    <span class="material-symbols-outlined text-[19px]">
                        picture_as_pdf
                    </span>
                    PDF
                </a>

            </div>

        </div>
    </section>


    {{-- =========================================================
         PERIOD CONTEXT
         ========================================================= --}}
    <section class="mb-5 rounded-xl border border-primary/15 bg-primary-container/10 px-4 py-3.5 sm:px-5">
        <div class="flex items-start gap-3">

            <span class="material-symbols-outlined mt-0.5 shrink-0 text-[20px] text-primary">
                calendar_month
            </span>

            <div class="min-w-0">
                <p class="text-xs font-bold uppercase tracking-wide text-primary">
                    Periode laporan
                </p>

                <p class="mt-0.5 text-sm font-bold text-on-surface">
                    {{ $monthLabel }}
                    @if ($divisionId)
                        <span class="font-normal text-on-surface-variant">
                            · Divisi terpilih
                        </span>
                    @else
                        <span class="font-normal text-on-surface-variant">
                            · Semua Divisi
                        </span>
                    @endif
                </p>
            </div>

        </div>
    </section>


    {{-- =========================================================
         FILTER
         ========================================================= --}}
    <section class="mb-5 rounded-2xl border border-outline-variant/40 bg-surface-container-lowest shadow-sm">

        <div class="border-b border-outline-variant/30 px-4 py-4 sm:px-5">
            <div class="flex items-center gap-3">

                <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-primary-container/40">
                    <span class="material-symbols-outlined text-[19px] text-primary">
                        filter_alt
                    </span>
                </div>

                <div>
                    <h2 class="text-sm font-extrabold text-on-surface">
                        Parameter Laporan
                    </h2>

                    <p class="mt-0.5 text-xs leading-5 text-on-surface-variant">
                        Pilih periode dan divisi yang ingin ditampilkan.
                    </p>
                </div>

            </div>
        </div>


        <form
            method="GET"
            action="{{ route('reports.index') }}"
            class="grid grid-cols-1 gap-4 p-4 sm:p-5 md:grid-cols-2 xl:grid-cols-[minmax(170px,220px)_minmax(120px,160px)_minmax(220px,1fr)_auto_auto]"
        >

            {{-- MONTH --}}
            <div>
                <label
                    for="report-month"
                    class="mb-1.5 block text-xs font-bold text-on-surface"
                >
                    Bulan
                </label>

                <select
                    id="report-month"
                    name="month"
                    class="min-h-11 w-full rounded-xl border border-outline-variant/60 bg-surface-container-low px-3 text-sm text-on-surface outline-none transition focus:border-primary focus:ring-2 focus:ring-primary/20"
                >
                    @foreach (range(1, 12) as $m)
                        <option
                            value="{{ $m }}"
                            @selected($month === $m)
                        >
                            {{ \Carbon\Carbon::create()->month($m)->translatedFormat('F') }}
                        </option>
                    @endforeach
                </select>
            </div>


            {{-- YEAR --}}
            <div>
                <label
                    for="report-year"
                    class="mb-1.5 block text-xs font-bold text-on-surface"
                >
                    Tahun
                </label>

                <input
                    id="report-year"
                    type="number"
                    name="year"
                    value="{{ $year }}"
                    min="2000"
                    max="2100"
                    inputmode="numeric"
                    class="min-h-11 w-full rounded-xl border border-outline-variant/60 bg-surface-container-low px-3 text-sm tabular-nums text-on-surface outline-none transition focus:border-primary focus:ring-2 focus:ring-primary/20"
                >
            </div>


            {{-- DIVISION --}}
            <div>
                <label
                    for="report-division"
                    class="mb-1.5 block text-xs font-bold text-on-surface"
                >
                    Divisi
                </label>

                <select
                    id="report-division"
                    name="division_id"
                    class="min-h-11 w-full rounded-xl border border-outline-variant/60 bg-surface-container-low px-3 text-sm text-on-surface outline-none transition focus:border-primary focus:ring-2 focus:ring-primary/20"
                >
                    <option value="">
                        Semua Divisi
                    </option>

                    @foreach ($divisions as $division)
                        <option
                            value="{{ $division->id }}"
                            @selected($divisionId === $division->id)
                        >
                            {{ $division->nama }}
                        </option>
                    @endforeach
                </select>
            </div>


            {{-- APPLY --}}
            <div class="flex items-end">
                <button
                    type="submit"
                    class="inline-flex min-h-11 w-full items-center justify-center gap-2 rounded-xl bg-primary-container px-4 py-2.5 text-sm font-extrabold text-on-primary transition hover:bg-primary focus:outline-none focus-visible:ring-2 focus-visible:ring-primary focus-visible:ring-offset-2 xl:w-auto"
                >
                    <span class="material-symbols-outlined text-[19px]">
                        search
                    </span>
                    Tampilkan
                </button>
            </div>


            {{-- RESET --}}
            <div class="flex items-end">
                <a
                    href="{{ route('reports.index') }}"
                    class="inline-flex min-h-11 w-full items-center justify-center gap-2 rounded-xl border border-outline-variant/60 bg-surface-container-low px-4 py-2.5 text-sm font-bold text-on-surface transition hover:bg-surface-container focus:outline-none focus-visible:ring-2 focus-visible:ring-primary focus-visible:ring-offset-2 xl:w-auto"
                >
                    <span class="material-symbols-outlined text-[18px]">
                        restart_alt
                    </span>
                    Reset
                </a>
            </div>

        </form>

    </section>


    {{-- =========================================================
         KPI SUMMARY
         ========================================================= --}}
    <section class="mb-5 grid grid-cols-1 gap-3 sm:grid-cols-2 xl:grid-cols-4">

        {{-- EMPLOYEE --}}
        <div class="rounded-2xl border border-outline-variant/40 bg-surface-container-lowest p-4 shadow-sm sm:p-5">
            <div class="flex items-start justify-between gap-3">

                <div class="min-w-0">
                    <p class="text-[11px] font-extrabold uppercase tracking-[0.08em] text-on-surface-variant">
                        Karyawan
                    </p>

                    <p class="mt-2 text-2xl font-extrabold tabular-nums text-primary">
                        {{ number_format($summary['employee_count']) }}
                    </p>

                    <p class="mt-1 text-xs text-on-surface-variant">
                        Dalam periode laporan
                    </p>
                </div>

                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-primary-container/40">
                    <span class="material-symbols-outlined text-primary">
                        groups
                    </span>
                </div>

            </div>
        </div>


        {{-- ATTENDANCE --}}
        <div class="rounded-2xl border border-outline-variant/40 bg-surface-container-lowest p-4 shadow-sm sm:p-5">
            <div class="flex items-start justify-between gap-3">

                <div class="min-w-0">
                    <p class="text-[11px] font-extrabold uppercase tracking-[0.08em] text-on-surface-variant">
                        Kehadiran
                    </p>

                    <p class="mt-2 text-2xl font-extrabold tabular-nums text-primary">
                        {{ number_format($summary['attendance_avg'], 1) }}%
                    </p>

                    <p class="mt-1 text-xs text-on-surface-variant">
                        Rata-rata kehadiran
                    </p>
                </div>

                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-secondary-container/60">
                    <span class="material-symbols-outlined text-secondary">
                        event_available
                    </span>
                </div>

            </div>
        </div>


        {{-- LATE --}}
        <div class="rounded-2xl border border-outline-variant/40 bg-surface-container-lowest p-4 shadow-sm sm:p-5">
            <div class="flex items-start justify-between gap-3">

                <div class="min-w-0">
                    <p class="text-[11px] font-extrabold uppercase tracking-[0.08em] text-on-surface-variant">
                        Keterlambatan
                    </p>

                    <p class="mt-2 text-2xl font-extrabold tabular-nums text-primary">
                        {{ number_format($summary['late_minutes']) }}
                    </p>

                    <p class="mt-1 text-xs text-on-surface-variant">
                        Total menit terlambat
                    </p>
                </div>

                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-surface-container">
                    <span class="material-symbols-outlined text-primary">
                        schedule
                    </span>
                </div>

            </div>
        </div>


        {{-- OFFICIAL OT --}}
        <div class="rounded-2xl border border-outline-variant/40 bg-surface-container-lowest p-4 shadow-sm sm:p-5">
            <div class="flex items-start justify-between gap-3">

                <div class="min-w-0">
                    <p class="text-[11px] font-extrabold uppercase tracking-[0.08em] text-on-surface-variant">
                        OT Resmi
                    </p>

                    <p class="mt-2 text-2xl font-extrabold tabular-nums text-secondary">
                        {{ $formatMinutes($summary['official_ot_minutes'] ?? 0) }}
                    </p>

                    <p class="mt-1 text-xs text-on-surface-variant">
                        Lembur resmi periode ini
                    </p>
                </div>

                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-secondary-container/60">
                    <span class="material-symbols-outlined text-secondary">
                        more_time
                    </span>
                </div>

            </div>
        </div>

    </section>


    {{-- =========================================================
         SECONDARY SUMMARY
         ========================================================= --}}
    <section class="mb-5 grid grid-cols-2 gap-3 lg:grid-cols-4">

        <div class="rounded-xl border border-outline-variant/30 bg-surface-container-low px-4 py-3.5">
            <div class="text-[11px] font-bold uppercase tracking-wide text-on-surface-variant">
                Hadir
            </div>

            <div class="mt-1 text-lg font-extrabold tabular-nums text-on-surface">
                {{ number_format($summary['present_days']) }}
            </div>

            <div class="mt-0.5 text-xs text-on-surface-variant">
                hari hadir
            </div>
        </div>


        <div class="rounded-xl border border-outline-variant/30 bg-surface-container-low px-4 py-3.5">
            <div class="text-[11px] font-bold uppercase tracking-wide text-on-surface-variant">
                Hari Telat
            </div>

            <div class="mt-1 text-lg font-extrabold tabular-nums text-on-surface">
                {{ number_format($summary['late_days']) }}
            </div>

            <div class="mt-0.5 text-xs text-on-surface-variant">
                kejadian
            </div>
        </div>


        <div class="rounded-xl border border-outline-variant/30 bg-surface-container-low px-4 py-3.5">
            <div class="text-[11px] font-bold uppercase tracking-wide text-on-surface-variant">
                Alpha
            </div>

            <div class="mt-1 text-lg font-extrabold tabular-nums text-on-surface">
                {{ number_format($summary['alpha_days']) }}
            </div>

            <div class="mt-0.5 text-xs text-on-surface-variant">
                hari
            </div>
        </div>


        <div class="rounded-xl border border-outline-variant/30 bg-surface-container-low px-4 py-3.5">
            <div class="text-[11px] font-bold uppercase tracking-wide text-on-surface-variant">
                OT Pending
            </div>

            <div class="mt-1 text-lg font-extrabold tabular-nums text-on-surface">
                {{ $formatMinutes(($summary['pending_vp_minutes'] ?? 0) + ($summary['pending_hr_minutes'] ?? 0)) }}
            </div>

            <div class="mt-0.5 text-xs text-on-surface-variant">
                VP + HR
            </div>
        </div>

    </section>


    {{-- =========================================================
         TABLE
         ========================================================= --}}
    <section class="overflow-hidden rounded-2xl border border-outline-variant/40 bg-surface-container-lowest shadow-sm">

        <div class="flex flex-col gap-3 border-b border-outline-variant/30 px-4 py-4 sm:px-5 lg:flex-row lg:items-center lg:justify-between">

            <div class="min-w-0">
                <div class="flex items-center gap-2">
                    <span class="material-symbols-outlined text-[20px] text-primary">
                        table_rows
                    </span>

                    <h2 class="text-base font-extrabold text-on-surface">
                        Rekap Per Karyawan
                    </h2>
                </div>

                <p class="mt-1 text-xs leading-5 text-on-surface-variant">
                    {{ $monthLabel }} ·
                    {{ $divisionId ? 'Divisi terpilih' : 'Semua divisi' }}
                </p>
            </div>


            <div class="inline-flex w-fit items-center gap-2 rounded-lg bg-surface-container-low px-3 py-2">
                <span class="material-symbols-outlined text-[17px] text-on-surface-variant">
                    swipe
                </span>

                <span class="text-xs font-semibold text-on-surface-variant">
                    Geser tabel untuk melihat kolom lainnya
                </span>
            </div>

        </div>


        <div class="overflow-x-auto">
            <table class="w-full min-w-[1080px] text-left">

                <thead>
                    <tr class="bg-surface-container-low text-[11px] font-extrabold uppercase tracking-[0.08em] text-on-surface-variant">

                        <th class="whitespace-nowrap px-4 py-3.5">
                            NIPEG
                        </th>

                        <th class="px-4 py-3.5">
                            Nama
                        </th>

                        <th class="px-4 py-3.5">
                            Divisi
                        </th>

                        <th class="whitespace-nowrap px-4 py-3.5 text-center">
                            Hadir
                        </th>

                        <th class="whitespace-nowrap px-4 py-3.5 text-center">
                            Hari Telat
                        </th>

                        <th class="whitespace-nowrap px-4 py-3.5 text-center">
                            Menit Telat
                        </th>

                        <th class="whitespace-nowrap px-4 py-3.5 text-center">
                            Alpha
                        </th>

                        <th class="whitespace-nowrap px-4 py-3.5 text-center">
                            Kehadiran
                        </th>

                        <th class="whitespace-nowrap px-4 py-3.5 text-right">
                            OT Resmi
                        </th>

                    </tr>
                </thead>


                <tbody class="divide-y divide-outline-variant/20 text-sm">

                    @forelse ($rows as $row)

                        <tr class="transition-colors hover:bg-surface-container-low/50">

                            {{-- NIPEG --}}
                            <td class="whitespace-nowrap px-4 py-4 align-top">
                                <span class="font-mono text-xs font-semibold tabular-nums text-on-surface-variant">
                                    {{ $row['nipeg'] }}
                                </span>
                            </td>


                            {{-- NAME --}}
                            <td class="min-w-[210px] px-4 py-4 align-top">
                                <div class="flex items-start gap-3">

                                    <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-primary-container/40">
                                        <span class="material-symbols-outlined text-[18px] text-primary">
                                            person
                                        </span>
                                    </div>

                                    <div class="min-w-0">
                                        <div class="truncate font-bold text-on-surface">
                                            {{ $row['nama'] }}
                                        </div>

                                        <div class="mt-0.5 text-xs text-on-surface-variant">
                                            {{ $row['nipeg'] }}
                                        </div>
                                    </div>

                                </div>
                            </td>


                            {{-- DIVISION --}}
                            <td class="min-w-[170px] px-4 py-4 align-top">
                                <span class="inline-flex max-w-full rounded-lg bg-surface-container px-2.5 py-1 text-xs font-bold text-primary">
                                    <span class="truncate">
                                        {{ $row['divisi'] }}
                                    </span>
                                </span>
                            </td>


                            {{-- PRESENT --}}
                            <td class="whitespace-nowrap px-4 py-4 text-center align-top">
                                <span class="font-bold tabular-nums text-on-surface">
                                    {{ number_format($row['hadir']) }}
                                </span>
                            </td>


                            {{-- LATE DAYS --}}
                            <td class="whitespace-nowrap px-4 py-4 text-center align-top">
                                @if ($row['telat_hari'] > 0)
                                    <span class="inline-flex items-center justify-center rounded-full bg-surface-container px-2.5 py-1 text-xs font-bold tabular-nums text-on-surface">
                                        {{ number_format($row['telat_hari']) }}
                                    </span>
                                @else
                                    <span class="text-sm font-semibold text-secondary">
                                        0
                                    </span>
                                @endif
                            </td>


                            {{-- LATE MINUTES --}}
                            <td class="whitespace-nowrap px-4 py-4 text-center align-top">
                                @if ($row['menit_telat'] > 0)
                                    <span class="font-bold tabular-nums text-on-surface">
                                        {{ number_format($row['menit_telat']) }}
                                    </span>
                                    <span class="text-xs text-on-surface-variant">
                                        mnt
                                    </span>
                                @else
                                    <span class="font-semibold text-secondary">
                                        0
                                    </span>
                                @endif
                            </td>


                            {{-- ALPHA --}}
                            <td class="whitespace-nowrap px-4 py-4 text-center align-top">
                                @if ($row['alpha'] > 0)
                                    <span class="inline-flex items-center justify-center rounded-full bg-surface-container px-2.5 py-1 text-xs font-bold tabular-nums text-on-surface">
                                        {{ number_format($row['alpha']) }}
                                    </span>
                                @else
                                    <span class="font-semibold text-secondary">
                                        0
                                    </span>
                                @endif
                            </td>


                            {{-- ATTENDANCE --}}
                            <td class="whitespace-nowrap px-4 py-4 text-center align-top">

                                @php
                                    $attendance = (float) $row['persen_kehadiran'];

                                    if ($attendance >= 95) {
                                        $attendanceClass = 'bg-secondary-container/60 text-secondary';
                                    } elseif ($attendance >= 85) {
                                        $attendanceClass = 'bg-surface-container text-on-surface';
                                    } else {
                                        $attendanceClass = 'bg-surface-container text-on-surface';
                                    }
                                @endphp

                                <span class="inline-flex items-center justify-center rounded-full px-2.5 py-1 text-xs font-extrabold tabular-nums {{ $attendanceClass }}">
                                    {{ number_format($attendance, 1) }}%
                                </span>

                            </td>


                            {{-- OT --}}
                            <td class="whitespace-nowrap px-4 py-4 text-right align-top">
                                <span class="font-extrabold tabular-nums text-secondary">
                                    {{ $formatMinutes($row['official_ot_minutes'] ?? 0) }}
                                </span>
                            </td>

                        </tr>

                    @empty

                        <tr>
                            <td colspan="9" class="px-6 py-16 text-center">

                                <div class="mx-auto flex max-w-md flex-col items-center">

                                    <div class="flex h-14 w-14 items-center justify-center rounded-2xl bg-surface-container">
                                        <span class="material-symbols-outlined text-[28px] text-on-surface-variant">
                                            data_info_alert
                                        </span>
                                    </div>

                                    <h3 class="mt-4 text-base font-extrabold text-on-surface">
                                        Belum ada data laporan
                                    </h3>

                                    <p class="mt-1 text-sm leading-6 text-on-surface-variant">
                                        Tidak terdapat data karyawan untuk parameter periode dan divisi yang dipilih.
                                    </p>

                                    <a
                                        href="{{ route('reports.index') }}"
                                        class="mt-4 inline-flex min-h-10 items-center gap-2 rounded-lg border border-outline-variant/60 px-4 text-sm font-bold text-primary transition hover:bg-surface-container-low"
                                    >
                                        <span class="material-symbols-outlined text-[18px]">
                                            restart_alt
                                        </span>
                                        Kembali ke Periode Default
                                    </a>

                                </div>

                            </td>
                        </tr>

                    @endforelse

                </tbody>

            </table>
        </div>


        {{-- TABLE FOOTER --}}
        <div class="border-t border-outline-variant/30 bg-surface-container-low/40 px-4 py-3.5 sm:px-5">

            <div class="flex flex-col gap-2 text-xs text-on-surface-variant sm:flex-row sm:items-center sm:justify-between">

                <span>
                    Sumber laporan: data presensi dan lembur resmi.
                </span>

                <span class="font-semibold">
                    Periode {{ $monthLabel }}
                </span>

            </div>

        </div>

    </section>

</div>
@endsection
@extends('layouts.absensi')

@section('title', 'Rekapitulasi Tim')

@section('p1_head')
<style>
    .team-recap-page {
        min-width: 0;
    }

    .team-card {
        border: 1px solid rgba(15, 23, 42, .07);
        box-shadow: 0 1px 3px rgba(15, 23, 42, .04);
    }

    .team-filter {
        border: 1px solid rgba(15, 23, 42, .08);
    }

    .team-input {
        min-height: 44px;
        transition:
            border-color 160ms ease,
            box-shadow 160ms ease,
            background-color 160ms ease;
    }

    .team-input:focus {
        outline: none;
        border-color: var(--primary, #1d4ed8);
        box-shadow: 0 0 0 3px rgba(29, 78, 216, .12);
    }

    .team-kpi {
        min-height: 136px;
    }

    .team-table-wrap {
        width: 100%;
        overflow-x: auto;
        overscroll-behavior-x: contain;
        -webkit-overflow-scrolling: touch;
        scrollbar-width: thin;
    }

    .team-table {
        min-width: 1080px;
        border-collapse: separate;
        border-spacing: 0;
    }

    .team-table th {
        white-space: nowrap;
    }

    .team-table tbody tr {
        transition: background-color 140ms ease;
    }

    .team-table tbody tr:hover {
        background: rgba(15, 23, 42, .025);
    }

    .team-number {
        font-variant-numeric: tabular-nums;
        font-feature-settings: "tnum";
    }

    .team-badge {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        min-height: 28px;
        padding: 5px 10px;
        border-radius: 999px;
        white-space: nowrap;
        font-size: 12px;
        line-height: 1.1;
        font-weight: 700;
    }

    .team-trend-card {
        min-height: 150px;
    }

    .team-mobile-note {
        display: none;
    }

    @media (max-width: 1023px) {
        .team-kpi {
            min-height: 120px;
        }

        .team-mobile-note {
            display: flex;
        }
    }

    @media (max-width: 767px) {
        .team-kpi {
            min-height: 112px;
        }

        .team-filter-actions {
            width: 100%;
        }

        .team-filter-actions > * {
            flex: 1 1 auto;
        }
    }

    @media (max-width: 379px) {
        .team-filter-actions {
            flex-direction: column;
        }

        .team-filter-actions > * {
            width: 100%;
        }
    }

    @media (prefers-reduced-motion: reduce) {
        .team-input,
        .team-table tbody tr {
            transition: none;
        }
    }
</style>
@endsection

@section('content')

@php
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

    $isVp = auth()->user()->isVp();

    $logsRows = $logs ?? collect();

    /*
     * Controller menyediakan statusLemburCounts.
     * Alias ini hanya untuk kebutuhan presentasi view.
     */
    $statusCounts = $statusLemburCounts ?? [];

    $bulanAktif = (int) ($bulan ?? now()->month);
    $tahunAktif = (int) ($tahun ?? now()->year);

    $divisionSelected = $divisionId ?? null;

    $totalPersonelValue = (int) ($totalPersonel ?? 0);
    $totalHariTelatValue = (int) ($totalHariTelat ?? 0);
    $pendingHours = (float) ($totalJamLemburPending ?? 0);
    $approvedHours = (float) ($totalJamLemburDisetujui ?? 0);
    $totalOvertimeRows = (int) ($totalLogLembur ?? 0);
@endphp

<div class="inti-page">

<div class="team-recap-page flex flex-col gap-space-base">

    {{-- =========================================================
         HEADER
         ========================================================= --}}
    <section class="flex flex-col lg:flex-row lg:items-end lg:justify-between gap-space-base">

        <div class="min-w-0">

            <div class="flex flex-wrap items-center gap-2 mb-2">

                <span class="font-label-sm text-label-sm text-on-surface-variant uppercase tracking-[0.12em]">
                    MONITORING TIM
                </span>

                @if ($isVp)
                    <span class="team-badge bg-primary-container/40 text-primary">
                        <span class="material-symbols-outlined text-[15px]">
                            groups
                        </span>
                        VP DIVISI
                    </span>
                @else
                    <span class="team-badge bg-primary-container/40 text-primary">
                        <span class="material-symbols-outlined text-[15px]">
                            admin_panel_settings
                        </span>
                        HR
                    </span>
                @endif

            </div>

            <h1 class="font-headline-lg text-headline-lg text-primary tracking-tight">
                Rekapitulasi Tim
            </h1>

            <p class="mt-1 max-w-3xl font-body-md text-body-md text-on-surface-variant">
                Ringkasan presensi, keterlambatan, dan lembur resmi berdasarkan periode yang dipilih.
            </p>

        </div>

        <div class="inline-flex items-center gap-2 px-3 py-2 rounded-lg bg-surface-container-low text-on-surface-variant shrink-0">

            <span class="material-symbols-outlined text-[18px]">
                calendar_month
            </span>

            <span class="font-label-sm text-label-sm">
                {{ $monthNames[$bulanAktif] ?? $bulanAktif }}
                {{ $tahunAktif }}
            </span>

        </div>

    </section>


    {{-- =========================================================
         FILTER
         ========================================================= --}}
    <section class="team-card team-filter bg-surface-container-lowest rounded-xl p-space-base">

        <div class="flex items-start gap-3 mb-space-base">

            <div class="w-10 h-10 rounded-lg bg-primary-container/40 text-primary flex items-center justify-center shrink-0">

                <span class="material-symbols-outlined">
                    tune
                </span>

            </div>

            <div class="min-w-0">

                <h2 class="font-title-md text-title-md text-on-surface">
                    Filter Rekap
                </h2>

                <p class="mt-0.5 font-body-sm text-body-sm text-on-surface-variant">
                    Pilih periode dan cakupan data yang ingin ditampilkan.
                </p>

            </div>

        </div>

        <form
            method="GET"
            action="{{ route('team-recap.index') }}"
            class="flex flex-col gap-space-base"
        >

            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-5 gap-space-base">

                {{-- BULAN --}}
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
                        class="team-input w-full rounded-lg border border-outline bg-surface text-on-surface px-3 py-2"
                    >
                        @foreach ($monthNames as $monthNumber => $monthName)
                            <option
                                value="{{ $monthNumber }}"
                                @selected($bulanAktif === $monthNumber)
                            >
                                {{ $monthName }}
                            </option>
                        @endforeach
                    </select>

                </div>


                {{-- TAHUN --}}
                <div>

                    <label
                        for="tahun"
                        class="block font-label-sm text-label-sm text-on-surface-variant mb-1.5"
                    >
                        Tahun
                    </label>

                    <input
                        id="tahun"
                        name="tahun"
                        type="number"
                        min="2000"
                        max="2100"
                        value="{{ $tahunAktif }}"
                        class="team-input w-full rounded-lg border border-outline bg-surface text-on-surface px-3 py-2"
                    >

                </div>


                {{-- DIVISI --}}
                <div class="xl:col-span-1">

                    <label
                        for="division_id"
                        class="block font-label-sm text-label-sm text-on-surface-variant mb-1.5"
                    >
                        Divisi
                    </label>

                    @if ($isVp)

                        {{-- VP tidak boleh memilih divisi lain.
                             Controller tetap menjadi enforcement utama. --}}
                        <select
                            id="division_id"
                            name="division_id"
                            disabled
                            class="team-input w-full rounded-lg border border-outline bg-surface-container-low text-on-surface-variant px-3 py-2 cursor-not-allowed"
                        >
                            @foreach (($divisions ?? collect()) as $division)
                                <option
                                    value="{{ $division->id }}"
                                    selected
                                >
                                    {{ $division->nama }}
                                </option>
                            @endforeach
                        </select>

                        <p class="mt-1.5 font-label-sm text-label-sm text-on-surface-variant">
                            Scope VP dikunci ke divisi sendiri.
                        </p>

                    @else

                        <select
                            id="division_id"
                            name="division_id"
                            class="team-input w-full rounded-lg border border-outline bg-surface text-on-surface px-3 py-2"
                        >

                            <option value="">
                                Semua Divisi
                            </option>

                            @foreach (($divisions ?? collect()) as $division)

                                <option
                                    value="{{ $division->id }}"
                                    @selected((int) $divisionSelected === (int) $division->id)
                                >
                                    {{ $division->nama }}
                                </option>

                            @endforeach

                        </select>

                    @endif

                </div>


                {{-- SEARCH --}}
                <div class="xl:col-span-2">

                    <label
                        for="q"
                        class="block font-label-sm text-label-sm text-on-surface-variant mb-1.5"
                    >
                        Cari Karyawan / NIPEG
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
                            type="search"
                            value="{{ $q ?? '' }}"
                            placeholder="Masukkan nama atau NIPEG..."
                            autocomplete="off"
                            class="team-input w-full rounded-lg border border-outline bg-surface text-on-surface pl-10 pr-3 py-2"
                        >

                    </div>

                </div>

            </div>


            <div class="team-filter-actions flex flex-wrap items-center gap-2">

                <button
                    type="submit"
                    class="inline-flex items-center justify-center gap-2 min-h-[44px] px-5 py-2 rounded-lg bg-primary text-on-primary font-title-sm hover:opacity-90 focus:outline-none focus:ring-2 focus:ring-primary/30"
                >

                    <span class="material-symbols-outlined text-[18px]">
                        search
                    </span>

                    Tampilkan

                </button>

                <a
                    href="{{ route('team-recap.index') }}"
                    class="inline-flex items-center justify-center gap-2 min-h-[44px] px-5 py-2 rounded-lg bg-surface-container text-on-surface font-title-sm hover:bg-surface-container-high focus:outline-none focus:ring-2 focus:ring-primary/20"
                >

                    <span class="material-symbols-outlined text-[18px]">
                        restart_alt
                    </span>

                    Reset

                </a>

            </div>

        </form>

        <div class="team-mobile-note mt-space-base items-start gap-2 rounded-lg bg-surface-container-low px-3 py-2 font-body-sm text-body-sm text-on-surface-variant">

            <span class="material-symbols-outlined text-[18px] shrink-0">
                swipe
            </span>

            <span>
                Pada layar kecil, tabel dapat digeser secara horizontal untuk melihat seluruh kolom.
            </span>

        </div>

    </section>


    {{-- =========================================================
         KPI
         ========================================================= --}}
    <section
        class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-space-base"
        aria-label="Ringkasan tim"
    >

        {{-- PERSONEL --}}
        <article class="team-card team-kpi bg-surface-container-lowest rounded-xl p-space-base">

            <div class="flex items-start justify-between gap-3">

                <div>

                    <p class="font-label-sm text-label-sm text-on-surface-variant uppercase tracking-wider">
                        Total Personel
                    </p>

                    <p class="mt-2 font-headline-md text-headline-md text-primary team-number">
                        {{ number_format($totalPersonelValue, 0, ',', '.') }}
                    </p>

                </div>

                <div class="w-10 h-10 rounded-lg bg-primary-container/40 text-primary flex items-center justify-center">

                    <span class="material-symbols-outlined">
                        groups
                    </span>

                </div>

            </div>

            <p class="mt-2 font-body-sm text-body-sm text-on-surface-variant">
                Dalam scope rekap saat ini
            </p>

        </article>


        {{-- TELAT --}}
        <article class="team-card team-kpi bg-surface-container-lowest rounded-xl p-space-base">

            <div class="flex items-start justify-between gap-3">

                <div>

                    <p class="font-label-sm text-label-sm text-on-surface-variant uppercase tracking-wider">
                        Hari Telat
                    </p>

                    <p class="mt-2 font-headline-md text-headline-md text-primary team-number">
                        {{ number_format($totalHariTelatValue, 0, ',', '.') }}
                    </p>

                </div>

                <div class="w-10 h-10 rounded-lg bg-error/10 text-error flex items-center justify-center">

                    <span class="material-symbols-outlined">
                        schedule
                    </span>

                </div>

            </div>

            <p class="mt-2 font-body-sm text-body-sm text-on-surface-variant">
                Catatan keterlambatan presensi
            </p>

        </article>


        {{-- PENDING --}}
        <article class="team-card team-kpi bg-surface-container-lowest rounded-xl p-space-base">

            <div class="flex items-start justify-between gap-3">

                <div>

                    <p class="font-label-sm text-label-sm text-on-surface-variant uppercase tracking-wider">
                        Lembur Pending VP
                    </p>

                    <p class="mt-2 font-headline-md text-headline-md text-primary team-number">
                        {{ number_format($pendingHours, 1, ',', '.') }}j
                    </p>

                </div>

                <div class="w-10 h-10 rounded-lg bg-tertiary-container/50 text-on-tertiary-container flex items-center justify-center">

                    <span class="material-symbols-outlined">
                        pending_actions
                    </span>

                </div>

            </div>

            <p class="mt-2 font-body-sm text-body-sm text-on-surface-variant">
                Menunggu proses persetujuan VP
            </p>

        </article>


        {{-- VERIFIED --}}
        <article class="team-card team-kpi bg-surface-container-lowest rounded-xl p-space-base">

            <div class="flex items-start justify-between gap-3">

                <div>

                    <p class="font-label-sm text-label-sm text-on-surface-variant uppercase tracking-wider">
                        Lembur Resmi
                    </p>

                    <p class="mt-2 font-headline-md text-headline-md text-primary team-number">
                        {{ number_format($approvedHours, 1, ',', '.') }}j
                    </p>

                </div>

                <div class="w-10 h-10 rounded-lg bg-secondary-container/50 text-secondary flex items-center justify-center">

                    <span class="material-symbols-outlined">
                        verified
                    </span>

                </div>

            </div>

            <p class="mt-2 font-body-sm text-body-sm text-on-surface-variant">
                Sudah Verified HR
            </p>

        </article>

    </section>


    {{-- =========================================================
         STATUS LEMBUR
         ========================================================= --}}
    <section class="team-card bg-surface-container-lowest rounded-xl p-space-base">

        <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-3 mb-space-base">

            <div>

                <div class="flex items-center gap-2">

                    <span class="material-symbols-outlined text-[21px] text-primary">
                        monitoring
                    </span>

                    <h2 class="font-headline-md text-headline-md text-primary">
                        Status Pengajuan Lembur
                    </h2>

                </div>

                <p class="mt-1 font-body-sm text-body-sm text-on-surface-variant">
                    Distribusi status pengajuan pada periode yang dipilih.
                </p>

            </div>

            <span class="font-body-sm text-body-sm text-on-surface-variant">
                {{ number_format($totalOvertimeRows, 0, ',', '.') }} lembur resmi
            </span>

        </div>

        @if (count($statusCounts) > 0)

            <div class="grid grid-cols-2 md:grid-cols-4 xl:grid-cols-6 gap-3">

                @foreach ($statusCounts as $label => $count)

                    <div class="rounded-lg bg-surface-container-low p-4">

                        <div class="font-label-sm text-label-sm text-on-surface-variant">
                            {{ $label }}
                        </div>

                        <div class="mt-2 font-headline-md text-headline-md text-primary team-number">
                            {{ number_format((int) $count, 0, ',', '.') }}
                        </div>

                    </div>

                @endforeach

            </div>

        @else

            <div class="rounded-lg bg-surface-container-low px-4 py-5 text-center">

                <span class="material-symbols-outlined text-[25px] text-on-surface-variant">
                    inbox
                </span>

                <p class="mt-2 font-body-sm text-body-sm text-on-surface-variant">
                    Belum ada data status pengajuan lembur untuk periode ini.
                </p>

            </div>

        @endif

    </section>


    {{-- =========================================================
         TREN MINGGUAN
         ========================================================= --}}
    <section class="team-card bg-surface-container-lowest rounded-xl p-space-base">

        <div class="mb-space-base">

            <div class="flex items-center gap-2">

                <span class="material-symbols-outlined text-[21px] text-primary">
                    show_chart
                </span>

                <h2 class="font-headline-md text-headline-md text-primary">
                    Tren Kehadiran Mingguan
                </h2>

            </div>

            <p class="mt-1 font-body-sm text-body-sm text-on-surface-variant">
                Persentase kehadiran berdasarkan data presensi dalam scope yang dipilih.
            </p>

        </div>

        <div class="grid grid-cols-2 md:grid-cols-4 xl:grid-cols-5 gap-3">

            @forelse (($trenMingguan ?? []) as $trend)

                <article class="team-trend-card rounded-lg border border-outline-variant/40 bg-surface-container-low p-4">

                    <div class="flex items-start justify-between gap-2">

                        <div>

                            <p class="font-title-sm text-title-sm text-primary">
                                {{ $trend['label'] ?? '-' }}
                            </p>

                            <p class="mt-0.5 font-label-sm text-label-sm text-on-surface-variant">
                                {{ $trend['rentang'] ?? '-' }}
                            </p>

                        </div>

                        <span class="material-symbols-outlined text-[19px] text-primary">
                            trending_up
                        </span>

                    </div>

                    <p class="mt-4 font-headline-md text-headline-md text-primary team-number">
                        {{ number_format((float) ($trend['rate'] ?? 0), 1, ',', '.') }}%
                    </p>

                </article>

            @empty

                <div class="col-span-full rounded-lg bg-surface-container-low p-6 text-center">

                    <span class="material-symbols-outlined text-[26px] text-on-surface-variant">
                        show_chart
                    </span>

                    <p class="mt-2 font-body-sm text-body-sm text-on-surface-variant">
                        Belum ada data tren kehadiran.
                    </p>

                </div>

            @endforelse

        </div>

        @if (!empty($trenMingguan))

            <div class="mt-space-base flex flex-wrap items-center gap-x-4 gap-y-1 font-label-sm text-label-sm text-on-surface-variant">

                <span>
                    Skala grafik:
                    {{ number_format((float) ($rateMin ?? 0), 1, ',', '.') }}%
                    –
                    {{ number_format((float) ($rateMax ?? 100), 1, ',', '.') }}%
                </span>

                <span>
                    Periode {{ $monthNames[$bulanAktif] ?? $bulanAktif }}
                    {{ $tahunAktif }}
                </span>

            </div>

        @endif

    </section>


    {{-- =========================================================
         LEMBUR RESMI TIM
         ========================================================= --}}
    <section class="team-card bg-surface-container-lowest rounded-xl overflow-hidden">

        <div class="px-space-base py-4 border-b border-surface-container-low">

            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">

                <div>

                    <div class="flex items-center gap-2">

                        <span class="material-symbols-outlined text-[21px] text-primary">
                            more_time
                        </span>

                        <h2 class="font-headline-md text-headline-md text-primary">
                            Lembur Resmi Tim
                        </h2>

                    </div>

                    <p class="mt-1 font-body-sm text-body-sm text-on-surface-variant">
                        Hanya pengajuan eligible yang telah Verified HR dan memiliki lembur terakui.
                    </p>

                </div>

                <span class="team-badge bg-secondary-container/50 text-secondary">

                    <span class="material-symbols-outlined text-[15px]">
                        verified
                    </span>

                    {{ number_format($totalOvertimeRows, 0, ',', '.') }} data

                </span>

            </div>

        </div>


        <div class="team-table-wrap">

            <table class="team-table w-full text-left">

                <thead>

                    <tr class="bg-surface-container-low text-on-surface-variant font-label-sm text-label-sm uppercase tracking-wider">

                        <th class="py-3 px-space-base">
                            Tanggal
                        </th>

                        <th class="py-3 px-space-base">
                            Karyawan
                        </th>

                        <th class="py-3 px-space-base">
                            NIPEG
                        </th>

                        <th class="py-3 px-space-base">
                            Divisi
                        </th>

                        <th class="py-3 px-space-base">
                            Jam Lembur
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

                    @forelse ($logsRows as $log)

                        @php
                            $employee = $log->user?->employee;
                            $division = $log->division ?? $employee?->division;
                            $recognized = (int) ($log->recognized_minutes ?? 0);
                        @endphp

                        <tr>

                            {{-- DATE --}}
                            <td class="py-4 px-space-base align-top">

                                <div class="font-title-sm text-title-sm text-primary">
                                    {{ $log->overtime_date?->translatedFormat('d M Y') ?? '-' }}
                                </div>

                            </td>


                            {{-- EMPLOYEE --}}
                            <td class="py-4 px-space-base align-top">

                                <div class="font-title-sm text-title-sm text-on-surface">
                                    {{ $employee?->nama ?? $log->user?->name ?? '-' }}
                                </div>

                                @if ($employee?->band_code !== null)

                                    <span class="mt-1 inline-flex team-badge bg-primary-container/30 text-primary">
                                        Band {{ $employee->band_code }}
                                    </span>

                                @endif

                            </td>


                            {{-- NIPEG --}}
                            <td class="py-4 px-space-base align-top team-number">
                                {{ $employee?->nipeg ?? '-' }}
                            </td>


                            {{-- DIVISION --}}
                            <td class="py-4 px-space-base align-top">
                                {{ $division?->nama ?? '-' }}
                            </td>


                            {{-- OVERTIME TIME --}}
                            <td class="py-4 px-space-base align-top team-number">

                                @if ($log->start_time || $log->end_time)

                                    <div class="inline-flex items-center gap-2">

                                        <span class="material-symbols-outlined text-[17px] text-on-surface-variant">
                                            schedule
                                        </span>

                                        <span>
                                            {{ $log->start_time
                                                ? \Carbon\Carbon::parse($log->start_time)->format('H:i')
                                                : '-' }}

                                            <span class="mx-1 text-on-surface-variant">
                                                —
                                            </span>

                                            {{ $log->end_time
                                                ? \Carbon\Carbon::parse($log->end_time)->format('H:i')
                                                : '-' }}
                                        </span>

                                    </div>

                                @else

                                    <span class="text-on-surface-variant">
                                        -
                                    </span>

                                @endif

                            </td>


                            {{-- STATUS --}}
                            <td class="py-4 px-space-base align-top">

                                <span class="team-badge bg-secondary-container/50 text-secondary">

                                    <span class="material-symbols-outlined text-[15px]">
                                        verified
                                    </span>

                                    Verified HR

                                </span>

                            </td>


                            {{-- RECOGNIZED --}}
                            <td class="py-4 px-space-base text-right align-top">

                                <div class="font-title-sm text-title-sm text-primary team-number">

                                    {{ intdiv($recognized, 60) }}j
                                    {{ str_pad((string) ($recognized % 60), 2, '0', STR_PAD_LEFT) }}m

                                </div>

                                <div class="mt-1 font-label-sm text-label-sm text-on-surface-variant">
                                    terakui
                                </div>

                            </td>

                        </tr>

                    @empty

                        <tr>

                            <td
                                colspan="7"
                                class="py-16 px-6 text-center"
                            >

                                <div class="mx-auto max-w-md">

                                    <div class="mx-auto w-12 h-12 rounded-full bg-surface-container flex items-center justify-center text-on-surface-variant">

                                        <span class="material-symbols-outlined text-[25px]">
                                            event_busy
                                        </span>

                                    </div>

                                    <h3 class="mt-4 font-title-md text-title-md text-on-surface">
                                        Belum ada lembur resmi
                                    </h3>

                                    <p class="mt-1 font-body-sm text-body-sm text-on-surface-variant">
                                        Tidak ditemukan lembur eligible yang sudah Verified HR pada periode dan scope yang dipilih.
                                    </p>

                                </div>

                            </td>

                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>

    </section>


    {{-- =========================================================
         PAGINATION
         ========================================================= --}}
    @if ($logsRows instanceof \Illuminate\Pagination\LengthAwarePaginator)

        <div class="pt-1">
            {{ $logsRows->links() }}
        </div>

    @endif


    {{-- =========================================================
         FOOTER INFO
         ========================================================= --}}
    <section class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 font-body-sm text-body-sm text-on-surface-variant">

        <div class="flex flex-wrap items-center gap-x-2 gap-y-1">

            <span>
                Periode:
                <strong class="text-on-surface">
                    {{ $monthNames[$bulanAktif] ?? $bulanAktif }}
                    {{ $tahunAktif }}
                </strong>
            </span>

            @if ($divisionSelected !== null && isset($divisions))

                @php
                    $selectedDivision = collect($divisions)->first(
                        fn ($division) => (int) $division->id === (int) $divisionSelected
                    );
                @endphp

                @if ($selectedDivision)

                    <span aria-hidden="true">•</span>

                    <span>
                        Divisi:
                        <strong class="text-on-surface">
                            {{ $selectedDivision->nama }}
                        </strong>
                    </span>

                @endif

            @endif

        </div>

        <div class="inline-flex items-center gap-1.5">

            <span class="material-symbols-outlined text-[16px]">
                security
            </span>

            <span>
                Cakupan data mengikuti otorisasi akun.
            </span>

        </div>

    </section>

</div>

</div>

@endsection
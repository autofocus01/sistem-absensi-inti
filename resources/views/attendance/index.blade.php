@extends('layouts.absensi')


@section('title', 'Rekap Absensi')

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
@endphp

<style>
    .attendance-page {
        display: flex;
        flex-direction: column;
        gap: 1.25rem;
    }

    .attendance-page .attendance-header {
        display: flex;
        align-items: flex-end;
        justify-content: space-between;
        gap: 1rem;
    }

    .attendance-page .attendance-eyebrow {
        display: inline-flex;
        align-items: center;
        gap: .4rem;
        color: var(--on-surface-variant, #43474d);
        font-size: 11px;
        line-height: 14px;
        font-weight: 700;
        letter-spacing: .08em;
        text-transform: uppercase;
    }

    .attendance-page .attendance-title {
        margin-top: .25rem;
        color: var(--primary, #001428);
        font-size: clamp(1.55rem, 2.5vw, 2rem);
        line-height: 1.2;
        font-weight: 700;
        letter-spacing: -.025em;
    }

    .attendance-page .attendance-description {
        margin-top: .4rem;
        max-width: 760px;
        color: var(--on-surface-variant, #43474d);
        font-size: .875rem;
        line-height: 1.45rem;
    }

    .attendance-page .attendance-primary-action {
        min-height: 44px;
        flex-shrink: 0;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: .5rem;
        padding: .7rem 1rem;
        border-radius: .7rem;
        background: var(--primary-container, #0f2942);
        color: var(--on-primary, #fff);
        font-size: .875rem;
        line-height: 1.25rem;
        font-weight: 700;
        text-decoration: none;
        transition:
            background-color .16s ease,
            transform .16s ease,
            box-shadow .16s ease;
    }

    .attendance-page .attendance-primary-action:hover {
        background: var(--primary, #001428);
        box-shadow: 0 6px 18px rgba(0, 20, 40, .12);
        transform: translateY(-1px);
    }

    .attendance-page .attendance-primary-action:focus-visible,
    .attendance-page button:focus-visible,
    .attendance-page a:focus-visible,
    .attendance-page input:focus-visible,
    .attendance-page select:focus-visible {
        outline: 3px solid rgba(15, 41, 66, .22);
        outline-offset: 2px;
    }

    .attendance-page .attendance-panel {
        background: var(--surface-container-lowest, #fff);
        border: 1px solid rgba(195, 198, 206, .55);
        border-radius: 1rem;
        box-shadow: 0 4px 16px rgba(15, 41, 66, .05);
    }

    .attendance-page .filter-panel {
        padding: 1rem;
    }

    .attendance-page .filter-grid {
        display: grid;
        grid-template-columns: minmax(220px, 1.6fr) repeat(4, minmax(130px, 1fr)) auto;
        gap: .75rem;
        align-items: end;
    }

    .attendance-page .field {
        min-width: 0;
    }

    .attendance-page .field-label {
        display: block;
        margin-bottom: .4rem;
        color: var(--on-surface-variant, #43474d);
        font-size: 11px;
        line-height: 14px;
        font-weight: 700;
        letter-spacing: .04em;
        text-transform: uppercase;
    }

    .attendance-page .field-control {
        width: 100%;
        min-height: 44px;
        border: 1px solid rgba(116, 119, 126, .34);
        border-radius: .7rem;
        background: var(--surface-container-low, #eff4ff);
        color: var(--on-surface, #0d1c2e);
        padding: .65rem .8rem;
        font-size: .875rem;
        line-height: 1.25rem;
        transition:
            border-color .16s ease,
            background-color .16s ease,
            box-shadow .16s ease;
    }

    .attendance-page .field-control:hover {
        border-color: rgba(116, 119, 126, .55);
    }

    .attendance-page .field-control:focus {
        border-color: var(--primary, #001428);
        background: var(--surface-container-lowest, #fff);
        box-shadow: 0 0 0 3px rgba(15, 41, 66, .10);
        outline: none;
    }

    .attendance-page .filter-actions {
        display: flex;
        align-items: center;
        gap: .5rem;
        min-height: 44px;
    }

    .attendance-page .filter-submit,
    .attendance-page .filter-reset {
        min-height: 44px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: .4rem;
        padding: .65rem .9rem;
        border-radius: .7rem;
        font-size: .875rem;
        line-height: 1.25rem;
        font-weight: 700;
        text-decoration: none;
        white-space: nowrap;
    }

    .attendance-page .filter-submit {
        border: 0;
        background: var(--primary-container, #0f2942);
        color: var(--on-primary, #fff);
        cursor: pointer;
    }

    .attendance-page .filter-submit:hover {
        background: var(--primary, #001428);
    }

    .attendance-page .filter-reset {
        border: 1px solid rgba(116, 119, 126, .24);
        background: var(--surface-container-low, #eff4ff);
        color: var(--on-surface, #0d1c2e);
    }

    .attendance-page .filter-reset:hover {
        background: var(--surface-container, #e6eeff);
    }

    .attendance-page .filter-helper {
        margin-top: .7rem;
        color: var(--on-surface-variant, #43474d);
        font-size: .75rem;
        line-height: 1rem;
    }

    .attendance-page .kpi-grid {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: .9rem;
    }

    .attendance-page .kpi-card {
        min-width: 0;
        padding: 1rem;
        border-radius: 1rem;
        background: var(--surface-container-lowest, #fff);
        border: 1px solid rgba(195, 198, 206, .5);
        box-shadow: 0 4px 16px rgba(15, 41, 66, .045);
    }

    .attendance-page .kpi-top {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: .75rem;
    }

    .attendance-page .kpi-label {
        color: var(--on-surface-variant, #43474d);
        font-size: 11px;
        line-height: 15px;
        font-weight: 700;
        letter-spacing: .055em;
        text-transform: uppercase;
    }

    .attendance-page .kpi-icon {
        width: 34px;
        height: 34px;
        flex-shrink: 0;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: .65rem;
        background: var(--surface-container-low, #eff4ff);
        color: var(--primary, #001428);
    }

    .attendance-page .kpi-value {
        margin-top: .65rem;
        color: var(--primary, #001428);
        font-size: 1.7rem;
        line-height: 2rem;
        font-weight: 750;
        letter-spacing: -.025em;
        font-variant-numeric: tabular-nums;
    }

    .attendance-page .kpi-value.error {
        color: var(--error, #ba1a1a);
    }

    .attendance-page .analytics-panel {
        padding: 1.15rem;
    }

    .attendance-page .section-heading {
        display: flex;
        align-items: center;
        gap: .7rem;
        min-width: 0;
    }

    .attendance-page .section-icon {
        width: 36px;
        height: 36px;
        flex-shrink: 0;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: .65rem;
        background: var(--surface-container-low, #eff4ff);
        color: var(--primary, #001428);
    }

    .attendance-page .section-title {
        color: var(--primary, #001428);
        font-size: 1rem;
        line-height: 1.35rem;
        font-weight: 700;
    }

    .attendance-page .section-description {
        margin-top: .2rem;
        color: var(--on-surface-variant, #43474d);
        font-size: .75rem;
        line-height: 1rem;
    }

    .attendance-page .chart-container {
        position: relative;
        width: 100%;
        height: 280px;
        margin-top: 1rem;
    }

    .attendance-page .chart-container.tall {
        height: 310px;
    }

    .attendance-page .chart-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: .9rem;
    }

    .attendance-page .table-panel {
        overflow: hidden;
    }

    .attendance-page .table-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 1rem;
        padding: 1.15rem;
        border-bottom: 1px solid rgba(195, 198, 206, .42);
    }

    .attendance-page .table-title {
        color: var(--primary, #001428);
        font-size: 1rem;
        line-height: 1.35rem;
        font-weight: 700;
    }

    .attendance-page .table-meta {
        color: var(--on-surface-variant, #43474d);
        font-size: .75rem;
        line-height: 1rem;
        white-space: nowrap;
    }

    .attendance-page .table-scroll {
        width: 100%;
        overflow-x: auto;
        overscroll-behavior-x: contain;
        scrollbar-width: thin;
    }

    .attendance-page table {
        border-collapse: separate;
        border-spacing: 0;
    }

    .attendance-page .attendance-table {
        width: 100%;
        min-width: 1080px;
    }

    .attendance-page .attendance-table th {
        position: sticky;
        top: 0;
        z-index: 1;
        padding: .8rem .9rem;
        background: var(--surface-container-low, #eff4ff);
        color: var(--on-surface-variant, #43474d);
        font-size: 10px;
        line-height: 14px;
        font-weight: 750;
        letter-spacing: .055em;
        text-transform: uppercase;
        white-space: nowrap;
    }

    .attendance-page .attendance-table td {
        padding: .85rem .9rem;
        color: var(--on-surface, #0d1c2e);
        font-size: .8125rem;
        line-height: 1.25rem;
        vertical-align: middle;
        border-top: 1px solid rgba(195, 198, 206, .28);
    }

    .attendance-page .attendance-table tbody tr {
        transition: background-color .14s ease;
    }

    .attendance-page .attendance-table tbody tr:hover {
        background: rgba(239, 244, 255, .65);
    }

    .attendance-page .employee-name {
        color: var(--primary, #001428);
        font-weight: 700;
        white-space: nowrap;
    }

    .attendance-page .employee-sub {
        margin-top: .15rem;
        color: var(--on-surface-variant, #43474d);
        font-size: .7rem;
        line-height: .95rem;
    }

    .attendance-page .badge {
        display: inline-flex;
        align-items: center;
        gap: .3rem;
        min-height: 26px;
        padding: .25rem .55rem;
        border-radius: 999px;
        font-size: 10px;
        line-height: 14px;
        font-weight: 700;
        white-space: nowrap;
    }

    .attendance-page .badge-band {
        background: rgba(15, 41, 66, .08);
        color: var(--primary, #001428);
    }

    .attendance-page .badge-division {
        background: var(--surface-container, #e6eeff);
        color: var(--primary, #001428);
    }

    .attendance-page .badge-good {
        background: rgba(134, 242, 228, .45);
        color: var(--secondary, #006a61);
    }

    .attendance-page .badge-normal {
        background: var(--surface-container, #e6eeff);
        color: var(--on-surface, #0d1c2e);
    }

    .attendance-page .badge-review {
        background: var(--error-container, #ffdad6);
        color: var(--error, #ba1a1a);
    }

    .attendance-page .location-stack {
        display: flex;
        flex-direction: column;
        gap: .3rem;
    }

    .attendance-page .location-item {
        display: inline-flex;
        align-items: center;
        gap: .35rem;
        white-space: nowrap;
    }

    .attendance-page .location-item .material-symbols-outlined {
        font-size: 15px;
        color: var(--on-surface-variant, #43474d);
    }

    .attendance-page .metric {
        font-variant-numeric: tabular-nums;
        white-space: nowrap;
    }

    .attendance-page .attendance-rate {
        display: flex;
        align-items: center;
        flex-wrap: wrap;
        gap: .4rem;
    }

    .attendance-page .actions {
        display: inline-flex;
        align-items: center;
        justify-content: flex-end;
        gap: .4rem;
    }

    .attendance-page .action-button {
        min-height: 36px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: .3rem;
        padding: .45rem .65rem;
        border-radius: .55rem;
        border: 1px solid transparent;
        background: transparent;
        font-size: .75rem;
        line-height: 1rem;
        font-weight: 700;
        cursor: pointer;
        white-space: nowrap;
    }

    .attendance-page .action-edit {
        color: var(--secondary, #006a61);
    }

    .attendance-page .action-edit:hover {
        background: rgba(0, 106, 97, .08);
    }

    .attendance-page .action-delete {
        color: var(--error, #ba1a1a);
    }

    .attendance-page .action-delete:hover {
        background: rgba(186, 26, 26, .08);
    }

    .attendance-page .empty-state {
        padding: 3.5rem 1.5rem;
        text-align: center;
        color: var(--on-surface-variant, #43474d);
    }

    .attendance-page .empty-icon {
        width: 48px;
        height: 48px;
        margin: 0 auto .75rem;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
        background: var(--surface-container-low, #eff4ff);
        color: var(--on-surface-variant, #43474d);
    }

    .attendance-page .pagination-wrap {
        padding: 1rem 1.15rem;
        border-top: 1px solid rgba(195, 198, 206, .38);
        overflow-x: auto;
    }

    .attendance-page .period-note {
        color: var(--on-surface-variant, #43474d);
        font-size: .75rem;
        line-height: 1rem;
    }

    @media (max-width: 1199px) {
        .attendance-page .filter-grid {
            grid-template-columns: repeat(3, minmax(0, 1fr));
        }

        .attendance-page .filter-grid .field-search {
            grid-column: 1 / -1;
        }

        .attendance-page .filter-actions {
            grid-column: 1 / -1;
            justify-content: flex-start;
        }
    }

    @media (max-width: 1023px) {
        .attendance-page .kpi-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .attendance-page .chart-grid {
            grid-template-columns: 1fr;
        }
    }

    @media (max-width: 767px) {
        .attendance-page {
            gap: 1rem;
        }

        .attendance-page .attendance-header {
            align-items: stretch;
            flex-direction: column;
        }

        .attendance-page .attendance-primary-action {
            width: 100%;
        }

        .attendance-page .filter-grid {
            grid-template-columns: 1fr;
        }

        .attendance-page .filter-grid .field-search,
        .attendance-page .filter-actions {
            grid-column: auto;
        }

        .attendance-page .filter-actions {
            display: grid;
            grid-template-columns: 1fr 1fr;
        }

        .attendance-page .filter-submit,
        .attendance-page .filter-reset {
            width: 100%;
        }

        .attendance-page .kpi-grid {
            grid-template-columns: 1fr 1fr;
            gap: .7rem;
        }

        .attendance-page .kpi-card {
            padding: .85rem;
        }

        .attendance-page .kpi-value {
            font-size: 1.45rem;
            line-height: 1.75rem;
        }

        .attendance-page .chart-container,
        .attendance-page .chart-container.tall {
            height: 250px;
        }

        .attendance-page .table-header {
            align-items: flex-start;
            flex-direction: column;
        }

        .attendance-page .table-meta {
            white-space: normal;
        }

        .attendance-page .analytics-panel,
        .attendance-page .table-header {
            padding: 1rem;
        }
    }

    @media (max-width: 379px) {
        .attendance-page .kpi-grid {
            grid-template-columns: 1fr;
        }

        .attendance-page .filter-actions {
            grid-template-columns: 1fr;
        }

        .attendance-page .chart-container,
        .attendance-page .chart-container.tall {
            height: 225px;
        }
    }

    @media (prefers-reduced-motion: reduce) {
        .attendance-page *,
        .attendance-page *::before,
        .attendance-page *::after {
            scroll-behavior: auto !important;
            transition-duration: .01ms !important;
            animation-duration: .01ms !important;
            animation-iteration-count: 1 !important;
        }
    }
</style>

<div class="inti-page p1-hr-governance attendance-page">

    {{-- =========================================================
         HEADER
         ========================================================= --}}
    <header class="attendance-header">
        <div>
            <span class="attendance-eyebrow">
                <span class="material-symbols-outlined text-[16px]" aria-hidden="true">event_available</span>
                Rekap Kehadiran Bulanan
            </span>

            <h1 class="attendance-title">
                Rekap Absensi
            </h1>

            <p class="attendance-description">
                Monitoring rekap kehadiran karyawan berdasarkan periode, divisi, dan lokasi presensi.
                Saat ini terdapat {{ number_format($recaps->total(), 0, ',', '.') }} rekap pada hasil pencarian.
            </p>
        </div>

        <a
            href="{{ route('attendance.create') }}"
            class="attendance-primary-action"
        >
            <span class="material-symbols-outlined text-[18px]" aria-hidden="true">add_task</span>
            <span>Input Rekap</span>
        </a>
    </header>

    {{-- =========================================================
         FILTER
         ========================================================= --}}
    <section class="attendance-panel filter-panel" aria-labelledby="attendance-filter-title">
        <div class="mb-4">
            <div id="attendance-filter-title" class="section-title">
                Filter Rekap
            </div>
            <div class="section-description">
                Gunakan filter untuk mempersempit data berdasarkan karyawan, divisi, periode, atau lokasi.
            </div>
        </div>

        <form
            data-auto-filter
            method="GET"
            action="{{ route('attendance.index') }}"
        >
            <div class="filter-grid">

                <div class="field field-search">
                    <label for="attendance-search" class="field-label">
                        Karyawan / NIPEG
                    </label>

                    <input
                        id="attendance-search"
                        type="text"
                        name="q"
                        value="{{ request('q') }}"
                        placeholder="Cari nama atau NIPEG..."
                        class="field-control"
                        autocomplete="off"
                    >
                </div>

                <div class="field">
                    <label for="attendance-division" class="field-label">
                        Divisi
                    </label>

                    <select
                        id="attendance-division"
                        name="division_id"
                        class="field-control"
                    >
                        <option value="">Semua Divisi</option>

                        @foreach ($divisions as $division)
                            <option
                                value="{{ $division->id }}"
                                @selected(request('division_id') == $division->id)
                            >
                                {{ $division->nama }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="field">
                    <label for="attendance-month" class="field-label">
                        Bulan
                    </label>

                    <select
                        id="attendance-month"
                        name="bulan"
                        class="field-control"
                    >
                        <option value="">Semua Bulan</option>

                        @foreach (range(1, 12) as $m)
                            <option
                                value="{{ $m }}"
                                @selected(request('bulan') == $m)
                            >
                                {{ $monthNames[$m] }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="field">
                    <label for="attendance-year" class="field-label">
                        Tahun
                    </label>

                    <select
                        id="attendance-year"
                        name="tahun"
                        class="field-control"
                    >
                        <option value="">Semua Tahun</option>

                        @foreach ($tahunTersedia as $tahun)
                            <option
                                value="{{ $tahun }}"
                                @selected(request('tahun') == $tahun)
                            >
                                {{ $tahun }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="field">
                    <label for="attendance-location" class="field-label">
                        Lokasi
                    </label>

                    <select
                        id="attendance-location"
                        name="lokasi"
                        class="field-control"
                    >
                        <option value="">Semua Lokasi</option>
                        <option
                            value="bandung"
                            @selected(request('lokasi') === 'bandung')
                        >
                            Bandung
                        </option>
                        <option
                            value="jakarta"
                            @selected(request('lokasi') === 'jakarta')
                        >
                            Jakarta
                        </option>
                    </select>
                </div>

                <div class="filter-actions">
                    <button
                        type="submit"
                        class="filter-submit"
                    >
                        <span class="material-symbols-outlined text-[18px]" aria-hidden="true">search</span>
                        Tampilkan
                    </button>

                    @if (
                        request('q') ||
                        request('bulan') ||
                        request('tahun') ||
                        request('lokasi') ||
                        request('division_id')
                    )
                        <a
                            href="{{ route('attendance.index') }}"
                            class="filter-reset"
                        >
                            <span class="material-symbols-outlined text-[18px]" aria-hidden="true">restart_alt</span>
                            Atur Ulang
                        </a>
                    @endif
                </div>

            </div>

            <p class="filter-helper">
                Filter berlaku pada tabel rekap dan ringkasan visual di bawah.
            </p>
        </form>
    </section>

    {{-- =========================================================
         KPI
         ========================================================= --}}
    <section class="kpi-grid" aria-label="Ringkasan rekap absensi">

        <article class="kpi-card">
            <div class="kpi-top">
                <span class="kpi-label">Rekap pada Scope Ini</span>

                <span class="kpi-icon">
                    <span class="material-symbols-outlined text-[20px]" aria-hidden="true">
                        fact_check
                    </span>
                </span>
            </div>

            <div class="kpi-value">
                {{ number_format($totalRekapChart, 0, ',', '.') }}
            </div>
        </article>

        <article class="kpi-card">
            <div class="kpi-top">
                <span class="kpi-label">Rata-rata Kehadiran</span>

                <span class="kpi-icon">
                    <span class="material-symbols-outlined text-[20px]" aria-hidden="true">
                        how_to_reg
                    </span>
                </span>
            </div>

            <div class="kpi-value">
                {{ number_format((float) $rataKehadiranChart, 1, ',', '.') }}%
            </div>
        </article>

        <article class="kpi-card">
            <div class="kpi-top">
                <span class="kpi-label">Total Alpha</span>

                <span class="kpi-icon">
                    <span class="material-symbols-outlined text-[20px]" aria-hidden="true">
                        person_off
                    </span>
                </span>
            </div>

            <div class="kpi-value {{ $totalAlphaChart > 0 ? 'error' : '' }}">
                {{ number_format((int) $totalAlphaChart, 0, ',', '.') }}
            </div>
        </article>

        <article class="kpi-card">
            <div class="kpi-top">
                <span class="kpi-label">Total Menit Telat</span>

                <span class="kpi-icon">
                    <span class="material-symbols-outlined text-[20px]" aria-hidden="true">
                        timer
                    </span>
                </span>
            </div>

            <div class="kpi-value">
                {{ number_format((int) $totalMenitTelatChart, 0, ',', '.') }}
            </div>
        </article>

    </section>

    {{-- =========================================================
         TREND CHART
         ========================================================= --}}
    <section class="attendance-panel analytics-panel">
        <div class="section-heading">
            <span class="section-icon">
                <span class="material-symbols-outlined text-[21px]" aria-hidden="true">
                    trending_up
                </span>
            </span>

            <div>
                <h2 class="section-title">
                    Tren Rata-rata Kehadiran Tahun {{ $tahunChart }}
                </h2>

                <p class="section-description">
                    Pergerakan rata-rata persentase kehadiran berdasarkan bulan.
                </p>
            </div>
        </div>

        <div class="chart-container tall">
            <canvas
                id="chart-tren-tahunan"
                aria-label="Grafik tren rata-rata kehadiran"
                role="img"
            ></canvas>
        </div>
    </section>

    {{-- =========================================================
         SECONDARY CHARTS
         ========================================================= --}}
    <section class="chart-grid">

        <article class="attendance-panel analytics-panel">
            <div class="section-heading">
                <span class="section-icon">
                    <span class="material-symbols-outlined text-[21px]" aria-hidden="true">
                        bar_chart
                    </span>
                </span>

                <div>
                    <h2 class="section-title">
                        Rata-rata Kehadiran per Divisi
                    </h2>

                    <p class="section-description">
                        Perbandingan performa kehadiran antar divisi.
                    </p>
                </div>
            </div>

            <div class="chart-container">
                <canvas
                    id="chart-kehadiran-divisi"
                    aria-label="Grafik rata-rata kehadiran per divisi"
                    role="img"
                ></canvas>
            </div>
        </article>

        <article class="attendance-panel analytics-panel">
            <div class="section-heading">
                <span class="section-icon">
                    <span class="material-symbols-outlined text-[21px]" aria-hidden="true">
                        location_city
                    </span>
                </span>

                <div>
                    <h2 class="section-title">
                        Sebaran Lokasi Presensi
                    </h2>

                    <p class="section-description">
                        Distribusi presensi antara lokasi Bandung dan Jakarta.
                    </p>
                </div>
            </div>

            <div class="chart-container">
                <canvas
                    id="chart-lokasi-rekap"
                    aria-label="Grafik sebaran lokasi presensi"
                    role="img"
                ></canvas>

                @if (($totalBandungChart + $totalJakartaChart) <= 0)
                    <div
                        class="absolute inset-0 flex items-center justify-center pointer-events-none"
                    >
                        <div class="text-center">
                            <span class="material-symbols-outlined text-[30px] text-on-surface-variant/60">
                                location_off
                            </span>
                            <p class="mt-1 text-xs text-on-surface-variant">
                                Belum ada data lokasi
                            </p>
                        </div>
                    </div>
                @endif
            </div>
        </article>

    </section>

    {{-- =========================================================
         TABLE
         ========================================================= --}}
    <section class="attendance-panel table-panel">

        <div class="table-header">
            <div>
                <h2 class="table-title">
                    Daftar Rekap Absensi
                </h2>

                <p class="section-description">
                    Data rekap bulanan karyawan pada hasil filter aktif.
                </p>
            </div>

            <div class="table-meta">
                {{ number_format($recaps->total(), 0, ',', '.') }} data
            </div>
        </div>

        <div class="table-scroll">
            <table class="attendance-table text-left">

                <thead>
                    <tr>
                        <th>Periode</th>
                        <th>Karyawan</th>
                        <th>Band</th>
                        <th>Divisi</th>
                        <th>Lokasi</th>
                        <th>Hadir / Hari Kerja</th>
                        <th>Kehadiran</th>
                        <th>Menit Telat</th>
                        <th class="text-right">Aksi</th>
                    </tr>
                </thead>

                <tbody>

                    @forelse ($recaps as $recap)

                        @php
                            $pct = (float) $recap->persen_kehadiran;
                        @endphp

                        <tr>

                            {{-- Periode --}}
                            <td>
                                <span class="metric font-semibold">
                                    {{ $monthNames[$recap->bulan] ?? $recap->bulan }}
                                    {{ $recap->tahun }}
                                </span>
                            </td>

                            {{-- Karyawan --}}
                            <td>
                                <div class="employee-name">
                                    {{ $recap->employee->nama }}
                                </div>

                                <div class="employee-sub">
                                    NIPEG {{ $recap->employee->nipeg }}
                                </div>
                            </td>

                            {{-- Band --}}
                            <td>
                                <span class="badge badge-band">
                                    <span
                                        class="material-symbols-outlined text-[13px]"
                                        aria-hidden="true"
                                    >
                                        badge
                                    </span>

                                    {{ $recap->employee->band_code !== null
                                        ? 'Band ' . $recap->employee->band_code
                                        : '-' }}
                                </span>
                            </td>

                            {{-- Divisi --}}
                            <td>
                                <span class="badge badge-division">
                                    {{ $recap->employee->division->nama }}
                                </span>
                            </td>

                            {{-- Lokasi --}}
                            <td>
                                <div class="location-stack">

                                    @if ($recap->lokasi_bandung > 0)
                                        <span class="location-item">
                                            <span
                                                class="material-symbols-outlined"
                                                aria-hidden="true"
                                            >
                                                corporate_fare
                                            </span>

                                            Bandung
                                            <span class="metric">
                                                ({{ $recap->lokasi_bandung }})
                                            </span>
                                        </span>
                                    @endif

                                    @if ($recap->lokasi_jakarta > 0)
                                        <span class="location-item">
                                            <span
                                                class="material-symbols-outlined"
                                                aria-hidden="true"
                                            >
                                                apartment
                                            </span>

                                            Jakarta
                                            <span class="metric">
                                                ({{ $recap->lokasi_jakarta }})
                                            </span>
                                        </span>
                                    @endif

                                    @if (
                                        $recap->lokasi_bandung == 0 &&
                                        $recap->lokasi_jakarta == 0
                                    )
                                        <span class="text-on-surface-variant">
                                            -
                                        </span>
                                    @endif

                                </div>
                            </td>

                            {{-- Hadir --}}
                            <td>
                                <span class="metric font-semibold">
                                    {{ $recap->hadir }}
                                </span>

                                <span class="text-on-surface-variant">
                                    /
                                </span>

                                <span class="metric">
                                    {{ $recap->hari_kerja }}
                                </span>
                            </td>

                            {{-- Persentase Kehadiran --}}
                            <td>

                                <div class="attendance-rate">

                                    <span
                                        class="metric font-bold
                                        {{ $pct >= 95
                                            ? 'text-secondary'
                                            : ($pct >= 80
                                                ? 'text-primary'
                                                : 'text-error') }}"
                                    >
                                        {{ number_format($pct, 1, ',', '.') }}%
                                    </span>

                                    @if ($pct >= 95)
                                        <span class="badge badge-good">
                                            Sangat Baik
                                        </span>
                                    @elseif ($pct >= 80)
                                        <span class="badge badge-normal">
                                            Normal
                                        </span>
                                    @else
                                        <span class="badge badge-review">
                                            Perlu Ditinjau
                                        </span>
                                    @endif

                                </div>

                            </td>

                            {{-- Telat --}}
                            <td>
                                <span class="metric font-semibold">
                                    {{ number_format((int) $recap->menit_telat, 0, ',', '.') }}
                                </span>
                                <span class="text-on-surface-variant">
                                    menit
                                </span>
                            </td>

                            {{-- Actions --}}
                            <td class="text-right">

                                <div class="actions">

                                    <a
                                        href="{{ route('attendance.edit', $recap) }}"
                                        class="action-button action-edit"
                                        title="Edit rekap"
                                    >
                                        <span
                                            class="material-symbols-outlined text-[17px]"
                                            aria-hidden="true"
                                        >
                                            edit
                                        </span>
                                        Edit
                                    </a>

                                    <form
                                        action="{{ route('attendance.destroy', $recap) }}"
                                        method="POST"
                                        class="inline"
                                        onsubmit="return confirm('Hapus rekap absensi ini?')"
                                    >
                                        @csrf
                                        @method('DELETE')

                                        <button
                                            type="submit"
                                            class="action-button action-delete"
                                            title="Hapus rekap"
                                        >
                                            <span
                                                class="material-symbols-outlined text-[17px]"
                                                aria-hidden="true"
                                            >
                                                delete
                                            </span>
                                            Hapus
                                        </button>
                                    </form>

                                </div>

                            </td>

                        </tr>

                    @empty

                        <tr>
                            <td colspan="9">

                                <div class="empty-state">

                                    <div class="empty-icon">
                                        <span
                                            class="material-symbols-outlined text-[25px]"
                                            aria-hidden="true"
                                        >
                                            event_busy
                                        </span>
                                    </div>

                                    <div class="font-title-sm text-title-sm text-primary">
                                        Belum ada rekap absensi
                                    </div>

                                    <p class="mt-1 text-xs text-on-surface-variant">
                                        Tidak ditemukan data yang sesuai dengan filter yang dipilih.
                                    </p>

                                </div>

                            </td>
                        </tr>

                    @endforelse

                </tbody>
            </table>
        </div>

        @if ($recaps->hasPages())
            <div class="pagination-wrap">
                {{ $recaps->links() }}
            </div>
        @endif

    </section>

    <p class="period-note">
        Ringkasan grafik mengikuti scope tahun, bulan, dan divisi yang sedang dipilih.
        Data tabel tetap mengikuti seluruh filter aktif.
    </p>

</div>
@endsection

@section('scripts')
<script>
    (() => {
        const trenTahunan = @json($trenBulananTahunIni);
        const kehadiranDivisi = @json($kehadiranPerDivisi);

        const lokasiRekap = {
            bandung: {{ (int) $totalBandungChart }},
            jakarta: {{ (int) $totalJakartaChart }},
        };

        const trendCanvas = document.getElementById('chart-tren-tahunan');
        const divisionCanvas = document.getElementById('chart-kehadiran-divisi');
        const locationCanvas = document.getElementById('chart-lokasi-rekap');

        if (!window.Chart) {
            return;
        }

        if (trendCanvas) {
            new Chart(trendCanvas, {
                type: 'line',

                data: {
                    labels: trenTahunan.map(item => item.label),

                    datasets: [{
                        label: 'Rata-rata Kehadiran (%)',
                        data: trenTahunan.map(item =>
                            item.ada_data
                                ? item.rata_kehadiran
                                : null
                        ),
                        borderColor: chartPalet.primary,
                        backgroundColor: 'rgba(15, 41, 66, .08)',
                        fill: true,
                        tension: 0.35,
                        spanGaps: true,
                        pointRadius: 3.5,
                        pointHoverRadius: 5,
                        borderWidth: 2.5,
                    }],
                },

                options: {
                    responsive: true,
                    maintainAspectRatio: false,

                    interaction: {
                        intersect: false,
                        mode: 'index',
                    },

                    plugins: {
                        legend: {
                            display: false,
                        },

                        tooltip: {
                            callbacks: {
                                label(context) {
                                    return `${context.parsed.y}%`;
                                }
                            }
                        }
                    },

                    scales: {
                        y: {
                            min: 0,
                            max: 100,

                            ticks: {
                                callback(value) {
                                    return `${value}%`;
                                }
                            },

                            grid: {
                                color: chartPalet.grid,
                            },

                            title: {
                                display: true,
                                text: 'Kehadiran',
                            },
                        },

                        x: {
                            grid: {
                                display: false,
                            },
                        },
                    },
                },
            });
        }

        if (divisionCanvas) {
            new Chart(divisionCanvas, {
                type: 'bar',

                data: {
                    labels: kehadiranDivisi.map(item => item.nama),

                    datasets: [{
                        label: 'Rata-rata Kehadiran (%)',
                        data: kehadiranDivisi.map(item => item.rata_kehadiran),
                        backgroundColor: chartPalet.secondary,
                        borderRadius: 6,
                        maxBarThickness: 28,
                    }],
                },

                options: {
                    indexAxis: 'y',
                    responsive: true,
                    maintainAspectRatio: false,

                    plugins: {
                        legend: {
                            display: false,
                        },

                        tooltip: {
                            callbacks: {
                                label(context) {
                                    return `${context.parsed.x}%`;
                                }
                            }
                        }
                    },

                    scales: {
                        x: {
                            min: 0,
                            max: 100,

                            ticks: {
                                callback(value) {
                                    return `${value}%`;
                                }
                            },

                            grid: {
                                color: chartPalet.grid,
                            },
                        },

                        y: {
                            grid: {
                                display: false,
                            },
                        },
                    },
                },
            });
        }

        if (
            locationCanvas &&
            (lokasiRekap.bandung + lokasiRekap.jakarta) > 0
        ) {
            new Chart(locationCanvas, {
                type: 'doughnut',

                data: {
                    labels: [
                        'Bandung',
                        'Jakarta',
                    ],

                    datasets: [{
                        data: [
                            lokasiRekap.bandung,
                            lokasiRekap.jakarta,
                        ],

                        backgroundColor: [
                            chartPalet.primary,
                            chartPalet.secondary,
                        ],

                        borderWidth: 2,
                        borderColor: '#ffffff',
                    }],
                },

                options: {
                    cutout: '66%',
                    responsive: true,
                    maintainAspectRatio: false,

                    plugins: {
                        legend: {
                            position: 'bottom',

                            labels: {
                                boxWidth: 10,
                                boxHeight: 10,
                                padding: 14,
                                usePointStyle: true,
                            },
                        },

                        tooltip: {
                            callbacks: {
                                label(context) {
                                    const value = context.raw ?? 0;
                                    return `${context.label}: ${value}`;
                                }
                            }
                        }
                    },
                },
            });
        }
    })();
</script>
@endsection
@extends('layouts.absensi')

@section('title', 'Data Divisi')

@section('content')
<div class="inti-page">

    <div class="max-w-7xl mx-auto space-y-6">

        {{-- HEADER --}}
        <section>
            <div class="flex flex-col lg:flex-row lg:items-start lg:justify-between gap-4">

                <div>
                    <div class="flex flex-wrap items-center gap-2 mb-2">

                        <span class="inline-flex items-center gap-1.5
                                     px-3 py-1 rounded-full
                                     bg-primary-container text-on-primary
                                     font-label-sm text-label-sm
                                     uppercase tracking-wider">
                            <span class="material-symbols-outlined text-[15px]">
                                account_tree
                            </span>
                            Struktur Organisasi
                        </span>

                        <span class="inline-flex items-center gap-1.5
                                     px-3 py-1 rounded-full
                                     bg-surface-container
                                     text-on-surface-variant
                                     font-label-sm text-label-sm">
                            <span class="material-symbols-outlined text-[15px]">
                                admin_panel_settings
                            </span>
                            HR Admin
                        </span>

                    </div>

                    <h1 class="font-headline-lg text-headline-lg
                               text-primary tracking-tight">
                        Data Divisi
                    </h1>

                    <p class="font-body-md text-body-md
                              text-on-surface-variant mt-2">
                        {{ $divisions->count() }} divisi resmi terdaftar
                        dalam struktur organisasi.
                    </p>
                </div>


                <a
                    href="{{ route('divisions.create') }}"
                    class="inline-flex items-center justify-center gap-2
                           min-h-10 px-4 rounded-lg
                           inti-btn inti-btn-primary
                           font-title-sm text-title-sm
                           focus:outline-none
                           focus-visible:ring-2
                           focus-visible:ring-primary
                           shrink-0"
                >
                    <span class="material-symbols-outlined text-[18px]">
                        add_business
                    </span>
                    Tambah Divisi
                </a>

            </div>
        </section>


        {{-- KPI --}}
        <section class="grid grid-cols-1 sm:grid-cols-2 gap-4">

            <div class="rounded-xl border border-outline-variant/70
                        bg-surface-container-lowest p-5">

                <div class="flex items-start justify-between gap-3">

                    <div>
                        <div class="font-label-sm text-label-sm
                                    text-on-surface-variant
                                    uppercase tracking-wider">
                            Total Divisi
                        </div>

                        <div class="font-headline-md text-headline-md
                                    text-primary mt-2 tabular-nums">
                            {{ $divisions->count() }}
                        </div>

                        <p class="font-body-sm text-body-sm
                                  text-on-surface-variant mt-1">
                            Struktur organisasi aktif
                        </p>
                    </div>

                    <div class="flex h-10 w-10 items-center justify-center
                                rounded-lg bg-primary-container
                                text-on-primary">
                        <span class="material-symbols-outlined">
                            account_tree
                        </span>
                    </div>

                </div>

            </div>


            <div class="rounded-xl border border-outline-variant/70
                        bg-surface-container-lowest p-5">

                <div class="flex items-start justify-between gap-3">

                    <div>
                        <div class="font-label-sm text-label-sm
                                    text-on-surface-variant
                                    uppercase tracking-wider">
                            Total Karyawan
                        </div>

                        <div class="font-headline-md text-headline-md
                                    text-primary mt-2 tabular-nums">
                            {{ $divisions->sum('employees_count') }}
                        </div>

                        <p class="font-body-sm text-body-sm
                                  text-on-surface-variant mt-1">
                            Terdistribusi pada seluruh divisi
                        </p>
                    </div>

                    <div class="flex h-10 w-10 items-center justify-center
                                rounded-lg bg-surface-container
                                text-primary">
                        <span class="material-symbols-outlined">
                            groups
                        </span>
                    </div>

                </div>

            </div>

        </section>


        {{-- TABLE --}}
        <section class="rounded-xl border border-outline-variant/70
                        bg-surface-container-lowest
                        shadow-sm overflow-hidden">

            <div class="p-5 sm:p-6
                        border-b border-outline-variant/60">

                <div class="flex flex-col sm:flex-row
                            sm:items-center sm:justify-between gap-3">

                    <div>
                        <h2 class="font-headline-sm text-headline-sm text-primary">
                            Daftar Divisi
                        </h2>

                        <p class="font-body-sm text-body-sm
                                  text-on-surface-variant mt-1">
                            Kelola struktur dan lini organisasi.
                        </p>
                    </div>

                    <span class="inline-flex items-center gap-1.5
                                 rounded-full px-3 py-1.5
                                 bg-surface-container
                                 text-on-surface-variant
                                 font-label-sm text-label-sm shrink-0">
                        <span class="material-symbols-outlined text-[15px]">
                            corporate_fare
                        </span>
                        {{ $divisions->count() }} divisi
                    </span>

                </div>

            </div>


            <div class="overflow-x-auto">

                <table class="w-full min-w-[700px] text-left">

                    <thead>
                        <tr class="bg-surface-container-low
                                   text-on-surface-variant
                                   font-label-sm text-label-sm
                                   uppercase tracking-wider">

                            <th class="px-5 py-3.5">
                                Nama Divisi
                            </th>

                            <th class="px-5 py-3.5">
                                Lini
                            </th>

                            <th class="px-5 py-3.5">
                                Jumlah Karyawan
                            </th>

                            <th class="px-5 py-3.5 text-right">
                                Aksi
                            </th>

                        </tr>
                    </thead>


                    <tbody class="divide-y divide-surface-container-low
                                 font-body-md text-body-md">

                        @forelse ($divisions as $division)

                            <tr class="hover:bg-surface-container-low/60
                                       transition-colors">

                                {{-- NAMA --}}
                                <td class="px-5 py-4 align-top">

                                    <div class="flex items-start gap-3">

                                        <div class="flex h-10 w-10 shrink-0
                                                    items-center justify-center
                                                    rounded-lg
                                                    bg-primary-container
                                                    text-on-primary">

                                            <span class="material-symbols-outlined">
                                                business
                                            </span>

                                        </div>

                                        <div class="min-w-0">

                                            <div class="font-title-sm text-title-sm
                                                        text-primary">
                                                {{ $division->nama }}
                                            </div>

                                            <div class="font-body-sm text-body-sm
                                                        text-on-surface-variant mt-0.5">
                                                Divisi resmi PT. INTI
                                            </div>

                                        </div>

                                    </div>

                                </td>


                                {{-- LINI --}}
                                <td class="px-5 py-4 align-top">

                                    @if ($division->lini)

                                        <span class="inline-flex items-center gap-1.5
                                                     rounded-full px-2.5 py-1
                                                     bg-surface-container
                                                     text-primary
                                                     font-label-sm text-label-sm
                                                     font-semibold">

                                            <span class="material-symbols-outlined text-[14px]">
                                                account_tree
                                            </span>

                                            {{ $division->lini }}

                                        </span>

                                    @else

                                        <span class="text-on-surface-variant">
                                            —
                                        </span>

                                    @endif

                                </td>


                                {{-- HEADCOUNT --}}
                                <td class="px-5 py-4 align-top">

                                    <div class="inline-flex items-center gap-2">

                                        <span class="flex h-8 w-8 items-center
                                                     justify-center rounded-lg
                                                     bg-surface-container
                                                     text-primary">

                                            <span class="material-symbols-outlined text-[17px]">
                                                groups
                                            </span>

                                        </span>

                                        <div>

                                            <div class="font-title-sm text-title-sm
                                                        text-primary tabular-nums">
                                                {{ $division->employees_count }}
                                            </div>

                                            <div class="font-label-sm text-label-sm
                                                        text-on-surface-variant">
                                                karyawan
                                            </div>

                                        </div>

                                    </div>

                                </td>


                                {{-- ACTION --}}
                                <td class="px-5 py-4 align-top">

                                    <div class="flex flex-wrap items-center
                                                justify-end gap-2">

                                        <a
                                            href="{{ route('divisions.edit', $division) }}"
                                            class="inline-flex items-center justify-center
                                                   gap-1.5 min-h-9 px-3 rounded-lg
                                                   bg-surface-container
                                                   text-primary
                                                   font-label-sm text-label-sm
                                                   font-semibold
                                                   hover:bg-surface-container-high
                                                   focus:outline-none
                                                   focus-visible:ring-2
                                                   focus-visible:ring-primary"
                                        >
                                            <span class="material-symbols-outlined text-[16px]">
                                                edit
                                            </span>
                                            Edit
                                        </a>


                                        <form
                                            action="{{ route('divisions.destroy', $division) }}"
                                            method="POST"
                                            class="inline"
                                            data-delete-division
                                        >
                                            @csrf
                                            @method('DELETE')

                                            <button
                                                type="submit"
                                                class="inline-flex items-center justify-center
                                                       gap-1.5 min-h-9 px-3 rounded-lg
                                                       bg-error-container
                                                       text-error
                                                       font-label-sm text-label-sm
                                                       font-semibold
                                                       hover:opacity-90
                                                       focus:outline-none
                                                       focus-visible:ring-2
                                                       focus-visible:ring-error"
                                            >
                                                <span class="material-symbols-outlined text-[16px]">
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

                                <td colspan="4" class="px-5 py-16">

                                    <div class="flex flex-col items-center
                                                justify-center text-center">

                                        <div class="flex h-14 w-14
                                                    items-center justify-center
                                                    rounded-full
                                                    bg-surface-container
                                                    text-on-surface-variant">

                                            <span class="material-symbols-outlined text-[28px]">
                                                account_tree
                                            </span>

                                        </div>

                                        <h3 class="font-title-sm text-title-sm
                                                   text-primary mt-4">
                                            Belum ada divisi
                                        </h3>

                                        <p class="font-body-sm text-body-sm
                                                  text-on-surface-variant
                                                  mt-1 max-w-md">
                                            Belum terdapat struktur divisi
                                            yang terdaftar.
                                        </p>

                                        <a
                                            href="{{ route('divisions.create') }}"
                                            class="inline-flex items-center justify-center
                                                   gap-2 min-h-10 px-4 rounded-lg
                                                   inti-btn inti-btn-primary
                                                   font-title-sm text-title-sm mt-4"
                                        >
                                            <span class="material-symbols-outlined text-[18px]">
                                                add_business
                                            </span>
                                            Tambah Divisi
                                        </a>

                                    </div>

                                </td>

                            </tr>

                        @endforelse

                    </tbody>

                </table>

            </div>


            <div class="px-5 py-3 border-t border-outline-variant/60 lg:hidden">

                <div class="flex items-center gap-2
                            font-body-sm text-body-sm
                            text-on-surface-variant">

                    <span class="material-symbols-outlined text-[16px]">
                        swipe
                    </span>

                    Geser tabel secara horizontal untuk melihat seluruh kolom.

                </div>

            </div>

        </section>


        {{-- CHART --}}
        @if ($divisions->count() > 0)

            <section class="rounded-xl border border-outline-variant/70
                            bg-surface-container-lowest
                            shadow-sm overflow-hidden">

                <div class="p-5 sm:p-6
                            border-b border-outline-variant/60">

                    <div class="flex items-start gap-3">

                        <div class="flex h-10 w-10 items-center justify-center
                                    rounded-lg bg-primary-container
                                    text-on-primary">

                            <span class="material-symbols-outlined">
                                bar_chart
                            </span>

                        </div>

                        <div>
                            <h2 class="font-headline-sm text-headline-sm text-primary">
                                Sebaran Karyawan per Divisi
                            </h2>

                            <p class="font-body-sm text-body-sm
                                      text-on-surface-variant mt-1">
                                Distribusi headcount berdasarkan struktur divisi.
                            </p>
                        </div>

                    </div>

                </div>

                <div class="p-4 sm:p-6">

                    <div class="relative h-[280px] sm:h-[340px] lg:h-[380px]">
                        <canvas id="chart-divisi-headcount"></canvas>
                    </div>

                </div>

            </section>

        @endif

    </div>
</div>
@endsection


@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {

    const divisiHeadcount = @json(
        $divisions->map(fn ($d) => [
            'nama' => $d->nama,
            'jumlah' => $d->employees_count,
        ])
    );

    const canvas = document.getElementById('chart-divisi-headcount');

    if (!canvas || !divisiHeadcount.length || typeof Chart === 'undefined') {
        return;
    }

    const palette = window.chartPalet || {};

    const primary =
        palette.primary ||
        'rgba(37, 99, 235, 0.75)';

    const grid =
        palette.grid ||
        'rgba(148, 163, 184, 0.20)';

    new Chart(canvas, {
        type: 'bar',

        data: {
            labels: divisiHeadcount.map(item => item.nama),

            datasets: [{
                label: 'Jumlah Karyawan',
                data: divisiHeadcount.map(item => item.jumlah),
                backgroundColor: primary,
                borderRadius: 6,
                maxBarThickness: 42,
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
                        label: function (context) {
                            return ` ${context.parsed.y} karyawan`;
                        },
                    },
                },
            },

            scales: {
                x: {
                    grid: {
                        display: false,
                    },

                    ticks: {
                        autoSkip: true,
                        maxRotation: 30,
                        minRotation: 0,
                    },
                },

                y: {
                    beginAtZero: true,

                    ticks: {
                        precision: 0,
                    },

                    grid: {
                        color: grid,
                    },
                },
            },
        },
    });

});
</script>
@endsection
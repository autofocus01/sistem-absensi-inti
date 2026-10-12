@extends('layouts.absensi')


@section('title', 'Review Anomali Import')

@section('content')
<div class="inti-page p1-hr-governance">

    {{-- =========================================================
         HEADER
         ========================================================= --}}
    <section class="mb-6">
        <div class="flex flex-col gap-3">

            <div class="flex flex-wrap items-center gap-2">
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full
                             bg-error text-white font-label-sm text-label-sm
                             uppercase tracking-wider">
                    <span class="material-symbols-outlined text-[16px]">
                        fact_check
                    </span>
                    Kendali Mutu Data
                </span>

                @if($totalPending > 0)
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full
                                 bg-error-container text-error
                                 font-label-sm text-label-sm">
                        <span class="material-symbols-outlined text-[15px]">
                            warning
                        </span>
                        {{ $totalPending }} perlu ditinjau
                    </span>
                @else
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full
                                 bg-secondary-container/60 text-secondary
                                 font-label-sm text-label-sm">
                        <span class="material-symbols-outlined text-[15px]">
                            check_circle
                        </span>
                        Tidak ada pending
                    </span>
                @endif
            </div>

            <div>
                <h1 class="font-headline-lg text-headline-lg text-primary tracking-tight">
                    Review Anomali Import
                </h1>

                <p class="font-body-md text-body-md text-on-surface-variant
                          max-w-3xl mt-2">
                    Tinjau baris import yang ditolak sistem karena tidak memenuhi
                    aturan validasi data. Untuk rekap bulanan, HR dapat membuat
                    rekap koreksi. Untuk absensi harian, perbaikan dilakukan pada
                    file sumber kemudian import dijalankan kembali.
                </p>
            </div>

        </div>
    </section>


    {{-- =========================================================
         INFORMATION BANNER
         ========================================================= --}}
    <section class="mb-6">
        <div class="rounded-xl border border-outline-variant
                    bg-surface-container-low p-4 sm:p-5">

            <div class="flex items-start gap-3">

                <div class="flex h-10 w-10 shrink-0 items-center justify-center
                            rounded-lg bg-primary-container text-white">
                    <span class="material-symbols-outlined text-[21px]">
                        rule
                    </span>
                </div>

                <div class="min-w-0">
                    <h2 class="font-title-sm text-title-sm text-primary">
                        Alur penanganan anomali
                    </h2>

                    <p class="font-body-sm text-body-sm text-on-surface-variant mt-1">
                        Data yang berstatus pending belum dianggap selesai.
                        Gunakan aksi yang tersedia sesuai jenis anomali sebelum
                        menandainya sebagai selesai.
                    </p>
                </div>

            </div>
        </div>
    </section>


    {{-- =========================================================
         KPI
         ========================================================= --}}
    <section class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-6">

        {{-- Pending --}}
        <div class="rounded-xl bg-surface-container-lowest
                    border border-outline-variant/70 shadow-sm p-5">

            <div class="flex items-start justify-between gap-4">

                <div class="min-w-0">
                    <div class="font-label-sm text-label-sm text-on-surface-variant
                                uppercase tracking-wider">
                        Anomali Pending
                    </div>

                    <div class="mt-2 font-headline-md text-headline-md
                                {{ $totalPending > 0 ? 'text-error' : 'text-primary' }}">
                        {{ $totalPending }}
                    </div>

                    <p class="mt-1 font-body-sm text-body-sm text-on-surface-variant">
                        Menunggu pemeriksaan HR
                    </p>
                </div>

                <div class="flex h-11 w-11 shrink-0 items-center justify-center
                            rounded-xl
                            {{ $totalPending > 0
                                ? 'bg-error-container text-error'
                                : 'bg-surface-container text-primary' }}">
                    <span class="material-symbols-outlined text-[22px]">
                        pending_actions
                    </span>
                </div>

            </div>
        </div>


        {{-- Selesai --}}
        <div class="rounded-xl bg-surface-container-lowest
                    border border-outline-variant/70 shadow-sm p-5">

            <div class="flex items-start justify-between gap-4">

                <div class="min-w-0">
                    <div class="font-label-sm text-label-sm text-on-surface-variant
                                uppercase tracking-wider">
                        Sudah Selesai
                    </div>

                    <div class="mt-2 font-headline-md text-headline-md text-secondary">
                        {{ $totalSelesai }}
                    </div>

                    <p class="mt-1 font-body-sm text-body-sm text-on-surface-variant">
                        Anomali yang sudah ditangani
                    </p>
                </div>

                <div class="flex h-11 w-11 shrink-0 items-center justify-center
                            rounded-xl bg-secondary-container/60 text-secondary">
                    <span class="material-symbols-outlined text-[22px]">
                        task_alt
                    </span>
                </div>

            </div>
        </div>

    </section>


    {{-- =========================================================
         CHARTS
         ========================================================= --}}
    @if (($totalPending + $totalSelesai) > 0)

        <section class="grid grid-cols-1 xl:grid-cols-2 gap-5 mb-6">

            {{-- Status --}}
            <div class="rounded-xl bg-surface-container-lowest
                        border border-outline-variant/70 shadow-sm overflow-hidden">

                <div class="p-5 border-b border-outline-variant/60">
                    <div class="flex items-start gap-3">

                        <div class="flex h-10 w-10 shrink-0 items-center justify-center
                                    rounded-lg bg-primary-container text-white">
                            <span class="material-symbols-outlined text-[21px]">
                                donut_large
                            </span>
                        </div>

                        <div>
                            <h2 class="font-headline-md text-headline-md text-primary">
                                Status Anomali
                            </h2>

                            <p class="font-body-sm text-body-sm
                                      text-on-surface-variant mt-1">
                                Perbandingan data yang masih pending dan sudah selesai.
                            </p>
                        </div>

                    </div>
                </div>

                <div class="p-4 sm:p-5">
                    <div class="relative h-64 sm:h-72">
                        <canvas id="chart-status-anomali"></canvas>
                    </div>
                </div>

            </div>


            {{-- Jenis --}}
            <div class="rounded-xl bg-surface-container-lowest
                        border border-outline-variant/70 shadow-sm overflow-hidden">

                <div class="p-5 border-b border-outline-variant/60">
                    <div class="flex items-start gap-3">

                        <div class="flex h-10 w-10 shrink-0 items-center justify-center
                                    rounded-lg bg-primary-container text-white">
                            <span class="material-symbols-outlined text-[21px]">
                                bar_chart
                            </span>
                        </div>

                        <div>
                            <h2 class="font-headline-md text-headline-md text-primary">
                                Sebaran Jenis Anomali
                            </h2>

                            <p class="font-body-sm text-body-sm
                                      text-on-surface-variant mt-1">
                                Jumlah baris berdasarkan jenis data yang bermasalah.
                            </p>
                        </div>

                    </div>
                </div>

                <div class="p-4 sm:p-5">
                    <div class="relative h-64 sm:h-72">
                        <canvas id="chart-jenis-anomali"></canvas>
                    </div>
                </div>

            </div>

        </section>

    @endif


    {{-- =========================================================
         TABLE
         ========================================================= --}}
    <section class="rounded-xl bg-surface-container-lowest
                    border border-outline-variant/70 shadow-sm overflow-hidden">

        {{-- Table header --}}
        <div class="p-5 border-b border-outline-variant/60">

            <div class="flex flex-col gap-3 sm:flex-row
                        sm:items-center sm:justify-between">

                <div>
                    <h2 class="font-headline-md text-headline-md text-primary">
                        Daftar Anomali
                    </h2>

                    <p class="font-body-sm text-body-sm text-on-surface-variant mt-1">
                        Periksa setiap baris sebelum data dinyatakan selesai.
                    </p>
                </div>

                <div class="flex flex-wrap items-center gap-2">
                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5
                                 rounded-lg bg-error-container text-error
                                 font-label-sm text-label-sm">
                        <span class="material-symbols-outlined text-[15px]">
                            pending_actions
                        </span>
                        {{ $totalPending }} Pending
                    </span>

                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5
                                 rounded-lg bg-secondary-container/60 text-secondary
                                 font-label-sm text-label-sm">
                        <span class="material-symbols-outlined text-[15px]">
                            task_alt
                        </span>
                        {{ $totalSelesai }} Selesai
                    </span>
                </div>

            </div>
        </div>


        {{-- Responsive table --}}
        <div class="overflow-x-auto">

            <table class="w-full min-w-[1120px] text-left">

                <thead>
                    <tr class="bg-surface-container-low
                               text-on-surface-variant
                               font-label-sm text-label-sm
                               uppercase tracking-wider">

                        <th class="px-5 py-3.5">
                            Baris
                        </th>

                        <th class="px-5 py-3.5">
                            Jenis
                        </th>

                        <th class="px-5 py-3.5">
                            NIPEG
                        </th>

                        <th class="px-5 py-3.5">
                            Nama
                        </th>

                        <th class="px-5 py-3.5">
                            Periode
                        </th>

                        <th class="px-5 py-3.5 min-w-[280px]">
                            Alasan Ditolak
                        </th>

                        <th class="px-5 py-3.5">
                            Status
                        </th>

                        <th class="px-5 py-3.5 text-right">
                            Aksi
                        </th>

                    </tr>
                </thead>


                <tbody class="divide-y divide-surface-container-low
                             font-body-md text-body-md">

                    @forelse ($importErrors as $error)

                        <tr class="transition-colors hover:bg-surface-container-low/50">

                            {{-- Baris --}}
                            <td class="px-5 py-4 align-top">
                                <span class="font-title-sm text-title-sm
                                             text-primary tabular-nums">
                                    {{ $error->baris_excel ?? '-' }}
                                </span>
                            </td>


                            {{-- Jenis --}}
                            <td class="px-5 py-4 align-top">

                                @if ($error->jenis === 'absensi_harian')

                                    <span class="inline-flex items-center gap-1.5
                                                 rounded-lg px-2.5 py-1
                                                 bg-surface-container-high
                                                 text-on-surface-variant
                                                 font-label-sm text-label-sm
                                                 whitespace-nowrap">
                                        <span class="material-symbols-outlined text-[15px]">
                                            event
                                        </span>
                                        Absensi Harian
                                    </span>

                                @else

                                    <span class="inline-flex items-center gap-1.5
                                                 rounded-lg px-2.5 py-1
                                                 bg-surface-container
                                                 text-primary
                                                 font-label-sm text-label-sm
                                                 whitespace-nowrap">
                                        <span class="material-symbols-outlined text-[15px]">
                                            calendar_month
                                        </span>
                                        Rekap Bulanan
                                    </span>

                                @endif

                            </td>


                            {{-- NIPEG --}}
                            <td class="px-5 py-4 align-top">
                                <span class="tabular-nums text-on-surface">
                                    {{ $error->nipeg }}
                                </span>
                            </td>


                            {{-- Nama --}}
                            <td class="px-5 py-4 align-top">
                                <div class="font-title-sm text-title-sm text-primary">
                                    {{ $error->nama ?? '-' }}
                                </div>
                            </td>


                            {{-- Periode --}}
                            <td class="px-5 py-4 align-top whitespace-nowrap">

                                <div class="font-body-md text-body-md text-on-surface">
                                    {{ \Carbon\Carbon::create()->month($error->bulan)->translatedFormat('F') }}
                                </div>

                                <div class="font-body-sm text-body-sm
                                            text-on-surface-variant">
                                    {{ $error->tahun }}
                                </div>

                            </td>


                            {{-- Alasan --}}
                            <td class="px-5 py-4 align-top">

                                <div class="max-w-[360px]">

                                    <div class="flex items-start gap-2">
                                        <span class="material-symbols-outlined
                                                     text-error text-[18px] shrink-0 mt-0.5">
                                            error
                                        </span>

                                        <span class="font-body-sm text-body-sm
                                                     text-error leading-5">
                                            {{ $error->alasan }}
                                        </span>
                                    </div>

                                </div>

                            </td>


                            {{-- Status --}}
                            <td class="px-5 py-4 align-top">

                                @if ($error->status === 'selesai')

                                    <span class="inline-flex items-center gap-1.5
                                                 rounded-full px-2.5 py-1
                                                 bg-secondary-container/60
                                                 text-secondary
                                                 font-label-sm text-label-sm
                                                 font-semibold whitespace-nowrap">
                                        <span class="material-symbols-outlined text-[15px]">
                                            check_circle
                                        </span>
                                        Selesai
                                    </span>

                                @else

                                    <span class="inline-flex items-center gap-1.5
                                                 rounded-full px-2.5 py-1
                                                 bg-error-container
                                                 text-error
                                                 font-label-sm text-label-sm
                                                 font-semibold whitespace-nowrap">
                                        <span class="material-symbols-outlined text-[15px]">
                                            pending
                                        </span>
                                        Pending
                                    </span>

                                @endif

                            </td>


                            {{-- Aksi --}}
                            <td class="px-5 py-4 align-top text-right">

                                @if ($error->status !== 'selesai')

                                    <div class="flex flex-col items-end gap-2">

                                        @if ($error->jenis === 'rekap_bulanan')

                                            <a href="{{ route('attendance.create', ['from_error' => $error->id]) }}"
                                               class="inline-flex items-center justify-center gap-1.5
                                                      min-h-9 px-3 rounded-lg
                                                      bg-primary-container text-white
                                                      font-title-sm text-title-sm
                                                      transition-opacity hover:opacity-90
                                                      focus:outline-none focus-visible:ring-2
                                                      focus-visible:ring-primary">

                                                <span class="material-symbols-outlined text-[17px]">
                                                    edit_note
                                                </span>

                                                Buat Rekap
                                            </a>

                                        @else

                                            <div class="max-w-[250px] text-right">
                                                <div class="inline-flex items-start gap-1.5
                                                            font-body-sm text-body-sm
                                                            text-on-surface-variant">

                                                    <span class="material-symbols-outlined
                                                                 text-[16px] shrink-0 mt-0.5">
                                                        upload_file
                                                    </span>

                                                    <span>
                                                        Perbaiki di file sumber,
                                                        lalu import ulang.
                                                    </span>

                                                </div>
                                            </div>

                                        @endif


                                        <form action="{{ route('attendance.import-errors.resolve', $error) }}"
                                              method="POST"
                                              class="inline">

                                            @csrf

                                            <button type="submit"
                                                    class="inline-flex items-center justify-center gap-1.5
                                                           min-h-9 px-3 rounded-lg
                                                           text-on-surface-variant
                                                           font-title-sm text-title-sm
                                                           border border-outline-variant
                                                           bg-surface-container-lowest
                                                           transition-colors
                                                           hover:bg-surface-container-low
                                                           focus:outline-none
                                                           focus-visible:ring-2
                                                           focus-visible:ring-primary"
                                                    onclick="return confirm('Tandai baris ini selesai tanpa membuat rekap?')">

                                                <span class="material-symbols-outlined text-[17px]">
                                                    done
                                                </span>

                                                Tandai Selesai
                                            </button>

                                        </form>

                                    </div>

                                @else

                                    <span class="inline-flex items-center gap-1.5
                                                 text-secondary
                                                 font-body-sm text-body-sm">
                                        <span class="material-symbols-outlined text-[16px]">
                                            verified
                                        </span>
                                        Sudah ditangani
                                    </span>

                                @endif

                            </td>

                        </tr>

                    @empty

                        <tr>
                            <td colspan="8" class="px-5 py-14">

                                <div class="flex flex-col items-center justify-center
                                            text-center max-w-md mx-auto">

                                    <div class="flex h-14 w-14 items-center justify-center
                                                rounded-full
                                                bg-secondary-container/60
                                                text-secondary">

                                        <span class="material-symbols-outlined text-[28px]">
                                            verified
                                        </span>

                                    </div>

                                    <h3 class="mt-4 font-headline-sm text-headline-sm text-primary">
                                        Tidak ada anomali import
                                    </h3>

                                    <p class="mt-2 font-body-sm text-body-sm
                                              text-on-surface-variant">
                                        Semua data import saat ini bersih dan tidak
                                        memerlukan tindakan koreksi.
                                    </p>

                                </div>

                            </td>
                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>


        {{-- Pagination --}}
        @if(method_exists($importErrors, 'hasPages') && $importErrors->hasPages())

            <div class="px-5 py-4 border-t border-outline-variant/60
                        overflow-x-auto">

                {{ $importErrors->links() }}

            </div>

        @endif

    </section>


    {{-- =========================================================
         MOBILE/TABLET SCROLL NOTE
         ========================================================= --}}
    @if($importErrors->count() > 0)
        <div class="mt-3 flex items-center gap-2
                    text-on-surface-variant font-body-sm text-body-sm
                    lg:hidden">

            <span class="material-symbols-outlined text-[16px]">
                swipe
            </span>

            Geser tabel ke samping untuk melihat seluruh kolom dan aksi.
        </div>
    @endif

</div>
@endsection


@section('scripts')
<script>
    const sebaranJenisAnomali = @json($sebaranJenis);

    const statusAnomali = {
        pending: {{ $totalPending }},
        selesai: {{ $totalSelesai }}
    };

    document.addEventListener('DOMContentLoaded', function () {

        if (!window.Chart) {
            return;
        }

        const statusCanvas = document.getElementById('chart-status-anomali');
        const jenisCanvas = document.getElementById('chart-jenis-anomali');

        /*
         * Chart status
         */
        if (
            statusCanvas &&
            (statusAnomali.pending + statusAnomali.selesai > 0)
        ) {

            new Chart(statusCanvas, {
                type: 'doughnut',

                data: {
                    labels: ['Pending', 'Selesai'],

                    datasets: [{
                        data: [
                            statusAnomali.pending,
                            statusAnomali.selesai
                        ],

                        backgroundColor: [
                            chartPalet.error,
                            chartPalet.secondary
                        ],

                        borderWidth: 2,
                        borderColor: '#ffffff',

                        hoverOffset: 4
                    }]
                },

                options: {
                    responsive: true,
                    maintainAspectRatio: false,

                    cutout: '68%',

                    animation: {
                        duration: window.matchMedia &&
                                   window.matchMedia('(prefers-reduced-motion: reduce)').matches
                            ? 0
                            : 350
                    },

                    plugins: {
                        legend: {
                            position: 'bottom',

                            labels: {
                                boxWidth: 10,
                                boxHeight: 10,
                                padding: 16,
                                usePointStyle: true
                            }
                        },

                        tooltip: {
                            callbacks: {
                                label: function (context) {
                                    const value = context.raw ?? 0;

                                    return `${context.label}: ${value} baris`;
                                }
                            }
                        }
                    }
                }
            });
        }


        /*
         * Chart jenis anomali
         */
        if (
            jenisCanvas &&
            Object.keys(sebaranJenisAnomali || {}).length > 0
        ) {

            const labels = Object.keys(sebaranJenisAnomali);
            const values = Object.values(sebaranJenisAnomali);

            new Chart(jenisCanvas, {
                type: 'bar',

                data: {
                    labels: labels,

                    datasets: [{
                        label: 'Jumlah Baris',
                        data: values,

                        backgroundColor: labels.map((_, index) => {
                            return index % 2 === 0
                                ? chartPalet.outline
                                : chartPalet.primary;
                        }),

                        borderRadius: 6,
                        maxBarThickness: 48
                    }]
                },

                options: {
                    responsive: true,
                    maintainAspectRatio: false,

                    animation: {
                        duration: window.matchMedia &&
                                   window.matchMedia('(prefers-reduced-motion: reduce)').matches
                            ? 0
                            : 350
                    },

                    plugins: {
                        legend: {
                            display: false
                        },

                        tooltip: {
                            callbacks: {
                                label: function (context) {
                                    return `${context.raw ?? 0} baris`;
                                }
                            }
                        }
                    },

                    scales: {
                        x: {
                            grid: {
                                display: false
                            },

                            ticks: {
                                autoSkip: false,
                                maxRotation: 35,
                                minRotation: 0
                            }
                        },

                        y: {
                            beginAtZero: true,

                            ticks: {
                                precision: 0
                            },

                            grid: {
                                color: chartPalet.grid
                            }
                        }
                    }
                }
            });
        }

    });
</script>
@endsection
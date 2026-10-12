@extends('layouts.absensi')


@section('title', 'Review Anomali')

@section('content')
<div class="inti-page p1-hr-governance">

    <div class="space-y-6">

        {{-- HEADER --}}
        <section>
            <div class="flex flex-col lg:flex-row lg:items-start lg:justify-between gap-4">

                <div>
                    <div class="flex flex-wrap items-center gap-2 mb-2">

                        <span class="inline-flex items-center gap-1.5
                                     px-3 py-1 rounded-full
                                     bg-error-container text-error
                                     font-label-sm text-label-sm
                                     uppercase tracking-wider">

                            <span class="material-symbols-outlined text-[15px]">
                                fact_check
                            </span>

                            Governance HR
                        </span>

                        <span class="inline-flex items-center gap-1.5
                                     px-3 py-1 rounded-full
                                     bg-surface-container
                                     text-on-surface-variant
                                     font-label-sm text-label-sm">

                            <span class="material-symbols-outlined text-[15px]">
                                shield
                            </span>

                            Review Anomali
                        </span>

                    </div>

                    <h1 class="font-headline-lg text-headline-lg
                               text-primary tracking-tight">
                        Review Anomali
                    </h1>

                    <p class="font-body-md text-body-md
                              text-on-surface-variant mt-2 max-w-3xl">
                        Temuan presensi dan lembur yang perlu ditinjau
                        dan ditindaklanjuti oleh HR.
                    </p>
                </div>


                <a
                    href="{{ route('attendance.anomalies', ['scan' => 1]) }}"
                    class="inline-flex items-center justify-center gap-2
                           min-h-11 px-5 rounded-lg
                           inti-btn inti-btn-primary
                           font-title-sm text-title-sm
                           focus:outline-none
                           focus-visible:ring-2
                           focus-visible:ring-primary
                           shrink-0"
                    data-anomaly-scan
                >
                    <span class="material-symbols-outlined text-[18px]">
                        sync
                    </span>

                    <span data-scan-label>
                        Scan Sekarang
                    </span>
                </a>

            </div>
        </section>


        {{-- STATUS --}}
        @if (session('status'))

            <div
                class="rounded-xl border border-secondary/20
                       bg-secondary/10 p-4"
                role="status"
            >
                <div class="flex items-start gap-3">

                    <span class="material-symbols-outlined text-secondary">
                        check_circle
                    </span>

                    <div class="font-body-sm text-body-sm text-secondary">
                        {{ session('status') }}
                    </div>

                </div>
            </div>

        @endif


        {{-- FILTER --}}
        <section class="rounded-xl border border-outline-variant/70
                        bg-surface-container-lowest
                        shadow-sm overflow-hidden">

            <div class="p-5 sm:p-6
                        border-b border-outline-variant/60">

                <div class="flex items-start gap-3">

                    <div class="flex h-10 w-10 shrink-0
                                items-center justify-center
                                rounded-lg
                                bg-surface-container
                                text-primary">

                        <span class="material-symbols-outlined">
                            filter_alt
                        </span>

                    </div>

                    <div>
                        <h2 class="font-headline-sm text-headline-sm text-primary">
                            Filter Anomali
                        </h2>

                        <p class="font-body-sm text-body-sm
                                  text-on-surface-variant mt-1">
                            Saring temuan berdasarkan status,
                            tingkat keparahan, dan jenis anomali.
                        </p>
                    </div>

                </div>

            </div>


            <form
                method="GET"
                action="{{ route('attendance.anomalies') }}"
                class="p-5 sm:p-6"
            >

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">

                    {{-- STATUS --}}
                    <div>

                        <label
                            for="anomaly-status"
                            class="block font-label-sm text-label-sm
                                   text-on-surface-variant mb-2"
                        >
                            Status
                        </label>

                        <select
                            id="anomaly-status"
                            name="status"
                            class="w-full min-h-11 rounded-lg
                                   border border-outline-variant
                                   bg-surface-container-low
                                   text-on-surface
                                   px-3
                                   font-body-md text-body-md
                                   focus:outline-none
                                   focus-visible:ring-2
                                   focus-visible:ring-primary"
                        >
                            <option value="">Semua Status</option>

                            @foreach ([
                                'OPEN' => 'Open',
                                'REVIEWED' => 'Reviewed',
                                'RESOLVED' => 'Resolved',
                                'IGNORED' => 'Ignored',
                            ] as $value => $label)

                                <option
                                    value="{{ $value }}"
                                    @selected(($filters['status'] ?? '') === $value)
                                >
                                    {{ $label }}
                                </option>

                            @endforeach

                        </select>

                    </div>


                    {{-- SEVERITY --}}
                    <div>

                        <label
                            for="anomaly-severity"
                            class="block font-label-sm text-label-sm
                                   text-on-surface-variant mb-2"
                        >
                            Severity
                        </label>

                        <select
                            id="anomaly-severity"
                            name="severity"
                            class="w-full min-h-11 rounded-lg
                                   border border-outline-variant
                                   bg-surface-container-low
                                   text-on-surface
                                   px-3
                                   font-body-md text-body-md
                                   focus:outline-none
                                   focus-visible:ring-2
                                   focus-visible:ring-primary"
                        >
                            <option value="">Semua Severity</option>

                            @foreach ([
                                'LOW' => 'Low',
                                'MEDIUM' => 'Medium',
                                'HIGH' => 'High',
                                'CRITICAL' => 'Critical',
                            ] as $value => $label)

                                <option
                                    value="{{ $value }}"
                                    @selected(($filters['severity'] ?? '') === $value)
                                >
                                    {{ $label }}
                                </option>

                            @endforeach

                        </select>

                    </div>


                    {{-- TYPE --}}
                    <div>

                        <label
                            for="anomaly-type"
                            class="block font-label-sm text-label-sm
                                   text-on-surface-variant mb-2"
                        >
                            Jenis Anomali
                        </label>

                        <input
                            id="anomaly-type"
                            type="text"
                            name="type"
                            value="{{ $filters['type'] ?? '' }}"
                            placeholder="Contoh: MISSING_CLOCK_OUT"
                            class="w-full min-h-11 rounded-lg
                                   border border-outline-variant
                                   bg-surface-container-low
                                   text-on-surface
                                   px-3
                                   font-body-md text-body-md
                                   focus:outline-none
                                   focus-visible:ring-2
                                   focus-visible:ring-primary"
                        >

                    </div>


                    {{-- DATE RANGE --}}
                    <div class="grid grid-cols-2 gap-2">

                        <div>
                            <label
                                for="anomaly-from"
                                class="block font-label-sm text-label-sm
                                       text-on-surface-variant mb-2"
                            >
                                Dari
                            </label>

                            <input
                                id="anomaly-from"
                                type="date"
                                name="from"
                                value="{{ $filters['from'] ?? '' }}"
                                class="w-full min-h-11 rounded-lg
                                       border border-outline-variant
                                       bg-surface-container-low
                                       text-on-surface
                                       px-2
                                       font-body-sm text-body-sm
                                       focus:outline-none
                                       focus-visible:ring-2
                                       focus-visible:ring-primary"
                            >
                        </div>


                        <div>
                            <label
                                for="anomaly-until"
                                class="block font-label-sm text-label-sm
                                       text-on-surface-variant mb-2"
                            >
                                Sampai
                            </label>

                            <input
                                id="anomaly-until"
                                type="date"
                                name="until"
                                value="{{ $filters['until'] ?? '' }}"
                                class="w-full min-h-11 rounded-lg
                                       border border-outline-variant
                                       bg-surface-container-low
                                       text-on-surface
                                       px-2
                                       font-body-sm text-body-sm
                                       focus:outline-none
                                       focus-visible:ring-2
                                       focus-visible:ring-primary"
                            >
                        </div>

                    </div>

                </div>


                <div class="flex flex-col sm:flex-row
                            sm:items-center sm:justify-end
                            gap-2 mt-5">

                    <a
                        href="{{ route('attendance.anomalies') }}"
                        class="inline-flex items-center justify-center gap-2
                               min-h-10 px-4 rounded-lg
                               bg-surface-container
                               text-on-surface
                               font-title-sm text-title-sm
                               hover:bg-surface-container-high
                               focus:outline-none
                               focus-visible:ring-2
                               focus-visible:ring-primary"
                    >
                        <span class="material-symbols-outlined text-[17px]">
                            restart_alt
                        </span>
                        Reset
                    </a>

                    <button
                        type="submit"
                        class="inline-flex items-center justify-center gap-2
                               min-h-10 px-4 rounded-lg
                               inti-btn inti-btn-primary
                               font-title-sm text-title-sm
                               focus:outline-none
                               focus-visible:ring-2
                               focus-visible:ring-primary"
                    >
                        <span class="material-symbols-outlined text-[17px]">
                            search
                        </span>
                        Terapkan Filter
                    </button>

                </div>

            </form>

        </section>


        {{-- ANOMALY TABLE --}}
        <section class="rounded-xl border border-outline-variant/70
                        bg-surface-container-lowest
                        shadow-sm overflow-hidden">

            <div class="p-5 sm:p-6
                        border-b border-outline-variant/60">

                <div class="flex flex-col sm:flex-row
                            sm:items-center sm:justify-between gap-3">

                    <div>

                        <h2 class="font-headline-sm text-headline-sm text-primary">
                            Daftar Temuan
                        </h2>

                        <p class="font-body-sm text-body-sm
                                  text-on-surface-variant mt-1">
                            Tinjau setiap anomali sebelum ditandai selesai.
                        </p>

                    </div>

                    <div class="inline-flex items-center gap-2
                                rounded-full px-3 py-1.5
                                bg-surface-container
                                text-on-surface-variant
                                font-label-sm text-label-sm
                                shrink-0">

                        <span class="material-symbols-outlined text-[16px]">
                            rule
                        </span>

                        Maks. 25 data per halaman

                    </div>

                </div>

            </div>


            <div class="overflow-x-auto">

                <table class="w-full min-w-[1050px] text-left">

                    <thead>
                        <tr class="bg-surface-container-low
                                   text-on-surface-variant
                                   font-label-sm text-label-sm
                                   uppercase tracking-wider">

                            <th class="px-5 py-3.5">
                                Tanggal
                            </th>

                            <th class="px-5 py-3.5">
                                Karyawan
                            </th>

                            <th class="px-5 py-3.5">
                                Anomali
                            </th>

                            <th class="px-5 py-3.5">
                                Severity
                            </th>

                            <th class="px-5 py-3.5">
                                Status
                            </th>

                            <th class="px-5 py-3.5 min-w-[320px]">
                                Tindakan
                            </th>

                        </tr>
                    </thead>


                    <tbody class="divide-y divide-surface-container-low
                                 font-body-md text-body-md">

                        @forelse ($anomalies as $item)

                            @php
                                $severity = strtoupper((string) $item->severity);
                                $status = strtoupper((string) $item->status);
                            @endphp

                            <tr class="hover:bg-surface-container-low/60
                                       transition-colors">

                                {{-- DATE --}}
                                <td class="px-5 py-4 align-top whitespace-nowrap">

                                    <div class="flex items-center gap-2">

                                        <span class="flex h-8 w-8 items-center
                                                     justify-center rounded-lg
                                                     bg-surface-container
                                                     text-primary">

                                            <span class="material-symbols-outlined text-[17px]">
                                                event
                                            </span>

                                        </span>

                                        <span class="font-title-sm text-title-sm
                                                     text-primary">
                                            {{ optional($item->tanggal)->format('d/m/Y') ?? '-' }}
                                        </span>

                                    </div>

                                </td>


                                {{-- EMPLOYEE --}}
                                <td class="px-5 py-4 align-top">

                                    <div class="font-title-sm text-title-sm text-primary">
                                        {{ $item->employee?->nama ?? '-' }}
                                    </div>

                                    @if ($item->employee?->nipeg)
                                        <div class="font-body-sm text-body-sm
                                                    text-on-surface-variant mt-0.5">
                                            NIPEG {{ $item->employee->nipeg }}
                                        </div>
                                    @endif

                                </td>


                                {{-- TYPE --}}
                                <td class="px-5 py-4 align-top">

                                    <div class="font-title-sm text-title-sm
                                                text-primary break-words max-w-[280px]">
                                        {{ $item->type }}
                                    </div>

                                </td>


                                {{-- SEVERITY --}}
                                <td class="px-5 py-4 align-top">

                                    @if ($severity === 'CRITICAL')

                                        <span class="inline-flex items-center gap-1.5
                                                     rounded-full px-2.5 py-1
                                                     bg-error-container text-error
                                                     font-label-sm text-label-sm
                                                     font-semibold">
                                            <span class="material-symbols-outlined text-[14px]">
                                                priority_high
                                            </span>
                                            Critical
                                        </span>

                                    @elseif ($severity === 'HIGH')

                                        <span class="inline-flex items-center gap-1.5
                                                     rounded-full px-2.5 py-1
                                                     bg-error/10 text-error
                                                     font-label-sm text-label-sm
                                                     font-semibold">
                                            <span class="material-symbols-outlined text-[14px]">
                                                warning
                                            </span>
                                            High
                                        </span>

                                    @elseif ($severity === 'MEDIUM')

                                        <span class="inline-flex items-center gap-1.5
                                                     rounded-full px-2.5 py-1
                                                     bg-amber-50 text-amber-900
                                                     font-label-sm text-label-sm
                                                     font-semibold">
                                            <span class="material-symbols-outlined text-[14px]">
                                                warning
                                            </span>
                                            Medium
                                        </span>

                                    @else

                                        <span class="inline-flex items-center gap-1.5
                                                     rounded-full px-2.5 py-1
                                                     bg-surface-container
                                                     text-on-surface-variant
                                                     font-label-sm text-label-sm
                                                     font-semibold">
                                            <span class="material-symbols-outlined text-[14px]">
                                                info
                                            </span>
                                            {{ $item->severity ?: 'Low' }}
                                        </span>

                                    @endif

                                </td>


                                {{-- STATUS --}}
                                <td class="px-5 py-4 align-top">

                                    @if ($status === 'OPEN')

                                        <span class="inline-flex items-center gap-1.5
                                                     rounded-full px-2.5 py-1
                                                     bg-error/10 text-error
                                                     font-label-sm text-label-sm
                                                     font-semibold">
                                            <span class="material-symbols-outlined text-[14px]">
                                                radio_button_checked
                                            </span>
                                            Open
                                        </span>

                                    @elseif ($status === 'REVIEWED')

                                        <span class="inline-flex items-center gap-1.5
                                                     rounded-full px-2.5 py-1
                                                     bg-amber-50 text-amber-900
                                                     font-label-sm text-label-sm
                                                     font-semibold">
                                            <span class="material-symbols-outlined text-[14px]">
                                                rate_review
                                            </span>
                                            Reviewed
                                        </span>

                                    @elseif ($status === 'RESOLVED')

                                        <span class="inline-flex items-center gap-1.5
                                                     rounded-full px-2.5 py-1
                                                     bg-secondary/10 text-secondary
                                                     font-label-sm text-label-sm
                                                     font-semibold">
                                            <span class="material-symbols-outlined text-[14px]">
                                                check_circle
                                            </span>
                                            Resolved
                                        </span>

                                    @elseif ($status === 'IGNORED')

                                        <span class="inline-flex items-center gap-1.5
                                                     rounded-full px-2.5 py-1
                                                     bg-surface-container
                                                     text-on-surface-variant
                                                     font-label-sm text-label-sm
                                                     font-semibold">
                                            <span class="material-symbols-outlined text-[14px]">
                                                visibility_off
                                            </span>
                                            Ignored
                                        </span>

                                    @else

                                        <span class="inline-flex items-center gap-1.5
                                                     rounded-full px-2.5 py-1
                                                     bg-surface-container
                                                     text-on-surface-variant
                                                     font-label-sm text-label-sm
                                                     font-semibold">
                                            {{ $item->status }}
                                        </span>

                                    @endif

                                </td>


                                {{-- ACTION --}}
                                <td class="px-5 py-4 align-top">

                                    @if ($item->status === 'OPEN')

                                        <form
                                            method="POST"
                                            action="{{ route('attendance.anomalies.resolve', $item) }}"
                                            class="space-y-3"
                                            data-anomaly-form
                                        >

                                            @csrf
                                            @method('PATCH')

                                            <div class="grid grid-cols-1 sm:grid-cols-[1fr_auto] gap-2">

                                                <input
                                                    type="text"
                                                    name="resolution_notes"
                                                    maxlength="1000"
                                                    placeholder="Catatan penyelesaian..."
                                                    class="w-full min-h-10 rounded-lg
                                                           border border-outline-variant
                                                           bg-surface-container-low
                                                           text-on-surface
                                                           px-3
                                                           font-body-sm text-body-sm
                                                           focus:outline-none
                                                           focus-visible:ring-2
                                                           focus-visible:ring-primary"
                                                >

                                                <select
                                                    name="status"
                                                    required
                                                    class="min-h-10 rounded-lg
                                                           border border-outline-variant
                                                           bg-surface-container-low
                                                           text-on-surface
                                                           px-3
                                                           font-body-sm text-body-sm
                                                           focus:outline-none
                                                           focus-visible:ring-2
                                                           focus-visible:ring-primary"
                                                >
                                                    <option value="REVIEWED">
                                                        Reviewed
                                                    </option>

                                                    <option value="RESOLVED">
                                                        Resolved
                                                    </option>

                                                    <option value="IGNORED">
                                                        Ignored
                                                    </option>
                                                </select>

                                            </div>


                                            <button
                                                type="submit"
                                                class="inline-flex items-center justify-center
                                                       gap-2 min-h-10 px-4 rounded-lg
                                                       inti-btn inti-btn-primary
                                                       font-title-sm text-title-sm
                                                       focus:outline-none
                                                       focus-visible:ring-2
                                                       focus-visible:ring-primary"
                                            >
                                                <span class="material-symbols-outlined text-[17px]">
                                                    save
                                                </span>

                                                Simpan Review
                                            </button>

                                        </form>

                                    @else

                                        <div class="rounded-lg
                                                    bg-surface-container-low
                                                    px-3 py-3">

                                            <div class="font-label-sm text-label-sm
                                                        text-on-surface-variant
                                                        uppercase tracking-wider">
                                                Catatan Resolusi
                                            </div>

                                            <div class="font-body-sm text-body-sm
                                                        text-on-surface mt-1 break-words">
                                                {{ $item->resolution_notes ?: 'Tidak ada catatan.' }}
                                            </div>

                                        </div>

                                    @endif

                                </td>

                            </tr>

                        @empty

                            <tr>

                                <td colspan="6" class="px-5 py-16">

                                    <div class="flex flex-col items-center
                                                justify-center text-center">

                                        <div class="flex h-14 w-14
                                                    items-center justify-center
                                                    rounded-full
                                                    bg-secondary/10
                                                    text-secondary">

                                            <span class="material-symbols-outlined text-[28px]">
                                                fact_check
                                            </span>

                                        </div>

                                        <h3 class="font-title-sm text-title-sm
                                                   text-primary mt-4">
                                            Tidak ada anomali
                                        </h3>

                                        <p class="font-body-sm text-body-sm
                                                  text-on-surface-variant
                                                  mt-1 max-w-md">
                                            Tidak ada temuan yang sesuai dengan
                                            filter saat ini.
                                        </p>

                                        <a
                                            href="{{ route('attendance.anomalies', ['scan' => 1]) }}"
                                            class="inline-flex items-center justify-center
                                                   gap-2 min-h-10 px-4 rounded-lg
                                                   inti-btn inti-btn-primary
                                                   font-title-sm text-title-sm mt-4"
                                        >
                                            <span class="material-symbols-outlined text-[17px]">
                                                sync
                                            </span>
                                            Jalankan Scan
                                        </a>

                                    </div>

                                </td>

                            </tr>

                        @endforelse

                    </tbody>

                </table>

            </div>


            {{-- MOBILE TABLE HINT --}}
            <div class="px-5 py-3 lg:hidden
                        border-t border-outline-variant/60">

                <div class="flex items-center gap-2
                            font-body-sm text-body-sm
                            text-on-surface-variant">

                    <span class="material-symbols-outlined text-[16px]">
                        swipe
                    </span>

                    Geser tabel secara horizontal untuk melihat
                    seluruh detail anomali.

                </div>

            </div>


            {{-- PAGINATION --}}
            @if ($anomalies->hasPages())

                <div class="px-5 py-4
                            border-t border-outline-variant/60">

                    {{ $anomalies->links() }}

                </div>

            @endif

        </section>


        {{-- GOVERNANCE NOTE --}}
        <section class="rounded-xl border border-outline-variant/70
                        bg-surface-container-lowest
                        shadow-sm p-5 sm:p-6">

            <div class="flex items-start gap-3">

                <div class="flex h-10 w-10 shrink-0
                            items-center justify-center
                            rounded-lg
                            bg-primary-container
                            text-on-primary">

                    <span class="material-symbols-outlined">
                        policy
                    </span>

                </div>

                <div>

                    <h2 class="font-title-sm text-title-sm text-primary">
                        Catatan Governance
                    </h2>

                    <p class="font-body-sm text-body-sm
                              text-on-surface-variant mt-1 leading-5">
                        Review anomali digunakan HR untuk mencatat
                        tindak lanjut atas temuan presensi dan lembur.
                        Setiap perubahan status tetap tercatat dalam
                        audit trail sistem.
                    </p>

                </div>

            </div>

        </section>

    </div>
</div>
@endsection


@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {

    const scanButton = document.querySelector('[data-anomaly-scan]');
    const scanLabel = document.querySelector('[data-scan-label]');

    if (scanButton) {
        scanButton.addEventListener('click', function () {

            if (scanButton.dataset.loading === '1') {
                return;
            }

            scanButton.dataset.loading = '1';
            scanButton.setAttribute('aria-busy', 'true');
            scanButton.classList.add('opacity-70', 'pointer-events-none');

            if (scanLabel) {
                scanLabel.textContent = 'Menjalankan Scan...';
            }
        });
    }


    document.querySelectorAll('[data-anomaly-form]').forEach(function (form) {

        form.addEventListener('submit', function () {

            const button = form.querySelector('button[type="submit"]');

            if (!button) {
                return;
            }

            if (button.dataset.loading === '1') {
                return;
            }

            button.dataset.loading = '1';
            button.disabled = true;
            button.setAttribute('aria-busy', 'true');
            button.classList.add('opacity-70');

            button.innerHTML = `
                <span class="material-symbols-outlined text-[17px]">
                    progress_activity
                </span>
                Menyimpan...
            `;
        });

    });

});
</script>
@endsection
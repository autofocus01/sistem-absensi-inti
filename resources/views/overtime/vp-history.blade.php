@extends('layouts.absensi')


@section('title', 'Riwayat Keputusan VP - PT. INTI')

@section('content')

<div class="vp-history-page inti-page p1-hr-governance">

    <div class="flex flex-col gap-space-base w-full">

        {{-- HEADER --}}
        <section class="vp-history-card bg-surface-container-lowest rounded-xl p-space-base lg:p-space-xl">

            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-space-base">

                <div class="min-w-0">

                    <div class="flex flex-wrap items-center gap-2 mb-2">

                        <span class="vp-history-status bg-secondary-container text-on-secondary-container">

                            <span class="material-symbols-outlined text-[15px]">
                                history
                            </span>

                            RIWAYAT KEPUTUSAN

                        </span>

                        <span class="text-on-surface-variant text-xs">
                            •
                        </span>

                        <span class="font-body-sm text-body-sm text-on-surface-variant">
                            VP Divisi
                        </span>

                    </div>

                    <h1 class="font-headline-lg text-headline-lg text-primary tracking-tight">
                        Riwayat Keputusan VP
                    </h1>

                    <p class="mt-1 max-w-3xl font-body-md text-body-md text-on-surface-variant">
                        Rekam keputusan lembur yang telah dibuat oleh akun VP
                        untuk anggota divisinya.
                    </p>

                </div>

                <a
                    href="{{ route('overtime.vp') }}"
                    class="inline-flex items-center justify-center gap-2 min-h-[44px] px-4 rounded-lg bg-primary text-on-primary font-title-sm hover:opacity-90 focus-visible:outline-none focus-visible:ring-4 focus-visible:ring-primary/15"
                >
                    <span class="material-symbols-outlined text-[18px]">
                        arrow_back
                    </span>

                    Antrean Approval
                </a>

            </div>

        </section>


        {{-- SUMMARY --}}
        <section class="grid grid-cols-1 sm:grid-cols-3 gap-3">

            <div class="vp-history-card rounded-xl bg-surface-container-lowest p-5">

                <div class="flex items-start justify-between gap-3">

                    <div>

                        <p class="font-label-sm text-label-sm text-on-surface-variant">
                            Total Keputusan
                        </p>

                        <p class="mt-2 font-headline-md text-headline-md text-primary">
                            {{ $submissions->total() }}
                        </p>

                        <p class="mt-1 font-body-sm text-body-sm text-on-surface-variant">
                            Seluruh keputusan akun VP
                        </p>

                    </div>

                    <span class="material-symbols-outlined text-primary text-[26px]">
                        fact_check
                    </span>

                </div>

            </div>


            <div class="vp-history-card rounded-xl bg-surface-container-lowest p-5">

                <div class="flex items-start justify-between gap-3">

                    <div>

                        <p class="font-label-sm text-label-sm text-on-surface-variant">
                            Cakupan
                        </p>

                        <p class="mt-2 font-title-lg text-title-lg text-on-surface">
                            Divisi Sendiri
                        </p>

                        <p class="mt-1 font-body-sm text-body-sm text-on-surface-variant">
                            Sesuai otorisasi VP
                        </p>

                    </div>

                    <span class="material-symbols-outlined text-secondary text-[26px]">
                        domain
                    </span>

                </div>

            </div>


            <div class="vp-history-card rounded-xl bg-surface-container-lowest p-5">

                <div class="flex items-start justify-between gap-3">

                    <div>

                        <p class="font-label-sm text-label-sm text-on-surface-variant">
                            Status Monitoring
                        </p>

                        <p class="mt-2 font-title-lg text-title-lg text-on-surface">
                            Real-time State
                        </p>

                        <p class="mt-1 font-body-sm text-body-sm text-on-surface-variant">
                            Keputusan vs status saat ini
                        </p>

                    </div>

                    <span class="material-symbols-outlined text-secondary text-[26px]">
                        timeline
                    </span>

                </div>

            </div>

        </section>


        {{-- HISTORY CARD --}}
        <section class="vp-history-card bg-surface-container-lowest rounded-xl overflow-hidden">

            <div class="px-space-base py-4 border-b border-surface-container-low">

                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">

                    <div>

                        <div class="flex items-center gap-2">

                            <span class="material-symbols-outlined text-[21px] text-primary">
                                manage_history
                            </span>

                            <h2 class="font-headline-sm text-headline-sm text-primary">
                                Keputusan Saya
                            </h2>

                        </div>

                        <p class="mt-1 font-body-sm text-body-sm text-on-surface-variant">
                            Hanya keputusan yang dibuat oleh akun VP yang sedang login.
                        </p>

                    </div>

                    <span class="vp-history-status bg-surface-container text-primary">
                        {{ $submissions->total() }} Keputusan
                    </span>

                </div>

            </div>


            <div class="vp-history-mobile-note items-start gap-2 mx-space-base mt-space-base rounded-lg bg-surface-container-low px-3 py-2 font-body-sm text-body-sm text-on-surface-variant">

                <span class="material-symbols-outlined text-[18px] shrink-0">
                    swipe
                </span>

                <span>
                    Geser tabel secara horizontal pada tablet atau ponsel untuk melihat seluruh detail riwayat.
                </span>

            </div>


            <div class="vp-history-table-wrap mt-space-base inti-responsive-scroll">

                <table class="vp-history-table text-left">

                    <thead>

                        <tr class="bg-surface-container-low text-on-surface-variant font-label-sm text-label-sm uppercase tracking-wider">

                            <th class="py-3 px-space-base">
                                No. Tiket
                            </th>

                            <th class="py-3 px-space-base">
                                Karyawan
                            </th>

                            <th class="py-3 px-space-base">
                                Tanggal &amp; Jam
                            </th>

                            <th class="py-3 px-space-base">
                                Durasi
                            </th>

                            <th class="py-3 px-space-base">
                                Keputusan VP
                            </th>

                            <th class="py-3 px-space-base">
                                Status Saat Ini
                            </th>

                            <th class="py-3 px-space-base">
                                Waktu Keputusan
                            </th>

                        </tr>

                    </thead>


                    <tbody class="divide-y divide-surface-container-low">

                    @forelse($submissions as $submission)

                        @php

                            $rejected =
                                $submission->status === 'REJECTED_VP';

                            $decisionLabel =
                                $rejected
                                    ? 'Ditolak'
                                    : 'Disetujui';

                            $decisionClass =
                                $rejected
                                    ? 'bg-red-50 text-red-800'
                                    : 'bg-emerald-50 text-emerald-800';

                            $decisionIcon =
                                $rejected
                                    ? 'cancel'
                                    : 'check_circle';

                            $currentMeta = match ($submission->status) {

                                'APPROVED_VP' => [
                                    'label' => 'Menunggu HR',
                                    'class' => 'bg-blue-50 text-blue-800',
                                    'icon' => 'pending'
                                ],

                                'VERIFIED_HR' => [
                                    'label' => 'Diverifikasi HR',
                                    'class' => 'bg-emerald-50 text-emerald-800',
                                    'icon' => 'verified'
                                ],

                                'REJECTED_HR' => [
                                    'label' => 'Ditolak HR',
                                    'class' => 'bg-red-50 text-red-800',
                                    'icon' => 'cancel'
                                ],

                                'RETURNED_TO_VP' => [
                                    'label' => 'Perlu Verifikasi Ulang',
                                    'class' => 'bg-amber-50 text-amber-900',
                                    'icon' => 'replay'
                                ],

                                'REJECTED_VP' => [
                                    'label' => 'Ditolak VP',
                                    'class' => 'bg-red-50 text-red-800',
                                    'icon' => 'block'
                                ],

                                default => [
                                    'label' => $submission->status,
                                    'class' => 'bg-surface-container text-on-surface-variant',
                                    'icon' => 'info'
                                ],

                            };

                            $decisionTime =
                                $submission->vp_reverified_at
                                ?: $submission->vp_approved_at;

                        @endphp


                        <tr class="align-top hover:bg-surface-container-low/40">


                            {{-- TICKET --}}
                            <td class="py-4 px-space-base">

                                <div class="vp-history-ticket">
                                    {{ $submission->ticket_number }}
                                </div>

                                <div class="mt-2 font-body-sm text-body-sm text-on-surface-variant">
                                    {{ $submission->division?->nama ?? 'Divisi belum tersedia' }}
                                </div>

                            </td>


                            {{-- EMPLOYEE --}}
                            <td class="py-4 px-space-base">

                                <div class="font-title-sm text-title-sm text-primary">
                                    {{ $submission->user?->name ?? 'Karyawan' }}
                                </div>

                                <div class="mt-1 font-body-sm text-body-sm text-on-surface-variant">

                                    {{ $submission->user?->employee?->nipeg ?? $submission->user?->email }}

                                    @if($submission->user?->employee?->band_code !== null)

                                        <span class="mx-1">
                                            ·
                                        </span>

                                        <span class="font-semibold text-primary">
                                            Band {{ $submission->user->employee->band_code }}
                                        </span>

                                    @endif

                                </div>

                            </td>


                            {{-- DATE --}}
                            <td class="py-4 px-space-base">

                                <div class="font-title-sm text-title-sm text-on-surface">
                                    {{ $submission->overtime_date?->format('d/m/Y') ?? '-' }}
                                </div>

                                <div class="mt-1 inline-flex items-center gap-1.5 font-body-sm text-body-sm text-on-surface-variant">

                                    <span class="material-symbols-outlined text-[16px]">
                                        schedule
                                    </span>

                                    {{ substr($submission->start_time, 0, 5) }}
                                    —
                                    {{ substr($submission->end_time, 0, 5) }}
                                    WIB

                                </div>

                            </td>


                            {{-- DURATION --}}
                            <td class="py-4 px-space-base">

                                <div class="font-title-lg text-title-lg text-secondary">
                                    {{ number_format((float) $submission->estimated_hours, 2) }}
                                </div>

                                <div class="mt-1 font-label-sm text-label-sm text-on-surface-variant">
                                    Jam
                                </div>

                            </td>


                            {{-- DECISION --}}
                            <td class="py-4 px-space-base">

                                <div class="vp-history-decision">

                                    <span class="vp-history-status {{ $decisionClass }}">

                                        <span class="material-symbols-outlined text-[15px]">
                                            {{ $decisionIcon }}
                                        </span>

                                        {{ $decisionLabel }}

                                    </span>


                                    @if($submission->vp_notes)

                                        <div class="vp-history-note bg-surface-container-low text-on-surface-variant">

                                            <div class="flex items-center gap-1.5 font-semibold text-on-surface">

                                                <span class="material-symbols-outlined text-[15px]">
                                                    sticky_note_2
                                                </span>

                                                Catatan VP

                                            </div>

                                            <div class="mt-1">
                                                {{ $submission->vp_notes }}
                                            </div>

                                        </div>

                                    @endif

                                </div>

                            </td>


                            {{-- CURRENT STATUS --}}
                            <td class="py-4 px-space-base">

                                <div class="vp-history-current">

                                    <span class="vp-history-status {{ $currentMeta['class'] }}">

                                        <span class="material-symbols-outlined text-[15px]">
                                            {{ $currentMeta['icon'] }}
                                        </span>

                                        {{ $currentMeta['label'] }}

                                    </span>


                                    @if(
                                        $submission->status === 'RETURNED_TO_VP'
                                        && $submission->hr_return_reason
                                    )

                                        <div class="vp-history-note bg-amber-50 text-amber-900">

                                            <div class="flex items-center gap-1.5 font-semibold">

                                                <span class="material-symbols-outlined text-[15px]">
                                                    report_problem
                                                </span>

                                                Catatan HR

                                            </div>

                                            <div class="mt-1">
                                                {{ $submission->hr_return_reason }}
                                            </div>

                                        </div>

                                    @elseif(
                                        $submission->status === 'REJECTED_HR'
                                        && $submission->hr_notes
                                    )

                                        <div class="vp-history-note bg-surface-container-low text-on-surface-variant">

                                            <div class="flex items-center gap-1.5 font-semibold text-on-surface">

                                                <span class="material-symbols-outlined text-[15px]">
                                                    sticky_note_2
                                                </span>

                                                Catatan HR

                                            </div>

                                            <div class="mt-1">
                                                {{ $submission->hr_notes }}
                                            </div>

                                        </div>

                                    @endif

                                </div>

                            </td>


                            {{-- DECISION TIME --}}
                            <td class="py-4 px-space-base">

                                <div class="vp-history-time font-body-sm text-body-sm text-on-surface-variant">

                                    <div class="flex items-start gap-1.5">

                                        <span class="material-symbols-outlined text-[16px]">
                                            schedule
                                        </span>

                                        <span>
                                            {{ $decisionTime?->format('d/m/Y H:i') ?? '-' }}
                                            WIB
                                        </span>

                                    </div>

                                </div>

                            </td>

                        </tr>


                    @empty

                        <tr>

                            <td
                                colspan="7"
                                class="vp-history-empty py-12 px-6 text-center"
                            >

                                <div class="flex flex-col items-center justify-center">

                                    <div class="vp-history-icon">

                                        <span class="material-symbols-outlined text-[26px]">
                                            history
                                        </span>

                                    </div>

                                    <p class="mt-4 font-title-md text-title-md text-on-surface">
                                        Belum ada keputusan lembur
                                    </p>

                                    <p class="mt-1 max-w-md font-body-sm text-body-sm text-on-surface-variant">
                                        Belum ada keputusan lembur yang diproses oleh akun VP ini.
                                    </p>

                                    <a
                                        href="{{ route('overtime.vp') }}"
                                        class="mt-5 inline-flex items-center justify-center gap-2 min-h-[42px] px-4 rounded-lg border border-primary/15 text-primary font-title-sm hover:bg-primary/5 focus-visible:outline-none focus-visible:ring-4 focus-visible:ring-primary/10"
                                    >

                                        <span class="material-symbols-outlined text-[18px]">
                                            playlist_add_check
                                        </span>

                                        Buka Antrean Approval

                                    </a>

                                </div>

                            </td>

                        </tr>

                    @endforelse

                    </tbody>

                </table>

            </div>


            {{-- PAGINATION --}}
            @if($submissions->hasPages())

                <div class="px-space-base pb-space-base pt-4">

                    <div class="border-t border-surface-container-low pt-4">
                        {{ $submissions->links() }}
                    </div>

                </div>

            @endif

        </section>

    </div>

</div>

@endsection
@extends('layouts.absensi')


@section('title', 'Riwayat Pengajuan Lembur - PT. INTI')

@section('content')

<div class="ot-history-page inti-page p1-hr-governance">

    <div class="flex flex-col gap-space-base w-full">

        {{-- HEADER --}}
        <section class="ot-history-card bg-surface-container-lowest rounded-xl p-space-base lg:p-space-xl">

            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-space-base">

                <div class="min-w-0">

                    <div class="flex flex-wrap items-center gap-2 mb-2">

                        <span class="ot-history-status bg-secondary-container text-on-secondary-container">

                            <span class="material-symbols-outlined text-[15px]">
                                history
                            </span>

                            RIWAYAT LEMBUR

                        </span>

                        <span class="text-on-surface-variant text-xs">
                            •
                        </span>

                        <span class="font-body-sm text-body-sm text-on-surface-variant">
                            Karyawan
                        </span>

                    </div>

                    <h1 class="font-headline-lg text-headline-lg text-primary tracking-tight">
                        Riwayat Pengajuan Lembur
                    </h1>

                    <p class="mt-1 max-w-3xl font-body-md text-body-md text-on-surface-variant">
                        Pantau status pengajuan, keputusan VP, serta hasil verifikasi HR
                        atas seluruh pengajuan lembur Anda.
                    </p>

                </div>


                <a
                    href="{{ route('overtime.index') }}"
                    class="inline-flex items-center justify-center gap-2 min-h-[44px] px-4 rounded-lg bg-primary text-on-primary font-title-sm hover:opacity-90 focus-visible:outline-none focus-visible:ring-4 focus-visible:ring-primary/15"
                >

                    <span class="material-symbols-outlined text-[18px]">
                        add
                    </span>

                    Pengajuan Baru

                </a>

            </div>

        </section>


        {{-- SUMMARY --}}
        <section class="grid grid-cols-1 sm:grid-cols-3 gap-3">

            <div class="ot-history-card rounded-xl bg-surface-container-lowest p-5">

                <div class="flex items-start justify-between gap-3">

                    <div>

                        <p class="font-label-sm text-label-sm text-on-surface-variant">
                            Total Pengajuan
                        </p>

                        <p class="mt-2 font-headline-md text-headline-md text-primary">
                            {{ $submissions->total() }}
                        </p>

                        <p class="mt-1 font-body-sm text-body-sm text-on-surface-variant">
                            Seluruh pengajuan lembur
                        </p>

                    </div>

                    <span class="material-symbols-outlined text-primary text-[26px]">
                        receipt_long
                    </span>

                </div>

            </div>


            <div class="ot-history-card rounded-xl bg-surface-container-lowest p-5">

                <div class="flex items-start justify-between gap-3">

                    <div>

                        <p class="font-label-sm text-label-sm text-on-surface-variant">
                            Urutan
                        </p>

                        <p class="mt-2 font-title-lg text-title-lg text-on-surface">
                            Terbaru
                        </p>

                        <p class="mt-1 font-body-sm text-body-sm text-on-surface-variant">
                            Pengajuan terbaru ditampilkan lebih dahulu
                        </p>

                    </div>

                    <span class="material-symbols-outlined text-secondary text-[26px]">
                        sort
                    </span>

                </div>

            </div>


            <div class="ot-history-card rounded-xl bg-surface-container-lowest p-5">

                <div class="flex items-start justify-between gap-3">

                    <div>

                        <p class="font-label-sm text-label-sm text-on-surface-variant">
                            Alur
                        </p>

                        <p class="mt-2 font-title-lg text-title-lg text-on-surface">
                            VP → HR
                        </p>

                        <p class="mt-1 font-body-sm text-body-sm text-on-surface-variant">
                            Keputusan dan verifikasi dapat dipantau
                        </p>

                    </div>

                    <span class="material-symbols-outlined text-secondary text-[26px]">
                        route
                    </span>

                </div>

            </div>

        </section>


        {{-- STATUS GUIDE --}}
        <section class="rounded-xl border border-primary/10 bg-primary-container/15 p-4">

            <div class="flex items-start gap-3">

                <span class="material-symbols-outlined text-primary">
                    info
                </span>

                <div class="min-w-0">

                    <p class="font-title-sm text-title-sm text-primary">
                        Cara membaca status
                    </p>

                    <p class="mt-1 font-body-sm text-body-sm text-on-surface-variant">
                        Status menunjukkan posisi pengajuan Anda pada alur persetujuan.
                        Catatan VP atau HR akan muncul pada kolom keputusan jika tersedia.
                    </p>

                </div>

            </div>

        </section>


        {{-- HISTORY --}}
        <section class="ot-history-card bg-surface-container-lowest rounded-xl overflow-hidden">

            <div class="px-space-base py-4 border-b border-surface-container-low">

                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">

                    <div>

                        <div class="flex items-center gap-2">

                            <span class="material-symbols-outlined text-[21px] text-primary">
                                manage_history
                            </span>

                            <h2 class="font-headline-sm text-headline-sm text-primary">
                                Daftar Pengajuan
                            </h2>

                        </div>

                        <p class="mt-1 font-body-sm text-body-sm text-on-surface-variant">
                            Detail status dan keputusan setiap pengajuan.
                        </p>

                    </div>

                    <span class="ot-history-status bg-surface-container text-primary">

                        <span class="material-symbols-outlined text-[15px]">
                            receipt_long
                        </span>

                        {{ $submissions->total() }} Pengajuan

                    </span>

                </div>

            </div>


            <div class="ot-history-mobile-note items-start gap-2 mx-space-base mt-space-base rounded-lg bg-surface-container-low px-3 py-2 font-body-sm text-body-sm text-on-surface-variant">

                <span class="material-symbols-outlined text-[18px] shrink-0">
                    swipe
                </span>

                <span>
                    Geser tabel secara horizontal pada tablet atau ponsel untuk melihat seluruh detail pengajuan.
                </span>

            </div>


            <div class="ot-history-table-wrap mt-space-base">

                <table class="ot-history-table text-left">

                    <thead>

                        <tr class="bg-surface-container-low text-on-surface-variant font-label-sm text-label-sm uppercase tracking-wider">

                            <th class="py-3 px-space-base">
                                No. Tiket
                            </th>

                            <th class="py-3 px-space-base">
                                Tanggal &amp; Jam
                            </th>

                            <th class="py-3 px-space-base">
                                Durasi
                            </th>

                            <th class="py-3 px-space-base">
                                Status
                            </th>

                            <th class="py-3 px-space-base">
                                Keputusan / Catatan
                            </th>

                            <th class="py-3 px-space-base">
                                Diperbarui
                            </th>

                        </tr>

                    </thead>


                    <tbody class="divide-y divide-surface-container-low">

                    @forelse($submissions as $submission)

                        @php

                            $statusMeta = match ($submission->status) {

                                'PENDING_VP' => [
                                    'label' => 'Menunggu VP',
                                    'class' => 'bg-amber-50 text-amber-800',
                                    'icon' => 'schedule'
                                ],

                                'APPROVED_VP' => [
                                    'label' => 'Disetujui VP • Menunggu HR',
                                    'class' => 'bg-blue-50 text-blue-800',
                                    'icon' => 'pending'
                                ],

                                'REJECTED_VP' => [
                                    'label' => 'Ditolak VP',
                                    'class' => 'bg-red-50 text-red-800',
                                    'icon' => 'cancel'
                                ],

                                'VERIFIED_HR' => [
                                    'label' => 'Diverifikasi HR',
                                    'class' => 'bg-emerald-50 text-emerald-800',
                                    'icon' => 'verified'
                                ],

                                'REJECTED_HR' => [
                                    'label' => 'Ditolak HR',
                                    'class' => 'bg-red-50 text-red-800',
                                    'icon' => 'block'
                                ],

                                'RETURNED_TO_VP' => [
                                    'label' => 'Dikembalikan ke VP • Verifikasi Ulang',
                                    'class' => 'bg-amber-50 text-amber-900',
                                    'icon' => 'replay'
                                ],

                                default => [
                                    'label' => $submission->status,
                                    'class' => 'bg-surface-container text-on-surface-variant',
                                    'icon' => 'info'
                                ],

                            };


                            $note = match ($submission->status) {

                                'RETURNED_TO_VP' =>
                                    $submission->hr_return_reason,

                                'REJECTED_VP',
                                'APPROVED_VP' =>
                                    $submission->vp_notes,

                                default =>
                                    $submission->hr_notes ?: $submission->vp_notes,

                            };

                        @endphp


                        <tr class="align-top hover:bg-surface-container-low/40">


                            {{-- TICKET --}}
                            <td class="py-4 px-space-base">

                                <div class="ot-history-ticket">
                                    {{ $submission->ticket_number }}
                                </div>

                                <div class="mt-2">

                                    <span class="ot-history-mode">

                                        {{ strtoupper(str_replace('_', ' ', $submission->submission_mode)) }}

                                    </span>

                                </div>

                            </td>


                            {{-- DATE & TIME --}}
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


                                @if($submission->recognized_minutes !== null)

                                    <div class="ot-history-recognized">

                                        <span class="material-symbols-outlined text-[15px]">
                                            verified
                                        </span>

                                        Diakui:
                                        {{ $submission->recognized_minutes }}
                                        menit

                                    </div>

                                @endif

                            </td>


                            {{-- STATUS --}}
                            <td class="py-4 px-space-base">

                                <div class="flex flex-col items-start gap-2">

                                    <span class="ot-history-status {{ $statusMeta['class'] }}">

                                        <span class="material-symbols-outlined text-[15px]">
                                            {{ $statusMeta['icon'] }}
                                        </span>

                                        {{ $statusMeta['label'] }}

                                    </span>


                                    @if(
                                        $submission->eligibility_status
                                        && $submission->eligibility_status !== 'ELIGIBLE'
                                    )

                                        <div class="inline-flex items-start gap-1.5 text-xs text-red-700">

                                            <span class="material-symbols-outlined text-[15px]">
                                                warning
                                            </span>

                                            <span>
                                                Kelayakan:
                                                {{ $submission->eligibility_status }}
                                            </span>

                                        </div>

                                    @endif

                                </div>

                            </td>


                            {{-- DECISION / NOTE --}}
                            <td class="py-4 px-space-base">

                                <div class="flex flex-col gap-2">

                                    @if($submission->status === 'REJECTED_VP')

                                        <div class="font-title-sm text-title-sm text-red-800">
                                            Ditolak oleh VP
                                        </div>

                                        <div class="font-body-sm text-body-sm text-on-surface-variant">

                                            <span class="material-symbols-outlined align-middle text-[15px]">
                                                person
                                            </span>

                                            {{ $submission->vpApprover?->name ?? 'VP Divisi' }}

                                        </div>


                                    @elseif(
                                        in_array(
                                            $submission->status,
                                            [
                                                'APPROVED_VP',
                                                'VERIFIED_HR',
                                                'REJECTED_HR',
                                                'RETURNED_TO_VP'
                                            ],
                                            true
                                        )
                                        && $submission->vp_approved_at
                                    )

                                        <div class="font-title-sm text-title-sm text-on-surface">

                                            VP: Disetujui

                                            @if($submission->status === 'RETURNED_TO_VP')
                                                <span class="text-amber-800">
                                                    • Menunggu Verifikasi Ulang
                                                </span>
                                            @endif

                                        </div>

                                        <div class="font-body-sm text-body-sm text-on-surface-variant">

                                            <span class="material-symbols-outlined align-middle text-[15px]">
                                                person
                                            </span>

                                            {{ $submission->vpApprover?->name ?? 'VP Divisi' }}

                                        </div>


                                    @else

                                        <div class="inline-flex items-center gap-1.5 font-body-sm text-body-sm text-on-surface-variant">

                                            <span class="material-symbols-outlined text-[16px]">
                                                hourglass_empty
                                            </span>

                                            Belum ada keputusan

                                        </div>

                                    @endif


                                    @if($note)

                                        <div class="ot-history-note bg-surface-container-low text-on-surface-variant">

                                            <div class="flex items-center gap-1.5 font-semibold text-on-surface">

                                                <span class="material-symbols-outlined text-[15px]">
                                                    sticky_note_2
                                                </span>

                                                Catatan

                                            </div>

                                            <div class="mt-1">
                                                {{ $note }}
                                            </div>

                                        </div>

                                    @endif

                                </div>

                            </td>


                            {{-- UPDATED --}}
                            <td class="py-4 px-space-base">

                                <div class="ot-history-time inline-flex items-start gap-1.5 font-body-sm text-body-sm text-on-surface-variant">

                                    <span class="material-symbols-outlined text-[16px]">
                                        schedule
                                    </span>

                                    <span>
                                        {{ $submission->updated_at?->format('d/m/Y H:i') ?? '-' }}
                                        WIB
                                    </span>

                                </div>

                            </td>

                        </tr>


                    @empty

                        <tr>

                            <td
                                colspan="6"
                                class="ot-history-empty py-12 px-6 text-center"
                            >

                                <div class="flex flex-col items-center justify-center">

                                    <div class="ot-history-empty-icon">

                                        <span class="material-symbols-outlined text-[27px]">
                                            history
                                        </span>

                                    </div>

                                    <p class="mt-4 font-title-md text-title-md text-on-surface">
                                        Belum ada pengajuan lembur
                                    </p>

                                    <p class="mt-1 max-w-md font-body-sm text-body-sm text-on-surface-variant">
                                        Pengajuan lembur yang Anda buat akan muncul di halaman ini
                                        beserta status dan keputusan setiap tahap.
                                    </p>

                                    <a
                                        href="{{ route('overtime.index') }}"
                                        class="mt-5 inline-flex items-center justify-center gap-2 min-h-[42px] px-4 rounded-lg bg-primary text-on-primary font-title-sm hover:opacity-90 focus-visible:outline-none focus-visible:ring-4 focus-visible:ring-primary/15"
                                    >

                                        <span class="material-symbols-outlined text-[18px]">
                                            add
                                        </span>

                                        Buat Pengajuan Lembur

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
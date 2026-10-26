@extends('layouts.absensi')


@section('title', 'Audit Trail - PT. INTI')

@section('content')
<div class="inti-page p1-hr-governance">

    <div class="space-y-6">

        {{-- =====================================================
             HEADER
        ====================================================== --}}
        <section>
            <div class="flex flex-col lg:flex-row
                        lg:items-start lg:justify-between gap-4">

                <div>

                    <div class="flex flex-wrap items-center gap-2 mb-2">

                        <span class="inti-badge inti-badge-lg inti-badge-primary">

                            <span class="material-symbols-outlined text-[15px]">
                                security
                            </span>

                            Governance
                        </span>

                        <span class="inti-badge inti-badge-lg inti-badge-neutral">

                            <span class="material-symbols-outlined text-[15px]">
                                history
                            </span>

                            Immutable Activity Record
                        </span>

                    </div>

                    <h1 class="inti-title">
                        Audit Trail
                    </h1>

                    <p class="inti-subtitle max-w-3xl">
                        Riwayat aksi penting pada presensi dan workflow
                        lembur yang tercatat oleh sistem.
                    </p>

                </div>

            </div>
        </section>


        {{-- =====================================================
             FILTER
        ====================================================== --}}
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
                            Filter Audit
                        </h2>

                        <p class="font-body-sm text-body-sm
                                  text-on-surface-variant mt-1">
                            Gunakan filter untuk mempersempit riwayat
                            aktivitas yang ingin diperiksa.
                        </p>

                    </div>

                </div>

            </div>


            <form
                method="GET"
                class="p-5 sm:p-6"
            >

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">

                    {{-- ACTION --}}
                    <div class="lg:col-span-2">

                        <label
                            for="audit-action"
                            class="block font-label-sm text-label-sm
                                   text-on-surface-variant mb-2"
                        >
                            Aksi
                        </label>

                        <input
                            id="audit-action"
                            type="text"
                            name="action"
                            value="{{ $filters['action'] ?? '' }}"
                            placeholder="CLOCK_OUT / HR_VERIFIED"
                            autocomplete="off"
                            class="w-full min-h-11 rounded-lg
                                   border border-outline-variant
                                   bg-surface-container-low
                                   text-on-surface
                                   px-3 sm:px-4
                                   font-body-md text-body-md
                                   focus:outline-none
                                   focus-visible:ring-2
                                   focus-visible:ring-primary
                                   focus-visible:border-primary"
                        >

                        <p class="font-label-sm text-label-sm
                                  text-on-surface-variant mt-1.5">
                            Masukkan nama aksi atau kode event yang tercatat.
                        </p>

                    </div>


                    {{-- FROM --}}
                    <div>

                        <label
                            for="audit-from"
                            class="block font-label-sm text-label-sm
                                   text-on-surface-variant mb-2"
                        >
                            Dari
                        </label>

                        <input
                            id="audit-from"
                            type="date"
                            name="from"
                            value="{{ $filters['from'] ?? '' }}"
                            class="w-full min-h-11 rounded-lg
                                   border border-outline-variant
                                   bg-surface-container-low
                                   text-on-surface
                                   px-3
                                   font-body-md text-body-md
                                   focus:outline-none
                                   focus-visible:ring-2
                                   focus-visible:ring-primary
                                   focus-visible:border-primary"
                        >

                    </div>


                    {{-- UNTIL --}}
                    <div>

                        <label
                            for="audit-until"
                            class="block font-label-sm text-label-sm
                                   text-on-surface-variant mb-2"
                        >
                            Sampai
                        </label>

                        <input
                            id="audit-until"
                            type="date"
                            name="until"
                            value="{{ $filters['until'] ?? '' }}"
                            class="w-full min-h-11 rounded-lg
                                   border border-outline-variant
                                   bg-surface-container-low
                                   text-on-surface
                                   px-3
                                   font-body-md text-body-md
                                   focus:outline-none
                                   focus-visible:ring-2
                                   focus-visible:ring-primary
                                   focus-visible:border-primary"
                        >

                    </div>

                </div>


                <div class="flex flex-col-reverse sm:flex-row
                            sm:items-center sm:justify-end gap-2 mt-5">

                    <a
                        href="{{ request()->url() }}"
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
                               min-h-10 px-5 rounded-lg
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


        {{-- =====================================================
             AUDIT LOG TABLE
        ====================================================== --}}
        <section class="rounded-xl border border-outline-variant/70
                        bg-surface-container-lowest
                        shadow-sm overflow-hidden">

            <div class="p-5 sm:p-6
                        border-b border-outline-variant/60">

                <div class="flex flex-col sm:flex-row
                            sm:items-center sm:justify-between gap-3">

                    <div>

                        <h2 class="font-headline-sm text-headline-sm text-primary">
                            Riwayat Aktivitas
                        </h2>

                        <p class="font-body-sm text-body-sm
                                  text-on-surface-variant mt-1">
                            Catatan aktivitas sistem berdasarkan waktu,
                            pengguna, aksi, dan sumber perubahan.
                        </p>

                    </div>

                    <div class="inline-flex items-center gap-2
                                rounded-full px-3 py-1.5
                                bg-surface-container
                                text-on-surface-variant
                                font-label-sm text-label-sm
                                shrink-0">

                        <span class="material-symbols-outlined text-[16px]">
                            manage_search
                        </span>

                        Audit Log

                    </div>

                </div>

            </div>


            <div class="overflow-x-auto">

                <table class="w-full min-w-[1100px] text-left">

                    <thead>

                        <tr class="bg-surface-container-low
                                   text-on-surface-variant
                                   font-label-sm text-label-sm
                                   uppercase tracking-wider">

                            <th class="px-5 py-3.5">
                                Waktu
                            </th>

                            <th class="px-5 py-3.5">
                                User
                            </th>

                            <th class="px-5 py-3.5">
                                Aksi
                            </th>

                            <th class="px-5 py-3.5">
                                Subject
                            </th>

                            <th class="px-5 py-3.5 min-w-[280px]">
                                Catatan
                            </th>

                            <th class="px-5 py-3.5">
                                IP Address
                            </th>

                        </tr>

                    </thead>


                    <tbody class="divide-y divide-surface-container-low
                                 font-body-md text-body-md">

                        @forelse ($logs as $log)

                            <tr class="hover:bg-surface-container-low/60
                                       transition-colors">

                                {{-- TIME --}}
                                <td class="px-5 py-4 align-top whitespace-nowrap">

                                    <div class="flex items-start gap-2">

                                        <span class="flex h-8 w-8 shrink-0
                                                     items-center justify-center
                                                     rounded-lg
                                                     bg-surface-container
                                                     text-primary">

                                            <span class="material-symbols-outlined text-[17px]">
                                                schedule
                                            </span>

                                        </span>

                                        <div>

                                            <div class="font-title-sm text-title-sm
                                                        text-primary">
                                                {{ $log->created_at?->format('d/m/Y') }}
                                            </div>

                                            <div class="font-mono text-xs
                                                        text-on-surface-variant mt-0.5">
                                                {{ $log->created_at?->format('H:i:s') }}
                                            </div>

                                        </div>

                                    </div>

                                </td>


                                {{-- USER --}}
                                <td class="px-5 py-4 align-top">

                                    <div class="flex items-start gap-2">

                                        <div class="flex h-8 w-8 shrink-0
                                                    items-center justify-center
                                                    rounded-full
                                                    bg-primary-container
                                                    text-on-primary
                                                    font-title-sm text-title-sm">

                                            {{ strtoupper(
                                                substr(
                                                    $log->user?->name ?? 'S',
                                                    0,
                                                    1
                                                )
                                            ) }}

                                        </div>

                                        <div class="min-w-0">

                                            <div class="font-title-sm text-title-sm
                                                        text-primary">
                                                {{ $log->user?->name ?? 'System' }}
                                            </div>

                                            @if ($log->user?->email)

                                                <div class="font-body-sm text-body-sm
                                                            text-on-surface-variant
                                                            mt-0.5 max-w-[220px]
                                                            truncate">
                                                    {{ $log->user->email }}
                                                </div>

                                            @endif

                                        </div>

                                    </div>

                                </td>


                                {{-- ACTION --}}
                                <td class="px-5 py-4 align-top">

                                    <span class="inline-flex items-center gap-1.5
                                                 rounded-full px-2.5 py-1
                                                 bg-secondary/10
                                                 text-secondary
                                                 font-label-sm text-label-sm
                                                 font-semibold">

                                        <span class="material-symbols-outlined text-[14px]">
                                            bolt
                                        </span>

                                        {{ $log->action }}

                                    </span>

                                </td>


                                {{-- SUBJECT --}}
                                <td class="px-5 py-4 align-top">

                                    <div class="inline-flex items-center gap-2
                                                rounded-lg
                                                bg-surface-container-low
                                                px-3 py-2">

                                        <span class="material-symbols-outlined
                                                     text-primary text-[17px]">
                                            description
                                        </span>

                                        <div>

                                            <div class="font-title-sm text-title-sm
                                                        text-primary">
                                                {{ class_basename($log->subject_type ?? '-') }}
                                            </div>

                                            <div class="font-mono text-xs
                                                        text-on-surface-variant mt-0.5">
                                                #{{ $log->subject_id ?? '-' }}
                                            </div>

                                        </div>

                                    </div>

                                </td>


                                {{-- NOTES --}}
                                <td class="px-5 py-4 align-top">

                                    @if ($log->notes)

                                        <div class="max-w-md
                                                    text-on-surface
                                                    leading-5
                                                    break-words">
                                            {{ $log->notes }}
                                        </div>

                                    @else

                                        <span class="text-on-surface-variant">
                                            —
                                        </span>

                                    @endif

                                </td>


                                {{-- IP --}}
                                <td class="px-5 py-4 align-top">

                                    <div class="inline-flex items-center gap-1.5
                                                rounded-lg
                                                bg-surface-container-low
                                                px-2.5 py-1.5
                                                font-mono text-xs
                                                text-on-surface-variant
                                                whitespace-nowrap">

                                        <span class="material-symbols-outlined text-[15px]">
                                            lan
                                        </span>

                                        {{ $log->ip_address ?? '-' }}

                                    </div>

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
                                                    bg-surface-container
                                                    text-on-surface-variant">

                                            <span class="material-symbols-outlined text-[28px]">
                                                manage_search
                                            </span>

                                        </div>

                                        <h3 class="font-title-sm text-title-sm
                                                   text-primary mt-4">
                                            Belum ada audit log
                                        </h3>

                                        <p class="font-body-sm text-body-sm
                                                  text-on-surface-variant
                                                  mt-1 max-w-md">
                                            Belum ditemukan aktivitas yang
                                            sesuai dengan filter yang digunakan.
                                        </p>

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
                    seluruh detail audit.

                </div>

            </div>


            {{-- PAGINATION --}}
            <div class="px-5 py-4
                        border-t border-outline-variant/60">

                {{ $logs->links() }}

            </div>

        </section>


        {{-- =====================================================
             GOVERNANCE INFORMATION
        ====================================================== --}}
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
                        verified_user
                    </span>

                </div>

                <div>

                    <h2 class="font-title-sm text-title-sm text-primary">
                        Audit & Governance
                    </h2>

                    <p class="font-body-sm text-body-sm
                              text-on-surface-variant
                              mt-1 leading-5">
                        Audit Trail digunakan untuk menelusuri aktivitas
                        penting yang dilakukan pengguna maupun sistem.
                        Informasi waktu, user, aksi, subject, catatan,
                        dan alamat IP ditampilkan untuk mendukung
                        kebutuhan monitoring dan pemeriksaan internal.
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

    const from = document.getElementById('audit-from');
    const until = document.getElementById('audit-until');

    if (!from || !until) {
        return;
    }

    const syncDateRange = function () {

        if (from.value) {
            until.min = from.value;
        } else {
            until.removeAttribute('min');
        }

        if (until.value) {
            from.max = until.value;
        } else {
            from.removeAttribute('max');
        }
    };

    from.addEventListener('change', syncDateRange);
    until.addEventListener('change', syncDateRange);

    syncDateRange();

});
</script>
@endsection
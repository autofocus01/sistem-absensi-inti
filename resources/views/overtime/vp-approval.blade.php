@extends('layouts.absensi')


@section('title', 'Persetujuan Lembur VP — PT. INTI')

@section('content')

<div class="vp-approval-page inti-page p1-hr-governance">

    <div class="flex flex-col gap-space-base w-full">

        {{-- HEADER --}}
        <section class="vp-card bg-surface-container-lowest rounded-xl p-space-base lg:p-space-xl">

            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-space-base">

                <div class="min-w-0">

                    <div class="flex flex-wrap items-center gap-2 mb-2">

                        <span class="vp-status bg-primary-container/40 text-primary">
                            <span class="material-symbols-outlined text-[15px]">
                                admin_panel_settings
                            </span>
                            OTORISASI VP DIVISI
                        </span>

                        <span class="vp-status bg-secondary-container/50 text-secondary">
                            <span class="material-symbols-outlined text-[15px]">
                                domain
                            </span>
                            DIVISION BOUNDARY
                        </span>

                    </div>

                    <h1 class="font-headline-lg text-headline-lg text-primary tracking-tight">
                        Persetujuan Lembur
                    </h1>

                    <p class="mt-1 max-w-3xl font-body-md text-body-md text-on-surface-variant">
                        Tinjau pengajuan lembur dalam cakupan divisi Anda sebelum diteruskan
                        ke proses verifikasi HR.
                    </p>

                </div>

                <div class="flex items-center gap-2 shrink-0">

                    <div
                        id="badge-total-pending"
                        class="vp-ticket"
                        aria-live="polite"
                    >
                        <span class="material-symbols-outlined text-[16px]">
                            pending_actions
                        </span>

                        Memuat...
                    </div>

                </div>

            </div>

        </section>


        {{-- KPI --}}
        <section class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-3">

            <div class="vp-card vp-kpi bg-surface-container-lowest rounded-xl p-5">

                <div class="flex items-start justify-between gap-3">

                    <div>
                        <p class="font-label-sm text-label-sm text-on-surface-variant">
                            Menunggu Approval
                        </p>

                        <p
                            id="stat-pending-count"
                            class="mt-2 font-headline-md text-headline-md text-primary"
                        >
                            0
                        </p>

                        <p class="mt-1 font-body-sm text-body-sm text-on-surface-variant">
                            Antrean aktif
                        </p>
                    </div>

                    <span class="material-symbols-outlined text-primary text-[25px]">
                        pending_actions
                    </span>

                </div>

            </div>


            <div class="vp-card vp-kpi bg-surface-container-lowest rounded-xl p-5">

                <div class="flex items-start justify-between gap-3">

                    <div>
                        <p class="font-label-sm text-label-sm text-on-surface-variant">
                            Kontrol Akses
                        </p>

                        <p class="mt-2 font-title-lg text-title-lg text-on-surface">
                            Division Boundary
                        </p>

                        <p class="mt-1 font-body-sm text-body-sm text-on-surface-variant">
                            Diverifikasi server-side
                        </p>
                    </div>

                    <span class="material-symbols-outlined text-secondary text-[25px]">
                        security
                    </span>

                </div>

            </div>


            <div class="vp-card vp-kpi bg-surface-container-lowest rounded-xl p-5">

                <div class="flex items-start justify-between gap-3">

                    <div>
                        <p class="font-label-sm text-label-sm text-on-surface-variant">
                            State Diproses
                        </p>

                        <p class="mt-2 font-title-lg text-title-lg text-on-surface">
                            PENDING / RETURNED
                        </p>

                        <p class="mt-1 font-body-sm text-body-sm text-on-surface-variant">
                            State yang tersedia untuk VP
                        </p>
                    </div>

                    <span class="material-symbols-outlined text-amber-600 text-[25px]">
                        sync_problem
                    </span>

                </div>

            </div>


            <div class="vp-card vp-kpi bg-surface-container-lowest rounded-xl p-5">

                <div class="flex items-start justify-between gap-3">

                    <div>
                        <p class="font-label-sm text-label-sm text-on-surface-variant">
                            Tahap Berikutnya
                        </p>

                        <p class="mt-2 font-title-lg text-title-lg text-on-surface">
                            HR
                        </p>

                        <p class="mt-1 font-body-sm text-body-sm text-on-surface-variant">
                            Setelah APPROVED_VP
                        </p>
                    </div>

                    <span class="material-symbols-outlined text-secondary text-[25px]">
                        arrow_forward
                    </span>

                </div>

            </div>

        </section>


        {{-- INFO STRIP --}}
        <section class="grid grid-cols-1 md:grid-cols-3 gap-3">

            <div class="rounded-xl border border-primary/10 bg-primary-container/20 p-4">

                <div class="flex items-start gap-3">

                    <span class="material-symbols-outlined text-primary">
                        fact_check
                    </span>

                    <div>

                        <p class="font-title-sm text-title-sm text-primary">
                            Tinjau Pengajuan
                        </p>

                        <p class="mt-1 font-body-sm text-body-sm text-on-surface-variant">
                            Periksa tanggal, jam, durasi, alasan, dan data karyawan.
                        </p>

                    </div>

                </div>

            </div>


            <div class="rounded-xl border border-secondary/10 bg-secondary-container/20 p-4">

                <div class="flex items-start gap-3">

                    <span class="material-symbols-outlined text-secondary">
                        verified_user
                    </span>

                    <div>

                        <p class="font-title-sm text-title-sm text-secondary">
                            Persetujuan Divisi
                        </p>

                        <p class="mt-1 font-body-sm text-body-sm text-on-surface-variant">
                            Keputusan VP hanya berlaku pada cakupan divisinya.
                        </p>

                    </div>

                </div>

            </div>


            <div class="rounded-xl border border-amber-200 bg-amber-50 p-4">

                <div class="flex items-start gap-3">

                    <span class="material-symbols-outlined text-amber-700">
                        replay
                    </span>

                    <div>

                        <p class="font-title-sm text-title-sm text-amber-900">
                            Pengembalian HR
                        </p>

                        <p class="mt-1 font-body-sm text-body-sm text-amber-900/75">
                            Pengajuan yang dikembalikan perlu ditinjau ulang sebelum diproses kembali.
                        </p>

                    </div>

                </div>

            </div>

        </section>


        {{-- APPROVAL QUEUE --}}
        <section class="vp-card bg-surface-container-lowest rounded-xl overflow-hidden">

            <div class="px-space-base py-4 border-b border-surface-container-low">

                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">

                    <div>

                        <div class="flex items-center gap-2">

                            <span class="material-symbols-outlined text-[21px] text-primary">
                                playlist_add_check
                            </span>

                            <h2 class="font-headline-sm text-headline-sm text-primary">
                                Antrean Approval
                            </h2>

                        </div>

                        <p class="mt-1 font-body-sm text-body-sm text-on-surface-variant">
                            Tinjau setiap pengajuan sebelum menentukan keputusan VP.
                        </p>

                    </div>

                    <span class="font-label-sm text-label-sm text-on-surface-variant">
                        Refresh setelah tindakan
                    </span>

                </div>

            </div>


            <div class="vp-mobile-note items-start gap-2 mx-space-base mt-space-base rounded-lg bg-surface-container-low px-3 py-2 font-body-sm text-body-sm text-on-surface-variant">

                <span class="material-symbols-outlined text-[18px] shrink-0">
                    swipe
                </span>

                <span>
                    Geser tabel secara horizontal pada tablet atau ponsel untuk melihat seluruh informasi dan tombol aksi.
                </span>

            </div>


            <div class="vp-table-wrap mt-space-base inti-responsive-scroll">

                <table class="vp-table">

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
                                Uraian
                            </th>

                            <th class="py-3 px-space-base">
                                Status
                            </th>

                            <th class="py-3 px-space-base text-right">
                                Aksi
                            </th>

                        </tr>

                    </thead>


                    <tbody
                        id="vp-pending-table-body"
                        class="divide-y divide-surface-container-low text-on-surface font-body-md text-body-md"
                    >

                        <tr>

                            <td colspan="7" class="vp-state py-12 px-6 text-center">

                                <div class="flex flex-col items-center justify-center">

                                    <div class="vp-state-icon">

                                        <span class="material-symbols-outlined animate-spin">
                                            progress_activity
                                        </span>

                                    </div>

                                    <p class="mt-3 font-title-sm text-title-sm text-on-surface">
                                        Memuat antrean approval
                                    </p>

                                    <p class="mt-1 font-body-sm text-body-sm text-on-surface-variant">
                                        Sedang mengambil pengajuan yang tersedia untuk VP.
                                    </p>

                                </div>

                            </td>

                        </tr>

                    </tbody>

                </table>

            </div>

        </section>

    </div>

</div>

@endsection


@section('scripts')

<script>
document.addEventListener('DOMContentLoaded', loadVpPendingData);


function esc(value) {

    if (value === null || value === undefined) {
        return '';
    }

    return String(value)
        .replaceAll('&', '&amp;')
        .replaceAll('<', '&lt;')
        .replaceAll('>', '&gt;')
        .replaceAll('"', '&quot;')
        .replaceAll("'", '&#039;');
}


function setVpQueueState(type, title, detail = '') {

    const tbody = document.getElementById('vp-pending-table-body');

    if (!tbody) {
        return;
    }

    let icon = 'info';

    if (type === 'loading') {
        icon = 'progress_activity';
    }

    if (type === 'empty') {
        icon = 'task_alt';
    }

    if (type === 'error') {
        icon = 'error';
    }

    tbody.innerHTML = `
        <tr>
            <td colspan="7" class="vp-state py-12 px-6 text-center">

                <div class="flex flex-col items-center justify-center">

                    <div class="vp-state-icon">

                        <span class="material-symbols-outlined ${type === 'loading' ? 'animate-spin' : ''}">
                            ${icon}
                        </span>

                    </div>

                    <p class="mt-3 font-title-sm text-title-sm ${
                        type === 'error'
                            ? 'text-error'
                            : 'text-on-surface'
                    }">
                        ${esc(title)}
                    </p>

                    ${
                        detail
                            ? `
                                <p class="mt-1 max-w-md font-body-sm text-body-sm text-on-surface-variant">
                                    ${esc(detail)}
                                </p>
                              `
                            : ''
                    }

                    ${
                        type === 'error'
                            ? `
                                <button
                                    type="button"
                                    onclick="loadVpPendingData()"
                                    class="vp-action-btn mt-4 inline-flex items-center justify-center gap-2 px-4 py-2 rounded-lg bg-primary text-on-primary font-title-sm"
                                >
                                    <span class="material-symbols-outlined text-[18px]">
                                        refresh
                                    </span>
                                    Coba Lagi
                                </button>
                              `
                            : ''
                    }

                </div>

            </td>
        </tr>
    `;
}


async function loadVpPendingData() {

    const tableBody =
        document.getElementById('vp-pending-table-body');

    const counter =
        document.getElementById('stat-pending-count');

    const badge =
        document.getElementById('badge-total-pending');


    if (counter) {
        counter.textContent = '...';
    }

    if (badge) {
        badge.innerHTML = `
            <span class="material-symbols-outlined text-[16px] animate-spin">
                progress_activity
            </span>
            Memuat...
        `;
    }


    setVpQueueState(
        'loading',
        'Memuat antrean approval',
        'Sedang mengambil pengajuan yang tersedia untuk VP.'
    );


    try {

        const response = await fetch(
            "{{ route('overtime.vp.pending') }}",
            {
                headers: {
                    'Accept': 'application/json'
                }
            }
        );


        if (!response.ok) {
            throw new Error(
                `HTTP Error Status: ${response.status}`
            );
        }


        const data = await response.json();

        const rows =
            Array.isArray(data.data)
                ? data.data
                : [];


        if (counter) {
            counter.textContent = rows.length;
        }


        if (badge) {

            badge.innerHTML = `
                <span class="material-symbols-outlined text-[16px]">
                    pending_actions
                </span>

                ${rows.length} Tiket
            `;
        }


        if (rows.length === 0) {

            setVpQueueState(
                'empty',
                'Tidak ada antrean approval',
                'Semua pengajuan pada cakupan divisi sudah diproses.'
            );

            return;
        }


        tableBody.innerHTML = rows.map(item => {

            const returned =
                item.status === 'RETURNED_TO_VP';


            const statusBadge = returned
                ? `
                    <span class="vp-status bg-error/10 text-error">
                        <span class="material-symbols-outlined text-[15px]">
                            replay
                        </span>
                        PERLU VERIFIKASI ULANG
                    </span>
                  `
                : `
                    <span class="vp-status bg-amber-100 text-amber-800">
                        <span class="material-symbols-outlined text-[15px]">
                            schedule
                        </span>
                        PENDING VP
                    </span>
                  `;


            const userName =
                item.user?.name ||
                item.user?.employee?.nama ||
                'Karyawan';


            const nip =
                item.user?.employee?.nipeg ||
                item.user?.email ||
                '';


            const band =
                item.user?.employee?.band_code !== null &&
                item.user?.employee?.band_code !== undefined
                    ? `Band ${esc(item.user.employee.band_code)}`
                    : '';


            const ticket =
                item.ticket_number ||
                `#${item.id ?? '-'}`;


            const date =
                item.overtime_date ||
                '-';


            const start =
                item.start_time ||
                '-';


            const end =
                item.end_time ||
                '-';


            // estimated_hours is the actual submitted interval. The quota and
            // approval amount must use recognized_minutes, not estimated_hours.
            const actualMinutes = Number.isFinite(Number(item.estimated_hours))
                ? Math.max(0, Math.round(Number(item.estimated_hours) * 60))
                : Math.max(0, Number(item.rounded_minutes ?? 0));

            const recognizedMinutes = Math.max(
                0,
                Number(item.recognized_minutes ?? item.recognized_overtime_minutes ?? 0)
            );

            const excessMinutes = Math.max(
                0,
                Number(item.excess_minutes ?? Math.max(0, actualMinutes - recognizedMinutes))
            );

            const formatHours = minutes => {
                const value = (Math.max(0, Number(minutes) || 0) / 60);
                return `${Number.isInteger(value) ? value.toFixed(0) : value.toFixed(2).replace(/0+$/, '').replace(/\\.$/, '')} jam`;
            };

            const actualDurationLabel = formatHours(actualMinutes);
            const recognizedDurationLabel = formatHours(recognizedMinutes);
            const excessDurationLabel = formatHours(excessMinutes);


            const reason =
                item.reason ||
                '-';


            const hrReason =
                item.hr_return_reason ||
                'Anomali perlu ditinjau ulang.';


            const returnedNote = returned
                ? `
                    <div class="mt-2 rounded-lg bg-error/5 border border-error/10 px-3 py-2 text-[12px] leading-relaxed text-error">
                        <div class="font-bold">
                            Catatan HR
                        </div>

                        <div class="mt-0.5">
                            ${esc(hrReason)}
                        </div>
                    </div>
                  `
                : '';


            const approveLabel =
                returned
                    ? 'Verifikasi Ulang'
                    : 'Setujui';


            return `
                <tr>

                    {{-- TICKET --}}
                    <td class="py-4 px-space-base align-top">

                        <span class="font-title-sm text-title-sm text-primary">
                            ${esc(ticket)}
                        </span>

                    </td>


                    {{-- EMPLOYEE --}}
                    <td class="py-4 px-space-base align-top">

                        <div class="vp-identity-name">
                            ${esc(userName)}
                        </div>

                        <div class="vp-identity-meta">

                            ${
                                nip
                                    ? esc(nip)
                                    : 'NIP belum tersedia'
                            }

                            ${
                                band
                                    ? `
                                        <span class="mx-1">·</span>
                                        ${band}
                                      `
                                    : ''
                            }

                        </div>

                    </td>


                    {{-- DATE / TIME --}}
                    <td class="py-4 px-space-base align-top">

                        <div class="font-title-sm text-title-sm text-on-surface">
                            ${esc(date)}
                        </div>

                        <div class="mt-1 inline-flex items-center gap-1.5 font-body-sm text-body-sm text-on-surface-variant">

                            <span class="material-symbols-outlined text-[16px]">
                                schedule
                            </span>

                            ${esc(start)}
                            —
                            ${esc(end)}
                            WIB

                        </div>

                    </td>


                    {{-- DURASI AKTUAL, DIAKUI, DAN EXCESS --}}
                    <td class="py-4 px-space-base align-top">
                        <div class="vp-duration-main font-title-lg text-title-lg text-on-surface">
                            ${esc(actualDurationLabel)}
                        </div>
                        <div class="vp-duration-meta">
                            Diakui: <strong>${esc(recognizedDurationLabel)}</strong>
                        </div>
                        ${
                            excessMinutes > 0
                                ? `<div class="vp-duration-meta vp-duration-excess">Excess: ${esc(excessDurationLabel)}</div>`
                                : `<div class="vp-duration-meta">Excess: 0 jam</div>`
                        }
                    </td>


                    {{-- REASON --}}
                    <td class="py-4 px-space-base align-top">

                        <div
                            class="vp-reason font-body-sm text-body-sm text-on-surface-variant"
                            title="${esc(reason)}"
                        >
                            ${esc(reason)}
                        </div>

                        ${returnedNote}

                    </td>


                    {{-- STATUS --}}
                    <td class="py-4 px-space-base align-top">

                        ${statusBadge}

                    </td>


                    {{-- ACTION --}}
                    <td class="py-4 px-space-base align-top">

                        <div class="flex flex-col xl:flex-row items-stretch xl:items-center justify-end gap-2 min-w-[230px]">

                            <button
                                type="button"
                                class="vp-action-btn inline-flex items-center justify-center gap-2 px-3 py-2 rounded-lg border border-error/20 bg-error/5 text-error font-title-sm hover:bg-error/10"
                                onclick="rejectItem('${esc(item.id)}', ${returned})"
                            >
                                <span class="material-symbols-outlined text-[18px]">
                                    close
                                </span>

                                Tolak
                            </button>


                            <button
                                type="button"
                                class="vp-action-btn inline-flex items-center justify-center gap-2 px-3 py-2 rounded-lg bg-primary text-on-primary font-title-sm hover:opacity-90"
                                onclick="decision('${esc(item.id)}', 'APPROVE', ${returned})"
                            >
                                <span class="material-symbols-outlined text-[18px]">
                                    ${
                                        returned
                                            ? 'replay'
                                            : 'check_circle'
                                    }
                                </span>

                                ${approveLabel}
                            </button>

                        </div>

                    </td>

                </tr>
            `;

        }).join('');

    } catch (error) {

        console.error(
            'Error fetching VP pending data:',
            error
        );


        if (counter) {
            counter.textContent = '—';
        }


        if (badge) {

            badge.innerHTML = `
                <span class="material-symbols-outlined text-[16px]">
                    error
                </span>
                Gagal Memuat
            `;
        }


        setVpQueueState(
            'error',
            'Antrean gagal dimuat',
            error.message || 'Terjadi kesalahan saat mengambil data.'
        );
    }
}


async function decision(
    id,
    action,
    isReverification = false,
    providedNotes = null
) {

    let notes = providedNotes;


    if (action === 'APPROVE') {

        if (isReverification) {

            notes = prompt(
                'Catatan verifikasi ulang VP (opsional):',
                ''
            );

            if (notes === null) {
                return;
            }

        } else {

            const confirmed = confirm(
                'Setujui pengajuan lembur ini?'
            );

            if (!confirmed) {
                return;
            }
        }
    }


    if (action === 'REJECT' && !notes) {

        notes = prompt(
            isReverification
                ? 'Alasan penolakan setelah verifikasi ulang:'
                : 'Alasan penolakan (opsional):',
            ''
        );

        if (notes === null) {
            return;
        }

        notes = notes.trim() || null;
    }


    const actionButtons =
        document.querySelectorAll(
            `button[onclick*="('${id}'"]`
        );


    actionButtons.forEach(button => {

        button.disabled = true;

        button.classList.add(
            'opacity-60',
            'cursor-not-allowed'
        );

    });


    try {

        const response = await fetch(
            `/overtime/vp/approve/${id}`,
            {
                method: 'POST',

                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },

                body: JSON.stringify({
                    action: action,
                    notes: notes
                })
            }
        );


        const data =
            await response
                .json()
                .catch(() => ({}));


        if (!response.ok) {

            throw new Error(
                data.message ||
                'Keputusan gagal diproses.'
            );
        }


        alert(
            data.message ||
            'Keputusan berhasil diproses.'
        );


        await loadVpPendingData();

    } catch (error) {

        alert(
            error.message ||
            'Terjadi kesalahan saat memproses keputusan.'
        );

        await loadVpPendingData();
    }
}


function rejectItem(
    id,
    isReverification = false
) {

    const notes = prompt(
        isReverification
            ? 'Alasan penolakan setelah verifikasi ulang:'
            : 'Alasan penolakan (opsional):',
        ''
    );


    if (notes === null) {
        return;
    }


    decision(
        id,
        'REJECT',
        isReverification,
        notes.trim() || null
    );
}
</script>

@endsection
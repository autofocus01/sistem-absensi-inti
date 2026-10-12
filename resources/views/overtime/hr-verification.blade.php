@extends('layouts.absensi')


@section('title', 'Verifikasi Lembur HR - PT. INTI')

@section('content')

<div class="hr-verification-page inti-page p1-hr-governance">

    <div class="flex flex-col gap-space-base w-full">

        {{-- =====================================================
             HEADER
             ===================================================== --}}
        <section class="hr-card bg-surface-container-lowest rounded-xl p-space-base lg:p-space-xl">

            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-space-base">

                <div class="min-w-0">

                    <div class="flex flex-wrap items-center gap-2 mb-2">

                        <span class="hr-status-badge bg-secondary-container text-on-secondary-container">
                            <span class="material-symbols-outlined text-[15px]">
                                admin_panel_settings
                            </span>

                            MODUL HR ADMIN
                        </span>

                        <span class="hr-status-badge bg-primary-container/40 text-primary">
                            <span class="material-symbols-outlined text-[15px]">
                                verified
                            </span>

                            FINAL REVIEW
                        </span>

                    </div>

                    <h1 class="font-headline-lg text-headline-lg text-primary tracking-tight">
                        Verifikasi Jam Lembur
                    </h1>

                    <p class="mt-1 max-w-3xl font-body-md text-body-md text-on-surface-variant">
                        Verifikasi akhir pengajuan lembur yang telah disetujui VP
                        dengan mencocokkan permohonan terhadap jam aktual.
                    </p>

                </div>

                <div class="flex items-center gap-2 shrink-0">

                    <div
                        id="badge-total-hr"
                        class="hr-ticket-badge"
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


        {{-- =====================================================
             INFORMATION STRIP
             ===================================================== --}}
        <section class="grid grid-cols-1 md:grid-cols-3 gap-3">

            <div class="rounded-xl border border-primary/10 bg-primary-container/20 p-4">

                <div class="flex items-start gap-3">

                    <span class="material-symbols-outlined text-primary">
                        fact_check
                    </span>

                    <div>

                        <p class="font-title-sm text-title-sm text-primary">
                            Pemeriksaan akhir
                        </p>

                        <p class="mt-1 font-body-sm text-body-sm text-on-surface-variant">
                            HR melakukan validasi sebelum lembur menjadi resmi.
                        </p>

                    </div>

                </div>

            </div>


            <div class="rounded-xl border border-secondary/10 bg-secondary-container/20 p-4">

                <div class="flex items-start gap-3">

                    <span class="material-symbols-outlined text-secondary">
                        verified
                    </span>

                    <div>

                        <p class="font-title-sm text-title-sm text-secondary">
                            Approved VP
                        </p>

                        <p class="mt-1 font-body-sm text-body-sm text-on-surface-variant">
                            Antrean berasal dari pengajuan yang telah disetujui VP.
                        </p>

                    </div>

                </div>

            </div>


            <div class="rounded-xl border border-error/10 bg-error/5 p-4">

                <div class="flex items-start gap-3">

                    <span class="material-symbols-outlined text-error">
                        report_problem
                    </span>

                    <div>

                        <p class="font-title-sm text-title-sm text-error">
                            Anomali
                        </p>

                        <p class="mt-1 font-body-sm text-body-sm text-on-surface-variant">
                            Data bermasalah dapat dikembalikan kepada VP untuk pemeriksaan ulang.
                        </p>

                    </div>

                </div>

            </div>

        </section>


        {{-- =====================================================
             QUEUE CARD
             ===================================================== --}}
        <section class="hr-card bg-surface-container-lowest rounded-xl overflow-hidden">

            <div class="px-space-base py-4 border-b border-surface-container-low">

                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">

                    <div>

                        <div class="flex items-center gap-2">

                            <span class="material-symbols-outlined text-[21px] text-primary">
                                playlist_add_check
                            </span>

                            <h2 class="font-headline-sm text-headline-sm text-primary">
                                Antrean Verifikasi HR
                            </h2>

                        </div>

                        <p class="mt-1 font-body-sm text-body-sm text-on-surface-variant">
                            Tinjau setiap pengajuan sebelum menentukan hasil verifikasi.
                        </p>

                    </div>

                    <span class="font-label-sm text-label-sm text-on-surface-variant">
                        Refresh otomatis setelah tindakan
                    </span>

                </div>

            </div>


            <div class="hr-mobile-note items-start gap-2 mx-space-base mt-space-base rounded-lg bg-surface-container-low px-3 py-2 font-body-sm text-body-sm text-on-surface-variant">

                <span class="material-symbols-outlined text-[18px] shrink-0">
                    swipe
                </span>

                <span>
                    Geser tabel secara horizontal pada tablet atau ponsel untuk melihat seluruh informasi dan tombol aksi.
                </span>

            </div>


            <div class="hr-table-wrap mt-space-base inti-responsive-scroll">

                <table class="hr-table w-full text-left">

                    <thead>

                        <tr class="bg-surface-container-low text-on-surface-variant font-label-sm text-label-sm uppercase tracking-wider">

                            <th class="py-3 px-space-base">
                                Karyawan &amp; Divisi
                            </th>

                            <th class="py-3 px-space-base">
                                Tanggal &amp; Jam
                            </th>

                            <th class="py-3 px-space-base">
                                Estimasi Durasi
                            </th>

                            <th class="py-3 px-space-base">
                                Status VP
                            </th>

                            <th class="py-3 px-space-base">
                                Uraian Tugas
                            </th>

                            <th class="py-3 px-space-base text-right">
                                Aksi HR
                            </th>

                        </tr>

                    </thead>

                    <tbody
                        id="hr-pending-table-body"
                        class="divide-y divide-surface-container-low text-on-surface font-body-md text-body-md"
                    >

                        <tr>

                            <td
                                colspan="6"
                                class="hr-loading py-10 px-6 text-center"
                            >

                                <div class="flex flex-col items-center justify-center">

                                    <div class="hr-state-icon">

                                        <span class="material-symbols-outlined animate-spin">
                                            progress_activity
                                        </span>

                                    </div>

                                    <p class="mt-3 font-title-sm text-title-sm text-on-surface">
                                        Memuat antrean verifikasi
                                    </p>

                                    <p class="mt-1 font-body-sm text-body-sm text-on-surface-variant">
                                        Sedang mengambil pengajuan yang menunggu tindakan HR.
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
document.addEventListener('DOMContentLoaded', loadHrPendingData);

function escapeHtml(value) {
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

function setHrQueueState(type, message, detail = '') {
    const tbody = document.getElementById('hr-pending-table-body');

    if (!tbody) {
        return;
    }

    let icon = 'info';
    let title = message;

    if (type === 'loading') {
        icon = 'progress_activity';
        title = message;
    }

    if (type === 'empty') {
        icon = 'task_alt';
    }

    if (type === 'error') {
        icon = 'error';
    }

    tbody.innerHTML = `
        <tr>
            <td colspan="6" class="py-12 px-6 text-center">
                <div class="flex flex-col items-center justify-center">

                    <div class="hr-state-icon ${type === 'loading' ? 'animate-pulse' : ''}">
                        <span class="material-symbols-outlined ${type === 'loading' ? 'animate-spin' : ''}">
                            ${icon}
                        </span>
                    </div>

                    <p class="mt-3 font-title-sm text-title-sm ${type === 'error' ? 'text-error' : 'text-on-surface'}">
                        ${escapeHtml(title)}
                    </p>

                    ${
                        detail
                            ? `<p class="mt-1 max-w-md font-body-sm text-body-sm text-on-surface-variant">
                                ${escapeHtml(detail)}
                               </p>`
                            : ''
                    }

                    ${
                        type === 'error'
                            ? `<button
                                    type="button"
                                    onclick="loadHrPendingData()"
                                    class="hr-action-btn mt-4 inline-flex items-center justify-center gap-2 px-4 py-2 rounded-lg bg-primary text-on-primary font-title-sm"
                               >
                                    <span class="material-symbols-outlined text-[18px]">
                                        refresh
                                    </span>
                                    Coba Lagi
                               </button>`
                            : ''
                    }

                </div>
            </td>
        </tr>
    `;
}

function loadHrPendingData() {
    const badgeElem = document.getElementById('badge-total-hr');

    if (badgeElem) {
        badgeElem.innerHTML = `
            <span class="material-symbols-outlined text-[16px] animate-spin">
                progress_activity
            </span>
            Memuat...
        `;
    }

    setHrQueueState(
        'loading',
        'Memuat antrean verifikasi',
        'Sedang mengambil pengajuan yang menunggu tindakan HR.'
    );

    fetch("{{ route('overtime.hr.pending') }}", {
        headers: {
            'Accept': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        }
    })
    .then(async response => {

        if (!response.ok) {
            throw new Error(`HTTP Error Status: ${response.status}`);
        }

        return response.json();
    })
    .then(res => {

        const tbody = document.getElementById('hr-pending-table-body');

        if (!tbody) {
            return;
        }

        const pendingData = Array.isArray(res.data)
            ? res.data
            : [];

        if (badgeElem) {

            badgeElem.innerHTML = `
                <span class="material-symbols-outlined text-[16px]">
                    pending_actions
                </span>

                ${pendingData.length} Tiket
            `;
        }


        if (pendingData.length === 0) {

            setHrQueueState(
                'empty',
                'Tidak ada antrean verifikasi',
                'Tidak ada pengajuan lembur yang menunggu verifikasi HR saat ini.'
            );

            return;
        }


        tbody.innerHTML = pendingData.map(item => {

            const userName =
                item.user?.name ||
                item.user?.employee?.nama ||
                'Karyawan';


            const employee =
                item.user?.employee ||
                null;


            const deptName =
                item.user?.employee?.division?.nama ||
                item.user?.employee?.department?.name ||
                item.user?.department?.name ||
                'Divisi belum tersedia';


            const bandCode =
                employee?.band_code !== null &&
                employee?.band_code !== undefined
                    ? `Band ${escapeHtml(employee.band_code)}`
                    : null;


            const vpName =
                item.vp_approver?.name ||
                'VP Divisi';


            const estHours =
                item.estimated_hours ||
                '0';


            const dateStr =
                item.overtime_date ||
                '-';


            const startTime =
                item.start_time ||
                '';


            const endTime =
                item.end_time ||
                '';


            const reasonStr =
                item.reason ||
                '-';


            return `
                <tr>

                    <td class="py-4 px-space-base align-top">

                        <div class="font-title-sm text-title-sm text-primary">
                            ${escapeHtml(userName)}
                        </div>

                        <div class="mt-1 flex flex-wrap items-center gap-1.5">

                            <span class="font-body-sm text-body-sm text-on-surface-variant">
                                ${escapeHtml(deptName)}
                            </span>

                            ${
                                bandCode
                                    ? `
                                        <span class="hr-status-badge bg-primary-container/30 text-primary">
                                            ${bandCode}
                                        </span>
                                      `
                                    : ''
                            }

                        </div>

                    </td>


                    <td class="py-4 px-space-base align-top">

                        <div class="font-title-sm text-title-sm text-on-surface">
                            ${escapeHtml(dateStr)}
                        </div>

                        <div class="mt-1 inline-flex items-center gap-1.5 font-body-sm text-body-sm text-on-surface-variant">

                            <span class="material-symbols-outlined text-[16px]">
                                schedule
                            </span>

                            ${escapeHtml(startTime)}
                            —
                            ${escapeHtml(endTime)}
                            WIB

                        </div>

                    </td>


                    <td class="py-4 px-space-base align-top">

                        <div class="font-title-sm text-title-sm text-secondary team-number">
                            ${escapeHtml(estHours)}
                        </div>

                        <div class="mt-1 font-label-sm text-label-sm text-on-surface-variant">
                            Jam
                        </div>

                    </td>


                    <td class="py-4 px-space-base align-top">

                        <span
                            class="hr-status-badge bg-secondary-container/50 text-secondary"
                            title="Disetujui oleh ${escapeHtml(vpName)}"
                        >

                            <span class="material-symbols-outlined text-[15px]">
                                check_circle
                            </span>

                            Approved VP

                        </span>

                        <div class="mt-1 font-label-sm text-label-sm text-on-surface-variant">
                            ${escapeHtml(vpName)}
                        </div>

                    </td>


                    <td class="py-4 px-space-base align-top">

                        <div
                            class="hr-reason text-body-sm text-body-sm text-on-surface-variant"
                            title="${escapeHtml(reasonStr)}"
                        >
                            ${escapeHtml(reasonStr)}
                        </div>

                    </td>


                    <td class="py-4 px-space-base align-top">

                        <div class="flex flex-col xl:flex-row items-stretch xl:items-center justify-end gap-2 min-w-[220px]">

                            <button
                                type="button"
                                onclick="verifyHR('${escapeHtml(item.id)}', 'RETURN_TO_VP')"
                                class="hr-action-btn inline-flex items-center justify-center gap-2 px-3 py-2 rounded-lg border border-amber-300 bg-amber-50 text-amber-900 font-title-sm hover:bg-amber-100"
                            >

                                <span class="material-symbols-outlined text-[18px]">
                                    undo
                                </span>

                                Kembalikan

                            </button>


                            <button
                                type="button"
                                onclick="verifyHR('${escapeHtml(item.id)}', 'VERIFY')"
                                class="hr-action-btn inline-flex items-center justify-center gap-2 px-3 py-2 rounded-lg bg-primary text-on-primary font-title-sm hover:opacity-90"
                            >

                                <span class="material-symbols-outlined text-[18px]">
                                    verified
                                </span>

                                Verifikasi

                            </button>

                        </div>

                    </td>

                </tr>
            `;
        }).join('');

    })
    .catch(err => {

        console.error('Error fetching HR pending data:', err);

        if (badgeElem) {

            badgeElem.innerHTML = `
                <span class="material-symbols-outlined text-[16px]">
                    error
                </span>
                Gagal Memuat
            `;
        }

        setHrQueueState(
            'error',
            'Gagal memuat antrean verifikasi',
            err.message || 'Terjadi kesalahan saat mengambil data.'
        );
    });
}


function verifyHR(id, action) {

    let notes = null;


    if (action === 'RETURN_TO_VP') {

        notes = prompt(
            'Jelaskan anomaly/data yang harus diperiksa ulang oleh VP:',
            ''
        );

        if (notes === null) {
            return;
        }

        if (!notes.trim()) {

            alert(
                'Alasan pengembalian wajib diisi.'
            );

            return;
        }

    } else {

        const confirmed = confirm(
            'Konfirmasi verifikasi akhir untuk pengajuan lembur ini?'
        );

        if (!confirmed) {
            return;
        }
    }


    const buttons = document.querySelectorAll(
        `button[onclick*="verifyHR('${id}'"]`
    );

    buttons.forEach(button => {
        button.disabled = true;
        button.classList.add('opacity-60', 'cursor-not-allowed');
    });


    fetch(`/overtime/hr/verify/${id}`, {

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
    })
    .then(async response => {

        const data = await response
            .json()
            .catch(() => ({}));

        if (!response.ok) {

            throw new Error(
                data.message ||
                'Proses HR gagal.'
            );
        }

        return data;
    })
    .then(data => {

        alert(
            data.message ||
            (
                action === 'RETURN_TO_VP'
                    ? 'Pengajuan dikembalikan ke VP.'
                    : 'Pengajuan berhasil diverifikasi.'
            )
        );

        loadHrPendingData();
    })
    .catch(err => {

        alert(
            err.message ||
            'Terjadi kesalahan saat memproses pengajuan.'
        );

        loadHrPendingData();
    });
}
</script>

@endsection
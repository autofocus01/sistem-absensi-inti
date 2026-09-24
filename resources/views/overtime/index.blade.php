@extends('layouts.absensi')

@section('title', 'Pengajuan Lembur — PT. INTI')

@section('content')
<div class="inti-page">

    <div class="inti-page-header">
        <div>
            <div class="inti-eyebrow">LAYANAN LEMBUR</div>

            <h1 class="inti-title">
                Pengajuan Jam Lembur
            </h1>

            <p class="inti-subtitle">
                Ajukan lembur untuk diproses melalui Karyawan → VP Divisi → HR.
            </p>
        </div>

        <span class="inti-badge inti-badge-info">
            2 tahap otorisasi + verifikasi HR
        </span>
    </div>

    @php
        $employee = auth()->user()?->employee;
        $bandCode = $employee?->band_code;

        $overtimeEligible = in_array(
            (int) $bandCode,
            [2, 3],
            true
        );
    @endphp

    @if (! $employee)
        <div class="inti-card inti-card-pad">
            <div class="inti-alert inti-alert-danger">
                Akun Anda belum terhubung dengan data karyawan.
                Silakan hubungi HR Administrator.
            </div>
        </div>

    @elseif (! $overtimeEligible)

        <div class="inti-card inti-card-pad">

            <div class="inti-section-title">
                Pengajuan Lembur
            </div>

            <div
                class="inti-alert inti-alert-warning"
                style="margin-top:1rem"
            >
                <strong>Pengajuan lembur tidak tersedia.</strong>

                <div style="margin-top:.4rem">
                    Pengajuan lembur hanya tersedia untuk karyawan
                    dengan <strong>Band Code 2 atau 3</strong>.
                </div>

                <div style="margin-top:.4rem">
                    Band Code Anda:
                    <strong>
                        {{ $bandCode !== null ? $bandCode : 'Belum terdaftar' }}
                    </strong>
                </div>
            </div>

            <div
                class="inti-subtitle"
                style="margin-top:1rem"
            >
                Jika Band Code Anda belum sesuai dengan data kepegawaian,
                silakan hubungi HR Administrator untuk melakukan koreksi
                data master karyawan.
            </div>

        </div>

    @else

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

            <div class="lg:col-span-8 inti-card inti-card-pad">

                <div class="inti-section-title">
                    Buat Pengajuan Baru
                </div>

                <div
                    class="inti-subtitle"
                    style="margin-bottom:1.25rem"
                >
                    Data presensi aktual tetap menjadi sumber validasi sistem.
                </div>

                <div
                    class="inti-alert inti-alert-info"
                    style="margin-bottom:1rem"
                >
                    Band Code Anda:
                    <strong>{{ $bandCode }}</strong>.
                    Anda memenuhi syarat untuk mengajukan lembur.
                </div>

                <form id="overtime-form">

                    @csrf

                    <input
                        type="hidden"
                        id="submission_mode"
                        name="submission_mode"
                        value="PRE_SUBMITTED"
                    >

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                        <div>
                            <label
                                class="inti-label"
                                for="overtime_date"
                            >
                                Tanggal Lembur *
                            </label>

                            <input
                                class="inti-input"
                                type="date"
                                id="overtime_date"
                                name="overtime_date"
                                required
                            >
                        </div>

                        <div>
                            <label
                                class="inti-label"
                                for="category"
                            >
                                Kategori
                            </label>

                            <select
                                class="inti-input"
                                id="category"
                                name="category"
                            >
                                <option value="hari_kerja">
                                    Hari Kerja
                                </option>

                                <option value="akhir_pekan">
                                    Akhir Pekan / Libur
                                </option>
                            </select>
                        </div>

                    </div>

                    <div
                        class="grid grid-cols-1 md:grid-cols-3 gap-4"
                        style="margin-top:1rem;padding:1rem;border-radius:12px;background:#eff4ff"
                    >

                        <div>
                            <label
                                class="inti-label"
                                for="start_time"
                            >
                                Jam Mulai
                            </label>

                            <input
                                class="inti-input"
                                type="time"
                                id="start_time"
                                name="start_time"
                                value="16:30"
                            >
                        </div>

                        <div>
                            <label
                                class="inti-label"
                                for="end_time"
                            >
                                Jam Selesai
                            </label>

                            <input
                                class="inti-input"
                                type="time"
                                id="end_time"
                                name="end_time"
                                value="19:30"
                            >
                        </div>

                        <div
                            style="display:grid;place-items:center;background:#fff;border-radius:10px"
                        >
                            <div class="inti-kpi-label">
                                Estimasi
                            </div>

                            <div
                                class="inti-kpi-value"
                                id="calculated_hours"
                                style="font-size:20px"
                            >
                                3.0 Jam
                            </div>
                        </div>

                    </div>

                    <div style="margin-top:1rem">

                        <label
                            class="inti-label"
                            for="reason"
                        >
                            Uraian Tugas / Pekerjaan *
                        </label>

                        <textarea
                            class="inti-input"
                            id="reason"
                            name="reason"
                            rows="4"
                            required
                            placeholder="Jelaskan kebutuhan dan tugas pekerjaan selama lembur..."
                        ></textarea>

                    </div>

                    <label
                        style="
                            display:flex;
                            gap:.6rem;
                            align-items:flex-start;
                            margin-top:1rem;
                            padding:.9rem;
                            border-radius:12px;
                            background:#f8f9ff;
                            font-size:12px;
                            color:#43474d
                        "
                    >

                        <input
                            type="checkbox"
                            id="employee_consent"
                            name="employee_consent"
                            value="1"
                            required
                            style="margin-top:2px"
                        >

                        <span>
                            Saya menyatakan data pengajuan lembur yang saya isi
                            benar dan bersedia diproses sesuai kebijakan perusahaan.
                            Persetujuan ini direkam sebagai evidence transaksi.
                        </span>

                    </label>

                    <div
                        id="form-message"
                        style="margin-top:1rem"
                    ></div>

                    <div
                        style="
                            display:flex;
                            justify-content:flex-end;
                            gap:.6rem;
                            margin-top:1rem
                        "
                    >

                        <button
                            class="inti-btn inti-btn-ghost"
                            type="reset"
                        >
                            Reset
                        </button>

                        <button
                            class="inti-btn inti-btn-primary"
                            id="btn-submit"
                            type="button"
                        >
                            <span class="material-symbols-outlined">
                                send
                            </span>

                            Kirim ke VP
                        </button>

                    </div>

                </form>

            </div>

            <div
                class="lg:col-span-4 inti-card inti-card-pad"
                style="height:max-content"
            >

                <div class="inti-section-title">
                    Alur Proses
                </div>

                <div
                    style="
                        margin-top:1rem;
                        display:grid;
                        gap:1rem
                    "
                >

                    <div>
                        <span class="inti-badge inti-badge-info">
                            01
                        </span>

                        <strong
                            style="
                                display:block;
                                margin-top:.35rem
                            "
                        >
                            Karyawan
                        </strong>

                        <div class="inti-subtitle">
                            Pengajuan + consent evidence.
                        </div>
                    </div>

                    <div>
                        <span class="inti-badge inti-badge-warning">
                            02
                        </span>

                        <strong
                            style="
                                display:block;
                                margin-top:.35rem
                            "
                        >
                            VP Divisi
                        </strong>

                        <div class="inti-subtitle">
                            Setujui atau tolak sesuai division boundary.
                        </div>
                    </div>

                    <div>
                        <span class="inti-badge inti-badge-success">
                            03
                        </span>

                        <strong
                            style="
                                display:block;
                                margin-top:.35rem
                            "
                        >
                            HR
                        </strong>

                        <div class="inti-subtitle">
                            Verifikasi submission yang sudah APPROVED_VP.
                        </div>
                    </div>

                </div>

                <div
                    class="inti-alert inti-alert-info"
                    style="margin-top:1.25rem"
                >
                    Clock Out 16:30 bukan lembur.
                    Sistem tetap menghitung ulang kelayakan
                    berdasarkan presensi aktual.
                </div>

            </div>

        </div>

    @endif

</div>
@endsection

@section('scripts')

@if ($overtimeEligible)

<script>
const s = document.getElementById('start_time');
const e = document.getElementById('end_time');
const c = document.getElementById('calculated_hours');

function updateHours() {
    if (!s.value || !e.value) {
        return;
    }

    const a = s.value.split(':');
    const b = e.value.split(':');

    let minutes =
        (+b[0] * 60 + +b[1]) -
        (+a[0] * 60 + +a[1]);

    if (minutes < 0) {
        c.textContent = '0.0 Jam';
        return;
    }

    c.textContent = `${(minutes / 60).toFixed(1)} Jam`;
}

s.addEventListener('change', updateHours);
e.addEventListener('change', updateHours);

updateHours();

document
    .getElementById('btn-submit')
    .addEventListener('click', async () => {

        const message =
            document.getElementById('form-message');

        const consent =
            document.getElementById('employee_consent');

        if (!consent.checked) {

            message.innerHTML =
                '<div class="inti-alert inti-alert-danger">' +
                'Centang persetujuan karyawan sebelum mengirim pengajuan.' +
                '</div>';

            return;
        }

        const payload = {
            overtime_date:
                document.getElementById('overtime_date').value,

            category:
                document.getElementById('category').value,

            start_time:
                s.value,

            end_time:
                e.value,

            reason:
                document.getElementById('reason').value,

            submission_mode:
                document.getElementById('submission_mode').value,

            employee_consent: true,
        };

        if (
            !payload.overtime_date ||
            !payload.reason
        ) {

            message.innerHTML =
                '<div class="inti-alert inti-alert-danger">' +
                'Tanggal dan uraian tugas wajib diisi.' +
                '</div>';

            return;
        }

        try {

            const response = await fetch(
                "{{ route('overtime.store') }}",
                {
                    method: 'POST',

                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN':
                            '{{ csrf_token() }}'
                    },

                    body: JSON.stringify(payload)
                }
            );

            const data =
                await response.json().catch(() => ({}));

            if (!response.ok) {

                throw new Error(
                    data.message ||
                    Object.values(data.errors || {})
                        .flat()
                        .join('\n') ||
                    'Pengajuan gagal.'
                );
            }

            message.innerHTML =
                '<div class="inti-alert inti-alert-success">' +
                (
                    data.message ||
                    'Pengajuan berhasil dikirim ke VP Divisi.'
                ) +
                '</div>';

            document
                .getElementById('overtime-form')
                .reset();

            document
                .getElementById('submission_mode')
                .value = 'PRE_SUBMITTED';

            updateHours();

        } catch (error) {

            message.innerHTML =
                '<div class="inti-alert inti-alert-danger">' +
                error.message +
                '</div>';
        }
    });
</script>

@endif

@endsection
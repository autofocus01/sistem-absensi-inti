@extends('layouts.absensi')

@section('title', 'Import Master Karyawan')

@section('content')
<div class="inti-page">

    <div class="space-y-6">

        {{-- =====================================================
             HEADER
             ===================================================== --}}
        <section>
            <div class="flex flex-col lg:flex-row lg:items-start lg:justify-between gap-4">

                <div class="min-w-0">
                    <div class="flex flex-wrap items-center gap-2 mb-2">
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full
                                     bg-primary-container text-on-primary
                                     font-label-sm text-label-sm uppercase tracking-wider">
                            <span class="material-symbols-outlined text-[15px]">
                                badge
                            </span>
                            Master Data
                        </span>

                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full
                                     bg-surface-container text-on-surface-variant
                                     font-label-sm text-label-sm">
                            <span class="material-symbols-outlined text-[15px]">
                                admin_panel_settings
                            </span>
                            HR Admin
                        </span>
                    </div>

                    <h1 class="font-headline-lg text-headline-lg text-primary tracking-tight">
                        Import Master Karyawan
                    </h1>

                    <p class="font-body-md text-body-md text-on-surface-variant
                              mt-2 max-w-3xl">
                        Upload data master karyawan dalam format XLSX, XLS, atau CSV.
                        Sistem melakukan validasi dan preview terlebih dahulu sebelum
                        data dapat di-commit ke master karyawan.
                    </p>
                </div>

                <a
                    href="{{ route('employees.index') }}"
                    class="inline-flex items-center justify-center gap-2
                           min-h-10 px-4 rounded-lg
                           bg-surface-container text-on-surface
                           font-title-sm text-title-sm
                           hover:bg-surface-container-high
                           focus:outline-none focus-visible:ring-2
                           focus-visible:ring-primary
                           shrink-0"
                >
                    <span class="material-symbols-outlined text-[18px]">
                        arrow_back
                    </span>
                    Data Karyawan
                </a>

            </div>
        </section>


        {{-- =====================================================
             FLASH RESULT
             ===================================================== --}}
        @if (session('import_result'))

            @php($result = session('import_result'))

            <section>
                <div class="rounded-xl border border-secondary/20
                            bg-secondary/10 p-4 sm:p-5">

                    <div class="flex items-start gap-3">

                        <div class="flex h-10 w-10 shrink-0 items-center justify-center
                                    rounded-lg bg-secondary-container text-secondary">
                            <span class="material-symbols-outlined">
                                check_circle
                            </span>
                        </div>

                        <div class="min-w-0">
                            <div class="font-title-sm text-title-sm text-secondary">
                                Import master karyawan selesai
                            </div>

                            <div class="font-body-sm text-body-sm
                                        text-on-surface-variant mt-1 leading-5">
                                Berhasil:
                                <strong>{{ $result['success'] ?? 0 }}</strong>

                                <span class="mx-1">·</span>

                                CREATE:
                                <strong>{{ $result['created'] ?? 0 }}</strong>

                                <span class="mx-1">·</span>

                                UPDATE:
                                <strong>{{ $result['updated'] ?? 0 }}</strong>

                                @if (!empty($result['errors']))
                                    <span class="mx-1">·</span>

                                    Error:
                                    <strong>{{ count($result['errors']) }}</strong>
                                @endif
                            </div>

                            @if (!empty($result['peringatan']))
                                <div class="mt-2 rounded-lg bg-amber-50 px-3 py-2
                                            font-body-sm text-body-sm text-amber-800">
                                    {{ $result['peringatan'] }}
                                </div>
                            @endif
                        </div>

                    </div>
                </div>
            </section>

        @endif


        {{-- =====================================================
             VALIDATION ERRORS
             ===================================================== --}}
        @if ($errors->any())

            <section>
                <div class="rounded-xl border border-error/20
                            bg-error/10 p-4 sm:p-5">

                    <div class="flex items-start gap-3">

                        <div class="flex h-10 w-10 shrink-0 items-center justify-center
                                    rounded-lg bg-error-container text-error">
                            <span class="material-symbols-outlined">
                                error
                            </span>
                        </div>

                        <div class="min-w-0">

                            <div class="font-title-sm text-title-sm text-error">
                                Import tidak dapat diproses
                            </div>

                            <ul class="mt-2 space-y-1 list-disc list-inside
                                       font-body-sm text-body-sm text-error">

                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach

                            </ul>

                        </div>

                    </div>
                </div>
            </section>

        @endif


        {{-- =====================================================
             WORKFLOW
             ===================================================== --}}
        <section>
            <div class="rounded-xl border border-outline-variant/70
                        bg-surface-container-lowest p-4 sm:p-5">

                <div class="flex flex-col sm:flex-row
                            sm:items-center gap-3 sm:gap-0">

                    <div class="flex items-center gap-3 flex-1">
                        <div class="flex h-9 w-9 items-center justify-center
                                    rounded-full bg-primary-container text-on-primary
                                    font-title-sm">
                            1
                        </div>

                        <div>
                            <div class="font-title-sm text-title-sm text-primary">
                                Upload
                            </div>

                            <div class="font-body-sm text-body-sm text-on-surface-variant">
                                Pilih file sumber
                            </div>
                        </div>
                    </div>

                    <span class="hidden sm:block material-symbols-outlined
                                 text-outline-variant">
                        chevron_right
                    </span>

                    <div class="flex items-center gap-3 flex-1 opacity-65">
                        <div class="flex h-9 w-9 items-center justify-center
                                    rounded-full bg-surface-container
                                    text-on-surface-variant font-title-sm">
                            2
                        </div>

                        <div>
                            <div class="font-title-sm text-title-sm">
                                Preview
                            </div>

                            <div class="font-body-sm text-body-sm text-on-surface-variant">
                                Periksa hasil validasi
                            </div>
                        </div>
                    </div>

                    <span class="hidden sm:block material-symbols-outlined
                                 text-outline-variant">
                        chevron_right
                    </span>

                    <div class="flex items-center gap-3 flex-1 opacity-65">
                        <div class="flex h-9 w-9 items-center justify-center
                                    rounded-full bg-surface-container
                                    text-on-surface-variant font-title-sm">
                            3
                        </div>

                        <div>
                            <div class="font-title-sm text-title-sm">
                                Commit
                            </div>

                            <div class="font-body-sm text-body-sm text-on-surface-variant">
                                Simpan data valid
                            </div>
                        </div>
                    </div>

                </div>

            </div>
        </section>


        {{-- =====================================================
             UPLOAD CARD
             ===================================================== --}}
        <section>
            <div class="rounded-xl border border-outline-variant/70
                        bg-surface-container-lowest shadow-sm overflow-hidden">

                <div class="p-5 sm:p-6 border-b border-outline-variant/60">

                    <div class="flex items-start gap-3">

                        <div class="flex h-11 w-11 shrink-0 items-center justify-center
                                    rounded-xl bg-primary-container text-on-primary">
                            <span class="material-symbols-outlined">
                                upload_file
                            </span>
                        </div>

                        <div>
                            <h2 class="font-headline-sm text-headline-sm text-primary">
                                Upload File Karyawan
                            </h2>

                            <p class="font-body-sm text-body-sm
                                      text-on-surface-variant mt-1">
                                Maksimal ukuran file 10 MB.
                            </p>
                        </div>

                    </div>

                </div>


                <div class="p-5 sm:p-6">

                    <form
                        id="employee-import-form"
                        method="POST"
                        action="{{ route('employees.import.preview') }}"
                        enctype="multipart/form-data"
                        class="space-y-5"
                    >

                        @csrf


                        {{-- Dropzone --}}
                        <div>

                            <label
                                for="employee-import-file"
                                class="block font-title-sm text-title-sm
                                       text-on-surface mb-2"
                            >
                                File Import
                            </label>

                            <label
                                for="employee-import-file"
                                id="employee-import-dropzone"
                                class="group flex flex-col items-center justify-center
                                       min-h-[190px] px-5 py-8
                                       rounded-xl border-2 border-dashed
                                       border-outline-variant
                                       bg-surface-container-low
                                       cursor-pointer
                                       transition-colors
                                       hover:border-primary
                                       hover:bg-surface-container"
                            >

                                <div class="flex h-14 w-14 items-center justify-center
                                            rounded-full bg-primary-container
                                            text-on-primary
                                            transition-transform
                                            group-hover:scale-105">

                                    <span class="material-symbols-outlined text-[28px]">
                                        cloud_upload
                                    </span>

                                </div>

                                <div class="mt-4 text-center">

                                    <div class="font-title-sm text-title-sm text-primary">
                                        Pilih file untuk di-upload
                                    </div>

                                    <div class="font-body-sm text-body-sm
                                                text-on-surface-variant mt-1">
                                        XLSX, XLS, atau CSV
                                    </div>

                                </div>

                                <div
                                    id="employee-import-file-name"
                                    class="hidden mt-3 max-w-full rounded-lg
                                           bg-surface-container-high px-3 py-2
                                           font-body-sm text-body-sm text-primary
                                           text-center break-all"
                                ></div>

                                <input
                                    id="employee-import-file"
                                    type="file"
                                    name="file"
                                    accept=".xlsx,.xls,.csv"
                                    required
                                    class="sr-only"
                                >

                            </label>

                            <p class="font-body-sm text-body-sm
                                      text-on-surface-variant mt-2">
                                Format yang didukung:
                                <strong>XLSX</strong>,
                                <strong>XLS</strong>,
                                <strong>CSV</strong>.
                                Maksimal 10 MB.
                            </p>

                        </div>


                        {{-- Required / optional --}}
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">

                            <div class="rounded-xl bg-surface-container-low p-4 sm:p-5">

                                <div class="flex items-center gap-2 mb-3">
                                    <span class="material-symbols-outlined
                                                 text-primary text-[19px]">
                                        required
                                    </span>

                                    <h3 class="font-title-sm text-title-sm text-primary">
                                        Kolom wajib
                                    </h3>
                                </div>

                                <ul class="space-y-2 font-body-sm text-body-sm
                                           text-on-surface-variant">

                                    <li class="flex items-center gap-2">
                                        <span class="material-symbols-outlined
                                                     text-[16px] text-secondary">
                                            check
                                        </span>
                                        NIPEG / NIP
                                    </li>

                                    <li class="flex items-center gap-2">
                                        <span class="material-symbols-outlined
                                                     text-[16px] text-secondary">
                                            check
                                        </span>
                                        Nama
                                    </li>

                                    <li class="flex items-center gap-2">
                                        <span class="material-symbols-outlined
                                                     text-[16px] text-secondary">
                                            check
                                        </span>
                                        Divisi
                                    </li>

                                </ul>

                            </div>


                            <div class="rounded-xl bg-surface-container-low p-4 sm:p-5">

                                <div class="flex items-center gap-2 mb-3">
                                    <span class="material-symbols-outlined
                                                 text-primary text-[19px]">
                                        tune
                                    </span>

                                    <h3 class="font-title-sm text-title-sm text-primary">
                                        Kolom opsional
                                    </h3>
                                </div>

                                <ul class="space-y-2 font-body-sm text-body-sm
                                           text-on-surface-variant">

                                    <li class="flex items-center gap-2">
                                        <span class="material-symbols-outlined text-[16px]">
                                            chevron_right
                                        </span>
                                        Jenis Kelamin
                                    </li>

                                    <li class="flex items-center gap-2">
                                        <span class="material-symbols-outlined text-[16px]">
                                            chevron_right
                                        </span>
                                        Jabatan
                                    </li>

                                    <li class="flex items-center gap-2">
                                        <span class="material-symbols-outlined text-[16px]">
                                            chevron_right
                                        </span>
                                        No HP
                                    </li>

                                    <li class="flex items-center gap-2">
                                        <span class="material-symbols-outlined text-[16px]">
                                            chevron_right
                                        </span>
                                        Alamat
                                    </li>

                                </ul>

                            </div>

                        </div>


                        {{-- Workflow information --}}
                        <div class="rounded-xl border border-outline-variant
                                    bg-surface-container-low p-4">

                            <div class="flex items-start gap-3">

                                <span class="material-symbols-outlined
                                             text-primary text-[20px] shrink-0">
                                    info
                                </span>

                                <div class="min-w-0">

                                    <div class="font-title-sm text-title-sm text-primary">
                                        Alur import
                                    </div>

                                    <p class="font-body-sm text-body-sm
                                              text-on-surface-variant mt-1 leading-5">
                                        Upload → Preview → Validasi → CREATE/UPDATE
                                        → Commit.
                                        Data yang gagal validasi tidak langsung
                                        masuk ke master karyawan.
                                    </p>

                                </div>

                            </div>

                        </div>


                        {{-- Actions --}}
                        <div class="flex flex-col-reverse sm:flex-row
                                    sm:items-center sm:justify-between gap-3 pt-1">

                            <a
                                href="{{ route('employees.index') }}"
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
                                <span class="material-symbols-outlined text-[18px]">
                                    arrow_back
                                </span>
                                Kembali
                            </a>

                            <button
                                id="employee-import-submit"
                                type="submit"
                                class="inline-flex items-center justify-center gap-2
                                       min-h-10 px-5 rounded-lg
                                       inti-btn inti-btn-primary
                                       font-title-sm text-title-sm
                                       focus:outline-none
                                       focus-visible:ring-2
                                       focus-visible:ring-primary
                                       disabled:opacity-60
                                       disabled:cursor-not-allowed"
                            >
                                <span
                                    id="employee-import-submit-icon"
                                    class="material-symbols-outlined text-[18px]"
                                >
                                    preview
                                </span>

                                <span id="employee-import-submit-text">
                                    Preview Import
                                </span>
                            </button>

                        </div>

                    </form>

                </div>

            </div>
        </section>


        {{-- =====================================================
             SECURITY / PROCESS CARDS
             ===================================================== --}}
        <section class="grid grid-cols-1 md:grid-cols-3 gap-4">

            <div class="rounded-xl border border-outline-variant/70
                        bg-surface-container-lowest p-5">

                <span class="material-symbols-outlined text-primary text-[24px]">
                    fact_check
                </span>

                <h3 class="font-title-sm text-title-sm text-primary mt-3">
                    Preview Dulu
                </h3>

                <p class="font-body-sm text-body-sm
                          text-on-surface-variant mt-1 leading-5">
                    File tidak langsung mengubah master sebelum melewati
                    tahap preview dan commit.
                </p>

            </div>


            <div class="rounded-xl border border-outline-variant/70
                        bg-surface-container-lowest p-5">

                <span class="material-symbols-outlined text-primary text-[24px]">
                    sync_alt
                </span>

                <h3 class="font-title-sm text-title-sm text-primary mt-3">
                    CREATE / UPDATE
                </h3>

                <p class="font-body-sm text-body-sm
                          text-on-surface-variant mt-1 leading-5">
                    NIPEG digunakan sebagai identitas untuk menentukan
                    apakah data dibuat atau diperbarui.
                </p>

            </div>


            <div class="rounded-xl border border-outline-variant/70
                        bg-surface-container-lowest p-5">

                <span class="material-symbols-outlined text-primary text-[24px]">
                    history
                </span>

                <h3 class="font-title-sm text-title-sm text-primary mt-3">
                    Audit Trail
                </h3>

                <p class="font-body-sm text-body-sm
                          text-on-surface-variant mt-1 leading-5">
                    Perubahan master dicatat untuk kebutuhan pemeriksaan
                    dan governance HR.
                </p>

            </div>

        </section>

        {{-- =====================================================
             RIWAYAT IMPOR (PROVENANCE)
             ===================================================== --}}
        <section class="inti-card overflow-hidden">
            <div class="border-b border-slate-200 px-5 py-4">
                <h2 class="text-base font-semibold text-slate-900">
                    Riwayat Impor
                </h2>

                <p class="mt-1 text-xs leading-5 text-slate-500">
                    Jejak file master karyawan yang pernah di-commit — nama file asli,
                    operator, waktu, dan jumlah baris.
                </p>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm min-w-[700px]">
                    <thead>
                        <tr class="bg-slate-50 text-xs uppercase tracking-wider text-slate-500">
                            <th class="py-3 px-5">File</th>
                            <th class="py-3 px-5">Operator</th>
                            <th class="py-3 px-5">Waktu</th>
                            <th class="py-3 px-5 text-right">Baris Masuk</th>
                            <th class="py-3 px-5 text-right">Gagal</th>
                            <th class="py-3 px-5">Status</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-slate-100">
                        @forelse(($batches ?? collect()) as $batch)
                            <tr>
                                <td class="py-3 px-5 font-medium text-slate-900">
                                    {{ $batch->nama_file }}
                                </td>

                                <td class="py-3 px-5 text-slate-600">
                                    {{ $batch->user?->name ?? '-' }}
                                </td>

                                <td class="py-3 px-5 text-slate-600">
                                    {{ $batch->created_at->format('d M Y H:i') }}
                                </td>

                                <td class="py-3 px-5 text-right">{{ $batch->berhasil }}</td>
                                <td class="py-3 px-5 text-right">{{ $batch->gagal }}</td>

                                <td class="py-3 px-5">
                                    @if ($batch->duplikat)
                                        <span class="inline-flex rounded-full bg-amber-100 px-2 py-0.5 text-xs font-semibold text-amber-800">
                                            File terulang
                                        </span>
                                    @elseif ($batch->gagal > 0)
                                        <span class="inline-flex rounded-full bg-amber-100 px-2 py-0.5 text-xs font-semibold text-amber-800">
                                            Sebagian
                                        </span>
                                    @else
                                        <span class="inline-flex rounded-full bg-emerald-100 px-2 py-0.5 text-xs font-semibold text-emerald-800">
                                            Bersih
                                        </span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-6 text-center text-sm text-slate-500">
                                    Belum ada impor yang di-commit.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>

    </div>
</div>
@endsection


@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {

    const input = document.getElementById('employee-import-file');
    const dropzone = document.getElementById('employee-import-dropzone');
    const fileName = document.getElementById('employee-import-file-name');
    const form = document.getElementById('employee-import-form');
    const submit = document.getElementById('employee-import-submit');
    const submitText = document.getElementById('employee-import-submit-text');
    const submitIcon = document.getElementById('employee-import-submit-icon');

    if (!input || !form) {
        return;
    }

    const maxSize = 10 * 1024 * 1024;

    input.addEventListener('change', function () {

        const file = input.files?.[0];

        if (!file) {
            fileName.textContent = '';
            fileName.classList.add('hidden');
            return;
        }

        if (file.size > maxSize) {
            fileName.textContent = 'Ukuran file melebihi batas 10 MB.';
            fileName.classList.remove('hidden');
            fileName.classList.add('text-error');
            input.value = '';
            return;
        }

        fileName.classList.remove('hidden', 'text-error');
        fileName.textContent =
            `${file.name} · ${(file.size / 1024 / 1024).toFixed(2)} MB`;
    });


    /*
     * Drag & drop hanya visual/input enhancement.
     * Tidak mengubah proses backend.
     */
    ['dragenter', 'dragover'].forEach(function (eventName) {
        dropzone?.addEventListener(eventName, function (event) {
            event.preventDefault();
            dropzone.classList.add(
                'border-primary',
                'bg-surface-container'
            );
        });
    });

    ['dragleave', 'drop'].forEach(function (eventName) {
        dropzone?.addEventListener(eventName, function (event) {
            event.preventDefault();
            dropzone.classList.remove(
                'border-primary',
                'bg-surface-container'
            );
        });
    });


    form.addEventListener('submit', function () {

        if (!input.files?.length) {
            return;
        }

        submit.disabled = true;

        submitIcon.textContent = 'progress_activity';
        submitIcon.classList.add('animate-spin');

        submitText.textContent = 'Memvalidasi...';
    });

});
</script>
@endsection
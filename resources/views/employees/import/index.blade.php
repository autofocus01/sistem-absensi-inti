@extends('layouts.absensi')

@section('title', 'Import Master Karyawan')

@section('content')
<div class="inti-page">
<div class="max-w-6xl mx-auto space-y-6">

    {{-- Header --}}
    <div class="flex flex-col md:flex-row md:items-start md:justify-between gap-4">
        <div>
            <span class="font-label-sm text-label-sm text-on-surface-variant uppercase tracking-wider">
                MASTER DATA
            </span>

            <h1 class="font-headline-lg text-headline-lg text-primary tracking-tight mt-1">
                Import Master Karyawan
            </h1>

            <p class="font-body-md text-body-md text-on-surface-variant mt-1 max-w-3xl">
                Upload data master karyawan dalam format XLSX, XLS, atau CSV.
                Sistem akan melakukan validasi terlebih dahulu sebelum data disimpan.
            </p>
        </div>

        <div class="inline-flex items-center gap-2 px-3 py-2 rounded-lg inti-btn inti-btn-primary font-title-sm whitespace-nowrap">
            <span class="material-symbols-outlined text-[18px]">badge</span>
            HR Admin
        </div>
    </div>

    {{-- Flash hasil import --}}
    @if (session('import_result'))
        @php($result = session('import_result'))

        <div class="rounded-xl border border-secondary/20 bg-secondary/10 p-4">
            <div class="flex items-start gap-3">
                <span class="material-symbols-outlined text-secondary text-[22px]">check_circle</span>

                <div>
                    <div class="font-title-sm text-title-sm text-secondary">
                        Import master karyawan selesai
                    </div>

                    <div class="font-body-sm text-body-sm text-on-surface-variant mt-1">
                        Berhasil: {{ $result['success'] ?? 0 }}
                        · CREATE: {{ $result['created'] ?? 0 }}
                        · UPDATE: {{ $result['updated'] ?? 0 }}
                        @if (!empty($result['errors']))
                            · Error: {{ count($result['errors']) }}
                        @endif
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- Error --}}
    @if ($errors->any())
        <div class="rounded-xl border border-error/20 bg-error/10 p-4">
            <div class="flex items-start gap-3">
                <span class="material-symbols-outlined text-error text-[22px]">error</span>

                <div class="font-body-md text-body-md text-error">
                    <div class="font-title-sm text-title-sm mb-1">
                        Import tidak dapat diproses
                    </div>

                    <ul class="list-disc list-inside space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    @endif

    {{-- Upload --}}
    <div class="inti-card inti-card-pad p-6">
        <div class="flex items-center gap-3 mb-5">
            <div class="w-10 h-10 rounded-lg inti-btn inti-btn-primary flex items-center justify-center">
                <span class="material-symbols-outlined">upload_file</span>
            </div>

            <div>
                <h2 class="font-headline-sm text-headline-sm text-primary">
                    Upload File Karyawan
                </h2>

                <p class="font-body-sm text-body-sm text-on-surface-variant">
                    Maksimal 10 MB.
                </p>
            </div>
        </div>

        <form
            method="POST"
            action="{{ route('employees.import.preview') }}"
            enctype="multipart/form-data"
            class="space-y-5"
        >
            @csrf

            <div>
                <label
                    for="employee-import-file"
                    class="block font-title-sm text-title-sm text-on-surface mb-2"
                >
                    File Import
                </label>

                <input
                    id="employee-import-file"
                    type="file"
                    name="file"
                    accept=".xlsx,.xls,.csv"
                    required
                    class="block w-full rounded-lg border border-outline-variant bg-surface-container-lowest text-on-surface file:mr-4 file:border-0 file:bg-primary-container file:px-4 file:py-2.5 file:text-on-primary file:font-title-sm file:cursor-pointer hover:file:bg-primary"
                >

                <p class="font-body-sm text-body-sm text-on-surface-variant mt-2">
                    Format yang didukung: XLSX, XLS, CSV.
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="rounded-lg bg-surface-container-low p-4">
                    <div class="font-title-sm text-title-sm text-primary mb-2">
                        Kolom wajib
                    </div>

                    <ul class="font-body-sm text-body-sm text-on-surface-variant space-y-1">
                        <li>• NIPEG / NIP</li>
                        <li>• Nama</li>
                        <li>• Divisi</li>
                    </ul>
                </div>

                <div class="rounded-lg bg-surface-container-low p-4">
                    <div class="font-title-sm text-title-sm text-primary mb-2">
                        Kolom opsional
                    </div>

                    <ul class="font-body-sm text-body-sm text-on-surface-variant space-y-1">
                        <li>• Jenis Kelamin</li>
                        <li>• Jabatan</li>
                        <li>• No HP</li>
                        <li>• Alamat</li>
                    </ul>
                </div>
            </div>

            <div class="rounded-lg border border-outline-variant bg-surface-container-low p-4">
                <div class="flex items-start gap-3">
                    <span class="material-symbols-outlined text-primary text-[20px]">info</span>

                    <div class="font-body-sm text-body-sm text-on-surface-variant">
                        <strong class="text-on-surface">Alur import:</strong>
                        Upload → Preview → Validasi → CREATE/UPDATE → Commit.
                        Data yang gagal validasi tidak boleh langsung masuk ke master.
                    </div>
                </div>
            </div>

            <div class="flex flex-wrap items-center gap-2">
                <button
                    type="submit"
                    class="inline-flex items-center gap-2 px-5 py-2.5 rounded-lg inti-btn inti-btn-primary font-title-sm hover:bg-primary transition-colors"
                >
                    <span class="material-symbols-outlined text-[18px]">preview</span>
                    Preview Import
                </button>

                <a
                    href="{{ route('employees.index') }}"
                    class="inline-flex items-center gap-2 px-5 py-2.5 rounded-lg bg-surface-container text-on-surface font-title-sm hover:bg-surface-container-high transition-colors"
                >
                    <span class="material-symbols-outlined text-[18px]">arrow_back</span>
                    Kembali ke Data Karyawan
                </a>
            </div>
        </form>
    </div>

    {{-- Informasi keamanan --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">

        <div class="inti-card inti-card-pad p-5">
            <span class="material-symbols-outlined text-primary">fact_check</span>
            <h3 class="font-title-sm text-title-sm text-primary mt-2">
                Preview Dulu
            </h3>
            <p class="font-body-sm text-body-sm text-on-surface-variant mt-1">
                File tidak langsung mengubah master sebelum preview dan commit.
            </p>
        </div>

        <div class="inti-card inti-card-pad p-5">
            <span class="material-symbols-outlined text-primary">sync_alt</span>
            <h3 class="font-title-sm text-title-sm text-primary mt-2">
                CREATE / UPDATE
            </h3>
            <p class="font-body-sm text-body-sm text-on-surface-variant mt-1">
                NIPEG digunakan sebagai identitas untuk menentukan data baru atau pembaruan.
            </p>
        </div>

        <div class="inti-card inti-card-pad p-5">
            <span class="material-symbols-outlined text-primary">history</span>
            <h3 class="font-title-sm text-title-sm text-primary mt-2">
                Audit Trail
            </h3>
            <p class="font-body-sm text-body-sm text-on-surface-variant mt-1">
                Perubahan master dicatat untuk kebutuhan pemeriksaan HR.
            </p>
        </div>

    </div>
</div>
</div>

@endsection

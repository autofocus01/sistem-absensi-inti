@extends('layouts.absensi')

@section('title', 'Preview Import Karyawan')

@section('content')
<div class="inti-page">
@php
    $errorsPreview = $preview['errors'] ?? [];
    $rowsPreview = $preview['rows'] ?? [];
    $headersPreview = $preview['headers'] ?? [];
    $valid = (int) ($preview['valid'] ?? 0);
    $totalRows = (int) ($preview['total_rows'] ?? 0);
@endphp

<div class="inti-page">

<div class="space-y-6">

    <div class="flex flex-col md:flex-row md:items-start md:justify-between gap-4">
        <div>
            <span class="font-label-sm text-label-sm text-on-surface-variant uppercase tracking-wider">
                MASTER DATA
            </span>

            <h1 class="font-headline-lg text-headline-lg text-primary tracking-tight mt-1">
                Preview Import Karyawan
            </h1>

            <p class="font-body-md text-body-md text-on-surface-variant mt-1">
                Periksa hasil validasi sebelum melakukan commit ke master karyawan.
            </p>
        </div>

        <a
            href="{{ route('employees.import') }}"
            class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-surface-container text-on-surface font-title-sm"
        >
            <span class="material-symbols-outlined text-[18px]">arrow_back</span>
            Upload Ulang
        </a>
    </div>

    {{-- Summary --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="inti-card inti-card-pad p-5">
            <div class="font-label-sm text-label-sm text-on-surface-variant uppercase">
                Total Baris
            </div>
            <div class="font-headline-md text-headline-md text-primary mt-1">
                {{ $totalRows }}
            </div>
        </div>

        <div class="inti-card inti-card-pad p-5">
            <div class="font-label-sm text-label-sm text-on-surface-variant uppercase">
                Valid
            </div>
            <div class="font-headline-md text-headline-md text-secondary mt-1">
                {{ $valid }}
            </div>
        </div>

        <div class="inti-card inti-card-pad p-5">
            <div class="font-label-sm text-label-sm text-on-surface-variant uppercase">
                Bermasalah
            </div>
            <div class="font-headline-md text-headline-md text-error mt-1">
                {{ count($errorsPreview) }}
            </div>
        </div>
    </div>

    {{-- Errors --}}
    @if (count($errorsPreview) > 0)
        <div class="rounded-xl border border-error/20 bg-error/10 p-5">
            <div class="flex items-start gap-3">
                <span class="material-symbols-outlined text-error">error</span>

                <div class="w-full">
                    <h2 class="font-title-sm text-title-sm text-error">
                        Baris bermasalah
                    </h2>

                    <div class="mt-3 space-y-2">
                        @foreach ($errorsPreview as $error)
                            <div class="rounded-lg bg-surface-container-lowest p-3 font-body-sm text-body-sm">
                                <strong>Baris {{ $error['row'] ?? '-' }}</strong>
                                — {{ $error['reason'] ?? 'Data tidak valid.' }}
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- Optional normalized preview rows --}}
    @if (count($rowsPreview) > 0)
        <div class="inti-card inti-card-pad overflow-x-auto">
            <div class="p-5 border-b border-surface-container-low">
                <h2 class="font-headline-sm text-headline-sm text-primary">
                    Data yang akan diproses
                </h2>
            </div>

            <table class="w-full min-w-[900px] text-left">
                <thead>
                    <tr class="bg-surface-container-low font-label-sm text-label-sm text-on-surface-variant uppercase">
                        <th class="px-4 py-3">NIPEG</th>
                        <th class="px-4 py-3">Nama</th>
                        <th class="px-4 py-3">Divisi</th>
                        <th class="px-4 py-3">Aksi</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-surface-container-low">
                    @foreach ($rowsPreview as $row)
                        <tr>
                            <td class="px-4 py-3 font-title-sm text-primary">
                                {{ $row['nipeg'] ?? '-' }}
                            </td>
                            <td class="px-4 py-3">
                                {{ $row['nama'] ?? '-' }}
                            </td>
                            <td class="px-4 py-3">
                                {{ $row['divisi'] ?? '-' }}
                            </td>
                            <td class="px-4 py-3">
                                @php($action = strtoupper((string) ($row['action'] ?? 'VALID')))

                                <span class="inline-flex px-2 py-1 rounded-full font-label-sm
                                    {{ $action === 'CREATE'
                                        ? 'bg-secondary-container text-secondary'
                                        : ($action === 'UPDATE'
                                            ? 'bg-surface-container text-primary'
                                            : 'bg-surface-container text-on-surface-variant') }}">
                                    {{ $action }}
                                </span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif

    {{-- Commit --}}
    <div class="inti-card inti-card-pad p-5">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">

            <div>
                <h2 class="font-title-sm text-title-sm text-primary">
                    Siap diproses?
                </h2>

                <p class="font-body-sm text-body-sm text-on-surface-variant mt-1">
                    Commit hanya menyimpan data yang lolos validasi.
                    Relasi <code>user_id</code> yang sudah ada tidak diambil alih oleh import.
                </p>
            </div>

            @if ($valid > 0)
                <form method="POST" action="{{ route('employees.import.commit') }}">
                    @csrf

                    <button
                        type="submit"
                        class="inline-flex items-center gap-2 px-5 py-2.5 rounded-lg inti-btn inti-btn-primary font-title-sm hover:bg-primary transition-colors"
                        onclick="return confirm('Commit {{ $valid }} baris valid ke master karyawan?')"
                    >
                        <span class="material-symbols-outlined text-[18px]">task_alt</span>
                        Commit {{ $valid }} Baris Valid
                    </button>
                </form>
            @else
                <span class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-error/10 text-error font-title-sm">
                    <span class="material-symbols-outlined text-[18px]">block</span>
                    Tidak ada baris valid
                </span>
            @endif

        </div>
    </div>
</div>
</div>

@endsection

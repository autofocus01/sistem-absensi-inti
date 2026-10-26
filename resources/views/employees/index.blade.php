@extends('layouts.absensi')


@section('title', 'Data Karyawan')

@section('content')
<div class="inti-page p1-hr-governance">

    {{-- =========================================================
         PAGE HEADER
         ========================================================= --}}
    <section class="mb-6">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">

            <div class="min-w-0">
                <div class="flex items-center gap-2 mb-2">
                    <span class="material-symbols-outlined text-primary text-[20px]">
                        badge
                    </span>

                    <span class="inti-eyebrow">
                        DATA MASTER • SDM PT. INTI
                    </span>
                </div>

                <h1 class="inti-title">
                    Data Karyawan
                </h1>

                <p class="inti-subtitle max-w-2xl">
                    Kelola dan monitor data master karyawan, divisi, band, serta keterhubungan akun pengguna.
                </p>
            </div>

            <div class="flex shrink-0">
                <a
                    href="{{ route('employees.create') }}"
                    class="inline-flex min-h-11 items-center justify-center gap-2 rounded-xl bg-primary-container px-4 py-2.5 text-sm font-bold text-on-primary shadow-sm transition-all hover:bg-primary focus:outline-none focus-visible:ring-2 focus-visible:ring-primary focus-visible:ring-offset-2"
                >
                    <span class="material-symbols-outlined text-[20px]">
                        person_add
                    </span>
                    <span>Tambah Karyawan</span>
                </a>
            </div>

        </div>
    </section>


    {{-- =========================================================
         FLASH / STATUS
         ========================================================= --}}
    @if (session('status'))
        <div
            role="status"
            class="mb-5 flex items-start gap-3 rounded-xl border border-secondary/20 bg-secondary/10 px-4 py-3.5"
        >
            <span class="material-symbols-outlined mt-0.5 shrink-0 text-[20px] text-secondary">
                check_circle
            </span>

            <div class="min-w-0">
                <p class="text-sm font-bold text-secondary">
                    Berhasil
                </p>

                <p class="mt-0.5 text-sm leading-5 text-on-surface">
                    {{ session('status') }}
                </p>
            </div>
        </div>
    @endif


    @if (session('account-created'))
        <div
            role="status"
            class="mb-5 rounded-xl border border-secondary/20 bg-secondary/10 p-4"
        >
            <div class="flex items-start gap-3">
                <span class="material-symbols-outlined mt-0.5 shrink-0 text-[20px] text-secondary">
                    person_add
                </span>

                <div class="min-w-0">
                    <p class="text-sm font-bold text-secondary">
                        Akun berhasil dibuat
                    </p>

                    <p class="mt-1 text-sm leading-6 text-on-surface">
                        Akun berhasil dibuat untuk
                        <strong>{{ session('account-created.employee') }}</strong>.
                    </p>

                    <div class="mt-3 grid gap-2 sm:grid-cols-2">
                        <div class="rounded-lg bg-surface-container-low px-3 py-2">
                            <div class="text-[11px] font-bold uppercase tracking-wide text-on-surface-variant">
                                Email
                            </div>
                            <div class="mt-1 break-all text-sm font-semibold text-on-surface">
                                {{ session('account-created.email') }}
                            </div>
                        </div>

                        <div class="rounded-lg bg-surface-container-low px-3 py-2">
                            <div class="text-[11px] font-bold uppercase tracking-wide text-on-surface-variant">
                                Password sementara
                            </div>
                            <div class="mt-1 break-all font-mono text-sm font-semibold text-on-surface">
                                {{ session('account-created.temporary_password') }}
                            </div>
                        </div>
                    </div>

                    <p class="mt-3 text-xs leading-5 text-on-surface-variant">
                        Sampaikan kredensial ini secara aman. Pengguna wajib mengganti password saat login pertama.
                    </p>
                </div>
            </div>
        </div>
    @endif


    @if (session('account-password-reset'))
        <div
            role="status"
            class="mb-5 rounded-xl border border-secondary/20 bg-secondary/10 p-4"
        >
            <div class="flex items-start gap-3">
                <span class="material-symbols-outlined mt-0.5 shrink-0 text-[20px] text-secondary">
                    lock_reset
                </span>

                <div class="min-w-0">
                    <p class="text-sm font-bold text-secondary">
                        Password sementara berhasil dibuat ulang
                    </p>

                    <p class="mt-1 text-sm leading-6 text-on-surface">
                        Password untuk
                        <strong>{{ session('account-password-reset.employee') }}</strong>
                        berhasil dibuat ulang.
                    </p>

                    <div class="mt-3 grid gap-2 sm:grid-cols-2">
                        <div class="rounded-lg bg-surface-container-low px-3 py-2">
                            <div class="text-[11px] font-bold uppercase tracking-wide text-on-surface-variant">
                                Email
                            </div>
                            <div class="mt-1 break-all text-sm font-semibold text-on-surface">
                                {{ session('account-password-reset.email') }}
                            </div>
                        </div>

                        <div class="rounded-lg bg-surface-container-low px-3 py-2">
                            <div class="text-[11px] font-bold uppercase tracking-wide text-on-surface-variant">
                                Password sementara
                            </div>
                            <div class="mt-1 break-all font-mono text-sm font-semibold text-on-surface">
                                {{ session('account-password-reset.temporary_password') }}
                            </div>
                        </div>
                    </div>

                    <p class="mt-3 text-xs leading-5 text-on-surface-variant">
                        Pengguna wajib mengganti password saat login berikutnya.
                    </p>
                </div>
            </div>
        </div>
    @endif


    {{-- =========================================================
         SUMMARY
         ========================================================= --}}
    <section class="mb-5 grid grid-cols-1 gap-3 sm:grid-cols-2 lg:grid-cols-3">

        <div class="rounded-xl border border-outline-variant/40 bg-surface-container-lowest p-4 shadow-sm">
            <div class="flex items-center justify-between gap-3">
                <div>
                    <p class="text-[11px] font-bold uppercase tracking-[0.08em] text-on-surface-variant">
                        Total Karyawan
                    </p>

                    <p class="mt-1 text-2xl font-extrabold tabular-nums text-primary">
                        {{ number_format($employees->total()) }}
                    </p>

                    <p class="mt-1 text-xs text-on-surface-variant">
                        Data master terdaftar
                    </p>
                </div>

                <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-primary-container/40">
                    <span class="material-symbols-outlined text-primary">
                        groups
                    </span>
                </div>
            </div>
        </div>


        <div class="rounded-xl border border-outline-variant/40 bg-surface-container-lowest p-4 shadow-sm">
            <div class="flex items-center justify-between gap-3">
                <div>
                    <p class="text-[11px] font-bold uppercase tracking-[0.08em] text-on-surface-variant">
                        Divisi
                    </p>

                    <p class="mt-1 text-2xl font-extrabold tabular-nums text-primary">
                        {{ $divisions->count() }}
                    </p>

                    <p class="mt-1 text-xs text-on-surface-variant">
                        Struktur organisasi
                    </p>
                </div>

                <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-surface-container-low">
                    <span class="material-symbols-outlined text-primary">
                        account_tree
                    </span>
                </div>
            </div>
        </div>


        <div class="rounded-xl border border-outline-variant/40 bg-surface-container-lowest p-4 shadow-sm sm:col-span-2 lg:col-span-1">
            <div class="flex items-center justify-between gap-3">
                <div>
                    <p class="text-[11px] font-bold uppercase tracking-[0.08em] text-on-surface-variant">
                        Tampilan
                    </p>

                    <p class="mt-1 text-2xl font-extrabold text-primary">
                        {{ $employees->count() }}
                    </p>

                    <p class="mt-1 text-xs text-on-surface-variant">
                        Data pada halaman ini
                    </p>
                </div>

                <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-surface-container-low">
                    <span class="material-symbols-outlined text-primary">
                        table_rows
                    </span>
                </div>
            </div>
        </div>

    </section>


    {{-- =========================================================
         FILTER
         ========================================================= --}}
    <section class="mb-5 rounded-2xl border border-outline-variant/40 bg-surface-container-lowest shadow-sm">

        <div class="border-b border-outline-variant/30 px-4 py-4 sm:px-5">
            <div class="flex items-center gap-3">
                <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-primary-container/40">
                    <span class="material-symbols-outlined text-[19px] text-primary">
                        filter_alt
                    </span>
                </div>

                <div>
                    <h2 class="text-sm font-extrabold text-on-surface">
                        Filter Data Karyawan
                    </h2>

                    <p class="mt-0.5 text-xs text-on-surface-variant">
                        Cari berdasarkan nama, NIPEG, atau divisi.
                    </p>
                </div>
            </div>
        </div>


        <form
            data-auto-filter
            method="GET"
            action="{{ route('employees.index') }}"
            class="grid grid-cols-1 gap-3 p-4 sm:p-5 lg:grid-cols-[minmax(0,1fr)_minmax(220px,280px)_auto_auto]"
        >

            <div class="min-w-0">
                <label
                    for="employee-search"
                    class="mb-1.5 block text-xs font-bold text-on-surface"
                >
                    Pencarian
                </label>

                <div class="relative">
                    <span class="material-symbols-outlined pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-[20px] text-on-surface-variant">
                        search
                    </span>

                    <input
                        id="employee-search"
                        type="search"
                        name="q"
                        value="{{ request('q') }}"
                        placeholder="Cari nama atau NIPEG..."
                        autocomplete="off"
                        class="min-h-11 w-full rounded-xl border border-outline-variant/60 bg-surface-container-low pl-10 pr-3 text-sm text-on-surface outline-none transition focus:border-primary focus:ring-2 focus:ring-primary/20"
                    >
                </div>
            </div>


            <div>
                <label
                    for="employee-division"
                    class="mb-1.5 block text-xs font-bold text-on-surface"
                >
                    Divisi
                </label>

                <select
                    id="employee-division"
                    name="division_id"
                    onchange="this.form.submit()"
                    class="min-h-11 w-full rounded-xl border border-outline-variant/60 bg-surface-container-low px-3 text-sm text-on-surface outline-none transition focus:border-primary focus:ring-2 focus:ring-primary/20"
                >
                    <option value="">Semua Divisi</option>

                    @foreach ($divisions as $division)
                        <option
                            value="{{ $division->id }}"
                            @selected(request('division_id') == $division->id)
                        >
                            {{ $division->nama }}
                        </option>
                    @endforeach
                </select>
            </div>


            <div class="flex items-end">
                <button
                    type="submit"
                    class="inline-flex min-h-11 w-full items-center justify-center gap-2 rounded-xl bg-primary-container px-4 text-sm font-bold text-on-primary transition hover:bg-primary focus:outline-none focus-visible:ring-2 focus-visible:ring-primary focus-visible:ring-offset-2 lg:w-auto"
                >
                    <span class="material-symbols-outlined text-[19px]">
                        search
                    </span>
                    Cari
                </button>
            </div>


            @if (request('q') || request('division_id'))
                <div class="flex items-end">
                    <a
                        href="{{ route('employees.index') }}"
                        class="inline-flex min-h-11 w-full items-center justify-center gap-2 rounded-xl border border-outline-variant/60 bg-surface-container-low px-4 text-sm font-bold text-on-surface transition hover:bg-surface-container hover:text-primary focus:outline-none focus-visible:ring-2 focus-visible:ring-primary focus-visible:ring-offset-2 lg:w-auto"
                    >
                        <span class="material-symbols-outlined text-[18px]">
                            restart_alt
                        </span>
                        Atur Ulang
                    </a>
                </div>
            @endif

        </form>
    </section>


    {{-- =========================================================
         TABLE
         ========================================================= --}}
    <section class="mb-6 overflow-hidden rounded-2xl border border-outline-variant/40 bg-surface-container-lowest shadow-sm">

        <div class="flex flex-col gap-3 border-b border-outline-variant/30 px-4 py-4 sm:flex-row sm:items-center sm:justify-between sm:px-5">

            <div>
                <h2 class="text-base font-extrabold text-on-surface">
                    Daftar Karyawan
                </h2>

                <p class="mt-1 text-xs text-on-surface-variant">
                    Menampilkan {{ $employees->count() }} data pada halaman ini.
                </p>
            </div>

            <div class="inline-flex w-fit items-center gap-2 rounded-lg bg-surface-container-low px-3 py-2">
                <span class="material-symbols-outlined text-[18px] text-on-surface-variant">
                    info
                </span>

                <span class="text-xs font-semibold text-on-surface-variant">
                    Geser tabel ke samping pada layar kecil
                </span>
            </div>

        </div>


        <div class="overflow-x-auto">
            <table class="w-full min-w-[1180px] text-left">

                <thead>
                    <tr class="bg-surface-container-low text-[11px] font-extrabold uppercase tracking-[0.08em] text-on-surface-variant">

                        <th class="whitespace-nowrap px-4 py-3.5">
                            NIPEG
                        </th>

                        <th class="px-4 py-3.5">
                            Karyawan
                        </th>

                        <th class="whitespace-nowrap px-4 py-3.5">
                            L/P
                        </th>

                        <th class="px-4 py-3.5">
                            Jabatan
                        </th>

                        <th class="whitespace-nowrap px-4 py-3.5">
                            Code Band
                        </th>

                        <th class="whitespace-nowrap px-4 py-3.5">
                            No. HP
                        </th>

                        <th class="px-4 py-3.5">
                            Divisi
                        </th>

                        <th class="px-4 py-3.5">
                            Akun
                        </th>

                        <th class="sticky right-0 bg-surface-container-low px-4 py-3.5 text-right">
                            Aksi
                        </th>

                    </tr>
                </thead>


                <tbody class="divide-y divide-outline-variant/20 text-sm">

                    @forelse ($employees as $employee)

                        <tr class="group transition-colors hover:bg-surface-container-low/50">

                            {{-- NIPEG --}}
                            <td class="whitespace-nowrap px-4 py-4 align-top">
                                <span class="font-mono text-xs font-semibold tabular-nums text-on-surface-variant">
                                    {{ $employee->nipeg }}
                                </span>
                            </td>


                            {{-- NAMA --}}
                            <td class="min-w-[210px] px-4 py-4 align-top">
                                <div class="flex items-start gap-3">

                                    <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-primary-container/40">
                                        <span class="material-symbols-outlined text-[18px] text-primary">
                                            person
                                        </span>
                                    </div>

                                    <div class="min-w-0">
                                        <div class="truncate font-bold text-on-surface">
                                            {{ $employee->nama }}
                                        </div>

                                        @if ($employee->jenis_kelamin)
                                            <div class="mt-0.5 text-xs text-on-surface-variant">
                                                {{ $employee->jenis_kelamin === 'L' ? 'Laki-laki' : 'Perempuan' }}
                                            </div>
                                        @endif
                                    </div>

                                </div>
                            </td>


                            {{-- GENDER --}}
                            <td class="whitespace-nowrap px-4 py-4 align-top">
                                @if ($employee->jenis_kelamin === 'L')
                                    <span class="inline-flex items-center rounded-full bg-primary-container/30 px-2.5 py-1 text-xs font-bold text-primary">
                                        L
                                    </span>
                                @elseif ($employee->jenis_kelamin === 'P')
                                    <span class="inline-flex items-center rounded-full bg-secondary-container/60 px-2.5 py-1 text-xs font-bold text-secondary">
                                        P
                                    </span>
                                @else
                                    <span class="text-sm text-on-surface-variant">
                                        -
                                    </span>
                                @endif
                            </td>


                            {{-- JABATAN --}}
                            <td class="max-w-[190px] px-4 py-4 align-top">
                                <span class="line-clamp-2 text-sm text-on-surface">
                                    {{ $employee->jabatan ?: '-' }}
                                </span>
                            </td>


                            {{-- BAND --}}
                            <td class="whitespace-nowrap px-4 py-4 align-top">
                                @if ($employee->band_code !== null)
                                    <span class="inline-flex items-center gap-1.5 rounded-full border border-primary/20 bg-primary-container/30 px-2.5 py-1 text-xs font-extrabold text-primary">
                                        <span class="material-symbols-outlined text-[15px]">
                                            workspace_premium
                                        </span>
                                        Band {{ $employee->band_code }}
                                    </span>
                                @else
                                    <span class="inline-flex items-center rounded-full bg-surface-container px-2.5 py-1 text-xs font-semibold text-on-surface-variant">
                                        Belum diisi
                                    </span>
                                @endif
                            </td>


                            {{-- PHONE --}}
                            <td class="whitespace-nowrap px-4 py-4 align-top">
                                <span class="text-sm text-on-surface">
                                    {{ $employee->no_hp ?: '-' }}
                                </span>
                            </td>


                            {{-- DIVISION --}}
                            <td class="min-w-[170px] px-4 py-4 align-top">
                                <span class="inline-flex max-w-full items-center rounded-lg bg-surface-container px-2.5 py-1 text-xs font-bold text-primary">
                                    <span class="truncate">
                                        {{ $employee->division->nama }}
                                    </span>
                                </span>
                            </td>


                            {{-- ACCOUNT --}}
                            <td class="min-w-[210px] px-4 py-4 align-top">
                                @if ($employee->user)

                                    <div class="flex flex-col gap-1.5">

                                        @if ($employee->user->is_active)
                                            <span class="inline-flex w-fit items-center gap-1.5 rounded-full bg-secondary-container/60 px-2.5 py-1 text-xs font-bold text-secondary">
                                                <span class="h-1.5 w-1.5 rounded-full bg-secondary"></span>
                                                Aktif
                                            </span>
                                        @else
                                            <span class="inline-flex w-fit items-center gap-1.5 rounded-full bg-surface-container px-2.5 py-1 text-xs font-bold text-on-surface-variant">
                                                <span class="h-1.5 w-1.5 rounded-full bg-on-surface-variant"></span>
                                                Nonaktif
                                            </span>
                                        @endif

                                        <span class="max-w-[190px] truncate text-xs text-on-surface-variant" title="{{ $employee->user->email }}">
                                            {{ $employee->user->email }}
                                        </span>

                                    </div>

                                @else

                                    <span class="inline-flex items-center gap-1.5 rounded-full bg-surface-container px-2.5 py-1 text-xs font-bold text-on-surface-variant">
                                        <span class="material-symbols-outlined text-[15px]">
                                            person_off
                                        </span>
                                        Belum ada akun
                                    </span>

                                @endif
                            </td>


                            {{-- ACTIONS --}}
                            <td class="sticky right-0 whitespace-nowrap bg-surface-container-lowest px-4 py-4 text-right align-top group-hover:bg-surface-container-low/50">

                                <div class="flex items-center justify-end gap-1.5">

                                    <a
                                        href="{{ route('employees.edit', $employee) }}"
                                        title="Edit data karyawan"
                                        aria-label="Edit {{ $employee->nama }}"
                                        class="inline-flex h-9 w-9 items-center justify-center rounded-lg text-on-surface-variant transition hover:bg-primary-container/30 hover:text-primary focus:outline-none focus-visible:ring-2 focus-visible:ring-primary"
                                    >
                                        <span class="material-symbols-outlined text-[19px]">
                                            edit
                                        </span>
                                    </a>


                                    @if ($employee->user)

                                        <a
                                            href="{{ route('employee-accounts.index', ['q' => $employee->nipeg]) }}"
                                            title="Kelola akun"
                                            aria-label="Kelola akun {{ $employee->nama }}"
                                            class="inline-flex h-9 w-9 items-center justify-center rounded-lg text-on-surface-variant transition hover:bg-primary-container/30 hover:text-primary focus:outline-none focus-visible:ring-2 focus-visible:ring-primary"
                                        >
                                            <span class="material-symbols-outlined text-[19px]">
                                                manage_accounts
                                            </span>
                                        </a>

                                    @else

                                        <a
                                            href="{{ route('employees.account.create', $employee) }}"
                                            title="Buat akun"
                                            aria-label="Buat akun {{ $employee->nama }}"
                                            class="inline-flex h-9 w-9 items-center justify-center rounded-lg text-on-surface-variant transition hover:bg-secondary-container/60 hover:text-secondary focus:outline-none focus-visible:ring-2 focus-visible:ring-secondary"
                                        >
                                            <span class="material-symbols-outlined text-[19px]">
                                                person_add
                                            </span>
                                        </a>

                                    @endif


                                    <form
                                        action="{{ route('employees.destroy', $employee) }}"
                                        method="POST"
                                        class="inline"
                                        onsubmit="return confirm('Hapus karyawan {{ addslashes($employee->nama) }}?')"
                                    >
                                        @csrf
                                        @method('DELETE')

                                        <button
                                            type="submit"
                                            title="Hapus karyawan"
                                            aria-label="Hapus {{ $employee->nama }}"
                                            class="inline-flex h-9 w-9 items-center justify-center rounded-lg text-error transition hover:bg-error/10 focus:outline-none focus-visible:ring-2 focus-visible:ring-error"
                                        >
                                            <span class="material-symbols-outlined text-[19px]">
                                                delete
                                            </span>
                                        </button>
                                    </form>

                                </div>

                            </td>

                        </tr>

                    @empty

                        <tr>
                            <td colspan="9" class="px-6 py-14 text-center">

                                <div class="mx-auto flex max-w-md flex-col items-center">

                                    <div class="flex h-14 w-14 items-center justify-center rounded-2xl bg-surface-container">
                                        <span class="material-symbols-outlined text-[28px] text-on-surface-variant">
                                            person_search
                                        </span>
                                    </div>

                                    <h3 class="mt-4 text-base font-extrabold text-on-surface">
                                        Data karyawan tidak ditemukan
                                    </h3>

                                    <p class="mt-1 text-sm leading-6 text-on-surface-variant">
                                        Tidak ada karyawan yang sesuai dengan filter atau pencarian saat ini.
                                    </p>

                                    @if (request('q') || request('division_id'))
                                        <a
                                            href="{{ route('employees.index') }}"
                                            class="mt-4 inline-flex min-h-10 items-center gap-2 rounded-lg border border-outline-variant/60 px-4 text-sm font-bold text-primary hover:bg-surface-container-low"
                                        >
                                            <span class="material-symbols-outlined text-[18px]">
                                                restart_alt
                                            </span>
                                            Tampilkan Semua
                                        </a>
                                    @endif

                                </div>

                            </td>
                        </tr>

                    @endforelse

                </tbody>

            </table>
        </div>


        {{-- PAGINATION --}}
        @if ($employees->hasPages())
            <div class="border-t border-outline-variant/30 px-4 py-4 sm:px-5">
                <div class="overflow-x-auto">
                    {{ $employees->links() }}
                </div>
            </div>
        @endif

    </section>


    {{-- =========================================================
         ANALYTICS
         ========================================================= --}}
    <section class="grid grid-cols-1 gap-5 lg:grid-cols-3">

        {{-- DIVISION --}}
        <div class="rounded-2xl border border-outline-variant/40 bg-surface-container-lowest p-4 shadow-sm sm:p-5 lg:col-span-2">

            <div class="flex items-start justify-between gap-3 border-b border-outline-variant/30 pb-4">

                <div class="flex items-start gap-3">
                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-primary-container/40">
                        <span class="material-symbols-outlined text-primary">
                            account_tree
                        </span>
                    </div>

                    <div>
                        <h2 class="text-base font-extrabold text-on-surface">
                            Sebaran Karyawan per Divisi
                        </h2>

                        <p class="mt-1 text-xs leading-5 text-on-surface-variant">
                            Gambaran jumlah personel berdasarkan struktur divisi.
                        </p>
                    </div>
                </div>

            </div>

            <div class="mt-5 h-72 sm:h-80">
                <canvas id="chart-karyawan-divisi"></canvas>
            </div>

        </div>


        {{-- GENDER --}}
        <div class="rounded-2xl border border-outline-variant/40 bg-surface-container-lowest p-4 shadow-sm sm:p-5">

            <div class="flex items-start gap-3 border-b border-outline-variant/30 pb-4">

                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-surface-container">
                    <span class="material-symbols-outlined text-primary">
                        wc
                    </span>
                </div>

                <div>
                    <h2 class="text-base font-extrabold text-on-surface">
                        Jenis Kelamin
                    </h2>

                    <p class="mt-1 text-xs leading-5 text-on-surface-variant">
                        Komposisi data gender karyawan.
                    </p>
                </div>

            </div>

            <div class="mt-5 h-72 sm:h-80">
                <canvas id="chart-karyawan-gender"></canvas>
            </div>

        </div>

    </section>

</div>
@endsection


@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const divisionCanvas = document.getElementById('chart-karyawan-divisi');
    const genderCanvas = document.getElementById('chart-karyawan-gender');

    if (typeof Chart === 'undefined') {
        return;
    }

    const sebaranDivisi = @json($sebaranDivisi);
    const sebaranGender = @json($sebaranGender);

    const palette = window.chartPalet || {
        primary: '#365f91',
        secondary: '#4f7d68',
        outline: '#8a9199',
        grid: 'rgba(120, 130, 140, 0.15)',
    };


    /*
     * Chart: Sebaran Divisi
     */
    if (divisionCanvas) {
        new Chart(divisionCanvas, {
            type: 'bar',

            data: {
                labels: sebaranDivisi.map(item => item.nama),

                datasets: [{
                    label: 'Jumlah Karyawan',
                    data: sebaranDivisi.map(item => item.jumlah),
                    backgroundColor: palette.primary,
                    borderRadius: 6,
                    borderSkipped: false,
                    maxBarThickness: 42,
                }],
            },

            options: {
                responsive: true,
                maintainAspectRatio: false,

                interaction: {
                    intersect: false,
                    mode: 'index',
                },

                plugins: {
                    legend: {
                        display: false,
                    },

                    tooltip: {
                        callbacks: {
                            label: function (context) {
                                return ' ' + context.parsed.y + ' karyawan';
                            }
                        }
                    }
                },

                scales: {
                    x: {
                        grid: {
                            display: false,
                        },

                        ticks: {
                            maxRotation: 35,
                            minRotation: 0,
                            autoSkip: true,
                            maxTicksLimit: 8,
                        }
                    },

                    y: {
                        beginAtZero: true,

                        ticks: {
                            precision: 0,
                        },

                        grid: {
                            color: palette.grid,
                        }
                    }
                }
            }
        });
    }


    /*
     * Chart: Gender
     */
    if (genderCanvas) {
        new Chart(genderCanvas, {
            type: 'doughnut',

            data: {
                labels: Object.keys(sebaranGender),

                datasets: [{
                    data: Object.values(sebaranGender),

                    backgroundColor: [
                        palette.primary,
                        palette.secondary,
                        palette.outline
                    ],

                    borderWidth: 2,

                    borderColor: document.documentElement.classList.contains('dark')
                        ? '#1f2937'
                        : '#ffffff',

                    hoverOffset: 5,
                }],
            },

            options: {
                responsive: true,
                maintainAspectRatio: false,

                cutout: '66%',

                plugins: {
                    legend: {
                        position: 'bottom',

                        labels: {
                            usePointStyle: true,
                            pointStyle: 'circle',
                            boxWidth: 9,
                            boxHeight: 9,
                            padding: 16,
                            font: {
                                size: 12,
                            }
                        }
                    },

                    tooltip: {
                        callbacks: {
                            label: function (context) {
                                return ' ' + context.label + ': ' + context.parsed;
                            }
                        }
                    }
                }
            }
        });
    }
});
</script>
@endsection
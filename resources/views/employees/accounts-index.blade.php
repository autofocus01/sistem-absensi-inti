@extends('layouts.absensi')

@section('title', 'Kelola Akun Karyawan')

@section('content')
<div class="inti-page">

    <div class="max-w-7xl mx-auto space-y-6">

        {{-- =====================================================
             HEADER
             ===================================================== --}}
        <section>
            <div class="flex flex-col lg:flex-row lg:items-start lg:justify-between gap-4">

                <div class="min-w-0">

                    <div class="flex flex-wrap items-center gap-2 mb-2">

                        <span class="inline-flex items-center gap-1.5
                                     px-3 py-1 rounded-full
                                     bg-primary-container text-on-primary
                                     font-label-sm text-label-sm
                                     uppercase tracking-wider">

                            <span class="material-symbols-outlined text-[15px]">
                                manage_accounts
                            </span>

                            Administrasi Akun
                        </span>

                        <span class="inline-flex items-center gap-1.5
                                     px-3 py-1 rounded-full
                                     bg-surface-container
                                     text-on-surface-variant
                                     font-label-sm text-label-sm">

                            <span class="material-symbols-outlined text-[15px]">
                                admin_panel_settings
                            </span>

                            HR Admin
                        </span>

                    </div>

                    <h1 class="font-headline-lg text-headline-lg
                               text-primary tracking-tight">
                        Kelola Akun Karyawan
                    </h1>

                    <p class="font-body-md text-body-md
                              text-on-surface-variant mt-2 max-w-3xl">
                        Administrasi akun login karyawan secara terpisah
                        dari data kepegawaian.
                    </p>

                </div>

            </div>
        </section>


        {{-- =====================================================
             STATUS
             ===================================================== --}}
        @if (session('status'))

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

                        <div class="font-body-md text-body-md
                                    text-secondary pt-1">
                            {{ session('status') }}
                        </div>

                    </div>

                </div>
            </section>

        @endif


        {{-- =====================================================
             ACCOUNT CREATED
             ===================================================== --}}
        @if (session('account-created'))

            @php($created = session('account-created'))

            <section>
                <div class="rounded-xl border border-secondary/20
                            bg-secondary/10 p-4 sm:p-5">

                    <div class="flex items-start gap-3">

                        <div class="flex h-10 w-10 shrink-0 items-center justify-center
                                    rounded-lg bg-secondary-container text-secondary">

                            <span class="material-symbols-outlined">
                                person_add
                            </span>

                        </div>

                        <div class="min-w-0">

                            <div class="font-title-sm text-title-sm text-secondary">
                                Akun berhasil dibuat
                            </div>

                            <p class="font-body-sm text-body-sm
                                      text-on-surface-variant mt-1">
                                Akun berhasil dibuat untuk
                                <strong>{{ $created['employee'] ?? '-' }}</strong>.
                            </p>

                            <div class="mt-3 rounded-lg
                                        bg-surface-container-low
                                        p-3 sm:p-4">

                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">

                                    <div>
                                        <div class="font-label-sm text-label-sm
                                                    text-on-surface-variant
                                                    uppercase tracking-wider">
                                            Email
                                        </div>

                                        <div class="font-title-sm text-title-sm
                                                    text-primary mt-1 break-all">
                                            {{ $created['email'] ?? '-' }}
                                        </div>
                                    </div>

                                    <div>
                                        <div class="font-label-sm text-label-sm
                                                    text-on-surface-variant
                                                    uppercase tracking-wider">
                                            Password sementara
                                        </div>

                                        <div class="font-title-sm text-title-sm
                                                    text-primary mt-1 break-all">
                                            {{ $created['temporary_password'] ?? '-' }}
                                        </div>
                                    </div>

                                </div>

                            </div>

                            <p class="font-body-sm text-body-sm
                                      text-on-surface-variant mt-3">
                                Sampaikan kredensial melalui kanal yang aman.
                                Pengguna wajib mengganti password saat login
                                pertama.
                            </p>

                        </div>

                    </div>

                </div>
            </section>

        @endif


        {{-- =====================================================
             PASSWORD RESET
             ===================================================== --}}
        @if (session('account-password-reset'))

            @php($reset = session('account-password-reset'))

            <section>
                <div class="rounded-xl border border-secondary/20
                            bg-secondary/10 p-4 sm:p-5">

                    <div class="flex items-start gap-3">

                        <div class="flex h-10 w-10 shrink-0 items-center justify-center
                                    rounded-lg bg-secondary-container text-secondary">

                            <span class="material-symbols-outlined">
                                lock_reset
                            </span>

                        </div>

                        <div class="min-w-0">

                            <div class="font-title-sm text-title-sm text-secondary">
                                Password sementara berhasil dibuat ulang
                            </div>

                            <p class="font-body-sm text-body-sm
                                      text-on-surface-variant mt-1">
                                Password baru dibuat untuk
                                <strong>{{ $reset['employee'] ?? '-' }}</strong>.
                            </p>

                            <div class="mt-3 rounded-lg
                                        bg-surface-container-low
                                        p-3 sm:p-4">

                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">

                                    <div>
                                        <div class="font-label-sm text-label-sm
                                                    text-on-surface-variant
                                                    uppercase tracking-wider">
                                            Email
                                        </div>

                                        <div class="font-title-sm text-title-sm
                                                    text-primary mt-1 break-all">
                                            {{ $reset['email'] ?? '-' }}
                                        </div>
                                    </div>

                                    <div>
                                        <div class="font-label-sm text-label-sm
                                                    text-on-surface-variant
                                                    uppercase tracking-wider">
                                            Password sementara
                                        </div>

                                        <div class="font-title-sm text-title-sm
                                                    text-primary mt-1 break-all">
                                            {{ $reset['temporary_password'] ?? '-' }}
                                        </div>
                                    </div>

                                </div>

                            </div>

                        </div>

                    </div>

                </div>
            </section>

        @endif


        {{-- =====================================================
             SEARCH
             ===================================================== --}}
        <section>

            <form
                data-auto-filter
                method="GET"
                class="rounded-xl border border-outline-variant/70
                       bg-surface-container-lowest
                       p-4 sm:p-5"
            >

                <div class="flex flex-col lg:flex-row
                            lg:items-end gap-3">

                    <div class="flex-1 min-w-0">

                        <label
                            for="employee-account-search"
                            class="block font-title-sm text-title-sm
                                   text-on-surface mb-2"
                        >
                            Cari karyawan
                        </label>

                        <div class="relative">

                            <span class="material-symbols-outlined
                                         absolute left-3 top-1/2
                                         -translate-y-1/2
                                         text-on-surface-variant
                                         text-[20px]">
                                search
                            </span>

                            <input
                                id="employee-account-search"
                                type="text"
                                name="q"
                                value="{{ request('q') }}"
                                placeholder="Nama, NIPEG, atau email..."
                                autocomplete="off"
                                class="w-full min-h-11 rounded-lg
                                       border border-outline-variant
                                       bg-surface-container-low
                                       text-on-surface
                                       pl-11 pr-4
                                       font-body-md text-body-md
                                       focus:outline-none
                                       focus-visible:ring-2
                                       focus-visible:ring-primary
                                       focus-visible:border-primary"
                            >

                        </div>

                    </div>


                    <div class="flex flex-col sm:flex-row gap-2">

                        <button
                            type="submit"
                            class="inline-flex items-center justify-center gap-2
                                   min-h-11 px-5 rounded-lg
                                   inti-btn inti-btn-primary
                                   font-title-sm text-title-sm
                                   focus:outline-none
                                   focus-visible:ring-2
                                   focus-visible:ring-primary"
                        >
                            <span class="material-symbols-outlined text-[18px]">
                                search
                            </span>

                            Cari
                        </button>

                        @if (request()->filled('q'))

                            <a
                                href="{{ route('employee-accounts.index') }}"
                                class="inline-flex items-center justify-center gap-2
                                       min-h-11 px-5 rounded-lg
                                       bg-surface-container
                                       text-on-surface
                                       font-title-sm text-title-sm
                                       hover:bg-surface-container-high
                                       focus:outline-none
                                       focus-visible:ring-2
                                       focus-visible:ring-primary"
                            >
                                <span class="material-symbols-outlined text-[18px]">
                                    close
                                </span>

                                Reset
                            </a>

                        @endif

                    </div>

                </div>

                @if (request()->filled('q'))

                    <div class="flex items-center gap-2 mt-3
                                font-body-sm text-body-sm
                                text-on-surface-variant">

                        <span class="material-symbols-outlined text-[16px]">
                            filter_alt
                        </span>

                        Menampilkan hasil untuk:
                        <strong class="text-primary">
                            {{ request('q') }}
                        </strong>

                    </div>

                @endif

            </form>

        </section>


        {{-- =====================================================
             ACCOUNT TABLE
             ===================================================== --}}
        <section class="rounded-xl border border-outline-variant/70
                        bg-surface-container-lowest
                        shadow-sm overflow-hidden">

            <div class="p-5 sm:p-6
                        border-b border-outline-variant/60">

                <div class="flex flex-col sm:flex-row
                            sm:items-center sm:justify-between gap-3">

                    <div>

                        <h2 class="font-headline-sm text-headline-sm text-primary">
                            Daftar Akun
                        </h2>

                        <p class="font-body-sm text-body-sm
                                  text-on-surface-variant mt-1">
                            Kelola status login dan kredensial akun
                            setiap karyawan.
                        </p>

                    </div>

                    <div class="inline-flex items-center gap-2
                                rounded-full px-3 py-1.5
                                bg-surface-container
                                text-on-surface-variant
                                font-label-sm text-label-sm shrink-0">

                        <span class="material-symbols-outlined text-[16px]">
                            groups
                        </span>

                        {{ $employees->total() }} karyawan

                    </div>

                </div>

            </div>


            <div class="overflow-x-auto">

                <table class="w-full min-w-[920px] text-left">

                    <thead>

                        <tr class="bg-surface-container-low
                                   font-label-sm text-label-sm
                                   text-on-surface-variant uppercase">

                            <th class="px-5 py-3.5">
                                Karyawan
                            </th>

                            <th class="px-5 py-3.5">
                                NIPEG
                            </th>

                            <th class="px-5 py-3.5">
                                Email Login
                            </th>

                            <th class="px-5 py-3.5">
                                Status
                            </th>

                            <th class="px-5 py-3.5">
                                Aksi
                            </th>

                        </tr>

                    </thead>


                    <tbody class="divide-y divide-surface-container-low
                                 font-body-md text-body-md">

                        @forelse ($employees as $employee)

                            <tr class="hover:bg-surface-container-low/60
                                       transition-colors">

                                {{-- Karyawan --}}
                                <td class="px-5 py-4 align-top">

                                    <div class="flex items-start gap-3">

                                        <div class="flex h-10 w-10 shrink-0
                                                    items-center justify-center
                                                    rounded-full
                                                    bg-primary-container
                                                    text-on-primary
                                                    font-title-sm">

                                            {{ strtoupper(
                                                substr(
                                                    (string) $employee->nama,
                                                    0,
                                                    1
                                                )
                                            ) }}

                                        </div>

                                        <div class="min-w-0">

                                            <div class="font-title-sm text-title-sm
                                                        text-primary">
                                                {{ $employee->nama }}
                                            </div>

                                            <div class="font-body-sm text-body-sm
                                                        text-on-surface-variant mt-0.5">
                                                {{ $employee->division?->nama ?? '-' }}
                                            </div>

                                        </div>

                                    </div>

                                </td>


                                {{-- NIPEG --}}
                                <td class="px-5 py-4 align-top">

                                    <span class="font-title-sm text-title-sm
                                                 text-primary tabular-nums">
                                        {{ $employee->nipeg }}
                                    </span>

                                </td>


                                {{-- Email --}}
                                <td class="px-5 py-4 align-top">

                                    @if ($employee->user)

                                        <div class="flex items-center gap-2
                                                    min-w-0 max-w-[280px]">

                                            <span class="material-symbols-outlined
                                                         text-[17px]
                                                         text-on-surface-variant
                                                         shrink-0">
                                                mail
                                            </span>

                                            <span class="truncate">
                                                {{ $employee->user->email }}
                                            </span>

                                        </div>

                                    @else

                                        <span class="text-on-surface-variant">
                                            Belum ada akun
                                        </span>

                                    @endif

                                </td>


                                {{-- Status --}}
                                <td class="px-5 py-4 align-top">

                                    @if ($employee->user)

                                        @if ($employee->user->is_active)

                                            <span class="inline-flex items-center gap-1.5
                                                         rounded-full px-2.5 py-1
                                                         bg-secondary-container
                                                         text-secondary
                                                         font-label-sm text-label-sm
                                                         font-semibold">

                                                <span class="h-1.5 w-1.5 rounded-full
                                                             bg-secondary">
                                                </span>

                                                Aktif

                                            </span>

                                        @else

                                            <span class="inline-flex items-center gap-1.5
                                                         rounded-full px-2.5 py-1
                                                         bg-error-container
                                                         text-error
                                                         font-label-sm text-label-sm
                                                         font-semibold">

                                                <span class="h-1.5 w-1.5 rounded-full
                                                             bg-error">
                                                </span>

                                                Nonaktif

                                            </span>

                                        @endif

                                    @else

                                        <span class="inline-flex items-center gap-1.5
                                                     rounded-full px-2.5 py-1
                                                     bg-surface-container
                                                     text-on-surface-variant
                                                     font-label-sm text-label-sm">

                                            <span class="material-symbols-outlined text-[14px]">
                                                person_off
                                            </span>

                                            Belum ada akun

                                        </span>

                                    @endif

                                </td>


                                {{-- Actions --}}
                                <td class="px-5 py-4 align-top">

                                    @if ($employee->user)

                                        <div class="flex flex-wrap items-center gap-2">

                                            {{-- Toggle --}}
                                            <form
                                                action="{{ route('employees.account.toggle', $employee) }}"
                                                method="POST"
                                                class="inline"
                                                data-account-action="toggle"
                                                data-action-label="{{ $employee->user->is_active ? 'Nonaktifkan' : 'Aktifkan' }}"
                                            >

                                                @csrf
                                                @method('PATCH')

                                                <button
                                                    type="submit"
                                                    class="inline-flex items-center justify-center
                                                           gap-1.5 min-h-9 px-3 rounded-lg
                                                           bg-surface-container
                                                           text-primary
                                                           font-label-sm text-label-sm
                                                           font-semibold
                                                           hover:bg-surface-container-high
                                                           focus:outline-none
                                                           focus-visible:ring-2
                                                           focus-visible:ring-primary"
                                                >

                                                    <span class="material-symbols-outlined text-[16px]">
                                                        {{ $employee->user->is_active
                                                            ? 'person_off'
                                                            : 'person_check' }}
                                                    </span>

                                                    {{ $employee->user->is_active
                                                        ? 'Nonaktifkan'
                                                        : 'Aktifkan' }}

                                                </button>

                                            </form>


                                            {{-- Reset Password --}}
                                            <form
                                                action="{{ route('employees.account.reset-password', $employee) }}"
                                                method="POST"
                                                class="inline"
                                                data-account-action="reset"
                                            >

                                                @csrf

                                                <button
                                                    type="submit"
                                                    class="inline-flex items-center justify-center
                                                           gap-1.5 min-h-9 px-3 rounded-lg
                                                           bg-primary-container
                                                           text-on-primary
                                                           font-label-sm text-label-sm
                                                           font-semibold
                                                           hover:opacity-90
                                                           focus:outline-none
                                                           focus-visible:ring-2
                                                           focus-visible:ring-primary"
                                                >

                                                    <span class="material-symbols-outlined text-[16px]">
                                                        lock_reset
                                                    </span>

                                                    Reset Password

                                                </button>

                                            </form>

                                        </div>

                                    @else

                                        <a
                                            href="{{ route('employees.account.create', $employee) }}"
                                            class="inline-flex items-center justify-center
                                                   gap-1.5 min-h-9 px-3 rounded-lg
                                                   bg-secondary-container
                                                   text-secondary
                                                   font-label-sm text-label-sm
                                                   font-semibold
                                                   hover:opacity-90
                                                   focus:outline-none
                                                   focus-visible:ring-2
                                                   focus-visible:ring-primary"
                                        >

                                            <span class="material-symbols-outlined text-[16px]">
                                                person_add
                                            </span>

                                            Buat Akun

                                        </a>

                                    @endif

                                </td>

                            </tr>

                        @empty

                            <tr>

                                <td colspan="5" class="px-5 py-14">

                                    <div class="flex flex-col items-center
                                                justify-center text-center">

                                        <div class="flex h-14 w-14
                                                    items-center justify-center
                                                    rounded-full
                                                    bg-surface-container
                                                    text-on-surface-variant">

                                            <span class="material-symbols-outlined text-[28px]">
                                                manage_accounts
                                            </span>

                                        </div>

                                        <h3 class="font-title-sm text-title-sm
                                                   text-primary mt-4">
                                            Tidak ada data akun
                                        </h3>

                                        <p class="font-body-sm text-body-sm
                                                  text-on-surface-variant
                                                  mt-1 max-w-md">

                                            @if (request()->filled('q'))
                                                Tidak ditemukan karyawan yang
                                                sesuai dengan pencarian
                                                "{{ request('q') }}".
                                            @else
                                                Belum ada data karyawan yang
                                                dapat ditampilkan.
                                            @endif

                                        </p>

                                        @if (request()->filled('q'))

                                            <a
                                                href="{{ route('employee-accounts.index') }}"
                                                class="inline-flex items-center gap-2
                                                       min-h-9 px-4 rounded-lg
                                                       bg-surface-container
                                                       text-on-surface
                                                       font-title-sm text-title-sm
                                                       mt-4"
                                            >

                                                <span class="material-symbols-outlined text-[17px]">
                                                    close
                                                </span>

                                                Hapus Pencarian

                                            </a>

                                        @endif

                                    </div>

                                </td>

                            </tr>

                        @endforelse

                    </tbody>

                </table>

            </div>


            {{-- Mobile table hint --}}
            <div class="px-5 py-3 border-t border-outline-variant/60
                        lg:hidden">

                <div class="flex items-center gap-2
                            font-body-sm text-body-sm
                            text-on-surface-variant">

                    <span class="material-symbols-outlined text-[16px]">
                        swipe
                    </span>

                    Geser tabel secara horizontal untuk melihat seluruh kolom.

                </div>

            </div>

        </section>


        {{-- =====================================================
             PAGINATION
             ===================================================== --}}
        @if ($employees->hasPages())

            <section>

                <div class="rounded-xl border border-outline-variant/70
                            bg-surface-container-lowest
                            p-3 sm:p-4 overflow-x-auto">

                    {{ $employees->links() }}

                </div>

            </section>

        @endif


        {{-- =====================================================
             GOVERNANCE NOTE
             ===================================================== --}}
        <section>

            <div class="rounded-xl border border-outline-variant/70
                        bg-surface-container-low
                        p-4 sm:p-5">

                <div class="flex items-start gap-3">

                    <span class="material-symbols-outlined
                                 text-primary text-[20px] shrink-0">
                        security
                    </span>

                    <div>

                        <div class="font-title-sm text-title-sm text-primary">
                            Batas administrasi akun
                        </div>

                        <p class="font-body-sm text-body-sm
                                  text-on-surface-variant mt-1 leading-5">
                            Halaman ini hanya mengelola akun login karyawan:
                            status aktif/nonaktif dan password sementara.
                            Data master kepegawaian tetap dikelola melalui
                            menu <strong>Data Karyawan</strong>.
                        </p>

                    </div>

                </div>

            </div>

        </section>

    </div>
</div>
@endsection


@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {

    const actionForms = document.querySelectorAll(
        '[data-account-action]'
    );

    actionForms.forEach(function (form) {

        form.addEventListener('submit', function (event) {

            const type = form.dataset.accountAction;
            const label = form.dataset.actionLabel || '';

            let message = '';

            if (type === 'toggle') {
                message = label + ' akun karyawan ini?';
            }

            if (type === 'reset') {
                message =
                    'Buat password sementara baru untuk akun ini?';
            }

            if (!message) {
                return;
            }

            if (!window.confirm(message)) {
                event.preventDefault();
                return;
            }

            const button = form.querySelector('button');

            if (!button) {
                return;
            }

            button.disabled = true;

            const icon = button.querySelector(
                '.material-symbols-outlined'
            );

            if (icon) {
                icon.textContent = 'progress_activity';
                icon.classList.add('animate-spin');
            }

            /*
             * Jangan mengubah action atau payload.
             * Hanya memberikan feedback visual ketika request berjalan.
             */
        });

    });

});
</script>
@endsection
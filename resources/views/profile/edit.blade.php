<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Profil Saya - PT. INTI</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">

    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>

    <script>
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    colors: {
                        primary: "#001428",
                        "primary-container": "#0f2942",
                        "on-primary": "#ffffff",
                        background: "#f8f9ff",
                        surface: "#f8f9ff",
                        "surface-container-lowest": "#ffffff",
                        "surface-container-low": "#eff4ff",
                        "surface-container": "#e6eeff",
                        "surface-container-high": "#dce9ff",
                        "surface-container-highest": "#d5e3fc",
                        "on-surface": "#0d1c2e",
                        "on-surface-variant": "#43474d",
                        outline: "#74777e",
                        "outline-variant": "#c3c6ce",
                        secondary: "#006a61",
                        "secondary-container": "#86f2e4",
                        error: "#ba1a1a",
                        "error-container": "#ffdad6"
                    },
                    fontFamily: {
                        jakarta: ["Plus Jakarta Sans", "sans-serif"]
                    }
                }
            }
        }
    </script>

    <style>
        :root {
            color-scheme: light;
        }

        html {
            scroll-behavior: smooth;
        }

        body {
            margin: 0;
            min-height: 100vh;
            background: #f8f9ff;
            color: #0d1c2e;
            font-family: "Plus Jakarta Sans", sans-serif;
            -webkit-font-smoothing: antialiased;
            overscroll-behavior: none;
        }

        * {
            box-sizing: border-box;
        }

        button,
        input,
        a {
            -webkit-tap-highlight-color: transparent;
        }

        button:focus-visible,
        a:focus-visible,
        input:focus-visible {
            outline: 3px solid rgba(15, 41, 66, .22);
            outline-offset: 2px;
        }

        .material-symbols-outlined {
            font-family: 'Material Symbols Outlined';
            font-weight: normal;
            font-style: normal;
            line-height: 1;
            letter-spacing: normal;
            text-transform: none;
            display: inline-block;
            white-space: nowrap;
            word-wrap: normal;
            direction: ltr;
            -webkit-font-feature-settings: 'liga';
            -webkit-font-smoothing: antialiased;
            font-variation-settings:
                'FILL' 0,
                'wght' 400,
                'GRAD' 0,
                'opsz' 24;
        }

        .profile-shell {
            width: min(100% - 32px, 1024px);
            margin-inline: auto;
        }

        .profile-card {
            background: #ffffff;
            border: 1px solid rgba(195, 198, 206, .55);
            border-radius: 18px;
            box-shadow: 0 8px 30px rgba(13, 28, 46, .055);
        }

        .profile-field {
            width: 100%;
            min-height: 46px;
            border-radius: 10px;
            border: 1px solid transparent;
            background: #eff4ff;
            padding: 11px 13px;
            color: #0d1c2e;
            font-size: 14px;
            line-height: 20px;
            transition:
                border-color .15s ease,
                box-shadow .15s ease,
                background-color .15s ease;
        }

        .profile-field:hover {
            border-color: #c3c6ce;
        }

        .profile-field:focus {
            border-color: #0f2942;
            background: #ffffff;
            box-shadow: 0 0 0 3px rgba(15, 41, 66, .10);
            outline: none;
        }

        .profile-button {
            min-height: 44px;
            border-radius: 10px;
            padding: 10px 16px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            font-size: 13px;
            font-weight: 600;
            transition:
                background-color .15s ease,
                transform .15s ease,
                box-shadow .15s ease;
        }

        .profile-button:active {
            transform: translateY(1px);
        }

        .profile-button-primary {
            color: #ffffff;
            background: #0f2942;
            box-shadow: 0 4px 12px rgba(15, 41, 66, .16);
        }

        .profile-button-primary:hover {
            background: #001428;
        }

        .profile-button-secondary {
            color: #001428;
            background: #eff4ff;
        }

        .profile-button-secondary:hover {
            background: #e6eeff;
        }

        .profile-tab {
            min-height: 44px;
            border-radius: 10px;
            padding: 9px 14px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 7px;
            white-space: nowrap;
            font-size: 13px;
            font-weight: 600;
            color: #43474d;
            transition: background-color .15s ease, color .15s ease;
        }

        .profile-tab:hover {
            background: #eff4ff;
        }

        .profile-tab.active {
            color: #ffffff;
            background: #001428;
        }

        .profile-info {
            border-radius: 12px;
            background: #eff4ff;
            padding: 14px;
            min-height: 82px;
        }

        .profile-alert {
            border-radius: 12px;
            padding: 13px 15px;
            display: flex;
            align-items: flex-start;
            gap: 10px;
            font-size: 13px;
            line-height: 19px;
        }

        .profile-alert-warning {
            background: rgba(215, 117, 3, .10);
            color: #4b2a00;
        }

        .profile-alert-success {
            background: rgba(0, 106, 97, .10);
            color: #00574f;
        }

        .profile-alert-info {
            background: #eff4ff;
            color: #0d1c2e;
        }

        .profile-alert-error {
            background: rgba(186, 26, 26, .08);
            color: #8a1111;
        }

        .password-wrapper {
            position: relative;
        }

        .password-wrapper .profile-field {
            padding-right: 48px;
        }

        .password-toggle {
            position: absolute;
            right: 8px;
            top: 50%;
            transform: translateY(-50%);
            width: 36px;
            height: 36px;
            border-radius: 8px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            color: #43474d;
        }

        .password-toggle:hover {
            background: #e6eeff;
            color: #001428;
        }

        .error-text {
            margin-top: 6px;
            color: #ba1a1a;
            font-size: 12px;
            line-height: 17px;
        }

        @media (max-width: 767px) {
            .profile-shell {
                width: min(100% - 20px, 1024px);
            }

            .profile-card {
                border-radius: 14px;
            }

            .profile-button {
                width: 100%;
            }

            .profile-tab {
                flex: 1 0 auto;
            }
        }

        @media (max-width: 379px) {
            .profile-shell {
                width: min(100% - 16px, 1024px);
            }

            .profile-tab {
                padding-inline: 10px;
                font-size: 12px;
            }

            .profile-hero-title {
                font-size: 22px !important;
                line-height: 29px !important;
            }
        }

        @media (prefers-reduced-motion: reduce) {
            html {
                scroll-behavior: auto;
            }

            *,
            *::before,
            *::after {
                transition-duration: .01ms !important;
                animation-duration: .01ms !important;
            }
        }
    </style>
</head>

<body>

@php($employee = $employee ?? auth()->user()->employee)

<header class="sticky top-0 z-30 border-b border-[#e6eeff] bg-white/95 backdrop-blur">
    <div class="profile-shell">
        <div class="flex min-h-[72px] items-center justify-between gap-4">
            <div class="flex min-w-0 items-center gap-3">
                <img
                    src="{{ asset('images/logo-inti.png') }}"
                    alt="PT. INTI"
                    class="h-8 w-auto shrink-0 object-contain"
                >

                <div class="hidden h-7 w-px bg-[#e6eeff] sm:block"></div>

                <div class="min-w-0">
                    <div class="text-[11px] font-semibold uppercase tracking-[.08em] text-[#74777e]">
                        Akun
                    </div>
                    <div class="truncate text-sm font-semibold text-[#001428]">
                        Profil Saya
                    </div>
                </div>
            </div>

            <a
                href="{{ auth()->user()->isDirekturUtama() ? route('direktur.dashboard') : (auth()->user()->isAdminSistem() ? route('admin.accounts.index') : route('dashboard')) }}"
                class="profile-button profile-button-secondary shrink-0"
            >
                <span class="material-symbols-outlined text-[18px]">arrow_back</span>
                <span class="hidden sm:inline">Kembali ke Dashboard</span>
                <span class="sm:hidden">Kembali</span>
            </a>
        </div>
    </div>
</header>

<main class="profile-shell py-6 sm:py-8">

    {{-- PROFILE HERO --}}
    <section class="mb-6 overflow-hidden rounded-[20px] bg-[#0f2942] text-white shadow-[0_12px_36px_rgba(13,28,46,.16)]">
        <div class="p-5 sm:p-7 lg:p-8">
            <div class="flex flex-col gap-5 sm:flex-row sm:items-center">

                <div class="flex h-20 w-20 shrink-0 items-center justify-center rounded-2xl bg-white/10 text-3xl font-semibold ring-1 ring-white/15 sm:h-24 sm:w-24 sm:text-4xl">
                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                </div>

                <div class="min-w-0">
                    <div class="mb-2 text-[11px] font-semibold uppercase tracking-[.09em] text-white/60">
                        Portal Kepegawaian & Infrastruktur Presensi
                    </div>

                    <h1 class="profile-hero-title text-[26px] font-semibold leading-8 tracking-tight sm:text-[30px] sm:leading-9">
                        {{ auth()->user()->name }}
                    </h1>

                    <div class="mt-3 flex flex-wrap gap-2">
                        <span class="inline-flex items-center gap-1.5 rounded-full bg-white/10 px-3 py-1.5 text-xs font-semibold text-white">
                            <span class="material-symbols-outlined text-[16px]">person</span>

                            @switch(auth()->user()->role)
                                @case('direktur_utama')
                                    Direktur Utama
                                    @break
                                @case('hr_admin')
                                    HR & Administrasi
                                    @break
                                @case('vp')
                                    Vice President (VP)
                                    @break
                                @case('karyawan')
                                    Karyawan
                                    @break
                                @case('admin_sistem')
                                    Administrator Sistem
                                    @break
                                @default
                                    {{ ucfirst(str_replace('_', ' ', auth()->user()->role ?? 'Pengguna')) }}
                            @endswitch
                        </span>

                        @if ($employee?->band_code !== null)
                            <span class="inline-flex items-center gap-1.5 rounded-full bg-[#86f2e4] px-3 py-1.5 text-xs font-semibold text-[#001428]">
                                <span class="material-symbols-outlined text-[16px]">workspace_premium</span>
                                Band {{ $employee->band_code }}
                            </span>
                        @endif
                    </div>
                </div>

            </div>
        </div>
    </section>

    {{-- SYSTEM MESSAGES --}}
    <div class="mb-6 space-y-3">

        @if (session('force-password-change'))
            <div class="profile-alert profile-alert-warning">
                <span class="material-symbols-outlined shrink-0 text-[20px]">warning</span>
                <div>
                    <strong class="font-semibold">Password sementara aktif.</strong>
                    Silakan ganti password Anda sekarang melalui tab
                    <strong>Keamanan</strong> sebelum melanjutkan aktivitas.
                </div>
            </div>
        @endif

        @if (session('status') === 'identity-managed-by-hr')
            <div class="profile-alert profile-alert-info">
                <span class="material-symbols-outlined shrink-0 text-[20px]">info</span>
                <div>
                    Nama dan email akun dikelola HR/Administrator Sistem agar identitas login tetap sesuai dengan data resmi.
                </div>
            </div>
        @endif

        @if (session('status') === 'profile-updated')
            <div class="profile-alert profile-alert-success">
                <span class="material-symbols-outlined shrink-0 text-[20px]">check_circle</span>
                <div>Profil berhasil diperbarui.</div>
            </div>
        @endif

        @if (session('status') === 'password-updated')
            <div class="profile-alert profile-alert-success">
                <span class="material-symbols-outlined shrink-0 text-[20px]">check_circle</span>
                <div>Password berhasil diperbarui.</div>
            </div>
        @endif

    </div>

    {{-- TABS --}}
    <div class="mb-6 overflow-x-auto rounded-xl border border-[#e6eeff] bg-white p-1.5 shadow-sm">
        <div class="flex min-w-max gap-1">
            <button
                type="button"
                onclick="showTab('biodata')"
                id="tabbtn-biodata"
                class="profile-tab active"
                aria-controls="tab-biodata"
                aria-selected="true"
            >
                <span class="material-symbols-outlined text-[18px]">badge</span>
                Biodata Akun
            </button>

            <button
                type="button"
                onclick="showTab('keamanan')"
                id="tabbtn-keamanan"
                class="profile-tab"
                aria-controls="tab-keamanan"
                aria-selected="false"
            >
                <span class="material-symbols-outlined text-[18px]">lock</span>
                Keamanan
            </button>
        </div>
    </div>

    {{-- BIODATA --}}
    <div id="tab-biodata" class="space-y-5">

        {{-- DATA KEPEGAWAIAN --}}
        <section class="profile-card p-5 sm:p-6 lg:p-7">
            <div class="mb-5 flex items-start gap-3">
                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-[#eff4ff] text-[#001428]">
                    <span class="material-symbols-outlined text-[21px]">badge</span>
                </div>

                <div class="min-w-0">
                    <h2 class="text-[17px] font-semibold leading-6 text-[#0d1c2e]">
                        Data Kepegawaian
                    </h2>
                    <p class="mt-1 text-xs leading-5 text-[#74777e]">
                        Jabatan dan divisi berdasarkan data karyawan yang terhubung.
                    </p>
                </div>
            </div>

            @if ($employee)

                <div class="grid grid-cols-1 gap-3 sm:grid-cols-2 lg:grid-cols-4">

                    <div class="profile-info">
                        <div class="mb-1 text-[10px] font-semibold uppercase tracking-[.06em] text-[#74777e]">
                            NIPEG
                        </div>
                        <div class="break-words text-sm font-semibold text-[#0d1c2e]">
                            {{ $employee->nipeg ?: '-' }}
                        </div>
                    </div>

                    <div class="profile-info">
                        <div class="mb-1 text-[10px] font-semibold uppercase tracking-[.06em] text-[#74777e]">
                            Band Code
                        </div>
                        <div class="text-sm font-semibold text-[#0d1c2e]">
                            {{ $employee->band_code !== null ? 'Band ' . $employee->band_code : '-' }}
                        </div>
                        <div class="mt-1 text-[11px] leading-4 text-[#74777e]">
                            Data kepegawaian
                        </div>
                    </div>

                    <div class="profile-info">
                        <div class="mb-1 text-[10px] font-semibold uppercase tracking-[.06em] text-[#74777e]">
                            Jabatan
                        </div>
                        <div class="break-words text-sm font-semibold text-[#0d1c2e]">
                            {{ $employee->jabatan ?: '-' }}
                        </div>
                    </div>

                    <div class="profile-info">
                        <div class="mb-1 text-[10px] font-semibold uppercase tracking-[.06em] text-[#74777e]">
                            Divisi
                        </div>
                        <div class="break-words text-sm font-semibold text-[#0d1c2e]">
                            {{ $employee->division?->nama ?? '-' }}
                        </div>
                    </div>

                </div>

                <div class="mt-4 flex items-start gap-2 text-xs leading-5 text-[#74777e]">
                    <span class="material-symbols-outlined mt-0.5 text-[17px]">info</span>
                    <span>
                        Data kepegawaian dikelola berdasarkan data resmi perusahaan dan tidak dapat diubah sendiri melalui halaman profil.
                    </span>
                </div>

            @else

                <div class="rounded-xl border border-[#e6eeff] bg-[#f8f9ff] p-4 text-sm leading-6 text-[#43474d]">
                    Akun ini belum dihubungkan ke data karyawan. NIPEG, Band Code, Jabatan,
                    dan Divisi hanya tersedia untuk akun yang memiliki relasi data karyawan.
                    Jika relasi perlu diperbarui, hubungi HR atau Administrator Sistem sesuai kewenangannya.
                </div>

            @endif
        </section>

        {{-- KONTAK --}}
        <section class="profile-card p-5 sm:p-6 lg:p-7">
            <div class="mb-5 flex items-start gap-3">
                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-[#eff4ff] text-[#001428]">
                    <span class="material-symbols-outlined text-[21px]">domain</span>
                </div>

                <div>
                    <h2 class="text-[17px] font-semibold leading-6 text-[#0d1c2e]">
                        Kontak & Lokasi Kantor
                    </h2>
                    <p class="mt-1 text-xs leading-5 text-[#74777e]">
                        Informasi kantor PT. INTI dan kontak akun.
                    </p>
                </div>
            </div>

            <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">

                <div class="profile-info sm:col-span-2">
                    <div class="mb-1 text-[10px] font-semibold uppercase tracking-[.06em] text-[#74777e]">
                        Alamat Kantor
                    </div>
                    <div class="text-sm font-semibold leading-6 text-[#0d1c2e]">
                        Jl. Moch. Toha No.77, Cigereleng, Kec. Regol,
                        Kota Bandung, Jawa Barat 40253
                    </div>
                </div>

                <div class="profile-info">
                    <div class="mb-1 text-[10px] font-semibold uppercase tracking-[.06em] text-[#74777e]">
                        Telepon Kantor
                    </div>
                    <div class="text-sm font-semibold text-[#0d1c2e]">
                        (022) 520-1501
                    </div>
                </div>

                <div class="profile-info">
                    <div class="mb-1 text-[10px] font-semibold uppercase tracking-[.06em] text-[#74777e]">
                        Email Korporat Anda
                    </div>
                    <div class="break-all text-sm font-semibold text-[#0d1c2e]">
                        {{ auth()->user()->email }}
                    </div>
                </div>

            </div>
        </section>

        {{-- DATA AKUN --}}
        <section class="profile-card p-5 sm:p-6 lg:p-7">
            <div class="mb-5 flex items-start gap-3">
                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-[#eff4ff] text-[#001428]">
                    <span class="material-symbols-outlined text-[21px]">account_circle</span>
                </div>

                <div>
                    <h2 class="text-[17px] font-semibold leading-6 text-[#0d1c2e]">
                        Data Akun
                    </h2>
                    <p class="mt-1 text-xs leading-5 text-[#74777e]">
                        Informasi yang digunakan untuk mengakses sistem.
                    </p>
                </div>
            </div>

            <div class="grid grid-cols-1 gap-3 md:grid-cols-2">

                <div class="profile-info">
                    <div class="mb-1 text-[10px] font-semibold uppercase tracking-[.06em] text-[#74777e]">
                        Role
                    </div>

                    <div class="text-sm font-semibold text-[#0d1c2e]">
                        @switch(auth()->user()->role)
                            @case('direktur_utama')
                                Direktur Utama
                                @break
                            @case('hr_admin')
                                HR & Administrasi
                                @break
                            @case('vp')
                                Vice President (VP)
                                @break
                            @case('karyawan')
                                Karyawan
                                @break
                            @case('admin_sistem')
                                Administrator Sistem
                                @break
                            @default
                                {{ ucfirst(str_replace('_', ' ', auth()->user()->role ?? 'Pengguna')) }}
                        @endswitch
                    </div>

                    <div class="mt-1 text-[11px] leading-4 text-[#74777e]">
                        Diatur administrator.
                    </div>
                </div>

                <div class="profile-info">
                    <div class="mb-1 text-[10px] font-semibold uppercase tracking-[.06em] text-[#74777e]">
                        Terdaftar Sejak
                    </div>

                    <div class="text-sm font-semibold text-[#0d1c2e]">
                        {{ auth()->user()->created_at->translatedFormat('d F Y') }}
                    </div>

                    <div class="mt-1 text-[11px] leading-4 text-[#74777e]">
                        Tanggal akun dibuat di sistem.
                    </div>
                </div>

            </div>

            @if ($employee)

                <div class="mt-5 grid grid-cols-1 gap-4 md:grid-cols-2">

                    <div>
                        <label class="mb-1.5 block text-xs font-semibold text-[#0d1c2e]">
                            Nama Lengkap
                        </label>

                        <div class="profile-field flex items-center bg-[#eff4ff]">
                            {{ auth()->user()->name }}
                        </div>
                    </div>

                    <div>
                        <label class="mb-1.5 block text-xs font-semibold text-[#0d1c2e]">
                            Email Login
                        </label>

                        <div class="profile-field flex items-center break-all bg-[#eff4ff]">
                            {{ auth()->user()->email }}
                        </div>
                    </div>

                </div>

                <div class="mt-4 flex items-start gap-2 text-xs leading-5 text-[#74777e]">
                    <span class="material-symbols-outlined mt-0.5 text-[17px]">admin_panel_settings</span>
                    <span>
                        Identitas akun karyawan dikelola HR. Gunakan tab <strong>Keamanan</strong>
                        untuk mengganti password.
                    </span>
                </div>

            @else

                <form
                    method="POST"
                    action="{{ route('profile.update') }}"
                    class="mt-5 space-y-4"
                >
                    @csrf
                    @method('PATCH')

                    <div>
                        <label for="name" class="mb-1.5 block text-xs font-semibold text-[#0d1c2e]">
                            Nama Lengkap <span class="text-[#ba1a1a]">*</span>
                        </label>

                        <input
                            id="name"
                            type="text"
                            name="name"
                            value="{{ old('name', auth()->user()->name) }}"
                            class="profile-field"
                            autocomplete="name"
                            required
                        >

                        @error('name')
                            <p class="error-text">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="email" class="mb-1.5 block text-xs font-semibold text-[#0d1c2e]">
                            Email <span class="text-[#ba1a1a]">*</span>
                        </label>

                        <input
                            id="email"
                            type="email"
                            name="email"
                            value="{{ old('email', auth()->user()->email) }}"
                            class="profile-field"
                            autocomplete="email"
                            required
                        >

                        @error('email')
                            <p class="error-text">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="pt-1">
                        <button
                            type="submit"
                            class="profile-button profile-button-primary"
                        >
                            <span class="material-symbols-outlined text-[18px]">save</span>
                            Simpan Perubahan
                        </button>
                    </div>
                </form>

            @endif
        </section>

    </div>

    {{-- KEAMANAN --}}
    <div id="tab-keamanan" class="hidden space-y-5">

        <section class="profile-card p-5 sm:p-6 lg:p-7">

            <div class="mb-6 flex items-start gap-3">
                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-[#eff4ff] text-[#001428]">
                    <span class="material-symbols-outlined text-[21px]">phonelink_lock</span>
                </div>

                <div>
                    <h2 class="text-[17px] font-semibold leading-6 text-[#0d1c2e]">
                        Ganti Password
                    </h2>
                    <p class="mt-1 text-xs leading-5 text-[#74777e]">
                        Gunakan password yang panjang, unik, dan tidak mudah ditebak.
                    </p>
                </div>
            </div>

            <div class="mb-5 rounded-xl bg-[#eff4ff] p-4">
                <div class="flex items-start gap-2.5">
                    <span class="material-symbols-outlined mt-0.5 text-[19px] text-[#001428]">
                        security
                    </span>

                    <div class="text-xs leading-5 text-[#43474d]">
                        <strong class="font-semibold text-[#0d1c2e]">Keamanan akun</strong><br>
                        Jangan gunakan password yang sama dengan akun lain.
                        Hindari menggunakan informasi pribadi yang mudah diketahui.
                    </div>
                </div>
            </div>

            <form
                method="POST"
                action="{{ route('password.update') }}"
                class="max-w-xl space-y-4"
            >
                @csrf
                @method('PUT')

                <div>
                    <label
                        for="current_password"
                        class="mb-1.5 block text-xs font-semibold text-[#0d1c2e]"
                    >
                        Password Saat Ini
                    </label>

                    <div class="password-wrapper">
                        <input
                            id="current_password"
                            type="password"
                            name="current_password"
                            autocomplete="current-password"
                            class="profile-field"
                        >

                        <button
                            type="button"
                            class="password-toggle"
                            onclick="togglePassword('current_password', this)"
                            aria-label="Tampilkan password"
                        >
                            <span class="material-symbols-outlined text-[19px]">
                                visibility
                            </span>
                        </button>
                    </div>

                    @error('current_password', 'updatePassword')
                        <p class="error-text">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label
                        for="password"
                        class="mb-1.5 block text-xs font-semibold text-[#0d1c2e]"
                    >
                        Password Baru
                    </label>

                    <div class="password-wrapper">
                        <input
                            id="password"
                            type="password"
                            name="password"
                            autocomplete="new-password"
                            class="profile-field"
                        >

                        <button
                            type="button"
                            class="password-toggle"
                            onclick="togglePassword('password', this)"
                            aria-label="Tampilkan password"
                        >
                            <span class="material-symbols-outlined text-[19px]">
                                visibility
                            </span>
                        </button>
                    </div>

                    @error('password', 'updatePassword')
                        <p class="error-text">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label
                        for="password_confirmation"
                        class="mb-1.5 block text-xs font-semibold text-[#0d1c2e]"
                    >
                        Konfirmasi Password Baru
                    </label>

                    <div class="password-wrapper">
                        <input
                            id="password_confirmation"
                            type="password"
                            name="password_confirmation"
                            autocomplete="new-password"
                            class="profile-field"
                        >

                        <button
                            type="button"
                            class="password-toggle"
                            onclick="togglePassword('password_confirmation', this)"
                            aria-label="Tampilkan password"
                        >
                            <span class="material-symbols-outlined text-[19px]">
                                visibility
                            </span>
                        </button>
                    </div>

                    @error('password_confirmation', 'updatePassword')
                        <p class="error-text">{{ $message }}</p>
                    @enderror
                </div>

                <div class="pt-2">
                    <button
                        type="submit"
                        class="profile-button profile-button-primary"
                    >
                        <span class="material-symbols-outlined text-[18px]">lock_reset</span>
                        Perbarui Password
                    </button>
                </div>

            </form>

        </section>

    </div>

</main>

<script>
    function showTab(tab) {
        const tabs = ['biodata', 'keamanan'];

        tabs.forEach(function (item) {
            const isActive = item === tab;

            const panel = document.getElementById('tab-' + item);
            const button = document.getElementById('tabbtn-' + item);

            if (panel) {
                panel.classList.toggle('hidden', !isActive);
            }

            if (button) {
                button.classList.toggle('active', isActive);
                button.setAttribute('aria-selected', isActive ? 'true' : 'false');
            }
        });

        if (window.location.hash !== '#' + tab) {
            history.replaceState(null, '', '#' + tab);
        }
    }

    function togglePassword(inputId, button) {
        const input = document.getElementById(inputId);

        if (!input || !button) {
            return;
        }

        const icon = button.querySelector('.material-symbols-outlined');

        if (input.type === 'password') {
            input.type = 'text';

            if (icon) {
                icon.textContent = 'visibility_off';
            }

            button.setAttribute('aria-label', 'Sembunyikan password');
        } else {
            input.type = 'password';

            if (icon) {
                icon.textContent = 'visibility';
            }

            button.setAttribute('aria-label', 'Tampilkan password');
        }
    }

    document.addEventListener('DOMContentLoaded', function () {
        const initialTab =
            window.location.hash === '#keamanan'
                ? 'keamanan'
                : 'biodata';

        showTab(initialTab);
    });
</script>

</body>
</html>
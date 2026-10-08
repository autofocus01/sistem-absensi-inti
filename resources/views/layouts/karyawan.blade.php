<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0, viewport-fit=cover"
    >

    <meta name="color-scheme" content="light">

    <title>@yield('title', 'Absensi Saya - PT. INTI')</title>

    <link href="https://fonts.googleapis.com" rel="preconnect">
    <link crossorigin href="https://fonts.gstatic.com" rel="preconnect">

    <link
        href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200"
        rel="stylesheet"
    >

    <link
        href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap"
        rel="stylesheet"
    >

    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>

    <script>
        tailwind.config = {
            darkMode: 'class',

            theme: {
                extend: {
                    colors: {
                        'on-surface-variant': '#43474d',
                        'primary': '#001428',
                        'on-primary': '#ffffff',

                        'surface-container-low': '#eff4ff',
                        'on-background': '#0d1c2e',
                        'surface-container-highest': '#d5e3fc',

                        'on-surface': '#0d1c2e',
                        'outline-variant': '#c3c6ce',

                        'secondary': '#006a61',
                        'error-container': '#ffdad6',

                        'background': '#f8f9ff',
                        'surface-bright': '#f8f9ff',
                        'surface': '#f8f9ff',
                        'outline': '#74777e',

                        'error': '#ba1a1a',

                        'surface-container-lowest': '#ffffff',
                        'primary-container': '#0f2942',

                        'surface-container': '#e6eeff',
                        'surface-container-high': '#dce9ff',

                        'secondary-container': '#86f2e4'
                    },

                    spacing: {
                        'space-2xl': '3rem',
                        'space-xl': '2rem',
                        'space-lg': '1.5rem',
                        'space-base': '1rem',
                        'space-md': '0.75rem',
                        'space-sm': '0.5rem',
                        'space-xs': '0.25rem'
                    },

                    fontFamily: {
                        body: ['Plus Jakarta Sans', 'sans-serif'],
                        title: ['Plus Jakarta Sans', 'sans-serif']
                    },

                    fontSize: {
                        'body-md': [
                            '14px',
                            {
                                lineHeight: '20px'
                            }
                        ],

                        'body-sm': [
                            '12px',
                            {
                                lineHeight: '16px'
                            }
                        ],

                        'title-sm': [
                            '14px',
                            {
                                lineHeight: '20px',
                                fontWeight: '600'
                            }
                        ],

                        'headline-md': [
                            '22px',
                            {
                                lineHeight: '28px',
                                fontWeight: '600'
                            }
                        ],

                        'headline-lg': [
                            '28px',
                            {
                                lineHeight: '36px',
                                fontWeight: '600'
                            }
                        ]
                    }
                }
            }
        }
    </script>

    <style>
        :root {
            color-scheme: light;
            --inti-header-height: 72px;
            --inti-sidebar-width: 280px;
            --inti-content-max-width: 1600px;
            --inti-radius: 14px;
        }

        @layer base {
            html,
            body {
                margin: 0;
                padding: 0;
                min-height: 100%;
            }

            html {
                background: #f8f9ff;
                color-scheme: light;
                scroll-behavior: smooth;
            }

            body {
                min-height: 100vh;
                background: #f8f9ff;
                color: #0d1c2e;
                overscroll-behavior-x: none;
                -webkit-font-smoothing: antialiased;
                -moz-osx-font-smoothing: grayscale;
            }

            button,
            a,
            input,
            select,
            textarea {
                -webkit-tap-highlight-color: transparent;
            }
        }

        /*
         * Material Symbols
         */
        .material-symbols-outlined {
            font-family: 'Material Symbols Outlined';
            font-weight: normal;
            font-style: normal;
            font-size: 24px;
            line-height: 1;
            letter-spacing: normal;
            text-transform: none;
            display: inline-block;
            white-space: nowrap;
            word-wrap: normal;
            direction: ltr;
            -webkit-font-feature-settings: 'liga';
            -webkit-font-smoothing: antialiased;
            font-feature-settings: 'liga';
            font-variation-settings:
                'FILL' 0,
                'wght' 400,
                'GRAD' 0,
                'opsz' 24;
        }

        /*
         * Accessibility
         */
        :where(
            a,
            button,
            input,
            select,
            textarea
        ):focus-visible {
            outline: 3px solid rgba(0, 106, 97, 0.30);
            outline-offset: 2px;
        }

        /*
         * Jangan menghilangkan scrollbar secara global.
         * Area yang memang membutuhkan overflow akan tetap
         * menggunakan scrollbar browser secara normal.
         */
        ::selection {
            background: rgba(0, 106, 97, 0.16);
            color: #0d1c2e;
        }

        /*
         * Layout helpers
         */
        .inti-shell {
            min-height: 100vh;
        }

        .inti-content {
            width: 100%;
            max-width: var(--inti-content-max-width);
            margin-inline: auto;
        }

        /*
         * Sidebar
         */
        #sidebar {
            width: var(--inti-sidebar-width);
            transition:
                transform 220ms ease,
                box-shadow 220ms ease;
        }

        #sidebar-backdrop {
            transition: opacity 180ms ease;
        }

        /*
         * Mobile/tablet:
         * sidebar tertutup secara default.
         */
        @media (max-width: 1023px) {
            #sidebar {
                width: min(82vw, 320px);
            }

            .inti-main-shell {
                padding-left: 0 !important;
            }

            .inti-header {
                left: 0 !important;
            }
        }

        /*
         * Tablet
         * 768px - 1023px
         */
        @media (min-width: 768px) and (max-width: 1023px) {
            :root {
                --inti-header-height: 72px;
            }

            .inti-main {
                padding-inline: 24px !important;
            }

            .inti-header {
                padding-inline: 24px !important;
            }
        }

        /*
         * Mobile
         */
        @media (max-width: 767px) {
            :root {
                --inti-header-height: 64px;
            }

            .inti-header {
                height: 64px !important;
                padding-inline: 16px !important;
            }

            .inti-main {
                padding-top: 88px !important;
                padding-inline: 16px !important;
                padding-bottom: 32px !important;
            }

            .inti-sidebar-header {
                height: 68px !important;
            }

            .inti-sidebar-footer {
                padding: 16px !important;
            }

            .inti-sidebar-user {
                min-width: 0;
            }
        }

        /*
         * Small phone
         */
        @media (max-width: 379px) {
            .inti-main {
                padding-inline: 12px !important;
            }

            .inti-header {
                padding-inline: 12px !important;
            }

            .inti-sidebar-nav {
                padding-inline: 12px !important;
            }

            .inti-sidebar-footer {
                padding: 12px !important;
            }
        }

        /*
         * Reduced motion
         */
        @media (prefers-reduced-motion: reduce) {
            html {
                scroll-behavior: auto;
            }

            *,
            *::before,
            *::after {
                transition-duration: 0.01ms !important;
                animation-duration: 0.01ms !important;
                animation-iteration-count: 1 !important;
            }
        }

        /*
         * Prevent accidental horizontal overflow from page content.
         */
        .inti-main {
            min-width: 0;
            overflow-x: clip;
        }

        /*
         * Content generated by individual pages may contain
         * responsive tables/charts. They should scroll inside
         * their own containers instead of expanding the page.
         */
        .inti-main table {
            max-width: 100%;
        }

        /*
         * Better mobile touch targets.
         */
        @media (max-width: 767px) {
            .inti-touch-target {
                min-height: 44px;
            }
        }
    </style>
</head>

<body class="bg-background font-body text-body-md text-on-surface antialiased">

    <!--
        Mobile / Tablet Sidebar Backdrop
    -->
    <div
        id="sidebar-backdrop"
        onclick="toggleSidebar(false)"
        class="fixed inset-0 z-40 hidden bg-slate-950/40 backdrop-blur-[2px] md:block lg:hidden"
        aria-hidden="true"
    ></div>

    <!--
        Sidebar
    -->
    <aside
        id="sidebar"
        class="
            fixed
            left-0
            top-0
            z-50
            flex
            h-screen
            -translate-x-full
            flex-col
            justify-between
            overflow-hidden
            bg-surface-container-lowest
            shadow-[0_8px_30px_rgba(13,28,46,0.08)]
            transition-transform
            duration-200
            ease-in-out
            lg:translate-x-0
        "
        aria-label="Navigasi utama"
    >

        <!-- Sidebar top -->
        <div class="flex min-h-0 flex-1 flex-col">

            <!-- Logo -->
            <div
                class="
                    inti-sidebar-header
                    flex
                    h-[72px]
                    shrink-0
                    items-center
                    border-b
                    border-surface-container-low/70
                    px-5
                    sm:px-6
                "
            >
                <a
                    href="{{ route('dashboard') }}"
                    class="flex min-w-0 items-center gap-3 rounded-lg"
                    aria-label="Dashboard PT. INTI"
                >
                    <img
                        src="{{ asset('images/logo-inti.png') }}"
                        alt="PT. INTI"
                        class="h-8 w-auto max-w-[150px] object-contain sm:h-9"
                    >
                </a>
            </div>

            <!-- Navigation -->
            <div
                class="
                    inti-sidebar-nav
                    min-h-0
                    flex-1
                    overflow-y-auto
                    px-4
                    py-5
                    sm:px-5
                "
            >

                <div class="px-2 pb-3">
                    <span
                        class="
                            text-[10px]
                            font-bold
                            uppercase
                            tracking-[0.12em]
                            text-on-surface-variant
                        "
                    >
                        Portal Karyawan
                    </span>
                </div>

                <nav
                    class="flex flex-col gap-1"
                    aria-label="Menu karyawan"
                >

                    <!-- Dashboard -->
                    <a
                        href="{{ route('dashboard') }}"
                        class="
                            inti-touch-target
                            group
                            flex
                            min-h-[42px]
                            items-center
                            gap-3
                            rounded-xl
                            px-3
                            py-2.5
                            font-title-sm
                            transition-colors
                            duration-150
                            {{ request()->routeIs('dashboard')
                                ? 'bg-primary-container text-on-primary shadow-sm'
                                : 'text-on-surface hover:bg-surface-container-low'
                            }}
                        "
                    >
                        <span
                            class="
                                material-symbols-outlined
                                shrink-0
                                text-[21px]
                            "
                            aria-hidden="true"
                        >
                            dashboard
                        </span>

                        <span class="truncate">
                            Dashboard
                        </span>
                    </a>

                    <!-- Absensi -->
                    <a
                        href="{{ route('attendance.my') }}"
                        class="
                            inti-touch-target
                            group
                            flex
                            min-h-[42px]
                            items-center
                            gap-3
                            rounded-xl
                            px-3
                            py-2.5
                            font-title-sm
                            transition-colors
                            duration-150
                            {{ request()->routeIs('attendance.my*')
                                ? 'bg-primary-container text-on-primary shadow-sm'
                                : 'text-on-surface hover:bg-surface-container-low'
                            }}
                        "
                    >
                        <span
                            class="
                                material-symbols-outlined
                                shrink-0
                                text-[21px]
                            "
                            aria-hidden="true"
                        >
                            event_available
                        </span>

                        <span class="truncate">
                            Absensi Saya
                        </span>
                    </a>

                    <!-- Overtime -->
                    <a
                        href="{{ route('overtime.index') }}"
                        class="
                            inti-touch-target
                            group
                            flex
                            min-h-[42px]
                            items-center
                            gap-3
                            rounded-xl
                            px-3
                            py-2.5
                            font-title-sm
                            transition-colors
                            duration-150
                            {{ request()->routeIs('overtime.index')
                                ? 'bg-primary-container text-on-primary shadow-sm'
                                : 'text-on-surface hover:bg-surface-container-low'
                            }}
                        "
                    >
                        <span
                            class="
                                material-symbols-outlined
                                shrink-0
                                text-[21px]
                            "
                            aria-hidden="true"
                        >
                            more_time
                        </span>

                        <span class="truncate">
                            Overtime
                        </span>
                    </a>

                    <!-- Timesheet -->
                    <a
                        href="{{ route('timesheet.index') }}"
                        class="
                            inti-touch-target
                            group
                            flex
                            min-h-[42px]
                            items-center
                            gap-3
                            rounded-xl
                            px-3
                            py-2.5
                            font-title-sm
                            transition-colors
                            duration-150
                            {{ request()->routeIs('timesheet.*')
                                ? 'bg-primary-container text-on-primary shadow-sm'
                                : 'text-on-surface hover:bg-surface-container-low'
                            }}
                        "
                    >
                        <span
                            class="
                                material-symbols-outlined
                                shrink-0
                                text-[21px]
                            "
                            aria-hidden="true"
                        >
                            calendar_clock
                        </span>

                        <span class="truncate">
                            Riwayat &amp; Timesheet
                        </span>
                    </a>

                </nav>
            </div>
        </div>

        <!-- Sidebar footer -->
        <div
            class="
                inti-sidebar-footer
                shrink-0
                border-t
                border-surface-container-low
                bg-surface-container-lowest
                p-4
                sm:p-5
            "
        >

            <!-- User -->
            <div
                class="
                    inti-sidebar-user
                    mb-3
                    flex
                    items-center
                    gap-3
                    px-1
                "
            >
                <div
                    class="
                        flex
                        h-10
                        w-10
                        shrink-0
                        items-center
                        justify-center
                        rounded-full
                        bg-primary-container
                        text-sm
                        font-bold
                        text-on-primary
                    "
                    aria-hidden="true"
                >
                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                </div>

                <div class="min-w-0 flex-1">
                    <div
                        class="
                            truncate
                            text-[13px]
                            font-semibold
                            leading-5
                            text-on-surface
                        "
                    >
                        {{ auth()->user()->name }}
                    </div>

                    <div
                        class="
                            truncate
                            text-[11px]
                            leading-4
                            text-on-surface-variant
                        "
                    >
                        {{ auth()->user()->email }}
                    </div>
                </div>
            </div>

            <!-- Profile -->
            <a
                href="{{ route('profile.edit') }}"
                class="
                    inti-touch-target
                    flex
                    min-h-[42px]
                    w-full
                    items-center
                    gap-3
                    rounded-xl
                    px-3
                    py-2.5
                    font-body-sm
                    text-on-surface
                    transition-colors
                    duration-150
                    hover:bg-surface-container-low
                "
            >
                <span
                    class="material-symbols-outlined text-[20px]"
                    aria-hidden="true"
                >
                    account_circle
                </span>

                <span>
                    Profil Saya
                </span>
            </a>

            <!-- Logout -->
            <form
                method="POST"
                action="{{ route('logout') }}"
            >
                @csrf

                <button
                    type="submit"
                    class="
                        inti-touch-target
                        mt-1
                        flex
                        min-h-[42px]
                        w-full
                        items-center
                        gap-3
                        rounded-xl
                        px-3
                        py-2.5
                        text-left
                        font-body-sm
                        text-error
                        transition-colors
                        duration-150
                        hover:bg-error/5
                    "
                >
                    <span
                        class="material-symbols-outlined text-[20px]"
                        aria-hidden="true"
                    >
                        logout
                    </span>

                    <span>
                        Logout
                    </span>
                </button>
            </form>
        </div>
    </aside>

    <!--
        Main shell
    -->
    <div
        class="
            inti-main-shell
            flex
            min-h-screen
            flex-col
            lg:pl-[280px]
        "
    >

        <!--
            Top header
        -->
        <header
            class="
                inti-header
                fixed
                left-0
                right-0
                top-0
                z-30
                flex
                h-[72px]
                items-center
                border-b
                border-surface-container-low/70
                bg-surface-container-lowest/95
                px-4
                shadow-[0_1px_8px_rgba(13,28,46,0.04)]
                backdrop-blur-xl
                lg:left-[280px]
                lg:px-8
            "
        >
            <div
                class="
                    flex
                    w-full
                    min-w-0
                    items-center
                    justify-between
                    gap-3
                "
            >

                <!-- Left -->
                <div class="flex min-w-0 items-center gap-3">

                    <!-- Mobile / tablet menu -->
                    <button
                        type="button"
                        onclick="toggleSidebar(true)"
                        class="
                            inti-touch-target
                            flex
                            h-10
                            w-10
                            shrink-0
                            items-center
                            justify-center
                            rounded-xl
                            text-primary
                            transition-colors
                            hover:bg-surface-container-low
                            lg:hidden
                        "
                        aria-label="Buka menu"
                        aria-controls="sidebar"
                        aria-expanded="false"
                    >
                        <span
                            class="material-symbols-outlined text-[23px]"
                            aria-hidden="true"
                        >
                            menu
                        </span>
                    </button>

                    <span
                        class="
                            material-symbols-outlined
                            hidden
                            shrink-0
                            text-[19px]
                            text-secondary
                            sm:inline-flex
                        "
                        aria-hidden="true"
                    >
                        badge
                    </span>

                    <span
                        class="
                            truncate
                            text-[13px]
                            font-semibold
                            text-primary
                            sm:text-sm
                        "
                    >
                        Portal Karyawan
                    </span>
                </div>

            </div>
        </header>

        <!--
            Main content
        -->
        <main
            class="
                inti-main
                min-h-screen
                flex-1
                bg-background
                px-4
                pb-12
                pt-24
                sm:px-6
                sm:pt-28
                lg:px-8
            "
        >

            <div class="inti-content">

                <!-- Session status -->
                @if (session('status'))
                    <div
                        class="
                            mb-4
                            flex
                            items-start
                            gap-3
                            rounded-xl
                            border
                            border-secondary/15
                            bg-secondary/10
                            px-4
                            py-3
                            text-sm
                            font-semibold
                            leading-5
                            text-secondary
                            shadow-sm
                        "
                        role="status"
                    >
                        <span
                            class="
                                material-symbols-outlined
                                mt-0.5
                                shrink-0
                                text-[20px]
                            "
                            aria-hidden="true"
                        >
                            check_circle
                        </span>

                        <span class="min-w-0">
                            {{ session('status') }}
                        </span>
                    </div>
                @endif

                <!-- Validation errors -->
                @if ($errors->any())
                    <div
                        class="
                            mb-4
                            rounded-xl
                            border
                            border-error/15
                            bg-error/5
                            px-4
                            py-3
                            text-sm
                            leading-5
                            text-error
                            shadow-sm
                        "
                        role="alert"
                    >
                        <div class="mb-1 flex items-center gap-2 font-semibold">
                            <span
                                class="
                                    material-symbols-outlined
                                    text-[20px]
                                "
                                aria-hidden="true"
                            >
                                error
                            </span>

                            <span>
                                Terjadi kesalahan
                            </span>
                        </div>

                        <ul class="ml-7 list-disc space-y-1">
                            @foreach ($errors->all() as $error)
                                <li>
                                    {{ $error }}
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @yield('content')

            </div>
        </main>
    </div>

    <script>
        function toggleSidebar(forceOpen = null) {
            const sidebar = document.getElementById('sidebar');
            const backdrop = document.getElementById('sidebar-backdrop');

            if (!sidebar || !backdrop) {
                return;
            }

            const isCurrentlyOpen =
                !sidebar.classList.contains('-translate-x-full');

            const shouldOpen =
                forceOpen === null
                    ? !isCurrentlyOpen
                    : forceOpen;

            if (shouldOpen) {
                sidebar.classList.remove('-translate-x-full');
                backdrop.classList.remove('hidden');
                document.body.classList.add('overflow-hidden');

                const menuButton = document.querySelector(
                    '[aria-controls="sidebar"]'
                );

                if (menuButton) {
                    menuButton.setAttribute('aria-expanded', 'true');
                }
            } else {
                sidebar.classList.add('-translate-x-full');
                backdrop.classList.add('hidden');
                document.body.classList.remove('overflow-hidden');

                const menuButton = document.querySelector(
                    '[aria-controls="sidebar"]'
                );

                if (menuButton) {
                    menuButton.setAttribute('aria-expanded', 'false');
                }
            }
        }

        /*
         * Tutup sidebar setelah memilih menu pada mobile/tablet.
         * Tidak memengaruhi navigasi atau route.
         */
        document.addEventListener('DOMContentLoaded', function () {
            const sidebar = document.getElementById('sidebar');

            if (!sidebar) {
                return;
            }

            const sidebarLinks = sidebar.querySelectorAll('a');

            sidebarLinks.forEach(function (link) {
                link.addEventListener('click', function () {
                    if (window.innerWidth < 1024) {
                        toggleSidebar(false);
                    }
                });
            });
        });

        /*
         * Jika viewport berubah dari mobile/tablet ke desktop,
         * reset state overlay tanpa mengubah route atau halaman.
         */
        window.addEventListener('resize', function () {
            if (window.innerWidth >= 1024) {
                const sidebar = document.getElementById('sidebar');
                const backdrop = document.getElementById('sidebar-backdrop');

                if (sidebar) {
                    sidebar.classList.remove('-translate-x-full');
                }

                if (backdrop) {
                    backdrop.classList.add('hidden');
                }

                document.body.classList.remove('overflow-hidden');

                const menuButton = document.querySelector(
                    '[aria-controls="sidebar"]'
                );

                if (menuButton) {
                    menuButton.setAttribute('aria-expanded', 'false');
                }
            }
        });

        /*
         * Escape untuk menutup sidebar pada mobile/tablet.
         */
        document.addEventListener('keydown', function (event) {
            if (event.key !== 'Escape') {
                return;
            }

            if (window.innerWidth >= 1024) {
                return;
            }

            toggleSidebar(false);
        });
    </script>

    @yield('scripts')

</body>
</html>
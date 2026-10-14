<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<script>
  (function () {
    const theme = localStorage.getItem('inti-theme') || 'system';
    const dark = theme === 'dark' || (theme === 'system' && window.matchMedia('(prefers-color-scheme: dark)').matches);
    document.documentElement.classList.toggle('dark', dark);
    document.documentElement.dataset.theme = theme;
  })();
</script>

<title>@yield('title', 'Sistem Absensi PT. INTI')</title>

<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" rel="stylesheet">
<link href="https://fonts.googleapis.com" rel="preconnect">
<link crossorigin href="https://fonts.gstatic.com" rel="preconnect">
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">

<link rel="stylesheet" href="{{ asset('css/p1-design-system.css') }}">
<link rel="stylesheet" href="{{ asset('css/p1-responsive-enterprise.css') }}">
<link rel="stylesheet" href="{{ asset('css/p1-hr-governance.css') }}">

<script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.4/dist/chart.umd.min.js"></script>

@include('partials.tailwind-config')


<style>
  @layer base {
    html, body {
      margin: 0;
      padding: 0;
    }

    body {
      overscroll-behavior: none;
    }
  }

  /* Keep scrollbars visible where content must be scrolled. */
  * {
    scrollbar-width: thin;
    scrollbar-color: #b7c3d2 transparent;
  }
  *::-webkit-scrollbar {
    width: 8px;
    height: 8px;
  }
  *::-webkit-scrollbar-thumb {
    background: #b7c3d2;
    border-radius: 999px;
  }
  *::-webkit-scrollbar-track {
    background: transparent;
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
    font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
  }
</style>

<style>
  html.dark {
    color-scheme: dark;
    --inti-primary: #dbe8f7;
    --inti-primary-container: #274563;
    --inti-secondary: #68d8cb;
    --inti-error: #ff8b84;
    --inti-warning: #f0b35a;
    --inti-background: #141c29;
    --inti-surface: #1b2636;
    --inti-surface-low: #202d40;
    --inti-surface-high: #2a3a50;
    --inti-text: #edf3fa;
    --inti-muted: #b7c3d2;
    --inti-outline: #52657d;
    --inti-shadow: 0 1px 10px rgba(0,0,0,.20);
    --inti-shadow-hover: 0 8px 24px rgba(0,0,0,.28);
  }

  html.dark body {
    background:#141c29 !important;
    color:#edf3fa !important;
  }

  html.dark .bg-background,
  html.dark .bg-surface,
  html.dark .bg-surface-bright {
    background-color:#141c29 !important;
  }

  html.dark .bg-surface-container-lowest {
    background-color:#1b2636 !important;
  }

  html.dark .bg-surface-container-low {
    background-color:#202d40 !important;
  }

  html.dark .bg-surface-container {
    background-color:#26364a !important;
  }

  html.dark .bg-surface-container-high {
    background-color:#2d4057 !important;
  }

  html.dark .bg-surface-container-highest {
    background-color:#344961 !important;
  }

  html.dark .text-on-surface,
  html.dark .text-primary {
    color:#edf3fa !important;
  }

  html.dark .text-on-surface-variant {
    color:#b7c3d2 !important;
  }

  html.dark .text-secondary {
    color:#65d8cc !important;
  }

  html.dark .border-outline,
  html.dark .border-outline-variant,
  html.dark .border-outline-variant\/50 {
    border-color:#40516a !important;
  }

  html.dark .bg-white {
    background-color:#1b2636 !important;
  }

  html.dark .bg-gray-50 {
    background-color:#202d40 !important;
  }

  html.dark .bg-gray-800 {
    background-color:#1b2636 !important;
  }

  html.dark .bg-gray-700 {
    background-color:#26364a !important;
  }

  html.dark .text-gray-800,
  html.dark .text-gray-700,
  html.dark .text-gray-900 {
    color:#edf3fa !important;
  }

  html.dark .text-gray-500,
  html.dark .text-gray-600 {
    color:#b7c3d2 !important;
  }

  html.dark .bg-surface-container-lowest\/90 {
    background-color:rgba(17,27,43,.92) !important;
  }

  html.dark .bg-amber-50 {
    background-color:#3a2d16 !important;
  }

  html.dark .text-amber-900 {
    color:#f7d58f !important;
  }

  html.dark .border-amber-200 {
    border-color:#765b24 !important;
  }

  html.dark .bg-secondary\/10 {
    background-color:rgba(101,216,204,.10) !important;
  }

  html.dark .bg-error\/10 {
    background-color:rgba(255,110,110,.10) !important;
  }

  html.dark .border-gray-300,
  html.dark .border-gray-200 {
    border-color:#40516a !important;
  }

  html.dark .inti-table tbody tr:hover {
    background:#172337 !important;
  }

  html.dark input,
  html.dark select,
  html.dark textarea {
    background-color:#111b2b !important;
    color:#e5edf8 !important;
    border-color:#40516a !important;
  }

  html.dark input::placeholder,
  html.dark textarea::placeholder {
    color:#8291a8 !important;
  }

  html.dark .inti-card,
  html.dark .inti-table-wrap {
    background:#1b2636;
    border-color:#3c4d63;
  }

  html.dark .bg-primary {
    background-color:#355b7f !important;
  }

  html.dark .bg-primary-container {
    background-color:#355b7f !important;
  }

  html.dark .text-on-primary {
    color:#f8fbff !important;
  }

  html.dark .ring-surface-container-lowest {
    --tw-ring-color:#1b2636 !important;
  }

  html.dark .shadow-xl,
  html.dark .shadow-lg {
    box-shadow:0 12px 32px rgba(0,0,0,.32) !important;
  }

  html.dark #sidebar {
    box-shadow:2px 0 18px rgba(0,0,0,.24) !important;
  }

  html.dark #sidebar-backdrop {
    background:rgba(0,0,0,.58) !important;
  }

  .inti-preference-panel {
    min-width:250px;
  }

  .inti-preference-panel select {
    min-height:36px;
  }

  @media (max-width:640px) {
    .inti-preference-panel {
      min-width:220px;
    }
  }
</style>

</head>

<body class="bg-background font-body-md text-body-md text-on-surface antialiased">

<div
  id="sidebar-backdrop"
  onclick="toggleSidebar()"
  class="fixed inset-0 bg-black/40 z-40 hidden md:hidden"
></div>

<aside
  id="sidebar"
  class="fixed left-0 top-0 h-full w-72 bg-surface-container-lowest shadow-[0_1px_8px_rgba(0,0,0,0.04)] z-50 flex flex-col justify-between select-none transform -translate-x-full transition-transform duration-200 ease-in-out md:translate-x-0"
>

  <div class="flex flex-col overflow-y-auto">

    <div class="h-20 px-space-lg flex items-center gap-space-md border-b border-surface-container-low/60 shrink-0">

      <a
        href="#"
        onclick="window.location.reload(); return false;"
        class="flex items-center focus:outline-none transition-opacity hover:opacity-80"
        title="Refresh Halaman"
      >
        <img
          src="{{ asset('images/logo-inti.png') }}"
          alt="PT. INTI"
          class="h-8 w-auto object-contain"
        >
      </a>

    </div>

    <div class="px-space-md py-space-xs">

      @if (config('app.demo_mode', false))

        <a
          href="{{ route('demo.index') }}"
          class="mb-3 flex items-center gap-2 px-space-md py-space-sm rounded-lg border border-amber-200 bg-amber-50 text-amber-900 font-title-sm text-title-sm hover:bg-amber-100"
        >
          <span class="material-symbols-outlined text-[20px]">science</span>
          Mode Demo
        </a>

      @endif


      {{-- =========================================================
           KARYAWAN
           ========================================================= --}}

      @if(auth()->user()->isKaryawan())

        <div class="px-space-sm pb-space-xs flex items-center justify-between">

          <span class="font-label-sm text-label-sm text-on-surface-variant uppercase tracking-wider">
            Portal Karyawan
          </span>

          <span class="px-1.5 py-0.5 rounded text-[10px] font-semibold bg-surface-container-low text-on-surface-variant">
            KARYAWAN
          </span>

        </div>

        <nav class="flex flex-col gap-space-xs">

          <a
            href="{{ route('dashboard') }}"
            class="flex items-center gap-2 px-space-md py-space-sm rounded-lg font-title-sm text-title-sm
            {{ request()->routeIs('dashboard') ? 'bg-primary-container text-on-primary' : 'text-on-surface hover:bg-surface-container-low' }}"
          >
            <span class="material-symbols-outlined text-[20px]">dashboard</span>
            Dashboard
          </a>

          <a
            href="{{ route('attendance.my') }}"
            class="flex items-center gap-2 px-space-md py-space-sm rounded-lg font-title-sm text-title-sm
            {{ request()->routeIs('attendance.my*') ? 'bg-primary-container text-on-primary' : 'text-on-surface hover:bg-surface-container-low' }}"
          >
            <span class="material-symbols-outlined text-[20px]">event_available</span>
            Absensi Saya
          </a>

          <a
            href="{{ route('timesheet.index') }}"
            class="flex items-center gap-2 px-space-md py-space-sm rounded-lg font-title-sm text-title-sm
            {{ request()->routeIs('timesheet.*') ? 'bg-primary-container text-on-primary' : 'text-on-surface hover:bg-surface-container-low' }}"
          >
            <span class="material-symbols-outlined text-[20px]">calendar_clock</span>
            Riwayat &amp; Timesheet
          </a>

          <a
            href="{{ route('overtime.index') }}"
            class="flex items-center gap-2 px-space-md py-space-sm rounded-lg font-title-sm text-title-sm
            {{ request()->routeIs('overtime.index') ? 'bg-primary-container text-on-primary' : 'text-on-surface hover:bg-surface-container-low' }}"
          >
            <span class="material-symbols-outlined text-[20px]">more_time</span>
            Pengajuan Lembur
          </a>

          @if (Route::has('overtime.history'))

            <a
              href="{{ route('overtime.history') }}"
              class="flex items-center gap-2 px-space-md py-space-sm rounded-lg font-title-sm text-title-sm
              {{ request()->routeIs('overtime.history*') ? 'bg-primary-container text-on-primary' : 'text-on-surface hover:bg-surface-container-low' }}"
            >
              <span class="material-symbols-outlined text-[20px]">history</span>
              Riwayat Lembur
            </a>

          @endif

        </nav>


      {{-- =========================================================
           VP
           ========================================================= --}}

      @elseif(auth()->user()->isVp())

        <div class="px-space-sm pb-space-xs flex items-center justify-between">

          <span class="font-label-sm text-label-sm text-on-surface-variant uppercase tracking-wider">
            Portal VP Divisi
          </span>

          <span class="px-1.5 py-0.5 rounded text-[10px] font-semibold bg-surface-container-low text-on-surface-variant">
            VP
          </span>

        </div>

        <nav class="flex flex-col gap-space-xs">

          <a
            href="{{ route('dashboard') }}"
            class="flex items-center gap-2 px-space-md py-space-sm rounded-lg font-title-sm text-title-sm
            {{ request()->routeIs('dashboard') ? 'bg-primary-container text-on-primary' : 'text-on-surface hover:bg-surface-container-low' }}"
          >
            <span class="material-symbols-outlined text-[20px]">dashboard</span>
            Dashboard
          </a>

          <a
            href="{{ route('attendance.my') }}"
            class="flex items-center gap-2 px-space-md py-space-sm rounded-lg font-title-sm text-title-sm
            {{ request()->routeIs('attendance.my*') ? 'bg-primary-container text-on-primary' : 'text-on-surface hover:bg-surface-container-low' }}"
          >
            <span class="material-symbols-outlined text-[20px]">event_available</span>
            Absensi Saya
          </a>

          <a
            href="{{ route('timesheet.index') }}"
            class="flex items-center gap-2 px-space-md py-space-sm rounded-lg font-title-sm text-title-sm
            {{ request()->routeIs('timesheet.*') ? 'bg-primary-container text-on-primary' : 'text-on-surface hover:bg-surface-container-low' }}"
          >
            <span class="material-symbols-outlined text-[20px]">calendar_clock</span>
            Riwayat &amp; Timesheet
          </a>

          <a
            href="{{ route('overtime.vp') }}"
            class="flex items-center gap-2 px-space-md py-space-sm rounded-lg font-title-sm text-title-sm
            {{ request()->routeIs('overtime.vp*') ? 'bg-primary-container text-on-primary' : 'text-on-surface hover:bg-surface-container-low' }}"
          >
            <span class="material-symbols-outlined text-[20px]">approval</span>
            Persetujuan Lembur
          </a>

          <a
            href="{{ route('team-recap.index') }}"
            class="flex items-center gap-2 px-space-md py-space-sm rounded-lg font-title-sm text-title-sm
            {{ request()->routeIs('team-recap.*') ? 'bg-primary-container text-on-primary' : 'text-on-surface hover:bg-surface-container-low' }}"
          >
            <span class="material-symbols-outlined text-[20px]">summarize</span>
            Rekap Divisi
          </a>

        </nav>


      {{-- =========================================================
           HR ADMIN
           ========================================================= --}}

      @elseif(auth()->user()->isHrAdmin())

        <div class="px-space-sm pb-space-xs flex items-center justify-between">

          <span class="font-title-sm text-title-sm text-primary">
            Portal HR &amp; Administrasi
          </span>

          <span class="px-1.5 py-0.5 rounded text-[10px] font-semibold bg-surface-container-low text-on-surface-variant">
            HR
          </span>

        </div>

        <nav class="flex flex-col gap-space-xs">

          <a
            href="{{ route('employees.index') }}"
            class="flex items-center gap-2 px-space-md py-space-sm rounded-lg font-title-sm text-title-sm
            {{ request()->routeIs('employees.*') ? 'bg-primary-container text-on-primary' : 'text-on-surface hover:bg-surface-container-low' }}"
          >
            <span class="material-symbols-outlined text-[20px]">badge</span>
            Data Karyawan
          </a>

          <a
            href="{{ route('employee-accounts.index') }}"
            class="flex items-center gap-2 px-space-md py-space-sm rounded-lg font-title-sm text-title-sm
            {{ request()->routeIs('employee-accounts.*') ? 'bg-primary-container text-on-primary' : 'text-on-surface hover:bg-surface-container-low' }}"
          >
            <span class="material-symbols-outlined text-[20px]">manage_accounts</span>
            Kelola Akun Karyawan
          </a>

          <a
            href="{{ route('employees.import') }}"
            class="flex items-center gap-2 px-space-md py-space-sm rounded-lg font-title-sm text-title-sm
            {{ request()->routeIs('employees.import*') ? 'bg-primary-container text-on-primary' : 'text-on-surface hover:bg-surface-container-low' }}"
          >
            <span class="material-symbols-outlined text-[20px]">upload_file</span>
            Import Karyawan
          </a>

          <a
            href="{{ route('divisions.index') }}"
            class="flex items-center gap-2 px-space-md py-space-sm rounded-lg font-title-sm text-title-sm
            {{ request()->routeIs('divisions.*') ? 'bg-primary-container text-on-primary' : 'text-on-surface hover:bg-surface-container-low' }}"
          >
            <span class="material-symbols-outlined text-[20px]">account_tree</span>
            Data Divisi
          </a>

          <a
            href="{{ route('attendance.index') }}"
            class="flex items-center gap-2 px-space-md py-space-sm rounded-lg font-title-sm text-title-sm
            {{ request()->routeIs('attendance.*') ? 'bg-primary-container text-on-primary' : 'text-on-surface hover:bg-surface-container-low' }}"
          >
            <span class="material-symbols-outlined text-[20px]">event_available</span>
            Rekap Absensi
          </a>

          <a
            href="{{ route('timesheet.index') }}"
            class="flex items-center gap-2 px-space-md py-space-sm rounded-lg font-title-sm text-title-sm
            {{ request()->routeIs('timesheet.*') ? 'bg-primary-container text-on-primary' : 'text-on-surface hover:bg-surface-container-low' }}"
          >
            <span class="material-symbols-outlined text-[20px]">calendar_clock</span>
            Riwayat &amp; Timesheet
          </a>

          <a
            href="{{ route('team-recap.index') }}"
            class="flex items-center gap-2 px-space-md py-space-sm rounded-lg font-title-sm text-title-sm
            {{ request()->routeIs('team-recap.*') ? 'bg-primary-container text-on-primary' : 'text-on-surface hover:bg-surface-container-low' }}"
          >
            <span class="material-symbols-outlined text-[20px]">groups</span>
            Rekapitulasi Tim
          </a>

          @if (Route::has('attendance.anomalies'))

            <a
              href="{{ route('attendance.anomalies') }}"
              class="flex items-center gap-2 px-space-md py-space-sm rounded-lg font-title-sm text-title-sm
              {{ request()->routeIs('attendance.anomalies*') ? 'bg-primary-container text-on-primary' : 'text-on-surface hover:bg-surface-container-low' }}"
            >
              <span class="material-symbols-outlined text-[20px]">fact_check</span>
              Review Anomali
            </a>

          @endif

          @if (Route::has('attendance.import-errors'))

            <a
              href="{{ route('attendance.import-errors') }}"
              class="flex items-center gap-2 px-space-md py-space-sm rounded-lg font-title-sm text-title-sm
              {{ request()->routeIs('attendance.import-errors*') ? 'bg-primary-container text-on-primary' : 'text-on-surface hover:bg-surface-container-low' }}"
            >
              <span class="material-symbols-outlined text-[20px]">error_outline</span>
              Error Import
            </a>

          @endif

          @if (Route::has('attendance.import.production'))

            <a
              href="{{ route('attendance.import.production') }}"
              class="flex items-center gap-2 px-space-md py-space-sm rounded-lg font-title-sm text-title-sm
              {{ request()->routeIs('attendance.import.production*') ? 'bg-primary-container text-on-primary' : 'text-on-surface hover:bg-surface-container-low' }}"
            >
              <span class="material-symbols-outlined text-[20px]">upload_file</span>
              Import Absensi
            </a>

          @endif

          @if (Route::has('overtime.import'))

            <a
              href="{{ route('overtime.import') }}"
              class="flex items-center gap-2 px-space-md py-space-sm rounded-lg font-title-sm text-title-sm
              {{ request()->routeIs('overtime.import*') ? 'bg-primary-container text-on-primary' : 'text-on-surface hover:bg-surface-container-low' }}"
            >
              <span class="material-symbols-outlined text-[20px]">schedule</span>
              Import Lembur
            </a>

          @endif

          @if (Route::has('overtime.hr.verification'))

            <a
              href="{{ route('overtime.hr.verification') }}"
              class="flex items-center gap-2 px-space-md py-space-sm rounded-lg font-title-sm text-title-sm
              {{ request()->routeIs('overtime.hr.*') ? 'bg-primary-container text-on-primary' : 'text-on-surface hover:bg-surface-container-low' }}"
            >
              <span class="material-symbols-outlined text-[20px]">verified_user</span>
              Verifikasi Lembur
            </a>

          @endif

          @if (Route::has('reports.index'))

            <a
              href="{{ route('reports.index') }}"
              class="flex items-center gap-2 px-space-md py-space-sm rounded-lg font-title-sm text-title-sm
              {{ request()->routeIs('reports.*') ? 'bg-primary-container text-on-primary' : 'text-on-surface hover:bg-surface-container-low' }}"
            >
              <span class="material-symbols-outlined text-[20px]">summarize</span>
              Laporan HR
            </a>

          @endif

          @if (Route::has('audit-logs.index'))

            <a
              href="{{ route('audit-logs.index') }}"
              class="flex items-center gap-2 px-space-md py-space-sm rounded-lg font-title-sm text-title-sm
              {{ request()->routeIs('audit-logs.*') ? 'bg-primary-container text-on-primary' : 'text-on-surface hover:bg-surface-container-low' }}"
            >
              <span class="material-symbols-outlined text-[20px]">manage_search</span>
              Audit Trail
            </a>

          @endif

        </nav>


      {{-- =========================================================
           ADMINISTRATOR SISTEM
           ========================================================= --}}

      @elseif(auth()->user()->isAdminSistem())

        <div class="px-space-sm pb-space-xs flex items-center justify-between">

          <span class="font-label-sm text-label-sm text-on-surface-variant uppercase tracking-wider">
            Portal Administrasi Sistem
          </span>

          <span class="px-1.5 py-0.5 rounded text-[10px] font-semibold bg-surface-container-low text-on-surface-variant">
            ADMIN
          </span>

        </div>

        <nav class="flex flex-col gap-space-xs">

          <a
            href="{{ route('admin.accounts.index') }}"
            class="flex items-center gap-2 px-space-md py-space-sm rounded-lg font-title-sm text-title-sm
            {{ request()->routeIs('admin.accounts.*') ? 'bg-primary-container text-on-primary' : 'text-on-surface hover:bg-surface-container-low' }}"
          >
            <span class="material-symbols-outlined text-[20px]">manage_accounts</span>
            Manajemen Akun Struktural
          </a>

          {{-- Audit Trail --}}
          @if (Route::has('audit-logs.index'))

            <a
              href="{{ route('audit-logs.index') }}"
              class="flex items-center gap-2 px-space-md py-space-sm rounded-lg font-title-sm text-title-sm
              {{ request()->routeIs('audit-logs.*') ? 'bg-primary-container text-on-primary' : 'text-on-surface hover:bg-surface-container-low' }}"
            >
              <span class="material-symbols-outlined text-[20px]">manage_search</span>
              Audit Trail
            </a>

          @endif

        </nav>


      {{-- =========================================================
           DIREKTUR UTAMA
           ========================================================= --}}

      @elseif(auth()->user()->isDirekturUtama())

        <div class="px-space-sm pb-space-xs flex items-center justify-between">

          <span class="font-label-sm text-label-sm text-on-surface-variant uppercase tracking-wider">
            Portal Direktur Utama
          </span>

          <span class="px-1.5 py-0.5 rounded text-[10px] font-semibold bg-surface-container-low text-on-surface-variant">
            DIREKTUR
          </span>

        </div>

        <nav class="flex flex-col gap-space-xs">

          <a
            href="{{ route('direktur.dashboard') }}#ringkasan"
            class="flex items-center gap-2 px-space-md py-space-sm rounded-lg font-title-sm text-title-sm
            {{ request()->routeIs('direktur.dashboard') && !request()->get('tab') ? 'bg-primary-container text-on-primary' : 'text-on-surface hover:bg-surface-container-low' }}"
          >
            <span class="material-symbols-outlined text-[20px]">dashboard</span>
            Dashboard
          </a>

          <a
            href="{{ route('direktur.dashboard') }}#realtime"
            class="flex items-center gap-2 px-space-md py-space-sm rounded-lg font-title-sm text-title-sm text-on-surface hover:bg-surface-container-low"
          >
            <span class="material-symbols-outlined text-[20px]">sensors</span>
            Realtime
          </a>

          <a
            href="{{ route('direktur.dashboard') }}#history"
            class="flex items-center gap-2 px-space-md py-space-sm rounded-lg font-title-sm text-title-sm text-on-surface hover:bg-surface-container-low"
          >
            <span class="material-symbols-outlined text-[20px]">history</span>
            History
          </a>

        </nav>

      @endif

    </div>
  </div>


  {{-- =========================================================
       USER FOOTER
       ========================================================= --}}

  <div class="p-space-md shrink-0 border-t border-surface-container-low">

    <div class="flex items-center gap-2 px-1 pb-2">

      <div class="w-9 h-9 shrink-0 rounded-full bg-primary-container text-on-primary flex items-center justify-center font-title-sm text-title-sm">
        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
      </div>

      <div class="min-w-0">

        <div class="font-title-sm text-title-sm text-on-surface truncate">
          {{ auth()->user()->name }}
        </div>

        <div class="font-body-sm text-body-sm text-on-surface-variant truncate">
          {{ auth()->user()->email }}
        </div>

        <!-- Notifikasi sesi aktif -->
        <div class="font-xxs text-on-surface-variant ml-2">
            Sesi: {{ ucfirst(auth()->user()->role) }}
        </div>

      </div>

    </div>

    <a
      href="{{ route('profile.edit') }}"
      class="flex items-center gap-2 px-3 py-2 rounded-lg hover:bg-surface-container-low font-body-sm text-body-sm text-on-surface"
    >
      <span class="material-symbols-outlined text-[18px]">account_circle</span>
      Profil Saya
    </a>

    <form method="POST" action="{{ route('logout') }}">

      @csrf

      <button
        type="submit"
        class="w-full flex items-center gap-2 px-3 py-2 rounded-lg hover:bg-surface-container-low font-body-sm text-body-sm text-error"
      >
        <span class="material-symbols-outlined text-[18px]">logout</span>
        Logout
      </button>

    </form>

  </div>

</aside>


<div class="md:pl-72 flex flex-col min-h-screen">

  <header class="fixed top-0 left-0 md:left-72 right-0 h-20 bg-surface-container-lowest/90 backdrop-blur-xl shadow-[0_1px_8px_rgba(0,0,0,0.04)] z-30 flex items-center justify-between px-4 md:px-space-xl">

    <div class="flex items-center gap-3">

      <button
        onclick="toggleSidebar()"
        class="md:hidden p-2 -ml-2 text-primary"
        aria-label="Buka menu"
      >
        <svg
          xmlns="http://www.w3.org/2000/svg"
          class="h-6 w-6"
          fill="none"
          viewBox="0 0 24 24"
          stroke="currentColor"
        >
          <path
            stroke-linecap="round"
            stroke-linejoin="round"
            stroke-width="2"
            d="M4 6h16M4 12h16M4 18h16"
          />
        </svg>
      </button>

      <span class="material-symbols-outlined text-[18px] text-secondary hidden sm:inline">
        shield
      </span>

      <span class="font-title-sm text-title-sm text-primary">

        @if(auth()->user()->isKaryawan())
            Portal Karyawan

        @elseif(auth()->user()->isVp())
            Portal VP Divisi

        @elseif(auth()->user()->isHrAdmin())
            Portal HR &amp; Administrasi

        @elseif(auth()->user()->isAdminSistem())
            Portal Administrasi Sistem

        @elseif(auth()->user()->isDirekturUtama())
            Portal Direktur Utama

        @else
            Portal
        @endif

      </span>

    </div>


    <details class="relative">

      <summary
        class="list-none cursor-pointer select-none inline-flex items-center gap-2 px-3 py-2 rounded-lg bg-surface-container-low text-on-surface hover:bg-surface-container text-sm font-semibold"
        aria-label="Pengaturan tampilan"
      >
        <span class="material-symbols-outlined text-[18px]">tune</span>
        <span class="hidden sm:inline">Pengaturan</span>
      </summary>

      <div class="inti-preference-panel absolute right-0 top-12 z-50 rounded-xl bg-surface-container-lowest border border-outline-variant/50 shadow-xl p-4 space-y-3">

        <div>

          <label
            for="inti-theme"
            class="block text-xs font-semibold text-on-surface-variant mb-1"
          >
            Tema
          </label>

          <select
            id="inti-theme"
            class="w-full rounded-lg border border-outline bg-surface-container-low px-3 py-2 text-sm text-on-surface"
          >
            <option value="light">Terang</option>
            <option value="dark">Gelap</option>
            <option value="system">Sesuai sistem</option>
          </select>

        </div>

        <p class="text-xs text-on-surface-variant leading-4">
          Preferensi tema tersimpan di perangkat ini.
        </p>

      </div>

    </details>

  </header>


  <main class="w-full pt-28 px-4 md:px-space-xl pb-space-2xl bg-background flex-1">

    @if (config('app.demo_mode', false))

      <div class="mb-space-base flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 px-space-base py-3 rounded-lg border border-amber-200 bg-amber-50 text-amber-900">

        <div class="flex items-center gap-2 font-title-sm text-title-sm">

          <span class="material-symbols-outlined text-[18px]">
            science
          </span>

          <strong>MODE DEMO</strong>

          <span class="font-normal">
            Waktu:
            {{ app(\App\Services\Demo\DemoClock::class)->now()->format('d/m/Y H:i') }}
            WIB
          </span>

        </div>

        <a
          href="{{ route('demo.index') }}"
          class="underline font-semibold"
        >
          Kontrol Waktu
        </a>

      </div>

    @endif


    @if (session('status'))

      <div class="mb-space-base px-space-base py-3 rounded-lg bg-secondary/10 text-secondary font-title-sm text-title-sm">
        {{ session('status') }}
      </div>

    @endif


    @if ($errors->any())

      <div class="mb-space-base px-space-base py-3 rounded-lg bg-error/10 text-error font-body-md text-body-md">

        <ul class="list-disc list-inside">

          @foreach ($errors->all() as $error)

            <li>
              {{ $error }}
            </li>

          @endforeach

        </ul>

      </div>

    @endif


    @yield('content')

  </main>

</div>


<script>

  function toggleSidebar() {

    document
      .getElementById('sidebar')
      .classList
      .toggle('-translate-x-full');

    document
      .getElementById('sidebar-backdrop')
      .classList
      .toggle('hidden');

  }


  function applyTheme(theme) {

    const dark =
      theme === 'dark' ||
      (
        theme === 'system' &&
        window.matchMedia('(prefers-color-scheme: dark)').matches
      );

    document.documentElement.classList.toggle('dark', dark);

    document.documentElement.dataset.theme = theme;

    localStorage.setItem('inti-theme', theme);

    const themeSelect =
      document.getElementById('inti-theme');

    if (themeSelect) {
      themeSelect.value = theme;
    }

    window.dispatchEvent(
      new CustomEvent('inti:themechange', {
        detail: {
          theme,
          dark
        }
      })
    );

  }


  function setupAutoFilters() {

    document
      .querySelectorAll(
        'form[data-auto-filter][method="GET"], form[data-auto-filter][method="get"]'
      )
      .forEach(form => {

        let timer = null;

        const submit = () => {

          if (form.dataset.autoSubmitting === '1') {
            return;
          }

          form.dataset.autoSubmitting = '1';

          form.requestSubmit();

        };


        form
          .querySelectorAll(
            'select, input[type="date"], input[type="month"]'
          )
          .forEach(control => {

            if (control.hasAttribute('onchange')) {
              return;
            }

            control.addEventListener('change', submit);

          });


        form
          .querySelectorAll(
            'input[type="text"], input[type="search"], input[type="number"]'
          )
          .forEach(control => {

            control.addEventListener('input', () => {

              clearTimeout(timer);

              timer = setTimeout(
                submit,
                control.type === 'number' ? 650 : 500
              );

            });

          });

      });

  }


  document.addEventListener('DOMContentLoaded', () => {

    const themeSelect =
      document.getElementById('inti-theme');

    if (themeSelect) {

      themeSelect.addEventListener(
        'change',
        e => applyTheme(e.target.value)
      );

    }

    applyTheme(
      localStorage.getItem('inti-theme') || 'system'
    );

    setupAutoFilters();


    const media =
      window.matchMedia('(prefers-color-scheme: dark)');

    media.addEventListener?.(
      'change',
      () => {

        if (
          (localStorage.getItem('inti-theme') || 'system') === 'system'
        ) {
          applyTheme('system');
        }

      }
    );

  });


  function intiIsDark() {

    return document
      .documentElement
      .classList
      .contains('dark');

  }


  window.intiGetChartPalette = function () {

    return intiIsDark()

      ? {
          primary: '#7dd3fc',
          secondary: '#5eead4',
          error: '#fb7185',
          outline: '#94a3b8',
          tertiary: '#fbbf24',
          grid: 'rgba(148,163,184,0.20)',
          text: '#dbe7f3',
          surface: '#1b2636',
        }

      : {
          primary: '#0f2942',
          secondary: '#006a61',
          error: '#ba1a1a',
          outline: '#74777e',
          tertiary: '#d77503',
          grid: 'rgba(67,71,77,0.10)',
          text: '#43474d',
          surface: '#ffffff',
        };

  };


  window.intiApplyChartTheme = function () {

    if (!window.Chart) {
      return;
    }

    const palette =
      window.intiGetChartPalette();

    Chart.defaults.font.family =
      "'Plus Jakarta Sans', sans-serif";

    Chart.defaults.color =
      palette.text;


    Object.values(Chart.instances || {})
      .forEach(chart => {

        if (chart.options?.scales) {

          Object.values(chart.options.scales)
            .forEach(scale => {

              scale.ticks =
                scale.ticks || {};

              scale.ticks.color =
                palette.text;

              scale.grid =
                scale.grid || {};

              if (scale.grid.display !== false) {
                scale.grid.color =
                  palette.grid;
              }

              if (scale.title?.display) {
                scale.title.color =
                  palette.text;
              }

            });

        }


        if (chart.options?.plugins?.legend) {

          chart.options.plugins.legend.labels =
            chart.options.plugins.legend.labels || {};

          chart.options.plugins.legend.labels.color =
            palette.text;

        }


        if (chart.options?.plugins?.tooltip) {

          chart.options.plugins.tooltip.titleColor =
            palette.text;

          chart.options.plugins.tooltip.bodyColor =
            palette.text;

          chart.options.plugins.tooltip.backgroundColor =
            palette.surface;

          chart.options.plugins.tooltip.borderColor =
            palette.outline;

          chart.options.plugins.tooltip.borderWidth =
            1;

        }


        chart.data.datasets.forEach(dataset => {

          const swap = value => {

            const map = {
              '#0f2942': palette.primary,
              '#7dd3fc': palette.primary,
              '#006a61': palette.secondary,
              '#5eead4': palette.secondary,
              '#ba1a1a': palette.error,
              '#fb7185': palette.error,
              '#74777e': palette.outline,
              '#94a3b8': palette.outline,
            };

            return map[value] || value;

          };


          if (
            typeof dataset.backgroundColor === 'string'
          ) {
            dataset.backgroundColor =
              swap(dataset.backgroundColor);
          }


          if (
            typeof dataset.borderColor === 'string'
          ) {
            dataset.borderColor =
              swap(dataset.borderColor);
          }


          if (
            Array.isArray(dataset.backgroundColor)
          ) {
            dataset.backgroundColor =
              dataset.backgroundColor.map(swap);
          }


          if (
            Array.isArray(dataset.borderColor)
          ) {
            dataset.borderColor =
              dataset.borderColor.map(swap);
          }


          if (
            dataset.borderColor === '#ffffff'
          ) {
            dataset.borderColor =
              palette.surface;
          }

        });


        chart.update('none');

      });

  };


  window.addEventListener(
    'inti:themechange',
    window.intiApplyChartTheme
  );


  const chartPalet =
    window.intiGetChartPalette();

  if (window.Chart) {

    Chart.defaults.font.family =
      "'Plus Jakarta Sans', sans-serif";

    Chart.defaults.color =
      chartPalet.text;

  }

</script>

@yield('scripts')

</body>
</html>
@extends('layouts.absensi')

@section('title','Dashboard — Sistem Absensi PT. INTI')

@section('content')
<div class="inti-page">

  {{-- =========================================================
       HEADER
       ========================================================= --}}
  <div class="inti-page-header">
    <div>
      <div class="inti-eyebrow">Sistem Absensi & Lembur PT. INTI</div>
      <h1 class="inti-title">Dashboard</h1>
      <p class="inti-subtitle">
        Ringkasan akses dan aktivitas sesuai peran Anda.
      </p>
    </div>
  </div>

  {{-- =========================================================
       WELCOME CARD
       ========================================================= --}}
  <div
    class="inti-card inti-card-pad"
    style="margin-bottom:1rem;background:linear-gradient(135deg,#0f2942,#001428);color:#fff"
  >
    <div style="display:flex;align-items:center;gap:1rem">
      <div
        style="width:48px;height:48px;border-radius:14px;background:rgba(255,255,255,.12);display:grid;place-items:center"
      >
        <span class="material-symbols-outlined">waving_hand</span>
      </div>

      <div>
        <div style="font-size:18px;font-weight:700">
          Selamat datang, {{ auth()->user()->name }}
        </div>

        <div style="font-size:13px;opacity:.8;margin-top:.2rem">
          Akses {{ strtoupper((string) auth()->user()->role) }}
          · {{ now()->translatedFormat('l, d F Y') }}
        </div>
      </div>
    </div>
  </div>


  {{-- =========================================================
       KARYAWAN
       ========================================================= --}}
  @if(auth()->user()->isKaryawan())

    <div
      class="inti-kpi-grid"
      style="grid-template-columns:repeat(3,minmax(0,1fr));margin-bottom:1rem"
    >

      <a
        href="{{ route('attendance.my') }}"
        class="inti-card inti-card-pad inti-card-hover"
        style="text-decoration:none"
      >
        <div class="inti-kpi-label">Presensi</div>

        <div class="inti-kpi-value">
          <span
            class="material-symbols-outlined"
            style="font-size:28px;vertical-align:-5px"
          >
            event_available
          </span>
          Absensi Saya
        </div>

        <div class="inti-kpi-meta">
          Clock In, Clock Out & riwayat
        </div>
      </a>


      <a
        href="{{ route('timesheet.index') }}"
        class="inti-card inti-card-pad inti-card-hover"
        style="text-decoration:none"
      >
        <div class="inti-kpi-label">Monitoring</div>

        <div class="inti-kpi-value">
          <span
            class="material-symbols-outlined"
            style="font-size:28px;vertical-align:-5px"
          >
            calendar_clock
          </span>
          Timesheet
        </div>

        <div class="inti-kpi-meta">
          Presensi dan lembur resmi
        </div>
      </a>


      <a
        href="{{ route('overtime.index') }}"
        class="inti-card inti-card-pad inti-card-hover"
        style="text-decoration:none"
      >
        <div class="inti-kpi-label">Operasional</div>

        <div class="inti-kpi-value">
          <span
            class="material-symbols-outlined"
            style="font-size:28px;vertical-align:-5px"
          >
            more_time
          </span>
          Lembur
        </div>

        <div class="inti-kpi-meta">
          Ajukan dan pantau proses
        </div>
      </a>

    </div>


  {{-- =========================================================
       VP
       ========================================================= --}}
  @elseif(auth()->user()->isVp())

    <div class="inti-kpi-grid" style="margin-bottom:1rem">

      <a
        href="{{ route('attendance.my') }}"
        class="inti-card inti-card-pad inti-card-hover"
        style="text-decoration:none"
      >
        <div class="inti-kpi-label">Presensi Pribadi</div>
        <div class="inti-kpi-value">Absensi Saya</div>
        <div class="inti-kpi-meta">Clock In / Clock Out</div>
      </a>


      <a
        href="{{ route('timesheet.index') }}"
        class="inti-card inti-card-pad inti-card-hover"
        style="text-decoration:none"
      >
        <div class="inti-kpi-label">Monitoring</div>
        <div class="inti-kpi-value">Timesheet</div>
        <div class="inti-kpi-meta">Riwayat presensi & lembur</div>
      </a>


      <a
        href="{{ route('overtime.vp') }}"
        class="inti-card inti-card-pad inti-card-hover"
        style="text-decoration:none"
      >
        <div class="inti-kpi-label">Approval</div>
        <div class="inti-kpi-value">Persetujuan Lembur</div>
        <div class="inti-kpi-meta">Antrean anggota divisi</div>
      </a>


      <a
        href="{{ route('team-recap.index') }}"
        class="inti-card inti-card-pad inti-card-hover"
        style="text-decoration:none"
      >
        <div class="inti-kpi-label">Reporting</div>
        <div class="inti-kpi-value">Rekap Divisi</div>
        <div class="inti-kpi-meta">Monitoring tim</div>
      </a>

    </div>


  {{-- =========================================================
       HR
       ========================================================= --}}
  @elseif(auth()->user()->isHrAdmin())

    <div class="inti-kpi-grid" style="margin-bottom:1rem">

      <a
        href="{{ route('employees.index') }}"
        class="inti-card inti-card-pad inti-card-hover"
        style="text-decoration:none"
      >
        <div class="inti-kpi-label">Master Data</div>
        <div class="inti-kpi-value">Karyawan</div>
        <div class="inti-kpi-meta">Kelola personel</div>
      </a>


      <a
        href="{{ route('attendance.index') }}"
        class="inti-card inti-card-pad inti-card-hover"
        style="text-decoration:none"
      >
        <div class="inti-kpi-label">Presensi</div>
        <div class="inti-kpi-value">Rekap Absensi</div>
        <div class="inti-kpi-meta">Monitoring attendance</div>
      </a>


      <a
        href="{{ route('team-recap.index') }}"
        class="inti-card inti-card-pad inti-card-hover"
        style="text-decoration:none"
      >
        <div class="inti-kpi-label">Reporting</div>
        <div class="inti-kpi-value">Rekapitulasi Tim</div>
        <div class="inti-kpi-meta">KPI operasional</div>
      </a>


      @if(Route::has('overtime.hr.verification'))
        <a
          href="{{ route('overtime.hr.verification') }}"
          class="inti-card inti-card-pad inti-card-hover"
          style="text-decoration:none"
        >
          <div class="inti-kpi-label">Governance</div>
          <div class="inti-kpi-value">Verifikasi Lembur</div>
          <div class="inti-kpi-meta">
            Proses setelah approval VP
          </div>
        </a>
      @endif

    </div>


    <div class="inti-card inti-card-pad">

      <div class="inti-section-title">
        Pusat Operasional HR
      </div>

      <div
        style="display:flex;flex-wrap:wrap;gap:.6rem;margin-top:1rem"
      >

        @if(Route::has('employees.import'))
          <a
            class="inti-btn inti-btn-primary"
            href="{{ route('employees.import') }}"
          >
            <span class="material-symbols-outlined">
              upload_file
            </span>
            Import Karyawan
          </a>
        @endif


        @if(Route::has('attendance.import.production'))
          <a
            class="inti-btn inti-btn-ghost"
            href="{{ route('attendance.import.production') }}"
          >
            <span class="material-symbols-outlined">
              file_upload
            </span>
            Import Absensi
          </a>
        @endif


        @if(Route::has('attendance.anomalies'))
          <a
            class="inti-btn inti-btn-ghost"
            href="{{ route('attendance.anomalies') }}"
          >
            <span class="material-symbols-outlined">
              fact_check
            </span>
            Review Anomali
          </a>
        @endif


        @if(Route::has('audit-logs.index'))
          <a
            class="inti-btn inti-btn-ghost"
            href="{{ route('audit-logs.index') }}"
          >
            <span class="material-symbols-outlined">
              manage_search
            </span>
            Audit Trail
          </a>
        @endif

      </div>

    </div>


  {{-- =========================================================
       ADMINISTRATOR SISTEM
       ========================================================= --}}
  @elseif(auth()->user()->isAdminSistem())

    {{-- Ringkasan utama Admin Sistem --}}
    <div
      class="inti-kpi-grid"
      style="margin-bottom:1rem"
    >

      <a
        href="{{ route('admin.accounts.index') }}"
        class="inti-card inti-card-pad inti-card-hover"
        style="text-decoration:none"
      >
        <div class="inti-kpi-label">
          Administrasi Sistem
        </div>

        <div class="inti-kpi-value">
          <span
            class="material-symbols-outlined"
            style="font-size:28px;vertical-align:-5px"
          >
            manage_accounts
          </span>
          Manajemen Akun
        </div>

        <div class="inti-kpi-meta">
          Kelola akun struktural dan hak akses login
        </div>
      </a>

    </div>


    {{-- Pusat Administrasi Sistem --}}
    <div
      class="inti-card inti-card-pad"
      style="margin-bottom:1rem"
    >

      <div class="inti-section-title">
        Pusat Administrasi Sistem
      </div>

      <p
        class="inti-kpi-meta"
        style="margin-top:.5rem;max-width:760px"
      >
        Gunakan modul Manajemen Akun untuk mengelola akun
        struktural dan akses login yang menjadi kewenangan
        Administrator Sistem.
      </p>

      <div
        style="display:flex;flex-wrap:wrap;gap:.6rem;margin-top:1rem"
      >

        <a
          href="{{ route('admin.accounts.index') }}"
          class="inti-btn inti-btn-primary"
        >
          <span class="material-symbols-outlined">
            manage_accounts
          </span>
          Buka Manajemen Akun
        </a>

      </div>

    </div>


    {{-- Batas kewenangan --}}
    <div class="inti-card inti-card-pad">

      <div
        style="display:flex;align-items:flex-start;gap:.8rem"
      >

        <span
          class="material-symbols-outlined"
          style="font-size:24px"
        >
          admin_panel_settings
        </span>

        <div style="min-width:0">

          <div class="inti-section-title">
            Batas Kewenangan
          </div>

          <p
            class="inti-kpi-meta"
            style="margin-top:.5rem;line-height:1.7"
          >
            Administrator Sistem berfokus pada administrasi
            akun struktural dan akses login sistem.
            Pengelolaan data kepegawaian, Band Code, presensi,
            import, serta proses HR tetap berada pada kewenangan
            HR sesuai pembagian akses sistem.
          </p>

        </div>

      </div>

    </div>


  {{-- =========================================================
       DIREKTUR UTAMA
       ========================================================= --}}
  @elseif(auth()->user()->isDirekturUtama())

    <a
      href="{{ route('direktur.dashboard') }}"
      class="inti-card inti-card-pad inti-card-hover"
      style="display:block;text-decoration:none"
    >

      <div class="inti-kpi-label">
        Executive Monitoring
      </div>

      <div class="inti-kpi-value">
        Dashboard Direktur Utama
      </div>

      <div class="inti-kpi-meta">
        Ringkasan kinerja presensi dan lembur perusahaan
      </div>

    </a>

  @endif

</div>
@endsection
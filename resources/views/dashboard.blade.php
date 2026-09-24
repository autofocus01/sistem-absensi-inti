@extends('layouts.absensi')
@section('title','Dashboard — Sistem Absensi PT. INTI')
@section('content')
<div class="inti-page">
  <div class="inti-page-header">
    <div><div class="inti-eyebrow">Sistem Absensi & Lembur PT. INTI</div><h1 class="inti-title">Dashboard</h1><p class="inti-subtitle">Ringkasan akses dan aktivitas sesuai peran Anda.</p></div>
  </div>

  <div class="inti-card inti-card-pad" style="margin-bottom:1rem;background:linear-gradient(135deg,#0f2942,#001428);color:#fff">
    <div style="display:flex;align-items:center;gap:1rem">
      <div style="width:48px;height:48px;border-radius:14px;background:rgba(255,255,255,.12);display:grid;place-items:center"><span class="material-symbols-outlined">waving_hand</span></div>
      <div><div style="font-size:18px;font-weight:700">Selamat datang, {{ auth()->user()->name }}</div><div style="font-size:13px;opacity:.8;margin-top:.2rem">Akses {{ strtoupper((string) auth()->user()->role) }} · {{ now()->translatedFormat('l, d F Y') }}</div></div>
    </div>
  </div>

  @if(auth()->user()->isKaryawan())
    <div class="inti-kpi-grid" style="grid-template-columns:repeat(3,minmax(0,1fr));margin-bottom:1rem">
      <a href="{{ route('attendance.my') }}" class="inti-card inti-card-pad inti-card-hover" style="text-decoration:none"><div class="inti-kpi-label">Presensi</div><div class="inti-kpi-value"><span class="material-symbols-outlined" style="font-size:28px;vertical-align:-5px">event_available</span> Absensi Saya</div><div class="inti-kpi-meta">Clock In, Clock Out & riwayat</div></a>
      <a href="{{ route('timesheet.index') }}" class="inti-card inti-card-pad inti-card-hover" style="text-decoration:none"><div class="inti-kpi-label">Monitoring</div><div class="inti-kpi-value"><span class="material-symbols-outlined" style="font-size:28px;vertical-align:-5px">calendar_clock</span> Timesheet</div><div class="inti-kpi-meta">Presensi dan lembur resmi</div></a>
      <a href="{{ route('overtime.index') }}" class="inti-card inti-card-pad inti-card-hover" style="text-decoration:none"><div class="inti-kpi-label">Operasional</div><div class="inti-kpi-value"><span class="material-symbols-outlined" style="font-size:28px;vertical-align:-5px">more_time</span> Lembur</div><div class="inti-kpi-meta">Ajukan dan pantau proses</div></a>
    </div>
  @elseif(auth()->user()->isVp())
    <div class="inti-kpi-grid" style="margin-bottom:1rem">
      <a href="{{ route('attendance.my') }}" class="inti-card inti-card-pad inti-card-hover" style="text-decoration:none"><div class="inti-kpi-label">Presensi Pribadi</div><div class="inti-kpi-value">Absensi Saya</div><div class="inti-kpi-meta">Clock In / Clock Out</div></a>
      <a href="{{ route('timesheet.index') }}" class="inti-card inti-card-pad inti-card-hover" style="text-decoration:none"><div class="inti-kpi-label">Monitoring</div><div class="inti-kpi-value">Timesheet</div><div class="inti-kpi-meta">Riwayat presensi & lembur</div></a>
      <a href="{{ route('overtime.vp') }}" class="inti-card inti-card-pad inti-card-hover" style="text-decoration:none"><div class="inti-kpi-label">Approval</div><div class="inti-kpi-value">Persetujuan Lembur</div><div class="inti-kpi-meta">Antrean anggota divisi</div></a>
      <a href="{{ route('team-recap.index') }}" class="inti-card inti-card-pad inti-card-hover" style="text-decoration:none"><div class="inti-kpi-label">Reporting</div><div class="inti-kpi-value">Rekap Divisi</div><div class="inti-kpi-meta">Monitoring tim</div></a>
    </div>
  @elseif(auth()->user()->isHrAdmin())
    <div class="inti-kpi-grid" style="margin-bottom:1rem">
      <a href="{{ route('employees.index') }}" class="inti-card inti-card-pad inti-card-hover" style="text-decoration:none"><div class="inti-kpi-label">Master Data</div><div class="inti-kpi-value">Karyawan</div><div class="inti-kpi-meta">Kelola personel</div></a>
      <a href="{{ route('attendance.index') }}" class="inti-card inti-card-pad inti-card-hover" style="text-decoration:none"><div class="inti-kpi-label">Presensi</div><div class="inti-kpi-value">Rekap Absensi</div><div class="inti-kpi-meta">Monitoring attendance</div></a>
      <a href="{{ route('team-recap.index') }}" class="inti-card inti-card-pad inti-card-hover" style="text-decoration:none"><div class="inti-kpi-label">Reporting</div><div class="inti-kpi-value">Rekapitulasi Tim</div><div class="inti-kpi-meta">KPI operasional</div></a>
      @if(Route::has('overtime.hr.verification'))<a href="{{ route('overtime.hr.verification') }}" class="inti-card inti-card-pad inti-card-hover" style="text-decoration:none"><div class="inti-kpi-label">Governance</div><div class="inti-kpi-value">Verifikasi Lembur</div><div class="inti-kpi-meta">Proses setelah approval VP</div></a>@endif
    </div>
    <div class="inti-card inti-card-pad"><div class="inti-section-title">Pusat Operasional HR</div><div style="display:flex;flex-wrap:wrap;gap:.6rem;margin-top:1rem">
      @if(Route::has('employees.import'))<a class="inti-btn inti-btn-primary" href="{{ route('employees.import') }}"><span class="material-symbols-outlined">upload_file</span>Import Karyawan</a>@endif
      @if(Route::has('attendance.import.production'))<a class="inti-btn inti-btn-ghost" href="{{ route('attendance.import.production') }}"><span class="material-symbols-outlined">file_upload</span>Import Absensi</a>@endif
      @if(Route::has('attendance.anomalies'))<a class="inti-btn inti-btn-ghost" href="{{ route('attendance.anomalies') }}"><span class="material-symbols-outlined">fact_check</span>Review Anomali</a>@endif
      @if(Route::has('audit-logs.index'))<a class="inti-btn inti-btn-ghost" href="{{ route('audit-logs.index') }}"><span class="material-symbols-outlined">manage_search</span>Audit Trail</a>@endif
    </div></div>
  @elseif(auth()->user()->isDirekturUtama())
    <a href="{{ route('direktur.dashboard') }}" class="inti-card inti-card-pad inti-card-hover" style="display:block;text-decoration:none"><div class="inti-kpi-label">Executive Monitoring</div><div class="inti-kpi-value">Dashboard Direktur Utama</div><div class="inti-kpi-meta">Ringkasan kinerja presensi dan lembur perusahaan</div></a>
  @endif
</div>
@endsection

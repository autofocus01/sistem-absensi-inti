@extends('layouts.absensi')

@section('title', 'Absensi Saya - PT. INTI')

@section('content')
<div class="inti-page">
  <div class="inti-page-header">
    <div>
      <span class="inti-eyebrow">PRESENSI PERSONAL</span>
      <h1 class="inti-title">Absensi Saya</h1>
      <p class="inti-subtitle">Kelola Clock In, Clock Out, dan lihat riwayat presensi Anda.</p>
    </div>
    <div class="inti-subtitle">{{ $today->translatedFormat('l, d F Y') }}</div>
  </div>

  @if(session('status'))
    <div class="inti-alert inti-alert-success" style="margin-bottom:1rem">{{ session('status') }}</div>
  @endif
  @if($errors->has('attendance'))
    <div class="inti-alert inti-alert-danger" style="margin-bottom:1rem">{{ $errors->first('attendance') }}</div>
  @endif

  @php($hasIn = !empty($todayAttendance?->jam_masuk))
  @php($hasOut = !empty($todayAttendance?->jam_pulang))

  <div class="inti-kpi-grid" style="margin-bottom:1rem">
    <div class="inti-card inti-kpi">
      <div class="inti-kpi-label">Hari Ini</div>
      <div class="inti-kpi-value">{{ $today->translatedFormat('d M Y') }}</div>
      <div class="inti-kpi-meta">Jam kerja normal 07:30–16:30</div>
    </div>
    <div class="inti-card inti-kpi">
      <div class="inti-kpi-label">Clock In</div>
      <div class="inti-kpi-value">{{ $todayAttendance?->jam_masuk ? substr($todayAttendance->jam_masuk, 0, 5) : '--:--' }}</div>
      <div class="inti-kpi-meta">{{ $hasIn ? 'Sudah tercatat' : 'Belum tercatat' }}</div>
    </div>
    <div class="inti-card inti-kpi">
      <div class="inti-kpi-label">Clock Out</div>
      <div class="inti-kpi-value">{{ $todayAttendance?->jam_pulang ? substr($todayAttendance->jam_pulang, 0, 5) : '--:--' }}</div>
      <div class="inti-kpi-meta">{{ $hasOut ? 'Sudah tercatat' : 'Belum tercatat' }}</div>
    </div>
    <div class="inti-card inti-kpi">
      <div class="inti-kpi-label">Status</div>
      <div class="inti-kpi-value" style="font-size:20px">
        @if($hasOut) Selesai @elseif($hasIn) Sedang Bekerja @else Belum Presensi @endif
      </div>
      <div class="inti-kpi-meta">{{ $today->translatedFormat('l') }}</div>
    </div>
  </div>

  <div class="inti-card inti-card-pad" style="margin-bottom:1rem">
    <div class="inti-page-header" style="margin-bottom:1rem">
      <div>
        <div class="inti-section-title">Presensi Hari Ini</div>
        <div class="inti-subtitle">{{ $employee->nama }} · {{ $employee->nipeg }} · {{ $employee->division?->nama ?? 'Divisi belum tersedia' }}</div>
      </div>
      <span class="inti-badge {{ $hasOut ? 'inti-badge-success' : ($hasIn ? 'inti-badge-info' : 'inti-badge-neutral') }}">
        <span class="material-symbols-outlined" style="font-size:16px">circle</span>
        {{ $hasOut ? 'Sudah Pulang' : ($hasIn ? 'Sedang Bekerja' : 'Belum Absen') }}
      </span>
    </div>

    <div class="inti-responsive-toolbar">
      @if(!$hasIn)
        <form method="POST" action="{{ route('attendance.my.clock-in') }}">
          @csrf
          <button type="submit" class="inti-btn inti-btn-primary">
            <span class="material-symbols-outlined">login</span> Clock In
          </button>
        </form>
      @elseif(!$hasOut)
        <form method="POST" action="{{ route('attendance.my.clock-out') }}" onsubmit="return confirm('Catat Clock Out sekarang?')">
          @csrf
          <button type="submit" class="inti-btn inti-btn-primary">
            <span class="material-symbols-outlined">logout</span> Clock Out
          </button>
        </form>
      @else
        <span class="inti-badge inti-badge-success" style="align-self:center">Presensi hari ini sudah lengkap.</span>
      @endif
    </div>

    @if($todayAttendance)
      <div class="inti-kpi-grid" style="margin-top:1rem">
        <div class="inti-card inti-card-pad">
          <div class="inti-kpi-label">Keterlambatan</div>
          <div class="inti-kpi-value" style="font-size:24px">{{ $todayAttendance->menitTelat() }} menit</div>
        </div>
        <div class="inti-card inti-card-pad">
          <div class="inti-kpi-label">Durasi Kerja</div>
          <div class="inti-kpi-value" style="font-size:24px">{{ $todayAttendance->durasiKerjaFormat() }}</div>
        </div>
        <div class="inti-card inti-card-pad">
          <div class="inti-kpi-label">Sumber</div>
          <div class="inti-kpi-value" style="font-size:18px">{{ $todayAttendance->sumber === 'self_service' ? 'Self Service' : ucfirst(str_replace('_', ' ', $todayAttendance->sumber)) }}</div>
        </div>
      </div>
    @endif
  </div>

  <div class="inti-card inti-card-pad" style="margin-bottom:1rem">
    <div class="inti-page-header" style="margin-bottom:1rem">
      <div>
        <div class="inti-section-title">Aturan Presensi</div>
        <div class="inti-subtitle">Ketentuan dasar presensi personal.</div>
      </div>
    </div>
    <ul style="margin:0;padding-left:1.25rem;line-height:1.8;color:var(--inti-muted)">
      <li>Jam kerja normal Senin–Jumat: <strong>07:30–16:30</strong>.</li>
      <li>Tidak ada grace period. Keterlambatan dihitung dari selisih aktual setelah 07:30.</li>
      <li>Clock Out mencatat jam pulang aktual; lembur diproses melalui modul Overtime dan otorisasi.</li>
    </ul>
  </div>

  <div class="inti-card inti-card-pad">
    <div class="inti-page-header" style="margin-bottom:1rem">
      <div>
        <div class="inti-section-title">Riwayat Presensi</div>
        <div class="inti-subtitle">{{ $monthLabel }}</div>
      </div>
      <div class="inti-responsive-toolbar">
        <form method="GET" action="{{ route('attendance.my') }}" class="inti-responsive-toolbar">
          <select name="month" class="inti-input" aria-label="Bulan">
            @foreach(range(1, 12) as $m)
              <option value="{{ $m }}" @selected($month === $m)>{{ \Carbon\Carbon::create()->month($m)->translatedFormat('F') }}</option>
            @endforeach
          </select>
          <input type="number" name="year" min="2000" max="2100" value="{{ $year }}" class="inti-input" aria-label="Tahun">
          <button class="inti-btn inti-btn-secondary" type="submit">Tampilkan</button>
        </form>
        <a href="{{ route('timesheet.index',['month'=>$month,'year'=>$year]) }}" class="inti-btn inti-btn-ghost">Buka Timesheet</a>
      </div>
    </div>

    <div class="inti-responsive-scroll">
      <table class="inti-table">
        <thead>
          <tr>
            <th>Tanggal</th>
            <th>Clock In</th>
            <th>Clock Out</th>
            <th>Keterlambatan</th>
            <th>Durasi</th>
            <th>Status</th>
          </tr>
        </thead>
        <tbody>
        @forelse($history as $attendance)
          <tr>
            <td><strong>{{ $attendance->tanggal->translatedFormat('d M Y') }}</strong></td>
            <td>{{ $attendance->jam_masuk ? substr($attendance->jam_masuk, 0, 5) : '--:--' }}</td>
            <td>{{ $attendance->jam_pulang ? substr($attendance->jam_pulang, 0, 5) : '--:--' }}</td>
            <td>{{ $attendance->menitTelat() }} menit</td>
            <td>{{ $attendance->durasiKerjaFormat() }}</td>
            <td><span class="inti-badge inti-badge-neutral">{{ $attendance->status() }}</span></td>
          </tr>
        @empty
          <tr>
            <td colspan="6">
              <div class="inti-empty">
                <span class="material-symbols-outlined inti-empty-icon">event_busy</span>
                <div class="inti-empty-title">Belum ada riwayat</div>
                <div class="inti-empty-text">Belum ada data presensi untuk periode yang dipilih.</div>
              </div>
            </td>
          </tr>
        @endforelse
        </tbody>
      </table>
    </div>
    <div style="margin-top:1rem">{{ $history->links() }}</div>
  </div>


@if (session('overtime.prompt'))
    @php($prompt = session('overtime.prompt'))
    <div id="overtime-decision-modal" style="position:fixed;inset:0;background:rgba(0,20,40,.45);display:grid;place-items:center;padding:1rem;z-index:1000">
        <div class="inti-card inti-card-pad" style="width:min(560px,100%);box-shadow:0 20px 60px rgba(0,0,0,.18)">
            <div class="inti-page-header" style="margin-bottom:1rem">
                <div>
                    <div class="inti-eyebrow">CLOCK OUT BERHASIL</div>
                    <h2 class="inti-title" style="font-size:24px">Apakah waktu setelah jam kerja akan diajukan sebagai lembur?</h2>
                </div>
                <span class="inti-badge inti-badge-warning">Band {{ $prompt['band_code'] }}</span>
            </div>
            <div class="inti-alert inti-alert-info">
                Clock In <strong>{{ substr($prompt['clock_in'],0,5) }}</strong> · Clock Out <strong>{{ substr($prompt['clock_out'],0,5) }}</strong> ·
                Jam lembur <strong>{{ $prompt['start_time'] }}–{{ $prompt['end_time'] }}</strong>.
            </div>
            <p class="inti-subtitle" style="margin-top:1rem">Jika <strong>Ya</strong>, sistem membuka halaman pengajuan lembur. Jika <strong>Tidak</strong>, presensi tetap tercatat sebagai hadir dan tidak ada pengajuan lembur dibuat.</p>
            <div style="display:flex;justify-content:flex-end;gap:.6rem;margin-top:1.25rem">
                <form method="POST" action="{{ route('attendance.my.overtime-dismiss') }}">
                    @csrf
                    <button type="submit" class="inti-btn inti-btn-ghost">Tidak, tetap Hadir</button>
                </form>
                <a href="{{ route('overtime.index') }}" class="inti-btn inti-btn-primary">Ya, Ajukan Lembur</a>
            </div>
        </div>
    </div>
@endif
</div>
@endsection

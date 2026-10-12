@extends('layouts.absensi')
@section('title','Pengajuan Lembur — PT. INTI')
@section('content')
@php($draft = session('overtime.draft'))
<style>
  .overtime-page { max-width: 1280px; margin: 0 auto; }
  .overtime-page, .overtime-page * { box-sizing: border-box; }
  .overtime-page .ot-panel { min-width: 0; }
  .overtime-page .ot-time-grid,
  .overtime-page .ot-metric-grid { display: grid; gap: .75rem; }
  .overtime-page .ot-time-grid { grid-template-columns: repeat(4, minmax(0, 1fr)); margin-top: 1rem; padding: 1rem; border-radius: 12px; background: #eff4ff; }
  .overtime-page .ot-metric-grid { grid-template-columns: repeat(4, minmax(0, 1fr)); margin-top: 1rem; }
  .overtime-page .ot-time-item, .overtime-page .ot-metric { min-width: 0; overflow-wrap: anywhere; }
  .overtime-page .ot-time-item strong { display: block; margin-top: .3rem; font-size: 15px; }
  .overtime-page .ot-metric { padding: .9rem; border: 1px solid #e4e8f0; border-radius: 12px; background: #f8f9ff; }
  .overtime-page .ot-metric strong { display: block; margin-top: .4rem; font-size: 20px; line-height: 1.25; font-variant-numeric: tabular-nums; }
  .overtime-page .ot-metric-excess { background: #fff7ed; border-color: #fed7aa; }
  .overtime-page .ot-excess-notice { display: flex; align-items: flex-start; gap: .75rem; margin-top: 1rem; padding: 1rem; border: 1px solid #fed7aa; border-radius: 12px; background: #fff7ed; color: #9a3412; }
  .overtime-page .ot-excess-notice .ot-notice-icon { flex: 0 0 auto; font-size: 22px; line-height: 1.2; }
  .overtime-page .ot-excess-notice p { margin: .25rem 0 0; line-height: 1.55; overflow-wrap: anywhere; }
  .overtime-page .ot-form-actions { display: flex; flex-wrap: wrap; justify-content: flex-end; align-items: center; gap: .75rem; margin-top: 1rem; }
  .overtime-page .ot-form-actions button { max-width: 100%; white-space: normal; }
  .overtime-page .ot-consent { display: flex; gap: .65rem; align-items: flex-start; margin-top: 1rem; padding: .9rem; border-radius: 12px; background: #f8f9ff; font-size: 12px; line-height: 1.55; color: #43474d; }
  .overtime-page .ot-consent input { flex: 0 0 auto; margin-top: 3px; }
  .overtime-page .ot-evidence-list { display: grid; gap: .85rem; margin-top: 1rem; }
  .overtime-page .ot-evidence-row { display: flex; justify-content: space-between; gap: 1rem; align-items: baseline; padding-bottom: .65rem; border-bottom: 1px solid #edf0f5; }
  .overtime-page .ot-evidence-row strong { text-align: right; overflow-wrap: anywhere; }
  .overtime-page .ot-form-message .inti-alert { overflow-wrap: anywhere; white-space: pre-line; }
  @media (max-width: 900px) {
    .overtime-page .ot-time-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); }
    .overtime-page .ot-metric-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); }
  }
  @media (max-width: 560px) {
    .overtime-page .ot-time-grid { gap: .85rem; padding: .85rem; }
    .overtime-page .ot-metric-grid { gap: .6rem; }
    .overtime-page .ot-metric { padding: .75rem; }
    .overtime-page .ot-metric strong { font-size: 18px; }
    .overtime-page .ot-form-actions { flex-direction: column-reverse; align-items: stretch; }
    .overtime-page .ot-form-actions button { width: 100%; }
  }
</style>
<div class="inti-page overtime-page">
  <div class="inti-page-header">
    <div>
      <div class="inti-eyebrow">LAYANAN LEMBUR</div>
      <h1 class="inti-title">Pengajuan Jam Lembur</h1>
      <p class="inti-subtitle">Pengajuan dilakukan setelah jam pulang dan divalidasi berdasarkan presensi aktual.</p>
    </div>
    <span class="inti-badge inti-badge-info">Karyawan → VP Divisi → HR</span>
  </div>

  @if(!$draft)
    <div class="inti-card inti-card-pad">
      <div class="inti-alert inti-alert-info">
        Halaman ini digunakan setelah jam pulang. Jika presensi hari ini memenuhi prasyarat lembur, sistem akan menampilkan pilihan untuk mengajukannya.
      </div>
    </div>
  @else
    @php($actualOt = (int) ($draft['actual_overtime_minutes'] ?? 0))
    @php($roundedOt = (int) ($draft['rounded_overtime_minutes'] ?? $actualOt))
    @php($recognizedOt = (int) ($draft['recognized_overtime_minutes'] ?? $draft['recognized_minutes'] ?? $roundedOt))
    @php($excessOt = (int) ($draft['excess_minutes'] ?? max(0, $roundedOt - $recognizedOt)))
    @php($maximumOt = (int) ($draft['maximum_minutes_event'] ?? 0))

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
      <div class="ot-panel lg:col-span-8 inti-card inti-card-pad">
        <div class="inti-section-title">Konfirmasi Pengajuan Lembur</div>
        <div class="inti-subtitle" style="margin-bottom:1.25rem">Tanggal dan jam berikut berasal dari AttendanceLog dan tidak dapat diubah dari halaman ini.</div>

        <form id="overtime-form">
          @csrf
          <input type="hidden" id="submission_mode" name="submission_mode" value="POST_SUBMITTED">
          <input type="hidden" id="overtime_date" name="overtime_date" value="{{ $draft['date'] }}">
          <input type="hidden" id="start_time" name="start_time" value="{{ $draft['start_time'] }}">
          <input type="hidden" id="end_time" name="end_time" value="{{ $draft['end_time'] }}">

          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="inti-card" style="padding:1rem;background:#f8f9ff">
              <div class="inti-kpi-label">Tanggal</div>
              <div class="inti-kpi-value" style="font-size:18px">{{ \Carbon\Carbon::parse($draft['date'])->translatedFormat('d F Y') }}</div>
            </div>
            <div class="inti-card" style="padding:1rem;background:#f8f9ff">
              <div class="inti-kpi-label">Kategori</div>
              <div class="inti-kpi-value" style="font-size:18px">{{ $draft['day_type'] === 'WORKDAY' ? 'Hari Kerja' : ($draft['day_type'] === 'WEEKEND' ? 'Akhir Pekan' : 'Libur Nasional') }}</div>
            </div>
          </div>

          <div class="ot-time-grid">
            <div class="ot-time-item"><div class="inti-label">Jam Masuk</div><strong>{{ substr($draft['clock_in'],0,5) }}</strong></div>
            <div class="ot-time-item"><div class="inti-label">Jam Pulang</div><strong>{{ substr($draft['clock_out'],0,5) }}</strong></div>
            <div class="ot-time-item"><div class="inti-label">Mulai OT</div><strong>{{ $draft['start_time'] }}</strong></div>
            <div class="ot-time-item"><div class="inti-label">Selesai OT</div><strong>{{ $draft['end_time'] }}</strong></div>
          </div>

          <div class="ot-metric-grid">
            <div class="ot-metric">
              <div class="inti-kpi-label">OT aktual</div>
              <strong>{{ intdiv($actualOt,60) }}j {{ $actualOt % 60 }}m</strong>
            </div>
            <div class="ot-metric">
              <div class="inti-kpi-label">Pembulatan</div>
              <strong>{{ intdiv($roundedOt,60) }}j {{ $roundedOt % 60 }}m</strong>
            </div>
            <div class="ot-metric">
              <div class="inti-kpi-label">OT diakui</div>
              <strong>{{ intdiv($recognizedOt,60) }}j {{ $recognizedOt % 60 }}m</strong>
            </div>
            <div class="ot-metric ot-metric-excess">
              <div class="inti-kpi-label">Kelebihan</div>
              <strong>{{ intdiv($excessOt,60) }}j {{ $excessOt % 60 }}m</strong>
            </div>
          </div>

          @if($excessOt > 0)
            <div class="ot-excess-notice" role="status" aria-live="polite">
              <span class="material-symbols-outlined ot-notice-icon" aria-hidden="true">info</span>
              <div>
                <strong>Durasi lembur melebihi batas maksimum</strong>
                <p>Pengajuan ke VP tetap menggunakan durasi yang diakui, yaitu <strong>{{ intdiv($recognizedOt,60) }} jam {{ $recognizedOt % 60 }} menit</strong>.</p>
                <p>Durasi berlebih (<strong>{{ intdiv($excessOt,60) }} jam {{ $excessOt % 60 }} menit</strong>) dicatat sebagai excess dan tidak menambah kuota lembur.</p>
              </div>
            </div>
          @endif

          <div style="margin-top:1rem">
            <label class="inti-label" for="reason">Uraian Tugas / Pekerjaan *</label>
            <textarea class="inti-input" id="reason" name="reason" rows="4" required placeholder="Jelaskan kebutuhan dan tugas pekerjaan selama lembur..."></textarea>
          </div>

          <label class="ot-consent">
            <input type="checkbox" id="employee_consent" name="employee_consent" value="1">
            <span>Saya menyatakan data pengajuan lembur ini benar dan bersedia diproses sesuai alur Karyawan → VP Divisi → HR.</span>
          </label>

          <div id="form-message" class="ot-form-message" style="margin-top:1rem"></div>
          <div class="ot-form-actions">
            <button class="inti-btn inti-btn-ghost" id="btn-dismiss-overtime" type="button">Tidak, Tetap Hadir</button>
            <button class="inti-btn inti-btn-primary" id="btn-submit" type="button"><span class="material-symbols-outlined">send</span>Ajukan {{ intdiv($recognizedOt,60) }}j {{ $recognizedOt % 60 }}m ke VP</button>
          </div>
        </form>
      </div>

      <div class="ot-panel lg:col-span-4 inti-card inti-card-pad" style="height:max-content">
        <div class="inti-section-title">Evidence Presensi</div>
        <div class="ot-evidence-list">
          <div class="ot-evidence-row"><span class="inti-kpi-label">Band</span><strong>Band {{ $draft['band_code'] }}</strong></div>
          <div class="ot-evidence-row"><span class="inti-kpi-label">Durasi kerja aktual</span><strong>{{ intdiv((int)$draft['actual_work_minutes'],60) }}j {{ ((int)$draft['actual_work_minutes']) % 60 }}m</strong></div>
          <div class="ot-evidence-row"><span class="inti-kpi-label">Batas OT</span><strong>{{ intdiv($maximumOt,60) }}j {{ $maximumOt % 60 }}m</strong></div>
          <div class="ot-evidence-row"><span class="inti-kpi-label">Minimum lembur</span><strong>{{ $draft['minimum_minutes'] }} menit</strong></div>
        </div>
        <div class="inti-alert inti-alert-info" style="margin-top:1.25rem">Jam pulang menjadi bukti dari server. Jam lembur tidak dapat diubah dari browser. Validasi akhir dilakukan server saat pengajuan.</div>
      </div>
    </div>
  @endif
</div>
@endsection

@section('scripts')
@if($draft)
<script>
const btn = document.getElementById('btn-submit');
const dismissBtn = document.getElementById('btn-dismiss-overtime');


dismissBtn?.addEventListener('click', async () => {
  if (dismissBtn.disabled) return;

  const originalLabel = dismissBtn.textContent;
  dismissBtn.disabled = true;
  dismissBtn.textContent = 'Menyimpan presensi...';

  try {
    const response = await fetch(
      "{{ route('attendance.my.overtime-dismiss') }}",
      {
        method: 'POST',
        headers: {
          'X-CSRF-TOKEN': "{{ csrf_token() }}",
          'Accept': 'text/html'
        },
        credentials: 'same-origin',
        redirect: 'follow'
      }
    );

    if (!response.ok) {
      throw new Error(
        'Presensi belum berhasil dikonfirmasi. Silakan coba lagi.'
      );
    }

    window.location.replace("{{ route('dashboard') }}");
  } catch (error) {
    dismissBtn.disabled = false;
    dismissBtn.textContent = originalLabel;

    const message = document.getElementById('form-message');

    if (message) {
      const alert = document.createElement('div');
      alert.className = 'inti-alert inti-alert-danger';
      alert.textContent = error.message ||
        'Terjadi kesalahan saat memproses pilihan.';

      message.replaceChildren(alert);
    }
  }
});

btn?.addEventListener('click', async () => {
  const f = document.getElementById('form-message');
  const consent = document.getElementById('employee_consent');
  const reason = document.getElementById('reason').value.trim();

  if (!consent.checked) {
    f.innerHTML = '<div class="inti-alert inti-alert-danger">Centang persetujuan karyawan sebelum mengirim pengajuan.</div>';
    return;
  }
  if (!reason) {
    f.innerHTML = '<div class="inti-alert inti-alert-danger">Uraian tugas wajib diisi.</div>';
    return;
  }

  btn.disabled = true;

  const payload = {
    overtime_date: document.getElementById('overtime_date').value,
    category: '{{ $draft['day_type'] === 'NATIONAL_HOLIDAY' ? 'hari_libur_nasional' : ($draft['day_type'] === 'WEEKEND' ? 'akhir_pekan' : 'hari_kerja') }}',
    start_time: document.getElementById('start_time').value,
    end_time: document.getElementById('end_time').value,
    reason,
    submission_mode: 'POST_SUBMITTED',
    employee_consent: true
  };

  try {
    const r = await fetch("{{ route('overtime.store') }}", {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
        'X-CSRF-TOKEN': '{{ csrf_token() }}'
      },
      body: JSON.stringify(payload)
    });

    const d = await r.json().catch(() => ({}));
    if (!r.ok) {
      throw new Error(d.message || Object.values(d.errors || {}).flat().join('\n') || 'Pengajuan gagal.');
    }

    const calc = d.calculation || {};
    const excess = Number(calc.excess_minutes || 0);
    const recognized = Number(calc.recognized_minutes || {{ $recognizedOt }});
    const extra = excess > 0
      ? ' Excess ' + Math.floor(excess / 60) + 'j ' + (excess % 60) + 'm tidak diperhitungkan ke quota.'
      : '';

    f.innerHTML = '<div class="inti-alert inti-alert-success">Pengajuan ' + Math.floor(recognized / 60) + 'j ' + (recognized % 60) + 'm berhasil dikirim ke VP Divisi.' + extra + '</div>';
    btn.textContent = 'Pengajuan terkirim';
  } catch (err) {
    btn.disabled = false;
    f.innerHTML = '<div class="inti-alert inti-alert-danger">' + err.message + '</div>';
  }
});
</script>
@endif
@endsection

@extends('layouts.absensi')
@section('title','Pengajuan Lembur — PT. INTI')
@section('content')
@php($draft = session('overtime.draft'))
<div class="inti-page">
  <div class="inti-page-header">
    <div>
      <div class="inti-eyebrow">LAYANAN LEMBUR</div>
      <h1 class="inti-title">Pengajuan Jam Lembur</h1>
      <p class="inti-subtitle">Pengajuan dilakukan setelah Clock Out dan divalidasi berdasarkan presensi aktual.</p>
    </div>
    <span class="inti-badge inti-badge-info">Karyawan → VP Divisi → HR</span>
  </div>

  @if(!$draft)
    <div class="inti-card inti-card-pad">
      <div class="inti-alert inti-alert-info">
        Halaman ini digunakan setelah Clock Out. Jika presensi hari ini memenuhi prasyarat lembur, sistem akan menampilkan pilihan untuk mengajukannya.
      </div>
    </div>
  @else
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
      <div class="lg:col-span-8 inti-card inti-card-pad">
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

          <div class="grid grid-cols-1 md:grid-cols-4 gap-4" style="margin-top:1rem;padding:1rem;border-radius:12px;background:#eff4ff">
            <div><div class="inti-label">Clock In</div><strong>{{ substr($draft['clock_in'],0,5) }}</strong></div>
            <div><div class="inti-label">Clock Out</div><strong>{{ substr($draft['clock_out'],0,5) }}</strong></div>
            <div><div class="inti-label">Mulai OT</div><strong>{{ $draft['start_time'] }}</strong></div>
            <div><div class="inti-label">Selesai OT</div><strong>{{ $draft['end_time'] }}</strong></div>
          </div>

          <div style="margin-top:1rem">
            <label class="inti-label" for="reason">Uraian Tugas / Pekerjaan *</label>
            <textarea class="inti-input" id="reason" name="reason" rows="4" required placeholder="Jelaskan kebutuhan dan tugas pekerjaan selama lembur..."></textarea>
          </div>

          <label style="display:flex;gap:.6rem;align-items:flex-start;margin-top:1rem;padding:.9rem;border-radius:12px;background:#f8f9ff;font-size:12px;color:#43474d">
            <input type="checkbox" id="employee_consent" name="employee_consent" value="1" style="margin-top:2px">
            <span>Saya menyatakan data pengajuan lembur ini benar dan bersedia diproses sesuai alur Karyawan → VP Divisi → HR.</span>
          </label>

          <div id="form-message" style="margin-top:1rem"></div>
          <div style="display:flex;justify-content:flex-end;gap:.6rem;margin-top:1rem">
            <form method="POST" action="{{ route('attendance.my.overtime-dismiss') }}" style="display:inline">
              @csrf
              <button class="inti-btn inti-btn-ghost" type="submit">Tidak, Tetap Hadir</button>
            </form>
            <button class="inti-btn inti-btn-primary" id="btn-submit" type="button"><span class="material-symbols-outlined">send</span>Ajukan ke VP</button>
          </div>
        </form>
      </div>

      <div class="lg:col-span-4 inti-card inti-card-pad" style="height:max-content">
        <div class="inti-section-title">Evidence Presensi</div>
        <div style="margin-top:1rem;display:grid;gap:.75rem">
          <div><div class="inti-kpi-label">Band</div><strong>Band {{ $draft['band_code'] }}</strong></div>
          <div><div class="inti-kpi-label">Durasi kerja aktual</div><strong>{{ intdiv((int)$draft['actual_work_minutes'],60) }}j {{ ((int)$draft['actual_work_minutes']) % 60 }}m</strong></div>
          <div><div class="inti-kpi-label">Minimum lembur</div><strong>{{ $draft['minimum_minutes'] }} menit</strong></div>
        </div>
        <div class="inti-alert inti-alert-info" style="margin-top:1.25rem">Clock Out 16:30 tepat waktu dan tidak membuka halaman lembur. Validasi akhir tetap dilakukan server saat pengajuan.</div>
      </div>
    </div>
  @endif
</div>
@endsection
@section('scripts')
@if($draft)
<script>
const btn=document.getElementById('btn-submit');
btn.addEventListener('click',async()=>{
  const f=document.getElementById('form-message');
  const consent=document.getElementById('employee_consent');
  const reason=document.getElementById('reason').value.trim();
  if(!consent.checked){f.innerHTML='<div class="inti-alert inti-alert-danger">Centang persetujuan karyawan sebelum mengirim pengajuan.</div>';return;}
  if(!reason){f.innerHTML='<div class="inti-alert inti-alert-danger">Uraian tugas wajib diisi.</div>';return;}
  const payload={
    overtime_date:document.getElementById('overtime_date').value,
    category:'{{ $draft['day_type'] === 'NATIONAL_HOLIDAY' ? 'hari_libur_nasional' : ($draft['day_type'] === 'WEEKEND' ? 'akhir_pekan' : 'hari_kerja') }}',
    start_time:document.getElementById('start_time').value,
    end_time:document.getElementById('end_time').value,
    reason,
    submission_mode:'POST_SUBMITTED',
    employee_consent:true
  };
  try{
    const r=await fetch("{{ route('overtime.store') }}",{method:'POST',headers:{'Content-Type':'application/json','Accept':'application/json','X-CSRF-TOKEN':'{{ csrf_token() }}'},body:JSON.stringify(payload)});
    const d=await r.json().catch(()=>({}));
    if(!r.ok)throw new Error(d.message||Object.values(d.errors||{}).flat().join('\n')||'Pengajuan gagal.');
    f.innerHTML='<div class="inti-alert inti-alert-success">'+(d.message||'Pengajuan berhasil dikirim ke VP Divisi.')+'</div>';
    btn.disabled=true;
  }catch(err){f.innerHTML='<div class="inti-alert inti-alert-danger">'+err.message+'</div>';}
});
</script>
@endif
@endsection

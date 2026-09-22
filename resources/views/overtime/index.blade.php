@extends('layouts.absensi')

@section('title', 'Pengajuan Lembur - PT. INTI')

@section('content')
<div class="flex flex-col gap-space-lg w-full">

  <!-- Banner Header -->
  <div class="relative overflow-hidden rounded-xl bg-surface-container-lowest shadow-[0_1px_8px_rgba(0,0,0,0.04)] p-space-xl">
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-space-md">
      <div>
        <div class="flex items-center gap-2 mb-1">
          <span class="px-2.5 py-0.5 rounded-full bg-secondary-container text-on-secondary-container font-label-sm text-label-sm font-semibold">
            Formulir Mandiri
          </span>
          <span class="text-on-surface-variant text-xs">•</span>
          <span class="font-body-sm text-body-sm text-on-surface-variant">Layanan Lembur Karyawan</span>
        </div>
        <h1 class="font-headline-lg text-headline-lg text-primary tracking-tight">
          Pengajuan Jam Lembur
        </h1>
        <p class="font-body-md text-body-md text-on-surface-variant">
          Isi estimasi jam kerja dan uraian tugas untuk pengajuan verifikasi berjenjang (VP Divisi &amp; HR).
        </p>
      </div>
    </div>
  </div>

  <!-- Form Section -->
  <div class="grid grid-cols-1 lg:grid-cols-12 gap-space-lg">
    <div class="lg:col-span-8 bg-surface-container-lowest rounded-xl shadow-[0_1px_8px_rgba(0,0,0,0.04)] p-space-xl flex flex-col gap-space-lg">
      <div class="flex items-center justify-between border-b border-surface-container-low pb-space-sm">
        <h2 class="font-headline-sm text-headline-sm text-primary">Buat Permohonan Baru</h2>
        <span class="font-body-sm text-body-sm text-secondary font-semibold">Otorisasi 2-Tahap</span>
      </div>

      <form id="overtime-form" class="flex flex-col gap-space-md">
        @csrf
        <input type="hidden" name="submission_mode" value="PRE_SUBMITTED">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-space-md">
          <div class="flex flex-col gap-1.5">
            <label class="font-title-sm text-title-sm text-on-surface" for="overtime_date">Tanggal Lembur <span class="text-error">*</span></label>
            <input type="date" id="overtime_date" name="overtime_date" class="w-full bg-surface-container-lowest text-on-surface font-body-md text-body-md rounded-lg py-2.5 px-3 border border-outline-variant/50 focus:outline-none focus:ring-2 focus:ring-primary" required>
          </div>
          <div class="flex flex-col gap-1.5">
            <label class="font-title-sm text-title-sm text-on-surface" for="category">Kategori Lembur</label>
            <select id="category" name="category" class="w-full bg-surface-container-lowest text-on-surface font-body-md text-body-md rounded-lg py-2.5 px-3 border border-outline-variant/50 focus:outline-none focus:ring-2 focus:ring-primary">
              <option value="hari_kerja">Hari Kerja (Weekday)</option>
              <option value="akhir_pekan">Akhir Pekan / Libur (Weekend)</option>
            </select>
          </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-space-md p-space-md bg-surface-container-low rounded-xl">
          <div class="flex flex-col gap-1.5">
            <label class="font-title-sm text-title-sm text-on-surface" for="start_time">Jam Mulai</label>
            <input type="time" id="start_time" value="16:30" class="bg-surface-container-lowest text-on-surface font-body-md text-body-md rounded-lg py-2 px-3 border border-outline-variant/50">
          </div>
          <div class="flex flex-col gap-1.5">
            <label class="font-title-sm text-title-sm text-on-surface" for="end_time">Jam Selesai</label>
            <input type="time" id="end_time" value="19:30" class="bg-surface-container-lowest text-on-surface font-body-md text-body-md rounded-lg py-2 px-3 border border-outline-variant/50">
          </div>
          <div class="flex flex-col justify-center items-center bg-surface-container-lowest rounded-lg p-2">
            <span class="font-label-sm text-label-sm text-on-surface-variant uppercase">Estimasi</span>
            <span class="font-headline-md text-headline-md text-secondary font-bold" id="calculated_hours">3.0 Jam</span>
          </div>
        </div>

        <div class="flex flex-col gap-1.5">
          <label class="font-title-sm text-title-sm text-on-surface" for="reason">Uraian Tugas / Pekerjaan <span class="text-error">*</span></label>
          <textarea id="reason" rows="3" class="w-full bg-surface-container-lowest text-on-surface font-body-md text-body-md rounded-lg p-3 border border-outline-variant/50 focus:outline-none focus:ring-2 focus:ring-primary" placeholder="Jelaskan kebutuhan dan tugas pekerjaan selama lembur..." required></textarea>
        </div>

        <div class="rounded-xl border border-outline-variant/50 bg-surface-container-low p-space-md">
          <label class="flex items-start gap-3 cursor-pointer">
            <input
              type="checkbox"
              id="employee_consent"
              name="employee_consent"
              value="1"
              class="mt-1 h-4 w-4 rounded border-outline-variant text-primary focus:ring-primary"
              required
            >
            <span class="font-body-sm text-body-sm text-on-surface">
              Saya menyatakan bahwa data pengajuan lembur ini benar dan saya memberikan persetujuan untuk diproses melalui alur Karyawan, VP Divisi, dan HR sesuai kebijakan yang berlaku.
              <span class="block mt-1 text-xs text-on-surface-variant">
                Persetujuan ini dicatat bersama waktu, IP, perangkat/browser, dan versi pernyataan sebagai bukti audit.
              </span>
            </span>
          </label>
        </div>

        <div class="flex items-center justify-end gap-space-sm pt-space-xs">
          <button type="reset" class="px-space-md py-2 rounded-lg bg-surface-container-low text-on-surface font-title-sm text-title-sm">Batal</button>
          <button type="button" id="btn-submit" class="px-space-xl py-2 rounded-lg bg-primary-container text-on-primary font-title-sm text-title-sm shadow-sm hover:bg-primary transition-all">Kirim ke VP</button>
        </div>
      </form>
    </div>

    <!-- Info Tracker -->
    <div class="lg:col-span-4 bg-surface-container-lowest rounded-xl shadow-[0_1px_8px_rgba(0,0,0,0.04)] p-space-lg flex flex-col gap-space-md h-fit">
      <span class="font-title-sm text-title-sm text-primary">Alur Otorisasi Lembur</span>
      <div class="flex flex-col gap-space-md relative pl-6 border-l-2 border-surface-container-high">
        <div>
          <span class="font-title-sm text-title-sm text-primary">1. Pengajuan Karyawan</span>
          <p class="font-body-sm text-body-sm text-on-surface-variant">Input estimasi jam dan alasan kerja.</p>
        </div>
        <div>
          <span class="font-title-sm text-title-sm text-primary">2. Review VP Divisi</span>
          <p class="font-body-sm text-body-sm text-on-surface-variant">Persetujuan atasan langsung/VP unit.</p>
        </div>
        <div>
          <span class="font-title-sm text-title-sm text-on-surface">3. Verifikasi HR</span>
          <p class="font-body-sm text-body-sm text-on-surface-variant">Sinkronisasi log mesin fingerprint/Face ID.</p>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection

@section('scripts')
<script>
  const startTime = document.getElementById('start_time');
  const endTime = document.getElementById('end_time');
  const calcText = document.getElementById('calculated_hours');

  function updateHours() {
    if (startTime.value && endTime.value) {
      const start = startTime.value.split(':');
      const end = endTime.value.split(':');
      let diff = (new Date(0,0,0, end[0], end[1]) - new Date(0,0,0, start[0], start[1])) / 3600000;
      if (diff < 0) diff += 24;
      calcText.textContent = `${diff.toFixed(1)} Jam`;
    }
  }

  startTime.addEventListener('change', updateHours);
  endTime.addEventListener('change', updateHours);

  document.getElementById('btn-submit').addEventListener('click', function(e) {
    e.preventDefault();

    const date = document.getElementById('overtime_date').value;
    const reason = document.getElementById('reason').value;
    const consent = document.getElementById('employee_consent').checked;

    if (!date || !reason || !consent) {
      alert('Mohon lengkapi tanggal, uraian alasan lembur, dan persetujuan pengajuan!');
      return;
    }

    fetch("{{ route('overtime.store') }}", {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
        "Accept": "application/json",
        "X-CSRF-TOKEN": "{{ csrf_token() }}"
      },
body: JSON.stringify({
  overtime_date: date,
  submission_mode: 'PRE_SUBMITTED',
  category: document.getElementById('category').value,
  start_time: startTime.value,
  end_time: endTime.value,
  reason: reason,
  employee_consent: consent
})
    })
    .then(async response => {
      const data = await response.json();
      if (!response.ok) {
        const errorMessage = data.message || Object.values(data.errors || {}).flat().join('\n');
        throw new Error(errorMessage || 'Gagal menyimpan data.');
      }
      return data;
    })
    .then(data => {
      alert(data.message || 'Pengajuan lembur berhasil dikirim ke VP!');
      document.getElementById('overtime-form').reset();
      updateHours();
    })
    .catch(err => {
      console.error('Error submit overtime:', err);
      alert('Gagal mengirim pengajuan:\n' + err.message);
    });
  });
</script>
@endsection
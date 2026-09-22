@extends('layouts.absensi')

@section('title', 'Persetujuan Lembur VP - PT. INTI')

@section('content')
<div class="flex flex-col gap-space-lg w-full">

  <!-- Banner VP -->
  <div class="relative overflow-hidden rounded-xl bg-surface-container-lowest shadow-[0_1px_8px_rgba(0,0,0,0.04)] p-space-xl">
    <div>
      <span class="px-2.5 py-0.5 rounded-full bg-secondary-container text-on-secondary-container font-label-sm text-label-sm font-semibold">
        Otorisasi VP Divisi
      </span>
      <h1 class="font-headline-lg text-headline-lg text-primary tracking-tight mt-1">
        Persetujuan Lembur Tim
      </h1>
      <p class="font-body-md text-body-md text-on-surface-variant">
        Tinjau dan setujui permohonan lembur dari anggota divisi sebelum diteruskan ke HR.
      </p>
    </div>
  </div>

  <!-- Cards Metrics -->
  <div class="grid grid-cols-1 md:grid-cols-3 gap-space-md">
    <div class="bg-surface-container-lowest rounded-xl p-space-lg shadow-[0_1px_8px_rgba(0,0,0,0.04)] flex items-center justify-between">
      <div>
        <span class="font-label-sm text-label-sm text-on-surface-variant uppercase">Menunggu Approval</span>
        <div class="font-headline-lg text-headline-lg text-on-tertiary-container mt-1" id="stat-pending-count">0</div>
      </div>
      <span class="material-symbols-outlined text-[32px] text-primary">pending_actions</span>
    </div>

    <div class="bg-surface-container-lowest rounded-xl p-space-lg shadow-[0_1px_8px_rgba(0,0,0,0.04)] flex items-center justify-between">
      <div>
        <span class="font-label-sm text-label-sm text-on-surface-variant uppercase">Unit / Divisi</span>
        <div class="font-title-md text-title-md text-primary mt-1">Bisnis &amp; Teknologi</div>
      </div>
      <span class="material-symbols-outlined text-[32px] text-primary">groups</span>
    </div>

    <div class="bg-surface-container-lowest rounded-xl p-space-lg shadow-[0_1px_8px_rgba(0,0,0,0.04)] flex items-center justify-between">
      <div>
        <span class="font-label-sm text-label-sm text-on-surface-variant uppercase">Status Otorisasi</span>
        <div class="font-title-md text-title-md text-secondary mt-1">Atasan Direct</div>
      </div>
      <span class="material-symbols-outlined text-[32px] text-secondary">verified</span>
    </div>
  </div>

  <!-- Table Antrean Approval VP -->
  <div class="bg-surface-container-lowest rounded-xl shadow-[0_1px_8px_rgba(0,0,0,0.04)] p-space-xl flex flex-col gap-space-md">
    <div class="flex items-center justify-between">
      <h2 class="font-headline-sm text-headline-sm text-primary">Daftar Antrean Approval VP</h2>
      <span class="px-2 py-0.5 rounded-full bg-surface-container text-primary font-label-sm text-label-sm font-bold" id="badge-total-pending">0 Tiket</span>
    </div>

    <div class="overflow-x-auto">
      <table class="w-full text-left border-collapse">
        <thead>
          <tr class="bg-surface-container-low text-on-surface-variant font-label-sm text-label-sm uppercase">
            <th class="py-3 px-space-md rounded-l-lg">No. Tiket</th>
            <th class="py-3 px-space-md">Karyawan</th>
            <th class="py-3 px-space-md">Tanggal &amp; Jam</th>
            <th class="py-3 px-space-md">Durasi</th>
            <th class="py-3 px-space-md">Uraian Pekerjaan</th>
            <th class="py-3 px-space-md text-right rounded-r-lg">Aksi VP</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-surface-container-low text-on-surface font-body-md text-body-md" id="vp-pending-table-body">
          <tr>
            <td colspan="6" class="py-8 text-center text-on-surface-variant">Memuat data pengajuan karyawan...</td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>

</div>
@endsection

@section('scripts')
<script>
  document.addEventListener('DOMContentLoaded', loadVpPendingData);

  function loadVpPendingData() {
    fetch("{{ route('overtime.vp.pending') }}")
      .then(res => res.json())
      .then(res => {
        const tbody = document.getElementById('vp-pending-table-body');
        const pendingData = res.data || [];

        document.getElementById('stat-pending-count').textContent = pendingData.length;
        document.getElementById('badge-total-pending').textContent = `${pendingData.length} Tiket`;

        if (pendingData.length === 0) {
          tbody.innerHTML = `<tr><td colspan="6" class="py-8 text-center text-on-surface-variant italic">Tidak ada antrean lembur yang membutuhkan approval VP.</td></tr>`;
          return;
        }

        tbody.innerHTML = pendingData.map(item => `
          <tr class="hover:bg-surface-container-low/50 transition-colors">
            <td class="py-4 px-space-md font-mono text-xs font-semibold text-primary">${item.ticket_number}</td>
            <td class="py-4 px-space-md">
              <div class="font-title-sm text-title-sm text-primary">${item.user?.name || 'Karyawan'}</div>
              <div class="font-body-sm text-body-sm text-on-surface-variant">${item.user?.email || ''}</div>
            </td>
            <td class="py-4 px-space-md">
              <div class="font-title-sm text-title-sm">${item.overtime_date}</div>
              <div class="font-body-sm text-body-sm text-on-surface-variant">${item.start_time} - ${item.end_time} WIB</div>
            </td>
            <td class="py-4 px-space-md font-title-sm text-secondary font-bold">${item.estimated_hours} Jam</td>
            <td class="py-4 px-space-md text-body-sm text-on-surface-variant max-w-xs truncate">${item.reason}</td>
            <td class="py-4 px-space-md text-right">
              <div class="inline-flex items-center gap-1">
                <button type="button" onclick="approveOvertime('${item.id}')" class="px-3 py-1.5 rounded-lg bg-secondary text-on-secondary font-title-sm text-title-sm hover:opacity-90">Setujui</button>
                <button type="button" onclick="rejectOvertime('${item.id}')" class="px-3 py-1.5 rounded-lg bg-red-600 text-white font-title-sm text-title-sm hover:bg-red-700">Tolak</button>
              </div>
            </td>
          </tr>
        `).join('');
      })
      .catch(err => console.error(err));
  }

  async function submitVpDecision(id, action, notes = null) {
    try {
      const response = await fetch(`/overtime/vp/approve/${id}`, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json',
          'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ action, notes })
      });

      const data = await response.json().catch(() => ({}));

      if (!response.ok) {
        throw new Error(data.message || 'Keputusan VP gagal diproses.');
      }

      alert(
        data.message
          || (action === 'APPROVE'
            ? 'Pengajuan lembur berhasil disetujui.'
            : 'Pengajuan lembur berhasil ditolak.')
      );

      loadVpPendingData();
    } catch (error) {
      alert(error.message || 'Terjadi kesalahan saat memproses keputusan VP.');
    }
  }

  function approveOvertime(id) {
    if (!confirm('Setujui pengajuan lembur ini?')) return;
    submitVpDecision(id, 'APPROVE');
  }

  function rejectOvertime(id) {
    const notes = prompt(
      'Masukkan alasan penolakan lembur (opsional):',
      ''
    );

    // Cancel pada prompt membatalkan keputusan.
    if (notes === null) return;

    if (!confirm('Tolak pengajuan lembur ini?')) return;

    submitVpDecision(id, 'REJECT', notes.trim() || null);
  }
</script>
@endsection
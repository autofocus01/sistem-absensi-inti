@extends('layouts.absensi')

@section('title', 'Verifikasi Lembur HR - PT. INTI')

@section('content')
<div class="flex flex-col gap-space-lg w-full">

  <!-- Header Banner -->
  <div class="relative overflow-hidden rounded-xl bg-surface-container-lowest shadow-[0_1px_8px_rgba(0,0,0,0.04)] p-space-xl">
    <div>
      <span class="px-2.5 py-0.5 rounded-full bg-secondary-container text-on-secondary-container font-label-sm text-label-sm font-semibold">
        Modul HR Admin
      </span>
      <h1 class="font-headline-lg text-headline-lg text-primary tracking-tight mt-1">
        Verifikasi Jam Lembur (Disetujui VP)
      </h1>
      <p class="font-body-md text-body-md text-on-surface-variant">
        Lakukan verifikasi akhir dengan mencocokkan permohonan lembur yang telah disetujui VP terhadap log jam pulang aktual.
      </p>
    </div>
  </div>

  <!-- Table Card -->
  <div class="bg-surface-container-lowest rounded-xl shadow-[0_1px_8px_rgba(0,0,0,0.04)] p-space-xl flex flex-col gap-space-md">
    <div class="flex items-center justify-between border-b border-surface-container-low pb-space-xs">
      <h2 class="font-headline-sm text-headline-sm text-primary">Antrean Pre-check Payroll</h2>
      <span class="px-2 py-0.5 rounded-full bg-surface-container text-primary font-label-sm text-label-sm font-bold" id="badge-total-hr">0 Tiket</span>
    </div>

    <div class="overflow-x-auto">
      <table class="w-full text-left border-collapse">
        <thead>
          <tr class="bg-surface-container-low text-on-surface-variant font-label-sm text-label-sm uppercase">
            <th class="py-3 px-space-md rounded-l-lg">Karyawan &amp; Divisi</th>
            <th class="py-3 px-space-md">Tanggal &amp; Jam</th>
            <th class="py-3 px-space-md">Estimasi Durasi</th>
            <th class="py-3 px-space-md">Status VP</th>
            <th class="py-3 px-space-md">Uraian Tugas</th>
            <th class="py-3 px-space-md text-right rounded-r-lg">Aksi Final HR</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-surface-container-low text-on-surface font-body-md text-body-md" id="hr-pending-table-body">
          <tr>
            <td colspan="6" class="py-8 text-center text-on-surface-variant">Memuat data verifikasi lembur...</td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>

</div>
@endsection

@section('scripts')
<script>
  document.addEventListener('DOMContentLoaded', loadHrPendingData);

  function loadHrPendingData() {
    // Panggil Endpoint API dengan proteksi catch error lengkap
    fetch("{{ route('overtime.hr.pending') }}", {
      headers: {
        'Accept': 'application/json',
        'X-CSRF-TOKEN': '{{ csrf_token() }}'
      }
    })
    .then(async response => {
      if (!response.ok) {
        throw new Error(`HTTP Error Status: ${response.status}`);
      }
      return response.json();
    })
    .then(res => {
      const tbody = document.getElementById('hr-pending-table-body');
      const pendingData = res.data || [];

      const badgeElem = document.getElementById('badge-total-hr');
      if (badgeElem) badgeElem.textContent = `${pendingData.length} Tiket`;

      if (pendingData.length === 0) {
        tbody.innerHTML = `
          <tr>
            <td colspan="6" class="py-8 text-center text-on-surface-variant italic">
              Tidak ada antrean lembur yang menunggu verifikasi HR saat ini.
            </td>
          </tr>
        `;
        return;
      }

      tbody.innerHTML = pendingData.map(item => {
        // Ambil nama user dan divisi dengan fallback aman
        const userName = (item.user && item.user.name) ? item.user.name : 'Karyawan';
        const deptName = (item.user && item.user.department && item.user.department.name) 
          ? item.user.department.name 
          : ((item.user && item.user.employee && item.user.employee.department && item.user.employee.department.name) 
            ? item.user.employee.department.name 
            : 'Bisnis & Teknologi');
        
        const vpName = (item.vp_approver && item.vp_approver.name) ? item.vp_approver.name : 'VP Divisi';
        const estHours = item.estimated_hours || '0';
        const dateStr = item.overtime_date || '-';
        const startTime = item.start_time || '';
        const endTime = item.end_time || '';
        const reasonStr = item.reason || '-';

        return `
          <tr class="hover:bg-surface-container-low/50 transition-colors">
            <td class="py-4 px-space-md">
              <div class="font-title-sm text-title-sm text-primary">${userName}</div>
              <div class="font-body-sm text-body-sm text-on-surface-variant">${deptName}</div>
            </td>
            <td class="py-4 px-space-md">
              <div class="font-title-sm text-title-sm">${dateStr}</div>
              <div class="font-body-sm text-body-sm text-on-surface-variant">${startTime} - ${endTime} WIB</div>
            </td>
            <td class="py-4 px-space-md font-title-sm text-secondary font-bold">
              ${estHours} Jam
            </td>
            <td class="py-4 px-space-md">
              <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full bg-secondary/10 text-secondary font-label-sm text-label-sm font-semibold" title="Disetujui oleh ${vpName}">
                <span class="material-symbols-outlined text-[14px]">done</span> Approved by ${vpName}
              </span>
            </td>
            <td class="py-4 px-space-md text-body-sm text-on-surface-variant max-w-xs truncate">
              ${reasonStr}
            </td>
            <td class="py-4 px-space-md text-right">
              <button onclick="verifyHR('${item.id}', 'VERIFY')" class="px-3 py-1.5 bg-primary-container text-on-primary rounded-lg font-title-sm text-title-sm hover:bg-primary transition-all shadow-sm">
                Verifikasi Final
              </button>
            </td>
          </tr>
        `;
      }).join('');
    })
    .catch(err => {
      console.error('Error fetching HR pending data:', err);
      const tbody = document.getElementById('hr-pending-table-body');
      tbody.innerHTML = `
        <tr>
          <td colspan="6" class="py-8 text-center text-error font-medium">
            Gagal memuat data verifikasi (${err.message}).
          </td>
        </tr>
      `;
    });
  }

  function verifyHR(id, action) {
    if (!confirm('Konfirmasi verifikasi akhir untuk pengajuan lembur ini?')) return;

    fetch(`/overtime/hr/verify/${id}`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
        'X-CSRF-TOKEN': '{{ csrf_token() }}'
      },
      body: JSON.stringify({ action: action })
    })
    .then(res => res.json())
    .then(data => {
      alert(data.message || 'Pengajuan berhasil diverifikasi!');
      loadHrPendingData();
    })
    .catch(err => console.error('Error verify HR:', err));
  }
</script>
@endsection
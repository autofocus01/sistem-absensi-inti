<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<style>
  body { font-family: sans-serif; color: #0d1c2e; font-size: 12px; }
  h1 { font-size: 18px; color: #001428; margin-bottom: 2px; }
  p.subtitle { color: #43474d; margin-top: 0; margin-bottom: 20px; }
  table { width: 100%; border-collapse: collapse; margin-top: 10px; }
  th, td { padding: 6px 8px; border: 1px solid #c3c6ce; text-align: left; }
  th { background-color: #eff4ff; color: #43474d; text-transform: uppercase; font-size: 10px; }
  .kpi-table td { border: none; padding: 3px 8px 3px 0; }
  .status-normal { color: #006a61; font-weight: bold; }
  .status-review { color: #ba1a1a; font-weight: bold; }
  .footer { margin-top: 30px; font-size: 10px; color: #74777e; }
</style>
</head>
<body>
  <h1>Rekap Absensi Eksekutif &mdash; PT. INTI (Persero)</h1>
  <p class="subtitle">Periode: {{ $namaBulan }} {{ $tahun }}</p>

  <table class="kpi-table">
    <tr><td><strong>Total Tenaga Kerja</strong></td><td>{{ number_format($totalKaryawan) }} orang</td></tr>
    <tr><td><strong>Rata-rata Kehadiran</strong></td><td>{{ $rataKehadiran }}%</td></tr>
    <tr><td><strong>Total Alpha/Mangkir</strong></td><td>{{ $totalAlpha }}</td></tr>
    <tr><td><strong>Total Menit Telat</strong></td><td>{{ number_format($totalMenitTelat) }} menit (&asymp; {{ round($totalMenitTelat / 60, 1) }} jam)</td></tr>
    <tr><td><strong>Disiplin &amp; On-Time Rate</strong></td><td>{{ $tingkatOnTime }}%</td></tr>
    <tr><td><strong>Total Jam Lembur Bulan Ini</strong></td><td>{{ $totalJamLembur }} Jam</td></tr>
    <tr><td><strong>Kelengkapan Data Rekap</strong></td><td>{{ $kelengkapanRekap }}% ({{ $karyawanDenganRekap }}/{{ $totalKaryawan }} karyawan)</td></tr>
    <tr><td><strong>Sebaran Lokasi - Bandung</strong></td><td>{{ $totalHariBandung }} hari ({{ $persenBandung }}%)</td></tr>
    <tr><td><strong>Sebaran Lokasi - Jakarta</strong></td><td>{{ $totalHariJakarta }} hari ({{ $persenJakarta }}%)</td></tr>
  </table>

  <table>
    <thead>
      <tr>
        <th>Divisi</th>
        <th>Total Personel</th>
        <th>Rata-rata Kehadiran</th>
        <th>Total Hari Telat</th>
        <th>Total Alpha</th>
        <th>Status</th>
      </tr>
    </thead>
    <tbody>
      @foreach ($matriksDivisi as $row)
      <tr>
        <td>{{ $row['divisi'] }}</td>
        <td>{{ $row['total_personel'] }}</td>
        <td>{{ $row['rata_kehadiran'] }}%</td>
        <td>{{ $row['total_telat_hari'] }}</td>
        <td>{{ $row['total_alpha'] }}</td>
        <td class="{{ $row['perlu_ditinjau'] ? 'status-review' : 'status-normal' }}">
          {{ $row['perlu_ditinjau'] ? 'Perlu Ditinjau' : 'Normal' }}
        </td>
      </tr>
      @endforeach
    </tbody>
  </table>

  <p class="footer">Dokumen ini dibuat otomatis dari sistem absensi PT. INTI pada {{ now()->translatedFormat('d F Y, H:i') }} WIB.</p>
</body>
</html>
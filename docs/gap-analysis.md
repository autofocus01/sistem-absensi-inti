# Gap Analysis per Fitur — INTI Presence

> Basis evaluasi: kode + 170 test Pest (663 asersi) per 14 Okt 2026.
> Metodologi: setiap angka dikelaskan menjadi **(1) transaksional** — lahir dari
> workflow sistem, **(2) eksternal** — masuk dari luar (file/perangkat), atau
> **(3) turunan** — dihitung dari yang lain. Klasifikasi inilah yang menentukan
> tingkat kepercayaan angka di dashboard.

---

## 1. Peta kesiapan per fitur

| # | Fitur | Kelas data | Status | Kekuatan utama | Gap tersisa |
|---|-------|-----------|--------|----------------|-------------|
| 1 | Clock in/out karyawan | Transaksional | ✅ Matang | Audit `CLOCK_IN/OUT`, lock upsert, rekap sinkron langsung | — |
| 2 | Impor absensi harian | Eksternal | ✅ Matang **+ Fase 1** | Preview→commit, validasi per baris, format paired/scan, penolakan timpa-sumber-lain, **provenance `import_batches` + deteksi file terulang** | Template resmi standar perusahaan (format file yang dikirim HR masih bervariasi) |
| 3 | Impor master karyawan | Eksternal | ✅ Matang **+ Fase 1** | CREATE/UPDATE preview, validasi divisi, anti NIPEG ganda, **provenance batch** | — |
| 4 | Rekap kehadiran | Turunan | ✅ Matang | Invariant jumlah hari, sinkron event-driven (clock & impor), perbaikan rekap stale | **Belum ada scheduler pengaman harian** (hanya manual + event-driven) |
| 5 | Status cuti/sakit/izin/perdin/alpha | Eksternal (manual) | ⚠️ Fase 2 | Sudah ada invariant jumlah hari; perubahan HR teraudit | **Berupa counter bulinan tanpa dokumen/persetujuan di sistem** — sumber misskomunikasi terbesar |
| 6 | Lembur | Transaksional | ✅ Matang **+ impor SPKL** | Alur karyawan→VP→HR penuh, consent evidence, kuota kebijakan, audit lengkap; **impor file SPKL perusahaan (HC_SPKL + HC_SPKL_PERSON) dengan provenance, deduplikasi NOSPKL, dan perhitungan pipeline produksi** | Durasi SPKL hanya berupa menit (LAMA) — jam mulai/selesai diturunkan (17:00 + LAMA) |
| 7 | Anomali presensi | Turunan (scan manual) | ✅ Matang | Scan teraudit, alur resolve + catatan | Scan masih dipicu manual, bukan terjadwal |
| 8 | Dashboard Direktur | Turunan | ✅ Matang **+ Fase 3** | Read-only, export, **kartu Kualitas Data**, **badge sumber per baris** (Sistem/Impor/Fingerprint/Face ID) | — |
| 9 | Realtime "hari ini" | Turunan | ✅ Cukup | Snapshot + jam berjalan | **Realtime sejati** butuh integrasi mesin (Fase 4, terblokir akses) |
| 10 | Error import per baris | Eksternal | ✅ Matang | Halaman khusus + alur resolve teraudit | Koreksi baris `absensi_harian` belum punya form koreksi (komentar di migrasi) |
| 11 | Audit trail | — | ✅ Matang | Cakupan luas, before→after, aktor historis tanpa FK | — |
| 12 | RBAC & keamanan | — | ✅ Matang | Role karyawan/VP/HR/direktur/admin, halaman 403 kustom, P2 regression test | — |

## 2. Gap utama (diurutkan prioritas)

### P1 — Status ketidakhadiran tanpa dokumen (Fase 2)
Cuti/sakit/izin/perdin/alpha diisi sebagai angka bulanan (form HR / impor),
tanpa pengajuan karyawan dan persetujuan di sistem. Angkanya *konsisten*
(invariant terjaga) tetapi **tidak dapat ditelusuri alasannya**.
→ **Terblokir**: alur pengajuan belum dirancang (menunggu keputusan alur approval
kantor: siapa atasan, berapa level, format surat).

### P2 — Excel multi-format dari perusahaan
Sudah ditangani teknis: deteksi header (baris judul didukung), dua format
(`paired`/`scan`), normalisasi NIP `90010001.0`, error per baris, dan
**deteksi file terulang via sha256**. Yang belum: **satu template resmi**
sehingga beda file cukup diterima apa adanya — sekarang beda format masih
berisiko ditolak/diseuaikan diam-diam oleh HR.
→ Rekomendasi: tetapkan 1 template (format paired) + contoh file di
`resources/` dan sosialisasikan ke pengirim data.

### P3 — Tidak ada jadwal pengaman (scheduler)
Sinkronisasi rekap berjalan event-driven (clock in/out, pasca-impor) plus
command manual `attendance:recap`. Bila ada jalur tulis yang terlewat,
rekap bisa diam-diam usang sampai disentuh manual.
→ Rekomendasi kecil: `Schedule::command('attendance:recap')->dailyAt('01:00')`
sebagai jaring pengaman (tidak mengubah hasil, hanya menjamin kesegaran).

### P4 — Realtime sejati (Fase 4)
Saat ini "realtime" = snapshot harian dari `attendance_logs` + jam berjalan.
Data dari mesin fingerprint/Face ID masuk lewat impor (harian) sehingga
tab realtime sebenarnya "harian terjadwal".
→ **Terblokir**: butuh akses/API mesin absensi perusahaan.

## 3. Roadmap

| Fase | Isi | Status |
|------|-----|--------|
| **1 — Kepastian asal data** | Tabel `import_batches`; nama file + sha256 + operator + hasil commit per impor; flag **file terulang** + peringatan; UI **Riwayat Impor** (HR); kartu **Impor Terakhir** (direktur); 2 test provenance | ✅ **Selesai 14 Okt 2026** |
| **2 — Alur pengajuan cuti/sakit/izin/perdin** | Karyawan ajukan → atasan/HR setujui → status rekap terisi otomatis dari dokumen | ⏸ Terblokir — menunggu pemetaan alur persetujuan kantor |
| **3 — Monitoring integritas data** | Kartu **Kualitas Data** di dashboard direktur: impor terakhir, komposisi sumber (langsung vs impor), baris impor bermasalah, anomali belum ditinjau; **badge sumber** per baris di tab realtime & riwayat; 1 test dashboard | ✅ **Selesai 14 Okt 2026** |
| **4 — Realtime sejati** | Integrasi mesin/API absensi atau clock-in terjadwal otomatis | ⏸ Terblokir — menunggu akses mesin |

## 4. Rekomendasi lanjutan (opsional, urut nilai)

1. **Template resmi 1 format** + contoh file (menutup P2, effort kecil).
2. **Scheduler `attendance:recap` harian** (menutup P3, effort sangat kecil).
3. **Form koreksi baris `absensi_harian`** di halaman Error Import agar baris
   gagal bisa diperbaiki tanpa impor ulang seluruh file.
4. **Badge sumber pada export Excel** rekap agar bukti cetak juga membawa asal data.
5. Fase 2 & 4 — menunggu prasyarat masing-masing.

## 5. Bukti kualitas

- **170/170 test Pest lulus, 663 asersi** — termasuk 5 test baru
  impor lembur SPKL (happy path + provenance, baris NIP tidak
  dikenal, deduplikasi impor ulang, penolakan non-eligible & kebijakan
  belum berlaku, RBAC 403) dan 3 test provenance Fase 1.
- `php artisan optimize:clear` / `optimize` / `route:list` normal.
- Smoke test live (server `php artisan serve` :8000): dashboard
  direktur, halaman verifikasi & impor HR (termasuk **Impor Lembur**
  baru), import absensi & karyawan, error import, timesheet karyawan,
  dan halaman VP — semuanya 200.

## 6. Integrasi file perusahaan (data UAT, 14 Okt 2026)

Tiga file Excel perusahaan dimuat ke data UAT dengan keputusan
kepemilikan data:

| File | Jalur muat | Keputusan |
|------|-----------|-----------|
| `Dummy_Kehadiran_Karyawan_Agustus_2026.xlsx` (973 baris) | Impor absensi produksi (provenance `import_batches`) | **File log menang** untuk Agustus: hadir/telat dihitung dari log asli (924 hari-hadir; 49 baris tgl 25 Agu adalah hari libur, tidak dihitung hadir) |
| `Rekap_Kehadiran_2026.xlsx` (50 karyawan × Jul+Agu) | Counter HR (perdin/cuti/sakit/ijin) | **Juli**: log direkonstruksi agar hadir & telat persis sesuai rekap (50/50 rekap identik dengan file). **Agustus**: counter dimuat sejauh muat ruang (hadir dari log asli) — 20 dari 117 hari counter muat, sisanya dibuang dengan catatan karena kedua file saling bertentangan |
| `Dummy_Pengajuan_Lembur_Agustus_2026.xlsx` (24 SPKL, 98 person-row) | Impor lembur SPKL (fitur baru) | 98/98 pengajuan masuk sebagai `VERIFIED_HR`, 105,5 jam dikenali, seluruhnya terhubung ke log absensi asli Agustus |

Persiapan pendukung: aturan lembur perusahaan berlaku sejak
2026-01-01 (agar Agustus tercakup), akun UAT untuk seluruh 50
karyawan (`karyawan.{nipeg}@test.local`), dan penyelarasan
`band_code` master karyawan dengan `KODEBAND` pada file SPKL.

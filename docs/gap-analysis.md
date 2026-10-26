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
**deteksi file terulang via sha256**. Dokumentasi format yang didukung
tersedia di `docs/import-format.md` (karyawan, absensi harian paired/scan,
SPKL). Yang belum: **satu template resmi** sehingga beda file cukup
diterima apa adanya — sekarang beda format masih berisiko
ditolak/diseuaikan diam-diam oleh HR.
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

## 7. Hardening UI/UX (26 Okt 2026)

Audit konsistensi dilakukan pada seluruh halaman dan seluruh role dashboard.
Prinsip: perubahan hanya kosmetik, tanpa mengubah rute, controller, query,
atau logika bisnis.

### 7.1 Header standar
Pola standar `inti-eyebrow` + `inti-title` + `inti-subtitle` (atau pola
pills pada halaman import) diterapkan pada 18 halaman yang sebelumnya
memakai tipografi ad-hoc. Halaman yang disengaja tetap berbeda
(`/attendance` hero khusus, `profile/edit`, `login`, `register`, `welcome`,
export PDF) tidak diubah.

### 7.2 Badge header
- Varian baru di design system: `inti-badge-primary` (pil navy),
  `inti-badge-lg` (ukuran header), dan normalisasi ikon 15px.
- Semua badge di header diseragamkan ke `inti-badge` + varian
  (primary/info/neutral/danger/success).
- Dua kelas badge yang **tidak pernah punya CSS** (`vp-status`,
  `hr-status-badge`) — termasuk badge yang dirender oleh JavaScript di
  halaman VP/HR — diganti ke `inti-badge`, sehingga tidak ada lagi kelas
  badge tanpa gaya.
- Cache-busting `?v=filemtime(...)` ditambahkan pada link CSS agar browser
  memuat versi baru tanpa hard refresh.

### 7.3 CSS overtime yang hilang (regresi commit 36ff3b5)
Commit `36ff3b5` menghapus 540 baris blok `<style>` inline dari 4 halaman
overtime (`vp-approval`, `vp-history`, `hr-verification`, `history`)
**tanpa mengekstraknya**, sehingga 53 kelas kustom (mobile note, status
pil, tabel, kartu, state kosong) tidak terdefinisi → catatan mobile tampil
di desktop, status pil tanpa bentuk.
- Diperbaiki dengan mengekstrak blok `<style>` dari commit awal ke
  `public/css/p1-overtime.css` yang dimuat via `@stack('styles')` hanya pada
  4 halaman tersebut.
- Verifikasi computed-style di browser: mobile note `display:none` di
  desktop, card border 1px, table wrap `overflow-x:auto`, status badge
  radius 999px.

### 7.4 Hasil
- Kelas kustom tidak terdefinisi: **0**.
- Semua halaman utama: **200** untuk role yang berhak.
- Pest: **170 passed, 663 assertions**.

1. **Template resmi 1 format** + contoh file (menutup P2, effort kecil).
2. **Scheduler `attendance:recap` harian** (menutup P3, effort sangat kecil).
3. **Form koreksi baris `absensi_harian`** di halaman Error Import agar baris
   gagal bisa diperbaiki tanpa impor ulang seluruh file.
4. **Badge sumber pada export Excel** rekap agar bukti cetak juga membawa asal data.
5. Fase 2 & 4 — menunggu prasyarat masing-masing.

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

## 8. Audit backend (26 Okt 2026)

Audit dilakukan pada autorisasi, validasi input, query, dan index.

### 8.1 Autorisasi per role
- Semua rute dikelompokkan dengan middleware `auth`, `active`, dan
  `role:...` yang sesuai (karyawan/vp/hr_admin/direktur_utama/admin_sistem).
- Controller kritis melakukan pengecekan tambahan:
  - `vpApprove`: karyawan harus VP, memiliki divisi, dan submission
    berada di divisinya (lockForUpdate + abort_unless division match).
  - `hrVerify`: hanya HR admin, status harus `APPROVED_VP`.
  - `MyAttendanceController::dismissOvertimePrompt`: `abort_unless isKaryawan`.
- Tidak ditemukan endpoint yang bisa diakses lintas role tanpa guard.

### 8.2 Validasi input
- Semua controller yang menerima input memanggil `$request->validate(...)`
  atau FormRequest (`AttendanceRecapController` pakai `$request->validated()`).
- Import service memvalidasi NIP, tanggal, jam, duplikat, dan NIP
  tidak ditemukan per baris.

### 8.3 Query & performa
- Tidak ditemukan N+1 pada jalur panas: `TimesheetController` memuat
  `$attendanceLogs` sekali lalu `->get()` di loop; `DirekturDashboardController`
  memakai clone aggregate.
- Index tersedia: `employees.nipeg` (unique), `attendance_logs (employee_id,
  tanggal)` (unique), `overtime_submissions (overtime_date,status)`,
  `(status, division_id)`, `holiday_calendars (tanggal, is_active)`,
  `attendance_anomalies (employee_id,tanggal)`, `(type,status)`.

### 8.4 Transaksi & integritas
- `OvertimeController::vpApprove` / `hrVerify` dan
  `ProductionAttendanceImportService::commit` dibungkus `DB::transaction`
  dengan `lockForUpdate()` pada baris yang diproses.
- Provenance impor (`import_batches`: nama file, sha256, operator, hasil)
  dan deteksi file terulang aktif.

### 8.5 Preflight
- `php artisan production:preflight`: **semua PASS** termasuk
  `HOLIDAY_DATA` (kalender libur nasional 2026 tersedia).

### 8.6 Rekomendasi minor (opsional)
- Tambah `Schedule::command('attendance:recap')->dailyAt('01:00')` sebagai
  jaring pengaman (P3 di gap analysis).
- Form koreksi baris `absensi_harian` di halaman Error Import.
- Badge sumber pada export Excel rekap.
- Cuti bersama perusahaan: sediakan importer holiday (data belum masuk).

### 8.7 Hasil
- Pest: **170 passed, 663 assertions**.
- Tidak ada perubahan kode yang merusak test; audit hanya menambah
  dokumentasi dan hardening UI.

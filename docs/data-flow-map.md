# Peta Alur Data — INTI Presence

> Dokumen rujukan: dari mana setiap angka berasal, melewati proses apa, disimpan di mana,
> dan siapa yang memakainya. Basis: kode `sistem-absensi-inti` per 14 Okt 2026.

---

## 1. Ringkasan alur (bird's-eye view)

```
┌─────────────────────────────────────────────────────────────────────────┐
│ SUMBER                                                                   │
│  A. Clock in/out aplikasi (karyawan)                                     │
│  B. Impor file Excel/CSV (HR)                                            │
│  C. Data legacy perangkat (face_id / mesin_fingerprint)                  │
│  D. Input manual HR (form rekap, cuti/sakit/izin/perdin/alpha)           │
└──────────────┬──────────────────────────────────────────────────────────┘
               ▼
┌──────────────────────────┐   ┌───────────────────────────────────────┐
│ TRANSAKSI / VALIDASI     │   │ PROVENANCE & AUDIT                    │
│  • session preview→commit│   │  • import_batches (nama file, sha256, │
│  • validasi per baris    │   │    operator, berhasil/gagal, duplikat)│
│  • lockForUpdate upsert  │   │  • audit_logs (before→after + aktor)  │
└──────────────┬───────────┘   └───────────────────────────────────────┘
               ▼
┌──────────────────────────┐
│ PENYIMPANAN (fakta)      │   attendance_logs  → sumber kebenaran harian
│  + attendance_import_    │   employees         → master karyawan
│    errors (baris bermas.)│   overtime_submissions → alur lembur
└──────────────┬───────────┘
               ▼  (AttendanceRecapSyncService / artisan attendance:recap)
┌──────────────────────────┐
│ TURUNAN (derived)        │   attendance_recap  — selalu dihitung ulang,
│                          │   tidak pernah diedit manual selain form HR
└──────────────┬───────────┘
               ▼
┌─────────────────────────────────────────────────────────────────────────┐
│ KONSUMEN                                                                 │
│  Dashboard Direktur (read-only + Kualitas Data) • Rekap HR • Rekap Tim   │
│  (VP) • Timesheet • Export Excel/PDF • Halaman Error Import              │
└─────────────────────────────────────────────────────────────────────────┘
```

**Aturan kebenaran (single source of truth):**

1. `attendance_logs` adalah fakta harian. Rekap/dash tidak pernah menulis ke sana.
2. `attendance_recap` hanya turunan — dihitung dari fakta (+ input resmi HR), selalu bisa disinkronkan ulang.
3. Setiap baris yang masuk lewat impor wajib meninggalkan jejak: `import_batches` + `audit_logs`.

---

## 2. Alur per jalur data

### A. Clock in/out aplikasi (transaksional)

| Asal | `MyAttendanceController` |
|------|--------------------------|
| Route | `/my-attendance` (karyawan) |
| Tulisan | `AttendanceLog` upsert dengan `lockForUpdate`, `sumber = self_service`, `jumlah_scan` bertambah |
| Audit | `CLOCK_IN` / `CLOCK_OUT` (before→after) |
| Rekap | `AttendanceRecapSyncService::syncPeriod()` langsung setelah clock in/out (bulan berjalan, employee terkait) |

### B. Impor absensi harian (eksternal — jalur paling terkontrol)

| Asal | `ProductionAttendanceImportController` → `ProductionAttendanceImportService` |
|------|------|
| Route | `GET/POST /attendance/import-production` (+ `/preview`, `/commit`) — hanya `hr_admin` |

Tahapan:

1. **Upload** — validasi `xlsx/xls/csv` ≤ 10 MB. Provenance dihitung di sini:
   `nama_file` asli + `file_hash` (sha256 isi file). File disimpan dengan nama UUID
   (nama asli tidak dipakai di disk). Session `attendance_import` =
   `{path, user_id, created_at, file_name, file_hash}` — berlaku **30 menit** dan
   **terikat user** (anti-pembajakan sesi preview).
2. **Preview** — parse (maks 5.000 baris, 100 kolom), deteksi header hingga 10 baris
   awal (file dengan baris judul didukung), deteksi format **`paired`**
   (Jam Masuk/Jam Pulang) atau **`scan`** (satu kolom Jam). Validasi per baris —
   **database belum berubah sama sekali**.
3. **Commit** — satu `DB::transaction`:
   - Format `scan`: baris dikelompokkan per (NIPEG, tanggal) → `jam_masuk` = scan
     pertama, `jam_pulang` = scan terakhir, `jumlah_scan` = jumlah scan.
   - Upsert `attendance_logs` (`sumber = import_production`), `lockForUpdate`.
   - **Penjaga timpa-manipulasi**: baris yang mau menimpa data sumber *lain* dengan
     nilai berbeda DITAHAN → error *"Presensi sudah ada dan berasal dari sumber lain.
     Perubahan ditahan untuk review HR."*
   - Baris sukses → audit `ATTENDANCE_IMPORTED` (nilai sebelum→sesudah + nomor baris Excel).
   - Baris gagal → `attendance_import_errors`
     (`jenis=absensi_harian`, `baris_excel`, `alasan`, `data_mentah`, `status=pending`)
     — dapat ditindaklanjuti lewat halaman **Error Import** (`/attendance-import-errors`).
   - Baris duplikat dalam file (NIPEG+tanggal sama) → ditolak per baris.
4. **Pasca-commit**:
   - `AttendanceRecapSyncService::syncPeriod()` untuk **setiap periode terdampak**
     (rekap tidak lagi stale setelah impor).
   - **Batch provenance dicatat** ke `import_batches` (lihat §3).
   - Audit `ATTENDANCE_IMPORT_COMMITTED`.
   - File sementara dihapus.

### C. Impor master karyawan (eksternal)

| Asal | `EmployeeImportController` → `EmployeeImportService` |
|------|------|
| Route | `/employees/import` (+ `/preview`, `/commit`) — hanya `hr_admin` |

- Preview → klasifikasi baris **CREATE/UPDATE**, commit dalam satu transaksi.
- Validasi: divisi harus ada di master, NIPEG duplikat dalam satu file ditolak.
- Audit: `EMPLOYEE_IMPORTED_CREATED` / `EMPLOYEE_IMPORTED_UPDATED` per baris +
  `EMPLOYEE_IMPORT_COMMITTED` ringkasan.
- Provenance batch juga dicatat (`jenis = master_karyawan`).

### D. Rekap kehadiran (turunan)

| Asal | `AttendanceRecapSyncService`, command `attendance:recap` (`GenerateAttendanceRecap`), form HR (`AttendanceRecapController`) |
|------|------|

- Dihitung dari `attendance_logs` per karyawan/bulan: `hari_kerja`, `hadir`,
  `perdin`, `cuti`, `sakit`, `ijin`, `alpha`, `telat`, `persen_kehadiran`.
- **Invariant** (validator di `AttendanceRecap`):
  `hadir + perdin + cuti + sakit + ijin + alpha === hari_kerja` — rekap yang tidak
  konsisten ditolak sistem.
- Pemicu sinkronisasi:
  1. clock in/out (langsung),
  2. setelah commit impor absensi (per periode terdampak),
  3. manual: `php artisan attendance:recap` — **belum ada jadwal otomatis**
     (`php artisan schedule:list` kosong).
- Perubahan manual oleh HR (form rekap) tercatat:
  `ATTENDANCE_RECAP_CREATED` / `UPDATED` / `DELETED`.

### E. Lembur (transaksional, alur persetujuan terlengkap)

`overtime_submissions` — alur: karyawan `PRE_SUBMITTED`/`POST_SUBMITTED`
(bukti persetujuan/consent evidence wajib) → VP `VP_APPROVED`/`VP_REJECTED`
(maupun `VP_REVERIFIED` / `VP_REJECTED_AFTER_HR_RETURN` saat HR mengembalikan)
→ HR `HR_VERIFIED`/`HR_REJECTED`/`HR_RETURNED_TO_VP`.
Didukung: kebijakan lembur per band (kuota harian/mingguan), kalender hari libur.
Semua transisi teraudit (`OT_SUBMITTED`, `VP_*`, `HR_*`).

### F. Anomali (dipicu manual, teraudit)

`AttendanceAnomalyService::sync()` dijalankan HR lewat tombol scan
(`?scan=1` pada `/attendance-anomalies`) → audit `ATTENDANCE_ANOMALY_SCAN`;
penyelesaian → `ATTENDANCE_ANOMALY_RESOLVED` + catatan penyelesaian.

### G. Konsumen (read-only)

| Halaman | Sumber data |
|---------|-------------|
| Dashboard Direktur `/direktur/dashboard` | rekap + logs + anomali + **Kualitas Data** (impor terakhir, komposisi sumber, error pending, anomali open) + tab realtime & riwayat dengan **badge sumber per baris** |
| Rekap HR `/attendance` | `attendance_recap` + error import terkait |
| Rekap Tim (VP) | rekap divisi yang dikelola |
| Timesheet / My Attendance | `attendance_logs` milik sendiri |
| Export Excel/PDF | turunan `buildReportData()` |

---

## 3. Provenance impor (`import_batches`)

Dicatat pada setiap commit impor (Fase 1, 14 Okt 2026):

| Kolom | Arti |
|-------|------|
| `jenis` | `absensi_harian` / `master_karyawan` |
| `nama_file` | nama file **asli** dari pengunggah |
| `file_hash` | sha256 isi file — dasar deteksi file terulang |
| `format` | `paired` / `scan` (absensi) |
| `total_baris`, `berhasil`, `gagal` | hasil commit |
| `periode` | daftar periode (`YYYY-MM`) yang tersinkron |
| `duplikat` | `true` bila isi file ini pernah diimpor sebelumnya |
| `user_id` | operator (tanpa FK — tetap teringat walau akun dihapus) |

Tampil di UI: **Riwayat Impor** pada kedua halaman import HR, peringatan "File ini
pernah diimpor sebelumnya" pada flash hasil commit, dan kartu **Impor Terakhir**
di dashboard direktur.

## 4. Cakupan audit trail (`audit_logs`)

| Aksi | Action | before→after |
|------|--------|--------------|
| Clock in/out | `CLOCK_IN`, `CLOCK_OUT` | ✓ |
| Impor absensi | `ATTENDANCE_IMPORTED`, `ATTENDANCE_IMPORT_COMMITTED` | ✓ / ringkasan |
| Impor karyawan | `EMPLOYEE_IMPORTED_CREATED/UPDATED`, `EMPLOYEE_IMPORT_COMMITTED` | ✓ / ringkasan |
| CRUD karyawan/divisi | `EMPLOYEE_*`, `DIVISION_*` | ✓ |
| Rekap HR | `ATTENDANCE_RECAP_CREATED/UPDATED/DELETED` | ✓ |
| Lembur | `OT_SUBMITTED`, `VP_*`, `HR_*` | ✓ |
| Anomali | `ATTENDANCE_ANOMALY_SCAN`, `_RESOLVED` | ringkasan / ✓ |
| Error import | `ATTENDANCE_IMPORT_ERROR_RESOLVED` | ✓ |

`audit_logs` sengaja menyimpan `user_id` **tanpa foreign key** agar identitas aktor
historis tidak hilang saat akun dihapus (komentar di migrasi).

## 5. Tabel & peran

| Tabel | Peran |
|-------|-------|
| `attendance_logs` | **Fakta harian** (sumber kebenaran) |
| `attendance_recap` | **Turunan** bulanan (dihitung ulang) |
| `employees`, `divisions` | Master |
| `attendance_import_errors` | Antrean baris bermasalah dari impor |
| `import_batches` | Provenance commit impor |
| `attendance_anomalies` | Anomali hasil scan HR |
| `overtime_submissions` (+ tabel kebijakan) | Alur lembur berpersetujuan |
| `holiday_calendars` | Hari kerja/libur |
| `audit_logs` | Jejak audit semua aksi mutasi |

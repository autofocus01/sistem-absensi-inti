# Sprint 6 Batch — Production Import, Anomaly Center, UAT & Preflight

## Tujuan

Batch ini membawa project ke workflow operasional HR yang lebih dekat ke production:

1. Import absensi production dengan preview sebelum commit.
2. Idempotent import untuk employee + tanggal yang sama.
3. Error import tetap masuk `attendance_import_errors`.
4. Anomaly Center untuk presensi dan lembur.
5. Status anomaly: `OPEN`, `REVIEWED`, `RESOLVED`, `IGNORED`.
6. Audit trail untuk commit import dan penyelesaian anomaly.
7. Production preflight read-only.

## Route HR

- `GET /attendance/import-production`
- `POST /attendance/import-production/preview`
- `POST /attendance/import-production/commit`
- `GET /attendance/anomalies`
- `PATCH /attendance/anomalies/{anomaly}`

## Import

Format yang didukung untuk web import:

- NIP + Tanggal + Jam Masuk + Jam Pulang
- NIP + DateTime scan
- NIP + Tanggal + Jam scan

Preview tidak menulis `attendance_logs`. Commit dilakukan dalam transaction dan menggunakan row lock untuk record yang sudah ada.

## Anomaly yang dideteksi

- `MISSING_CLOCK_IN`
- `INVALID_TIME_ORDER`
- `DUPLICATE_SCAN`
- `SUSPICIOUS_CLOCK_OUT`
- `RECOGNIZED_EXCEEDS_ACTUAL`
- `OT_WITHOUT_ATTENDANCE_LINK`
- `INVALID_VERIFIED_OT`

## Preflight

```bash
php artisan production:preflight --no-db
php artisan production:preflight
```

Command bersifat read-only dan gagal jika check penting tidak terpenuhi.

## Test batch

```bash
./vendor/bin/pest tests/Feature/Import/ProductionAttendanceImportTest.php
./vendor/bin/pest tests/Feature/Anomaly/AttendanceAnomalyTest.php
./vendor/bin/pest tests/Feature/Deployment/ProductionPreflightTest.php
./vendor/bin/pest
```

Migration Sprint 6:

`2026_09_21_120000_create_attendance_anomalies_table.php`

**Jangan menjalankan migration terhadap database operasional sebelum backup dan review migration.**

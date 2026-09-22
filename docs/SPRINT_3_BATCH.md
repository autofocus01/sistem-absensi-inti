# PT INTI — Revolusi Sistem Absensi — Sprint 3 Batch

Batch ini sengaja dikumpulkan agar implementasi tidak dilakukan satu fitur kecil per satu waktu.

## Isi batch

1. **Audit Trail Foundation**
   - `audit_logs` migration
   - `AuditLog` model
   - `AuditLogService`
   - menyimpan actor, action, subject, before/after, IP, user-agent, request-id.

2. **Overtime Policy Conflict Guard**
   - `OvertimePolicyConflictValidator`
   - company event/monthly maximum tidak boleh melampaui legal maximum yang dikonfigurasi.

3. **Policy Health Check**
   - command: `php artisan overtime:policy-health`
   - mendeteksi rule legal/company yang hilang atau konflik untuk WORKDAY, WEEKEND, NATIONAL_HOLIDAY.
   - gunakan `--date=YYYY-MM-DD` untuk audit effective date tertentu.

4. **Automated regression tests**
   - audit logging
   - legal/company overtime limit conflict.

## Integrasi yang sengaja tidak dilakukan otomatis

Audit logging harus dipasang pada titik mutasi bisnis yang sudah final agar tidak mencatat event ganda. Setelah batch ini dipasang, integrasikan `AuditLogService` ke:

- clock-in
- clock-out
- koreksi absensi oleh HR
- submit lembur
- approve VP
- reject VP
- verify HR
- reject HR

Gunakan action yang konsisten, misalnya:
`ATTENDANCE_CLOCK_IN`, `ATTENDANCE_CLOCK_OUT`, `ATTENDANCE_CORRECTED`,
`OVERTIME_SUBMITTED`, `OVERTIME_APPROVED_VP`, `OVERTIME_REJECTED_VP`,
`OVERTIME_VERIFIED_HR`, `OVERTIME_REJECTED_HR`.

## Urutan instalasi

Copy isi folder `app/`, `database/`, dan `tests/` ke project aktif.

Lalu:

```bash
php artisan migrate
php artisan optimize:clear
./vendor/bin/pest tests/Unit/Audit tests/Unit/Overtime/OvertimePolicyConflictValidatorTest.php
php artisan overtime:policy-health
./vendor/bin/pest
```

Jika project aktif sudah memiliki implementasi file yang lebih baru, jangan overwrite membabi buta. Merge hanya file batch yang memang belum ada.

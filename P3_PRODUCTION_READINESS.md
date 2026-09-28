# P3 Production Readiness — Baseline

Tanggal baseline: 25 September 2026

## Perubahan P3.1

1. Batas kumulatif lembur tidak lagi menolak seluruh pengajuan ketika sebagian waktu masih dapat diakui.
2. Engine melakukan **capping recognized minutes** terhadap batas legal/company yang aktif.
3. Selisih waktu yang tidak diakui dicatat sebagai `excess_minutes`.
4. Pelanggaran batas dicatat melalui `anomaly_codes`, sementara `actual_minutes` tetap menyimpan bukti durasi aktual dari presensi.
5. `ProductionPreflight` sekarang memeriksa schema recognition: `rounded_minutes`, `excess_minutes`, dan `anomaly_codes`.

## Contoh perilaku

Jika batas harian yang tersisa adalah 30 menit dan kandidat lembur yang sudah lolos pembulatan adalah 120 menit:

- actual attendance tetap tersimpan;
- recognized = 30 menit;
- excess = 90 menit;
- anomaly = `LEGAL_DAILY_LIMIT_EXCEEDED`;
- pengajuan tidak dihapus hanya karena melewati batas.

## Yang belum dikunci

- Kuota perusahaan harian/mingguan/bulanan belum diberi angka default baru karena nilai kuota bulanan perusahaan belum ditetapkan.
- Data kalender libur produksi harus berasal dari kalender resmi; `HolidayCalendarSeeder` berisi data UAT dummy dan tidak boleh dianggap sebagai kalender produksi.
- Sebelum deployment, jalankan migrasi terbaru dan `production:preflight` terhadap database target.

## Verifikasi lokal

```bash
composer dump-autoload
php artisan optimize:clear
php artisan migrate:status
php artisan production:preflight
php artisan test
```

## Perubahan P3.2 — Konsistensi aturan weekend/hari libur

Baseline company rule menggunakan minimum 30 menit dan pembulatan floor 30 menit untuk WEEKEND dan NATIONAL_HOLIDAY. Test unit yang sebelumnya menggunakan minimum/pembulatan 60 menit diselaraskan dengan policy seeder.

Skenario tambahan:

- Weekend 08:00–08:59 → actual 59 menit, rounded 30 menit, recognized 30 menit, eligible.
- National holiday 08:00–08:59 → actual 59 menit, rounded 30 menit, recognized 30 menit, eligible.
- Weekend 08:00–15:00 dengan maximum event 360 menit → actual 420, recognized 360, excess 60, anomaly `OVERTIME_OVER_WEEKEND_LIMIT`.

## P3.3 — Checklist validasi production

Urutan validasi target production/staging:

1. `php artisan migrate --force`
2. `php artisan production:preflight`
3. Pastikan kalender hari libur berasal dari sumber resmi perusahaan; `HolidayCalendarSeeder` hanya data UAT.
4. Pastikan terdapat policy aktif untuk WORKDAY, WEEKEND, dan NATIONAL_HOLIDAY.
5. Buat satu attendance weekday: Clock-In 07:30, Clock-Out 16:30 → OT 0.
6. Buat weekday Clock-Out 17:17 → actual OT 47, recognized 30 setelah floor 30.
7. Buat weekend Clock-In 08:00, Clock-Out 12:00 → actual/recognized 240 menit, tanpa syarat kerja 8 jam.
8. Submit OT setelah Clock-Out; Clock-Out sendiri tidak membuat submission OT.
9. Verifikasi alur `PENDING_VP → APPROVED_VP → VERIFIED_HR`.
10. Verifikasi VP tidak dapat memproses submission divisi lain.
11. Uji event melewati maximum event; attendance tetap ada, recognized di-cap, excess/anomaly tercatat.
12. Uji quota kumulatif; recognized hanya memakai sisa quota, excess tetap tercatat.
13. Pastikan dashboard/Timesheet memakai `recognized_minutes`, bukan `actual_minutes`, untuk total OT yang diakui.
14. Setelah seluruh smoke test, backup database dan tandai build sebagai release candidate.

## Catatan environment verifikasi

Syntax check P3.2 berhasil. PHPUnit pada environment build ini belum dapat dijalankan karena ekstensi PHP `DOMDocument` tidak tersedia; hasil tersebut bukan indikasi failure pada test aplikasi. PHPUnit harus dijalankan pada environment development/CI yang memiliki ekstensi PHP XML/DOM.

# PT INTI — P3 Final Release Readiness

## Scope

Baseline ini menggabungkan P1 UI, P2 security/audit, dan finalisasi P3 business-rule/production readiness.

## Locked business rules

- Hari kerja: 07:30–16:30, Senin–Jumat.
- Tidak ada grace period keterlambatan.
- Lembur hari kerja dihitung setelah 16:30 sampai Clock-out aktual.
- Lembur weekend/hari libur dihitung dari Clock-in sampai Clock-out aktual.
- Syarat kerja minimum 8 jam hanya untuk hari kerja.
- Weekend/hari libur tidak memakai gate 8 jam.
- Minimum lembur internal: 30 menit.
- Pembulatan default: floor per 30 menit.
- Batas event internal: hari kerja 4 jam; weekend/hari libur 6 jam.
- Overtime tidak dibuat otomatis oleh Clock-out. Pengajuan dilakukan setelah Clock-out.
- Actual attendance dipertahankan walaupun sebagian waktu lembur tidak diakui.
- Actual, rounded, recognized, dan excess dipisahkan.
- Quota daily/weekly/monthly dapat dikonfigurasi pada company overtime rule; nilai nullable berarti belum dibatasi pada level tersebut.
- Quota yang sudah diakui diperlakukan sebagai reservation selama workflow aktif; final USED dipindahkan saat VERIFIED_HR.
- VP hanya dapat memproses submission dari divisinya.
- HR hanya dapat memverifikasi submission yang sudah APPROVED_VP.

## Production validation performed in build environment

- PHP syntax lint: PASS untuk seluruh file PHP pada `app`, `routes`, `config`, `database`, dan `tests`.
- ZIP archive integrity: PASS.
- PHPUnit/Pest runtime: belum dapat dieksekusi pada build environment karena ekstensi PHP DOMDocument tidak tersedia.

## Required final machine validation

```bash
composer dump-autoload
php artisan optimize:clear
php artisan migrate
php artisan production:preflight
php artisan test
```

Jika semua command tersebut PASS pada environment deployment, baseline ini menjadi kandidat final release.

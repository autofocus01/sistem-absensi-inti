# Production Deployment Checklist

Dokumen ini digunakan setelah UAT dinyatakan selesai dan sebelum release production.

## 1. Release

- [ ] Branch/release yang dideploy sama dengan versi yang telah lulus UAT.
- [ ] Commit/tag release dibuat.
- [ ] Backup source dan database tersedia.
- [ ] Tidak ada data dummy/UAT yang akan digunakan sebagai data production.

## 2. Environment

- [ ] `.env` production dibuat di server; jangan commit file `.env`.
- [ ] `APP_ENV=production`.
- [ ] `APP_DEBUG=false`.
- [ ] `APP_KEY` production sudah tersedia dan tidak dibagikan.
- [ ] `APP_URL` menggunakan HTTPS.
- [ ] Database production menggunakan credential khusus, bukan credential development.
- [ ] `SESSION_SECURE_COOKIE=true`.
- [ ] `SESSION_HTTP_ONLY=true`.
- [ ] `SESSION_SAME_SITE=lax` atau nilai yang telah diuji untuk deployment.

## 3. Install/build

```bash
composer install --no-dev --optimize-autoloader
npm ci
npm run build
php artisan storage:link
php artisan optimize
```

## 4. Database

Jangan menjalankan `php artisan migrate:fresh` di production.

Gunakan:

```bash
php artisan migrate --force
```

Seeder production tidak membuat akun `@test.local` maupun 50 dummy employee UAT. `DatabaseSeeder` hanya menjalankan data master/configuration umum ketika `APP_ENV=production`.

## 5. Preflight

Jalankan setelah `.env` dan database siap:

```bash
php artisan production:preflight
```

Semua pemeriksaan harus `PASS` sebelum go-live.

## 6. Web server

- [ ] Nginx/Apache document root menunjuk ke `public/`.
- [ ] PHP-FPM sesuai versi PHP project.
- [ ] `storage/` dan `bootstrap/cache/` writable oleh service account yang diperlukan.
- [ ] HTTPS aktif.
- [ ] Database tidak diekspos langsung ke internet.

## 7. Application services

- [ ] Scheduler/cron aktif jika aplikasi menggunakannya.
- [ ] Queue worker aktif jika aplikasi menggunakan queue.
- [ ] Failed jobs dipantau.
- [ ] Log Laravel, web server, PHP-FPM, dan database dipantau.

## 8. Data production

- [ ] Master employee resmi telah diimport/divalidasi.
- [ ] NIPEG unik.
- [ ] Band dan division tervalidasi.
- [ ] User resmi telah dibuat.
- [ ] Scope VP/VP Hukum sesuai division.
- [ ] HR memiliki akses administrasi yang diperlukan.
- [ ] Direktur hanya mendapatkan akses monitoring/executive.

## 9. Smoke test

- [ ] Login/logout Karyawan.
- [ ] Clock-In/Clock-Out.
- [ ] Pengajuan lembur eligible.
- [ ] Penolakan/non-eligibility Band yang tidak diperbolehkan.
- [ ] Approval VP sesuai division.
- [ ] Verification HR.
- [ ] Dashboard Direktur.
- [ ] Audit log.

## 10. Backup and rollback

- [ ] Backup database sebelum migration.
- [ ] Backup file import/storage yang relevan.
- [ ] Restore backup pernah diuji.
- [ ] Ada prosedur rollback release.
- [ ] Ada PIC teknis selama periode go-live.

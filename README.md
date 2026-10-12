# Sistem Informasi Presensi dan Lembur — Demo/KP

Aplikasi Laravel untuk demonstrasi alur presensi, timesheet, pengajuan lembur, persetujuan VP, verifikasi HR, rekap, dan dashboard. Repository ini ditujukan untuk pembelajaran/Kerja Praktik dan demonstrasi menggunakan data dummy.

> **Penting:** Ini adalah paket demo. Jangan gunakan repository public ini sebagai deployment produksi dan jangan masukkan data karyawan, file impor, database, log, kredensial, atau konfigurasi internal perusahaan. Penggunaan operasional memerlukan review, persetujuan, konfigurasi, dan proses deployment resmi perusahaan.

## Stack

- PHP dan Laravel
- MySQL atau SQLite untuk lingkungan lokal sesuai konfigurasi
- Vite, Tailwind CSS, dan JavaScript untuk aset frontend

## Persiapan lokal

Pastikan versi PHP dan ekstensi yang diminta oleh `composer.json` tersedia.

```bash
composer install
cp .env.example .env
php artisan key:generate
```

Atur `.env` untuk database lokal. Karena file SQLite tidak disertakan, buat file database kosong terlebih dahulu (Linux/macOS: `touch database/database.sqlite`; PowerShell: `New-Item database/database.sqlite -ItemType File`). Lalu jalankan migrasi dan seeder yang memang disediakan untuk demo. Periksa `database/seeders/DatabaseSeeder.php` sebelum menjalankan seeder agar memahami data dummy yang dibuat.

```bash
php artisan migrate --seed
```

Untuk mengaktifkan kontrol waktu/skenario demo secara lokal, ubah `DEMO_MODE=true` pada `.env`. Pastikan `DEMO_MODE=false` untuk lingkungan selain demo. Jangan pernah menggunakan data perusahaan asli dalam demo.

Pasang dan build aset frontend:

```bash
npm ci
npm run build
php artisan serve
```

Buka alamat lokal yang ditampilkan oleh Laravel. Jangan mengunggah `.env` ke GitHub.

## Fitur utama

- Presensi dan timesheet
- Pengajuan lembur dan alur persetujuan VP → verifikasi HR
- Rekap presensi dan laporan
- Pengelolaan data karyawan dan akun sesuai peran
- Dashboard eksekutif dan pemantauan anomali
- Audit trail untuk aktivitas aplikasi
- Mode demo untuk simulasi yang dikonfigurasi secara lokal

## Prinsip keamanan repository

- `.env`, database lokal, log, dan file impor diabaikan oleh `.gitignore`.
- Gunakan hanya data dummy yang tidak dapat dikaitkan dengan karyawan atau operasi perusahaan nyata.
- Jika kredensial pernah masuk ke Git atau ZIP yang dibagikan, anggap kredensial tersebut terekspos: rotasi/ganti nilainya dan bersihkan riwayat Git sebelum publikasi.
- Untuk source operasional perusahaan, gunakan repository private yang dikelola organisasi dan ikuti prosedur deployment internal.

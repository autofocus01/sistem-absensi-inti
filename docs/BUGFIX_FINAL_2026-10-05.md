# Bugfix Final — 5 Oktober 2026

## Root cause yang diperbaiki

1. **User test fixture tidak selalu aktif**
   - `UserFactory` sekarang menetapkan `is_active = true` dan `must_change_password = false` secara eksplisit.
   - Ini mencegah test yang sah terkena middleware `active` dan redirect ke login dengan pesan akun dinonaktifkan.

2. **SQLite tidak mengenal fungsi `YEAR()` / `MONTH()` MySQL**
   - `PeriodOptionsService` sekarang memilih ekspresi tahun sesuai database driver.
   - `SyncAttendanceRecap` juga dibuat portable untuk SQLite, PostgreSQL, dan MySQL/MariaDB.

3. **Kontrak waktu API lembur tidak konsisten**
   - `OvertimeController` menyimpan `start_time` dan `end_time` dalam format canonical `H:i:s`.
   - Tampilan tetap boleh menampilkan `H:i` kepada pengguna.

4. **Blade Pengajuan Lembur memiliki nested `<form>`**
   - Form POST untuk tombol "Tidak, Tetap Hadir" berada di dalam form utama.
   - Struktur diperbaiki menjadi satu form utama + tombol yang membuat form POST secara aman melalui JavaScript.

5. **Profile mengikuti governance akun terbaru**
   - Identitas akun (nama/email) dikelola HR/Administrator Sistem.
   - Pengguna tidak dapat menghapus akun sendiri.
   - Dead/unreachable code pada `ProfileController` dibersihkan.

6. **Test governance akun diselaraskan dengan UI/route terbaru**
   - Pembuatan akun karyawan kembali ke halaman `employee-accounts`, bukan master `employees`.
   - Test Profile lama yang mengharuskan self-edit/self-delete diselaraskan dengan governance terbaru.

## Verifikasi statis

- Seluruh file PHP di `app`, `routes`, `database`, dan `tests` lolos `php -l`.
- Blade `resources/views/overtime/index.blade.php` memiliki pasangan directive `@if/@endif` yang seimbang.

## Setelah ZIP dipasang di mesin lokal

Jalankan:

```bash
php artisan optimize:clear
php artisan migrate:fresh --seed
php artisan test
```

Jika database lokal berisi data penting, **jangan** menjalankan `migrate:fresh --seed`; gunakan:

```bash
php artisan optimize:clear
php artisan migrate
php artisan test
```

Untuk demo, gunakan database/demo data yang memang sudah disiapkan proyek.

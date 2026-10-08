# Sistem Absensi & Lembur PT INTI

Sistem informasi absensi, timesheet, pengajuan lembur, persetujuan VP Divisi, dan verifikasi HR untuk kebutuhan operasional PT INTI.

## Status

**Release:** v1.0 Production  
**Status:** Production Ready / Final Sign-Off  
**Stack:** Laravel / PHP / MySQL

## Fitur Utama

### Absensi & Timesheet
- Pencatatan Clock In dan Clock Out.
- Rekap absensi dan timesheet karyawan.
- Dukungan import data absensi production.
- Validasi master karyawan berdasarkan NIPEG.
- Pencegahan duplikasi data absensi untuk karyawan/tanggal yang sama.
- Pencatatan anomaly dan import error.

### Master Karyawan
- Import master karyawan melalui XLSX/XLS/CSV.
- Preview sebelum commit.
- Validasi NIPEG, nama, dan divisi.
- Deteksi NIPEG duplikat dalam satu file.
- Deteksi CREATE/UPDATE.
- Tidak mengubah `user_id` secara tidak sengaja saat import.
- Audit perubahan data master.

### Pengajuan Lembur
Alur resmi:

```text
Karyawan
   ↓
VP Divisi
   ↓
HR
```

Pengajuan menyimpan bukti persetujuan karyawan, termasuk waktu, IP, user-agent, sumber, versi pernyataan, dan teks persetujuan.

### Persetujuan VP
VP hanya dapat memproses pengajuan dari divisinya sendiri.

VP dapat:
- Setujui pengajuan.
- Tolak pengajuan dengan catatan.
- Melihat riwayat keputusan yang dibuatnya.

### Verifikasi HR
HR memproses pengajuan yang sudah disetujui VP.

HR dapat:
- Verifikasi.
- Tolak.
- Melihat status dan data pendukung pengajuan.

### Riwayat
Tersedia dua perspektif:
- **Karyawan:** Riwayat Pengajuan Lembur miliknya sendiri.
- **VP:** Riwayat keputusan yang dibuat oleh VP tersebut.

## Aturan Lembur

Jam kerja normal:

```text
Senin–Jumat
07:30–16:30
```

Tidak menggunakan sistem shift.

### Lembur Hari Kerja

- Clock Out tepat pukul 16:30 = bukan lembur.
- Lembur dimulai setelah 16:30.
- Minimum lembur hari kerja = 30 menit.
- Pembulatan dilakukan ke bawah per kelipatan 30 menit.

Contoh:

| Clock Out | Lembur Diakui |
|---|---:|
| 16:30 | 0 menit |
| 16:59 | 0 menit |
| 17:00 | 30 menit |
| 17:01 | 30 menit |
| 17:17 | 30 menit |
| 17:29 | 30 menit |
| 17:30 | 60 menit |

### Weekend / Hari Libur Nasional

Pengajuan lembur pada hari istirahat mingguan atau hari libur nasional diproses menggunakan policy khusus dan minimum yang dikonfigurasi.

Batas legal dan batas internal perusahaan dipisahkan dalam policy agar tidak tercampur.

### Validasi Presensi Aktual

Sistem tidak hanya mempercayai jam yang diketik pada formulir.

Setelah Clock Out:
- waktu aktual presensi digunakan untuk rekonsiliasi;
- durasi lembur dihitung ulang;
- `recognized_minutes` ditentukan oleh engine perhitungan;
- pengajuan yang tidak memenuhi syarat ditandai tidak eligible.

Contoh:

```text
Pengajuan:
16:30–19:30

Clock Out aktual:
10:00

Hasil:
INVALID_INTERVAL
recognized_minutes = 0
```

Sistem tidak memperlakukan kondisi tersebut sebagai lembur overnight/shift.

## Batas Lembur

Sistem memisahkan:
- batas legal;
- batas internal perusahaan;
- batas per kejadian;
- akumulasi harian/mingguan/bulanan sesuai policy.

Batas umum legal yang digunakan dalam policy hari kerja mengikuti ketentuan yang telah dijadikan dasar implementasi sistem. Detail angka sebaiknya selalu dikonfirmasi kembali terhadap regulasi dan kebijakan perusahaan yang berlaku saat deployment.

## Role & Hak Akses

### Karyawan
- Dashboard.
- Absensi & Timesheet.
- Pengajuan lembur.
- Riwayat pengajuan lembur.
- Profil.

### VP
- Dashboard.
- Absensi sendiri.
- Persetujuan lembur anggota divisinya.
- Setujui/tolak.
- Riwayat keputusan VP.
- Rekap divisi.
- Profil.

### HR
- Dashboard HR.
- Master karyawan.
- Import karyawan.
- Import absensi production.
- Pengelolaan divisi.
- Verifikasi lembur.
- Audit/log terkait operasional.

### Batas Divisi

VP tidak boleh memproses pengajuan dari divisi lain.

Pemeriksaan division boundary dilakukan di server, bukan hanya di tampilan frontend.

## State Machine Lembur

Status utama:

```text
PENDING_VP
    ├── APPROVED_VP
    │       ├── VERIFIED_HR
    │       └── REJECTED_HR
    │
    └── REJECTED_VP
```

Status pengajuan disimpan pada database dan perubahan keputusan dicatat bersama identitas approver/verifier dan timestamp.

## Keamanan & Audit

Sistem menerapkan beberapa pengamanan:
- Authorization berdasarkan role.
- Validasi division boundary VP.
- Validasi state transition di server.
- Transaksi database untuk keputusan VP/HR.
- `lockForUpdate` pada proses keputusan yang membutuhkan konsistensi.
- Pengajuan aktif ganda pada tanggal yang sama dicegah.
- Consent karyawan direkam sebagai evidence.
- Audit log untuk aktivitas penting.
- Data official reporting hanya menggunakan pengajuan yang memenuhi status dan eligibility yang ditentukan sistem.

## Import Data

### Import Master Karyawan

Format utama:

```text
NIPEG
Nama
Jenis Kelamin
Jabatan
No HP
Alamat
Divisi
```

Alur:

```text
Upload
  ↓
Preview
  ↓
Validasi
  ↓
CREATE / UPDATE
  ↓
Commit
```

### Import Absensi Production

Sistem mendukung format import yang disediakan modul production attendance import.

Data yang tidak memiliki NIPEG pada master karyawan akan ditolak dan tidak membuat AttendanceLog.

## Instalasi

Clone/copy source project ke server:

```bash
git clone <repository-url> sistem-absensi-inti
cd sistem-absensi-inti
```

Install dependency:

```bash
composer install --no-dev --optimize-autoloader
```

Siapkan environment:

```bash
cp .env.example .env
php artisan key:generate
```

Atur koneksi database pada `.env`, kemudian:

```bash
php artisan migrate --force
```

Jika deployment menggunakan asset frontend:

```bash
npm install
npm run build
```

## Konfigurasi Production

Minimal:

```env
APP_ENV=production
APP_DEBUG=false
```

Pastikan `APP_KEY`, database, URL aplikasi, dan konfigurasi server sudah benar.

Setelah deployment:

```bash
php artisan optimize:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

## Preflight

Sebelum go-live:

```bash
php artisan production:preflight
```

Untuk pengecekan tanpa koneksi database:

```bash
php artisan production:preflight --no-db
```

Mode `--no-db` hanya digunakan untuk pemeriksaan yang memang tidak membutuhkan koneksi database.

## Testing

Jalankan seluruh test:

```bash
php artisan test
```

Target release:

```text
0 failed
```

Sebelum deployment production, test suite harus dijalankan setelah migration dan source final berada pada commit/release yang akan digunakan.

## Database Backup

Sebelum migration/deployment production:

```bash
mysqldump -u <username> -p absensi_inti > absensi_inti_backup_before_release.sql
```

Simpan backup di lokasi terpisah dari server aplikasi.

## Deployment Checklist

- [ ] Backup database.
- [ ] Pastikan source release sudah benar.
- [ ] Pastikan `.env` production tidak ikut repository/package.
- [ ] `APP_ENV=production`.
- [ ] `APP_DEBUG=false`.
- [ ] Dependency production terpasang.
- [ ] Migration status sudah benar.
- [ ] `php artisan production:preflight` PASS.
- [ ] `php artisan test` PASS.
- [ ] Cache production dibuat ulang.
- [ ] Permission `storage` dan `bootstrap/cache` benar.
- [ ] Login Karyawan berhasil.
- [ ] Login VP berhasil.
- [ ] Login HR berhasil.
- [ ] UAT pengajuan → VP → HR berhasil.
- [ ] UAT Setujui/Tolak VP berhasil.
- [ ] Riwayat Karyawan dan Riwayat VP berhasil.
- [ ] Import karyawan berhasil.
- [ ] Import absensi berhasil.
- [ ] Backup database tersedia.

## Rollback

Jika release perlu dikembalikan:

1. Hentikan proses deployment.
2. Simpan log/error yang terjadi.
3. Kembalikan source ke release sebelumnya.
4. Jangan menjalankan `migrate:fresh` pada production.
5. Jika migration release menyebabkan perubahan schema yang tidak kompatibel, gunakan prosedur rollback migration yang telah diuji atau restore database backup sesuai runbook.
6. Bersihkan dan bangun ulang cache:

```bash
php artisan optimize:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

7. Jalankan preflight.
8. Lakukan smoke test login dan transaksi utama.

## Struktur Konseptual

```text
Absensi
   ↓
Timesheet
   ↓
Pengajuan Lembur
   ↓
Validasi Policy + Presensi
   ↓
VP Divisi
   ├── Tolak
   └── Setujui
          ↓
         HR
       ├── Tolak
       └── Verifikasi
              ↓
       Official Overtime Record
```

## Catatan Release

Release v1.0 ditujukan sebagai baseline production setelah:
- seluruh automated test lulus;
- migration production tervalidasi;
- production preflight lulus;
- UAT Karyawan/VP/HR selesai;
- backup database tersedia;
- konfigurasi production diverifikasi.

## Prinsip Operasional

Untuk perubahan berikutnya:

1. Jangan langsung mengubah source production.
2. Buat perubahan di branch/development.
3. Tambahkan atau perbarui automated test.
4. Jalankan seluruh test suite.
5. Jalankan preflight.
6. Lakukan UAT.
7. Backup database.
8. Baru deploy.

**Baseline v1.0 sebaiknya dikunci setelah sign-off production.**

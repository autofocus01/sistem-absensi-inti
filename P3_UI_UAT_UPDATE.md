# P3 UI / UAT Update

Baseline: `sistem-absensi-inti(5).zip`

## Perubahan utama

### 1. Clock Out → keputusan lembur
- Clock Out tetap hanya menyimpan `AttendanceLog`.
- Untuk Karyawan Band 2/3, sistem menyiapkan prompt setelah Clock Out jika prasyarat lembur terpenuhi.
- Hari kerja: prompt hanya jika Clock Out > 16:30 dan durasi kerja aktual >= 8 jam.
- Weekend/hari libur: tidak menggunakan gate 8 jam; durasi aktual mengikuti policy.
- Pilihan **Ya** membuka halaman pengajuan lembur dengan tanggal/jam yang berasal dari presensi.
- Pilihan **Tidak** tidak membuat/mengubah `OvertimeSubmission`.
- Clock Out tepat 16:30 tidak membuka halaman lembur.

### 2. Pengajuan lembur Karyawan
- Jalur Karyawan hanya `POST_SUBMITTED`.
- Tanggal dan jam evidence tidak lagi dipilih bebas oleh pengguna.
- Weekday: lembur dimulai 16:30.
- Weekend/holiday: lembur menggunakan Clock In → Clock Out aktual.
- Minimum 8 jam hanya weekday.

### 3. Timesheet VP
- VP sekarang melihat seluruh karyawan dalam divisinya sendiri.
- Filter nama/NIPEG.
- Filter divisi tetap terkunci pada divisi VP melalui server-side scope.
- Pagination 20 baris default untuk menghindari tabel terlalu panjang.

### 4. Timesheet HR
- Filter nama/NIPEG + divisi tetap tersedia.
- Pagination 20 baris default.
- Summary tetap dihitung dari keseluruhan hasil filter, bukan hanya halaman aktif.

### 5. Dashboard Direktur
- Data keseluruhan karyawan ditambahkan.
- Status hari ini: Belum Presensi / Sedang Bekerja / Selesai.
- Clock In / Clock Out.
- Kehadiran bulanan.
- Hari telat.
- Jumlah anomaly per karyawan.
- Realtime tab.
- History tab.
- Frekuensi anomaly per divisi.
- Filter periode/divisi.
- Jam realtime sampai detik.

## Validasi

- PHP lint changed controllers/routes/request: PASS.
- Blade compilation untuk 4 view yang berubah: PASS.
- PHPUnit/Pest dari build environment tidak dapat dijalankan karena PHP environment tidak menyediakan `DOMDocument`. Ini merupakan keterbatasan environment build, bukan error syntax file.

## Setelah extract

Jalankan di environment project kamu:

```bash
composer dump-autoload
php artisan optimize:clear
php artisan test
```

Tidak perlu menjalankan `migrate:fresh`.

# Format File Excel yang Didukung untuk Import

Dokumen ini merangkum format file Excel/CSV yang dapat diunggah ke sistem
absensi INTI, agar perusahaan **tidak perlu mengubah data asli** — sistem
menyesuaikan dengan format yang sudah ada.

Aturan umum:
- Ekstensi yang diterima: `.xlsx`, `.xls`, `.csv`.
- Baris judul/keterangan sebelum header (maks. 10 baris pertama) otomatis
  dilewati; sistem mencari baris header yang memuat kolom NIP/NIPEG.
- NIP/NIPEG boleh berupa angka desimal Excel (mis. `90010001.0`) —
  otomatis dinormalisasi.
- Header tidak peka huruf besar/kecil dan spasi; `_`, `-`, `/` dianggap spasi.
- NIP yang tidak ditemukan di master karyawan ditolak per baris (tidak
  membatalkan seluruh import).

---

## 1. Import Karyawan (`/employee-accounts` → Import)

Sheet pertama, header wajib memuat **NIPEG** dan **Nama** dan **Divisi**.

| Kolom (salah satu nama yang diterima) | Wajib | Keterangan |
|---|---|---|
| `NIPEG` / `NIP` / `Nomor Induk Pegawai` | Ya | Maks. 20 karakter. |
| `Nama` / `Nama Karyawan` / `Name` | Ya | |
| `Divisi` / `Division` | Ya | Harus cocok dengan master divisi (case-insensitive). |
| `Jenis Kelamin` / `Gender` / `JK` | Tidak | `L` atau `P`. |
| `Jabatan` / `Position` | Tidak | |
| `No HP` / `No. HP` / `Nomor HP` / `Phone` / `Telepon` | Tidak | |
| `Alamat` / `Address` | Tidak | |

NIPEG duplikat di dalam file ditolak. NIP yang sudah ada di master akan
di-update (upsert).

---

## 2. Import Absensi Harian (`/attendance/import-production`)

Format dideteksi otomatis: **paired** (jam masuk & pulang per baris) atau
**scan** (satu scan per baris, digabungkan jadi masuk/pulang).

Header wajib memuat kolom NIP dan tanggal.

### 2.1 Format Paired

| Kolom (salah satu) | Wajib | Keterangan |
|---|---|---|
| `NIPEG` / `NIP` / `PIN` / `NIK` | Ya | |
| `TGL_MASUK` / `Tanggal` / `Date` / `Tgl` / `Tanggal Absensi` | Ya | Format tanggal Excel atau `YYYY-MM-DD`. |
| `JAM_MASUK` / `Jam Masuk` / `Clock In` / `Time In` / `Check In` / `Waktu Masuk` | Ya | |
| `JAM_PULANG` / `Jam Pulang` / `Jam Keluar` / `Clock Out` / `Time Out` / `Check Out` / `Waktu Pulang` | Ya | |

Jam masuk/pulang boleh kosong (mis. baris hari libur) — baris tetap
diimpor dengan jam kosong. Jam pulang tidak boleh lebih awal dari jam
masuk jika keduanya terisi.

### 2.2 Format Scan

| Kolom (salah satu) | Wajib | Keterangan |
|---|---|---|
| `NIPEG` / `NIP` / `PIN` / `NIK` | Ya | |
| `Tanggal` / `Date` / `Tgl` / `TGL_MASUK` / `Tanggal Absensi` / `DateTime` / `Tanggal Waktu` | Ya | |
| `Jam` / `Waktu` / `Time` / `Jam Scan` / `Waktu Scan` / `Scan Time` | Ya (atau kolom datetime) | Satu scan per baris. |

Scan dengan waktu identik di hari yang sama diperlakukan sebagai satu scan.
Masuk = waktu terpagi, pulang = waktu terakhir.

### 2.3 Aturan khusus
- Duplikat NIP + tanggal dalam satu file ditolak.
- Sumber data non-import (`face_id`, dll.) tidak ditimpa jika nilainya
  berbeda (import tidak menimpa data yang sudah ada).
- Setelah commit, rekap kehadiran (`AttendanceRecap`) disinkronkan
  otomatis untuk periode yang terpengaruh.
- File dengan isi identik yang diimpor ulang ditandai sebagai duplikat
  (`duplikat = true`) pada batch kedua.

---

## 3. Import Pengajuan Lembur / SPKL (`/overtime/import`)

File workbook dengan **dua sheet**:

| Sheet | Kolom wajib | Keterangan |
|---|---|---|
| `HC_SPKL` | `NOSPKL`, `KETERANGAN`, `TGLMULAI`, `TGLSELESAI`, `KDKRJ_DIV`, `NIPEG_AJUKAN`, `TGLPENGAJUAN`, `IDJOB_AJUKAN`, `DITERIMAOLEH`, `TGLTERIMASDM`, `XUSER`, `XUPDATE`, `FLAG_APP`, `TGL_APP`, `JOBTITLE_AJUKAN`, `DITERIMAVP`, `TGLTERIMAVP`, `FLAG_VERIFY`, `FLAG_PENGAWAS`, `TGLAPP_PENGAWAS` | Header SPKL. |
| `HC_SPKL_PERSON` | `NOSPKL`, `NIPEG`, `PIN`, `KDKERJ`, `IDJOB`, `PANGKAT`, `LAMA`, `KODEBAND` | Detail karyawan yang mengajukan lembur. |

Aturan bisnis yang diterapkan saat import:
- Hanya karyawan dengan band 2 atau 3 yang eligible mengajukan lembur.
- Pengajuan duplikat (SPKL yang sama sudah ada) dilewati dengan peringatan.
- Akun HR wajib ada; bila tidak, import dibatalkan.

---

## 4. Hari Libur (Holiday Calendar)

Modul holiday calendar tersedia di database (`holiday_calendars`), namun
**data kalender libur nasional belum disuplai perusahaan**. Saat ini:
- Baris absensi pada hari libur tetap diimpor (jam kosong).
- Setelah data libur diterima, aturan import akan disesuaikan (skip/flag
  baris pada hari libur) tanpa mengubah format file.

---

## Catatan untuk perusahaan

- Nama kolom dapat bervariasi sesuai daftar di atas; sistem mencoba
  mencocokkan nama kolom secara fleksibel.
- Perubahan nama kolom di luar daftar yang didukung akan menyebabkan
  baris ditolak dengan pesan alasan yang jelas.
- Jangan ubah urutan atau struktur sheet; baris judul di atas header
  diperbolehkan.

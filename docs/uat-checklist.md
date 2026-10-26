# Checklist UAT — INTI Presence

Tanggal acuan: 29 Okt 2026. Status: ☐ belum, ☑ lulus, ✖ gagal.

Kredensial UAT (password `password`):
- Karyawan: `karyawan.band2@test.local` (dan akun `karyawan.{nipeg}@test.local`)
- VP: `vp@test.local`
- HR: `hr@test.local`
- Direktur: `direktur@test.local`
- Admin Sistem: `admin.sistem@test.local`

## A. Akses & otorisasi

- [ ] Karyawan tidak dapat membuka `/employees`, `/divisions`, `/reports`, `/audit-logs`, `/overtime/vp`, `/overtime/hr-verification` (403/redirect).
- [ ] VP hanya melihat divisi sendiri di `/overtime/vp`, `/rekapitulasi-tim`, `/timesheet`.
- [ ] HR dapat membuka semua modul HR; Direktur hanya `/direktur/dashboard`.
- [ ] Admin Sistem hanya `/admin/accounts` dan `/audit-logs`.

## B. Karyawan

- [ ] `/dashboard` menampilkan 3 KPI + jam kerja hari ini.
- [ ] `/attendance/my` clock in/out berfungsi; status badge (inti-badge) tampil.
- [ ] `/overtime` form pengajuan lembur submit → muncul di VP.
- [ ] `/overtime/history` menampilkan riwayat pengajuan + status.
- [ ] `/timesheet` menampilkan rekap bulanan sendiri.
- [ ] Tampilan konsisten: header `inti-eyebrow/title/subtitle`, badge pil.

## C. VP Divisi

- [ ] `/dashboard` 4 KPI.
- [ ] `/overtime/vp` daftar pending, approve/reject berfungsi, catatan wajib saat reject.
- [ ] `/overtime/vp/history` riwayat keputusan.
- [ ] `/rekapitulasi-tim` rekap divisi sendiri.
- [ ] `/timesheet` rekap divisi.
- [ ] Mobile note (geser tabel) hanya tampil di tablet/ponsel.

## D. HR Admin

- [ ] `/dashboard` 4 KPI.
- [ ] `/employees` CRUD karyawan + toggle akun.
- [ ] `/employee-accounts` kelola akun karyawan.
- [ ] `/divisions` CRUD divisi.
- [ ] `/attendance` rekap kehadiran (create/edit).
- [ ] `/attendance/import-production` preview+commit file perusahaan (paired/scan); baris holiday kosong tidak dihitung hadir.
- [ ] `/employees/import` preview+commit master karyawan.
- [ ] `/overtime/import` preview+commit SPKL (band 2/3 eligible).
- [ ] `/attendance/anomalies` daftar + resolve.
- [ ] `/attendance-import-errors` daftar + resolve.
- [ ] `/overtime/hr-verification` verifikasi/return-to-VP (catatan wajib).
- [ ] `/rekapitulasi-tim` & `/reports` (Excel/PDF export).
- [ ] `/audit-logs` immutable activity record.
- [ ] Badge header: MODUL HR ADMIN, Final Review, Kendali Mutu Data, dll. tampil sebagai pil.

## E. Direktur Utama

- [ ] `/direktur/dashboard` KPI eksekutif + komposisi kehadiran + export Excel/PDF.
- [ ] Tidak ada akses ke modul HR/VP (403).

## F. Admin Sistem

- [ ] `/admin/accounts` CRUD akun struktural.
- [ ] `/audit-logs` read-only.

## G. Konsistensi UI (semua role)

- [ ] Header setiap halaman: eyebrow 11px uppercase, title 28px, subtitle 14px.
- [ ] Badge header berbentuk pil (radius 999px) dengan varian warna konsisten.
- [ ] Mobile note tersembunyi di desktop, tampil di ≤1023px.
- [ ] Dark mode: semua halaman tetap terbaca (badge, card, tabel).
- [ ] Tidak ada 404/500 di semua halaman di atas.

## H. Data & integritas

- [ ] Rekap kehadiran: invariant jumlah hari (hadir + perdin + cuti + sakit + ijin + alpha = hari kerja) per karyawan.
- [ ] Impor file terulang ditandai `duplikat = true` pada batch kedua.
- [ ] `php artisan production:preflight` semua PASS.
- [ ] Pest: 170 passed, 663 assertions.

## I. Deliverable

- [ ] `docs/gap-analysis.md` ter-update (UI + backend audit).
- [ ] `docs/import-format.md` tersedia untuk perusahaan.
- [ ] `docs/uat-checklist.md` tersedia.
- [ ] `sistem-absensi-inti-bersih.zip` di-refresh.

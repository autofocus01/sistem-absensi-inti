<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AttendanceLog extends Model
{
    use HasFactory;

    /**
     * Jam operasional resmi kantor: 07:30 - 16:30, Senin-Jumat, tanpa shift.
     * Ubah di sini kalau kebijakan jam kerja berubah.
     */
    public const JAM_MASUK_NORMAL = '07:30:00';
    public const JAM_PULANG_NORMAL = '16:30:00';

    /**
     * Lembur dibulatkan ke bawah per kelipatan menit ini (aturan umum: 30 menit).
     */
    public const PEMBULATAN_LEMBUR_MENIT = 30;

    protected $fillable = [
        'employee_id', 'tanggal', 'jam_masuk', 'jam_pulang', 'sumber', 'jumlah_scan',
        // 'status_lembur', 'disetujui_oleh', 'disetujui_pada' SENGAJA tidak fillable -
        // cuma boleh diubah lewat method approve()/reject() di bawah, bukan mass-assignment.
    ];

    protected $casts = [
        'tanggal' => 'date',
        'disetujui_pada' => 'datetime',
    ];

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'disetujui_oleh');
    }

    /**
     * Lembur di atas ambang ini (dalam menit) butuh otorisasi khusus Direktur Utama,
     * tidak cukup disetujui HR biasa lewat halaman Rekapitulasi Tim.
     */
    public const OTORISASI_KHUSUS_MENIT = 180; // 3 jam

    public function butuhOtorisasiKhusus(): bool
    {
        return $this->menitLembur() > self::OTORISASI_KHUSUS_MENIT;
    }

    public function approve(User $user): void
    {
        $this->update([
            'status_lembur'  => 'disetujui',
            'disetujui_oleh' => $user->id,
            'disetujui_pada' => now(),
        ]);
    }

    public function reject(User $user): void
    {
        $this->update([
            'status_lembur'  => 'ditolak',
            'disetujui_oleh' => $user->id,
            'disetujui_pada' => now(),
        ]);
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    /**
     * Hari kerja = Senin s.d. Jumat. Sistem ini belum mengenal shift,
     * jadi Sabtu/Minggu selalu dianggap di luar jam operasional normal
     * dan tidak dihitung lembur di versi ini.
     */
    public function isHariKerja(): bool
    {
        return $this->tanggal->isWeekday();
    }

    /**
     * Menit lembur = selisih Jam Pulang aktual terhadap 16:30, dihitung
     * mulai menit ke-1 (tanpa toleransi), lalu dibulatkan KE BAWAH per
     * kelipatan 30 menit. Hanya berlaku di hari kerja & saat sudah ada
     * jam pulang. Lembur sebelum 07:30 (datang lebih awal) tidak dihitung.
     *
     * Contoh: pulang 16:59 -> selisih 29 menit -> dibulatkan ke bawah -> 0 menit.
     *         pulang 17:05 -> selisih 35 menit -> dibulatkan ke bawah -> 30 menit.
     */
    public function menitLembur(): int
    {
        if (! $this->isHariKerja() || ! $this->jam_pulang) {
            return 0;
        }

        $tanggal = $this->tanggal->format('Y-m-d');
        $pulangNormal = Carbon::parse("{$tanggal} " . self::JAM_PULANG_NORMAL);
        $pulangAktual = Carbon::parse("{$tanggal} {$this->jam_pulang}");

        if ($pulangAktual->lessThanOrEqualTo($pulangNormal)) {
            return 0;
        }

        $selisihMenit = $pulangNormal->diffInMinutes($pulangAktual);

        return intdiv($selisihMenit, self::PEMBULATAN_LEMBUR_MENIT) * self::PEMBULATAN_LEMBUR_MENIT;
    }

    public function jamLembur(): float
    {
        return round($this->menitLembur() / 60, 2);
    }

    /**
     * Format tampilan ringkas untuk kolom timesheet, mis. "2j 30m" atau "-" kalau nihil.
     */
    public function lemburFormat(): string
    {
        $menit = $this->menitLembur();

        if ($menit <= 0) {
            return '-';
        }

        $jam = intdiv($menit, 60);
        $sisaMenit = $menit % 60;

        return trim("{$jam}j" . ($sisaMenit > 0 ? " {$sisaMenit}m" : ''));
    }

    /**
     * Menit telat masuk terhadap 07:30 (hanya di hari kerja).
     */
    public function menitTelat(): int
    {
        if (! $this->isHariKerja() || ! $this->jam_masuk) {
            return 0;
        }

        $tanggal = $this->tanggal->format('Y-m-d');
        $masukNormal = Carbon::parse("{$tanggal} " . self::JAM_MASUK_NORMAL);
        $masukAktual = Carbon::parse("{$tanggal} {$this->jam_masuk}");

        return $masukAktual->greaterThan($masukNormal)
            ? $masukNormal->diffInMinutes($masukAktual)
            : 0;
    }

    public function durasiKerjaMenit(): ?int
    {
        if (! $this->jam_masuk || ! $this->jam_pulang) {
            return null;
        }

        $tanggal = $this->tanggal->format('Y-m-d');
        $masuk = Carbon::parse("{$tanggal} {$this->jam_masuk}");
        $pulang = Carbon::parse("{$tanggal} {$this->jam_pulang}");

        return max(0, $masuk->diffInMinutes($pulang));
    }

    public function durasiKerjaFormat(): string
    {
        $menit = $this->durasiKerjaMenit();

        if ($menit === null) {
            return '-';
        }

        return intdiv($menit, 60) . 'j ' . str_pad((string) ($menit % 60), 2, '0', STR_PAD_LEFT) . 'm';
    }

    public function status(): string
    {
        if (! $this->isHariKerja()) {
            return 'Di Luar Hari Kerja';
        }

        if (! $this->jam_masuk) {
            return 'Tidak Ada Presensi';
        }

        if (! $this->jam_pulang) {
            return 'Belum Tap Pulang';
        }

        return $this->menitLembur() > 0 ? 'Hadir + Lembur' : 'Hadir';
    }
}
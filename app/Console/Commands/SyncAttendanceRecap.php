<?php

namespace App\Console\Commands;

use App\Models\AttendanceLog;
use App\Services\Attendance\AttendanceRecapSyncService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class SyncAttendanceRecap extends Command
{
    protected $signature = 'attendance:sync-recap
        {--year= : Tahun target. Jika kosong, semua tahun yang memiliki AttendanceLog diproses.}
        {--month= : Bulan target 1-12. Jika kosong, semua bulan pada tahun target diproses.}';

    protected $description = 'Sinkronkan AttendanceRecap dari AttendanceLog sebagai sumber fakta presensi.';

    public function handle(AttendanceRecapSyncService $sync): int
    {
        $year = $this->option('year') !== null ? (int) $this->option('year') : null;
        $month = $this->option('month') !== null ? (int) $this->option('month') : null;

        if ($year !== null && ($year < 2000 || $year > 2100)) {
            $this->error('--year harus berada pada rentang 2000-2100.');
            return self::FAILURE;
        }

        if ($month !== null && ($month < 1 || $month > 12)) {
            $this->error('--month harus 1-12.');
            return self::FAILURE;
        }

        $driver = DB::connection()->getDriverName();
        $periodExpression = match ($driver) {
            'sqlite' => "CAST(strftime('%Y', tanggal) AS INTEGER) as tahun, CAST(strftime('%m', tanggal) AS INTEGER) as bulan",
            'pgsql' => 'EXTRACT(YEAR FROM tanggal)::integer as tahun, EXTRACT(MONTH FROM tanggal)::integer as bulan',
            default => 'YEAR(tanggal) as tahun, MONTH(tanggal) as bulan',
        };

        $periods = AttendanceLog::query()
            ->selectRaw($periodExpression)
            ->when($year !== null, fn ($q) => $q->whereYear('tanggal', $year))
            ->when($month !== null, fn ($q) => $q->whereMonth('tanggal', $month))
            ->distinct()
            ->orderByDesc('tahun')
            ->orderByDesc('bulan')
            ->get();

        if ($periods->isEmpty()) {
            $this->warn('Tidak ada AttendanceLog untuk periode yang diminta.');
            return self::SUCCESS;
        }

        $total = 0;
        foreach ($periods as $period) {
            $count = $sync->syncPeriod((int) $period->tahun, (int) $period->bulan);
            $total += $count;
            $this->line(sprintf('%04d-%02d: %d rekap disinkronkan', $period->tahun, $period->bulan, $count));
        }

        $this->info("Selesai. Total rekap disinkronkan: {$total}.");
        return self::SUCCESS;
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\AttendanceLog;
use App\Models\Division;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;

class TeamRecapController extends Controller
{
    public function index(Request $request)
    {
        $divisions = Division::orderBy('nama')->get();
        $divisionId = $request->input('division_id');
        $bulan = (int) $request->input('bulan', now()->month);
        $tahun = (int) $request->input('tahun', now()->year);
        $q = $request->input('q');

        $logsQuery = AttendanceLog::with(['employee.division', 'approver'])
            ->whereYear('tanggal', $tahun)
            ->whereMonth('tanggal', $bulan)
            ->when($divisionId, fn ($qr) => $qr->whereHas('employee', fn ($e) => $e->where('division_id', $divisionId)))
            ->when($q, fn ($qr) => $qr->whereHas('employee', function ($e) use ($q) {
                $e->where('nama', 'like', "%{$q}%")->orWhere('nipeg', 'like', "%{$q}%");
            }))
            ->where('jam_masuk', '!=', null);

        $semuaLog = (clone $logsQuery)->get();

        // Cuma baris yang beneran ada lemburnya yang relevan buat direview - dihitung ulang dari
        // method model (bukan kolom tersimpan), biar konsisten dengan yang tampil di Timesheet.
        $logLembur = $semuaLog->filter(fn (AttendanceLog $log) => $log->menitLembur() > 0)
            ->sortByDesc('tanggal')
            ->values();

        $totalPersonel = $semuaLog->pluck('employee_id')->unique()->count();
        $totalMenitLemburPending = $logLembur->where('status_lembur', 'pending')->sum(fn (AttendanceLog $l) => $l->menitLembur());
        $totalMenitLemburDisetujui = $logLembur->where('status_lembur', 'disetujui')->sum(fn (AttendanceLog $l) => $l->menitLembur());
        $totalHariTelat = $semuaLog->filter(fn (AttendanceLog $log) => $log->menitTelat() > 0)->count();

        // Tren Kehadiran Mingguan: bagi tanggal 1..akhir bulan jadi kelompok 7 harian (Minggu 1-4/5),
        // rate = jumlah hari hadir / (jumlah karyawan x hari kerja Senin-Jumat di minggu itu).
        // Hari libur nasional tanggal tetap dikeluarkan dari hitungan hari kerja, biar nggak bikin
        // rate keliatan drop palsu (misal 17 Agustus = Hari Kemerdekaan, kantor tutup).
        $liburNasionalTanggalTetap = ['01-01', '05-01', '06-01', '08-17', '12-25']; // Tahun Baru, Buruh, Pancasila, Kemerdekaan, Natal
        $jumlahKaryawanScope = Employee::when($divisionId, fn ($q) => $q->where('division_id', $divisionId))->count();
        $akhirBulan = \Carbon\Carbon::create($tahun, $bulan, 1)->endOfMonth()->day;
        $trenMingguan = [];
        for ($mulaiHari = 1; $mulaiHari <= $akhirBulan; $mulaiHari += 7) {
            $akhirHari = min($mulaiHari + 6, $akhirBulan);
            $tanggalMulai = \Carbon\Carbon::create($tahun, $bulan, $mulaiHari);
            $tanggalAkhir = \Carbon\Carbon::create($tahun, $bulan, $akhirHari);

            $hariKerjaMinggu = 0;
            for ($d = $tanggalMulai->copy(); $d->lte($tanggalAkhir); $d->addDay()) {
                $isLiburTetap = in_array($d->format('m-d'), $liburNasionalTanggalTetap, true);
                if ($d->dayOfWeekIso <= 5 && ! $isLiburTetap) {
                    $hariKerjaMinggu++;
                }
            }

            $hadirMinggu = $semuaLog->filter(fn (AttendanceLog $log) => $log->tanggal->between($tanggalMulai, $tanggalAkhir))->count();
            $ekspektasi = $jumlahKaryawanScope * $hariKerjaMinggu;
            $rate = $ekspektasi > 0 ? round(($hadirMinggu / $ekspektasi) * 100, 1) : 0;

            $trenMingguan[] = [
                'label' => 'Minggu ' . (count($trenMingguan) + 1),
                'rentang' => $tanggalMulai->format('d') . '-' . $tanggalAkhir->format('d M'),
                'rate' => min($rate, 100),
            ];
        }

        // Koordinat SVG dihitung di sini (bukan @php di Blade) - viewBox 400x140.
        // Skala Y otomatis nyesuain rentang data (bukan fix 0-100%) - kalau semua nilai
        // ngumpul di 90-98%, chart-nya nge-zoom ke rentang itu biar variasinya keliatan.
        $lebarChart = 400;
        $tinggiChart = 140;
        $paddingChart = 20;
        $jumlahTitik = count($trenMingguan);
        $semuaRate = array_column($trenMingguan, 'rate');
        $rateMin = max(0, (min($semuaRate) ?: 0) - 5);
        $rateMax = min(100, (max($semuaRate) ?: 100) + 5);
        if ($rateMax - $rateMin < 5) {
            $rateMin = max(0, $rateMin - 5);
            $rateMax = min(100, $rateMax + 5);
        }
        $rentangRate = ($rateMax - $rateMin) ?: 1;

        $svgPoints = [];
        foreach ($trenMingguan as $i => $minggu) {
            $x = $jumlahTitik > 1
                ? $paddingChart + ($i * (($lebarChart - 2 * $paddingChart) / ($jumlahTitik - 1)))
                : $lebarChart / 2;
            $y = $tinggiChart - $paddingChart - ((($minggu['rate'] - $rateMin) / $rentangRate) * ($tinggiChart - 2 * $paddingChart));
            $svgPoints[] = ['x' => round($x, 1), 'y' => round($y, 1), 'rate' => $minggu['rate'], 'label' => $minggu['label'], 'rentang' => $minggu['rentang']];
        }
        $svgPolyline = implode(' ', array_map(fn ($p) => "{$p['x']},{$p['y']}", $svgPoints));

        $page = (int) $request->input('page', 1);
        $perPage = 20;
        $logLemburPaginated = new LengthAwarePaginator(
            $logLembur->forPage($page, $perPage)->values(),
            $logLembur->count(),
            $perPage,
            $page,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        return view('team-recap.index', [
            'divisions'                 => $divisions,
            'divisionId'                => $divisionId,
            'bulan'                     => $bulan,
            'tahun'                     => $tahun,
            'q'                         => $q,
            'logs'                      => $logLemburPaginated,
            'totalLogLembur'            => $logLembur->count(),
            'totalPersonel'             => $totalPersonel,
            'totalJamLemburPending'     => round($totalMenitLemburPending / 60, 1),
            'totalJamLemburDisetujui'   => round($totalMenitLemburDisetujui / 60, 1),
            'totalHariTelat'            => $totalHariTelat,
            'trenMingguan'              => $trenMingguan,
            'rateMin'                   => round($rateMin, 1),
            'rateMax'                   => round($rateMax, 1),
            'svgPoints'                 => $svgPoints,
            'svgPolyline'               => $svgPolyline,
            'lebarChart'                => $lebarChart,
            'tinggiChart'               => $tinggiChart,
        ]);
    }

    public function approve(Request $request, AttendanceLog $attendanceLog)
    {
        if ($attendanceLog->butuhOtorisasiKhusus()) {
            return back()->withErrors(['status' => 'Lembur di atas 3 jam butuh otorisasi khusus Direktur Utama, tidak bisa disetujui lewat halaman ini.']);
        }

        $attendanceLog->approve(Auth::user());

        return back()->with('status', 'Lembur disetujui.');
    }

    public function reject(Request $request, AttendanceLog $attendanceLog)
    {
        if ($attendanceLog->butuhOtorisasiKhusus()) {
            return back()->withErrors(['status' => 'Lembur di atas 3 jam butuh otorisasi khusus Direktur Utama, tidak bisa ditolak lewat halaman ini.']);
        }

        $attendanceLog->reject(Auth::user());

        return back()->with('status', 'Lembur ditolak.');
    }

    public function approveAllPending(Request $request)
    {
        $bulan = (int) $request->input('bulan', now()->month);
        $tahun = (int) $request->input('tahun', now()->year);
        $divisionId = $request->input('division_id');

        $logs = AttendanceLog::with('employee')
            ->whereYear('tanggal', $tahun)
            ->whereMonth('tanggal', $bulan)
            ->where('status_lembur', 'pending')
            ->when($divisionId, fn ($q) => $q->whereHas('employee', fn ($e) => $e->where('division_id', $divisionId)))
            ->get()
            ->filter(fn (AttendanceLog $log) => $log->menitLembur() > 0 && ! $log->butuhOtorisasiKhusus());

        foreach ($logs as $log) {
            $log->approve(Auth::user());
        }

        return back()->with('status', "{$logs->count()} lembur pending berhasil disetujui sekaligus (lembur >3 jam dilewati, butuh otorisasi Direktur).");
    }
}
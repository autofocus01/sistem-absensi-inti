<?php

namespace App\Http\Controllers;

use App\Models\AttendanceLog;
use App\Models\AttendanceRecap;
use App\Models\Division;
use App\Models\Employee;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class DirekturDashboardController extends Controller
{
    public function index(Request $request)
    {
        $data = $this->buildReportData($request);

        // Digabung di sini (bukan controller/halaman terpisah) supaya Direktur bisa lihat semuanya
        // di satu halaman lewat tab, tanpa pindah URL.
        $data['otorisasiLogs'] = AttendanceLog::with(['employee.division'])
            ->where('status_lembur', 'pending')
            ->whereNotNull('jam_pulang')
            ->get()
            ->filter(fn (AttendanceLog $log) => $log->butuhOtorisasiKhusus())
            ->sortByDesc('tanggal')
            ->values();

        return view('direktur.dashboard', $data);
    }

    private function buildReportData(Request $request): array
    {
        $tahun = (int) $request->input('tahun', now()->year);
        $bulan = (int) $request->input('bulan', now()->month);

        $totalKaryawan = Employee::count();

        $recapBulanIni = AttendanceRecap::where('tahun', $tahun)->where('bulan', $bulan);

        $rataKehadiran = (clone $recapBulanIni)->avg('persen_kehadiran') ?? 0;
        $totalAlpha = (clone $recapBulanIni)->sum('alpha');
        $totalMenitTelat = (clone $recapBulanIni)->sum('menit_telat');
        $totalHadir = (clone $recapBulanIni)->sum('hadir');
        $totalPerdin = (clone $recapBulanIni)->sum('perdin');
        $totalTelatHari = (clone $recapBulanIni)->sum('telat_hari');

        // Disiplin & On-Time Rate: proporsi hari hadir yang TANPA catatan telat.
        $tingkatOnTime = $totalHadir > 0
            ? round((($totalHadir - $totalTelatHari) / $totalHadir) * 100, 1)
            : 0;

        // Lembur bulan ini: dihitung ulang dari log harian (bukan kolom tersimpan), biar konsisten
        // dengan Rekapitulasi Tim & Otorisasi Khusus. Ditampilkan dalam JAM, bukan Rupiah - sistem
        // ini tidak menyimpan data gaji/tarif lembur, jadi estimasi biaya sengaja tidak dibuat-buat.
        $totalMenitLembur = AttendanceLog::whereYear('tanggal', $tahun)
            ->whereMonth('tanggal', $bulan)
            ->whereNotNull('jam_pulang')
            ->get()
            ->sum(fn (AttendanceLog $log) => $log->menitLembur());
        $totalJamLembur = round($totalMenitLembur / 60, 1);

        // Kelengkapan Data Rekap: berapa persen karyawan yang SUDAH punya rekap bulanan untuk periode ini.
        // Ini pengganti "Kepatuhan Operasi" di mockup (yang mengacu ke anak usaha yang tidak kita punya
        // datanya) - versi kita menandakan kelengkapan input HR, bukan kepatuhan entitas anak.
        $karyawanDenganRekap = (clone $recapBulanIni)->distinct('employee_id')->count('employee_id');
        $kelengkapanRekap = $totalKaryawan > 0 ? round(($karyawanDenganRekap / $totalKaryawan) * 100, 1) : 0;

        // Sebaran fasilitas: cuma 2 lokasi yang beneran ada datanya (kolom lokasi_bandung/lokasi_jakarta
        // di rekap bulanan) - bukan 4 lokasi fiktif kayak di mockup awal.
        $totalHariBandung = (clone $recapBulanIni)->sum('lokasi_bandung');
        $totalHariJakarta = (clone $recapBulanIni)->sum('lokasi_jakarta');
        $totalHariLokasi = $totalHariBandung + $totalHariJakarta;
        $persenBandung = $totalHariLokasi > 0 ? round(($totalHariBandung / $totalHariLokasi) * 100, 1) : 0;
        $persenJakarta = $totalHariLokasi > 0 ? round(($totalHariJakarta / $totalHariLokasi) * 100, 1) : 0;

        $matriksDivisi = Division::withCount('employees')
            ->get()
            ->map(function (Division $division) use ($tahun, $bulan) {
                $recaps = AttendanceRecap::whereHas('employee', fn ($q) => $q->where('division_id', $division->id))
                    ->where('tahun', $tahun)
                    ->where('bulan', $bulan)
                    ->get();

                return [
                    'divisi'            => $division->nama,
                    'total_personel'    => $division->employees_count,
                    'rata_kehadiran'    => round($recaps->avg('persen_kehadiran') ?? 0, 1),
                    'total_telat_hari'  => $recaps->sum('telat_hari'),
                    'total_alpha'       => $recaps->sum('alpha'),
                    'perlu_ditinjau'    => ($recaps->avg('persen_kehadiran') ?? 100) < 80,
                ];
            });

        return [
            'tahun'             => $tahun,
            'bulan'             => $bulan,
            'totalKaryawan'     => $totalKaryawan,
            'rataKehadiran'     => round($rataKehadiran, 1),
            'totalAlpha'        => $totalAlpha,
            'totalMenitTelat'   => $totalMenitTelat,
            'totalHadir'        => $totalHadir,
            'totalPerdin'       => $totalPerdin,
            'tingkatOnTime'     => $tingkatOnTime,
            'totalJamLembur'    => $totalJamLembur,
            'kelengkapanRekap'  => $kelengkapanRekap,
            'karyawanDenganRekap' => $karyawanDenganRekap,
            'totalHariBandung'  => $totalHariBandung,
            'totalHariJakarta'  => $totalHariJakarta,
            'persenBandung'     => $persenBandung,
            'persenJakarta'     => $persenJakarta,
            'matriksDivisi'     => $matriksDivisi,
        ];
    }

    public function exportExcel(Request $request)
    {
        $data = $this->buildReportData($request);
        $namaBulan = \Carbon\Carbon::create()->month($data['bulan'])->translatedFormat('F');

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Rekap Absensi');

        $sheet->setCellValue('A1', "Rekap Absensi PT. INTI - {$namaBulan} {$data['tahun']}");
        $sheet->mergeCells('A1:F1');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);

        $sheet->setCellValue('A3', 'Total Tenaga Kerja');
        $sheet->setCellValue('B3', $data['totalKaryawan']);
        $sheet->setCellValue('A4', 'Rata-rata Kehadiran');
        $sheet->setCellValue('B4', $data['rataKehadiran'] . '%');
        $sheet->setCellValue('A5', 'Total Alpha/Mangkir');
        $sheet->setCellValue('B5', $data['totalAlpha']);
        $sheet->setCellValue('A6', 'Total Menit Telat');
        $sheet->setCellValue('B6', $data['totalMenitTelat']);
        $sheet->setCellValue('A7', 'Disiplin & On-Time Rate');
        $sheet->setCellValue('B7', $data['tingkatOnTime'] . '%');
        $sheet->setCellValue('A8', 'Total Jam Lembur Bulan Ini');
        $sheet->setCellValue('B8', $data['totalJamLembur'] . ' Jam');
        $sheet->setCellValue('A9', 'Kelengkapan Data Rekap');
        $sheet->setCellValue('B9', $data['kelengkapanRekap'] . '% (' . $data['karyawanDenganRekap'] . '/' . $data['totalKaryawan'] . ' karyawan)');
        $sheet->setCellValue('A10', 'Sebaran Lokasi - Bandung');
        $sheet->setCellValue('B10', $data['totalHariBandung'] . ' hari (' . $data['persenBandung'] . '%)');
        $sheet->setCellValue('A11', 'Sebaran Lokasi - Jakarta');
        $sheet->setCellValue('B11', $data['totalHariJakarta'] . ' hari (' . $data['persenJakarta'] . '%)');

        $headerRow = 13;
        $headers = ['Divisi', 'Total Personel', 'Rata-rata Kehadiran (%)', 'Total Hari Telat', 'Total Alpha', 'Status'];
        $kolom = ['A', 'B', 'C', 'D', 'E', 'F'];
        foreach ($headers as $i => $header) {
            $sheet->setCellValue($kolom[$i] . $headerRow, $header);
        }
        $sheet->getStyle("A{$headerRow}:F{$headerRow}")->getFont()->setBold(true);

        $row = $headerRow + 1;
        foreach ($data['matriksDivisi'] as $divisi) {
            $sheet->setCellValue('A' . $row, $divisi['divisi']);
            $sheet->setCellValue('B' . $row, $divisi['total_personel']);
            $sheet->setCellValue('C' . $row, $divisi['rata_kehadiran']);
            $sheet->setCellValue('D' . $row, $divisi['total_telat_hari']);
            $sheet->setCellValue('E' . $row, $divisi['total_alpha']);
            $sheet->setCellValue('F' . $row, $divisi['perlu_ditinjau'] ? 'Perlu Ditinjau' : 'Normal');
            $row++;
        }

        foreach (range('A', 'F') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $filename = "rekap-absensi-{$data['tahun']}-{$data['bulan']}.xlsx";
        $tempFile = tempnam(sys_get_temp_dir(), 'absensi_export');

        $writer = new Xlsx($spreadsheet);
        $writer->save($tempFile);

        return response()->download($tempFile, $filename)->deleteFileAfterSend(true);
    }

    public function exportPdf(Request $request)
    {
        $data = $this->buildReportData($request);
        $data['namaBulan'] = \Carbon\Carbon::create()->month($data['bulan'])->translatedFormat('F');

        $pdf = Pdf::loadView('direktur.report-pdf', $data)->setPaper('a4', 'portrait');

        return $pdf->download("rekap-absensi-{$data['tahun']}-{$data['bulan']}.pdf");
    }
}
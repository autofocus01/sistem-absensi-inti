<?php

namespace App\Http\Controllers;

use App\Services\Reporting\ProductionReportService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class ProductionReportController extends Controller
{
    public function __construct(private readonly ProductionReportService $reports) {}

    public function index(Request $request)
    {
        $year = $this->year($request);
        $month = $this->month($request);
        $divisionId = $request->filled('division_id') ? (int) $request->input('division_id') : null;
        $data = $this->reports->period($year, $month, $divisionId);
        $data['divisionId'] = $divisionId;
        $data['monthLabel'] = $data['start']->translatedFormat('F Y');
        $data['formatMinutes'] = fn (int $minutes) => $this->reports->formatMinutes($minutes);
        return view('reports.index', $data);
    }

    public function excel(Request $request)
    {
        $year = $this->year($request); $month = $this->month($request);
        $divisionId = $request->filled('division_id') ? (int) $request->input('division_id') : null;
        $data = $this->reports->period($year, $month, $divisionId);
        $sheet = (new Spreadsheet())->getActiveSheet();
        $sheet->setTitle('Rekap Resmi');
        $sheet->fromArray(['Rekap Resmi HRIS PT INTI', null, null, null, null, null, null], null, 'A1');
        $sheet->fromArray(['Periode', $data['start']->translatedFormat('F Y')], null, 'A2');
        $sheet->fromArray(['Ringkasan', 'Nilai'], null, 'A4');
        $summary = [
            ['Total Karyawan', $data['summary']['employee_count']],
            ['Rata-rata Kehadiran', $data['summary']['attendance_avg'] . '%'],
            ['Hadir', $data['summary']['present_days']],
            ['Hari Telat', $data['summary']['late_days']],
            ['Menit Telat', $data['summary']['late_minutes']],
            ['Alpha', $data['summary']['alpha_days']],
            ['OT Resmi', $this->reports->formatMinutes($data['summary']['official_ot_minutes'])],
            ['OT Pending VP', $this->reports->formatMinutes($data['summary']['pending_vp_minutes'])],
            ['OT Pending HR', $this->reports->formatMinutes($data['summary']['pending_hr_minutes'])],
        ];
        $sheet->fromArray($summary, null, 'A5');
        $row = 16;
        $sheet->fromArray(['NIPEG', 'Nama', 'Divisi', 'Hadir', 'Hari Telat', 'Menit Telat', 'Alpha', 'Kehadiran %', 'OT Resmi'], null, 'A'.$row);
        foreach ($data['rows'] as $item) {
            $row++;
            $sheet->fromArray([$item['nipeg'], $item['nama'], $item['divisi'], $item['hadir'], $item['telat_hari'], $item['menit_telat'], $item['alpha'], $item['persen_kehadiran'], $this->reports->formatMinutes($item['official_ot_minutes'])], null, 'A'.$row);
        }
        foreach (range('A', 'I') as $col) $sheet->getColumnDimension($col)->setAutoSize(true);
        $file = tempnam(sys_get_temp_dir(), 'inti-report-');
        (new Xlsx($sheet->getParent()))->save($file);
        return response()->download($file, "rekap-hris-{$year}-{$month}.xlsx")->deleteFileAfterSend(true);
    }

    public function pdf(Request $request)
    {
        $year = $this->year($request); $month = $this->month($request);
        $divisionId = $request->filled('division_id') ? (int) $request->input('division_id') : null;
        $data = $this->reports->period($year, $month, $divisionId);
        $data['monthLabel'] = $data['start']->translatedFormat('F Y');
        $data['formatMinutes'] = fn (int $minutes) => $this->reports->formatMinutes($minutes);
        return Pdf::loadView('reports.pdf', $data)->setPaper('a4', 'landscape')->download("rekap-hris-{$year}-{$month}.pdf");
    }

    private function year(Request $request): int { $year = (int) $request->input('year', now()->year); return $year >= 2000 && $year <= 2100 ? $year : now()->year; }
    private function month(Request $request): int { $month = (int) $request->input('month', now()->month); return $month >= 1 && $month <= 12 ? $month : now()->month; }
}

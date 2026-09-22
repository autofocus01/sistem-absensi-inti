<?php

namespace App\Http\Controllers;

use App\Services\Attendance\ProductionAttendanceImportService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Throwable;

class ProductionAttendanceImportController extends Controller
{
    public function __construct(private readonly ProductionAttendanceImportService $imports) {}

    public function index(Request $request): View
    {
        abort_unless($request->user()?->isHrAdmin(), 403);
        return view('attendance.import.index');
    }

    public function preview(Request $request): View|RedirectResponse
    {
        abort_unless($request->user()?->isHrAdmin(), 403);
        $validated = $request->validate(['file'=>['required','file','mimes:xlsx,xls,csv','max:10240']]);
        try {
            $path = $this->imports->store($validated['file']);
            $preview = $this->imports->preview($path);
            session(['attendance_import_path'=>$path]);
            return view('attendance.import.preview', compact('preview'));
        } catch (Throwable $e) {
            report($e);
            return back()->withErrors(['file'=>$e->getMessage()]);
        }
    }

    public function commit(Request $request): RedirectResponse
    {
        abort_unless($request->user()?->isHrAdmin(), 403);
        $path = (string) session('attendance_import_path');
        if ($path === '') return back()->withErrors(['file'=>'Sesi preview import tidak ditemukan. Upload ulang file.']);
        try {
            $result = $this->imports->commit($path, $request->user()->id);
            $this->imports->forget($path);
            $request->session()->forget('attendance_import_path');
            return redirect()->route('attendance.import.production')->with('import_result', $result);
        } catch (Throwable $e) {
            report($e);
            return back()->withErrors(['file'=>'Commit import gagal. File tidak dihapus agar dapat diperiksa ulang.']);
        }
    }
}

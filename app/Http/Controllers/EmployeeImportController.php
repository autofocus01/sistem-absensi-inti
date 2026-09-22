<?php

namespace App\Http\Controllers;

use App\Services\EmployeeImportService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Throwable;

class EmployeeImportController extends Controller
{
    public function __construct(private readonly EmployeeImportService $imports) {}

    public function index(Request $request): View
    {
        abort_unless($request->user()?->isHrAdmin(), 403);
        return view('employees.import.index');
    }

    public function preview(Request $request): View|RedirectResponse
    {
        abort_unless($request->user()?->isHrAdmin(), 403);
        $validated = $request->validate(['file' => ['required','file','mimes:xlsx,xls,csv','max:10240']]);
        try {
            $path = $this->imports->store($validated['file']);
            $preview = $this->imports->preview($path);
            session(['employee_import_path' => $path]);
            return view('employees.import.preview', compact('preview'));
        } catch (Throwable $e) {
            report($e);
            return back()->withErrors(['file' => $e->getMessage()]);
        }
    }

    public function commit(Request $request): RedirectResponse
    {
        abort_unless($request->user()?->isHrAdmin(), 403);
        $path = (string) session('employee_import_path');
        if ($path === '') return back()->withErrors(['file' => 'Sesi preview import tidak ditemukan. Upload ulang file.']);
        try {
            $result = $this->imports->commit($path, $request->user()->id);
            $this->imports->forget($path);
            $request->session()->forget('employee_import_path');
            return redirect()->route('employees.import')->with('import_result', $result);
        } catch (Throwable $e) {
            report($e);
            return back()->withErrors(['file' => 'Commit import gagal. File dipertahankan untuk pemeriksaan ulang.']);
        }
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\ImportBatch;
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
        $batches = ImportBatch::query()
            ->where('jenis', ImportBatch::JENIS_KARYAWAN)
            ->with('user')
            ->latest('id')
            ->take(5)
            ->get();

        return view('employees.import.index', compact('batches'));
    }

    public function preview(Request $request): View|RedirectResponse
    {
        abort_unless($request->user()?->isHrAdmin(), 403);
        $validated = $request->validate(['file' => ['required','file','mimes:xlsx,xls,csv','max:10240']]);
        try {
            $file = $validated['file'];
            // Provenance dihitung SEBELUM store() memindahkan file sementara.
            $provenance = [
                'file_name' => $file->getClientOriginalName(),
                'file_hash' => hash_file('sha256', $file->getRealPath()) ?: '',
            ];
            $path = $this->imports->store($file);
            $preview = $this->imports->preview($path);
            session([
                'employee_import' => [
                    'path' => $path,
                    'user_id' => $request->user()->id,
                    'created_at' => now()->timestamp,
                    ...$provenance,
                ],
                'employee_import_path' => $path,
            ]);
            return view('employees.import.preview', compact('preview'));
        } catch (Throwable $e) {
            report($e);
            return back()->withErrors(['file' => $e->getMessage()]);
        }
    }

    public function commit(Request $request): RedirectResponse
    {
        abort_unless($request->user()?->isHrAdmin(), 403);
        $meta = session('employee_import', []);
        $path = (string) ($meta['path'] ?? '');
        if ((int) ($meta['user_id'] ?? 0) !== (int) $request->user()->id || now()->timestamp - (int) ($meta['created_at'] ?? 0) > 1800) {
            $request->session()->forget(['employee_import', 'employee_import_path']);
            return back()->withErrors(['file' => 'Sesi preview import tidak valid atau sudah kedaluwarsa. Upload ulang file.']);
        }
        if ($path === '') return back()->withErrors(['file' => 'Sesi preview import tidak ditemukan. Upload ulang file.']);
        try {
            $result = $this->imports->commit($path, $request->user()->id, [
                'file_name' => (string) ($meta['file_name'] ?? 'file_import'),
                'file_hash' => (string) ($meta['file_hash'] ?? ''),
            ]);
            $this->imports->forget($path);
            $request->session()->forget(['employee_import', 'employee_import_path']);
            return redirect()->route('employees.import')->with('import_result', $result);
        } catch (Throwable $e) {
            report($e);
            return back()->withErrors(['file' => 'Commit import gagal. File dipertahankan untuk pemeriksaan ulang.']);
        }
    }
}

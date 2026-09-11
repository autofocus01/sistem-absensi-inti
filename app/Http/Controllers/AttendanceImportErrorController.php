<?php

namespace App\Http\Controllers;

use App\Models\AttendanceImportError;

class AttendanceImportErrorController extends Controller
{
    public function index()
    {
        $importErrors = AttendanceImportError::orderBy('status')
            ->orderByDesc('created_at')
            ->paginate(20);

        return view('attendance.import-errors', compact('importErrors'));
    }

    public function resolve(AttendanceImportError $importError)
    {
        $importError->update(['status' => 'selesai']);

        return back()->with('status', 'Baris anomali ditandai selesai.');
    }
}
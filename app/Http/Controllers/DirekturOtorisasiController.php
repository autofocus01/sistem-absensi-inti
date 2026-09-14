<?php

namespace App\Http\Controllers;

use App\Models\AttendanceLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DirekturOtorisasiController extends Controller
{
    public function approve(AttendanceLog $attendanceLog)
    {
        $attendanceLog->approve(Auth::user());

        return redirect(route('direktur.dashboard') . '#otorisasi')->with('status', 'Lembur khusus disetujui.');
    }

    public function reject(AttendanceLog $attendanceLog)
    {
        $attendanceLog->reject(Auth::user());

        return redirect(route('direktur.dashboard') . '#otorisasi')->with('status', 'Lembur khusus ditolak.');
    }
}
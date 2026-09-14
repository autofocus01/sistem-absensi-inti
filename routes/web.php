<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\DivisionController;
use App\Http\Controllers\AttendanceRecapController;
use App\Http\Controllers\DirekturDashboardController;
use App\Http\Controllers\DirekturOtorisasiController;
use App\Http\Controllers\AttendanceImportErrorController;
use App\Http\Controllers\TimesheetController;
use App\Http\Controllers\TeamRecapController;

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::get('/timesheet', [TimesheetController::class, 'index'])->name('timesheet.index');
});

Route::middleware(['auth', 'role:direktur_utama'])->group(function () {
    Route::get('/direktur/dashboard', [DirekturDashboardController::class, 'index'])
        ->name('direktur.dashboard');
    Route::get('/direktur/dashboard/export/excel', [DirekturDashboardController::class, 'exportExcel'])
        ->name('direktur.export-excel');
    Route::get('/direktur/dashboard/export/pdf', [DirekturDashboardController::class, 'exportPdf'])
        ->name('direktur.export-pdf');
    Route::post('/direktur/otorisasi-khusus/{attendanceLog}/approve', [DirekturOtorisasiController::class, 'approve'])
        ->name('direktur.otorisasi-khusus.approve');
    Route::post('/direktur/otorisasi-khusus/{attendanceLog}/reject', [DirekturOtorisasiController::class, 'reject'])
        ->name('direktur.otorisasi-khusus.reject');
});
 
Route::middleware(['auth', 'role:hr_admin'])->group(function () {
    Route::resource('employees', EmployeeController::class)->except(['show']);
    Route::resource('attendance', AttendanceRecapController::class)->except(['show']);
    Route::resource('divisions', DivisionController::class)->except(['show']);
 
    Route::get('/attendance-import-errors', [AttendanceImportErrorController::class, 'index'])
        ->name('attendance.import-errors');
    Route::post('/attendance-import-errors/{importError}/resolve', [AttendanceImportErrorController::class, 'resolve'])
        ->name('attendance.import-errors.resolve');

    Route::get('/rekapitulasi-tim', [TeamRecapController::class, 'index'])->name('team-recap.index');
    Route::post('/rekapitulasi-tim/approve-all', [TeamRecapController::class, 'approveAllPending'])->name('team-recap.approve-all');
    Route::post('/rekapitulasi-tim/{attendanceLog}/approve', [TeamRecapController::class, 'approve'])->name('team-recap.approve');
    Route::post('/rekapitulasi-tim/{attendanceLog}/reject', [TeamRecapController::class, 'reject'])->name('team-recap.reject');
});

require __DIR__.'/auth.php';
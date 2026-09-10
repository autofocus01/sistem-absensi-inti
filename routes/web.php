<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\DivisionController;
use App\Http\Controllers\AttendanceRecapController;
use App\Http\Controllers\DirekturDashboardController;

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
    Route::get('/direktur/dashboard', [DirekturDashboardController::class, 'index'])
        ->name('direktur.dashboard');
    Route::resource('employees', EmployeeController::class)->except(['show']);
    Route::resource('attendance', AttendanceRecapController::class)->except(['show']);
});

Route::middleware(['auth', 'role:direktur_utama'])->group(function () {
    Route::get('/direktur/dashboard', [DirekturDashboardController::class, 'index'])
        ->name('direktur.dashboard');
    Route::get('/direktur/dashboard/export/excel', [DirekturDashboardController::class, 'exportExcel'])
        ->name('direktur.export-excel');
    Route::get('/direktur/dashboard/export/pdf', [DirekturDashboardController::class, 'exportPdf'])
        ->name('direktur.export-pdf');
});
 
Route::middleware(['auth', 'role:hr_admin'])->group(function () {
    Route::resource('employees', EmployeeController::class)->except(['show']);
    Route::resource('attendance', AttendanceRecapController::class)->except(['show']);
});

require __DIR__.'/auth.php';

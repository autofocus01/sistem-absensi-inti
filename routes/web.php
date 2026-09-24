<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\EmployeeImportController;
use App\Http\Controllers\DivisionController;
use App\Http\Controllers\AttendanceRecapController;
use App\Http\Controllers\AttendanceImportErrorController;
use App\Http\Controllers\AttendanceAnomalyController;
use App\Http\Controllers\ProductionAttendanceImportController;
use App\Http\Controllers\TimesheetController;
use App\Http\Controllers\TeamRecapController;
use App\Http\Controllers\OvertimeController;
use App\Http\Controllers\ProductionReportController;
use App\Http\Controllers\MyAttendanceController;
use App\Http\Controllers\AuditLogController;
use App\Http\Controllers\DirekturDashboardController;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


/*
|--------------------------------------------------------------------------
| PUBLIC
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return view('welcome');
});


/*
|--------------------------------------------------------------------------
| DASHBOARD
|--------------------------------------------------------------------------
*/

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');


/*
|--------------------------------------------------------------------------
| GENERAL AUTHENTICATED USER
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {

    /*
    |--------------------------------------------------------------------------
    | PROFILE
    |--------------------------------------------------------------------------
    */

    Route::get('/profile', [ProfileController::class, 'edit'])
        ->name('profile.edit');

    Route::patch('/profile', [ProfileController::class, 'update'])
        ->name('profile.update');

    Route::delete('/profile', [ProfileController::class, 'destroy'])
        ->name('profile.destroy');


    /*
    |--------------------------------------------------------------------------
    | TIMESHEET
    |--------------------------------------------------------------------------
    |
    | Karyawan, VP, dan HR dapat mengakses timesheet.
    | Pembatasan data dilakukan di controller berdasarkan user/employee.
    |
    */

    Route::middleware('role:karyawan,vp,hr_admin')->group(function () {

        Route::get('/timesheet', [TimesheetController::class, 'index'])
            ->name('timesheet.index');

    });


    /*
    |--------------------------------------------------------------------------
    | ABSENSI SAYA
    |--------------------------------------------------------------------------
    |
    | Karyawan dan VP hanya mengakses data absensinya sendiri.
    |
    */

    Route::middleware('role:karyawan,vp')
        ->prefix('attendance/my')
        ->group(function () {

            Route::get('/', [MyAttendanceController::class, 'index'])
                ->name('attendance.my');

            Route::post('/clock-in', [MyAttendanceController::class, 'clockIn'])
                ->name('attendance.my.clock-in');

            Route::post('/clock-out', [MyAttendanceController::class, 'clockOut'])
                ->name('attendance.my.clock-out');

        });


    /*
    |--------------------------------------------------------------------------
    | OVERTIME ENTRY POINT
    |--------------------------------------------------------------------------
    |
    | GET /overtime:
    | - Karyawan -> form pengajuan lembur
    | - VP       -> halaman approval VP
    | - HR       -> halaman verifikasi HR
    |
    | POST /overtime/submit:
    | - hanya Karyawan
    |
    */

    Route::middleware('role:karyawan,vp,hr_admin')
        ->get('/overtime', function (Request $request) {

            $role = (string) ($request->user()?->role ?? '');

            if ($role === 'vp') {
                return redirect()->route('overtime.vp');
            }

            if ($role === 'hr_admin') {
                return redirect()->route('overtime.hr.verification');
            }

            return view('overtime.index');

        })
        ->name('overtime.index');


    /*
    |--------------------------------------------------------------------------
    | SUBMIT OVERTIME
    |--------------------------------------------------------------------------
    */

    Route::middleware('role:karyawan')
        ->post('/overtime/submit', [OvertimeController::class, 'store'])
        ->name('overtime.store');


    /*
    |--------------------------------------------------------------------------
    | OVERTIME HISTORY - KARYAWAN
    |--------------------------------------------------------------------------
    */

    Route::middleware('role:karyawan')
        ->get('/overtime/history', [OvertimeController::class, 'employeeHistory'])
        ->name('overtime.history');

});


/*
|--------------------------------------------------------------------------
| VP DIVISI
|--------------------------------------------------------------------------
|
| VP hanya mengelola approval lembur dari divisinya sendiri.
|
*/

Route::middleware(['auth', 'role:vp'])
    ->prefix('overtime/vp')
    ->group(function () {

        /*
        |--------------------------------------------------------------------------
        | Dashboard Approval VP
        |--------------------------------------------------------------------------
        */

        Route::get('/', function () {
            return view('overtime.vp-approval');
        })->name('overtime.vp');


        /*
        |--------------------------------------------------------------------------
        | Pending Overtime
        |--------------------------------------------------------------------------
        */

        Route::get('/pending-data', [OvertimeController::class, 'vpPendingList'])
            ->name('overtime.vp.pending');


        /*
        |--------------------------------------------------------------------------
        | Approve / Reject Overtime
        |--------------------------------------------------------------------------
        */

        Route::post('/approve/{id}', [OvertimeController::class, 'vpApprove'])
            ->name('overtime.vp.approve');


        /*
        |--------------------------------------------------------------------------
        | History Approval VP
        |--------------------------------------------------------------------------
        */

        Route::get('/history', [OvertimeController::class, 'vpHistory'])
            ->name('overtime.vp.history');

    });


/*
|--------------------------------------------------------------------------
| REKAPITULASI TIM / DIVISI
|--------------------------------------------------------------------------
|
| VP  -> hanya divisinya sendiri.
| HR  -> dapat mengakses rekap sesuai kewenangannya.
|
*/

Route::middleware(['auth', 'role:hr_admin,vp'])
    ->group(function () {

        Route::get('/rekapitulasi-tim', [TeamRecapController::class, 'index'])
            ->name('team-recap.index');

        /*
        | Compatibility route untuk sidebar/dashboard.
        */

        Route::get('/reports/team', [TeamRecapController::class, 'index'])
            ->name('reports.team');

    });


/*
|--------------------------------------------------------------------------
| HR ADMIN
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'role:hr_admin'])
    ->group(function () {

        /*
        |--------------------------------------------------------------------------
        | EMPLOYEES
        |--------------------------------------------------------------------------
        */

        Route::resource('employees', EmployeeController::class)
            ->except(['show']);


        /*
        |--------------------------------------------------------------------------
        | EMPLOYEE IMPORT
        |--------------------------------------------------------------------------
        */

        Route::get('/employees/import', [EmployeeImportController::class, 'index'])
            ->name('employees.import');

        Route::post('/employees/import/preview', [EmployeeImportController::class, 'preview'])
            ->name('employees.import.preview');

        Route::post('/employees/import/commit', [EmployeeImportController::class, 'commit'])
            ->name('employees.import.commit');


        /*
        |--------------------------------------------------------------------------
        | ATTENDANCE ADMIN
        |--------------------------------------------------------------------------
        */

        Route::resource('attendance', AttendanceRecapController::class)
            ->except(['show']);


        /*
        |--------------------------------------------------------------------------
        | DIVISIONS
        |--------------------------------------------------------------------------
        */

        Route::resource('divisions', DivisionController::class)
            ->except(['show']);


        /*
        |--------------------------------------------------------------------------
        | ATTENDANCE IMPORT ERRORS
        |--------------------------------------------------------------------------
        */

        Route::get('/attendance-import-errors', [
            AttendanceImportErrorController::class,
            'index',
        ])->name('attendance.import-errors');

        Route::post('/attendance-import-errors/{importError}/resolve', [
            AttendanceImportErrorController::class,
            'resolve',
        ])->name('attendance.import-errors.resolve');


        /*
        |--------------------------------------------------------------------------
        | PRODUCTION ATTENDANCE IMPORT
        |--------------------------------------------------------------------------
        |
        | Import dari sistem Face Recognition resmi.
        |
        */

        Route::get('/attendance/import-production', [
            ProductionAttendanceImportController::class,
            'index',
        ])->name('attendance.import.production');

        Route::post('/attendance/import-production/preview', [
            ProductionAttendanceImportController::class,
            'preview',
        ])->name('attendance.import.production.preview');

        Route::post('/attendance/import-production/commit', [
            ProductionAttendanceImportController::class,
            'commit',
        ])->name('attendance.import.production.commit');


        /*
        |--------------------------------------------------------------------------
        | ATTENDANCE ANOMALIES
        |--------------------------------------------------------------------------
        */

        Route::get('/attendance/anomalies', [
            AttendanceAnomalyController::class,
            'index',
        ])->name('attendance.anomalies');

        Route::patch('/attendance/anomalies/{anomaly}', [
            AttendanceAnomalyController::class,
            'resolve',
        ])->name('attendance.anomalies.resolve');


        /*
        |--------------------------------------------------------------------------
        | HR OVERTIME VERIFICATION
        |--------------------------------------------------------------------------
        */

        Route::get('/overtime/hr-verification', function () {
            return view('overtime.hr-verification');
        })->name('overtime.hr.verification');


        Route::get('/overtime/hr/pending-data', [
            OvertimeController::class,
            'hrPendingList',
        ])->name('overtime.hr.pending');


        Route::post('/overtime/hr/verify/{id}', [
            OvertimeController::class,
            'hrVerify',
        ])->name('overtime.hr.verify');


        /*
        |--------------------------------------------------------------------------
        | HR REPORTS
        |--------------------------------------------------------------------------
        */

        Route::get('/reports', [
            ProductionReportController::class,
            'index',
        ])->name('reports.index');

        Route::get('/reports/excel', [
            ProductionReportController::class,
            'excel',
        ])->name('reports.excel');

        Route::get('/reports/pdf', [
            ProductionReportController::class,
            'pdf',
        ])->name('reports.pdf');


        /*
        |--------------------------------------------------------------------------
        | AUDIT LOG
        |--------------------------------------------------------------------------
        */

        Route::get('/audit-logs', [
            AuditLogController::class,
            'index',
        ])->name('audit-logs.index');

    });


/*
|--------------------------------------------------------------------------
| DIREKTUR UTAMA
|--------------------------------------------------------------------------
|
| Executive monitoring.
| Tidak memberikan akses otomatis ke modul HR atau VP.
|
*/

Route::middleware(['auth', 'role:direktur_utama'])
    ->group(function () {

        Route::get('/direktur/dashboard', [
            DirekturDashboardController::class,
            'index',
        ])->name('direktur.dashboard');


        Route::get('/direktur/dashboard/export/excel', [
            DirekturDashboardController::class,
            'exportExcel',
        ])->name('direktur.export-excel');


        Route::get('/direktur/dashboard/export/pdf', [
            DirekturDashboardController::class,
            'exportPdf',
        ])->name('direktur.export-pdf');

    });


/*
|--------------------------------------------------------------------------
| AUTH ROUTES
|--------------------------------------------------------------------------
*/

require __DIR__.'/auth.php';
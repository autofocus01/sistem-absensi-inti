// Replace the GET /overtime closure with this version.
Route::middleware('role:karyawan,vp,hr_admin')
    ->get('/overtime', function (Request $request) {
        $role = (string) ($request->user()?->role ?? '');

        if ($role === 'vp') {
            return redirect()->route('overtime.vp');
        }

        if ($role === 'hr_admin') {
            return redirect()->route('overtime.hr.verification');
        }

        return view('overtime.index', [
            'draft' => $request->session()->get('overtime.draft'),
        ]);
    })
    ->name('overtime.index');

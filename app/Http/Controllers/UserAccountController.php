<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\User;
use App\Services\Audit\AuditLogService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class UserAccountController extends Controller
{
    public function __construct(
        private readonly AuditLogService $audit,
    ) {
    }

    public function index(Request $request): View
    {
        $employees = Employee::with(['division', 'user'])
            ->when($request->filled('q'), function ($query) use ($request) {
                $q = $request->input('q');
                $query->where(function ($sub) use ($q) {
                    $sub->where('nama', 'like', "%{$q}%")
                        ->orWhere('nipeg', 'like', "%{$q}%")
                        ->orWhereHas('user', fn ($u) => $u->where('email', 'like', "%{$q}%"));
                });
            })
            ->orderBy('nama')
            ->paginate(15)
            ->withQueryString();

        return view('employees.accounts-index', compact('employees'));
    }

    public function create(Employee $employee): View
    {
        abort_if($employee->user()->exists(), 409, 'Karyawan ini sudah memiliki akun login.');

        return view('employees.account-create', [
            'employee' => $employee->load('division'),
        ]);
    }

    public function store(Request $request, Employee $employee): RedirectResponse
    {
        abort_if($employee->user()->exists(), 409, 'Karyawan ini sudah memiliki akun login.');

        $data = $request->validate([
            'email' => ['required', 'email', 'lowercase', 'max:255', 'unique:users,email'],
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        $temporaryPassword = $data['password'];

        $user = User::create([
            'name' => $employee->nama,
            'email' => $data['email'],
            'password' => Hash::make($temporaryPassword),
            'role' => 'karyawan',
            'is_active' => true,
            'must_change_password' => true,
        ]);

        $employee->user()->associate($user);
        $employee->save();

        $this->audit->record(
            'ACCOUNT_CREATED',
            $user,
            null,
            [
                'user_id' => $user->id,
                'employee_id' => $employee->id,
                'nipeg' => $employee->nipeg,
                'email' => $user->email,
                'role' => $user->role,
                'is_active' => $user->is_active,
            ],
            'Akun login karyawan dibuat oleh HR. Password awal ditetapkan sebagai password sementara dan wajib diganti saat login pertama.',
            $request,
        );

        return redirect()
            ->route('employee-accounts.index')
            ->with('account-created', [
                'employee' => $employee->nama,
                'email' => $user->email,
                'temporary_password' => $temporaryPassword,
            ]);
    }

    public function toggleActive(Request $request, Employee $employee): RedirectResponse
    {
        $user = $employee->user;

        abort_if(! $user, 404, 'Karyawan ini belum memiliki akun login.');
        abort_if($user->role !== 'karyawan', 403, 'Akun ini bukan akun karyawan.');

        $before = $user->only(['id', 'email', 'role', 'is_active']);
        $user->is_active = ! $user->is_active;
        $user->save();

        $this->audit->record(
            $user->is_active ? 'ACCOUNT_ACTIVATED' : 'ACCOUNT_DEACTIVATED',
            $user,
            $before,
            $user->only(['id', 'email', 'role', 'is_active']),
            ($user->is_active ? 'Akun karyawan diaktifkan' : 'Akun karyawan dinonaktifkan') . ' oleh HR.',
            $request,
        );

        return back()->with(
            'status',
            $user->is_active
                ? 'Akun karyawan berhasil diaktifkan.'
                : 'Akun karyawan berhasil dinonaktifkan.'
        );
    }

    public function resetPassword(Request $request, Employee $employee): RedirectResponse
    {
        $user = $employee->user;

        abort_if(! $user, 404, 'Karyawan ini belum memiliki akun login.');
        abort_if($user->role !== 'karyawan', 403, 'Akun ini bukan akun karyawan.');

        $temporaryPassword = Str::password(12, true, true, false, false);

        $before = $user->only(['id', 'email', 'role', 'must_change_password']);

        $user->forceFill([
            'password' => Hash::make($temporaryPassword),
            'must_change_password' => true,
        ])->save();

        $this->audit->record(
            'ACCOUNT_PASSWORD_RESET',
            $user,
            $before,
            $user->only(['id', 'email', 'role', 'must_change_password']),
            'Password akun karyawan di-reset oleh HR. Password sementara wajib diganti saat login berikutnya.',
            $request,
        );

        return back()->with('account-password-reset', [
            'employee' => $employee->nama,
            'email' => $user->email,
            'temporary_password' => $temporaryPassword,
        ]);
    }
}

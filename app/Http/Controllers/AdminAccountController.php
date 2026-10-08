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

class AdminAccountController extends Controller
{
    private const STRUCTURAL_ROLES = ['vp', 'hr_admin', 'direktur_utama'];

    public function __construct(private readonly AuditLogService $audit) {}

    public function index(Request $request): View
    {
        $accounts = User::query()
            ->with('employee.division')
            ->whereIn('role', self::STRUCTURAL_ROLES)
            ->when($request->filled('q'), function ($query) use ($request) {
                $q = $request->input('q');
                $query->where(function ($sub) use ($q) {
                    $sub->where('name', 'like', "%{$q}%")
                        ->orWhere('email', 'like', "%{$q}%")
                        ->orWhereHas('employee', fn ($e) => $e->where('nipeg', 'like', "%{$q}%"));
                });
            })
            ->when($request->filled('status'), function ($query) use ($request) {
                $query->where('is_active', $request->input('status') === 'aktif');
            })
            ->orderBy('role')
            ->orderBy('name')
            ->paginate(15)
            ->withQueryString();

        return view('admin.accounts.index', compact('accounts'));
    }

    public function create(): View
    {
        $employees = Employee::query()
            ->whereNull('user_id')
            ->with('division')
            ->orderBy('nama')
            ->get();

        return view('admin.accounts.create', compact('employees'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'role' => ['required', 'in:vp,hr_admin,direktur_utama'],
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'lowercase', 'max:255', 'unique:users,email'],
            'employee_id' => ['nullable', 'integer', 'exists:employees,id'],
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        if ($data['role'] === 'vp' && empty($data['employee_id'])) {
            return back()->withErrors(['employee_id' => 'Akun VP harus dihubungkan ke data karyawan untuk menentukan cakupan divisi.'])->withInput();
        }

        $employee = ! empty($data['employee_id']) ? Employee::with('user')->findOrFail($data['employee_id']) : null;
        if ($employee?->user) {
            return back()->withErrors(['employee_id' => 'Karyawan tersebut sudah memiliki akun login.'])->withInput();
        }

        if ($employee) {
            $data['name'] = $employee->nama;
        }

        $temporaryPassword = $data['password'];
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($temporaryPassword),
            'role' => $data['role'],
            'is_active' => true,
            'must_change_password' => true,
        ]);

        if ($employee) {
            $employee->user()->associate($user);
            $employee->save();
        }

        $this->audit->record(
            'STRUCTURAL_ACCOUNT_CREATED',
            $user,
            null,
            ['user_id' => $user->id, 'email' => $user->email, 'role' => $user->role, 'employee_id' => $employee?->id],
            'Akun struktural dibuat oleh administrator sistem. Password awal bersifat sementara.',
            $request,
        );

        return redirect()->route('admin.accounts.index')->with('account-created', [
            'name' => $user->name,
            'email' => $user->email,
            'role' => $user->role,
            'temporary_password' => $temporaryPassword,
        ]);
    }

    public function toggleActive(Request $request, User $user): RedirectResponse
    {
        abort_unless(in_array($user->role, self::STRUCTURAL_ROLES, true), 404);
        abort_if($user->is($request->user()), 422, 'Akun administrator yang sedang digunakan tidak dapat dinonaktifkan dari sesi ini.');

        $before = $user->only(['id', 'email', 'role', 'is_active']);
        $user->is_active = ! $user->is_active;
        $user->save();

        $this->audit->record(
            $user->is_active ? 'STRUCTURAL_ACCOUNT_ACTIVATED' : 'STRUCTURAL_ACCOUNT_DEACTIVATED',
            $user,
            $before,
            $user->only(['id', 'email', 'role', 'is_active']),
            ($user->is_active ? 'Akun struktural diaktifkan' : 'Akun struktural dinonaktifkan') . ' oleh administrator sistem.',
            $request,
        );

        return back()->with('status', $user->is_active ? 'Akun struktural diaktifkan.' : 'Akun struktural dinonaktifkan.');
    }

    public function resetPassword(Request $request, User $user): RedirectResponse
    {
        abort_unless(in_array($user->role, self::STRUCTURAL_ROLES, true), 404);

        $temporaryPassword = Str::password(12, true, true, false, false);
        $before = $user->only(['id', 'email', 'role', 'must_change_password']);
        $user->forceFill([
            'password' => Hash::make($temporaryPassword),
            'must_change_password' => true,
        ])->save();

        $this->audit->record(
            'STRUCTURAL_ACCOUNT_PASSWORD_RESET',
            $user,
            $before,
            $user->only(['id', 'email', 'role', 'must_change_password']),
            'Password akun struktural di-reset oleh administrator sistem.',
            $request,
        );

        return back()->with('account-password-reset', [
            'name' => $user->name,
            'email' => $user->email,
            'temporary_password' => $temporaryPassword,
        ]);
    }
}

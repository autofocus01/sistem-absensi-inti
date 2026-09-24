<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Services\Audit\AuditLogService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function __construct(
        private readonly AuditLogService $audit,
    ) {
    }

    /**
     * Tampilkan halaman profile user yang sedang login.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update profile user.
     *
     * - Nama dan email diperbarui melalui ProfileUpdateRequest.
     * - Jika email berubah, email_verified_at di-reset menjadi null.
     * - Jika email tidak berubah, status verifikasi email dipertahankan.
     * - Setiap update berhasil dicatat sebagai PROFILE_UPDATED.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $user = $request->user();

        // Simpan kondisi sebelum perubahan untuk audit.
        $before = $user->toArray();

        // Gunakan hasil validasi dari ProfileUpdateRequest.
        $user->fill($request->validated());

        // Jika email berubah, reset status verifikasi email.
        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        // Ambil data terbaru setelah perubahan.
        $after = $user->fresh();

        // Catat perubahan profile.
        $this->audit->record(
            'PROFILE_UPDATED',
            $after,
            $before,
            $after->toArray(),
            'Profil pengguna diperbarui oleh pengguna yang bersangkutan.',
            $request
        );

        return Redirect::route('profile.edit')
            ->with('status', 'profile-updated');
    }

    /**
     * Hapus akun user yang sedang login.
     *
     * Password harus benar dan error masuk ke error bag
     * "userDeletion".
     */
    public function destroy(Request $request): RedirectResponse
    {
        // Validasi password dengan error bag khusus userDeletion.
        $request->validateWithBag('userDeletion', [
            'password' => [
                'required',
                'current_password',
            ],
        ]);

        $user = $request->user();

        // Simpan data sebelum akun dihapus untuk audit.
        $before = $user->toArray();

        // Catat audit sebelum user dihapus.
        $this->audit->record(
            'ACCOUNT_DELETED',
            $user,
            $before,
            null,
            'Akun dihapus oleh pemilik akun setelah verifikasi password.',
            $request
        );

        // Logout terlebih dahulu.
        Auth::logout();

        // Hapus akun.
        $user->delete();

        // Hapus session lama dan regenerate CSRF token.
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        // Kembali ke halaman utama.
        return Redirect::to('/');
    }
}
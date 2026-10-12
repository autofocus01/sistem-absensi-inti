<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Memaksa pergantian password sementara sebelum aplikasi dapat dipakai.
 *
 * Akun baru/reset password ditandai must_change_password. Tanpa middleware
 * ini, user cukup mengabaikan redirect ke halaman profil dan melanjutkan
 * bekerja memakai password sementara yang sudah diketahui HR/admin.
 */
class EnsurePasswordChanged
{
    /**
     * Route yang tetap boleh diakses sebelum password diganti:
     * halaman profil, update profil/password, dan logout.
     */
    private const ALLOWED_ROUTES = [
        'profile.edit',
        'profile.update',
        'profile.destroy',
        'password.update',
        'logout',
    ];

    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user && $user->must_change_password) {
            $route = $request->route()?->getName();

            if ($route !== null && ! in_array($route, self::ALLOWED_ROUTES, true)) {
                // Permintaan mutasi (POST/PATCH/DELETE) tidak bisa diamankan
                // dengan redirect — user mungkin sudah mengirim data. Tolak
                // eksplisit agar tidak ada state change dengan password sementara.
                if (! $request->isMethod('GET')) {
                    abort(423, 'Password sementara harus diganti terlebih dahulu.');
                }

                return redirect()
                    ->route('profile.edit')
                    ->with('force-password-change', true)
                    ->withErrors([
                        'password' => 'Password sementara harus diganti sebelum menggunakan aplikasi.',
                    ]);
            }
        }

        return $next($request);
    }
}

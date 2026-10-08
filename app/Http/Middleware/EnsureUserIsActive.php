<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsActive
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        // Hanya akun yang secara eksplisit berstatus nonaktif yang diblokir.
        // Nilai null dapat muncul pada data legacy sebelum governance akun diterapkan
        // dan tidak boleh membuat request terautentikasi tiba-tiba dianggap logout.
        if (! $user || $user->is_active === false) {
            auth()->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('login')->withErrors([
                'email' => 'Akun Anda sedang dinonaktifkan. Hubungi HR untuk bantuan.',
            ]);
        }

        return $next($request);
    }
}

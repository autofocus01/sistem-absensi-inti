<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Security headers baseline untuk seluruh respons web.
 *
 * Catatan CSP: aplikasi memuat aset dari CDN (Tailwind CDN, Google Fonts,
 * Chart.js, jsDelivr) dan beberapa inline script untuk konfigurasi Tailwind
 * serta toggle tema. CSP penuh dengan nonce akan membutuhkan perubahan
 * besar pada view, sehingga belum diaktifkan untuk menghindari merusak UI.
 * Header di bawah dipilih karena aman (tidak memblokir aset CDN) dan
 * memberi proteksi nyata: clickjacking, MIME sniffing, referrer leak.
 */
class SecurityHeaders
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Clickjacking: aplikasi absensi internal tidak boleh di-iframe.
        $response->headers->set('X-Frame-Options', 'SAMEORIGIN');

        // Cegah browser menebak Content-Type (MIME confusion attacks).
        $response->headers->set('X-Content-Type-Options', 'nosniff');

        // Batas jumlah informasi Referer yang dikirim ke pihak ketiga (CDN).
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');

        // Nonaktifkan API browser yang tidak dipakai aplikasi.
        $response->headers->set('Permissions-Policy', 'geolocation=(self), microphone=(), camera=(), payment=()');

        // HSTS hanya bermakna pada HTTPS. APP_URL http (lokal) tidak memicu header.
        if ($request->secure()) {
            $response->headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains');
        }

        return $response;
    }
}

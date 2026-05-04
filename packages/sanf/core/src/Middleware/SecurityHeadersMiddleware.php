<?php

namespace Sanf\Core\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SecurityHeadersMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        if ($response instanceof Response) {
            // Mencegah MIME type sniffing oleh browser
            $response->headers->set('X-Content-Type-Options', 'nosniff');

            // Mencegah halaman dimuat dalam iframe (proteksi clickjacking)
            $response->headers->set('X-Frame-Options', 'SAMEORIGIN');

            // Memaksa koneksi HTTPS untuk 1 tahun ke depan (termasuk subdomain)
            $response->headers->set(
                'Strict-Transport-Security',
                'max-age=31536000; includeSubDomains'
            );

            // Opsional tapi direkomendasikan:
            // Kontrol referrer information
            $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');

            // Nonaktifkan fitur browser yang tidak diperlukan
            $response->headers->set('X-Permitted-Cross-Domain-Policies', 'none');
        }

        return $response;
    }
}

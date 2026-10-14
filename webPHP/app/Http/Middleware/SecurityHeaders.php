<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Ajoute les en-têtes de sécurité HTTP de base, absents jusqu'ici de toute réponse de
 * l'application (ni middleware, ni config serveur). CSP volontairement permissive sur
 * 'self' + les CDN déjà utilisés par l'app pour éviter de casser des scripts/styles
 * existants — à resserrer au cas par cas si de nouveaux domaines externes sont ajoutés.
 */
class SecurityHeaders
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        $response->headers->set('X-Frame-Options', 'DENY');
        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
        $response->headers->set('Permissions-Policy', 'geolocation=(), microphone=(), camera=()');
        $response->headers->set(
            'Content-Security-Policy',
            "default-src 'self'; "
            . "script-src 'self' 'unsafe-inline' 'unsafe-eval' https://cdnjs.cloudflare.com https://cdn.jsdelivr.net; "
            . "style-src 'self' 'unsafe-inline' https://fonts.googleapis.com https://cdnjs.cloudflare.com; "
            . "font-src 'self' https://fonts.gstatic.com https://cdnjs.cloudflare.com; "
            . "img-src 'self' data: https:; "
            . "connect-src 'self' https:; "
            . "frame-ancestors 'none';"
        );

        if ($request->secure()) {
            $response->headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains');
        }

        return $response;
    }
}

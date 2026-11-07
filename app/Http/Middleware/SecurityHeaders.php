<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Vite;
use Symfony\Component\HttpFoundation\Response;

/**
 * Browser-side defences on every web response: a Content Security Policy listing the only hosts the
 * pages load from, plus the usual hardening headers. nginx sets X-Frame-Options and nosniff too.
 */
class SecurityHeaders
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
        $response->headers->set('Permissions-Policy', 'camera=(), microphone=(), geolocation=(), payment=()');

        if ($request->isSecure()) {
            $response->headers->set('Strict-Transport-Security', 'max-age=31536000');
        }

        // The local Vite dev server serves scripts from another origin with hot reload; the policy is for real builds.
        if (! (app()->isLocal() && Vite::isRunningHot())) {
            $response->headers->set('Content-Security-Policy', self::policy());
        }

        return $response;
    }

    public static function policy(): string
    {
        return implode('; ', [
            "default-src 'self'",
            // shortcut: Alpine (invitations, Filament) evaluates its x-data expressions and the pages carry small
            // inline scripts, so 'unsafe-inline' and 'unsafe-eval' stay; switch to Alpine's CSP build and nonces to drop them.
            "script-src 'self' 'unsafe-inline' 'unsafe-eval'",
            "style-src 'self' 'unsafe-inline' https://fonts.googleapis.com https://fonts.bunny.net",
            "font-src 'self' data: https://fonts.gstatic.com https://fonts.bunny.net",
            // Photos may come from Spaces later, Google avatars and YouTube thumbnails: any https image.
            "img-src 'self' data: blob: https:",
            "media-src 'self' https:",
            "frame-src 'self' https://www.youtube-nocookie.com", // 'self': the editor's phone preview
            "connect-src 'self'",
            // Filament's file upload (FilePond) draws image previews in a worker made from a blob: URL.
            "worker-src 'self' blob:",
            "frame-ancestors 'self'", // the editor's phone preview frames our own pages; nobody else may
            "object-src 'none'",
            "base-uri 'self'",
            "form-action 'self'",
        ]);
    }
}

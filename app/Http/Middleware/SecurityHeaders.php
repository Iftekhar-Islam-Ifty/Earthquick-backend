<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SecurityHeaders
{
    /**
     * Add browser protections that are safe for the current server-rendered
     * storefront. CSP starts in report-only mode while inline resources are
     * inventoried. Enforce CSP only after review; enable HSTS only after the
     * production domain is confirmed HTTPS-only.
     *
     * @param  Closure(Request): Response  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('X-Frame-Options', 'SAMEORIGIN');
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
        $response->headers->set('Permissions-Policy', 'camera=(), geolocation=(), microphone=()');

        // Start with a strict report-only policy. Existing inline scripts and
        // styles may be reported by browsers, but will not be blocked yet.
        $scriptSources = ["'self'"];
        $styleSources = ["'self'", 'https://fonts.googleapis.com'];
        $connectSources = ["'self'"];

        if (app()->environment('local')) {
            $scriptSources[] = 'http://localhost:5173';
            $scriptSources[] = 'http://127.0.0.1:5173';
            $styleSources[] = 'http://localhost:5173';
            $styleSources[] = 'http://127.0.0.1:5173';
            $connectSources[] = 'ws://localhost:5173';
            $connectSources[] = 'ws://127.0.0.1:5173';
        }

        $contentSecurityPolicy = implode('; ', [
            "default-src 'self'",
            "base-uri 'self'",
            "object-src 'none'",
            "frame-ancestors 'self'",
            "form-action 'self'",
            'img-src '.implode(' ', ["'self'", 'data:', 'blob:']),
            'script-src '.implode(' ', $scriptSources),
            'style-src '.implode(' ', $styleSources),
            'font-src '.implode(' ', ["'self'", 'data:', 'https://fonts.gstatic.com']),
            'connect-src '.implode(' ', $connectSources),
        ]);

        $response->headers->set('Content-Security-Policy-Report-Only', $contentSecurityPolicy);

        return $response;
    }
}

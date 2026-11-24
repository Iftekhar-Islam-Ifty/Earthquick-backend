<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Keep the shared Laravel session cookie browser-session-only while an admin
 * session is active, including when that admin browses public storefront pages.
 */
class AdminSessionCookieLifetime
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        if (! $request->hasSession() || ! $request->session()->get('admin.browser_session_only')) {
            return $response;
        }

        $sessionCookieName = (string) config('session.cookie');

        foreach ($response->headers->getCookies() as $cookie) {
            if ($cookie->getName() === $sessionCookieName) {
                $response->headers->setCookie($cookie->withExpires(0));
                break;
            }
        }

        return $response;
    }
}

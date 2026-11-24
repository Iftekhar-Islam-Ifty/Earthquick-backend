<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Make the shared Laravel session cookie a browser-session cookie only while
 * an administrator is actively using the admin area.
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

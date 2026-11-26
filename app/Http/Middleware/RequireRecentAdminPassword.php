<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RequireRecentAdminPassword
{
    public function handle(Request $request, Closure $next): Response
    {
        $confirmedAt = (int) $request->session()->get('admin.password_confirmed_at', 0);
        if ($confirmedAt > 0 && (time() - $confirmedAt) < 600) {
            return $next($request);
        }

        $referer = (string) $request->headers->get('referer', '');
        $path = parse_url($referer, PHP_URL_PATH);
        $host = parse_url($referer, PHP_URL_HOST);
        $sameHost = $host === null || strtolower($host) === strtolower($request->getHost());
        $isAdminPath = is_string($path) && ($path === '/admin' || str_starts_with($path, '/admin/'));
        $query = parse_url($referer, PHP_URL_QUERY);
        $returnTo = $sameHost && $isAdminPath
            ? url($path.($query ? '?'.$query : ''))
            : route('admin.dashboard');
        $request->session()->put('url.intended', $returnTo);

        return redirect()->route('admin.security.confirm-password');
    }
}

<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/**
 * Preserve the admin login while browsing the storefront, and enforce its
 * browser-session and admin-idle boundaries without affecting customers.
 */
class AdminSessionBoundary
{
    public function handle(Request $request, Closure $next): Response
    {
        $guard = Auth::guard();
        $user = $guard->user();

        if (! $user || ! $user->is_admin) {
            return $next($request);
        }

        $isAdminRequest = $request->is('admin', 'admin/*');

        // A legacy remember cookie may restore an admin before the route runs.
        // Revoke that token and require a password login instead.
        if ($guard->viaRemember()) {
            $this->endAdminSession($request);

            if ($isAdminRequest) {
                return redirect()->route('login')
                    ->with('error', 'Please sign in again to access the admin panel.');
            }

            return $next($request);
        }

        // Sessions created before this policy have no authentication marker;
        // require one fresh password login after deployment.
        if (! $request->session()->has('admin.authenticated_at')) {
            $this->endAdminSession($request);

            if ($isAdminRequest) {
                return redirect()->route('login')
                    ->with('error', 'Please sign in again to access the admin panel.');
            }

            return $next($request);
        }

        // Storefront browsing in another tab should not sign the admin out.
        // The inactivity clock is based on admin requests, not public browsing.
        if ($isAdminRequest) {
            $lastActivity = (int) $request->session()->get('admin.last_activity_at', 0);
            $idleLimit = max(60, (int) config('session.admin_idle_timeout', 900));

            if ($lastActivity > 0 && (time() - $lastActivity) >= $idleLimit) {
                $this->endAdminSession($request);

                return redirect()->route('login')
                    ->with('error', 'Your admin session expired after inactivity. Please sign in again.');
            }

            $request->session()->put('admin.last_activity_at', time());
        }

        // Keep the shared session cookie browser-session-only while the admin
        // is signed in, including storefront pages opened in another tab.
        $request->session()->put('admin.browser_session_only', true);

        return $next($request);
    }

    private function endAdminSession(Request $request): void
    {
        Auth::logout();
        $request->session()->forget([
            'admin.authenticated_at',
            'admin.last_activity_at',
            'admin.browser_session_only',
            'admin.mfa_verified',
            'admin.password_confirmed_at',
            'admin.recovery_codes',
        ]);
        $request->session()->invalidate();
        $request->session()->regenerateToken();
    }
}

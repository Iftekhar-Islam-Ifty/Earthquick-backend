<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/**
 * Keep administrator authentication confined to the admin area and expire
 * inactive admin sessions without changing customer remember-me behavior.
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

        // Leaving /admin ends the administrator session while preserving the
        // customer's cart and other unrelated session data.
        if (! $isAdminRequest) {
            $this->endAdminSession($request);

            return $next($request);
        }

        $lastActivity = (int) $request->session()->get('admin.last_activity_at', 0);
        $idleLimit = max(60, (int) config('session.admin_idle_timeout', 900));

        if ($lastActivity > 0 && (time() - $lastActivity) >= $idleLimit) {
            $this->endAdminSession($request);

            return redirect()->route('login')
                ->with('error', 'Your admin session expired after inactivity. Please sign in again.');
        }

        $request->session()->put('admin.browser_session_only', true);
        $request->session()->put('admin.last_activity_at', time());

        return $next($request);
    }

    private function endAdminSession(Request $request): void
    {
        Auth::logout();
        $request->session()->forget([
            'admin.authenticated_at',
            'admin.last_activity_at',
            'admin.browser_session_only',
        ]);
        $request->session()->regenerate();
    }
}

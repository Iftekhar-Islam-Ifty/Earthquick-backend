<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/* =========================================================================
 * ADMIN MIDDLEWARE
 * Enforces role-based security verification for administrative routes.
 * Ensures user is authenticated and possesses verified is_admin privileges.
 * ========================================================================= */

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     * Restrict access to authenticated administrators only.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Redirect unauthenticated guests to login screen
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Please login with administrator credentials to access the admin panel.');
        }

        // Abort with 403 Forbidden if user lacks administrative privileges
        if (!Auth::user()->is_admin) {
            abort(403, 'Unauthorized access. This area is reserved for system administrators only.');
        }

        return $next($request);
    }
}

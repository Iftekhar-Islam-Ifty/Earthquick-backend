<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\View\View;

class AdminPasswordConfirmationController extends Controller
{
    public function show(Request $request): View
    {
        abort_unless($request->user()?->is_admin && $request->session()->boolean('admin.mfa_verified'), 403);
        return view('auth.admin-confirm-password');
    }

    public function confirm(Request $request): RedirectResponse
    {
        abort_unless($request->user()?->is_admin && $request->session()->boolean('admin.mfa_verified'), 403);
        $key = 'admin-password-confirm:'.$request->user()->id.'|'.$request->ip();
        if (RateLimiter::tooManyAttempts($key, 5)) {
            return back()->withErrors(['password' => 'Too many attempts. Try again in '.RateLimiter::availableIn($key).' seconds.']);
        }
        $request->validate(['password' => ['required', 'string']]);
        if (! Hash::check($request->input('password'), $request->user()->password)) {
            RateLimiter::hit($key, 300);
            return back()->withErrors(['password' => 'The password is incorrect.']);
        }

        RateLimiter::clear($key);
        $request->session()->put('admin.password_confirmed_at', time());
        return redirect()->intended(route('admin.dashboard'));
    }
}

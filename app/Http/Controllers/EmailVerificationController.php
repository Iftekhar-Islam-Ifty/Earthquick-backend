<?php

namespace App\Http\Controllers;

use Illuminate\Auth\Events\Verified;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use Illuminate\View\View;

class EmailVerificationController extends Controller
{
    public function notice(): View|RedirectResponse
    {
        if (request()->user()->hasVerifiedEmail()) {
            return redirect()->route('account.dashboard');
        }
        return view('auth.verify-email');
    }

    public function verify(Request $request, int $id, string $hash): RedirectResponse
    {
        abort_unless(URL::hasValidSignature($request), 403);
        abort_unless((int) $request->user()->getKey() === $id, 403);
        abort_unless(sha1($request->user()->getEmailForVerification()) === $hash, 403);

        if ($request->user()->markEmailAsVerified()) {
            event(new Verified($request->user()));
        }

        return $request->user()->is_admin
            ? redirect()->route('admin.security.setup')->with('success', 'Email verified. Set up admin two-step verification to continue.')
            : redirect()->route('account.dashboard')->with('success', 'Email address verified successfully.');
    }

    public function resend(Request $request): RedirectResponse
    {
        if (! $request->user()->hasVerifiedEmail()) {
            $request->user()->sendEmailVerificationNotification();
        }

        return back()->with('status', 'A verification link has been sent if your email is not verified yet.');
    }
}

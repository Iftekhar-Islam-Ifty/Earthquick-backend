<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\AdminTotp;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\View\View;

class AdminSecurityController extends Controller
{
    public function setup(Request $request, AdminTotp $totp): View|RedirectResponse
    {
        $user = $this->admin($request);
        if ($user->admin_totp_confirmed_at) {
            return redirect()->route('admin.security.challenge');
        }

        if (! $user->admin_totp_secret) {
            $user->forceFill(['admin_totp_secret' => $totp->newSecret()])->save();
        }

        return view('auth.admin-mfa-setup', [
            'secret' => $user->admin_totp_secret,
            'provisioningUri' => $totp->provisioningUri($user->admin_totp_secret, $user->email),
        ]);
    }

    public function confirmSetup(Request $request, AdminTotp $totp): RedirectResponse
    {
        $request->validate(['code' => ['required', 'regex:/^\d{6}$/']]);
        $user = $this->admin($request);

        $step = $user->admin_totp_secret
            ? $totp->matchingCounter($user->admin_totp_secret, $request->string('code')->toString())
            : null;
        if ($user->admin_totp_confirmed_at || $step === null) {
            return back()->withErrors(['code' => 'The authenticator code is invalid. Try the current six-digit code.']);
        }

        $codes = collect(range(1, 10))->map(fn () => Str::upper(Str::random(16)))->all();
        $user->forceFill([
            'admin_totp_confirmed_at' => now(),
            'admin_totp_last_step' => $step,
            'admin_recovery_codes' => array_map(fn ($code) => Hash::make($code), $codes),
        ])->save();

        $request->session()->put('admin.mfa_verified', true);
        $request->session()->put('admin.password_confirmed_at', time());
        $request->session()->flash('admin.recovery_codes', $codes);

        return redirect()->route('admin.security.recovery-codes');
    }

    public function recoveryCodes(Request $request): View|RedirectResponse
    {
        $this->admin($request);
        $codes = $request->session()->pull('admin.recovery_codes');
        if (! $codes) {
            return redirect()->route('admin.dashboard');
        }

        return view('auth.admin-recovery-codes', compact('codes'));
    }

    public function challenge(Request $request): View|RedirectResponse
    {
        $user = $this->admin($request);
        if (! $user->admin_totp_confirmed_at) {
            return redirect()->route('admin.security.setup');
        }
        if ($request->session()->boolean('admin.mfa_verified')) {
            return redirect()->route('admin.dashboard');
        }

        return view('auth.admin-mfa-challenge');
    }

    public function verifyChallenge(Request $request, AdminTotp $totp): RedirectResponse
    {
        $user = $this->admin($request);
        $key = 'admin-mfa:'.$user->id.'|'.$request->ip();
        if (RateLimiter::tooManyAttempts($key, 5)) {
            return back()->withErrors(['code' => 'Too many attempts. Try again in '.RateLimiter::availableIn($key).' seconds.']);
        }

        $request->validate(['code' => ['required', 'string', 'max:64']]);
        $provided = strtoupper(preg_replace('/[^A-Za-z0-9]/', '', $request->string('code')->toString()));
        $step = $totp->matchingCounter((string) $user->admin_totp_secret, $provided);
        $valid = $step !== null && DB::transaction(function () use ($user, $step): bool {
            $locked = User::query()->lockForUpdate()->findOrFail($user->id);
            $lastStep = $locked->admin_totp_last_step;
            if ($lastStep !== null && $step <= (int) $lastStep) {
                return false;
            }
            $locked->forceFill(['admin_totp_last_step' => $step])->save();
            return true;
        });

        if (! $valid && strlen($provided) >= 16) {
            $valid = DB::transaction(function () use ($user, $provided): bool {
                $locked = User::query()->lockForUpdate()->findOrFail($user->id);
                $codes = $locked->admin_recovery_codes ?? [];
                foreach ($codes as $index => $hash) {
                    if (Hash::check($provided, $hash)) {
                        unset($codes[$index]);
                        $locked->forceFill(['admin_recovery_codes' => array_values($codes)])->save();
                        return true;
                    }
                }
                return false;
            });
        }

        if (! $valid) {
            RateLimiter::hit($key, 300);
            return back()->withErrors(['code' => 'The authentication code is invalid.']);
        }

        RateLimiter::clear($key);
        $request->session()->put('admin.mfa_verified', true);
        $request->session()->put('admin.password_confirmed_at', time());
        $request->session()->regenerate();

        return redirect()->intended(route('admin.dashboard'));
    }

    private function admin(Request $request): User
    {
        abort_unless($request->user()?->is_admin, 403);
        return $request->user();
    }
}

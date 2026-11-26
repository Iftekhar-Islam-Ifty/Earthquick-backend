<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;

/* =========================================================================
 * AUTHENTICATION CONTROLLER
 * Manages customer and administrator registration, dual-identifier login
 * (email or phone), session lifecycle, and role-based redirects.
 * ========================================================================= */
class AuthController extends Controller
{
    private const LOGIN_MAX_ATTEMPTS = 5;

    private const REGISTRATION_MAX_ATTEMPTS = 5;

    private const THROTTLE_DECAY_SECONDS = 60;

    private const REGISTRATION_THROTTLE_DECAY_SECONDS = 600;

    /**
     * Display customer authentication sign-in view.
     *
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function showLoginForm()
    {
        if (Auth::check()) {
            if (Auth::user()->is_admin) {
                return redirect()->route('admin.dashboard');
            }
            return redirect()->route('account.dashboard');
        }

        return view('auth.login');
    }

    /**
     * Authenticate client via email or Bangladeshi mobile phone number.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function login(Request $request)
    {
        $request->validate([
            'identifier' => 'required|string',
            'password'   => 'required|string',
        ], [
            'identifier.required' => 'Email address or mobile phone number is required.',
            'password.required'   => 'Account password is required.',
        ]);

        $identifier = trim($request->input('identifier'));
        $password = $request->input('password');
        $remember = $request->boolean('remember');
        $throttleKey = 'login:'.Str::lower($identifier).'|'.$request->ip();

        if (RateLimiter::tooManyAttempts($throttleKey, self::LOGIN_MAX_ATTEMPTS)) {
            return back()
                ->withErrors([
                    'identifier' => 'Too many login attempts. Please try again in '.RateLimiter::availableIn($throttleKey).' seconds.',
                ])
                ->withInput($request->only('identifier', 'remember'));
        }

        // Resolve credential attribute type by format
        $fieldType = filter_var($identifier, FILTER_VALIDATE_EMAIL) ? 'email' : 'phone';

        $credentials = [
            $fieldType => $identifier,
            'password' => $password,
        ];

        // Authenticate without a persistent cookie first. Admin accounts are
        // deliberately session-only; customer remember-me is applied below.
        if (Auth::attempt($credentials, false)) {
            RateLimiter::clear($throttleKey);

            // Route administrator directly to executive admin panel
            if (Auth::user()->is_admin) {
                $admin = Auth::user();

                // Rotate any old persistent admin token, then restore this
                // login as a non-remembered session.
                Auth::logout();
                Auth::login($admin, false);
                $request->session()->regenerate();
                // Never carry MFA proof from an earlier admin session into a
                // fresh password login, even when the browser cookie survives.
                $request->session()->forget([
                    'admin.mfa_verified',
                    'admin.password_confirmed_at',
                    'admin.recovery_codes',
                ]);
                $request->session()->put([
                    'admin.authenticated_at' => time(),
                    'admin.last_activity_at' => time(),
                    'admin.browser_session_only' => true,
                ]);

                if (! $admin->hasVerifiedEmail()) {
                    return redirect()->route('verification.notice');
                }
                if (! $admin->admin_totp_confirmed_at) {
                    return redirect()->route('admin.security.setup');
                }
                return redirect()->route('admin.security.challenge');
            }

            $customer = Auth::user();
            if ($remember) {
                Auth::login($customer, true);
            }

            $request->session()->regenerate();

            // Route customer to intended page or personal dashboard
            return redirect()->intended(route('account.dashboard'))
                ->with('success', 'Welcome back, ' . Auth::user()->name . '! Signed in successfully.');
        }

        RateLimiter::hit($throttleKey, self::THROTTLE_DECAY_SECONDS);

        return back()
            ->withErrors([
                'identifier' => 'Invalid credentials. Please verify your email/phone and password.',
            ])
            ->withInput($request->only('identifier', 'remember'));
    }

    /**
     * Display customer account registration view.
     *
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function showRegisterForm()
    {
        if (Auth::check()) {
            return redirect()->route('account.dashboard');
        }

        return view('auth.register');
    }

    /**
     * Register a new client account and initialize authenticated session.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function register(Request $request)
    {
        $throttleKey = 'registration:'.$request->ip();
        if (RateLimiter::tooManyAttempts($throttleKey, self::REGISTRATION_MAX_ATTEMPTS)) {
            return back()
                ->withErrors([
                    'email' => 'Too many registration attempts. Please try again in '.RateLimiter::availableIn($throttleKey).' seconds.',
                ])
                ->withInput($request->except('password'));
        }

        // Count every registration request, including invalid and successful ones.
        RateLimiter::hit($throttleKey, self::REGISTRATION_THROTTLE_DECAY_SECONDS);

        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|string|email|max:255|unique:users,email',
            'phone'    => ['required', 'string', 'regex:/^(?:\+?88)?01[3-9]\d{8}$/', 'unique:users,phone'],
            'city'     => 'nullable|string|max:100',
            'password' => 'required|string|min:12',
        ], [
            'name.required'     => 'Full name is required.',
            'email.required'    => 'Email address is required.',
            'email.unique'      => 'This email address is already registered.',
            'phone.required'    => 'Mobile phone number is required.',
            'phone.regex'       => 'Please provide a valid 11-digit Bangladeshi mobile number (e.g. 017XXXXXXXX).',
            'phone.unique'      => 'This phone number is already registered.',
            'password.required' => 'Password is required.',
            'password.min'      => 'Password must contain at least 12 characters.',
        ]);

        // Create user record
        $user = User::create([
            'name'     => trim($request->name),
            'email'    => strtolower(trim($request->email)),
            'phone'    => trim($request->phone),
            'city'     => $request->city ?? 'Chattogram',
            'password' => Hash::make($request->password),
        ]);

        Auth::login($user);
        $request->session()->regenerate();
        event(new Registered($user));

        return redirect()->route('verification.notice')
            ->with('success', 'Your account is ready. Verify your email to enable password recovery.');
    }

    /**
     * Terminate client session and invalidate authentication tokens.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home')->with('success', 'You have been signed out successfully.');
    }
}

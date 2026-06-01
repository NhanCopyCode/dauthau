<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Cache\RateLimiter;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class LoginController extends Controller
{
    /**
     * Max login attempts before rate-lockout.
     */
    private const MAX_ATTEMPTS = 5;

    /**
     * Lockout duration in minutes.
     */
    private const LOCKOUT_MINUTES = 15;

    /**
     * Show the login form.
     */
    public function showLoginForm(): View
    {
        return view('admin.pages.login');
    }

    /**
     * Handle an incoming login request.
     *
     * @throws ValidationException
     */
    public function login(Request $request): RedirectResponse
    {
        $request->validate([
            'email'    => ['required', 'string', 'email', 'max:255'],
            'password' => ['required', 'string', 'min:1'],
        ]);

        // Trim and normalize email
        $credentials = [
            'email'    => mb_strtolower(trim($request->input('email'))),
            'password' => $request->input('password'),
        ];

        // Rate-limit check BEFORE authentication attempt
        if ($this->hasTooManyLoginAttempts($request)) {
            $seconds = $this->limiter()->availableIn($this->throttleKey($request));

            throw ValidationException::withMessages([
                'email' => trans('auth.throttle', [
                    'seconds' => $seconds,
                    'minutes' => ceil($seconds / 60),
                ]),
            ]);
        }

        // Attempt authentication
        if (Auth::attempt(
            $credentials,
            (bool) $request->boolean('remember')
        )) {
            // Regenerate session — prevent session fixation
            $request->session()->regenerate();

            // Clear rate limiter on success
            $this->clearLoginAttempts($request);

            return redirect()->intended(route('auth.home'));
        }

        // Increment rate limiter
        $this->incrementLoginAttempts($request);

        // Generic error — never leak whether email exists
        throw ValidationException::withMessages([
            'email' => trans('auth.failed'),
        ]);
    }

    /**
     * Log the user out of the application.
     */
    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }

    // ─── Rate Limiting ────────────────────────────────────

    private function limiter(): RateLimiter
    {
        return app(RateLimiter::class);
    }

    private function throttleKey(Request $request): string
    {
        return Str::lower($request->input('email')) . '|' . $request->ip();
    }

    private function hasTooManyLoginAttempts(Request $request): bool
    {
        return $this->limiter()->tooManyAttempts(
            $this->throttleKey($request),
            self::MAX_ATTEMPTS
        );
    }

    private function incrementLoginAttempts(Request $request): void
    {
        $this->limiter()->hit(
            $this->throttleKey($request),
            self::LOCKOUT_MINUTES * 60
        );
    }

    private function clearLoginAttempts(Request $request): void
    {
        $this->limiter()->clear($this->throttleKey($request));
    }
}

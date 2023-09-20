<?php

namespace Cachet\Http\Controllers\Auth;

use Cachet\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class AuthenticatedSessionController extends Controller
{
    /**
     * Create a new login controller instance.
     */
    public function __construct()
    {
        $this->middleware('cachet.guest:'.config('cachet.guard'))->except('destroy');
    }

    /**
     * Show the login form.
     *
     * @return \Inertia\Response|\Symfony\Component\HttpFoundation\Response
     */
    public function show(): Response
    {
        if ($loginPath = config('cachet.routes.login', false)) {
            return Inertia::location($loginPath);
        }

        return Inertia::render('Auth/Login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect()->intended(config('cachet.routes.dashboard', '/status/dashboard'));
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect(config('cachet.routes.login', '/status/login'));
    }
}

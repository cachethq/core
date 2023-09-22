<?php

declare(strict_types=1);

namespace Cachet\Http\Controllers;

use Illuminate\Routing\Controller;
use Inertia\Inertia;

class LoginController extends Controller
{
    /**
     * Create a new login controller instance.
     */
    public function __construct()
    {
        $this->middleware('cachet.guest:'.config('cachet.guard'))->except('logout');
    }

    /**
     * Show the login form.
     *
     * @return \Inertia\Response|\Symfony\Component\HttpFoundation\Response
     */
    public function showLoginForm()
    {
        if ($loginPath = config('cachet.routes.login', false)) {
            return Inertia::location($loginPath);
        }

        return Inertia::render('Dashboard/Login');
    }
}

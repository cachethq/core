<?php

namespace Cachet\Http\Middleware;

use Illuminate\Http\Request;
use Illuminate\Session\Middleware\AuthenticateSession as BaseAuthenticateSession;

class AuthenticateSession extends BaseAuthenticateSession
{
    /**
     * Get the path the user should be redirected to when their session is not authenticated.
     */
    protected function redirectTo(Request $request): ?string
    {
        return route('filament.cachet.auth.login');
    }
}

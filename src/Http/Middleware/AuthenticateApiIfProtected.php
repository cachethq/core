<?php

namespace Cachet\Http\Middleware;

use Cachet\Settings\AppSettings;
use Closure;
use Illuminate\Contracts\Auth\Factory as AuthFactory;
use Illuminate\Http\Request;

class AuthenticateApiIfProtected
{
    public function __construct(
        private AppSettings $settings,
        private AuthFactory $auth,
    ) {}

    public function handle(Request $request, Closure $next)
    {
        if ($this->settings->api_protected && ! $this->auth->guard('sanctum')->check()) {
            abort(401, 'Unauthenticated.');
        }

        return $next($request);
    }
}

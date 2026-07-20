<?php

namespace Cachet\Http\Middleware;

use Cachet\Settings\AppSettings;
use Closure;
use Illuminate\Contracts\Auth\Factory as AuthFactory;
use Illuminate\Http\Request;

class AuthenticateMcpIfProtected
{
    public function __construct(
        private AppSettings $settings,
        private AuthFactory $auth,
    ) {}

    public function handle(Request $request, Closure $next)
    {
        $authenticated = $this->auth->guard('sanctum')->check();

        if ($this->settings->mcp_protected && ! $authenticated) {
            abort(401, 'Unauthenticated.');
        }

        if ($authenticated) {
            $this->auth->shouldUse('sanctum');
        }

        return $next($request);
    }
}

<?php

namespace Cachet\Http\Middleware;

use Cachet\Settings\AppSettings;
use Closure;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Auth\Middleware\Authenticate as BaseAuthenticationMiddleware;
use Illuminate\Http\Request;

class ApiEnabled extends BaseAuthenticationMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  Request  $request
     * @param  Closure(Request):mixed  $next
     * @param  string  ...$guards
     * @return mixed
     *
     * @throws AuthenticationException
     */
    public function handle($request, Closure $next, ...$guards)
    {
        if (!AppSettings::getOrDefault('api_enabled', true)) {
            abort(404);
        }
        return $next($request);
    }
}

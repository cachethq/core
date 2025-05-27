<?php

namespace Cachet\Http\Middleware;

use Cachet\Settings\AppSettings;
use Closure;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Auth\Middleware\Authenticate as BaseAuthenticationMiddleware;
use Illuminate\Http\Request;

class ApiPublicOrProtected extends BaseAuthenticationMiddleware
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
        $protected = AppSettings::getOrDefault('api_protected', false);
        if ($protected) {
            return parent::handle($request, $next, ...$guards);
        }
        return $next($request);
    }
}

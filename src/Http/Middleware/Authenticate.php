<?php

namespace Cachet\Http\Middleware;

use Closure;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Auth\Middleware\Authenticate as BaseAuthenticationMiddleware;
use Illuminate\Http\Request;

class Authenticate extends BaseAuthenticationMiddleware
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
        $guard = config('cachet.guard');

        if (! empty($guard)) {
            $guards[] = $guard;
        }

        return parent::handle($request, $next, ...$guards);
    }
}

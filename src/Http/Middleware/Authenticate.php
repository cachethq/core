<?php

namespace Cachet\Http\Middleware;

use Closure;
use Illuminate\Auth\Middleware\Authenticate as BaseAuthenticationMiddleware;

class Authenticate extends BaseAuthenticationMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request):mixed  $next
     * @param  string[]  ...$guards
     * @return mixed
     *
     * @throws \Illuminate\Auth\AuthenticationException
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

<?php

namespace Cachet\Http\Middleware;

use Cachet\Settings\AppSettings;
use Closure;
use Illuminate\Http\Request;

class ApiEnabled
{
    /**
     * Handle an incoming request.
     *
     * @param  Request  $request
     * @param  Closure(Request):mixed  $next
     * @return mixed
     *
     */
    public function handle($request, Closure $next)
    {
        if (!AppSettings::getOrDefault('api_enabled', true)) {
            abort(404);
        }
        return $next($request);
    }
}

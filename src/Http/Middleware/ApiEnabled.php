<?php

namespace Cachet\Http\Middleware;

use Cachet\Settings\AppSettings;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

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
        throw_unless(AppSettings::getOrDefault('api_enabled', true), NotFoundHttpException::class);

        return $next($request);
    }
}

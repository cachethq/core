<?php

namespace Cachet\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class ForceJsonResponse
{
    /**
     * Force the API to always respond with JSON, regardless of the Accept
     * header, so errors are never rendered as HTML or redirects.
     */
    public function handle(Request $request, Closure $next)
    {
        $request->headers->set('Accept', 'application/json');

        return $next($request);
    }
}

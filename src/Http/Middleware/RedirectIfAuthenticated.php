<?php

namespace Cachet\Http\Middleware;

use Cachet\Cachet;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request):Response  $next
     */
    public function handle(Request $request, Closure $next, ?string $guard = null): Response
    {
        if (Auth::guard($guard)->check()) {
            return redirect(Cachet::path());
        }

        return $next($request);
    }
}
